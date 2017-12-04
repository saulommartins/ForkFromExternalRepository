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
    * Classe de mapeamento para relatório Extrato de Recurso
    * Data de Criação: 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.29
*/

/*
$Log$
Revision 1.6  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Classe de mapeamento para relatório Extrato de Recurso
  * Data de Criação: 04/07/2005

  * @author Analista: Dieine da Silva
  * @author Desenvolvedor: Anderson R. M. Buzo

*/
class TOrcamentoExtratoRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoExtratoRecurso()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('cod_suplementacao','integer' ,true ,''   ,true ,true );
    $this->AddCampo('exercicio'        ,'char'    ,true ,'04' ,true ,true );
    $this->AddCampo('cod_norma'        ,'integer' ,true ,''   ,false,true );
    $this->AddCampo('cod_tipo'         ,'integer' ,true ,''   ,false,true );
    $this->AddCampo('dt_suplementacao' ,'date'    ,true ,''   ,false,false);
    $this->AddCampo('motivo'           ,'text'    ,false,''   ,false,false);
}

/**
    * Método para montar SQL para recuperar valores para o relatorio extrato de recurso
    * @access Private
    * @return String $stSql
*/
function montaRecuperaTodos()
{
    $stSql .= "SELECT ORE.exercicio                                                                                         \n";
    $stSql .= "      ,ORE.cod_recurso                                                                                       \n";
    $stSql .= "      ,ORE.nom_recurso                                                                                       \n";
    $stSql .= "      ,orcamento.fn_receita_realizada_recurso_periodo( '".$this->getDado('exercicio')."'                     \n";
    $stSql .= "                                                      ,'".$this->getDado('cod_entidade')."'                  \n";
    $stSql .= "                                                      ,ORE.cod_recurso                                       \n";
    $stSql .= "                                                      ,'".$this->getDado('dt_inicial')."'                    \n";
    $stSql .= "                                                      ,'".$this->getDado('dt_final')."' ) AS saldo           \n";
    $stSql .= "      ,empenho.fn_consultar_valor_pago_recurso   ( '".$this->getDado('exercicio')."'                         \n";
    $stSql .= "                                                 , '".$this->getDado('cod_entidade')."'                      \n";
    $stSql .= "                                                 , ORE.cod_recurso                                           \n";
    $stSql .= "                                                 , '".$this->getDado('dt_inicial')."'                        \n";
    $stSql .= "                                                 , '".$this->getDado('dt_final')."'                          \n";
    $stSql .= "                                                 , '' ) as vl_pago                                           \n";
    $stSql .= "      ,empenho.fn_consultar_valor_pago_recurso_rp( '".$this->getDado('exercicio')."'                         \n";
    $stSql .= "                                                 , '".$this->getDado('cod_entidade')."'                      \n";
    $stSql .= "                                                 , ORE.cod_recurso                                           \n";
    $stSql .= "                                                 , '".$this->getDado('dt_inicial')."'                        \n";
    $stSql .= "                                                 , '".$this->getDado('dt_final')."') as vl_pago_rp           \n";
    $stSql .= "      ,empenho.fn_consultar_valor_pago_anulado_recurso( '".$this->getDado('exercicio')."'                    \n";
    $stSql .= "                                                      , '".$this->getDado('cod_entidade')."'                 \n";
    $stSql .= "                                                      , ORE.cod_recurso                                      \n";
    $stSql .= "                                                      , '".$this->getDado('dt_inicial')."'                   \n";
    $stSql .= "                                                      , '".$this->getDado('dt_final')."'                     \n";
    $stSql .= "                                                      , '' ) as vl_anulado                                   \n";
    $stSql .= "      ,empenho.fn_consultar_valor_pago_anulado_recurso_rp( '".$this->getDado('exercicio')."'                 \n";
    $stSql .= "                                                         , '".$this->getDado('cod_entidade')."'              \n";
    $stSql .= "                                                         , ORE.cod_recurso                                   \n";
    $stSql .= "                                                         , '".$this->getDado('dt_inicial')."'                \n";
    $stSql .= "                                                         , '".$this->getDado('dt_final')."') as vl_anulado_rp \n";
    $stSql .= "FROM orcamento.recurso('".$this->getDado('exercicio')."') AS ORE                                                                            \n";

    return $stSql;
}

