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
    * Data de Criação   : 29/03/2005

    * @author Analista: Diego B. Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor: $
    $Date: 2007-05-30 10:17:32 -0300 (Qua, 30 Mai 2007) $

    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.14  2007/05/30 13:14:45  luciano
#9090#

Revision 1.13  2007/04/05 15:16:08  cako
Bug #8996#

Revision 1.12  2007/02/05 18:54:09  rodrigo_sr
Bug #7871#

Revision 1.11  2007/01/08 13:04:53  bruce
Bug #7868#

Revision 1.10  2006/10/11 17:29:36  cako
Ajustes

Revision 1.9  2006/10/02 16:51:39  cleisson
ajustes

Revision 1.8  2006/09/29 16:56:45  jose.eduardo
Bug #7060#

Revision 1.7  2006/09/28 16:37:10  eduardo
Bug #7060#

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
$stPrograma      = "ManterEstornoPagamentoOrdem";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
include_once( $pgJs );

$obREmpenhoPagamentoLiquidacao = new REmpenhoPagamentoLiquidacao;

switch ($stAcao) {
    case "estornar":
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem                         ( $_REQUEST["inCodigoOrdem"]       );
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio                           ( $_REQUEST["stExercicio"]         );
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio($_REQUEST['stExercicioEmpenho']);
        $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"]    );
 //     $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano                 ( $_REQUEST["inCodContaBanco"]     );
 //     $obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio                ( Sessao::getExercicio()               );
        $obREmpenhoPagamentoLiquidacao->setDataAnulacao                                                  ( $_REQUEST["stDtAnulacao"]        );
        $obREmpenhoPagamentoLiquidacao->setObservacao                                                    ( $_REQUEST["stObservacao"]        );

        $obErro = $obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->listarItensEstorno ( $rsRecordSet );
        $arValores = array();
        $i = 0;
        $inCount = 0;
        $nuValorTotal = 0;
        $stMaiorData = 0;
        while ( !$rsRecordSet->eof() ) {

            $obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->setCodNota( $rsRecordSet->getCampo("cod_nota") );
            $obREmpenhoPagamentoLiquidacao->setExercicio( $rsRecordSet->getCampo("ex_nota") );

            $stCampo = "nuVlPagamento_".$rsRecordSet->getCampo('cod_nota')."_".$rsRecordSet->getCampo('ex_nota')."_".$rsRecordSet->getCampo('dt_pagamento').'_'.($i + 1);
            $nuValor   = $_REQUEST[$stCampo];
            $nuValor   = str_replace( '.' , '' , $nuValor );
            $nuValor   = str_replace( ',' , '.', $nuValor );

            if ($nuValor > 0.00) {
                if ( $nuValor <= $rsRecordSet->getCampo( 'vl_pagonaoprestado' ) ) {
                    $arValores[$inCount]['cod_nota']   = $rsRecordSet->getCampo( 'cod_nota'   );
                    $arValores[$inCount]['exercicio']  = $rsRecordSet->getCampo( 'ex_nota'    );
                    $arValores[$inCount]['vl_pago']    = $rsRecordSet->getCampo( 'vl_pago'    );
                    $arValores[$inCount]['cod_plano']  = $rsRecordSet->getCampo( 'cod_plano' );
                    $arValores[$inCount]['exercicio_plano'] = $rsRecordSet->getCampo( 'exercicio_plano' );
                    $arValores[$inCount]['timestamp'] = $rsRecordSet->getCampo( 'timestamp' );
                    $arValores[$inCount]['vl_estornado']  = $nuValor;
                    $nuValorTotal = $nuValorTotal + $nuValor;
                    $inCount++;

                    $arData = explode('/',$rsRecordSet->getCampo('dt_pagamento'));
                    $stData = $arData[2].$arData[1].$arData[0];
                    if ($stData >= $stMaiorData) {
                        $stMaiorData = $stData;
                        $dtMaiorData = $arData[0].'/'.$arData[1].'/'.$arData[2];
                    }
                } else {
                    $obErro->setDescricao( 'Valor a estornar da nota '.$rsRecordSet->getcampo('cod_nota').'/'.$rsRecordSet->getCampo('ex_nota').' excede o valor pago!' );
                    break;
                }
            }
            $rsRecordSet->proximo();
            $i++;
        }
        if (!$obErro->ocorreu()) {
            if ($nuValorTotal > 0) {
                $arDataEstorno = explode('/',$_REQUEST['stDtAnulacao']);
                $stDtEstorno = $arDataEstorno[2].$arDataEstorno[1].$arDataEstorno[0];
                if ($stDtEstorno >= $stMaiorData) {
                    $obREmpenhoPagamentoLiquidacao->setDataPagamento ( $dtMaiorData );
                    $obREmpenhoPagamentoLiquidacao->setValoresPagos  ( $arValores );
                } else {
                    $obErro->setDescricao('A data do estorno deve ser igual ou superior à data do pagamento mais recente com valor a estornar informado ('.$dtMaiorData.').');
                }
            } else {
                $obErro->setDescricao('O valor a estornar deve ser maior que 0,00');
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obREmpenhoPagamentoLiquidacao->estornarOP();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId().$stLink.$stFiltro,"Estorno de Pagamento de O.P. concluído com sucesso! (Realizados estornos para a Ordem: ".$_REQUEST["inCodigoOrdem"].")","estornar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_estornar","erro");
        }
    break;
}

?>
