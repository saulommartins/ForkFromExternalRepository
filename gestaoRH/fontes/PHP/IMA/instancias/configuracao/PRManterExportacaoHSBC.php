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
    * Configuração da Exportação do HSBC
    * Data de Criação: 11/12/2009

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Mancilha

    * @package URBEM
    * @subpackage Mapeamento

    * $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterExportacaoHSBC";
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
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioHSBC.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCConta.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCLocal.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCOrgao.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCBanco.class.php");

$obTMONBanco                    = new TMONBanco;
$obTMONContaCorrente            = new TMONContaCorrente;
$obTIMAConfiguracaoConvenioHSBC = new TIMAConfiguracaoConvenioHSBC;
$obTIMAConfiguracaoHSBCConta    = new TIMAConfiguracaoHSBCConta;
$obTIMAConfiguracaoHSBCLocal    = new TIMAConfiguracaoHSBCLocal;
$obTIMAConfiguracaoHSBCOrgao    = new TIMAConfiguracaoHSBCOrgao;
$obTIMAConfiguracaoHSBCBanco 	= new TIMAConfiguracaoHSBCBanco;

$obTIMAConfiguracaoHSBCConta->obTIMAConfiguracaoConvenioHSBC = &$obTIMAConfiguracaoConvenioHSBC;
$obTIMAConfiguracaoHSBCLocal->obTIMAConfiguracaoHSBCConta    = &$obTIMAConfiguracaoHSBCConta;
$obTIMAConfiguracaoHSBCOrgao->obTIMAConfiguracaoHSBCConta    = &$obTIMAConfiguracaoHSBCConta;
$obTIMAConfiguracaoHSBCBanco->obTIMAConfiguracaoHSBCConta    = &$obTIMAConfiguracaoHSBCConta;

