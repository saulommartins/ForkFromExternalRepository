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
    * Classe de mapeamento da função fn_relatorio_boletim_tesouraria
    * Data de Criação: 12/12//2005

    * @author Analista: Lucas Leusin Oiagem
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-04-04 16:08:37 -0300 (Sex, 04 Abr 2008) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.30  2007/10/16 20:12:09  cako
Ticket#10251#

Revision 1.28  2007/02/26 21:28:22  cako
Bug #8311#

Revision 1.27  2007/02/16 17:11:41  cako
Bug #8400#

Revision 1.26  2007/02/16 12:54:29  cako
Bug #7769#

Revision 1.25  2007/02/12 12:21:45  cako
Bug #7549#

Revision 1.24  2006/11/14 12:22:45  cako
Bug #7232#

Revision 1.23  2006/10/17 16:30:04  cako
Bug #7202#

Revision 1.22  2006/07/19 16:40:33  jose.eduardo
Bug #6596#

Revision 1.21  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTesourariaEmitirBoletim extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTesourariaEmitirBoletim()
{
    parent::Persistente();
}

function recuperaDemonstrativoCaixa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDemonstrativoCaixa();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDemonstrativoCaixa()
{
    $stSql  = "SELECT *                                                                                             \n";
    $stSql .= "FROM tesouraria.fn_boletim_demonstrativo_caixa('".$this->getDado("stFiltroTransferencia")."',        \n";
    $stSql .= "                                               '".$this->getDado("stFiltroTransferenciaEstornada")."',\n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamentoTmp")    ."',      \n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamentoEstornado")."',   \n";
    $stSql .= "                                               '".$this->getDado("stFiltroArrecadacao")  ."',        \n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamento")    ."',        \n";
    $stSql .= "                                               '".$this->getDado("stEntidade")  ."',                 \n";
    $stSql .= "                                               '".$this->getDado("stExercicio")  ."',                \n";
    $stSql .= "                                               '".$this->getDado("botcems")  ."'                     \n";
    $stSql .= ") as retorno( cod_boletim       INTEGER                                                              \n";
    $stSql .= "             ,dt_boletim        VARCHAR                                                              \n";
    $stSql .= "             ,hora              VARCHAR                                                              \n";
    $stSql .= "             ,descricao         VARCHAR                                                              \n";
    $stSql .= "             ,estorno           BOOLEAN                                                              \n";
    $stSql .= "             ,valor             NUMERIC                                                              \n";
    $stSql .= "             ,conta_credito     INTEGER                                                              \n";
    $stSql .= "             ,nom_conta_credito VARCHAR                                                              \n";
    $stSql .= "             ,conta_debito      INTEGER                                                              \n";
    $stSql .= "             ,nom_conta_debito  VARCHAR                                                              \n";
    $stSql .= "             ,cgm_usuario       INTEGER                                                              \n";
    $stSql .= "             ,nom_cgm           VARCHAR                                                              \n";
    $stSql .= "             ,tipo              VARCHAR                                                              \n";
    $stSql .= "             ,cod_tipo          INTEGER                                                              \n";
    $stSql .= ")                                                                                                    \n";

    return $stSql;
}

function recuperaMovimentoBanco(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMovimentoBanco($obTransacao);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMovimentoBanco($obTransacao = "")
{
    $boNovoPlanoConta = (Sessao::getExercicio() > '2012')?true:false;
    $stSql  = "SELECT '".$this->getDado("stExercicio")."' as exercicio, *                                            \n";
    $stSql .= "FROM tesouraria.fn_recupera_movimento_banco('".$this->getDado("stFiltroTransferenciaBanco")."',       \n";
    $stSql .= "                                               '".$this->getDado("stFiltroTransferenciaEstornadaBanco")."',\n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamentoTmpBanco")    ."',     \n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamentoEstornadoBanco")."',   \n";
    $stSql .= "                                               '".$this->getDado("stFiltroArrecadacao")  ."',        \n";
    $stSql .= "                                               '".$this->getDado("stFiltroPagamentoBanco")    ."',        \n";
    $stSql .= "                                               '".$this->getDado("stEntidade")  ."',                 \n";
    $stSql .= "                                               '".$this->getDado("stExercicio")  ."',                \n";
    $stSql .= "                                               '".$this->getDado("stDtBoletim")  ."',                \n";
    $stSql .= "                                               '".$boNovoPlanoConta."'           \n";
    $stSql .= ") as retorno( cod_estrutural    VARCHAR                                                              \n";
    $stSql .= "             ,cod_plano         INTEGER                                                              \n";
    $stSql .= "             ,nom_conta         VARCHAR                                                              \n";
    $stSql .= "             ,saldo_anterior    NUMERIC                                                              \n";
    $stSql .= "             ,vl_credito        NUMERIC                                                              \n";
    $stSql .= "             ,vl_debito         NUMERIC                                                              \n";
    $stSql .= "             ,cod_recurso       INTEGER                                                              \n";
    $stSql .= "             ,nom_recurso       VARCHAR                                                              \n";
    $stSql .= ")                                                                                                    \n";

    if ($this->getDado("boSemMovimentacao") == 'S') {
        $stSql .= "UNION                                                                                           \n";
        $stSql .= "SELECT *                                                                                        \n";
        $stSql .= "FROM tesouraria.fn_relatorio_demostrativo_saldos('".$this->getDado("stExercicio")."',           \n";
        $stSql .= "                                               '".$this->getDado("inCodEntidade")."',           \n";
        $stSql .= "                                               '".$this->getDado("stDtBoletim")."',             \n";
        $stSql .= "                                               '".$this->getDado("stDtBoletim")."',             \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               'S',                                             \n";
        $stSql .= "                                               '',                                              \n";
        $stSql .= "                                               '',                                               \n";
        $stSql .= "                                               ''                                               \n";
        $stSql .= ") as retorno( exercicio          VARCHAR                                                        \n";
        $stSql .= "             ,cod_estrutural     VARCHAR                                                        \n";
        $stSql .= "             ,cod_plano          INTEGER                                                        \n";
        $stSql .= "             ,nom_conta          VARCHAR                                                        \n";
        $stSql .= "             ,saldo_anterior     NUMERIC                                                        \n";
        $stSql .= "             ,vl_credito         NUMERIC                                                        \n";
        $stSql .= "             ,vl_debito          NUMERIC                                                        \n";
        $stSql .= "             ,cod_recurso        INTEGER                                                        \n";
        $stSql .= "             ,nom_recurso        VARCHAR                                                        \n";
        $stSql .= ") ORDER BY cod_estrutural ASC                                                                   \n";
    }

    return $stSql;
}

