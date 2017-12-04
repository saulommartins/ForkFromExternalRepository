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
    * Paginae Oculta para funcionalidade Fechamento Terminal
    * Data de Criação   : 07/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Id: OCFechamentoTerminal.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php" );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "FechamentoTerminal";
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
//$obRTesourariaBoletim->listarBoletimAberto( $rsBoletim );
//if ( !$rsBoletim->eof() ) {
//    $obRTesourariaBoletim->setCodBoletim( $rsBoletim->getCampo( "cod_boletim" ) );
//}

switch ($_REQUEST["stCtrl"]) {
    case 'mostraSpan':
        if ($_REQUEST["stFecharTerminal"]=="I") {
            $obRTesourariaTerminal = new RTesourariaTerminal();
            $obRTesourariaTerminal->addUsuarioTerminal();
            $obRTesourariaTerminal->listarSituacaoAbertosAtivos($rsTerminais,'cod_entidade,cod_terminal');
            if (!$rsTerminais->eof()) {

                $arTerminais = $rsTerminais->getElementos();

                unset($chaveEntidadeTerminal);
                unset($chaveEntidadeTerminalOld);

                foreach ($arTerminais as $arTerminal) {

                    $chaveEntidadeTerminal = $arTerminal['cod_entidade'].'-'.$arTerminal['cod_terminal'];

                    // Verificação para não listar repetido os terminais se houver multiplos boletins
                    if ($chaveEntidadeTerminal != $chaveEntidadeTerminalOld) {
                        $arTerminalUnico[] = $arTerminal;
                        $chaveEntidadeTerminalOld = $arTerminal['cod_entidade'].'-'.$arTerminal['cod_terminal'];
                    }
                }

                $rsTerminais = new RecordSet;
                $rsTerminais->preenche( $arTerminalUnico );
            }

            //Define Objeto Select para definir qual formalário será fechado
            $obCmbTerminais = new Select;
            $obCmbTerminais->setRotulo      ( "Selecione Terminal"                       );
            $obCmbTerminais->setTitle       ( "Selecione Entidade - Terminal que deseja fechar" );
            $obCmbTerminais->setName        ( "inCodTerminal"                            );
            $obCmbTerminais->addOption      ( "","Selecione"                             );
            $obCmbTerminais->setCampoId     ( "[cod_entidade]-[cod_terminal]"  );
            $obCmbTerminais->setCampoDesc   ( "[cod_entidade] - [cod_terminal]"      );
            $obCmbTerminais->preencheCombo  ( $rsTerminais         );
            $obCmbTerminais->setNull        ( false                                      );
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

            $stJs .= "d.getElementById('spnTerminais').innerHTML = '".$stHTML."';\n";

            SistemaLegado::executaFrameOculto( $stJs );
        }
    break;
    case 'mostraData':

        if ($_REQUEST["stFecharTerminal"]=="T") {
            //Define Objeto Text para Nr. do Terminal
/*            $obTxtDataMovimentacao = new Data;
            $obTxtDataMovimentacao->setName      ( "stDataMovimentacao"                         );
            $obTxtDataMovimentacao->setValue     ( $stDataMovimentacao                          );
            $obTxtDataMovimentacao->setRotulo    ( "Data Movimentação"                          );
            //$obTxtDataMovimentacao->setTitle     ( "Informe a Data de Movimentação do Boletim"  );
            $obTxtDataMovimentacao->setNull      ( false                                        );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ($obTxtDataMovimentacao);
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","'",$stHTML );
*/
            $stHTML = "";

            $stJs .= "d.getElementById('spnDataMovimentacao').innerHTML = '".$stHTML."';\n";

            SistemaLegado::executaFrameOculto( $stJs );

        }

    break;
    case 'mostraDataLabel':
        if (Sessao::read('numCgm')>0) {
            $obTTesourariaUsuarioTerminal = new TTesourariaUsuarioTerminal;
            if (Sessao::read('numCgm') != 0) {
                $stFiltro .= " AND TUT.cgm_usuario = ".Sessao::read('numCgm');
            }
            if($_REQUEST['inCodTerminal'])
                $stFiltro .= " AND TUT.cod_terminal = ".$_REQUEST['inCodTerminal'];
            $obTTesourariaUsuarioTerminal->recuperaRelacionamento($rsUsuario, $stFiltro );
            //$obTTesourariaUsuarioTerminal->debug();

            if ( ( ($rsUsuario->eof() ) || ( $rsUsuario->getNumLinhas() > 0) && $rsUsuario->getCampo('responsavel') != 't' && Sessao::read('numCgm') != 0) ) {
                include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
                $stJs =  "d.getElementById('spnDataMovimentacao').innerHTML = '';\n";
                $stJs .= "f.Ok.disabled = true;\n";
                SistemaLegado::executaFrameOculto( $stJs );
                SistemaLegado::exibeAviso("Somente o responsável pelo terminal ".$_REQUEST['inCodTerminal']." poderá fechá-lo.","","");
            } else {
                  $obRTesourariaTerminal = new RTesourariaTerminal;
                  $obRTesourariaTerminal->setCodTerminal($_REQUEST["inCodTerminal"]);
                  $obRTesourariaTerminal->listarSituacaoPorBoletim( $rsAbertura, $obRTesourariaBoletim, 'aberto' );

                  $obHdnFecharTerminal = new Hidden;
                  $obHdnFecharTerminal->setName  ( "stFecharTerminal"    );
                  $obHdnFecharTerminal->setValue ( "I"                   );

                  /* Comentado para pegar os valores no PR
                  $obHdnTimestampTerminal = new Hidden;
                  $obHdnTimestampTerminal->setName  ( "stTimestampTerminal" );
                  $obHdnTimestampTerminal->setValue ( $rsAbertura->getCampo("timestamp_terminal")  );

                  $obHdnTimestampAbertura = new Hidden;
                  $obHdnTimestampAbertura->setName  ( "stTimestampAbertura"    );
                  $obHdnTimestampAbertura->setValue ( $rsAbertura->getCampo("timestamp_abertura")  );

                  $obHdnCgmTerminal = new Hidden;
                  $obHdnCgmTerminal->setName  ( "inCodCgmTerminal"    );
                  $obHdnCgmTerminal->setValue ( $rsAbertura->getcampo("cgm_usuario") );
                  */

                  //Define Objeto Text para Nr. do Terminal
                  $obTxtDataMovimentacao = new Label;
                  $obTxtDataMovimentacao->setName      ( "stDataMovimentacao"                         );
                  $obTxtDataMovimentacao->setRotulo    ( "Data Movimentação"                          );
                  if ($rsAbertura->getNumLinhas() > 1) {
                    $obTxtDataMovimentacao->setValue     ( 'Há múltiplos boletins para este terminal.' );
                  } else {
                    $obTxtDataMovimentacao->setValue     ( $rsAbertura->getCampo("dt_abertura")         );
                  }

                  $obFormulario = new Formulario;
                  if(Sessao::read('numCgm') > 0)
                  $obFormulario->addHidden    ( $obHdnFecharTerminal                      );
                  //$obFormulario->addHidden    ( $obHdnTimestampTerminal                 );
                  //$obFormulario->addHidden    ( $obHdnTimestampAbertura                 );
                  //$obFormulario->addHidden    ( $obHdnCgmTerminal                       );
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
                      $stJs .=  "d.getElementById('spnDataMovimentacao').innerHTML = '';\n";
                  }
                SistemaLegado::executaFrameOculto( $stJs );
            }
        } else {
            $stJs  = "f.Ok.disabled = false;\n";
            SistemaLegado::executaFrameOculto( $stJs );
        }
    break;

    case 'buscaTerminaisAbertos':
        if ($_REQUEST['inCodEntidade']) {

            $obRTesourariaTerminal = new RTesourariaTerminal();
            $obRTesourariaTerminal->addUsuarioTerminal();
            $obRTesourariaTerminal->listarSituacaoPorBoletim( $rsTerminais, $obRTesourariaBoletim, 'aberto','cod_entidade, cod_terminal' );

            $inCount = 1;
            unset($chaveEntidadeTerminalOld);
            unset($chaveEntidadeTerminal);

            while ( !$rsTerminais->eof() ) {

                $chaveEntidadeTerminal = $rsTerminais->getCampo( "cod_entidade" ).'-'.$rsTerminais->getCampo( "cod_terminal");

                // Verificação para não listar repetido os terminais se houver multiplos boletins
                if ($chaveEntidadeTerminal != $chaveEntidadeTerminalOld) {
                    $inCodTerminal = $rsTerminais->getCampo( "cod_terminal" );
                    $stJs2 .= "f.inCodTerminal.options[".$inCount."] = new Option( '".$inCodTerminal."', '".$inCodTerminal."', '' ); \n";

                    $chaveEntidadeTerminalOld = $rsTerminais->getCampo( "cod_entidade" ).'-'.$rsTerminais->getCampo( "cod_terminal");
                    $inCount++;
                }

                $rsTerminais->proximo();
            }
        }
        $stJs  = "d.getElementById('spnDataMovimentacao').innerHTML='';";
        $stJs .= "limpaSelect(f.inCodTerminal,0);";
        $stJs .= "f.inCodTerminal.options[0] = new Option( 'Selecione', '', 'selected' );\n";
        $stJs .= "f.Ok.disabled = true;\n".$stJs2;
        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>
