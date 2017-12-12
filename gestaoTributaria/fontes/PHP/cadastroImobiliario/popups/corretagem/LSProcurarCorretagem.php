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
    * Página de lista para o cadastro de corretagem
    * Data de Criação   : 31/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: LSProcurarCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.8  2006/09/15 15:04:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"  );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCorretor.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
$stLink .= "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&nomForm=".$_REQUEST["nomForm"];
$stLink .= "&boTipoCorretagem=".$_REQUEST["boTipoCorretagem"];

//MANTEM FILTRO E PAGINACAO
//if ($_GET["pg"] and  $_GET["pos"]) {
//    $stLink .= "&pg=".$_GET["pg"];
//    $stLink .= "&pos=".$_GET["pos"];
//}

//DEFINICAO DO FILTRO PARA CONSULTA
if ($_REQUEST["boTipoCorretagem"] == "corretor") {
    $obRCIMCorretor  = new RCIMCorretor;
    if ($_REQUEST["stRegistroCreci"]) {
        $obRCIMCorretor->setRegistroCreci( $_REQUEST["stRegistroCreci"] );
        $stLink .= "&stRegistroCreci=".$_REQUEST["stRegistroCreci"];
    }
    if ($_REQUEST["inCGMCreci"]) {
        $obRCIMCorretor->obRCGMPessoaFisica->setNumCGM( $_REQUEST["inCGMCreci"] );
        $stLink .= "&inCGMCreci=".$_REQUEST["inCGMCreci"];
    }
    if ($_REQUEST["stNomeCreci"]) {
        $obRCIMCorretor->obRCGMPessoaFisica->setNomCGM( $_REQUEST["stNomeCreci"] );
        $stLink .= "&stNomeCreci=".$_REQUEST["stNomeCreci"];
    }
    $obRCIMCorretor->listarCorretores( $rsLista );
} elseif ($_REQUEST["boTipoCorretagem"] == "imobiliaria") {
    $obRCIMImobiliaria  = new RCIMImobiliaria( new RCIMCorretor );
    if ($_REQUEST["stRegistroCreci"]) {
        $obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stRegistroCreci"] );
        $stLink .= "&stRegistroCreci=".$_REQUEST["stRegistroCreci"];
    }
    if ($_REQUEST["inCGMCreci"]) {
        $obRCIMImobiliaria->obRCGMPessoaJuridica->setNumCGM( $_REQUEST["inCGMCreci"] );
        $stLink .= "&inCGMCreci=".$_REQUEST["inCGMCreci"];
    }
    if ($_REQUEST["stNomeCreci"]) {
        $obRCIMImobiliaria->obRCGMPessoaJuridica->setNomCGM( $_REQUEST["stNomeCreci"] );
        $stLink .= "&stNomeCreci=".$_REQUEST["stNomeCreci"];
    }
    $obRCIMImobiliaria->listarImobiliarias( $rsLista );
} elseif ($_REQUEST["boTipoCorretagem"] == "todos") {
    $obRCIMCorretagem  = new RCIMCorretagem;
    if ($_REQUEST["stRegistroCreci"]) {
        $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stRegistroCreci"] );
        $stLink .= "&stRegistroCreci=".$_REQUEST["stRegistroCreci"];
    }
    if ($_REQUEST["inCGMCreci"]) {
        $obRCIMCorretagem->obRCGM->setNumCGM( $_REQUEST["inCGMCreci"] );
        $stLink .= "&inCGMCreci=".$_REQUEST["inCGMCreci"];
    }
    if ($_REQUEST["stNomeCreci"]) {
        $obRCIMCorretagem->obRCGM->setNomCGM( $_REQUEST["stNomeCreci"] );
        $stLink .= "&stNomeCreci=".$_REQUEST["stNomeCreci"];
    }
    $obRCIMCorretagem->listarCorretagem( $rsLista );
}

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CRECI" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 62 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "creci"   );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm"  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addAcao();
$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao  ( $stAcao                );
$obLista->ultimaAcao->setFuncao( true                   );
$obLista->ultimaAcao->setLink  ( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo ( "1","creci"            );
$obLista->ultimaAcao->addCampo ( "2","nom_cgm"          );
$obLista->commitAcao();
$obLista->show();

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST["nomForm"] );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName("tipoBusca");
$obHdnTipoBusca->setValue( $_REQUEST["boTipoCorretagem"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();

?>
