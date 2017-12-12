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
    * Classe de mapeamento para relatorio Razão da receita
    * Data de Criação: 27/06/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.30
*/

/*
$Log$
Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TContabilidadeRazaoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeRazaoReceita()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('cod_entidade'     ,'varchar',false,'',false,false);
    $this->AddCampo('dt_inicial'       ,'integer',false,'',false,false);
    $this->AddCampo('dt_final'         ,'varchar',false,'',false,false);
    $this->AddCampo('data'             ,'varchar',false,'',false,false);
    $this->AddCampo('mes'              ,'integer',false,'',false,false);
    $this->AddCampo('cod_receita'      ,'integer',false,'',false,false);
    $this->AddCampo('demonstrar'       ,'varchar',false,'',false,false);

}

function montaRecuperaTodos()
{
    $stSql .= "SELECT ORE.cod_receita                                                           \n";
    $stSql .= "      ,ORE.cod_entidade                                                          \n";
    $stSql .= "      ,OCR.descricao AS nom_conta                                                \n";
    $stSql .= "      ,OCR.cod_estrutural                                                        \n";
    $stSql .= "      ,contabilidade.fn_recupera_contra_partida_completa( CLV.exercicio      \n";
    $stSql .= "                                                             ,CLV.cod_lote       \n";
    $stSql .= "                                                             ,CLV.tipo           \n";
    $stSql .= "                                                             ,CLV.sequencia      \n";
    $stSql .= "                                                             ,CLV.tipo_valor     \n";
    $stSql .= "                                                             ,CLV.cod_entidade   \n";
    $stSql .= "      ) as contra_partida                                                        \n";
    $stSql .= "      ,CHC.nom_historico                                                         \n";
    $stSql .= "      ,CL.complemento                                                            \n";
    $stSql .= "      ,TO_CHAR( CLO.dt_lote, 'dd/mm/yyyy' ) as dt_lote                           \n";
    $stSql .= "      ,CLV.vl_lancamento                                                         \n";
    $stSql .= "      ,CL.tipo                                                                   \n";
    $stSql .= "      ,CLR.estorno                                                                \n";
    $stSql .= "      ,CLV.tipo_valor                                                             \n";
    $stSql .= "FROM orcamento.receita                AS ORE                                 \n";
    $stSql .= "    ,orcamento.conta_receita          AS OCR                                 \n";
    $stSql .= "    ,contabilidade.lancamento_receita AS CLR                                 \n";
    $stSql .= "    ,contabilidade.lancamento         AS CL                                  \n";
    $stSql .= "    ,contabilidade.historico_contabil AS CHC                                 \n";
    $stSql .= "    ,contabilidade.lote               AS CLO                                 \n";
    $stSql .= "    ,contabilidade.valor_lancamento   AS CLV                                 \n";
    $stSql .= " -- Join com orcamento.conta_receita                                             \n";
    $stSql .= "WHERE ORE.exercicio     = OCR.exercicio                                          \n";
    $stSql .= "  AND ORE.cod_conta     = OCR.cod_conta                                          \n";
    $stSql .= " -- Join com contabilidade.lancamento_receita                                    \n";
    $stSql .= "  AND ORE.exercicio    = CLR.exercicio                                           \n";
    $stSql .= "  AND ORE.cod_receita  = CLR.cod_receita                                         \n";
    $stSql .= "  AND ORE.cod_entidade = CLR.cod_entidade                                        \n";
    $stSql .= " -- Join com contabilidade.lancamento                                            \n";
    $stSql .= "  AND CLR.exercicio    = CL.exercicio                                            \n";
    $stSql .= "  AND CLR.cod_entidade = CL.cod_entidade                                         \n";
    $stSql .= "  AND CLR.tipo         = CL.tipo                                                 \n";
    $stSql .= "  AND CLR.cod_lote     = CL.cod_lote                                             \n";
    $stSql .= "  AND CLR.sequencia    = CL.sequencia                                            \n";
    $stSql .= " -- Join com contabilidade.historico_contabil                                    \n";
    $stSql .= "  AND CL.exercicio     = CHC.exercicio                                           \n";
    $stSql .= "  AND CL.cod_historico = CHC.cod_historico                                       \n";
    $stSql .= " -- Join com contabilidade.lote                                                  \n";
    $stSql .= "  AND CL.exercicio     = CLO.exercicio                                           \n";
    $stSql .= "  AND CL.cod_entidade  = CLO.cod_entidade                                        \n";
    $stSql .= "  AND CL.tipo          = CLO.tipo                                                \n";
    $stSql .= "  AND CL.cod_lote      = CLO.cod_lote                                            \n";
    $stSql .= " -- Join com contabilidade.lancamento_valor                                      \n";
    $stSql .= "  AND CL.exercicio     = CLV.exercicio                                           \n";
    $stSql .= "  AND CL.cod_entidade  = CLV.cod_entidade                                        \n";
    $stSql .= "  AND CL.tipo          = CLV.tipo                                                \n";
    $stSql .= "  AND CL.cod_lote      = CLV.cod_lote                                            \n";
    $stSql .= "  AND CL.sequencia     = CLV.sequencia                                           \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaValorReceita(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaValorReceita().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorReceita()
{
    $stSql .= "SELECT orcamento.fn_receita_realizada_periodo( '".$this->getDado( "exercicio" )."'    \n";
    $stSql .= "                                              ,'".$this->getDado( "cod_entidade" )."' \n";
    $stSql .= "                                              ,".$this->getDado( "cod_receita" )."    \n";
    $stSql .= "                                              ,'".$this->getDado( "dt_inicial" )."'   \n";
    $stSql .= "                                              ,'".$this->getDado( "dt_final" )."'     \n";
    $stSql .= ") AS vl_receita                                                                       \n";

    return $stSql;
}

}
