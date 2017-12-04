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
    * Página de Relatório da Evolução da Receita
    * Data de Criação  : 16/07/2008

    * @author Leopoldo Braga Barreiro

    * Casos de uso : uc-02.01.37

    * $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"                                    );

$preview = new PreviewBirt( 2, 8, 3 );
$preview->setTitulo( 'Relatório do Birt' );
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel(true);

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "and e.cod_entidade in (" . implode(',', $_REQUEST['inCodEntidade']) . ")" );

// Parametros do Relatorio

// Nome da Entidade

$stNomeEntidadePrincipal = "";
$arNomesEntidades = array();

if ( count($_REQUEST['inCodEntidade']) == 1 ) {

     $preview->addParametro( 'nom_entidade', $rsEntidade->getCampo('nom_cgm') );
     $arNomesEntidades[] = $rsEntidade->getCampo('nom_cgm');

} else {

    $inCodEntidadePrefeitura = SistemaLegado::pegaDado('valor','administracao.configuracao'," WHERE parametro = 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 8 ");

    while ( !$rsEntidade->eof() ) {

        if ( $rsEntidade->getCampo('cod_entidade') == $inCodEntidadePrefeitura ) {
            $stNomeEntidadePrincipal = $rsEntidade->getCampo('nom_cgm');
        }
        $arNomesEntidades[] = $rsEntidade->getCampo('nom_cgm');
        $rsEntidade->proximo();

    }
}

$preview->addParametro( 'nom_entidade', $stNomeEntidadePrincipal );
$preview->addParametro( 'nomes_entidades', implode(", ", $arNomesEntidades));

// Exercicio

$preview->addParametro( 'exercicio', $_REQUEST['stExercicio'] );

// Entidades

$preview->addParametro( 'cod_entidade', implode(",", $_REQUEST['inCodEntidade']) );

// Demonstrar Sintéticas

$inSinteticas = (strtolower($_REQUEST['stDemonstraSinteticas']) == "s") ? 1 : 0;
$preview->addParametro( 'sinteticas', $inSinteticas );

// Codigo de Recurso

$inCodRecurso = (integer) $_REQUEST['inCodRecurso'];

if ($inCodRecurso > 0) {
    $preview->addParametro('cod_recurso', $inCodRecurso);
} else {
    $preview->addParametro('cod_recurso', 0);
}

// Assinaturas

$arAssinaturaSelecionada = array();

if ($_REQUEST['stIncluirAssinaturas'] == "sim") {

    $arAssinatura = Sessao::read('assinaturas');

    if ( is_array($arAssinatura) && isset($arAssinatura['selecionadas']) && count($arAssinatura['selecionadas']) > 0 ) {

        $arAssinaturaSelecionada = $arAssinatura['selecionadas'];

        for ( $x = 0; $x < count($arAssinaturaSelecionada); $x++ ) {
            $stParametroCgm = "assinatura_" . $arAssinaturaSelecionada[$x]['papel'];
            $stParametroCargo = "cargo_" . $arAssinaturaSelecionada[$x]['papel'];
            $preview->addParametro( $stParametroCgm, $arAssinaturaSelecionada[$x]['stNomCGM']);
            $preview->addParametro( $stParametroCargo, $arAssinaturaSelecionada[$x]['stCargo']);
        }
    }
}

$arParametros = $preview->arParametros;

if (!isset($arParametros['assinatura_1'])) {
    $preview->addParametro('assinatura_1', '');
    $preview->addParametro('cargo_1', '');
}
if (!isset($arParametros['assinatura_2'])) {
    $preview->addParametro('assinatura_2', '');
    $preview->addParametro('cargo_2', '');
}
if (!isset($arParametros['assinatura_3'])) {
    $preview->addParametro('assinatura_3', '');
    $preview->addParametro('cargo_3', '');
}

// Exibição do Relatorio Birt

$preview->preview();
