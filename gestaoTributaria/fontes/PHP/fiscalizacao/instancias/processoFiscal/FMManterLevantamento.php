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
    * Página de Formulario Que Verifica o Tipo de Situação do Levantamento

    * Data de Criação   : 17/07/2007

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva
    * @ignore

    * $Id:

    *Casos de uso: uc-05.07.02
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

$stImg = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/botao_confirma.png";

//Define o nome dos arquivos PHP
switch ($_REQUEST['bt_faturamento']) {
    case 'servico':
        $stPrograma = "ManterServico";
    break;

    case 'nota':
        $stPrograma = "ManterNota";
    break;

    default:
        $stPrograma = "ManterRetido";
    break;
}

$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";
$stCaminho  = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

include_once 'JSManterLevantamento.php';

$obForm = new Form();
$obForm->setAction( $pgProc );

#### Campos Hidden ####
//stAcao
$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Cod. Tipo Fiscalização
$obHdnInTipoFiscalizacao = new Hidden();
$obHdnInTipoFiscalizacao->setName( "inTipoFiscalizacao" );
$obHdnInTipoFiscalizacao->setValue( $_REQUEST["inTipoFiscalizacao"] );

//Cod. Processo Fiscal
$obHdnInCodProcesso = new Hidden();
$obHdnInCodProcesso->setName( "inCodProcesso" );
$obHdnInCodProcesso->setId( "inCodProcesso" );
$obHdnInCodProcesso->setValue( $_REQUEST["inCodProcesso"] );

//Cod. Fiscal
$obHdnInCodFiscal = new Hidden();
$obHdnInCodFiscal->setName( "inCodFiscal" );
$obHdnInCodFiscal->setId( "inCodFiscal" );
$obHdnInCodFiscal->setValue( $_REQUEST["inCodFiscal"] );

//Inscricao
$obHdnInInscricao = new Hidden();
$obHdnInInscricao->setName( "inInscricao" );
$obHdnInInscricao->setId( "inInscricao" );
$obHdnInInscricao->setValue( $_REQUEST["inInscricao"] );

//Cod. Atividade
$obHdnCodAtividade = new Hidden();
$obHdnCodAtividade->setName( "inCodAtividade" );
$obHdnCodAtividade->setId( "inCodAtividade" );
$obHdnCodAtividade->setValue( $_REQUEST["inCodAtividade"] );

//Cod. Modalidade
$obHdnCodModalidade = new Hidden();
$obHdnCodModalidade->setName( "inCodModalidade" );
$obHdnCodModalidade->setId( "inCodModalidade" );
$obHdnCodModalidade->setValue( $_REQUEST["inCodModalidade"] );

//Nom. Modalidade
$obHdnNomModalidade = new Hidden();
$obHdnNomModalidade->setName( "inNomModalidade" );
$obHdnNomModalidade->setId( "inNomModalidade" );
$obHdnNomModalidade->setValue( $_REQUEST["inNomModalidade"] );

//Nom. Atividade
$obHdnNomAtividade = new Hidden();
$obHdnNomAtividade->setName( "inNomAtividade" );
$obHdnNomAtividade->setId( "inNomAtividade" );
$obHdnNomAtividade->setValue( $_REQUEST["inNomAtividade"] );

//Cod. Inicio
$obHdnInInicio = new Hidden();
$obHdnInInicio->setName( "inInicio" );
$obHdnInInicio->setId( "inInicio" );
$obHdnInInicio->setValue( $_REQUEST['inInicio']);

//Cod. Termino
$obHdnInTermino = new Hidden();
$obHdnInTermino->setName( "inTermino" );
$obHdnInTermino->setId( "inTermino" );
$obHdnInTermino->setValue( $_REQUEST['inTermino']);

//stUrl
$obHdnStUrl = new Hidden();
$obHdnStUrl->setName( "stUrl" );
$obHdnStUrl->setId( "stUrl" );
$obHdnStUrl->setValue( $_REQUEST["stUrl"] );

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
$obMensagem->setValue( "Existem documentos que ainda não foram entregues. Deseja continuar o Cadastro de Levantamento Fiscal?" );
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
$obBtnSim->obEvento->setOnClick( "javascript:submitLevantamento(1);" );
$obBtnSim->setDisabled( false );

$obBtnNao = new Button;
$obBtnNao->setName( "Nao" );
$obBtnNao->setValue( "Não" );
$obBtnNao->setTipo( "button" );
$obBtnNao->setStyle( "font-size:12px; color:#0A5A82; font-weight:bold; background-color:#E4EAE4; width:100px;" );
$obBtnNao->obEvento->setOnClick( "javascript:submitLevantamento(2);" );
$obBtnNao->setDisabled( false );

//Monta o formulário
$obFormulario = new Formulario();
$obFormulario->setClassCampo('');
$obFormulario->setClassCampoE('');
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnInTipoFiscalizacao );
$obFormulario->addHidden( $obHdnInCodProcesso );
$obFormulario->addHidden( $obHdnInInicio );
$obFormulario->addHidden( $obHdnInTermino );
$obFormulario->addHidden( $obHdnInCodFiscal );
$obFormulario->addHidden( $obHdnInInscricao );
$obFormulario->addHidden( $obHdnCodAtividade );
$obFormulario->addHidden( $obHdnCodModalidade );
$obFormulario->addHidden( $obHdnNomAtividade );
$obFormulario->addHidden( $obHdnNomModalidade );
$obFormulario->addHidden( $obHdnStUrl );
$obFormulario->addSpan( $obSpanImgMensagem );
$obFormulario->defineBarra( array( $obBtnSim, $obBtnNao ), 'center', '' );
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
