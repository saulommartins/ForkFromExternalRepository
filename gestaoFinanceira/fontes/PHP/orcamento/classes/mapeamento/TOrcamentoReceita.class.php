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

    * Classe de mapeamento da tabela ORCAMENTO.RECEITA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    $Id: TOrcamentoReceita.class.php 66123 2016-07-19 21:02:27Z michel $

    * Casos de uso: uc-02.01.06, uc-02.04.04, uc-02.01.34, uc-02.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TOrcamentoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoReceita()
{
    parent::Persistente();

    $this->setTabela('orcamento.receita');

    $this->setCampoCod('cod_receita');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('cod_receita','integer',true,'',true,false);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',false,true);
    $this->AddCampo('cod_conta','integer',true,'',false,true);
    $this->AddCampo('cod_recurso','integer',true,'',false,true);
    $this->AddCampo('vl_original','numeric',true,'14,02',false,false);
    $this->AddCampo('dt_criacao','date',false,'',false,false);
    $this->AddCampo('credito_tributario','boolean',false,'',false,false);
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                          ".$stQuebra;
    $stSql .= "     CR.mascara_classificacao,                                   ".$stQuebra;
    $stSql .= "     trim(CR.descricao) as descricao,                            ".$stQuebra;
    $stSql .= "     O.*,                                                        ".$stQuebra;
    $stSql .= "     R.nom_recurso,                                              ".$stQuebra;
    $stSql .= "     R.masc_recurso_red,                                         ".$stQuebra;
    $stSql .= "     R.cod_detalhamento,                                         ".$stQuebra;
    $stSql .= "     R.cod_recurso                                               ".$stQuebra;
    $stSql .= " FROM                                                            ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_receita AS CR,                   ".$stQuebra;
    $stSql .= "     orcamento.receita        AS O,                              ".$stQuebra;
    $stSql .= "     orcamento.recurso('".$this->getDado('exercicio')."') AS R   ".$stQuebra;
    $stSql .= " WHERE                                                           ".$stQuebra;
    $stSql .= "         CR.exercicio IS NOT NULL                                ".$stQuebra;
    $stSql .= "     AND O.cod_conta   = CR.cod_conta                            ".$stQuebra;
    $stSql .= "     AND O.exercicio   = CR.exercicio                            ".$stQuebra;
    $stSql .= "     AND O.cod_recurso = R.cod_recurso                           ".$stQuebra;
    $stSql .= "     AND O.exercicio   = R.exercicio                             ".$stQuebra;

    return $stSql;
}

function montaRecuperaRelacionamentoComEntidades()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                          ".$stQuebra;
    $stSql .= "     CR.mascara_classificacao,                                   ".$stQuebra;
    $stSql .= "     trim(CR.descricao) as descricao,                            ".$stQuebra;
    $stSql .= "     O.*,                                                        ".$stQuebra;
    $stSql .= "     R.nom_recurso,                                              ".$stQuebra;
    $stSql .= "     R.masc_recurso_red,                                         ".$stQuebra;
    $stSql .= "     R.cod_detalhamento,                                         ".$stQuebra;
    $stSql .= "     R.cod_recurso                                               ".$stQuebra;
    $stSql .= " FROM                                                            ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_receita AS CR,                   ".$stQuebra;
    $stSql .= "     orcamento.receita        AS O,                              ".$stQuebra;
    $stSql .= "     orcamento.entidade        AS E,                              ".$stQuebra;
    $stSql .= "     orcamento.recurso('".$this->getDado('exercicio')."') AS R   ".$stQuebra;
    $stSql .= " WHERE                                                           ".$stQuebra;
    $stSql .= "         CR.exercicio IS NOT NULL                                ".$stQuebra;
    $stSql .= "     AND O.cod_conta   = CR.cod_conta                            ".$stQuebra;
    $stSql .= "     AND O.exercicio   = CR.exercicio                            ".$stQuebra;
    $stSql .= "     AND O.cod_recurso = R.cod_recurso                           ".$stQuebra;
    $stSql .= "     AND O.exercicio   = R.exercicio                             ".$stQuebra;

    return $stSql;
}

function recuperaRelacionamentoContaReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoContaReceita().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoContaReceita()
{
    $stSql  = "SELECT OCR.*                         \n";
    $stSql .= "      ,ORE.cod_receita               \n";
    $stSql .= "FROM orcamento.receita       AS ORE  \n";
    $stSql .= "    ,orcamento.conta_receita AS OCR  \n";
    $stSql .= "WHERE ORE.exercicio = OCR.exercicio  \n";
    $stSql .= "  AND ORE.cod_conta = OCR.cod_conta  \n";

    return $stSql;
}

function recuperaRelacionamentoEntidadeSintetica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoEntidadeSintetica($stCondicao).$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoEntidadeSintetica($stFiltro = '')
{
     $stSql  = " SELECT conta_receita.cod_estrutural                                                                 \n";
     $stSql .= "       ,conta_receita.descricao                                                                      \n";
     $stSql .= "       ,conta_receita.cod_conta                                                                      \n";
     $stSql .= "       ,previsoes.cod_receita                                                                        \n";
     $stSql .= "       ,previsoes.periodo                                                                            \n";
     $stSql .= "       ,SUM(previsoes.vl_periodo) AS vl_periodo                                                      \n";
     $stSql .= "   FROM orcamento.conta_receita                                                                      \n";
     $stSql .= "  INNER JOIN ( SELECT conta_receita.cod_estrutural                                                   \n";
     $stSql .= "                     ,previsao_receita.periodo                                                       \n";
     $stSql .= "                     ,previsao_receita.vl_periodo                                                    \n";
     $stSql .= "                     ,previsao_receita.exercicio                                                     \n";
     $stSql .= "                     ,receita.cod_receita                                                            \n";
     $stSql .= "                 FROM orcamento.conta_receita                                                        \n";
     $stSql .= "                     ,orcamento.receita                                                              \n";
     $stSql .= "                      JOIN orcamento.recurso as recurso
                                      ON ( recurso.cod_recurso = receita.cod_recurso
                                       AND recurso.exercicio = receita.exercicio )                                   \n";
     $stSql .= "                     ,orcamento.previsao_receita                                                     \n";
     $stSql .= "                WHERE conta_receita.cod_conta      = receita.cod_conta                               \n";
     $stSql .= "                  AND conta_receita.exercicio      = receita.exercicio                               \n";
     $stSql .= "                  AND previsao_receita.exercicio   = receita.Exercicio                               \n";
     $stSql .= "                  AND previsao_receita.cod_receita = receita.cod_receita                             \n";
     $stSql .= "  $stFiltro                                                                                          \n";
     $stSql .= "         ) AS previsoes                                                                              \n";
     $stSql .= "    ON (previsoes.cod_estrutural LIKE(publico.fn_mascarareduzida(conta_receita.cod_estrutural)||'%'))\n";
     $stSql .= " WHERE conta_receita.exercicio = '".Sessao::getExercicio()."'                                            \n";
     $stSql .= " GROUP BY conta_receita.descricao                                                                    \n";
     $stSql .= "       ,conta_receita.cod_conta                                                                      \n";
     $stSql .= "         ,conta_receita.cod_estrutural                                                               \n";
     $stSql .= "         ,previsoes.cod_receita                                                                      \n";
     $stSql .= "         ,previsoes.periodo                                                                          \n";
     $stSql .= " ORDER BY conta_receita.cod_estrutural ASC                                                           \n";

   return $stSql;
}

function recuperaRelacionamentoEntidadeAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoEntidadeAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoEntidadeAnalitica()
{
    $stSql  = "SELECT conta_receita.cod_estrutural                              \n";
    $stSql .= "      ,conta_receita.descricao                                   \n";
    $stSql .= "      ,conta_receita.cod_conta                                   \n";
    $stSql .= "      ,receita.cod_receita                                       \n";
    $stSql .= "      ,receita.cod_entidade                                      \n";
    $stSql .= "      ,receita.cod_recurso                                       \n";
    $stSql .= "      ,receita.vl_original                                       \n";
    $stSql .= "      ,previsao_receita.periodo                                  \n";
    $stSql .= "      ,previsao_receita.vl_periodo                               \n";
    $stSql .= "  FROM orcamento.receita                                         \n";
    $stSql .= "       JOIN orcamento.recurso as recurso
                      ON ( recurso.cod_recurso = receita.cod_recurso
                        AND recurso.exercicio = receita.exercicio )             \n";
    $stSql .= "      ,orcamento.conta_receita                                   \n";
    $stSql .= "      ,orcamento.previsao_receita                                \n";
    $stSql .= " WHERE receita.exercicio            = conta_receita.exercicio    \n";
    $stSql .= "   AND receita.cod_conta            = conta_receita.cod_conta    \n";
    $stSql .= "   AND previsao_receita.cod_receita = receita.cod_receita        \n";
    $stSql .= "   AND previsao_receita.exercicio   = receita.exercicio          \n";

    return $stSql;
}

function recuperaReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceita().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceita()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                           ".$stQuebra;
    $stSql .= "      CR.mascara_classificacao,                                    ".$stQuebra;
    $stSql .= "      CR.descricao,                                                ".$stQuebra;
    $stSql .= "      O.*                                                          ".$stQuebra;
    $stSql .= "  FROM                                                             ".$stQuebra;
    $stSql .= "      orcamento.VW_CLASSIFICACAO_RECEITA  AS CR,                   ".$stQuebra;
    $stSql .= "      ORCAMENTO.RECEITA                   AS O                     ".$stQuebra;
    $stSql .= "  LEFT JOIN (  SELECT dr.exercicio , dr.cod_receita_secundaria     ".$stQuebra;
    $stSql .= "               FROM contabilidade.desdobramento_receita as dr,     ".$stQuebra;
    $stSql .= "                    orcamento.receita as ore                       ".$stQuebra;
    $stSql .= "               WHERE   ore.cod_receita = dr.cod_receita_secundaria ".$stQuebra;
    $stSql .= "                   AND ore.exercicio   = dr.exercicio              ".$stQuebra;
    $stSql .= "          AND ore.exercicio = '".$this->getDado('exercicio')."'    ".$stQuebra;
    $stSql .= "  ) as recsec ON (O.cod_receita = recsec.cod_receita_secundaria    ".$stQuebra;
    $stSql .= "                   AND O.exercicio   = recsec.exercicio )          ".$stQuebra;
    $stSql .= "  WHERE                                                            ".$stQuebra;
    $stSql .= "          CR.exercicio IS NOT NULL                                 ".$stQuebra;
    $stSql .= "      AND O.cod_conta     = CR.cod_conta                           ".$stQuebra;
    $stSql .= "      AND O.exercicio     = CR.exercicio                           ".$stQuebra;
if($this->getDado('exercicio'))
    $stSql .= "      AND O.exercicio = '".$this->getDado('exercicio')."'    \n";
if($this->getDado('cod_conta'))
    $stSql .= "      AND O.cod_conta = ".$this->getDado('cod_conta')."      \n";
if($this->getDado('cod_recurso'))
    $stSql .= "      AND O.cod_recurso = ".$this->getDado('cod_recurso')."  \n";
    $stSql .= "      AND recsec.cod_receita_secundaria is null              \n";

    return $stSql;
}

function recuperaReceitaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaAnalitica()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.mascara_classificacao,                   ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.descricao,                               ".$stQuebra;
    $stSql .= "      RECEITA.*                                              ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.VW_CLASSIFICACAO_RECEITA  AS CLASSIFICACAO,  ".$stQuebra;
    $stSql .= "      ORCAMENTO.RECEITA                   AS RECEITA,        ".$stQuebra;
    $stSql .= "      ORCAMENTO.CONTA_RECEITA             AS CR,             ".$stQuebra;
    $stSql .= "      CONTABILIDADE.PLANO_CONTA           AS CPC,            ".$stQuebra;
    $stSql .= "      CONTABILIDADE.PLANO_ANALITICA       AS CPA             ".$stQuebra;
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          CLASSIFICACAO.exercicio IS NOT NULL                ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CLASSIFICACAO.cod_conta    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CLASSIFICACAO.exercicio    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CR.exercicio               ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CR.cod_conta               ".$stQuebra;
    $stSql .= "      and CPC.exercicio         = CR.exercicio               ".$stQuebra;
    $stSql .= "      AND CASE WHEN substr(cr.cod_estrutural,1,1) = '9' AND cr.exercicio > '2007'    \n";
    $stSql .= "               THEN CPC.cod_estrutural    = CR.cod_estrutural                    \n";
    $stSql .= "               ELSE CPC.cod_estrutural    = '4.'||CR.cod_estrutural              \n";
    $stSql .= "          END                                                                    \n";
    $stSql .= "      and CPC.exercicio         = CPA.exercicio              ".$stQuebra;
    $stSql .= "      AND CPC.cod_conta         = CPA.cod_conta              ".$stQuebra;

    return $stSql;
}
function recuperaReceitaAnaliticaTCE(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaAnaliticaTCE().$stCondicao.$stOrdem; 
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaAnaliticaTCE()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.mascara_classificacao,                   ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.descricao,                               ".$stQuebra;
    $stSql .= "      RECEITA.*                                              ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.VW_CLASSIFICACAO_RECEITA  AS CLASSIFICACAO,  ".$stQuebra;
    $stSql .= "      ORCAMENTO.RECEITA                   AS RECEITA,        ".$stQuebra;
    $stSql .= "      ORCAMENTO.CONTA_RECEITA             AS CR,             ".$stQuebra;
    $stSql .= "      CONTABILIDADE.CONFIGURACAO_LANCAMENTO_RECEITA AS CLR   ".$stQuebra;
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          CLASSIFICACAO.exercicio IS NOT NULL                ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CLASSIFICACAO.cod_conta    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CLASSIFICACAO.exercicio    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CR.exercicio               ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CR.cod_conta               ".$stQuebra;
    $stSql .= "      AND CR.exercicio          = CLR.exercicio              ".$stQuebra;
    $stSql .= "      AND CR.cod_conta          = CLR.cod_conta_receita      ".$stQuebra;

    return $stSql;
}

function recuperaLancamentoReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLancamentoReceita().$stCondicao.$stOrdem; 
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaLancamentoReceita(){
    
    $stQuebra = "\n";
    $stSql  = "      SELECT                                                      ".$stQuebra;
    $stSql .= "          CLASSIFICACAO.mascara_classificacao,                    ".$stQuebra;
    $stSql .= "          CLASSIFICACAO.descricao,                                ".$stQuebra;
    $stSql .= "          RECEITA.*                                               ".$stQuebra;
    $stSql .= "      FROM                                                        ".$stQuebra;
    $stSql .= "          orcamento.VW_CLASSIFICACAO_RECEITA  AS CLASSIFICACAO,   ".$stQuebra;
    $stSql .= "          ORCAMENTO.RECEITA                   AS RECEITA,         ".$stQuebra;
    $stSql .= "          ORCAMENTO.CONTA_RECEITA             AS CR               ".$stQuebra;
    $stSql .= "     WHERE                                                        ".$stQuebra;
    $stSql .= "           CLASSIFICACAO.exercicio IS NOT NULL                    ".$stQuebra;
    $stSql .= "       AND RECEITA.cod_conta     = CLASSIFICACAO.cod_conta        ".$stQuebra;
    $stSql .= "       AND RECEITA.exercicio     = CLASSIFICACAO.exercicio        ".$stQuebra;
    $stSql .= "       AND RECEITA.exercicio     = CR.exercicio                   ".$stQuebra;
    $stSql .= "       AND RECEITA.cod_conta     = CR.cod_conta                   ".$stQuebra;

    return $stSql;
    
}

function recuperaReceitaDedutora(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaDedutora().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaDedutora()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      CR.mascara_classificacao,                              ".$stQuebra;
    $stSql .= "      CR.descricao,                                          ".$stQuebra;
    $stSql .= "      O.*                                                    ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.VW_CLASSIFICACAO_RECEITA  AS CR,             ".$stQuebra;
    $stSql .= "      ORCAMENTO.RECEITA                   AS O               ".$stQuebra;
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          CR.exercicio IS NOT NULL                           ".$stQuebra;
    $stSql .= "      AND O.cod_conta     = CR.cod_conta                     ".$stQuebra;
    $stSql .= "      AND O.exercicio     = CR.exercicio                     ".$stQuebra;
    $stSql .= "      AND (CR.mascara_classificacao  like '9.7%'              ".$stQuebra;
    $stSql .= "      OR  CR.mascara_classificacao  like '9%')              ".$stQuebra;

    return $stSql;
}

function recuperaReceitaDedutoraAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaDedutoraAnalitica().$stCondicao.$stOrdem;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaDedutoraAnalitica()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                     ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.mascara_classificacao,                   ".$stQuebra;
    $stSql .= "      CLASSIFICACAO.descricao,                               ".$stQuebra;
    $stSql .= "      RECEITA.*                                              ".$stQuebra;
    $stSql .= "  FROM                                                       ".$stQuebra;
    $stSql .= "      orcamento.VW_CLASSIFICACAO_RECEITA  AS CLASSIFICACAO,  ".$stQuebra;
    $stSql .= "      ORCAMENTO.RECEITA                   AS RECEITA,        ".$stQuebra;
    $stSql .= "      ORCAMENTO.CONTA_RECEITA             AS CR,             ".$stQuebra;
    if ( $this->getDado('exercicio') > '2012' ) {
        $stSql .= "      CONTABILIDADE.CONFIGURACAO_LANCAMENTO_RECEITA AS CLR   ".$stQuebra;
    } else {
        $stSql .= "      CONTABILIDADE.PLANO_CONTA           AS CPC,   ".$stQuebra;
        $stSql .= "      CONTABILIDADE.PLANO_ANALITICA       AS CPA    ".$stQuebra;
    }
    $stSql .= "  WHERE                                                      ".$stQuebra;
    $stSql .= "          CLASSIFICACAO.exercicio IS NOT NULL                ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CLASSIFICACAO.cod_conta    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CLASSIFICACAO.exercicio    ".$stQuebra;
    $stSql .= "      AND CLASSIFICACAO.mascara_classificacao  LIKE '9.%'    ".$stQuebra;
    $stSql .= "      AND RECEITA.exercicio     = CR.exercicio               ".$stQuebra;
    $stSql .= "      AND RECEITA.cod_conta     = CR.cod_conta               ".$stQuebra;

    if ( $this->getDado('exercicio') > '2012' ) {
        $stSql .= "      AND CR.exercicio          = CLR.exercicio              ".$stQuebra;
        $stSql .= "      AND CR.cod_conta          = CLR.cod_conta_receita      ".$stQuebra;
        $stSql .= "      AND CLR.estorno = 'false'                              ".$stQuebra;
    } else {
        $stSql .= "      and CPC.exercicio         = CR.exercicio               ".$stQuebra;

        if ( $this->getDado('exercicio') < '2008' ) {
            $stSql .= "      AND CPC.cod_estrutural    = '4.'||CR.cod_estrutural    ".$stQuebra;
        } else {
            $stSql .= "      AND CPC.cod_estrutural    = CR.cod_estrutural    ".$stQuebra;
        }
        $stSql .= "      and CPC.exercicio         = CPA.exercicio              ".$stQuebra;
        $stSql .= "      AND CPC.cod_conta         = CPA.cod_conta              ".$stQuebra;
    }

    return $stSql;
}

function recuperaAnexo01(&$rsRecordSet, $stFiltroEntidade = "", $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAnexo01( $stFiltroEntidade ).$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAnexo01($stFiltroEntidade = "")
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                             ".$stQuebra;
    $stSql .= "     VCLA.cod_conta,                                                ".$stQuebra;
    $stSql .= "     VCLA.descricao,                                                ".$stQuebra;
    $stSql .= "     substr( VCLA.mascara_classificacao, 1, 5 ) as classificacao,   ".$stQuebra;
    $stSql .= "     R.vl_original                                                  ".$stQuebra;
    $stSql .= " FROM                                                               ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_receita AS VCLA                 ".$stQuebra;
    $stSql .= "     LEFT OUTER JOIN orcamento.receita AS R ON                      ".$stQuebra;
    $stSql .= "         R.exercicio = VCLA.exercicio AND                           ".$stQuebra;
    $stSql .= "         R.cod_conta = VCLA.cod_conta".$stFiltroEntidade."          ".$stQuebra;
    $stSql .= " WHERE                                                              ".$stQuebra;
    $stSql .= "     VCLA.exercicio IS NOT NULL                                     ".$stQuebra;

    return $stSql;
}

