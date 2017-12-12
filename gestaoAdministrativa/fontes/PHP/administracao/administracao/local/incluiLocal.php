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
* Manutneção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4047 $
$Name$
$Author: cassiano $
$Date: 2005-12-16 17:41:36 -0200 (Sex, 16 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");

$stMascaraLocal = pegaConfiguracao("mascara_local");
$stMascaraSetor = pegaConfiguracao("mascara_setor");
$inTamanhoMascara = strlen($stMascaraSetor);
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
$anoE = pegaConfiguracao("ano_exercicio");
$codOrgao = $_REQUEST['codOrgao'];
$codUnidade = $_REQUEST['codUnidade'];
?>
<script type="text/javascript">

    function validaSetor(origem,item)
    {
        var f = document.frm;
        f.controle.value = 2;
        f.stOrigem.value = origem;
        f.stItem.value = item;
        f.submit();
    }

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomLocal.value.length;
        if (campo == 0) {
            mensagem += "@nome do local";
            erro = true;
         }

        campo = document.frm.stCodSetor.value.length;
        if (campo == 0) {
            mensagem += "@setor";
            erro = true;
         }

        if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
            return !(erro);
        }

        function IncluiNumCgm()
        {
          var stFrmAnt = document.frm.action;
//          document.frm.action = document.frm.action + '&codOrgao='+<?=$codOrgao?> + '&codUnidade='+<?=$codUnidade?> + '&anoE=' + <?=$anoE?>;
          document.frm.controle.value = 3;
          document.frm.submit();
          document.frm.controle.value = 2;
          document.frm.action = stFrmAnt;
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
<form name="frm" action="incluiLocal.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="controle" value="1">
<input type="hidden" name="stOrigem" value="text">
<input type="hidden" name="stItem" value="">
<table width="100%">
<tr><td class="alt_dados" colspan=2>Dados para local</td></tr>
<tr>
    <td class="label" width="20%">*Setor</td>
    <td class="field">
    <input type="text" name="stCodSetor" size="<?php echo ($inTamanhoMascara+2); ?>" maxlength="<?=$inTamanhoMascara;?>" value=""
    onBlur="validaSetor('text','');"
    onKeyPress="return(isValido(this,event,'0123456789'));"
    onKeyUp="JavaScript: mascaraDinamico('<?=$stMascaraSetor;?>', this, event);"><br>
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
        $combo .= "<select name=\"stNomeOrgao\" onchange=\"validaSetor('combo','orgao');\" style=\"width: 400px;\" >\n";
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
    <br>
    <select name="stNomeUnidade"style="width: 400px;"  onchange="validaSetor('combo','unidade');">
        <option value="XXX">Selecione a unidade</option>
    </select>
    <br>
    <select name="stNomeDpto"style="width: 400px;"  onchange="validaSetor('combo','dpto');">
        <option value="XXX">Selecione o departamento</option>
    </select>
    <br>
    <select name="stNomeSetor"style="width: 400px;"  onchange="validaSetor('combo','setor');">
        <option value="XXX">Selecione o setor</option>
    </select>
    </td>
</tr>
<tr>
    <td class="label">*Nome do local</td>
    <td class=field>
        <input type="text" size="60" maxlength="60" name="nomLocal" value="">
    </td>
</tr>
</tr>
<tr>
    <td class=label  title="Usuário responsável pelo local">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="IncluiNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="" border=0></a>
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

    $stCodSetor  = $_REQUEST['stCodSetor'];
    $nomLocal  = $_REQUEST['nomLocal'];
    $usuarioResponsavel  = $_REQUEST['usuarioResponsavel'];

    $js = "";
    $arCodSetor = preg_split( "/[^a-zA-Z0-9]/",$stCodSetor);

    $stExercicio = $arCodSetor[4];
    $codOrgao = (int) $arCodSetor[0];
    $codUnidade = (int) $arCodSetor[1];
    $codDepartamento = (int) $arCodSetor[2];
    $codSetor = (int) $arCodSetor[3];

    if (comparaValor("cod_setor", $codSetor, "administracao.setor"," and cod_setor > 0 and cod_orgao='".$codOrgao."' and cod_unidade='".$codUnidade."' and cod_departamento='".$codDepartamento."' and ano_exercicio='".$stExercicio."'")) {
        exibeAviso("Setor inválido! (".$stCodSetor.")","unica","erro");
        executaFrameOculto("f.ok.disabled = false;");
        break;
    }
    $codLocal = pegaID("cod_local", "administracao.local", "where cod_orgao=$codOrgao and cod_unidade=$codUnidade and cod_departamento=$codDepartamento and cod_setor=$codSetor and ano_exercicio='".$stExercicio."'");
    $configuracao = new configuracaoLegado;
    $configuracao->setaValorLocal($codLocal,$codSetor,$codDepartamento,$codUnidade,$codOrgao,$nomLocal,$stExercicio,$usuarioResponsavel);
    if (comparaValor("nom_local", $nomLocal, "administracao.local", "and cod_orgao = $codOrgao and cod_unidade = $codUnidade and cod_departamento = $codDepartamento and cod_setor = $codSetor",1)) {
        if ($configuracao->insertLocal()) {
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomSetor);
                $audicao->insereAuditoria();
                $stCodLocal = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$stExercicio;
                $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
                if ($arCodLocal[0]) {
                    $stCodLocal = $arCodLocal[1];
                }
                echo '<script type="text/javascript">
                    //alertaAviso("'.$nomLocal.'","incluir","aviso","'.Sessao::getId().'");
                    alertaAviso("'.$stCodLocal.' - '.$nomLocal.'","incluir","aviso","'.Sessao::getId().'");
                    </script>';
                    $js .= "f.nomLocal.value = ''; \n";
                    $js .= "f.usuarioResponsavel.value = ''; \n";
                    $js .= "f.usuarioResponsavelN.value = ''; \n";
                    $js .= "f.ok.disabled = false; \n";
                    executaFrameOculto($js);
        } else {
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomLocal.'","n_incluir","erro","'.Sessao::getId().'");
                    </script>';
                    executaFrameOculto("f.ok.disabled = false;");
        }
    } else {
        echo '
            <script type="text/javascript">
            alertaAviso("O Local '.$nomLocal.' já existe","unica","erro","'.Sessao::getId().'");
            </script>';
            executaFrameOculto("f.ok.disabled = false;");
    }
break;

//Validação do setor
case 2:

    $stCodSetor = $_REQUEST['stCodSetor'];
    $stOrigem = $_REQUEST['stOrigem'];
    $stNomeOrgao = $_REQUEST['stNomeOrgao'];
    $stNomeSetor = $_REQUEST['stNomeSetor'];
    $stItem = $_REQUEST['stItem'];
    $stNomeUnidade = $_REQUEST['stNomeUnidade'];
    $stNomeDpto = $_REQUEST['stNomeDpto'];
    $nomLocal = $_REQUEST['nomLocal'];

    $js = "";
    $js .= 'window.parent.frames["telaMensagem"].document.location = "'.CAM_FW_INSTANCIAS.'index/menu.html";';
    $arCodSetor = preg_split( "/[^a-zA-Z0-9]/",$stCodSetor);
    $stExercicio = $arCodSetor[4];
    $inCodOrgao = (int) $arCodSetor[0];
    $inCodUnidade = (int) $arCodSetor[1];
    $inCodDpto = (int) $arCodSetor[2];
    $inCodSetor = (int) $arCodSetor[3];

    $boCodOrgao = false;
    $boCodUnidade = false;
    $boCodDpto = false;
    $boCodSetor = false;

    if ($stOrigem == "combo") {
        switch ($stItem) {
            case 'orgao':
                if ($stNomeOrgao == "XXX") {
                    $inCodOrgao = 0;
                    $arCodSetor[0] = 0;
                } else {
                    $arOrgao = explode("/",$stNomeOrgao);
                    $inCodOrgao = (int) $arOrgao[0];
                    $arCodSetor[0] = $arOrgao[0];
                }
                $inCodUnidade = 0;
                $inCodDpto = 0;
                $inCodSetor = 0;
                $stExercicio = $arOrgao[1];
            break;
            case 'unidade':
                if ($stNomeUnidade == "XXX") {
                    $inCodUnidade = 0;
                    $arCodSetor[1] = 0;
                } else {
                    $arUnidade = explode("/",$stNomeUnidade);
                    $inCodUnidade = (int) $arUnidade[0];
                    $arCodSetor[1] = $arUnidade[0];
                }
                $inCodDpto = 0;
                $inCodSetor = 0;
            break;
            case 'dpto':
                if ($stNomeDpto == "XXX") {
                    $inCodDpto = 0;
                    $arCodSetor[2] = 0;
                } else {
                    $arDpto = explode("/",$stNomeDpto);
                    $inCodDpto = (int) $arDpto[0];
                    $arCodSetor[2] = $arDpto[0];
                }
                $inCodSetor = 0;
            break;
            case 'setor':
                if ($stNomeSetor == "XXX") {
                    $inCodSetor = 0;
                    $arCodSetor[3] = 0;
                } else {
                    $arSetor = explode("/",$stNomeSetor);
                    $inCodSetor = (int) $arSetor[0];
                    $arCodSetor[3] = $arSetor[0];
                }
            break;
        }
    }

    //Se o usuário não preencheu o exercício o sistema utiliza o exercício do login
    if (strlen($stExercicio) != 4) {
        $stExercicio = Sessao::getExercicio();
        $stCodSetor = $arCodSetor[0].".".$arCodSetor[1].".".$arCodSetor[2].".".$arCodSetor[3]."/".$stExercicio;
        $arSetor = validaMascara($stMascaraSetor,$stCodSetor);
        if ($arSetor[0]) {
            $stCodSetor = $arSetor[1];
        }
        $js .= "f.stCodSetor.value = '".$stCodSetor."' \n";
    }

    //Valida o órgão
    if ($inCodOrgao > 0) {
        if (comparaValor("cod_orgao", $inCodOrgao, "administracao.orgao"," and cod_orgao > 0 and ano_exercicio='".$stExercicio."'")) {
            $js .= 'erro = true;
                    mensagem += "@Órgão inválido! ('.$arCodSetor[0].'/'.$stExercicio.')";';
            $js .= "f.stNomeOrgao.options[0].selected = true; \n";
        } else {
            $boCodOrgao = true;
            $js .= 'validaCombo("'.$arCodSetor[0].'/'.$stExercicio.'",f.stNomeOrgao);';
        }
    }

    //Valida a UNIDADE
    if ($boCodOrgao) {
        //Monta a combo da unidade de acordo com o órgão
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
        //Verifica se foi preenchida a unidade
        if ($inCodUnidade > 0) {
            if (comparaValor("cod_unidade", $inCodUnidade, "administracao.unidade"," and cod_unidade > 0 and cod_orgao='".$inCodOrgao."' and ano_exercicio='".$stExercicio."'")) {
                $js .= 'erro = true;
                        mensagem += "@Unidade inválida! ('.$arCodSetor[1].'/'.$stExercicio.')";';
            } else {
                $boCodUnidade = true;
                $js .= 'validaCombo("'.$arCodSetor[1].'/'.$stExercicio.'",f.stNomeUnidade);';
            }
        }
    } else {
        $js .= "limpaSelect(f.stNomeUnidade,1); \n";
    }

    //Valida o DEPARTAMENTO
    if ($boCodOrgao and $boCodUnidade) {
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
        //Verifica se foi preenchido o departamento
        if ($inCodDpto > 0) {
            if (comparaValor("cod_departamento", $inCodDpto, "administracao.departamento"," and cod_departamento > 0 and cod_orgao='".$inCodOrgao."' and cod_unidade='".$inCodUnidade."' and ano_exercicio='".$stExercicio."'")) {
                $js .= 'erro = true;
                        mensagem += "@Departamento inválido! ('.$arCodSetor[2].'/'.$stExercicio.')";';
            } else {
                $boCodDpto = true;
                $js .= 'validaCombo("'.$arCodSetor[2].'/'.$stExercicio.'",f.stNomeDpto);';
            }
        }
    } else {
        $js .= "limpaSelect(f.stNomeDpto,1); \n";
    }

    //Valida o SETOR
    if ($boCodOrgao and $boCodUnidade and $boCodDpto) {
        $sSQL = "SELECT cod_setor, ano_exercicio, nom_setor FROM administracao.setor
                WHERE cod_orgao = ".$inCodOrgao."
                AND cod_unidade = ".$inCodUnidade."
                AND cod_departamento = ".$inCodDpto."
                AND ano_exercicio = ".$stExercicio."
                AND cod_setor > 0
                ORDER by lower(nom_setor) ";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $combo = "";
        $iCont = 1;
        $js .= "limpaSelect(f.stNomeSetor,1); \n";
        while (!$dbEmp->eof()) {
            $codSetor = trim($dbEmp->pegaCampo("cod_setor"));
            $nomSetor = trim($dbEmp->pegaCampo("nom_setor"));
            $dbEmp->vaiProximo();
            $stCodSetorf = $codSetor."/".$stExercicio;
            $arSetor = validaMascara($arCodSetor[3].'/'.$stExercicio,$stCodSetorf);
            if ($arSetor[0]) {
                $stCodSetorf = $arSetor[1];
            }
            $js .= "f.stNomeSetor.options[".$iCont++."] = new Option('".$nomSetor."','".$stCodSetorf."');\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        //Verifica se foi preenchido o setor
        if ($inCodSetor > 0) {
            if (comparaValor("cod_setor", $inCodSetor, "administracao.setor"," and cod_setor > 0 and cod_orgao='".$inCodOrgao."' and cod_unidade='".$inCodUnidade."' and cod_departamento='".$inCodDpto."' and ano_exercicio='".$stExercicio."'")) {
                $js .= 'erro = true;
                        mensagem += "@Setor inválido! ('.$arCodSetor[3].'/'.$stExercicio.')";';
            } else {
                $boCodSetor = true;
                $js .= 'validaCombo("'.$arCodSetor[3].'/'.$stExercicio.'",f.stNomeSetor);';
            }
        }
    } else {
        $js .= "limpaSelect(f.stNomeSetor,1); \n";
    }

    $stCodSetor = $arCodSetor[0].".".$arCodSetor[1].".".$arCodSetor[2].".".$arCodSetor[3]."/".$stExercicio;
    $arSetor = validaMascara($stMascaraSetor,$stCodSetor);
    if ($arSetor[0]) {
        $stCodSetor = $arSetor[1];
    }
    $js .= "f.stCodSetor.value = '".$stCodSetor."' \n";

    executaFrameOculto($js);

break;

case 3:

    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$usuarioResponsavel;
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\" \n";
    executaFrameOculto($js);
break;

}//fim switch
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
