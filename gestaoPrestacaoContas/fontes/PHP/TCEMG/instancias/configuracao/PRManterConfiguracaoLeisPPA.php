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
  * Página de Processamento da Configuração de Leis do PPA
  * Data de Criação: 14/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: $

  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoLeisPPA.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoLeisPPA";
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
        $rsTTCEMGConfiguracaoLeisPPA = new RecordSet();
        $obTTCEMGConfiguracaoLeisPPA = new TTCEMGConfiguracaoLeisPPA();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu) {
            // INCLUINDO LEIS DE CONSULTA
            if ($request->get('inCodLeiPPA') != '') {
                $obTTCEMGConfiguracaoLeisPPA->setDado('exercicio',Sessao::getExercicio());
                $obTTCEMGConfiguracaoLeisPPA->setDado('cod_norma',$request->get('inCodLeiPPA'));
                $obTTCEMGConfiguracaoLeisPPA->setDado('tipo_configuracao',"consulta");
                $obTTCEMGConfiguracaoLeisPPA->setDado('status',true);
                $obTTCEMGConfiguracaoLeisPPA->recuperaPorChave($rsTTCEMGConfiguracaoLeisPPA,$boTransacao);

                $inCodNormaSalvo = Sessao::read('inCodNormaSalvo');
                if ($rsTTCEMGConfiguracaoLeisPPA->getCampo('cod_norma') != $inCodNormaSalvo) {
                    $obTTCEMGConfiguracaoLeisPPAAnterior = new TTCEMGConfiguracaoLeisPPA();
                    $obTTCEMGConfiguracaoLeisPPAAnterior->setDado('exercicio',Sessao::getExercicio());
                    $obTTCEMGConfiguracaoLeisPPAAnterior->setDado('cod_norma',$inCodNormaSalvo);
                    $obTTCEMGConfiguracaoLeisPPAAnterior->setDado('tipo_configuracao',"consulta");
                    $obTTCEMGConfiguracaoLeisPPAAnterior->setDado('status',false);
                    $obErro = $obTTCEMGConfiguracaoLeisPPAAnterior->alteracao($boTransacao);
                }

                if ($rsTTCEMGConfiguracaoLeisPPA->getNumLinhas() < 0) {
                    $obErro = $obTTCEMGConfiguracaoLeisPPA->inclusao($boTransacao);
                } else {
                    $obErro = $obTTCEMGConfiguracaoLeisPPA->alteracao($boTransacao);
                }

                // INCLUINDO LEIS DE ALTERAÇÃO
                if (!$obErro->ocorreu) {
                    $arNormas = Sessao::read('arNormas');
                    if (count($arNormas) > 0) {
                        foreach ($arNormas as $arNorma) {
                            $obTTCEMGConfiguracaoLeisPPA->setDado('exercicio',Sessao::getExercicio());
                            $obTTCEMGConfiguracaoLeisPPA->setDado('cod_norma',$arNorma['inCodNorma']);
                            $obTTCEMGConfiguracaoLeisPPA->setDado('tipo_configuracao',"alteracao");
                            $obTTCEMGConfiguracaoLeisPPA->setDado('status',true);
                            $obTTCEMGConfiguracaoLeisPPA->recuperaPorChave($rsTTCEMGConfiguracaoLeisPPA,$boTransacao);

                            if ($rsTTCEMGConfiguracaoLeisPPA->getNumLinhas() < 0) {
                                $obErro = $obTTCEMGConfiguracaoLeisPPA->inclusao($boTransacao);
                            } else {
                                $obErro = $obTTCEMGConfiguracaoLeisPPA->alteracao($boTransacao);
                            }
                        }
                    }
                    $arNormasRemovidos = Sessao::read('arNormasRemovido');
                    if (count($arNormasRemovidos) > 0) {
                        foreach ($arNormasRemovidos as $arNormaRemovido) {
                            $obTTCEMGConfiguracaoLeisPPA->setDado('exercicio',Sessao::getExercicio());
                            $obTTCEMGConfiguracaoLeisPPA->setDado('cod_norma',$arNorma['inCodNorma']);
                            $obTTCEMGConfiguracaoLeisPPA->setDado('tipo_configuracao',"alteracao");
                            $obTTCEMGConfiguracaoLeisPPA->setDado('status',false);
                            $obTTCEMGConfiguracaoLeisPPA->recuperaPorChave($rsTTCEMGConfiguracaoLeisPPA,$boTransacao);

                            if ($rsTTCEMGConfiguracaoLeisPPA->getNumLinhas() < 0) {
                                $obErro = $obTTCEMGConfiguracaoLeisPPA->inclusao($boTransacao);
                            } else {
                                $obErro = $obTTCEMGConfiguracaoLeisPPA->alteracao($boTransacao);
                            }
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Confiduração Leis do PPA","incluir","aviso", Sessao::getId(), "../");
                Sessao::remove('arNormas');
                Sessao::remove('arNormasRemovido');
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGConfiguracaoLeisPPA);
        }

        break;
}

?>
