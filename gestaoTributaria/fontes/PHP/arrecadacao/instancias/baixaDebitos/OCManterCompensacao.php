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
  * Página de formulário oculto
  * Data de criação : 11/12/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: OCManterCompensacao.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.03.10
**/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

switch ($_REQUEST['stCtrl']) {
    case "SomaParcelasPagas":
        $flTotalPago = 0.00;
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match( "/boParPaga_[0-9]/", $valor) ) {
                $arKey = explode('§',$key);
                $flTotalPago += number_format(str_replace( ',', '.', str_replace('.','', $arKey[3])), 2, '.', '');
            }
        }

        Sessao::write( "total_pago", $flTotalPago );
        Sessao::write( "total_compensacao", $flTotalPago + Sessao::read( "saldo_disponivel") );
        //------------------------------------
        $flTotalCompensar = 0.00;
        $boDivida = false;
        $boUnica = false;
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                if ($arKey[4] == -1) {
                    $boDivida = true;
                }
            }
        }

        $stJs = "";
        $inDevendo = 0;
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                $arKey = explode('§',$key);
                if ( $_REQUEST["boAplicaAcrescimos"] )
                    $flTotalCompensar += number_format(str_replace( ',', '.', str_replace('.','', $arKey[3])), 2, '.', '');
                else
                    $flTotalCompensar += number_format(str_replace( ',', '.', str_replace('.','', $arKey[2])), 2, '.', '');

                if ( ( Sessao::read( "total_compensacao" ) - $flTotalCompensar <= 0.00 ) && !$boDivida ) {
                    if ($arKey[7] == 0) { //eh parcela unica
                        $boUnica = true;
                        break;
                    }else
                    if ($inDevendo) {
                        if ( $_REQUEST["boAplicaAcrescimos"] )
                            $flTotalCompensar -= number_format(str_replace( ',', '.', str_replace('.','', $arKey[3])), 2, '.', '');
                        else
                            $flTotalCompensar -= number_format(str_replace( ',', '.', str_replace('.','', $arKey[2])), 2, '.', '');

                        $stJs .= 'd.frm.'.$valor.'.checked = false;';
                    }

                    $inDevendo++;
                }
            }
        }

        if ($inDevendo > 1) {
            $stJs .= "alertaAviso('Total a compensar insuficiente para carne.', 'form','erro','".Sessao::getId()."');";
        }

        if ($boUnica) {
            $flTotalCompensar = 0.00;
            foreach ($_REQUEST as $valor => $key) {
                if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                    $stJs .= 'd.frm.'.$valor.'.checked = false;';
                }
            }

            $stJs .= "alertaAviso('Total a compensar insuficiente para carne de parcela única.', 'form','erro','".Sessao::getId()."');";
        }else
        if ($boDivida) {
            if ( Sessao::read( "total_compensacao" ) - $flTotalCompensar <= 0.00 ) {
                $flTotalCompensar = 0.00;
                foreach ($_REQUEST as $valor => $key) {
                    if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                        $stJs .= 'd.frm.'.$valor.'.checked = false;';
                    }
                }

                $stJs .= "alertaAviso('Total a compensar insuficiente para carne da Dívida Ativa.', 'form','erro','".Sessao::getId()."');";
            }
        }

        Sessao::write( "total_compensar", $flTotalCompensar );
        //------------------------------------

        Sessao::write( "saldo_restante", Sessao::read( "total_compensacao" ) - Sessao::read( "total_compensar" ) );

        $flSaldoRestante = number_format( Sessao::read( "saldo_restante" ), 2, ',', '.' );

        $flTotalCompensacao = number_format( $flTotalPago + Sessao::read( "saldo_disponivel" ), 2, ',', '.' );
        $flTotalPago = number_format( $flTotalPago, 2, ',', '.' );
        $flTotalCompensar = number_format( $flTotalCompensar, 2, ',', '.' );
        $stJs .= 'd.getElementById("lblVlrComp").innerHTML = "'.$flTotalCompensar.'";';
        $stJs .= 'd.getElementById("lblVlrParcSel").innerHTML = "'.$flTotalPago.'";';
        $stJs .= 'd.getElementById("lblTotComp").innerHTML = "'.$flTotalCompensacao.'";';
        $stJs .= 'd.getElementById("lblSldRest").innerHTML = "'.$flSaldoRestante.'";';
        if ( Sessao::read( "total_compensacao" ) > 0.00 )
            $stJs .= 'd.frm.boCompensar.value = 1';
        else
            $stJs .= 'd.frm.boCompensar.value = 0';

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "SomaParcelasVencer":
        $flTotalCompensar = 0.00;
        $boDivida = false;
        $boUnica = false;
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                if ($arKey[4] == -1) {
                    $boDivida = true;
                }
            }
        }

        $stJs = "";
        $inDevendo = 0;
        foreach ($_REQUEST as $valor => $key) {
            if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                $arKey = explode('§',$key);
                if ( $_REQUEST["boAplicaAcrescimos"] )
                    $flTotalCompensar += number_format(str_replace( ',', '.', str_replace('.','', $arKey[3])), 2, '.', '');
                else
                    $flTotalCompensar += number_format(str_replace( ',', '.', str_replace('.','', $arKey[2])), 2, '.', '');

                if ( ( Sessao::read( "total_compensacao" ) - $flTotalCompensar <= 0.00 ) && !$boDivida ) {
                    if ($arKey[7] == 0) { //eh parcela unica
                        $boUnica = true;
                        break;
                    }else
                    if ($inDevendo) {
                        if ( $_REQUEST["boAplicaAcrescimos"] )
                            $flTotalCompensar -= number_format(str_replace( ',', '.', str_replace('.','', $arKey[3])), 2, '.', '');
                        else
                            $flTotalCompensar -= number_format(str_replace( ',', '.', str_replace('.','', $arKey[2])), 2, '.', '');

                        $stJs .= 'd.frm.'.$valor.'.checked = false;';
                    }

                    $inDevendo++;
                }
            }
        }

        if ($inDevendo > 1) {
            $stJs .= "alertaAviso('Total a compensar insuficiente para carne.', 'form','erro','".Sessao::getId()."');";
        }

        if ($boUnica) {
            $flTotalCompensar = 0.00;
            foreach ($_REQUEST as $valor => $key) {
                if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                    $stJs .= 'd.frm.'.$valor.'.checked = false;';
                }
            }

            $stJs .= "alertaAviso('Total a compensar insuficiente para carne de parcela única.', 'form','erro','".Sessao::getId()."');";
        }else
        if ($boDivida) {
            if ( Sessao::read( "total_compensacao" ) - $flTotalCompensar <= 0.00 ) {
                $flTotalCompensar = 0.00;
                foreach ($_REQUEST as $valor => $key) {
                    if ( preg_match( "/boParVenc_[0-9]/", $valor) ) {
                        $stJs .= 'd.frm.'.$valor.'.checked = false;';
                    }
                }

                $stJs .= "alertaAviso('Total a compensar insuficiente para carne da Dívida Ativa.', 'form','erro','".Sessao::getId()."');";
            }
        }

        Sessao::write( "total_compensar", $flTotalCompensar );
        Sessao::write( "saldo_restante", Sessao::read( "total_compensacao" ) - $flTotalCompensar );
        $flSaldoRestante = number_format( Sessao::read( "total_compensacao" ) - $flTotalCompensar, 2, ',', '.' );
        $flTotalCompensar = number_format( $flTotalCompensar, 2, ',', '.' );
        $stJs .= 'd.getElementById("lblVlrComp").innerHTML = "'.$flTotalCompensar.'";';
        $stJs .= 'd.getElementById("lblSldRest").innerHTML = "'.$flSaldoRestante.'";';
        if ( Sessao::read( "total_compensacao" ) > 0.00 )
            $stJs .= 'd.frm.boCompensar.value = 1';
        else
            $stJs .= 'd.frm.boCompensar.value = 0';

        sistemaLegado::executaFrameOculto( $stJs );
        break;
}
