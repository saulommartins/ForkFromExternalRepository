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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNTipoVeiculoVinculo.class.php";
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNEspecieVeiculoTCE.class.php');
include_once(CAM_GPC_TCERN_MAPEAMENTO.'TTCERNTipoVeiculoTCE.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoTipoVeiculo";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$arVinculo = array();

foreach ($_REQUEST as $key => $value) {
    if ($value) {
        $arKey = explode ('_', $key);
        if ($arKey[0] == 'inCodEspecie') {
            $arVinculo[$arKey[2]]['tipo_urbem'] = $arKey[1];
            $arVinculo[$arKey[2]]['especie'] = $value;
        }

        if ($arKey[0] == 'inCodTipo') {
            $arVinculo[$arKey[2]]['tipo_urbem'] = $arKey[1];
            $arVinculo[$arKey[2]]['tipo'] = $value;
        }
    }
}

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'manter' :

        if (is_array($arVinculo)) {
            $obTTCERNTipoVeiculoVinculo = new TTCERNTipoVeiculoVinculo();
            $obTTCERNTipoVeiculoVinculo->recuperaTodos($rsVinculos);

            while (!$rsVinculos->EOF()) {
                $obTTCERNTipoVeiculoVinculo->setDado('cod_tipo', $rsVinculos->getCampo('cod_tipo'));
                $obTTCERNTipoVeiculoVinculo->exclusao();
                $rsVinculos->proximo();
            }

            foreach ($arVinculo as $key => $arValue) {

                if ($arValue['especie'] && $arValue['tipo']) {
                    $obTTCERNTipoVeiculoVinculo->setDado('cod_tipo'       , $arValue['tipo_urbem']);
                    $obTTCERNTipoVeiculoVinculo->setDado('cod_especie_tce', $arValue['especie']);
                    $obTTCERNTipoVeiculoVinculo->setDado('cod_tipo_tce'   , $arValue['tipo']);
                    $obTTCERNTipoVeiculoVinculo->inclusao();
                }
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");

        } else {

            sistemaLegado::exibeAviso(urlencode('É necessário vincular ao menos um tipo de veículo!'),"n_incluir","erro");

        }
}

Sessao::encerraExcecao();
