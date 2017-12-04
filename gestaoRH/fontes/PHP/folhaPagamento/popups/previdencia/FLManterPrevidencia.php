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
    * Filtro
    * Data de Criação: 04/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30908 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterPrevidencia";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

$stAcao          = $_REQUEST['stAcao'];
$stCtrl          = $_REQUEST['stCtrl'];
$campoNum        = $_REQUEST['campoNum'];
$campoNom        = $_REQUEST['campoNom'];
$nomForm         = $_REQUEST['nomForm'];
$inCodEvento     = $_REQUEST['inCodEvento'];
$stDescricao     = $_REQUEST['stDescricao'];
$boEventoSistema = $_GET['boEventoSistema'] ? "true" : "false";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName                         ( "campoNum"                                            );
$obHdnCampoNum->setValue                        ( $campoNum                                             );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName                         ( "campoNom"                                            );
$obHdnCampoNom->setValue                        ( $campoNom                                             );

$obHdnNomForm =  new Hidden;
$obHdnNomForm->setName                          ( "nomForm"                                             );
$obHdnNomForm->setValue                         ( $nomForm                                              );

$obHdnEventoSistema =  new Hidden;
$obHdnEventoSistema->setName                    ( "boEventoSistema"                                     );
$obHdnEventoSistema->setValue                   ( $boEventoSistema                                      );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgList                                               );

$obTxtCodigo= new TextBox;
$obTxtCodigo->setRotulo                         ( "Código"                                              );
$obTxtCodigo->setTitle                          ( "Informe o código do evento."                         );
$obTxtCodigo->setName                           ( "inCodEvento"                                         );
$obTxtCodigo->setMaxLength                      ( 10                                                    );
$obTxtCodigo->setSize                           ( 10                                                    );
$obTxtCodigo->setInteiro                        ( true                                                  );
$obTxtCodigo->setNull                           ( true                                                  );
$obTxtCodigo->setValue                          ( $inCodEvento                                          );

$obTxtDescricao= new TextBox;
$obTxtDescricao->setRotulo                      ( "Descrição"                                           );
$obTxtDescricao->setTitle                       ( "Informe a descrição do evento."                      );
$obTxtDescricao->setName                        ( "stDescricao"                                         );
$obTxtDescricao->setValue                       ( $stDescricao                                          );
$obTxtDescricao->setMaxLength                   ( 80                                                    );
$obTxtDescricao->setSize                        ( 40                                                    );
$obTxtDescricao->setNull                        ( true                                                  );

$stNatureza = $_GET['stNatureza'];

$obRdnNaturezaProventos = new Radio();
$obRdnNaturezaProventos->setName                ( "stNatureza"                                          );
$obRdnNaturezaProventos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaProventos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaProventos->setLabel               ( "Proventos"                                           );
$obRdnNaturezaProventos->setValue               ( "P"                                                   );
$obRdnNaturezaProventos->setNull                ( false                                                 );
if ( $stNatureza == "P" or !isset($stNatureza) ) {
    $obRdnNaturezaProventos->setChecked         ( true                                                  );
} else {
    $obRdnNaturezaProventos->setDisabled        ( true                                                  );
}

$obRdnNaturezaDescontos = new Radio();
$obRdnNaturezaDescontos->setName                ( "stNatureza"                                          );
$obRdnNaturezaDescontos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaDescontos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaDescontos->setLabel               ( "Descontos"                                           );
$obRdnNaturezaDescontos->setValue               ( "D"                                                   );
$obRdnNaturezaDescontos->setNull                ( false                                                 );
if ($stNatureza == "D") {
    $obRdnNaturezaDescontos->setChecked     ( true                                                  );
} else {
    $obRdnNaturezaDescontos->setDisabled        ( true                                                  );
}

$obRdnNaturezaBase = new Radio();
$obRdnNaturezaBase->setName                     ( "stNatureza"                                          );
$obRdnNaturezaBase->setRotulo                   ( "Natureza"                                            );
$obRdnNaturezaBase->setTitle                    ( "Selecione a natureza do evento."                     );
$obRdnNaturezaBase->setLabel                    ( "Base"                                                );
$obRdnNaturezaBase->setValue                    ( "B"                                                   );
$obRdnNaturezaBase->setNull                     ( false                                                 );
if ($stNatureza == "B") {
    $obRdnNaturezaBase->setChecked              ( true                                                  );
} else {
    $obRdnNaturezaBase->setDisabled             ( true                                                  );
}

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'                                        );
$obBtnCancelar->setValue                ( 'Cancelar'                                        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();"                                 );

$obBtnOk = new ok;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( "Dados para o Filtro"                                 );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                        ( $obHdnCampoNum                                        );
$obFormulario->addHidden                        ( $obHdnCampoNom                                        );
$obFormulario->addHidden                        ( $obHdnNomForm                                         );
$obFormulario->addHidden                        ( $obHdnEventoSistema                                   );
$obFormulario->addComponente                    ( $obTxtCodigo                                          );
$obFormulario->addComponente                    ( $obTxtDescricao                                       );
$obFormulario->agrupaComponentes                ( array($obRdnNaturezaProventos,$obRdnNaturezaDescontos,$obRdnNaturezaBase));
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnCancelar)                        );
//$obFormulario->Cancelar();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
