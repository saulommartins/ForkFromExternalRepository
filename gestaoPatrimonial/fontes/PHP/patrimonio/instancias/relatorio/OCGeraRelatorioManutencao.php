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
  * Data de criação : 22/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,5);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('manutencao');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );
$preview->addParametro( 'entidade', $request->get('inCodEntidade') );
// Seta o parâmetro para data inicial e final no relatório.
if ( !empty($_REQUEST['stDtInicial']) && !empty($_REQUEST['stDtFinal']) ) {
    $preview->addParametro( 'dt_inicial', $_REQUEST['stDtInicial'] );
    $preview->addParametro( 'dt_final', $_REQUEST['stDtFinal'] );
}

// Seta o parâmetro ordenacao no relatório.
if ( !empty($_REQUEST['stSituacao']) ) {
    $preview->addParametro( 'situacao', $_REQUEST['stSituacao'] );
}

$preview->preview();
