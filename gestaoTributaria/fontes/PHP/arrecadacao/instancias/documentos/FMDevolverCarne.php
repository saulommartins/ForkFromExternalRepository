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
  * Página de Devolução de Carnê
  * Data de criação : 16/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: FMDevolverCarne.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.7  2006/09/15 11:50:45  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 11:08:05  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgFormDevol = "FMDevolverCarne.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRCarne = new RARRCarne;
$stAcao = $request->get('stAcao');
//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obTxtNumeracao = new Textbox;
$obTxtNumeracao->setName   ( "stNumeracao" );
$obTxtNumeracao->setTitle  ( "Numeração"   );
$obTxtNumeracao->setRotulo ( "*Numeração"   );
$obTxtNumeracao->setNull   ( true          );
$obTxtNumeracao->setSize   ( 20            );
$obTxtNumeracao->setMaxLength( 21          );
$obTxtNumeracao->setValue  ( $_REQUEST["stNumeracao"]  );

$obTxtMotivo = new Textbox;
$obTxtMotivo->setName   ( "inMotivo" );
$obTxtMotivo->setRotulo ( "*Motivo"   );
$obTxtMotivo->setTitle  ( "Motivo"   );
$obTxtMotivo->setValue  ( $_REQUEST["inMotivo"]  );
$obTxtMotivo->setNull   ( true       );

$rsMotivo = new RecordSet;
$obRARRCarne->listarMotivo( $rsMotivo );

$obCmbMotivo = new Select;
$obCmbMotivo->setName       ( "stMotivo"      );
$obCmbMotivo->setRotulo     ( "Motivo"        );
$obCmbMotivo->setCampoId    ( "cod_motivo"    );
$obCmbMotivo->setCampoDesc  ( "descricao"     );
$obCmbMotivo->addOption     ( "", "Selecione" );
$obCmbMotivo->preencheCombo ( $rsMotivo       );

$obBtnIncluirCarne = new Button;
$obBtnIncluirCarne->setName( "stIncluirCarne" );
$obBtnIncluirCarne->setValue( "Incluir" );
$obBtnIncluirCarne->obEvento->setOnClick( "buscaValor('incluirCarne');" );

$obBtnLimparCarne = new Button;
$obBtnLimparCarne->setName( "stLimparCarne" );
$obBtnLimparCarne->setValue( "Limpar" );
$obBtnLimparCarne->obEvento->setOnClick( "buscaValor('limparCarne');" );

$obSpnCarnes = new Span;
$obSpnCarnes->setId   ( "spnCarne" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//$obForm = new Form;
//$obForm->setAction( $pgOcul  );
//$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados para Devolução" );
$obFormulario->addComponente( $obTxtNumeracao );
$obFormulario->addComponenteComposto( $obTxtMotivo, $obCmbMotivo );
$obFormulario->defineBarra  ( array( $obBtnIncluirCarne, $obBtnLimparCarne ), 'left', '' );
$obFormulario->addSpan( $obSpnCarnes );
$obFormulario->Ok();
$obFormulario->Show();
