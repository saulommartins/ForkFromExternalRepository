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
    * Página Oculta do Registrar/Importar Evento
    * Data de CriAção   : 19/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore
    $Id: OCImportarRegistroEvento.php 66156 2016-07-22 13:16:30Z carlos.silva $

    * Caso de uso: uc-04.05.49

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpanOpcoes(Request $request)
{
    Sessao::remove("arLoteMatriculas");
    Sessao::remove("arLoteEventos");

    switch ($request->get("stOpcao")) {
        case "lote_evento":
            $stJs = gerarSpanLoteEvento($request);
        break;
        case "lote_matricula":
            $stJs = gerarSpanLoteMatricula();
        break;
        case "importar":
            $stJs = gerarSpanImportar();
        break;
    }

    return $stJs;
}

function preencherDadosEvento(Request $request)
{
    $inContrato        = $request->get("inContrato","");
    $inCodigoEvento    = $request->get("inCodigoEvento","");
    $stProporcional    = $request->get('stProporcional',"");
    $stTipoFiltro      = $request->get('stTipoFiltro',"");
    $boQuebrarDisabled = $request->get('boQuebrarDisabled',"");

    $rsEvento = new RecordSet();

    if ($inCodigoEvento != "") {
        $inCodigoEvento = str_pad($inCodigoEvento,strlen(Sessao::read("stMascaraEvento")),"0",STR_PAD_LEFT);

        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = " AND codigo = '".$inCodigoEvento."'";
        $obTFolhaPagamentoEvento->recuperaEventos($rsEvento,$stFiltro);
    }

    $obErro = new erro();
    if ( $rsEvento->getNumLinhas() == -1 )
        $obErro->setDescricao("@Código de evento inválido!(".$inCodigoEvento.")");
    if ( !$obErro->ocorreu() and $rsEvento->getCampo("evento_sistema") == "t")
        $obErro->setDescricao("@Código de evento inválido!(Evento ".$inCodigoEvento." é um evento de sistema)");

    if ( !$obErro->ocorreu() ) {
        $inCodEvento        = $rsEvento->getCampo('cod_evento');
        $stDescricao        = $rsEvento->getCampo('descricao');
        $stObservacao       = $rsEvento->getCampo('observacao');
        $stNatureza         = $rsEvento->getCampo('proventos_descontos');
        $stTipo             = ($rsEvento->getCampo('tipo') == "V") ? "Variável" : "Fixo";
        $stFixado           = $rsEvento->getCampo("fixado");
        $stLimiteCalculo    = $rsEvento->getCampo("limite_calculo");
        $boApresentaParcela = $rsEvento->getCampo("apresenta_parcela");
        Sessao::write("fixado",$stFixado);
        Sessao::write("limite_calculo",$stLimiteCalculo);
        Sessao::write("stTextoComplementar",$stObservacao);
        Sessao::write("stNatureza",$stNatureza);
        Sessao::write("stTipo",$stTipo);
        Sessao::write("HdninCodigoEvento",$inCodEvento);
        $boIncluir = "false";
        $boLimpar  = "false";
    } else {
        $inCodContrato      = 0;
        $inCodEvento        = 0;
        $inCodigoEvento     = '';
        $stDescricao        = '&nbsp;';
        $stObservacao       = '&nbsp;';
        $stNatureza         = '&nbsp;';
        $stTipo             = '&nbsp;';
        $stFixado           = '';
        $stLimiteCalculo    = '';
        $boApresentaParcela = '';
        Sessao::remove("fixado");
        Sessao::remove("limite_calculo");
        Sessao::remove("stTextoComplementar");
        Sessao::remove("stNatureza");
        Sessao::remove("stTipo");
        Sessao::remove("HdninCodigoEvento");
        if ($inCodigoEvento != "")
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        $boIncluir = "true";
        $boLimpar  = "true";
    }

    $stJs .= "jq('#inCodigoEvento').val('".$inCodigoEvento."');\n";
    $stJs .= "jq('#stEvento').html('".$stDescricao."');\n";
    $stJs .= "jq('#hdnDescEvento').val('".$stDescricao."');\n";
    $stJs .= "jq('#HdninCodigoEvento').val('".$inCodEvento."');\n";
    $stJs .= "jq('#stTextoComplementar').html('".$stObservacao."');\n";
    $stJs .= "jq('#stNatureza').html('".$stNatureza."');\n";
    $stJs .= "jq('#stTipo').html('".$stTipo."');\n";

    if ($inContrato != "" and $inCodigoEvento != "" and !$obErro->ocorreu()) {
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$inContrato;
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $inCodContrato      = $rsContrato->getCampo("cod_contrato");

        $stJs .= gerarSpanRegistroEvento($inCodEvento,$inCodContrato,$stFixado,$stTipo,$stLimiteCalculo,$boApresentaParcela);
        $stJs .= peencherDadosRegistroEvento($inCodEvento,$inCodContrato,$stProporcional);
    }else if($inCodigoEvento != "" and !$obErro->ocorreu() && $stTipoFiltro !=""){
        $stJs .= gerarSpanRegistroEvento($inCodEvento,0,$stFixado,$stTipo,$stLimiteCalculo,$boApresentaParcela,$stTipoFiltro);
    }

    return $stJs;
}

function peencherDadosRegistroEvento($inCodEvento,$inCodContrato,$stProporcional)
{
    if ($inCodEvento != 0 and $inCodContrato != 0) {
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php";
        $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
        $boProporcional  = ( $stProporcional == "Sim" ) ? "TRUE" : "FALSE";
        $stFiltro .= " AND evento.cod_evento = ".$inCodEvento;
        $stFiltro .= " AND contrato.cod_contrato = ".$inCodContrato;
        $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND proporcional IS ".$boProporcional;
        $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro);

        if ($rsRegistroEvento->getNumLinhas() >= 1) {
            $rsRegistroEvento->addFormatacao("valor","NUMERIC_BR");
            $rsRegistroEvento->addFormatacao("quantidade","NUMERIC_BR");
            $nuValorEvento = $rsRegistroEvento->getCampo("valor");
            $nuQuantidadeEvento = $rsRegistroEvento->getCampo("quantidade");
            $nuQuantidadeParcelasEvento = $rsRegistroEvento->getCampo("parcela");
            $inMesCarencia = $rsRegistroEvento->getCampo("mes_carencia");
            $stExcluirLancamento = "true";

            $stJs .= "jq('#boExcluirLancamento').prop('disabled', false);\n";
            $stJs .= "jq('#boExcluirLancamentoDisabled').val('false');\n";
        } else {
            $nuValorEvento = "";
            $nuQuantidadeEvento = "";
            $nuQuantidadeParcelasEvento = "";
            $inMesCarencia = 0;
            $stExcluirLancamento = "false";

            $stJs .= "jq('#boExcluirLancamento').prop('disabled', true);\n";
            $stJs .= "jq('#boExcluirLancamentoDisabled').val('true');\n";
        }

        if (Sessao::read("fixado") == "V") {
            $stJs .= "jq('#nuValorEvento').val('".$nuValorEvento."');\n";
            $stJs .= "jq('#nuQuantidadeEvento').val('".$nuQuantidadeEvento."');\n";
        } else {
            $stJs .= "jq('#nuQuantidadeEvento').val('".$nuQuantidadeEvento."');\n";
        }

        if (Sessao::read("limite_calculo") == "t") {
            $stJs .= "jq('#nuQuantidadeParcelasEvento').val('".$nuQuantidadeParcelasEvento."');\n";
            $stJs .= "jq('#inMesCarenciaEvento').val('".$inMesCarencia."');\n";
        }
    }

    return $stJs;
}

function gerarSpanRegistroEvento($inCodEvento,$inCodContrato,$stFixado,$stTipo,$stLimiteCalculo,$boApresentaParcela,$stTipoFiltro = '')
{
    $boGera = false;

    if ($stTipoFiltro != "") {
        $boGera = true;
    } else {
        if ($inCodEvento != 0 and $inCodContrato != 0) {
            $boGera = true;
        }
    }

    if ($boGera == true) {
        $obTxtValor = new Numerico;
        $obTxtValor->setName      ( "nuValorEvento"                  );
        $obTxtValor->setId        ( "nuValorEvento"                  );
        $obTxtValor->setTitle     ( "Informe o valor a ser lançado." );
        $obTxtValor->setAlign     ( "RIGHT"                          );
        $obTxtValor->setRotulo    ( "**Valor"                        );
        $obTxtValor->setMaxLength ( 14                               );
        $obTxtValor->setMaxValue  ( 999999999.99                     );
        $obTxtValor->setSize      ( 12                               );
        $obTxtValor->setDecimais  ( 2                                );
        $obTxtValor->setNegativo  ( false                            );

        $obTxtQuantidade = new Numerico;
        $obTxtQuantidade->setName      ( "nuQuantidadeEvento"                  );
        $obTxtQuantidade->setId        ( "nuQuantidadeEvento"                  );
        $obTxtQuantidade->setTitle     ( "Informe a quantidade a ser lançada." );
        $obTxtQuantidade->setAlign     ( "RIGHT"                               );

        if ($stFixado == 'Q') {
            $obTxtQuantidade->setRotulo ( "**Quantidade"                       );
        } else {
            $obTxtQuantidade->setRotulo ( "Quantidade"                         );
        }

        $obTxtQuantidade->setMaxLength ( 14                                    );
        $obTxtQuantidade->setMaxValue  ( 999999999.99                          );
        $obTxtQuantidade->setSize      ( 12                                    );
        $obTxtQuantidade->setDecimais  ( 2                                     );

        $obTxtQuantidadeParcelas = new TextBox;
        $obTxtQuantidadeParcelas->setName      ( "nuQuantidadeParcelasEvento"                      );
        $obTxtQuantidadeParcelas->setId        ( "nuQuantidadeParcelasEvento"                      );
        $obTxtQuantidadeParcelas->setRotulo    ( "**Quantidade de Parcelas"                        );
        $obTxtQuantidadeParcelas->setInteiro   ( true                                              );
        $obTxtQuantidadeParcelas->setMaxLength ( 10                                                );
        $obTxtQuantidadeParcelas->setSize      ( 10                                                );
        $obTxtQuantidadeParcelas->setTitle     ( "Informe a quantidade de parcelas a ser lançada." );
        
        $obInCarencia= new Inteiro;
        $obInCarencia->setRotulo ( "Meses de Carência"                                        );
        $obInCarencia->setTitle  ( "Informe a quantidade de meses de carência para o evento." );
        $obInCarencia->setName   ( "inMesCarenciaEvento"                                      );
        $obInCarencia->setValue  ( 0                                                          );
        $obInCarencia->setNull   ( false                                                      );
        
        $obCkbExcluirLancamento = new CheckBox();
        $obCkbExcluirLancamento->setRotulo   ( "Excluir Lançamento"                                           );
        $obCkbExcluirLancamento->setName     ( "boExcluirLancamento"                                          );
        $obCkbExcluirLancamento->setId       ( "boExcluirLancamento"                                          );
        $obCkbExcluirLancamento->setValue    ( "sim"                                                          );
        $obCkbExcluirLancamento->setTitle    ( "Marque a opïção para excluir o evento informado do contrato." );
        $obCkbExcluirLancamento->setDisabled ( true                                                           );

        $obHdnExcluirLancamento = new hidden();
        $obHdnExcluirLancamento->setName ( "boExcluirLancamentoDisabled" );

        $obFormulario = new Formulario;
        if ($stFixado == 'V') {
            $obFormulario->addComponente        ( $obTxtValor                                               );
        }
        $obFormulario->addComponente            ( $obTxtQuantidade                                          );
        if ($stTipo == 'Variável' and  $stLimiteCalculo == "t" and $boApresentaParcela == 't') {
            $obFormulario->addComponente( $obTxtQuantidadeParcelas );
            $obFormulario->addComponente( $obInCarencia );
        }

        $obFormulario->addComponente                ( $obCkbExcluirLancamento );
        $obFormulario->addHidden($obHdnExcluirLancamento);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
    }

    $stJs .= "jq('#spnDadosLoteEvento').html('".$stHTML."');\n";

    return $stJs;
}

