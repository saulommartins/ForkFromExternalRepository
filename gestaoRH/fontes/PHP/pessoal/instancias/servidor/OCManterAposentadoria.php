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
    * Página de Oculto de Aposentadoria
    * Data de Criação: 21/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30876 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//Define o nome dos arquivos PHP
$stPrograma = "ManterAposentadoria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function preencherEnquadramento()
{
    if ($_GET['inCodClassificacao'] != '') {
        include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
        $obTPessoalEnquadramento = new TPessoalEnquadramento;
        $stFiltro = " AND classificacao_enquadramento.cod_classificacao = ".$_GET['inCodClassificacao'];
        $obTPessoalEnquadramento->recuperaRelacionamento($rsEnquadramento,$stFiltro);
        $stJs.= "limpaSelect(f.inCodEnquadramento,0);                                 \n";
        $stJs.= "f.inCodEnquadramento[0] = new Option('Selecione','','selected');     \n";
        $inIndex = 1;
        while (!$rsEnquadramento->eof()) {
            $stJs.= "f.inCodEnquadramento[".$inIndex."] = new Option('".$rsEnquadramento->getCampo('descricao')."','".$rsEnquadramento->getCampo("cod_enquadramento")."','');     \n";
            $inIndex++;
            $rsEnquadramento->proximo();
        }
    } else {
            $stJs.= "limpaSelect(f.inCodEnquadramento,0);                                 \n";
        $stJs.= "f.inCodEnquadramento[0] = new Option('Selecione','','selected');     \n";
            $stJs.= "d.getElementById('stTipoReajuste').innerHTML = ''";
        }

        return $stJs;
}

function preencherReajuste()
{
    if ($_GET['inCodEnquadramento'] != '') {
        include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
        $obTPessoalEnquadramento = new TPessoalEnquadramento;

        $stFiltro = " AND classificacao_enquadramento.cod_classificacao = ".$_GET['inCodClassificacao'];
        $stFiltro .= " AND enquadramento.cod_enquadramento = ".$_GET['inCodEnquadramento'];
        $obTPessoalEnquadramento->recuperaRelacionamento($rsEnquadramento,$stFiltro);
        $stJs.= "d.getElementById('stTipoReajuste').innerHTML = '".$rsEnquadramento->getCampo("reajuste")."';     \n";

        return $stJs;
    }
}

function preencherAlterar()
{
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
    $obTPessoalEnquadramento = new TPessoalEnquadramento;
    $stFiltro = " AND enquadramento.cod_enquadramento = ".$_GET['inCodEnquadramento'];
    $obTPessoalEnquadramento->recuperaRelacionamento($rsEnquadramento,$stFiltro);
    $stJs .= preencherEnquadramento();
    $stJs .= "f.inCodEnquadramento.value = ".$_GET['inCodEnquadramento'].";\n";
    $stJs .= "d.getElementById('stTipoReajuste').innerHTML = '".$rsEnquadramento->getCampo("reajuste")."';     \n";

    return $stJs;
}

function preencherExcluir()
{
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacao.class.php");
    $obTPessoalEnquadramento = new TPessoalEnquadramento;
    $obTPessoalClassificacao = new TPessoalClassificacao;
    $stFiltro = " AND enquadramento.cod_enquadramento = ".$_GET['inCodEnquadramento'];
    $obTPessoalEnquadramento->recuperaRelacionamento($rsEnquadramento,$stFiltro);
    $stFiltro = " WHERE cod_classificacao = ".$_GET['inCodClassificacao'];
    $obTPessoalClassificacao->recuperaTodos($rsClassificacao,$stFiltro);
    $stJs .= "d.getElementById('stClassificacao').innerHTML = '".$rsClassificacao->getCampo("nome_classificacao")."';\n";
    $stJs .= "d.getElementById('stEnquadramento').innerHTML = '".$rsEnquadramento->getCampo("descricao")."';\n";
    $stJs .= "d.getElementById('stTipoReajuste').innerHTML = '".$rsEnquadramento->getCampo("reajuste")."';     \n";

    return $stJs;
}

