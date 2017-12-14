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
    * Data de Criação: 15/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26126 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-16 17:23:35 -0200 (Ter, 16 Out 2007) $

    * Casos de uso : uc-03.05.29
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TLIC."TLicitacaoConvenio.class.php");
include_once(TLIC."TLicitacaoConvenioAditivos.class.php");
include_once(TLIC."TLicitacaoParticipanteConvenio.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';


$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

// padrão do programa
$stPrograma = "ManterAditivoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );
//fim do padrão

$obTLicitacaoContrato = new TLicitacaoConvenio;
$obTLicitacaoContrato->setDado('num_convenio', $_REQUEST["inNumConvenio"]);
$obTLicitacaoContrato->setDado('exercicio', $_REQUEST["stExercicio"]);
$obTLicitacaoContrato->recuperaConvenioListagem($rsLicitacaoConvenio);

$obLblNumeroContrato = new Label;
$obLblNumeroContrato->setRotulo('Número do Contrato');
$obLblNumeroContrato->setValue($_REQUEST["inNumConvenio"]."/".$_REQUEST["stExercicio"]);

$obLblObjeto = new Label;
$obLblObjeto->setRotulo('Objeto');
$obLblObjeto->setValue($rsLicitacaoConvenio->getCampo("objeto_descricao"));

$obLblRespJuridico = new Label;
$obLblRespJuridico->setRotulo('Responsável Jurídico');
$obLblRespJuridico->setValue($rsLicitacaoConvenio->getCampo("cgm_responsavel")." - ".$rsLicitacaoConvenio->getCampo("nom_cgm"));

$obLblDtAssinatura = new Label;
$obLblDtAssinatura->setRotulo('Data da Assinatura');
$obLblDtAssinatura->setValue($rsLicitacaoConvenio->getCampo("dt_assinatura"));

$obLblVencimento = new Label;
$obLblVencimento->setRotulo('Vencimento');
$obLblVencimento->setValue($rsLicitacaoConvenio->getCampo("dt_vigencia"));

$obLblVlConvenio = new Label;
$obLblVlConvenio->setRotulo('Valor do Convênio');
$obLblVlConvenio->setValue(number_format(str_replace(".", ",", $rsLicitacaoConvenio->getCampo('valor')), 2, ",", "."));

$obTLicitacaoParticipanteConvenio = new TLicitacaoParticipanteConvenio;
$obTLicitacaoParticipanteConvenio->setDado('num_convenio', $_REQUEST["inNumConvenio"]);
$obTLicitacaoParticipanteConvenio->setDado('exercicio', $_REQUEST["stExercicio"]);
$obTLicitacaoParticipanteConvenio->recuperaParticipanteConvenio($rsLicitacaoParticipanteConvenio);

$table = new Table();
$table->setRecordset( $rsLicitacaoParticipanteConvenio );
$table->setSummary('Participantes');
$table->Head->addCabecalho( 'Código' , 10  );
$table->Head->addCabecalho( 'Descrição do Participante' , 50  );
$table->Body->addCampo( "cgm_fornecedor", 'E' );
$table->Body->addCampo( "nom_cgm", 'E' );
$table->montaHTML();

$obSpanLista = new Span;
$obSpanLista->setId("obSpanLista");
$obSpanLista->setValue($table->getHtml());

// monta informações dos dados dos aditivos
if ($stAcao != "incluir") {
    $obNumeroAditivo = new Label;
    $obNumeroAditivo->setRotulo('Número do Aditivo');
    $obNumeroAditivo->setValue($_REQUEST["inNumeroAditivo"]."/".$_REQUEST["stExercicioAditivo"]);
}

$dtAssinatura = "";
$inRespJuridico = "";
$dtInicioExcucao = "";
$dtFinalVigencia = "";
$stObjeto = "";
$stObservacao = "";
$stFundamentacaoLegal = "";
$vlValorConvenio = "";
if ($stAcao != "incluir") {
    $obLicitacaoConvenioAditivos = new TLicitacaoConvenioAditivos;
    $obLicitacaoConvenioAditivos->setDado("num_convenio"        , $_REQUEST["inNumConvenio"]);
    $obLicitacaoConvenioAditivos->setDado("exercicio_convenio"  , $_REQUEST["stExercicio"]);
    $obLicitacaoConvenioAditivos->setDado("num_aditivo"         , $_REQUEST["inNumeroAditivo"]);
    $obLicitacaoConvenioAditivos->recuperaConvenioAditivo($rsLicitacaoConvenioAditivo);

    $inCodRespJuridico      = $rsLicitacaoConvenioAditivo->getCampo("responsavel_juridico");
    $stRespJuridico         = $rsLicitacaoConvenioAditivo->getCampo("cgm_responsavel_juridico");
    $dtAssinatura           = $rsLicitacaoConvenioAditivo->getCampo("dt_assinatura");
    $dtInicioExcucao        = $rsLicitacaoConvenioAditivo->getCampo("inicio_execucao");
    $dtFinalVigencia        = $rsLicitacaoConvenioAditivo->getCampo("dt_vigencia");
    $stObjeto               = $rsLicitacaoConvenioAditivo->getCampo("objeto");
    $stObservacao           = $rsLicitacaoConvenioAditivo->getCampo("observacao");
    $stFundamentacaoLegal   = $rsLicitacaoConvenioAditivo->getCampo("fundamentacao");
    $vlValorConvenio = number_format(str_replace(".", ",", $rsLicitacaoConvenioAditivo->getCampo("valor_convenio")), 2, ",", ".");
}

if ($stAcao != "anular") {
    //monta o popUp de pessoa juridica
    $obResponsavelJuridico = new IPopUpCGMVinculado( $obForm );
    $obResponsavelJuridico->setTabelaVinculo( 'sw_cgm_pessoa_fisica' );
    $obResponsavelJuridico->setCampoVinculo( 'numcgm' );
    $obResponsavelJuridico->setNomeVinculo( 'Responsavel' );
    $obResponsavelJuridico->setTitle( 'Informe o CGM do responsável jurídico do aditivo.' );
    $obResponsavelJuridico->setRotulo( 'Responsável Jurídico' );
    $obResponsavelJuridico->setName( 'stResponsavelJuridico');
    $obResponsavelJuridico->setId( 'stResponsavelJuridico');
    $obResponsavelJuridico->setValue( $stRespJuridico );
    $obResponsavelJuridico->obCampoCod->setName( "inCodRespJuridico" );
    $obResponsavelJuridico->obCampoCod->setId( "inCodRespJuridico" );
    $obResponsavelJuridico->obCampoCod->setValue( $inCodRespJuridico );
    $obResponsavelJuridico->obCampoCod->setNull( true );
    $obResponsavelJuridico->setNull( false );

    //monta o campo Data de Assinatura
    $obDtAssinatura = new Data;
    $obDtAssinatura->setRotulo('Data da Assinatura');
    $obDtAssinatura->setTitle('Informe a data de assinatura do aditivo.');
    $obDtAssinatura->setName('dtAssinatura');
    $obDtAssinatura->setValue($dtAssinatura);
    $obDtAssinatura->setNull(false);

    //monta o campo Data de Inicio de Execução
    $obDtInicioExecucao = new Data;
    $obDtInicioExecucao->setRotulo('Data de Início de Execução');
    $obDtInicioExecucao->setTitle('Informe a data de início da excuçãodo aditivo.');
    $obDtInicioExecucao->setName('dtInicioExcucao');
    $obDtInicioExecucao->setValue($dtInicioExcucao);
    $obDtInicioExecucao->setNull(false);

    //monta o campo Data Final de Vigência
    $obDtFinalVigencia = new Data;
    $obDtFinalVigencia->setRotulo('Data Final de Vigência');
    $obDtFinalVigencia->setTitle('Informe a final de vigência do aditivo.');
    $obDtFinalVigencia->setName('dtFinalVigencia');
    $obDtFinalVigencia->setValue($dtFinalVigencia);
    $obDtFinalVigencia->setNull(false);

    $obTxtObjeto = new TextBox;
    $obTxtObjeto->setRotulo('Objeto');
    $obTxtObjeto->setTitle('Informe o objeto do aditivo.');
    $obTxtObjeto->setName('stObjeto');
    $obTxtObjeto->setNull(false);
    $obTxtObjeto->setMaxLength(50);
    $obTxtObjeto->setSize(60);
    $obTxtObjeto->setValue($stObjeto);

    $obIPopUpLei = new IPopUpNorma();
    $obIPopUpLei->obInnerNorma->setId('stFundamentacaoLegal');
    $obIPopUpLei->obInnerNorma->obCampoCod->stId = 'inCodLei';
    $obIPopUpLei->obInnerNorma->obCampoCod->setName( "inCodLei" );
    $obIPopUpLei->obInnerNorma->setRotulo("Fundamentação Legal");
    $obIPopUpLei->setExibeDataNorma(true);

    $obTxtObservacao = new TextArea;
    $obTxtObservacao->setRotulo('Observação');
    $obTxtObservacao->setTitle('Informe a observação do aditivo.');
    $obTxtObservacao->setName('stObservacao');
    $obTxtObservacao->setNull(false);
    $obTxtObservacao->setMaxCaracteres(198);
    $obTxtObservacao->setValue($stObservacao);

    $obVlValorConvenio = new Moeda;
    $obVlValorConvenio->setRotulo('Valor');
    $obVlValorConvenio->setTitle('Informe o valor do aditivo.');
    $obVlValorConvenio->setId('vlValorConvenio');
    $obVlValorConvenio->setName('vlValorConvenio');
    $obVlValorConvenio->setNull(false);
    $obVlValorConvenio->setCaracteresAceitos( "[0-9\,]" );
    $obVlValorConvenio->setValue($vlValorConvenio);
} else {
    $obResponsavelJuridico = new Label;
    $obResponsavelJuridico->setRotulo('Responsável Jurídico');
    $obResponsavelJuridico->setValue($inCodRespJuridico." - ".$stRespJuridico);

    $obDtAssinatura = new Label;
    $obDtAssinatura->setRotulo('Data da Assinatura');
    $obDtAssinatura->setValue($dtAssinatura);

    $obDtInicioExecucao = new Label;
    $obDtInicioExecucao->setRotulo('Data de Início de Execução');
    $obDtInicioExecucao->setValue($dtInicioExcucao);

    $obDtFinalVigencia = new Label;
    $obDtFinalVigencia->setRotulo('Data Final de Vigência');
    $obDtFinalVigencia->setValue($dtFinalVigencia);

    $obTxtObjeto = new Label;
    $obTxtObjeto->setRotulo('Objeto');
    $obTxtObjeto->setValue($stObjeto);

    $obTxtFundLegal = new Label;
    $obTxtFundLegal->setRotulo('Fundamentação Legal');
    $obTxtFundLegal->setValue($stFundamentacaoLegal);

    $obTxtObservacao = new Label;
    $obTxtObservacao->setRotulo('Observação');
    $obTxtObservacao->setValue($stObservacao);

    $obVlValorConvenio = new Label;
    $obVlValorConvenio->setRotulo('Valor');
    $obVlValorConvenio->setValue($vlValorConvenio);

    $obDataAnulacao = new Data;
    $obDataAnulacao->setName("dtAnulacao");
    $obDataAnulacao->setRotulo("Data Anulação");
    $obDataAnulacao->setTitle("Informe a data de anulação.");
    $obDataAnulacao->setNull(false);
    $obDataAnulacao->setValue(date("d/m/Y"));

    $obTxtMotivoAnulacao = new TextBox;
    $obTxtMotivoAnulacao->setRotulo('Motivo');
    $obTxtMotivoAnulacao->setTitle('Informe o motivo da anulação.');
    $obTxtMotivoAnulacao->setName('stMotivoAnulacao');
    $obTxtMotivoAnulacao->setNull(false);
    $obTxtMotivoAnulacao->setMaxLength(50);
    $obTxtMotivoAnulacao->setSize(75);

}

//objetos hidden das labels
$obHdnInNumConvenio = new Hidden;
$obHdnInNumConvenio->setName( "inNumConvenio" );
$obHdnInNumConvenio->setValue( $_REQUEST["inNumConvenio"] );

$obHdnStExercicio = new Hidden;
$obHdnStExercicio->setName( "stExercicio" );
$obHdnStExercicio->setValue( $_REQUEST["stExercicio"] );

if ($stAcao != "incluir") {
    $obHdnInNumeroAditivo = new Hidden;
    $obHdnInNumeroAditivo->setName( "inNumeroAditivo" );
    $obHdnInNumeroAditivo->setValue( $_REQUEST["inNumeroAditivo"] );

    $obHdnStExercicioAditivo = new Hidden;
    $obHdnStExercicioAditivo->setName( "stExercicioAditivo" );
    $obHdnStExercicioAditivo->setValue( $_REQUEST["stExercicioAditivo"] );
}

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setId    ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""              );

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull( true );
$obVeiculoPublicidade->obCampoCod->setNull( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataPublicacao" );
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( date('d/m/Y') );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setObrigatorioBarra( true );
$obDataPublicacao->setTitle( "Informe a data de publicação." );

$obNumeroPublicacao = new Inteiro();
$obNumeroPublicacao->setId   ( "inNumPublicacao" );
$obNumeroPublicacao->setName ( "inNumPublicacao" );
$obNumeroPublicacao->setValue( '' );
$obNumeroPublicacao->setRotulo( "Número Publicação" );
$obNumeroPublicacao->setObrigatorioBarra( false	);
$obNumeroPublicacao->setTitle( "Informe o Número da Publicação " );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, _stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

//Campo Observação da Publicação
$_obTxtObservacao = new TextArea;
$_obTxtObservacao->setId     ( "_stObservacao"                               );
$_obTxtObservacao->setName   ( "_stObservacao"                               );
$_obTxtObservacao->setValue  ( ""                                            );
$_obTxtObservacao->setRotulo ( "Observação"                                  );
$_obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação." );
$_obTxtObservacao->setObrigatorioBarra( false                                );
$_obTxtObservacao->setRows   ( 2                                             );
$_obTxtObservacao->setCols   ( 100                                           );
$_obTxtObservacao->setMaxCaracteres( 80 );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnInNumConvenio );
$obFormulario->addHidden( $obHdnStExercicio );

