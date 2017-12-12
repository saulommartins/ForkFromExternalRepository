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
* Arquivo de implementação de manutenção de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: consultaProcessoDespacho.php 63829 2015-10-22 12:06:07Z franver $

$Revision: 4412 $
$Name$
$Author: lizandro $
$Date: 2005-12-28 13:46:15 -0200 (Qua, 28 Dez 2005) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
include_once (CAM_FW_LEGADO."mascarasLegado.lib.php"     );

$acao = $_REQUEST['acao'];
$codProcesso = $_REQUEST['codProcesso'];
$anoExercicio = $_REQUEST['anoExercicio'];
$codAndamento = $_REQUEST['codAndamento'];
$nomSetor = $_REQUEST['nomSetor'];

?>
<?php
if (isset($acao))
   Sessao::write('acao', $acao);
?>
<?php
$sSQL = "SELECT nom_acao FROM administracao.acao WHERE cod_acao =".Sessao::read('acao');
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$gera="";
while (!$dbEmp->eof()) {
   $nomeacao  = trim($dbEmp->pegaCampo("nom_acao"));
   $dbEmp->vaiProximo();
   $gera .= $nomeacao;
}
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
?>
<html><head>
<script type="text/javascript">
function alertaMensagem(erro,tipo)
{
    var x = 350;
    var y = 200;
    var sArq = '../../../framework/instancias/index/mensagem.php?<?=Sessao::getId();?>&mensagem='+erro+'&tipo='+tipo;
    //var wVolta=false;
    mensagem = window.open(sArq,'mensagem','width=300px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

function alertaAviso(objeto,tipo,chamada)
{
    var x = 350;
    var y = 200;
    var sArq = '../../../framework/popups/alerta/alerta.php?<?=Sessao::getId()?>&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    //var wVolta=false;
    mensagem = window.open(sArq,'mensagem','width=300px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

</script>

<link rel=STYLESHEET type=text/css href='../../includes/stylos_ns.css'>

<meta http-equiv='Pragma' content='no-cache'>
<meta http-equiv='Cache-Control' content='no-store, no-cache, must-revalidate'>
<meta http-eqiv='Expires' content='10 mar 1967 09:00:00 GMT'>
</head><body leftmargin=0 topmargin=0>
<table width="100%" align="center">
<tr>
<td align="center">
<table width=100%>
<tr>
<td class="labelcenter" height=5 width=100%><font size=1 color=#535453><b>&raquo; <?=$gera?></b></font></td>
</tr>
</table>

<?php
//********************************************************************************//
include '../../../framework/legado/processosLegado.class.php';
    $p = new processosLegado;

    if ($despachos = $p->pegaDadosDespacho($codProcesso,$anoExercicio,$codAndamento)) {

    //$nomUsuario = stripslashes($nomUsuario);
?>

<table width='100%'>
<tr>
    <td class="alt_dados" colspan="2">
        Dados do processo
    </td>
</tr>
<tr>
    <td class='label'>
        Processo
    </td>
    <td class=field>
<?php          $mascaraProcesso = pegaConfiguracao('mascara_processo',5);
            $arProcesso =  validaMascaraDinamica($mascaraProcesso, $codProcesso."/".$anoExercicio);
            $codP   = $arProcesso[1];

        echo $codP;?>
    </td>
</tr>
<tr>
    <td class="label">
        Localização
    </td>
    <td class=field>
        <?=utf8_encode($nomSetor);?>
    </td>
</tr>
<tr>
    <td class=alt_dados colspan="2">
        Dados do despacho
    </td>
</tr>
<?php
if (is_array($despachos)) {
    while (list($key, $val) = each($despachos)) {
?>
<tr>
    <td class="label" width="20%">Usuário</td>
    <td class="field" width="80%">
        <?=$val["codUsuario"]?> - <?=$val["nomUsuario"];?>
    </td>
</tr>
<tr>
    <td class="label" width="20%">Data</td>
    <td class="field" width="80%">
        <?=timestampToBr($val["timestamp"]);?>
    </td>
</tr>
<tr>
    <td class="alt_dados" colspan='2'>Despacho</td>
</tr>
<tr>
    <td class="show_dados" width="80%" colspan='2'>
        <?php
            $val["descricao"] = preg_replace("/\n/","<br>",$val["descricao"]);
            echo $val["descricao"];
        ?>
    </td>
</tr>
<?php
    }
}
?>
<tr>
    <td class='field' colspan='2'>
        <input type="button" value="Fechar" style='width: 60px;' onClick="window.close();">
    </td>
</tr>
</table>
<?php
    } else {
        echo "<br><b>Erro! Nenhum despacho selecionado!</b>";
    }
include '../../../framework/include/rodape.inc.php';
?>
