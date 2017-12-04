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
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNContrato.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl   = $_REQUEST['stCtrl'];
$convenio = explode('§', $_REQUEST['stNumConvenio']);

$obTTCERNContrato = new TTCERNContrato;
$obTTCERNContrato->setDado('num_convenio'       , $convenio[0]);
$obTTCERNContrato->setDado('cod_entidade'       , $convenio[1]);
$obTTCERNContrato->setDado('exercicio'          ,"'". $convenio[2]."'");
$obTTCERNContrato->setDado('num_contrato'       , $_REQUEST['inNumContrato']);
$obTTCERNContrato->setDado('exercicio_contrato' , "'".Sessao::getExercicio()."'");
$obTTCERNContrato->recuperaContrato($rsContrato);

if ($rsContrato->getNumLinhas() > 0 && $_REQUEST['stAcao'] == 'incluir') {
    SistemaLegado::exibeAviso("Já existe um contrato cadastrado com este mesmo número","n_erro","erro");
    die;
}

$stProcesso = explode ("/",$_REQUEST['stChaveProcesso']);

$obTTCERNContrato = new TTCERNContrato;
$obTTCERNContrato->setDado('num_contrato'                 , $_REQUEST['inNumContrato']);
$obTTCERNContrato->setDado('num_convenio'                 , $convenio[0]);
$obTTCERNContrato->setDado('cod_entidade'                 , $convenio[1]);
$obTTCERNContrato->setDado('exercicio'                    , $convenio[2]);
$obTTCERNContrato->setDado('exercicio_contrato'           , Sessao::getExercicio());
$obTTCERNContrato->setDado('cod_processo'                 , $stProcesso[0]);
$obTTCERNContrato->setDado('exercicio_processo'           , $stProcesso[1]);
$obTTCERNContrato->setDado('bimestre'                     , $_REQUEST['inBimestre']);
$obTTCERNContrato->setDado('cod_conta_especifica'         , $_REQUEST['inCodContaEspecifica']);
$obTTCERNContrato->setDado('dt_entrega_recurso'           , $_REQUEST['dtEntregaRecurso']);
$obTTCERNContrato->setDado('valor_repasse'                , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlRepasse'])));
$obTTCERNContrato->setDado('valor_executado'              , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlExecutado'])));
$obTTCERNContrato->setDado('receita_aplicacao_financeira' , str_replace(',', '.', str_replace('.', '', $_REQUEST['vlReceitaAplicacaoFinanceira'])));
$obTTCERNContrato->setDado('dt_recebimento_saldo'         , $_REQUEST['dtRecebimentoSaldo']);
$obTTCERNContrato->setDado('dt_prestacao_contas'          , $_REQUEST['dtPrestacaoContas']);

if ($_REQUEST['stAcao'] == 'incluir') {
    $obTTCERNContrato->inclusao();
    SistemaLegado::exibeAviso("Contrato incluido com sucesso","incluir","incluir_n");
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    $obTTCERNContrato->alteracao();
    SistemaLegado::exibeAviso("Contrato alterado com sucesso","incluir","n_alterar");
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}
