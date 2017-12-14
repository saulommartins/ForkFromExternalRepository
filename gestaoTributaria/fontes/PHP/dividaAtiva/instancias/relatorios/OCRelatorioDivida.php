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

    * Página de Frame Oculto para Relatorio de Divida
    * Data de Criação   : 18/04/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCRelatorioDivida.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.04.10
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );

function montaListaGrupoCredito(&$rsLista, $stName = 'Créditos')
{
    $rsLista->setPrimeiroElemento();
    if ( !$rsLista->eof() ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );

        $obLista->setTitulo ("Lista de ".$stName);

        $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Código");
            $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Descrição" );
            $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stCodGrupo" );
        $obLista->commitDado();

        $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stGrupoDescricao" );
        $obLista->commitDado();

        $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluiGrupoCredito('ExcluirGrupoCredito');" );
            $obLista->ultimaAcao->addCampo( "1", "stCodGrupo" );
            
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp;";
    }

    $stJs = "d.getElementById('spnListaGrupos').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaGrupoCredito(){
    include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );

    $obIPopUpGrupoCredito = new MontaGrupoCredito;
    $obIPopUpGrupoCredito->setRotulo ( "Grupo de Crédito" );
    $obIPopUpGrupoCredito->setTitulo ( "Informe o código do grupo de crédito." );
    
    $obBtnIncluirGrupoCredito = new Button;
    $obBtnIncluirGrupoCredito->setName              ( "btnIncluirGrupoCredito" );
    $obBtnIncluirGrupoCredito->setValue             ( "Incluir" );
    $obBtnIncluirGrupoCredito->setTipo              ( "button" );
    $obBtnIncluirGrupoCredito->obEvento->setOnClick ( "montaParametrosGET('IncluirGrupoCredito', 'inCodGrupo', true);" );
    $obBtnIncluirGrupoCredito->setDisabled          ( false );
    
    $obBtnLimparGrupoCredito = new Button;
    $obBtnLimparGrupoCredito->setName               ( "btnLimparGrupoCredito" );
    $obBtnLimparGrupoCredito->setValue              ( "Limpar" );
    $obBtnLimparGrupoCredito->setTipo               ( "button" );
    $obBtnLimparGrupoCredito->obEvento->setOnClick  ( "montaParametrosGET('limpaGrupoCredito');" );
    $obBtnLimparGrupoCredito->setDisabled           ( false );
    
    $botoesGrupoCredito = array ( $obBtnIncluirGrupoCredito, $obBtnLimparGrupoCredito );
    
    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ("Grupos de Crédito");
    $obIPopUpGrupoCredito->geraFormulario ( $obFormulario, true, true );
    $obFormulario->defineBarra ( $botoesGrupoCredito, 'left', '' );
    $obFormulario->montaInnerHTML();
    
    Sessao::remove( "arListaGrupoCredito" );
    Sessao::remove( "arListaCredito" );
    
    $stJs  = "d.getElementById('spnGrupoCredito').innerHTML = '".$obFormulario->getHTML()."';";
    $stJs .= "d.getElementById('spnListaGrupos').innerHTML = '';";
    return $stJs;
}

