<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 16/11/2012

    * @author Analista: Gelson
    * @author Desenvolvedor: Carolina

    * @package URBEM
    * @subpackage Mapeamento

    $Revision:
    $Name$
    $Author:
    $Date:

    * Casos de uso:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTransparenciaCedidosAdidos extends Persistente
{
     function recuperaCedidosAdidos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
     {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCedidosAdidos().$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

   public function montaRecuperaCedidosAdidos()
   {
        $stSql = "
                                    SELECT  (select cod_entidade from orcamento.entidade where cod_entidade = ".$this->getDado('codEntidade') ." and exercicio = '".$this->getDado('exercicio') ."' ) as numero_entidade
                                              ,  to_char((select pega0datafinalcompetenciadoperiodomovimento(".$this->getDado('inCodPeriodoMovimentacao') .")::date), 'mm/yyyy') as mes_ano
                                              , contrato.registro as matricula_servidor
                                              , adidos_cedidos.*
                                       FROM pessoal".$this->getDado('stEntidade') .".contrato
                                               , (
                                                                SELECT contrato_servidor.cod_contrato
                                                                          , sw_cgm.nom_cgm
                                                                          , recuperarSituacaoDoContratoLiteral(contrato_servidor.cod_contrato,".$this->getDado('inCodPeriodoMovimentacao') .",'".$this->getDado('stEntidade') ."') as situacao
                                                                          , (SELECT norma.num_norma||'/'||norma.exercicio FROM normas.norma WHERE norma.cod_norma = adido_cedido.cod_norma) as ato_cedencia
                                                                          , to_char(adido_cedido.dt_inicial,'ddmmyyyy') as dt_inicial
                                                                          , to_char(adido_cedido.dt_final,'ddmmyyyy') as dt_final
                                                                          , (CASE WHEN adido_cedido.tipo_cedencia = 'a' THEN 'Adido'
                                                                             ELSE 'Cedido'
                                                                             END) as tipo_cedencia
                                                                         , (CASE WHEN adido_cedido.indicativo_onus = 'c' THEN 'Ônus do Cedente'
                                                                             ELSE 'Ônus do Cessionário'
                                                                             END) as indicativo_onus
                                                                        , (SELECT nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm = adido_cedido.cgm_cedente_cessionario) as orgao_cedente_cessionario
                                                                        , adido_cedido.num_convenio
                                                                        , adido_cedido_local.descricao_local as local
                                                                FROM pessoal".$this->getDado('stEntidade') .".contrato_servidor

                                                        INNER JOIN (
                                                                                        SELECT adido_cedido.*
                                                                                          FROM pessoal".$this->getDado('stEntidade') .".adido_cedido
                                                                                  INNER JOIN (
                                                                                                                  SELECT adido_cedido.cod_contrato
                                                                                                                            , max(adido_cedido.timestamp) as timestamp
                                                                                                                    FROM pessoal".$this->getDado('stEntidade') .".adido_cedido
                                                                                                                  WHERE adido_cedido.timestamp <= (select ultimotimestampperiodomovimentacao(".$this->getDado('inCodPeriodoMovimentacao') .",'".$this->getDado('stEntidade') ."'))::timestamp
                                                                                                             GROUP BY adido_cedido.cod_contrato
                                                                                                      ) as max_adido_cedido
                                                                                                ON max_adido_cedido.cod_contrato = adido_cedido.cod_contrato
                                                                                              AND max_adido_cedido.timestamp = adido_cedido.timestamp
                                                                            ) as adido_cedido
                                                                       ON contrato_servidor.cod_contrato = adido_cedido.cod_contrato
                                                           INNER JOIN pessoal".$this->getDado('stEntidade') .".servidor_contrato_servidor
                                                                       ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

                                                           INNER JOIN pessoal".$this->getDado('stEntidade') .".servidor
                                                                       ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor

                                                           INNER JOIN sw_cgm
                                                                ON servidor.numcgm = sw_cgm.numcgm

                                                            LEFT JOIN (
                                                                                        SELECT adido_cedido_local.cod_contrato
                                                                                                  , adido_cedido_local.cod_local
                                                                                                  , local.descricao as descricao_local
                                                                                           FROM pessoal".$this->getDado('stEntidade') .".adido_cedido_local
                                                                                    INNER JOIN (
                                                                                                                SELECT adido_cedido_local.cod_contrato
                                                                                                                          , max(adido_cedido_local.timestamp) as timestamp
                                                                                                                  FROM pessoal".$this->getDado('stEntidade') .".adido_cedido_local
                                                                                                                WHERE adido_cedido_local.timestamp <= (select ultimotimestampperiodomovimentacao(".$this->getDado('inCodPeriodoMovimentacao') .",'".$this->getDado('stEntidade') ."'))::timestamp
                                                                                                           GROUP BY adido_cedido_local.cod_contrato
                                                                                                      ) as max_adido_cedido_local
                                                                                                 ON max_adido_cedido_local.cod_contrato = adido_cedido_local.cod_contrato
                                                                                               AND max_adido_cedido_local.timestamp = adido_cedido_local.timestamp

                                                                                        LEFT JOIN organograma.local
                                                                                          ON local.cod_local = adido_cedido_local.cod_local

                                                                            ) as adido_cedido_local
                                                                     ON contrato_servidor.cod_contrato = adido_cedido_local.cod_contrato

                                               ) as adidos_cedidos
                                      WHERE contrato.cod_contrato = adidos_cedidos.cod_contrato
                                 ORDER BY nom_cgm, contrato.registro



                 ";

            return $stSql;

    }

}

?>
