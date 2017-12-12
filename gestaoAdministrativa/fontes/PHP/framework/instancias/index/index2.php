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
include '../../../../../../config.php';
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<script type="text/javascript">
      window.status = ":::::::: URBEM ::::::::";
</script>
<html>
<head>

    <link href="<?=CAM_FW_IMAGENS;?>favicon.ico" type="image/ico" rel="icon" />
    <title>:::::::: URBEM ::::::::</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<frameset id="frOculto" rows="77,*,22,0" border="0">
    <frame name="telaTopo" id="telaTopo" src="topo.php?inicio=0" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize="noresize">
    <frameset cols="185,*" id="frTela" border="0">
        <frame name="telaMenu" id="telaMenu" src="menu.php" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0" noresize >
            <frameset rows="*,50" border="0"  >
                <frame name="telaPrincipal" id="telaPrincipal" src="inicial.php" marginwidth="0" marginheight="0" scrolling="auto" frameborder="0" noresize >
                <frame name="telaMensagem" id="telaMensagem" src="menu.html" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0" noresize >
            </frameset>
    </frameset>
    <frame name="telaStatus" id="telaStatus" src="status.php" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" noresize >
    <frame name="oculto" id="oculto" src="" marginwidth="100%" marginheight="100%" scrolling="yes" frameborder="0" noresize >
</frameset>
</html>
