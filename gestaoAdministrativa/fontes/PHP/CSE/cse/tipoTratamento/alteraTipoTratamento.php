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

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

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

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
    return !(erro);
}// Fim da function Valida

function Salvar()
{
    if ( Valida() ) {
        document.frm.submit();
    }
}

function Cancela()
{
    var aux = document.getElementById('lista');
    aux.innerHTML = '&nbsp;';
    document.frm.codTxtClassificacao.value = "";
    document.frm.codClassificacao.options[1].selected;
    document.frm.codTxtClassificacao.focus();

    return false;
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
            <input type="text" name="codTxtClassificacao" value="<?=$codClassificacao != "xxx" ? $codClassificacao: "";?>" size="5" maxlength="5" onchange="JavaScript: limpaResultado();preencheCampo(this, document.frm.codClassificacao);">
            <select name="codClassificacao" style="width:200px" onchange="JavaScript: preencheCampo(this, document.frm.codTxtClassificacao)">
                <option value="xxx">Selecione</option>
<?php
$sSQL  = " select ";
$sSQL .= "   cod_classificacao as codClass, ";
$sSQL .= "   nom_classificacao as nomClass ";
$sSQL .= " from cse.classificacao_tratamento ";
$sSQL .= " where cod_classificacao > 0 ";
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
            <a href='<?=$PHP_SELF;?>?<?=$sessao->id;?>&controle=1&codTratamento=<?=$cod;?>&pagina=<?=$pagina;?>'>
            <img src='<?=CAM_FW_IMAGENS."btneditar.gif";?>' border='0'>
            </a>
        </td>
    </tr>
<?php
        }
    } else {
?>
    <tr>
        <td class="show_dados_center" colspan="4">
            <b>Nenhum registro encrontrado!</d>
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
<?php
}
?>
</span>
    </form>
<?php
    break;

//Formulário em HTML para entrada de dados
case 1:
$codClassificacao = pegaDado("cod_classificacao","cse.tipo_tratamento","Where cod_tratamento = '".$codTratamento."' ");
$nomTratamento = pegaDado("nom_tratamento","cse.tipo_tratamento","Where cod_tratamento = '".$codTratamento."' ");
$nomClassificacao = pegaDado("nom_classificacao","cse.classificacao_tratamento","Where cod_classificacao = '".$codClassificacao."' ");
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = f.nomTratamento.value.length;
        if (campo==0) {
            mensagem += "@Campo Tratamento inválido!()";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        document.frm.ok.disabled = true;
        if (Valida()) {
            document.frm.submit();
        } else {
            document.frm.ok.disabled = false;
        }
    }

    function Cancela()
    {
        document.frm.target = "telaPrincipal";
        document.frm.controle.value = 0;
        document.frm.submit();
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='oculto' onSubmit="return false;">
<input type='hidden' name='controle' value='2'>
<input type='hidden' name='codTratamento' value="<?=$codTratamento;?>">
<input type='hidden' name='codClassificacao' value="<?=$codClassificacao;?>">
<input type='hidden' name='pagina' value="<?=$pagina;?>">
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Tratamento
        </td>
    </tr>
    <tr>
        <td class='label' width='20%' rowspan="2" title="Descrição do tratamento">
            *Tratamento
        </td>
        <td class='field' width='80%'>
            <?=$nomClassificacao;?>
        </td>
    </tr>
    <tr>
    <td class='field'>
        <input type='text' name='nomTratamento' value="<?=$nomTratamento;?>" size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoAltera(); ?>
    </td>
</tr>
</table>
</form>
<?php
    break;

//Inclusão, alteração ou exclusão de dados
case 2:
    $js = "";
    $ok = true;
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_tratamento", $nomTratamento, "cse.tipo_tratamento","And cod_tratamento <> '".$codTratamento."' And cod_classificacao = '".$codClassificacao."' ",1)) {
        $js .= "mensagem += '@O nome ".$nomTratamento." já existe'; \n";
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $cse = new cse();

        $objeto = $nomTratamento;
        $var["nomTratamento"] = $nomTratamento;
        $var["codClassificacao"] = $codClassificacao;
        $var["codTratamento"] = $codTratamento;
        if ($cse->alterarTipoTratamento($var)) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            sistemaLegado::alertaAviso($PHP_SELF."?codClassificacao=".$codClassificacao."&pagina=".$pagina,$objeto,"alterar","aviso","");
        } else {
            sistemaLegado::exibeAviso($objeto,"n_alterar","erro");
            $js .= "f.ok.disabled = false; \n";
        }
    } else {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    }
    break;
}//Fim switch

SistemaLegado::executaFrameOculto($js);
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
