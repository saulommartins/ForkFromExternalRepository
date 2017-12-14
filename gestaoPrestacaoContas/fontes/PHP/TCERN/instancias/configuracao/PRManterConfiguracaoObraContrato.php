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
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObra.class.php");
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraContrato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObraContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$obra = explode('§', $_REQUEST['stNumObra']);

$obTTCERNObraContrato = new TTCERNObraContrato;
$obTTCERNObraContrato->setDado('num_contrato', $_REQUEST['stContrato']);
$obTTCERNObraContrato->recuperaPorChave($rsObraContrato);

if ($rsObraContrato->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir') {
    SistemaLegado::exibeAviso("Já existe um contrato com este mesmo número","n_erro","erro");
    die;
}

$obTTCERNObraContrato = new TTCERNObraContrato;
$obTTCERNObraContrato->recuperaMaxId($rsMax);

$obTTCERNObraContrato->setDado('id'                       , ($rsMax->getCampo('max_id') + 1));
$obTTCERNObraContrato->setDado('cod_entidade'             , $obra[1]);
$obTTCERNObraContrato->setDado('exercicio'                , $obra[2]);
$obTTCERNObraContrato->setDado('num_obra'                 , $obra[0]);
$obTTCERNObraContrato->setDado('num_contrato'             , $_REQUEST['stContrato']);
$obTTCERNObraContrato->setDado('servico'                  , $_REQUEST['stServico']);
$obTTCERNObraContrato->setDado('processo_licitacao'       , $_REQUEST['stProcessoLicitacao']);
$obTTCERNObraContrato->setDado('numcgm'                   , $_REQUEST['inCGM']);
$obTTCERNObraContrato->setDado('valor_contrato'           , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlContrato'])));
$obTTCERNObraContrato->setDado('valor_executado_exercicio', str_replace(',', '.', str_replace('.', '', $_REQUEST['vlExecutadoExercicio'])));
$obTTCERNObraContrato->setDado('valor_a_exercutar'        , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlAExecutar'])));
$obTTCERNObraContrato->setDado('dt_inicio_contrato'       , $_REQUEST['dtInicioContrato']);
$obTTCERNObraContrato->setDado('dt_termino_contrato'      , $_REQUEST['dtTerminoContrato']);
$obTTCERNObraContrato->setDado('num_art'                  , $_REQUEST['inART']);
$obTTCERNObraContrato->setDado('valor_iss'                , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlISS'])));
$obTTCERNObraContrato->setDado('num_dcms'                 , $_REQUEST['inDCMS']);
$obTTCERNObraContrato->setDado('valor_inss'               , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlINSS'])));
$obTTCERNObraContrato->setDado('numcgm_fiscal'            , $_REQUEST['inCGMFiscal']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNObraContrato->inclusao();
    SistemaLegado::exibeAviso("Contrato incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNObraContrato->alteracao();
    SistemaLegado::exibeAviso("Contrato alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
die;
