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
    * Página de lista para popup de Domicilio Fiscal
    * Data de Criação   : 25/07/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: LSProcurarDomicilioFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.6  2006/09/15 13:47:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"  );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"    );
include_once( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarDomicilioFiscal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRCIMLote   = new RCIMLote;
$obRCIMImovel = new RCIMImovel( $obRCIMLote );
$stFiltro = "";

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $_REQUEST["stMascaraLote"] );

$stLink .= "&stAcao=".$_REQUEST["stAcao"];
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&nomForm=".$_REQUEST["nomForm"];

//MANTEM FILTRO E PAGINACAO
//if ($_GET["pg"] and  $_GET["pos"]) {
//    $stLink .= "&pg=".$_GET["pg"];
//    $stLink .= "&pos=".$_GET["pos"];
//}

if ($_REQUEST["inCodImob"]) {
    $obRCIMImovel->setNumeroInscricao( $_REQUEST["inCodImob"] );
    $stLink .= "&inCodImob=".$_REQUEST["inCodImob"];
}
if ($_REQUEST["stNumeroLote"]) {
    $obRCIMImovel->roRCIMLote->setNumeroLote( $_REQUEST["stNumeroLote"] );
    $stLink .= "&stNumeroLote=".$_REQUEST["stNumeroLote"];
}
if ($_REQUEST["stChaveLocalizacao"]) {
    $obRCIMImovel->roRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["stChaveLocalizacao"] );
    $stLink .= "&stChaveLocalizacao=".$_REQUEST["stChaveLocalizacao"];
}
if ($_REQUEST["inCodBairro"]) {
    $obRCIMImovel->obRCIMBairro->setCodigoBairro( $_REQUEST["inCodBairro"] );
    $stLink .= "&inCodBairro=".$_REQUEST["inCodBairro"];
}
if ($_REQUEST["stNomLogradouro"]) {
    $obRCIMImovel->setLogradouro( $_REQUEST["stNomLogradouro"] );
    $stLink .= "&stNomLogradouro=".$_REQUEST["stNomLogradouro"];
}
if ($_REQUEST["inNumero"]) {
    $obRCIMImovel->setNumeroImovel( $_REQUEST["inNumero"] );
    $stLink .= "&inNumero=".$_REQUEST["inNumero"];
}
$obRCIMImovel->addProprietario();
if ($_REQUEST["inNumCGM"]) {
    $obRCIMImovel->roUltimoProprietario->setNumeroCGM( $_REQUEST['inNumCGM'] );
    $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
}
if ($_REQUEST["stNomCGM"]) {
    $obRCIMImovel->roUltimoProprietario->obRCGM->setNomCGM( $_REQUEST['stNomCGM'] );
    $stLink .= "&stNomCGM=".$_REQUEST["stNomCGM"];
}

$obRCIMImovel->listarImoveisConsulta($rsLista, $boTransacao );

$rsLista->addStrPad( "valor", strlen( $_REQUEST["stMascaraLote"] ), "0" );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Localização");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lote" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Imobiliária" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "localizacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->ultimoDado->setCampo( "valor_lote" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","inscricao_municipal");
$obLista->ultimaAcao->addCampo("2","logradouro");
$obLista->ultimaAcao->addCampo("3","numero");
$obLista->ultimaAcao->addCampo("4","complemento");
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
