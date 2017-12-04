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
     * Arquivo de implementação de manutenção de processo
     * Data de Criação: 25/07/2005

     * @author Analista: Cassiano
     * @author Desenvolvedor: Cassiano

     * Casos de uso: uc-01.06.98

    $Id: interfaceProcessos.class.php 65625 2016-06-02 18:34:54Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
?>
<script type="text/javascript">
     function zebra(id, classe)
     {
            var tabela = document.getElementById(id);
            var linhas = tabela.getElementsByTagName("tr");
            for (var i = 0; i < linhas.length; i++) {
                ((i%2) == 0) ? linhas[i].className = classe : void(0);
            }
    }
</script>
   
<?php
 class interfaceProcessos
 {
 /***************************************************************************
 Mostra as opções para inclusão de Processo:
     Matrícula, Inscrição, outros
/**************************************************************************/

/*
    public function interfaceProcessos()
    {
            global $jsOnload;
            $jsOnload = 'setarFoco();';
    }
*/

    public function listaTipos()
    {
?>
        <script type="text/javascript">
            function validaCodigo(processo)
            {
                var x = 350;
                var y = 200;
                var sArq = 'validaCodigo.php?<?=Sessao::getId();?>&especieProcesso='+processo;
                var wVolta=false;
                erro = window.open(sArq,'help','width=350px,height=150px,resizable=1,scrollbars=0,left='+x+',top='+y);
            }
        </script>
        <br>
        <b>Escolha que tipo de processo deseja incluir:</b>
        <br><br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="javascript:validaCodigo('matricula');">
                        Incluir Processo de Imóvel
                    </a>
                </td>
            </tr>
        </table>
        <br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="javascript:validaCodigo('inscricao');">
                        Incluir Processo de Alvará
                    </a>
                </td>
            </tr>
        </table>
        <br>
        <table width="75%">
            <tr>
                <td class='fieldcenter' style='font-weight: bold;'>
                    <a href="javascript:validaCodigo('outros');" >
                        Incluir outro processo
                    </a>
                </td>
            </tr>
        </table>

<?php
    }//Fim function listaTipos

/***************************************************************************
Formulário para validação de códigos: Matrícula, Inscrição, outros
/**************************************************************************/
    public function formValidaCodigo($action="",$especieProcesso="")
    {
        if($action=="")
            $action = $PHP_SELF.'?'.Sessao::getId();
        switch ($especieProcesso) {
            case 'matricula':
                $sLabel = "Número Matricula";
                $sField = "numMatricula";
                $iMaxLength = "8";
                break;
            case 'inscricao':
                $sLabel = "Número Inscrição";
                $sField = "numInscricao";
                $iMaxLength = "6";
                break;
            case 'outros':
                $sLabel = "Número CGM";
                $sField = "numCgm";
                $iMaxLength = "20";
                break;
        }
?>
        <center>
        <form name="frm2" action="<?=$action;?>" method="POST">
        <input type="hidden" name="controle" value="1">
        <input type="hidden" name="especieProcesso" value="<?=$especieProcesso;?>">
        <table width='95%' cellspacing=0 border=0 cellpadding=0>
        <tr>
            <td colspan=2 height=10>
            </td>
        </tr>
        <tr>
            <td class="label">
        <?=$sLabel;?>
            </td>
            <td class="field">
                <input type="text" name="num" value="" size=10 maxlength="<?=$iMaxLength;?>">
            </td>
        </tr>
        <tr>
            <td colspan=2 class='field'>
                <input type="submit" value="OK" style='width: 60px;'>&nbsp;
                <input type="reset" value="Limpar" style='width: 60px;'>
            </td>
        </tr>

        </table>
        </form>
            <script type="text/javascript">
                placeFocus();
            </script>
<?php
    }//Fim function formValidaCodigo

/**************************************************************************
Gera o Combo com os tipos de vínculo de um processo
/**************************************************************************/
    public function comboVinculo($nome="vinculo",$default="",$espec="")
    {
        $vetVinculo['imobiliaria'] = "Cadastro Imobiliário";
        $vetVinculo['inscricao']   = "Cadastro Econômico";
        $vetVinculo['funcionario'] = "Cadastro de RH";
        $vetVinculo['cgm']         = "CGM";
        $vetVinculo['licitacao']   = "Cadastro da Licitação";
            $combo = "";
            $combo .= "<select name='".$nome."' style='width: 200px;'".$espec." id='comboVinculo'>\n";
            if($default=="")
                    $selected = "selected";
            $combo .= "<option value='xxx' ".$selected.">Selecione um vínculo</option>\n";
            foreach ($vetVinculo as $chave=>$valor) {
                $selected = "";
                    if($chave==$default)
                        $selected = "selected";
                $combo .= "<option value='".$chave."'".$selected.">".$valor."</option>\n";
            }
            $combo .= "</select>";

        return $combo;
    }//Fim function comboVinculo

/**************************************************************************
Gera o Combo com os assuntos relativos a uma classificação de processo
/**************************************************************************/
    public function comboClassificacao($nome="codClassificacao",$default="",$espec="")
    {
        $sql = "Select cod_classificacao, nom_classificacao
                From sw_classificacao
                Order by nom_classificacao";
        //echo "<!--".$sql."-->";
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            $combo = "";
            $combo .= "<select name='".$nome."' style='width: 200px;'".$espec.">\n";
            if($default=="")
                    $selected = "selected";
            $combo .= "<option value='xxx' ".$selected.">Selecione uma classificação</option>\n";
            while (!$dataBase->eof()) {
                $codClassificacao = trim($dataBase->pegaCampo("cod_classificacao"));
                $nomClassificacao = trim($dataBase->pegaCampo("nom_classificacao"));
                $selected = "";
                if($codClassificacao==$default)
                        $selected = "selected";
                $dataBase->vaiProximo();
                $combo .= "<option value='".$codClassificacao."'".$selected.">".$nomClassificacao."</option>\n";
            }
            $combo .= "</select>";
            $dataBase->limpaSelecao();
            $dataBase->fechaBD();

            return $combo;

    }//Fim function comboClassificacao

/**************************************************************************
Gera o Combo com os tipos de grupo do bem
/**************************************************************************/
    public function comboAssunto($nome="codAssunto",$default="",$codClassificacao=0,$espec="")
    {
        $combo = "";
        $combo .= "<select name='".$nome."' style='width: 200px;'".$espec.">\n";
        if($default=="")
                $selected = "selected";
        $combo .= "<option value='xxx' ".$selected.">Selecione um assunto</option>\n";
        if ($codClassificacao != 'xxx') {
        if ($codClassificacao>0) {
            $sql = "Select cod_assunto, nom_assunto
                From sw_assunto
                Where cod_classificacao = '".$codClassificacao."'
                Order by nom_assunto";
            //echo "<!--".$sql."-->";
            $dataBase = new dataBaseLegado;
            $dataBase->abreBD();
            $dataBase->abreSelecao($sql);
            $dataBase->vaiPrimeiro();
                while (!$dataBase->eof()) {
                    $codAssunto = trim($dataBase->pegaCampo("cod_assunto"));
                    $nomAssunto = trim($dataBase->pegaCampo("nom_assunto"));
                    $selected = "";
                        if($codAssunto==$default)
                            $selected = "selected";
                    $dataBase->vaiProximo();
                    $combo .= "<option value='".$codAssunto."'".$selected.">".$nomAssunto."</option>\n";
                }

            $dataBase->limpaSelecao();
            $dataBase->fechaBD();
        }
        }
        $combo .= "</select>";

        return $combo;
    }//Fim function comboAssunto

/**************************************************************************
Gera os checkboxes com os documentos exigidos para um tipo de processo
/**************************************************************************/
    public function checkDocumentos($codClassificacao,$codAssunto,$marcados="")
    {
        $sql = "Select A.cod_classificacao, A.cod_assunto, A.cod_documento, D.nom_documento
                From sw_documento as D, sw_documento_assunto as A
                Where A.cod_documento = D.cod_documento
                And A.cod_classificacao = ".$codClassificacao."
                And A.cod_assunto = ".$codAssunto."
                Order by nom_documento";


        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            $checkBox = "<table width='100%'>";
            while (!$dataBase->eof()) {
                $codDoc = trim($dataBase->pegaCampo("cod_documento"));
                $nomDoc = trim($dataBase->pegaCampo("nom_documento"));

                $checked = "checked";
                if (is_array($marcados)) {
                        foreach ($marcados as $valor) {
                            if($valor==$codDoc)
                                $checked = "checked";
                        }
                    }

                $dataBase->vaiProximo();
                $checkBox .= "
                <tr>
                    <td class=field>
                        <input type='checkbox' ".$checked." name='codDocumentos[]' id='codDocumentos".$codDoc."' value='".$codDoc."'' onclick='javascript:desabilitar(".$codDoc.");'>".$nomDoc."
                    </td>
                    <td class=field>
                        <input type='button' name='btnCopia".$codDoc."' value='Cópia Digital' onclick=\"copiaDigital($codDoc);\">
                    </td>
                </tr>";
            }
            $checkBox .= "</table>";
            $dataBase->limpaSelecao();
            $dataBase->fechaBD();

            return $checkBox;
    }//Fim function checkDocumentos

/**************************************************************************
Gera o Combo com os códigos do Setor (usando a chave composta)
/**************************************************************************/
    public function comboSetor($nome="chaveSetor",$default="",$codClassificacao=0,$codAssunto=0,$espec="")
{        $sql = "Select cod_setor, cod_departamento, cod_unidade, cod_orgao, ano_exercicio, nom_setor
                From administracao.setor Order by nom_setor ";
        if ($codClassificacao > 0 and $codAssunto > 0) {
            $sql = "Select S.nom_setor, A.num_passagens,
                    A.cod_setor, A.cod_departamento, A.cod_unidade,
                    A.cod_orgao, A.ano_exercicio
                    From administracao.setor as S, sw_andamento_padrao as A
                    Where A.cod_setor > 0
                    And A.cod_setor = S.cod_setor
                    And A.cod_departamento = S.cod_departamento
                    And A.cod_unidade = S.cod_unidade
                    And A.cod_orgao = S.cod_orgao
                    And A.cod_classificacao = '".$codClassificacao."'
                    and A.cod_assunto = '".$codAssunto."'
                    Order by A.ordem ";
        }
      
        $dataBase = new dataBaseLegado;
        $dataBase->abreBD();
        $dataBase->abreSelecao($sql);
        $dataBase->vaiPrimeiro();
            $combo = "";
            $combo .= "<select name='".$nome."' style='width: 180px;'".$espec.">\n";
            if($default=="")
                $selected = "selected";
                $combo .= "<option value='xxx' ".$selected.">Selecione um setor</option>\n";
                while (!$dataBase->eof()) {
                    $codOrgao = $dataBase->pegaCampo("cod_orgao");
                    $codDpto = $dataBase->pegaCampo("cod_departamento");
                    $codUnidade = $dataBase->pegaCampo("cod_unidade");
                    $codSetor = $dataBase->pegaCampo("cod_setor");
                    $anoEx = $dataBase->pegaCampo("ano_exercicio");
                    $chaveSetor = $codSetor.",".$codDpto.",".$codUnidade.",".$codOrgao.",".$anoEx;
                    $nomSetor = trim($dataBase->pegaCampo("nom_setor"));
                    $selected = "";
                    if($chaveSetor==$default)
                        $selected = "selected";
                    $dataBase->vaiProximo();
                    $combo .= "<option value='".$chaveSetor."'".$selected.">".$nomSetor."</option>\n";
                }
            $combo .= "</select>";
            $dataBase->limpaSelecao();
            $dataBase->fechaBD();

            return $combo;
    }//Fim function comboSetor

/***************************************************************************
Monta o formulário para incluir processos
/**************************************************************************/

//function formIncluiProcesso($processo="",$codigo="",$numCgm="",$tipoProcesso="") {
function formIncluiProcesso($dadosForm="",$action="",$controle=0)
{
    $acao = $dadosForm['acao'];
    if ($acao == 57) {
        Sessao::write('acao', $acao);
    }

    $arInteressados = Sessao::getRequestProtocolo();

    if (!$arInteressados['interessados']) {
        $arInteressados['interessados'] = array();
        Sessao::write('arRequestProtocolo', $arInteressados);
    }

    if (!$arInteressados['permitidos']) {
        $arInteressados['permitidos'] = array();
        Sessao::write('arRequestProtocolo', $arInteressados);
    }

    if ( Sessao::getVoltarProtocolo() ) {
        $arRequestProcesso = Sessao::getRequestProtocolo();

        foreach ($arRequestProcesso as $stCampo => $stValor) {
            $_REQUEST[$stCampo] = $stValor;
        }
        Sessao::setVoltarProtocolo(false);
    }

    foreach ($_REQUEST AS $indice => $valorIndice) {
        global $$indice;
        $$indice = $valorIndice;
    }

    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    // operacoes no frame oculto
    switch ($ctrl_frm) {

        // busca o nome do fornecedor a partir do codigo informado
        case 1:

            if ($numCgm) {
                // busca nome do fornecedor atraves do cod_fornecedor informado
                $sql  = "   SELECT                     ";
                $sql .= "         c.numcgm, c.nom_cgm  ";
                $sql .= "     FROM                     ";
                $sql .= "         sw_cgm as c         ";
                $sql .= "     WHERE c.numcgm =".$numCgm;

                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $nomCgm = stripslashes(trim($conn->pegaCampo("nom_cgm")));
                $conn->limpaSelecao();
                $conn->fechaBD();
            } else {
                $nomCgm = "";
            }

            if (strlen($nomCgm) > 0) {
                $js .= 'f.target = \'\';';
                $js .= 'f.ctrl.value = 0;';
                $js .= 'f.ctrl_frm.value = 0;';
                $js .= 'd.getElementById("nomCGM").innerHTML = "'.$nomCgm.'";';
                // Campo Hidden.
                $js .= 'd.getElementById("nomCgm").value = "'.$nomCgm.'";';
                $js .= 'd.getElementById("inserirCgm").disabled = false;';
                $js .= 'd.getElementById("inserirCgm").focus();';
            } else {
                $js .= "erro = true;\n";
                $js .= 'mensagem += "@Interessado inválido ou inexistente! ('.$numCgm.')";';
                $js .= 'f.numCgm.value = "";';
                $js .= 'd.getElementById(\'nomCGM\').innerHTML = "&nbsp;";';
                // Campo Hidden.
                $js .= 'd.getElementById("nomCgm").value = "";';
                $js .= 'window.parent.frames["telaPrincipal"].boSugerido = false;';
                $js .= 'f.numCgm.focus();';
            }

            sistemaLegado::executaFrameOculto($js);

            exit();

        break;
        case 2:
            # Procura pelo CGM informado que poderá ter acesso ao processo quando o mesmo for confidencial
            if (!empty($numCgmAcesso)) {

                $sql  = "   SELECT                     ";
                $sql .= "         c.numcgm, c.nom_cgm  ";
                $sql .= "     FROM                     ";
                $sql .= "         sw_cgm as c         ";
                $sql .= "     WHERE c.numcgm =".$numCgmAcesso;
                $sql .= "       AND EXISTS ( select 1 from administracao.usuario as tabela_vinculo where tabela_vinculo.numcgm = c.numcgm )";

                $conn = new dataBaseLegado;
                $conn->abreBD();
                $conn->abreSelecao($sql);
                $conn->vaiPrimeiro();

                $stCGMPermitido = stripslashes(trim($conn->pegaCampo("nom_cgm")));
                $conn->limpaSelecao();
                $conn->fechaBD();

                if (!empty($stCGMPermitido)) {

                    $stCGMPermitido = stripslashes(trim($stCGMPermitido));
                    $js .= 'f.target = \'\';';
                    $js .= 'f.ctrl.value = 0;';
                    $js .= 'f.ctrl_frm.value = 0;';
                    $js .= 'd.getElementById("nomCGMAcesso").innerHTML = "'.$stCGMPermitido.'";';
                    $js .= 'jQuery("#nomCGMAcesso").html("'.$stCGMPermitido.'");';

                    // Campo Hidden.
                    $js .= 'd.getElementById("nomCgmAcesso").value = "'.$stCGMPermitido.'";';
                    $js .= 'd.getElementById("inserirCgmAcesso").disabled = false;';
                    $js .= 'd.getElementById("inserirCgmAcesso").focus();';
                   
                } else {
                    $js .= "erro = true;\n";
                    $js .= 'mensagem += "@CGM informado inválido ou inexistente! ('.$numCgmAcesso.')";';
                    $js .= 'f.numCgmAcesso.value = "";';
                    $js .= 'd.getElementById(\'nomCGMAcesso\').innerHTML = "&nbsp;";';
                    // Campo Hidden.
                    $js .= 'd.getElementById("nomCgmAcesso").value = "";';
                    $js .= 'window.parent.frames["telaPrincipal"].boSugerido = false;';
                    $js .= 'f.numCgmAcesso.focus();';
                }
            }

            sistemaLegado::executaFrameOculto($js);

            exit();
        break;
    }
    // encerra operacoes no frame oculto

    switch ($ctrl) {
        case 0:
            $proc = new processosLegado;
            //Grava os campos do vetor como variáveis
            if (is_array($dadosForm)) {
                foreach ($dadosForm as $chave=>$valor) {
                    $$chave = $valor;
                }
            } else {
                $arRequestProcesso = Sessao::getRequestProtocolo();
                foreach ($arRequestProcesso as $chave=>$valor) {
                    $$chave = $valor;
                }
            }

            $disable = false;

            //Verifica o tipo de numeração de processo - manual ou automática
            $tipoNumeracao = pegaConfiguracao("tipo_numeracao_processo",5);
    
            if ($tipoNumeracao==2) { //Manual
                if (strlen($anoExercicio)==0) {
                    $anoExercicio       = pegaConfiguracao("ano_exercicio");
                    $anoExercicioManual = pegaConfiguracao("ano_exercicio");
                    $mascara_processo   = pegaConfiguracao("mascara_processo");
                }

            }
            if ($numCgm>0) {
                if (!$nomCgm = stripslashes(pegaDado("nom_cgm","sw_cgm","Where numcgm = '".$numCgm."' "))) {
                    $nomCgm = "CGM inexistente!";
                }
            }

            echo"
            <script type='text/javascript'>
                boSugerido = false;
                if ( document.getElementById('link_volta') ) {
                    document.getElementById('link_volta').style.visibility = 'hidden';
                }
                function validaCodigo(processo)
                {
                    var x = 350;
                    var y = 200;
                    var sArq = 'validaCodigo.php?".Sessao::getId()."&especieProcesso='+processo;
                    var wVolta=false;
                    tela = window.open(sArq,'tela','width=300px,height=120px,resizable=1,scrollbars=1,left='+x+',top='+y);
                }
                function incluiProcesso()
                {
                    var x = 350;
                    var y = 200;
                    var sArq = 'anexaProcesso.php?".Sessao::getId()."';
                    var wVolta=false;
                    tela = window.open(sArq,'tela','width=300px,height=120px,resizable=1,scrollbars=0,left='+x+',top='+y);
                }
                function removeSelecionados()
                {
                    var combo = document.frm.processosAnexos;
                    newList = new Array ( combo.options.length );
                        for (var i = combo.options.length - 1; i >= 0; i--) {
                            if (combo.options[i].selected == true) {
                                combo.options[i] = null;
                            }
                        }
                }
                function desabilitar(cod)
                {
                    eval ('var x = document.frm.codDocumentos'+cod+'.checked');
                    if (x == false) {
                        eval ('document.frm.btnCopia'+cod+'.disabled = true');
                    } else {
                        eval ('document.frm.btnCopia'+cod+'.disabled = false');
                    }
                }
                function copiaDigital(cod)
                {
                    var x = 200;
                    var y = 140;
                    var sArq = '".CAM_FW_LEGADO."imagens/copiaDigitalLegado.php?".Sessao::getId()."&codDoc='+cod;
                    var wVolta=false;
                    tela = window.open(sArq,'tela','titlebar=no,hotkeys=no,width=450px,height=320px,resizable=1,scrollbars=1,left='+x+',top='+y);
                    window.tela.focus();
                }
                function ValidaProcesso()
                {
                    var mensagem = '';
                    var erro = false;
                    var campo;
                    var campoaux;

                    "; if ($vinculo == 'imobiliaria') { echo"
                        campo = trim( document.frm.numMatricula.value );
                        if (campo== '') {
                            mensagem += '@O campo Inscrição Imobiliária é obrigatório';
                            erro = true;
                        }
                    "; } else if ($vinculo == 'inscricao') { echo"
                        campo = trim( document.frm.numInscricao.value );
                        if (campo== '') {
                            mensagem += '@O campo Inscrição Econômica é obrigatório';
                            erro = true;
                        }
                    "; } 
                    
                        if ($tipoNumeracao == 2) { echo"
                        campo = trim( document.frm.codProcesso.value );
                        if (campo== '') {
                            mensagem += '@O campo Número do processo é obrigatório';
                            erro = true;
                        }
                    "; } echo"
                    
                    if(document.frm.centroCusto){
                         campo = document.frm.centroCusto.value;
                         if (campo=='') {
                             mensagem += '@O campo Centro de Custo é obrigatório';
                             erro = true;
                         }
                    }
                    
                    var expReg = /\\n/g;
                    campo = document.frm.observacoes.value.replace( expReg, '');
                    campo = trim(campo);

                    if (campo=='') {
                        mensagem += '@O campo Observações é obrigatório';
                        erro = true;
                    }

                    campo = document.frm.codClassificacao.value;
                    if (campo=='xxx') {
                        mensagem += '@A combo Classificação é obrigatória';
                        erro = true;
                    }

                    campo = document.frm.codAssunto.value;
                    if (campo=='xxx') {
                        mensagem += '@A combo Assunto é obrigatória';
                        erro = true;
                    }
                    
                    campo = jq('#inCodOrganogramaClassificacao').val();

                    if (campo=='0.00.00' || campo=='') {
                        mensagem += '@A combo Classificação de Encaminhamento de Processo é obrigatória';
                        erro = true;
                    }

                    if (erro) {
                        jq('#botaoOk').attr('disabled','disabled');
                        LiberaFrames(true,true);
                        alertaAviso(mensagem,'form','erro','".Sessao::getId()."');
                    }

                    return !(erro);

                }// Fim da function Valida

                //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
                function SalvarProcesso()
                {
                    window.parent.frames['telaPrincipal'].BloqueiaFrames(true,false);
                    if (Valida()) {
                        if (ValidaProcesso()) {
                            document.frm.action = '".$action."?".Sessao::getId()."&controle=".($controle+1)."';
                            document.frm.submit();
                        }
                    }
                    document.getElementById('botaoOk').disabled = false;
                    document.getElementById('botaoLimpar').disabled = false;
                }

                // funcao que busca Conta de Débito no frame oculto
                function busca_cgm(cod)
                {
                    if (document.frm.numCgm.value != '') {
                        document.frm.action = '".$action."?".Sessao::getId()."';
                        var f = document.frm;
                        f.target = 'oculto';
                        f.ctrl_frm.value = cod;
                        f.submit();
                    } else {
                        document.frm.numCgm.value = '';
                        document.frm.HdnnumCgm.value = '';
                        document.getElementById('nomCGM').innerHTML = '&nbsp;';
                    }
                }

                // Preenche o nome do CGM que poderá visualizar o processo
                function busca_cgm_acesso(cod)
                {
                    if (document.frm.numCgmAcesso.value != '') {
                        document.frm.action = '".$action."?".Sessao::getId()."';
                        var f = document.frm;
                        f.target = 'oculto';
                        f.ctrl_frm.value = cod;
                        f.submit();
                    } else {
                        document.frm.numCgmAcesso.value = '';
                        document.frm.HdnnumCgmAcesso.value = '';
                        document.getElementById('nomCGMAcesso').innerHTML = '&nbsp;';
                    }
                }

                function submitVinculo()
                {
                    var stOut;
                    "; if ($vinculo) { echo"
                        for (var inCount=0; inCount<document.frm.elements.length; inCount++) {
                            if (document.frm.elements[inCount].name != 'vinculo') {
                                if(document.frm.elements[inCount].type != 'select-one')
                                    document.frm.elements[inCount].value = '';
                                else
                                    document.frm.elements[inCount].options[0].value = '';
                            }
                        }
                    "; } echo" 
                    document.frm.submit();
                }
                function selecionarAcao(inCodigoAcao)
                {
                    var stActionTmp = document.frm.action;
                    var stTargetTmp = document.frm.target;
                    document.frm.action = '".CAM_GA_PROT_INSTANCIAS."processo/OCManterProcesso.php?".Sessao::getId()."';
                    document.frm.action += '&stCtrl=selecionarAcao&inCodigoAcao=' + inCodigoAcao;
                    document.frm.target = 'oculto';
                    document.frm.submit();
                    document.frm.action = stActionTmp;
                    document.frm.target = stTargetTmp;
                }

                function alteraSugerido(campo)
                {
                    if (campo.value.length == '') {
                        boSugerido=false;
                    } else {
                        boSugerido=true;
                    }
                }

                function buscaInscricao(vinculo)
                {
                    var inscricao;
                    if (document.frm.numMatricula) {
                        inscricao = document.frm.numMatricula;
                    } else if (document.frm.numInscricao) {
                        inscricao = document.frm.numInscricao;
                    }
                    if (inscricao.value.length > 0 && boSugerido == false) {
                        var stLink = ' ".CAM_PROTOCOLO."protocolo/processos/OCIncluiProcesso.php? ';
                        stLink += '".Sessao::getId()."&inInscricao=' + inscricao.value;
                        ajaxJavaScript( stLink, vinculo );
                    } else if (inscricao.value.length == 0  && boSugerido == false) {
                        document.frm.HdnnumCgm.value='';
                        document.frm.numCgm.value='';
                        document.getElementById('nomCGM').innerHTML='&nbsp;';
                    }
                }
            
                function setarFoco()
                {
                    combo = document.getElementById('comboVinculo');
                    var elementos = document.frm.elements.length;
                    var setaProximoFoco = false;

                    if (combo.value !='xxx') {
                        for (var i = 0 ; i<=elementos; i++) {
                            if (setaProximoFoco == true) {
                                document.frm.elements[i].focus();

                                return;
                            }

                            if (document.frm.elements[i].name == 'vinculo') {
                                setaProximoFoco = true;
                            }
                        }
                    }
                }
            </script>
        ";
        echo"

            <form name=\"frm\" method=\"post\" action=\"".$action."?".Sessao::getId()."\">

                <input type=\"hidden\" name=\"ctrl_frm\" value=\"\">
                <input type=\"hidden\" name=\"acao\" value=\"".$acao."\">
                <input type=\"hidden\" name=\"ctrl\" value=\"".$ctrl."\">

                <table width=\"100%\">
                <tr>
                    <td class=alt_dados colspan=2>
                        Vínculos de processo
                    </td>
                </tr>

                <tr>
                <td class=\"label\" width=\"30%\" title=\"Vínculo do processo\">
                    *Vínculo
                </td>
        ";
          
                if (!empty($vinculo) && $vinculo != "xxx") {
                    echo"
                    <td class=\"field\" width=\"70%\">
                        <input type=\"hidden\" name=\"vinculo\" value=\"".$vinculo."\"/>
                    ";
                    switch ($vinculo) {
                        case "cgm" :
                            $lblVinculo = "CGM";
                        break;

                        case "imobiliaria" :
                            $lblVinculo = "Cadastro Imobiliário";
                        break;

                        case "inscricao" :
                            $lblVinculo = "Cadastro Econômico";
                        break;

                        case "funcionario" :
                            $lblVinculo = "Cadastro de RH";
                        break;

                        case "licitacao" :
                            $lblVinculo = "Cadastro da Licitação";
                        break;
                    }
                    echo $lblVinculo;
                echo"
                </td>
                ";
                } else {
                    $arInteressados['interessados'] = array();
                    $arInteressados['permitidos'] = array();
                    Sessao::write('arRequestProtocolo', $arInteressados);
                echo"
                    <td class=\"field\" width=\"70%\">
                ";
                
                echo $this->comboVinculo("vinculo",$vinculo,"onChange='submitVinculo();'"); 
                echo"
                    </td>
                ";
                
                }
                echo"
                </tr>
                </table>

                <table width=\"100%\">
                ";
                
                if (isset($vinculo) and ($vinculo!='xxx')) {
                    echo"
                    <tr>
                        <td colspan=\"2\" class=\"alt_dados\">
                            Dados de interessado
                        </td>
                    </tr>
                    ";
                
                    if ($vinculo=="imobiliaria") {
                        $ro = " readonly='' ";
                        $disable = true;
                        $idCampo = 'numMatricula';
                        echo"
                        <tr>
                        <td class=\"label\" width=\"30%\" title=\"Número de inscrição de cadastro imobiliário\">
                            *Inscrição Imobiliária
                        </td>
                        <td class=\"field\" width=\"70%\">
                            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
                                <tr>
                                    <td class=\"field\" width=\"13%\" id=\"tdLblnumMatricula\" style=\"display:none;\">
                                        ".$numMatricula."
                                    </td>
                                    <td class=\"field\" width=\"13%\" id=\"tdFrmnumMatricula\">
                                        <input name=\"numMatricula\" id=\"numMatricula\" onblur=\"JavaScript:buscaInscricao('imobiliaria');validaMinLength(this,1);\" onkeypress=\"JavaScript:return inteiro( event );\"  maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\" value=\"".$numMatricula."\">
                                    </td>
                                    <td class=\"fieldleft\" valign=\"top\">&nbsp;
                                        <input type=\"hidden\" id=\"stNumeroDomicilio\">
                                        <a href=\"JavaScript: abrePopUp('../../../../../../gestaoTributaria/fontes/PHP/cadastroImobiliario/popups/imovel/FLProcurarImovel.php','frm','numMatricula','stNumeroDomicilio','todos','PHPSESSID=".$PHPSESSID."&iURLRandomica=20050705100644463','800','550');;\" title=\"Buscar inscrição imobiliária\">
                                        <img src=\"".CAM_FW_IMAGENS."botao_popup.png\" id=\"imgnumMatricula\" align=\"middle\" border=\"\" height=\"\" width=\"\"></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        </tr>
                    ";
                
                    } elseif ($vinculo=="inscricao") {
                        $ro = " readonly='' ";
                        $disable = true;
                        echo"
                        <tr>
                            <td class=\"label\" width=\"30%\">
                                *Inscrição Econômica
                            </td>
                            <td class=\"field\" width=\"13%\">
                                <table border=0><tr>
                                    <td class=\"field\" width=\"13%\" id=\"tdLblnumInscricao\" style=\"display:none;\">
                                        ".$numMatricula."
                                    </td>
                                    <td id=\"tdFrmnumInscricao\">
                                        <input id=\"numInscricao\" name=\"numInscricao\" onblur=\"JavaScript:buscaInscricao('inscricao');validaMinLength(this,1);\" onkeypress=\"JavaScript:return inteiro( event );\" align=\"left\" type=\"text\" value=\"".$numInscricao."\">
                                        <input type=\"hidden\" id='hdnNumInscricao' name='hdnNumInscricao' value=''>
                                    </td>
                                    <td class=\"fieldleft\" valign=\"top\">
                                        &nbsp;
                                        <a href=\"JavaScript: abrePopUp('../../../../../../gestaoTributaria/fontes/PHP/cadastroEconomico/popups/inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','numInscricao','hdnNumInscricao','todos','".Sessao::getId()."&".$_REQUEST['iURLRandomica']."','800','550');;\" title=\"Buscar inscrição econômica\">
                                            <img src=\"".CAM_FW_IMAGENS."botao_popup.png\" id=\"imgnumInscricao\" align=\"middle\" border=\"\" height=\"\" width=\"\"></a>
                                    </td>
                                </tr></table>
                            </td>
                        </tr>
                        ";
                    }
                    echo"
                    <tr>
                    <td class=\"label\" width=\"30%\" title=\"Número CGM do interessado\">
                        *Interessado
                    </td>
                    <td class=\"field\" width=\"70%\">
                        <table border=0>
                        <tr>
                            <td class=\"field\" width=\"13%\">
                                 <input name=\"numCgm\" id=\"numCgm\" onKeyUp=\"if (this.value != '') { document.getElementById('inserirCgm').disabled = true; } else { document.getElementById('inserirCgm').disabled = false; } \" onkeypress=\"JavaScript:return inteiro( event );\" onBlur=\"JavaScript:busca_cgm(1);alteraSugerido(this);\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\" value=\"".$numCgm."\">
                                 <input type='hidden' id=\"HdnnumCgm\" name='HdnnumCgm' value='' >
                                 <input type='hidden' name='nomCgm' id='nomCgm' value='' >
                            </td>
                            <td id=\"nomCGM\" class=\"fakefield\" height=\"20\" width=\"60%\">
                                &nbsp;
                            </td>
                            <td class=\"fieldleft\" valign=\"top\">
                                &nbsp;<a href=\"JavaScript: document.getElementById('inserirCgm').disabled = true; abrePopUp('../../../CGM/popups/cgm/FLProcurarCgm.php','frm','numCgm','nomCgm','todos','PHPSESSID=".$PHPSESSID."&iURLRandomica=".$REQUEST['iURLRandomica']."','800','550');;\" title=\"Buscar cgm\">
                                        <img src=\"".CAM_FW_IMAGENS."botao_popup.png\" align=\"middle\" border=\"\" height=\"\" width=\"\"></a>
                            </td>
                        </tr>
                        </table>
                    </td>
                    </tr>
                    <tr>
                        <td class=\"label\" valign=\"top\">&nbsp;</td>
                        <td class=\"field\" style=\"text-align:left;\">
                            <input type=\"button\" id=\"inserirCgm\" name=\"Inserir\" value=\"Incluir\" onclick=\"ajaxJavaScript( 'OCIncluiProcesso.php?numCgm='+document.frm.numCgm.value+'&nomCgm='+document.frm.nomCgm.value+'&vinculo=".$vinculo."', 'incluiInteressado');\" >
                            <input type=\"button\" name=\"Limpar\" value=\"Limpar\" onclick=\"$('nomCgm').innerHTML = '&nbsp;'; $('numCgm').value = ''; \" >
                        </td>
                    ";
                    // PREENCHE O CAMPO INNER CASO EXISTA O NUMCGM
                    if ($numCgm) {
                        echo "<script>";                        
                        echo "document.getElementById('nomCGM').innerHTML = '".stripslashes($nomCgm)."';";
                        echo "</script>";
                    }
                    echo"
                    </tr>
                    <!-- Listagem de Interessados - Código Legado -->
                    <tr>
                        <td colspan='2' width=\"100%\">
                            <span id=\"spnInteressados\"></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' class='alt_dados'>
                            Dados de processo
                        </td>
                    </tr>
                    <!-- Numeração Manual -->
                    ";

                    $arr = preg_split("/[^a-zA-Z0-9]/", $codClassifAssunto);
                    if ($arr[0] != 0 && $arr[1] != 0 && $arr[0] != "" && $arr[1] != "") {
                        $_POST["codClassificacao"] = $arr[0];
                        $_POST["codAssunto"]       = $arr[1];
                        $codClassificacao = $_POST["codClassificacao"];
                        $codAssunto       = $_POST["codAssunto"];
                    }
            
                    $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
                    echo"
                    <script type=\"text/javascript\">
                        function preenche_combos(campo_a, campo_b)
                        {
                            var res = campo_b.split(\".\");
                            if(res[1]=='' || res[1]=='000'){
                                document.frm.action = \"".$action."?".Sessao::getId()."&controle=".$ctrl."\";
                                document.frm.target = 'oculto';
                                document.frm.codClassificacao.value = res[0];
                                document.frm.change_ClassifAssunto2.value = '1';
                                preencheCA (campo_a, campo_b);
                                document.frm.submit();            
                            } else {
                                document.frm.action = \"".$action."?".Sessao::getId()."&controle=".$ctrl."\";
                                document.frm.target = 'telaPrincipal';
                                document.frm.codClassificacao.value = res[0];
                                document.frm.change_ClassifAssunto2.value = '1';             
                                verifica_valores();
                                document.frm.submit();
                            }
                        }

                        function verifica_valores()
                        {
                            if ((document.frm.codClassificacao.value != 'xxx') && (document.frm.codAssunto.value != 'xxx')) {
                                document.frm.action = \"".$action."?".Sessao::getId()."&controle=".$ctrl."&boVerificaValores=true\";
                                document.frm.target = 'telaPrincipal';
                                document.frm.change_ClassifAssunto.value = '1';
                                document.frm.submit();
                            }
                        }
                        window.onload = function() {
                            document.frm.observacoes.focus();
                        }
                    </script>
                    ";

                    // se codAssunto e codClassficacao estado setados...
                    // monta a variavel $codClassifAssunto
                    if ($codAssunto and $codClassificacao and
                        $codClassificacao != 'xxx' and $codAssunto != 'xxx' and
                        $change_ClassifAssunto == 1) {
                        $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassificacao."-".$codAssunto);
                        $codClassifAssunto   = $arCodClassifAssunto[1];
                    }

                    // quando a operacao for inclusao de processo utiliza a verificaca de valores para submeter o form
                    if (Sessao::read('acao') == 57) {
                        $submit = "verifica_valores();";
                    } else {
                        $submit = "";
                    }
                    echo"
                    <tr>
                        <td class=label width=30% rowspan=3 title=\"Classificação e assunto de processo\">*Classificação/Assunto</td>
                        <td class=field>
                            <input type=\"hidden\" size=\"2\" name=\"change_ClassifAssunto\" >
                            <input type=\"hidden\" size=\"2\" name=\"change_ClassifAssunto2\">
                    ";
                    if (!empty($codClassificacao) && !empty($codAssunto)) {
                        echo $codClassifAssunto;
                        echo "<input type=\"hidden\" name =\"codClassifAssunto\" id=\"codClassifAssunto\" size=\"25\" maxlength=\"20\" value=\"".$codClassifAssunto."\">";    
                        if (strlen($codClassifAssunto) < 3 && strlen($codClassifAssunto) > 0) {
                            echo "
                                <script type='text/javascript'>
                                    alertaAviso('Classificação/Assunto Inválido','unica','erro','".Sessao::getId()."');
                                    mudaTelaPrincipal('incluiProcesso.php?".Sessao::getId()."');
                                </script>
                            ";
                        }
                        $arrClassifAssunto = explode(".",$codClassifAssunto);
                        if (($arrClassifAssunto[0] == '000') or ($arrClassifAssunto[1] == '000')) {
                            echo "
                                <script type='text/javascript'>
                                    alertaAviso('Classificação/Assunto Inválido','unica','erro','".Sessao::getId()."');
                                    mudaTelaPrincipal('incluiProcesso.php?".Sessao::getId()."');
                                </script>
                            ";
                        }
                    } else {
                        $mascaraAssunto_size=strlen($mascaraAssunto);
                        if ($controle==5) {
                            $codClassifAssunto='';
                        }
                        echo"
                        <input type='text' name='codClassifAssunto' size=".$mascaraAssunto_size." maxlength=".$mascaraAssunto_size." value='".$codClassifAssunto."'
                            onKeyUp=\"JavaScript: mascaraDinamico('".$mascaraAssunto."', this, event);\"
                            onChange=\"JavaScript:preenche_combos( 'codClassifAssunto', this.value );\">
                        ";
                    }
                    echo"
                        </td>
                    </tr>
                    <tr>
                    <td class=field>
                    ";
                
                    if (!empty($codClassificacao)) {
                        $codClassificacao = ($codClassificacao==='xxx') ? '000' : $codClassificacao;
                        $sSQL = "SELECT * FROM sw_classificacao where cod_classificacao=$codClassificacao";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();
                        while (!$dbEmp->eof()) {
                            $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
                            $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
                            $dbEmp->vaiProximo();
                        }
                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        if (!$codClassificacaof) {
                            echo"
                                <script type='text/javascript'>
                                    alertaAviso('Classificação/Assunto Inválido','unica','erro','".Sessao::getId()."');
                                    mudaTelaPrincipal('incluiProcesso.php?".Sessao::getId()."');
                                </script>
                            ";
                        }
                        echo"<input type=\"hidden\" name=\"codClassificacao\" size=\"25\" maxlength=\"20\" value=\"".$codClassificacao."\">";
                        echo $nomClassificacaof;
                    }
                    if (empty($codClassificacao)) {
                        echo" <select name=\"codClassificacao\" onChange=\"JavaScript: preencheCA( 'codClassificacao', this.value ); ".$submit."\"";
                    }

                    if (empty($codClassificacao)) {
                        echo" style=\"width: auto\"><option value=\"xxx\">Selecione classificação</option>";
                        $sSQL = "SELECT * FROM sw_classificacao ORDER by nom_classificacao";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();
                        $comboCla = "";
                        while (!$dbEmp->eof()) {
                            $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
                            $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
                            $dbEmp->vaiProximo();
                            $comboCla .= "         <option value=".$codClassificacaof;
                            if (isset($codClassificacao)) {
                                if ($codClassificacaof == $codClassificacao)
                                    $comboCla .= " SELECTED";
                            }
                            $comboCla .= ">".$nomClassificacaof."</option>\n";
                        }
                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        echo $comboCla;
                        echo"</select>";
                    }
                    echo"
                    </td>
                    </tr>
                    <tr>
                    <td class=field>
                    ";
                
                    if (!empty($codAssunto)) {
                        $codAssunto = ($codAssunto==='xxx') ? '000' : $codAssunto;
                        $sSQL = "SELECT * FROM sw_assunto WHERE cod_assunto = ".$codAssunto." AND cod_classificacao = ".$codClassificacao."";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();
                        while (!$dbEmp->eof()) {
                            $codAssuntof  = trim($dbEmp->pegaCampo("cod_assunto"));
                            $nomAssuntof  = trim($dbEmp->pegaCampo("nom_assunto"));
                            $dbEmp->vaiProximo();
                        }
                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        if (!$codAssuntof) {
                            echo "
                                <script type='text/javascript'>
                                    alertaAviso('Classificação/Assunto Inválido','unica','erro','".Sessao::getId()."');
                                    mudaTelaPrincipal('incluiProcesso.php?".Sessao::getId()."');
                                </script>
                            ";
                        }
                        echo"<input type=\"hidden\" name=\"codAssunto\" size=\"25\" maxlength=\"20\" value=\"".$codAssunto."\">";
                        echo $nomAssuntof;
                    } else {
                        echo"<select name=\"codAssunto\" onChange=\"JavaScript: preencheCA( 'codAssunto', this.value ); verifica_valores();\" ";
                        echo"\"style=\"width: auto\"><option value=\"xxx\" SELECTED>Selecione assunto</option>";
                        if (($codClassificacao > 0) AND ($codClassificacao != "xxx")) {
                            $sSQL = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$codClassificacao." ORDER by nom_assunto";
                            $dbEmp = new dataBaseLegado;
                            $dbEmp->abreBD();
                            $dbEmp->abreSelecao($sSQL);
                            $dbEmp->vaiPrimeiro();
                            $comboAss = "";
                            while (!$dbEmp->eof()) {
                                $codAssuntof  = trim($dbEmp->pegaCampo("cod_assunto"));
                                $nomAssuntof  = trim($dbEmp->pegaCampo("nom_assunto"));
                                $dbEmp->vaiProximo();
                                $comboAss .= "         <option value=".$codAssuntof;
                                if (isset($codAssunto)) {
                                    if ($codAssuntof == $codAssunto)
                                        $comboAss .= " SELECTED";
                                }
                                $comboAss .= ">".$nomAssuntof."</option>\n";
                            }
                            $dbEmp->limpaSelecao();
                            $dbEmp->fechaBD();
                            echo $comboAss;
                            echo"</select>";
                        }
                    }
                    echo"
                    </td>
                    </tr>
                    ";                
                }
                
                $varClassifAssunto = preg_split("/[^a-zA-Z0-9]/", $codClassifAssunto);
                //if CLASSIFICACAO ASSUNTO
                if ( ($varClassifAssunto[0] != 0 and $varClassifAssunto[1] != 0)
                    or ($codAssunto != ""
                    and $codAssunto != "xxx"
                    and $codAssunto != 0
                    and $codClassificacao != ""
                    and $codClassificacao != "xxx"
                    and $codClassificacao != 0)
                ){

                    if ($codClassifAssunto) {
                        $aux = preg_split("/[^a-zA-Z0-9]/", $codClassifAssunto);
                        $codAssunto = $aux[1];
                        $codClassificacao = $aux[0];
                    }

                    if ($tipoNumeracao == 2) {

                        $sSQL = "SELECT * FROM administracao.configuracao where cod_modulo = 5 AND parametro = 'mascara_processo'";
                        $dbEmp = new dataBaseLegado;
                        $dbEmp->abreBD();
                        $dbEmp->abreSelecao($sSQL);
                        $dbEmp->vaiPrimeiro();
                        while (!$dbEmp->eof()) {
                            $mascProcesso  = trim($dbEmp->pegaCampo("valor"));
                            $dbEmp->vaiProximo();
                        }
                        $dbEmp->limpaSelecao();
                        $dbEmp->fechaBD();
                        $arMascProc = preg_split("/[^a-zA-Z0-9]/", $mascProcesso);
                        $mascara_processo_size=strlen($arMascProc[0]);
                        echo"
                        <tr>
                            <td class=\"label\">
                                *Número do Processo
                            </td>
                            <td class=\"field\">
                                <input type='text' name='codProcesso' size=\"".$mascara_processo_size."\" maxlength=\"".$mascara_processo_size."\" value=\"".$codProcesso."\" >
                                <b>/</b>                       
                                <input type='text' name='anoExercicioManual' value=\"".$anoExercicioManual."\" size='5' maxlength='4' readonly=\"\">
                            </td>
                        </tr>
                        ";
                        $anoExercicio=$anoExercicioManual;
                    }
                    
                    $sSQL = "SELECT * FROM administracao.configuracao where cod_modulo = 5 AND parametro = 'centro_custo' AND exercicio <= '".Sessao::getExercicio()."' ORDER BY exercicio DESC LIMIT 1 ";
                    $dbCC = new dataBaseLegado;
                    $dbCC->abreBD();
                    $dbCC->abreSelecao($sSQL);
                    $dbCC->vaiPrimeiro();
                    while (!$dbCC->eof()) {
                        $centroCusto  = trim($dbCC->pegaCampo("valor"));
                        $dbCC->vaiProximo();
                    }
                    $dbCC->limpaSelecao();
                    $dbCC->fechaBD();
                        
                    if($centroCusto=='true'){
                         $sSQL = "SELECT * FROM almoxarifado.centro_custo ORDER BY descricao";
                         $dbCC = new dataBaseLegado;
                         $dbCC->abreBD();
                         $dbCC->abreSelecao($sSQL);
                         $dbCC->vaiPrimeiro();
                         while (!$dbCC->eof()) {
                             $listaCentroCusto[trim($dbCC->pegaCampo("cod_centro"))]  = trim($dbCC->pegaCampo("descricao"));
                             $dbCC->vaiProximo();
                         }
                         $dbCC->limpaSelecao();
                         $dbCC->fechaBD();
                    
                         echo"
                         <tr>
                             <td class=\"label\">
                                 *Centro de Custo
                             </td>
                             <td class=\"field\">
                                   <select name='centroCusto' style='width: 200px'><option value=''>Selecione</option> \n";
                                   
                                   foreach ($listaCentroCusto AS $key => $value) {
                                     $selected = "";
                                     echo "<option value='".$key."' ".$selected.">".$value."</option>\n";
                                   }
                                   
                            echo "
                                   </select>                     
                             </td>
                         </tr>";
                    }
                    
                    echo"
                    <tr>
                        <td class=\"label\" title=\"Informações adicionais do processo\">
                            *Observações
                        </td>
                        <td class=\"field\">                            
                            <textarea autofocus name='observacoes' cols='40' rows='4' >".$observacoes."</textarea>                            
                        </td>
                    </tr>
                    <tr>
                        <td class=\"label\" title=\"Descrição rápida do assunto do processo\">
                            Assunto Reduzido
                        </td>
                        <td class=\"field\">
                            <input type=\"text\" name='resumo' size=\"80\" maxlength=\"80\" value=\"".$resumo."\">
                        </td>
                    </tr>
                    ";
                    if ($codAssunto != "" && $codAssunto != "xxx") {
                        echo"
                        <tr>
                            <td class=label title=\"Define se o processo é confidencial\">
                                Confidencial
                            </td>
                        ";
                        $dbConfig = new dataBaseLegado;
                        $dbConfig->abreBd();
                        $select = "select confidencial from sw_assunto where cod_assunto = $codAssunto";
                        $dbConfig->abreSelecao($select);
                        $confidencial = $dbConfig->pegaCampo("confidencial");
                        $dbConfig->limpaSelecao();
                        $dbConfig->fechaBd();
                        if ($confidencial == 't' || $conf == 't'){
                            $boMostraTable = 'table-row'; 
                            echo "<td class=\"field\">
                                    <input type='radio' name='conf' value='t' onClick='jQuery(\"#tablePermissao\").css(\"display\", \"table-row\");' checked>Sim
                                    <input type='radio' name='conf' value='f' onClick='jQuery(\"#tablePermissao\").css(\"display\", \"none\");'>Não
                                </td>
                            </tr>";
                        }else{
                            $boMostraTable = 'none'; 
                            echo "<td class=\"field\">
                                    <input type='radio' name='conf' value='t' onClick='jQuery(\"#tablePermissao\").css(\"display\", \"table-row\");'>Sim
                                    <input type='radio' name='conf' value='f' onClick='jQuery(\"#tablePermissao\").css(\"display\", \"none\");' checked>Não
                                    </td>
                                </tr>";
                        }
                    }

                    # CGM Visualizador. Quando confidencial quem pode visualizar o processo.
                    echo "
                    <tr id='tablePermissao' style='display:".$boMostraTable.";'>
                        <td class='label' title='Informe o CGM que pode visualizar o processo' >CGM Visualizador</td>
                        <td class='field'  >
                            <table border=0>
                                <tr>
                                    <td class=\"field\" width=\"13%\">
                                         <input name=\"numCgmAcesso\" id=\"numCgmAcesso\" onkeypress=\"JavaScript:return inteiro( event );\" onBlur=\"JavaScript:busca_cgm_acesso(2);alteraSugerido(this);\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\" value=\"".$numCgm."\">
                                         <input type='hidden' id=\"HdnnumCgmAcesso\" name='HdnnumCgmAcesso' value='' >
                                         <input type='hidden' name='nomCgmAcesso' id='nomCgmAcesso' value='' >
                                    </td>
                                    <td id=\"nomCGMAcesso\" class=\"fakefield\" height=\"20\" width=\"60%\">
                                        &nbsp;
                                    </td>
                                    <td class=\"fieldleft\" valign=\"top\">
                                        &nbsp;<a href=\"JavaScript: abrePopUp('../../../CGM/popups/cgm/FLProcurarCgm.php','frm','numCgmAcesso','nomCGMAcesso','vinculado','PHPSESSID=".$PHPSESSID."&iURLRandomica=".$REQUEST['iURLRandomica']."&stTabelaVinculo=administracao.usuario&stCampoVinculo=numcgm','800','550');\" title=\"Buscar cgm\">
                                        <img src=\"".CAM_FW_IMAGENS."botao_popup.png\" align=\"middle\" border=\"\" height=\"\" width=\"\"></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td class='field' style='text-align:left;' colspan='3'>
                                        <input type='button' id='inserirCgmAcesso' name='Inserir' value='Incluir' onclick=\"ajaxJavaScript( 'OCIncluiProcesso.php?numCgmAcesso='+document.frm.numCgmAcesso.value+'&nomCgmAcesso='+document.frm.nomCgmAcesso.value+'&vinculo=".$vinculo."', 'incluiAcessoCGM');\" >
                                        <input type='button' name='Limpar' value='Limpar' onclick=\"$('nomCGMAcesso').innerHTML = '&nbsp;'; $('numCgmAcesso').value = ''; \" >
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr> 

                    <!-- Listagem de CGM que pode visualizar o processo -->
                    <tr>
                        <td colspan='2' width=\"100%\">
                            <span id=\"spnPermitidos\"></span>
                        </td>
                    </tr> ";

                    echo"

                    <tr>
                        <td class=alt_dados colspan=\"2\">
                            Atributos de processo
                        </td>
                    </tr>
                    ";

                    $select = 	"
                        SELECT
                            AP.cod_atributo,
                            AP.nom_atributo,
                            AP.tipo,
                            AP.valor_padrao
                        FROM
                            sw_atributo_protocolo AS AP,
                            sw_assunto_atributo   AS AT
                        WHERE
                            AP.cod_atributo      = AT.cod_atributo AND
                            AT.cod_classificacao = ".$codClassificacao." AND
                            AT.cod_assunto       = ".$codAssunto."
                        ORDER BY
                            AP.nom_atributo";

                    $dbConfig = new dataBaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($select);

                    while (!($dbConfig->eof())) {
                        $codAtributo = $dbConfig->pegaCampo("cod_atributo");
                        $nomAtributo = $dbConfig->pegaCampo("nom_atributo");
                        $tipo        = $dbConfig->pegaCampo("tipo");

                        if (!(isset($valorAtributo[$codAtributo]))) {
                            $valorPadrao = $dbConfig->pegaCampo("valor_padrao");
                        } else {
                            $valorPadrao = $valorAtributo[$codAtributo];
                        }

                        $valorLista = $dbConfig->pegaCampo("valor_padrao");

                        if ($tipo == "t") {
                            echo "<tr>\n";
                            echo "	<td class=\"label\">\n";
                            echo $nomAtributo;
                            echo "	</td>\n";
                            echo "	<td class=\"field\">\n";
                            echo "		<input type='text' size='60' name=valorAtributo[".$codAtributo."] value='".$valorPadrao."'>";
                        }

                        if ($tipo == "n") {
                            echo "<tr>\n";
                            echo "	<td class=\"label\">\n";
                            echo $nomAtributo;
                            echo "	</td>\n";
                            echo "	<td class=\"field\">\n";
                            echo "		<input type='text' size='60' name=valorAtributo[".$codAtributo."] value='".$valorPadrao."' onKeyPress=return(isValido(this,event,'0123456789'))>";
                        }

                        if ($tipo == "l") {
                            echo "<tr>\n";
                            echo "	<td class=\"label\">\n";
                            echo $nomAtributo;
                            echo "	</td>\n";
                            echo "	<td class=\"field\">\n";
                            $lista = explode("\n", $valorLista);
                            $selected = "";
                            echo "	<select name='valorAtributo[".$codAtributo."]' style='width: 200px'><option value=\"xxx\">Selecione</option> \n";

                            while (list($key, $val) = each($lista)) {
                                $val = trim($val);
                                if ($valorAtributo[$codAtributo] == $val) {
                                    $selected = "selected";
                                }
                                echo "		<option value='".$val."' ".$selected.">".$val."</option>\n";
                                $selected = "";
                            }
                            echo "	</select>\n";
                        }
                        $dbConfig->vaiProximo();
                    }
                    echo"
                    </td>
                    </tr>
                    <tr>
                        <td class=alt_dados colspan=\"2\">
                            Documentos de processo
                        </td>
                    </tr>
                    <tr>
                        <td class=\"label\">
                            Nome do documento
                        </td>
                        <td class=\"field\">
                    ";
                            if (((isset($_REQUEST['codAssunto'])) and $_REQUEST['codAssunto'] != '' and ($_REQUEST['codAssunto']!='xxx') and (isset($_REQUEST['codClassificacao'])) and $_REQUEST['codClassificacao'] != '' and ($_REQUEST['codClassificacao']!='xxx') and $_REQUEST['codClassificacao'] != '') or (isset($_REQUEST['codClassifAssunto']) and $_REQUEST['codClassifAssunto'] != '') ) {
                                if ($_REQUEST['tipoProcesso']!='xxx') {
                                    $aux = preg_split("/[^a-zA-Z0-9]/", $codClassifAssunto);
                                    $codAssunto = $aux[1];
                                    $codClassificacao = $aux[0];
                                    if ($codAssunto == '') {
                                        $codAssunto = $_REQUEST['codAssunto'];
                                    }
                                    if ($codClassificacao == '') {
                                        $codClassificacao = $_REQUEST['codClassificacao'];
                                    }
                                    echo $this->checkDocumentos($codClassificacao,$codAssunto);
                                }
                            }
                    echo"                            
                        </td>
                    </tr>
                    <tr>
                        <td class=alt_dados colspan=\"2\">
                            Encaminhamento de processo
                        </td>
                    </tr>
                    ";
                    if ($_REQUEST['codClassificacao'] != "xxx" && $_REQUEST['codAssunto'] != "xxx") {
                        # Verifica se o processo possui algum andamento padrão.
                        $andamentoPadrao = new processosLegado;
                        $andamentoPadrao->verificaAndamentoPadrao($_REQUEST['codClassificacao'], $_REQUEST['codAssunto']);

                        if (!isset($codMasSetor)) {
                            $select = "
                            SELECT
                                *
                            FROM
                                sw_andamento_padrao
                            WHERE
                                cod_classificacao = ".$_REQUEST['codClassificacao']." AND
                                cod_assunto       = ".$_REQUEST['codAssunto']." AND
                                ordem             = 1";

                            $conn = new dataBaseLegado;
                            $conn->abreBd();
                            $conn->abreSelecao($select);

                            if ($conn->numeroDeLinhas > 0) {
                                $inCodOrganogramaAtivo = SistemaLegado::pegaDado("cod_organograma", "organograma.organograma", "WHERE ativo = true");
                                $inCodOrganogramaAtual = SistemaLegado::pegaDado("cod_organograma", "organograma.orgao_nivel", "WHERE cod_orgao = ".$conn->pegaCampo('cod_orgao')." LIMIT 1");
                                # Validação para só permitir que seja sugerido o andamento padrão caso ele faça parte do organograma ativo.
                                 if ($inCodOrganogramaAtual == $inCodOrganogramaAtivo) {
                                      $codAssuntoPadrao       = $conn->pegaCampo('cod_assunto');
                                      $codClassificacaoPadrao = $conn->pegaCampo('cod_classificacao');
                                      $codOrgaoPadrao         = $conn->pegaCampo('cod_orgao');
                                      $numPassagensPadrao     = $conn->pegaCampo('num_passagens');
                                      $descricaoPadrao        = $conn->pegaCampo('descricao');
                                      $ordemPadrao            = $conn->pegaCampo('ordem');
                                      $numDiaPadrao           = $conn->pegaCampo('num_dia');
                                      $codMasSetor            = $codOrgaoPadrao;
                                }
                            }

                            $conn->limpaSelecao();
                            $conn->fechaBd();
                        }
                    }

                    $obFormulario = new Formulario;
                    $obFormulario->setLarguraRotulo(0);
                    $obFormulario->setForm(null);

                    # Instancia para o novo objeto Organograma
                    $obIMontaOrganograma = new IMontaOrganograma;
                    $obIMontaOrganograma->setNivelObrigatorio(1);

                    if (!empty($codOrgaoPadrao))
                        $obIMontaOrganograma->setCodOrgao($codOrgaoPadrao);

                    $obIMontaOrganograma->geraFormulario($obFormulario);

                    $obFormulario->montaHtml();
                    echo $obFormulario->getHTML();
                    echo"
                    <script type=\"text/javascript\">
                        if (document.frm.codOrgao.value == \"xxx\") {
                            window.scrollTo(0,200);
                        }
                    </script>
                    ";
                }//FIM IF CLASSIFICACAO ASSUNTO
                if ($codClassificacao and $codAssunto) {
                    $this->montaLinksAssuntoAcao($codClassificacao, $codAssunto);
                }
                echo"
                <script type=\"text/javascript\">
                    function LimpaTela()
                    {
                        ajaxJavaScript('OCIncluiProcesso.php?', 'limpaListaInteressados');
                        document.frm.action = \"".$action."?".Sessao::getId()."&controle=0&ctrl=0&acao=57\";
                        document.frm.target = 'telaPrincipal';
                        window.location = 'incluiProcesso.php?".Sessao::getId()."&acao=57';
                    }
                </script>
                ";
                echo"
                </table>
                <script type=\"text/javascript\">
                    if (document.frm.inCodOrganograma) {
                        if (document.frm.inCodOrganograma.value == \"xxx\") {
                            document.getElementById('botaoOk').disabled = true;
                        }
                    }
                </script>
                </form>
                ";

                if (isset($vinculo) and ($vinculo!='xxx')) {
                    $obFormAssinaturas = new Formulario;
                    $obForm = new Form;
                    $obForm->setName('frmAssinatura');
                    $obForm->setId('frmAssinatura');

                    include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
                    $obMontaAssinaturas = new IMontaAssinaturas;
                    $obMontaAssinaturas->setOpcaoAssinaturas( false );
                    $obMontaAssinaturas->obRadioAssinaturasSim->obEvento->setOnClick("ajaxJavaScript('OCIncluiProcesso.php?".Sessao::getId()."&stIncluirAssinaturas='+this.value, 'montaEntidade');");
                    $obMontaAssinaturas->obRadioAssinaturasNao->obEvento->setOnCLick("ajaxJavaScript('OCIncluiProcesso.php?".Sessao::getId()."&stIncluirAssinaturas='+this.value, 'montaEntidade');");

                    $obSpnEntidade = new Span();
                    $obSpnEntidade->setId('spnEntidade');

                    $obFormAssinaturas->addForm ( $obForm );
                    $obFormAssinaturas->setLarguraRotulo(30);
                    $obFormAssinaturas->addSpan($obSpnEntidade);
                    $obMontaAssinaturas->geraFormulario($obFormAssinaturas);
                    $obFormAssinaturas->montaHtml();
    
                    echo $obFormAssinaturas->getHTML();
                }

                if
                (  (isset($codAssunto)) and ($codAssunto!='xxx') and
                (isset($codClassificacao)) and ($codClassificacao!='xxx') and
                (isset($vinculo)) and ($vinculo!='xxx')){
                echo"
                    <table class='table' width=\"100%\" cellspacing=\"2\" cellpadding=\"2\" border=\"0\">
                    <tr>
                        <td style='text-align:left;' class='field'>
                            <input type=\"button\" value=\"OK\" style='width: 60px;' name='botaoOk' id='botaoOk' onClick=\"SalvarProcesso();\">
                            &nbsp;
                            <input type=\"button\" value=\"Limpar\" id= 'botaoLimpar' style='width: 60px;' onClick=\"LimpaTela();\">
                        </td>
                    </tr>
                ";

                echo "<script> jq('#botaoOK').val(); </script>";

                }
        break;
    }

    //Checar se o tipo de proceesso selecionado possui andamento padrão
    $arInteressados = Sessao::getRequestProtocolo();

    if (count($arInteressados['interessados']) > 0) {
        echo "<script>";
        echo " ajaxJavaScript( 'OCIncluiProcesso.php?vinculo=".$vinculo."', 'montaListaInteressados');";
        echo "</script>";
    }


}// Fim da function formIncluiProcesso

function montaLinksAssuntoAcao($inCodigoClassificacao, $inCodigoAssunto)
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
    include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAcao.class.php");
    $rsAcao = new RecordSet();
    $obTPROAssuntoAcao = new TPROAssuntoAcao();
    $obTPROAssuntoAcao->obTPROAssunto = &$obTPROAssunto;
    $stFiltroAcao  = " AND assunto_acao.cod_classificacao = ".$inCodigoClassificacao;
    $stFiltroAcao .= " AND assunto_acao.cod_assunto = ".$inCodigoAssunto;
    $stOrdem .= " ORDER BY                          \n";
    $stOrdem .= " assunto_acao.cod_classificacao,   \n";
    $stOrdem .= " assunto_acao.cod_acao,            \n";
    $stOrdem .= " gestao.ordem,                     \n";
    $stOrdem .= " modulo.ordem,                     \n";
    $stOrdem .= " funcionalidade.ordem,             \n";
    $stOrdem .= " acao.ordem                        \n";
    $obTPROAssuntoAcao->recuperaRelacionamentoComPermissao($rsAcao,$stFiltroAcao,$stOrdem);

    $obLista = new Lista();
    $obLista->setMostraPaginacao(false);
    $obLista->setTitulo('Lista de Ações Relacionadas');
    $obLista->setRecordSet($rsAcao);
    $obLista->addCabecalho('', 5);
    $obLista->addCabecalho('Gestão', 15);
    $obLista->addCabecalho('Modulo', 15);
    $obLista->addCabecalho('Funcionalidade', 15);
    $obLista->addCabecalho('Ação', 15);
    $obLista->addCabecalho('', 5);

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_gestao');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_modulo');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_funcionalidade');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('[cod_acao]-[nom_acao]');
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao('avancarproc');
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:selecionarAcao()");
    $obLista->ultimaAcao->addCampo("1","cod_acao");
    $obLista->commitAcao();
    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();

    $stLink  = "<tr><td colspan=2>";
    $stLink .= $stHTML."</td></tr>";

    echo $stLink;
}

