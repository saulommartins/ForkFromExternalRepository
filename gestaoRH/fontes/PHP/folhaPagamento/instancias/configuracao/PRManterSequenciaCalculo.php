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
* Página de Processamento de Sequência de Cálculo
* Data de Criação: 05/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30711 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSequencia.class.php"   );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$link = Sessao::read("link");
//Define o nome dos arquivos PHP
$stPrograma = "ManterSequenciaCalculo";
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRSequencia  = new RFolhaPagamentoSequencia;

switch ($stAcao) {
    case "incluir":
        $obRSequencia->setSequencia   ( $_POST['inSequencia']   );
        $obRSequencia->setDescricao   ( $_POST['stDescricao']   );
        $obRSequencia->setComplemento ( $_POST['stComplemento'] );
        $obErro = $obRSequencia->incluirSequencia($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Sequência: ".$_POST['inSequencia'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRSequencia->setCodSequencia ( $_POST['inCodSequencia'] );
        $obRSequencia->setSequencia    ( $_POST['inSequencia']    );
        $obRSequencia->setDescricao    ( $_POST['stDescricao']    );
        $obRSequencia->setComplemento  ( $_POST['stComplemento']  );
        $obErro = $obRSequencia->alterarSequencia($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Sequência: ".$_POST['inSequencia'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRSequencia->setCodSequencia ( $_GET['inCodSequencia'] );
        $obErro = $obRSequencia->excluirSequencia($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Sequência: ".$_GET['inSequencia'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
