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
    * Página de Processamento para Pagamento do módulo Tesouraria
    * Data de Criação   : 10/01/2006

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.10  2006/07/05 20:39:07  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"     );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case 'incluir':

        $obRTesourariaBoletim = new RTesourariaBoletim;

        $obRTesourariaBoletim->setCodBoletim($_REQUEST['inCodBoletim']);
        $obRTesourariaBoletim->setExercicio($_REQUEST['stExercicio']);
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST['inCodTerminal']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST['stTimestampTerminal']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCgm(Sessao::read('numCgm'));
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST['stTimestampUsuario']);
        $obRTesourariaBoletim->setDataBoletim($_REQUEST['stDtBoletim']);

        $obRTesourariaBoletim->addBordero();

        $arItens = Sessao::read('arItens');

        if ( count($arItens) > 0 ) {

            foreach ($arItens as $arValues) {

                $obRTesourariaBoletim->roUltimoBordero->addTransacaoTransferencia();

                $flValor = str_replace( '.','',$arValues['inValor'] );
                $flValor = str_replace( ',','.',$flValor );

                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($arValues['inCodigoEntidade']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->obRCGM->setNumCGM($arValues['inCodCredor']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->roRTesourariaBordero->setExercicio($_REQUEST['stExercicio']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->setTipo($arValues['stTipoTransacao']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->obRMONAgencia->setCodAgencia($arValues['inCodAgenciaCredor']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->obRMONAgencia->obRMONBanco->setCodBanco($arValues['inCodBancoCredor']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->setContaCorrente($arValues['stNumeroContaCredor']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->setNumDocumento($arValues['inNrDocumento']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->setDescricao($arValues['stObservacao']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->setValorTransferencia($flValor);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoTransferencia->obRContabilidadePlanoBanco->setCodPlano($arValues['inCodContaCredor']);

            }
        }

        if ( $_REQUEST['stDtBoletim'] == date( 'd/m/Y' ) ) {
            $obRTesourariaBoletim->roUltimoBordero->setTimestampBordero( date( 'Y-m-d H:i:s.ms' ) );
        } else {
            list( $stDia, $stMes, $stAno ) = explode( '/', $_REQUEST['stDtBoletim'] );
            $obRTesourariaBoletim->roUltimoBordero->setTimestampBordero( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
        }

        $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade'] );
        $obRTesourariaBoletim->roUltimoBordero->obRContabilidadePlanoBanco->setCodPlano($_REQUEST['inCodConta'] );
        $obRTesourariaBoletim->roUltimoBordero->roRTesourariaBoletim->setCodBoletim($_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->roUltimoBordero->setExercicio($_REQUEST['stExercicio'] );
        $obRTesourariaBoletim->roUltimoBordero->roRTesourariaBoletim->setExercicio( $_REQUEST['stExercicioBoletim']);
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST['inCodTerminal']);
        $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm') );
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST['stTimestampTerminal'] );
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST['stTimestampUsuario'] );

        $obRTesourariaBoletim->roUltimoBordero->addAssinatura();

        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');

        for ($x=1; $x<=3; $x++) {

            if ($_REQUEST["inNumAssinante_".$x]) {

                $obRTesourariaBoletim->roUltimoBordero->addAssinatura();

                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obRCGM->setNumCGM($_REQUEST["inNumAssinante_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setCargo($_REQUEST["stCargo_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setNumMatricula($_REQUEST["inNumMatricula_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setSituacao(true);
            }
        }

        $obErro = $obRTesourariaBoletim->roUltimoBordero->incluirTransacaoTransferenciaAssinatura( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Borderô de Transferência","incluir","aviso", Sessao::getId(), "../");

            $stCaminho = CAM_GF_TES_INSTANCIAS."bordero/OCRelatorioBordero.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho;
            $stCampos .= "&inCodBordero=".$obRTesourariaBoletim->roUltimoBordero->getCodBordero();
            $stCampos .= "&stExercicio=".$obRTesourariaBoletim->roUltimoBordero->getExercicio();
            $stCampos .= "&inCodEntidade=".$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->getCodigoEntidade();
            $stCampos .= "&stTipoBordero=T";
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;

    case 'alterar':

    break;

}

?>