/***************************************************************************
Monta o formulário para receber processos em andamento que podem
ser recebidos pelo setor do usuário
/**************************************************************************/
    public function formSelecionaEncaminhaProcesso($action,$setor,$dpto,$unidade,$orgao,$anoExercicio)
    {
            //Constroi a query para encontrar os processos recebidos por um determinado setor -- USAR CHAVE COMPOSTA PARA COMPARAR O SETOR
    $sql =  "
            SELECT sw_processo.ano_exercicio
                 , sw_processo.cod_processo
                 , sw_processo.timestamp
                 , sw_ultimo_andamento.cod_andamento
                 , sw_classificacao.nom_classificacao
                 , sw_assunto.nom_assunto
                 , sw_cgm.nom_cgm
             FROM  public.sw_processo
                 , public.sw_processo_interessado
                 , public.sw_ultimo_andamento
                 , public.sw_classificacao
                 , public.sw_assunto
                 , public.sw_cgm
                 , public.sw_situacao_processo
             WHERE sw_processo.cod_classificacao     = sw_assunto.cod_classificacao
               AND sw_processo.cod_assunto           = sw_assunto.cod_assunto
               AND sw_processo_interessado.cod_processo  = sw_processo.cod_processo
               AND sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio
               AND sw_processo_interessado.numcgm    = sw_cgm.numcgm
               AND sw_processo.cod_situacao          = sw_situacao_processo.cod_situacao
               AND sw_ultimo_andamento.ano_exercicio = sw_processo.ano_exercicio
               AND sw_ultimo_andamento.cod_processo  = sw_processo.cod_processo
               AND sw_assunto.cod_classificacao      = sw_classificacao.cod_classificacao
               AND sw_ultimo_andamento.cod_orgao                  =       '".Sessao::read('codOrgao')."'
               AND sw_ultimo_andamento.cod_unidade                =       '".Sessao::read('codUnidade')."'
               AND sw_ultimo_andamento.cod_departamento           =       '".Sessao::read('codDpto')."'
               AND sw_ultimo_andamento.cod_setor                  =       '".Sessao::read('codSetor')."'
               AND sw_ultimo_andamento.ano_exercicio_setor        =       '".Sessao::getExercicio()."'
               AND sw_situacao_processo.cod_situacao              =       3      -- Regra de Negócio (Fixo)
              ";

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;

        if ($registros) {
?>

        <table width='85%'>
            <tr>
                <td class='alt_dados' width='10%'>Cód. Processo</td>
                <td class='alt_dados' width='35%'>Classificação</td>
                <td class='alt_dados' width='35%'>Assunto</td>
                <td class='alt_dados' width='20%'>Data (Hora)</td>
                <td class='alt_dados' width='10%'>Usuário</td>
                <td class='alt_dados' width='10%' colspan="2">Consulta</td>

            </tr>
<?php

            while (!$conn->eof()) {
                $codProcesso = $conn->pegaCampo("cod_processo");
                $anoEx = $conn->pegaCampo("ano_exercicio");
                $codAssunto = $conn->pegaCampo("cod_assunto");
                $codClassificacao = $conn->pegaCampo("cod_classificacao");
                $classificacao = $conn->pegaCampo("nom_classificacao");
                $assunto = $conn->pegaCampo("nom_assunto");
                $timestamp = $conn->pegaCampo("timestamp");               
                $chave = $codProcesso."-".$anoEx."-".$codClassificacao."-".$codAssunto;
?>
            <tr>
                <td class="show_dados">
            <?=$codProcesso."/".$anoEx;?>
                </td>
                <td class="show_dados">
            <?=$classificacao;?>
                </td>
                <td class="show_dados">
            <?=$assunto;?>
                </td>
                <td class="show_dados">
            <?php
                    $date = timestampToBr($timestamp,d);
                    $time = timestampToBr($timestamp,hs);
                    echo $date." (".$time.")";
            ?>
                </td>
                <td class="show_dados">
            <?=$usuario;?>
                </td>
                <td class="show_dados"><div align="center">
                    <a href='consultaProcesso.php?<?=Sessao::getId()?>&codProcesso=<?=$codProcesso?>&anoExercicio=<?=$anoEx?>&controle=0&ctrl=2'>
                        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Consultar Processo"  border=0>
                    </a></div>
                </td>
                <td class="show_dados">
                    <input type="button" value="Encaminhar" onClick="document.location='encaminhaProcesso?<?=Sessao::getId();?>&codProcesso=<?=$chave?>&controle=1'">
                </td>
            </tr>
<?php
                $conn->vaiProximo();
            }
        $conn->limpaSelecao();
        $conn->fechaBD();
?>
        </table>
        </form>
<?php } else { ?>
            <br><b>Nenhum processo disponível para encaminhamento!</b>
<?php } ?>
<?php
    }//Fim da function formSelecionaEncaminhaProcesso

