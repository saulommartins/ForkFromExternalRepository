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

    * Página de Formulario de Inclusao/Alteracao de Conta Contabil
    * Data de Criação   : 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor Anderson: R. M. Buzo

    * @ignore

    * $Id: FMManterPlanoConta.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-02.02.02
*/

include_once( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once( CAM_FRAMEWORK."legado/funcoesLegado.lib.php");
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php");
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php");
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php");
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php");
include_once( CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaEncerrada.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');

if (isset($filtro)) {
    foreach ($filtro as $stCampo2 => $stValor2) {
        if (is_array($stValor2)) {
            foreach ($stValor2 as $stCampo3 => $stValor3) {
                $stFiltro .= "&".$stCampo3."=".urlencode( $stValor3 );
            }
        } else {
            $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
       }
    }
}

//busca entidade logada
$stFiltroEntidade = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao)." AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade = new TEntidade;
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltroEntidade);

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->listar( $rsSistemaContabil );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->listar( $rsClassificacaoContabil );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->listar( $rsRecurso );
$obRContabilidadePlanoBanco->obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

// POG criado para não demonstrar o banco ( 0 - Não Informado ) criado pela migração.
$arBancos = $rsBanco->getElementos();
foreach ($arBancos as $arBanco) {
    if ($arBanco['cod_banco'] != 0) {
        $arNewBancos[] = $arBanco;
    }
}
$rsBanco->setElementos( $arNewBancos );
$rsBanco->setNumLinhas( count( $arNewBancos ) );

if ($stAcao == "alterar") {
    $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
    $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsAgencia );

    $obRMONContaCorrente = new RMONContaCorrente;
    $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
    $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
    $obRMONContaCorrente->listarContaCorrente( $rsContaCorrente );
}
$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$boDisabled = true;
$boDesdobrada = false;
$boDesabilitaClassificacao = false;
$boDesabilitaRecurso = false;
$boPossuiMovimentacao = false;

if ($stAcao == 'alterar') {
    $inCodConta = $_GET['inCodConta'];
    $inCodPlano = $_GET['inCodPlano'];
    $obRContabilidadePlanoBanco->setCodConta( $inCodConta );
    $obRContabilidadePlanoBanco->setCodPlano( $inCodPlano );
    $obRContabilidadePlanoBanco->consultar( $boTransacao );

    $inCodSistemaContabil = $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->getCodSistema();

    $inCodClassContabil = $obRContabilidadePlanoBanco->obRContabilidadeClassificacaoContabil->getCodClassificacao();
    $stCodClassificacao = $obRContabilidadePlanoBanco->getCodEstrutural();
    $stNomConta = $obRContabilidadePlanoBanco->getNomConta();
    $stNatSaldo = $obRContabilidadePlanoBanco->getNaturezaSaldo();
    if ($stNatSaldo == 'devedor') {
        $stNatSaldo = 'D';
    } else if ($stNatSaldo == 'credor') {
        $stNatSaldo = 'C';
    } else if ($stNatSaldo == 'misto') {
        $stNatSaldo = 'X';
    }

    $stEscrituracao = $obRContabilidadePlanoBanco->getEscrituracao();
    $stIndicadorSuperavit = '';
    $stFuncao = '';

    if ( Sessao::getExercicio() > '2012' ) {
        if ( !empty( $_REQUEST['inCodPlano'] ) ) {
            $stIndicadorSuperavit = $obRContabilidadePlanoBanco->getIndicadorSuperavit();
        }
        $stFuncao = $obRContabilidadePlanoBanco->getFuncao();
    }

    $inCodRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecurso();
    $inCodRecursoContraPartida = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecursoContraPartida();

    $inCodPlano = $obRContabilidadePlanoBanco->getCodPlano();
    if ($inCodPlano) {

        $stCodBanco = $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->getCodBanco();
        $stCodAgencia = $obRContabilidadePlanoBanco->obRMONAgencia->getCodAgencia();
        $inCodEntidadeTmp = $obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade();

        // A LINHA ABAIXO É TEMPORÁRIA DEVIDO A ALTERAÇÃO DA TABELA CONTABILIDADE.PLANO_BANCO - REMOVER FUTURAMENTE
        $inCodEntidade = ( $inCodEntidadeTmp != 0 ) ? $obRContabilidadePlanoBanco->obROrcamentoEntidade->getCodigoEntidade() : '';

        $stContaCorrente = $obRContabilidadePlanoBanco->getContaCorrente();
        $boContaAnalitica = true;
        $boDesabilitaRecurso = false;
        if ($inCodEntidade) {
            $boContaBanco = true;
            $boDisabled = false;
        }
    } else {
        $boContaSintetica = true;
    }
}

if (!$inCodEntidade) {
   $obROrcamentoConfiguracao     = new ROrcamentoConfiguracao;

   $obROrcamentoConfiguracao->setExercicio( Sessao::getExercicio() );
   $obROrcamentoConfiguracao->setCodModulo( 8 );
   $obROrcamentoConfiguracao->consultarConfiguracaoEspecifica('cod_entidade_prefeitura');
   $inCodEntidade = $obROrcamentoConfiguracao->getCodEntidadePrefeitura();
}

