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
 * Página de Filtro para Relatório de Ïtens Perecíveis
 * Data de Criação   : 03/10/2007

 * @ignore

 * Casos de uso : uc-03.03.27
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,29,7);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->addParametro( 'pAlmoxarifado', $_POST['inCodAlmoxarifado'] );
$preview->addParametro( 'pCodCatalogo' , $_POST['inCodCatalogo'    ] );
$preview->addParametro( 'pDescricao'   , $_POST['inHdnDescItem'    ] );
$preview->addParametro ( 'pEstrutural' , $_POST['stChaveClassificacao'] );
$preview->addParametro ( 'pSaldo'      , $_POST['boComSaldo'] );

$ar_path = explode('/',$_SERVER["PHP_SELF"]);

for ( $i = 1; $i < ( count($ar_path) - 7 ); $i++ ) {
    $stCam .= $ar_path[$i]."/";
}

$preview->addParametro( 'imgPath', "http://" . $_SERVER["SERVER_NAME"] . "/" . $stCam . "gestaoAdministrativa/fontes/PHP/framework/barcode/index.php" );

$preview->preview();
