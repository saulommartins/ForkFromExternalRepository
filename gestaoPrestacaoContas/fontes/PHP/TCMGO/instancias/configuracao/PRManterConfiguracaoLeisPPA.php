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
  * $Id: PRManterConfiguracaoLeisPPA.php 61668 2015-02-24 13:48:38Z michel $

  * $Revision: 61668 $
  * $Name: $
  * $Author: michel $
  * $Date: 2015-02-24 10:48:38 -0300 (Tue, 24 Feb 2015) $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoLeisPPA.class.php");

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
        $obErro = new Erro;
        $rsTTCMGOConfiguracaoLeisPPA = new RecordSet();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        $arDescPubLei = array();
        $arDescPubLei[1] = 'Diário Oficial do Estado';
        $arDescPubLei[2] = 'Diário Oficial do Município';
        $arDescPubLei[3] = 'Placar da Prefeitura ou da Câmara Municipal';
        $arDescPubLei[4] = 'Jornal de grande circulação';
        $arDescPubLei[5] = 'Diário Oficial da União';
        $arDescPubLei[9] = 'Endereço eletrônico completo (Internet)';

        if (!$obErro->ocorreu()) {
            // INCLUINDO LEIS DE CONSULTA
            $arNormasRemovidos = Sessao::read('arNormasRemovido');
            if (count($arNormasRemovidos) > 0) {
                foreach ($arNormasRemovidos as $arNormaRemovido) {
                    $obTTCMGOConfiguracaoLeisNormaRemovido = new TTCMGOConfiguracaoLeisPPA();
                    $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('exercicio',Sessao::getExercicio());
                    $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('cod_norma',$arNormaRemovido['inCodNorma']);
                    $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('tipo_configuracao',"alteracao");
                    $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('cod_veiculo_publicacao', 0);
                    $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('status',false);
                    $obTTCMGOConfiguracaoLeisNormaRemovido->recuperaPorChave($rsNormaRemovido,$boTransacao);
                    
                    if($rsNormaRemovido->getCampo('cod_veiculo_publicacao')>0)
                        $obTTCMGOConfiguracaoLeisNormaRemovido->setDado('cod_veiculo_publicacao',$rsNormaRemovido->getCampo('cod_veiculo_publicacao'));

                    if ($rsNormaRemovido->getNumLinhas() < 0) 
                        $obErro = $obTTCMGOConfiguracaoLeisNormaRemovido->inclusao($boTransacao);
                    else 
                        $obErro = $obTTCMGOConfiguracaoLeisNormaRemovido->alteracao($boTransacao);
                }
            }
            
            $obTTCMGOConfiguracaoLeisPPA = new TTCMGOConfiguracaoLeisPPA();
            $obTTCMGOConfiguracaoLeisPPA->setDado('exercicio',Sessao::getExercicio());
            $obTTCMGOConfiguracaoLeisPPA->setDado('tipo_configuracao',"consulta");
            $obTTCMGOConfiguracaoLeisPPA->recuperaPorChave($rsTTCMGOConfiguracaoLeisPPA,$boTransacao);

            while (!$rsTTCMGOConfiguracaoLeisPPA->eof()) {
                $obTTCMGOConfiguracaoLeisPPAAnterior = new TTCMGOConfiguracaoLeisPPA();
                $obTTCMGOConfiguracaoLeisPPAAnterior->setDado('exercicio',Sessao::getExercicio());
                $obTTCMGOConfiguracaoLeisPPAAnterior->setDado('cod_norma',$rsTTCMGOConfiguracaoLeisPPA->getCampo('cod_norma'));
                $obTTCMGOConfiguracaoLeisPPAAnterior->setDado('tipo_configuracao',"consulta");
                $obTTCMGOConfiguracaoLeisPPAAnterior->setDado('cod_veiculo_publicacao',$rsTTCMGOConfiguracaoLeisPPA->getCampo('cod_veiculo_publicacao'));
                $obTTCMGOConfiguracaoLeisPPAAnterior->setDado('status',false);
                $obErro = $obTTCMGOConfiguracaoLeisPPAAnterior->alteracao($boTransacao);

                $rsTTCMGOConfiguracaoLeisPPA->proximo();
            }

            // INCLUINDO LEIS DE ALTERAÇÃO
            if (!$obErro->ocorreu()) {                    
                $arNormas = Sessao::read('arNormas');

                if (count($arNormas) > 0 && !$obErro->ocorreu()) {
                    foreach ($arNormas as $arNorma) {
                        $obTTCMGOConfiguracaoLeisPPA = new TTCMGOConfiguracaoLeisPPA();
                        $obTTCMGOConfiguracaoLeisPPA->setDado('exercicio',Sessao::getExercicio());
                        $obTTCMGOConfiguracaoLeisPPA->setDado('cod_norma',$arNorma['inCodNorma']);
                        $obTTCMGOConfiguracaoLeisPPA->setDado('tipo_configuracao',"alteracao");
                        $obTTCMGOConfiguracaoLeisPPA->setDado('status',true);
                        $obTTCMGOConfiguracaoLeisPPA->setDado('cod_veiculo_publicacao',($arNorma['codPubLeiAlteracao'] !='') ? $arNorma['codPubLeiAlteracao'] : 0);
                        
                        $stDescLei = ($arNorma['codPubLeiAlteracao'] == 9 ) ? $arNorma['stDescLeiAlteracao'] : $arDescPubLei[$arNorma['codPubLeiAlteracao']];
                        $obTTCMGOConfiguracaoLeisPPA->setDado('descricao_publicacao',$stDescLei);
                        $obTTCMGOConfiguracaoLeisPPA->recuperaPorChave($rsTTCMGOConfiguracaoLeisPPA,$boTransacao);

                        if ($rsTTCMGOConfiguracaoLeisPPA->getNumLinhas() < 0) {
                            $obErro = $obTTCMGOConfiguracaoLeisPPA->inclusao($boTransacao);
                        } else {
                            $obErro = $obTTCMGOConfiguracaoLeisPPA->alteracao($boTransacao);
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId(),"Configuração Leis do PPA","incluir","aviso", Sessao::getId(), "../");
                Sessao::remove('arNormas');
                Sessao::remove('arNormasRemovido');
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCMGOConfiguracaoLeisPPA);
        }

        break;
}

?>
