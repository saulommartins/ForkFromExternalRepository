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
    * Página de Oculto do Registro de Evento de Rescisão
    * Data de Criação: 16/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: melo $
    $Date: 2007-07-24 14:47:15 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                            );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectRegSubCarEsp.class.php"                                   );
include_once( CAM_GRH_PES_COMPONENTES."ISelectPadrao.class.php"                                         );
include_once( CAM_GRH_PES_COMPONENTES."IBuscaInnerLotacao.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IBuscaInnerLocal.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoRescisao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpan($stTipoFiltro="")
{
    $stTipoFiltro = ( $_GET['stTipoFiltro'] != "" ) ? $_GET['stTipoFiltro'] : $stTipoFiltro;
    switch ($stTipoFiltro) {
        case "contrato":
            $stHtml .= gerarSpan1($stEval);
        break;
        case "cgm_contrato":
            $stHtml .= gerarSpan2($stEval);
        break;
        case "cargo":
            $stHtml .= gerarSpan3($stEval);
        break;
        case "funcao":
            $stHtml .= gerarSpan4($stEval);
        break;
        case "padrao":
            $stHtml .= gerarSpan5($stEval);
        break;
        case "lotacao":
            $stHtml .= gerarSpan6($stEval);
        break;
        case "local":
            $stHtml .= gerarSpan7($stEval);
        break;
    }
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '$stHtml';  \n";
    $stJs .= "f.stEval.value = '$stEval';                           \n";

    return $stJs;
}

function gerarSpan1(&$stEval)
{
    $obIContratoDigitoVerificador = new IContratoDigitoVerificador("",true);
    $obIContratoDigitoVerificador->setPagFiltro(true);
    $obIContratoDigitoVerificador->obTxtRegistroContrato->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Matrícula");
    $obIContratoDigitoVerificador->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan2(&$stEval)
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato(true);
    $obIFiltroCGMContrato->setTituloFormulario("CGM/Matrícula");
    $obIFiltroCGMContrato->obCmbContrato->setNull(false);
    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan3(&$stEval)
{
    $obISelectRegSubCarEsp = new ISelectRegSubCarEsp;
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Cargo");
    $obISelectRegSubCarEsp->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan4(&$stEval)
{
    $obISelectRegSubCarEsp = new ISelectRegSubCarEsp(true);
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Função");
    $obISelectRegSubCarEsp->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan5(&$stEval)
{
    $obISelectPadrao = new ISelectPadrao;
    $obISelectPadrao->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Padrão");
    $obISelectPadrao->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan6(&$stEval)
{
    $obIBuscaInnerLotacao = new IBuscaInnerLotacao;
    $obIBuscaInnerLotacao->obBscLotacao->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Lotação");
    $obIBuscaInnerLotacao->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function gerarSpan7(&$stEval)
{
    $obIBuscaInnerLocal = new IBuscaInnerLocal;
    $obIBuscaInnerLocal->obBscLocal->setNull(false);
    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Local");
    $obIBuscaInnerLocal->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function processarFiltro()
{
    $stJs .= gerarSpan("contrato");
    $stJs .= "document.frm.stTipoFiltro.value = 'contrato'; \n";

    return $stJs;
}

function processarForm()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
    $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao;
    $arIncluirAlterar = Sessao::read('arIncluirAlterar');
    $obFormulario = new Formulario;
    $obFormulario->incluirAlterar( "Evento",$arIncluirAlterar,true );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->getInnerJavaScript();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnBotoes').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnEvalBotoes.value = '$stEval';                  \n";
    $stJs .= $obFormulario->getInnerJavascriptBarra();
    $stFiltro  = " AND cod_contrato = ".$_GET["inCodContrato"];
    $stFiltro .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
    $stFiltro .= " AND evento.natureza != 'B'";
    $stOrdem  = " descricao";
    $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsEventos,$stFiltro,$stOrdem);
    $arEventos = (is_array($rsEventos->getElementos())) ? $rsEventos->getElementos() : array();
    $arEventosTemp = array();
    $inIndex2 = 0;
    foreach ($arEventos as $inIndex=>$arEvento) {
        if ($arEvento['evento_sistema'] == 'f') {
            $arTemp['inId']                         = $inIndex2;
            $arTemp['inCodigoEvento']               = $arEvento['codigo'];
            $arTemp['stDescricao']                  = $arEvento['descricao'];
            $arTemp['nuValorEvento']                = number_format($arEvento['valor'],2,",",".");
            $arTemp['nuQuantidadeEvento']           = number_format($arEvento['quantidade'],2,",",".");
            $arTemp['nuQuantidadeParcelasEvento']   = $arEvento["parcela"];
            $arTemp['inCodRegistro']                = $arEvento['cod_registro'];
            $arTemp['stDesdobramento']              = $arEvento['desdobramento'];
            $arEventosTemp[count($arEventosTemp)] = $arTemp;
            $inIndex2++;
        }
    }
    Sessao::write('arEventos',$arEventosTemp);
    $stJs .= montaListaEventos($arEventosTemp);

    return $stJs;
}

function limparFiltro()
{
    $stJs .= gerarSpan("contrato");
    $stJs .= "document.frm.stTipoFiltro.value = 'contrato';                     \n";

    return $stJs;
}

function montaListaEventos($arEventos)
{
    foreach ($arEventos as $inIndex=>$arEvento) {
        switch ($arEvento['stDesdobramento']) {
            case 'S':
                $arEvento['stDesdobramentoTexto'] = "Saldo Salário";
                break;
            case 'A':
                $arEvento['stDesdobramentoTexto'] = "Aviso Prévio Indenizado";
                break;
            case 'V':
                $arEvento['stDesdobramentoTexto'] = "Férias Vencidas";
                break;
            case 'P':
                $arEvento['stDesdobramentoTexto'] = "Férias Proporcionais";
                break;
            case 'D':
                $arEvento['stDesdobramentoTexto'] = "13º Salário";
                break;
        }
        $arEvento['nuQuantidadeEvento'] = ($arEvento['nuQuantidadeParcelasEvento'] != "" ? number_format($arEvento['nuQuantidadeEvento']) . "/" . $arEvento['nuQuantidadeParcelasEvento'] : $arEvento['nuQuantidadeEvento']);
        $arEvento['stCampoNomEvento'] = 'inCodigoEvento';
        $arEventos[$inIndex] = $arEvento;
    }

    $rsEventos = new Recordset;
    $rsEventos->preenche($arEventos);

    $obLista = new Lista;
    $obLista->setTitulo("Eventos Cadastrados");
    $obLista->setRecordSet( $rsEventos );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Desdobramento");
    $obLista->ultimoCabecalho->setWidth( 15 );
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
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[inCodigoEvento] - [stDescricao]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stDesdobramentoTexto" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "nuValorEvento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "nuQuantidadeEvento" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setLinkId("alterar");
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarEvento');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->ultimaAcao->addCampo("2","inCodigoEvento");
    $obLista->ultimaAcao->addCampo("3","stCampoNomEvento");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setLinkId("excluir");
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirEvento');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnEventosCadastrados').innerHTML = '".$stHtml."';   \n";
    $stJs .= montaListaEventosBase($arEventos);

    if ( Sessao::read('inMesCompetencia') == 12 ) {
        $boComplementacao = false;
        foreach ($arEventos as $inIndex=>$arEvento) {
            if ($arEvento['stDesdobramento'] == 'C') {
                $boComplementacao = true;
                break;
            }
        }
        if ($boComplementacao) {
            foreach ($arEventos as $inIndex=>$arEvento) {
                if ($arEvento['stDesdobramento'] == 'D') {
                    $stJs .= "d.getElementById('alterar_".($inIndex+1)."').href = 'javascript:executaFuncaoAjax(\'mostraMensagem\');';";
                    $stJs .= "d.getElementById('excluir_".($inIndex+1)."').href = 'javascript:executaFuncaoAjax(\'mostraMensagem\');';";
                }
            }
        }
    }

    return $stJs;
}

function agruparEventos($rsEventosBase)
{
    $arEventosInceridos = array();
    $arEventosBase      = array();
    while (!$rsEventosBase->eof()) {
        if ( !in_array($rsEventosBase->getCampo("codigo_base"),$arEventosInceridos) ) {
            $arTemp['codigo_base']    = $rsEventosBase->getCampo("codigo_base");
            $arTemp['descricao_base'] = $rsEventosBase->getCampo("descricao_base");
            $arEventosBase[]          = $arTemp;
        }
        $arEventosInceridos[] = $rsEventosBase->getCampo("codigo_base");
        $rsEventosBase->proximo();
    }
    $rsEventosBase = new Recordset;
    $rsEventosBase->preenche($arEventosBase);

    return $rsEventosBase;
}

function montaListaEventosBase($arEventos)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $rsEventosBase = new Recordset;
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
    foreach ($arEventos as $arEvento) {
        $stFiltro = " WHERE codigo = '".$arEvento['inCodigoEvento']."'";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);
        if ( $rsEvento->getNumLinhas() > 0 ) {
            $stCodEventos .= $rsEvento->getCampo('cod_evento').",";
        }
    }
    if ($stCodEventos != "") {
        $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);
        $stFiltro  = " AND evento_base.cod_evento IN ($stCodEventos)";
        $stFiltro .= " AND evento_base.cod_configuracao = 4";

        $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
        $obTFolhaPagamentoEventoBase->recuperaEventoBase($rsEventosBase,$stFiltro);
    }
    $rsEventosBase = agruparEventos($rsEventosBase);
    if ( $rsEventosBase->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setTitulo("Bases de Eventos");
        $obLista->setRecordSet( $rsEventosBase );
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
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "codigo_base" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "descricao_base" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);

        $stJs .= "d.getElementById('spnEventosBase').innerHTML = '".$stHtml."';   \n";
    } else {
        $stJs .= "d.getElementById('spnEventosBase').innerHTML = '';   \n";
    }

    return $stJs;
}

