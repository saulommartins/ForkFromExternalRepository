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
    * Página de Processamento de Comissao de Avaliacao
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * $Id: PRUnidadeOrcamentaria.php 60929 2014-11-25 16:24:43Z michel $

    * Casos de uso: uc-02.01.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoUnidadeOrcamentaria.class.php';
include CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "UnidadeOrcamentaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria;
$obROrgaoOrcamento      = new ROrcamentoOrgaoOrcamentario;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro();

//recupera os exercicios do ppa para propagar o recurso pelos exercicios do ppa
$obRPPAManterPPA = new RPPAManterPPA();
$obRPPAManterPPA->stExercicio = Sessao::getExercicio();
$obRPPAManterPPA->listByExercicio($rsRecordSet);

$stExercicio      = (int) Sessao::getExercicio();
$stExercicioFinal = (int) $rsRecordSet->getCampo('ano_final');

switch ($stAcao) {
    case "incluir":
        $arCodOrgao = preg_split( "/[^a-zA-Z0-9]/", $_POST['inCodOrgao']   );
        $arOrgao = explode('§', $_POST[$_POST['stNivel']]);

        //seta os dados comuns a todos os inserts
        $obRUnidadeOrcamentaria->setNumeroUnidade                             ($_POST['inNumeroUnidade']);
        $obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[0]);
        $obRUnidadeOrcamentaria->setNomUnidade                                ($_REQUEST['stNomeUnidade']);
        $obRUnidadeOrcamentaria->setCodResponsavel                            ($_REQUEST['inCGM']);

        //faz o insert para cada ano até o ano_final do ppa
        do {
            if (!$obErro->ocorreu()) {
                $obRUnidadeOrcamentaria->setExercicio                         ($stExercicio);
                $obErro = $obRUnidadeOrcamentaria->incluir($boTransacao);
            }
            $stExercicio++;
        } while ( $stExercicio <= $stExercicioFinal && $rsRecordSet->getNumLinhas() >= 1 );

        $obRUnidadeOrcamentaria->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obRUnidadeOrcamentaria->buscarMascara();
        $inNumUnidade = str_pad($_POST['inNumeroUnidade'], strlen($obRUnidadeOrcamentaria->getMascara()), "0", STR_PAD_LEFT);

        $obROrgaoOrcamento->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obROrgaoOrcamento->buscarMascara();
        $inNumOrgao = str_pad($arCodOrgao[0], strlen($obROrgaoOrcamento->getMascara()), "0", STR_PAD_LEFT);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Unidade: ".$inNumOrgao.".".$inNumUnidade,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $arCodOrgao   = preg_split( "/[^a-zA-Z0-9]/", $_POST['inCodOrgao']   );
        $arOrgao = explode('§', $_POST[$_POST['stNivel']]);

        //seta os dados comuns a todos
        $obRUnidadeOrcamentaria->setNumeroUnidade                             ($_POST['inNumeroUnidade']);
        $obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arCodOrgao[0]);
        $obRUnidadeOrcamentaria->setNomUnidade                                ($_REQUEST['stNomeUnidade']);
        $obRUnidadeOrcamentaria->setCodResponsavel                            ($_REQUEST['inCGM']);

        //faz o insert para cada ano até o ano_final do ppa
        do {
            if (!$obErro->ocorreu()) {
                $obRUnidadeOrcamentaria->setExercicio                         ($stExercicio);
                $obErro = $obRUnidadeOrcamentaria->alterar                    ($boTransacao);
            }
            $stExercicio++;
        } while ( $stExercicio <= $stExercicioFinal && $rsRecordSet->getNumLinhas() >= 1 );

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');

        if ( count($arFiltro) > 0 ) {
            foreach ($arFiltro as $arFiltro2) {
                foreach ($arFiltro2 as $stCampo => $stValor) {
                    $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
                }
            }
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        $obRUnidadeOrcamentaria->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obRUnidadeOrcamentaria->buscarMascara();
        $inNumUnidade = str_pad($_POST['inNumeroUnidade'], strlen($obRUnidadeOrcamentaria->getMascara()), "0", STR_PAD_LEFT);

        $obROrgaoOrcamento->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obROrgaoOrcamento->buscarMascara();
        $inNumOrgao = str_pad($arCodOrgao[0], strlen($obROrgaoOrcamento->getMascara()), "0", STR_PAD_LEFT);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Unidade: ".$inNumOrgao.".".$inNumUnidade,"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        //seta os dados comuns a todos
        $obRUnidadeOrcamentaria->setNumeroUnidade                             ($_GET['inNumeroUnidade']);
        $obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_GET['inNumeroOrgao']);
        //faz o insert para cada ano até o ano_final do ppa
        do {
            if (!$obErro->ocorreu()) {
                $obRUnidadeOrcamentaria->setExercicio                         ($stExercicio);
                $obErro = $obRUnidadeOrcamentaria->excluir                    ($boTransacao);
            }
            $stExercicio++;
        } while ( $stExercicio <= $stExercicioFinal && $rsRecordSet->getNumLinhas() >= 1 );

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        if ( count($arFiltro) > 0 ) {
            foreach ($arFiltro as $arFiltro2) {
                foreach ($arFiltro2 as $stCampo => $stValor) {
                    $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
                }
            }
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        $obRUnidadeOrcamentaria->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obRUnidadeOrcamentaria->buscarMascara();
        $inNumUnidade = str_pad($_GET['inNumeroUnidade'], strlen($obRUnidadeOrcamentaria->getMascara()), "0", STR_PAD_LEFT);

        $obROrgaoOrcamento->obRConfiguracaoOrcamento->setExercicio($arCodOrgao[1]);
        $obROrgaoOrcamento->buscarMascara();
        $inNumOrgao = str_pad($inNumOrgao, strlen($obROrgaoOrcamento->getMascara()), "0", STR_PAD_LEFT);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,"Unidade: ".$_GET['inNumeroOrgao'].".".$inNumUnidade,"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, urlencode("Não é possível excluir unidade que está em uso no sistema"), "n_excluir", "erro", Sessao::getId(), "../");
        }
    break;
}
?>
