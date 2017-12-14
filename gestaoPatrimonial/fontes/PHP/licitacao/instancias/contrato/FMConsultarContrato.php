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
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.Label" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Formulário para cadastro de documentos exigidos
    * Data de Criação   : 06/10/2006

    * @author Leandro André Zis

    * $Id: FMConsultarContrato.php 65317 2016-05-12 17:40:05Z carlos.silva $

    * Casos de uso : uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoContrato.class.php";
include_once TLIC."TLicitacaoTipoContrato.class.php";
include_once TLIC."TLicitacaoContratoDocumento.class.php";
include_once TLIC."TLicitacaoPublicacaoContrato.class.php";
include_once TCOM."TComprasFornecedor.class.php";
include_once TLIC."TLicitacaoDocumentosAtributos.class.php";
include_once TLIC."TLicitacaoContratoArquivo.class.php";
include_once TLIC."TLicitacaoContratoAditivos.class.php";
include_once TLIC."TLicitacaoTipoTermoAditivo.class.php";
include_once TLIC."TLicitacaoTipoAlteracaoValor.class.php";
include_once TLIC."TLicitacaoContratoAditivosAnulacao.class.php";
include_once TLIC."TLicitacaoContratoAditivosAnulacao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoContrato.class.php";

include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";
include_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
include_once CAM_GP_LIC_COMPONENTES."IPopUpLicitacao.class.php";
include_once CAM_GP_LIC_COMPONENTES."ISelectDocumento.class.php";
include_once CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php";
include_once CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php";
include_once CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacao.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasTipoObjeto.class.php";

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);

Sessao::remove('arValores');
Sessao::remove('arArquivos');

$stAcao = $request->get('stAcao');
$inNumContrato = $request->get('inNumContrato');
$inCodEntidade = $request->get('inCodEntidade');
$stExercicio = $request->get('stExercicio');

$stURL .= "&pos=".Sessao::read('pos');
$stURL .= "&pg=".Sessao::read('pg');
$stURL .= "&paginando=".Sessao::read('paginando');

$obTLicitacaoTipoContrato = new TLicitacaoTipoContrato();
$obTLicitacaoTipoContrato->recuperaTodos( $rsTipoContrato, ' WHERE cod_tipo IN (43,38,19,46,20,35,27,23,42,10,12,14,6,1,39,28,16,4,18,26,30,24,45,8,34,31,32,33,3,22) ', ' ORDER BY descricao ' );

