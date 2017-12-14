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
  * Data de criação : 23/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//gestaoPatrimonial/fontes/RPT/patrimonio/report/design/classificacao.rptdesign
$preview = new PreviewBirt(3,6,6);
$preview->setVersaoBirt( '2.5.0' );

$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('classificacao');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

// Seta o valor do parâmetro cod_natureza do relatório.
if ( $request->get('inCodNatureza') ) {
    $preview->addParametro( 'cod_natureza', $request->get('inCodNatureza') );
}

// Seta o valor do parâmetro cod_grupo do relatório.
if ( $request->get('inCodGrupo') ) {
    $preview->addParametro( 'cod_grupo', $request->get('inCodGrupo') );
}

// Seta o valor do parâmetro cod_especie do relatório.
if ( $request->get('inCodEspecie') ) {
    $preview->addParametro( 'cod_especie', $request->get('inCodEspecie') );
}

// Seta o valor do parâmetro cod_entidade do relatório.
if ( $request->get('inCodEntidade') ) {
    $preview->addParametro( 'cod_entidade', $request->get('inCodEntidade') );
}

$preview->preview();