/***************************************************************************
Monta o formulário para receber processos em andamento que podem
ser recebidos pelo setor do usuário
/**************************************************************************/
function formEncaminhaProcesso($action, $codProcesso, $anoExercicio, $codClassificacao, $codAssunto, $orgao, $pagina, $flag)
{
    $proc = new processosLegado;
    # Verifica se o tipo de processo selecionado possui andamento padrão
    $verificaAndamentoPadrao = $proc->verificaAndamentoPadrao($codClassificacao,$codAssunto);

    # Verifica qual o próximo setor dentro do andamento padrão
    if ($verificaAndamentoPadrao) {
        $proximo          = $proc->verficaProximoAndamento($codProcesso, $anoExercicio, $codClassificacao, $codAssunto, $orgao);
        $matrizPadrao     = $proc->matrizAndamentoPadrao($codClassificacao,$codAssunto);
        $chaveSetorPadrao = $matrizPadrao[$proximo]['chaveSetor'];

        # Caso o proximo retorne algo diferente de zero (0)
        if ($chaveSetorPadrao && $exercicioPadrao && $proximo) {
            $codMasSetor = $chaveSetorPadrao."/".$exercicioPadrao;
        }
    }

    $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
    $stNomeAssunto  = pegaDado("nom_assunto","sw_assunto","Where cod_classificacao = '".$codClassificacao."' and cod_assunto = '".$codAssunto."' ");
?>
<script src="<?=CAM_GA;?>/javaScript/ifuncoesJs.js" type="text/javascript"></script>

        <form name='frm' action='<?=$action;?>?<?=Sessao::getId();?>' method='post' onSubmit="return false;">
        <input type="hidden" name="anoExercicio" value='<?=$anoExercicio;?>'>
        <input type="hidden" name="pagina" value='<?=$pagina;?>'>
        <input type="hidden" name="codProcessoFl" value="<?=$codProcessoFl?>">
        <input type="hidden" name="codClassificacao" value="<?=$codClassificacao?>">
        <input type="hidden" name="codAssunto" value="<?=$codAssunto?>">
        <input type="hidden" name="numCgm" value="<?=$numCgm?>">
        <input type="hidden" name="numCgmU" value="<?=$numCgmU?>">
        <input type="hidden" name="numCgmUltimo" value="<?=$numCgmUltimo?>">
        <input type="hidden" name="dataInicial" value="<?=$dataInicial?>">
        <input type="hidden" name="dataFinal" value="<?=$dataFinal?>">
        <input type="hidden" name="ordem" value="<?=$ordem?>">
        <table width='100%'>
<?php
        if ($flag == 0 && empty($chaveSetorPadrao)) {
            $andamentoPadrao = new processosLegado;
            $andamentoPadrao->verificaAndamentoPadrao($codClassificacao, $codAssunto);
            $codOrgao = $andamentoPadrao->orgaoPadrao;
        } else {
            $codOrgao = $chaveSetorPadrao;
        }
?>
                <tr>
                    <td colspan='2' class='alt_dados'>
                        Encaminhamento de Processo
                    </td>
                <tr>
                    <td class="label" width='20%'>
                        Processo
                    </td>
                    <td class="field">
                        <?=$codProcesso."/".$anoExercicio;?>
                    </td>
                </tr>
                <tr>
                    <td class="label">
                        Assunto
                    </td>
                    <td class="field">
                    <?php
                        $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto,$codClassificacao.".".$codAssunto);
                        echo $arCodClassifAssunto[1].' '.$stNomeAssunto;
                    ?>
                    </td>
                </tr>
            </table>
        <?php
          
            $obFormulario = new Formulario;
            $obFormulario->setForm(null);

            # Instancia para o novo objeto Organograma
            $obIMontaOrganograma = new IMontaOrganograma;
            $obIMontaOrganograma->setNivelObrigatorio(1);

            if (!empty($codOrgao))
                $obIMontaOrganograma->setCodOrgao($codOrgao);

            $obIMontaOrganograma->geraFormulario($obFormulario);

            $obFormulario->montaHtml();
            echo $obFormulario->getHTML();

        ?>
        <script type="text/javascript">

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                var sCodigo;
                var formulario = document.frm;
                var nSetor;
                var nomParametro;
                var varsGet;
                var andamento;
                var nomEx;
                var exercicio;

                codigoOrgao = jQuery('#hdnUltimoOrgaoSelecionado').val();

                //nSetor = formulario.nomSetor.value;
                nomParametro = "chaveSetorPadrao";
                andamento = "padrao";
                nomEx = "exercicioPadrao";
                // exercicio = formulario.anoExercicioSetor.value;

                if (Valida()) {
                    if (exercicio == '') {
                        alertaAviso("Aguarde todos os processos concluírem.",'form','erro','<?=Sessao::getId()?>');
                    } else {
                        varsGet ="%26"+nomParametro+"="+sCodigo+"%26codProcesso=<?=$codProcesso?>%26anoExercicio=<?=$anoExercicio?>%26codOrgao="+codigoOrgao+"%26andamento="+andamento+"%26"+nomEx+"="+exercicio+"%26pagina=<?=$pagina?>";
                        varsGet += '&stDescQuestao=<?php echo "Deseja encaminhar este processo para o setor selecionado?";?>';
                        alertaQuestao("<?=CAM_PROTOCOLO;?>protocolo/processos/encaminhaProcesso.php?<?=Sessao::getId()?>", "controle" , "3"+varsGet ,"Deseja encaminhar este processo para o setor selecionado?","sn","<?=Sessao::getId()?>");
                       
                    }
                }
            }

            function Cancela()
            {
                document.frm.action = "encaminhaProcesso.php?<?=Sessao::getId()?>&controle=1";              
                document.frm.submit();
            }

            function carregaSetor()
            {
                document.frm.action = "encaminhaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso.'-'.$anoExercicio."-".$codClassificacao."-".$codAssunto?>&anoExercicio=<?=$anoExercicio?>&codClassificacao=<?=$codClassificacao?>&codAssunto=<?=$codAssunto?>&orgao=<?=$orgao?>&controle=1&flag=1&codOrgao=<?=$codOrgao?>";
                document.frm.submit();
            }
        </script>
         <table width='100%'>
            <tr>
              <td colspan='2' class='field'>
         <?php
                geraBotaoOk(1,0,1,1);
        ?>
            </td>
        </tr>
        <input type=hidden name=codClassificacao_base value='<?=$_GET["codClassificacao_base"];?>'>
        <input type=hidden name=codAssunto_base value='<?=$_GET["codAssunto_base"];?>'>
    </table>
        </form>

