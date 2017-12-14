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
    * Página de geração do recordSet para o Relatório Metas de Execução da Despesa
    * Data de Criação   : 28/08/2006

    * @author Analista: Diego Vitoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.33
*/

/*
$Log$
Revision 1.7  2006/10/24 13:57:14  bruce
Bug #7201#

Revision 1.6  2006/09/25 12:06:26  cleisson
Bug #7031#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoDespesa.class.php"                          );

$obPrevisao = new TOrcamentoPrevisaoDespesa;

$obRelatorio = new RRelatorio;

/*Passando os filtros para listar as despesas cadastradas*/

//montagem do filtro

$arCondicoes = array();

// array de filtros sera usada para mostrar os filtros usados no fim do relatorio
$arFiltroRelatorio = Sessao::read('filtroRelatorio');

if (count( $arFiltroRelatorio['inCodEntidade'] ) > 0 ) {
    $arEntidades = $arFiltroRelatorio['inCodEntidade'];
    $stEntidades = implode ( ' , ' , $arEntidades );
    $arCondicoes[] = "despesa.cod_entidade  in ( $stEntidades  )";

    /// procurando os dados das entidades escolhidas para colocar no filtro
    include_once ( CAM_GF_ORC_MAPEAMENTO. 'TOrcamentoEntidade.class.php' );
    $obEntidade = new TOrcamentoEntidade;
    $obEntidade->setDado('exercicio', $arFiltroRelatorio['exercicio'] );

    foreach ($arEntidades as $entidade) {
        $stCondicao = " and E.cod_entidade = $entidade ";
        $obEntidade->recuperaEntidades( $rsEntidade, $stCondicao );

        $Filtro['filtro'] = 'Entidade';
        $Filtro['valor']  = $entidade .' - '.$rsEntidade->getCampo ( 'nom_cgm' ) ;
        $arFiltros[]      = $Filtro;
    }
}

if ($arFiltroRelatorio['stExercicio']) {
    $arCondicoes[] = ' despesa.exercicio = '. $arFiltroRelatorio['stExercicio'];
    $Filtro['filtro'] = 'Exercício';
    $Filtro['valor']  = $arFiltroRelatorio['stExercicio'];
    $arFiltros[]      = $Filtro;
}

$arOrgaoUnidade = explode( '.', $arFiltroRelatorio['stDotacaoOrcamentaria'] );

