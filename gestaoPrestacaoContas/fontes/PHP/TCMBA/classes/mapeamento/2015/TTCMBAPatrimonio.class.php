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
    * Data de Criação: 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62952 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-08-21 11:39:18 -0300 (Qui, 21 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAPatrimonio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMBAPatrimonio()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
    $stSql = "
            SELECT 1 AS tipo_registro
                 , ".$this->getDado('inCodGestora')." AS unidade_gestora
                 , TO_CHAR(bem.".$this->getDado('tipoPeriodo').",'yyyy') AS exercicio
                 , bem.num_placa AS tombo_bem
                 , tipo_bem.cod_tipo_tcm AS tipo_bem
                 , TRANSLATE(bem.descricao, ';,!?', '') AS descricao
                 , bem_comprado.cod_empenho
                 , '2' AS anterior_siga
                 , bem.vl_bem AS valor_bem
                 , sw_cgm.nom_cgm AS funcionario_responsavel
                 , sw_cgm_pessoa_fisica.cpf
                 , TO_CHAR(bem.".$this->getDado('tipoPeriodo').",'dd/mm/yyyy') AS data_aquisicao
                 , TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS data_baixa
                 , 000 as num_orgao
                 , 000 as num_unidade

              FROM patrimonio.bem

        INNER JOIN tcmba.tipo_bem
                ON tipo_bem.cod_natureza = bem.cod_natureza
               AND tipo_bem.cod_grupo    = bem.cod_grupo
               
        INNER JOIN (
                     SELECT bem.cod_bem
                          , CASE WHEN bem_comprado.cod_bem IS NOT NULL
                                 THEN bem_comprado.cod_empenho
                                 ELSE bem_comprado_empenho.cod_empenho
                             END AS cod_empenho
                          , CASE WHEN bem_comprado.cod_bem IS NOT NULL
                                 THEN bem_comprado.cod_entidade
                                 ELSE bem_comprado_empenho.cod_entidade
                            END AS cod_entidade
                       FROM patrimonio.bem
                  
                  LEFT JOIN patrimonio.bem_comprado
                         ON bem_comprado.cod_bem = bem.cod_bem
                  
                  LEFT JOIN patrimonio.bem_comprado_empenho
                         ON bem_comprado_empenho.cod_bem = bem.cod_bem
                         
                 ) AS bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem
         
         LEFT JOIN (
                     SELECT bem_responsavel.cod_bem
                          , bem_responsavel.numcgm
                       FROM patrimonio.bem_responsavel
                 
                 INNER JOIN ( SELECT cod_bem
                                   , MAX(timestamp) AS timestamp
                                FROM patrimonio.bem_responsavel
                            GROUP BY cod_bem
                            ) AS bem_responsavel_max
                         ON bem_responsavel_max.cod_bem = bem_responsavel.cod_bem
                        AND bem_responsavel_max.timestamp = bem_responsavel.timestamp
                        
                   ) AS bem_responsavel
                ON bem_responsavel.cod_bem = bem.cod_bem
         
         LEFT JOIN sw_cgm
                ON sw_cgm.numcgm = bem_responsavel.numcgm
         
         LEFT JOIN sw_cgm_pessoa_fisica
                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
         
         LEFT JOIN patrimonio.bem_baixado
                ON bem_baixado.cod_bem = bem.cod_bem
         
         LEFT JOIN(
                     SELECT historico_bem.cod_bem
                          , historico_bem.cod_orgao
                       FROM patrimonio.historico_bem
                 
                 INNER JOIN ( SELECT historico_bem.cod_bem
                                   , MAX(historico_bem.timestamp) AS timestamp
                                FROM patrimonio.historico_bem
                            GROUP BY cod_bem
                            ) AS historico_bem_max
                         ON historico_bem_max.cod_bem = historico_bem.cod_bem
                        AND historico_bem_max.timestamp = historico_bem.timestamp
                        
                   ) AS historico_bem
                ON historico_bem.cod_bem = bem.cod_bem
             
             WHERE bem.".$this->getDado('tipoPeriodo')." BETWEEN TO_DATE('".$this->getDado('dt_inicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_fim')."','dd/mm/yyyy')
                OR (
                    ((bem_baixado.cod_bem IS NOT NULL) AND (bem_baixado.dt_baixa BETWEEN TO_DATE('".$this->getDado('dt_inicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_fim')."','dd/mm/yyyy')))
                   ) \n ";
    
    if ( $this->getDado('cod_entidade') != '' ) {
        $stSql .= " AND bem_comprado.cod_entidade = ".$this->getDado('cod_entidade')." ";
    }
    
    return $stSql;
}

}

?>