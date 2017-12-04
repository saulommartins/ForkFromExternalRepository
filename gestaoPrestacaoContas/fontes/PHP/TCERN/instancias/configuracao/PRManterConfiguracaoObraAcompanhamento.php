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
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraAcompanhamento.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObraAcompanhamento";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obTTCERNObraAcompanhamento = new TTCERNObraAcompanhamento;

if ($stAcao == 'incluir') {
    $obTTCERNObraAcompanhamento->recuperaMaxId($rsMax);
    $obTTCERNObraAcompanhamento->setDado('id', ($rsMax->getCampo('max_id') + 1));
} elseif ($stAcao == 'manter') {
    $obTTCERNObraAcompanhamento->setDado('id', $_REQUEST['InId']);
}

$obTTCERNObraAcompanhamento->setDado('obra_contrato_id'  , $_REQUEST['stContrato']);
$obTTCERNObraAcompanhamento->setDado('dt_evento'         , $_REQUEST['dtEvento']);
$obTTCERNObraAcompanhamento->setDado('numcgm_responsavel', $_REQUEST['inCGMResponsavel']);
$obTTCERNObraAcompanhamento->setDado('cod_situacao'      , $_REQUEST['inCodSituacao']);
$obTTCERNObraAcompanhamento->setDado('justificativa'     , $_REQUEST['stJustificativa']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNObraAcompanhamento->inclusao();
    SistemaLegado::exibeAviso("Acompanhamento incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNObraAcompanhamento->alteracao();
    SistemaLegado::exibeAviso("Acompanhamento alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
die;
