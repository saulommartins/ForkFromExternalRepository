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
* Página de processamento da linha
* Data de Criação: 07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_NEGOCIO."RBeneficioLinha.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterLinha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRBeneficioLinha = new RBeneficioLinha;

switch ($stAcao) {
    case "incluir":
        $rsLinha = new Recordset;
        $obRBeneficioLinha->setDescricao( $_POST['stDescricaoLinha'] );
        $stFiltro = " WHERE UPPER(descricao) = '".strtoupper($obRBeneficioLinha->getDescricao())."'";
        $obErro = $obRBeneficioLinha->listarLinha($rsLinha,$stFiltro,$stOrder,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            if ( $rsLinha->getNumLinhas() == -1 ) {
                $obErro = $obRBeneficioLinha->incluirLinha($boTransacao);
                if ( !$obErro->ocorreu() ) {
                    sistemaLegado::alertaAviso($pgForm,"Linha: ".$_POST['stDescricaoLinha'],"incluir","aviso", Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
            } else {
                $stMensagem = "Descrição de linha já cadastrada.";
                sistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
            }
        }
    break;

    case "alterar":
        $obRBeneficioLinha->setCodLinha    ( $_POST['inCodLinha']       );
        $obRBeneficioLinha->setDescricao   ( $_POST['stDescricaoLinha'] );
        $stFiltro = " WHERE UPPER(descricao)  = '".strtoupper($obRBeneficioLinha->getDescricao())."' \n";
        $stFiltro .= " AND cod_linha <> '".$obRBeneficioLinha->getCodLinha()."'";
        $obErro = $obRBeneficioLinha->listarLinha($rsLinha,$stFiltro,$stOrder,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            if ( $rsLinha->getNumLinhas() == -1 ) {
                $obErro = $obRBeneficioLinha->alterarLinha($boTransacao);
                if ( !$obErro->ocorreu() ) {
                    sistemaLegado::alertaAviso($pgList,"Linha: ".$_POST['stDescricaoLinha'],"alterar","aviso", Sessao::getId(), "../");
                } else {
                    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                }
            } else {
                $stMensagem = "Descrição de linha já cadastrada.";
                sistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
            }
        }
    break;

    case "excluir";
        $obRBeneficioLinha->setCodLinha    ( $_REQUEST['inCodLinha']       );
        $obErro = $obRBeneficioLinha->excluirLinha($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Linha: ".$_REQUEST['stDescricaoLinha'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Linha: ".urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

?>
