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
    * Página de processamento do IMA Configuração - BESC
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: alex $
    $Date: 2007-09-27 16:08:17 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-04.08.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoBESC";
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
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBesc.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescConta.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescLocal.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescOrgao.class.php");

$obTMONBanco                    = new TMONBanco;
$obTMONContaCorrente            = new TMONContaCorrente;
$obTIMAConfiguracaoConvenioBesc = new TIMAConfiguracaoConvenioBesc;
$obTIMAConfiguracaoBescConta    = new TIMAConfiguracaoBescConta;
$obTIMAConfiguracaoBescLocal    = new TIMAConfiguracaoBescLocal;
$obTIMAConfiguracaoBescOrgao    = new TIMAConfiguracaoBescOrgao;

$obTIMAConfiguracaoBescConta->obTIMAConfiguracaoConvenioBesc = &$obTIMAConfiguracaoConvenioBesc;
$obTIMAConfiguracaoBescLocal->obTIMAConfiguracaoBescConta    = &$obTIMAConfiguracaoBescConta;
$obTIMAConfiguracaoBescOrgao->obTIMAConfiguracaoBescConta    = &$obTIMAConfiguracaoBescConta;

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
            $obTIMAConfiguracaoBescConta->recuperaVigencias($rsConfiguracaoBescConta, $stFiltro);

            if ($rsConfiguracaoBescConta->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("A vigência informada é menor que a vigência anterior.");
            }
        }

        $stFiltro = " WHERE num_banco = '".Sessao::read("stNumBanco")."'";
        $obTMONBanco->recuperaTodos($rsBanco,$stFiltro);

        $stFiltro  = "	WHERE cod_convenio_banco = '".$_POST['stCodConvenio']."'";
        $stFiltro .= "	  AND cod_banco = ".$rsBanco->getCampo("cod_banco");
        $obTIMAConfiguracaoConvenioBesc->recuperaTodos($rsConfiguracaoConvenioBesc,$stFiltro);

        $obTIMAConfiguracaoConvenioBesc->setDado("cod_convenio_banco", $_POST['stCodConvenio']);
        $obTIMAConfiguracaoConvenioBesc->setDado("cod_banco"         , $rsBanco->getCampo("cod_banco"));

        if ($rsConfiguracaoConvenioBesc->getNumLinhas() == -1) {
            $obTIMAConfiguracaoConvenioBesc->setDado("cod_convenio", "");
            $obTIMAConfiguracaoConvenioBesc->inclusao();
        } else {
            $obTIMAConfiguracaoConvenioBesc->setDado("cod_convenio", $rsConfiguracaoConvenioBesc->getCampo("cod_convenio"));
        }

        foreach ($arContasConvenio as $arTemp) {
            $stFiltro  = " WHERE num_conta_corrente = '".$arTemp["stContaCorrente"]."'";
            $stFiltro .= "   AND num_agencia = '".$arTemp["stNumAgenciaTxt"]."'";
            $obTMONContaCorrente->recuperaRelacionamento($rsConta,$stFiltro);

            $obTIMAConfiguracaoBescConta->setDado("cod_agencia"       , $rsConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBescConta->setDado("cod_conta_corrente", $rsConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBescConta->setDado("descricao"         , $arTemp["stDescricaoConvenio"]);
            $obTIMAConfiguracaoBescConta->setDado("vigencia"          , $_REQUEST["dtVigencia"]);
            $obTIMAConfiguracaoBescConta->inclusao();

            foreach ($arTemp["inCodLotacaoSelecionados"] as $inCodOrgao) {
                $obTIMAConfiguracaoBescOrgao->setDado("cod_orgao",$inCodOrgao);
                $obTIMAConfiguracaoBescOrgao->inclusao();
            }

            if (is_array($arTemp["inCodLocalSelecionados"]) and count($arTemp["inCodLocalSelecionados"]) > 0) {
                foreach ($arTemp["inCodLocalSelecionados"] as $inCodLocal) {
                    $obTIMAConfiguracaoBescLocal->setDado("cod_local",$inCodLocal);
                    $obTIMAConfiguracaoBescLocal->inclusao();
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
        $obTIMAConfiguracaoBescConta->recuperaTodos($rsConfiguracaoBescConta, $stFiltro);

        while (!$rsConfiguracaoBescConta->eof()) {
            $obTIMAConfiguracaoBescConta->setDado("cod_convenio"      , $rsConfiguracaoBescConta->getCampo("cod_convenio"));
            $obTIMAConfiguracaoBescConta->setDado("cod_banco"         , $rsConfiguracaoBescConta->getCampo("cod_banco"));
            $obTIMAConfiguracaoBescConta->setDado("cod_agencia"       , $rsConfiguracaoBescConta->getCampo("cod_agencia"));
            $obTIMAConfiguracaoBescConta->setDado("cod_conta_corrente", $rsConfiguracaoBescConta->getCampo("cod_conta_corrente"));
            $obTIMAConfiguracaoBescConta->setDado("timestamp"         , $rsConfiguracaoBescConta->getCampo("timestamp"));

            $obTIMAConfiguracaoBescLocal->exclusao();
            $obTIMAConfiguracaoBescOrgao->exclusao();
            $obTIMAConfiguracaoBescConta->exclusao();

            $rsConfiguracaoBescConta->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Configuração da exportação bancária concluída com sucesso!","excluir","aviso", Sessao::getId(), "../");
        break;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
