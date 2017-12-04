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
    * Classe de mapeamento para relatorio anexo 15
    * Data de Criação: 06/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-10-16 10:30:31 -0200 (Ter, 16 Out 2007) $

    * Casos de uso: uc-02.02.12
*/

/*
$Log$
Revision 1.16  2007/10/16 12:30:03  cako
Ticket#10368#

Revision 1.15  2007/04/17 20:16:13  luciano
#8815#

Revision 1.14  2006/12/11 22:22:05  cleisson
Bug #4513#

Revision 1.12  2006/07/26 13:22:58  jose.eduardo
Bug #4513#

Revision 1.11  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.PLANO_CONTA
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeRelatorioAnexo15 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeRelatorioAnexo15()
{
    parent::Persistente();
    $this->setTabela('');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo('cod_entidade','varchar',false,'',false,false);
    $this->AddCampo('dt_inicial'  ,'integer',false,'',false,false);
    $this->AddCampo('dt_final'    ,'varchar',false,'',false,false);

}

function montaRecuperaTodos()
{
    $stSql  = "SELECT tbl.cod_estrutural                                                                                \n";
    $stSql .= "      ,abs( sum( tbl.vl_arrecadado_debito ) + sum( tbl.vl_arrecadado_credito ) ) as vl_arrecadado        \n";
    $stSql .= "      ,OCR.nom_conta                                                                                     \n";
    $stSql .= "      ,CASE WHEN publico.fn_nivel( tbl.cod_estrutural ) > 5                                              \n";
    $stSql .= "        THEN 5                                                                                           \n";
    $stSql .= "        ELSE publico.fn_nivel( tbl.cod_estrutural )                                                      \n";
    $stSql .= "      END AS nivel                                                                                       \n";
    $stSql .= "FROM(                                                                                                    \n";
    $stSql .= "      SELECT substr( OPC.cod_estrutural, 1, 9 )  AS cod_estrutural                                       \n";
    $stSql .= "            ,OPC.exercicio                                                                               \n";
    $stSql .= "            ,sum( coalesce( CCD.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_debito                 \n";
    $stSql .= "            ,sum( coalesce( CCC.vl_lancamento, 0.00 ) ) * ( -1 ) AS vl_arrecadado_credito                \n";
    $stSql .= "      FROM contabilidade.plano_conta    AS OPC                                                       \n";
    $stSql .= "      -- Join com plano analitica                                                                        \n";
    $stSql .= "      LEFT JOIN contabilidade.plano_analitica AS OCA                                                 \n";
    $stSql .= "      ON( OPC.cod_conta = OCA.cod_conta                                                                  \n";
    $stSql .= "      AND OPC.exercicio = OCA.exercicio  )                                                               \n";
    $stSql .= "      -- Join com contabilidade.valor_lancamento                                                         \n";
    $stSql .= "      LEFT JOIN ( SELECT CCD.cod_plano                                                                   \n";
    $stSql .= "                        ,CCD.exercicio                                                                   \n";
    $stSql .= "                        ,sum( vl_lancamento ) as vl_lancamento                                           \n";
    $stSql .= "                  FROM contabilidade.conta_debito     AS CCD                                         \n";
    $stSql .= "                      ,contabilidade.valor_lancamento AS CVLD                                        \n";
    $stSql .= "                      ,contabilidade.lancamento       AS CLA                                        \n";
    $stSql .= "                      ,contabilidade.lote             AS CLO                                         \n";
    $stSql .= "                  WHERE CCD.cod_lote       = CVLD.cod_lote                                               \n";
    $stSql .= "                    AND CCD.tipo           = CVLD.tipo                                                   \n";
    $stSql .= "                    AND CCD.sequencia      = CVLD.sequencia                                              \n";
    $stSql .= "                    AND CCD.exercicio      = CVLD.exercicio                                              \n";
    $stSql .= "                    AND CCD.tipo_valor     = CVLD.tipo_valor                                             \n";
    $stSql .= "                    AND CCD.cod_entidade   = CVLD.cod_entidade                                           \n";
    $stSql .= "                    AND CVLD.tipo_valor    = 'D'                                                         \n";

    $stSql .= "                    AND CVLD.cod_lote      = CLA.cod_lote                                                 \n";
    $stSql .= "                    AND CVLD.tipo          = CLA.tipo                                                     \n";
    $stSql .= "                    AND CVLD.cod_entidade  = CLA.cod_entidade                                             \n";
    $stSql .= "                    AND CVLD.exercicio     = CLA.exercicio                                                \n";
    $stSql .= "                    AND CVLD.sequencia     = CLA.sequencia                                                \n";

    $stSql .= "                    AND CLA.cod_lote      = CLO.cod_lote                                                \n";
    $stSql .= "                    AND CLA.tipo          = CLO.tipo                                                    \n";
    $stSql .= "                    AND CLA.cod_entidade  = CLO.cod_entidade                                            \n";
    $stSql .= "                    AND CLA.exercicio     = CLO.exercicio                                               \n";

    $stSql .= "                    AND CCD.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                    AND CVLD.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                    AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                        AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                    AND CLA.cod_historico not between 800 and 899                                          \n";
    $stSql .= "                  GROUP BY CCD.cod_plano                                                                 \n";
    $stSql .= "                          ,CCD.exercicio                                                                 \n";
    $stSql .= "                  ORDER BY CCD.cod_plano                                                                 \n";
    $stSql .= "                          ,CCD.exercicio                                                                 \n";
    $stSql .= "      ) AS CCD ON( OCA.cod_plano = CCD.cod_plano                                                         \n";
    $stSql .= "               AND OCA.exercicio = CCD.exercicio                                                         \n";
    $stSql .= "      )                                                                                                  \n";
    $stSql .= "      -- Join com contabilidade.valor_lancamento                                                         \n";
    $stSql .= "      LEFT JOIN ( SELECT CCC.cod_plano                                                                   \n";
    $stSql .= "                        ,CCC.exercicio                                                                   \n";
    $stSql .= "                        ,sum(vl_lancamento) as vl_lancamento                                             \n";
    $stSql .= "                  FROM contabilidade.conta_credito    AS CCC                                         \n";
    $stSql .= "                      ,contabilidade.valor_lancamento AS CVLC                                        \n";
    $stSql .= "                      ,contabilidade.lancamento       AS CLA                                        \n";
    $stSql .= "                      ,contabilidade.lote             AS CLO                                         \n";
    $stSql .= "                  WHERE CCC.cod_lote       = CVLC.cod_lote                                               \n";
    $stSql .= "                    AND CCC.tipo           = CVLC.tipo                                                   \n";
    $stSql .= "                    AND CCC.sequencia      = CVLC.sequencia                                              \n";
    $stSql .= "                    AND CCC.exercicio      = CVLC.exercicio                                              \n";
    $stSql .= "                    AND CCC.tipo_valor     = CVLC.tipo_valor                                             \n";
    $stSql .= "                    AND CCC.cod_entidade   = CVLC.cod_entidade                                           \n";
    $stSql .= "                    AND CVLC.tipo_valor    = 'C'                                                         \n";

    $stSql .= "                    AND CVLC.cod_lote      = CLA.cod_lote                                                 \n";
    $stSql .= "                    AND CVLC.tipo          = CLA.tipo                                                     \n";
    $stSql .= "                    AND CVLC.cod_entidade  = CLA.cod_entidade                                             \n";
    $stSql .= "                    AND CVLC.exercicio     = CLA.exercicio                                                \n";
    $stSql .= "                    AND CVLC.sequencia     = CLA.sequencia                                                \n";

    $stSql .= "                    AND CLA.cod_lote      = CLO.cod_lote                                                \n";
    $stSql .= "                    AND CLA.tipo          = CLO.tipo                                                    \n";
    $stSql .= "                    AND CLA.cod_entidade  = CLO.cod_entidade                                            \n";
    $stSql .= "                    AND CLA.exercicio     = CLO.exercicio                                               \n";

    $stSql .= "                    AND CCC.exercicio      = '".$this->getDado('exercicio')."'                           \n";
    $stSql .= "                    AND CVLC.cod_entidade  IN( ".$this->getDado('cod_entidade')." )                      \n";
    $stSql .= "                    AND CLO.dt_lote BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                                        AND TO_DATE( '".$this->getDado('dt_final')  ."', 'dd/mm/yyyy' )  \n";
    $stSql .= "                    AND CLA.cod_historico not between 800 and 899                                          \n";
    $stSql .= "                  GROUP BY CCC.cod_plano                                                                 \n";
    $stSql .= "                          ,CCC.exercicio                                                                 \n";
    $stSql .= "                  ORDER BY CCC.cod_plano                                                                 \n";
    $stSql .= "                          ,CCC.exercicio                                                                 \n";
    $stSql .= "      ) AS CCC ON ( OCA.cod_plano = CCC.cod_plano                                                        \n";
    $stSql .= "               AND  OCA.exercicio = CCC.exercicio                                                        \n";
    $stSql .= "      )                                                                                                  \n";
    $stSql .= "      WHERE OPC.exercicio = '".$this->getDado('exercicio')."'                                            \n";
    $stSql .= "      AND ( OPC.cod_estrutural LIKE '4.1%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '4.2%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '4.7%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '4.8%'                                                               \n";
    //deduções
    if ((int) $this->getDado('exercicio')<2009 ) {
        $stSql .= "      OR     OPC.cod_estrutural LIKE '4.9%'                                                          \n";
    } else {
        $stSql .= "      OR    OPC.cod_estrutural LIKE '9.%'                                                            \n";
    }
    $stSql .= "      OR    OPC.cod_estrutural LIKE '3.3%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '3.4%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '5.1.2%'                                                             \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '5.1.3%'                                                             \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '5.2%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '6.1%'                                                               \n";
    $stSql .= "      OR    OPC.cod_estrutural LIKE '6.2%' )                                                             \n";
    $stSql .= "      GROUP BY OPC.cod_estrutural                                                                        \n";
    $stSql .= "              ,OPC.exercicio                                                                             \n";
    $stSql .= "      ORDER BY OPC.cod_estrutural                                                                        \n";
    $stSql .= "              ,OPC.exercicio                                                                             \n";
    $stSql .= ") AS tbl                                                                                                 \n";
    $stSql .= ",contabilidade.plano_conta AS OCR                                                                        \n";
    $stSql .= "WHERE tbl.cod_estrutural = substr( OCR.cod_estrutural, 1, 9 )                                            \n";
    //deduções até 2008
    if ((int) $this->getDado('exercicio')<2009 ) {
        $stSql .= "AND   (length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9                               \n";
        $stSql .= "OR (  OCR.cod_estrutural ='4.9.7.2.1.01.00.00.00.00' OR                                              \n";
        $stSql .= "      OCR.cod_estrutural ='4.9.7.2.2.01.00.00.00.00'  ) )                                             \n";
    } else {
        $stSql .= "AND   length( publico.fn_mascarareduzida( OCR.cod_estrutural ) ) <= 9                               \n";
    }
    $stSql .= "AND   tbl.exercicio      = OCR.exercicio                                                                 \n";
    $stSql .= "GROUP BY tbl.cod_estrutural                                                                              \n";
    $stSql .= "        ,OCR.nom_conta                                                                                   \n";
    $stSql .= "ORDER BY tbl.cod_estrutural                                                                              \n";
    $stSql .= "        ,OCR.nom_conta                                                                                   \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método
    * montaRecuperaContaAnalitica.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDespesaEmpenho(&$rsRecordSet, $stOrdem = " ORDER BY tbl.cod_estrutural " , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDespesaEmpenho().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDespesaEmpenho()
{
    $stSql .= "SELECT                                                                                                   \n";
    $stSql .= "    cod_estrutural,                                                                                      \n";
    $stSql .= "    nom_conta,                                                                                           \n";
    $stSql .= "    sum(valor) as valor                                                                                  \n";
    $stSql .= "FROM (                                                                                                   \n";
    $stSql .= "(                                                                                                        \n";
    $stSql .= "SELECT                                                                                                   \n";
    $stSql .= "      tbl.cod_estrutural                                                                                 \n";
    $stSql .= "      ,CPC.nom_conta                                                                                     \n";
    $stSql .= "      ,sum(coalesce(tbl.valor,0.00)) as valor                                                            \n";
    $stSql .= "FROM(                                                                                                    \n";
    $stSql .= "     SELECT CPC.exercicio                                                                                \n";
    $stSql .= "           ,substr( CPC.cod_estrutural, 1, 5 ) as cod_estrutural                                         \n";
    $stSql .= "           ,sum(coalesce(EIPE.vl_total,0.00))  as valor                                                  \n";
    $stSql .= "     FROM contabilidade.plano_conta     AS CPC                                                           \n";
    $stSql .= "         ,orcamento.conta_despesa       AS OCD                                                           \n";
    $stSql .= "         ,orcamento.despesa             AS OD                                                            \n";
    $stSql .= "         ,empenho.pre_empenho_despesa   AS EPED                                                          \n";
    $stSql .= "         ,empenho.item_pre_empenho      AS EIPE                                                          \n";
    $stSql .= "         ,empenho.empenho               AS EE                                                            \n";
    $stSql .= "       -- Join com conta_despesa                                                                         \n";
    $stSql .= "     WHERE CPC.exercicio        = OCD.exercicio                                                          \n";
    $stSql .= "       AND CPC.cod_estrutural   = '3.'||OCD.cod_estrutural                                               \n";
    $stSql .= "       -- Join com despesa                                                                               \n";
    $stSql .= "       AND OCD.exercicio        = OD.exercicio                                                           \n";
    $stSql .= "       AND OCD.cod_conta        = OD.cod_conta                                                           \n";
    $stSql .= "       -- Join com pre_empenho_despesa                                                                   \n";
    $stSql .= "       AND OD.exercicio         = EPED.exercicio                                                         \n";
    $stSql .= "       AND OD.cod_despesa       = EPED.cod_despesa                                                       \n";
    $stSql .= "       -- Join com item_pre_empenho                                                                      \n";
    $stSql .= "       AND EIPE.exercicio       = EPED.exercicio                                                         \n";
    $stSql .= "       AND EIPE.cod_pre_empenho = EPED.cod_pre_empenho                                                   \n";
    $stSql .= "       -- Join com Empenho                                                                               \n";
    $stSql .= "       AND EPED.exercicio       = EE.exercicio                                                           \n";
    $stSql .= "       AND EPED.cod_pre_empenho = EE.cod_pre_empenho                                                     \n";
    $stSql .= "       -- filtros                                                                                        \n";
    $stSql .= "       AND (  CPC.cod_estrutural like '3.3%'                                                             \n";
    $stSql .= "           OR CPC.cod_estrutural like '3.4%' )                                                           \n";
    $stSql .= "       AND CPC.exercicio = '".$this->getDado( 'exercicio' )."'                                           \n";
    $stSql .= "       AND OD.cod_entidade IN( ".$this->getDado( 'cod_entidade' )." )                                    \n";
    $stSql .= "       AND EE.dt_empenho >= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy' )                    \n";
    $stSql .= "       AND EE.dt_empenho <= TO_DATE('".$this->getDado('dt_final'  )."','dd/mm/yyyy' )                    \n";
    $stSql .= "     GROUP BY CPC.exercicio                                                                              \n";
    $stSql .= "             ,CPC.cod_estrutural                                                                         \n";
    $stSql .= "     ORDER BY CPC.exercicio                                                                              \n";
    $stSql .= "             ,CPC.cod_estrutural                                                                         \n";
    $stSql .= ") as tbl                                                                                                 \n";
    $stSql .= ",contabilidade.plano_conta AS CPC                                                                        \n";
    $stSql .= "WHERE tbl.exercicio      = CPC.exercicio                                                                 \n";
    $stSql .= "  AND tbl.cod_estrutural = substr( CPC.cod_estrutural,1,5 )                                              \n";
    $stSql .= "  AND publico.fn_nivel( CPC.cod_estrutural ) <= 3                                                        \n";
    $stSql .= " GROUP BY tbl.cod_estrutural                                                                             \n";
    $stSql .= "        ,CPC.nom_conta                                                                                   \n";
    $stSql .= " ORDER BY tbl.cod_estrutural                                                                             \n";
    $stSql .= ")                                                                                                        \n";
    $stSql .= "UNION                                                                                                    \n";
    $stSql .= "(                                                                                                        \n";
    $stSql .= "SELECT                                                                                                   \n";
    $stSql .= "      tbl.cod_estrutural                                                                                 \n";
    $stSql .= "      ,CPC.nom_conta                                                                                     \n";
    $stSql .= "      ,sum(coalesce(tbl.valor,0.00)) as valor                                                            \n";
    $stSql .= "FROM(                                                                                                    \n";
    $stSql .= "        SELECT CPC.exercicio                                                                             \n";
    $stSql .= "           ,substr( CPC.cod_estrutural, 1, 5 ) as cod_estrutural                                         \n";
    $stSql .= "           ,sum(coalesce(EAI.vl_anulado,0.00)*-1)  as valor                                              \n";
    $stSql .= "     FROM contabilidade.plano_conta     AS CPC                                                           \n";
    $stSql .= "         ,orcamento.conta_despesa       AS OCD                                                           \n";
    $stSql .= "         ,orcamento.despesa             AS OD                                                            \n";
    $stSql .= "         ,empenho.pre_empenho_despesa   AS EPED                                                          \n";
    $stSql .= "         ,empenho.item_pre_empenho      AS EIPE                                                          \n";
    $stSql .= "         ,empenho.empenho_anulado_item  AS EAI                                                           \n";
    $stSql .= "         ,empenho.empenho               AS EE                                                            \n";
    $stSql .= "       -- Join com conta_despesa                                                                         \n";
    $stSql .= "     WHERE CPC.exercicio        = OCD.exercicio                                                          \n";
    $stSql .= "       AND CPC.cod_estrutural   = '3.'||OCD.cod_estrutural                                               \n";
    $stSql .= "       -- Join com despesa                                                                               \n";
    $stSql .= "       AND OCD.exercicio        = OD.exercicio                                                           \n";
    $stSql .= "       AND OCD.cod_conta        = OD.cod_conta                                                           \n";
    $stSql .= "       -- Join com pre_empenho_despesa                                                                   \n";
    $stSql .= "       AND OD.exercicio         = EPED.exercicio                                                         \n";
    $stSql .= "       AND OD.cod_despesa       = EPED.cod_despesa                                                       \n";
    $stSql .= "       -- Join com item_pre_empenho                                                                      \n";
    $stSql .= "       AND EIPE.exercicio       = EPED.exercicio                                                         \n";
    $stSql .= "       AND EIPE.cod_pre_empenho = EPED.cod_pre_empenho                                                   \n";
    $stSql .= "                                                                                                         \n";
    $stSql .= "       AND EIPE.cod_pre_empenho    = EAI.cod_pre_empenho                                                 \n";
    $stSql .= "       AND EIPE.exercicio          = EAI.exercicio                                                       \n";
    $stSql .= "       AND EIPE.num_item           = EAI.num_item                                                        \n";
    $stSql .= "       -- Join com Empenho                                                                               \n";
    $stSql .= "       AND EPED.exercicio       = EE.exercicio                                                           \n";
    $stSql .= "       AND EPED.cod_pre_empenho = EE.cod_pre_empenho                                                     \n";
    $stSql .= "       -- filtros                                                                                        \n";
    $stSql .= "       AND (  CPC.cod_estrutural like '3.3%'                                                             \n";
    $stSql .= "           OR CPC.cod_estrutural like '3.4%' )                                                           \n";
    $stSql .= "       AND CPC.exercicio = '".$this->getDado( 'exercicio' )."'                                           \n";
    $stSql .= "       AND OD.cod_entidade IN( ".$this->getDado( 'cod_entidade' )." )                                    \n";
    $stSql .= "       AND to_date( to_char( EAI.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy' ) >= TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy' ) \n";
    $stSql .= "       AND to_date( to_char( EAI.timestamp, 'dd/mm/yyyy'), 'dd/mm/yyyy' ) <= TO_DATE('".$this->getDado('dt_final'  )."','dd/mm/yyyy' ) \n";
    $stSql .= "     GROUP BY CPC.exercicio                                                                              \n";
    $stSql .= "             ,CPC.cod_estrutural                                                                         \n";
    $stSql .= "     ORDER BY CPC.exercicio                                                                              \n";
    $stSql .= "             ,CPC.cod_estrutural                                                                         \n";
    $stSql .= ") as tbl                                                                                                 \n";
    $stSql .= ",contabilidade.plano_conta AS CPC                                                                        \n";
    $stSql .= "WHERE tbl.exercicio      = CPC.exercicio                                                                 \n";
    $stSql .= "  AND tbl.cod_estrutural = substr( CPC.cod_estrutural,1,5 )                                              \n";
    $stSql .= "  AND publico.fn_nivel( CPC.cod_estrutural ) <= 3                                                        \n";
    $stSql .= " GROUP BY tbl.cod_estrutural                                                                             \n";
    $stSql .= "        ,CPC.nom_conta                                                                                   \n";
    $stSql .= " ORDER BY tbl.cod_estrutural                                                                             \n";
    $stSql .= ")                                                                                                        \n";
    $stSql .= ") as tbl GROUP BY                                                                                        \n";
    $stSql .= "    cod_estrutural,                                                                                      \n";
    $stSql .= "    nom_conta                                                                                            \n";

    return $stSql;
}

}
