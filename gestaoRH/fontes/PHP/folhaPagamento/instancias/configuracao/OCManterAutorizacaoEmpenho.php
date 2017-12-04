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
    * Oculto
    * Data de Criação: 10/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-25 12:22:44 -0300 (Ter, 25 Set 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

function processarForm()
{
    $stJs = "";

    if ($_REQUEST['stAcao'] == 'incluir') {
        Sessao::write("arConfiguracoesEmpenhos",array());
        Sessao::write("arConfiguracoesLLA"     ,array());
        Sessao::write("arConfiguracoesEvento"  ,array());
        Sessao::write("stOpcoes","");
        Sessao::write("stOpcoesConfiguracaoEvento","");

        return;
    }

    $stFiltro = " WHERE vigencia                  = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')
                    AND to_char(vigencia, 'yyyy') = '".Sessao::read('inExercicioVigencia')."'";
    $stOrdem  = " ORDER BY dt_vigencia DESC LIMIT 1";

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php");
    $obTFolhaPagamentoConfiguracaoEmpenho = new TFolhaPagamentoConfiguracaoEmpenho();
    $obTFolhaPagamentoConfiguracaoEmpenho->recuperaVigencias($rsConfiguracaoEmpenhoVigencias, $stFiltro, $stOrdem);

    include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenho.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento.class.php");

    $obTCGMCGM                                                  = new TCGMCGM();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho            = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao();
    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento();

    $stFiltro = " WHERE configuracao_autorizacao_empenho.vigencia  = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')
                    AND configuracao_autorizacao_empenho.exercicio = '".Sessao::read('inExercicioVigencia')."'
                    AND configuracao_autorizacao_empenho.timestamp = '".$rsConfiguracaoEmpenhoVigencias->getCampo('timestamp')."'";

    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->recuperaRelacionamento($rsConfiguracaoAutorizacaoEmpenho,$stFiltro);

    $arConfiguracoesEmpenhos = array();
    Sessao::remove("arConfiguracoesEmpenhos");

    while (!$rsConfiguracaoAutorizacaoEmpenho->eof()) {
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("cod_configuracao_autorizacao",$rsConfiguracaoAutorizacaoEmpenho->getCampo("cod_configuracao_autorizacao"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("exercicio",$rsConfiguracaoAutorizacaoEmpenho->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("timestamp",$rsConfiguracaoAutorizacaoEmpenho->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->recuperaPorChave($rsHistorico);

        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->setDado("cod_configuracao_autorizacao",$rsConfiguracaoAutorizacaoEmpenho->getCampo("cod_configuracao_autorizacao"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->setDado("exercicio",$rsConfiguracaoAutorizacaoEmpenho->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->setDado("timestamp",$rsConfiguracaoAutorizacaoEmpenho->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->recuperaPorChave($rsDescricao);

        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("cod_configuracao_autorizacao",$rsConfiguracaoAutorizacaoEmpenho->getCampo("cod_configuracao_autorizacao"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("exercicio",$rsConfiguracaoAutorizacaoEmpenho->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("timestamp",$rsConfiguracaoAutorizacaoEmpenho->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->recuperaPorChave($rsComplemento);

        $obTCGMCGM->setDado("numcgm",$rsConfiguracaoAutorizacaoEmpenho->getCampo("numcgm"));
        $obTCGMCGM->recuperaPorChave($rsCGM);

        $inId = count($arConfiguracoesEmpenhos)+1;
        $arConfiguracaoEmpenho                                        = array();
        $arConfiguracaoEmpenho["inCodConfiguracaoAutorizacaoEmpenho"] = $rsConfiguracaoAutorizacaoEmpenho->getCampo("cod_configuracao_autorizacao");
        $arConfiguracaoEmpenho["inId"]                                = $inId;
        $arConfiguracaoEmpenho["inNumCGM"]                            = $rsConfiguracaoAutorizacaoEmpenho->getCampo("numcgm");
        $arConfiguracaoEmpenho["stNomCGM"]                            = $rsCGM->getCampo("nom_cgm");
        $arConfiguracaoEmpenho["stDescricaoAutorizacao"]              = $rsDescricao->getCampo("descricao");
        $arConfiguracaoEmpenho["inCodHistoricoPadrao"]                = $rsHistorico->getCampo("cod_historico");
        $arConfiguracaoEmpenho["stDescricaoItemAutorizacao"]          = $rsConfiguracaoAutorizacaoEmpenho->getCampo("descricao_item");
        $arConfiguracaoEmpenho["stComplementoAutorizacao"]            = $rsComplemento->getCampo("complemento_item");
        $arConfiguracoesEmpenhos[]                                    = $arConfiguracaoEmpenho;
        $rsConfiguracaoAutorizacaoEmpenho->proximo();
    }
    Sessao::write("arConfiguracoesEmpenhos",$arConfiguracoesEmpenhos);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLla.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaLocal.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaLotacao.class.php");
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php");
    $obTOrganogramaOrgao = new TOrganogramaOrgao();
    $obRPessoalServidor  = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();
    $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor();
    $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo      = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo();
    $obTFolhaPagamentoConfiguracaoEmpenhoLla              = new TFolhaPagamentoConfiguracaoEmpenhoLla();
    $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal         = new TFolhaPagamentoConfiguracaoEmpenhoLlaLocal();
    $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao       = new TFolhaPagamentoConfiguracaoEmpenhoLlaLotacao();

    $stFiltro = " WHERE configuracao_empenho_lla.vigencia  = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')
                    AND configuracao_empenho_lla.exercicio = '".Sessao::read('inExercicioVigencia')."'
                    AND configuracao_empenho_lla.timestamp = '".$rsConfiguracaoEmpenhoVigencias->getCampo('timestamp')."'";

    $obTFolhaPagamentoConfiguracaoEmpenhoLla->recuperaRelacionamento($rsConfiguracaoLla, $stFiltro);

    $arConfiguracoesLLA = array();
    Sessao::remove("arConfiguracoesLLA");

    while (!$rsConfiguracaoLla->eof()) {
        $stFiltro  = " AND cod_configuracao_lla = ".$rsConfiguracaoLla->getCampo("cod_configuracao_lla");
        $stFiltro .= " AND exercicio = '".$rsConfiguracaoLla->getCampo("exercicio")."'";
        $stFiltro .= " AND timestamp = '".$rsConfiguracaoLla->getCampo("timestamp")."'";
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->recuperaRelacionamento($rsLocal,$stFiltro);
        $extra = "";
        if ($rsLocal->getNumLinhas() == 1) {
            Sessao::write("stOpcoes","local");
            $stOpcao     = "local";
            $stRotulo    = "Local ".$rsLocal->getCampo("descricao");
            $inCodigo    = $rsLocal->getCampo("cod_local");
            $inNumPAO    = $rsLocal->getCampo("num_pao");
            $stDescricao = $rsLocal->getCampo("descricao");
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->setDado("cod_configuracao_lla",$rsConfiguracaoLla->getCampo("cod_configuracao_lla"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->setDado("exercicio",$rsConfiguracaoLla->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->setDado("timestamp",$rsConfiguracaoLla->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->recuperaPorChave($rsLotacao);
        if ($rsLotacao->getNumLinhas() == 1) {
            Sessao::write("stOpcoes","lotacao");
            $obTOrganogramaOrgao->setDado('cod_orgao', $rsLotacao->getCampo("cod_orgao"));
            $obTOrganogramaOrgao->recuperaDadosUltimoOrgao($rsOrgao);

            $stOpcao     = "lotacao";
            $stRotulo    = "Lotação : ".$rsOrgao->getCampo("orgao")." - ".$rsOrgao->getCampo("descricao");
            $inCodigo    = $rsLotacao->getCampo("cod_orgao");
            $inNumPAO    = $rsLotacao->getCampo("num_pao");
            $stDescricao = $rsOrgao->getCampo("descricao");
            $extra       = $rsOrgao->getCampo("orgao");
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("cod_modulo",22);
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("cod_configuracao_lla",$rsConfiguracaoLla->getCampo("cod_configuracao_lla"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("exercicio",$rsConfiguracaoLla->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("timestamp",$rsConfiguracaoLla->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->recuperaPorChave($rsAtributos);
        if ($rsAtributos->getNumLinhas() == 1) {
            Sessao::write("stOpcoes","atributo");
            Sessao::write("inCodAtributo",$rsAtributos->getCampo("cod_atributo"));
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("cod_cadastro",$rsAtributos->getCampo("cod_cadastro"));
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("cod_atributo",$rsAtributos->getCampo("cod_atributo"));
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("cod_configuracao_lla",$rsAtributos->getCampo("cod_configuracao_lla"));
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("timestamp",$rsAtributos->getCampo("timestamp"));
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->recuperaPorChave($rsAtributosValor);

            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$rsAtributos->getCampo("cod_atributo")) );
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

            $stOpcao = "atributo";
            $stRotulo = "Atributo ".$rsAtributosSelecionados->getCampo("nom_atributo");
            $inCodigo = $rsAtributos->getCampo("cod_cadastro")."-".$rsAtributos->getCampo("cod_atributo");
            $inNumPAO = $rsAtributosValor->getCampo("num_pao");
            if ($rsAtributosSelecionados->getCampo("cod_tipo") == 3 or $rsAtributosSelecionados->getCampo("cod_tipo") == 4) {
                if ($rsAtributosSelecionados->getCampo("cod_tipo") == 3) {
                    include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
                    $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
                    $stFiltro  = " WHERE cod_modulo = 22";
                    $stFiltro .= "   AND cod_cadastro = ".$rsAtributos->getCampo("cod_cadastro");
                    $stFiltro .= "   AND cod_atributo = ".$rsAtributos->getCampo("cod_atributo");
                    $stFiltro .= "   AND cod_valor = ".$rsAtributosValor->getCampo("valor");
                    $obTAdministracaoAtributoValorPadrao->recuperaTodos($rsValor,$stFiltro);
                    $stDescricao = $rsValor->getCampo("valor_padrao");
                    $extra = $rsAtributosValor->getCampo("valor");
                }
                if ($rsAtributosSelecionados->getCampo("cod_tipo") == 4) {
                    $stDescricao = "";
                    $extra = array();
                    while (!$rsAtributosValor->eof()) {
                        $extra[] = $rsAtributosValor->getCampo("valor");
                        $rsAtributosValor->proximo();
                    }
                }
            } else {
                $stDescricao = $rsAtributosValor->getCampo("valor");
                $extra = $rsAtributosValor->getCampo("valor");
            }
        }

        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php");
        $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade();
        $obTOrcamentoProjetoAtividade->setDado("num_pao",$inNumPAO);
        $obTOrcamentoProjetoAtividade->setDado("exercicio",$rsConfiguracaoLla->getCampo("exercicio"));
        $stOrderBy = "
              ORDER BY acao.num_acao
                     , dotacao
        ";
        $obTOrcamentoProjetoAtividade->recuperaPorNumPAODotacao($rsPAO, "WHERE pao.exercicio = '".$rsConfiguracaoLla->getCampo("exercicio")."' AND pao.num_pao = ".$inNumPAO , $stOrderBy , $boTransacao);

        $arConfiguracaoLLA                      = array();
        $arConfiguracaoLLA["inId"]              = count($arConfiguracoesLLA)+1;
        $arConfiguracaoLLA["stOpcao"]           = $stOpcao;
        $arConfiguracaoLLA["rotulo"]            = $stRotulo;
        $arConfiguracaoLLA["codigo"]            = $inCodigo;
        $arConfiguracaoLLA["stHdnDotacao"]      = $rsPAO->getCampo("dotacao");
        $arConfiguracaoLLA["inNumPAO"]          = $rsPAO->getCampo("num_acao");
        $arConfiguracaoLLA["inHdnNumPAO"]       = $inNumPAO;
        $arConfiguracaoLLA["stNomPAO"]          = $rsPAO->getCampo("titulo");
        $arConfiguracaoLLA["descricao"]         = $stDescricao;
        $arConfiguracaoLLA["extra"]             = $extra;
        $arConfiguracoesLLA[]                   = $arConfiguracaoLLA;
        $rsConfiguracaoLla->proximo();
    }
    Sessao::write("arConfiguracoesLLA",$arConfiguracoesLLA);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoEvento.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoSubDivisao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoCargo.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoSituacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLocal.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLotacao.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoContaDespesa.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoAtributo.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoAtributoValor.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php");

    $obTFolhaPagamentoConfiguracaoEmpenho                 = new TFolhaPagamentoConfiguracaoEmpenho();
    $obTFolhaPagamentoConfiguracaoEmpenhoEvento           = new TFolhaPagamentoConfiguracaoEmpenhoEvento();
    $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao       = new TFolhaPagamentoConfiguracaoEmpenhoSubDivisao();
    $obTFolhaPagamentoConfiguracaoEmpenhoCargo            = new TFolhaPagamentoConfiguracaoEmpenhoCargo();
    $obTFolhaPagamentoConfiguracaoEmpenhoSituacao         = new TFolhaPagamentoConfiguracaoEmpenhoSituacao();
    $obTFolhaPagamentoConfiguracaoEmpenhoLocal            = new TFolhaPagamentoConfiguracaoEmpenhoLocal();
    $obTFolhaPagamentoConfiguracaoEmpenhoLotacao          = new TFolhaPagamentoConfiguracaoEmpenhoLotacao();
    $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa     = new TFolhaPagamentoConfiguracaoEmpenhoContaDespesa();
    $obTFolhaPagamentoConfiguracaoEmpenhoAtributo         = new TFolhaPagamentoConfiguracaoEmpenhoAtributo();
    $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor    = new TFolhaPagamentoConfiguracaoEmpenhoAtributoValor();
    $obTPessoalSubDivisao                                 = new TPessoalSubDivisao();

    $obTFolhaPagamentoConfiguracaoEmpenhoEvento->obTFolhaPagamentoConfiguracaoEmpenho                = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->obTFolhaPagamentoConfiguracaoEmpenho            = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoCargo->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoLocal->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->obTFolhaPagamentoConfiguracaoEmpenho               = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->obTFolhaPagamentoConfiguracaoEmpenho          = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
    $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->obTFolhaPagamentoConfiguracaoEmpenhoAtributo = &$obTFolhaPagamentoConfiguracaoEmpenhoAtributo;

    $stFiltro = " AND configuracao_empenho.vigencia          = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')
                  AND configuracao_empenho.exercicio         = '".Sessao::read('inExercicioVigencia')."'
                  AND configuracao_empenho.exercicio_pao     = '".Sessao::read('inExercicioVigencia')."'
                  AND configuracao_empenho.exercicio_despesa = '".Sessao::read('inExercicioVigencia')."'
                  AND configuracao_empenho.timestamp     = '".$rsConfiguracaoEmpenhoVigencias->getCampo('timestamp')."'";

    $obTFolhaPagamentoConfiguracaoEmpenho->recuperaRelacionamento($rsConfiguracoesEventos, $stFiltro);

    $arConfiguracoesEvento = array();
    Sessao::remove("arConfiguracoesEvento");

    while (!$rsConfiguracoesEventos->eof()) {
        $arConfiguracaoEvento = array();

        $obTFolhaPagamentoConfiguracaoEmpenho->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
        $obTFolhaPagamentoConfiguracaoEmpenho->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoEmpenho->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenho->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
        $obTFolhaPagamentoConfiguracaoEmpenho->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));

        $stConfiguracao = $rsConfiguracoesEventos->getCampo("cod_configuracao");

        $obTFolhaPagamentoConfiguracaoEmpenhoEvento->recuperaPorChave($rsEventos);
        $arCodEventos = array();

        while (!$rsEventos->eof()) {
            $arCodEventos[] = $rsEventos->getCampo("cod_evento");
            $rsEventos->proximo();
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->recuperaPorChave($rsSituacao);

        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->recuperaPorChave($rsSubDivisao);
        $arSubDivisao = array();
        $arRegime = array();
        while (!$rsSubDivisao->eof()) {
            $obTPessoalSubDivisao->setDado("cod_sub_divisao",$rsSubDivisao->getCampo("cod_sub_divisao"));
            $obTPessoalSubDivisao->recuperaPorChave($rsRegime);
            if (!in_array($rsRegime->getCampo("cod_regime"), $arRegime)) {
                $arRegime[]     = $rsRegime->getCampo("cod_regime");
            }
            if (!in_array($rsSubDivisao->getCampo("cod_sub_divisao"), $arSubDivisao)) {
                $arSubDivisao[] = $rsSubDivisao->getCampo("cod_sub_divisao");
            }
            $rsSubDivisao->proximo();
        }

        $arCargo = array();        
        foreach ($arSubDivisao as $subDivisao) {
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_sub_divisao",$subDivisao);        
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->recuperaPorChave($rsCargo);            
            foreach ($rsCargo->getElementos() as $value) {
                $arCargo[$subDivisao][] = $value['cod_cargo'];
            }
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLocal->recuperaPorChave($rsLocal);

        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->recuperaPorChave($rsLotacao);

        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("cod_configuracao",$rsConfiguracoesEventos->getCampo("cod_configuracao"));
        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("cod_evento",$rsConfiguracoesEventos->getCampo("cod_evento"));
        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("exercicio",$rsConfiguracoesEventos->getCampo("exercicio"));
        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("sequencia",$rsConfiguracoesEventos->getCampo("sequencia"));
        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("timestamp",$rsConfiguracoesEventos->getCampo("timestamp"));
        $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->recuperaPorChave($rsAtributosEventos);

        if ($rsLocal->getNumLinhas() > -1) {
            include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php");
            $obTOrganogramaLocal = new TOrganogramaLocal();
            $obTOrganogramaLocal->setDado("cod_local",$rsLocal->getCampo("cod_local"));
            $obTOrganogramaLocal->recuperaPorChave($rsLocal);

            Sessao::write("stOpcoesConfiguracaoEvento","local");
            $stOpcoesConfiguracaoEvento = "local";
            $stRotulo                   = "Local";
            $inCodigo                   = $rsLocal->getCampo("cod_local");
            $stDescricao                = $rsLocal->getCampo("descricao");
            $extra                      = $rsLocal->getCampo("cod_local");
        }
        if ($rsLotacao->getNumLinhas() > -1) {
            $obTOrganogramaOrgao->setDado('cod_orgao', $rsLotacao->getCampo("cod_orgao"));
            $obTOrganogramaOrgao->recuperaDadosUltimoOrgao($rsOrgao, $stFiltroOrgao);

            Sessao::write("stOpcoesConfiguracaoEvento","lotacao");
            $stOpcoesConfiguracaoEvento = "lotacao";
            $stRotulo                   = "Lotação";
            $inCodigo                   = $rsLotacao->getCampo("cod_orgao");
            $stDescricao                = $rsOrgao->getCampo("descricao");
            $extra                      = $rsOrgao->getCampo("orgao");
        }
        if ($rsAtributosEventos->getNumLinhas() > -1) {
            $extra = "";
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->setDado("cod_contrato",$rsAtributosEventos->getCampo("cod_contrato"));
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->setDado("cod_atributo",$rsAtributosEventos->getCampo("cod_atributo"));
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->setDado("timestamp",$rsAtributosEventos->getCampo("timestamp"));
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->recuperaPorChave($rsAtributosEventosValor);

            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$rsAtributosEventos->getCampo("cod_atributo")) );
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosEventosSelecionados );

            Sessao::write("stOpcoesConfiguracaoEvento","atributo");
            $stOpcoesConfiguracaoEvento = "atributo";
            $stRotulo = "Atributo ".$rsAtributosSelecionados->getCampo("nom_atributo");
            $inCodigo = $rsAtributosEventos->getCampo("cod_cadastro")."-".$rsAtributosEventos->getCampo("cod_atributo");
            if ($rsAtributosEventosSelecionados->getCampo("cod_tipo") == 3 or $rsAtributosEventosSelecionados->getCampo("cod_tipo") == 4) {
                if ($rsAtributosEventosSelecionados->getCampo("cod_tipo") == 3) {
                    include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
                    $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
                    $stFiltro  = " WHERE cod_modulo = 22";
                    $stFiltro .= "   AND cod_cadastro = ".$rsAtributosEventos->getCampo("cod_cadastro");
                    $stFiltro .= "   AND cod_atributo = ".$rsAtributosEventos->getCampo("cod_atributo");
                    $stFiltro .= "   AND cod_valor = ".$rsAtributosEventosValor->getCampo("valor");
                    $obTAdministracaoAtributoValorPadrao->recuperaTodos($rsValor,$stFiltro);
                    $stDescricao = $rsValor->getCampo("valor_padrao");
                    $extra = $rsAtributosEventosValor->getCampo("valor");
                }
                if ($rsAtributosEventosSelecionados->getCampo("cod_tipo") == 4) {
                    $extra = array();
                    while (!$rsAtributosEventosValor->eof()) {
                        $extra[] = $rsAtributosEventosValor->getCampo("valor");
                        $rsAtributosEventosValor->proximo();
                    }
                }
            } else {
                $stDescricao = $rsAtributosEventosValor->getCampo("valor");
                $extra = $rsAtributosEventosValor->getCampo("valor");
            }
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->recuperaPorChave($rsConfiguracoesContaDespesa);

        if ($rsConfiguracoesContaDespesa->getCampo('cod_conta') != "") {
            include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php");
            $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
            $stFiltro = " WHERE exercicio =  '".$rsConfiguracoesEventos->getCampo("exercicio_despesa")."'
                            AND cod_conta = ".$rsConfiguracoesContaDespesa->getCampo('cod_conta');
            $obTOrcamentoContaDespesa->recuperaTodos($rsContaDespesa, $stFiltro);
        } else {
            $rsContaDespesa = new RecordSet();
        }

        $arConfiguracaoEvento                                   = array();
        $arConfiguracaoEvento["inId"]                           = count($arConfiguracoesEvento)+1;
        $arConfiguracaoEvento["stOpcoesConfiguracaoEvento"]     = $stOpcoesConfiguracaoEvento;
        $arConfiguracaoEvento["stConfiguracao"]                 = $stConfiguracao;
        while (!$rsSituacao->eof()) {
            switch ($rsSituacao->getCampo("situacao")) {
                case "a":
                    $arConfiguracaoEvento["stSituacao1"] = $rsSituacao->getCampo("situacao");
                    break;
                case "o":
                    $arConfiguracaoEvento["stSituacao2"] = $rsSituacao->getCampo("situacao");
                    break;
                case "p":
                    $arConfiguracaoEvento["stSituacao3"] = $rsSituacao->getCampo("situacao");
                    break;
            }
            $rsSituacao->proximo();
        }

        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php");
        $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade();
        $stOrderBy = "
              ORDER BY acao.num_acao
                     , dotacao
        ";
        $obTOrcamentoProjetoAtividade->recuperaPorNumPAODotacao($rsPAO, "WHERE pao.exercicio = '".$rsConfiguracoesEventos->getCampo("exercicio_despesa")."' AND pao.num_pao =".$rsConfiguracoesEventos->getCampo("num_pao")." AND acao.num_acao = ".$rsConfiguracoesEventos->getCampo("num_acao") , $stOrderBy , $boTransacao);

        $arConfiguracaoEvento["stMascClassificacao"]      = $rsContaDespesa->getCampo("cod_estrutural");
        $arConfiguracaoEvento["stRubricaDespesa"]         = $rsContaDespesa->getCampo("descricao");
        $arConfiguracaoEvento["inCodDespesa"]             = $rsConfiguracoesEventos->getCampo("cod_despesa");
        $arConfiguracaoEvento["inCodContaDespesa"]        = $rsConfiguracoesContaDespesa->getCampo('cod_conta');
        $arConfiguracaoEvento["inExercicioDespesa"]       = $rsConfiguracoesEventos->getCampo("exercicio_despesa");
        $arConfiguracaoEvento["inCodRegime"]              = array_unique($arRegime);
        $arConfiguracaoEvento["inCodSubDivisao"]          = $arSubDivisao;
        $arConfiguracaoEvento["inCodCargo"]               = $arCargo;
        $arConfiguracaoEvento["inCodEventoSelecionados"]  = $arCodEventos;
        $arConfiguracaoEvento["rotulo"]                   = $stRotulo;
        $arConfiguracaoEvento["codigo"]                   = $inCodigo;
        $arConfiguracaoEvento["inNumPAO"]                 = $rsConfiguracoesEventos->getCampo("num_acao");
        $arConfiguracaoEvento["inHdnNumPAOEvento"]        = $rsConfiguracoesEventos->getCampo("num_pao");
        $arConfiguracaoEvento["stHdnDotacaoEvento"]       = $rsPAO->getCampo("dotacao");
        $arConfiguracaoEvento["stNomPAO"]                 = $rsConfiguracoesEventos->getCampo("nom_pao");
        $arConfiguracaoEvento["descricao"]                = $stDescricao;
        $arConfiguracaoEvento["extra"]                    = $extra;

        $arConfiguracoesEvento[]                          = $arConfiguracaoEvento;
        $rsConfiguracoesEventos->proximo();
    }

    if (trim(Sessao::read("stOpcoesConfiguracaoEvento")) != "" and trim(Sessao::read("stOpcoes")) == "") {
        Sessao::write("stOpcoes",Sessao::read("stOpcoesConfiguracaoEvento"));
    }
    if (trim(Sessao::read("stOpcoesConfiguracaoEvento")) == "" and trim(Sessao::read("stOpcoes"))  != "" ) {
        Sessao::write("stOpcoesConfiguracaoEvento",Sessao::read("stOpcoes"));
    }

    Sessao::write("arConfiguracoesEvento",$arConfiguracoesEvento);

    return $stJs;
}

########### ABA DADOS AUTORIZAÇÃO
function incluirAutorizacao()
{
    $arConfiguracoesEmpenhos = Sessao::read("arConfiguracoesEmpenhos");
    $inId = count($arConfiguracoesEmpenhos)+1;
    $arConfiguracaoEmpenho["inId"]                      = $inId;
    $arConfiguracaoEmpenho["inNumCGM"]                  = $_REQUEST["inNumCGM"];
    $arConfiguracaoEmpenho["stNomCGM"]                  = $_REQUEST["campoInner"];
    $arConfiguracaoEmpenho["stDescricaoAutorizacao"]    = $_REQUEST["stDescricaoAutorizacao"];
    $arConfiguracaoEmpenho["inCodHistoricoPadrao"]      = $_REQUEST["inCodHistoricoPadrao"];
    $arConfiguracaoEmpenho["stDescricaoItemAutorizacao"]= $_REQUEST["stDescricaoItemAutorizacao"];
    $arConfiguracaoEmpenho["stComplementoAutorizacao"]  = $_REQUEST["stComplementoAutorizacao"];
    $arConfiguracoesEmpenhos[]        = $arConfiguracaoEmpenho;
    Sessao::write("arConfiguracoesEmpenhos",$arConfiguracoesEmpenhos);

    $stJs  = montaAutorizacao();

    return $stJs;
}

function alterarAutorizacao()
{
    $arConfiguracoesEmpenhos                            = Sessao::read("arConfiguracoesEmpenhos");
    $arConfiguracaoEmpenho["inId"]                      = Sessao::read("inId");
    $arConfiguracaoEmpenho["inNumCGM"]                  = $_REQUEST["inNumCGM"];
    $arConfiguracaoEmpenho["stNomCGM"]                  = $_REQUEST["campoInner"];
    $arConfiguracaoEmpenho["stDescricaoAutorizacao"]    = $_REQUEST["stDescricaoAutorizacao"];
    $arConfiguracaoEmpenho["inCodHistoricoPadrao"]      = $_REQUEST["inCodHistoricoPadrao"];
    $arConfiguracaoEmpenho["stDescricaoItemAutorizacao"]= $_REQUEST["stDescricaoItemAutorizacao"];
    $arConfiguracaoEmpenho["stComplementoAutorizacao"]  = $_REQUEST["stComplementoAutorizacao"];
    $arConfiguracoesEmpenhos[Sessao::read("inId")-1] = $arConfiguracaoEmpenho;
    Sessao::write("arConfiguracoesEmpenhos",$arConfiguracoesEmpenhos);

    Sessao::write("inId","");
    $stJs  = montaAutorizacao();
    $stJs .= "f.btIncluirAutorizacao.disabled = false;\n";
    $stJs .= "f.btAlterarAutorizacao.disabled = true; \n";

    return $stJs;
}

function excluirAutorizacao()
{
    $arConfiguracoesEmpenhos = Sessao::read("arConfiguracoesEmpenhos");
    $arTemp = array();
    foreach ($arConfiguracoesEmpenhos as $arConfiguracaoEmpenho) {
        if ($arConfiguracaoEmpenho["inId"] != $_REQUEST["inId"]) {
            $arConfiguracaoEmpenho["inId"] = count($arTemp)+1;
            $arTemp[] = $arConfiguracaoEmpenho;
        }
    }
    Sessao::write("arConfiguracoesEmpenhos",$arTemp);
    $stJs  = montaAutorizacao();

    return $stJs;
}

function montaAutorizacao()
{
    $rsLista = new RecordSet();
    $arLista = Sessao::read("arConfiguracoesEmpenhos");
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Configurações da Aba Dados Autorização");

    $obLista->Head->addCabecalho("Descrição Item da Autorização",50);

    $obLista->Body->addCampo( 'stDescricaoItemAutorizacao', 'E' );

    $obLista->Body->addAcao("alterar","executaFuncaoAjax('%s','&inId=%s')",array('montaAlterarAutorizacao','inId'));
    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirAutorizacao','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs = "d.getElementById('spnConfiguracoesEmpenho').innerHTML = '$stHtml';";

    return $stJs;
}

function montaAlterarAutorizacao()
{
    Sessao::write("inId",$_REQUEST["inId"]);

    $arConfiguracoesEmpenhos = Sessao::read("arConfiguracoesEmpenhos");
    $arConfiguracaoEmpenho   = $arConfiguracoesEmpenhos[$_REQUEST["inId"]-1];

    $stJs  = "f.inNumCGM.value = '".$arConfiguracaoEmpenho["inNumCGM"]."';                                      \n";
    $stJs .= "f.campoInner.value = '".$arConfiguracaoEmpenho["stNomCGM"]."';                                    \n";
    $stJs .= "d.getElementById('campoInner').innerHTML = '".$arConfiguracaoEmpenho["stNomCGM"]."';              \n";
    $stJs .= "f.stDescricaoAutorizacao.value = '".$arConfiguracaoEmpenho["stDescricaoAutorizacao"]."';          \n";
    $stJs .= "f.inCodHistoricoPadrao.value = '".$arConfiguracaoEmpenho["inCodHistoricoPadrao"]."';              \n";
    $stJs .= "f.stDescricaoItemAutorizacao.value = '".$arConfiguracaoEmpenho["stDescricaoItemAutorizacao"]."';  \n";
    $stJs .= "f.stComplementoAutorizacao.value = '".$arConfiguracaoEmpenho["stComplementoAutorizacao"]."';      \n";
    $stJs .= "f.btIncluirAutorizacao.disabled = true;                                                           \n";
    $stJs .= "f.btAlterarAutorizacao.disabled = false;                                                          \n";

    return $stJs;
}

function buscaCGM()
{
    include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
    $stJs = '';
    $obTCGMCGM = new TCGMCGM();
    $obTCGMCGM->setDado("numcgm",$_REQUEST["inNumCGM"]);
    $obTCGMCGM->recuperaPorChave($rsCGM);
    if ($rsCGM->getNumLinhas() == 1) {
        $stNomCgm = $rsCGM->getCampo("nom_cgm");
        $inNumCgm = $_REQUEST["inNumCGM"];
    } else {
        $stNomCgm = "&nbsp;";
        $inNumCgm = "";
        $stJs .= "alertaAviso('Campo CGM Fornecedor inválido!(".$_REQUEST["inNumCGM"].")','form','erro','".Sessao::getId()."');\n";
    }
    $stJs .= "d.getElementById('campoInner').innerHTML = '$stNomCgm';";
    $stJs .= "f.campoInner.value = '".$stNomCgm."';";
    $stJs .= "f.inNumCGM.value = '".$inNumCgm."';";

    return $stJs;
}
########### ABA DADOS AUTORIZAÇÃO

########### ABA LOTAÇÃO/LOCAL/ATRIBUTO
function gerarSpanLLA()
{
    global $request;
    $stJs = "";

    $stOpcoes = ($request->get("stOpcoes") != "") ? $request->get("stOpcoes") : Sessao::read("stOpcoes");
    switch ($stOpcoes) {
        case "lotacao":
            $stHtml = gerarSpanLotacao();
            break;
        case "local":
            $stHtml = gerarSpanLocal();
            break;
        case "atributo":
            $stHtml = gerarSpanAtributo();
            break;
        default:
            $stJs = "f.stOpcoes.value = '';";
            $stHtml = "";
            break;
    }
    $stJs .= "d.getElementById('spnOpcoesConfiguracao').innerHTML = '$stHtml';\n";

    return $stJs;
}

function gerarSpanLotacao()
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IBuscaInnerLotacao.class.php' );
    include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpPAO.class.php' );

    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"	    );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $dtVigencia = Sessao::read('dtVigencia');

    $stFiltro  = " WHERE dt_inicial <= to_date('".$dtVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

    $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
    $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);

    $obIBuscaInnerLotacao = new IBuscaInnerLotacao( array('cod_organograma' => $rsOrganograma->getCampo('cod_organograma')) );
    $obIPopUpPAO          = new IPopUpPAO(array('exercicio'=>Sessao::read('inExercicioVigencia')));

    $obHdnNumPAO = new Hidden();
    $obHdnNumPAO->setId('inHdnNumPAO');
    $obHdnNumPAO->setName('inHdnNumPAO');
    
    $obHdnDotacao = new Hidden();
    $obHdnDotacao->setId('stHdnDotacao');
    $obHdnDotacao->setName('stHdnDotacao');
    
    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnNumPAO);
    $obFormulario->addHidden($obHdnDotacao);
    $obFormulario->addTitulo("Lotação");
    $obIBuscaInnerLotacao->geraFormulario($obFormulario);
    $obFormulario->addComponente($obIPopUpPAO);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function gerarSpanLocal()
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IBuscaInnerLocal.class.php' );
    include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpPAO.class.php' );
    $obIBuscaInnerLocal   = new IBuscaInnerLocal;
    $obIPopUpPAO          = new IPopUpPAO(array('exercicio'=>Sessao::read('inExercicioVigencia')));

    $obHdnNumPAO = new Hidden();
    $obHdnNumPAO->setId('inHdnNumPAO');
    $obHdnNumPAO->setName('inHdnNumPAO');
    
    $obHdnDotacao = new Hidden();
    $obHdnDotacao->setId('stHdnDotacao');
    $obHdnDotacao->setName('stHdnDotacao');
    
    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnNumPAO);
    $obFormulario->addHidden($obHdnDotacao);
    $obFormulario->addTitulo("Local");
    $obIBuscaInnerLocal->geraFormulario($obFormulario);
    $obFormulario->addComponente($obIPopUpPAO);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    //$stEval .= $obFormulario->obJavaScript->getInnerJavaScript();
    return $obFormulario->getHTML();
}

function gerarSpanAtributo()
{
    include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpPAO.class.php' );
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $obCmbAtributo = new Select();
    $obCmbAtributo->setRotulo("Atributo Dinâmico");
    $obCmbAtributo->setName("inCodAtributo");
    $obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
    $obCmbAtributo->setNullBarra(false);
    $obCmbAtributo->setCampoDesc("nom_atributo");
    $obCmbAtributo->setCampoId("cod_atributo");
    $obCmbAtributo->addOption("","Selecione");
    $obCmbAtributo->preencheCombo($rsAtributos);
    $obCmbAtributo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtributosDinamicos','inCodAtributo');");

    $obSpnAtributo = new Span();
    $obSpnAtributo->setId("spnAtributo");

    $arComponentesAtributos = array($obCmbAtributo);

    $obIPopUpPAO = new IPopUpPAO(array('exercicio'=>Sessao::read('inExercicioVigencia')));

    $obHdnNumPAO = new Hidden();
    $obHdnNumPAO->setId('inHdnNumPAO');
    $obHdnNumPAO->setName('inHdnNumPAO');
    
    $obHdnDotacao = new Hidden();
    $obHdnDotacao->setId('stHdnDotacao');
    $obHdnDotacao->setName('stHdnDotacao');
    
    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnNumPAO);
    $obFormulario->addHidden($obHdnDotacao);
    $obFormulario->addTitulo("Atributo Dinâmico");
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);
    $obFormulario->addComponente($obIPopUpPAO);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicos($inCodAtributo="",$stValor="")
{
    $stJs = '';
    $inCodAtributo = ($_REQUEST['inCodAtributo'] != "") ? $_REQUEST['inCodAtributo'] : $inCodAtributo;
    if ($inCodAtributo != "") {
        $rsAtributos = new RecordSet();
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$inCodAtributo) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
        if ($stValor!="") {
            $rsAtributos->setCampo("valor",$stValor,true);
            if (is_array($stValor)) {
                $stJs2 .= "var array = new Array();                         \n";
                $stJs2 .= "var campo= f.Atributo_".$inCodAtributo."_5_Disponiveis;\n";
                $stJs2 .= "var tam = campo.length;                          \n";
                foreach ($stValor as $inIndex=>$stTemp) {
                    $stJs2 .= "array[".$inIndex."] = '".$stTemp."';         \n";
                }
                $stJs2 .= "for (var i=0 ;i<tam;i++) {                         \n";
                $stJs2 .= "    for (var j=0 ;j<tam;j++) {                     \n";
                $stJs2 .= "        if (campo.options[i].value == array[j]) {  \n";
                $stJs2 .= "            campo.options[i].selected = true;    \n";
                $stJs2 .= "        }                                        \n";
                $stJs2 .= "    }                                            \n";
                $stJs2 .= "}                                                \n";
                $stJs2 .= "passaItem('document.frm.Atributo_".$inCodAtributo."_5_Disponiveis','document.frm.Atributo_".$inCodAtributo."_5_Selecionados','selecao');";
            }
        }

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastro");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs = "f.hdnOpcoesConfiguracao.value='".$obFormulario->getInnerJavaScript()."';";
    }
    $stJs .= "d.getElementById('spnAtributo').innerHTML = '$stHtml';   \n";
    $stJs .= $stJs2;

    return $stJs;
}

function validarLLA()
{
    $obErro = new Erro();
    switch ($_REQUEST["stOpcoes"]) {
        case "lotacao";
            if ($_REQUEST["HdninCodLotacao"] == "") {
                $obErro->setDescricao("Campo Lotação inválido!()");
            } else {
                $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
                if (is_array($arConfiguracoesLLA)) {
                    foreach ($arConfiguracoesLLA as $arConfiguracaoLLA) {
                        if ($arConfiguracaoLLA["codigo"] == $_REQUEST["HdninCodLotacao"] and $arConfiguracaoLLA["inId"] != Sessao::read("inId")) {
                            $obErro->setDescricao($obErro->getDescricao()."@A lotação informada já foi incluída na lista!()");
                        }
                    }
                }
            }
            break;
        case "local";
            if ($_REQUEST["inCodLocal"] == "") {
                $obErro->setDescricao("Campo Local inválido!()");
            } else {
                $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
                if (is_array($arConfiguracoesLLA)) {
                    foreach ($arConfiguracoesLLA as $arConfiguracaoLLA) {
                        if ($arConfiguracaoLLA["codigo"] == $_REQUEST["inCodLocal"] and $arConfiguracaoLLA["inId"] != Sessao::read("inId")) {
                            $obErro->setDescricao($obErro->getDescricao()."@O local informada já foi incluída na lista!()");
                        }
                    }
                }
            }
            break;
        case "atributo";
            if ($_REQUEST["inCodAtributo"] == "") {
                $obErro->setDescricao("Campo Atributo Dinâmico inválido!()");
            } else {
                $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
                if (is_array($arConfiguracoesLLA)) {
                    $stNomeAtributo = "Atributo_".$_REQUEST["inCodAtributo"]."_".$_REQUEST["inCodCadastro"];
                    foreach ($arConfiguracoesLLA as $arConfiguracaoLLA) {
                        if ($arConfiguracaoLLA["codigo"] == $_REQUEST["inCodCadastro"]."-".$_REQUEST["inCodAtributo"] and $arConfiguracaoLLA["extra"] == $_REQUEST[$stNomeAtributo] and $arConfiguracaoLLA["inId"] != Sessao::read("inId")) {
                            $obErro->setDescricao($obErro->getDescricao()."@O atributo informado já foi incluído na lista!()");
                        }
                    }
                }
            }
            $stNomeAtributo = "Atributo_".$_REQUEST["inCodAtributo"]."_".$_REQUEST["inCodCadastro"];
            if (Sessao::read($stNomeAtributo."_Selecionados") and $arConfiguracaoLLA["inId"] != Sessao::read("inId")) {
                $obErro->setDescricao($obErro->getDescricao()."@Esse atribudo dinâmico já foi inserido na lista, como se trata de um atributo do tipo seleção múltiplo só é possível a alteração do que já foi incluído na lista.");
            }
            break;
    }
    if ($_REQUEST["inNumPAO"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo PAO inválido!()");
    }

    return $obErro;
}

function processarInformacoesLLA()
{
    global $request;

    switch ($request->get("stOpcoes")) {
        case "lotacao";
            $arDados["inCodigo"]    = $request->get("HdninCodLotacao");
            $arDados["stDescricao"] = $request->get("stLotacao");
            $arDados["stRotulo"]    = "Lotação : ".$request->get("inCodLotacao")." - ".$request->get("stLotacao");
            $arDados["extra"]       = $request->get("inCodLotacao");
        break;

        case "local";
            $arDados["inCodigo"]    = $request->get("inCodLocal");
            $arDados["stDescricao"] = html_entity_decode($request->get("stLocal"));
            $arDados["stRotulo"]    = "Local ".$request->get("stLocal");
        break;

        case "atributo";
            include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
            $obRPessoalServidor = new RPessoalServidor();
            $obRPessoalServidor->addContratoServidor();
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$request->get('inCodAtributo')) );
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

            $stNomeAtributo = "Atributo_".$request->get("inCodAtributo")."_".$request->get("inCodCadastro");
            $arDados["inCodigo"] = $request->get("inCodCadastro")."-".$request->get("inCodAtributo");
            if ($rsAtributos->getCampo("cod_tipo") == 3 or $rsAtributos->getCampo("cod_tipo") == 4) {
                if ($rsAtributos->getCampo("cod_tipo") == 3) {
                    include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
                    $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
                    $stFiltro  = " WHERE cod_modulo = 22";
                    $stFiltro .= "   AND cod_cadastro = ".$request->get("inCodCadastro");
                    $stFiltro .= "   AND cod_atributo = ".$request->get("inCodAtributo");
                    $stFiltro .= "   AND cod_valor = ".$request->get("$stNomeAtributo");
                    $obTAdministracaoAtributoValorPadrao->recuperaTodos($rsValor,$stFiltro);

                    $arDados["stDescricao"] = $rsValor->getCampo("valor_padrao");
                    $arDados["extra"] = $request->get($stNomeAtributo);
                }
                if ($rsAtributos->getCampo("cod_tipo") == 4) {
                    $arDados["extra"] = $request->get($stNomeAtributo."_Selecionados");
                    Sessao::write($stNomeAtributo."_Selecionados",true);
                }
            } else {
                $arDados["stDescricao"] = $request->get($stNomeAtributo);
                $arDados["extra"]       = $request->get($stNomeAtributo);
            }

            $arDados["stRotulo"] = "Atributo ".$rsAtributos->getCampo("nom_atributo");
        break;
    }

    return $arDados;
}

function incluirLLA()
{
    $obErro = validarLLA();
    if (!$obErro->ocorreu()) {
        $arDados = processarInformacoesLLA();
        $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
        $arConfiguracaoLLA["inId"]              = count($arConfiguracoesLLA)+1;
        $arConfiguracaoLLA["stOpcao"]           = $_REQUEST["stOpcoes"];
        $arConfiguracaoLLA["rotulo"]            = $arDados["stRotulo"];
        $arConfiguracaoLLA["codigo"]            = $arDados["inCodigo"];
        $arConfiguracaoLLA["stHdnDotacao"]      = $_REQUEST["stHdnDotacao"];
        $arConfiguracaoLLA["inNumPAO"]          = $_REQUEST["inNumPAO"];
        $arConfiguracaoLLA["inHdnNumPAO"]       = $_REQUEST["inHdnNumPAO"];
        $arConfiguracaoLLA["stNomPAO"]          = $_REQUEST["campoInnerPAO"];
        $arConfiguracaoLLA["descricao"]         = $arDados["stDescricao"];
        $arConfiguracaoLLA["extra"]             = $arDados["extra"];
        $arConfiguracoesLLA[] = $arConfiguracaoLLA;
        Sessao::write("arConfiguracoesLLA",$arConfiguracoesLLA);
        $stJs  = montaLLA();
        $stJs .= gerarSpanLLA();
        if ($_REQUEST["stOpcoes"] == "atributo") {
            $arCodigo = explode("-",$arConfiguracaoLLA["codigo"]);
            $stJs .= "f.inCodAtributo.value = ".$arCodigo[1].";\n";
            $stJs .= "f.inCodAtributo.disabled = true;\n";
            $stJs .= gerarSpanAtributosDinamicos($arCodigo[1],"");
            $stJs .= "if (typeof f.Atributo_".$arCodigo[1]."_".$arCodigo[0]." != 'undefined') { f.Atributo_".$arCodigo[1]."_".$arCodigo[0].".value = '';}\n";
        }
        //$stJs .= "f.stOpcoes.disabled = true;\n";
        Sessao::write("stOpcoes",$_REQUEST["stOpcoes"]);
        $stJs .= desabilitaComboOpcoes(false);
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function alterarLLA()
{
    global $request;

    $obErro = validarLLA();
    if (!$obErro->ocorreu()) {
        $arDados = processarInformacoesLLA();

        $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
        $arConfiguracaoLLA["inId"]              = Sessao::read("inId");
        $arConfiguracaoLLA["stOpcao"]           = $request->get("stOpcoes");
        $arConfiguracaoLLA["rotulo"]            = html_entity_decode($arDados["stRotulo"]);
        $arConfiguracaoLLA["codigo"]            = $arDados["inCodigo"];
        $arConfiguracaoLLA["stHdnDotacao"]      = $request->get("stHdnDotacao");
        $arConfiguracaoLLA["inNumPAO"]          = $request->get("inNumPAO");
        $arConfiguracaoLLA["inHdnNumPAO"]       = $request->get("inHdnNumPAO");
        $arConfiguracaoLLA["stNomPAO"]          = html_entity_decode($request->get("campoInnerPAO"));
        $arConfiguracaoLLA["descricao"]         = html_entity_decode($arDados["stDescricao"]);
        $arConfiguracaoLLA["extra"]             = $arDados["extra"];
        $arConfiguracoesLLA[Sessao::read("inId")-1] = $arConfiguracaoLLA;

        Sessao::write("arConfiguracoesLLA",$arConfiguracoesLLA);
        $stJs  = montaLLA();
        $stJs .= gerarSpanLLA();
        if (Sessao::read("stOpcoes") == "atributo") {
            $arCodigo = explode("-",$arConfiguracaoLLA["codigo"]);
            $stJs .= "f.inCodAtributo.value = ".$arCodigo[1].";\n";
            $stJs .= "f.inCodAtributo.disabled = true;\n";
            $stJs .= gerarSpanAtributosDinamicos($arCodigo[1],"");
            $stJs .= "if (typeof f.Atributo_".$arCodigo[1]."_".$arCodigo[0]." != 'undefined') { f.Atributo_".$arCodigo[1]."_".$arCodigo[0].".value = '';}\n";
        }
        $stJs .= "f.obBtnIncluir.disabled = false;\n";
        $stJs .= "f.obBtnAlterar.disabled = true;\n";
        Sessao::write("inId","");
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirLLA()
{
    $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
    $arTemp = array();
    foreach ($arConfiguracoesLLA as $arConfiguracaoLLA) {
        if ($arConfiguracaoLLA["inId"] != $_REQUEST["inId"]) {
            $arConfiguracaoLLA["inId"] = count($arTemp)+1;
            $arTemp[] = $arConfiguracaoLLA;
        }
    }

    $stJs = limparLLA();
    Sessao::write("arConfiguracoesLLA",$arTemp);
    if (count($arTemp) == 0) {
        Sessao::write("stOpcoes","");
        $stJs .= gerarSpanComboOpcoes(false);

        if ( is_array(Sessao::read("arConfiguracoesEvento")) && count(Sessao::read("arConfiguracoesEvento")) == 0 ) {
            Sessao::write("stOpcoesConfiguracaoEvento", "");
            $stJs .= gerarSpanComboOpcoes(true);
        }
    }
    $stJs .= montaLLA();

    return $stJs;
}

function limparLLA()
{
    $stJs  = gerarSpanComboOpcoes(false);
    $stJs .= "jQuery('#obBtnIncluir').removeAttr('disabled');\n";
    $stJs .= "jQuery('#obBtnAlterar').attr('disabled', true);\n";

    return $stJs;
}

function montaAlterarLLA()
{
    global $request;

    Sessao::write("inId", $request->get("inId"));

    $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
    $arConfiguracaoLLA = $arConfiguracoesLLA[$request->get("inId")-1];
    //$stJs  = "f.stOpcoes.value = '".$arConfiguracaoLLA["stOpcao"]."';";
    $stJs  = "jQuery('#stOpcoes').val('".$arConfiguracaoLLA["stOpcao"]."');";
    switch ($arConfiguracaoLLA["stOpcao"]) {
        case "lotacao":
            $stHtml = gerarSpanLotacao();
            $stJs .= "d.getElementById('spnOpcoesConfiguracao').innerHTML = '$stHtml';\n";
            $stJs .= "d.getElementById('stLotacao').innerHTML = '".$arConfiguracaoLLA["descricao"]."'; \n";
            $stJs .= "f.inCodLotacao.value = '".$arConfiguracaoLLA["extra"]."'; \n";
            $stJs .= "f.HdninCodLotacao.value = '".$arConfiguracaoLLA["codigo"]."'; \n";
            $stJs .= "f.stLotacao.value = '".$arConfiguracaoLLA["descricao"]."'; \n";
            break;
        case "local":
            $stHtml = gerarSpanLocal();
            $stJs .= "d.getElementById('spnOpcoesConfiguracao').innerHTML = '$stHtml';\n";
            $stJs .= "d.getElementById('stLocal').innerHTML = '".$arConfiguracaoLLA["descricao"]."'; \n";
            $stJs .= "f.inCodLocal.value = '".$arConfiguracaoLLA["codigo"]."'; \n";
            $stJs .= "f.stLocal.value = '".$arConfiguracaoLLA["descricao"]."'; \n";
            break;
        case "atributo":
            //$stHtml = gerarSpanAtributo();
            //$stJs .= "d.getElementById('spnOpcoesConfiguracao').innerHTML = '$stHtml';\n";
            $arCodigo = explode("-",$arConfiguracaoLLA["codigo"]);
            $stJs .= "f.inCodAtributo.value = '".$arCodigo[1]."';\n";
            $stJs .= gerarSpanAtributosDinamicos($arCodigo[1],$arConfiguracaoLLA["extra"]);
            break;
    }

    $stJs .= "d.getElementById('campoInnerPAO').innerHTML = '".$arConfiguracaoLLA["stNomPAO"]."';\n";
    $stJs .= "f.campoInnerPAO.value = '".$arConfiguracaoLLA["stNomPAO"]."'; \n";
    $stJs .= "f.inNumPAO.value = '".$arConfiguracaoLLA["inNumPAO"]."'; \n";
    $stJs .= "f.inHdnNumPAO.value = '".$arConfiguracaoLLA["inHdnNumPAO"]."';";
    $stJs .= "f.stHdnDotacao.value = '".$arConfiguracaoLLA["stHdnDotacao"]."';";
    $stJs .= "f.obBtnIncluir.disabled = true;\n";
    $stJs .= "f.obBtnAlterar.disabled = false;\n";

    return $stJs;
}

function montaLLA()
{
    $rsLista = new RecordSet();
    $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
    $arLista = (is_array($arConfiguracoesLLA)) ? $arConfiguracoesLLA : array();
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Configurações da Aba Lotação/Local/Atributo");

    $obLista->Head->addCabecalho("Descrição",35);
    $obLista->Head->addCabecalho("PAO",30);

    $obLista->Body->addCampo( 'rotulo', 'E' );
    $obLista->Body->addCampo( '[stHdnDotacao] - [stNomPAO]', 'E' );

    $obLista->Body->addAcao("alterar","executaFuncaoAjax('%s','&inId=%s')",array('montaAlterarLLA','inId'));
    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirLLA','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();
    $stJs = "d.getElementById('spnConfiguracoesLLA').innerHTML = '$stHtml';";

    return $stJs;
}
########### ABA LOTAÇÃO/LOCAL/ATRIBUTO

########### ABA EVENTOS
function buscarEvento()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = " AND codigo = '".$_REQUEST["inCodigo"]."'";
    $obTFolhaPagamentoEvento->recuperaEventos($rsEvento,$stFiltro);
    if ($rsEvento->getNumLinhas() == 1) {
        $stDescEvento = $rsEvento->getCampo("descricao");
        $inCodigo = $rsEvento->getCampo("codigo");
    } else {
        $stDescEvento = "&nbsp;";
        $inCodigo = "";
    }
    $stJs  = "d.getElementById('inCampoInnerEvento').innerHTML = '$stDescEvento';       \n";
    $stJs .= "f.HdninCodigo.value = '$stDescEvento'; \n";
    $stJs .= "f.inCodigo.value = '$inCodigo';    \n";

    return $stJs;
}

function gerarSpanConfiguracaoEvento()
{
    global $request;

    $stJs = "";
    $stOpcoesConfiguracaoEvento = ($request->get("stOpcoesConfiguracaoEvento") != "") ? $request->get("stOpcoesConfiguracaoEvento") : Sessao::read("stOpcoesConfiguracaoEvento");

    if ($stOpcoesConfiguracaoEvento == "lotacao") {
        //Lotacao
        $stHtml = gerarSpanLotacaoEvento();
    } elseif ($stOpcoesConfiguracaoEvento == "local") {
        //Local
        $stHtml = gerarSpanLocalEvento();
    } elseif ($stOpcoesConfiguracaoEvento == "atributo") {
        //Atributo
        $stHtml = gerarSpanAtributoEvento();
    } else {
        //Default
        $stJs = "jQuery('#stOpcoesConfiguracaoEvento').val('');\n";
        $stHtml = "";
    }
    $stJs .= "d.getElementById('spnOpcoesConfiguracaoEventos').innerHTML = '$stHtml';\n";

    return $stJs;

}

function gerarSpanLotacaoEvento()
{
    include_once( CAM_GRH_PES_COMPONENTES.'IBuscaInnerLotacao.class.php'                );
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"	    );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $dtVigencia = Sessao::read('dtVigencia');

    $stFiltro  = " WHERE dt_inicial <= to_date('".$dtVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

    $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
    $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);

    $obIBuscaInnerLotacao = new IBuscaInnerLotacao( array('extensao'=>'Evento', 'cod_organograma' => $rsOrganograma->getCampo('cod_organograma')) );

    $stOnBlur = $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->getOnBlur();
    $stOnBlur .= "BloqueiaFrames(true,false);\n";
    $stOnBlur .= "montaParametrosGET('preencherEventos','stOpcoesConfiguracaoEvento,stConfiguracao,HdninCodLotacaoEvento');";
    $obIBuscaInnerLotacao->obBscLotacao->obCampoCod->obEvento->setOnBlur($stOnBlur);

    $obFormulario = new Formulario;
    $obIBuscaInnerLotacao->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();
}

function gerarSpanLocalEvento()
{
    include_once ( CAM_GRH_PES_COMPONENTES.'IBuscaInnerLocal.class.php' );
    $obIBuscaInnerLocal   = new IBuscaInnerLocal("Evento");
    $stOnBlur = $obIBuscaInnerLocal->obBscLocal->obCampoCod->obEvento->getOnBlur();
    $stOnBlur .= "BloqueiaFrames(true,false);\n";
    $stOnBlur .= "montaParametrosGET('preencherEventos','stOpcoesConfiguracaoEvento,stConfiguracao,inCodLocalEvento');";
    $obIBuscaInnerLocal->obBscLocal->obCampoCod->obEvento->setOnBlur($stOnBlur);

    $obFormulario = new Formulario;
    $obIBuscaInnerLocal->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanAtributoEvento()
{
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor();
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $obCmbAtributo = new Select();
    $obCmbAtributo->setRotulo("Atributo Dinâmico");
    $obCmbAtributo->setName("inCodAtributoEvento");
    $obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
    $obCmbAtributo->setNullBarra(false);
    $obCmbAtributo->setCampoDesc("nom_atributo");
    $obCmbAtributo->setCampoId("cod_atributo");
    $obCmbAtributo->addOption("","Selecione");
    $obCmbAtributo->preencheCombo($rsAtributos);
    $obCmbAtributo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtributosDinamicosEvento','inCodAtributoEvento');");

    $obSpnAtributo = new Span();
    $obSpnAtributo->setId("spnAtributoEvento");

    $arComponentesAtributos = array($obCmbAtributo);

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obCmbAtributo);
    $obFormulario->addSpan($obSpnAtributo);
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();

    return $obFormulario->getHTML();
}

function gerarSpanAtributosDinamicosEvento($inCodAtributo="",$stValor="")
{
    $inCodAtributo = ($_REQUEST['inCodAtributoEvento'] != "") ? $_REQUEST['inCodAtributoEvento'] : $inCodAtributo;
    $stJs = '';
    if ($inCodAtributo != "") {
        $rsAtributos = new RecordSet();
        include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor();
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$inCodAtributo) );
        $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

        if ($stValor!="") {
            $rsAtributos->setCampo("valor",$stValor,true);
            if (is_array($stValor)) {
                $stJs2 .= "var array = new Array();                         \n";
                $stJs2 .= "var campo= f.AtributoEvento_".$inCodAtributo."_5_Disponiveis;\n";
                $stJs2 .= "var tam = campo.length;                          \n";
                foreach ($stValor as $inIndex=>$stTemp) {
                    $stJs2 .= "array[".$inIndex."] = '".$stTemp."';         \n";
                }
                $stJs2 .= "for (var i=0 ;i<tam;i++) {                         \n";
                $stJs2 .= "    for (var j=0 ;j<tam;j++) {                     \n";
                $stJs2 .= "        if (campo.options[i].value == array[j]) {  \n";
                $stJs2 .= "            campo.options[i].selected = true;    \n";
                $stJs2 .= "        }                                        \n";
                $stJs2 .= "    }                                            \n";
                $stJs2 .= "}                                                \n";
                $stJs2 .= "passaItem('document.frm.AtributoEvento_".$inCodAtributo."_5_Disponiveis','document.frm.AtributoEvento_".$inCodAtributo."_5_Selecionados','selecao');";
            }
        }

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "AtributoEvento_"  );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obHdnCodCadastro = new hidden();
        $obHdnCodCadastro->setName("inCodCadastroEvento");
        $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

        $obFormulario = new Formulario();
        $obFormulario->addHidden($obHdnCodCadastro);
        $obMontaAtributos->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
        $stJs = "f.hdnOpcoesConfiguracaoEventos.value = f.hdnOpcoesConfiguracaoEventos.value + '".$obFormulario->getInnerJavaScript()."';";
    }
    $stJs .= "d.getElementById('spnAtributoEvento').innerHTML = '$stHtml';   \n";
    $stJs .= $stJs2;

    return $stJs;
}

function validarEvento(Request $request)
{
    $obErro = new Erro();
    $arInCodEventoSelecionados = $request->get('inCodEventoSelecionados');
    if (!is_array($arInCodEventoSelecionados) or $arInCodEventoSelecionados[0] == "") {
        $obErro->setDescricao("@Campo Eventos inválido!()");
    }
    if ($request->get("stConfiguracao") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Configuração inválido!()");
    }
    if ($request->get("inCodSubDivisaoSelecionados") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Subdivisão inválido!()");
    }
    if ($request->get("stSituacao1") == "" and $request->get("stSituacao2") == "" and $request->get("stSituacao3") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Situação inválido!()");
    }
    if ($request->get("stOpcoesConfiguracaoEvento") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Opções de Configuração inválido!()");
    } else {
        switch ($request->get("stOpcoesConfiguracaoEvento")) {
            case "lotacao";
                if ($request->get("HdninCodLotacaoEvento") == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Lotação inválido!()");
                }
                break;
            case "local";
                if ($request->get("inCodLocalEvento") == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Local inválido!()");
                }
                break;
            case "atributo";
                if ($request->get("inCodAtributoEvento") == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico inválido!()");
                }
                $stNomeAtributo = "AtributoEvento_".$request->get("inCodAtributoEvento")."_".$request->get("inCodCadastroEvento");
                if (Sessao::read($stNomeAtributo."_Selecionados") and $arConfiguracaoEvento["inId"] != Sessao::read("inId")) {
                    $obErro->setDescricao($obErro->getDescricao()."@Esse atribudo dinâmico já foi inserido na lista, como se trata de um atributo do tipo seleção múltiplo só é possível a alteração do que já foi incluído na lista.");
                }
                break;
        }
    }
    if ($request->get("inNumPAOEvento") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo PAO inválido!()");
    }
    if ($request->get("stMascClassificacao") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Rubrica de Despesa inválido!()");
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa.class.php");
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa = new FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa();
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("exercicio"     ,Sessao::read('inExercicioVigencia'));
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("num_pao"       ,$request->get("inNumPAOEvento"));
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("cod_estrutural",$request->get("stMascClassificacao"));
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->recuperaDespesaPorPAORubricaDespesa($rsDespesa,$stFiltro);
        if ($rsDespesa->getNumLinhas() == -1) {
            $obErro->setDescricao($obErro->getDescricao()."@A Rubrica de Despesa selecionada não possui despesa orçada.");
        }
    }
    if ($request->get("inCodDespesa") == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Dotação inválido!()");
    }

    return $obErro;
}

function carregaEvento()
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," ORDER BY descricao");
    while (!$rsEventos->eof()) {
        $stJs .= " jQuery('#inCodEventoDisponiveis').append(new Option('".$rsEventos->getCampo('descricao')."','".$rsEventos->getCampo('codigo')."') ); ";        
        $rsEventos->proximo();
    }

    return $stJs;
}

function processarInformacoesEvento(Request $request)
{
    Sessao::write("stOpcoesConfiguracaoEvento",$request->get("stOpcoesConfiguracaoEvento"));
    switch ($request->get("stOpcoesConfiguracaoEvento")) {
        case "lotacao";
            $arDados["inCodigo"] = $request->get("HdninCodLotacaoEvento");
            $arDados["stDescricao"] = $request->get("stLotacaoEvento");
            $arDados["stRotulo"] = "Lotação";
            $arDados["extra"] = $request->get("inCodLotacaoEvento");
            break;
        case "local";
            $arDados["inCodigo"] = $request->get("inCodLocalEvento");
            $arDados["stDescricao"] = $request->get("stLocalEvento");
            $arDados["stRotulo"] = "Local";
            $arDados["extra"] = $request->get("inCodLocalEvento");
            break;
        case "atributo";
            include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
            $obRPessoalServidor = new RPessoalServidor();
            $obRPessoalServidor->addContratoServidor();
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$request->get('inCodAtributoEvento')) );
            $obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

            $stNomeAtributo = "AtributoEvento_".$request->get("inCodAtributoEvento")."_".$request->get("inCodCadastroEvento");
            $arDados["inCodigo"] = $request->get("inCodCadastroEvento")."-".$request->get("inCodAtributoEvento");
            if ($rsAtributos->getCampo("cod_tipo") == 3 or $rsAtributos->getCampo("cod_tipo") == 4) {
                if ($rsAtributos->getCampo("cod_tipo") == 3) {
                    include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoValorPadrao.class.php");
                    $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
                    $stFiltro  = " WHERE cod_modulo = 22";
                    $stFiltro .= "   AND cod_cadastro = ".$request->get("inCodCadastroEvento");
                    $stFiltro .= "   AND cod_atributo = ".$request->get("inCodAtributoEvento");
                    $stFiltro .= "   AND cod_valor = ".$request->get("$stNomeAtributo");
                    $obTAdministracaoAtributoValorPadrao->recuperaTodos($rsValor,$stFiltro);
                    $arDados["stDescricao"] = $rsValor->getCampo("valor_padrao");
                    $arDados["extra"] = $request->get($stNomeAtributo);
                }
                if ($rsAtributos->getCampo("cod_tipo") == 4) {
                    $arDados["extra"] = $request->get($stNomeAtributo."_Selecionados");
                    Sessao::write($stNomeAtributo."_Selecionados",true);
                }
            } else {
                $arDados["stDescricao"] = $request->get($stNomeAtributo);
                $arDados["extra"] = $request->get($stNomeAtributo);
            }
            $arDados["stRotulo"] = "Atributo ".$rsAtributos->getCampo("nom_atributo");
            break;
    }

    return $arDados;
}

function incluirEvento(Request $request)
{
    $obErro = validarEvento($request);
    if (!$obErro->ocorreu()) {
        $arDados = processarInformacoesEvento($request);
        
        $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");
        $arConfiguracaoEvento["inId"]                       = count($arConfiguracoesEvento)+1;
        $arConfiguracaoEvento["stOpcoesConfiguracaoEvento"] = $request->get("stOpcoesConfiguracaoEvento");
        $arConfiguracaoEvento["inCodEventoSelecionados"]    = $request->get("inCodEventoSelecionados");
        $arConfiguracaoEvento["stConfiguracao"]             = $request->get("stConfiguracao");
        $arConfiguracaoEvento["stSituacao1"]                = $request->get("stSituacao1");
        $arConfiguracaoEvento["stSituacao2"]                = $request->get("stSituacao2");
        $arConfiguracaoEvento["stSituacao3"]                = $request->get("stSituacao3");
        $arConfiguracaoEvento["stMascClassificacao"]        = $request->get("stMascClassificacao");
        $arConfiguracaoEvento["stRubricaDespesa"]           = buscaRubrica($request->get("stMascClassificacao"));
        $arConfiguracaoEvento["inCodDespesa"]               = $request->get("inCodDespesa");

        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php");
        $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
        $stFiltro = " WHERE exercicio = '".Sessao::read('inExercicioVigencia')."' AND cod_estrutural = '".$request->get("stMascClassificacao")."'";
        $obTOrcamentoContaDespesa->recuperaTodos($rsContaDespesa, $stFiltro);

        $arConfiguracaoEvento["inCodContaDespesa"]          = $rsContaDespesa->getCampo('cod_conta');
        $arConfiguracaoEvento["inCodRegime"]                = $request->get("inCodRegimeSelecionados");
        $arConfiguracaoEvento["inCodSubDivisao"]            = $request->get("inCodSubDivisaoSelecionados");
        foreach ($request->get("inCodSubDivisaoSelecionados") as $key => $value) {
            $arConfiguracaoEvento["inCodCargo"][$key]       = $request->get("inCodCargoSelecionados");            
        }
        $arConfiguracaoEvento["rotulo"]                     = $arDados["stRotulo"];
        $arConfiguracaoEvento["codigo"]                     = $arDados["inCodigo"];
        $arConfiguracaoEvento["inNumPAO"]                   = $request->get("inNumPAOEvento");
        $arConfiguracaoEvento["inHdnNumPAOEvento"]          = $request->get("inHdnNumPAOEvento");
        $arConfiguracaoEvento["stHdnDotacaoEvento"]         = $request->get("stHdnDotacaoEvento");
        $arConfiguracaoEvento["stNomPAO"]                   = $request->get("campoInnerPAOEvento");
        $arConfiguracaoEvento["descricao"]                  = $arDados["stDescricao"];
        $arConfiguracaoEvento["extra"]                      = $arDados["extra"];

        $arConfiguracoesEvento[] = $arConfiguracaoEvento;
        Sessao::write('arConfiguracoesEvento',$arConfiguracoesEvento);
        $stJs  = montaEvento();
        $stJs .= gerarSpanConfiguracaoEvento();
        $stJs .= limparEvento();
        $stJs .= desabilitaComboOpcoes(true);
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }
    $stJs .= "LiberaFrames(true,true); \n";

    return $stJs;
}

function alterarEvento(Request $request)
{
    $obErro = validarEvento($request);
    if (!$obErro->ocorreu()) {
        $arDados = processarInformacoesEvento($request);
        $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");
        $arConfiguracaoEvento["inId"]                           = Sessao::read("inId");
        $arConfiguracaoEvento["stOpcoesConfiguracaoEvento"]     = $request->get("stOpcoesConfiguracaoEvento");
        $arConfiguracaoEvento["inCodEventoSelecionados"]        = $request->get("inCodEventoSelecionados");
        $arConfiguracaoEvento["stConfiguracao"]                 = $request->get("stConfiguracao");
        $arConfiguracaoEvento["stSituacao1"]                    = $request->get("stSituacao1");
        $arConfiguracaoEvento["stSituacao2"]                    = $request->get("stSituacao2");
        $arConfiguracaoEvento["stSituacao3"]                    = $request->get("stSituacao3");
        $arConfiguracaoEvento["stMascClassificacao"]            = $request->get("stMascClassificacao");
        $arConfiguracaoEvento["stRubricaDespesa"]               = buscaRubrica($request->get("stMascClassificacao"));
        $arConfiguracaoEvento["inCodDespesa"]                   = $request->get("inCodDespesa");

        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php");
        $obTOrcamentoContaDespesa = new TOrcamentoContaDespesa();
        $stFiltro = " WHERE exercicio = '".Sessao::read('inExercicioVigencia')."' AND cod_estrutural = '".$request->get("stMascClassificacao")."'";
        $obTOrcamentoContaDespesa->recuperaTodos($rsContaDespesa, $stFiltro);

        $arConfiguracaoEvento["inCodContaDespesa"]              = $rsContaDespesa->getCampo('cod_conta');
        $arConfiguracaoEvento["inCodRegime"]                    = $request->get("inCodRegimeSelecionados");
        $arConfiguracaoEvento["inCodSubDivisao"]                = $request->get("inCodSubDivisaoSelecionados");
        foreach ($request->get("inCodSubDivisaoSelecionados") as $key => $value) {
            $arConfiguracaoEvento["inCodCargo"][$key]           = $request->get("inCodCargoSelecionados");            
        }        
        $arConfiguracaoEvento["rotulo"]                         = $arDados["stRotulo"];
        $arConfiguracaoEvento["codigo"]                         = $arDados["inCodigo"];
        $arConfiguracaoEvento["inNumPAO"]                       = $request->get("inNumPAOEvento");
        $arConfiguracaoEvento["inHdnNumPAOEvento"]              = $request->get("inHdnNumPAOEvento");
        $arConfiguracaoEvento["stHdnDotacaoEvento"]             = $request->get("stHdnDotacaoEvento");
        $arConfiguracaoEvento["stNomPAO"]                       = $request->get("campoInnerPAOEvento");
        $arConfiguracaoEvento["descricao"]                      = $arDados["stDescricao"];
        $arConfiguracaoEvento["extra"]                          = $arDados["extra"];

        $arConfiguracoesEvento[Sessao::read("inId")-1] = $arConfiguracaoEvento;
        Sessao::write("arConfiguracoesEvento",$arConfiguracoesEvento);
        $stJs  = montaEvento();
        $stJs .= gerarSpanConfiguracaoEvento();
        $stJs .= limparEvento();
        Sessao::write("inId","");
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }
    $stJs .= "LiberaFrames(true,true); \n";

    return $stJs;
}

function excluirEvento()
{
    $arTemp = array();
    $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");
    foreach ($arConfiguracoesEvento as $arConfiguracaoEvento) {
        if ($arConfiguracaoEvento["inId"] != $_REQUEST["inId"]) {
            $arConfiguracaoEvento["inId"] = count($arTemp)+1;
            $arTemp[] = $arConfiguracaoEvento;
        }
    }
    Sessao::write("arConfiguracoesEvento",$arTemp);
    $stJs  = limparEvento();
    $stJs .= montaEvento();
    if (count($arTemp) == 0) {
        Sessao::write("stOpcoesConfiguracaoEvento","");
        $stJs .= gerarSpanComboOpcoes(true);

        if ( is_array(Sessao::read("arConfiguracoesLLA")) && count(Sessao::read("arConfiguracoesLLA")) == 0 ) {
            Sessao::write("stOpcoes", "");
            $stJs .= gerarSpanComboOpcoes(false);
        }
    }

    return $stJs;
}

function limparEvento()
{
    $stJs  = gerarSpanComboOpcoes(true);
    $stJs .= "jQuery('#stConfiguracao').val('');                                    \n";
    $stJs .= "jQuery('#obBtnIncluirEvento').removeAttr('disabled');                 \n";
    $stJs .= "jQuery('#obBtnAlterarEvento').attr('disabled', true);                 \n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveis,0);                              \n";
    $stJs .= "limpaSelect(f.inCodEventoSelecionados,0);                             \n";
    $stJs .= "f.stSituacao1.checked = false;                                        \n";
    $stJs .= "f.stSituacao2.checked = false;                                        \n";
    $stJs .= "f.stSituacao3.checked = false;                                        \n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoDisponiveis,0);                          \n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados,0);                         \n";
    $stJs .= "limpaSelect(f.inCodCargoDisponiveis,0);                               \n";
    $stJs .= "limpaSelect(f.inCodCargoSelecionados,0);                              \n";
    $stJs .= "passaItem('document.frm.inCodRegimeSelecionados','document.frm.inCodRegimeDisponiveis','tudo');\n";
    $stJs .= "d.getElementById('campoInnerPAOEvento').innerHTML = '&nbsp;';         \n";
    $stJs .= "f.campoInnerPAOEvento.value = '';                                     \n";
    $stJs .= "f.inNumPAOEvento.value = '';                                          \n";
    $stJs .= "f.inHdnNumPAOEvento.value = '';                                       \n";
    $stJs .= "f.stHdnDotacaoEvento.value = '';                                      \n";
    $stJs .= "d.getElementById('stRubricaDespesa').innerHTML = '&nbsp;';            \n";
    $stJs .= "f.stMascClassificacao.value = '';                                     \n";
    $stJs .= "f.HdnstMascClassificacao.value = '';                                  \n";
    $stJs .= "f.stRubricaDespesa.value = '';                                        \n";
    $stJs .= "limpaSelect(f.inCodDespesa,0);                                        \n";
    $stJs .= "f.inCodDespesa[0] = new Option('Selecione','','');                    \n";

    return $stJs;
}

function montaAlterarEvento()
{
    Sessao::write("inId",$_REQUEST["inId"]);

    $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");
    $arConfiguracaoEvento = $arConfiguracoesEvento[$_REQUEST["inId"]-1];

    $stJs  = "limpaSelect(f.inCodEventoDisponiveis,0);\n";
    $stJs .= "limpaSelect(f.inCodEventoSelecionados,0);\n";
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = "";
    $stEventosSelecionados = implode(",",$arConfiguracaoEvento["inCodEventoSelecionados"]);
    if (trim($stEventosSelecionados) != "") {
        $stFiltro = " WHERE cod_evento NOT IN (".$stEventosSelecionados.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," ORDER BY descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveis[".$inIndex."] = new Option('".$rsEventos->getCampo('codigo')."-".$rsEventos->getCampo("descricao")."','".$rsEventos->getCampo('cod_evento')."','');\n";
        $inIndex++;
        $rsEventos->proximo();
    }

    $inIndex = 0;

    if (trim($stEventosSelecionados) == "") {
        $stEventosSelecionados = "null";
    }
    $stFiltro = " WHERE cod_evento IN (".$stEventosSelecionados.")";
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," ORDER BY descricao");
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoSelecionados[".$inIndex."] = new Option('".$rsEventos->getCampo('codigo')."-".$rsEventos->getCampo("descricao")."','".$rsEventos->getCampo('cod_evento')."','');\n";
        $inIndex++;
        $rsEventos->proximo();
    }

    $stJs .= "f.stSituacao1.checked = false; ";
    $stJs .= "f.stSituacao2.checked = false; ";
    $stJs .= "f.stSituacao3.checked = false; ";
    $stJs .= "f.stConfiguracao.value = '".$arConfiguracaoEvento["stConfiguracao"]."';\n";
    $stJs .= ($arConfiguracaoEvento["stSituacao1"] == "a") ? "f.stSituacao1.checked = true;" : "";
    $stJs .= ($arConfiguracaoEvento["stSituacao2"] == "o") ? "f.stSituacao2.checked = true;" : "";
    $stJs .= ($arConfiguracaoEvento["stSituacao3"] == "p") ? "f.stSituacao3.checked = true;" : "";

    $stJs .= "limpaSelect(f.inCodSubDivisaoDisponiveis,0);                                                      \n";
    $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados,0);                                                     \n";
    $stJs .= "limpaSelect(f.inCodCargoSelecionados,0);                                                          \n";
    $stJs .= "passaItem('document.frm.inCodRegimeSelecionados','document.frm.inCodRegimeDisponiveis','tudo');   \n";
    $stJs .= passaItem("inCodRegimeDisponiveis","inCodRegimeSelecionados",$arConfiguracaoEvento["inCodRegime"]);
    $stJs .= preencherSubDivisao($arConfiguracaoEvento["inCodRegime"]);
    $stJs .= passaItem("inCodSubDivisaoDisponiveis","inCodSubDivisaoSelecionados",$arConfiguracaoEvento["inCodSubDivisao"]);
    $stJs .= preencherCargo();        
    if ( $arConfiguracaoEvento["inCodSubDivisao"][0] ) {
        if ( $arConfiguracaoEvento["inCodCargo"][0] ){
            $arCargosConfigurados = $arConfiguracaoEvento["inCodSubDivisao"][0];        
            $stJs .= passaItem("inCodCargoDisponiveis","inCodCargoSelecionados",$arConfiguracaoEvento["inCodCargo"][$arCargosConfigurados]);
        }
    }        
    $stJs .= "d.getElementById('campoInnerPAOEvento').innerHTML = '".$arConfiguracaoEvento["stNomPAO"]."';      \n";
    $stJs .= "f.campoInnerPAOEvento.value = '".$arConfiguracaoEvento["stNomPAO"]."';                            \n";
    $stJs .= "f.inNumPAOEvento.value = '".$arConfiguracaoEvento["inNumPAO"]."';                                 \n";
    $stJs .= "f.inHdnNumPAOEvento.value = '".$arConfiguracaoEvento["inHdnNumPAOEvento"]."';                     \n";
    $stJs .= "f.stHdnDotacaoEvento.value = '".$arConfiguracaoEvento["stHdnDotacaoEvento"]."';                   \n";
    $stJs .= "jQuery('#stOpcoesConfiguracaoEvento').val('".$arConfiguracaoEvento["stOpcoesConfiguracaoEvento"]."');\n";

    switch ($arConfiguracaoEvento["stOpcoesConfiguracaoEvento"]) {
        case "lotacao":
            $stHtml = gerarSpanLotacaoEvento();
            $stJs .= "d.getElementById('spnOpcoesConfiguracaoEventos').innerHTML = '$stHtml';                   \n";
            $stJs .= "d.getElementById('stLotacaoEvento').innerHTML = '".$arConfiguracaoEvento["descricao"]."'; \n";
            $stJs .= "f.stLotacaoEvento.value = '".$arConfiguracaoEvento["descricao"]."';                       \n";
            $stJs .= "f.HdninCodLotacaoEvento.value = '".$arConfiguracaoEvento["codigo"]."';                    \n";
            $stJs .= "f.inCodLotacaoEvento.value = '".$arConfiguracaoEvento["extra"]."';                        \n";
            break;
        case "local":
            $stHtml = gerarSpanLocalEvento();
            $stJs .= "d.getElementById('spnOpcoesConfiguracaoEventos').innerHTML = '$stHtml';                   \n";
            $stJs .= "d.getElementById('stLocalEvento').innerHTML = '".$arConfiguracaoEvento["descricao"]."';   \n";
            $stJs .= "f.stLocalEvento.value = '".$arConfiguracaoEvento["descricao"]."';                         \n";
            $stJs .= "f.inCodLocalEvento.value = '".$arConfiguracaoEvento["codigo"]."';                         \n";
            break;
        case "atributo":
            $stHtml = gerarSpanAtributoEvento();
            $stJs .= "d.getElementById('spnOpcoesConfiguracaoEventos').innerHTML = '$stHtml';                   \n";
            $arCodigo = explode("-",$arConfiguracaoEvento["codigo"]);
            $stJs .= "f.inCodAtributoEvento.value = '".$arCodigo[1]."';                                         \n";
            $stJs .= gerarSpanAtributosDinamicosEvento($arCodigo[1],$arConfiguracaoEvento["extra"]);
            break;
    }

    $stJs .= "d.getElementById('stRubricaDespesa').innerHTML = '".$arConfiguracaoEvento["stRubricaDespesa"]."'; \n";
    $stJs .= "f.stMascClassificacao.value = '".$arConfiguracaoEvento["stMascClassificacao"]."';                 \n";
    $stJs .= "f.HdnstMascClassificacao.value = '".$arConfiguracaoEvento["stMascClassificacao"]."';              \n";
    $stJs .= "f.stRubricaDespesa.value = '".$arConfiguracaoEvento["stRubricaDespesa"]."';                       \n";
    $_REQUEST["stMascClassificacao"] = $arConfiguracaoEvento["stMascClassificacao"];
    $_REQUEST["inNumPAOEvento"] = $arConfiguracaoEvento["inNumPAO"];
    $_REQUEST["stHdnDotacaoEvento"] = $arConfiguracaoEvento["stHdnDotacaoEvento"];
    $stJs .= preencherDotacao();
    $stJs .= "f.inCodDespesa.value = '".$arConfiguracaoEvento["inCodDespesa"]."';\n";

    $stJs .= "jQuery('#obBtnIncluirEvento').attr('disabled', true);\n";
    $stJs .= "jQuery('#obBtnAlterarEvento').removeAttr('disabled');\n";

    return $stJs;
}

function preencherCargo()
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php" );
    $obRPessoalCargo = new RPessoalCargo();
    $stJs  = "limpaSelect(f.inCodCargoDisponiveis,0); \n";            
    $obRPessoalCargo->listarCargo($rsCargos , $boTransacao);        
    while ( !$rsCargos->eof() ) {
        $stJs .= " jQuery('#inCodCargoDisponiveis').append(new Option('".trim($rsCargos->getCampo('descricao'))."','".$rsCargos->getCampo('cod_cargo')."') ); ";
        $rsCargos->proximo();
    }    
    return $stJs;
}


function montaEvento()
{
    $rsLista = new RecordSet();
    $arLista = Sessao::read("arConfiguracoesEvento");
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Configurações da Aba Evento");

    $obLista->Head->addCabecalho("Opção de Configuração",30);
    $obLista->Body->addCampo( '[rotulo]: [extra]-[descricao] - [stConfiguracao]', 'E' );
    $obLista->Head->addCabecalho("PAO",30);
    $obLista->Body->addCampo( '[stHdnDotacaoEvento] - [stNomPAO]', 'E' );
    $obLista->Head->addCabecalho("Rubrica de Despesa",30);
    $obLista->Body->addCampo( '[stMascClassificacao] - [stRubricaDespesa]', 'E' );
    $obLista->Body->addAcao("alterar","executaFuncaoAjax('%s','&inId=%s')",array('montaAlterarEvento','inId'));
    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirEvento','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();
    $stJs = "d.getElementById('spnConfiguracoesEventos').innerHTML = '$stHtml';";

    return $stJs;
}

function preencherSubDivisao($inCodRegimeSelecionados)
{
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"                                   );
    include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                                       );
    $stJs  = "limpaSelect(f.inCodSubDivisaoDisponiveis,0);                              \n";
    if ( is_array($inCodRegimeSelecionados) ) {
        foreach ($inCodRegimeSelecionados as $inCodRegime) {
            $stCodRegime .= $inCodRegime.",";
        }
        $stCodRegime = substr($stCodRegime,0,strlen($stCodRegime)-1);
        $obRPessoalSubDivisao = new RPessoalSubDivisao( new RPessoalRegime );
        $obRPessoalSubDivisao->listarSubDivisaoDeCodigosRegime($rsSubDivisao,$stCodRegime);
        $inIndex = 0;
        while ( !$rsSubDivisao->eof() ) {
            $stJs .= "f.inCodSubDivisaoDisponiveis[".$inIndex."] = new Option('".$rsSubDivisao->getCampo('nom_sub_divisao')."','".$rsSubDivisao->getCampo('cod_sub_divisao')."','');\n";
            $inIndex++;
            $rsSubDivisao->proximo();
        }
    }

    return $stJs;
}

function buscaRubrica($stMascara)
{
    include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );
    $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
    $obROrcamentoClassificacaoDespesa->setMascClassificacao($stMascara);
    $obROrcamentoClassificacaoDespesa->listar( $rsLista, " ORDER BY mascara_classificacao" );

    return $rsLista->getCampo("descricao");
}
########### ABA EVENTOS

function passaItem($DE,$PARA,$arSelecionados)
{
    $stJs  = "var array = new Array();                         \n";
    $stJs .= "var campo= f.".$DE.";                            \n";
    $stJs .= "var tam = campo.length;                          \n";
    foreach ($arSelecionados as $inIndex=>$stTemp) {
        $stJs .= "array[".$inIndex."] = '".$stTemp."';         \n";
    }
    $stJs .= "for (var i=0 ;i<tam;i++) {                         \n";
    $stJs .= "    for (var j=0 ;j<tam;j++) {                     \n";
    $stJs .= "        if (campo.options[i].value == array[j]) {  \n";
    $stJs .= "            campo.options[i].selected = true;    \n";
    $stJs .= "        }                                        \n";
    $stJs .= "    }                                            \n";
    $stJs .= "}                                                \n";
    $stJs .= "passaItem('document.frm.".$DE."','document.frm.".$PARA."','selecao');";

    return $stJs;
}

function gerarSpansAbas($boMontaAutorizacao = true)
{
    $stJs = '';

    if ($boMontaAutorizacao) {
        $stJs .= montaAutorizacao();
    }
    $stJs .= gerarSpanComboHistoricoPadrao();
    $stJs .= gerarSpanComboOpcoes(false);
    $stJs .= gerarSpanComboOpcoes(true);
    $stJs .= montaLLA();
    if (Sessao::read("stOpcoes") == "atributo") {
        $stJs .= "f.inCodAtributo.value = ".Sessao::read("inCodAtributo").";\n";
        $stJs .= "f.inCodAtributo.disabled = true;\n";
        $stJs .= gerarSpanAtributosDinamicos(Sessao::read("inCodAtributo"),"");
    }
    $stJs .= montaEvento();

    return $stJs;
}

function limparFiltro()
{
    $stJs  = gerarSpansAbas();
    $stJs .= limparLLA();
    $stJs .= limparEvento();

    return $stJs;
}

function preencherEventos()
{
    $stJs = '';
    switch ($_REQUEST["stOpcoesConfiguracaoEvento"]) {
        case "lotacao":
            $inCodigo = $_REQUEST["HdninCodLotacaoEvento"];
            break;
        case "local":
            $inCodigo = $_REQUEST["inCodLocalEvento"];
            break;
        case "atributo":
            $inCodigo = "0";
            break;
    }
    if ($_REQUEST["stConfiguracao"] != "" and $inCodigo != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
        $arData = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
        $dtCompetencia = $arData[2]."-".$arData[1]."-".$arData[0];

        $stJs .= "limpaSelect(f.inCodEventoDisponiveis,0);\n";
        $stJs .= "limpaSelect(f.inCodEventoSelecionados,0);\n";
        switch ($_REQUEST["stConfiguracao"]) {
            case "1":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoEventoCalculado->setDado("vigencia",$dtCompetencia);
                $obTFolhaPagamentoEventoCalculado->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                switch ($_REQUEST["stOpcoesConfiguracaoEvento"]) {
                    case "lotacao":
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_orgao",$inCodigo);
                        break;
                    case "local":
                        $obTFolhaPagamentoEventoCalculado->setDado("cod_local",$inCodigo);
                        break;
                }
                $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosAutorizacaoEmpenho($rsEventos);
                break;
            case "2":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();
                $obTFolhaPagamentoEventoFeriasCalculado->setDado("vigencia",$dtCompetencia);
                $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                switch ($_REQUEST["stOpcoesConfiguracaoEvento"]) {
                    case "lotacao":
                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_orgao",$inCodigo);
                        break;
                    case "local":
                        $obTFolhaPagamentoEventoFeriasCalculado->setDado("cod_local",$inCodigo);
                        break;
                }
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculadosAutorizacaoEmpenho($rsEventos);
                break;
            case "3":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
                $obTFolhaPagamentoEventoDecimoCalculado->setDado("vigencia",$dtCompetencia);
                $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                switch ($_REQUEST["stOpcoesConfiguracaoEvento"]) {
                    case "lotacao":
                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_orgao",$inCodigo);
                        break;
                    case "local":
                        $obTFolhaPagamentoEventoDecimoCalculado->setDado("cod_local",$inCodigo);
                        break;
                }
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosCalculadosAutorizacaoEmpenho($rsEventos);
                break;
            case "4":
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("vigencia",$dtCompetencia);
                $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                switch ($_REQUEST["stOpcoesConfiguracaoEvento"]) {
                    case "lotacao":
                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_orgao",$inCodigo);
                        break;
                    case "local":
                        $obTFolhaPagamentoEventoRescisaoCalculado->setDado("cod_local",$inCodigo);
                        break;
                }
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosAutorizacaoEmpenho($rsEventos);
                break;
            default:
                $rsEventos = new RecordSet();
                break;
        }
        $inIndex = 0;
        $stEventosSelecionados = "";
        while (!$rsEventos->eof()) {
            $stEventosSelecionados .= $rsEventos->getCampo("cod_evento").",";
            $stJs .= "f.inCodEventoSelecionados[".$inIndex."] = new Option('".$rsEventos->getCampo('codigo')."-".$rsEventos->getCampo("descricao")."','".$rsEventos->getCampo('cod_evento')."','');\n";
            $inIndex++;
            $rsEventos->proximo();
        }
        $stEventosSelecionados = substr($stEventosSelecionados,0,strlen($stEventosSelecionados)-1);

        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $stFiltro = "";
        if (trim($stEventosSelecionados) != "") {
            $stFiltro = " WHERE cod_evento NOT IN (".$stEventosSelecionados.")";
        }
        $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," ORDER BY descricao");
        $inIndex = 0;
        while (!$rsEventos->eof()) {
            $stJs .= "f.inCodEventoDisponiveis[".$inIndex."] = new Option('".$rsEventos->getCampo('codigo')."-".$rsEventos->getCampo("descricao")."','".$rsEventos->getCampo('cod_evento')."','');\n";
            $inIndex++;
            $rsEventos->proximo();
        }
    }
    $stJs .= " LiberaFrames(true, false);";

    return $stJs;
}

