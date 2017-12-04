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
    * Página oculta do Relatório de IRRF
    * Data de Criação: 04/07/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.04.46
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFeriasVencidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherSpan($boExecuta=false)
{
    switch ($_POST['stOpcao']) {
        case 'contrato':
            montaFiltroContrato(true);
        break;
        case 'cgm_contrato':
            montaSpanCGMContrato(true);
        break;
        case 'lotacao_local';
             montaSpanLotacaoLocal(true);
        break;
        case 'regime_sub_divisao':
             montaSpanRegimeSubDivisao( true );
        break;
    }

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaFiltroContrato($boExecuta = false)
{
    include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );

    $obIFiltroContrato = new IFiltroContrato;
    $obIFiltroContrato->setInformacoesFuncao  ( false );
    //$obIFiltroContrato->obTxtContrato->setNull ( false );

    $obFormulario = new Formulario;
    $obIFiltroContrato->geraFormulario ( $obFormulario );

    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $obFormulario->montaInnerHtml();

    $stJs .= "f.stEval.value = '".$stEval."'; \n";
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';     \n";
    $stJs .= "d.getElementById('inContrato').focus();";

    $stJs .= montaSpanBotoesContrato ( false );
    $stJs .= montaSpanListaContratos ( false );

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}
function montaSpanBotoesContrato($boExecuta = false)
{
    $obFormulario = new Formulario;
    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btnIncluir" );
    $obBtnIncluir->setValue             ( "Incluir"    );
    $obBtnIncluir->setTipo              ( "button"     );
    $obBtnIncluir->obEvento->setOnClick ( "buscaValor('incluiContrato');" );

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName  ( 'btnLimpar' );
    $obBtnLimpar->setValue ( 'Limpar'    );
    $obBtnLimpar->setTipo  ( 'button'    );
    $obBtnLimpar->obEvento->setOnClick ( "buscaValor('limparContrato'); "   );

    $obFormulario->defineBarra ( array( $obBtnIncluir, $obBtnLimpar ) );
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnBotoes').innerHTML = '".$obFormulario->getHTML()."';    \n";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }

}

