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
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';

//********************************************************************************
if (!(isset($ctrl)))
$ctrl = 0;

switch ($ctrl) {
case 0:
?>

<script type="text/javascript">
    function isNumber(fld, e)
    {
        var key = '';
        var strCheck = '0123456789';
        var whichCode = (window.Event) ? e.which : e.keyCode;
            //Os códigos abaixo permitem a navegação através das setas, tecla home, end, delete...
            if (whichCode == 13) return true;  // Enter
            if (whichCode == 0) return true;
            if (whichCode == 1) return true;
            if (whichCode == 2) return true;
            if (whichCode == 3) return true;
            if (whichCode == 4) return true;
            if (whichCode == 5) return true;
            if (whichCode == 6) return true;
            if (whichCode == 7) return true;
            if (whichCode == 8) return true;  // Backspace
            if (whichCode == 9) return true;
            if (whichCode == 10) return true;
        key = String.fromCharCode(whichCode);  // Get key value from key code
        if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
    }

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

            if (erro) SistemaLegado::alertaAviso(mensagem,'form','erro');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.action = "procuraVeiculo.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
         }
      }
 </script>

<form name="frm" action="procuraVeiculo.php?<?=Sessao::getId()?>" method="POST">

<table width=300>

<tr>
<td class="alt_dados" colspan=2>Procure o Veículo</td>
</tr>

