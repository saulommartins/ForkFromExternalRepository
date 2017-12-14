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
    * Titulo do arquivo : Arquivo de processamento de Vínculo do Tipo de Veículo do TCM para o URBEM
    * Data de Criação   : 22/12/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id: PRManterVinculoTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO.'TTMGVinculoTipoVeiculoTCE.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoTipoVeiculo";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

foreach ($_REQUEST as $key => $value) {
    if ($value) {
        $arKey = explode('_', $key);
        if (substr($key,0,10) == 'inCodTipo_') {
            $arVinculo[$arKey[1]]['tipo_urbem'] = $arKey[1];
            $arVinculo[$arKey[1]]['tipo_tce'] = $value;
        }
        if (substr($key,0,13) == 'inCodSubtipo_') {
            $arVinculo[$arKey[1]]['subtipo_tce'] = $value;
        }
    }
}

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($stAcao) {
    case 'manter' :
        if (is_array($arVinculo)) {
            $obTTMGVinculoTipoVeiculoTCE = new TTMGVinculoTipoVeiculoTCE();
            $obTTMGVinculoTipoVeiculoTCE->recuperaTodos($rsVinculos, '', '', $boTransacao);

            while (!$rsVinculos->EOF()) {
                $obTTMGVinculoTipoVeiculoTCE->setDado('cod_tipo', $rsVinculos->getCampo('cod_tipo'));
                $obTTMGVinculoTipoVeiculoTCE->exclusao($boTransacao);
                $rsVinculos->proximo();
            }

            foreach ($arVinculo as $arVinculoTMP) {
                if ($arVinculoTMP['tipo_urbem'] && $arVinculoTMP['tipo_tce'] && $arVinculoTMP['subtipo_tce']) {
                    $obTTMGVinculoTipoVeiculoTCE->setDado('cod_tipo', $arVinculoTMP['tipo_urbem']);
                    $obTTMGVinculoTipoVeiculoTCE->setDado('cod_tipo_tce', $arVinculoTMP['tipo_tce']);
                    $obTTMGVinculoTipoVeiculoTCE->setDado('cod_subtipo_tce', $arVinculoTMP['subtipo_tce']);
                    $obErro = $obTTMGVinculoTipoVeiculoTCE->inclusao($boTransacao);
                }
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário vincular ao menos um tipo de veículo!'),"n_incluir","erro");
        }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSTNAporteRecursoRPPSReceita );