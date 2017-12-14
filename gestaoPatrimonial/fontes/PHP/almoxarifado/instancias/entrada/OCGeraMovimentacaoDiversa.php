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
  * Data de criação : 12/08/2008

  $Id:$

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once(TALM."TAlmoxarifadoNaturezaLancamento.class.php"                                       );

$preview = new PreviewBirt(3,29,8);
$preview->setVersaoBirt( '2.5.0' );

$arrayItens = Sessao::read('itens');

$preview->setTitulo(' Nota de Entrada  ');

$preview->setNomeArquivo('notaEntrada');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

$preview->addParametro  ( "cod_acao" , Sessao::read('acao'));

$preview->addParametro  ( "prmUltimoLancamento" , $_REQUEST['inNumLancamento'] );

$preview->addParametro  ( "prmCodNatureza" , $_REQUEST['inCodNatureza'] );

$preview->addParametro  ( "prmCodOrdem" , $_REQUEST['inCodOrdem'] );

if ($_REQUEST['inCodNatureza'] == 1) {
    $preview->addParametro  ( "prmCodAlmoxarifado" , $arrayItens[0]['inCodAlmoxarifado'] );
} else {
    $preview->addParametro  ( "prmCodAlmoxarifado" , $_REQUEST['inCodAlmoxarifado'] );
}

if ($_REQUEST['exercicioReemissao'] != '') {
    $preview->addParametro  ( "prExercicioReemissao" , $_REQUEST['exercicioReemissao'] );
} else {
    $preview->addParametro  ( "prExercicioReemissao" , '' );
}

if ($_REQUEST['inNomNatureza'] != '') {
    $preview->addParametro  ( "prNomNatureza" , $_REQUEST['inNomNatureza'] );
} else {
    $preview->addParametro  ( "prNomNatureza" , '' );
}

$preview->preview();
