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
    * Página de Oculto do Entidade Intermediadora
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-06 11:18:17 -0300 (Sex, 06 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeIntermediadora.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEntidade.class.php"                           );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEntidadeContribuicao.class.php"                           );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterEntidadeIntermediadora";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTEstagioEntidadeIntermediadora = new TEstagioEntidadeIntermediadora();
$obTEstagioInstituicaoEntidade = new TEstagioInstituicaoEntidade();
$obTEstagioEntidadeContribuicao = new TEstagioEntidadeContribuicao();

$obTEstagioInstituicaoEntidade->obTEstagioEntidadeIntermediadora = &$obTEstagioEntidadeIntermediadora;
$obTEstagioEntidadeContribuicao->obTEstagioEntidadeIntermediadora = &$obTEstagioEntidadeIntermediadora;

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $obTEstagioEntidadeIntermediadora->setDado("numcgm",$_POST['inCGM']);
        $obTEstagioEntidadeIntermediadora->setDado("percentual_atual",$_POST['nuPercentualContribuicao']);
        $obTEstagioEntidadeIntermediadora->inclusao();

        $obTEstagioEntidadeContribuicao->setDado("percentual",$_POST['nuPercentualContribuicao']);
        $obTEstagioEntidadeContribuicao->inclusao();

        foreach (Sessao::read('arInstituicoes') as $arInstituicao) {
            $obTEstagioInstituicaoEntidade->setDado("cgm_instituicao",$arInstituicao['inNumCGMInstituicao']);
            $obTEstagioInstituicaoEntidade->inclusao();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Entidade Intermediadora ".$_POST['inCGM']."-".$_POST['stNomCGM'],"incluir","aviso", Sessao::getId(), "../");
    break;
    case "alterar":
        Sessao::setTrataExcecao(true);
        $obTEstagioEntidadeIntermediadora->setDado("numcgm",$_POST['inCGM']);
        $obTEstagioEntidadeIntermediadora->recuperaTodos($rsEntidadeIntermediadora);

        $vAtual = preg_replace( '/[,.]/','', $rsEntidadeIntermediadora->getCampo('percentual_atual'));
        $vNovo = preg_replace( '/[,.]/','',$_POST['nuPercentualContribuicao']);

        if ($vAtual != $vNovo) {
            $obTEstagioEntidadeIntermediadora->setDado("percentual_atual",$_POST['nuPercentualContribuicao']);
            $obTEstagioEntidadeIntermediadora->alteracao();
            $obTEstagioEntidadeContribuicao->setDado("percentual",$_POST['nuPercentualContribuicao']);
            $obTEstagioEntidadeContribuicao->inclusao();
        }

        $obTEstagioInstituicaoEntidade->exclusao();
        foreach (Sessao::read('arInstituicoes') as $arInstituicao) {
            $obTEstagioInstituicaoEntidade->setDado("cgm_instituicao",$arInstituicao['inNumCGMInstituicao']);
            $obTEstagioInstituicaoEntidade->inclusao();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Entidade Intermediadora ".$_POST['inCGM']."-".$_POST['stNomCGM'],"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $obTEstagioEntidadeIntermediadora->setDado("numcgm",$_GET['inNumCGM']);
        $stFiltro = " AND entidade_intermediadora.numcgm = ".$_GET['inNumCGM'];
        $obTEstagioEntidadeIntermediadora->recuperaEstagiariosDaEntidade($rsEstagiarios,$stFiltro);
        if ( $rsEstagiarios->getNumLinhas() > 0 ) {
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgList,"A Entidade Intermediadora ".$_GET['inNumCGM']."-".$_GET['stNomCGM']." está vinculada a um ou mais estagiários","n_excluir","erro", Sessao::getId(), "../");
        } else {
            $obTEstagioEntidadeContribuicao->exclusao();
            $obTEstagioInstituicaoEntidade->exclusao();
            $obTEstagioEntidadeIntermediadora->exclusao();
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgList,"Entidade Intermediadora ".$_GET['inNumCGM']."-".$_GET['stNomCGM'],"excluir","aviso", Sessao::getId(), "../");
        }
    break;
}

?>
