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
    * Processamento
    * Data de Criação: 13/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31015 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:02:38 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.05.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenho.class.php"           );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico.class.php"  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao.class.php"  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento.class.php");
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor.class.php"      );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo.class.php"           );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLla.class.php"                   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaLocal.class.php"              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLlaLotacao.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenho.class.php"                      );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoEvento.class.php"                );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoSubDivisao.class.php"            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoCargo.class.php"                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoSituacao.class.php"              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLocal.class.php"                 );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoLotacao.class.php"               );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoAtributo.class.php"              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoAtributoValor.class.php"         );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEmpenhoContaDespesa.class.php"          );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php"                                   );

$stPrograma = 'ManterAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$dtVigencia          = $_REQUEST['dtVigencia'];
$arDtVigencia        = explode("/", $dtVigencia);
$inExercicioVigencia = $arDtVigencia[2];

switch ($stAcao) {
    case "alterar":
    case "incluir":

        switch ($stAcao) {
            case "alterar":
                 $pgProx = $pgList;
                 break;
            default:
                 $pgProx = $pgForm;
                 break;
        }

        Sessao::setTrataExcecao(true);
        Sessao::getTransacao()->setMapeamento($obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho);
        $arConfiguracoesEmpenhos = Sessao::read("arConfiguracoesEmpenhos");
        if (is_array($arConfiguracoesEmpenhos) && count($arConfiguracoesEmpenhos) > 0) {
            foreach ($arConfiguracoesEmpenhos as $arConfiguracaoAutorizacao) {
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho            = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico();
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao();
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento();

                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho   = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho   = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;

                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("vigencia",$dtVigencia);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("exercicio",$inExercicioVigencia);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("cod_modalidade",8);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("numcgm",$arConfiguracaoAutorizacao["inNumCGM"]);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("complementar",false);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("descricao_item",$arConfiguracaoAutorizacao["stDescricaoItemAutorizacao"]);
                $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->inclusao();
                if ($arConfiguracaoAutorizacao["inCodHistoricoPadrao"] != "") {
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->setDado("cod_historico",$arConfiguracaoAutorizacao["inCodHistoricoPadrao"]);
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->inclusao();
                }
                if ($arConfiguracaoAutorizacao["stDescricaoAutorizacao"]) {
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->setDado("descricao",$arConfiguracaoAutorizacao["stDescricaoAutorizacao"]);
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->inclusao();
                }
                if ($arConfiguracaoAutorizacao["stComplementoAutorizacao"]) {
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->setDado("complemento_item",$arConfiguracaoAutorizacao["stComplementoAutorizacao"]);
                    $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->inclusao();
                }
                unset($obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho);
                unset($obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico);
                unset($obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao);
                unset($obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento);
            }
        }

        $arConfiguracoesLLA = Sessao::read("arConfiguracoesLLA");
        if (is_array($arConfiguracoesLLA) && count($arConfiguracoesLLA) > 0) {
            foreach ($arConfiguracoesLLA as $arConfiguracaoLLA) {
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor       = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor();
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo            = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo();
                $obTFolhaPagamentoConfiguracaoEmpenhoLla                    = new TFolhaPagamentoConfiguracaoEmpenhoLla();
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal               = new TFolhaPagamentoConfiguracaoEmpenhoLlaLocal();
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao             = new TFolhaPagamentoConfiguracaoEmpenhoLlaLotacao();

                $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->obTFolhaPagamentoConfiguracaoEmpenhoLla                 = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->obTFolhaPagamentoConfiguracaoEmpenhoLla               = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->obTFolhaPagamentoConfiguracaoEmpenhoLla              = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo = &$obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo;

                $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("vigencia",$dtVigencia);
                $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("exercicio",$inExercicioVigencia);
                $obTFolhaPagamentoConfiguracaoEmpenhoLla->inclusao();
                switch ($arConfiguracaoLLA["stOpcao"]) {
                    case "lotacao":
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->setDado("cod_orgao",$arConfiguracaoLLA["codigo"]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->setDado("num_pao",$arConfiguracaoLLA["inHdnNumPAO"]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->inclusao();
                        break;
                    case "local":
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->setDado("cod_local",$arConfiguracaoLLA["codigo"]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->setDado("num_pao",$arConfiguracaoLLA["inHdnNumPAO"]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->inclusao();
                        break;
                    case "atributo":
                        $arCodigo = explode("-",$arConfiguracaoLLA["codigo"]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("cod_cadastro",$arCodigo[0]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("cod_modulo",22);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->setDado("cod_atributo",$arCodigo[1]);
                        $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->inclusao();
                        if (is_array($arConfiguracaoLLA["extra"])) {
                            foreach ($arConfiguracaoLLA["extra"] as $inValor) {
                                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("valor",$inValor);
                                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("num_pao",$arConfiguracaoLLA["inHdnNumPAO"]);
                                $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->inclusao();
                            }
                        } else {
                            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("valor",$arConfiguracaoLLA["extra"]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->setDado("num_pao",$arConfiguracaoLLA["inHdnNumPAO"]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->inclusao();
                        }
                        break;
                }

                unset($obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor);
                unset($obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo);
                unset($obTFolhaPagamentoConfiguracaoEmpenhoLla);
                unset($obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal);
                unset($obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao);
            }
        }

        $arConfiguracoesEvento = Sessao::read("arConfiguracoesEvento");        
        if (is_array($arConfiguracoesEvento) && count($arConfiguracoesEvento) > 0) {
            foreach ($arConfiguracoesEvento as $arConfiguracaoEvento) {
                $arConfiguracoes = array();

                if ($arConfiguracaoEvento["stConfiguracao"] == "10") {
                    $arConfiguracoes   = array(1,2,3,4);
                } else {
                    $arConfiguracoes[] = $arConfiguracaoEvento["stConfiguracao"];
                }
                
                foreach ($arConfiguracoes as $inCodConfiguracao) {
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

                    $obTFolhaPagamentoConfiguracaoEmpenhoEvento->obTFolhaPagamentoConfiguracaoEmpenho                = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->obTFolhaPagamentoConfiguracaoEmpenho            = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoCargo->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoLocal->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->obTFolhaPagamentoConfiguracaoEmpenho               = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->obTFolhaPagamentoConfiguracaoEmpenho          = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
                    $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->obTFolhaPagamentoConfiguracaoEmpenhoAtributo = &$obTFolhaPagamentoConfiguracaoEmpenhoAtributo;

                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("vigencia",$dtVigencia);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("exercicio",$inExercicioVigencia);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("cod_configuracao",$inCodConfiguracao);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("exercicio_despesa",$inExercicioVigencia);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("cod_despesa",$arConfiguracaoEvento["inCodDespesa"]);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("num_pao",$arConfiguracaoEvento["inHdnNumPAOEvento"]);
                    $obTFolhaPagamentoConfiguracaoEmpenho->setDado("exercicio_pao",$inExercicioVigencia);
                    $obTFolhaPagamentoConfiguracaoEmpenho->inclusao();

                    $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->setDado("cod_conta",$arConfiguracaoEvento["inCodContaDespesa"]);
                    $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->inclusao();

                    for ($inIndex=1;$inIndex<=3;$inIndex++) {
                        if ($arConfiguracaoEvento["stSituacao".$inIndex] != "") {
                            $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->setDado("situacao",$arConfiguracaoEvento["stSituacao".$inIndex]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->inclusao();
                        }
                    }

                    foreach ($arConfiguracaoEvento["inCodEventoSelecionados"] as $inCodEvento) {
                        $obTFolhaPagamentoConfiguracaoEmpenhoEvento->setDado("cod_evento",$inCodEvento);
                        $obTFolhaPagamentoConfiguracaoEmpenhoEvento->inclusao();
                    }

                    foreach ($arConfiguracaoEvento["inCodSubDivisao"] as $inCodSubDivisao) {
                        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->setDado("cod_sub_divisao",$inCodSubDivisao);
                        $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->inclusao();                                                

                        if ($arConfiguracaoEvento['inCodCargo'][$inCodSubDivisao]) {
                            foreach ( $arConfiguracaoEvento['inCodCargo'][$inCodSubDivisao] as $inCodCargo ) {
                                $inSequencia = $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->getDado('sequencia');
                                $stTimestamp = $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->getDado('timestamp');
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_configuracao",$inCodConfiguracao);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("exercicio"       ,$inExercicioVigencia);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_sub_divisao" ,$inCodSubDivisao);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("sequencia"       ,$inSequencia);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("timestamp"       ,$stTimestamp);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_cargo"       ,$inCodCargo);
                                $obTFolhaPagamentoConfiguracaoEmpenhoCargo->inclusao();                            
                            }    
                        }
                        
                    }
                    switch ($arConfiguracaoEvento["stOpcoesConfiguracaoEvento"]) {
                        case "lotacao":
                            $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->setDado("cod_orgao",$arConfiguracaoEvento["codigo"]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->inclusao();
                            break;
                        case "local":
                            $obTFolhaPagamentoConfiguracaoEmpenhoLocal->setDado("cod_local",$arConfiguracaoEvento["codigo"]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoLocal->inclusao();
                            break;
                        case "atributo":
                            $arCodigo = explode("-",$arConfiguracaoEvento["codigo"]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("cod_cadastro",$arCodigo[0]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("cod_modulo",22);
                            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->setDado("cod_atributo",$arCodigo[1]);
                            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->inclusao();
                            if (is_array($arConfiguracaoEvento["extra"])) {
                                foreach ($arConfiguracaoEvento["extra"] as $inValor) {
                                    $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->setDado("valor",$inValor);
                                    $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->inclusao();
                                }
                            } else {
                                $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->setDado("valor",$arConfiguracaoEvento["extra"]);
                                $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->inclusao();
                            }
                            break;
                    }

                    unset($obTFolhaPagamentoConfiguracaoEmpenho);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoEvento);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoCargo);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoLocal);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoLotacao);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoAtributo);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor);
                    unset($obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa);
                }
            }
        }
        Sessao::encerraExcecao();
        $stMensagem = "Configuração de autorizaçao de empenho concluída.";
        sistemaLegado::alertaAviso($pgProx."?".Sessao::getId()."&stAcao=".$stAcao,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        break;

    case "excluir":

        Sessao::setTrataExcecao(true);
        $stFiltro = " WHERE vigencia  = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')
                        AND exercicio = '".$inExercicioVigencia."'";

        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
        $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->recuperaTodos($rsConfiguracaoAutorizacaoEmpenho, $stFiltro);

        while (!$rsConfiguracaoAutorizacaoEmpenho->eof()) {

            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho            = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenho();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao   = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento = new TFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento();

            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho   = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho   = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho = &$obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho;

            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("cod_configuracao_autorizacao",$rsConfiguracaoAutorizacaoEmpenho->getCampo('cod_configuracao_autorizacao'));
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("exercicio",$rsConfiguracaoAutorizacaoEmpenho->getCampo('exercicio'));
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->setDado("timestamp",$rsConfiguracaoAutorizacaoEmpenho->getCampo('timestamp'));

            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoComplemento->exclusao();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoDescricao->exclusao();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenhoHistorico->exclusao();
            $obTFolhaPagamentoConfiguracaoAutorizacaoEmpenho->exclusao();

            $stFiltroTimestamp = $stFiltro . " AND timestamp = '".$rsConfiguracaoAutorizacaoEmpenho->getCampo('timestamp')."'";

            $rsConfiguracaoAutorizacaoEmpenho->proximo();
        }

        $obTFolhaPagamentoConfiguracaoEmpenhoLla = new TFolhaPagamentoConfiguracaoEmpenhoLla();
        $obTFolhaPagamentoConfiguracaoEmpenhoLla->recuperaTodos($rsConfiguracaoEmpenhoLla, $stFiltro);
        while (!$rsConfiguracaoEmpenhoLla->eof()) {

            $obTFolhaPagamentoConfiguracaoEmpenhoLla              = new TFolhaPagamentoConfiguracaoEmpenhoLla();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo      = new TFolhaPagamentoConfiguracaoEmpenhoLlaAtributo();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal         = new TFolhaPagamentoConfiguracaoEmpenhoLlaLocal();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao       = new TFolhaPagamentoConfiguracaoEmpenhoLlaLotacao();

            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->obTFolhaPagamentoConfiguracaoEmpenhoLla                 = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->obTFolhaPagamentoConfiguracaoEmpenhoLla               = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->obTFolhaPagamentoConfiguracaoEmpenhoLla              = &$obTFolhaPagamentoConfiguracaoEmpenhoLla;
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo = &$obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo;

            $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("cod_configuracao_lla",$rsConfiguracaoEmpenhoLla->getCampo('cod_configuracao_lla'));
            $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("exercicio",$rsConfiguracaoEmpenhoLla->getCampo('exercicio'));
            $obTFolhaPagamentoConfiguracaoEmpenhoLla->setDado("timestamp",$rsConfiguracaoEmpenhoLla->getCampo('timestamp'));

            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributoValor->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaAtributo->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLocal->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLlaLotacao->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLla->exclusao();

            $rsConfiguracaoEmpenhoLla->proximo();
        }

        $obTFolhaPagamentoConfiguracaoEmpenho = new TFolhaPagamentoConfiguracaoEmpenho();
        $obTFolhaPagamentoConfiguracaoEmpenho->recuperaTodos($rsConfiguracaoEmpenho, $stFiltro);
        while (!$rsConfiguracaoEmpenho->eof()) {

            $obTFolhaPagamentoConfiguracaoEmpenho              = new TFolhaPagamentoConfiguracaoEmpenho();
            $obTFolhaPagamentoConfiguracaoEmpenhoEvento        = new TFolhaPagamentoConfiguracaoEmpenhoEvento();
            $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao    = new TFolhaPagamentoConfiguracaoEmpenhoSubDivisao();
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo         = new TFolhaPagamentoConfiguracaoEmpenhoCargo();
            $obTFolhaPagamentoConfiguracaoEmpenhoSituacao      = new TFolhaPagamentoConfiguracaoEmpenhoSituacao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLocal         = new TFolhaPagamentoConfiguracaoEmpenhoLocal();
            $obTFolhaPagamentoConfiguracaoEmpenhoLotacao       = new TFolhaPagamentoConfiguracaoEmpenhoLotacao();
            $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa  = new TFolhaPagamentoConfiguracaoEmpenhoContaDespesa();
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo      = new TFolhaPagamentoConfiguracaoEmpenhoAtributo();
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor = new TFolhaPagamentoConfiguracaoEmpenhoAtributoValor();

            $obTFolhaPagamentoConfiguracaoEmpenhoEvento->obTFolhaPagamentoConfiguracaoEmpenho                = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->obTFolhaPagamentoConfiguracaoEmpenho            = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoLocal->obTFolhaPagamentoConfiguracaoEmpenho                 = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->obTFolhaPagamentoConfiguracaoEmpenho               = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->obTFolhaPagamentoConfiguracaoEmpenho          = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->obTFolhaPagamentoConfiguracaoEmpenho              = &$obTFolhaPagamentoConfiguracaoEmpenho;
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->obTFolhaPagamentoConfiguracaoEmpenhoAtributo = &$obTFolhaPagamentoConfiguracaoEmpenhoAtributo;

            $obTFolhaPagamentoConfiguracaoEmpenho->setDado("cod_configuracao",$rsConfiguracaoEmpenho->getCampo('cod_configuracao'));
            $obTFolhaPagamentoConfiguracaoEmpenho->setDado("sequencia",$rsConfiguracaoEmpenho->getCampo('sequencia'));
            $obTFolhaPagamentoConfiguracaoEmpenho->setDado("exercicio",$rsConfiguracaoEmpenho->getCampo('exercicio'));
            $obTFolhaPagamentoConfiguracaoEmpenho->setDado("timestamp",$rsConfiguracaoEmpenho->getCampo('timestamp'));

            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("cod_configuracao",$rsConfiguracaoEmpenho->getCampo('cod_configuracao'));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("sequencia",$rsConfiguracaoEmpenho->getCampo('sequencia'));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("exercicio",$rsConfiguracaoEmpenho->getCampo('exercicio'));
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->setDado("timestamp",$rsConfiguracaoEmpenho->getCampo('timestamp'));

            $obTFolhaPagamentoConfiguracaoEmpenhoEvento->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoCargo->exclusao();            
            $obTFolhaPagamentoConfiguracaoEmpenhoSubDivisao->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoSituacao->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributoValor->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoAtributo->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLotacao->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoLocal->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenhoContaDespesa->exclusao();
            $obTFolhaPagamentoConfiguracaoEmpenho->exclusao();

            $rsConfiguracaoEmpenho->proximo();
        }

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração da Autorização de Empenho removida com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

?>
