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
  * Página de Formulario de Configuração de IDE
  * Data de Criação: 20/02/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  * $Id: FMManterConfiguracaoArquivoDCLRF.php 64886 2016-04-11 17:22:14Z evandro $
  *   
  * $Rev: 64886 $
  * $Author: evandro $
  * $Date: 2016-04-11 14:22:14 -0300 (Mon, 11 Apr 2016) $
*/
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoArquivoDCLRF.class.php");
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoArquivoDCLRF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$rsTTCEMGConfiguracaoArquivoDCLRF = new RecordSet();
$obTTCEMGConfiguracaoArquivoDCLRF = new TTCEMGConfiguracaoArquivoDCLRF();
$obTTCEMGConfiguracaoArquivoDCLRF->setDado('exercicio',$request->get('inExercicio'));
$obTTCEMGConfiguracaoArquivoDCLRF->setDado('mes_referencia',$request->get('inMes'));
$obTTCEMGConfiguracaoArquivoDCLRF->recuperaValoresArquivoDCLRF($rsTTCEMGConfiguracaoArquivoDCLRF);

if($rsTTCEMGConfiguracaoArquivoDCLRF->getNumLinhas() > 0) {
    $vlSaldoAtualConcessoesGarantia               = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_concessoes_garantia'), '2', ',', '.');
    $vlSaldoAtualConcessoesGarantiaInterna        = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_concessoes_garantia_interna'), '2', ',', '.');
    $vlSaldoAtualConcessoesGarantiaExterna        = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_concessoes_garantia'), '2', ',', '.');
    $vlSaldoAtualContraConcessoesGarantiaInterna  = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_contra_concessoes_garantia_interna'), '2', ',', '.');
    $vlSaldoAtualContraConcessoesGarantiaExterna  = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_saldo_atual_contra_concessoes_garantia_externa'), '2', ',', '.');
    $stMedidasCorretivas                          = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('medidas_corretivas');
    $vlReceitaPrivatizacao                        = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('receita_privatizacao'), '2', ',', '.');
    $vlLiquidadoIncentivoContribuinte             = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_liquidado_incentivo_contribuinte'), '2', ',', '.');
    $vlLiquidadoIncentivoInstituicaoFinanceiro    = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_liquidado_incentivo_instituicao_financeira'), '2', ',', '.');
    $vlInscritoRPNPIncentivoContribuinte          = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_inscrito_rpnp_incentivo_contribuinte'), '2', ',', '.');
    $vlInscritoRPNPIncentivoInstituicaoFinanceiro = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_inscrito_rpnp_incentivo_instituicao_financeira'), '2', ',', '.');
    $vlCompromissado                              = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_compromissado'), '2', ',', '.');
    $vlRecursosNaoAplicados                       = number_format($rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('valor_recursos_nao_aplicados'), '2', ',', '.');
    $inPublicacaoRelatorioLRF                     = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('publicacao_relatorio_lrf');
    $dtPublicacaoRelatorioLRF                     = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('dt_publicacao_relatorio_lrf');
    $inBimestre                                   = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('bimestre');
    $inMetaBimestral                              = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('meta_bimestral');
    $stMedidasAdotadas                            = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('medida_adotada');
    $inContOpCredito                              = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('cont_op_credito');
    $stDescricaoOPCredito                         = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('desc_cont_op_credito');
    $inRealizOpCredito                            = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('realiz_op_credito');
    $inTipoRealizOpCreditoCapta                   = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_capta');
    $inTipoRealizOpCreditoReceb                   = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_receb');
    $inTipoRealizOpCreditoAssunDir                = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_assun_dir');
    $inTipoRealizOpCreditoAssunObg                = $rsTTCEMGConfiguracaoArquivoDCLRF->getCampo('tipo_realiz_op_credito_assun_obg');

} else {
    $vlSaldoAtualConcessoesGarantia = "0,00";
    $vlSaldoAtualConcessoesGarantiaExterna = "0,00";
    $vlSaldoAtualConcessoesGarantiaInterna = "0,00";
    $vlSaldoAtualContraConcessoesGarantiaExterna = "0,00";
    $vlSaldoAtualContraConcessoesGarantiaInterna = "0,00"; 
    $vlReceitaPrivatizacao = "0,00";
    $vlLiquidadoIncentivoContribuinte = "0,00";
    $vlLiquidadoIncentivoInstituicaoFinanceiro = "0,00";
    $vlInscritoRPNPIncentivoContribuinte = "0,00";
    $vlInscritoRPNPIncentivoInstituicaoFinanceiro = "0,00";
    $vlCompromissado = "0,00";
    $vlRecursosNaoAplicados = "0,00";

}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnAno = new Hidden();
$obHdnAno->setId   ('stExercicio');
$obHdnAno->setName ('stExercicio');
$obHdnAno->setValue($request->get('inExercicio'));

