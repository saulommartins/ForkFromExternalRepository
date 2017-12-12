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
    * Classe de mapeamento da tabela CONTABILIDADE.VALOR_LANCAMENTO
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32478 $
    $Name$
    $Autor: $
    $Date: 2007-07-06 13:04:23 -0300 (Sex, 06 Jul 2007) $

    * Casos de uso: uc-02.02.21
                    uc-02.02.04
                    uc-02.02.05
                    uc-02.08.01
                    uc-02.02.31
*/

/*
$Log$
Revision 1.22  2007/07/06 16:04:23  vitor
Bug#8743#

Revision 1.21  2007/07/06 15:42:37  vitor
Bug#8743#

Revision 1.20  2007/06/20 12:53:01  vitor
Bug#8825#

Revision 1.19  2007/06/18 21:00:16  vitor
#8825#

Revision 1.18  2007/06/14 15:40:48  vitor
#9400#

Revision 1.17  2007/06/07 14:42:45  vitor
#8825#

Revision 1.16  2007/06/06 19:32:28  vitor
#8825#

Revision 1.15  2007/06/04 15:15:03  vitor
#8839##8840##8841#

Revision 1.14  2007/05/21 19:26:29  vitor
Bug #8825#

Revision 1.13  2006/09/18 14:46:13  jose.eduardo
Bug #6985#

Revision 1.12  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.VALOR_LANCAMENTO
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeValorLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeValorLancamento()
{
    parent::Persistente();
    $this->setTabela('contabilidade.valor_lancamento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lote,tipo,sequencia,exercicio,tipo_valor,cod_entidade');

    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('tipo_valor','char',true,'01',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('vl_lancamento','numeric',true,'14,02',false,false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaTodos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function inclusaoPorPl(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if (!$this->getDado('cod_lote')) {
        $this->insereLote($inCodLote, $boTransacao);
    }

    $stSql = $this->montaInclusaoPorPl();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta sql para Inclusao
    * @access Private
    * @return String
*/
function montaInclusaoPorPl()
{
    $stSQL  = "SELECT contabilidade.fn_insere_lancamentos (  ";
    $stSQL .= "     '".$this->getDado('exercicio')."' ";
    $stSQL .= "     ,".$this->getDado('cod_plano_deb');
    $stSQL .= "     ,".$this->getDado('cod_plano_cred');
    $stSQL .= "     ,'".$this->getDado('cod_estrutural_deb')."' ";
    $stSQL .= "     ,'".$this->getDado('cod_estrutural_cred')."' ";
    $stSQL .= "     ,".$this->getDado('vl_lancamento')." ";
    $stSQL .= "     ,".$this->getDado('cod_lote')." ";
    $stSQL .= "     ,".$this->getDado('cod_entidade')." ";
    $stSQL .= "     ,".$this->getDado('cod_historico')." ";
    $stSQL .= "     ,'".$this->getDado('tipo')."' ";
    $stSQL .= "     ,'".$this->getDado('complemento')."' ";
    $stSQL .= "     ) as sequencia ";

    return $stSQL;
}

function insereLote(&$inCodLote, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaInsereLote();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $inCodLote = $rsRecordSet->getCampo('cod_lote');
    $this->setDado('cod_lote', $inCodLote);

    return $obErro;
}

function montaInsereLote()
{
    $stSql  = " SELECT  \n";
    $stSql .= "      contabilidade.fn_insere_lote( ";
    $stSql .= " '".$this->getDado('exercicio')."' ";
    $stSql .= " ,".$this->getDado('cod_entidade');
    $stSql .= " ,'".$this->getDado('tipo')."' ";
    $stSql .= " ,'".$this->getDado('nom_lote')."' ";
    $stSql .= " ,'".$this->getDado('dt_lote')."' ";
    $stSql .= " ) as cod_lote \n";

    return $stSql ;
}

