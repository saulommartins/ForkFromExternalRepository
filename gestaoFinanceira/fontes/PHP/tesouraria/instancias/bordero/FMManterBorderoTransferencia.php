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
    * Página de Formulário para Arrecadação Receita
    * Data de Criação   : 23/01/2006

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30737 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20

*/

/*
$Log$
Revision 1.14  2006/07/05 20:39:07  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                     );
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->addArrecadacao();
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$rsBanco = new RecordSet;
$rsAgencia = new RecordSet;
$obRMONConta = new RMONContaCorrente;
$obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

$obForm = new Form;
$obForm->setAction ( $pgProc    );
$obForm->setTarget ( "oculto"   );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao"   );
$obHdnAcao->setValue( $stAcao   );

$obHdnNumBordero = new Hidden;
$obHdnNumBordero->setName( "inNumBordero"  );
$obHdnNumBordero->setValue( ""        );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl"   );
$obHdnCtrl->setValue( ""        );

$obHdnCodBancoCredor =  new Hidden;
$obHdnCodBancoCredor->setName   ( "inCodBancoCredor" );
$obHdnCodBancoCredor->setValue  ( $_REQUEST["inCodBancoCredor"]  );

$obHdnCodAgenciaCredor = new Hidden;
$obHdnCodAgenciaCredor->setName    ( "inCodAgenciaCredor"           );
$obHdnCodAgenciaCredor->setValue   ( $_REQUEST["inCodAgenciaCredor"]);

$obHdnAction = new Hidden;
$obHdnAction->setName("stAction");
$obHdnAction->setValue(CAM_FW_POPUPS."relatorio/OCRelatorio.php");

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."bordero/OCRelatorioBorderoTransferencia.php" );

$stEval = "
    if (trim(document.getElementById('spnLista').innerHTML) == '') {
        erro = true;
        mensagem += '@Adicione uma Transferência!()';
    } else {
        document.frm.inCodEntidade.disabled = false;
    }
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

$obApplet = new IAppletTerminal( $obForm );

// Define Objeto Select para Entidade
$obCmbEntidade = new Select();
$obCmbEntidade->setRotulo    ( "*Entidade"                );
$obCmbEntidade->setName      ( "inCodEntidade"            );
$obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
$obCmbEntidade->setCampoId   ( "cod_entidade"             );
$obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
$obCmbEntidade->setValue     ( $inCodEntidade             );
$obCmbEntidade->setNull      ( true                       );
if ($rsEntidade->getNumLinhas() > 1) {
    $obCmbEntidade->addOption    ( ""            ,"Selecione" );
    $obCmbEntidade->obEvento->setOnChange( "mostraSpanBoletim();" );
} else $jsSL = "mostraSpanBoletim();";
$obCmbEntidade->preencheCombo( $rsEntidade                );

$obSpanBoletim = new Span;
$obSpanBoletim->setId( "spnBoletim" );

//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "stExercicio"         );
$obTxtExercicio->setValue     ( Sessao::getExercicio()    );
$obTxtExercicio->setRotulo    ( "*Exercício"          );
$obTxtExercicio->setTitle     ( "Informe o Exercício" );
$obTxtExercicio->setNull      ( true                  );
$obTxtExercicio->setMaxLength ( 4                     );
$obTxtExercicio->setSize      ( 5                     );

$obSpanContaBanco = new Span;
$obSpanContaBanco->setId( "spnContaBanco" );

$obCmbTipoTransacao = new Select;
$obCmbTipoTransacao->setRotulo ( "*Tipo"                             );
$obCmbTipoTransacao->setName   ( "stTipoTransacao"                   );
$obCmbTipoTransacao->addOption ( "","Selecione"                      );
$obCmbTipoTransacao->addOption ( "6","Entre contas da mesma Entidade"        );
$obCmbTipoTransacao->addOption ( "7","Entre contas de Entidades diferentes"  );
$obCmbTipoTransacao->addOption ( "8" ,"Consignações"                 );
$obCmbTipoTransacao->setValue  ( ""                                  );
$obCmbTipoTransacao->setStyle  ( "width: 120px"                      );
$obCmbTipoTransacao->setNull   ( true                                );
$obCmbTipoTransacao->setTitle  ( "Informe o Tipo de Transação a efetuar" );
$obCmbTipoTransacao->obEvento->setOnChange ( "desabilitaCredor(this.value);"  );

// Define Objeto Numeric para valor
$obTxtValor = new Moeda;
$obTxtValor->setRotulo        ( "*Valor"          );
$obTxtValor->setTitle         ( "Informe o valor da transação" );
$obTxtValor->setName          ( "inValor"         );
$obTxtValor->setValue         ( $inValor          );
$obTxtValor->setSize          ( 10                );
$obTxtValor->setMaxLength     ( 10                );
$obTxtValor->setNull          ( true              );

//Define o objeto INNER para armazenar a Conta Banco
$obBscContaCredor = new BuscaInner;
$obBscContaCredor->setRotulo( "*Conta" );
$obBscContaCredor->setTitle( "Informe a conta (no caso de transferências entre contas da mesma entidade" );
$obBscContaCredor->setNull( true  );
$obBscContaCredor->setId( "stContaCredor" );
$obBscContaCredor->setValue( '' );
$obBscContaCredor->obCampoCod->setName("inCodContaCredor");
$obBscContaCredor->obCampoCod->setValue( "" );
$obBscContaCredor->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodContaCredor','stContaCredor','bordero_transf&inCodEntidade='+document.frm.inCodEntidade.value+'&stTipoTransacao='+document.frm.stTipoTransacao.value,'".Sessao::getId()."','800','550');" );
$obBscContaCredor->setValoresBusca( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), $obForm->getName(), 'bordero_transf' );

// Define Objeto BuscaInner para o Credor
$obBscCredor = new BuscaInner();
$obBscCredor->setRotulo               ( "*Credor"                                              );
$obBscCredor->setTitle                ( "Informe o Credor / Beneficiário"                      );
$obBscCredor->setId                   ( "stNomCredor"                                          );
$obBscCredor->setValue                ( $stNomCredor                                           );
$obBscCredor->setNull                 ( true                                                   );
$obBscCredor->obCampoCod->setName     ( "inCodCredor"                                          );
$obBscCredor->obCampoCod->setSize     ( 10                                                     );
$obBscCredor->obCampoCod->setMaxLength( 8                                                      );
$obBscCredor->obCampoCod->setValue    ( $inCodCredor                                           );
$obBscCredor->obCampoCod->setAlign    ( "left"                                                 );
$obBscCredor->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodCredor','stNomCredor','geral','".Sessao::getId()."','800','550');");
$obBscCredor->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

$obTxtBancoCredor = new TextBox;
$obTxtBancoCredor->setRotulo        ( "*Banco"                           );
$obTxtBancoCredor->setTitle         ( "Informe o banco do Credor"        );
$obTxtBancoCredor->setName          ( "inNumBancoCredor"                 );
$obTxtBancoCredor->setValue         ( $inNumBancoCredor                  );
$obTxtBancoCredor->setSize          ( 10                                 );
$obTxtBancoCredor->setMaxLength     ( 6                                  );
$obTxtBancoCredor->setNull          ( true                               );
$obTxtBancoCredor->setInteiro       ( true                               );
$obTxtBancoCredor->obEvento->setOnChange ( "preencheAgenciaCredor('');" );

$obCmbBancoCredor = new Select;
$obCmbBancoCredor->setRotulo        ( "Banco"                      );
$obCmbBancoCredor->setName          ( "cmbBancoCredor"             );
$obCmbBancoCredor->addOption        ( "", "Selecione"              );
$obCmbBancoCredor->setValue         ( $_REQUEST['inNumBancoCredor']);
$obCmbBancoCredor->setCampoId       ( "num_banco"                  );
$obCmbBancoCredor->setCampoDesc     ( "nom_banco"                  );
$obCmbBancoCredor->preencheCombo    ( $rsBanco                     );
$obCmbBancoCredor->setNull          ( true                         );
$obCmbBancoCredor->setStyle         ( "width: 220px"               );
$obCmbBancoCredor->obEvento->setOnChange ( "preencheAgenciaCredor('');"  );

$obTxtAgenciaCredor = new TextBox;
$obTxtAgenciaCredor->setRotulo        ( "*Agencia"                                 );
$obTxtAgenciaCredor->setTitle         ( "Informe a Agência do Credor"              );
$obTxtAgenciaCredor->setName          ( "inNumAgenciaCredor"                       );
$obTxtAgenciaCredor->setValue         ( $inNumAgenciaCredor                        );
$obTxtAgenciaCredor->setSize          ( 10                                            );
$obTxtAgenciaCredor->setMaxLength     ( 6                                             );
$obTxtAgenciaCredor->setNull          ( true                                          );
$obTxtAgenciaCredor->setInteiro       ( true                                          );
$obTxtAgenciaCredor->obEvento->setOnChange ( "preencheCamposCodigosCredor('');" );

$obCmbAgenciaCredor = new Select;
$obCmbAgenciaCredor->setName          ( "cmbAgenciaCredor"             );
$obCmbAgenciaCredor->addOption        ( "", "Selecione"                );
$obCmbAgenciaCredor->setValue         ( $_REQUEST['inNumAgenciaCredor']   );
$obCmbAgenciaCredor->setCampoId       ( "cod_agencia"                  );
$obCmbAgenciaCredor->setCampoDesc     ( "nom_agencia"                  );
$obCmbAgenciaCredor->preencheCombo    ( $rsAgencia                     );
$obCmbAgenciaCredor->setNull          ( true                           );
$obCmbAgenciaCredor->setStyle         ( "width: 220px"                 );
$obCmbAgenciaCredor->obEvento->setOnChange ( "preencheCamposCodigosCredor('');" );

$obTxtContaCorrenteCredor = new TextBox ;
$obTxtContaCorrenteCredor->setRotulo    ( "*Conta Corrente"                        );
$obTxtContaCorrenteCredor->setName      ( "stNumeroContaCredor"                    );
$obTxtContaCorrenteCredor->setValue     ( $stNumeroContaCredor                     );
$obTxtContaCorrenteCredor->setTitle     ( "Informe a Conta-Corrente do Credor"     );
$obTxtContaCorrenteCredor->setSize      ( 20                                       );
$obTxtContaCorrenteCredor->setMaxLength ( 30                                       );
$obTxtContaCorrenteCredor->setNull      ( true                                     );

$obTxtNrDocumento = new TextBox ;
$obTxtNrDocumento->setRotulo    ( "Nr Documento"                           );
$obTxtNrDocumento->setName      ( "inNrDocumento"                          );
$obTxtNrDocumento->setValue     ( $inNrDocumento                           );
$obTxtNrDocumento->setTitle     ( "Informe o nr. do documento de origem"   );
$obTxtNrDocumento->setSize      ( 20                                       );
$obTxtNrDocumento->setMaxLength ( 20                                       );
$obTxtNrDocumento->setNull      ( true                                     );

// Define Objeto TextArea para observações
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacao"                                   );
$obTxtObs->setId     ( "stObservacao"                                   );
$obTxtObs->setValue  ( $stObservacao                                    );
$obTxtObs->setRotulo ( "Observação"                                     );
$obTxtObs->setTitle  ( "Digite observações se forem necessárias"        );
$obTxtObs->setNull   ( true                                             );
$obTxtObs->setRows   ( 2                                                );
$obTxtObs->setCols   ( 100                                              );

$obSpan = new Span;
$obSpan->setId( "spnLista" );

$obBtnIncluir = new Button();
$obBtnIncluir->setValue( 'Incluir' );
$obBtnIncluir->obEvento->setOnClick( "inclui();" );

$obBtnLimpar = new Button();
$obBtnLimpar->setValue( 'Limpar' );
$obBtnLimpar->obEvento->setOnClick( "limpa();" );

$obBscCGM = new BuscaInner();
$obBscCGM->setRotulo               ( "CGM"                                                  );
$obBscCGM->setTitle                ( "Informe o CGM do(s) assinante(s) do Borderô"          );
$obBscCGM->setId                   ( "stNomAssinante_1"                                     );
$obBscCGM->setValue                ( $stNomAssinante_1                                      );
$obBscCGM->setNull                 ( true                                                   );
$obBscCGM->obCampoCod->setName     ( "inNumAssinante_1"                                     );
$obBscCGM->obCampoCod->setSize     ( 10                                                     );
$obBscCGM->obCampoCod->setMaxLength( 8                                                      );
$obBscCGM->obCampoCod->setValue    ( $inNumAssinante_1                                      );
$obBscCGM->obCampoCod->setAlign    ( "left"                                                 );
$obBscCGM->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_1','stNomAssinante_1','geral','".Sessao::getId()."','800','550');");
$obBscCGM->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

$obTxtMatricula = new TextBox;
$obTxtMatricula->setRotulo        ( "Nr. Matrícula"                               );
$obTxtMatricula->setTitle         ( "Informe a Matrícula do(s) assinante(s) do Borderô" );
$obTxtMatricula->setName          ( "inNumMatricula_1"                            );
$obTxtMatricula->setValue         ( $inNumMatricula_1                             );
$obTxtMatricula->setSize          ( 10                                            );
$obTxtMatricula->setMaxLength     ( 6                                             );
$obTxtMatricula->setNull          ( true                                          );
$obTxtMatricula->setInteiro       ( true                                          );

$obLblCargo = new Label();
$obLblCargo->setRotulo( "Cargo"     );
$obLblCargo->setId    ( "stCargo"   );
$obLblCargo->setValue ( "Cargo"     );

$obTxtCargo = new TextBox;
$obTxtCargo->setRotulo        ( "Cargo"                                   );
$obTxtCargo->setTitle         ( "Informe o Cargo do(s) assinante(s) do Borderô" );
$obTxtCargo->setName          ( "stCargo_1"                               );
$obTxtCargo->setValue         ( $stCargo_1                                );
$obTxtCargo->setSize          ( 30                                        );
$obTxtCargo->setMaxLength     ( 25                                        );
$obTxtCargo->setNull          ( true                                      );

$obBscCGM2 = new BuscaInner();
$obBscCGM2->setRotulo               ( "CGM"                                                  );
$obBscCGM2->setTitle                ( "Informe o CGM do(s) Assinante(s) do Borderô"          );
$obBscCGM2->setId                   ( "stNomAssinante_2"                                     );
$obBscCGM2->setValue                ( $stNomAssinante_2                                      );
$obBscCGM2->setNull                 ( true                                                   );
$obBscCGM2->obCampoCod->setName     ( "inNumAssinante_2"                                     );
$obBscCGM2->obCampoCod->setSize     ( 10                                                     );
$obBscCGM2->obCampoCod->setMaxLength( 8                                                      );
$obBscCGM2->obCampoCod->setValue    ( $inNumAssinante_2                                      );
$obBscCGM2->obCampoCod->setAlign    ( "left"                                                 );
$obBscCGM2->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_2','stNomAssinante_2','geral','".Sessao::getId()."','800','550');");
$obBscCGM2->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

$obTxtMatricula2 = new TextBox;
$obTxtMatricula2->setRotulo        ( "Nr. Matrícula"                               );
$obTxtMatricula2->setTitle         ( "Informe a Matrícula do(s) assinante(s) do Borderô" );
$obTxtMatricula2->setName          ( "inNumMatricula_2"                            );
$obTxtMatricula2->setValue         ( $inNumMatricula_2                             );
$obTxtMatricula2->setSize          ( 10                                            );
$obTxtMatricula2->setMaxLength     ( 6                                             );
$obTxtMatricula2->setNull          ( true                                          );
$obTxtMatricula2->setInteiro       ( true                                          );

$obLblCargo2 = new Label();
$obLblCargo2->setRotulo( "Cargo"      );
$obLblCargo2->setId    ( "stCargo2"   );
$obLblCargo2->setValue ( "Cargo"      );

$obTxtCargo2 = new TextBox;
$obTxtCargo2->setRotulo        ( "Cargo"                                   );
$obTxtCargo2->setTitle         ( "Informe o Cargo do(s) assinante(s) do Borderô" );
$obTxtCargo2->setName          ( "stCargo_2"                               );
$obTxtCargo2->setValue         ( $stCargo_2                                );
$obTxtCargo2->setSize          ( 30                                        );
$obTxtCargo2->setMaxLength     ( 25                                        );
$obTxtCargo2->setNull          ( true                                      );

$obBscCGM3 = new BuscaInner();
$obBscCGM3->setRotulo               ( "CGM"                                                  );
$obBscCGM3->setTitle                ( "Informe o CGM do(s) assinante(s) do Borderô"          );
$obBscCGM3->setId                   ( "stNomAssinante_3"                                     );
$obBscCGM3->setValue                ( $stNomAssinante_3                                      );
$obBscCGM3->setNull                 ( true                                                   );
$obBscCGM3->obCampoCod->setName     ( "inNumAssinante_3"                                     );
$obBscCGM3->obCampoCod->setSize     ( 10                                                     );
$obBscCGM3->obCampoCod->setMaxLength( 8                                                      );
$obBscCGM3->obCampoCod->setValue    ( $inNumAssinante_3                                      );
$obBscCGM3->obCampoCod->setAlign    ( "left"                                                 );
$obBscCGM3->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_3','stNomAssinante_3','geral','".Sessao::getId()."','800','550');");
$obBscCGM3->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

$obTxtMatricula3 = new TextBox;
$obTxtMatricula3->setRotulo        ( "Nr. Matrícula"                               );
$obTxtMatricula3->setTitle         ( "Informe a Matrícula do(s) assinante(s) do Borderô" );
$obTxtMatricula3->setName          ( "inNumMatricula_3"                            );
$obTxtMatricula3->setValue         ( $inNumMatricula_3                             );
$obTxtMatricula3->setSize          ( 10                                            );
$obTxtMatricula3->setMaxLength     ( 6                                             );
$obTxtMatricula3->setNull          ( true                                          );
$obTxtMatricula3->setInteiro       ( true                                          );

$obLblCargo3 = new Label();
$obLblCargo3->setRotulo( "Cargo"      );
$obLblCargo3->setId    ( "stCargo2"   );
$obLblCargo3->setValue ( "Cargo"      );

$obTxtCargo3 = new TextBox;
$obTxtCargo3->setRotulo        ( "Cargo"                                   );
$obTxtCargo3->setTitle         ( "Informe o Cargo do(s) assinante(s) do Borderô" );
$obTxtCargo3->setName          ( "stCargo_3"                               );
$obTxtCargo3->setValue         ( $stCargo_3                                );
$obTxtCargo3->setSize          ( 30                                        );
$obTxtCargo3->setMaxLength     ( 25                                        );
$obTxtCargo3->setNull          ( true                                      );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo     ( "Dados para Borderô de Transferências" );
$obFormulario->addForm       ( $obForm                  );
$obFormulario->addHidden     ( $obHdnAcao               );
$obFormulario->addHidden     ( $obHdnCtrl               );
$obFormulario->addHidden     ( $obApplet                );
$obFormulario->addHidden     ( $obHdnCodBancoCredor     );
$obFormulario->addHidden     ( $obHdnCodAgenciaCredor   );
$obFormulario->addHidden     ( $obHdnEval, true         );
$obFormulario->addHidden     ( $obHdnAction             );
$obFormulario->addHidden     ( $obHdnNumBordero         );
$obFormulario->addHidden     ( $obHdnCaminho            );
$obFormulario->addComponente ( $obCmbEntidade           );
$obFormulario->addSpan       ( $obSpanBoletim           );
$obFormulario->addComponente ( $obTxtExercicio          );
$obFormulario->addSpan       ( $obSpanContaBanco        );
$obFormulario->addTitulo     ( "Dados para Transferência entre Entidades / Consignações" );
$obFormulario->addComponente ( $obCmbTipoTransacao      );
$obFormulario->addComponente ( $obTxtValor              );
$obFormulario->addComponente ( $obBscContaCredor        );
$obFormulario->addComponente ( $obBscCredor             );
$obFormulario->addComponenteComposto ( $obTxtBancoCredor, $obCmbBancoCredor       );
$obFormulario->addComponenteComposto ( $obTxtAgenciaCredor, $obCmbAgenciaCredor   );
$obFormulario->addComponente ( $obTxtContaCorrenteCredor      );
$obFormulario->addComponente ( $obTxtNrDocumento        );
$obFormulario->addComponente ( $obTxtObs                );

$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan( $obSpan );

$obFormulario->addTitulo     ( "Assinantes" );
$obFormulario->addComponente ( $obBscCGM      );
$obFormulario->agrupaComponentes( array($obTxtMatricula, $obLblCargo, $obTxtCargo) );
$obFormulario->addComponente ( $obBscCGM2     );
$obFormulario->agrupaComponentes( array($obTxtMatricula2, $obLblCargo2, $obTxtCargo2) );
$obFormulario->addComponente ( $obBscCGM3     );
$obFormulario->agrupaComponentes( array($obTxtMatricula3, $obLblCargo3, $obTxtCargo3) );

$obBtnPreEmissao = new Button;
$obBtnPreEmissao->setValue("Pré-Emissão do Borderô para Conferência");
$obBtnPreEmissao->obEvento->setOnClick( "mostraPreEmissao();" );
$obFormulario->defineBarra(array($obBtnPreEmissao));

$obBtnOk = new Ok;
$obBtnCancelar = new Button;
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->obEvento->setOnClick( "limpaForm();" );

$obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );

$obFormulario->show();
if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
