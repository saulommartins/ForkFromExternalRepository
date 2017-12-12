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
    * Popup para confirmação de Receitas
    * Data de Criação   : 12/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.03
*/

/*
$Log$
Revision 1.6  2006/07/05 20:40:12  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/**************************************************************************/
/**** Pega os códigos de erro que vieram                                ***/
/**************************************************************************/
$msgs = "";
$imagem = "erroa.png";

//Grava o nome da ação realizada
$nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));

$msgs .= "Você deseja salvar as informações atuais?";
$imagem = "botao_confirma.png";
$sTitulo = "Confirmação";

?>

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
<img src="<?=CAM_FW_IMAGENS.$imagem;?>" width=48 height=48 border=0>
</td>
<td>
<font color="#000000" face="Arial, Helvetica, sans-serif" size=4><b>&nbsp;
<?=$sTitulo?>
</b></font>
</td>
</tr>
<tr>
<td colspan=2>
<textarea cols=36 rows=5 class="tela_erro"><?=$msgs?></textarea>
</td>
</tr>
<tr>
<td colspan=2 align=center>
<?php
echo "
<script type='text/javascript'>
function mudaPagina()
{
window.opener.parent.frames['telaPrincipal'].document.frm.stRedireciona.value='FMDetalhamentoReceitas.php?".Sessao::getId()."&stExercicio=".$_GET["stExercicio"]."&inCodConta=".$_GET["inCodPlano"]."';
window.opener.parent.frames['telaPrincipal'].document.frm.submit();
window.close();
}
</script>
<input type='button' value='Sim' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;'
onClick='mudaPagina();'>
<input type='button' value='Não' style='font-size : 12px; color : #0A5A82; font-weight : bold; background-color : #E4EAE4; width : 100px;' onClick='javascript:opener.parent.frames[\"telaPrincipal\"].location=\"../../instancias/configuracao/FMDetalhamentoReceitas.php?".Sessao::getId()."&stExercicio=".$_GET['stExercicio']."&inCodConta=".$_GET['inCodPlano']."\"; window.close();'>";
?>
</td>
</tr>
</table>
</center>
</body>
</html>
