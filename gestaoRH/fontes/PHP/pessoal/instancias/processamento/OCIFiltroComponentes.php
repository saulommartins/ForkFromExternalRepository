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
    * Oculto do componente IFiltroAtributoDinamico
    * Data de Criação: 20/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCIFiltroComponentes.php 66003 2016-07-06 20:26:49Z evandro $

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpan()
{
    $boAtualizarLotacao = false;

    Sessao::write('arContratos',"");
    Sessao::write('arPensionistas',"");
    Sessao::write('arEstagios',"");
    switch ($_GET["stTipoFiltro"]) {
        case "contrato":
        case "contrato_todos":
        case "contrato_rescisao":
        case "contrato_aposentado":
            $stHtml = montaSpanContrato($stJs,$_GET["stTipoFiltro"]);
            break;
        case "contrato_rescisao":
            $stHtml = montaSpanContrato($stJs, true);
            break;
        case "contrato_pensionista":
            $stHtml = montaSpanContratoPensionista($stJs);
            break;
        case "cgm_contrato":
        case "cgm_contrato_todos":
        case "cgm_contrato_rescisao":
        case "cgm_contrato_aposentado":
            $stHtml = montaSpanCGMContrato($stJs,$_GET["stTipoFiltro"]);
            break;
        case "cgm_contrato_rescisao":
            $stHtml = montaSpanCGMContrato($stJs, true);
            break;
        case "cgm_contrato_pensionista":
            $stHtml = montaSpanCGMContratoPensionista($stJs);
            break;

        //#################
        //INÍCIO ESTAGIÁRIO
        //#################
        case "cgm_codigo_estagio":
            $stHtml = montaSpanCGMCodigoEstagio($stJs);
            break;
        case "atributo_estagiario":
        case "atributo_estagiario_grupo":
            $stHtml = montaSpanAtributoEstagio($stEval,true);
            break;
        case "instituicao_ensino":
            $stHtml = montaSpanInstituicaoEnsino($stEval);
            break;
        case "entidade_intermediadora":
            $stHtml = montaSpanEntidadeIntermediadora($stEval);
            break;
        //#################
        //FIM ESTAGIÁRIO
        //#################

        case "lotacao":
            $stHtml = montaSpanLotacao($stEval);
            $boAtualizarLotacao = true;
            break;
        case "local":
            $stHtml = montaSpanLocal($stEval);
            break;
        case "lotacao_grupo":
            $stHtml = montaSpanLotacao($stEval,true);
            $boAtualizarLotacao = true;
            break;
        case "local_grupo":
            $stHtml = montaSpanLocal($stEval,true);
            break;
        case "atributo_servidor":
            $stHtml = montaSpanAtributo($stEval,"servidor");
            break;
        case "atributo_servidor_grupo":
            $stHtml = montaSpanAtributo($stEval,"servidor",true);
            break;
        case "atributo_pensionista":
            $stHtml = montaSpanAtributo($stEval,"pensionista");
            break;
        case "atributo_pensionista_grupo":
            $stHtml = montaSpanAtributo($stEval,"pensionista",true);
            break;
        case "sub_divisao":
            $stHtml = montaSpanRegimeSubdivisao($stEval);
            break;
        case "sub_divisao_grupo":
            $stHtml = montaSpanRegimeSubdivisao($stEval,true);
            break;
        case "sub_divisao_funcao":
            $stHtml = montaSpanRegimeSubdivisao($stEval,false,true);
            break;
        case "sub_divisao_funcao_grupo":
            $stHtml = montaSpanRegimeSubdivisao($stEval,true,true);
            break;
        case "reg_sub_car_esp":
            $stHtml = montaSpanRegSubCarEsp($stEval);
            break;
        case "reg_sub_car_esp_grupo":
            $stHtml = montaSpanRegSubCarEsp($stEval,true);
            break;
        case "reg_sub_fun_esp":
            $stHtml = montaSpanRegSubFunEsp($stEval);
            break;
        case "reg_sub_fun_esp_grupo":
            $stHtml = montaSpanRegSubFunEsp($stEval, true);
            break;
        case "padrao":
            $stHtml = montaSpanPadrao($stEval);
            break;
        case "padrao_grupo":
            $stHtml = montaSpanPadrao($stEval,true);
            break;
        case "evento":
            $stHtml = montaSpanEvento($stJs);
            break;
        case "evento_multiplo":
            $stHtml = montaSpanEventoMultiplo($stEval);
            break;
        case "cargo":
            $stHtml = montaSpanCargo($stEval);
            break;
        case "cargo_grupo":
            $stHtml = montaSpanCargo($stEval,true);
            break;
        case "funcao":
            $stHtml = montaSpanCargo($stEval,false,true);
            break;
        case "funcao_grupo":
            $stHtml = montaSpanCargo($stEval,true,true);
            break;
        case "periodo":
            $stHtml = montaSpanPeriodo($stEval);
            break;
        case "geral":
            $stHtml = "";
            $stEval = "";
            break;
    }
    $stEval = isset($stEval) ? $stEval : "";
    $stJs = isset($stJs) ? $stJs : "";
    $stJs .= "d.getElementById('spnTipoFiltro').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnTipoFiltro.value = '$stEval';\n";

    if ($boAtualizarLotacao === true) {
        $stJs .= atualizarLotacao();
    }

    return $stJs;
}

