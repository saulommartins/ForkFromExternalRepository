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
    * Oculto
    * Data de Criação: 11/05/2007

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoCalculoFolhaPagamento.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO . "RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
include_once ( CAM_GRH_PES_COMPONENTES. "IFiltroContrato.class.php"                                         );
include_once ( CAM_GRH_PES_COMPONENTES. "IFiltroCGMContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES. "ISelectMultiploLotacao.class.php"                                  );

//$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterFolhaSituacao";
//$pgFilt = "FL".$stPrograma.".php";
//$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

function processarFormFiltro($boExecuta=false)
{
    $rsRecordset = new recordset;
    $rsRecordset = serialize($rsRecordset);
    Sessao::write("boExcluirCalculados",true);
    Sessao::write('contratos',$rsRecordset);
    $stJs .= limparSpans();
    $stJs .= gerarSpan1();
    $stJs .= gerarSpan3();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarListResultado($boExecuta=false)
{
    $rsContratos  = Sessao::read('contratos');
    if ( Sessao::read('calculados') < $rsContratos->getNumLinhas() ) {
        $stJs .= "f.submit();";
        $stJs .= gerarSpan1Resultado(false,false);
    } else {
        $rsContratos = ( is_object(unserialize( Sessao::read('contratos') )) ) ? unserialize( Sessao::read('contratos') ) : new recordset;
        $arRegistros = array();
        while ( !$rsContratos->eof() ) {
            $stContratos .= $rsContratos->getCampo('cod_contrato').",";
            $arRegistros[] = $rsContratos->getCampo('cod_contrato');
            $rsContratos->proximo();
        }
        $stContratos = substr($stContratos,0,strlen($stContratos)-1);
        $boPaginacao = true;
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $obRFolhaPagamentoCalculoFolhaPagamento = new RFolhaPagamentoCalculoFolhaPagamento;
        $obRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
        $obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obRFolhaPagamentoCalculoFolhaPagamento->listarContratosCalculados($rsCalculados,$arRegistros);

//        $arCalculados = array();
//        $arLista = $rsLista->getElementos();
//        foreach ($arLista as $arContrato) {
//            if ( in_array($arContrato["cod_contrato"],$arRegistros) ) {
//                $arCalculados[] = $arContrato;
//            }
//        }
//        $rsCalculados = new recordset();
//        $rsCalculados->preenche($arCalculados);
        Sessao::write("rsCalculados",$rsCalculados);
        $inSucesso = ($rsCalculados->getNumLinhas() > 0) ? $rsCalculados->getNumLinhas() : 0;
        $stJs .= "d.getElementById('inQuantContratosSucesso').innerHTML = '".$inSucesso."';    \n";

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
        $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
        $stFiltro  = " AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND contrato.cod_contrato IN ($stContratos)";
        $obTFolhaPagamentoLogErroCalculo->recuperaRelacionamento($rsListaErro,$stFiltro);
        $inErro  = ($rsListaErro->getNumLinhas() > 0) ? $rsListaErro->getNumLinhas() : 0;
        Sessao::write("rsListaErro",$rsListaErro);

        $stJs .= "d.getElementById('inQuantContratosErro').innerHTML = '".$inErro."';    \n";

        if ($inErro AND $_REQUEST["stOpcao"] == "") {
            $stJs .= "f.stOpcao[1].checked = true;";
            $stJs .= gerarSpan2Resultado();
        } else {
            $stJs .= gerarSpan1Resultado(false,true);
        }
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaSpanFiltro($boExecuta=false)
{
    switch ($_POST['stOpcao']) {
        case 'contrato':
            $stJs .= gerarSpan1();
            $stJs .= gerarSpan3();
            $stJs .= limparSpan5();
        break;
        case 'cgm_contrato':
            $stJs .= gerarSpan2();
            $stJs .= gerarSpan3();
            $stJs .= limparSpan5();
        break;
        case 'lotacao':
            $stJs .= limparSpans();
            $stJs .= gerarSpan5();
        break;
        case 'geral':
            $stJs .= limparSpans();
        break;
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitaSpanResultado($boExecuta=false)
{
    switch ($_POST['stOpcao']) {
        case 'calculados':
            $stJs .= gerarSpan1Resultado();
        break;
        case 'erro':
            $stJs .= gerarSpan2Resultado();
        break;
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparSpans($boExecuta=false)
{
    $stJs .= limparSpan1();
    $stJs .= limparSpan3();
    $stJs .= limparSpan4();
    $stJs .= limparSpan5();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparSpan1($boExecuta=false)
{
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparSpan3($boExecuta=false)
{
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparSpan4($boExecuta=false)
{
    $stJs .= "d.getElementById('spnSpan4').innerHTML = '';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparSpan5($boExecuta=false)
{
    $stJs .= "d.getElementById('spnSpan5').innerHTML = '';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1($boExecuta=false)
{
    $obIFiltroContrato = new IFiltroContrato;

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario          ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan2($boExecuta=false)
{
    $obIFiltroCGMContrato = new IFiltroCGMContrato;

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false)
{
    $obBtnIncluir = new Button;
    $obBtnIncluir->setName                      ( "btnIncluir"                                              );
    $obBtnIncluir->setValue                     ( "Incluir"                                                 );
    $obBtnIncluir->setTipo                      ( "button"                                                  );
    $obBtnIncluir->obEvento->setOnClick         ( "buscaValorFiltro('incluir');"                            );

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName                       ( "btnLimpar"                                               );
    $obBtnLimpar->setValue                      ( "Limpar"                                                  );
    $obBtnLimpar->setTipo                       ( "button"                                                  );
    $obBtnLimpar->obEvento->setOnClick          ( "buscaValorFiltro('limpar');"                             );

    $obFormulario = new Formulario;
    $obFormulario->defineBarra                  ( array($obBtnIncluir,$obBtnLimpar),'',''                   );
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$obFormulario->getHTML()."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan4($boExecuta=false)
{
    $rsLista = Sessao::read('contratos');
    $rsLista->addFormatacao("nom_cgm","HTML");
    $obLista = new Lista;
    $obLista->setRecordSet( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo("Matrículas a Calcular");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "contrato" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirDado('excluir');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    //$stHtml = str_replace("'","\\'",$stHtml);

    if ( $rsLista->getNumLinhas() ) {
        $stJs .= habilitarOpcoes();
    }

    $stJs .= "d.getElementById('spnSpan4').innerHTML = '".addslashes($stHtml)."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan5($boExecuta=false)
{
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;

    $obFormulario = new Formulario;
    $obFormulario->addComponente        ( $obISelectMultiploLotacao                   );
    $obFormulario->obJavaScript->montaJavaScript();
    $obFormulario->montaInnerHtml();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $stJs .= "d.getElementById('spnSpan5').innerHTML = '".$obFormulario->getHTML()."';  \n";
    $stJs .= "f.hdnSpan5.value = '".$stEval."';                                         \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function gerarSpan1Resultado($boExecuta=false,$boConcluido=true)
{
    $_SERVER["PHP_SELF"] = str_replace("OCManterCalculoSalario","LSManterCalculoSalario",$_SERVER["PHP_SELF"]);
    if ($boConcluido) {
        $rsCalculados = Sessao::read("rsCalculados");
        //if ($rsCalculados->getNumLinhas() <= 100) {
            $obLista = new Lista;
            $obLista->setRecordSet( $rsCalculados );
            $obLista->setTitulo("Matrículas Calculadas com Sucesso");

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Matrícula");
            $obLista->ultimoCabecalho->setWidth( 10 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("CGM");
            $obLista->ultimoCabecalho->setWidth( 35 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Ação");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("DIREITA");
            $obLista->ultimoDado->setCampo( "registro" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento("ESQUERDA");
            $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "visualizar" );
            $obLista->ultimaAcao->setFuncao("true");
            $obLista->ultimaAcao->setLink("javaScript:processarPopUp()");
            $obLista->ultimaAcao->addCampo( "&inRegistro" , "registro" );
            $obLista->ultimaAcao->addCampo( "&numcgm" , "numcgm" );
            $obLista->ultimaAcao->addCampo( "&nom_cgm" , "nom_cgm" );
            $obLista->commitAcao();

            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);
        //}
    } else {
        $rsContratos = Sessao::read('contratos');
        $inNumContratos = $rsContratos->getNumLinhas();
        $nuPorcentagem  = number_format((Sessao::read('calculados')*100/$inNumContratos), 2, ',', ' ');
        $stHtml  = "<center>".$nuPorcentagem."% dos contrato(s) calculado(s) até o momento!<br>";
        $stHtml .= "<img id=\"img_carregando\" src=\"".CAM_FW_IMAGENS."loading.gif\"></center>";
    }

    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function processarEventos($rsEventos)
{
    $arEventosCalculados = ( $rsEventos->getNumLinhas() > 0 ) ? $rsEventos->getElementos() : array();
    $arEventosTemp = array();
    foreach ($arEventosCalculados as $arEventoCalculado) {
        $boErro = false;
        foreach ($arEventosTemp as $arEventoCalculado2) {
            if ($arEventoCalculado['cod_contrato'] == $arEventoCalculado2['cod_contrato']) {
                $boErro = true;
                break;
            }
        }
        if ($boErro == false) {
            $arEventosTemp[] = $arEventoCalculado;
        }
    }
    $rsCalculados = new recordset;
    $rsCalculados->preenche($arEventosTemp);

    return $rsCalculados;
}

function gerarSpan2Resultado($boExecuta=false)
{
    $rsLista = Sessao::read("rsListaErro");

    $obLista = new Lista;
    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo("Matrículas com Erro no Cálculo");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Matrícula");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Evento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Erro");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "registro" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "erro" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $obBtnOk = new ok();
    $obBtnOk->setValue                      ( "Imprimir"                );
    $obBtnOk->obEvento->setOnclick("buscaValorFiltro('imprimir');");
    if ($rsLista->getNumLinhas() == -1) {
        $obBtnOk->setDisabled(true);
    }

    $obBtnRecalcular = new ok();
    $obBtnRecalcular->setValue              ( "Recalcular"                );
    $obBtnRecalcular->setStyle("with:300");
    $obBtnRecalcular->obEvento->setOnClick("buscaValorFiltro('recalcular');");
    if ($rsLista->getNumLinhas() == -1) {
        $obBtnRecalcular->setDisabled(true);
    }

    $obFormulario = new Formulario;
    $obFormulario->addTitulo("Para obter o erro exato do contrato precione o botão recalcular.");
    $obFormulario->defineBarra              ( array($obBtnOk,$obBtnRecalcular),"",""     );
    $obFormulario->obJavaScript->montaJavaScript();
    $obFormulario->montaInnerHtml();

    $stHtml .= $obFormulario->getHtml();

    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function incluir($boExecuta=false)
{
    $obRFolhaPagamentoCalculoFolhaPagamento = new RFolhaPagamentoCalculoFolhaPagamento ;
    $obRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
    $obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obErro = new erro;
    if ($_POST['inContrato'] == "") {
        $obErro->setDescricao('Campo Matrícula inválido().');
    }
    if ($_POST['inNumCGM'] == "" and $_POST['inOpcao'] == 'cgm_contrato') {
        $obErro->setDescricao('Campo CGM inválido().');
    }
    if ( !$obErro->ocorreu() ) {
        //Nessa variável inContrato está armazenado o valor do registro
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$_POST['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
        $stFiltro  = " AND registro_evento_periodo.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTFolhaPagamentoRegistroEvento->recuperaRegistrosEventosRegistradosPorUsuario($rsRegistroEvento,$stFiltro,"");
        if ( $rsRegistroEvento->getNumLinhas() < 0 ) {
            $obErro->setDescricao('O contrato não possui eventos registrados para esta competência().');
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsContratos        = Sessao::read('contratos');
        if (is_object($rsContratos)) {
            while ( !$rsContratos->eof() ) {
                if ( $rsContratos->getCampo('contrato') == $_POST['inContrato'] ) {
                    $obErro->setDescricao('Matrícula já inserida na lista.');
                    break;
                }
                $rsContratos->proximo();
            }
        }
        $rsContratos = new recordset;
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
        $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
        $obTPessoalContratoServidorCasoCausa->setDado("rescindidos",true);
        $stFiltro = " AND registro = ".$_POST['inContrato'];
        $obTPessoalContratoServidorCasoCausa->recuperaRescisaoContrato($rsRescisao,$stFiltro);
        if ( $rsRescisao->getNumLinhas() == 1 ) {
            $obErro->setDescricao("A matrícula ".$_POST['inContrato']." está rescindida.");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsContratos        = Sessao::read('contratos');
        $rsContratos        = ( is_object($rsContratos) ) ? $rsContratos : new recordset;
        $arContratos        = ( $rsContratos->getElementos() != 0 ) ? $rsContratos->getElementos() : array();
        $arTemp             =  array();
        if ($_POST['stOpcao'] == 'contrato') {
            $arCGM = explode("-",$_POST['hdnCGM']);
            $inNumCGM = $arCGM[0];
            $stNomCGM = stripslashes($arCGM[1]);
            $stJs .= gerarSpan1();
        } else {
            $inNumCGM = $_POST['inNumCGM'];
            $stNomCGM = stripslashes($_POST['inCampoInner']);
            $stJs .= gerarSpan2();
        }
        $rsContratos->setUltimoElemento();

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $obTPessoalContrato->recuperaTodos($rsCodContrato," WHERE registro = ".$_POST['inContrato']);

        $inId               = ( $rsContratos->getCampo('inId') != "" ) ? $rsContratos->getCampo('inId') : 0;
        $arTemp['inId']     = $inId+1;
        $arTemp['contrato'] = $_POST['inContrato'];
        $arTemp['cod_contrato'] = $rsCodContrato->getCampo("cod_contrato");
        $arTemp['numcgm']   = $inNumCGM;
        $arTemp['nom_cgm']  = $stNomCGM;
        $arContratos[]      = $arTemp;

        $obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->listarContratosComRegistroDeEventoPorCgm($rsContratosAutomaticos,$inNumCGM,$_POST['inContrato']);
        $inId++;
        while ( !$rsContratosAutomaticos->eof() ) {
            $boInserir = true;
            foreach ($arContratos as $arContrato) {
                if ( $arContrato['contrato'] == $rsContratosAutomaticos->getCampo('registro') ) {
                    $boInserir = false;
                    break;
                }
            }
            if ($boInserir) {
                $arTemp['inId']     = $inId+1;
                $arTemp['contrato'] = $rsContratosAutomaticos->getCampo('registro');
                $arTemp['cod_contrato'] = $rsContratosAutomaticos->getCampo('cod_contrato');
                $arTemp['numcgm']   = $inNumCGM;
                $arTemp['nom_cgm']  = $stNomCGM;
                $arContratos[]      = $arTemp;
            }
            $rsContratosAutomaticos->proximo();
        }

        $rsContratos        = new recordset;
        $rsContratos->preenche($arContratos);
        Sessao::write('contratos',$rsContratos);
        $stJs .= gerarSpan4();
        $stJs .= desabilitarOpcoes();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function excluir($boExecuta=false)
{
    $rsContratos        = Sessao::read('contratos');
    $arContratos        = $rsContratos->getElementos();
    $arTemp             = array();
    foreach ($arContratos as $arContrato) {
        if ($_GET['inId'] != $arContrato['inId']) {
            $arTemp[] = $arContrato;
        }
    }
    $arContratos        = array();
    $arContratos        = $arTemp;
    $rsContratos        = new recordset;
    $rsContratos->preenche($arContratos);
    Sessao::write('contratos',$rsContratos);
    $stJs .= gerarSpan4();
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limpar($boExecuta=false)
{
    if ($_POST['stOpcao'] == 'contrato') {
        $stJs .= gerarSpan1();
    } else {
        $stJs .= gerarSpan2();
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function desabilitarOpcoes($boExecuta=false)
{
    $stJs .= "f.stOpcao[2].disabled = true;     \n";
    $stJs .= "f.stOpcao[3].disabled = true;     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function habilitarOpcoes($boExecuta=false)
{
    $stJs .= "f.stOpcao[2].disabled = false;     \n";
    $stJs .= "f.stOpcao[3].disabled = false;     \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function submeter()
{
    $stJs .= "d.getElementById('fundo_carregando').height = document.height;    \n";
    $stJs .= "BloqueiaFrames(true,false);                                       \n";
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

function imprimir()
{
    $stJs .= "parent.frames[2].Salvar();    \n";

    return $stJs;
}

function recalcular()
{
    Sessao::write('contratos',Sessao::read("rsListaErro"));
    Sessao::remove('calculados');
    Sessao::write("boExcluirCalculados",0);
    Sessao::remove("link");
    $stJs .= "stAction = f.action;";
    $stJs .= "f.action ='PRManterFolhaSituacao.php?".Sessao::getId()."&stAcao=calcular&stErro=t';";
    $stJs .= submeter();
    $stJs .= "f.action = stAction;";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "submeter":
        $stJs .= submeter();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
