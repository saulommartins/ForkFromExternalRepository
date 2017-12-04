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
/*
    * Processamento para cadastro de compensações de horas
    * Data de Criação   : 03/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arLink = Sessao::read("link");
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCompensacaoHoras";
$pgFilt      = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList      = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm      = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc      = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul      = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS        = "JS".$stPrograma.".js";

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoCompensacaoHoras.class.php");
include_once(CAM_GRH_PON_MAPEAMENTO."TPontoCompensacaoHorasExclusao.class.php");
$obTPontoCompensacaoHoras = new TPontoCompensacaoHoras();
$obTPontoCompensacaoHorasExclusao = new TPontoCompensacaoHorasExclusao();
$obTPontoCompensacaoHorasExclusao->obTPontoCompensacaoHoras = &$obTPontoCompensacaoHoras;

$obErro = new erro();

Sessao::setTrataExcecao(true);
switch ($stAcao) {
    case "incluir":
        $pgRetorno = $pgForm;
        $stMensagem = "Compensação de horas incluída com sucesso.";
        $rsContratos = new Recordset();
        switch ($_POST["stTipoFiltro"]) {
            case "contrato":
            case "cgm_contrato":
                $rsContratos->preenche(Sessao::read("arContratos"));
                break;
            case "lotacao":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
                $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
                $stFiltro = " WHERE contrato_servidor_orgao.cod_orgao IN (".implode(",",$_POST["inCodLotacaoSelecionados"]).")";
                $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro);
                break;
            case "local":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
                $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
                $stFiltro = " WHERE contrato_servidor_local.cod_local IN (".implode(",",$_POST["inCodLocalSelecionados"]).")";
                $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro);
                break;
            case "reg_sub_fun_esp":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
                $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
                $stFiltro = " WHERE contrato_servidor_funcao.cod_cargo IN (".implode(",",$_POST["inCodFuncaoSelecionados"]).")";
                $stFiltro .= "   AND contrato_servidor_regime_funcao.cod_regime IN (".implode(",",$_POST["inCodRegimeSelecionadosFunc"]).")";
                $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao IN (".implode(",",$_POST["inCodSubDivisaoSelecionadosFunc"]).")";
                if (count($_POST["inCodEspecialidadeSelecionadosFunc"])) {
                    $stFiltro .= " AND contrato_servidor_especialiade_funcao.cod_especialidade IN (".implode(",",$_POST["inCodEspecialidadeSelecionadosFunc"]).")";
                }
                $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro);
                break;
        }
        if (trim($_POST["dtFalta"]) != "") {
            $arDtFalta = explode("/",$_POST["dtFalta"]);
            $dtFalta   = $arDtFalta[2]."-".$arDtFalta[1]."-".$arDtFalta[0];
        }
        if (trim($_POST["dtCompensacao"]) != "") {
            $arDtCompensacao = explode("/",$_POST["dtCompensacao"]);
            $dtCompensacao   = $arDtCompensacao[2]."-".$arDtCompensacao[1]."-".$arDtCompensacao[0];
        }

        while (!$rsContratos->eof()) {
            $stFiltro  = " AND compensacao_horas.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $stFiltro .= " AND (dt_falta = '".$dtFalta."' OR dt_compensacao = '".$dtCompensacao."')";
            $obTPontoCompensacaoHoras->recuperaRelacionamento($rsCompensacaoHoras,$stFiltro);
            if ($rsCompensacaoHoras->getNumLinhas() == 1) {
                $obErro->setDescricao("A compensação de horas para um ou mais contratos não foi realizada porque houve conflito da data de falta ou da data de compensação da falta. Favor Revisar.");
            } else {
                $obTPontoCompensacaoHoras->setDado("cod_compensacao","");
                $obTPontoCompensacaoHoras->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                $obTPontoCompensacaoHoras->setDado("dt_falta",$_POST["dtFalta"]);
                $obTPontoCompensacaoHoras->setDado("dt_compensacao",$_POST["dtCompensacao"]);
                $obTPontoCompensacaoHoras->inclusao();
            }
            $rsContratos->proximo();
        }
        break;
    case "alterar";
        $pgRetorno = $pgList;
        $stMensagem = "Compensação de horas alterada com sucesso.";
        if (trim($_POST["dtFalta"]) != "") {
            $arDtFalta = explode("/",$_POST["dtFalta"]);
            $dtFalta   = $arDtFalta[2]."-".$arDtFalta[1]."-".$arDtFalta[0];
        }
        if (trim($_POST["dtCompensacao"]) != "") {
            $arDtCompensacao = explode("/",$_POST["dtCompensacao"]);
            $dtCompensacao   = $arDtCompensacao[2]."-".$arDtCompensacao[1]."-".$arDtCompensacao[0];
        }

        if (trim($_POST["dtFaltaAntiga"]) != "") {
            $arDtFalta = explode("/",$_POST["dtFaltaAntiga"]);
            $dtFaltaAntiga   = $arDtFalta[2]."-".$arDtFalta[1]."-".$arDtFalta[0];
        }
        if (trim($_POST["dtCompensacaoAntiga"]) != "") {
            $arDtCompensacao = explode("/",$_POST["dtCompensacaoAntiga"]);
            $dtCompensacaoAntiga   = $arDtCompensacao[2]."-".$arDtCompensacao[1]."-".$arDtCompensacao[0];
        }
        switch ($_POST["stTipoFiltro"]) {
            case "contrato":
            case "cgm_contrato":
                $arContratos[0]["cod_contrato"] = $_POST["inCodigo"];
                $rsContratos = new recordset;
                $rsContratos->preenche($arContratos);
                break;
            case "lotacao":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
                $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
                $stFiltro = " WHERE contrato_servidor_orgao.cod_orgao = ".$_POST["inCodigo"];
                $stFiltro .= "   AND EXISTS (SELECT 1
                                               FROM ponto.compensacao_horas
                                              WHERE contrato_servidor_orgao.cod_contrato = compensacao_horas.cod_contrato
                                                AND compensacao_horas.dt_falta = '".$dtFaltaAntiga."'
                                                AND compensacao_horas.dt_compensacao = '".$dtCompensacaoAntiga."')";
                $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro);
                break;
            case "local":
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
                $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
                $stFiltro = " WHERE contrato_servidor_local.cod_local = ".$_POST["inCodigo"];
                $stFiltro .= "   AND EXISTS (SELECT 1
                                               FROM ponto.compensacao_horas
                                              WHERE contrato_servidor_local.cod_contrato = compensacao_horas.cod_contrato
                                                AND compensacao_horas.dt_falta = '".$dtFaltaAntiga."'
                                                AND compensacao_horas.dt_compensacao = '".$dtCompensacaoAntiga."')";
                $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro);
                break;
            case "reg_sub_fun_esp":
                $arCodigo = explode('_',$_POST["inCodigo"]);
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
                $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
                $stFiltro  = " WHERE contrato_servidor_funcao.cod_cargo = ".$arCodigo[2];
                $stFiltro .= "   AND contrato_servidor_regime_funcao.cod_regime = ".$arCodigo[0];
                $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = ".$arCodigo[1];
                if ($arCodigo[3] != 0) {
                    $stFiltro .= " AND contrato_servidor_especialiade_funcao.cod_especialidade = ".$arCodigo[3];
                }
                $stFiltro .= "   AND EXISTS (SELECT 1
                                               FROM ponto.compensacao_horas
                                              WHERE contrato_servidor_funcao.cod_contrato = compensacao_horas.cod_contrato
                                                AND compensacao_horas.dt_falta = '".$dtFaltaAntiga."'
                                                AND compensacao_horas.dt_compensacao = '".$dtCompensacaoAntiga."')";
                $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro);
                break;
        }

        while (!$rsContratos->eof()) {
            $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
            $stFiltro .= "   AND dt_falta = '".$dtFaltaAntiga."'";
            $stFiltro .= "   AND dt_compensacao = '".$dtCompensacaoAntiga."'";
            $obTPontoCompensacaoHoras->recuperaTodos($rsCompensacaoHorasAntigas,$stFiltro);
            $rsCompensacaoHoras = new recordset;
            if ($rsCompensacaoHorasAntigas->getNumLinhas() == 1) {
                $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltro .= "   AND cod_compensacao != ".$rsCompensacaoHorasAntigas->getCampo("cod_compensacao");
                $stFiltro .= "   AND (dt_falta = '".$dtFalta."' OR dt_compensacao = '".$dtCompensacao."')";
                $obTPontoCompensacaoHoras->recuperaTodos($rsCompensacaoHoras,$stFiltro);
            }
            if ($rsCompensacaoHoras->getNumLinhas() == 1) {
                $obErro->setDescricao("A compensação de horas para um ou mais contratos não foi realizada porque houve conflito da data de falta ou da data de compensação da falta. Favor Revisar.");
            } else {
                if ($rsCompensacaoHorasAntigas->getNumLinhas() == 1) {
                    $obTPontoCompensacaoHoras->setDado("cod_compensacao",$rsCompensacaoHorasAntigas->getCampo("cod_compensacao"));
                    $obTPontoCompensacaoHoras->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                    $obTPontoCompensacaoHoras->setDado("dt_falta",$_POST["dtFalta"]);
                    $obTPontoCompensacaoHoras->setDado("dt_compensacao",$_POST["dtCompensacao"]);
                    $obTPontoCompensacaoHoras->alteracao();
                }
            }
            $rsContratos->proximo();
        }
        break;
    case "excluir":
        $pgRetorno = $pgList;
        $stMensagem = "Compensação de horas excluída com sucesso.";
        foreach ($_POST as $stCampo=>$stValor) {
            if (strpos($stCampo,"excluirCompensacao") === 0) {
                $arExcluirCompensacao = explode("_",$stCampo);
                $inCodigo       = $arExcluirCompensacao[1];
                $dtFalta        = $arExcluirCompensacao[2];
                $dtCompensacao  = $arExcluirCompensacao[3];

                if (trim($dtFalta) != "") {
                    $arDtFalta = explode("/",$dtFalta);
                    $dtFalta   = $arDtFalta[2]."-".$arDtFalta[1]."-".$arDtFalta[0];
                }
                if (trim($dtCompensacao) != "") {
                    $arDtCompensacao = explode("/",$dtCompensacao);
                    $dtCompensacao   = $arDtCompensacao[2]."-".$arDtCompensacao[1]."-".$arDtCompensacao[0];
                }
                switch ($_POST["stTipoFiltro"]) {
                    case "contrato":
                    case "cgm_contrato":
                        $arContratos[0]["cod_contrato"] = $inCodigo;
                        $rsContratos = new recordset;
                        $rsContratos->preenche($arContratos);
                        break;
                    case "lotacao":
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
                        $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
                        $stFiltro  = " WHERE contrato_servidor_orgao.cod_orgao = ".$inCodigo;
                        $stFiltro .= "   AND EXISTS (SELECT 1
                                                       FROM ponto.compensacao_horas
                                                      WHERE contrato_servidor_orgao.cod_contrato = compensacao_horas.cod_contrato
                                                        AND compensacao_horas.dt_falta = '".$dtFalta."'
                                                        AND compensacao_horas.dt_compensacao = '".$dtCompensacao."')";
                        $obTPessoalContratoServidorOrgao->recuperaContratosDaLotacao($rsContratos,$stFiltro);
                        break;
                    case "local":
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
                        $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
                        $stFiltro = " WHERE contrato_servidor_local.cod_local = ".$inCodigo;
                        $stFiltro .= "   AND EXISTS (SELECT 1
                                                       FROM ponto.compensacao_horas
                                                      WHERE contrato_servidor_local.cod_contrato = compensacao_horas.cod_contrato
                                                        AND compensacao_horas.dt_falta = '".$dtFalta."'
                                                        AND compensacao_horas.dt_compensacao = '".$dtCompensacao."')";
                        $obTPessoalContratoServidorLocal->recuperaContratosDoLocal($rsContratos,$stFiltro);
                        break;
                    case "reg_sub_fun_esp":
                        $arCodigo = explode('_',$inCodigo);
                        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorFuncao.class.php");
                        $obTPessoalContratoServidorFuncao = new TPessoalContratoServidorFuncao();
                        $stFiltro  = " WHERE contrato_servidor_funcao.cod_cargo = ".$arCodigo[2];
                        $stFiltro .= "   AND contrato_servidor_regime_funcao.cod_regime = ".$arCodigo[0];
                        $stFiltro .= "   AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = ".$arCodigo[1];
                        if ($arCodigo[3] != 0) {
                            $stFiltro .= " AND contrato_servidor_especialiade_funcao.cod_especialidade = ".$arCodigo[3];
                        }
                        $stFiltro .= "   AND EXISTS (SELECT 1
                                                       FROM ponto.compensacao_horas
                                                      WHERE contrato_servidor_funcao.cod_contrato = compensacao_horas.cod_contrato
                                                        AND compensacao_horas.dt_falta = '".$dtFalta."'
                                                        AND compensacao_horas.dt_compensacao = '".$dtCompensacao."')";
                        $obTPessoalContratoServidorFuncao->recuperaContratosDaFuncao($rsContratos,$stFiltro);
                        break;
                }
                Sessao::consultarDadosSessao();
                while (!$rsContratos->eof()) {
                    $stFiltro  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                    $stFiltro .= "   AND dt_falta = '".$dtFalta."'";
                    $stFiltro .= "   AND dt_compensacao = '".$dtCompensacao."'";
                    $obTPontoCompensacaoHoras->recuperaTodos($rsCompensacaoHoras,$stFiltro);
                    if ($rsCompensacaoHoras->getNumLinhas() == 1) {
                        $obTPontoCompensacaoHorasExclusao->setDado("cod_compensacao",$rsCompensacaoHoras->getCampo("cod_compensacao"));
                        $obTPontoCompensacaoHorasExclusao->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                        $obTPontoCompensacaoHorasExclusao->setDado("numcgm",Sessao::read("numCgm"));
                        $obTPontoCompensacaoHorasExclusao->inclusao();
                    }

                    $rsContratos->proximo();
                }
            }
        }
        break;
}
Sessao::encerraExcecao();
if ($obErro->ocorreu()) {
    sistemaLegado::alertaAviso($pgRetorno,$obErro->getDescricao(),"n_".$stAcao,"erro",Sessao::getId(),"../");
} else {
    sistemaLegado::alertaAviso($pgRetorno,$stMensagem,$stAcao,"aviso",Sessao::getId(),"../");
}
?>
