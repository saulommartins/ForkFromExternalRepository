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
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoContrato.class.php");
include_once(TLIC."TLicitacaoContratoAditivos.class.php");
include_once(TLIC."TLicitacaoPublicacaoContratoAditivos.class.php");
include_once(TLIC."TLicitacaoTipoTermoAditivo.class.php");
include_once(TLIC."TLicitacaoTipoAlteracaoValor.class.php");

include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

$stAcao = $request->get('stAcao');

$stPrograma = "ManterAditivoContrato";
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

$obTLicitacaoContrato = new TLicitacaoContrato;
$obTLicitacaoContrato->setDado('num_contrato', $request->get("inNumContrato"));
$obTLicitacaoContrato->setDado('cod_entidade', $request->get("inCodEntidade"));
$obTLicitacaoContrato->setDado('exercicio', $request->get("stExercicioContrato"));
$obTLicitacaoContrato->recuperaDadosAditivosCompraDireta($rsLicitacaoContrato);

$obLblNumeroContrato = new Label;
$obLblNumeroContrato->setRotulo('Número do Contrato');
$obLblNumeroContrato->setValue($request->get("inNumContrato")."/".$request->get("stExercicioContrato"));

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($request->get("inCodEntidade")." - ".$rsLicitacaoContrato->getCampo('nom_entidade'));

$obLblObjeto = new Label;
$obLblObjeto->setRotulo('Objeto');
$obLblObjeto->setValue($rsLicitacaoContrato->getCampo('descricao'));

$obLblRespJuridico = new Label;
$obLblRespJuridico->setRotulo('Responsável Jurídico');
$obLblRespJuridico->setValue($rsLicitacaoContrato->getCampo('cgm_responsavel_juridico')." - ".$rsLicitacaoContrato->getCampo('nom_cgm'));

$obLblContratado = new Label;
$obLblContratado->setRotulo('Contratado');
$obLblContratado->setValue($rsLicitacaoContrato->getCampo('cgm_contratado')." - ".$rsLicitacaoContrato->getCampo('nom_contratado'));

$obLblDtAssinatura = new Label;
$obLblDtAssinatura->setRotulo('Data da Assinatura');
$obLblDtAssinatura->setValue($rsLicitacaoContrato->getCampo('dt_assinatura'));

$obLblVencimento = new Label;
$obLblVencimento->setRotulo('Vencimento');
$obLblVencimento->setValue($rsLicitacaoContrato->getCampo('vencimento'));

$obLblVlContratado = new Label;
$obLblVlContratado->setRotulo('Valor Contratado');
$obLblVlContratado->setValue(number_format(str_replace(".", ",", $rsLicitacaoContrato->getCampo('valor_contratado')), 2, ",", "."));

// monta informações dos dados dos aditivos
if ($stAcao != "incluirCD") {
    $obNumeroAditivo = new Label;
    $obNumeroAditivo->setRotulo('Número do Aditivo');
    $obNumeroAditivo->setValue($request->get("inNumeroAditivo")."/".$request->get("stExercicioAditivo"));
}

$dtAssinatura = "";
$inRespJuridico = "";
$dtInicioExcucao = "";
$dtFinalVigencia = "";
$stObjeto = "";
$stFundamentacaoLegal = "";
$vlValorContratado = "";

