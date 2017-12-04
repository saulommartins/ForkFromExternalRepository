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
    * Página de Manter Cadastro de Pensionista
    * Data de Criação: 14/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2007-12-13 14:51:02 -0200 (Qui, 13 Dez 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCID.class.php"                                              );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGrauParentesco.class.php"                                   );
include_once ( CAM_GA_CSE_MAPEAMENTO."TProfissao.class.php"                                             );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaCasoCausa.class.php"                  );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php"                                         );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php" 					);
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"                );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );

//Exclui "/" em caso de apóstrofe
$stNomCGM = stripslashes($_REQUEST['stNomCGM']);

$obLblCGM = new Label;
$obLblCGM->setRotulo ( "CGM" );
$obLblCGM->setName   ( "stCGM" );
$obLblCGM->setValue  ( $_REQUEST['inCGM']."-".$stNomCGM );

$obHdnCGM =  new Hidden;
$obHdnCGM->setName  ( "inCGM" );
$obHdnCGM->setValue ( $_REQUEST['inCGM'] );

$obHdnPensionista =  new Hidden;
$obHdnPensionista->setName  ( "inCodPensionista" );
$obHdnPensionista->setValue ( $inCodPensionista );

$obLblNascimento = new Label;
$obLblNascimento->setRotulo ( "Data de Nascimento" );
$obLblNascimento->setValue  ( $dtNascimento );
$obLblNascimento->setId     ( "dtNascimento" );

$obLblSexo = new Label;
$obLblSexo->setRotulo ( "Sexo" );
$obLblSexo->setValue  ( $stSexo );
$obLblSexo->setId     ( "stSexo" );

$obLblRG = new Label;
$obLblRG->setRotulo ( "RG" );
$obLblRG->setValue  ( $stRG );
$obLblRG->setId     ( "stRG" );

$obLblCPF = new Label;
$obLblCPF->setRotulo ( "CPF" );
$obLblCPF->setValue  ( $stCPF );
$obLblCPF->setId     ( "stCPF" );

$obLblEndereco = new Label;
$obLblEndereco->setRotulo ( "Endereço" );
$obLblEndereco->setValue  ( $stEndereco );
$obLblEndereco->setId     ( "stEndereco" );

$obLblTelefone = new Label;
$obLblTelefone->setRotulo ( "Telefone Fixo" );
$obLblTelefone->setValue  ( $stTelefone );
$obLblTelefone->setId     ( "stTelefone" );

$obLblCelular = new Label;
$obLblCelular->setRotulo ( "Telefone Celular" );
$obLblCelular->setValue ( $stCelular         );
$obLblCelular->setId ( "stCelular"        );

$obTProfissao = new TProfissao;
$obTProfissao->recuperaTodos($rsProfissao);
$obCmbOcupacao = new Select;
$obCmbOcupacao->setName       ( "inCodProfissao"                     );
$obCmbOcupacao->setValue      ( $inCodProfissao                      );
$obCmbOcupacao->setStyle      ( "width: 250px"                       );
$obCmbOcupacao->setRotulo     ( "Ocupação"                           );
$obCmbOcupacao->setNull       ( false                                );
$obCmbOcupacao->addOption     ( "", "Selecione"                      );
$obCmbOcupacao->setCampoID    ( "[cod_profissao]"                    );
$obCmbOcupacao->setCampoDesc  ( "[nom_profissao]"                    );
$obCmbOcupacao->setTitle      ( "Informe a ocupação do pensionista." );
$obCmbOcupacao->preencheCombo ( $rsProfissao                                                          );

$obBscCID = new BuscaInner;
$obBscCID->setRotulo                       ( "CID"                                      );
$obBscCID->setTitle                        ( "Informe o CID do servidor, caso exista."  );
$obBscCID->setNull                         ( true                                       );
$obBscCID->setName                         ( "stCID"                                    );
$obBscCID->setId                           ( "stCID"                                    );
$obBscCID->setValue                        ( $stDescricaoCID                            );
$obBscCID->obCampoCod->setName             ( "inSiglaCID"                               );
$obBscCID->obCampoCod->setId               ( "inSiglaCID"                               );
$obBscCID->obCampoCod->setValue            ( $inSiglaCID                                );
$obBscCID->obCampoCod->setInteiro          ( false                                      );
$obBscCID->obCampoCod->setSize             ( 10                                         );
$obBscCID->obCampoCod->setAlign            ( "left"                                     );
$obBscCID->obCampoCod->setToUpperCase      ( true                                       );
$obBscCID->obCampoCod->obEvento->setOnBlur ( "montaParametrosGET('buscaCID');\n"        );
$obBscCID->setFuncaoBusca                  ( "abrePopUp('".CAM_GRH_PES_POPUPS."CID/FLProcurarCID.php','frm','inSiglaCID','stCID','','".Sessao::getId()."','800','550')" );

