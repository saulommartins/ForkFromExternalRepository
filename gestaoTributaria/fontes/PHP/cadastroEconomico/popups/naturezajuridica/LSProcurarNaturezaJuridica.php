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
    * Página de Lista do Popup para Natureza JuridicaR
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    *
    * @ignore

    * $Id: LSProcurarNaturezaJuridica.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08
*/

/*
$Log$
Revision 1.8  2006/09/15 13:50:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarNaturezaJuridica";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$obRCEMNaturezaJuridica = new RCEMNaturezaJuridica;

$stFiltro = "";
$stLink .= "&stAcao=".$stAcao;

if ($_REQUEST["inCodigoNatureza"]) {
    $arTmpCodigo = explode("-", $_REQUEST['inCodigoNatureza']);
    $obRCEMNaturezaJuridica->setCodigoNatureza( $arTmpCodigo[0].$arTmpCodigo[1] );
}
if ($_REQUEST["stNomeNatureza"]) {
    $obRCEMNaturezaJuridica->setNomeNatureza( $_REQUEST["stNomeNatureza"] );
}

$obRCEMNaturezaJuridica->listarNaturezaJuridica( $rsLista );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_natureza" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_natureza" );
$obLista->commitDado();

$obLista->addAcao();
$stAcao = "selecionar";
$obLista->ultimaAcao->setAcao   ( $stAcao );
$obLista->ultimaAcao->setFuncao ( true );
$obLista->ultimaAcao->addCampo  ( "&inCodigoLogradouro", "cod_natureza" );
$obLista->ultimaAcao->addCampo  ( "&stNomeLogradouro"  , "nom_natureza" );
$obLista->ultimaAcao->setLink   ("Javascript:preencheCampos()");
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
