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

header("Expires: Mon, 10 mar 1967 09:00:00 GMT"); // qualquer data no passado
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0

include_once '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"      );

$SQL = "select i.nom_impressora, i.fila_impressao, u.impressora_padrao from administracao.usuario_impressora as u, administracao.impressora as i WHERE u.cod_impressora = i.cod_impressora AND u.numcgm = ".Sessao::read('numCgm')."";

$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($SQL);
$dbEmp->vaiPrimeiro();
$comboImpressora="<select name=sFilaImpressao>\n<option value=xxx >Selecione</option>\n";
while (!$dbEmp->eof()) {
   $sSelected = "";
   $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
   $fila_impressao  = trim($dbEmp->pegaCampo("fila_impressao"));
   $bImpressoraPadrao = strtolower($dbEmp->pegaCampo("impressora_padrao"));
   if ($bImpressoraPadrao=='t') {
       $sSelected = "selected";
   }
   $dbEmp->vaiProximo();
   $comboImpressora .= "<option value='".$fila_impressao."' ".$sSelected.">".$nom_impressora."</option>\n";
}
$comboImpressora .="</select>";
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
?>

<html><head>
<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<meta http-eqiv='Expires' content='10 mar 1967 09:00:00 GMT'>
<link rel="STYLESHEET" type="text/css" href="stylos_ns.css">
</head><body leftmargin=0 topmargin=0>
<center><table width=100%>
<tr>
<td class="labelcenter" height=5 width=100%><font size=1 color=#535453><b>&raquo; Imprimir</b></font></td>
</tr>
</table>

<script type="text/javascript">

function alertaAviso(objeto,tipo,chamada)
{
    var x = 350;
    var y = 200;
    var sessao = '<?=Sessao::getId()?>';
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../popups/alerta/alerta.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    var sAux = "msga"+ sessaoid +" = window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}
function Valida()
{
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.sFilaImpressao.value;
            if (campo == "xxx") {
            mensagem += "@Selecione uma Impressora";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
</script>
<form name="frm" action="relatorioPdfLegado.php?<?=Sessao::getId()?>" method="POST" enctype="application/x-www-form-urlencoded">
<table width=450 cellspacing=0 border=0 cellpadding=0>
<tr><td class=alt_dados colspan=2>Selecione a impressora e o número de cópias para a impressão do relatório</td></tr>

<tr>
<td width="50%" class="label">Selecione a Impressora:</td>
<td width="50%" class="field"><?=$comboImpressora?></td>
</tr>

<tr>
<td width="50%" class="label">Número de Cópias</td>
<td width="50%" class="field"><input type="text" name="iCopias" size=4 value="1">
<input type="hidden" name="sAcaoPDF" value="<?=$_REQUEST['sAcaoPDF']?>">
<input type="hidden" name="sScriptXML" value="<?=$_REQUEST['sScriptXML']?>">
<input type="hidden" name="sSQL" value="<?php print stripslashes($sSQL);?>">
<input type="hidden" name="sTitulo" value="<?=$_REQUEST['sTitulo']?>">
<input type="hidden" name="sSubTitulo" value="<?=$_REQUEST['sSubTitulo']?>">
<input type="hidden" name="sParametros" value="<?php print stripslashes($_REQUEST['sParametros']);?>">
</td>
</tr>

<tr>
<td colspan=2 class="field"><input type="button" value="OK" style="width: 60px" onClick="Salvar();")>&nbsp;
</td>
</tr>

</table>

</form>

</center></body></html>
