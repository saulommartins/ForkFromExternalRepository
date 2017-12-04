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
    * Pagine de processos do Movimentação SEFIP
    * Data de Criação: 06/02/2006

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.40

    $Id: PRManterMovimentacaoSEFIP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
$link = Sessao::read("link");
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterMovimentacaoSEFIP";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$aCategorias       = Sessao::read("aCategorias");

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalSefip.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaida.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovimentoSefipRetorno.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalMovSefipSaidaMovSefipRetorno.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoriaMovimento.class.php");
$obTPessoalSefip = new TPessoalSefip();
$obTPessoalMovSefipSaida = new TPessoalMovSefipSaida();
$obTPessoalMovimentoSefipRetorno = new TPessoalMovimentoSefipRetorno();
$obTPessoalMovSefipSaidaMovSefipRetorno = new TPessoalMovSefipSaidaMovSefipRetorno();
$obTPessoalCategoriaMovimento = new TPessoalCategoriaMovimento();
$obTPessoalMovSefipSaida->obTPessoalSefip = &$obTPessoalSefip;
$obTPessoalMovimentoSefipRetorno->obTPessoalSefip = &$obTPessoalSefip;
$obTPessoalMovSefipSaidaMovSefipRetorno->obTPessoalMovSefipSaida = &$obTPessoalMovSefipSaida;
$obTPessoalMovSefipSaidaMovSefipRetorno->obTPessoalMovimentoSefipRetorno = &$obTPessoalMovimentoSefipRetorno;
$obTPessoalCategoriaMovimento->obTPessoalMovSefipSaida = &$obTPessoalMovSefipSaida;

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $pgRetorno = $pgForm;
        $obErro = new Erro;
        //Verificações
        if ($_POST['stMovimentacao'] == 'A') {
            if ($_POST['boRetorno'] == 'N') {
                if (count($aCategorias) <= 0) {
                    $obErro->setDescricao('Inclua ao menos um Indicativo!');
                }
            } else {
                if (!$_POST['stNumSefipRetorno']) {
                    $obErro->setDescricao('Escolha uma SEFIP para retorno!');
                }
            }
        }
        if (!$obErro->ocorreu()) {
            $stFiltro = " WHERE num_sefip = '".trim($_POST["stCodigoSEFIP"])."'";
            $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
            if ($rsSefip->getNumLinhas() > 0) {
                $obErro->setDescricao("O Código SEFIP ".$_POST["stCodigoSEFIP"]." já foi cadastrado.");
            }
        }
        if (!$obErro->ocorreu()) {
            $obTPessoalSefip->setDado("descricao",$_POST["stDescricao"]);
            $obTPessoalSefip->setDado("num_sefip",$_POST["stCodigoSEFIP"]);
            $obTPessoalSefip->setDado("repetir_mensal",($_POST["stRepetir"]=="S")?true:false);
            $obTPessoalSefip->inclusao();
            //Movimentação SEFIP
            //R = Retorno
            //A = Afastamento (Saída)
            if ($_POST['stMovimentacao'] == 'R') {
                // Inclusão de sefip de retorno
                $obTPessoalMovimentoSefipRetorno->inclusao();
            } else {
                //Inclusão de Sefip de saida
                $obTPessoalMovSefipSaida->inclusao();
                if ($_POST['boRetorno'] == 'N') {
                    // inclusão de categorias para sefip sem retorno
                    foreach ($aCategorias as $categoria) {
                        $obTPessoalCategoriaMovimento->setDado("cod_categoria",$categoria['inCodCategoria']);
                        $obTPessoalCategoriaMovimento->setDado("indicativo",$categoria['stIndicativo']);
                        $obTPessoalCategoriaMovimento->inclusao();
                    }
                } else {
                    // inclusão de sefip de saida com retorno
                    $stFiltro = " WHERE num_sefip = '".trim($_POST["stNumSefipRetorno"])."'";
                    $obTPessoalSefip->recuperaTodos($rsSefipRetorno,$stFiltro);

                    $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_saida",$obTPessoalSefip->getDado("cod_sefip"));
                    $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_retorno",$rsSefipRetorno->getCampo("cod_sefip"));
                    $obTPessoalMovSefipSaidaMovSefipRetorno->inclusao();
                }
            }
        }
        Sessao::encerraExcecao();
        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
        } else {
            sistemaLegado::alertaAviso($pgRetorno,"Inclusão da SEFIP ".$_POST["stCodigoSEFIP"]." concluída com sucesso.",$stAcao,"aviso",Sessao::getId(),"../");
        }
        break;
    case 'alterar':
        Sessao::setTrataExcecao(true);
        $obErro = new Erro;
        //Verificações
        if ($_POST['stMovimentacao'] == 'A') {
            if ($_POST['boRetorno'] == 'N') {
                if (count($aCategorias) <= 0) {
                    $obErro->setDescricao('Inclua ao menos um Indicativo!');
                }
            } else {
                if (!$_POST['stNumSefipRetorno']) {
                    $obErro->setDescricao('Escolha uma SEFIP para retorno!');
                }
            }
        }
        if (!$obErro->ocorreu()) {
            $stFiltro = " WHERE num_sefip = '".$_POST["stCodigoSEFIP"]."'";
            $obTPessoalSefip->recuperaTodos($rsSefip,$stFiltro);
            $obTPessoalSefip->setDado("cod_sefip",$rsSefip->getCampo("cod_sefip"));
            $obTPessoalSefip->setDado("descricao",$_POST["stDescricao"]);
            $obTPessoalSefip->setDado("num_sefip",$_POST["stCodigoSEFIP"]);
            $obTPessoalSefip->setDado("repetir_mensal",($_POST["stRepetir"]=="S")?true:false);
            $obTPessoalSefip->alteracao();
            //Movimentação SEFIP
            //R = Retorno
            //A = Afastamento (Saída)
            if (Sessao::read("stMovimentacao") == 'A') {
                //alteração de Sefip de saida
                $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_saida",$rsSefip->getCampo("cod_sefip"));
                $obTPessoalMovSefipSaidaMovSefipRetorno->exclusao();

                // alteração de categorias para sefip sem retorno
                $obTPessoalCategoriaMovimento->exclusao();

                if ($_POST['boRetorno'] == 'N') {
                    foreach ($aCategorias as $categoria) {
                        $obTPessoalCategoriaMovimento->setDado("cod_categoria",$categoria['inCodCategoria']);
                        $obTPessoalCategoriaMovimento->setDado("indicativo",$categoria['stIndicativo']);
                        $obTPessoalCategoriaMovimento->inclusao();
                    }
                } else {
                    // alteração de sefip de saida com retorno
                    $stFiltro = " WHERE num_sefip = '".trim($_POST["stNumSefipRetorno"])."'";
                    $obTPessoalSefip->recuperaTodos($rsSefipRetorno,$stFiltro);

                    $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_saida",$obTPessoalSefip->getDado("cod_sefip"));
                    $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_retorno",$rsSefipRetorno->getCampo("cod_sefip"));
                    $obTPessoalMovSefipSaidaMovSefipRetorno->inclusao();
                }
            }
        }
        Sessao::encerraExcecao();
        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_alterar","erro");
        } else {
            sistemaLegado::alertaAviso($pgList,"Alteração da SEFIP ".$_POST["stCodigoSEFIP"]." concluída com sucesso.",$stAcao,"aviso",Sessao::getId(),"../");
        }
        break;
    case 'excluir':
        Sessao::setTrataExcecao(true);
        $obErro = new Erro;

        $obTPessoalSefip->setDado("cod_sefip",$_GET['cod_sefip']);
        $obTPessoalMovSefipSaida->recuperaPorChave($rsSefipSaida);
        if ($rsSefipSaida->getNumLinhas() > 0) {
            $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_saida",$_GET['cod_sefip']);
            $obTPessoalMovSefipSaidaMovSefipRetorno->exclusao();

            $obTPessoalCategoriaMovimento->exclusao();

            $obTPessoalMovSefipSaida->exclusao();
        } else {
            $obTPessoalMovSefipSaidaMovSefipRetorno->setDado("cod_sefip_retorno",$_GET['cod_sefip']);
            $obTPessoalMovSefipSaidaMovSefipRetorno->exclusao();

            $obTPessoalMovimentoSefipRetorno->exclusao();
        }
        $obTPessoalSefip->exclusao();

        Sessao::encerraExcecao();
        if ($obErro->ocorreu()) {
            sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_excluir","erro");
        } else {
            sistemaLegado::alertaAviso($pgList,"Exclusão da SEFIP concluída com sucesso.",$stAcao,"aviso",Sessao::getId(),"../");
        }
        break;
}

?>
