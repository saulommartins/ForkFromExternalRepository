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
    * Pagina de processamento para Empenho - Ordem de Pagamento
    * Data de Criação   : 28/03/2005

    * @author Analista: Diego B. Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2007-08-27 16:19:07 -0300 (Seg, 27 Ago 2007) $

    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.11  2007/08/27 19:19:07  luciano
Bug#9663#

Revision 1.10  2007/08/16 15:51:53  luciano
Bug#9663#,Bug#9921#

Revision 1.9  2007/08/14 14:27:12  luciano
Bug#9663#

Revision 1.8  2007/02/05 18:55:37  rodrigo_sr
Bug #7869#

Revision 1.7  2007/01/03 16:48:36  bruce
Bug #7868#

Revision 1.6  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.5  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos')."&stAcao=".$stAcao;

//Trecho de código do filtro
$stFiltro = '';
if ($stAcao != 'incluir') {
    if ( Sessao::read('filtro') ) {
        $arFiltro = Sessao::read('filtro');
        $stFiltro = '';
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
        }
        $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
    }
}

//Define o nome dos arquivos PHP
$stPrograma      = "ManterPagamentoOrdem";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;

switch ($stAcao) {

    case "pagar":
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem                         ( $_REQUEST["inCodigoOrdem"]    );
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio                           ( $_REQUEST["stExercicio"] );
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"] );
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio($_REQUEST['stExercicioEmpenho']);
        $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano( $_REQUEST["inCodContaBanco"] );
        $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio ( Sessao::getExercicio());
        $obREmpenhoPagamentoLiquidacao->setDataPagamento($_REQUEST["stDtPagamento"]);
        $obREmpenhoPagamentoLiquidacao->setObservacao($_REQUEST["stObservacao"]);
        $obErro = $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->listarItensPagamento ( $rsRecordSet );

        $arValores = array();
        $i = 0;

        while ( !$rsRecordSet->eof() ) {

            $stCampo = "nuVlPagamento_".$rsRecordSet->getCampo('cod_nota')."-".$rsRecordSet->getCampo('ex_nota')."_" . ($i + 1);

            $nuValor   = $_REQUEST[$stCampo];
            $nuValor   = str_replace( '.' , '' , $nuValor );
            $nuValor   = str_replace( ',' , '.', $nuValor );

            if ( $_REQUEST['boAdiantamento'] && $rsRecordSet->getCampo('vl_pago') > 0) {
                 $obErro->setDescricao ( 'Esta OP é de adiantamentos/subvenções e não pode ser paga novamente.' );
                 break;
            }

            if ($nuValor <= 0) {
                $obErro->setDescricao ( 'Valor a Pagar deve ser maior que zero.' );
                break;
            }

            if ( $nuValor <= $rsRecordSet->getCampo( 'vl_pagamento' )) {
                $arValores[$i]['cod_nota']   = $rsRecordSet->getCampo( 'cod_nota'   );
                $arValores[$i]['exercicio']  = $rsRecordSet->getCampo( 'ex_nota'    );
                $arValores[$i]['vl_a_pagar'] = $rsRecordSet->getCampo( 'vl_pagamento' );
                $arValores[$i++]['vl_pago']  = $nuValor;
            } else {
                $obErro->setDescricao( 'Valor a pagar excede o valor disponível para pagamento!' );
                break;
            }

            $rsRecordSet->proximo();
        }

        $obREmpenhoPagamentoLiquidacao->setValoresPagos( $arValores );

        if ( !$obErro->ocorreu() )
            $obErro = $obREmpenhoPagamentoLiquidacao->pagarOP();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId().$stLink.$stFiltro,"Realizados pagamentos para a Ordem: ".$_REQUEST["inCodigoOrdem"],"incluir","aviso", Sessao::getId(), "../");
        }

    break;
}

if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_pagar","erro");
}

?>
