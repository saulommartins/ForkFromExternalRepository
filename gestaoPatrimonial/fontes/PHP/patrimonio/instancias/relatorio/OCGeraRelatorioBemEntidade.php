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
/*
  * Página de geração de relatório
  * Data de criação : 19/01/2008

    * @author Analista:
    * @author Programador: Lucas Teixeira Stephano

    $Id: $

    Caso de uso: uc-03.01.20
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,11);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório de Bens por Entidade');

$preview->setNomeArquivo('relatorioBemEntidade');

$exercicio = Sessao::getExercicio();
$preview->addParametro( 'exercicio', $exercicio );

//seta a natureza
if ($_REQUEST['inCodNatureza'] != '') {
    $preview->addParametro( 'cod_natureza', $_REQUEST['inCodNatureza'] );
} else {
    $preview->addParametro( 'cod_natureza', '' );
}

//seta o grupo
if ($_REQUEST['inCodGrupo'] != '') {
    $preview->addParametro( 'cod_grupo', $_REQUEST['inCodGrupo'] );
} else {
    $preview->addParametro( 'cod_grupo', '' );
}

//seta a especie
if ($_REQUEST['inCodEspecie'] != '') {
    $preview->addParametro( 'cod_especie', $_REQUEST['inCodEspecie'] );
} else {
    $preview->addParametro( 'cod_especie', '' );
}
//seta descricao
if ($_REQUEST['stDescricaoBem'] != '') {
    $preview->addParametro('stDescricaoBem', $_REQUEST['stHdnDescricaoBem']);
} else {
    $preview->addParametro('stDescricaoBem', '');
}

//seta a entidade
if ($_REQUEST['inCodEntidade'] != '') {
    $preview->addParametro( 'cod_entidade', $_REQUEST['inCodEntidade'] );
} else {
    $preview->addParametro( 'cod_entidade', '' );
}

//seta o período
$preview->addParametro( 'data_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro( 'data_final'  , $_REQUEST['stDataFinal']  );

if ($_REQUEST['stOrdenacao'] != '') {
    $preview->addParametro( 'ordenacao', $_REQUEST['stOrdenacao'] );
} else {
    $preview->addParametro( 'ordenacao', '' );
}

# Local
if ($_REQUEST['inCodLocal'] != '' ) {
    $preview->addParametro( 'cod_local', $_REQUEST['inCodLocal'] );
} else {
    $preview->addParametro( 'cod_local', '' );
}

# Bem Baixado
if ($_REQUEST['boBemBaixado'] != '' ) {
    $preview->addParametro( 'bem_baixado', $_REQUEST['boBemBaixado'] );
} else {
    $preview->addParametro( 'bem_baixado', '' );
}

# Organograma
if ($_REQUEST['inCodOrganogramaAtivo'] != '') {
    if ($_REQUEST['inCodOrganogramaClassificacao'] != '') {

        if($_REQUEST['inCodOrganogramaAtivo'] != '')
        $boPermissaoHierarquica = SistemaLegado::pegaDado('permissao_hierarquica', 'organograma.organograma', ' WHERE cod_organograma = '.$_REQUEST['inCodOrganogramaAtivo']);

        if($_REQUEST['hdnUltimoOrgaoSelecionado'] != '')        
        $stOrgaoReduzido = SistemaLegado::pegaDado('orgao_reduzido', 'organograma.vw_orgao_nivel', ' WHERE cod_orgao = '.$_REQUEST['hdnUltimoOrgaoSelecionado']);
        
        if ($boPermissaoHierarquica == 't'){
            $preview->addParametro( 'cod_orgao', $stOrgaoReduzido.'%' );
        } else {
            $preview->addParametro( 'cod_orgao', $stOrgaoReduzido );
        }
    } else {

    }

    $preview->addParametro( 'cod_organograma', $_REQUEST['inCodOrganogramaAtivo'] );
} else {
    $preview->addParametro( 'cod_organograma', '' );
}

switch ($_REQUEST['boAgrupar']) {
    case 'classificacao':
        $preview->addParametro( 'agrupar' , 'classificacao' );
    break;

    case 'local':
        $preview->addParametro( 'agrupar'   , 'local' );
    break;

    case 'organograma':
        $preview->addParametro( 'agrupar'   , 'organograma' );
    break;
}

$preview->addParametro( 'cod_organograma', '' );
$preview->addParametro( 'cod_orgao', '');

$preview->preview();
