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
    * Interface de Inclusão/Alteração Previsão Receita
    * Data de Criação   : 28/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2007-03-06 09:56:23 -0300 (Ter, 06 Mar 2007) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.9  2007/03/06 12:56:23  cako
Bug #8593#

Revision 1.8  2007/03/05 19:30:19  cako
Bug #8592#

Revision 1.7  2007/01/30 11:42:32  luciano
#7317#

Revision 1.6  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoDespesa.class.php" );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "MetasDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS = "JS".$stPrograma.".js";
include_once( $pgJS );

$stCaminho = CAM_GF_ORC_INSTANCIAS."elaboracaoOrcamento/";
?>
 <script type="text/javascript">

            function zebra(id, classe)
            {
                var tabela = document.getElementById(id);
                var linhas = tabela.getElementsByTagName("tr");
                    for (var i = 0; i < linhas.length; i++) {
                    ((i%2) == 0) ? linhas[i].className = classe : void(0);
                }
            }
        </script>
<?php
/**
    * Instância o OBJETO da regra de negócios RPrevisaoReceita
*/
$obRPrevisaoDespesa       = new ROrcamentoPrevisaoDespesa;
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obROrcamentoDespesa      = new ROrcamentoDespesa;

$arColunas        = array();
$arLinhas         = array();
$arID             = array();
$arIDPeriodo      = array();
$arValorFuncaoCol = array();
$arIDValor2       = array();

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obRConfiguracaoOrcamento->consultarConfiguracao();
if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasDespesa();
}

// Função de nomes dos meses para a configuração mesal
function mes($inValor)
{
    $stMes = array ( "","Jan.","Fev.","Mar.","Abr.","Maio","Jun.",
                     "Jul.","Ago.","Set.","Out.","Nov.","Dez.");

    return $stMes[$inValor];
}

// Calcula qual o tipo de configuração para montar o DataGrid
if ($inUnidadesMedidasMetas > 0) {
    $inNumeroColunas = (12/$inUnidadesMedidasMetas);
}
// Monta o label das colunas do DataGrid
if ($inNumeroColunas != 12) {
    for ($inKey=1; $inKey<=$inNumeroColunas; $inKey++) {
        $stLabel = $inKey."&#186";
        array_push( $arColunas, $stLabel );
    }
} else {
    for ($inKey=1; $inKey<=$inNumeroColunas; $inKey++) {
        array_push( $arColunas, mes($inKey) );
    }
}

/**
    * Monta sessão com os valores do filtro
*/
$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

if ($_REQUEST['stDescricao']) {
    $obRPrevisaoDespesa->setDescricao( $_REQUEST['stDescricao'] );
    $stLink .= '&stDescricao = '.$_REQUEST['stDescricao'];
}

if ($_REQUEST['inCodDotacaoInicial']) {
    $obRPrevisaoDespesa->setCodDotacaoInicial( $_REQUEST['inCodDotacaoInicial'] );
    $stLink .= '&inCodDotacaoInicial = '.$_REQUEST['inCodDotacaoInicial'];
}

if ($_REQUEST['inCodDotacaoFinal']) {
    $obRPrevisaoDespesa->setCodDotacaoFinal( $_REQUEST['inCodDotacaoFinal'] );
    $stLink .= '&inCodDotacaoFinal = '.$_REQUEST['inCodDotacaoFinal'];
}

if ($_REQUEST['inCodRubricaDespesaInicial']) {
    $obRPrevisaoDespesa->setCodRubricaDespesaInicial( $_REQUEST['inCodRubricaDespesaInicial'] );
    $stLink .= '&inCodRubricaDespesaInicial = '.$_REQUEST['inCodRubricaDespesaInicial'];
}

if ($_REQUEST['inCodRubricaDespesaFinal']) {
    $obRPrevisaoDespesa->setCodRubricaDespesaFinal( $_REQUEST['inCodRubricaDespesaFinal'] );
    $stLink .= '&inCodRubricaDespesaFinal = '.$_REQUEST['inCodRubricaDespesaFinal'];
}

if ($_REQUEST['inNumCGM']) {
    $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
    $stLink .= '&inNumCGM = '.$_REQUEST['inNumCGM'];
}

