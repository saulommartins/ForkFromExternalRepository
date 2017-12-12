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
    * Classe de mapeamento da tabela TESOURARIA_ARRECADACAO
    * Data de Criação: 07/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-14 18:43:59 -0300 (Ter, 14 Ago 2007) $

    * Casos de uso: uc-02.04.04
    * 				uc-02.04.33
*/

/*
$Log$
Revision 1.24  2007/08/14 21:42:44  cako
Bug#9921#

Revision 1.23  2007/08/10 18:32:12  cako
Bug#9842#

Revision 1.22  2007/07/25 15:49:49  domluc
Add Verificação de  vinculo na Receita

Revision 1.21  2007/03/15 19:02:59  domluc
Caso de Uso 02.04.33

Revision 1.20  2006/10/23 16:34:51  domluc
Adicionado Debug do Sql

Revision 1.19  2006/09/01 16:56:14  jose.eduardo
uc-02.04.04

Revision 1.18  2006/07/14 17:58:24  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.17  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ARRECADACAO
  * Data de Criação: 07/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaArrecadacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaArrecadacao()
{
    parent::Persistente();
    $this->setTabela("tesouraria.arrecadacao");

    $this->setCampoCod('cod_arrecadacao');
    $this->setComplementoChave('exercicio,timestamp_arrecadacao');

    $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''  , true  , false );
    $this->AddCampo('exercicio'             , 'char'     , true, '4' , true  , true  );
    $this->AddCampo('timestamp_arrecadacao' , 'timestamp', false, ''  , true  , false );
    $this->AddCampo('cod_autenticacao'       , 'integer' , true, ''  , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'    , true, ''  , false , true  );
    $this->AddCampo('cod_boletim'           , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cod_terminal'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal'    , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'           , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'     , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cod_plano'             , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cod_entidade'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('observacao'            , 'text'     , true, ''  , false , false );
    $this->AddCampo('devolucao'             , 'boolean'  , false,''  , false , false );
}

function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT                                                                           \n";
    $stSql .= "     ar.numeracao,                                                                \n";
    $stSql .= "     ar.cod_terminal,                                                             \n";
    $stSql .= "     ar.timestamp_terminal,                                                       \n";
    $stSql .= "     ar.cgm_usuario,                                                              \n";
    $stSql .= "     cgm.nom_cgm,                                                                 \n";
    $stSql .= "     ar.timestamp_usuario,                                                        \n";
    $stSql .= "     ar.cod_plano,                                                                \n";
    $stSql .= "     pb.nom_conta,                                                                \n";
    $stSql .= "     ar.cod_entidade,                                                             \n";
    $stSql .= "     oe.nom_entidade,                                                             \n";
    $stSql .= "     ar.exercicio,                                                                \n";
    $stSql .= "     ar.timestamp_arrecadacao,                                                    \n";
    $stSql .= "     to_char(ar.timestamp_arrecadacao,'dd/mm/yyyy') as dt_arrecadacao,            \n";
    $stSql .= "     ar.observacao,                                                               \n";
    $stSql .= "     a.timestamp_abertura,                                                        \n";
    $stSql .= "     f.timestamp_fechamento                                                       \n";
    $stSql .= " FROM                                                                             \n";
    $stSql .= "     tesouraria.arrecadacao  as ar                                                \n";
    $stSql .= "         LEFT OUTER JOIN sw_cgm as cgm ON (                                       \n";
    $stSql .= "             ar.cgm_usuario = cgm.numcgm                                          \n";
    $stSql .= "         )                                                                        \n";
    $stSql .= "         LEFT OUTER JOIN (                                                        \n";
    $stSql .= "             SELECT                                                               \n";
    $stSql .= "                 oe.cod_entidade,                                                 \n";
    $stSql .= "                 oe.exercicio,                                                    \n";
    $stSql .= "                 sw_cgm.nom_cgm as nom_entidade                                   \n";
    $stSql .= "             FROM                                                                 \n";
    $stSql .= "                 orcamento.entidade  as oe,                                       \n";
    $stSql .= "                 sw_cgm                                                           \n";
    $stSql .= "             WHERE                                                                \n";
    $stSql .= "                 oe.numcgm   = sw_cgm.numcgm                                      \n";
    $stSql .= "         ) as oe ON (                                                             \n";
    $stSql .= "             oe.cod_entidade = ar.cod_entidade AND                                \n";
    $stSql .= "             oe.exercicio    = ar.exercicio                                       \n";
    $stSql .= "         )                                                                        \n";
    $stSql .= "         LEFT OUTER JOIN (                                                        \n";
    $stSql .= "             SELECT                                                               \n";
    $stSql .= "                 pb.cod_plano,                                                    \n";
    $stSql .= "                 pb.exercicio,                                                    \n";
    $stSql .= "                 pc.nom_conta                                                     \n";
    $stSql .= "             FROM                                                                 \n";
    $stSql .= "                 contabilidade.plano_banco       as pb,                           \n";
    $stSql .= "                 contabilidade.plano_analitica   as pa,                           \n";
    $stSql .= "                 contabilidade.plano_conta       as pc                            \n";
    $stSql .= "             WHERE                                                                \n";
    $stSql .= "                 pb.cod_plano    = pa.cod_plano  AND                              \n";
    $stSql .= "                 pb.exercicio    = pa.exercicio  AND                              \n";
    $stSql .= "                                                                                  \n";
    $stSql .= "                 pa.cod_conta    = pc.cod_conta  AND                              \n";
    $stSql .= "                 pa.exercicio    = pc.exercicio                                   \n";
    $stSql .= "         ) as pb ON (                                                             \n";
    $stSql .= "             pb.cod_plano    = ar.cod_plano  AND                                  \n";
    $stSql .= "             pb.exercicio    = ar.exercicio                                       \n";
    $stSql .= "         )                                                                        \n";
    $stSql .= "     ,                                                                            \n";
    $stSql .= "     tesouraria.usuario_terminal as ut,                                           \n";
    $stSql .= "     tesouraria.terminal as t,                                                    \n";
    $stSql .= "     tesouraria.abertura as a                                                     \n";
    $stSql .= "         LEFT OUTER JOIN tesouraria.fechamento as f ON (                          \n";
    $stSql .= "             a.cod_terminal          = f.cod_terminal        AND                  \n";
    $stSql .= "             a.exercicio             = f.exercicio           AND                  \n";
    $stSql .= "             a.timestamp_terminal    = f.timestamp_terminal  AND                  \n";
    $stSql .= "             a.cgm_usuario           = f.cgm_usuario         AND                  \n";
    $stSql .= "             a.timestamp_usuario     = f.timestamp_usuario   AND                  \n";
    $stSql .= "             a.timestamp_abertura    = f.timestamp_abertura                       \n";
    $stSql .= "         )                                                                        \n";
    $stSql .= " WHERE                                                                            \n";
    $stSql .= "     ar.cod_terminal         = ut.cod_terminal       AND                          \n";
    $stSql .= "     ar.timestamp_terminal   = ut.timestamp_terminal AND                          \n";
    $stSql .= "     ar.cgm_usuario          = ut.cgm_usuario        AND                          \n";
    $stSql .= "     ar.timestamp_usuario    = ut.timestamp_usuario  AND                          \n";
    $stSql .= "                                                                                  \n";
    $stSql .= "     ar.cod_terminal         = a.cod_terminal       AND                           \n";
    $stSql .= "     ar.timestamp_terminal   = a.timestamp_terminal AND                           \n";
    $stSql .= "     ar.cgm_usuario          = a.cgm_usuario        AND                           \n";
    $stSql .= "     ar.timestamp_usuario    = a.timestamp_usuario  AND                           \n";
    $stSql .= "     ar.timestamp_abertura   = a.timestamp_abertura AND                           \n";
    $stSql .= "                                                                                  \n";
    $stSql .= "     ut.cod_terminal         = t.cod_terminal        AND                          \n";
    $stSql .= "     ut.exercicio            = t.exercicio           AND                          \n";
    $stSql .= "     ut.timestamp_terminal   = t.timestamp_terminal  AND                          \n";
    $stSql .= "                                                                                  \n";
    $stSql .= "     ut.cod_terminal         = a.cod_terminal        AND                          \n";
    $stSql .= "     ut.exercicio            = a.exercicio           AND                          \n";
    $stSql .= "     ut.timestamp_terminal   = a.timestamp_terminal  AND                          \n";
    $stSql .= "     ut.cgm_usuario          = a.cgm_usuario         AND                          \n";
    $stSql .= "     ut.timestamp_usuario    = a.timestamp_usuario   AND                          \n";
    $stSql .= "                                                                                  \n";

    $stSql .= "     NOT EXISTS ( SELECT 1                                                        \n";
    $stSql .= "                    FROM tesouraria.arrecadacao_estornada t_ae                    \n";
    $stSql .= "                   WHERE t_ae.numeracao = ar.numeracao                            \n";
    $stSql .= "                ) AND                                                             \n";
//     $stSql .= "     ar.numeracao NOT IN (                                                        \n";
//     $stSql .= "         SELECT                                                                   \n";
//     $stSql .= "             numeracao                                                            \n";
//     $stSql .= "         FROM                                                                     \n";
//     $stSql .= "             tesouraria.arrecadacao_estornada                                     \n";
//     $stSql .= "     ) AND                                                                        \n";

    $stSql .= "     NOT EXISTS ( SELECT 1                                                        \n";
    $stSql .= "                    FROM tesouraria.terminal_desativado t_td                      \n";
    $stSql .= "                   WHERE t_td.cod_terminal       = t.cod_terminal                 \n";
    $stSql .= "                     AND t_td.exercicio          = t.exercicio                    \n";
    $stSql .= "                     AND t_td.timestamp_terminal = t.timestamp_terminal           \n";
    $stSql .= "                )                                                                 \n";
//     $stSql .= "     t.cod_terminal || '-' || t.exercicio || '-' || t.timestamp_terminal NOT IN ( \n";
//     $stSql .= "         SELECT                                                                   \n";
//     $stSql .= "             cod_terminal || '-' || exercicio || '-' || timestamp_terminal        \n";
//     $stSql .= "         FROM                                                                     \n";
//     $stSql .= "             tesouraria.terminal_desativado                                       \n";
//     $stSql .= "     )                                                                            \n";
    return $stSql;
}

function montaRecuperaArrecadacaoNaoEstornada()
{
    $stSql .= "SELECT                                                                                           \n";
    $stSql .= "    TA.cod_arrecadacao,                                                                          \n";
    $stSql .= "    TA.exercicio,                                                                                \n";
    $stSql .= "    TA.timestamp_arrecadacao,                                                                    \n";
    $stSql .= "    TA.cod_autenticacao,                                                                         \n";
    $stSql .= "    TA.dt_autenticacao,                                                                          \n";
    $stSql .= "    TA.cod_boletim,                                                                              \n";
    $stSql .= "    TA.cod_terminal,                                                                             \n";
    $stSql .= "    TA.timestamp_terminal,                                                                       \n";
    $stSql .= "    TA.cgm_usuario,                                                                              \n";
    $stSql .= "    TA.timestamp_usuario,                                                                        \n";
    $stSql .= "    TA.cod_plano,                                                                                \n";
    $stSql .= "    TA.cod_entidade,                                                                             \n";
    $stSql .= "    TA.observacao,                                                                               \n";
    $stSql .= "    TAC.numeracao,                                                                               \n";
    $stSql .= "    TAR.cod_receita,                                                                             \n";
    $stSql .= "    TAR.cod_receita_dedutora,                                                                    \n";
    $stSql .= "    TAR.vl_arrecadacao,                                                                          \n";
    $stSql .= "    TAR.vl_deducao,                                                                              \n";
    $stSql .= "    TO_CHAR( TA.timestamp_arrecadacao, 'dd/mm/yyyy' ) as dt_arrecadacao,                         \n";
    $stSql .= "    TAR.descricao                                                                                \n";
    $stSql .= "FROM                                                                                             \n";
    $stSql .= "    tesouraria.arrecadacao AS TA                                                                 \n";
    $stSql .= "        LEFT JOIN tesouraria.arrecadacao_estornada AS TAE ON (                                   \n";
    $stSql .= "            TA.cod_arrecadacao          = TAE.cod_arrecadacao AND                                \n";
    $stSql .= "            TA.exercicio                = TAE.exercicio      AND                                 \n";
    $stSql .= "            TA.timestamp_arrecadacao    = TAE.timestamp_arrecadacao                              \n";
    $stSql .= "        )                                                                                        \n";
    $stSql .= "        LEFT JOIN tesouraria.arrecadacao_carne AS TAC ON(                                        \n";
    $stSql .= "            TA.exercicio                = TAC.exercicio         AND                              \n";
    $stSql .= "            TA.cod_arrecadacao          = TAC.cod_arrecadacao   AND                              \n";
    $stSql .= "            TA.timestamp_arrecadacao    = TAC.timestamp_arrecadacao                              \n";
    $stSql .= "        )                                                                                        \n";
    $stSql .= "        LEFT JOIN (                                                                              \n";
    $stSql .= "            SELECT                                                                               \n";
    $stSql .= "                TAR.exercicio,                                                                   \n";
    $stSql .= "                TAR.cod_arrecadacao,                                                             \n";
    $stSql .= "                TAR.timestamp_arrecadacao,                                                       \n";
    $stSql .= "                TAR.cod_receita,                                                                 \n";
    $stSql .= "                TAR.vl_arrecadacao,                                                              \n";
    $stSql .= "                TARD.cod_receita_dedutora,                                                       \n";
    $stSql .= "                TARD.vl_deducao,                                                                 \n";
    $stSql .= "                OCR.descricao                                                                    \n";
    $stSql .= "            FROM                                                                                 \n";
    $stSql .= "                tesouraria.arrecadacao_receita as TAR                                            \n";
    $stSql .= "                LEFT JOIN tesouraria.arrecadacao_receita_dedutora AS TARD ON(                    \n";
    $stSql .= "                    TAR.exercicio                = TARD.exercicio       AND                      \n";
    $stSql .= "                    TAR.cod_arrecadacao          = TARD.cod_arrecadacao AND                      \n";
    $stSql .= "                    TAR.cod_receita              = TARD.cod_receita     AND                      \n";
    $stSql .= "                    TAR.timestamp_arrecadacao    = TARD.timestamp_arrecadacao                    \n";
    $stSql .= "                )                                                                                \n";
    $stSql .= "                ,orcamento.receita       AS RECEITA                                              \n";
    $stSql .= "                ,orcamento.conta_receita AS OCR                                                  \n";
    $stSql .= "            WHERE                                                                                \n";
    $stSql .= "                 TAR.cod_receita         = RECEITA.cod_receita AND                               \n";
    $stSql .= "                 TAR.exercicio           = RECEITA.exercicio   AND                               \n";
    $stSql .= "                 RECEITA.cod_conta       = OCR.cod_conta       AND                               \n";
    $stSql .= "                 RECEITA.exercicio       = OCR.exercicio                                         \n";
    $stSql .= "        ) AS TAR ON(                                                                             \n";
    $stSql .= "            TA.exercicio                = TAR.exercicio         AND                              \n";
    $stSql .= "            TA.cod_arrecadacao          = TAR.cod_arrecadacao   AND                              \n";
    $stSql .= "            TA.timestamp_arrecadacao    = TAR.timestamp_arrecadacao                              \n";
    $stSql .= "        )                                                                                        \n";
    $stSql .= "    ,tesouraria.boletim      AS TB                                                               \n";
    $stSql .= "WHERE                                                                                            \n";
    $stSql .= "    TAE.cod_arrecadacao     IS NULL               AND                                            \n";
    $stSql .= "    TA.cod_boletim          = TB.cod_boletim      AND                                            \n";
    $stSql .= "    TA.exercicio            = TB.exercicio                                                       \n";

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
function recuperaArrecadacaoNaoEstornada(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaArrecadacaoNaoEstornada().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaArrecadacaoNaoEstornadaReceita()
{
    $stSql  = "SELECT *                                                                                           \n";
    $stSql .= "FROM (                                                                                             \n";
    $stSql .= "     SELECT                                                                                        \n";
    $stSql .= "          TA.cod_arrecadacao                                                                       \n";
    $stSql .= "         ,TA.exercicio                                                                             \n";
    $stSql .= "         ,TA.timestamp_arrecadacao                                                                 \n";
    $stSql .= "         ,TA.cod_autenticacao                                                                      \n";
    $stSql .= "         ,TA.dt_autenticacao                                                                       \n";
    $stSql .= "         ,TA.cod_boletim                                                                           \n";
    $stSql .= "         ,TB.dt_boletim                                                                            \n";
    $stSql .= "         ,TA.cod_terminal                                                                          \n";
    $stSql .= "         ,TA.timestamp_terminal                                                                    \n";
    $stSql .= "         ,TA.cgm_usuario                                                                           \n";
    $stSql .= "         ,TA.timestamp_usuario                                                                     \n";
    $stSql .= "         ,TA.cod_plano                                                                             \n";
    $stSql .= "         ,TA.cod_entidade                                                                          \n";
    $stSql .= "         ,TA.observacao                                                                            \n";
    $stSql .= "         ,TAER.cod_receita                                                                         \n";
    $stSql .= "         ,TAER.vl_arrecadacao                                                                      \n";
    $stSql .= "         ,TAER.vl_estornado                                                                        \n";
    $stSql .= "         ,TARDE.cod_receita_dedutora                                                               \n";
    $stSql .= "         ,TARDE.vl_deducao                                                                         \n";
    $stSql .= "         ,TARDE.vl_deducao_estornado                                                               \n";
    $stSql .= "         ,TO_CHAR( TA.timestamp_arrecadacao, 'dd/mm/yyyy' ) as dt_arrecadacao                      \n";
    $stSql .= "         ,TAER.descricao                                                                           \n"; 
    $stSql .= "         ,lancamento_baixa_patrimonio_alienacao.cod_bem                                           \n";
    $stSql .= "     FROM                                                                                          \n";
    $stSql .= "         tesouraria.arrecadacao AS TA                                                              \n";
    $stSql .= "         INNER JOIN tesouraria.arrecadacao_estornada AS TAE ON (                                   \n";
    $stSql .= "                 TA.cod_arrecadacao          = TAE.cod_arrecadacao                                 \n";
    $stSql .= "             AND TA.exercicio                = TAE.exercicio                                       \n";
    $stSql .= "             AND TA.timestamp_arrecadacao    = TAE.timestamp_arrecadacao                           \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "         LEFT JOIN (                                                                               \n";
    $stSql .= "             SELECT                                                                                \n";
    $stSql .= "                  TAR.exercicio                                                                    \n";
    $stSql .= "                 ,TAR.cod_arrecadacao                                                              \n";
    $stSql .= "                 ,TAR.timestamp_arrecadacao                                                        \n";
    $stSql .= "                 ,TAR.cod_receita                                                                  \n";
    $stSql .= "                 ,TAR.vl_arrecadacao                                                               \n";
    $stSql .= "                 ,sum(coalesce(TAER.vl_estornado,0.00)) as vl_estornado                            \n";
    $stSql .= "                 ,OCR.descricao                                                                    \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 tesouraria.arrecadacao_receita as TAR                                             \n";
    $stSql .= "                 LEFT JOIN tesouraria.arrecadacao_estornada_receita AS TAER ON(                    \n";
    $stSql .= "                         TAR.exercicio                = TAER.exercicio                             \n";
    $stSql .= "                     AND TAR.cod_arrecadacao          = TAER.cod_arrecadacao                       \n";
    $stSql .= "                     AND TAR.cod_receita              = TAER.cod_receita                           \n";
    $stSql .= "                     AND TAR.timestamp_arrecadacao    = TAER.timestamp_arrecadacao                 \n";
    $stSql .= "                 )                                                                                 \n";
    $stSql .= "                 ,orcamento.receita       AS RECEITA                                               \n";
    $stSql .= "                 ,orcamento.conta_receita AS OCR                                                   \n";
    $stSql .= "             WHERE                                                                                 \n";
    $stSql .= "                     TAR.cod_receita         = RECEITA.cod_receita                                 \n";
    $stSql .= "                 AND TAR.exercicio           = RECEITA.exercicio                                   \n";
    $stSql .= "                 AND RECEITA.cod_conta       = OCR.cod_conta                                       \n";
    $stSql .= "                 AND RECEITA.exercicio       = OCR.exercicio                                       \n";
    $stSql .= "             GROUP BY                                                                              \n";
    $stSql .= "                  TAR.exercicio                                                                    \n";
    $stSql .= "                 ,TAR.cod_arrecadacao                                                              \n";
    $stSql .= "                 ,TAR.timestamp_arrecadacao                                                        \n";
    $stSql .= "                 ,TAR.cod_receita                                                                  \n";
    $stSql .= "                 ,TAR.vl_arrecadacao                                                               \n";
    $stSql .= "                 ,OCR.descricao                                                                    \n";
    $stSql .= "             HAVING                                                                                \n";
    $stSql .= "                 TAR.vl_arrecadacao > sum(coalesce(TAER.vl_estornado,0.00))                        \n";
    $stSql .= "         ) AS TAER ON(                                                                             \n";
    $stSql .= "                 TA.exercicio                = TAER.exercicio                                      \n";
    $stSql .= "             AND TA.cod_arrecadacao          = TAER.cod_arrecadacao                                \n";
    $stSql .= "             AND TA.timestamp_arrecadacao    = TAER.timestamp_arrecadacao                          \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "         LEFT JOIN (                                                                               \n";
    $stSql .= "             SELECT                                                                                \n";
    $stSql .= "                  TAR.exercicio                                                                    \n";
    $stSql .= "                 ,TAR.cod_arrecadacao                                                              \n";
    $stSql .= "                 ,TAR.timestamp_arrecadacao                                                        \n";
    $stSql .= "                 ,TAR.cod_receita                                                                  \n";
    $stSql .= "                 ,TAR.vl_arrecadacao                                                               \n";
    $stSql .= "                 ,TARD.cod_receita_dedutora                                                        \n";
    $stSql .= "                 ,TARD.vl_deducao                                                                  \n";
    $stSql .= "                 ,sum(coalesce(TARDE.vl_estornado,0.00)) as vl_deducao_estornado                   \n";
    $stSql .= "             FROM                                                                                  \n";
    $stSql .= "                 tesouraria.arrecadacao_receita as TAR                                             \n";
    $stSql .= "                 JOIN tesouraria.arrecadacao_receita_dedutora AS TARD ON(                     \n";
    $stSql .= "                         TAR.exercicio                = TARD.exercicio                             \n";
    $stSql .= "                     AND TAR.cod_arrecadacao          = TARD.cod_arrecadacao                       \n";
    $stSql .= "                     AND TAR.cod_receita              = TARD.cod_receita                           \n";
    $stSql .= "                     AND TAR.timestamp_arrecadacao    = TARD.timestamp_arrecadacao                 \n";
    $stSql .= "                 )                                                                                 \n";
    $stSql .= "                 LEFT JOIN tesouraria.arrecadacao_receita_dedutora_estornada AS TARDE ON(         \n";
    $stSql .= "                         TARD.exercicio                = TARDE.exercicio                           \n";
    $stSql .= "                     AND TARD.cod_arrecadacao          = TARDE.cod_arrecadacao                     \n";
    $stSql .= "                     AND TARD.cod_receita              = TARDE.cod_receita                         \n";
    $stSql .= "                     AND TARD.timestamp_arrecadacao    = TARDE.timestamp_arrecadacao               \n";
    $stSql .= "                     AND TARD.cod_receita_dedutora     = TARDE.cod_receita_dedutora                \n";
    $stSql .= "                 )                                                                                 \n";
    $stSql .= "             GROUP BY                                                                              \n";
    $stSql .= "                  TAR.exercicio                                                                    \n";
    $stSql .= "                 ,TAR.cod_arrecadacao                                                              \n";
    $stSql .= "                 ,TAR.timestamp_arrecadacao                                                        \n";
    $stSql .= "                 ,TAR.cod_receita                                                                  \n";
    $stSql .= "                 ,TAR.vl_arrecadacao                                                               \n";
    $stSql .= "                 ,TARD.cod_receita_dedutora                                                        \n";
    $stSql .= "                 ,TARD.vl_deducao                                                                  \n";
    $stSql .= "             HAVING                                                                                \n";
    $stSql .= "                 TARD.vl_deducao > sum(coalesce(TARDE.vl_estornado,0.00))                          \n";
    $stSql .= "         ) AS TARDE ON(                                                                            \n";
    $stSql .= "                 TA.exercicio                = TARDE.exercicio                                     \n";
    $stSql .= "             AND TA.cod_arrecadacao          = TARDE.cod_arrecadacao                               \n";
    $stSql .= "             AND TA.timestamp_arrecadacao    = TARDE.timestamp_arrecadacao                         \n";
    $stSql .= "         )                                                                                         \n";
    $stSql .= "
                 LEFT JOIN contabilidade.lancamento_baixa_patrimonio_alienacao
                        ON TA.cod_arrecadacao       = lancamento_baixa_patrimonio_alienacao.cod_arrecadacao
                       AND TA.exercicio             = lancamento_baixa_patrimonio_alienacao.exercicio_arrecadacao
                       AND TA.timestamp_arrecadacao = lancamento_baixa_patrimonio_alienacao.timestamp_arrecadacao \n ";
    $stSql .= "         , tesouraria.boletim      AS TB                                                           \n";
    $stSql .= "     WHERE                                                                                         \n";
    $stSql .= "             TA.cod_boletim          = TB.cod_boletim                                              \n";
    $stSql .= "         AND TA.exercicio            = TB.exercicio                                                \n";
    $stSql .= "         AND TA.cod_entidade         = TB.cod_entidade                                             \n";
    $stSql .= "         AND TA.devolucao = false                                                                  \n";
    $stSql .= "         AND TAER.vl_arrecadacao     > TAER.vl_estornado                                           \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "     UNION                                                                                         \n";
    $stSql .= "                                                                                                   \n";
    $stSql .= "     SELECT                                                                                        \n";
    $stSql .= "          TA.cod_arrecadacao                                                                       \n";
    $stSql .= "         ,TA.exercicio                                                                             \n";
    $stSql .= "         ,TA.timestamp_arrecadacao                                                                 \n";
    $stSql .= "         ,TA.cod_autenticacao                                                                      \n";
    $stSql .= "         ,TA.dt_autenticacao                                                                       \n";
    $stSql .= "         ,TA.cod_boletim                                                                           \n";
    $stSql .= "         ,TB.dt_boletim                                                                            \n";
    $stSql .= "         ,TA.cod_terminal                                                                          \n";
    $stSql .= "         ,TA.timestamp_terminal                                                                    \n";
    $stSql .= "         ,TA.cgm_usuario                                                                           \n";
    $stSql .= "         ,TA.timestamp_usuario                                                                     \n";
    $stSql .= "         ,TA.cod_plano                                                                             \n";
    $stSql .= "         ,TA.cod_entidade                                                                          \n";
    $stSql .= "         ,TA.observacao                                                                            \n";
    $stSql .= "         ,TAR.cod_receita                                                                          \n";
    $stSql .= "         ,TAR.vl_arrecadacao                                                                       \n";
    $stSql .= "         ,0.00 as vl_estornado                                                                     \n";
    $stSql .= "         ,TAR.cod_receita_dedutora                                                                 \n";
    $stSql .= "         ,TAR.vl_deducao                                                                           \n";
    $stSql .= "         ,0.00 as vl_deducao_estornado                                                             \n";
    $stSql .= "         ,TO_CHAR( TA.timestamp_arrecadacao, 'dd/mm/yyyy' ) as dt_arrecadacao                      \n";
    $stSql .= "         ,TAR.descricao                                                                            \n";
    $stSql .= "         ,lancamento_baixa_patrimonio_alienacao.cod_bem                                            \n";
    $stSql .= "     FROM                                                                                          \n";
    $stSql .= "         tesouraria.arrecadacao AS TA                                                              \n";
    $stSql .= "             LEFT JOIN tesouraria.arrecadacao_estornada AS TAE ON (                                \n";
    $stSql .= "                     TA.cod_arrecadacao          = TAE.cod_arrecadacao                             \n";
    $stSql .= "                 AND TA.exercicio                = TAE.exercicio                                   \n";
    $stSql .= "                 AND TA.timestamp_arrecadacao    = TAE.timestamp_arrecadacao                       \n";
    $stSql .= "             )                                                                                     \n";
    $stSql .= "             LEFT JOIN (                                                                           \n";
    $stSql .= "                 SELECT                                                                            \n";
    $stSql .= "                     TAR.exercicio,                                                                \n";
    $stSql .= "                     TAR.cod_arrecadacao,                                                          \n";
    $stSql .= "                     TAR.timestamp_arrecadacao,                                                    \n";
    $stSql .= "                     TAR.cod_receita,                                                              \n";
    $stSql .= "                     TAR.vl_arrecadacao,                                                           \n";
    $stSql .= "                     TARD.cod_receita_dedutora,                                                    \n";
    $stSql .= "                     TARD.vl_deducao,                                                              \n";
    $stSql .= "                     OCR.descricao                                                                 \n";
    $stSql .= "                 FROM                                                                              \n";
    $stSql .= "                     tesouraria.arrecadacao_receita as TAR                                         \n";
    $stSql .= "                     LEFT JOIN tesouraria.arrecadacao_receita_dedutora AS TARD ON(                 \n";
    $stSql .= "                             TAR.exercicio                = TARD.exercicio                         \n";
    $stSql .= "                         AND TAR.cod_arrecadacao          = TARD.cod_arrecadacao                   \n";
    $stSql .= "                         AND TAR.cod_receita              = TARD.cod_receita                       \n";
    $stSql .= "                         AND TAR.timestamp_arrecadacao    = TARD.timestamp_arrecadacao             \n";
    $stSql .= "                     )                                                                             \n";
    $stSql .= "                     ,orcamento.receita       AS RECEITA                                           \n";
    $stSql .= "                     ,orcamento.conta_receita AS OCR                                               \n";
    $stSql .= "                 WHERE                                                                             \n";
    $stSql .= "                         TAR.cod_receita         = RECEITA.cod_receita                             \n";
    $stSql .= "                     AND TAR.exercicio           = RECEITA.exercicio                               \n";
    $stSql .= "                     AND RECEITA.cod_conta       = OCR.cod_conta                                   \n";
    $stSql .= "                     AND RECEITA.exercicio       = OCR.exercicio                                   \n";
    $stSql .= "             ) AS TAR ON(                                                                          \n";
    $stSql .= "                     TA.exercicio                = TAR.exercicio                                   \n";
    $stSql .= "                 AND TA.cod_arrecadacao          = TAR.cod_arrecadacao                             \n";
    $stSql .= "                 AND TA.timestamp_arrecadacao    = TAR.timestamp_arrecadacao                       \n";
    $stSql .= "             )                                                                                     \n";
    $stSql .= "
                 LEFT JOIN contabilidade.lancamento_baixa_patrimonio_alienacao
                        ON TA.cod_arrecadacao       = lancamento_baixa_patrimonio_alienacao.cod_arrecadacao
                       AND TA.exercicio             = lancamento_baixa_patrimonio_alienacao.exercicio_arrecadacao
                       AND TA.timestamp_arrecadacao = lancamento_baixa_patrimonio_alienacao.timestamp_arrecadacao \n ";
    $stSql .= "         , tesouraria.boletim      AS TB                                                           \n";
    $stSql .= "     WHERE                                                                                         \n";
    $stSql .= "             TAE.cod_arrecadacao     IS NULL                                                       \n";
    $stSql .= "         AND TA.cod_boletim          = TB.cod_boletim                                              \n";
    $stSql .= "         AND TA.exercicio            = TB.exercicio                                                \n";
    $stSql .= "         AND TA.cod_entidade         = TB.cod_entidade                                             \n";
    $stSql .= "         AND TA.devolucao = false                                                                  \n";
    $stSql .= ") as TBL                                                                                           \n";

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
function recuperaArrecadacaoNaoEstornadaReceita(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaArrecadacaoNaoEstornadaReceita().$stCondicao.$stOrdem;
    $this->setDebug ( $stSql );
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
function recuperaTerminalArrecadacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaTerminalArrecadacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTerminalArrecadacao()
{
    $stSql .= "     SELECT                                                          \n";
    $stSql .= "         TT.timestamp_terminal,                                      \n";
    $stSql .= "         TT.cod_terminal,                                            \n";
    $stSql .= "         TT.ip,                                                      \n";
    $stSql .= "         UT.cgm_usuario,                                             \n";
    $stSql .= "         UT.timestamp_usuario,                                       \n";
    $stSql .= "         CGM.nom_cgm,                                                \n";
    $stSql .= "         to_char(A.dt_boletim,'dd/mm/yyyy') as dt_boletim,           \n";
    $stSql .= "         max(A.timestamp_abertura) as timestamp_abertura,            \n";
    $stSql .= "         (                                                           \n";
    $stSql .= "         SELECT                                                      \n";
    $stSql .= "             timestamp_fechamento                                    \n";
    $stSql .= "         FROM                                                        \n";
    $stSql .= "             tesouraria.fechamento                                   \n";
    $stSql .= "         WHERE                                                       \n";
    $stSql .= "             cod_terminal        = TT.cod_terminal       AND         \n";
    $stSql .= "             timestamp_terminal  = TT.timestamp_terminal AND         \n";
    $stSql .= "             cgm_usuario         = UT.cgm_usuario        AND         \n";
    $stSql .= "             timestamp_usuario   = UT.timestamp_usuario  AND         \n";
    $stSql .= "             timestamp_abertura  = max(A.timestamp_abertura)         \n";
    $stSql .= "     ) as timestamp_fechamento                                       \n";
    $stSql .= "     FROM                                                            \n";
    $stSql .= "         tesouraria.terminal as TT,                                  \n";
    $stSql .= "         tesouraria.usuario_terminal as UT,                          \n";
    $stSql .= "         tesouraria.abertura as A                                    \n";
    $stSql .= "             LEFT OUTER JOIN tesouraria.fechamento as f ON (         \n";
    $stSql .= "                 a.cod_terminal          = f.cod_terminal        AND \n";
    $stSql .= "                 a.timestamp_terminal    = f.timestamp_terminal  AND \n";
    $stSql .= "                 a.timestamp_abertura    = f.timestamp_abertura      \n";
    $stSql .= "             ),                                                      \n";
    $stSql .= "         sw_cgm                      as CGM                          \n";
    $stSql .= "     WHERE                                                           \n";
    $stSql .= "         UT.cgm_usuario          = CGM.numcgm                        \n";
    $stSql .= "     AND TT.timestamp_terminal   = UT.timestamp_terminal             \n";
    $stSql .= "     AND TT.cod_terminal         = UT.cod_terminal                   \n";
    $stSql .= "     AND TT.exercicio            = UT.exercicio                      \n";
    $stSql .= "     AND UT.responsavel          = true                              \n";
    $stSql .= "     AND UT.timestamp_usuario    = A.timestamp_usuario               \n";
    $stSql .= "     AND UT.timestamp_terminal   = A.timestamp_terminal              \n";
    $stSql .= "     AND UT.exercicio            = A.exercicio                       \n";
    $stSql .= "     AND UT.cod_terminal         = A.cod_terminal                    \n";
    $stSql .= "     AND UT.cgm_usuario          = A.cgm_usuario                     \n";

    $stSql .= "     AND NOT EXISTS ( SELECT 1                                                    \n";
    $stSql .= "                       FROM tesouraria.terminal_desativado t_td                   \n";
    $stSql .= "                      WHERE t_td.cod_terminal       = TT.cod_terminal             \n";
    $stSql .= "                        AND t_td.exercicio          = TT.exercicio                \n";
    $stSql .= "                        AND t_td.timestamp_terminal = TT.timestamp_terminal       \n";
    $stSql .= "                    )                                                             \n";
//     $stSql .= "     AND TT.cod_terminal || '-' || TT.exercicio || '-' || TT.timestamp_terminal NOT IN ( \n";
//     $stSql .= "         SELECT                                                                   \n";
//     $stSql .= "             cod_terminal || '-' || exercicio || '-' || timestamp_terminal        \n";
//     $stSql .= "         FROM                                                                     \n";
//     $stSql .= "             tesouraria.terminal_desativado                                       \n";
//     $stSql .= "     )                                                                            \n";
    return $stSql;
}

    public function recuperaVerificaClassReceitasPorNumeracao(&$rsRecordSet, $stFiltro="", $stOrder = "",  $boTransacao = "")
    {
      return $this->executaRecupera("montaRecuperaVerificaClassReceitasPorNumeracao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVerificaClassReceitasPorNumeracao()
    {
        $stSql = "
                    select tesouraria.fn_verifica_classificacao_receitas_por_numeracao
                          (	 '" . $this->getDado('numeracao') ."'
                              ," . $this->getDado('exercicio') ."
                          ) as classificadas
        ";

        return $stSql;
    }

}
