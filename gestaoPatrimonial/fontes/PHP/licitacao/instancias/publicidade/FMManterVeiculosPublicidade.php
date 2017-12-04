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
    * Página de Formulário para cadastro de veículos de publicidade
    * Data de Criação   : 22/09/2006

    * @author Leandro André Zis

    * Casos de uso : uc-03.05.11

    $Id: FMManterVeiculosPublicidade.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoTipoVeiculosPublicidade.class.php");

$stPrograma = "ManterVeiculosPublicidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);

$obTTipoVeiculosPublicidade = new TLicitacaoTipoVeiculosPublicidade();
$obTTipoVeiculosPublicidade->recuperaTodos($rsTipoVeiculoPublicidade);

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction  ( $pgProc );
$obForm->setTarget  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbTipoVeiculoPublicidade = new TextBoxSelect;
$obCmbTipoVeiculoPublicidade->setRotulo               ( "Tipo do Veículo de Publicidade");
$obCmbTipoVeiculoPublicidade->setTitle                ( "Informe o tipo de veículo de publicidade.");
$obCmbTipoVeiculoPublicidade->setName                 ('inCodTipoVeiculoPublicidade');
$obCmbTipoVeiculoPublicidade->setNull                 ( false                          );
$obCmbTipoVeiculoPublicidade->obTextBox->setName      ('inCodTipoVeiculoPublicidadeTxt');
$obCmbTipoVeiculoPublicidade->obTextBox->setId        ('inCodTipoVeiculoPublicidadeTxt');
$obCmbTipoVeiculoPublicidade->obTextBox->setSize      ( 8                              );
$obCmbTipoVeiculoPublicidade->obTextBox->setMaxLength ( 10                             );
$obCmbTipoVeiculoPublicidade->obSelect->setName       ('inCodTipoVeiculoPublicidade');
$obCmbTipoVeiculoPublicidade->obSelect->setId         ('inCodTipoVeiculoPublicidade');
$obCmbTipoVeiculoPublicidade->obSelect->setCampoID    ( "cod_tipo_veiculos_publicidade" );
$obCmbTipoVeiculoPublicidade->obSelect->setCampoDesc  ( "descricao"                     );
$obCmbTipoVeiculoPublicidade->obSelect->addOption     ( "", "Selecione"                 );
$obCmbTipoVeiculoPublicidade->obSelect->preencheCombo ( $rsTipoVeiculoPublicidade       );
$obCmbTipoVeiculoPublicidade->obSelect->setStyle      ( "width: 200px"                  );

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTitle("Selecione o CGM do veículo de publicidade.");

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                   );
$obFormulario->setAjuda         ("UC-03.05.11");
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addTitulo        ( "Dados para Inclusão de Veículo de Publicidade"   );
$obFormulario->addComponente    ( $obCmbTipoVeiculoPublicidade );
$obFormulario->addComponente    ( $obIPopUpCGM );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
