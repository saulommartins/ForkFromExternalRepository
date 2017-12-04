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
* Arquivo de instância para Tratamento
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.98
*/

  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
  include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"   );
  include_once 'tratamento.lib.php';
  include_once '../cse.class.php';
if ($controle > 0) {
    $ctrl = -1;
}

if (!(isset($ctrl))) {
    $ctrl = 0;
    $sessao->transf = array();
    $sessao->transf['cse'] = array();
    $sessao->transf[exame] = array();
    $sessao->transf[internacao] = array();
}
//echo $ctrl."<br>";

if ($ctrl >= 0) {
    if (isset($ctrlant)) {
        $sessao->transf['cse'][$ctrlant] = $_POST;
    }
    $aAbas = array("Prescrição", "Exames", "Internações");
    ?>
<script type="text/javascript">
    function mudarAba(keyant,key)
    {
        var f = document.frm;
        f.target = 'telaPrincipal';
        f.action = "<?=$PHP_SELF;?>?<?=$sessao->id?>&ctrl="+key+"&ctrlant="+keyant;
<?php
if ($ctrl == 0) {
?>
        if ( validaAbas() ) {
           f.submit();
        }
<?php
} else {
?>
           f.submit();
<?php
}
?>
    }

    function validaAbas()
    {
        var ok = true;
        var campo;
        var mensagem = "";
        var f = eval("document.frm");

        campo = f.codCidadao.value.length;
        campoAux = f.nomCidadao.value.length;
        if (campo == 0 || campoAux == 0) {
            mensagem += '@Campo Cidadão inválido!()';
            ok = false;
        }
        campo = f.descricao.value.length;
        if (campo == 0) {
            mensagem += '@Campo Descrição inválido!()';
            ok = false;
        }
        campo = f.descricao.value.length;
        if (campo > 240) {
            mensagem += '@Campo Descrição inválido!(".f.descricao.value.")';
            ok = false;
        }

        if (f.dataInicio.value.length == 0 || f.dataTermino.value.length == 0) {
            mensagem += '@Campo Período de tratamento inválido!()';
            ok = false;
        }

        campo = f.periodicidade.value.length;
        if (campo == 0) {
            mensagem += '@Campo Frequência inválido!()';
            ok = false;
        }

        campo = f.codClassificacao.value;
        if (campo == "XXX") {
            mensagem += '@Campo Classificação inválido!()';
            ok = false;
        }

        if (f.codTipo.value == "XXX" || f.codTipo.disabled == true) {
            mensagem += '@Campo Tratamento inválido!()';
            ok = false;
        }

        if (!ok) {
            alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');

            return false;
        } else {
            return true;
        }
    }

    function validacao(cod)
    {
        var f = document.frm;
        f.target = 'oculto';
        f.controle.value = cod;
        f.submit();
    }

    function excluiExame(cod)
    {
        document.frm.editarExame.value = cod;
        validacao('6');
    }

    function alteraExame(cod)
    {
        document.frm.editarExame.value = cod;
        validacao('5');
    }

    function limparexame()
    {
        var f = document.frm;
        f.incluirExame.value = "Incluir Exame";
        f.editarExame.value = "-1";
        f.descExame.value = "";
        f.dataExame.value = "";
        f.codTxtInstExame.value = "";
        f.codInstExame.options[0] = new Option("Selecione", "XXX");
        f.codInstExame.options[0].selected = true;
        f.codExame.options[0] = new Option("Selecione", "XXX");
        f.codExame.options[0].selected = true;
    }

    function excluiInt(cod)
    {
        document.frm.editarInt.value = cod;
        validacao('10');
    }

    function alteraInt(cod)
    {
        document.frm.editarInt.value = cod;
        validacao('9');
    }

    function limparint()
    {
        var f = document.frm;
        f.incluirInt.value = "Incluir Internação";
        f.editarInt.value = "-1";
        f.dataBaixa.value = "";
        f.dataAlta.value = "";
        f.motivo.value = "";
        f.codInstituicao.options[0] = new Option("Selecione", "XXX");
        f.codInstituicao.options[0].selected = true;
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f = document.frm;

        if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        var f = document.frm;
        f.ok.disabled = true;
        if (Valida()) {
            f.target = 'oculto';
            f.controle.value = 1;
            f.submit();
        } else {
            f.ok.disabled = false;
        }
    }
</script>
<form action="<?=$PHP_SELF;?>?<?=$sessao->id;?>&ctrlAtual=<?=$ctrl;?>" method='POST' name='frm' >
<input type='hidden' name='controle' value='0'>
<table width='100%' cellspacing=1 cellpadding=4><tr>

<?php
    while (list($key, $val) = each($aAbas)) {
        if ($ctrl == $key) {
            $abas = "show_dados";
            echo    "<td class=".$abas." width='33%'><b>".$val."</b></td>";
        } else {
            $abas = "labelleft";
            echo    "<td class=".$abas." width='33%'>
                <a href='javascript:mudarAba($ctrl,$key);'>".$val."</a></td>";
        }

    }
    echo "</tr></table>";

    //Grava os campos do vetor como variáveis
    foreach ($sessao->transf['cse'] as $vet) {
        foreach ($vet as $chave=>$valor) {
            $$chave = $valor;
        }
    }
}