/**
    * Monta sql para recuperaRelacionamento
    * @access Private
    * @return String
*/
function montaRecuperaRelacionamento()
{
    $stSQL  = "SELECT                                            \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    to_char(lo.dt_lote, 'dd/mm/yyyy') as dt_lote, \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    abs( la.vl_lancamento ) as vl_lancamento,     \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico                               \n";
    $stSQL .= "FROM                                              \n";
    $stSQL .= "    contabilidade.lancamento as l,            \n";
    $stSQL .= "    contabilidade.valor_lancamento as la,     \n";
    $stSQL .= "    contabilidade.historico_contabil as h,    \n";
    $stSQL .= "    contabilidade.lote as lo                  \n";
    $stSQL .= "WHERE                                             \n";
    $stSQL .= "    lo.cod_lote = l.cod_lote AND                  \n";
    $stSQL .= "    lo.exercicio = l.exercicio AND                \n";
    $stSQL .= "    lo.tipo = l.tipo AND                          \n";
    $stSQL .= "    lo.cod_entidade = l.cod_entidade AND          \n";
    $stSQL .= "    l.cod_historico = h.cod_historico AND         \n";
    $stSQL .= "    l.exercicio = h.exercicio AND                 \n";
    $stSQL .= "    l.sequencia = la.sequencia AND                \n";
    $stSQL .= "    l.exercicio = la.exercicio AND                \n";
    $stSQL .= "    l.tipo = la.tipo AND                          \n";
    $stSQL .= "    l.cod_lote = la.cod_lote AND                  \n";
    $stSQL .= "    l.cod_entidade  = la.cod_entidade             \n";
    $stSQL .= $this->getDado("stFiltro");
    $stSQL .= "GROUP BY                                          \n";
    $stSQL .= "    abs( vl_lancamento ),                         \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    dt_lote,                                      \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico                               \n";

    return $stSQL;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelacionamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamento().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta sql para recuperaRelacionamento
    * @access Private
    * @return String
*/
function montaRecuperaValorLancamento()
{
    $stSQL  = "SELECT                                            \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    to_char(lo.dt_lote, 'dd/mm/yyyy') as dt_lote, \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    la.tipo_valor,                                \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    abs( la.vl_lancamento ) as vl_lancamento,     \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico                               \n";
    $stSQL .= "FROM                                              \n";
    $stSQL .= "    contabilidade.lancamento as l,                \n";
    $stSQL .= "    contabilidade.valor_lancamento as la,         \n";
    $stSQL .= "    contabilidade.historico_contabil as h,        \n";
    $stSQL .= "    contabilidade.lote as lo                      \n";
    $stSQL .= "WHERE                                             \n";
    $stSQL .= "    lo.cod_lote = l.cod_lote AND                  \n";
    $stSQL .= "    lo.exercicio = l.exercicio AND                \n";
    $stSQL .= "    lo.tipo = l.tipo AND                          \n";
    $stSQL .= "    lo.cod_entidade = l.cod_entidade AND          \n";
    $stSQL .= "    l.cod_historico = h.cod_historico AND         \n";
    $stSQL .= "    l.exercicio = h.exercicio AND                 \n";
    $stSQL .= "    l.sequencia = la.sequencia AND                \n";
    $stSQL .= "    l.exercicio = la.exercicio AND                \n";
    $stSQL .= "    l.tipo = la.tipo AND                          \n";
    $stSQL .= "    l.cod_lote = la.cod_lote AND                  \n";
    $stSQL .= "    l.cod_entidade = la.cod_entidade              \n";
    $stSQL .= $this->getDado("stFiltro");
    $stSQL .= "GROUP BY                                    adicionada aspas no stCodEntidade, para ir como uma string      \n";
    $stSQL .= "    abs( vl_lancamento ),                         \n";
    $stSQL .= "    lo.cod_lote,                                  \n";
    $stSQL .= "    dt_lote,                                      \n";
    $stSQL .= "    lo.nom_lote,                                  \n";
    $stSQL .= "    l.sequencia,                                  \n";
    $stSQL .= "    l.cod_historico,                              \n";
    $stSQL .= "    l.tipo,                                       \n";
    $stSQL .= "    l.cod_entidade,                               \n";
    $stSQL .= "    h.cod_historico,                              \n";
    $stSQL .= "    h.nom_historico                               \n";

    return $stSQL;
}

/**
    * Monta sql para recuperaRelatorio
    * @access Private
    * @return String
*/
function montaRecuperaRelatorio()
{
    $stSql  = "SELECT                                                             \n";
    $stSql .= "        tabela.*,                                                  \n";
    $stSql .= "        pc.nom_conta,                                              \n";
    $stSql .= "        pc.cod_estrutural                                          \n";
    $stSql .= "FROM (                                                             \n";
    $stSql .= "    SELECT                                                         \n";
    $stSql .= "            l.cod_lote,                                            \n";
    $stSql .= "            lo.nom_lote,                                           \n";
    $stSql .= "            l.sequencia,                                           \n";
    $stSql .= "            l.tipo,                                                \n";
    $stSql .= "            l.cod_historico,                                       \n";
    $stSql .= "            l.exercicio,                                           \n";
    $stSql .= "            l.cod_entidade,                                        \n";
    $stSql .= "            l.complemento,                                         \n";
    $stSql .= "    hc.nom_historico||' '||l.complemento||CASE WHEN (tret.cod_recibo_extra IS NOT NULL)\n";
    $stSql .= "       OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL)\n";
    $stSql .= "          THEN ' - Recibo: '                                       \n";
    $stSql .= "             ||coalesce(cast(tret.cod_recibo_extra as varchar),'') \n";
    $stSql .= "        --   ||coalesce(cast(tret2.cod_recibo_extra as varchar),'')\n";
    $stSql .= "             ||coalesce(cast(ret.cod_recibo_extra as varchar),'')  \n";
    $stSql .= "          ELSE ' '                                                 \n";
    $stSql .= "       END                                                         \n";
    $stSql .= "       ||CASE WHEN (tt.observacao IS NOT NULL)                     \n";
    $stSql .= "                   OR (tte.observacao IS NOT NULL)                 \n";
    $stSql .= "                   OR (tarrec.observacao IS NOT NULL)            \n";
    $stSql .= "           THEN ' - '||coalesce(tt.observacao, '')                 \n";
    $stSql .= "                     ||coalesce(tte.observacao,'')                 \n";
//    $stSql .= "                     ||coalesce(tarrec.observacao,'')            \n";
    $stSql .= "           ELSE ' '                                                \n";
    $stSql .= "        END                                                        \n";
    $stSql .= "     AS nom_historico,                                             \n";
    $stSql .= "            CASE WHEN (tret.cod_recibo_extra is null ) THEN        \n";
    $stSql .= "                       tret2.cod_recibo_extra                      \n";
    $stSql .= "                 ELSE                                              \n";
    $stSql .= "                       tret.cod_recibo_extra                       \n";
    $stSql .= "            END AS cod_recibo_extra,                               \n";
    $stSql .= "            vl.vl_lancamento,                                      \n";
    $stSql .= "            vl.tipo_valor,                                         \n";
    $stSql .= "            cgm.nom_cgm,                                           \n";
    $stSql .= "            to_char( lo.dt_lote, 'dd/mm/yyyy') AS dt_lote,         \n";
    $stSql .= "            CASE WHEN cc.cod_plano is not null THEN cc.cod_plano   \n";
    $stSql .= "                 ELSE cd.cod_plano                                 \n";
    $stSql .= "            END AS cod_plano                                       \n";
    $stSql .= "    FROM                                                           \n";
    $stSql .= "           contabilidade.lancamento         AS l,                  \n";
    $stSql .= "           contabilidade.lote               AS lo,                 \n";
    $stSql .= "           contabilidade.historico_contabil AS hc,                 \n";
    $stSql .= "           orcamento.entidade               as en,                 \n";
    $stSql .= "           sw_cgm                           as cgm,                \n";
    $stSql .= "           contabilidade.valor_lancamento   AS vl                  \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_credito AS cc                       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cc.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cc.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cc.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cc.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cc.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cc.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "    LEFT JOIN                                                      \n";
    $stSql .= "           contabilidade.conta_debito AS cd                        \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                cd.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                cd.tipo         = vl.tipo           AND            \n";
    $stSql .= "                cd.sequencia    = vl.sequencia      AND            \n";
    $stSql .= "                cd.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                cd.tipo_valor   = vl.tipo_valor     AND            \n";
    $stSql .= "                cd.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "           tesouraria.transferencia AS tt                          \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tt.cod_lote     = vl.cod_lote       AND            \n";
    $stSql .= "                tt.tipo         = vl.tipo           AND            \n";
    $stSql .= "                tt.exercicio    = vl.exercicio      AND            \n";
    $stSql .= "                tt.cod_entidade = vl.cod_entidade                  \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "     LEFT JOIN                                                     \n";
    $stSql .= "            tesouraria.transferencia_estornada  AS tte             \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                tte.cod_lote_estorno  = vl.cod_lote AND            \n";
    $stSql .= "                tte.tipo         = vl.tipo           AND           \n";
    $stSql .= "                tte.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                tte.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                      \n";
    $stSql .= "      LEFT JOIN tesouraria.recibo_extra_transferencia AS ret       \n";
    $stSql .= "            ON (                                                   \n";
    $stSql .= "                ret.cod_lote     = vl.cod_lote       AND           \n";
    $stSql .= "                ret.tipo         = vl.tipo           AND           \n";
    $stSql .= "                ret.exercicio    = vl.exercicio      AND           \n";
    $stSql .= "                ret.cod_entidade = vl.cod_entidade                 \n";
    $stSql .= "            )                                                    \n";
    $stSql .= "     LEFT JOIN (SELECT tbl.exercicio                                      \n";
    $stSql .= "               ,tbl.cod_entidade                                          \n";
    $stSql .= "               ,tbll.tipo                                                  \n";
    $stSql .= "               ,tbll.cod_lote                                              \n";
    $stSql .= "               ,ta.observacao                                             \n";
    $stSql .= "         FROM                                                             \n";
    $stSql .= "                tesouraria.boletim_liberado AS tbl                        \n";
    $stSql .= "         LEFT JOIN tesouraria.arrecadacao AS ta                           \n";
    $stSql .= "                   ON  ta.exercicio    = tbl.exercicio                    \n";
    $stSql .= "                   AND ta.cod_entidade = tbl.cod_entidade                 \n";
    $stSql .= "                   AND ta.cod_boletim      = tbl.cod_boletim                    \n";
    $stSql .= "         JOIN tesouraria.boletim_liberado_lote as tbll               \n";
    $stSql .= "             	  ON tbll.cod_boletim = tbl.cod_boletim                      \n";
    $stSql .= "                  AND tbll.cod_entidade = tbl.cod_entidade               \n";
    $stSql .= "                	 AND tbll.exercicio = tbll.exercicio                        \n";
    $stSql .= "                	 AND tbll.timestamp_liberado = tbll.timestamp_liberado      \n";
    $stSql .= "                	 AND tbll.timestamp_fechamento = tbll.timestamp_fechamento  \n";
    $stSql .= "         WHERE                                                            \n";
    $stSql .= "                ta.exercicio    = tbl.exercicio                           \n";
    $stSql .= "                AND ta.cod_entidade = tbl.cod_entidade                    \n";
    $stSql .= "                AND ta.cod_boletim  = tbl.cod_boletim                     \n";
    $stSql .= "        ) AS tarrec                                                       \n";
    $stSql .= "          ON (                                                            \n";
    $stSql .= "             tarrec.cod_lote    = vl.cod_lote    AND                      \n";
    $stSql .= "            tarrec.tipo         = vl.tipo        AND                      \n";
    $stSql .= "            tarrec.exercicio    = vl.exercicio   AND                      \n";
    $stSql .= "            tarrec.cod_entidade = vl.cod_entidade                         \n";
    $stSql .= "            )                                                           \n";
    $stSql .= "     LEFT JOIN (SELECT                                                              \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = '".$this->getDado('stExercicio')."' AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stCodigoEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote_estorno,               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret                                                               \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret.cod_lote_estorno  = vl.cod_lote    AND                        \n";
    $stSql .= "                 tret.tipo         = vl.tipo        AND                             \n";
    $stSql .= "                 tret.exercicio    = vl.exercicio   AND                             \n";
    $stSql .= "                 tret.cod_entidade = vl.cod_entidade                                \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "      LEFT JOIN (SELECT                                                             \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote,                       \n";
    $stSql .= "                 tesouraria.transferencia_estornada.tipo,                           \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio,                      \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade,                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_recibo_extra             \n";
    $stSql .= "             FROM                                                                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada                                 \n";
    $stSql .= "             LEFT JOIN tesouraria.recibo_extra_transferencia ON (                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.exercicio =                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio AND                   \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_entidade =               \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade  AND               \n";
    $stSql .= "                 tesouraria.recibo_extra_transferencia.cod_lote =                   \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_lote                        \n";
    $stSql .= "                 )                                                                  \n";
    $stSql .= "             WHERE                                                                  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.exercicio = '".$this->getDado('stExercicio')."' AND  \n";
    $stSql .= "                 tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado('stCodigoEntidade').") \n";
    $stSql .= "             GROUP BY                                                               \n";
    $stSql .= "                     tesouraria.transferencia_estornada.cod_lote,                   \n";
    $stSql .= "                      tesouraria.transferencia_estornada.tipo,                      \n";
    $stSql .= "                      tesouraria.transferencia_estornada.exercicio,                 \n";
    $stSql .= "                      tesouraria.transferencia_estornada.cod_entidade,              \n";
    $stSql .= "                      tesouraria.recibo_extra_transferencia.cod_recibo_extra        \n";
    $stSql .= "                                                                                    \n";
    $stSql .= "            ) AS tret2                                                              \n";
    $stSql .= "              ON (                                                                  \n";
    $stSql .= "                 tret2.cod_lote     = vl.cod_lote     AND                           \n";
    $stSql .= "                 tret2.tipo         = vl.tipo         AND                           \n";
    $stSql .= "                 tret2.exercicio    = vl.exercicio    AND                           \n";
    $stSql .= "            tret2.cod_entidade = vl.cod_entidade                                    \n";
    $stSql .= "            )                                                                       \n";
    $stSql .= "    WHERE                                                          \n";
    $stSql .= "            vl.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            vl.tipo          = l.tipo            AND               \n";
    $stSql .= "            vl.sequencia     = l.sequencia       AND               \n";
    $stSql .= "            vl.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            vl.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            lo.cod_lote      = l.cod_lote        AND               \n";
    $stSql .= "            lo.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            lo.tipo          = l.tipo            AND               \n";
    $stSql .= "            lo.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            hc.cod_historico = l.cod_historico   AND               \n";
    $stSql .= "            hc.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            en.cod_entidade  = l.cod_entidade    AND               \n";
    $stSql .= "            en.exercicio     = l.exercicio       AND               \n";
    $stSql .= "            cgm.numcgm       = en.numcgm                           \n";
    $stSql .= $this->getDado( "stFiltro" );
    $stSql .= "    ORDER BY                                                       \n";
    $stSql .= "            to_date(dt_lote::varchar,'yyyy-mm-dd'),                         \n";
    $stSql .= "            l.cod_lote,                                            \n";
    $stSql .= "            l.cod_entidade,                                        \n";
    $stSql .= "            l.tipo,                                                \n";
    $stSql .= "            l.sequencia ASC,                                       \n";
    $stSql .= "            vl.vl_lancamento DESC,                                 \n";
    $stSql .= "            vl.tipo_valor DESC                                     \n";
    $stSql .= "    ) AS tabela,                                                   \n";
    $stSql .= "    contabilidade.plano_analitica AS pa,                           \n";
    $stSql .= "    contabilidade.plano_conta     AS pc                            \n";
    $stSql .= "WHERE                                                              \n";
    $stSql .= "    tabela.cod_plano = pa.cod_plano     AND                        \n";
    $stSql .= "    tabela.exercicio = pa.exercicio     AND                        \n";
    $stSql .= "    pa.cod_conta     = pc.cod_conta     AND                        \n";
    $stSql .= "    pa.exercicio     = pc.exercicio                                \n";
    $stSql .= "GROUP BY                                                         \n";
    $stSql .= "            tabela.cod_lote,                                     \n";
    $stSql .= "            tabela.nom_lote,                                     \n";
    $stSql .= "            tabela.sequencia,                                    \n";
    $stSql .= "            tabela.tipo,                                         \n";
    $stSql .= "            tabela.cod_historico,                                \n";
    $stSql .= "            tabela.nom_historico,                                \n";
    $stSql .= "            tabela.exercicio,                                    \n";
    $stSql .= "            tabela.cod_entidade,                                 \n";
    $stSql .= "            tabela.complemento,                                  \n";
    $stSql .= "            tabela.cod_recibo_extra,                             \n";
    $stSql .= "            tabela.vl_lancamento,                                \n";
    $stSql .= "            tabela.tipo_valor,                                   \n";
    $stSql .= "            tabela.nom_cgm,                                      \n";
    $stSql .= "            tabela.dt_lote,                                      \n";
    $stSql .= "            tabela.cod_plano,                                    \n";
    $stSql .= "            pc.nom_conta,                                        \n";
    $stSql .= "            pc.cod_estrutural                                    \n";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaRelatorio.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelatorio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "", $stExercicio = "", $stCodigoEntidade = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelatorio().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta sql para recuperaDadosExportacao
    * @access Private
    * @return String
*/

function montaRecuperaDadosExportacao()
{
    $stSql  = " SELECT                                                                                                                                                       \n";
    $stSql .= "      replace(pc.cod_estrutural,'.','') AS cod_estrutural,                                                                                                    \n";
    $stSql .= "      replace(abs(tcers.totaliza_valor_movimentacao(pc.cod_estrutural,pc.exercicio,'".$this->getDado( "stCodEntidades" )."','D')),'.','') AS valor_movimentacao,  \n";
//  $stSql .= "      CASE WHEN substr(pc.cod_estrutural, 1, 5) = '1.1.2'  THEN  'R'                                                                                        \n";
//  $stSql .= "           WHEN substr(pc.cod_estrutural, 1, 3) = '2.1'    THEN  'D'                                                                                        \n";
//  $stSql .= "      END AS identificador,                                                                                                                                 \n";
    $stSql .= "      'D' as identificador,                                                                                                                                   \n";
    $stSql .= "      rd.classificacao                                                                                                                                        \n";
    $stSql .= " FROM                                                                                                                                                         \n";
    $stSql .= "      tcers.rd_extra              AS rd,                                                                                                                      \n";
    $stSql .= "      contabilidade.plano_conta   AS pc                                                                                                                       \n";
    $stSql .= " WHERE                                                                                                                                                        \n";
    $stSql .= "      rd.cod_conta    = pc.cod_conta                              AND                                                                                         \n";
    $stSql .= "      rd.exercicio    = pc.exercicio                              AND                                                                                         \n";
    $stSql .= "      pc.exercicio    = '". $this->getDado( "stExercicio" ) ."'                                                                                               \n";
    $stSql .= " UNION                                                                                                                                                        \n";
    $stSql .= " SELECT                                                                                                                                                       \n";
    $stSql .= "      replace(pc.cod_estrutural,'.','') AS cod_estrutural,                                                                                                    \n";
    $stSql .= "      replace(abs(tcers.totaliza_valor_movimentacao(pc.cod_estrutural,pc.exercicio,'".$this->getDado( "stCodEntidades" )."','R')),'.','') AS valor_movimentacao,  \n";
    $stSql .= "      'R' as identificador,                                                                                                                                   \n";
    $stSql .= "      rd.classificacao                                                                                                                                        \n";
    $stSql .= " FROM                                                                                                                                                         \n";
    $stSql .= "      tcers.rd_extra              AS rd,                                                                                                                      \n";
    $stSql .= "      contabilidade.plano_conta   AS pc                                                                                                                       \n";
    $stSql .= " WHERE                                                                                                                                                        \n";
    $stSql .= "      rd.cod_conta    = pc.cod_conta                              AND                                                                                         \n";
    $stSql .= "      rd.exercicio    = pc.exercicio                              AND                                                                                         \n";
    $stSql .= "      pc.exercicio    = '". $this->getDado( "stExercicio" ) ."'                                                                                               \n";

    return $stSql;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExportacao().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Mesma função do recuperaTodos, mas efetua chamada a outro método para montar o SQL, o método montaExclusaoSaldosImplantacao
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaExclusaoSaldosImplantacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaExclusaoSaldosImplantacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaExclusaoSaldosImplantacao()
{
    $stSql  = " delete from contabilidade.conta_debito      where exercicio='". $this->getDado( "stExercicio" ) ."' and tipo='I';\n";
    $stSql .= " delete from contabilidade.conta_credito     where exercicio='". $this->getDado( "stExercicio" ) ."' and tipo='I';\n";
    $stSql .= " delete from contabilidade.valor_lancamento  where exercicio='". $this->getDado( "stExercicio" ) ."' and tipo='I';\n";
    $stSql .= " delete from contabilidade.lancamento        where exercicio='". $this->getDado( "stExercicio" ) ."' and tipo='I';\n";
    $stSql .= " delete from contabilidade.lote              where exercicio='". $this->getDado( "stExercicio" ) ."' and tipo='I';\n";

    return $stSql;
}

function recuperaSistemaContabilCreditoDebito(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaSistemaContabilCreditoDebito();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSistemaContabilCreditoDebito()
{
    $stSql  = "SELECT                                                    \n";
    $stSql .= "     pcc.cod_conta   as cod_conta_credito                 \n";
    $stSql .= "    ,pcc.exercicio   as exercicio_credito                 \n";
    $stSql .= "    ,scc.cod_sistema as cod_sistema_credito               \n";
    $stSql .= "    ,scc.nom_sistema as nom_sistema_credito               \n";
    $stSql .= "    ,pac.cod_plano   as cod_plano_credito                 \n";
    $stSql .= "    ,pcd.cod_conta   as cod_conta_debito                  \n";
    $stSql .= "    ,pcd.exercicio   as exercicio_debito                  \n";
    $stSql .= "    ,scd.cod_sistema as cod_sistema_debito                \n";
    $stSql .= "    ,scd.nom_sistema as nom_sistema_debito                \n";
    $stSql .= "    ,pad.cod_plano   as cod_plano_debito                  \n";
    $stSql .= "FROM                                                      \n";
    $stSql .= "     contabilidade.plano_conta      as pcc                \n";
    $stSql .= "    ,contabilidade.plano_conta      as pcd                \n";
    $stSql .= "    ,contabilidade.sistema_contabil as scc                \n";
    $stSql .= "    ,contabilidade.sistema_contabil as scd                \n";
    $stSql .= "    ,contabilidade.plano_analitica  as pac                \n";
    $stSql .= "    ,contabilidade.plano_analitica  as pad                \n";
    $stSql .= "WHERE                                                     \n";
    $stSql .= "    pcc.cod_sistema = scc.cod_sistema                     \n";
    $stSql .= "AND pcc.exercicio   = scc.exercicio                       \n";
    $stSql .= "AND pcc.cod_conta   = pac.cod_conta                       \n";
    $stSql .= "AND pcc.exercicio   = pac.exercicio                       \n";
    $stSql .= "AND pac.cod_plano   = ".$this->getDado("cod_plano_credito")."\n";
    $stSql .= "AND pac.exercicio   = '".$this->getDado("stExercicio")."' \n";
    $stSql .= "AND pcd.cod_sistema = scd.cod_sistema                     \n";
    $stSql .= "AND pcd.exercicio   = scd.exercicio                       \n";
    $stSql .= "AND pcd.cod_conta   = pad.cod_conta                       \n";
    $stSql .= "AND pcd.exercicio   = pad.exercicio                       \n";
    $stSql .= "AND pad.cod_plano   = ".$this->getDado("cod_plano_debito")."\n";
    $stSql .= "AND pad.exercicio   = '".$this->getDado("stExercicio")."' \n";

    return $stSql;
}

function recuperaLancamento(&$rsRecordSet, $stFiltro = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaLancamento().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaLancamento()
{
    $stSQL  = "SELECT                                                                               \n";
    $stSQL .= "    lo.cod_lote,                                                                     \n";
    $stSQL .= "    to_char(lo.dt_lote, 'dd/mm/yyyy') as dt_lote,                                    \n";
    $stSQL .= "    lo.nom_lote,                                                                     \n";
    $stSQL .= "    l.sequencia,                                                                     \n";
    $stSQL .= "    l.cod_historico,                                                                 \n";
    $stSQL .= "    l.tipo,                                                                          \n";
    $stSQL .= "    la.tipo_valor,                                                                   \n";
    $stSQL .= "    l.complemento,                                                                   \n";
    $stSQL .= "    l.cod_entidade,                                                                  \n";
    $stSQL .= "    l.exercicio,                                                                     \n";
    $stSQL .= "    abs( la.vl_lancamento ) as vl_lancamento,                                        \n";
    $stSQL .= "    h.cod_historico,                                                                 \n";
    $stSQL .= "    h.nom_historico,                                                                 \n";
    $stSQL .= "    CASE WHEN (la.tipo_valor = 'C'  ) THEN                                           \n";
    $stSQL .= "              credito.cod_plano                                                      \n";
    $stSQL .= "         ELSE                                                                        \n";
    $stSQL .= "              debito.cod_plano                                                       \n";
    $stSQL .= "       END AS cod_plano,                                                             \n";
    $stSQL .= "    CASE WHEN (la.tipo_valor = 'C'  ) THEN                                           \n";
    $stSQL .= "              credito.nom_conta                                                      \n";
    $stSQL .= "         ELSE                                                                        \n";
    $stSQL .= "              debito.nom_conta                                                       \n";
    $stSQL .= "       END AS nom_conta                                                              \n";
    $stSQL .= "FROM                                                                                 \n";
    $stSQL .= "    contabilidade.lancamento as l,                                                   \n";
    $stSQL .= "    contabilidade.valor_lancamento as la                                             \n";
    $stSQL .= "    LEFT JOIN(SELECT pc.nom_conta                                                    \n";
    $stSQL .= "                   , pa.cod_plano                                                    \n";
    $stSQL .= "                   , cd.exercicio                                                    \n";
    $stSQL .= "                   , cd.cod_entidade                                                 \n";
    $stSQL .= "                   , cd.tipo                                                         \n";
    $stSQL .= "                   , cd.cod_lote                                                     \n";
    $stSQL .= "                   , cd.sequencia                                                    \n";
    $stSQL .= "                   , cd.tipo_valor                                                   \n";
    $stSQL .= "                FROM contabilidade.conta_debito as cd                                \n";
    $stSQL .= "                     JOIN contabilidade.plano_analitica as pa                        \n";
    $stSQL .= "                       ON (     pa.exercicio = cd.exercicio                          \n";
    $stSQL .= "                            AND pa.cod_plano = cd.cod_plano )                        \n";
    $stSQL .= "                     JOIN contabilidade.plano_conta as pc                            \n";
    $stSQL .= "                       ON (     pc.exercicio = pa.exercicio                          \n";
    $stSQL .= "                            AND pc.cod_conta = pa.cod_conta  )  ) as debito          \n";
    $stSQL .= "           ON(     debito.exercicio    = la.exercicio                                \n";
    $stSQL .= "               AND debito.cod_entidade = la.cod_entidade                             \n";
    $stSQL .= "               AND debito.tipo         = la.tipo                                     \n";
    $stSQL .= "               AND debito.cod_lote     = la.cod_lote                                 \n";
    $stSQL .= "               AND debito.sequencia    = la.sequencia                                \n";
    $stSQL .= "               AND debito.tipo_valor   = la.tipo_valor )                             \n";
    $stSQL .= "    LEFT JOIN(SELECT pc.nom_conta                                                    \n";
    $stSQL .= "                   , pa.cod_plano                                                    \n";
    $stSQL .= "                   , cd.exercicio                                                    \n";
    $stSQL .= "                   , cd.cod_entidade                                                 \n";
    $stSQL .= "                   , cd.tipo                                                         \n";
    $stSQL .= "                   , cd.cod_lote                                                     \n";
    $stSQL .= "                   , cd.sequencia                                                    \n";
    $stSQL .= "                   , cd.tipo_valor                                                   \n";
    $stSQL .= "                FROM contabilidade.conta_credito as cd                               \n";
    $stSQL .= "                     JOIN contabilidade.plano_analitica as pa                        \n";
    $stSQL .= "                       ON (     pa.exercicio = cd.exercicio                          \n";
    $stSQL .= "                            AND pa.cod_plano = cd.cod_plano )                        \n";
    $stSQL .= "                     JOIN contabilidade.plano_conta as pc                            \n";
    $stSQL .= "                       ON (     pc.exercicio = pa.exercicio                          \n";
    $stSQL .= "                            AND pc.cod_conta = pa.cod_conta  )  ) as credito         \n";
    $stSQL .= "           ON(     credito.exercicio    = la.exercicio                               \n";
    $stSQL .= "               AND credito.cod_entidade = la.cod_entidade                            \n";
    $stSQL .= "               AND credito.tipo         = la.tipo                                    \n";
    $stSQL .= "               AND credito.cod_lote     = la.cod_lote                                \n";
    $stSQL .= "               AND credito.sequencia    = la.sequencia                               \n";
    $stSQL .= "               AND credito.tipo_valor   = la.tipo_valor ),                           \n";
    $stSQL .= "    contabilidade.historico_contabil as h,                                           \n";
    $stSQL .= "    contabilidade.lote as lo                                                         \n";
    $stSQL .= "WHERE                                                                                \n";
    $stSQL .= "    lo.cod_lote = l.cod_lote AND                                                     \n";
    $stSQL .= "    lo.exercicio = l.exercicio AND                                                   \n";
    $stSQL .= "    lo.tipo = l.tipo AND                                                             \n";
    $stSQL .= "    lo.cod_entidade = l.cod_entidade AND                                             \n";
    $stSQL .= "    l.cod_historico = h.cod_historico AND                                            \n";
    $stSQL .= "    l.exercicio = h.exercicio AND                                                    \n";
    $stSQL .= "    l.sequencia = la.sequencia AND                                                   \n";
    $stSQL .= "    l.exercicio = la.exercicio AND                                                   \n";
    $stSQL .= "    l.tipo = la.tipo AND                                                             \n";
    $stSQL .= "    l.cod_lote = la.cod_lote AND                                                     \n";
    $stSQL .= "    l.cod_entidade = la.cod_entidade                                                 \n";

    return $stSQL;
}

function recuperaValorLancamento(&$rsRecordSet, $stFiltro = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaValorLancamento().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
