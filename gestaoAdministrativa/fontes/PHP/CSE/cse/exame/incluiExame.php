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

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.92
*/

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
    include_once '../cse.class.php';
    include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria

if (!isset($controle)) {
    $controle = 0;
    $sessao->transf = "";
}

switch ($controle) {
//Formulário em HTML para entrada de dados
case 0:
?>
<script type="text/javascript">
    function validacao(cod)
    {
        var f = document.frm;
        f.controle.value = cod;
        f.submit();
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        campo = f.nomExame.value.length;
            if (campo==0) {
                mensagem += "@Campo Exame inválido!()";
                erro = true;
            }

        campo = f.codClassificacao.value;
            if (campo == 'XXX') {
                mensagem += "@Campo Cassificação inválido!()";
                erro = true;
            }

        campo = f.codTipo;
        if (campo.disabled == true) {
            mensagem += "@Campo Tratamento inválido!()";
            erro = true;
        } else {
            campo = f.codTipo.value;
            if (campo == 'XXX') {
                mensagem += "@Campo Tratamento inválido!()";
                erro = true;
            }
        }

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.controle.value = 1;
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }

    function limpaResultado()
    {
        limpaSelect(document.frm.codTipo,1);
        document.frm.codTxtTipo.value = "";
        document.frm.codTipo.disabled = true;
        document.frm.codTxtTipo.disabled = true;
        document.frm.codTxtClassificacao.focus();

        return false;
    }
</script>
<form name='frm' method='post' action='<?=$PHP_SELF;?>?<?=$sessao->id;?>' target='oculto'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
    <tr>
        <td class="alt_dados" colspan="2">
            Dados do Exame
        </td>
    </tr>
    <tr>
        <td class='label' width='20%'>
            *Classificação
        </td>
        <td class='field' width='80%'
        <input type="text" name="codTxtClassificacao" maxlength="5" size="5" value="" onChange="JavaScript: if (preencheCampo( this, document.frm.codClassificacao )) {validacao(2);} else {limpaResultado();}" onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
            $combo = montaComboGenerico("codClassificacao", "cse.classificacao_tratamento", "cod_classificacao", "nom_classificacao", "",
                     "style='width: 200px;' onchange='preencheCampo( this, document.frm.codTxtClassificacao );validacao(2);' ",
                     "", true, false, false);
            echo $combo;
        ?>
        </td>
    </tr>
    <tr>
        <td class='label'>
            *Tratamento
        </td>
        <td class='field'>
            <input type="text" name="codTxtTipo" value="" size="5" maxlength="5" disabled="" onChange="JavaScript: preencheCampo( this, document.frm.codTipo);" onKeyPress="return(isValido(this, event, '0123456789'));">
            <select name='codTipo' style='width: 200px;' disabled="" onChange="JavaScript: preencheCampo( this, document.frm.codTxtTipo);">
                <option value='XXX'>Selecione</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class='label'>
            *Exame
        </td>
        <td class='field'>
            <input type='text' name='nomExame' value='' size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
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

//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "";
    $ok = true;
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_exame", $nomExame, "cse.tipo_exame","And cod_tratamento = '".$codTipo."' And cod_classificacao = '".$codClassificacao."'",1)) {
        $js .= "mensagem += '@O nome ".$nomExame." já existe'; \n";
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $cse = new cse();

        $objeto = $nomExame;
        if ($cse->incluirExame($_POST) ) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            sistemaLegado::alertaAviso($PHP_SELF,$objeto,"incluir","aviso","");
        } else {
            sistemaLegado::exibeAviso($objeto,"n_incluir","erro");
            $js .= "f.ok.disabled = false; \n";
        }
    } else {
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    }
    break;

//Cria uma lista de opções de Tipo de Tratamento de acordo com a Classificação de Tratamento escolhida
case 2:
    $js = "";
    //Destrói as opções de tipo de tratamento existentes no campo
    $js .= "
        var campo = f.codTipo;
        var campoTxt = f.codTxtTipo;
        campo.disabled = false;
        var aux;
        var tam = campo.options.length;
            while (tam > 0) {
                campo.options[tam] = null;
                tam = tam - 1 ;
            }
        campo.options[0].selected = true; \n";
    if ($codClassificacao != "XXX" or $codClassificacao > 0) {
        $js .= "campo.disabled = false;";
        $js .= "campoTxt.disabled = false;";
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
            $js .= 'campo.options['.$cont.'] = new Option("'.$nom.'",'.$cod.'); ';
            $conn->vaiProximo();
            $cont++;
        }
        $conn->limpaSelecao();
        if ($cont == 1) {
            $js .= "campo.disabled = true;\n";
            $js .= "campoTxt.value = '';\n";
            $js .= "campoTxt.disabled = true;\n";
            $js .= "f.ok.disabled = true;\n";
            $js .= "aux = d.getElementById('lista'); ";
            $js .= 'aux.innerHTML = "&nbsp;"; ';
        } else {
            $js .= "f.ok.disabled = false;\n";
            $js .= "campo.disabled = false;\n";
            $js .= "campoTxt.disabled = false;\n";
            $js .= "campoTxt.focus();\n";
        }
    } else {
        $js .= "campo.disabled = true;";
        $js .= "campoTxt.disabled = true;";
    }
    break;
}//Fim switch

sistemaLegado::executaFrameOculto($js);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
