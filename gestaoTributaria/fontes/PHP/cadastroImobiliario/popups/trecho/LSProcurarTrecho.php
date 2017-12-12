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
    * Página de lista para o cadastro de trecho
    * Data de Criação   : 20/02/2004

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Marcelo Boezzio Paulino
    * @author Desenvolvedor: Alessandro La-Rocca Silveira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

    * @ignore

    * $Id: LSProcurarTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.6  2006/09/15 15:04:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once(CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php");
    include_once(CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php");

    //Define o nome dos arquivos PHP
    $stPrograma = "ProcurarTrecho";
    $pgFilt = "FL".$stPrograma.".php";
    $pgList = "LS".$stPrograma.".php";
    $pgForm = "FM".$stPrograma.".php";
    $pgProc = "PR".$stPrograma.".php";
    $pgOcul = "OCManterLote.php";
    $pgJS   = "JS".$stPrograma.".js";

    include_once($pgJS);

    $obRCIMTrecho = new RCIMTrecho;
    $stFiltro = "";
    $stAcao = $request->get('stAcao');

    $stLink .= "&stAcao=".$stAcao;

    if ($_REQUEST["stNomeLogradouro"]) {
        $obRCIMTrecho->setNomeLogradouro( $_REQUEST["stNomeLogradouro"] );
        $stLink .= "&stNomeLogradouro=".$_REQUEST["stNomeLogradouro"];
    }
    if ($_REQUEST["inCodigoUF"]) {
        $obRCIMTrecho->setCodigoUF( $_REQUEST["inCodigoUF"] );
        $stLink .= "&inCodigoUF=".$_REQUEST["inCodigoUF"];
    }
    if ($_REQUEST["inCodigoMunicipio"]) {
        $obRCIMTrecho->setCodigoMunicipio( $_REQUEST["inCodigoMunicipio"] );
        $stLink .= "&inCodigoMunicipio=".$_REQUEST["inCodigoMunicipio"];
    }
    $obRCIMTrecho->listarTrechos($rsLista, $boTransacao );

    $obLista = new Lista;
    $obLista->obPaginacao->setFiltro("&stLink=".$stLink );

    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código ");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Logradouro" );
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "codigo_sequencia" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "tipo_nome" );
    $obLista->commitDado();

    $obLista->addAcao();

    $stAcao = "SELECIONAR";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
    $obLista->ultimaAcao->addCampo("1","codigo_sequencia");
    $obLista->ultimaAcao->addCampo("2","tipo_nome");
    $obLista->ultimaAcao->addCampo("3","sequencia");
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
