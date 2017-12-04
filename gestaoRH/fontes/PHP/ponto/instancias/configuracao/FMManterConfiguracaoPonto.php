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
/*
 * Fomrulário para configuração do ponto
 * Data de Criação   : 13/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ManterConfiguracaoPonto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::write("stAcao",$stAcao);

//VALORES DAFAULT
$boLimitarAtrasos           = "NÃO";
$boSNAdicionalNoturno       = "NÃO";
$boSNAbonoHorasTrabalhadas  = "NÃO";
$boSNDescontosFalta         = "NÃO";
$boSNFeriadoExtra           = "NÃO";
$boSNHorasExtrasNoturnas    = "NÃO";
$boSNArredondarTempo        = "NÃO";
$boSNAntes1Periodo          = "NÃO";
$boSNEntre1e2Periodo        = "NÃO";
$boSNDepois2Periodo         = "NÃO";
$boSNSomenteAutorizacao     = "NÃO";
$boSNAtrasos                = "NÃO";
$boSNFaltas                 = "NÃO";
$boSNBancoHoras             = "NÃO";
$stPerInicial               = "22:00";
$stPerFinal                 = "05:00";
$nuFatorMultiplicacao       = "1,14";
$stMaximoHorasExtras        = "23:59";
$stMaximoDebito             = "23:59";

if ($stAcao == "alterar") {
    $stLocation = $pgList;
    $inCodConfiguracao = $_GET["inCodConfiguracao"];

    //**************************************************************************************************************************//
    //Aba Parâmetros Gerais
    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoParametrosGerais.class.php");
    $obTPontoConfiguracaoParametrosGerais = new TPontoConfiguracaoParametrosGerais();
    $stFiltro = " WHERE configuracao_parametros_gerais.cod_configuracao = ".$inCodConfiguracao;
    $obTPontoConfiguracaoParametrosGerais->recuperaRelacionamento($rsConfiguracoes,$stFiltro);
    $stUltimoTimestamp          = $rsConfiguracoes->getCampo("timestamp");
    $stDescricao                = $rsConfiguracoes->getCampo("descricao");
    $stPerInicial               = $rsConfiguracoes->getCampo("hora_noturno1");
    $stPerFinal                 = $rsConfiguracoes->getCampo("hora_noturno2");
    $inCodDiaDSR                = $rsConfiguracoes->getCampo("cod_dia_dsr");
    $dtVigencia                 = $rsConfiguracoes->getCampo("vigencia_formatada");
    $boLimitarAtrasos           = ($rsConfiguracoes->getCampo("limitar_atrasos")   == "t") ? "SIM" : "NÂO";
    $boSNAdicionalNoturno       = ($rsConfiguracoes->getCampo("separar_adicional") == "t") ? "SIM" : "NÂO";
    $boSNAbonoHorasTrabalhadas  = ($rsConfiguracoes->getCampo("lancar_abono")      == "t") ? "SIM" : "NÂO";
    $boSNDescontosFalta         = ($rsConfiguracoes->getCampo("lancar_desconto")   == "t") ? "SIM" : "NÂO";
    $boSNFeriadoExtra           = ($rsConfiguracoes->getCampo("trabalho_feriado")  == "t") ? "SIM" : "NÂO";
    $boSNHorasExtrasNoturnas    = ($rsConfiguracoes->getCampo("somar_extras")      == "t") ? "SIM" : "NÂO";

    Sessao::write("inCodConfiguracao",$inCodConfiguracao);
    Sessao::write("stUltimoTimestamp",$stUltimoTimestamp);

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoLotacao.class.php");
    $obTPontoConfiguracaoLotacao = new TPontoConfiguracaoLotacao();
    $obTPontoConfiguracaoLotacao->setDado("cod_configuracao",$inCodConfiguracao);
    $obTPontoConfiguracaoLotacao->setDado("timestamp",$stUltimoTimestamp);
    $obTPontoConfiguracaoLotacao->recuperaLotacoesDisponiveis($rsLotacoesDisponiveis, "", " ORDER BY cod_estrutural ");

    $stFiltro  = " WHERE configuracao_lotacao.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND configuracao_lotacao.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoConfiguracaoLotacao->recuperaLotacoesOcupadas($rsLotacoesOcupadas, $stFiltro, " ORDER BY cod_estrutural ");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoDiasUteis.class.php");
    $obTPontoDiasUteis = new TPontoDiasUteis();
    $stFiltro  = " WHERE dias_uteis.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND dias_uteis.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoDiasUteis->recuperaTodos($rsPontoDiasUteis,$stFiltro);
    $arPontoDiasUteis = array();
    while (!$rsPontoDiasUteis->eof()) {
        $arPontoDiasUteis[] = $rsPontoDiasUteis->getCampo("cod_dia");
        $rsPontoDiasUteis->proximo();
    }

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoCalendarioPonto.class.php");
    $obTPontoCalendarioPonto = new TPontoCalendarioPonto();
    $stFiltro  = " WHERE calendario_ponto.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND calendario_ponto.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoCalendarioPonto->recuperaRelacionamento($rsCadendario,$stFiltro);
    if ($rsCadendario->getNumLinhas() == 1) {
        $inCodCalendario    = $rsCadendario->getCampo("cod_calendar");
        $stDescCalendario   = $rsCadendario->getCampo("descricao");
        $stFuncaoCalendario = "abrePopUp('".CAM_GRH_CAL_POPUPS."calendario/FMConsultarCalendario.php','frm','','','','".Sessao::getId()."&inCodCalendario=".$inCodCalendario."','800','550')";
    }

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaltas.class.php");
    $obTPontoFaltas = new TPontoFaltas();
    $stFiltro  = " WHERE faltas.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND faltas.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoFaltas->recuperaTodos($rsFaltas,$stFiltro);
    $inFaltas = $rsFaltas->getCampo("minutos");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoAtrasos.class.php");
    $obTPontoAtrasos = new TPontoAtrasos();
    $stFiltro  = " WHERE atrasos.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND atrasos.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoAtrasos->recuperaTodos($rsAtrasos,$stFiltro);
    $inAtrasos = $rsAtrasos->getCampo("minutos");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasExtras.class.php");
    $obTPontoHorasExtras = new TPontoHorasExtras();
    $stFiltro  = " WHERE horas_extras.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND horas_extras.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoHorasExtras->recuperaTodos($rsHorasExtras,$stFiltro);
    $inHorasExtras   = $rsHorasExtras->getCampo("minutos");
    $stLimTolerancia = $rsHorasExtras->getCampo("periodo");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoRemarcacoesConsecutivas.class.php");
    $obTPontoRemarcacoesConsecutivas = new TPontoRemarcacoesConsecutivas();
    $stFiltro  = " WHERE remarcacoes_consecutivas.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND remarcacoes_consecutivas.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoRemarcacoesConsecutivas->recuperaTodos($rsRemarcacoesConsecutivas,$stFiltro);
    $inLimiteDe = $rsRemarcacoesConsecutivas->getCampo("minutos");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoArredondarTempo.class.php");
    $obTPontoArredondarTempo = new TPontoArredondarTempo();
    $stFiltro  = " WHERE arredondar_tempo.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND arredondar_tempo.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoArredondarTempo->recuperaTodos($rsArredondarTempo,$stFiltro);
    if ($rsArredondarTempo->getNumLinhas() == 1) {
        $boSNArredondarTempo = "SIM";
        Sessao::write("boSNArredondarTempo",$boSNArredondarTempo);
        Sessao::write("stEntrada1",$rsArredondarTempo->getCampo("hora_entrada1"));
        Sessao::write("stEntrada2",$rsArredondarTempo->getCampo("hora_entrada2"));
        Sessao::write("stSaida1",$rsArredondarTempo->getCampo("hora_saida1"));
        Sessao::write("stSaida2",$rsArredondarTempo->getCampo("hora_saida2"));
    }

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFatorMultiplicacao.class.php");
    $obTPontoFatorMultiplicacao = new TPontoFatorMultiplicacao();
    $stFiltro  = " WHERE fator_multiplicacao.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND fator_multiplicacao.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoFatorMultiplicacao->recuperaTodos($rsFatorMultiplicacao,$stFiltro);
    $nuFatorMultiplicacao = $rsFatorMultiplicacao->getCampo("fator");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasAnterior.class.php");
    $obTPontoHorasAnterior = new TPontoHorasAnterior();
    $stFiltro  = " WHERE horas_anterior.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND horas_anterior.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoHorasAnterior->recuperaTodos($rsHorasAnterior,$stFiltro);
    $stDiaAnterior = $rsHorasAnterior->getCampo("horas");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasPosterior.class.php");
    $obTPontoHorasPosterior = new TPontoHorasPosterior();
    $stFiltro  = " WHERE horas_posterior.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND horas_posterior.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoHorasPosterior->recuperaTodos($rsHorasPosterior,$stFiltro);
    $stDiaPosterior = $rsHorasPosterior->getCampo("horas");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaltaDsr.class.php");
    $obTPontoFaltaDsr = new TPontoFaltaDsr();
    $stFiltro  = " WHERE falta_dsr.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND falta_dsr.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoFaltaDsr->recuperaTodos($rsFaltaDsr,$stFiltro);
    $stFaltaLimiteDSR = $rsFaltaDsr->getCampo("horas");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasDescontoDsr.class.php");
    $obTPontoHorasDescontoDsr = new TPontoHorasDescontoDsr();
    $stFiltro  = " WHERE horas_desconto_dsr.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND horas_desconto_dsr.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoHorasDescontoDsr->recuperaTodos($rsHorasDescontoDsr,$stFiltro);
    $stDescontoDSR = $rsHorasDescontoDsr->getCampo("horas");

    //**************************************************************************************************************************//
    //Aba Horas Extras
    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoHorasExtras2.class.php");
    $obTPontoConfiguracaoHorasExtras2 = new TPontoConfiguracaoHorasExtras2();
    $stFiltro  = " WHERE configuracao_horas_extras_2.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND configuracao_horas_extras_2.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoConfiguracaoHorasExtras2->recuperaTodos($rsConfiguracaoHorasExtras2,$stFiltro);
    $boSNAntes1Periodo        = ($rsConfiguracaoHorasExtras2->getCampo("anterior_periodo_1")   == "t") ? "SIM" : "NÂO";
    $boSNEntre1e2Periodo      = ($rsConfiguracaoHorasExtras2->getCampo("entre_periodo_1_2")    == "t") ? "SIM" : "NÂO";
    $boSNDepois2Periodo       = ($rsConfiguracaoHorasExtras2->getCampo("posterior_periodo_2")  == "t") ? "SIM" : "NÂO";
    $boSNSomenteAutorizacao   = ($rsConfiguracaoHorasExtras2->getCampo("autorizacao")          == "t") ? "SIM" : "NÂO";
    $boSNAtrasos              = ($rsConfiguracaoHorasExtras2->getCampo("atrasos")              == "t") ? "SIM" : "NÂO";
    $boSNFaltas               = ($rsConfiguracaoHorasExtras2->getCampo("faltas")               == "t") ? "SIM" : "NÂO";

    //**************************************************************************************************************************//
    //Aba Banco de Horas
    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoBancoHoras.class.php");
    $obTPontoConfiguracaoBancoHoras = new TPontoConfiguracaoBancoHoras();
    $stFiltro  = " WHERE configuracao_banco_horas.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND configuracao_banco_horas.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoConfiguracaoBancoHoras->recuperaTodos($rsConfiguracaoBancoHoras,$stFiltro);
    $boSNBancoHoras           = ($rsConfiguracaoBancoHoras->getCampo("ativar_banco")   == "t") ? "SIM" : "NÂO";
    $stContagemLimites        = $rsConfiguracaoBancoHoras->getCampo("contagem_limites");
    $stLimiteMaximo           = $rsConfiguracaoBancoHoras->getCampo("horas_excesso");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasMaximoExtras.class.php");
    $obTPontoBancoHorasMaximoExtras = new TPontoBancoHorasMaximoExtras();
    $stFiltro  = " WHERE banco_horas_maximo_extras.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND banco_horas_maximo_extras.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoBancoHorasMaximoExtras->recuperaTodos($rsBancoHorasMaximoExtras,$stFiltro);
    $stMaximoHorasExtras = $rsBancoHorasMaximoExtras->getCampo("horas");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasMaximoDebito.class.php");
    $obTPontoBancoHorasMaximoDebito = new TPontoBancoHorasMaximoDebito();
    $stFiltro  = " WHERE banco_horas_maximo_debito.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND banco_horas_maximo_debito.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoBancoHorasMaximoDebito->recuperaTodos($rsBancoHorasMaximoDebito,$stFiltro);
    $stMaximoDebito = $rsBancoHorasMaximoDebito->getCampo("horas");

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasDias.class.php");
    $obTPontoBancoHorasDias = new TPontoBancoHorasDias();
    $stFiltro  = " WHERE banco_horas_dias.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= "   AND banco_horas_dias.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoBancoHorasDias->recuperaTodos($rsBancoHorasDias,$stFiltro);
    $arPontoDiaSemanaBancoDados = array();
    while (!$rsBancoHorasDias->eof()) {
        $arPontoDiaSemanaBancoDados[] = $rsBancoHorasDias->getCampo("cod_dia");
        $rsBancoHorasDias->proximo();
    }

    include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoFaixasHorasExtras.class.php");
    $obTPontoFormatoFaixasHorasExtras = new TPontoFormatoFaixasHorasExtras();
    $stFiltro  = " AND formato_faixas_horas_extras.cod_configuracao = ".$inCodConfiguracao;
    $stFiltro .= " AND formato_faixas_horas_extras.timestamp = '".$stUltimoTimestamp."'";
    $obTPontoFormatoFaixasHorasExtras->recuperaRelacionamento($rsFormatoFaixasHorasExtras,$stFiltro);

    if ($rsFormatoFaixasHorasExtras->getNumLinhas() > 0) {
        $stMensagem  = "<font color='red' size='2'>";
        $stMensagem .= "Esta configuração possui faixas de horas-extras que foram utilizadas na Configuração do Formato da Exportação.
                        Caso você faça alguma alteração e pressione o botão OK, a Configuração do Formato da Exportação será perdida.
                        Se realmente for necessária a alteração desta configuração, você deve refazer as configurações perdidas do Formato de Exportação.";
        $stMensagem .= "</font>";
    }
}