#####################################################################################################################
#LOTE DE Matrículas
######################################
function gerarSpanLoteMatricula()
{
    include_once CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php";

    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->setTituloFormulario("Dados da Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->setRotulo("Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");

    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";

    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
    Sessao::write("stMascaraEvento",$stMascaraEvento);

    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $obBscInnerEvento = new BuscaInner;
    $obBscInnerEvento->setRotulo              ( "**Evento"          );
    $obBscInnerEvento->setId                  ( "stEvento"          );
    $obBscInnerEvento->setTitle               ( "Informe o evento a ser lançado." );
    $obBscInnerEvento->obCampoCod->setName    ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setId      ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setValue   ( $inCodigoEvento     );
    $obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"     );
    $obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento );
    $obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
    $obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEvento','stEvento','','".Sessao::getId()."&stNaturezasAceitas=P-I-D&stNaturezaChecked=P&boInformarValorQuantidade=t&boInformarQuantidadeParcelas=t&boSugerirValorQuantidade=f&boEventoSistema=f','800','550')" );
    $obBscInnerEvento->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");
    $obImagemEvento = $obBscInnerEvento->getImagem();
    $obImagemEvento->setId('inImgEvento');
    $obBscInnerEvento->setImagem ($obImagemEvento);

    $obLblTextoComplementar = new Label;
    $obLblTextoComplementar->setRotulo ( "Texto Complementar"  );
    $obLblTextoComplementar->setId     ( "stTextoComplementar" );
    $obLblTextoComplementar->setValue  ( "&nbsp;"              );

    $obLblNatureza = new Label;
    $obLblNatureza->setRotulo ( "Natureza"   );
    $obLblNatureza->setId     ( "stNatureza" );
    $obLblNatureza->setValue  ( "&nbsp;"     );

    $obLblTipo = new Label;
    $obLblTipo->setRotulo ( "Tipo"   );
    $obLblTipo->setId     ( "stTipo" );
    $obLblTipo->setValue  ( "&nbsp;" );

    $obCmbLancarProporcional = new Select();
    $obCmbLancarProporcional->setRotulo("Lançar Somente na Aba Proporcional");
    $obCmbLancarProporcional->setTitle("Marque como SIM, para lançar apenas eventos na aba proporcional (válidos somente para esta competência). Para lançar no registro de eventos fixo/variável, utilizar a opïção NãO.");
    $obCmbLancarProporcional->setName("stProporcional");
    $obCmbLancarProporcional->setValue("Não");
    $obCmbLancarProporcional->addOption("Sim","Sim");
    $obCmbLancarProporcional->addOption("Não","Não");

    $obSpnDadosEvento = new Span();
    $obSpnDadosEvento->setId("spnDadosLoteEvento");

    $obSpanLoteMatricula = new Span();
    $obSpanLoteMatricula->setId("spnLoteMatricula");

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir" );
    $obBtnIncluir->setValue             ( "Incluir"   );
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirLoteMatricula','',true);" );
    $arBarra[] = $obBtnIncluir;

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName              ( "btAlterar" );
    $obBtnAlterar->setValue             ( "Alterar"   );
    $obBtnAlterar->setDisabled          ( true        );
    $obBtnAlterar->obEvento->setOnClick ( "montaParametrosGET( 'alterarLoteMatricula', '', true  );" );
    $arBarra[] = $obBtnAlterar;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar" );
    $obBtnLimpar->setValue             ( "Limpar"   );
    $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparLoteMatricula','',true);");
    $arBarra[] = $obBtnLimpar;

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario( $obFormulario );
    $obFormulario->addTitulo    ( "Dados do Evento" );
    $obFormulario->addComponente( $obCmbLancarProporcional );
    $obFormulario->addComponente( $obBscInnerEvento        );
    $obFormulario->addComponente( $obLblTextoComplementar  );
    $obFormulario->addComponente( $obLblNatureza           );
    $obFormulario->addComponente( $obLblTipo               );
    $obFormulario->addSpan      ( $obSpnDadosEvento        );
    $obFormulario->defineBarra( $arBarra , "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;");
    $obFormulario->addSpan($obSpanLoteMatricula);
    $obFormulario->montaInnerHTML();

    $stJs .= "jq('#spnOpcao').html('".$obFormulario->getHTML()."');\n";
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= "jq('#inContrato').focus();\n";
    $stJs .= "jq('#btIncluir').prop('disabled', true);\n";
    $stJs .= "jq('#btLimpar').prop('disabled', true);\n";

    return $stJs;
}

function gerarListaLoteMatricula()
{
    $rsLoteMatricula = new recordset;
    $arLoteMatricula = ( is_array(Sessao::read('arLoteMatriculas')) ) ? Sessao::read('arLoteMatriculas') : array();
    $rsLoteMatricula->preenche($arLoteMatricula);
    $rsLoteMatricula->addFormatacao("valor","NUMERIC_BR");
    $rsLoteMatricula->addFormatacao("quantidade","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsLoteMatricula );
    $obLista->setTitulo     ("Lista de Eventos Registrados para a Matrícula");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Tipo");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Prop");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->ultimoDado->setContar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "desc_evento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "proporcional" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarLoteMatricula');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirLoteMatricula');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->setRotuloSomatorio("Somatório dos Lançamentos");

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "jq('#spnLoteMatricula').html('".$stHtml."');\n";

    return $stJs;
}

function incluirLoteMatricula(Request $request)
{
    $obErro = validarLoteEvento('incluir', $request);
    if ( !$obErro->ocorreu() ) {
        $inCodEvento = $request->get("HdninCodigoEvento");
        $inCodEvento = (!empty($inCodEvento)) ? $inCodEvento : Sessao::read("HdninCodigoEvento");
        $arLoteMatriculas = Sessao::read("arLoteMatriculas");
        $arTemp["inId"]         = count($arLoteMatriculas);
        $arTemp["registro"]     = $request->get("inContrato");
        $arTemp["nom_cgm"]      = getNomeCGM($request->get("inContrato"));
        $arTemp["codigo"]       = $request->get("inCodigoEvento");
        $arTemp["cod_evento"]   = $inCodEvento;
        $arTemp["desc_evento"]  = $request->get("hdnDescEvento");
        $arTemp["texto_comp"]   = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]     = Sessao::read("stNatureza");
        $arTemp["tipo"]         = Sessao::read("stTipo");
        $arTemp["proporcional"] = $request->get("stProporcional");
        $arTemp["valor"]        = str_replace(',','.',str_replace('.','',$request->get("nuValorEvento")));
        $arTemp["quantidade"]   = str_replace(',','.',str_replace('.','',$request->get("nuQuantidadeEvento")));
        $arTemp["parcelas"]     = $request->get("nuQuantidadeParcelasEvento");
        $arTemp["boExcluir"]    = $request->get("boExcluirLancamento");
        $arTemp["boExcluirDisabled"] = $request->get("boExcluirLancamentoDisabled");
        $arTemp["mes_carencia"]  = $request->get("inMesCarenciaEvento");

        $arLoteMatriculas[] = $arTemp;
        Sessao::write("arLoteMatriculas",$arLoteMatriculas);

        $stJs .= gerarListaLoteMatricula();
        $stJs .= limparLoteMatricula();
        $stJs .= "d.getElementById('inCodigoEvento').focus();\n";
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarLoteMatricula(Request $request)
{
    $obErro = validarLoteEvento("alterar", $request);
    if ( !$obErro->ocorreu() ) {
        $inCodEvento = $request->get("HdninCodigoEvento");
        $inCodEvento = (!empty($inCodEvento)) ? $inCodEvento : Sessao::read("HdninCodigoEvento");
        $arLoteMatriculas = Sessao::read("arLoteMatriculas");
        $arTemp["inId"]              = Sessao::read("inId");
        $arTemp["registro"]          = $request->get("inContrato");
        $arTemp["nom_cgm"]           = getNomeCGM($request->get("inContrato"));
        $arTemp["codigo"]            = $request->get("inCodigoEvento");
        $arTemp["cod_evento"]        = $inCodEvento;
        $arTemp["desc_evento"]       = $request->get("hdnDescEvento");
        $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]          = Sessao::read("stNatureza");
        $arTemp["tipo"]              = Sessao::read("stTipo");
        $arTemp["proporcional"]      = $request->get("stProporcional");
        $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$request->get("nuValorEvento")));
        $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$request->get("nuQuantidadeEvento")));
        $arTemp["parcelas"]          = $request->get("nuQuantidadeParcelasEvento");
        $arTemp["boExcluir"]         = $request->get("boExcluirLancamento");
        $arTemp["boExcluirDisabled"] = $request->get("boExcluirLancamentoDisabled");
        $arTemp["mes_carencia"]      = $request->get("inMesCarenciaEvento");

        $arLoteMatriculas[Sessao::read("inId")] = $arTemp;

        Sessao::write("arLoteMatriculas",$arLoteMatriculas);
        $stJs .= gerarListaLoteMatricula();
        $stJs .= limparLoteMatricula();

        $stJs .= "document.frm.btAlterar.disabled = true;\n";
        $stJs .= "document.frm.btIncluir.disabled = false;\n";
        Sessao::remove("inId");
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirLoteMatricula(Request $request)
{
    $inId = $request->get('inId');
    $arTemp = array();
    $arLoteMatriculas = Sessao::read("arLoteMatriculas");
    for ( $i=0; $i<count($arLoteMatriculas); $i++ ) {
        if ($arLoteMatriculas[$i]['inId'] != $inId) {
            $arTemp[] = $arLoteMatriculas[$i];
        }
    }
    Sessao::write("arLoteMatriculas",$arTemp);
    $stJs .= gerarListaLoteMatricula();
    $stJs .= limparLoteMatricula();

    return $stJs;
}