<?php
    }//Fim da function formEncaminhaProcesso

/***************************************************************************
/**************************************************************************/
function formEncaminhaProcessoLote()
{
?>
<script src="<?=CAM_GA;?>/javaScript/ifuncoesJs.js" type="text/javascript"></script>

        <form name='frm' action='<?=$action;?>?<?=Sessao::getId();?>' method='post' onSubmit="return false;">
        <table width='100%'>
                <tr>
                    <td colspan='2' class='alt_dados'>
                        Encaminhamento de Processo
                    </td>
                </tr>
        </table>
        <?php
            # ANTIGO
            # include(CAM_FW_LEGADO."filtrosSELegado.inc.php");

            # NOVO COMPONENTE
            $obFormulario = new Formulario;
            $obFormulario->addForm(null);

            # Instancia para o novo objeto Organograma
            $obIMontaOrganograma = new IMontaOrganograma;
            $obIMontaOrganograma->setNivelObrigatorio(1);
            $obIMontaOrganograma->geraFormulario($obFormulario);

            $obFormulario->montaHtml();
            echo $obFormulario->getHTML();

        ?>

    <script type="text/javascript">

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                var sCodigo;
                var formulario = document.frm;
                var nSetor;
                var nomParametro;
                var varsGet;
                var andamento;
                var nomEx;
                var exercicio;

                codigoOrgao = jQuery('#hdnUltimoOrgaoSelecionado').val();
                //nSetor = formulario.nomSetor.value;
                nomParametro = "chaveSetorPadrao";
                andamento = "padrao";
                nomEx = "exercicioPadrao";
                // exercicio = formulario.anoExercicioSetor.value;

                if (Valida()) {
                    // var expReg = new RegExp("[^0-9a-zA-Z]", "gi");
                    // varsGet ="%26chaveSetor="+document.frm.codMasSetor.value.replace( expReg,"_");
                    // varsGet = '&stDescQuestao=<?php echo urlencode( "Deseja encaminhar os processos selecionados para o setor?");?>;
                    // alertaQuestao("<?=CAM_PROTOCOLO;?>protocolo/processos/encaminhaProcessoLote.php?<?=Sessao::getId()?>&codProcesso=<?=$codProcesso;?>&numCgm=<?=$numCgm;?>&stChaveProcesso=<?=$stChaveProcesso;?>&codAssunto=<?=$codAssunto;?>&codClassificacao=<?=$codClassificacao;?>&resumo=<?=$resumo;?>&dataInicio=<?=$dataInicio;?>&dataTermino=<?=$dataTermino;?>", "controle" , "3"+varsGet ,"Deseja encaminhar os processos selecionados para o setor?","sn","<?=Sessao::getId()?>");

                    varsGet ="%26"+nomParametro+"="+sCodigo+"%26codOrgao="+codigoOrgao+"%26andamento="+andamento+"%26"+nomEx+"="+exercicio+"%26pagina=<?=$pagina?>";
                    varsGet += '&stDescQuestao=<?php echo "Deseja encaminhar estes processos para o setor selecionado?";?>';
                    alertaQuestao("<?=CAM_PROTOCOLO;?>protocolo/processos/encaminhaProcessoLote.php?<?=Sessao::getId()?>", "controle" , "3"+varsGet ,"Deseja encaminhar estes processos para o setor selecionado?","sn","<?=Sessao::getId()?>");
                    //document.frm.submit();
                }
            }

            function Cancela()
            {
                document.frm.action = "encaminhaProcessoLote.php?<?=Sessao::getId()?>&controle=1";
                document.frm.submit();
            }

            function carregaSetor()
            {
                document.frm.action = "encaminhaProcessoLote.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&codProcesso=<?=$codProcesso.'-'.$anoExercicio."-".$codClassificacao."-".$codAssunto?>&anoExercicio=<?=$anoExercicio?>&codClassificacao=<?=$codClassificacao?>&codAssunto=<?=$codAssunto?>&setor=<?=$setor?>&dpto=<?=$dpto?>&unidade=<?=$unidade?>&orgao=<?=$orgao?>&anoExercicioSetor=<?=$anoExercicioSetor?>&controle=1&flag=1&codOrgao=<?=$codOrgao?>&codUnidade=<?=$codUnidade?>&codDepartamento=<?=$codDepartamento?>&codSetor=<?=$codSetor?>";
                document.frm.submit();
            }
        </script>
        <table width='100%'>
            <tr>
                <td colspan='2' class='field'>
                    <?php
                        geraBotaoOk(1,0,1,1);
                    ?>
                </td>
            </tr>
        </table>
    </form>

