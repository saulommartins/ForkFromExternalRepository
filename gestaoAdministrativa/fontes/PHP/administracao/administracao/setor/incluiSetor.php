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
* Manutneção de setores
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
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[1]."/9999";
$stMascaraDpto = $arMascaraSetor[2]."/9999";

$controle = $_REQUEST['controle'];

if (!(isset($controle))) {
    $controle = 0;
}

switch ($controle) {
case 0:

    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $stCodUnidade = $_REQUEST['stCodUnidade'];
    $stCodDpto = $_REQUEST['stCodDpto'];
?>
<script type="text/javascript">
    function validaOrganograma(iCod,campo,campoCod,stItemMsg)
    {
        var cod = iCod;
        var val;
        var erro = true;
        var msg = "Campo "+stItemMsg+" inválido ("+iCod+")";
        var f = document.frm;
        var tam = campo.options.length - 1;

        if (campoCod.value.length==0) {
            campo.options[0].selected = true;

            return false;
        }
        //Percorre todos os valores para encontrar qual item da combo tem o valor digitado
        while (tam >= 0) {
            val = campo.options[tam].value;
            if (cod==val) {
                campo.options[tam].selected = true;
                erro = false;
            }
            tam = tam - 1 ;
        }
        //Se não encontrou o valor o código digitado é inválido
        if (erro) {
            campoCod.value = "";
            campoCod.focus();
            campo.options[0].selected = true;
            alertaAviso(msg,'unica','erro','<?=Sessao::getId();?>');

            return false;
        } else {
            return true;
        }
    }

    function validaOrgao(iCod,campo,campoCod,stItemMsg)
    {
        var f = document.frm;
        if (validaOrganograma(iCod,campo,campoCod,stItemMsg)) {
            f.stNomeUnidade.disabled = false;
            f.stCodUnidade.disabled = false;
            f.controle.value = 2;
            f.submit();
            f.controle.value = 1;
        } else {
            f.stCodUnidade.value = "";
            f.stNomeUnidade.options[0].selected = true;
            limpaSelect(f.stNomeUnidade,1);
            f.stCodUnidade.disabled = true;
        }
    }

    function preencheOrgao()
    {
        var f = document.frm;
        if (f.stNomeOrgao.value == "XXX") {
            f.stCodOrgao.value = "";
            f.stCodUnidade.value = "";
            f.stNomeUnidade.options[0].selected = true;
            limpaSelect(f.stNomeUnidade,1);
            f.stCodUnidade.disabled = true;

            return false;
        }
        if (preencheCampo(f.stNomeOrgao,f.stCodOrgao)) {
            f.stNomeUnidade.disabled = false;
            f.stCodUnidade.disabled = false;
            f.controle.value = 2;
            f.submit();
            f.controle.value = 1;
        } else {
            f.stCodUnidade.value = "";
            f.stNomeUnidade.options[0].selected = true;
            limpaSelect(f.stNomeUnidade,1);
            f.stCodUnidade.disabled = true;
        }
    }

    function validaUnidade(iCod,campo,campoCod,stItemMsg)
    {
        var f = document.frm;
        if (validaOrganograma(iCod,campo,campoCod,stItemMsg)) {
            f.stNomeDpto.disabled = false;
            f.stCodDpto.disabled = false;
            f.controle.value = 3;
            f.submit();
            f.controle.value = 1;
        } else {
            f.stCodDpto.value = "";
            f.stNomeDpto.options[0].selected = true;
            limpaSelect(f.stNomeDpto,1);
            f.stCodDpto.disabled = true;
        }
    }

    function preencheUnidade()
    {
        var f = document.frm;
        if (f.stNomeUnidade.value == "XXX") {
            f.stCodUnidade.value = "";
            f.stCodDpto.value = "";
            f.stNomeDpto.options[0].selected = true;
            limpaSelect(f.stNomeDpto,1);
            f.stCodDpto.disabled = true;

            return false;
        }
        if (preencheCampo(f.stNomeUnidade,f.stCodUnidade)) {
            f.stNomeDpto.disabled = false;
            f.stCodDpto.disabled = false;
            f.controle.value = 3;
            f.submit();
            f.controle.value = 1;
        } else {
            f.stCodDpto.value = "";
            f.stNomeDpto.options[0].selected = true;
            limpaSelect(f.stNomeDpto,1);
            f.stCodDpto.disabled = true;
        }
    }

    function IncluiNumCgm()
    {
          var stFrmAnt = document.frm.action;
          document.frm.controle.value = 4;
          document.frm.submit();
          document.frm.controle.value = 1;
          document.frm.action = stFrmAnt;
      }

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.stCodOrgao.value.length;
        if (campo == 0) {
            mensagem += "@Órgão";
            erro = true;
         }

        campo = document.frm.stCodUnidade.value.length;
        if (campo == 0) {
            mensagem += "@Unidade";
            erro = true;
         }

        campo = document.frm.stCodDpto.value.length;
        if (campo == 0) {
            mensagem += "@Departamento";
            erro = true;
         }

        campo = document.frm.nomSetor.value.length;
        if (campo == 0) {
            mensagem += "@Nome do setor";
            erro = true;
         }

        if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
            return !(erro);
        }

      function Salvar()
      {
         var f = document.frm;
         f.ok.disabled = true;
         f.controle.value = 1;
         if (Valida()) {
            f.submit();
         } else {
            f.ok.disabled = false;
         }
      }
 </script>
