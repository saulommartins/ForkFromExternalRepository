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
    * Data de Criação   : 06/10/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Aldo Jean

    * @ignore

    $Revision:  $
    $Name$
    $Autor: $
    $Date:  $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoFuncao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma 			= "Funcao";
$pgFilt 			= "FL".$stPrograma.".php";
$pgList 			= "LS".$stPrograma.".php";
$pgOcul 			= "OC".$stPrograma.".php";
$pgJs   			= "JS".$stPrograma.".php";

include_once($pgJs);

$stCaminho   = CAM_GF_PPA_INSTANCIAS."receita/";
$inExercicio = sessao::read('exercicio');
$obTOrcamentoFuncao = new TOrcamentoFuncao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Definicao dos objetos hidden
$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST[ 'campoNum' ] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST[ 'campoNom' ] );

if ($_REQUEST['inCodigo']) {
    $obRegra->setCodigo( $_REQUEST['inCodigo'] );
    $stLink .= '&inCodigo='.$_REQUEST['inCodigo'];
}
$cod_funcao = $_REQUEST['stCodigo'];
$descricao = $_REQUEST['stNome'];
if ($cod_funcao != "") {
    $stFiltro = " WHERE OF.cod_funcao LIKE '%".$cod_funcao."%' AND OF.exercicio = '".$inExercicio."'   \n";
} else {
    $stFiltro = " WHERE OF.exercicio = '".$inExercicio."'   \n";
}
if ($descricao != "") {
    $stFiltro .= "AND OF.descricao ILIKE '%".$descricao."%'";
}

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link',$arLink);
}

$stLink .= '&campoNum='.$_REQUEST[ 'campoNum' ];
$stLink .= '&campoNom='.$_REQUEST[ 'campoNom' ];
$stLink .= '&nomForm=' .$_REQUEST[ 'nomForm' ];
$stLink .= "&stAcao=".$stAcao;

$stFiltro .= "ORDER BY OF.cod_funcao";

$obTOrcamentoFuncao->recuperaFuncao( $rsLista, $stFiltro, $stOrder );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");// pk
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Função ");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício ");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();

// Cabeçalho para a ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_funcao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereFuncao()");
$obLista->ultimaAcao->addCampo('1',	'cod_funcao');
$obLista->ultimaAcao->addCampo('2',	'descricao');
$obLista->commitAcao();
$obLista->show();

$obForm = new Form;
$obForm->setAction( $pgFilt );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Voltar'                                          );

$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt."?.$stLink','telaPrincipal');");

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );

$obFormulario->show();
?>
