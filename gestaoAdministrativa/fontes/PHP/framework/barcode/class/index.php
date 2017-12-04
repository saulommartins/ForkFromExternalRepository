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
//////////////////////////////////////////////////////////////////////
// index.php
//--------------------------------------------------------------------
//
// Holding Constant
//
//--------------------------------------------------------------------
// Revision History
// v1.2.1	27 jun	2005	Jean-Sébastien Goupil	Font support added
// V1.02	8  mar	2005	Jean-Sebastien Goupil	Spreading all Classes in single file
// V1.00	17 jun	2004	Jean-Sebastien Goupil
//--------------------------------------------------------------------
// $Id: index.php 59612 2014-09-02 12:00:51Z gelson $
// PHP5-Revision: 1.4
//--------------------------------------------------------------------
// Copyright (C) Jean-Sebastien Goupil
// http://other.lookstrike.com/barcode/
//--------------------------------------------------------------------
//////////////////////////////////////////////////////////////////////
if(!defined('IN_CB'))die('You are not allowed to access to this page.');

//////////////////////////////////////////////////////////////////////
// Constants
//////////////////////////////////////////////////////////////////////
define('IMG_FORMAT_PNG',	1);
define('IMG_FORMAT_JPEG',	2);
define('IMG_FORMAT_WBMP',	4);
define('IMG_FORMAT_GIF',	8);

define('SIZE_SPACING_FONT',	5);

// Function str_split is not available for PHP4. So we emulate it here.
if (!function_exists('str_split')) {
    function str_split($string, $split_length = 1)
    {
        $array = explode("\r\n", chunk_split($string, $split_length));
        array_pop($array);

        return $array;
    }
}
