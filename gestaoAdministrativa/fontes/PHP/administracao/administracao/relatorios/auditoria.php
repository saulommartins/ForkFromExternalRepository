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
    * Arquivo de instância para Relatorio.
    * Data de Criação: 29/04/2008

    * @author Rodrigo Soares Rodrigues

    * Casos de uso: uc-01.03.94

    $Id: auditoria.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );

$preview = new PreviewBirt(1,2,3);
$preview->setVersaoBirt( '2.5.0' );

if ($_REQUEST['moduloCod'] != "" && strtolower($_REQUEST['moduloCod']) != "xxx") {
    $preview->addParametro( 'pCodModulo', $_REQUEST['moduloCod'] );
} else {
    $preview->addParametro( 'pCodModulo', '' );
}

if (strlen($_REQUEST['numCgm'] > 0)) {
    $preview->addParametro( 'pNumCgm', $_REQUEST['numCgm'] );
} else {
    $preview->addParametro( 'pNumCgm', '' );
}

if ( (strlen($_REQUEST['sDataIni'])>0) and (strlen($_REQUEST['sDataFim'])==0) ) {
    $dtInicial = dataToSql($_REQUEST['sDataIni']);
    $preview->addParametro('dtInicial', $dtInicial);
    $preview->addParametro('dtFinal', 'null');
    $sqlPeriodo = " AND au.timestamp >= '".$dtInicial."' ";
    $preview->addParametro( 'pData', $sqlPeriodo);
} elseif ( (strlen($_REQUEST['sDataIni'])==0) and (strlen($_REQUEST['sDataFim'])>0) ) {
    $dtFinal = dataToSql($_REQUEST['sDataFim']);
    $preview->addParametro('dtInicial', 'null');
    $preview->addParametro('dtFinal', $dtFinal);
    $sqlPeriodo .= " AND au.timestamp <= '".$dtFinal."' ";
    $preview->addParametro( 'pData', $sqlPeriodo);
} elseif ( (strlen($_REQUEST['sDataIni'])>0) and (strlen($_REQUEST['sDataFim'])>0) ) {
    $dtInicial = dataToSql($_REQUEST['sDataIni']);
    $dtFinal = dataToSql($_REQUEST['sDataFim'])." 23:59:59.999";
    $preview->addParametro('dtInicial', $dtInicial);
    $preview->addParametro('dtFinal', $dtFinal);
    $sqlPeriodo .= " AND au.timestamp Between '".$dtInicial."' AND '".$dtFinal."' ";
   // $preview->addParametro( 'pData', $sqlPeriodo);
} else {
    $preview->addParametro('dtInicial', 'null');
    $preview->addParametro('dtFinal', 'null');
    $preview->addParametro( 'pData', '');
}

$preview->addParametro ( 'orderby' , $_REQUEST['orderby']);

$preview->preview();
