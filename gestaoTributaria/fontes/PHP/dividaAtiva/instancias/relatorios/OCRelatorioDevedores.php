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

    * $Id: OCRelatorioDevedores.php 60480 2014-10-23 18:32:04Z carolina $

    * Casos de uso: uc-05.04.10
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

function montaListaCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();
    if ( !$rsLista->eof() ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );

        $obLista->setTitulo ("Lista de Créditos");

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Exercício");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stCodCredito" );
        $obLista->commitDado();
        
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stExercicio" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stCreditoDescricao" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->addCampo( "1", "stCodCredito" );
        $obLista->ultimaAcao->setLink( "javascript:excluirCredito();" );
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp;";
    }

    $stJs = "d.getElementById('spnListaCreditos').innerHTML = '".$stHTML."';";

    return $stJs;
}


function montaListaGrupoCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();
    if ( !$rsLista->eof() ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );

        $obLista->setTitulo ("Lista de Grupos de Créditos");

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
        $obLista->ultimaAcao->addCampo( "1", "stCodGrupo" );
        $obLista->ultimaAcao->setLink( "javascript:excluirGrupoCredito();" );
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

switch ($_REQUEST["stCtrl"]) {
    case "limpaCredito":
        $stJs  = "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('inCodCredito').value = '';\n";
        $stJs .= "d.getElementById('stExercicio').value = '';\n";
        echo $stJs;
    break;

    case "limpaGrupoCredito":
        $stJs = "f.inCodGrupo.value = '';\n";
        $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('inCodGrupo').value = '';\n";
        echo $stJs;
    break;

    case "limpaGeral":
        Sessao::write( "arListaGrupoCredito", array());
        Sessao::write( "arListaCredito", array());
    break;

    case "excluirGrupoCredito":
        if ($_REQUEST["inIndice1"]) {
            $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
            $arListaGrupoCreditoTMP = array();
            $inTotalDados = count( $arListaGrupoCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] != $_GET["inIndice1"]) {
                    $arListaGrupoCreditoTMP[] = $arListaGrupoCreditoSessao[$inX];
                }
            }

            Sessao::write( "arListaGrupoCredito", $arListaGrupoCreditoTMP );

            $rsListaGrupoCredito = new RecordSet;
            $rsListaGrupoCredito->preenche( $arListaGrupoCreditoTMP );

            $stJs = montaListaGrupoCredito( $rsListaGrupoCredito );
            sistemaLegado::executaFrameOculto( $stJs );
        }
    break;

    case "incluirGrupoCredito":
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

                $stJs = montaListaGrupoCredito( $rsListaGrupoCredito );
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

    case "buscaCredito":
        $inCodCreditoComposto  = explode('.',$_REQUEST["inCodCredito"]);
        
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->obRMONCredito->setCodCredito  ($inCodCreditoComposto[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($inCodCreditoComposto[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($inCodCreditoComposto[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($inCodCreditoComposto[3]);
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao  = $obRARRGrupo->obRMONCredito->getDescricao();

        if ($stDescricao != '') {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";

        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado não existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }
        
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "incluirCredito":
        if ($_REQUEST["inCodCredito"]) {
            $arListaCreditoSessao = Sessao::read( "arListaCredito" );
            $boIncluir = true;
            
            $inTotalDados = count( $arListaCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if (($arListaCreditoSessao[$inX]["stCodCredito"] == $_REQUEST["inCodCredito"]) && ($arListaCreditoSessao[$inX]["stExercicio"] == $_REQUEST["stExercicio"])) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                
                $stExercicio = $_REQUEST["stExercicio"];

                $inCodCreditoComposto  = explode('.',$_REQUEST["inCodCredito"]);
                
                if ( $stExercicio == '' ) {
                    echo "alertaAviso('O campo Exercício não foi preenchido!','form','erro','".Sessao::getId()."');";
                    break;
                }

                $obRARRGrupo = new RARRGrupo;
                $obRARRGrupo->obRMONCredito->setCodCredito  ($inCodCreditoComposto[0]);
                $obRARRGrupo->obRMONCredito->setCodEspecie  ($inCodCreditoComposto[1]);
                $obRARRGrupo->obRMONCredito->setCodGenero   ($inCodCreditoComposto[2]);
                $obRARRGrupo->obRMONCredito->setCodNatureza ($inCodCreditoComposto[3]);
                $obRARRGrupo->obRMONCredito->consultarCredito();
        
                $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
                $stDescricao  = $obRARRGrupo->obRMONCredito->getDescricao();
                $obRARRGrupo->consultarGrupo();
                $arListaCreditoSessao[$inTotalDados]["stCodCredito"]       = $_REQUEST["inCodCredito"];
                $arListaCreditoSessao[$inTotalDados]["stCreditoDescricao"] = $obRARRGrupo->obRMONCredito->getDescricao()." / ".$_REQUEST["stExercicio"];
                $arListaCreditoSessao[$inTotalDados]["stExercicio"]        = $stExercicio;

                Sessao::write( "arListaCredito", $arListaCreditoSessao );

                $rsListaCredito = new RecordSet;
                $rsListaCredito->preenche( $arListaCreditoSessao );

                $stJs  = montaListaCredito( $rsListaCredito );
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "f.stExercicio.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            } else {
                $stJs  = "alertaAviso('@Crédito já está na lista. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            }
            echo $stJs;
        }
    break;

    case "excluirCredito":
        if ($_REQUEST["inIndice1"]) {
            $arListaCreditoSessao = Sessao::read( "arListaCredito" );
            $arListaCreditoTMP = array();
            $inTotalDados = count( $arListaCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaCreditoSessao[$inX]["stCodCredito"] != $_GET["inIndice1"]) {
                    $arListaCreditoTMP[] = $arListaCreditoSessao[$inX];
                }
            }

            Sessao::write( "arListaCredito", $arListaCreditoTMP );

            $rsListaCredito = new RecordSet;
            $rsListaCredito->preenche( $arListaCreditoTMP );

            $stJs = montaListaCredito( $rsListaCredito );
            sistemaLegado::executaFrameOculto( $stJs );
        }
    break;
}

?>