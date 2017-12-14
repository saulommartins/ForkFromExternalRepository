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
    * Página de Processamento para Configuração de Tipos de Diárias
    * Data de Criação: 07/08/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRTipoDiarias.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasDiaria.class.php"                                         );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiaria.class.php"                                     );
include_once ( CAM_GRH_DIA_MAPEAMENTO."TDiariasTipoDiariaDespesa.class.php"                              );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoClassificacaoDespesa.class.php"                          );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                               );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "TipoDiarias";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);

        // $rsTipoDiarias        = new RecordSet();
        // $rsTipoDiariasDespesa = new RecordSet();

        $arTipoDiarias = Sessao::read('arTipoDiaria');

        //Verifica os ids que existem no banco. Caso não existam na sessão exclui
        $arCodTipoDiarias         = array();
        foreach ($arTipoDiarias as $arTipoDiariasTemp) {
            if ($arTipoDiariasTemp['inCodTipo'] != "") {
                $arCodTipoDiarias[] = $arTipoDiariasTemp['inCodTipo'];
            }
        }

        $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
        $obTDiariasTipoDiaria->recuperaTipoDiaria($rsTipoDiarias);
        while (!$rsTipoDiarias->eof()) {
            if (!in_array($rsTipoDiarias->getCampo('cod_tipo'), $arCodTipoDiarias)) {

                $obTDiariasDiaria = new TDiariasDiaria();
                $stFiltroDiarias = " WHERE cod_tipo = ".$rsTipoDiarias->getCampo('cod_tipo')."
                                       AND timestamp = '".$rsTipoDiarias->getCampo('timestamp')."' ";
                $obTDiariasDiaria->recuperaTodos($rsDiarias, $stFiltroDiarias);

                if ($rsDiarias->getNumLinhas()>0) {
                    Sessao::getExcecao()->setDescricao("Não foi possivel remover Tipo de Diária - ".$rsTipoDiarias->getCampo('nom_tipo').". O registro ainda é utilizado por Concessões de Diárias.");
                    break;
                }

                $obTDiariasTipoDiariaDespesa = new TDiariasTipoDiariaDespesa();
                $stComplementoChaveTipoDiariaDespesa = $obTDiariasTipoDiariaDespesa->getComplementoChave();
                $obTDiariasTipoDiariaDespesa->setComplementoChave('cod_tipo');
                $obTDiariasTipoDiariaDespesa->setDado('cod_tipo', $rsTipoDiarias->getCampo('cod_tipo'));
                $obTDiariasTipoDiariaDespesa->recuperaPorChave($rsTipoDiariasDespesa);

                if ($rsTipoDiariasDespesa->getNumLinhas() > 0) {
                    $obTDiariasTipoDiariaDespesa->exclusao();

                    $obTDiariasTipoDiariaDespesa->setComplementoChave($stComplementoChaveTipoDiariaDespesa);
                }

                if (!Sessao::getExcecao()->ocorreu()) {
                    $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
                    $stComplementoChaveTipoDiaria = $obTDiariasTipoDiaria->getComplementoChave();
                    $obTDiariasTipoDiaria->setComplementoChave('cod_tipo');
                    $obTDiariasTipoDiaria->setDado('cod_tipo', $rsTipoDiarias->getCampo('cod_tipo'));
                    $obTDiariasTipoDiaria->exclusao();

                    $obTDiariasTipoDiaria->setComplementoChave($stComplementoChaveTipoDiaria);

                    if (Sessao::getExcecao()->ocorreu()) {
                        break;
                    }
                }
            }
            $rsTipoDiarias->proximo();
        }

        //Verifica alteraçoes e inclusões de tipos diarias
        if (!Sessao::getExcecao()->ocorreu()) {
            foreach ($arTipoDiarias as $arTipoDiariasTemp) {

                if (Sessao::getExcecao()->ocorreu()) {
                    break;
                }

                //Verifica Norma
                $rsNorma    = new RecordSet();
                $arCodNorma = ltrim($arTipoDiariasTemp['stCodNorma'], 0);
                if($arCodNorma[0] == "/")
                    $arCodNorma = "0".$arCodNorma;
                $arCodNorma = explode("/",$arCodNorma);
                $inCodNorma = "";
                if (count($arCodNorma)>0) {
                    $obTNorma = new TNorma();
                    $stFiltroNorma = " WHERE num_norma = '".$arCodNorma[0]."' AND exercicio = '".$arCodNorma[1]."'";
                    $obTNorma->recuperaTodos($rsNorma, $stFiltroNorma);
                    if ($rsNorma->getNumLinhas() > 0) {
                        $inCodNorma  = $rsNorma->getCampo('cod_norma');
                    }
                }

                //Verifica Mascara Classificação - Rubrica Despesa
                $inCodConta   = "";
                $inExercicio  = "";
                if ($arTipoDiariasTemp['stMascClassificacao'] != "") {
                    $stFiltroOrcamentoClassificacaoDespesa = " AND mascara_classificacao like '".$arTipoDiariasTemp['stMascClassificacao']."%' AND exercicio = '".Sessao::getExercicio()."'";
                    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa();
                    $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento($rsClassificacaoDespesa, $stFiltroOrcamentoClassificacaoDespesa);
                    if ( $rsClassificacaoDespesa->getNumLinhas() > 0 ) {
                        $inCodConta          = $rsClassificacaoDespesa->getCampo('cod_conta');
                        $inExercicio         = $rsClassificacaoDespesa->getCampo('exercicio');
                    }
                }

                $obTDiariasTipoDiaria = new TDiariasTipoDiaria();

                if ($arTipoDiariasTemp['inCodTipo'] != "") {
                    $obTDiariasTipoDiaria->setDado('cod_tipo', $arTipoDiariasTemp['inCodTipo']);
                }

                $obTDiariasTipoDiaria->setDado('nom_tipo',    $arTipoDiariasTemp['stNomeTipoDiaria']);
                $obTDiariasTipoDiaria->setDado('valor',       number_format($arTipoDiariasTemp['flValorDiaria'], 2, ".", ""));
                $obTDiariasTipoDiaria->setDado('cod_norma',   $inCodNorma);
                $obTDiariasTipoDiaria->setDado('vigencia', $arTipoDiariasTemp['dtDataVigencia']);

                // if ($arTipoDiariasTemp['inCodTipo'] != "") {
                //     $obTDiariasTipoDiaria->alteracao();
                // } else {
                    $obTDiariasTipoDiaria->inclusao();
                // }
                $stFiltroTipoDiaria = " WHERE timestamp = (SELECT MAX(timestamp)
                                                             FROM diarias.tipo_diaria tp
                                                            WHERE tp.cod_tipo = tipo_diaria.cod_tipo
                                                          )
                                          AND cod_tipo = ".$obTDiariasTipoDiaria->getDado('cod_tipo');
                $obTDiariasTipoDiaria = new TDiariasTipoDiaria();
                $obTDiariasTipoDiaria->recuperaTodos($rsTipoDiariasTmp, $stFiltroTipoDiaria);

                if (!Sessao::getExcecao()->ocorreu()) {
                    // $obTDiariasTipoDiariaDespesa->setDado('cod_tipo',  $obTDiariasTipoDiaria->getDado('cod_tipo'));
                    // $obTDiariasTipoDiariaDespesa->exclusao();
                    if ($inCodConta != "") {
                        $obTDiariasTipoDiariaDespesa = new TDiariasTipoDiariaDespesa();
                        $obTDiariasTipoDiariaDespesa->setDado('cod_tipo',  $rsTipoDiariasTmp->getCampo('cod_tipo'));
                        $obTDiariasTipoDiariaDespesa->setDado('cod_conta', $inCodConta);
                        $obTDiariasTipoDiariaDespesa->setDado('exercicio', $inExercicio);
                        $obTDiariasTipoDiariaDespesa->setDado('timestamp', $rsTipoDiariasTmp->getCampo('timestamp'));
                        $obTDiariasTipoDiariaDespesa->inclusao();
                    }
                }

            }//foreach arTipoDiarias
        }//!Sessao::getExcecao()->ocorreu()

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgForm,"Configuração de Tipos de Diária concluído.","incluir","aviso", Sessao::getId(), "../");
    break;
}

?>
