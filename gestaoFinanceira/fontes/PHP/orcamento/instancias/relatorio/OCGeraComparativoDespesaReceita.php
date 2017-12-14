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
    * Pagina oculto para gerar o relatorio de Comparativo de Despesa com Receita
    * Data de Criação: 02/09/2009
    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
    * @package URBEM
    * @subpackage ORCAMENTO
    * @ignore
    * $Id: OCGeraComparativoDespesaReceita.php 66305 2016-08-05 19:24:10Z michel $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stDestinacao = '';
if ($_POST['inCodUso'] != '') {
    $stDestinacao .= $_POST['inCodUso'].'.';
}
if ($_POST['inCodDestinacao'] != '') {
    $stDestinacao .= $_POST['inCodDestinacao'].'.';
}
if ($_POST['inCodEspecificacao'] != '') {
    $stDestinacao .= $_POST['inCodEspecificacao'].'.';
}
if ($_POST['inCodDetalhamento'] != '') {
    $stDestinacao .= $_POST['inCodDetalhamento'];
}
if ($stDestinacao != '') {
    $stDestinacao .= "%";
}

#comparativoDespesaReceita.rptdesign
$preview = new PreviewBirt(2, 8, 8);
$preview->setTitulo      ('Comparativo de Despesa X Receita');
$preview->setVersaoBirt  ('2.5.0');
$preview->setExportaExcel(true);

$preview->addParametro('stCodEntidade', implode(',', $_POST['inCodEntidade']));
$preview->addParametro('stExercicio', $_POST['stExercicio']);
$preview->addParametro('stDestinacaoRecurso', $stDestinacao);
$preview->addParametro('stCodRecurso', $_POST['inCodRecurso']);

$preview->preview();