<?php
    }//Fim da function formEncaminhaProcesso

/***************************************************************************
Exibe os dados de um processo
/**************************************************************************/
    public function exibeConsultaProcesso($processo)
    {
    $colspan = "6";

    if (is_array($processo)) {
        foreach ($processo as $chave=>$valor) {
            $$chave = $valor;
        }
        $observacoes = preg_replace("/\n/","<br>",$observacoes);
    }

    if (!empty($codProcesso) && !empty($anoExercicio)) {
        $sql = "SELECT  numcgm
                  FROM  sw_processo_interessado
                 WHERE  cod_processo = ".$codProcesso."
                   AND  ano_exercicio = '".$anoExercicio."'";

        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;

    }

?>
    <form name=frm action="consultaProcesso.php?<?=Sessao::getId()?>&pagina=<?=$pagina?>&verificador=false" method="post">
            <input type='hidden' name='codProcesso' value='<?=$codProcesso;?>'>
            <input type='hidden' name='anoExercicio' value='<?=$anoExercicio;?>'>
            <input type='hidden' name='controle' value='1'>

        <table width="100%">
            <tr>
                <td class='alt_dados' colspan='10'>
                    Dados do(s) Interessado(s)<a name="DadosDoInteressado">&nbsp;</a>
                </td>
            </tr>
    <?php if ($vinculo=="imobiliaria") { ?>
            <tr>
                <td class="label" width="30%" >
                    Inscrição Imobiliária
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
            <?=$numMatricula;?>
                </td>
            </tr>
    <?php } elseif ($vinculo=="inscricao") { ?>
            <tr>
                <td class="label" width="30%" >
                    Inscrição Econômica
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
            <?=$numInscricao;?>
                </td>
            </tr>
    <?php }

        if ($registros > 0) {
            for ($i = 1; $i <= $registros; $i++) {
                $numcgm = $conn->pegaCampo("numcgm");

                if ($_GET['mostraDados'] != 1) {
                    $mostraDados = false;
                } else {
                    $mostraDados = true;
                }
                $this->dadosContribuinte($numcgm, $mostraDados, $i);
                $conn->vaiProximo();
            }
        }
    ?>
            <tr>
                <td class='alt_dados' colspan="<?=$colspan+1?>">
                    Dados do Processo
                </td>
            </tr>
            <tr>
                <td class=label>
                    Código
                </td>
                <td class=field colspan="<?=$colspan?>">
                    <?php
                        $mascaraProcesso = pegaConfiguracao("mascara_processo", 5);
                        $codProcessoC    = $codProcesso.$anoExercicio;
                        $numCasas        = strlen($mascaraProcesso) - 1;
                        $codProcessoS    = str_pad($codProcessoC, $numCasas, "0" ,STR_PAD_LEFT);
                        $codProcessoS    = geraMascaraDinamica($mascaraProcesso, $codProcessoS);
                    ?>
                    <b><?=$codProcessoS;?></b>
                </td>
            </tr>
            <tr>
                <td class="label" width="30%">
                    Classificação/Assunto
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
<?php          $mascaraAssunto = pegaConfiguracao('mascara_assunto',5);
            $arCodClassifAssunto =  validaMascaraDinamica($mascaraAssunto, $codClassif."-".$codAssunto);
            $codClassifAssunto   = $arCodClassifAssunto[1];
?>
            <?=$codClassifAssunto?><br>
            <?=$classificacao;?><br>
            <?=$assunto;?>
                </td>
            </tr>
            
            <?php
               $centroCusto = pegaConfiguracao("centro_custo", 5);
               
               if($centroCusto=='true'){
                   $codCentroCusto = '';
                   $nomCentroCusto = '';
                   $stCentroCusto = '';
                   
                   $sSQL = "SELECT sw_processo.*
                                 , centro_custo.descricao as descricao_centro
                              FROM sw_processo
                        INNER JOIN almoxarifado.centro_custo
                                ON centro_custo.cod_centro=sw_processo.cod_centro
                             WHERE ano_exercicio = '".$anoExercicio."'
                               AND cod_processo = ".$codProcesso;
                   $dbConfig = new dataBaseLegado;
                   $dbConfig->abreBD();
                   $dbConfig->abreSelecao($sSQL);
                   $dbConfig->vaiPrimeiro();
                   while (!$dbConfig->eof()) {
                       $codCentroCusto  = $dbConfig->pegaCampo("cod_centro");
                       $nomCentroCusto  = trim($dbConfig->pegaCampo("descricao_centro"));
                       $dbConfig->vaiProximo();
                   }
                   $dbConfig->limpaSelecao();
                   $dbConfig->fechaBD();
                   
                   if($codCentroCusto!=''&&$nomCentroCusto!='')
                      $stCentroCusto = $codCentroCusto." - ".$nomCentroCusto;
                      
                   echo "
                   <tr>
                       <td class=label width='30%'>
                           Centro de Custo
                       </td>
                       <td class=field width='70%' colspan='2'>
                           ".$stCentroCusto."
                       </td>
                   </tr>
                   ";
               }
               ?>
            
            <tr>
                <td class="label" width="30%">
                    Observações
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
            <?php
                        echo $observacoes."&nbsp;";
            ?>
                </td>
            </tr>
            <tr>
                <td class="label" width="30%">
                    Assunto Reduzido
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
            <?php
                        echo $resumo."&nbsp;";
            ?>
                </td>
            </tr>
            <tr>
                <td class="label" width="30%" >
                    Situação do Processo
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>">
            <?=$nomSituacao;?>
                </td>
            </tr>
    <?php
                $select = 	"SELECT
                                U.numcgm,
                                U.username,
                                P.timestamp
                            FROM
                                sw_processo AS P,
                                administracao.usuario  AS U
                            WHERE
                                P.cod_usuario   = U.numcgm AND
                                P.ano_exercicio = '".$_GET['anoExercicio']."' AND
                                P.cod_processo = ".$codProcesso;
               
                /*
                    * Adicionado no Select acima o ano_exercicio, pois não estava sendo filtrado.
                */
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $dbConfig->abreSelecao($select);
                $numCgmI      = $dbConfig->pegaCampo("numcgm");
                $nomUsuario   = $dbConfig->pegaCampo("username");
                $dataInclusao = $dbConfig->pegaCampo("timestamp");
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
                $arrData      = explode(" ", $dataInclusao);
                $vet          = explode("-", $arrData[0]);
                $dataInclusao = $vet[2]."/".$vet[1]."/".$vet[0];
    ?>
            <tr>
                <td class=label>
                    Data da Inclusão
                </td>
                <td class=field colspan="<?=$colspan?>">
            <?=$dataInclusao?>
                </td>
            </tr>
            <tr>
                <td class=label>
                    Usuário que Incluiu
                </td>
                <td class=field colspan="<?=$colspan?>">
            <?=$numCgmI?> - <?=$nomUsuario?>
                </td>
            </tr>

<?php
            // INICIO Processo Arquivado (temporário ou definitivo)
            if ($codSituacao == '5' || $codSituacao == '9') {

                if (!empty($codProcesso) && !empty($_GET['anoExercicio'])) {

                    $sqlRecuperaArquivador = "
                        SELECT
                            numcgm,
                            nom_cgm,
                            localizacao
                        FROM
                            sw_processo_arquivado,
                            sw_cgm
                        WHERE
                            sw_processo_arquivado.cgm_arquivador = sw_cgm.numcgm AND
                            sw_processo_arquivado.ano_exercicio = '".$_GET['anoExercicio']."' AND
                            sw_processo_arquivado.cod_processo = ".$codProcesso;

                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($sqlRecuperaArquivador);
                    $numArquivador = $dbConfig->pegaCampo("numcgm");
                    $nomArquivador = $dbConfig->pegaCampo("nom_cgm");
                    $localizacaoFisica = $dbConfig->pegaCampo("localizacao");
                    $dbConfig->limpaSelecao();
                    $dbConfig->fechaBd();
                }
?>
            <!-- ARQUIVADOR -->
            <tr>
                <td class=label>
                    Usuário que Arquivo
                </td>
                <td class=field colspan="<?=$colspan?>">
            <?=$numArquivador?> - <?=$nomArquivador?>
                </td>
            </tr>

            <tr>
                <td class=label>
                    Texto Complementar
                </td>
                <td class=field colspan="<?=$colspan?>">
                    <?= $textoComplementar ?>
                </td>
            </tr>

            <tr>
                <td class=label>
                    Localização Física do Arquivamento
                </td>
                <td class=field colspan="<?=$colspan?>">
                    <?= $localizacaoFisica ?>
                </td>
            </tr>

<?php
                } // FIM Processo Arquivado (temporário ou definitivo)

                $select  = " SELECT                                             \n";
                $select .= "     AP.nom_atributo,                               \n";
                $select .= "     AAV.valor                                      \n";
                $select .= " FROM                                               \n";
                $select .= "     sw_assunto_atributo_valor AS AAV,             \n";
                $select .= "     sw_atributo_protocolo AS AP                   \n";
                $select .= " WHERE                                              \n";
                $select .= "     AAV.cod_processo      = $codProcesso     AND   \n";
                $select .= "     AAV.exercicio         = '".$anoExercicio."'    AND   \n";
                $select .= "     AAV.cod_assunto       = $codAssunto      AND   \n";
                $select .= "     AAV.cod_classificacao = $codClassif      AND   \n";
                $select .= "     AAV.cod_atributo      = AP.cod_atributo        \n";
                $select .= " ORDER BY AP.nom_atributo                           \n";
              
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $dbConfig->abreSelecao($select);
                if ($dbConfig->numeroDeLinhas > 0) {
                    echo "<tr>
                            <td class=alt_dados colspan=".($colspan+1).">
                                Atributos de Assunto de Processo
                            </td>
                        </tr>
                        <tr>
                            <td class=labelcenter>
                                Nome do atributo
                            </td>
                            <td class=labelcenter colspan=".$colspan.">
                                Valor do atributo
                            </td>
                        </tr>";
                }
                while (!($dbConfig->eof())) {
                    echo "<tr>";
                    echo "	<td class=show_dados>".$dbConfig->pegaCampo("nom_atributo")."</td>";
                    echo "	<td class=show_dados colspan=".$colspan.">".$dbConfig->pegaCampo("valor")."</td>";
                    echo "</tr>";
                    $dbConfig->vaiProximo();
                }

                $this->exibeAtributosProcesso($codProcesso, $anoExercicio);

                    $select = 	"SELECT
                                    DA.cod_documento,
                                    D.nom_documento
                                FROM
                                    sw_documento         AS D,
                                    sw_assunto           AS A,
                                    sw_documento_assunto AS DA
                                WHERE
                                    DA.cod_documento     = D.cod_documento     AND
                                    DA.cod_assunto       = A.cod_assunto       AND
                                    DA.cod_classificacao = A.cod_classificacao AND
                                    A.cod_assunto        = ".$codAssunto."     AND
                                    A.cod_classificacao  = ".$codClassif."
                                ORDER BY D.nom_documento";

                    //Order by adicionado por Cristiano Sperb               
                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($select);
                    if ($dbConfig->numeroDeLinhas > 0) {
                        echo "<tr>
                                <td class=alt_dados  colspan=".($colspan+1).">
                                    Documentos de processo
                                </td>
                            </tr>
                            <tr>
                                <td class=labelcenter>
                                    Nome do documento
                                </td>
                                <td class=labelcenter width='10%'>
                                    Situação
                                </td>
                                <td class=labelcenter colspan=".$colspan.">
                                    Ícones de imagem
                                </td>
                            </tr>";
                    }
                    $nomAnt = "";
                    while (!$dbConfig->eof()) {
                        $codDoc = $dbConfig->pegaCampo("cod_documento");
                        $nomDoc = $dbConfig->pegaCampo("nom_documento");
                        $select2 = 	"SELECT
                                        dp.cod_documento,
                                        d.nom_documento
                                    FROM
                                        sw_documento_processo as dp,
                                        sw_documento          as d
                                    WHERE
                                        dp.cod_processo  = ".$codProcesso." AND
                                        dp.exercicio  = '".$anoExercicio."' AND
                                        dp.cod_documento = d.cod_documento  AND
                                        dp.cod_documento = ".$codDoc;
                        $dbConfig2 = new databaseLegado;
                        $dbConfig2->abreBd();
                        $dbConfig2->abreSelecao($select2);
                        $entregue = "&nbsp;";
                        if ($dbConfig2->numeroDeLinhas > 0) {
                            $entregue = "Entregue";
                        } else {
                            $entregue = "Não entregue";
                        }
                        if ($nomAnt != $nomDoc) {
                            echo "<tr><td class=show_dados>".$nomDoc."</td>
                                        <td class=show_dados>".$entregue."</td>
                                        <td class=show_dados colspan=".$colspan.">
                                            &nbsp;\n";
                        }

                        $selectCopia = 	"SELECT
                                            imagem,
                                            cod_copia,
                                            anexo
                                        FROM
                                            sw_copia_digital
                                        WHERE
                                            cod_processo = ".$codProcesso." AND
                                            exercicio = '".$anoExercicio."' AND
                                            cod_documento = ".$codDoc;
                        $dbCopia = new databaseLegado;
                        $dbCopia->abreBd();
                        $dbCopia->abreSelecao($selectCopia);
?>
  <script>
    function winClose()
    {
        downloadWindow.close();
    }

     function abreDownload(sessao, codCopia, codProcesso, anoExercicio, codDoc)
     {
         var sArq = 'anexo.php?'+sessao+'&codCopia='+codCopia+'&codProcesso='+codProcesso+'&anoExercicio='+anoExercicio+'&codDoc='+codDoc;
         downloadWindow = window.open(sArq,'width=150px,height=150px,resizable=0,scrollbars=0','left=1,top=1');
     self.setTimeout ('winClose()', 1000);
     }

  </script>
<?php
                        while (!($dbCopia->eof())) {
                            $tipoAnexo = $dbCopia->pegaCampo("imagem");
                            $anexo = $dbCopia->pegaCampo("anexo");
                            $codCopia = $dbCopia->pegaCampo("cod_copia");
                            if ($tipoAnexo == "t") {
                                $anexoImg = pathinfo($anexo);
?>
                                <a href="javascript:abrePopUpAnexo('exibeAnexo.php','<?=$anexoImg["basename"];?>','<?=Sessao::getId();?>','800','550');"><img src="<?=CAM_FW_IMAGENS."imagem.png";?>" border=0></a>&nbsp;
<?php
                                echo "\n";
                            } elseif ($tipoAnexo == "f") {
                                $anexoDoc = pathinfo($anexo);
                                echo "

                                    <a href=# onClick=' abreDownload(\"Sessao::getId()\", \"$codCopia\", \"$codProcesso\", \"$anoExercicio\", \"$codDoc\")'><img src='".CAM_FW_IMAGENS."outDoc.png' border=0></a>&nbsp;\n
                                ";
                            }
                            $dbCopia->vaiProximo();
                        }
                        $dbConfig->vaiProximo();
                        $nomAnt = $nomDoc;

                        echo "</td></tr>";
                    }
                    $dbConfig->limpaSelecao();
                    $dbConfig->fechaBd();

//********************************************************************
// busca PROCESSOS EM APENSO
//********************************************************************

            // define parametros para expandir/retrair a lista
            $imagem = "";
            $aux = $_GET['mostraApensado'];

            if ($aux != 1) {
                $imagem = CAM_FW_IMAGENS."botao_expandir.png";
                $title  = "Expandir dados dos processos em apenso";
                $mostraApensado = "1";
                $desc = "desc";
            } else {
                $imagem = CAM_FW_IMAGENS."botao_retrair.png";
                $title  = "Retrair dados dos processos em apenso";
                $mostraApensado = "";
                $desc = "";
            }
?>
            <table width="100%">
            <tr>
                <td class=alt_dados colspan='<?=$colspan+1?>' width="30%">
                    Processos em Apenso <a name="ProcessosEmApenso"></a>
                </td>
                <td class="alt_dados" colspan="2">
                    <a href="JavaScript: DadosApensado();">
                        <img align="right" src="<?=$imagem?>" border="0" name="imagemExpande" title="<?=$title?>">
                    </a>
                </td>
            </tr>
            </table>
<?php
        $paginacao = new paginacaoLegada;
        $count = $paginacao->contador();

        $select = "
            SELECT
                cod_processo_filho,
                exercicio_filho,
                timestamp_apensamento,
                timestamp_desapensamento
            FROM
                sw_processo_apensado
            WHERE
                cod_processo_pai = ".$codProcesso." AND
                exercicio_pai    = '".$anoExercicio."'
            ORDER BY
                timestamp_apensamento ".$desc.",
                cod_processo_filho
            ";

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);

        if ($dbConfig->numeroDeLinhas > 0) {
?>
            <table width='100%' id="processos">
            <tr>
                <td class="labelcenterCabecalho" width='2%'>
                    &nbsp;
                </td>
                <td class="labelcenterCabecalho" width='1%'>
                    Código
                </td>
                <td class="labelcenterCabecalho" width='15%'>
                    Classificação
                </td>
                <td class="labelcenterCabecalho" width='15%'>
                    Assunto
                </td>
                <td class="labelcenterCabecalho" width='25%'>
                    Interessado
                </td>
                <td class="labelcenterCabecalho" width='20%'>
                    Data de apensamento
                </td>
                <td class="labelcenterCabecalho" width='20%'>
                    Data de desapensamento
                </td>
            </tr>
<?php
            $exit_While = 0;

            while (!($dbConfig->eof()) and $exit_While != 1) {

                $dt_tmp = "";
                $dt_tmp2 = "";
                $hr_tmp = "";
                $hr_tmp2 = "";

                $codFilho           = $dbConfig->pegaCampo("cod_processo_filho");
                $exercicioFilho     = $dbConfig->pegaCampo("exercicio_filho");
                $dataApensamento    = $dbConfig->pegaCampo("timestamp_apensamento");
                $dataDesapensamento = $dbConfig->pegaCampo("timestamp_desapensamento");

                $dt_tmp = timestampToBr($dataApensamento,"d");
                $hr_tmp = timestampToBr($dataApensamento,"h");
                $dataApensamento = $dt_tmp." (".$hr_tmp.")";

                if ($dataDesapensamento != "") {
                    $dt_tmp2 = timestampToBr($dataDesapensamento,"d");
                    $hr_tmp2 = timestampToBr($dataDesapensamento,"h");
                    $dataDesapensamento = $dt_tmp2." (".$hr_tmp2.")";
                }

                $selectFilho =	"
                    SELECT
                        C.nom_classificacao,
                        A.nom_assunto,
                        G.nom_cgm
                    FROM
                        sw_classificacao AS C,
                        sw_assunto       AS A,
                        sw_cgm           AS G,
                        sw_processo      AS P,
                        sw_processo_interessado
                    WHERE
                        P.cod_classificacao = C.cod_classificacao AND
                        P.cod_classificacao = A.cod_classificacao AND
                        P.cod_assunto       = A.cod_assunto       AND
                        sw_processo_interessado.ano_exercicio = P.ano_exercicio AND
                        sw_processo_interessado.cod_processo  = P.cod_processo  AND
                        sw_processo_interessado.numcgm = G.numcgm               AND
                        P.cod_processo      = ".$codFilho."       AND
                        P.ano_exercicio     = '".$exercicioFilho."' " ;

                $dbFilho = new databaseLegado;
                $dbFilho->abreBd();
                $dbFilho->abreSelecao($selectFilho);

                $nomClassFilho   = $dbFilho->pegaCampo("nom_classificacao");
                $nomAssuntoFilho = $dbFilho->pegaCampo("nom_assunto");
                $nomInterFilho   = $dbFilho->pegaCampo("nom_cgm");

                $codFilhoC = $codFilho.$exercicioFilho;
                $numCasas     = strlen($mascaraProcesso) - 1;
                $codFilhoM = str_pad($codFilhoC, $numCasas, "0" ,STR_PAD_LEFT);
                $codFilhoM = geraMascaraDinamica($mascaraProcesso, $codFilhoM);

?>
                <tr>
                    <td class=show_dados> <?=$count++;?> </td>

                    <td class=show_dados_right> <?=$codFilhoM;?> </td>
                    <td class=show_dados> <?=$nomClassFilho;?> </td>
                    <td class=show_dados> <?=$nomAssuntoFilho;?> </td>
                    <td class=show_dados> <?=$nomInterFilho;?> </td>
                    <td class=show_dados_center> <?=$dataApensamento;?> </td>
                    <td class=show_dados_center> <?=$dataDesapensamento;?> &nbsp;</td>
                </tr>
<?php
                if ($mostraApensado == 1) {
                    $exit_While = 1;
                }

                $dbConfig->vaiProximo();
            }
?>
            </table>
<?php
        } else {
?>
            <table width='100%'>
            <tr>
                <td class="show_dados" width='5%'>
                    Nenhum Processo em Apenso
                </td>
            </tr>
            </table>
<?php
        }

        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

