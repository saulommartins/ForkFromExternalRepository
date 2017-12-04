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
    * Paginae Oculta para funcionalidade Reabertura Terminal
    * Data de Criação   : 27/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 32122 $
    $Name$
    $Autor:$
    $Date: 2007-09-11 18:33:05 -0300 (Ter, 11 Set 2007) $

    * Casos de uso: uc-02.04.06

*/

/*
$Log$
Revision 1.13  2007/09/11 21:33:05  luciano
Ticket#10074#

Revision 1.12  2007/09/11 15:49:29  luciano
Ticket#10074#

Revision 1.11  2007/08/02 00:46:37  luciano
Bug#9774#

Revision 1.10  2007/05/08 21:53:01  cako
Bug #9218#

Revision 1.9  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ReaberturaTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

if ( Sessao::read('numCgm') < 1 ) {
    list( $_REQUEST['inCodEntidade'], $_REQUEST['inCodTerminal'] ) = explode( '-', $_REQUEST['inCodTerminal'] );
}

$obRTesourariaBoletim  = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
$obRTesourariaBoletim->listarBoletimAberto( $rsBoletim , 'cod_entidade' );

$boMultiploBoletim = $obRTesourariaBoletim->multiploBoletim();

// se não for multiplos boletins seta o codigo do boletim aberto.
if (!$boMultiploBoletim) {
    if ( !$rsBoletim->eof() ) {
        $obRTesourariaBoletim->setCodBoletim( $rsBoletim->getCampo( "cod_boletim" ) );
    }
}

switch ($_REQUEST["stCtrl"]) {
    case 'mostraSpan':
        if ($_REQUEST["stReabrirTerminal"]=="I") {
            $obRTesourariaTerminal = new RTesourariaTerminal();
            $obRTesourariaTerminal->addUsuarioTerminal();
//            while ( !$rsBoletim->eof() ) {
    //            $obRTesourariaBoletim->setCodBoletim( $rsBoletim->getCampo('cod_boletim' ) );
      //          $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $rsBoletim->getCampo('cod_entidade') );
                $obRTesourariaTerminal->listarSituacaoPorBoletim($rsTerminais, $obRTesourariaBoletim, 'fechado');
  //              $arTerminal[] = $rsTerminais->getElementos();
        //        $rsBoletim->proximo();
//            }

            $arTerminalFinal = array();
            unset($chaveEntidadeTerminal);
            unset($chaveEntidadeTerminalOld);

            foreach ($rsTerminais->arElementos as $arTerm) {
                if( is_array( $arTerm ) )

                    $chaveEntidadeTerminal = $arTerm['cod_entidade'].'-'.$arTerm['cod_terminal'];

                    if ($chaveEntidadeTerminal != $chaveEntidadeTerminalOld) {
                        $arTerminalFinal[] = $arTerm;
                        $chaveEntidadeTerminalOld = $arTerm['cod_entidade'].'-'.$arTerm['cod_terminal'];
                    }
            }

            $rsTerminais = new RecordSet;
            $rsTerminais->preenche( $arTerminalFinal );

            //Define Objeto Select para definir qual formalário será reaberto
            $obCmbTerminais = new Select;
            $obCmbTerminais->setRotulo      ( "Selecione Terminal" );
            $obCmbTerminais->setName        ( "inCodTerminal"      );
            $obCmbTerminais->addOption      ( "","Selecione"       );
            $obCmbTerminais->setCampoId     ( "[cod_entidade]-[cod_terminal]"   );
            $obCmbTerminais->setCampoDesc   ( "[cod_entidade] - [cod_terminal]" );
            $obCmbTerminais->preencheCombo  ( $rsTerminais         );
            $obCmbTerminais->setNull        ( false                );
            $obCmbTerminais->obEvento->setOnChange( "mostraDataLabel(".Sessao::read('numCgm').");" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ($obCmbTerminais);
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            SistemaLegado::executaFrameOculto("d.getElementById('spnTerminais').innerHTML = '".$stHTML."';");
        }
    break;
    case 'mostraDataLabel':
        if (Sessao::read('numCgm') > 0) {
            $obTTesourariaUsuarioTerminal = new TTesourariaUsuarioTerminal;
            $stFiltro .= " AND TUT.cgm_usuario = ".Sessao::read('numCgm');
            if($_REQUEST['inCodTerminal'])
                $stFiltro .= " AND TUT.cod_terminal = ".$_REQUEST['inCodTerminal'];
            $obTTesourariaUsuarioTerminal->recuperaRelacionamento($rsUsuario, $stFiltro );
            if (  ( $rsUsuario->eof() ) || ( $rsUsuario->getNumLinhas() > 0 && $rsUsuario->getCampo('responsavel') != 't') ) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
                SistemaLegado::exibeAviso("Somente o responsável pelo terminal ".$_REQUEST['inCodTerminal']." poderá reabri-lo.","","");
            } else {
                $obRTesourariaTerminal = new RTesourariaTerminal;
                $obRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
                $obRTesourariaTerminal->listarSituacaoPorBoletim($rsReabertura, $obRTesourariaBoletim, 'fechado' );

                $obHdnReabrirTerminal = new Hidden;
                $obHdnReabrirTerminal->setName  ( "stReabrirTerminal"    );
                $obHdnReabrirTerminal->setValue ( "I"                   );
                /*
                $obHdnTimestampTerminal = new Hidden;
                $obHdnTimestampTerminal->setName  ( "stTimestampTerminal" );
                $obHdnTimestampTerminal->setValue ( $rsReabertura->getCampo("timestamp_terminal")  );
                */
                //Define Objeto Text para Nr. do Terminal
                $obTxtDataMovimentacao = new Label;
                $obTxtDataMovimentacao->setName      ( "stDataMovimentacao"                         );
                $obTxtDataMovimentacao->setRotulo    ( "Data Movimentação"                          );

                if ($rsReabertura->getNumLinhas() > 1) {
                    $obTxtDataMovimentacao->setValue     ( 'Há múltiplos boletins para este terminal.' );
                } else {
                    $obTxtDataMovimentacao->setValue     ( $rsReabertura->getCampo("dt_fechamento")         );
                }

                $obFormulario = new Formulario;
                if(Sessao::read('numCgm') > 0)
                $obFormulario->addHidden    ( $obHdnReabrirTerminal   );
                //$obFormulario->addHidden    ( $obHdnTimestampTerminal );

                $obFormulario->addComponente ($obTxtDataMovimentacao);
                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                if ($_REQUEST["inCodTerminal"]) {
                    $stJs  = "f.Ok.disabled = false;\n";
                    $stJs .= "d.getElementById('spnDataMovimentacao').innerHTML = '".$stHTML."';\n";
                } else {
                    $stJs .= "d.getElementById('spnDataMovimentacao').innerHTML = '';\n";
                }
                SistemaLegado::executaFrameOculto( $stJs );
            }
        } else {
            $stJs  = "f.Ok.disabled = false;\n";
            SistemaLegado::executaFrameOculto( $stJs );
        }
    break;
    case 'buscaTerminaisFechados':
        if ($_REQUEST['inCodEntidade']) {

            if ( !$rsBoletim->eof() ) {
                $obRTesourariaTerminal = new RTesourariaTerminal();
                $obRTesourariaTerminal->addUsuarioTerminal();
                $obRTesourariaTerminal->listarSituacaoPorBoletim( $rsTerminais, $obRTesourariaBoletim, 'fechado','cod_entidade, cod_terminal' );

                $inCount = 1;
                unset($chaveEntidadeTerminalOld);
                unset($chaveEntidadeTerminal);

                while ( !$rsTerminais->eof() ) {

                    $chaveEntidadeTerminal = $rsTerminais->getCampo( "cod_entidade" ).'-'.$rsTerminais->getCampo( "cod_terminal");

                    if ($chaveEntidadeTerminal != $chaveEntidadeTerminalOld) {
                        $inCodTerminal = $rsTerminais->getCampo( "cod_terminal" );
                        $stJs2 .= "f.inCodTerminal.options[".$inCount."] = new Option( '".$inCodTerminal."', '".$inCodTerminal."', '' ); \n";

                        $chaveEntidadeTerminalOld = $rsTerminais->getCampo( "cod_entidade" ).'-'.$rsTerminais->getCampo( "cod_terminal");
                        $inCount++;
                    }
                    $rsTerminais->proximo();
                }
            } else {
                SistemaLegado::exibeAviso("Não há boletim aberto para esta entidade!", "", "erro" );
            }
        }
        $stJs  = "d.getElementById('spnDataMovimentacao').innerHTML='';";
        $stJs .= "limpaSelect(f.inCodTerminal,0);";
        $stJs .= "f.inCodTerminal.options[0] = new Option( 'Selecione', '', 'selected' );\n".$stJs2;
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>