$jsOnLoad = "executaFuncaoAjax('onLoad')";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $stAcao                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

$obHdnConfiguracao =  new Hidden;
$obHdnConfiguracao->setName("inCodConfiguracao");
$obHdnConfiguracao->setValue($inCodConfiguracao);

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

//**************************************************************************************************************************//
//Aba Parâmetros Gerais
$obTxtDescricao = new TextBox();
$obTxtDescricao->setName('stDescricao');
$obTxtDescricao->setId('stDescricao');
$obTxtDescricao->setValue($stDescricao);
$obTxtDescricao->setRotulo('Descrição');
$obTxtDescricao->setTitle('Informe o nome da configuração.');
$obTxtDescricao->setNull(false);
$obTxtDescricao->setSize('100');
$obTxtDescricao->setMaxLength('100');

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
$obISelectMultiploLotacao = new ISelectMultiploLotacao();
$obISelectMultiploLotacao->setNull(false);
if ($stAcao == "alterar") {
    $obISelectMultiploLotacao->setDisponiveis($rsLotacoesDisponiveis);
    $obISelectMultiploLotacao->setSelecionados($rsLotacoesOcupadas);
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
$obTPessoalDiasTurno = new TPessoalDiasTurno();
$obTPessoalDiasTurno->recuperaTodos($rsDiasTurno);
$arDiasUteis = array();
while (!$rsDiasTurno->eof()) {
    $obChkDiaSemana = new Checkbox;
    $obChkDiaSemana->setRotulo('Dias Úteis');
    $obChkDiaSemana->setName('boDiaUtil_'.$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemana->setId('boDiaUtil_'.$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemana->setValue($rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemana->setLabel($rsDiasTurno->getCampo("nom_dia"));
    $obChkDiaSemana->setTitle('Marque os dias úteis para que o sistema possa identificá-los para cálculo das horas-extras. Os dias selecionados serão considerados como úteis e os não selecionados como não úteis.');
    if ($stAcao == "alterar") {
        if (in_array($rsDiasTurno->getCampo("cod_dia"),$arPontoDiasUteis)) {
            $obChkDiaSemana->setChecked(true);
        }
    }
    $arDiasUteis[] = $obChkDiaSemana;
    $rsDiasTurno->proximo();
}

include_once(CAM_GRH_CAL_COMPONENTES."IPopUpCalendario.class.php");
$obIPopUpCalendario = new IPopUpCalendario();
$obIPopUpCalendario->setRotulo("Calendário de Feriados");
$obIPopUpCalendario->setTitle("Selecione o calendário previamente cadastrado em Gestão Recursos Humanos :: Calendário :: Calendário :: Incluir Calendário.");
$obIPopUpCalendario->obCampoCod->setValue($inCodCalendario);
$obIPopUpCalendario->setValue($stDescCalendario);
$obIPopUpCalendario->setFuncaoCalendario($stFuncaoCalendario);

$obIntFaltas = new Inteiro();
$obIntFaltas->setRotulo("Faltas");
$obIntFaltas->setName("inFaltas");
$obIntFaltas->setId("inFaltas");
$obIntFaltas->setValue($inFaltas);
$obIntFaltas->setTitle("Informe a quantidade de minutos limite para que sejam consideradas como horas-faltas para o servidor.   Exemplo: Faltas = 10 minutos. Se as horas de um servidor durante o dia somar 11 minutos, será apresentado no relatório 11 minutos de faltas, caso contrário se somar 9 minutos durante o dia, será exibido no relatório 0 minutos de faltas.");
$obIntFaltas->setMaxLength(4);

$obIntAtrasos = new Inteiro();
$obIntAtrasos->setRotulo("Atrasos");
$obIntAtrasos->setName("inAtrasos");
$obIntAtrasos->setId("inAtrasos");
$obIntAtrasos->setValue($inAtrasos);
$obIntAtrasos->setTitle("Informe a quantidade de minutos limite para que o sistema considere atraso. Caso exceda o tempo, os minutos deixarão de ser considerado atraso e passarão a horas-faltas. Exemplo: parâmetro Atrasos = 10 minutos. O servidor possui 5 minutos de horas-faltas, o sistema apresentará no relatório espelho do ponto as colunas Atrasos = 5 minutos e Faltas = 0 minutos. Caso o servidor possua no dia 15 minutos de horas-faltas, o sistema irá apresentar no relatório: Atrasos = 0 minutos e Faltas = 15 minutos.");
$obIntAtrasos->setMaxLength(4);

$obLblMin = new Label;
$obLblMin->setValue("Min");

$obLimitarAtrasos = new SimNao();
$obLimitarAtrasos->setRotulo("Limitar os Atrasos ao Tempo?");
$obLimitarAtrasos->setName("boLimitarAtrasos");
$obLimitarAtrasos->setId("boLimitarAtrasos");
$obLimitarAtrasos->setTitle("Marcar esta opção para que o sistema limite a quantidade de horas de atraso e considere o restante como horas-faltas. Exemplo: parâmetro Atrasos = 5 minutos. Se o servidor possuir 30 minutos de horas-faltas no dia, no relatório irá constar 5 minutos de atraso e 25 minutos de horas-faltas.");
$obLimitarAtrasos->setChecked($boLimitarAtrasos);

$obIntHorasExtras = new Inteiro();
$obIntHorasExtras->setRotulo("Horas-Extras");
$obIntHorasExtras->setName("inHorasExtras");
$obIntHorasExtras->setId("inHorasExtras");
$obIntHorasExtras->setValue($inHorasExtras);
$obIntHorasExtras->setTitle("Informar a quantidade de horas ou minutos para que o sistema desconsidere as horas-extras, ou seja, até o limite desta quantidade não contabilizará as horas-extras. Caso o servidor realize mais horas-extras do que este parâmetro, as horas serão registradas na coluna horas-extras. Exemplo: 30 minutos, 90 minutos....");
$obIntHorasExtras->setMaxLength(4);

$obRdoLimToleranciaDiario = new Radio();
$obRdoLimToleranciaDiario->setRotulo("Limites de Tolerância");
$obRdoLimToleranciaDiario->setName("stLimTolerancia");
$obRdoLimToleranciaDiario->setId("stLimTolerancia");
$obRdoLimToleranciaDiario->setLabel("Diário");
$obRdoLimToleranciaDiario->setValue("D");
$obRdoLimToleranciaDiario->setTitle("Marcar como devem ser contados os limites de tolerância das horas-extras, faltas e atrasos: somatório diário ou semanal.");
$obRdoLimToleranciaDiario->setChecked(($stLimTolerancia == "D" || $stLimTolerancia == "") ? true : false);

$obRdoLimToleranciaSemanal = new Radio();
$obRdoLimToleranciaSemanal->setRotulo("Limites de Tolerância");
$obRdoLimToleranciaSemanal->setName("stLimTolerancia");
$obRdoLimToleranciaSemanal->setId("stLimTolerancia");
$obRdoLimToleranciaSemanal->setLabel("Semanal");
$obRdoLimToleranciaSemanal->setValue("S");
$obRdoLimToleranciaSemanal->setTitle("Marcar como devem ser contados os limites de tolerância das horas-extras, faltas e atrasos: somatório diário ou semanal.");
$obRdoLimToleranciaSemanal->setChecked(($stLimTolerancia == "S") ? true : false);

$obIntLimiteDe = new Inteiro();
$obIntLimiteDe->setRotulo("Desconsiderar Remarcações Consecutivas até o Limite de");
$obIntLimiteDe->setName("inLimiteDe");
$obIntLimiteDe->setId("inLimiteDe");
$obIntLimiteDe->setValue($inLimiteDe);
$obIntLimiteDe->setTitle("Informar neste campo a quantidade de minutos para verificação de marcações consecutivas e muito próximas entre si, ou seja, quantidade de tempo mínima para que desconsidere vários  registradas de marcações para um mesmo período.");
$obIntLimiteDe->setMaxLength(4);

$obSNArredondarTempo = new SimNao();
$obSNArredondarTempo->setRotulo("Arredondar Tempo?");
$obSNArredondarTempo->setName("boSNArredondarTempo");
$obSNArredondarTempo->setId("boSNArredondarTempo");
$obSNArredondarTempo->setTitle("Marcar este campo, para que o sistema arredonde o horário de marcação do ponto, conforme o parâmetro de tempo indicada para cada entrada e saída dos campos seguintes. Exemplo: Se nos campos de entrada e saída constarem 00:15 minutos , o sistema irá considerar o seguinte: Se o servidor registrar o ponto entre 07:45 e 08:15, será exibido normalmente porém calculado como 08:00.");
$obSNArredondarTempo->setChecked($boSNArredondarTempo);
$obSNArredondarTempo->obRadioSim->obEvento->setOnChange("montaParametrosGET('gerarSpanArredondar','boSNArredondarTempo')");
$obSNArredondarTempo->obRadioNao->obEvento->setOnChange("montaParametrosGET('gerarSpanArredondar','boSNArredondarTempo')");

$obSpnArredondarTempo = new Span();
$obSpnArredondarTempo->setId("spnArredondarTempo");

$obHdnArredondarTempo = new HiddenEval();
$obHdnArredondarTempo->setName("hdnArredondarTempo");
$obHdnArredondarTempo->setId("hdnArredondarTempo");

$obHorPerInicial = new Hora();
$obHorPerInicial->setRotulo("Período Horas Noturnas");
$obHorPerInicial->setName("stPerInicial");
$obHorPerInicial->setId("stPerInicial");
$obHorPerInicial->setTitle("Informe o período que o sistema deve considerar horas noturnas.");
$obHorPerInicial->setNull(false);
$obHorPerInicial->setValue($stPerInicial);

$obHorPerFinal = new Hora();
$obHorPerFinal->setRotulo("Período Horas Noturnas");
$obHorPerFinal->setName("stPerFinal");
$obHorPerFinal->setId("stPerFinal");
$obHorPerFinal->setTitle("Informe o período que o sistema deve considerar horas noturnas.");
$obHorPerFinal->setNull(false);
$obHorPerFinal->setValue($stPerFinal);

$obLblAs = new Label;
$obLblAs->setValue("Às");

$obSNAdicionalNoturno = new SimNao();
$obSNAdicionalNoturno->setRotulo("Separar Adicional Noturno de Horas-Extras?");
$obSNAdicionalNoturno->setName("boSNAdicionalNoturno");
$obSNAdicionalNoturno->setId("boSNAdicionalNoturno");
$obSNAdicionalNoturno->setTitle("Marque para adicionar às horas-extras as horas do adicional noturno, ou seja, será calculada e separada as horas do período noturno (22:00 hs - 05:00hs), sendo considerado trabalho noturno (adicional noturno) e as horas-extras que estiverem dentro deste período serão consideradas extra noturna.");
$obSNAdicionalNoturno->setChecked($boSNAdicionalNoturno);

$obNumFatorMultiplicacao = new Numerico();
$obNumFatorMultiplicacao->setRotulo("Fator de Multiplicação da Hora Reduzida Adicional Noturno");
$obNumFatorMultiplicacao->setName("nuFatorMultiplicacao");
$obNumFatorMultiplicacao->setId("nuFatorMultiplicacao");
$obNumFatorMultiplicacao->setTitle("Informe o coeficiente de multiplicação para o cálculo do adicional noturno com hora reduzida.");
$obNumFatorMultiplicacao->setValue($nuFatorMultiplicacao);

$obHorDiaAnterior = new Hora();
$obHorDiaAnterior->setRotulo("Horas Pertencentes ao Dia Anterior");
$obHorDiaAnterior->setName("stDiaAnterior");
$obHorDiaAnterior->setId("stDiaAnterior");
$obHorDiaAnterior->setValue($stDiaAnterior);
$obHorDiaAnterior->setTitle("Informe a quantidade de horas que serão somadas ao último horário registrado no dia, para que o sistema tenha controle do limite de até quando considerar marcações do mesmo dia. Este recurso é útil para as pessoas que trabalham em período noturno. Quando as marcações de ponto do servidor ultrapassar para o próximo dia (00:00), normalmente essas marcações ficariam desorganizadas, já que seriam registradas no dia seguinte. Utilizando este parâmetro, o sistema irá somar essas horas ao último horário registrado em um dia, passando a ler os próximos horários como sendo desse mesmo dia até este limite de horas somadas.");

$obHorDiaPosterior = new Hora();
$obHorDiaPosterior->setRotulo("Horas Pertencentes ao Dia Posterior");
$obHorDiaPosterior->setName("stDiaPosterior");
$obHorDiaPosterior->setId("stDiaPosterior");
$obHorDiaPosterior->setValue($stDiaPosterior);
$obHorDiaPosterior->setTitle("Informe neste campo a quantidade de horas que deverão ser consideradas para registro do dia posterior. É um parâmetro especial para quem inicia seu horário de trabalho às 00:00. Exemplo: Se digitar 00:30 neste campo, todas os registros realizados entre 23:30 e 23:59 serão considerados como sendo do próximo dia do seu real dia de registro.");

$obHorFaltaLimiteDSR = new Hora();
$obHorFaltaLimiteDSR->setRotulo("Quantidade de Horas-Faltas Limite para Abonar o DSR");
$obHorFaltaLimiteDSR->setName("stFaltaLimiteDSR");
$obHorFaltaLimiteDSR->setId("stFaltaLimiteDSR");
$obHorFaltaLimiteDSR->setValue($stFaltaLimiteDSR);
$obHorFaltaLimiteDSR->setTitle("Informe a quantidade de hora de falta limite para que o sistema não desconte o abono (quantidade de horas de abono) do descanso remunerado da respectiva semana. ");

$obHorDescontoDSR = new Hora();
$obHorDescontoDSR->setRotulo("Quantidade de Horas Desconto do DSR");
$obHorDescontoDSR->setName("stDescontoDSR");
$obHorDescontoDSR->setId("stDescontoDSR");
$obHorDescontoDSR->setValue($stDescontoDSR);
$obHorDescontoDSR->setTitle("Informe a quantidade de horas de abono que deverá ser descontado no dia do repouso, em caso das horas-faltas ultrapassar o limite.");

include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoDiasSemana.class.php");
$obTAdministracaoDiasSemana = new TDiasSemana();
$obTAdministracaoDiasSemana->recuperaTodos($rsDiasSemana);
$obCmbDiaSemana = new Select();
$obCmbDiaSemana->setRotulo("Dia da Semana do DSR");
$obCmbDiaSemana->setName("inCodDiaDSR");
$obCmbDiaSemana->setId("inCodDiaDSR");
$obCmbDiaSemana->setValue($inCodDiaDSR);
$obCmbDiaSemana->setCampoId("cod_dia");
$obCmbDiaSemana->setCampoDesc("nom_dia");
$obCmbDiaSemana->preencheCombo($rsDiasSemana);
$obCmbDiaSemana->setNull(false);
$obCmbDiaSemana->setTitle("Selecione o dia da semana em que será considerado o dia do descanso semanal remunerado.");

$obSNAbonoHorasTrabalhadas = new SimNao();
$obSNAbonoHorasTrabalhadas->setRotulo("Lançar Abono como Horas Trabalhadas?");
$obSNAbonoHorasTrabalhadas->setName("boSNAbonoHorasTrabalhadas");
$obSNAbonoHorasTrabalhadas->setId("boSNAbonoHorasTrabalhadas");
$obSNAbonoHorasTrabalhadas->setTitle("Marque esta opção para que o sistema some as horas de abono às horas trabalhadas. Exemplo: com a opção não selecionada:  Horas Trabalhadas = 220:00 , Horas Abonadas = 00:00. Com a opção selecionada: Horas Trabalhadas = 176:00 , Horas Abonadas = 44:00.");
$obSNAbonoHorasTrabalhadas->setChecked($boSNAbonoHorasTrabalhadas);

$obSNDescontosFalta = new SimNao();
$obSNDescontosFalta->setRotulo("Lançar Descontos como Falta?");
$obSNDescontosFalta->setName("boSNDescontosFalta");
$obSNDescontosFalta->setId("boSNDescontosFalta");
$obSNDescontosFalta->setTitle("Marcar esta opção para que o sistema some as horas-faltas às horas descontadas (abono). Exemplo: O servidor, durante seu período mensal possui o total de 40 horas de faltas e 20 horas descontadas. Com esta opção selecionada o espelho de ponto ficará da seguinte maneira: Faltas = 060:00 e Horas Descontadas = 000:00. Com esta opção não selecionada o espelho de ponto ficará da seguinte maneira: Faltas = 040:00  e Horas Descontadas = 020:00.");
$obSNDescontosFalta->setChecked($boSNDescontosFalta);

$obSNFeriadoExtra = new SimNao();
$obSNFeriadoExtra->setRotulo("Trabalho em Feriado Lançado como Extra?");
$obSNFeriadoExtra->setName("boSNFeriadoExtra");
$obSNFeriadoExtra->setId("boSNFeriadoExtra");
$obSNFeriadoExtra->setTitle("Marque esta opção para que o sistema considere os dias trabalhados como horas-extras (e não some como horas trabalhadas).");
$obSNFeriadoExtra->setChecked($boSNFeriadoExtra);

$obSNHorasExtrasNoturnas = new SimNao();
$obSNHorasExtrasNoturnas->setRotulo("Somar Horas-Extras e Extras Noturnas?");
$obSNHorasExtrasNoturnas->setName("boSNHorasExtrasNoturnas");
$obSNHorasExtrasNoturnas->setId("boSNHorasExtrasNoturnas");
$obSNHorasExtrasNoturnas->setTitle("Marque esta opção para que o sistema adicione às horas-extras as horas noturnas. ");
$obSNHorasExtrasNoturnas->setChecked($boSNHorasExtrasNoturnas);

$obDtVigencia = new Data();
$obDtVigencia->setRotulo("Vigência");
$obDtVigencia->setName("dtVigencia");
$obDtVigencia->setId("dtVigencia");
$obDtVigencia->setValue($dtVigencia);
$obDtVigencia->setNull(false);
$obDtVigencia->setTitle("Informe a vigência da configuração.");

//**************************************************************************************************************************//
//Aba Horas Extras
$obSNAntes1Periodo = new SimNao();
$obSNAntes1Periodo->setRotulo("Antes do 1° Período?");
$obSNAntes1Periodo->setName("boSNAntes1Periodo");
$obSNAntes1Periodo->setId("boSNAntes1Periodo");
$obSNAntes1Periodo->setTitle("Marque esta opção para que o sistema considere como horas-extras, as horas trabalhadas antes do primeiro período (entrada1) da grade de horários/escala do servidor.");
$obSNAntes1Periodo->setChecked($boSNAntes1Periodo);

$obSNEntre1e2Periodo = new SimNao();
$obSNEntre1e2Periodo->setRotulo("Entre o 1° e o 2° Período?");
$obSNEntre1e2Periodo->setName("boSNEntre1e2Periodo");
$obSNEntre1e2Periodo->setId("boSNEntre1e2Periodo");
$obSNEntre1e2Periodo->setTitle("Marque esta opção para que o sistema considere como horas-extras, o período entre a saída1 e entrada2 da grade de horários/escala do servidor.");
$obSNEntre1e2Periodo->setChecked($boSNEntre1e2Periodo);

$obSNDepois2Periodo = new SimNao();
$obSNDepois2Periodo->setRotulo("Depois do 2° Período?");
$obSNDepois2Periodo->setName("boSNDepois2Periodo");
$obSNDepois2Periodo->setId("boSNDepois2Periodo");
$obSNDepois2Periodo->setTitle("Marque esta opção para que o sistema considere como horas-extras, as horas trabalhadas depois do segundo período(saída2) da grade de horários/escala do servidor.");
$obSNDepois2Periodo->setChecked($boSNDepois2Periodo);

$obSNSomenteAutorizacao = new SimNao();
$obSNSomenteAutorizacao->setRotulo("Somente com Autorização?");
$obSNSomenteAutorizacao->setName("boSNSomenteAutorizacao");
$obSNSomenteAutorizacao->setId("boSNSomenteAutorizacao");
$obSNSomenteAutorizacao->setTitle("Marque esta opção para que o sistema calcule as horas-extras somente para quem possuir autorização (para autorizar geração, acessar manutenção :: alterar ponto).");
$obSNSomenteAutorizacao->setChecked($boSNSomenteAutorizacao);

$obSNAtrasos = new SimNao();
$obSNAtrasos->setRotulo("Atrasos?");
$obSNAtrasos->setName("boSNAtrasos");
$obSNAtrasos->setId("boSNAtrasos");
$obSNAtrasos->setTitle("Marque esta opção para que o sistema compense os minutos de atraso nas horas-extras do mesmo dia. Exemplo: Se o servidor tiver 20 minutos de horas extra e 10 minutos de atraso, o sistema apresentará no relatório espelho do ponto apenas 10 minutos de horas-extras.");
$obSNAtrasos->setChecked($boSNAtrasos);

$obSNFaltas = new SimNao();
$obSNFaltas->setRotulo("Faltas?");
$obSNFaltas->setName("boSNFaltas");
$obSNFaltas->setId("boSNFaltas");
$obSNFaltas->setTitle("Marque esta opção para que o sistema desconte das horas-extras as horas-faltas que o servidor possuir.");
$obSNFaltas->setChecked($boSNFaltas);

$arCampoHorasExtras = array();
$stIdsComponentes   = "";

$arDiasUteisFaixa = array();
$rsDiasTurno->setPrimeiroElemento();
$arDiasHorasExtras = array();
while (!$rsDiasTurno->eof()) {
    $obChkDiaSemanaHorasExtras = new Checkbox;
    $obChkDiaSemanaHorasExtras->setRotulo("Dias Horas-Extras");
    $obChkDiaSemanaHorasExtras->setName("boDiaUtilHorasExtras_".$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemanaHorasExtras->setId("boDiaUtilHorasExtras_".$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemanaHorasExtras->setValue("false");
    $obChkDiaSemanaHorasExtras->setLabel($rsDiasTurno->getCampo("nom_dia"));
    $obChkDiaSemanaHorasExtras->setTitle("Marque os dias em que as horas-extras serão calculadas, conforme o percentual e a quantidade de horas selecionados. Exemplo: Segunda a Sexta , percentual de 50% nas primeiras duas horas calculadas e o restante das horas serão consideradas de percentual 100%.");
    $obChkDiaSemanaHorasExtras->obEvento->setOnChange("if (this.value == 'true') { this.value = 'false' } else { this.value = 'true' }");
    $obChkDiaSemanaHorasExtras->setNullBarra(false);

    $arDiasHorasExtras[] = $obChkDiaSemanaHorasExtras;
    $arCampoHorasExtras[] = $obChkDiaSemanaHorasExtras;
    $stIdsComponentes .= $obChkDiaSemanaHorasExtras->getId().",";
    $rsDiasTurno->proximo();
}

$obIntPercentual = new Inteiro;
$obIntPercentual->setRotulo("Percentual");
$obIntPercentual->setName("inPercentual");
$obIntPercentual->setId("inPercentual");
$obIntPercentual->setNullBarra(false);
$obIntPercentual->setMaxlength(3);
$obIntPercentual->setTitle("Informe o percentual das horas-extras. Exemplo: 50,00 , 100,00.");

$arCampoHorasExtras[] = $obIntPercentual;
$stIdsComponentes .= $obIntPercentual->getId().",";

$obLblPercentual = new Label;
$obLblPercentual->setValue("%");

$obHorQuantHoras = new Hora();
$obHorQuantHoras->setRotulo("Quantidade de Horas");
$obHorQuantHoras->setName("stQuantHoras");
$obHorQuantHoras->setId("stQuantHoras");
$obHorQuantHoras->setNullBarra(false);
$obHorQuantHoras->setTitle("Informe a quantidade limite de horas que deverão ser enquadrados os percentuais das extras. Exemplo: Servidor possui na segunda-feira 2 horas-extras, na terça-feira 3 horas-extras e na quarta-feira 5 horas-extras. Sendo a configuração de 50% até 01:00hs, 100% até 02:00 e 150% até 23:59 , com cálculos Diários, o sistema calcularia: Na segunda-feira: 01:00 hora extra 50% na segunda-feira e 01:00 hora extra a 100%. Na terça-feira, calcularia 01:00 hora extra a 50% e 2 horas-extras a 100%. Na Quarta-feira, 01:00 extra a 50% e 2 horas-extras a 150%.");

$arCampoHorasExtras[] = $obHorQuantHoras;
$stIdsComponentes .= $obHorQuantHoras->getId().",";

$obRdoHorasExtrasDiario = new Radio;
$obRdoHorasExtrasDiario->setRotulo("Cálculo das Horas-Extras");
$obRdoHorasExtrasDiario->setName("stCalculoHorasExtras");
$obRdoHorasExtrasDiario->setId("stCalculoHorasExtras");
$obRdoHorasExtrasDiario->setValue("Diário");
$obRdoHorasExtrasDiario->setLabel("Diário");
$obRdoHorasExtrasDiario->setTitle("Selecione a forma de contagem e enquadramento das horas-extras: diário, semanal ou mensal.");
$obRdoHorasExtrasDiario->setChecked(true);

$arCampoHorasExtras[] = $obRdoHorasExtrasDiario;
$stIdsComponentes .= $obRdoHorasExtrasDiario->getId().",";

$obRdoHorasExtrasMensal = new Radio;
$obRdoHorasExtrasMensal->setRotulo("Cálculo das Horas-Extras");
$obRdoHorasExtrasMensal->setName("stCalculoHorasExtras");
$obRdoHorasExtrasMensal->setId("stCalculoHorasExtras");
$obRdoHorasExtrasMensal->setValue("Mensal");
$obRdoHorasExtrasMensal->setLabel("Mensal");
$obRdoHorasExtrasMensal->setTitle("Selecione a forma de contagem e enquadramento das horas-extras: diário, semanal ou mensal.");

$arCampoHorasExtras[] = $obRdoHorasExtrasMensal;

$obRdoHorasExtrasSemanal = new Radio;
$obRdoHorasExtrasSemanal->setRotulo("Cálculo das Horas-Extras");
$obRdoHorasExtrasSemanal->setName("stCalculoHorasExtras");
$obRdoHorasExtrasSemanal->setId("stCalculoHorasExtras");
$obRdoHorasExtrasSemanal->setValue("Semanal");
$obRdoHorasExtrasSemanal->setLabel("Semanal");
$obRdoHorasExtrasSemanal->setTitle("Selecione a forma de contagem e enquadramento das horas-extras: diário, semanal ou Semanal.");

$arCampoHorasExtras[] = $obRdoHorasExtrasSemanal;

$obSpnFaixasHorasExtras = new Span();
$obSpnFaixasHorasExtras->setId("spnFaixasHorasExtras");

$stIdsComponentes = substr($stIdsComponentes,0,strlen($stIdsComponentes)-1);

//**************************************************************************************************************************//
//Aba Banco de Horas
$obSNBancoHoras = new SimNao();
$obSNBancoHoras->setRotulo("Ativar Banco de Horas?");
$obSNBancoHoras->setName("boSNBancoHoras");
$obSNBancoHoras->setId("boSNBancoHoras");
$obSNBancoHoras->setTitle("Clique para ativar o banco de horas, ou seja, para que o sistema apenas armazene as horas-extras e horas de falta do servidor em um banco de horas. Com isso, o servidor passará a não ter horas-extras nem horas-faltas em seu relatório mensal.");
$obSNBancoHoras->setChecked($boSNBancoHoras);

$obRdoContagemLimitesDiario = new Radio;
$obRdoContagemLimitesDiario->setRotulo("Contagem dos Limites");
$obRdoContagemLimitesDiario->setName("stContagemLimites");
$obRdoContagemLimitesDiario->setId("stContagemLimites");
$obRdoContagemLimitesDiario->setValue("D");
$obRdoContagemLimitesDiario->setLabel("Diário");
$obRdoContagemLimitesDiario->setTitle("Marque a forma de cálculo dos limites do banco de horas : diário, semanal ou mensal.");
$obRdoContagemLimitesDiario->setChecked(($stContagemLimites == "D" || $stContagemLimites == "") ? true : false);

$arCampoContagemLimites[] = $obRdoContagemLimitesDiario;

$obRdoContagemLimitesMensal = new Radio;
$obRdoContagemLimitesMensal->setRotulo("Contagem dos Limites");
$obRdoContagemLimitesMensal->setName("stContagemLimites");
$obRdoContagemLimitesMensal->setId("stContagemLimites");
$obRdoContagemLimitesMensal->setValue("M");
$obRdoContagemLimitesMensal->setLabel("Mensal");
$obRdoContagemLimitesMensal->setTitle("Marque a forma de cálculo dos limites do banco de horas : diário, semanal ou mensal.");
$obRdoContagemLimitesMensal->setChecked(($stContagemLimites == "M") ? true : false);

$arCampoContagemLimites[] = $obRdoContagemLimitesMensal;

$obRdoContagemLimitesSemanal = new Radio;
$obRdoContagemLimitesSemanal->setRotulo("Contagem dos Limites");
$obRdoContagemLimitesSemanal->setName("stContagemLimites");
$obRdoContagemLimitesSemanal->setId("stContagemLimites");
$obRdoContagemLimitesSemanal->setValue("S");
$obRdoContagemLimitesSemanal->setLabel("Semanal");
$obRdoContagemLimitesSemanal->setTitle("Marque a forma de cálculo dos limites do banco de horas : diário, semanal ou mensal.");
$obRdoContagemLimitesSemanal->setChecked(($stContagemLimites == "S") ? true : false);

$arCampoContagemLimites[] = $obRdoContagemLimitesSemanal;

$obHorMaximoHorasExtras = new Hora();
$obHorMaximoHorasExtras->setRotulo("Máximo de Horas-Extras");
$obHorMaximoHorasExtras->setName("stMaximoHorasExtras");
$obHorMaximoHorasExtras->setId("stMaximoHorasExtras");
$obHorMaximoHorasExtras->setValue($stMaximoHorasExtras);
$obHorMaximoHorasExtras->setTitle("Informe a quantidade máxima de horas-extras que poderão ser enviadas para o banco de horas, de acordo com tipo de cálculo (diário, semanal ou mensal).");

$obHorMaximoDebito = new Hora();
$obHorMaximoDebito->setRotulo("Máximo de Débito");
$obHorMaximoDebito->setName("stMaximoDebito");
$obHorMaximoDebito->setId("stMaximoDebito");
$obHorMaximoDebito->setValue($stMaximoDebito);
$obHorMaximoDebito->setTitle("Informe o limite máximo de horas débito que poderá ser lançado no banco de horas, de acordo com o tipo de cálculo(diário , semanal ou mensal).");

$obRdoLimMaxBanco = new Radio();
$obRdoLimMaxBanco->setRotulo("Horas que Excedem o Limite Máximo Lançar em");
$obRdoLimMaxBanco->setName("stLimiteMaximo");
$obRdoLimMaxBanco->setId("stLimiteMaximo");
$obRdoLimMaxBanco->setValue("B");
$obRdoLimMaxBanco->setLabel("Banco de Horas");
$obRdoLimMaxBanco->setTitle("Informe para onde as horas-extras deverão ser registradas: Opção Banco de Horas - serão computados como banco de horas a quantidade de extras exceda o limite máximo (diário, semanal ou mensal). Caso contrário, as extras não excedentes serão contabilizadas como horas-extras. Opção Horas-Extras - serão computadas como horas-extras a quantidade de extras ultrapassar o limite máximo (diário, semanal ou mensal) , caso contrário, as extras não excedentes serão contabilizadas no banco de horas.");
$obRdoLimMaxBanco->setChecked(($stLimiteMaximo == "B" || $stLimiteMaximo == "" ) ? true : false);

$obRdoLimMaxExtra = new Radio();
$obRdoLimMaxExtra->setRotulo("Horas que Excedem o Limite Máximo Lançar em");
$obRdoLimMaxExtra->setName("stLimiteMaximo");
$obRdoLimMaxExtra->setId("stLimiteMaximo");
$obRdoLimMaxExtra->setValue("H");
$obRdoLimMaxExtra->setLabel("Horas-Extras");
$obRdoLimMaxExtra->setTitle("Informe para onde as horas-extras deverão ser registradas: Opção Banco de Horas - serão computados como banco de horas a quantidade de extras exceda o limite máximo (diário, semanal ou mensal). Caso contrário, as extras não excedentes serão contabilizadas como horas-extras. Opção Horas-Extras - serão computadas como horas-extras a quantidade de extras ultrapassar o limite máximo (diário, semanal ou mensal) , caso contrário, as extras não excedentes serão contabilizadas no banco de horas.");
$obRdoLimMaxExtra->setChecked(($stLimiteMaximo == "H") ? true : false);

$rsDiasTurno->setPrimeiroElemento();
$arDiasBancoDados = array();
while (!$rsDiasTurno->eof()) {
    $obChkDiaSemanaBancoDados = new Checkbox;
    $obChkDiaSemanaBancoDados->setRotulo("Dias");
    $obChkDiaSemanaBancoDados->setName("boDiaUtilBancoDados_".$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemanaBancoDados->setId("boDiaUtilBancoDados_".$rsDiasTurno->getCampo("cod_dia"));
    $obChkDiaSemanaBancoDados->setValue("false");
    $obChkDiaSemanaBancoDados->setLabel($rsDiasTurno->getCampo("nom_dia"));
    $obChkDiaSemanaBancoDados->setTitle("Informe os dias a considerar para o banco de horas.");
    if ($stAcao == "alterar") {
        if (in_array($rsDiasTurno->getCampo("cod_dia"),$arPontoDiaSemanaBancoDados)) {
            $obChkDiaSemanaBancoDados->setChecked(true);
        }
    }

    $arDiasBancoDados[] = $obChkDiaSemanaBancoDados;
    $rsDiasTurno->proximo();
}

$obLblMensagem = new Label();
$obLblMensagem->setRotulo("Observação");
$obLblMensagem->setId("stMensagem");
$obLblMensagem->setValue($stMensagem);

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formularioabas;
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnConfiguracao);
$obFormulario->addTitulo($obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right");
$obFormulario->addForm($obForm);
$obFormulario->addAba("Parâmetros Gerais");
$obFormulario->addComponente($obTxtDescricao);
$obFormulario->addComponente($obISelectMultiploLotacao);
$obFormulario->agrupaComponentes($arDiasUteis);
$obFormulario->addComponente($obIPopUpCalendario);
$obFormulario->addTitulo("Tempo de Tolerância");
$obFormulario->agrupaComponentes(array($obIntFaltas,$obLblMin));
$obFormulario->agrupaComponentes(array($obIntAtrasos,$obLblMin));
$obFormulario->addComponente($obLimitarAtrasos);
$obFormulario->agrupaComponentes(array($obIntHorasExtras,$obLblMin));
$obFormulario->agrupaComponentes(array($obRdoLimToleranciaDiario,$obRdoLimToleranciaSemanal));
$obFormulario->agrupaComponentes(array($obIntLimiteDe,$obLblMin));
$obFormulario->addComponente($obSNArredondarTempo);
$obFormulario->addSpan($obSpnArredondarTempo);
$obFormulario->addHidden($obHdnArredondarTempo,true);
$obFormulario->addTitulo("Período Noturno");
$obFormulario->agrupaComponentes(array($obHorPerInicial,$obLblAs,$obHorPerFinal));
$obFormulario->addComponente($obSNAdicionalNoturno);
$obFormulario->addComponente($obNumFatorMultiplicacao);
$obFormulario->addComponente($obHorDiaAnterior);
$obFormulario->addComponente($obHorDiaPosterior);
$obFormulario->addTitulo("Descanso Semanal Remunerado - DSR");
$obFormulario->addComponente($obHorFaltaLimiteDSR);
$obFormulario->addComponente($obHorDescontoDSR);
$obFormulario->addComponente($obCmbDiaSemana);
$obFormulario->addTitulo("Lançamentos");
$obFormulario->addComponente($obSNAbonoHorasTrabalhadas);
$obFormulario->addComponente($obSNDescontosFalta);
$obFormulario->addComponente($obSNFeriadoExtra);
$obFormulario->addComponente($obSNHorasExtrasNoturnas);
$obFormulario->addComponente($obDtVigencia);
$obFormulario->addAba("Horas-Extras");
$obFormulario->addTitulo("Considerar Horas-Extras");
$obFormulario->addComponente($obSNAntes1Periodo);
$obFormulario->addComponente($obSNEntre1e2Periodo);
$obFormulario->addComponente($obSNDepois2Periodo);
$obFormulario->addComponente($obSNSomenteAutorizacao);
$obFormulario->addTitulo("Compensar Horas-Extras (Mesmo Dia)");
$obFormulario->addComponente($obSNAtrasos);
$obFormulario->addComponente($obSNFaltas);
$obFormulario->addTitulo("Faixas de Horas-Extras");
$obFormulario->agrupaComponentes($arDiasHorasExtras);
$obFormulario->agrupaComponentes(array($obIntPercentual,$obLblPercentual));
$obFormulario->addComponente($obHorQuantHoras);
$obFormulario->agrupaComponentes(array($obRdoHorasExtrasDiario,$obRdoHorasExtrasMensal,$obRdoHorasExtrasSemanal));
$obFormulario->incluirAlterar("FaixasHorasExtras",$arCampoHorasExtras,false,true,$stIdsComponentes);
$obFormulario->addSpan($obSpnFaixasHorasExtras);
$obFormulario->addAba("Banco de Horas");
$obFormulario->addComponente($obSNBancoHoras);
$obFormulario->addTitulo("Limites do Banco de Horas");
$obFormulario->agrupaComponentes($arCampoContagemLimites);
$obFormulario->addComponente($obHorMaximoHorasExtras);
$obFormulario->addComponente($obHorMaximoDebito);
$obFormulario->agrupaComponentes(array($obRdoLimMaxBanco,$obRdoLimMaxExtra));
$obFormulario->addTitulo("Dias a Considerar para o Banco de Dados");
$obFormulario->agrupaComponentes($arDiasBancoDados);
if (trim($stMensagem) != "" AND $stAcao = "alterar") {
    $obFormulario->addDiv(2,"OBSERVACAO");
    $obFormulario->addComponente($obLblMensagem);
    $obFormulario->fechaDiv();
}
if ($stAcao == "incluir") {
    $obFormulario->ok();
} else {
    $obFormulario->cancelar($stLocation);
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