//********************************************************************
// busca EM APENSO A PROCESSOS
//********************************************************************

        // define parametros para expandir/retrair a lista
        $imagem = "";
        $aux = $_GET['mostraEmApenso'];

        if ($aux != 1) {
            $imagem = CAM_FW_IMAGENS."botao_expandir.png";
            $title  = "Expandir dados dos processos em apenso";
            $mostraEmApenso = "1";
            $desc = "desc";
        } else {
            $imagem = CAM_FW_IMAGENS."botao_retrair.png";
            $title  = "Retrair dados dos processos em apenso";
            $mostraEmApenso = "";
            $desc = "";
        }
?>
        <table width="100%" id="processos">
        <tr>
            <td class="alt_dados" colspan='<?=$colspan+1?>' width="30%">
                Em Apenso a Processos <a name="EmApensoAProcessos"></a>
            </td>
            <td class="alt_dados" colspan="2">
                <a href="JavaScript: DadosEmApenso();">
                    <img align="right" src="<?=$imagem?>" border="0" name="imagemExpande" title="<?=$title?>">
                </a>
            </td>
        </tr>
        </table>
<?php
        $paginacao = new paginacaoLegada;
        $count = $paginacao->contador();

        $select = "
            SELECT
                cod_processo_pai,
                exercicio_pai,
                timestamp_apensamento,
                timestamp_desapensamento
            FROM
                sw_processo_apensado
            WHERE
                cod_processo_filho = ".$codProcesso." AND
                exercicio_filho    = '".$anoExercicio."'
            ORDER BY
                timestamp_apensamento ".$desc.",
                cod_processo_pai
            ";

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);

        if ($dbConfig->numeroDeLinhas > 0) {
?>
            <table width='100%' id="processos">
            <tr>
                <td class="labelcenterCabecalho" width='2%'>
                    &nbsp;
                </td>
                <td class="labelcenterCabecalho" width='1%'>
                    Código
                </td>
                <td class="labelcenterCabecalho" width='15%'>
                    Classificação
                </td>
                <td class="labelcenterCabecalho" width='15%'>
                    Assunto
                </td>
                <td class="labelcenterCabecalho" width='25%'>
                    Interessado
                </td>
                <td class="labelcenterCabecalho" width='20%'>
                    Data de apensamento
                </td>
                <td class="labelcenterCabecalho" width='20%'>
                    Data de desapensamento
                </td>
            </tr>

<?php
            $exit_While = 0;

            while (!($dbConfig->eof()) and $exit_While != 1) {

                $dt_tmp = "";
                $dt_tmp2 = "";
                $hr_tmp = "";
                $hr_tmp2 = "";

                $codPai             = $dbConfig->pegaCampo("cod_processo_pai");
                $exercicioPai       = $dbConfig->pegaCampo("exercicio_pai");
                $dataApensamento    = $dbConfig->pegaCampo("timestamp_apensamento");
                $dataDesapensamento = $dbConfig->pegaCampo("timestamp_desapensamento");

                $dt_tmp = timestampToBr($dataApensamento,"d");
                $hr_tmp = timestampToBr($dataApensamento,"h");
                $dataApensamento = $dt_tmp." (".$hr_tmp.")";

                if ($dataDesapensamento != "") {
                    $dt_tmp2 = timestampToBr($dataDesapensamento,"d");
                    $hr_tmp2 = timestampToBr($dataDesapensamento,"h");
                    $dataDesapensamento = $dt_tmp2." (".$hr_tmp2.")";
                }

                $selectPai =	"
                    SELECT
                        C.nom_classificacao,
                        A.nom_assunto,
                        G.nom_cgm
                    FROM
                        sw_classificacao AS C,
                        sw_assunto       AS A,
                        sw_cgm           AS G,
                        sw_processo      AS P,
                        sw_processo_interessado
                    WHERE
                        P.cod_classificacao = C.cod_classificacao AND
                        P.cod_classificacao = A.cod_classificacao AND
                        P.cod_assunto       = A.cod_assunto       AND

                        sw_processo_interessado.ano_exercicio = P.ano_exercicio  AND
                        sw_processo_interessado.cod_processo  = P.cod_processo  AND
                        sw_processo_interessado.numcgm = G.numcgm            AND

                        P.cod_processo      = ".$codPai."         AND
                        P.ano_exercicio     = '".$exercicioPai."'
                ";
                $dbPai = new databaseLegado;
                $dbPai->abreBd();
                $dbPai->abreSelecao($selectPai);

                $nomClassPai   = $dbPai->pegaCampo("nom_classificacao");
                $nomAssuntoPai = $dbPai->pegaCampo("nom_assunto");
                $nomInterPai   = $dbPai->pegaCampo("nom_cgm");

                $codPaiC = $codPai.$exercicioPai;
                $numCasas= strlen($mascaraProcesso) - 1;
                $codPaiM = str_pad($codPaiC, $numCasas, "0" ,STR_PAD_LEFT);
                $codPaiM = geraMascaraDinamica($mascaraProcesso, $codPaiM);
?>
                <tr>
                    <td class="show_dados_center_bold"> <?=$count++;?> </td>
                    <td class="show_dados_right"> <?=$codPaiM;?> </td>
                    <td class="show_dados"> <?=$nomClassPai;?> </td>
                    <td class="show_dados"> <?=$nomAssuntoPai;?> </td>
                    <td class="show_dados"> <?=$nomInterPai;?> </td>
                    <td class="show_dados_center"> <?=$dataApensamento;?> </td>
                    <td class="show_dados_center"> <?=$dataDesapensamento;?> &nbsp;</td>
                </tr>
<?php
                if ($mostraEmApenso == 1) {
                    $exit_While = 1;
                }

                $dbConfig->vaiProximo();
            }
        } else {
?>
            <table width='100%' id="processos">
            <tr>
                <td class="show_dados" width='5%'>
                    Apenso a nenhum processo
                </td>
            </tr>
<?php
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
        ?>
        <script>zebra('processos','zb');</script>
   <?php }

     //Fim da function exibeConsultaProcesso()

    public function exibeAndamentosProcesso($codProcesso,$anoExercicio, $codSetor)
    {
        $colspan = "9";
        $p = new processosLegado;
        $mascaraSetor = pegaConfiguracao('mascara_setor',2);
        $andamento = $p->pegaDadosAndamento($codProcesso,$anoExercicio);
?>

        <script type="text/javascript">
            function abreDespacho(codProcesso,anoExercicio,codAndamento,nomSetor,nomUsuario,chave)
            {
                var x = 180;
                var y = 110;
                nomSetor = escape(nomSetor);
                nomUsuario = escape(nomUsuario);
                var sArq = 'consultaProcessoDespacho.php?<?=Sessao::getId();?>&codProcesso='+codProcesso+'&anoExercicio='+anoExercicio+'&codAndamento='+codAndamento+'&nomSetor='+nomSetor+'&nomUsuario='+nomUsuario+'&chave='+chave;
                mensagem = window.open(sArq,'','width=800px,height=550px,resizable=1,scrollbars=1,left='+x+',top='+y);

            }
        </script>
            <input type='hidden' name='codProcesso' value='<?=$codProcesso;?>'>
            <input type='hidden' name='anoExercicio' value='<?=$anoExercicio;?>'>
            <input type='hidden' name='controle' value='0'>

            <table width="100%">
<?php
                $imagem = "";
                $aux = $_GET['mostraTramite'];

                if ($aux != 1) {
                    $imagem = CAM_FW_IMAGENS."botao_expandir.png";
                    $title  = "Expandir dados dos tramites dos processos";
                } else {
                    $imagem = CAM_FW_IMAGENS."botao_retrair.png";
                    $title  = "Retrair dados dos tramites dos processos";
                    $mostraTramites = 1;
                }
?>
            <tr>
                <td class='alt_dados' width="30%" colspan="<?=$colspan?>">
                    Trâmites do Processo <a name="TramitesDoProcesso"></a>
                </td>
                <td class=alt_dados colspan=2>
                    <a href="JavaScript: DadosTramites();">
                        <img align="right" src="<?=$imagem?>" border="0" name="imagemExpande" title="<?=$title?>">
                    </a>
                </td>
            </tr>
            </table>

            <table width="100%" id="processos">
            <tr>
                <td class="labelcenterCabecalho" rowspan="2" width="1%">&nbsp;</td>
                <td class="labelcenterCabecalho" rowspan="2" width="1%">Ordem</td>
                <td class="labelcenterCabecalho" rowspan="2" width="25%">Código Estrutural / Descrição</td>
                <td class="labelcenterCabecalho" colspan="2" width="16%">Recebimento</td>
                <td class="labelcenterCabecalho" colspan="2" width="16%">Encaminhamento</td>
                <td class="labelcenterCabecalho" rowspan="2" width="5%">Despacho</td>
            </tr>

            <tr>
                <td class="labelcenterCabecalho">Data</td>
                <td class="labelcenterCabecalho">Usuário</td>
                <td class="labelcenterCabecalho">Data</td>
                <td class="labelcenterCabecalho">Usuário</td>
            </tr>
<?php

            $paginacao = new paginacaoLegada;
            $count = $paginacao->contador();

        /*****************************************************************************/
        // grava os campos do vetor ANDAMENTO retornados da funcao 'pegaDadosAndamento'
        // e atribui ao vetor AND.
        /*****************************************************************************/

            if (is_array($andamento)) {

                foreach ($andamento as $and) {

                    $prox_andamento = "";
                    $dt_bd = "";
                    $dt_bd2 = "";
                    $data = "";
                    $data2 = "";
                    $hora = "";
                    $hora2 = "";
                    $cod_recibo = "";

        /*****************************************************************************/
        // seleciona o ANDAMENTO que sera exibido quando a lista nao estiver expandia
        /*****************************************************************************/

                    if ($mostraTramites != 1 and !$max_andamento) {
                        // seleciona o maior codigo de Andamento
                        $sql_max = "
                            SELECT
                                max(cod_andamento) as max_and
                            FROM
                                sw_andamento
                            WHERE
                                cod_processo = '".$and[codProcesso]."'
                                AND ano_exercicio = '".$and[anoExercicio] ."'
                        ";

                        $dbConfig = new databaseLegado;
                        $dbConfig->abreBd();
                        $dbConfig->abreSelecao($sql_max);

                        // monta a data de recebimento
                        if ($dbConfig->numeroDeLinhas > 0) {
                            $max_andamento = $dbConfig->pegaCampo("max_and");
                        }
                    }

        /*****************************************************************************/
        // verifica se o andamento que esta sendo verficado possui RECEBIMENTO
        /*****************************************************************************/
                    $sql_rec = "
                        SELECT
                            timestamp
                        FROM
                            sw_recebimento
                        WHERE
                            cod_andamento = '".$and[codAndamento]."'
                            AND cod_processo = '".$and[codProcesso]."'
                            AND ano_exercicio = '".$and[anoExercicio]."'
                    ";

                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($sql_rec);

                    // monta a data de recebimento
                    if ($dbConfig->numeroDeLinhas > 0) {
                        $dt_bd = $dbConfig->pegaCampo("timestamp");

                        // monta a data de recebimento
                        if (strlen($dt_bd) > 10) {
                            $data = timestampToBr($dt_bd,"d");
                            $hora = timestampToBr($dt_bd,"h");
                            $and[dt_recebimento] = $data." (".$hora.")";

                            // seleciona usuario que recebeu
                            $sql_user = "
                                SELECT
                                    cod_usuario
                                FROM
                                    sw_assinatura_digital
                                WHERE
                                    cod_andamento = '".$and[codAndamento]."'
                                    AND cod_processo = '".$and[codProcesso]."'
                                    AND ano_exercicio = '".$and[anoExercicio]."'
                            ";

                            $dbConfig2 = new databaseLegado;
                            $dbConfig2->abreBd();
                            $dbConfig2->abreSelecao($sql_user);

                            // busca o nome do usuario a partir do codUsuario encontrado
                            if ($dbConfig2->numeroDeLinhas > 0) {
                                $and[coduser_recebimento] = $dbConfig2->pegaCampo("cod_usuario");
                                $and[user_recebimento] = pegaDado("username","administracao.usuario","Where numcgm = '".$and[coduser_recebimento]."'");
                                $and[nom_recebimento] = pegaDado("nom_cgm", "public.sw_cgm", "Where numcgm =".$and[coduser_recebimento]."");
                                $dbConfig2->limpaSelecao();

                            } else {
                            // verifica se o registro se encontra na tabela recibo impresso
                                $cod_recibo = pegaDado("cod_recibo","sw_recibo_impresso","Where cod_andamento = '".$and[codAndamento]."' and cod_processo = '".$and[codProcesso]."' and ano_exercicio = '".$and[anoExercicio]."'");
                                if ($cod_recibo > 0) {
                                    $and[user_recebimento] = "offline";
                                }
                            }

                        } else {
                            $and[dt_recebimento] = "Valor Inválido";
                        }
                    } else {

                        $and[dt_recebimento] = "&nbsp;";
                    }

                    $dbConfig->limpaSelecao();

        /*****************************************************************************/
        // verifica se o andamento que esta sendo verficado possui ENCAMINHAMENTO
        /*****************************************************************************/

                    //incrementa o codAndamento atual para buscar o prox Andamento
                    $prox_andamento = $and[codAndamento];
                    $prox_andamento++;

                    $sql_enc = "
                        SELECT
                            timestamp,
                            cod_usuario
                        FROM
                            sw_andamento
                        WHERE
                            cod_andamento = '".$prox_andamento."'
                            AND cod_processo = '".$and[codProcesso]."'
                            AND ano_exercicio = '".$and[anoExercicio]."'
                    ";

                    $dbConfig = new databaseLegado;
                    $dbConfig->abreBd();
                    $dbConfig->abreSelecao($sql_enc);

                    // monta a data de recebimento
                    if ($dbConfig->numeroDeLinhas > 0) {
                        $dt_bd2 = $dbConfig->pegaCampo("timestamp");
                        $and[coduser_encaminhamento] = $dbConfig->pegaCampo("cod_usuario");

                        // monta a data de recebimento
                        if (strlen($dt_bd2) > 10) {
                            $data2 = timestampToBr($dt_bd2,"d");
                            $hora2 = timestampToBr($dt_bd2,"h");
                            $and[dt_encaminhamento] = $data2." (".$hora2.")";
                            $and[user_encaminhamento] = pegaDado("username","administracao.usuario","Where numcgm = '".$and[coduser_encaminhamento]."'");
                            $and[nom_encaminhamento] = pegaDado("nom_cgm", "public.sw_cgm","Where numcgm = '".$and[coduser_encaminhamento]."'");
                        } else {
                            $and[dt_encaminhamento] = "Valor Inválido";
                        }
                    } else {
                        $and[dt_encaminhamento] = "&nbsp;";
                        $and[user_encaminhamento] = "&nbsp;";
                    }
                    $dbConfig->limpaSelecao();

            if (
                    ($mostraTramites == 1) or
                    ($mostraTramites != 1 and $max_andamento == $and[codAndamento])
                ) {

                    // mascara o codigo do Setor
                    $stCodSetor = $and[codOrgao].".".$and[codUnidade].".".$and[codDpto].".".$and[codSetor]."/".$and[anoExercicioSetor];
                    $arCodSetor = validaMascara($mascaraSetor,$stCodSetor);
?>
                <tr>
                    <td class="show_dados_center_bold"><?php echo $count++; ?></td>
                    <td class="show_dados_right"><?=$and[codAndamento];?></td>
                    <td class="show_dados"><?=$and['chaveOrgao'];?> - <?=$and['nomOrgao'];?></td>
                    <td class="show_dados_center"><?=$and[dt_recebimento];?></td>
                    <td title="<?=trim($and[nom_recebimento]);?>"  class="show_dados">&nbsp;<?=$and[user_recebimento];?></td>
                    <td class="show_dados_center"><?=$and[dt_encaminhamento];?></td>
                    <td title="<?=trim( $and[nom_encaminhamento]);?>" class="show_dados"><?=$and[user_encaminhamento]?></td>
                    <td class="show_dados">
<?php
                        if ($despachos = $p->pegaDadosDespacho($codProcesso,$anoExercicio,$and[codAndamento])) {
?>
                            <input type='button' onClick="abreDespacho('<?=$codProcesso;?>','<?=$anoExercicio;?>','<?=$and[codAndamento];?>','<?=$and[nomSetor];?>','<?=$desp[nomUsuario];?>','<?=$chave;?>');" name='verDespacho' value='Ver despacho'>
<?php
                        } else {
                            echo "&nbsp;";
                        }
?>
                    </td>
                </tr>
<?php
                }
            }
        }
?>
        </table>
        <table width="100%">
            <tr>
                <td class="field" colspan="<?=$colspan?>">
                    <input type="button" name="voltar" value="Voltar"
                    onclick="javascript: paginando();">
                </td>
            </tr>
        </table>
        </form>
        <script>zebra('processos','zb');</script>
<?php

    }//Fim da function exibeAndamentosProcesso

    public function exibeAtributosProcesso($inCodProcesso,$chAnoExercicio)
    {
        $stSQL ="SELECT                                                                               \n";
        $stSQL.="    sw_atributo_protocolo.nom_atributo                                               \n";
        $stSQL.="    ,sw_processo_atributo.indexavel                                                  \n";
        $stSQL.="    ,sw_processo_atributo.leitura                                                    \n";
        $stSQL.="    ,sw_processo_atributo_valor.valor                                                \n";
        $stSQL.="FROM                                                                                 \n";
        $stSQL.="    sw_processo_atributo,                                                            \n";
        $stSQL.="    sw_processo_atributo_valor,                                                      \n";
        $stSQL.="    sw_atributo_protocolo                                                            \n";
        $stSQL.="WHERE                                                                                \n";
        $stSQL.="    sw_processo_atributo.cod_atributo=sw_atributo_protocolo.cod_atributo             \n";
        $stSQL.="    AND sw_processo_atributo.cod_atributo=sw_processo_atributo_valor.cod_atributo    \n";
        $stSQL.="    AND sw_processo_atributo_valor.cod_processo=".$inCodProcesso."                   \n";
        $stSQL.="    AND sw_processo_atributo_valor.ano_exercicio= '".$chAnoExercicio."'              \n";
        $stSQL.="ORDER BY sw_atributo_protocolo.cod_atributo                                          \n";

        $dbConfig = new databaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($stSQL);

        // monta a data de recebimento
       if ($dbConfig->numeroDeLinhas > 0) {
?>
        <table width='100%' id="processo">
            <tr>
                <td class="alt_dados" colspan="2">
                    Atributos de Processo
                </td>
            </tr>
<?php
        while (!$dbConfig->eof()) {
?>
                <tr>
                    <td class="label" width="30%">
                        <?=$dbConfig->pegaCampo("nom_atributo");?>
                    </td>
                    <td class="field" >
                        <?=$dbConfig->pegaCampo("valor");?>
                    </td>
                </tr>
<?php
            $dbConfig->vaiProximo();
        }
?>
        </table>
         <script>zebra('processos','zb');</script>
<?php
        }
    }

