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
* Página de Aba de Contrato
* Data de Criação   : ???

* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 32866 $
$Name$
$Author: souzadl $
$Date: 2006-10-25 07:42:42 -0300 (Qua, 25 Out 2006) $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                         );

//INFORMAÇÕES CONTRATUAIS
$obLblContrato = new Label();
$obLblContrato->setRotulo("Matrícula");
$obLblContrato->setValue($inMatricula);

//registro do cartao ponto
$obLblCartaoPonto = new Label;
$obLblCartaoPonto->setRotulo                            ( "Cartão Ponto"                                    );
$obLblCartaoPonto->setName                              ( "inCartaoPonto"                                   );
$obLblCartaoPonto->setValue                             ( $inCartaoPonto                                    );

if ($_GET['inCodContrato'] != "") {
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php");
    $obTPessoalAdidoCedido = new TPessoalAdidoCedido();
    $stFiltro = " AND contrato.cod_contrato = ".$_GET['inCodContrato'];
    $obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro);
    Sessao::write('rsAdidoCedido', $rsAdidoCedido);
    switch (true) {
        case $rsAdidoCedido->getNumLinhas() == -1:
            $stTipoCedencia = "Nenhum";
            break;
        case $rsAdidoCedido->getCampo("tipo_cedencia") == "c":
            $stTipoCedencia = "Cedido";
            break;
        case $rsAdidoCedido->getCampo("tipo_cedencia") == "a":
            $stTipoCedencia = "Adido";
            break;
    }
}

$obLblCedencia = new Label();
$obLblCedencia->setRotulo("Tipo de Cedência");
$obLblCedencia->setId("stTipoCedencia");
$obLblCedencia->setValue($stTipoCedencia);

$obSpnCedencia = new Span();
$obSpnCedencia->setId("spnCedencia");

$obLblSituacao = new Label();
$obLblSituacao->setId      ( "stSituacao" );
$obLblSituacao->setValue   ( $stSituacao  );
$obLblSituacao->setRotulo  ( "Situação"   );

$obSpanAposentadoria = new Span();
$obSpanAposentadoria->setId("spnAposentadoria");

$obLblDataNomeacao = new Label;
$obLblDataNomeacao->setName               ( "dtDataNomeacao"           );
$obLblDataNomeacao->setRotulo             ( "Data de Nomeação"         );
$obLblDataNomeacao->setValue              ( $dtDataNomeacao            );

