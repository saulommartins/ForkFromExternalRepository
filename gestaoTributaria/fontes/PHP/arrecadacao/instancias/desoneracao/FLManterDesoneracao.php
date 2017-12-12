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
  * Página de Filtro de Desoneração
  * Data de criação : 01/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: FLManterDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.7  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obRARRDesoneracao = new RARRDesoneracao;
$obRARRDesoneracao->listarTipoDesoneracao( $rsTipo );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obTxtCodigoDesoneracao = new TextBox;
$obTxtCodigoDesoneracao->setTitle       ( "Código da desoneração." );
$obTxtCodigoDesoneracao->setName        ( "inCodigoDesoneracao" );
$obTxtCodigoDesoneracao->setRotulo      ( "Código"              );
$obTxtCodigoDesoneracao->setMaxLength   ( 7                     );
$obTxtCodigoDesoneracao->setSize        ( 7                     );
$obTxtCodigoDesoneracao->setValue       ( $inCodigoDesoneracao  );
$obTxtCodigoDesoneracao->setInteiro     ( true                  );

$obTxtTipo = new TextBox;
$obTxtTipo->setTitle              ( "Tipo da desoneração." );
$obTxtTipo->setName               ( "inCodigoTipo" );
$obTxtTipo->setRotulo             ( "Tipo de Desoneração" );
$obTxtTipo->setMaxLength          ( 7                     );
$obTxtTipo->setSize               ( 7                     );
$obTxtTipo->setValue              ( $inCodigoTipo         );
$obTxtTipo->setInteiro            ( true                  );

$obCmbTipo = new Select;
$obCmbTipo->setName               ( "stTipo"               );
$obCmbTipo->setRotulo             ( "Tipo de desoneração"  );
$obCmbTipo->setCampoId            ( "cod_tipo_desoneracao" );
$obCmbTipo->setCampoDesc          ( "descricao"            );
$obCmbTipo->addOption             ( "", "Selecione"        );
$obCmbTipo->preencheCombo         ( $rsTipo                );

$obBscCredito = new BuscaInner;
$obBscCredito->setTitle            ( "Crédito para o qual a desoneração foi definida." );
$obBscCredito->setRotulo           ( "Crédito"         );
$obBscCredito->setId               ( "stCredito"       );
$obBscCredito->obCampoCod->setName ( "inCodigoCredito" );
$obBscCredito->obCampoCod->setValue( $inCodigoCredito  );
$obBscCredito->obCampoCod->setSize ( 9                 );
$obBscCredito->obCampoCod->obEvento->setOnChange("buscaValor('buscaCredito');");
$obBscCredito->setFuncaoBusca      ( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodigoCredito','stCredito','todos','".Sessao::getId()."','800','550');");

$obBtnOK = new OK;
$obBtnLimpar = new Limpar;

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addTitulo ( "Dados para Filtro" );
$obFormulario->addComponente( $obTxtCodigoDesoneracao );
$obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
$obFormulario->addComponente( $obBscCredito );
$obFormulario->defineBarra( array ( $obBtnOK, $obBtnLimpar ) );
$obFormulario->Show();
