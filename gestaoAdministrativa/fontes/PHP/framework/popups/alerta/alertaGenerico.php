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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/**************************************************************************/
/**** Pega os códigos de erro que vieram                                ***/
/**************************************************************************/
$msgs = "";
$imagem = "erroa.png";
//Grava o nome da ação realizada,
# oh my god, isso nao acaba nunca...
# quem chama essa coisa!
#$nomAcao = sessao->transf2["acao_generica"];
$nomAcao = Sessao::read('acao_generica');
//Retira as barras das strings
$chave = $_REQUEST['chave'];
$valor = $_REQUEST['valor'];
$pagQuestao = $_REQUEST['pagQuestao'];
$pagQuestao = str_replace("*_*", "&", $pagQuestao);
$pagQuestao = str_replace("*-*-*", "?", $pagQuestao);
$arPagQuestao = explode('&',$pagQuestao);
/* Comentado porque estava com problemas
foreach ($arPagQuestao as $stKey => $stValue) {
    $arKey = explode('=',$stValue);
    //echo "=$arKey[0]<br>";
    if ( strstr( $arKey[0], '.php') ) {
        $arOut[] = $stValue.'?'.Sessao::getId();
    } else {
        $arOut[] = $stValue;
    }
}
$pagQuestao = implode('&',$arOut);
*/
foreach ($arPagQuestao as $stKey => $stValue) {
    $arKey = explode('=',$stValue);

    if ($arKey[0] == 'stDescQuestao') {
        $stDescQuestao = $arKey[1];
    }
}

$obj = stripslashes($stDescQuestao);
$pag = $pagQuestao;
$tipo    = $_REQUEST['tipo'];
$chamada = $_REQUEST['chamada'];

switch ($tipo) {
case "incluir":
$msgs .= $obj." incluído com sucesso";
break;
case "n_incluir":
$msgs .= "Não foi possível incluir ".$obj.", contate o Administrador";
break;
case "alterar":
$msgs .= $obj." alterado com sucesso";
break;
case "n_alterar":
$msgs .= "Não foi possível alterar ".$obj.", contate o Administrador";
break;
case "sn_excluir":
$msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
$imagem = "botao_confirma.png";
break;
case "sn_cancelar":
$msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
$imagem = "botao_confirma.png";
break;
case "pp_excluir":
$msgs .= "Confirma ".$nomAcao." (".$obj.") ?";
$imagem = "botao_confirma.png";
break;
case "cc":
$msgs .= $obj;
break;
case "excluir":
$msgs .= $obj." excluído com sucesso";
break;
case "n_excluir":
$msgs .= "Não foi possível excluir ".$obj.", contate o Administrador";
break;
case "cancelar":
$msgs .= $obj." cancelado com sucesso";
break;
case "n_cancelar":
$msgs .= "Não foi possível cancelar ".$obj.", contate o Administrador";
break;
case "form":
$ch_mensagem = substr($obj, 1);
$rel_mensagem = str_replace("@","\n","$ch_mensagem");
$msgs .= $rel_mensagem;
break;
case "unica":
$msgs .= $obj;
break;
case "ccform":
$msgs .= $obj;
$imagem = "botao_confirma.png";
break;
case "sn":
$msgs .= $obj;
$imagem = "botao_confirma.png";
break;
case "pp":
$msgs .= $obj;
$imagem = "botao_confirma.png";
break;
}

switch ($chamada) {
case "erro":
$sTitulo = "ERRO";
break;
case "aviso":
$sTitulo = "Aviso";
break;
case "cc":
$sTitulo = "Confirmação";
break;
case "sn":
$sTitulo = "Confirmação";
break;
case "pp":
$sTitulo = "Confirmação";
break;
case "decisao":
$sTitulo = "Confirmação";
break;
case "ccform":
$sTitulo = "Confirmação";
break;
case "oculto":
$sTitulo = "Confirmação";
break;
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0//EN">

<html>
<head>
  <title><?=$sTitulo?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="STYLESHEET" type="text/css" href="../includes/stylos_ns.css">

<script type="text/javascript"><!--
var skipcycle = false

function fcsOnMe()
{
if (!skipcycle) {
window.focus();
}
mytimer = setTimeout('fcsOnMe()', 500);
}
//-->
</script>

</head>
<body bgcolor=#E4EAE4 leftmargin="0" topmargin="0" onload = "mytimer = setTimeout('fcsOnMe()', 500);">

<center>
<table width=270>

<tr>
<td width="48" height="48">
<img src="<?=CAM_FW_IMAGENS."/".$imagem;?>" width=48 height=48 border=0>
</td>
<td>
<font color="#000000" face="Arial, Helvetica, sans-serif" size=4><b>&nbsp;
<?=$sTitulo?>
</b></font>
</td>
</tr>
<tr>
<td colspan=2>
<?php
if ($tipo == "decisao") {
?>
<textarea disabled=true cols=36 rows=5 class="tela_erro"><?php

echo "$msgs\n";
# de novo, estao me perseguindo
/*
while (list($chave,$valor) = each(sessao->transf2)) {
$val = stripslashes($valor);
print "- $val\n";
}
*/
?></textarea>
<?php
} else {
?>
<textarea disabled=true cols=36 rows=5 class="tela_erro"><?=$msgs?></textarea>
<?php
}
?>
</td>
</tr>
<tr>
<td colspan=2 align=center>
<?php
switch ($chamada) {
case "erro":
echo '<input type="button" value="OK" style="font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;" onClick="javascript:window.close();">';
break;
case "aviso":
echo '<input type="button" value="OK" style="font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;" onClick="javascript:window.close();">';
break;
case "cc":
echo "
<script type='text/javascript'>
function mudaPagina()
{
sPag = '$pag?".Sessao::getId()."&".$chave."=".$valor."';
window.opener.parent.frames['telaPrincipal'].document.location.replace(sPag);
window.close();
}
</script>
<input type='button' value='Confirmar' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'> <input type='button' value='Cancelar' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:window.close();'>";
break;
case "sn":
echo "
<script type='text/javascript'>
function mudaPagina()
{
sPag = '$pag&".$chave."=".$valor."';
window.opener.parent.frames['telaPrincipal'].document.location.replace(sPag);
window.close();
}
</script>
<input type='button' value='Sim' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'> <input type='button' value='Não' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:window.close();'>";
break;
case "pp":
echo "
<script type='text/javascript'>
function mudaPagina()
{
sPag = '$pag&".$chave."=".$valor."';
window.opener.document.location.replace(sPag);
window.close();
}
</script>
<input type='button' value='Sim' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'> <input type='button' value='Não' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:window.close();'>";
break;
case "decisao":
echo "
<script type='text/javascript'>
function mudaPagina()
{
sPag = '$pag?".Sessao::getId()."';
window.opener.parent.frames['telaPrincipal'].document.location.replace(sPag);
window.close();
}
</script>
<input type='button' value='Sim' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='javascript:window.close()'> <input type='button' value='Não' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='mudaPagina();'>";
break;
case "ccform":
echo "
<script type='text/javascript'>
function mudaPagina()
{
window.opener.parent.frames['telaPrincipal'].document.frm.submit();
window.close();
}
</script>
<input type='button' value='Confirmar' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'> <input type='button' value='Cancelar' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:window.close();'>";
break;
case "oculto":
echo "
<script type='text/javascript'>
function mudaPagina()
{
window.opener.parent.frames['oculto'].document.frm.submit();
window.close();
}
</script>
<input type='button' value='Sim' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'> <input type='button' value='Não' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:window.close();'>";
break;
}
?>
</td>
</tr>
</table>
</center>
</body>
</html>
