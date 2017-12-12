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
* Manutneção de relatórios
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24715 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:38:35 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.03.94
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_FW_LEGADO."funcoesLegado.lib.php");
setAjuda("UC-01.03.94");

$controle = $_REQUEST['controle'];

if (!isset($controle)) {
    $controle = 0;
}

switch ($controle) {
case 0:
?>
<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.action = "<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId()?>&controle="+cod;
        f.target = 'oculto';
        f.submit();
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.action = "relatorioAuditoriaMostra.php?<?=Sessao::getId()?>";
            f.target = "telaPrincipal";
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }
</script>
<form action="relatorioAuditoriaMostra.php?<?=Sessao::getId()?>" method="POST" name="frm">
  <table width="100%">
<tr>
<td class="alt_dados" colspan=2>Filtrar por:</td>
</tr>
<tr>
    <td class="label">Módulo</td>
    <td class='field'>
    <?php
        echo montaComboGenerico("moduloCod", "administracao.modulo", "cod_modulo", "nom_modulo", "",
                "style='width: 200px;' ", "", true, false, false,"","Todos");
    ?>
    </td>
</tr>
<tr>
    <td class='label' width='15%'>Usuário:</td>
    <td class='field' width='85%' nowrap>
        <input type='text' name="numCgm" value="" size='5' maxlength='10' onBlur="validacao(1);" onKeyPress="return(isValido(this,event,'0123456789'))" >
        <input type='text' name="nomCgm" size=25 readonly="" value="">
        &nbsp;<a href="javascript:procurarCgm('frm','numCgm','nomCgm','usuario','<?=Sessao::getId();?>');"><img
        src='<?=CAM_FW_IMAGENS."procuracgm.gif";?>' alt='Busca' width='20' height='20' border='0'></a>
    </td>
</tr>

<tr>
    <td class="label">Data Inicial:</td>
    <td class="field">
    <?php geraCampoData("sDataIni", $sDataIni, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>
<!--
    <input
        type="text" name="sDataIni" size="10" maxlength="10">&nbsp;<a
        href="javascript:MostraCalendario('frm','sDataIni','<?=Sessao::getId()?>');"><img
        src="../../images/calendario.gif" border=0></a></td>
-->
</tr>
<tr>
    <td class="label">Data Final:</td>
    <td class="field">
    <?php geraCampoData("sDataFim", $sDatafim, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','Sessao::getId()');};\"" );?>

<!--
    <input
        type="text" name="sDataFim" readonly="" size="10" maxlength="10">&nbsp;<a
        href="javascript:MostraCalendario('frm','sDataFim','<?=Sessao::getId()?>');"><img
        src="../../images/calendario.gif" border=0></a></td>
-->
</tr>

<tr>
    <td class="label">Ordenar por:</td>
    <td class="field">
        <select name="orderby">
        <option value="a.nom_acao" SELECTED>Ação</option>
        <option value="au.timestamp">Data</option>
        <option value="f.nom_funcionalidade">Funcionalidade</option>
        <option value="m.nom_modulo">Módulo</option>
        <option value="u.username">Usuário</option>
        </select>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(1,1,0); ?>
    </td>
</tr>
</table>
</form>
<?php
    break;

//Busca o nome do usuário de acordo com o cgm fornecido
case 1:
    $js = "";

    $numCgm = $_REQUEST['numCgm'];

    if (strlen($numCgm) > 0) {
        $sql = "Select u.numcgm, u.username, c.nom_cgm
                From administracao.usuario as u, sw_cgm as c
                Where u.numcgm = c.numcgm
                And u.numcgm = '".$numCgm."' ";

        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();

            if (!$conn->eof()) {
                $nom = AddSlashes($conn->pegaCampo("nom_cgm"));
                $js .= "f.nomCgm.value = '".$nom."' ";
            } else {
                $js .= "f.nomCgm.value = 'USUÁRIO INVÁLIDO' ";
            }
        $conn->limpaSelecao();

    }
    break;
}//fim switch

executaFrameOculto($js);

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';?>