if ($_REQUEST['inCodEntidade']) {
    $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}

if ($_REQUEST['stDotacaoOrcamentaria']) {
    $arOrgaoUnidade = explode( "." , $_REQUEST['stDotacaoOrcamentaria'] );
    if ($arOrgaoUnidade[0] != 0) {
    $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arOrgaoUnidade[0] );
    }
    if ($arOrgaoUnidade[1] != 0) {
    $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $arOrgaoUnidade[1] );
    }
    $stLink .= '&stDotacaoOrcamentaria='.$_REQUEST['stDotacaoOrcamentaria'];
}

$stLink .= "&stAcao=".$stAcao;

$stOrder = " cod_despesa ";
$obRPrevisaoDespesa->listar($rsListaDespesa, $stOrder);

if ($rsListaDespesa->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso("Nenhum registro de Despesa encontrado com o filtro informado.","form","aviso");
    echo "<script> window.location = '".$pgFilt."?".Sessao::getId()."'; </script>";
}

//alertaAviso($pgForm."?inCodEntidade=".$_POST['inCodEntidade']."&stDotacaoOrcamentaria=".$_POST['stDotacaoOrcamentaria'], $inCodDespesa."/".$obROrcamentoDespesa->getExercicio(), "incluir", "aviso", Sessao::getId(), "../");

// Primeiro ARRAY monta o label das colunas do DataGrid
// Segundo ARRAY monta chave de identificador da receita
while ( !$rsListaDespesa->eof() ) {
    $stLabel = $rsListaDespesa->getCampo( 'cod_despesa' )."-".$rsListaDespesa->getCampo( 'descricao' );
    $arDespesa[] = $rsListaDespesa->getCampo( 'cod_despesa' );
    array_push( $arLinhas         , $stLabel );
    array_push( $arID             , $rsListaDespesa->getCampo( 'cod_despesa' ) );
    array_push( $arValorFuncaoCol , $rsListaDespesa->getCampo( 'vl_original' ) );

    // ---
    $stPeriodo = $rsListaDespesa->getCampo( 'periodo' );
    array_push( $arIDPeriodo    , $stPeriodo );

    $rsListaDespesa->proximo();
}

foreach ($arValorFuncaoCol as $inKey=>$inValor) {
    if ($stFuncaoValorTotal == "") {
        $stFuncaoValorTotal = $inValor;
    } else {
        $stFuncaoValorTotal = $stFuncaoValorTotal.":".$inValor;
    }
}

foreach ($arID as $inKey=>$inValor) {
    if ($stCodDespesa == "") {
        $stCodDespesa = $inValor;
    } else {
        $stCodDespesa = $stCodDespesa.":".$inValor;
    }
}

$arID_cod = explode( ":", $stCodDespesa );

// Monta filtro
if ($stCodDespesa != "") {
    $stFiltro .= "  ( ";
    for ( $inContLinhas = 0; $inContLinhas < count($arID_cod); $inContLinhas++) {
        $obRPrevisaoDespesa->setCodigoDespesa   ( $arID_cod[$inContLinhas] );
        if ( $obRPrevisaoDespesa->getCodigoDespesa() ) {
            $stFiltro .= " cod_despesa = ".$obRPrevisaoDespesa->getCodigoDespesa()." OR ";
        }
    }
    if ($stFiltro) {
        $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 3 );
    }
    $stFiltro .= " ) ";
}

$obRPrevisaoDespesa->listarPeriodo( $rsListaDespesa2, $stFiltro );

// Primeiro ARRAY monta o label das colunas do DataGrid
// Segundo ARRAY monta chave de identificador da receita
$inCount = 0;
$inCodAnterior = $rsListaDespesa2->getCampo('cod_despesa');
while ( !$rsListaDespesa2->eof() ) {
    if ( $rsListaDespesa2->getCampo('cod_despesa') != $inCodAnterior ) {
        $inCount++;
    }
    $arIDValor2[ $rsListaDespesa2->getCampo('cod_despesa') ][ ($rsListaDespesa2->getCampo('periodo') - 1) ] = $rsListaDespesa2->getCampo( 'vl_previsto' );

    $inCodAnterior = $rsListaDespesa2->getCampo('cod_despesa');

    $rsListaDespesa2->proximo();
}

