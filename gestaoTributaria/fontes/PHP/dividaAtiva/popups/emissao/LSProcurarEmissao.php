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
  * Página de Lista de Emissao
  * Data de criação : 26/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSProcurarEmissao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.03
**/

/*
$Log$
Revision 1.2  2007/02/27 19:54:37  cercato
alteracoes em funcao das modificacoes nas tabelas do banco de dados.

Revision 1.1  2006/09/29 10:50:59  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarEmissao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//MANTEM FILTRO E PAGINACAO
$link   = Sessao::read('linkPopUp'  );
$stLink = Sessao::read('stLinkPopUp');

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$_REQUEST['stAcao'];

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
$stLink .= "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm");
$stLink .= "&stNomCampoCombo=".$request->get("stNomCampoCombo");

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('linkPopUp', $link);
Sessao::write('stLinkPopUp', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = '';
if ($_REQUEST['stInscricao']) {
    $stFiltro .= " \n ddp.cod_inscricao = ".$_REQUEST['stInscricao']." AND ";
}

if ($_REQUEST['inCodTipoDocumento']) {
    $stFiltro .= " \n ddd.cod_tipo_documento = ".$_REQUEST['inCodTipoDocumento']." AND ";
}

if ($_REQUEST['stNome']) {
    $stFiltro .= " \n amd.nome_documento iLIKE '%".$_REQUEST['stNome']."%' AND ";
}

if ($_REQUEST['stExercicio']) {
    $stFiltro .= " \n ddp.exercicio = '".$_REQUEST['stExercicio']."' AND ";
}

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
}

$obTDATDividaDocumento = new TDATDividaDocumento;
$obTDATDividaDocumento->recuperaListaDocumentoPopUp( $rsDocumento, $stFiltro, " ORDER BY ddp.cod_inscricao " );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsDocumento );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Documento" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_inscricao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_documento] - [nome_documento]" );
$obLista->commitDado();

$stAcao = "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );

if ($request->get('stNomCampoCombo')) {
    $obLista->ultimaAcao->setLink( "JavaScript:InsereTipoDoc();" );
    $obLista->ultimaAcao->addCampo("1","num_documento");
    $obLista->ultimaAcao->addCampo("2","nome_documento");
    $obLista->ultimaAcao->addCampo("3","cod_tipo_documento");
} else {
    $obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
    $obLista->ultimaAcao->addCampo("1","num_documento");
    $obLista->ultimaAcao->addCampo("2","nome_documento");
}
$obLista->commitAcao();

$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get('campoNom') );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get('campoNum') );

$obHdnNomCampoCombo = new Hidden;
$obHdnNomCampoCombo->setName( "stNomCampoCombo" );
$obHdnNomCampoCombo->setValue( $request->get('stNomCampoCombo') );

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
$obFormulario->addHidden($obHdnNomCampoCombo);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();

include_once ( $pgJS );