include_once ( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php"       );
$obTTipoNorma = new TTipoNorma();
$stFiltro = " WHERE cod_tipo_norma = ".$inCodTipoNormaTxt;
$obTTipoNorma->recuperaTodos( $rsTipoNorma, $stFiltro );

$obLblTipoNorma = new Label();
$obLblTipoNorma->setName("stTipoNorma");
$obLblTipoNorma->setRotulo("Tipo Norma");
$obLblTipoNorma->setValue($inCodTipoNormaTxt."-".$rsTipoNorma->getCampo("nom_tipo_norma"));

include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"           );
$obTNorma = new TNorma();
$stFiltro = " where cod_norma = ".$inCodNorma;
$obTNorma->recuperaNormas( $rsNorma, $stFiltro );

$obLblPortariaNomeacao = new Label();
$obLblPortariaNomeacao->setName("stPortaria");
$obLblPortariaNomeacao->setRotulo("Portaria de Nomeação");
$obLblPortariaNomeacao->setValue($rsNorma->getCampo("num_norma_exercicio")."-".$rsNorma->getCampo("nom_norma"));

$obLblDataPosse = new Label;
$obLblDataPosse->setName   ( "dtDataPosse" );
$obLblDataPosse->setRotulo ( "Data da Posse" );
$obLblDataPosse->setValue  ( $dtDataPosse  );

$obLblDataProgressao = new Label;
$obLblDataProgressao->setName   ( "dtDataProgressao" );
$obLblDataProgressao->setRotulo ( "Data Início para Progressão " );
$obLblDataProgressao->setValue  ( $dtDataProgressao  );

$obLblDataExameMedico = new Label;
$obLblDataExameMedico->setName   ( "dtValidadeExameMedico" );
$obLblDataExameMedico->setRotulo ( "Validade do Exame Médico" );
$obLblDataExameMedico->setValue  ( $dtValidadeExameMedico  );

// Tipo de admissao
$obLblTipoAdmissao = new Label;
$obLblTipoAdmissao->setRotulo                         ( "Tipo de Admissão"                              );
$obLblTipoAdmissao->setValue                          ( $inCodTipoAdmissao."-".$stTipoAdmissao                                 );
$obLblTipoAdmissao->setName                           ( "stTipoAdmissao"                              );

//Tipo de vinculo empregaticio
$obLblVinculoEmpregaticio = new Label;
$obLblVinculoEmpregaticio->setRotulo                         ( "Vínculo Empregatício"                               );
$obLblVinculoEmpregaticio->setName                             ( "stVinculoEmpregaticio"                              );
$obLblVinculoEmpregaticio->setValue                          ( $inCodVinculoEmpregaticio."-".$stVinculoEmpregaticio                               );

//Selecão da categoria
$obLblCategoria = new Label;
$obLblCategoria->setRotulo                         ( "Categoria"                                );
$obLblCategoria->setName                             ( "stCategoria"                              );
$obLblCategoria->setValue                          ( $inCodCategoria."-".$stCategoria                               );

//Selecão dos agentes nocivos
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalOcorrencia.class.php"   );
$obTPessoalOcorrencia = NEW TPessoalOcorrencia();
$stFiltro = " WHERE num_ocorrencia = ".$stNumClassificacao;
$obTPessoalOcorrencia->recuperaTodos( $rsAgentesNocivos, $stFiltro);
$obLblAgentesNocivos = new Label;
$obLblAgentesNocivos->setRotulo             ( "Classificação dos Agentes Nocivos"   );
$obLblAgentesNocivos->setName               ( "stNumClassificacao"                  );
$obLblAgentesNocivos->setValue              ( $stNumClassificacao ."-".$rsAgentesNocivos->getCampo("descricao")                  );

//INFORMAÇÕES DO CARGO
//Selecão da regime
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php"   );
$obTPessoalRegime = new TPessoalRegime();
$stFiltro = " WHERE cod_regime = ".$inCodRegime;
$obTPessoalRegime->recuperaTodos($rsRegime,$stFiltro);
$obLblCodRegime = new Label;
$obLblCodRegime->setRotulo                  ( "Regime"                              );
$obLblCodRegime->setName                    ( "inCodRegime"                         );
$obLblCodRegime->setValue                   ( $inCodRegime."-".$rsRegime->getCampo("descricao")                          );

//Selecão da Sub-divisao
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"   );
$obTPesssoalSubDivisao = new TPessoalSubDivisao();
$stFiltro = " WHERE cod_sub_divisao = ".$inCodSubDivisao;
$obTPesssoalSubDivisao->recuperaTodos($rsSubDivisao,$stFiltro);
$obLblCodSubDivisao = new Label;
$obLblCodSubDivisao->setRotulo              ( "Subdivisão"                          );
$obLblCodSubDivisao->setName                ( "inCodSubDivisao"                     );
$obLblCodSubDivisao->setValue               ( $inCodSubDivisao."-". $rsSubDivisao->getCampo("descricao")                     );

include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"   );
$obTPesssoalCargo = new TPessoalCargo();
$stFiltro = " WHERE cod_cargo = ".$inCodCargo;
$obTPesssoalCargo->recuperaTodos($rsCargo,$stFiltro);
$obLblCargo = new Label;
$obLblCargo->setRotulo                      ( "Cargo"                               );
$obLblCargo->setName                        ( "inCodCargo"                          );
$obLblCargo->setValue                       ( $inCodCargo   ."-".$rsCargo->getCampo("descricao")                        );

//Selecão da Especialidade Cargo
if ($inCodEspecialidadeCargo != "") {
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"   );
    $obTPesssoalEspecialidade = new TPessoalEspecialidade();
    $stFiltro = " WHERE cod_especialidade = ".$inCodEspecialidadeCargo;
    $obTPesssoalEspecialidade->recuperaTodos($rsEspecialidade,$stFiltro);
}
$obLblCodEspecialidadeCargo = new Label;
$obLblCodEspecialidadeCargo->setRotulo      ( "Especialidade"                          );
$obLblCodEspecialidadeCargo->setName        ( "inCodEspecialidadeCargo"                );
if ($inCodEspecialidadeCargo != "") {
    $obLblCodEspecialidadeCargo->setValue       ( $inCodEspecialidadeCargo   ."-".$rsEspecialidade->getCampo("descricao")              );
}

//FIM INFORMAÇÕES DO CARGO

//INFORMAÇÕES DA FUNÇÃO
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php"   );
$obTPessoalRegime = new TPessoalRegime();
$stFiltro = " WHERE cod_regime = ".$inCodRegimeFuncao;
$obTPessoalRegime->recuperaTodos($rsRegime,$stFiltro);
$obLblCodRegimeFuncao = new Label;
$obLblCodRegimeFuncao->setRotulo             ( "Regime"                             );
$obLblCodRegimeFuncao->setName               ( "inCodRegimeFuncao"                  );
$obLblCodRegimeFuncao->setValue              ( $inCodRegimeFuncao."-".$rsRegime->getCampo("descricao")                   );

include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"   );
$obTPesssoalSubDivisao = new TPessoalSubDivisao();
$stFiltro = " WHERE cod_sub_divisao = ".$inCodSubDivisaoFuncao;
$obTPesssoalSubDivisao->recuperaTodos($rsSubDivisao,$stFiltro);
$obLblCodSubDivisaoFuncao = new Label;
$obLblCodSubDivisaoFuncao->setRotulo        ( "Subdivisão"                          );
$obLblCodSubDivisaoFuncao->setName          ( "inCodSubDivisaoFuncao"               );
$obLblCodSubDivisaoFuncao->setValue         ( $inCodSubDivisaoFuncao  ."-".$rsSubDivisao->getCampo("descricao")              );

//Selecão da funcao
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"   );
$obTPesssoalCargo = new TPessoalCargo();
$stFiltro = " WHERE cod_cargo = ".$inCodFuncao;
$obTPesssoalCargo->recuperaTodos($rsCargo,$stFiltro);
$obLblCodFuncao = new Label;
$obLblCodFuncao->setRotulo                  ( "Função"                              );
$obLblCodFuncao->setName                    ( "inCodFuncao"                         );
$obLblCodFuncao->setValue                   ( $inCodFuncao  ."-".$rsCargo->getCampo("descricao")                        );

//Selecão da Especialidade Funcao
if ($inCodEspecialidadeFuncao != "") {
    include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"   );
    $obTPesssoalEspecialidade = new TPessoalEspecialidade();
    $stFiltro = " WHERE cod_especialidade = ".$inCodEspecialidadeFuncao;
    $obTPesssoalEspecialidade->recuperaTodos($rsEspecialidade,$stFiltro);
}
$obLblCodEspecialidadeFuncao = new Label;
$obLblCodEspecialidadeFuncao->setRotulo     ( "Especialidade"                          );
$obLblCodEspecialidadeFuncao->setName       ( "inCodEspecialidadeFuncao"               );
if ($inCodEspecialidadeFuncao != "") {
    $obLblCodEspecialidadeFuncao->setValue      ( $inCodEspecialidadeFuncao ."-".$rsEspecialidade->getCampo("descricao")               );
}

$obLblAlteracaoFuncao = new Label();
$obLblAlteracaoFuncao->setRotulo           ( "Data da Alteração da Função"         );
$obLblAlteracaoFuncao->setName             ( "dtLblAlteracaoFuncao"               );
$obLblAlteracaoFuncao->setValue            ( $dtDataAlteracaoFuncao                );

//FIM INFORMAÇÕES DA FUNÇÃO

$obROrganogramaOrgao = new ROrganogramaOrgao;
$obROrganogramaOrgao->setCodOrgaoEstruturado($inCodLotacao);
$obROrganogramaOrgao->listarOrgaoReduzido( $rsLotacao);
$obLblLotacao = new Label();
$obLblLotacao->setRotulo                         ( "Lotação"                             );
$obLblLotacao->setValue                          ( $inCodLotacao."-".$rsLotacao->getCampo("descricao")                                 );
$obLblLotacao->setName                           ( "stLotacao"                           );

//Informação de local
if ($inCodLocal != "") {
    $obROrganogramaLocal = new ROrganogramaLocal();
    $obROrganogramaLocal->setCodLocal( $inCodLocal );
    $obROrganogramaLocal->listarLocal( $rsLocal );
}
$obLblLocal = new Label;
$obLblLocal->setRotulo                      ( "Local"                               );
$obLblLocal->setName                          ( "stLocal"                             );
if ($inCodLocal != "") {
    $obLblLocal->setValue           ( $inCodLocal."-".$rsLocal->getCampo("descricao")                           );
}

$obLblDataFGTS = new Label;
$obLblDataFGTS->setName                     ( "dtDataFGTS"                          );
$obLblDataFGTS->setRotulo                   ( "Data de Opção do FGTS"               );
$obLblDataFGTS->setValue                    ( $dtDataFGTS                           );

//Selecão do banco
if ($inCodBancoFGTS != "") {
    include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
    $obTMONBanco = new TMONBanco();
    $stFiltro = " WHERE num_banco = '".$inCodBancoFGTS."'";
    $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);
}
$obLblCodBanco = new Label;
$obLblCodBanco->setRotulo               ( "Banco"                                   );
$obLblCodBanco->setName                 ( "inCodBancoFGTS"                          );
if ($inCodBancoFGTS != "") {
    $obLblCodBanco->setValue                ( $inCodBancoFGTS ."-".$rsBanco->getCampo("nom_banco")                          );
}

