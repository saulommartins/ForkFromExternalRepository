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

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoObra";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$obTTCERNObra = new TTCERNObra;
$obTTCERNObra->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
$obTTCERNObra->setDado('exercicio'   , Sessao::getExercicio());
$obTTCERNObra->setDado('num_obra'    , $_REQUEST['stNumObra']);
$obTTCERNObra->recuperaPorChave($rsObra);

if ($rsObra->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir') {
    SistemaLegado::exibeAviso("Já existe um contrato com este mesmo número","n_erro","erro");
    die;
}

$obTTCERNObra = new TTCERNObra;
$obTTCERNObra->setDado('cod_entidade'        , $_REQUEST['inCodEntidade']);
$obTTCERNObra->setDado('exercicio'           , Sessao::getExercicio());
$obTTCERNObra->setDado('num_obra'            , $_REQUEST['stNumObra']);
$obTTCERNObra->setDado('num_obra'            , $_REQUEST['stNumObra']);
$obTTCERNObra->setDado('obra'                , $_REQUEST['stObra']);
$obTTCERNObra->setDado('objetivo'            , $_REQUEST['stObjetivo']);
$obTTCERNObra->setDado('localizacao'         , $_REQUEST['stLocalizacao']);
$obTTCERNObra->setDado('cod_recurso_1'       , $_REQUEST['inCodRecurso1']);
$obTTCERNObra->setDado('cod_recurso_2'       , $_REQUEST['inCodRecurso2']);
$obTTCERNObra->setDado('cod_recurso_3'       , $_REQUEST['inCodRecurso3']);
$obTTCERNObra->setDado('valor_recurso_1'     , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlFonte1'])));
$obTTCERNObra->setDado('valor_recurso_2'     , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlFonte2'])));
$obTTCERNObra->setDado('valor_recurso_3'     , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlFonte3'])));
$obTTCERNObra->setDado('num_art'            , $_REQUEST['inART']);
$obTTCERNObra->setDado('cod_cidade'          , $_REQUEST['inCodCidade']);
$obTTCERNObra->setDado('valor_orcamento_base', str_replace(',', '.', str_replace('.', '', $_REQUEST['vlOrcamentoBase'])));
$obTTCERNObra->setDado('projeto_existente'   , $_REQUEST['stProjetoExistente']);
$obTTCERNObra->setDado('observacao'          , $_REQUEST['stObservacao']);
$obTTCERNObra->setDado('latitude'            , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlLatitude'])));
$obTTCERNObra->setDado('longitude'           , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlLongitude'])));
$obTTCERNObra->setDado('rdc'                 , $_REQUEST['inRdc']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNObra->inclusao();
    SistemaLegado::exibeAviso("Contrato incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNObra->alteracao();
    SistemaLegado::exibeAviso("Contrato alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
die;
