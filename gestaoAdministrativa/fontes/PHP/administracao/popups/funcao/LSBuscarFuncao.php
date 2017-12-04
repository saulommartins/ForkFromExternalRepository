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
    * Arquivo de popup para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.03.95

    $Id: LSBuscarFuncao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stPrograma = "BuscarFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "selecionar";
}
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$stLink .= '&stNomeFuncao='.$_REQUEST['stNomeFuncao'];
$stLink .= "&inCodModulo=".$_REQUEST['inCodModulo'];
$stLink .= "&inCodBiblioteca=".$_REQUEST['inCodBiblioteca'];
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

include( $pgJs );

$obRegra = new RFuncao;
$stMascFuncao = $obRegra->obTFuncao->recuperaMascaraFuncao();
$arMascFuncao = explode('.', $stMascFuncao);

$stFiltro = "";

if (isset($_REQUEST['inCodModulo'])) {
    $stFiltro = "and f.cod_modulo='".$_REQUEST['inCodModulo']."' ";
}
if (isset($_REQUEST['inCodBiblioteca'])) {
    $stFiltro .= "and f.cod_biblioteca='".$_REQUEST['inCodBiblioteca']."' ";
}

$rsLista = new RecordSet;
$obRegra->setTipoFuncao( $_REQUEST['tipoBusca'] );
$obRegra->setNomeFuncao( $_REQUEST['stNomeFuncao'] );
$obRegra->listarPorModuloBiblioteca($rsLista,$stFiltro);

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$rsLista->addStrPad( "cod_modulo"    , strlen($arMascFuncao[0]) );
$rsLista->addStrPad( "cod_biblioteca", strlen($arMascFuncao[1]) );
$rsLista->addStrPad( "cod_funcao"    , strlen($arMascFuncao[2]) );

$obLista->setRecordSet( $rsLista );
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
$obLista->ultimoDado->setCampo( "nom_funcao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:setaFuncao();" );
$obLista->ultimaAcao->addCampo("1","cod_modulo"    );
$obLista->ultimaAcao->addCampo("2","cod_biblioteca");
$obLista->ultimaAcao->addCampo("3","cod_funcao"    );
$obLista->ultimaAcao->addCampo("4","nom_funcao");
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

$obHdnCampoFuncao = new Hidden;
$obHdnCampoFuncao->setName ( 'stNomeFuncao' );
$obHdnCampoFuncao->setValue( $_REQUEST['stNomeFuncao'] );

$obHdnInCodBiblioteca = new Hidden;
$obHdnInCodBiblioteca->setName( 'inCodBiblioteca' );
$obHdnInCodBiblioteca->setValue(  $_REQUEST['inCodBiblioteca'] );

$obHdnInCodModulo = new Hidden;
$obHdnInCodModulo->setName( 'inCodModulo' );
$obHdnInCodModulo->setValue(  $_REQUEST['inCodModulo'] );

$obHdnStTipoBusca = new Hidden;
$obHdnStTipoBusca->setName( 'tipoBusca' );
$obHdnStTipoBusca->setValue( $_REQUEST['tipoBusca'] );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( 'voltar' );
$obBtnVoltar->setValue( 'Voltar' );
//$obBtnVoltar->obEvento->setOnClick( "document.frm.submit();" );
$obBtnVoltar->obEvento->setOnClick("Voltar();" );

$obBtnFechar = new Button;
$obBtnFechar->setName( 'fechar' );
$obBtnFechar->setValue( 'Fechar' );
$obBtnFechar->obEvento->setOnClick( "window.close();" );

$obForm = new Form;
$obForm->setAction                  ( $pgFilt );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );

$obFormulario->addHidden            ( $obHdnForm     );
$obFormulario->addHidden            ( $obHdnCampoNum );
$obFormulario->addHidden            ( $obHdnCampoNom );
$obFormulario->addHidden            ( $obHdnInCodBiblioteca);
$obFormulario->addHidden            ( $obHdnInCodModulo );
$obFormulario->addHidden            ( $obHdnStTipoBusca );
$obFormulario->addHidden            ( $obHdnCampoFuncao );

$obFormulario->defineBarra          ( array( $obBtnVoltar, $obBtnFechar ) , '', '');
$obFormulario->show                 ();

?>
