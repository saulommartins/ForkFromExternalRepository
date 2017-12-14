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
    * Página Oculto de Alteracão de Atividade para Inscrição Ecônomica
    * Data de Criação   : 30/12/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: OCAlterarAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.14  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AlterarAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obMontaAtividade = new MontaAtividade;

function montaListaAtividade($arListaAtividade)
{
     $rsListaAtividade = new Recordset;
     $rsListaAtividade->preenche( is_array($arListaAtividade) ? $arListaAtividade : array() );

     if ( !$rsListaAtividade->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaAtividade );
         $obLista->setTitulo ("Listas de Atividade Econômicas");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Código");
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 40 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Data de Início" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Data de Término" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Principal" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stChaveAtividade" );
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeAtividade" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "dtDataInicio" );
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "dtDataTermino" );
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stPrincipal" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inId" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirAtividade');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);

     } else {
         $stHTML = "&nbsp;";
     }
     global $pgOcul;
     global $pgProc;
     ;
     $stJs = "d.getElementById('lsListaAtividade').innerHTML = '".$stHTML."';\n";
     $stJs.= "f.stChaveAtividade.value = '';\n";
     //$stJs.= "f.dtDataInicio.value = '';\n";
     $stJs.= "f.dtDataTermino.value = '';\n";
     $stJs.= "f.inCodAtividade_1.selectedIndex = 0;";
     //$stJs.= "f.stCtrl.value = 'preencheCombosAtividade'\n";
     //$stJs.= "f.target = 'oculto'\n";
     //$stJs.= "f.action = '".$pgOcul."?".Sessao::getId()."'\n";
     //$stJs.= "f.submit()\n";
     //$stJs.= "f.action = '".$pgProc."?".Sessao::getId()."'\n";
     $stJs.= "f.stChaveAtividade.focus() ;\n";
     $stJs.= "f.dtDataInicio.value = '".date("d/m/Y")."';";

     return $stJs;
     //sistemaLegado::executaFrameOculto($stJs);
}

