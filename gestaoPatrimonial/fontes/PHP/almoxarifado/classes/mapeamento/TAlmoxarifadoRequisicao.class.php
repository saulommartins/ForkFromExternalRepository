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
    * Classe de mapeamento da tabela ALMOXARIFADO.REQUISICAO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 23059 $
    $Name$
    $Author: tonismar $
    $Date: 2007-06-04 17:29:27 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-03.03.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.REQUISICAO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoRequisicao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoRequisicao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.requisicao');

    $this->setCampoCod('cod_requisicao');
    $this->setComplementoChave('exercicio,cod_almoxarifado');

    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('cod_requisicao','integer',true,'',true,false);
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,true);
    $this->AddCampo('cgm_solicitante','integer',true,'',false,true);
    $this->AddCampo('cgm_requisitante','integer',true,'',false,true);
    $this->AddCampo('dt_requisicao','date',false,'',false,false);
    $this->AddCampo('observacao','text',false,'',false,false);

}

function recuperaRequisicaoItemConsultar(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRequisicaoItemConsultar().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRequisicaoItemConsultar()
{
    $stSql .= "    SELECT req.exercicio                                                                        \n";
    $stSql .= "         , req.cod_almoxarifado                                                                 \n";
    $stSql .= "         , req.cod_requisicao                                                                   \n";
    $stSql .= "         , req.observacao                                                                       \n";
    $stSql .= "         , to_char(req.dt_requisicao, 'dd/mm/yyyy') as dt_requisicao                            \n";
    $stSql .= "         , req.cgm_solicitante                                                                  \n";
    $stSql .= "         , cgm2.nom_cgm as nom_solicitante                                                      \n";
    $stSql .= "         , req.cgm_requisitante                                                                 \n";
    $stSql .= "         , cgm3.nom_cgm as nom_requisitante                                                     \n";
    $stSql .= "         , cgm.nom_cgm                                                                          \n";
    $stSql .= "      FROM almoxarifado.requisicao AS req                                                       \n";
    $stSql .= "INNER JOIN almoxarifado.requisicao_item AS reqi                                                 \n";
    $stSql .= "        ON req.exercicio = reqi.exercicio                                                       \n";
    $stSql .= "       AND req.cod_almoxarifado = reqi.cod_almoxarifado                                         \n";
    $stSql .= "       AND req.cod_requisicao = reqi.cod_requisicao                                             \n";
    $stSql .= "INNER JOIN almoxarifado.almoxarifado AS aa                                                      \n";
    $stSql .= "        ON req.cod_almoxarifado = aa.cod_almoxarifado                                           \n";
    $stSql .= "INNER JOIN sw_cgm AS cgm                                                                        \n";
    $stSql .= "        ON cgm.numcgm = aa.cgm_almoxarifado                                                     \n";
    $stSql .= "INNER JOIN sw_cgm AS cgm2                                                                       \n";
    $stSql .= "        ON cgm2.numcgm = req.cgm_solicitante                                                    \n";
    $stSql .= "INNER JOIN sw_cgm AS cgm3                                                                       \n";
    $stSql .= "        ON cgm3.numcgm = req.cgm_requisitante                                                   \n";

    return $stSql;
}
function recuperaRequisicaoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRequisicaoItem().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRequisicaoItem()
{
$stSql .= " select                                                                                    \n";
$stSql .= "     req.exercicio                                                                         \n";
$stSql .= "     ,req.cod_almoxarifado                                                                 \n";
$stSql .= "     ,req.cod_requisicao                                                                   \n";
$stSql .= "     ,req.cgm_requisitante                                                                 \n";
$stSql .= "     ,req.cgm_solicitante                                                                  \n";
$stSql .= "     ,req.dt_requisicao                            \n";
$stSql .= "     ,req.observacao                                                                       \n";
//$stSql .= "     ,req.cod_item                                                                         \n";
//$stSql .= "     ,req.cod_marca                                                                        \n";
//$stSql .= "     ,req.cod_centro                                                                       \n";
$stSql .= "     ,req.motivo                                                                           \n";
$stSql .= "     ,cgm.nom_cgm                                                                          \n";
$stSql .= "     ,cgm.numcgm                                                                           \n";
$stSql .= "     ,cgm2.nom_cgm as nom_solicitante                                                      \n";
$stSql .= "     ,cgm2.numcgm as num_solicitante                                                       \n";
//$stSql .= "     ,req.qtd_anulada                                                                      \n";
//$stSql .= "     ,req.qtd_requisitada                                                                  \n";
//$stSql .= "     ,lanc.qtd_atendida                                                                    \n";
//$stSql .= "     ,lanc2.qtd_devolvida                                                                  \n";
//$stSql .= "     ,lanc.cod_lancamento                                                                  \n";
$stSql .= " from                                                                                      \n";
$stSql .= "     (select                                                                               \n";
$stSql .= "         ar.exercicio                                                                      \n";
$stSql .= "         ,ar.cod_almoxarifado                                                              \n";
$stSql .= "         ,ar.cod_requisicao                                                                \n";
$stSql .= "         ,ar.cgm_requisitante                                                              \n";
$stSql .= "         ,ar.cgm_solicitante                                                               \n";
$stSql .= "         ,to_char(ar.dt_requisicao, 'dd/mm/yyyy' ) as dt_requisicao                        \n";
$stSql .= "         ,ar.observacao                                                                    \n";
$stSql .= "         ,ari.cod_item                                                                     \n";
$stSql .= "         ,ari.cod_marca                                                                    \n";
$stSql .= "         ,ari.cod_centro                                                                   \n";
$stSql .= "         ,sum(ari.quantidade) as qtd_requisitada                                           \n";
$stSql .= "         ,anu.motivo                                                                       \n";
$stSql .= "         ,anu.qtd_anulada                                                                  \n";
$stSql .= "     from                                                                                  \n";
$stSql .= "         almoxarifado.requisicao_item ari                                                 \n";
$stSql .= "         left join                                                                         \n";
$stSql .= "             (select                                                                       \n";
$stSql .= "                  ara.cod_requisicao                                                       \n";
$stSql .= "                 ,ara.cod_almoxarifado                                                     \n";
$stSql .= "                 ,ara.exercicio                                                            \n";
$stSql .= "                 ,ara.motivo                                                               \n";
$stSql .= "                 ,aria.cod_item                                                            \n";
$stSql .= "                 ,aria.cod_marca                                                           \n";
$stSql .= "                 ,aria.cod_centro                                                          \n";
$stSql .= "                 ,sum(aria.quantidade) as qtd_anulada                                      \n";
$stSql .= "              from                                                                         \n";
$stSql .= "                 almoxarifado.requisicao_anulacao as ara                                   \n";
$stSql .= "                 ,almoxarifado.requisicao_itens_anulacao as aria                           \n";
$stSql .= "              where                                                                        \n";
$stSql .= "                 ara.exercicio = aria.exercicio                                            \n";
$stSql .= "                 and ara.cod_almoxarifado = aria.cod_almoxarifado                          \n";
$stSql .= "                 and ara.cod_requisicao = aria.cod_requisicao                              \n";
$stSql .= "              group by                                                                     \n";
$stSql .= "                  ara.cod_requisicao                                                       \n";
$stSql .= "                 ,ara.cod_almoxarifado                                                     \n";
$stSql .= "                 ,ara.exercicio                                                            \n";
$stSql .= "                 ,ara.motivo                                                               \n";
$stSql .= "                 ,aria.cod_item                                                            \n";
$stSql .= "                 ,aria.cod_marca                                                           \n";
$stSql .= "                 ,aria.cod_centro                                                          \n";
$stSql .= "             ) as anu                                                                      \n";
$stSql .= "         on                                                                                \n";
$stSql .= "             ari.cod_almoxarifado = anu.cod_almoxarifado                                    \n";
$stSql .= "             and ari.cod_requisicao = anu.cod_requisicao                                    \n";
$stSql .= "             and ari.exercicio = anu.exercicio                                              \n";
$stSql .= "             and ari.cod_item = anu.cod_item                                                \n";
$stSql .= "             and ari.cod_marca = anu.cod_marca                                              \n";
$stSql .= "             and ari.cod_centro = anu.cod_centro                                            \n";
$stSql .= "         ,almoxarifado.requisicao as ar                                             \n";
$stSql .= "     where                                                                                 \n";
$stSql .= "         ar.exercicio = ari.exercicio                                                      \n";
$stSql .= "         and ar.cod_almoxarifado = ari.cod_almoxarifado                                    \n";
$stSql .= "         and ar.cod_requisicao = ari.cod_requisicao                                        \n";
if ( $this->getDado('acao') == 'anular' ) {
    $stSql .= "         and COALESCE(ari.quantidade,0) <> COALESCE(anu.qtd_anulada,0)                     \n";
} elseif (( $this->getDado('acao') == 'alterar') || ( $this->getDado('acao') == 'excluir' )) {
    $stSql .= "         and COALESCE(anu.qtd_anulada,0) = 0                     \n";
}
$stSql .= "     group by                                                                              \n";
$stSql .= "         ar.exercicio                                                                      \n";
$stSql .= "         ,ar.cod_almoxarifado                                                              \n";
$stSql .= "         ,ar.cod_requisicao                                                                \n";
$stSql .= "         ,ar.cgm_requisitante                                                              \n";
$stSql .= "         ,ar.cgm_solicitante                                                               \n";
$stSql .= "         ,ar.dt_requisicao                                                                 \n";
$stSql .= "         ,ar.observacao                                                                    \n";
$stSql .= "         ,ari.cod_item                                                                     \n";
$stSql .= "         ,ari.cod_marca                                                                    \n";
$stSql .= "         ,ari.cod_centro                                                                   \n";
$stSql .= "         ,anu.motivo                                                                       \n";
$stSql .= "         ,anu.qtd_anulada                                                                  \n";
$stSql .= "     ) as req                                                                              \n";
$stSql .= " left join                                                                                 \n";
$stSql .= "     (select                                                                               \n";
$stSql .= "         alm.cod_lancamento                                                                \n";
$stSql .= "         ,alm.cod_item                                                                     \n";
$stSql .= "         ,alm.cod_marca                                                                    \n";
$stSql .= "         ,alm.cod_almoxarifado                                                             \n";
$stSql .= "         ,alm.cod_centro                                                                   \n";
$stSql .= "         ,alm.exercicio_lancamento as exercicio                                            \n";
$stSql .= "         ,alr.cod_requisicao                                                               \n";
$stSql .= "         ,sum(alm.quantidade) as qtd_atendida                                              \n";
$stSql .= "     from                                                                                  \n";
$stSql .= "         almoxarifado.lancamento_material as alm                                           \n";
$stSql .= "         ,almoxarifado.lancamento_requisicao as alr                                        \n";
$stSql .= "     where                                                                                 \n";
$stSql .= "         alm.cod_lancamento = alr.cod_lancamento                                           \n";
$stSql .= "         and alm.cod_item = alr.cod_item                                                   \n";
$stSql .= "         and alm.cod_marca = alr.cod_marca                                                 \n";
$stSql .= "         and alm.cod_almoxarifado = alr.cod_almoxarifado                                   \n";
$stSql .= "         and alm.cod_centro = alr.cod_centro                                               \n";
$stSql .= "         and alm.exercicio_lancamento = alr.exercicio                                      \n";
$stSql .= "         and alm.cod_natureza = 8                                                          \n";
$stSql .= "         and alm.tipo_natureza = 'E'                                                       \n";
$stSql .= "     group by                                                                              \n";
$stSql .= "         alm.cod_lancamento                                                                \n";
$stSql .= "         ,alm.cod_item                                                                     \n";
$stSql .= "         ,alm.cod_marca                                                                    \n";
$stSql .= "         ,alm.cod_almoxarifado                                                             \n";
$stSql .= "         ,alm.cod_centro                                                                   \n";
$stSql .= "         ,alm.exercicio_lancamento                                                         \n";
$stSql .= "         ,alr.cod_requisicao                                                               \n";
$stSql .= "     ) as lanc                                                                             \n";
$stSql .= " on                                                                                        \n";
$stSql .= "     req.exercicio = lanc.exercicio                                                        \n";
$stSql .= "     and req.cod_almoxarifado = lanc.cod_almoxarifado                                      \n";
$stSql .= "     and req.cod_requisicao = lanc.cod_requisicao                                          \n";
$stSql .= "     and req.cod_item = lanc.cod_item                                                      \n";
$stSql .= "     and req.cod_marca = lanc.cod_marca                                                    \n";
$stSql .= "     and req.cod_centro = lanc.cod_centro                                                  \n";
$stSql .= " left join                                                                                 \n";
$stSql .= "     (select                                                                               \n";
$stSql .= "         alm.cod_lancamento                                                                \n";
$stSql .= "         ,alm.cod_item                                                                     \n";
$stSql .= "         ,alm.cod_marca                                                                    \n";
$stSql .= "         ,alm.cod_almoxarifado                                                             \n";
$stSql .= "         ,alm.cod_centro                                                                   \n";
$stSql .= "         ,alm.exercicio_lancamento as exercicio                                            \n";
$stSql .= "         ,alr.cod_requisicao                                                               \n";
$stSql .= "         ,sum(alm.quantidade) as qtd_devolvida                                             \n";
$stSql .= "     from                                                                                  \n";
$stSql .= "         almoxarifado.lancamento_material as alm                                           \n";
$stSql .= "         ,almoxarifado.lancamento_requisicao as alr                                        \n";
$stSql .= "     where                                                                                 \n";
$stSql .= "         alm.cod_lancamento = alr.cod_lancamento                                           \n";
$stSql .= "         and alm.cod_item = alr.cod_item                                                   \n";
$stSql .= "         and alm.cod_marca = alr.cod_marca                                                 \n";
$stSql .= "         and alm.cod_almoxarifado = alr.cod_almoxarifado                                   \n";
$stSql .= "         and alm.cod_centro = alr.cod_centro                                               \n";
$stSql .= "         and alm.exercicio_lancamento = alr.exercicio                                      \n";
$stSql .= "         and alm.cod_natureza = 8                                                          \n";
$stSql .= "         and alm.tipo_natureza = 'S'                                                       \n";
$stSql .= "     group by                                                                              \n";
$stSql .= "         alm.cod_lancamento                                                                \n";
$stSql .= "         ,alm.cod_item                                                                     \n";
$stSql .= "         ,alm.cod_marca                                                                    \n";
$stSql .= "         ,alm.cod_almoxarifado                                                             \n";
$stSql .= "         ,alm.cod_centro                                                                   \n";
$stSql .= "         ,alm.exercicio_lancamento                                                         \n";
$stSql .= "         ,alr.cod_requisicao                                                               \n";
$stSql .= "     ) as lanc2                                                                            \n";
$stSql .= " on                                                                                        \n";
$stSql .= "     req.exercicio = lanc.exercicio                                                        \n";
$stSql .= "     and req.cod_almoxarifado = lanc.cod_almoxarifado                                      \n";
$stSql .= "     and req.cod_requisicao = lanc.cod_requisicao                                          \n";
$stSql .= "     and req.cod_item = lanc.cod_item                                                      \n";
$stSql .= "     and req.cod_marca = lanc.cod_marca                                                    \n";
$stSql .= "     and req.cod_centro = lanc.cod_centro                                                  \n";
$stSql .= "                                                                                           \n";
$stSql .= "     ,sw_cgm as cgm                                                                        \n";
$stSql .= "     ,sw_cgm as cgm2                                                                       \n";
$stSql .= "     ,almoxarifado.almoxarifado as aa                                                      \n";
$stSql .= " where                                                                                     \n";
$stSql .= "     cgm.numcgm = aa.cgm_almoxarifado                                                      \n";
$stSql .= "     and cgm2.numcgm = req.cgm_solicitante                                                 \n";
$stSql .= "     and aa.cod_almoxarifado = req.cod_almoxarifado                                        \n";
$stSql .= "     and coalesce(req.qtd_requisitada,0) <> coalesce(lanc.qtd_atendida,0)                  \n";

/*    $stSql .= "SELECT                                              \n";
    $stSql .= "    ar.exercicio,                                   \n";
    $stSql .= "    ar.cod_almoxarifado,                            \n";
    $stSql .= "    ar.cod_requisicao,                              \n";
    $stSql .= "    to_char(ar.dt_requisicao,'dd/mm/yyyy') as dt_requisicao,\n";
    $stSql .= "    ar.observacao,                                  \n";
    $stSql .= "    ar.cgm_solicitante,                             \n";
//    $stSql .= "    ari.cod_item,                                   \n";
//    $stSql .= "    ari.cod_marca,                                  \n";
//    $stSql .= "    ari.cod_centro,                                 \n";
    $stSql .= "    cgm.nom_cgm,                                    \n";
    $stSql .= "    cgm.numcgm,                                     \n";
    $stSql .= "    cgm2.nom_cgm as nom_solicitante                 \n";
    $stSql .= "FROM                                                \n";
    $stSql .= "    ALMOXARIFADO.REQUISICAO as ar                   \n";
    $stSql .= "LEFT JOIN                                           \n";
    $stSql .= "    ALMOXARIFADO.LANCAMENTO_REQUISICAO as alr       \n";
    $stSql .= "ON                                                  \n";
    $stSql .= "    ar.cod_requisicao = alr.cod_requisicao,         \n";
    $stSql .= "    ALMOXARIFADO.REQUISICAO_ITENS as ari            \n";
    $stSql .= "LEFT JOIN                                           \n";
    $stSql .= "    ALMOXARIFADO.REQUISICAO_ITENS_ANULACAO as aria  \n";
    $stSql .= "ON                                                  \n";
    $stSql .= "    ari.cod_requisicao = aria.cod_requisicao,       \n";
    $stSql .= "    ALMOXARIFADO.ALMOXARIFADO as aa,                \n";
    $stSql .= "    SW_CGM as cgm,                                  \n";
    $stSql .= "    SW_CGM as cgm2                                  \n";
    $stSql .= "WHERE                                               \n";
    $stSql .= "    alr.cod_requisicao is null and                  \n";
    $stSql .= "    aria.cod_requisicao is null and                 \n";
    $stSql .= "    cgm.numcgm = aa.cgm_almoxarifado and            \n";
    $stSql .= "    cgm2.numcgm = ar.cgm_solicitante and            \n";
    $stSql .= "    aa.cod_almoxarifado = ar.cod_almoxarifado and   \n";
    $stSql .= "    ar.exercicio = ari.exercicio and                \n";
    $stSql .= "    ar.cod_almoxarifado = ari.cod_almoxarifado and  \n";
    $stSql .= "    ar.cod_requisicao = ari.cod_requisicao          \n";*/

    return $stSql;
}

function recuperaPermiteMovimentacaoSaida(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPermiteMovimentacaoSaida($stFiltro).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPermiteMovimentacaoSaida($stFiltro)
{
   $stSql .= "SELECT tot_requisicao_item.exercicio                                                                      \n ";
   $stSql .= "     , tot_requisicao_item.cod_almoxarifado                                                               \n ";
   $stSql .= "     , tot_requisicao_item.cod_requisicao                                                                 \n ";
   $stSql .= "     , sw_cgm.nom_cgm                                                                                      \n ";
   $stSql .= "     , requisicao.dt_requisicao as dt_data_requisicao                                                     \n ";
   $stSql .= "     , to_char(requisicao.dt_requisicao,'dd/mm/yyyy') as dt_requisicao                                     \n ";
   $stSql .= "  FROM ( SELECT exercicio                                                                                  \n ";
   $stSql .= "              , cod_almoxarifado                                                                           \n ";
   $stSql .= "              , cod_requisicao                                                                             \n ";
   $stSql .= "              , SUM(quantidade) AS tot_qtd_item                                                            \n ";
   $stSql .= "           FROM almoxarifado.requisicao_item                                                              \n ";
   $stSql .= "       GROUP BY exercicio                                                                                  \n ";
   $stSql .= "              , cod_almoxarifado                                                                           \n ";
   $stSql .= "              , cod_requisicao                                                                             \n ";
   $stSql .= "       ) AS tot_requisicao_item                                                                           \n ";
   $stSql .= "            LEFT JOIN ( SELECT exercicio                                                                   \n ";
   $stSql .= "                             , cod_almoxarifado                                                            \n ";
   $stSql .= "                             , cod_requisicao                                                              \n ";
   $stSql .= "                             , SUM(quantidade) AS tot_qtd_item_anu                                         \n ";
   $stSql .= "                          FROM almoxarifado.requisicao_itens_anulacao                                      \n ";
   $stSql .= "                      GROUP BY exercicio                                                                   \n ";
   $stSql .= "                             , cod_almoxarifado                                                            \n ";
   $stSql .= "                             , cod_requisicao )                                                            \n ";
   $stSql .= "     AS requisicao_itens_anulacao ON tot_requisicao_item.exercicio = requisicao_itens_anulacao.exercicio  \n ";
   $stSql .= "                  and tot_requisicao_item.cod_almoxarifado = requisicao_itens_anulacao.cod_almoxarifado   \n ";
   $stSql .= "                 and tot_requisicao_item.cod_requisicao   = requisicao_itens_anulacao.cod_requisicao      \n ";
   $stSql .= "            LEFT JOIN ( SELECT lancamento_requisicao.exercicio                                             \n ";
   $stSql .= "                   , lancamento_requisicao.cod_almoxarifado                                                \n ";
   $stSql .= "                   , lancamento_requisicao.cod_requisicao                                                  \n ";
   $stSql .= "                   , SUM(lancamento_material.quantidade) AS tot_qtd_saida                                  \n ";
   $stSql .= "                FROM almoxarifado.lancamento_material                                                      \n ";
   $stSql .= "                   , almoxarifado.natureza_lancamento                                                      \n ";
   $stSql .= "                   , almoxarifado.natureza                                                                 \n ";
   $stSql .= "                   , almoxarifado.lancamento_requisicao                                                    \n ";
   $stSql .= "               WHERE                                                                                       \n ";
   $stSql .= "                     natureza.tipo_natureza = 'S'                                                          \n ";
   $stSql .= "                 AND natureza_lancamento.cod_natureza = natureza.cod_natureza                              \n ";
   $stSql .= "                 AND natureza_lancamento.tipo_natureza = natureza.tipo_natureza                            \n ";
   $stSql .= "                 AND lancamento_material.num_lancamento = natureza_lancamento.num_lancamento               \n ";
   $stSql .= "                 AND lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento   \n ";
   $stSql .= "                 AND lancamento_material.cod_natureza = natureza_lancamento.cod_natureza                   \n ";
   $stSql .= "                 AND lancamento_material.tipo_natureza = natureza_lancamento.tipo_natureza                 \n ";
   $stSql .= "                 AND lancamento_material.cod_lancamento = lancamento_requisicao.cod_lancamento             \n ";
   $stSql .= "                 AND lancamento_material.cod_item = lancamento_requisicao.cod_item                         \n ";
   $stSql .= "                 AND lancamento_material.cod_marca = lancamento_requisicao.cod_marca                       \n ";
   $stSql .= "                 AND lancamento_material.cod_almoxarifado = lancamento_requisicao.cod_almoxarifado         \n ";
   $stSql .= "            GROUP BY lancamento_requisicao.exercicio                                                       \n ";
   $stSql .= "                   , lancamento_requisicao.cod_almoxarifado                                                \n ";
   $stSql .= "                   , lancamento_requisicao.cod_requisicao )                                                \n ";
   $stSql .= "  AS lancamento_requisicao_saida ON tot_requisicao_item.exercicio = lancamento_requisicao_saida.exercicio \n ";
   $stSql .= "                  and tot_requisicao_item.cod_almoxarifado = lancamento_requisicao_saida.cod_almoxarifado \n ";
   $stSql .= "                  and tot_requisicao_item.cod_requisicao   = lancamento_requisicao_saida.cod_requisicao   \n ";

   $stSql .= " LEFT JOIN ( SELECT                                                                                                             \n";
   $stSql .= "                  lancamento_material_devolvido.cod_almoxarifado                                                                \n";
   $stSql .= "                 ,lancamento_material_devolvido.exercicio_lancamento as exercicio                                               \n";
   $stSql .= "                 ,lancamento_requisicao_devolvido.cod_requisicao                                                                \n";
   $stSql .= "                 ,sum(lancamento_material_devolvido.quantidade) as tot_qtd_item_devol                                           \n";
   $stSql .= "             FROM                                                                                                               \n";
   $stSql .= "                 almoxarifado.lancamento_material as lancamento_material_devolvido                                              \n";
   $stSql .= "             JOIN                                                                                                               \n";
   $stSql .= "                 almoxarifado.lancamento_requisicao as lancamento_requisicao_devolvido                                          \n";
   $stSql .= "             ON (                                                                                                               \n";
   $stSql .= "                     lancamento_material_devolvido.cod_lancamento = lancamento_requisicao_devolvido.cod_lancamento and          \n";
   $stSql .= "                     lancamento_material_devolvido.cod_item = lancamento_requisicao_devolvido.cod_item and                      \n";
   $stSql .= "                     lancamento_material_devolvido.cod_marca = lancamento_requisicao_devolvido.cod_marca and                    \n";
   $stSql .= "                     lancamento_material_devolvido.cod_almoxarifado = lancamento_requisicao_devolvido.cod_almoxarifado and      \n";
   $stSql .= "                     lancamento_material_devolvido.cod_centro = lancamento_requisicao_devolvido.cod_centro                      \n";
   $stSql .= "                )                                                                                                               \n";
   $stSql .= "             WHERE                                                                                                              \n";
   $stSql .= "                lancamento_material_devolvido.cod_natureza = 7  and                                                             \n";
   $stSql .= "                lancamento_material_devolvido.tipo_natureza = 'E'                                                               \n";
   $stSql .= "             GROUP BY                                                                                                           \n";
   $stSql .= "                  lancamento_material_devolvido.cod_almoxarifado                                                                \n";
   $stSql .= "                 ,lancamento_material_devolvido.exercicio_lancamento                                                            \n";
   $stSql .= "                 ,lancamento_requisicao_devolvido.cod_requisicao                                                                \n";
   $stSql .= "                                                                                                                                \n";
   $stSql .= " ) AS lancamento_requisicao_devolvida ON tot_requisicao_item.exercicio = lancamento_requisicao_devolvida.exercicio              \n";
   $stSql .= "             and tot_requisicao_item.cod_almoxarifado = lancamento_requisicao_devolvida.cod_almoxarifado                        \n";
   $stSql .= "             and tot_requisicao_item.cod_requisicao = lancamento_requisicao_devolvida.cod_requisicao                            \n";

   $stSql .= "       , sw_cgm                                                                                            \n ";
   $stSql .= "       , almoxarifado.almoxarifado                                                                         \n ";
   $stSql .= "       , almoxarifado.requisicao                                                                           \n ";
   $stSql .= "         INNER JOIN almoxarifado.requisicao_homologada                                                     \n ";
   $stSql .= "                 ON requisicao.exercicio = requisicao_homologada.exercicio                                 \n ";
   $stSql .= "                AND requisicao.cod_almoxarifado = requisicao_homologada.cod_almoxarifado                   \n ";
   $stSql .= "                AND requisicao.cod_requisicao = requisicao_homologada.cod_requisicao                       \n ";
   $stSql .= "       , almoxarifado.requisicao_item                                                                     \n ";
   $stSql .= " WHERE tot_requisicao_item.tot_qtd_item > COALESCE( requisicao_itens_anulacao.tot_qtd_item_anu, 0 )       \n ";
   $stSql .= "   AND tot_requisicao_item.tot_qtd_item - COALESCE( requisicao_itens_anulacao.tot_qtd_item_anu, 0 ) > COALESCE( abs(lancamento_requisicao_saida.tot_qtd_saida), 0) - COALESCE( lancamento_requisicao_devolvida.tot_qtd_item_devol, 0)         \n ";
   $stSql .= "   AND sw_cgm.numcgm = almoxarifado.cgm_almoxarifado                                                       \n ";
   $stSql .= "   AND almoxarifado.cod_almoxarifado = tot_requisicao_item.cod_almoxarifado                               \n ";
   $stSql .= "   AND requisicao.exercicio = tot_requisicao_item.exercicio                                               \n ";
   $stSql .= "   AND requisicao.cod_requisicao = tot_requisicao_item.cod_requisicao                                     \n ";
   $stSql .= "   AND requisicao.cod_almoxarifado = tot_requisicao_item.cod_almoxarifado                                 \n ";
   $stSql .= "   AND requisicao.exercicio = requisicao_item.exercicio                                                   \n ";
   $stSql .= "   AND requisicao.cod_requisicao = requisicao_item.cod_requisicao                                         \n ";
   $stSql .= "   AND requisicao.cod_almoxarifado = requisicao_item.cod_almoxarifado                                     \n ";
   $stSql .= "   AND requisicao_homologada.timestamp = (SELECT MAX(timestamp) from almoxarifado.requisicao_homologada requisicao_homologada2          \n";
   $stSql .= "                                           WHERE requisicao_homologada.exercicio = requisicao_homologada2.exercicio                     \n";
   $stSql .= "                                             AND requisicao_homologada.cod_almoxarifado = requisicao_homologada2.cod_almoxarifado       \n";
   $stSql .= "                                             AND requisicao_homologada.cod_requisicao   = requisicao_homologada2.cod_requisicao)        \n";
   $stSql .= "   AND requisicao_homologada.homologada = 't'                                                                                           \n";
   $stSql .= $stFiltro;
   $stSql .= " GROUP BY 1, 2, 3, 4, 5, 6                                                                                 \n ";

   return $stSql;
}

function recuperaPermiteMovimentacaoEntrada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPermiteMovimentacaoEntrada($stFiltro).$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPermiteMovimentacaoEntrada($stFiltro)
{
   $stSql  = " SELECT tot_requisicao_item.exercicio                                                                      ";
   $stSql .= "     , tot_requisicao_item.cod_almoxarifado                                                                ";
   $stSql .= "     , tot_requisicao_item.cod_requisicao                                                                  ";
   $stSql .= "     , to_char(requisicao.dt_requisicao,'dd/mm/yyyy') as dt_requisicao                                  ";
   $stSql .= "     , sw_cgm.nom_cgm                                                                                   ";
   $stSql .= "  FROM ( SELECT exercicio                                                                               ";
   $stSql .= "              , cod_almoxarifado                                                                        ";
   $stSql .= "              , cod_requisicao                                                                          ";
   $stSql .= "              , SUM(quantidade) AS tot_qtd_item                                                         ";
   $stSql .= "           FROM almoxarifado.requisicao_item                                                           ";
   $stSql .= "       GROUP BY exercicio                                                                               ";
   $stSql .= "              , cod_almoxarifado                                                                        ";
   $stSql .= "              , cod_requisicao                                                                          ";
   $stSql .= "       ) AS tot_requisicao_item                                                                            ";
   $stSql .= "            LEFT JOIN ( SELECT exercicio                                                                ";
   $stSql .= "                             , cod_almoxarifado                                                         ";
   $stSql .= "                             , cod_requisicao                                                           ";
   $stSql .= "                             , SUM(quantidade) AS tot_qtd_item_anu                                      ";
   $stSql .= "                          FROM almoxarifado.requisicao_itens_anulacao                                   ";
   $stSql .= "                      GROUP BY exercicio                                                                ";
   $stSql .= "                             , cod_almoxarifado                                                         ";
   $stSql .= "                             , cod_requisicao                                                           ";
   $stSql .= "     ) AS requisicao_itens_anulacao ON tot_requisicao_item.exercicio = requisicao_itens_anulacao.exercicio ";
   $stSql .= "                    and tot_requisicao_item.cod_almoxarifado = requisicao_itens_anulacao.cod_almoxarifado  ";
   $stSql .= "                    and tot_requisicao_item.cod_requisicao   = requisicao_itens_anulacao.cod_requisicao    ";
   $stSql .= "            LEFT JOIN ( SELECT lancamento_requisicao.exercicio                                             \n ";
   $stSql .= "                   , lancamento_requisicao.cod_almoxarifado                                                \n ";
   $stSql .= "                   , lancamento_requisicao.cod_requisicao                                                  \n ";
   $stSql .= "                   , SUM(lancamento_material.quantidade) AS tot_qtd                                        \n ";
   $stSql .= "                FROM almoxarifado.lancamento_material                                                      \n ";
   $stSql .= "                   , almoxarifado.natureza_lancamento                                                      \n ";
   $stSql .= "                   , almoxarifado.natureza                                                                 \n ";
   $stSql .= "                   , almoxarifado.lancamento_requisicao                                                    \n ";
   $stSql .= "               WHERE                                                                                       \n ";
   $stSql .= "                     natureza_lancamento.cod_natureza = natureza.cod_natureza                              \n ";
   $stSql .= "                 AND natureza_lancamento.tipo_natureza = natureza.tipo_natureza                            \n ";
   $stSql .= "                 AND lancamento_material.num_lancamento = natureza_lancamento.num_lancamento               \n ";
   $stSql .= "                 AND lancamento_material.exercicio_lancamento = natureza_lancamento.exercicio_lancamento   \n ";
   $stSql .= "                 AND lancamento_material.cod_natureza = natureza_lancamento.cod_natureza                   \n ";
   $stSql .= "                 AND lancamento_material.tipo_natureza = natureza_lancamento.tipo_natureza                 \n ";
   $stSql .= "                 AND lancamento_material.cod_lancamento = lancamento_requisicao.cod_lancamento             \n ";
   $stSql .= "                 AND lancamento_material.cod_item = lancamento_requisicao.cod_item                         \n ";
   $stSql .= "                 AND lancamento_material.cod_marca = lancamento_requisicao.cod_marca                       \n ";
   $stSql .= "                 AND lancamento_material.cod_almoxarifado = lancamento_requisicao.cod_almoxarifado         \n ";
   $stSql .= "            GROUP BY lancamento_requisicao.exercicio                                                       \n ";
   $stSql .= "                   , lancamento_requisicao.cod_almoxarifado                                                \n ";
   $stSql .= "                   , lancamento_requisicao.cod_requisicao )                                                \n ";
   $stSql .= "  AS lancamento_requisicao_entrada ON tot_requisicao_item.exercicio = lancamento_requisicao_entrada.exercicio \n ";
   $stSql .= "                  and tot_requisicao_item.cod_almoxarifado = lancamento_requisicao_entrada.cod_almoxarifado \n ";
   $stSql .= "                  and tot_requisicao_item.cod_requisicao   = lancamento_requisicao_entrada.cod_requisicao   \n ";
   $stSql .= "       , sw_cgm                                                                                         ";
   $stSql .= "       , almoxarifado.almoxarifado                                                                      ";
   $stSql .= "       , almoxarifado.requisicao                                                                        ";
   $stSql .= "       , almoxarifado.requisicao_item                                                                   ";
   $stSql .= " WHERE tot_requisicao_item.tot_qtd_item > COALESCE( requisicao_itens_anulacao.tot_qtd_item_anu, 0 )        ";
   $stSql .= "   AND COALESCE( lancamento_requisicao_entrada.tot_qtd        , 0) < 0                                     \n ";
   $stSql .= "   AND sw_cgm.numcgm = almoxarifado.cgm_almoxarifado                                                    ";
   $stSql .= "   AND almoxarifado.cod_almoxarifado = tot_requisicao_item.cod_almoxarifado                                ";
   $stSql .= "   AND requisicao.exercicio = tot_requisicao_item.exercicio                                                ";
   $stSql .= "   AND requisicao.cod_requisicao = tot_requisicao_item.cod_requisicao                                      ";
   $stSql .= "   AND requisicao.cod_almoxarifado = tot_requisicao_item.cod_almoxarifado                                  ";
   $stSql .= "   AND requisicao.exercicio = requisicao_item.exercicio                                                   \n ";
   $stSql .= "   AND requisicao.cod_requisicao = requisicao_item.cod_requisicao                                         \n ";
   $stSql .= "   AND requisicao.cod_almoxarifado = requisicao_item.cod_almoxarifado                                     \n ";
   $stSql .= $stFiltro;
   $stSql .= " GROUP BY 1, 2, 3, 4, 5                                                                                    \n ";

   return $stSql;

}

function recuperaRequisicaoAlteracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRequisicaoAlteracao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRequisicaoAlteracao()
{
    $stSql = " select                                                    \n";
    $stSql .= " 	req.exercicio                                         \n";
    $stSql .= " 	,req.cod_almoxarifado                                 \n";
    $stSql .= " 	,req.cod_requisicao                                   \n";
    $stSql .= " 	,req.cgm_requisitante                                 \n";
    $stSql .= " 	,req.cgm_solicitante                                  \n";
    $stSql .= " 	,to_char(req.dt_requisicao, 'dd/mm/yyyy') as dt_requisicao \n";
    $stSql .= " 	,req.observacao                                       \n";
    $stSql .= " 	,cgm.nom_cgm                                          \n";
    $stSql .= " 	,cgm.numcgm                                           \n";
    $stSql .= " 	,cgm2.nom_cgm as nom_solicitante                      \n";
    $stSql .= " 	,cgm2.numcgm as num_solicitante                       \n";
    $stSql .= " from                                                      \n";
    $stSql .= " 	 almoxarifado.requisicao as req                       \n";
    $stSql .= " 	,almoxarifado.requisicao_item as reqi                 \n";
    $stSql .= " left join													 \n";
    $stSql .= " 	 almoxarifado.requisicao_itens_anulacao as aria          \n";
    $stSql .= " on                                                           \n";
    $stSql .= " 		reqi.exercicio = aria.exercicio                      \n";
    $stSql .= " 	and reqi.cod_almoxarifado = aria.cod_almoxarifado        \n";
    $stSql .= " 	and reqi.cod_requisicao = aria.cod_requisicao            \n";
    $stSql .= " 	and reqi.cod_item = aria.cod_item                        \n";
    $stSql .= " 	and reqi.cod_marca = aria.cod_marca                      \n";
    $stSql .= " 	and reqi.cod_centro = aria.cod_centro                    \n";
    $stSql .= " left join                                                    \n";
    $stSql .= " 	almoxarifado.lancamento_requisicao as alr                \n";
    $stSql .= " on                                                           \n";
    $stSql .= " 		reqi.exercicio = alr.exercicio                       \n";
    $stSql .= " 	and reqi.cod_almoxarifado = alr.cod_almoxarifado         \n";
    $stSql .= " 	and reqi.cod_requisicao = alr.cod_requisicao             \n";
    $stSql .= " 	and reqi.cod_item = alr.cod_item                         \n";
    $stSql .= " 	and reqi.cod_marca = alr.cod_marca                       \n";
    $stSql .= " 	and reqi.cod_centro = alr.cod_centro                     \n";
    $stSql .= " 	,sw_cgm as cgm                                        \n";
    $stSql .= " 	,sw_cgm as cgm2                                       \n";
    $stSql .= " 	,almoxarifado.almoxarifado                           \n";
    $stSql .= " where                                                     \n";
    $stSql .= " 		req.exercicio = reqi.exercicio                    \n";
    $stSql .= " 	and req.cod_almoxarifado = reqi.cod_almoxarifado      \n";
    $stSql .= " 	and req.cod_requisicao = reqi.cod_requisicao          \n";
    //$stSql .= " 	and req.cgm_solicitante = cgm.numcgm                  \n";
    $stSql .= " 	and req.cgm_requisitante = cgm2.numcgm                \n";
    $stSql .= " 	and req.cod_almoxarifado = almoxarifado.cod_almoxarifado                \n";
    $stSql .= " 	and almoxarifado.cgm_almoxarifado = cgm.numcgm                \n";
    if ( $this->getDado('acao') == 'anular' ) {
        //$stSql .= "    and aria.cod_item is not null \n";
    } else {
        $stSql .= "    and aria.cod_item is null \n";
    }
    $stSql .= "    and alr.cod_item is null                                  \n";

    return $stSql;
}

}