if ($stAcao != "incluirCD") {
    $obLicitacaoContratoAditivos = new TLicitacaoContratoAditivos;
    $obLicitacaoContratoAditivos->setDado("num_contrato", $request->get('inNumContrato'));
    $obLicitacaoContratoAditivos->setDado("exercicio", $request->get('stExercicioAditivo'));
    $obLicitacaoContratoAditivos->setDado("exercicio_contrato", $request->get('tExercicioContrato'));
    $obLicitacaoContratoAditivos->setDado("cod_entidade", $request->get('inCodEntidade'));
    $obLicitacaoContratoAditivos->setDado("num_aditivo", $request->get('inNumeroAditivo'));
    $obLicitacaoContratoAditivos->recuperaContratosAditivosCompraDireta($rsLicitacaoContratoAditivo);

    $inCodRespJuridico = $rsLicitacaoContratoAditivo->getCampo("responsavel_juridico");
    $stRespJuridico = $rsLicitacaoContratoAditivo->getCampo("cgm_responsavel_juridico");
    $inCodTipoTermo = $rsLicitacaoContratoAditivo->getCampo("tipo_termo_aditivo");
    $stTermoAditivo = $rsLicitacaoContratoAditivo->getCampo("descricao_termo_aditivo");
    $inCodTipoAlteracaoValor = $rsLicitacaoContratoAditivo->getCampo("tipo_valor");
    $stAlteracaoValor = $rsLicitacaoContratoAditivo->getCampo("descricao_tipo_alteracao_valor");
    $dtAssinatura = $rsLicitacaoContratoAditivo->getCampo("dt_assinatura");
    $dtInicioExcucao = $rsLicitacaoContratoAditivo->getCampo("inicio_execucao");
    $dtFimExecucao = $rsLicitacaoContratoAditivo->getCampo("fim_execucao");
    $dtFinalVigencia = $rsLicitacaoContratoAditivo->getCampo("dt_vencimento");
    $stObjeto = $rsLicitacaoContratoAditivo->getCampo("objeto");
    $stJustificativa = $rsLicitacaoContratoAditivo->getCampo("justificativa");
    $stFundamentacaoLegal = $rsLicitacaoContratoAditivo->getCampo("fundamentacao");
    
    $vlValorContratado = number_format(str_replace(".", ",", $rsLicitacaoContratoAditivo->getCampo("valor_contratado")), 2, ",", ".");
}