function recuperaAnexo02(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAnexo02().$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAnexo02()
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                             ".$stQuebra;
    $stSql .= "     VCLA.cod_conta,                                                ".$stQuebra;
    $stSql .= "     VCLA.descricao,                                                ".$stQuebra;
    $stSql .= "     VCLA.mascara_classificacao,                                    ".$stQuebra;
    $stSql .= "     substr( VCLA.mascara_classificacao, 1, 7 ) as classificacao,   ".$stQuebra;
    $stSql .= "     R.vl_original                                                  ".$stQuebra;
    $stSql .= " FROM                                                               ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_receita AS VCLA                     ".$stQuebra;
    $stSql .= "     LEFT OUTER JOIN orcamento.receita AS R ON                      ".$stQuebra;
    $stSql .= "         R.exercicio = VCLA.exercicio AND                           ".$stQuebra;
    $stSql .= "         R.cod_conta = VCLA.cod_conta                               ".$stQuebra;
    $stSql .= " WHERE                                                              ".$stQuebra;
    $stSql .= "     VCLA.exercicio IS NOT NULL                                     ".$stQuebra;

    return $stSql;
}

function recuperaCodReceita(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCodReceita();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodReceita()
{
    $stSql  =  "select rec.cod_receita\n";
    $stSql .=  "from orcamento.conta_receita    as ocr,\n";
    $stSql .=  "      orcamento.receita          as rec\n";
    $stSql .=  "where     ocr.cod_estrutural= '".$this->getDado( 'cod_estrutural')."'\n";
    $stSql .=  "      and ocr.exercicio = '".$this->getDado( 'exercicio' ) ."'\n";
    $stSql .=  "      and rec.cod_conta = ocr.cod_conta\n";
    $stSql .=  "      and rec.exercicio = ocr.exercicio\n";

    return $stSql;
}

function recuperaPrevisaoReceita(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPrevisaoReceita();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPrevisaoReceita()
{
    $stSql = "
               select receita.exercicio
                    , receita.cod_entidade
                    , receita.cod_receita
                    , recurso.cod_fonte
                    , receita.vl_original
                 from orcamento.receita
                 join orcamento.recurso as recurso
                   on recurso.exercicio = receita.exercicio
                  and recurso.cod_recurso = receita.cod_recurso
                where receita.cod_entidade IN (".$this->getDado( 'cod_entidade').")
                  and receita.exercicio = '".$this->getDado( 'exercicio')."'
             ";

    return $stSql;
}

function recuperaReceitaArrecadada(&$rsRecordSet)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaArrecada();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaArrecada()
{
    $stSql = "
select to_char(receita.dt_criacao, 'dd/mm/yyyy') as dt_criacao
      ,substring(conta_receita.cod_estrutural from 3 for 1 ) as cod_nat_receita
      ,recurso.cod_fonte
      ,receita.vl_original
 from orcamento.receita
 join orcamento.conta_receita
   on conta_receita.exercicio = receita.exercicio
  and conta_receita.cod_conta = receita.cod_conta
 join orcamento.recurso as recurso
   on recurso.exercicio = receita.exercicio
  and recurso.cod_recurso = receita.cod_recurso
where receita.cod_entidade IN (".$this->getDado( 'cod_entidade').")
  and receita.dt_criacao between to_date('".$this->getDado( 'dt_inicial')."','dd/mm/yyyy')
  and to_date('".$this->getDado( 'dt_final')."','dd/mm/yyyy')
  and receita.cod_entidade in (".$this->getDado( 'cod_entidade').")
  and receita.exercicio = '".$this->getDado( 'exercicio')."' ";

    return $stSql;
}

function recuperaClassReceitasOrcamentariasCredito(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaClassReceitasOrcamentariasCredito().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta Sql para recuperaRelacionamento
    * @access public
    * @return String $stSql
*/
function montaRecuperaClassReceitasOrcamentariasCredito()
{
    $stSql = "
       SELECT receita.cod_receita AS codigo
            , receita.exercicio
            , receita.cod_entidade
            , conta_receita.cod_estrutural
            , conta_receita.descricao AS desc
            , CASE
                WHEN (  (    SELECT receita_credito.cod_credito
                              FROM orcamento.receita_credito
                             WHERE receita_credito.cod_receita = receita.cod_receita
                               AND receita_credito.exercicio = receita.exercicio
                             LIMIT 1
                        ) IS NOT NULL
                     )
                 OR  (  (   SELECT 1
                              FROM orcamento.receita_credito_acrescimo
                             WHERE receita_credito_acrescimo.cod_receita = receita.cod_receita
                               AND receita_credito_acrescimo.exercicio = receita.exercicio
                             LIMIT 1
                        ) IS NOT NULL
                     )
                THEN
                    TRUE
                ELSE
                    FALSE
                 END AS possui_creditos
           , CASE WHEN (arrec.cod_estrutural IS NOT NULL) THEN
                       FALSE
                  ELSE
                       TRUE
                  END AS npossui_arrecadacao

        FROM orcamento.conta_receita
        JOIN orcamento.receita
          ON conta_receita.cod_conta = receita.cod_conta
         AND conta_receita.exercicio = receita.exercicio
         ";

        if (Sessao::getExercicio() > 2012) {
            $stSql .= "
            JOIN contabilidade.configuracao_lancamento_receita
              ON configuracao_lancamento_receita.cod_conta_receita = conta_receita.cod_conta
             AND configuracao_lancamento_receita.exercicio         = conta_receita.exercicio

            LEFT JOIN contabilidade.desdobramento_receita
              ON desdobramento_receita.cod_receita_secundaria = receita.cod_receita
             AND desdobramento_receita.exercicio              = receita.exercicio

            JOIN contabilidade.plano_conta
              ON plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta
             AND plano_conta.exercicio = configuracao_lancamento_receita.exercicio
             ";
        } else {
            $stSql .= "
            JOIN contabilidade.plano_conta
              ON plano_conta.cod_estrutural = '4.'||conta_receita.cod_estrutural
             AND plano_conta.exercicio = conta_receita.exercicio
         ";
        }

        $stSql .= "
        JOIN contabilidade.plano_analitica
          ON plano_analitica.cod_conta = plano_conta.cod_conta
         AND plano_analitica.exercicio = plano_conta.exercicio

              LEFT JOIN (  SELECT dr.exercicio , dr.cod_receita_secundaria
                        FROM contabilidade.desdobramento_receita AS dr,
                             orcamento.receita AS ore
                        WHERE    ore.cod_receita = dr.cod_receita_secundaria
                             AND ore.exercicio   = dr.exercicio
                        ) AS recsec ON (orcamento.receita.cod_receita = recsec.cod_receita_secundaria
                             AND orcamento.receita.exercicio   = recsec.exercicio )
              LEFT JOIN ( SELECT
                               orcamento.receita.exercicio,
                               orcamento.receita.cod_receita,
                               orcamento.conta_receita.cod_estrutural
                          FROM orcamento.receita
                              ,orcamento.conta_receita
                              ,tesouraria.arrecadacao_receita
                          WHERE
                               orcamento.receita.exercicio   = tesouraria.arrecadacao_receita.exercicio
                           AND orcamento.receita.cod_receita = tesouraria.arrecadacao_receita.cod_receita
                           AND orcamento.receita.exercicio   = orcamento.conta_receita.exercicio
                           AND orcamento.receita.cod_conta   = orcamento.conta_receita.cod_conta

                   AND EXISTS ( SELECT  boletim_lote_arrecadacao.exercicio
                                       ,boletim_lote_arrecadacao.cod_entidade
                                       ,boletim_lote_arrecadacao.timestamp_arrecadacao
                                       ,boletim_lote_arrecadacao.cod_arrecadacao
                                FROM tesouraria.boletim_lote_arrecadacao
                                WHERE   boletim_lote_arrecadacao.exercicio  = arrecadacao_receita.exercicio
                                    AND boletim_lote_arrecadacao.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                                    AND boletim_lote_arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                             )


                          GROUP BY
                               orcamento.receita.exercicio,
                               orcamento.receita.cod_receita,
                               orcamento.conta_receita.cod_estrutural
                        ) AS arrec  ON  (     orcamento.receita.exercicio   = arrec.exercicio
                             AND  orcamento.receita.cod_receita = arrec.cod_receita )


        WHERE recsec.cod_receita_secundaria IS null
          ";
        // filtro
        $stSql .= " AND receita.exercicio = '" . $this->getDado('exercicio') . "' ";

        if (Sessao::getExercicio() > 2012) {
            $stSql .= " AND configuracao_lancamento_receita.estorno = 'f'         \n";
            $stSql .= " AND desdobramento_receita.cod_receita_secundaria IS NULL  \n";
        }

        if ( ( $this->getDado('cod_estrutural_inicial') ) and ( ( $this->getDado('cod_estrutural_inicial') ) == ( $this->getDado('cod_estrutural_final') ) ) ) {
            $stSql .= " AND conta_receita.cod_estrutural like (publico.fn_mascarareduzida('" . $this->getDado('cod_estrutural_inicial') . "')||'%') \n";
        } else {
             if ( $this->getDado('cod_estrutural_inicial') and $this->getDado('cod_estrutural_final')  ) {
                $stSql .= " AND conta_receita.cod_estrutural BETWEEN '" . $this->getDado('cod_estrutural_inicial') . "' \n";
                $stSql .= " AND '" . $this->getDado('cod_estrutural_final') . "'";
            }

            if ( $this->getDado('inCodReceitaInicial') and $this->getDado('inCodReceitaFinal') ) {
                $stSql .= " AND orcamento.receita.cod_receita BETWEEN '" .$this->getDado('inCodReceitaInicial'). "' \n";
                $stSql .= " AND '" .$this->getDado('inCodReceitaFinal'). "' \n";
            }

        }

        return $stSql;
}

function recuperaClassReceitasCreditosOrcamentarios(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
        return $this->executaRecupera("montaRecuperaClassReceitasCreditosOrcamentarios",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaClassReceitasCreditosOrcamentarios()
{
    $stSql = "
       SELECT credito.cod_credito
            , credito.cod_especie
            , credito.cod_genero
            , credito.cod_natureza
            , credito.cod_credito || '.' || credito.cod_especie || '.' || credito.cod_genero || '.' || credito.cod_natureza as codigo
            , credito.descricao_credito AS desc
            , CASE WHEN (receita_credito.divida_ativa IS TRUE)
                   THEN '1'
                   ELSE '0'
              END AS divida_ativa
            , null as cod_acrescimo
            , 0 as cod_tipo
            , CASE WHEN (receita_credito.divida_ativa IS TRUE)
                   THEN 'Principal/Dívida Ativa'
                   ELSE 'Principal'
              END AS descricao_acrescimo
            , receita_credito_desconto.cod_receita_dedutora AS cod_dedutora
            , conta_receita_dedutora.descricao AS desc_dedutora
            , CASE WHEN (receita_credito_desconto.cod_receita IS NOT NULL)
                   THEN 't'
                   ELSE ''
              END AS dedutora
         FROM orcamento.conta_receita
            , orcamento.receita
         JOIN orcamento.receita_credito
           ON receita_credito.cod_receita = receita.cod_receita
          AND receita_credito.exercicio = receita.exercicio
         JOIN monetario.credito
           ON credito.cod_credito  = receita_credito.cod_credito
          AND credito.cod_especie  = receita_credito.cod_especie
          AND credito.cod_genero   = receita_credito.cod_genero
          AND credito.cod_natureza = receita_credito.cod_natureza
    LEFT JOIN orcamento.receita_credito_desconto
           ON receita_credito_desconto.exercicio = receita_credito.exercicio
          AND receita_credito_desconto.cod_especie = receita_credito.cod_especie
          AND receita_credito_desconto.cod_genero = receita_credito.cod_genero
          AND receita_credito_desconto.cod_natureza = receita_credito.cod_natureza
          AND receita_credito_desconto.cod_credito = receita_credito.cod_credito
          AND receita_credito_desconto.cod_receita = receita_credito.cod_receita
    LEFT JOIN orcamento.receita AS receita_dedutora
           ON receita_dedutora.cod_receita = receita_credito_desconto.cod_receita_dedutora
          AND receita_dedutora.exercicio = receita_credito_desconto.exercicio_dedutora
    LEFT JOIN orcamento.conta_receita AS conta_receita_dedutora
           ON conta_receita_dedutora.exercicio = receita_credito_desconto.exercicio_dedutora
          AND conta_receita_dedutora.cod_conta = receita_dedutora.cod_conta
        WHERE conta_receita.cod_conta = receita.cod_conta
          AND conta_receita.exercicio = receita.exercicio
    ";

    // filtro
    $stSql .= " AND receita.exercicio = '" . $this->getDado('exercicio') . "' ";
    if ( $this->getDado('codigo') ) {
        $stSql .= " AND receita.cod_receita = " . $this->getDado('codigo') . " \n";
    }

    $stSql .= "

        UNION

       SELECT credito.cod_credito
            , credito.cod_especie
            , credito.cod_genero
            , credito.cod_natureza
            , credito.cod_credito || '.' || credito.cod_especie || '.' || credito.cod_genero || '.' || credito.cod_natureza as codigo
            , credito.descricao_credito as desc
            , CASE WHEN (receita_credito_acrescimo.divida_ativa IS TRUE)
                   THEN '1'
                   ELSE '0'
              END AS divida_ativa
            , acrescimo.cod_acrescimo
            , acrescimo.cod_tipo
            , CASE WHEN (receita_credito_acrescimo.divida_ativa IS TRUE)
                   THEN acrescimo.descricao_acrescimo || '/Dívida Ativa'
                   ELSE acrescimo.descricao_acrescimo
              END AS descricao_acrescimo
            , null AS cod_dedutora
            , '' AS desc_dedutora
            , '' AS dedutora
         FROM orcamento.conta_receita
            , orcamento.receita
         JOIN orcamento.receita_credito_acrescimo
           ON receita_credito_acrescimo.cod_receita = receita.cod_receita
          AND receita_credito_acrescimo.exercicio = receita.exercicio
         JOIN monetario.credito
           ON credito.cod_credito  = receita_credito_acrescimo.cod_credito
          AND credito.cod_especie  = receita_credito_acrescimo.cod_especie
          AND credito.cod_genero   = receita_credito_acrescimo.cod_genero
          AND credito.cod_natureza = receita_credito_acrescimo.cod_natureza
         JOIN monetario.credito_acrescimo
           ON credito_acrescimo.cod_credito  = receita_credito_acrescimo.cod_credito
          AND credito_acrescimo.cod_especie  = receita_credito_acrescimo.cod_especie
          AND credito_acrescimo.cod_genero   = receita_credito_acrescimo.cod_genero
          AND credito_acrescimo.cod_natureza = receita_credito_acrescimo.cod_natureza
          AND credito_acrescimo.cod_acrescimo = receita_credito_acrescimo.cod_acrescimo
          AND credito_acrescimo.cod_tipo = receita_credito_acrescimo.cod_tipo
         JOIN monetario.acrescimo
           ON acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
          AND acrescimo.cod_tipo = credito_acrescimo.cod_tipo
        WHERE conta_receita.cod_conta = receita.cod_conta
          AND conta_receita.exercicio = receita.exercicio

    ";
    // filtro
    $stSql .= " AND receita.exercicio = '" . $this->getDado('exercicio') . "' ";
    if ( $this->getDado('codigo') ) {
        $stSql .= " AND receita.cod_receita = " . $this->getDado('codigo') . " \n";
    }

    return $stSql;
}

function recuperaLancamentosReceita(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaLancamentosReceita",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
function montaRecuperaLancamentosReceita()
{
    //Filtros da consulta
    $stFiltro = "";
    if ($this->getDado('cod_entidade') != '') {
        $stFiltro .= " AND receita.cod_entidade IN(".$this->getDado('cod_entidade').") ";
    }
    if ($this->getDado('exercicio') != '') {
        $stFiltro .= " AND receita.exercicio = '".$this->getDado('exercicio')."' ";
    }
    if ($this->getDado('cod_estrutural') != '') {
        $stFiltro .= " AND conta_receita.cod_estrutural = '".$this->getDado('cod_estrutural')."' ";
    }
    if ($this->getDado('dt_inicial') != '' AND $this->getDado('dt_final') != '') {
        $stFiltro .= " AND lote.dt_lote BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy') ";
    }

    $stSql = "
        SELECT cod_estrutural
             , SUM(vl_lancamento) AS vl_periodo
          FROM ( SELECT conta_receita.cod_estrutural
                      , SUM(valor_lancamento.vl_lancamento) * -1 AS vl_lancamento
                   FROM orcamento.receita
             INNER JOIN orcamento.conta_receita
                     ON receita.exercicio               = conta_receita.exercicio
                    AND receita.cod_conta               = conta_receita.cod_conta

             INNER JOIN contabilidade.lancamento_receita
                     ON receita.exercicio               = lancamento_receita.exercicio
                    AND receita.cod_receita             = lancamento_receita.cod_receita

             INNER JOIN contabilidade.lancamento
                     ON lancamento_receita.cod_lote     = lancamento.cod_lote
                    AND lancamento_receita.sequencia    = lancamento.sequencia
                    AND lancamento_receita.exercicio    = lancamento.exercicio
                    AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                    AND lancamento_receita.tipo         = lancamento.tipo

             INNER JOIN contabilidade.valor_lancamento
                     ON lancamento.exercicio            = valor_lancamento.exercicio
                    AND lancamento.sequencia            = valor_lancamento.sequencia
                    AND lancamento.cod_entidade         = valor_lancamento.cod_entidade
                    AND lancamento.cod_lote             = valor_lancamento.cod_lote
                    AND lancamento.tipo                 = valor_lancamento.tipo

             INNER JOIN contabilidade.lote
                     ON lancamento.cod_lote             = lote.cod_lote
                    AND lancamento.cod_entidade         = lote.cod_entidade
                    AND lancamento.exercicio            = lote.exercicio
                    AND lancamento.tipo                 = lote.tipo

                  WHERE lancamento_receita.estorno      = true
                    AND lancamento_receita.tipo         = 'A'
                    AND valor_lancamento.tipo_valor     = 'D'
                    ".$stFiltro."
               GROUP BY cod_estrutural

            UNION

                 SELECT conta_receita.cod_estrutural
                      , SUM(valor_lancamento.vl_lancamento) * -1 AS vl_lancamento
                   FROM orcamento.receita
             INNER JOIN orcamento.conta_receita
                     ON receita.exercicio = conta_receita.exercicio
                    AND receita.cod_conta = conta_receita.cod_conta

             INNER JOIN contabilidade.lancamento_receita
                     ON receita.exercicio               = lancamento_receita.exercicio
                    AND receita.cod_receita             = lancamento_receita.cod_receita

             INNER JOIN contabilidade.lancamento
                     ON lancamento_receita.cod_lote     = lancamento.cod_lote
                    AND lancamento_receita.sequencia    = lancamento.sequencia
                    AND lancamento_receita.exercicio    = lancamento.exercicio
                    AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                    AND lancamento_receita.tipo         = lancamento.tipo

             INNER JOIN contabilidade.valor_lancamento
                     ON lancamento.exercicio            = valor_lancamento.exercicio
                    AND lancamento.sequencia            = valor_lancamento.sequencia
                    AND lancamento.cod_entidade         = valor_lancamento.cod_entidade
                    AND lancamento.cod_lote             = valor_lancamento.cod_lote
                    AND lancamento.tipo                 = valor_lancamento.tipo

             INNER JOIN contabilidade.lote
                     ON lancamento.cod_lote             = lote.cod_lote
                    AND lancamento.cod_entidade         = lote.cod_entidade
                    AND lancamento.exercicio            = lote.exercicio
                    AND lancamento.tipo                 = lote.tipo

                  WHERE lancamento_receita.estorno      = false
                    AND lancamento_receita.tipo         = 'A'
                    AND valor_lancamento.tipo_valor     = 'C'
                    ".$stFiltro."
               GROUP BY cod_estrutural


                ) AS tb1
      GROUP BY tb1.cod_estrutural

    ";

    return $stSql;
}

function recuperaReceitaConfiguracaoLancamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " GROUP BY conta_receita.cod_estrutural
         , conta_receita.descricao
         , receita.exercicio
         , receita.cod_conta ";
    if ( !strstr($stCondicao, 'WHERE') ) {
        $stCondicao = ' WHERE '.substr($stCondicao, 4);
    }
    $stSql = $this->montaRecuperaReceitaConfiguracaoLancamento().$stCondicao.$stGroup.$stOrdem;
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaConfiguracaoLancamento()
{
    $stSql = "       SELECT conta_receita.cod_estrutural
                           , conta_receita.descricao
                           , receita.exercicio
                           , receita.cod_conta
                        FROM orcamento.receita
                  INNER JOIN orcamento.conta_receita
                          ON receita.cod_conta = conta_receita.cod_conta
                         AND receita.exercicio = conta_receita.exercicio ";

    return $stSql;
}

function recuperaReceitaExportacaoPlanejamento10(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaExportacaoPlanejamento10();
    $this->setDebug( $stSql);    
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaExportacaoPlanejamento10()
{
    $stSql = "
        SELECT tipo_registro
             , cod_receita_final AS cod_receita
             , cod_orgao
             , deducao_receita
             , identificador_deducao
             , CASE WHEN SUBSTR(natureza_receita::text, 1, 1) = '9'
                    THEN SUBSTR(natureza_receita::text, 2, 8)::integer
                    ELSE natureza_receita
                END AS natureza_receita
             , remove_acentos(especificacao) as especificacao
             , CASE WHEN SUBSTR(cod_receita_final::VARCHAR, 1, 1) = '9'
                    THEN REPLACE(REPLACE(sum(tabela.vl_previsto)::VARCHAR,'.',','),'-','')
                    ELSE REPLACE(sum(tabela.vl_previsto)::VARCHAR,'.',',')
                END AS vl_previsto
          FROM (
               SELECT 10::integer AS tipo_registro
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                           ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                                 END
                       END AS cod_receita_final
                    , configuracao_entidade.valor AS cod_orgao
                    , rec.masc_recurso_red AS recurso
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN 1
                           ELSE 2
                       END AS deducao_receita
                    , valores_identificadores.cod_identificador::integer AS identificador_deducao
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                           ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                                 END
                       END AS natureza_receita
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN (SELECT TRIM(o_cr.descricao)
                                   FROM orcamento.conta_receita AS o_cr
                                  WHERE o_cr.exercicio ='".Sessao::getExercicio()."'
                                    AND RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9),15,'0') = REPLACE(o_cr.cod_estrutural,'.',''))
                           ELSE (SELECT TRIM(descricao)
                                   FROM orcamento.conta_receita AS o_cr
                                  WHERE o_cr.exercicio ='".Sessao::getExercicio()."'
                                    AND RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8),14,'0') = REPLACE(o_cr.cod_estrutural,'.',''))
                       END AS especificacao
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN detalhamento_receitas.valor_previsto
                           ELSE ABS(detalhamento_receitas.valor_previsto)
                       END AS vl_previsto
                 FROM orcamento.receita

            LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') as rec 
                   ON rec.cod_recurso = receita.cod_recurso
                  AND rec.exercicio   = receita.exercicio                 
                 
                 JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio

                 JOIN administracao.configuracao_entidade
                   ON configuracao_entidade.cod_entidade = receita.cod_entidade
                  AND configuracao_entidade.exercicio = receita.exercicio
                  
                JOIN tcemg.fn_detalhamento_receitas('".Sessao::getExercicio()."','','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('entidades')."','','','','','','','') 
                  AS detalhamento_receitas (                      
                    cod_estrutural      varchar,                                           
                    receita             integer,                                           
                    recurso             varchar,                                           
                    descricao           varchar,                                           
                    valor_previsto      numeric,                                           
                    arrecadado_periodo  numeric,                                           
                    arrecadado_ano      numeric,                                           
                    diferenca           numeric                                           
                ) ON detalhamento_receitas.cod_estrutural = conta_receita.cod_estrutural \n";
    if ( Sessao::getExercicio() == '2014' ) {
        $stSql .= "    AND SUBSTR(detalhamento_receitas.cod_estrutural, 1, 1) != '9' \n";
    }
    $stSql .= "
            LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                   ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                  AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita

            LEFT JOIN tcemg.valores_identificadores
                   ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador

                 WHERE receita.exercicio = '".Sessao::getExercicio()."'
                   AND receita.cod_entidade IN (".$this->getDado('entidades').")
                   AND configuracao_entidade.cod_modulo = 55
                   AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                   AND receita.vl_original <> 0.00
             GROUP BY  cod_receita_final
                     , conta_receita.cod_estrutural
                     , conta_receita.descricao
                     , cod_orgao
                     , identificador_deducao
                     , detalhamento_receitas.valor_previsto
                     , rec.masc_recurso_red \n";
    
    if ( Sessao::getExercicio() == '2014' ) {
        $stSql .= "    
    
             UNION

               SELECT
                    10::integer AS tipo_registro
                    , SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer AS cod_receita_final
                    , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                    , rec.masc_recurso_red AS recurso
                    , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN 1
                           ELSE 2
                    END AS deducao_receita
                    , valores_identificadores.cod_identificador AS indentificador_deducao
                    , SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer AS natureza_receita
                    , TRIM(conta_receita.descricao) AS especificacao
                    , SUM(arrecadacao_receita_dedutora.vl_deducao) AS vl_previsto
    
                 FROM orcamento.receita
                 
            LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') as rec 
                   ON rec.cod_recurso = receita.cod_recurso
                  AND rec.exercicio   = receita.exercicio
    
                 JOIN tesouraria.arrecadacao_receita_dedutora
                   ON arrecadacao_receita_dedutora.cod_receita_dedutora=receita.cod_receita
                  AND arrecadacao_receita_dedutora.exercicio=receita.exercicio
                  AND arrecadacao_receita_dedutora.timestamp_arrecadacao::date BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dt_final')."', 'dd/mm/yyyy' )
    
                 JOIN administracao.configuracao_entidade
                   ON configuracao_entidade.cod_entidade = receita.cod_entidade
                  AND configuracao_entidade.exercicio = receita.exercicio
    
                 JOIN orcamento.conta_receita
                   ON conta_receita.cod_conta = receita.cod_conta
                  AND conta_receita.exercicio = receita.exercicio        
    
            LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                   ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                  AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
    
            LEFT JOIN tcemg.valores_identificadores
                   ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
    
                 WHERE receita.exercicio = '".Sessao::getExercicio()."'
                   AND receita.cod_entidade IN (".$this->getDado('entidades').")
                   AND configuracao_entidade.cod_modulo = 55
                   AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'

             GROUP BY receita.cod_receita
                     , receita.exercicio
                     , cod_orgao
                     , conta_receita.cod_estrutural
                     , conta_receita.descricao
                     , indentificador_deducao
                     , natureza_receita
                     , especificacao
                     , rec.masc_recurso_red \n ";
    }
    $stSql .= "    
               ) AS tabela
             WHERE tabela.vl_previsto<>0.00
              GROUP BY tipo_registro, cod_orgao, deducao_receita, identificador_deducao, natureza_receita, cod_receita, especificacao
              ORDER BY tabela.natureza_receita
    ";

    return $stSql;
}

function recuperaReceitaExportacaoPlanejamento11(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaExportacaoPlanejamento11();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaExportacaoPlanejamento11()
{
    $stSql = "
        SELECT 11 AS tipo_registro
             , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                    THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                           ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                                 END
                END AS cod_receita
             , receita.cod_recurso::integer AS cod_font_recursos
             , REPLACE(REPLACE(detalhamento_receitas.valor_previsto::VARCHAR,'.',','),'-','') AS vl_arrecadado_fonte
          FROM orcamento.receita
          JOIN orcamento.conta_receita
            ON conta_receita.cod_conta = receita.cod_conta
           AND conta_receita.exercicio = receita.exercicio
           
          JOIN (SELECT cod_estrutural
                     , receita
                     , recurso
                     , descricao
                     , sum(detalhamento.valor_previsto) as valor_previsto
                    
                 FROM
                    (
                          SELECT * FROM tcemg.fn_detalhamento_receitas('".Sessao::getExercicio()."','','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('entidades')."','','','','','','','')
                              AS detalhamento_receitas
                               (                      
                                 cod_estrutural      varchar,                                           
                                 receita             integer,                                           
                                 recurso             varchar,                                           
                                 descricao           varchar,                                           
                                 valor_previsto      numeric,                                           
                                 arrecadado_periodo  numeric,                                           
                                 arrecadado_ano      numeric,                                           
                                 diferenca           numeric                                           
                               ) \n";
if ( Sessao::getExercicio() == '2014' ) {
               $stSql .= "   WHERE SUBSTR(cod_estrutural, 1, 1) != '9'

    
                           UNION 
                        
                          SELECT conta_receita.cod_estrutural::varchar AS cod_estrutural
                               , receita.cod_receita AS receita
                               , rec.masc_recurso_red AS recurso
                               , TRIM(conta_receita.descricao)::varchar AS descricao
                               , 0.00::numeric AS valor_previsto
                               , SUM(arrecadacao_receita_dedutora.vl_deducao)::numeric AS valor_previsto
                               , 0.00::numeric AS arrecadado_ano
                               , 0.00::numeric AS diferenca

                            FROM orcamento.receita
        
                       LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') as rec 
                              ON rec.cod_recurso = receita.cod_recurso
                             AND rec.exercicio   = receita.exercicio
                        
                            JOIN tesouraria.arrecadacao_receita_dedutora
                              ON arrecadacao_receita_dedutora.cod_receita_dedutora=receita.cod_receita
                             AND arrecadacao_receita_dedutora.exercicio=receita.exercicio
                             AND arrecadacao_receita_dedutora.timestamp_arrecadacao::date BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dt_final')."', 'dd/mm/yyyy' )
            
                            JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = receita.cod_entidade
                             AND configuracao_entidade.exercicio = receita.exercicio
            
                            JOIN orcamento.conta_receita
                              ON conta_receita.cod_conta = receita.cod_conta
                             AND conta_receita.exercicio = receita.exercicio
              
            
                       LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                              ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
                             AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
            
                       LEFT JOIN tcemg.valores_identificadores
                              ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
            
                           WHERE receita.exercicio = '".Sessao::getExercicio()."'
                             AND receita.cod_entidade IN (".$this->getDado('entidades').")
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
        
                        GROUP BY receita.cod_receita
                               , receita.exercicio
                               , cod_estrutural
                               , conta_receita.descricao
                               , rec.masc_recurso_red \n";
            }
    $stSql .= "       )
                   AS detalhamento 
             GROUP BY cod_estrutural
                    , receita
                    , recurso
                    , descricao
               )
            AS detalhamento_receitas
            ON detalhamento_receitas.cod_estrutural = conta_receita.cod_estrutural
               
         WHERE receita.exercicio = '".Sessao::getExercicio()."'
           AND receita.cod_entidade IN (".$this->getDado('entidades').")
           AND receita.vl_original <> 0.00
           AND detalhamento_receitas.valor_previsto<>0.00
         GROUP BY receita.cod_receita
             , receita.cod_recurso
             , conta_receita.cod_estrutural
             , detalhamento_receitas.valor_previsto
         ORDER BY tipo_registro, cod_receita, cod_font_recursos
    ";

    return $stSql;
}

function recuperaLancamentosCreditosReceber(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLancamentosCreditosReceber();
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamentosCreditosReceber()
{
    $stSql = "
           SELECT
                  receita.exercicio
                , receita.cod_entidade
                , receita.vl_original
                , receita.cod_receita
                , conta_receita.descricao
                , conta_receita.cod_estrutural --Conta débito
                , plano_analitica.cod_plano
                , plano_conta.nom_conta
                , plano_conta.cod_estrutural AS cod_estrutural_plano
                , conta_receita.cod_conta
                , configuracao_lancamento_receita.cod_conta

               , ( select plano_conta.cod_estrutural
                     from contabilidade.plano_conta
                    where plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta
                      and plano_conta.exercicio = configuracao_lancamento_receita.exercicio ) as cod_estrutural_credito

               , ( select plano_analitica.cod_plano
                     from contabilidade.plano_conta
               inner join contabilidade.plano_analitica
                       on plano_analitica.cod_conta = plano_conta.cod_conta
                      and plano_analitica.exercicio = plano_conta.exercicio
                    where plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta
                      and plano_conta.exercicio = configuracao_lancamento_receita.exercicio ) as cod_plano_credito

              from orcamento.receita

        inner join orcamento.conta_receita
                on conta_receita.cod_conta = receita.cod_conta
               and conta_receita.exercicio = receita.exercicio

        inner join orcamento.receita_credito_tributario
                on receita_credito_tributario.cod_receita = receita.cod_receita
               and receita_credito_tributario.exercicio   = receita.exercicio

        inner join contabilidade.plano_analitica
                on plano_analitica.cod_conta = receita_credito_tributario.cod_conta
               and plano_analitica.exercicio = receita_credito_tributario.exercicio

        inner join contabilidade.plano_conta
                on plano_conta.cod_conta = receita_credito_tributario.cod_conta
               and plano_conta.exercicio = receita_credito_tributario.exercicio

        inner join contabilidade.configuracao_lancamento_receita
                on configuracao_lancamento_receita.cod_conta_receita = conta_receita.cod_conta
               and configuracao_lancamento_receita.exercicio         = conta_receita.exercicio
               and configuracao_lancamento_receita.estorno           = false

             WHERE receita.credito_tributario = true
               AND receita.cod_entidade = ".$this->getDado('cod_entidade')."
               AND receita.vl_original > 0
               AND receita.exercicio = '".Sessao::getExercicio()."'
    ";

    return $stSql;
}

    public function verificaClassificacaoReceita(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVerificaClassificacaoReceita();
        $this->setDebug( $stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
    }

    public function montaVerificaClassificacaoReceita()
    {
        $stSql = "
          SELECT *
            FROM orcamento.valida_receita('".$this->getDado('exercicio_classificacao')."','".$this->getDado('classificacao_receita')."')
              AS classificacao_receita ( cod_estrutural varchar
                                       , nivel integer
                                       , descricao VARCHAR
                                       , bo_validacao VARCHAR
                                       );
        ";
        return $stSql;
    }


}