if ( ($arOrgaoUnidade[0] != '00') and ( $arOrgaoUnidade[0] != '' ) ) {
    $arCondicoes[] = ' despesa.num_orgao = ' . $arOrgaoUnidade[0] ;

    include_once ( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoUnidade.class.php' );
    $obOrgao = new TOrcamentoUnidade;
    $stCondicao  = " and orgao.exercicio = " . $arFiltroRelatorio['stExercicio'] ;
    $stCondicao .= " and orgao.num_orgao = " . $arOrgaoUnidade[0];

    $obOrgao->recuperaRelacionaMento( $rsOrgao, $stCondicao );

    $Filtro['filtro'] = 'Orgão';
    $Filtro['valor']  = $arOrgaoUnidade[0]  .' - '. $rsOrgao->getCampo( 'nom_orgao' ) ;
    $arFiltros[]      = $Filtro;
}

if ( ( $arOrgaoUnidade[1] != '00' ) and ( $arOrgaoUnidade[1] != '' ) ) {
    $arCondicoes[] = ' despesa.num_unidade = ' .  $arOrgaoUnidade[1] ;

    $obOrgao = new TOrcamentoUnidade;
    $stCondicao  = " and orgao.exercicio = " . $arFiltroRelatorio['stExercicio']   ;
    $stCondicao .= " and orgao.num_orgao = " . $arOrgaoUnidade[0];
    $stCondicao .= " and unidade.num_unidade = " . $arOrgaoUnidade[1];

    $obOrgao->recuperaRelacionaMento( $rsOrgao, $stCondicao );

    $Filtro['filtro'] = 'Unidade';
    $Filtro['valor']  = $arOrgaoUnidade[1]  . ' - '. $rsOrgao->getCampo( 'nom_unidade' );
    $arFiltros[]      = $Filtro;
}

if ($arFiltroRelatorio['inCodRecurso']) {
    $arCondicoes[] = 'despesa.cod_recurso = '. $arFiltroRelatorio['inCodRecurso'];

    // passando dados do recurso para o Filtro
    include_once ( CAM_GF_ORC_MAPEAMENTO. 'TOrcamentoRecurso.class.php');
    $obRecurso = new TOrcamentoRecurso;
    $obRecurso->setDado( 'inExercicio', $arFiltroRelatorio['stExercicio'] );
    $stCondicao = " and RO.cod_recurso = " . $arFiltroRelatorio['inCodRecurso'];
    $obRecurso->recuperaDadosExportacao( $rsRecurso, $stCondicao );

    $Filtro['filtro'] = 'Recurso';
    $Filtro['valor']  = $arFiltroRelatorio['inCodRecurso'] .' - '. $rsRecurso->getCampo('nom_recurso');
    $arFiltros[]      = $Filtro;
}

if ($arFiltroRelatorio['inCodUso'] && $arFiltroRelatorio['inCodDestinacao'] && $arFiltroRelatorio['inCodEspecificacao']) {
    $arCondicoes[] = "recurso.masc_recurso_red like '".$arFiltroRelatorio['inCodUso'].".".$arFiltroRelatorio['inCodDestinacao'].".".$arFiltroRelatorio['inCodEspecificacao']."%' ";
    $Filtro['filtro'] = 'Destinação de Recurso';
    $Filtro['valor'] = $arFiltroRelatorio['inCodUso'].".".$arFiltroRelatorio['inCodDestinacao'].".".$arFiltroRelatorio['inCodEspecificacao'].".".$arFiltroRelatorio['inCodDetalhamento'];
    $arFiltros[] = $Filtro;
}

if ($arFiltroRelatorio['inCodDotacaoInicial']) {
    $arCondicoes[] = ' despesa.cod_despesa >= '. $arFiltroRelatorio['inCodDotacaoInicial'] ;

    include_once ( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoDespesa.class.php' );
    $obDespesa = new TOrcamentoDespesa;
    $obDespesa->setDado( 'exercício', $arFiltroRelatorio['stExercicio'] );
    $stCondicao = ' AND O.cod_despesa = '. $arFiltroRelatorio['inCodDotacaoInicial'];
    $obDespesa->recuperaDespesa( $rsDespesa, $stCondicao );

    $Filtro['filtro'] = 'Dotação inicial';
    $Filtro['valor']  = $arFiltroRelatorio['inCodDotacaoInicial'] . ' - '. $rsDespesa->getCampo('descricao');
    $arFiltros[]      = $Filtro;
}

if ($arFiltroRelatorio['inCodDotacaoFinal']) {
    $arCondicoes[] =  ' despesa.cod_despesa <= '. $arFiltroRelatorio['inCodDotacaoFinal'];

    include_once ( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoDespesa.class.php' );
    $obDespesa = new TOrcamentoDespesa;
    $obDespesa->setDado( 'exercício', $arFiltroRelatorio['stExercicio'] );
    $stCondicao = ' AND O.cod_despesa = '. $arFiltroRelatorio['inCodDotacaoFinal'];
    $obDespesa->recuperaDespesa( $rsDespesa, $stCondicao );

    $Filtro['filtro'] = 'Dotação final';
    $Filtro['valor']  = $arFiltroRelatorio['inCodDotacaoFinal']. ' - '. $rsDespesa->getCampo('descricao');
    $arFiltros[]      = $Filtro;
}

if ( count( $arCondicoes ) > 0 ) {
    $stFiltro = implode ( ' and ', $arCondicoes );
}

$obPrevisao->setDado('exercicio', $arFiltroRelatorio['stExercicio'] );

if ($arFiltroRelatorio['boDemonstrarSintéticas'] == 'S') {
    $obPrevisao->recuperaPrevisoesSintetico( $rsMetasDespesa, $stFiltro );
} else {
    $obPrevisao->recuperaPrevisoesAnalitico( $rsMetasDespesa, $stFiltro );
}

$arMetas = array();

$inCont = 0;

while ( !$rsMetasDespesa->eof() ) {
   $arMetas[$rsMetasDespesa->getCampo('cod_conta')]['descricao']                          = $rsMetasDespesa->getCampo('descricao')     ;
   $arMetas[$rsMetasDespesa->getCampo('cod_conta')]['cod_estrutural']                     = $rsMetasDespesa->getCampo('cod_estrutural');
   $arMetas[$rsMetasDespesa->getCampo('cod_conta')][$rsMetasDespesa->getCampo('periodo')] = $rsMetasDespesa->getCampo('vl_previsto')   ;
   $rsMetasDespesa->proximo();
}

/// pegando o numero de periodos usado pelo sistema
$inColunas = count( current( $arMetas ) ) - 2;

/// calculando o total Ano de cada linha
$arDados = array();
foreach ($arMetas as $indice => $arLinha) {
    $arMetas[$indice]['totalAno'] = 0;
    for ($inCont= 1 ; $inCont <= $inColunas; $inCont++) {
        $arMetas[$indice]['totalAno'] = $arMetas[$indice]['totalAno'] + $arMetas[$indice][$inCont];
    }
    $arDados[] = $arMetas[$indice];
}

Sessao::write('arDados',$arDados);
Sessao::write('inColunas',$inColunas);
Sessao::write('filtros',$arFiltros);
//sessao->transf5['dados']    = $arDados;
//sessao->transf5['periodos'] = $inColunas;
//sessao->transf5['filtros']  = $arFiltros;

$obRelatorio->executaFrameOculto('OCGeraMetasExecucaoDespesa.php');

?>
