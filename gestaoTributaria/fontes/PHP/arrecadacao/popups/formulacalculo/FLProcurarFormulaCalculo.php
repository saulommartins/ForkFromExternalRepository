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
  * Página de Filtro para popup de FORMULA DE CALCULO
  * Data de criação : 22/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FLProcurarFormulaCalculo.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

/*
$Log$
Revision 1.5  2006/09/15 11:51:01  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:50:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarFormulaCalculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//destroi arrays de sessao que armazenam os dados do FILTRO
//unset( $sessao->filtro );
//unset( $sessao->link );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "stCodFuncao" );
$obTxtCodigo->setSize      ( 10 );
$obTxtCodigo->setRotulo    ( "Código" );

$obTxtFuncao = new TextBox;
$obTxtFuncao->setName      ( "stFuncao"   );
$obTxtFuncao->setSize      ( 40 );
$obTxtFuncao->setRotulo    ( "Função" );

$obCmbBiblioteca = new Select;
$obCmbBiblioteca->setName      ( "inCodBiblioteca"   );
$obCmbBiblioteca->setSize      ( 10 );
$obCmbBiblioteca->setRotulo    ( "Biblioteca" );

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("0");

$obIFrame2 = new IFrame;
$obIFrame2->setName("telaMensagem");
$obIFrame2->setWidth("100%");
$obIFrame2->setHeight("50");

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden             ( $obHdnCtrl           );
$obFormulario->addHidden             ( $obHdnAcao           );
$obFormulario->addHidden             ( $obHdnCampoNom       );
$obFormulario->addHidden             ( $obHdnCampoNum       );
$obFormulario->addTitulo             ( "Dados para filtro"  );
$obFormulario->addComponente         ( $obTxtCodigo         );
$obFormulario->addComponente         ( $obTxtFuncao         );
$obFormulario->addComponente         ( $obCmbBiblioteca     );
$obFormulario->OK();
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();
