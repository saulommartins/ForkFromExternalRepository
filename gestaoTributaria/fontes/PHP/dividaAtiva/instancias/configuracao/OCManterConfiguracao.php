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
    * Página de Frame Oculto para Configuração da Divida Ativa
    * Data de Criação   : 05/05/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.01
*/

/*
$Log$
Revision 1.7  2007/03/01 13:16:39  cercato
Bug #8532#

Revision 1.6  2006/09/15 14:36:02  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_DAT_NEGOCIO."RDATConfiguracao.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "ajustaDocumentos":
        if ($_REQUEST["stDocumento"]) {
            $obRDATConfiguracao = new RDATConfiguracao;
            $obRDATConfiguracao->consultarDocumento( $_REQUEST["stDocumento"] );

            $stSecretaria = $obRDATConfiguracao->getDocumentoSecretaria();
            $stCoordenador = $obRDATConfiguracao->getDocumentoCoordenador();
            $stChefeDepartamento = $obRDATConfiguracao->getDocumentoChefeDepartamento();
            $stSetorArrecadacao = $obRDATConfiguracao->getDocumentoSetorArrecadacao();
            $stDocumentoMsg = $obRDATConfiguracao->getDocumentoMensagem();

            $boUtilMetCal = $obRDATConfiguracao->getDocumentoUtilizarMetCalc();
            $boUtilIncVal = $obRDATConfiguracao->getDocumentoUtilizarIncidValDA();
            $boUtilLeiDA = $obRDATConfiguracao->getDocumentoUtilizarLeiDA();
            $boUtilMsg = $obRDATConfiguracao->getDocumentoUtilizarMsg();
            $boUtilResp2 = $obRDATConfiguracao->getDocumentoUtilizarResp2();
        } else {
            $stSecretaria = "";
            $stCoordenador = "";
            $stChefeDepartamento = "";
            $stSetorArrecadacao = "";
            $stDocumentoMsg = "";
            $boUtilMetCal = true;
            $boUtilIncVal = true;
            $boUtilLeiDA = true;
            $boUtilMsg = true;
            $boUtilResp2 = true;
        }

        $stDocumentoMsg = str_replace( "\n", "\\n", $stDocumentoMsg );
        $stDocumentoMsg = str_replace( "\r", "\\r", $stDocumentoMsg );

        $stCoordenador = str_replace( "\n", "\\n", $stCoordenador );
        $stCoordenador = str_replace( "\r", "\\r", $stCoordenador );

        $stChefeDepartamento = str_replace( "\n", "\\n", $stChefeDepartamento );
        $stChefeDepartamento = str_replace( "\r", "\\r", $stChefeDepartamento );

        $js .= "jq_('#stMensagem[type=textarea]').attr('disabled', '".!$boUtilMsg."');";
        $js .= "jq_('#boMsg[type=checkbox]').attr('checked', '".$boUtilMsg."');";

        $js .= "jq_('#stChefeDepartamento[type=textarea]').attr('disabled', '".!$boUtilResp2."');";
        $js .= "jq_('#boResp2[type=checkbox]').attr('checked', '".$boUtilResp2."');";

        $js .= "jq_('#stSecretaria').val('".$stSecretaria."');";
        $js .= "jq_('#stSetorArrecadacao').val('".$stSetorArrecadacao."');";
        $js .= "jq_('#stCoordenador').val('".$stCoordenador."');";
        $js .= "jq_('#stChefeDepartamento').val('".$stChefeDepartamento."');";
        $js .= "jq_('#stMensagem').val('".$stDocumentoMsg."');";
        switch ($_REQUEST["stDocumento"]) {
            default:
                $js .= "jq_('#spnDocumento').html('');";
                break;

            case 1: //"Certidão de Dívida Ativa"
                $stMetodologiaCalculo = $obRDATConfiguracao->getDocumentoMetodologiaCalculo();
                $stMetodologiaCalculo = str_replace( "\n","\\n", $stMetodologiaCalculo );
                $stMetodologiaCalculo = str_replace( "\r","\\r", $stMetodologiaCalculo );
                $stLeiDA = $obRDATConfiguracao->getDocumentoNroLeiInscricaoDA();

                $obChkUtilizarLeiDA = new Checkbox;
                $obChkUtilizarLeiDA->setName ( "boLeiDA" );
                $obChkUtilizarLeiDA->setId ( "boLeiDA" );
                $obChkUtilizarLeiDA->obEvento->setOnChange( "ControleLeiDA();");
                $obChkUtilizarLeiDA->setChecked( $boUtilLeiDA );
                $obChkUtilizarLeiDA->montaHTML();

                $obTxtLeiDA = new TextBox;
                $obTxtLeiDA->setRotulo  ( $obChkUtilizarLeiDA->getHTML().'Lei Municipal para Certidão Dívida Ativa');
                $obTxtLeiDA->setTitle   ( 'Informar número de lei municipal para certidão de dívida ativa.');
                $obTxtLeiDA->setName    ( 'stLeiDA');
                $obTxtLeiDA->setValue   ( $stLeiDA );
                $obTxtLeiDA->setDisabled( !$boUtilLeiDA );
                $obTxtLeiDA->setSize    ( 40 );
                $obTxtLeiDA->setMaxLength ( 40 );
                $obTxtLeiDA->setNull    ( true );

                $obChkUtilizarMetodologiaCalculo = new Checkbox;
                $obChkUtilizarMetodologiaCalculo->setName ( "boMetCalc" );
                $obChkUtilizarMetodologiaCalculo->setId ( "boMetCalc" );
                $obChkUtilizarMetodologiaCalculo->obEvento->setOnChange( "ControleMetCalc();");
                $obChkUtilizarMetodologiaCalculo->setChecked( $boUtilMetCal );
                $obChkUtilizarMetodologiaCalculo->montaHTML();

                $obTxtMetodologiaCalculo = new TextArea;
                $obTxtMetodologiaCalculo->setName ( "stMetodologiaCalculo" );
                $obTxtMetodologiaCalculo->setRotulo ( $obChkUtilizarMetodologiaCalculo->getHTML()."Metodologia de Cálculo" );
                $obTxtMetodologiaCalculo->setTitle ( "Informar metodologia de cálculo." );
                $obTxtMetodologiaCalculo->setValue ( $stMetodologiaCalculo );
                $obTxtMetodologiaCalculo->setNull ( true );
                $obTxtMetodologiaCalculo->setDisabled( !$boUtilMetCal );
                $obTxtMetodologiaCalculo->setCols ( 80 );
                $obTxtMetodologiaCalculo->setRows ( 5 );
                $obTxtMetodologiaCalculo->setMaxCaracteres(3000);
                $obTxtMetodologiaCalculo->setStyle ( "width: 540px" );

                $obChkUtilizarIncidenciaValorDebitoDA = new Checkbox;
                $obChkUtilizarIncidenciaValorDebitoDA->setName ( "boIncidValDA" );
                $obChkUtilizarIncidenciaValorDebitoDA->setId ( "boIncidValDA" );
                $obChkUtilizarIncidenciaValorDebitoDA->setTitle ( "Apresentar incidência sobre o valor do débito inscrito em dívida ativa." );
                $obChkUtilizarIncidenciaValorDebitoDA->setRotulo ("Incidência Sobre Valor Débito Dívida Ativa");
                $obChkUtilizarIncidenciaValorDebitoDA->setChecked( $boUtilIncVal );

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obTxtLeiDA );
                $obFormulario->addComponente( $obTxtMetodologiaCalculo );
                $obFormulario->addComponente( $obChkUtilizarIncidenciaValorDebitoDA );
                $obFormulario->montaInnerHTML();
                $stHTML = str_replace("\n","\\n",$obFormulario->getHTML());
                $stHTML = str_replace("\r","\\r",$stHTML);
                $js .= "jq_('#spnDocumento').html('".$stHTML."');";
                break;

            case 2: //"Termo de Inscricao de Dívida Ativa"
                $stMetodologiaCalculo = $obRDATConfiguracao->getDocumentoMetodologiaCalculo();
                $stMetodologiaCalculo = str_replace( "\n","\\n", $stMetodologiaCalculo );
                $stMetodologiaCalculo = str_replace( "\r","\\r", $stMetodologiaCalculo );

                $obChkUtilizarMetodologiaCalculo = new Checkbox;
                $obChkUtilizarMetodologiaCalculo->setName ( "boMetCalc" );
                $obChkUtilizarMetodologiaCalculo->setId ( "boMetCalc" );
                $obChkUtilizarMetodologiaCalculo->obEvento->setOnChange( "ControleMetCalc();");
                $obChkUtilizarMetodologiaCalculo->setChecked( $boUtilMetCal );
                $obChkUtilizarMetodologiaCalculo->montaHTML();

                $obTxtMetodologiaCalculo = new TextArea;
                $obTxtMetodologiaCalculo->setName ( "stMetodologiaCalculo" );
                $obTxtMetodologiaCalculo->setRotulo ( $obChkUtilizarMetodologiaCalculo->getHTML()."Metodologia de Cálculo" );
                $obTxtMetodologiaCalculo->setTitle ( "Informar metodologia de cálculo." );
                $obTxtMetodologiaCalculo->setValue ( $stMetodologiaCalculo );
                $obTxtMetodologiaCalculo->setNull ( true );
                $obTxtMetodologiaCalculo->setDisabled( !$boUtilMetCal );
                $obTxtMetodologiaCalculo->setCols ( 80 );
                $obTxtMetodologiaCalculo->setRows ( 5 );
                $obTxtMetodologiaCalculo->setMaxCaracteres(3000);
                $obTxtMetodologiaCalculo->setStyle ( "width: 540px" );

                $obChkUtilizarIncidenciaValorDebitoDA = new Checkbox;
                $obChkUtilizarIncidenciaValorDebitoDA->setName ( "boIncidValDA" );
                $obChkUtilizarIncidenciaValorDebitoDA->setId ( "boIncidValDA" );
                $obChkUtilizarIncidenciaValorDebitoDA->setTitle ( "Apresentar incidência sobre o valor do débito inscrito em dívida ativa." );
                $obChkUtilizarIncidenciaValorDebitoDA->setRotulo ("Incidência Sobre Valor Débito Dívida Ativa");
                $obChkUtilizarIncidenciaValorDebitoDA->setChecked( $boUtilIncVal );

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obTxtMetodologiaCalculo );
                $obFormulario->addComponente( $obChkUtilizarIncidenciaValorDebitoDA );
                $obFormulario->montaInnerHTML();
                $stHTML = str_replace("\n","\\n",$obFormulario->getHTML());
                $stHTML = str_replace("\r","\\r",$stHTML);
                $js .= "jq_('#spnDocumento').html('".$stHTML."');";
                break;

            case 3: //"Memorial de Cálculo da Dívida Ativa"
                $js .= "jq_('#spnDocumento').html('');";
                break;

            case 4: //"Termo Consolidação"
                $js .= "jq_('#spnDocumento').html('');";
                break;

            case 5: //"Termo de Parcelamento"
                $js .= "jq_('#spnDocumento').html('');";
                break;

            case 6: //"Notificacao de Dívida Ativa"
                $js .= "jq_('#spnDocumento').html('');";
                break;
        }

        SistemaLegado::executaFrameOculto( $js );
        break;

    case "limpaArray":
        Sessao::remove('valores');
        break;

    case "buscaCreditoDivida":
        $obRMONCredito = new RMONCredito;

        if ($_REQUEST["inCreditoDivida"]) {
            $arCodigos = explode( ".", $_REQUEST["inCreditoDivida"] );
            if (count ($arCodigos) != 4) {
                $stJs = 'f.inCreditoDivida.value = "";';
                $stJs .= 'f.inCreditoDivida.focus();';
                $stJs .= 'd.getElementById("stCreditoDivida").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCreditoDivida"].")','form','erro','".Sessao::getId()."');";
            } else {
                $obRMONCredito->setCodCredito( $arCodigos[0] );
                $obRMONCredito->setCodEspecie( $arCodigos[1] );
                $obRMONCredito->setCodGenero( $arCodigos[2] );
                $obRMONCredito->setCodNatureza( $arCodigos[3] );

                $obRMONCredito->listarCreditosPopUp( $rsLista );
                if ( $rsLista->eof() ) {
                    $stJs = 'f.inCreditoDivida.value = "";';
                    $stJs .= 'f.inCreditoDivida.focus();';
                    $stJs .= 'd.getElementById("stCreditoDivida").innerHTML = "&nbsp;";';
                    $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCreditoDivida"].")','form','erro','".Sessao::getId()."');";
                } else {
                    $stJs .= 'd.getElementById("stCreditoDivida").innerHTML = "'.$rsLista->getCampo("descricao_credito").'";';
                }
            }
        } else {
            $stJs .= 'd.getElementById("stCreditoDivida").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaMoeda":
        $obRMONMoeda = new RMONMoeda;
        if ($_REQUEST["inMoeda"]) {
            $obRMONMoeda->setCodMoeda( $_REQUEST["inMoeda"] );
            $obRMONMoeda->listarMoeda($rsLista, $boTransacao );
            if ( $rsLista->eof() ) {
                $stJs = 'f.inMoeda.value = "";';
                $stJs .= 'f.inMoeda.focus();';
                $stJs .= 'd.getElementById("stMoeda").innerHTML = "&nbsp;";';
                $stJs .= 'f.inSimboloMoeda.value = "";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inMoeda"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stMoeda").innerHTML = "'.$rsLista->getCampo("descricao_singular").'";';
                $stJs .= 'f.inSimboloMoeda.value = "'.$rsLista->getCampo("simbolo").'";';
                $arValoresSessao["stMoeda"]        = $rsLista->getCampo("descricao_singular");
                $arValoresSessao["inSimboloMoeda"] = $rsLista->getCampo("simbolo");
                $arValoresSessao["inMoeda"]        = $_REQUEST["inMoeda"];
                Sessao::write('valores', $arValoresSessao);
            }
        } else {
            $stJs = 'd.getElementById("stMoeda").innerHTML = "&nbsp;";';
            $stJs .= 'f.inSimboloMoeda.value = "";';
            $stJs .= 'f.inMoeda.value = "";';
            Sessao::remove('valores');
            if ($_REQUEST["inMoeda"] == '0') {
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inMoeda"].")','form','erro','".Sessao::getId()."');";
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaIndicadorEconomico":
        $obRMONIndicador = new RMONIndicadorEconomico;
        if ($_REQUEST["inIndicadorEconomico"]) {
            $obRMONIndicador->setCodIndicador( $_REQUEST["inIndicadorEconomico"] );
            $obRMONIndicador->listarIndicadores($rsLista, $boTransacao );

            if ( $rsLista->eof() ) {
                $stJs = 'f.inIndicadorEconomico.value = "";';
                $stJs .= 'f.inIndicadorEconomico.focus();';
                $stJs .= 'd.getElementById("stIndicadorEconomico").innerHTML = "&nbsp;";';
                $stJs .= 'f.inAbreviatura.value = "";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inIndicadorEconomico"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stIndicadorEconomico").innerHTML = "'.$rsLista->getCampo("descricao").'";';

                $stJs .= 'f.inAbreviatura.value = "'.$rsLista->getCampo("abreviatura").'";';

                $arValoresSessao["inAbreviatura"]        = $rsLista->getCampo("abreviatura");
                $arValoresSessao["inIndicadorEconomico"] = $_REQUEST["inIndicadorEconomico"];
                $arValoresSessao["stIndicadorEconomico"] = $rsLista->getCampo("descricao");
            }
        } else {
            $stJs = 'd.getElementById("stIndicadorEconomico").innerHTML = "&nbsp;";';
            $stJs .= 'f.inAbreviatura.value = "";';
            $arValoresSessao["inAbreviatura"]        = "";
            $arValoresSessao["inIndicadorEconomico"] = "";
            $arValoresSessao["stIndicadorEconomico"] = "&nbsp;";
        }

        Sessao::write('valores', $arValoresSessao);
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "desativarSpam":
        $arValoresSessao = Sessao::read('valores');
        if ($_REQUEST["stValorReferencia"] == "sim") {
            $obFormulario = new Formulario;
            if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
                //credito de divida ativa
                $obBscMoeda = new BuscaInner;
                $obBscMoeda->setRotulo ( "Moeda" );
                $obBscMoeda->setNull ( false );
                $obBscMoeda->setId ( "stMoeda" );
                $obBscMoeda->obCampoCod->setName ("inMoeda");
                $obBscMoeda->obCampoCod->obEvento->setOnChange ("buscaValor('buscaMoeda');");
                $obBscMoeda->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."moeda/FLProcurarMoeda.php','frm','inMoeda','stMoeda','todos','".Sessao::getId()."','800','550');" );
                $obBscMoeda->setTitle ( "Moeda a ser utilizada como valor de referência" );
                $obBscMoeda->obCampoCod->setValue ( $arValoresSessao["inMoeda"] );

                //valor de referencia
                $rsReferencia = new RecordSet;
                $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
                $arDados[0]['nom_Referencia'] = 'Máximo';
                $arDados[1]['nom_Referencia'] = 'Mínimo';
                $rsReferencia->preenche( $arDados );

                $obCmbReferencia = new Select;
                $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
                $obCmbReferencia->setName ( "cmbReferenciaMoeda" );
                $obCmbReferencia->setRotulo ( "Valor de Referência" );
                $obCmbReferencia->addOption ( "", "Selecione" );
                $obCmbReferencia->setCampoId ( "nom_Referencia" );
                $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
                $obCmbReferencia->preencheCombo ( $rsReferencia );
                $obCmbReferencia->setNull ( false );
                $obCmbReferencia->setValue ( $arValoresSessao["cmbMoeda"] );

                $obTxtSimboloMoeda = new TextBox;
                $obTxtSimboloMoeda->setRotulo ( "Valor de Referência" );
                $obTxtSimboloMoeda->setName ( "inSimboloMoeda" );
                $obTxtSimboloMoeda->setReadOnly ( true );
                $obTxtSimboloMoeda->setValue ( $arValoresSessao["inSimboloMoeda"] );

                $obTxtValorReferencia = new Numerico;
                $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
                $obTxtValorReferencia->setFloat ( true );
                $obTxtValorReferencia->setName ( "inValorReferenciaMoeda" );
                $obTxtValorReferencia->setValue ( $arValoresSessao["inValorReferencia"] );

                $obFormulario->addComponente ( $obBscMoeda );
                $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtSimboloMoeda, $obTxtValorReferencia) );
            }else
            if ($_REQUEST["stTipoValorReferencia"] == "indicador") {
                //Indicador economico
                $obBscIndicadorEconomico = new BuscaInner;
                $obBscIndicadorEconomico->setRotulo ( "Indicador Econômico" );
                $obBscIndicadorEconomico->setNull ( false );
                $obBscIndicadorEconomico->setId ( "stIndicadorEconomico" );
                $obBscIndicadorEconomico->obCampoCod->setName ("inIndicadorEconomico");
                $obBscIndicadorEconomico->obCampoCod->obEvento->setOnChange ("buscaValor('buscaIndicadorEconomico');");
                $obBscIndicadorEconomico->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."indicadorEconomico/FLProcurarIndicador.php','frm','inIndicadorEconomico','stIndicadorEconomico','todos','".Sessao::getId()."','800','550');" );
                $obBscIndicadorEconomico->setTitle ( "Indicador econômico a ser utilizado como valor de referência" );
                $obBscIndicadorEconomico->obCampoCod->setValue ( $arValoresSessao["inIndicadorEconomico"] );

                //valor de referencia
                $rsReferencia = new RecordSet;
                $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
                $arDados[0]['nom_Referencia'] = 'Máximo';
                $arDados[1]['nom_Referencia'] = 'Mínimo';
                $rsReferencia->preenche( $arDados );

                $obCmbReferencia = new Select;
                $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
                $obCmbReferencia->setName ( "cmbReferenciaIndicador" );
                $obCmbReferencia->setRotulo ( "Valor de Referência" );
                $obCmbReferencia->addOption ( "", "Selecione" );
                $obCmbReferencia->setValue ( $_REQUEST['inNumReferencia'] );
                $obCmbReferencia->setCampoId ( "nom_Referencia" );
                $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
                $obCmbReferencia->preencheCombo ( $rsReferencia );
                $obCmbReferencia->setNull ( false );
                $obCmbReferencia->setValue ( $arValoresSessao["cmbIndicador"] );

                $obTxtAbreviatura = new TextBox; //abreviatura do indicador economico
                $obTxtAbreviatura->setRotulo ( "Valor de Referência" );
                $obTxtAbreviatura->setName ( "inAbreviatura" );
                $obTxtAbreviatura->setReadOnly ( true );
                $obTxtAbreviatura->setValue ( $arValoresSessao["inAbreviatura"] );

                $obTxtValorReferencia = new Numerico;
                $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
                $obTxtValorReferencia->setFloat ( true );
                $obTxtValorReferencia->setName ( "inValorReferenciaIndicador" );
                $obTxtValorReferencia->setValue ( $arValoresSessao["inValorReferenciaIndicador"] );

                $obFormulario->addComponente ( $obBscIndicadorEconomico );
                $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtValorReferencia, $obTxtAbreviatura) );
            }

            $obFormulario->montaInnerHTML();
            $js = "d.getElementById('spnTipoValor').innerHTML = '". $obFormulario->getHTML(). "';\n";

            if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
                $js .= 'd.getElementById("stMoeda").innerHTML = "'.$arValoresSessao["stMoeda"].'";';
            }else
            if ($_REQUEST["stTipoValorReferencia"] == "indicador") {
                $js .= 'd.getElementById("stIndicadorEconomico").innerHTML = "'.$arValoresSessao["stIndicadorEconomico"].'";';
            }
        } else {
            if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
                $arValoresSessao["cmbMoeda"] = $_REQUEST["cmbReferenciaMoeda"];
                $arValoresSessao["inValorReferencia"] = $_REQUEST["inValorReferenciaMoeda"];
            }else
            if ($_REQUEST["stTipoValorReferencia"] == "indicador") {
                $arValoresSessao["cmbIndicador"] = $_REQUEST["cmbReferenciaIndicador"];
                $arValoresSessao["inValorReferenciaIndicador"] = $_REQUEST["inValorReferenciaIndicador"];
            }

            $js = "d.getElementById('spnTipoValor').innerHTML = '&nbsp;';\n";
        }

        Sessao::write('valores', $arValoresSessao);
        SistemaLegado::executaFrameOculto($js);
        break;

    case "montaTipoValorReferencia":
        Sessao::read('valores', $arValoresSessao);
        if ($_REQUEST["stValorReferencia"] == "sim") {
            $obFormulario = new Formulario;
            if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
                $arValoresSessao["cmbIndicador"]               = $_REQUEST["cmbReferenciaIndicador"];
                $arValoresSessao["inValorReferenciaIndicador"] = $_REQUEST["inValorReferenciaIndicador"];

                //credito de divida ativa
                $obBscMoeda = new BuscaInner;
                $obBscMoeda->setRotulo ( "Moeda" );
                $obBscMoeda->setNull ( false );
                $obBscMoeda->setId ( "stMoeda" );
                $obBscMoeda->obCampoCod->setName ("inMoeda");
                $obBscMoeda->obCampoCod->obEvento->setOnChange ("buscaValor('buscaMoeda');");
                $obBscMoeda->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."moeda/FLProcurarMoeda.php','frm','inMoeda','stMoeda','todos','".Sessao::getId()."','800','550');" );
                $obBscMoeda->setTitle ( "Moeda a ser utilizada como valor de referência" );
                $obBscMoeda->obCampoCod->setValue ( $arValoresSessao["inMoeda"] );

                //valor de referencia
                $rsReferencia = new RecordSet;
                $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
                $arDados[0]['nom_Referencia'] = 'Máximo';
                $arDados[1]['nom_Referencia'] = 'Mínimo';
                $rsReferencia->preenche( $arDados );

                $obCmbReferencia = new Select;
                $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
                $obCmbReferencia->setName ( "cmbReferenciaMoeda" );
                $obCmbReferencia->setRotulo ( "Valor de Referência" );
                $obCmbReferencia->addOption ( "", "Selecione" );
                $obCmbReferencia->setCampoId ( "nom_Referencia" );
                $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
                $obCmbReferencia->preencheCombo ( $rsReferencia );
                $obCmbReferencia->setNull ( false );
                $obCmbReferencia->setValue ( $arValoresSessao["cmbMoeda"] );

                $obTxtSimboloMoeda = new TextBox;
                $obTxtSimboloMoeda->setRotulo ( "Valor de Referência" );
                $obTxtSimboloMoeda->setName ( "inSimboloMoeda" );
                $obTxtSimboloMoeda->setReadOnly ( true );
                $obTxtSimboloMoeda->setValue ( $arValoresSessao["inSimboloMoeda"] );

                $obTxtValorReferencia = new Numerico;
                $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
                $obTxtValorReferencia->setFloat ( true );
                $obTxtValorReferencia->setName ( "inValorReferenciaMoeda" );
                $obTxtValorReferencia->setValue ( $arValoresSessao["inValorReferencia"] );

                $obFormulario->addComponente ( $obBscMoeda );
                $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtSimboloMoeda, $obTxtValorReferencia) );
            }else
            if ($_REQUEST["stTipoValorReferencia"] == "indicador") {
                $arValoresSessao["cmbMoeda"]          = $_REQUEST["cmbReferenciaMoeda"];
                $arValoresSessao["inValorReferencia"] = $_REQUEST["inValorReferenciaMoeda"];

                //Indicador economico
                $obBscIndicadorEconomico = new BuscaInner;
                $obBscIndicadorEconomico->setRotulo ( "Indicador Econômico" );
                $obBscIndicadorEconomico->setNull ( false );
                $obBscIndicadorEconomico->setId ( "stIndicadorEconomico" );
                $obBscIndicadorEconomico->obCampoCod->setName ("inIndicadorEconomico");
                $obBscIndicadorEconomico->obCampoCod->obEvento->setOnChange ("buscaValor('buscaIndicadorEconomico');");
                $obBscIndicadorEconomico->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."indicadorEconomico/FLProcurarIndicador.php','frm','inIndicadorEconomico','stIndicadorEconomico','todos','".Sessao::getId()."','800','550');" );
                $obBscIndicadorEconomico->setTitle ( "Indicador econômico a ser utilizado como valor de referência" );
                $obBscIndicadorEconomico->obCampoCod->setValue ( $arValoresSessao["inIndicadorEconomico"] );

                //valor de referencia
                $rsReferencia = new RecordSet;
                $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
                $arDados[0]['nom_Referencia'] = 'Máximo';
                $arDados[1]['nom_Referencia'] = 'Mínimo';
                $rsReferencia->preenche( $arDados );

                $obCmbReferencia = new Select;
                $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
                $obCmbReferencia->setName ( "cmbReferenciaIndicador" );
                $obCmbReferencia->setRotulo ( "Valor de Referência" );
                $obCmbReferencia->addOption ( "", "Selecione" );
                $obCmbReferencia->setValue ( $_REQUEST['inNumReferencia'] );
                $obCmbReferencia->setCampoId ( "nom_Referencia" );
                $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
                $obCmbReferencia->preencheCombo ( $rsReferencia );
                $obCmbReferencia->setNull ( false );
                $obCmbReferencia->setValue ( $arValoresSessao["cmbIndicador"] );

                $obTxtAbreviatura = new TextBox; //abreviatura do indicador economico
                $obTxtAbreviatura->setRotulo ( "Valor de Referência" );
                $obTxtAbreviatura->setName ( "inAbreviatura" );
                $obTxtAbreviatura->setReadOnly ( true );
                $obTxtAbreviatura->setValue ( $arValoresSessao["inAbreviatura"] );

                $obTxtValorReferencia = new Numerico;
                $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
                $obTxtValorReferencia->setFloat ( true );
                $obTxtValorReferencia->setName ( "inValorReferenciaIndicador" );
                $obTxtValorReferencia->setValue ( $arValoresSessao["inValorReferenciaIndicador"] );

                $obFormulario->addComponente ( $obBscIndicadorEconomico );
                $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtValorReferencia, $obTxtAbreviatura) );
            }

            $obFormulario->montaInnerHTML();
            $js = "d.getElementById('spnTipoValor').innerHTML = '". $obFormulario->getHTML(). "';\n";

            if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
                $js .= 'd.getElementById("stMoeda").innerHTML = "'.$arValoresSessao["stMoeda"].'";';
            }else
            if ($_REQUEST["stTipoValorReferencia"] == "indicador") {
                $js .= 'd.getElementById("stIndicadorEconomico").innerHTML = "'.$arValoresSessao["stIndicadorEconomico"].'";';
            }
        } else {
            $arValoresSessao["cmbMoeda"]          = $_REQUEST["cmbReferenciaMoeda"];
            $arValoresSessao["inValorReferencia"] = $_REQUEST["inValorReferenciaMoeda"];

            $arValoresSessao["cmbIndicador"]               = $_REQUEST["cmbReferenciaIndicador"];
            $arValoresSessao["inValorReferenciaIndicador"] = $_REQUEST["inValorReferenciaIndicador"];

            $js = "d.getElementById('spnTipoValor').innerHTML = '&nbsp;';\n";
        }

        Sessao::write('valores', $arValoresSessao);
        SistemaLegado::executaFrameOculto($js);
        break;

    case "montaTipoValorReferenciaPreenxer":
        $arValoresSessao = array();
        $arValoresSessao["stMoeda"]              = "&nbsp;";
        $arValoresSessao["stIndicadorEconomico"] = "&nbsp;";

        $obFormulario = new Formulario;
        if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
            //credito de divida ativa
            $obBscMoeda = new BuscaInner;
            $obBscMoeda->setRotulo ( "Moeda" );
            $obBscMoeda->setNull ( false );
            $obBscMoeda->setId ( "stMoeda" );
            $obBscMoeda->obCampoCod->setName ("inMoeda");
            $obBscMoeda->obCampoCod->obEvento->setOnChange ("buscaValor('buscaMoeda');");
            $obBscMoeda->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."moeda/FLProcurarMoeda.php','frm','inMoeda','stMoeda','todos','".Sessao::getId()."','800','550');" );
            $obBscMoeda->setTitle ( "Moeda a ser utilizada como valor de referência" );

            //valor de referencia
            $rsReferencia = new RecordSet;
            $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
            $arDados[0]['nom_Referencia'] = 'Máximo';
            $arDados[1]['nom_Referencia'] = 'Mínimo';
            $rsReferencia->preenche( $arDados );

            $obCmbReferencia = new Select;
            $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
            $obCmbReferencia->setName ( "cmbReferenciaMoeda" );
            $obCmbReferencia->setRotulo ( "Valor de Referência" );
            $obCmbReferencia->addOption ( "", "Selecione" );
            $obCmbReferencia->setCampoId ( "nom_Referencia" );
            $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
            $obCmbReferencia->preencheCombo ( $rsReferencia );
            $obCmbReferencia->setValue ( $_REQUEST["stcmbReferencia"] );
            $obCmbReferencia->setNull ( false );

            $obTxtSimboloMoeda = new TextBox;
            $obTxtSimboloMoeda->setRotulo ( "Valor de Referência" );
            $obTxtSimboloMoeda->setName ( "inSimboloMoeda" );
            $obTxtSimboloMoeda->setReadOnly ( true );

            $obTxtValorReferencia = new Numerico;
            $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
            $obTxtValorReferencia->setFloat ( true );
            $obTxtValorReferencia->setName ( "inValorReferenciaMoeda" );
            $obTxtValorReferencia->setValue( $_REQUEST["inValorReferencia"] );

            $obFormulario->addComponente ( $obBscMoeda );
            $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtSimboloMoeda, $obTxtValorReferencia) );
        } else {
            //Indicador economico
            $obBscIndicadorEconomico = new BuscaInner;
            $obBscIndicadorEconomico->setRotulo ( "Indicador Econômico" );
            $obBscIndicadorEconomico->setNull ( false );
            $obBscIndicadorEconomico->setId ( "stIndicadorEconomico" );
            $obBscIndicadorEconomico->obCampoCod->setName ("inIndicadorEconomico");
            $obBscIndicadorEconomico->obCampoCod->obEvento->setOnChange ("buscaValor('buscaIndicadorEconomico');");
            $obBscIndicadorEconomico->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."indicadorEconomico/FLProcurarIndicador.php','frm','inIndicadorEconomico','stIndicadorEconomico','todos','".Sessao::getId()."','800','550');" );
            $obBscIndicadorEconomico->setTitle ( "Indicador econômico a ser utilizado como valor de referência" );

            //valor de referencia
            $rsReferencia = new RecordSet;
            $arDados = array(); //1 => 'nom_Referencia' => 'Máximo', 2 => 'nom_Referencia' => 'Mínimo');
            $arDados[0]['nom_Referencia'] = 'Máximo';
            $arDados[1]['nom_Referencia'] = 'Mínimo';
            $rsReferencia->preenche( $arDados );

            $obCmbReferencia = new Select;
            $obCmbReferencia->setTitle ( "Valor a ser utilizado como referência" );
            $obCmbReferencia->setName ( "cmbReferenciaIndicador" );
            $obCmbReferencia->setRotulo ( "Valor de Referência" );
            $obCmbReferencia->addOption ( "", "Selecione" );
            $obCmbReferencia->setValue ( $_REQUEST["stcmbReferencia"] );
            $obCmbReferencia->setCampoId ( "nom_Referencia" );
            $obCmbReferencia->setCampoDesc ( "nom_Referencia" );
            $obCmbReferencia->preencheCombo ( $rsReferencia );
            $obCmbReferencia->setNull ( false );

            $obTxtAbreviatura = new TextBox; //abreviatura do indicador economico
            $obTxtAbreviatura->setRotulo ( "Valor de Referência" );
            $obTxtAbreviatura->setName ( "inAbreviatura" );
            $obTxtAbreviatura->setReadOnly ( true );

            $obTxtValorReferencia = new Numerico;
            $obTxtValorReferencia->setRotulo ( "Valor de Referência" );
            $obTxtValorReferencia->setFloat ( true );
            $obTxtValorReferencia->setName ( "inValorReferenciaIndicador" );
            $obTxtValorReferencia->setValue( $_REQUEST["inValorReferencia"] );

            $obFormulario->addComponente ( $obBscIndicadorEconomico );
            $obFormulario->agrupaComponentes ( array( $obCmbReferencia, $obTxtValorReferencia, $obTxtAbreviatura) );
        }

        $obFormulario->montaInnerHTML();
        $js = "d.getElementById('spnTipoValor').innerHTML = '". $obFormulario->getHTML(). "';\n";

        if ($_REQUEST["stTipoValorReferencia"] == "moeda") {
            $obRMONMoeda = new RMONMoeda;
            if ($_REQUEST["inHdnMoeda"]) {
                $obRMONMoeda->setCodMoeda( $_REQUEST["inHdnMoeda"] );
                $obRMONMoeda->listarMoeda($rsLista, $boTransacao );
                if ( !$rsLista->eof() ) {
                    $js .= "f.inMoeda.value = '".$_REQUEST["inHdnMoeda"]."';\n";
                    $js .= "d.getElementById('stMoeda').innerHTML = '".$rsLista->getCampo('descricao_singular')."';\n";
                    $js .= "f.inSimboloMoeda.value = '".$rsLista->getCampo('simbolo')."';\n";
                    $arValoresSessao["stMoeda"]        = $rsLista->getCampo("descricao_singular");
                    $arValoresSessao["inSimboloMoeda"] = $rsLista->getCampo("simbolo");
                    $arValoresSessao["inMoeda"]        = $_REQUEST["inHdnMoeda"];
                }
            }
        } else {
            $obRMONIndicador = new RMONIndicadorEconomico;
            if ($_REQUEST["inHdnIndicadorEconomico"]) {
                $obRMONIndicador->setCodIndicador( $_REQUEST["inHdnIndicadorEconomico"] );
                $obRMONIndicador->listarIndicadores($rsLista, $boTransacao );
                if ( !$rsLista->eof() ) {
                    $js .= "f.inIndicadorEconomico.value = '".$_REQUEST["inHdnIndicadorEconomico"]."';\n";
                    $js .= 'd.getElementById("stIndicadorEconomico").innerHTML = "'.$rsLista->getCampo("descricao").'";';
                    $js .= 'f.inAbreviatura.value = "'.$rsLista->getCampo("abreviatura").'";';

                    $arValoresSessao["inAbreviatura"]        = $rsLista->getCampo("abreviatura");
                    $arValoresSessao["inIndicadorEconomico"] = $_REQUEST["inHdnIndicadorEconomico"];
                    $arValoresSessao["stIndicadorEconomico"] = $rsLista->getCampo("descricao");
                }
            }
        }

        $obRMONCredito = new RMONCredito;
        if ($_REQUEST["inCreditoDivida"]) {
            $arCodigos = explode( ".", $_REQUEST["inCreditoDivida"] );
            if (count ($arCodigos) == 4) {
                $obRMONCredito->setCodCredito( $arCodigos[0] );
                $obRMONCredito->setCodEspecie( $arCodigos[1] );
                $obRMONCredito->setCodGenero( $arCodigos[2] );
                $obRMONCredito->setCodNatureza( $arCodigos[3] );
                $obRMONCredito->listarCreditosPopUp( $rsLista );
                if ( !$rsLista->eof() ) {
                    $js .= 'd.getElementById("stCreditoDivida").innerHTML = "'.$rsLista->getCampo("descricao_credito").'";';
                }
            }
        }

        Sessao::write('valores', $arValoresSessao);
        SistemaLegado::executaFrameOculto($js);
        break;
}

?>
