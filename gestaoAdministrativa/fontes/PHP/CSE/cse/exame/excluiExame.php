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
* Arquivo de instância para Exame
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.92
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."paginacaoLegada.class.php");
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (isset($excluir)) {
    $codExame = $excluir;
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

    function validacao(cod)
    {
        var f = document.frm;
        f.controle.value = cod;
        f.submit();
    }

    function habilitaBotaoOk(boValida)
    {
        if (boValida) {
            document.frm.ok.disabled = false;
        } else {
            aux = document.getElementById('lista');
            aux.innerHTML = "&nbsp;";
            document.frm.ok.disabled = true;
        }
    }

    function Salvar()
    {
        var f = document.frm;
        f.controle.value = 0;
        f.target = "telaPrincipal";
        f.submit();
    }

    function limpaResultado()
    {
        var aux = document.getElementById('lista');
        aux.innerHTML = '&nbsp;';
        limpaSelect(document.frm.codTipo,1);
        document.frm.codTxtTipo.value = "";
        document.frm.codTipo.disabled = true;
        document.frm.codTxtTipo.disabled = true;
        document.frm.codTxtClassificacao.focus();

        return false;
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target="oculto">
<input type='hidden' name='controle' value=''>
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Classificação / Tratamento Cadastrados
        </td>
    </tr>
    <tr>
        <td class='label' width='20%'>
            Classificação
        </td>
        <td class='field' width='80%'>
            <input type="text" name="codTxtClassificacao" value="<?=$codClassificacao != "XXX" ? $codClassificacao : "";?>" size="5" maxlength="5" onChange="javascript: if (preencheCampo( this, document.frm.codClassificacao )) {validacao(2);} else {limpaResultado();}"onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
            $combo = montaComboGenerico("codClassificacao", "cse.classificacao_tratamento", "cod_classificacao", "nom_classificacao", $codClassificacao,"style='width: 200px;' onchange='preencheCampo( this, document.frm.codTxtClassificacao );validacao(2);' ", "", true, false, false);
            echo $combo;
        ?>
        </td>
    </tr>
    <tr>
        <td class='label'>
            Tratamento
        </td>
        <td class='field'>
<?php
if ($codClassificacao != 'XXX' and $codClassificacao and $codTipo) {
?>
            <input type="text" name="codTxtTipo" value="<?=$codTipo != "XXX" ? $codTipo : "";?>" size="5" maxlength="5" onChange="javascript: habilitaBotaoOk(preencheCampo(this, document.frm.codTipo));" onKeyPress="return(isValido(this, event, '0123456789'));">
<?php
    $combo = montaComboGenerico("codTipo", "cse.tipo_tratamento", "cod_tratamento", "nom_tratamento", $codTipo,"style='width: 200px;' onchange='habilitaBotaoOk(preencheCampo(this, document.frm.codTxtTipo));' ", "Where cod_classificacao = ".$codClassificacao, true, false, false);
    echo $combo;
} else {
    unset($codTipo);
?>
            <input type="text" name="codTxtTipo" value="<?=$codTipo;?>" size="5" maxlength="5" onChange="javascript: habilitaBotaoOk(preencheCampo(this, document.frm.codTipo));" disabled onKeyPress="return(isValido(this, event, '0123456789'));">
            <select name='codTipo' style='width: 200px;' disabled="" onchange='habilitaBotaoOk(preencheCampo(this, document.frm.codTxtTipo));'>
                <option value='XXX'>Selecione</option>
            </select>
<?php
}
?>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <table width="100%" cellspacing=0 border=0 cellpadding=0>
                <tr>
                    <td>
<?php
if ($codClassificacao and $codTipo) {
    $stDisabled = "";
} else {
    $stDisabled = " disabled";
}
?>
                        <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();"<?=$stDisabled;?>>&nbsp;
                        <input type="reset" name="limpar" value="Limpar" style="width: 60px">
                    </td>
                    <td class="fieldright_noborder">
                        <b>* Campos obrigatórios</b>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<?php
if ( isset($codClassificacao) and $codClassificacao != "XXX" and isset($codTipo) and $codTipo != "XXX") {
    $sql  = " Select ";
    $sql .= " cod_exame, nom_exame ";
    $sql .= "  From cse.tipo_exame ";
    $sql .= "  where ";
    $sql .= "  cod_classificacao = ".$codClassificacao." and ";
    $sql .= " cod_tratamento = ".$codTipo." ";
    //echo $sql;
    //Inicia o relatório em html
    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sql,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->complemento='&codClassificacao='.$codClassificacao.'&codTipo='.$codTipo;
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(nom_exame)","ASC");
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
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_exame)","ASC");
        $sSQL = $paginacao->geraSQL();
        //Pega os dados encontrados em uma query
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
    }
?>
<span id="lista">
<table width='100%'>
    <tr>
        <td colspan="4" class="alt_dados">
            Exames Cadastrados
        </td>
    </tr>
    <tr>
        <td width="5%" class="label">
            &nbsp;
        </td>
        <td width="12%" class="labelcenter">
            Código
        </td>
        <td width='80%' class="labelcenter">
            Exame
        </td>
        <td class="label">
            &nbsp;
        </td>
    </tr>
<?php
    if ( !$conn->eof() ) {
        $iCont = $paginacao->contador();
        while (!$conn->eof()) {
            $cod = $conn->pegaCampo("cod_exame");
            $nom = $conn->pegaCampo("nom_exame");
            $nomTipo = pegaDado("nom_tratamento","cse.tipo_tratamento","Where cod_tratamento = '".$codTipo."' ");
            $nomClassificacao = pegaDado("nom_classificacao","cse.classificacao_tratamento","Where cod_classificacao = '".$codClassificacao."' ");
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
        <td class='botao' width='5%'>

            <?php echo "

        <a href='#'
onClick=\"alertaQuestao('".CAM_CSE."cse/exame/excluiExame.php?".$sessao->id."&stDescQuestao=".$nom."','excluir','".$cod."','".$nom."','sn_excluir','$sessao->id');\">
                  <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>";?>

<!--            <a href='#' onClick="alertaQuestao(<?=$onClick;?>);">
                <img src='../../images/btnexcluir.gif' border='0'>
             </a>-->
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="4">
            <b>Nenhum registro encontrado!</d>
        </td>
    </tr>
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
</span>
<?php
}
break;
//Formulário em HTML para entrada de dados
//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    $cse = new cse();

    $nomExame = pegaDado("nom_exame","cse.tipo_exame","Where cod_exame = '".$codExame."' ");

    $objeto = $nomExame;
    if ($cse->excluirExame($codExame) ) {
        //Insere auditoria
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
        $audicao->insereAuditoria();
        //Exibe mensagem e retorna para a página padrão
        alertaAviso($PHP_SELF."?codClassificacao=".$codClassificacao."&codTipo=".$codTipo."&pagina=".$pagina,$objeto,"excluir","aviso");
    } else {
        exibeAviso($objeto,"n_excluir","erro");
        $js .= "f.ok.disabled = false; \n";
    }
    break;
case 2:
    $js = "";
    //Destrói as opções de tipo de tratamento existentes no campo
    $js .= "
        var campo = f.codTipo;
        var campoTxt = f.codTxtTipo;
        var tam = campo.options.length;
            while (tam > 0) {
                campo.options[tam] = null;
                tam = tam - 1 ;
            }
        campo.options[0].selected = true; \n
        campoTxt.value = '';\n";

    if ($codClassificacao != "XXX" or $codClassificacao > 0) {
        $sql = "Select cod_tratamento, nom_tratamento
            From cse.tipo_tratamento
            Where cod_classificacao = ".$codClassificacao;
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        $cont = 1;
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_tratamento");
                $nom = $conn->pegaCampo("nom_tratamento");
                $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.");\n";
                $conn->vaiProximo();
                $cont++;
            }
        $conn->limpaSelecao();
        if ($cont == 1) {
            $js .= "campo.disabled = true;\n";
            $js .= "campoTxt.disabled = true;\n";
            $js .= "f.ok.disabled = true;\n";
            $js .= "aux = d.getElementById('lista'); ";
            $js .= 'aux.innerHTML = "&nbsp;"; ';
        } else {
            $js .= "campo.disabled = false;\n";
            $js .= "campoTxt.disabled = false;\n";
            $js .= "campoTxt.focus();\n";
        }
    } else {
        $js .= "campo.disabled = true;\n";
        $js .= "campoTxt.disabled = true;\n";
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
