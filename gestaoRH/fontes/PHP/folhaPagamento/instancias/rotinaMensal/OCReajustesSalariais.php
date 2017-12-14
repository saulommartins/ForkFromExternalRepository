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
    * Arquivo de Oculto
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30930 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-06 09:52:53 -0200 (Qui, 06 Dez 2007) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                                             );

//Define o nome dos arquivos PHP
$stPrograma = "ReajustesSalariais";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

function gerarSpanValoresReajustes()
{
    $obFormulario = new Formulario();
    if ($_GET["stReajuste"] == "p" OR !isset($_GET["stReajuste"])) {
        $onChange = " montaParametrosGET('preencheComboReajuste','stReajuste,stAcao,inCodPadrao');";

        $obTxtCodPadrao = new TextBox;
        $obTxtCodPadrao->setRotulo             ( "Padrão"     );
        $obTxtCodPadrao->setName               ( "inCodPadrao" );
        $obTxtCodPadrao->setValue              ( $inCodPadrao );
        $obTxtCodPadrao->setTitle              ( "Informe o padrão para reajuste. Para reajustar todos os padrões, deixar esta opção em branco." );
        $obTxtCodPadrao->setSize               ( 10    );
        $obTxtCodPadrao->setMaxLength          ( 6    );
        $obTxtCodPadrao->setInteiro            ( true );
        $obTxtCodPadrao->setNull               ( true );
        $obTxtCodPadrao->obEvento->setOnBlur($onChange);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
        $obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
        $obTFolhaPagamentoPadrao->recuperaRelacionamento($rsPadrao);
        $obCmbCodPadrao = new Select;
        $obCmbCodPadrao->setName                  ( "stPadrao"            );
        $obCmbCodPadrao->setValue                 ( $inCodPadrao          );
        $obCmbCodPadrao->setRotulo                ( "Padrão"              );
        $obCmbCodPadrao->setTitle                 ( "Informe o padrão para reajuste. Para reajustar todos os padrões, deixar esta opção em branco." );
        $obCmbCodPadrao->setNull                  ( true                  );
        $obCmbCodPadrao->setCampoId               ( "[cod_padrao]" );
        $obCmbCodPadrao->setCampoDesc             ( "[descricao] - [valor]" );
        $obCmbCodPadrao->addOption                ( "", "Selecione"       );
        $obCmbCodPadrao->preencheCombo            ( $rsPadrao             );
        $obCmbCodPadrao->setStyle                 ( "width: 250px"        );
        $obCmbCodPadrao->obEvento->setOnChange($onChange);

        $obFormulario->addComponenteComposto($obTxtCodPadrao,$obCmbCodPadrao);
    } else {
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
        $obIFiltroTipoFolha = new IFiltroTipoFolha();
        $onChange  = $obIFiltroTipoFolha->obCmbTipoCalculo->obEvento->getOnChange();
        $onChange .= " montaParametrosGET('preencheComboReajuste','stReajuste,inCodConfiguracao,stAcao,inCodigoEvento');";
        $obIFiltroTipoFolha->obCmbTipoCalculo->obEvento->setOnChange($onChange);

        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );
        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

        //Define a mascara do campo Evento
        $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
        $obRFolhaPagamentoConfiguracao->consultar();
        $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

        $obBscInnerEvento = new BuscaInner;
        $obBscInnerEvento->setRotulo              ( "Evento"         );
        $obBscInnerEvento->setId                  ( "stEvento"       );
        $obBscInnerEvento->setTitle               ( "Informe o ou busque o evento para reajuste. Os eventos serão reajustados conforme o tipo fixado (quantidade ou valor)." );
        $obBscInnerEvento->setNull(false);
        $obBscInnerEvento->obCampoCod->setName    ( "inCodigoEvento"    );
        $obBscInnerEvento->obCampoCod->setId      ( "inCodigoEvento"    );
        $obBscInnerEvento->obCampoCod->setPreencheComZeros ( "E"     );
        $obBscInnerEvento->obCampoCod->setMascara ( $stMascaraEvento );
        $obBscInnerEvento->obCampoDescrHidden->setName( "hdnDescEvento" );
        $obBscInnerEvento->setFuncaoBusca( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEvento','stEvento','','".Sessao::getId()."&stNaturezasAceitas=P-D&stNaturezaChecked=P&boInformarValorQuantidade=true&boInformarQuantidadeParcelas=false&boSugerirValorQuantidade=false&boEventoSistema=false','800','550')" );
        $obBscInnerEvento->obCampoCod->obEvento->setOnBlur("montaParametrosGET('preencherEventoFixado','stReajuste,inCodConfiguracao,stAcao,inCodigoEvento');");

        $obLblEventoFixado = new Label();
        $obLblEventoFixado->setId("lblEventoFixado");
        $obLblEventoFixado->setRotulo("Evento Fixado em");

        $obHdnEventoFixado = new hidden();
        $obHdnEventoFixado->setName("hdnEventoFixado");

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        list($inCodDia, $inCodMes, $inCodAno) = explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));

        $obHdnCodMes = new hidden();
        $obHdnCodMes->setName("inCodMes");
        $obHdnCodMes->setId("inCodMes");
        $obHdnCodMes->setValue((int) $inCodMes);

        $obHdnCodAno = new hidden();
        $obHdnCodAno->setName("inAno");
        $obHdnCodAno->setId("inAno");
        $obHdnCodAno->setValue($inCodAno);

        $obIFiltroTipoFolha->geraFormulario($obFormulario);
        $obFormulario->addComponente($obBscInnerEvento);
        $obFormulario->addComponente($obLblEventoFixado);
        $obFormulario->addHidden($obHdnEventoFixado);
        $obFormulario->addHidden($obHdnCodMes);
        $obFormulario->addHidden($obHdnCodAno);
    }

    $obFormulario->montaInnerHTML();
    $stJs .= preencheComboReajuste();
    $stJs .= "d.getElementById('spnValoresReajustes').innerHTML = '".$obFormulario->getHTML()."';\n";

    return $stJs;
}

