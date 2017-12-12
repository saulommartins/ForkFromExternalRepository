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
    * Página de Listagem de Itens
    * Data de Criação   : 21/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2006-07-10 15:15:52 -0300 (Seg, 10 Jul 2006) $

    * Casos de uso: uc-02.01.04
*/

/*
$Log$
Revision 1.5  2006/07/10 18:15:52  andre.almeida
Correções na paginação.

Revision 1.4  2006/07/05 20:42:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCaminho   = CAM_GF_ORC_INSTANCIAS."classEconomica/";

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

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

$inCodClassificacao = $_POST['inCodClassificacao'] ? $_POST['inCodClassificacao'] : $_GET['inCodClassificacao'];
if ($inCodClassificacao) {

    $arClassificacao = explode( "." , $inCodClassificacao );
    $inCount         = count( $arClassificacao );
    //busca o codigo da Classificacao que sera inserido
    //o codigo sera o ultimo do array que nao possua valor igual a zero
    for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
        if ($arClassificacao[$inPosicao] != 0) {
            break;
        }
    }
    //remonta a Classificacao de Despesa, colocanco '0' na ultima casa com valor
    for ($inPosicaoNew = 0; $inPosicaoNew <= $inPosicao; $inPosicaoNew++) {
            $stVerificaClassDespesa .= $arClassificacao[$inPosicaoNew].".";
    }
    $stVerificaClassDespesa = substr( $stVerificaClassDespesa, 0, strlen( $stVerificaClassDespesa ) - 1 );

    $obROrcamentoClassificacaoDespesa->setMascClassificacao( $stVerificaClassDespesa );
}

$stDescricao = $_POST['stDescricao'] ? $_POST['stDescricao'] : $_GET['stDescricao'];
if ($stDescricao) {
    $obROrcamentoClassificacaoDespesa->setDescricao( $stDescricao );
}

$stLink .= "&stAcao=".$stAcao;

$obROrcamentoClassificacaoDespesa->listar( $rsLista, "mascara_classificacao" );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "mascara_classificacao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodConta"                , "cod_conta"             );
$obLista->ultimaAcao->addCampo("&stDescricao"               , "descricao"             );
$obLista->ultimaAcao->addCampo("&stMascClassDespesa"        , "mascara_classificacao" );
$obLista->ultimaAcao->addCampo("&stMascClassDespesaReduzida", "mascara_classificacao_reduzida" );
$obLista->ultimaAcao->addCampo("stDescQuestao"              , "mascara_classificacao" );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
?>
