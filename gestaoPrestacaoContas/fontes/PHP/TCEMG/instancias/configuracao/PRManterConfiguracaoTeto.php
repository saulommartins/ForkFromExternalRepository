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
    * Página de Processamento Configuração de Orgão
    * Data de Criação   : 14/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore

    * $Id: PRManterConfiguracaoTeto.php 65298 2016-05-10 18:53:52Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTetoRemuneratorio.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTetoRemuneratorioControle.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoTeto";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get("stAcao");

$stModulo = $request->get("hdnStModulo");
$arListaTetos = Sessao::read('arListaTetos');
$arListaTetosDelete = Sessao::read('arListaTetosDelete');

switch ($stAcao) {
    default:
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTTCEMGTetoRemuneratorio = new TTCEMGTetoRemuneratorio();
        $obTTCEMGTetoRemuneratorioControle = new TTCEMGTetoRemuneratorioControle();

        if (!$obErro->ocorreu()) {
            foreach ($arListaTetos as $chave => $teto) {

                if (trim($teto['cod_evento']) != "") {
                    $obErro = $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo::INTEGER = ".$teto['cod_evento']."::INTEGER", "", $boTransacao);
                    $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , $rsEvento->getCampo('cod_evento'));
                } else {
                    $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , trim($teto['cod_evento']));
                }
    
                $obTTCEMGTetoRemuneratorio->setDado("exercicio"    ,$teto['exercicio']);
                $obTTCEMGTetoRemuneratorio->setDado("cod_entidade" ,$request->get('hdnCodEntidade'));
                $obTTCEMGTetoRemuneratorio->setDado("vigencia"     ,$teto['vigencia']);
                $obTTCEMGTetoRemuneratorio->setDado("teto"         ,$teto['teto']);
                $obTTCEMGTetoRemuneratorio->setDado("justificativa",$teto['justificativa']);
                $obErro = $obTTCEMGTetoRemuneratorio->recuperaPorChave($rsTeto, $boTransacao);
                if (!$obErro->ocorreu()) {

                    if ($rsTeto->getNumLinhas() > 0) {
                        $obErro = $obTTCEMGTetoRemuneratorio->alteracao($boTransacao);

                        if (!$obErro->ocorreu()) {

                            if (($rsTeto->getCampo('cod_evento') != $teto['cod_evento']) ||
                                ($rsTeto->getCampo('justificativa') != $teto['justificativa']) ||
                                ($rsTeto->getCampo('teto') != SistemaLegado::formataValorDecimal($teto['teto']))
                               ) {
                                $obTTCEMGTetoRemuneratorioControle->setDado("exercicio"   ,$teto['exercicio']);
                                $obTTCEMGTetoRemuneratorioControle->setDado("cod_entidade",$request->get('hdnCodEntidade'));
                                $obTTCEMGTetoRemuneratorioControle->setDado("vigencia"    ,$teto['vigencia']);
                                $obTTCEMGTetoRemuneratorioControle->setDado("teto"        ,$teto['teto']);
                                $obErro = $obTTCEMGTetoRemuneratorioControle->recuperaPorChave($rsTetoControle, $boTransacao);
        
                                if (!$obErro->ocorreu()) {
        
                                    if ($rsTetoControle->getNumLinhas() > 0) {
                                        $obErro = $obTTCEMGTetoRemuneratorioControle->alteracao($boTransacao);
                                    } else {
                                        $obErro = $obTTCEMGTetoRemuneratorioControle->inclusao($boTransacao);
                                    }
                                }
                            }
                        }
                    } else {
                        $obErro = $obTTCEMGTetoRemuneratorio->inclusao($boTransacao);
                    }
                }

                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }

        if (!$obErro->ocorreu()) {
            if ($arListaTetosDelete != "") {
                foreach ($arListaTetosDelete as $chave => $teto) {
                    if (trim($teto['cod_evento']) != "") {
                        $obErro = $obTFolhaPagamentoEvento->recuperaTodos($rsEvento, " WHERE codigo::INTEGER = ".$teto['cod_evento']."::INTEGER", "", $boTransacao);
                        $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , $rsEvento->getCampo('cod_evento'));
                    } else {
                        $obTTCEMGTetoRemuneratorio->setDado("cod_evento" , trim($teto['cod_evento']));
                    }
    
                    $obTTCEMGTetoRemuneratorioControle->setDado("exercicio"   ,$teto['exercicio']);
                    $obTTCEMGTetoRemuneratorioControle->setDado("cod_entidade",$request->get('hdnCodEntidade'));
                    $obTTCEMGTetoRemuneratorioControle->setDado("vigencia"    ,$teto['vigencia']);
                    $obTTCEMGTetoRemuneratorioControle->setDado("teto"        ,$teto['teto']);
                    $obErro = $obTTCEMGTetoRemuneratorio->exclusao($boTransacao);
    
                    if (!$obErro->ocorreu()) {
                        $obTTCEMGTetoRemuneratorio->setDado("exercicio"    ,$teto['exercicio']);
                        $obTTCEMGTetoRemuneratorio->setDado("cod_entidade" ,$request->get('hdnCodEntidade'));
                        $obTTCEMGTetoRemuneratorio->setDado("vigencia"     ,$teto['vigencia']);
                        $obTTCEMGTetoRemuneratorio->setDado("teto"         ,$teto['teto']);
                        $obTTCEMGTetoRemuneratorio->setDado("justificativa",$teto['justificativa']);
                        $obErro = $obTTCEMGTetoRemuneratorio->exclusao($boTransacao);
                    }
    
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
        }

        if(!$obErro->ocorreu()){
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGTetoRemuneratorio);
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}
?>