//Selecão do agencia bancaria
if ($inCodAgenciaFGTS != "") {
    include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
    $obTMONAgencia = new TMONAgencia();
    $stFiltro = " WHERE num_agencia = '".$inCodAgenciaFGTS."'";
    $obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);
}
$obLblCodAgenciaBanco = new Label;
$obLblCodAgenciaBanco->setRotulo             ( "Agência"     );
$obLblCodAgenciaBanco->setName               ( "inCodAgenciaFGTS" );
if ($inCodAgenciaFGTS != "") {
    $obLblCodAgenciaBanco->setValue              ( $inCodAgenciaFGTS."-".$rsAgencia->getCampo("nom_agencia")  );
}

$obLblContaCredito = new Label;
$obLblContaCredito->setRotulo    ( "Conta para Crédito");
$obLblContaCredito->setName      ( "inContaCreditoFGTS" );
$obLblContaCredito->setValue     ( $inContaCreditoFGTS  );

//seleção de padrao
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php");
$obTFolhaPagamentoPadrao = new TFolhaPagamentoPadrao();
$stFiltro = " WHERE cod_padrao = ".$inCodPadrao;
$obTFolhaPagamentoPadrao->recuperaTodos($rsPadrao,$stFiltro);
$obLblCodPadrao = new Label;
$obLblCodPadrao->setRotulo             ( "Padrão"     );
$obLblCodPadrao->setName               ( "inCodPadrao" );
$obLblCodPadrao->setValue              ( $inCodPadrao."-".$rsPadrao->getCampo("descricao")  );

