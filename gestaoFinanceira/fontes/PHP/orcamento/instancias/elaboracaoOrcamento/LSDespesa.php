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
    * Data de Criação   : 28/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';

//Define o nome dos arquivos PHP
if (Sessao::getExercicio() > '2009') {
    $stPrograma = 'DespesaAcao';
} else {
    $stPrograma = 'Despesa';
}

$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgCons = 'CO'.$stPrograma.'.php';
$pgPror = 'PO'.$stPrograma.'.php';

$stCaminho = CAM_GF_ORC_INSTANCIAS.'elaboracaoOrcamento/';
$obROrcamentoDespesa = new ROrcamentoDespesa;

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

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

include_once( CAM_GF_ORC_MAPEAMENTO . "TOrcamentoDespesa.class.php" );
$obTOrcamentoDespesa = new TOrcamentoDespesa;

$arFiltro = array();

// Se nao houver entidade selecionada, busca as entidades disponiveis para o usuario.
if ( !is_array($_REQUEST['inCodEntidade']) ) {
    include_once( CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php');
    $obROrcamentoEntidade = new ROrcamentoEntidade();
    $obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
    while (!$rsEntidades->eof()) {
        $_REQUEST['inCodEntidade'][] = $rsEntidades->getCampo('cod_entidade');
        $rsEntidades->proximo();
    }
}

if ( count( $_REQUEST['inCodEntidade'] ) > 0 ) {
    $stEntidades = implode( ' , ', $_REQUEST['inCodEntidade'] );
    $arFiltro[] = " despesa.cod_entidade in ( $stEntidades ) ";
}

if ($_REQUEST['inCodDotacaoInicial']) {
    $arFiltro[] = "despesa.cod_despesa >= " .  $_REQUEST['inCodDotacaoInicial'] ;
}

if ($_REQUEST['inCodDotacaoFinal']) {
    $arFiltro[] = "despesa.cod_despesa <= " .  $_REQUEST['inCodDotacaoFinal'] ;
}

if ($_REQUEST['stDescricao']) {
    $arFiltro[] = "conta_despesa.descricao ilike '%" .  $_REQUEST['stDescricao'] ."%'";
}

if ($_REQUEST['inCodRecurso']) {
    $arFiltro[] = "despesa.cod_recurso = ". $_REQUEST['inCodRecurso'] ;
}

if ($_REQUEST['inCodUso'] != "" && $_REQUEST['inCodDestinacao'] != "" && $_REQUEST['inCodEspecificacao'] != "") {
    $arFiltro[] = "recurso.masc_recurso_red like '".$_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao']."%' ";
}

if ($_REQUEST['inCodDetalhamento']) {
    $arFiltro[] =  "recurso.cod_detalhamento = ".$_REQUEST['inCodDetalhamento'];
}

if ($_REQUEST['inCodDespesa']) {
    $arFiltro[] = "conta_despesa.cod_estrutural = '".  $_REQUEST['inCodDespesa'] ."'";
}

if ($_REQUEST['inCodOrgao']) {
    $arOrgao = explode( '-', $_REQUEST['inCodOrgao']);
    $arFiltro[] = 'despesa.num_orgao = ' .$arOrgao[1];
}

if ($_REQUEST['inCodUnidade']) {
    $arUnidade = explode ( '-',  $_REQUEST['inCodUnidade'] );
    $arFiltro[] = "despesa.num_unidade = " . $arUnidade[0];
}

if ($_REQUEST['inCodFuncao']) {
    $arFiltro[] = "despesa.cod_funcao = ". $_REQUEST['inCodFuncao'] ;
}

if ($_REQUEST['inCodSubFuncao']) {
    $arFiltro[] = "despesa.cod_subfuncao = " . $_REQUEST['inCodSubFuncao'] ;
}

if ($_REQUEST['inCodPrograma']) {
    //$arFiltro[] = "despesa.cod_programa = " . $_REQUEST['inCodPrograma'];
    $arFiltro[] = "programa.num_programa = " . $_REQUEST['inCodPrograma'];
}

if ($_REQUEST['inCodPAO']) {
    //$arFiltro[] = "despesa.num_pao = " .  $_REQUEST['inCodPAO'] ;
    $arFiltro[] = "acao.num_acao = " .  $_REQUEST['inCodPAO'] ;
}

$arFiltro[] = "despesa.exercicio = '" . Sessao::getExercicio() ."'";

if ( $_POST['stDotacaoOrcamentaria'] )
    $arDotacao = explode( '.',  $_POST['stDotacaoOrcamentaria'] );
    if ( ($arDotacao[6] != '00000000000000') and ($arDotacao[6]) ) {
        $arFiltro[] = " replace(  orcamento.fn_consulta_class_despesa( conta_despesa.cod_conta
                                           , conta_despesa.exercicio::character varying
                                           ,(( SELECT configuracao.valor
                                                 FROM administracao.configuracao
                                                WHERE configuracao.cod_modulo = 8
                                                  AND configuracao.parametro::text = 'masc_class_despesa'::text
                                                  AND configuracao.exercicio = conta_despesa.exercicio))::character varying )
                          ,'.','') = '" . $arDotacao[6] . "'";
    }

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = ' where ' . implode( ' and ', $arFiltro ) ;
}

$obTOrcamentoDespesa->setDado('exercicio',Sessao::getExercicio());
if (Sessao::getExercicio() > '2009') {
    $obTOrcamentoDespesa->listaDespesaAcao($rsLista, $stFiltro);
} else {
    $obTOrcamentoDespesa->listaDespesa($rsLista, $stFiltro);
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código Reduzido");
$obLista->ultimoCabecalho->setWidth( 15 );
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
$obLista->ultimoDado->setCampo( "cod_despesa" );
$obLista->ultimoDado->setTitle( "dotacao"     );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setTitle( "dotacao"     );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addAcao();

$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo('&inCodDespesa'      , 'cod_despesa');
$obLista->ultimaAcao->addCampo('&stDescricao'       , 'descricao');
$obLista->ultimaAcao->addCampo('&stDescricaoRecurso', 'nom_recurso');
$obLista->ultimaAcao->addCampo('&inCodRecurso'      , 'cod_recurso');
$obLista->ultimaAcao->addCampo('&inCodEntidade'     , 'cod_entidade');
$obLista->ultimaAcao->addCampo('&stMascClassDespesa', 'mascara_classificacao');
$obLista->ultimaAcao->addCampo('&stDescQuestao'     , 'cod_despesa');
$obLista->ultimaAcao->addCampo('&inCodPPA'          , 'cod_ppa');
$obLista->ultimaAcao->addCampo('&inCodAcao'         , 'cod_acao');
$obLista->ultimaAcao->addCampo('&inNumAcao'         , 'num_acao');
$obLista->ultimaAcao->addCampo('&inAno'             , 'ano');
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx. "?stAcao=$stAcao&".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?stAcao=$stAcao&".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();
?>
