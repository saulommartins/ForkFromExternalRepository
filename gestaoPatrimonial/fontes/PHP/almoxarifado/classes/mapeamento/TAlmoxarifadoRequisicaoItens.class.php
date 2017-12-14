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
    * Classe de mapeamento da tabela ALMOXARIFADO.REQUISICAO_ITENS
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TAlmoxarifadoRequisicaoItens.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

/*
$Log$
Revision 1.29  2007/03/22 19:34:05  tonismar
bug #8696 #8695

Revision 1.28  2006/10/13 17:27:47  larocca
Correção na lista de anulação de requisições.

Revision 1.27  2006/10/13 16:06:43  larocca
Correção na lista de anulação de requisições.

Revision 1.26  2006/10/13 14:56:10  leandro.zis
corrigido pq nah estava saindo repetidos os itens com varias anulacoes no consultar requisicoes

Revision 1.25  2006/10/13 11:36:22  larocca
Correção na lista de anulação de requisições.

Revision 1.24  2006/10/13 09:12:14  larocca
Alteração nas Querys de Anulação de Requisição e Consulta de Requisição

Revision 1.23  2006/10/11 16:08:48  leandro.zis
separadas as funcoes que traz os itens da anulação e do consultar

Revision 1.22  2006/10/10 14:52:45  larocca
BUG #7153#

Revision 1.21  2006/10/05 18:05:01  leandro.zis
Bug #7086#
Bug #7098#

Revision 1.20  2006/10/03 08:53:02  leandro.zis
Bug #7086#

Revision 1.19  2006/09/29 10:20:44  tonismar
bug #6900#

Revision 1.18  2006/09/18 17:03:06  tonismar
#6891#

Revision 1.17  2006/09/18 15:14:23  tonismar
#6900#

Revision 1.16  2006/09/13 16:58:39  tonismar
#6886#

Revision 1.15  2006/09/13 15:12:29  tonismar
#6886#

Revision 1.14  2006/08/09 13:49:09  fernando
alteração do nome da tabela de almoxarifado.requisicao_itens para almoxarifado.requisicao_item

Revision 1.13  2006/08/09 13:44:02  fernando
alteração do nome da tabela de almoxarifado.requisicao_itens para almoxarifado.requisicao_item

Revision 1.12  2006/07/28 19:47:50  tonismar
Requisição

Revision 1.11  2006/07/20 21:00:38  tonismar
comitei pro Zank passar o script do help

Revision 1.10  2006/07/06 14:04:44  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.REQUISICAO_ITENS
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoRequisicaoItens extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoRequisicaoItens()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.requisicao_item');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_almoxarifado,cod_requisicao,exercicio,cod_centro,cod_marca,cod_item');

    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('cod_requisicao','integer',true,'',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('exercicio','char',true,'4',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('cod_centro','integer',true,'',true,true);
    $this->AddCampo('cod_marca','integer',true,'',true,true);
    $this->AddCampo('cod_item','integer',true,'',true,true);
    $this->AddCampo('quantidade','numeric',true,'14.4',false,false);

}

function recuperaSaldoRequisitado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoRequisitado().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoRequisitado()
{
    $stSql .= " SELECT                                                        \n";
    $stSql .= "     (ari.quantidade-coalesce(aria.tot_quantidade_anulacao, 0)) as saldo_requisitado  \n";
    $stSql .= " FROM                                                          \n";
    $stSql .= "     almoxarifado.requisicao_item as ari                       \n";
    $stSql .= " LEFT OUTER JOIN (                                             \n";
    $stSql .= "     SELECT exercicio,                                                         \n";
    $stSql .= "            cod_requisicao,                                                    \n";
    $stSql .= "            cod_almoxarifado,                                                  \n";
    $stSql .= "            cod_item,                                                          \n";
    $stSql .= "            cod_marca,                                                         \n";
    $stSql .= "            cod_centro,                                                        \n";
    $stSql .= "            SUM(quantidade) as tot_quantidade_anulacao                    \n";
    $stSql .= "     FROM                                                      \n";
    $stSql .= "       almoxarifado.requisicao_itens_anulacao                \n";
    $stSql .= "     GROUP BY exercicio, cod_almoxarifado, cod_requisicao,  \n";
    $stSql .= "             cod_item, cod_marca, cod_centro )as aria           \n";
    $stSql .= "     ON                                                      \n";
    $stSql .= "       ari.exercicio = aria.exercicio and                      \n";
    $stSql .= "       ari.cod_almoxarifado = aria.cod_almoxarifado and        \n";
    $stSql .= "       ari.cod_requisicao = aria.cod_requisicao and            \n";
    $stSql .= "       ari.cod_item  = aria.cod_item  and                      \n";
    $stSql .= "       ari.cod_marca = aria.cod_marca and                      \n";
    $stSql .= "       ari.cod_centro = aria.cod_centro                       \n";
    $stSql .= " WHERE                                                         \n";
    $stSql .= "     ari.exercicio = '".$this->getDado('exercicio')."' and           \n";
    $stSql .= "     ari.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and          \n";
    $stSql .= "     ari.cod_requisicao = ".$this->getDado('cod_requisicao')." and              \n";
    $stSql .= "     ari.cod_item  = ".$this->getDado('cod_item')." and                        \n";
    $stSql .= "     ari.cod_marca = ".$this->getDado('cod_marca')." and                        \n";
    $stSql .= "     ari.cod_centro = ".$this->getDado('cod_centro')."                         \n";

    return $stSql;

}

function recuperaSaldoAtendido(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoAtendido().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoAtendido()
{
    $stSql .= " SELECT                                                        \n";
    $stSql .= "     sum(alm.quantidade) as saldo_atendido \n";
    $stSql .= " FROM                                                          \n";
    $stSql .= "     almoxarifado.lancamento_material as alm                       \n";
    $stSql .= " JOIN                                               \n";
    $stSql .= "     almoxarifado.lancamento_requisicao as alr            \n";
    $stSql .= " ON (                                                          \n";
    $stSql .= "     alm.cod_lancamento = alr.cod_lancamento and                        \n";
    $stSql .= "     alm.cod_item = alr.cod_item and          \n";
    $stSql .= "     alm.cod_marca = alr.cod_marca and              \n";
    $stSql .= "     alm.cod_almoxarifado = alr.cod_almoxarifado and                        \n";
    $stSql .= "     alm.cod_centro = alr.cod_centro )                        \n";
    $stSql .= " WHERE                                                         \n";
    $stSql .= "     alm.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and          \n";
    $stSql .= "     alr.cod_requisicao = ".$this->getDado('cod_requisicao')." and              \n";
    $stSql .= "     alr.exercicio = '".$this->getDado('exercicio')."' and              \n";
    $stSql .= "     alm.cod_item  = ".$this->getDado('cod_item')." and                        \n";
    $stSql .= "     alm.cod_marca = ".$this->getDado('cod_marca')." and                        \n";
    $stSql .= "     alm.cod_centro = ".$this->getDado('cod_centro')."                         \n";

    return $stSql;
}

function recuperaSaldoDevolvido(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoDevolvido().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoDevolvido()
{
    $stSql .= " SELECT                                                                 \n";
    $stSql .= "     sum(lancamento_material.quantidade) as saldo_devolvido                              \n";
    $stSql .= " FROM                                                                   \n";
    $stSql .= "     almoxarifado.lancamento_material                            \n";
    $stSql .= " JOIN                                                                   \n";
    $stSql .= "     almoxarifado.lancamento_requisicao                          \n";
    $stSql .= " ON (                                                                   \n";
    $stSql .= "     lancamento_material.cod_lancamento = lancamento_requisicao.cod_lancamento and                        \n";
    $stSql .= "     lancamento_material.cod_item = lancamento_requisicao.cod_item and                                    \n";
    $stSql .= "     lancamento_material.cod_marca = lancamento_requisicao.cod_marca and                                  \n";
    $stSql .= "     lancamento_material.cod_almoxarifado = lancamento_requisicao.cod_almoxarifado and                    \n";
    $stSql .= "     lancamento_material.cod_centro = lancamento_requisicao.cod_centro )                                  \n";
    $stSql .= " WHERE                                                                  \n";
    $stSql .= "     lancamento_material.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and  \n";
    $stSql .= "     lancamento_requisicao.cod_requisicao = ".$this->getDado('cod_requisicao')." and      \n";
    $stSql .= "     lancamento_requisicao.exercicio = ".$this->getDado('exercicio')." and                \n";

    if ( $this->getDado('cod_item') ) {
        $stSql .= "     lancamento_material.cod_item  = ".$this->getDado('cod_item')." and                 \n";
    }

    if ( $this->getDado('cod_marca') ) {
        $stSql .= "     lancamento_material.cod_marca = ".$this->getDado('cod_marca')." and                \n";
    }

    if ( $this->getDado('cod_centro') ) {
        $stSql .= "     lancamento_material.cod_centro = ".$this->getDado('cod_centro')." and              \n";
    }

    $stSql .= "     lancamento_material.cod_natureza = 7  and                                          \n";
    $stSql .= "     lancamento_material.tipo_natureza = 'E'                                            \n";

    return $stSql;
}

function recuperaItens(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaItens().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaItens()
{
    $stSql .= " select                                                       \n";
    $stSql .= "      ar.cod_requisicao                                       \n";
    $stSql .= "     ,ar.exercicio                                            \n";
    $stSql .= "     ,ar.cod_almoxarifado                                     \n";
    $stSql .= "     ,ar.observacao                                           \n";
    $stSql .= "     ,ari.cod_centro                                          \n";
    $stSql .= "     ,ari.cod_item                                            \n";
    $stSql .= "     ,ari.cod_marca                                           \n";
    $stSql .= "     ,aa.cgm_almoxarifado                                     \n";
    $stSql .= "     ,cgm.nom_cgm                                             \n";
    $stSql .= "     ,aci.descricao as nom_item                               \n";
    $stSql .= "     ,am.descricao as nom_marca                               \n";
    $stSql .= "     ,acc.descricao as nom_centro                             \n";
    $stSql .= "     ,ari.quantidade                                          \n";
    $stSql .= "    ,sum(alm.quantidade) as saldo_estoque                     \n";

    $stSql .= " from                                                         \n";
    $stSql .= "      almoxarifado.requisicao as ar                           \n";
    $stSql .= "     ,almoxarifado.requisicao_item as ari                    \n";
    $stSql .= "     ,almoxarifado.almoxarifado as aa                         \n";
    $stSql .= "     ,almoxarifado.centro_custo as acc                        \n";
    $stSql .= "     ,almoxarifado.marca as am                                \n";
    $stSql .= "     ,almoxarifado.catalogo_item as aci                       \n";
    $stSql .= "     ,sw_cgm as cgm                                           \n";

    $stSql .= "     ,almoxarifado.lancamento_material as alm                 \n";
    $stSql .= "     ,almoxarifado.estoque_material as aem                    \n";

    $stSql .= "     ,almoxarifado.centro_custo_permissao as accp             \n";

    $stSql .= " where                                                        \n";
    $stSql .= "         ar.cod_requisicao    = ari.cod_requisicao            \n";
    $stSql .= "     and ar.exercicio         = ari.exercicio                 \n";
    $stSql .= "     and ar.cod_almoxarifado  = ari.cod_almoxarifado          \n";
    $stSql .= "     and ari.cod_almoxarifado = aa.cod_almoxarifado           \n";
    $stSql .= "     and ari.cod_centro       = acc.cod_centro                \n";
    $stSql .= "     and ari.cod_item         = aci.cod_item                  \n";
    $stSql .= "     and ari.cod_marca        = am.cod_marca                  \n";
    $stSql .= "     and aa.cgm_almoxarifado  = cgm.numcgm                    \n";

    if ( $this->getDado('cod_requisicao') ) {
        $stSql .= " and ar.cod_requisicao = ".$this->getDado('cod_requisicao');
    }
    if ( $this->getDado('cod_almoxarifado') ) {
        $stSql .= " and ar.cod_almoxarifado = ". $this->getDado('cod_almoxarifado');
    }
    if ( $this->getDado('exercicio') ) {
        $stSql .= " and ar.exercicio = '".$this->getDado('exercicio')."' ";
    }
    if ( $this->getDado('cod_item') ) {
        $stSql .= " and ari.cod_item = ".$this->getDado('cod_item');
    }
    if ( $this->getDado('cod_centro') ) {
        $stSql .= " and ari.cod_centro = ".$this->getDado('cod_centro');
    }
    if ( $this->getDado('cod_marca') ) {
        $stSql .= " and ari.cod_marca = ".$this->getDado('cod_marca');
    }

    $stSql .= "     and aem.cod_item  = alm.cod_item                            \n";
    $stSql .= "     and aem.cod_marca = alm.cod_marca                           \n";
    $stSql .= "     and aem.cod_almoxarifado = alm.cod_almoxarifado             \n";
    $stSql .= "     and aem.cod_centro = alm.cod_centro                         \n";
    $stSql .= "     and alm.cod_item = ari.cod_item                             \n";
    $stSql .= "     and alm.cod_marca = ari.cod_marca                           \n";
    $stSql .= "     and alm.cod_almoxarifado = ari.cod_almoxarifado             \n";
    $stSql .= "     and alm.cod_centro = ari.cod_centro                         \n";

    $stSql .= "     and accp.cod_centro = ari.cod_centro                        \n";
    $stSql .= "     and accp.responsavel = 't'                       \n";

    $stSql .= " group by                                                       \n";
    $stSql .= "      ar.cod_requisicao                                         \n";
    $stSql .= "     ,ar.exercicio                                              \n";
    $stSql .= "     ,ar.cod_almoxarifado                                       \n";
    $stSql .= "     ,ar.observacao                                             \n";
    $stSql .= "     ,ari.cod_centro                                            \n";
    $stSql .= "     ,ari.cod_item                                              \n";
    $stSql .= "     ,ari.cod_marca                                             \n";
    $stSql .= "     ,aa.cgm_almoxarifado                                       \n";
    $stSql .= "     ,cgm.nom_cgm                                               \n";
    $stSql .= "     ,aci.descricao                                             \n";
    $stSql .= "     ,am.descricao                                              \n";
    $stSql .= "     ,acc.descricao                                             \n";
    $stSql .= "     ,ari.quantidade                                            \n";

    return $stSql;
}

function recuperaRequisicaoItensAnulacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRequisicaoItensAnulacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRequisicaoItensAnulacao()
{
$stSql .= " select                                                          \n";
$stSql .= "      ari.cod_requisicao                                         \n";
$stSql .= "     ,ari.cod_almoxarifado                                       \n";
$stSql .= "     ,ari.exercicio                                              \n";
$stSql .= "     ,ari.cod_centro                                             \n";
$stSql .= "     ,ari.cod_item                                               \n";
$stSql .= "     ,ari.cod_marca                                              \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,aci.descricao as nom_item                                  \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,am.descricao as nom_marca                                  \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,acc.descricao as nom_centro                                \n";
$stSql .= "                                                                 \n";
$stSql .= " ,(Select sum(quantidade) From almoxarifado.requisicao_itens_anulacao as ra Where ra.cod_requisicao = ari.cod_requisicao And ra.cod_almoxarifado = ari.cod_almoxarifado And ra.exercicio = ari.exercicio And ra.cod_item = ari.cod_item And ra.cod_marca = ari.cod_marca And ra.cod_centro = ari.cod_centro) as qtd_anulada  \n";
$stSql .= " ,(Select (sum(quantidade)*-1) From almoxarifado.lancamento_requisicao as lr Inner Join almoxarifado.lancamento_material as lm On lm.cod_lancamento = lr.cod_lancamento And lm.cod_item = lr.cod_item And lm.cod_almoxarifado = lr.cod_almoxarifado And lm.cod_marca = lr.cod_marca And lm.cod_centro = lr.cod_centro Where lr.cod_requisicao = ari.cod_requisicao And lr.cod_item = ari.cod_item And lr.cod_marca = ari.cod_marca And lr.cod_centro = ari.cod_centro And lr.cod_almoxarifado = ari.cod_almoxarifado And lr.exercicio = ari.exercicio ) as qtd_atendida  \n";
//$stSql .= "     ,sum(ari.quantidade) as qtd_requisitada                     \n";
//$stSql .= "     ,abs(sum(lm.quantidade)) as qtd_atendida                        \n";
//$stSql .= "     ,sum(alms.quantidade) as qtd_devolvida                        \n";
$stSql .= "     ,ari.quantidade as qtd_requisitada                     \n";
//$stSql .= "     ,sum(ra.quantidade) as qtd_anulada                     \n";
$stSql .= " from                                                            \n";
$stSql .= "     almoxarifado.requisicao_item as ari                        \n";
/*$stSql .= " left join                                                       \n";
$stSql .= "     almoxarifado.requisicao_itens_anulacao as aria              \n";
$stSql .= " on                                                              \n";
$stSql .= "     aria.exercicio = ari.exercicio                              \n";
$stSql .= "     and aria.cod_almoxarifado = ari.cod_almoxarifado            \n";
$stSql .= "     and aria.cod_requisicao = ari.cod_requisicao                \n";
$stSql .= "     and aria.cod_item = ari.cod_item                            \n";
$stSql .= "     and aria.cod_marca = ari.cod_marca                          \n";
$stSql .= "     and aria.cod_centro = ari.cod_centro                        \n";
$stSql .= " left join                                                       \n";
$stSql .= "     almoxarifado.lancamento_requisicao as alr                   \n";
$stSql .= " on                                                              \n";
$stSql .= "     ari.cod_item = alr.cod_item                                 \n";
$stSql .= "     and ari.cod_marca = alr.cod_marca                           \n";
$stSql .= "     and ari.cod_almoxarifado = alr.cod_almoxarifado             \n";
$stSql .= "     and ari.cod_centro = alr.cod_centro                         \n";
$stSql .= "     and ari.exercicio = alr.exercicio                           \n";
$stSql .= "     and ari.cod_requisicao = alr.cod_requisicao                 \n";
$stSql .= " left join                                                       \n";
$stSql .= "     almoxarifado.lancamento_material as alm                     \n";
$stSql .= " on                                                              \n";
$stSql .= "     alr.cod_lancamento = alm.cod_lancamento                     \n";
$stSql .= "     and alr.cod_item = alm.cod_item                             \n";
$stSql .= "     and alr.cod_marca = alm.cod_marca                           \n";
$stSql .= "     and alr.cod_almoxarifado = alm.cod_almoxarifado             \n";
$stSql .= "     and alr.cod_centro = alm.cod_centro                         \n";
$stSql .= "     and alr.exercicio = alm.exercicio_lancamento                \n";
$stSql .= "     and alm.cod_natureza = 7                                    \n";
$stSql .= "     and alm.tipo_natureza = 'S'                                 \n";
$stSql .= " left join                                                       \n";
$stSql .= "    (                                                            \n";
$stSql .= "        select                                                   \n";
$stSql .= "             quantidade                                          \n";
$stSql .= "            ,cod_item                                            \n";
$stSql .= "            ,cod_marca                                           \n";
$stSql .= "            ,cod_almoxarifado                                    \n";
$stSql .= "            ,cod_centro                                          \n";
$stSql .= "            ,exercicio_lancamento                                \n";
$stSql .= "            ,cod_lancamento                                \n";
$stSql .= "            ,cod_natureza                                        \n";
$stSql .= "            ,tipo_natureza                                       \n";
$stSql .= "        from                                                     \n";
$stSql .= "            almoxarifado.lancamento_material                     \n";
$stSql .= "    ) as alms                                                    \n";
$stSql .= " on                                                              \n";
$stSql .= "    ari.exercicio = alms.exercicio_lancamento                    \n";
//$stSql .= "    and alr.cod_lancamento = alms.cod_lancamento \n";
$stSql .= "    and ari.cod_item = alms.cod_item                             \n";
$stSql .= "    and ari.cod_marca = alms.cod_marca                           \n";
$stSql .= "    and ari.cod_almoxarifado = alms.cod_almoxarifado             \n";
$stSql .= "    and ari.cod_centro = alms.cod_centro                         \n";
$stSql .= "    and alms.cod_natureza = 7                                    \n";
$stSql .= "    and alms.tipo_natureza = 'E'                                 \n";*/
$stSql .= "    ,almoxarifado.catalogo_item as aci                           \n";
$stSql .= "    ,almoxarifado.marca as am                                    \n";
$stSql .= "    ,almoxarifado.centro_custo as acc                            \n";
$stSql .= " where                                                           \n";
$stSql .= "     aci.cod_item = ari.cod_item                                 \n";
$stSql .= "     and ari.cod_marca = am.cod_marca                            \n";
$stSql .= "     and ari.cod_centro = acc.cod_centro                         \n";
$stSql .= "     and ari.quantidade       > (coalesce((Select sum(quantidade) From almoxarifado.requisicao_itens_anulacao as ra Where ra.cod_requisicao = ari.cod_requisicao And ra.cod_almoxarifado = ari.cod_almoxarifado And ra.exercicio = ari.exercicio And ra.cod_item = ari.cod_item And ra.cod_marca = ari.cod_marca And ra.cod_centro = ari.cod_centro),0) +  \n";
$stSql .= "     coalesce((Select (sum(quantidade)*-1) From almoxarifado.lancamento_requisicao as lr Inner Join almoxarifado.lancamento_material as lm On lm.cod_lancamento = lr.cod_lancamento And lm.cod_item = lr.cod_item And lm.cod_almoxarifado = lr.cod_almoxarifado And lm.cod_marca = lr.cod_marca And lm.cod_centro = lr.cod_centro Where lr.cod_requisicao = ari.cod_requisicao And lr.cod_item = ari.cod_item And lr.cod_marca = ari.cod_marca And lr.cod_centro = ari.cod_centro And lr.cod_almoxarifado = ari.cod_almoxarifado And lr.exercicio = ari.exercicio ),0)) \n";

