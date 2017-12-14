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
    * Formulário
    * Data de Criação: 29/06/2016

    * @author Desenvolvedor: Evandro Melos

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ProcurarCnae";
$pgList = "LS".$stPrograma.".php";

Sessao::remove("link");
$stAcao = $request->get('tipoBusca');

$obIFrame = new IFrame;
$obIFrame->setName("oculto");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("50");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "50"          );

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $request->get("campoNum") );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $request->get("campoNom") );

$obTxtCodigoCnae = new TextBox;
$obTxtCodigoCnae->setName    ( "inCodCnae" );
$obTxtCodigoCnae->setId      ( "inCodCnae" );
$obTxtCodigoCnae->setRotulo  ( "Código CNAE Fiscal" );
$obTxtCodigoCnae->setTitle   ( "Informe o Código CNAE Fiscal" );
$obTxtCodigoCnae->setSize    ( 20 );
$obTxtCodigoCnae->setMaxLength ( 20 );

$obTxtDescricaoCNAE = new TextBox;
$obTxtDescricaoCNAE->setName   ( "stDescricaoCNAE" );
$obTxtDescricaoCNAE->setId     ( "stDescricaoCNAE" );
$obTxtDescricaoCNAE->setRotulo ( "Descrição da CNAE Fiscal" );
$obTxtDescricaoCNAE->setTitle  ( "Informe a Descrição da CNAE Fiscal" );
$obTxtDescricaoCNAE->setSize    ( 50 );
$obTxtDescricaoCNAE->setMaxLength ( 60 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo       ( "Dados para Filtro do código da CNAE Fiscal" );
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnCampoNum );
$obFormulario->addHidden       ( $obHdnCampoNom );
$obFormulario->addComponente   ( $obTxtCodigoCnae );
$obFormulario->addComponente   ( $obTxtDescricaoCNAE );

$obFormulario->OK();
$obFormulario->show();
$obIFrame2->show();
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
