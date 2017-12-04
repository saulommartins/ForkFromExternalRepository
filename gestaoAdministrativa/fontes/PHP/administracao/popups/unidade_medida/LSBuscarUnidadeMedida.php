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
 * Arquivo de popup para manutenção de unidades de medidas
 * Data de Criação: 26/08/2008

 *
 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Janilson Mendes P. da Silva

$Revision:
$Name$
$Author:  $
$Date: $

Casos de uso:
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GA_ADM_NEGOCIO."RUnidadeMedidaPopUp.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RUnidadeMedidaPopUp;
$obVUnidadeMedidaPopUp = new VUnidadeMedidaPopUp( $obController );

$stPrograma = "BuscarUnidadeMedida";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

include_once( $pgJs );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
$stNomeUnidade = $_REQUEST['stNomeUnidade'];

$where = $obVUnidadeMedidaPopUp->filtrosUnidadeMedida( $_REQUEST );
$obRsUnidadeMedida = $obVUnidadeMedidaPopUp->recuperarListaUnidade( $where );

if ( empty( $stAcao ) ) {
    $stAcao = "selecionar";
}

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link',$arLink);
}

$stLink .= '&stNomeUnidade='.$stNomeUnidade;
$stLink .= "&stAcao=".$stAcao."&nomForm=".$_REQUEST['nomForm']."&campoNum=".$_REQUEST['campoNum']."&campoNom=".$_REQUEST['campoNom']."&campoNom=".$_REQUEST['campoNom'];

$obLista = new Lista;

$obLista->obPaginacao->setFiltro( "&stLink=".$stLink );

$obRsUnidadeMedida->addStrPad( "cod_unidade", 2 );
$obRsUnidadeMedida->addStrPad( "cod_grandeza", 2 );

$obLista->setRecordSet( $obRsUnidadeMedida );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Retorno" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_unidade].[cod_grandeza]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[simbolo] ([nom_unidade])" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:setaUnidade();" );
$obLista->ultimaAcao->addCampo("1","cod_unidade" );
$obLista->ultimaAcao->addCampo("2","cod_grandeza" );
$obLista->ultimaAcao->addCampo("3","nom_unidade" );
$obLista->ultimaAcao->addCampo("4","simbolo" );
$obLista->commitAcao();

$obLista->show();

$obHdnForm = new Hidden;
$obHdnForm->setName( 'nomForm' );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( 'campoNum' );
$obHdnCampoNum->setValue(  $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( 'campoNom' );
$obHdnCampoNom->setValue(  $_REQUEST['campoNom'] );

$obHdnStNomeUnidade = new Hidden;
$obHdnStNomeUnidade->setName( 'campoNom' );
$obHdnStNomeUnidade->setValue(  $_REQUEST['stNomeUnidade'] );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( 'voltar' );
$obBtnVoltar->setValue( 'Voltar' );
$obBtnVoltar->obEvento->setOnClick( "document.frm.submit();" );

$obBtnFechar = new Button;
$obBtnFechar->setName( 'fechar' );
$obBtnFechar->setValue( 'Fechar' );
$obBtnFechar->obEvento->setOnClick( "window.close();" );

$obForm = new Form;
$obForm->setAction( $pgFilt );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnForm     );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->defineBarra( array( $obBtnVoltar, $obBtnFechar ) , '', '');
$obFormulario->show();

?>