function atualizarLotacao()
{
    include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."ISelectAnoCompetencia.class.php");
    include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

    $stJs 					 = "";
    $arFiltroCompetencia 	 = Sessao::read("arFiltroCompetencia");
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

function montaSpanPeriodo(&$stEval)
{
    $obPeriodo = new Periodo();
    $obPeriodo->setNull(false);
    $obPeriodo->setRotulo(Sessao::read("stRotuloPeriodoComponente"));
    $obFormulario = new Formulario;
    $obFormulario->addComponente($obPeriodo);
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanCargo(&$stEval,$boGrupo=false,$boFuncao=false)
{
    include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploCargo.class.php");
    $obISelectMultiploCargo = new ISelectMultiploCargo($boFuncao);
    if ($boFuncao) {
        $obISelectMultiploCargo->obCmbFuncao->setNull(false);
    } else {
        $obISelectMultiploCargo->obCmbCargo->setNull(false);
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Cargo");
    $obISelectMultiploCargo->geraFormulario($obFormulario);
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
    include_once ( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php" );

    $stName = "Evento";

    $stJs1 = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inCodigoEvento='+document.frm.inCodigoEvento.value+'&hdnDescEvento='+document.frm.hdnDescEvento.value,'incluir$stName' );  \n";
    $stJs2 = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."','limpaFormulario$stName' );";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName             ( "btIncluir$stName"         );
    $obBtnIncluir->setValue            ( "Incluir"                  );
    $obBtnIncluir->obEvento->setOnClick( $stJs1.$stJs2              );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( $stJs2                     );
    $arBarra[] = $obBtnLimpar;

    $obSpnEventos = new Span;
    $obSpnEventos->setid                            ( "spnEventos"                                                      );

    $obIBscEvento = new IBscEvento;
    $obIBscEvento->setEventoSistema("");
    $obIBscEvento->obBscInnerEvento->setNullBarra   ( false                                                             );
    $obIBscEvento->obTxtValor->setNullBarra         ( false                                                             );

    $obFormulario = new Formulario;
    $obIBscEvento->geraFormulario                   ( $obFormulario                                                     );
    $obFormulario->defineBarra                      ( $arBarra                                                          );
    $obFormulario->addSpan                          ( $obSpnEventos                                                     );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();

    return $obFormulario->getHTML();
}

function montaSpanEventoMultiplo(&$stEval)
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                         );

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

function montaSpanPadrao(&$stEval,$boGrupo=false)
{
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                               );

    $obFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
    $obFolhaPagamentoPadrao->listarPadrao($rsPadrao);
    $obCmbPadrao = new SelectMultiplo();
    $obCmbPadrao->setName              ( 'inCodPadrao'                                                 );
    $obCmbPadrao->setRotulo            ( "Padrão"                                                      );
    $obCmbPadrao->setTitle             ( "Selecione o(s) padrão(ões)."                                 );
    $obCmbPadrao->SetNomeLista1        ( 'inCodPadraoDisponiveis'                                      );
    $obCmbPadrao->setCampoId1          ( '[cod_padrao]'                                                );
    $obCmbPadrao->setCampoDesc1        ( '[descricao]'                                                 );
    $obCmbPadrao->setStyle1            ( "width: 300px"                                                );
    $obCmbPadrao->SetRecord1           ( $rsPadrao                                                     );
    $obCmbPadrao->SetNomeLista2        ( 'inCodPadraoSelecionados'                                     );
    $obCmbPadrao->setCampoId2          ( '[cod_Padrao]'                                                );
    $obCmbPadrao->setCampoDesc2        ( '[descricao]'                                                 );
    $obCmbPadrao->setStyle2            ( "width: 300px"                                                );
    $obCmbPadrao->SetRecord2           ( new recordset                                                 );
    $obCmbPadrao->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Padrão");
    $obFormulario->addComponente( $obCmbPadrao );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanRegSubCarEsp(&$stEval,$boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                          );
    $obISelectMultiploRegSubCarEsp = new ISelectMultiploRegSubCarEsp;
    $obISelectMultiploRegSubCarEsp->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Regime/Subdivisão/Cargo/Especialidade");
    $obISelectMultiploRegSubCarEsp->geraFormulario( $obFormulario );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanRegSubFunEsp(&$stEval, $boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploRegSubCarEsp.class.php"                          );
    $obISelectMultiploRegSubCarEsp = new ISelectMultiploRegSubCarEsp(true);
    $obISelectMultiploRegSubCarEsp->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Regime/Subdivisão/Função/Especialidade");
    $obISelectMultiploRegSubCarEsp->geraFormulario( $obFormulario );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function addComponenteAgrupamento(&$obFormulario)
{
    $obChkAgrupar = new CheckBox();
    $obChkAgrupar->setRotulo("Agrupamento");
    $obChkAgrupar->setLabel("Agrupar");
    $obChkAgrupar->setName("boAgrupar");
    $obChkAgrupar->setValue("true");
    $obChkAgrupar->setTitle("Marque para agrupar e quebrar página no relatório.");
    if ($_GET["boQuebrarDisabled"] == "false") {
        $obChkAgrupar->obEvento->setOnChange("document.frm.boQuebrar.disabled = !document.frm.boQuebrar.disabled;");

        $obChkQuebrarPagina = new CheckBox();
        $obChkQuebrarPagina->setRotulo("Agrupamento");
        $obChkQuebrarPagina->setLabel("Quebrar Página");
        $obChkQuebrarPagina->setName("boQuebrar");
        $obChkQuebrarPagina->setValue("true");
        $obChkQuebrarPagina->setTitle("Marque para agrupar e quebrar página no relatório.");
        $obChkQuebrarPagina->setDisabled(true);

        $obFormulario->addComponenteComposto($obChkAgrupar,$obChkQuebrarPagina);
    } else {
        $obFormulario->addComponente($obChkAgrupar);
    }
}

function montaSpanLotacao(&$stEval,$boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLotacao.class.php' );
    global $request;
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;
    $obISelectMultiploLotacao->setNull(false);
    if (trim($request->get("inAno")) != "" and trim($_GET["inCodMes"]) != "") {
        $inDia = date("t",mktime(0,0,0,$_GET["inCodMes"],1,$request->get("inAno")));
        $dtCompetencia = date("Y-m-d",mktime(0,0,0,$_GET["inCodMes"],$inDia,$request->get("inAno")));
        $obISelectMultiploLotacao->obTOrganogramaOrgao->setDado('vigencia', $dtCompetencia);
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Lotação");
    $obFormulario->addComponente( $obISelectMultiploLotacao );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }

    if ($request->get('boHdnLotacaoSubNivel')) {
        $obCheckLotacaoSubNivel = new CheckBox();
        $obCheckLotacaoSubNivel->setRotulo ('Subníveis da lotação');
        $obCheckLotacaoSubNivel->setTitle  ('Selecionar para que sejam incluídos os subníveis dos orgãos das lotações relacionadas.');
        $obCheckLotacaoSubNivel->setId     ('boSubNivelLotacao');
        $obCheckLotacaoSubNivel->setName   ('boSubNivelLotacao');
        $obCheckLotacaoSubNivel->setValue  (true); 
        $obFormulario->addComponente( $obCheckLotacaoSubNivel );   
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function montaSpanLocal(&$stEval,$boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLocal.class.php'   );

    $obISelectMultiploLocal   = new ISelectMultiploLocal;
    $obISelectMultiploLocal->setNull(false);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Local");
    $obFormulario->addComponente( $obISelectMultiploLocal   );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
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

    $stJs = "f.hdnValidaMatriculas.value = '$stHdnValidaMatriculas';\n";

    return $stJs;
}

function montaSpanContrato(&$stJs, $stTipo="contrato_todos")
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php'   );
    //$stTipo
    //todos= todos os servidores
    //contrato=somente servidores não rescindidos e não aposentados
    //aposentados=somente servidores aposentados
    //rescindidos=somente servidores rescindidos

    $obSpnContratos = new Span;
    $obSpnContratos->setid( "spnContratos");

    $stName = "Contrato";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContrato='+document.frm.inContrato.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obLblCGM = new Label;
    $obLblCGM->setRotulo ( "CGM"      );
    $obLblCGM->setName   ( "inNomCGM" );
    $obLblCGM->setId     ( "inNomCGM" );

    $obHdnCGM = new Hidden;
    $obHdnCGM->setName                  ( "hdnCGM"   );
    $obHdnCGM->setValue                 ( ""         );

    $obIContratoDigitoVerificador = new IContratoDigitoVerificador();
    $obIContratoDigitoVerificador->setPagFiltro(true);
    $obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur   ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContrato='+this.value+'&stTipo=".$stTipo."', 'preencheCGMContrato' );" );
    $obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContrato='+this.value+'&stTipo=".$stTipo."', 'preencheCGMContrato' );" );
    $obIContratoDigitoVerificador->setFuncaoBuscaFiltro("abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLMatriculaFiltroComponente.php','frm','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getId()."','','".Sessao::getId()."&stTipo=".$stTipo."','800','550')");

    //$obIFiltroContrato = new IFiltroContrato($boRescindido);
    //$obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra(false);

    $obFormulario = new Formulario;
    $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    //$obIFiltroContrato->geraFormulario($obFormulario);
    $obFormulario->Incluir($stName,array($obIContratoDigitoVerificador->obTxtRegistroContrato,
                                            $obLblCGM,
                                            $obHdnCGM),true);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas($stTipo);

    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnCGM);
    $obFormulario->addComponente($obLblCGM);
    $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    //$obIFiltroContrato->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function montaSpanContratoPensionista(&$stJs)
{
    Sessao::write("arPensionistas",array());

    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroContrato.class.php'   );
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroPensionista.class.php'   );

    $obSpnContratos = new Span;
    $obSpnContratos->setid( "spnContratosPensionistas");

    $stName = "ContratoPensionista";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContratoPensionista='+document.frm.inContratoPensionista.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obIFiltroPensionista = new IFiltroPensionista(true);
    $obIFiltroPensionista->obIContratoDigitoVerificador->obTxtRegistroContrato->setNullBarra(false);

    $obFormulario = new Formulario;
    $obIFiltroPensionista->geraFormulario($obFormulario);
    $obFormulario->Incluir($stName,array($obIFiltroPensionista->obIContratoDigitoVerificador->obTxtRegistroContrato,
                                            $obIFiltroPensionista->obLblCGM,
                                            $obIFiltroPensionista->obHdnCGM),true);
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

function montaSpanCGMContrato(&$stJs, $boRescindido=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMContrato.class.php'   );

    $obSpnContratos = new Span;
    $obSpnContratos->setid( "spnContratos");

    $stName = "Contrato";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContrato='+document.frm.inContrato.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obIFiltroCGMContrato = new IFiltroCGMContrato($boRescindido);
    $obIFiltroCGMContrato->obCmbContrato->setNullBarra(false);
    $obIFiltroCGMContrato->obBscCGM->setNullBarra(false);

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario($obFormulario);
    $obFormulario->Incluir("Contrato",array($obIFiltroCGMContrato->obCmbContrato,
                                $obIFiltroCGMContrato->obBscCGM),true);

    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas("cgm_contrato");

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function montaSpanCGMContratoPensionista(&$stJs)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMContrato.class.php'   );
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMPensionista.class.php'   );

    $obSpnContratos = new Span;
    $obSpnContratos->setid( "spnContratosPensionistas");

    $stName = "ContratoPensionista";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inContratoPensionista='+document.frm.inContratoPensionista.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obIFiltroCGMPensionista = new IFiltroCGMPensionista(true);
    $obIFiltroCGMPensionista->obCmbContrato->setNullBarra(false);
    $obIFiltroCGMPensionista->obBscCGM->setNullBarra(false);

    $obFormulario = new Formulario;
    $obIFiltroCGMPensionista->geraFormulario($obFormulario);

    $obFormulario->Incluir($stName,array($obIFiltroCGMPensionista->obCmbContrato,
                                              $obIFiltroCGMPensionista->obBscCGM),true);

    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas("cgm_contrato_pensionista");

    $obFormulario = new Formulario;
    $obIFiltroCGMPensionista->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratos);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function incluirContrato()
{
    $obErro = new erro;
    $concederFerias = sessao::read("boPossuiEvento");
    $stJs = isset($stJs) ? $stJs : "";
    if ($concederFerias) {

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_REQUEST["inAno"]);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_REQUEST["inCodMes"]);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
        if ($rsPeriodoMovimentacao->getNumLinhas() == -1) {
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        }

        if (!$obErro->ocorreu()) {
            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php");
            $obTPessoalFerias = new TPessoalFerias;
            $stFiltroEvento = " INNER JOIN pessoal.contrato
                                        ON contrato.cod_contrato = registro_evento_periodo.cod_contrato
                                     WHERE registro_evento_periodo.cod_periodo_movimentacao =".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao")."
                                       AND contrato.registro =".$_GET['inContrato'];
            $obTPessoalFerias->possuiEvento($rsPossuiEventos, $stFiltroEvento);
            if ($rsPossuiEventos->getNumLinhas() == -1) {
                $obErro->setDescricao("Erro ao Conceder Férias, matricula ".$_GET['inContrato']." não possui registros na folha salário para o cálculo das férias.");
            }
        }
    }

    if (!$obErro->ocorreu()) {
        $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();
        foreach ($arContratos as $arContrato) {
            if ($arContrato['inContrato'] == $_GET['inContrato']) {
                $obErro->setDescricao("Matrícula já inserida na lista.");
                break;
            }
        }
    }

    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
    }

    if (!$obErro->ocorreu()) {
        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arContratos[]                          = $arContrato;
        Sessao::write("arContratos",$arContratos);
        $stJs .= montaListaContratos(Sessao::read('arContratos'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContrato()
{
    $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();
    $arTemp = array();
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arContrato['inId'] = $inId;
            $arTemp[] = $arContrato;
        }
    }
    Sessao::write("arContratos",$arTemp);
    $stJs .= montaListaContratos(Sessao::read('arContratos'));

    return $stJs;
}

function montaListaContratos($arContratos)
{
    $rsContratos = new Recordset;
    $rsContratos->preenche($arContratos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Matrículas");
    $obLista->setRecordSet( $rsContratos );
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
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContrato]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."','excluirContrato');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnContratos').innerHTML = '".$stHtml."';   \n";
    if ($rsContratos->getNumLinhas() > 0) {
        $stJs .= "f.inValidaMatriculas.value = '1';";
    } else {
        $stJs .= "f.inValidaMatriculas.value = '0';";
    }

    return $stJs;
}

function incluirContratoPensionista()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arPensionistas = ( is_array(Sessao::read('arPensionistas')) ) ? Sessao::read('arPensionistas') : array();
        foreach ($arPensionistas as $arPensionista) {
            if ($arPensionista['inContratoPensionista'] == $_GET['inContratoPensionista']) {
                $obErro->setDescricao("Matrícula já inserida na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContratoPensionista'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
        
        $arPensionistas = Sessao::read("arPensionistas");
        $arPensionista                             = array();
        $arPensionista['inId']                     = count($arPensionistas);
        $arPensionista['inContratoPensionista']    = $_GET['inContratoPensionista'];
        $arPensionista['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arPensionista['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arPensionista['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arPensionistas[]        = $arPensionista;
        Sessao::write("arPensionistas",$arPensionistas);
        $stJs .= montaListaPensionistas(Sessao::read('arPensionistas'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContratoPensionista()
{
    $arPensionistas = ( is_array(Sessao::read('arPensionistas')) ) ? Sessao::read('arPensionistas') : array();
    $arTemp = array();
    foreach ($arPensionistas as $arPensionista) {
        if ($arPensionista['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arPensionista['inId'] = $inId;
            $arTemp[] = $arPensionista;
        }
    }
    Sessao::write("arPensionistas",$arTemp);
    $stJs .= montaListaPensionistas(Sessao::read('arPensionistas'));

    return $stJs;
}

function montaListaPensionistas($arPensionistas)
{
    $rsPensionistas = new Recordset;
    $rsPensionistas->preenche($arPensionistas);

    $obLista = new Lista;
    $obLista->setTitulo("Matrículas");
    $obLista->setRecordSet( $rsPensionistas );
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
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inContratoPensionista]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."','excluirContratoPensionista');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratosPensionistas').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function montaSpanAtributo(&$stEval,$stCadastro,$boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroAtributoDinamico.class.php' );
    $obIFiltroAtributoDinamico = new IFiltroAtributoDinamico();
    switch ($stCadastro) {
        case "servidor":
            $obIFiltroAtributoDinamico->setServidor();
            break;
        case "pensionista":
            $obIFiltroAtributoDinamico->setPensionista();
            break;
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Atributo Dinâmico");
    $obIFiltroAtributoDinamico->geraFormulario($obFormulario);
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n", "", $stEval);

    return $obFormulario->getHTML();
}

function montaSpanRegimeSubDivisao(&$stEval,$boGrupo=false,$boFuncao=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploRegSubCarEsp.class.php' );

    $obIFiltroRegSubCarEsp = new ISelectMultiploRegSubCarEsp($boFuncao);
    $obIFiltroRegSubCarEsp->setDisabledFuncao        ( true );
    $obIFiltroRegSubCarEsp->setDisabledCargo         ( true );
    $obIFiltroRegSubCarEsp->setDisabledEspecialidade ( true );
    $obIFiltroRegSubCarEsp->setNull(false);

    $obFormulario = new Formulario;
    if ($boFuncao) {
        $obFormulario->addTitulo("Filtro por Regime/SubDivisão Função");
    } else {
        $obFormulario->addTitulo("Filtro por Regime/SubDivisão");
    }
    $obIFiltroRegSubCarEsp->geraFormulario       ( $obFormulario                                             );
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    return $obFormulario->getHTML();
}

function incluirEvento()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arEventos = ( is_array(Sessao::read('arEventos')) ) ? Sessao::read('arEventos') : array();
        foreach ($arEventos as $arEvento) {
            if ($arEvento['inCodigoEvento'] == $_GET['inCodigoEvento']) {
                $obErro->setDescricao("Evento já inserida na lista.");
                break;
            }
        }
    }
    if ($_GET['inCodigoEvento'] == '' || $_GET['hdnDescEvento'] == '') {
        $obErro->setDescricao("Evento inválido.(".$_GET['inCodigoEvento'].")");
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = " WHERE codigo = '".$_GET['inCodigoEvento']."'";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro);
        $arEventos = Sessao::read("arEventos");
        $arEvento                             = array();
        $arEvento['inId']                     = count($arEventos);
        $arEvento['inCodigoEvento']           = $_GET['inCodigoEvento'];
        $arEvento['stDescEvento']             = $_GET["hdnDescEvento"];
        $arEvento['inCodEvento']              = $rsEventos->getCampo("cod_evento");
        $arEventos[]        = $arEvento;
        Sessao::write("arEventos",$arEventos);
        $stJs = montaListaEventos(Sessao::read('arEventos'));
    } else {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirEvento()
{
    $arEventos = ( is_array(Sessao::read('arEventos')) ) ? Sessao::read('arEventos') : array();
    $arTemp = array();
    foreach ($arEventos as $arEvento) {
        if ($arEvento['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arEvento['inId'] = $inId;
            $arTemp[] = $arEvento;
        }
    }
    Sessao::write("arEventos",$arTemp);
    $stJs = montaListaEventos(Sessao::read('arEventos'));

    return $stJs;
}

function montaListaEventos($arEventos)
{
    $rsEventos = new Recordset;
    $rsEventos->preenche($arEventos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Eventos para Filtro");
    $obLista->setRecordSet( $rsEventos );
    $obLista->setMostraPaginacao( false );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descricao");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inCodigoEvento]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[stDescEvento]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."','excluirEvento');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnEventos').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

function limpaFormularioEvento()
{
    $stJs  = "document.frm.inCodigoEvento.value = '';                               \n";
    $stJs .= "document.getElementById('stEvento').innerHTML = '&nbsp;';             \n";
    $stJs .= "document.frm.HdninCodigoEvento.value = '';                            \n";
    $stJs .= "document.frm.hdnDescEvento.value = '';                                \n";
    $stJs .= "document.getElementById('stTextoComplementar').innerHTML = '&nbsp;';  \n";

    return $stJs;
}

function gerarSpanAtributosDinamicos()
{
    if ($_REQUEST['inCodAtributo'] != "") {
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$_REQUEST['inCodAtributo']) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obChkAtributoDinamico = new CheckBox;
        $obChkAtributoDinamico->setName                 ( "boAtributoDinamico"                                                                          );
        $obChkAtributoDinamico->setStyle                ( ""                                                                                            );
        $obChkAtributoDinamico->setRotulo               ( "Ordenar/Agrupar Dados"                                                                       );
        $obChkAtributoDinamico->setTitle                ( "Selecionando está opção será habilitada a opção para ordenar da informação no relatório."    );
        $obChkAtributoDinamico->setValue                ( true                                                                                          );

        $obChkEmitirTotais = new CheckBox;
        $obChkEmitirTotais->setName                     ( "boEmitirTotais"                                                                              );
        $obChkEmitirTotais->setRotulo                   ( "Emitir Totais por Agrupamento"                                                               );
        $obChkEmitirTotais->setTitle                    ( "Selecionando a opção o relatório apresentará os totais ao final de cada agrupamento."        );
        $obChkEmitirTotais->setValue                    ( true                                                                                          );

        $obChkEmitirRelatorio = new CheckBox;
        $obChkEmitirRelatorio->setName                  ( "boEmitirRelatorio"                                                                           );
        $obChkEmitirRelatorio->setRotulo                ( "Emitir Somente Relatório de Totais"                                                          );
        $obChkEmitirRelatorio->setTitle                 ( "Selecionando a opção o relatório apresentará somente os totais dos agrupamentos."            );
        $obChkEmitirRelatorio->setValue                 ( true                                                                                          );

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->addComponente($obChkAtributoDinamico);
        $obFormulario->addComponente($obChkEmitirTotais);
        $obFormulario->addComponente($obChkEmitirRelatorio);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $Js .= $obFormulario->getInnerJavaScript();
    }
    $stJs .= "d.getElementById('spnAtributoDinamico').innerHTML = '$stHtml';                                 \n";
    $stJs .= "f.hdnEvalAtributosDinamicos.value = f.hdnEvalAtributosDinamicos.value + '$Js';                 \n";

    return $stJs;
}

function preencheCGMContrato()
{
    $stNomCGM = "";
    $inRegistro = "";
    $stSubFiltro="";
    if ($_GET["inContrato"] != "") {
        $Competencia=Sessao::read('Competencia');
        if(is_array($Competencia)){
            $stDataFinal= explode('/',(SistemaLegado::retornaUltimoDiaMes($Competencia['inCodMes'],$Competencia['inAno'] )));
            $stDataFinal=$stDataFinal[2]."-".$stDataFinal[1]."-".$stDataFinal[0];
            $stSubFiltro = "AND dt_rescisao <= TO_DATE('".$stDataFinal."','yyyy-mm-dd') ";
        }        
    switch ($_GET["stTipo"]) {
            case "contrato":
                $boValidaAtivos = Sessao::read('valida_ativos_cgm');
                $stFiltro  = " AND registro = ".$_GET["inContrato"];
                $stFiltro .= " AND NOT EXISTS (SELECT 1 FROM pessoal.contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato ".$stSubFiltro.")";
                if ($boValidaAtivos == 'true') {
                    $stFiltro .= " AND situacao ILIKE '%Ativo%' ";
                }
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);                
                if ($rsCGM->getNumLinhas() == 1) {
                    $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". trim($rsCGM->getCampo('nom_cgm'));
                    $inRegistro = $_GET["inContrato"];
                } else {
                    $stNomCGM = "";
                    $stJs .= "alertaAviso('@A Matrícula ".$_GET['inContrato']." não está ativa ou não existe.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "f.inContrato.value = '';\n";
                }
                break;
            case "contrato_aposentado":
                $stFiltro  = " AND registro = ".$_GET["inContrato"];
                $stFiltro .= " AND EXISTS (SELECT 1 FROM pessoal.aposentadoria WHERE aposentadoria.cod_contrato = contrato.cod_contrato";
                $stFiltro .= "               AND NOT EXISTS (SELECT 1 FROM pessoal.aposentadoria_excluida WHERE aposentadoria.cod_contrato = aposentadoria_excluida.cod_contrato))";
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
                if ($rsCGM->getNumLinhas() == 1) {
                    $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". trim($rsCGM->getCampo('nom_cgm'));
                    $inRegistro = $_GET["inContrato"];
                } else {
                    $stNomCGM = "";
                    $stJs .= "alertaAviso('@A Matrícula ".$_GET['inContrato']." não está aposentada ou não existe.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "f.inContrato.value = '';\n";
                }
                break;
            case "contrato_rescisao":
                $stFiltro  = " AND registro = ".$_GET["inContrato"];

                $stFiltro  .= "
                    AND EXISTS (SELECT 1 FROM pessoal.contrato_servidor_caso_causa WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato ".$stSubFiltro."
                        UNION ALL
                        SELECT 1 FROM pessoal.contrato_pensionista_caso_causa WHERE contrato_pensionista_caso_causa.cod_contrato = contrato.cod_contrato ".$stSubFiltro." ) ";

                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);

                if ($rsCGM->getNumLinhas() == 1) {
                    $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". trim($rsCGM->getCampo('nom_cgm'));
                    $inRegistro = $_GET["inContrato"];
                } else {
                    $stNomCGM = "";
                    $stJs .= "alertaAviso('@A Matrícula ".$_GET['inContrato']." não está rescindida ou não existe.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "f.inContrato.value = '';\n";
                }
                break;
            case "contrato_todos":
                $stFiltro = " AND registro = ".$_GET["inContrato"];
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTPessoalContrato = new TPessoalContrato();
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
        if ($rsCGM->getNumLinhas() == 1) {
                    $stNomCGM = $rsCGM->getCampo('numcgm') ." - ". trim($rsCGM->getCampo('nom_cgm'));
                    $inRegistro = $_GET["inContrato"];
                } else {
                    $stNomCGM = "";
                    $stJs .= "alertaAviso('@A Matrícula ".$_GET['inContrato']." está rescindida ou não existe.','form','erro','".Sessao::getId()."');\n";
                    $stJs .= "f.inContrato.value = '';\n";
                }
                break;
        }
    }
    $stJs = isset($stJs) ? $stJs : "";
    $stJs .= "f.inContrato.value = '".$inRegistro."';\n";
    $stJs .= "d.getElementById('inNomCGM').innerHTML = '".addslashes($stNomCGM)."';       \n";
    $stJs .= "f.hdnCGM.value = '".addslashes($stNomCGM)."';                               \n";

    return $stJs;
}

//#################
//INÍCIO ESTAGIÁRIO
//#################

function montaSpanCGMCodigoEstagio(&$stJs)
{
    $obSpnEstagios = new Span;
    $obSpnEstagios->setid( "spnEstagios");

    $stName = "Estagio";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inCodigoEstagio='+document.frm.inCodigoEstagio.value,'incluir$stName' ); limpaFormulario$stName();}" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    include_once ( CAM_GRH_EST_COMPONENTES."IPopUpCodigoEstagio.class.php");
    $obIPopUpCodigoEstagio = new IPopUpCodigoEstagio();

    $obFormulario = new Formulario;
    $obIPopUpCodigoEstagio->geraFormulario($obFormulario);

    $obFormulario->Incluir($stName,array($obIPopUpCodigoEstagio->obTxtCodigoEstagio,
                                                                $obIPopUpCodigoEstagio->obLblCGM),true);

    $obFormulario->addSpan($obSpnEstagios);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas("cgm_codigo_estagio");

    $obFormulario = new Formulario;
    $obIPopUpCodigoEstagio->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnEstagios);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function incluirEstagio()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arEstagios = ( is_array(Sessao::read('arEstagios')) ) ? Sessao::read('arEstagios') : array();
        foreach ($arEstagios as $arEstagio) {
            if ($arEstagio['inCodigoEstagio'] == $_GET['inCodigoEstagio']) {
                $obErro->setDescricao("Estágio já inserido na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
        $obTEstagioEstagiarioEstagio = new TEstagioEstagiarioEstagio;
        $stFiltro = " AND numero_estagio = '".$_GET['inCodigoEstagio']."'";
        $obTEstagioEstagiarioEstagio->recuperaCgmDoCodigoEstagio($rsCGM,$stFiltro);
        $arEstagio                             = array();
        $arEstagios = Sessao::read("arEstagios");
        $arEstagio['inId']                     = count($arEstagios);
        $arEstagio['inCodigoEstagio']          = $_GET['inCodigoEstagio'];
        $arEstagio['cod_estagio']              = $rsCGM->getCampo("cod_estagio");
        $arEstagio['numcgm']                   = $rsCGM->getCampo("cgm_estagiario");
        $arEstagio['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arEstagios[]        = $arEstagio;
        Sessao::write("arEstagios",$arEstagios);
        $stJs .= montaListaEstagios(Sessao::read('arEstagios'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirEstagio()
{
    $arEstagios = ( is_array(Sessao::read('arEstagios')) ) ? Sessao::read('arEstagios') : array();
    $arTemp = array();
    foreach ($arEstagios as $arEstagio) {
        if ($arEstagio['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arEstagio['inId'] = $inId;
            $arTemp[] = $arEstagio;
        }
    }
    Sessao::write("arEstagios",$arTemp);
    $stJs .= montaListaEstagios(Sessao::read('arEstagios'));

    return $stJs;
}

function montaListaEstagios($arEstagios)
{
    $rsEstagios = new Recordset;
    $rsEstagios->preenche($arEstagios);

    $obLista = new Lista;
    $obLista->setTitulo("Estágios");
    $obLista->setRecordSet( $rsEstagios );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Estágio");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "[inCodigoEstagio]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."','excluirEstagio');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnEstagios').innerHTML = '".$stHtml."';   \n";
    if ($rsEstagios->getNumLinhas() > 0) {
        $stJs .= "f.inValidaMatriculas.value = '1';";
    } else {
        $stJs .= "f.inValidaMatriculas.value = '0';";
    }

    return $stJs;
}

function montaSpanAtributoEstagio(&$stEval,$boGrupo=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroAtributoDinamico.class.php' );
    $obIFiltroAtributoDinamico = new IFiltroAtributoDinamico();
    $obIFiltroAtributoDinamico->setEstagiario();
    $obIFiltroAtributoDinamico->obCmbAtributo->obEvento->setOnChange( "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&inCodAtributo='+document.frm.inCodAtributo.value,'gerarSpanAtributosDinamicosEstagio' );");

    $obHdnEvalAtributosDinamicos = new HiddenEval();
    $obHdnEvalAtributosDinamicos->setName("hdnEvalAtributosDinamicos");
    $obHdnEvalAtributosDinamicos->setValue("");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Atributo Dinâmico");
    $obIFiltroAtributoDinamico->geraFormulario($obFormulario);
    $obFormulario->addHidden($obHdnEvalAtributosDinamicos);
    if ($boGrupo) {
        addComponenteAgrupamento($obFormulario);
    }
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n", "", $stEval);

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicosEstagio()
{
    if ($_REQUEST['inCodAtributo'] != "") {
        include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
        $obRCadastroDinamico = new RCadastroDinamico();
        $obRCadastroDinamico->setCodCadastro(1);
        $obRCadastroDinamico->obRModulo->setCodModulo(39);
        $obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$_GET['inCodAtributo']) );
        $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $Js .= $obFormulario->getInnerJavaScript();
    }

    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroAtributoDinamico.class.php' );
    $obIFiltroAtributoDinamico = new IFiltroAtributoDinamico();

    $stJs .= "d.getElementById('".$obIFiltroAtributoDinamico->obSpnAtributo->getId()."').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnEvalAtributosDinamicos.value = f.hdnEvalAtributosDinamicos.value + '$Js';\n";

    return $stJs;
}

function montaSpanInstituicaoEnsino(&$stEval)
{
    $obForm = Sessao::read("obForm");

    include_once(CAM_GRH_EST_COMPONENTES."IPopUpInstituicaoEntidade.class.php");
    $obIPopUpInstituicaoEntidade = new IPopUpInstituicaoEntidade();
    $obIPopUpInstituicaoEntidade->setDadosExtra(false);
    $obIPopUpInstituicaoEntidade->setFiltro(true);

    $obHdnEvalInstituicaoEnsino = new HiddenEval();
    $obHdnEvalInstituicaoEnsino->setName("hdnEvalInstituicaoEnsino");
    $obHdnEvalInstituicaoEnsino->setValue("");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Instituição de Ensino");
    $obIPopUpInstituicaoEntidade->geraFormulario($obFormulario,$obForm);
    $obFormulario->addHidden($obHdnEvalInstituicaoEnsino);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n", "", $stEval);

    return $obFormulario->getHTML();
}

function montaSpanEntidadeIntermediadora(&$stEval)
{
    $obForm = sessao::read("obForm");

    include_once(CAM_GRH_EST_COMPONENTES."IPopUpInstituicaoEntidade.class.php");
    $obIPopUpInstituicaoEntidade = new IPopUpInstituicaoEntidade(false);
    $obIPopUpInstituicaoEntidade->setDadosExtra(false);
    $obIPopUpInstituicaoEntidade->setFiltro(true);

    $obHdnEvalEntidadeIntermediadora = new HiddenEval();
    $obHdnEvalEntidadeIntermediadora->setName("hdnEvalEntidadeIntermediadora");
    $obHdnEvalEntidadeIntermediadora->setValue("");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Entidade Intermediadora");
    $obIPopUpInstituicaoEntidade->geraFormulario($obFormulario,$obForm);
    $obFormulario->addHidden($obHdnEvalEntidadeIntermediadora);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n", "", $stEval);

    return $obFormulario->getHTML();
}

//#################
//FIM ESTAGIÁRIO
//#################

//echo "window.alert('".$_GET["stCtrl"]."');";
switch ($_GET["stCtrl"]) {
    case "gerarSpan":
        $stJs = gerarSpan();
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
    case "incluirEvento":
        $stJs = incluirEvento();
        break;
    case "excluirEvento":
        $stJs = excluirEvento();
        break;
    case "incluirEstagio":
        $stJs .= incluirEstagio();
        break;
    case "excluirEstagio":
        $stJs .= excluirEstagio();
        break;
    case "limpaFormularioEvento":
        $stJs = limpaFormularioEvento();
        break;
    case "gerarSpanAtributosDinamicos":
        $stJs = gerarSpanAtributosDinamicos();
        break;
    case "gerarSpanAtributosDinamicosEstagio":
        $stJs = gerarSpanAtributosDinamicosEstagio();
        break;
    case "preencheCGMContrato":
        $stJs = preencheCGMContrato();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
