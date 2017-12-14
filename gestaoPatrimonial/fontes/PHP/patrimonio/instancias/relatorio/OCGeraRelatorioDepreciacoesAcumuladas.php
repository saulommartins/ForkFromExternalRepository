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
  * Página que abre o preview do relatório desenvolvido no Birt.
  * Data de criação : 10/12/2009

  * @copyright CNM Confederação Nacional dos Municípios.
  * @link http://www.cnm.org.br CNM Confederação Nacional dos Municípios.

  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,17);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');
$preview->setNomeArquivo('depreciacoes');
$preview->addParametro( 'exercicio', Sessao::getExercicio() );

$inCodNatureza = $request->get('inCodNatureza');
$inCodGrupo = $request->get('inCodGrupo');
$inCodEspecie = $request->get('inCodEspecie');
$inCodEntidade = $request->get('inCodEntidade');

$stFiltro = '';

if (!empty($inCodNatureza)) {
    $stFiltro  .= ' AND bem.cod_natureza = '.$inCodNatureza;
}

if (!empty($inCodGrupo)) {
    $stFiltro  .= ' AND bem.cod_grupo = '.$inCodGrupo;
}

if (!empty($inCodEspecie)) {
    $stFiltro .= ' AND bem.cod_especie  = '.$inCodEspecie;
}

if (!empty($inCodEntidade)) {
    $stFiltro .= ' AND bem_comprado.cod_entidade  = '.$inCodEntidade;
}

$preview->addParametro( 'stExercicio', $_POST['stExercicio'] );
$preview->addParametro( 'stFiltro', $stFiltro );
$preview->preview();

?>