function limparLoteMatricula()
{
    $stJs .= "jq('#spnDadosLoteEvento').html('');         \n";
    $stJs .= "jq('#stEvento').html('&nbsp;');             \n";
    $stJs .= "jq('#hdnDescEvento').val('');               \n";
    $stJs .= "jq('#HdninCodigoEvento').val('');           \n";
    $stJs .= "jq('#inCodigoEvento').val('');              \n";
    $stJs .= "jq('#stTextoComplementar').html('&nbsp;');  \n";
    $stJs .= "jq('#stNatureza').html('&nbsp;');           \n";
    $stJs .= "jq('#stTipo').html('&nbsp;');               \n";
    $stJs .= "jq('#stProporcional').val('Não');           \n";
    $stJs .= "jq('input[name=btAlterar]').prop('disabled',true);  \n";
    $stJs .= "jq('input[name=btIncluir]').prop('disabled',false); \n";

    return $stJs;
}

function montaAlterarLoteMatricula(Request $request)
{
    $inId = $request->get('inId');
    Sessao::write("inId",$inId);
    $arLoteMatriculas = Sessao::read("arLoteMatriculas");
    $arLoteEvento = $arLoteMatriculas[$inId];
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = " WHERE codigo = '".$arLoteEvento["codigo"]."'";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " WHERE registro = ".$arLoteEvento["registro"];
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);

    $stJs .= gerarSpanRegistroEvento($rsEvento->getCampo("cod_evento"),$rsContrato->getCampo("cod_contrato"),$rsEvento->getCampo("fixado"),$arLoteEvento["tipo"],$rsEvento->getCampo("limite_calculo"),$rsEvento->getCampo("apresenta_parcela"));
    $stJs .= "d.getElementById('stEvento').innerHTML = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.inCodigoEvento.value = '".$arLoteEvento["codigo"]."';\n";
    $stJs .= "d.frm.hdnDescEvento.value = '".$arLoteEvento["desc_evento"]."';\n";
    $stJs .= "f.HdninCodigoEvento.value = '".$arLoteEvento["cod_evento"]."';\n";
    $stJs .= "d.getElementById('stTextoComplementar').innerHTML = '".$arLoteEvento["texto_comp"]."';\n";
    $stJs .= "d.getElementById('stNatureza').innerHTML = '".$arLoteEvento["natureza"]."';\n";
    $stJs .= "d.getElementById('stTipo').innerHTML = '".$arLoteEvento["tipo"]."';\n";
    $stJs .= "f.stProporcional.value = '".$arLoteEvento["proporcional"]."';\n";
    $stJs .= "f.inContrato.value = '".$arLoteEvento["registro"]."';";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".$arLoteEvento["nom_cgm"]."';\n";
    if ($arLoteEvento["quantidade"] != "") {
        $stJs .= "f.nuQuantidadeEvento.value = '".number_format($arLoteEvento["quantidade"],2,',','.')."';\n";
    }
    if ($arLoteEvento["valor"] != "") {
        $stJs .= "f.nuValorEvento.value = '".number_format($arLoteEvento["valor"],2,',','.')."';\n";
    }
    if ($arLoteEvento["parcelas"] != "") {
        $stJs .= "f.nuQuantidadeParcelasEvento.value = '".$arLoteEvento["parcelas"]."';\n";
    }

    $stJs .= "if(f.inMesCarenciaEvento){\n";
    if ($arLoteEvento["mes_carencia"] != "") {
        $stJs .= "f.inMesCarenciaEvento.value = '".$arLoteEvento["mes_carencia"]."';\n";
    } else {
        $stJs .= "f.inMesCarenciaEvento.value = 0;\n";
    }
    $stJs .= "}\n";
    
    if ($arLoteEvento["boExcluirDisabled"] != "") {
        $stJs .= "d.getElementById('boExcluirLancamento').disabled = ".$arLoteEvento["boExcluirDisabled"].";\n";
        $stJs .= "f.boExcluirLancamentoDisabled.value = '".$arLoteEvento["boExcluirDisabled"]."';";
    }
    if ($arLoteEvento["boExcluir"]) {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = true;\n";
    } else {
        $stJs .= "d.getElementById('boExcluirLancamento').checked = false;\n";
    }
    $stJs .= "document.frm.btAlterar.disabled = false;\n";
    $stJs .= "document.frm.btIncluir.disabled = true;\n";

    return $stJs;
}

#####################################################################################################################
#LOTE DE EVENTO
######################################
function gerarSpanLoteEvento(Request $request)
{
    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";

    $obIFiltroComponentes = new IFiltroComponentes;
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setLocal();    
    $obIFiltroComponentes->setEvento();
    $stOnChange = "montaParametrosGET('gerarSpanMatricula','inCodigoEvento,stProporcional,stTipoFiltro,boQuebrarDisabled',true);";

    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
    //Define a mascara do campo Evento
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
    Sessao::write("stMascaraEvento",$stMascaraEvento);

    $obBscInnerEvento = new BuscaInner;
    $obBscInnerEvento->setRotulo              ( "Evento"         );
    $obBscInnerEvento->setId                  ( "stEvento"       );
    $obBscInnerEvento->setTitle               ( "Informe o evento a ser lançado." );
    $obBscInnerEvento->obCampoCod->setName    ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setId      ( "inCodigoEvento"    );
    $obBscInnerEvento->obCampoCod->setValue   ( $inCodigoEvento     );
    $obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"     );
    $obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento );
    $obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
    $obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEvento','stEvento','','".Sessao::getId()."&stNaturezasAceitas=P-I-D&stNaturezaChecked=P&boInformarValorQuantidade=t&boInformarQuantidadeParcelas=t&boSugerirValorQuantidade=f&boEventoSistema=f','800','550')" );
    $obBscInnerEvento->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherDadosEvento','inCodigoEvento,stProporcional,stTipoFiltro,boQuebrarDisabled',true);");
    $obImagemEvento = $obBscInnerEvento->getImagem();
    $obImagemEvento->setId('inImgEvento');
    $obBscInnerEvento->setImagem ($obImagemEvento);

    $obLblTextoComplementar = new Label;
    $obLblTextoComplementar->setRotulo ( "Texto Complementar"  );
    $obLblTextoComplementar->setId     ( "stTextoComplementar" );
    $obLblTextoComplementar->setValue  ( "&nbsp;"              );

    $obLblNatureza = new Label;
    $obLblNatureza->setRotulo ( "Natureza"   );
    $obLblNatureza->setId     ( "stNatureza" );
    $obLblNatureza->setValue  ("&nbsp;"      );

    $obLblTipo = new Label;
    $obLblTipo->setRotulo ( "Tipo"   );
    $obLblTipo->setId     ( "stTipo" );
    $obLblTipo->setValue  ( "&nbsp;" );

    $obCmbLancarProporcional = new Select();
    $obCmbLancarProporcional->setRotulo ( "Lançar Somente na Aba Proporcional" );
    $obCmbLancarProporcional->setTitle  ( "Marque como SIM, para lançar apenas eventos na aba proporcional (válidos somente para esta competência). Para lançar no registro de eventos fixo/variável, utilizar a opição Não." );
    $obCmbLancarProporcional->setName   ( "stProporcional" );
    $obCmbLancarProporcional->setId     ( "stProporcional" );
    $obCmbLancarProporcional->setValue  ( "Não" );
    $obCmbLancarProporcional->addOption ( "Sim","Sim" );
    $obCmbLancarProporcional->addOption ( "Não","Não" );

    $obSpnDadosEvento = new Span();
    $obSpnDadosEvento->setId ( "spnDadosLoteEvento" );

    $obSpanLoteEvento = new Span();
    $obSpanLoteEvento->setId ( "spnLoteEvento" );

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir" );
    $obBtnIncluir->setValue             ( "Incluir" );
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET( 'incluirLoteEvento','',true);" );
    $arBarra[] = $obBtnIncluir;

    $obBtnAlterar = new Button;
    $obBtnAlterar->setName              ( "btAlterar" );
    $obBtnAlterar->setValue             ( "Alterar" );
    $obBtnAlterar->setDisabled          ( true );
    $obBtnAlterar->obEvento->setOnClick ( "montaParametrosGET( 'alterarLoteEvento', '', true  );" );
    $arBarra[] = $obBtnAlterar;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar" );
    $obBtnLimpar->setValue             ( "Limpar" );
    $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limparLoteEvento','',true);" );
    $arBarra[] = $obBtnLimpar;

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Dados do Evento"        );
    $obFormulario->addComponente ( $obBscInnerEvento        );
    $obFormulario->addComponente ( $obLblTextoComplementar  );
    $obFormulario->addComponente ( $obLblNatureza           );
    $obFormulario->addComponente ( $obLblTipo               );
    $obFormulario->addComponente ( $obCmbLancarProporcional );

    $obIFiltroComponentes->geraFormulario( $obFormulario );
    $obIFiltroComponentes->obCmbTipoFiltro->obEvento->setOnChange($stOnChange);

    $obFormulario->addSpan ( $obSpnDadosEvento );
    $obFormulario->defineBarra ( $arBarra , "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" );
    $obFormulario->addSpan ( $obSpanLoteEvento );
    $obFormulario->montaInnerHTML();

    $stJs  = "jq('#spnOpcao').html('".$obFormulario->getHTML()."');\n";
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= "jq('#inCodigoEvento').focus();\n";
    $stJs .= "jq('#btIncluir').prop('disabled', true);\n";
    $stJs .= "jq('#btLimpar').prop('disabled', true);\n";

    return $stJs;
}