if ($stAcao == "incluir") {
    $boContaAnalitica = true;
    $boDesabilitaRecurso = false;

    $js .= " buscaValor('tipoContaAnalitica');";
} elseif ($stAcao == "alterar") {
    if ( ($inCodSistemaContabil == "5") || ( $inCodSistemaContabil == "4" && Sessao::getExercicio() > '2012' && $stEscrituracao == 'sintetica' ) ) {
        $js .= " buscaValor('tipoContaSintetica');";
    } else {
        $js .= " buscaValor('tipoContaAnalitica');";
    }
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO ABA Identificação
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == 'alterar') {

    // Verifica se a conta tem movimentacao
    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }
    $obTContabilidadePlanoConta->setDado('stCodEstrutural',mascaraReduzida($stCodClassificacao));
    $obTContabilidadePlanoConta->setDado('exercicio',Sessao::getExercicio());
    $obTContabilidadePlanoConta->verificaMovimentacaoConta($rsMovimentacao,$boTransacao);
    if ($rsMovimentacao->getCampo('retorno') == 't') {
        $boTemMovimentacao = true;
    }

    if ( Sessao::getExercicio() > '2012' ) {
        $obTContabilidadePlanoConta = new TContabilidadePlanoContaTCEMS;
    } else {
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta;
    }
    $obTContabilidadePlanoConta->setDado( 'exercicio',Sessao::getExercicio() );
    $obTContabilidadePlanoConta->setDado( 'cod_estrutural',$stCodClassificacao );
    $obTContabilidadePlanoConta->verificaContaDesdobrada( $rsContas );

    // Verifica se a conta tem desdobramentos
    if ( $rsContas->getCampo('retorno') == 't' ) {

        $boDesdobrada = true;
        $boDesabilitaRecurso = true;
        $boDesabilitaClassificacao = true;
    } else {
        $jsOnload = "montaParametrosGET('HabilitaCampos');";
    }

    //Define Objeto Hidden para Código da Conta
    $obHdnCodConta = new Hidden;
    $obHdnCodConta->setName ( "inCodConta" );
    $obHdnCodConta->setValue( $inCodConta );

    //Define Objeto Hidden para Código do Plano
    $obHdnCodPlano = new Hidden;
    $obHdnCodPlano->setName ( "inCodPlano" );
    $obHdnCodPlano->setValue( $inCodPlano );

    //Define Objeto Hidden para Natureza do saldo
    $obHdnNatSaldo = new Hidden;
    $obHdnNatSaldo->setName ( "stHdnNatSaldo" );
    $obHdnNatSaldo->setValue( $stNatSaldo );

    //Define Objeto Hidden para Código do Sistema Contabil
    $obHdnCodSistContab = new Hidden;
    $obHdnCodSistContab->setName ( "inCodSistContab" );
    $obHdnCodSistContab->setValue( $inCodSistemaContabil );

    //Define Objeto Hidden para Classificacao Contabil
    $obHdnClassContabil = new Hidden;
    $obHdnClassContabil->setName ( "stCodClass" );
    $obHdnClassContabil->setValue( $stCodClassificacao );

    //Define Objeto Label para Código da Classificacao Contabil
    $obLblCodClassContabil = new Label;
    $obLblCodClassContabil->setRotulo    ( "Código de Classificação"                    );
    if ( !Sessao::getExercicio() > '2012' ) {
        $obLblCodClassContabil->setValue( $stCodClassificacao );
    } else {
        $obLblCodClassContabil->setValue( $stCodClassificacao );
    }
}

//Define Objeto Hidden para Código da Classificacao Contabil
$obHdnCodClassContabil = new Hidden;
$obHdnCodClassContabil->setName ( "inCodClassContabil" );
$obHdnCodClassContabil->setId   ( "inCodClassContabil" );
$obHdnCodClassContabil->setValue( $inCodClassContabil );

$obSpanSistemaContabil = new Span;
$obSpanSistemaContabil->setId( "spnSistemaContabil" );

if ( !Sessao::getExercicio() > '2012' ) {
    // Define Objeto TextBox para Codigo da Classificacao Contabil
    $obTxtClassContabil = new TextBox;
    $obTxtClassContabil->setName   ( "txtCodClassContabil"                 );
    $obTxtClassContabil->setId     ( "txtCodClassContabil"                 );
    $obTxtClassContabil->setValue  ( $inCodClassContabil                  );
    $obTxtClassContabil->setRotulo ( "Classificação Contábil"            );
    $obTxtClassContabil->setTitle  ( "Selecione a classificação contábil" );
    $obTxtClassContabil->setInteiro( true                                 );
    $obTxtClassContabil->setNull   ( false );
    $obTxtClassContabil->setDisabled  ( $boDesabilitaClassificacao );
    $obTxtClassContabil->obEvento->setOnChange( "document.getElementById('inCodClassContabil').value = this.value;");

    // Define Objeto Select para Nome da Classificacao Contabil
    $obCmbClassContabil = new Select;
    $obCmbClassContabil->setName      ( "stNomeClassContabil"    );
    $obCmbClassContabil->setId        ( "stNomeClassContabil"    );
    $obCmbClassContabil->setValue     ( $inCodClassContabil      );
    $obCmbClassContabil->addOption    ( "", "Selecione"          );
    $obCmbClassContabil->setCampoId   ( "cod_classificacao"      );
    $obCmbClassContabil->setCampoDesc ( "nom_classificacao"      );
    $obCmbClassContabil->preencheCombo( $rsClassificacaoContabil );
    $obCmbClassContabil->setNull      ( false );
    $obCmbClassContabil->setDisabled  ( $boDesabilitaClassificacao);
    $obCmbClassContabil->obEvento->setOnChange( "document.getElementById('inCodClassContabil').value = this.value;");
}