$obHdnMes = new Hidden();
$obHdnMes->setId   ('stMes');
$obHdnMes->setName ('stMes');
$obHdnMes->setValue($request->get('inMes'));

$obFlValorSaldoAtualConcessoesGarantia = new Numerico();
$obFlValorSaldoAtualConcessoesGarantia->setId('flValorSaldoAtualConcessoesGarantia');
$obFlValorSaldoAtualConcessoesGarantia->setName('flValorSaldoAtualConcessoesGarantia');
$obFlValorSaldoAtualConcessoesGarantia->setRotulo('Saldo atual das concessões de garantia');
$obFlValorSaldoAtualConcessoesGarantia->setTitle('Saldo atual das concessões de garantia decorrentes do compromisso de adimplência de obrigação financeira ou contratual assumida por Ente da Federação ou entidade a ele vinculada (art. 29, IV, da Lei de Responsabilidade Fiscal).');
$obFlValorSaldoAtualConcessoesGarantia->setDecimais(2);
$obFlValorSaldoAtualConcessoesGarantia->setMaxLength(15);
$obFlValorSaldoAtualConcessoesGarantia->setSize(17);
$obFlValorSaldoAtualConcessoesGarantia->setValue($vlSaldoAtualConcessoesGarantia);

$obFlValorSaldoAtualConcessoesGarantiaInterna = new Numerico();
$obFlValorSaldoAtualConcessoesGarantiaInterna->setId('flValorSaldoAtualConcessoesGarantiaInterna');
$obFlValorSaldoAtualConcessoesGarantiaInterna->setName('flValorSaldoAtualConcessoesGarantiaInterna');
$obFlValorSaldoAtualConcessoesGarantiaInterna->setRotulo('Saldo atual das concessões de garantia Interna');
$obFlValorSaldoAtualConcessoesGarantiaInterna->setTitle('Saldo atual das concessões de garantia INTERNAS decorrentes do compromisso de adimplência de obrigação financeira ou contratual assumida por Ente da Federação ou entidade a ele vinculada (art. 29, IV, da Lei de Responsabilidade Fiscal).');
$obFlValorSaldoAtualConcessoesGarantiaInterna->setDecimais(2);
$obFlValorSaldoAtualConcessoesGarantiaInterna->setMaxLength(15);
$obFlValorSaldoAtualConcessoesGarantiaInterna->setSize(17);
$obFlValorSaldoAtualConcessoesGarantiaInterna->setValue($vlSaldoAtualConcessoesGarantiaInterna);

$obFlValorSaldoAtualConcessoesGarantiaExterna = new Numerico();
$obFlValorSaldoAtualConcessoesGarantiaExterna->setId('flValorSaldoAtualConcessoesGarantiaExterna');
$obFlValorSaldoAtualConcessoesGarantiaExterna->setName('flValorSaldoAtualConcessoesGarantiaExterna');
$obFlValorSaldoAtualConcessoesGarantiaExterna->setRotulo('Saldo atual das concessões de garantia Externa');
$obFlValorSaldoAtualConcessoesGarantiaExterna->setTitle('Saldo atual das concessões de garantia EXTERNAS decorrentes do compromisso de adimplência de obrigação financeira ou contratual assumida por Ente da Federação ou entidade a ele vinculada (art. 29, IV, da Lei de Responsabilidade Fiscal).');
$obFlValorSaldoAtualConcessoesGarantiaExterna->setDecimais(2);
$obFlValorSaldoAtualConcessoesGarantiaExterna->setMaxLength(15);
$obFlValorSaldoAtualConcessoesGarantiaExterna->setSize(17);
$obFlValorSaldoAtualConcessoesGarantiaExterna->setValue($vlSaldoAtualConcessoesGarantiaExterna);

$obFlValorSaldoAtualContraConcessoesGarantiaInterna = new Numerico();
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setId('flValorSaldoAtualContraConcessoesGarantiaInterna');
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setName('flValorSaldoAtualContraConcessoesGarantiaInterna');
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setRotulo('Saldo atual das contra garantias Interna Recebidas');
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setTitle('Saldo atual das contra garantias INTERNAS recebidas em virtude da concessão de garantias às operações internas, tendo por finalidade salvaguardar o ente dos riscos decorrentes da concessão de garantias, nos termos da lei.');
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setDecimais(2);
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setMaxLength(15);
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setSize(17);
$obFlValorSaldoAtualContraConcessoesGarantiaInterna->setValue($vlSaldoAtualContraConcessoesGarantiaInterna);