switch ($ctrl) {
//Formulário em HTML para entrada de dados
case 0:
?>
<table width='100%'>
<tr><td class="alt_dados" colspan="2">&raquo; Dados da Prescrição</td></tr>
<tr>
    <td class='label' width='20%' title="Nome do cidadão">*Cidadão</td>
    <td class='field' width='80%'>
        <input type='text' name='codCidadao' value="<?=$codCidadao;?>" size='5' maxlength='10' onKeyUp="return autoTab(this, 10, event);" onKeyPress="return(isValido(this, event, '0123456789'))" onChange="validacao(2);">
        <input type='text' name='nomCidadao' value="<?=$nomCidadao;?>" size='60' maxlength='200' readonly="" tabindex="1">
<!--abrePopUp("../../includes/procuraCidadao.php","frm","codCidadao","nomCidadao","","<?=$sessao->id?>","800","550")-->

<a href='javascript:abrePopUp("<?=CAM_GA_CSE_POPUPS."Cidadao/procuraCidadao.php";?>","frm","codCidadao","nomCidadao","","<?=$sessao->id?>","800","550");'>
            <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar cidadão" width=22 height=22 border=0>
        </a>
    </td>
</tr>
<tr>
    <td class='label' style="vertical-align: top;" title="Descrição da prescrição">*Descrição</td>
    <td class='field'>
        <textarea name='descricao' cols='40' rows='2'
        onKeyPress="return(maxTextArea(this.form.descricao,240,event));"
        onBlur="return(maxTextArea(this.form.descricao,240,event,true));"
        ><?=$descricao;?></textarea>
    </td>
</tr>
<tr>
    <td class='label' title="Período da prescrição">*Período de tratamento</td>
    <td class='field'>
        <?php geraCampoData("dataInicio", $dataInicio, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (
!verificaData(this) ){alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id','');};\"" );?>&nbsp;a&nbsp;
        <?php geraCampoData("dataTermino", $dataTermino, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (
!verificaData(this) ){alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id','');};\"" );?>
    </td>
</tr>
<tr>
    <td class='label' title="Frequência do tratamento">*Frequência</td>
    <td class='field'>
        <input type='text' name='periodicidade' value="<?=$periodicidade;?>" size='40' maxlength='80' onKeyUp="return autoTab(this, 80, event);" >
    </td>
</tr>
<tr>
    <td class='label' title="Classificação do tratamento">*Classificação</td>
    <td class='field'>
        <input type="text" name="codTxtClassificacao" value="<?=$codClassificacao;?>" size="5" maxlength="5" onchange="JavaScript: preencheCampo(this, document.frm.codClassificacao);validacao(3);" onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
            $combo = montaComboGenerico("codClassificacao", "cse.classificacao_tratamento", "cod_classificacao", "nom_classificacao", $codClassificacao,
                     "style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtClassificacao);validacao(3);' ",
                     "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' title="Descrição do tratamento">*Tratamento</td>
    <td class='field'>
    <?php
        if ($codClassificacao > 0) {
    ?>
        <input type="text" name="codTxtTipo" value="<?=$codTipo;?>" size="5" maxlength="5" onChange="JavaScript: preencheCampo( this, document.frm.codTipo);validacao(7);" onKeyPress="return(isValido(this, event, '0123456789'));">
    <?php
            $combo = montaComboGenerico("codTipo", "cse.tipo_tratamento", "cod_tratamento", "nom_tratamento", $codTipo,
                     "style='width: 200px;' onchange='preencheCampo( this, document.frm.codTxtTipo);validacao(7);' ",
                     "Where cod_classificacao = '".$codClassificacao."' ", true, false, false);
            echo $combo;
        } else {
        ?>
        <input type="text" name="codTxtTipo" value="" size="5" maxlength="5" onChange="JavaScript: preencheCampo( this, document.frm.codTipo);validacao(7);" onKeyPress="return(isValido(this, event, '0123456789'));">
        <select name='codTipo' style='width: 200px;' disabled  onChange="JavaScript: preencheCampo( this, document.frm.codTxtTipo);validacao(7);">
            <option value='XXX' selected>Selecione</option>
        </select>
        <?php } ?>
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
?>
<table width='100%'>
<tr><td class="alt_dados" colspan="2">&raquo; Dados do Exame</td></tr>
<tr>
    <td class='label' width="20%" title="Instituição para exame">*Instituição</td>
    <td class='field' width="80%">
        <input type="text" size="5" maxlength="5" name="codTxtInstExame" onchange="preencheCampo(this, document.frm.codInstExame);" width="20%" onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
            $combo = montaComboGenerico("codInstExame", "cse.instituicao_saude", "cod_instituicao", "nom_instituicao", '',
                     "style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtInstExame);'",
                     "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' title="Data do exame">*Data</td>
    <td class='field'>
        <?php geraCampoData("dataExame", $dataExame, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (
!verificaData(this) ){alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id','');};\"" );?>
    </td>
</tr>
<tr>
    <td class='label' style="vertical-align: top;" title="Descrição do exame">*Exame</td>
    <td class='field'>
        <input type="text" size="5" maxlength="5" name="codTxtExame" onchange="preencheCampo(this, document.frm.codExame);" onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
        if ($codTipo > 0) {
            $combo = montaComboGenerico("codExame", "cse.tipo_exame", "cod_exame", "nom_exame", '',
                     "style='width: 200px;'  onchange='preencheCampo(this, document.frm.codTxtExame);'",
                     "Where cod_classificacao = '".$codClassificacao."'
                     And cod_tratamento = '".$codTipo."' ",
                     true, false, false);
            echo $combo;
        } else {
        ?>
        <select name='codExame' style='width: 200px;' disabled="">
            <option value='XXX'>Selecione</option>
        </select>
        <?php } ?>
    </td>
</tr>
<tr>
    <td class='label' style="vertical-align: top;" title="Detalhamento do exame">*Descrição</td>
    <td class='field'>
        <textarea name='descExame' cols='40' rows='2'
        onKeyPress="return(maxTextArea(this.form.descExame,240,event));"
        onBlur="return(maxTextArea(this.form.descExame,240,event,true));"
        ></textarea>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <table width="100%" cellspacing=0 border=0 cellpadding=0>
            <tr>
                <td>
                    <input type="hidden" name="editarExame" value="-1">
                    <input type="button" name="incluirExame" value="Incluir Exame" onClick="validacao('4');" style="width: 100px;" >&nbsp;
                    <input type="button" name="limparExame" value="Limpar" onClick="limparexame();" style="width: 100px;" >&nbsp;
                </td>
                <td class="fieldright_noborder">
                    <b>* Campos obrigatórios</b>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan='2' height='1' class='field' id='exame'>
    <?php
    if (count($sessao->transf[exame]) > 0) {
        $aux = geraResumoExame($sessao->transf[exame]);
        echo $aux[1];
    }
     ?>
    </td>
</tr>
</table>
</form>
<?php
break;
case 2:
?>
<table width='100%'>
<tr><td class="alt_dados" colspan="2">&raquo; Dados da Internação</td></tr>
<tr>
    <td class='label' title="Instituição para internação" width="20%">*Instituição</td>
    <td class='field' width="80%">
        <input type="text" name="codTxtInstituicao" size="5" maxlength="5" onchange="JavaScript: preencheCampo(this, document.frm.codInstituicao);" onKeyPress="return(isValido(this, event, '0123456789'));">
        <?php
            $combo = montaComboGenerico("codInstituicao", "cse.instituicao_saude", "cod_instituicao", "nom_instituicao", '',
                     "style='width: 200px;'  onchange='JavaScript: preencheCampo(this, document.frm.codTxtInstituicao);'", "", true, false, false);
            echo $combo;
        ?>
    </td>
</tr>
<tr>
    <td class='label' title="Período de internação">*Período</td>
    <td class='field'>
        <?php geraCampoData("dataBaixa", $dataBaixa, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (
!verificaData(this) ){alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id','';};\"" );?>&nbsp;a&nbsp;
        <?php geraCampoData("dataAlta", $dataAlta, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if (
!verificaData(this) ){alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id','');};\"" );?>
    </td>
</tr>
<tr>
    <td class='label' style="vertical-align: top;" title="Motivo da internação">*Motivo da internação</td>
    <td class='field'>
        <textarea name='motivo' cols='40' rows='2'
        onKeyPress="return(maxTextArea(this.form.motivo,240,event));"
        onBlur="return(maxTextArea(this.form.motivo,240,event,true));"
        ></textarea>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <table width="100%" cellspacing=0 border=0 cellpadding=0>
            <tr>
                <td>
                    <input type="hidden" name="editarInt" value="-1">
                    <input type="button" name="incluirInt" value="Incluir Internação" onClick="validacao('8');" style="width: 130px;" >&nbsp;
                    <input type="button" name="limparInt" value="Limpar" onClick="limparint();" style="width: 120px;" >&nbsp;
                </td>
                <td class="fieldright_noborder">
                    <b>* Campos obrigatórios</b>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan='2' height='1' class='field' id='internacao'>
    <?php
    if (count($sessao->transf[internacao]) > 0) {
        $aux = geraResumoInternacao($sessao->transf[internacao]);
        echo $aux[1];
    }
     ?>
    </td>
</tr>
</table>
</form>
<?php
break;
}//Fim switch($ctrl)

if (!isset($controle)) {
    $controle = 0;
}

switch ($controle) {
//Inclusão, alteração ou exclusão de dados
case 1:
    $js = "f.controle.value = 0; \n";
    $ok = true;
/*** Faz a validação dos campos ***/
    $sessao->transf['cse'][$ctrlAtual] = $_POST;

    //Grava os campos do vetor como variáveis
    if (is_array($sessao->transf['cse'][0])) {
        foreach ($sessao->transf['cse'][0] as $chave=>$valor) {
            $$chave = $valor;
        }
    } else {
        $js .= "mensagem += '@Os Campos de Dados do Tratamento são Obrigatórios'; \n";
        $ok = false;
    }

    if (strlen($codCidadao) == 0 || strlen($nomCidadao) == 0 ) {
        $js .= "mensagem += '@Campo Cidadão inválido!()'; \n";
        $ok = false;
    }
    if (strlen($descricao) == 0) {
        $js .= "mensagem += '@Campo Descrição inválido!()'; \n";
        $ok = false;
    }
    if (strlen($descricao) > 240) {
        $js .= "mensagem += '@Campo Descrição inválido!(".$descricao.")'; \n";
        $ok = false;
    }

    if (strlen($dataInicio) == 0 || strlen($dataTermino) == 0) {
        $js .= "mensagem += '@Campo Período de tratamento inválido!()'; \n";
        $ok = false;
    }

    if ( !verificaData($dataInicio) ) {
        $js .= "mensagem += '@Campo Período de tratamento inválido!(".$dataInicio.")'; \n";
        $ok = false;
    }

    if ( !verificaData($dataTermino)  ) {
        $js .= "mensagem += '@Campo Período de tratamento inválido!(".$dataTermino.")'; \n";
        $ok = false;
    }

    if (strlen($periodicidade) == 0) {
        $js .= "mensagem += '@Campo Frequência inválido!()'; \n";
        $ok = false;
    }

    if ($codClassificacao == "XXX") {
        $js .= "mensagem += '@Campo Classificação inválido!()'; \n";
        $ok = false;
    }
    if ($codTipo == "XXX" or (!isset($codTipo))) {
        $js .= "mensagem += '@Campo Tratamento inválido!()'; \n";
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if (!$ok) {
        $js .= "f.controle.value = 0; \n";
        $js .= "f.ok.disabled = false; \n";
        $js .= "erro = true; \n";
    } else {
        $obj = new cse();

        $var = $sessao->transf['cse'][0];
        $var[exame] = $sessao->transf[exame];
        $var[internacao] = $sessao->transf[internacao];

        $objeto = "Tratamento";
        if ($obj->incluirTratamento($var) ) {
            $objeto = "Tratamento ".$obj->codigo;
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
    }
    break;

//Busca o nome do cidadão de acordo com o código fornecido
case 2:
    $js = "f.controle.value = 0; \n";
    if ($codCidadao > 0) {
        if (!$nomCidadao = pegaDado("nom_cidadao","cse.cidadao","Where cod_cidadao = '".$codCidadao."' ")) {
            $js = "alertaAviso('Cidadão inválido!(".$codCidadao.")','form','erro','".$sessao->id."','')\n";
            $nomCidadao = "";
        }
    } else {
        $nomCidadao = "";
    }
    $js .= 'f.nomCidadao.value = "'.$nomCidadao.'" ';
    break;

//Cria uma lista de opções de Tipo de Tratamento de acordo com a Classificação de Tratamento escolhida
case 3:
    $js = "f.controle.value = 0; \n";

    //Reseta o array dos exames se a Classificação de Tratamento for alterada
    $sessao->transf[exame] = array();

    //Destrói as opções de tipo de tratamento existentes no campo
    $js .= "
        campo = f.codTipo;
        campoTxt = f.codTxtTipo;
        campo.disabled = false;
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
    } else {
        $js .= "campo.disabled = true;\n";
        $js .= "campoTxt.disabled = true;\n";
    }
    break;

//Inclui um novo exame à lista de Exames
case 4:
    $js = "f.controle.value = 0; \n";
    $ok = true;

    //Valida os campos
    if ($codInstExame == "XXX" or (!isset($codInstExame))) {
        $js .= "mensagem += '@Campo Instituição inválido!()'; \n";
        $ok = false;
    }

    if (strlen($dataExame)==0) {
        $ok = false;
        $js .= "mensagem += '@Campo Data inválido!()'; \n";
    }

    if ( !verificaData($dataExame) ) {
        $ok = false;
        $js .= "mensagem += '@Campo Data inválido!(".$dataExame.")'; \n";
    }

    if ($codExame == "XXX" or (!isset($codExame))) {
        $js .= "mensagem += '@Campo Tipo inválido!()'; \n";
        $ok = false;
    }

    if (strlen($descExame)==0) {
        $ok = false;
        $js .= "mensagem += '@Campo Descrição inválido!()'; \n";
    }
    if (strlen($descExame) > 240) {
        $js .= "mensagem += '@O Campo Descrição inválido!(Não pode possuir mais que 240 caracteres)'; \n";
        $ok = false;
    }
    /*
    //Verifica se o exame selecionado já faz parte da lista
    if ($editarExame < 0) {
        if (is_array($sessao->transf[exame])) {
            foreach ($sessao->transf[exame] as $vetAux) {
                if ($vetAux[codExame] == $codExame) {
                    $js .= "mensagem += '@Este exame já foi incluído'; \n";
                    $ok = false;
                }
            }
        }
    }
    */
    if (!$ok) {
        $js .= "erro = true; \n ";
    } else {
        if (!is_array($sessao->transf[exame])) {
            $sessao->transf[exame] = array();
        }

        $vet[descExame] = $descExame;
        $vet[dataExame] = $dataExame;
        $vet[codInstExame] = $codInstExame;
        $vet[codExame] = $codExame;

        if ($editarExame >= 0) { //Altera o valor de um item editado
            $sessao->transf[exame][$editarExame] = $vet;
        } else { //Adiciona o novo item à matriz de itens
            $sessao->transf[exame][] = $vet;
        }

        $aux = geraResumoExame($sessao->transf[exame]);

        $js .= $aux[0];

        //Reseta os valores dos campos do item
        $js .= "f.descExame.value = ''; \n";
        //Obs: Manter os campos instituição e data preenchidos para facilitar a inclusão de diversos exames,
        //     somente no caso de inclusão, não em alteração
        if ($editarExame >= 0) {
            $js .= "f.dataExame.value = '';\n";
            $js .= "f.codTxtInstExame.value = '';\n";
            $js .= 'f.codInstExame.options[0] = new Option("Selecione","XXX"); ';
            $js .= "f.codInstExame.options[0].selected = true; \n";
        }
        $js .= 'f.codExame.options[0] = new Option("Selecione","XXX"); ';
        $js .= "f.codExame.options[0].selected = true; \n";
        $js .= "f.codTxtExame.value = ''; \n";
        $js .= "f.editarExame.value = '-1'; \n ";
        $js .= "f.incluirExame.value = 'Incluir Exame'; \n ";
    }
    break;

//Altera o formulário de Exame para permitir a edição de um item selecionado
case 5:
    $js = "f.controle.value = 0; \n";
    $vet = $sessao->transf[exame][$editarExame];

    $nomExame = pegaDado("nom_exame","cse.tipo_exame","Where cod_exame = '".$vet[codExame]."' ");

    $js .= "var campo = f.codTxtExame;";
    $js .= 'campo.value = '.$vet[codExame].'; ';

    $js .= "var campo = f.codExame;";
    $js .= 'campo.options[0] = new Option("'.$nomExame.'",'.$vet[codExame].'); ';
    $js .= "campo.options[0].selected = true; \n";

    $inst = pegaDado("nom_instituicao","cse.instituicao_saude","Where cod_instituicao = '".$vet[codInstExame]."' ");

    $js .= "var campo = f.codTxtInstExame;";
    $js .= 'campo.value = '.$vet[codInstExame].'; ';

    $js .= "var campo = f.codInstExame;";
    $js .= 'campo.options[0] = new Option("'.$inst.'",'.$vet[codInstExame].'); ';
    $js .= "campo.options[0].selected = true; \n";

    $js .= "f.dataExame.value = '".$vet[dataExame]."'; \n ";
    $js .= "f.incluirExame.value = 'Alterar Exame'; \n ";

    $desc = preg_replace( "/\r?\n/","#@#",$vet[descExame]);
    $js .= "desc =  '".$desc."'; \n";
    $js .= 'aux = desc.replace(/#@#/gi, "\r\n"); ';
    $js .= "f.descExame.value = aux; \n";

    break;

case 6:
    $js = "f.controle.value = 0; \n";
    $vetor = $sessao->transf[exame];
    $sessao->transf[exame] = array();

    //Exclui o item escolhido da lista de itens
    foreach ($vetor as $chave=>$vet) {
        if ($chave!=$editarExame) {
            $sessao->transf[exame][] = $vet;
        }
    }

    //Chama a função que recria a tabela com o resumo dos itens
    $aux = geraResumoExame($sessao->transf[exame]);

    $js .= $aux[0];
    break;

case 7:
    $js = "f.controle.value = 0; \n";
    //Reseta o array dos exames se a tipo de Tratamento for alterada
    $sessao->transf[exame] = array();
    break;

//Inclui uma nova internação à lista de Internações
case 8:
    $js = "f.controle.value = 0; \n";
    $ok = true;

    //Valida os campos
    if ($codInstituicao == "XXX" or (!isset($codInstituicao))) {
        $js .= "mensagem += '@Campo Instituição inválido!()'; \n";
        $ok = false;
    }

    if (strlen($dataAlta)==0 and strlen($dataBaixa)==0) {
        $ok = false;
        $js .= "mensagem += '@Campo Período inválido!()'; \n";
    }

    if ( !verificaData($dataBaixa) ) {
        $ok = false;
        $js .= "mensagem += '@Campo Período inválido!(".$dataBaixa.")'; \n";
    }

    if ( !verificaData($dataAlta) ) {
        $ok = false;
        $js .= "mensagem += '@Campo Período inválido!(".$dataAlta.")'; \n";
    }

    if (strlen($motivo)==0) {
        $ok = false;
        $js .= "mensagem += '@Campo Motivo da Internação inválido!()'; \n";
    }
    if (strlen($motivo) > 240) {
        $js .= "mensagem += '@Campo Motivo da Internação inválido!(Não pode possuir mais de 240 caracteres)'; \n";
        $ok = false;
    }
/*
    //Verifica se a instituição selecionada já faz parte da lista
    if ($editarInt < 0) {
        if (is_array($sessao->transf[internacao])) {
            foreach ($sessao->transf[internacao] as $vetAux) {
                if ($vetAux[codInstituicao] == $codInstituicao) {
                    $js .= "mensagem += '@Esta Instituição já foi incluída'; \n";
                    $ok = false;
                }
            }
        }
    }
*/
    if (!$ok) {
        $js .= "erro = true; \n ";
    } else {
        if (!is_array($sessao->transf[internacao])) {
            $sessao->transf[internacao] = array();
        }

        $vet[dataBaixa] = $dataBaixa;
        $vet[dataAlta] = $dataAlta;
        $vet[codInstituicao] = $codInstituicao;
        $vet[motivo] = $motivo;

        if ($editarInt >= 0) { //Altera o valor de um item editado
            $sessao->transf[internacao][$editarInt] = $vet;
        } else { //Adiciona o novo item à matriz de itens
            $sessao->transf[internacao][] = $vet;
        }

        $aux = geraResumoInternacao($sessao->transf[internacao]);

        $js .= $aux[0];

        //Reseta os valores dos campos do item
        $js .= "f.dataBaixa.value = ''; \n";
        $js .= "f.dataAlta.value = ''; \n";
        $js .= "f.motivo.value = ''; \n";
        $js .= "f.codTxtInstituicao.value = ''; \n";
        $js .= 'f.codInstituicao.options[0] = new Option("Selecione","XXX"); ';
        $js .= "f.codInstituicao.options[0].selected = true; \n";
        $js .= "f.editarInt.value = '-1'; \n ";
        $js .= "f.incluirInt.value = 'Incluir Internação'; \n ";
    }
    break;

//Altera o formulário de Internação para permitir a edição de um item selecionado
case 9:
    $js = "f.controle.value = 0; \n";
    $vet = $sessao->transf[internacao][$editarInt];

    $inst = pegaDado("nom_instituicao","cse.instituicao_saude","Where cod_instituicao = '".$vet[codInstituicao]."' ");

    $js .= "var campo = f.codTxtInstituicao;";
    $js .= 'campo.value = '.$vet[codInstituicao].'; ';

    $js .= "var campo = f.codInstituicao;";
    $js .= 'campo.options[0] = new Option("'.$inst.'",'.$vet[codInstituicao].'); ';
    $js .= "campo.options[0].selected = true; \n";

    $js .= "f.dataBaixa.value = '".$vet[dataBaixa]."'; \n ";
    $js .= "f.dataAlta.value = '".$vet[dataAlta]."'; \n ";
    $js .= "f.incluirInt.value = 'Alterar Internação'; \n ";

    $desc = preg_replace( "/\r?\n/","#@#",$vet[motivo]);
    $js .= "desc =  '".$desc."'; \n";
    $js .= 'aux = desc.replace(/#@#/gi, "\r\n"); ';
    $js .= "f.motivo.value = aux; \n";

    break;

//Exclui um item da lista de internações
case 10:
    $js = "f.controle.value = 0; \n";
    $vetor = $sessao->transf[internacao];
    $sessao->transf[internacao] = array();

    //Exclui o item escolhido da lista de itens
    foreach ($vetor as $chave=>$vet) {
        if ($chave!=$editarInt) {
            $sessao->transf[internacao][] = $vet;
        }
    }

    //Chama a função que recria a tabela com o resumo dos itens
    $aux = geraResumoInternacao($sessao->transf[internacao]);

    $js .= $aux[0];
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

    if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>','');
}
</script>
</head>

<body onLoad="javascript:executa();">

</body>
</html>
<?php
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