if ($stAcao == 'incluir') {
    // Define Objeto Textbox para Codigo de Classificação
    $obTxtCodClass = new TextBox;
    $obTxtCodClass->setName      ( "stCodClass"                                 );
    $obTxtCodClass->setId        ( "stCodClass"                                 );
    $obTxtCodClass->setValue     ( "$stCodClassificacao"                        );
    $obTxtCodClass->setSize      ( 30                                           );
    $obTxtCodClass->setMaxLength ( 30                                           );
    $obTxtCodClass->setRotulo    ( "Código de Classificação"                    );
    $obTxtCodClass->setTitle     ( "Informe o código de classificação da conta" );
    $obTxtCodClass->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."',this,event)");
    $obTxtCodClass->obEvento->setOnBlur ("buscaValor('mascaraEstrutural','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
    $obTxtCodClass->obEvento->setOnKeyPress( "return validaExpressao( this, event, '[0-9.]');" );
    $obTxtCodClass->setNull( false );
}

// Define Objeto TextBox para Descrição da Conta
$obTxtDescrConta = new TextBox;
$obTxtDescrConta->setName     ( "stDescrConta"                 );
$obTxtDescrConta->setId       ( "stDescrConta"                 );
$obTxtDescrConta->setValue    ( "$stNomConta"                  );
$obTxtDescrConta->setSize     ( 80                             );
$obTxtDescrConta->setMaxLength( 150                            );
$obTxtDescrConta->setRotulo   ( "Descrição da Conta"           );
$obTxtDescrConta->setTitle    ( "Informe a descrição da conta" );
$obTxtDescrConta->setNull     ( false );

if ( Sessao::getExercicio() > '2012' ) {
    // Define Objeto TextArea para a Função
    $obTxtFuncao = new TextArea;
    $obTxtFuncao->setName     ( "stFuncao"                   );
    $obTxtFuncao->setId       ( "stFuncao"                   );
    $obTxtFuncao->setValue    ( $stFuncao                    );
    $obTxtFuncao->setRotulo   ( "Funçao"                     );
    $obTxtFuncao->setTitle    ( "Informe a Função"           );
    // $obTxtFuncao->setNull     ( true );
}

$obHdnTipoConta = new Hidden;
$obHdnTipoConta->setName ('stTipoConta');
$obHdnTipoConta->setId ('stTipoConta');
$obHdnTipoConta->setValue ( $boContaAnalitica ? 'A' : 'S');

if ($boDesdobrada || $boTemMovimentacao) {
    //Define Objeto Label para Tipo Conta
    $obLblTipoConta = new Label;
    $obLblTipoConta->setRotulo( 'Tipo de Conta' );
    $obLblTipoConta->setValue( $boContaAnalitica ? 'Analítica' : 'Sintética' );
} else {
    // Define Objeto Radio Para Tipo de conta
    $obRdTipoContaSintetica = new Radio;
    $obRdTipoContaSintetica->setName   ( "inTipoConta"    );
    $obRdTipoContaSintetica->setId     ( "inTipoConta"    );
    $obRdTipoContaSintetica->setValue  ( "Sintetica"      );
    $obRdTipoContaSintetica->setLabel  ("Sintética"       );
    $obRdTipoContaSintetica->setChecked( $boContaSintetica);
    $obRdTipoContaSintetica->obEvento->setOnClick( "buscaValor('tipoContaSintetica'); document.getElementById('stTipoConta').value = 'S'; montaParametrosGET('HabilitaCampos'); " );

    $obRdTipoContaAnalitica = new Radio;
    $obRdTipoContaAnalitica->setName ( "inTipoConta" );
    $obRdTipoContaAnalitica->setId   ( "inTipoConta" );
    $obRdTipoContaAnalitica->setRotulo ( "*Tipo de Conta" );
    $obRdTipoContaAnalitica->setValue( "Analitica"   );
    $obRdTipoContaAnalitica->setLabel( "Analítica"   );
    $obRdTipoContaAnalitica->setChecked( $boContaAnalitica );
    $obRdTipoContaAnalitica->obEvento->setOnClick( "buscaValor('tipoContaAnalitica'); document.getElementById('stTipoConta').value = 'A'; montaParametrosGET('HabilitaCampos');" );
}

//Se Municipio TCE-PE, testa se foi selecionado o campo Tipo Conta TCE-PE
$testeTCEPE  = "";
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16){
    $testeTCEPE = " if (document.getElementById('stTipoContaTCEPE').value == '') {
                        mensagem = 'Selecione o Tipo Conta Bancária TCE-PE.';
                        erro = true;
                    }
                  ";
}