function montaCredito(){
    // instancia objeto
    $obRMONCredito = new RMONCredito;
    // pegar mascara de credito
    $obRMONCredito->consultarMascaraCredito();
    $stMascaraCredito = $obRMONCredito->getMascaraCredito();
    $obRARRConfiguracao = new RARRConfiguracao;
    $obRARRConfiguracao->setAnoExercicio ( Sessao::getExercicio() );
    $obRARRConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

    $obBscCredito = new BuscaInner;
    $obBscCredito->setRotulo    ( "Crédito"         );
    $obBscCredito->setTitle     ( "Crédito que será calculado."   );
    $obBscCredito->setId        ( "stCredito"       );
    $obBscCredito->setNull      ( true              );
    $obBscCredito->obCampoCod->setName      ("inCodCredito"             );
    $obBscCredito->obCampoCod->setId        ("inCodCredito"             );
    $obBscCredito->obCampoCod->setValue     ( $_REQUEST["inCodCredito"] );
    $obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
    $obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
    $obBscCredito->obCampoCod->setMascara   ($stMascaraCredito          );
    $obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('BuscaDoCredito');");
    $obBscCredito->obCampoCod->obEvento->setOnBlur("validarCredito(this);");
    $obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCredito','stCredito','todos','".Sessao::getId()."','800','550');" );
    
    $obBtnIncluirCredito = new Button;
    $obBtnIncluirCredito->setName              ( "btnIncluirCredito" );
    $obBtnIncluirCredito->setValue             ( "Incluir" );
    $obBtnIncluirCredito->setTipo              ( "button" );
    $obBtnIncluirCredito->obEvento->setOnClick ( "montaParametrosGET('IncluirCredito', 'inCodCredito', true);" );
    $obBtnIncluirCredito->setDisabled          ( false );
    
    $obBtnLimparCredito = new Button;
    $obBtnLimparCredito->setName               ( "btnLimparCredito" );
    $obBtnLimparCredito->setValue              ( "Limpar" );
    $obBtnLimparCredito->setTipo               ( "button" );
    $obBtnLimparCredito->obEvento->setOnClick  ( "montaParametrosGET('limpaCredito');" );
    $obBtnLimparCredito->setDisabled           ( false );
    
    $botoesCredito = array ( $obBtnIncluirCredito, $obBtnLimparCredito );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ("Crédito");
    $obFormulario->addComponente($obBscCredito);
    $obFormulario->defineBarra ( $botoesCredito, 'left', '' );
    //  $obFormulario->setFormFocus($obBscCredito->obCampoCod->getId());

    $obFormulario->montaInnerHTML();
    
    Sessao::remove( "arListaGrupoCredito" );
    Sessao::remove( "arListaCredito" );
    
    $stJs  = "d.getElementById('spnGrupoCredito').innerHTML = '".$obFormulario->getHTML()."';";
    $stJs .= "d.getElementById('spnListaGrupos').innerHTML = '';";
    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "limpaGrupoCredito":
        $stJs = "f.inCodGrupo.value = '';";
        $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
        echo $stJs;
    break;

    case "limpaGeral":
        Sessao::write( "arListaGrupoCredito", array() );
    break;

    case "ExcluirGrupoCredito":
        if ($_REQUEST["inIndice1"]) {
            if ($_REQUEST["stRdbGrupo"] == 'credito') {
                $arListaGrupoCreditoSessao = Sessao::read( "arListaCredito" );
            }else{
               $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
            }
            $arListaGrupoCreditoTMP = array();
            $inTotalDados = count( $arListaGrupoCreditoSessao );

            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] != $_GET["inIndice1"]) {
                    $arListaGrupoCreditoTMP[] = $arListaGrupoCreditoSessao[$inX];
                }
            }
             if ($_REQUEST["stRdbGrupo"] == 'credito') {
                 Sessao::write( "arListaCredito", $arListaGrupoCreditoTMP );
             }else{
               Sessao::write( "arListaGrupoCredito", $arListaGrupoCreditoTMP );
             }
            $rsListaGrupoCredito = new RecordSet;
            $rsListaGrupoCredito->preenche( $arListaGrupoCreditoTMP );

            $stJs = montaListaGrupoCredito( $rsListaGrupoCredito, 'Grupos de Créditos' );
            sistemaLegado::executaFrameOculto( $stJs );
        }
    break;

    case "IncluirGrupoCredito":
        if ($_GET["inCodGrupo"]) {
            $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
            $boIncluir = true;
            $inTotalDados = count( $arListaGrupoCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] == $_GET["inCodGrupo"]) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arDados = explode( "/", $_GET["inCodGrupo"] );
                $obRARRGrupo = new RARRGrupo;
                $obRARRGrupo->setCodGrupo( $arDados[0] );
                $obRARRGrupo->setExercicio( $arDados[1] );
                $obRARRGrupo->consultarGrupo();
                $arListaGrupoCreditoSessao[$inTotalDados]["stCodGrupo"] = $_GET["inCodGrupo"];
                $arListaGrupoCreditoSessao[$inTotalDados]["stGrupoDescricao"] = $obRARRGrupo->getDescricao();

                Sessao::write( "arListaGrupoCredito", $arListaGrupoCreditoSessao );

                $rsListaGrupoCredito = new RecordSet;
                $rsListaGrupoCredito->preenche( $arListaGrupoCreditoSessao );

                $stJs = montaListaGrupoCredito( $rsListaGrupoCredito, 'Grupos de Créditos' );
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Grupo de crédito já está na lista. (".$_GET["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            }

            echo $stJs;
        }
    break;

    case "buscaLogradouro":
        $obRCIMTrecho  = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
            }
        }
        echo $stJs;
    break;

    case 'montaCriterio':

        if ($_REQUEST['inCodSituacao'] == 6) {

            $obRdCriterioPaga = new Radio;
            $obRdCriterioPaga->setRotulo('Com Cobrança');
            $obRdCriterioPaga->setName("stCriterio");
            $obRdCriterioPaga->setLabel('Pago');
            $obRdCriterioPaga->setValue('pago');

            $obRdCriterioCancelada = new Radio;
            $obRdCriterioCancelada->setName("stCriterio");
            $obRdCriterioCancelada->setLabel('Cancelada');
            $obRdCriterioCancelada->setValue('cancelada');

            $obRdCriterioVencida = new Radio;
            $obRdCriterioVencida->setName("stCriterio");
            $obRdCriterioVencida->setLabel('Vencida');
            $obRdCriterioVencida->setValue('vencida');

            $obFormulario = new Formulario;
            $obFormulario->agrupaComponentes (
                array(
                    $obRdCriterioPaga,
                    $obRdCriterioCancelada,
                    $obRdCriterioVencida,
                )
            );
            $obFormulario->montaInnerHTML();

            echo "document.getElementById('spnCriterio').innerHTML = '".$obFormulario->getHTML()."';";

        } elseif ($_REQUEST['inCodSituacao'] == 2) {
            $obRdCriterioAberta = new Radio;
            $obRdCriterioAberta->setRotulo('Sem Cobrança');
            $obRdCriterioAberta->setName("stCriterio");
            $obRdCriterioAberta->setLabel('Aberta');
            $obRdCriterioAberta->setValue('aberta');

            $obRdCriterioCancelada = new Radio;
            $obRdCriterioCancelada->setName("stCriterio");
            $obRdCriterioCancelada->setLabel('Cancelada');
            $obRdCriterioCancelada->setValue('cancelada');
            
            $obRdCriterioEstornada = new Radio;
            $obRdCriterioEstornada->setName("stCriterio");
            $obRdCriterioEstornada->setLabel('Estornada');
            $obRdCriterioEstornada->setValue('estornada');

            $obFormulario = new Formulario;
            $obFormulario->agrupaComponentes (
                array(
                    $obRdCriterioAberta,
                    $obRdCriterioCancelada,
                    $obRdCriterioEstornada
                )
            );
            $obFormulario->montaInnerHTML();

            echo "document.getElementById('spnCriterio').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
          echo "document.getElementById('spnCriterio').innerHTML = '';";
        }
    break;
    
    case 'montaGrupoCredito':
        $sj = montaGrupoCredito();
        echo $sj;
    break;
    
    case 'montaCredito':
        $sj = montaCredito();
        echo $sj;
    break;
    
    case "IncluirCredito":
        if ($_GET["inCodCredito"]) {
            $arListaCreditoSessao = Sessao::read( "arListaCredito" );
            $boIncluir = true;
            $inTotalDados = count( $arListaCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaCreditoSessao[$inX]["stCodGrupo"] == $_GET["inCodCredito"]) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arDados = explode( ".", $_GET["inCodCredito"] );
                $obRMONCredito = new RMONCredito;
                $obRMONCredito->setCodCredito ( $arDados[0]  );
                $obRMONCredito->setCodEspecie ( $arDados[1]  );
                $obRMONCredito->setCodGenero  ( $arDados[2]   );
                $obRMONCredito->setCodNatureza( $arDados[3] );
                $obRMONCredito->consultarCredito();

                $arListaCreditoSessao[$inTotalDados]["stCodGrupo"] = $_GET["inCodCredito"];
                $arListaCreditoSessao[$inTotalDados]["stGrupoDescricao"] = $obRMONCredito->getDescricao();

                Sessao::write( "arListaCredito", $arListaCreditoSessao );

                $rsListaCredito = new RecordSet;
                $rsListaCredito->preenche( $arListaCreditoSessao );

                $stJs = montaListaGrupoCredito( $rsListaCredito, 'Créditos' );
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Crédito já está na lista. (".$_GET["inCodCredito"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            }
            echo $stJs;
        }
    break;
    
    case "limpaCredito":
        $stJs = "f.inCodCredito.value = '';";
        $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
        echo $stJs;
    break;

    

}

?>
