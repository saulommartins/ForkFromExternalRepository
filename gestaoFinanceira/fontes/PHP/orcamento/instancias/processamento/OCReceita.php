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
    * Arquivo oculto para busca de Receita
    * Data de Criação: 09/05/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Id: OCReceita.php 64153 2015-12-09 19:16:02Z evandro $

    Casos de uso: uc-02.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );

function buscaReceitaCod()
{
    global $request;
    $stJs = empty($stJs) ? "" : $stJs;
    if ($_GET['stUsaEntidade'] == "S") {
        if ( ( $request->get($request->get('stNomSelectMultiplo')) && is_array($request->get($request->get('stNomSelectMultiplo'))) ) || ( $_GET['inCodEntidade'] && !is_array($_GET['inCodEntidade']) ) ) {
            if ( $request->get($request->get('stNomSelectMultiplo')) && is_array($request->get($request->get('stNomSelectMultiplo'))) ) {
                $stEntidades = "";
                foreach ($request->get($request->get('stNomSelectMultiplo')) as $key => $valor) {
                    $stEntidades .= $valor . ",";
                }
                $stEntidades = substr($stEntidades, 0, strlen($stEntidades) - 1);
            } elseif ( $_GET['inCodEntidade'] && !is_array($_GET['inCodEntidade']) ) {
                $stEntidades = $_GET['inCodEntidade'];
            }
            if ($_GET[$_GET['stNomCampoCod']]) {
                $rsReceita = buscaReceita( $_REQUEST['tipoBusca'], $stEntidades );
                if ($rsReceita->getNumLinhas() > 0) {
                    $stDescricao = $rsReceita->getCampo ('descricao');
                } else {
                    $boVerificador = verificaReceitaLancamento();
                    if ( $boVerificador ) {
                        $stJs .= "alertaAviso('Necessário configurar o Lançamento de Receita da Conta ". $_GET[$_GET['stNomCampoCod']]."','frm','erro','".Sessao::getId()."'); \n";
                    }else{
                        $stJs .= "alertaAviso('Receita inválida para a entidade selecionada.','frm','erro','".Sessao::getId()."'); \n";
                    }
                }
            }
       } else {
            $stJs .= "alertaAviso('É necessário informar uma entidade para a receita.','frm','erro','".Sessao::getId()."'); \n";
       }
    } else {
        if ($_GET[$_GET['stNomCampoCod']]) {
            $rsReceita = buscaReceita( $_REQUEST['tipoBusca'] );
            if ($rsReceita->getNumLinhas() > 0) {
                $stDescricao = $rsReceita->getCampo( "descricao" );
            } else {
                $stJs .= "alertaAviso('Receita inválida.(".$_GET[$_GET['stNomCampoCod']].")','aviso','aviso','" . Sessao::getId() . "');\n";
            }
        }
    }
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."')";

    return $stJs;
}

function verificaReceitaLancamento() {
    #Verifica se o problema é nao estar configurado o lancamento de receita
    #tabela contabilidade.configuracao_lançamento_receita
    $obMapeamento = new TOrcamentoReceita();
    $boVerificador = false;
    $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'";
    $stFiltro .= " AND RECEITA.cod_receita = ". $_GET[$_GET['stNomCampoCod']];
    if($stEntidades)
        $stFiltro .= " AND RECEITA.cod_entidade in (".$stEntidades.") ";
    $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                  FROM contabilidade.desdobramento_receita as dr
                 WHERE   receita.cod_receita = dr.cod_receita_secundaria
                   AND receita.exercicio   = dr.exercicio ) ";
    $obMapeamento->recuperaLancamentoReceita($rsLancamentoReceita, $stFiltro);
    if ($rsLancamentoReceita->getNumLinhas() > 0)
        $boVerificador = true;
    return $boVerificador;  
}

function buscaReceita($stTipoBusca, $stEntidades = "")
{
    switch ($stTipoBusca) {
        case 'contArrec': // Arrecadação via mod. Contabilidade - Não listar receitas dedutoras
            $obMapeamento = new TOrcamentoReceita();
            $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= " AND RECEITA.cod_receita = ". $_GET[$_GET['stNomCampoCod']];
            if($stEntidades)
                $stFiltro .= " AND RECEITA.cod_entidade in (".$stEntidades.") ";
            if ( Sessao::getExercicio() < 2008 ) {
                $stFiltro .= " AND CPC.cod_estrutural not like '4.9.%'";
            } else {
                $stFiltro .= " AND CPC.cod_estrutural not like '9.%'";
            }
            $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                              FROM contabilidade.desdobramento_receita as dr
                                             WHERE   receita.cod_receita = dr.cod_receita_secundaria
                                                 AND receita.exercicio   = dr.exercicio ) ";
            if ( Sessao::getExercicio() > '2012' ) {
                $obMapeamento->recuperaReceitaAnaliticaTCE($rsRecordSet, $stFiltro);
            } else {
                $obMapeamento->recuperaReceitaAnalitica($rsRecordSet, $stFiltro);
            }
        break;

        case 'retencoes':
        $obMapeamento = new TOrcamentoReceita();
            $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= " AND RECEITA.cod_receita = ". $_GET[$_GET['stNomCampoCod']];
            if($stEntidades)
                $stFiltro .= " AND RECEITA.cod_entidade in (".$stEntidades.") ";
            $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                              FROM contabilidade.desdobramento_receita as dr
                                             WHERE   receita.cod_receita = dr.cod_receita_secundaria
                                                 AND receita.exercicio   = dr.exercicio ) ";
            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= " AND CLR.estorno = 'false' ";
                $obMapeamento->recuperaReceitaAnaliticaTCE($rsRecordSet, $stFiltro);
            } else {
                $obMapeamento->recuperaReceitaAnalitica($rsRecordSet, $stFiltro);
            }

        break;

        case 'receitaDedutora':
        case 'receitaDedutoraExportacao':
            $obMapeamento = new TOrcamentoReceita();
            $stFiltro  = " AND ORE.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND ORE.cod_receita = ". $_GET[$_GET['stNomCampoCod']];
            $stFiltro .= " AND OCR.cod_estrutural like '9.%' ";
            $obMapeamento->recuperaRelacionamentoContaReceita($rsRecordSet, $stFiltro);
        break;

        default: // Tras todas do exercicio
            $obMapeamento = new TOrcamentoReceita();
            $stFiltro  = " AND ORE.exercicio = '".Sessao::getExercicio()."' ";
            $stFiltro .= " AND ORE.cod_receita = ". $_GET[$_GET['stNomCampoCod']];
            $obMapeamento->recuperaRelacionamentoContaReceita($rsRecordSet, $stFiltro);
        break;
    }

    return $rsRecordSet;
}
$stJs = isset($stJs) ? $stJs : '';
switch ($_GET['stCtrl']) {
    case 'buscaReceitaCod':
        $stJs .= buscaReceitaCod();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