$obHdnEval = new HiddenEval;
$obHdnEval->setName('stEval' );
$obHdnEval->setValue("

                if ( document.getElementById('stTipoConta').value == 'A' && document.getElementById('boContaBanco').checked == true ) {
                    ".$testeTCEPE."
                    if (document.getElementById('stContaCorrente').value == '') {
                        mensagem = 'Informe uma Conta Corrente.';
                        erro = true;
                    }
                    if (document.getElementById('inCodAgencia').value == '') {
                        mensagem = 'Selecione uma Agência Bancária.';
                        erro = true;
                    }
                    if (document.getElementById('inCodBanco').value == '') {
                        mensagem = 'Selecione um Banco.';
                        erro = true;
                    }
                    if (document.getElementById('inCodEntidade').value == '') {
                        mensagem = 'Selecione uma Entidade.';
                        erro = true;
                    }
               }
            " );

$obCmbNaturezaDoSaldo = new Select;
$obCmbNaturezaDoSaldo->setName      ( "stNatSaldo"                    );
$obCmbNaturezaDoSaldo->setRotulo    ( "Natureza do Saldo"             );
$obCmbNaturezaDoSaldo->setID        ( "stNatSaldo"                    );
$obCmbNaturezaDoSaldo->addOption    ( "", "Selecione"                 );
$obCmbNaturezaDoSaldo->addOption    ( "D", "Devedor"                  );
$obCmbNaturezaDoSaldo->addOption    ( "C", "Credor"                   );
$obCmbNaturezaDoSaldo->setValue     ( $stNatSaldo   );
if ( strtolower($rsEntidades->getCampo('nom_cgm')) == 'tribunal de contas estado de mato grosso do sul' && Sessao::getExercicio() > 2011 ) {
    $obCmbNaturezaDoSaldo->addOption    ( "X", "Misto"                   );
}
$obCmbNaturezaDoSaldo->setTitle     ( "Selecione a Natureza do Saldo" );
$obCmbNaturezaDoSaldo->setNull      ( false                           );

// Define Objeto TextBox para Codigo do Recurso
$obTxtRecurso = new TextBox;
$obTxtRecurso->setName    ( "inCodRecurso"                     );
$obTxtRecurso->setId      ( "inCodRecurso"                     );
$obTxtRecurso->setValue   ( $inCodRecurso                      );
$obTxtRecurso->setRotulo  ( "**Recurso"                        );
$obTxtRecurso->setTitle   ( "Selecione o recurso orçamentário (Obrigatório caso campo Recurso ContraPartida for preenchido)" );
$obTxtRecurso->setDisabled( $boDesabilitaRecurso               );
$obTxtRecurso->setMascara ( $stMascaraRecurso                  );
$obTxtRecurso->setPreencheComZeros ( 'E'                       );

// Define Objeto Select para o Recurso
$obCmbRecurso = new Select;
$obCmbRecurso->setName      ( "stNomeRecurso"    );
$obCmbRecurso->setId        ( "stNomeRecurso"    );
$obCmbRecurso->setValue     ( $inCodRecurso      );
$obCmbRecurso->addOption    ( "", "Selecione"    );
$obCmbRecurso->setCampoId   ( "[cod_fonte]"    );
$obCmbRecurso->setCampoDesc ( "[nom_recurso]"  );
$obCmbRecurso->preencheCombo( $rsRecurso         );
$obCmbRecurso->setDisabled  ( $boDesabilitaRecurso );

// Define Objeto TextBox para Codigo do Recurso ContraPartida
$obTxtRecursoContraPartida = new TextBox;
$obTxtRecursoContraPartida->setName    ( "inCodRecursoContraPartida"        );
$obTxtRecursoContraPartida->setId      ( "inCodRecursoContraPartida"        );
$obTxtRecursoContraPartida->setValue   ( $inCodRecursoContraPartida         );
$obTxtRecursoContraPartida->setRotulo  ( "Recurso ContraPartida"            );
$obTxtRecursoContraPartida->setTitle   ( "Selecione o recurso orçamentário" );
$obTxtRecursoContraPartida->setDisabled( $boDesabilitaRecurso               );
$obTxtRecursoContraPartida->setMascara ( $stMascaraRecurso                  );
$obTxtRecursoContraPartida->setPreencheComZeros ( 'E'                       );
 
// Define Objeto Select para o Recurso ContraPartida
$obCmbRecursoContraPartida = new Select;
$obCmbRecursoContraPartida->setName      ( "stNomeRecursoContraPartida"    );
$obCmbRecursoContraPartida->setId        ( "stNomeRecursoContraPartida"    );
$obCmbRecursoContraPartida->setValue     ( $inCodRecursoContraPartida      );
$obCmbRecursoContraPartida->addOption    ( "", "Selecione"    );
$obCmbRecursoContraPartida->setCampoId   ( "[cod_fonte]"    );
$obCmbRecursoContraPartida->setCampoDesc ( "[cod_fonte] - [nom_recurso]"  );
$obCmbRecursoContraPartida->preencheCombo( $rsRecurso         );
$obCmbRecursoContraPartida->setDisabled  ( $boDesabilitaRecurso );

//******************************************************//
// Define COMPONENTES DO FORMULARIO ABA Conta de banco
//******************************************************//
// Define Objeto CheckBox
$obChkContaBanco = new CheckBox;
$obChkContaBanco->setName ( "boContaBanco" );
$obChkContaBanco->setId   ( "boContaBanco" );
$obChkContaBanco->setValue( "true" );
$obChkContaBanco->setChecked( $boContaBanco );
$obChkContaBanco->setLabel( "Esta conta é uma conta de banco." );
$obChkContaBanco->setTitle( "Habilitando este checkbox, os campos abaixo serão habilitados" );

//Verificar se é municipio PE, para ativar ou desativar o combo Tipo Conta TCE-PE
$ativaTipoTCEPE="";
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16){
    $ativaTipoTCEPE = ",stTipoContaTCEPE";
}

if ($boTemMovimentacao == 1) {
    $obChkContaBanco->obEvento->setOnClick("if (this.checked == true) {
                                            habilitaCampo('inNumBanco,stNomeBanco,inNumAgencia,stNomeAgencia,stContaCorrente,inCodEntidade,stNomEntidade".$ativaTipoTCEPE."');
                                        } else {
                                            desabilitaCampo('inNumBanco,stNomeBanco,inNumAgencia,stNomeAgencia,stContaCorrente,inCodEntidade,stNomEntidade".$ativaTipoTCEPE."');
                                        } ");
} else {
    $obChkContaBanco->obEvento->setOnClick("if (this.checked == true) {
                                            habilitaCampo('inNumBanco,stNomeBanco,inNumAgencia,stNomeAgencia,stContaCorrente,inCodEntidade,stNomEntidade".$ativaTipoTCEPE."');
                                        } else {
                                            desabilitaCampo('inNumBanco,stNomeBanco,inNumAgencia,stNomeAgencia,stContaCorrente,inCodEntidade,stNomEntidade".$ativaTipoTCEPE."');
                                        } ");
}

