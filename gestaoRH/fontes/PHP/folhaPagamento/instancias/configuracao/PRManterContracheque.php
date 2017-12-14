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
    * Data de Criação: 02/08/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-07 09:43:09 -0300 (Ter, 07 Ago 2007) $

    * Casos de uso: uc-04.05.63
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoContracheque.class.php");
$obTFolhaPagamentoConfiguracaoContracheque = new TFolhaPagamentoConfiguracaoContracheque();

$stPrograma = 'ManterContracheque';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFolhaPagamentoConfiguracaoContracheque );
$obTFolhaPagamentoConfiguracaoContracheque->excluirTodos();
$arConfiguracoes = Sessao::read("arConfiguracoes");
foreach ($arConfiguracoes as $arConfiguracao) {
    $obTFolhaPagamentoConfiguracaoContracheque->setDado("nom_campo",$arConfiguracao["stCampoId"]);
    $obTFolhaPagamentoConfiguracaoContracheque->setDado("linha",$arConfiguracao["inLinha"]);
    $obTFolhaPagamentoConfiguracaoContracheque->setDado("coluna",$arConfiguracao["inColuna"]);
    $obTFolhaPagamentoConfiguracaoContracheque->inclusao();
    $obTFolhaPagamentoConfiguracaoContracheque->setDado("cod_configuracao_contra","");
}
$stMensagem = "Configuração dos campos do contracheque concluída.";
SistemaLegado::LiberaFrames();
sistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>
