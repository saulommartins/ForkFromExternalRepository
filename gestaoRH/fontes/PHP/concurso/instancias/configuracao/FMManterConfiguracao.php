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
* Página de Formulário Configuração de Concursos
* Data de Criação   : 22/03/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage

$Revision: 30547 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_CON_NEGOCIO."RConfiguracaoConcurso.class.php");

$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRConfiguracaoConcurso = new RConfiguracaoConcurso;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRConfiguracaoConcurso->consultarConfiguracao();
//$boPortariaSequencial     = $obRConfiguracaoConcurso->getPortariaSequencial();
$stMascaraNota      	    = $obRConfiguracaoConcurso->getMascaraNota();
$inCodTipoPortariaEdital    = $obRConfiguracaoConcurso->getTipoPortariaEdital();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obRConfiguracaoConcurso->obRTipoNorma->listarTodos( $rsTipoNormas );

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setName         ( "inTipoNorma" );
$obTxtTipoNorma->setValue        ( $inCodTipoPortariaEdital );
$obTxtTipoNorma->setRotulo       ( "Selecione o tipo de norma para edital" );
$obTxtTipoNorma->setTitle  		 ( "Tipo de norma para edital" );
$obTxtTipoNorma->setSize         ( 10 );
$obTxtTipoNorma->setMaxLength    ( 10 );
$obTxtTipoNorma->setNull         ( false );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setName      ( "inCodTipoNorma" );
$obCmbTipoNorma->setRotulo    ( "Tipo Norma" );
$obCmbTipoNorma->setStyle     ( "width: 250px" );
$obCmbTipoNorma->addOption    ( "", "Selecione" );
$obCmbTipoNorma->setCampoId   ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc ( "nom_tipo_norma" );
$obCmbTipoNorma->setValue     ( $inCodTipoPortariaEdital );
$obCmbTipoNorma->preencheCombo( $rsTipoNormas );
$obCmbTipoNorma->setNull      ( false );
$obCmbTipoNorma->setTitle     ( 'Selecione o tipo de norma para o edital' );

$obTxtMascaraNota = new TextBox;
$obTxtMascaraNota->setName         ( "stMascaraNota" );
$obTxtMascaraNota->setValue        ( $stMascaraNota);
$obTxtMascaraNota->setRotulo       ( "Máscara para Nota" );
$obTxtMascaraNota->setTitle  		( "Informe a máscara para a nota" );
$obTxtMascaraNota->setSize         ( 10 );
$obTxtMascaraNota->setMaxLength    ( 10 );
$obTxtMascaraNota->setNull         ( false );
$obTxtMascaraNota->obEvento->setOnKeyDown("return validaMascara(this,event);");
$obTxtMascaraNota->obEvento->setOnKeyUp("return retiraCaracteres(this,event);");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Configuração" );

$obFormulario->addComponenteComposto( $obTxtTipoNorma, $obCmbTipoNorma);
$obFormulario->addComponente        ( $obTxtMascaraNota );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once($pgJs);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
