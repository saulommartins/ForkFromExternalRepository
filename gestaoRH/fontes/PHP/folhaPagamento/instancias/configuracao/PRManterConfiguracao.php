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
    * Processamento
    * Data de Criação: 17/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-07 09:43:09 -0300 (Ter, 07 Ago 2007) $

    * Casos de uso: uc-04.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                              );

//Define o nome dos arquivos PHP
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php?";
$pgList = "LS".$stPrograma.".php?";
$pgForm = "FM".$stPrograma.".php?".$stLink;
$pgProc = "PR".$stPrograma.".php?";
$pgOcul = "OC".$stPrograma.".php?";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoconfiguracao = new RFolhaPagamentoConfiguracao;
$obErro = new Erro();
switch ($stAcao) {
    case "alterar":
        if ($_POST["stImpressora"] == "" and $_POST["stImpressao"] == "matricial") {
            $obErro->setDescricao("Para o tipo de impressão igual a matricial, deve ser informado a impressora no campo Impressora.");
        }
        if (!$obErro->ocorreu()) {
            $obRFolhaPagamentoconfiguracao->setMascaraEvento($_POST['inMascaraEvento']);
            $obRFolhaPagamentoconfiguracao->setApresentaAbaBase($_POST['boApresentaBase']);
            $obRFolhaPagamentoconfiguracao->setMensagemAniversariantes($_POST['stMensagemAniversariantes']);
            $obRFolhaPagamentoconfiguracao->setMesCalculoDecimo(12);
            $obRFolhaPagamentoconfiguracao->setImpressao($_POST["stImpressao"]);
            $obRFolhaPagamentoconfiguracao->setImpressora($_POST["stImpressora"]);
            $obErro = $obRFolhaPagamentoconfiguracao->salvar();
        }
        if ( !$obErro->ocorreu() ) {
            $stMensagem = "Configuração atualizada.";
            sistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}
?>
