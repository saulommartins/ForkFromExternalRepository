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
    * Data de Criação   : 13/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * $Id: PROrgaoOrcamentario.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.02
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_NEGOCIO . 'ROrcamentoOrgaoOrcamentario.class.php';
include CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "OrgaoOrcamentario";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrgaoOrcamentario  = new ROrcamentoOrgaoOrcamentario;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro();

//recupera os exercicios do ppa para propagar o recurso pelos exercicios do ppa
$obRPPAManterPPA = new RPPAManterPPA();
$obRPPAManterPPA->stExercicio = Sessao::getExercicio();
$obRPPAManterPPA->listByExercicio($rsRecordSet);

$stExercicio      = (int) Sessao::getExercicio();
$stExercicioFinal = (int) $rsRecordSet->getCampo('ano_final');

switch ($stAcao) {
    case "incluir":
        //seta os dados q são comuns a todos os exercicios
        $arOrgao = explode('§', $_POST[$_POST['stNivel']]);
        $obROrgaoOrcamentario->setNumeroOrgao   ($_REQUEST['inNumeroOrgao']);
        $obROrgaoOrcamentario->setNomeOrgao     ($_REQUEST['stNomeOrgao']);
        $obROrgaoOrcamentario->setCodResponsavel($_REQUEST['inCGM']);

        //faz o insert para cada ano até o ano_final do ppa
        do {
            if (!$obErro->ocorreu()) {
                $obROrgaoOrcamentario->setExercicio     ($stExercicio);
                $obErro = $obROrgaoOrcamentario->salvar ($boTransacao);
            }
            $stExercicio++;
        } while ( $stExercicio <= $stExercicioFinal && $rsRecordSet->getNumLinhas() >= 1 );
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Orgão número: ".$_POST['inNumeroOrgao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "excluir":
        //seta o dado comum a todas as exclusões
        $obROrgaoOrcamentario->setNumeroOrgao($_GET['inNumeroOrgao']);

        //faz o insert para cada ano até o ano_final do ppa
        do {
            if (!$obErro->ocorreu()) {
                $obROrgaoOrcamentario->setExercicio     ($stExercicio);
                $obErro = $obROrgaoOrcamentario->excluir($boTransacao);
            }
            $stExercicio++;
        } while ( $stExercicio <= $stExercicioFinal && $rsRecordSet->getNumLinhas() >= 1 );

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        if ( count($arFiltro) > 0 ) {
            foreach ($arFiltro as $stCampo => $stValor) {
                $stFiltro .= $stCampo."=".urlencode( (string) $stValor )."&";
            }
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Orgão número: ".$_GET['inNumeroOrgao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, urlencode("Não é possível excluir órgão que está em uso no sistema"), "n_excluir", "erro", Sessao::getId(), "../");
        }
    break;

}
?>