function montaSpanListaContratos($boExecuta = false)
{
    $rsRecordSet = new Recordset;
    $arContratos = Sessao::read('arContratos');

    if ( count($arContratos) > 0 ) {
        $rsRecordSet->preenche( $arContratos );
    }

      // Montagem Lista
    $obLstContratos = new Lista;

    $obLstContratos->setTitulo          ( 'Matrículas para o Filtro' );
    $obLstContratos->setMostraPaginacao ( false                     );
    $obLstContratos->setRecordset       ( $rsRecordSet              );

    // Cabeçalho da lista
    $obLstContratos->addCabecalho();
    $obLstContratos->ultimoCabecalho->addConteudo ( "&nbsp;"     );
    $obLstContratos->ultimoCabecalho->setWidth    ( 3            );
    $obLstContratos->commitCabecalho();

    $obLstContratos->addCabecalho();
    $obLstContratos->ultimoCabecalho->addConteudo ( 'Matrícula'   );
    $obLstContratos->ultimoCabecalho->setWidth    ( 20           );
    $obLstContratos->commitCabecalho();

    $obLstContratos->addCabecalho();
    $obLstContratos->ultimoCabecalho->addConteudo ( 'CGM'        );
    $obLstContratos->ultimoCabecalho->setWidth    ( 75           );
    $obLstContratos->commitCabecalho();

    $obLstContratos->addCabecalho();
    $obLstContratos->ultimoCabecalho->addConteudo ( 'Ação' );
    $obLstContratos->ultimoCabecalho->setWidth    ( 5      );
    $obLstContratos->commitCabecalho();

    $obLstContratos->addAcao();
    $obLstContratos->ultimaAcao->setAcao( "EXCLUIR" );
    $obLstContratos->ultimaAcao->setFuncao( true );
    $obLstContratos->ultimaAcao->setLink( "JavaScript:alteraDado('excluirContrato');" );
    $obLstContratos->ultimaAcao->addCampo("1","inId");
    $obLstContratos->commitAcao();

    $obLstContratos->addDado();
    $obLstContratos->ultimoDado->setCampo( 'Matrícula' );
    $obLstContratos->commitDado();

    $obLstContratos->addDado();
    $obLstContratos->ultimoDado->setCampo( 'CGM' );
    $obLstContratos->commitDado();

    $obLstContratos->montaHTML();
    $stHtml = $obLstContratos->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('spnListaContratos').innerHTML = ' " .$stHtml. "'; ";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}
function montaSpanCGMContrato($boExecuta=false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IFiltroCGMContrato.class.php' );

    $obIFiltroCGMContrato = new IFiltroCGMContrato;
    $obIFiltroCGMContrato->setInformacoesFuncao  ( false );
    $obIFiltroCGMContrato->obCmbContrato->setNull( false );

    $obFormulario = new Formulario;
    $obIFiltroCGMContrato->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."';    \n";
    $stJs .= "f.hdnFiltro.value                       = '".$stEval."';                     \n";

    $stJs .= montaSpanBotoesContrato ( false );
    $stJs .= montaSpanListaContratos ( false );

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function montaSpanLotacaoLocal($boExecuta = false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLocal.class.php'   );
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLotacao.class.php' );

    $obISelectMultiploLocal   = new ISelectMultiploLocal;
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;

    $obFormulario = new Formulario;
    $obFormulario->addComponente                ( $obISelectMultiploLotacao );
    $obFormulario->addComponente                ( $obISelectMultiploLocal   );

    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    // zerando a array de contratos
    Sessao::write('arContratos', array());

    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnFiltro').innerHTML         = '".$obFormulario->getHTML()."'; \n";
    $stJs .= "d.getElementById('spnListaContratos').innerHTML = '';                             \n";
    $stJs .= "d.getElementById('spnBotoes').innerHTML         = '';                             \n";
    $stJs .= "f.stEval.value                                  = '".$stEval."';                  \n";

    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}
function incluiContrato($boExecuta = false)
{
    $arContrato  = array();
    $arContratos = Sessao::read('arContratos');
    $arFiltro    = Sessao::read('filtroRelatorio');

    if (!$_POST['inContrato']) {
        sistemaLegado::exibeAviso("Escolha um contrato!","","");
    } else {
        $i = 0;
        $achou = false;

        while (( $i< count($arContratos)) and ( !$achou )) {
            $achou = $arContratos[$i]['Contrato'] == $_POST['inContrato'];
            $i++;
        }

        if ($achou) {
            sistemaLegado::exibeAviso("Esta contrato já foi adicionado à lista!","","");
        } else {
            $arContrato['Contrato'] = $_POST['inContrato'];
            $arContrato['inId'    ] = count($arContratos);

            if ($_POST['stOpcao'] == 'cgm_contrato') {
                $arContrato['CGM'] = $_POST['inNumCGM'] .' - '. $_POST['inCampoInner'];
            } elseif ($_POST['stOpcao'] == 'contrato') {
                $arContrato['CGM'] = $_POST['hdnCGM'];
            }
            $arContratos[] = $arContrato;
            Sessao::write('arContratos', $arContratos);

            $stJs = montaSpanListaContratos( false );
            $stJs .= $_POST['stOpcao'] == 'contrato'? limparContrato( false ) : limparCGM ( false ) ;
            // zerando as lotações e locais escolhidas
            $arFiltro['inCodLotacaoSelecionados'] = array();
            $arFiltro['inCodLocalSelecionados']   = array();
             Sessao::write('filtroRelatorio', $arFiltro);
        }
    }
    if ($boExecuta) {
       sistemaLegado::executaFrameOculto( $stJs );
    } else {
       return $stJs;
    }
}
function excluirContrato($id)
{
    $inCount  = 0;
    $arTemp   = array();
    $arContratos = Sessao::read('arContratos');

    foreach ($arContratos as $campo => $valor) {
        if ($valor['inId'] != $id) {
            $arTemp[] =  $valor;
        }
    }
    $arContratos = $arTemp;
    Sessao::write('arContratos', $arContratos);

    return  montaSpanListaContratos();
}

function limparContrato($boExecuta = false)
{
    $stJs.= "f.inContrato.value = '';                           \n";
    $stJs.= "d.getElementById('inNomCGM').innerHTML = '&nbsp;'; \n";
    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function limparCGM($boExecuta = false)
{
     $stJs.= "limpaSelect(f.inContrato,0);                       \n";
     $stJs.= "f.inContrato[0] = new Option('Selecione','','selected');\n";
     $stJs.= "f.inNumCGM.value = '';                             \n";
     $stJs.= "d.getElementById('inCampoInner').innerHTML = '&nbsp;'; \n";
     if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
     } else {
        return $stJs;
     }

}

function montaSpanRegimeSubDivisao($boExecuta = false)
{
    include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploRegSubCarEsp.class.php' );
    $arFiltro = Sessao::read('filtroRelatorio');

    $obIFiltroRegSubCarEsp = new ISelectMultiploRegSubCarEsp;
    $obIFiltroRegSubCarEsp->setDisabledFuncao        ( true );
    $obIFiltroRegSubCarEsp->setDisabledCargo         ( true );
    $obIFiltroRegSubCarEsp->setDisabledEspecialidade ( true );

    $obFormulario = new Formulario;
    $obIFiltroRegSubCarEsp->geraFormulario       ( $obFormulario                                             );
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);
    $obFormulario->montaInnerHtml();

    $stJs .= "d.getElementById('spnFiltro').innerHTML = '".$obFormulario->getHTML()."'; \n";
    $stJs .= "d.getElementById('spnListaContratos').innerHTML = '';                     \n";
    $stJs .= "d.getElementById('spnBotoes').innerHTML         = '';                     \n";
    $stJs .= "f.stEval.value                                  = '".$stEval."';          \n";

    // zerando a array de contratos
    $arFiltro['inCodLotacaoSelecionados'] = array();
    $arFiltro['inCodLocalSelecionados']   = array();
    Sessao::write('filtroRelatorio', $arFiltro);
    Sessao::write('arContratos',  array());

    if ($boExecuta) {
        sistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

$stJs = '';

switch ($_POST["stCtrl"]) {
    case "preencherSpan":
        $stJs.= preencherSpan();
    break;

    case "limpar":
        $stJs.= processarFiltro();
    break;
    case 'incluiContrato':
        incluiContrato( true );
    break;
    case 'excluirContrato':
        $stJs =  excluirContrato ( $_POST['inId'] ? $_POST['inId'] : $_GET['inId'] );
    break;
    case 'limparContrato':
        limparContrato( true );
    break;
}
if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
