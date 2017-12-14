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
    * @author Desenvolvedor: Aldo Jean Soares Silva

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date:  $
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoSubfuncao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma 			= "SubFuncao";
$pgFilt 			= "FL".$stPrograma.".php";
$pgList 			= "LS".$stPrograma.".php";
$pgOcul 			= "OC".$stPrograma.".php";
$pgJs   			= "JS".$stPrograma.".php";

include_once($pgJs);

$InExercicio = sessao::read('exercicio');
$obTOrcamentoSubFuncao = new TOrcamentoSubFuncao;

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
$InCodSubFuncao = $_REQUEST['stCodigo'];
$chDescricao = $_REQUEST['stNome'];

if ($InCodSubFuncao != "") {
    $stFiltro = " WHERE cod_subfuncao ILIKE '%".$InCodSubFuncao."%' AND exercicio = '".$InExercicio."'   \n";
} else {
    $stFiltro = " WHERE exercicio = '".$InExercicio."'   \n";
}
if ($chDescricao != "") {
    $stFiltro .= "AND descricao ILIKE '%".$chDescricao."%'";
}

$stLink .= '&campoNum='.$_REQUEST[ 'campoNum' ];
$stLink .= '&campoNom='.$_REQUEST[ 'campoNom' ];
$stLink .= '&nomForm=' .$_REQUEST[ 'nomForm' ];
$stLink .= "&stAcao=".$stAcao;

$stFiltro .= "ORDER BY cod_subfuncao";

$obTOrcamentoSubFuncao->recuperaTodos( $rsLista, $stFiltro, $stOrder );  //??

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
$obLista->ultimoCabecalho->addConteudo("SubFunção ");
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
$obLista->ultimoDado->setCampo( "cod_subfuncao" );
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
$obLista->ultimaAcao->setLink("JavaScript:insereSubFuncao()");
$obLista->ultimaAcao->addCampo('1',	'cod_subfuncao');
$obLista->ultimaAcao->addCampo('2',	'descricao');
$obLista->commitAcao();
$obLista->show();

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
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt."?.$stLink','telaPrincipal');");

$obFormulario->defineBarra              ( array( $obBtnCancelar,$obBtnFiltro ) , '', ''     );

$obFormulario->show();
?>
