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
    * Data de Criação: 09/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLManterRegistroEvento.php 66393 2016-08-23 18:52:42Z michel $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEvento";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId();
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId();
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar($boTransacao);
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao"            );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl"            );
$obHdnCtrl->setValue( $_REQUEST["stStrl"] );

$obHdnCampoNum =  new Hidden;
$obHdnCampoNum->setName ( "campoNum"            );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obHdnCampoNom =  new Hidden;
$obHdnCampoNom->setName ( "campoNom"            );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnNomForm =  new Hidden;
$obHdnNomForm->setName ( "nomForm"            );
$obHdnNomForm->setValue( $_REQUEST["nomForm"] );

$obHdnEventoSistema =  new Hidden;
$obHdnEventoSistema->setName ( "boEventoSistema"            );
$obHdnEventoSistema->setValue( $_REQUEST['boEventoSistema'] );

$obHdnEventoBase =  new Hidden;
$obHdnEventoBase->setName ( "boEventoBase"            );
$obHdnEventoBase->setValue( $_REQUEST['boEventoBase'] );

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
$obTxtCodigo->setMascara                        ( $stMascaraEvento                                      );
$obTxtCodigo->setPreencheComZeros               ( 'E'                                                   );

$obTxtDescricao= new TextBox;
$obTxtDescricao->setRotulo                      ( "Descrição"                                           );
$obTxtDescricao->setTitle                       ( "Informe a descrição do evento."                      );
$obTxtDescricao->setName                        ( "stDescricao"                                         );
$obTxtDescricao->setValue                       ( $stDescricao                                          );
$obTxtDescricao->setMaxLength                   ( 80                                                    );
$obTxtDescricao->setSize                        ( 40                                                    );
$obTxtDescricao->setNull                        ( true                                                  );

$obRdnNaturezaProventos = new Radio();
$obRdnNaturezaProventos->setName                ( "stNatureza"                                          );
$obRdnNaturezaProventos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaProventos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaProventos->setLabel               ( "Proventos"                                           );
$obRdnNaturezaProventos->setValue               ( "P"                                                   );
$obRdnNaturezaProventos->setNull                ( false                                                 );
if ($_GET['boBase'] == 'true') {
    $obRdnNaturezaProventos->setDisabled        ( true                                                  );
}
if ( ($stNatureza == "P" or !isset($stNatureza)) and $_GET['boBase'] != 'true' ) {
    $obRdnNaturezaProventos->setChecked         ( true                                                  );
}

$obRdnNaturezaDescontos = new Radio();
$obRdnNaturezaDescontos->setName                ( "stNatureza"                                          );
$obRdnNaturezaDescontos->setRotulo              ( "Natureza"                                            );
$obRdnNaturezaDescontos->setTitle               ( "Selecione a natureza do evento."                     );
$obRdnNaturezaDescontos->setLabel               ( "Descontos"                                           );
$obRdnNaturezaDescontos->setValue               ( "D"                                                   );
$obRdnNaturezaDescontos->setNull                ( false                                                 );
if ($_GET['boBase'] == 'true') {
    $obRdnNaturezaDescontos->setDisabled        ( true                                                  );
}
if ($stNatureza == "D" and $_GET['boBase'] != 'true') {
    $obRdnNaturezaDescontos->setChecked         ( true                                                  );
}

$obRdnNaturezaBase = new Radio();
$obRdnNaturezaBase->setName                     ( "stNatureza"                                          );
$obRdnNaturezaBase->setRotulo                   ( "Natureza"                                            );
$obRdnNaturezaBase->setTitle                    ( "Selecione a natureza do evento."                     );
$obRdnNaturezaBase->setLabel                    ( "Base"                                                );
$obRdnNaturezaBase->setValue                    ( "B"                                                   );
$obRdnNaturezaBase->setNull                     ( false                                                 );
if ($_GET['boBase'] != 'true') {
    $obRdnNaturezaBase->setDisabled             ( true                                                  );
}
if ($stNatureza == "B" or $_GET['boBase'] == 'true') {
    $obRdnNaturezaBase->setChecked              ( true                                                  );
}

$obBtnCancelar = new Button;
$obBtnCancelar->setName                 ( 'cancelar'        );
$obBtnCancelar->setValue                ( 'Cancelar'        );
$obBtnCancelar->obEvento->setOnClick    ( "window.close();" );

$obBtnOk = new ok;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm                                               );
$obFormulario->addTitulo         ( "Dados para o Filtro"                                 );
$obFormulario->addHidden         ( $obHdnAcao                                            );
$obFormulario->addHidden         ( $obHdnCtrl                                            );
$obFormulario->addHidden         ( $obHdnCampoNum                                        );
$obFormulario->addHidden         ( $obHdnCampoNom                                        );
$obFormulario->addHidden         ( $obHdnNomForm                                         );
$obFormulario->addHidden         ( $obHdnEventoSistema                                   );
$obFormulario->addHidden         ( $obHdnEventoBase                                      );
$obFormulario->addComponente     ( $obTxtCodigo                                          );
$obFormulario->addComponente     ( $obTxtDescricao                                       );
$obFormulario->agrupaComponentes ( array($obRdnNaturezaProventos,$obRdnNaturezaDescontos,$obRdnNaturezaBase));
$obFormulario->defineBarra       ( array($obBtnOk,$obBtnCancelar)                        );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
