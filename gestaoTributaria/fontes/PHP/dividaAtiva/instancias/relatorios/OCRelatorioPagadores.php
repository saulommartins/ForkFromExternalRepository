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
/**
  * Página de frame oculto para o Relatorio Pagadores
  * Data de criação : 10/11/2015
  * @author Analista: Luciana Dellay
  * @author Programador: Evandro Melos
  * $Id:$
**/

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

    $stJs = "jq('#spnListaCreditos').html('".$stHTML."'); ";

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

    $stJs = "jq('#spnListaGrupos').html('".$stHTML."'); \n";

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case "limpaCredito":
        $stJs  = "jq('#stCredito').html('&nbsp;'); \n";
        $stJs .= "jq('#inCodCredito').val(''); \n";
        $stJs .= "jq('#stExercicio').val(''); \n";
        echo $stJs;
    break;

    case "limpaGrupoCredito":
        $stJs  = "jq('#inCodGrupo').val(''); \n";
        $stJs .= "jq('#stGrupo').html('&nbsp;'); \n";
        $stJs .= "jq('#inCodGrupo').val(''); \n";
        echo $stJs;
    break;

    case "limpaGeral":
        Sessao::write( "arListaGrupoCredito", array());
        Sessao::write( "arListaCredito", array());
    break;

    case "excluirGrupoCredito":
        if ($request->get("inIndice1")) {
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
                $stJs .= "jq('#inCodGrupo').val('');";
                $stJs .= "jq('#stGrupo').html('&nbsp;'); ";
            } else {
                $stJs = "alertaAviso('@Grupo de crédito já está na lista. (".$request->get('inCodGrupo').")','form','erro','".Sessao::getId()."');";
                $stJs .= "jq('#inCodGrupo').val('');";
                $stJs .= "jq('#stGrupo').html('&nbsp;'); ";
            }
            echo $stJs;
        }
    break;

    case "buscaCredito":
        $inCodCreditoComposto  = explode('.',$request->get("inCodCredito"));
        
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->obRMONCredito->setCodCredito  ($inCodCreditoComposto[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($inCodCreditoComposto[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($inCodCreditoComposto[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($inCodCreditoComposto[3]);
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao  = $obRARRGrupo->obRMONCredito->getDescricao();

        if ($stDescricao != '') {
            $stJs .= "jq_('#stCredito').html('".$stDescricao."'); \n";

        } else {
            $stJs .= "jq_('#inCodCredito').val('');";
            $stJs .= "jq_('#inCodCredito').focus();";
            $stJs .= "jq_('#stCredito').html('&nbsp;''); \n";
            $stJs .= "alertaAviso('@Crédito informado não existe. (".$request->get("inCodCredito").")','form','erro','".Sessao::getId()."');";
        }
        
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "incluirCredito":
        if ($request->get("inCodCredito")) {
            $arListaCreditoSessao = Sessao::read( "arListaCredito" );
            $boIncluir = true;
            
            $inTotalDados = count( $arListaCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if (($arListaCreditoSessao[$inX]["stCodCredito"] == $request->get("inCodCredito")) && ($arListaCreditoSessao[$inX]["stExercicio"] == $request->get("stExercicio"))) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                
                $stExercicio = $request->get("stExercicio");

                $inCodCreditoComposto  = explode('.',$request->get("inCodCredito"));
                
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
                $arListaCreditoSessao[$inTotalDados]["stCodCredito"]       = $request->get("inCodCredito");
                $arListaCreditoSessao[$inTotalDados]["stCreditoDescricao"] = $obRARRGrupo->obRMONCredito->getDescricao()." / ".$request->get("stExercicio");
                $arListaCreditoSessao[$inTotalDados]["stExercicio"]        = $stExercicio;

                Sessao::write( "arListaCredito", $arListaCreditoSessao );

                $rsListaCredito = new RecordSet;
                $rsListaCredito->preenche( $arListaCreditoSessao );

                $stJs  = montaListaCredito( $rsListaCredito );
                $stJs .= "jq('#inCodCredito').val('');";
                $stJs .= "jq('#stExercicio').val('');";
                $stJs .= "jq('#stCredito').html('&nbsp;');";
            } else {
                $stJs  = "alertaAviso('@Crédito já está na lista. (".$request->get("inCodCredito").")','form','erro','".Sessao::getId()."');";
                $stJs .= "jq('#inCodCredito').val('');";
                $stJs .= "jq('#stCredito').html('&nbsp;');";
            }
            echo $stJs;
        }
    break;

    case "excluirCredito":
        if ($request->get("inIndice1")) {
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