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
  * Data de Criação: 21/01/2015

  * @author Analista: Ane Pereira
  * @author Desenvolvedor: Arthur Cruz

  * @ignore

  * $Id: PRManterConfiguracaoLeisLDO.php 61768 2015-03-03 13:08:43Z michel $

  * $Revision: $
  * $Name: $
  * $Author: $ 
  * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoLeisLDO.class.php");

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
        $rsTTCMGOConfiguracaoLeisLDO = new RecordSet();
        $obTTCMGOConfiguracaoLeisLDO = new TTCMGOConfiguracaoLeisLDO();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            // INCLUINDO LEIS DE CONSULTA
            $obTTCMGOConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
            $obTTCMGOConfiguracaoLeisLDO->setDado('tipo_configuracao',"consulta");
            $obTTCMGOConfiguracaoLeisLDO->setDado('status',true);
            $obTTCMGOConfiguracaoLeisLDO->recuperaPorChave($rsTTCMGOConfiguracaoLeisLDO,$boTransacao);

            while (!$rsTTCMGOConfiguracaoLeisLDO->eof()) {
                $obTTCMGOConfiguracaoLeisLDO = new TTCMGOConfiguracaoLeisLDO();
                $obTTCMGOConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                $obTTCMGOConfiguracaoLeisLDO->setDado('cod_norma',$rsTTCMGOConfiguracaoLeisLDO->getCampo('cod_norma'));
                $obTTCMGOConfiguracaoLeisLDO->setDado('tipo_configuracao',"consulta");
                $obTTCMGOConfiguracaoLeisLDO->setDado('status',false);
                
                $obErro = $obTTCMGOConfiguracaoLeisLDO->alteracao($boTransacao);

                $rsTTCMGOConfiguracaoLeisLDO->proximo();
            }

            $obTTCMGOConfiguracaoLeisLDO = new TTCMGOConfiguracaoLeisLDO();
            // INCLUINDO LEIS DE ALTERAÇÃO
            if (!$obErro->ocorreu()) {
                $arNormasRemovidos = Sessao::read('arNormasRemovido');
                if (count($arNormasRemovidos) > 0) {
                    foreach ($arNormasRemovidos as $arNormaRemovido) {
                        $obTTCMGOConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                        $obTTCMGOConfiguracaoLeisLDO->setDado('cod_norma',$arNormaRemovido['inCodNorma']);
                        $obTTCMGOConfiguracaoLeisLDO->setDado('tipo_configuracao',"alteracao");
                        $obTTCMGOConfiguracaoLeisLDO->setDado('status',false);
                        $obTTCMGOConfiguracaoLeisLDO->recuperaPorChave($rsTTCMGOConfiguracaoLeisLDO,$boTransacao);

                        if ($rsTTCMGOConfiguracaoLeisLDO->getNumLinhas() < 0)
                            $obErro = $obTTCMGOConfiguracaoLeisLDO->inclusao($boTransacao);
                        else
                            $obErro = $obTTCMGOConfiguracaoLeisLDO->alteracao($boTransacao);
                        
                        if ($obErro->ocorreu())
                            break;
                    }
                }
                
                if (!$obErro->ocorreu()) {
                    $arNormas = Sessao::read('arNormas');
                    if (count($arNormas) > 0) {
                        foreach ($arNormas as $arNorma) {
                            $obTTCMGOConfiguracaoLeisLDO->setDado('exercicio',Sessao::getExercicio());
                            $obTTCMGOConfiguracaoLeisLDO->setDado('cod_norma',$arNorma['inCodNorma']);
                            $obTTCMGOConfiguracaoLeisLDO->setDado('tipo_configuracao',"alteracao");
                            $obTTCMGOConfiguracaoLeisLDO->setDado('status',true);
                            $obTTCMGOConfiguracaoLeisLDO->recuperaPorChave($rsTTCMGOConfiguracaoLeisLDO,$boTransacao);
    
                            if ($rsTTCMGOConfiguracaoLeisLDO->getNumLinhas() < 0)
                                $obErro = $obTTCMGOConfiguracaoLeisLDO->inclusao($boTransacao);
                            else
                                $obErro = $obTTCMGOConfiguracaoLeisLDO->alteracao($boTransacao);
                            
                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Configuração Leis do LDO","incluir","aviso", Sessao::getId(), "../");
                Sessao::remove('inCodNormaSalvo');
                Sessao::remove('arNormas');
                Sessao::remove('arNormasRemovido');
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCMGOConfiguracaoLeisLDO);
        }

        break;
}