<form name="frm" action="incluiSetor.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="controle" value="1">

<table width=100%>
<tr><td class="alt_dados" colspan=2>Dados para setor</td></tr>
<tr>
    <td class="label" width="20%">*Órgão</td>
    <td class=field>
    <input type="text" name="stCodOrgao" value="<?=$stCodOrgao;?>" maxlength="15" size="15"
    onBlur="validaOrgao(this.value,document.frm.stNomeOrgao,document.frm.stCodOrgao,'Órgão');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraOrgao;?>', this, event);">
    <?php
        $sSQL = "SELECT cod_orgao, ano_exercicio, nom_orgao
                FROM administracao.orgao
                WHERE cod_orgao > 0
                ORDER BY lower(nom_orgao) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $combo .= "<select name=\"stNomeOrgao\" onchange=\"preencheOrgao();\" style=\"width: 400px;\" >\n";
        $combo .= "<option value='XXX'>Selecione o órgão</option>\n";
        while (!$dbEmp->eof()) {
            $codOrgaof = trim($dbEmp->pegaCampo("cod_orgao"));
            $stExercicioOrgao = $dbEmp->pegaCampo("ano_exercicio");
            $nomOrgao = trim($dbEmp->pegaCampo("nom_orgao"));
            $dbEmp->vaiProximo();
            $stCodOrgaof = $codOrgaof."/".$stExercicioOrgao;
            $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgaof);
            if ($arCodOrgao[0]) {
                $stCodOrgaof = $arCodOrgao[1];
            }
            $selected = "";
            if ($stCodOrgao == ($stCodOrgaof)) {
                $selected = "selected";
            }
            $combo .= "<option ".$selected." value=\"".$stCodOrgaof."\">".$nomOrgao." - ".$stExercicioOrgao."</option>\n";
    }
        $combo .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $combo;
?>
    </td>
</tr>
<tr>
    <td class="label">*Unidade</td>
<?php
    //Se o formulário estiver sendo carregado após uma inclusão, apresenta os códigos inseridos anteriormente
    if ((strlen($stCodUnidade) > 0) and (strlen($stCodOrgao) > 0)) {
        $arCodOrgao = explode("/",$stCodOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
?>
    <td class=field>
    <input type="text" name="stCodUnidade" value="<?=$stCodUnidade;?>" maxlength="15" size="15"
    onBlur="validaUnidade(this.value,document.frm.stNomeUnidade,document.frm.stCodUnidade,'Unidade');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraUnidade;?>', this, event);">
    <?php
        $sSQL = "SELECT cod_unidade, ano_exercicio, nom_unidade FROM administracao.unidade
                 WHERE cod_orgao = ".$inCodOrgao."
                 AND ano_exercicio = ".$stExercicio."
                 AND cod_unidade > 0
                 ORDER by lower(nom_unidade) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $combo .= "<select name=\"stNomeUnidade\" onchange=\"preencheUnidade();\" style=\"width: 400px;\" >\n";
        $combo .= "<option value='XXX'>Selecione a unidade</option>\n";
        while (!$dbEmp->eof()) {
            $codUnidade = trim($dbEmp->pegaCampo("cod_unidade"));
            $nomUnidade = trim($dbEmp->pegaCampo("nom_unidade"));
            $dbEmp->vaiProximo();
            $stCodUnidadef = $codUnidade."/".$stExercicio;
            $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidadef);
            if ($arCodUnidade[0]) {
                $stCodUnidadef = $arCodUnidade[1];
            }
            $selected = "";
            if ($stCodUnidade == ($stCodUnidadef)) {
                $selected = "selected";
            }
            $combo .= "<option ".$selected." value=\"".$stCodUnidadef."\">".$nomUnidade."</option>\n";
    }
        $combo .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $combo;
?>
    </td>
<?php
} else {
?>
    <td class="field">
        <input type="text" name="stCodUnidade" value="<?=$stCodUnidade;?>" maxlength="15" size="15" disabled=""
    onBlur="validaUnidade(this.value,document.frm.stNomeUnidade,document.frm.stCodUnidade,'Unidade');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraUnidade;?>', this, event);">
        <select name="stNomeUnidade" disabled="" style="width: 400px;"  onchange="preencheUnidade();">
        <option value="XXX">Selecione a unidade</option>
        </select>
    </td>
<?php
}
?>
</tr>
<tr>
    <td class="label">*Departamento</td>
