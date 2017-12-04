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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 27723 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-24 08:33:46 -0200 (Qui, 24 Jan 2008) $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php';

$stPrograma = "ManterBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgFormConsulta = "FMManterConsultarBem.php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."bem/";

$arFiltro = Sessao::read('filtro');
//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($request->get('pg') ? $request->get('pg') : 0));
    Sessao::write('pos',($request->get('pos') ? $request->get('pos') : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$request->get('pg'));
    Sessao::write('pos',$request->get('pos'));
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}
$_REQUEST['hdnEvalCombos']='';

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

//seta os filtros

if ($stAcao != 'consultar') {
    $stFiltro = " AND NOT EXISTS (  SELECT 1
                                     FROM patrimonio.bem_baixado
                                    WHERE bem_baixado.cod_bem = bem.cod_bem
                                 ) ";
}
//codigo do bem
if ($_REQUEST['inCodBem'] != '') {
    $stFiltro .= " AND bem.cod_bem = ".$_REQUEST['inCodBem']." ";
}
//codigo da natureza
if ($_REQUEST['inCodNatureza'] != '') {
    $stFiltro .= " AND bem.cod_natureza = ".$_REQUEST['inCodNatureza']." ";
}
//codigo do grupo
if ($_REQUEST['inCodGrupo'] != '') {
    $stFiltro .= " AND bem.cod_grupo = ".$_REQUEST['inCodGrupo']." ";
}
//codigo da especie
if ($_REQUEST['inCodEspecie'] != '') {
    $stFiltro .= " AND bem.cod_especie = ".$_REQUEST['inCodEspecie']." ";
}
//descricao do bem
if ($_REQUEST['stHdnDescricaoBem'] != '') {
    $stFiltro .= " AND bem.descricao ILIKE '".$_REQUEST['stHdnDescricaoBem']."' ";
}
//detalhamento do bem
if ($_REQUEST['stHdnDetalhamentoBem'] != '') {
    $stFiltro .= " AND bem.detalhamento ILIKE '".$_REQUEST['stHdnDetalhamentoBem']."' ";
}
//codigo da marca
if ($_REQUEST['inCodMarca'] != '') {
    $stFiltro .= " AND bem_marca.cod_marca = ".$_REQUEST['inCodMarca']." ";
}
//fornecedor
if ($_REQUEST['inCodFornecedor'] != '') {
    $stFiltro .= " AND bem.numcgm = ".$_REQUEST['inCodFornecedor']." ";
}
//valor do bem
if ($_REQUEST['inValorBem'] != '') {
    $stFiltro .= " AND bem.vl_bem = ".number_format(str_replace('.','',$_REQUEST['inValorBem']),2,'.','')." ";
}
//valor de depreciacao
if ($_REQUEST['inValorDepreciacao'] != '') {
    $stFiltro .= " AND bem.vl_depreciacao = ".number_format(str_replace('.','',$_REQUEST['inValorDepreciacao']),2,'.','')." ";
}
//data depreciacao
if ($_REQUEST['stDataInicialDepreciacao'] != '') {
    $stFiltro .= " AND depreciacao.dt_depreciacao BETWEEN TO_DATE('".$_REQUEST['stDataInicialDepreciacao']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinalDepreciacao']."','dd/mm/yyyy') ";
}
//data aquisicao
if ($_REQUEST['stDataInicialAquisicao'] != '') {
    $stFiltro .= " AND bem.dt_aquisicao BETWEEN TO_DATE('".$_REQUEST['stDataInicialAquisicao']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinalAquisicao']."','dd/mm/yyyy') ";
}
//data incorporacao
if ($_REQUEST['stDataInicialIncorporacao'] != '') {
    $stFiltro .= " AND bem.dt_incorporacao BETWEEN TO_DATE('".$_REQUEST['stDataInicialIncorporacao']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinalIncorporacao']."','dd/mm/yyyy') ";
}
//data vencimento
if ($_REQUEST['stDataInicialVencimento'] != '') {
    $stFiltro .= " AND bem.dt_depreciacao BETWEEN TO_DATE('".$_REQUEST['stDataInicialVencimento']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinalVencimento']."','dd/mm/yyyy') ";
}
//placa de identificacao
if ($_REQUEST['stPlacaIdentificacao'] == 'nao') {
    $stFiltro .= " AND bem.num_placa IS NULL ";
} elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
    $stFiltro .= " AND bem.num_placa IS NOT NULL ";
}
if ($_REQUEST['stNumeroPlaca'] != '') {
    $stFiltro .= " AND bem.num_placa ILIKE '".$_REQUEST['stNumeroPlaca']."' ";
}
//entidade
if ($_REQUEST['inCodEntidade'] != '') {
    $stFiltro .= " AND bem_comprado.cod_entidade = ".$_REQUEST['inCodEntidade']." ";
}
//exercicio
if ($_REQUEST['stExercicio'] != '') {
    $stFiltro .= " AND bem_comprado.exercicio = '".$_REQUEST['stExercicio']."' ";
}
//empenho
if ($_REQUEST['inNumEmpenho'] != '') {
    $stFiltro .= " AND bem_comprado.cod_empenho = ".$_REQUEST['inNumEmpenho']." ";
}
//nota fiscal
if ($_REQUEST['stNumNotaFiscal'] != '') {
    $stFiltro .= " AND bem_comprado.nota_fiscal = ".$_REQUEST['stNumNotaFiscal']." ";
}
//responsavel pelo bem
if ($_REQUEST['inNumResponsavel'] != '') {
    $stFiltro .= " AND bem_responsavel.numcgm = ".$_REQUEST['inNumResponsavel']." ";
}
//Data de inicio do responsavel
if ($_REQUEST['stDataInicialResponsavel'] != '') {
    $stFiltro .= " AND bem_responsavel.dt_inicio BETWEEN TO_DATE('".$_REQUEST['stDataInicialResponsavel']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinalResponsavel']."','dd/mm/yyyy') ";
}

