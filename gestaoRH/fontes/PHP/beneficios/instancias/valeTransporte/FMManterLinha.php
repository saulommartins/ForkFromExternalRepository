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
* Página de formulário para inclusão/alteração de linha
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2006-08-18 16:32:22 -0300 (Sex, 18 Ago 2006) $

* Casos de uso: uc-04.06.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

SistemaLegado::exibirAjuda( "../../../../../Manuais/HTML/beneficios/UC-04.06.02/manUC-04.06.02.html" );

$stPrograma = "ManterLinha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($_GET['inCodLinha']) {
    $inCodLinha = $_GET['inCodLinha'];
}
if ($_GET['stDescricaoLinha']) {
    $stDescricaoLinha = $_GET['stDescricaoLinha'];
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodLinha = new Hidden;
$obHdnCodLinha->setName( "inCodLinha" );
$obHdnCodLinha->setValue( $inCodLinha );

//Define objeto TEXTBOX para armazenar a DESCRICAO DA LINHA
$obTxtLinha= new TextBox;
$obTxtLinha->setRotulo        ( "Linha"                 );
$obTxtLinha->setTitle         ( "Descrição da Linha"    );
$obTxtLinha->setName          ( "stDescricaoLinha"      );
$obTxtLinha->setId            ( "stDescricaoLinha"      );
$obTxtLinha->setValue         ( trim($stDescricaoLinha) );
$obTxtLinha->setSize          ( 40                      );
$obTxtLinha->setMaxLength     ( 80                      );
$obTxtLinha->setNull          ( false                   );
$obTxtLinha->setEspacosExtras ( false                   );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                   );
$obFormulario->addHidden            ( $obHdnAcao                );
$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnCodLinha            );
$obFormulario->addTitulo            ( "Descrição da Linha"      );
$obFormulario->addComponente        ( $obTxtLinha               );
$obFormulario->setFormFocus         ( $obTxtLinha->getId()      );
if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
