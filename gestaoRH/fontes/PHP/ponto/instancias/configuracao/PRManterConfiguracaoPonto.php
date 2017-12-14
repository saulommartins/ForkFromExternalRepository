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
 * Processamento para configuração do ponto
 * Data de Criação   : 14/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arLink = Sessao::read("link");
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoPonto";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoRelogioPonto.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoRelogioPontoExclusao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoParametrosGerais.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoHorasExtras2.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoLotacao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoCalendarioPonto.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoArredondarTempo.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoDiasUteis.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaltas.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoAtrasos.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasExtras.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasHorasExtra.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaixasDias.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasAnterior.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasPosterior.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFaltaDsr.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoHorasDescontoDsr.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoRemarcacoesConsecutivas.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFatorMultiplicacao.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoConfiguracaoBancoHoras.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasMaximoExtras.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasDias.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoBancoHorasMaximoDebito.class.php");

$obTPontoConfiguracaoRelogioPonto       = new TPontoConfiguracaoRelogioPonto();
$obTPontoConfiguracaoRelogioPontoExclusao = new TPontoConfiguracaoRelogioPontoExclusao();
$obTPontoConfiguracaoParametrosGerais   = new TPontoConfiguracaoParametrosGerais();
$obTPontoConfiguracaoHorasExtras2       = new TPontoConfiguracaoHorasExtras2();
$obTPontoConfiguracaoLotacao            = new TPontoConfiguracaoLotacao();
$obTPontoCalendarioPonto                = new TPontoCalendarioPonto();
$obTPontoArredondarTempo                = new TPontoArredondarTempo();
$obTPontoDiasUteis                      = new TPontoDiasUteis();
$obTPontoFaltas                         = new TPontoFaltas();
$obTPontoAtrasos                        = new TPontoAtrasos();
$obTPontoHorasExtras                    = new TPontoHorasExtras();
$obTPontoFaixasHorasExtra               = new TPontoFaixasHorasExtra();
$obTPontoFaixasDias                     = new TPontoFaixasDias();
$obTPontoHorasAnterior                  = new TPontoHorasAnterior();
$obTPontoHorasPosterior                 = new TPontoHorasPosterior();
$obTPontoFaltaDsr                       = new TPontoFaltaDsr();
$obTPontoHorasDescontoDsr               = new TPontoHorasDescontoDsr();
$obTPontoRemarcacoesConsecutivas        = new TPontoRemarcacoesConsecutivas();
$obTPontoFatorMultiplicacao             = new TPontoFatorMultiplicacao();
$obTPontoConfiguracaoBancoHoras         = new TPontoConfiguracaoBancoHoras();
$obTPontoBancoHorasMaximoExtras         = new TPontoBancoHorasMaximoExtras();
$obTPontoBancoHorasDias                 = new TPontoBancoHorasDias();
$obTPontoBancoHorasMaximoDebito         = new TPontoBancoHorasMaximoDebito();

$obTPontoCalendarioPonto->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoArredondarTempo->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoDiasUteis->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoFaltas->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoAtrasos->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoHorasExtras->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoHorasAnterior->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoHorasPosterior->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoFaltaDsr->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoHorasDescontoDsr->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoRemarcacoesConsecutivas->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoFatorMultiplicacao->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;
$obTPontoConfiguracaoLotacao->obTPontoConfiguracaoParametrosGerais = &$obTPontoConfiguracaoParametrosGerais;

$obTPontoFaixasHorasExtra->obTPontoConfiguracaoHorasExtras2 = &$obTPontoConfiguracaoHorasExtras2;
$obTPontoFaixasDias->obTPontoFaixasHorasExtra = &$obTPontoFaixasHorasExtra;

$obTPontoBancoHorasMaximoExtras->obTPontoConfiguracaoBancoHoras = &$obTPontoConfiguracaoBancoHoras;
$obTPontoBancoHorasDias->obTPontoConfiguracaoBancoHoras = &$obTPontoConfiguracaoBancoHoras;
$obTPontoBancoHorasMaximoDebito->obTPontoConfiguracaoBancoHoras = &$obTPontoConfiguracaoBancoHoras;

$obTPontoConfiguracaoParametrosGerais->obTPontoConfiguracaoRelogioPonto = &$obTPontoConfiguracaoRelogioPonto;
$obTPontoConfiguracaoHorasExtras2->obTPontoConfiguracaoRelogioPonto = &$obTPontoConfiguracaoRelogioPonto;
$obTPontoConfiguracaoBancoHoras->obTPontoConfiguracaoRelogioPonto = &$obTPontoConfiguracaoRelogioPonto;

