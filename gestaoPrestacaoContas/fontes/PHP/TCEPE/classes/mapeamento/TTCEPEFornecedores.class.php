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
    * 
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEFornecedores.class.php 60286 2014-10-10 13:41:30Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEFornecedores extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEFornecedores()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaFornecedor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaFornecedor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaFornecedor().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFornecedor()
    {
        $stSql .=" SELECT CASE --WHEN folha = TRUE THEN ''                                                                                                              \n";
        $stSql .="             WHEN pf.cpf IS NOT NULL THEN pf.cpf                                                                                                      \n";
        $stSql .="             WHEN pj.cnpj IS NOT NULL THEN pj.cnpj                                                                                                    \n";
        $stSql .="        END AS num_documento                                                                                                                          \n";
        $stSql .="      , CASE WHEN cg.numcgm = cgm_tipo_credor.cgm_credor THEN cgm_tipo_credor.cod_tipo_credor                                                         \n";
        $stSql .="             WHEN pf.numcgm IS NOT NULL THEN 1                                                                                                        \n";
        $stSql .="             ELSE 2                                                                                                                                   \n";
        $stSql .="        END AS tipo_documento                                                                                                                         \n";
        $stSql .="      , cg.nom_cgm                                                                                                                                    \n";
        $stSql .="      , sigla_uf                                                                                                                                      \n";
        $stSql .="      , nom_municipio                                                                                                                                 \n";
        $stSql .="   FROM (                                                                                                                                             \n";
        $stSql .="           SELECT cg.numcgm                                                                                                                           \n";
        $stSql .="                , cg.nom_cgm                                                                                                                          \n";
        $stSql .="                --Está comentado a verificação se é Folha de Pagamento                                                                                \n";
        $stSql .="                /*, CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 9, 2) IN ('01','03','04','05','09','11','16','48','94')                            \n"; 
        $stSql .="                            THEN true                                                                                                                 \n"; 
        $stSql .="                       WHEN ''||SUBSTR(conta_despesa.cod_estrutural, 9, 2)||SUBSTR(conta_despesa.cod_estrutural, 12, 2) IN ('3626','3628','3699')     \n"; 
        $stSql .="                            THEN true                                                                                                                 \n"; 
        $stSql .="                       WHEN SUBSTR(conta_despesa.cod_estrutural, 0, 14) IN ('3.1.9.0.92.01','3.1.9.0.92.02','3.1.7.1.92.01','3.1.7.1.92.02',          \n"; 
        $stSql .="                                   '3.1.9.1.92.01','3.1.9.1.92.02','3.3.9.0.36.07')                                                                   \n"; 
        $stSql .="                            THEN true                                                                                                                 \n"; 
        $stSql .="                            ELSE false                                                                                                                \n"; 
        $stSql .="                  END                                                                                                                                 \n"; 
        $stSql .="                  AS folha */                                                                                                                         \n";
        $stSql .="                , sw_uf.sigla_uf                                                                                                                      \n";
        $stSql .="                , sw_municipio.nom_municipio                                                                                                          \n";
        $stSql .="             FROM sw_cgm AS cg                                                                                                                        \n";
        $stSql .="                       JOIN sw_uf                                                                                                                     \n";
	$stSql .="                         ON sw_uf.cod_uf=cg.cod_uf                                                                                                    \n";
	$stSql .="                       JOIN sw_municipio                                                                                                              \n";
	$stSql .="                         ON sw_municipio.cod_municipio=cg.cod_municipio                                                                               \n";
	$stSql .="                        AND sw_municipio.cod_uf=cg.cod_uf                                                                                             \n";
        $stSql .="                , empenho.pre_empenho AS pre                                                                                                          \n";
        $stSql .="                       JOIN empenho.empenho                                                                                                           \n";
	$stSql .="                         ON empenho.exercicio=pre.exercicio                                                                                           \n";
	$stSql .="                        AND empenho.cod_pre_empenho=pre.cod_pre_empenho                                                                               \n";
        $stSql .="                       /*JOIN empenho.pre_empenho_despesa AS EPED                                                                                     \n";
        $stSql .="                         ON EPED.cod_pre_empenho=pre.cod_pre_empenho                                                                                  \n";
        $stSql .="                        AND EPED.exercicio=pre.exercicio                                                                                              \n";
	$stSql .="                       JOIN orcamento.despesa AS OD                                                                                                   \n";
        $stSql .="                         ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa                                                           \n";
        $stSql .="                       JOIN orcamento.conta_despesa                                                                                                   \n";
        $stSql .="                         ON conta_despesa.exercicio=OD.exercicio                                                                                      \n";
        $stSql .="                        AND conta_despesa.cod_conta=OD.cod_conta*/                                                                                    \n";
        $stSql .="            WHERE cg.numcgm = pre.cgm_beneficiario                                                                                                    \n";
        $stSql .="              AND pre.exercicio = '".$this->getDado('stExercicio')."'                                                                                 \n";
        $stSql .="              AND empenho.dt_empenho                                                                                                                  \n";
        $stSql .="                  BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')                                                                    \n";
        $stSql .="                      AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy' )                                                                     \n";
        $stSql .="         GROUP BY cg.numcgm                                                                                                                           \n";
	$stSql .="                , cg.nom_cgm                                                                                                                          \n";
        $stSql .="                --, conta_despesa.cod_estrutural                                                                                                      \n";
        $stSql .="                , sw_uf.sigla_uf                                                                                                                      \n";
        $stSql .="                , sw_municipio.nom_municipio                                                                                                          \n";
        $stSql .="        ) AS cg                                                                                                                                       \n";
        $stSql .="LEFT JOIN sw_cgm_pessoa_fisica AS pf                                                                                                                  \n";
        $stSql .="       ON ( cg.numcgm = pf.numcgm )                                                                                                                   \n";
        $stSql .="LEFT JOIN sw_cgm_pessoa_juridica AS pj                                                                                                                \n";
        $stSql .="       ON ( cg.numcgm = pj.numcgm )                                                                                                                   \n";
        $stSql .="LEFT JOIN tcepe.cgm_tipo_credor                                                                                                                       \n";
        $stSql .="       ON ( cg.numcgm = cgm_tipo_credor.cgm_credor )                                                                                                  \n"; 
        $stSql .=" GROUP BY pf.cpf                                                                                                                                      \n";
	$stSql .="        , pj.cnpj                                                                                                                                     \n";
	$stSql .="        , pf.numcgm                                                                                                                                   \n";
	$stSql .="        , cg.nom_cgm                                                                                                                                  \n";
	$stSql .="        , tipo_documento                                                                                                                              \n";
        $stSql .="        , sigla_uf                                                                                                                                    \n";
	$stSql .="        , nom_municipio                                                                                                                               \n";
        $stSql .=" ORDER BY cg.nom_cgm                                                                                                                                  \n";

        return $stSql;
    }

}
