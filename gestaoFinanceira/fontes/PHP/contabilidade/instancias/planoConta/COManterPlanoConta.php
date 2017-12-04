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

    * Página de Consulta de Conta Contabil
    * Data de Criação   : 22/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: COManterPlanoConta.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-02.02.02
                    uc-02.02.19

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoConta.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaEncerrada.class.php" );
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
foreach ($filtro as $stCampo2 => $stValor2) {
    if (is_array($stValor2)) {
        foreach ($stValor2 as $stCampo3 => $stValor3) {
            $stFiltro .= "&".$stCampo3."=".urlencode( $stValor3 );
        }
    } else {
        $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
   }
}

include_once $pgJS;

$stFiltroEntidade = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao)." AND entidade.exercicio = '".Sessao::getExercicio()."' ";
$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltroEntidade);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$inCodConta = $_GET['inCodConta'];
$inCodPlano = $_GET['inCodPlano'];
$stTipoConta = 'Sintética';

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->setCodConta( $inCodConta );
$obRContabilidadePlanoBanco->setCodPlano( $inCodPlano );
$obRContabilidadePlanoBanco->consultar();

$inCodSistemaContabil = $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->getCodSistema();
$stNomSistemaContabil = $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->getNomSistema();
$inCodClassContabil = $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->getCodClassificacao();
$stNomClassContabil = $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->getNomClassificacao();
$stCodClassificacao = $obRContabilidadePlanoBanco->getCodEstrutural();
$stNomConta = $obRContabilidadePlanoBanco->getNomConta();
$inCodRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getMascRecurso();
$stNomRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getNome();

$inCodPlano = $obRContabilidadePlanoBanco->getCodPlano();
if ($inCodPlano) {
    $stTipoConta = 'Analítica';
    $stCodBanco = $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getCodBanco();
    $stNomBanco = $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getNomBanco();
    $stCodAgencia = $obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia();
    $stNomAgencia = $obRContabilidadePlanoBanco->obRMONAgencia->getNomAgencia();
    $inCodEntidade = $obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade();
    $stNomEntidade = $obRContabilidadePlanoBanco->obROrcamentoEntidade->getNomeEntidade();
    $stContaCorrente = $obRContabilidadePlanoBanco->getContaCorrente();
    
    $boContaAnalitica = true;
    $boHabilitaRecurso = false;
    if ($stContaCorrente) {
        $boContaBanco = true;
        $boDisabled = false;
    }
}


SistemaLegado::executaFrameOculto("buscaDado('montaListaEntidadeValor');");

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgCons );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto da ação stAcao
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Define o objeto do codigo do plano
$obHdnCodPlano = new Hidden;
$obHdnCodPlano->setName ( "inCodPlano" );
$obHdnCodPlano->setValue( $inCodPlano );

//Define o objeto do codigo estrutural
$obHdnCodEstrutural = new Hidden;
$obHdnCodEstrutural->setName ( "stCodEstrutural" );
$obHdnCodEstrutural->setValue( $stCodEstrutural );

//Define o objeto da data de saldo
$obHdnDtSaldo = new Hidden;
$obHdnDtSaldo->setName ( "dtSaldo" );
$obHdnDtSaldo->setValue( $_GET['dtSaldo'] );

// Define Objeto Label para Codigo do Sistema Contabil
$obLblSistemaContabil = new Label;
$obLblSistemaContabil->setValue  ( $inCodSistemaContabil.' - '.$stNomSistemaContabil );
if ( strtolower($rsEntidades->getCampo('nom_cgm')) == 'tribunal de contas estado de mato grosso do sul' && Sessao::getExercicio() > 2011 ) {
    $obLblSistemaContabil->setRotulo ( "Natureza Contábil" );
} else {
    $obLblSistemaContabil->setRotulo ( "Sistema Contábil" );
}

if ( strtolower($rsEntidades->getCampo('nom_cgm')) != 'tribunal de contas estado de mato grosso do sul' ) {
    // Define Objeto Label para Codigo da Classificacao Contabil
    $obLblClassContabil = new Label;
    $obLblClassContabil->setValue  ( $inCodClassContabil.' - '.$stNomClassContabil);
    $obLblClassContabil->setRotulo ( "Classificação Contábil" );
}