switch ($stAcao) {
    case "alterar":
    case "incluir":
        Sessao::setTrataExcecao(true);

        $arContasConvenio = Sessao::read("arContasConvenio");

        if (empty($arContasConvenio)) {
            Sessao::getExcecao()->setDescricao("Deve ser informado no mínimo uma Contas do Convênio.");
        }

        if (trim($stAcao)=="incluir") {
            $stFiltro = " WHERE vigencia >= to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
            $obTIMAConfiguracaoHSBCConta->recuperaVigencias($rsConfiguracaoHSBCConta, $stFiltro);

            if ($rsConfiguracaoHSBCConta->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("A vigência informada é menor que a vigência anterior.");
            }
        }

        $stFiltro = " WHERE num_banco = '".Sessao::read("stNumBanco")."'";
        $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);

        $stFiltro  = "	WHERE cod_convenio_banco = '".$_POST['stCodConvenio']."'";
        $stFiltro .= "	  AND cod_banco = ".$rsBanco->getCampo("cod_banco");
        $obTIMAConfiguracaoConvenioHSBC->recuperaTodos($rsConfiguracaoConvenioHSBC,$stFiltro);

        $obTIMAConfiguracaoConvenioHSBC->setDado("cod_convenio_banco", $_POST['stCodConvenio']);
        $obTIMAConfiguracaoConvenioHSBC->setDado("cod_banco"         , $rsBanco->getCampo("cod_banco"));

        if ($rsConfiguracaoConvenioHSBC->getNumLinhas() == -1) {
            $obTIMAConfiguracaoConvenioHSBC->setDado("cod_convenio", "");
            $obTIMAConfiguracaoConvenioHSBC->inclusao();
        } else {
            $obTIMAConfiguracaoConvenioHSBC->setDado("cod_convenio", $rsConfiguracaoConvenioHSBC->getCampo("cod_convenio"));
        }

        foreach ($arContasConvenio as $arTemp) {
            $stFiltro  = " WHERE num_conta_corrente = '".$arTemp["stContaCorrente"]."'";
            $stFiltro .= "   AND num_agencia = '".$arTemp["stNumAgenciaTxt"]."'";
            $obTMONContaCorrente->recuperaRelacionamento($rsConta,$stFiltro);

            $obTIMAConfiguracaoHSBCConta->setDado("cod_agencia"       , $rsConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoHSBCConta->setDado("cod_conta_corrente", $rsConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoHSBCConta->setDado("descricao"         , $arTemp["stDescricaoConvenio"]);
            $obTIMAConfiguracaoHSBCConta->setDado("vigencia"          , $_REQUEST["dtVigencia"]);
            $obTIMAConfiguracaoHSBCConta->inclusao();

            if (isset($arTemp["inCodBancoSelecionados"])) {
                foreach ($arTemp["inCodBancoSelecionados"] as $codOutroBanco) {
                $obTIMAConfiguracaoHSBCBanco->setDado("cod_convenio" 	   , $rsConfiguracaoConvenioHSBC->getCampo("cod_convenio"));
                $obTIMAConfiguracaoHSBCBanco->setDado("cod_banco"    	   , $rsConta->getCampo("cod_banco"));
                $obTIMAConfiguracaoHSBCBanco->setDado("cod_agencia"    	   , $rsConta->getCampo("cod_agencia"));
                $obTIMAConfiguracaoHSBCBanco->setDado("cod_conta_corrente" , $rsConta->getCampo("cod_conta_corrente"));
                $obTIMAConfiguracaoHSBCBanco->setDado("timestamp"	   , $rsConta->getCampo("timestamp"));
                $obTIMAConfiguracaoHSBCBanco->setDado("cod_banco_outros"   , $codOutroBanco);
                $obTIMAConfiguracaoHSBCBanco->inclusao();
                }
            }

            foreach ($arTemp["inCodLotacaoSelecionados"] as $inCodOrgao) {
                $obTIMAConfiguracaoHSBCOrgao->setDado("cod_orgao",$inCodOrgao);
                $obTIMAConfiguracaoHSBCOrgao->inclusao();
            }

            if (is_array($arTemp["inCodLocalSelecionados"]) and count($arTemp["inCodLocalSelecionados"]) > 0) {
                foreach ($arTemp["inCodLocalSelecionados"] as $inCodLocal) {
                    $obTIMAConfiguracaoHSBCLocal->setDado("cod_local",$inCodLocal);
                    $obTIMAConfiguracaoHSBCLocal->inclusao();
                }
            }
        }

        Sessao::encerraExcecao();
        Sessao::write("arContasConvenio", array());
        if (trim($stAcao)=="incluir") {
            sistemaLegado::alertaAviso($pgForm,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        }
        break;

    case "excluir":
        Sessao::setTrataExcecao(true);

        $stFiltro = " WHERE vigencia = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
        $obTIMAConfiguracaoHSBCConta->recuperaTodos($rsConfiguracaoHSBCConta, $stFiltro);

        while (!$rsConfiguracaoHSBCConta->eof()) {
            $obTIMAConfiguracaoHSBCConta->setDado("cod_convenio"      , $rsConfiguracaoHSBCConta->getCampo("cod_convenio"));
            $obTIMAConfiguracaoHSBCConta->setDado("cod_banco"         , $rsConfiguracaoHSBCConta->getCampo("cod_banco"));
            $obTIMAConfiguracaoHSBCConta->setDado("cod_agencia"       , $rsConfiguracaoHSBCConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoHSBCConta->setDado("cod_conta_corrente", $rsConfiguracaoHSBCConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoHSBCConta->setDado("timestamp"         , $rsConfiguracaoHSBCConta->getCampo("timestamp"));

            $obTIMAConfiguracaoHSBCLocal->exclusao();
            $obTIMAConfiguracaoHSBCOrgao->exclusao();
            $obTIMAConfiguracaoHSBCBanco->exclusao();
            $obTIMAConfiguracaoHSBCConta->exclusao();

            $rsConfiguracaoHSBCConta->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
