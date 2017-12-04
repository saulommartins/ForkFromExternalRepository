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
* Arquivo de instância para TipoTratamento
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.91
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (isset($excluir)) {
    $codDeficiencia = $excluir;
    $controle = 1;
}

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($controle) {
case 0:
?>
<script type="text/javascript">
<!--
function Valida()
{
    var mensagem = "";
    var erro = false;
    var campo;
    var campoaux;
    var f = document.frm;

    campo = document.frm.codClassificacao.value;
    if (campo == "xxx") { // Campo cnpj tem que ter 14 caracteres >
        mensagem += "@Campo Classificação inválido!()";
        erro = true;
    }

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>');
    return !(erro);
}// Fim da function Valida

function Salvar()
{
    if ( Valida() ) {
        document.frm.submit();
    }
}

function limpaResultado()
{
    var aux = document.getElementById('lista');
    aux.innerHTML = '&nbsp;';
    document.frm.codTxtClassificacao.focus();

    return false;
}
//-->
</script>
<form method="post" name="frm" action="<?=$PHP_SELF;?>?<?=$sessao->id;?>" onSubmit="return false;">
<table width='100%'>
    <tr>
        <td colspan="2" class="alt_dados">
            Classificações Cadastradas
        </td>
    </tr>
    <tr>
        <td width="20%" class="label">
            Classificação
        </td>
        <td width="80%" class="field">
            <input type="text" name="codTxtClassificacao" value="<?=$codClassificacao != "xxx" ? $codClassificacao: "";?>" size="5" maxlength="5" onchange="JavaScript: limpaResultado(); preencheCampo(this, document.frm.codClassificacao);">
            <select name="codClassificacao" style="width:200px" onchange="JavaScript: preencheCampo(this, document.frm.codTxtClassificacao);">
                <option value="xxx">Selecione</option>
<?php
$sSQL  = " select ";
$sSQL .= "   cod_classificacao as codClass, ";
$sSQL .= "   nom_classificacao as nomClass ";
$sSQL .= " from cse.classificacao_tratamento ";
$sSQL .= " order by nom_classificacao ";
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();
while ( !$conn->eof() ) {
    $codClass = $conn->pegaCampo('codClass');
    $nomClass = $conn->pegaCampo('nomClass');
    if ($codClass == $codClassificacao) {
        $selected = " selected";
    } else {
        $selected = "";
    }
?>
                <option value="<?=$codClass;?>"<?=$selected;?>><?=$nomClass;?></option>
<?php
    $conn->vaiProximo();
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="field">
            <?php geraBotaoAltera();?>
        </td>
    </tr>
</table>
<span id="lista">
<?php
if ( isset($codClassificacao) and $codClassificacao != "xxx") {
$sql = "Select cod_tratamento, nom_tratamento
        From cse.tipo_tratamento
        Where cod_classificacao = ".$codClassificacao;
//echo $sql;
//Inicia o relatório em html
$paginacao = new paginacaoLegada;
$paginacao->pegaDados($sql,"10");
$paginacao->pegaPagina($pagina);
$paginacao->complemento = "&codClassificacao=".$codClassificacao;
$paginacao->geraLinks();
$paginacao->pegaOrder("lower(nom_tratamento)","ASC");
$sSQL = $paginacao->geraSQL();
//Pega os dados encontrados em uma query
$conn = new dataBaseLegado;
$conn->abreBD();
$conn->abreSelecao($sSQL);
$conn->fechaBD();
$conn->vaiPrimeiro();
if ( $pagina > 0 and $conn->eof() ) {
    $pagina--;
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento = "&codClassificacao=".$codClassificacao;
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(nom_tratamento)","ASC");
    $sSQL = $paginacao->geraSQL();
    //Pega os dados encontrados em uma query
    $conn->abreBD();
    $conn->abreSelecao($sSQL);
    $conn->fechaBD();
    $conn->vaiPrimeiro();
}
?>
<table width="100%">
    <tr>
        <td colspan="4" class="alt_dados">
            Tratamentos cadastrados
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
            Tratamento
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont = $paginacao->contador();
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod_tratamento");
            $nom = $conn->pegaCampo("nom_tratamento");
            $conn->vaiProximo();
?>
    <tr>
        <td class="label">
            <?=$iCont++;?>
        </td>
        <td class='show_dados_right'>
            <?=$cod;?>
        </td>
        <td class='show_dados'>
            <?=$nom;?>
        </td>
        <td class='botao' width='10%'>

            <?php echo "
       <a href='#' onClick=\"alertaQuestao('".CAM_CSE."cse/tipoTratamento/excluiTipoTratamento.php?".$sessao->id."&stDescQuestao=".$nom."','excluir','".$cod."%26codClassificacao=".$codClassificacao."','".$nom."','sn_excluir','$sessao->id');\">
                                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>";?>

            </a>
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="4">
            <b>Nenhum registro encontrado!</td>
        </td>
<?php
    }
?>
</table>
<table width='450' align='center'>
    <tr>
        <td align='center'>
            <font size='2'>
            <?php $paginacao->mostraLinks();?>
            </font>
        </td>
    </tr>
</table>
<?php
}
?>
</span>
    </form>
<?php
    break;

//Formulário em HTML para entrada de dados

//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    $cse = new cse();

    $codTratamento = $excluir;
    $nomTratamento = pegaDado("nom_tratamento","cse.tipo_tratamento","Where cod_tratamento = '".$codTratamento."' ");

    $objeto = $nomTratamento;
    if ($cse->excluirTipoTratamento($codTratamento) ) {
        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
        $audicao->insereAuditoria();
        //Exibe mensagem e retorna para a página padrão
        alertaAviso($PHP_SELF."?codClassificacao=".$codClassificacao."&pagina=".$pagina,$objeto,"excluir","aviso");
    } else {
        exibeAviso($objeto,"n_excluir","erro");
        $js .= "f.ok.disabled = false; \n";
    }
    break;

}//Fim switch

?>
<html>
<head>
<script type="text/javascript">
function executa()
{
    var mensagem = "";
    var erro = false;
    var f = window.parent.frames["telaPrincipal"].document.frm;
    var d = window.parent.frames["telaPrincipal"].document;
    var aux;
    <?php echo $js; ?>

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>');
}
</script>
</head>

<body onLoad="javascript:executa();">

</body>
</html>
<?php
 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
