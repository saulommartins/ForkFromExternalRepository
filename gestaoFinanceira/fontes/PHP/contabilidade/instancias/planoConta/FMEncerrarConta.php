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
    * Página de Formulario de Inclusao/Alteracao de Conta Contabil
    * Data de Criação   : 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor Anderson: R. M. Buzo

    * @ignore

    * $Id: FMEncerrarConta.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-02.02.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoContaTCEMS.class.php");
include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php");
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "EncerrarConta";
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
    $stAcao = "encerrar";
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


$obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
$obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsAgencia );

$obRMONContaCorrente = new RMONContaCorrente;
$obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
$obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
$obRMONContaCorrente->listarContaCorrente( $rsContaCorrente );

$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

$inCodConta = $_GET['inCodConta'];
$inCodPlano = $_GET['inCodPlano'];
$obRContabilidadePlanoBanco->setCodConta( $inCodConta );
$obRContabilidadePlanoBanco->setCodPlano( $inCodPlano );
$obRContabilidadePlanoBanco->consultar();

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

$inCodRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecurso();

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

if (!$inCodEntidade) {
   $obROrcamentoConfiguracao     = new ROrcamentoConfiguracao;

   $obROrcamentoConfiguracao->setExercicio( Sessao::getExercicio() );
   $obROrcamentoConfiguracao->setCodModulo( 8 );
   $obROrcamentoConfiguracao->consultarConfiguracaoEspecifica('cod_entidade_prefeitura');
   $inCodEntidade = $obROrcamentoConfiguracao->getCodEntidadePrefeitura();
}

