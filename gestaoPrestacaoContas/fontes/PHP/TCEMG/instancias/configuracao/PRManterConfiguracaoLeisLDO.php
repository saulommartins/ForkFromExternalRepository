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
/**
  * Página de Processamento da Configuração de Leis do LDO
  * Data de Criação: 15/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  * $Id: PRManterConfiguracaoLeisLDO.php 59612 2014-09-02 12:00:51Z gelson $

  * $Revision: 59612 $
  * $Name: $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoLeisLDO.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoLeisLDO";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$stAcao = $request->get('stAcao');

$obErro = new Erro;

switch ($stAcao) {
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $rsTTCEMGConfiguracaoLeisLDO = new RecordSet();
        $obTTCEMGConfiguracaoLeisLDO = new TTCEMGConfiguracaoLeisLDO();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu) {
            // INCLUINDO LEIS DE CONSULTA
            if ($request->get('inCodLeiLDO') != '') {
                $obTTCEMGConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                $obTTCEMGConfiguracaoLeisLDO->setDado('cod_norma',$request->get('inCodLeiLDO'));
                $obTTCEMGConfiguracaoLeisLDO->setDado('tipo_configuracao',"consulta");
                $obTTCEMGConfiguracaoLeisLDO->setDado('status',true);
                $obTTCEMGConfiguracaoLeisLDO->recuperaPorChave($rsTTCEMGConfiguracaoLeisLDO,$boTransacao);

                $inCodNormaSalvo = Sessao::read('inCodNormaSalvo');
                if ($rsTTCEMGConfiguracaoLeisLDO->getCampo('cod_norma') != $inCodNormaSalvo) {
                    $obTTCEMGConfiguracaoLeisLDOAnterior = new TTCEMGConfiguracaoLeisLDO();
                    $obTTCEMGConfiguracaoLeisLDOAnterior->setDado('exercicio',Sessao::getExercicio());
                    $obTTCEMGConfiguracaoLeisLDOAnterior->setDado('cod_norma',$inCodNormaSalvo);
                    $obTTCEMGConfiguracaoLeisLDOAnterior->setDado('tipo_configuracao',"consulta");
                    $obTTCEMGConfiguracaoLeisLDOAnterior->setDado('status',false);
                    $obErro = $obTTCEMGConfiguracaoLeisLDOAnterior->alteracao($boTransacao);
                }

                if ($rsTTCEMGConfiguracaoLeisLDO->getNumLinhas() < 0) {
                    $obErro = $obTTCEMGConfiguracaoLeisLDO->inclusao($boTransacao);
                } else {
                    $obErro = $obTTCEMGConfiguracaoLeisLDO->alteracao($boTransacao);
                }

                // INCLUINDO LEIS DE ALTERAÇÃO
                if (!$obErro->ocorreu) {
                    $arNormas = Sessao::read('arNormas');
                    if (count($arNormas) > 0) {
                        foreach ($arNormas as $arNorma) {
                            $obTTCEMGConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                            $obTTCEMGConfiguracaoLeisLDO->setDado('cod_norma',$arNorma['inCodNorma']);
                            $obTTCEMGConfiguracaoLeisLDO->setDado('tipo_configuracao',"alteracao");
                            $obTTCEMGConfiguracaoLeisLDO->setDado('status',true);
                            $obTTCEMGConfiguracaoLeisLDO->recuperaPorChave($rsTTCEMGConfiguracaoLeisLDO,$boTransacao);

                            if ($rsTTCEMGConfiguracaoLeisLDO->getNumLinhas() < 0) {
                                $obErro = $obTTCEMGConfiguracaoLeisLDO->inclusao($boTransacao);
                            } else {
                                $obErro = $obTTCEMGConfiguracaoLeisLDO->alteracao($boTransacao);
                            }
                        }
                    }
                    $arNormasRemovidos = Sessao::read('arNormasRemovido');
                    if (count($arNormasRemovidos) > 0) {
                        foreach ($arNormasRemovidos as $arNormaRemovido) {
                            $obTTCEMGConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                            $obTTCEMGConfiguracaoLeisLDO->setDado('cod_norma',$arNorma['inCodNorma']);
                            $obTTCEMGConfiguracaoLeisLDO->setDado('tipo_configuracao',"alteracao");
                            $obTTCEMGConfiguracaoLeisLDO->setDado('status',false);
                            $obTTCEMGConfiguracaoLeisLDO->recuperaPorChave($rsTTCEMGConfiguracaoLeisLDO,$boTransacao);

                            if ($rsTTCEMGConfiguracaoLeisLDO->getNumLinhas() < 0) {
                                $obErro = $obTTCEMGConfiguracaoLeisLDO->inclusao($boTransacao);
                            } else {
                                $obErro = $obTTCEMGConfiguracaoLeisLDO->alteracao($boTransacao);
                            }
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Confiduração Leis do LDO","incluir","aviso", Sessao::getId(), "../");
                Sessao::remove('arNormas');
                Sessao::remove('arNormasRemovido');
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoLeisLDO);
        }

        break;
}
