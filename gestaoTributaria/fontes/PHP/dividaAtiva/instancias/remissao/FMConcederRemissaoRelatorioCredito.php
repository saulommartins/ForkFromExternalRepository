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
    * Página de Formulario de Relatorio da remissao da divida ativa

    * Data de Criação: 05/05/2010

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Eduardo Paculski Schitz
    * @ignore

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$arListaCreditoSessao = Sessao::read('arListaCredito');

$inTotalNaListaCreditoSessao = count($arListaCreditoSessao);
if ($inTotalNaListaCreditoSessao > 0) {
    $stFiltro2 .= '\'';
    for ($inX=0; $inX<$inTotalNaListaCreditoSessao; $inX++) {
        $stFiltro2 .= $arListaCreditoSessao[$inX]['stExercicio'].$arListaCreditoSessao[$inX]['stCodCredito'].'-';
    }
    $stFiltro2 = SUBSTR($stFiltro2,0,-1).'\'';
}

$stFiltro = "(".$stFiltro2.", false";

$preview = new PreviewBirt(5, 33, 3);
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Remissão');
$preview->addParametro('stFiltro' , $stFiltro);
if ($_REQUEST['inCodNorma']) {
    $preview->addParametro('cod_norma', $_REQUEST['inCodNorma']);
} else {
    $preview->addParametro('cod_norma', 0);
}
$preview->setFormato('pdf');
$preview->preview();