/***************************************************************************
Monta uma tabela com os dados do contribuinte ('sw_cgm')
/**************************************************************************/
    public function dadosContribuinte($numCgm, $mostraDados = true, $i = "")
    {
        $colspan = "6";

        if ($numCgm>0) {
            if(!$numCgm = pegaDado("numcgm","sw_cgm","Where numcgm = '".$numCgm."'"))

                return false;
            include_once '../../../framework/legado/cgmLegado.class.php'; //Insere a classe que manipula os dados do cgm
            $cgm = new cgmLegado;
            $dadosCgm = $cgm->pegaDadosCgm($numCgm);

            //Grava como variável o nome da chave do vetor com o seu respectivo valor
            foreach ($dadosCgm as $campo=>$valor) {
                $$campo = trim($valor);
            }
        }

        $imagem = "";

    if ($numCgm == $_REQUEST['tdId']) {
        if ($_GET['mostraDados'] != 1) {
                $imagem = CAM_FW_IMAGENS."botao_expandir.png";
                $title  = "Expandir dados do interessado";
                $mostraDados = false;
        } else {
                $imagem = CAM_FW_IMAGENS."botao_retrair.png";
                $title  = "Retrair dados do interessado";
                $mostraDados = true;
        }

        if ($_REQUEST['tdId'] != Sessao::read('tdId')) {
            $imagem = CAM_FW_IMAGENS."botao_retrair.png";
            $title  = "Retrair dados do interessado";
            $mostraDados = true;
            // Guarda o último ID visualizado.
            Sessao::write('tdId', $_REQUEST['tdId']);
        }

    } else {
        $mostraDados = false;
        $imagem = CAM_FW_IMAGENS."botao_expandir.png";
        $title  = "Expandir dados do interessado";
    }

?>
            <tr>
                <td class="label" width="30%">
                    Interessado <?=$i;?>
                </td>
                <td class="field" width="70%" colspan="<?=$colspan?>" valign="middle">
                    <a href="JavaScript:DadosInteressado(<?=$numCgm;?>);">
                        <img align="right" src="<?=$imagem?>" border="0" name="imagemExpande" title="<?=$title?>">
                    </a>
                    <?=$numCgm;?> - <?=$nomCgm;?>
                </td>
            </tr>
    <?php
        if ($mostraDados == true) {
            $style = (($numCgm == $_REQUEST['tdId']) ? "inline-table" : "none");

    ?>
            <tr>
                <td id="td_<?=$numCgm;?>" colspan="6" width="100%">
                    <table style="display:<?=$style;?>; width:100%;">
                        <?php
                            if (pegaDado( "nom_fantasia", "sw_cgm_pessoa_juridica", " where numcgm = ".$numCgm ) != "") {
                        ?>
                        <span class='itemText'></span>
                        <tr>
                            <td class="label" width="30%">
                                Nome Fantasia
                            </td>
                            <td class="field" width="70%" colspan="<?=$colspan?>">
                                <?php
                                echo pegaDado( "nom_fantasia", "sw_cgm_pessoa_juridica", " where numcgm = ".$numCgm );
                                ?>&nbsp;
                            </td>
                        </tr>
                        <?php
                            }
                        ?>
                        <tr>
                            <td class="label" width="30%">Logradouro</td>
                            <td class="field" colspan="<?=$colspan?>" width="70%">
                            <?=$tipoLogradouro;?>&nbsp;<?=$logradouro;?>&nbsp;<?=$numero;?>&nbsp;<?=$complemento;?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Estado</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$estado;?></td>
                        </tr>
                        <tr>
                            <td class="label">Cidade</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?=$municipio;?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Bairro</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$bairro;?></td>
                        </tr>
                        <tr>
                            <td class="label">CEP</td>
                            <td class="field" colspan="<?=$colspan?>"><?php echo formataCep($cep); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Logradouro Correspondência</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?=$tipoLogradouroCorresp;?>&nbsp;<?=$logradouroCorresp;?>&nbsp;<?=$numeroCorresp;?>&nbsp;<?=$complementoCorresp;?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Estado Correspondência</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$estadoCorresp;?></td>
                        </tr>
                        <tr>
                            <td class="label">Cidade Correspondência</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$municipioCorresp;?></td>
                        </tr>
                        <tr>
                            <td class="label">Bairro Correspondência</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$bairroCorresp;?></td>
                        </tr>
                        <tr>
                            <td class="label">CEP Correspondência</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?php echo formataCep($cepCorresp); ?></td>
                            </tr>
                        <tr>
                            <td class="label">Telefone Residencial</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?php echo formataFone($foneRes); ?></td>
                            </tr>
                        <tr>
                            <td class="label">Telefone Comercial</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?php echo formataFone($foneCom); ?></td>
                        </tr>
                        <tr>
                            <td class="label">Telefone Celular</td>
                            <td class="field" colspan="<?=$colspan?>">
                        <?php echo formataFone($foneCel); ?></td>
                        </tr>
                        <tr>
                            <td class="label">e-mail</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$email;?></td>
                        </tr>
                        <tr>
                            <td class="label">e-mail adicional</td>
                            <td class="field" colspan="<?=$colspan?>"><?=$emailAdic;?></td>
                        </tr>
            <?php
                        if ($pessoa == 'juridica') {
            ?>
                            <tr>
                                <td class="label">CNPJ</td>
                                <td class="field" colspan="<?=$colspan?>">
                            <?php echo numeroToCnpj($cnpj); ?></td>
                            </tr>
                            <tr>
                                <td class="label">Inscrição Estadual</td>
                                <td class="field" colspan="<?=$colspan?>"><?=$inscEst;?></td>
                            </tr>
<?php
            }

            if ($pessoa == 'fisica') {
?>
                            <tr>
                                <td class="label">CPF</td>
                                <td class="field" colspan="<?=$colspan?>">
                                <?php echo numeroToCpf($cpf); ?></td>
                            </tr>
                            <tr>
                                <td class="label">RG</td>
                                <td class="field" colspan="<?=$colspan?>"><?=$rg;?></td>
                            </tr>
<?php
            }
?>
                    </table>
                </td>
            </tr>
<?php
        }//Fim do if mostraDados

    }//Fim da function dadosContribuinte

}//Fim da classe
?>
