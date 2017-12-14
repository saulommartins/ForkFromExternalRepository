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
define('IN_CB',true);

require 'class/index.php';
require 'class/Font.php';
require 'class/FColor.php';
require 'class/BarCode.php';
require 'class/FDrawing.php';
include 'class/i25.barcode.php';

$font = new Font('./class/font/Arial.ttf', 18);

$color_black = new FColor(0,0,0);
$color_white = new FColor(255,255,255);

/* PARAMETROS*/
$inWidth = $_REQUEST['altura'];
$stValor = $_REQUEST['numeracao'];

$boDrawText = true;
if (isset($_REQUEST['drawText'])) {
    $boDrawText = $_REQUEST['drawText'];
}

$inBarWidth = 2;
if (isset($_REQUEST['barWidth'])) {
    $inBarWidth = $_REQUEST['barWidth'];
}
$boChecksum = ( strlen( $stValor ) % 2 ) != 0 ;

/* Here is the list of the arguments:
1 - Thickness
2 - Color of bars
3 - Color of spaces
4 - Resolution
5 - Text
6 - Text Font
7 - checksum */
$code = new i25( $inWidth , $color_black , $color_white , $inBarWidth , $stValor , $font , $boChecksum );
$code->setDrawText($boDrawText);

/*
1 - Filename (empty : display on screen)
2 - Background color */
$drawing = new FDrawing('',$color_white);
$drawing->setBarcode($code);
$drawing->draw();

// Header that says it is an image (remove it if you save the barcode to a file)
header('Content-Type: image/png');

// Draw (or save) the image into PNG format.
$drawing->finish(IMG_FORMAT_PNG);
