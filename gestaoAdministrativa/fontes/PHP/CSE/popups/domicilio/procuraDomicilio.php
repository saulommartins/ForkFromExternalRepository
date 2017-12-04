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
* Arquivo de instância para manutenção de Cidadão
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 4239 $
$Name$
$Author: lizandro $
$Date: 2005-12-22 13:53:44 -0200 (Qui, 22 Dez 2005) $

* Casos de uso: uc-01.07.97
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");

if (isset($acao))
$sessao->acao = $acao;

$sSQL = "SELECT nom_acao FROM administracao.acao WHERE cod_acao =".$sessao->acao;
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
    var sessao   = '<?=$sessao->id?>';
    var sessaoid = sessao.substr(10,6);
    var sArq = 'alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    //var wVolta=false;
    var sAux = "msga"+ sessaoid +" = window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}
function voltaPagina()
{
    document.frm1.action = "procuraDomicilio.php?<?=$sessao->id?>&ctrl=0";
    document.frm1.submit();
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

<form action="procuraDomicilio.php?<?=$sessao->id?>&ctrl=1" method="POST" name=frm1>

<?php
//********************************************************************************
if (!(isset($ctrl))) {
$ctrl = 0;
unset($sessao->transf3);
unset($sessao->transf4);
}

switch ($ctrl) {
case 0:
unset($sessao->transf3);
unset($sessao->transf4);
?>
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Busca de Domicílio</td>
    </tr>
    <tr>
        <td class="label" widht="20%">Tipo de Localidade</td>
        <td class="field" widht="80%">
            <select name="codLocalidade">
                <option value="0">Selecione uma opção</option>
        <?php
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select =   "SELECT
                        cod_localidade,
                        nom_localidade
                        FROM
                        cse.tipo_localidade
                        WHERE
                        cod_localidade > 0
                        order by nom_localidade";
            //echo $select."<br>";
            $dbConfig->abreSelecao($select);
            while (!$dbConfig->eof()) {
                $lista[] = $dbConfig->pegaCampo("cod_localidade")."/".$dbConfig->pegaCampo("nom_localidade");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
            if ($lista != "") {
                while (list($key, $val) = each($lista)) {
                    $combo = explode("/", $val);
                    echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
                }
            }
        ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label">Tipo de Cobertura</td>
        <td class="field">
            <select name="codCobertura">
                <option value="0">Selecione uma opção</option>
        <?php
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select =   "SELECT
                        cod_cobertura,
                        nom_cobertura
                        FROM
                        cse.tipo_cobertura
                        WHERE
                        cod_cobertura > 0
                        order by nom_cobertura";
            //echo $select."<br>";
            $dbConfig->abreSelecao($select);
            while (!$dbConfig->eof()) {
                $lista[] = $dbConfig->pegaCampo("cod_cobertura")."/".$dbConfig->pegaCampo("nom_cobertura");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
            if ($lista != "") {
                while (list($key, $val) = each($lista)) {
                    $combo = explode("/", $val);
                    echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
                }
            }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label">Tipo de Domicílio</td>
        <td class="field">
            <select name="codTipoDomicilio">
                <option value="0">Selecione uma opção</option>
        <?php
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select =   "SELECT
                        cod_domicilio,
                        nom_domicilio
                        FROM
                        cse.tipo_domicilio
                        WHERE
                        cod_domicilio > 0
                        order by nom_domicilio";
            //echo $select."<br>";
            $dbConfig->abreSelecao($select);
            while (!$dbConfig->eof()) {
                $lista[] = $dbConfig->pegaCampo("cod_domicilio")."/".$dbConfig->pegaCampo("nom_domicilio");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            $dbConfig->fechaBd();
            if ($lista != "") {
                while (list($key, $val) = each($lista)) {
                    $combo = explode("/", $val);
                    echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
                }
            }
        ?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label">Ordem:</td>
        <td class="field">
            <input type="radio" name="ordem" value="cod_domicilio" checked>Código&nbsp;
            <input type="radio" name="ordem" value="logradouro">Logradouro
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type="submit" name="ok" value="OK" style="width: 60px">
        </td>
    </tr>
</table>
<?php
    break;
    case 1:
?>
<script type="text/javascript">
function Insere(codDomicilio,logradouro, numero, complemento)
{
    //var iCodDomicilio;
    //var sLogradouro;
    //iCodDomicilio = codDomicilio;
    //sLogradouro = logradouro;
        window.opener.parent.frames['telaPrincipal'].document.frm.codDomicilio.value = codDomicilio;
        var nomeDomicilio = logradouro +' - '+ numero;
        if (complemento != 'false') {
            nomeDomicilio += ' - '+ complemento;
        }
        window.opener.parent.frames['telaPrincipal'].document.frm.nomDomicilio.value = nomeDomicilio;
        //window.opener.parent.frames['telaPrincipal'].document.frm.numDomicilio.value = numero;
        //window.opener.parent.frames['telaPrincipal'].document.frm.proximo.disabled = false;
        window.close();
}
</script>
<?php
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $select  = " SELECT ";
    $select .= "     cod_domicilio, ";
    $select .= "     logradouro, ";
    $select .= "     numero, ";
    $select .= "     complemento ";
    $select .= " FROM ";
    $select .= "     cse.domicilio ";
    $select .= " WHERE ";
    $select .= "     cod_domicilio > 0 ";
    if ($codLocalidade != 0) {
        $select .= " AND cod_localidade = ".$codLocalidade;
    }

    if ($codCobertura != 0) {
        $select .= " AND cod_cobertura = ".$codCobertura;
    }

    if ($codTipoDomicilio != 0) {
        $select .= " AND cod_tipo_domicilio = ".$codTipoDomicilio." ";
    }
    if (!(isset($sessao->transf3['domicilio']))) {
        $sessao->transf4['domicilio'] = "";
        $sessao->transf4['domicilio'] = $select;
        $sessao->transf3['domicilio'] = $ordem;
    }
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sessao->transf4['domicilio'],"15");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(".$sessao->transf3['domicilio'].")","ASC");
    $sSQL = $paginacao->geraSQL();
    $dbConfig->abreSelecao($sSQL);
    $dbConfig->fechaBd();
?>
<table width="100%">
    <tr>
        <td class="alt_dados">Código Domicílio</td>
        <td class="alt_dados" colspan="2">Logradouro</td>
    </tr>
<?php
    if ( !$dbConfig->eof() ) {
        while (!$dbConfig->eof()) {
            $codDomicilio   = $dbConfig->pegaCampo("cod_domicilio");
            $logradouro     = $dbConfig->pegaCampo("logradouro");
            $numero         = $dbConfig->pegaCampo("numero");
            $complemento    = $dbConfig->pegaCampo("complemento");
            $codDomicilio   = $dbConfig->pegaCampo("cod_domicilio");
            $logradouro     = $dbConfig->pegaCampo("logradouro");
            $numero         = $dbConfig->pegaCampo("numero");
            $complemento    = $dbConfig->pegaCampo("complemento");
            $nomDomicilio = $logradouro." - ".$numero;
            if ($complemento) {
                $nomDomicilio .= " - ".$complemento;
            } else {
                $complemento = "false";
            }
?>
    <tr>
        <td class="show_dados_right" width="30%"><?=$codDomicilio;?></td>
        <td class="show_dados">
            <a href="#" onclick="javascript:Insere(<?=$codDomicilio;?>,'<?=$logradouro;?>','<?=$numero;?>','<?=$complemento;?>' );"><?=$nomDomicilio;?></a>
        </td>
    </tr>
<?php
        $dbConfig->vaiProximo();
        }
    } else {
?>
    <tr>
        <td class="show_dados" colspan="2">
            <b>Não Foram Encontrados Domicílios para sua Busca</b>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type='button' name='voltar' value='Voltar' onclick='javascript:voltaPagina();'>
        </td>
    </tr>
<?php
    }
    $dbConfig->limpaSelecao();
?>
</table>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
            <?=$paginacao->mostraLinks();?>
            </font>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    }
?>
