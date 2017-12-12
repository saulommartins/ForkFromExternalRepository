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

$Revision: 3149 $
$Name$
$Author: pablo $
$Date: 2005-11-30 13:54:33 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.98
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'; //Insere o início da página html

if(!isset($controle))
        $controle = 0;

switch ($controle) {
    case 0:
        $exercicio = pegaConfiguracao("ano_exercicio");
 ?>
<center>
<form name="frm2" action="anexaProcesso.php?<?=Sessao::getId();?>" method="POST">
<input type="hidden" name="controle" value="1">
<table width=250 cellspacing=0 border=0 cellpadding=0>
<tr><td colspan=2 height=10></td></tr>
<tr>
    <td class="label">cód. Processo:</td>
    <td class="field">
        <input type="text" name="codProcesso" value="" size=10 maxlength=20>
    </td>
</tr>
<tr>
    <td class="label">Exercício:</td>
    <td class="field">
        <input type="text" name="anoExercicio" value="<?=$exercicio;?>" size=5 maxlength=4>
    </td>
</tr>

<tr><td colspan=2 height=10></td></tr>
<tr>
    <td colspan=2>
        <input type="submit" value="Anexar">
    </td>
</tr>

</table>
</form>
 <script type="text/javascript">
    placeFocus();
 </script>
<?php
    break;
    case 1:
    $sql = "Select cod_processo, cod_situacao
            From sw_processo
            Where cod_processo = '".$codProcesso."'
            And ano_exercicio = '".$anoExercicio."' ";
    echo "<!--$sql-->\n";
    //Chama a classe do banco de dados e executa a query
    $conn = new dataBaseLegado;
    $conn->abreBD();
    $conn->abreSelecao($sql);
    $conn->vaiPrimeiro();
        if ($conn->numeroDeLinhas > 0) {
            $ok = true; //Processo existe
                //Verfica se o processo pode ser anexado -- Processos 'Em andamento, a receber' e 'anexados' não podem ser anexados
                $codSituacao = $conn->pegaCampo("cod_situacao");
                if ($codSituacao == "2") {
                    $ok = false;
                    $msg = "Este processo não pode ser incluido, pois está 'Em andamento, a receber'!";
                } elseif ($codSituacao == "4") {
                    $ok = false;
                    $msg = "Este processo não pode ser incluido, pois já está anexado a outro!";
                }
        } else {
            $ok = false; //Processo não existe
            $msg = "Não há nenhum processo cadastrado com este número";
        }
    $conn->limpaSelecao();
    $conn->fechaBD();

    if ($ok) {
?>
<script type="text/javascript">
    var combo = window.opener.parent.frames['telaPrincipal'].document.frm.processosAnexos;
    var sNumAnexos = window.opener.parent.frames['telaPrincipal'].document.frm.processosAnexos.options.length;
    var codProcesso = "<?=$codProcesso;?>";
    var jaExiste = false;

    newList = new Array( sNumAnexos );

    for (var i = sNumAnexos - 1; i >= 0; i--) {
        if (combo.options[i].value==codProcesso) {
            document.write("<br><b>Este processo já está anexado!</b>");
            jaExiste = true;
        }
    }
    if (!jaExiste) {
        combo.options[sNumAnexos] = new Option( "Processo <?=$codProcesso."/".$anoExercicio;?>", <?=$codProcesso.".".$anoExercicio;?> );
        window.close();
    }

</script>
<?php } else { ?>
    <br>
    <b><?=$msg;?></b>
<?php } ?>
<br><br>
        <input type="button" value="Voltar" onClick="javascript:history.back(-1);">&nbsp;&nbsp;
        <input type="button" value="Fechar" onClick="javascript:window.close();">
<?php
    break;
}//Fim switch
?>
</html>
