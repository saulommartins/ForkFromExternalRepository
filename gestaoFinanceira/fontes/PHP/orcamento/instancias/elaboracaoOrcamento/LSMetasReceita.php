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
    $Date: 2008-02-13 15:31:44 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-02.01.06
*/

/*
$Log$
Revision 1.8  2007/03/06 12:56:23  cako
Bug #8593#

Revision 1.7  2007/01/30 18:44:33  luciano
#7316#

Revision 1.6  2006/08/18 19:32:56  eduardo
Bug #5238#
Bug #5239#

Revision 1.5  2006/07/05 20:43:03  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php");
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php");
/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "MetasReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS = "JS".$stPrograma.".js";

include_once( $pgJS );
ini_set('max_input_vars', '10000');
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
$stCaminho = CAM_GF_ORC_INSTANCIAS."elaboracaoOrcamento/";

/**
    * Instância o OBJETO da regra de negócios RPrevisaoReceita
*/
$obRPrevisaoReceita       = new ROrcamentoPrevisaoReceita;
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obROrcamentoReceita      = new ROrcamentoReceita;

$arColunas        = array();
$arLinhas         = array();
$arID             = array();
$arIDPeriodo      = array();
$arValorFuncaoCol = array();
$arIDPeriodo2     = array();

$obRConfiguracaoOrcamento->consultarConfiguracao();
if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita();
}

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
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

/**
    * Prepara classificacao da dedutorapara enviar a pesquisa
*/
if ($_REQUEST['inCodClassificacao']) {
    $arClassificacao = explode( "." , $_REQUEST['inCodClassificacao'] );
    $inCount         = count( $arClassificacao );
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassificacao[$inPosicao] != 0) {
               break;
           }
    }

    for ($inPosicaoNew = 0; $inPosicaoNew <= $inPosicao; $inPosicaoNew++) {
        $stVerificaClassificacao .= $arClassificacao[$inPosicaoNew].".";
    }
    $stVerificaClassificacao = substr( $stVerificaClassificacao, 0, strlen( $stVerificaClassificacao ) - 1 );
}
/* ****************************************************** */

if ($_REQUEST['inNumCGM']) {
    $obRPrevisaoReceita->obROrcamentoReceita->obROrcamentoEntidade->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
    $stLink .= '&inNumCGM='.$_REQUEST['inNumCGM'];
}

if ($_REQUEST['inCodEntidade']) {
    $obRPrevisaoReceita->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}

if ($_REQUEST['stCodEstruturalInicial']) {
    $obRPrevisaoReceita->setCodEstruturalInicial( $_REQUEST['stCodEstruturalInicial'] );
    $stLink .= '&stCodEstruturalInicial='.$_REQUEST['stCodEstruturalInicial'];
}

if ($_REQUEST['stCodEstruturalFinal']) {
    $obRPrevisaoReceita->setCodEstruturalFinal( $_REQUEST['stCodEstruturalFinal'] );
    $stLink .= '&stCodEstruturalFinal='.$_REQUEST['stCodEstruturalFinal'];
}

if ($_REQUEST['inCodReceitaReduzidoInicial']) {
    $obRPrevisaoReceita->setCodReceitaInicial( $_REQUEST['inCodReceitaReduzidoInicial'] );
    $stLink .= '&inCodReceitaReduzidoInicial='.$_REQUEST['inCodReceitaReduzidoInicial'];
}

if ($_REQUEST['inCodReceitaReduzidoFinal']) {
    $obRPrevisaoReceita->setCodReceitaFinal( $_REQUEST['inCodReceitaReduzidoFinal'] );
    $stLink .= '&inCodReceitaReduzidoFinal='.$_REQUEST['inCodReceitaReduzidoFinal'];
}

if ($stVerificaClassificacao) {
    if ( substr($stVerificaClassificacao, 0, 5) == '9.1.7') {
        $obRPrevisaoReceita->setCodEstruturalDedutora( '%'.$stVerificaClassificacao.'%' );
        $stLink .= '&stCodEstruturalDedutora='.$stVerificaClassificacao;
    } else {
        SistemaLegado::exibeAviso("Classificação de conta dedutora inválida.","form","aviso");
        echo "<script> window.location = '".$pgFilt."?".Sessao::getId()."'; </script>";
    }

}