if ($stAcao != "incluir") {
    $obFormulario->addHidden( $obHdnInNumeroAditivo );
    $obFormulario->addHidden( $obHdnStExercicioAditivo );
}

$obFormulario->addTitulo( "Dados do Convênio" );
$obFormulario->addComponente( $obLblNumeroContrato );
$obFormulario->addComponente( $obLblObjeto );
$obFormulario->addComponente( $obLblRespJuridico );
$obFormulario->addComponente( $obLblDtAssinatura );
$obFormulario->addComponente( $obLblVencimento );
$obFormulario->addComponente( $obLblVlConvenio );

$obFormulario->addSpan( $obSpanLista );

$obFormulario->addTitulo( "Dados do Aditivo" );
if ($stAcao != "incluir") {
    $obFormulario->addComponente( $obNumeroAditivo );
}

$obFormulario->addComponente( $obResponsavelJuridico );
$obFormulario->addComponente( $obDtAssinatura );
$obFormulario->addComponente( $obDtInicioExecucao );
$obFormulario->addComponente( $obDtFinalVigencia );
$obFormulario->addComponente( $obTxtObjeto );
if ($stAcao == "anular") {
    $obFormulario->addComponente( $obTxtFundLegal );
}else{
    $obIPopUpLei->geraFormulario($obFormulario);
}

