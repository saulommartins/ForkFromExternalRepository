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
    * Página Oculta - Exportação Arquivos GF

    * Data de Criação   : 27/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php";
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAUnidadeOrcamentaria.class.php";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$stJs = "";
switch ($_REQUEST['stCtrl']) {
case "montaDados":

    // limpa o select de opções selecionadas
    $stJs .= "\n for ( var chave in $('arCodUnidadesOrcamentariasDisponiveis').options ) { ";
    $stJs .= "\n    if ($('arCodUnidadesOrcamentariasDisponiveis').options[chave])";
    $stJs .= "\n        $('arCodUnidadesOrcamentariasDisponiveis').options[chave] = null;";
    $stJs .= "\n }";
    $stJs .= "\n ";

    // limpa o select de opções selecionadas
    $stJs .= "\n for ( var chave in $('arCodUnidadesOrcamentariasSelecionadas').options ) { ";
    $stJs .= "\n    if ($('arCodUnidadesOrcamentariasSelecionadas').options[chave])";
    $stJs .= "\n        $('arCodUnidadesOrcamentariasSelecionadas').options[chave] = null;";
    $stJs .= "\n }";
    $stJs .= "\n ";

    if ($_REQUEST['inCodEntidade'] != '') {

        $stSchema = ($_REQUEST['inCodEntidade'] != 1 ? "pessoal_".$_REQUEST['inCodEntidade'] : "pessoal");
        $stFiltro = "\n AND EXISTS (SELECT cod_orgao"
                   ."\n             FROM ".$stSchema.".contrato_servidor_orgao"
                   ."\n             WHERE contrato_servidor_orgao.cod_orgao = orgao.cod_orgao"
                   ."\n             GROUP BY cod_orgao"
                   ."\n             ORDER BY cod_orgao)  ";

        $stFiltro .= "\n AND NOT EXISTS (SELECT cod_orgao"
                   ."\n                 FROM tcmpa.unidade_orcamentaria"
                   ."\n                 WHERE unidade_orcamentaria.cod_orgao = orgao.cod_orgao"
                   ."\n                   AND unidade_orcamentaria.exercicio = ".Sessao::getExercicio()
                   ."\n                 GROUP BY cod_orgao"
                   ."\n                 ORDER BY cod_orgao)  ";
        $stOrder = "\n ORDER BY cod_estrutural";
        $obTOrganogramaOrgao = new TOrganogramaOrgao();
        $obTOrganogramaOrgao->recuperaDadosComboUnidadeOrcamentaria( $rsOrganogramaOrgao, $stFiltro, $stOrder );

        $count = 0;
        while (!$rsOrganogramaOrgao->eof()) {
            $stCodigo = $rsOrganogramaOrgao->getCampo('cod_orgao')."-".$rsOrganogramaOrgao->getCampo('cod_nivel')."-".$rsOrganogramaOrgao->getCampo('cod_organograma');
            $stDescricao = $rsOrganogramaOrgao->getCampo('cod_estrutural')." - ".$rsOrganogramaOrgao->getCampo('descricao');
            $stJs .= "\n $('arCodUnidadesOrcamentariasDisponiveis').options[".$count."] = new Option('".$stDescricao."', '".$stCodigo."', '');";
            $rsOrganogramaOrgao->proximo();
            $count++;
        }

        $stFiltro = "\n AND EXISTS (SELECT cod_orgao"
                   ."\n              FROM tcmpa.unidade_orcamentaria"
                   ."\n              WHERE unidade_orcamentaria.cod_orgao = orgao.cod_orgao"
                   ."\n                AND unidade_orcamentaria.exercicio = ".Sessao::getExercicio()
                   ."\n                AND unidade_orcamentaria.cod_entidade = ".$_REQUEST['inCodEntidade']
                   ."\n              GROUP BY cod_orgao"
                   ."\n              ORDER BY cod_orgao)";
        $stOrder = "\n ORDER BY cod_estrutural";
        $obTOrganogramaOrgao = new TOrganogramaOrgao();
        $obTOrganogramaOrgao->recuperaDadosComboUnidadeOrcamentaria( $rsOrganogramaOrgao, $stFiltro, $stOrder );

        $count = 0;
        while (!$rsOrganogramaOrgao->eof()) {
            $stCodigo = $rsOrganogramaOrgao->getCampo('cod_orgao')."-".$rsOrganogramaOrgao->getCampo('cod_nivel')."-".$rsOrganogramaOrgao->getCampo('cod_organograma');
            $stDescricao = $rsOrganogramaOrgao->getCampo('cod_estrutural')." - ".$rsOrganogramaOrgao->getCampo('descricao');
            $stJs .= "\n $('arCodUnidadesOrcamentariasSelecionadas').options[".$count."] = new Option('".$stDescricao."', '".$stCodigo."', '');";
            $rsOrganogramaOrgao->proximo();
            $count++;
        }

        $obTTPAUnidadeOrcamentaria = new TTPAUnidadeOrcamentaria();
        $obTTPAUnidadeOrcamentaria->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTTPAUnidadeOrcamentaria->setDado('exercicio'   , Sessao::getExercicio()        );
        $obTTPAUnidadeOrcamentaria->recuperaUnidadeGestora( $rsUnidadeOrcamentaria );

        $stJs .= "\n $('inCodUnidadeGestora').value = '".$rsUnidadeOrcamentaria->getCampo('unidade_gestora')."'";
    }

    break;
}

echo $stJs;

?>
