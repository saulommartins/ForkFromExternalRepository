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
  * Data de criação : 03/02/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Programador: Grasiele Torres

    $Id: $

    Caso de uso: uc-03.01.09

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,6,13);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioCargaPatrimonial');

$exercicio = Sessao::getExercicio();
$preview->addParametro( 'exercicio', $exercicio );

if ($_REQUEST['inCodOrgao'] != '') {
    $preview->addParametro( 'cod_orgao', $_REQUEST['inCodOrgao'] );
} else {
    $preview->addParametro( 'cod_orgao', '' );
}

if ($_REQUEST['stClassificacaoReduzida'] != '') {
    $preview->addParametro( 'classificacaoReduzida', $_REQUEST['stClassificacaoReduzida'] );
} else {
    $preview->addParametro( 'classificacaoReduzida', '' );
}

if ($_REQUEST['inCodLocal'] != '') {
    $preview->addParametro( 'cod_local', $_REQUEST['inCodLocal'] );
} else {
    $preview->addParametro( 'cod_local', '' );
}

if ($_REQUEST['tipoRelatorio'] != '') {
    $preview->addParametro( 'tipoRelatorio', $_REQUEST['tipoRelatorio'] );
} else {
    $preview->addParametro( 'tipoRelatorio', '' );
}

$preview->addParametro('cod_natureza'     , $_REQUEST['inCodNatureza']);
$preview->addParametro('cod_grupo'        , $_REQUEST['inCodGrupo']);
$preview->addParametro('cod_especie'      , $_REQUEST['inCodEspecie']);
$preview->addParametro('entidade'         , $_REQUEST['inCodEntidade']);

$preview->preview();
