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

    * @author Analista: Dagiane
    * @author Desenvolvedor: Lisiane Morais

    * @ignore

    $Revision:$
    $Name$
    $Author:$
    $Date:  $

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
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaContaSalario.class.php"               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc);

//Exclui "/" em caso de apóstrofe
$stNomCGM = stripslashes($_REQUEST['nom_cgm_pensionista']);

include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
$obTCGM = new TCGM;
$stFiltro = " WHERE CGM.numcgm = ".$_REQUEST['numcgm_pensionista'];
$obTCGM->recuperaRelacionamento($rsCGM,$stFiltro);

$dataNasc = SistemaLegado::dataToBr($rsCGM->getCampo('dt_nascimento'));

$obLblCGM = new Label;
$obLblCGM->setRotulo ( "CGM" );
$obLblCGM->setName   ( "stCGM" );
$obLblCGM->setValue  ( $_REQUEST['numcgm_pensionista']."-".$stNomCGM );

$obHdnCGM =  new Hidden;
$obHdnCGM->setName  ( "inCGM" );
$obHdnCGM->setValue ( $_REQUEST['numcgm_pensionista'] );

$obHdnPensionista =  new Hidden;
$obHdnPensionista->setName  ( "cod_pensionista" );
$obHdnPensionista->setValue ( $inCodPensionista );

$obLblNascimento = new Label;
$obLblNascimento->setRotulo ( "Data de Nascimento" );
$obLblNascimento->setValue  ( $dataNasc );
$obLblNascimento->setId     ( "dtNascimento" );

$obLblSexo = new Label;
$obLblSexo->setRotulo ( "Sexo" );
$obLblSexo->setValue  ( $rsCGM->getCampo('sexo'));
$obLblSexo->setId     ( "stSexo" );

$obLblRG = new Label;
$obLblRG->setRotulo ( "RG" );
$obLblRG->setValue  ( $rsCGM->getCampo('rg'));
$obLblRG->setId     ( "stRG" );

$obLblCPF = new Label;
$obLblCPF->setRotulo ( "CPF" );
$obLblCPF->setValue  ( $rsCGM->getCampo('cpf'));
$obLblCPF->setId     ( "stCPF" );

$obLblEndereco = new Label;
$obLblEndereco->setRotulo               ( "Endereço" );
$obLblEndereco->setValue                ( $rsCGM->getCampo('endereco'));
$obLblEndereco->setId                   ( "stEndereco" );
                                        
$obLblTelefone = new Label;             
$obLblTelefone->setRotulo               ( "Telefone Fixo" );
$obLblTelefone->setValue                ( $rsCGM->getCampo('fone_residencial') );
$obLblTelefone->setId                   ( "stTelefone" );

$obLblCelular = new Label;
$obLblCelular->setRotulo                ( "Telefone Celular"                                 );
$obLblCelular->setValue                 ( $rsCGM->getCampo('fone_celular')                   );
$obLblCelular->setId                    ( "stCelular"                                        );

$obTProfissao = new TProfissao;
$obTProfissao->setDado                  ( 'cod_profissao', $_REQUEST['cod_profissao']        );
$obTProfissao->recuperaPorChave         ( $rsProfissao                                       );

$obLblOcupacao = new Label;
$obLblOcupacao->setRotulo               ( "Ocupação"                                         );
$obLblOcupacao->setValue                ( $rsProfissao->getCampo('nom_profissao')            );
$obLblOcupacao->setId                   ( "stOcupação"                                       );
                                                                                             
$obLblCID = new Label;                                                                       
$obLblCID->setRotulo                    ( "CID"                                              );
$obLblCID->setValue                     ( $_REQUEST['cod_cid']                               );
$obLblCID->setId                        ( "stCID"                                            );

$obHdnCodCID = new Hidden;
$obHdnCodCID->setName                   ( "inCodCID"                                         );
$obHdnCodCID->setId                     ( "inCodCID"                                         );
$obHdnCodCID->setValue                  ( $_REQUEST['cod_cid']                               );