function excluir()
{
    global $pgProc;
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
    $obTPessoalContrato = new TPessoalContrato();
    $obTPessoalContrato->setDado("cod_contrato",$_GET['inContrato']);
    $obTPessoalContrato->recuperaPorChave($rsContrato);
    $stID = str_replace("&","*_*", Sessao::getId());
    $stJs .= "alertaQuestao('".CAM_GRH_PES_INSTANCIAS."servidor/".$pgProc."?".$stID."*_*stAcao=excluir*_*inContrato=".$_GET['inContrato']."*_*stDescQuestao=Confirma a exclusão do registro de aposentadoria para a matrícula ".$rsContrato->getCampo("registro")." (".$_GET['hdnCGM'].")','sn_excluir','".Sessao::getId()."');  \n";

    return $stJs;
}

//Verifica se Data de Concessão é anterior a Data de Requerimento
function comparaDatas()
{
    ;
    $dtConcessao                 = $_GET['dtConcessao'];
    $dtRequerimentoAposentadoria = $_GET['dtRequerimentoAposentadoria'];
    $dtPublicacao                = $_GET['dtPublicacao'];
    $dtEncerramento				 = trim($_GET['dtEncerramento']);
    $stJs = "";
    if ( $dtConcessao != "" and sistemaLegado::comparaDatas($dtRequerimentoAposentadoria,$dtConcessao) ) {
        $stMensagem = " Data de concessão (".$dtConcessao.") não pode ser anterior à Data de Requerimento (".$dtRequerimentoAposentadoria.") !";
        $stJs .= "f.dtConcessao.value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    }
    if ( $dtPublicacao != "" and sistemaLegado::comparaDatas($dtRequerimentoAposentadoria,$dtPublicacao) ) {
        $stMensagem = " Data de publicação (".$dtPublicacao.") não pode ser anterior à Data de Requerimento (".$dtRequerimentoAposentadoria.") !";
        $stJs .= "f.dtPublicacao.value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    }
    if ($dtPublicacao != "" and $dtConcessao != "") {
        if ( $dtRequerimentoAposentadoria != "" and sistemaLegado::comparaDatas($dtRequerimentoAposentadoria,$dtPublicacao) or sistemaLegado::comparaDatas($dtRequerimentoAposentadoria,$dtConcessao) ) {
            $stMensagem = " Data de Requerimento não pode ser posterior as datas de Concessão e Aposentadoria !";
            $stJs .= "f.dtRequerimentoAposentadoria.value = '';\n";
            $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        }
    }
    if ( $dtEncerramento != "" and (sistemaLegado::comparaDatas($dtConcessao,$dtEncerramento) or sistemaLegado::comparaDatas($dtRequerimentoAposentadoria,$dtEncerramento) or sistemaLegado::comparaDatas($dtPublicacao,$dtEncerramento)) ) {
        $stMensagem = " Data de Encerramento (".$dtEncerramento.") não pode ser anterior as datas de Concessão, Requerimento e Publicação!";
        $stJs .= "f.dtEncerramento.value = '';\n";
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
    }

    return $stJs;
}

function submeter()
{
    $obErro = new Erro();
    if ($_GET['inContrato'] == "") {
        $obErro->setDescricao("Campo Matrícula inválido!()");
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalAposentadoria = new TPessoalAposentadoria();
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltro = " WHERE registro = ".$_GET['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
        $stFiltro = " AND aposentadoria.cod_contrato = ".$rsContrato->getCampo("cod_contrato");

        $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
        if ( $rsAposentadoria->getNumLinhas() < 0 ) {
            $obErro->setDescricao("Servidor não registrado no cadastro de aposentadorias.");
        }
    }
    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
    } else {
        $stJs .= "parent.frames[2].Salvar();";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "preencherEnquadramento":
        $stJs.= preencherEnquadramento();
    break;
    case "preencherReajuste":
        $stJs.= preencherReajuste();
    break;
    case "preencherAlterar":
        $stJs.= preencherAlterar();
    break;
    case "preencherExcluir":
        $stJs.= preencherExcluir();
    break;
    case "excluir":
        $stJs.= excluir();
    break;
    case "submeter":
        $stJs .= submeter();
    break;
    case "comparaDatas":
        $stJs.= comparaDatas();
    break;
}

if ($stJs) {
    echo $stJs;
}
?>
