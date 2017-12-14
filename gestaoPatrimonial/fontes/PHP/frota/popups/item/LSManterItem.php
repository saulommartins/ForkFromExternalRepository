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
 * Página de Formulário Almoxarifado
 * Data de Criação   : 28/10/2005

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Er Galvão Abbott

 * @ignore

 * Casos de uso: uc-03.02.12

 $Id: LSManterItem.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaItem.class.php";

$stPrograma = "ManterItem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

# Função para retornar o registro selecionado.
$stFncJavaScript  = " function insereItem(num,nom,uni) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$request->get('nomCampoUnidade')."') ) { \n";
$stFncJavaScript .= "     var sUni = uni; \n";
$stFncJavaScript .= "     window.opener.parent.frames['telaPrincipal'].document.getElementById('".$request->get('nomCampoUnidade')."').innerHTML = sUni; \n";
$stFncJavaScript .= " } \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributos_" );
$obAtributos->recuperaVetor( $arChave    );

$stCaminho = CAM_GP_ALM_INSTANCIAS."catalogo/";

$stAcao = $request->get("stAcao");

if (empty($stAcao)) {
    $stAcao = "alterar";
}

if ($request->get('inCodigo')) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
} else {
    if (Sessao::read('filtro')) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }

    Sessao::write('paginando' , true);
}

$filtro = isset($filtro) ? $filtro : null;

Sessao::write('filtro' , $filtro);

$stFiltro = "";
$stLink   = "";

$rsLista = new RecordSet;

$obTFrotaItem = new TFrotaItem();

if ($request->get('inCodItem') != '') {
    $stFiltro = " AND item.cod_item = ".$request->get('inCodItem')." ";
}

if ($request->get('stHdnDescricao') != '') {
    $stFiltro .= " AND catalogo_item.descricao ILIKE '".$request->get('stHdnDescricao')."' ";
}

if ($request->get('inCodTipo') != '') {
    $stFiltro .= " AND tipo_item.cod_tipo = ".$request->get('inCodTipo')." ";
    $stLink .= "&inCodTipo=".$request->get('inCodTipo');
}

if ($_REQUEST['stTipoConsulta'] == 'sem_combustivel') {
    $stFiltro .= " AND tipo_item.cod_tipo <> 1 ";
    $stLink .= "&stTipoConsulta=".$_REQUEST['stTipoConsulta'];
}

$obTFrotaItem->recuperaItem( $rsLista, $stFiltro );

$stLink .= "&stAcao=".$stAcao;

# Monta o Link para Paginação.
if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}

if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}

if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo do Item no Almoxarifado" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo do Item no Frota" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descricao" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_item" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "desc_tipo_alm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "desc_tipo_frota" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereItem();" );
$obLista->ultimaAcao->addCampo("1","cod_item");
$obLista->ultimaAcao->addCampo("2","descricao");

if ($request->get('nomCampoUnidade')) {
    $obLista->ultimaAcao->addCampo("3","nom_unidade");
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