if ($_REQUEST['inCodReceita']) {
    $arClassificacao = explode( "." , $_REQUEST['inCodReceita'] );
    $inCount         = count( $arClassificacao );
    //busca o codigo da Classificacao que sera inserido
    //o codigo sera o ultimo do array que nao possua valor igual a zero
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassificacao[$inPosicao] != 0) {
            break;
        }
    }
    //remonta a Classificacao de Receita, colocanco '0' na ultima casa com valor
    for ($inPosicaoNew = 0; $inPosicaoNew <= $inPosicao; $inPosicaoNew++) {
            $stVerificaClassReceita .= $arClassificacao[$inPosicaoNew].".";
    }
    $stVerificaClassReceita = substr( $stVerificaClassReceita, 0, strlen( $stVerificaClassReceita ) - 1 );

    $obRPrevisaoReceita->obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $stVerificaClassReceita );
    $stLink .= '&inCodReceita='.$_REQUEST['inCodReceita'];
}

$stOrder = " cod_receita ";

$obRPrevisaoReceita->listar($rsListaReceita, $stOrder);

if ($rsListaReceita->getNumLinhas() < 0) {
    SistemaLegado::exibeAviso("Nenhum registro de Receita encontrado com o filtro informado.","form","aviso");
    echo "<script> window.location = '".$pgFilt."?".Sessao::getId()."'; </script>";
}

// Primeiro ARRAY monta o label das colunas do DataGrid
// Segundo ARRAY monta chave de identificador da receita

while ( !$rsListaReceita->eof() ) {
    $stLabel = $rsListaReceita->getCampo( 'cod_receita' )."-".$rsListaReceita->getCampo( 'descricao' );
    $arReceita[] = $rsListaReceita->getCampo( 'cod_receita' );
    array_push( $arLinhas         , $stLabel );
    array_push( $arID             , $rsListaReceita->getCampo( 'cod_receita' ) );
    array_push( $arValorFuncaoCol , $rsListaReceita->getCampo( 'vl_original' ) );

    // ---
    $stPeriodo = $rsListaReceita->getCampo( 'periodo' );
    array_push( $arIDPeriodo    , $stPeriodo );

    $rsListaReceita->proximo();
}

foreach ($arValorFuncaoCol as $inKey=>$inValor) {
    if ($stFuncaoValorTotal == "") {
        $stFuncaoValorTotal = $inValor;
    } else {
        $stFuncaoValorTotal = $stFuncaoValorTotal.":".$inValor;
    }
}

foreach ($arID as $inKey=>$inValor) {
    if ($stCodReceita == "") {
        $stCodReceita = $inValor;
    } else {
        $stCodReceita = $stCodReceita.":".$inValor;
    }
}

$arID_cod = explode( ":", $stCodReceita );

// Monta filtro com códigos da receita
if ($stCodReceita != "") {
    $stFiltro .= " ( ";
    for ( $inContLinhas = 0; $inContLinhas < count($arID_cod); $inContLinhas++) {
        $obRPrevisaoReceita->setCodigoReceita   ( $arID_cod[$inContLinhas] );
        if ( $obRPrevisaoReceita->getCodigoReceita() ) {
            $stFiltro .= " cod_receita = ".$obRPrevisaoReceita->getCodigoReceita()." OR ";
        }
    }
    if ($stFiltro) {
        $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 3 );
    }
    $stFiltro .= " ) ";
}

$obRPrevisaoReceita->listarPeriodo($rsListaReceita2, $stFiltro );

// Primeiro ARRAY monta o label das colunas do DataGrid
// Segundo ARRAY monta chave de identificador da receita
$inCount = 0;

$inCodAnterior = $rsListaReceita2->getCampo('cod_receita');
while ( !$rsListaReceita2->eof() ) {
    if ( $rsListaReceita2->getCampo('cod_receita') != $inCodAnterior ) {
        $inCount++;
    }
        $arIDPeriodo2[ $rsListaReceita2->getCampo('cod_receita') ][ ($rsListaReceita2->getCampo('periodo') - 1) ] = $rsListaReceita2->getCampo( 'vl_periodo' );
        $inCodAnterior = $rsListaReceita2->getCampo('cod_receita');
        $rsListaReceita2->proximo();
}

