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

    * Casos de uso : uc-03.05.12
*/

/*
$Log$
Revision 1.3  2006/10/06 15:34:02  leandro.zis
correções

Revision 1.2  2006/10/06 13:46:59  leandro.zis
correção dos botoes da tela de consulta

Revision 1.1  2006/10/06 13:31:00  leandro.zis
uc 03.05.12

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");

$stPrograma = "ManterDocumentosExigidos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'incluir';

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == 'consultar') {
   $obLblNomeDocumento = new Label;
   $obLblNomeDocumento->setRotulo              ( "Nome do Documento" );
   $obLblNomeDocumento->setValue               ( $_REQUEST['stNomDocumento']);
} else {
   $obTxtNomeDocumento = new TextBox;
   $obTxtNomeDocumento->setRotulo               ( "Nome do Documento");
   $obTxtNomeDocumento->setTitle                ( "Informe o nome do documento.");
   $obTxtNomeDocumento->setName                 ('stNomeDocumento');
   $obTxtNomeDocumento->setSize                 ( 40                             );
   $obTxtNomeDocumento->setMaxLength            ( 40                             );
   $obTxtNomeDocumento->setNull                 ( false                          );
}

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.05.12");
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addTitulo        ( "Dados para Inclusão de Documentos Exigidos"   );
if($stAcao == 'consultar')
   $obFormulario->addComponente     ( $obLblNomeDocumento );
else
   $obFormulario->addComponente     ( $obTxtNomeDocumento );

if ($stAcao == 'consultar') {
   $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
   $obButtonVoltar = new Button;
   $obButtonVoltar->setName  ( "Voltar" );
   $obButtonVoltar->setValue ( "Voltar" );
   $obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");
   $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
} else
   $obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