function incluirEvento()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
    $arEventos = ( is_array(Sessao::read('arEventos')) ? Sessao::read('arEventos') : array());
    $obErro    = new erro;
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $stFiltro  = " AND evento.codigo = '".$_GET['inCodigoEvento']."'";
    $stFiltro .= " AND sub_divisao.cod_sub_divisao = ".$_GET['inCodSubDivisao'];
    $stFiltro .= " AND cargo.cod_cargo = ".$_GET['inCodCargo'];
    $stFiltro .= ( $_GET['inCodEspecialidade'] ) ? " AND especialidade.cod_especialidade = ".$_GET['inCodEspecialidade'] : "";
    switch ($_GET['stDesdobramento']) {
        case "S":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 1";
            break;
        case "A":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 4";
            break;
        case "V":
        case "P":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 2";
            break;
        case "D":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 3";
            break;
    }
    $obTFolhaPagamentoRegistroEvento->recuperaRelacionamentoConfiguracao($rsConfiguracao,$stFiltro);
    if ( $rsConfiguracao->getNumLinhas() < 0 ) {
        $obErro->setDescricao("O evento informado não possui configuração para a subdivisão/cargo e/ou especialidade do contrato em manutenção.");
    }
    if ( !$obErro->ocorreu() ) {
        foreach ($arEventos as $arEvento) {
            if ($arEvento['inCodigoEvento'] == $_GET['inCodigoEvento'] and $arEvento['stDesdobramento'] == $_GET['stDesdobramento']) {
                switch ($_GET['stDesdobramento']) {
                    case 'S':
                        $stDesd = "Saldo Salário";
                    break;
                    case 'A':
                        $stDesd = "Aviso Prévio Indenizado";
                    break;
                    case 'V':
                        $stDesd = "Férias Vencidas";
                    break;
                    case 'P':
                        $stDesd = "Férias Proporcionais";
                    break;
                    case 'D':
                        $stDesd = "13º Salário";
                    break;
                }
                $obErro->setDescricao("O Evento ".$_GET['inCodigoEvento']."-".$_GET['hdnDescEvento']."(".$stDesd.") já está incluído na lista.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arEventos = Sessao::read("arEventos");
        $arEvento                               = array();
        $arEvento['inId']                       = count($arEventos);
        $arEvento['inCodigoEvento']             = $_GET['inCodigoEvento'];
        $arEvento['stDescricao']                = $_GET['hdnDescEvento'];
        $arEvento['nuValorEvento']              = $_GET['nuValorEvento'];
        $arEvento['nuQuantidadeEvento']         = $_GET['nuQuantidadeEvento'];
        $arEvento['nuQuantidadeParcelasEvento'] = $_GET['nuQuantidadeParcelasEvento'];
        $arEvento['stDesdobramento']            = $_GET['stDesdobramento'];
        $arEventos[]          = $arEvento;
        Sessao::write("arEventos",$arEventos);
        $stJs .= montaListaEventos(Sessao::read("arEventos"));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarEvento()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
    $obErro = new erro;
    $arEventos = ( is_array(Sessao::read('arEventos')) ? Sessao::read('arEventos') : array());
    $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento;
    $stFiltro  = " AND evento.codigo = '".$_GET['inCodigoEvento']."'";
    $stFiltro .= " AND sub_divisao.cod_sub_divisao = ".$_GET['inCodSubDivisao'];
    $stFiltro .= " AND cargo.cod_cargo = ".$_GET['inCodCargo'];
    $stFiltro .= ( $_GET['inCodEspecialidade'] ) ? " AND especialidade.cod_especialidade = ".$_GET['inCodEspecialidade'] : "";
    switch ($_GET['stDesdobramento']) {
        case "S":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 1";
            break;
        case "A":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 4";
            break;
        case "V":
        case "P":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 2";
            break;
        case "D":
            $stFiltro .= " AND configuracao_evento_caso.cod_configuracao = 3";
            break;
    }
    $obTFolhaPagamentoRegistroEvento->recuperaRelacionamentoConfiguracao($rsConfiguracao,$stFiltro);
    if ( $rsConfiguracao->getNumLinhas() < 0 ) {
        $obErro->setDescricao("O evento informado não possui configuração para a subdivisão/cargo e/ou especialidade do contrato em manutenção.");
    }
    if ( !$obErro->ocorreu() ) {
        foreach ($arEventos as $arEvento) {
            if($arEvento['inCodigoEvento'] == $_GET['inCodigoEvento'] and $arEvento['stDesdobramento'] == $_GET['stDesdobramento']
               and $arEvento['inId'] != Sessao::read('inIdEditar')){
                switch ($_GET['stDesdobramento']) {
                    case 'S':
                        $stDesd = "Saldo Salário";
                    break;
                    case 'A':
                        $stDesd = "Aviso Prévio Indenizado";
                    break;
                    case 'V':
                        $stDesd = "Férias Vencidas";
                    break;
                    case 'P':
                        $stDesd = "Férias Proporcionais";
                    break;
                    case 'D':
                        $stDesd = "13º Salário";
                    break;
                }
                $obErro->setDescricao("O Evento ".$_GET['inCodigoEvento']."-".$_GET['hdnDescEvento']."(".$stDesd.") já está incluído na lista.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arEventos = Sessao::read("arEventos");
        $arEvento                                                       = array();
        $arEvento['inId']                                               = Sessao::read('inIdEditar');
        $arEvento['inCodigoEvento']                                     = $_GET['inCodigoEvento'];
        $arEvento['stDescricao']                                        = $_GET['hdnDescEvento'];
        $arEvento['nuValorEvento']                                      = ($_GET['nuValorEvento'] != "")?$_GET['nuValorEvento']:'0,00';
        $arEvento['nuQuantidadeEvento']                                 = ($_GET['nuQuantidadeEvento'] != "")?$_GET['nuQuantidadeEvento']:'0,00';
        $arEvento['nuQuantidadeParcelasEvento']                         = $_GET['nuQuantidadeParcelasEvento'];
        $arEvento['stDesdobramento']                                    = $_GET['stDesdobramento'];
        $arEventos[Sessao::read('inIdEditar')]     = $arEvento;
        Sessao::write("arEventos",$arEventos);
        $stJs .= montaListaEventos(Sessao::read("arEventos"));
        Sessao::write('inIdEditar',"");
        Sessao::write('inCodRegistro',"");
        $stJs .= "f.btAlterarEvento.disabled = true;     \n";
        $stJs .= "f.btIncluirEvento.disabled = false;    \n";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirEvento()
{
    $arEventos = ( is_array(Sessao::read('arEventos')) ? Sessao::read('arEventos') : array());
    $arTemp = array();

    foreach ($arEventos as $arEvento) {
        if ($arEvento['inId'] != $_GET['inId']) {
            $inId = sizeof($arTemp);
            $arEvento['inId'] = $inId;
            $arTemp[] = $arEvento;
        }
    }
    Sessao::write("arEventos",$arTemp);
    $stJs .= montaListaEventos(Sessao::read('arEventos'));

    return $stJs;
}

function montaAlterarEvento(Request $request)
{
    include_once(CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php");
    include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php");
    $arEventos = ( is_array(Sessao::read('arEventos')) ? Sessao::read('arEventos') : array());
    $arEvento  = $arEventos[$_GET['inId']];
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->setCodigo( $arEvento['inCodigoEvento'] );
    $obRFolhaPagamentoEvento->listarEvento( $rsEvento );
    $stJs .= preencheDescEvento($request);

    $stJs .= "jq('#inCodigoEvento').val('".$arEvento['inCodigoEvento']."'); \n";
    $stJs .= "jq('#hdnDescEvento').val('".$arEvento['stDescricao']."'); \n";
    $stJs .= "jq('#stEvento').html('".$arEvento['stDescricao']."'); \n";

    if ($rsEvento->getCampo('fixado') == 'V') {
        $stJs .= "jq('#nuValorEvento').val('".$arEvento['nuValorEvento']."'); \n";
        $stJs .= "jq('#nuQuantidadeEvento').val('".$arEvento['nuQuantidadeEvento']."'); \n";

        if ($rsEvento->getCampo('apresenta_parcela') != 'f') {
            $stJs .= "jq('#nuQuantidadeParcelasEvento').val('".$arEvento['nuQuantidadeParcelasEvento']."'); \n";
        }
    }
    if ($rsEvento->getCampo('fixado') == 'Q') {
        $stJs .= "jq('#nuQuantidadeEvento').val('".$arEvento['nuQuantidadeEvento']."'); \n";

        if ($rsEvento->getCampo('apresenta_parcela') != 'f') {
            $stJs .= "jq('#nuQuantidadeParcelasEvento').val('".$arEvento['nuQuantidadeParcelasEvento']."'); \n";
        }
    }
    Sessao::write('inIdEditar',$_GET['inId']);
    Sessao::write('inCodRegistro',$arEvento['inCodRegistro']);

    $stJs .= "jq('#stDesdobramento').val('".$arEvento['stDesdobramento']."');                               \n";
    $stJs .= "jq('#stCmbDesdobramento').val('".$arEvento['stDesdobramento']."');                            \n";
    $stJs .= "jq('#btAlterarEvento').prop('disabled', false);                                               \n";
    $stJs .= "jq('#btIncluirEvento').prop('disabled', true);                                                \n";

    return $stJs;
}

function mostraMensagem()
{
    $stMensagem = "Já existe um registro de evento com desdobramento de Complementação de 13º Salário, por este motivo este registro não pode ser alterado/excluído!";
    $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "gerarSpan":
        $stJs .= gerarSpan();
    break;
    case "processarFiltro":
        $stJs .= processarFiltro();
    break;
    case "processarForm":
        $stJs .= processarForm();
    break;
    case "limparFiltro":
        $stJs .= limparFiltro();
    break;
    case "incluirEvento":
        $stJs .= incluirEvento();
    break;
    case "alterarEvento":
        $stJs .= alterarEvento();
    break;
    case "excluirEvento":
        $stJs .= excluirEvento();
    break;
    case "montaAlterarEvento":
        $stJs .= montaAlterarEvento($request);
    break;
    case "mostraMensagem":
        $stJs .= mostraMensagem();
    break;
}

if ($stJs) {
   echo $stJs;
}

?>