function validarLoteEvento($stAcao="incluir", Request $request)
{
    $obErro = new Erro();

    $inCodigoEvento = $request->get("inCodigoEvento","");

    if ($inCodigoEvento == "") {
        $stErro .= "@Campo Evento inválido!()";
    }

    if ($stErro == "") {
        switch ($request->get("stTipoFiltro")) {
            case 'contrato':
                $inContrato = $request->get("inContrato","");
                if ($inContrato == "") {
                    $stErro .= "@Campo Tipo de Filtro (Matrícula) inválido!()";
                }
                $registro = $inContrato;
            break;

            case 'lotacao':
                $arCodLotacaoSelecionados = $request->get("inCodLotacaoSelecionados","");
                if ($arCodLotacaoSelecionados = "") {
                    $stErro .= "@Campo Tipo de Filtro (Lotação) inválido!()";
                }
                $registro = $arCodLotacaoSelecionados;
            break;

            case 'local':
                $arCodLocalSelecionados = $request->get("inCodLocalSelecionados","");
                if ($arCodLocalSelecionados = "") {
                    $stErro .= "@Campo Tipo de Filtro (Local) inválido!()";
                }
                $registro = $arCodLocalSelecionados;
            break;

            case 'evento':
                $inCodigoInnerEvento = $request->get("inCodigoInnerEvento","");
                if ($inCodigoInnerEvento = "") {
                    $stErro .= "@Campo Tipo de Filtro (Evento) inválido!()";
                }
                $registro = $inCodigoInnerEvento;
            break;

            // default = Geral
            default:
                $registro = "";
            break;
        }
    }

    if ($stErro == "") {
        if (Sessao::read("fixado") == "V" and $request->get("nuValorEvento","") == "") {
            $stErro .= "@Campo Valor inválido!()";
        }

        if (Sessao::read("fixado") == "Q" and $request->get("nuQuantidadeEvento","") == "") {
            $stErro .= "@Campo Quantidade inválido!()";
        }

        $nuQuantidadeParcelasEvento = $request->get("nuQuantidadeParcelasEvento");

        if ($nuQuantidadeParcelasEvento != null && $nuQuantidadeParcelasEvento == "") {
            $stErro .= "@Campo Quantidade de Parcelas inválido!()";
        }

        $inCodigoEvento = str_pad($inCodigoEvento,strlen(Sessao::read("stMascaraEvento")),"0",STR_PAD_LEFT);
        $arLoteEventos = Sessao::read("arLoteEventos");
    
        if (is_array($arLoteEventos) and $stAcao == "incluir") {
            foreach ($arLoteEventos as $arLoteEvento) {
                if (($arLoteEvento["proporcional"] == $request->get("stProporcional","")) &&
                    ($arLoteEvento["registro"] == $registro) &&
                    ($arLoteEvento["codigo"] == $inCodigoEvento)
                   ) {
                    $stErro .= "@O evento ".$inCodigoEvento." já foi incluído para o contrato ".$request->get("inContrato")."!()";
                    break;
                }
            }
        }

        $arLoteMatriculas = Sessao::read("arLoteMatriculas");

        if (is_array($arLoteMatriculas) and $stAcao == "incluir") {
            foreach ($arLoteMatriculas as $arLoteMatricula) {
                if ($arLoteMatricula["proporcional"] == $request->get("stProporcional") and $arLoteMatricula["registro"] == $request->get("inContrato") and $arLoteMatricula["codigo"] == $inCodigoEvento) {
                    $stErro .= "@O evento ".$inCodigoEvento." já foi incluído para o contrato ".$request->get("inContrato")."!()";
                    break;
                }
            }
        }

        if ($inCodigoEvento != "") {
            include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php";
            $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
            $stFiltro  = " AND codigo = '".$inCodigoEvento."'";
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 1";
            $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRelacionamentoConfiguracao( $rsEvento, $stFiltro);
            if (!$obErro->ocorreu()) {
                if ($rsEvento->getNumLinhas() < 0) {
                    $stErro .= "@O evento informado não possui configuração para a subdivisïção/cargo e/ou especialidade do contrato em manutenção.";
                }else{
                    $request->set('hdnDescEvento', $rsEvento->getCampo("descricao"));
                }
            }
        }
    }

    $obErro->setDescricao($stErro);

    return $obErro;
}

function getNomeCGM($inRegistro)
{
    $stNomCgm = "";

    // Busca o nome do servidor
    include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " AND registro = ".$inRegistro;
    $obTPessoalContrato->recuperaCgmDoRegistroServidor($rsCgm,$stFiltro);

    if ($rsCgm->getNumLinhas() == -1) {
        // Busca o nome do pensionista
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalPensionista.class.php";
        $obTPessoalPensionista = new TPessoalPensionista();
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCgm,$stFiltro);
    }

    $stNomCgm = $rsCgm->getCampo("numcgm")." - ".$rsCgm->getCampo("nom_cgm");

    return $stNomCgm;
}

