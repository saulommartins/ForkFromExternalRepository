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
    * Frame Relatorio de Remissão
    * Data de Criação: 06/10/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$stFiltro = "";
if ($_REQUEST["inCodCredito"]) {
    $stCredito = $_REQUEST["inCodCredito"];
    $arCredito = explode( ".", $_REQUEST["inCodCredito"] );
    $stFiltro .= "
        AND parcela_origem.cod_credito = ".$arCredito[0]."
        AND parcela_origem.cod_especie = ".$arCredito[1]."
        AND parcela_origem.cod_genero = ".$arCredito[2]."
        AND parcela_origem.cod_natureza = ".$arCredito[3]."
    ";
}

$stFiltro .= "
    GROUP BY
        parcela_origem.cod_credito,
        parcela_origem.cod_especie,
        parcela_origem.cod_genero,
        parcela_origem.cod_natureza,
        credito.cod_credito,
        credito.cod_especie,
        credito.cod_genero,
        credito.cod_natureza,
        credito.descricao_credito
";

$stDtAnterior = $_REQUEST["inExercicio"]."-12-31";
$stDtAtualInicial = $_REQUEST["inExercicio"]."-01-01";
$stDtAtualFinal = $_REQUEST["inExercicio"]."-12-31";

if ($_REQUEST["stTipoRelatorio"] == "analitico") {
    $preview = new PreviewBirt( 5, 33, 5 );
} else {
    $preview = new PreviewBirt( 5, 33, 4 );
}

$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Extrato da Dívida Ativa');
$preview->addParametro( 'stFiltro', $stFiltro );
$preview->addParametro( 'dtAnterior', $stDtAnterior );
$preview->addParametro( 'dtAtualInicial', $stDtAtualInicial );
$preview->addParametro( 'dtAtualFinal', $stDtAtualFinal );
$preview->addParametro( 'stCredito', $stCredito );
$preview->setFormato('pdf');
//$preview->setExportaExcel ( true );
//$preview->setExportaWord(true);
$preview->preview();
?>