//Label da progressao
$obLblProgressao = new Label;
$obLblProgressao->setRotulo ( 'Progressão'    );
$obLblProgressao->setName   ( 'stlblProgressao' );
$obLblProgressao->setId     ( 'stlblProgressao' );
$obLblProgressao->setValue  ( $stlblProgressao  );

$obLblHorasMensais = new Label();
$obLblHorasMensais->setRotulo           ( "Horas Mensais"                            );
$obLblHorasMensais->setName             ( "stHorasMensais"                           );
$obLblHorasMensais->setValue            ( $stHorasMensais                            );

$obLblHorasSemanais = new Label;
$obLblHorasSemanais->setRotulo           ( "Horas Semanais"                           );
$obLblHorasSemanais->setName             ( "stHorasSemanais"                          );
$obLblHorasSemanais->setValue            ( $stHorasSemanais                           );

//Valor do salario salarial
$obLblSalario = new Label;
$obLblSalario->setRotulo    ( "Salário");
$obLblSalario->setName      ( "inSalario" );
$obLblSalario->setValue     ( $inSalario  );

//Vigência
$obLblVigenciaSalario = new Label;
$obLblVigenciaSalario->setName               ( "LblVigenciaSalario"            );
$obLblVigenciaSalario->setRotulo             ( "Vigência do Salário"          );
$obLblVigenciaSalario->setValue              ( $dtVigenciaSalario             );

