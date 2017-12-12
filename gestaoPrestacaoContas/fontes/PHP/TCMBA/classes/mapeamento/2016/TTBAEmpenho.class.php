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
    * Data de Criação: 10/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63396 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.4  2007/10/13 20:05:49  diego
Corrigindo formatação e informações

Revision 1.3  2007/10/07 22:31:11  diego
Corrigindo formatação e informações

Revision 1.2  2007/07/16 02:41:13  diego
retirado dado fixo de 2006

Revision 1.1  2007/07/11 04:46:53  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

/**
  *
  * Data de Criação: 10/07/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAEmpenho extends TEmpenhoEmpenho
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::TEmpenhoEmpenho();

    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT despesa.exercicio                                                                  
                      ,despesa.num_orgao                                                              
                      ,despesa.num_unidade                                                            
                      ,despesa.cod_funcao                                                             
                      ,despesa.cod_subfuncao                                                          
                      ,despesa.cod_programa                                                           
                      ,despesa.num_pao                                                                
                      ,orcamento.fn_consulta_tipo_pao(despesa.exercicio,despesa.num_pao) AS tipo_pao      
                      ,despesa.cod_recurso                                                            
                      ,REPLACE(conta_despesa.cod_estrutural,'.','') AS estrutural                           
                      ,sw_cgm.nom_cgm                                                                
                      ,empenho.cod_empenho                                                            
                      ,CASE WHEN pre_empenho.cod_tipo = 1 THEN 3
                            WHEN pre_empenho.cod_tipo = 2 THEN 2
                            WHEN pre_empenho.cod_tipo = 3 THEN 1
                      END AS tipo_empenho 
                      ,TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') AS dt_empenho                         
                      ,CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                                 
                            WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj                                
                            ELSE ''                                                              
                      END AS cpf_cnpj
                      ,CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN 1
                            ELSE 2
                      END AS pf_pj
                      ,sume.valor_empenhado
                      , 1 AS tipo_registro
                      , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                      , ".$this->getDado('exercicio')."::VARCHAR||LPAD(".$this->getDado('mes')."::VARCHAR,2,'0') AS competencia
                      , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||licitacao.cod_licitacao::VARCHAR AS processo_licitatorio
                      , 'N' AS contrato_aplicavel
                      , 'N' AS licitacao_sujeito
                      , pre_empenho.descricao AS historico

                  FROM empenho.empenho

            INNER JOIN empenho.pre_empenho
                    ON empenho.exercicio = pre_empenho.exercicio
                   AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = cgm_beneficiario

             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

            INNER JOIN empenho.pre_empenho_despesa
                    ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                   AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

            INNER JOIN orcamento.conta_despesa
                    ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                   AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

            INNER JOIN orcamento.despesa
                    ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                   AND despesa.exercicio = pre_empenho_despesa.exercicio

             LEFT JOIN empenho.item_pre_empenho_julgamento
                    ON item_pre_empenho_julgamento.exercicio=pre_empenho.exercicio
                   AND item_pre_empenho_julgamento.cod_pre_empenho=pre_empenho.cod_pre_empenho
                   AND item_pre_empenho_julgamento.num_item = (SELECT E_IPEJ.num_item
                                                                 FROM empenho.item_pre_empenho_julgamento AS E_IPEJ
                                                                WHERE E_IPEJ.exercicio=pre_empenho.exercicio
                                                                  AND E_IPEJ.cod_pre_empenho=pre_empenho.cod_pre_empenho LIMIT 1
                                                              )

             LEFT JOIN compras.mapa_cotacao
                    ON mapa_cotacao.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                   AND mapa_cotacao.exercicio_cotacao = item_pre_empenho_julgamento.exercicio

             LEFT JOIN licitacao.licitacao
                    ON licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa
                   AND licitacao.cod_mapa = mapa_cotacao.cod_mapa                                            

            INNER JOIN (
                        SELECT exercicio
                             , cod_pre_empenho
                             , COALESCE(SUM(vl_total),0.00) AS valor_empenhado
                          FROM empenho.item_pre_empenho
                         WHERE exercicio = '".$this->getDado('exercicio')."'
                         GROUP BY exercicio,cod_pre_empenho
                       ) AS sume
                    ON sume.exercicio = pre_empenho.exercicio
                   AND sume.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                                                  
                 WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                   AND empenho.cod_entidade IN ( ".$this->getDado('entidades')." )
                   AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
        ";
        
    return $stSql;
}

}