function preencherDotacao()
{
    $stJs  = "limpaSelect(f.inCodDespesa,0);\n";
    $stJs .= "f.inCodDespesa[0] = new Option('Selecione','');\n";
    if (trim($_REQUEST["stMascClassificacao"]) != "" and trim($_REQUEST["inNumPAOEvento"]) != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa.class.php");
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa = new FFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa();
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("exercicio"     ,Sessao::read('inExercicioVigencia'));
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("num_pao"       ,$_REQUEST["inNumPAOEvento"]);
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->setDado("cod_estrutural",$_REQUEST["stMascClassificacao"]);
        $obFFolhaPagamentoRecuperaDespesaPorPAORubricaDespesa->recuperaDespesaPorPAORubricaDespesa($rsDespesa);
        $inIndex = 1;

        while (!$rsDespesa->eof()) {
            $stDescricao = $rsDespesa->getCampo("descricao_conta").' / DEST. REC.: '.$rsDespesa->getCampo("cod_fonte").' - '.$rsDespesa->getCampo("descricao_recurso");
            $stJs .= "f.inCodDespesa[".$inIndex."] = new Option('".$rsDespesa->getCampo('cod_despesa')." - ".$stDescricao."','".$rsDespesa->getCampo('cod_despesa')."');\n";
            $inIndex++;
            $rsDespesa->proximo();
        }
    }

    return $stJs;
}