//Selecão do banco
include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
$obTMONBanco = new TMONBanco();
$stFiltro = " WHERE num_banco = '".$inCodBancoSalario."'";
$obTMONBanco->recuperaTodos($rsBanco,$stFiltro);
$obLblCodBancoSalario = new Label();
$obLblCodBancoSalario->setRotulo             ( "Banco"     );
$obLblCodBancoSalario->setName               ( "inCodBancoSalario" );
$obLblCodBancoSalario->setValue              ( $inCodBancoSalario."-".$rsBanco->getCampo("nom_banco")  );

//Selecão do agencia bancaria
include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
$obTMONAgencia = new TMONAgencia();
$stFiltro = " WHERE num_agencia = '".$inCodAgenciaSalario."'";
$obTMONAgencia->recuperaTodos($rsAgencia,$stFiltro);
$obLblCodAgenciaSalario = new Label();
$obLblCodAgenciaSalario->setRotulo             ( "Agência"     );
$obLblCodAgenciaSalario->setName               ( "inCodAgenciaSalario" );
$obLblCodAgenciaSalario->setValue              ( $inCodAgenciaSalario."-".$rsAgencia->getCampo("nom_agencia")  );

//Conta para crédito salarial
$obLblContaSalario = new Label;
$obLblContaSalario->setRotulo    ( "Conta para Crédito");
$obLblContaSalario->setName      ( "inContaSalario" );
$obLblContaSalario->setValue     ( $inContaSalario  );

//Forma de pagamento
switch ($inCodFormaPagamento) {
    case "1":
        $stFormaPagamento = $inCodFormaPagamento."-Em dinheiro";
        break;
    case "2":
        $stFormaPagamento = $inCodFormaPagamento."-Chequeo";
        break;
    case "3":
        $stFormaPagamento = $inCodFormaPagamento."-Crédito em conta";
        break;
    case "4":
        $stFormaPagamento = $inCodFormaPagamento."-Ordem de pagamento";
        break;
}
$obLblCodFormaPagamento = new Label();
$obLblCodFormaPagamento->setRotulo             ( "Forma de Pagamento"     );
$obLblCodFormaPagamento->setName               ( "inCodFormaPagamento" );
$obLblCodFormaPagamento->setValue              ( $stFormaPagamento  );

//Tipo de pagamento
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoPagamento.class.php");
$obTPessoalTipoPagamento = new TPessoalTipoPagamento();
$stFiltro = " WHERE cod_tipo_pagamento = ".$inCodTipoPagamento;
$obTPessoalTipoPagamento->recuperaTodos($rsTipoPagamento,$stFiltro);
$obLblCodTipoPagamento = new Label;
$obLblCodTipoPagamento->setRotulo             ( "Tipo de Pagamento"     );
$obLblCodTipoPagamento->setName               ( "inCodTipoPagamento" );
$obLblCodTipoPagamento->setValue              ( $inCodTipoPagamento ."-".$rsTipoPagamento->getCampo("descricao") );