/**
    * Método para montar SQL para recuperar saldo anterior por recurso
    * @access Private
    * @return String $stSql
*/
function montaRecuperaSladoAnteriorRecurso()
{
    $stSql  = "SELECT coalesce( sum( vl_lancamento ), 0.00 ) as saldo           \n";
    $stSql .= "      ,CPR.cod_recurso                                           \n";
    $stSql .= "FROM contabilidade.valor_lancamento AS CVL                       \n";
    $stSql .= "  --Ligação valor_lancamento : conta_debito                      \n";
    $stSql .= "     LEFT JOIN contabilidade.conta_debito AS CCD             \n";
    $stSql .= "     ON( CVL.exercicio    = CCD.exercicio                        \n";
    $stSql .= "     AND CVL.cod_entidade = CCD.cod_entidade                     \n";
    $stSql .= "     AND CVL.tipo_valor   = CCD.tipo_valor                       \n";
    $stSql .= "     AND CVL.tipo         = CCD.tipo                             \n";
    $stSql .= "     AND CVL.cod_lote     = CCD.cod_lote                         \n";
    $stSql .= "     AND CVL.sequencia    = CCD.sequencia  )                     \n";
    $stSql .= "  --Ligação valor_lancamento : conta_credito                     \n";
    $stSql .= "     LEFT JOIN contabilidade.conta_credito AS CCC            \n";
    $stSql .= "     ON( CVL.exercicio    = CCC.exercicio                        \n";
    $stSql .= "     AND CVL.cod_entidade = CCC.cod_entidade                     \n";
    $stSql .= "     AND CVL.tipo_valor   = CCC.tipo_valor                       \n";
    $stSql .= "     AND CVL.tipo         = CCC.tipo                             \n";
    $stSql .= "     AND CVL.cod_lote     = CCC.cod_lote                         \n";
    $stSql .= "     AND CVL.sequencia    = CCC.sequencia  )                     \n";
    $stSql .= "    ,contabilidade.lancamento       AS CLA                   \n";
    $stSql .= "    ,contabilidade.lote             AS CLO                   \n";
    $stSql .= "    ,contabilidade.plano_analitica  AS CPA                   \n";
    $stSql .= "    ,contabilidade.plano_recurso    AS CPR                   \n";
    $stSql .= "  --Ligação valor_lancamento : lancamento                        \n";
    $stSql .= "WHERE CVL.exercicio    = CLA.exercicio                           \n";
    $stSql .= "  AND CVL.cod_entidade = CLA.cod_entidade                        \n";
    $stSql .= "  AND CVL.tipo         = CLA.tipo                                \n";
    $stSql .= "  AND CVL.cod_lote     = CLA.cod_lote                            \n";
    $stSql .= "  AND CVL.sequencia    = CLA.sequencia                           \n";
    $stSql .= "  --Ligação lancamento : lote                                    \n";
    $stSql .= "  AND CLA.exercicio    = CLO.exercicio                           \n";
    $stSql .= "  AND CLA.cod_entidade = CLO.cod_entidade                        \n";
    $stSql .= "  AND CLA.tipo         = CLO.tipo                                \n";
    $stSql .= "  AND CLA.cod_lote     = CLO.cod_lote                            \n";
    $stSql .= "  --Ligação conta_debito/credito : plano_analitica               \n";
    $stSql .= "  AND CVL.exercicio    = CPA.exercicio                           \n";
    $stSql .= "  AND COALESCE( CCD.cod_plano, CCC.cod_plano ) = CPA.cod_plano   \n";
    $stSql .= "  --Ligação plano_analitica : plano_recurso                      \n";
    $stSql .= "  AND CPA.exercicio    = CPR.exercicio                           \n";
    $stSql .= "  AND CPA.cod_plano    = CPR.cod_plano                           \n";
    $stSql .= "  --Filtros                                                      \n";
    $stSql .= "  AND CVL.tipo         = 'I'                                     \n";

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
function recuperaSaldoAnteriorRecurso(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaSladoAnteriorRecurso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
