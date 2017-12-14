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
* Arquivo de instância para procura de Lotação
* Data de Criação: 09/07/2007

* @author Analista: Dagiane
* @author Desenvolvedor: Alexandre Melo

Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php" );

//DEFINE O NOME DOS ARQUIVOS PHP
$stPrograma  = "ProcurarLotacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormBaixa = "FM".$stPrograma."Baix.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJs        = "JS".$stPrograma.".js";

$pgProx = $pgList;
Sessao::remove( "link" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName                     ( "stAcao" );
$obHdnAcao->setValue                    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                     ( "stCtrl" );
$obHdnCtrl->setValue                    ( "" );

$obHdnExtensao = new Hidden;
$obHdnExtensao->setName                 ( "stExtensao" );
$obHdnExtensao->setValue                ( $_REQUEST["stExtensao"] );

$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName                 ( "campoNum"            );
$obHdnCampoNum->setValue                ( $_REQUEST["campoNum"] );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName                 ( "campoNom"            );
$obHdnCampoNom->setValue                ( $_REQUEST["campoNom"] );

$inCodOrganograma = $_REQUEST['inCodOrganograma'];
if ($inCodOrganograma == "") {
    $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
    $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
    $inCodOrganograma = $rsOrganogramaVigente->getCampo('cod_organograma');
}

$stFiltro = " AND orgao_nivel.cod_organograma = ".$inCodOrganograma;

$obTOrganogramaOrgao = new TOrganogramaOrgao;
$obTOrganogramaOrgao->setDado('vigencia', date('Y-m-d'));
$obTOrganogramaOrgao->recuperaOrgaos( $rsOrganogramaOrgao, $stFiltro, " LIMIT 1 " );

$stMascLotacao   = strtr  ( $rsOrganogramaOrgao->getCampo('cod_estrutural') , "012345678" , "999999999" );
$inMaxLenLotacao = strlen ( $stMascLotacao );

$obHdnCodOrganograma = new Hidden();
$obHdnCodOrganograma->setName           ( "inCodOrganograma" );
$obHdnCodOrganograma->setValue          ( $inCodOrganograma  );

$obTxtCodigo = new TextBox();
$obTxtCodigo->setRotulo     			("Código Estrutural"			 );
$obTxtCodigo->setTitle      			("Código Estrutural da Lotação." );
$obTxtCodigo->setName       			("inCodigo"						 );
$obTxtCodigo->setMaxLength  			( 20							 );
$obTxtCodigo->setSize       			( 10							 );
$obTxtCodigo->setMaxLength  			( $inMaxLenLotacao               );
$obTxtCodigo->setMascara				( $stMascLotacao                 );

$obTxtFiltro = new TextBox;
$obTxtFiltro->setRotulo                 ( "Descrição"        );
$obTxtFiltro->setTitle                  ( "Informe o filtro." );
$obTxtFiltro->setName                   ( "stDescricao"      );
$obTxtFiltro->setValue                  ( $stDescricao       );
$obTxtFiltro->setSize                   ( 80 );
$obTxtFiltro->setMaxLength              ( 80 );
$obTxtFiltro->setInteiro                ( False );

//INSTANCIA DO FORMULARIO
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm              );
$obFormulario->addHidden                ( $obHdnAcao           );
$obFormulario->addHidden                ( $obHdnCtrl           );
$obFormulario->addHidden                ( $obHdnExtensao       );
$obFormulario->addHidden                ( $obHdnCampoNum       );
$obFormulario->addHidden                ( $obHdnCampoNom       );
$obFormulario->addHidden                ( $obHdnCodOrganograma );
$obFormulario->addTitulo                ( "Dados para Filtro"  );
$obFormulario->addComponente            ( $obTxtCodigo         );
$obFormulario->addComponente            ( $obTxtFiltro         );
$obFormulario->OK                       ();
$obFormulario->show                     ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
