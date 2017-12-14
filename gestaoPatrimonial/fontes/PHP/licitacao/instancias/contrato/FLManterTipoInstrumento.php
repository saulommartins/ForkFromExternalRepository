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
 * Arquivo de instância para manutenção de tipo instrumento
 * Data de Criação: 26042016
 * @author Analista: Gelson Wolowski Gonçalves 
 * @author Desenvolvedor: Lisiane da Rosa Morais
 *
 * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterTipoInstrumento";
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
$obTxtCodigo->setTitle     ( "Informe o Código do Tipo de Instrumento." );
$obTxtCodigo->setSize      ( 3 );
$obTxtCodigo->setInteiro   ( true );
$obTxtCodigo->setMaxLength ( 3 );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setId        ( "stDescricao" );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Informe a Descrição do Tipo de Instrumento." );
$obTxtDescricao->setSize      ( 100 );
$obTxtDescricao->setMaxLength ( 100 );

$obTxtCodigoTribunal = new TextBox;
$obTxtCodigoTribunal->setName      ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setId        ( "inCodigoTribunal" );
$obTxtCodigoTribunal->setRotulo    ( "Código Tribunal" );
$obTxtCodigoTribunal->setTitle     ( "Informe o Código do Tipo de Instrumento Conforme Orientação do Tribunal de Contas." );
$obTxtCodigoTribunal->setSize      ( 3 );
$obTxtCodigoTribunal->setInteiro   ( true );
$obTxtCodigoTribunal->setMaxLength ( 3 );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );
$obFormulario->addTitulo        ( "Dados para Filtro" );
$obFormulario->addComponente    ( $obTxtCodigo );
$obFormulario->addComponente    ( $obTxtDescricao );
$obFormulario->addComponente    ( $obTxtCodigoTribunal );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
