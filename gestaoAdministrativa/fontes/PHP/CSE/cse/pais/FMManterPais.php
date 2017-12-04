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
/*
Arquivo de instância para manutenção de países
* Data de Criação: 15/06/2007

* @author Analista     : Fabio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterPais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stLocation = $pgList."?".$sessao->id."&stAcao=".$_REQUEST['stAcao'].$stFiltro;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""       );

if ($_REQUEST['stAcao']=="alterar") {
$obHdnCodPais = new Hidden;
$obHdnCodPais->setName ( "inCodPais"            );
$obHdnCodPais->setValue( $_REQUEST['inCodPais'] );
}

$obTxtNome = new TextBox;
$obTxtNome->setRotulo   ( "Nome"                    );
$obTxtNome->setTitle    ( "Informe o nome do país." );
$obTxtNome->setName     ( "stNome"                  );
$obTxtNome->setNull     ( false                     );
$obTxtNome->setSize     ( 20                        );
$obTxtNome->setMaxLength( 20                        );
if (isset($_REQUEST['stNome'])) { $obTxtNome->setValue($_REQUEST['stNome']); }

$obTxtNacionalidade = new TextBox;
$obTxtNacionalidade->setRotulo   ( "Nacionalidade"                              );
$obTxtNacionalidade->setTitle    ( "Informe a nacionalidade referente ao país." );
$obTxtNacionalidade->setName     ( "stNacionalidade"                            );
$obTxtNacionalidade->setNull     ( false                                        );
$obTxtNacionalidade->setSize     ( 80                                           );
$obTxtNacionalidade->setMaxLength( 80                                           );
if (isset($_REQUEST['stNacionalidade'])) { $obTxtNacionalidade->setValue($_REQUEST['stNacionalidade']); }

$obCodRais = new TextBox;
$obCodRais->setRotulo( "Código RAIS"                        );
$obCodRais->setTitle ( "Informe o código da nacionalidade." );
$obCodRais->setName  ( "inCodRais"                          );
$obCodRais->setNull  ( false                                );
$obCodRais->setSize  ( 10                                   );
if (isset($_REQUEST['inCodRais'])) { $obCodRais->setValue($_REQUEST['inCodRais']); }

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm             );
$obFormulario->addHidden    ( $obHdnAcao          );
$obFormulario->addHidden    ( $obHdnCtrl          );
if ($_REQUEST['stAcao']=='alterar') { $obFormulario->addHidden( $obHdnCodPais ); }
$obFormulario->addComponente( $obTxtNome          );
$obFormulario->addComponente( $obTxtNacionalidade );
$obFormulario->addComponente( $obCodRais          );

if ($stAcao=="incluir") {
    $obFormulario->OK();
} else {
    $stLocation = $pgList.'?'.$sessao->id.'&stAcao='.$stAcao.'&pg='.$_REQUEST['pg'].'&pos='.$_REQUEST['pos'];
    $obFormulario->Cancelar( $stLocation );
}
$obFormulario->show();
?>