function gerarListaLoteEvento()
{
    $rsLoteEvento = new recordset;
    $arLoteEvento = ( is_array(Sessao::read('arLoteEventos')) ) ? Sessao::read('arLoteEventos') : array();
    $rsLoteEvento->preenche($arLoteEvento);
    $rsLoteEvento->addFormatacao("valor","NUMERIC_BR");
    $rsLoteEvento->addFormatacao("quantidade","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsLoteEvento );
    $obLista->setTitulo     ("Lista de Matrículas");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Prop");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->ultimoDado->setContar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->ultimoDado->setContar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "proporcional" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->ultimoDado->setSomar();
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarLoteEvento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirLoteEvento');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->setRotuloSomatorio("Somatório dos Lançamentos");

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "document.getElementById('spnLoteEvento').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function limparLoteEvento()
{
    $stJs .= "jq('#spnTipoFiltro').html('');                        \n";
    $stJs .= "jq('#spnDadosLoteEvento').html('');                   \n";
    $stJs .= "jq('input[name=hdnDescEvento]').val('');              \n";
    $stJs .= "jq('input[name=HdninCodigoEvento]').val('');          \n";
    $stJs .= "jq('#inCodigoEvento').val('');                        \n";
    $stJs .= "jq('#stEvento').html('&nbsp;');                       \n";
    $stJs .= "jq('#inCodigoEvento').attr('readonly', false);        \n";
    $stJs .= "jq('#inImgEvento').attr('hidden', false);             \n";
    $stJs .= "jq('#stTextoComplementar').html('&nbsp;');            \n";
    $stJs .= "jq('#stNatureza').html('&nbsp;');                     \n";
    $stJs .= "jq('#stTipo').html('&nbsp;');                         \n";
    if (Sessao::read("limite_calculo") == "t")
        $stJs .= "jq('#nuQuantidadeParcelasEvento').val('');        \n";
    $stJs .= "jq('#stProporcional').val('Não');                     \n";
    $stJs .= "jq('#stTipoFiltro').prop('disabled',false);           \n";
    $stJs .= "jq('#stTipoFiltro').val('');                          \n";
    $stJs .= "jq('input[name=btAlterar]').prop('disabled',true);    \n";
    $stJs .= "jq('input[name=btIncluir]').prop('disabled',false);   \n";

    return $stJs;
}

function incluirLoteEvento(Request $request)
{
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoPeriodo.class.php";
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";

    $obErro = validarLoteEvento('incluir', $request);

    $inCodigoEvento = $request->get("inCodigoEvento","");

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $inCodPeriodoMovimentacao = $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");

    switch ($request->get("stTipoFiltro")) {
        case 'lotacao':
            Sessao::write("arLoteEventos","");
            $arCodLotacaoSelecionados = $request->get("inCodLotacaoSelecionados","");
            $arCodLotacaoSelecionados = implode(",",$arCodLotacaoSelecionados);
            $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_orgao",$arCodLotacaoSelecionados);
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
            $obTFolhaPagamentoRegistroEventoPeriodo->recuperaContratosDeLotacao($rsRecordSet);
        break;

        case 'local':
            Sessao::write("arLoteEventos","");
            $arCodLocalSelecionados = $request->get("inCodLocalSelecionados","");
            $arCodLocalSelecionados = implode(",",$arCodLocalSelecionados);
            $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_local",$arCodLocalSelecionados);
            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_periodo_movimentacao",$inCodPeriodoMovimentacao);
            $obTFolhaPagamentoRegistroEventoPeriodo->recuperaContratosDeLocal($rsRecordSet);
        break;

        case 'evento':
            Sessao::write("arLoteEventos","");
            $inCodigoInnerEvento = (integer)$request->get("inCodigoInnerEvento","");
            $stFiltro = " WHERE contratos_calculados.cod_evento = ".$inCodigoInnerEvento."
                            AND contratos_calculados.cod_periodo_movimentacao = ".$inCodPeriodoMovimentacao."
                        \n";
            $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
            $obTFolhaPagamentoEventoCalculado->setDado ("boFiltroEvento", true);
            $obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsRecordSet, $stFiltro);
        break;

        case "geral":
            $stOrdem = " GROUP BY cod_contrato, registro, numcgm, nom_cgm ORDER BY registro, nom_cgm";

            $obTFolhaPagamentoRegistroEventoPeriodo = new TFolhaPagamentoRegistroEventoPeriodo;
            $obTFolhaPagamentoRegistroEventoPeriodo->recuperaContratoGeral($rsRecordSet, $stFiltro, $stOrdem);
        break;
    }

    if ( !$obErro->ocorreu() ) {
        $arTemp = array();
        $arLoteTemp = array();

        if ($request->get("stTipoFiltro") == "contrato") {
            $arLoteEventos = Sessao::read("arLoteEventos");
            $arLoteTemp = $arLoteEventos;
            $arTemp["inId"]              = count($arLoteEventos);
            $arTemp["registro"]          = $request->get("inContrato");
            $arTemp["nom_cgm"]           = getNomeCGM($request->get("inContrato"));
            $arTemp["codigo"]            = $request->get("inCodigoEvento");
            $arTemp["cod_evento"]        = Sessao::read("HdninCodigoEvento");
            $arTemp["desc_evento"]       = $request->get("hdnDescEvento");
            $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
            $arTemp["natureza"]          = Sessao::read("stNatureza");
            $arTemp["tipo"]              = Sessao::read("stTipo");
            $arTemp["proporcional"]      = $request->get("stProporcional");
            $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$request->get("nuValorEvento")));
            $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$request->get("nuQuantidadeEvento")));
            $arTemp["parcelas"]          = $request->get("nuQuantidadeParcelasEvento");
            $arTemp["boExcluir"]         = $request->get("boExcluirLancamento");
            $arTemp["boExcluirDisabled"] = $request->get("boExcluirLancamentoDisabled");
            $arTemp["mes_carencia"]      = $request->get("inMesCarenciaEvento");

            $arLoteTemp[] = $arTemp;
        } else {
            foreach ($rsRecordSet->getElementos() as $index => $value) {
                $arTemp["inId"]              = $index;
                $arTemp["registro"]          = $value["registro"];
                $arTemp["nom_cgm"]           = $value["nom_cgm"];
                $arTemp["codigo"]            = $request->get("inCodigoEvento");
                $arTemp["cod_evento"]        = Sessao::read("HdninCodigoEvento");
                $arTemp["desc_evento"]       = $request->get("hdnDescEvento");
                $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
                $arTemp["natureza"]          = Sessao::read("stNatureza");
                $arTemp["tipo"]              = Sessao::read("stTipo");
                $arTemp["proporcional"]      = $request->get("stProporcional");
                $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$request->get("nuValorEvento")));
                $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$request->get("nuQuantidadeEvento")));
                $arTemp["parcelas"]          = $request->get("nuQuantidadeParcelasEvento");
                $arTemp["boExcluir"]         = $request->get("boExcluirLancamento");
                $arTemp["boExcluirDisabled"] = $request->get("boExcluirLancamentoDisabled");
                $arTemp["mes_carencia"]      = $request->get("inMesCarenciaEvento");

                $arLoteTemp[] = $arTemp;
            }
        }

        Sessao::write("arLoteEventos",$arLoteTemp);

        $stJs .= gerarListaLoteEvento();
        if($request->get("stTipoFiltro") == "contrato"){
            $stJs .= "jq('#inContrato').val(''); \n";
            $stJs .= "jq('#inNomCGM').html('');  \n";
            $stJs .= "jq('#inContrato').focus(); \n";
        }else{
            $stJs .= limparLoteEvento();
            $stJs .= "jq('#stTipoFiltro').focus();\n";
        }
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarLoteEvento(Request $request)
{
    $obErro = validarLoteEvento("alterar", $request);
    if ( !$obErro->ocorreu() ) {
        $arLoteEventos = Sessao::read("arLoteEventos");
        $inCodEvento = $request->get("HdninCodigoEvento");
        $inCodEvento = (!empty($inCodEvento)) ? $inCodEvento : Sessao::read("HdninCodigoEvento");
        $arTemp["inId"]              = Sessao::read("inId");
        $arTemp["registro"]          = $request->get("inContrato");
        $arTemp["nom_cgm"]           = getNomeCGM($request->get("inContrato"));
        $arTemp["codigo"]            = $request->get("inCodigoEvento");
        $arTemp["cod_evento"]        = $inCodEvento;
        $arTemp["desc_evento"]       = $request->get("hdnDescEvento");
        $arTemp["texto_comp"]        = Sessao::read("stTextoComplementar");
        $arTemp["natureza"]          = Sessao::read("stNatureza");
        $arTemp["tipo"]              = Sessao::read("stTipo");
        $arTemp["proporcional"]      = $request->get("stProporcional");
        $arTemp["valor"]             = str_replace(',','.',str_replace('.','',$request->get("nuValorEvento")));
        $arTemp["quantidade"]        = str_replace(',','.',str_replace('.','',$request->get("nuQuantidadeEvento")));
        $arTemp["parcelas"]          = $request->get("nuQuantidadeParcelasEvento");
        $arTemp["boExcluir"]         = $request->get("boExcluirLancamento");
        $arTemp["boExcluirDisabled"] = $request->get("boExcluirLancamentoDisabled");
        $arTemp["mes_carencia"]      = $request->get("inMesCarenciaEvento");

        $arLoteEventos[Sessao::read("inId")] = $arTemp;
        Sessao::write("arLoteEventos",$arLoteEventos);
        $stJs .= gerarListaLoteEvento();
        $stJs .= limparLoteEvento();

        $stJs .= "document.frm.btAlterar.disabled = true;\n";
        $stJs .= "document.frm.btIncluir.disabled = false;\n";
        Sessao::remove("inId");
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirLoteEvento(Request $request)
{
    $inId = $request->get('inId');
    $arTemp = array();
    $arLoteEventos = Sessao::read("arLoteEventos");
    for ( $i=0; $i<count( $arLoteEventos ); $i++ ) {
        if ($arLoteEventos[$i]['inId'] != $inId) {
            $arTemp[] = $arLoteEventos[$i];
        }
    }
    Sessao::write("arLoteEventos",$arTemp);
    $stJs .= gerarListaLoteEvento();
    $stJs .= limparLoteEvento();

    return $stJs;
}

function montaAlterarLoteEvento(Request $request)
{
    $inId = $request->get('inId');
    Sessao::write("inId",$inId);
    $arLoteEventos = Sessao::read("arLoteEventos");
    $arLoteEvento = $arLoteEventos[$inId];

    $request->set("inContrato", $arLoteEvento["registro"]);
    $request->set("inCodigoEvento", $arLoteEvento["codigo"]);
    $request->set('stProporcional', $arLoteEvento["proporcional"]);

    $stJs .= gerarSpanMatricula('contrato', FALSE);
    $stJs .= preencherDadosEvento($request);
    $stJs .= "jq('#stEvento').html('".$arLoteEvento["desc_evento"]."');                                      \n";
    $stJs .= "jq('#inCodigoEvento').val('".$arLoteEvento["codigo"]."');                                      \n";
    $stJs .= "jq('#hdnDescEvento').val('".$arLoteEvento["desc_evento"]."');                                  \n";
    $stJs .= "jq('#HdninCodigoEvento').val('".$arLoteEvento["cod_evento"]."');                               \n";
    $stJs .= "jq('#stTextoComplementar').html('".$arLoteEvento["texto_comp"]."');                            \n";
    $stJs .= "jq('#stNatureza').html('".$arLoteEvento["natureza"]."');                                       \n";
    $stJs .= "jq('#stTipo').html('".$arLoteEvento["tipo"]."');                                               \n";
    $stJs .= "jq('#stProporcional').val('".$arLoteEvento["proporcional"]."');                                \n";
    $stJs .= "jq('#stTipoFiltro').val('contrato');                                                           \n";
    $stJs .= "jq('#stTipoFiltro').prop('disabled',true);                                                     \n";
    $stJs .= "jq('#inContrato').val('".$arLoteEvento["registro"]."');                                        \n";
    $stJs .= "jq('#inNomCGM').html('".$arLoteEvento["nom_cgm"]."');                                          \n";
    if ( $arLoteEvento["quantidade"] != "" )
        $stJs .= "jq('#nuQuantidadeEvento').val('".number_format($arLoteEvento["quantidade"],2,',','.')."'); \n";
    if ( $arLoteEvento["valor"] != "" )
        $stJs .= "jq('#nuValorEvento').val('".number_format($arLoteEvento["valor"],2,',','.')."');           \n";
    if ( $arLoteEvento["parcelas"] != "" )
        $stJs .= "jq('#nuQuantidadeParcelasEvento').val('".$arLoteEvento["parcelas"]."');                    \n";
    $stJs .= "if(jq('#inMesCarenciaEvento')){                                                                \n";
    if ( $arLoteEvento["mes_carencia"] != "" )
        $stJs .= "  jq('#inMesCarenciaEvento').val('".$arLoteEvento["mes_carencia"]."');                     \n";
    else
        $stJs .= "  jq('#inMesCarenciaEvento').val(0);                                                       \n";
    $stJs .= "}                                                                                              \n";
    if ( $arLoteEvento["boExcluirDisabled"] != '' ){
        $stJs .= "jq('#boExcluirLancamento').prop('disabled',".$arLoteEvento["boExcluirDisabled"].");        \n";
        $stJs .= "jq('#boExcluirLancamentoDisabled').val('".$arLoteEvento["boExcluirDisabled"]."');          \n";
    }
    if ( $arLoteEvento["boExcluir"] )
        $stJs .= "jq('#boExcluirLancamento').prop('checked',true);                                           \n";
    else
        $stJs .= "jq('boExcluirLancamento').prop('checked',false);                                           \n";
    $stJs .= "jq('input[name=btAlterar]').prop('disabled',false);                                            \n";
    $stJs .= "jq('input[name=btIncluir]').prop('disabled',true);                                             \n";
    $stJs .= "jq('input[name=btAlterar]').focus();                                                           \n";
    $stJs .= "jq('#inCodigoEvento').attr('readonly', true);                                                  \n";
    $stJs .= "jq('#inImgEvento').attr('hidden', true);                                                       \n";
    $stJs .= "jq('#inContrato').attr('readonly', true);                                                      \n";
    $stJs .= "jq('#inImgContrato').attr('hidden', true);                                                     \n";

    return $stJs;
}

#####################################################################################################################
#IMPORTAR
######################################
function gerarSpanImportar()
{
    $arColunas[0]['coluna'] = "Matrícula";
    $arColunas[0]['valor']  = "contrato";

    $arColunas[1]['coluna'] = "Evento";
    $arColunas[1]['valor']  = "evento";

    $arColunas[2]['coluna'] = "Valor";
    $arColunas[2]['valor']  = "valor";

    $arColunas[3]['coluna'] = "Quantidade";
    $arColunas[3]['valor']  = "quantidade";

    $arColunas[4]['coluna'] = "Quantidade Parcelas";
    $arColunas[4]['valor']  = "quantidadeParcelas";
    
    $arColunas[5]['coluna'] = "Meses de Carência";
    $arColunas[5]['valor']  = "mesesCarencia";

    $rsColunas = new RecordSet;
    $rsColunas->preenche( $arColunas );

    $obFilArquivo = new FileBox;
    $obFilArquivo->setRotulo        ( "Arquivo de Importação" );
    $obFilArquivo->setName          ( "stCaminho" );
    $obFilArquivo->setId            ( "stCaminho" );
    $obFilArquivo->setSize          ( 40          );
    $obFilArquivo->setMaxLength     ( 100         );

    $obCmbCasaDecimal = new Select;
    $obCmbCasaDecimal->setRotulo     ( "Delimitador de Casa Decimal"   );
    $obCmbCasaDecimal->setName       ( "stCasaDecimal"  );
    $obCmbCasaDecimal->setId         ( "stCasaDecimal"  );
    $obCmbCasaDecimal->setValue      ( ""               );
    $obCmbCasaDecimal->addOption     ( "" , "Últimos dois algarismos"   );
    $obCmbCasaDecimal->addOption     ( ",", "Vírgula"  );
    $obCmbCasaDecimal->addOption     ( ".", "Ponto"  );

    $obTxtDelimitador = new TextBox;
    $obTxtDelimitador->setRotulo            ( "Delimitador de Coluna"                     );
    $obTxtDelimitador->setTitle             ( "Informe o delimitador a ser utilizado na leitura do arquivo." );
    $obTxtDelimitador->setName              ( "stDelimitador"                             );
    $obTxtDelimitador->setId                ( "stDelimitador"                             );
    $obTxtDelimitador->setValue             ( ";"                                         );
    $obTxtDelimitador->setSize              ( 1                                           );
    $obTxtDelimitador->setMaxLength         ( 1                                           );
    $obTxtDelimitador->setCaracteresAceitos("[^#]");

    $obCmbColuna1 = new Select;
    $obCmbColuna1->setRotulo     ( "Coluna 1"  );
    $obCmbColuna1->setName       ( "stColuna1" );
    $obCmbColuna1->setValue      ( "contrato"  );
    $obCmbColuna1->setCampoID    ( "valor"     );
    $obCmbColuna1->setCampoDesc  ( "coluna"    );
    $obCmbColuna1->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna2 = new Select;
    $obCmbColuna2->setRotulo     ( "Coluna 2"  );
    $obCmbColuna2->setName       ( "stColuna2" );
    $obCmbColuna2->setValue      ( "evento"    );
    $obCmbColuna2->setCampoID    ( "valor"     );
    $obCmbColuna2->setCampoDesc  ( "coluna"    );
    $obCmbColuna2->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna3 = new Select;
    $obCmbColuna3->setRotulo     ( "Coluna 3"  );
    $obCmbColuna3->setName       ( "stColuna3" );
    $obCmbColuna3->setValue      ( "valor"     );
    $obCmbColuna3->setCampoID    ( "valor"     );
    $obCmbColuna3->setCampoDesc  ( "coluna"    );
    $obCmbColuna3->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna4 = new Select;
    $obCmbColuna4->setRotulo     ( "Coluna 4"  );
    $obCmbColuna4->setName       ( "stColuna4" );
    $obCmbColuna4->setValue      ( "quantidade");
    $obCmbColuna4->setCampoID    ( "valor"     );
    $obCmbColuna4->setCampoDesc  ( "coluna"    );
    $obCmbColuna4->preencheCombo ( $rsColunas  );

    $rsColunas->setPrimeiroElemento();
    $obCmbColuna5 = new Select;
    $obCmbColuna5->setRotulo     ( "Coluna 5"  );
    $obCmbColuna5->setName       ( "stColuna5" );
    $obCmbColuna5->setValue      ( "quantidadeParcelas"  );
    $obCmbColuna5->setCampoID    ( "valor"     );
    $obCmbColuna5->setCampoDesc  ( "coluna"    );
    $obCmbColuna5->preencheCombo ( $rsColunas  );
    
    $rsColunas->setPrimeiroElemento();
    $obCmbColuna6 = new Select;
    $obCmbColuna6->setRotulo     ( "Coluna 6"  );
    $obCmbColuna6->setName       ( "stColuna6" );
    $obCmbColuna6->setValue      ( "mesesCarencia"  );
    $obCmbColuna6->setCampoID    ( "valor"     );
    $obCmbColuna6->setCampoDesc  ( "coluna"    );
    $obCmbColuna6->preencheCombo ( $rsColunas  );

    $obSpnListaEventos = new Span;
    $obSpnListaEventos->setId    ( "spnListaEventos" );
    $obSpnListaEventos->setValue ( ""                );

    $obSpnValoresSomados = new Span;
    $obSpnValoresSomados->setId    ( "spnValoresSomados" );
    $obSpnValoresSomados->setValue ( ""                  );

    $obBtnImportarEvento = new Button;
    $obBtnImportarEvento->setName              ( "btnImportar"       );
    $obBtnImportarEvento->setValue             ( "Importar"          );
    $obBtnImportarEvento->setTipo              ( "button"            );
    $obBtnImportarEvento->obEvento->setOnClick ( "BloqueiaFrames(true,false); buscaValor('importarEventos');" );
    $obBtnImportarEvento->setDisabled          ( false               );

    $obbtnLimparLista = new Button;
    $obbtnLimparLista->setName              ( "btnLimparLista" );
    $obbtnLimparLista->setValue             ( "Limpar"         );
    $obbtnLimparLista->setTipo              ( "button"         );
    $obbtnLimparLista->obEvento->setOnClick ( "executaFuncaoAjax('limparImportar','',true);"  );
    $obbtnLimparLista->setDisabled          ( false            );

    $botoesForm = array ( $obBtnImportarEvento , $obbtnLimparLista );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Folha/Importação" );
    $obFormulario->addComponente ( $obFilArquivo      );
    $obFormulario->addComponente ( $obCmbCasaDecimal  );
    $obFormulario->addComponente ( $obTxtDelimitador  );
    $obFormulario->addComponente ( $obCmbColuna1      );
    $obFormulario->addComponente ( $obCmbColuna2      );
    $obFormulario->addComponente ( $obCmbColuna3      );
    $obFormulario->addComponente ( $obCmbColuna4      );
    $obFormulario->addComponente ( $obCmbColuna5      );
    $obFormulario->addComponente ( $obCmbColuna6      );
    $obFormulario->defineBarra($botoesForm);
    $obFormulario->addSpan ( $obSpnListaEventos   );
    $obFormulario->addSpan ( $obSpnValoresSomados );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs .= "jq('#spnOpcao').html('".$obFormulario->getHTML()."');\n";
    $stJs .= "jq('#stEval').val('".$stEval."');\n";

    return $stJs;
}

function preencheSpnListaEventos($boMostraAcao = true)
{
    $rsEventosCadastrados = new recordset;
    $arEventosCadastrados = ( is_array(Sessao::read('EventosCadastrados')) ) ? Sessao::read('EventosCadastrados') : array();
    $rsEventosCadastrados->preenche($arEventosCadastrados);
    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsEventosCadastrados );
    $obLista->setTitulo     ("Eventos Cadastrados"  );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("SituAaão");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    if ($boMostraAcao) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodigoEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "quantidade" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "parcelas" );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "mes_carencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "stSituacao" );
    $obLista->commitDado();

    if ($boMostraAcao) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "alterar" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarEvento();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirEvento();" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
    }

    $obLista->montaInnerHTML();
    $stHtml = $obLista->getHTML();

    $stJs = "jq('#spnListaEventos').html('".$stHtml."');\n";

    return $stJs;
}

