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
    * Página de Processamento de Configuração da Exportação do Banco BanPará
    * Data de Criação: 09/04/2008

    * @author Rafael Garbin

    * Casos de uso: uc-04.08.18

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao 	= $_REQUEST["stAcao"];
$stPrograma = "ExportacaoBancoBanPara";

$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;

include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaEmpresa.class.php" );
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php" 		);
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaOrgao.class.php" 	);
include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaLocal.class.php" 	);

$obTIMAConfiguracaoBanparaEmpresa = new TIMAConfiguracaoBanparaEmpresa();
$obTIMAConfiguracaoBanpara 		  = new TIMAConfiguracaoBanpara();
$obTIMAConfiguracaoBanparaOrgao   = new TIMAConfiguracaoBanparaOrgao();
$obTIMAConfiguracaoBanparaLocal   = new TIMAConfiguracaoBanparaLocal();

$obTIMAConfiguracaoBanpara->obTIMAConfiguracaoBanparaEmpresa  = &$obTIMAConfiguracaoBanparaEmpresa;
$obTIMAConfiguracaoBanparaLocal->obTIMAConfiguracaoBanpara    = &$obTIMAConfiguracaoBanpara;
$obTIMAConfiguracaoBanparaOrgao->obTIMAConfiguracaoBanpara    = &$obTIMAConfiguracaoBanpara;

$arOrgaos = Sessao::read("arOrgaos");

switch (trim($stAcao)) {
    case "alterar":
    case "incluir":
        Sessao::setTrataExcecao(true);

        if (is_array($arOrgaos) && count($arOrgaos) == 0) {
            Sessao::getExcecao()->setDescricao("Deve ser informado no mínimo uma configuração de orgão.");
        }

        if (trim($stAcao)=="incluir") {
            $stFiltro = " WHERE ultima_vigencia_competencia.vigencia >= to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
            $obTIMAConfiguracaoBanpara->recuperaVigencias($rsConfiguracaoBanpara, $stFiltro);

            if ($rsConfiguracaoBanpara->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("A vigência informada é menor que a vigência anterior.");
            }
        }

        $stFiltro  = " WHERE codigo = ".$_POST["inCodigoEmpresa"];
        $obTIMAConfiguracaoBanparaEmpresa->recuperaTodos($rsConfiguracaoBanparaEmpresa, $stFiltro);

        if ($rsConfiguracaoBanparaEmpresa->getNumLinhas() == -1) {
            $obTIMAConfiguracaoBanparaEmpresa->setDado("codigo",   $_POST["inCodigoEmpresa"]);
            $obTIMAConfiguracaoBanparaEmpresa->inclusao();
        } else {
            $obTIMAConfiguracaoBanparaEmpresa->setDado("cod_empresa", $rsConfiguracaoBanparaEmpresa->getCampo("cod_empresa"));
        }

        if (is_array($arOrgaos)) {
           foreach ($arOrgaos as $arOrgao) {
                $obTIMAConfiguracaoBanpara->setDado("num_orgao_banpara", $arOrgao["inCodigoOrgao"]);
                $obTIMAConfiguracaoBanpara->setDado("descricao"	, $arOrgao['stDescricao']);
                $obTIMAConfiguracaoBanpara->setDado("vigencia"	, $_POST['dtVigencia']);
                $obTIMAConfiguracaoBanpara->inclusao();

                foreach ($arOrgao['inCodLotacaoSelecionados'] as $inCodOrgao) {
                    $obTIMAConfiguracaoBanparaOrgao->setDado("cod_orgao", $inCodOrgao);
                    $obTIMAConfiguracaoBanparaOrgao->inclusao();
                }
                if ($arOrgao['inCodLocalSelecionados'] != NULL && $arOrgao['inCodLocalSelecionados'] != '') {
                    foreach ($arOrgao['inCodLocalSelecionados'] as $inCodLocal) {
                        $obTIMAConfiguracaoBanparaLocal->setDado("cod_local", $inCodLocal);
                        $obTIMAConfiguracaoBanparaLocal->inclusao();
                    }
                }

           }
        }

        Sessao::encerraExcecao();
        Sessao::write("arOrgaos",array());
        if (trim($stAcao)=="incluir") {
            sistemaLegado::alertaAviso($pgForm,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!",$stAcao,"aviso", Sessao::getId(), "../");
        }
        break;

    case "excluir":
        Sessao::setTrataExcecao(true);

        $stFiltro = " WHERE vigencia = to_date('".$_REQUEST["dtVigencia"]."','dd/mm/yyyy')";
        $obTIMAConfiguracaoBanpara->recuperaTodos($rsConfiguracaoBanpara, $stFiltro);

        while (!$rsConfiguracaoBanpara->eof()) {
            $obTIMAConfiguracaoBanpara->setDado("cod_empresa"      , $rsConfiguracaoBanpara->getCampo("cod_empresa"));
            $obTIMAConfiguracaoBanpara->setDado("num_orgao_banpara", $rsConfiguracaoBanpara->getCampo("num_orgao_banpara"));
            $obTIMAConfiguracaoBanpara->setDado("timestamp"        , $rsConfiguracaoBanpara->getCampo("timestamp"));

            $obTIMAConfiguracaoBanparaLocal->exclusao();
            $obTIMAConfiguracaoBanparaOrgao->exclusao();
            $obTIMAConfiguracaoBanpara->exclusao();

            $rsConfiguracaoBanpara->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
