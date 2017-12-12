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
    * Página de lista para o cadastro de imóvel
    * Data de Criação   : 30/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: LSBuscaLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.8  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterImovel";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma."Lote.php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$pgProx = $pgForm;

/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/

$stAcao = $request->get('stAcao');
$link = Sessao::read('link');
//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
$stTipo = $request->get("stTipo");
if ($stTipo == "urbano") {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($stTipo == "rural") {
    $obRCIMLote = new RCIMLoteRural;
}
$stLink = "&stTipo=".$stTipo;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
Sessao::Write('stLink', $stLink);

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );
$obRCIMLocalizacao = new RCIMLocalizacao;
$obRCIMLocalizacao->setValorComposto($_REQUEST["stChaveLocalizacao"]);
$obRCIMLocalizacao->consultaCodigoLocalizacao($inCodigoLocalizacao);
if ($inCodigoLocalizacao) {
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $inCodigoLocalizacao );
}
if ($_REQUEST["stNumeroLote"]) {
    $obRCIMLote->setNumeroLote( $_REQUEST["stNumeroLote"] );
}
$obRCIMLote->listarLotes( $rsListaLote );

$rsListaLote->addStrPad( "valor", strlen( $stMascaraLote ), "0" );
/**
    * InstÃ¢ncia o OBJETO Lista
*/

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaLote );
$obLista->setTitulo ("Registros de lote");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Localização" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lote" );
$obLista->ultimoCabecalho->setWidth( 96 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDado();

$stAcao = "selecionar";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( "selecionar" );
$obLista->ultimaAcao->addCampo ("&inCodigoLote", "cod_lote" );
$obLista->ultimaAcao->addCampo ("&inNumeroLote", "valor" );
$obLista->ultimaAcao->setLink  ( "JavaScript: buscaLote(); " );
$obLista->ultimaAcao->setLink  ( $pgProx."?".Sessao::getId().$stLink."&stTipoLote=".$stTipo );
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.09" );
$obFormulario->show();

?>
