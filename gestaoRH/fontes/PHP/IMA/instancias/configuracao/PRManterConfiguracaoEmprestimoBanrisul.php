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
 * Página de Configuração de Empréstimos do Banrisul
 * Data de Criação   : 06/09/2009

 * @author Analista      Dagiane
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConfiguracaoBanrisulEmprestimo.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php';

$stPrograma = "ManterConfiguracaoEmprestimoBanrisul";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgProx = $pgForm;

Sessao::setTrataExcecao(true);
Sessao::getExcecao()->setLocal('telaprincipal');
$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
$obTFolhaPagamentoEvento->setDado('codigo', $_REQUEST['inCodigoEvento'] );
$obTFolhaPagamentoEvento->listar($rsListaEvento);

$obTIMAConfiguracaoBanrisulEmprestimo = new TIMAConfiguracaoBanrisulEmprestimo;
$obTIMAConfiguracaoBanrisulEmprestimo->setDado('cod_evento', $rsListaEvento->getCampo('cod_evento'));

$obErro = $obTIMAConfiguracaoBanrisulEmprestimo->excluirTodos();
if (!$obErro->ocorreu()) {
    $obErro = $obTIMAConfiguracaoBanrisulEmprestimo->inclusao();
}
Sessao::encerraExcecao();

sistemaLegado::alertaAviso($pgForm,"Configuração Empréstimos Banrisul concluída com sucesso!" ,$stAcao?$stAcao:'incluir',"aviso",Sessao::getId(),"../");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