$obHdnCodCID = new Hidden;
$obHdnCodCID->setName        ( "inCodCID"                                               );
$obHdnCodCID->setId          ( "inCodCID"                                               );
$obHdnCodCID->setValue       ( $inCodCID                                                );



$obTxtCodParentesco = new TextBox;
$obTxtCodParentesco->setRotulo    ( "Grau Parentesco"                                                     );
$obTxtCodParentesco->setTitle     ( "Informe o grau de parentesco do pensionista em relação ao servidor." );
$obTxtCodParentesco->setName      ( "inCodGrauParentesco"                                                 );
$obTxtCodParentesco->setValue     ( $inCodGrauParentesco                                                  );
$obTxtCodParentesco->setMaxLength ( 10    );
$obTxtCodParentesco->setSize      ( 10    );
$obTxtCodParentesco->setNull      ( false );

$obRPessoalGrauParentesco = new RPessoalGrauParentesco;
$obRPessoalGrauParentesco->listarGrauParentesco( $rsGrauParentesco );
$obCmbCodParentesco = new Select;
$obCmbCodParentesco->setName       ( "stGrauParentesco"   );
$obCmbCodParentesco->setValue      ( $inCodGrauParentesco );
$obCmbCodParentesco->setStyle      ( "width: 250px"       );
$obCmbCodParentesco->setRotulo     ( "Grau Parentesco"    );
$obCmbCodParentesco->setNull       ( false                );
$obCmbCodParentesco->addOption     ( "", "Selecione"      );
$obCmbCodParentesco->setCampoID    ( "[cod_grau]"         );
$obCmbCodParentesco->setCampoDesc  ( "[nom_grau]"         );
$obCmbCodParentesco->preencheCombo ( $rsGrauParentesco    );

$obIMontaAgencia = new IMontaAgencia();
$obIMontaAgencia->obITextBoxSelectBanco->setTitle             ( "Informe o banco para pagamento." );
$obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setTitle  ( "Informe o banco para pagamento." );
$obIMontaAgencia->obITextBoxSelectBanco->obSelect->setTitle   ( "Informe o banco para pagamento." );
$obIMontaAgencia->obITextBoxSelectBanco->setNull(false);
$obIMontaAgencia->obTextBoxSelectAgencia->obTextBox->setTitle ( "Informe a agência para pagamento." );
$obIMontaAgencia->obTextBoxSelectAgencia->setTitle ( "Informe a agência para pagamento." );
$obIMontaAgencia->obTextBoxSelectAgencia->setNull(false);

$obTxtContaCorrente = new TextBox();
$obTxtContaCorrente->setRotulo("Conta Corrente");
$obTxtContaCorrente->setTitle("Digite o número da conta corrente.");
$obTxtContaCorrente->setName("stNumConta");
$obTxtContaCorrente->setId("stNumConta");
$obTxtContaCorrente->setSize(11);
$obTxtContaCorrente->setMaxLength(11);

$obIFiltroContrato = new IFiltroContrato(true);
$obIFiltroContrato->setTituloFormulario         ( "Dados da Matrícula do Gerador do Benefício"                           );
$obIFiltroContrato->obIContratoDigitoVerificador->setNull( false                                 );
$obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange( "montaParametrosGET('verificaContrato','inContrato');" );
$obIFiltroContrato->obIContratoDigitoVerificador->setTipo("geral_contrato_servidor");

$obLblContratoServidor = new Label;
$obLblContratoServidor->setRotulo               ( "Matrícula"                                                           );
$obLblContratoServidor->setValue                ( $_REQUEST['inRegistroServidor']                                       );

//Exclui "/" em caso de apóstrofe
$stNomCGMServidor = stripslashes($_REQUEST['stNomCGMServidor']);

$obLblCGMServidor = new Label;
$obLblCGMServidor->setRotulo                    ( "CGM"                                                                 );
$obLblCGMServidor->setValue                     ( $_REQUEST['inCGMServidor']."-".$stNomCGMServidor                      );

