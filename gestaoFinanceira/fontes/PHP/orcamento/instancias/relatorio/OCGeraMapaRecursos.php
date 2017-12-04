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
 * Página do Relatório Mapa de Recursos
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Eduardo Schitz <eduardo.schitz@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,8,5);
$preview->setTitulo('Mapa de Recursos');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$preview->addParametro ( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade'] ) );

$preview->addParametro('data_final', $_REQUEST['stDataFinal']);

if ($_REQUEST['stCodRecursoInicial'] != "") {
    $preview->addParametro('cod_recurso_ini', $_REQUEST['stCodRecursoInicial']);
} else {
    $preview->addParametro('cod_recurso_ini', 0);
}

if ($_REQUEST['stCodRecursoFinal'] != "") {
    $preview->addParametro('cod_recurso_fim', $_REQUEST['stCodRecursoFinal']);
} else {
    $preview->addParametro('cod_recurso_fim', 0);
}

$preview->preview();
