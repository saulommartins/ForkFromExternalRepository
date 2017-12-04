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
  * Página de Processamento da Configuração Metas Fiscais
  * Data de Criação: 22/01/2015

  * @author Analista: Ane Pereira
  * @author Desenvolvedor: Arthur Cruz

  * @ignore
  *
  * $Id: PRManterConfiguracaoMetasFiscaisLDO.php 61541 2015-02-03 12:04:33Z evandro $

  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOMetasFiscaisLDO.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasFiscaisLDO";
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
        $rsTTCMGOMetasFiscaisLDO = new RecordSet();
        $obTTCMGOMetasFiscaisLDO = new TTCMGOMetasFiscaisLDO();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $obTTCMGOMetasFiscaisLDO->setDado('exercicio'                                 , $request->get('stExercicio'));
            $obTTCMGOMetasFiscaisLDO->setDado('valor_corrente_receita'                    , $request->get('flValorCorrenteReceita'));
            $obTTCMGOMetasFiscaisLDO->setDado('valor_corrente_despesa'                    , $request->get('flValorCorrenteDespesa'));
            $obTTCMGOMetasFiscaisLDO->setDado('valor_corrente_resultado_primario'         , $request->get('flValorCorrenteResultadoPrimario'));
            $obTTCMGOMetasFiscaisLDO->setDado('valor_corrente_resultado_nominal'          , $request->get('flValorCorrenteResultadoNominal'));
            $obTTCMGOMetasFiscaisLDO->setDado('valor_corrente_divida_consolidada_liquida' , $request->get('flValorCorrenteDividaConsolidadaLiquida'));

            $obTTCMGOMetasFiscaisLDO->recuperaPorChave($rsTTCMGOMetasFiscaisLDO, $boTransacao);

            if ($rsTTCMGOMetasFiscaisLDO->getNumLinhas() < 0) {
                $obErro = $obTTCMGOMetasFiscaisLDO->inclusao($boTransacao);
            } else {
                $obErro = $obTTCMGOMetasFiscaisLDO->alteracao($boTransacao);
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId(),"Configurar Metas Fiscais concluído com sucesso!","manter","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMGOMetasFiscaisLDO);
        }

        break;
}

?>