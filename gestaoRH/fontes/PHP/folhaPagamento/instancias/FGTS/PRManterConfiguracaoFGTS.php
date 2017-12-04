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
    * Processamento de Manter Configuração FGTS
    * Data de Criação   : 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFGTS.class.php"                                        );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRFolhaPagamentoFGTS = new RFolhaPagamentoFGTS;

switch ($stAcao) {
    case "incluir":
        foreach ($_POST as $stChave=>$stValue) {
            if ( strpos($stChave,"inCodigoFGTS") === 0 ) {
                $inCodTipo = substr($stChave,12,strlen($stChave));
                $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->setCodigo($stValue);
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSEvento->setCodTipo($inCodTipo);
            }
        }
        $obErro = new erro;
        $arCategorias = Sessao::read("categoria");
        foreach ($arCategorias as $arCategoria) {
            $obRFolhaPagamentoFGTS->addRPessoalCategoria();
            $obRFolhaPagamentoFGTS->roRPessoalCategoria->setCodCategoria($arCategoria['inCodTxtCategoriaSefip']);
            if ($arCategoria['flValorDeposito'] != "") {
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSCategoria->setAliquotaDeposito($arCategoria['flValorDeposito']);
            } else {
                $obErro->setDescricao("Campo Alíquota para Depósito inválido!()");
                break;
            }
            if ($arCategoria['flValorRemuneracao'] != "") {
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSCategoria->setAliquotaContribuicao($arCategoria['flValorRemuneracao']);
            } else {
                $obErro->setDescricao("Campo Alíquota sobre Remuneração inválido!()");
                break;
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoFGTS->setVigencia($_POST['dtVigencia']);
            $obErro = $obRFolhaPagamentoFGTS->incluirFGTS();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"FGTS","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "alterar":
        $obErro = new erro;
        if ( count(Sessao::read('categoria')) == 0 ) {
            $obErro->setDescricao("Para a alteração ser concluida com sucesso deve haver no mínimo uma categoria.");
        }
        if ( !$obErro->ocorreu() ) {
            foreach ($_POST as $stChave=>$stValue) {
                if ( strpos($stChave,"inCodigoFGTS") === 0 ) {
                    $inCodTipo = substr($stChave,12,strlen($stChave));
                    $obRFolhaPagamentoFGTS->addRFolhaPagamentoEvento();
                    $obRFolhaPagamentoFGTS->roRFolhaPagamentoEvento->setCodigo($stValue);
                    $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSEvento->setCodTipo($inCodTipo);
                }
            }
            $arCategorias = Sessao::read("categoria");
            foreach ($arCategorias as $inIndex=>$arCategoria) {
                $obRFolhaPagamentoFGTS->addRPessoalCategoria();
                $obRFolhaPagamentoFGTS->roRPessoalCategoria->setCodCategoria($arCategoria['inCodTxtCategoriaSefip']);
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSCategoria->setAliquotaDeposito($_POST["flValorDeposito_".($inIndex+1)]);
                $obRFolhaPagamentoFGTS->roRFolhaPagamentoFGTSCategoria->setAliquotaContribuicao($_POST["flValorRemuneracao_".($inIndex+1)]);
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoFGTS->setCodFGTS($_POST['inCodFGTS']);
            $obErro = $obRFolhaPagamentoFGTS->listarFGTS($rsFGTS);
            if ( !$obErro->ocorreu() ) {
                if ( SistemaLegado::comparaDatas($rsFGTS->getCampo('vigencia'),$_POST['dtVigencia']) ) {
                    $obErro->setDescricao("A vigência informada é menor que a vigência anterior.");
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoFGTS->setVigencia($_POST['dtVigencia']);
            $obErro = $obRFolhaPagamentoFGTS->alterarFGTS();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"FGTS","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}

?>
