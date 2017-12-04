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
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3574 $
$Name$
$Author: lizandro $
$Date: 2005-12-07 15:23:54 -0200 (Qua, 07 Dez 2005) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma  = "ManterDocumentoDinamico";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJs        = "JS".$stPrograma.".js";

$pgProx = $pgList;

include( $pgJs );

Sessao::write('link','');

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;
$rsRecordSet = new RecordSet;
$obRDocumentoDinamico->obRModulo->listar($rsRecordSet);

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtModulo = new TextBox;
$obTxtModulo->setRotulo    ( "Módulo" );
$obTxtModulo->setTitle     ( "Informe o módulo");
$obTxtModulo->setName      ( "inCodModuloTxt"     );
$obTxtModulo->setValue     ( $inCodModuloTxt);
$obTxtModulo->setSize      ( 6 );
$obTxtModulo->setMaxLength ( 6 );
$obTxtModulo->setNull      ( false );

$obCmbModulo = new Select;
$obCmbModulo->setRotulo     ( "Módulo"        );
$obCmbModulo->setName       ("inCodModulo"    );
$obCmbModulo->setValue      ($inCodModulo     );
$obCmbModulo->setStyle      ( "width: 200px"   );
$obCmbModulo->setCampoID    ("cod_modulo" );
$obCmbModulo->setCampoDesc  ("nom_modulo"     );
$obCmbModulo->addOption     ( "", "Selecione" );
$obCmbModulo->setNull       (false            );
$obCmbModulo->preencheCombo ( $rsRecordSet    );

$obForm = new Form;
$obForm->setAction                  ( $pgProx );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm             );
$obFormulario->addHidden                ( $obHdnAcao          );
$obFormulario->addHidden                ( $obHdnCtrl          );
$obFormulario->addTitulo                ( "Dados para Filtro" );
$obFormulario->addComponenteComposto ( $obTxtModulo, $obCmbModulo) ;
$obFormulario->OK                       ();
$obFormulario->show                     ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