function preencheMascClassificacao()
{
    $stJs = '';
    $stMascClassificacao = $_REQUEST["stMascClassificacao"];
    if ($stMascClassificacao != "") {
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php");
        $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
        $obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascClassificacao );
        $obROrcamentoClassificacaoDespesa->setExercicio( Sessao::read('inExercicioVigencia') );
        $obROrcamentoClassificacaoDespesa->listar($rsClassificacaoDespesa);

        // Seta mascara completa para buscar dotação corretamente
        $_REQUEST["stMascClassificacao"] = $rsClassificacaoDespesa->getCampo("mascara_classificacao");

        $inNumLinhas = $rsClassificacaoDespesa->getNumLinhas();
        if ($inNumLinhas > 0) {
            $stDescricaoDespesa = $rsClassificacaoDespesa->getCampo("descricao");
            $stJs .= 'd.getElementById("stRubricaDespesa").innerHTML = "'.$stDescricaoDespesa.'";';
            $stJs .= preencherDotacao();
        } else {
            $stJs .= 'f.stMascClassificacao.value = "";';
            $stJs .= 'f.stMascClassificacao.focus();';
            $stJs .= 'd.getElementById("stRubricaDespesa").innerHTML = "&nbsp;";';
            if ($stMascClassificacao != "" && $inNumLinhas <= 0) {
                $stJs .= "alertaAviso('Valor inválido. (".$stMascClassificacao.")','form','erro','".Sessao::getId()."');\n";
            }
            $_REQUEST["stMascClassificacao"] = "";
            $stJs .= preencherDotacao();
        }
    }

    return $stJs;
}

