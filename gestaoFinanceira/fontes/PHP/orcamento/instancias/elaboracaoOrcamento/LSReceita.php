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
    * Data de Criação   : 03/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: LSReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"    );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Receita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCaminho   = CAM_GF_ORC_INSTANCIAS."elaboracaoOrcamento/";

//$obROrcamentoReceita = new ROrcamentoReceita;
$obTOrcamentoReceita = new TOrcamentoReceita;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$stFiltro = isset($stFiltro) ? $stFiltro : null;
$stLink = isset($stLink) ? $stLink : null;
$stFiltroTemporario = isset($stFiltroTemporario) ? $stFiltroTemporario : null;
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'excluir' : $pgProx = $pgProc; break;
    default        : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg  = (!is_null($request->get('pg'))) ? $request->get('pg') : 0;
    $inPos = (!is_null($request->get('pos'))) ? $request->get('pos') : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $request->get('pg');
    $inPos = $request->get('pos');
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

if ($_REQUEST['stCodReceita']) {
    $arClassificacao = explode( "." , $_REQUEST['stCodReceita'] );
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

    //$obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $stVerificaClassReceita );
    $stFiltro .= " AND CR.mascara_classificacao like '". $stVerificaClassReceita ."%'";
    $stLink .= '&stCodReceita='.$_REQUEST['stCodReceita'];
}

if ($_REQUEST['inCodRecurso']) {
    $stFiltro .= " AND R.cod_recurso =".$_REQUEST['inCodRecurso'];
    $stLink .= '&inCodRecurso=' .  $_REQUEST['inCodRecurso'];
}

if ($request->get('inCodUso') != "" && $_REQUEST['inCodDestinacao'] != "" && $_REQUEST['inCodEspecificacao'] != "") {
    $stFiltro .= " AND R.masc_recurso_red like '".$_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao']."%' ";
    $stLink .= "&inCodUso=".$_REQUEST['inCodUso'];
    $stLink .= "&inCodDestinacao=".$_REQUEST['inCodDestinacao'];
    $stLink .= "&inCodEspecificaca=".$_REQUEST['inCodEspecificacao'];
}

if ($request->get('inCodDetalhamento')) {
    $stFiltro .= " AND R.cod_detalhamento = ".$_REQUEST['inCodDetalhamento'];
    $stLink .= "&inCodDetalhamento=".$_REQUEST['inCodDetalhamento'];
}

if ($_REQUEST['inCodReceitaInicial']) {
    $stFiltro .= " AND cod_receita >=".$_REQUEST['inCodReceitaInicial'];
    $stLink .= '&inCodReceitaInicial='.$_REQUEST['inCodReceitaInicial'];
}
if ($_REQUEST['inCodReceitaFinal']) {
    $stFiltro .= " AND cod_receita <=".$_REQUEST['inCodReceitaFinal'];
    $stLink .= '&inCodReceitaFinal='.  $_REQUEST['inCodReceitaFinal'];
}

if ($_REQUEST['stDescricao']) {
    $stFiltro .= " AND lower(CR.descricao) like lower('%". $_REQUEST['stDescricao'] ."%')";
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

$stFiltro .= " AND o.exercicio = '".Sessao::getExercicio()."' ";

$stLink .= "&stAcao=".$stAcao;

$stFiltro .= " AND CR.mascara_classificacao not like '9.%' ";

if (count($_REQUEST['inCodEntidade']) == 1) {
    $stFiltro .="\nAND E.cod_entidade = O.cod_entidade "."";
    $stFiltro .= "\nAND E.cod_entidade = ".$_REQUEST['inCodEntidade'][0]."\n";
    $stFiltro .= "AND O.exercicio = E.exercicio AND E.exercicio = CR.exercicio\n";
} elseif (count($_REQUEST['inCodEntidade'])>1) {
    $stFiltro .="\nAND E.cod_entidade = O.cod_entidade \nAND ("."";
    for ($inIndice = 0; $inIndice < count($_REQUEST['inCodEntidade']) - 1; $inIndice++ ) {
    $stFiltroTemporario .= "E.cod_entidade = ".$_REQUEST['inCodEntidade'][$inIndice]." OR ";
    }
    $stFiltroTemporario .= "E.cod_entidade = ".$_REQUEST['inCodEntidade'][$inIndice].")\n";
    $stFiltro .= $stFiltroTemporario."";
    $stFiltro .= " AND O.exercicio = E.exercicio AND E.exercicio = CR.exercicio";
}

$obTOrcamentoReceita->setDado('exercicio', Sessao::getExercicio() );
if (count($_REQUEST['inCodEntidade']) > 0) {
    $obTOrcamentoReceita->recuperaRelacionamentoComEntidades( $rsLista, $stFiltro, " CR.mascara_classificacao, O.cod_receita");
} else {
    $obTOrcamentoReceita->recuperaRelacionamento( $rsLista, $stFiltro, " CR.mascara_classificacao, O.cod_receita");
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Classificação");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Reduzido");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 55 );
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
$obLista->ultimoDado->setCampo( "cod_receita" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodReceita"        , "cod_receita"           );
$obLista->ultimaAcao->addCampo("&stDescricao"         , "descricao"             );
$obLista->ultimaAcao->addCampo("&stDescricaoRecurso"  , "nom_recurso"           );
$obLista->ultimaAcao->addCampo("&inCodRecurso"        , "cod_recurso"           );
$obLista->ultimaAcao->addCampo("&stMascClassReceita"  , "mascara_classificacao" );
$obLista->ultimaAcao->addCampo("stDescQuestao"        , "cod_receita"           );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
?>
