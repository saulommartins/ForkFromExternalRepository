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
 * Página de processamento oculto para o relatório de logradouros
 * Data de Criação   : 15/07/2015
 * @author Analista: Gelson Wolowski Gonçalves
 * @author Desenvolvedor: Evandro Melos
 * $Id: OCGeraRelatorioLogradouros.php 63656 2015-09-24 19:44:19Z evandro $
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$rsDadosRelatorio  = Sessao::read('dados_relatorio');
$boMostrarHisorico = Sessao::read('mostra_historico');

if ($boMostrarHisorico == 'true'){
    $arDados['boMostrarHistorico'] = 'true';
}else{
    $arDados['boMostrarHistorico'] = 'false';
}

if ($boMostrarHisorico == 'true'){
    $arDados['boNorma'] = 'true';
}else{
    $arDados['boNorma'] = 'false';
}

$arDados['arDadosLogradouro'] = $rsDadosRelatorio->getElementos();

//gestaoTributaria/fontes/RPT/cadastroImobiliario/MPDF/LHLogradouros.php
$obMPDF = new FrameWorkMPDF(5,12,2);
$obMPDF->setFormatoFolha("A4-L");

$obMPDF->setNomeRelatorio("Relatorio Logradouros");

$obMPDF->setConteudo($arDados);

$obMPDF->gerarRelatorio();
?>
