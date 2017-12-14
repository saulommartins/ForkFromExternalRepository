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
    * Data de Criação   : 13/08/2010

    * @description Arquivo que chama o relatório Birt

    * @author Desenvolvedor: Tonismar R. Bernardo

      $Id: OCRelatorioConsistenciaColetora.php 66466 2016-08-31 14:34:38Z michel $

    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

SistemaLegado::BloqueiaFrames(true, false);

$arFiltro = Sessao::read('arFiltro');

$stDataInicial      = $arFiltro['stDataInicial'];
$stDataFinal        = $arFiltro['stDataFinal'];
$locaisSelecionados = $arFiltro['locaisSelecionados'];
$tipoRelatorio      = $arFiltro['tipoRelatorio'];
$inCodEntidade      = $arFiltro['inCodEntidade'];

#relatorioConsistenciaColetora.rptdesign
$birt = new PreviewBirt(3,6,18);
$birt->setVersaoBirt('2.5.0');
$birt->setFormato('pdf');
$birt->addParametro("data_inicial"        , $stDataInicial);
$birt->addParametro("codigo_arquivo"      , $_REQUEST["codigo"]);
$birt->addParametro("data_final"          , $stDataFinal);
$birt->addParametro("locais_selecionados" , $locaisSelecionados);
$birt->addParametro("tipo_relatorio"      , $tipoRelatorio);
$birt->addParametro("cod_entidade"        , $inCodEntidade);

$birt->preview();

?>