if ($inNumContrato) {
  $obTContrato = new TLicitacaoContrato();
  $obTContrato->setDado('num_contrato', $inNumContrato);
  $obTContrato->setDado('cod_entidade', $inCodEntidade);
  $obTContrato->setDado('exercicio', $stExercicio);
  $obTContrato->recuperaRelacionamento($rsContrato);

  $inCodLicitacao = $rsContrato->getCampo('cod_licitacao');
  $inCodModalidade = $rsContrato->getCampo('cod_modalidade');
  $stModalidade = $rsContrato->getCampo('modalidade');
  $stDescObjeto = $rsContrato->getCampo('descricao');
  $inCGMResponsavelJuridico = $rsContrato->getCampo('cgm_responsavel_juridico');
  $stLabelAssinatura = $rsContrato->getCampo('dt_assinatura');
  $stLabelVencimento = $rsContrato->getCampo('vencimento');
  $dtFimExecucao    = $rsContrato->getCampo('fim_execucao');
  $dtInicioExecucao = $rsContrato->getCampo('inicio_execucao');
  $inCGMContratado = $rsContrato->getCampo('cgm_contratado');
  $stNomContratado = $rsContrato->getCampo('nom_contratado');
  $stNomCGM = $rsContrato->getCampo('nom_cgm');
  $inCodDocumento = $rsContrato->getCampo('cod_documento');
  $inCodTipoDocumento = $rsContrato->getCampo('cod_tipo_documento');
  $nmValorGarantiaExecucao = number_format($rsContrato->getCampo('valor_garantia'),2,',','.');
  $vlContrato = number_format($rsContrato->getCampo('valor_contratado'),2,',','.');
  $stTipoContrato = $rsContrato->getCampo('tipo_descricao');
  $stExercicioContrato  = $rsContrato->getCampo('exercicio');
  $stExercicioLicitacao = $rsContrato->getCampo('exercicio_licitacao');
  
  $stNomEntidade = $rsContrato->getCampo('nom_entidade');
  $inNumOrgao = $rsContrato->getCampo('num_orgao');
  $stNomOrgao = $rsContrato->getCampo('nom_orgao');
  $inNumUnidade = $rsContrato->getCampo('num_unidade');
  $stNomUnidade = $rsContrato->getCampo('nom_unidade');
  $inNumeroContrato = $rsContrato->getCampo('numero_contrato');
  $stObjeto = $rsContrato->getCampo('objeto');
  $inCodTipoObjeto = $rsContrato->getCampo('cod_tipo_objeto');
  $stTipoObjeto = $rsContrato->getCampo('tipo_objeto');
  $stObjeto = $rsContrato->getCampo('objeto');
  $stFormaFornecimento = $rsContrato->getCampo('forma_fornecimento');
  $stFormaPagamento = $rsContrato->getCampo('forma_pagamento');
  $inCGMSignatario = $rsContrato->getCampo('cgm_signatario');
  $stCGMSignatario = $rsContrato->getCampo('nom_signatario');
  $stPrazoExecucao = $rsContrato->getCampo('prazo_execucao');
  $stMultaRescisoria = $rsContrato->getCampo('multa_rescisoria');
  $stJustificativa = $rsContrato->getCampo('justificativa');
  $stRazao = $rsContrato->getCampo('razao');
  $stFundamentacaoLegal = $rsContrato->getCampo('fundamentacao_legal');

  $obTContratoDocumento = new TLicitacaoContratoDocumento;
  $obTContratoDocumento->setDado('num_contrato', $inNumContrato);
  $obTContratoDocumento->setDado('cod_entidade', $inCodEntidade);
  $obTContratoDocumento->setDado('exercicio', $stExercicio);
  $obTContratoDocumento->recuperaDocumentos($rsDocumentos);
  $arDocumentos = array();
  $inCount = 0;

  while (!$rsDocumentos->eof()) {
     $arDados = array();
     $arDados['boNovo'] = false;
     $arDados['id'            ] = $inCount + 1;
     $arDados['inCodDocumento'] = $rsDocumentos->getCampo('cod_documento');
     $arDados['dtValidade'] = $rsDocumentos->getCampo('dt_validade');
     $arDados['dtEmissao'] = $rsDocumentos->getCampo('dt_emissao');
     $arDados['stNumDocumento'] = $rsDocumentos->getCampo('num_documento');
     $arDados['stNomDocumento'] = $rsDocumentos->getCampo('nom_documento');
     $arDocumentos[] = $arDados;
     $rsDocumentos->proximo();
     $inCount++;
  }
  Sessao::write('arDocumentos', $arDocumentos);

  //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
  $obTLicitacaoPublicacaoContrato = new TLicitacaoPublicacaoContrato();
  $obTLicitacaoPublicacaoContrato->setDado('num_contrato', $inNumContrato);
  $obTLicitacaoPublicacaoContrato->setDado('exercicio', $stExercicio);
  $obTLicitacaoPublicacaoContrato->setDado('cod_entidade', $inCodEntidade);
  $obTLicitacaoPublicacaoContrato->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );
  $inCount = 0;
  $arValores = array();
  while ( !$rsVeiculosPublicacao->eof() ) {
      $arValores[$inCount]['id'            ] = $inCount + 1;
      $arValores[$inCount]['inVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
      $arValores[$inCount]['stVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'nom_veiculo');
      $arValores[$inCount]['dtLabelPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao');
      $arValores[$inCount]['inNumPublicacao'] = $rsVeiculosPublicacao->getCampo( 'num_publicacao');
      $arValores[$inCount]['stObservacao'  ] = $rsVeiculosPublicacao->getCampo( 'observacao');
      $arValores[$inCount]['inCodLicitacao'] = $rsVeiculosPublicacao->getCampo( 'cod_licitacao');
      $inCount++;
      $rsVeiculosPublicacao->proximo();
  }
  Sessao::write('arValores', $arValores);

  //recupera os arquivos digitais
  $stFiltro = " WHERE num_contrato = ".$inNumContrato." and cod_entidade = ".$inCodEntidade." and exercicio = '".$stExercicioContrato."' ";
  $obTLicitacaoContratoArquivo = new TLicitacaoContratoArquivo;
  $obTLicitacaoContratoArquivo->recuperaTodos($rsContratoArquivo, $stFiltro);
  $inCount = 0;
  $arArquivos = array();
  while ( !$rsContratoArquivo->eof() ) {
      $arArquivos[$inCount]['id'       ]   = $inCount + 1;
      $arArquivos[$inCount]['arquivo']     = $rsContratoArquivo->getCampo( 'arquivo' );
      $arArquivos[$inCount]['nom_arquivo'] = $rsContratoArquivo->getCampo( 'nom_arquivo' );
      $arArquivos[$inCount]['num_contrato'] = $rsContratoArquivo->getCampo( 'num_contrato' );
      $arArquivos[$inCount]['cod_entidade'] = $rsContratoArquivo->getCampo( 'cod_entidade' );
      $arArquivos[$inCount]['exercicio'] = $rsContratoArquivo->getCampo( 'exercicio' );
      $inCount++;
      $rsContratoArquivo->proximo();
  }
  Sessao::write('arArquivos', $arArquivos);
} else {
  $inCodLicitacao = "";
  $inCodModalidade = "";
  $stDescObjeto = "";
  $inCGMResponsavelJuridico = "";
  $stLabelAssinatura = "";
  $stLabelVencimento = "";
  $dtFimExecucao    = "";
  $dtInicioExecucao = "";
  $inCGMContratado = "";
  $stNomContratado = "";
  $stNomCGM = "";
  $inCodDocumento = "";
  $inCodTipoDocumento = "";
  $nmValorGarantiaExecucao = "";
  $vlContrato = "";
  $stTipoContrato = "";
  $stExercicioContrato  = "";
  $stExercicioLicitacao = "";
}

$obForm = new Form;
$obForm->setAction   ( $pgProc );
$obForm->setTarget   ( "oculto" );
$obForm->setEncType  ( "multipart/form-Label" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLabelOrgao = new Label;
$obLabelOrgao->setRotulo( "Órgão" );
$obLabelOrgao->setTitle( "Selecione o orgão orçamentário." );
$obLabelOrgao->setName( "inNumOrgaoLabel" );
$obLabelOrgao->setId( "inNumOrgaoLabel" );
$obLabelOrgao->setValue( $inNumOrgao.'-'.$stNomOrgao );

$obLabelUnidade = new Label;
$obLabelUnidade->setRotulo( "Unidade" );
$obLabelUnidade->setTitle( "Selecione a unidade orçamentária." );
$obLabelUnidade->setName( "inNumUnidadeLabel" );
$obLabelUnidade->setId( "inNumUnidadeLabel" );
$obLabelUnidade->setValue( $inNumUnidade.'-'.$stNomUnidade );

$obLabelTipoObjeto = new Label();
$obLabelTipoObjeto->setName     ( 'inCodTipoObjeto' );
$obLabelTipoObjeto->setRotulo   ( 'Tipo de Objeto' );
$obLabelTipoObjeto->setTitle    ( 'Selecione o Tipo de Objeto.' );
$obLabelTipoObjeto->setValue    (  $inCodTipoObjeto.'-'.$stTipoObjeto );

$obLabelNumeroContrato = new Label;
$obLabelNumeroContrato->setName  ( "inNumeroContrato" );
$obLabelNumeroContrato->setId  ( "inNumeroContrato" );
$obLabelNumeroContrato->setRotulo( "Número do Contrato" );
$obLabelNumeroContrato->setTitle ( "Informe o número do contrato." );
$obLabelNumeroContrato->setValue( $inNumeroContrato);

$obLblModalidade= new Label;
$obLblModalidade->setRotulo ( "Modalidade" );
$obLblModalidade->setValue  ( $stModalidade );

$obLblNumeroLicitacao= new Label;
$obLblNumeroLicitacao->setRotulo    ( "Número da Licitação" );
$obLblNumeroLicitacao->setValue     ( $inCodLicitacao);

$obCmbTipoContrato = new Select();
$obCmbTipoContrato->setRotulo( 'Tipo de contrato' );
$obCmbTipoContrato->setTitle( 'Selecione o tipo de contrato' );
$obCmbTipoContrato->setName( 'inTipoContrato' );
$obCmbTipoContrato->setId( 'inTipoContrato' );
$obCmbTipoContrato->addOption( '', 'Selecione' );
$obCmbTipoContrato->setCampoId( 'cod_tipo' );
$obCmbTipoContrato->setCampoDesc( 'descricao' );
$obCmbTipoContrato->setStyle('width: 300');
$obCmbTipoContrato->setNull(false);
$obCmbTipoContrato->preencheCombo( $rsTipoContrato );

$obLblDescObjeto = new Label;
$obLblDescObjeto->setRotulo ( "Objeto" );
$obLblDescObjeto->setId     ( 'stDescObjeto');
$obLblDescObjeto->setValue  ( $stDescObjeto );

$obLblTipoContrato = new Label;
$obLblTipoContrato->setRotulo ( "Tipo de Contrato");
$obLblTipoContrato->setValue ( $stTipoContrato );

$obLblExercicioContrato = new Label;
$obLblExercicioContrato->setRotulo ( "Exercício do Contrato");
$obLblExercicioContrato->setValue ( $stExercicioContrato );

$obLblExercicioLicitacao = new Label;
$obLblExercicioLicitacao->setRotulo ( "Exercício da Licitação");
$obLblExercicioLicitacao->setValue ( $stExercicioLicitacao );

$obLblEntidade = new Label;
$obLblEntidade->setRotulo ( "Entidade");
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );

# Campo Chave
$obLblResponsavelJuridico = new Label;
$obLblResponsavelJuridico->setRotulo('Responsável Jurídico');
$obLblResponsavelJuridico->setValue($inCGMResponsavelJuridico.'-'.$stNomCGM);

$obLblContratado = new Label;
$obLblContratado->setRotulo('Contratado');
$obLblContratado->setValue($inCGMContratado.'-'.$stNomContratado);

$obLabelObjeto = new Label;
$obLabelObjeto->setId     ( "stObjeto" );
$obLabelObjeto->setName   ( "stObjeto" );
$obLabelObjeto->setRotulo ( "Objeto do Contrato" );
$obLabelObjeto->setTitle  ( "Informe todos detalhes do contrato.");
$obLabelObjeto->setValue( $stObjeto );

$obLabelFormaFornecimento = new Label;
$obLabelFormaFornecimento->setId     ( "stFormaFornecimento" );
$obLabelFormaFornecimento->setName   ( "stFormaFornecimento" );
$obLabelFormaFornecimento->setRotulo ( "Forma de Fornecimento" );
$obLabelFormaFornecimento->setTitle  ( "Descrição da forma de fornecimento ou regime de execução, conforme previsão do art. 55, II, da Lei Federal n. 8.666/93.");
$obLabelFormaFornecimento->setValue( $stFormaFornecimento );

$obLabelFormaPagamento = new Label;
$obLabelFormaPagamento->setId     ( "stFormaPagamento" );
$obLabelFormaPagamento->setName   ( "stFormaPagamento" );
$obLabelFormaPagamento->setRotulo ( "Forma de Pagamento" );
$obLabelFormaPagamento->setTitle  ( "Descrever o preço e as condições de pagamento, os critérios, Label-base e periodicidade do reajustamento de preços, os critérios de atualização monetária entre a Label do adimplemento das obrigações e a do efetivo pagamento, conforme previsão do art. 55, III, da Lei Federal n. 8.666/93" );
$obLabelFormaPagamento->setValue( $stFormaPagamento );

$obCGMSignatario = new Label;
$obCGMSignatario->setName   ( 'stCGMSignatario');
$obCGMSignatario->setId     ( 'stCGMSignatario');
$obCGMSignatario->setRotulo ( 'CGM Signatário' );
$obCGMSignatario->setValue  ( $inCGMSignatario.'-'.$stCGMSignatario );

$obLabelPrazoExecucao = new Label;
$obLabelPrazoExecucao->setId     ( "stPrazoExecucao" );
$obLabelPrazoExecucao->setName   ( "stPrazoExecucao" );
$obLabelPrazoExecucao->setRotulo ( "Prazo de Execução" );
$obLabelPrazoExecucao->setTitle  ( "Os prazos de início de etapas de execução, de conclusão, de entrega, de observação e de recebimento definitivo, conforme o caso, de acordo com a previsão do art. 55, IV, da Lei Federal n. 8.666/93." );
$obLabelPrazoExecucao->setValue ( $stPrazoExecucao );

$obLabelMultaRescisoria = new Label;
$obLabelMultaRescisoria->setId     ( "stMultaRescisoria" );
$obLabelMultaRescisoria->setName   ( "stMultaRescisoria" );
$obLabelMultaRescisoria->setRotulo ( "Multa Rescisória" );
$obLabelMultaRescisoria->setTitle  ( "Descrição da previsão de multa rescisória, conforme previsão do art. 55, VII, da Lei Federal n. 8.666/93." );
$obLabelMultaRescisoria->setValue ( $stMultaRescisoria );

$obLabelExercicioContrato = new Label;
$obLabelExercicioContrato->setName  ( "stExercicioContrato" );
$obLabelExercicioContrato->setId  ( "stExercicioContrato" );
$obLabelExercicioContrato->setRotulo( "Exercício do Contrato" );
$obLabelExercicioContrato->setValue( Sessao::getExercicio());

$obLabelLabelAssinatura = new Label;
$obLabelLabelAssinatura->setName('dtAssinatura');
$obLabelLabelAssinatura->setValue($stLabelAssinatura);
$obLabelLabelAssinatura->setRotulo('Data da Assinatura');
$obLabelLabelAssinatura->setTitle('Informe a Data da assinatura.');

$obLabelVencimento = new Label;
$obLabelVencimento->setName('dtVencimento');
$obLabelVencimento->setValue($stLabelVencimento);
$obLabelVencimento->setRotulo('Vencimento');
$obLabelVencimento->setTitle('Informe o vencimento do contrato.');

$obLabelLabelInicioExecucao = new Label;
$obLabelLabelInicioExecucao->setName   ( 'dtInicioExecucao'                     );
$obLabelLabelInicioExecucao->setId     ( 'dtInicioExecucao'                     );
$obLabelLabelInicioExecucao->setValue  ( $dtInicioExecucao                      );
$obLabelLabelInicioExecucao->setRotulo ( 'Data de Início de Execução'           );
$obLabelLabelInicioExecucao->setTitle  ( 'Informe a Data de início de execução.');

$obLabelLabelFimExecucao = new Label;
$obLabelLabelFimExecucao->setName   ( 'dtFimExecucao'                     );
$obLabelLabelFimExecucao->setId     ( 'dtFimExecucao'                     );
$obLabelLabelFimExecucao->setValue  ( $dtFimExecucao                      );
$obLabelLabelFimExecucao->setRotulo ( 'Data de Fim de Execução'           );
$obLabelLabelFimExecucao->setTitle  ( 'Informe a Data de fim de execução.');

$obLabelValorContrato = new Label;
$obLabelValorContrato->setName  ( "vlContrato" );
$obLabelValorContrato->setId  ( "vlContrato" );
$obLabelValorContrato->setRotulo( "Valor do Contrato" );
$obLabelValorContrato->setNull( false );
if ($vlContrato == '') {
    $vlContrato = '0,00';
}
$obLabelValorContrato->setValue( $vlContrato );

$obLabelValorGarantiaExecucao = new Label();
$obLabelValorGarantiaExecucao->setNull(false);
if ($nmValorGarantiaExecucao == '') {
    $nmValorGarantiaExecucao = '0,00';
}
$obLabelValorGarantiaExecucao->setValue($nmValorGarantiaExecucao);
$obLabelValorGarantiaExecucao->setName('nmValorGarantiaExecucao');
$obLabelValorGarantiaExecucao->setRotulo('Valor da Garantia de Execução');
$obLabelValorGarantiaExecucao->setTitle('Informe o valor da garantia de execução.');

$obLabelJustificativa = new Label;
$obLabelJustificativa->setId     ( "stJustificativa" );
$obLabelJustificativa->setName   ( "stJustificativa" );
$obLabelJustificativa->setRotulo ( "Justificativa" );
$obLabelJustificativa->setTitle  ( "Informe a Justificativa." );
$obLabelJustificativa->setValue ( $stJustificativa );

$obLabelRazao = new Label;
$obLabelRazao->setId     ( "stRazao" );
$obLabelRazao->setName   ( "stRazao" );
$obLabelRazao->setRotulo ( "Razão" );
$obLabelRazao->setTitle  ( "Informe a razão." );
$obLabelRazao->setValue ( $stRazao );

$obLabelFundamentacaoLegal = new Label;
$obLabelFundamentacaoLegal->setId     ( "stFundamentacaoLegal" );
$obLabelFundamentacaoLegal->setName   ( "stFundamentacaoLegal" );
$obLabelFundamentacaoLegal->setRotulo ( "Fundamentação Legal" );
$obLabelFundamentacaoLegal->setTitle  ( "Informe a Fundamentação Legal." );
$obLabelFundamentacaoLegal->setValue ( $stFundamentacaoLegal );

$obChkImprimirContrato = new CheckBox;
$obChkImprimirContrato->setRotulo('Imprimir Contrato');
$obChkImprimirContrato->setName('boImprimirContrato');
$obChkImprimirContrato->setTitle('Deseja Imprimir o contrato?');

$obLabelEmissao = new Label();
$obLabelEmissao->setName('stLabelEmissao');
$obLabelEmissao->setId('stLabelEmissao');
$obLabelEmissao->setRotulo('Data de Emissão');
$obLabelEmissao->setValue($request->get('stLabelEmissao'));

$obLabelValidade = new Label();
$obLabelValidade->setName ( "stLabelValidade" );
$obLabelValidade->setId ( "stLabelValidade" );
$obLabelValidade->setValue( $request->get('stLabelValidade') );
$obLabelValidade->setRotulo( "Data de Validade" );
$obLabelValidade->setTitle( "Informe a Data de Validade do Documento." );

$obLabelNumDiasVcto = new Label;
$obLabelNumDiasVcto->setName  ( "inNumDiasValido" );
$obLabelNumDiasVcto->setId  ( "inNumDiasValido" );
$obLabelNumDiasVcto->setRotulo( "Dias para Vencimento" );
$obLabelNumDiasVcto->setTitle ( "Informe o número de dias para o vencimento do documento." );
$obLabelNumDiasVcto->setValue ( $request->get('inNumDiasValido') );

$obLabelNumDocumento = new Label;
$obLabelNumDocumento->setName  ( "stNumDocumento" );
$obLabelNumDocumento->setId  ( "stNumDocumento" );
$obLabelNumDocumento->setRotulo( "Número do Documento" );
$obLabelNumDocumento->setTitle ( "Informe o número do documento." );

$obSpnAtributosDocumento = new Span;
$obSpnAtributosDocumento->setId('spnAtributosDocumento');

$obSpnListaDocumentos = new Span;
$obSpnListaDocumentos->setId('spnListaDocumentos');

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

/****************************************************************************************************************************/
//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaArquivo = new Span;
$obSpnListaArquivo->setID("spnListaArquivos");
/****************************************************************************************************************************/

$jsOnLoad = "";
$jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaDocumentos&consultar=1'); \n";
$jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculos&consultar=1'); \n";
$jsOnLoad.= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaArquivos&num_contrato=".$inNumContrato."&exercicio=".$stExercicioLicitacao."&cod_entidade=".$inCodEntidade."&consultar=1'); \n ";

//define o formulário
$obFormulario = new FormularioAbas;
$obFormulario->addForm          ( $obForm     );
$obFormulario->setAjuda         ("UC-03.05.22");
$obFormulario->addHidden        ( $obHdnCtrl  );
$obFormulario->addHidden        ( $obHdnAcao  );

//Aba contrato
$obFormulario->addAba("Contrato");
$obFormulario->addTitulo        ( "Dados do Contrato"   );

$obFormulario->addComponente    ( $obLblTipoContrato );
$obFormulario->addComponente    ( $obLblExercicioContrato );
$obFormulario->addComponente    ( $obLblExercicioLicitacao );
$obFormulario->addComponente    ( $obLblEntidade );

$obFormulario->addComponente    ( $obLabelOrgao );
$obFormulario->addComponente    ( $obLabelUnidade );

$obFormulario->addComponente    ( $obLblModalidade );
$obFormulario->addComponente    ( $obLblNumeroLicitacao );
$obFormulario->addComponente    ( $obLabelTipoObjeto );

$obFormulario->addComponente    ( $obLabelNumeroContrato );
$obFormulario->addComponente    ( $obLblDescObjeto );
$obFormulario->addComponente    ( $obLblResponsavelJuridico );
$obFormulario->addComponente   ( $obLblContratado );

$obFormulario->addComponente    ( $obLabelObjeto );
$obFormulario->addComponente    ( $obLabelFormaFornecimento );
$obFormulario->addComponente    ( $obLabelFormaPagamento );
$obFormulario->addComponente    ( $obCGMSignatario );
$obFormulario->addComponente    ( $obLabelPrazoExecucao );
$obFormulario->addComponente    ( $obLabelMultaRescisoria );

$obFormulario->addComponente    ( $obLabelLabelAssinatura );
$obFormulario->addComponente    ( $obLabelVencimento  );
$obFormulario->addComponente    ( $obLabelLabelInicioExecucao );
$obFormulario->addComponente    ( $obLabelLabelFimExecucao );
$obFormulario->addComponente    ( $obLabelValorContrato );
$obFormulario->addComponente    ( $obLabelValorGarantiaExecucao );

$obFormulario->addComponente    ( $obLabelJustificativa );
$obFormulario->addComponente    ( $obLabelRazao );
$obFormulario->addComponente    ( $obLabelFundamentacaoLegal );

$obFormulario->addSpan          ( $obSpnAtributosDocumento );
$obFormulario->addSpan          ( $obSpnListaDocumentos );
$obFormulario->addSpan          ( $obSpnListaVeiculo );
$obFormulario->addSpan          ( $obSpnListaArquivo );

//////////ADITIVOS
$obFormulario->addAba    ("Aditivos");
$obFormulario->addTitulo ( "Lista de Aditivos" );

$obLicitacaoContratoAditivos = new TLicitacaoContratoAditivos;
$obLicitacaoContratoAditivos->setDado("numero_contrato"   , $request->get('inNumContrato')      );
$obLicitacaoContratoAditivos->setDado("exercicio_contrato", $request->get('stExercicioContrato'));
$obLicitacaoContratoAditivos->setDado("cod_entidade"      , $request->get('inCodEntidade')      );
$obLicitacaoContratoAditivos->recuperaContratosAditivosLicitacao($rsLicitacaoContratoAditivo);

foreach($rsLicitacaoContratoAditivo->getElementos() as $aditivo) {
    $obFormulario->addTitulo ( "Aditivo" );

    $inCodRespJuridico = $aditivo["responsavel_juridico"];
    $stRespJuridico = $aditivo["cgm_responsavel_juridico"];
    $inCodTipoTermo = $aditivo["tipo_termo_aditivo"];
    $stTermoAditivo = $aditivo["descricao_termo_aditivo"];
    $inCodTipoAlteracaoValor = $aditivo["tipo_valor"];
    $stAlteracaoValor = $aditivo["descricao_tipo_alteracao_valor"];
    $dtAssinatura = $aditivo["dt_assinatura"];
    $dtInicioExcucao = $aditivo["inicio_execucao"];
    $dtFimExecucao = $aditivo["fim_execucao"];
    $dtFinalVigencia = $aditivo["dt_vencimento"];
    $stObjeto = $aditivo["objeto"];
    $stJustificativa = $aditivo["justificativa"];
    $stFundamentacaoLegal = $aditivo["fundamentacao"];
    $vlValorContratado = number_format($aditivo["valor_contratado"],2,',','.');
    
    $obResponsavelJuridico = new Label;
    $obResponsavelJuridico->setRotulo( 'Responsável Jurídico' );
    $obResponsavelJuridico->setName( 'stResponsavelJuridico');
    $obResponsavelJuridico->setId( 'stResponsavelJuridico');
    $obResponsavelJuridico->setValue( $inCodRespJuridico.' - '.$stRespJuridico );
    
    $obTLicitacaoTipoTermoAditivo = new TLicitacaoTipoTermoAditivo();
    $obTLicitacaoTipoTermoAditivo->recuperaTodos( $rsTipoTermoAditivo, ' ORDER BY cod_tipo ' );

    $obTipoTermoAditivo = new Label;
    $obTipoTermoAditivo->setRotulo ( 'Tipo de Termo do Aditivo' );
    $obTipoTermoAditivo->setName   ( 'inCodTipoTermoAditivo' );
    $obTipoTermoAditivo->setId     ( 'inCodTipoTermoAditivo' );
    $obTipoTermoAditivo->setValue( $inCodTipoTermo.' - '.$stTermoAditivo);
    
    $obTLicitacaoTipoAlteracaoValor = new TLicitacaoTipoAlteracaoValor();
    $obTLicitacaoTipoAlteracaoValor->recuperaTodos( $rsTipoAlteracaoValor, ' ORDER BY cod_tipo ' );
    
    $obTipoAlteracaoValor = new Label;
    $obTipoAlteracaoValor->setRotulo ( 'Tipo de Alteração do Valor' );
    $obTipoAlteracaoValor->setName   ( 'inCodTipoAlteracaoValor' );
    $obTipoAlteracaoValor->setId     ( 'inCodTipoAlteracaoValor' );
    $obTipoAlteracaoValor->setValue( $inCodTipoAlteracaoValor.' - '.$stAlteracaoValor );
    
    $obDtAssinatura = new Label;
    $obDtAssinatura->setRotulo('Data da Assinatura');
    $obDtAssinatura->setName('dtAssinatura');
    $obDtAssinatura->setId('dtAssinatura');
    $obDtAssinatura->setValue($dtAssinatura);
    
    $obDtInicioExecucao = new Label;
    $obDtInicioExecucao->setRotulo('Data de Início de Execução');
    $obDtInicioExecucao->setName('dtInicioExcucao');
    $obDtInicioExecucao->setId('dtInicioExcucao');
    $obDtInicioExecucao->setValue($dtInicioExcucao);
    
    $obDtFimExecucao = new Label;
    $obDtFimExecucao->setRotulo('Data de Término da Execução');
    $obDtFimExecucao->setName('dtFimExecucao');
    $obDtFimExecucao->setId('dtFimExecucao');
    $obDtFimExecucao->setValue($dtFimExecucao);
    
    $obDtFinalVigencia = new Label;
    $obDtFinalVigencia->setRotulo('Data Final de Vigência');
    $obDtFinalVigencia->setName('dtFinalVigencia');
    $obDtFinalVigencia->setId('dtFinalVigencia');
    $obDtFinalVigencia->setValue($dtFinalVigencia);
    
    $obTxtObjeto = new Label;
    $obTxtObjeto->setName('stObjeto');
    $obTxtObjeto->setId('stObjeto');
    $obTxtObjeto->setRotulo('Objeto');
    $obTxtObjeto->setValue($stObjeto);
    
    $obTxtJustificativa = new Label;
    $obTxtJustificativa->setRotulo('Justificativa');
    $obTxtJustificativa->setName('stJustificativa');
    $obTxtJustificativa->setId('stJustificativa');
    $obTxtJustificativa->setValue($stJustificativa);
    
    $obTxtFundLegal = new Label;
    $obTxtFundLegal->setRotulo('Fundamentação Legal');
    $obTxtFundLegal->setName('stFundamentacaoLegal');
    $obTxtFundLegal->setId('vlValorContratado');
    $obTxtFundLegal->setValue($stFundamentacaoLegal);
    
    $obVlValorContratado = new Label;
    $obVlValorContratado->setRotulo('Valor do Aditivo');
    $obVlValorContratado->setName('vlValorContratado');
    $obVlValorContratado->setId('vlValorContratado');
    $obVlValorContratado->setValue($vlValorContratado);
    
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
    
////////ANULAÇÕES
    $obTLicitacaoContratoAditivosAnulacao = new TLicitacaoContratoAditivosAnulacao;
    $obTLicitacaoContratoAditivosAnulacao->setDado('num_aditivo'       , $aditivo['num_aditivo']);
    $obTLicitacaoContratoAditivosAnulacao->setDado('cod_entidade'      , $aditivo['cod_entidade']);
    $obTLicitacaoContratoAditivosAnulacao->setDado('num_contrato'      , $aditivo['num_contrato']);
    $obTLicitacaoContratoAditivosAnulacao->setDado('exercicio_contrato', $aditivo['exercicio_contrato']);
    $obTLicitacaoContratoAditivosAnulacao->setDado('exercicio'         , $aditivo['exercicio']);
    $obTLicitacaoContratoAditivosAnulacao->recuperaPorChave($rsAnulacao);
    
    if($rsAnulacao->getNumLinhas() > 0) {
        $obFormulario->addTitulo ( "Este aditivo possui a seguinte anulação" );
        
        $dtAnulacao = $rsAnulacao->getCampo('dt_anulacao');
        $stMotivo = $rsAnulacao->getCampo('motivo');
        $vlAnulacao = number_format($rsAnulacao->getCampo('valor_anulacao'),2,',','.');;
        
        $obDtAnulacao = new Label;
        $obDtAnulacao->setRotulo('Data da Anulação');
        $obDtAnulacao->setName('dtAnulacao');
        $obDtAnulacao->setId('dtAnulacao');
        $obDtAnulacao->setValue($dtAnulacao);
        
        $obTxtMotivo = new Label;
        $obTxtMotivo->setRotulo('Justificativa');
        $obTxtMotivo->setName('stJustificativa');
        $obTxtMotivo->setId('stJustificativa');
        $obTxtMotivo->setValue($stMotivo);
        
        $obVlValorAnulado = new Label;
        $obVlValorAnulado->setRotulo('Valor Anulado');
        $obVlValorAnulado->setName('vlValorAnulado');
        $obVlValorAnulado->setId('vlValorAnulado');
        $obVlValorAnulado->setValue($vlAnulacao);
        
        $obFormulario->addComponente( $obDtAnulacao );
        $obFormulario->addComponente( $obTxtMotivo );
        $obFormulario->addComponente( $obVlValorAnulado );
    }
}

//Aba empenho
$obFormulario->addAba("Empenhos");
$obFormulario->addTitulo ( "Dados dos Empenhos" );

$obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato;
$obTEmpenhoEmpenhoContrato->setDado('cod_entidade'      , $request->get('inCodEntidade'));
$obTEmpenhoEmpenhoContrato->setDado('num_contrato'      , $request->get('inNumContrato'));
$obTEmpenhoEmpenhoContrato->setDado('exercicio_contrato', $request->get('stExercicio')  );
$obTEmpenhoEmpenhoContrato->recuperaEmpenhoPorContrato($rsEmpenho);

if($rsEmpenho->getNumLinhas() > 0) {

    $rsEmpenho->addFormatacao('valor_empenho', 'NUMERIC_BR');
    $rsEmpenho->addFormatacao('valor_anulado', 'NUMERIC_BR');
    $rsEmpenho->addFormatacao('valor_total', 'NUMERIC_BR');
    
    $obTable = new Table;
    $obTable->setRecordset($rsEmpenho);
    $obTable->addLineNumber(true);
    
    $obTable->Head->addCabecalho('Empenho', 5);
    $obTable->Head->addCabecalho('Fornecedor', 20);
    $obTable->Head->addCabecalho('Data do Empenho', 5);
    $obTable->Head->addCabecalho('Valor Empenhado', 5);
    $obTable->Head->addCabecalho('Valor Anulado', 5);
    $obTable->Head->addCabecalho('Valor Total', 5);
    
    $obTable->Body->addCampo('[cod_empenho] / [exercicio]', 'C');
    $obTable->Body->addCampo('[numcgm] - [nom_cgm]', 'E');
    $obTable->Body->addCampo('[dt_empenho]', 'C');
    $obTable->Body->addCampo('[valor_empenho]', 'D');
    $obTable->Body->addCampo('[valor_anulado]', 'D');
    $obTable->Body->addCampo('[valor_total]', 'D');
    
    $obTable->montaHTML(true, false);
    $stHTML = $obTable->getHtml();
    
    $obSpnListaEmpenho = new Span;
    $obSpnListaEmpenho->setID("spnListaEmpenho");
    $obSpnListaEmpenho->setValue($stHTML);

    $obFormulario->addSpan($obSpnListaEmpenho);
}

$obSpnObservacao = new Span;
$obSpnObservacao->setID("spnObservacao");
$obSpnObservacao->setClass("observacao");
$obSpnObservacao->setValue("Caso o empenho não tenha sido vinculado na hora do cadastro, para que o mesmo seja listado aqui será necessário utilizar a ação: Gestão Financeira :: Empenho :: Empenho :: Vincular Empenho a um Contrato");

$obFormulario->addSpan($obSpnObservacao);

foreach ($request->getAll() as $chave =>$valor) {
    $param.= "&".$chave."=".$valor;
}

$obBtnCancelar = new Button;
$obBtnCancelar->setName             ( 'cancelar' );
$obBtnCancelar->setValue            ( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "Cancelar('". $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stURL."');" );

$obFormulario->defineBarra(array($obBtnCancelar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
