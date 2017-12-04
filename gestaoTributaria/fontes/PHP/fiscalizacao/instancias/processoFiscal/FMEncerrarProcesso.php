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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php';
require_once CAM_GT_FIS_INSTANCIAS.'processoFiscal/JSEmitirDocumento.php';
include_once CAM_GT_FIS_NEGOCIO.'/RFISProcessoFiscal.class.php';
include_once CAM_GT_FIS_VISAO.'/VFISProcessoFiscal.class.php';

$obController = new RFISProcessoFiscal;
$obVisao = new VFISProcessoFiscal($obController);

$stAcao 			= $_GET['stAcao'];

$inCodProcesso 	    = $_REQUEST['inCodProcesso'];
$inTipoFiscalizacao = $_REQUEST['inTipoFiscalizacao'];

$stPrograma 	= "ManterProcesso";
$pgFilt         = "FL".$stPrograma.".php";
$pgList     	= "LS".$stPrograma.".php";
$pgForm     	= "FM".$stPrograma.".php";
$pgProc     	= "PR".$stPrograma.".php";
$pgOcul     	= "OC".$stPrograma.".php";
$pgJs         	= "JS".$stPrograma.".php";

include_once($pgJs);

//rsFundamentacao
$rsFundamentacao = $obVisao->getFundamentacao($inCodProcesso, $inTipoFiscalizacao);

//rsProcesso
$rsProcesso = $obVisao->BuscaDadosProcesso($inCodProcesso);

//rsGrupoCredito
$rsListaGrupoCredito = $obVisao->BuscaGrupoCreditoProcesso($inCodProcesso);

//rsTipoFiscalizacao
$rsTipoFiscalizacao  = $obVisao->getTipoFiscalizacao($inTipoFiscalizacao);

//Definição do Form
$obForm = new Form;
$obForm->setAction                  ($pgProc);
$obForm->setTarget                  ("telaPrincipal");

