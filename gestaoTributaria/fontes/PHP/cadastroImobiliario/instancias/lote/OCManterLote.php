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
    * Página de processamento oculto para o cadastro de lote
    * Data de Criação   : 01/12/2004

    * @

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: OCManterLote.php 62230 2015-04-10 17:35:05Z michel $

    * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"   );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMProprietario.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLote.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgLote   = "FM".$stPrograma."Lote.php";
$pgProp   = "FM".$stPrograma."Proprietario.php";
$pgCond   = "FM".$stPrograma."Condominio.php";
$pgTransf = "FM".$stPrograma."Transferencia.php";

$stCaminho   = CAM_GT_CIM_INSTANCIAS."lote/";

function BuscaCGM()
{
    global $_REQUEST;
    $obRCGM = new RCGM;

    $stText = "inNumCGM";
    $stSpan = "inNomCGM";
    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function montaListaConfrontacao(&$rsListaConfrontacao)
{
     if ( !$rsListaConfrontacao->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaConfrontacao );
         $obLista->setTitulo ("Listas de confrontações");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Ponto Cardeal");
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Tipo" );
         $obLista->ultimoCabecalho->setWidth( 15 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 30 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Extensão" );
         $obLista->ultimoCabecalho->setWidth( 18 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Testada" );
         $obLista->ultimoCabecalho->setWidth( 18 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomePontoCardeal" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stLsTipoConfrotacao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stDescricao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->ultimoDado->setCampo( "flExtensao" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stTestada" );
         $obLista->commitDado();

         if ($_REQUEST['stAcao'] == "alterar") {

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "ALTERAR" );
            $obLista->ultimaAcao->addCampo( "1","inLinha" );
            $obLista->ultimaAcao->addCampo( "2","stLsTipoConfrotacao" );
            $obLista->ultimaAcao->addCampo( "3","flExtensao" );
            $obLista->ultimaAcao->addCampo( "4","stTestada" );
            $obLista->ultimaAcao->addCampo( "5", "stChaveTrecho" );
            $obLista->ultimaAcao->addCampo( "6", "stTrecho" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "javascript:montaConfrontacaoAlterar();" );
            $obLista->commitAcao();

         }

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inLinha" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirConfrontacao');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }
     $stJs .= "d.getElementById('lsListaConfrontacoes').innerHTML = '".$stHTML."';";

     return $stJs;
}

function montaListaLote($arListaLotes)
{
    GLOBAL $flAreaTotal;

    $rsListaLotes = new Recordset;
    $rsListaLotes->preenche( is_array($arListaLotes) ? $arListaLotes : array() );

    if ( !$rsListaLotes->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaLotes    );
        $obLista->setTitulo                    ( "Lista de lotes" );
        $obLista->setMostraPaginacao           ( false );
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    (2);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "Lote"           );
        $obLista->ultimoCabecalho->setWidth    (40);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    (2);
        $obLista->commitCabecalho              ();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inValorLote" );
        $obLista->commitDado();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDado('excluiLote');" );
        $obLista->ultimaAcao->addCampo("1","inLinha");

        $obLista->commitAcao();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp;";
    }
    $stUnidade = substr( $_REQUEST['flAreaLote'], strpos( $_REQUEST['flAreaLote'], " ") + 1 );
    $flAreaTotal = substr( $_REQUEST['flAreaLote'], 0, strpos( $_REQUEST['flAreaLote'], " ") );
    $flAreaTotal =  str_replace(',','.',str_replace('.','',$flAreaTotal));
    while ( !$rsListaLotes->eof() ) {
        $flAreaTotal = $rsListaLotes->getCampo("flAreaReal") + $flAreaTotal;
        $rsListaLotes->proximo();
    }
    $stJs .= "d.getElementById('spanLotes').innerHTML = '".$stHTML."';\n";
    $stJs .= "d.getElementById('flAreaLote').innerHTML = '".str_replace('.',',',$flAreaTotal)." ".$stUnidade."';\n";

    return $stJs;
}