switch ($_REQUEST["stCtrl"]) {

    case "montaAtividade":

        $boInsereAtividade = true;
        if ( empty($_REQUEST['dtDataInicio']) ) {
            $stJs = "alertaAviso('@Valor inválido. (A data de início não pode ser nula.)','form','aviso','".Sessao::getId()."');";
        } else {

            $stMensagem = "";
            if ( sistemaLegado::comparaDatas( $_REQUEST['dtDataInicio'] , $_REQUEST['dtDataTermino'] ) && !empty($_REQUEST['dtDataTermino']) ) {
                $stMensagem = "A data de término deve ser posterior à data de início!";
                $boInsereAtividade = false;
            }

            if ($_REQUEST['stChaveAtividade'] == "") {
                $stMensagem = "Nenhuma atividade informada.";
                $stJs .= " f.inCodAtividade_1.selectedIndex = 0 ;\n";
                $boInsereAtividade = false;
            }

            // VERIFICA DE A DATA DE ABERTURA DA INSCRICAO NAO E SUPERIOR A DATA DE INICIO DA ATIVIDADE
            if (sistemaLegado::comparaDatas( $_REQUEST['stDtAbertura'], $_REQUEST['dtDataInicio'] ) ) {
                $stMensagem = " data de início deve ser maior que a data de abertura da inscrição";
                $boInsereAtividade = false;
            }

            if ($_REQUEST['stChaveAtividade'] == "") {
                $stMensagem = "nulo";
            }

            $arAtividadesSessao = Sessao::read( "Atividades" );
            foreach ($arAtividadesSessao as $campo => $valor) {
                if ($arAtividadesSessao[$campo]['stChaveAtividade'] == $_REQUEST['stChaveAtividade']) {
                    $stMensagem = "Atividade ".$_REQUEST['stChaveAtividade']." - já existe.";
                }
                if (( $arAtividadesSessao[$campo]['stPrincipal'] == $_REQUEST['stPrincipal'] ) && ( $_REQUEST['stPrincipal'] == "sim" )) {
                    $stMensagem = "Só pode ser cadastrada uma atividade principal.";
                }
            }

            if ($stMensagem == "" && $boInsereAtividade == true) {
                $stTemp = 'inCodAtividade_';
                $inTemp = $_REQUEST['inNumNiveis'] - 1;
                $stTemp .= $inTemp;
                $arTemp = explode('§',$_REQUEST[$stTemp] );

                if ($arTemp[1] == "") {
                    $stMensagem = "Atividade ".$_REQUEST['stChaveAtividade']." não existe.";
                    $stJs = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto($stJs);
                } else {
                    $obRCEMAtividade = new RCEMAtividade;
                    $rsRecordSet = new Recordset;
                    $rsAtividade = new Recordset;
                    $rsRecordSet->preenche( $arAtividadesSessao );
                    $rsRecordSet->setUltimoElemento();

                    $obRCEMAtividade->setCodigoAtividade( $arTemp[1] );
                    $obRCEMAtividade->setValorComposto( $_REQUEST['stChaveAtividade'] );
                    $obRCEMAtividade->listarAtividade( $rsAtividade );

                    $inUltimoId = $rsRecordSet->getCampo("inId");
                    if (!$inUltimoId) {
                        $inProxId = 1;
                    } else {
                        $inProxId = $inUltimoId + 1;
                    }

                    $arElementos['inId']                     = $inProxId;
                    $arElementos['stChaveAtividade']         = $_REQUEST['stChaveAtividade'];
                    $arElementos['stNomeAtividade']          = $rsAtividade->getCampo( 'nom_atividade' );
                    $arElementos['inCodigoAtividade']        = $rsAtividade->getCampo( 'cod_atividade' );
                    $arElementos['dtDataInicio']             = $_REQUEST['dtDataInicio'];
                    $arElementos['dtDataTermino']            = $_REQUEST['dtDataTermino'];
                    $arElementos['stPrincipal']              = $_REQUEST['stPrincipal'];
                    $arAtividadesSessao[]          = $arElementos;

                    Sessao::write( "Atividades", $arAtividadesSessao );
                    $stJs = montaListaAtividade( $arAtividadesSessao );
                }
            } else {
                $stJs = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
            }
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "preencheProxCombo":
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("§" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
    break;
    case "preencheCombosAtividade":
        $obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaAtividade->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
        $obMontaAtividade->setMascara              ( $_REQUEST['stMascara'] );
        $obMontaAtividade->preencheCombosAtividade();
    break;
    case "excluirAtividade":
        $id = $_REQUEST['inId'];
        $stMensagem = false;

        if ($stMensagem==false) {
            $arAtividadesSessao = Sessao::read( "Atividades" );
            reset( $arAtividadesSessao );
            while ( list( $arId ) = each( $arAtividadesSessao ) ) {
                if ($arAtividadesSessao[$arId]["inId"] != $id) {
                    $arElementos['inId']              = $arAtividadesSessao[$arId]["inId"];
                    $arElementos['inCodigoAtividade'] = $arAtividadesSessao[$arId]["inCodigoAtividade"];
                    $arElementos['stChaveAtividade']  = $arAtividadesSessao[$arId]["stChaveAtividade"];
                    $arElementos['stNomeAtividade']   = $arAtividadesSessao[$arId]["stNomeAtividade"];
                    $arElementos['dtDataInicio']      = $arAtividadesSessao[$arId]["dtDataInicio"];
                    $arElementos['dtDataTermino']     = $arAtividadesSessao[$arId]["dtDataTermino"];
                    $arElementos['stPrincipal']       = $arAtividadesSessao[$arId]["stPrincipal"];
                    $arTMP[] = $arElementos;
                }
            }

            $arAtividadesSessao = $arTMP;
            Sessao::write( "Atividades", $arAtividadesSessao );
            $stJs = montaListaAtividade( $arTMP );
       } else {
           $stJs = "alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
       }
       sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limpar":

        $stJs .= "d.getElementById('lsListaAtividade').innerHTML = '';\n";
        sistemaLegado::executaFrameOculto( $stJs );
        Sessao::write( 'Atividades', array() );

    break;
    case "montaAtividadeAlterar":

        $stJs = montaListaAtividade( Sessao::read( 'Atividades' ) );
        sistemaLegado::executaFrameOculto($stJs);

    break;

    case "limparAtividade":

        $stJs .= "d.getElementById('stChaveAtividade').innerHTML = '';\n";
        $stJs .= montaListaAtividade( Sessao::read( 'Atividades' ) );
        sistemaLegado::executaFrameOculto( $stJs );

    break;

    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inNumProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inNumProcesso.value = "";';
                $stJs .= 'f.inNumProcesso.focus();';
//$stJs .= 'd.frm.inNumProcesso.focus();';
                $stJs .= "alertaAviso('@Processo nao encontrado. (".$_POST["inNumProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;
}
?>