if ( $this->getDado('cod_almoxarifado') ) {
    $stSql .= " and ari.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')."\n";
}

if ( $this->getDado('cod_requisicao') ) {
    $stSql .= " and ari.cod_requisicao = ".$this->getDado('cod_requisicao')."\n";
}

if ( $this->getDado('exercicio') ) {
    $stSql .= " and ari.exercicio = '".$this->getDado('exercicio')."'\n";
}

$stSql .= " group by                                                        \n";
$stSql .= "      ari.cod_requisicao                                         \n";
$stSql .= "     ,ari.cod_almoxarifado                                       \n";
$stSql .= "     ,ari.exercicio                                              \n";
$stSql .= "     ,ari.cod_centro                                             \n";
$stSql .= "     ,ari.cod_item                                               \n";
$stSql .= "     ,ari.cod_marca                                              \n";
$stSql .= "     ,aci.descricao                                              \n";
$stSql .= "     ,am.descricao                                               \n";
$stSql .= "     ,acc.descricao                                              \n";
$stSql .= "     ,ari.quantidade										      \n";
//$stSql .= "     ,ra.quantidade										      \n";
//echo "<pre>".$stSql."</pre>";
return $stSql;

}

function recuperaRequisicaoItensConsultar(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRequisicaoItensConsultar().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRequisicaoItensConsultar()
{
$stSql .= " select                                                          \n";
$stSql .= "      ari.cod_requisicao                                         \n";
$stSql .= "     ,ari.cod_almoxarifado                                       \n";
$stSql .= "     ,ari.exercicio                                              \n";
$stSql .= "     ,ari.cod_centro                                             \n";
$stSql .= "     ,ari.cod_item                                               \n";
$stSql .= "     ,ari.cod_marca                                              \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,aci.descricao as nom_item                                  \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,am.descricao as nom_marca                                  \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,acc.descricao as nom_centro                                \n";
$stSql .= "                                                                 \n";
$stSql .= "     ,sum(ari.quantidade) as qtd_requisitada                     \n";
$stSql .= "     ,abs(sum(alm.quantidade)) as qtd_atendida                        \n";
$stSql .= "     ,aria.qtd_anulada                        \n";
$stSql .= "     ,sum(alms.quantidade) as qtd_devolvida                        \n";
$stSql .= "     ,ari.quantidade as qtd_requisitada                     \n";
$stSql .= " from                                                            \n";
$stSql .= "     almoxarifado.requisicao_item as ari                        \n";
$stSql .= " left join(                                                        \n";
$stSql .= " select                                                            \n";
$stSql .= "     aria.exercicio                            \n";
$stSql .= "     ,aria.cod_almoxarifado            \n";
$stSql .= "     ,aria.cod_requisicao                \n";
$stSql .= "     ,aria.cod_item                            \n";
$stSql .= "     ,aria.cod_marca                                   \n";
$stSql .= "     ,aria.cod_centro                                   \n";
$stSql .= "     , sum(aria.quantidade ) as qtd_anulada             \n";
$stSql .= " from almoxarifado.requisicao_itens_anulacao as aria              \n";
$stSql .= " group by                                                       \n";
$stSql .= "     aria.exercicio                            \n";
$stSql .= "     ,aria.cod_almoxarifado            \n";
$stSql .= "     ,aria.cod_requisicao                \n";
$stSql .= "     ,aria.cod_item                            \n";
$stSql .= "     ,aria.cod_centro                          \n";
$stSql .= "     ,aria.cod_marca ) as aria                         \n";
$stSql .= " on                                                              \n";
$stSql .= "     aria.exercicio = ari.exercicio                              \n";
$stSql .= "     and aria.cod_almoxarifado = ari.cod_almoxarifado            \n";
$stSql .= "     and aria.cod_requisicao = ari.cod_requisicao                \n";
$stSql .= "     and aria.cod_item = ari.cod_item                            \n";
$stSql .= "     and aria.cod_marca = ari.cod_marca                          \n";
$stSql .= "     and aria.cod_centro = ari.cod_centro                        \n";
$stSql .= " left join                                                       \n";
$stSql .= "     almoxarifado.lancamento_requisicao as alr                   \n";
$stSql .= " on                                                              \n";
$stSql .= "     ari.cod_item = alr.cod_item                                 \n";
$stSql .= "     and ari.cod_marca = alr.cod_marca                           \n";
$stSql .= "     and ari.cod_almoxarifado = alr.cod_almoxarifado             \n";
$stSql .= "     and ari.cod_centro = alr.cod_centro                         \n";
$stSql .= "     and ari.exercicio = alr.exercicio                           \n";
$stSql .= "     and ari.cod_requisicao = alr.cod_requisicao                 \n";
$stSql .= " left join                                                       \n";
$stSql .= "     almoxarifado.lancamento_material as alm                     \n";
$stSql .= " on                                                              \n";
$stSql .= "     alr.cod_lancamento = alm.cod_lancamento                     \n";
$stSql .= "     and alr.cod_item = alm.cod_item                             \n";
$stSql .= "     and alr.cod_marca = alm.cod_marca                           \n";
$stSql .= "     and alr.cod_almoxarifado = alm.cod_almoxarifado             \n";
$stSql .= "     and alr.cod_centro = alm.cod_centro                         \n";
$stSql .= "     and alr.exercicio = alm.exercicio_lancamento                \n";
$stSql .= "     and alm.cod_natureza = 7                                    \n";
$stSql .= "     and alm.tipo_natureza = 'S'                                 \n";
$stSql .= " left join                                                       \n";
$stSql .= "    (                                                            \n";
$stSql .= "        select                                                   \n";
$stSql .= "             quantidade                                          \n";
$stSql .= "            ,cod_item                                            \n";
$stSql .= "            ,cod_marca                                           \n";
$stSql .= "            ,cod_almoxarifado                                    \n";
$stSql .= "            ,cod_centro                                          \n";
$stSql .= "            ,exercicio_lancamento                                \n";
$stSql .= "            ,cod_lancamento                                \n";
$stSql .= "            ,cod_natureza                                        \n";
$stSql .= "            ,tipo_natureza                                       \n";
$stSql .= "        from                                                     \n";
$stSql .= "            almoxarifado.lancamento_material                     \n";
$stSql .= "    ) as alms                                                    \n";
$stSql .= " on                                                              \n";
$stSql .= "    ari.exercicio = alms.exercicio_lancamento                    \n";
$stSql .= "    and alr.cod_lancamento = alms.cod_lancamento \n";
$stSql .= "    and ari.cod_item = alms.cod_item                             \n";
$stSql .= "    and ari.cod_marca = alms.cod_marca                           \n";
$stSql .= "    and ari.cod_almoxarifado = alms.cod_almoxarifado             \n";
$stSql .= "    and ari.cod_centro = alms.cod_centro                         \n";
$stSql .= "    and alms.cod_natureza = 7                                    \n";
$stSql .= "    and alms.tipo_natureza = 'E'                                 \n";
$stSql .= "    ,almoxarifado.catalogo_item as aci                           \n";
$stSql .= "    ,almoxarifado.marca as am                                    \n";
$stSql .= "    ,almoxarifado.centro_custo as acc                            \n";
$stSql .= " where                                                           \n";
$stSql .= "     aci.cod_item = ari.cod_item                                 \n";
$stSql .= "     and ari.cod_marca = am.cod_marca                            \n";
$stSql .= "     and ari.cod_centro = acc.cod_centro                         \n";

if ( $this->getDado('cod_almoxarifado') ) {
    $stSql .= " and ari.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')."\n";
}

if ( $this->getDado('cod_requisicao') ) {
    $stSql .= " and ari.cod_requisicao = ".$this->getDado('cod_requisicao')."\n";
}

if ( $this->getDado('exercicio') ) {
    $stSql .= " and ari.exercicio = '".$this->getDado('exercicio')."'\n";
}

$stSql .= " group by                                                        \n";
$stSql .= "      ari.cod_requisicao                                         \n";
$stSql .= "     ,ari.cod_almoxarifado                                       \n";
$stSql .= "     ,ari.exercicio                                              \n";
$stSql .= "     ,ari.cod_centro                                             \n";
$stSql .= "     ,ari.cod_item                                               \n";
$stSql .= "     ,ari.cod_marca                                              \n";
$stSql .= "     ,aci.descricao                                              \n";
$stSql .= "     ,am.descricao                                               \n";
$stSql .= "     ,acc.descricao                                              \n";
$stSql .= "     ,ari.quantidade										      \n";
$stSql .= "     ,aria.qtd_anulada									      \n";

return $stSql;

}

function recuperaRequisicaoDevolucao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRequisicaoDevolucao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRequisicaoDevolucao()
{
    $stSql = "SELECT ri.cod_almoxarifado,
                     ri.cod_requisicao,
                     ri.exercicio,
                     ri.cod_centro,
                     ri.cod_marca,
                     ri.cod_item,
                     ri.quantidade,
                     lm.tipo_natureza,
                     lr.cod_lancamento

                FROM almoxarifado.requisicao_item AS ri
                JOIN almoxarifado.lancamento_requisicao AS lr
                  ON lr.cod_almoxarifado = ri.cod_almoxarifado
                 AND lr.cod_requisicao = ri.cod_requisicao
                 AND lr.exercicio = ri.exercicio
                 AND lr.cod_centro = ri.cod_centro
                 AND lr.cod_marca = ri.cod_marca
                 AND lr.cod_item = ri.cod_item
                JOIN almoxarifado.lancamento_material AS lm
                  ON lm.cod_lancamento = lr.cod_lancamento
                 AND lm.cod_item = lr.cod_item
                 AND lm.cod_marca = lr.cod_marca
                 AND lm.cod_almoxarifado = lr.cod_almoxarifado
                 AND lm.cod_centro = lr.cod_centro";

    return $stSql;
}

}
