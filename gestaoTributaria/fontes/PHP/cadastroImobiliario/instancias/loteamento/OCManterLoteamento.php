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
    * Página de processamento oculto para o cadastro de loteamento
    * Data de Criação   : 31/08/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho
                             Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterLoteamento.php 61291 2014-12-30 15:55:05Z evandro $

    * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.17  2007/08/27 19:22:29  cercato
Bug#10019#

Revision 1.16  2006/09/18 10:30:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLoteamento.class.php"       );
include_once( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"            );
include_once( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once( CAM_GT_CIM_COMPONENTES."MontaLocalizacaoLoteamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma          = "ManterLoteamento";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgFormVerificaNivel = "FM".$stPrograma."VerificaNivel.php";
$pgFormNivel         = "FM".$stPrograma."Nivel.php";
$pgFormUltimoNivel   = "FM".$stPrograma."UltimoNivel.php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";

include_once( $pgJs );

$obMontaLocalizacao = new MontaLocalizacao();
$obMontaLocalizacao->setCadastroLocalizacao( false );
$obMontaLocalizacaoLoteamento = new MontaLocalizacaoLoteamento();
$obMontaLocalizacaoLoteamento->setCadastroLocalizacao( false );

function montaListaLote($arListaLotes)
{
    GLOBAL $inNumCaucionado;

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
        $obLista->ultimoCabecalho->setWidth    (20);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "Localização"    );
        $obLista->ultimoCabecalho->setWidth    (40);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "Caucionado"     );
        $obLista->ultimoCabecalho->setWidth    (10);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    (2);
        $obLista->commitCabecalho              ();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNumLote" );
        $obLista->ultimoDado->setAlinhamento( "DIREITA" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stLocalizacaoLoteamento" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "boCaucionado" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
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
    $inNumCaucionado = 0;
    $rsListaLotes->setPrimeiroElemento();
    while ( !$rsListaLotes->eof() ) {
        if ( $rsListaLotes->getCampo("boCaucionado") == "Sim" ) {            
            $inNumCaucionado++;
        }
        $rsListaLotes->proximo();
    }
    $stJs .= "d.getElementById('spanLotes').innerHTML = '".$stHTML."';\n";
    $stJs .= "d.getElementById('QtdLotes').innerHTML = '".$inNumCaucionado."';";

    return $stJs;
}

switch ($_REQUEST ["stCtrl"]) {
    case "buscaLocalizacao":
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacao"] );
        $obRCIMLocalizacao->listarLocalizacao( $rsLocalizacao );
        if ( $rsLocalizacao->eof() ) {
            $stJs .= 'f.stChaveLocalizacao.value = "";';
            $stJs .= 'f.stChaveLocalizacao.focus();';
            $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Chave Localização inválida. (".$_POST["stChaveLocalizacao"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "'.$rsLocalizacao->getCampo("nom_localizacao").'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
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
        if ($_REQUEST["inPosicao"] == $_REQUEST["inNumNiveis"]) {
            $obMontaLocalizacao->setCadastroLoteamento( true );
            $obRCIMLote = new RCIMLote;
            $obRCIMConfiguracao = new RCIMConfiguracao;
            $obRCIMConfiguracao->setCodigoModulo( 12 );
            $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obRCIMConfiguracao->consultarConfiguracao();
            $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

            if ( $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] ) {
                $arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
                $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $arCodigoLocalizacao[1] );
                $obRCIMLote->listarLotes( $rsLote );

            } elseif ($_REQUEST["inCodigoLocalizacao"]) {
                $obRCIMLote->setCodigoLote( $_REQUEST["inNumLoteamento"] );
                $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
                $obRCIMLote->listarLotes( $rsLote );
            } else {
                $rsLote = new RecordSet;
            }

            $rsLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

            $js .= "f.inNumLoteamento.options[0] = new Option('Selecione','','selected');\n";
            $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
            $inContador = 1;
            while ( !$rsLote->eof() ) {
                $js .= "f.inNumLoteamento.options[$inContador] = ";
                $js .= "new Option('".$rsLote->getCampo("valor")."','".$rsLote->getCampo("cod_lote")."',''); \n";
                $js .= "f.inNumLote.options[$inContador] = ";
                $js .= "new Option('".$rsLote->getCampo("valor")."','".$rsLote->getCampo("cod_lote")."-".$rsLote->getCampo("valor")."',''); \n";
                $inContador++;
                $rsLote->proximo();
            }
            $js .= $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
            SistemaLegado::executaFrameOculto($js);
        } else {
            $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "carregaLotes1":
        if ( $_REQUEST["inNumLoteamento"] || !$_REQUEST["stChaveLocalizacao"] ) break;
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
        $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
        $obRCIMLote = new RCIMLote;
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
        $obRCIMLote->listarLotes( $rsLote );
        $rsLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

        $js .= "f.inNumLoteamento.options[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsLote->eof() ) {
            $js .= "f.inNumLoteamento.options[$inContador] = ";
            $js .= "new Option('".$rsLote->getCampo("valor")."','".$rsLote->getCampo("cod_lote")."',''); \n";
            $inContador++;
            $rsLote->proximo();
        }
        SistemaLegado::executaFrameOculto($js);
        break;

    case "carregaLotes2":
        if ( $_REQUEST["inNumLote"] || !$_REQUEST["stChaveLocalizacaoLoteamento"] ) break;
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacaoLoteamento"]);
        $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
        $obRCIMLote = new RCIMLote;
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
        $obRCIMLote->listarLotes( $rsLoteLoteamento );
        $rsLoteLoteamento->addStrPad( "valor", strlen( $stMascaraLote ), "0" );
        $js .= "limpaSelect(f.inNumLote,0);\n";
        $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsLoteLoteamento->eof() ) {
            $js .= "f.inNumLote.options[$inContador] = ";
            $js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."',''); \n";
            $js .= "f.inNumLote.options[$inContador] = ";
            $js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."-".$rsLoteLoteamento->getCampo("valor")."',''); \n";
            $inContador++;
            $rsLoteLoteamento->proximo();
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "buscaCGM":
        if ($_POST[ 'inNumCGM' ] != '') {
            $msgAviso = "Pessoa Jurídica";
            $obRCGMPessoaJuridica = new RCGMPessoaJuridica;
            $obRCGMPessoaJuridica->setNumCGM( $_POST[ 'inNumCGM' ] );
            $obRCGMPessoaJuridica->consultarCGM( $rsCGM );
            $inNumLinhas = $rsCGM->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs  = 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@O CGM informado não pertence a uma ".$msgAviso.".(".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs  = 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
        }
    break;
    case "habilitaLotes":

        $obBscLote = new BuscaInner;
        $obBscLote->setRotulo ( "Loteamento de origem" );
        $obBscLote->setTitle  ( "Lote que deu origem ao loteamento" );
        $obBscLote->setId     ( "campoInner" );
        $obBscLote->setFuncaoBusca( "abrePopUp('../popups/cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','juridica','".Sessao::getId()."','800','550')" );

        $obRdnCaucionado = new SimNao;
        $obRdnCaucionado->setRotulo ( "Caucionado"   );
        $obRdnCaucionado->setName   ( "boCaucionado" );
        $obRdnCaucionado->setNull   ( false          );
        $obRdnCaucionado->setChecked( false          );

        $obBtnIncluirLote = new Button;
        $obBtnIncluirLote->setName              ( "btnIncluirLote" );
        $obBtnIncluirLote->setValue             ( "Incluir" );
        $obBtnIncluirLote->setTipo              ( "button" );
        $obBtnIncluirLote->obEvento->setOnClick ( "incluiLote();" );
        $obBtnIncluirLote->setDisabled          ( false );

        $obBtnLimparLote = new Reset;
        $obBtnLimparLote->setName              ( "btnLimparLote" );
        $obBtnLimparLote->setValue             ( "Limpar" );

        $obFormulario = new Formulario;
        $obFormulario->addComponente     ( $obBscLote       );
        $obFormulario->addComponente     ( $obRdnCaucionado );

        $obFormulario->addLinha();
        $obFormulario->ultimaLinha->addCelula();
        $obFormulario->ultimaLinha->ultimaCelula->setColSpan    ( 2 );
        $obFormulario->ultimaLinha->ultimaCelula->setClass      ( "fieldcenter"                   );
        $obFormulario->ultimaLinha->ultimaCelula->addComponente ( $obBtnIncluirLote );
        $obFormulario->ultimaLinha->ultimaCelula->addComponente ( $obBtnLimparLote  );
        $obFormulario->ultimaLinha->commitCelula();
        $obFormulario->commitLinha();

        $obFormulario->montaInnerHTML    ();
        $stHTML = $obFormulario->getHTML ();
        $js .= "d.getElementById('spanLotes').innerHTML = '".$stHTML."';\n";
        SistemaLegado::executaFrameOculto($js);
    break;
    case "excluiLote":
        $inLinha = $_REQUEST["inLinha"] ? $_REQUEST["inLinha"] : 0;
        $arNovaListaLote = array();
        $inContLinha = 0;
        $arLotesSessao = Sessao::read('lotes');
        foreach ($arLotesSessao as $inChave => $arLotes) {
            if ($inChave != $inLinha) {
                $arLotes["inLinha"] = $inContLinha++;
                $arNovaListaLote[] = $arLotes;
            }
        }
        $arLotesSessao = array();
        $arLotesSessao = $arNovaListaLote;
        Sessao::write('lotes', $arLotesSessao);
        $rsListaLote = new RecordSet;
        $stJs = montaListaLote( $arLotesSessao );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "incluiLote":
        $arCodigoLocalizacao = explode('-',$_REQUEST['inCodLocalizacaoLoteamento_'.( $_REQUEST['inNumNiveis'] - 1 ) ]);
        $arNumLote = explode('-',$_REQUEST['inNumLote']);
        $obRCIMLote   = new RCIMLote;
        $rsLoteamento = new RecordSet;
        $obRCIMLote->setCodigoLote( $arNumLote[0] );
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $arCodigoLocalizacao[1] );
        $obRCIMLote->buscarLotes( $rsLotes );
        $stMensagem = "";

        $boErro = false;
        $arLotesSessao = Sessao::read('lotes');
        if ($arLotesSessao) {
            foreach ($arLotesSessao as  $inChave => $arLotes) {
                if ( ( $arLotes["inNumLote"] == $arNumLote[1] ) && ( $arLotes["stLocalizacaoLoteamento"] == $_REQUEST['stChaveLocalizacaoLoteamento'] ) ) {
                    $boErro = true;
                    $stMensagem  = "Lote já informado!";
                    break;
                }
            }
        }

        if ( $rsLotes->getNumLinhas() <= 0 ) {
            $boErro = true;
            $stMensagem = "Campo Lote inválido.";
        }

        if ($arNumLote[0] == "") {
            $boErro = true;
            $stMensagem = "Campo Lote nulo.";
        }

        if ($arNumLote[0] == $_REQUEST['inNumLoteamento']) {
            $boErro = true;
            $stMensagem = "Campo Lote não pode ser igual ao lote de origem.";
        }

        if ($boErro) {
            $stJs = "alertaAviso('".$stMensagem."(".$arNumLote[1].")','form','erro','".Sessao::getId()."', '../');";
        } else {
            if ($_REQUEST['boCaucionado'] == "S") {
                $boTmp = "Sim";
            } elseif ($_REQUEST['boCaucionado'] == "N") {
                $boTmp = "Não";
            }
            $stJs  = "f.inNumLote.selectedIndex = 0;\n";
            $arLote = array( "inNumLote"    => $arNumLote[1],
                             "boCaucionado" => $boTmp,
                             "inCodLote"    => $arNumLote[0],
                             "stLocalizacaoLoteamento" => $_REQUEST['stChaveLocalizacaoLoteamento'] );
            $arLotesSessao     = Sessao::read('lotes');
            $arLote["inLinha"] = count( $arLotesSessao );
            $arLotesSessao[]   = $arLote;
            Sessao::write('lotes', $arLotesSessao);
            $stJs .= montaListaLote( $arLotesSessao );
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "limpaLotes":

        $stJs .= "d.getElementById('spanLotes').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('QtdLotes').innerHTML = '&nbsp;';\n";
        $stJs .= "f.stChaveLocalizacaoLoteamento.value = '';\n";
        $stJs .= "d.getElementById('stNomeChaveLocalizacaoLoteamento').innerHTML = '&nbsp;';\n";

        for ($i=1;$i<=($_REQUEST['inNumNiveis']-1);$i++) {
            $stJs .= "f.inCodLocalizacaoLoteamento_".$i.".selectedIndex = 0;\n";
        }

        $stJs .= montaListaLote( Sessao::read('lotes') );
        SistemaLegado::executaFrameOculto($stJs);

    break;
    case "limpaTudo":
        $arLotesSessao = array();
        Sessao::write('lotes', $arLotesSessao);

        $lista = new RecordSet;
        $stJs .= montaListaLote( $lista );
        SistemaLegado::executaFrameOculto($stJs);

    break;
    case "listaLote":
        $arLotesSessao = Sessao::read('lotes');
        $stJs .= montaListaLote( $arLotesSessao );
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case "buscaProcesso":
        if ($_POST['inNumProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);
            $obRProcesso = new RProcesso;
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inNumProcesso.value = "";';
                $stJs .= 'f.inNumProcesso.focus();';
                $stJs .= "SistemaLegado::alertaAviso('@Processo não encontrado. (".$_POST["inNumProcesso"].")','form','erro','".Sessao::getId()."');";
                SistemaLegado::executaFrameOculto( $stJs );
            }
        }
    break;
    case "buscaLote":
        if ($_POST['inNumLoteamento'] != '') {
            $obRCIMLote = new RCIMLote;
            $obRCIMLote->setNumeroLote( $_REQUEST["inNumLoteamento"] );
            $obRCIMLote->buscarLotes( $rsLotes );

            if ( $rsLotes->getNumLinhas() < 1 ) {
                $stJs .= 'f.inNumLoteamento.value = "";';
                $stJs .= 'f.inNumLoteamento.focus();';
                $stJs .= "SistemaLegado::alertaAviso('@Lote não encontrado. (".$_POST["inNumLoteamento"].")','form','erro','".Sessao::getId()."');";
                SistemaLegado::executaFrameOculto( $stJs );
            }
        }
    break;
    case "preencheProxComboLoteamento":
        $stNomeComboLocalizacao = "inCodLocalizacaoLoteamento_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacaoLoteamento_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacaoLoteamento->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacaoLoteamento->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacaoLoteamento->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacaoLoteamento->setValorReduzido     ( $arChaveLocal[3] );
        if ($_REQUEST["inPosicao"] == $_REQUEST["inNumNiveis"]) {
            $obMontaLocalizacaoLoteamento->setCadastroLoteamento( true );
            $obRCIMLote = new RCIMLote;
            $obRCIMConfiguracao = new RCIMConfiguracao;
            $obRCIMConfiguracao->setCodigoModulo( 12 );
            $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obRCIMConfiguracao->consultarConfiguracao();
            $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

            if ( $_REQUEST[ "inCodLocalizacaoLoteamento_".( $_REQUEST["inNumNiveis"] - 1 ) ] ) {
                $arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacaoLoteamento_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
                $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $arCodigoLocalizacao[1] );
                $obRCIMLote->listarLotes( $rsLoteLoteamento );

            } elseif ($_REQUEST["inCodigoLocalizacaoLoteamento"] || $_REQUEST['stChaveLocalizacaoLoteamento']) {
                $obRCIMLote->setCodigoLote( $_REQUEST["inNumLoteamento"] );
                $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacaoLoteamento"] );
                $obRCIMLote->listarLotes( $rsLoteLoteamento );
            } else {
                $rsLoteLoteamento = new RecordSet;
            }

            $rsLoteLoteamento->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

            $js .= "limpaSelect(f.inNumLote,0);\n";
            $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
            $inContador = 1;
            while ( !$rsLoteLoteamento->eof() ) {
                //$js .= "f.inNumLote.options[$inContador] = ";
                //$js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."',''); \n";
                $js .= "f.inNumLote.options[$inContador] = ";
                $js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."-".$rsLoteLoteamento->getCampo("valor")."',''); \n";
                $inContador++;
                $rsLoteLoteamento->proximo();
            }
            $js .= $obMontaLocalizacaoLoteamento->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
            sistemaLegado::executaFrameOculto($js);
        } else {
            $obMontaLocalizacaoLoteamento->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
    break;
    case "preencheCombosLoteamento":
        $obRCIMLote = new RCIMLote;
        $obMontaLocalizacaoLoteamento->setCadastroLoteamento( true );
        $obRCIMLote->obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacaoLoteamento"] );
        $obRCIMLote->obRCIMLocalizacao->listarLocalizacao( $rsLocalizacao );
        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $rsLocalizacao->getCampo( "cod_localizacao" ) );
        $obRCIMLote->listarLotes( $rsLoteLoteamento );
        $rsLoteLoteamento->addStrPad( "valor", strlen( $stMascaraLote ), "0" );
        $js .= "limpaSelect(f.inNumLote,0);\n";
        $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
        $inContador = 1;
        while ( !$rsLoteLoteamento->eof() ) {
            $js .= "f.inNumLote.options[$inContador] = ";
            $js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."',''); \n";
            $js .= "f.inNumLote.options[$inContador] = ";
            $js .= "new Option('".$rsLoteLoteamento->getCampo("valor")."','".$rsLoteLoteamento->getCampo("cod_lote")."-".$rsLoteLoteamento->getCampo("valor")."',''); \n";
            $inContador++;
            $rsLoteLoteamento->proximo();
        }

        $obMontaLocalizacaoLoteamento->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacaoLoteamento->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacaoLoteamento->setValorReduzido ( $_REQUEST["stChaveLocalizacaoLoteamento"] );
        $js .= $obMontaLocalizacaoLoteamento->preencheCombos();
        sistemaLegado::executaFrameOculto($js);
    break;
}
SistemaLegado::liberaFrames();
?>
