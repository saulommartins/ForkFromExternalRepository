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
    ob_start();

    if ( !isset( $_GET["file"] ) ) {
        header( "HTTP/1.1 400 Bad Request" );
        echo( "<html><body><h1>HTTP 400 - Bad Request</h1></body></html>" );
        exit;
    }
    $rfile = realpath('compressed/'. $_GET["file"].'.gz');

    $file_last_modified = filemtime($rfile);
    header( "Last-Modified: " . date( "r", $file_last_modified ) );

    $max_age = 24 * 60 * 60; // 300 days

    $expires = $file_last_modified + $max_age;
    header( "Expires: " . date( "r", $expires ) );

    $etag = dechex( $file_last_modified );
    header( "ETag: " . $etag );

    $cache_control = "must-revalidate, proxy-revalidate, max-age=" . $max_age . ", s-maxage=" . $max_age;
    header( "Cache-Control: " . $cache_control );
    header("Content-type: text/javascript; charset: utf-8");
    header("Content-encoding: gzip");
    header( "Content-Length: " . filesize($rfile) );

    echo readfile($rfile);

    ob_end_flush();
