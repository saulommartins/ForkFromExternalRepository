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

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"  );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"  ); //Inclui classe para inserir auditoria

if (!(isset($ctrl))) {
    $ctrl = 0;
}

if (isset($pagina)) {
    $ctrl = 1;
}

switch ($ctrl) {
case 0:
?>
<form name="frm" action="<?=$PHP_SELF;?>?<?=$sessao->id?>" method="POST">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Dados para Filtro</td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Código do cidadão">Código</td>
        <td class="field" width="80%">
            <input type="text" name="codCidadao" size="6" maxlength="6" onKeyPress="return(isValido(this, event,'0123456789'))">
        </td>
    </tr>
    <tr>
        <td class="label" title="Nome do cidadão">Nome</td>
        <td class="field">
            <input type="text" name="nomCidadao" size="40" maxlength="200">
        </td>
    </tr>
    <tr>
        <td class="label" title="RG do cidadão">RG</td>
        <td class="field">
            <input type="text" name="rg" size="10" maxlength="10" onKeyPress="return(isValido(this, event,'0123456789'))">
        </td>
    </tr>
    <tr>
        <td class="label" title="Data de nascimento do cidadão">Data de Nascimento</td>
        <td class=field>
            <?php geraCampoData("dtNasc", $dataNasc, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <input type="hidden" name="ctrl" value="1">
            <input type="submit" value="Ok" onClick="" style="width: 60px">
        </td>
    </tr>
</table>
</form>
<?php
break;
case 1:

$sql = "SELECT cod_cidadao,nom_cidadao FROM cse.cidadao ";

$sqlFiltro = "";

if ($codCidadao != "") {
    $sqlFiltro .= " cod_cidadao = ".$codCidadao." AND " ;
}

if ($nomCidadao != "") {
    $sqlFiltro .= " lower(nom_cidadao) LIKE lower('%".$nomCidadao."%') AND ";
}

if ($dtNasc != "") {
$dtNasc = dataToSql($dtNasc);
$sqlFiltro .= " dt_nascimento = '".$dtNasc."' AND ";
}

if ($rg != "") {
    $sqlFiltro .= " AND num_rg = '".$rg."' AND ";
}

if ($sqlFiltro) {
    $sql .= " WHERE ".substr($sqlFiltro, 0, strlen($sqlFiltro) - 4);
}

if ($volta == "true") {
    $sql = $sessao->transf3;
} else {
    $sessao->transf3= $sql;
}

//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_cidadao)","ASC");
$sSQL = $paginacao->geraSQL();

//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();
if ($conn->numeroDeLinhas==0) {
    exit("<br><b>Nenhum registro encontrado!</b>");
}
?>
<table width='100%'>
    <tr>
        <td class='alt_dados' colspan="4">
            Registros cadastrados
        </td>
    </tr>
    <tr>
        <td width="5%" class="label">
            &nbsp;
        </td>
        <td width="12%" class="labelcenter">
            Código
        </td>
        <td width="80%" class="labelcenter">
            Nome do cidadão
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    $iCont = $paginacao->contador();
    while (!$conn->eof()) {
        $cod = $conn->pegaCampo("cod_cidadao");
        $nom = $conn->pegaCampo("nom_cidadao");
        $conn->vaiProximo();
?>
    <tr>
        <td class="label">
            <?=$iCont++?>
        </td>
        <td class='show_dados_right'>
            <?=$cod;?>
        </td>
        <td class='show_dados'>
            <?=$nom;?>
        </td>
        <td class='botao' width='10%'>
            <a href="alteraCidadaoMostra.php?<?=$sessao->id;?>&codCidadao=<?=$cod;?>&alterar=true&pagina=<?=$pagina;?>" >
            <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border='0'>
            </a>
        </td>
    </tr>
<?php
    }
$html .= "</table>";
echo $html;
?>
    <table width='450' align='center'><tr><td align='center'><font size='2'>
    <?php $paginacao->mostraLinks();  ?>
    </font></tr></td></table>
<?php
break;
}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