$grauParentesco = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$_REQUEST['cod_grau']);
$obLblGrauParentesco = new Label;
$obLblGrauParentesco->setRotulo         ( "Grau Parentesco"                                  );
$obLblGrauParentesco->setName           ( "grauParentesco"                                   );
$obLblGrauParentesco->setId             ( "grauParentesco"                                   );
$obLblGrauParentesco->setValue          ( $grauParentesco                                    );

$obTPessoalContratoPensionistaContaSalario = new TPessoalContratoPensionistaContaSalario;
$stFiltro = " AND contrato_pensionista_conta_salario.cod_contrato = ".$_REQUEST['cod_contrato'];
$obTPessoalContratoPensionistaContaSalario->recuperaRelacionamento($rsContaSalario,$stFiltro);

include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
$stFiltro = " WHERE cod_banco = ".$rsContaSalario->getCampo("cod_banco"). " AND  cod_agencia = ".$rsContaSalario->getCampo("cod_agencia");
$obTMONAgencia = new TMONAgencia;
$obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);

$stBanco = SistemaLegado::pegaDado('nom_banco', 'monetario.banco',  'WHERE cod_banco='.$rsContaSalario->getCampo("cod_banco"));
$obLblBanco = new Label;
$obLblBanco->setRotulo                  ( "Banco"                                            );
$obLblBanco->setValue                   ( $stBanco                                           );
$obLblBanco->setId                      ( "stBanco"                                          );

$obLblAgencia = new Label;
$obLblAgencia->setRotulo                ( "Agência"                                          );
$obLblAgencia->setValue                 ( $rsAgencia->getCampo('num_agencia')." - ". $rsAgencia->getCampo('nom_agencia') );
$obLblAgencia->setId                    ( "stAgencia"                                        );

$obLblContaCorrente =  new Label;
$obLblContaCorrente->setRotulo          ( "Conta Corrente"                                   );
$obLblContaCorrente->setId              ( "stNumConta"                                       );
$obLblContaCorrente->setValue           ( $rsContaSalario->getCampo('nr_conta')              );

$obLblContratoServidor = new Label;
$obLblContratoServidor->setRotulo       ( "Matrícula"                                        );
$obLblContratoServidor->setValue        ( $_REQUEST['registro_servidor']                     );

//Exclui "/" em caso de apóstrofe
$stNomCGMServidor = stripslashes        ( $_REQUEST['nom_cgm_servidor']                      );

$obLblCGMServidor = new Label;
$obLblCGMServidor->setRotulo            ( "CGM"                                              );
$obLblCGMServidor->setValue             ( $_REQUEST['numcgm_servidor']."-".$stNomCGMServidor );
                                        
$obHdnNomCGMRescisao = new Hidden;      
$obHdnNomCGMRescisao->setName           ( "stNomCGMRescisao"                                 );
$obHdnNomCGMRescisao->setValue          ( $_REQUEST['nom_cgm_servidor']                      );
                                        
$obHdnRegistroRescisao = new Hidden;    
$obHdnRegistroRescisao->setName         ( "inRegistroRescisao"                               );
$obHdnRegistroRescisao->setValue        ( $_REQUEST['registro_servidor']                     );
                                        
$obHdnRegistroServidor = new Hidden;    
$obHdnRegistroServidor->setName         ( "inContrato"                                       );
$obHdnRegistroServidor->setValue        ( $_REQUEST['registro_servidor']                     );
                                        
$obHdnContratoServidor = new Hidden;    
$obHdnContratoServidor->setName         ( "inCodContratoServidor"                            );
$obHdnContratoServidor->setValue        ( $_REQUEST['cod_contrato_cedente']                  );
                                        
$obLblContratoPensionista = new Label;  
$obLblContratoPensionista->setRotulo    ( "Matrícula do Pensionista"                         );
$obLblContratoPensionista->setValue     ( $_REQUEST['registro_servidor']                     );