Sessao::setTrataExcecao(true);
switch ($stAcao) {
    case "incluir":
    case "alterar";
        if ($stAcao == "incluir") {
            $pgRetorno = $pgForm;
        }
        if ($stAcao == "alterar") {
            $pgRetorno = $pgList;
        }
        if ($stAcao == "alterar") {
            $pgRetorno .= "&inCodConfiguracao=".$_POST["inCodConfiguracao"];
        }
        $stMensagem = "Configuração do Ponto Concluída com Sucesso.";

        $obTPontoConfiguracaoParametrosGerais->setDado("cod_dia_dsr"        ,$_POST["inCodDiaDSR"]);
        $obTPontoConfiguracaoParametrosGerais->setDado("descricao"          ,$_POST["stDescricao"]);
        $obTPontoConfiguracaoParametrosGerais->setDado("limitar_atrasos"    ,($_POST["boLimitarAtrasos"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("hora_noturno1"      ,$_POST["stPerInicial"]);
        $obTPontoConfiguracaoParametrosGerais->setDado("hora_noturno2"      ,$_POST["stPerFinal"]);
        $obTPontoConfiguracaoParametrosGerais->setDado("separar_adicional"  ,($_POST["boSNAdicionalNoturno"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("lancar_abono"       ,($_POST["boSNAbonoHorasTrabalhadas"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("lancar_desconto"    ,($_POST["boSNDescontosFalta"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("trabalho_feriado"   ,($_POST["boSNFeriadoExtra"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("somar_extras"       ,($_POST["boSNHorasExtrasNoturnas"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoParametrosGerais->setDado("vigencia"           ,$_POST["dtVigencia"]);

        $obTPontoConfiguracaoHorasExtras2->setDado("anterior_periodo_1"     ,($_POST["boSNAntes1Periodo"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoHorasExtras2->setDado("entre_periodo_1_2"      ,($_POST["boSNEntre1e2Periodo"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoHorasExtras2->setDado("posterior_periodo_2"    ,($_POST["boSNDepois2Periodo"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoHorasExtras2->setDado("autorizacao"            ,($_POST["boSNSomenteAutorizacao"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoHorasExtras2->setDado("atrasos"                ,($_POST["boSNAtrasos"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoHorasExtras2->setDado("faltas"                 ,($_POST["boSNFaltas"]=="S")?TRUE:FALSE);

        $obTPontoConfiguracaoBancoHoras->setDado("ativar_banco"             ,($_POST["boSNBancoHoras"]=="S")?TRUE:FALSE);
        $obTPontoConfiguracaoBancoHoras->setDado("contagem_limites"         ,$_POST["stContagemLimites"]);
        $obTPontoConfiguracaoBancoHoras->setDado("horas_excesso"            ,$_POST["stLimiteMaximo"]);

        if ($stAcao == "incluir") {
            $obTPontoConfiguracaoRelogioPonto->inclusao();
        } else {
            $obTPontoConfiguracaoRelogioPonto->setDado("cod_configuracao",$_POST["inCodConfiguracao"]);
        }
        $obTPontoConfiguracaoParametrosGerais->inclusao();
        $obTPontoConfiguracaoHorasExtras2->inclusao();
        $obTPontoConfiguracaoBancoHoras->inclusao();

        $obErro = new erro();
        foreach ($_POST["inCodLotacaoSelecionados"] as $inCodOrgao) {
            $stFiltro = " AND cod_orgao =  ".$inCodOrgao;
            if ($stAcao == "alterar") {
                $stFiltro .= " AND configuracao_lotacao.cod_configuracao != ".$_POST["inCodConfiguracao"];
            }
            $obTPontoConfiguracaoLotacao->recuperaRelacionamento($rsOrgao,$stFiltro);
            if ($rsOrgao->getNumLinhas() > 0) {
                $obErro->setDescricao("No entando, uma ou mais lotações não puderam ser inseridas para essa configuração por já terem sido inseridas em outra configuração.");
            } else {
                $obTPontoConfiguracaoLotacao->setDado("cod_orgao",$inCodOrgao);
                $obTPontoConfiguracaoLotacao->inclusao();
            }
        }
        if ($obErro->ocorreu()) {
            $stMensagem .= $obErro->getDescricao();
        }

        if ($_POST["inCodCalendario"] != "") {
            $obTPontoCalendarioPonto->setDado("cod_calendar",$_POST["inCodCalendario"]);
            $obTPontoCalendarioPonto->inclusao();
        }

        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalDiasTurno.class.php");
        $obTPessoalDiasTurno = new TPessoalDiasTurno();
        $obTPessoalDiasTurno->recuperaTodos($rsDiasTurno);
        while (!$rsDiasTurno->eof()) {
            if ($_POST["boDiaUtil_".$rsDiasTurno->getCampo("cod_dia")] != "") {
                $obTPontoDiasUteis->setDado("cod_dia",$_POST["boDiaUtil_".$rsDiasTurno->getCampo("cod_dia")]);
                $obTPontoDiasUteis->inclusao();
            }
            $rsDiasTurno->proximo();
        }
        if ($_POST["inFaltas"] != "") {
            $obTPontoFaltas->setDado("minutos",$_POST["inFaltas"]);
            $obTPontoFaltas->inclusao();
        }
        if ($_POST["inAtrasos"] != "") {
            $obTPontoAtrasos->setDado("minutos",$_POST["inAtrasos"]);
            $obTPontoAtrasos->inclusao();
        }
        if ($_POST["inHorasExtras"] != "") {
            $obTPontoHorasExtras->setDado("minutos",$_POST["inHorasExtras"]);
            $obTPontoHorasExtras->setDado("periodo",$_POST["stLimTolerancia"]);
            $obTPontoHorasExtras->inclusao();
        }
        if ($_POST["inLimiteDe"] != "") {
            $obTPontoRemarcacoesConsecutivas->setDado("minutos",$_POST["inLimiteDe"]);
            $obTPontoRemarcacoesConsecutivas->inclusao();
        }
        if ($_POST["boSNArredondarTempo"] == "S") {
            $obTPontoArredondarTempo->setDado("hora_entrada1",$_POST["stEntrada1"]);
            $obTPontoArredondarTempo->setDado("hora_entrada2",$_POST["stEntrada2"]);
            $obTPontoArredondarTempo->setDado("hora_saida1",$_POST["stSaida1"]);
            $obTPontoArredondarTempo->setDado("hora_saida2",$_POST["stSaida2"]);
            $obTPontoArredondarTempo->inclusao();
        }
        if ($_POST["nuFatorMultiplicacao"] != "") {
            $obTPontoFatorMultiplicacao->setDado("fator",$_POST["nuFatorMultiplicacao"]);
            $obTPontoFatorMultiplicacao->inclusao();
        }
        if ($_POST["stDiaAnterior"] != "") {
            $obTPontoHorasAnterior->setDado("horas",$_POST["stDiaAnterior"]);
            $obTPontoHorasAnterior->inclusao();
        }
        if ($_POST["stDiaPosterior"] != "") {
            $obTPontoHorasPosterior->setDado("horas",$_POST["stDiaPosterior"]);
            $obTPontoHorasPosterior->inclusao();
        }
        if ($_POST["stFaltaLimiteDSR"] != "") {
            $obTPontoFaltaDsr->setDado("horas",$_POST["stFaltaLimiteDSR"]);
            $obTPontoFaltaDsr->inclusao();
        }
        if ($_POST["stDescontoDSR"] != "") {
            $obTPontoHorasDescontoDsr->setDado("horas",$_POST["stDescontoDSR"]);
            $obTPontoHorasDescontoDsr->inclusao();
        }

        $arFaixasHorasExtras = Sessao::read("arFaixasHorasExtras");
        if (is_array($arFaixasHorasExtras) and count($arFaixasHorasExtras)>0) {
            foreach ($arFaixasHorasExtras as $arFaixaHorasExtras) {
                switch ($arFaixaHorasExtras["stCalculoHorasExtras"]) {
                    case "Diário":
                        $stCalculoHorasExtras = "D";
                        break;
                    case "Semanal":
                        $stCalculoHorasExtras = "S";
                        break;
                    case "Mensal":
                        $stCalculoHorasExtras = "M";
                        break;
                }
                $obTPontoFaixasHorasExtra->setDado("cod_faixa"          ,"");
                $obTPontoFaixasHorasExtra->setDado("percentual"         ,$arFaixaHorasExtras["inPercentual"]);
                $obTPontoFaixasHorasExtra->setDado("horas"              ,$arFaixaHorasExtras["stQuantHoras"]);
                $obTPontoFaixasHorasExtra->setDado("calculo_horas_extra",$stCalculoHorasExtras);
                $obTPontoFaixasHorasExtra->inclusao();

                foreach ($arFaixaHorasExtras["arDias"] as $inCodDia) {
                    $obTPontoFaixasDias->setDado("cod_dia",$inCodDia);
                    $obTPontoFaixasDias->inclusao();
                }
            }
        }

        if ($_POST["stMaximoHorasExtras"] != "") {
            $obTPontoBancoHorasMaximoExtras->setDado("horas",$_POST["stMaximoHorasExtras"]);
            $obTPontoBancoHorasMaximoExtras->inclusao();
        }
        if ($_POST["stMaximoDebito"] != "") {
            $obTPontoBancoHorasMaximoDebito->setDado("horas",$_POST["stMaximoDebito"]);
            $obTPontoBancoHorasMaximoDebito->inclusao();
        }

        $rsDiasTurno->setPrimeiroElemento();
        while (!$rsDiasTurno->eof()) {
            if ($_POST["boDiaUtilBancoDados_".$rsDiasTurno->getCampo("cod_dia")] != "") {
                $obTPontoBancoHorasDias->setDado("cod_dia",$rsDiasTurno->getCampo("cod_dia"));
                $obTPontoBancoHorasDias->inclusao();
            }
            $rsDiasTurno->proximo();
        }

        break;
    case "excluir":
        $pgRetorno = $pgList;
        $stMensagem = "Configuração do Ponto Excluída com Sucesso.";

        Sessao::consultarDadosSessao();
        $obTPontoConfiguracaoRelogioPontoExclusao->setDado("cod_configuracao",$_GET["inCodConfiguracao"]);
        $obTPontoConfiguracaoRelogioPontoExclusao->setDado("numcgm",Sessao::read("numCgm"));
        $obTPontoConfiguracaoRelogioPontoExclusao->inclusao();
        break;
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
?>
