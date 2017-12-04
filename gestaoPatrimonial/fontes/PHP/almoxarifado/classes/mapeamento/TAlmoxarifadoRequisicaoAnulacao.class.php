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

    $Revision: 26901 $
    $Name$
    $Author: bruce $
    $Date: 2007-11-26 10:04:42 -0200 (Seg, 26 Nov 2007) $

    * Casos de uso: uc-03.03.11
*/

/*
$Log$
Revision 1.6  2006/12/11 13:11:24  hboaventura
acrescentado os campos "Requisitante" e "Solicitante" no Filtro

Revision 1.5  2006/11/29 11:04:21  tonismar
colocado formatação para data

Revision 1.4  2006/10/13 11:36:22  larocca
Correção na lista de anulação de requisições.

Revision 1.3  2006/10/13 09:12:14  larocca
Alteração nas Querys de Anulação de Requisição e Consulta de Requisição

Revision 1.2  2006/10/10 14:52:45  larocca
BUG #7153#

Revision 1.1  2006/08/01 20:31:52  tonismar
Classe de requisição anulação

Revision 1.10  2006/07/06 14:04:44  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:09:27  diego

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
class TAlmoxarifadoRequisicaoAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoRequisicaoAnulacao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.requisicao_anulacao');

    $this->setCampoCod('cod_requisicao');
    $this->setComplementoChave('exercicio,cod_almoxarifado');

    $this->AddCampo('exercicio','char',true,'4',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('cod_requisicao','integer',true,'',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoRequisicao');
    $this->AddCampo('motivo','varchar',false,'500,',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
}

function recuperaRequisicaoAlteracaoAnulacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRequisicaoAlteracaoAnulacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRequisicaoAlteracaoAnulacao()
{
$stSql .= "    Select                                                    \n";
$stSql .= "         req.exercicio                                        \n";
$stSql .= "        ,req.cod_almoxarifado                                 \n";
$stSql .= "        ,req.cod_requisicao                                   \n";
$stSql .= "        ,req.cgm_requisitante                                 \n";
$stSql .= "        ,req.cgm_solicitante                                  \n";
$stSql .= "        ,to_char(req.dt_requisicao, 'dd/mm/yyyy') as dt_requisicao  \n";
$stSql .= "        ,req.observacao                                       \n";
$stSql .= "        ,cgm.nom_cgm                                          \n";
$stSql .= "        ,cgm.numcgm                                           \n";
$stSql .= "        ,cgm2.nom_cgm as nom_solicitante                      \n";
$stSql .= "        ,cgm2.numcgm as num_solicitante                       \n";
//$stSql .= "        ,sum(aria.quantidade) as qtd_anulada                  \n";
$stSql .= "    From                                                      \n";
$stSql .= "        almoxarifado.requisicao as req                        \n";
$stSql .= "    Inner Join                                                \n";
$stSql .= "        almoxarifado.requisicao_item as reqi                  \n";
$stSql .= "    On                                                        \n";
$stSql .= "            reqi.cod_almoxarifado = req.cod_almoxarifado      \n";
$stSql .= "        And reqi.cod_requisicao   = req.cod_requisicao        \n";
$stSql .= "        And reqi.exercicio        = req.exercicio             \n";
$stSql .= "    Inner Join                                                \n";
$stSql .= "        almoxarifado.almoxarifado as almoxarifado             \n";
$stSql .= "    On                                                        \n";
$stSql .= "        almoxarifado.cod_almoxarifado = req.cod_almoxarifado  \n";
$stSql .= "    Inner Join                                                \n";
$stSql .= "        sw_cgm as cgm                                         \n";
$stSql .= "    On                                                        \n";
$stSql .= "            almoxarifado.cgm_almoxarifado = cgm.numcgm        \n";
$stSql .= "    Inner Join                                                \n";
$stSql .= "        sw_cgm as cgm2                                        \n";
$stSql .= "    On                                                        \n";
$stSql .= "            req.cgm_requisitante  = cgm2.numcgm               \n";
/*$stSql .= "    Left Join                                                 \n";
$stSql .= "        almoxarifado.requisicao_itens_anulacao as aria        \n";
$stSql .= "    On                                                        \n";
$stSql .= "            reqi.cod_almoxarifado = aria.cod_almoxarifado     \n";
$stSql .= "        And reqi.cod_requisicao   = aria.cod_requisicao       \n";
$stSql .= "        And reqi.cod_item         = aria.cod_item             \n";
$stSql .= "        And reqi.cod_marca        = aria.cod_marca            \n";
$stSql .= "        And reqi.cod_centro       = aria.cod_centro           \n";
$stSql .= "        And reqi.exercicio        = aria.exercicio            \n";
$stSql .= "    Left Join                                                 \n";
$stSql .= "        almoxarifado.lancamento_requisicao as alr             \n";
$stSql .= "    On                                                        \n";
$stSql .= "            alr.cod_almoxarifado  = reqi.cod_almoxarifado     \n";
$stSql .= "        And alr.cod_requisicao    = reqi.cod_requisicao       \n";
$stSql .= "        And alr.cod_item          = reqi.cod_item             \n";
$stSql .= "        And alr.cod_marca         = reqi.cod_marca            \n";
$stSql .= "        And alr.cod_centro        = reqi.cod_centro           \n";
$stSql .= "        And alr.exercicio         = reqi.exercicio            \n";*/
$stSql .= "    Where                                                     \n";
//$stSql .= "            reqi.quantidade       <> coalesce((Select sum(quantidade) From almoxarifado.requisicao_itens_anulacao as ra Inner Join almoxarifado.requisicao_anulacao as rr On rr.cod_requisicao = ra.cod_requisicao Where ra.cod_requisicao = reqi.cod_requisicao),0) \n";
$stSql .= "            reqi.quantidade       > (coalesce((Select sum(quantidade) From almoxarifado.requisicao_itens_anulacao as ra Where ra.cod_requisicao = reqi.cod_requisicao And ra.cod_almoxarifado = reqi.cod_almoxarifado And ra.exercicio = reqi.exercicio And ra.cod_item = reqi.cod_item And ra.cod_marca = reqi.cod_marca And ra.cod_centro = reqi.cod_centro),0) +  \n";
$stSql .= "         coalesce((Select (sum(quantidade)*-1) From almoxarifado.lancamento_requisicao as lr Inner Join almoxarifado.lancamento_material as lm On lm.cod_lancamento = lr.cod_lancamento And lm.cod_item = lr.cod_item And lm.cod_almoxarifado = lr.cod_almoxarifado And lm.cod_marca = lr.cod_marca And lm.cod_centro = lr.cod_centro Where lr.cod_requisicao = reqi.cod_requisicao And lr.cod_item = reqi.cod_item And lr.cod_marca = reqi.cod_marca And lr.cod_centro = reqi.cod_centro And lr.cod_almoxarifado = reqi.cod_almoxarifado And lr.exercicio = reqi.exercicio ),0)) \n";

