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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"  );

$obRRelatorio = new RRelatorio;

$obRRelatorio->setNumCGM( Sessao::read('numCgm') );
$obErro = $obRRelatorio->listarImpressoraUsuario( $rsRecordSet );

// Recupera Impressora Padrão
$rsImpPadrao = new RecordSet;
$obRRelatorio->consultarImpressoraPadrao( $rsImpPadrao );
$stFilaImpressao = $obRRelatorio->getFilaImpressao();
//$stNomImpressora = $obRRelatorio->getNomImpressora();

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "boCtrl" );
$obHdnCtrl->setValue ( "true" );

$obCmbImpressora = new Select;
$obCmbImpressora->setRotulo     ( "Impressora"          );
$obCmbImpressora->setName       ( "stFilaImpressao"     );
$obCmbImpressora->setValue      ( $stFilaImpressao      );
$obCmbImpressora->setStyle      ( "width: 200px"        );
$obCmbImpressora->setCampoId    ( "fila_impressao"      );
$obCmbImpressora->setCampoDesc  ( "nom_impressora"      );
$obCmbImpressora->addOption     ( "","Impressora Local" );
//$obCmbImpressora->addOption     ( $stFilaImpressao,$stNomImpressora );
$obCmbImpressora->preencheCombo ( $rsRecordSet          );

$obTxtNumCopias = new TextBox;
$obTxtNumCopias->setName        ( "inNumCopias"      );
$obTxtNumCopias->setRotulo      ( "Número de cópias" );
$obTxtNumCopias->setValue       ( "1" );
$obTxtNumCopias->setSize        ( 3 );
$obTxtNumCopias->setMaxLength   ( 3 );

$obForm = new Form;
$obForm->setAction( "frame.php" );
$obForm->setTarget( "relatorio" );

$obFormulario = new Formulario;
$obFormulario->setForm  ( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Selecione a impressora e o número de cópias para a impressão do relatório" );
$obFormulario->addComponente( $obCmbImpressora );
$obFormulario->addComponente( $obTxtNumCopias  );
$obFormulario->OK();
$obFormulario->show();
?>
