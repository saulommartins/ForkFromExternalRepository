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
    * Página de Formulario para inclusao na tabela arrecadaçaõ tipo pagamento
    * Data de Criação   : 05/12/2005

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Marcelo B. Paulino

    * @ignore

    * $Id: FMManterFechamentoBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.11  2006/09/15 11:50:21  fabio
corrigidas tags de caso de uso

Revision 1.10  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBaixaManual";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSManterFechamentoBaixaManual.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( 'link', "" );

$obRMONAgencia = new RMONAgencia();
$rsBanco = new recordSet();
$rsAgencia = new recordSet();

$obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

//DEFINICAO DOS COMPONENTES
$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setId       ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl  = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obTxtBanco = new TextBox;
$obTxtBanco->setRotulo        ( "*Banco"                           );
$obTxtBanco->setTitle         ( "Banco ao qual a agência pertence." );
$obTxtBanco->setName          ( "inNumbanco"                       );
//$obTxtBanco->setValue         ( $inNumBanco                        );
$obTxtBanco->setSize          ( 10                                 );
$obTxtBanco->setMaxLength     ( 6                                  );
$obTxtBanco->setNull          ( true                               );
$obTxtBanco->setInteiro       ( true                               );
$obTxtBanco->obEvento->setOnChange ( "preencheAgencia('');" );

$obCmbBanco = new Select;
$obCmbBanco->setName          ( "cmbBanco"                   );
$obCmbBanco->addOption        ( "", "Selecione"              );
//$obCmbBanco->setValue         ( $_REQUEST['inNumBanco']      );
$obCmbBanco->setCampoId       ( "num_banco"                  );
$obCmbBanco->setCampoDesc     ( "nom_banco"                  );
$obCmbBanco->preencheCombo    ( $rsBanco                     );
$obCmbBanco->setNull          ( true                         );
$obCmbBanco->setStyle         ( "width: 220px"               );
$obCmbBanco->obEvento->setOnChange ( "preencheAgencia('');"  );

$obTxtAgencia = new TextBox;
$obTxtAgencia->setRotulo        ( "*Agência"                                    );
$obTxtAgencia->setTitle         ( "Agência bancária na qual a conta foi aberta." );
$obTxtAgencia->setName          ( "inNumAgencia"                                );
//$obTxtAgencia->setValue         ( $inNumAgencia                                 );
$obTxtAgencia->setSize          ( 10                                            );
$obTxtAgencia->setMaxLength     ( 6                                             );
$obTxtAgencia->setNull          ( true                                          );
$obTxtAgencia->setInteiro       ( true                                          );

$obCmbAgencia = new Select;
$obCmbAgencia->setName          ( "cmbAgencia"                   );
$obCmbAgencia->addOption        ( "", "Selecione"                );
//$obCmbAgencia->setValue         ( $_REQUEST['inNumAgencia']      );
$obCmbAgencia->setCampoId       ( "num_agencia"                  );
$obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgencia->preencheCombo    ( $rsAgencia                     );
$obCmbAgencia->setNull          ( true                           );
$obCmbAgencia->setStyle         ( "width: 220px"                 );

$obCheckTodosBancos = new CheckBox;
$obCheckTodosBancos->setTitle ( "Informe se deve ser efetuado o fechamento para todos os bancos disponíveis." );
$obCheckTodosBancos->setName ( "inTodosBancos" );
$obCheckTodosBancos->setRotulo ( "Efetuar Fechamento de Todos os Bancos" );
$obCheckTodosBancos->setValue ( "1" );
$obCheckTodosBancos->setNull ( true );
$obCheckTodosBancos->obEvento->setOnClick("habilitaBanco();");

$obBtnConfirma = new Ok; //Button;
//$obBtnConfirma->setName               ( "btnFechamentoBaixaManual" );
//$obBtnConfirma->setValue              ( "Ok" );
//$obBtnConfirma->setTipo               ( "button" );
$obBtnConfirma->obEvento->setOnClick  ( "FechamentoBaixaManual();" );
//$obBtnConfirma->setDisabled           ( false );

$obBtnLimpar = new Limpar;
$arBotoes = array( $obBtnConfirma, $obBtnLimpar );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
//$obForm->setTarget( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addTitulo( "Dados para Baixa Manual de Débito" );
$obFormulario->addForm  ( $obForm          );
$obFormulario->addHidden( $obHdnCtrl       );
$obFormulario->addHidden( $obHdnAcao       );
$obFormulario->addComponenteComposto    ($obTxtBanco,$obCmbBanco      );
$obFormulario->addComponenteComposto    ($obTxtAgencia,$obCmbAgencia  );
$obFormulario->addComponente ( $obCheckTodosBancos );
$obFormulario->defineBarra( $arBotoes );
//$obFormulario->Ok();
$obFormulario->show();
?>
