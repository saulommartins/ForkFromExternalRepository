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
    * Página de Formulário para configuração do arquivo DDC TCE/MG
    * Data de Criação: 05/03/2014

    * @author Analista:      Sergio Luiz dos Santos
    * @author Desenvolvedor: Arthur Cruz

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGConfiguracaoDDC.class.php';

Sessao::remove('arDividas');

$stPrograma = "ManterConfiguracaoArquivoDDC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);
$stAcao = $request->get('stAcao');

if ($stAcao == "alterar") {
    
    $obTTCEMGConfiguracaoDDC = new TTCEMGConfiguracaoDDC();
    $obTTCEMGConfiguracaoDDC->setDado('exercicio',$request->get('inExercicio'));
    $obTTCEMGConfiguracaoDDC->setDado('mes_referencia',$request->get('inMes'));
    $obTTCEMGConfiguracaoDDC->setDado('cod_entidade',$request->get('inCodEntidade'));
    $stFiltro = " AND configuracao_ddc.nro_contrato_divida = '".$request->get('inNroContrato')."' AND configuracao_ddc.cod_norma = ".$request->get('inNroLeiAutorizacao');
    $obTTCEMGConfiguracaoDDC->recuperaDadosDDC($rsTTCEMGConfiguracaoDDC, $stFiltro );
    
    include_once( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php" );
    $obNorma = new RNorma;
    $obNorma->setCodNorma( $rsTTCEMGConfiguracaoDDC->getCampo('nro_lei_autorizacao') );
    $obNorma->listarDecreto( $rsNorma );
    
    $inExercicio         = $rsTTCEMGConfiguracaoDDC->getCampo('exercicio');
    $inMes               = $rsTTCEMGConfiguracaoDDC->getCampo('mes_referencia');
    $inCodEntidade       = $rsTTCEMGConfiguracaoDDC->getCampo('cod_entidade');
    $inOrgao             = $rsTTCEMGConfiguracaoDDC->getCampo('cod_orgao');
    $inLeiAutorizacao    = $rsTTCEMGConfiguracaoDDC->getCampo('nro_lei_autorizacao');
    $stNomNorma          = $rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
    $inNroContrato       = $rsTTCEMGConfiguracaoDDC->getCampo('nro_contrato_divida');
    $dtDataAssinatura    = $rsTTCEMGConfiguracaoDDC->getCampo('dt_assinatura');
    $inContratoDecLei    = $rsTTCEMGConfiguracaoDDC->getCampo('contrato_dec_lei');
    $stObjetoContrato    = $rsTTCEMGConfiguracaoDDC->getCampo('objeto_contrato_divida');
    $stEspecificacaoContrato  = $rsTTCEMGConfiguracaoDDC->getCampo('especificacao_contrato_divida');
    $inTipoLancamento    = $rsTTCEMGConfiguracaoDDC->getCampo('tipo_lancamento');
    $inCGMCredor         = $rsTTCEMGConfiguracaoDDC->getCampo('numcgm');
    $stNomeCGMCredor     = $rsTTCEMGConfiguracaoDDC->getCampo('nom_cgm');
    $stJustificativa     = $rsTTCEMGConfiguracaoDDC->getCampo('justificativa_cancelamento');
    $flValorSaldoAnt     = $rsTTCEMGConfiguracaoDDC->getCampo('valor_saldo_anterior');
    $flValorContratacao  = $rsTTCEMGConfiguracaoDDC->getCampo('valor_contratacao');
    $flValorAmortizacao  = $rsTTCEMGConfiguracaoDDC->getCampo('valor_amortizacao');
    $flValorCancelamento = $rsTTCEMGConfiguracaoDDC->getCampo('valor_cancelamento');
    $flValorEncampacao   = $rsTTCEMGConfiguracaoDDC->getCampo('valor_encampacao');
    $flValorAtualizacao  = $rsTTCEMGConfiguracaoDDC->getCampo('valor_atualizacao');
    $flValorSaldoAtual   = $rsTTCEMGConfiguracaoDDC->getCampo('valor_saldo_atual');
}else{
    $inExercicio         = "";
    $inMes               = "";
    $inCodEntidade       = $request->get('inCodEntidade');
    $inOrgao             = "";
    $inLeiAutorizacao    = "";
    $dtDataAssinatura    = "";
    $inContratoDecLei    = "";
    $stObjetoContrato    = "";
    $stEspecificacaoContrato  = "";
    $inTipoLancamento    = "";
    $inCGMCredor         = "";
    $stNomeCGMCredor      = "";
    $stJustificativa     = "";
    $flValorSaldoAnt     = "";
    $flValorContratacao  = "";
    $flValorAmortizacao  = "";
    $flValorCancelamento = "";
    $flValorEncampacao   = "";
    $flValorAtualizacao  = "";
    $flValorSaldoAtual   = "";
}

if ($request->get('stAcao') == '') {
    $stAcao = 'configurar';
} else {
    $stAcao = $request->get('stAcao');
}

$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obHdnAno = new Hidden();
$obHdnAno->setId   ('inExercicio');
$obHdnAno->setName ('inExercicio');
$obHdnAno->setValue($request->get('inExercicio'));

$obHdnMes = new Hidden();
$obHdnMes->setId   ('inMes');
$obHdnMes->setName ('inMes');
$obHdnMes->setValue($request->get('inMes'));

$obHdnId = new Hidden();
$obHdnId->setId   ('inHdnId');
$obHdnId->setName ('inHdnId');

$obEntidadeUsuarioCadastroDeLei = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuarioCadastroDeLei->setNull ( false );
$obEntidadeUsuarioCadastroDeLei->setCodEntidade($inCodEntidade);

if($stAcao == "alterar"){
    $obEntidadeUsuarioCadastroDeLei->obTextBox->setDisabled(true);
    $obEntidadeUsuarioCadastroDeLei->obSelect->setDisabled(true);

    $obHdnEntidade = new Hidden;
    $obHdnEntidade->setName ( "hdnEntidade"  );
    $obHdnEntidade->setId   ( "hdnEntidade"  );
    $obHdnEntidade->setValue( $inCodEntidade );
}

$obIPopUpLeiAutorizacao = new IPopUpNorma();
$obIPopUpLeiAutorizacao->obInnerNorma->setId              ( "stNomeLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setId  ( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setName( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->setRotulo          ( "Lei de Autorização"  );
$obIPopUpLeiAutorizacao->obInnerNorma->setTitle           ( "Informe o número de Lei de Autorização");
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setValue($inLeiAutorizacao);
$obIPopUpLeiAutorizacao->obInnerNorma->setValue           ( $stNomNorma );

$obTxtNumContratoDivida = new TextBox;
$obTxtNumContratoDivida->setRotulo     ( "Número do Contrato"                      );
$obTxtNumContratoDivida->setTitle      ( "Informe o número do contrato de dívida." );
$obTxtNumContratoDivida->setName       ( "inNumContratoDivida"                     );
$obTxtNumContratoDivida->setId         ( "inNumContratoDivida"                     );
$obTxtNumContratoDivida->setValue      ( $inNroContrato                            );
$obTxtNumContratoDivida->setNull       ( false                                     );
$obTxtNumContratoDivida->setMaxLength  ( 30                                        );
$obTxtNumContratoDivida->setSize       ( 25                                        );
$obTxtNumContratoDivida->setInteiro    ( true                                      );

if($stAcao == "alterar"){
    $obTxtNumContratoDivida->setDisabled(true);
    
    $obHdnNroContrato = new Hidden;
    $obHdnNroContrato->setName ( "hdnNroContrato"  );
    $obHdnNroContrato->setId   ( "hdnNroContrato"  );
    $obHdnNroContrato->setValue( $inNroContrato );
}


$obDtAssinaturaDivida = new Data;
$obDtAssinaturaDivida->setName     ( "dtAssinaturaDivida"           );
$obDtAssinaturaDivida->setId       ( "dtAssinaturaDivida"           );
$obDtAssinaturaDivida->setRotulo   ( "Data de Assinatura"           );
$obDtAssinaturaDivida->setValue    ( $dtDataAssinatura              );
$obDtAssinaturaDivida->setTitle    ( "Informe a data de Assinatura do Contrato.");
$obDtAssinaturaDivida->setNull     ( false                          );
$obDtAssinaturaDivida->setSize     ( 10                             );
$obDtAssinaturaDivida->setMaxLength( 10                             );

$obLabelContratoDecLei = new Label;
$obLabelContratoDecLei->setRotulo( "*Contrato decorrente de Lei de Autorização" );

$obRadioContratoDecLeiSim = new Radio();
$obRadioContratoDecLeiSim->setName  ('stContratoDecLei');
$obRadioContratoDecLeiSim->setId    ('stContratoDecLei');
$obRadioContratoDecLeiSim->setValue (1);
$obRadioContratoDecLeiSim->setLabel ('Sim');
$obRadioContratoDecLeiSim->setChecked(true);

$obRadioContratoDecLeiNao = new Radio();
$obRadioContratoDecLeiNao->setName  ('stContratoDecLei');
$obRadioContratoDecLeiNao->setId    ('stContratoDecLei');
$obRadioContratoDecLeiNao->setValue (2);
$obRadioContratoDecLeiNao->setLabel ('Não');

$obTextObjetoContrato = new TextArea;
$obTextObjetoContrato->setRotulo ( "Objeto do contrato" );
$obTextObjetoContrato->setTitle  ( "Informe o Objeto do contrato." );
$obTextObjetoContrato->setName   ( "stObjetoContrato" );
$obTextObjetoContrato->setId     ( "stObjetoContrato" );
$obTextObjetoContrato->setNull   ( false );
$obTextObjetoContrato->setValue  ( $stObjetoContrato );
$obTextObjetoContrato->setMaxCaracteres (1000); 

$obTextDescDivida = new TextArea;
$obTextDescDivida->setRotulo ( "Descrição da dívida consolidada" );
$obTextDescDivida->setTitle  ( "Informe a descrição da dívida consolidada." );
$obTextDescDivida->setName   ( "stDescDivida" );
$obTextDescDivida->setId     ( "stDescDivida" );
$obTextDescDivida->setNull   ( false );
$obTextDescDivida->setValue  ( $stEspecificacaoContrato );
$obTextDescDivida->setMaxCaracteres (500);

$obCmbTipoLancamento = new Select();
$obCmbTipoLancamento->setName   ( "inTipoLancamento"    );
$obCmbTipoLancamento->setId     ( "inTipoLancamento"    );
$obCmbTipoLancamento->setRotulo ( "Tipo de Lançamento"  );
$obCmbTipoLancamento->setTitle  ( "Informe o Tipo de Lançamento." );
$obCmbTipoLancamento->setNull   ( false );
$obCmbTipoLancamento->setValue  ( $inTipoLancamento );
$obCmbTipoLancamento->addOption ( "","Selecione");
$obCmbTipoLancamento->addOption ( "1","Dívida Mobiliária" );
$obCmbTipoLancamento->addOption ( "2","Dívida Contratual de PPP" );
$obCmbTipoLancamento->addOption ( "3","Demais Dívidas Contratuais Internas" );
$obCmbTipoLancamento->addOption ( "4","Dívidas Contratuais Externas" );
$obCmbTipoLancamento->addOption ( "5","Precatórios Posteriores a 05/05/2000 (inclusive) - Vencidos e não Pagos" );
$obCmbTipoLancamento->addOption ( "6","Parcelamento de Dívidas de Tributos" );
$obCmbTipoLancamento->addOption ( "7","Parcelamento de Dívidas Previdenciárias" );
$obCmbTipoLancamento->addOption ( "8","Parcelamento de Dívidas das Demais Contribuições Sociais" );
$obCmbTipoLancamento->addOption ( "9","Parcelamento de Dívidas do FGTS" );
$obCmbTipoLancamento->addOption ( "10","Outras Dívidas" );
$obCmbTipoLancamento->addOption ( "11","Passivos Reconhecidos" );

$obBscCGMCredor = new IPopUpCGM($obForm);
$obBscCGMCredor->setId                    ('stNomeCGMCredor');
$obBscCGMCredor->setRotulo                ( 'Credor'       );
$obBscCGMCredor->setTipo                  ('fisica'           );
$obBscCGMCredor->setTitle                 ( 'Informe o CGM relacionado ao credor');
$obBscCGMCredor->setValue                 ( $stNomeCGMCredor);
$obBscCGMCredor->obCampoCod->setName      ( 'inCGMCredor' );
$obBscCGMCredor->obCampoCod->setId        ( 'inCGMCredor' );
$obBscCGMCredor->obCampoCod->setSize      (8);
$obBscCGMCredor->obCampoCod->setValue     ( $inCGMCredor   );
$obBscCGMCredor->setNull                  ( true                );

$obTextJustificativaCancelamento = new TextArea;
$obTextJustificativaCancelamento->setRotulo   ( "Justificativa para o cancelamento da dívida" );
$obTextJustificativaCancelamento->setTitle    ( "Informe a Justificativa."    );
$obTextJustificativaCancelamento->setName     ( "stJustificativaCancelamento" );
$obTextJustificativaCancelamento->setId       ( "stJustificativaCancelamento" );
$obTextJustificativaCancelamento->setValue    ( $stJustificativa              );
$obTextJustificativaCancelamento->setMaxCaracteres (500);
    
$obFlValorSaldoAnterior = new Numerico();
$obFlValorSaldoAnterior->setId    ( "flValorSaldoAnterior"    );
$obFlValorSaldoAnterior->setName  ( "flValorSaldoAnterior"    );
$obFlValorSaldoAnterior->setRotulo( "Valor do Saldo Anterior" );
$obFlValorSaldoAnterior->setTitle ( "Informe Valor do Saldo Anterior.");
$obFlValorSaldoAnterior->setValue ( $flValorSaldoAnt          );
$obFlValorSaldoAnterior->setNull  ( false );
$obFlValorSaldoAnterior->setDecimais(2);
$obFlValorSaldoAnterior->setMaxLength(16);
$obFlValorSaldoAnterior->setSize(17);

$obFlValorContratacaoMes = new Numerico();
$obFlValorContratacaoMes->setId    ( "flValorContratacaoMes"    );
$obFlValorContratacaoMes->setName  ( "flValorContratacaoMes"    );
$obFlValorContratacaoMes->setRotulo( "Valor de Contratação"     );
$obFlValorContratacaoMes->setTitle ( "Informe o Valor de Contratação no mês.");
$obFlValorContratacaoMes->setNull  ( false );
$obFlValorContratacaoMes->setValue ( $flValorContratacao        );
$obFlValorContratacaoMes->setDecimais(2);
$obFlValorContratacaoMes->setMaxLength(16);
$obFlValorContratacaoMes->setSize(17); 

$obFlValorAmortizacaoMes = new Numerico();
$obFlValorAmortizacaoMes->setId    ( "flValorAmortizacaoMes"    );
$obFlValorAmortizacaoMes->setName  ( "flValorAmortizacaoMes"    );
$obFlValorAmortizacaoMes->setRotulo( "Valor de Amortização"     );
$obFlValorAmortizacaoMes->setTitle ( "Informe o Valor de Amortização no mês.");
$obFlValorAmortizacaoMes->setValue ( $flValorAmortizacao        );
$obFlValorAmortizacaoMes->setNull  ( false );
$obFlValorAmortizacaoMes->setDecimais(2);
$obFlValorAmortizacaoMes->setMaxLength(16);
$obFlValorAmortizacaoMes->setSize(17);  
    
$obFlValorCancelamentoMes = new Numerico();
$obFlValorCancelamentoMes->setId    ( "flValorCancelamentoMes" );
$obFlValorCancelamentoMes->setName  ( "flValorCancelamentoMes" );
$obFlValorCancelamentoMes->setRotulo( "Valor de Cancelamento"  );
$obFlValorCancelamentoMes->setTitle ( "Informe o Valor de Cancelamento no mês.");
$obFlValorCancelamentoMes->setValue ( $flValorCancelamento     );
$obFlValorCancelamentoMes->setNull  ( false );
$obFlValorCancelamentoMes->setDecimais(2);
$obFlValorCancelamentoMes->setMaxLength(16);
$obFlValorCancelamentoMes->setSize(17);

$obFlValorEncampacaoMes = new Numerico();
$obFlValorEncampacaoMes->setId    ( "flValorEncampacaoMes" );
$obFlValorEncampacaoMes->setName  ( "flValorEncampacaoMes" );
$obFlValorEncampacaoMes->setRotulo( "Valor de Encampação"  );
$obFlValorEncampacaoMes->setTitle ( "Informe o Valor de Encampação no mês.");
$obFlValorEncampacaoMes->setValue ( $flValorEncampacao     );
$obFlValorEncampacaoMes->setNull  ( false );
$obFlValorEncampacaoMes->setDecimais(2);
$obFlValorEncampacaoMes->setMaxLength(16);
$obFlValorEncampacaoMes->setSize(17);

$obFlValorAtualizacaoMes = new Numerico();
$obFlValorAtualizacaoMes->setId    ( "flValorAtualizacaoMes" );
$obFlValorAtualizacaoMes->setName  ( "flValorAtualizacaoMes" );
$obFlValorAtualizacaoMes->setRotulo( "Valor da Atualização"  );
$obFlValorAtualizacaoMes->setTitle ( "Informe o Valor da Atualização no mês.");
$obFlValorAtualizacaoMes->setNull  ( false );
$obFlValorAtualizacaoMes->setValue ( $flValorAtualizacao     );
$obFlValorAtualizacaoMes->setDecimais(2);
$obFlValorAtualizacaoMes->setMaxLength(16);
$obFlValorAtualizacaoMes->setSize(17);

$obFlValorSaldoAtual = new Numerico();
$obFlValorSaldoAtual->setId    ( "flValorSaldoAtual" );
$obFlValorSaldoAtual->setName  ( "flValorSaldoAtual" );
$obFlValorSaldoAtual->setRotulo( "Valor do Saldo Atual"  );
$obFlValorSaldoAtual->setTitle ( "Informe o Valor do Saldo Atual.");
$obFlValorSaldoAtual->setValue ( $flValorSaldoAtual  );
$obFlValorSaldoAtual->setNull  ( false );
$obFlValorSaldoAtual->setDecimais(2);
$obFlValorSaldoAtual->setMaxLength(16);
$obFlValorSaldoAtual->setSize(17);

if($stAcao == "configurar"){
    $obSpnListaDividas = new Span();
    $obSpnListaDividas->setId("spnListaDividas");
 
    $obBtnIncluirDivida = new Button();
    $obBtnIncluirDivida->setName             ( "btIncluirDivida"                       );
    $obBtnIncluirDivida->setId               ( "btIncluirDivida"                       );
    $obBtnIncluirDivida->setValue            ( "Incluir"                               );
    $obBtnIncluirDivida->obEvento->setOnClick( "montaParametrosGET('incluirDividaLista', 'inExercicio,inMes,inCodEntidade,inCodLeiAutorizacao,stNomeLeiAutorizacao,inNumContratoDivida,dtAssinaturaDivida,stContratoDecLei,stObjetoContrato,stDescDivida,inTipoLancamento,inCGMCredor,stNomeCGMCredor,stJustificativaCancelamento,flValorSaldoAnterior,flValorContratacaoMes,flValorAmortizacaoMes,flValorCancelamentoMes,flValorEncampacaoMes,flValorAtualizacaoMes,flValorSaldoAtual');"  );
    $obBtnIncluirDivida->setTitle            ( "Clique para incluir a dívida a lista"  );
    
    $obBtnAlterarDivida = new Button();
    $obBtnAlterarDivida->setName             ( "btAlterarDivida"                       );
    $obBtnAlterarDivida->setId               ( "btAlterarDivida"                       );
    $obBtnAlterarDivida->setValue            ( "Alterar"                               );
    $obBtnAlterarDivida->obEvento->setOnClick( "montaParametrosGET('alterarListaDivida', 'inHdnId,inExercicio,inMes,inCodEntidade,inCodLeiAutorizacao,stNomeLeiAutorizacao,inNumContratoDivida,dtAssinaturaDivida,stContratoDecLei,stObjetoContrato,stDescDivida,inTipoLancamento,inCGMCredor,stNomeCGMCredor,stJustificativaCancelamento,flValorSaldoAnterior,flValorContratacaoMes,flValorAmortizacaoMes,flValorCancelamentoMes,flValorEncampacaoMes,flValorAtualizacaoMes,flValorSaldoAtual');"  );
    $obBtnAlterarDivida->setTitle            ( "Clique para alterar a dívida a lista"  );
    $obBtnAlterarDivida->setDisabled         ( true                                    );
    
    $obBtEnviar = new Button();
    $obBtEnviar->setValue('Ok');
    $obBtEnviar->setName ( "btnEnviar"                 );
    $obBtEnviar->setId   ( "btnENviar"                 );
    $obBtEnviar->obEvento->setOnClick("montaParametrosGET('enviarFormulario','inNumContratoDivida')");
    $obBtEnviar->setTitle( "Clique para enviar"  );
}

$obBtLimpar = new Button();
$obBtLimpar->setValue('Limpar');
$obBtLimpar->setName ( "btnLimpar"                 );
$obBtLimpar->setId   ( "btnLimpar"                 );
$obBtLimpar->obEvento->setOnClick("limparDivida();");
$obBtLimpar->setTitle( "Clique para limpar a dívida a lista"  );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ( $obForm    );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnAno  );
$obFormulario->addHidden            ( $obHdnMes  );
$obFormulario->addHidden            ( $obHdnId   );

if($stAcao == "alterar"){
    $obFormulario->addHidden        ( $obHdnEntidade    );
    $obFormulario->addHidden        ( $obHdnNroContrato );
}

$obFormulario->addTitulo            ( "Dados para configuração do DDC"     );
$obFormulario->addComponente        ( $obEntidadeUsuarioCadastroDeLei      );
$obIPopUpLeiAutorizacao->geraFormulario( $obFormulario                     );
$obFormulario->addComponente        ( $obTxtNumContratoDivida              );
$obFormulario->addComponente        ( $obDtAssinaturaDivida                );
$obFormulario->agrupaComponentes    (array($obLabelContratoDecLei,$obRadioContratoDecLeiSim,$obRadioContratoDecLeiNao));
$obFormulario->addComponente        ( $obTextObjetoContrato                );
$obFormulario->addComponente        ( $obTextDescDivida                    );
$obFormulario->addComponente        ( $obCmbTipoLancamento                 );
$obFormulario->addComponente        ( $obBscCGMCredor                      );
$obFormulario->addComponente        ( $obTextJustificativaCancelamento     );
$obFormulario->addComponente        ( $obFlValorSaldoAnterior              );
$obFormulario->addComponente        ( $obFlValorContratacaoMes             );
$obFormulario->addComponente        ( $obFlValorAmortizacaoMes             );
$obFormulario->addComponente        ( $obFlValorCancelamentoMes            );
$obFormulario->addComponente        ( $obFlValorEncampacaoMes              );
$obFormulario->addComponente        ( $obFlValorAtualizacaoMes             );
$obFormulario->addComponente        ( $obFlValorSaldoAtual                 );

$obOk = new Ok();

if($stAcao == "configurar"){
    $obFormulario->defineBarra(array($obBtnIncluirDivida, $obBtnAlterarDivida, $obBtLimpar));
    $obFormulario->addSpan($obSpnListaDividas);
    $obFormulario->defineBarra(array($obBtEnviar));
}else{
   $obFormulario->defineBarra(array($obOk,$obBtLimpar)); 
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
