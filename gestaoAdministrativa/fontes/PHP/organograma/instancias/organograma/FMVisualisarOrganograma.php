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
* Arquivo de instância para manutenção de organograma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php");

$stPrograma = "VisualisarOrganograma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsTipoNorma = new RecordSet;
$rsNorma     = new RecordSet;
$obRegra     = new ROrganogramaOrgao;
$obRegra->obROrganograma->setCodOrganograma( $_POST['inCodOrganograma'] );
$obRegra->obROrganograma->consultar();
$stDataImplantacao = $obRegra->obROrganograma->getDtImplantacao();
$obRegra->obVOrgaoNivel->recuperaTodos($rsArvore, " WHERE cod_organograma=".$_POST['inCodOrganograma'] );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obBtnVoltar = new Button;
$obBtnVoltar->setName( "btnVoltar" );
$obBtnVoltar->setValue( "Voltar" );
$obBtnVoltar->setTipo( "submit" );

$obArvore = new Arvore;
$obArvore->setRecordSet( $rsArvore );
$obArvore->setName("orgao");
$obArvore->setValue("[orgao] - [descricao]");
$obArvore->setRotulo( $stDataImplantacao );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Visualização do Organograma" );
$obFormulario->addComponente        ( $obArvore );
$obFormulario->defineBarra          ( array( $obBtnVoltar ) ,'','');

//$obFormulario->OK                   ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
