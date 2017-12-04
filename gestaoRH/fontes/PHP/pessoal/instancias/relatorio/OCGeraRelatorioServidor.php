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
* Página relatório de Servidor
* Data de Criação   : 30/10/2014
* @author Analista: Dagiane
* @author Desenvolvedor: Evandro Melos
*
* $Revision: $
* $Name: $
* $Author: $
* $Date: $
* $Id: OCGeraRelatorioServidor.php 61236 2014-12-19 11:22:40Z diogo.zarpelon $ 
*/

set_time_limit(0);

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;

$obMPDF = new FrameWorkMPDF(4,22,17);
$obMPDF->setNomeRelatorio("Relatorio Servidor");
$obMPDF->setFormatoFolha("A4");

$rsDados = Sessao::read('rsServidores');
$obMPDF->setTipoSaida('D');

if ($rsDados->getElementos()) {
    $obMPDF->setConteudo($rsDados->getElementos());
} else {
    $arDados = array();
    
    $obMPDF->setConteudo($arDados);
}

$obMPDF->gerarRelatorio();

?>