function recuperaTransferencia(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaTransferencia().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTransferencia()
{
    $stSql .= "CREATE TEMPORARY TABLE tmp_transferencia AS (                                    \n";
    $stSql .= "    SELECT TB.cod_boletim                                                        \n";
    $stSql .= "          ,TT.exercicio                                                          \n";
    $stSql .= "          ,TO_CHAR( TB.dt_boletim, 'dd/mm/yyyy' ) AS dt_boletim                  \n";
    $stSql .= "          ,TO_CHAR( TT.timestamp_transferencia, 'HH24:mi:ss' ) as hora           \n";
    $stSql .= "          ,TT.cod_entidade                                                       \n";
    $stSql .= "          ,TT.tipo                                                               \n";
    $stSql .= "          ,TT.cod_lote                                                           \n";
    $stSql .= "          ,TT.valor as vl_lancamento                                             \n";
    $stSql .= "          ,TT.cod_plano_debito as conta_debito                                   \n";
    $stSql .= "          ,TT.cod_plano_credito as conta_credito                                 \n";
    $stSql .= "          ,TT.cgm_usuario                                                        \n";
    $stSql .= "    FROM tesouraria.boletim             AS TB                                    \n";
    $stSql .= "        ,tesouraria.transferencia as TT                                          \n";
    $stSql .= "      -- Join com tesouraria_transferencia                                       \n";
    $stSql .= "    WHERE TB.exercicio   = TT.exercicio                                          \n";
    $stSql .= "      AND TB.cod_boletim = TT.cod_boletim                                        \n";
    $stSql .= "      AND TB.cod_entidade= TT.cod_entidade                                       \n";
    $stSql .= "      AND TT.cod_tipo not in (1,2)                                               \n";
    $stSql .= "      -- filtros                                                                 \n";
    $stSql .= str_replace( '\'\'', '\'', $this->getDado( 'stFiltroTransferencia' ) )          ."\n";
    $stSql .= "                                                                                 \n";
    $stSql .= ");                                                                               \n";
    $stSql .= "                                                                                 \n";
    $stSql .= "SELECT tbl.*                                                                     \n";
    $stSql .= "      ,tbl.conta_debito ||' - '||CPCD.nom_conta as conta_debito                  \n";
    $stSql .= "      ,tbl.conta_credito||' - '||CPCC.nom_conta as conta_credito                 \n";
    $stSql .= "FROM(                                                                            \n";
    $stSql .= "      SELECT TT.exercicio                                                        \n";
    $stSql .= "            ,TT.conta_debito                                                     \n";
    $stSql .= "            ,TT.conta_credito                                                    \n";
    $stSql .= "            ,SUM( TT.vl_lancamento ) AS vl_lancamento                            \n";
    $stSql .= "      FROM tmp_transferencia AS TT                                               \n";
    $stSql .= "          ,contabilidade.plano_analitica AS CPAD                                 \n";
    $stSql .= "          ,contabilidade.plano_analitica AS CPAC                                 \n";
    $stSql .= "      WHERE TT.exercicio     = CPAD.exercicio                                    \n";
    $stSql .= "        AND TT.conta_debito  = CPAD.cod_plano                                    \n";
    $stSql .= "        AND TT.exercicio     = CPAC.exercicio                                    \n";
    $stSql .= "        AND TT.conta_credito = CPAC.cod_plano                                    \n";
    $stSql .= "      GROUP BY TT.exercicio                                                      \n";
    $stSql .= "              ,TT.conta_debito                                                   \n";
    $stSql .= "              ,TT.conta_credito                                                  \n";
    $stSql .= "      ORDER BY TT.exercicio                                                      \n";
    $stSql .= "              ,TT.conta_debito                                                   \n";
    $stSql .= "              ,TT.conta_credito                                                  \n";
    $stSql .= ") AS tbl                                                                         \n";
    $stSql .= ",contabilidade.plano_analitica AS CPAD                                           \n";
    $stSql .= ",contabilidade.plano_conta     AS CPCD                                           \n";
    $stSql .= ",contabilidade.plano_analitica AS CPAC                                           \n";
    $stSql .= ",contabilidade.plano_conta     AS CPCC                                           \n";
    $stSql .= "WHERE tbl.exercicio     = CPAD.exercicio                                         \n";
    $stSql .= "  AND tbl.conta_debito  = CPAD.cod_plano                                         \n";
    $stSql .= "  AND CPAD.exercicio    = CPCD.exercicio                                         \n";
    $stSql .= "  AND CPAD.cod_conta    = CPCD.cod_conta                                         \n";
    $stSql .= "  AND tbl.exercicio     = CPAC.exercicio                                         \n";
    $stSql .= "  AND tbl.conta_credito = CPAC.cod_plano                                         \n";
    $stSql .= "  AND CPAC.exercicio    = CPCC.exercicio                                         \n";
    $stSql .= "  AND CPAC.cod_conta    = CPCC.cod_conta                                         \n";
    $stSql .= "  AND (replace( substr( CPCD.cod_estrutural, 1, 9 ), '.', '' ) between '11111' AND '11114'  \n";
    $stSql .= "    OR replace( substr( CPCC.cod_estrutural, 1, 9 ), '.', '' ) between '11111' AND '11114') \n";
    $stSql .= "ORDER BY CPCD.cod_estrutural                                                     \n";
    $stSql .= ";                                                                                \n";

    return $stSql;
}

function recuperaPagamento(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaPagamento().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPagamento()
{
    $stSql .= "-- Pagamentos                                                                                                    \n";
    $stSql .= "CREATE TEMPORARY TABLE tmp_pagamentos AS (                                                                       \n";
    $stSql .= "         SELECT  tp.timestamp                                                                                    \n";
    $stSql .= "                ,tp.exercicio_boletim as exercicio                                                               \n";
    $stSql .= "                ,nl.exercicio_empenho                                                                            \n";
    $stSql .= "                ,tp.cod_entidade                                                                                 \n";
    $stSql .= "                ,tp.cod_boletim                                                                                  \n";
    $stSql .= "                ,contabilidade.fn_recupera_conta_lancamento( CP.exercicio                                        \n";
    $stSql .= "                                              ,CP.cod_entidade                                                   \n";
    $stSql .= "                                              ,CP.cod_lote                                                       \n";
    $stSql .= "                                              ,CP.tipo                                                           \n";
    $stSql .= "                                              ,2                                                      \n";
    $stSql .= "                                              ,'D') as cod_plano_debito                                          \n";
    $stSql .= "                ,tp.cod_plano as cod_plano_credito                                                               \n";
    $stSql .= "                ,nlp.vl_pago AS vl_pago                                                                          \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "          FROM  tesouraria.pagamento AS TP                                                                       \n";
    $stSql .= "                JOIN empenho.nota_liquidacao_paga as nlp                                                         \n";
    $stSql .= "                ON (    nlp.cod_nota     = tp.cod_nota                                                           \n";
    $stSql .= "                    AND nlp.cod_entidade = tp.cod_entidade                                                       \n";
    $stSql .= "                    AND nlp.exercicio    = tp.exercicio                                                          \n";
    $stSql .= "                    AND nlp.timestamp    = tp.timestamp                                                          \n";
    $stSql .= "                )                                                                                                \n";
    $stSql .= "                JOIN empenho.nota_liquidacao as nl                                                               \n";
    $stSql .= "                ON (    nl.cod_nota     = nlp.cod_nota                                                           \n";
    $stSql .= "                    AND nl.exercicio    = nlp.exercicio                                                          \n";
    $stSql .= "                    AND nl.cod_entidade = nlp.cod_entidade                                                       \n";
    $stSql .= "                )                                                                                                \n";
    $stSql .= "                JOIN contabilidade.pagamento as cp                                                               \n";
    $stSql .= "                ON (    cp.cod_entidade         = nlp.cod_entidade                                               \n";
    $stSql .= "                    AND cp.exercicio_liquidacao = nlp.exercicio                                                  \n";
    $stSql .= "                    AND cp.cod_nota             = nlp.cod_nota                                                   \n";
    $stSql .= "                    AND cp.timestamp            = nlp.timestamp                                                  \n";
    $stSql .= "                )                                                                                                \n";
    $stSql .= "                JOIN contabilidade.lancamento_empenho as cle                                                     \n";
    $stSql .= "                ON (    cle.cod_lote     = cp.cod_lote                                                           \n";
    $stSql .= "                    AND cle.cod_entidade = cp.cod_entidade                                                       \n";
    $stSql .= "                    AND cle.sequencia    = cp.sequencia                                                          \n";
    $stSql .= "                    AND cle.exercicio    = cp.exercicio                                                          \n";
    $stSql .= "                    AND cle.tipo         = cp.tipo                                                               \n";
    $stSql .= "                )                                                                                                \n";
    $stSql .= "         WHERE  tp.exercicio_boletim = '".$this->getDado('stExercicio')."'                                       \n";
    $stSql .= "            AND tp.cod_entidade in (".$this->getDado('stEntidade').")                                         \n";
    $stSql .= "            AND TO_DATE(TO_CHAR(tp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') = TO_DATE('".$this->getDado('stDtBoletim')."','dd/mm/yyyy') \n";
    $stSql .= "            AND cle.estorno = false                                                                              \n";
    if($this->getDado('inCodBoletim'))
        $stSql .= "        AND tp.cod_boletim in (".$this->getDado('inCodBoletim')." )                                          \n";
    $stSql .= "                                                                                                                 \n";
    if($this->getDado('inCGM'))
        $stSql .= "        AND tp.cgm_usuario = ".$this->getDado('inCGM')."                                                     \n";
    if($this->getDado('inCodTerminal'))
        $stSql .= "        AND tp.cod_terminal = ".$this->getDado('inCodTerminal')."                                            \n";
    $stSql .= ");                                                                                                               \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "CREATE INDEX btree_tmp_pagamentos ON tmp_pagamentos    ( exercicio, cod_entidade, cod_boletim, timestamp  );     \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "-- Estornos                                                                                                      \n";
    $stSql .= "CREATE TEMPORARY TABLE tmp_estornos as (                                                                         \n";
    $stSql .= "                 SELECT  tpe.timestamp                                                                           \n";
    $stSql .= "                        ,tpe.exercicio_boletim as exercicio                                                      \n";
    $stSql .= "                        ,tp.exercicio_empenho                                                                    \n";
    $stSql .= "                        ,tpe.cod_entidade                                                                        \n";
    $stSql .= "                        ,tpe.cod_boletim                                                                         \n";
    $stSql .= "                        ,tp.cod_plano_credito as cod_plano_debito                                                \n";
    $stSql .= "                        ,tp.cod_plano_debito as cod_plano_credito                                                \n";
    $stSql .= "                        ,coalesce(sum(nlpa.vl_anulado),0.00) as vl_pago                                          \n";
    $stSql .= "                  FROM  tesouraria.pagamento_estornado as tpe                                                    \n";
    $stSql .= "                        JOIN empenho.nota_liquidacao_paga_anulada as nlpa                                        \n";
    $stSql .= "                        ON (    tpe.timestamp_anulado = nlpa.timestamp_anulada                                   \n";
    $stSql .= "                            AND tpe.cod_nota          = nlpa.cod_nota                                            \n";
    $stSql .= "                            AND tpe.cod_entidade      = nlpa.cod_entidade                                        \n";
    $stSql .= "                            AND tpe.exercicio         = nlpa.exercicio                                           \n";
    $stSql .= "                            AND tpe.timestamp         = nlpa.timestamp                                           \n";
    $stSql .= "                        )                                                                                        \n";
    $stSql .= "                        JOIN (                                                                                   \n";
    $stSql .= "                            SELECT    tp.exercicio                                                               \n";
    $stSql .= "                                     ,tp.cod_entidade                                                            \n";
    $stSql .= "                                     ,tp.timestamp                                                               \n";
    $stSql .= "                                     ,tp.cod_nota                                                                \n";
    $stSql .= "                                     ,nl.exercicio_empenho                                                       \n";
    $stSql .= "                                     ,contabilidade.fn_recupera_conta_lancamento( CP.exercicio                   \n";
    $stSql .= "                                                                   ,CP.cod_entidade                              \n";
    $stSql .= "                                                                   ,CP.cod_lote                                  \n";
    $stSql .= "                                                                   ,CP.tipo                                      \n";
    $stSql .= "                                                                   ,2                                 \n";
    $stSql .= "                                                                   ,'D') as cod_plano_debito                     \n";
    $stSql .= "                                     ,tp.cod_plano as cod_plano_credito                                          \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "                               FROM  tesouraria.pagamento AS TP                                                  \n";
    $stSql .= "                                     JOIN empenho.nota_liquidacao_paga as nlp                                    \n";
    $stSql .= "                                     ON (    nlp.cod_nota     = tp.cod_nota                                      \n";
    $stSql .= "                                         AND nlp.cod_entidade = tp.cod_entidade                                  \n";
    $stSql .= "                                         AND nlp.exercicio    = tp.exercicio                                     \n";
    $stSql .= "                                         AND nlp.timestamp    = tp.timestamp                                     \n";
    $stSql .= "                                     )                                                                           \n";
    $stSql .= "                                     JOIN empenho.nota_liquidacao as nl                                          \n";
    $stSql .= "                                     ON (    nl.cod_nota     = nlp.cod_nota                                      \n";
    $stSql .= "                                         AND nl.exercicio    = nlp.exercicio                                     \n";
    $stSql .= "                                         AND nl.cod_entidade = nlp.cod_entidade                                  \n";
    $stSql .= "                                     )                                                                           \n";
    $stSql .= "                                     JOIN contabilidade.pagamento as cp                                          \n";
    $stSql .= "                                     ON (    cp.cod_entidade         = nlp.cod_entidade                          \n";
    $stSql .= "                                         AND cp.exercicio_liquidacao = nlp.exercicio                             \n";
    $stSql .= "                                         AND cp.cod_nota             = nlp.cod_nota                              \n";
    $stSql .= "                                         AND cp.timestamp            = nlp.timestamp                             \n";
    $stSql .= "                                     )                                                                           \n";
    $stSql .= "                                     JOIN contabilidade.lancamento_empenho as cle                                \n";
    $stSql .= "                                     ON (    cle.cod_lote     = cp.cod_lote                                      \n";
    $stSql .= "                                         AND cle.cod_entidade = cp.cod_entidade                                  \n";
    $stSql .= "                                         AND cle.sequencia    = cp.sequencia                                     \n";
    $stSql .= "                                         AND cle.exercicio    = cp.exercicio                                     \n";
    $stSql .= "                                         AND cle.tipo         = cp.tipo                                          \n";
    $stSql .= "                                     )                                                                           \n";
    $stSql .= "                             where  cle.estorno = false                                                          \n";
    $stSql .= "                                and tp.cod_entidade in (".$this->getDado('stEntidade').")                        \n";
    $stSql .= "                                and tp.exercicio_boletim = '".$this->getDado('stExercicio')."'                   \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "                        ) as TP on (    tp.cod_nota     = tpe.cod_nota                                           \n";
    $stSql .= "                                    AND tp.exercicio    = tpe.exercicio                                          \n";
    $stSql .= "                                    AND tp.cod_entidade = tpe.cod_entidade                                       \n";
    $stSql .= "                                    AND tp.timestamp    = tpe.timestamp                                          \n";
    $stSql .= "                        )                                                                                        \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "                  WHERE tpe.cod_entidade in (".$this->getDado('stEntidade').")                                   \n";
    $stSql .= "                    AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,'dd/mm/yyyy'),'dd/mm/yyyy') = TO_DATE('".$this->getDado('stDtBoletim')."','dd/mm/yyyy') \n";
    $stSql .= "                    AND tpe.exercicio_boletim = '".$this->getDado('stExercicio')."'                              \n";
    if($this->getDado('inCodBoletim'))
        $stSql .= "                AND tpe.cod_boletim IN (".$this->getDado('inCodBoletim')." )                                  \n";
   if($this->getDado('inCGM'))
        $stSql .= "                AND tpe.cgm_usuario = ".$this->getDado('inCGM')."                                             \n";
    if($this->getDado('inCodTerminal'))
        $stSql .= "                AND tpe.cod_terminal = ".$this->getDado('inCodTerminal')."                                    \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "               GROUP BY tpe.timestamp                                                                             \n";
    $stSql .= "                        ,tpe.exercicio_boletim                                                                    \n";
    $stSql .= "                        ,tp.exercicio_empenho                                                                     \n";
    $stSql .= "                        ,tpe.cod_entidade                                                                         \n";
    $stSql .= "                        ,tpe.cod_boletim                                                                          \n";
    $stSql .= "                        ,tp.cod_plano_credito                                                                     \n";
    $stSql .= "                        ,tp.cod_plano_debito                                                                      \n";
    $stSql .= ");                                                                                                                \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "CREATE INDEX btree_tmp_estornos ON tmp_estornos ( exercicio, cod_entidade, cod_boletim, timestamp  );             \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "-- Pagamentos Extra-Orçamentarios                                                                                 \n";
    $stSql .= "CREATE TEMPORARY TABLE tmp_transferencias AS (                                                                    \n";
    $stSql .= "     SELECT                                                                                                       \n";
    $stSql .= "         tt.timestamp_transferencia as timestamp                                                                  \n";
    $stSql .= "         ,tt.exercicio                                                                                            \n";
    $stSql .= "         ,'' as exercicio_empenho                                                                                 \n";
    $stSql .= "         ,tt.cod_entidade                                                                                         \n";
    $stSql .= "         ,tt.cod_boletim                                                                                          \n";
    $stSql .= "         ,tt.cod_plano_debito                                                                                     \n";
    $stSql .= "         ,tt.cod_plano_credito                                                                                    \n";
    $stSql .= "         ,tt.valor as vl_pago                                                                                     \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "     FROM tesouraria.boletim as tb                                                                                \n";
    $stSql .= "          JOIN tesouraria.transferencia as tt                                                                     \n";
    $stSql .= "          ON (   tt.cod_entidade = tb.cod_entidade                                                                \n";
    $stSql .= "             AND tt.cod_boletim  = tb.cod_boletim                                                                 \n";
    $stSql .= "             AND tt.exercicio    = tb.exercicio                                                                   \n";
    $stSql .= "          )                                                                                                       \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "     WHERE   tt.cod_tipo = 1                                                                                      \n";
    $stSql .= str_replace( '\'\'', '\'', $this->getDado( 'stFiltroTransferencia' ) )."                                           \n";
    $stSql .= "     UNION                                                                                                        \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "     SELECT                                                                                                       \n";
    $stSql .= "          tte.timestamp_estornada as timestamp                                                                    \n";
    $stSql .= "         ,tte.exercicio                                                                                           \n";
    $stSql .= "         ,'' as exercicio_empenho                                                                                 \n";
    $stSql .= "         ,tte.cod_entidade                                                                                        \n";
    $stSql .= "         ,tte.cod_boletim                                                                                         \n";
    $stSql .= "         ,tt.cod_plano_credito as cod_plano_debito                                                                \n";
    $stSql .= "         ,tt.cod_plano_debito as cod_plano_credito                                                                \n";
    $stSql .= "         ,(tte.valor * -1) as vl_pago                                                                             \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "     FROM tesouraria.boletim as tb                                                                                \n";
    $stSql .= "          JOIN tesouraria.transferencia_estornada as tte                                                          \n";
    $stSql .= "          ON (   tte.cod_entidade = tb.cod_entidade                                                               \n";
    $stSql .= "             AND tte.cod_boletim  = tb.cod_boletim                                                                \n";
    $stSql .= "             AND tte.exercicio    = tb.exercicio                                                                  \n";
    $stSql .= "          )                                                                                                       \n";
    $stSql .= "          JOIN tesouraria.transferencia as tt                                                                     \n";
    $stSql .= "          ON (   tt.cod_entidade = tte.cod_entidade                                                               \n";
    $stSql .= "             AND tt.tipo         = tte.tipo                                                                       \n";
    $stSql .= "             AND tt.exercicio    = tte.exercicio                                                                  \n";
    $stSql .= "             AND tt.cod_lote     = tte.cod_lote                                                                   \n";
    $stSql .= "          )                                                                                                       \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "     WHERE   tt.cod_tipo = 1                                                                                      \n";
    $stSql .= str_replace( '\'\'', '\'', $this->getDado( 'stFiltroTransferenciaEstornada' ) )."                                  \n";
    $stSql .= ");                                                                                                               \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "SELECT TP.exercicio                                                                                               \n";
    $stSql .= "          ,TP.exercicio_empenho                                                                                   \n";
    $stSql .= "          ,TP.conta_debito ||' - '||CPCD.nom_conta AS conta_debito                                                \n";
    $stSql .= "          ,TP.conta_credito||' - '||CPCC.nom_conta AS conta_credito                                               \n";
    $stSql .= "          ,CPCC.cod_estrutural                                                                                    \n";
    $stSql .= "          ,sum(TP.vl_pago) as vl_pago                                                                             \n";
    $stSql .= "    FROM                                                                                                          \n";
    $stSql .= "        ( SELECT                                                                                                  \n";
    $stSql .= "               TP.exercicio                                                                                       \n";
    $stSql .= "              ,TP.exercicio_empenho                                                                               \n";
    $stSql .= "              ,TP.vl_pago as vl_pago                                                                              \n";
    $stSql .= "              ,tp.cod_plano_credito as conta_credito                                                              \n";
    $stSql .= "              ,TP.cod_plano_debito  as conta_debito                                                               \n";
    $stSql .= "        FROM tesouraria.boletim   AS TB                                                                           \n";
    $stSql .= "             JOIN ( SELECT timestamp                                                                              \n";
    $stSql .= "                           ,exercicio                                                                             \n";
    $stSql .= "                           ,exercicio_empenho                                                                     \n";
    $stSql .= "                           ,cod_entidade                                                                          \n";
    $stSql .= "                           ,cod_boletim                                                                           \n";
    $stSql .= "                           ,cod_plano_credito                                                                     \n";
    $stSql .= "                           ,cod_plano_debito                                                                      \n";
    $stSql .= "                           ,vl_pago                                                                               \n";
    $stSql .= "                    FROM tmp_pagamentos                                                                           \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "                UNION                                                                                             \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "                     SELECT timestamp                                                                             \n";
    $stSql .= "                           ,exercicio                                                                             \n";
    $stSql .= "                           ,exercicio_empenho                                                                     \n";
    $stSql .= "                           ,cod_entidade                                                                          \n";
    $stSql .= "                           ,cod_boletim                                                                           \n";
    $stSql .= "                           ,cod_plano_credito                                                                     \n";
    $stSql .= "                           ,cod_plano_debito                                                                      \n";
    $stSql .= "                           ,(vl_pago * -1) as vl_pago                                                             \n";
    $stSql .= "                    FROM tmp_estornos                                                                             \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "                UNION                                                                                             \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "                     SELECT timestamp                                                                             \n";
    $stSql .= "                           ,exercicio                                                                             \n";
    $stSql .= "                           ,exercicio_empenho                                                                     \n";
    $stSql .= "                           ,cod_entidade                                                                          \n";
    $stSql .= "                           ,cod_boletim                                                                           \n";
    $stSql .= "                           ,cod_plano_credito                                                                     \n";
    $stSql .= "                           ,cod_plano_debito                                                                      \n";
    $stSql .= "                           ,vl_pago                                                                              \n";
    $stSql .= "                    FROM tmp_transferencias                                                                       \n";
    $stSql .= "                                                                                                                  \n";
    $stSql .= "             ) as TP ON (   tp.cod_boletim  = tb.cod_boletim                                                      \n";
    $stSql .= "                        AND tp.cod_entidade = tb.cod_entidade                                                     \n";
    $stSql .= "                        AND tp.exercicio    = tb.exercicio                                                        \n";
    $stSql .= "             )                                                                                                    \n";
    $stSql .= "         WHERE                                                                                                    \n";
    $stSql .= "               tb.cod_entidade in (".$this->getDado('stEntidade').")                                              \n";
    $stSql .= "           AND tb.exercicio = '".$this->getDado('stExercicio')."'                                                 \n";
    $stSql .= "           AND tb.dt_boletim = TO_DATE('".$this->getDado('stDtBoletim')."','dd/mm/yyyy')                          \n";
    if($this->getDado('inCodBoletim'))
        $stSql .= "       AND tb.cod_boletim IN ( ".$this->getDado('inCodBoletim')." )                                          \n";
    $stSql .= "        ) as TP                                                                                                  \n";
    $stSql .= "        ,contabilidade.plano_analitica AS CPAD                                                                   \n";
    $stSql .= "        ,contabilidade.plano_conta     AS CPCD                                                                   \n";
    $stSql .= "        ,contabilidade.plano_analitica AS CPAC                                                                   \n";
    $stSql .= "        ,contabilidade.plano_conta     AS CPCC                                                                   \n";
    $stSql .= "    WHERE TP.exercicio     = CPAD.exercicio                                                                      \n";
    $stSql .= "      AND TP.conta_debito  = CPAD.cod_plano                                                                      \n";
    $stSql .= "      AND CPAD.exercicio   = CPCD.exercicio                                                                      \n";
    $stSql .= "      AND CPAD.cod_conta   = CPCD.cod_conta                                                                      \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "      AND TP.exercicio     = CPAC.exercicio                                                                      \n";
    $stSql .= "      AND TP.conta_credito = CPAC.cod_plano                                                                      \n";
    $stSql .= "      AND CPAC.exercicio   = CPCC.exercicio                                                                      \n";
    $stSql .= "      AND CPAC.cod_conta   = CPCC.cod_conta                                                                      \n";
    $stSql .= "                                                                                                                 \n";
    $stSql .= "  GROUP BY TP.exercicio                                                                                          \n";
    $stSql .= "          ,TP.exercicio_empenho                                                                                  \n";
    $stSql .= "          ,TP.conta_debito, CPCD.nom_conta                                                                       \n";
    $stSql .= "          ,TP.conta_credito, CPCC.nom_conta                                                                      \n";
    $stSql .= "          ,CPCC.cod_estrutural                                                                                   \n";
    $stSql .= "    ORDER BY TP.exercicio_empenho                                                                                \n";
    $stSql .= "            ,TP.exercicio                                                                                        \n";
    $stSql .= "            ,CPCC.cod_estrutural;                                                                                \n";

    return $stSql;
}

function recuperaArrecadacao(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaArrecadacao($boTransacao).$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaArrecadacao($boTransacao = "")
{
    $stSql .= "SELECT *                                                             \n";
    if (Sessao::getExercicio() > '2012') {
        $stSql .= "FROM tesouraria.fn_listar_arrecadacao_tce('".$this->getDado("stFiltroArrecadacao")."', '".$this->getDado("stFiltroArrecadacao")."') ;  \n";
    } else {
        $stSql .= "FROM tesouraria.fn_listar_arrecadacao_tce('".$this->getDado("stFiltroArrecadacao")."', '".$this->getDado("stFiltroArrecadacao")."') ;  \n";
    }
    $stSql .= "                                                                     \n";
    $stSql .= "SELECT *                                                             \n";
    $stSql .= "FROM(SELECT tbl.exercicio                                                 \n";
    $stSql .= "           ,tbl.debito ||' - '||CPCD.nom_conta  AS conta_debito           \n";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "           ,CASE WHEN tbl.conta_receita <> '' THEN                    \n";
        $stSql .= "                 tbl.conta_receita                                    \n";
        $stSql .= "            ELSE                                                      \n";
        $stSql .= "                 tbl.credito||' - '||CPCC.nom_conta                   \n";
        $stSql .= "            END AS conta_credito                                      \n";
    } else {
        $stSql .= "                ,tbl.credito||' - '||CPCC.nom_conta AS conta_credito  \n";
    }

    $stSql .= "           ,SUM( valor - vl_desconto + vl_multa + vl_juros ) AS valor     \n";
    $stSql .= "           ,CASE WHEN tbl.tipo = 'A'                                      \n";
    $stSql .= "              THEN CPCD.cod_estrutural                                    \n";
    $stSql .= "              ELSE CASE WHEN tbl.tipo = 'E'                               \n";
    $stSql .= "                     THEN CPCC.cod_estrutural                             \n";
    $stSql .= "                     ELSE '2.1'                                           \n";
    $stSql .= "              END                                                         \n";
    $stSql .= "            END AS cod_estrutural                                         \n";
    $stSql .= "           ,tbl.tipo                                                      \n";
    $stSql .= "     FROM(                                                                \n";
    $stSql .= "         SELECT TA.exercicio                                              \n";
    $stSql .= "               ,TA.conta_debito                                           \n";
    $stSql .= "               ,TA.conta_debito as debito                                 \n";
    $stSql .= "               ,TA.conta_credito                                          \n";
    $stSql .= "               ,TA.cod_receita as credito                                 \n";
    $stSql .= "               ,TA.conta_receita                                          \n";
    $stSql .= "               ,SUM( TA.valor )       AS valor                            \n";
    $stSql .= "               ,SUM( TA.vl_desconto ) AS vl_desconto                      \n";
    $stSql .= "               ,SUM( TA.vl_multa )    AS vl_multa                         \n";
    $stSql .= "               ,SUM( TA.vl_juros )    AS vl_juros                         \n";
    $stSql .= "               ,'A'                   AS tipo                             \n";
    $stSql .= "         FROM tmp_arrecadacao AS TA                                       \n";
    $stSql .= "         GROUP BY TA.exercicio                                            \n";
    $stSql .= "                 ,TA.conta_debito                                         \n";
    $stSql .= "                 ,debito                                                  \n";
    $stSql .= "                 ,TA.conta_credito                                        \n";
    $stSql .= "                 ,credito                                                 \n";
    $stSql .= "                 ,TA.conta_receita                                        \n";
    $stSql .= "                                                                          \n";
    $stSql .= "         UNION ALL                                                        \n";
    $stSql .= "                                                                          \n";
    $stSql .= "         SELECT TAE.exercicio                                             \n";
    $stSql .= "               ,TAE.conta_debito                                          \n";
    $stSql .= "               ,TAE.cod_receita as debito                                 \n";
    $stSql .= "               ,TAE.conta_credito                                         \n";
    $stSql .= "               ,TAE.conta_credito as credito                              \n";
    $stSql .= "               ,TAE.conta_receita                                         \n";
    $stSql .= "               ,SUM( TAE.valor )*(-1)       AS valor                      \n";
    $stSql .= "               ,SUM( TAE.vl_desconto )*(-1) AS vl_desconto                \n";
    $stSql .= "               ,SUM( TAE.vl_multa )*(-1)    AS vl_multa                   \n";
    $stSql .= "               ,SUM( TAE.vl_juros )*(-1) AS vl_juros                      \n";
    $stSql .= "               ,'E'                   AS tipo                             \n";
    $stSql .= "         FROM tmp_arrecadacao_estornada AS TAE                            \n";
    $stSql .= "         GROUP BY TAE.exercicio                                           \n";
    $stSql .= "                 ,TAE.conta_debito                                        \n";
    $stSql .= "                 ,debito                                                  \n";
    $stSql .= "                 ,TAE.conta_credito                                       \n";
    $stSql .= "                 ,credito                                                 \n";
    $stSql .= "                 ,TAE.conta_receita                                       \n";
    $stSql .= "                                                                          \n";
    $stSql .= "         UNION ALL                                                        \n";
    $stSql .= "                                                                          \n";
    $stSql .= "       SELECT TT.exercicio                                                \n";
    $stSql .= "                    ,TT.cod_plano_debito as conta_debito                  \n";
    $stSql .= "                    ,TT.cod_plano_debito as debito                        \n";
    $stSql .= "                    ,TT.cod_plano_credito as conta_credito                \n";
    $stSql .= "                    ,TT.cod_plano_credito as credito                      \n";
    $stSql .= "                    ,'' AS conta_receita                                  \n";
    $stSql .= "                    ,TT.valor as valor                                    \n";
    $stSql .= "                    ,0.00              as vl_desconto                     \n";
    $stSql .= "                    ,0.00              as vl_multa                        \n";
    $stSql .= "                    ,0.00              as vl_juros                        \n";
    $stSql .= "                    ,'T'               as tipo                            \n";
    $stSql .= "              FROM tesouraria.boletim             AS TB                   \n";
    $stSql .= "                  ,tesouraria.transferencia       AS TT                   \n";
    $stSql .= "                -- Join com tesouraria_transferencia                      \n";
    $stSql .= "              WHERE TB.exercicio   = TT.exercicio                         \n";
    $stSql .= "                AND TB.cod_boletim = TT.cod_boletim                       \n";
    $stSql .= "                AND TB.cod_entidade= TT.cod_entidade                      \n";
    $stSql .= "                AND TT.cod_tipo = 2  -- Arrecadacao Extra                 \n";
    $stSql .= "                -- filtros                                                \n";
    $stSql .= str_replace( '\'\'', '\'', $this->getDado( 'stFiltroTransferencia' ) )."   \n";
    $stSql .= "                                                                          \n";
    $stSql .= "         UNION ALL                                                        \n";
    $stSql .= "                                                                          \n";
    $stSql .= "              SELECT TTE.exercicio                                        \n";
    $stSql .= "                    ,TT.cod_plano_credito as conta_debito                 \n";
    $stSql .= "                    ,TT.cod_plano_credito as debito                       \n";
    $stSql .= "                    ,TT.cod_plano_debito as conta_credito                 \n";
    $stSql .= "                    ,TT.cod_plano_debito as credito                       \n";
    $stSql .= "                    ,'' AS conta_receita                                  \n";
    $stSql .= "                    ,(TTE.valor * -1) as valor                            \n";
    $stSql .= "                    ,0.00              as vl_desconto                     \n";
    $stSql .= "                    ,0.00              as vl_multa                        \n";
    $stSql .= "                    ,0.00              as vl_juros                        \n";
    $stSql .= "                    ,'T'               as tipo                            \n";
    $stSql .= "              FROM tesouraria.boletim             AS TB                   \n";
    $stSql .= "                  ,tesouraria.transferencia       AS TT                   \n";
    $stSql .= "                   JOIN tesouraria.transferencia_estornada as TTE         \n";
    $stSql .= "                     ON (    TTE.cod_entidade = TT.cod_entidade           \n";
    $stSql .= "                         AND TTE.exercicio    = TT.exercicio              \n";
    $stSql .= "                         AND TTE.cod_lote     = TT.cod_lote               \n";
    $stSql .= "                         AND TTE.tipo         = TT.tipo                   \n";
    $stSql .= "                     )                                                    \n";
    $stSql .= "                -- Join com tesouraria_transferencia                      \n";
    $stSql .= "              WHERE TB.exercicio    = TTE.exercicio                       \n";
    $stSql .= "                AND TB.cod_boletim  = TTE.cod_boletim                     \n";
    $stSql .= "                AND TB.cod_entidade = TTE.cod_entidade                    \n";
    $stSql .= "                AND TT.cod_tipo = 2 -- Arrecadacao Extra                  \n";
    $stSql .= "                -- filtros                                                \n";
    $stSql .= str_replace( '\'\'', '\'', $this->getDado( 'stFiltroTransferenciaEstornada' ) )."   \n";
    $stSql .= "     ) AS tbl                                                             \n";
    // $stSql .= "     ,contabilidade.plano_analitica AS CPAD                               \n";
    // $stSql .= "     ,contabilidade.plano_conta     AS CPCD                               \n";
    // $stSql .= "     ,contabilidade.plano_analitica AS CPAC                               \n";
    // $stSql .= "     ,contabilidade.plano_conta     AS CPCC                               \n";
    $stSql .= "    LEFT JOIN contabilidade.plano_analitica CPAD                          \n";
    $stSql .= "           ON tbl.exercicio    = CPAD.exercicio                           \n";
    $stSql .= "          AND tbl.conta_debito = CPAD.cod_plano                           \n";
    $stSql .= "    LEFT JOIN contabilidade.plano_conta CPCD                              \n";
    $stSql .= "           ON CPAD.exercicio   = CPCD.exercicio                           \n";
    $stSql .= "          AND CPAD.cod_conta   = CPCD.cod_conta                           \n";
    $stSql .= "    LEFT JOIN contabilidade.plano_analitica CPAC                          \n";
    $stSql .= "           ON tbl.exercicio    = CPAC.exercicio                           \n";
    $stSql .= "          AND tbl.conta_credito= CPAC.cod_plano                           \n";
    $stSql .= "    LEFT JOIN contabilidade.plano_conta CPCC                              \n";
    $stSql .= "           ON CPAC.exercicio   = CPCC.exercicio                           \n";
    $stSql .= "          AND CPAC.cod_conta   = CPCC.cod_conta                           \n";
    // $stSql .= "     WHERE tbl.exercicio     = CPAD.exercicio                             \n";
    // $stSql .= "       AND tbl.conta_debito  = CPAD.cod_plano                             \n";
    // $stSql .= "       AND CPAD.exercicio    = CPCD.exercicio                             \n";
    // $stSql .= "       AND CPAD.cod_conta    = CPCD.cod_conta                             \n";
    // $stSql .= "       AND tbl.exercicio     = CPAC.exercicio                             \n";
    // $stSql .= "       AND tbl.conta_credito = CPAC.cod_plano                             \n";
    // $stSql .= "       AND CPAC.exercicio    = CPCC.exercicio                             \n";
    // $stSql .= "       AND CPAC.cod_conta    = CPCC.cod_conta                             \n";
    // $stSql .= "       AND (replace( substr( CPCD.cod_estrutural, 1, 9 ), '.', '' ) between '11111' AND '11114'  \n";
    // $stSql .= "         OR replace( substr( CPCC.cod_estrutural, 1, 9 ), '.', '' ) between '11111' AND '11114') \n";
    $stSql .= "     GROUP BY tbl.exercicio                                               \n";
    $stSql .= "             ,tbl.conta_debito                                            \n";
    $stSql .= "             ,tbl.debito                                                  \n";
    $stSql .= "             ,tbl.conta_credito                                           \n";
    $stSql .= "             ,tbl.credito                                                 \n";
    $stSql .= "             ,tbl.conta_receita                                           \n";
    $stSql .= "             ,CPCD.nom_conta                                              \n";
    $stSql .= "             ,CPCC.nom_conta                                              \n";
    $stSql .= "             ,CPCD.cod_estrutural                                         \n";
    $stSql .= "             ,CPCC.cod_estrutural                                         \n";
    $stSql .= "             ,tbl.tipo                                                    \n";
    $stSql .= "     ORDER BY tbl.exercicio                                               \n";
    $stSql .= ") as tbl                                                                  \n";
    $stSql .= "ORDER BY exercicio                                                        \n";
    $stSql .= "        ,tipo                                                             \n";
    $stSql .= "        ,cod_estrutural                                                   \n";

    return $stSql;
}

}
