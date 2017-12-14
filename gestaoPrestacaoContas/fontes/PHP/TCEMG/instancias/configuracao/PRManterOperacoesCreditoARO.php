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
    * Página de Processamento Operações de Crédito ARO
    * Data de Criação   : 10/03/2015
    * 
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    $Id: PRManterOperacoesCreditoARO.php 62529 2015-05-18 17:56:34Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGOperacoesCreditoARO.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOperacoesCreditoARO";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
 
$stAcao = $_REQUEST['stAcao'];

switch ($stAcao) {
    default:
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $obTTCEMGOperacoesCreditoARO = new TTCEMGOperacoesCreditoARO();

        if(!$obErro->ocorreu()){            
            $obTTCEMGOperacoesCreditoARO->setDado("exercicio"       , Sessao::getExercicio()            );
            $obTTCEMGOperacoesCreditoARO->setDado("cod_entidade"    , $_REQUEST['inCodEntidade']        );
            $obTTCEMGOperacoesCreditoARO->setDado("dt_contratacao"  , $_REQUEST['dtContratacao']        );
            $obTTCEMGOperacoesCreditoARO->setDado("vl_contratado"   , $_REQUEST['nuVlContratado']       );
            $obTTCEMGOperacoesCreditoARO->setDado("dt_principal"    , $_REQUEST['dtLiquidacaoPrincipal']);
            $obTTCEMGOperacoesCreditoARO->setDado("dt_juros"        , $_REQUEST['dtLiquidacaoJuros']    );
            $obTTCEMGOperacoesCreditoARO->setDado("dt_encargos"     , $_REQUEST['dtLiquidacaoEncargos'] );
            $obTTCEMGOperacoesCreditoARO->setDado("vl_liquidacao"   , $_REQUEST['nuVlLiquidacao']       );

            $obTTCEMGOperacoesCreditoARO->recuperaPorChave($rsRecordSet);

            if ($rsRecordSet->eof())
                $obErro = $obTTCEMGOperacoesCreditoARO->inclusao($boTransacao);
            else
                $obErro = $obTTCEMGOperacoesCreditoARO->alteracao($boTransacao);       
        }

        if(!$obErro->ocorreu()){
            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEMGOperacoesCreditoARO);

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        }else{
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}
?>