function gerarSpanComboOpcoes($boAbaEventos=false)
{
    $stJs      = "";
    $stEventos = "";

    if ($boAbaEventos) {
        $stEventos = "ConfiguracaoEvento";
    }

    if ( Sessao::read("stOpcoes$stEventos") != "" ) {
        $stJs .= desabilitaComboOpcoes($boAbaEventos);
    } else {
        $stJs .= habilitaComboOpcoes($boAbaEventos);
    }

    if ($boAbaEventos) {
        $stJs .= gerarSpanConfiguracaoEvento();
    } else {
        $stJs .= gerarSpanLLA();
    }

    return $stJs;
}

function habilitaComboOpcoes($boAbaEventos=false)
{
    $stEventos = "";

    if ($boAbaEventos) {
        $stEventos = "ConfiguracaoEvento";
        $stTitulo  = "Selecione a opção para configuração do PAO e rubrica de despesa para o evento.";
    } else {
        $stTitulo  = "Selecione a opção para configuração da autorização de empenho.";
    }

    $obCmbOpcoes= new Select;
    $obCmbOpcoes->setRotulo       ( "Opções de Configuração"                               );
    $obCmbOpcoes->setName         ( "stOpcoes$stEventos"                                   );
    $obCmbOpcoes->setId           ( "stOpcoes$stEventos"                                   );
    $obCmbOpcoes->setStyle        ( "width: 200px"                                         );
    $obCmbOpcoes->setTitle        ( $stTitulo );
    $obCmbOpcoes->addOption       ( "", "Selecione"                                        );
    $obCmbOpcoes->addOption       ( "lotacao", "Lotação"                                   );
    $obCmbOpcoes->addOption       ( "local", "Local"                                       );
    $obCmbOpcoes->addOption       ( "atributo", "Atributo"                                 );

    if ($boAbaEventos) {
        $obCmbOpcoes->obEvento->setOnChange("montaParametrosGET('gerarSpanConfiguracaoEvento','stOpcoesConfiguracaoEvento',true);");
    } else {
        $obCmbOpcoes->obEvento->setOnChange("montaParametrosGET('gerarSpanLLA','stOpcoes',true);");
    }

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obCmbOpcoes);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs = "jQuery('#spnComboOpcoes$stEventos').html('$stHtml');";

    return $stJs;
}