$obFormulario->addComponente( $obTxtObservacao );
$obFormulario->addComponente( $obVlValorConvenio );

$obFormulario->addTitulo        ( 'Veículo de Publicação' );
$obFormulario->addComponente    ( $obVeiculoPublicidade );
$obFormulario->addComponente    ( $obDataPublicacao );
$obFormulario->addComponente    ( $obNumeroPublicacao );
$obFormulario->addComponente    ( $_obTxtObservacao );
$obFormulario->defineBarra      ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan          ( $obSpnListaVeiculo );
$obFormulario->addHidden        ( $obHdnCodVeiculo );

if ($stAcao == "anular") {
    $obFormulario->addTitulo( "Dados da Anulação do Aditivo" );
    $obFormulario->addComponente( $obDataAnulacao );
    $obFormulario->addComponente( $obTxtMotivoAnulacao );
}

if ($stAcao == "incluir") {
    $obFormulario->Ok();
} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar($stLocation);
}
$obFormulario->show();
if ($stAcao == 'alterar' || $stAcao == 'anular'|| $stAcao == 'anular') {
    echo "<script type=\"text/javascript\"> \r\n";
    echo " ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumeroAditivo=".$_REQUEST['inNumeroAditivo']."&inNumConvenio=".$_REQUEST['inNumConvenio']."&stExercicio=".$_REQUEST['stExercicioAditivo']."', 'carregaListaVeiculos');  \r\n";
    echo " ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLei=".$rsLicitacaoConvenioAditivo->getCampo("cod_norma_autorizativa"). "', 'montaBuscaNorma');     \r\n";
    echo "</script>";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
