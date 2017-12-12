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
    * Data de Criação: 17/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2008-03-12 16:37:26 -0300 (Qua, 12 Mar 2008) $

    * Casos de uso: uc-04.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                 );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                 );

include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$inMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();
$boApresentaBase = $obRFolhaPagamentoConfiguracao->getApresentaAbaBase();
$stImpressao = $obRFolhaPagamentoConfiguracao->getImpressao();
$stImpressora = $obRFolhaPagamentoConfiguracao->getImpressora();
$stMensagemAniversariantes = $obRFolhaPagamentoConfiguracao->getMensagemAniversariantes();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

$obLblAviso = new Label;
$obLblAviso->setRotulo ( "Mascara do Evento"      );
$obLblAviso->setName   ( "stLblAviso"             );
$obLblAviso->setId     ( "stLblAviso"             );
$obLblAviso->setValue  ( "A máscara não pode ser alterada,pois já existe um evento cadastrado no sistema."         );

$obTxtMascara= new TextBox;
$obTxtMascara->setRotulo                        ( "Máscara do Evento"                                   );
$obTxtMascara->setTitle                         ( "Informe a máscara do evento."                        );
$obTxtMascara->setName                          ( "inMascaraEvento"                                     );
$obTxtMascara->setValue                         ( $inMascaraEvento                                      );
$obTxtMascara->setMaxLength                     ( 5                                                     );
$obTxtMascara->setSize                          ( 10                                                    );
$obTxtMascara->setNull                          ( false                                                 );

$obRdoSim = new Radio;
$obRdoSim->setName                              ( "boApresentaBase"                                     );
$obRdoSim->setTitle                             ( "Selecione se a aba será apresentada no cadastro de registro de evento por contrato." );
$obRdoSim->setRotulo                            ( "Apresenta Aba Base no Registro de Evento"            );
$obRdoSim->setLabel                             ( "Sim"                                                 );
$obRdoSim->setValue                             ( "true"                                                );
$obRdoSim->setNull                              ( false                                                 );
$obRdoSim->setChecked                           ( $boApresentaBase == 'true' || !$boApresentaBase       );

$obRdoNao = new Radio;
$obRdoNao->setName                              ( "boApresentaBase"                                     );
$obRdoNao->setTitle                             ( "Selecione se a aba base será apresentada no cadastro de registro de evento por contrato." );
$obRdoNao->setRotulo                            ( "Apresenta Aba Base no Registro de Evento"            );
$obRdoNao->setLabel                             ( "Não"                                                 );
$obRdoNao->setValue                             ( "false"                                               );
$obRdoNao->setNull                              ( false                                                 );
$obRdoNao->setChecked                           ( $boApresentaBase == 'false'                           );

$obTxtMensagemAniversariantes = new TextArea();
$obTxtMensagemAniversariantes->setRotulo("Mensagem padrão para Aniversariantes"                         );
$obTxtMensagemAniversariantes->setName("stMensagemAniversariantes"  									);
$obTxtMensagemAniversariantes->setValue($stMensagemAniversariantes									    );
$obTxtMensagemAniversariantes->setTitle("Informe a mensagem padrão para Aniversariantes."   			);
$obTxtMensagemAniversariantes->setMaxCaracteres(240														);

$obRdoImpressaoLaiser = new Radio;
$obRdoImpressaoLaiser->setName( "stImpressao" );
$obRdoImpressaoLaiser->setTitle( "Selecione o tipo de impressão para os contracheques." );
$obRdoImpressaoLaiser->setRotulo( "Tipo de Impressão" );
$obRdoImpressaoLaiser->setLabel( "Laser" );
$obRdoImpressaoLaiser->setValue( "laser" );
//$obRdoImpressaoLaiser->obEvento->setOnChange("bloqueiaImpressora();");
if ($stImpressao == "laser" or $stImpressao == "") {
    $obRdoImpressaoLaiser->setChecked( true );
}

$obRdoImpressaoMatricial = new Radio;
$obRdoImpressaoMatricial->setName( "stImpressao" );
$obRdoImpressaoMatricial->setTitle( "Selecione o tipo de impressão para os contracheques." );
$obRdoImpressaoMatricial->setRotulo( "Tipo de Impressão" );
$obRdoImpressaoMatricial->setLabel( "Matricial" );
$obRdoImpressaoMatricial->setValue( "matricial" );
//$obRdoImpressaoMatricial->obEvento->setOnChange("desbloqueiaImpressora();");
if ($stImpressao == "matricial") {
    $obRdoImpressaoMatricial->setChecked(true);
}

include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuarioImpressora.class.php");
$obTAdministracaoUsuarioImpressora = new TAdministracaoUsuarioImpressora();
$stFiltro = " AND usimp.numcgm = ".Sessao::read('numCgm');
$obTAdministracaoUsuarioImpressora->recuperaRelacionamento($rsImpressora,$stFiltro);

$obCmbImpressora = new Select();
$obCmbImpressora->setRotulo("Impressora");
$obCmbImpressora->setTitle("Selecione a impressora matricial para a impressão do contracheque.");
$obCmbImpressora->setName("stImpressora");
$obCmbImpressora->setId("stImpressora");
$obCmbImpressora->setStyle("width: 200px");
$obCmbImpressora->addOption("","Selecione");
$obCmbImpressora->setCampoId("cod_impressora");
$obCmbImpressora->setCampoDesc("nom_impressora");
$obCmbImpressora->setValue($stImpressora);
$obCmbImpressora->preencheCombo($rsImpressora);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                               );
$obForm->setTarget                              ( "oculto"                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                        ( "Dados para Configuração"                             );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );

$rsEventos = new Recordset;
$obRFolhaPagamentoEvento = new  RFolhaPagamentoEvento;
$obRFolhaPagamentoEvento->listar($rsEventos);
if ( $rsEventos->getNumLinhas() > 0 ) {
     $obTxtMascara->setDisabled(true);
     $obFormulario->agrupaComponentes(array($obTxtMascara,$obLblAviso)                                  );
} else {
     $obFormulario->addComponente                    ( $obTxtMascara                                    );
}

$obFormulario->agrupaComponentes                ( array($obRdoSim,$obRdoNao)                            );
$obFormulario->addTitulo("Dados para Emissão do Contracheque");
$obFormulario->addComponente                    ( $obTxtMensagemAniversariantes                         );
$obFormulario->agrupaComponentes(array($obRdoImpressaoLaiser,$obRdoImpressaoMatricial));
$obFormulario->addComponente($obCmbImpressora);
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