$obHdnAcao =  new Hidden;
$obHdnAcao->setName                 ("stAcao");
$obHdnAcao->setValue                ($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                 ("stCtrl");
$obHdnCtrl->setValue                ($_GET['stCtrl']);

$obHdnCodigoProcesso =  new Hidden;
$obHdnCodigoProcesso->setName       ("inCodProcesso");
$obHdnCodigoProcesso->setValue      ($inCodProcesso);

$obHdnCodigoIncricao =  new Hidden;
$obHdnCodigoIncricao->setName       ("inInscricao");
$obHdnCodigoIncricao->setValue      ($_REQUEST['inInscricao']);

$obHdnCodigoFiscal =  new Hidden;
$obHdnCodigoFiscal->setName         ("inCodFiscal");
$obHdnCodigoFiscal->setValue        (substr($_SESSION['numCgm'], 5,-2));

//Processo Fiscal
$obProcessoFiscal = new Label;
$obProcessoFiscal->setRotulo        ("Processo Fiscal");
$obProcessoFiscal->setName          ("inProcessoFiscal");
$obProcessoFiscal->setValue         ($inCodProcesso);

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($inTipoFiscalizacao) {
    case 1: //Inscricao Economica
        $obInscricaoEconomica = new Label();
        $obInscricaoEconomica->setRotulo("Inscrição Econômica");
        $obInscricaoEconomica->setName  ("stInscricaoEconomica");
        $obInscricaoEconomica->setValue ($_REQUEST['inInscricao']);
    break;

    case 2: //Inscricao Municipal
        $obInscricaoMunicipal = new Label();
        $obInscricaoMunicipal->setRotulo("Inscrição Municipal");
        $obInscricaoMunicipal->setName  ("stInscricaoMunicipal");
        $obInscricaoMunicipal->setValue ($_REQUEST['inInscricao']);
    break;
}

//Tipo Fiscalização
$obTipoFiscalizacao = new Label;
$obTipoFiscalizacao->setRotulo      ("Tipo de Fiscalização");
$obTipoFiscalizacao->setName        ("stTipoFiscalizacao");
$obTipoFiscalizacao->setValue       ($inTipoFiscalizacao ." - ". $rsTipoFiscalizacao->getCampo('descricao'));

//Fundamentação Legal
$obFundamentacaoLegal = new Label;
$obFundamentacaoLegal->setRotulo    ("Fundamentação Legal");
$obFundamentacaoLegal->setName      ("stFundamentacaoLegal");
$obFundamentacaoLegal->setValue     ($rsFundamentacao->arElementos[0]['cod_processo_protocolo']." / ". $rsFundamentacao->arElementos[0]['ano_exercicio']);

$dtInicio = new Label;
$dtInicio->setRotulo                ("Data de Inicio");
$dtInicio->setName                  ("lbInicio");
$dtInicio->setValue                 ($rsProcesso->getCampo('periodo_inicio'));

$dtPrevisaoEncerramento = new Label;
$dtPrevisaoEncerramento->setRotulo  ("Previsão de Encerramento");
$dtPrevisaoEncerramento->setName    ("lbPrevisaoEncerramento");
$dtPrevisaoEncerramento->setValue   ($rsProcesso->getCampo('previsao_termino'));

$dtEncerramento = new Data;
$dtEncerramento->setRotulo          ("Data de Encerramento");
$dtEncerramento->setName            ("dtEncerramento");
$dtEncerramento->setId              ("dtEncerramento");
$dtEncerramento->setTitle           ("Informe a Data de Encerramento.");
$dtEncerramento->setNull            (false);

$obObservacao = new TextArea;
$obObservacao->setRotulo            ("Observação");
$obObservacao->setName              ("stObeservacao");
$obObservacao->setId                ("stObeservacao");
$obObservacao->setTitle             ("Informe as Observações.");
$obObservacao->setNull              (false);

$obTermoEncerramento = new ITextBoxSelectDocumento();
$obTermoEncerramento->setCodAcao( substr($_SESSION["acao"], 5,-2) ) ;
$obTermoEncerramento->obTextBoxSelectDocumento->setRotulo( "Termo de Encerramento" );
$obTermoEncerramento->obTextBoxSelectDocumento->setTitle( "Selecione o Termo de Encerramento." );
$obTermoEncerramento->obTextBoxSelectDocumento->obTextBox->setSize( 10 );
$obTermoEncerramento->obTextBoxSelectDocumento->obSelect->setStyle( "width: 261px;" );
$obTermoEncerramento->obTextBoxSelectDocumento->setNull( false );
$obTermoEncerramento->obTextBoxSelectDocumento->obTextBox->setNull( false );

$obSpanCreditoGrupo = new Span;
$obSpanCreditoGrupo->setId          ('spnListaCreditoGrupo');
$obSpanCreditoGrupo->setValue       ($rsListaGrupoCredito);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo            ('Dados para Encerramento de Processo Fiscal');
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addHidden            ($obHdnCodigoFiscal);
$obFormulario->addHidden            ($obHdnCodigoIncricao);
$obFormulario->addHidden            ($obHdnCodigoProcesso);
$obFormulario->addComponente        ($obTipoFiscalizacao);
$obFormulario->addComponente        ($obProcessoFiscal);

switch ($inTipoFiscalizacao) {
    case 1:
        $obFormulario->addComponente($obInscricaoEconomica);
        $obFormulario->addComponente($obFundamentacaoLegal);
    break;

    case 2:
        $obFormulario->addComponente($obInscricaoMunicipal);
    break;
}

$obFormulario->addSpan              ($obSpanCreditoGrupo);

$obFormulario->addComponente        ($dtInicio);
$obFormulario->addComponente        ($dtPrevisaoEncerramento);
$obFormulario->addComponente        ($dtEncerramento);
$obFormulario->addComponente        ($obObservacao);

$obTermoEncerramento->geraFormulario($obFormulario);

$obBtnOK = new Ok();

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTipo("button");
$obBtnLimpar->setStyle("width: 60px;");
$obBtnLimpar->obEvento->setOnClick("limparFormEncerrar();");

$arBotoes = array($obBtnOK, $obBtnLimpar);

$obFormulario->defineBarra($arBotoes, 'left', '<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;');

$obFormulario->show();
