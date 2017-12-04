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
    * Página de Include Oculta - Exportação Arquivos TCMBA

    * Data de Criação   : 02/07/2015

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    $Id $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBARiscosFiscais extends Persistente
{

    public function recuperaRiscosFiscais(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRiscosFiscais().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRiscosFiscais()
    {
        $stSql .= " SELECT '1' AS registro
                         , riscos_fiscais.exercicio
                         , CASE WHEN riscos_fiscais.cod_identificador = 5 THEN 1
                                WHEN riscos_fiscais.cod_identificador = 9 THEN 2
                                WHEN riscos_fiscais.cod_identificador = 1 THEN 3
                                WHEN riscos_fiscais.cod_identificador = 3 THEN 5
                                WHEN riscos_fiscais.cod_identificador = 2 THEN 6
                                WHEN riscos_fiscais.cod_identificador = 7 THEN 7
                                WHEN ( riscos_fiscais.cod_identificador = 6 OR riscos_fiscais.cod_identificador = 4
                                  OR riscos_fiscais.cod_identificador = 8 OR riscos_fiscais.cod_identificador = 10 ) THEN 99
                            END AS cod_passivo_contigentes
                         , riscos_fiscais.descricao AS descricao_passivo
                         , riscos_fiscais.valor AS valor_passivo
                         , providencias.descricao AS descricao_providencia
                         , providencias.valor AS valor_providencia
                         , row_number() over( order by riscos_fiscais.cod_risco ) as sequencial
                      FROM stn.riscos_fiscais
                INNER JOIN stn.providencias
                        ON providencias.cod_risco = riscos_fiscais.cod_risco
                       AND providencias.cod_entidade = riscos_fiscais.cod_entidade
                       AND providencias.exercicio = riscos_fiscais.exercicio 
                     WHERE riscos_fiscais.exercicio = '".$this->getDado('exercicio')."'
                       AND riscos_fiscais.cod_entidade in (".$this->getDado('stEntidades').")
        ";
        return $stSql;
    }
}
