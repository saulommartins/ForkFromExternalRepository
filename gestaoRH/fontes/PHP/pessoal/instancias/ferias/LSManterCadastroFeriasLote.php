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
    * Página de Lista do Férias
    * Data de Criação: 08/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id:$

    * Casos de uso: uc-04.04.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'alterar');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCadastroFerias";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);
include_once($pgOcul);

sistemalegado::BloqueiaFrames();
flush();

$jsOnload = "executaFuncaoAjax('processarListaLote');";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stCaminho = CAM_GRH_PES_INSTANCIAS."ferias/";

$arTemp = array();
foreach ($request->getAll() as $key => $valor) {
    if (trim($key) != "hdnTipoFiltro" and trim($key) != "hdnCompetencia") {
        $arTemp[$key] = $valor;
    }
}

$request = new Request($arTemp);

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read("link");
if ($request->get("pg") and  $request->get("pos")) {
    $stLink.= "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
    Sessao::write("link",$link);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $request = new Request($link);
} else {
    foreach ($request->getAll() as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
if ($request->get("inAno") != "" and $request->get("inCodMes") != "") {
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$request->get("inAno"));
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$request->get("inCodMes"));
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
    if ($rsPeriodoMovimentacao->getNumLinhas() == -1) {
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    }
} else {
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
}

// Necessário carregar da sessão e testar se existe no request, pois quando vai Conceder Férias não usa o request
if ($request->get("stCodigos") == "") {
    $arContratos = Sessao::read("arContratos");
    $stFiltroAux = "";
    foreach ($arContratos as $campo) {
        if ($campo['cod_contrato'] != "") {
            $stFiltroAux .= $campo['cod_contrato'].",";
        }
    }
}

if ($stFiltroAux != "") {
    $stValoresFiltro = substr($stFiltroAux,0,-1);
} else {
    $stValoresFiltro = $request->get("stCodigos");
}

include_once CAM_GRH_PES_MAPEAMENTO."TPessoalFerias.class.php";
$obTPessoalFerias = new TPessoalFerias();
$obTPessoalFerias->setDado("stAcao"                     ,$request->get("stAcao"));
$obTPessoalFerias->setDado("stTipoFiltro"               ,$request->get("stTipoFiltro"));
$obTPessoalFerias->setDado("stValoresFiltro"            ,$stValoresFiltro);
$obTPessoalFerias->setDado("inCodPeriodoMovimentacao"   ,$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$obTPessoalFerias->setDado("boFeriasVencidas"           ,(trim($request->get('boApresentarSomenteFerias')) != "") ? 'true' : 'false');
$obTPessoalFerias->setDado("inCodLote"                  ,(trim($request->get("inCodLote")) != "") ? $request->get("inCodLote") : 0);
$obTPessoalFerias->setDado("inCodRegime"                ,$request->get("inCodRegime"));
$obTPessoalFerias->setDado("inCodFormaPagamento"        ,$request->get("inCodFormaPagamento"));
$obTPessoalFerias->setDado("boLote"                     ,true);
$obTPessoalFerias->concederFerias($rsLista,$stFiltro," ORDER BY concederFerias.nom_cgm, concederFerias.dt_inicial_aquisitivo, concederFerias.dt_final_aquisitivo");

include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGeradoContratoServidor.class.php";
$obTPessoalAssentamentoGeradoContratoServidor = new TPessoalAssentamentoGeradoContratoServidor();
while (!$rsLista->eof()) {
    $stFiltro  = " AND assentamento_gerado_contrato_servidor.cod_contrato = ".$rsLista->getCampo("cod_contrato")." \n";
    $stFiltro .= " AND assentamento_assentamento.cod_motivo = 10 \n";
    $stFiltro .= " AND (assentamento_gerado.periodo_inicial BETWEEN to_date('".$rsLista->getCampo("dt_inicial_aquisitivo_formatado")."','dd/mm/yyyy') AND to_date('".$rsLista->getCampo("dt_final_aquisitivo_formatado")."','dd/mm/yyyy') \n";
    $stFiltro .= "  OR  assentamento_gerado.periodo_final BETWEEN to_date('".$rsLista->getCampo("dt_inicial_aquisitivo_formatado")."','dd/mm/yyyy') AND to_date('".$rsLista->getCampo("dt_final_aquisitivo_formatado")."','dd/mm/yyyy')) \n";
    $obTPessoalAssentamentoGeradoContratoServidor->recuperaRelacionamento($rsAssentamentoGerado,$stFiltro);

    $inQuantFaltas = 0;
    while (!$rsAssentamentoGerado->eof()) {
        $inQuantFaltas += $rsAssentamentoGerado->getCampo("dias_do_periodo");
        $rsAssentamentoGerado->proximo();
    }

    $arDiasFeriasAbono = gerarQuantDiasGozoAbono($inQuantFaltas,$rsLista->getCampo("dt_inicial_aquisitivo_formatado"),$rsLista->getCampo("dt_final_aquisitivo_formatado"),$request->get("inCodFormaPagamento"));
    $rsLista->setCampo("dias_ferias",$arDiasFeriasAbono[0]);
    $rsLista->setCampo("dias_abono",$arDiasFeriasAbono[1]);

    switch ($request->get("inCodFormaPagamento")) {
        case '1':
        case '2':
            $rsLista->setCampo("saldo_dias",0);
        break;
        case '3':
            $rsLista->setCampo("saldo_dias",20-$rsLista->getCampo("ferias_tiradas"));
        break;
        case '4':
            $rsLista->setCampo("saldo_dias",15-$rsLista->getCampo("ferias_tiradas"));
        break;
    }

    $rsLista->setCampo("dias_faltas",$inQuantFaltas);
    $rsLista->proximo();
}
$rsLista->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setMostraPaginacao(false);
$obLista->setMostraSelecionaTodos(true);
$stTitulo = '</div></td></tr><tr><td colspan="10" class="alt_dados">Matrículas';
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Admissão" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Função" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Período Aquisitivo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Dias" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Abono" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saldo" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_admissao_formatado" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "desc_funcao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[dt_inicial_aquisitivo_formatado] a [dt_final_aquisitivo_formatado]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dias_ferias" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dias_abono" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "saldo_dias" );
$obLista->commitDado();

$obChkLote = new CheckBox;
$obChkLote->setName( "boLote_[cod_contrato]"  );
switch ($request->get('stTipoFiltro')) {
    case "contrato":
    case "cgm_contrato":
    case "contrato_todos":
    case "cgm_contrato_todos":
    case "geral":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]_[dias_faltas]_[dt_inicial_aquisitivo_formatado]_[dt_final_aquisitivo_formatado]");
        break;
    case "funcao":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]_[dias_faltas]_[dt_inicial_aquisitivo_formatado]_[dt_final_aquisitivo_formatado]_[cod_funcao]");
        break;
    case "lotacao":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]_[dias_faltas]_[dt_inicial_aquisitivo_formatado]_[dt_final_aquisitivo_formatado]_[cod_orgao]");
        break;
    case "local":
        $obChkLote->setValue("[cod_contrato]_[dias_ferias]_[dias_abono]_[dias_faltas]_[dt_inicial_aquisitivo_formatado]_[dt_final_aquisitivo_formatado]_[cod_local]");
        break;
}

