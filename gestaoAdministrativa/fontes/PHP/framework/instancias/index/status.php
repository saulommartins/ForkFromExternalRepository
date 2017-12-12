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
include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include URBEM_ROOT_PATH . 'gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

$sAux = "";
if (defined('ENV_TYPE') && constant('ENV_TYPE') == 'dev') {
    $sAux = "Base: ".BD_NAME."(".BD_HOST.") - ";
}
$sAux .= Sessao::read('nomCgm')." - ".Sessao::read('nomSetor')." - " .Sessao::getExercicio();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Tela Status</title>
  <script src="<?=CAM_GA;?>javaScript/compressed/ifuncoesJs.js" type="text/javascript"></script>
  <script src="<?=CAM_GA;?>javaScript/compressed/funcoesJs.js" type="text/javascript"></script>
  <script src="<?=CAM_GA;?>javaScript/compressed/genericas.js" type="text/javascript"></script>
  <script src="<?=CAM_GA;?>javaScript/compressed/prototype.js" type="text/javascript"></script>

  <script type="text/javascript">
        function mudaTelaMenu(sPag)
        {
            sPagMsg  = "mensagem.php?<?=Sessao::getId()?>";
            sPagOc   = "limpaSession.inc.php?<?=Sessao::getId()?>";
            sPagMenu = "menu.php?<?=Sessao::getId()?>&nivel=0";

            parent.frames["telaMenu"].location.replace(sPagMenu);
            parent.frames["telaPrincipal"].location.replace(sPag);
            parent.frames["telaMensagem"].location.replace(sPagMsg);
            parent.frames["oculto"].location.replace(sPagOc);
        }

        function mudaMenu(sPag)
        {
            document.location.replace(sPag);
        }

      MontaCSS();

    </script>

</head>
<body topmargin="0" leftmargin="0" style="margin-top:0;">
<table width="100%" cellpadding="0" cellspacing="0">
<?php
if (Sessao::started()) {
?>

<tr>
    <td align="left" height="30" class="status" align="right">
        <font color="black" face="Arial, Helvetica, sans-serif" size=2><b>&nbsp;<?=$sAux?> <span id="stTerminalLogado"> </span><span id="stSaldoCaixa"></span></b></font> &nbsp;
    </td>
    <td width="2%"  class="status">&nbsp;</td>
    <td width="3%" class="status" height="30" >&nbsp;
        <a  href="#" onclick="mudaTelaMenu('inicial.php?<?=Sessao::getId();?>');" title="Início">
        <img src="<?=CAM_FW_IMAGENS?>botao_home.png" width="30" height="20" border="0">
       &nbsp; </a>&nbsp;
    </td>
    <td width="3%" class="status" height="30">&nbsp;
        <a id="logout_link" onclick="window.parent.location.href = this.href;return false;" href="<?=URBEM_ROOT_URL?>index.php?action=sair" title="Sair"  >
        <img src="<?=CAM_FW_IMAGENS?>botao_sair.png" width="30" height="20" border="0">
       &nbsp; </a>&nbsp;
    </td>
    <td width="2%"  class="status">&nbsp;</td>
</tr>
<?php } else { ?>
<tr>
    <td height="30" class="status"> &nbsp;</td>
</tr>
<?php } ?>
</table>
</body>
</html>