if ($stAcao != "anularCD") {
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
    $obResponsavelJuridico->obCampoCod->setName( "inCodResponsavelJuridico" );
    $obResponsavelJuridico->obCampoCod->setId( "inCodResponsavelJuridico" );
    $obResponsavelJuridico->obCampoCod->setValue( $inCodRespJuridico );
    $obResponsavelJuridico->obCampoCod->setNull( true );
    $obResponsavelJuridico->setNull( false );

    //recupera os tipos de termos de aditivo
    $obTLicitacaoTipoTermoAditivo = new TLicitacaoTipoTermoAditivo();
    $obTLicitacaoTipoTermoAditivo->recuperaTodos( $rsTipoTermoAditivo, ' ORDER BY cod_tipo ' );
    
    //preenche o select com os tipos de veiculo
    $obTipoTermoAditivo = new Select();
    $obTipoTermoAditivo->setRotulo ( 'Tipo de Termo do Aditivo' );
    $obTipoTermoAditivo->setTitle  ( 'Selecione o tipo de termo.' );
    $obTipoTermoAditivo->setName   ( 'inCodTipoTermoAditivo' );
    $obTipoTermoAditivo->setId     ( 'inCodTipoTermoAditivo' );
    $obTipoTermoAditivo->addOption ( '', 'Selecione' );
    $obTipoTermoAditivo->setCampoId( 'cod_tipo' );
    $obTipoTermoAditivo->setCampoDesc( 'descricao' );
    $obTipoTermoAditivo->preencheCombo( $rsTipoTermoAditivo );
    $obTipoTermoAditivo->setValue( $inCodTipoTermo );
    $obTipoTermoAditivo->setNull   ( false );
    
    //recupera os tipos de valores
    $obTLicitacaoTipoAlteracaoValor = new TLicitacaoTipoAlteracaoValor();
    $obTLicitacaoTipoAlteracaoValor->recuperaTodos( $rsTipoAlteracaoValor, ' ORDER BY cod_tipo ' );
    
    //preenche o select com os tipos de veiculo
    $obTipoAlteracaoValor = new Select();
    $obTipoAlteracaoValor->setRotulo ( 'Tipo de Alteração do Valor' );
    $obTipoAlteracaoValor->setTitle  ( 'Selecione o tipo de alteração do valor.' );
    $obTipoAlteracaoValor->setName   ( 'inCodTipoAlteracaoValor' );
    $obTipoAlteracaoValor->setId     ( 'inCodTipoAlteracaoValor' );
    $obTipoAlteracaoValor->addOption ( '', 'Selecione' );
    $obTipoAlteracaoValor->setCampoId( 'cod_tipo' );
    $obTipoAlteracaoValor->setCampoDesc( 'descricao' );
    $obTipoAlteracaoValor->preencheCombo( $rsTipoAlteracaoValor );
    $obTipoAlteracaoValor->setValue( $inCodTipoAlteracaoValor );
    $obTipoAlteracaoValor->setNull   ( false );
    
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
    $obDtFimExecucao = new Data;
    $obDtFimExecucao->setRotulo('Data de Término da Execução');
    $obDtFimExecucao->setTitle('Informe a final da execução do aditivo.');
    $obDtFimExecucao->setName('dtFimExecucao');
    $obDtFimExecucao->setValue($dtFimExecucao);
    $obDtFimExecucao->setNull(true);

    //monta o campo Data Final de Vigência
    $obDtFinalVigencia = new Data;
    $obDtFinalVigencia->setRotulo('Data Final de Vigência');
    $obDtFinalVigencia->setTitle('Informe a final de vigência do aditivo.');
    $obDtFinalVigencia->setName('dtFinalVigencia');
    $obDtFinalVigencia->setValue($dtFinalVigencia);
    $obDtFinalVigencia->setNull(false);

    $obTxtObjeto = new TextArea;
    $obTxtObjeto->setName('stObjeto');
    $obTxtObjeto->setId('stObjeto');
    $obTxtObjeto->setRotulo('Objeto');
    $obTxtObjeto->setTitle('Informe o objeto do aditivo.');
    $obTxtObjeto->setRows(2);
    $obTxtObjeto->setCols(100);
    $obTxtObjeto->setValue($stObjeto);
    $obTxtObjeto->setNull(false );
    
    $obTxtJustificativa = new TextArea;
    $obTxtJustificativa->setName('stJustificativa');
    $obTxtJustificativa->setId('stJustificativa');
    $obTxtJustificativa->setRotulo('Justificativa');
    $obTxtJustificativa->setTitle('Informe a justificativa do aditivo.');
    $obTxtJustificativa->setRows(2);
    $obTxtJustificativa->setCols(100);
    $obTxtJustificativa->setValue($stJustificativa);
    $obTxtJustificativa->setNull(false );

    $obTxtFundLegal = new TextBox;
    $obTxtFundLegal->setRotulo('Fundamentação Legal');
    $obTxtFundLegal->setTitle('Informe a fundamentação legal do aditivo.');
    $obTxtFundLegal->setName('stFundamentacaoLegal');
    $obTxtFundLegal->setNull(false);
    $obTxtFundLegal->setMaxLength(50);
    $obTxtFundLegal->setSize(60);
    $obTxtFundLegal->setValue($stFundamentacaoLegal);

    $obVlValorContratado = new Moeda;
    $obVlValorContratado->setRotulo('Valor');
    $obVlValorContratado->setTitle('Informe o valor do aditivo.');
    $obVlValorContratado->setName('vlValorContratado');
    $obVlValorContratado->setNull(false);
    $obVlValorContratado->setValue($vlValorContratado);
} else {
    $obResponsavelJuridico = new Label;
    $obResponsavelJuridico->setRotulo('Responsável Jurídico');
    $obResponsavelJuridico->setValue($inCodRespJuridico." - ".$stRespJuridico);
    
    $obTipoTermoAditivo = new Label;
    $obTipoTermoAditivo->setRotulo('Tipo de Termo do Aditivo');
    $obTipoTermoAditivo->setValue($inCodTipoTermo." - ".$stTermoAditivo);
    
    $obTipoAlteracaoValor = new Label;
    $obTipoAlteracaoValor->setRotulo('Tipo de Alteração do Valor');
    $obTipoAlteracaoValor->setValue($inCodTipoAlteracaoValor." - ".$stAlteracaoValor);

    $obDtAssinatura = new Label;
    $obDtAssinatura->setRotulo('Data da Assinatura');
    $obDtAssinatura->setValue($dtAssinatura);

    $obDtInicioExecucao = new Label;
    $obDtInicioExecucao->setRotulo('Data de Início de Execução');
    $obDtInicioExecucao->setValue($dtInicioExcucao);

    $obDtFimExecucao = new Label;
    $obDtFimExecucao->setRotulo('Data de Término da Execução');
    $obDtFimExecucao->setValue($dtFimExecucao);
    
    $obDtFinalVigencia = new Label;
    $obDtFinalVigencia->setRotulo('Data Final de Vigência');
    $obDtFinalVigencia->setValue($dtFinalVigencia);

    $obTxtObjeto = new Label;
    $obTxtObjeto->setRotulo('Objeto');
    $obTxtObjeto->setValue($stObjeto);
    
    $obTxtJustificativa = new Label;
    $obTxtJustificativa->setRotulo('Justificativa');
    $obTxtJustificativa->setValue($stJustificativa);

    $obTxtFundLegal = new Label;
    $obTxtFundLegal->setRotulo('Fundamentação Legal');
    $obTxtFundLegal->setValue($stFundamentacaoLegal);

    $obVlValorContratado = new Label;
    $obVlValorContratado->setRotulo('Valor');
    $obVlValorContratado->setValue($vlValorContratado);

    $obVlValorAnulacao = new Moeda;
    $obVlValorAnulacao->setRotulo('Valor Anulação');
    $obVlValorAnulacao->setTitle('Informe o valor da anulação.');
    $obVlValorAnulacao->setName('vlValorAnulacao');
    $obVlValorAnulacao->setNull(false);
    
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
$obHdnInNumContrato = new Hidden;
$obHdnInNumContrato->setName( "inNumContrato" );
$obHdnInNumContrato->setValue( $request->get("inNumContrato") );

$obHdnStExercicio = new Hidden;
$obHdnStExercicio->setName( "stExercicioContrato" );
$obHdnStExercicio->setValue( $request->get("stExercicioContrato") );

$obHdnInCodEntidade = new Hidden;
$obHdnInCodEntidade->setName( "inCodEntidade" );
$obHdnInCodEntidade->setValue( $request->get("inCodEntidade") );

if ($stAcao != "incluirCD") {
    $obHdnInNumeroAditivo = new Hidden;
    $obHdnInNumeroAditivo->setName( "inNumeroAditivo" );
    $obHdnInNumeroAditivo->setValue( $request->get("inNumeroAditivo") );

    $obHdnStExercicioAditivo = new Hidden;
    $obHdnStExercicioAditivo->setName( "stExercicioAditivo" );
    $obHdnStExercicioAditivo->setValue( $request->get("stExercicioAditivo") );
}

if ($stAcao == 'alterarCD') {
    //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
    $obTLicitacaoPublicacaoContrato = new TLicitacaoPublicacaoContratoAditivos();
    $obTLicitacaoPublicacaoContrato->setDado('num_contrato', $request->get('inNumContrato'));
    $obTLicitacaoPublicacaoContrato->setDado('exercicio', Sessao::getExercicio());
    $obTLicitacaoPublicacaoContrato->setDado('exercicio_contrato', $request->get("stExercicioContrato"));
    $obTLicitacaoPublicacaoContrato->setDado('cod_entidade', $request->get('inCodEntidade'));
    $obTLicitacaoPublicacaoContrato->setDado('num_aditivo', $request->get('inNumeroAditivo'));

    $inCount = 0;
    $arValores = array();
    
    $obTLicitacaoPublicacaoContrato->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );

    while ( !$rsVeiculosPublicacao->eof() ) {
        $arValores[$inCount]['id'            ]   = $inCount + 1;
        $arValores[$inCount]['inVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
        $arValores[$inCount]['stVeiculo'     ]   = $rsVeiculosPublicacao->getCampo( 'nom_veiculo');
        $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao');
        $arValores[$inCount]['inNumPublicacao']  = $rsVeiculosPublicacao->getCampo( 'num_publicacao');
        $arValores[$inCount]['stObservacao'  ]   = $rsVeiculosPublicacao->getCampo( 'observacao');
        $inCount++;
        $rsVeiculosPublicacao->proximo();
    }
}

Sessao::write('arValores', $arValores);

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
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ( "stObservacao"                               );
$obTxtObservacao->setName   ( "stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setObrigatorioBarra( false                               );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMaxCaracteres( 80 );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnInNumContrato );
$obFormulario->addHidden( $obHdnStExercicio );
$obFormulario->addHidden( $obHdnInCodEntidade );

if ($stAcao != "incluirCD") {
    $obFormulario->addHidden( $obHdnInNumeroAditivo );
    $obFormulario->addHidden( $obHdnStExercicioAditivo );
}

$obFormulario->addTitulo( "Dados do Contrato" );
$obFormulario->addComponente( $obLblNumeroContrato );
$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblObjeto );
$obFormulario->addComponente( $obLblRespJuridico );
$obFormulario->addComponente( $obLblContratado );
$obFormulario->addComponente( $obLblDtAssinatura );
$obFormulario->addComponente( $obLblVencimento );
$obFormulario->addComponente( $obLblVlContratado );

$obFormulario->addTitulo( "Dados do Aditivo" );

if ($stAcao != "incluirCD") {
    $obFormulario->addComponente( $obNumeroAditivo );
}

$obFormulario->addComponente( $obResponsavelJuridico );
$obFormulario->addComponente( $obTipoTermoAditivo );
$obFormulario->addComponente( $obTipoAlteracaoValor );
$obFormulario->addComponente( $obDtAssinatura );
$obFormulario->addComponente( $obDtInicioExecucao );
$obFormulario->addComponente( $obDtFimExecucao );
$obFormulario->addComponente( $obDtFinalVigencia );
$obFormulario->addComponente( $obTxtObjeto );
$obFormulario->addComponente( $obTxtJustificativa );
$obFormulario->addComponente( $obTxtFundLegal );
$obFormulario->addComponente( $obVlValorContratado );


if ($stAcao == "anularCD") {
    $obFormulario->addTitulo( "Dados da Anulação do Aditivo" );
    $obFormulario->addComponente( $obVlValorAnulacao );
    $obFormulario->addComponente( $obDataAnulacao );
    $obFormulario->addComponente( $obTxtMotivoAnulacao );
}

$obFormulario->addTitulo    ( 'Veículo de Publicação' );
$obFormulario->addComponente( $obVeiculoPublicidade );
$obFormulario->addComponente( $obDataPublicacao );
$obFormulario->addComponente( $obNumeroPublicacao );
$obFormulario->addComponente( $obTxtObservacao );
$obFormulario->defineBarra  ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan      ( $obSpnListaVeiculo );
$obFormulario->addHidden    ( $obHdnCodVeiculo );

if ($stAcao == "incluirCD") {
    $obFormulario->Ok();

} else {

    $dadosFiltro = Sessao::read('dadosFiltro',$param);
    foreach ($dadosFiltro as $chave =>$valor) {
        $stFiltro.= "&".$chave."=".$valor;
    }

    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obFormulario->Cancelar($stLocation);
}

$obFormulario->show();

$jsOnLoad = "";
if ($stAcao == 'alterarCD' || $stAcao == 'anularCD') {
    $jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculos');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
