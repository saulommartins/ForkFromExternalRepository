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

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @ignore

    $Id: OCIFiltroComponentesDependentes.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpan()
{
    $boAtualizarLotacao = false;

    Sessao::remove('arContratos');
    Sessao::remove('arCGMDependentes');
    switch ($_GET["stTipoFiltro"]) {
        case "cgm_dependente":
            $stHtml = montaSpanDependente($stJs, $_REQUEST['boFiltrarPensaoJudicial']);
            break;
        case "cgm_servidor_dependente":
            $stHtml = montaSpanServidorDependente($stJs, $_REQUEST['boFiltrarPensaoJudicial']);
            break;
        case "matricula_dependente_servidor":
            $stHtml = montaSpanMatriculaDependenteDeServidor($stJs, $_REQUEST['boFiltrarPensaoJudicial']);
            break;
        case "lotacao":
            $boAtualizarLotacao = true;
            $stHtml = montaSpanLotacao($stEval);
            break;
        case "local":
            $stHtml = montaSpanLocal($stEval);
            break;
        case "lotacao_grupo":
            $boAtualizarLotacao = true;
            $stHtml = montaSpanLotacao($stEval,true);
            break;
        case "local_grupo":
            $stHtml = montaSpanLocal($stEval,true);
            break;
        case "geral":
            $stHtml = "";
            $stEval = "";
            break;
    }
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
    include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");

    $stJs 					 = "";
    $arFiltroCompetencia 	 = Sessao::read("arFiltroCompetencia");
    $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");

    if (is_array($arFiltroCompetencia) && !empty($arFiltroCompetencia)) {
        foreach ($arFiltroCompetencia as $obFiltroCompetencia) {
            if (trim($obFiltroCompetencia->getCodigoPeriodoMovimentacao()) != "") {
                $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
                $obFPessoalOrganogramaVigentePorTimestamp->setDado("cod_periodo_movimentacao",$obFiltroCompetencia->getCodigoPeriodoMovimentacao());
                $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

                $inCodOrganograma = $rsOrganogramaVigente->getCampo("cod_organograma");
                $stDataFinal      = $rsOrganogramaVigente->getCampo("dt_final");

                if (is_array($arSelectMultiploLotacao) && !empty($arSelectMultiploLotacao)) {
                    foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                        $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
                    }
                }
            }
        }
    }

    return $stJs;
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

    $obISelectMultiploLotacao = new ISelectMultiploLotacao;
    $obISelectMultiploLotacao->setNull(false);

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

function montaSpanMatriculaDependenteDeServidor(&$stJs, $boFiltrarPensaoJudicial = false)
{
    $obLblCGM = new Label();
    $obLblCGM->setRotulo("CGM");
    $obLblCGM->setId("stCGMServidor");
    $obLblCGM->setName("stCGMServidor");

    include_once ( CAM_GRH_PES_COMPONENTES.'IContratoDigitoVerificador.class.php'   );
    $obIContratoDigitoVerificador = new IContratoDigitoVerificador();
    $obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&inContrato='+this.value, 'preencherDependentesDoServidor' );");
    $obIContratoDigitoVerificador->setFuncaoBuscaFiltro ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarMatriculaPensaoJudicial.php','frm','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."','".$obIContratoDigitoVerificador->obTxtRegistroContrato->getId()."','','".Sessao::getId()."','800','550')" );
    $obIContratoDigitoVerificador->setPagFiltro(true);

    $obCmbDependente = new Select;
    $obCmbDependente->setRotulo                   ( "Dependente"                              );
    $obCmbDependente->setTitle                    ( "Selecione o dependente."                 );
    $obCmbDependente->setName                     ( "inCodDependente"                         );
    $obCmbDependente->setId                       ( "inCodDependente"                         );
    $obCmbDependente->setValue                    ( ""                                        );
    $obCmbDependente->setStyle                    ( "width: 200px"                            );
    $obCmbDependente->addOption                   ( "", "Selecione"                           );
    $obCmbDependente->setNullBarra(false);

    $stName = "Dependente";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&inCodDependente='+document.frm.inCodDependente.value,'incluir$stName' ); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obSpnDependentes = new Span();
    $obSpnDependentes->setId("spnDependentes");

    $obFormulario = new Formulario;
    $obFormulario->Incluir($stName,array($obLblCGM,$obIContratoDigitoVerificador->obTxtRegistroContrato,$obCmbDependente),true);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs  = $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas($_GET["stTipoFiltro"]);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por Matrículas com Dependente");
    $obFormulario->addComponente($obLblCGM);
    $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    $obFormulario->addComponente($obCmbDependente);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnDependentes);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function preencherDependentesDoServidor()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php");
    $obTPessoalServidorDependente = new TPessoalServidorDependente();
    $stFiltro = " AND contrato.registro = ".$_GET["inContrato"];
    $obTPessoalServidorDependente->recuperaDependentesDeServidor($rsDependentes,$stFiltro," ORDER BY nom_cgm");
    $stJs .= "limpaSelect($('inCodDependente'),0);\n";
    $stJs .= "$('inCodDependente').options[0] = new Option('Selecione','', 'selected');\n";
    if ($rsDependentes->getNumLinhas() > 0) {
        $stCGMServidor = $rsDependentes->getCampo("numcgm_servidor")."-".$rsDependentes->getCampo("nom_cgm_servidor");
        $inRegistro = $rsDependentes->getCampo("registro");
        $inIndex = 1;
        while (!$rsDependentes->eof()) {
            $stJs .= "$('inCodDependente').options[".$inIndex."] = new Option('".$rsDependentes->getCampo("numcgm")."-".$rsDependentes->getCampo("nom_cgm")."','".$rsDependentes->getCampo("cod_dependente")."', '');\n";
            $inIndex++;
            $rsDependentes->proximo();
        }
    } else {
        $stCGMServidor = "";
        $inRegistro = "";
    }
    $stJs .= "$('stCGMServidor').innerHTML = '".$stCGMServidor."';\n";
    $stJs .= "$('inContrato').value = '".$inRegistro."';\n";

    return $stJs;
}