<?php
    //Se o formulário estiver sendo carregado após uma inclusão, apresenta os códigos inseridos anteriormente
    if ((strlen($stCodUnidade) > 0) and (strlen($stCodOrgao) > 0) and (strlen($stCodDpto) > 0)) {
        $arCodOrgao = explode("/",$stCodOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $arCodUnidade = explode("/",$stCodUnidade);
        $inCodUnidade = $arCodUnidade[0];
?>
    <td class="field">
    <input type="text" name="stCodDpto" value="<?=$stCodDpto;?>" maxlength="15" size="15"
    onBlur="validaOrganograma(this.value,document.frm.stNomeDpto,document.frm.stCodDpto,'Departamento');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraDpto;?>', this, event);">
    <?php
        $sSQL = "SELECT cod_departamento, ano_exercicio, nom_departamento FROM administracao.departamento
                 WHERE cod_orgao = ".$inCodOrgao."
                 AND cod_unidade = ".$inCodUnidade."
                 AND ano_exercicio = ".$stExercicio."
                 AND cod_departamento > 0
                 ORDER by lower(nom_departamento) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $combo .= "<select name=\"stNomeDpto\" onchange=\"preencheCampo(this,document.frm.stCodDpto);\" style=\"width: 400px;\" >\n";
        $combo .= "<option value='XXX'>Selecione a unidade</option>\n";
        while (!$dbEmp->eof()) {
            $codDpto = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomDpto = trim($dbEmp->pegaCampo("nom_departamento"));
            $dbEmp->vaiProximo();
            $stCodDptof = $codDpto."/".$stExercicio;
            $arCodDpto = validaMascara($stMascaraDpto,$stCodDptof);
            if ($arCodDpto[0]) {
                $stCodDptof = $arCodDpto[1];
            }
            $selected = "";
            if ($stCodDpto == ($stCodDptof)) {
                $selected = "selected";
            }
            $combo .= "<option ".$selected." value=\"".$stCodDptof."\">".$nomDpto."</option>\n";
    }
        $combo .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo $combo;
?>
    </td>
<?php
} else {
?>
    <td class="field">
        <input type="text" name="stCodDpto" value="<?=$stCodDpto;?>" maxlength="15" size="15" disabled=""
        onBlur="validaOrganograma(this.value,document.frm.stNomeDpto,document.frm.stCodDpto,'Departamento');"
        onKeyPress="return(isValido(this,event,'0123456789'));"
        onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraDpto;?>', this, event);">
        <select name="stNomeDpto" disabled="" style="width: 400px;"  onchange="preencheCampo(this,document.frm.stCodDpto);">
        <option value="XXX">Selecione o departamento</option>
        </select>
    </td>
<?php
}
?>
</tr>
<tr>
    <td class="label">*Nome do Setor</td>
    <td class=field>
        <input type="text" size="60" maxlength="60" name="nomSetor" value="">
    </td>
</tr>
<tr>
    <td class=label  title="Usuário responsável pelo setor">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="IncluiNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar CGM" border=0></a>
    </td>
</tr>
<tr>
    <td class=field colspan=2>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>
<?php
break;

case 1:

    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $stCodUnidade = $_REQUEST['stCodUnidade'];
    $stCodDpto = $_REQUEST['stCodDpto'];
    $nomSetor = $_REQUEST['nomSetor'];
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $arCodOrgao = explode("/",$stCodOrgao);
    $codOrgao = $arCodOrgao[0];
    $stExercicio = $arCodOrgao[1];

    $arCodUnidade = explode("/",$stCodUnidade);
    $codUnidade = $arCodUnidade[0];

    $arCodDpto = explode("/",$stCodDpto);
    $codDpto = $arCodDpto[0];

    $stWhere = " Where cod_orgao = '".$codOrgao."'
                 And cod_unidade = '".$codUnidade."'
                 And cod_departamento = '".$codDpto."'
                 And ano_exercicio = '".$stExercicio."'";
$codSetor = pegaID("cod_setor", "administracao.setor", $stWhere);
$configuracao = new configuracaoLegado;
$configuracao->setaValorSetor($codSetor,$codDpto,$codUnidade,$codOrgao,$nomSetor,$stExercicio,$usuarioResponsavel);
if (comparaValor("nom_setor", $nomSetor, "administracao.setor", "and cod_orgao = $codOrgao and cod_unidade = $codUnidade and cod_departamento = $codDpto and ano_exercicio='".$stExercicio."' ",1)) {
    if ($configuracao->insertSetor()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomSetor);
            $audicao->insereAuditoria();
            $stCodSetor = $codOrgao.".".$codUnidade.".".$codDpto.".".$codSetor."/".$stExercicio;
            $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
            if ($arCodSetor[0]) {
                $stCodSetor = $arCodSetor[1];
            }
            echo '<script type="text/javascript">
                    alertaAviso("'.$stCodSetor.' - '.$nomSetor.'","incluir","aviso","'.Sessao::getId().'");
                 </script>';
            //Limpa o formulário para nova inclusão
            $js = "f.nomSetor.value = ''; \n";
            $js .= "f.usuarioResponsavel.value = ''; \n";
            $js .= "f.usuarioResponsavelN.value = ''; \n";
            $js .= "f.ok.disabled = false; \n";
            executaFrameOculto($js);

    } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomSetor.'","n_incluir","erro","'.Sessao::getId().'");
                 </script>';
                 executaFrameOculto("f.ok.disabled = false;");
    }
} else {
    echo '
        <script type="text/javascript">
        alertaAviso("O Setor '.$nomSetor.' já existe","unica","erro","'.Sessao::getId().'");
        </script>';
        executaFrameOculto("f.ok.disabled = false;");
}
break;