// Define Objeto Label para Codigo de Classificação
$obLblCodClass = new Label;
$obLblCodClass->setValue     ( "$stCodClassificacao" );
$obLblCodClass->setRotulo    ( "Código de Classificação" );

// Define Objeto Label para Descrição da Conta
$obLblDescrConta = new Label;
$obLblDescrConta->setValue    ( "$stNomConta" );
$obLblDescrConta->setRotulo   ( "Descrição da conta" );

// Define Objeto Label Para Tipo de conta
$obLblTipoConta = new Label;
$obLblTipoConta->setValue  ( $stTipoConta    );
$obLblTipoConta->setRotulo ( "Tipo de conta" );

// Define Objeto Label para Codigo do Recurso
$obLblRecurso = new Label;
if ($inCodRecurso) {
    $obLblRecurso->setValue   ( $inCodRecurso.' - '.$stNomRecurso );
} else {
    $obLblRecurso->setValue   ( '' );
}

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio() );
$obRConfiguracaoOrcamento->consultarConfiguracao();
if ($obRConfiguracaoOrcamento->getDestinacaoRecurso() == 'true') {
    $obLblRecurso->setRotulo ( "Destinação de Recurso");
} else $obLblRecurso->setRotulo  ( "Recurso" );

// Define Objeto Span para lista de Saldo
$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Label para Codigo do Banco
$obLblEntidade = new Label;
$obLblEntidade->setValue    ( $inCodEntidade.' - '.$stNomEntidade );
$obLblEntidade->setRotulo   ( "Entidade"         );

// Define Objeto Label para Codigo do Banco
$obLblBanco = new Label;
$obLblBanco->setValue    ( $_REQUEST["stNumBanco"].' - '.$stNomBanco );
$obLblBanco->setRotulo   ( "Banco"            );

// Define Objeto Label para Codigo da Agência
$obLblAgencia = new Label;
$obLblAgencia->setValue    ( $_REQUEST["stNumAgencia"].' - '.$stNomAgencia );
$obLblAgencia->setRotulo   ( "Agência" );

// Define Objeto Label para Codigo da Conta Corrente
$obLblContaCorrente = new Label;
$obLblContaCorrente->setValue    ( $stContaCorrente );
$obLblContaCorrente->setRotulo   ( "Conta Corrente" );

$obTContabilidadePlanoContaEncerrada = new TContabilidadePlanoContaEncerrada();
$obTContabilidadePlanoContaEncerrada->setDado('cod_conta',$request->get('inCodConta'));
$obTContabilidadePlanoContaEncerrada->setDado('exercicio',$request->get('stExercicio'));
$obTContabilidadePlanoContaEncerrada->recuperaPorChave($rsContaEncerrada);

if($rsContaEncerrada->getNumLinhas() > 0 ){
    
    $obLblSituacao = new Label;
    $obLblSituacao->setRotulo( "Situação" );
    $obLblSituacao->setValue ( "Encerrada" );
    
    $obLblDataEncerramento = new Label;
    $obLblDataEncerramento->setRotulo( "Data de encerramento da conta" );
    $obLblDataEncerramento->setValue ( $rsContaEncerrada->getCampo('dt_encerramento') );
    
    $obLblMotivoEncerramento = new Label;
    $obLblMotivoEncerramento->setRotulo( "Motivo" );
    $obLblMotivoEncerramento->setValue ( $rsContaEncerrada->getCampo('motivo') );
}