function desabilitaComboOpcoes($boAbaEventos=false)
{
    $stEventos = "";

    if ($boAbaEventos) {
        $stEventos = "ConfiguracaoEvento";
        $stTitulo  = "Selecione a opção para configuração do PAO e rubrica de despesa para o evento.";
    } else {
        $stTitulo  = "Selecione a opção para configuração da autorização de empenho.";
    }

    $obHdnOpcoes = new Hidden;
    $obHdnOpcoes->setName("stOpcoes$stEventos");
    $obHdnOpcoes->setId  ("stOpcoes$stEventos");
    $obHdnOpcoes->setValue( Sessao::read("stOpcoes$stEventos") );

    $obLblOpcoes= new Label();
    $obLblOpcoes->setRotulo("Opções de Configuração");
    $obLblOpcoes->setName("lblOpcoes$stEventos");
    $obLblOpcoes->setId  ("lblOpcoes$stEventos");

    switch ( Sessao::read("stOpcoes$stEventos") ) {
        case "lotacao":
            $obLblOpcoes->setValue("Lotação");
            break;
        case "local":
            $obLblOpcoes->setValue("Local");
            break;
        case "atributo":
            $obLblOpcoes->setValue("Atributo");
            break;
    }

    $obFormulario = new Formulario();
    $obFormulario->addHidden($obHdnOpcoes);
    $obFormulario->addComponente($obLblOpcoes);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();

    $stJs = "jQuery('#spnComboOpcoes$stEventos').html('$stHtml');";

    return $stJs;
}

