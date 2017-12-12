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
    * Página de Formulário da Caonfiguração do cadastro imobiliario
    * Data de Criação   : 18/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCManterCondominio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"     );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"   );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"            );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"       );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacaoLoteamento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLote.class.php"              );

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
        $obLista->ultimoCabecalho->addConteudo ( "Localização"           );
        $obLista->ultimoCabecalho->setWidth    (20);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "Lote"    );
        $obLista->ultimoCabecalho->setWidth    (20);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "Imóveis"    );
        $obLista->ultimoCabecalho->setWidth    (40);
        $obLista->commitCabecalho              ();
        $obLista->addCabecalho                 ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"         );
        $obLista->ultimoCabecalho->setWidth    (2);
        $obLista->commitCabecalho              ();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stLocalizacaoLoteamento" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNumLote" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inImoveis" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirDado ('excluiLote');" );
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
    $stJs .= "d.getElementById('spanLotes').innerHTML = '".$stHTML."';\n";

    return $stJs;
}

switch ($_REQUEST ["stCtrl"]) {
    case 'buscaLocalizacao':
    Sessao::remove('inNumLote');
    $stJs .= 'f.inNumLote.value = "";';
        if (!$_REQUEST['stChaveLocalizacao']) {
            $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
        } else {
            $obRCIMLocalizacao = new RCIMLocalizacao;
            $obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacao'] );
            $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
            if ( $rsLocalizacao->getNumLinhas() > 0 ) {
                $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
                $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "'.$stDescricao.'";';//&nbsp;";';
                Sessao::write('inNumLote', $_REQUEST['stChaveLocalizacao']);
            } else {
                $stJs .= 'f.stChaveLocalizacao.value = "";';
                $stJs .= 'f.stChaveLocalizacao.focus();';
                $stJs .= 'd.getElementById("stNomeChaveLocalizacao").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Localização inválida. (".$_REQUEST["stChaveLocalizacao"].")', 'form','erro','".Sessao::getId()."');";
            }
        }

