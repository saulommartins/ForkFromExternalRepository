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
  * Página de Filtro para popup de MOEDA
  * Data de criação : 19/12/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Diego Bueno Coelho

    * $Id: FLProcurarMoeda.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.06
**/

/*
$Log$
Revision 1.4  2006/09/18 08:47:27  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarMoeda";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('link');
Sessao::remove('stLink');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

$obTxtCodMoeda = new TextBox;
$obTxtCodMoeda->setName      ( "inCodMoeda"   );
$obTxtCodMoeda->setTitle     ( 'Codigo da Moeda');
$obTxtCodMoeda->setRotulo    ( "Código" );
$obTxtCodMoeda->setInteiro ( true );
$obTxtCodMoeda->setSize    ( 10 );
$obTxtCodMoeda->setMaxLength ( 10 );
$obTxtCodMoeda->setNull    ( true );

$obTxtDescSingular = new TextBox;
$obTxtDescSingular->setName      ( "stDescSingular"   );
$obTxtDescSingular->setRotulo    ( "Descrição Singular" );
$obTxtDescSingular->setTitle     ( 'Descrição singualar da moeda');
$obTxtDescSingular->setSize      ( 40 );
$obTxtDescSingular->setMaxLength ( 40 );
$obTxtDescSingular->setNull      ( true );

$obTxtFracaoSingular = new TextBox;
$obTxtFracaoSingular->setName      ( "stFracaoSingular"   );
$obTxtFracaoSingular->setRotulo    ( "Fração Singular" );
$obTxtFracaoSingular->setTitle     ( 'Fração singualar da moeda');
$obTxtFracaoSingular->setSize      ( 40 );
$obTxtFracaoSingular->setMaxLength ( 40 );
$obTxtFracaoSingular->setNull      ( true );

$obTxtSimbolo = new TextBox;
$obTxtSimbolo->setName      ( "stSimbolo"   );
$obTxtSimbolo->setRotulo    ( "Símbolo" );
$obTxtSimbolo->setTitle     ( 'Símbolo da moeda');
$obTxtSimbolo->setSize      ( 4 );
$obTxtSimbolo->setMaxLength ( 4 );
$obTxtSimbolo->setNull      ( true );

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
$obFormulario->addComponente         ( $obTxtCodMoeda       );
$obFormulario->addComponente         ( $obTxtDescSingular   );
$obFormulario->addComponente         ( $obTxtFracaoSingular );
$obFormulario->addComponente         ( $obTxtSimbolo        );
$obFormulario->OK();
$obFormulario->show();
$obIFrame->show();
$obIFrame2->show();
