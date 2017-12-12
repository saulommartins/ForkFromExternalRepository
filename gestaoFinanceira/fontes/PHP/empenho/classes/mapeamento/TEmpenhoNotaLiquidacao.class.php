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
    * Classe de mapeamento da tabela EMPENHO.NOTA_LIQUIDACAO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoNotaLiquidacao.class.php 66543 2016-09-16 18:20:53Z franver $

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-04-02 15:15:10 -0300 (Qua, 02 Abr 2008) $

    * Casos de uso: uc-02.03.03, uc-02.03.04, uc-02.03.16
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.NOTA_LIQUIDACAO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoNotaLiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoNotaLiquidacao()
{
    parent::Persistente();
    $this->setTabela('empenho.nota_liquidacao');

    $this->setCampoCod('cod_nota');
    $this->setComplementoChave('cod_entidade,exercicio');

    $this->AddCampo('exercicio','char',true,'04',false,true);
    $this->AddCampo('cod_nota','integer',true,'',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',true,false);
    $this->AddCampo('exercicio_empenho','char',true,'04',false,true);
    $this->AddCampo('cod_empenho','integer',true,'',true,true);
    $this->AddCampo('dt_vencimento','date',true,'',false,false);
    $this->AddCampo('dt_liquidacao','date',true,'',false,false);
    $this->AddCampo('observacao','varchar',true,'160',false,false);
    $this->AddCampo('hora','time',false,'',false,false);

}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                  \n";
    $stSql .= "    *,                                                  \n";
    $stSql .= "    publico.fn_numeric_br( (VL_ITENS - VL_ITENS_ANULADOS) ) AS VL_NOTA \n";
    $stSql .= "FROM (                                                  \n";
    $stSql .= "    SELECT                                              \n";
    $stSql .= "        ENL.COD_NOTA,                                  \n";
    $stSql .= "        ENL.COD_ENTIDADE,                              \n";
    $stSql .= "        ENL.EXERCICIO,                                  \n";
    $stSql .= "        TO_CHAR ( ENL.DT_LIQUIDACAO,'dd/mm/yyyy') AS dt_liquidacao,                               \n";
    $stSql .= "        CGME.NOM_CGM AS ENTIDADE,                       \n";
    $stSql .= "        ENL.COD_EMPENHO,                                \n";
    $stSql .= "        ENL.EXERCICIO_EMPENHO,                          \n";
    $stSql .= "        ENL.EXERCICIO AS EXERCICIO_NOTA,                \n";
    $stSql .= "        empenho.fn_consultar_valor_liquidado_nota(ENL.EXERCICIO,ENL.COD_EMPENHO,ENL.COD_ENTIDADE,ENL.COD_NOTA) AS VL_ITENS, \n";
    $stSql .= "        empenho.fn_consultar_valor_liquidado_anulado_nota(ENL.EXERCICIO,ENL.COD_EMPENHO,ENL.COD_ENTIDADE,ENL.COD_NOTA) AS VL_ITENS_ANULADOS, \n";
    $stSql .= "        TO_CHAR ( EE.DT_EMPENHO,'dd/mm/yyyy') AS DT_EMPENHO,    \n";
    $stSql .= "        EPE.CGM_BENEFICIARIO,                           \n";
    $stSql .= "        CGM.NOM_CGM AS BENEFICIARIO                     \n";
    $stSql .= "    FROM                                                \n";
    $stSql .= "      empenho.nota_liquidacao AS ENL                \n";
    $stSql .= "    LEFT JOIN                                           \n";
    $stSql .= "      empenho.empenho AS EE                         \n";
    $stSql .= "    ON                                                  \n";
    $stSql .= "        EE.COD_EMPENHO  = ENL.COD_EMPENHO AND           \n";
    $stSql .= "        EE.EXERCICIO    = ENL.EXERCICIO AND             \n";
    $stSql .= "        EE.COD_ENTIDADE = ENL.COD_ENTIDADE              \n";
    $stSql .= "    LEFT JOIN                                           \n";
    $stSql .= "      empenho.pre_empenho AS EPE                    \n";
    $stSql .= "    ON                                                  \n";
    $stSql .= "        EPE.EXERCICIO       = EE.EXERCICIO AND          \n";
    $stSql .= "        EPE.COD_PRE_EMPENHO = EE.COD_PRE_EMPENHO        \n";
    $stSql .= "    LEFT JOIN                                           \n";
    $stSql .= "      sw_cgm AS CGM                                 \n";
    $stSql .= "    ON                                                  \n";
    $stSql .= "        CGM.NUMCGM = EPE.CGM_BENEFICIARIO               \n";
    $stSql .= "    LEFT JOIN                                           \n";
    $stSql .= "      orcamento.entidade AS OE                      \n";
    $stSql .= "    ON                                                  \n";
    $stSql .= "        OE.COD_ENTIDADE = ENL.COD_ENTIDADE              \n";
    $stSql .= "    LEFT JOIN                                           \n";
    $stSql .= "      sw_cgm AS CGME                                \n";
    $stSql .= "    ON                                                  \n";
    $stSql .= "        CGME.NUMCGM = OE.NUMCGM                         \n";
    $stSql .= "    ) AS NOTA_LIQUIDACAO                                \n";

    return $stSql;
}