$obFlValorSaldoAtualContraConcessoesGarantiaExterna = new Numerico();
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setId('flValorSaldoAtualContraConcessoesGarantiaExterna');
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setName('flValorSaldoAtualContraConcessoesGarantiaExterna');
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setRotulo('Saldo atual das contra garantias Externa Recebidas');
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setTitle('Saldo atual das contra garantias EXTERNAS recebidas em virtude da concessão de garantias às operações externas, tendo por finalidade salvaguardar o ente dos riscos decorrentes da concessão de garantias, nos termos da lei.');
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setDecimais(2);
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setMaxLength(15);
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setSize(17);
$obFlValorSaldoAtualContraConcessoesGarantiaExterna->setValue($vlSaldoAtualContraConcessoesGarantiaExterna);

$obTxtMedidasCorretivas = new TextArea();
$obTxtMedidasCorretivas->setName('stMedidasCorretivas');
$obTxtMedidasCorretivas->setId('stMedidasCorretivas');
$obTxtMedidasCorretivas->setRotulo('Deverá ser indicado as medidas corretivas adotadas ou a adotar caso o ente ultrapasse o limite de concessão de Garantia, definido no art. 9º da Resolução n.º 43/2002 do Senado Federal.');
$obTxtMedidasCorretivas->setMaxCaracteres(4000);
$obTxtMedidasCorretivas->setRows(3);
$obTxtMedidasCorretivas->setValue($stMedidasCorretivas);
$obTxtMedidasCorretivas->setNull(true);