<tr>
<td class=label>Marca</td>
<td class=field>
<select name=codMarca onChange="document.frm.submit();">
<option value=xxx SELECTED>Todos</option>
<?php
        //Faz o combo de marcas
        $sSQL = "SELECT cod_marca, nom_marca FROM sw_marca ORDER by nom_marca ";
        $dbEmp = new dataBase;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboCodMarca = "";
        while (!$dbEmp->eof()) {
            $codMarcaf  = trim($dbEmp->pegaCampo("cod_marca"));
            $nomMarcaf  = trim($dbEmp->pegaCampo("nom_marca"));
            $dbEmp->vaiProximo();
            $comboCodMarca .= "<option value=".$codMarcaf;
                if(isset($codMarca))
                    if($codMarca == $codMarcaf)
                        $comboCodMarca .= " SELECTED";
            $comboCodMarca .= ">".$nomMarcaf."</option>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $comboCodMarca;

?>
</select>
</td>
</tr>

<tr>
<td class=label>Modelo</td>
<td class=field>
<select name=codModelo onChange="document.frm.submit();">
<option value=xxx SELECTED>Todos</option>
<?php
if (isset($codMarca)) {
        if ($codMarca != "xxx") {

            //Faz o combo de modelos
            $sSQL = "SELECT cod_modelo, nom_modelo FROM sw_modelo WHERE cod_marca = ".$codMarca." ORDER by nom_modelo";
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboCodMod = "";
            while (!$dbEmp->eof()) {
                $codModelof  = trim($dbEmp->pegaCampo("cod_modelo"));
                $nomModelof  = trim($dbEmp->pegaCampo("nom_modelo"));
                $dbEmp->vaiProximo();
                $comboCodMod .= "<option value=".$codModelof;
                    if(isset($codModelo))
                        if($codModelo == $codModelof)
                            $comboCodMod .= " SELECTED";
                $comboCodMod .= ">".$nomModelof."</option>\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboCodMod;
}
}
?>
</select>
</td>
</tr>

<tr>
<td class=label>Tipo</td>
<td class=field>
<select name=codTipo onChange="document.frm.submit();">
<option value=xxx SELECTED>Todos</option>
<?php
        //Faz o combo de tipos
        $sSQL = "SELECT cod_tipo, nom_tipo FROM ".TIPO_VEICULO." ORDER by nom_tipo ";
        $dbEmp = new dataBase;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboCodTipo = "";
        while (!$dbEmp->eof()) {
            $codTipof  = trim($dbEmp->pegaCampo("cod_tipo_veiculo"));
            $nomTipof  = trim($dbEmp->pegaCampo("nom_tipo_veiculo"));
            $dbEmp->vaiProximo();
            $comboCodTipo .= "<option value=".$codTipof;
                if(isset($codTipo))
                    if($codTipof == $codTipo)
                        $comboCodTipo .= " SELECTED";
        $comboCodTipo .= ">".$nomTipof."</option>\n";
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $comboCodTipo;
?>
</select>
</td>
</tr>

<tr>
    <td class=label>Placa</td>
    <td class=field>
        <input type='text' name='placa' value='<?=$placa;?>' size='8' maxlength='7'>
    </td>
</tr>

<tr>
    <td class=label>Prefixo</td>
    <td class=field>
        <input type='text' name='prefixo' value='<?=$prefixo;?>' size='15' maxlength='15'>
    </td>
</tr>
<tr>
    <td class=label>Responsável:</td>
    <td class=field>
        <input type='text' name='codResponsavel' value='<?=$codResponsavel;?>' onKeyPress="return(isNumber(this, event))" size='10' maxlength='10'>
    </td>
</tr>

<tr>
<td>
    <input type="hidden" name="nomForm" value="<?=$nomForm;?>">
    <input type="hidden" name="campoCodVeiculo" value="<?=$campoCodVeiculo;?>">
</td>
</tr>

<tr>
<td class=field colspan=2><input type="button" value="OK"  style='width: 60px;' onClick="Salvar();"></td>
</tr>

</table>
</form>

<?php
    break;

case 1:
?>
<!-------------------------------------------------------------------------------->
<script type="text/javascript">
function Insere(codVeiculo)
{
    var iCodVeiculo;
    iCodVeiculo = codVeiculo;
        window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.<?=$campoCodVeiculo;?>.value = iCodVeiculo;
        window.close();
}
</script>
<!-------------------------------------------------------------------------------->
<table width=320>
<tr>
    <td class="alt_dados">Código</td>
    <td class="alt_dados">Modelo</td>
    <td class="alt_dados">Marca</td>
    <td class="alt_dados">Placa</td>
</tr>

<?php
$sql = "SELECT v.cod_veiculo, mar.nom_marca, mod.nom_modelo,
(upper(substr(v.placa, 1, 3)) || '-' || substr(v.placa, 4, 4) ) as placa
FROM sw_veiculo as v, sw_marca as mar, sw_modelo as mod
WHERE v.cod_marca = mod.cod_marca
AND v.cod_modelo = mod.cod_modelo
AND mod.cod_marca = mar.cod_marca ";

if($codMarca!='xxx')
    $sql .= " And mod.cod_marca = '".$codMarca."' ";

if($codModelo!='xxx')
    $sql .= " And mod.cod_modelo = '".$codModelo."' ";

if($codTipo!='xxx')
    $sql .= " And v.cod_tipo_veiculo = '".$codTipo."' ";

if(strlen($placa)>0)
    $sql .= " And lower(v.placa) like lower('%".$placa."%') ";

if(strlen($prefixo)>0)
    $sql .= " And v.prefixo = '".$prefixo."' ";

if(strlen($codResponsavel)>0)
    $sql .= " And v.cod_responsavel = '".$codResponsavel."' ";

$sql .= " ORDER by v.placa ";

    $dbEmp = new dataBase;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sql);
    $dbEmp->vaiPrimeiro();
    $html = "";
    while (!$dbEmp->eof()) {
        $codVeiculo  = trim($dbEmp->pegaCampo("cod_veiculo"));
        $placa  = trim($dbEmp->pegaCampo("placa"));
        $nomMarca = trim($dbEmp->pegaCampo("nom_marca"));
        $nomModelo = trim($dbEmp->pegaCampo("nom_modelo"));
        $dbEmp->vaiProximo();
        $html .= "<tr>";
        $html .= "<td class=show_dados><a href=# onClick=\"Insere('".$codVeiculo."');\">".$codVeiculo."</a></td>";
        $html .= "<td class=show_dados><a href=# onClick=\"Insere('".$codVeiculo."');\">".$nomMarca."</a></td>";
        $html .= "<td class=show_dados><a href=# onClick=\"Insere('".$codVeiculo."');\">".$nomModelo."</a></td>";
        $html .= "<td class=show_dados><a href=# onClick=\"Insere('".$codVeiculo."');\">".$placa."</a></td>";
        $html .= "</tr>";
    }
    echo $html;
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    //echo $dbEmp->numeroDeLinhas;
?>
</table>
<input type="button" value="Voltar" style='width: 60px;' onClick="javascript:document.location.replace('procuraVeiculo.php?<?=Sessao::getId()?>');">
&nbsp;
<input type="button" value="Fechar" style='width: 60px;' onClick="javascript:window.close();">

<?php
break;
}
include 'rodape.php';
?>
