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
 * Página de Listagem para seleção de dados a serem alterados/excluídos
 * Data de Criação: 24/09/2009
 * Caso de uso: uc-02.09.05
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * $Id: $
 *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_CLASSES."negocio/RPPAManterReceita.class.php";
include_once CAM_GF_PPA_CLASSES."visao/VPPAManterReceita.class.php";

// Objeto controller
$obVisao = new VPPAManterReceita( new RPPAManterReceita );

//Define o nome dos arquivos PHP
$stPrograma = 'ManterReceita';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma. '.php';
$pgExcl     = 'FMExcluirReceita.php';
$pgCons     = 'FMConsultarReceita.php';
$inCodPPA  = $_REQUEST['inCodPPA'];
$inCodConta = 0;
if (!empty($_REQUEST['inCodConta'])) {
    $inCodConta = $_REQUEST['inCodConta'];
}

$stAcao = trim(strtolower($_REQUEST['stAcao']));

// Definir link
if ($stAcao == 'excluir') {
    if ($obVisao->isPPAHomologado($inCodPPA)) {
        $pgAcao = $pgExcl;
    } else {
        $pgAcao = $pgProc;
    }
} elseif ($stAcao == 'alterar') {
    $pgAcao = $pgForm;
} elseif ($stAcao == 'consultar') {
    $pgAcao = $pgCons;
} else {
    $pgAcao = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] && $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$stCaminho  = CAM_GF_PPA_INSTANCIAS . 'receita/' . $pgAcao;
$stCaminho .= '?' . Sessao::getId() . '&stAcao=' . $stAcao . $stLink;

$link = Sessao::read( 'link' );
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write( 'link', $link );
}
// Monta lista de Receitas cadastradas.
$rsReceita = $obVisao->recuperaListaReceitas($inCodPPA, $inCodConta);
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Dados da Lista dos resultados para ' . $stAcao );

$obLista->setRecordSet($rsReceita);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5 );
$obLista->commitCabecalho();
// Código da Receita
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Conta Receita' );
$obLista->ultimoCabecalho->setWidth   ( 10 );
$obLista->commitCabecalho();
// Descrição
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( 'Descrição' );
$obLista->ultimoCabecalho->setWidth   ( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 7 );
$obLista->commitCabecalho();
// DADOS DA LISTA
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setCampo      ( 'cod_conta' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->ultimoDado->setCampo      ( 'descricao' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );

$obLista->ultimaAcao->addCampo( '&cod_receita'      , 'cod_receita' );
$obLista->ultimaAcao->addCampo( 'cod_ppa'           , 'cod_ppa' );
$obLista->ultimaAcao->addCampo( 'exercicio'         , 'exercicio' );
$obLista->ultimaAcao->addCampo( 'cod_conta'         , 'cod_conta' );
$obLista->ultimaAcao->addCampo( 'cod_entidade'      , 'cod_entidade' );
$obLista->ultimaAcao->addCampo( 'nom_entidade'      , 'nom_entidade' );
$obLista->ultimaAcao->addCampo( 'valor_total'       , 'valor_total' );
$obLista->ultimaAcao->addCampo( 'periodo'           , 'periodo' );
$obLista->ultimaAcao->addCampo( 'destinacao_recurso', 'destinacao_recurso' );
$obLista->ultimaAcao->addCampo( 'cod_receita_dados' , 'cod_receita_dados' );
$obLista->ultimaAcao->addCampo( 'descricao'         , 'descricao' );
$obLista->ultimaAcao->addCampo( 'cod_norma'         , 'cod_norma' );
$obLista->ultimaAcao->addCampo( 'stDescQuestao'     , 'cod_conta');
$obLista->ultimaAcao->setLink ( $stCaminho );
$obLista->commitAcao();
$obLista->show();

?>