function gerarSpanTipoReajuste($boSetFocus=true)
{
    $obFormulario = new Formulario();
    if ($_REQUEST["stTipoReajuste"] == "p" OR !isset($_REQUEST["stTipoReajuste"])) {
        $obNumPercentualReajuste = new Numerico();
        $obNumPercentualReajuste->setRotulo("Percentual do Reajuste");
        $obNumPercentualReajuste->setTitle("Informe o percentual de reajuste.");
        $obNumPercentualReajuste->setName("nuPercentualReajuste");
        $obNumPercentualReajuste->setId("nuPercentualReajuste");
        $obNumPercentualReajuste->setMaxValue(100);
        $obNumPercentualReajuste->setMinValue(0.0001);
        $obNumPercentualReajuste->setMaxLength(7);
        $obNumPercentualReajuste->setNull(false);
        $obNumPercentualReajuste->setDecimais(4);
        $obNumPercentualReajuste->obEvento->setOnChange("montaParametrosGET('preencherObservacao','stTipoReajuste,nuPercentualReajuste,dtVigencia');");
        $obLabel = new Label();
        $obLabel->setValue("%");
        $obFormulario->agrupaComponentes(array($obNumPercentualReajuste,$obLabel));
        $stJsFocus = "d.getElementById('nuPercentualReajuste').focus();";
    } else {
        $obNumValorReajuste = new Numerico();
        $obNumValorReajuste->setRotulo("Valor do Reajuste");
        $obNumValorReajuste->setTitle("Informe o valor de reajuste.");
        $obNumValorReajuste->setName("nuValorReajuste");
        $obNumValorReajuste->setId("nuValorReajuste");
        $obNumValorReajuste->setMaxLength(7);
        $obNumValorReajuste->setMinValue(0.01);
        $obNumValorReajuste->setNull(false);
        $obNumValorReajuste->obEvento->setOnChange("montaParametrosGET('preencherObservacao','stTipoReajuste,nuValorReajuste,dtVigencia');");
        $obFormulario->addComponente($obNumValorReajuste);
        $stJsFocus = "d.getElementById('nuValorReajuste').focus();";
    }

    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('hdnTipoReajuste').value = '".$obFormulario->getInnerJavaScript()."';\n";
    $stJs .= "d.getElementById('spnTipoReajuste').innerHTML = '".$obFormulario->getHTML()."';\n";
    if ($boSetFocus) {
        $stJs .= $stJsFocus;
    }

    return $stJs;
}

function preencheComboReajuste()
{
    $stJs = "";

    if (trim($_GET["stAcao"])=="excluir") {
        $rsReajuste = new recordset;

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajuste.class.php");
        $obTFolhaPagamentoReajuste = new TFolhaPagamentoReajuste();

        if ($_GET["stReajuste"] == "p" OR !isset($_GET["stReajuste"])) {
            $stFiltro = " WHERE origem = 'P'";
            if (trim($_GET["inCodPadrao"]) != "") {
                $stFiltro .= "      AND EXISTS ( SELECT 1                                                                        \n";
                $stFiltro .= "                       FROM folhapagamento.reajuste_padrao_padrao         \n";
                $stFiltro .= "                      WHERE reajuste.cod_reajuste = reajuste_padrao_padrao.cod_reajuste            \n";
                $stFiltro .= "                        AND reajuste_padrao_padrao.cod_padrao = ".$_GET["inCodPadrao"].")  \n";
            }
            $obTFolhaPagamentoReajuste->recuperaReajuste($rsReajuste, $stFiltro, " ORDER BY cod_reajuste DESC");
        } else {
            if (trim($_GET["inCodConfiguracao"]) == "") {
                $stFiltro = " WHERE origem != 'P'";
                $obTFolhaPagamentoReajuste->recuperaReajuste($rsReajuste, $stFiltro, " ORDER BY cod_reajuste DESC");
            } else {
                if ($_GET["inCodigoEvento"] != "") {
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
                    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
                    $stFiltroEvento = " WHERE codigo = '".str_pad(trim($_GET["inCodigoEvento"]),strlen(Sessao::read("stMascaraEvento")),"0",STR_PAD_LEFT)."'";
                    $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltroEvento);
                    $inCodEvento = $rsEvento->getCampo("cod_evento");

                    $obTFolhaPagamentoReajuste->setDado("cod_configuracao",$_GET["inCodConfiguracao"]);
                    $obTFolhaPagamentoReajuste->setDado("cod_evento",$inCodEvento);
                    $obTFolhaPagamentoReajuste->recuperaReajuste($rsReajuste, $stFiltro, " ORDER BY cod_reajuste DESC");
                }
            }
        }
        $stJs .= "limpaSelect(f.inCodReajuste,0); \n";
        $stJs .= "f.inCodReajuste[0] = new Option('Selecione','','selected');\n";

        $contador = 1;
        while (!$rsReajuste->eof()) {
            $chave = $rsReajuste->getCampo("cod_reajuste")."*_*".$rsReajuste->getCampo("origem");
            $stJs .= "f.inCodReajuste[".$contador."] = new Option('".$rsReajuste->getCampo("descricao")."','".$chave."'); \n";
            $contador++;

            $rsReajuste->proximo();
        }
    }

    return $stJs;
}