case 2:

    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $stCodUnidade = $_REQUEST['stCodUnidade'];
    $stCodDpto = $_REQUEST['stCodDpto'];

    //Se o orgão foi alterado recria a combo de unidade
    if (isset($stCodOrgao) and strlen($stCodOrgao) > 0) {
        $arCodOrgao = explode("/",$stCodOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $js = "";
        $sSQL = "SELECT cod_unidade, ano_exercicio, nom_unidade FROM administracao.unidade
                 WHERE cod_orgao = ".$inCodOrgao."
                 AND ano_exercicio = ".$stExercicio."
                 AND cod_unidade > 0
                 ORDER by lower(nom_unidade) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $iCont = 1;
        $js .= "limpaSelect(f.stNomeUnidade,1); \n";
        while (!$dbEmp->eof()) {
            $codUnidade = trim($dbEmp->pegaCampo("cod_unidade"));
            $nomUnidade = trim($dbEmp->pegaCampo("nom_unidade"));
            $dbEmp->vaiProximo();
            $stCodUnidadef = $codUnidade."/".$stExercicio;
            $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidadef);
            if ($arCodUnidade[0]) {
                $stCodUnidadef = $arCodUnidade[1];
            }
            $js .= "f.stNomeUnidade.options[".$iCont++."] = new Option('".$nomUnidade."','".$stCodUnidadef."');\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        executaFrameOculto($js);
    }
break;

case 3:

    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $stCodUnidade = $_REQUEST['stCodUnidade'];
    $stCodDpto = $_REQUEST['stCodDpto'];

    //Se a unidade foi alterado recria a combo de departamento
    if (isset($stCodUnidade) and strlen($stCodUnidade) > 0) {
        $arCodOrgao = explode("/",$stCodOrgao);
        $inCodOrgao = $arCodOrgao[0];
        $stExercicio = $arCodOrgao[1];
        $arCodUnidade = explode("/",$stCodUnidade);
        $inCodUnidade = $arCodUnidade[0];
        $js = "";
        $sSQL = "SELECT cod_departamento, ano_exercicio, nom_departamento FROM administracao.departamento
                 WHERE cod_orgao = ".$inCodOrgao."
                 AND cod_unidade = ".$inCodUnidade."
                 AND ano_exercicio = ".$stExercicio."
                 AND cod_departamento > 0
                 ORDER by lower(nom_departamento) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $iCont = 1;
        $js .= "limpaSelect(f.stNomeDpto,1); \n";
        while (!$dbEmp->eof()) {
            $codDpto = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomDpto = trim($dbEmp->pegaCampo("nom_departamento"));
            $dbEmp->vaiProximo();
            $stCodDptof = $codDpto."/".$stExercicio;
            $arCodDpto = validaMascara($stMascaraDpto,$stCodDptof);
            if ($arCodDpto[0]) {
                $stCodDptof = $arCodDpto[1];
            }
            $js .= "f.stNomeDpto.options[".$iCont++."] = new Option('".$nomDpto."','".$stCodDptof."');\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        executaFrameOculto($js);
    }
break;

case 4:

    $usuarioNumCgm = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm, administracao.usuario
                 WHERE sw_cgm.numcgm = ".$usuarioNumCgm."
                   AND sw_cgm.numcgm = usuario.numcgm";
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    if ($usuarioResponsavel == "") {
        echo '<script type="text/javascript">
                    alertaAviso("Código de Usuário invalido ('.$usuarioNumCgm.')","unica","erro","'.Sessao::getId().'");
              </script>';
    }

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\" \n";
    executaFrameOculto($js);
break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
