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
    * Página de Filtro Sistema Contábil
    * Data de Criação   : 10/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: FLManterSistemaContabil.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeSistemaContabil.class.php";

$stPrograma = "ManterSistemaContabil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::write('filtro', array());
Sessao::write('paginando', array());

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodSistema = new TextBox;
$obTxtCodSistema->setRotulo        ( "Código" );
$obTxtCodSistema->setTitle         ( "Informe um código de sistema contábil" );
$obTxtCodSistema->setName          ( "inCodSistema" );
$obTxtCodSistema->setValue         ( $inCodSistema );
$obTxtCodSistema->setSize          ( 5 );
$obTxtCodSistema->setMaxLength     ( 5 );
$obTxtCodSistema->setNull          ( true  );
$obTxtCodSistema->setInteiro       ( true  );

$obTxtNomSistema = new TextBox;
$obTxtNomSistema->setRotulo        ( "Descrição" );
$obTxtNomSistema->setTitle         ( "Informe uma descrição de sistema contábil" );
$obTxtNomSistema->setName          ( "stNomSistema" );
$obTxtNomSistema->setValue         ( $stNomSistema );
$obTxtNomSistema->setSize          ( 40 );
$obTxtNomSistema->setMaxLength     ( 80 );
$obTxtNomSistema->setNull          ( true );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda             ('UC-02.02.01');
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addComponente        ( $obTxtCodSistema  );
$obFormulario->addComponente        ( $obTxtNomSistema  );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
