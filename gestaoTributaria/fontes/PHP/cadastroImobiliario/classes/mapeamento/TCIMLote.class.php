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

     * Classe de mapeamento para a tabela IMOBILIARIO.LOTE
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMLote.class.php 59845 2014-09-15 19:32:00Z carolina $

     * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.LOTE
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMLote extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMLote()
{
    parent::Persistente();
    $this->setTabela('imobiliario.lote');

    $this->setCampoCod('cod_lote');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lote','integer',true,'',true,false);
    $this->AddCampo('dt_inscricao','date',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function mostraLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaMostraLote().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function mostraLoteParcelamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaMostraLoteParcelamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     L.COD_LOTE,                                             \n";
    $stSQL .= "     L.TIMESTAMP,                                            \n";
    $stSQL .= "     TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO,   \n";
    $stSQL .= "     AL.COD_GRANDEZA,                                        \n";
    $stSQL .= "     AL.COD_UNIDADE,                                         \n";
    $stSQL .= "     AL.AREA_REAL,                                           \n";
    $stSQL .= "     PM.VL_PROFUNDIDADE_MEDIA,                               \n";
    $stSQL .= "     LL.COD_LOCALIZACAO,                                     \n";
    $stSQL .= "     LL.VALOR,                                               \n";
    $stSQL .= "     LB.COD_BAIRRO,                                          \n";
    $stSQL .= "     LB.COD_UF,                                              \n";
    $stSQL .= "     LB.COD_MUNICIPIO,                                       \n";
    $stSQL .= "     LPA.validado,                                           \n";
    $stSQL .= "     LA.valor_composto                                       \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     imobiliario.vw_lote_ativo               AS L            \n";
    $stSQL .= " LEFT JOIN                                                   \n";
    $stSQL .= "     imobiliario.lote_parcelado              AS LPA          \n";
    $stSQL .= " ON                                                          \n";
    $stSQL .= "     L.cod_lote = LPA.cod_lote                               \n";
    $stSQL .= " JOIN  imobiliario.imovel_lote               AS IL           \n";
    $stSQL .= " ON  IL.cod_lote = L.cod_lote,                               \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual          AS AL,          \n";
    $stSQL .= "     imobiliario.profundidade_media          AS PM,          \n";
    $stSQL .= "     imobiliario.lote_localizacao            AS LL,          \n";
    $stSQL .= "     imobiliario.lote_bairro                 AS LB,          \n";
    $stSQL .= "     imobiliario.vw_localizacao_ativa        AS LA           \n";
    $stSQL .= " WHERE                                                       \n";
    $stSQL .= "     L.COD_LOTE = AL.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = PM.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = LL.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = LB.COD_LOTE AND                            \n";
    $stSQL .= "     LL.COD_LOCALIZACAO  = LA.COD_LOCALIZACAO AND            \n";
    $stSQL .= "    ( LPA.validado IS NULL OR LPA.validado = true )          \n";

    return $stSQL;
}

function recuperaRelacionamentoConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

//Otimização da consulta - GRIS - 04/01/2006
function montaRecuperaRelacionamentoConsulta()
{
    $stSQL  = "SELECT lote.cod_lote                                                                   \n";
    $stSQL .= "     , lote.timestamp                                                                  \n";
    $stSQL .= "     , to_char(lote.dt_inscricao,'dd/mm/yyyy')       AS dt_inscricao                   \n";
    $stSQL .= "     , lote_parcelado.validado                                                         \n";
    $stSQL .= "     , to_char(IBL.dt_inicio,'dd/mm/yyyy')    AS dt_baixa                              \n";
    $stSQL .= "     , to_char(IBL.dt_termino,'dd/mm/yyyy')   AS dt_termino                            \n";
    $stSQL .= "     , IBL.justificativa                                                               \n";
    $stSQL .= "     , profundidade_media.vl_profundidade_media                                        \n";
    $stSQL .= "     , lote_localizacao.cod_localizacao                                                \n";
    $stSQL .= "     , lote_bairro.cod_bairro                                                          \n";
    $stSQL .= "     , lote_bairro.cod_uf                                                              \n";
    $stSQL .= "     , lote_bairro.cod_municipio                                                       \n";
    $stSQL .= "     , lote_localizacao.valor                                                          \n";
    $stSQL .= "     , localizacao.codigo_composto                   AS valor_composto                 \n";
    $stSQL .= "     , area_lote.cod_grandeza                                                          \n";
    $stSQL .= "     , area_lote.cod_unidade                                                           \n";
    $stSQL .= "     , area_lote.area_real                                                             \n";
    $stSQL .= "  FROM imobiliario.lote                                                                \n";
    $stSQL .= "  LEFT JOIN (                                                                          \n";
    $stSQL .= "  SELECT ipm.*                                                                         \n";
    $stSQL .= "      FROM imobiliario.profundidade_media as ipm,    \n";
    $stSQL .= "           ( select cod_lote,  \n";
    $stSQL .= "                    MAX(timestamp)as timestamp   \n";
    $stSQL .= "               FROM imobiliario.profundidade_media  \n";
    $stSQL .= "              GROUP BY cod_lote  \n";
    $stSQL .= "           ) as mipm  \n";
    $stSQL .= "       WHERE ipm.cod_lote = mipm.cod_lote   \n";
    $stSQL .= "         AND ipm.timestamp = mipm.timestamp ) profundidade_media  \n";
    $stSQL .= " ON profundidade_media.cod_lote = lote.cod_lote   \n";
    $stSQL .= "       LEFT JOIN imobiliario.lote_parcelado ON lote.cod_lote = lote_parcelado.cod_lote \n";

    $stSQL .= "       LEFT JOIN (                                                                     \n";
    $stSQL .= "            SELECT                                                                     \n";
    $stSQL .= "                BAL.*                                                                  \n";
    $stSQL .= "            FROM                                                                       \n";
    $stSQL .= "                imobiliario.baixa_lote AS BAL,                                         \n";
    $stSQL .= "                (                                                                      \n";
    $stSQL .= "                SELECT                                                                 \n";
    $stSQL .= "                    MAX (TIMESTAMP) AS TIMESTAMP,                                      \n";
    $stSQL .= "                    cod_lote                                                           \n";
    $stSQL .= "                FROM                                                                   \n";
    $stSQL .= "                    imobiliario.baixa_lote                                             \n";
    $stSQL .= "                GROUP BY                                                               \n";
    $stSQL .= "                    cod_lote                                                           \n";
    $stSQL .= "                ) AS BL                                                                \n";
    $stSQL .= "            WHERE                                                                      \n";
    $stSQL .= "                BAL.cod_lote = BL.cod_lote AND                                         \n";
    $stSQL .= "                BAL.timestamp = BL.timestamp                                           \n";
    $stSQL .= "        ) IBL ON                                                                       \n";
    $stSQL .= "            IBL.cod_lote = lote.cod_lote                                               \n";

//    $stSQL .= "       LEFT JOIN imobiliario.baixa_lote     ON lote.cod_lote = baixa_lote.cod_lote     \n";

    $stSQL .= "     , imobiliario.lote_localizacao                                                    \n";
    $stSQL .= "     , imobiliario.lote_bairro                                                         \n";
    $stSQL .= "     , imobiliario.localizacao                                                         \n";

    $stSQL .= "     LEFT JOIN (                                                                       \n";
    $stSQL .= "          SELECT                                                                       \n";
    $stSQL .= "               BAL.*                                                                   \n";
    $stSQL .= "           FROM                                                                        \n";
    $stSQL .= "               imobiliario.baixa_localizacao AS BAL,                                   \n";
    $stSQL .= "               (                                                                       \n";
    $stSQL .= "               SELECT                                                                  \n";
    $stSQL .= "                   MAX (TIMESTAMP) AS TIMESTAMP,                                       \n";
    $stSQL .= "                   cod_localizacao                                                     \n";
    $stSQL .= "               FROM                                                                    \n";
    $stSQL .= "                   imobiliario.baixa_localizacao                                       \n";
    $stSQL .= "               GROUP BY                                                                \n";
    $stSQL .= "                   cod_localizacao                                                     \n";
    $stSQL .= "               ) AS BL                                                                 \n";
    $stSQL .= "           WHERE                                                                       \n";
    $stSQL .= "               BAL.cod_localizacao = BL.cod_localizacao AND                            \n";
    $stSQL .= "               BAL.timestamp = BL.timestamp                                            \n";
    $stSQL .= "       ) BL ON                                                                         \n";
    $stSQL .= "           BL.cod_localizacao = localizacao.cod_localizacao                            \n";

    $stSQL .= "     , ( SELECT area_lote.cod_lote                                                     \n";
    $stSQL .= "              , area_lote.cod_grandeza                                                 \n";
    $stSQL .= "              , area_lote.cod_unidade                                                  \n";
    $stSQL .= "              , area_lote.area_real                                                    \n";
    $stSQL .= "           FROM imobiliario.area_lote                                                  \n";
    $stSQL .= "              , ( SELECT area_lote.cod_lote, MAX(area_lote.timestamp) AS timestamp     \n";
    $stSQL .= "                    FROM imobiliario.area_lote                                         \n";
    $stSQL .= "                   GROUP BY area_lote.cod_lote ) as area_lote_max                      \n";
    $stSQL .= "          WHERE area_lote.cod_lote  = area_lote_max.cod_lote                           \n";
    $stSQL .= "            AND area_lote.timestamp = area_lote_max.timestamp                          \n";
    $stSQL .= "       ) AS area_lote                                                                  \n";
    $stSQL .= "  WHERE                 \n";
    $stSQL .= "     lote.cod_lote                     = lote_localizacao.cod_lote                  \n";
    $stSQL .= "    AND lote.cod_lote                     = lote_bairro.cod_lote                       \n";
    $stSQL .= "    AND lote_localizacao.cod_localizacao  = localizacao.cod_localizacao                \n";
    $stSQL .= "    AND lote.cod_lote                     = area_lote.cod_lote                         \n";
    $stSQL .= "    AND (lote_parcelado.validado IS NULL OR lote_parcelado.validado )                  \n";
    $stSQL .= "    AND (BL.cod_localizacao IS NULL OR BL.dt_termino IS NOT NULL)                      \n";

    return $stSQL;
}

function recuperaRelacionamentoLoteValidado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoLoteValidado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLoteValidado()
{
    $stSQL  = " SELECT                                                    \n";
    $stSQL .= "     L.COD_LOTE,                                           \n";
    $stSQL .= "     TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO, \n";
    $stSQL .= "     AL.COD_GRANDEZA,                                      \n";
    $stSQL .= "     AL.COD_UNIDADE,                                       \n";
    $stSQL .= "     AL.AREA_REAL,                                         \n";
    $stSQL .= "     PM.VL_PROFUNDIDADE_MEDIA,                             \n";
    $stSQL .= "     LL.COD_LOCALIZACAO,                                   \n";
    $stSQL .= "     LL.VALOR,                                             \n";
    $stSQL .= "     LB.COD_BAIRRO,                                        \n";
    $stSQL .= "     LB.COD_UF,                                            \n";
    $stSQL .= "     LB.COD_MUNICIPIO                                      \n";
    $stSQL .= " FROM                                                      \n";
    $stSQL .= "     imobiliario.vw_lote_ativo AS L,                           \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual AS AL,                     \n";
    $stSQL .= "     imobiliario.profundidade_media AS PM,                     \n";
    $stSQL .= "     imobiliario.lote_localizacao AS LL,                       \n";
    $stSQL .= "     imobiliario.lote_bairro AS LB                             \n";
    $stSQL .= " WHERE                                                     \n";
    $stSQL .= "     L.COD_LOTE = AL.COD_LOTE AND                          \n";
    $stSQL .= "     L.COD_LOTE = PM.COD_LOTE AND                          \n";
    $stSQL .= "     L.COD_LOTE = LL.COD_LOTE AND                          \n";
    $stSQL .= "     L.COD_LOTE = LB.COD_LOTE                              \n";

    return $stSQL;
}

function montaMostraLote()
{
    $stSQL  = " SELECT                                                    \n";
    $stSQL .= "     L.COD_LOTE,                                           \n";
    $stSQL .= "     TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO, \n";
    $stSQL .= "     AL.COD_GRANDEZA,                                      \n";
    $stSQL .= "     AL.COD_UNIDADE,                                       \n";
    $stSQL .= "     AL.AREA_REAL,                                         \n";
    $stSQL .= "     PM.VL_PROFUNDIDADE_MEDIA,                             \n";
    $stSQL .= "     LL.COD_LOCALIZACAO,                                   \n";
    $stSQL .= "     LL.VALOR,                                             \n";
    $stSQL .= "     LB.COD_BAIRRO,                                        \n";
    $stSQL .= "     LB.COD_UF,                                            \n";
    $stSQL .= "     LB.COD_MUNICIPIO,                                     \n";
    $stSQL .= "     LA.VALOR_COMPOSTO,                                    \n";
    $stSQL .= "     LPA.validado                                          \n";
    $stSQL .= " FROM                                                      \n";
    $stSQL .= "     imobiliario.vw_lote_ativo AS L                            \n";
    $stSQL .= " LEFT JOIN                                                 \n";
    $stSQL .= "     imobiliario.lote_parcelado AS LPA                         \n";
    $stSQL .= " ON                                                        \n";
    $stSQL .= "     L.cod_lote = LPA.cod_lote,                            \n";
    $stSQL .= "     imobiliario.lote_urbano AS LU,                            \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual AS AL,                     \n";
    $stSQL .= "     imobiliario.profundidade_media AS PM,                     \n";
    $stSQL .= "     imobiliario.lote_localizacao AS LL,                       \n";
    $stSQL .= "     imobiliario.lote_bairro AS LB,                            \n";
    $stSQL .= "     imobiliario.vw_localizacao_ativa AS LA                    \n";
    $stSQL .= " WHERE                                                     \n";
    $stSQL .= "     L.COD_LOTE = LU.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = AL.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = PM.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = LL.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = LB.COD_LOTE                AND           \n";
    $stSQL .= "     LL.COD_LOCALIZACAO = LA.COD_LOCALIZACAO AND           \n";
    $stSQL .= "    ( LPA.validado IS NULL OR LPA.validado = true )        \n";

    return $stSQL;
}

function montaMostraLoteParcelamento()
{
    $stSQL  = " SELECT                                                    \n";
    $stSQL .= "     L.COD_LOTE,                                           \n";
    $stSQL .= "     TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO, \n";
    $stSQL .= "     AL.COD_GRANDEZA,                                      \n";
    $stSQL .= "     AL.COD_UNIDADE,                                       \n";
    $stSQL .= "     AL.AREA_REAL,                                         \n";
    $stSQL .= "     PM.VL_PROFUNDIDADE_MEDIA,                             \n";
    $stSQL .= "     LL.COD_LOCALIZACAO,                                   \n";
    $stSQL .= "     LL.VALOR,                                             \n";
    $stSQL .= "     LB.COD_BAIRRO,                                        \n";
    $stSQL .= "     LB.COD_UF,                                            \n";
    $stSQL .= "     LB.COD_MUNICIPIO,                                     \n";
    $stSQL .= "     LA.VALOR_COMPOSTO                                    \n";
    $stSQL .= "                                          \n";
    $stSQL .= " FROM                                                      \n";
    $stSQL .= "     imobiliario.vw_lote_ativo AS L                            \n";
    $stSQL .= " INNER JOIN                                                 \n";
    $stSQL .= "      imobiliario.parcelamento_solo AS PAS        \n";
    $stSQL .= "                      \n";
    $stSQL .= " ON                                                        \n";
    $stSQL .= "     L.cod_lote = PAS.cod_lote,                            \n";
    $stSQL .= "     imobiliario.lote_urbano AS LU,                            \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual AS AL,                     \n";
    $stSQL .= "     imobiliario.profundidade_media AS PM,                     \n";
    $stSQL .= "     imobiliario.lote_localizacao AS LL,                       \n";
    $stSQL .= "     imobiliario.lote_bairro AS LB,                            \n";
    $stSQL .= "     imobiliario.vw_localizacao_ativa AS LA                    \n";
    $stSQL .= " WHERE                                                     \n";
    $stSQL .= "     L.COD_LOTE = LU.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = AL.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = PM.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = LL.COD_LOTE                AND           \n";
    $stSQL .= "     L.COD_LOTE = LB.COD_LOTE                AND           \n";
    $stSQL .= "     LL.COD_LOCALIZACAO = LA.COD_LOCALIZACAO           \n";
    

    return $stSQL;
}

function recuperaRelacionamentoProcesso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoProcesso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoProcesso()
{
    $stSQL  = " SELECT                                                      \n";
    $stSQL .= "     lp.cod_lote as cod_lote,                    \n";
    $stSQL .= "     lp.cod_processo as cod_processo,                        \n";
    $stSQL .= "     lp.ano_exercicio as ano_exercicio,                      \n";
    $stSQL .= "     lpad(lp.cod_processo::varchar,5,'0') || '/' || lp.ano_exercicio as cod_processo_ano,                      \n";
    $stSQL .= "     lp.timestamp as timestamp,                              \n";
    $stSQL .= "     to_char(lp.timestamp,'dd/mm/yyyy') as data,             \n";
    $stSQL .= "     to_char(lp.timestamp,'hh24:mi:ss') as hora                 \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "         imobiliario.lote_processo AS lp                       \n";

    return $stSQL;

}

function recuperaLoteProprietarios(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLoteProprietarios($stFiltro).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLoteProprietarios($stFiltro)
{
    $stSQL  = "    SELECT                                                                   \n";
    $stSQL .= "        P.numcgm                                                             \n";
    $stSQL .= "    FROM                                                                     \n";
    $stSQL .= "        imobiliario.fn_recupera_lote_proprietarios(".$stFiltro.") AS P   \n";
//  $stSQL .= "        ( numcgm integer, ordem integer )                                    \n";
    $stSQL .= "        ( numcgm integer )                                                   \n";

    return $stSQL;
}

function recuperaAreaEdificadaLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAreaEdificadaLote($stFiltro).$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAreaEdificadaLote($stFiltro)
{
    $stSQL .= "SELECT                                                            \n";
    $stSQL .= "      sum(imobiliario.fn_calcula_area_imovel( imovel_lote.inscricao_municipal )) AS area_lote   \n";
    $stSQL .= "  FROM imobiliario.imovel_lote                                                                   \n";
    $stSQL .= "     , ( SELECT imovel_lote.inscricao_municipal                                                  \n";
    $stSQL .= "              , MAX(imovel_lote.timestamp) AS timestamp                                          \n";
    $stSQL .= "           FROM imobiliario.imovel_lote                                                          \n";
    $stSQL .= "          WHERE imovel_lote.cod_lote = ".$stFiltro."                                             \n";
    $stSQL .= "          GROUP BY imovel_lote.inscricao_municipal                                               \n";
    $stSQL .= "        ) AS imovel_lote_max                                                                     \n";
    $stSQL .= "WHERE imovel_lote.inscricao_municipal = imovel_lote_max.inscricao_municipal                      \n";
    $stSQL .= "  AND imovel_lote.timestamp           = imovel_lote_max.timestamp                                \n";
    $stSQL .= "  AND imovel_lote.cod_lote            = ".$stFiltro."                                            \n";

    return $stSQL;
}

function recuperaEdificacoesLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEdificacoesLote().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEdificacoesLote()
{
    $stSql = "SELECT\n";
    $stSql .= "        il.cod_lote,\n";
    $stSql .= "        ic.cod_construcao,\n";
    $stSql .= "        ite.nom_tipo,\n";
    $stSql .= "        ii.inscricao_municipal,\n";
    $stSql .= "        lp.timestamp as timestamp_parcelamento,\n";
    $stSql .= "        iil.timestamp\n";
    $stSql .= "    FROM\n";
    $stSql .= "        imobiliario.lote as il\n";
    $stSql .= "        LEFT JOIN (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                BAT.*\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_lote AS BAT,\n";
    $stSql .= "                (\n";
    $stSql .= "                SELECT\n";
    $stSql .= "                    MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                    cod_lote\n";
    $stSql .= "                FROM\n";
    $stSql .= "                    imobiliario.baixa_lote\n";
    $stSql .= "                GROUP BY\n";
    $stSql .= "                    cod_lote\n";
    $stSql .= "                ) AS BT\n";
    $stSql .= "            WHERE\n";
    $stSql .= "                BAT.cod_lote = BT.cod_lote AND\n";
    $stSql .= "                BAT.timestamp = BT.timestamp \n";
    $stSql .= "        ) ibl\n";
    $stSql .= "        ON\n";
    $stSql .= "            ibl.cod_lote = il.cod_lote\n";

    $stSql .= "        INNER JOIN \n";
    $stSql .= "        (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                IIL.*\n";
    $stSql .= "            FROM \n";
    $stSql .= "                imobiliario.imovel_lote IIL,\n";
    $stSql .= "                (SELECT \n";
    $stSql .= "                    MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                    INSCRICAO_MUNICIPAL\n";
    $stSql .= "                FROM \n";
    $stSql .= "                    imobiliario.imovel_lote \n";
    $stSql .= "                GROUP BY \n";
    $stSql .= "                    INSCRICAO_MUNICIPAL \n";
    $stSql .= "                ) AS IL \n";
    $stSql .= "            WHERE \n";
    $stSql .= "                    IIL.INSCRICAO_MUNICIPAL = IL.INSCRICAO_MUNICIPAL \n";
    $stSql .= "                AND IIL.TIMESTAMP = IL.TIMESTAMP \n";
    $stSql .= "        ) AS IIL ON  \n";
    $stSql .= "        iil.cod_lote = il.cod_lote \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        imobiliario.imovel  as ii ON  ii.inscricao_municipal = iil.inscricao_municipal  \n";

    $stSql .= "    LEFT JOIN (\n";
    $stSql .= "        SELECT\n";
    $stSql .= "            BAT.*\n";
    $stSql .= "        FROM\n";
    $stSql .= "            imobiliario.baixa_imovel AS BAT,\n";
    $stSql .= "            (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                inscricao_municipal\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_imovel\n";
    $stSql .= "            GROUP BY\n";
    $stSql .= "                inscricao_municipal\n";
    $stSql .= "            ) AS BT\n";
    $stSql .= "        WHERE\n";
    $stSql .= "            BAT.inscricao_municipal = BT.inscricao_municipal AND\n";
    $stSql .= "            BAT.timestamp = BT.timestamp \n";
    $stSql .= "    ) ibi\n";
    $stSql .= "    ON\n";
    $stSql .= "        ibi.inscricao_municipal = ii.inscricao_municipal\n";

    $stSql .= "    INNER JOIN imobiliario.lote_parcelado  as lp ON \n";
    $stSql .= "        lp.cod_lote = il.cod_lote \n";
    $stSql .= "    INNER JOIN \n";
    $stSql .= "        imobiliario.unidade_autonoma as iua ON iua.inscricao_municipal = ii.inscricao_municipal \n";

    $stSql .= "    LEFT JOIN (\n";
    $stSql .= "        SELECT\n";
    $stSql .= "            BAT.*\n";
    $stSql .= "        FROM\n";
    $stSql .= "            imobiliario.baixa_unidade_autonoma AS BAT,\n";
    $stSql .= "            (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                inscricao_municipal,\n";
    $stSql .= "                cod_tipo,\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_unidade_autonoma\n";
    $stSql .= "            GROUP BY\n";
    $stSql .= "                inscricao_municipal,\n";
    $stSql .= "                cod_tipo,\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            ) AS BT\n";
    $stSql .= "        WHERE\n";
    $stSql .= "            BAT.inscricao_municipal = BT.inscricao_municipal AND\n";
    $stSql .= "            BAT.cod_tipo = BT.cod_tipo AND\n";
    $stSql .= "            BAT.cod_construcao = BT.cod_construcao AND\n";
    $stSql .= "            BAT.timestamp = BT.timestamp \n";
    $stSql .= "    ) ibua\n";
    $stSql .= "    ON\n";
    $stSql .= "        ibua.inscricao_municipal    = iua.inscricao_municipal   AND  \n";
    $stSql .= "        ibua.cod_tipo               = iua.cod_tipo              AND  \n";
    $stSql .= "        ibua.cod_construcao         = iua.cod_construcao\n";

    $stSql .= "    INNER JOIN \n";
    $stSql .= "        imobiliario.construcao_edificacao as ice ON ice.cod_construcao = iua.cod_construcao \n";
    $stSql .= "    INNER JOIN  imobiliario.construcao as ic ON ic.cod_construcao = ice.cod_construcao \n";

    $stSql .= "    LEFT JOIN (\n";
    $stSql .= "        SELECT\n";
    $stSql .= "            BAT.*\n";
    $stSql .= "        FROM\n";
    $stSql .= "            imobiliario.baixa_construcao AS BAT,\n";
    $stSql .= "            (\n";
    $stSql .= "            SELECT\n";
    $stSql .= "                MAX (TIMESTAMP) AS TIMESTAMP,\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            FROM\n";
    $stSql .= "                imobiliario.baixa_construcao\n";
    $stSql .= "            GROUP BY\n";
    $stSql .= "                cod_construcao\n";
    $stSql .= "            ) AS BT\n";
    $stSql .= "        WHERE\n";
    $stSql .= "            BAT.cod_construcao = BT.cod_construcao AND\n";
    $stSql .= "            BAT.timestamp = BT.timestamp \n";
    $stSql .= "    ) ibc \n";
    $stSql .= "    ON \n";
    $stSql .= "        ibc.cod_construcao = ic.cod_construcao\n";

    $stSql .= "    INNER JOIN  \n";
    $stSql .= "        imobiliario.tipo_edificacao as ite ON ite.cod_tipo = ice.cod_tipo \n";
    $stSql .= "    WHERE \n";
    $stSql .= "        ((ibl.dt_inicio IS NULL) OR (ibl.dt_inicio IS NOT NULL AND ibl.dt_termino IS NOT NULL) AND ibl.cod_lote = il.cod_lote) AND\n";

    $stSql .= "        ((ibi.dt_inicio IS NULL) OR (ibi.dt_inicio IS NOT NULL AND ibi.dt_termino IS NOT NULL) AND ibi.inscricao_municipal = ii.inscricao_municipal) AND\n";

    $stSql .= "        ((ibua.dt_inicio IS NULL) OR (ibua.dt_inicio IS NOT NULL AND ibua.dt_termino IS NOT NULL) AND ibua.inscricao_municipal = iua.inscricao_municipal AND ibua.cod_tipo = iua.cod_tipo AND ibua.cod_construcao = iua.cod_construcao ) AND\n";

    $stSql .= "        ((ibc.dt_inicio IS NULL) OR (ibc.dt_inicio IS NOT NULL AND ibc.dt_termino IS NOT NULL) AND ibc.cod_construcao = ic.cod_construcao)\n";

    return $stSql;
}

function recuperaAreaLoteOriginal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAreaLoteOriginal().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAreaLoteOriginal()
{
    $stSQL .= " SELECT * FROM imobiliario.area_lote WHERE  \n";

    return $stSQL;
}

function recuperaLotesParcelados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLotesParcelados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLotesParcelados()
{
    $stSQL .= " SELECT                                              \n";
    $stSQL .= "     LP.*,                                           \n";
    $stSQL .= "     AL.area_real                                    \n";
    $stSQL .= " FROM                                                \n";
    $stSQL .= "     imobiliario.lote_parcelado    LP,               \n";
    $stSQL .= "     imobiliario.parcelamento_solo PS,               \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual    AL                \n";
    $stSQL .= " WHERE                                               \n";
    $stSQL .= "        LP.cod_parcelamento = PS.cod_parcelamento    \n";
    $stSQL .= "    AND LP.validado = TRUE                           \n";
    $stSQL .= "    AND LP.cod_lote = AL.cod_lote                    \n";

    return $stSQL;
}
function recuperaImoveisLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImoveisLote().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaImoveisLote()
{
    $stSQL .= " SELECT                                              \n";
    $stSQL .= "     il.inscricao_municipal                          \n";
    $stSQL .= " FROM                                                \n";
    $stSQL .= "     imobiliario.lote l                              \n";
    $stSQL .= " INNER JOIN imobiliario.vw_max_imovel_lote il        \n";
    $stSQL .= "                        ON il.cod_lote = l.cod_lote  \n";

    return $stSQL;
}

function recuperaRelacionamentoLotesCadastrados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoLotesCadastrados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoLotesCadastrados()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     L.COD_LOTE,                                             \n";
    $stSQL .= "     L.TIMESTAMP,                                            \n";
    $stSQL .= "     TO_CHAR(L.DT_INSCRICAO,'dd/mm/yyyy') AS DT_INSCRICAO,   \n";
    $stSQL .= "     AL.COD_GRANDEZA,                                        \n";
    $stSQL .= "     AL.COD_UNIDADE,                                         \n";
    $stSQL .= "     AL.AREA_REAL,                                           \n";
    $stSQL .= "     PM.VL_PROFUNDIDADE_MEDIA,                               \n";
    $stSQL .= "     LL.COD_LOCALIZACAO,                                     \n";
    $stSQL .= "     LL.VALOR,                                               \n";
    $stSQL .= "     LB.COD_BAIRRO,                                          \n";
    $stSQL .= "     LB.COD_UF,                                              \n";
    $stSQL .= "     LB.COD_MUNICIPIO,                                       \n";
    $stSQL .= "     LPA.validado,                                           \n";
    $stSQL .= "     LA.valor_composto                                       \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "     imobiliario.vw_lote_ativo               AS L            \n";
    $stSQL .= " LEFT JOIN                                                   \n";
    $stSQL .= "     imobiliario.lote_parcelado              AS LPA          \n";
    $stSQL .= " ON                                                          \n";
    $stSQL .= "     L.cod_lote = LPA.cod_lote,                              \n";
    $stSQL .= "     imobiliario.vw_area_lote_atual          AS AL,          \n";
    $stSQL .= "     imobiliario.profundidade_media          AS PM,          \n";
    $stSQL .= "     imobiliario.lote_localizacao            AS LL,          \n";
    $stSQL .= "     imobiliario.lote_bairro                 AS LB,          \n";
    $stSQL .= "     imobiliario.vw_localizacao_ativa        AS LA           \n";
    $stSQL .= " WHERE                                                       \n";
    $stSQL .= "     L.COD_LOTE = AL.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = PM.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = LL.COD_LOTE AND                            \n";
    $stSQL .= "     L.COD_LOTE = LB.COD_LOTE AND                            \n";
    $stSQL .= "     LL.COD_LOCALIZACAO  = LA.COD_LOCALIZACAO AND            \n";
    $stSQL .= "    ( LPA.validado IS NULL OR LPA.validado = true )          \n";

    return $stSQL;
}

function recuperaLotesDesmembradosNaoValidados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLotesDesmembradosNaoValidados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaLotesDesmembradosNaoValidados()
{
    $stSQL  ="SELECT                                                                     \n";
    $stSQL .="    lote.cod_lote,                                                         \n";
    $stSQL .="    lote_parcelado.cod_parcelamento,                                       \n";
    $stSQL .="    lote_localizacao.valor,                                                \n";
    $stSQL .="    vw_localizacao_ativa.cod_localizacao,                                  \n";
    $stSQL .="    vw_localizacao_ativa.valor_composto,                                   \n";
    $stSQL .="    lote_rural.cod_lote as cod_lote_rual,                                  \n";
    $stSQL .="    lote_urbano.cod_lote as cod_lote_urbano                                \n";
    $stSQL .="FROM                                                                       \n";
    $stSQL .="    imobiliario.vw_lote_ativo AS lote                                      \n";
    $stSQL .="INNER JOIN                                                                 \n";
    $stSQL .="    imobiliario.lote_parcelado                                             \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="    lote.cod_lote = lote_parcelado.cod_lote                                \n";
    $stSQL .="INNER JOIN                                                                 \n";
    $stSQL .="    imobiliario.parcelamento_solo                                          \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="    lote.cod_lote = parcelamento_solo.cod_lote                             \n";
    $stSQL .="INNER JOIN                                                                 \n";
    $stSQL .="    imobiliario.lote_localizacao                                           \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="    lote.cod_lote = lote_localizacao.cod_lote                              \n";
    $stSQL .="INNER JOIN                                                                 \n";
    $stSQL .="    imobiliario.vw_localizacao_ativa                                       \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="    lote_localizacao.cod_localizacao = vw_localizacao_ativa.cod_localizacao\n";
    $stSQL .="LEFT JOIN                                                                  \n";
    $stSQL .="    imobiliario.lote_rural                                                 \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="     lote.cod_lote = lote_rural.cod_lote                                   \n";
    $stSQL .="LEFT JOIN                                                                  \n";
    $stSQL .="    imobiliario.lote_urbano                                                \n";
    $stSQL .="ON                                                                         \n";
    $stSQL .="     lote.cod_lote = lote_urbano.cod_lote                                  \n";
    $stSQL .="where                                                                      \n";
    $stSQL .="    parcelamento_solo.cod_tipo=2                                           \n";
    $stSQL .="    AND lote_parcelado.validado=false                                      \n";

    return $stSQL;
}

function recuperaListaConfrontacaoLote(&$rsRecordSet, $inCodLote = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaConfrontacaoLote($inCodLote);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaListaConfrontacaoLote($inCodLote)
{
    $stSQL = "
        SELECT DISTINCT L.COD_LOTE
             , LL.VALOR::INTEGER
          FROM imobiliario.vw_lote_ativo AS L
     LEFT JOIN imobiliario.lote_parcelado AS LPA
            ON L.cod_lote = LPA.cod_lote
    INNER JOIN imobiliario.lote_localizacao AS LL
            ON L.COD_LOTE = LL.COD_LOTE
    INNER JOIN imobiliario.vw_localizacao_ativa AS LA
            ON LL.COD_LOCALIZACAO = LA.COD_LOCALIZACAO
         WHERE ( LPA.validado IS NULL OR LPA.validado = true )
           AND LL.cod_localizacao IN ( SELECT DISTINCT cod_localizacao
                                         FROM imobiliario.lote_localizacao
                                        WHERE cod_lote = ".$inCodLote."
                                     )
           AND L.cod_lote != ".$inCodLote."
      ORDER BY LL.VALOR::INTEGER
    ";

    return $stSQL;
}

function recuperaListaConfrontacaoLocalizacao(&$rsRecordSet, $stCodLocalizacao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaConfrontacaoLocalizacao($stCodLocalizacao);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaListaConfrontacaoLocalizacao($stCodLocalizacao)
{
    $stSQL = "
        SELECT DISTINCT L.COD_LOTE
             , LL.VALOR::INTEGER
          FROM imobiliario.vw_lote_ativo AS L
     LEFT JOIN imobiliario.lote_parcelado AS LPA
            ON L.cod_lote = LPA.cod_lote
    INNER JOIN imobiliario.lote_localizacao AS LL
            ON L.COD_LOTE = LL.COD_LOTE
    INNER JOIN imobiliario.vw_localizacao_ativa AS LA
            ON LL.COD_LOCALIZACAO = LA.COD_LOCALIZACAO
         WHERE ( LPA.validado IS NULL OR LPA.validado = true )
           AND LL.cod_localizacao IN ( SELECT DISTINCT lote_localizacao.cod_localizacao
                                         FROM imobiliario.lote_localizacao
                                         JOIN imobiliario.localizacao
                                           ON localizacao.cod_localizacao = lote_localizacao.cod_localizacao
                                        WHERE localizacao.codigo_composto = '".$stCodLocalizacao."'
                                     )
      ORDER BY LL.VALOR::INTEGER
    ";

    return $stSQL;
}

}