$obFlValorReceitaPrivatizacao = new Numerico();
$obFlValorReceitaPrivatizacao->setId('flValorReceitaPrivatizacao');
$obFlValorReceitaPrivatizacao->setName('flValorReceitaPrivatizacao');
$obFlValorReceitaPrivatizacao->setRotulo('Receita de Privatização');
$obFlValorReceitaPrivatizacao->setTitle('Valores correspondente a Receita de Privatização.<br/>
 Registrar o valor arrecadado da Receita de Privatizações, subtraído das despesas de vendas (impostos de renda sobre a operação, comissão de venda e gastos com avaliação e reestruturação da empresa) e acrescido das dívidas transferidas identificadas no sistema financeiro.');
$obFlValorReceitaPrivatizacao->setDecimais(2);
$obFlValorReceitaPrivatizacao->setMaxLength(15);
$obFlValorReceitaPrivatizacao->setSize(17);
$obFlValorReceitaPrivatizacao->setValue($vlReceitaPrivatizacao);

$obFlValorLiquidadoIncentivoContribuinte = new Numerico();
$obFlValorLiquidadoIncentivoContribuinte->setId('flValorLiquidadoIncentivoContribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setName('flValorLiquidadoIncentivoContribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setRotulo('Valor Liquidado de Incentivo a Contribuinte');
$obFlValorLiquidadoIncentivoContribuinte->setTitle('Registrar as despesas de capital liquidadas sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, tendo por base tributo de competência do ente da Federação, se resultar na diminuição, direta ou indireta, do ônus do ente (art. 32, §3o, inciso I da LRF).');
$obFlValorLiquidadoIncentivoContribuinte->setDecimais(2);
$obFlValorLiquidadoIncentivoContribuinte->setMaxLength(15);
$obFlValorLiquidadoIncentivoContribuinte->setSize(17);
$obFlValorLiquidadoIncentivoContribuinte->setValue($vlLiquidadoIncentivoContribuinte);

$obFlValorLiquidadoIncentivoInstituicaoFinanceiro = new Numerico();
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setId('flValorLiquidadoIncentivoInstituicaoFinanceiro');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setName('flValorLiquidadoIncentivoInstituicaoFinanceiro');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setRotulo('Valor Liquidado de Incentivo concedido por Instituição Financeira');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setTitle('Registrar as despesas de capital liquidadas sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, concedido por instituição financeira controlada pelo ente da Federação(art. 32, § 3o, inciso II da LRF).');
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setDecimais(2);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setMaxLength(15);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setSize(17);
$obFlValorLiquidadoIncentivoInstituicaoFinanceiro->setValue($vlLiquidadoIncentivoInstituicaoFinanceiro);

$obFlValorInscritoRPNPIncentivoContribuinte = new Numerico();
$obFlValorInscritoRPNPIncentivoContribuinte->setId('flValorInscritoRPNPIncentivoContribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setName('flValorInscritoRPNPIncentivoContribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setRotulo('Valor Inscrito em Restos a Pagar Não Processados de Incentivo a Contribuinte');
$obFlValorInscritoRPNPIncentivoContribuinte->setTitle('Registrar as despesas de capital inscritas em Restos a Pagar Não Processados sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, tendo por base tributo de competência do ente da Federação, se resultar na diminuição, direta ou indireta, do ônus do ente (art. 32, § 3o, inciso I da LRF).');
$obFlValorInscritoRPNPIncentivoContribuinte->setDecimais(2);
$obFlValorInscritoRPNPIncentivoContribuinte->setMaxLength(15);
$obFlValorInscritoRPNPIncentivoContribuinte->setSize(17);
$obFlValorInscritoRPNPIncentivoContribuinte->setValue($vlInscritoRPNPIncentivoContribuinte);

$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro = new Numerico();
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setId('flValorInscritoRPNPIncentivoInstituicaoFinanceiro');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setName('flValorInscritoRPNPIncentivoInstituicaoFinanceiro');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setRotulo('Valor Inscrito em Restos a Pagar Não Processados de Incentivo concedido por Instituição Financeira');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setTitle('Registrar as despesas de capital inscritas em Restos a Pagar Não Processados sob a forma de empréstimo ou financiamento a contribuinte, com o intuito de promover incentivo fiscal, concedido por instituição financeira controlada pelo ente da Federação (art. 32, § 3o, inciso II da LRF).');
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setDecimais(2);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setMaxLength(15);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setSize(17);
$obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro->setValue($vlInscritoRPNPIncentivoInstituicaoFinanceiro);

$obFlValorCompromissado = new Numerico();
$obFlValorCompromissado->setId('flValorCompromissado');
$obFlValorCompromissado->setName('flValorCompromissado');
$obFlValorCompromissado->setRotulo('Total dos valores compromissados (Passivo Financeiro)');
$obFlValorCompromissado->setTitle('Total dos valores compromissados (Passivo Financeiro).<br/>
OBS: Incluem-se o saldo atual negativo apurado da conta devedores diversos do ativo financeiro.');
$obFlValorCompromissado->setDecimais(2);
$obFlValorCompromissado->setMaxLength(15);
$obFlValorCompromissado->setSize(17);
$obFlValorCompromissado->setValue($vlCompromissado);

$obFlValorRecursosNaoAplicados = new Numerico();
$obFlValorRecursosNaoAplicados->setId('flValorRecursosNaoAplicados');
$obFlValorRecursosNaoAplicados->setName('flValorRecursosNaoAplicados');
$obFlValorRecursosNaoAplicados->setRotulo('Recursos do FUNDEB não aplicados no exercício anterior (§2° do art. 21, lei 11.494/2007)');
$obFlValorRecursosNaoAplicados->setTitle('Recursos do FUNDEB não aplicados no exercício anterior (§2° do art. 21, lei 11.494/2007).<br/>
OBS: Deve ser informado somente no mês de janeiro e apenas pelo órgão "02 - Prefeitura Municipal".');
$obFlValorRecursosNaoAplicados->setDecimais(2);
$obFlValorRecursosNaoAplicados->setMaxLength(15);
$obFlValorRecursosNaoAplicados->setSize(17);
$obFlValorRecursosNaoAplicados->setValue($vlRecursosNaoAplicados);


$obRdPublicacaoRelatorioLRF_S = new Radio;
$obRdPublicacaoRelatorioLRF_S->setId('inPublicacaoRelatorioLRF');
$obRdPublicacaoRelatorioLRF_S->setName('inPublicacaoRelatorioLRF');
$obRdPublicacaoRelatorioLRF_S->setRotulo('O poder deu publicidade aos relatórios (RGF e/ou RREO) da LRF?');
$obRdPublicacaoRelatorioLRF_S->setLabel("Sim");
$obRdPublicacaoRelatorioLRF_S->setValue(1);
$obRdPublicacaoRelatorioLRF_S->setChecked($inPublicacaoRelatorioLRF==1);
$obRdPublicacaoRelatorioLRF_S->setNull(true);

$obRdPublicacaoRelatorioLRF_N = new Radio;
$obRdPublicacaoRelatorioLRF_N->setId('inPublicacaoRelatorioLRF');
$obRdPublicacaoRelatorioLRF_N->setName('inPublicacaoRelatorioLRF');
$obRdPublicacaoRelatorioLRF_N->setRotulo('O poder deu publicidade aos relatórios (RGF e/ou RREO) da LRF?');
$obRdPublicacaoRelatorioLRF_N->setLabel("Não");
$obRdPublicacaoRelatorioLRF_N->setValue(0);
$obRdPublicacaoRelatorioLRF_N->setChecked($inPublicacaoRelatorioLRF==0 or !$inPublicacaoRelatorioLRF);
$obRdPublicacaoRelatorioLRF_N->setNull(true);

$obDtPublicacaoRelatorioLRF = new Data;
$obDtPublicacaoRelatorioLRF->setId("dtPublicacaoRelatorioLRF");
$obDtPublicacaoRelatorioLRF->setName("dtPublicacaoRelatorioLRF");
$obDtPublicacaoRelatorioLRF->setRotulo("Data de publicação dos relatórios da LRF");
$obDtPublicacaoRelatorioLRF->setValue($dtPublicacaoRelatorioLRF);
$obDtPublicacaoRelatorioLRF->setNull(true);

$obCmbBimestre = new Select;
$obCmbBimestre->setId( "inBimestre" );
$obCmbBimestre->setName( "inBimestre" );
$obCmbBimestre->setRotulo( "Periodo a que se refere a data de publicação da LRF" );
$obCmbBimestre->addOption( "", "Selecione" );
$obCmbBimestre->addOption( "1", "1º Bimestre" );
$obCmbBimestre->addOption( "2", "2º Bimestre" );
$obCmbBimestre->addOption( "3", "3º Bimestre" );
$obCmbBimestre->addOption( "4", "4º Bimestre" );
$obCmbBimestre->addOption( "5", "5º Bimestre" );
$obCmbBimestre->addOption( "6", "6º Bimestre" );
$obCmbBimestre->setNull( true );
$obCmbBimestre->setValue( $inBimestre);
$obCmbBimestre->setStyle( "width: 220px" );

$obRdMetaBimestral_S = new Radio;
$obRdMetaBimestral_S->setId('inMetaBimestral');
$obRdMetaBimestral_S->setName('inMetaBimestral');
$obRdMetaBimestral_S->setRotulo('A meta bimestral de arrecadação informada no arquivo MTBIARREC do Instrumento de Planejamento foi cumprida em relação à receita arrecadada no bimestre em questão?');
$obRdMetaBimestral_S->setLabel("Sim");
$obRdMetaBimestral_S->setValue(1);
$obRdMetaBimestral_S->setChecked($inMetaBimestral==1);
$obRdMetaBimestral_S->setNull(true);
                  
$obRdMetaBimestral_N = new Radio;
$obRdMetaBimestral_N->setId('inMetaBimestral');
$obRdMetaBimestral_N->setName('inMetaBimestral');
$obRdMetaBimestral_N->setRotulo('A meta bimestral de arrecadação informada no arquivo MTBIARREC do Instrumento de Planejamento foi cumprida em relação à receita arrecadada no bimestre em questão?');
$obRdMetaBimestral_N->setLabel("Não");
$obRdMetaBimestral_N->setValue(2);
$obRdMetaBimestral_N->setChecked($inMetaBimestral==2 or !$inMetaBimestral);
$obRdMetaBimestral_N->setNull(true);

$obTxtMedidasAdotadas = new TextArea();
$obTxtMedidasAdotadas->setName('stMedidasAdotadas');
$obTxtMedidasAdotadas->setId('stMedidasAdotadas');
$obTxtMedidasAdotadas->setRotulo('Deverá ser indicado as medidas adotadas para a recuperação dos créditos e as ações de combate à evasão e sonegação das receitas.');
$obTxtMedidasAdotadas->setMaxCaracteres(4000);
$obTxtMedidasAdotadas->setRows(3);
$obTxtMedidasAdotadas->setValue($stMedidasAdotadas);
$obTxtMedidasAdotadas->setNull(true);

if($_REQUEST['inMes'] == 12) {
    $obRdContOpCredito_S = new Radio;
    $obRdContOpCredito_S->setId('inContOpCredito');
    $obRdContOpCredito_S->setName('inContOpCredito');
    $obRdContOpCredito_S->setRotulo('Houve contratação de operação de crédito junto a instituição financeira que não atendeu às condições e limites estabelecidos pelo artigo 33 da Lei Complementar n° 101/2000?');
    $obRdContOpCredito_S->setLabel("Sim");
    $obRdContOpCredito_S->setValue(1);
    $obRdContOpCredito_S->setChecked($inContOpCredito==1);
    $obRdContOpCredito_S->setNull(false);
                      
    $obRdContOpCredito_N = new Radio;
    $obRdContOpCredito_N->setId('inContOpCredito');
    $obRdContOpCredito_N->setName('inContOpCredito');
    $obRdContOpCredito_N->setRotulo('Houve contratação de operação de crédito junto a instituição financeira que não atendeu às condições e limites estabelecidos pelo artigo 33 da Lei Complementar n° 101/2000?');
    $obRdContOpCredito_N->setLabel("Não");
    $obRdContOpCredito_N->setValue(2);
    $obRdContOpCredito_N->setChecked($inContOpCredito==2 or !$inContOpCredito);
    $obRdContOpCredito_N->setNull(false);
    
    $obTxtDescContOpCredito = new TextArea();
    $obTxtDescContOpCredito->setName('stDescContOpCredito');
    $obTxtDescContOpCredito->setId('stDescContOpCredito');
    $obTxtDescContOpCredito->setRotulo('Informe a ocorrência de cancelamento, amortização ou constituição de reserva, de acordo com o artigo 33 da Lei Complementar n°101/2000.');
    $obTxtDescContOpCredito->setMaxCaracteres(1000);
    $obTxtDescContOpCredito->setRows(3);
    $obTxtDescContOpCredito->setValue($stDescricaoOPCredito);
    $obTxtDescContOpCredito->setNull(true);
    
    $obRdRealizOpCredito_S = new Radio;
    $obRdRealizOpCredito_S->setId('inRealizOpCredito');
    $obRdRealizOpCredito_S->setName('inRealizOpCredito');
    $obRdRealizOpCredito_S->setRotulo('Foram realizadas operações de crédito vedadas pelo artigo 37 da Lei Complementar n° 101/2000?');
    $obRdRealizOpCredito_S->setLabel("Sim");
    $obRdRealizOpCredito_S->setValue(1);
    $obRdRealizOpCredito_S->setChecked($inRealizOpCredito==1);
    $obRdRealizOpCredito_S->setNull(false);
                      
    $obRdRealizOpCredito_N = new Radio;
    $obRdRealizOpCredito_N->setId('inRealizOpCredito');
    $obRdRealizOpCredito_N->setName('inRealizOpCredito');
    $obRdRealizOpCredito_N->setRotulo('Foram realizadas operações de crédito vedadas pelo artigo 37 da Lei Complementar n° 101/2000?');
    $obRdRealizOpCredito_N->setLabel("Não");
    $obRdRealizOpCredito_N->setValue(2);
    $obRdRealizOpCredito_N->setChecked($inRealizOpCredito==2 or !$inRealizOpCredito);
    $obRdRealizOpCredito_N->setNull(false);
    
    $obRdTipoRealizOpCreditoCapta_S = new Radio;
    $obRdTipoRealizOpCreditoCapta_S->setId('inTipoRealizOpCreditoCapta');
    $obRdTipoRealizOpCreditoCapta_S->setName('inTipoRealizOpCreditoCapta');
    $obRdTipoRealizOpCreditoCapta_S->setRotulo('Houve captação de recursos a título de antecipação de receita de tributo ou contribuição cujo fato gerador ainda não tenha ocorrido, sem prejuízo do disposto no § 7° do artigo 150 da Constituição?');
    $obRdTipoRealizOpCreditoCapta_S->setLabel("Sim");
    $obRdTipoRealizOpCreditoCapta_S->setValue(1);
    $obRdTipoRealizOpCreditoCapta_S->setChecked($inTipoRealizOpCreditoCapta==1);
    $obRdTipoRealizOpCreditoCapta_S->setNull(true);
                      
    $obRdTipoRealizOpCreditoCapta_N = new Radio;
    $obRdTipoRealizOpCreditoCapta_N->setId('inTipoRealizOpCreditoCapta');
    $obRdTipoRealizOpCreditoCapta_N->setName('inTipoRealizOpCreditoCapta');
    $obRdTipoRealizOpCreditoCapta_N->setRotulo('Houve captação de recursos a título de antecipação de receita de tributo ou contribuição cujo fato gerador ainda não tenha ocorrido, sem prejuízo do disposto no § 7° do artigo 150 da Constituição?');
    $obRdTipoRealizOpCreditoCapta_N->setLabel("Não");
    $obRdTipoRealizOpCreditoCapta_N->setValue(2);
    $obRdTipoRealizOpCreditoCapta_N->setChecked($inTipoRealizOpCreditoCapta==2 or !$inTipoRealizOpCreditoCapta);
    $obRdTipoRealizOpCreditoCapta_N->setNull(true);
    
    $obRdTipoRealizOpCreditoReceb_S = new Radio;
    $obRdTipoRealizOpCreditoReceb_S->setId('inTipoRealizOpCreditoReceb');
    $obRdTipoRealizOpCreditoReceb_S->setName('inTipoRealizOpCreditoReceb');
    $obRdTipoRealizOpCreditoReceb_S->setRotulo('Houve recebimento antecipado de valores de empresa em que o Poder Público detenha, direta ou indiretamente, a maioria do capital social com direito à voto, salvo lucros e dividendos, na forma da legislação?');
    $obRdTipoRealizOpCreditoReceb_S->setLabel("Sim");
    $obRdTipoRealizOpCreditoReceb_S->setValue(1);
    $obRdTipoRealizOpCreditoReceb_S->setChecked($inTipoRealizOpCreditoReceb==1);
    $obRdTipoRealizOpCreditoReceb_S->setNull(true);
                      
    $obRdTipoRealizOpCreditoReceb_N = new Radio;
    $obRdTipoRealizOpCreditoReceb_N->setId('inTipoRealizOpCreditoReceb');
    $obRdTipoRealizOpCreditoReceb_N->setName('inTipoRealizOpCreditoReceb');
    $obRdTipoRealizOpCreditoReceb_N->setRotulo('Houve recebimento antecipado de valores de empresa em que o Poder Público detenha, direta ou indiretamente, a maioria do capital social com direito à voto, salvo lucros e dividendos, na forma da legislação?');
    $obRdTipoRealizOpCreditoReceb_N->setLabel("Não");
    $obRdTipoRealizOpCreditoReceb_N->setValue(2);
    $obRdTipoRealizOpCreditoReceb_N->setChecked($inTipoRealizOpCreditoReceb==2 or !$inTipoRealizOpCreditoReceb);
    $obRdTipoRealizOpCreditoReceb_N->setNull(true);
    
    $obRdTipoRealizOpCreditoAssunDir_S = new Radio;
    $obRdTipoRealizOpCreditoAssunDir_S->setId('inTipoRealizOpCreditoAssunDir');
    $obRdTipoRealizOpCreditoAssunDir_S->setName('inTipoRealizOpCreditoAssunDir');
    $obRdTipoRealizOpCreditoAssunDir_S->setRotulo('Houve assunção direta de compromisso, confissão de dívida ou operação assemelhada, com fornecedor de bens, mercadorias ou serviços, mediante emissão, aceite ou aval de título de crédito, não se aplicando a
empresas estatais dependentes?');
    $obRdTipoRealizOpCreditoAssunDir_S->setLabel("Sim");
    $obRdTipoRealizOpCreditoAssunDir_S->setValue(1);
    $obRdTipoRealizOpCreditoAssunDir_S->setChecked($inTipoRealizOpCreditoAssunDir==1);
    $obRdTipoRealizOpCreditoAssunDir_S->setNull(true);
                      
    $obRdTipoRealizOpCreditoAssunDir_N = new Radio;
    $obRdTipoRealizOpCreditoAssunDir_N->setId('inTipoRealizOpCreditoAssunDir');
    $obRdTipoRealizOpCreditoAssunDir_N->setName('inTipoRealizOpCreditoAssunDir');
    $obRdTipoRealizOpCreditoAssunDir_N->setRotulo('Houve assunção direta de compromisso, confissão de dívida ou operação assemelhada, com fornecedor de bens, mercadorias ou serviços, mediante emissão, aceite ou aval de título de crédito, não se aplicando a
empresas estatais dependentes?');
    $obRdTipoRealizOpCreditoAssunDir_N->setLabel("Não");
    $obRdTipoRealizOpCreditoAssunDir_N->setValue(2);
    $obRdTipoRealizOpCreditoAssunDir_N->setChecked($inTipoRealizOpCreditoAssunDir==2 or !$inTipoRealizOpCreditoAssunDir);
    $obRdTipoRealizOpCreditoAssunDir_N->setNull(true);
    
    $obRdTipoRealizOpCreditoAssunObg_S = new Radio;
    $obRdTipoRealizOpCreditoAssunObg_S->setId('inTipoRealizOpCreditoAssunObg');
    $obRdTipoRealizOpCreditoAssunObg_S->setName('inTipoRealizOpCreditoAssunObg');
    $obRdTipoRealizOpCreditoAssunObg_S->setRotulo('Houve assunção de obrigação, sem autorização orçamentária, com fornecedores para pagamento a posteriori de bens e serviços?');
    $obRdTipoRealizOpCreditoAssunObg_S->setLabel("Sim");
    $obRdTipoRealizOpCreditoAssunObg_S->setValue(1);
    $obRdTipoRealizOpCreditoAssunObg_S->setChecked($inTipoRealizOpCreditoAssunObg==1);
    $obRdTipoRealizOpCreditoAssunObg_S->setNull(true);
                      
    $obRdTipoRealizOpCreditoAssunObg_N = new Radio;
    $obRdTipoRealizOpCreditoAssunObg_N->setId('inTipoRealizOpCreditoAssunObg');
    $obRdTipoRealizOpCreditoAssunObg_N->setName('inTipoRealizOpCreditoAssunObg');
    $obRdTipoRealizOpCreditoAssunObg_N->setRotulo('Houve assunção de obrigação, sem autorização orçamentária, com fornecedores para pagamento a posteriori de bens e serviços?');
    $obRdTipoRealizOpCreditoAssunObg_N->setLabel("Não");
    $obRdTipoRealizOpCreditoAssunObg_N->setValue(2);
    $obRdTipoRealizOpCreditoAssunObg_N->setChecked($inTipoRealizOpCreditoAssunObg==2 or !$inTipoRealizOpCreditoAssunObg);
    $obRdTipoRealizOpCreditoAssunObg_N->setNull(true);
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnAno );
$obFormulario->addHidden       ( $obHdnMes );
$obFormulario->setLarguraRotulo( 40 );
$obFormulario->setLarguraCampo ( 60 );
$obFormulario->addTitulo       ( "Configuração do Arquivo Dados Complementares à LRF" );
if(Sessao::getExercicio() >= 2016) {
    $obFormulario->addComponente     ( $obFlValorSaldoAtualConcessoesGarantiaInterna );
    $obFormulario->addComponente     ( $obFlValorSaldoAtualConcessoesGarantiaExterna );
    $obFormulario->addComponente     ( $obFlValorSaldoAtualContraConcessoesGarantiaInterna );
    $obFormulario->addComponente     ( $obFlValorSaldoAtualContraConcessoesGarantiaExterna );    
    $obFormulario->addComponente     ( $obTxtMedidasCorretivas );
}else{
    $obFormulariomulario->addComponente   ( $obFlValorSaldoAtualConcessoesGarantia );
}

$obFormulario->addComponente   ( $obFlValorReceitaPrivatizacao );
$obFormulario->addComponente   ( $obFlValorLiquidadoIncentivoContribuinte );
$obFormulario->addComponente   ( $obFlValorLiquidadoIncentivoInstituicaoFinanceiro );
$obFormulario->addComponente   ( $obFlValorInscritoRPNPIncentivoContribuinte );
$obFormulario->addComponente   ( $obFlValorInscritoRPNPIncentivoInstituicaoFinanceiro );
$obFormulario->addComponente   ( $obFlValorCompromissado );
$obFormulario->addComponente   ( $obFlValorRecursosNaoAplicados );

if(Sessao::getExercicio() >= 2016) {
    $obFormulario->agrupaComponentes ( array($obRdPublicacaoRelatorioLRF_S, $obRdPublicacaoRelatorioLRF_N ));
    $obFormulario->addComponente     ( $obDtPublicacaoRelatorioLRF );
    $obFormulario->addComponente     ( $obCmbBimestre );
    $obFormulario->agrupaComponentes ( array($obRdMetaBimestral_S, $obRdMetaBimestral_N ));    
    $obFormulario->addComponente     ( $obTxtMedidasAdotadas );

    if($_REQUEST['inMes'] == 12) {
        $obFormulario->addTitulo    ( ' Informações Sobre Operações de Crédito' );
        $obFormulario->agrupaComponentes ( array($obRdContOpCredito_S, $obRdContOpCredito_N ));
        $obFormulario->addComponente ( $obTxtDescContOpCredito );
        $obFormulario->agrupaComponentes ( array($obRdRealizOpCredito_S, $obRdRealizOpCredito_N ));
        $obFormulario->agrupaComponentes ( array($obRdTipoRealizOpCreditoCapta_S, $obRdTipoRealizOpCreditoCapta_N ));
        $obFormulario->agrupaComponentes ( array($obRdTipoRealizOpCreditoReceb_S, $obRdTipoRealizOpCreditoReceb_N ));
        $obFormulario->agrupaComponentes ( array($obRdTipoRealizOpCreditoAssunDir_S, $obRdTipoRealizOpCreditoAssunDir_N ));
        $obFormulario->agrupaComponentes ( array($obRdTipoRealizOpCreditoAssunObg_S, $obRdTipoRealizOpCreditoAssunObg_N ));
    }
}
$obFormulario->OK();
$obFormulario->show();

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php");
?>