//Tipo Conta Bancária - TCEPE
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 16) {
    include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEPlanoBancoTipoConta.class.php');
    $obTTCEPEPlanoBancoTipoConta = new TTCEPEPlanoBancoTipoConta();
    
    $obTTCEPEPlanoBancoTipoConta->setDado( "cod_plano", $inCodPlano);
    $obTTCEPEPlanoBancoTipoConta->recuperaPlanoBancoTipoConta($rsTipoConta);
    
    $stTipoContaBanco = "";
    while(!$rsTipoConta->eof()){
        $stTipoContaBanco = $rsTipoConta->getCampo('descricao');
        $rsTipoConta->proximo();
    }
    
    $obLblTipoContaBanco = new Label;
    $obLblTipoContaBanco->setValue    ( $stTipoContaBanco );
    $obLblTipoContaBanco->setRotulo   ( "Tipo Conta Bancária TCE-PE" );

    include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoContaCorrente.class.php');
    $obTTCEPETipoContaCorrente = new TTCEPETipoContaCorrente;
    $obTTCEPETipoContaCorrente->setDado('cod_tipo', $obRContabilidadePlanoBanco->getTipoContaCorrenteTCEPE());
    $obTTCEPETipoContaCorrente->recuperaPorChave($rsTipoContaCorrenteTCEPE);
    
    $inTipoContaCorrenteTCEPE = $obRContabilidadePlanoBanco->getTipoContaCorrenteTCEPE();
    $obLblTipoContaCorrente = new Label;
    $obLblTipoContaCorrente->setValue ( $rsTipoContaCorrenteTCEPE->getCampo('cod_tipo').' - '.$rsTipoContaCorrenteTCEPE->getCampo('descricao'));
    $obLblTipoContaCorrente->setRotulo( "Tipo Conta Corrente TCE-PE" );  
}
//Tipo Conta Bancária - TCEMG
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) {
    $obRContabilidadePlanoConta = new RContabilidadePlanoConta();
    $obRContabilidadePlanoConta->setExercicio( Sessao::getExercicio() );
    $obRContabilidadePlanoConta->setCodConta( $inCodConta );
    $obRContabilidadePlanoConta->consultar();
    include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGTipoContaCorrente.class.php');
    $obTTCEMGTipoContaCorrente = new TTCEMGTipoContaCorrente;
    $obTTCEMGTipoContaCorrente->setDado('cod_tipo', $obRContabilidadePlanoConta->getTipoContaCorrenteTCEMG());
    $obTTCEMGTipoContaCorrente->recuperaPorChave($rsTipoContaCorrenteTCEMG);

    $inTipoContaCorrenteTCEMG = $obRContabilidadePlanoBanco->getTipoContaCorrenteTCEMG();
    $obLblTipoContaCorrente = new Label;
    $obLblTipoContaCorrente->setValue ( $rsTipoContaCorrenteTCEMG->getCampo('cod_tipo').' - '.$rsTipoContaCorrenteTCEMG->getCampo('descricao'));
    $obLblTipoContaCorrente->setRotulo( "Conta Corrente TCE-MG" );  
}
//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo( "Dados para conta contabil" );

$obFormulario->addTitulo("Identificação da conta");

$obFormulario->addHidden( $obHdnAcao          );
$obFormulario->addHidden( $obHdnCtrl          );
$obFormulario->addHidden( $obHdnCodPlano      );
$obFormulario->addHidden( $obHdnCodEstrutural );
$obFormulario->addHidden( $obHdnDtSaldo );
$obFormulario->addComponente( $obLblSistemaContabil );
if ( strtolower($rsEntidades->getCampo('nom_cgm')) != 'tribunal de contas estado de mato grosso do sul' ) {
    $obFormulario->addComponente( $obLblClassContabil   );
}
$obFormulario->addComponente( $obLblCodClass        );
$obFormulario->addComponente( $obLblDescrConta      );
$obFormulario->addComponente( $obLblTipoConta       );
$obFormulario->addComponente( $obLblRecurso         );

if($rsContaEncerrada->getNumLinhas() > 0 ){
    $obFormulario->addComponente( $obLblSituacao           );
    $obFormulario->addComponente( $obLblDataEncerramento   );
    $obFormulario->addComponente( $obLblMotivoEncerramento );
}
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 11) {
    $obFormulario->addComponente( $obLblTipoContaCorrente );
}
$obFormulario->addTitulo("Saldos da conta contábil");
$obFormulario->addSpan( $obSpan );

$obFormulario->addTitulo("Conta de Banco");
$obFormulario->addComponente( $obLblEntidade      );
$obFormulario->addComponente( $obLblBanco         );
$obFormulario->addComponente( $obLblAgencia       );
$obFormulario->addComponente( $obLblContaCorrente );

//Tipo Conta Bancária - TCEPE
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 16) {
    $obFormulario->addComponente( $obLblTipoContaBanco );
    $obFormulario->addComponente( $obLblTipoContaCorrente );
}


$obOk = new OK;
$obOk->obEvento->setOnClick( "Cancelar('".$pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro."','telaPrincipal');" );

$obFormulario->defineBarra( array( $obOk ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
