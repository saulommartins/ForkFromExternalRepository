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
* Arquivo de instância para popup de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Norma";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManter".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

$stLink="";

$obRegra = new RNorma;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

//Monta sessao com os valores do filtro
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    $arLinkPopUp = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp',$arLinkPopUp);
}

include_once( $pgJS );

if ($_REQUEST['stExercicio']) {
    $obRegra->setExercicio( $_REQUEST['stExercicio'] );
    $stLink .= '&stExercicio='.$_REQUEST['stExercicio'];
}
if ($_REQUEST['inCodTipoNorma'] != "") {
    $obRegra->obRTipoNorma->setCodTipoNorma( $_REQUEST['inCodTipoNorma'] );
    $stLink .= '&inCodTipoNorma='.$_REQUEST['inCodTipoNorma'];
}

if ($_REQUEST['stNomeNorma']) {
    $obRegra->setNomeNorma( $_REQUEST['stNomeNorma'] );
    $stLink .= '&stNomeNorma='.$_REQUEST['stNomeNorma'];
}

if ($_REQUEST['stDescricaoNorma']) {
    $obRegra->setDescricaoNorma($_REQUEST['stDescricaoNorma'] );
    $stLink .= '&stDescricaoNorma='.$_REQUEST['stDescricaoNorma'];
}

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];
$stLink .= "&boExibeDataNorma=".$_REQUEST['boExibeDataNorma'];
$stLink .= "&boExibeDataPublicacao=".$_REQUEST['boExibeDataPublicacao'];
$stLink .= "&boRetornaNumExercicio=".$_REQUEST['boRetornaNumExercicio'];

$obRegra->listarDecreto( $rsLista );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Norma");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 65 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Publicação");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_norma_exercicio" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_norma" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_publicacao" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );

if ($_REQUEST['boRetornaNumExercicio']) {
   $obLista->ultimaAcao->setLink( "JavaScript:insereNumExercicio();" );
} else {
   $obLista->ultimaAcao->setLink( "JavaScript:insere( '".$_POST["boExibeDataNorma"]."', '".$_POST["boExibeDataPublicacao"]."' );" );
}
$obLista->ultimaAcao->addCampo("1","cod_norma");
$obLista->ultimaAcao->addCampo("2","nom_norma");
$obLista->ultimaAcao->addCampo("3","nom_tipo_norma");
$obLista->ultimaAcao->addCampo("4","num_norma_exercicio");
$obLista->ultimaAcao->addCampo("5","dt_assinatura");
$obLista->ultimaAcao->addCampo("6","dt_publicacao");
$obLista->commitAcao();

$obLista->show();

$obFormulario = new Formulario;

// DEFINE BOTOES
$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"   );
$obBtnIncluir->setValue             ( "Incluir Nova" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir();"   );

$COD_ACAO_INCLUIR_NORMA = 515;
$obTAdministracaoAcao = new TAdministracaoAcao;
$obTAdministracaoAcao->setDado('cod_acao', $COD_ACAO_INCLUIR_NORMA);
$obTAdministracaoAcao->recuperaPermissao($rsPermissaoAcao);
$boUsuarioNaoTemPermissao = $rsPermissaoAcao->getNumLinhas() >0 ? false : true;

$obBtnIncluir->setDisabled          ( $boUsuarioNaoTemPermissao );

$obBtnFiltro = new Button;
$obBtnFiltro->setName                   ( 'filtro'                                          );
$obBtnFiltro->setValue                  ( 'Filtro'                                          );
$obBtnFiltro->obEvento->setOnClick      ( "Cancelar('".$pgFilt.$stLink."','telaPrincipal');"        );

$obFormulario->defineBarra              ( array( $obBtnIncluir,$obBtnFiltro ) , '', ''     );
$obFormulario->show();
