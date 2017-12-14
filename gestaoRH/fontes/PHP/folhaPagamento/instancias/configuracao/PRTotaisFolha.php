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
/*
* Processamento para configuração de totais da folha
* Data de Criação   : 05/03/2009

* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

* @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTotaisFolhaEventos.class.php"                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoTotaisFolha.class.php"                        );

$stPrograma = "TotaisFolha";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?stAcao=".$_REQUEST["stAcao"];
$pgForm     = "FM".$stPrograma.".php?stAcao=".$_REQUEST["stAcao"];
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao     = trim($_REQUEST["stAcao"]);

$obTFolhaPagamentoTotaisFolhaEventos = new TFolhaPagamentoTotaisFolhaEventos;
$obTFolhaPagamentoConfiguracaoTotaisFolha = new TFolhaPagamentoConfiguracaoTotaisFolha;
$obTFolhaPagamentoTotaisFolhaEventos->obTFolhaPagamentoConfiguracaoTotaisFolha = &$obTFolhaPagamentoConfiguracaoTotaisFolha;

Sessao::setTrataExcecao(true);
$obTFolhaPagamentoTotaisFolhaEventos->excluirTodos();
$obTFolhaPagamentoConfiguracaoTotaisFolha->excluirTodos();

$arConfiguracoes = Sessao::read("arConfiguracoes");
foreach ($arConfiguracoes as $arConfiguracao) {
    $obTFolhaPagamentoConfiguracaoTotaisFolha->setDado("descricao",$arConfiguracao["descricao"]);
    $obTFolhaPagamentoConfiguracaoTotaisFolha->inclusao();
    foreach ($arConfiguracao["eventos"] as $inCodEvento) {
        $obTFolhaPagamentoTotaisFolhaEventos->setDado("cod_evento",$inCodEvento);
        $obTFolhaPagamentoTotaisFolhaEventos->inclusao();
    }
    $obTFolhaPagamentoConfiguracaoTotaisFolha->setDado("cod_configuracao","");
}
Sessao::encerraExcecao();
sistemaLegado::alertaAviso($pgForm,"Configuração dos totais da folha concluída com sucesso",$stAcao,"aviso",Sessao::getId(),"../");

?>