function preencherEventoFixado()
{
    $rsEvento = new RecordSet();
    $stFixado = "&nbsp;";
    $stEvento = "&nbsp;";
    $stCodigoEvento = "";
    if (trim($_GET["inCodigoEvento"]) != "") {
        include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
        $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
        $obRFolhaPagamentoConfiguracao->consultar();
        $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
        Sessao::write("stMascaraEvento",$stMascaraEvento);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stCodigo = str_pad(trim($_GET["inCodigoEvento"]),strlen($stMascaraEvento),"0",STR_PAD_LEFT);
        $stFiltro  = " WHERE codigo = '".$stCodigo."'";
        $stFiltro .= "   AND natureza IN ('P','D')";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
        if ($rsEvento->getNumLinhas() == 1) {
            $stEvento = $rsEvento->getCampo("descricao");
            $stCodigoEvento = $rsEvento->getCampo("codigo");
            if (strtoupper($rsEvento->getCampo("fixado")) == strtoupper("v")) {
                $stFixado = "Valor";
            } else {
                $stFixado = "Quantidade";
            }
        }
    }
    $stJs  = "f.hdnEventoFixado.value = '".$stFixado."';\n";
    $stJs .= "d.getElementById('lblEventoFixado').innerHTML = '".$stFixado."';\n";
    $stJs .= "d.getElementById('stEvento').innerHTML = '".$stEvento."';\n";
    $stJs .= "d.getElementById('inCodigoEvento').value = '".$stCodigoEvento."';\n";
    $stJs .= preencheComboReajuste();

    return $stJs;
}

function gerarSpanNivelPadrao()
{
    $rsNivelPadrao = new RecordSet();
    if ($_GET["inCodPadrao"] != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoNivelPadraoNivel.class.php");
        $obTFolhaPagamentoNivelPadraoNivel = new TFolhaPagamentoNivelPadraoNivel();
        $stFiltro = " AND FPNP.cod_padrao = ".$_GET["inCodPadrao"];
        $obTFolhaPagamentoNivelPadraoNivel->recuperaRelacionamento($rsNivelPadrao,$stFiltro);
    }
    $obRdoAtualizarProgressao = new CheckBox();
    $obRdoAtualizarProgressao->setRotulo("Atualizar Progressões");
    $obRdoAtualizarProgressao->setName("boAtualizarProgressoes");
    $obRdoAtualizarProgressao->setTitle("Marque esta opção para atualizar os valores das progressões dos padrões.");

    $obFormulario = new Formulario();
    if ($rsNivelPadrao->getNumLinhas() == 1) {
        $obFormulario->addComponente($obRdoAtualizarProgressao);
    }
    $obFormulario->montaInnerHTML();
    $stJs = "d.getElementById('spnNivelPadrao').innerHTML = '".$obFormulario->getHTML()."';\n";

    return $stJs;
}

function processarForm()
{
    $stJs  = gerarSpanValoresReajustes();
    if ($_REQUEST['stAcao'] != 'excluir') {
        $stJs .= gerarSpanTipoReajuste(false);
    }

    return $stJs;
}

