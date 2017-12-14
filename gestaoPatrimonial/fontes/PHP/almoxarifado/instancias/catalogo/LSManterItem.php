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

    $Id: LSManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.03.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCatalogoItem.class.php");

$stPrograma = "ManterItem";
$pgFormCon  = "FMManterItemConsulta.php";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributos_" );
$obAtributos->recuperaVetor( $arChave    );

$stCaminho = CAM_GP_ALM_INSTANCIAS."catalogo/";

$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ( !Sessao::read('paginando') ) {
    $arFiltro = array();
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('filtro', $arFiltro);
    Sessao::write('pg', $_GET['pg'] ? $_GET['pg'] : 0);
    Sessao::write('pos', $_GET['pos']? $_GET['pos'] : 0);
    Sessao::write('paginando', true);
 } else {
    Sessao::write('pg', $_GET['pg']);
    Sessao::write('pos', $_GET['pos']);
}

$arrayFiltro = Sessao::read('filtro');
    if ($arrayFiltro) {

        foreach ($arrayFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }

$obRegra = new RAlmoxarifadoCatalogoItem;
$stFiltro = "";
$stLink   = "";

$stLink .= "&stAcao=".$stAcao;

$rsLista = new RecordSet;
$obRegra->setCodigo     ( $_REQUEST['inCodItem'] );
$obRegra->setDescricao  ( $_REQUEST['stHdnDescricao'] );
$obRegra->setServico    ( true );  //Para listar também os serviços
$obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo'] );
$obRegra->obRAlmoxarifadoClassificacao->setEstrutural($_REQUEST['stChaveClassificacao']);
$obRegra->obRAlmoxarifadoTipoItem->setCodigo($_REQUEST['inCodTipo']);
$rsCodClassificacao = new RecordSet;
if ($_REQUEST['stChaveClassificacao']) {
   $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao($rsCodClassificacao, $_REQUEST['stChaveClassificacao'], $_REQUEST['inCodCatalogo']);
   $obRegra->obRAlmoxarifadoClassificacao->setCodigo( $rsCodClassificacao->getCampo('cod_classificacao') );
}
//monta array de atributos dinamicos
foreach ($arChave as $key => $value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) ) {
       $value = implode( "," , $value );
    }
    $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}
$obRegra->listar( $rsLista );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Catálogo");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_catalogo] - [desc_catalogo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_estrutural" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_item" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "desc_tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[descricao]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigo"      , "cod_item");
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"[cod_catalogo] - [descricao]");

if ($stAcao == "excluir") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'consultar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormCon."?".Sessao::getId().$stLink."&pg=".$_GET['pg']."&pos=".$_GET['pos']."&stAcao=".$stAcao );
} else {
   $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->setAjuda("UC-03.03.06");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