//orgao
if ($_REQUEST['hdnUltimoOrgaoSelecionado'] != '') {
    $stFiltro .= " AND historico_bem.cod_orgao = ".$_REQUEST['hdnUltimoOrgaoSelecionado'];
}

//local
if ($_REQUEST['inCodLocal'] != '') {
    $stFiltro .= " AND historico_bem.cod_local = ".$_REQUEST['inCodLocal']." ";
}
//situacao
if ($_REQUEST['inCodSituacao'] != '') {
    $stFiltro .= " AND historico_bem.cod_situacao = ".$_REQUEST['inCodSituacao']." ";
}
//descricao da situacao
if ($_REQUEST['stHdnDescricaoSituacao']) {
    $stFiltro .= " AND historico_bem.descricao ILIKE '".$_REQUEST['stHdnDescricaoSituacao']."' ";
}

if ($_REQUEST['stBemBaixado'] == 'sim') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM patrimonio.bem_baixado
                                 WHERE bem_baixado.cod_bem = bem.cod_bem
                              ) ";
}
if ($_REQUEST['stBemBaixado'] == 'nao') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                      FROM patrimonio.bem_baixado
                                     WHERE bem_baixado.cod_bem = bem.cod_bem
                                  ) ";
}

// se acao for excluir, retorna so os grupos sem especies vinculadas
if ($stAcao == 'excluir') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                      FROM frota.proprio
                                     WHERE proprio.cod_bem = bem.cod_bem
                                  ) ";
}

//filtro pelos atributos dinâmicos
if (count($_REQUEST['atributos']) > 0) {
    foreach ($_REQUEST['atributos'] as $index => $valor) {
    if ($valor != '') {
        $arKeyAtributo = explode(',', $index);

        $stFiltro .= "AND EXISTS ( SELECT 1
                        FROM patrimonio.bem_atributo_especie
                       WHERE bem_atributo_especie.cod_bem = bem.cod_bem
                         AND cod_modulo = ".$arKeyAtributo[0]."
                         AND cod_cadastro = ".$arKeyAtributo[1]."
                         AND cod_atributo = ".$arKeyAtributo[2]."
                         AND valor ilike '".$valor."'
                    ) ";
    }
    }
}

//ordenacao
if ($_REQUEST['stOrdenacao'] == 'cod_bem') {
    $stOrdem = ' ORDER BY  bem.cod_bem ';
} elseif ($_REQUEST['stOrdenacao'] == 'descricao') {
    $stOrdem = ' ORDER BY bem.descricao ';
} elseif ($_REQUEST['stOrdenacao'] == 'classificacao') {
    $stOrdem = ' ORDER BY bem.cod_natureza, bem.cod_grupo, bem.cod_especie,bem.cod_bem';
} elseif ($_REQUEST['stOrdenacao'] == 'placa') {
    $stOrdem = ' ORDER BY bem.num_placa ';
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}
$obTPatrimonioBem = new TPatrimonioBem();
$obTPatrimonioBem->recuperaRelacionamento( $rsBem, $stFiltro, $stOrdem );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('UC-03.01.06');
$stLink .= "&stAcao=".$stAcao;

$obLista->setRecordSet( $rsBem );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Placa" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_natureza].[cod_grupo].[cod_especie]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_bem" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_placa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_padrao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodBem", "cod_bem" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "descricao" );

if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'consultar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormConsulta."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
