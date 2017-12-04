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
* Página de formulário para inclusão/alteração da Marca
* Data de Criação: 07/07/2005

* @author Analista:
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso: uc-03.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterMarca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

include_once($pgJs);

if ($_REQUEST['inCodigo']) {
    $inCodLinha = $_GET['inCodigo'];
}

$inCodigo = $_REQUEST['inCodigo'];
$stDescricaoMarca = $_REQUEST['stDescricaoMarca'];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

/*$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );*/

$obHdnCodMarca = new Hidden;
$obHdnCodMarca->setName( "inCodigo" );
$obHdnCodMarca->setValue( $inCodigo );

if ($stAcao == "alterar") {
   $obLblCodMarca = new Label;
   $obLblCodMarca->setRotulo       ( "Código"                          );
   $obLblCodMarca->setId           ( "CodMarca"                        );
   $obLblCodMarca->setName         ( "inCodigo"                      );
   $obLblCodMarca->setValue        ( $inCodigo                       );
}

//Define objeto TEXTBOX para armazenar a DESCRICAO DA MARCA
$obTxtMarca= new TextBox;
$obTxtMarca->setRotulo        ( "Descrição"                 );
$obTxtMarca->setTitle         ( "Informe a descrição da marca."    );
$obTxtMarca->setName          ( "stDescricaoMarca"      );
$obTxtMarca->setId            ( "stDescricaoMarca"      );
$obTxtMarca->setValue         ( stripslashes(stripslashes(htmlentities($stDescricaoMarca       ))));
$obTxtMarca->setSize          ( 50                      );
$obTxtMarca->setMaxLength     ( 80                      );
$obTxtMarca->setNull          ( false                   );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                   );
$obFormulario->setAjuda             ("UC-03.03.03");
$obFormulario->addHidden            ( $obHdnAcao                );
//$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnCodMarca            );
$obFormulario->addTitulo            ( "Dados da Marca"          );
if ($stAcao == "alterar" )
   $obFormulario->addComponente     ( $obLblCodMarca            );
$obFormulario->addComponente        ( $obTxtMarca               );
$obFormulario->setFormFocus         ( $obTxtMarca->getId()      );
if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
     $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