$arIDValor = array();
if ($arReceita) {
    foreach ($arReceita as $inIndice => $inCodigoReceita) {
        if ( isset($arIDPeriodo2[$inCodigoReceita])) {
            $arIDValor[]= $arIDPeriodo2[$inCodigoReceita];
        } else {
            $arIDValor[] = array();
        }
    }
}

$inQtdCol = count($arColunas);
$inQtdLin = count($arLinhas);

$stLink .= "&stAcao=".$stAcao;

/**
    * Instância o OBJETO FORM
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

$obHdnCodReceita = new Hidden;
$obHdnCodReceita->setName ( "stCodReceita" );
$obHdnCodReceita->setValue( $stCodReceita );

$obHdnValorFuncaoTotal = new Hidden;
$obHdnValorFuncaoTotal->setName ( "stFuncaoValorTotal" );
$obHdnValorFuncaoTotal->setValue( $stFuncaoValorTotal );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade"         );
$obHdnCodEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obTxtTotal = new Moeda;
$obTxtTotal->setName ( "inFuncaoTotal" );
$obTxtTotal->setValue( $inFuncaoTotal );
$obTxtTotal->setReadOnly( true );
$obTxtTotal->setSize ( 18 );
$obTxtTotal->setMaxLength( 18 );
//$obTxtTotal->obEvento->setOnChange("somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );");

$obTxtComponente = new Numerico;
$obTxtComponente->setName ( "inCelula" );
$obTxtComponente->setSize ( 18 );
$obTxtComponente->setMaxLength( 18 );
//$obTxtComponente->obEvento->setOnChange("somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );");

$obDataGrid = new DataGrid;
$obDataGrid->addForm( $obForm );
//$obDataGrid->funcao = "somatorio( [inQtdLinhas], [inQtdColunas], [inLinhaCorrente], [inColunaCorrente], 'l', '[stNomCelula]', 'inFuncaoTotal'  );";

$obDataGrid->addHidden            ( $obHdnAcao );
$obDataGrid->addHidden            ( $obHdnCodReceita );
$obDataGrid->addHidden            ( $obHdnValorFuncaoTotal );
$obDataGrid->addHidden            ( $obHdnQtdCol );
$obDataGrid->addHidden            ( $obHdnQtdLin );
$obDataGrid->addHidden            ( $obHdnCodEntidade );

$obDataGrid->setLinhasColunas     ( count($arLinhas), count($arColunas) );
$obDataGrid->setLabelColuna       ( $arColunas );
$obDataGrid->setLabelLinha        ( $arLinhas );
$obDataGrid->setID                ( $arID );
$obDataGrid->setIDColunas         ( $arIDPeriodo );
$obDataGrid->setValor             ( $arIDValor );
$obDataGrid->setValorFuncaoCol    ( $arValorFuncaoCol );
$obDataGrid->setComponente        ( $obTxtComponente );
$obDataGrid->setComponenteFuncao  ( $obTxtTotal );
$obDataGrid->setRotuloLinha       ( "Receitas" );
$obDataGrid->setRotuloColunas     ( "Períodos" );

// Caso setFuncaoColuna ( true ) || setFuncaoLinha ( true )
// Deve ser setado seus respectivos Labels
$obDataGrid->setLabelFuncaoColuna ( "Total" );
$obDataGrid->setFuncaoColuna      ( true );
//---
//$obDataGrid->setLabelFuncaoLinha  ( "Total" );
//$obDataGrid->setFuncaoLinha       ( true );

// Para o titulo do grid deve ser adicionado na última linha
$obDataGrid->addTituloGrid        ( "Registros de metas de arrecadação de receita" );

$obDataGrid->montaGrid();

$obOk  = new Ok();
$obOk->obEvento->setOnClick("validaRequest(); BloqueiaFrames(true,false); Salvar();");
$obLimpar  = new Limpar;

$obDataGrid->defineBarra( array( $obOk, $obLimpar ) );

$obDataGrid->show();

?>
        <script>zebra('Array','zb');</script>
<?php
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
