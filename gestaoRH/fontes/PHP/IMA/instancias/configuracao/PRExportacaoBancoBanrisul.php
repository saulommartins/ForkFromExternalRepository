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
    * Página de processamento do IMA Configuração - Banrisul
    * Data de Criação: 26/02/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: PRExportacaoBancoBanrisul.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.16
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoBanrisul";
$stAcao = $request->get('stAcao');

$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao.$stLink;
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
include_once(CAM_GT_MON_MAPEAMENTO."TMONAgencia.class.php");
include_once(CAM_GT_MON_MAPEAMENTO."TMONContaCorrente.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBanrisul.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanrisulConta.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanrisulLocal.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanrisulOrgao.class.php");

$obTMONBanco                    	= new TMONBanco;
$obTMONContaCorrente            	= new TMONContaCorrente;
$obTIMAConfiguracaoConvenioBanrisul = new TIMAConfiguracaoConvenioBanrisul;
$obTIMAConfiguracaoBanrisulConta    = new TIMAConfiguracaoBanrisulConta;
$obTIMAConfiguracaoBanrisulLocal    = new TIMAConfiguracaoBanrisulLocal;
$obTIMAConfiguracaoBanrisulOrgao    = new TIMAConfiguracaoBanrisulOrgao;

$obTIMAConfiguracaoBanrisulConta->obTIMAConfiguracaoConvenioBanrisul = &$obTIMAConfiguracaoConvenioBanrisul;
$obTIMAConfiguracaoBanrisulLocal->obTIMAConfiguracaoBanrisulConta    = &$obTIMAConfiguracaoBanrisulConta;
$obTIMAConfiguracaoBanrisulOrgao->obTIMAConfiguracaoBanrisulConta    = &$obTIMAConfiguracaoBanrisulConta;

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
            $obTIMAConfiguracaoBanrisulConta->recuperaVigencias($rsConfiguracaoBanrisulConta, $stFiltro);

            if ($rsConfiguracaoBanrisulConta->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("A vigência informada é menor que a vigência anterior.");
            }
        }

        $stFiltro = " WHERE num_banco = '".Sessao::read("stNumBanco")."'";
        $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);

        $stFiltro  = "	WHERE cod_convenio_banco = '".$_POST['stCodConvenio']."'";
        $stFiltro .= "	  AND cod_banco = ".$rsBanco->getCampo("cod_banco");
        $obTIMAConfiguracaoConvenioBanrisul->recuperaTodos($rsConfiguracaoConvenioBanrisul,$stFiltro);

        $obTIMAConfiguracaoConvenioBanrisul->setDado("cod_convenio_banco", $_POST['stCodConvenio']);
        $obTIMAConfiguracaoConvenioBanrisul->setDado("cod_banco"         , $rsBanco->getCampo("cod_banco"));

        if ($rsConfiguracaoConvenioBanrisul->getNumLinhas() == -1) {
            $obTIMAConfiguracaoConvenioBanrisul->setDado("cod_convenio", "");
            $obTIMAConfiguracaoConvenioBanrisul->inclusao();
        } else {
            $obTIMAConfiguracaoConvenioBanrisul->setDado("cod_convenio", $rsConfiguracaoConvenioBanrisul->getCampo("cod_convenio"));
        }

        foreach ($arContasConvenio as $arTemp) {
            $stFiltro  = " WHERE num_conta_corrente = '".$arTemp["stContaCorrente"]."'";
            $stFiltro .= "   AND num_agencia = '".$arTemp["stNumAgenciaTxt"]."'";
            $obTMONContaCorrente->recuperaRelacionamento($rsConta,$stFiltro);

            $obTIMAConfiguracaoBanrisulConta->setDado("cod_agencia"       , $rsConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBanrisulConta->setDado("cod_conta_corrente", $rsConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBanrisulConta->setDado("descricao"         , $arTemp["stDescricaoConvenio"]);
            $obTIMAConfiguracaoBanrisulConta->setDado("vigencia"          , $_REQUEST["dtVigencia"]);
            $obTIMAConfiguracaoBanrisulConta->inclusao();

            foreach ($arTemp["inCodLotacaoSelecionados"] as $inCodOrgao) {
                $obTIMAConfiguracaoBanrisulOrgao->setDado("cod_orgao",$inCodOrgao);
                $obTIMAConfiguracaoBanrisulOrgao->inclusao();
            }

            if (is_array($arTemp["inCodLocalSelecionados"]) and count($arTemp["inCodLocalSelecionados"]) > 0) {
                foreach ($arTemp["inCodLocalSelecionados"] as $inCodLocal) {
                    $obTIMAConfiguracaoBanrisulLocal->setDado("cod_local",$inCodLocal);
                    $obTIMAConfiguracaoBanrisulLocal->inclusao();
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
        $obTIMAConfiguracaoBanrisulConta->recuperaTodos($rsConfiguracaoBanrisulConta, $stFiltro);

        while (!$rsConfiguracaoBanrisulConta->eof()) {
            $obTIMAConfiguracaoBanrisulConta->setDado("cod_convenio"      , $rsConfiguracaoBanrisulConta->getCampo("cod_convenio"));
            $obTIMAConfiguracaoBanrisulConta->setDado("cod_banco"         , $rsConfiguracaoBanrisulConta->getCampo("cod_banco"));
            $obTIMAConfiguracaoBanrisulConta->setDado("cod_agencia"       , $rsConfiguracaoBanrisulConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBanrisulConta->setDado("cod_conta_corrente", $rsConfiguracaoBanrisulConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBanrisulConta->setDado("timestamp"         , $rsConfiguracaoBanrisulConta->getCampo("timestamp"));

            $obTIMAConfiguracaoBanrisulLocal->exclusao();
            $obTIMAConfiguracaoBanrisulOrgao->exclusao();
            $obTIMAConfiguracaoBanrisulConta->exclusao();

            $rsConfiguracaoBanrisulConta->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