function montaListaEdificacoes()
{
/*
lista as edificações para validação
*/

    $obRCIMEdificacao = new RCIMEdificacao();
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
    $obErro = $obRCIMEdificacao->obRCIMImovel->roRCIMLote->consultaLoteOriginal( $inCodLoteOriginal, $nuLotesValidacao );
    // agrupar inscricações por edificação
    $arNewElementos = array();
    $obRCIMEdificacao->obRCIMImovel->roRCIMLote->setCodigoLote( $inCodLoteOriginal );
    $obRCIMEdificacao->listarEdificacoesLote ( $rsEdificacoesLote );

    if ( $rsEdificacoesLote->getNumLinhas() > 0 ) {
        $arElementos = $rsEdificacoesLote->arElementos;
        $boPrimeiro = true;

        for ( $i=0; $i < count($arElementos);$i++) {
            if ($arElementos[$i]['timestamp'] < $arElementos[$i]['timestamp_parcelamento']) {
                if ($boPrimeiro) { // se for o primeiro elemento, copia imediatamente!
                    $arNewElementos[0] = $arElementos[$i];
                    $arNewElementos[0]["seq"] = 1;
                    $arNewElementos[0]["sel"] = 't';
                    $cur = end($arNewElementos);
                    $boPrimeiro = false;
                // caso o cod_construcao e cod_lote forem iguais ele concatena as inscrições municipais
                } elseif ( ($cur["cod_lote"] ==  $arElementos[$i]["cod_lote"]) && ($cur["cod_construcao"] ==  $arElementos[$i]["cod_construcao"]) && (!$boPrimeiro) ) {
                    $arNewElementos[count($arNewElementos)-1]["inscricao_municipal"] .= ",".$arElementos[$i]["inscricao_municipal"];
                // caso seja de lote igual, mas construcao diferente, adiciona novo elemento no array
                } elseif ( ($cur["cod_lote"] ==  $arElementos[$i]["cod_lote"]) && ($cur["cod_construcao"] !=  $arElementos[$i]["cod_construcao"]) && (!$boPrimeiro) ) {
                    $arNewElementos[] = $arElementos[$i];
                    $arNewElementos[count($arNewElementos)-1]["seq"] = count($arNewElementos);
                    $arNewElementos[count($arNewElementos)-1]["sel"] = 't';
                    $cur = end($arNewElementos);
                }
            }
        }
    }

    // guarda na seção as lista de edificações
    Sessao::write('lsEdificacoes', $arNewElementos);
    $rsNovoRecordsetEdificacoes = new Recordset;
    $rsNovoRecordsetEdificacoes->preenche($arNewElementos);

    $obLista = new Lista;
    $obLista->setRecordSet( $rsNovoRecordsetEdificacoes );
    $obLista->setTitulo ("Lista de Edificações");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo de Edificação" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Inscrições Imobiliárias" );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Selecionar");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_construcao" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_tipo" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inscricao_municipal" );
    $obLista->commitDado();

    $obChkSel = new Checkbox;
    $obChkSel->setName              ( "boSel"                                 );
    $obChkSel->obEvento->setOnChange( "buscaValor('atualizaCheckSel');"       );
    $obChkSel->setChecked           ( false                                   );
    $obLista->addDadoComponente     ( $obChkSel                               );
    $obLista->ultimoDado->setCampo  ( "[cod_construcao][inscricao_municipal]" );
    $obLista->commitDadoComponente  ();

    $obLista->montaHtml();

    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('lsListaEdificacoes').innerHTML = '".$stHtml."';";

    return $stJs;

} // fecha if validar

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCadastroLocalizacao( false );