//carregar lotes
/*
            include_once( CAM_GT_CIM_COMPONENTES."ITextBoxSelectLote.class.php");

            $obITextBoxSelectLote = new ITextBoxSelectLote();
            $obITextBoxSelectLote->setChaveLocalizacao( $_REQUEST['stChaveLocalizacao'] );
            $obITextBoxSelectLote->setNumLoteamento   ( $_REQUEST['inNumLoteamento'] );
            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obITextBoxSelectLote );
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHtml();

            $stJs .= "d.getElementById('spanCompLotes').innerHTML = '".$stHtml."';";
*/
        SistemaLegado::executaIFrameOculto ( $stJs );
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

            //$js .= "f.inNumLoteamento.options[0] = new Option('Selecione','','selected');\n";
            $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
            $inContador = 1;
            while ( !$rsLote->eof() ) {
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

    case "carregaLotes1":
        if ( $_REQUEST["inNumLote"] ) break;

            $obMontaLocalizacao->setCadastroLoteamento( true );

            $obRCIMConfiguracao = new RCIMConfiguracao;
            $obRCIMConfiguracao->setCodigoModulo( 12 );
            $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
            $obRCIMConfiguracao->consultarConfiguracao();
            $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

            include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
            $obRCIMLocalizacao = new RCIMLocalizacao;
            $obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
            $obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);

            $obRCIMLote = new RCIMLote;
            $obRCIMLote->setCodigoLote( $_REQUEST["inNumLoteamento"] );
            $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
            $obRCIMLote->listarLotes( $rsLote );

            $rsLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

            include_once( CAM_GT_CIM_COMPONENTES."ITextBoxSelectLote.class.php");

            $obITextBoxSelectLote = new ITextBoxSelectLote();
            $obITextBoxSelectLote->setChaveLocalizacao( $_REQUEST['stChaveLocalizacao'] );
            $obITextBoxSelectLote->setNumLoteamento   ( $_REQUEST['inNumLoteamento'] );
            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obITextBoxSelectLote );
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHtml();
            $js .= "d.getElementById('spanCompLotes').innerHTML = '".$stHtml."';";
  //          $js .= "f.inNumLote.options[0] = new Option('Selecione','','selected');\n";
            $inContador = 1;
            while ( !$rsLote->eof() ) {
    //            $js .= "f.inNumLote.options[$inContador] = ";
      //          $js .= "new Option('".$rsLote->getCampo("valor")."','".$rsLote->getCampo("cod_lote")."-".$rsLote->getCampo("valor")."',''); \n";
                $inContador++;
                $rsLote->proximo();
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
                $stJs .= "alertaAviso('@O CGM informado não pertence a uma ".$msgAviso.".(".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs  = 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
        }
    break;
    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();
            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;

    case "visualizarProcesso":
        $obRCIMCondominio   = new RCIMCondominio;

        $arChaveAtributoCondominioProcesso =  array( "cod_condominio" => $_REQUEST["cod_condominio"],"timestamp" => "'$_REQUEST[timestamp]'", "cod_processo" => $_REQUEST["cod_processo"] );
        $obRCIMCondominio->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCondominioProcesso );
        $obRCIMCondominio->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosCondominioProcesso );

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo" );
        $obLblProcesso->setValue     ( str_pad($_REQUEST["cod_processo"],5,"0",STR_PAD_LEFT) . "/" . $_REQUEST["ano_exercicio"]  );

        $obMontaAtributosCondominioProcesso = new MontaAtributos;
        $obMontaAtributosCondominioProcesso->setTitulo     ( "Atributos"        );
        $obMontaAtributosCondominioProcesso->setName       ( "Atributo_"  );
        $obMontaAtributosCondominioProcesso->setLabel       ( true  );
        $obMontaAtributosCondominioProcesso->setRecordSet  ( $rsAtributosCondominioProcesso );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso  );
        $obMontaAtributosCondominioProcesso->geraFormulario ( $obFormularioProcesso );
        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnAtributosProcesso').innerHTML = '".$stHtml."';";
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
        Sessao::write('lotes', $arNovaListaLote);
        $rsListaLote = new RecordSet;
        $stJs = montaListaLote( $arNovaListaLote );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "incluiLote":

        if ($_REQUEST['inNumLote'] != "" and $_REQUEST['stChaveLocalizacao'] != "") {
            $obRCIMLote   = new RCIMLote;
            $obRCIMLote->setNumeroLote( $_REQUEST['inNumLote'] );
            $obRCIMLote->obRCIMLocalizacao->setValorComposto( $_REQUEST['stChaveLocalizacao'] );
            $obRCIMLote->buscarLotes( $rsLotes );
            $rsRecordSet = new RecordSet;
            if ( !$rsLotes->Eof() ) {
                $obTCIMLote   = new TCIMLote;
                $stFiltro = "    WHERE l.cod_lote = ".$rsLotes->getCampo("cod_lote");
                $stOrdem  = " ORDER BY il.inscricao_municipal";
                $obErro = $obTCIMLote->recuperaImoveisLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
                $inCont = 1;
                while (!$rsRecordSet->eof()) {
                    $stImoveisLote .= $rsRecordSet->getCampo("inscricao_municipal"). ", ";
                    $inCont++;
                    $rsRecordSet->proximo();
                }

                $stImoveisLote =  substr( $stImoveisLote, 0, strlen( $stImoveisLote )  - 2 );
                $obRCIMCondominio   = new RCIMCondominio;
                //-------------------- VERIFICA SE LOTE JA FAZ PARTE DE OUTRO CONDOMINIO
                if ($obRCIMCondominio->verificaLotePertenceCondominio( $rsLotes->getCampo("cod_lote"), $_REQUEST['inCodigoCondominio'] ) ) {
                    $boErro = true;
                    $stMensagem = 'Lote já pertencente a outro condomínio';
                } else {
                //-------------------- VERIFICA SE LOTE JA FAZ PARTE DE OUTRO CONDOMINIO FIM
                    $arLotesSessao = Sessao::read('lotes');
                    if ($arLotesSessao) {
                        foreach ($arLotesSessao as  $inChave => $arLotes) {
                            if ( ( $arLotes["inNumLote"] == $_REQUEST['inNumLote'] ) && ( $arLotes["stLocalizacaoLoteamento"] == $_REQUEST['stChaveLocalizacao'] ) ) {
                                $boErro = true;
                                $stMensagem  = "Lote já informado!";
                                $stJs = 'f.inNumLote.focus();';
                            }
                        }
                    }
                }
            }

            if ( $rsLotes->getNumLinhas() <= 0 ) {
                $boErro = true;
                $stMensagem = "Campo Lote inválido.";
            }

            if ($_REQUEST['inNumLote'] == "") {
                $boErro = true;
                $stMensagem = "Campo Lote nulo.";
            }

            if ($_REQUEST['inNumLote'] == $_REQUEST['inNumLoteamento']) {
                $boErro = true;
                $stMensagem = "Campo Lote não pode ser igual ao lote de origem.";
            }
        } else {
            if (!$_REQUEST['stChaveLocalização']) {
                $boErro = true;
                $stMensagem = "Informe a localização!";
            }
            if (!$_REQUEST['inNumLote']) {
                $boErro = true;
                $stMensagem = "Informe o lote!";
            }
        }

        if ($boErro) {
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."', '../');";
        } else {
            $stJs  = "f.inNumLote.selectedIndex = 0;\n";
            $arLote = array( "inNumLote"    => $_REQUEST['inNumLote'],
                             "inCodLote"    => $rsLotes->getCampo('cod_lote'),
                             "inImoveis" => $stImoveisLote,
                             "stLocalizacaoLoteamento" => $_REQUEST['stChaveLocalizacao'] );
            $arLote["inLinha"] = count( $arLotesSessao );
            $arLotesSessao[] = $arLote;

            Sessao::write('lotes', $arLotesSessao);
            $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '&nbsp;';";
            $stJs .= "f.stChaveLocalizacao.value = '';\n";
            $stJs .= "f.inNumLote.value = ''; \n";
            $stJs .= montaListaLote( $arLotesSessao);
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "limpaLotes":

        $stJs  = "f.inNumLote.value='';\n";

        $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '&nbsp;';";
        $stJs .= "f.stChaveLocalizacao.value = '';\n";

        $arLotesSessao = Sessao::read('lotes');
        $stJs .= montaListaLote( $arLotesSessao );
        SistemaLegado::executaFrameOculto($stJs);

    break;
    case "limpaPagina":
        Sessao::remove('lotes');
    break;

    case "listaLote":
        $arLotesSessao = Sessao::read('lotes');
        $stJs .= montaListaLote( $arLotesSessao );
        SistemaLegado::executaFrameOculto( $stJs );
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

    case "PreencheCondominioIntervaloInicial":

        $obRCIMCondominio = new RCIMCondominio;
        if ($_REQUEST["inCodCondominio"]) {
            $obRCIMCondominio->setCodigoCondominio ( $_REQUEST["inCodCondominio"] );
            $obRCIMCondominio->listarCondominios( $rsCondominio, $stFiltro );
            if ( $rsCondominio->getNumLinhas() < 1 ) {
                $stJs = "f.inCodCondominioInicial.value ='';\n";
                $stJs .= "f.inCodCondominioInicial.focus();\n";
                $stJs .= "alertaAviso('@Condomínio informado não existe. (".$_GET["inCodCondominio"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "f.inCodCondominioInicial.value ='';\n";
        }

        echo $stJs;
        exit;
    break;
    case "PreencheCondominioIntervaloFinal":
        $obRCIMCondominio = new RCIMCondominio;
        if ($_REQUEST["inCodCondominio"]) {
            $obRCIMCondominio->setCodigoCondominio ( $_REQUEST["inCodCondominio"] );
            $obRCIMCondominio->listarCondominios( $rsCondominio, $stFiltro );
            if ( $rsCondominio->getNumLinhas() < 1 ) {
                $stJs = "f.inCodCondominioFinal.value ='';\n";
                $stJs .= "f.inCodCondominioFinal.focus();\n";
                $stJs .= "alertaAviso('@Condomínio informado não existe. (".$_GET["inCodCondominio"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "f.inCodCondominioFinal.value ='';\n";
        }

        echo $stJs;
        exit;
    break;

}
SistemaLegado::liberaFrames();
if ($stJs) {
    SistemaLegado::executaFrameOculto($stJs);
}
?>