// Tipo de salario
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoSalario.class.php");
$obTPessoalTipoSalario = new TPessoalTipoSalario();
$stFiltro = " WHERE cod_tipo_Salario = ".$inCodTipoSalario;
$obTPessoalTipoSalario->recuperaTodos($rsTipoSalario,$stFiltro);
$obLblCodTipoSalario = new Label();
$obLblCodTipoSalario->setRotulo             ( "Tipo de Salário"     );
$obLblCodTipoSalario->setName               ( "inCodTipoSalario" );
$obLblCodTipoSalario->setValue              ( $inCodTipoSalario."-".$rsTipoSalario->getCampo("descricao")  );

$obLblAdiantamento = new Label;
$obLblAdiantamento->setRotulo         ( "Adiantamento"   );
$obLblAdiantamento->setName           ( "boAdiantamento"  );
$obLblAdiantamento->setValue          ( ($boAdiantamento == t) ? "Sim" : "Não");

//Define o objeto INNER para buscar sindicato cadastrado
$rsSindicato = new recordset;
if ($inNumCGMSindicato!="") {
    include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
    $obTCGMCGM = new TCGMCGM();
    $stFiltro = " WHERE numcgm = ".$inNumCGMSindicato;
    $obTCGMCGM->recuperaTodos($rsSindicato,$stFiltro);
}
$obLblCgmSindicato = new Label();
$obLblCgmSindicato->setRotulo           ( "CGM do Sindicato"        );
$obLblCgmSindicato->setName               ( "stNomSindicato"          );
$obLblCgmSindicato->setValue( $inNumCGMSindicato  ."-".$rsSindicato->getCampo("nom_cgm")     );

$obLblDataBase = new Label;
$obLblDataBase->setName   ( "dtDataBase" );
$obLblDataBase->setRotulo ( "Data-Base" );
$obLblDataBase->setValue  ( $inDataBase  );

if ($inCodConselho != "") {
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalConselho.class.php");
    $obTPessoalConselho = new TPessoalConselho();
    $stFiltro = " WHERE cod_conselho = ".$inCodConselho;
    $obTPessoalConselho->recuperaTodos($rsConselho,$stFiltro);
}
$obLblConselho = new Label();
$obLblConselho->setRotulo               ( "Conselho"                                );
$obLblConselho->setName                 ( "inCodConselho"                           );
if ($inCodConselho != "") {
    $obLblConselho->setValue                ( $inCodConselho  ."-".  $rsConselho->getCampo("descricao")                        );
}

$obLblNumeroConselho = new Label();
$obLblNumeroConselho->setRotulo         ( "Número Conselho Profissional"            );
$obLblNumeroConselho->setName           ( "inNumeroConselho"                        );
$obLblNumeroConselho->setValue          ( $inNumeroConselho                         );

$obLblDataValidade = new Label();
$obLblDataValidade->setName             ( "dtDataValidadeConselho"                  );
$obLblDataValidade->setValue            ( $dtDataValidadeConselho                   );
$obLblDataValidade->setRotulo           ( "Data de Validade"                        );

//Selecão da grade de horário
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalGradeHorario.class.php");
$obTPessoalGradeHorario = new TPessoalGradeHorario();
$stFiltro = " where cod_grade = ".$inCodGradeHorario;
$obTPessoalGradeHorario->recuperaTodos($rsGrade,$stFiltro);
$obLblGradeHorario = new Label();
$obLblGradeHorario->setRotulo                  ( "Tipo"                                );
$obLblGradeHorario->setName                    ( "inCodGradeHorario"                   );
$obLblGradeHorario->setValue                   ( $inCodGradeHorario  ."-".$rsGrade->getCampo("descricao")                  );

$obSpnTurnos = new Span;
$obSpnTurnos->setId ('spnTurnos' );

$obLblAdmissao = new Label();
$obLblAdmissao->setName               ( "dtAdmissao"           );
$obLblAdmissao->setRotulo             ( "Data de Admissão"         );
$obLblAdmissao->setValue              ( $dtAdmissao            );

$obSpnRescisao = new Span();
$obSpnRescisao->setId("spnRescisao");
