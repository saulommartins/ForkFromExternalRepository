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

if ( strpos( strtolower( $_SERVER["HTTP_USER_AGENT"] ), "gecko" ) === false ) {
    header("Location: browser.php");
}

if (version_compare(phpversion(), "4.2.2") < 0 ) {
   header("Location: versaoPHP.php");
}

$stErroPHPIni = "";
$boErroPHPIni = false;
if ( ini_get("register_globals") != 0 ) {
   $stErroPHPIni .= "- register_globals<br>";
   $boErroPHPIni = true;
}
if ( ini_get("short_open_tag") < 1 ) {
   $stErroPHPIni .= "- short_open_tag<br>";
   $boErroPHPIni = true;
}

if ( ini_get("session.name") != "PHPSESSID" ) {
    $stErroPHPIni .= "- session.name<br>";
    $boErroPHPIni = true;
}
if ( ini_get("session.gc_maxlifetime") < 1440 ) {
    $stErroPHPIni .= "- session.gc_maxlifetime<br>";
    $boErroPHPIni = true;
}
if ( ini_get("session.cache_expire") < 360 ) {
    $stErroPHPIni .= "- session.cache_expire<br>";
    $boErroPHPIni = true;
}
if ( ini_get("session.use_trans_sid") > 0 ) {
    $stErroPHPIni .= "- session.use_trans_sid<br>";
    $boErroPHPIni = true;
}
if ( (integer) ini_get("memory_limit") < 256 ) {
    $stErroPHPIni .= "- memory_limit<br>";
    $boErroPHPIni = true;
}
if ( ini_get("precision") < 12 ) {
    $stErroPHPIni .= "- precision<br>";
    $boErroPHPIni = true;
}
if ( ini_get("allow_call_time_pass_reference") < 1 ) {
    $stErroPHPIni .= "- allow_call_time_pass_reference<br>";
    $boErroPHPIni = true;
}
if ( ini_get("magic_quotes_gpc") < 1 ) {
    $stErroPHPIni .= "- magic_quotes_gpc<br>";
    $boErroPHPIni = true;
}

if ($boErroPHPIni) {
    //include_once("erroPHPIni.php");
    //exit();
}
