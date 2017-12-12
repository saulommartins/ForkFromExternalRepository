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
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNConvenio.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

$obTTCERNConvenio = new TTCERNConvenio;
$obTTCERNConvenio->setDado('num_convenio' , $_REQUEST['stNumConvenio']);
$obTTCERNConvenio->setDado('exercicio'    , Sessao::getExercicio());
$obTTCERNConvenio->setDado('cod_entidade' , $_REQUEST['inCodEntidade']);
$obTTCERNConvenio->recuperaConvenio($rsConvenio);

if ($rsConvenio->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir') {
    SistemaLegado::exibeAviso("Já existe um convênio cadastrado com este mesmo número","n_erro","erro");
    die;
}

$stProcesso = explode ("/",$_REQUEST['stChaveProcesso']);

$obTTCERNConvenio = new TTCERNConvenio;
$obTTCERNConvenio->setDado('cod_entidade'       , $_REQUEST['inCodEntidade']);
$obTTCERNConvenio->setDado('exercicio'          , Sessao::getExercicio());
$obTTCERNConvenio->setDado('cod_processo'       , $stProcesso[0]);
$obTTCERNConvenio->setDado('exercicio_processo' , $stProcesso[1]);
$obTTCERNConvenio->setDado('num_convenio'       , $_REQUEST['stNumConvenio']);
$obTTCERNConvenio->setDado('numcgm_recebedor'   , $_REQUEST['inCGM']);
$obTTCERNConvenio->setDado('cod_objeto'         , $_REQUEST['inCodEntidade']);
$obTTCERNConvenio->setDado('cod_recurso_1'      , $_REQUEST['inCodRecurso1']);
$obTTCERNConvenio->setDado('cod_recurso_2'      , $_REQUEST['inCodRecurso2']);
$obTTCERNConvenio->setDado('cod_recurso_3'      , $_REQUEST['inCodRecurso3']);
$obTTCERNConvenio->setDado('valor_recurso_1'    , str_replace(',', '.', str_replace('.', '', $_REQUEST['stValorFonte1'])));
$obTTCERNConvenio->setDado('valor_recurso_2'    , str_replace(',', '.', str_replace('.', '', $_REQUEST['stValorFonte2'])));
$obTTCERNConvenio->setDado('valor_recurso_3'    , str_replace(',', '.', str_replace('.', '', $_REQUEST['stValorFonte3'])));
$obTTCERNConvenio->setDado('dt_inicio_vigencia' , $_REQUEST['dtInicioVigencia']);
$obTTCERNConvenio->setDado('dt_termino_vigencia', $_REQUEST['dtTerminoVigencia']);
$obTTCERNConvenio->setDado('dt_assinatura'      , $_REQUEST['dtAssinatura']);
$obTTCERNConvenio->setDado('dt_publicacao'      , $_REQUEST['dtPublicacao']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNConvenio->inclusao();
    SistemaLegado::exibeAviso("Convênio incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNConvenio->alteracao();
    SistemaLegado::exibeAviso("Convênio alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
die;
