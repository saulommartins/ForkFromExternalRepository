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
    * Página de Processamento do Instituição de Ensino
    * Data de Criação: 03/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30843 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-06 14:43:57 -0300 (Sex, 06 Out 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEnsino.class.php"                             );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsino.class.php"                        );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioCursoInstituicaoEnsinoMes.class.php"                     );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioInstituicaoEntidade.class.php"                           );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php"                             );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterInstituicaoEnsino";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTEstagioInstituicaoEnsino        = new TEstagioInstituicaoEnsino();
$obTEstagioCursoInstituicaoEnsino   = new TEstagioCursoInstituicaoEnsino();
$obTEstagioCursoInstituicaoEnsinoMes= new TEstagioCursoInstituicaoEnsinoMes();
$obTEstagioInstituicaoEntidade      = new TEstagioInstituicaoEntidade();
$obTEstagioEstagiarioEstagio        = new TEstagioEstagiarioEstagio();
$obTEstagioCursoInstituicaoEnsino->obTEstagioInstituicaoEnsino = &$obTEstagioInstituicaoEnsino;
$obTEstagioInstituicaoEntidade->obTEstagioInstituicaoEnsino = &$obTEstagioInstituicaoEnsino;
$obTEstagioCursoInstituicaoEnsinoMes->obTEstagioCursoInstituicaoEnsino = &$obTEstagioCursoInstituicaoEnsino;

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $obTEstagioInstituicaoEnsino->setDado("numcgm",$_POST['inCGM']);
        $obTEstagioInstituicaoEnsino->inclusao();
        foreach (Sessao::read('arCursos') as $arCurso) {
            $obTEstagioCursoInstituicaoEnsino->setDado("cod_curso",$arCurso['inCodCurso']);
            $obTEstagioCursoInstituicaoEnsino->setDado("vl_bolsa",$arCurso['nuValorBolsa']);
            $obTEstagioCursoInstituicaoEnsinoMes->setDado("cod_mes",$arCurso['inCodMes']);
            $obTEstagioCursoInstituicaoEnsino->inclusao();
            if ($arCurso['inCodMes'] != "") {
                $obTEstagioCursoInstituicaoEnsinoMes->inclusao();
            }
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Instituição ".$_POST['inCGM']."-".$_POST['stNomCGM'],"incluir","aviso", Sessao::getId(), "../");
    break;
    case "alterar":
        Sessao::setTrataExcecao(true);
        $obTEstagioInstituicaoEnsino->setDado("numcgm",$_POST['inCGM']);
        $stFiltro = " WHERE cgm_instituicao_ensino = ".$_POST['inCGM'];
        $obTEstagioEstagiarioEstagio->recuperaCursosVinculadosAEstagiario($rsEstagiario,$stFiltro);
        $stCurso = "";
        $arCursos = array();
        while (!$rsEstagiario->eof()) {
            $stCurso .= $rsEstagiario->getCampo("cod_curso").",";
            $arCursos[] = $rsEstagiario->getCampo("cod_curso");
            $rsEstagiario->proximo();
        }
        $stCurso = substr($stCurso,0,strlen($stCurso)-1);
        $obTEstagioCursoInstituicaoEnsino->setDado("cod_curso",$stCurso);
        $obTEstagioCursoInstituicaoEnsinoMes->exclusaoPorCGMCurso();
        $obTEstagioCursoInstituicaoEnsino->exclusaoPorCGMCurso();
        foreach (Sessao::read('arCursos') as $arCurso) {
            $obTEstagioCursoInstituicaoEnsino->setDado("cod_curso",$arCurso['inCodCurso']);
            $obTEstagioCursoInstituicaoEnsino->setDado("vl_bolsa",$arCurso['nuValorBolsa']);
            if ( in_array($arCurso['inCodCurso'],$arCursos) ) {
                $obTEstagioCursoInstituicaoEnsino->alteracao();
            } else {
                $obTEstagioCursoInstituicaoEnsino->inclusao();
            }
            if ($arCurso['inCodMes'] != "") {
                $obTEstagioCursoInstituicaoEnsinoMes->recuperaPorChave($rsMes);
                $obTEstagioCursoInstituicaoEnsinoMes->setDado("cod_mes",$arCurso['inCodMes']);
                if ( $rsMes->getNumLinhas() > 0 ) {
                    $obTEstagioCursoInstituicaoEnsinoMes->alteracao();
                } else {
                    $obTEstagioCursoInstituicaoEnsinoMes->inclusao();
                }
            }
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Instituição ".$_POST['inCGM']."-".$_POST['stNomCGM'],"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $obTEstagioInstituicaoEnsino->setDado("numcgm",$_GET['inNumCGM']);
        $obTEstagioInstituicaoEntidade->recuperaPorChave($rsEntidadeIntermediadora);
        $stFiltro = " WHERE cgm_instituicao_ensino = ".$_GET['inNumCGM'];
        $obTEstagioEstagiarioEstagio->recuperaTodos($rsEstagiario,$stFiltro);
        if ( $rsEntidadeIntermediadora->getNumLinhas() > 0 ) {
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgList,"A instituição de ensino ".$_GET['inNumCGM']."-".$_GET['stNomCGM']." está vinculada a uma ou mais entidade(s) intermediadora(s)","n_excluir","erro", Sessao::getId(), "../");
        } elseif ( $rsEstagiario->getNumLinhas() > 0 ) {
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgList,"A instituição de ensino ".$_GET['inNumCGM']."-".$_GET['stNomCGM']." está vinculada a um ou mais estagiário(s) através de um de seus cursos","n_excluir","erro", Sessao::getId(), "../");
        } else {
            $obTEstagioCursoInstituicaoEnsinoMes->exclusao();
            $obTEstagioCursoInstituicaoEnsino->exclusao();
            $obTEstagioInstituicaoEnsino->exclusao();
            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso($pgList,"Instituição de ensino ".$_GET['inNumCGM']."-".$_GET['stNomCGM'],"excluir","aviso", Sessao::getId(), "../");
        }
    break;
}

?>
