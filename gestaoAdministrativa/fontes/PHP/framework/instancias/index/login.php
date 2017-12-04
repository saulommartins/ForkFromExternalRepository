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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/
# atualmente necessitamos de um navegador baseado no engine gecko :-D
if ( strpos( strtolower( $_SERVER["HTTP_USER_AGENT"] ), "gecko" ) === false ) {
    header("Location: browser.php");
}

if (version_compare(phpversion(), "4.2.2") < 0 ) {
   header("Location: versaoPHP.php");
}

$stErroPHPIni = "";
$boErroPHPIni = false;
if ( ini_get("register_globals") != 0 ) {
#   $stErroPHPIni .= "- register_globals<br>";
#    $boErroPHPIni = true;
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
    include_once 'erroPHPIni.php';
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-BR" lang="pt-BR">
<head profile="http://www.w3.org/2000/08/w3c-synd/#">
 <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
  <meta name="keywords" content="doc, php-doc, documentacaos" />
  <meta name="description" content="doc" />
  <title>Urbem 1.0 :: Login</title>

<style>
 @import url(../../temas/padrao/CSS/login.css);
</style>

</head>

<body onload="javascript:document.getElementById('usuario').focus();">
<div id="logon">
 <div id="formulario1">
 <form action="PRLogin.php" id="frm" target="oculto" method="post">
    <div id="logo_urbem">
        <img alt="Logo Urbem" id="img_logo_urbem" src="../../temas/padrao/imagens/logo_urbem_login.png"/>
    </div>
    <div id="label-frm">
        <label for="usuario" id="label_usuario" title="Usuario">Usuário</label><br />
        <label for="senha" id="label_senha" title="Logon">Senha</label><br />
        <label for="exercicio" id="label_exercicio" title="Exercicio">Exercício</label>
    </div>
    <div id="input-frm">
        <input accesskey="u" type="text" id="usuario" name="usuario" title="Usuario" tabindex="1"/><br />
        <input accesskey="s" type="password" id="senha" name="senha" title="Senha" tabindex="2" /><br />
        <input accesskey="x" type="text" id="exercicio" name="exercicio" title="Exercicio" size="4" tabindex="4" maxlength="4" value="<?=date( 'Y', time());?>" />
    </div>
    <div id="submit-frm">
        <div id="input_login">
            <input accesskey="e" type="submit" id="enviar" name="enviar" title="Login" value="Login" tabindex="3" onKeyPress="event.keycode==13?'submit'" />
        </div>
        <div id="input_limpar">
            <input accesskey="l" type="reset" id="limpar" name="limpar" title="Limpar" tabindex="5" value="Limpar" />
        </div>
    </div>
    </form>
 </div>
    <div id="erro">
    </div>
</div>

</body>
</html>