$arIDValor = array();

if ($arDespesa) {
    foreach ($arDespesa as $inIndice => $inCodigoDespesa) {
        if ( isset($arIDValor2[$inCodigoDespesa])) {
            $arIDValor[]= $arIDValor2[$inCodigoDespesa];
        } else {
            $arIDValor[] = array();
        }
    }
}

$inQtdCol = count($arColunas);
$inQtdLin = count($arLinhas);

/**
    * Instância o OBJETO Lista
*/
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnQtdCol = new Hidden;
$obHdnQtdCol->setName ( "inQtdCol" );
$obHdnQtdCol->setValue( $inQtdCol );

$obHdnQtdLin = new Hidden;
$obHdnQtdLin->setName ( "inQtdLin" );
$obHdnQtdLin->setValue( $inQtdLin );

$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName ( "stCodDespesa" );
$obHdnCodDespesa->setValue( $stCodDespesa );

$obHdnValorFuncaoTotal = new Hidden;
$obHdnValorFuncaoTotal->setName ( "stFuncaoValorTotal" );
$obHdnValorFuncaoTotal->setValue( $stFuncaoValorTotal );

$obTxtTotal = new Moeda;
$obTxtTotal->setName ( "inFuncaoTotal" );
$obTxtTotal->setValue( $inFuncaoTotal );
$obTxtTotal->setSize ( 18 );
$obTxtTotal->setReadOnly( true );
$obTxtTotal->setMaxLength( 18 );
//$obTxtTotal->obEvento->setOnChange("somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );");

$obTxtComponente = new Moeda;
$obTxtComponente->setName ( "inCelula" );
$obTxtComponente->setSize ( 18 );
$obTxtComponente->setMaxLength( 18 );
//$obTxtComponente->obEvento->setOnChange("somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );");

$obDataGrid = new DataGrid;
$obDataGrid->addForm( $obForm );
//$obDataGrid->funcao = "somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );";

$obDataGrid->addHidden            ( $obHdnAcao );
$obDataGrid->addHidden            ( $obHdnCodDespesa );
$obDataGrid->addHidden            ( $obHdnValorFuncaoTotal );
$obDataGrid->addHidden            ( $obHdnQtdCol );
$obDataGrid->addHidden            ( $obHdnQtdLin );

$obDataGrid->setLinhasColunas     ( count($arLinhas), count($arColunas) );
$obDataGrid->setLabelColuna       ( $arColunas );
$obDataGrid->setLabelLinha        ( $arLinhas );
$obDataGrid->setID                ( $arID );
$obDataGrid->setIDColunas         ( $arIDPeriodo );
$obDataGrid->setValor             ( $arIDValor );
$obDataGrid->setValorFuncaoCol    ( $arValorFuncaoCol );
$obDataGrid->setComponente        ( $obTxtComponente );
$obDataGrid->setComponenteFuncao  ( $obTxtTotal );
$obDataGrid->setRotuloLinha       ( "Despesas" );
$obDataGrid->setRotuloColunas     ( "Períodos" );

// Caso setFuncaoColuna ( true ) || setFuncaoLinha ( true )
// Deve ser setado seus respectivos Labels
$obDataGrid->setLabelFuncaoColuna ( "Total" );
$obDataGrid->setFuncaoColuna      ( true );
//---
//$obDataGrid->setLabelFuncaoLinha  ( "Total" );
//$obDataGrid->setFuncaoLinha       ( true );

// Para o titulo do grid deve ser adicionado na última linha
$obDataGrid->addTituloGrid        ( "Registros de metas de execução de despesa" );

$obDataGrid->montaGrid();
$obOk   = new Ok;
$obOk->obEvento->setOnClick("validaRequest(); BloqueiaFrames(true,false); Salvar();");

$obVoltar = new Button;
$obVoltar->setName  ( "Cancelar" );
$obVoltar->setValue ( "Cancelar" );
$obVoltar->obEvento->setOnClick("Cancelar2();");
$obDataGrid->defineBarra( array($obOk,  $obVoltar ) );

//$obDataGrid->OK();

$obDataGrid->show();
?>
<script>zebra('Array','zb');</script>
