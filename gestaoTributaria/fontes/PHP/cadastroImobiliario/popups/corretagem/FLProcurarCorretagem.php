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
    * Página de filtro para o cadastro de corretagem
    * Data de Criação   : 31/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FLProcurarCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.13
*/

/*
$Log$
Revision 1.7  2006/09/15 15:04:00  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCorretagem";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

//destroi arrays de sessao que armazenam os dados do FILTRO
//unset( $sessao->filtro );
//unset( $sessao->link );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST['stCtrl']  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao']  );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

// DEFINE OBJETOS DO FILTRO ATIVIDADE/INSCRICAO
$obRadioImobiliaria = new Radio;
$obRadioImobiliaria->setName      ( "boTipoCorretagem"   );
$obRadioImobiliaria->setRotulo    ( "Tipo de Corretagem" );
$obRadioImobiliaria->setLabel     ( "Imobiliária"        );
$obRadioImobiliaria->setValue     ( "imobiliaria"        );
$obRadioImobiliaria->setNull      ( false                );
if ($_REQUEST['tipoBusca'] != 'imobiliaria') {
    $obRadioImobiliaria->setChecked( false               );
    $obRadioImobiliaria->setDisabled( true               );
} else {
    $obRadioImobiliaria->setChecked( true                );
    $obRadioImobiliaria->setDisabled( false              );
}

$obRadioCorretor= new Radio;
$obRadioCorretor->setName         ( "boTipoCorretagem"   );
$obRadioCorretor->setLabel        ( "Corretor"           );
$obRadioCorretor->setValue        ( "corretor"           );
$obRadioCorretor->setNull         ( false                );
if ($_REQUEST['tipoBusca'] != 'corretor') {
    $obRadioCorretor->setChecked( false                  );
    $obRadioCorretor->setDisabled( true                  );
} else {
    $obRadioCorretor->setChecked( true                   );
    $obRadioCorretor->setDisabled( false                 );
}

$obRadioTodos = new Radio;
$obRadioTodos->setName            ( "boTipoCorretagem"   );
$obRadioTodos->setLabel           ( "Todos"              );
$obRadioTodos->setValue           ( "todos"              );
$obRadioTodos->setNull            ( false                );
if ($_REQUEST['tipoBusca'] != 'todos') {
    $obRadioTodos->setChecked( false                     );
    $obRadioTodos->setDisabled( true                     );
} else {
    $obRadioTodos->setChecked( true                      );
    $obRadioTodos->setDisabled( false                    );
}

$obTxtRegistroCreci = new TextBox;
$obTxtRegistroCreci->setRotulo    ( "CRECI"                                );
$obTxtRegistroCreci->setTitle     ( "Número do registro no CRECI"          );
$obTxtRegistroCreci->setName      ( "stRegistroCreci"                      );
$obTxtRegistroCreci->setId        ( "stRegistroCreci"                      );
$obTxtRegistroCreci->setSize      ( 10                                     );
$obTxtRegistroCreci->setMaxLength ( 10                                     );
$obTxtRegistroCreci->setNull      ( true                                   );
$obTxtRegistroCreci->obEvento->setOnKeyPress( "return validaCRECI(event);" );

$obTxtCGMCreci = new TextBox;
$obTxtCGMCreci->setRotulo         ( "CGM"                             );
$obTxtCGMCreci->setTitle          ( "CGM da Imobiliária ou Corretor"  );
$obTxtCGMCreci->setName           ( "inCGMCreci"                      );
$obTxtCGMCreci->setSize           ( 10                                );
$obTxtCGMCreci->setMaxLength      ( 10                                );
$obTxtCGMCreci->setNull           ( true                              );
$obTxtCGMCreci->setInteiro        ( true                              );

$obTxtNomeCreci = new TextBox;
$obTxtNomeCreci->setRotulo        ( "Nome"                            );
$obTxtNomeCreci->setTitle         ( "Nome da Imobiliária ou Corretor" );
$obTxtNomeCreci->setName          ( "stNomeCreci"                     );
$obTxtNomeCreci->setMaxLength     ( 200                               );
$obTxtNomeCreci->setNull          ( true                              );
$obTxtNomeCreci->setStyle         ( "width: 340px"                    );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                 );
$obFormulario->addHidden        ( $obHdnCtrl              );
$obFormulario->addHidden        ( $obHdnAcao              );
$obFormulario->addHidden        ( $obHdnForm              );
$obFormulario->addHidden        ( $obHdnCampoNum          );
$obFormulario->addHidden        ( $obHdnCampoNom          );
$obFormulario->addTitulo        ( "Dados para Corretagem" );
$obFormulario->agrupaComponentes( array( $obRadioImobiliaria,$obRadioCorretor,$obRadioTodos ) );
$obFormulario->addComponente    ( $obTxtRegistroCreci     );
$obFormulario->addComponente    ( $obTxtCGMCreci          );
$obFormulario->addComponente    ( $obTxtNomeCreci         );
$obFormulario->setFormFocus     ( $obTxtRegistroCreci->getId() );
$obFormulario->Ok();
$obFormulario->show();
//executaFrameOculto("document.frm.stRegistroCreci.focus();");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
