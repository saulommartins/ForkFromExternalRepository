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
  * Data de criação : 18/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

  $Id: OCGeraRelatorioApoliceSeguros.php 66415 2016-08-25 14:36:56Z lisiane $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,3);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('apoliceSeguros');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

// Seta o parâmetro cod_apolice no relatório.
$inCodApolice = $request->get('inCodApolice');
if ( !empty($inCodApolice) ) {
    $preview->addParametro( 'cod_apolice', $inCodApolice );
}

// Seta o parâmetro num_apolice no relatório.
$inNumApolice = $request->get('inNumApolice');
if ( !empty($inNumApolice) ) {
    $preview->addParametro( 'num_apolice', $inNumApolice );
}

// Seta o parâmetro num_cgm no relatório.
$preview->addParametro( 'num_cgm', $request->get('inNumCGM'));
// Seta o parâmetro ordenacao no relatório.
$stOrdenacao = $request->get('stOrdenacao');
if ( !empty( $stOrdenacao )) {
    $preview->addParametro( 'ordenacao', $stOrdenacao);
}

// Seta o parâmetro cod_entidade no relatório.
$inCodEntidade = $request->get('inCodEntidade');
if ( !empty($inCodEntidade) ) {
    $preview->addParametro( 'entidade', $inCodEntidade);
}
$preview->preview();
