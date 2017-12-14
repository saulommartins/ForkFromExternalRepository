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
    * Página de Frame Oculto de LayoutCarne
    * Data de Criação   : 29/09/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRModeloCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRAcaoModeloCarne.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRObservacaoLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVariaveisLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRInformacaoAdicional.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRInformacaoAdicionalLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRObservacaoDebitoLayoutCarne.class.php" );

function montaListaInformacao($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Informações Adicionais" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Descrição" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Ordem" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Largura" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "cod_informacao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "descricao" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "ordem_lista" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "largura" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirInfo();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","cod_informacao" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","ordem_lista" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnInformacao').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaAtributos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Atributos" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Ordem" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Largura" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "codigo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "nom_atributo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "ordem_lista" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "largura" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirVariavel();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","codigo" );
        $obLista->ultimaAcao->addCampo ( "inIndice2","ordem_lista" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaVariaveis').innerHTML = '".$stHTML."';\n";

    return $js;
}

switch ($_REQUEST['stCtrl']) {
    case "incluirModeloArquivo":
        if ($_GET["stModeloArquivo"]) {
            $stFiltro = " AND modelo_carne.nom_modelo = '".$_GET["stModeloArquivo"]."'";

            $obTARRModeloCarne = new TARRModeloCarne;
            $obTARRModeloCarne->recuperaListaModeloCarneLayout( $rsModelos, $stFiltro );
            if ( !$rsModelos->eof() ) {
                $js = "alertaAviso('@Nome do Modelo de Arquivo (".$_GET["stModeloArquivo"].") já cadastrado!','form','erro','".Sessao::getId()."');\n";
                $js .= "f.stModeloArquivo.setfocus();\n";
            } else {
                $arAcoes = array( 962, 963, 980, 964, 1677, 1678, 978, 979, 1672, 2240, 1755, 1648, 1849 );
                $obTARRAcaoModeloCarne = new TARRAcaoModeloCarne;

                Sessao::setTrataExcecao( true );
                Sessao::getTransacao()->setMapeamento( $obTARRModeloCarne );
                    $obTARRModeloCarne->proximoCod( $inCodModelo );

                    $obTARRModeloCarne->setDado( "cod_modelo", $inCodModelo );
                    $obTARRModeloCarne->setDado( "nom_modelo", $_GET["stModeloArquivo"] );
                    $obTARRModeloCarne->setDado( "nom_arquivo", "RCarneDiversosLayoutUrbem.class.php" );
                    $obTARRModeloCarne->setDado( "capa_primeira_folha", true );
                    $obTARRModeloCarne->inclusao();

                    for ( $inX=0; $inX<count($arAcoes); $inX++ ) {
                        $obTARRAcaoModeloCarne->setDado( "cod_modelo", $inCodModelo );
                        $obTARRAcaoModeloCarne->setDado( "cod_acao", $arAcoes[$inX] );
                        $obTARRAcaoModeloCarne->inclusao();
                    }
                Sessao::encerraExcecao();

                unset( $rsModelos );
                $obTARRModeloCarne->recuperaListaModeloCarneLayout( $rsModelos );
                $js = "f.stModeloArquivo.value = '';\n";
                $js .= "limpaSelect(f.cmbModeloArquivo,1); \n";
                $js .= "f.cmbModeloArquivo[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsModelos->Eof() ) {
                    $js .= "f.cmbModeloArquivo.options[$inContador] = new Option('".$rsModelos->getCampo("nom_modelo")."','".$rsModelos->getCampo("cod_modelo")."'); \n";
                    $rsModelos->proximo();
                    $inContador++;
                }
            }

            echo $js;
        }
        break;

    case "carregaModelo":
        if ($_GET["inCodModelo"]) {
            $arTipo = array( array( "cod_tipo" => 1, "nom_tipo" => "Imobiliário", "cod_modulo" => 12 ), array( "cod_tipo" => 2, "nom_tipo" => "Econômico", "cod_modulo" => 14 ) );

            $rsModulos = new RecordSet;
            $rsModulos->preenche( $arTipo );

            $stFiltro = " and cod_modelo = ".$_GET["inCodModelo"];
            $obTARRModeloCarne = new TARRModeloCarne;
            $obTARRModeloCarne->recuperaListaModeloCarneLayout( $rsModelos, $stFiltro );

            if ( $rsModelos->getCampo('capa_primeira_folha') == 't' ) {
                $js = "f.stCapaUnica[0].checked = true;\n";
                $js .= "f.stCapaUnica[1].checked = false;\n";
            } else {
                $js = "f.stCapaUnica[0].checked = false;\n";
                $js .= "f.stCapaUnica[1].checked = true;\n";
            }

            $js .= "limpaSelect( f.cmbModulos, 1 ); \n";
            $js .= "f.cmbModulos[0] = new Option('Selecione','', 'selected');\n";
            $inContador = 1;
            $inCodTipo = 0;
            while ( !$rsModulos->Eof() ) {
                if ( $rsModelos->getCampo("cod_modulo") == $rsModulos->getCampo("cod_modulo") ) {
                    $inCodTipo = $rsModulos->getCampo("cod_tipo");
                    $js .= "f.cmbModulos.options[$inContador] = new Option('".$rsModulos->getCampo("nom_tipo")."','".$rsModulos->getCampo("cod_tipo")."', 'selected'); \n";
                } else {
                    $js .= "f.cmbModulos.options[$inContador] = new Option('".$rsModulos->getCampo("nom_tipo")."','".$rsModulos->getCampo("cod_tipo")."'); \n";
                }

                $rsModulos->proximo();
                $inContador++;
            }

            if ($inCodTipo) {
                $obTARRInformacaoAdicional = new TARRInformacaoAdicional;
                $obTARRInformacaoAdicional->recuperaTodos( $rsInformacao );

                $obCmbInformacao = new Select;
                $obCmbInformacao->setName         ( "cmbInfo" );
                $obCmbInformacao->addOption       ( "", "Selecione" );
                $obCmbInformacao->setRotulo       ( "Informação Adicional" );
                $obCmbInformacao->setTitle        ( "Informação adicional para apresentar no carne" );
                $obCmbInformacao->setCampoId      ( "cod_informacao" );
                $obCmbInformacao->setCampoDesc    ( "descricao" );
                $obCmbInformacao->preencheCombo   ( $rsInformacao );
                $obCmbInformacao->setNull         ( false );
                $obCmbInformacao->setStyle        ( "width: 220px" );

                $obSpnInformacao = new Span;
                $obSpnInformacao->setId ( "spnInformacao" );

                $stOrdem = " ORDER BY cod_cadastro ASC ";
                if ($inCodTipo == 1) {
                    $stFiltro = " WHERE cod_modulo = 12 and cod_cadastro in ( 2, 3, 4, 5 ) ";
                }else
                    if ($inCodTipo == 2) {
                        $stFiltro = " WHERE cod_modulo = 14 and cod_cadastro in ( 1, 2, 3, 5 ) ";
                    }

                $obTARRModeloCarne = new TARRModeloCarne;
                $obTARRModeloCarne->recuperaAtributosDinamicos( $rsVariaveis, $stFiltro, $stOrdem );

                $obCmbVariaveis = new Select;
                $obCmbVariaveis->setName         ( "cmbVariaveis" );
                $obCmbVariaveis->addOption       ( "", "Selecione" );
                $obCmbVariaveis->setRotulo       ( "Atributos" );
                $obCmbVariaveis->setTitle        ( "Atributos para apresentar no carne" );
                $obCmbVariaveis->setCampoId      ( "[cod_modulo]-[cod_cadastro]-[cod_atributo]" );
                $obCmbVariaveis->setCampoDesc    ( "nom_atributo" );
                $obCmbVariaveis->preencheCombo   ( $rsVariaveis );
                $obCmbVariaveis->setNull         ( false );
                $obCmbVariaveis->setStyle        ( "width: 220px" );

                $arDadosOrdem = array();
                $arDadosOrdem[0]["num_ordem"] = 1;
                $arDadosOrdem[1]["num_ordem"] = 2;
                $arDadosOrdem[2]["num_ordem"] = 3;

                $rsOrdem = new RecordSet;
                $rsOrdem->preenche( $arDadosOrdem );

                $obCmbOrdem = new Select;
                $obCmbOrdem->setName         ( "cmbOrdem" );
                $obCmbOrdem->addOption       ( "", "Selecione" );
                $obCmbOrdem->setRotulo       ( "Ordem de Listagem" );
                $obCmbOrdem->setTitle        ( "Ordem de listagem no carne" );
                $obCmbOrdem->setCampoId      ( "num_ordem" );
                $obCmbOrdem->setCampoDesc    ( "num_ordem" );
                $obCmbOrdem->preencheCombo   ( $rsOrdem );
                $obCmbOrdem->setNull         ( false );
                $obCmbOrdem->setStyle        ( "width: 220px" );

                $obCmbOrdemInfo = new Select;
                $obCmbOrdemInfo->setName         ( "cmbOrdemInfo" );
                $obCmbOrdemInfo->addOption       ( "", "Selecione" );
                $obCmbOrdemInfo->setRotulo       ( "Ordem de Listagem" );
                $obCmbOrdemInfo->setTitle        ( "Ordem de listagem no carne" );
                $obCmbOrdemInfo->setCampoId      ( "num_ordem" );
                $obCmbOrdemInfo->setCampoDesc    ( "num_ordem" );
                $obCmbOrdemInfo->preencheCombo   ( $rsOrdem );
                $obCmbOrdemInfo->setNull         ( false );
                $obCmbOrdemInfo->setStyle        ( "width: 220px" );

                $obBtnIncluirInfo = new Button;
                $obBtnIncluirInfo->setName( "stIncluirInfo" );
                $obBtnIncluirInfo->setValue( "Incluir" );
                $obBtnIncluirInfo->obEvento->setOnClick( "montaParametrosGET('incluirInfo', 'cmbInfo,cmbOrdemInfo', true);" );

                $obBtnLimparInfo= new Button;
                $obBtnLimparInfo->setName( "stLimparInfo" );
                $obBtnLimparInfo->setValue( "Limpar" );
                $stOnChange = "ajaxJavaScript('OCManterLayoutCarne.php?".Sessao::getId()."&inCodTipo='+document.frm.cmbModulos.value+'&stObservacaoCapa='+document.frm.stObservacaoCapa.value+'&stObservacaoCorpo='+document.frm.stObservacaoCorpo.value,'limparInfo');";
                $obBtnLimparInfo->obEvento->setOnClick( $stOnChange );

                $obBtnIncluirVariavel = new Button;
                $obBtnIncluirVariavel->setName( "stIncluirVariavel" );
                $obBtnIncluirVariavel->setValue( "Incluir" );
                $obBtnIncluirVariavel->obEvento->setOnClick( "montaParametrosGET('incluirVariavel', 'cmbVariaveis,cmbOrdem', true);" );

                $obBtnLimparVariavel= new Button;
                $obBtnLimparVariavel->setName( "stLimparVariavel" );
                $obBtnLimparVariavel->setValue( "Limpar" );
                $stOnChange = "ajaxJavaScript('OCManterLayoutCarne.php?".Sessao::getId()."&inCodTipo='+document.frm.cmbModulos.value+'&stObservacaoCapa='+document.frm.stObservacaoCapa.value+'&stObservacaoCorpo='+document.frm.stObservacaoCorpo.value,'limparVariavel');";
                $obBtnLimparVariavel->obEvento->setOnClick( $stOnChange );

                $obTARRObservacaoLayoutCarne = new TARRObservacaoLayoutCarne;
                $stFiltro = " WHERE cod_modelo = ".$_GET["inCodModelo"];
                $obTARRObservacaoLayoutCarne->recuperaTodos( $rsObservacoes, $stFiltro );
                $stObsCapa = $stObsCorpo = "";
                while ( !$rsObservacoes->Eof() ) {
                    if ( $rsObservacoes->getCampo( "capa" ) == 't' ) {
                        $stObsCapa = $rsObservacoes->getCampo( "observacao" );
                    } else {
                        $stObsCorpo = $rsObservacoes->getCampo( "observacao" );
                    }

                    $rsObservacoes->proximo();
                }

                $obTxtObservacaoCapa = new TextArea;
                $obTxtObservacaoCapa->setName ( "stObservacaoCapa" );
                $obTxtObservacaoCapa->setRotulo ( "Observações para Capa do Carne" );
                $obTxtObservacaoCapa->setTitle ( "Observações para o contribuinte." );
                $obTxtObservacaoCapa->setNull  ( true );
                $obTxtObservacaoCapa->setCols ( 30 );
                $obTxtObservacaoCapa->setRows ( 5 );
                $obTxtObservacaoCapa->setMaxCaracteres(300);
                $obTxtObservacaoCapa->setValue ( $stObsCapa );
                $obTxtObservacaoCapa->setStyle ( "width: 100%" );

                $obTxtObservacaoCorpo = new TextArea;
                $obTxtObservacaoCorpo->setName ( "stObservacaoCorpo" );
                $obTxtObservacaoCorpo->setRotulo ( "Observações para Carne" );
                $obTxtObservacaoCorpo->setTitle ( "Observações para o contribuinte." );
                $obTxtObservacaoCorpo->setNull  ( true );
                $obTxtObservacaoCorpo->setCols ( 30 );
                $obTxtObservacaoCorpo->setRows ( 5 );
                $obTxtObservacaoCorpo->setMaxCaracteres(300);
                $obTxtObservacaoCorpo->setValue ( $stObsCorpo );
                $obTxtObservacaoCorpo->setStyle ( "width: 100%" );

                $stFiltro = " WHERE cod_modelo = ".$_GET["inCodModelo"];
                $obTARRObservacaoDebitoLayoutCarne = new TARRObservacaoDebitoLayoutCarne;
                $obTARRObservacaoDebitoLayoutCarne->recuperaTodos( $rsObsDeb, $stFiltro );

                if ( !$rsObsDeb->Eof() ) {
                    $stObDev =$rsObsDeb->getCampo("observacao_devedor");
                    $stObNDev = $rsObsDeb->getCampo("observacao_nao_devedor");
                    $boMgsDeb = true;
                } else {
                    $boMgsDeb = false;
                }

                $obRdbMsgCondArrecSim = new Radio;
                $obRdbMsgCondArrecSim->setRotulo     ( "Observação de Débitos" );
                $obRdbMsgCondArrecSim->setName       ( "stMsgArrecadacao" );
                $obRdbMsgCondArrecSim->setLabel      ( "Utilizar" );
                $obRdbMsgCondArrecSim->setValue      ( true );
                $obRdbMsgCondArrecSim->setTitle      ( "Apresenta uma observação conforme a condição da inscrição na arrecadação." );
                $obRdbMsgCondArrecSim->setNull       ( false );
                $obRdbMsgCondArrecSim->setChecked    ( $boMgsDeb );
                $pgOcul = "OCManterLayoutCarne.php?".Sessao::getId();
                $stOnChange = "ajaxJavaScript('".$pgOcul."&stMsgArrecadacao='+this.value,'carregaObservacaoDebitos');";
                $obRdbMsgCondArrecSim->obEvento->setOnChange( $stOnChange );

                $obRdbMsgCondArrecNao = new Radio;
                $obRdbMsgCondArrecNao->setRotulo   ( "Observação de Débitos" );
                $obRdbMsgCondArrecNao->setName     ( "stMsgArrecadacao" );
                $obRdbMsgCondArrecNao->setLabel    ( "Desativado" );
                $obRdbMsgCondArrecNao->setTitle    ( "Apresenta uma observação conforme a condição da inscrição na arrecadação." );
                $obRdbMsgCondArrecNao->setValue    ( false );
                $obRdbMsgCondArrecNao->setNull     ( false );
                $obRdbMsgCondArrecNao->setChecked  ( !$boMgsDeb );
                $obRdbMsgCondArrecNao->obEvento->setOnChange( $stOnChange );

                $obSpnObsDevedor = new Span;
                $obSpnObsDevedor->setId ( "spnObsDevedor" );

                //caso inicie ja exista usar
                if ($boMgsDeb) {
                    $obTxtObservacaoDevedor = new TextArea;
                    $obTxtObservacaoDevedor->setName ( "stObservacaoDevedor" );
                    $obTxtObservacaoDevedor->setRotulo ( "Observação Devedor" );
                    $obTxtObservacaoDevedor->setTitle ( "Observação para o contribuinte devedor." );
                    $obTxtObservacaoDevedor->setNull  ( true );
                    $obTxtObservacaoDevedor->setCols ( 50 );
                    $obTxtObservacaoDevedor->setRows ( 2 );
                    $obTxtObservacaoDevedor->setMaxCaracteres(100);
                    $obTxtObservacaoDevedor->setValue ( $stObDev );
                    $obTxtObservacaoDevedor->setStyle ( "width: 100%" );

                    $obTxtObservacaoNDevedor = new TextArea;
                    $obTxtObservacaoNDevedor->setName ( "stObservacaoNDevedor" );
                    $obTxtObservacaoNDevedor->setRotulo ( "Observação Não Devedor" );
                    $obTxtObservacaoNDevedor->setTitle ( "Observação para o contribuinte não devedor." );
                    $obTxtObservacaoNDevedor->setNull  ( true );
                    $obTxtObservacaoNDevedor->setCols ( 50 );
                    $obTxtObservacaoNDevedor->setRows ( 2 );
                    $obTxtObservacaoNDevedor->setMaxCaracteres(100);
                    $obTxtObservacaoNDevedor->setValue ( $stObNDev );
                    $obTxtObservacaoNDevedor->setStyle ( "width: 100%" );

                    $obFormObsDev = new Formulario;
                    $obFormObsDev->addComponente ( $obTxtObservacaoDevedor );
                    $obFormObsDev->addComponente ( $obTxtObservacaoNDevedor );
                    $obFormObsDev->montaInnerHTML();
                    $obSpnObsDevedor->setValue ( $obFormObsDev->getHTML() );
                }
                //---------------

                $obFormulario = new Formulario;
                if ( $_GET["inCodTipo"] == 1 )
                    $obFormulario->addTitulo ( "Módulo Imobiliário" );
                else
                    $obFormulario->addTitulo ( "Módulo Econômico" );

                $obFormulario->addComponente ( $obTxtObservacaoCapa );
                $obFormulario->addComponente ( $obTxtObservacaoCorpo );
                $obFormulario->agrupaComponentes ( array( $obRdbMsgCondArrecSim, $obRdbMsgCondArrecNao ) );
                $obFormulario->addSpan       ( $obSpnObsDevedor );
                $obFormulario->addComponente ( $obCmbInformacao );
                $obFormulario->addComponente ( $obCmbOrdemInfo );
                $obFormulario->defineBarra   ( array( $obBtnIncluirInfo, $obBtnLimparInfo ),"","" );
                $obFormulario->addSpan       ( $obSpnInformacao );
                $obFormulario->addComponente ( $obCmbVariaveis );
                $obFormulario->addComponente ( $obCmbOrdem );
                $obFormulario->defineBarra ( array( $obBtnIncluirVariavel, $obBtnLimparVariavel ),"","" );
                $obFormulario->montaInnerHTML();
                $js .= "d.getElementById('spnModulo').innerHTML = '".$obFormulario->getHTML()."';\n";

                $obTARRInformacaoAdicionalLayoutCarne = new TARRInformacaoAdicionalLayoutCarne;
                $obTARRInformacaoAdicionalLayoutCarne->recuperaInfoLayout( $rsInfo, $stFiltro, " ORDER BY ordem, posicao_inicial ASC " );

                $inOrdem = 0;
                $arLayoutInfo = array();
                while ( !$rsInfo->Eof() ) {
                    if ( $inOrdem != $rsInfo->getCampo("ordem") ) {
                        $inOrdem = $rsInfo->getCampo("ordem");
                        $inTotalEncontrado = 1;
                    }else
                        $inTotalEncontrado++;

                    $arLayoutInfo[] = array (
                        "largura" => $rsInfo->getCampo("largura"),
                        "ordem" => $rsInfo->getCampo("ordem"),
                        "ordem_lista" => $rsInfo->getCampo("ordem").".".$inTotalEncontrado,
                        "descricao" => $rsInfo->getCampo( "descricao" ),
                        "cod_informacao" => $rsInfo->getCampo( "cod_informacao" )
                    );

                    $rsInfo->proximo();
                }

                Sessao::write( "layoutInfo", $arLayoutInfo );

                $rsListaInfo = new RecordSet;
                $rsListaInfo->preenche( $arLayoutInfo );
                $rsListaInfo->ordena( "ordem_lista", "ASC", SORT_STRING );

                $js .= montaListaInformacao( $rsListaInfo );

                $obTARRVariaveisLayoutCarne = new TARRVariaveisLayoutCarne;
                $obTARRVariaveisLayoutCarne->recuperaVariaveisLayout( $rsVariaveis, $stFiltro, " ORDER BY ordem, posicao_inicial ASC " ); //utilizando mesmo filtro das observações

                $inOrdem = 0;
                $arLayoutVariaveis = array();
                while ( !$rsVariaveis->Eof() ) {
                    if ( $inOrdem != $rsVariaveis->getCampo("ordem") ) {
                        $inOrdem = $rsVariaveis->getCampo("ordem");
                        $inTotalEncontrado = 1;
                    }else
                        $inTotalEncontrado++;

                    $arLayoutVariaveis[] = array (
                        "largura" => $rsVariaveis->getCampo("largura"),
                        "ordem" => $rsVariaveis->getCampo("ordem"),
                        "ordem_lista" => $rsVariaveis->getCampo("ordem").".".$inTotalEncontrado,
                        "nom_atributo" => $rsVariaveis->getCampo( "nom_atributo" ),
                        "cod_modulo" => $rsVariaveis->getCampo("cod_modulo"),
                        "cod_cadastro" => $rsVariaveis->getCampo("cod_cadastro"),
                        "cod_atributo" => $rsVariaveis->getCampo("cod_atributo"),
                        "codigo" => $rsVariaveis->getCampo("cod_modulo")."-".$rsVariaveis->getCampo("cod_cadastro")."-".$rsVariaveis->getCampo("cod_atributo")
                    );

                    $rsVariaveis->proximo();
                }

                Sessao::write( "layoutVariaveis", $arLayoutVariaveis );

                $rsListaVariaveis = new RecordSet;
                $rsListaVariaveis->preenche( $arLayoutVariaveis );
                $rsListaVariaveis->ordena( "ordem_lista", "ASC", SORT_STRING );

                $js .= montaListaAtributos( $rsListaVariaveis );
            } else {
                Sessao::write( "layoutInfo", array() );

                $rsListaVariaveis = new RecordSet;
                Sessao::write( "layoutVariaveis", array() );
                $js .= "d.getElementById('spnModulo').innerHTML = '&nbsp;';\n";
                $js .= montaListaAtributos( $rsListaVariaveis );
            }

            echo $js;
        }
        break;

    case "excluirInfo":
        if ($_REQUEST["inIndice1"] && $_REQUEST["inIndice2"]) {
            $arLayoutInfo = Sessao::read( "layoutInfo" );
            $inTotalInfo = count( $arLayoutInfo );
            $arTMPInfo = array();
            $arOrdem = explode( ".", $_REQUEST["inIndice2"] );
            for ($inX=0; $inX<$inTotalInfo; $inX++) {
                if ($_REQUEST["inIndice1"] != $arLayoutInfo[$inX]["cod_informacao"]) {
                    if ($arOrdem[0] == $arLayoutInfo[$inX]["ordem"]) { //ordem sem posicao
                        $arTMPOrdem = explode ( ".", $arLayoutInfo[$inX]["ordem_lista"] );
                        if ($arTMPOrdem[1] > $arOrdem[1]) {
                            $arTMPInfo[] = array (
                                "largura" => $arLayoutInfo[$inX]["largura"],
                                "ordem" => $arLayoutInfo[$inX]["ordem"],
                                "ordem_lista" => $arLayoutInfo[$inX]["ordem"].".".($arTMPOrdem[1]-1),
                                "descricao" => $arLayoutInfo[$inX][ "descricao" ],
                                "cod_informacao" => $arLayoutInfo[$inX][ "cod_informacao" ]
                            );
                        } else {
                            $arTMPInfo[] = $arLayoutInfo[$inX];
                        }
                    } else {
                        $arTMPInfo[] = $arLayoutInfo[$inX];
                    }
                }
            }

            unset( $arLayoutInfo );
            Sessao::write( "layoutInfo", $arTMPInfo );

            $rsListaInfo = new RecordSet;
            $rsListaInfo->preenche( $arTMPInfo );
            $rsListaInfo->ordena( "ordem_lista", "ASC", SORT_STRING );

            $js = montaListaInformacao( $rsListaInfo );
            sistemaLegado::executaFrameOculto( $js );
        }
        break;

    case "excluirVariavel":
        if ($_REQUEST["inIndice1"] && $_REQUEST["inIndice2"]) {
            // ( "inIndice1","codigo" );
            // ( "inIndice2","ordem_lista" );
            $arDadosOrdem = explode( "-", $_REQUEST["inIndice2"] );
            $arLayoutVariaveis = Sessao::read( "layoutVariaveis" );
            $inTotalVariaveis = count( $arLayoutVariaveis );
            $arTMPVariaveis = array();
            $arOrdem = explode( ".", $_REQUEST["inIndice2"] );
            for ($inX=0; $inX<$inTotalVariaveis; $inX++) {
                if ($_REQUEST["inIndice2"] != $arLayoutVariaveis[$inX]["ordem_lista"]) {
                    if ($arOrdem[0] == $arLayoutVariaveis[$inX]["ordem"]) { //ordem sem posicao
                        $arTMPOrdem = explode ( ".", $arLayoutVariaveis[$inX]["ordem_lista"] );
                        if ($arTMPOrdem[1] > $arOrdem[1]) {
                            $arTMPVariaveis[] = array(
                                "largura" => $arLayoutVariaveis[$inX]["largura"],
                                "ordem" => $arLayoutVariaveis[$inX]["ordem"],
                                "ordem_lista" => $arLayoutVariaveis[$inX]["ordem"].".".($arTMPOrdem[1]-1),
                                "nom_atributo" => $arLayoutVariaveis[$inX][ "nom_atributo" ],
                                "cod_modulo" => $arLayoutVariaveis[$inX][ "cod_modulo" ],
                                "cod_cadastro" => $arLayoutVariaveis[$inX][ "cod_cadastro" ],
                                "cod_atributo" => $arLayoutVariaveis[$inX][ "cod_atributo" ],
                                "codigo" => $arLayoutVariaveis[$inX][ "codigo" ]
                            );
                        } else {
                            $arTMPVariaveis[] = $arLayoutVariaveis[$inX];
                        }
                    } else {
                        $arTMPVariaveis[] = $arLayoutVariaveis[$inX];
                    }
                }
            }

            unset( $arLayoutVariaveis );
            Sessao::write( "layoutVariaveis", $arTMPVariaveis );

            $rsListaVariaveis = new RecordSet;
            $rsListaVariaveis->preenche( $arTMPVariaveis );
            $rsListaVariaveis->ordena( "ordem_lista", "ASC", SORT_STRING );

            $js = montaListaAtributos( $rsListaVariaveis );
            sistemaLegado::executaFrameOculto( $js );
        }
        break;

    case "incluirInfo":
        if (!$_GET["cmbInfo"]) {
            $js = "alertaAviso('@Campo Informação Adicional não foi selecionado!','form','erro','".Sessao::getId()."');\n";
            echo $js;
            exit;
        }else
            if (!$_GET["cmbOrdemInfo"]) {
                $js = "alertaAviso('@Campo Ordem não foi selecionado!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }

        $arLayoutInfo = Sessao::read( "layoutInfo" );
        $inTotalInfo = count( $arLayoutInfo );
        for ($inX=0; $inX<$inTotalInfo; $inX++) {
            if ($_GET["cmbInfo"] == $arLayoutInfo[$inX]["cod_informacao"]) {
                $js = "alertaAviso('@Informação Adicional selecionada já está na lista!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }
        }

        $stFiltro = " WHERE cod_informacao = ".$_GET["cmbInfo"];
        $obTARRInformacaoAdicional = new TARRInformacaoAdicional;
        $obTARRInformacaoAdicional->recuperaTodos( $rsInformacao, $stFiltro );

        if ( strlen($rsInformacao->getCampo("descricao")) > $rsInformacao->getCampo("largura") ) {
            $inLarguraDoTexto = strlen($rsInformacao->getCampo("descricao"))*2; //falta adicionar campo largura na tabela InformacaoAdicional
        } else {
            $inLarguraDoTexto = $rsInformacao->getCampo("largura");
        }

        $inLarguraMaxima = 160;

        $inTotalUsado = 0;
        $inTotalEncontrado = 1;
        for ($inX=0; $inX<$inTotalInfo; $inX++) {
            if ($_GET["cmbOrdemInfo"] == $arLayoutInfo[$inX]["ordem"]) {
                $inTotalUsado += $arLayoutInfo[$inX]["largura"];
                $inTotalEncontrado++;
            }
        }

        if ($inTotalUsado) {
            if ( ( $inTotalUsado + $inLarguraDoTexto ) > $inLarguraMaxima ) {
                $js = "alertaAviso('@Informação selecionada possuí largura (".$inLarguraDoTexto.") maior que a disponível (".($inLarguraMaxima-$inTotalUsado).")!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }
        }

        $arLayoutInfo[] = array (
            "largura" => $inLarguraDoTexto,
            "ordem" => $_GET["cmbOrdemInfo"],
            "ordem_lista" => $_GET["cmbOrdemInfo"].".".$inTotalEncontrado,
            "descricao" => $rsInformacao->getCampo( "descricao" ),
            "cod_informacao" => $_GET["cmbInfo"]
        );

        Sessao::write( "layoutInfo", $arLayoutInfo );

        $rsListaInfo = new RecordSet;
        $rsListaInfo->preenche( $arLayoutInfo );
        $rsListaInfo->ordena( "ordem_lista", "ASC", SORT_STRING );

        $js = montaListaInformacao( $rsListaInfo );
        $js .= "f.cmbOrdemInfo.value = '';";
        $js .= "f.cmbInfo.value = '';";
        echo $js;
        break;

    case "incluirVariavel":
        if (!$_GET["cmbVariaveis"]) {
            $js = "alertaAviso('@Campo Atributos não foi selecionado!','form','erro','".Sessao::getId()."');\n";
            echo $js;
            exit;
        }else
            if (!$_GET["cmbOrdem"]) {
                $js = "alertaAviso('@Campo Ordem não foi selecionado!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }

        $arLayoutVariaveis = Sessao::read( "layoutVariaveis" );
        $inTotalVariaveis = count( $arLayoutVariaveis );
        for ($inX=0; $inX<$inTotalVariaveis; $inX++) {
            if ($_GET["cmbVariaveis"] == $arLayoutVariaveis[$inX]["codigo"]) {
                $js = "alertaAviso('@Atributo selecionado já está na lista!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }
        }

        $arVariavel = explode( "-", $_GET["cmbVariaveis"] );

        $obTARRModeloCarne = new TARRModeloCarne;
        $stFiltro = " WHERE atributo_dinamico.cod_modulo = ".$arVariavel[0]." AND atributo_dinamico.cod_cadastro = ".$arVariavel[1]." AND atributo_dinamico.cod_atributo = ".$arVariavel[2];
        $obTARRModeloCarne->recuperaAtributosDinamicos( $rsVariaveis, $stFiltro );

        $inLarguraDoTexto = 0;
        $inLarguraMaxima = 160;
        if ($arVariavel[0] == 12) { //imob
            $obTARRModeloCarne->recuperaLarguraAtributosDinamicosImovel( $rsLarguraAtributo, $stFiltro );
        }else
            if ($arVariavel[0] == 14) { //eco
                $obTARRModeloCarne->recuperaLarguraAtributosDinamicosEmpresa( $rsLarguraAtributo, $stFiltro );
            }

        if ( !$rsLarguraAtributo->Eof() ) {
            $inLarguraDoTexto = $rsLarguraAtributo->getCampo("largura");
        }

        $inTotalUsado = 0;
        $inTotalEncontrado = 1;
        for ($inX=0; $inX<$inTotalVariaveis; $inX++) {
            if ($_GET["cmbOrdem"] == $arLayoutVariaveis[$inX]["ordem"]) {
                $inTotalUsado += $arLayoutVariaveis[$inX]["largura"];
                $inTotalEncontrado++;
            }
        }

        $inLarguraDoTexto = number_format( $inLarguraDoTexto, 0, '', '' );

        if ($inTotalUsado) {
            if ( ( $inTotalUsado + $inLarguraDoTexto ) > $inLarguraMaxima ) {
                $js = "alertaAviso('@Atributo selecionado possuí largura (".$inLarguraDoTexto.") maior que a disponível (".($inLarguraMaxima-$inTotalUsado).")!','form','erro','".Sessao::getId()."');\n";
                echo $js;
                exit;
            }
        }

        $arLayoutVariaveis[] = array (
            "largura" => $inLarguraDoTexto,
            "ordem" => $_GET["cmbOrdem"],
            "ordem_lista" => $_GET["cmbOrdem"].".".$inTotalEncontrado,
            "nom_atributo" => $rsVariaveis->getCampo( "nom_atributo" ),
            "cod_modulo" => $arVariavel[0],
            "cod_cadastro" => $arVariavel[1],
            "cod_atributo" => $arVariavel[2],
            "codigo" => $_GET["cmbVariaveis"]
        );

        Sessao::write( "layoutVariaveis", $arLayoutVariaveis );

        $rsListaVariaveis = new RecordSet;
        $rsListaVariaveis->preenche( $arLayoutVariaveis );
        $rsListaVariaveis->ordena( "ordem_lista", "ASC", SORT_STRING );

        $js = montaListaAtributos( $rsListaVariaveis );
        $js .= "f.cmbOrdem.value = '';";
        $js .= "f.cmbVariaveis.value = '';";
        echo $js;
        break;

    case "limparInfo":
        $js = "f.cmbOrdemInfo.value = '';";
        $js .= "f.cmbInfo.value = '';";
        echo $js;
        break;

    case "limparVariavel":
        $js = "f.cmbOrdem.value = '';";
        $js .= "f.cmbVariaveis.value = '';";
        echo $js;
        break;

    case "carregaObservacaoDebitos":
        if ($_GET["stMsgArrecadacao"]) {
            $obTxtObservacaoDevedor = new TextArea;
            $obTxtObservacaoDevedor->setName ( "stObservacaoDevedor" );
            $obTxtObservacaoDevedor->setRotulo ( "Observação Devedor" );
            $obTxtObservacaoDevedor->setTitle ( "Observação para o contribuinte devedor." );
            $obTxtObservacaoDevedor->setNull  ( true );
            $obTxtObservacaoDevedor->setCols ( 50 );
            $obTxtObservacaoDevedor->setRows ( 2 );
            $obTxtObservacaoDevedor->setMaxCaracteres(100);
            $obTxtObservacaoDevedor->setValue ( $_GET["stObservacaoDevedor"] );
            $obTxtObservacaoDevedor->setStyle ( "width: 100%" );

            $obTxtObservacaoNDevedor = new TextArea;
            $obTxtObservacaoNDevedor->setName ( "stObservacaoNDevedor" );
            $obTxtObservacaoNDevedor->setRotulo ( "Observação Não Devedor" );
            $obTxtObservacaoNDevedor->setTitle ( "Observação para o contribuinte não devedor." );
            $obTxtObservacaoNDevedor->setNull  ( true );
            $obTxtObservacaoNDevedor->setCols ( 50 );
            $obTxtObservacaoNDevedor->setRows ( 2 );
            $obTxtObservacaoNDevedor->setMaxCaracteres(100);
            $obTxtObservacaoNDevedor->setValue ( $_GET["stObservacaoNDevedor"] );
            $obTxtObservacaoNDevedor->setStyle ( "width: 100%" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obTxtObservacaoDevedor );
            $obFormulario->addComponente ( $obTxtObservacaoNDevedor );
            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnObsDevedor').innerHTML = '".$obFormulario->getHTML()."';\n";
        } else {
            $stJs = "d.getElementById('spnObsDevedor').innerHTML = '&nbsp;';\n";
        }
        echo $stJs;
        break;

    case "carregaModulo":
        if ($_GET["inCodTipo"]) {
            $stOrdem = " ORDER BY cod_cadastro ASC ";
            if ($_GET["inCodTipo"] == 1) {
                $stFiltro = " WHERE cod_modulo = 12 and cod_cadastro in ( 2, 3, 4, 5 ) ";
            }else
                if ($_GET["inCodTipo"] == 2) {
                    $stFiltro = " WHERE cod_modulo = 14 and cod_cadastro in ( 1, 2, 3, 5 ) ";
                }

            $obTARRInformacaoAdicional = new TARRInformacaoAdicional;
            $obTARRInformacaoAdicional->recuperaTodos( $rsInformacao );

            $obCmbInformacao = new Select;
            $obCmbInformacao->setName         ( "cmbInfo" );
            $obCmbInformacao->addOption       ( "", "Selecione" );
            $obCmbInformacao->setRotulo       ( "Informação Adicional" );
            $obCmbInformacao->setTitle        ( "Informação adicional para apresentar no carne" );
            $obCmbInformacao->setCampoId      ( "cod_informacao" );
            $obCmbInformacao->setCampoDesc    ( "descricao" );
            $obCmbInformacao->preencheCombo   ( $rsInformacao );
            $obCmbInformacao->setNull         ( false );
            $obCmbInformacao->setStyle        ( "width: 220px" );

            $obSpnInformacao = new Span;
            $obSpnInformacao->setId ( "spnInformacao" );

            $obTARRModeloCarne = new TARRModeloCarne;
            $obTARRModeloCarne->recuperaAtributosDinamicos( $rsVariaveis, $stFiltro, $stOrdem );

            $obCmbVariaveis = new Select;
            $obCmbVariaveis->setName         ( "cmbVariaveis" );
            $obCmbVariaveis->addOption       ( "", "Selecione" );
            $obCmbVariaveis->setRotulo       ( "Atributos" );
            $obCmbVariaveis->setTitle        ( "Atributos para apresentar no carne" );
            $obCmbVariaveis->setCampoId      ( "[cod_modulo]-[cod_cadastro]-[cod_atributo]" );
            $obCmbVariaveis->setCampoDesc    ( "nom_atributo" );
            $obCmbVariaveis->preencheCombo   ( $rsVariaveis );
            $obCmbVariaveis->setNull         ( false );
            $obCmbVariaveis->setStyle        ( "width: 220px" );

            $arDadosOrdem = array();
            $arDadosOrdem[0]["num_ordem"] = 1;
            $arDadosOrdem[1]["num_ordem"] = 2;
            $arDadosOrdem[2]["num_ordem"] = 3;

            $rsOrdem = new RecordSet;
            $rsOrdem->preenche( $arDadosOrdem );

            $obCmbOrdem = new Select;
            $obCmbOrdem->setName         ( "cmbOrdem" );
            $obCmbOrdem->addOption       ( "", "Selecione" );
            $obCmbOrdem->setRotulo       ( "Ordem de Listagem" );
            $obCmbOrdem->setTitle        ( "Ordem de listagem no carne" );
            $obCmbOrdem->setCampoId      ( "num_ordem" );
            $obCmbOrdem->setCampoDesc    ( "num_ordem" );
            $obCmbOrdem->preencheCombo   ( $rsOrdem );
            $obCmbOrdem->setNull         ( false );
            $obCmbOrdem->setStyle        ( "width: 220px" );

            $obCmbOrdemInfo = new Select;
            $obCmbOrdemInfo->setName         ( "cmbOrdemInfo" );
            $obCmbOrdemInfo->addOption       ( "", "Selecione" );
            $obCmbOrdemInfo->setRotulo       ( "Ordem de Listagem" );
            $obCmbOrdemInfo->setTitle        ( "Ordem de listagem no carne" );
            $obCmbOrdemInfo->setCampoId      ( "num_ordem" );
            $obCmbOrdemInfo->setCampoDesc    ( "num_ordem" );
            $obCmbOrdemInfo->preencheCombo   ( $rsOrdem );
            $obCmbOrdemInfo->setNull         ( false );
            $obCmbOrdemInfo->setStyle        ( "width: 220px" );

            $obBtnIncluirInfo = new Button;
            $obBtnIncluirInfo->setName( "stIncluirInfo" );
            $obBtnIncluirInfo->setValue( "Incluir" );
            $obBtnIncluirInfo->obEvento->setOnClick( "montaParametrosGET('incluirInfo', 'cmbInfo,cmbOrdemInfo', true);" );

            $obBtnLimparInfo= new Button;
            $obBtnLimparInfo->setName( "stLimparInfo" );
            $obBtnLimparInfo->setValue( "Limpar" );
            $stOnChange = "ajaxJavaScript('OCManterLayoutCarne.php?".Sessao::getId()."&inCodTipo='+document.frm.cmbModulos.value+'&stObservacaoCapa='+document.frm.stObservacaoCapa.value+'&stObservacaoCorpo='+document.frm.stObservacaoCorpo.value,'limparInfo');";
            $obBtnLimparInfo->obEvento->setOnClick( $stOnChange );

            $obBtnIncluirVariavel = new Button;
            $obBtnIncluirVariavel->setName( "stIncluirVariavel" );
            $obBtnIncluirVariavel->setValue( "Incluir" );
            $obBtnIncluirVariavel->obEvento->setOnClick( "montaParametrosGET('incluirVariavel', 'cmbVariaveis,cmbOrdem', true);" );

            $obBtnLimparVariavel= new Button;
            $obBtnLimparVariavel->setName( "stLimparVariavel" );
            $obBtnLimparVariavel->setValue( "Limpar" );
            $stOnChange = "ajaxJavaScript('OCManterLayoutCarne.php?".Sessao::getId()."&inCodTipo='+document.frm.cmbModulos.value+'&stObservacaoCapa='+document.frm.stObservacaoCapa.value+'&stObservacaoCorpo='+document.frm.stObservacaoCorpo.value,'limparVariavel');";
            $obBtnLimparVariavel->obEvento->setOnClick( $stOnChange );

            $obTxtObservacaoCapa = new TextArea;
            $obTxtObservacaoCapa->setName ( "stObservacaoCapa" );
            $obTxtObservacaoCapa->setRotulo ( "Observações para Capa do Carne" );
            $obTxtObservacaoCapa->setTitle ( "Observações para o contribuinte." );
            $obTxtObservacaoCapa->setNull  ( true );
            $obTxtObservacaoCapa->setCols ( 30 );
            $obTxtObservacaoCapa->setRows ( 5 );
            $obTxtObservacaoCapa->setMaxCaracteres(300);
            $obTxtObservacaoCapa->setValue ( $_GET["stObservacaoCapa"] );
            $obTxtObservacaoCapa->setStyle ( "width: 100%" );

            $obTxtObservacaoCorpo = new TextArea;
            $obTxtObservacaoCorpo->setName ( "stObservacaoCorpo" );
            $obTxtObservacaoCorpo->setRotulo ( "Observações para Carne" );
            $obTxtObservacaoCorpo->setTitle ( "Observações para o contribuinte." );
            $obTxtObservacaoCorpo->setNull  ( true );
            $obTxtObservacaoCorpo->setCols ( 30 );
            $obTxtObservacaoCorpo->setRows ( 5 );
            $obTxtObservacaoCorpo->setMaxCaracteres(300);
            $obTxtObservacaoCorpo->setValue ( $_GET["stObservacaoCorpo"] );
            $obTxtObservacaoCorpo->setStyle ( "width: 100%" );

            $obRdbMsgCondArrecSim = new Radio;
            $obRdbMsgCondArrecSim->setRotulo     ( "Observação de Débitos" );
            $obRdbMsgCondArrecSim->setName       ( "stMsgArrecadacao" );
            $obRdbMsgCondArrecSim->setLabel      ( "Utilizar" );
            $obRdbMsgCondArrecSim->setValue      ( true );
            $obRdbMsgCondArrecSim->setTitle      ( "Apresenta uma observação conforme a condição da inscrição na arrecadação." );
            $obRdbMsgCondArrecSim->setNull       ( false );
            $obRdbMsgCondArrecSim->setChecked    ( false );
            $pgOcul = "OCManterLayoutCarne.php?".Sessao::getId();
            $stOnChange = "ajaxJavaScript('".$pgOcul."&stMsgArrecadacao='+this.value,'carregaObservacaoDebitos');";
            $obRdbMsgCondArrecSim->obEvento->setOnChange( $stOnChange );

            $obRdbMsgCondArrecNao = new Radio;
            $obRdbMsgCondArrecNao->setRotulo   ( "Observação de Débitos" );
            $obRdbMsgCondArrecNao->setName     ( "stMsgArrecadacao" );
            $obRdbMsgCondArrecNao->setLabel    ( "Desativado" );
            $obRdbMsgCondArrecNao->setTitle    ( "Apresenta uma observação conforme a condição da inscrição na arrecadação." );
            $obRdbMsgCondArrecNao->setValue    ( false );
            $obRdbMsgCondArrecNao->setNull     ( false );
            $obRdbMsgCondArrecNao->setChecked  ( true );
            $obRdbMsgCondArrecNao->obEvento->setOnChange( $stOnChange );

            $obSpnObsDevedor = new Span;
            $obSpnObsDevedor->setId ( "spnObsDevedor" );

            $obFormulario = new Formulario;
            if ( $_GET["inCodTipo"] == 1 )
                $obFormulario->addTitulo ( "Módulo Imobiliário" );
            else
                $obFormulario->addTitulo ( "Módulo Econômico" );

            $obFormulario->addComponente ( $obTxtObservacaoCapa );
            $obFormulario->addComponente ( $obTxtObservacaoCorpo );
            $obFormulario->agrupaComponentes ( array( $obRdbMsgCondArrecSim, $obRdbMsgCondArrecNao ) );
            $obFormulario->addSpan       ( $obSpnObsDevedor );
            $obFormulario->addComponente ( $obCmbInformacao );
            $obFormulario->addComponente ( $obCmbOrdemInfo );
            $obFormulario->defineBarra   ( array( $obBtnIncluirInfo, $obBtnLimparInfo ),"","" );
            $obFormulario->addSpan       ( $obSpnInformacao );

            $obFormulario->addComponente ( $obCmbVariaveis );
            $obFormulario->addComponente ( $obCmbOrdem );
            $obFormulario->defineBarra   ( array( $obBtnIncluirVariavel, $obBtnLimparVariavel ),"","" );
            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnModulo').innerHTML = '".$obFormulario->getHTML()."';\n";
        } else {
            $stJs = "d.getElementById('spnModulo').innerHTML = '&nbsp;';\n";
        }

        $rsListaVariaveis = new RecordSet;
        Sessao::write( "layoutVariaveis", array() );
        $stJs .= montaListaAtributos( $rsListaVariaveis );

        echo $stJs;
        break;
}
