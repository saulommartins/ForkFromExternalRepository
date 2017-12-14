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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraAditivo.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObraAditivo";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obTTCERNObraAditivo = new TTCERNObraAditivo;

if ($stAcao == 'incluir') {
    $obTTCERNObraAditivo->recuperaMaxId($rsMax);
    $obTTCERNObraAditivo->setDado('id', ($rsMax->getCampo('max_id') + 1));
} elseif ($stAcao == 'manter') {
    $obTTCERNObraAditivo->setDado('id', $_REQUEST['InId']);
}

$obTTCERNObraAditivo->setDado('obra_contrato_id', $_REQUEST['stContrato']);
$obTTCERNObraAditivo->setDado('num_aditivo'     , $_REQUEST['inNumAditivo']);
$obTTCERNObraAditivo->setDado('dt_aditivo'      , $_REQUEST['dtAditivo']);
$obTTCERNObraAditivo->setDado('prazo'           , $_REQUEST['stPrazo']);
$obTTCERNObraAditivo->setDado('prazo_aditado'   , $_REQUEST['stPrazoAditado']);
$obTTCERNObraAditivo->setDado('valor'           , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlValor'])));
$obTTCERNObraAditivo->setDado('valor_aditado'   , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlValorAditado'])));
$obTTCERNObraAditivo->setDado('num_art'         , $_REQUEST['inART']);
$obTTCERNObraAditivo->setDado('motivo'          , $_REQUEST['stMotivo']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNObraAditivo->inclusao();
    SistemaLegado::exibeAviso("Aditivo incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNObraAditivo->alteracao();
    SistemaLegado::exibeAviso("Aditivo alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
die;