if ($stAcao == "encerrar") {
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

//Define Objeto Hidden para Classificacao Contabil
$obHdnClassContabil = new Hidden;
$obHdnClassContabil->setName ( "stCodClass" );
$obHdnClassContabil->setValue( $stCodClassificacao );

//Define Objeto Label para Código da Classificacao Contabil
$obLblCodClassContabil = new Label;
$obLblCodClassContabil->setRotulo    ( "Código de Classificação"                    );
$obLblCodClassContabil->setValue( $stCodClassificacao );

//Define Objeto Hidden para Código da Classificacao Contabil
$obHdnCodClassContabil = new Hidden;
$obHdnCodClassContabil->setName ( "inCodClassContabil" );
$obHdnCodClassContabil->setId   ( "inCodClassContabil" );
$obHdnCodClassContabil->setValue( $inCodClassContabil );

//Define Objeto Hidden para Descricao da Conta
$obHdnDescricaoConta = new Hidden;
$obHdnDescricaoConta->setName ( "stDescricaoConta" );
$obHdnDescricaoConta->setValue( $stNomConta );

// Define Objeto TextBox para Descrição da Conta
$obTxtDescrConta = new TextBox;
$obTxtDescrConta->setName     ( "stDescrConta"                 );
$obTxtDescrConta->setId       ( "stDescrConta"                 );
$obTxtDescrConta->setValue    (  $stNomConta                   );
$obTxtDescrConta->setSize     ( 80                             );
$obTxtDescrConta->setMaxLength( 150                            );
$obTxtDescrConta->setRotulo   ( "Descrição da Conta"           );
$obTxtDescrConta->setTitle    ( "Informe a descrição da conta" );
$obTxtDescrConta->setNull     ( false );
$obTxtDescrConta->setDisabled ( true );

$obHdnTipoConta = new Hidden;
$obHdnTipoConta->setName ('stTipoConta');
$obHdnTipoConta->setId ('stTipoConta');
$obHdnTipoConta->setValue ( $boContaAnalitica ? 'A' : 'S');

//Define Objeto Label para Tipo Conta
$obLblTipoConta = new Label;
$obLblTipoConta->setRotulo( 'Tipo de Conta' );
$obLblTipoConta->setValue( $boContaAnalitica ? 'Analítica' : 'Sintética' );

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
$obCmbNaturezaDoSaldo->setDisabled  ( true );

// Define Objeto TextBox para Codigo do Recurso
$obTxtRecurso = new TextBox;
$obTxtRecurso->setName    ( "inCodRecurso"                     );
$obTxtRecurso->setId      ( "inCodRecurso"                     );
$obTxtRecurso->setValue   ( $inCodRecurso                      );
$obTxtRecurso->setRotulo  ( "Recurso"                          );
$obTxtRecurso->setTitle   ( "Selecione o recurso orçamentário" );
$obTxtRecurso->setDisabled( $boDesabilitaRecurso               );
$obTxtRecurso->setMascara ( $stMascaraRecurso                  );
$obTxtRecurso->setPreencheComZeros ( 'E'                       );
$obTxtRecurso->setDisabled ( true );

// Define Objeto Select para o Recurso
$obCmbRecurso = new Select;
$obCmbRecurso->setName      ( "stNomeRecurso"    );
$obCmbRecurso->setId        ( "stNomeRecurso"    );
$obCmbRecurso->setValue     ( $inCodRecurso      );
$obCmbRecurso->addOption    ( "", "Selecione"    );
$obCmbRecurso->setCampoId   ( "[cod_fonte]"    );
$obCmbRecurso->setCampoDesc ( "[nom_recurso]"  );
$obCmbRecurso->preencheCombo( $rsRecurso         );
$obCmbRecurso->setDisabled  ( true );

//******************************************************//
// Define COMPONENTES DO FORMULARIO ABA Conta de banco
//******************************************************//
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
$obTxtCodEntidade->setDisabled ( true );
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
$obCmbNomEntidade->setDisabled ( true );
$obCmbNomEntidade->setStyle  ( "width: 500px;" );

// Define Objeto TextBox para Codigo do Banco
$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setValue    ( $_REQUEST['inNumBanco']         );
$obTxtBanco->setRotulo   ( "*Banco"            );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o banco" );
$obTxtBanco->setDisabled ( true                );
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
$obCmbBanco->setDisabled  ( true            );
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
$obTxtAgencia->setDisabled ( true                  );
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

$obCmbAgencia->setCampoId   ( "num_agencia"    );
$obCmbAgencia->setCampoDesc ( "nom_agencia"    );
$obCmbAgencia->preencheCombo( $rsAgencia       );
$obCmbAgencia->setDisabled  ( true             );

$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('MontaContaCorrente'); ");

$obHdnContaCorrente = new Hidden();
$obHdnContaCorrente->setName( 'inContaCorrente');
$obHdnContaCorrente->setId  ( 'inContaCorrente');
$obHdnContaCorrente->setValue( $_REQUEST['inContaCorrente']);

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo   ( "*Conta Corrente");
$obCmbContaCorrente->setName      ( "stContaCorrente"    );
$obCmbContaCorrente->setId        ( "stContaCorrente"    );
$obCmbContaCorrente->setValue     ( $_REQUEST['stContaCorrente']   );
$obCmbContaCorrente->addOption    ( "", "Selecione"          );
$obCmbContaCorrente->setCampoId ( "num_conta_corrente"  );
$obCmbContaCorrente->setCampoDesc( "num_conta_corrente" );
$obCmbContaCorrente->preencheCombo( $rsContaCorrente );
$obCmbContaCorrente->setCampoId   ( "num_conta_corrente"     );
$obCmbContaCorrente->setCampoDesc ( "num_conta_corrente"     );
$obCmbContaCorrente->setDisabled  ( true );
$obCmbContaCorrente->obEvento->setOnChange  ( " montaParametrosGET('BuscaContaCorrente'); ");

SistemaLegado::executaFramePrincipal($js);

$dtData = date("d/m/Y");
$obDtEncerramento = new Data();
$obDtEncerramento->setName    ( "stDtEncerramento" );
$obDtEncerramento->setId      ( "stDtEncerramento" );
$obDtEncerramento->setRotulo  ( "Data de encerramento da conta" );
$obDtEncerramento->setNull    ( false );
$obDtEncerramento->setValue   ( $dtData );

$obTxtAreaMotivo = new TextArea();
$obTxtAreaMotivo->setName   ( "stMotivo" );
$obTxtAreaMotivo->setId     ( "stMotivo" );
$obTxtAreaMotivo->setRotulo ( "Motivo" );
$obTxtAreaMotivo->setNull   ( false );

$stLocation = $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro;

$obBtnOk = new OK();
$obBtnOk->setValue('Encerrar');
$obCancelar  = new Cancelar;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Conta Contábil" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodConta );
$obFormulario->addHidden( $obHdnCodPlano );
$obFormulario->addHidden( $obHdnClassContabil );
$obFormulario->addHidden( $obHdnNatSaldo );
$obFormulario->addHidden( $obHdnCodClassContabil );
$obFormulario->addHidden( $obHdnBanco );
$obFormulario->addHidden( $obHdnAgencia );
$obFormulario->addHidden( $obHdnTipoConta );
$obFormulario->addHidden( $obHdnContaCorrente );
$obFormulario->addHidden( $hdnCodEntidade );
$obFormulario->addHidden( $obHdnDescricaoConta );
$obFormulario->addComponente( $obLblTipoConta );
$obFormulario->addComponente( $obCmbNaturezaDoSaldo);
$obFormulario->addComponente( $obLblCodClassContabil );
$obFormulario->addComponente( $obTxtDescrConta );
$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso );
$obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
$obFormulario->addComponenteComposto( $obTxtBanco  , $obCmbBanco   );
$obFormulario->addComponenteComposto( $obTxtAgencia, $obCmbAgencia );
$obFormulario->addComponente( $obCmbContaCorrente );
$obFormulario->addComponente( $obDtEncerramento );
$obFormulario->addComponente( $obTxtAreaMotivo );

$obFormulario->defineBarra( array( $obBtnOk, $obCancelar ) );

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
