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
* Página de geração de relatório
* Data de criação : 10/11/2015
* @author Analista: Luciana Dellay
* @author Programador: Evandro Melos
* $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once CAM_GT_DAT_MAPEAMENTO."TARRRelatorioPagadores.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CLA_MPDF;

$arDados['arRegistros'] = Sessao::read('arRegistros');

// Preparando a chamada para o layout do relatório
//LHRelatorioPagadores.php
$obMPDF = new FrameWorkMPDF(5,33,9);
$obMPDF->setDataInicio(date('d/m/Y'));
$obMPDF->setDataFinal(date('d/m/Y'));
$obMPDF->setNomeRelatorio("Relatorio de Pagadores");

$obMPDF->setConteudo($arDados);
$obMPDF->gerarRelatorio();