function preencherObservacao()
{
    if ($_REQUEST['stTipoReajuste'] == 'p') {
        $stObservacao = "Reajuste salarial de ".$_REQUEST["nuPercentualReajuste"]."% a partir de ".$_REQUEST["dtVigencia"];
    } else {
        $stObservacao = "Reajuste salarial a partir de ".$_REQUEST["dtVigencia"];
    }
    $stJs = "f.stObservacao.value = '$stObservacao'";

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stReajuste"] == "e") {
        if ($_GET["inCodConfiguracao"] == "") {
            $obErro->setDescricao("@Campo Tipo de Cálculo inválido!()");
        }
        if ($_GET["inCodigoEvento"] == "") {
            $obErro->setDescricao("@Campo Evento inválido!()");
        }
        // Verifica se a folha esta aberta
        if ( !$obErro->ocorreu() ) {
            include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
            if ($_GET["inCodConfiguracao"] == 0) { //complementar
                include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"       );
                $obRFolhaPagamentoPeriodoMovimentacao =  new RFolhaPagamentoPeriodoMovimentacao ;
                $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
                $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsMovimentacao);
                if ($rsMovimentacao->getCampo('cod_periodo_movimentacao') != "") {
                    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsMovimentacao->getCampo('cod_periodo_movimentacao'));
                } else {
                    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(0);
                }

                $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta();
                $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
                $stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

                if (trim($obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getSituacao()) != "a") {
                  $obErro->setDescricao("@A folha complementar está fechada. Para efetuar o reajuste por evento é necessário reabri-lá.!()");
                }
            }
            if ($_GET["inCodConfiguracao"] == 1) { //Salário
                include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php");
                $obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
                $obRFolhaPagamentoFolhaSituacao->consultarFolha();
                if (trim($obRFolhaPagamentoFolhaSituacao->getSituacao()) != "Aberto") {
                    $obErro->setDescricao("@A folha salário está fechada. Para efetuar o reajuste por evento é necessário reabri-lá.!()");
                }
            }
        }
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= "BloqueiaFrames(true,false);";
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function gerarSpanReajustesSalariais()
{
    if (is_array(Sessao::read("arRegistros"))) {
        $rsRegistros = new RecordSet();
        $rsRegistros->preenche(Sessao::read("arRegistros"));
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
        $dtCompetencia = $arCompetencia[2]."-".$arCompetencia[1];
        Sessao::write("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));

        $nuFaixaInicial = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuFaixaInicial")));
        $nuFaixaFinal = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuFaixaFinal")));
        $nuPercentualReajuste = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuPercentualReajuste")));
        $nuValorReajuste = (float) str_replace(",",".",str_replace(".","",Sessao::read("nuValorReajuste")));
        $stTipoReajuste = Sessao::read("stTipoReajuste");

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadraoPadrao.class.php");
        $obTFolhaPagamentoPadraoPadrao = new TFolhaPagamentoPadraoPadrao();

        $stValoresFiltro = "";
        switch (Sessao::read("stTipoFiltro")) {
            case "contrato":
            case "cgm_contrato":
                $stValoresFiltro = "";
                $arContratos = Sessao::read("arContratos");
                foreach ($arContratos as $arContrato) {
                    $stValoresFiltro .= $arContrato["cod_contrato"].",";
                }
                $stValoresFiltro = substr($stValoresFiltro,0,strlen($stValoresFiltro)-1);
                break;
            case "lotacao":
                $stValoresFiltro = implode(",",Sessao::read("inCodLotacaoSelecionados"));
                break;
            case "local":
                $stValoresFiltro = implode(",",Sessao::read("inCodLocalSelecionados"));
                break;
            case "reg_sub_fun_esp":
                $stValoresFiltro  = implode(",",Sessao::read("inCodRegimeSelecionadosFunc"))."#";
                $stValoresFiltro .= implode(",",Sessao::read("inCodSubDivisaoSelecionadosFunc"))."#";
                $stValoresFiltro .= implode(",",Sessao::read("inCodFuncaoSelecionados"))."#";
                if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
                    $stValoresFiltro .= implode(",",Sessao::read("inCodEspecialidadeSelecionadosFunc"));
                }
                break;
            case "atributo_servidor":
            case "atributo_pensionista":
                $inCodAtributo = Sessao::read("inCodAtributo");
                $inCodCadastro = Sessao::read("inCodCadastro");
                $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
                if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
                    $inArray = 1;
                    $stValores     = implode(",",Sessao::read($stNomeAtributo."_Selecionados"));
                } else {
                    $inArray = 0;
                    $stValores     = Sessao::read($stNomeAtributo);
                }
                $stValoresFiltro = $inArray."#".$inCodAtributo."#".$stValores;
                break;
        }

        if (Sessao::read("stReajuste") == "e") {
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $stFiltroEvento = " WHERE codigo = '".trim(Sessao::read("inCodigoEvento"))."'";
            $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltroEvento);
            $obTFolhaPagamentoPadraoPadrao->setDado("inCodConfiguracao",Sessao::read("inCodConfiguracao"));
            $obTFolhaPagamentoPadraoPadrao->setDado("inCodEvento",$rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoPadraoPadrao->setDado("stFixado",$rsEvento->getCampo("fixado"));
        }

        if (Sessao::read("stCadastro") == "p") {
            if ($rsEvento->getCampo("fixado") == "Q") {
                $stFiltro  = " AND (quantidade_registro BETWEEN ".$nuFaixaInicial." AND ".$nuFaixaFinal.")\n";
            } else {
                $stFiltro  = " AND (valor_registro BETWEEN ".$nuFaixaInicial." AND ".$nuFaixaFinal.")\n";
            }
        } else {
            $stFiltro  = " AND (contrato.valor_padrao BETWEEN ".$nuFaixaInicial." AND ".$nuFaixaFinal.")\n";
        }
        if (Sessao::read("inCodPadrao") != "") {
            $stFiltro .= " AND contrato.cod_padrao = ".Sessao::read("inCodPadrao")."\n";
        }
        $obTFolhaPagamentoPadraoPadrao->setDado("stTipoReajuste",$stTipoReajuste);
        $obTFolhaPagamentoPadraoPadrao->setDado("stReajuste",Sessao::read("stReajuste"));
        $obTFolhaPagamentoPadraoPadrao->setDado("nuValorNovo",$nuValorReajuste);
        $obTFolhaPagamentoPadraoPadrao->setDado("percentual",$nuPercentualReajuste);
        $obTFolhaPagamentoPadraoPadrao->setDado("stTipoFiltro",Sessao::read("stTipoFiltro"));
        $obTFolhaPagamentoPadraoPadrao->setDado("stValoresFiltro",$stValoresFiltro);
        if (Sessao::read("stCadastro") == "o") {
            $obTFolhaPagamentoPadraoPadrao->setDado("stSituacao",'P');
        }
        if (Sessao::read("stCadastro") == "a") {
            $obTFolhaPagamentoPadraoPadrao->setDado("stSituacao",'A');
        }
        if (Sessao::read("stCadastro") == "p") {
            $obTFolhaPagamentoPadraoPadrao->setDado("stSituacao",'E');
        }
        $obTFolhaPagamentoPadraoPadrao->setDado("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoPadraoPadrao->recuperaAjustesSalariais($rsRegistros,$stFiltro," ORDER BY nom_cgm");

        Sessao::write("arRegistros",$rsRegistros->getElementos());
        $arPadroes = array();
        while (!$rsRegistros->eof()) {
            $arPadroes[] = $rsRegistros->getCampo("cod_padrao");
            $rsRegistros->proximo();
        }
        $arPadroes = array_unique($arPadroes);
        Sessao::write("arPadroes",$arPadroes);
        $rsRegistros->setPrimeiroElemento();
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
    $obTable = new TableTree();
    $obTable->setRecordset( $rsRegistros );
    $obTable->setArquivo( CAM_GRH_FOL_INSTANCIAS."rotinaMensal/DTReajustesSalariais.php");
    $obTable->setParametros( array( "sub_divisao","funcao","especialidade" ,"regime","orgao") );
    $obTable->setComplementoParametros( "stAcao=10");
    $obTable->setSummary("Simulação de Valores Reajuste Salarial");
    $obTable->Head->addCabecalho("Matrícula",10);
    $obTable->Head->addCabecalho("Nome",30);
    $obTable->Head->addCabecalho("Valor Atual",10);
    $obTable->Head->addCabecalho("Valor Novo",10);
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Head->addCabecalho("Salário Atual",10);
        $obTable->Head->addCabecalho("Salário Novo",10);
    }
    $obTable->Body->addCampo("registro","D");
    $obTable->Body->addCampo("nom_cgm","E");
    $obTable->Body->addCampo("valor","D");
    $obTable->Body->addCampo("valor_novo","D");
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Body->addCampo("salario","D");
        $obTable->Body->addCampo("salario_novo","D");
    }
    $obTable->Body->addAcao("excluir","executaFuncaoAjax('%s','&registro=%s&cod_contrato=%s')",array('excluirReajusteSalarial','registro','cod_contrato'));
    $obTable->Foot->addSoma ( 'valor', "D" );
    $obTable->Foot->addSoma ( 'valor_novo', "D" );
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Foot->addSoma ( 'salario', "D" );
        $obTable->Foot->addSoma ( 'salario_novo', "D" );
    }
    $obTable->montaHTML(true);
    $stJs  = "d.getElementById('spnReajustesSalariais').innerHTML = '".$obTable->getHtml()."';\n";
    $stJs .= "d.getElementById('ok').disabled = false;\n";
    $stJs .= "d.getElementById('imprimir').disabled = false;\n";
    $stJs .= "d.getElementById('cancelar').disabled = false;\n";

    return $stJs;
}