if ($rsEntidade->getNumLinhas()== 1 ) {
$inCodEntidade =  $rsEntidade->getCampo('cod_entidade');
}

$hdnCodEntidade = new Hidden;
$hdnCodEntidade->setName('hdnCodEntidade');
$hdnCodEntidade->setId ('hdnCodEntidade');
$hdnCodEntidade->setValue ( $inCodEntidade );

// Define Objeto TextBox para Codigo da Entidade
$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName   ( "inCodEntidade" );
$obTxtCodEntidade->setId     ( "inCodEntidade" );
$obTxtCodEntidade->setValue  ( $inCodEntidade  );
$obTxtCodEntidade->setRotulo ( "*Entidade"      );
$obTxtCodEntidade->setTitle  ( "Selecione a Entidade" );

if ($boTemMovimentacao == 1) {
    $obTxtCodEntidade->setDisabled ( $boTemMovimentacao );
} else {
    $obTxtCodEntidade->setDisabled ( $boDisabled );
}

$obTxtCodEntidade->setInteiro( true  );

// Define Objeto Select para Nome da Entidade
$obCmbNomEntidade = new Select;
$obCmbNomEntidade->setName      ( "stNomEntidade"  );
$obCmbNomEntidade->setId        ( "stNomEntidade"  );
$obCmbNomEntidade->setValue     ( $inCodEntidade   );
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbNomEntidade->addOption              ( "", "Selecione"               );
}
$obCmbNomEntidade->setCampoId   ( "cod_entidade" );
$obCmbNomEntidade->setCampoDesc ( "nom_cgm" );
$obCmbNomEntidade->preencheCombo( $rsEntidade    );

if ($boTemMovimentacao == 1) {
    $obCmbNomEntidade->setDisabled ( $boTemMovimentacao );
} else {
    $obCmbNomEntidade->setDisabled  ( $boDisabled    );
}

$obCmbNomEntidade->setStyle  ( "width: 500px;" );