function atualizarLotacao()
{
    global $request;
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"	    );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $obTPessoalContratoServidorOrgao 	  = new TPessoalContratoServidorOrgao();
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

    $obTPessoalContratoServidorOrgao->recuperaDataPrimeiroCadastro($rsContratoServidorOrgao);

    $stJs = "";
    $stAcao = $request->get('stAcao');
    $obErro = new Erro();

    if (trim($_REQUEST["dtVigencia"])!="") {
        $stVigencia = $_REQUEST["dtVigencia"];
        $arDtVigencia = explode("/", $stVigencia);
        Sessao::write("inExercicioVigencia", $arDtVigencia[2]);
        Sessao::write("dtVigencia",$stVigencia);
    } else {
        $stVigencia = Sessao::read("dtVigencia");
    }

    $stFiltro  = " WHERE dt_inicial <= to_date('".$stVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

    if ($rsPeriodoMovimentacao->getNumLinhas() != -1) {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);

        if ($rsOrganograma->getNumLinhas() != -1) {
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        } else {
            $stFiltro  = " WHERE dt_inicial <= to_date('".$rsContratoServidorOrgao->getCampo("dt_cadastro")."','dd/mm/yyyy')	\n";
            $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1																\n";
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

            $obErro->setDescricao("Não existem servidores vinculados a nenhum orgão em ".$stVigencia.". A vigência informada deve ser a partir de ".$rsPeriodoMovimentacao->getCampo("dt_inicial").".");
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
            $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        }

        list($inDia,$inMes,$inAno)= explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;
    } else {
        $stFiltro  = " WHERE dt_inicial <= to_date('".$rsContratoServidorOrgao->getCampo("dt_cadastro")."','dd/mm/yyyy')	\n";
        $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1																\n";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);
        $obErro->setDescricao("Vigência anterior ao primeiro período de movimentação. A vigência informada deve ser a partir de ".$rsPeriodoMovimentacao->getCampo("dt_inicial").".");
    }

    if (!$obErro->ocorreu()) {
        Sessao::write("arConfiguracoesEmpenhos",array());
        Sessao::write("arConfiguracoesLLA",array());
        Sessao::write("arConfiguracoesEvento",array());
        Sessao::write("stOpcoes","");
        Sessao::write("stOpcoesConfiguracaoEvento", "");
        $stJs .= gerarSpansAbas(false);
        $stJs .= limparLLA();
        $stJs .= limparEvento();
    } else {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
        $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");

        list($inDia,$inMes,$inAno)= explode("/", $rsContratoServidorOrgao->getCampo("dt_cadastro"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;

        // Atualizando o componente na tela
        $arDtVigencia = explode("/", $rsPeriodoMovimentacao->getCampo("dt_inicial"));
        Sessao::write("inExercicioVigencia", $arDtVigencia[2]);
        Sessao::write('dtVigencia',$rsPeriodoMovimentacao->getCampo("dt_inicial"));

        $stJs .= "jQuery('#dtVigencia').val('".$rsPeriodoMovimentacao->getCampo("dt_inicial")."'); \n";
        $stJs .= gerarSpanComboHistoricoPadrao();
        $stJs .= " alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); \n";
    }

    return $stJs;
}

function gerarSpanComboHistoricoPadrao()
{
    $dtVigencia           = Sessao::read('dtVigencia');
    $arDtVigencia         = explode("/",$dtVigencia);
    $inExercicioVigencia  = $arDtVigencia[2];
    $inCodHistoricoPadrao = null;

    include_once(CAM_GF_EMP_MAPEAMENTO."TEmpenhoHistoricoEmpenho.class.php");
    $obTEmpenhoHistoricoEmpenho   = new TEmpenhoHistoricoEmpenho();
    $stFiltro = " WHERE exercicio = '".$inExercicioVigencia."'";
    $stOrdem  = " ORDER BY nom_historico ";
    $obTEmpenhoHistoricoEmpenho->recuperaTodos($rsHistorico,$stFiltro, $stOrdem);

    while (!$rsHistorico->eof()) {
        $stNomHistorico = str_replace("'","\'",$rsHistorico->getCampo('nom_historico'));
        $rsHistorico->setCampo('nom_historico', $stNomHistorico);
        $rsHistorico->proximo();
    }
    $rsHistorico->setPrimeiroElemento();

    $obCmbHistoricoPadrao = new Select;
    $obCmbHistoricoPadrao->setRotulo       ( "Histórico Padrão"              );
    $obCmbHistoricoPadrao->setName         ( "inCodHistoricoPadrao"          );
    $obCmbHistoricoPadrao->setId           ( "inCodHistoricoPadrao"          );
    $obCmbHistoricoPadrao->setStyle        ( "width: 200px"                  );
    $obCmbHistoricoPadrao->setTitle        ( "Selecione o histórico padrão." );
    $obCmbHistoricoPadrao->setCampoID      ( "cod_historico"                 );
    $obCmbHistoricoPadrao->setCampoDesc    ( "nom_historico"                 );
    $obCmbHistoricoPadrao->addOption       ( "", "Selecione"                 );
    $obCmbHistoricoPadrao->setValue        ( $inCodHistoricoPadrao           );
    $obCmbHistoricoPadrao->preencheCombo   ( $rsHistorico                    );

    $obFormulario = new Formulario();
    $obFormulario->addComponente($obCmbHistoricoPadrao);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs   = "jQuery('#spnCmbHistoricoPadrao').html('$stHtml');";

    return $stJs;

}

function submeter()
{
    $obErro = new Erro();
    $arConfiguracoesLLA    = Sessao::read("arConfiguracoesLLA");
    $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");

    if(count($arConfiguracoesLLA)    == 0 &&
       count($arConfiguracoesEvento) == 0){
       $obErro->setDescricao("Não foram realizadas configurações obrigatórias. É necessário que existam configurações na guia Local/Lotação/Atributos ou na guia Eventos");
    } else {
        $stJs = "parent.frames[2].Salvar();\n";
    }

    if ($obErro->ocorreu()) {
        $stJs = "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

switch ( $request->get("stCtrl") ) {
    case "incluirAutorizacao":
       $stJs = incluirAutorizacao();
       break;
    case "alterarAutorizacao":
       $stJs = alterarAutorizacao();
       break;
    case "excluirAutorizacao":
       $stJs = excluirAutorizacao();
       break;
    case "montaAlterarAutorizacao":
        $stJs = montaAlterarAutorizacao();
        break;
    case "buscaCGM":
        $stJs = buscaCGM();
        break;
    case "gerarSpanLLA":
        $stJs = gerarSpanLLA();
        break;
    case "gerarSpanAtributosDinamicos":
        $stJs = gerarSpanAtributosDinamicos();
        break;
    case "incluirLLA":
        $stJs = incluirLLA();
        break;
    case "alterarLLA":
        $stJs = alterarLLA();
        break;
    case "excluirLLA":
        $stJs = excluirLLA();
        break;
    case "limparLLA":
        $stJs = limparLLA();
        break;
    case "montaAlterarLLA":
        $stJs = montaAlterarLLA();
        break;
    case "buscarEvento":
        $stJs = buscarEvento();
        break;
    case "gerarSpanConfiguracaoEvento":
        $stJs = gerarSpanConfiguracaoEvento();
        break;
    case "gerarSpanAtributosDinamicosEvento":
        $stJs = gerarSpanAtributosDinamicosEvento();
        break;
    case "incluirEvento":
        $stJs = incluirEvento($request);
        break;
    case "alterarEvento":
        $stJs = alterarEvento($request);
        break;
    case "excluirEvento":
        $stJs = excluirEvento();
        break;
    case "limparEvento":
        $stJs = limparEvento();
        break;
    case "montaAlterarEvento":
        $stJs = montaAlterarEvento();
        break;
    case "gerarSpansAbas":
        $stJs = gerarSpansAbas();
        break;
    case "limparFiltro":
        $stJs = limparFiltro();
        break;
    case "preencherEventos":
        $stJs = preencherEventos();
        break;
    case "preencherDotacao":
        $stJs = preencherDotacao();
        break;
    case "preencheMascClassificacao":
        $stJs = preencheMascClassificacao();
        break;
    case "gerarSpanComboOpcoes":
        $stJs = gerarSpanComboOpcoes($_REQUEST['boAbaEventos']);
        break;
    case "habilitaComboOpcoes":
        $stJs = habilitaComboOpcoes($_REQUEST['boAbaEventos']);
        break;
    case "desabilitaComboOpcoes":
        $stJs = desabilitaComboOpcoes($_REQUEST['boAbaEventos']);
        break;
    case "processarForm":
        $stJs = processarForm($_REQUEST['stAcao']);
        break;
    case "atualizarLotacao":
        $stJs = atualizarLotacao();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "gerarSpanComboHistoricoPadrao":
        $stJs = gerarSpanComboHistoricoPadrao();
        break;
    case "carregaEvento":
        $stJs = carregaEvento();
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
