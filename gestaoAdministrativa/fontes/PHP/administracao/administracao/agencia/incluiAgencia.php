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
* Manutenção de agência
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.97
*/

 include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
 include(CAM_FW_LEGADO."funcoesLegado.lib.php");
 include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
 include '../agencia.class.php';

/**************************************************************************
 Gera uma combo marcando um valor pré-selecionado, se houver
/**************************************************************************/
function comboBanco($default="",$nome="nomBanco")
{
    $combo = "";
    $combo .= "<select name='".$nome."' style='width: 200px;' tabindex='1' onchange='retornaCodBanco(this.value);'>\n";
        if($default=="")
            $selected = "selected";
    $combo .= "<option value='xxx' ".$selected.">Selecione uma opção</option>\n";
        $sql = "Select cod_banco, nom_banco
                From administracao.banco
                Order by nom_banco";
        echo "<!--".$sql."-->";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            while (!$conn->eof()) {
                $cod = $conn->pegaCampo("cod_banco");
                $nom = trim($conn->pegaCampo("nom_banco"));
                $selected = "";
                    //Verifica se o valor passado para a função deve estar marcado
                    if($cod==$default)
                        $selected = "selected";
                $conn->vaiProximo();
                $combo .= "<option value='".$cod."' ".$selected.">".$nom."</option>\n";
            }
        $conn->limpaSelecao();
    $combo .= "</select>";
    print $combo;
}//Fim da function comboBanco

if (!isset($controle)) {
    $controle = 0;
    $codAgencia = "";
    $nomAgencia = "";
}

switch ($controle) {
case 0:
?>
<script type="text/javascript">
    function retornaCodBanco(cod)
    {
        var f;

        f = document.frm;

        if (cod=="xxx") {
            f.codBanco.value = "";
        } else {
            f.codBanco.value = cod;
        }
    }

    function validaCodBanco(iCod)
    {
        var cod = parseInt(iCod);
        var val;
        var erro = true;
        var msg = "O Código do Banco "+iCod+" é Inválido";
        var f = document.frm;
        var campo = f.nomBanco;
        var tam = campo.options.length - 1;
        if (f.codBanco.value.length==0)
            return false;
        //Percorre todos os valores para encontrar qual item da combo tem o valor digitado
        while (tam >= 0) {
            val = parseInt(f.nomBanco.options[tam].value);
            if (cod==val) {
                f.nomBanco[tam].selected = true;
                erro = false;
            }
            tam = tam - 1 ;
        }
        //Se não encontrou o valor o código digitado é inválido
        if (erro) {
            f.codBanco.value = "";
            f.codBanco.focus();
            f.nomBanco[0].selected = true;
            alertaAviso(msg,'unica','erro','<?=Sessao::getId()?>');
        }
    }
    function validaCodAgencia(iCod)
    {
        //alert ("valor-"+document.frm.codAgencia.value);
        //alert ("Valor " + iCod);
        var msg = "O Código da Agência "+iCod+" é Inválido";
        var f = document.frm;
        if (iCod.length!='6' || iCod=='0000-0') {
            f.codAgencia.value = "";
            f.codAgencia.focus();
            alertaAviso(msg,'unica','erro','<?=Sessao::getId()?>');
        }
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var f;

        f = document.frm;

        campo = f.codBanco.value.length;
            if (campo==0) {
                mensagem += "@Código do Banco";
                erro = true;
            }

        campo = f.codAgencia.value.length;
            if (campo==0) {
                mensagem += "@Código Agência";
                erro = true;
            }

        campo = f.nomAgencia.value.length;
            if (campo==0) {
                mensagem += "@Nome";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'formulario','','<?=Sessao::getId()?>');
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
</script>

<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=Sessao::getId();?>'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
<tr><td class='alt_dados' colspan='2'>Dados para Agência</td></tr>
<tr>
    <td class='label' width='30%'>*Código do Banco:</td>
    <td class='field' width='70%'>
        <input type='text' name='codBanco' value="<?=$codBanco;?>" size='5' maxlength='4' onBlur="validaCodBanco(this.value);" onKeyUp="return autoTab(this, 4, event);" onKeyPress="return(isNumber(this, event))">
        <?php comboBanco($codBanco); ?>
    </td>
</tr>
<tr>
    <td class='label' width='30%'>*Código da Agência:</td>
    <td class='field' width='70%'>
        <input type='text' name='codAgencia' value="<?=$codAgencia;?>" size='5' maxlength='6' onBlur="validaCodAgencia(this.value);" onKeyUp="mascaraDinamico ('9999-9', this, event);">
    </td>
</tr>
<tr>
    <td class='label' width='30%'>*Nome da Agência:</td>
    <td class='field' width='70%'>
        <input type='text' name='nomAgencia' value="<?=$nomAgencia;?>" size='40' maxlength='80'>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>

<?php
    break;
case 1:
    $ok = true;
    $objeto = "Agência ".$codAgencia." - ".$nomAgencia;
    //$pag = $PHP_SELF."?".Sessao::getId()."&controle=0&codBanco=".$codBanco."&codAgencia=".$codAgencia."&nomAgencia=".$nomAgencia;
    /*** Primeiro valida os dados, depois faz a inclusão ***/
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("cod_agencia", $codAgencia, "administracao.agencia","And cod_banco = '".$codBanco."'")) {
        alertaAviso($pag,"O código ".$codAgencia." já existe!","unica","erro");
        $ok = false;
    }
    if (!comparaValor("nom_agencia", $nomAgencia, "administracao.agencia","And cod_banco = '".$codBanco."'",1)) {
        alertaAviso($pag,"O nome ".$nomAgencia." já existe!","unica","erro");
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $agencia = new agencia($codBanco,$codAgencia,$nomAgencia);
        if ($agencia->incluirAgencia()) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            alertaAviso($PHP_SELF,$objeto,"incluir","aviso");
        } else {
            alertaAviso($objeto,"n_incluir","erro");
            $js = "f.ok.disabled = false;";
            executaFrameOculto($js);
        }
    }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
