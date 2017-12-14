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
    * Página de Filtro para Relatório de Ïtens
    * Data de Criação   : 24/01/2006

    * @author Gelson W. Gonçalves

    * @ignore

    * $Id: OCGeraMovimentacaoEstoque.php 44404 2010-06-04 12:57:34Z davi.aroldi $

    * Casos de uso : uc-03.03.24
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,14);

// Nova versão
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Inventário');

$preview->setNomeArquivo('relatorioInventario');

if (count($_REQUEST['inCodAlmoxarifadoSelecionado'])>0) {
    foreach ($_REQUEST['inCodAlmoxarifadoSelecionado'] as $array) {
        $arrAlmoxarifado.=  $array.",";
    }
    $arrAlmoxarifado=substr($arrAlmoxarifado,0,strlen($arrAlmoxarifado)-1);
    $preview->addParametro( "codAlmoxarifado",$arrAlmoxarifado );
} else {
    $preview->addParametro( "codAlmoxarifado","" );
}
$dadosInventario = explode('-',$_REQUEST['idInventario']);
$exercicio       = $dadosInventario[0];
$codAlmoxarifado = $dadosInventario[1];
$codInventario   = $dadosInventario[2];

$preview->addParametro( "exercicio_inventario", $exercicio);
$preview->addParametro( "cod_almoxarifado", $codAlmoxarifado );
$preview->addParametro( "cod_inventario", $codInventario );

if (!empty($_REQUEST['stOrdem'])) {
    switch ($_REQUEST['stOrdem']) {
        case "classificacao" : $preview->addParametro( "tipo_ordenacao", "catalogo_classificacao.cod_estrutural,catalogo_item.descricao_resumida" ); break;
        case "item"          : $preview->addParametro( "tipo_ordenacao", "catalogo_item.cod_item" );                break;
        case "descricao"     : $preview->addParametro( "tipo_ordenacao", "catalogo_item.descricao_resumida" ); break;
    }
}

if (!empty($_REQUEST['stTipoRelatorio'])) {
   $preview->addParametro( "tipo_relatorio", $_REQUEST['stTipoRelatorio']);
}

if ($_REQUEST['stGrupoAlmoxarifado'] != '') {
    $arGrupo[] = $_REQUEST['stGrupoAlmoxarifado'];
}

$preview->addParametro( 'cgm_usuario',Sessao::read('numCgm') );

# comentado pois será feito um ticket para mudar a apresentação das assinaturas, por enquanto elas não devem aparecer
#$preview->addParametro ("incluir_assinaturas"	  , $boIncluirAssinaturas);
#$preview->addAssinaturas( Sessao::read('assinaturas'));

$preview->preview();
