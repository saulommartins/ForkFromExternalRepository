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
    * Página de Frame Oculto de Definição de Desoneração
    * Data de Criação   : 30/05/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterDesoneracao.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.16  2006/11/21 15:50:05  cercato
bug #6853#

Revision 1.15  2006/10/02 09:11:43  domluc
#6973#

Revision 1.14  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.13  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRDesoneracao = new RARRDesoneracao;
$rsCreditos = new RecordSet;
switch ($_REQUEST['stCtrl']) {
    case "verificaProrrogacao":
        if ($_REQUEST["dtExpiracao"] != "") {
            if ($_REQUEST["dtProDes"] != "") {
                $arDataExp = explode("/", $_REQUEST["dtExpiracao"]);
                $arDataDes = explode("/", $_REQUEST["dtProDes"]);
                if ($arDataDes[2].$arDataDes[1].$arDataDes[0] <= $arDataExp[2].$arDataExp[1].$arDataExp[0]) {
                    $stJs = "f.dtProDes.value ='';\n";
                    $stJs .= "f.dtProDes.focus();\n";
                    $stJs .= "alertaAviso('Data da prorrogação ".$_REQUEST["dtProDes"]." é inferior a data de expiração ".$_REQUEST["dtExpiracao"]."!','form','erro','".Sessao::getId()."', '../');";

                    SistemaLegado::executaFrameOculto($stJs);
                }
            }
        }
        break;

    case "buscaDados":
        include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
        $obRFuncao = new RFuncao;

        if ($_REQUEST['inCodigoFormula'] != "") {
            $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);
            $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
            $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
            $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            $obRFuncao->consultar();

            $inCodFuncao = $obRFuncao->getCodFuncao () ;
            $stDescricao = "&nbsp;";
            $stDescricao = $obRFuncao->getComentario() ;
            $stNomeFuncao = $obRFuncao->getNomeFuncao();
       }

        if ( !empty($inCodFuncao) ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodigoFormula.value ='';\n";
            $stJs .= "f.inCodigoFormula.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
        }

        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs .= "d.getElementById('stFundamentacao').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida.";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacao"].")','form','erro','".Sessao::getId()."', '../');";
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaInscricaoImobiliaria":
        $obRCIMLote   = new RCIMLote;
        $obRCIMImovel = new RCIMImovel( $obRCIMLote );
        if ($_REQUEST["inCodImobiliaria"]) {
            $obRCIMImovel->setNumeroInscricao( $_REQUEST["inCodImobiliaria"] );
            $obRCIMImovel->listarImoveis($rsLista, $boTransacao );
            if ($rsLista->eof()) {
                $stJs = 'f.inCodImobiliaria.value = "";';
                $stJs .= 'f.inCodImobiliaria.focus();';
                $stJs .= "d.getElementById('stNomeImobiliaria').innerHTML = '&nbsp;'\n";
                $stJs .= "alertaAviso('Código de inscrição inválido (".$_REQUEST["inCodImobiliaria"].")','form','erro','".Sessao::getId()."', '../');";
            } else {
                $stEndereco = $rsLista->getCampo("logradouro");
                if ( $rsLista->getCampo("numero") )
                    $stEndereco = $stEndereco.", ".$rsLista->getCampo("numero");

                if ( $rsLista->getCampo("complemento") )
                    $stEndereco = $stEndereco." - ".$rsLista->getCampo("complemento");

                $stJs = "d.getElementById('stNomeImobiliaria').innerHTML = '".$stEndereco."'\n";
            }
        } else {
            $stJs = 'f.inCodImobiliaria.value = "";';
            $stJs .= "d.getElementById('stNomeImobiliaria').innerHTML = '&nbsp;'\n";
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaInscricaoEconomica":
        $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
        if ($_REQUEST["inCodEconomica"]) {
            $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inCodEconomica"] );
            $obRCEMInscricaoEconomica->listarInscricao( $rsLista );

            if ($rsLista->eof()) {
                $stJs = 'f.inCodEconomica.value = "";';
                $stJs .= 'f.inCodEconomica.focus();';
                $stJs .= "d.getElementById('stNomeEconomica').innerHTML = '&nbsp;'\n";
                $stJs .= "alertaAviso('Código de inscrição inválido (".$_REQUEST["inCodEconomica"].")','form','erro','".Sessao::getId()."', '../');";
            } else {
                $stJs = "d.getElementById('stNomeEconomica').innerHTML = '".$rsLista->getCampo("nom_cgm")."'\n";
            }
        } else {
            $stJs = 'f.inCodEconomica.value = "";';
            $stJs .= "d.getElementById('stNomeEconomica').innerHTML = '&nbsp;'\n";
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "TipoInscricao":
        $obFormulario = new Formulario;

        if (!$_REQUEST['inNumCGM']) {
            $stJs = "alertaAviso('O campo CGM deve estar preenchido.','form','erro','".Sessao::getId()."', '../');";
            $stJs .= 'f.boTipoInscricao[0].checked = false;';
            $stJs .= 'f.boTipoInscricao[1].checked = false;';
        } else {
            if ($_REQUEST["boTipoInscricao"] == "II") {

                include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                 );
                include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                 );
                $obRCIMImovel = new RCIMImovel( new RCIMLote );
                $obRCIMImovel->addProprietario();
                $obRCIMImovel->roUltimoProprietario->setNumeroCGM( $_REQUEST['inNumCGM'] );
                $obRCIMImovel->listarImoveisProprietario( $rsImoveis, $boTransacao );

                $obCmbII = new Select;
                $obCmbII->setName       ( "inCodImobiliaria"       );
                $obCmbII->setTitle      ( ""                       );
                $obCmbII->setRotulo     ( "Inscrição Imobiliária"  );
                $obCmbII->addOption     ( "", "Selecione"      );
                $obCmbII->setStyle      ( "width: 150px"           );
                $obCmbII->setCampoID    ( "[inscricao_municipal]"     );
                $obCmbII->setCampoDesc  ( "inscricao_municipal"                  );
                $obCmbII->preencheCombo ( $rsImoveis  );

                $obFormulario->addComponente ( $obCmbII );
            } else {

                include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );

                $obRCEMInscricaoEconomica    =    new RCEMInscricaoEconomica();
                $obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST['inNumCGM']  );
                $obRCEMInscricaoEconomica->listarInscricao( $rsInscricaoEconomica, $boTransacao );

                $obCmbIE = new Select;
                $obCmbIE->setName       ( "inCodEconomica"       );
                $obCmbIE->setTitle      ( ""                       );
                $obCmbIE->setRotulo     ( "Inscrição Econômica"  );
                $obCmbIE->addOption     ( "", "Selecione"      );
                $obCmbIE->setStyle      ( "width: 150px"           );
                $obCmbIE->setCampoID    ( "[inscricao_economica]"     );
                $obCmbIE->setCampoDesc  ( "inscricao_economica"                  );
                $obCmbIE->preencheCombo ( $rsInscricaoEconomica  );

                $obFormulario->addComponente ( $obCmbIE );
            }

            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnTipoInscricao').innerHTML = '". $obFormulario->getHTML(). "';\n";
        }

        @SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaCredito":
        $arCodigoCredito = explode('.',$_REQUEST['inCodigoCredito']);
        $obRARRDesoneracao->obRMONCredito->setCodCredito  ( $arCodigoCredito[0] );
        $obRARRDesoneracao->obRMONCredito->setCodEspecie  ( $arCodigoCredito[1] );
        $obRARRDesoneracao->obRMONCredito->setCodGenero   ( $arCodigoCredito[2] );
        $obRARRDesoneracao->obRMONCredito->setCodNatureza ( $arCodigoCredito[3] );

        $obRARRDesoneracao->obRMONCredito->listarCreditos( $rsCreditos );

        if ( !$rsCreditos->eof()  && $_REQUEST['stAcao'] == "incluir" && ($arCodigoCredito[3] != "" ) ) {
            $stJs = "d.getElementById('stCredito').innerHTML = '".$rsCreditos->getCampo( "descricao_credito" )."';\n";
        } else {
            $stMsg = "Crédito inválido.";
            $stJs .= 'f.inCodigoCredito.value = "";';
            $stJs .= 'f.inCodigoCredito.focus();';
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'\n";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoCredito"].")','form','erro','".Sessao::getId()."', '../');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaDesoneracaoProrrogar":
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obErro = $obRARRDesoneracao->consultarDesoneracao();
        if ( $obRARRDesoneracao->getTipo() && !$obErro->ocorreu() ) {
            $stJs = "d.getElementById('stDesoneracao').innerHTML = '".$obRARRDesoneracao->getTipo()."';\n";
            $stJs .= "d.getElementById('inNumCGM').innerHTML = '".$obRARRDesoneracao->obRCGM->getNumCGM()."';\n";
            $stJs .= "d.getElementById('stNomCGM').innerHTML = '".$obRARRDesoneracao->obRCGM->getNomCGM()."';\n";
        } else {
            $stMsg = "Código inválido.";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoDesoneracao"].")','form','erro','".Sessao::getId()."', '../');";
            $stJs .= "d.getElementById('stDesoneracao').innerHTML = '&nbsp;';";
            $stJs .= 'f.inCodigoDesoneracao.value = "";';
            $stJs .= 'f.inCodigoDesoneracao.focus();';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaDesoneracao":
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obErro = $obRARRDesoneracao->consultarDesoneracao();
        if ( $obRARRDesoneracao->getTipo() && !$obErro->ocorreu() ) {
            if ( !$obRARRDesoneracao->getExpiracao() ) {
                $stJs = "d.getElementById('stDesoneracao').innerHTML = '".$obRARRDesoneracao->getTipo()."';\n";
            }else
            if ( SistemaLegado::comparaDatas($obRARRDesoneracao->getExpiracao(),date('d/m/Y')) ) {
                $stJs = "d.getElementById('stDesoneracao').innerHTML = '".$obRARRDesoneracao->getTipo()."';\n";
            } else {
                $stMsg = "Erro! Data de expiração é maior que a data atual.";
                $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoDesoneracao"].")','form','erro','".Sessao::getId()."', '../');";
            }
        } else {
            $stMsg = "Código inválido.";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoDesoneracao"].")','form','erro','".Sessao::getId()."', '../');";
            $stJs .= "d.getElementById('stDesoneracao').innerHTML = '&nbsp;';";
            $stJs .= 'f.inCodigoDesoneracao.value = "";';
            $stJs .= 'f.inCodigoDesoneracao.focus();';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaLegal":
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacao').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacao"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacao').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacao.value = "";';
            $stJs .= 'f.inCodigoFundamentacao.focus();';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCGM":
        $stText = "inNumCGM";
        $stSpan = "stNomCGM";
        if ($_REQUEST[ $stText ] != "") {

            $obRARRDesoneracao->obRCGM->setNumCGM( $_REQUEST[ $stText ] );
            $obRARRDesoneracao->obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";

            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.'.$stText.'.value = "";';
                $stJs .= 'f.'.$stText.'.focus();';
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
            } else {
               $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        } else {
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaFuncao":

        include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
        $obRFuncao = new RFuncao;

        if ($_REQUEST['inCodigoFormula'] != "") {
            $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);
            if ( ($arCodFuncao[0] != 25) OR ($arCodFuncao[1] != 02)) {
               $stJs .= "f.inCodigoFormula.value ='';\n";
               $stJs .= "f.inCodigoFormula.focus();\n";
               $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
               $stJs .= "alertaAviso('@Função inválida. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
               SistemaLegado::executaFrameOculto($stJs);
               break;
            }
            $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
            $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
            $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            $obRFuncao->consultar();

            $inCodFuncao = $obRFuncao->getCodFuncao () ;
            $stDescricao = "&nbsp;";
            $stDescricao = $obRFuncao->getComentario() ;
            $stNomeFuncao = $obRFuncao->getNomeFuncao();
       }

        if ($stDescricao || $stNomeFuncao) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodigoFormula.value ='';\n";
            $stJs .= "f.inCodigoFormula.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaFuncaoDefinirDesoneracao":

        include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
        $obRFuncao = new RFuncao;

        if ($_REQUEST['inCodigoFormula'] != "") {
            $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);

            if ( ($arCodFuncao[0] != 25) OR ($arCodFuncao[1] != 03)) {
               $stJs .= "f.inCodigoFormula.value ='';\n";
               $stJs .= "f.inCodigoFormula.focus();\n";
               $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
               $stJs .= "alertaAviso('@Função inválida. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
               SistemaLegado::executaFrameOculto($stJs);
               break;
            }
            $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
            $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
            $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            $obRFuncao->consultar();

            $inCodFuncao = $obRFuncao->getCodFuncao () ;
            $stDescricao = "&nbsp;";
            $stDescricao = $obRFuncao->getComentario() ;
            $stNomeFuncao = $obRFuncao->getNomeFuncao();
       }

        if ($stDescricao || $stNomeFuncao) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodigoFormula.value ='';\n";
            $stJs .= "f.inCodigoFormula.focus();\n";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "limpaTudo":
        $stJs = "d.getElementById('stCredito').innerHTML = '&nbsp;'\n";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "limparConcessaoGrupo":

        $stJs .= "f.inCodigoDesoneracao.value = ''\n";
        $stJs .= "d.getElementById('stDesoneracao').innerHTML = '&nbsp;'\n";
        $stJs .= "f.inCodigoFormula.value = '';\n";
        $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;'\n";
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "montaAtributos":
        /* Atributos Dinamicos */

        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
        $obErro = $obRARRDesoneracao->consultarDesoneracao();
        if ( $obRARRDesoneracao->getTipo() && !$obErro->ocorreu() ) {
            if ( !$obRARRDesoneracao->getExpiracao() ) {
                $stJs = "d.getElementById('stDesoneracao').innerHTML = '".$obRARRDesoneracao->getTipo()."';\n";
                $boOk = TRUE;
            }else
            if ( SistemaLegado::comparaDatas($obRARRDesoneracao->getExpiracao(),date('d/m/Y')) ) {
                $stJs = "d.getElementById('stDesoneracao').innerHTML = '".$obRARRDesoneracao->getTipo()."';\n";
                $boOk = TRUE;
            } else {
                $stMsg = "Erro! Data de expiração é maior que a data atual.";
                $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoDesoneracao"].")','form','erro','".Sessao::getId()."', '../');";
            }
            if ($boOk) {
                $obRARRDesoneracao->obRCadastroDinamico->setChavePersistenteValores  ( array ( "cod_desoneracao" => $_REQUEST["inCodigoDesoneracao"] ) );
                $obRARRDesoneracao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDesoneracao );

                $obFormulario = new Formulario;

                $obMontaAtributosDesoneracao = new MontaAtributos;
                $obMontaAtributosDesoneracao->setTitulo     ( "Atributos"              );
                $obMontaAtributosDesoneracao->setName       ( "AtributoDesoneracao_"    );
                $obMontaAtributosDesoneracao->setRecordSet  ( $rsAtributosDesoneracao );
                $obMontaAtributosDesoneracao->geraFormulario( $obFormulario );
                $obFormulario->montaInnerHTML();
                $stHtml = $obFormulario->getHTML();

                if ( $stHtml )
                    $stJs .= "d.getElementById('spnAtributos').innerHTML = '".$stHtml."'\n";
            }
        } else {
            $stMsg = "Código inválido.";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoDesoneracao"].")','form','erro','".Sessao::getId()."', '../');";
            $stJs .= "d.getElementById('stDesoneracao').innerHTML = '&nbsp;';";
            $stJs .= 'f.inCodigoDesoneracao.value = "";';
            $stJs .= 'f.inCodigoDesoneracao.focus();';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

}
