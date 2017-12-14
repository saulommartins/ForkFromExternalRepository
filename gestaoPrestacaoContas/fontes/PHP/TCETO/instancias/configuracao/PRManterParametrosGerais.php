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
    * Pacote de configuração do TCETO - Processamento Configurar Parâmetros Gerais
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterParametrosGerais.php 60645 2014-11-05 15:47:16Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();

$obErro = new Erro;

$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

$obTAdministracaoConfiguracao->setDado("cod_modulo",64);
$obTAdministracaoConfiguracao->setDado("exercicio",  Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado("parametro", "tceto_orgao_prefeitura" );
$obTAdministracaoConfiguracao->setDado( "valor", $_POST['inCodExecutivo'] );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_unidade_prefeitura" );
$obTAdministracaoConfiguracao->setDado( "valor", $_POST['inCodUnidadeExecutivo'] );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_orgao_camara" );
$obTAdministracaoConfiguracao->setDado( "valor", $_POST['inCodLegislativo'] );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );  

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_unidade_camara" );
$obTAdministracaoConfiguracao->setDado( "valor", $_POST['inCodUnidadeLegislativo'] );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_orgao_rpps" );
$obTAdministracaoConfiguracao->setDado( "valor", ($_POST['inCodRPPS']?$_POST['inCodRPPS']:'') );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_unidade_rpps" );
$obTAdministracaoConfiguracao->setDado( "valor", ($_POST['inCodUnidadeRPPS']?$_POST['inCodUnidadeRPPS']:'') );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_orgao_outros" );
$obTAdministracaoConfiguracao->setDado( "valor", ($_POST['inCodOutros']?$_POST['inCodOutros']:'') );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

$obTAdministracaoConfiguracao->setDado("parametro", "tceto_unidade_outros" );
$obTAdministracaoConfiguracao->setDado( "valor", ($_POST['inCodUnidadeOutros']?$_POST['inCodUnidadeOutros']:'') );
$obErro = $obTAdministracaoConfiguracao->recuperaPorChave( $rsRecordSet, $boTransacao );
$obErro = $obTAdministracaoConfiguracao->alteracao( $boTransacao );

if ( !$obErro->ocorreu() ) {
    $obErro = $obTransacao->commitAndClose();
} else {
    $obTransacao->rollbackAndClose();
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>
