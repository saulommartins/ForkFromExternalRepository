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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once (CAM_FW_LEGADO."dataBaseLegado.class.php"    );
if (!(isset($_SESSION['sessao']))) {
    header( "location:login.php?".Sessao::getId()."&erro=2" );
}
?>

<html>

<head>
<title></title>

<link href="includes/estilos_inicial.css" type="text/css" rel="stylesheet">
<script type="text/JavaScript">
    function mudaMenu(func)
    {
        sPag = "menu.php?<?=Sessao::getId();?>&nivel=3&cod_func_pass="+func;
        parent.parent.frames["telaMenu"].location.replace(sPag);
    }
</script>
</head>

<body text="#000000" bgcolor="#cccccc" leftmargin="0" topmargin="0" marginheight="0" marginwidth="0">

<table width='100%'>

<tr>
    <td valign="top" width="45%">

    </td>
</tr>
</table>

</body>