$stCtrl = $_REQUEST['stCtrl'];
switch ($stCtrl) {
    case "buscaLocalizacao":
        $stJs = '';
        if (!$_REQUEST["stChaveLocalizacao"]) {
           $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
        } else {
            $obRCIMLocalizacao = new RCIMLocalizacao;
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
                if ( $obErro->ocorreu() ) {
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
        break;

    case "montaEdificacao":
        SistemaLegado::executaFrameOculto( montaListaEdificacoes() );
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
    case "buscaBairro":

        $obRCIMBairro       = new RCIMBairro;
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );

        $inCodUF        = $arConfiguracao["cod_uf"];
        $inCodMunicipio = $arConfiguracao["cod_municipio"];

        $obRCIMBairro->setCodigoBairro    ( $_REQUEST["inCodigoBairroLote"] );
        $obRCIMBairro->setCodigoUF        ( $arConfiguracao["cod_uf"] );
        $obRCIMBairro->setCodigoMunicipio ( $arConfiguracao["cod_municipio"] );
        $obErro = $obRCIMBairro->consultarBairro();

        if ( $obRCIMBairro->getNomeBairro() ) {
            $stJs = 'f.inCodigoUF.value = '.$inCodUF.';';
            $stJs .= 'f.inCodigoMunicipio.value = '.$inCodMunicipio.';';
            $stJs .= 'd.getElementById("innerBairroLote").innerHTML = "'.$obRCIMBairro->getNomeBairro().'";';
        } else {
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'd.getElementById("innerBairroLote").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodigoBairroLote"].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaLogradouro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
                $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaLogradouroFiltro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ($_REQUEST["inNumLogradouro"]) {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
        }

        if ( $rsLogradouro->eof() ) {
            $stJs .= 'f.inNumLogradouro.value = "";';
            $stJs .= 'f.inNumLogradouro.focus();';
            $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
            $stJs .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
            $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomeLogradouro.'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
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
                $stJs .= 'd.getElementById("inNumTrecho").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('Valor inválido. (".$_REQUEST["inNumTrecho"].")','frm','erro','".Sessao::getId()."');";
            } else {
                $stTrecho = $rsTrecho->getCampo ("tipo_nome");
                $stSequencia  = $rsTrecho->getCampo ("sequencia");
                $stJs .= "f.stTrecho.value = '$stTrecho';";
                $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "'.$stTrecho.' ('.$stSequencia.')";';
            }
        } else {
            $stJs .= 'f.inNumTrecho.value = "";';
            $stJs .= 'f.inNumTrecho.focus();';
            $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "incluirConfrontacao":
        $arConfrontacoesSessao = Sessao::read('confrontacoes');
        if ($_REQUEST['stAcaoAlterar']) { //caso seja para alteração
            switch ($_REQUEST['stAcaoAlterar']) {

                case "trecho":

                    $obRCIMTrecho = new RCIMTrecho;
                    $arLogradouroSequencia = explode(".",$_REQUEST['inNumTrecho']);
                    $obRCIMTrecho->setCodigoLogradouro($arLogradouroSequencia[0]);
                    $obRCIMTrecho->setSequencia($arLogradouroSequencia[1]);
                    $obRCIMTrecho->consultarTrecho( $rsTrecho );
                    $stTrecho = $rsTrecho->getCampo ("tipo_nome");
                    $stSequencia  = $rsTrecho->getCampo ("sequencia");
                    $stDescricao = $_REQUEST['inNumTrecho']." - ".$stTrecho;
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['stDescricao'] = $stDescricao;
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['stTrecho'] = $stTrecho;
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['inCodigoTrechoConfrnotacao'] = $rsTrecho->getCampo('cod_trecho');
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['inCodigoLogradouroConfrontacao'] = $rsTrecho->getCampo('cod_logradouro');
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['stChaveTrecho'] = $_REQUEST['inNumTrecho'];

                break;

                case "lote":

                    $obCIMLoteConfrontacao = new RCIMLote;
                    $obCIMLoteConfrontacao->setCodigoLote( $_REQUEST["inCodigoLoteConfrontacao"] );
                    $obCIMLoteConfrontacao->consultarLote();
                    $stDescricao = $obCIMLoteConfrontacao->getNumeroLote();

                break;

                case "outros":
                    $stDescricao = str_replace(chr(13).chr(10),"<br>",$_REQUEST["stDescricaoOutros"]);

                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['stDescricaoOutros'] = $stDescricao;
                    $arConfrontacoesSessao[$_REQUEST['inIndice']]['stDescricao']       = $stDescricao;

                break;

            }

            $obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
            $obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );

            while ( !$rsListaPontosCardeais->eof() ) {
                $arPontoCardeal[ $rsListaPontosCardeais->getCampo("cod_ponto")] = $rsListaPontosCardeais->getCampo("nom_ponto");
                $rsListaPontosCardeais->proximo();
            }
            $arConfrontacoesSessao[$_REQUEST['inIndice']]['inCodigoPontoCardeal'] = $_REQUEST['inCodigoPontoCardeal'];
            $arConfrontacoesSessao[$_REQUEST['inIndice']]['stNomePontoCardeal']   = $arPontoCardeal[$_REQUEST['inCodigoPontoCardeal']];

            //trecho
            $arConfrontacoesSessao[$_REQUEST['inIndice']]['boTestada'] = $_REQUEST['boTestada'];
            $arConfrontacoesSessao[$_REQUEST['inIndice']]['stTestada'] = $_REQUEST['boTestada'] == 'S' ? 'Sim' : 'Não';

            $arConfrontacoesSessao[$_REQUEST['inIndice']]['flExtensao'] = $_REQUEST['flExtensao'];

            Sessao::write('confrontacoes', $arConfrontacoesSessao);

            //------------------------------------------------------------------ FINALIZANDO
            $rsListaConfrontacao = new RecordSet;
            $rsListaConfrontacao->preenche( $arConfrontacoesSessao );

            $stJs .= "f.inCodigoPontoCardeal.options[0].selected = true;\n";
            $stJs .= "f.stTipoConfrotacao[0].disabled = false;\n";
            $stJs .= "f.stTipoConfrotacao[1].disabled = false;\n";
            $stJs .= "f.stTipoConfrotacao[2].disabled = false;\n";
            $stJs .= "f.stTipoConfrotacao[0].checked = false;\n";
            $stJs .= "f.stTipoConfrotacao[1].checked = false;\n";
            $stJs .= "f.stTipoConfrotacao[2].checked = false;\n";
            $stJs .= "f.stAcaoConfrontacao.value = '';\n";
            $stJs .= "f.inIndice.value = '';\n";
            $stJs .= "f.flExtensao.value = '';\n";
            $stJs .= 'd.getElementById("spnConfrontacao").innerHTML = "";';

            $stJs .=  montaListaConfrontacao( $rsListaConfrontacao );
            SistemaLegado::executaFrameOculto($stJs);
            exit;

        } else {//inclusao

        $arConfrontacoesSessao = Sessao::read('confrontacoes');
            switch ( strtoupper( $_REQUEST["stTipoConfrotacao"] ) ) {
                case "TRECHO":
                    $obRCIMTrecho = new RCIMTrecho;
                    $arLogradouroSequencia = explode(".",$_REQUEST['inNumTrecho']);
                    $obRCIMTrecho->setCodigoLogradouro($arLogradouroSequencia[0]);
                    $obRCIMTrecho->setSequencia($arLogradouroSequencia[1]);
                    $obRCIMTrecho->consultarTrecho( $rsTrecho );
                    $stTrecho = $rsTrecho->getCampo ("tipo_nome");
                    $stSequencia  = $rsTrecho->getCampo ("sequencia");
                    $stDescricao = $_REQUEST['inNumTrecho']." - ".$stTrecho;
                break;
                case "LOTE":
                    $obCIMLoteConfrontacao = new RCIMLote;
                    $obCIMLoteConfrontacao->setCodigoLote( $_REQUEST["inCodigoLoteConfrontacao"] );
                    $obCIMLoteConfrontacao->consultarLote();
                    $stDescricao = $obCIMLoteConfrontacao->getNumeroLote();
                break;
                case "OUTROS":
                    $stDescricao = str_replace(chr(13).chr(10),"<br>",$_REQUEST["stDescricaoOutros"]);
                break;
            }
        }

        $obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
        $obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );
        $rsListaPontosCardeais->setPrimeiroElemento();

        while ( !$rsListaPontosCardeais->eof() ) {
            $arPontoCardeal[ $rsListaPontosCardeais->getCampo("cod_ponto")] = $rsListaPontosCardeais->getCampo("nom_ponto");
            $rsListaPontosCardeais->proximo();
        }

        $arConfrontacoes = Sessao::read('confrontacoes');
        $arConfrontacoes[] = array(
                 "inCodigoPontoCardeal"           => $_REQUEST["inCodigoPontoCardeal"],
                 "stNomePontoCardeal"             => $arPontoCardeal[$_REQUEST["inCodigoPontoCardeal"]],
                 "stTipoConfrotacao"              => $_REQUEST["stTipoConfrotacao"],
                 "stLsTipoConfrotacao"            => ucfirst( $_REQUEST["stTipoConfrotacao"] ),
                 "flExtensao"                     => $_REQUEST["flExtensao"],
                 "boTestada"                      => $_REQUEST["boTestada"],
                 "stTestada"                      => $_REQUEST["boTestada"] == "S" ? "Sim" : "Não",
                 "inCodigoLoteConfrontacao"       => $_REQUEST["inCodigoLoteConfrontacao"],
                 "inCodigoTrechoConfrontacao"     => $_REQUEST["inCodigoTrechoConfrontacao"],
                 "inCodigoLogradouroConfrontacao" => $_REQUEST["inCodigoLogradouroConfrontacao"],
                 "stDescricaoOutros"              => $_REQUEST["stDescricaoOutros"],
                 "stChaveTrecho"                  => $_REQUEST["inNumTrecho"],
                 "stTrecho"                       => $_REQUEST["stTrecho"],
                 "stDescricao"                    => $stDescricao,
                 "inLinha"                        => count( Sessao::read('confrontacoes')  ));

        Sessao::write('confrontacoes', $arConfrontacoes);

        $rsListaConfrontacao = new RecordSet;
        $rsListaConfrontacao->preenche( Sessao::read('confrontacoes') );
        $stJs .= "f.inCodigoPontoCardeal.options[0].selected = true;\n";
        $stJs .= "f.stTipoConfrotacao[0].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[1].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[2].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[0].checked = false;\n";
        $stJs .= "f.stTipoConfrotacao[1].checked   = false;\n";
        $stJs .= "f.stTipoConfrotacao[2].checked = false;\n";
        $stJs .= "f.stAcaoConfrontacao.value = '';\n";
        $stJs .= "f.flExtensao.value = '';\n";
        $stJs .= 'd.getElementById("spnConfrontacao").innerHTML = "";';
        $stJs .=  montaListaConfrontacao( $rsListaConfrontacao );
        SistemaLegado::executaFrameOculto($stJs);

    break;

    case "alterarConfrontacaoLista":

        $obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
        $obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );

        while ( !$rsListaPontosCardeais->eof() ) {
            $arPontoCardeal[ $rsListaPontosCardeais->getCampo("cod_ponto")] = $rsListaPontosCardeais->getCampo("nom_ponto");
            $rsListaPontosCardeais->proximo();
        }
        $arConfrontacoesSessao[$_REQUEST['inIndice']]['inCodigoPontoCardeal'] = $_REQUEST['inCodigoPontoCardeal'];
        $arConfrontacoesSessao[$_REQUEST['inIndice']]['stNomePontoCardeal']   = $arPontoCardeal[$_REQUEST['inCodigoPontoCardeal']];
        $arConfrontacoesSessao[$_REQUEST['inIndice']]['flExtensao']           = $_REQUEST['flExtensao'];

        Sessao::write('confrontacoes', $arConfrontacoesSessao);

        $rsListaConfrontacao = new RecordSet;
        $rsListaConfrontacao->preenche( Sessao::read('confrontacoes') );

        $stJs .= "f.inCodigoPontoCardeal.options[0].selected = true;\n";
        $stJs .= "f.stTipoConfrotacao[0].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[1].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[2].disabled = false;\n";
        $stJs .= "f.stTipoConfrotacao[0].checked = false;\n";
        $stJs .= "f.stTipoConfrotacao[1].checked = false;\n";
        $stJs .= "f.stTipoConfrotacao[2].checked = false;\n";
        $stJs .= "f.stAcaoConfrontacao.value = '';\n";
        $stJs .= "f.inIndice.value = '';\n";
        $stJs .= "f.flExtensao.value = '';\n";

        $stJs .=  montaListaConfrontacao( $rsListaConfrontacao );
        SistemaLegado::executaFrameOculto($stJs);

    break;

    case "excluirConfrontacao":
        $arNovaListaConfrontacao = array();
        $inContLinha = 0;
        $inContador = 0;
        $arConfrontacoesSessao = Sessao::read('confrontacoes');
        foreach ($arConfrontacoesSessao as $inChave => $arConfrontacoes) {
            if ($inChave != $_REQUEST[inLinha]) {
                $arConfrontacoes["inLinha"] = $inContLinha++;
                $arNovaListaConfrontacao[] = $arConfrontacoes;
                if ($arConfrontacoes["stTipoConfrotacao"] == "trecho") {
                    $arChaveConfrontacao = explode( ".",$arConfrontacoes["stChaveTrecho"] );
                    $stConfrontacao = $arConfrontacoes["stTrecho"]." (".$arChaveConfrontacao[1].")";
                    $inContador++;
                }
            } elseif ($arConfrontacoes["stTipoConfrotacao"] == "trecho" && $_REQUEST['stAcao'] != "incluir" && $arConfrontacoesSessao[$inChave]["inCodigoConfrontacao"]) {
                $obRCIMConfrotacaoTrecho = new RCIMConfrontacaoTrecho( new RCIMLote );
                $obRCIMConfrotacaoTrecho->setCodigoConfrontacao( $arConfrontacoesSessao[$inChave]["inCodigoConfrontacao"] );
                $obRCIMConfrotacaoTrecho->roRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );

                $arChaveTrecho = explode( ".", $arConfrontacoesSessao[$inChave]["stChaveTrecho"] );
                $obRCIMConfrotacaoTrecho->obRCIMTrecho->setCodigoLogradouro( $arChaveTrecho[0] );
                $obRCIMConfrotacaoTrecho->obRCIMTrecho->setSequencia( $arChaveTrecho[1] );

                $obErro = $obRCIMConfrotacaoTrecho->verificaImovelConfrontacao();
                if ( $obErro->ocorreu() ) {
                    $stJs .= "alertaAviso('".urlencode($obErro->getDescricao())."','frm','erro','".Sessao::getId()."');";
                    SistemaLegado::executaFrameOculto($stJs);
                    exit();
                }
            }
        }
        $arConfrontacoesSessao = array();
        Sessao::write('confrontacoes', $arConfrontacoesSessao);
        Sessao::write('confrontacoes', $arNovaListaConfrontacao);
        $rsListaConfrontacao = new RecordSet;
        $rsListaConfrontacao->preenche( Sessao::read('confrontacoes') );
        $stJs .= montaListaConfrontacao( $rsListaConfrontacao );
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "trecho":
        $ArDescricao = explode ('-', $_REQUEST['descricao'] );
        $inNumTrecho = trim($ArDescricao[0]);
        $ArDescricaoTemp = '';

        for($i=1; $i<(count($ArDescricao));$i++){
            if($ArDescricaoTemp=='')
                $ArDescricaoTemp .= trim($ArDescricao[$i]);
            else
                $ArDescricaoTemp .= ' - '.trim($ArDescricao[$i]);
        }
        $stTrecho = $ArDescricaoTemp;

        $inNumTrecho = (isset($_REQUEST['stChaveTrecho'])) ? $_REQUEST['stChaveTrecho'] : trim($ArDescricao[0]);
        $stTrecho = (isset($_REQUEST['stTrecho'])) ? $_REQUEST['stTrecho'] : $stTrecho;

        $obBuscaTrecho = new BuscaInner;
        $obBuscaTrecho->setId                           ( "stNumTrecho"     );
        $obBuscaTrecho->setTitle                        ( "Trecho da confrontação" );
        $obBuscaTrecho->setNull                         ( true              );
        $obBuscaTrecho->obCampoCod->setName             ( "inNumTrecho"     );
        $obBuscaTrecho->obCampoCod->setValue            ( $inNumTrecho      );
        $obBuscaTrecho->obCampoCod->setInteiro          ( false             );
        $obBuscaTrecho->obCampoCod->obEvento->setOnChange ( "buscarTrecho();" );
        $obBuscaTrecho->setFuncaoBusca                  ("abrePopUp('".CAM_GT_CIM_POPUPS."trecho/FLProcurarTrecho.php','frm','inNumTrecho','stNumTrecho','','".Sessao::getId()."','800','550')");
        $obBuscaTrecho->setRotulo                       ( "*Trecho"         );

        $obRdoTestada = new SimNao;
        $obRdoTestada->setName   ( "boTestada" );
        $obRdoTestada->setRotulo ( "*Testada"  );
        $obRdoTestada->setChecked ( $_REQUEST['testada']  );
        $obRdoTestada->setTitle  ( "Informa se o trecho faz parte da testada" );

        $obHdnAcao = new Hidden;
        $obHdnAcao->setName  ('stAcaoAlterar');
        $obHdnAcao->setValue ( 'trecho' );

        $obFormulario = new formulario;
        if ($_REQUEST['Acao'] == 'alterarConfrontacao') {
            $obFormulario->addHidden ( $obHdnAcao );
        }
        $obFormulario->addComponente( $obRdoTestada  );
        $obFormulario->addComponente( $obBuscaTrecho );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs .= "d.getElementById(\"spnConfrontacao\").innerHTML = '".$stHtml."'; ";

        if ($_REQUEST['Acao'] == 'alterarConfrontacao') {
            $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "'. $stTrecho .'";';
        } else {
            $stJs .= 'd.getElementById("stNumTrecho").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "lote":

        $obRCIMLote = new RCIMLote;
        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->setCodigoModulo( 12 );
        $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCIMConfiguracao->consultarConfiguracao();
        $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

        $obHdnTrecho = new Hidden;
        $obHdnTrecho->setName  ('stTrecho');
        $obHdnTrecho->setValue ( '' );

        $obHdnAcao = new Hidden;
        $obHdnAcao->setName  ('stAcaoAlterar');
        $obHdnAcao->setValue ( 'lote' );

        if ($request->get('stAcao') == 'incluir') {
            if ($request->get('stChaveLocalizacao') != '') {
                $obTCIMLote = new TCIMLote;
                $obTCIMLote->recuperaListaConfrontacaoLocalizacao( $rsLoteConfrotacao, $request->get("stChaveLocalizacao") );
            } else {
                $stJs .= "alertaAviso('A localização deve ser informada antes de incluir uma confrontação.','frm','erro','".Sessao::getId()."');";
                $stJs .= "jq_('#stTipoConfrontacaoTrecho').prop('checked', 'checked');";
                SistemaLegado::executaFrameOculto($stJs);
                exit();
            }
        } else {
            $obTCIMLote = new TCIMLote;
            $obTCIMLote->recuperaListaConfrontacaoLote( $rsLoteConfrotacao, $_REQUEST["inCodigoLote"] );
        }

        $rsLoteConfrotacao->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

        if ($_REQUEST['Acao'] != 'alterarConfrontacao') {
            $obCmbConfrontacaoLote = new Select;
            $obCmbConfrontacaoLote->setName       ( "inCodigoLoteConfrontacao" );
            $obCmbConfrontacaoLote->setTitle      ( "Lote que faz a confrontação" );
            $obCmbConfrontacaoLote->setNull       ( false                      );
            $obCmbConfrontacaoLote->setRotulo     ( "Lote"                     );
            $obCmbConfrontacaoLote->setStyle      ( "width: 150px"             );
            $obCmbConfrontacaoLote->addOption     ( "", "Selecione"            );
            $obCmbConfrontacaoLote->setCampoID    ( "cod_lote"                 );
            $obCmbConfrontacaoLote->setCampoDesc  ( "valor"                    );
            $obCmbConfrontacaoLote->preencheCombo ( $rsLoteConfrotacao         );
        }

        $obFormulario = new formulario;
        if ($_REQUEST['Acao'] == 'alterarConfrontacao') {
            $obFormulario->addHidden ( $obHdnAcao );
        }
        $obFormulario->addHidden ( $obHdnTrecho );

        if ($_REQUEST['Acao'] != 'alterarConfrontacao') {
            $obFormulario->addComponente( $obCmbConfrontacaoLote );
        }

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs .= "d.getElementById(\"spnConfrontacao\").innerHTML = '".$stHtml."';";

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "outros":

        $obTxtDescricaoOutros = new TextArea;
        $obTxtDescricaoOutros->setName     ( "stDescricaoOutros" );
        $obTxtDescricaoOutros->setTitle    ( "Descrição da confrontação" );
        $obTxtDescricaoOutros->setRotulo   ( "Descrição"         );
        $obTxtDescricaoOutros->setValue   ( $_REQUEST['descricao']  );
        $obTxtDescricaoOutros->setNull     ( false               );
        $obTxtDescricaoOutros->setCols     ( 50                  );
        $obTxtDescricaoOutros->setRows     ( 5                   );

        $obHdnTrecho = new Hidden;
        $obHdnTrecho->setName  ('stTrecho');
        $obHdnTrecho->setValue ('');

        $obHdnAcao = new Hidden;
        $obHdnAcao->setName  ('stAcaoAlterar');
        $obHdnAcao->setValue ( 'outros' );

        $obFormulario = new formulario;
        if ($_REQUEST['Acao'] == 'alterarConfrontacao') {
            $obFormulario->addHidden ( $obHdnAcao );
        }
        $obFormulario->addHidden ($obHdnTrecho);
        $obFormulario->addComponente( $obTxtDescricaoOutros );
        $obFormulario->montaInnerHTML();

        $stHtml = $obFormulario->getHTML();

        $stJs .= "d.getElementById(\"spnConfrontacao\").innerHTML = '".$stHtml."';";

        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "limparFiltro":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( "" );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "limparConfrontacao":
        $arConfrontacoesSessao = array();
        Sessao::write('confrontacoes',  $arConfrontacoesSessao );
        $rsListaConfrontacao = new RecordSet;
        $stJs .= "f.inCodigoPontoCardeal.options[0].selected = true;\n";
        $stJs .= "f.flExtensao.value = '';\n";
        $stJs .=  montaListaConfrontacao( $rsListaConfrontacao );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "LimparSessao":
        $arConfrontacoesSessao = array();
        Sessao::write('confrontacoes', $arConfrontacoesSessao );
    break;
    case "limparFormulario":
        for ( $i=1; $i<=($_REQUEST['inNumNiveis']-1); $i++ ) {
            $stJs .= "f.inCodLocalizacao_".$i.".options[0].selected = true;\n";
        }
        $arConfrontacoesSessao = array();
        Sessao::write('confrontacoes', $arConfrontacoesSessao );
        $rsListaConfrontacao = new RecordSet;
        $stJs .= "f.inCodigoPontoCardeal.options[0].selected = true;\n";
        $stJs .= "f.flExtensao.value = '';\n";
        $stJs .=  montaListaConfrontacao( $rsListaConfrontacao );
        $stJs .= "d.getElementById('innerBairroLote').innerHTML = '&nbsp;'\n";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "validaDataInscricaoLote":
        $stDataLimite = "15000421";
        $stDataInscricaoLote = $_REQUEST["dtDataInscricaoLote"];
        $stDiaInscricaoLote = substr($stDataInscricaoLote,0,2);
        $stMesInscricaoLote = substr($stDataInscricaoLote,3,5);
        $stAnoInscricaoLote = substr($stDataInscricaoLote,6);
        $stDataInscricaoLote = $stAnoInscricaoLote.$stMesInscricaoLote.$stDiaInscricaoLote;
        if ($stDataInscricaoLote < $stDataLimite) {
            $stJs .= "    erro = true;                                                                      ";
            $stJs .= "    f.dtDataInscricaoLote.value=\"\";                                                 ";
            $stJs .= "    mensagem += \"@Campo Data da Inscrição deve ser posterior a 21/04/1500!\";        ";
            $stJs .= "    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');                     ";
            $stJs .= "    f.dtDataInscricaoLote.focus();                                                    ";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "validaDataDesmembramento":
        $stDataDesmembramento = $_REQUEST["dtDataDesmembramento"];
        $stDiaDesmembramento = substr($stDataDesmembramento,0,2);
        $stMesDesmembramento = substr($stDataDesmembramento,3,5);
        $stAnoDesmembramento = substr($stDataDesmembramento,6);
        $stDataDesmembramento = $stAnoDesmembramento.$stMesDesmembramento.$stDiaDesmembramento;
        if ($stDataDesmembramento < $stDataInscricaoLote) {
            $stJs .= "    erro = true;                                                                      ";
            $stJs .= "    f.dtDataDesmembramento.value=\"\";                                                 ";
            $stJs .= "    mensagem += \"@Campo Data do desmembramento deve ser posterior a data de inscrição!\";        ";
            $stJs .= "    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');                     ";
            $stJs .= "    f.dtDataDesmembramento.focus();                                                    ";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "visualizarProcesso":
        if ($_REQUEST["funcionalidade"] == 178) {
            $obRCIMLote = new RCIMLoteUrbano;
        } elseif ($_REQUEST["funcionalidade"] == 193) {
            $obRCIMLote = new RCIMLoteRural;
        }

        $arChaveAtributoLoteProcesso =  array( "cod_lote" => $_REQUEST["cod_lote"],"timestamp"=> $_REQUEST[timestamp], "cod_processo" => $_REQUEST["cod_processo"] );
        $obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLoteProcesso );
        $obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLoteProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obMontaAtributosLoteProcesso = new MontaAtributos;
        $obMontaAtributosLoteProcesso->setTitulo     ( "Atributos"        );
        $obMontaAtributosLoteProcesso->setName       ( "Atributo_"  );
        $obMontaAtributosLoteProcesso->setLabel       ( true  );
        $obMontaAtributosLoteProcesso->setRecordSet  ( $rsAtributosLoteProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosLoteProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaProcesso":
        $stJs = '';
        $obRProcesso  = new RProcesso;
        if ( $request->get('inNumProcesso') != '' ) {
            list($inProcesso,$inExercicio) = explode("/",$request->get('inNumProcesso'));
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inNumProcesso.value = "";';
                $stJs .= 'f.inNumProcesso.focus();';
                $stJs .= "alertaAviso('@Processo nao encontrado. (".$_POST["inNumProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "incluiLote":
        $obRCIMLote   = new RCIMLote;
        $rsLoteamento = new RecordSet;
        $arNumLote = explode("-",$_REQUEST['inNumLote']);
        $inNumLote = $arNumLote[0];
        $obRCIMLote->setCodigoLote( $inNumLote );
        $obRCIMLote->buscarLotesCadastrados( $rsLotes );
        $stMensagem = "";

        $boErro = false;
        $arLoteSessao = Sessao::read('lotes');

        foreach ($arLoteSessao as  $inChave => $arLotes) {
            if ($arLotes["inNumLote"] == $inNumLote) {
                $boErro = true;
                $stMensagem  = "Lote já informado!";
                break;
            }
        }

        if ( $rsLotes->getNumLinhas() <= 0 ) {
            $boErro = true;
            $stMensagem = "Campo Lote inválido.";
        } else {
            $flAreaLote = $rsLotes->getCampo("area_real");
        }

        if ($inNumLote == "") {
            $boErro = true;
            $stMensagem = "Campo Lote nulo.";
        }

        if ($boErro) {
            $stJs = "alertaAviso('".$stMensagem."(".$arNumLote[1].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            if ($_REQUEST['boCaucionado'] == "S") {
                $boTmp = "Sim";
            } elseif ($_REQUEST['boCaucionado'] == "N") {
                $boTmp = "Não";
            }
            $stJs  = "f.inNumLote.value = '';\n";
            $arLote = array( "inNumLote"    => $arNumLote[0],
                             "inValorLote"  => $arNumLote[1],
                             "flAreaReal"   => $flAreaLote );

            $arLote["inLinha"] = count( Sessao::read('lotes') );

            $arLoteSessao[] = $arLote;
            Sessao::write('lotes', $arLoteSessao);
            $stJs .= montaListaLote( Sessao::read('lotes') );
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluiLote":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaLote = array();
        $inContLinha = 0;
        $arLoteSessao = Sessao::read('lotes');
        foreach ($arLoteSessao as $inChave => $arLotes) {
            if ($inChave != $inLinha) {
                $arLotes["inLinha"] = $inContLinha++;
                $arNovaListaLote[] = $arLotes;
            }
        }
        Sessao::write('lotes', $arNovaListaLote);
        $rsListaLote = new RecordSet;
        $stJs = montaListaLote( Sessao::read('lotes') );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "limparSpanLote";
        $stJs .= "d.getElementById('flAreaLote').innerHTML = '".$_REQUEST['flAreaLote']."';\n ";
        $stJs .= 'd.getElementById("spanLotes").innerHTML = "&nbsp;";';
        $arLoteSessao = array();
        Sessao::write('lotes', $arLoteSessao );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "atualizaCheckSel":
        $listaEdificacoesSessao = Sessao::read('lsEdificacoes');
        foreach ($listaEdificacoesSessao as $listaEdificacoes) {
            $posicaoSessao = $listaEdificacoes["seq"] - 1;
            $posicaoID     = $listaEdificacoes["seq"];
            if ($_REQUEST["boSel_$posicaoID"]) {
                $listaEdificacoesSessao[$posicaoSessao]['sel'] = 't';
            } else {
                $listaEdificacoesSessao[$posicaoSessao]['sel'] = "f";
            }
        }
        Sessao::write('lsEdificacoes', $listaEdificacoesSessao);
    break;
    case "calculaAreaTotal":
        $flAreaRealOrigem = str_replace( ".", "", $_REQUEST['flAreaRealOrigem'] );
        $flAreaRealOrigem = str_replace( ",", ".", $flAreaRealOrigem            );
        $flAreaResultante = bcdiv( $flAreaRealOrigem , $_REQUEST['inQuantLote'] , 3 );
        $flAreaResultante = round( $flAreaResultante, 2 );
        $flAreaResultante = number_format( $flAreaResultante, 2, ',', '.' );
        if ($_REQUEST["funcionalidade"] == 178) {
            $stUnidadeMedida = "m²";
        } elseif ($_REQUEST["funcionalidade"] == 193) {
            $stUnidadeMedida = "ha";
        }
        $js  = "f.flAreaLote.value = '$flAreaResultante';                      \n";
        $js .= "d.getElementById('flAreaResultante').innerHTML = '$flAreaResultante $stUnidadeMedida';\n";
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "Trecho":
        montaTrecho();
    break;

}
SistemaLegado::LiberaFrames();
?>
