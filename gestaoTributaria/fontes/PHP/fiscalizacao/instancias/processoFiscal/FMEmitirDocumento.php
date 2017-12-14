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
    * Página de Formulario que confirma emissão de documentos

    * Data de Criação   : 17/07/2007

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Fellipe Esteves dos Santos
    * @ignore

    * $Id:

    *Casos de uso: uc-05.07.02
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_INSTANCIAS."processoFiscal/JSEmitirDocumento.php" );

$stImg = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/botao_confirma.png";

// Define o nome dos arquivos PHP
$stPrograma = "EmitirDocumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList    	= "LS".$stPrograma.".php";
$pgForm  	= "FM".$stPrograma.".php";
$pgProc   	= "PR".$stPrograma.".php";
$pgOcul   	= "OC".$stPrograma.".php";
$pgJS      	= "JS".$stPrograma.".php";
$stCaminho  = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

$obForm = new Form();
$obForm->setAction( $pgProc );

#### Campos Hidden ####

//Imagem
$obImg = new Img();
$obImg->setWidth( 48 );
$obImg->setHeight( 48 );
$obImg->setCaminho( $stImg );
$obImg->setBorder( 0 );
$obImg->setStyle( "margin-left: 17px; float:left;" );
$obImg->montaHTML();

//Label
$obTitulo = new Label();
$obTitulo->setValue("<b style='margin-top:12px; margin-left: 14px; position:absolute; font-family: Arial, Helvetica, sans-serif; font-size:18px;'>Confirmação</b>");
$obTitulo->montaHTML();

//Observações
$obMensagem = new Textarea;
$obMensagem->setName( "stMensagem" );
$obMensagem->setValue( $_REQUEST['stLabel'] );
$obMensagem->setStyle( "margin:4px 0px 0px 17px; background-color:#E4EAE4; color:#0A5A82; font-family:Tahoma,Arial,Helvetica,sans-serif; font-size:12px; font-weight:bold; float:left;" );
$obMensagem->setNull( true );
$obMensagem->setDisabled( true );
$obMensagem->montaHTML();

//Span
$obSpanImgMensagem = new Span;
$obSpanImgMensagem->setValue( $obImg->getHtml() . $obTitulo->getHtml() . $obMensagem->getHtml() );

//Botões de Sim e Não
$obBtnSim = new Button;
$obBtnSim->setName( "Sim" );
$obBtnSim->setValue( "Sim" );
$obBtnSim->setId( "Sim" );
$obBtnSim->setTipo( "button" );
$obBtnSim->setStyle( "font-size:12px; color:#0A5A82; font-weight:bold; background-color:#E4EAE4; width:100px;" );
$obBtnSim->obEvento->setOnClick( "javascript:submitImpressao(true);" );
$obBtnSim->setDisabled( false );

$obBtnNao = new Button;
$obBtnNao->setName( "Nao" );
$obBtnNao->setValue( "Não" );
$obBtnNao->setTipo( "button" );
$obBtnNao->setStyle( "font-size:12px; color:#0A5A82; font-weight:bold; background-color:#E4EAE4; width:100px;" );
$obBtnNao->obEvento->setOnClick( "javascript:submitImpressao(false);" );
$obBtnNao->setDisabled( false );

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->setClassCampo('');
$obFormulario->setClassCampoE('');
$obFormulario->addForm( $obForm );

//Monta hiddens
foreach ($_REQUEST as $name => $value) {
    if (trim($value) == "Array") {
        foreach ($_REQUEST["$name"] as $index => $value) {
            $obHdn = new Hidden();
            $obHdn->setName( $name."[$index]" );
            $obHdn->setId( $name."[$index]" );
            $obHdn->setValue( $value );
            $obFormulario->addHidden( $obHdn );
        }
    } else {
        $obHdn = new Hidden();
        $obHdn->setName( $name );
        $obHdn->setId( $name );
        $obHdn->setValue( $_REQUEST["$name"] );
        $obFormulario->addHidden( $obHdn );
    }
}

$obFormulario->addSpan( $obSpanImgMensagem );
$obFormulario->defineBarra( array( $obBtnSim, $obBtnNao ), 'center', '' );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