/*	$stSql = " select                                                    \n";
    $stSql .= " 	req.exercicio                                         \n";
    $stSql .= " 	,req.cod_almoxarifado                                 \n";
    $stSql .= " 	,req.cod_requisicao                                   \n";
    $stSql .= " 	,req.cgm_requisitante                                 \n";
    $stSql .= " 	,req.cgm_solicitante                                  \n";
    $stSql .= " 	,req.dt_requisicao                                    \n";
    $stSql .= " 	,req.observacao                                       \n";
    $stSql .= " 	,cgm.nom_cgm                                          \n";
    $stSql .= " 	,cgm.numcgm                                           \n";
    $stSql .= " 	,cgm2.nom_cgm as nom_solicitante                      \n";
    $stSql .= " 	,cgm2.numcgm as num_solicitante                       \n";
    $stSql .= "     ,sum(aria.quantidade) as qtd_anulada                     \n";
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
    $stSql .= " where                                                     \n";
    $stSql .= " 		req.exercicio = reqi.exercicio                    \n";
    $stSql .= " 	and req.cod_almoxarifado = reqi.cod_almoxarifado      \n";
    $stSql .= " 	and req.cod_requisicao = reqi.cod_requisicao          \n";
    $stSql .= " 	and req.cgm_solicitante = cgm.numcgm                  \n";
    $stSql .= " 	and req.cgm_requisitante = cgm2.numcgm                \n";*/
    //$stSql .= " and reqi.quantidade > coalesce((select sum(ra.quantidade) from almoxarifado.requisicao_itens_anulacao as ra inner join almoxarifado.requisicao_item as ri on ri.cod_centro = ra.cod_centro and ri.cod_item = ra.cod_item and ri.cod_marca = ra.cod_marca and ri.cod_requisicao = ra.cod_requisicao and ri.exercicio = ra.exercicio and ri.cod_almoxarifado = ra.cod_almoxarifado and ri.cod_requisicao = reqi.cod_requisicao ),0) \n";
    if ( $this->getDado('acao') == 'anular' ) {
        //$stSql .= "    and aria.cod_item is not null \n";
    } else {
        $stSql .= "    and aria.cod_item is null \n";
    }
//	$stSql .= "    and alr.cod_item is null                                  \n";
    return $stSql;
}

}
