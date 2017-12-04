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
  * Página de Listao para popup de credito
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: LSProcurarCredito.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.10
**/

/*
$Log$
Revision 1.16  2007/08/15 13:03:39  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.15  2007/08/13 18:55:06  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.14  2006/09/29 16:09:31  cercato
adicionada verificacao (testa se existem dados) antes de formatar dados da lista.

Revision 1.13  2006/09/18 08:47:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCredito";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRMONCredito   = new RMONCredito;

$stFiltro = "";
$boTransacao = "";

$stLink = "&stAcao=".$request->get('stAcao');
$stLink .= "&campoNom=".$request->get('campoNom');
$stLink .= "&campoNum=".$request->get('campoNum');
$stLink .= "&nomForm=".$request->get('nomForm');

// FILTRAGEM

if ( isset($_REQUEST["stCodCredito"])&&$_REQUEST['stCodCredito']!='' ) {
    $obRMONCredito->setCodCredito( $_REQUEST["stCodCredito"] );
    $stLink .= "&stCodCredito=".$_REQUEST["stCodCredito"];
}
if ( isset($_REQUEST["stDescCredito"])&&$_REQUEST['stDescCredito']!='' ) {
    $obRMONCredito->setDescricao( $_REQUEST["stDescCredito"] );
    $stLink .= "&stDescCredito=".$_REQUEST["stDescCredito"];
}
if ( isset($_REQUEST["stCodEspecie"])&&$_REQUEST['stCodEspecie']!='' ) {
    $obRMONCredito->setCodEspecie( $_REQUEST["stCodEspecie"] );
    $stLink .= "&stCodEspecie=".$_REQUEST["stCodEspecie"];
}
if ( isset($_REQUEST["stCodGenero"])&&$_REQUEST['stCodGenero']!='' ) {
    $obRMONCredito->setCodGenero( $_REQUEST["stCodGenero"] );
    $stLink .= "&stCodGenero=".$_REQUEST["stCodGenero"];
}
if ( isset($_REQUEST["stCodNatureza"])&&$_REQUEST['stCodNatureza']!='' ) {
    $obRMONCredito->setCodNatureza( $_REQUEST["stCodNatureza"] );
    $stLink .= "&stCodNatureza=".$_REQUEST["stCodNatureza"];
}
if ( isset($_REQUEST["stCodEntidade"])&&$_REQUEST['stCodEntidade']!='' ) {
    $obRMONCredito->setCodEntidade( $_REQUEST["stCodEntidade"] );
    $stLink .= "&stCodEntidade=".$_REQUEST["stCodEntidade"];
}
if ( Sessao::getExercicio() ) {
    $obRMONCredito->setExercicio( Sessao::getExercicio() );
    $stLink .= "&stExercicio=".Sessao::getExercicio();
}
if ( isset($_REQUEST['stTipoReceita'])&&$_REQUEST['stTipoReceita']!='' ) {
    $stLink .= "&stTipoReceita=".$_REQUEST['stTipoReceita'];
    $obRMONCredito->listarCreditosPopUpGF($rsLista, $boTransacao );
} else {
$obRMONCredito->listarCreditosPopUp($rsLista, $boTransacao );
}

$obRMONCredito->consultarMascaraCredito();
$stMascaraCredito = $obRMONCredito->getMascaraCredito();
$arMascaraCredito = explode(".", $stMascaraCredito);
for ($inX=0; $inX<4; $inX++) {
    $arMascaraCredito[$inX] = strlen($arMascaraCredito[$inX]);
}

if ( !$rsLista->Eof() ) {
    $arDados = $rsLista->getElementos();
    for ( $inX=0; $inX<count($arDados); $inX++) {
        $arDados[$inX]["cod_credito"] = sprintf("%0".$arMascaraCredito[0]."d", $arDados[$inX]["cod_credito"]);
        $arDados[$inX]["cod_especie"] = sprintf("%0".$arMascaraCredito[1]."d", $arDados[$inX]["cod_especie"]);
        $arDados[$inX]["cod_genero"] = sprintf("%0".$arMascaraCredito[2]."d", $arDados[$inX]["cod_genero"]);
        $arDados[$inX]["cod_natureza"] = sprintf("%0".$arMascaraCredito[3]."d", $arDados[$inX]["cod_natureza"]);
    }

    $rsLista->preenche( $arDados );
    $rsLista->setPrimeiroElemento();
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Crédito");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição do Crédito" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Espécie" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Genêro" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_especie" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_genero" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_natureza" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("5","cod_credito");
$obLista->ultimaAcao->addCampo("4","cod_especie");
$obLista->ultimaAcao->addCampo("3","cod_genero");
$obLista->ultimaAcao->addCampo("2","cod_natureza");
$obLista->ultimaAcao->addCampo("1","descricao_credito");
$obLista->ultimaAcao->addCampo("6","cod_entidade");
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();
