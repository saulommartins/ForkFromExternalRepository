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
    * Página de processamento do IMA Configuração
    * Data de Criação: 20/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * Casos de uso: uc-04.08.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoExportacao";
$stAcao     = $_REQUEST["stAcao"];

$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgJS   = "JS".$stPrograma.".js";

include_once( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
include_once( CAM_GT_MON_MAPEAMENTO."TMONContaCorrente.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBb.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBbConta.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBbLocal.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBbOrgao.class.php");

$obTMONBanco                  = new TMONBanco;
$obTMONContaCorrente          = new TMONContaCorrente;
$obTIMAConfiguracaoConvenioBb = new TIMAConfiguracaoConvenioBb;
$obTIMAConfiguracaoBbConta 	  = new TIMAConfiguracaoBbConta;
$obTIMAConfiguracaoBbLocal    = new TIMAConfiguracaoBbLocal;
$obTIMAConfiguracaoBbOrgao    = new TIMAConfiguracaoBbOrgao;

$obTIMAConfiguracaoBbConta->obTIMAConfiguracaoConvenioBb = &$obTIMAConfiguracaoConvenioBb;
$obTIMAConfiguracaoBbLocal->obTIMAConfiguracaoBbConta    = &$obTIMAConfiguracaoBbConta;
$obTIMAConfiguracaoBbOrgao->obTIMAConfiguracaoBbConta    = &$obTIMAConfiguracaoBbConta;

switch ($stAcao) {
    case "alterar":
    case "incluir":
        Sessao::setTrataExcecao(true);

        $arContasConvenio = Sessao::read("arContasConvenio");

        if (is_array($arContasConvenio) && count($arContasConvenio) == 0) {
            Sessao::getExcecao()->setDescricao("Deve ser informado no mínimo uma Contas do Convênio.");
        }

        if (trim($stAcao)=="incluir") {
            $stFiltro = " WHERE vigencia >= to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
            $obTIMAConfiguracaoBbConta->recuperaVigencias($rsConfiguracaoBbConta, $stFiltro);

            if ($rsConfiguracaoBbConta->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("A vigência informada é menor que a vigência anterior.");
            }
        }

        $stFiltro = " WHERE num_banco = '".Sessao::read("stNumBanco")."'";
        $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);

        $stFiltro  = "	WHERE cod_convenio_banco = '".$_POST['stCodConvenio']."'";
        $stFiltro .= "	  AND cod_banco = ".$rsBanco->getCampo("cod_banco");
        $obTIMAConfiguracaoConvenioBb->recuperaTodos($rsConfiguracaoConvenioBb,$stFiltro);

        $obTIMAConfiguracaoConvenioBb->setDado("cod_convenio_banco", $_POST['stCodConvenio']);
        $obTIMAConfiguracaoConvenioBb->setDado("cod_banco"         , $rsBanco->getCampo("cod_banco"));

        if ($rsConfiguracaoConvenioBb->getNumLinhas() == -1) {
            $obTIMAConfiguracaoConvenioBb->setDado("cod_convenio", "");
            $obTIMAConfiguracaoConvenioBb->inclusao();
        } else {
            $obTIMAConfiguracaoConvenioBb->setDado("cod_convenio", $rsConfiguracaoConvenioBb->getCampo("cod_convenio"));
        }

        foreach ($arContasConvenio as $arTemp) {
            $stFiltro  = " WHERE num_conta_corrente = '".$arTemp["stContaCorrente"]."'";
            $stFiltro .= "   AND num_agencia = '".$arTemp["stNumAgenciaTxt"]."'";
            $obTMONContaCorrente->recuperaRelacionamento($rsConta,$stFiltro);

            $obTIMAConfiguracaoBbConta->setDado("cod_agencia"       , $rsConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBbConta->setDado("cod_conta_corrente", $rsConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBbConta->setDado("descricao"         , $arTemp["stDescricaoConvenio"]);
            $obTIMAConfiguracaoBbConta->setDado("vigencia"          , $_REQUEST["dtVigencia"]);
            $obTIMAConfiguracaoBbConta->inclusao();

            foreach ($arTemp["inCodLotacaoSelecionados"] as $inCodOrgao) {
                $obTIMAConfiguracaoBbOrgao->setDado("cod_orgao",$inCodOrgao);
                $obTIMAConfiguracaoBbOrgao->inclusao();
            }

            if (is_array($arTemp["inCodLocalSelecionados"]) and count($arTemp["inCodLocalSelecionados"]) > 0) {
                foreach ($arTemp["inCodLocalSelecionados"] as $inCodLocal) {
                    $obTIMAConfiguracaoBbLocal->setDado("cod_local",$inCodLocal);
                    $obTIMAConfiguracaoBbLocal->inclusao();
                }
            }
        }

        Sessao::encerraExcecao();
        Sessao::write("arContasConvenio",array());
        if (trim($stAcao)=="incluir") {
            sistemaLegado::alertaAviso($pgForm,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        }
        break;

    case "excluir":
        Sessao::setTrataExcecao(true);

        $stFiltro = " WHERE vigencia = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
        $obTIMAConfiguracaoBbConta->recuperaTodos($rsConfiguracaoBbConta, $stFiltro);

        while (!$rsConfiguracaoBbConta->eof()) {
            $obTIMAConfiguracaoBbConta->setDado("cod_convenio"      , $rsConfiguracaoBbConta->getCampo("cod_convenio"));
            $obTIMAConfiguracaoBbConta->setDado("cod_banco"         , $rsConfiguracaoBbConta->getCampo("cod_banco"));
            $obTIMAConfiguracaoBbConta->setDado("cod_agencia"       , $rsConfiguracaoBbConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBbConta->setDado("cod_conta_corrente", $rsConfiguracaoBbConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBbConta->setDado("timestamp"         , $rsConfiguracaoBbConta->getCampo("timestamp"));

            $obTIMAConfiguracaoBbLocal->exclusao();
            $obTIMAConfiguracaoBbOrgao->exclusao();
            $obTIMAConfiguracaoBbConta->exclusao();

            $rsConfiguracaoBbConta->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