$obHdnContratoPensionista = new Hidden;
$obHdnContratoPensionista->setName      ( "inCodContratoPensionista"                         );
$obHdnContratoPensionista->setValue     ( $_REQUEST['cod_contrato']                          );
                                                                                             
$obLblNumBeneficio = new Label;                                                              
$obLblNumBeneficio->setRotulo           ( "Número do Benefício"                              );
$obLblNumBeneficio->setId               ( "inNumBeneficio"                                   );
$obLblNumBeneficio->setValue            ( $_REQUEST['num_beneficio']                         );
                                                                                             
$obLblProcesso = new Label;                                                                  
$obLblProcesso->setRotulo               ( "Processo"                                         );
$obLblProcesso->setId                   ( 'inNumProcesso'                                    );
$obLblProcesso->setValue                ( $stChaveProcesso                                   );

$obLblDataInclusaoProcessao = new Label;
$obLblDataInclusaoProcessao->setRotulo ( "Data da Inclusão do Processo"                      );
$obLblDataInclusaoProcessao->setValue  ( $dtInclusaoProcesso                                 );
$obLblDataInclusaoProcessao->setId     ( "dtInclusaoProcesso"                                );

$tipoDependencia = SistemaLegado::pegaDado('descricao', 'pessoal.tipo_dependencia',  'WHERE cod_dependencia ='.$inCodTipoDependencia);
$obLblTipoDependencia = new Label;
$obLblTipoDependencia->setValue        ( $tipoDependencia                                    );
$obLblTipoDependencia->setRotulo       ( "Tipo de Dependência"                               );
$obLblTipoDependencia->setId           ( "inCodTipoDependencia"                              );

$obLblPercentualPagamentoPensao = new Label;
$obLblPercentualPagamentoPensao->setId          ( "nuPercentualPagamentoPensao"             );
$obLblPercentualPagamentoPensao->setRotulo      ( "Percentual de Pagamento da Pensão"       );
$obLblPercentualPagamentoPensao->setValue       ( $nuPercentualPagamentoPensao              );

$obDtaInicioBeneficio = new Label;
$obDtaInicioBeneficio->setId            ( "dtInicioBeneficio"                               );
$obDtaInicioBeneficio->setValue         ( $dtInicioBeneficio                                );
$obDtaInicioBeneficio->setRotulo        ( "Data de Início do Benefício"                     );
                                                                                            
$obDtaEncerramentoBeneficio = new Label;                                                    
$obDtaEncerramentoBeneficio->setId      ( "dtEncerramentoBeneficio"                         );
$obDtaEncerramentoBeneficio->setValue   ( $dtEncerramentoBeneficio                          );
$obDtaEncerramentoBeneficio->setRotulo  ( "Data de Encerramento do Benefício"               );
                                                                                            
$obSpnCalculoPensao = new Span;                                                             
$obSpnCalculoPensao->setId  ( "spnCalculoPensao"                                            );                                         
                                                                                            
$obTxtMotivoEncerramento = new Label;                                                       
$obTxtMotivoEncerramento->setId         ( "stMotivoEncerramento"                            );
$obTxtMotivoEncerramento->setValue      ( $stMotivoEncerramento                             );
$obTxtMotivoEncerramento->setRotulo     ( "Motivo do Encerramento"                          );

$obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
$obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
$obIMontaOrganograma = new IMontaOrganograma();
$obIMontaOrganograma->setCodOrgao($inCodLotacao);
$obIMontaOrganograma->setNivelObrigatorio(1);
$obIMontaOrganograma->setComponenteSomenteLeitura(true);
$obIMontaOrganograma->obROrganograma->setCodOrganograma($rsOrganogramaVigente->getCampo('cod_organograma'));

$obSpanPrevidencia = new Span;
$obSpanPrevidencia->setId ( "spnPrevidencia" );

?>