// Define Objeto TextBox para Codigo do Banco
$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco']         );
$obTxtBanco->setRotulo   ( "*Banco"            );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o banco" );
$obTxtBanco->setDisabled ( $boDisabled         );
$obTxtBanco->setInteiro  ( true                );
$obTxtBanco->obEvento->setOnChange  ( " if(this.value != '') montaParametrosGET('MontaAgencia');
                                        else {
                                            document.getElementById('inCodBanco').value = '';
                                            document.getElementById('inCodAgencia').value = '';
                                            document.getElementById('stContaCorrente').value = '';
                                        }
                                    ");

$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId ('inCodBanco');
$obHdnBanco->setValue ( $_REQUEST['inCodBanco'] );

// Define Objeto Select para Nome do Banco
$obCmbBanco = new Select;
$obCmbBanco->setName      ( "stNomeBanco"   );
$obCmbBanco->setId        ( "stNomeBanco"   );
$obCmbBanco->setValue     ( $_REQUEST['inNumBanco']   );
$obCmbBanco->setDisabled  ( $boDisabled     );
$obCmbBanco->addOption    ( "", "Selecione" );
$obCmbBanco->setCampoId   ( "num_banco"     );
$obCmbBanco->setCampoDesc ( "nom_banco"     );
$obCmbBanco->preencheCombo( $rsBanco        );
$obCmbBanco->obEvento->setOnChange  ( " montaParametrosGET('MontaAgencia');");

// Define Objeto TextBox para Codigo da Agência
$obTxtAgencia = new TextBox;
$obTxtAgencia->setName     ( "inNumAgencia"        );
$obTxtAgencia->setId       ( "inNumAgencia"        );
$obTxtAgencia->setValue    ( $_REQUEST['inNumAgencia'] );
$obTxtAgencia->setRotulo   ( "*Agência"            );
$obTxtAgencia->setMaxLength( 10                    );
$obTxtAgencia->setTitle    ( "Selecione a agência" );
$obTxtAgencia->setDisabled ( $boDisabled           );
$obTxtAgencia->obEvento->setOnChange  ( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnAgencia = new Hidden;
$obHdnAgencia->setName ( 'inCodAgencia' );
$obHdnAgencia->setId ( 'inCodAgencia' );
$obHdnAgencia->setValue ( $_REQUEST['inCodAgencia'] );

// Define Objeto Select para Nome da agencia
$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->setValue     ( $_REQUEST['inNumAgencia']  );
$obCmbAgencia->addOption    ( "", "Selecione"  );
if ($stAcao == "alterar") {
    $obCmbAgencia->setCampoId   ( "num_agencia"    );
    $obCmbAgencia->setCampoDesc ( "nom_agencia"    );
    $obCmbAgencia->preencheCombo( $rsAgencia       );
}
$obCmbAgencia->setDisabled  ( $boDisabled      );
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
$obHdnContaCorrente->setValue( $_REQUEST['inContaCorrente']);

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo	 ( "*Conta Corrente");
$obCmbContaCorrente->setName      ( "stContaCorrente"    );
$obCmbContaCorrente->setId        ( "stContaCorrente"    );
$obCmbContaCorrente->setValue     ( $_REQUEST['stContaCorrente']   );
$obCmbContaCorrente->addOption    ( "", "Selecione"          );
if ($stAcao == "alterar") {
    $obCmbContaCorrente->setCampoId ( "num_conta_corrente"  );
    $obCmbContaCorrente->setCampoDesc( "num_conta_corrente" );
    $obCmbContaCorrente->preencheCombo( $rsContaCorrente );
}
$obCmbContaCorrente->setCampoId   ( "num_conta_corrente"     );
$obCmbContaCorrente->setCampoDesc ( "num_conta_corrente"     );
$obCmbContaCorrente->setDisabled  ( $boDisabled );
$obCmbContaCorrente->obEvento->setOnChange  ( " montaParametrosGET('BuscaContaCorrente'); ");

//Tipo Conta Bancária - TCEPE
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
   
    include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoContaCorrente.class.php');
    $obTTCEPETipoContaCorrente = new TTCEPETipoContaCorrente();
    $obTTCEPETipoContaCorrente->recuperaTodos($rsListaTipoConta);
    
    $obCmbTipoContaCorrente = new Select();
    $obCmbTipoContaCorrente->setRotulo	  ( "*Tipo Conta Corrente TCE-PE"         );
    $obCmbTipoContaCorrente->setName      ( "inTipoContaCorrenteTCEPE"            );
    $obCmbTipoContaCorrente->setId        ( "inTipoContaCorrenteTCEPE"            );
    $obCmbTipoContaCorrente->addOption    ( "", "Selecione"                       );
    $obCmbTipoContaCorrente->setCampoId   ( "cod_tipo"                            );
    $obCmbTipoContaCorrente->setCampoDesc ( "[cod_tipo] - [descricao]"            );
    $obCmbTipoContaCorrente->preencheCombo( $rsListaTipoConta                     );
    $obCmbTipoContaCorrente->setValue     ( $_REQUEST['inTipoContaCorrenteTCEPE'] );
    $obCmbTipoContaCorrente->setObrigatorio (true);
    
    include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEPlanoBancoTipoConta.class.php');
    $obTTCEPEPlanoBancoTipoConta = new TTCEPEPlanoBancoTipoConta();
    $obTTCEPEPlanoBancoTipoConta->listaTipoContaBanco($rsListaTipoConta);
    
    $obTTCEPEPlanoBancoTipoConta->setDado( "cod_plano", $inCodPlano);
    $obTTCEPEPlanoBancoTipoConta->setDado( "exercicio", Sessao::getExercicio());
    $obTTCEPEPlanoBancoTipoConta->recuperaPorChave($rsTipoConta);
    
    $codTipoContaBanco = "";
    
    while(!$rsTipoConta->eof()){
        $codTipoContaBanco = $rsTipoConta->getCampo('cod_tipo_conta_banco');
        $rsTipoConta->proximo();
    }
    
    $obCmbTipoConta = new Select();
    $obCmbTipoConta->setRotulo	( "*Tipo Conta Bancária TCE-PE" );
    $obCmbTipoConta->setName    ( "stTipoContaTCEPE"            );
    $obCmbTipoConta->setId      ( "stTipoContaTCEPE"            );
    $obCmbTipoConta->setValue   ( $_REQUEST['stTipoContaTCEPE'] );
    $obCmbTipoConta->addOption  ( "", "Selecione"               );
    $obCmbTipoConta->setCampoId ( "cod_tipo_conta_banco"        );
    $obCmbTipoConta->setCampoDesc( "descricao"                  );
    $obCmbTipoConta->preencheCombo( $rsListaTipoConta           );
    $obCmbTipoConta->setValue   ( $codTipoContaBanco            );
    $obCmbTipoConta->setDisabled( $boDisabled                   );
}

//Conta Corrente - TCE-MG
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11 && (Sessao::getExercicio() >= "2015")) {
    
    include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTCEMGTipoContaCorrente.class.php');
    $obTTCEMGTipoContaCorrente = new TTCEMGTipoContaCorrente();
    $obTTCEMGTipoContaCorrente->recuperaTodos($rsListaTipoConta);

    $obCmbTipoContaCorrenteTCEMG = new Select();
    $obCmbTipoContaCorrenteTCEMG->setRotulo	  ( "Conta Corrente TCE-MG"         );
    $obCmbTipoContaCorrenteTCEMG->setName      ( "inTipoContaCorrenteTCEMG"            );
    $obCmbTipoContaCorrenteTCEMG->setId        ( "inTipoContaCorrenteTCEMG"            );
    $obCmbTipoContaCorrenteTCEMG->addOption    ( "", "Selecione"                       );
    $obCmbTipoContaCorrenteTCEMG->setCampoId   ( "cod_tipo"                            );
    $obCmbTipoContaCorrenteTCEMG->setCampoDesc ( "[cod_tipo] - [descricao]"            );
    $obCmbTipoContaCorrenteTCEMG->preencheCombo( $rsListaTipoConta                     );
    $obCmbTipoContaCorrenteTCEMG->setValue     ( $_REQUEST['inTipoContaCorrenteTCEMG'] );
    $obCmbTipoContaCorrenteTCEMG->setObrigatorio (true);
}
SistemaLegado::executaFramePrincipal($js);

$obTContabilidadePlanoContaEncerrada = new TContabilidadePlanoContaEncerrada();
$obTContabilidadePlanoContaEncerrada->setDado('cod_conta',$request->get('inCodConta'));
$obTContabilidadePlanoContaEncerrada->setDado('exercicio',$request->get('stExercicio'));
$obTContabilidadePlanoContaEncerrada->recuperaPorChave($rsContaEncerrada);

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.02');
$obFormulario->addTitulo( "Dados para Conta Contábil" );

