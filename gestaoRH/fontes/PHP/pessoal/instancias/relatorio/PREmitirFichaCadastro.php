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
    * Página de Processo do Relatório Emitir Ficha de Cadastro
    * Data de Criação : 24/06/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30860 $
    $Name$
    $Autor: $
    $Date: 2008-01-11 15:26:27 -0200 (Sex, 11 Jan 2008) $

    * Casos de uso: uc-04.04.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$ParametroNovo = " , ? ";

//Define o nome dos arquivos PHP
$stPrograma = "EmitirFichaCadastro";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $arContratos = Sessao::read('arContratos');
        if (count($arContratos)>0) {
            foreach ($arContratos as $array) {
                $arrContrato.=  $array['inContrato'].",";
            }
        $arrContrato = substr($arrContrato,0,strlen($arrContrato)-1);

        } else {
            $arrContrato = "null";
        }
        break;
    case "lotacao_grupo":
        $arrLotacao = implode(",",$_POST['inCodLotacaoSelecionados']);
        $stAgrupamento = "lotacao";
        if ($_REQUEST['boAgrupar']) {
            $stQuebraLotacao = "true";
        } else {
            $stQuebraLotacao = "false";
        }
        break;
    case "local_grupo":
        $arrLocal = implode(",",$_POST['inCodLocalSelecionados']);
        $stAgrupamento = "local";
        if ($_REQUEST['boAgrupar']) {
            $stQuebraLocal = "true";
        } else {
            $stQuebraLocal = "false";
        }
        break;
    case "atributo_servidor_grupo":
        if (count($_REQUEST['inCodAtributo'])>0) {
            $ValorAtributo = $_REQUEST['Atributo_'.$_REQUEST['inCodAtributo'].'_5'];

            if ($ValorAtributo == '' and is_array($_REQUEST['Atributo_'.$_REQUEST['inCodAtributo'].'_5_Selecionados']) ) {
                foreach ($_REQUEST['Atributo_'.$_REQUEST['inCodAtributo'].'_5_Selecionados'] as $array) {
                    $arrAtributo.= $array.",";
                }
                $ValorAtributo = substr($arrAtributo, 0, strlen($arrAtributo)-1);
            }

            if ($ValorAtributo == '') {
                $ValorAtributo = "null";
            }

            $atrSQL = $_REQUEST['inCodAtributo'];

            if ($atrSQL=='') {
                $atrSQL = "null";
            }

            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
            include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
            $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico;
            $obTAdministracaoAtributoDinamico->setDado("cod_modulo",22);
            $obTAdministracaoAtributoDinamico->setDado("cod_cadastro",5);
            $obTAdministracaoAtributoDinamico->setDado("cod_atributo",$_POST["inCodAtributo"]);
            $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributo);
            $inCodTipoAtributo = $rsAtributo->getCampo("cod_tipo");

            if ($_REQUEST['boAgrupar']) {
                $stQuebraAtributo =  "true";
            } else {
                $stQuebraAtributo = "false";
            }
        }
        break;
}

$preview = new PreviewBirt(4,22,5);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Emitir Ficha Cadastral do Servidor');
$preview->setNomeArquivo('EmitirFichaCadastral');
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stcodEntidade", Sessao::getCodEntidade($boTransacao));
$preview->addParametro("stDescAtributo", $_REQUEST["stDescCadastro"]);
$preview->addParametro( "codMatricula",$arrContrato);
$preview->addParametro("codLotacao", $arrLotacao);
$preview->addParametro("stQuebraLotacao", $stQuebraLotacao);
$preview->addParametro("codLocal", $arrLocal);
$preview->addParametro("stQuebraLocal", $stQuebraLocal);
$preview->addParametro("vlrAtributo", $ValorAtributo  );
$preview->addParametro("Atributo", $atrSQL            );
$preview->addParametro("inCodTipoAtributo", $inCodTipoAtributo  );
$preview->addParametro("stQuebraAtributo", $stQuebraAtributo);
$preview->setFormato('pdf');

//Listar Demitidos
if ($_REQUEST['boListarDemitidos'] == 1) {
    $preview->addParametro("stListarDemitidos", "true");
} else {
    $preview->addParametro("stListarDemitidos", "false");
}

$preview->addParametro("stAgrupamento", $stAgrupamento);
$preview->preview();
?>