function incluirDependente()
{
    $arDependentes = Sessao::read("arDependentes");
    $obErro = new Erro;
    if (is_array($arDependentes)) {
        foreach ($arDependentes as $arDependente) {
            if ($arDependente["cod_dependente"] == $_GET["inCodDependente"]) {
                $obErro->setDescricao("O dependente ".$arDependente["numcgm"]."-".$arDependente["nom_cgm"]." já foi inserido na lista.");
            }
        }
    }

    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php");
        $obTPessoalServidorDependente = new TPessoalServidorDependente();
        $stFiltro = " AND dependente.cod_dependente = ".$_GET["inCodDependente"];
        $obTPessoalServidorDependente->recuperaDependentesDeServidor($rsDependente,$stFiltro," ORDER BY nom_cgm");
        $arDependente = $rsDependente->getElementos();
        $arDependentes[] = $arDependente[0];
        Sessao::write("arDependentes",$arDependentes);
        $stJs = montaListaDependentes();
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirDependente()
{
    $arDependentes = Sessao::read("arDependentes");
    $arDependentesTemp = array();
    if (is_array($arDependentes)) {
        foreach ($arDependentes as $arDependente) {
            if ($arDependente["cod_dependente"] != $_GET["cod_dependente"]) {
                $arDependentesTemp[] = $arDependente;
            }
        }
    }
    Sessao::write("arDependentes",$arDependentesTemp);
    $stJs = montaListaDependentes();

    return $stJs;
}

function montaListaDependentes()
{
    include_once(CAM_FW_COMPONENTES."Table/Table.class.php");

    $rsDependentes = new Recordset();
    $rsDependentes->preenche(Sessao::read("arDependentes"));

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Dependentes");
    $obLista->setRecordSet( $rsDependentes );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome");
    $obLista->ultimoCabecalho->setWidth( 65 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "numcgm" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."','excluirDependente');");
    $obLista->ultimaAcao->addCampo("1","cod_dependente");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "$('spnDependentes').innerHTML = '".$stHtml."';\n";
    if ($rsDependentes->getNumLinhas() > 0) {
        $stJs .= "f.inValidaMatriculas.value = '1';";
    } else {
        $stJs .= "f.inValidaMatriculas.value = '0';";
    }

    return $stJs;
}

function montaValidaMatriculas($stTipoFiltro)
{
    switch ($stTipoFiltro) {
        case "matricula_dependente_servidor":
        case "cgm_dependente":
            $stMensagem = "Deve haver pelo menos um dependente na lista de dependentes";
            break;
    }

    $stHdnValidaMatriculas .= "if (document.frm.stTipoFiltro.value == \"$stTipoFiltro\") {";
    $stHdnValidaMatriculas .= "  if (document.frm.inValidaMatriculas.value == \"0\") {";
    $stHdnValidaMatriculas .= "     erro = true; ";
    $stHdnValidaMatriculas .= "     mensagem += \"@".$stMensagem."!()\"; ";
    $stHdnValidaMatriculas .= "  }";
    $stHdnValidaMatriculas .= "}";

    $stJs = "f.hdnValidaMatriculas.value = '$stHdnValidaMatriculas';\n";

    return $stJs;
}

function montaSpanDependente(&$stJs, $boFiltrarPensaoJudicial = false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IBuscaInnerCGMDependente.class.php'   );

    $obSpnContratosDependentes = new Span;
    $obSpnContratosDependentes->setid("spnContratoDependente");

    $stName = "ContratoDependente";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&inCGMDependente='+document.frm.inCGMDependente.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obIBuscaInnerCGMDependente = new IBuscaInnerCGMDependente($boFiltrarPensaoJudicial);
    $obIBuscaInnerCGMDependente->obBscDependente->obCampoCod->setNullBarra(true);

    $obFormulario = new Formulario;
    $obIBuscaInnerCGMDependente->geraFormulario($obFormulario);
    $obFormulario->Incluir($stName,array($obIBuscaInnerCGMDependente->obBscDependente->obCampoCod,
                                         $obIBuscaInnerCGMDependente->obBscDependente),true);
    $obFormulario->addSpan($obSpnContratosDependentes);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs  = $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas($_GET["stTipoFiltro"]);

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Filtro por CGM de Dependente");
    $obIBuscaInnerCGMDependente->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratosDependentes);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function incluirContratoDependente()
{
    $obErro    = new erro;

    if ( !$obErro->ocorreu() ) {
        $arCGMDependentes = ( is_array(Sessao::read('arCGMDependentes')) ) ? Sessao::read('arCGMDependentes') : array();
        foreach ($arCGMDependentes as $arCGMDependente) {
            if ($arCGMDependente['numcgm'] == $_GET['inCGMDependente']) {
                $obErro->setDescricao("CGM de dependente já inserido na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
        $obTCGM = new TCGM;
        $stFiltro = " AND CGM.numcgm = '".addslashes($_GET['inCGMDependente'])."'";
        $obTCGM->recuperaRelacionamentoSintetico($rsCGM,$stFiltro);
    }
    if ( !$obErro->ocorreu() ) {
        $arCGMDependentes = Sessao::read("arCGMDependentes");
        $arCGMDependente                             = array();
        $arCGMDependente['inId']                     = count($arCGMDependentes);
        $arCGMDependente['numcgm']                   = $_GET['inCGMDependente'];
        $arCGMDependente['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arCGMDependentes[]        = $arCGMDependente;
        Sessao::write("arCGMDependentes",$arCGMDependentes);
        $stJs .= montaListaContratosDependente(Sessao::read('arCGMDependentes'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContratoDependente()
{
    $arCGMDependentes = ( is_array(Sessao::read('arCGMDependentes')) ? Sessao::read('arCGMDependentes') : array());
    $arTemp = array();
    foreach ($arCGMDependentes as $arCGMDependente) {
        if ($arCGMDependente['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arTemp[] = $arCGMDependente;
        }
    }
    Sessao::write('arCGMDependentes',$arTemp);
    $stJs .= montaListaContratosDependente(Sessao::read('arCGMDependentes'));

    return $stJs;
}

function montaListaContratosDependente($arCGMDependentes)
{
    $rsCGM = new Recordset;
    $rsCGM->preenche($arCGMDependentes);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de CGM's de Dependentes");
    $obLista->setRecordSet( $rsCGM );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
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
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."','excluirContratoDependente');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratoDependente').innerHTML = '".$stHtml."';   \n";
    if ($rsCGM->getNumLinhas() > 0) {
        $stJs .= "f.inValidaMatriculas.value = '1';";
    } else {
        $stJs .= "f.inValidaMatriculas.value = '0';";
    }

    return $stJs;
}

############################## SERVIDOR - DEPENDENTE ##############################

function montaSpanServidorDependente(&$stJs, $boFiltrarPensaoJudicial = false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMServidorDependente.class.php'   );

    $obSpnContratosServidorDependente = new Span;
    $obSpnContratosServidorDependente->setid( "spnContratoServidorDependente");

    $stName = "ContratoServidorDependente";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );
    $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."&inContrato='+document.frm.inContrato.value,'incluir$stName' ); limpaFormulario$stName(); }" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar$stName"          );
    $obBtnLimpar->setValue             ( "Limpar"                   );
    $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
    $arBarra[] = $obBtnLimpar;

    $obIFiltroCGMServidorDependente = new IFiltroCGMServidorDependente($boFiltrarPensaoJudicial);
    $obIFiltroCGMServidorDependente->obBscDependente->obBscServidorDependente->obCampoCod->setNullBarra(true);

    $obFormulario = new Formulario;
    $obIFiltroCGMServidorDependente->geraFormulario($obFormulario);
    $obFormulario->Incluir($stName,array($obIFiltroCGMServidorDependente->obBscDependente->obBscServidorDependente->obCampoCod,
                                         $obIFiltroCGMServidorDependente->obBscDependente->obBscServidorDependente,
                                         $obIFiltroCGMServidorDependente->obCmbContrato
                                        ),true);
    $obFormulario->addSpan($obSpnContratosServidorDependente);
    $obFormulario->obJavaScript->montaJavaScript();
    $stJs  = $obFormulario->getInnerJavascriptBarra();
    $stJs .= montaValidaMatriculas("cgm_servidor_dependente");

    $obFormulario = new Formulario;
    $obIFiltroCGMServidorDependente->geraFormulario($obFormulario);
    $obFormulario->defineBarra($arBarra);
    $obFormulario->addSpan($obSpnContratosServidorDependente);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function incluirContratoServidorDependente()
{
    $obErro    = new erro;
    if ( !$obErro->ocorreu() ) {
        $arContratos = ( is_array(Sessao::read('arContratos')) ) ? Sessao::read('arContratos') : array();
        foreach ($arContratos as $arContrato) {
            if ($arContrato['inContrato'] == $_GET['inContrato']) {
                $obErro->setDescricao("Matrícula já inserida na lista.");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " AND registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);
    }
    if ( !$obErro->ocorreu() ) {
        $arContratos = Sessao::read('arContratos');
        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arContratos[]        = $arContrato;
        Sessao::write('arContratos',$arContratos);
        $stJs .= montaListaContratosServidorDependente(Sessao::read('arContratos'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirContratoServidorDependente()
{
    $arContratos = ( is_array(Sessao::read('arContratos')) ? Sessao::read('arContratos') : array());
    $arTemp = array();
    foreach ($arContratos as $arContrato) {
        if ($arContrato['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arTemp[] = $arContrato;
        }
    }
    Sessao::write('arContratos',$arTemp);
    $stJs .= montaListaContratosServidorDependente(Sessao::read('arContratos'));

    return $stJs;
}

function montaListaContratosServidorDependente($arContratos)
{
    $rsContratos = new Recordset;
    $rsContratos->preenche($arContratos);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Matrículas de Servidores");
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
    $obLista->ultimaAcao->setLink( "JavaScript:ajaxJavaScriptSincronoRH('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentesDependentes.php?".Sessao::getId()."','excluirContratoServidorDependente');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnContratoServidorDependente').innerHTML = '".$stHtml."';   \n";
    if ($rsContratos->getNumLinhas() > 0) {
        $stJs .= "f.inValidaMatriculas.value = '1';";
    } else {
        $stJs .= "f.inValidaMatriculas.value = '0';";
    }

    return $stJs;
}

//echo "window.alert('".$_GET["stCtrl"]."');";
switch ($_GET["stCtrl"]) {
    case "gerarSpan":
        $stJs = gerarSpan();
        break;
    case "incluirContratoDependente":
        $stJs = incluirContratoDependente();
        break;
    case "excluirContratoDependente":
        $stJs = excluirContratoDependente();
        break;
    case "incluirContratoServidorDependente":
        $stJs = incluirContratoServidorDependente();
        break;
    case "excluirContratoServidorDependente":
        $stJs = excluirContratoServidorDependente();
        break;
    case "preencherDependentesDoServidor":
        $stJs = preencherDependentesDoServidor();
        break;
    case "incluirDependente":
        $stJs = incluirDependente();
        break;
    case "excluirDependente":
        $stJs = excluirDependente();
        break;
}
if ($stJs != "") {
    echo $stJs;
}
?>