if($rsContaEncerrada->getNumLinhas() > 0 && $stAcao == 'alterar'){
       
    $obHdnAcao->setValue( "encerrar" );
    $boEncerrar = true;
    
    # Aba Identificação
    $obFormulario->addAba("Identificação");
    $obFormulario->addTitulo("Identificação da Conta");
    
    $obLblTipoContaAnalitica = new Label;
    $obLblTipoContaAnalitica->setRotulo( "Tipo de Conta"  );
    $obLblTipoContaAnalitica->setValue( $boContaAnalitica ? 'Analitica' : 'Sintética' );
    
    switch ($stNatSaldo) {
        case 'D': $stNatSaldo = 'Devedor'; break;
        case 'C': $stNatSaldo = 'Credor'; break;
        case 'X': $stNatSaldo = 'Misto'; break;
    }
    
    $obLblNaturezaSaldo = new Label;
    $obLblNaturezaSaldo->setRotulo( "Natureza do Saldo" );
    $obLblNaturezaSaldo->setValue( $stNatSaldo );
    
    // Somente para conta analitica
    $obLblSistemaContabil = new Label;
    $stNomeContaContabil  = "";
    $obRContabilidadePlanoBanco->obRContabilidadeSistemaContabil->listarSistemaContaAnalitica( $rsSistemaContabil );
    foreach ($rsSistemaContabil->getElementos() as $inChave => $stValor){
        if($stValor['cod_sistema'] == $inCodSistemaContabil){
            $stNomeContaContabil = $stValor['cod_sistema']." - ".$stValor['nom_sistema'];
        }
    }
    if ( strtolower($rsEntidades->getCampo('nom_cgm')) == 'tribunal de contas estado de mato grosso do sul' && Sessao::getExercicio() > 2011 ) {
        $obLblSistemaContabil->setRotulo ( "Natureza Contábil" );
    } else {
        $obLblSistemaContabil->setRotulo ( "Sistema Contábil"  );
    }
    $obLblSistemaContabil->setValue ( $stNomeContaContabil  );
    
    if ( Sessao::getExercicio() > '2012' ) {
        $stIndicadorSuperavit = $obRContabilidadePlanoBanco->getIndicadorSuperavit();
        $obLblIndicadorSuperavit = new Label;
        $obLblIndicadorSuperavit->setRotulo ( "Indicador Superávit" );
        $obLblIndicadorSuperavit->setValue ( $stIndicadorSuperavit  );
    }
    
    $obLblCodClassContabil = new Label;
    $obLblCodClassContabil->setRotulo( "Código de Classificação" );
    $obLblCodClassContabil->setValue ( $stCodClassificacao       );
    
    $obLblDescrConta = new Label;
    $obLblDescrConta->setRotulo( "Descrição da Conta" );
    $obLblDescrConta->setValue ( $stNomConta          );
    
    $obLblFuncao = new Label;
    $obLblFuncao->setRotulo( "Funçao " );
    $obLblFuncao->setValue ( $stFuncao );
    
    $stNomeRecurso = "";
    foreach ($rsRecurso->getElementos() as $inChave => $stValor){
        if($stValor['cod_recurso'] == $inCodRecurso){
            $stNomeRecurso = $stValor['cod_recurso']." - ".$stValor['nom_recurso'];
        }
    }
    $obLblRecurso = new Label;
    $obLblRecurso->setRotulo( "Recurso"      );
    $obLblRecurso->setValue ( $stNomeRecurso );
        
    $obFormulario->addHidden    ( $obHdnAcao               );
    $obFormulario->addComponente( $obLblTipoContaAnalitica );
    $obFormulario->addComponente( $obLblNaturezaSaldo      );
    if($boContaAnalitica){
        $obFormulario->addComponente( $obLblSistemaContabil );
        if ( Sessao::getExercicio() > '2012' ) {
            $obFormulario->addComponente( $obLblIndicadorSuperavit );
        }
    }
    $obFormulario->addComponente( $obLblCodClassContabil   );
    $obFormulario->addComponente( $obLblDescrConta         );
    $obFormulario->addComponente( $obLblFuncao             );
    $obFormulario->addComponente( $obLblRecurso            );
            
    # Aba Conta de Banco
    $obFormulario->addAba("Conta de Banco");
    $obFormulario->addTitulo("Conta de Banco");
    
    $obLblContaBanco = new Label;
    $obLblContaBanco->setRotulo( "" );
    if($boContaBanco){
        $obLblContaBanco->setValue ( "Esta conta é uma conta de banco." );
    }else{
        $obLblContaBanco->setValue ( "Esta não é uma conta de banco." );
    }
        
    $stNomEntidade = "";
    foreach ($rsEntidade->getElementos() as $inChave => $stValor){
        if($stValor['cod_entidade'] == $inCodEntidade){
            $stNomEntidade = $stValor['cod_entidade']." - ".$stValor['nom_cgm'];
        }
    }
    $obLblEntidade = new Label;
    $obLblEntidade->setRotulo ( "Entidade"     );
    $obLblEntidade->setValue  ( $stNomEntidade );
    
    $stNomBanco = "";
    foreach ($rsBanco->getElementos() as $inChave => $stValor){
        if($stValor['num_banco'] == $_REQUEST['inNumBanco']){
            $stNomBanco = $stValor['num_banco']." - ".$stValor['nom_banco'];
        }
    }    
    $obLblBanco = new Label;
    $obLblBanco->setRotulo ( "Banco"     );
    $obLblBanco->setValue  ( $stNomBanco );
    
    $stNomAgencia = "";
    foreach ($rsAgencia->getElementos() as $inChave => $stValor){
        if($stValor['num_agencia'] == $_REQUEST['inNumAgencia']){
            $stNomAgencia = $stValor['num_agencia']." - ".$stValor['nom_agencia'];
        }
    }
    
    $obLblAgencia = new Label;
    $obLblAgencia->setRotulo ( "Agência"   );
    $obLblAgencia->setValue  ( $stNomAgencia );
 
    $obLblContaCorrente = new Label;
    $obLblContaCorrente->setRotulo ( "Conta Corrente"             );
    $obLblContaCorrente->setValue  ( $_REQUEST['stContaCorrente'] );
    
    $obLblTipoConta = new Label;
    $obLblTipoConta->setRotulo ( "Tipo Conta Bancária TCE-PE" );
    $obLblTipoConta->setValue  ( $_REQUEST['stTipoContaTCEPE'] );
       
    $obFormulario->addComponente( $obLblContaBanco );
    $obFormulario->addComponente( $obLblEntidade   );
    $obFormulario->addComponente( $obLblBanco      );
    $obFormulario->addComponente( $obLblAgencia    );
    $obFormulario->addComponente( $obLblContaCorrente );
    //Tipo Conta Bancária - TCEPE
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
        $obFormulario->addComponente( $obLblTipoConta );
    }
    
}else{

    # Aba Identificação
    $obFormulario->addAba("Identificação");
    $obFormulario->addTitulo("Identificação da Conta");
    
    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addHidden( $obHdnAcao );
    
    if ($stAcao == 'alterar') {
        $obFormulario->addHidden( $obHdnCodConta );
        $obFormulario->addHidden( $obHdnCodPlano );
        $obFormulario->addHidden( $obHdnCodSistContab );
        $obFormulario->addHidden( $obHdnClassContabil );
        $obFormulario->addHidden( $obHdnNatSaldo );
    }
    
    $obFormulario->addHidden( $obHdnCodClassContabil );
    $obFormulario->addHidden( $obHdnBanco );
    $obFormulario->addHidden( $obHdnAgencia );
    $obFormulario->addHidden( $obHdnTipoConta );
    $obFormulario->addHidden( $obHdnEval, true );
    $obFormulario->addHidden( $obHdnContaCorrente );
    $obFormulario->addHidden( $hdnCodEntidade );
    
    if ($boDesdobrada || $boTemMovimentacao) {
        $obFormulario->addComponente( $obLblTipoConta );
    } else {
        $obFormulario->agrupaComponentes( array($obRdTipoContaAnalitica, $obRdTipoContaSintetica) );
    }
    $obFormulario->addComponente($obCmbNaturezaDoSaldo);
    
    $obFormulario->addSpan( $obSpanSistemaContabil );
    
    if ( !Sessao::getExercicio() > '2012' ) {
        $obFormulario->addComponenteComposto( $obTxtClassContabil  , $obCmbClassContabil   );
    }
    
    if ($stAcao == 'alterar') {
        $obFormulario->addComponente( $obLblCodClassContabil );
    } else {
        $obFormulario->addComponente( $obTxtCodClass );
    }
    
    $obFormulario->addComponente( $obTxtDescrConta );
    if ( Sessao::getExercicio() > '2012' ) {
        $obFormulario->addComponente( $obTxtFuncao );
    }
    
    include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
    
    $obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
    $obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
    $obRConfiguracaoOrcamento->consultarConfiguracao();
    if ($obRConfiguracaoOrcamento->getDestinacaoRecurso() == 'true') {
        include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
        $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
        $obIMontaRecursoDestinacao->setCodRecurso  ( $inCodRecurso );
        $obIMontaRecursoDestinacao->setFiltro      ( true          ); // Para tornar não obrigatório informar a destinação.
        $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    } else {
        $obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso );
        $obFormulario->addComponenteComposto( $obTxtRecursoContraPartida, $obCmbRecursoContraPartida );
    }
    
    //Tipo Conta Bancária - TCEMG
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 11 && (Sessao::getExercicio() >= "2015")) {
        $obFormulario->addComponente( $obCmbTipoContaCorrenteTCEMG );
    }
        //Tipo Conta Bancária - TCEPE
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
        $obFormulario->addComponente( $obCmbTipoContaCorrente );
    }
    
    # Aba Conta de Banco
    $obFormulario->addAba("Conta de Banco");
    $obFormulario->addTitulo("Conta de Banco");
    $obFormulario->addComponente( $obChkContaBanco );
    $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
    $obFormulario->addComponenteComposto( $obTxtBanco  , $obCmbBanco   );
    $obFormulario->addComponenteComposto( $obTxtAgencia, $obCmbAgencia );
    //$obFormulario->addComponente( $obTxtContaCorrente ); Comentado devido ao bug #9738
    $obFormulario->addComponente( $obCmbContaCorrente );
    
    //Tipo Conta Bancária - TCEPE
    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 16) {
        $obFormulario->addComponente( $obCmbTipoConta );
    }
}

if ($stAcao == 'alterar') {
    $stLocation = $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro;

    if($boEncerrar){
        $obOk = new Ok();
        $obOk->setValue ("Ok");
        $obOk->obEvento->setOnclick("Cancelar('".$stLocation."');");
        $obFormulario->defineBarra( array( $obOk) );
    }else{
        $obFormulario->Cancelar( $stLocation );    
    }
    
} else {
    $obFormulario->OK();
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