$obHdnNomCGMRescisao = new Hidden;
$obHdnNomCGMRescisao->setName                   ( "stNomCGMRescisao"                                                    );
$obHdnNomCGMRescisao->setValue                  ( $_REQUEST['stNomCGM']                                                 );

$obHdnRegistroRescisao = new Hidden;
$obHdnRegistroRescisao->setName                 ( "inRegistroRescisao"                                                  );
$obHdnRegistroRescisao->setValue                ( $_REQUEST['inRegistroPensionista']                                    );

$obHdnRegistroServidor = new Hidden;
$obHdnRegistroServidor->setName                 ( "inContrato"                                                          );
$obHdnRegistroServidor->setValue                ( $_REQUEST['inRegistroServidor']                                       );

$obHdnContratoServidor = new Hidden;
$obHdnContratoServidor->setName                 ( "inCodContratoServidor"                                               );
$obHdnContratoServidor->setValue                ( $_REQUEST['inCodContratoServidor']                                    );

$obIContratoDigitoVerificador = new IContratoDigitoVerificador;
$obIContratoDigitoVerificador->setExtender      ( "Pensionista"                                                         );
$obIContratoDigitoVerificador->setRotulo        ("Matrícula do Pensionista"                                             );
$obIContratoDigitoVerificador->setNull          (false                                                                  );
$obIContratoDigitoVerificador->setTitle         ("Informe o contrato do pensionista."                                   );

$obLblContratoPensionista = new Label;
$obLblContratoPensionista->setRotulo            ( "Matrícula do Pensionista"                                            );
$obLblContratoPensionista->setValue             ( $_REQUEST['inRegistroPensionista']                                    );

$obHdnContratoPensionista = new Hidden;
$obHdnContratoPensionista->setName              ( "inCodContratoPensionista"                                            );
$obHdnContratoPensionista->setValue             ( $_REQUEST['inCodContratoPensionista']                                 );

$obTxtNumBeneficio = new TextBox;
$obTxtNumBeneficio->setRotulo    ( "Número do Benefício"                                                 );
$obTxtNumBeneficio->setTitle     ( "Informe o número do benefício relacionado ao Tribunal de Contas."    );
$obTxtNumBeneficio->setName      ( "inNumBeneficio"                                                      );
$obTxtNumBeneficio->setValue     ( $inNumBeneficio                                                       );
$obTxtNumBeneficio->setMaxLength ( 15                                                                    );
$obTxtNumBeneficio->setSize      ( 12                                                                    );
$obTxtNumBeneficio->setInteiro   ( true                                                                  );

$obIPopUpProcesso = new IPopUpProcesso($obForm);
$obIPopUpProcesso->obCampoCod->obEvento->setOnBlur("montaParametrosGET('processarProcesso','stChaveProcesso');"         );

$obLblDataInclusaoProcessao = new Label;
$obLblDataInclusaoProcessao->setRotulo ( "Data da Inclusão do Processo" );
$obLblDataInclusaoProcessao->setValue  ( $dtInclusaoProcesso );
$obLblDataInclusaoProcessao->setId     ( "dtInclusaoProcesso" );

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDependencia.class.php");
$obTPessoalTipoDependencia = new TPessoalTipoDependencia;
$obTPessoalTipoDependencia->recuperaTodos($rsTipoDependencia);
$obCmbTipoDependencia = new Select;
$obCmbTipoDependencia->setName       ( "inCodTipoDependencia"             );
$obCmbTipoDependencia->setValue      ( $inCodTipoDependencia              );
$obCmbTipoDependencia->setStyle      ( "width: 250px"                     );
$obCmbTipoDependencia->setRotulo     ( "Tipo de Dependência"              );
$obCmbTipoDependencia->setNull       ( false                              );
$obCmbTipoDependencia->addOption     ( "", "Selecione"                    );
$obCmbTipoDependencia->setCampoID    ( "[cod_dependencia]"                );
$obCmbTipoDependencia->setCampoDesc  ( "[descricao]"                      );
$obCmbTipoDependencia->setNull       ( false                              );
$obCmbTipoDependencia->setTitle      ( "Selecione o tipo de dependência." );
$obCmbTipoDependencia->preencheCombo ( $rsTipoDependencia                 );

