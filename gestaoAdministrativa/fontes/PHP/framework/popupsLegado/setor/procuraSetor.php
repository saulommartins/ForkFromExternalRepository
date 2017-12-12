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

Casos de uso: uc-01.01.00, uc-02.02.02, uc-03.01.04
*/

//     include("../sistema/setup.inc.php");
//     include("tabelas.inc.php");
//     include("views.inc.php");
//     include("../classes/sessao.class.php");
//     include("../classes/dataBase.class.php");
//     include("../bibliotecas/funcoes.lib.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
?>
<?php
if (isset($acao)) {
Sessao::write('acao', $acao);
}
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
function alertaAviso(objeto,tipo,chamada)
{
    var x = 350;
    var y = 200;
    var sessao   = '<?=Sessao::getId()?>';
    var sessaoid = sessao.substr(10,6);
    var sArq = 'alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    //var wVolta=false;
    var sAux = "msga"+ sessaoid +" = window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

</script>

<link rel=STYLESHEET type=text/css href=stylos_ns.css>

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
//********************************************************************************
if (!(isset($ctrl)))
$ctrl = 0;
//echo $ctrl;
switch ($ctrl) {
case 0:
?>

<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.codOrgao.value;
            if (campo == 'xxx') {
            mensagem += "- Selecione o Órgão\n";
            erro = true;
         }

         campo = document.frm.codUnidade.value;
            if (campo == 'xxx') {
            mensagem += "- Selecione a Unidade\n";
            erro = true;
         }

         campo = document.frm.codDepartamento.value;
            if (campo == 'xxx') {
            mensagem += "- Selecione o Departamento\n";
            erro = true;
         }

         campo = document.frm.codSetor.value;
            if (campo == 'xxx') {
            mensagem += "- Selecione o Setor\n";
            erro = true;
         }

         campo = document.frm.exercicio.value.length;
            if (campo == 0) {
            mensagem += "- Selecione o Exercício\n";
            erro = true;
         }

            if (erro) {
                window.alert(mensagem);
                //alertaAviso(mensagem,'form','erro');
            } else {
                return !(erro);
            }
      }
      function Salvarx()
      {
         if (Valida()) {
            document.frm.action = "procuraSetor.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
         }
      }

      function Salvar(nomesetor,setor,ano)
      {
        var sNomeSetor;
        var sBem;
        var sAno;
        sNomeSetor = nomesetor;
        sSetor = setor;
        sAno = ano;
        if (Valida()) {
            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.<?=$campoNomeSetor;?>.value = sNomeSetor;
            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.<?=$campoSetor;?>.value = sSetor;
            window.opener.parent.frames['telaPrincipal'].document.<?=$nomForm;?>.<?=$campoexercicio;?>.value = sAno;
            window.close();
        }
      }
 </script>

<form name="frm" action="procuraSetor.php?<?=Sessao::getId()?>" method="POST">

<table width=300>

<tr>
<td class="alt_dados" colspan=2>Procure o Setor</td>
</tr>

<tr>
<td class=label>Órgão</td>
<td class=field>
<select name=codOrgao onChange="document.frm.submit();">
<option value=xxx SELECTED>Selecione</option>
<?php
        //Faz o combo de Órgãos
        $sSQL = "SELECT cod_orgao, nom_orgao, ano_exercicio FROM administracao.orgao ORDER by nom_orgao";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboCodOrgao = "";
        while (!$dbEmp->eof()) {
            $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
            $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $chave = $codOrgaof."-".$anoEf;
            $dbEmp->vaiProximo();
            $comboCodOrgao .= "<option value=".$chave;
                if(isset($codOrgao))
                    if($chave == $codOrgao)
                        $comboCodOrgao .= " SELECTED";
            $comboCodOrgao .= ">".$nomOrgaof." - ".$anoEf."</option>\n";
        }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $comboCodOrgao;

?>
</select>
</td>
</tr>

<tr>
<td class=label>Unidade</td>
<td class=field>
<select name=codUnidade onChange="document.frm.submit();">
<option value=xxx SELECTED>Selecione</option>
<?php
if (isset($codOrgao)) {
        if ($codOrgao != "xxx") {
            $variaveis = explode("-",$codOrgao);
            $codOrgaom = $variaveis[0];
            $codAno = $variaveis[1];

            //Faz o combo de Órgãos
            $sSQL = "SELECT cod_unidade, nom_unidade FROM administracao.unidade WHERE cod_orgao = ".$codOrgaom." AND ano_exercicio = '".$codAno."' ORDER by nom_unidade";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboCodUnio = "";
            while (!$dbEmp->eof()) {
                $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
                $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
                $chaveU = $codUnidadef."-".$codOrgaom."-".$codAno;
                $dbEmp->vaiProximo();
                $comboCodUni .= "<option value=".$chaveU;
                    if(isset($codUnidade))
                        if($chaveU == $codUnidade)
                            $comboCodUni .= " SELECTED";
                $comboCodUni .= ">".$nomUnidadef." - ".$codAno."</option>\n";
            }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboCodUni;
    }
}
?>
</select>
</td>
</tr>

<tr>
<td class=label>Departamento</td>
<td class=field>
<select name=codDepartamento onChange="document.frm.submit();">
<option value=xxx SELECTED>Selecione</option>
<?php
if (isset($codUnidade)) {
        if ($codUnidade != "xxx") {
            $variaveis = explode("-",$codUnidade);
            $codUnidaded = $variaveis[0];
            $codOrgaod = $variaveis[1];
            $codAnod = $variaveis[2];

            //Faz o combo de Órgãos
            $sSQL = "SELECT cod_departamento, nom_departamento FROM administracao.departamento WHERE cod_unidade = ".$codUnidaded." AND cod_orgao = ".$codOrgaod." AND ano_exercicio = '".$codAnod."' ORDER by nom_departamento";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboCodDep = "";
            while (!$dbEmp->eof()) {
                $codDepartamentof  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamentof  = trim($dbEmp->pegaCampo("nom_departamento"));
                $chaveD = $codDepartamentof."-".$codUnidaded."-".$codOrgaod."-".$codAnod;
                $dbEmp->vaiProximo();
                $comboCodDep .= "<option value=".$chaveD;
                    if(isset($codDepartamento))
                        if($chaveD == $codDepartamento)
                            $comboCodDep .= " SELECTED";
            $comboCodDep .= ">".$nomDepartamentof." - ".$codAnod."</option>\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboCodDep;
}
}
?>
</select>
</td>
</tr>

<tr>
<td class=label>Setor</td>
<td class=field>
<select name=codSetor onChange="document.frm.submit();">
<option value='xxx' SELECTED>Selecione</option>
<?php
if (isset($codDepartamento)) {
        if ($codDepartamento != "xxx") {
            $variaveis = explode("-",$codDepartamento);
            $codDepartamentos = $variaveis[0];
            $codUnidades = $variaveis[1];
            $codOrgaos = $variaveis[2];
            $codAnos = $variaveis[3];

            //Faz o combo de Órgãos
            $sSQL =     "SELECT
                        cod_setor,
                        nom_setor,
                        ano_exercicio
                        FROM
                        administracao.setor
                        WHERE
                        cod_departamento = ".$codDepartamentos." AND
                        cod_unidade = ".$codUnidades." AND
                        cod_orgao = ".$codOrgaos." AND
                        ano_exercicio = '".$codAnos."'
                        ORDER by nom_setor";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboCodDep = "";
            while (!$dbEmp->eof()) {
                $codSetorf  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetorf  = trim($dbEmp->pegaCampo("nom_setor"));
                $anoExercicio = trim($dbEmp->pegaCampo("ano_exercicio"));
                $chaveS = $codSetorf."-".$codDepartamentos."-".$codUnidades."-".$codOrgaos."-".$codAnos;
                $dbEmp->vaiProximo();
                $comboCodSet .= "<option value=".$chaveS;
                    if(isset($codSetor))
                        if($chaveS == $codSetor)
                            $comboCodSet .= " SELECTED";
            $comboCodSet .= ">".$nomSetorf." - ".$codAnos."</option>\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboCodSet;
    }
}
?>
</select>
</td>
</tr>

<tr>
<td class=label>Exercício</td>
<td class=field><input type="text" name="exercicio" value="<?=$anoExercicio?>" size=4 maxlength=4>
<input type="hidden" name="nomForm" value="<?=$nomForm;?>">
<input type="hidden" name="campoNomeSetor" value="<?=$campoNomeSetor;?>">
<input type="hidden" name="campoSetor" value="<?=$campoSetor;?>">
<input type="hidden" name="campoexercicio" value="<?=$campoexercicio;?>">
</td>
</tr>

<tr>
<?php
//echo $codSetor."<br>";
if (isset($codSetor)) {
if ($codSetor != 'xxx') {
    $var = explode("-", $codSetor);
    $select =   "SELECT
                nom_setor
                FROM
                administracao.setor
                WHERE
                cod_orgao = ".$var[3]." AND
                cod_unidade = ".$var[2]." AND
                cod_departamento = ".$var[1]." AND
                cod_setor = ".$var[0];
    //echo $select."<br>";
    $dbConfig = new databaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $nome = $dbConfig->pegaCampo("nom_setor");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();
    /*echo $var[0]."<br>";
    echo $var[1]."<br>";
    echo $var[2]."<br>";
    echo $var[3]."<br>";
    echo $nome."<br>";*/
    $chaveS = $var[3].".".$var[2].".".$var[1].".".$var[0];
}
}
?>
<td class=field colspan=2><input type="button" value="OK" style='width: 60px;'
onClick="Salvar('<?=$nome?>','<?=$chaveS?>','<?=$codAnos?>');"></td>
</tr>

</table>
</form>

<?php
    break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
