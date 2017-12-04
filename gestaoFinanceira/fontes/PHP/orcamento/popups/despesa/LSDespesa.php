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
/*
    * Página De listagem de Itens
    * Data de Criação   : 20/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @ignore

    $Id: LSDespesa.php 62398 2015-05-04 17:23:58Z michel $

    $Revision: 31725 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:28:41 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-02.01.26
                    uc-02.01.06
                    uc-02.01.33
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CodigoReduzido";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stFncJavaScript  = " function insereDespesa(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";

if ($_REQUEST['tipoBusca'] == 'autorizacaoEmpenho') {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].buscaDado('buscaDespesa'); \n";
}

$stFncJavaScript .= " window.close();   \n";
$stFncJavaScript .= " }                 \n";

$obROrcamentoDespesa = new ROrcamentoDespesa;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if (isset($_GET['stAcao'])) {
    $stAcao = $_GET['stAcao'];
} elseif (isset($_POST['stAcao'])) {
    $stAcao = $_POST['stAcao'];
}

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ( isset($_GET['stNomSelectMultiplo']) || isset($_REQUEST['inCodEntidade'])) {
    if ( $request->get($request->get('stNomSelectMultiplo')) && is_array( $request->get($request->get('stNomSelectMultiplo')) )) {
        $stEntidades = "";
        foreach ($_GET[$_GET['stNomSelectMultiplo']] as $key => $valor) {
            $stEntidades .= $valor.",";
        }
        $inCodEntidade = substr($stEntidades,0,strlen($stEntidades)-1);
    } elseif ( $_REQUEST['inCodEntidade'] && !is_array($_REQUEST['inCodEntidade']) ) {
        $inCodEntidade = $_REQUEST['inCodEntidade'];
    }
} else {
    $inCodEntidade = "";
}

$stTipoBusca      = isset($_REQUEST['tipoBusca'])    ? $_REQUEST['tipoBusca']    : '';
$inNumOrgao       = isset($_REQUEST['inNumOrgao'])   ? $_REQUEST['inNumOrgao']   : '';
$inNumUnidade     = isset($_REQUEST['inNumUnidade']) ? $_REQUEST['inNumUnidade'] : '';
$inCodPrograma    = isset($_REQUEST['inCodPrograma'])? $_REQUEST['inCodPrograma']: '';
$inCodPAO         = isset($_REQUEST['inCodPAO'])     ? $_REQUEST['inCodPAO']     : '';
$inCodDespesa     = isset($_REQUEST['inCodDespesa']) ? $_REQUEST['inCodDespesa'] : '';
$stDescricao      = isset($_REQUEST['stDescricao'])  ? $_REQUEST['stDescricao']  : '';
$inCodCentroCusto = isset($_REQUEST['inCodCentroCusto']) ? $_REQUEST['inCodCentroCusto'] : '';
$stMascClassificacaoDespesa = isset($_REQUEST['stMascClassificacaoDespesa']) ? $_REQUEST['stMascClassificacaoDespesa'] : '';
$stLink = '';
//Monta sessae com os valores do filtro
$arFiltro = Sessao::read('filtroPopUp');

if($_REQUEST['pg']&&$_REQUEST['pos']){
    if ( is_array($arFiltro) ) {
        $_REQUEST = $arFiltro;
    }
}
    
foreach ($_REQUEST as $key => $valor) {
    $arFiltro[$key] = $valor;
}
Sessao::write('filtroPopUp',$arFiltro);

if ( isset($_REQUEST["campoNom"]) ) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ( isset($_REQUEST["nomForm"]) ) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ( isset($_REQUEST["campoNum"]) ) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($stTipoBusca) {
    $stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];
}
if ($inCodEntidade) {
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}
if ($inNumOrgao) {
    $stLink .= '&inNumOrgao='. $inNumOrgao;
}
if ($inNumUnidade) {
    $stLink .= '&inNumUnidade='. $inNumUnidade;
}
if ($inCodPrograma) {
    $stLink .= '&inCodPrograma='. $inCodPrograma;
}
if ($inCodPAO) {
    $stLink .= '&inCodPAO='. $inCodPAO;
    //$inCodPAO está chegando aqui com o codigo de num_acao, transformar $inCodPAO para num_pao
    $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto ( $inCodPAO );
    $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setExercicio ( Sessao::getExercicio() );
    $obROrcamentoDespesa->obROrcamentoProjetoAtividade->consultarPorNumAcao( $rsPAO );
    $inCodPAO = $rsPAO->getCampo('num_pao');
}
if ($inCodDespesa) {
    $stLink .= '&inCodDespesa='. $inCodDespesa;
}
if ($stDescricao) {
    $stLink .= '&stDescricao='. $stDescricao;
}
if ($inCodCentroCusto) {
    $stLink .= '&inCodCentroCusto='. $inCodCentroCusto;
    }
if ($stMascClassificacaoDespesa) {
    $stLink .= '&stMascClassificacaoDespesa='.$_REQUEST['stMascClassificacaoDespesa'];
}
$stLink .= "&stAcao=".$stAcao;

if ($inCodEntidade && is_array($inCodEntidade)) {
    foreach ($inCodEntidade as $pos => $valor) {
        $stCodEntidade .= $valor.",";
    }
    $inCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-1);
}
if ($stTipoBusca == 'autorizacaoEmpenho') {
    $obROrcamentoDespesa->setDescricao( $stDescricao );
    $obROrcamentoDespesa->obROrcamentoEntidade->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $inNumUnidade );
    if ($inCodCentroCusto) {
        $obROrcamentoDespesa->setCodCentroCusto($inCodCentroCusto);
    }
    if ($inCodDespesa) {
        $obROrcamentoDespesa->setCodDespesa($inCodDespesa);
    }
    $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $inNumOrgao );
    $obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma( $inCodPrograma );
    $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto ( $inCodPAO );
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascClassificacaoDespesa );
    $obROrcamentoDespesa->listarDespesaUsuario( $rsLista , "ORDER BY cod_despesa");
} elseif ($stTipoBusca == 'alteracaoOrcamento') {
    $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    if( $stAcao == 'Remaneja' )
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao('3.1');
    $obROrcamentoDespesa->listarDespesa( $rsLista );    
} else {
    $obROrcamentoDespesa->setCodDespesa( $inCodDespesa );
    $obROrcamentoDespesa->setDescricao( $stDescricao );
    $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascClassificacaoDespesa );
    $obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
    $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade( $inNumUnidade );
    $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $inNumOrgao );
    $obROrcamentoDespesa->obROrcamentoPrograma->setCodPrograma( $inCodPrograma );
    $obROrcamentoDespesa->obROrcamentoProjetoAtividade->setNumeroProjeto ( $inCodPAO );
    $obROrcamentoDespesa->listarDespesa( $rsLista, "ORDER BY cod_despesa" );
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
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
$obLista->ultimoDado->setCampo( "cod_despesa" );
$obLista->ultimoDado->setTitle( "dotacao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setTitle( "dotacao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:window.close();insereDespesa();" );
$obLista->ultimaAcao->addCampo("1","cod_despesa");
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

Sessao::write('paginando',false);
?>
