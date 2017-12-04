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

    $Id: LSReceita.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CodigoReduzido";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stFncJavaScript .= " function insereReceita(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

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

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST['stDescricao']) {
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}
if ($_REQUEST['inCodEntidade']) {
    $stLink .= '&inCodEntidade='.$_REQUEST['inCodEntidade'];
}
if ($_REQUEST['stCodEstrutural']) {
    $stLink .= '&stCodEstrutural='.$_REQUEST['stCodEstrutural'];
}
if ($_REQUEST['tipoBusca']) {
    $stLink .= '&tipoBusca='.$_REQUEST['tipoBusca'];
}

$stLink .= "&stAcao=".$stAcao;

switch ($_REQUEST['tipoBusca']) {
    case 'contArrec':
        include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
        $obMapeamento = new TOrcamentoReceita();
        $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'\n";
        if($_REQUEST['inCodEntidade'])
            $stFiltro .= " AND RECEITA.cod_entidade in (". $_REQUEST['inCodEntidade'].") \n";
        if($_REQUEST['stCodEstrutural'])
            $stFiltro .= " AND CLASSIFICACAO.mascara_classificacao like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' \n";
        if($_REQUEST['stDescricao'])
            $stFiltro .= " AND lower(CLASSIFICACAO.descricao) like lower('%".$_REQUEST['stDescricao']."%') \n";
        if (Sessao::getExercicio() < 2008) {
            $stFiltro .= " AND CPC.cod_estrutural not like '4.9.%' \n";
        } else {
            $stFiltro .= " AND CPC.cod_estrutural not like '9.%' \n";
        }
        $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                          FROM contabilidade.desdobramento_receita as dr
                                         WHERE   receita.cod_receita = dr.cod_receita_secundaria
                                             AND receita.exercicio   = dr.exercicio ) ";
        $obMapeamento->recuperaReceitaAnalitica($rsLista, $stFiltro, " ORDER BY cod_receita");
    break;

    case 'retencoes':
     include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
        $obMapeamento = new TOrcamentoReceita();
        $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'\n";
        if($_REQUEST['inCodEntidade'])
            $stFiltro .= " AND RECEITA.cod_entidade in (". $_REQUEST['inCodEntidade'].") \n";
        if($_REQUEST['stCodEstrutural'])
            $stFiltro .= " AND CLASSIFICACAO.mascara_classificacao like publico.fn_mascarareduzida('".$_REQUEST['stCodEstrutural']."')||'%' \n";
        if($_REQUEST['stDescricao'])
            $stFiltro .= " AND lower(CLASSIFICACAO.descricao) like lower('%".$_REQUEST['stDescricao']."%') \n";
        $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                          FROM contabilidade.desdobramento_receita as dr
                                         WHERE   receita.cod_receita = dr.cod_receita_secundaria
                                             AND receita.exercicio   = dr.exercicio ) ";
        if ( Sessao::getExercicio() > '2012' ) {
            $stFiltro .= " AND CLR.estorno = 'false' ";
            $obMapeamento->recuperaReceitaAnaliticaTCE($rsLista, $stFiltro, " ORDER BY mascara_classificacao");
        } else {
            $stFiltro .= " AND SUBSTR(CR.cod_estrutural,1,1) <> '9' ";
            $obMapeamento->recuperaReceitaAnalitica($rsLista, $stFiltro, " ORDER BY mascara_classificacao");
        }

    break;

    case 'receitaDedutora':
        include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora         ( true                         );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['stCodEstrutural'] );
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade               ( $_REQUEST['inCodEntidade']   );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao        ( $_REQUEST['stDescricao']     );
        $obROrcamentoReceita->listarReceitaDedutora( $rsLista, "ORDER BY cod_receita" );
    break;

    case 'receitaDedutoraExportacao':
        include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora         ( true                         );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['stCodEstrutural'] );
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade               ( $_REQUEST['inCodEntidade']   );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao        ( $_REQUEST['stDescricao']     );
        $obROrcamentoReceita->listarReceitaDedutora( $rsLista, "ORDER BY cod_receita" );
    break;

    default:
        include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_REQUEST['stCodEstrutural'] );
        $obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade               ( $_REQUEST['inCodEntidade']   );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDescricao        ( $_REQUEST['stDescricao']     );
        $obROrcamentoReceita->listarReceita( $rsLista );
    break;
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
$obLista->ultimoDado->setCampo( "cod_receita" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereReceita();" );
switch ($_REQUEST['tipoBusca']) {
    case 'estrutural':
        $obLista->ultimaAcao->addCampo("1","mascara_classificacao");
    break;

    case 'receitaDedutora':
        $obLista->ultimaAcao->addCampo("1","mascara_classificacao");
    break;

    case 'receitaDedutoraExportacao':
        $obLista->ultimaAcao->addCampo("1","cod_receita");
    break;

    default:
        $obLista->ultimaAcao->addCampo("1","cod_receita");
    break;
}
$obLista->ultimaAcao->addCampo("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
