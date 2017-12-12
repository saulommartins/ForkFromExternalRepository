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
    * Página de Processamento de Comissao de Avaliacao
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-06-25 13:10:49 -0300 (Seg, 25 Jun 2007) $

    * Casos de uso: uc-02.01.03
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "PAO";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoProjetoAtividade  = new ROrcamentoProjetoAtividade;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro();

switch ($stAcao) {
    case "incluir":
        $arProjetoAtividade = Sessao::read('arProjetoAtividade');
        $stPAO = '';

        foreach ($arProjetoAtividade AS $arProjetoAtividadeTMP) {
            $obROrcamentoProjetoAtividade->setNumeroProjeto( $arProjetoAtividadeTMP['num_pao'] );
            $obROrcamentoProjetoAtividade->setNome         ( $arProjetoAtividadeTMP['nom_pao'] );
            $obROrcamentoProjetoAtividade->setExercicio    ( $arProjetoAtividadeTMP['exercicio'] );

            $obROrcamentoProjetoAtividade->consultar($rsProjetoAtividade);
            if ($rsProjetoAtividade->getNumLinhas() <= 0) {
                $obROrcamentoProjetoAtividade->setDetalhamento ( $arProjetoAtividadeTMP['detalhamento'] );
                $obErro = $obROrcamentoProjetoAtividade->incluir();
                $stPAO .= $arProjetoAtividadeTMP['num_pao'] . ' ,';
            }
        }

        if ( !$obErro->ocorreu() ) {
            $stFiltro = "inTipoPAO=".$_POST['inTipoPAO'];
            SistemaLegado::alertaAviso($pgForm."?".$stFiltro,"PAO: ".substr($stPAO,0,-1), "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obROrcamentoProjetoAtividade->setMascara       ( $_POST['stMascara']      );
        $arNumProjeto = explode( "/" , $_POST['inNumeroProjeto'] );
        $obROrcamentoProjetoAtividade->setNumeroProjeto( $arNumProjeto[0]         );
        $obROrcamentoProjetoAtividade->setExercicio    ( $arNumProjeto[1]         );
        $obROrcamentoProjetoAtividade->setNome         ( $_POST['stNome']         );
        $obROrcamentoProjetoAtividade->setDetalhamento ( $_POST['stDetalhamento'] );
        $obErro = $obROrcamentoProjetoAtividade->alterar();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"PAO: ".$arNumProjeto[0]." - ".$_POST['stNome'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "excluir":
        $obROrcamentoProjetoAtividade->setMascara       ( $_POST['stMascara']      );
        $obROrcamentoProjetoAtividade->setNumeroProjeto( $_GET['inNumeroProjeto'] );
        $obROrcamentoProjetoAtividade->setExercicio    ( $_GET['stExercicio']   );
        $obErro = $obROrcamentoProjetoAtividade->excluir();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,"PAO: ".$_GET['inNumeroProjeto']." - ".$_GET['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}
?>
