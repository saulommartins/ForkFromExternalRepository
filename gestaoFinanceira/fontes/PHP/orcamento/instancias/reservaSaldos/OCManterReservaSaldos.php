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
    * Paginae Oculta de Reserva de Saldos
    * Data de Criação   : 04/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterReservaSaldos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');
$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;

function montaLabel($flSaldoDotacao)
{
    $flSaldoDotacao = number_format( $flSaldoDotacao ,2,',','.');

    $obHdnSaldo = new Hidden;
    $obHdnSaldo->setName ( "flVlSaldoDotacao" );
    $obHdnSaldo->setValue( $flSaldoDotacao );

    $obLblSaldo = new Label;
    $obLblSaldo->setRotulo( "Saldo da Dotação" );
    $obLblSaldo->setValue ( $flSaldoDotacao );

    $obFormulario = new Formulario;
    $obFormulario->addHidden( $obHdnSaldo );
    $obFormulario->addComponente( $obLblSaldo );
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnSaldoDotacao').innerHTML = '".$stHtml."';";

    return $js1;
}

switch ($stCtrl) {
 case 'buscaDespesa':

    if ($request->get('inCodDespesa') != "" and $request->get('inCodigoEntidade') != "") {

        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodigoEntidade') );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->setdataEmpenho($request->get('dtDataReserva'));
        $obREmpenhoAutorizacaoEmpenho->setCodEntidade($request->get('inCodigoEntidade'));
        $obREmpenhoAutorizacaoEmpenho->setTipoEmissao('R');
        $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnteriorDataEmpenho($nuSaldoDotacao);

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesa.focus();';
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$request->get('inCodDespesa').")','form','erro','".Sessao::getId()."');";
            $js .= "d.getElementById('spnSaldoDotacao').innerHTML = '';";
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
            $js .= montaLabel( $nuSaldoDotacao );
        }
    } else $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
    $js .= "LiberaFrames(true,false);";
    SistemaLegado::executaFrameOculto($js);
    break;
}
?>