function preencheSpnValoresSomados($boMostraAcao = true)
{
    $arEventosCadastrados = Sessao::read('EventosCadastrados');
    $arValoresSomados = array();
    if (is_array($arEventosCadastrados)) {
        include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        foreach ($arEventosCadastrados as $inIndex=>$arEventoCadastrado) {
            if ($arEventoCadastrado["stSituacao"] == 'Ok') {
                $inCodigoEvento             = $arEventoCadastrado["inCodigoEvento"];
                $nuValorEvento              = str_replace( '.', '',  $arEventoCadastrado["valor"] );
                $nuValorEvento              = str_replace( ',', '.', $nuValorEvento );
                $nuQuantidadeEvento         = str_replace( '.', '',  $arEventoCadastrado["quantidade"] );
                $nuQuantidadeEvento         = str_replace( ',', '.', $nuQuantidadeEvento );
                $inQuantidadeParcelasEvento = $arEventoCadastrado["parcelas"];
                $inMesCarenciaEvento        = $arEventoCadastrado["mes_carencia"];

                $stFiltro = " WHERE codigo = '".$inCodigoEvento."'";
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

                $arValoresSomados[$inCodigoEvento]["registro"]++;
                $arValoresSomados[$inCodigoEvento]["inCodigoEvento"]             = $inCodigoEvento;
                $arValoresSomados[$inCodigoEvento]["stDescEvento"]               = trim($rsEvento->getCampo("descricao"));
                $arValoresSomados[$inCodigoEvento]["nuValorEvento"]             += $nuValorEvento;
                $arValoresSomados[$inCodigoEvento]["nuQuantidadeEvento"]        += $nuQuantidadeEvento;
                $arValoresSomados[$inCodigoEvento]["nuQuantidadeParcelasEvento"]+= $inQuantidadeParcelasEvento;
                $arValoresSomados[$inCodigoEvento]["inMesCarenciaEvento"]       += $inMesCarenciaEvento;
            }
        }
    }
    $arTemp = array();
    foreach ($arValoresSomados as $arValorSomado) {
        $arTemp[] = $arValorSomado;
    }

    $rsValoresSomados = new RecordSet;
    $rsValoresSomados->preenche($arTemp);
    $rsValoresSomados->addFormatacao("nuValorEvento"             , "NUMERIC_BR");
    $rsValoresSomados->addFormatacao("nuQuantidadeEvento"        , "NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setRecordSet  ( $rsValoresSomados );
    $obLista->setTitulo     ("Resumo Valores Somados"  );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade de Parcelas");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Meses de Carência");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inCodigoEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stDescEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuValorEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuQuantidadeEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "nuQuantidadeParcelasEvento" );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "inMesCarenciaEvento" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "jq('#spnValoresSomados').html('".$stHtml."');\n";

    return $stJs;
}

function retornaArrayGet($inId)
{
    global $request;
    $arEvento['inId'                      ] = $inId;
    $arEvento['inContrato'                ] = $request->get('inContrato');
    $arEvento['inCodigoEvento'            ] = $request->get('inCodigoEvento');
    $arEvento['nuValorEvento'             ] = $request->get('nuValorEvento');
    $arEvento['nuQuantidadeEvento'        ] = $request->get('nuQuantidadeEvento');
    $arEvento['nuQuantidadeParcelasEvento'] = $request->get('nuQuantidadeParcelasEvento');
    $arEvento['stHdnFixado'               ] = $request->get('stHdnFixado');
    $arEvento['stHdnApresentaParcela'     ] = $request->get('stHdnApresentaParcela');
    $arEvento['stSituacao'                ] = 'Ok';
    $arEvento['inMesCarenciaEvento'       ] = $request->get('inMesCarenciaEvento');

    return $arEvento;
}

function validaRegistroContrato($inRegistro)
{
    include_once CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php";
    $boErro = false;
    $stErro = "";
    $stValidaInteiro = "";
    $stValidaInteiro = validaInteiro( $inRegistro );
    if ($stValidaInteiro == "") {
        $obRPessoalContrato = new RPessoalContrato;
        $obRPessoalContrato->listarCgmDoRegistro( $rsContrato, $inRegistro );
        if ( $rsContrato->getNumLinhas() <= 0 )
            $boErro = true;
    } else
        $boErro = true;

    if ($boErro)
        $stErro = " - contrato informado já inválido";

    return $stErro;
}

function validaCodigoEvento($inCodigoEvento, $stMascaraEvento, &$rsEvento)
{
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
    $stErro = "";

    $ate = strlen( $stMascaraEvento ) - strlen( $inCodigoEvento );
    for ($i=0; $i<$ate; $i++) {
        $inCodigoEvento = "0".$inCodigoEvento;
    }

    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->setCodigo( $inCodigoEvento );
    $obRFolhaPagamentoEvento->setNaturezas( 'P' );
    $obRFolhaPagamentoEvento->setNaturezas( 'I' );
    $obRFolhaPagamentoEvento->setNaturezas( 'D' );
    $obRFolhaPagamentoEvento->setEventoSistema( 'false' );
    $obRFolhaPagamentoEvento->listarEvento( $rsEvento );

    if ( $rsEvento->getNumLinhas() <= 0 )
        $stErro = " - evento informado é inválido";

    return $stErro;
}

function validaValor($nuValor, $stCasaDecimal = "", $mensagem   ="valor informado é inválido")
{
    $stErro = "";
    if ($stCasaDecimal != "") {
        if ($stCasaDecimal != '.')
            $nuValor = str_replace( '.', '', $nuValor );
        if ($stCasaDecimal != ',')
            $nuValor = str_replace( ',', '', $nuValor );
        $stCasaDecimal = "\\".$stCasaDecimal;
    }

    //Verifica se o valor possui de 0 a 9 numeros, o separador decimal e 0 a 2 digitos de casa decimal OU se o valor já formado por e somente até 11 números
    if ( !((preg_match( "^[0-9]{0,9}".$stCasaDecimal."[0-9]{0,2}$^", $nuValor, $matriz )) || (preg_match( "^[0-9]{0,11}$^", $nuValor, $matriz )) ) )
        $stErro = " - $mensagem";

    return $stErro;
}

function validaInteiro($nuValor)
{
    $stErro = "";
    if ( !preg_match( "^[0-9]{0,10}$^", $nuValor, $matriz ) )
        $stErro = " - quantidade de parcelas informada é inválida";

    return $stErro;
}

