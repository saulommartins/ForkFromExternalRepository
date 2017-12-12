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
    * Página de processamento oculto para o cadastro de face de quadra
    * Data de Criação   : 24/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterFaceQuadra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.07
*/

/*
$Log$
Revision 1.11  2006/09/18 10:30:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php");
include_once (CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php");
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFaceQuadra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaLocalizacao = new MontaLocalizacao;
$obErro = new Erro;

function listaTrecho($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de trechos" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Logradouro" );
        $obLista->ultimoCabecalho->setWidth( 82 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNumTrecho" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTrecho" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiTrecho');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

    }

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('stNumTrecho').innerHTML = '&nbsp;';\n ";
    $stJs .= "d.getElementById('spnTrechoCadastrado').innerHTML = '".$stHtml."';";
    $stJs .= "f.inNumTrecho.value = '';";
    $stJs .= "f.stTrecho.value = '';";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

switch ($_REQUEST["stCtrl"]) {
    case "buscaLocalizacao":
        if (!$_REQUEST["stChaveLocalizacao"]) {
           $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
        } else {
            $inCodigoNivel2 = Sessao::read('inCodigoNivel2');
            $obRCIMLocalizacao = new RCIMLocalizacao;
            $obRCIMLocalizacao->setCodigoNivel( $inCodigoNivel2 - 1 );
            $obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacao"] );
            $obErro = $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
            if ( $obErro->ocorreu() || !$inCodigoLocalizacao) {
                $stJs .= 'f.stChaveLocalizacao.value = "";';
                $stJs .= 'f.stChaveLocalizacao.focus();';
                $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';

                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["stChaveLocalizacao"].")','form','erro','".Sessao::getId()."');";
            } else {
                $obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
                $obErro = $obRCIMLocalizacao->consultarLocalizacao();
                if ( $obErro->ocorreu() || !$obRCIMLocalizacao->getNomeLocalizacao() ) {
                    $stJs .= 'f.stChaveLocalizacao.value = "";';
                    $stJs .= 'f.stChaveLocalizacao.focus();';
                    $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';

                    $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["stChaveLocalizacao"].")','form','erro','".Sessao::getId()."');";
                } else {
                    $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "'.$obRCIMLocalizacao->getNomeLocalizacao().'";';
                }
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        SistemaLegado::LiberaFrames();
        $stJs = "";
        break;

    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );

    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
        break;

    case "buscarTrecho":
        $obRCIMTrecho = new RCIMTrecho;
        $rsTrecho  = new RecordSet;
        if ($_REQUEST['inNumTrecho'] != "") {
            $arLogradouroSequencia = explode(".",$_REQUEST['inNumTrecho']);
            $obRCIMTrecho->setCodigoLogradouro($arLogradouroSequencia[0]);
            $obRCIMTrecho->setSequencia($arLogradouroSequencia[1]);

            if ($arLogradouroSequencia[1] != "") {
                $obRCIMTrecho->consultarTrecho( $rsTrecho );
            }
            $inNumLinhas = $rsTrecho->getNumLinhas();

            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inNumTrecho.value = "";';
                $stJs .= 'f.inNumTrecho.focus();';
                $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('Valor inválido. (".$_REQUEST["inNumTrecho"].")','frm','erro','".Sessao::getId()."');";
            } else {
                $stTrecho = $rsTrecho->getCampo ("tipo_nome");
                $stSequencia  = $rsTrecho->getCampo ("sequencia");
                $stJs .= "f.stTrecho.value = '$stTrecho';";
                $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "'.$stTrecho.' ('.$stSequencia.')";';
                $stJs .= "f.btnIncluirTrecho.disabled = false;";
            }
        } else {
            $stJs .= 'f.inNumTrecho.value = "";';
            $stJs .= 'f.inNumTrecho.focus();';
            $stJs .= 'd.getElementById("stnNumTrecho").innerHTML = "&nbsp;";';
            $stJs .= "f.btnIncluirTrecho.disabled = false;";
        }

    SistemaLegado::executaFrameOculto($stJs);
        SistemaLegado::LiberaFrames();
        $stJs = "";
    break;

    case "limparSessaoTrechos":
        $arTrechosSessao = array();
        Sessao::write('Trechos', $arTrechosSessao);
    break;
    case "ListaTrecho":
        $rsRecordSet = new RecordSet;
        $rsRecordSet->setUltimoElemento();
        $arTrechosSessao = Sessao::read('Trechos');
        listaTrecho( $arTrechosSessao );
    break;
    case "MontaTrecho":
            $stMensagem = false;
            $arTrechosSessao = Sessao::read('Trechos');
            if ( is_array( $arTrechosSessao ) ) {
                foreach ($arTrechosSessao as $campo => $valor) {
                    if ($arTrechosSessao[$campo]['inNumTrecho'] == $_REQUEST['inNumTrecho']) {
                        $stMensagem = " Trecho ".$_REQUEST['inNumTrecho']." - já existe.";
                    }
                }
            } else {
                $arTrechosSessao = array();
                Sessao::write('Trechos', $arTrechosSessao);
            }
            if ($stMensagem == "") {
                $obRCIMTrecho = new RCIMTrecho;
                $rsRecordSet = new Recordset;
                $rsTrecho = new Recordset;
                $rsRecordSet->preenche( $arTrechosSessao );
                $rsRecordSet->setUltimoElemento();

                $arTmp = explode('.',$_REQUEST['inNumTrecho']);
                $obRCIMTrecho->setCodigoLogradouro( $arTmp[0] );
                $obRCIMTrecho->setSequencia ( $arTmp[1] );
                $obRCIMTrecho->consultarTrecho( $rsTrecho );

                $inUltimoId = $rsRecordSet->getCampo("inId");
                if (!$inUltimoId) {
                    $inProxId = 1;
                } else {
                    $inProxId = $inUltimoId + 1;
                }

                $arDescricaoTrecho = explode('(',$_REQUEST['stTrecho']);

                $arElementos['inId']               = $inProxId;
                $arElementos['inNumTrecho']        = $_REQUEST['inNumTrecho'];
                $arElementos['stTrecho']           = $arDescricaoTrecho[0];
                $arElementos['inCodigoTrecho']     = $rsTrecho->getCampo("cod_trecho");
                $arElementos['inCodigoLogradouro'] = $arTmp[0];
                $arElementos['inSequencia']        = $arTmp[1];
                $arTrechosSessao[]                 = $arElementos;
                Sessao::write('Trechos', $arTrechosSessao);
                listaTrecho( $arTrechosSessao );
            } else {
                $stJs = "SistemaLegado::alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
                SistemaLegado::executaFrameOculto($stJs);
            }

   break;
   case "excluiTrecho":
       $id = $_REQUEST['inId'];
       $stMensagem = false;
       $arTrechosSessao = Sessao::read('Trechos');

       if ($stMensagem==false) {
           reset($arTrechosSessao);
           while ( list( $arId ) = each( $arTrechosSessao ) ) {
               if ($arTrechosSessao[$arId]["inId"] != $id) {
                   $arElementos['inId']           = $arTrechosSessao[$arId]["inId"];
                   $arElementos['inNumTrecho']    = $arTrechosSessao[$arId]["inNumTrecho"];
                   $arElementos['stTrecho']       = $arTrechosSessao[$arId]["stTrecho"];
                   $arElementos['inCodigoTrecho'] = $arTrechosSessao[$arId]["inCodigoTrecho"];
                   $arTMP[] = $arElementos;
               }
           }
           Sessao::write('Trechos', $arTMP);
           listaTrecho( $arTMP );
       } else {
           $stJs = "SistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
       }
   break;
   case "buscarLogradouro":
       $obRCIMTrecho     = new RCIMTrecho;
       $rsLogradouro  = new RecordSet;

       if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
           $stJs .= 'f.inNumLogradouro.value = "";';
           $stJs .= 'f.inNumLogradouro.focus();';
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
               $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
               $stJs .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
               $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
           }
       }

        SistemaLegado::executaFrameOculto($stJs);
        SistemaLegado::LiberaFrames();

        $stJs = "";
        break;

   case "limparSpnTrecho":
        $arTrechosSessao = array();
        Sessao::write('Trechos', $arTrechosSessao);
        break;

   case "buscaLegalAliquota":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacaoAliquota'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacaoAliquota').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacaoAliquota"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacaoAliquota').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacaoAliquota.value = "";';
            $stJs .= 'f.inCodigoFundamentacaoAliquota.focus();';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaLegal":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
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
}

if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>
