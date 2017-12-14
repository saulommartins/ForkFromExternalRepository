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
  * Página de Lista de Cobranca
  * Data de criação : 16/04/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSProcurarCobranca.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.02
**/

/*
$Log$
Revision 1.5  2007/08/17 14:10:20  cercato
filtrando inscricoes sem cobranca.

Revision 1.4  2007/08/08 21:43:48  cercato
correcao no filtro da cobranca.

Revision 1.3  2007/07/19 21:02:22  cercato
Bug #9705#

Revision 1.2  2007/07/13 15:37:01  cercato
correcao para funcionar a lista de cobrancas

Revision 1.1  2007/04/16 18:13:46  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once (CAM_FRAMEWORK."/request/Request.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCobranca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
$stLink = "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm");

//MANTEM FILTRO E PAGINACAO
if ( !empty($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = " parcelamento.numero_parcelamento != -1 AND parcelamento.exercicio != '-1' AND "; //inscricao sem cobranca
if ( !empty($_REQUEST['inCodigo']) ) {
    $stFiltro .= " \n divida_ativa.cod_inscricao = '".$_REQUEST['inCodigo']."' AND ";
}

if ( !empty($_REQUEST['stExercicio']) ) {
    $stFiltro .= " \n divida_ativa.exercicio = '".$_REQUEST['stExercicio']."' AND ";
}

if ( !empty($_REQUEST['inCGM'] )) {
    $stFiltro .= " \n divida_cgm.numcgm = '".$_REQUEST['inCGM']."' AND ";
}

if (!empty($_REQUEST['stNome'] )) {
    $stFiltro .= " \n lower(sw_cgm.nom_cgm) like lower('".$_REQUEST['stHdnNome']."') AND ";
}

if ( !empty($_REQUEST['inInscImob'] )) {
    $stFiltro .= " \n divida_imovel.inscricao_municipal = '".$_REQUEST['inInscImob']."' AND ";
}

if (!empty($_REQUEST['inInscEcon'] )) {
    $stFiltro .= " \n divida_empresa.inscricao_economica = '".$_REQUEST['inInscEcon']."' AND ";
}

if ( !empty($stFiltro )) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
}

$obTDATDividaParcelamento = new TDATDividaParcelamento;
$obTDATDividaParcelamento->recuperaListaCobrancaPopUP( $rsDivida, $stFiltro );
//$obTDATDividaParcelamento->debug();
$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsDivida );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contribuinte");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cobrança/Ano" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Imobiliária/Econômica" );
$obLista->ultimoCabecalho->setWidth( 40 );
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
$obLista->ultimoDado->setCampo( "[numero_parcelamento]/[exercicio_cobranca]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_inscricao_imec][descricao_inscricao_imec]" );
$obLista->commitDado();

$_REQUEST['stAcao'] =  "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","numero_parcelamento");
$obLista->ultimaAcao->addCampo("2","exercicio_cobranca");
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

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