$obNumPercentualPagamentoPensao = new Numerico;
$obNumPercentualPagamentoPensao->setName        ( "nuPercentualPagamentoPensao"                                         );
$obNumPercentualPagamentoPensao->setId          ( "nuPercentualPagamentoPensao"                                         );
$obNumPercentualPagamentoPensao->setAlign       ( "RIGHT"                                                               );
$obNumPercentualPagamentoPensao->setRotulo      ( "Percentual de Pagamento da Pensão"                                   );
$obNumPercentualPagamentoPensao->setMaxLength   ( 5                                                                     );
$obNumPercentualPagamentoPensao->setSize        ( 10                                                                    );
$obNumPercentualPagamentoPensao->setDecimais    ( 2                                                                     );
$obNumPercentualPagamentoPensao->setNegativo    ( false                                                                 );
$obNumPercentualPagamentoPensao->setNull        ( true                                                                  );
$obNumPercentualPagamentoPensao->setValue       ( $nuPercentualPagamentoPensao                                          );
$obNumPercentualPagamentoPensao->setTitle       ( "Informe o percentual para pagamento da pensão."                      );
$obNumPercentualPagamentoPensao->setDisabled    ( true                                                                  );
if ($stAcao == "incluir") {
    $obNumPercentualPagamentoPensao->obEvento->setOnChange( "montaParametrosGET('validarPercentual','nuPercentualPagamentoPensao,inContrato')" );
} else {
    $obNumPercentualPagamentoPensao->obEvento->setOnChange( "montaParametrosGET('validarPercentual','nuPercentualPagamentoPensao,inContrato,inCodContratoPensionista')" );
}

$obDtaInicioBeneficio = new Data;
$obDtaInicioBeneficio->setName                  ( "dtInicioBeneficio"                                                   );
$obDtaInicioBeneficio->setValue                 ( $dtInicioBeneficio                                                    );
$obDtaInicioBeneficio->setRotulo                ( "Data de Início do Benefício"                                         );
$obDtaInicioBeneficio->setTitle                 ( "Informe a data de início do benefício."                              );
$obDtaInicioBeneficio->setNull                  ( false );
$obDtaInicioBeneficio->obEvento->setOnChange    ( "montaParametrosGET('validarDatas','dtInicioBeneficio,dtEncerramentoBeneficio');" );

$obDtaEncerramentoBeneficio = new Data;
$obDtaEncerramentoBeneficio->setName    ( "dtEncerramentoBeneficio"                      );
$obDtaEncerramentoBeneficio->setId      ( "dtEncerramentoBeneficio"                      );
$obDtaEncerramentoBeneficio->setValue   ( $dtEncerramentoBeneficio                       );
$obDtaEncerramentoBeneficio->setRotulo  ( "Data de Encerramento do Benefício"            );
$obDtaEncerramentoBeneficio->setTitle   ( "Informe a data de encerramento do benefício." );
if ($stAcao == "alterar") {
    $obDtaEncerramentoBeneficio->obEvento->setOnChange( "montaParametrosGET('geraHTMLCalculoPensao','dtInicioBeneficio,dtEncerramentoBeneficio');" );
} else {
    $obDtaEncerramentoBeneficio->obEvento->setOnChange( "montaParametrosGET('validarDatas','dtInicioBeneficio,dtEncerramentoBeneficio');"          );
}

// Verifica se já possui rescisão de contrato com calculo
// Caso possua e já tenha passado do perÍodo de movimentacÃo atual, não poderá alterar seu valor
if ($stAcao == "alterar") {
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php" );
    $obRPessoalRescisaoContrato = new RPessoalRescisaoContrato();

    if ($obRPessoalRescisaoContrato->desabilitarDataRescisaoPensionista($_REQUEST['inCodContratoPensionista'])) {
        $obDtaEncerramentoBeneficio->setReadOnly    ( true );
    }
}

$obSpnCalculoPensao = new Span;
$obSpnCalculoPensao->setId  ( "spnCalculoPensao" );

$obTxtMotivoEncerramento = new TextArea;
$obTxtMotivoEncerramento->setName   ( "stMotivoEncerramento"              );
$obTxtMotivoEncerramento->setValue  ( $stMotivoEncerramento               );
$obTxtMotivoEncerramento->setRotulo ( "Motivo do Encerramento"            );
$obTxtMotivoEncerramento->setTitle  ( "Informe o motivo do encerramento." );

$obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
$obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
$obIMontaOrganograma = new IMontaOrganograma();
$obIMontaOrganograma->setCodOrgao($inCodLotacao);
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->obROrganograma->setCodOrganograma($rsOrganogramaVigente->getCampo('cod_organograma'));

$obSpanPrevidencia = new Span;
$obSpanPrevidencia->setId ( "spnPrevidencia" );

?>
