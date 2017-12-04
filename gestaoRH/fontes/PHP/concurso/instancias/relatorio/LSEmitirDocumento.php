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
* Página de Listagem de Documentos
* Data de Criação   : 05/04/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/administracao/classes/negocio/RDocumentoDinamicoDocumento.class.php';
include_once ( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "EmitirDocumento";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = "../modulos/configuracao/documentoDinamico/";

unset($sessao->transf1);
unset($sessao->transf2);
unset($sessao->transf3);

$obRConcursoCandidato = new RConcursoCandidato;
$rsListaCargos = $rsListaCandidatos  = new RecordSet;

$sessao->transf1 = $_REQUEST['nuDocumento'];

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$obRConcursoCandidato->obRConcursoConcurso->setCodEdital($_REQUEST['nuEdital']);

$inClaInicial = $_REQUEST['inClaInicial'];
$inClaFinal   = $_REQUEST['inClaFinal'];

//CALCULA LIMIT E OFFSET PARA FILTRO POR CLASSIFICACAO
if ($inClaInicial != '') {
    $inOffset = $inClaInicial - 1;
    $inLimit = ($inClaFinal != '') ? $inClaFinal - $inClaInicial + 1 : 'ALL';
} else {
    $inOffset = 0;
    $inLimit = ($inClaFinal != '') ?  $inClaFinal : 'ALL';
}

$stOrder = ' LIMIT '.$inLimit.' OFFSET '.$inOffset.' ';

if ($_REQUEST['inNumCGM'] != '') {
    $stFiltro .="And cc.numcgm <= ".$_REQUEST['inNumCGM'];
}
$stFiltro .= " AND t.media >= c.nota_minima";
$obRConcursoCandidato->listarCandidatoPorEdital($rsListaCandidatos,$stFiltro,$stOrder);

$sessao->transf2 = $rsListaCandidatos;

$stFiltro = " where CCA.cod_edital=".$obRConcursoCandidato->obRConcursoConcurso->getCodEdital()." ";
$obRConcursoCandidato->obTConcursoConcursoCandidato->recuperaRelacionamento($rsListaCargos,$stFiltro);
$sessao->transf3 = $rsListaCargos;

$obLista = new Lista;
$obLista->setRecordSet( $rsListaCandidatos );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome do Candidato" );
$obLista->ultimoCabecalho->setWidth( 85 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->show();

if ($rsListaCandidatos->getCampo('numcgm') != '') {
    $obBtnImprimir = new Ok;
    $obBtnImprimir->setName              ( "btImprimir"      );
    $obBtnImprimir->setValue             ( "Ok" );

    $obForm = new Form;
    $obForm->setAction( "../../../popups/popups/relatorio/OCRelatorio.php" );
    $obForm->setTarget( "oculto" );

    $obHdnCaminho = new Hidden;
    $obHdnCaminho->setName("stCaminho");
    $obHdnCaminho->setValue( "../../../modulos/concurso/relatorio/OCEmitirDocumento.php" );

    $obFormulario = new Formulario;
    $obFormulario->addForm      ( $obForm );
    $obFormulario->addHidden    ( $obHdnCaminho );
    $obFormulario->addTitulo    ( "Imprimir Documentos" );
    $obFormulario->definebarra(array($obBtnImprimir),"");

    $obFormulario->show();
}

?>
