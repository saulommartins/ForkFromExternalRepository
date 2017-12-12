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
* Arquivo de instância para relatório de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18630 $
$Name$
$Author: cassiano $
$Date: 2006-12-08 14:34:31 -0200 (Sex, 08 Dez 2006) $

Casos de uso: uc-01.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$rsTipoNorma = new recordset();
$rsTipoNorma = Sessao::read('rsTipoNorma');

$obMPDF = new FrameWorkMPDF(1,15,1);

$obMPDF->setCodEntidades($request->get('inCodEntidade'));
$obMPDF->setDataInicio($request->get("stDataInicial"));
$obMPDF->setDataFinal($request->get("stDataFinal"));
$obMPDF->setNomeRelatorio("RelatorioDeNormas");

$arConteudo = array();
$arConteudo["arRecordSet"] = $rsTipoNorma->getElementos();

$obMPDF->setConteudo($arConteudo);
$obMPDF->gerarRelatorio();

?>