function validaEventoImportacao($inContrato, $inCodigoEvento, $nuValorEvento, $nuQuantidadeEvento, $nuQuantidadeParcelasEvento, $inMesesCarencia, $stFixado, $stApresentaParcelas, $inCodPeriodoMovimentacao, $stMascaraEvento)
{    
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php";
    $stErro = "";

    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $obTFolhaPagamentoRegistroEvento->setDado( "cod_periodo_movimentacao", $inCodPeriodoMovimentacao );
    $obTFolhaPagamentoRegistroEvento->setDado( "registro"                , $inContrato               );
    $obTFolhaPagamentoRegistroEvento->setDado( "codigo"                  , formataEvento( $inCodigoEvento, $stMascaraEvento ) );
    $obTFolhaPagamentoRegistroEvento->recuperaEventosPorContratoEPeriodo( $rsRegistroEventos );

    if ( $rsRegistroEventos->getNumLinhas() > 0 ) {
        $stErro = " - já existe um evento cadastrado para este contrato";
    } else {
        // Verifica se este evento já foi cadastrado para o contrato
        $ate = count( Sessao::read('EventosCadastrados') );
        $arEventosCadastrados = Sessao::read('EventosCadastrados');
        for ($i=0; $i<$ate; $i++) {
            if ( ( $inCodigoEvento == $arEventosCadastrados[$i]['inCodigoEvento'] ) && ( $inContrato == $arEventosCadastrados[$i]['inContrato'] ) ) {
                $stErro = " - já existe um evento cadastrado para este contrato";
            }
        }
    }

    if( !$inContrato )
        $stErro .= " - contrato não informado";
    if( !$inCodigoEvento )
        $stErro .= " - evento não informado";
    if( ( $stFixado != 'Q' ) && (( $nuValorEvento =='' ) || ( $nuValorEvento == '0,00' ) ) )
        $stErro .= " - valor não informado";
    if( ( $stFixado == 'Q' ) && ( $nuValorEvento =='' ) )
        $stErro .= " - valor não pode ser informado para este evento";
    if( ( $stFixado == 'Q' ) && ( $nuQuantidadeEvento =='' ) || ( $nuQuantidadeEvento == '0,00' ) )
        $stErro .= " - quantidade não informada";
    if( ( $stApresentaParcelas != 'f' ) && (( $nuQuantidadeParcelasEvento =='' ) || ( $nuQuantidadeParcelasEvento == '0,00' )) )
        $stErro .= " - quantidade de parcelas não informada";
    if( ( $stApresentaParcelas == 'f' ) && (!( $nuQuantidadeParcelasEvento =='' ) || ( $nuQuantidadeParcelasEvento == '0,00' )) )
        $stErro .= " - quantidade de parcelas não pode ser informada para este evento";
    if( ( $inMesesCarencia == '' ) && ($stApresentaParcelas == 't') )
        $stErro .= " - os meses de carência não podem ser informados para este evento";

    return $stErro;
}

function formataValor($stValor, $stCasaDecimal = "")
{
    if ($stValor != "") {
        if ( validaValor( $stValor, $stCasaDecimal ) == "" ) {
            switch ($stCasaDecimal) {
                case "":
                    if ( strlen( $stValor ) == 2 ) {
                        $stValor = "0".$stValor;
                    } elseif ( strlen( $stValor ) == 1 ) {
                        $stValor = "00".$stValor;
                    }
                    $stValor = substr( $stValor, 0, strlen( $stValor ) - 2 ) .".". substr( $stValor, strlen( $stValor ) - 2, 2 );
                break;
                case ".":
                    $stValor = str_replace( ',', '', $stValor );
                break;
                case ",":
                    $stValor = str_replace( '.', '', $stValor );
                    $stValor = str_replace( ',', '.', $stValor );
                break;
            }
            $stValor = number_format ( $stValor, 2, ",", ".");
        }
    } else {
        $stValor = "0,00";
    }

    return $stValor;
}

function formataEvento($inCodigoEvento, $stMascaraEvento)
{
    if ($inCodigoEvento != "") {
        $ate = strlen( $stMascaraEvento ) - strlen( $inCodigoEvento );
        for ($i=0; $i<$ate; $i++) {
            $inCodigoEvento = "0".$inCodigoEvento;
        }
    } else {
        $inCodigoEvento = "&nbsp;";
    }

    return $inCodigoEvento;
}

function importarEventos(Request $request)
{
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
    include_once CLA_ARQUIVO_CSV;

    $inId          = 0;
    $stErroLinha   = "";
    $stErroArquivo = "";

    $stCaminho = $_FILES["stCaminho"]["tmp_name"];
    $stDelimitador = $request->get('stDelimitador');
    $stCasaDecimal = $request->get('stCasaDecimal');
    Sessao::write("EventosCadastrados",array());

    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao( $rsUltimaMovimentacao );
    $inCodPeriodoMovimentacao = $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");

    //Monta colunas do arquivo
    for ($i=1; $i<=6; $i++) {
        for ($j=$i+1; $j<=6; $j++) {
            if ($request->get( "stColuna".$i ) == $request->get( "stColuna".$j )) {
                $stErroArquivo = "Coluna $i é igual a coluna $j.";
            }
        }

        $inColuna = $i-1;
        switch ($request->get( "stColuna".$i )) {
            case "contrato":
                $inColContratro          = $inColuna;
            break;
            case "evento":
                $inColEvento             = $inColuna;
            break;
            case "valor":
                $inColValor              = $inColuna;
            break;
            case "quantidade":
                $inColQuantidade         = $inColuna;
            break;
            case "quantidadeParcelas":
                $inColQuantidadeParcelas = $inColuna;
            break;
            case "mesesCarencia":
                $inColMesesCarencia = $inColuna;
            break;
        }
    }

    if ($stErroArquivo == "") {
        $arquivoEventos = new ArquivoCSV( $stCaminho );
        $arquivoEventos->setDelimitadorColuna( $stDelimitador );
        $obErro = $arquivoEventos->Abrir('r');
        if ( !$obErro->ocorreu() ) {
            $arEventosCadastrados = Sessao::read("EventosCadastrados");
            while ( !feof( $arquivoEventos->reArquivo ) ) {
                $stErroLinha = "Erro";
                $arLinhas = $arquivoEventos->LerLinha();
                if ($arLinhas != '') {
                    $stErroLinha .= validaRegistroContrato ( $arLinhas[ $inColContratro          ] );
                    $stErroLinha .= validaCodigoEvento     ( $arLinhas[ $inColEvento             ], $stMascaraEvento, $rsEvento );
                    $stErroLinha .= validaValor            ( $arLinhas[ $inColValor              ], $stCasaDecimal );
                    $stErroLinha .= validaValor            ( $arLinhas[ $inColQuantidade         ], $stCasaDecimal, "quantidade informada é inválida" );
                    $stErroLinha .= validaInteiro          ( $arLinhas[ $inColQuantidadeParcelas ] );
                    $stErroLinha .= validaEventoImportacao ( $arLinhas[ $inColContratro          ]
                                                           , $arLinhas[ $inColEvento             ]
                                                           , $arLinhas[ $inColValor              ]
                                                           , $arLinhas[ $inColQuantidade         ]
                                                           , $arLinhas[ $inColQuantidadeParcelas ]
                                                           , $arLinhas[ $inColMesesCarencia      ]
                                                           , $rsEvento->getCampo( "fixado" )
                                                           , $rsEvento->getCampo( "apresenta_parcela" )
                                                           , $inCodPeriodoMovimentacao
                                                           , $stMascaraEvento );

                    if ($stErroLinha == "Erro") {
                        $stErroLinha = "Ok";
                    }
                    $inId = $inId + 1;

                    $arEvento['inId'                      ] = $inId;
                    $arEvento['registro'                  ] = $arLinhas[ $inColContratro          ] ? $arLinhas[ $inColContratro          ] : "&nbsp;";
                    $arEvento['inCodigoEvento'            ] = formataEvento( $arLinhas[ $inColEvento ], $stMascaraEvento );
                    $arEvento['cod_evento'                ] = $rsEvento->getCampo("cod_evento");
                    $arEvento['valor'                     ] = formataValor( $arLinhas[ $inColValor ], $stCasaDecimal );
                    $arEvento['quantidade'                ] = formataValor( $arLinhas[ $inColQuantidade         ], $stCasaDecimal );
                    $arEvento['parcelas'                  ] = $arLinhas[ $inColQuantidadeParcelas ] ? $arLinhas[ $inColQuantidadeParcelas ] : "&nbsp;";
                    $arEvento['stHdnFixado'               ] = $rsEvento->getCampo( "fixado" ) ? $rsEvento->getCampo( "fixado" ) : "&nbsp;";
                    $arEvento['stHdnApresentaParcela'     ] = $rsEvento->getCampo( "apresenta_parcela" ) ? $rsEvento->getCampo( "apresenta_parcela" ) : "&nbsp;";
                    $arEvento['stSituacao'                ] = $stErroLinha;
                    $arEvento['boExcluirDisabled'         ] = "true";
                    $arEvento['mes_carencia'              ] = $arLinhas[ $inColMesesCarencia ] ? $arLinhas[ $inColMesesCarencia ] : "0";

                    $arEventosCadastrados[] = $arEvento;
                }
            }
            Sessao::write("EventosCadastrados",$arEventosCadastrados);
            $arquivoEventos->Fechar();

            $stJs .= preencheSpnListaEventos( false );
            $stJs .= preencheSpnValoresSomados( false );
        } else {
            $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        }
    }

    return $stJs;
}

function limpaCamposLista()
{
    $stJs .= "jq('#inNomCGM').html('&nbsp;');\n";
    $stJs .= "jq('#inContrato').val('');\n";
    $stJs .= "jq('#inCodigoEvento').val('');\n";
    $stJs .= "jq('#stEvento').html('&nbsp;');\n";
    $stJs .= "jq('#spnDadosLoteEvento').html('');\n";

    return $stJs;
}

######################################
#IMPORTAR
######################################

function limparImportar()
{
    $stJs .= "document.frm.reset();\n";
    $stJs .= "jq('#stOpcaoImportar').prop('checked',true);\n";
    $stJs .= gerarSpanImportar();

    return $stJs;
}

function gerarSpanMatricula($stTipoFiltro, $boQuebrarDisabled)
{
    $boAtualizarLotacao = false;

    Sessao::write('arContratos',"");
    Sessao::write('arPensionistas',"");
    Sessao::write('arEstagios',"");

    switch ($stTipoFiltro) {
        case "contrato":
        case "contrato_todos":
        case "contrato_rescisao":
        case "contrato_aposentado":
            $stHtml = montaSpanContrato($stJs,$stTipoFiltro);
        break;
        case "contrato_rescisao":
            $stHtml = montaSpanContrato($stJs, true);
        break;
        case "contrato_pensionista":
            $stHtml = montaSpanContratoPensionista($stJs);
        break;
        case "lotacao":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanLotacao($stEval);
            $boAtualizarLotacao = true;
        break;
        case "lotacao_grupo":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanLotacao($stEval,true);
            $boAtualizarLotacao = true;
        break;
        case "local":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanLocal($stEval);
        break;
        case "local_grupo":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanLocal($stEval,true);
        break;
        case "evento":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanEvento($stJs);
        break;
        case "evento_multiplo":
            Sessao::write('arLoteEventos',"");
            $stJs .= "jq('#spnLoteEvento').html('');\n";
            $stHtml = montaSpanEventoMultiplo($stEval);
        break;
    }

    $stEval = isset($stEval) ? $stEval : "";
    $stJs = isset($stJs) ? $stJs : "";

    $stJs .= "jq('#spnTipoFiltro').html('".$stHtml."');\n";
    $stJs .= "jq('#hdnTipoFiltro').val('".$stEval."');\n";

    if ($boAtualizarLotacao === true) {
        $stJs .= atualizarLotacao();
    }

    $stJs .= gerarSpanRegistroEvento(0,0,'','&nbsp;','','',$stTipoFiltro);

    return $stJs;
}

