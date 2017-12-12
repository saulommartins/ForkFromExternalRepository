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
 * Extensão da Classe de mapeamento
 * Data de Criação: 14/02/2012

 * @author Desenvolvedor: Jean Felipe da Silva

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTRNAnexContaCorrente extends Persistente
{
    public function TTRNAnexContaCorrente()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function recuperaHeader(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaHeader",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaHeader()
    {
        $stSql = "SELECT '0' AS tipo_registro
                        , 'ANEXO26' AS nome_arquivo
                        , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
                        , 'O' AS tipo_arquivo
                        , to_char(CURRENT_DATE,'dd/mm/yyyy') AS dt_arquivo
                        , substr(CAST(CURRENT_TIME AS text),1,8) AS hr_arquivo
                , configuracao_entidade.valor AS cod_orgao
                , sw_cgm.nom_cgm AS nom_orgao

                    FROM administracao.configuracao_entidade
            JOIN orcamento.entidade
              ON entidade.exercicio = configuracao_entidade.exercicio
             AND entidade.cod_entidade = configuracao_entidade.cod_entidade
            JOIN sw_cgm
              ON sw_cgm.numcgm = entidade.numcgm

            WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
              AND configuracao_entidade.cod_entidade = ( SELECT valor::INTEGER
                                                           FROM administracao.configuracao
                                                          WHERE parametro = 'cod_entidade_prefeitura'
                                                            AND exercicio = '".$this->getDado('exercicio')."' )
              AND configuracao_entidade.cod_modulo = 49
              AND configuracao_entidade.parametro = 'cod_orgao_tce'";

        return $stSql;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "SELECT '1' AS tipo_registro
                          , '' AS brancos
                  , banco.num_banco AS banco
                  , replace( agencia.num_agencia, '-', '') AS agencia
                  , replace( conta_corrente.num_conta_corrente, '-', '') AS conta_corrente
                  , plano_conta.nom_conta AS descricao
                  , (SELECT valor FROM administracao.configuracao
                 WHERE exercicio = '".$this->getDado('exercicio')."' AND parametro = 'nom_prefeitura') AS titular

            FROM contabilidade.plano_banco
            JOIN monetario.conta_corrente
              ON conta_corrente.cod_banco = plano_banco.cod_banco
             AND conta_corrente.cod_agencia = plano_banco.cod_agencia
             AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
            JOIN monetario.agencia
              ON agencia.cod_banco = conta_corrente.cod_banco
             AND agencia.cod_agencia = conta_corrente.cod_agencia
            JOIN monetario.banco
              ON banco.cod_banco = agencia.cod_banco
            JOIN contabilidade.plano_analitica
              ON plano_analitica.exercicio = plano_banco.exercicio
             AND plano_analitica.cod_plano = plano_banco.cod_plano
            JOIN contabilidade.plano_conta
              ON plano_conta.exercicio = plano_analitica.exercicio
             AND plano_conta.cod_conta = plano_analitica.cod_conta

           WHERE plano_banco.cod_entidade IN (".$this->getDado('inCodEntidade').") AND ";

            if ($this->getDado('inBimestre') == '1') {
                $stSql.="conta_corrente.data_criacao <= '".$this->getDado('dtFin')."'";
            } else {
                $stSql.="conta_corrente.data_criacao BETWEEN '".$this->getDado('dtIni')."' AND '".$this->getDado('dtFin')."'";
            }
            $stSql.= " AND plano_conta.cod_estrutural NOT LIKE '1.1.1.1.1%'";
        $stSql.=" GROUP BY banco.num_banco
                             , agencia.num_agencia
                 , conta_corrente.num_conta_corrente
                 , plano_conta.nom_conta
            ORDER BY banco
                 , agencia
                ";

        return $stSql;
    }

}
