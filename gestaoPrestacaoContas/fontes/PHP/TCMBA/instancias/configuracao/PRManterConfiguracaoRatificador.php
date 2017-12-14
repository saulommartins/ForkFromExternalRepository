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
* Processamento de Configuração de Ratificador TCM-BA
* Data de Criação: 11/08/2015

* @author Analista: Ane Caroline Fiegenbaum Pereira
* @author Desenvolvedor: Jean Silva 

$Id: PRManterConfiguracaoRatificador.php 63383 2015-08-24 12:34:24Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAConfiguracaoRatificador.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoRatificador";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get("stAcao");

$obErro = new Erro();
$boFlagTransacao = false;
$obTransacao = new Transacao;
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
$obTTCMBAConfiguracaoRatificador = new TTCMBAConfiguracaoRatificador();

if(!$obErro->ocorreu()){
    $arRatificadores = Sessao::read('arRatificadores');

    if (is_array($arRatificadores) && count($arRatificadores) > 0) {
        $obTTCMBAConfiguracaoRatificador->setDado("exercicio",'2015');
        $obTTCMBAConfiguracaoRatificador->setDado("cod_entidade",$request->get('hdnCodEntidade'));
        $obTTCMBAConfiguracaoRatificador->setDado("num_orgao"   ,$request->get('inMontaCodOrgaoM'));
        $obTTCMBAConfiguracaoRatificador->setDado("num_unidade" ,$request->get('inMontaCodUnidadeM'));
        $obErro = $obTTCMBAConfiguracaoRatificador->exclusao($boTransacao);
                 
        foreach( $arRatificadores AS $arRatificador ){
            $obTTCMBAConfiguracaoRatificador->setDado("exercicio"           ,Sessao::getExercicio());
            $obTTCMBAConfiguracaoRatificador->setDado("cod_entidade"        ,$arRatificador['cod_entidade']);
            $obTTCMBAConfiguracaoRatificador->setDado("cgm_ratificador"     ,$arRatificador['cgm_ratificador']);
            $obTTCMBAConfiguracaoRatificador->setDado("num_unidade"         ,$request->get('inMontaCodUnidadeM'));
            $obTTCMBAConfiguracaoRatificador->setDado("num_orgao"           ,$request->get('inMontaCodOrgaoM'));
            $obTTCMBAConfiguracaoRatificador->setDado("dt_inicio_vigencia"  ,$arRatificador['dt_inicio_vigencia']);
            $obTTCMBAConfiguracaoRatificador->setDado("dt_fim_vigencia"     ,$arRatificador['dt_fim_vigencia']);
            $obTTCMBAConfiguracaoRatificador->setDado("cod_tipo_responsavel",$arRatificador['cod_tipo_responsavel']);
            $obTTCMBAConfiguracaoRatificador->recuperaPorChave($rsRecordSet, $boTransacao);
                
            if ($rsRecordSet->eof()) {
                $obErro = $obTTCMBAConfiguracaoRatificador->inclusao($boTransacao);
            } else {
                $obErro = $obTTCMBAConfiguracaoRatificador->alteracao($boTransacao);
            }
        }

    } else {
        $obTTCMBAConfiguracaoRatificador->setDado("exercicio"   ,Sessao::getExercicio());
        $obTTCMBAConfiguracaoRatificador->setDado("cod_entidade",$request->get('hdnCodEntidade'));
        $obTTCMBAConfiguracaoRatificador->setDado("num_orgao"   ,$request->get('inMontaCodOrgaoM'));
        $obTTCMBAConfiguracaoRatificador->setDado("num_unidade" ,$request->get('inMontaCodUnidadeM'));
        $obErro = $obTTCMBAConfiguracaoRatificador->exclusao($boTransacao);
    }
    
    if(!$obErro->ocorreu()){
        $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCEPEConfiguracaoRatificador);
        SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao."&modulo=".$stModulo,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
    }else{
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
}

?>
