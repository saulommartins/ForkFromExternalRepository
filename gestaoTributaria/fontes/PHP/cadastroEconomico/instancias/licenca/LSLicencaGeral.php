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
    * Página de Listagem de para Alterar Elementos da Licenca, e Alteracao de Licenca
    * Data de Criação   : 29/04/2005

    * @author Lucas Teixeira Stephanou

    * @ignore

    * $Id: LSLicencaGeral.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.10  2006/12/04 10:52:56  cercato
Bug #7534#

Revision 1.9  2006/11/23 11:23:08  cercato
Bug #7536#

Revision 1.8  2006/10/11 10:08:15  dibueno
Adaptando receber cod_licenca de acordo com a configuração do módulo

Revision 1.7  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_CEM_NEGOCIO."RCEMLicencaDiversa.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "AlterarLicencaGeral";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgElem = "FMAlterarLicencaGeral.php";
//$pgForm = "FMAlterarLicencaGeralTipo.php";
$pgForm = "PRConcederLicencaGeral2.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//$stCaminho = "../modulos/cadastroEconomico/hierarqativ/";

 //Define arquivos PHP para cada acao
$stAcao = $request->get('stAcao');
switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgForm; break;
    case 'elemento'  : $pgProx = $pgElem; break;
    DEFAULT          : $pgProx = $pgForm;
}

 //MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
 $stLink .= "&stAcao=".$stAcao;
 if ($_GET["pg"] and  $_GET["pos"]) {
     $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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

Sessao::write( "link", $link );
$obRegra = new RCEMLicencaDiversa;
$rsLista = new RecordSet;
// SETAR POSSIVEIS FILTROS

if ($_REQUEST["inCGM"]) {
    $obRegra->obRCGM->setNumCGM($_REQUEST["inCGM"]);
}
if ($_REQUEST["stLicenca"]) {

    $stTipoLicenca = $_REQUEST["stTipoLicenca"];

    $inCodigoLicenca = $_REQUEST["stLicenca"];

    if ($stTipoLicenca != 0) {
        $arLicenca = explode ( '/', $_REQUEST["stLicenca"] );
        $inCodigoLicenca 	= $arLicenca[0];
        $inExercicio 		= $arLicenca[1];
    }

    $obRegra->setCodigoLicenca ( $inCodigoLicenca   );

    if ( $inExercicio )
        $obRegra->setExercicio ( $inExercicio );
}

if ( Sessao::read('acao') == 554 ) {
    $stFiltro = " cod_tipo_diversa in (select cod_tipo from economico.tipo_licenca_diversa where cod_utilizacao = 1) AND  ";
}
$obRegra->listarLicencas( $rsLista, $stFiltro );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Licença" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo da Licença" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_licenca" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addAcao();
//$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setAcao( $stAcao);
$obLista->ultimaAcao->addCampo( "&inCodigoLicenca"  , "cod_licenca"         );
$obLista->ultimaAcao->addCampo( "&stExercicio"      , "exercicio"           );
$obLista->ultimaAcao->addCampo( "&inNumCGM"         , "numcgm"              );
$obLista->ultimaAcao->addCampo( "&stNomeCGM"        , "nom_cgm"             );
$obLista->ultimaAcao->addCampo( "&inCodigoTipo"     , "cod_tipo_diversa"    );
$obLista->ultimaAcao->addCampo( "&stNomTipo"        , "nom_tipo"            );
$obLista->ultimaAcao->addCampo( "&dtInicio"         , "dt_inicio"           );
$obLista->ultimaAcao->addCampo( "&dtTermino"        , "dt_termino"          );
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.12" );
$obFormulario->show();

?>
