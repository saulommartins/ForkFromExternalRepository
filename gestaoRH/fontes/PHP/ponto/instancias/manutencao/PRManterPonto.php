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
    * Formulário
    * Data de Criação: 13/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPonto.class.php"                               );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDadosRelogioPontoExtras.class.php"                         );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoDias.class.php"                                );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoHorario.class.php"                             );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoJustificativa.class.php"                       );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoRelogioPontoJustificativaExclusao.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );

$stPrograma = "ManterPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stLink  = Sessao::getId();
$stLink .= "&stDataInicial=".$_REQUEST["stPeriodoInicial"];
$stLink .= "&stDataFinal=".$_REQUEST["stPeriodoFinal"];
$stLink .= "&boTipoManutencao=".$_REQUEST["boTipoManutencao"];
$stLink .= "&inCodContrato=".$_REQUEST["inCodContrato"];

$stMensagem = "";
if (trim($_REQUEST["inCodContrato"])!="") {
    $stFiltro = " WHERE cod_contrato = ".$_REQUEST["inCodContrato"];
    $obTPessoalContrato = new TPessoalContrato();
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
    $stMensagem = "Matrícula: ".$rsContrato->getCampo("registro");
}

( $_POST["boAutorizarHorasExtras"] == 1 ? $boAutorizarHorasExtras = "true" : $boAutorizarHorasExtras = "false" );

if (trim($_POST["okVoltar"]) == "okLista") {
    $pgProx = $pgList;
} elseif (trim($_POST["okVoltar"]) == "okFiltro") {
    $pgProx = $pgFilt;
} elseif (trim($_POST["okVoltar"]) == "okManutencaoIndividual") {
    $pgProx = $pgForm;
}

function formataData($data)
{
    $stDataFormatada = str_replace("_", "/", $data);

    return $stDataFormatada;
}

function montaHorarios()
{
    $arRetorno = array();

    foreach ($_POST as $chave => $valor) {
        $pos = strpos($chave, "*_*");

        if ($pos === false) {
            //faz nada
        } else {
            list($lixo, $data, $lixo, $inCodContrato) = explode("*_*", $chave);
            $dataFormatada = formataData($data);

            if ($_REQUEST["boTipoManutencao"] == "LOTE_DIARIO") {
                $arRetorno[$inCodContrato][$dataFormatada][] = $valor;
            } else {
                $arRetorno[$dataFormatada][] = $valor;
            }
        }
    }

    return $arRetorno;
}

Sessao::setTrataExcecao(true);

$obTPontoDadosRelogioPonto         = new TPontoDadosRelogioPonto();
$obTPontoDadosRelogioPontoExtras   = new TPontoDadosRelogioPontoExtras();
$obTPontoRelogioPontoDias          = new TPontoRelogioPontoDias();
$obTPontoRelogioPontoHorario       = new TPontoRelogioPontoHorario();
$obTPontoRelogioPontoJustificativa = new TPontoRelogioPontoJustificativa();
$obTPontoRelogioPontoJustificativaExclusao = new TPontoRelogioPontoJustificativaExclusao();

$obTPontoRelogioPontoDias->obTPontoDadosRelogioPonto          = &$obTPontoDadosRelogioPonto;
$obTPontoDadosRelogioPontoExtras->obTPontoDadosRelogioPonto   = &$obTPontoDadosRelogioPonto;
$obTPontoRelogioPontoJustificativa->obTPontoDadosRelogioPonto = &$obTPontoDadosRelogioPonto;
$obTPontoRelogioPontoHorario->obTPontoRelogioPontoDias        = &$obTPontoRelogioPontoDias;

$arHorarios = montaHorarios();
$arJustificativas = Sessao::read("arJustificativas");

if (count($arHorarios) > 0) {
    if (trim($_REQUEST["boTipoManutencao"]) == "LOTE_DIARIO") {
        foreach ($arHorarios as $inCodContrato => $arDatas) {
            // PONTO.DADOS_RELOGIO_PONTO
            $obTPontoDadosRelogioPonto->setDado("cod_contrato", $inCodContrato);
            $obTPontoDadosRelogioPonto->recuperaPorChave($rsDadosRelogioPonto);
            if ($rsDadosRelogioPonto->getNumLinhas() < 0) {
                $obTPontoDadosRelogioPonto->inclusao();
            }

            foreach ($arDatas as $stData => $dadosHorario) {
                // PONTO.RELOGIO_PONTO_DIAS
                $stFiltro  = " WHERE cod_contrato = ".$inCodContrato;
                $stFiltro .= "   AND dt_ponto = to_date('".$stData."','dd/mm/yyyy')";
                $obTPontoRelogioPontoDias->recuperaTodos($rsRelogioPontoDias,$stFiltro);

                if ($rsRelogioPontoDias->getNumLinhas() < 0) {
                    $obTPontoRelogioPontoDias->setDado("cod_ponto", "");
                    $obTPontoRelogioPontoDias->setDado("dt_ponto", $stData);
                    $obTPontoRelogioPontoDias->inclusao();
                } else {
                    $obTPontoRelogioPontoDias->setDado("cod_ponto", $rsRelogioPontoDias->getCampo("cod_ponto"));
                }

                if (trim($dadosHorario[0]) == "") {
                    $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$inCodContrato;
                    $stFiltro .= "   AND relogio_ponto_dias.dt_ponto = to_date('".$stData."','dd/mm/yyyy')";

                    $obTPontoRelogioPontoHorario->recuperaTodosHorariosDia($rsRelogioPontoHorario, $stFiltro);

                    while (!$rsRelogioPontoHorario->eof()) {
                        $obTPontoRelogioPontoHorario->setDado("cod_contrato" , $rsRelogioPontoHorario->getCampo("cod_contrato"));
                        $obTPontoRelogioPontoHorario->setDado("cod_ponto"    , $rsRelogioPontoHorario->getCampo("cod_ponto"));
                        $obTPontoRelogioPontoHorario->setDado("cod_horario"  , $rsRelogioPontoHorario->getCampo("cod_horario"));
                        $obTPontoRelogioPontoHorario->setDado("timestamp"    , $rsRelogioPontoHorario->getCampo("timestamp"));
                        $obTPontoRelogioPontoHorario->exclusao();

                        $rsRelogioPontoHorario->proximo();
                    }
                }

                foreach ($dadosHorario as $stHora) {
                    // PONTO.RELOGIO_PONTO_HORARIO
                    if (trim($stHora)!="") {
                        $obTPontoRelogioPontoHorario->setDado("cod_horario", "");
                        $obTPontoRelogioPontoHorario->setDado("hora", $stHora);
                        $obTPontoRelogioPontoHorario->inclusao();
                    }
                }
            }
        }
    } else {
        // PONTO.DADOS_RELOGIO_PONTO
        $obTPontoDadosRelogioPonto->setDado("cod_contrato", $_POST["inCodContrato"]);
        $obTPontoDadosRelogioPonto->recuperaPorChave($rsDadosRelogioPonto);
        if ($rsDadosRelogioPonto->getNumLinhas() < 0) {
            $obTPontoDadosRelogioPonto->inclusao();
        }

        $obTPontoDadosRelogioPontoExtras->setDado("autorizar_horas_extras", $boAutorizarHorasExtras);
        $obTPontoDadosRelogioPontoExtras->inclusao();

        foreach ($arHorarios as $stData => $dadosHorario) {
            // PONTO.RELOGIO_PONTO_DIAS
            $stFiltro  = " WHERE cod_contrato = ".$_POST["inCodContrato"];
            $stFiltro .= "   AND dt_ponto = to_date('".$stData."','dd/mm/yyyy')";
            $obTPontoRelogioPontoDias->recuperaTodos($rsRelogioPontoDias,$stFiltro);

            if ($rsRelogioPontoDias->getNumLinhas() < 0) {
                $obTPontoRelogioPontoDias->setDado("cod_ponto", "");
                $obTPontoRelogioPontoDias->setDado("dt_ponto", $stData);
                $obTPontoRelogioPontoDias->inclusao();
            } else {
                $obTPontoRelogioPontoDias->setDado("cod_ponto", $rsRelogioPontoDias->getCampo("cod_ponto"));
            }

            if (trim($dados[0]) == "") {
                $stFiltro  = " WHERE relogio_ponto_dias.cod_contrato = ".$_POST["inCodContrato"];
                $stFiltro .= "   AND relogio_ponto_dias.dt_ponto = to_date('".$stData."','dd/mm/yyyy')";

                $obTPontoRelogioPontoHorario->recuperaTodosHorariosDia($rsRelogioPontoHorario, $stFiltro);

                while (!$rsRelogioPontoHorario->eof()) {
                    $obTPontoRelogioPontoHorario->setDado("cod_contrato" , $rsRelogioPontoHorario->getCampo("cod_contrato"));
                    $obTPontoRelogioPontoHorario->setDado("cod_ponto"    , $rsRelogioPontoHorario->getCampo("cod_ponto"));
                    $obTPontoRelogioPontoHorario->setDado("cod_horario"  , $rsRelogioPontoHorario->getCampo("cod_horario"));
                    $obTPontoRelogioPontoHorario->setDado("timestamp"    , $rsRelogioPontoHorario->getCampo("timestamp"));
                    $obTPontoRelogioPontoHorario->exclusao();

                    $rsRelogioPontoHorario->proximo();
                }
            }

            foreach ($dadosHorario as $stHora) {
                // PONTO.RELOGIO_PONTO_HORARIO
                if (trim($stHora)!="") {
                    $obTPontoRelogioPontoHorario->setDado("cod_horario", "");
                    $obTPontoRelogioPontoHorario->setDado("hora", $stHora);
                    $obTPontoRelogioPontoHorario->inclusao();
                }
            }
        }

        // PONTO.RELOGIO_PONTO_JUSTIFICATIVA
        if (count($arJustificativas)>0 ) {
            foreach ($arJustificativas as $inId => $dados) {
                $obTPontoRelogioPontoJustificativa->setDado( "sequencia"        , $dados["inSequencia"]);
                $obTPontoRelogioPontoJustificativa->setDado( "cod_justificativa", $dados["inCodJustificativa"]);
                $obTPontoRelogioPontoJustificativa->setDado( "periodo_inicio"   , $dados["stDataInicial"]);
                $obTPontoRelogioPontoJustificativa->setDado( "periodo_termino"  , $dados["stDataFinal"]);
                $obTPontoRelogioPontoJustificativa->setDado( "horas_falta"      , $dados["stHoraFaltasAnular"]);
                $obTPontoRelogioPontoJustificativa->setDado( "horas_abonar"     , $dados["stHoraAbonar"]);
                $obTPontoRelogioPontoJustificativa->setDado( "observacao"       , $dados["stObservacao"]);
                $obTPontoRelogioPontoJustificativa->inclusao();
            }
        }
        // PONTO.RELOGIO_PONTO_JUSTIFICATIVA_EXCLUSAO
        $arJustificativasExclusao = Sessao::read("arJustificativasExcluidos");
        if (count($arJustificativasExclusao)>0 ) {
            foreach ($arJustificativasExclusao as $inId => $dados) {
                if (trim($dados["inSequencia"]) != "") {
                    $obTPontoRelogioPontoJustificativaExclusao->setDado( "sequencia"        , $dados["inSequencia"]);
                    $obTPontoRelogioPontoJustificativaExclusao->setDado( "cod_justificativa", $dados["inCodJustificativa"]);
                    $obTPontoRelogioPontoJustificativaExclusao->setDado( "cod_contrato"     , $dados["inCodContrato"]);
                    $obTPontoRelogioPontoJustificativaExclusao->setDado( "timestamp"        , $dados["stTimestamp"]);
                    $obTPontoRelogioPontoJustificativaExclusao->inclusao();
                }
            }
        }
    }
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgProx."?".$stLink, $stMensagem,"incluir","aviso", Sessao::getId(), "../");

?>
