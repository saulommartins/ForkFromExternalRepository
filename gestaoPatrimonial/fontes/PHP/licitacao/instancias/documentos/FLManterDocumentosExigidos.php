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
    * Data de Criação   : 22/09/2006

    * @author Leandro André Zis

    * @ignore

    * Casos de uso : uc-03.04.08
*/

/*
$Log$
Revision 1.2  2006/12/20 17:39:17  larocca
Bug #7633#

Revision 1.1  2006/10/06 13:31:00  leandro.zis
uc 03.05.12

Revision 1.1  2006/09/25 09:42:48  leandro.zis
uc 03.11.05

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterDocumentosExigidos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsAtributosDisponiveis = new RecordSet;
$rsAtributosSelecionados = new RecordSet;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obForm = new Form;
$obForm->setAction                  ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtNomeDocumento = new TextBox;
$obTxtNomeDocumento->setRotulo               ( "Nome do Documento");
$obTxtNomeDocumento->setTitle                ( "Informe o nome do documento.");
$obTxtNomeDocumento->setName                 ( "stNomeDocumento" );
$obTxtNomeDocumento->setSize                 ( 40                             );
$obTxtNomeDocumento->setMaxLength            ( 40                             );

$obPeriodicidade = new Periodicidade;
$obPeriodicidade->setExercicio(Sessao::getExercicio());

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.05.12");
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obTxtNomeDocumento );
$obFormulario->addComponente    ( $obPeriodicidade );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