$obChkLote->setChecked(true);

$obLista->addDadoComponente( $obChkLote );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('[cod_contrato]');
$obLista->commitDadoComponente();

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue($stCtrl);

$obHdnRegime =  new Hidden;
$obHdnRegime->setName("inCodRegime");
$obHdnRegime->setValue($request->get("inCodRegime"));

$obHdnTipoFiltro =  new Hidden;
$obHdnTipoFiltro->setName("stTipoFiltro");
$obHdnTipoFiltro->setValue($request->get("stTipoFiltro"));

$obHdnTipoFiltroLote =  new Hidden;
$obHdnTipoFiltroLote->setName("stTipoFiltroLote");
$obHdnTipoFiltroLote->setValue($request->get("stTipoFiltroLote"));

$obHdnCodigos =  new Hidden;
$obHdnCodigos->setName("stCodigos");
$obHdnCodigos->setValue($request->get("stCodigos"));

$obHdnFormaPagamento =  new Hidden;
$obHdnFormaPagamento->setName("inCodFormaPagamento");
$obHdnFormaPagamento->setValue($request->get("inCodFormaPagamento"));

$obHdnInicialFerias =  new Hidden;
$obHdnInicialFerias->setName("dtInicialFerias");
$obHdnInicialFerias->setValue($request->get("dtInicialFerias"));

$obHdnFinalFerias =  new Hidden;
$obHdnFinalFerias->setName("dtFinalFerias");
$obHdnFinalFerias->setValue($request->get("dtFinalFerias"));

$obHdnRetornoFerias =  new Hidden;
$obHdnRetornoFerias->setName("dtRetornoFerias");
$obHdnRetornoFerias->setValue($request->get("dtRetornoFerias"));

$obHdnMes =  new Hidden;
$obHdnMes->setName("inCodMes");
$obHdnMes->setValue($request->get("inCodMes"));

$obHdnAno =  new Hidden;
$obHdnAno->setName("inAno");
$obHdnAno->setValue($request->get("inAno"));

$obHdnPagamento13 =  new Hidden;
$obHdnPagamento13->setName("boPagamento13");
$obHdnPagamento13->setValue($request->get("boPagamento13"));

$obHdnTipo =  new Hidden;
$obHdnTipo->setName("inCodTipo");
$obHdnTipo->setValue($request->get("inCodTipo"));

$obHdnLote =  new Hidden;
$obHdnLote->setName("stNomeLote");
$obHdnLote->setValue($request->get("stNomeLote"));

$obOk = new Ok();

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnRegime);
$obFormulario->addHidden($obHdnTipoFiltro);
$obFormulario->addHidden($obHdnTipoFiltroLote);
$obFormulario->addHidden($obHdnCodigos);
$obFormulario->addHidden($obHdnFormaPagamento);
$obFormulario->addHidden($obHdnInicialFerias);
$obFormulario->addHidden($obHdnFinalFerias);
$obFormulario->addHidden($obHdnRetornoFerias);
$obFormulario->addHidden($obHdnMes);
$obFormulario->addHidden($obHdnAno);
$obFormulario->addHidden($obHdnPagamento13);
$obFormulario->addHidden($obHdnTipo);
$obFormulario->addHidden($obHdnLote);
$obFormulario->addLista($obLista);
$obFormulario->defineBarra(array($obOk));
$obFormulario->show();

sistemalegado::LiberaFrames();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
