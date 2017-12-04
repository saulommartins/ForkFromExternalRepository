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
    * Página de lista para o cadastro de face de imóvel
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: LSProcurarImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.8  2006/09/15 15:04:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRCIMLote   = new RCIMLote;
$obRCIMImovel = new RCIMImovel( $obRCIMLote );

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

$stAcao = $_REQUEST["stAcao"];

$stLink = "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&nomForm=".$_REQUEST['nomForm'];

$stFiltro = "";
if ($_REQUEST["inCodImob"] != "") {
    $stFiltro .= " AND imovel.inscricao_municipal = ".$_REQUEST["inCodImob"];
    $stLink .= "&inCodImob=".$_REQUEST["inCodImob"];
}
if ($_REQUEST["stNumeroLote"] != "") {
    $stFiltro .= " AND LPAD( UPPER( lote_localizacao.valor ), 10,'0' ) = LPAD( UPPER('".$_REQUEST["stNumeroLote"]."'), 10,'0' ) ";
    $stLink .= "&stNumeroLote=".$_REQUEST["stNumeroLote"];
}

if ($_REQUEST["stChaveLocalizacao"] != "") {
    $stLink .= "&stChaveLocalizacao=".$_REQUEST["stChaveLocalizacao"];
    $obRCIMLocalizacao = new RCIMLocalizacao;
    $obRCIMLocalizacao->setValorComposto( $_REQUEST["stChaveLocalizacao"] );
    $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
    if ( $rsLocalizacao->getNumLinhas() > 0 ) {
        $stCodigo = $rsLocalizacao->getCampo("cod_localizacao");
        $stFiltro .= " AND localizacao.cod_localizacao = ".$stCodigo;
    }
}

$obTCIMImovel = new TCIMImovel;
$obTCIMImovel->recuperaImovelPopup( $rsLista, $stFiltro );

$rsLista->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Localização");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lote" );
$obLista->ultimoCabecalho->setWidth( 10 );
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
$obLista->ultimoDado->setCampo( "valor_composto" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_municipal" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
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
$obHdnCampoNom->setName ( "campoNom" );
$obHdnCampoNom->setId	( "campoNom" );
$obHdnCampoNom->setValue( $request->get("campoNom") );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName ( "campoNum" );
$obHdnCampoNum->setId	( "campoNum" );
$obHdnCampoNum->setValue( $request->get("campoNum") );

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
