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
    * Formulário
    * Data de Criação: 07/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.04

    $Id:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterImportacaoPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function gerarSpanImportacaoParcial()
{
    include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php" );

    Sessao::write('arContratos', array());

    $obIFiltroContrato = new IFiltroContrato;
    $obFormulario = new Formulario;

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir"    );
    $obBtnIncluir->setId                ( "btIncluir"    );
    $obBtnIncluir->setValue             ( "Incluir"      );
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirServidorImportar','inContrato');" );
    $arBarra[] = $obBtnIncluir;

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName              ( "btLimpar"      );
    $obBtnLimpar->setId                ( "btLimpar"      );
    $obBtnLimpar->setValue             ( "Limpar"        );
    $obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpaCamposContrato');" );
    $arBarra[] = $obBtnLimpar;

    $obSpnListaServidorImportar = new Span;
    $obSpnListaServidorImportar->setId( "spnContratos" );

    $obIFiltroContrato->geraFormulario( $obFormulario               );
    $obFormulario->defineBarra        ( $arBarra                    );
    $obFormulario->addSpan            ( $obSpnListaServidorImportar );

    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $stJs .= " if (jQuery('#boImportacaoParcial').attr('checked')) {                                           \n";
    $stJs .= "      jQuery('#spnImportacaoParcial').html('".$obFormulario->getHTML()."');                      \n";
    $stJs .= "      jQuery('#btIncluir').click( function () { montaParametrosGET('limpaCamposContrato'); } );  \n";
    $stJs .= " } else {                                                                                        \n";
    $stJs .= "      jQuery('#spnImportacaoParcial').html('');                                                  \n";
    $stJs .= " }                                                                                               \n";

    return $stJs;
}

function incluirServidorImportar()
{
    $obErro    = new erro;

    if ( trim($_GET['inContrato'])=="" ) {
        $obErro->setDescricao("Informe uma matrícula para inserir na lista de servidores a importar.");
    }

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
        $arContratos = Sessao::read("arContratos");
        $arContrato                             = array();
        $arContrato['inId']                     = count($arContratos);
        $arContrato['inContrato']               = $_GET['inContrato'];
        $arContrato['cod_contrato']             = $rsCGM->getCampo("cod_contrato");
        $arContrato['numcgm']                   = $rsCGM->getCampo("numcgm");
        $arContrato['nom_cgm']                  = $rsCGM->getCampo("nom_cgm");
        $arContratos[]        = $arContrato;
        Sessao::write("arContratos",$arContratos);
        $stJs .= montaListaContratos(Sessao::read('arContratos'));
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

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
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirServidorImportar');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "jQuery('#spnContratos').html('".$stHtml."');   \n";
    $stJs .= limpaCamposContrato();

    return $stJs;
}

function limpaCamposContrato()
{
    $stJs .= "jQuery('#inNomCGM').html('');                  \n";
    $stJs .= "jQuery('#inContrato').val('');                 \n";

    return $stJs;
}

function excluirServidorImportar()
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
    $stJs .= montaListaContratos($arTemp);

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanImportacaoParcial":
        $stJs .= gerarSpanImportacaoParcial();
        break;
    case "incluirServidorImportar":
        $stJs .= incluirServidorImportar();
        break;
    case "excluirServidorImportar":
        $stJs .= excluirServidorImportar();
        break;
    case "limpaCamposContrato":
        $stJs .= limpaCamposContrato();
        break;
}

if($stJs)
   echo($stJs);
?>
