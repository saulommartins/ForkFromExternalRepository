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
    * Página de Formulário para cadastro de documentos exigidos
    * Data de Criação   : 06/10/2006

    * @author Leandro André Zis

    * @ignore

    * $Id: FLManterContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterTipoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "inCodigo" );
$obTxtCodigo->setId        ( "inCodigo" );
$obTxtCodigo->setRotulo    ( "Código" );
$obTxtCodigo->setTitle     ( "Informe o Código do Tipo de Contrato." );
$obTxtCodigo->setSize      ( 3 );
$obTxtCodigo->setInteiro   ( true );
$obTxtCodigo->setMaxLength ( 3 );

$obTxtSigla = new TextBox;
$obTxtSigla->setName      ( "stSigla" );
$obTxtSigla->setId        ( "stSigla" );
$obTxtSigla->setRotulo    ( "Sigla" );
$obTxtSigla->setTitle     ( "Informe a Sigla do tipo de Contrato." );
$obTxtSigla->setSize      ( 8 );
$obTxtSigla->setMaxLength ( 8 );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setId        ( "stDescricao" );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Informe a Descrição do Tipo de Contrato." );
$obTxtDescricao->setSize      ( 80 );
$obTxtDescricao->setMaxLength ( 80 );

$obTxtCodigoTribunal = new TextBox;
$obTxtCodigoTribunal->setName      ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setId        ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setRotulo    ( "Código Tribunal" );
$obTxtCodigoTribunal->setTitle     ( "Informe o Código do Tipo de Contrato Conforme Orientação do Tribunal de Contas." );
$obTxtCodigoTribunal->setSize      ( 3 );
$obTxtCodigoTribunal->setInteiro   ( true );
$obTxtCodigoTribunal->setMaxLength ( 3 );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->setAjuda         ("UC-03.05.22" );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addTitulo        ( "Dados para Filtro" );
$obFormulario->addComponente    ( $obTxtCodigo );
$obFormulario->addComponente    ( $obTxtSigla );
$obFormulario->addComponente    ( $obTxtDescricao );
$obFormulario->addComponente    ( $obTxtCodigoTribunal );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
