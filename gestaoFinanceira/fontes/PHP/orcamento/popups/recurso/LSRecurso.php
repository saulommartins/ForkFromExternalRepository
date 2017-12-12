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
    * Data de Criação   : 29/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2008-02-11 09:36:34 -0200 (Seg, 11 Fev 2008) $

    * Casos de uso: uc-02.01.05
*/

/*
$Log$
Revision 1.6  2006/07/05 20:44:01  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"        );

//Define o nome dos arquivos PHP
$stPrograma = "Recurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stFncJavaScript  = " function insereRecurso(num,nom) {  \n";
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

$obROrcamentoRecurso = new ROrcamentoRecurso;

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

//Monta sessao com os valores do filtro
$arFiltro = Sessao::read('linkPopUp');
if ( is_array($arFiltro) ) {
    $_REQUEST = $arFiltro;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arFiltro[$key] = $valor;
    }
}
Sessao::write('arFiltro',$arFiltro);

$stLink = isset($stLink) ? $stLink : "";

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$request->get('campoNom');
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST['inCodRecurso']) {
    $obROrcamentoRecurso->setCodRecurso( $_REQUEST['inCodRecurso'] );
    $stLink .= '&inCodRecurso='.$_REQUEST['inCodRecurso'];
}
if ($_REQUEST['stDescricao']) {
    $obROrcamentoRecurso->setNome( $_REQUEST['stDescricao'] );
    $stLink .= '&stDescricao='.$_REQUEST['stDescricao'];
}

$stLink .= "&stAcao=".$stAcao;

$stOrder = " ORDER BY exercicio, cod_recurso";
$obROrcamentoRecurso->listar( $rsLista, $stOrder );
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição ");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_fonte" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_recurso" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereRecurso();" );
$obLista->ultimaAcao->addCampo("1","cod_fonte");
$obLista->ultimaAcao->addCampo("2","nom_recurso");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