function montaSpanContrato(&$stJs, $stTipo="contrato_todos")
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php'   );
    // $stTipo
    // todos       = todos os servidores
    // contrato    = somente servidores não rescindidos e não aposentados
    // aposentados = somente servidores aposentados
    // rescindidos = somente servidores rescindidos

    $obSpnContratos = new Span;
    $obSpnContratos->setid ( "spnContratos" );

    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->obIContratoDigitoVerificador->setRotulo("**Matrícula");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra( false );
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange("montaParametrosGET('preencherDadosEvento','inCodigoEvento,inContrato,stProporcional',true);");
    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("");
    $obIFiltroContrato->obIContratoDigitoVerificador->obImagem->setId('inImgContrato');

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario($obFormulario);

    $obFormulario->addSpan( $obSpnContratos );
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas($stTipo);

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario($obFormulario);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function montaSpanContratoPensionista(&$stJs)
{
    Sessao::write("arPensionistas",array());

    include_once CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php';
    include_once CAM_GRH_PES_COMPONENTES.'IFiltroPensionista.class.php';

    $obSpnContratos = new Span;
    $obSpnContratos->setid( "spnContratosPensionistas" );

    $stName = "ContratoPensionista";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluirContratoPensionista");
    $obBtnIncluir->setValue             ( "Incluir"             );

    $obBtnIncluir->obEvento->setOnClick ( "if ( ValidaContratoPensionista() ) { montaParametrosGET('incluirContratoPensionista','inContratoPensionista'); limpaFormularioContratoPensionista(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimparContratoPensionista"           );
    $obBtnLimpar->setValue             ( "Limpar"                                );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormularioContratoPensionista();" );
    $arBarra[] = $obBtnLimpar;

    $obIFiltroPensionista = new IFiltroPensionista(true);
    $obIFiltroPensionista->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra(false);

    $obFormulario = new Formulario;
    $obIFiltroPensionista->geraFormulario($obFormulario);
    $obFormulario->Incluir($stName,
                            array($obIFiltroPensionista->obIContratoDigitoVerificador->obTxtRegistroContrato,
                                  $obIFiltroPensionista->obLblCGM,
                                  $obIFiltroPensionista->obHdnCGM
                                 ),
                            true
                           );
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas("contrato_pensionista");

    $obFormulario = new Formulario;
    $obIFiltroPensionista->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function montaSpanLotacao(&$stEval,$boGrupo=false)
{
    include_once CAM_GRH_PES_COMPONENTES.'ISelectMultiploLotacao.class.php';
    global $request;
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;
    $obISelectMultiploLotacao->setNull(false);

    if (trim($request->get("inAno", "")) != "" and trim($request->get("inCodMes", "")) != "") {
        $inDia = date("t",mktime(0,0,0,$request->get("inCodMes"),1,$request->get("inAno")));
        $dtCompetencia = date("Y-m-d",mktime(0,0,0,$request->get("inCodMes"),$inDia,$request->get("inAno")));
        $obISelectMultiploLotacao->obTOrganogramaOrgao->setDado('vigencia', $dtCompetencia);
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Lotação");
    $obFormulario->addComponente( $obISelectMultiploLotacao );

    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanLocal(&$stEval,$boGrupo=false)
{
    include_once CAM_GRH_PES_COMPONENTES.'ISelectMultiploLocal.class.php';

    $obISelectMultiploLocal = new ISelectMultiploLocal;
    $obISelectMultiploLocal->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo ("Filtro por Local");
    $obFormulario->addComponente( $obISelectMultiploLocal );

    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanEvento(&$stJs)
{
    include_once CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php";

    $obSpnEventos = new Span;
    $obSpnEventos->setid ( "spnEventos" );

    $obIBscEvento = new IBscEvento('inCodigoInnerEvento', 'stInnerEvento', 'stInnerTextoComplementar');

    $obFormulario = new Formulario;
    $obFormulario->addTitulo ("Filtro por Evento");
    $obIBscEvento->geraFormulario ( $obFormulario );
    $obFormulario->addSpan ( $obSpnEventos );
    
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanEventoMultiplo(&$stEval)
{
    include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";

    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento();
    $obRFolhaPagamentoEvento->listarEvento($rsEventos);

    $obCmbEvento = new SelectMultiplo();
    $obCmbEvento->setName            ( 'inCodEvento'                                             );
    $obCmbEvento->setRotulo          ( "Eventos"                                                 );
    $obCmbEvento->setTitle           ( "Selecione os eventos a serem apresentados no relatório (podem ser selecionados até 10 eventos)." );
    $obCmbEvento->SetNomeLista1      ( 'inCodEventoDisponiveis'                                  );
    $obCmbEvento->setCampoId1        ( '[cod_evento]'                                            );
    $obCmbEvento->setCampoDesc1      ( '[codigo]-[descricao]'                                    );
    $obCmbEvento->setStyle1          ( "width: 300px"                                            );
    $obCmbEvento->SetRecord1         ( $rsEventos                                                );
    $obCmbEvento->SetNomeLista2      ( 'inCodEventoSelecionados'                                 );
    $obCmbEvento->setCampoId2        ( '[cod_evento]'                                            );
    $obCmbEvento->setCampoDesc2      ( '[codigo]-[descricao]'                                    );
    $obCmbEvento->setStyle2          ( "width: 300px"                                            );
    $obCmbEvento->SetRecord2         ( new recordset()                                           );
    $obCmbEvento->setNull            ( false                                                     );
    $obCmbEvento->obSelect1->setSize ( 10                                                        );
    $obCmbEvento->obSelect2->setSize ( 10                                                        );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Evento");
    $obFormulario->addComponente( $obCmbEvento );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaValidaMatriculas($stTipoFiltro)
{
    if ($stTipoFiltro == "cgm_codigo_estagio") {
        $stMensagem = "Deve haver pelo menos um estagiário na lista de estagiários";
    } else {
        $stMensagem = "Deve haver pelo menos uma matrícula na lista de matrículas";
    }

    $stHdnValidaMatriculas  = "if (document.frm.stTipoFiltro.value == \"$stTipoFiltro\") {";
    $stHdnValidaMatriculas .= "  if (document.frm.inValidaMatriculas.value == \"0\") {";
    $stHdnValidaMatriculas .= "     erro = true; ";
    $stHdnValidaMatriculas .= "     mensagem += \"@".$stMensagem."!()\"; ";
    $stHdnValidaMatriculas .= "  }";
    $stHdnValidaMatriculas .= "}";

    $stJs = "jq('#hdnValidaMatriculas').val('$stHdnValidaMatriculas');\n";

    return $stJs;
}

function atualizarLotacao()
{
    include_once CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php";
    include_once CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php";
    include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php";
    include_once CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php";
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";

    $stJs                    = "";
    $arFiltroCompetencia     = Sessao::read("arFiltroCompetencia");
    $arFiltroAnoCompetencia  = Sessao::read("arFiltroAnoCompetencia");
    $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");

    if (is_array($arFiltroCompetencia) && count($arFiltroCompetencia) > 0) {
        foreach ($arFiltroCompetencia as $obFiltroCompetencia) {
            if (trim($obFiltroCompetencia->getCodigoPeriodoMovimentacao()) != "") {
                $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
                $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$obFiltroCompetencia->getCodigoPeriodoMovimentacao());
                $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

                $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
                $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }
            }
        }
    }

    if (is_array($arFiltroAnoCompetencia) && count($arFiltroAnoCompetencia) > 0) {
        foreach ($arFiltroAnoCompetencia as $obFiltroAnoCompetencia) {
            if (trim($obFiltroAnoCompetencia->getCodigoPeriodoMovimentacao()) != "") {
                $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
                $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$obFiltroAnoCompetencia->getCodigoPeriodoMovimentacao());
                $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

                $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
                $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

                if (is_array($arSelectMultiploLotacao) && count($arSelectMultiploLotacao) > 0) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }
            }
        }
    }

    return $stJs;
}

$boAjax = true;

switch ($request->get("stCtrl")) {
    case "gerarSpanOpcoes":
        $stJs = gerarSpanOpcoes($request);
    break;
    case "gerarSpanLoteEvento":
        $stJs = gerarSpanLoteEvento($request);
    break;
    case "preencherDadosEvento":
        $stJs = preencherDadosEvento($request);
    break;
    case "incluirLoteEvento":
        $stJs = incluirLoteEvento($request);
    break;
    case "alterarLoteEvento":
        $stJs = alterarLoteEvento($request);
    break;
    case "excluirLoteEvento":
        $stJs = excluirLoteEvento($request);
    break;
    case "limparLoteEvento":
        $stJs = limparLoteEvento();
    break;
    case "montaAlterarLoteEvento":
        $stJs = montaAlterarLoteEvento($request);
    break;
    case "incluirLoteMatricula":
        $stJs = incluirLoteMatricula($request);
    break;
    case "alterarLoteMatricula":
        $stJs = alterarLoteMatricula($request);
    break;
    case "excluirLoteMatricula":
        $stJs = excluirLoteMatricula($request);
    break;
    case "limparLoteMatricula":
        $stJs = limparLoteMatricula();
    break;
    case "montaAlterarLoteMatricula":
        $stJs = montaAlterarLoteMatricula($request);
    break;
    case "importarEventos":        
        $boAjax = false;
        $stJs = " var jq  = window.parent.frames[\"telaPrincipal\"].jQuery; ";
        $stJs .= importarEventos($request);
        $stJs .= " LiberaFrames(true,true); ";
    break;
    case "limparImportar":
        $stJs = limparImportar();
    break;
    case "submeter":
        $stJs = submeter($request);
    break;
    case "gerarSpanMatricula":
        $stJs = gerarSpanMatricula($request->get('stTipoFiltro'), $request->get('boQuebrarDisabled'));
        if($request->get("inCodigoEvento","") != "")
            $stJs .= preencherDadosEvento($request);
    break;
    case "incluirContrato":
        $stJs = incluirContrato();
    break;
    case "excluirContrato":
        $stJs = excluirContrato();
    break;
    case "incluirContratoPensionista":
        $stJs = incluirContratoPensionista();
    break;
    case "excluirContratoPensionista":
        $stJs = excluirContratoPensionista();
    break;
}

if ($stJs) {
    if ($boAjax) {
        echo $stJs;
    } else {
        sistemaLegado::executaFrameOculto($stJs);
    }
}
?>
