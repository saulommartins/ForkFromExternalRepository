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
    * Página de Listagem de CNAE
    * Data de Criação   : 24/11/2004

    * @author Tonismar Régis Bernardo

    * $Id: LSProcurarCnae.php 59612 2014-09-02 12:00:51Z gelson $

    * @ignore
*/

/*
$Log$
Revision 1.4  2006/09/15 13:46:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_REGRA."RCEMCnae.class.php" );
include_once( CAM_REGRA."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCnae";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once($pgJS);

$obRCEMCnae = new RCEMCnae( new RCEMAtividade );
if ($_REQUEST[ "inCodigoNivel" ]) {
    $obRCEMCnae->setCodigoNivel( $_REQUEST[ "inCodigoNivel" ] );
}

if ($_REQUEST[ "stValorComposto" ]) {
    $obRCEMCnae->setValorComposto( $_REQUEST[ "stValorComposto" ] );
}

if ($_REQUEST[ "stNomeCnae" ]) {
    $obRCEMCnae->setNomeCnae( $_REQUEST[ "stNomeCnae" ] );
}

$obRCEMCnae->listarCnae( $rsListaCnae );

$obLista = new Lista;

$obLista->setRecordSet( $rsListaCnae );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nível" );
$obLista->ultimoCabecalho->setWidth( 20 );
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
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_nivel" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atividade" );
$obLista->commitDado();
$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","cod_cnae");
$obLista->ultimaAcao->addCampo("2","nom_atividade");

$obLista->commitAcao();
$obLista->show();

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