function gerarSpanReajustesSalariaisExclusao()
{
    $boAposentado  = false;
    $boServidor    = false;
    $boPensionista = false;
    $boAtributo    = false;

    switch (Sessao::read("stCadastro")) {
        case "o":
            $boAposentado = true;
            break;
        case "a":
            $boServidor = true;
            break;
        case "p":
            $boPensionista = true;
            break;
    }

    // Busca Competencia
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $dtCompetencia = $arCompetencia[2]."-".$arCompetencia[1];

    list($inCodReajuste, $stOrigem) = explode("*_*", Sessao::read("inCodReajuste"));

    if (Sessao::read("stReajuste") == "e") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $stFiltroEvento = " WHERE codigo = '".trim(Sessao::read("inCodigoEvento"))."'";
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltroEvento);
    }

    // Adicionando Filtros
    $stFiltro = "";

    # Contrato
    if (is_array(Sessao::read("arContratos"))) {
        $stContratos = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stContratos .= $arContrato["cod_contrato"].",";
        }
        $stContratos = substr($stContratos,0,strlen($stContratos)-1);
        $stFiltro .= " AND contrato.cod_contrato IN (".$stContratos.")";
    }

    # Lotação
    if (is_array(Sessao::read("inCodLotacaoSelecionados"))) {
        if (Sessao::read("stCadastro") == "o" OR Sessao::read("stCadastro") == "a") {
            $stFiltro .= " AND contrato_servidor_orgao.cod_orgao IN (".implode(",",Sessao::read("inCodLotacaoSelecionados")).")";
        }
        if (Sessao::read("stCadastro") == "p") {
            $stFiltro .= " AND contrato_pensionista_orgao.cod_orgao IN (".implode(",",Sessao::read("inCodLotacaoSelecionados")).")";
        }
    }

    # Local
    if (is_array(Sessao::read("inCodLocalSelecionados"))) {
        if (Sessao::read("stCadastro") == "o" OR Sessao::read("stCadastro") == "a") {
            $stFiltro .= " AND EXISTS ( SELECT cod_local
                                             , max(timestamp) as timestamp
                                          FROM pessoal.contrato_servidor_local
                                         WHERE contrato_servidor_local.cod_local IN (".implode(",",Sessao::read("inCodLocalSelecionados")).")
                                           AND contrato.cod_contrato = contrato_servidor_local.cod_contrato
                                      GROUP BY cod_local)";
        }
        if (Sessao::read("stCadastro") == "p") {
            $stFiltro .= " AND EXISTS ( SELECT cod_local
                                             , max(timestamp) as timestamp
                                          FROM pessoal.contrato_pensionista_local
                                         WHERE contrato_pensionista_local.cod_local IN (".implode(",",Sessao::read("inCodLocalSelecionados")).")
                                           AND pensionista.cod_contrato = contrato_pensionista_local.cod_contrato
                                      GROUP BY cod_local)";
        }
    }

    # Função
    if (is_array(Sessao::read("inCodFuncaoSelecionados"))) {
        if (Sessao::read("stCadastro") == "o" OR Sessao::read("stCadastro") == "a") {
            $stFiltro .= " AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN (".implode(",",Sessao::read("inCodSubDivisaoSelecionadosFunc")).")";
            $stFiltro .= " AND contrato_servidor_funcao.cod_cargo IN (".implode(",",Sessao::read("inCodFuncaoSelecionados")).")";
        }
        if (Sessao::read("stCadastro") == "p") {
            $stFiltro .= " AND contrato_pensionista_sub_divisao_funcao.cod_sub_divisao IN (".implode(",",Sessao::read("inCodSubDivisaoSelecionadosFunc")).")";
            $stFiltro .= " AND contrato_pensionista_funcao.cod_cargo IN (".implode(",",Sessao::read("inCodFuncaoSelecionados")).")";
        }
    }

    # Especialidade
    if (is_array(Sessao::read("inCodEspecialidadeSelecionados"))) {
        if (Sessao::read("stCadastro") == "o" OR Sessao::read("stCadastro") == "a") {
            $stFiltro .= " AND contrato_servidor_especialidade.cod_especialidade IN (".implode(",",Sessao::read("inCodEspecialidadeSelecionados")).")";
        }
        if (Sessao::read("stCadastro") == "p") {
            $stFiltro .= " AND contrato_pensionista_especialidade.cod_especialidade IN (".implode(",",Sessao::read("inCodEspecialidadeSelecionados")).")";
        }
    }

    // Atributo
    if (Sessao::read("stTipoFiltro") == "atributo_servidor" OR Sessao::read("stTipoFiltro") == "atributo_pensionista") {
        $inCodAtributo = Sessao::read("inCodAtributo");
        $inCodCadastro = Sessao::read("inCodCadastro");
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
            $stValor = implode(",",Sessao::read($stNomeAtributo."_Selecionados"));
        } else {
            $stValor = trim(Sessao::read($stNomeAtributo));
        }
        if (Sessao::read("stTipoFiltro") == "atributo_servidor") {
            $stFiltro .= " AND atributo_contrato_servidor_valor.cod_atributo = ".$inCodAtributo;
            if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor IN (".$stValor.")";
            } else {
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor = '".$stValor."'";
            }
        }
        if (Sessao::read("stTipoFiltro") == "atributo_pensionista") {
            $stFiltro .= " AND atributo_contrato_pensionista.cod_atributo = ".$inCodAtributo;
            if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
                $stFiltro .= " AND atributo_contrato_pensionista.valor IN (".$stValor.")";
            } else {
                $stFiltro .= " AND atributo_contrato_pensionista.valor = '".$stValor."'";
            }
        }
    }

    if (Sessao::read("stCadastro") == "p") {
        $stFiltro .= " AND pensionista.cod_pensionista is NOT NULL";
        $stFiltro .= " AND contrato_pensionista.dt_encerramento IS NULL";
    }

    if (Sessao::read("stReajuste") == "p") {
        if (Sessao::read("inCodPadrao") != "") {
            if (Sessao::read("stCadastro") == "o" OR Sessao::read("stCadastro") == "a") {
                $stFiltro .= " AND contrato_servidor_padrao.cod_padrao = ".Sessao::read("inCodPadrao");
            }
            if (Sessao::read("stCadastro") == "p") {
                $stFiltro .= " AND contrato_pensionista_padrao.cod_padrao = ".Sessao::read("inCodPadrao");
            }
        }
    }

    if (Sessao::read("stTipoFiltro") == "atributo_servidor" OR Sessao::read("stTipoFiltro") == "atributo_pensionista") {
        $boAtributo = true;
        $inCodAtributo = Sessao::read("inCodAtributo");
        $inCodCadastro = Sessao::read("inCodCadastro");
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
            $stValor = implode(",",Sessao::read($stNomeAtributo."_Selecionados"));
        } else {
            $stValor = trim(Sessao::read($stNomeAtributo));
        }
        if (Sessao::read("stTipoFiltro") == "atributo_servidor") {
            $stFiltro .= " AND atributo_contrato_servidor_valor.cod_atributo = ".$inCodAtributo;
            if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor IN (".$stValor.")";
            } else {
                $stFiltro .= " AND atributo_contrato_servidor_valor.valor = '".$stValor."'";
            }
        }
        if (Sessao::read("stTipoFiltro") == "atributo_pensionista") {
            $stFiltro .= " AND atributo_contrato_pensionista.cod_atributo = ".$inCodAtributo;
            if (is_array(Sessao::read($stNomeAtributo."_Selecionados"))) {
                $stFiltro .= " AND atributo_contrato_pensionista.valor IN (".$stValor.")";
            } else {
                $stFiltro .= " AND atributo_contrato_pensionista.valor = '".$stValor."'";
            }
        }
    }

    // Recupera Dado da Listagem
    switch ($stOrigem) {
        case "C": #Complementar
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoComplementar.class.php");
            $obTFolhaPagamentoReajusteRegistroEventoComplementar = new TFolhaPagamentoReajusteRegistroEventoComplementar();
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("inCodPeriodoMovimentacao", $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("inCodEvento"             , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoReajusteRegistroEventoComplementar->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajusteRegistroEventoComplementar->recuperaReajustePensionistaComplementar($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajusteRegistroEventoComplementar->recuperaReajusteComplementar($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
        case "S": #Sálario
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEvento.class.php");
            $obTFolhaPagamentoReajusteRegistroEventoSalario = new TFolhaPagamentoReajusteRegistroEvento();
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("inCodEvento"             , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoReajusteRegistroEventoSalario->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajusteRegistroEventoSalario->recuperaReajustePensionistaSalario($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajusteRegistroEventoSalario->recuperaReajusteSalario($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
        case "D": #Décimo
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoDecimo.class.php");
            $obTFolhaPagamentoReajusteRegistroEventoDecimo = new TFolhaPagamentoReajusteRegistroEventoDecimo();
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("inCodEvento"             , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoReajusteRegistroEventoDecimo->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajusteRegistroEventoDecimo->recuperaReajustePensionistaDecimo($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajusteRegistroEventoDecimo->recuperaReajusteDecimo($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
        case "F": #Férias
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoFerias.class.php");
            $obTFolhaPagamentoReajusteRegistroEventoFerias = new TFolhaPagamentoReajusteRegistroEventoFerias();
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("inCodEvento"             , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoReajusteRegistroEventoFerias->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajusteRegistroEventoFerias->recuperaReajustePensionistaFerias($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajusteRegistroEventoFerias->recuperaReajusteFerias($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
        case "R": #Rescição
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajusteRegistroEventoRescisao.class.php");
            $obTFolhaPagamentoReajusteRegistroEventoRescisao = new TFolhaPagamentoReajusteRegistroEventoRescisao();
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("inCodPeriodoMovimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("inCodEvento"             , $rsEvento->getCampo("cod_evento"));
            $obTFolhaPagamentoReajusteRegistroEventoRescisao->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajusteRegistroEventoRescisao->recuperaReajustePensionistaRescisao($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajusteRegistroEventoRescisao->recuperaReajusteRescisao($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
        case "P": #Padrão
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajustePadraoPadrao.class.php");
            $obTFolhaPagamentoReajustePadraoPadrao = new TFolhaPagamentoReajustePadraoPadrao();
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("boAposentado"            , $boAposentado);
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("boServidor"              , $boServidor);
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("boPensionista"           , $boPensionista);
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("boAtributo"              , $boAtributo);
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("competencia"             , $dtCompetencia);
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("dtVigencia"              , Sessao::read("dtVigencia"));
            $obTFolhaPagamentoReajustePadraoPadrao->setDado("inCodReajuste"           , $inCodReajuste);
            if (Sessao::read("stCadastro") == "p") {
                $obTFolhaPagamentoReajustePadraoPadrao->recuperaReajustePensionistaPadrao($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            } else {
                $obTFolhaPagamentoReajustePadraoPadrao->recuperaReajustePadrao($rsRegistros, $stFiltro, " ORDER BY nom_cgm");
            }
            break;
    }

    //Inicia montagem da listagem de contratos para excluir o reajuste
    Sessao::write("arRegistros",$rsRegistros->getElementos());
    Sessao::write("arRegistrosExclusao",$rsRegistros->getElementos());
    $arPadroes = array();
    while (!$rsRegistros->eof()) {
        $arPadroes[] = $rsRegistros->getCampo("cod_padrao");
        $rsRegistros->proximo();
    }
    $arPadroes = array_unique($arPadroes);
    Sessao::write("arPadroes",$arPadroes);
    $rsRegistros->setPrimeiroElemento();

    $obChkExcluirReajuste = new CheckBox;
    $obChkExcluirReajuste->setName ('boExcluirReajuste_[cod_contrato]_');
    $obChkExcluirReajuste->setId ('boExcluirReajuste_[cod_contrato]_');
    $obChkExcluirReajuste->setValue(true);
    $obChkExcluirReajuste->setChecked(true);
    $obChkExcluirReajuste->obEvento->setOnChange("montaParametrosGET('atualizarContratosExclusao', '');");

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
    $obTable = new TableTree();
    $obTable->setRecordset( $rsRegistros );
    $obTable->setArquivo( CAM_GRH_FOL_INSTANCIAS."rotinaMensal/DTReajustesSalariais.php");
    $obTable->setParametros( array( "sub_divisao","funcao","especialidade" , "regime", "orgao", "cod_reajuste") );
    $obTable->setComplementoParametros( "stAcao=excluir");
    $obTable->setSummary("Lista de valores para Exclusão do Reajuste Salarial");
    $obTable->Head->addCabecalho("Matrícula",10);
    $obTable->Head->addCabecalho("Nome",50);
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Head->addCabecalho("Padrão", 15);
        $obTable->Head->addCabecalho("Salário",15);
    } else {
        $obTable->Head->addCabecalho("Evento", 20);
        $obTable->Head->addCabecalho("Valor",10);
    }
    $obTable->Head->addCabecalho("Marca/Desmarca",10);

    // Seta os valores referentes
    $obTable->Body->addCampo("registro","D");
    $obTable->Body->addCampo("nom_cgm","E");
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Body->addCampo("padrao","D");
        $obTable->Body->addCampo("salario","D");
    } else {
        $obTable->Body->addCampo("evento","E");
        if ($rsEvento->getCampo("fixado")=="V") {
            Sessao::write("fixado", "V");
            $obTable->Body->addCampo("valor","D");
        } else {
            Sessao::write("fixado", "Q");
            $obTable->Body->addCampo("quantidade","D");
        }
    }
    $obTable->Body->addComponente($obChkExcluirReajuste);

    // Monta os somátorios
    if (Sessao::read("stReajuste") == "p") {
        $obTable->Foot->addSoma ( 'padrao', "D" );
        $obTable->Foot->addSoma ( 'salario', "D" );
    } else {
        if ($rsEvento->getCampo("fixado")=="V") {
            $obTable->Foot->addSoma ( 'valor', "D" );
        } else {
            $obTable->Foot->addSoma ( 'quantidade', "D" );
        }
    }
    $obTable->Foot->addSoma ( 'Marca/Desmarca' );

    $obTable->montaHTML(true);

    $stJs  = "d.getElementById('spnReajustesSalariais').innerHTML = '".$obTable->getHtml()."';  \n";
    $stJs .= "d.getElementById('btnExcluir').disabled = false;                                  \n";
    $stJs .= "d.getElementById('imprimir').disabled = false;                                    \n";
    $stJs .= "d.getElementById('cancelar').disabled = false;                                    \n";
    $stJs .= "d.getElementById('marcaTodos').disabled = false;                                  \n";
    $stJs .= "d.getElementById('desmarcaTodos').disabled = false;                               \n";

    return $stJs;
}

function submeterSimulacao()
{
    $stId = str_replace("&","*_*",Sessao::getId());
    $stJs = "alertaQuestao('".CAM_GRH_FOL_INSTANCIAS."rotinaMensal/PRReajustesSalariais.php?$stId*_*stAcao=".$_GET["stAcao"]."*_*stDescQuestao=Cuidado: Não executar duas vezes o reajuste salarial para a mesma seleção.','sn_excluir','".Sessao::getId()."');\n";

    return $stJs;
}

function removerReajuste()
{
    if (count(Sessao::read("arRegistrosExclusao")) == 0) {
        $stJs = "alertaAviso('Deve ser selecionado pelo menos um contrato para a exclusão do reajuste.','form','erro','".Sessao::getId()."');\n";
    } else {
        $stId = str_replace("&","*_*",Sessao::getId());
        $stJs = "alertaQuestao('".CAM_GRH_FOL_INSTANCIAS."rotinaMensal/PRReajustesSalariais.php?$stId*_*stAcao=".$_GET["stAcao"]."*_*stDescQuestao=','sn_excluir','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirReajusteSalarial()
{
    $arRegistros = Sessao::read("arRegistros");
    $arTemp = array();
    $arCodContratosExcluidos = Sessao::read("arCodContratosExcluidos");
    $arCodContratosExcluidos[] = $_GET["cod_contrato"];
    Sessao::write("arCodContratosExcluidos",$arCodContratosExcluidos);
    foreach ($arRegistros as $arRegistro) {
        if ($arRegistro["registro"] != $_GET["registro"]) {
            $arTemp[] = $arRegistro;
        }
    }
    Sessao::write("arRegistros",$arTemp);
    $stJs = gerarSpanReajustesSalariais();

    return $stJs;
}

function desmarcaTodos()
{
    Sessao::write("arRegistrosExclusao", array());
    $stJs   = "    for (indice=0; indice<document.forms.length; indice++) { \n";
    $stJs  .= "        for (i=0; i<document.forms[indice].elements.length; i++) { \n";
    $stJs  .= "            if (document.forms[indice].elements[i].type == 'checkbox') { \n";
    $stJs  .= "                document.forms[indice].elements[i].checked = false; \n";
    $stJs  .= "            } \n";
    $stJs  .= "        } \n";
    $stJs  .= "    } \n";

    return $stJs;
}

function marcaTodos()
{
    $arRegistros = Sessao::read("arRegistros");
    Sessao::write("arRegistrosExclusao", $arRegistros);

    $stJs  = "    for (indice=0; indice<document.forms.length; indice++) { \n";
    $stJs .= "        for (i=0; i<document.forms[indice].elements.length; i++) { \n";
    $stJs .= "            if (document.forms[indice].elements[i].type == 'checkbox') { \n";
    $stJs .= "                document.forms[indice].elements[i].checked = true; \n";
    $stJs .= "            } \n";
    $stJs .= "        } \n";
    $stJs .= "    } \n";

    return $stJs;
}

function limparFiltro()
{
    $stJs .= " jQuery('#stReajustePadrao').attr('disabled', 'disabled'); \n";
    $stJs .= " jQuery('#stReajusteEvento').attr('disabled', 'disabled'); \n";

    if ($_GET["stCadastro"] == "p") {
        $_GET["stReajuste"] = "e";
        $stJs .= gerarSpanValoresReajustes();
        $stJs .= " jQuery('#stReajusteEvento').attr('disabled', ''); \n";
        $stJs .= " jQuery('#stReajusteEvento').attr('checked', 'checked'); \n";
    } else {
        $stJs .= " jQuery('#stReajustePadrao').attr('disabled', ''); \n";
        $stJs .= " jQuery('#stReajusteEvento').attr('disabled', ''); \n";
    }

    return $stJs;
}

function atualizarContratosExclusao()
{
    $arContratosExclusao = array();
    foreach ($_GET as $chave => $valor) {
        if (strpos($chave, "boExcluirReajuste") !== FALSE) {
              $arAux = explode("_", $chave);
              $arContratosExclusao[] = $arAux[1];
        }
    }

    $arTemp = array();
    foreach (Sessao::read("arRegistros") as $arRegistro) {
        if (in_array($arRegistro["cod_contrato"], $arContratosExclusao)) {
           $arTemp[] =  $arRegistro;
        }
    }

    Sessao::remove("arRegistrosExclusao");
    Sessao::write("arRegistrosExclusao", $arTemp);

    return $stJs;
}

function montaNorma($stSelecionado = "")
{
    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.$stCombo,0); \n";
    $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
    //$stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

    if ($_REQUEST[ 'inCodTipoNorma' ] != "") {
        $inCodTipoNorma = $_REQUEST[ 'inCodTipoNorma' ];

        $stFiltro = " WHERE cod_tipo_norma =".$inCodTipoNorma;
        $obTNorma = new TNorma();
        $obTNorma->recuperaNormas( $rsCombo, $stFiltro );

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId               = str_replace(' ','',$rsCombo->getCampo("cod_norma"));
            $stDesc             = $rsCombo->getCampo("nom_norma");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }

    sistemaLegado::executaFrameOculto($stJs);
}

switch ($_REQUEST["stCtrl"]) {
    case "gerarSpanValoresReajustes":
        $stJs = gerarSpanValoresReajustes();
        break;
    case "gerarSpanTipoReajuste":
        $stJs = gerarSpanTipoReajuste();
        break;
    case "gerarSpanNivelPadrao":
        $stJs = gerarSpanNivelPadrao();
        break;
    case "processarForm":
        $stJs = processarForm();
        break;
    case "preencherEventoFixado":
        $stJs = preencherEventoFixado();
        break;
    case "preencherObservacao":
        $stJs = preencherObservacao();
        break;
    case "preencheComboReajuste":
        $stJs = preencheComboReajuste();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "gerarSpanReajustesSalariais":
        $stJs  = gerarSpanReajustesSalariais();
        $stJs .= "LiberaFrames(true, false);";
        break;
    case "gerarSpanReajustesSalariaisExclusao":
        $stJs = gerarSpanReajustesSalariaisExclusao();
        $stJs .= "LiberaFrames(true, false);";
        break;
    case "submeterSimulacao":
        $stJs = submeterSimulacao();
        break;
    case "removerReajuste":
        $stJs = removerReajuste();
        break;
    case "excluirReajusteSalarial":
        $stJs = excluirReajusteSalarial();
        break;
    case "limparFiltro":
        $stJs = limparFiltro();
        break;
    case "marcaTodos":
        $stJs = marcaTodos();
        break;
    case "desmarcaTodos":
        $stJs = desmarcaTodos();
        break;
    case "atualizarContratosExclusao":
        $stJs = atualizarContratosExclusao();
        break;
    case "montaNorma":
        $stJs = montaNorma();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