function recuperaLiquidacaoEsfinge(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( trim($stOrdem) )
        $stOrdem = ( strpos($stOrdem,"ORDER BY") === false ) ? " ORDER BY $stOrdem" : $stOrdem;
    $stSql = $this->montaRecuperaLiquidacaoEsfinge().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLiquidacaoEsfinge() {
    $stSql = "  SELECT  nota_liquidacao.cod_entidade
                      , nota_liquidacao.cod_empenho  
                      , nota_liquidacao.dt_liquidacao
                      , publico.fn_numeric_br((vl_itens - vl_itens_anulados)) AS vl_total
                  FROM (                                                  
                      SELECT
                                nota_liquidacao.cod_nota,                                  
                                nota_liquidacao.cod_entidade,                              
                                nota_liquidacao.exercicio,                                  
                                TO_CHAR ( nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') AS dt_liquidacao,                               
                                sw_cgm.nom_cgm AS entidade,                       
                                nota_liquidacao.cod_empenho,                                
                                nota_liquidacao.exercicio_empenho,                          
                                nota_liquidacao.exercicio AS exercicio_nota,                
                                empenho.fn_consultar_valor_liquidado_nota(nota_liquidacao.exercicio,nota_liquidacao.cod_empenho,nota_liquidacao.cod_entidade,nota_liquidacao.cod_nota) AS vl_itens, 
                                empenho.fn_consultar_valor_liquidado_anulado_nota(nota_liquidacao.exercicio,nota_liquidacao.cod_empenho,nota_liquidacao.cod_entidade,nota_liquidacao.cod_nota) AS vl_itens_anulados, 
                                TO_CHAR ( empenho.dt_empenho,'dd/mm/yyyy') AS dt_empenho,    
                                pre_empenho.cgm_beneficiario,                           
                                sw_cgm.nom_cgm AS beneficiario
                                
                           FROM empenho.nota_liquidacao      
                      LEFT JOIN empenho.empenho
                             ON empenho.cod_empenho = nota_liquidacao.cod_empenho
                            AND empenho.exercicio = nota_liquidacao.exercicio
                            AND empenho.cod_entidade = nota_liquidacao.cod_entidade              
                      LEFT JOIN empenho.pre_empenho
                             ON pre_empenho.exercicio = empenho.exercicio
                            AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho        
                      LEFT JOIN sw_cgm 
                             ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario           
                      LEFT JOIN orcamento.entidade                      
                             ON entidade.cod_entidade = nota_liquidacao.cod_entidade              
                      LEFT JOIN sw_cgm AS sw_cgm_entidade                            
                             ON sw_cgm_entidade.numcgm = entidade.numcgm          
                      ) AS nota_liquidacao 
                  
                      WHERE TO_DATE(dt_liquidacao, 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado("dt_inicial")."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado("dt_final")."', 'dd/mm/yyyy')
                        AND cod_entidade IN (".$this->getDado("cod_entidade").")
                        AND exercicio = '".$this->getDado("exercicio")."'
                   GROUP BY 
                            cod_entidade
                          , cod_empenho
                          , dt_liquidacao
                          , vl_total";
            
    return $stSql;
}

function recuperaValoresAnular(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( trim($stOrdem) )
        $stOrdem = ( strpos($stOrdem,"ORDER BY") === false ) ? " ORDER BY $stOrdem" : $stOrdem;
    $stSql = $this->montaRecuperaValoresAnular().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValoresAnular()
{
    $stSql = " SELECT   *,
                        TRUNC( ( total_liquidado - valor_pago ) * ( liquidado / total_liquidado ), 2 ) as total_a_anular
                FROM (
                        SELECT
                                ( ipe.vl_total -
                                empenho.fn_consultar_valor_empenhado_anulado_item(  e.exercicio,
                                                                                    e.cod_empenho,
                                                                                    e.cod_entidade ,
                                                                                    ipe.num_item)
                                ) as empenhado,
                                ( nli.vl_total -
                                empenho.fn_consultar_valor_liquidado_anulado_nota_item( e.exercicio,
                                                                                        e.cod_empenho,
                                                                                        e.cod_entidade,
                                                                                        nl.cod_nota ,
                                                                                        ipe.num_item)
                                ) as liquidado,
                                (empenho.fn_consultar_valor_liquidado_nota( nl.exercicio,
                                                                            e.cod_empenho,
                                                                            e.cod_entidade,
                                                                            nl.cod_nota)
                                -
                                empenho.fn_consultar_valor_liquidado_anulado_nota(  nl.exercicio,
                                                                                    e.cod_empenho,
                                                                                    e.cod_entidade,
                                                                                    nl.cod_nota)
                                ) as total_liquidado,
                                (empenho.fn_consultar_valor_pagamento_nota_nao_anulada( nl.exercicio,
                                                                                        nl.cod_nota,
                                                                                        nl.cod_entidade)
                                -
                                empenho.fn_consultar_valor_pagamento_anulado_nota(  nl.exercicio,
                                                                                    nl.cod_nota,
                                                                                    nl.cod_entidade)
                                ) as valor_pago,
                                e.exercicio,
                                e.cod_empenho,
                                e.cod_entidade,
                                nl.cod_nota,
                                nl.exercicio as exercicio_nota,
                                nli.num_item,
                                ipe.*
                        FROM
                                empenho.empenho as e,
                                empenho.item_pre_empenho as ipe,
                                empenho.nota_liquidacao_item as nli,
                                empenho.nota_liquidacao as nl
                        WHERE
                                e.cod_empenho       = nl.cod_empenho          AND
                                e.exercicio         = nl.exercicio_empenho    AND
                                e.cod_entidade      = nl.cod_entidade         AND
                                ipe.cod_pre_empenho = nli.cod_pre_empenho     AND
                                ipe.exercicio       = nli.exercicio_item      AND
                                ipe.num_item        = nli.num_item            AND
                                nl.exercicio        = nli.exercicio           AND
                                nl.cod_nota         = nli.cod_nota            AND
                                nl.cod_entidade     = nli.cod_entidade
                        AND nl.cod_nota = 2153 AND nl.exercicio = '2012'  AND nl.cod_entidade = 2  ORDER BY  nli.cod_nota, nli.num_item) as tabela ";

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
function recuperaRelacionamentoPorNota(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( trim($stOrdem) )
        $stOrdem = ( strpos($stOrdem,"ORDER BY") === false ) ? " ORDER BY $stOrdem" : $stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPorNota().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoPorNota()
{
    $stSql  = " SELECT                                                                     \n";
    $stSql .= "       tabela.*                                                             \n";
    $stSql .= " FROM (                                                                     \n";
    $stSql .= "      SELECT                                                                \n";
    $stSql .= "            NL.cod_nota,                                                    \n";
    $stSql .= "            AE.cod_autorizacao,                                             \n";
    $stSql .= "            EE.cod_empenho,                                                 \n";
    $stSql .= "            TO_CHAR(EE.dt_vencimento,'dd/mm/yyyy') AS dt_vencimento,        \n";
    $stSql .= "            TO_CHAR(EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,              \n";
    $stSql .= "            PD.cod_despesa,                                                 \n";
    $stSql .= "            PE.descricao,                                                   \n";
    $stSql .= "            PE.exercicio,                                                   \n";
    $stSql .= "            PE.cod_pre_empenho,                                             \n";
    $stSql .= "            AE.cod_entidade,                                                \n";
    $stSql .= "            AR.cod_reserva,                                                 \n";
    $stSql .= "            PD.cod_conta,                                                   \n";
    $stSql .= "            C.nom_cgm AS nom_fornecedor,                                    \n";
    $stSql .= "            R.vl_reserva,                                                   \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado(                        \n";
    $stSql .= "                                                   PE.exercicio             \n";
    $stSql .= "                                                  ,EE.cod_empenho           \n";
    $stSql .= "                                                  ,AE.cod_entidade          \n";
    $stSql .= "             ) AS vl_empenhado,                                             \n";
    $stSql .= "           empenho.fn_consultar_valor_empenhado_anulado(                \n";
    $stSql .= "                                                           PE.exercicio     \n";
    $stSql .= "                                                          ,EE.cod_empenho   \n";
    $stSql .= "                                                          ,AE.cod_entidade  \n";
    $stSql .= "             ) AS vl_empenhado_anulado,                                     \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado(                        \n";
    $stSql .= "                                                   PE.exercicio             \n";
    $stSql .= "                                                  ,EE.cod_empenho           \n";
    $stSql .= "                                                  ,AE.cod_entidade          \n";
    $stSql .= "             ) AS vl_liquidado,                                             \n";
    $stSql .= "           empenho.fn_consultar_valor_liquidado_anulado(                \n";
    $stSql .= "                                                           PE.exercicio     \n";
    $stSql .= "                                                          ,EE.cod_empenho   \n";
    $stSql .= "                                                          ,AE.cod_entidade  \n";
    $stSql .= "             ) AS vl_liquidado_anulado                                      \n";
    $stSql .= "      FROM                                                                  \n";
    $stSql .= "             empenho.empenho             AS EE,                         \n";
    $stSql .= "             empenho.empenho_autorizacao AS EA,                         \n";
    $stSql .= "             empenho.autorizacao_empenho AS AE,                         \n";
    $stSql .= "             empenho.autorizacao_reserva AS AR,                         \n";
    $stSql .= "             orcamento.reserva           AS  R,                         \n";
    $stSql .= "             sw_cgm                        AS  C,                         \n";
    $stSql .= "             empenho.pre_empenho         AS PE,                         \n";
    $stSql .= "             empenho.pre_empenho_despesa AS PD,                         \n";
    $stSql .= "             orcamento.despesa           AS OD                          \n";
    $stSql .= "             (                                                              \n";
    $stSql .= "              SELECT                                                        \n";
    $stSql .= "                  NL.*                                                      \n";
    $stSql .= "              FROM                                                          \n";
    $stSql .= "                  empenho.nota_liquidacao AS NL                         \n";
    $stSql .= "              LEFT JOIN                                                     \n";
    $stSql .= "                  empenho.pagamento_liquidacao AS PL                        \n";
    $stSql .= "              ON                                                            \n";
    $stSql .= "                  NL.exercicio    = PL.exercicio_liquidacao AND             \n";
    $stSql .= "                  NL.cod_nota     = PL.cod_nota             AND             \n";
    $stSql .= "                  NL.cod_entidade = PL.cod_entidade                         \n";
    $stSql .= "              WHERE                                                         \n";
    $stSql .= "                  PL.COD_ORDEM IS NULL                                      \n";
    $stSql .= "             ) AS NL                                                        \n";
    $stSql .= "     WHERE                                                                  \n";
    $stSql .= "                EE.cod_pre_empenho = PE.cod_pre_empenho                     \n";
    $stSql .= "          AND   EE.exercicio       = PE.exercicio                           \n";
    $stSql .= "          AND   EE.cod_empenho     = EE.cod_empenho                         \n";
    $stSql .= "          AND   EA.exercicio       = EE.exercicio                           \n";
    $stSql .= "          AND   EA.cod_entidade    = EE.cod_entidade                        \n";
    $stSql .= "          AND   EA.cod_empenho     = EE.cod_empenho                         \n";
    $stSql .= "          AND   AE.exercicio       = EA.exercicio                           \n";
    $stSql .= "          AND   AE.cod_autorizacao = EA.cod_autorizacao                     \n";
    $stSql .= "          AND   AE.cod_entidade    = EA.cod_entidade                        \n";
    $stSql .= "          AND   AR.exercicio       = AE.exercicio                           \n";
    $stSql .= "          AND   AR.cod_entidade    = AE.cod_entidade                        \n";
    $stSql .= "          AND   AR.cod_autorizacao = AE.cod_autorizacao                     \n";
    $stSql .= "          AND    C.numcgm          = PE.cgm_beneficiario                    \n";
    $stSql .= "          AND    R.cod_reserva     = AR.cod_reserva                         \n";
    $stSql .= "          AND    R.exercicio       = AR.exercicio                           \n";
    $stSql .= "          AND   PD.cod_pre_empenho = PE.cod_pre_empenho                     \n";
    $stSql .= "          AND   PD.exercicio       = PE.exercicio                           \n";
    $stSql .= "          AND   OD.exercicio       = PD.exercicio                           \n";
    $stSql .= "          AND   OD.cod_despesa     = PD.cod_despesa                         \n";
    $stSql .= "                                                                            \n";
    $stSql .= "          AND   EE.cod_empenho     = NL.cod_empenho                         \n";
    $stSql .= "          AND   EE.exercicio       = NL.exercicio_empenho                   \n";
    $stSql .= "          AND   EE.cod_entidade    = NL.cod_entidade                        \n";
    $stSql .= "                                                                            \n";
    $stSql .= "          AND   OD.num_unidade||OD.num_orgao IN (                           \n";
    $stSql .= "                          SELECT                                            \n";
    $stSql .= "                                num_unidade||num_orgao                      \n";
    $stSql .= "                          FROM                                              \n";
    $stSql .= "                              empenho.permissao_autorizacao             \n";
    $stSql .= "                          WHERE numcgm    = ".$this->getDado("numcgm")."    \n";
    $stSql .= "                          AND   exercicio = '".$this->getDado("exercicio")."'\n";
    $stSql .= "                                                 )                          \n";
    $stSql .= " ) AS tabela                                                                \n";

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
function recuperaNotaLiquidacaoEmpenho(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotaLiquidacaoEmpenho().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaNotaLiquidacaoEmpenho()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "     ( it.vl_total - empenho.fn_consultar_valor_empenhado_anulado_item( em.exercicio, em.cod_empenho, em.cod_entidade , it.num_item) ) as empenhado \n";
    $stSql .= "    ,( li.vl_total - empenho.fn_consultar_valor_liquidado_anulado_nota_item( nl.exercicio, em.cod_empenho, em.cod_entidade, nl.cod_nota , it.num_item)  ) as liquidado \n";
    $stSql .= "    ,re.cod_recurso                                                      \n";
    $stSql .= "    ,re.nom_recurso                                                      \n";
    $stSql .= "    ,pao.num_pao                                                         \n";
    $stSql .= "    ,pao.nom_pao                                                         \n";
    $stSql .= "    ,ppa.acao.num_acao                                                   \n";
    $stSql .= "    ,pe.descricao                                                        \n";
    $stSql .= "    ,nl.cod_nota                                                         \n";
    $stSql .= "    ,li.num_item                                                         \n";
    $stSql .= "    ,it.num_item                                                         \n";
    $stSql .= "    ,it.nom_item                                                         \n";
    $stSql .= "    ,it.complemento                                                      \n";
    $stSql .= "    ,nl.cod_entidade                                                     \n";
    $stSql .= "    ,cgme.nom_cgm as nom_entidade                                        \n";
    $stSql .= "    ,to_char(em.dt_vencimento,'dd/mm/yyyy') as dt_vencimento             \n";
    $stSql .= "    ,to_char(em.dt_empenho,'dd/mm/yyyy') as dt_empenho                   \n";
    $stSql .= "    ,lpad(nl.cod_empenho::text,6,'0'::text) as cod_empenho                           \n";
    $stSql .= "    ,to_char(nl.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao             \n";
    $stSql .= "    ,nl.observacao                                                       \n";
    $stSql .= "    ,nl.exercicio_empenho                                                \n";
    $stSql .= "    ,nl.exercicio as exercicio_nota                                      \n";
    $stSql .= "    ,to_char(nl.dt_vencimento,'dd/mm/yyyy') as dt_vencimento_liquidacao  \n";
    $stSql .= "    ,cg.numcgm                                                           \n";
    $stSql .= "    ,cg.nom_cgm                                                          \n";
    $stSql .= "    ,oo.num_orgao   ||' - '|| oo.nom_orgao AS num_nom_orgao              \n";
    $stSql .= "    ,ou.num_unidade ||' - '|| ou.nom_unidade AS num_nom_unidade          \n";
    $stSql .= "         ,   de.num_orgao                                                \n";
    $stSql .= "    ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                         \n";
    $stSql .= "          ELSE pj.cnpj                                                   \n";
    $stSql .= "     END as cpf_cnpj                                                     \n";
    $stSql .= "    ,CASE WHEN pf.numcgm IS NOT NULL THEN 'CPF'                          \n";
    $stSql .= "          ELSE 'CNPJ'                                                    \n";
    $stSql .= "     END as cpfcnpj                                                      \n";
    $stSql .= "         ,cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco                                    \n";
    $stSql .= "         ,mu.nom_municipio                                               \n";
    $stSql .= "         ,uf.nom_uf                                                      \n";
    $stSql .= "         ,uf.sigla_uf                                                    \n";
    $stSql .= "         ,empenho.fn_consultar_valor_liquidado_item(                     \n";
    $stSql .= "                                                   pe.exercicio          \n";
    $stSql .= "                                                  ,em.cod_empenho        \n";
    $stSql .= "                                                  ,em.cod_entidade       \n";
    $stSql .= "                                                  ,li.num_item           \n";
    $stSql .= "             ) AS vl_liquidado                                           \n";
    $stSql .= "    ,CASE WHEN estornos.num_item IS NOT NULL THEN 't'                    \n";
    $stSql .= "          ELSE 'f'                                                       \n";
    $stSql .= "     END as  possui_estornos                                             \n";
    $stSql .= "    ,LPAD(atributo_empenho_valor.valor,4,'0') AS cod_processo
                   ,atributo_empenho_valor_ano.valor AS ano_processo                    \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "     empenho.nota_liquidacao      as nl                                  \n";
    $stSql .= "    ,empenho.nota_liquidacao_item as li                                  \n";
    $stSql .= "    LEFT JOIN ( SELECT                                                   \n";
    $stSql .= "            E.exercicio,                                                 \n";
    $stSql .= "            E.cod_empenho,                                               \n";
    $stSql .= "            LI.cod_pre_empenho,                                          \n";
    $stSql .= "            E.cod_entidade,                                              \n";
    $stSql .= "            NL.cod_nota,                                                 \n";
    $stSql .= "            LI.num_item,                                                 \n";
    $stSql .= "            cast(to_char(NL.dt_liquidacao,'dd/mm/yyyy') as text),        \n";
    $stSql .= "            LI.vl_total,                                                 \n";
    $stSql .= "            IA.vl_anulado,                                               \n";
    $stSql .= "            cast(to_char(IA.timestamp,'dd/mm/yyyy') as text)             \n";
    $stSql .= "        FROM     empenho.empenho                       AS  E             \n";
    $stSql .= "                ,empenho.nota_liquidacao               AS NL             \n";
    $stSql .= "                ,empenho.nota_liquidacao_item          AS LI             \n";
    $stSql .= "                ,empenho.nota_liquidacao_item_anulado  AS IA             \n";
    $stSql .= "        WHERE                                                            \n";
    $stSql .= "                NL.exercicio_empenho = E.exercicio                       \n";
    $stSql .= "        AND     NL.cod_empenho       = E.cod_empenho                     \n";
    $stSql .= "        AND     NL.cod_entidade      = E.cod_entidade                    \n";
    $stSql .= "        AND     LI.exercicio         = NL.exercicio                      \n";
    $stSql .= "        AND     LI.cod_nota          = NL.cod_nota                       \n";
    $stSql .= "        AND     LI.cod_entidade      = NL.cod_entidade                   \n";
    $stSql .= "        AND     IA.cod_entidade      = LI.cod_entidade                   \n";
    $stSql .= "        AND     IA.cod_nota          = LI.cod_nota                       \n";
    $stSql .= "        AND     IA.exercicio         = LI.exercicio                      \n";
    $stSql .= "        AND     IA.num_item          = LI.num_item                       \n";
    $stSql .= "        AND     IA.cod_pre_empenho   = LI.cod_pre_empenho                \n";
    $stSql .= "        AND     IA.exercicio_item    = LI.exercicio_item                 \n";
    $stSql .= "    ) AS estornos  ON (                                                  \n";
    $stSql .= "                estornos.exercicio    = li.exercicio                     \n";
    $stSql .= "        AND     estornos.cod_entidade = li.cod_entidade                  \n";
    $stSql .= "        AND     estornos.cod_pre_empenho = li.cod_pre_empenho            \n";
    $stSql .= "        AND     estornos.num_item     = li.num_item                      \n";
    $stSql .= "        AND     estornos.cod_nota     = li.cod_nota                      \n";
    $stSql .= "    )                                                                    \n";
    $stSql .= "    ,empenho.empenho              as em                                  \n";
    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "      orcamento.entidade AS OE                                           \n";
    $stSql .= "    ON (                                                                 \n";
    $stSql .= "       OE.COD_ENTIDADE = EM.COD_ENTIDADE AND                             \n";
    $stSql .= "       OE.EXERCICIO    = EM.EXERCICIO       )                            \n";
    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "      sw_cgm AS CGME                                                     \n";
    $stSql .= "    ON (                                                                 \n";
    $stSql .= "        CGME.NUMCGM = OE.NUMCGM  )                                       \n";
    $stSql .= "    ,empenho.pre_empenho          as pe                                  \n";
    $stSql .= "     LEFT JOIN 
                            empenho.atributo_empenho_valor 
                      ON (  atributo_empenho_valor.exercicio = pe.exercicio AND
                            atributo_empenho_valor.cod_pre_empenho  = pe.cod_pre_empenho AND
                            atributo_empenho_valor.cod_modulo = 10 AND
                            atributo_empenho_valor.cod_atributo = 120 )  
                     LEFT JOIN 
                            empenho.atributo_empenho_valor AS atributo_empenho_valor_ano
                      ON (  atributo_empenho_valor_ano.exercicio = pe.exercicio AND
                            atributo_empenho_valor_ano.cod_pre_empenho  = pe.cod_pre_empenho AND
                            atributo_empenho_valor_ano.cod_modulo = 10 AND
                            atributo_empenho_valor_ano.cod_atributo = 121 )             \n";
    $stSql .= "         ,empenho.pre_empenho_despesa as pd                              \n";
    $stSql .= "         ,orcamento.despesa          as de                               \n";
    $stSql .= "         LEFT JOIN orcamento.recurso as re ON (                          \n";
    $stSql .= "             re.cod_recurso = de.cod_recurso AND                         \n";
    $stSql .= "             re.exercicio   = de.exercicio                               \n";
    $stSql .= "            )                                                            \n";
    $stSql .= "         LEFT JOIN orcamento.pao as pao ON (                             \n";
    $stSql .= "             de.num_pao     = pao.num_pao    AND                         \n";
    $stSql .= "             de.exercicio   = pao.exercicio                              \n";
    $stSql .= "            )                                                            \n";
    $stSql .= "         JOIN orcamento.pao_ppa_acao AS pao_ppa_acao                     \n";
    $stSql .= "	          ON ( pao_ppa_acao.num_pao = pao.num_pao                       \n";
    $stSql .= "	         AND pao_ppa_acao.exercicio = pao.exercicio                     \n";
    $stSql .= "	           )                                                            \n";
    $stSql .= "	        JOIN ppa.acao                                                   \n";
    $stSql .= "	          ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                \n";
    $stSql .= "         LEFT JOIN orcamento.unidade as ou ON (                          \n";
    $stSql .= "             ou.num_orgao   = de.num_orgao AND                           \n";
    $stSql .= "             ou.num_unidade = de.num_unidade AND                         \n";
    $stSql .= "             ou.exercicio   = de.exercicio                               \n";
    $stSql .= "            )                                                            \n";
    $stSql .= "         LEFT JOIN orcamento.orgao as oo ON (                            \n";
    $stSql .= "             ou.num_orgao   = oo.num_orgao AND                           \n";
    $stSql .= "             ou.exercicio   = oo.exercicio                               \n";
    $stSql .= "            )                                                            \n";
    $stSql .= "    ,empenho.item_pre_empenho     as it                                  \n";
    $stSql .= "    ,sw_cgm                       as cg                                  \n";
    $stSql .= "      LEFT JOIN                                                          \n";
    $stSql .= "      sw_cgm_pessoa_fisica        as pf                                  \n";
    $stSql .= "       ON (cg.numcgm = pf.numcgm)                                        \n";
    $stSql .= "      LEFT JOIN                                                          \n";
    $stSql .= "      sw_cgm_pessoa_juridica     as pj                                   \n";
    $stSql .= "       ON (cg.numcgm = pj.numcgm)                                        \n";
    $stSql .= "    ,sw_municipio                as mu                                   \n";
    $stSql .= "        ,sw_uf                       as uf                               \n";
    $stSql .= "WHERE   nl.cod_empenho          = em.cod_empenho                         \n";
    $stSql .= "AND     nl.exercicio_empenho    = em.exercicio                           \n";
    $stSql .= "AND     nl.cod_entidade         = em.cod_entidade                        \n";
    $stSql .= "AND     em.cod_pre_empenho      = pe.cod_pre_empenho                     \n";
    $stSql .= "AND     em.exercicio            = pe.exercicio                           \n";
    $stSql .= "AND     it.cod_pre_empenho      = li.cod_pre_empenho                     \n";
    $stSql .= "AND     it.exercicio            = li.exercicio_item                      \n";
    $stSql .= "AND     it.num_item             = li.num_item                            \n";
    $stSql .= "AND     nl.exercicio            = li.exercicio                           \n";
    $stSql .= "AND     nl.cod_nota             = li.cod_nota                            \n";
    $stSql .= "AND     nl.cod_entidade         = li.cod_entidade                        \n";
    $stSql .= "AND     pe.cod_pre_empenho      = it.cod_pre_empenho                     \n";
    $stSql .= "AND     pe.exercicio            = it.exercicio                           \n";
    $stSql .= "     --Empenho Despesa                                                   \n";
    $stSql .= "     AND     pd.cod_pre_empenho  = pe.cod_pre_empenho                    \n";
    $stSql .= "     AND     pd.exercicio        = pe.exercicio                          \n";
    $stSql .= "     --Orcamento/Despesa                                                 \n";
    $stSql .= "     AND     pd.cod_despesa      = de.cod_despesa                        \n";
    $stSql .= "     AND     pd.exercicio        = de.exercicio                          \n";
    $stSql .= "AND     pe.cgm_beneficiario     = cg.numcgm                              \n";
    $stSql .= "AND     cg.cod_municipio        = mu.cod_municipio                       \n";
    $stSql .= "AND     cg.cod_uf               = mu.cod_uf                              \n";
    $stSql .= "     AND     mu.cod_uf           = uf.cod_uf                             \n";

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
function recuperaNotaLiquidacaoEmpenhoRestos(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotaLiquidacaoEmpenhoRestos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaNotaLiquidacaoEmpenhoRestos()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "     ( it.vl_total - empenho.fn_consultar_valor_empenhado_anulado_item( em.exercicio, em.cod_empenho, em.cod_entidade , it.num_item) ) as empenhado \n";
    $stSql .= "    ,( li.vl_total - empenho.fn_consultar_valor_liquidado_anulado_nota_item( nl.exercicio, em.cod_empenho, em.cod_entidade, nl.cod_nota , it.num_item)  ) as liquidado \n";
    $stSql .= "    ,ERPE.recurso as cod_recurso                                         \n";
    $stSql .= "    ,recurso.nom_recurso                                                 \n";
    $stSql .= "    ,ERPE.num_pao                                                        \n";
    $stSql .= "    ,pao.nom_pao                                                         \n";
    $stSql .= "    ,ppa.acao.num_acao                                                \n";
    $stSql .= "    ,pe.descricao                                                      \n";
    $stSql .= "    ,nl.cod_nota                                                        \n";
    $stSql .= "    ,li.num_item                                                         \n";
    $stSql .= "    ,it.num_item                                                         \n";
    $stSql .= "    ,it.nom_item                                                         \n";
    $stSql .= "    ,nl.cod_entidade                                                  \n";
    $stSql .= "    ,cgme.nom_cgm as nom_entidade                       \n";
    $stSql .= "    ,to_char(em.dt_vencimento,'dd/mm/yyyy') as dt_vencimento  \n";
    $stSql .= "    ,to_char(em.dt_empenho,'dd/mm/yyyy') as dt_empenho         \n";
    $stSql .= "    ,lpad(nl.cod_empenho::VARCHAR,6,'0') as cod_empenho                        \n";
    $stSql .= "    ,to_char(nl.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao         \n";
    $stSql .= "    ,nl.observacao                                                       \n";
    $stSql .= "    ,nl.exercicio_empenho                                                \n";
    $stSql .= "    ,nl.exercicio as exercicio_nota                                      \n";
    $stSql .= "    ,to_char(nl.dt_vencimento,'dd/mm/yyyy') as dt_vencimento_liquidacao  \n";
    $stSql .= "    ,cg.numcgm                                                           \n";
    $stSql .= "    ,cg.nom_cgm                                                          \n";
    $stSql .= "    ,CASE WHEN pf.numcgm IS NOT NULL THEN pf.cpf                         \n";
    $stSql .= "          ELSE pj.cnpj                                                   \n";
    $stSql .= "     END as cpf_cnpj                                                     \n";
    $stSql .= "    ,CASE WHEN pf.numcgm IS NOT NULL THEN 'CPF'                          \n";
    $stSql .= "          ELSE 'CNPJ'                                                    \n";
    $stSql .= "     END as cpfcnpj                                                      \n";
    $stSql .= "         ,cg.tipo_logradouro||' '||cg.logradouro||' '||cg.numero||' '||cg.complemento as endereco                                    \n";
    $stSql .= "         ,mu.nom_municipio                                               \n";
    $stSql .= "         ,uf.nom_uf                                                      \n";
    $stSql .= "         ,uf.sigla_uf                                                    \n";
    $stSql .= "         ,empenho.fn_consultar_valor_liquidado_item(                 \n";
    $stSql .= "                                                   pe.exercicio          \n";
    $stSql .= "                                                  ,em.cod_empenho        \n";
    $stSql .= "                                                  ,em.cod_entidade       \n";
    $stSql .= "                                                  ,li.num_item           \n";
    $stSql .= "             ) AS vl_liquidado                                           \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "     empenho.nota_liquidacao      as nl                                  \n";
    $stSql .= "    ,empenho.nota_liquidacao_item as li                                  \n";
    $stSql .= "    ,empenho.empenho              as em                                  \n";

    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "      orcamento.entidade AS OE                                       \n";
    $stSql .= "    ON (                                                                 \n";
    $stSql .= "        OE.EXERCICIO    = EM.EXERCICIO                                   \n";
    $stSql .= "    AND OE.COD_ENTIDADE = EM.COD_ENTIDADE )                              \n";
    $stSql .= "    LEFT JOIN                                                            \n";
    $stSql .= "      sw_cgm AS CGME                                                 \n";
    $stSql .= "    ON (                                                                 \n";
    $stSql .= "        CGME.NUMCGM = OE.NUMCGM  )                                       \n";

    $stSql .= "    ,empenho.pre_empenho          as pe                                  \n";
    $stSql .= "    LEFT JOIN empenho.restos_pre_empenho AS ERPE                         \n";
    $stSql .= "    ON( pe.cod_pre_empenho = ERPE.cod_pre_empenho                        \n";
    $stSql .= "    AND pe.exercicio       = ERPE.exercicio        )                     \n";
    $stSql .= "    LEFT JOIN orcamento.pao as pao ON (                                  \n";
    $stSql .= "         pao.num_pao   = ERPE.num_pao    AND                             \n";
    $stSql .= "         pao.exercicio = '".$this->getDado('exercicio')."' )             \n";
    $stSql .= "    LEFT JOIN orcamento.pao_ppa_acao                                     \n";
    $stSql .= "      ON ( pao_ppa_acao.num_pao = ERPE.num_pao                           \n";
    $stSql .= "     AND pao_ppa_acao.exercicio       = '".$this->getDado('exercicio')."' ) \n";
    $stSql .= "    LEFT JOIN orcamento.recurso                                          \n";
    $stSql .= "      ON ( recurso.cod_recurso = ERPE.recurso                            \n";
    $stSql .= "     AND recurso.exercicio     = ERPE.exercicio        )                 \n";
    $stSql .= "    LEFT JOIN ppa.acao                                                   \n";
    $stSql .= "      ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                       \n";
    $stSql .= "    ,empenho.item_pre_empenho     as it                                  \n";
    $stSql .= "    ,sw_cgm                       as cg                                  \n";
    $stSql .= "      LEFT JOIN                                                          \n";
    $stSql .= "      sw_cgm_pessoa_fisica        as pf                                  \n";
    $stSql .= "       ON (cg.numcgm = pf.numcgm)                                        \n";
    $stSql .= "      LEFT JOIN                                                          \n";
    $stSql .= "      sw_cgm_pessoa_juridica     as pj                                   \n";
    $stSql .= "       ON (cg.numcgm = pj.numcgm)                                        \n";
    $stSql .= "    ,sw_municipio                as mu                                   \n";
    $stSql .= "        ,sw_uf                       as uf                               \n";
    $stSql .= "WHERE   nl.cod_empenho          = em.cod_empenho                         \n";
    $stSql .= "AND     nl.exercicio_empenho    = em.exercicio                           \n";
    $stSql .= "AND     nl.cod_entidade         = em.cod_entidade                        \n";
    $stSql .= "AND     em.cod_pre_empenho      = pe.cod_pre_empenho                     \n";
    $stSql .= "AND     em.exercicio            = pe.exercicio                           \n";

    $stSql .= "AND     it.cod_pre_empenho      = li.cod_pre_empenho                     \n";
    $stSql .= "AND     it.exercicio            = li.exercicio_item                      \n";
    $stSql .= "AND     it.num_item             = li.num_item                            \n";

    $stSql .= "AND     nl.exercicio            = li.exercicio                           \n";
    $stSql .= "AND     nl.cod_nota             = li.cod_nota                            \n";
    $stSql .= "AND     nl.cod_entidade         = li.cod_entidade                        \n";
    $stSql .= "AND     nl.exercicio_empenho    = li.exercicio_item                      \n";

    $stSql .= "AND     pe.cod_pre_empenho      = it.cod_pre_empenho                     \n";
    $stSql .= "AND     pe.exercicio            = it.exercicio                           \n";

    $stSql .= "AND     pe.cgm_beneficiario     = cg.numcgm                              \n";
    $stSql .= "AND     cg.cod_municipio        = mu.cod_municipio                       \n";
    $stSql .= "AND     cg.cod_uf               = mu.cod_uf                              \n";
    $stSql .= "     AND     mu.cod_uf           = uf.cod_uf                             \n";

    return $stSql;
}

function recuperaItensAnulacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaItensAnulacao().$stCondicao.$stOrdem. ") as tabela";
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaItensAnulacao()
{
    $stSql  = " SELECT *,                                                                 \n";
    $stSql .= "     (total_liquidado - valor_pago) as restante_anular,                    \n";
    $stSql .= "     ROUND( ( total_liquidado - valor_pago ) * ( liquidado / total_liquidado ), 2 ) as total_a_anular \n";
    $stSql .= " FROM (                                                                    \n";
    $stSql .= " SELECT                                                                    \n";
    $stSql .= "     ( ipe.vl_total -                                                      \n";
    $stSql .= "       empenho.fn_consultar_valor_empenhado_anulado_item( e.exercicio,     \n";
    $stSql .= "                                                          e.cod_empenho,   \n";
    $stSql .= "                                                          e.cod_entidade , \n";
    $stSql .= "                                                          ipe.num_item)    \n";
    $stSql .= "     ) as empenhado,                                                       \n";
    $stSql .= "     ( nli.vl_total -                                                      \n";
    $stSql .= "     empenho.fn_consultar_valor_liquidado_anulado_nota_item( e.exercicio,  \n";
    $stSql .= "                                                         e.cod_empenho,    \n";
    $stSql .= "                                                         e.cod_entidade,   \n";
    $stSql .= "                                                         nl.cod_nota ,     \n";
    $stSql .= "                                                         ipe.num_item)     \n";
    $stSql .= "     ) as liquidado,                                                       \n";
    $stSql .= "     (empenho.fn_consultar_valor_liquidado_nota(  nl.exercicio,            \n";
    $stSql .= "                                                  e.cod_empenho,           \n";
    $stSql .= "                                                  e.cod_entidade,          \n";
    $stSql .= "                                                  nl.cod_nota) -           \n";
    $stSql .= "     empenho.fn_consultar_valor_liquidado_anulado_nota(                    \n";
    $stSql .= "                                             nl.exercicio,                 \n";
    $stSql .= "                                             e.cod_empenho,                \n";
    $stSql .= "                                             e.cod_entidade,               \n";
    $stSql .= "                                             nl.cod_nota)                  \n";
    $stSql .= "     ) as total_liquidado,                                                 \n";
    $stSql .= "     (empenho.fn_consultar_valor_pagamento_nota_nao_anulada( nl.exercicio  \n";
    $stSql .= "                                                ,nl.cod_nota               \n";
    $stSql .= "                                                ,nl.cod_entidade           \n";
    $stSql .= "     )  -                                                                  \n";
    $stSql .= "     empenho.fn_consultar_valor_pagamento_anulado_nota( nl.exercicio       \n";
    $stSql .= "                                                        ,nl.cod_nota       \n";
    $stSql .= "                                                        ,nl.cod_entidade   \n";
    $stSql .= "     ) ) as valor_pago,                                                    \n";
    $stSql .= "     e.exercicio,                                                          \n";
    $stSql .= "     e.cod_empenho,                                                        \n";
    $stSql .= "     e.cod_entidade,                                                       \n";
    $stSql .= "     nl.cod_nota,                                                          \n";
    $stSql .= "     nl.exercicio as exercicio_nota,                                       \n";
    $stSql .= "     nli.num_item,                                                         \n";
    $stSql .= "     ipe.*                                                                 \n";
    $stSql .= " FROM                                                                      \n";
    $stSql .= "     empenho.empenho as e,                                                 \n";
    $stSql .= "     empenho.item_pre_empenho as ipe,                                      \n";
    $stSql .= "     empenho.nota_liquidacao_item as nli,                                  \n";
    $stSql .= "     empenho.nota_liquidacao as nl                                         \n";
    $stSql .= " where                                                                     \n";
    $stSql .= "     e.cod_empenho       = nl.cod_empenho          AND                     \n";
    $stSql .= "     e.exercicio         = nl.exercicio_empenho    AND                     \n";
    $stSql .= "     e.cod_entidade      = nl.cod_entidade         AND                     \n";
    $stSql .= "     ipe.cod_pre_empenho = nli.cod_pre_empenho AND                         \n";
    $stSql .= "     ipe.exercicio       = nli.exercicio_item      AND                     \n";
    $stSql .= "     ipe.num_item        = nli.num_item            AND                     \n";
    $stSql .= "     nl.exercicio        = nli.exercicio           AND                     \n";
    $stSql .= "     nl.cod_nota         = nli.cod_nota            AND                     \n";
    $stSql .= "     nl.cod_entidade     = nli.cod_entidade                                \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaNotasDisponiveis()
{
    $stSql  = "CREATE TEMPORARY TABLE TEMP_TESTE AS (                                                      \n";
    $stSql .= "    SELECT                                                                                  \n";
    $stSql .= "        ENL.cod_nota,                                                                       \n";
    $stSql .= "        EPE.descricao,                                                                   \n";
    $stSql .= "        ENL.cod_entidade,                                                                   \n";
    $stSql .= "        ENL.exercicio,                                                                      \n";
    $stSql .= "        TO_CHAR ( ENL.dt_liquidacao,'dd/mm/yyyy') AS dt_liquidacao,                         \n";
    $stSql .= "        CGME.numcgm AS entidade,                                                            \n";
    $stSql .= "        ENL.cod_empenho,                                                                    \n";
    $stSql .= "        ENL.exercicio_empenho,                                                              \n";
    $stSql .= "        ENL.exercicio AS exercicio_nota,                                                    \n";
    $stSql .= "        coalesce( OD.cod_recurso, ERPE.recurso ) as cod_recurso,                            \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_nota( ENL.exercicio                          \n";
    $stSql .= "                                                    ,ENL.cod_empenho                        \n";
    $stSql .= "                                                    ,ENL.cod_entidade                       \n";
    $stSql .= "                                                    ,ENL.cod_nota                           \n";
    $stSql .= "        ) AS vl_itens,                                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_anulado_nota( ENL.exercicio                  \n";
    $stSql .= "                                                            ,ENL.cod_empenho                \n";
    $stSql .= "                                                            ,ENL.cod_entidade               \n";
    $stSql .= "                                                            ,ENL.cod_nota                   \n";
    $stSql .= "        ) AS vl_itens_anulados,                                                             \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_nota( ENL.exercicio                             \n";
    $stSql .= "                                                    ,ENL.cod_nota                           \n";
    $stSql .= "                                                    ,ENL.cod_entidade                       \n";
    $stSql .= "        ) AS vl_ordem,                                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_anulado_nota( ENL.exercicio                  \n";
    $stSql .= "                                                            ,ENL.cod_nota                   \n";
    $stSql .= "                                                            ,ENL.cod_entidade               \n";
    $stSql .= "        ) AS vl_ordem_anulada,                                                              \n";
    $stSql .= "        TO_CHAR ( EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,                                \n";
    $stSql .= "        EPE.cgm_beneficiario,                                                               \n";
    $stSql .= "        EPE.implantado,                                                                     \n";
    $stSql .= "        CGM.nom_cgm AS beneficiario                                                         \n";
    $stSql .= "    FROM                                                                                    \n";
    $stSql .= "    empenho.nota_liquidacao AS ENL                                                      \n";
    $stSql .= "    LEFT JOIN                                                                               \n";
    $stSql .= "    orcamento.entidade      AS OE                                                       \n";
    $stSql .= "    ON                                                                                      \n";
    $stSql .= "      ( OE.cod_entidade = ENL.cod_entidade AND                                              \n";
    $stSql .= "        OE.exercicio    = ENL.exercicio       )                                             \n";
    $stSql .= "    LEFT JOIN                                                                               \n";
    $stSql .= "    sw_cgm                     AS CGME                                                     \n";
    $stSql .= "    ON                                                                                      \n";
    $stSql .= "        CGME.numcgm = OE.numcgm                                                             \n";
    $stSql .= "     ,empenho.empenho     AS EE                                                         \n";
    $stSql .= "     ,empenho.pre_empenho AS EPE                                                        \n";
    $stSql .= "     LEFT JOIN empenho.pre_empenho_despesa AS EPD                                       \n";
    $stSql .= "     ON( EPD.cod_pre_empenho = EPE.cod_pre_empenho                                          \n";
    $stSql .= "     AND EPD.exercicio       = EPE.exercicio        )                                       \n";
    $stSql .= "    LEFT JOIN                                                                               \n";
    $stSql .= "    orcamento.despesa AS OD                                                             \n";
    $stSql .= "    ON                                                                                      \n";
    $stSql .= "      ( OD.cod_despesa = EPD.cod_despesa AND                                                \n";
    $stSql .= "        OD.exercicio   = EPD.exercicio   )                                                  \n";
    $stSql .= "    LEFT JOIN empenho.restos_pre_empenho AS ERPE                                        \n";
    $stSql .= "    ON( EPE.cod_pre_empenho = ERPE.cod_pre_empenho                                          \n";
    $stSql .= "    AND EPE.exercicio       = ERPE.exercicio        )                                       \n";
    $stSql .= "    LEFT JOIN                                                                               \n";
    $stSql .= "    sw_cgm  AS CGM                                                                         \n";
    $stSql .= "    ON                                                                                      \n";
    $stSql .= "        CGM.numcgm = EPE.cgm_beneficiario                                                   \n";
    $stSql .= "    WHERE                                                                                   \n";
    $stSql .= "         EE.cod_empenho      = ENL.cod_empenho                                              \n";
    $stSql .= "    AND  EE.exercicio        = ENL.exercicio_empenho                                        \n";
    $stSql .= "    AND  EE.cod_entidade     = ENL.cod_entidade                                             \n";
    $stSql .= "    AND  EPE.exercicio       = EE.exercicio                                                 \n";
    $stSql .= "    AND  EPE.cod_pre_empenho = EE.cod_pre_empenho                                           \n";
    $stSql .= $this->getDado('filtro')                                                                   ."\n";
    $stSql .= ");                                                                                          \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "                                                                                            \n";
    $stSql .= "SELECT                                                                                      \n";
    $stSql .= "    *                                                                                       \n";
    $stSql .= "    ,publico.fn_numeric_br( (TEMP_TESTE.vl_itens - TEMP_TESTE.vl_itens_anulados) ) AS vl_nota   \n";
    $stSql .= "FROM TEMP_TESTE                                                                             \n";
    $stSql .= "WHERE                                                                                       \n";
    $stSql .= "( TEMP_TESTE.vl_itens - TEMP_TESTE.vl_itens_anulados ) > ( TEMP_TESTE.vl_ordem - TEMP_TESTE.vl_ordem_anulada) \n";
    $stSql .= "ORDER BY TEMP_TESTE.cod_empenho                                                               \n";

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
function recuperaRecuperaNotasDisponiveis(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( 'filtro', $stCondicao );

    if(trim($stOrdem))
    $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotasDisponiveis().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaNotasDisponiveisImplantadas()
{
    $stSql  = "CREATE TEMPORARY TABLE TEMP_EMPENHO AS (                                             \n";
    $stSql .= "    SELECT                                                                           \n";
    $stSql .= "        ENL.cod_nota,                                                                \n";
    $stSql .= "        ENL.cod_entidade,                                                            \n";
    $stSql .= "        EPE.descricao,                                                                   \n";
    $stSql .= "        ENL.exercicio,                                                               \n";
    $stSql .= "        TO_CHAR ( ENL.dt_liquidacao,'dd/mm/yyyy') AS dt_liquidacao,                  \n";
    $stSql .= "        CGME.numcgm AS entidade,                                                     \n";
    $stSql .= "        ENL.cod_empenho,                                                             \n";
    $stSql .= "        ENL.exercicio_empenho,                                                       \n";
    $stSql .= "        ENL.exercicio AS exercicio_nota,                                             \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_nota( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_empenho                 \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "        ) AS vl_itens,                                                               \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_anulado_nota( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_empenho         \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "        ) AS vl_itens_anulados,                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_nota( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "        ) AS vl_ordem,                                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_anulado_nota( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "        ) AS vl_ordem_anulada,                                                              \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_pago( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "        ) AS vl_pago,                                                                \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_pago_anulado( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "        ) AS vl_pago_anulado,                                                        \n";
    $stSql .= "        TO_CHAR ( EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,                         \n";
    $stSql .= "        EPE.cgm_beneficiario,                                                        \n";
    $stSql .= "        EPE.implantado,                                                              \n";
    $stSql .= "        CGM.nom_cgm AS beneficiario                                                  \n";
    $stSql .= "    FROM                                                                             \n";
    $stSql .= "    empenho.nota_liquidacao AS ENL                                               \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    orcamento.entidade      AS OE                                                \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "      ( OE.cod_entidade = ENL.cod_entidade AND                                       \n";
    $stSql .= "        OE.exercicio    = ENL.exercicio       )                                      \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    sw_cgm                     AS CGME                                              \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "        CGME.numcgm = OE.numcgm                                                      \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.pagamento_liquidacao AS EPL                                          \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        ENL.exercicio    = EPL.exercicio_liquidacao AND                              \n";
    $stSql .= "        ENL.cod_entidade = EPL.cod_entidade         AND                              \n";
    $stSql .= "        ENL.cod_nota     = EPL.cod_nota                )                             \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.nota_liquidacao_paga as NLP                                          \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        NLP.cod_entidade = ENL.cod_entidade AND                                      \n";
    $stSql .= "        NLP.cod_nota     = ENL.cod_nota     AND                                      \n";
    $stSql .= "        NLP.exercicio    = ENL.exercicio        )                                    \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLP                    \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        EPL.cod_nota             = EPLP.cod_nota              AND                    \n";
    $stSql .= "        EPL.cod_ordem            = EPLP.cod_ordem             AND                    \n";
    $stSql .= "        EPL.exercicio_liquidacao = EPLP.exercicio_liquidacao  AND                    \n";
    $stSql .= "        EPL.cod_entidade         = EPLP.cod_entidade          AND                    \n";
    $stSql .= "        EPL.exercicio            = EPLP.exercicio             AND                    \n";
    $stSql .= "        NLP.cod_nota             = EPLP.cod_nota              AND                    \n";
    $stSql .= "        NLP.exercicio            = EPLP.exercicio_liquidacao  AND                    \n";
    $stSql .= "        NLP.cod_entidade         = EPLP.cod_entidade          AND                    \n";
    $stSql .= "        NLP.timestamp            = EPLP.timestamp                 )                  \n";
    $stSql .= "     ,empenho.empenho          AS EE                                             \n";
    $stSql .= "     ,empenho.pre_empenho      AS EPE                                            \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    sw_cgm AS CGM                                                                   \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "        CGM.numcgm = EPE.cgm_beneficiario                                            \n";
    $stSql .= "     ,empenho.restos_pre_empenho AS RPE                                          \n";
    $stSql .= "    WHERE                                                                            \n";
    $stSql .= "         EE.cod_empenho      = ENL.cod_empenho                                       \n";
    $stSql .= "    AND  EE.exercicio        = ENL.exercicio_empenho                                 \n";
    $stSql .= "    AND  EE.cod_entidade     = ENL.cod_entidade                                      \n";
    $stSql .= "    AND  EPE.exercicio       = EE.exercicio                                          \n";
    $stSql .= "    AND  EPE.cod_pre_empenho = EE.cod_pre_empenho                                    \n";
    $stSql .= "    AND  RPE.cod_pre_empenho = EPE.cod_pre_empenho                                   \n";
    $stSql .= "    AND  RPE.exercicio       = EPE.exercicio                                         \n";
    $stSql .= $this->getDado('filtro')                                                            ."\n";
    $stSql .= ");                                                                                   \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "SELECT                                                                               \n";
    $stSql .= "    *                                                                                \n";
    $stSql .= "    ,publico.fn_numeric_br( (TEMP_EMPENHO.vl_itens - TEMP_EMPENHO.vl_itens_anulados) ) AS vl_nota \n";
    $stSql .= "FROM TEMP_EMPENHO                                                                    \n";
    $stSql .= "WHERE                                                                                \n";
    $stSql .= "( TEMP_EMPENHO.vl_itens - TEMP_EMPENHO.vl_itens_anulados ) > ( TEMP_EMPENHO.vl_ordem - TEMP_EMPENHO.vl_ordem_anulada) \n";
    $stSql .= "GROUP BY                                                                             \n";
    $stSql .= "        cod_nota,                                                                    \n";
    $stSql .= "        cod_entidade,                                                                \n";
    $stSql .= "        exercicio,                                                                   \n";
    $stSql .= "        dt_liquidacao,                                                               \n";
    $stSql .= "        entidade,                                                                    \n";
    $stSql .= "        cod_empenho,                                                                 \n";
    $stSql .= "        exercicio_empenho,                                                           \n";
    $stSql .= "        exercicio_nota,                                                              \n";
    $stSql .= "        vl_itens,                                                                    \n";
    $stSql .= "        vl_itens_anulados,                                                           \n";
    $stSql .= "        vl_ordem,                                                                    \n";
    $stSql .= "        vl_ordem_anulada,                                                            \n";
    $stSql .= "        vl_pago,                                                                     \n";
    $stSql .= "        vl_pago_anulado,                                                             \n";
    $stSql .= "        dt_empenho,                                                                  \n";
    $stSql .= "        cgm_beneficiario,                                                            \n";
    $stSql .= "        implantado,                                                                  \n";
    $stSql .= "        beneficiario,                                                                 \n";
    $stSql .= "        descricao                                                                 \n";
    $stSql .= "ORDER BY  cod_nota ;                                                          \n";
    $stSql .= ";                                                                             \n";

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
function recuperaNotasDisponiveisImplantadas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado('filtro', $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotasDisponiveisImplantadas().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoPagos(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoPagos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoPagos()
{
    $stSql  = " SELECT                                                      \n";
    $stSql .= "    op.cod_ordem    as cod_ordem,                            \n";
    $stSql .= "    op.exercicio    as exercicio,                            \n";
    $stSql .= "    to_char(op.dt_emissao,'dd/mm/yyyy') as dt_emissao,       \n";
    $stSql .= "    to_char(nlp.timestamp,'dd/mm/yyyy') as dt_pagamento,     \n";
    $stSql .= "    coalesce(nlp.vl_pago,0.00)  as vl_pago,                  \n";
    $stSql .= "    coalesce(nlpa.vl_anulado,0.00)  as vl_estornado,         \n";
    $stSql .= "    p.cod_plano    as conta,                                 \n";
    $stSql .= "    p.nom_conta    as nome_conta,                            \n";
    $stSql .= "    CASE WHEN p.nom_conta is null THEN                       \n";
    $stSql .= "        'Inexistente'                                        \n";
    $stSql .= "    ELSE                                                     \n";
    $stSql .= "        p.cod_plano || ' - ' || p.nom_conta                  \n";
    $stSql .= "    END as conta_nome_conta,                                 \n";
    $stSql .= "    coalesce(empenho.fn_consultar_valor_pagar(e.exercicio,e.cod_empenho,e.cod_entidade,nlp.timestamp,0,''),0.00) as vl_pagar       \n";
    $stSql .= "FROM                                                         \n";
    $stSql .= "      empenho.empenho               as e                     \n";
    $stSql .= "    , empenho.nota_liquidacao       as nl                    \n";
    $stSql .= "    , empenho.nota_liquidacao_paga  as nlp                   \n";
    $stSql .= "        LEFT OUTER JOIN empenho.nota_liquidacao_paga_anulada as nlpa ON          \n";
    $stSql .= "                nlp.exercicio = nlpa.exercicio                   \n";
    $stSql .= "            AND nlp.cod_nota = nlpa.cod_nota                     \n";
    $stSql .= "            AND nlp.cod_entidade = nlpa.cod_entidade             \n";
    $stSql .= "            AND nlp.timestamp = nlpa.timestamp                   \n";
    $stSql .= "        LEFT OUTER JOIN (                                        \n";
    $stSql .= "            SELECT                                               \n";
    $stSql .= "                p.cod_entidade,                                  \n";
    $stSql .= "                p.cod_nota,                                      \n";
    $stSql .= "                p.exercicio_liquidacao,                          \n";
    $stSql .= "                p.timestamp,                                     \n";
    $stSql .= "                pa.cod_plano,                                    \n";
    $stSql .= "                pc.nom_conta                                     \n";
    $stSql .= "            FROM                                                 \n";
    $stSql .= "                  contabilidade.pagamento       as p             \n";
    $stSql .= "                , contabilidade.lancamento_empenho as le         \n";
    $stSql .= "                , contabilidade.conta_credito   as cc            \n";
    $stSql .= "                , contabilidade.plano_analitica as pa            \n";
    $stSql .= "                , contabilidade.plano_conta     as pc            \n";
    $stSql .= "            WHERE                                                \n";
    $stSql .= "                    p.cod_lote          = le.cod_lote            \n";
    $stSql .= "                AND p.tipo              = le.tipo                \n";
    $stSql .= "                AND p.sequencia         = le.sequencia           \n";
    $stSql .= "                AND p.exercicio         = le.exercicio           \n";
    $stSql .= "                AND p.cod_entidade      = le.cod_entidade        \n";
    $stSql .= "                AND le.estorno          = false                  \n";
    $stSql .= "                --Ligação LANCAMENTO EMPENHO : CONTA_CREDITO     \n";
    $stSql .= "                AND le.cod_lote         = cc.cod_lote            \n";
    $stSql .= "                AND le.tipo             = cc.tipo                \n";
    $stSql .= "                AND le.exercicio        = cc.exercicio           \n";
    $stSql .= "                AND le.cod_entidade     = cc.cod_entidade        \n";
    $stSql .= "                --Ligação CONTA_CREDITO : PLANO ANALITICA        \n";
    $stSql .= "                AND cc.cod_plano        = pa.cod_plano           \n";
    $stSql .= "                AND cc.exercicio        = pa.exercicio           \n";
    if ($this->getDado('exercicio') == $this->getDado('exercicio_empenho')) {
        if ( Sessao::getExercicio() >= '2016' ) {
            $stSql .= "                AND cc.sequencia        = 2                  \n";
        } else {
            $stSql .= "                AND cc.sequencia        = 3                  \n";
        }
    } else {
        $stSql .= "                AND cc.sequencia        = 2                  \n";
    }

    $stSql .= "                --Ligação PLANO ANALITICA : PLANO CONTA          \n";
    $stSql .= "                AND pa.cod_conta        = pc.cod_conta           \n";
    $stSql .= "                AND pa.exercicio        = pc.exercicio           \n";
    $stSql .= "        ) as p ON (                                              \n";
    $stSql .= "            --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO           \n";
    $stSql .= "                nlp.cod_entidade    = p.cod_entidade             \n";
    $stSql .= "            AND nlp.cod_nota        = p.cod_nota                 \n";
    $stSql .= "            AND nlp.exercicio       = p.exercicio_liquidacao     \n";
    $stSql .= "            AND nlp.timestamp       = p.timestamp                \n";
    $stSql .= "        )                                                        \n";

    $stSql .= "    , empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp \n";
    $stSql .= "    , empenho.pagamento_liquidacao  as pl                        \n";
    $stSql .= "    , empenho.ordem_pagamento       as op                        \n";

    $stSql .= "WHERE                                                            \n";
    $stSql .= "    --Ligação EMPENHO : NOTA LIQUIDAÇÃO                          \n";
    $stSql .= "        e.exercicio         = nl.exercicio_empenho               \n";
    $stSql .= "    AND e.cod_entidade      = nl.cod_entidade                    \n";
    $stSql .= "    AND e.cod_empenho       = nl.cod_empenho                     \n";

    $stSql .= "    --Ligação NOTA LIQUIDAÇÃO : NOTA LIQUIDAÇÃO PAGA             \n";
    $stSql .= "    AND nl.exercicio        = nlp.exercicio                      \n";
    $stSql .= "    AND nl.cod_nota         = nlp.cod_nota                       \n";
    $stSql .= "    AND nl.cod_entidade     = nlp.cod_entidade                   \n";

    $stSql .= "   --Ligação NOTA LIQUIDAÇÃO PAGA : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          \n";
    $stSql .= "    AND nlp.cod_entidade    = plnlp.cod_entidade                 \n";
    $stSql .= "    AND nlp.cod_nota        = plnlp.cod_nota                     \n";
    $stSql .= "    AND nlp.exercicio       = plnlp.exercicio_liquidacao         \n";
    $stSql .= "    AND nlp.timestamp       = plnlp.timestamp                    \n";

    $stSql .= "    --Ligação PAGAMENTO LIQUIDACAO : PAGAMENTO LIQUIDACAO NOTA LIQUIDACAO PAGA          \n";
    $stSql .= "    AND pl.cod_ordem        = plnlp.cod_ordem                    \n";
    $stSql .= "    AND pl.exercicio        = plnlp.exercicio                    \n";
    $stSql .= "    AND pl.cod_entidade     = plnlp.cod_entidade                 \n";
    $stSql .= "    AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao     \n";
    $stSql .= "    AND pl.cod_nota         = plnlp.cod_nota                     \n";

    $stSql .= "    --Ligação PAGAMENTO LIQUIDACAO : ORDEM PAGAMENTO             \n";
    $stSql .= "    AND pl.cod_ordem        = op.cod_ordem                       \n";
    $stSql .= "    AND pl.exercicio        = op.exercicio                       \n";
    $stSql .= "    AND pl.cod_entidade     = op.cod_entidade                    \n";

    return $stSql;
}

function recuperaRelacionamentoLiquidados(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoLiquidados().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLiquidados()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "     nli.cod_nota                            as cod_nota,                    \n";
    $stSql .= "     nli.exercicio                           as exercicio,                   \n";
    $stSql .= "     nli.cod_pre_empenho                     as cod_pre_empenho,             \n";
    $stSql .= "     nli.exercicio_item                      as exercicio_item,              \n";
    $stSql .= "     to_char(nl.dt_liquidacao,'dd/mm/yyyy')  as dt_liquidacao,               \n";
    $stSql .= "     coalesce(sum(nli.vl_total),0.00)        as vl_total,                    \n";
    $stSql .= "     coalesce(sum(nlia.vl_anulado),0.00)     as vl_anulado                   \n";
    $stSql .= " FROM                                                                        \n";
    $stSql .= "     empenho.empenho              as e,                                      \n";
    $stSql .= "     empenho.nota_liquidacao      as nl,                                     \n";
    $stSql .= "     empenho.nota_liquidacao_item as nli                                     \n";
    $stSql .= "     left join (                                                             \n";
    $stSql .= "                 select  cod_entidade                                        \n";
    $stSql .= "                        ,cod_nota                                            \n";
    $stSql .= "                        ,exercicio                                           \n";
    $stSql .= "                        ,cod_pre_empenho                                     \n";
    $stSql .= "                        ,num_item                                            \n";
    $stSql .= "                        ,exercicio_item                                      \n";
    $stSql .= "                        ,sum(vl_anulado) as vl_anulado                       \n";
    $stSql .= "                 from empenho.nota_liquidacao_item_anulado                   \n";
    $stSql .= "                 group by  cod_entidade                                      \n";
    $stSql .= "                          ,cod_nota                                          \n";
    $stSql .= "                          ,exercicio                                         \n";
    $stSql .= "                          ,cod_pre_empenho                                   \n";
    $stSql .= "                          ,num_item                                          \n";
    $stSql .= "                          ,exercicio_item                                    \n";
    $stSql .= "               ) as nlia ON (                                                \n";
    $stSql .= "                                  nli.exercicio       = nlia.exercicio       \n";
    $stSql .= "                              AND nli.cod_nota        = nlia.cod_nota        \n";
    $stSql .= "                              AND nli.cod_entidade    = nlia.cod_entidade    \n";
    $stSql .= "                              AND nli.num_item        = nlia.num_item        \n";
    $stSql .= "                              AND nli.cod_pre_empenho = nlia.cod_pre_empenho \n";
    $stSql .= "                              AND nli.exercicio_item  = nlia.exercicio_item  \n";
    $stSql .= "                            )                                                \n";
    $stSql .= " WHERE                                                                       \n";
    $stSql .= "     -- empenho -> nota_liquidacao                                           \n";
    $stSql .= "         e.exercicio     = nl.exercicio_empenho                              \n";
    $stSql .= "     AND e.cod_entidade  = nl.cod_entidade                                   \n";
    $stSql .= "     AND e.cod_empenho   = nl.cod_empenho                                    \n";
    $stSql .= "     -- nota_liquidacao -> nota_liquidacao_item                              \n";
    $stSql .= "     AND nl.exercicio    = nli.exercicio                                     \n";
    $stSql .= "     AND nl.cod_nota     = nli.cod_nota                                      \n";
    $stSql .= "     AND nl.cod_entidade = nli.cod_entidade                                  \n";

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
function recuperaRelacionamentoManutencaoDatas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoManutencaoDatas().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoManutencaoDatas()
{
    $stSql  = " SELECT                                                                \n";
    $stSql .= "    nl.exercicio,                                                      \n";
    $stSql .= "    nl.cod_nota,                                                       \n";
    $stSql .= "    sum(nli.vl_total) as vl_total,                                     \n";
    $stSql .= "    to_char(nl.dt_liquidacao,'dd/mm/yyyy') as dt_liquidacao            \n";
    $stSql .= " FROM                                                                  \n";
    $stSql .= "    empenho.empenho               e,                               \n";
    $stSql .= "    empenho.nota_liquidacao      nl,                               \n";
    $stSql .= "    empenho.nota_liquidacao_item nli                               \n";
    $stSql .= " WHERE                                                                 \n";
    $stSql .= "    e.cod_empenho       = nl.cod_empenho        AND                    \n";
    $stSql .= "    e.cod_entidade      = nl.cod_entidade       AND                    \n";
    $stSql .= "    e.exercicio         = nl.exercicio_empenho  AND                    \n";
    $stSql .= "                                                                       \n";
    $stSql .= "    nl.exercicio        = nli.exercicio         AND                    \n";
    $stSql .= "    nl.cod_nota         = nli.cod_nota          AND                    \n";
    $stSql .= "    nl.cod_entidade     = nli.cod_entidade      AND                    \n";
    $stSql .= "                                                                       \n";
    $stSql .= "    e.cod_empenho       = ".$this->getDado('cod_empenho')."     AND  \n";
    $stSql .= "    e.cod_entidade      = ".$this->getDado('cod_entidade')."    AND  \n";
    $stSql .= "    e.exercicio         = '".$this->getDado('exercicio')."'            \n";
    $stSql .= " GROUP BY                                                              \n";
    $stSql .= "    nl.exercicio,                                                      \n";
    $stSql .= "    nl.cod_nota,                                                       \n";
    $stSql .= "    nl.dt_liquidacao                                                   \n";

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
function recuperaNotaLiquidacaoAnulada(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNotaLiquidacaoAnulada();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNotaLiquidacaoAnulada()
{
    $stSql  = "SELECT tbl.cod_empenho                                                                                               \n";
    $stSql .= "      ,tbl.exercicio_empenho                                                                                         \n";
    $stSql .= "      ,tbl.dt_vencimento_liquidacao                                                                                                \n";
    $stSql .= "      ,tbl.dt_empenho                                                                                                \n";
    $stSql .= "      ,tbl.dt_vencimento                                                                                             \n";
    $stSql .= "      ,tbl.descricao                                                                                                 \n";
    $stSql .= "      ,tbl.observacao                                                                                                \n";
    $stSql .= "      ,tbl.cgm_beneficiario AS numcgm                                                                                \n";
    $stSql .= "      ,CGM.nom_cgm                                                                                                   \n";
    $stSql .= "      ,MUN.nom_municipio                                                                                             \n";
    $stSql .= "      ,UF.sigla_uf                                                                                                   \n";
    $stSql .= "      ,CGM.tipo_logradouro||' '||CGM.logradouro||', '||CGM.numero AS endereco                                        \n";
    $stSql .= "      ,coalesce( PJ.cnpj, PF.cpf ) AS cpf_cnpj                                                                       \n";
    $stSql .= "      ,CGM.fone_residencial  AS fone                                                                                 \n";
    $stSql .= "      ,tbl.exercicio                                                                                                 \n";
    $stSql .= "      ,tbl.cod_entidade                                                                                              \n";
    $stSql .= "      ,CGME.nom_cgm as nom_entidade                                                                                  \n";
    $stSql .= "      ,tbl.cod_nota                                                                                                  \n";
    $stSql .= "      ,tbl.dt_anulacao                                                                                               \n";
    $stSql .= "      ,tbl.num_item                                                                                                  \n";
    $stSql .= "      ,tbl.nom_item                                                                                                  \n";
    $stSql .= "      ,sum( coalesce( tbl.vl_anulado         , 0.00 ) ) AS vl_anulado                                                \n";
    $stSql .= "      ,( coalesce( tbl.vl_total, 0.00 ) - sum( coalesce( tbl.vl_anulado_anterior, 0.00 ) ) ) AS liquidado            \n";
    $stSql .= "      ,tbl.vl_empenhado - tbl.vl_empenho_anulado as empenhado                                                        \n";
    $stSql .= "FROM(                                                                                                                \n";
    $stSql .= "     SELECT ENL.exercicio_empenho                                                                                    \n";
    $stSql .= "           ,ENL.cod_empenho                                                                                          \n";
    $stSql .= "           ,TO_CHAR( ENL.dt_vencimento, 'dd/mm/yyyy' ) as dt_vencimento_liquidacao                                               \n";
    $stSql .= "           ,TO_CHAR( EE.dt_empenho   , 'dd/mm/yyyy' ) as dt_empenho                                                  \n";
    $stSql .= "           ,TO_CHAR( EE.dt_vencimento, 'dd/mm/yyyy' ) as dt_vencimento                                               \n";
    $stSql .= "           ,EPE.descricao                                                                                            \n";
    $stSql .= "           ,EPE.cgm_beneficiario                                                                                     \n";
    $stSql .= "           ,ENL.observacao                                                                                           \n";
    $stSql .= "           ,ENLIA.exercicio                                                                                          \n";
    $stSql .= "           ,ENLIA.cod_entidade                                                                                       \n";
    $stSql .= "           ,ENLIA.cod_nota                                                                                           \n";
    $stSql .= "           ,TO_CHAR( ENLIA.timestamp, 'dd/mm/yyyy' ) as dt_anulacao                                                  \n";
    $stSql .= "           ,ENLI.num_item                                                                                            \n";
    $stSql .= "           ,EIPE.nom_item                                                                                            \n";
    $stSql .= "           ,CASE WHEN ENLIA.timestamp = '".$this->getDado( 'timestamp' )."'                                          \n";
    $stSql .= "               THEN sum( ENLIA.vl_anulado )                                                                          \n";
    $stSql .= "            END AS vl_anulado                                                                                        \n";
    $stSql .= "           ,CASE WHEN ENLIA.timestamp < '".$this->getDado( 'timestamp' )."'                                          \n";
    $stSql .= "               THEN sum( ENLIA.vl_anulado )                                                                          \n";
    $stSql .= "           END AS vl_anulado_anterior                                                                                \n";
    $stSql .= "           ,ENLI.vl_total                                                                                            \n";
    $stSql .= "           ,EIPE.vl_empenhado                                                                                        \n";
    $stSql .= "           ,coalesce( EEAI.vl_anulado, 0.00 ) as vl_empenho_anulado                                                  \n";
    $stSql .= "     FROM empenho.nota_liquidacao              AS ENL                                                                \n";
    $stSql .= "       --Ligação nota_liquidacao : nota_liquidacao_item                                                              \n";
    $stSql .= "         LEFT JOIN empenho.nota_liquidacao_item AS ENLI                                                              \n";
    $stSql .= "         ON( ENL.exercicio        = ENLI.exercicio                                                                   \n";
    $stSql .= "         AND ENL.cod_entidade     = ENLI.cod_entidade                                                                \n";
    $stSql .= "         AND ENL.cod_nota         = ENLI.cod_nota       )                                                            \n";
    $stSql .= "       --Ligação nota_liquidacao_item : nota_liquidacao_item_anulado                                                 \n";
    $stSql .= "         LEFT JOIN empenho.nota_liquidacao_item_anulado AS ENLIA                                                     \n";
    $stSql .= "         ON( ENLI.exercicio       = ENLIA.exercicio                                                                  \n";
    $stSql .= "         AND ENLI.cod_entidade    = ENLIA.cod_entidade                                                               \n";
    $stSql .= "         AND ENLI.cod_nota        = ENLIA.cod_nota                                                                   \n";
    $stSql .= "         AND ENLI.cod_pre_empenho = ENLIA.cod_pre_empenho                                                            \n";
    $stSql .= "         AND ENLI.num_item        = ENLIA.num_item                                                                   \n";
    $stSql .= "         AND ENLI.exercicio_item  = ENLIA.exercicio_item  )                                                          \n";
    $stSql .= "       --Ligação nota_liquidacao : empenho                                                                           \n";
    $stSql .= "         LEFT JOIN empenho.empenho AS EE                                                                             \n";
    $stSql .= "         ON( ENL.cod_empenho       = EE.cod_empenho                                                                  \n";
    $stSql .= "         AND ENL.exercicio_empenho = EE.exercicio                                                                    \n";
    $stSql .= "         AND ENL.cod_entidade      = EE.cod_entidade  )                                                              \n";
    $stSql .= "       --Ligação empenho : pre_empenho                                                                               \n";
    $stSql .= "         LEFT JOIN empenho.pre_empenho AS EPE                                                                        \n";
    $stSql .= "         ON( EE.cod_pre_empenho    = EPE.cod_pre_empenho                                                             \n";
    $stSql .= "         AND EE.exercicio          = EPE.exercicio        )                                                          \n";
    $stSql .= "       --Ligação pre_empenho : item_pre_empenho                                                                      \n";
    $stSql .= "         LEFT JOIN ( SELECT exercicio                                                                                \n";
    $stSql .= "                           ,cod_pre_empenho                                                                          \n";
    $stSql .= "                           ,num_item                                                                                 \n";
    $stSql .= "                           ,nom_item                                                                                 \n";
    $stSql .= "                           ,sum( vl_total ) as vl_empenhado                                                          \n";
    $stSql .= "                     FROM empenho.item_pre_empenho                                                                   \n";
    $stSql .= "                     GROUP BY exercicio                                                                              \n";
    $stSql .= "                             ,cod_pre_empenho                                                                        \n";
    $stSql .= "                             ,num_item                                                                               \n";
    $stSql .= "                             ,nom_item                                                                               \n";
    $stSql .= "         ) AS EIPE ON( EPE.cod_pre_empenho = EIPE.cod_pre_empenho                                                    \n";
    $stSql .= "                   AND EPE.exercicio       = EIPE.exercicio                                                          \n";
    $stSql .= "                   AND ENLI.num_item       = EIPE.num_item         )                                                 \n";
    $stSql .= "       --Ligação empenho : empenho_anulado_item                                                                      \n";
    $stSql .= "       LEFT JOIN( SELECT exercicio                                                                                   \n";
    $stSql .= "                        ,cod_empenho                                                                                 \n";
    $stSql .= "                        ,cod_entidade                                                                                \n";
    $stSql .= "                        ,sum( vl_anulado ) AS vl_anulado                                                             \n";
    $stSql .= "                  FROM empenho.empenho_anulado_item AS EEAI                                                          \n";
    $stSql .= "                  WHERE timestamp <= '".$this->getDado( 'timestamp' )."'                                             \n";
    $stSql .= "                  GROUP BY exercicio                                                                                 \n";
    $stSql .= "                          ,cod_empenho                                                                               \n";
    $stSql .= "                          ,cod_entidade                                                                              \n";
    $stSql .= "     ) AS EEAI ON( EE.cod_empenho  = EEAI.cod_empenho                                                                \n";
    $stSql .= "               AND EE.exercicio    = EEAI.exercicio                                                                  \n";
    $stSql .= "               AND EE.cod_entidade = EEAI.cod_entidade      )                                                        \n";
    $stSql .= "     WHERE ENL.cod_nota         = ".$this->getDado( 'cod_nota' )."                                                   \n";
    $stSql .= "       AND ENL.cod_entidade     = ".$this->getDado( 'cod_entidade' )."                                               \n";
    $stSql .= "       AND ENL.exercicio_empenho= '".$this->getDado( 'exercicio' )."'                                                \n";
    $stSql .= "       AND ENLIA.timestamp     <= '".$this->getDado( 'timestamp' )."'                                                \n";
    $stSql .= "     GROUP BY ENL.exercicio_empenho                                                                                  \n";
    $stSql .= "             ,ENL.cod_empenho                                                                                        \n";
    $stSql .= "             ,ENL.dt_vencimento                                                                                          \n";
    $stSql .= "             ,EE.dt_empenho                                                                                          \n";
    $stSql .= "             ,EE.dt_vencimento                                                                                       \n";
    $stSql .= "             ,EPE.cgm_beneficiario                                                                                   \n";
    $stSql .= "             ,EPE.descricao                                                                                          \n";
    $stSql .= "             ,ENL.observacao                                                                                         \n";
    $stSql .= "             ,ENLIA.exercicio                                                                                        \n";
    $stSql .= "             ,ENLIA.cod_entidade                                                                                     \n";
    $stSql .= "             ,ENLIA.cod_nota                                                                                         \n";
    $stSql .= "             ,ENLIA.timestamp                                                                                        \n";
    $stSql .= "             ,ENLI.num_item                                                                                          \n";
    $stSql .= "             ,EIPE.nom_item                                                                                          \n";
    $stSql .= "             ,ENLI.vl_total                                                                                          \n";
    $stSql .= "             ,EIPE.vl_empenhado                                                                                      \n";
    $stSql .= "             ,EEAI.vl_anulado                                                                                        \n";
    $stSql .= "     ORDER BY ENL.exercicio_empenho                                                                                  \n";
    $stSql .= "             ,ENL.cod_empenho                                                                                        \n";
    $stSql .= "             ,ENLIA.exercicio                                                                                        \n";
    $stSql .= "             ,ENLIA.cod_entidade                                                                                     \n";
    $stSql .= "             ,ENLIA.cod_nota                                                                                         \n";
    $stSql .= "             ,ENLIA.timestamp                                                                                        \n";
    $stSql .= "             ,ENLI.num_item                                                                                          \n";
    $stSql .= "             ,ENLI.vl_total                                                                                          \n";
    $stSql .= "             ,EEAI.vl_anulado                                                                                        \n";
    $stSql .= ") AS tbl                                                                                                             \n";
    $stSql .= "    ,sw_cgm       AS CGM                                                                                             \n";
    $stSql .= "    LEFT JOIN sw_cgm_pessoa_fisica AS PF                                                                             \n";
    $stSql .= "    ON( CGM.numcgm = PF.numcgm )                                                                                     \n";
    $stSql .= "    LEFT JOIN sw_cgm_pessoa_juridica AS PJ                                                                           \n";
    $stSql .= "    ON( CGM.numcgm = PJ.numcgm )                                                                                     \n";
    $stSql .= "    ,sw_municipio AS MUN                                                                                             \n";
    $stSql .= "    ,sw_uf        AS UF                                                                                              \n";
    $stSql .= "    ,orcamento.entidade AS OE                                                                                        \n";
    $stSql .= "    LEFT JOIN sw_cgm AS CGME                                                                                         \n";
    $stSql .= "    ON( OE.numcgm  = CGME.numcgm  )                                                                                  \n";
    $stSql .= " -- Join tbl : CGM                                                                                                   \n";
    $stSql .= "WHERE tbl.cgm_beneficiario = CGM.numcgm                                                                              \n";
    $stSql .= " -- Join CGM : Municipio                                                                                             \n";
    $stSql .= "  AND CGM.cod_municipio    = MUN.cod_municipio                                                                       \n";
    $stSql .= "  AND CGM.cod_uf           = MUN.cod_uf                                                                              \n";
    $stSql .= " -- Join CGM : Municipio                                                                                             \n";
    $stSql .= "  AND MUN.cod_uf           = UF.cod_uf                                                                               \n";
    $stSql .= " -- Join tbl : entidade                                                                                              \n";
    $stSql .= "  AND tbl.cod_entidade = OE.cod_entidade                                                                             \n";
    $stSql .= "  AND tbl.exercicio    = OE.exercicio                                                                                \n";
    $stSql .= "GROUP BY tbl.exercicio_empenho                                                                                       \n";
    $stSql .= "        ,tbl.cod_empenho                                                                                             \n";
    $stSql .= "        ,tbl.dt_empenho                                                                                              \n";
    $stSql .= "        ,tbl.dt_vencimento_liquidacao                                                                                              \n";
    $stSql .= "        ,tbl.dt_vencimento                                                                                           \n";
    $stSql .= "        ,tbl.descricao                                                                                               \n";
    $stSql .= "        ,tbl.observacao                                                                                              \n";
    $stSql .= "        ,tbl.cgm_beneficiario                                                                                        \n";
    $stSql .= "        ,CGM.nom_cgm                                                                                                 \n";
    $stSql .= "        ,CGM.fone_residencial                                                                                        \n";
    $stSql .= "        ,MUN.nom_municipio                                                                                           \n";
    $stSql .= "        ,UF.sigla_uf                                                                                                 \n";
    $stSql .= "        ,CGM.tipo_logradouro                                                                                         \n";
    $stSql .= "        ,CGM.logradouro                                                                                              \n";
    $stSql .= "        ,CGM.numero                                                                                                  \n";
    $stSql .= "        ,PF.cpf                                                                                                      \n";
    $stSql .= "        ,PJ.cnpj                                                                                                     \n";
    $stSql .= "        ,tbl.exercicio                                                                                               \n";
    $stSql .= "        ,tbl.cod_entidade                                                                                            \n";
    $stSql .= "        ,CGME.nom_cgm                                                                                                \n";
    $stSql .= "        ,tbl.cod_nota                                                                                                \n";
    $stSql .= "        ,tbl.dt_anulacao                                                                                             \n";
    $stSql .= "        ,tbl.num_item                                                                                                \n";
    $stSql .= "        ,tbl.nom_item                                                                                                \n";
    $stSql .= "        ,tbl.vl_total                                                                                                \n";
    $stSql .= "        ,tbl.vl_empenhado                                                                                            \n";
    $stSql .= "        ,tbl.vl_empenho_anulado                                                                                      \n";
    $stSql .= "ORDER BY tbl.exercicio_empenho                                                                                       \n";
    $stSql .= "        ,tbl.cod_empenho                                                                                             \n";
    $stSql .= "        ,tbl.exercicio                                                                                               \n";
    $stSql .= "        ,tbl.cod_entidade                                                                                            \n";
    $stSql .= "        ,tbl.cod_nota                                                                                                \n";
    $stSql .= "        ,tbl.dt_anulacao                                                                                             \n";
    $stSql .= "        ,tbl.num_item                                                                                                \n";

    return $stSql;
}

/**
    * Monta a cláusula SQL
    * Cada tipo de campo recebe aqui um tratamento diferenciado em seu retorno.
    * @access Public
    * @return String String contendo o SQL
*/
function montaRecuperaNotasAPagarDisponiveisImplantadas()
{
    $stSql  = "CREATE TEMPORARY TABLE TEMP_EMPENHO AS (                                             \n";
    $stSql .= "    SELECT                                                                           \n";
    $stSql .= "        ENL.cod_nota,                                                                \n";
    $stSql .= "        ENL.cod_entidade,                                                            \n";
    $stSql .= "        ENL.exercicio,                                                               \n";
    $stSql .= "        TO_CHAR ( ENL.dt_liquidacao,'dd/mm/yyyy') AS dt_liquidacao,                  \n";
    $stSql .= "        CGME.numcgm AS entidade,                                                     \n";
    $stSql .= "        ENL.cod_empenho,                                                             \n";
    $stSql .= "        ENL.exercicio_empenho,                                                       \n";
    $stSql .= "        ENL.exercicio AS exercicio_nota,                                             \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_nota( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_empenho                 \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "        ) AS vl_itens,                                                               \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_anulado_nota( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_empenho         \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "        ) AS vl_itens_anulados,                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_nota( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "        ) AS vl_ordem,                                                                      \n";
    $stSql .= "      empenho.fn_consultar_valor_apagar_anulado_nota( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "        ) AS vl_ordem_anulada,                                                              \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_pago( ENL.exercicio                   \n";
    $stSql .= "                                                    ,ENL.cod_nota                    \n";
    $stSql .= "                                                    ,ENL.cod_entidade                \n";
    $stSql .= "        ) AS vl_pago,                                                                \n";
    $stSql .= "      empenho.fn_consultar_valor_liquidado_pago_anulado( ENL.exercicio           \n";
    $stSql .= "                                                            ,ENL.cod_nota            \n";
    $stSql .= "                                                            ,ENL.cod_entidade        \n";
    $stSql .= "        ) AS vl_pago_anulado,                                                        \n";
    $stSql .= "        TO_CHAR ( EE.dt_empenho,'dd/mm/yyyy') AS dt_empenho,                         \n";
    $stSql .= "        EPE.cgm_beneficiario,                                                        \n";
    $stSql .= "        EPE.implantado,                                                              \n";
    $stSql .= "        CGM.nom_cgm AS beneficiario                                                  \n";
    $stSql .= "    FROM                                                                             \n";
    $stSql .= "    empenho.nota_liquidacao AS ENL                                               \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    orcamento.entidade      AS OE                                                \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "      ( OE.cod_entidade = ENL.cod_entidade AND                                       \n";
    $stSql .= "        OE.exercicio    = ENL.exercicio       )                                      \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    sw_cgm                     AS CGME                                              \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "        CGME.numcgm = OE.numcgm                                                      \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.pagamento_liquidacao AS EPL                                          \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        ENL.exercicio    = EPL.exercicio_liquidacao AND                              \n";
    $stSql .= "        ENL.cod_entidade = EPL.cod_entidade         AND                              \n";
    $stSql .= "        ENL.cod_nota     = EPL.cod_nota                )                             \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.nota_liquidacao_paga as NLP                                          \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        NLP.cod_entidade = ENL.cod_entidade AND                                      \n";
    $stSql .= "        NLP.cod_nota     = ENL.cod_nota     AND                                      \n";
    $stSql .= "        NLP.exercicio    = ENL.exercicio        )                                    \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLP                    \n";
    $stSql .= "    ON (                                                                             \n";
    $stSql .= "        EPL.cod_nota             = EPLP.cod_nota              AND                    \n";
    $stSql .= "        EPL.cod_ordem            = EPLP.cod_ordem             AND                    \n";
    $stSql .= "        EPL.exercicio_liquidacao = EPLP.exercicio_liquidacao  AND                    \n";
    $stSql .= "        EPL.cod_entidade         = EPLP.cod_entidade          AND                    \n";
    $stSql .= "        EPL.exercicio            = EPLP.exercicio             AND                    \n";
    $stSql .= "        NLP.cod_nota             = EPLP.cod_nota              AND                    \n";
    $stSql .= "        NLP.exercicio            = EPLP.exercicio_liquidacao  AND                    \n";
    $stSql .= "        NLP.cod_entidade         = EPLP.cod_entidade          AND                    \n";
    $stSql .= "        NLP.timestamp            = EPLP.timestamp                 )                  \n";
    $stSql .= "     ,empenho.empenho          AS EE                                             \n";
    $stSql .= "     ,empenho.pre_empenho      AS EPE                                            \n";
    $stSql .= "    LEFT JOIN                                                                        \n";
    $stSql .= "    sw_cgm AS CGM                                                                   \n";
    $stSql .= "    ON                                                                               \n";
    $stSql .= "        CGM.numcgm = EPE.cgm_beneficiario                                            \n";
    $stSql .= "     ,empenho.restos_pre_empenho AS RPE                                          \n";
    $stSql .= "    WHERE                                                                            \n";
    $stSql .= "         EE.cod_empenho      = ENL.cod_empenho                                       \n";
    $stSql .= "    AND  EE.exercicio        = ENL.exercicio_empenho                                 \n";
    $stSql .= "    AND  EE.cod_entidade     = ENL.cod_entidade                                      \n";
    $stSql .= "    AND  EPE.exercicio       = EE.exercicio                                          \n";
    $stSql .= "    AND  EPE.cod_pre_empenho = EE.cod_pre_empenho                                    \n";
    $stSql .= "    AND  RPE.cod_pre_empenho = EPE.cod_pre_empenho                                   \n";
    $stSql .= "    AND  RPE.exercicio       = EPE.exercicio                                         \n";
    $stSql .= $this->getDado('filtro')                                                            ."\n";
    $stSql .= ");                                                                                   \n";
    $stSql .= "                                                                                     \n";
    $stSql .= "SELECT                                                                               \n";
    $stSql .= "    *                                                                                \n";
    $stSql .= "    ,publico.fn_numeric_br( (TEMP_EMPENHO.vl_itens - TEMP_EMPENHO.vl_itens_anulados) ) AS vl_nota \n";
    $stSql .= "FROM TEMP_EMPENHO                                                                    \n";
    $stSql .= "WHERE                                                                                \n";
    $stSql .= "( TEMP_EMPENHO.vl_itens - TEMP_EMPENHO.vl_itens_anulados ) > ( TEMP_EMPENHO.vl_ordem - TEMP_EMPENHO.vl_ordem_anulada )  \n";
    $stSql .= "GROUP BY                                                                             \n";
    $stSql .= "        cod_nota,                                                                    \n";
    $stSql .= "        cod_entidade,                                                                \n";
    $stSql .= "        exercicio,                                                                   \n";
    $stSql .= "        dt_liquidacao,                                                               \n";
    $stSql .= "        entidade,                                                                    \n";
    $stSql .= "        cod_empenho,                                                                 \n";
    $stSql .= "        exercicio_empenho,                                                           \n";
    $stSql .= "        exercicio_nota,                                                              \n";
    $stSql .= "        vl_itens,                                                                    \n";
    $stSql .= "        vl_itens_anulados,                                                           \n";
    $stSql .= "        vl_ordem,                                                                    \n";
    $stSql .= "        vl_ordem_anulada,                                                            \n";
    $stSql .= "        vl_pago,                                                                     \n";
    $stSql .= "        vl_pago_anulado,                                                             \n";
    $stSql .= "        dt_empenho,                                                                  \n";
    $stSql .= "        cgm_beneficiario,                                                            \n";
    $stSql .= "        implantado,                                                                  \n";
    $stSql .= "        beneficiario                                                                 \n";
    $stSql .= "ORDER BY  cod_nota ;                                                          \n";
    $stSql .= ";                                                                             \n";

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
function recuperaNotasAPagarDisponiveisImplantadas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado('filtro', $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaNotasAPagarDisponiveisImplantadas().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}
function montaRecuperaMaiorDataLiquidacao()
{
    $stSql  =" SELECT                                                                                                                   \n";
    $stSql .="    CASE WHEN to_date('".$this->getDado("stDataEmpenho")."','dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN   \n";
    $stSql .="        CASE WHEN max(dt_liquidacao) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN             \n";
    $stSql .="            '01/01/".$this->getDado("stExercicio")."'                                                                   \n";
    $stSql .="        ELSE                                                                                                              \n";
    $stSql .="            to_char(max(dt_liquidacao),'dd/mm/yyyy')                                                                      \n";
    $stSql .="        END                                                                                                               \n";
    $stSql .="    ELSE                                                                                                                  \n";
    $stSql .="        CASE WHEN max(dt_liquidacao) < to_date('01/01/".$this->getDado("stExercicio")."','dd/mm/yyyy') THEN             \n";
    $stSql .="            '".$this->getDado("stDataEmpenho")."'                                                                         \n";
    $stSql .="        ELSE                                                                                                              \n";
    $stSql .="            CASE WHEN max(dt_liquidacao) < to_date('".$this->getDado("stDataEmpenho")."','dd/mm/yyyy') THEN               \n";
    $stSql .="                '".$this->getDado("stDataEmpenho")."'                                                                     \n";
    $stSql .="            ELSE                                                                                                          \n";
    $stSql .="                to_char(max(dt_liquidacao),'dd/mm/yyyy')                                                                  \n";
    $stSql .="            END                                                                                                           \n";
    $stSql .="        END                                                                                                               \n";
    $stSql .="    END AS data_liquidacao                                                                                                \n";
    $stSql .="FROM                                                                                                                      \n";
    $stSql .= "    empenho.nota_liquidacao                                                                                              \n";

    return $stSql;

}

function montaRecuperaMaiorDataLiquidacaoAnulacao()
{
    $stSql  =" SELECT                                                                                                                   \n";
    $stSql .="    CASE WHEN to_date('".$this->getDado("stDataLiquidacao")."'::text,'dd/mm/yyyy') < to_date('01/01/".$this->getDado("stExercicio")."'::text,'dd/mm/yyyy') THEN   \n";
    $stSql .="        CASE WHEN to_date(max(timestamp)::text,'yyyy-mm-dd' ) < to_date('01/01/".$this->getDado("stExercicio")."'::text,'dd/mm/yyyy') THEN             \n";
    $stSql .="            '01/01/".$this->getDado("stExercicio")."'                                                                     \n";
    $stSql .="        ELSE                                                                                                              \n";
    $stSql .="            to_char(to_date(max(timestamp)::text,'yyyy-mm-dd' ),'dd/mm/yyyy')                                                   \n";
    $stSql .="        END                                                                                                               \n";
    $stSql .="    ELSE                                                                                                                  \n";
    $stSql .="        CASE WHEN to_date(max(timestamp)::text,'yyyy-mm-dd' ) < to_date('01/01/".$this->getDado("stExercicio")."'::text,'dd/mm/yyyy') THEN             \n";
    $stSql .="            '".$this->getDado("stDataLiquidacao")."'                                                                      \n";
    $stSql .="        ELSE                                                                                                              \n";
    $stSql .="            CASE WHEN to_date(max(timestamp)::text,'yyyy-mm-dd' ) < to_date('".$this->getDado("stDataLiquidacao")."'::text,'dd/mm/yyyy') THEN               \n";
    $stSql .="                '".$this->getDado("stDataLiquidacao")."'                                                                  \n";
    $stSql .="            ELSE                                                                                                          \n";
    $stSql .="                to_char(to_date(max(timestamp)::text,'yyyy-mm-dd' ),'dd/mm/yyyy')                                               \n";
    $stSql .="            END                                                                                                           \n";
    $stSql .="        END                                                                                                               \n";
    $stSql .="    END AS data_anulacao                                                                                                  \n";
    $stSql .="FROM                                                                                                                      \n";
    $stSql .= "    empenho.nota_liquidacao_item_anulado                                                                                 \n";

    return $stSql;

}

function montaRecuperaMaiorDataLiquidacaoAnulacaoEmpenho()
{
    $stSql  = "SELECT                                                              \n";
    $stSql .= "     to_char(max(ia.timestamp),'dd/mm/yyyy') as dataanulacao        \n";
    $stSql .= "    FROM     empenho.empenho                       AS  E            \n";
    $stSql .= "            ,empenho.nota_liquidacao               AS NL            \n";
    $stSql .= "            ,empenho.nota_liquidacao_item          AS LI            \n";
    $stSql .= "            ,empenho.nota_liquidacao_item_anulado  AS IA            \n";
    $stSql .= "    WHERE                                                           \n";
    $stSql .= "                                                                    \n";
    $stSql .= "            NL.exercicio_empenho = E.exercicio                      \n";
    $stSql .= "    AND     NL.cod_empenho       = E.cod_empenho                    \n";
    $stSql .= "    AND     NL.cod_entidade      = E.cod_entidade                   \n";
    $stSql .= "                                                                    \n";
    $stSql .= "    AND     LI.exercicio         = NL.exercicio                     \n";
    $stSql .= "    AND     LI.cod_nota          = NL.cod_nota                      \n";
    $stSql .= "    AND     LI.cod_entidade      = NL.cod_entidade                  \n";
    $stSql .= "                                                                    \n";
    $stSql .= "    AND     IA.cod_entidade      = LI.cod_entidade                  \n";
    $stSql .= "    AND     IA.cod_nota          = LI.cod_nota                      \n";
    $stSql .= "    AND     IA.exercicio         = LI.exercicio                     \n";
    $stSql .= "    AND     IA.num_item          = LI.num_item                      \n";
    $stSql .= "    AND     IA.cod_pre_empenho   = LI.cod_pre_empenho               \n";

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
function recuperaMaiorDataLiquidacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMaiorDataLiquidacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaiorDataLiquidacaoAnulacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMaiorDataLiquidacaoAnulacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMaiorDataLiquidacaoAnulacaoEmpenho(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMaiorDataLiquidacaoAnulacaoEmpenho().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValores()
{
    $stSql  = "select  liq.cod_entidade\n";
    $stSql .= "       ,coalesce(tot_op.valor_op          ,0.00) as vl_total_op\n";
    $stSql .= "       ,coalesce(ntpg.vl_pagamento        ,0.00) as vl_total_por_liquidacao  \n";
    $stSql .= "       ,coalesce(ntpg.vl_pago_liq         ,0.00) as vl_pago \n";
    $stSql .= "       ,coalesce(ntpg.vl_pago_anulado_liq ,0.00) as vl_pago_anulado \n";
    $stSql .= "       ,ntpg.cod_ordem \n";
    $stSql .= "       ,coalesce(ntpg.vl_pagamento,0.00)-(coalesce(ntpg.vl_pago_liq,0.00)-coalesce(ntpg.vl_pago_anulado_liq ,0.00)) as vl_a_pagar\n";
    $stSql .= "\n";
    $stSql .= "from empenho.nota_liquidacao as liq \n";
    $stSql .= "    join (\n";
    $stSql .= "          select  pl.vl_pagamento\n";
    $stSql .= "                 ,pl.cod_entidade\n";
    $stSql .= "                 ,pl.cod_ordem\n";
    $stSql .= "                 ,pl.exercicio\n";
    $stSql .= "                 ,pl.cod_nota\n";
    $stSql .= "                 ,pl.exercicio_liquidacao\n";
    $stSql .= "                 ,plnlp.timestamp\n";
    $stSql .= "                 ,nlp_nlpa.vl_pago    as vl_pago_liq\n";
    $stSql .= "                 ,nlp_nlpa.vl_anulado as vl_pago_anulado_liq\n";
    $stSql .= "                  \n";
    $stSql .= "          from empenho.pagamento_liquidacao as pl\n";
    $stSql .= "               left join empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp\n";
    $stSql .= "                    on (    plnlp.cod_ordem    = pl.cod_ordem\n";
    $stSql .= "                        and plnlp.exercicio    = pl.exercicio\n";
    $stSql .= "                        and plnlp.cod_entidade = pl.cod_entidade\n";
    $stSql .= "                        and plnlp.cod_nota = ". $this->getDado('cod_nota') . " \n";
    $stSql .= "                       )\n";
    $stSql .= "               left join (\n";
    $stSql .= "                           select  nlp.cod_nota\n";
    $stSql .= "                                  ,nlp.exercicio\n";
    $stSql .= "                                  ,nlp.cod_entidade\n";
    $stSql .= "                                  ,nlp.vl_pago\n";
    $stSql .= "                                  ,nlpa.vl_anulado\n";
    $stSql .= "                                  ,nlp.timestamp\n";
    $stSql .= "                                  \n";
    $stSql .= "                           from empenho.nota_liquidacao_paga as nlp\n";
    $stSql .= "                                left join empenho.nota_liquidacao_paga_anulada as nlpa\n";
    $stSql .= "                                     on (     nlp.cod_nota     = nlpa.cod_nota\n";
    $stSql .= "                                          and nlp.cod_entidade = nlpa.cod_entidade\n";
    $stSql .= "                                          and nlp.exercicio    = nlpa.exercicio\n";
    $stSql .= "                                          and nlp.timestamp    = nlpa.timestamp\n";
    $stSql .= "                                        )\n";
    $stSql .= "                         ) as nlp_nlpa on (    nlp_nlpa.cod_nota     = plnlp.cod_nota\n";
    $stSql .= "                                           and nlp_nlpa.exercicio    = plnlp.exercicio_liquidacao\n";
    $stSql .= "                                           and nlp_nlpa.cod_entidade = plnlp.cod_entidade\n";
    $stSql .= "                                           and nlp_nlpa.timestamp    = plnlp.timestamp\n";
    $stSql .= "                                          )\n";
    $stSql .= "                   \n";
    $stSql .= "         ) as ntpg on (     liq.cod_nota     = ntpg.cod_nota \n";
    $stSql .= "                        and liq.cod_entidade = ntpg.cod_entidade \n";
    $stSql .= "                        and liq.exercicio    = ntpg.exercicio_liquidacao\n";
    $stSql .= "                      ) \n";
    $stSql .= "\n";
    $stSql .= "    join (\n";
    $stSql .= "          select  sum(vl_pagamento) as valor_op\n";
    $stSql .= "                 ,cod_ordem\n";
    $stSql .= "                 ,exercicio\n";
    $stSql .= "                 ,cod_entidade\n";
    $stSql .= "                 ,exercicio_liquidacao\n";
    $stSql .= "          from empenho.pagamento_liquidacao\n";
    $stSql .= "          group by  cod_ordem\n";
    $stSql .= "                   ,exercicio\n";
    $stSql .= "                   ,cod_entidade\n";
    $stSql .= "                   ,exercicio_liquidacao\n";
    $stSql .= "         ) as tot_op on (     tot_op.cod_ordem    = ntpg.cod_ordem\n";
    $stSql .= "                          and tot_op.exercicio    = ntpg.exercicio\n";
    $stSql .= "                          and tot_op.cod_entidade = ntpg.cod_entidade\n";
    $stSql .= "                          and tot_op.exercicio_liquidacao = ntpg.exercicio_liquidacao\n";
    $stSql .= "                        )\n";
    $stSql .= "  \n";
    $stSql .= "\n";

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
function recuperaValores(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY") === false) ? " ORDER BY $stOrdem" : $stOrdem;

    $stSql = $this->montaRecuperaValores().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
