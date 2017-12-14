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
    * Oculto
    * Data de Criação   : 01/08/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-30 10:38:15 -0300 (Qui, 30 Ago 2007) $

    * Caso de uso: uc-04.05.63
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

switch ($request->get('stCtrl')) {
    case "incluirConfiguracao":
        $stJs = incluirConfiguracao($request);
        break;
    case "alterarConfiguracao":
        $stJs = alterarConfiguracao();
        break;
    case "excluirConfiguracao":
        $stJs = excluirConfiguracao();
        break;
    case "montaAlterarConfiguracao":
        $stJs = montaAlterarConfiguracao();
        break;
    case "montaConfiguracao":
        $stJs = montaConfiguracao();
        break;
}

function validaConfiguracao()
{
    $obErro = new Erro();
    $arConfiguracoes = Sessao::read("arConfiguracoes");
    if (is_array($arConfiguracoes)) {
        foreach ($arConfiguracoes as $arConfiguracao) {
            if ($arConfiguracao["stCampoId"] == $_GET["stCampo"] and $arConfiguracao["inId"] != Sessao::read("inId")) {
                $obErro->setDescricao("O campo selecionado já se encontra na lista de configurações do contracheque.");
                break;
            }
            if ($arConfiguracao["inColuna"] == $_GET["inColuna"] and $arConfiguracao["inLinha"] == $_GET["inLinha"]) {
                $obErro->setDescricao("Já existe um campo inserido na linha ".$_GET["inLinha"]." e coluna ".$_GET["inColuna"].".");
                break;
            }
        }
    }
    if ($_GET["inLinha"]%5 !== 0) {
        $obErro->setDescricao("O campo Linha deve ser informado com um valor múltiplo de 5, ou seja, valores válidos 5, 10, 15, 20, ...");
    }

    return $obErro;
}

function incluirConfiguracao($request)
{
    $obErro = validaConfiguracao();
    if (!$obErro->ocorreu()) {
        $arConfiguracoes = Sessao::read("arConfiguracoes");
        $inId = count($arConfiguracoes)+1;
        $arCampos = Sessao::read("arCampos");
        foreach ($arCampos as $arCampo) {
            if ($arCampo["cod"] == $request->get("stCampo")) {
                $stCampoDesc = $arCampo["desc"];
            }
        }
        $arConfiguracao["inId"]         = $inId;
        $arConfiguracao["stCampoId"]    = $request->get("stCampo");
        $arConfiguracao["stCampoDesc"]  = $stCampoDesc;
        $arConfiguracao["inColuna"]     = $request->get("inColuna");
        $arConfiguracao["inLinha"]      = $request->get("inLinha");
        $arConfiguracoes[]              = $arConfiguracao;
        Sessao::write("arConfiguracoes",$arConfiguracoes);
        $stJs .= montaConfiguracao();
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function alterarConfiguracao()
{
    $obErro = validaConfiguracao();
    if (!$obErro->ocorreu()) {
        $arConfiguracoes = Sessao::read("arConfiguracoes");
        $inId = Sessao::read("inId");
        $arCampos = Sessao::read("arCampos");
        foreach ($arCampos as $arCampo) {
            if ($arCampo["cod"] == $_GET["stCampo"]) {
                $stCampoDesc = $arCampo["desc"];
            }
        }
        $arConfiguracao["inId"]         = $inId;
        $arConfiguracao["stCampoId"]    = $_GET["stCampo"];
        $arConfiguracao["stCampoDesc"]  = $stCampoDesc;
        $arConfiguracao["inColuna"]     = $_GET["inColuna"];
        $arConfiguracao["inLinha"]      = $_GET["inLinha"];
        $arConfiguracoes[$inId-1]        = $arConfiguracao;
        Sessao::write("arConfiguracoes",$arConfiguracoes);

        $stJs .= montaConfiguracao();
        $stJs .= "f.btIncluirConfiguracao.disabled = false;\n";
        $stJs .= "f.btAlterarConfiguracao.disabled = true;\n";
        Sessao::write("inId","");
    } else {
        $stJs = "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirConfiguracao()
{
    $arTemp = array();
    $arConfiguracoes = Sessao::read("arConfiguracoes");
    foreach ($arConfiguracoes as $arConfiguracao) {
        if ($arConfiguracao["inId"] != $_GET["inId"]) {
            //$arConfiguracao["inId"] = count($arConfiguracoes)+1;
            $arTemp[] = $arConfiguracao;
        }
    }
    Sessao::write("arConfiguracoes",$arTemp);
    $stJs .= montaConfiguracao();

    return $stJs;
}

function montaAlterarConfiguracao()
{
    Sessao::write("inId",$_GET["inId"]);
    $arConfiguracoes = Sessao::read("arConfiguracoes");
    $arConfiguracao = $arConfiguracoes[$_GET["inId"]-1];
    $stJs  = "f.stCampo.value = '".$arConfiguracao["stCampoId"]."';\n";
    $stJs .= "f.inColuna.value = '".$arConfiguracao["inColuna"]."';\n";
    $stJs .= "f.inLinha.value = '".$arConfiguracao["inLinha"]."';\n";
    $stJs .= "f.btIncluirConfiguracao.disabled = true;\n";
    $stJs .= "f.btAlterarConfiguracao.disabled = false;\n";

    return $stJs;
}

function montaConfiguracao()
{
    $rsLista = new RecordSet();
    $arLista = is_array(Sessao::read("arConfiguracoes"))?Sessao::read("arConfiguracoes"):array();
    $rsLista->preenche($arLista);

    $obLista = new Table();
    $obLista->setRecordset($rsLista);
    $obLista->setSummary("Lista de Campos");

    $obLista->Head->addCabecalho("Campo",50);
    $obLista->Head->addCabecalho("Coluna",10);
    $obLista->Head->addCabecalho("Linha",10);

    $obLista->Body->addCampo( 'stCampoDesc', 'E' );
    $obLista->Body->addCampo( 'inColuna', 'D' );
    $obLista->Body->addCampo( 'inLinha', 'D' );

    $obLista->Body->addAcao("alterar","executaFuncaoAjax('%s','&inId=%s')",array('montaAlterarConfiguracao','inId'));
    $obLista->Body->addAcao("excluir","executaFuncaoAjax('%s','&inId=%s')",array('excluirConfiguracao','inId'));

    $obLista->montaHTML(true);
    $stHtml = $obLista->getHtml();

    $stJs .= "d.getElementById('spnConfiguracoes').innerHTML = '$stHtml';";

    return $stJs;
}

if ($stJs) {
   echo $stJs;
}
?>
