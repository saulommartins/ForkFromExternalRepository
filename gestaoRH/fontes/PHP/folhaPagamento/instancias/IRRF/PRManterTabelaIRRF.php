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
    * Processamento de TabelaIRRF
    * Data de Criação   : 06/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2007-10-19 19:01:51 -0200 (Sex, 19 Out 2007) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoIRRF.class.php"                                        );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterTabelaIRRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRFolhaPagamentoIRRF = new RFolhaPagamentoIRRF;

switch ($stAcao) {
    case "incluir":
        $obErro = new erro;
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php");
        $obTFolhaPagamentoTabelaIrrf = new TFolhaPagamentoTabelaIrrf();
        $obTFolhaPagamentoTabelaIrrf->recuperaUltimaVigencia($rsVigencia);
        $arData = explode("-",$rsVigencia->getCampo("vigencia"));
        $stData = $arData[2]."/".$arData[1]."/".$arData[0];

        if ( SistemaLegado::comparaDatas($stData,$_POST['dtVigencia']) ) {
            $obErro->setDescricao("A vigência informada é menor que a vigência anterior ".$stData.".");
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoIRRF->setDependente             ( $_POST['flValorDependente'] );
            $obRFolhaPagamentoIRRF->setLimiteIsencao          ( $_POST['flValorLimite']     );
            $obRFolhaPagamentoIRRF->setVigencia               ( $_POST['dtVigencia']        );

            foreach ($_POST as $stName=>$stValue) {
                if ( strpos($stName,'inCodigoIRRF') === 0 ) {
                    $inCodTipo = substr($stName,12,strlen($stName));
                    $obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->setCodigo($stValue);
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEventoIRRF->setCodTipo($inCodTipo);
                }
            }
            $arFaixas = Sessao::read("faixa");
            if ( count ($arFaixas ) > 0 ) {
                foreach ($arFaixas as $arFaixa) {
                    $obRFolhaPagamentoIRRF->addRFolhaPagamentoFaixaIRRF();
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setInicial          ($arFaixa['flValorInicial']);
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setFinal            ($arFaixa['flValorFinal']  );
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setAliquota         ($arFaixa['flAliquota']    );
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setParcelaDeduzir   ($arFaixa['flParcela']     );
                }
            }
            $arCids = Sessao::read("cid");
            if ( count ($arCids) > 0 ) {
                foreach ($arCids as $arCID) {
                    $obRFolhaPagamentoIRRF->addRPessoalCID();
                    $obRFolhaPagamentoIRRF->roRPessoalCID->setCodCID($arCID['cod_cid']);
                }
            }
            $arEventosAjudaCusto = Sessao::read("eventoAjudaCusto");
            if ( count ($arEventosAjudaCusto) > 0 ) {
                foreach ($arEventosAjudaCusto as $arEventoAjudaCusto) {
                    $obRFolhaPagamentoIRRF->addEventoAjudaCusto($arEventoAjudaCusto['flCodigoEvento']);
                }
            }
            $obErro = $obRFolhaPagamentoIRRF->incluirIRRF();
       }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"IRRF","incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obErro = new erro;
        if ( SistemaLegado::comparaDatas($_POST['dtVigenciaAntiga'],$_POST['dtVigencia']) ) {
            $obErro->setDescricao("A vigência informada é menor que a vigência anterior.");
        }
        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoIRRF->setCodTabela              ( $_POST['inCodTabela']       );
            $obRFolhaPagamentoIRRF->setTimestamp              ( $_POST['stTimestamp']       );
            $obRFolhaPagamentoIRRF->setDependente             ( $_POST['flValorDependente'] );
            $obRFolhaPagamentoIRRF->setLimiteIsencao          ( $_POST['flValorLimite']     );
            $obRFolhaPagamentoIRRF->setVigencia               ( $_POST['dtVigencia']        );
            foreach ($_POST as $stName=>$stValue) {
                if ( strpos($stName,'inCodigoIRRF') === 0 ) {
                    $inCodTipo = substr($stName,12,strlen($stName));
                    $obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->setCodigo($stValue);
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEventoIRRF->setCodTipo($inCodTipo);
                }
            }
            $arFaixas = Sessao::read("faixa");
            if ( count ($arFaixas ) > 0 ) {
                foreach ($arFaixas as $arFaixa) {
                    $obRFolhaPagamentoIRRF->addRFolhaPagamentoFaixaIRRF();
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setInicial          ($arFaixa['flValorInicial']);
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setFinal            ($arFaixa['flValorFinal']  );
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setAliquota         ($arFaixa['flAliquota']    );
                    $obRFolhaPagamentoIRRF->roRFolhaPagamentoFaixaIRRF->setParcelaDeduzir   ($arFaixa['flParcela']     );
                }
            }
            $arCids = Sessao::read("cid");
            if ( count ($arCids) > 0 ) {
                foreach ($arCids as $arCID) {
                    $obRFolhaPagamentoIRRF->addRPessoalCID();
                    $obRFolhaPagamentoIRRF->roRPessoalCID->setCodCID($arCID['cod_cid']);
                }
            }
            $arEventosAjudaCusto = Sessao::read("eventoAjudaCusto");
            if ( count ($arEventosAjudaCusto) > 0 ) {
                foreach ($arEventosAjudaCusto as $arEventoAjudaCusto) {
                    $obRFolhaPagamentoIRRF->addEventoAjudaCusto($arEventoAjudaCusto['flCodigoEvento']);
                }
            }
            $obErro = $obRFolhaPagamentoIRRF->alterarIRRF();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"IRRF","alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir";
        $obRFolhaPagamentoIRRF->setCodTabela ( $_REQUEST['inCodTabela'] );
        $obRFolhaPagamentoIRRF->setTimestamp ( $_REQUEST['stTimestamp'] );
        $obRFolhaPagamentoIRRF->setVigencia($_REQUEST['dtVigencia']);
        $obErro = $obRFolhaPagamentoIRRF->excluirIRRF();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,"IRRF: ".$_REQUEST['dtVigencia'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}

?>
