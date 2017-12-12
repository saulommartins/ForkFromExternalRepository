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
 include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"       );
 include_once '../cse.class.php';
 include_once (CAM_FW_LEGADO."configuracaoLegado.class.php");
 include_once (CAM_FW_LEGADO."auditoriaLegada.class.php"   ); //Inclui classe para inserir auditoria
 include_once (CAM_FW_LEGADO."paginacaoLegada.class.php"   );
 include_once (CAM_FW_LEGADO."mascarasLegado.lib.php"      );
if ( !isset($pagina)) {
   $pagina = "0";
}

$arAba = array (
               0 => "Cidadão/Domicílio",
               1 => "Documentação",
               2 => "Escolaridade",
               3 => "Dados Profissionais",
               4 => "Despesas Mensais",
               5 => "Vínculo Familiar",
               6 => "Censo",
               7 => "Programas Sociais"
               );

$arAbaAtual = array (
                   0 => "cidadao",
                   1 => "documentacao",
                   2 => "escolaridade",
                   3 => "profissionais",
                   4 => "despesas",
                   5 => "vinculo",
                   6 => "censo",
                   7 => "programas"
                   );
function geraNomeVar($nome, $valor)
{
   global $aba;
   global $sessao;
   if ( is_array( $valor ) ) {
       foreach ($valor as $stNome => $stValor) {
           $nomeNovo = $nome."[".$stNome."]";
           if ( is_array( $stValor ) ) {
               foreach ($sessao->transf4[$aba] as $indice => $lixo) {
                   if ( strpos( $indice, $nome."[".$stNome."]" ) !== false  ) {
                       $sessao->transf4[$aba][$indice] = "";
                   }
               }
               geraNomeVar( $nomeNovo, $stValor);
           } else {
               $sessao->transf4[$aba][$nomeNovo] = $stValor;
           }
       }
   } else {
       $sessao->transf4[$aba][$nome] = $valor;
   }
}
if ( !isset( $ctrl ) ) {
   $ctrl = 0;
   $sessao->transf4 = array();
} else {
   //SETA OS VALORES DA ABA ANTERIOR NA VAR DE SESSAO
   foreach ($_POST as $stCampo => $stValor) {
       if ($stCampo != "aba" and $stCampo != "ctrl" and $stCampo != "anoCenso" and $stCampo != "anoPrograma") {
           geraNomeVar( $stCampo, $stValor );
       }
   }

   //BUSCA OS VALORES DA VAR DE SESSAO DO FORM CORRENTE
   if ( count( $sessao->transf4[$arAbaAtual[$ctrl]] ) ) {
       foreach ($sessao->transf4[$arAbaAtual[$ctrl]] as $campo => $valor) {
           if ( strtoupper($valor) != "XXX" ) {
               $$campo = $valor;
           } else {
               $$campo = "";
           }
       }
   }
}
?>
<script type="text/javascript">
<!--
     function atualizaMunicipio()
     {
       document.frm.target = "oculto";
       document.frm.ctrl.value = 12;
       document.frm.submit();
     }

     function zeraMunicipio()
     {
       document.frm.txtMunicipio.value = "";
       limpaSelect(document.frm.municipio,1);
       //document.frm.txtMunicipio.disabled = true;
       //document.frm.municipio.disabled = true;
     }

function valida()
{
   var erro = false;
   var campo;
   var f = document.frm;
   var mensagem = ""
<?php
if ($ctrl == 0) {
?>
   campo = f.codDomicilio.value.length;
   if (campo == 0) {
       mensagem += "@Campo Logradouro inválido!()";
       erro = true;
   }
   campo = f.nomCgm.value.length;
   if (campo == 0) {
       mensagem += "@Campo Nome completo inválido!()";
       erro = true;
   }
   campo = f.dataNasc.value.length;
   if (campo == 0) {
       mensagem += "@Campo Data de nascimento inválido!()";
       erro = true;
   }
   campo = f.dataNasc;
   if ( !( verificaData(campo) ) ) {
       mensagem += "@Campo Data de nascimento inválido!("+campo.value+")";
       erro = true;
   }
   campo = f.codRacaCidadao.value.length;
   if (campo == 0) {
       mensagem += "@Campo Raça/cor inválido!()";
       erro = true;
   }
   campo = f.nomMae.value.length;
   if (campo == 0) {
       mensagem += "@Campo Nome da mãe inválido!()";
       erro = true;
   }
   campo = f.codEstadoCivil.value;
   if ( campo.toUpperCase() == 'XXX' ) {
       mensagem += "@Campo Estado civil inválido!()";
       erro = true;
   }
<?php
} elseif ($ctrl == 1) {
?>
   campo = document.frm.cpf.value.length;
   if (campo > 0) {
       if (campo<14) { //> Campo cpf tem que ter 11 caracteres
           mensagem += "@Campo CPF inválido!("+document.frm.cpf.value+")";
           erro = true;
       }

       campoaux = document.frm.cpf.value;
       var expReg = new RegExp("[^a-zA-Z0-9]","g");
       var campoAuxDesmasc = campoaux.replace(expReg, '');
       if (campo==14) {
           if (!VerificaCPF(campoAuxDesmasc)) { //> Verifica se o CPF é válido
               mensagem += "@CPF inválido!("+campoaux+")";
               erro = true;
           }
       }
   }
<?php
}
?>
   if (erro) { alertaAviso(mensagem,'form','erro','<?=$sessao->id;?>'); }

   return !(erro);
}
function mudaAba(aba)
{
   if ( valida() ) {
       document.frm.target = "telaPrincipal";
<?php
if ($ctrl == 0) {
?>
       if (document.frm.respDomicilio[1].checked && aba == 4) {
           aba = 0;
           alertaAviso("Aba disponível apenas para o responsável pelo domicílio!",'form','erro','<?=$sessao->id;?>');
       }
<?php
} elseif ($sessao->transf4['cidadao']['respDomicilio'] == "f") {
?>
       if (aba == 4) {
           aba = <?=$ctrl;?>;
           alertaAviso("Aba disponível apenas para o responsável pelo domicílio!",'form','erro','<?=$sessao->id;?>');
       }
<?php
}
?>
       document.frm.ctrl.value = aba;
       document.frm.submit();
   }
}

function Salvar()
{
   if ( valida() ) {
       document.frm.target = "oculto";
       document.frm.ctrl.value = 10;
       document.frm.submit();
   }
}

function testaCPF(campoCpf)
{
   var erro = true;
   var expReg = new RegExp("[^a-zA-Z0-9]","g");
   var campoCpfDesmasc = campoCpf.value.replace(expReg, '');
   if (campoCpfDesmasc.length > 0) {
       if (campoCpf.value.length == 14) {
           if ( !VerificaCPF( campoCpfDesmasc ) ) {//> Verifica se o CPF é válido
               erro = false;
           }
       } else {
           erro = false;
       }
   }

   return erro;
}

function buscaDomicilio()
{
   document.frm.target = "oculto";
   document.frm.ctrl.value = 8;
   document.frm.submit();

   return true;
}

function buscaCNPJ(campo)
{
   if (campo) {
       document.frm.target = "oculto";
       document.frm.ctrl.value = 9;
       document.frm.submit();

       return true;
   } else {
       document.frm.cnpj.value = "";
   }
}

function limpaCNPJ()
{
   document.frm.cnpj.value = "";
}

function buscaEmpresa()
{
   return true;
}

function buscaCGM()
{
   document.frm.target = "oculto";
   document.frm.ctrl.value = 11;
   document.frm.submit();

   return true;
}

function desabilitaCGM(desabilita)
{
   if (desabilita == true) {
       document.frm.numCgm.value = "";
       document.frm.numCgm.readOnly = true;
       document.frm.nomCgm.value = "";
       document.frm.nomCgm.readOnly = false;
       document.frm.nomCgm.focus();
   } else {
       document.frm.numCgm.value = "";
       document.frm.numCgm.readOnly = false;
       document.frm.nomCgm.value = "";
       document.frm.nomCgm.readOnly = true;
   }
}

//-->
</script>
<form action="<?=$PHP_SELF;?>?<?=$sessao->id?>" method="post" name="frm" onSubmit="return false;">
<input type="hidden" name="ctrl" value="">
<table width="100%">
   <tr>
<?php
   $flagQuebra = 0;
   foreach ($arAba as $codAba => $nomAba) {
       if ($codAba == $ctrl) {
           echo "        <td width='24%' class='show_dados_center'><b>".$nomAba."</b></td>\n";
       } else {
           echo "        <td width='24%' class='labelcenter'><a href='JavaScript: mudaAba(".$codAba.")'>".$nomAba."</a></td>\n";
       }
       $flagQuebra++;
       if ($flagQuebra == 4) {
           echo "    </tr>\n";
           echo "    <tr>\n";
       }
   }
?>
</tr>
</table>
<?php
switch ($ctrl) {
   case 0:
?>
<input type="hidden" name="aba" value="cidadao">
<table width="100%">
   <tr>
       <td colspan="2" class="alt_dados">
           Relação Cidadão/Domicílio
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Descrição do domicílio">*Domicílio</td>
       <td class="field" width="80%">
           <input type="text" name="codDomicilio" size="6" maxlength="6" value="<?=$codDomicilio?>" onchange="javascript: buscaDomicilio();"  onKeyPress="return(isValido(this, event, '0123456789'));">
           <input type="text" name="nomDomicilio" size="60" maxlength="126" readonly value="<?=$nomDomicilio;?>">
           <a href="javascript:procuraDomicilio('frm','codDomicilio','nomDomicilio','<?=$sessao->id?>');"><img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar Fornecedor" width=20 height=20 border=0></a>
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Responsável pelo domicílio">*Responsável</td>
       <td class="field" width="80%">
<?php
   if ($respDomicilio == "t") {
       $tChecked = " checked";
       $fChecked = "";
   } else {
       $tChecked = "";
       $fChecked = " checked";
   }

?>
           <input type="radio" name="respDomicilio" value="t"<?=$tChecked;?> onClick="JavaScrip: desabilitaCGM( false );">Sim&nbsp;
           <input type="radio" name="respDomicilio" value="f"<?=$fChecked;?> onClick="JavaScrip: desabilitaCGM( true );">Não
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="CGM do cidadão">CGM</td>
       <td class="field" width="80%">
           <input type="text" size='8' maxlength='8' onKeyPress="return(isValido(this, event, '0123456789'))" name="numCgm" value='<?=$numCgm;?>' onchange="javascript: buscaCGM();">
           <a href='javascript:procurarCgm("frm","numCgm","nomCgm","geral","<?=$sessao->id?>");'>
           <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="Procurar Interessado" width=22 height=22 border=0>
           </a>
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Nome completo do cidadão">*Nome Completo</td>
       <td class="field" width="80%">
           <input type="text" name="nomCgm" size="80" maxlength="200" value="<?=$nomCgm;?>">
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Data de nascimento do cidadão">*Data de Nascimento</td>
       <td class="field" width="80%">
           <?php geraCampoData("dataNasc", $dataNasc, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Sexo do cidadão">*Sexo</td>
       <td class="field" width="80%">
<?php
   if ( $sexoCidadao == "1" or empty($sexoCidadao) ) {
       $mChecked = " checked";
       $fChecked = "";
   } else {
       $mChecked = "";
       $fChecked = " checked";
   }

?>
           <input type="radio" name="sexoCidadao" value="1"<?=$mChecked;?>>M
           <input type="radio" name="sexoCidadao" value="2"<?=$fChecked;?>>F
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Raça/cor do cidadão">*Raça/Cor</td>
       <td class="field" width="80%">
           <input type="text" size="6" maxlength="6" name="codTxtRacaCidadao" value="<?=$codRacaCidadao?>" onchange='preencheCampo(this, document.frm.codRacaCidadao);'  onKeyPress="return(isValido(this, event, '0123456789'));">
<?php
   $combo = montaComboGenerico("codRacaCidadao", "cse.raca", "cod_raca", "nom_raca", $codRacaCidadao,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtRacaCidadao);' ","", true, false, false);
   echo $combo;
?>
       </td>
   </tr>
       <tr>
       <td class="label" width="20%" title="País de origem do cidadão">Pais de Origem</td>
       <td class="field" width="80%">
           <input type="text" name="paisOrCidadao" size="80" maxlength="200" value="<?=$paisOrCidadao;?>">
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Telefone celular do cidadão">Celular</td>
       <td class="field" width="80%">
           <input type="text" name="telCelular" size="10" maxlength="10" value="<?=$telCelular;?>">
       </td>
   </tr>
   <tr>
       <td class="label" width="20%" title="Estado de origem do cidadão">Estado de Origem</td>
       <td class="field" width="80%">
           <input type="text" name="txtEstado" maxlength="6" size="6" value="<?=$estado;?>" onChange ="javascript:if ( preencheCampo(this, document.frm.estado)) { atualizaMunicipio() } else { zeraMunicipio(); };" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
   $combo = montaComboGenerico("estado", "sw_uf", "cod_uf", "nom_uf", $estado,"style='width: 200px;' onchange='preencheCampo(this, document.frm.txtEstado);if ( preencheCampo(this, document.frm.estado)) {
atualizaMunicipio() } else { zeraMunicipio(); };' ","", true, false, false);
   echo $combo;
?>
       </td>
   </tr>
   <tr>
       <td class="label" title="Município de origem do cidadão">Município de Origem</td>
       <td class="field">
       <?php
   if ( !empty($municipio)) {
       $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$estado." ORDER by nom_municipio";
       $dbEmp = new dataBaseLegado;
       $dbEmp->abreBD();
       $dbEmp->abreSelecao($sSQL);
       $dbEmp->vaiPrimeiro();
       $comboMunicipio = "";
       while (!$dbEmp->eof()) {
           $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
           $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
           $dbEmp->vaiProximo();
           $comboMunicipio .= "                    ";
           $comboMunicipio .= "<option value='".$codg_municipio."'";
           if ($codg_municipio == $municipio) {
               $comboMunicipio .= " SELECTED";
           }
           $comboMunicipio .= ">".$nomg_municipio."</option>\n";
       }
       $dbEmp->limpaSelecao();
       $dbEmp->fechaBD();
   }
?>
               <input type="text" name="txtMunicipio" maxlength="6" size="6" value="<?=$municipio;?>" <?=$stDisabled;?> onChange="JavaScript: preencheCampo(this, document.frm.municipio);" onKeyPress="return(isValido(this,event,'0123456789'));">
               <select name="municipio" style="width: 200px" onChange="JavaScript: preencheCampo(this, document.frm.txtMunicipio);">
                    <option value="xxx">Selecione uma cidade</option>
<?=$comboMunicipio;?>
                </select>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome completo do pai">Nome do Pai</td>
        <td class="field" width="80%">
            <input type="text" name="nomPai" size="80" maxlength="200" value="<?=$nomPai;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome completo da mãe">*Nome da mãe</td>
        <td class="field" width="80%">
            <input type="text" name="nomMae" size="80" maxlength="200" value="<?=$nomMae;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado civil do cidadão">*Estado civil</td>
        <td class="field" width="80%">
            <input type="text" size="6" maxlength="6" name="codTxtEstadoCivil" value="<?=$codEstadoCivil?>" onchange='preencheCampo(this, document.frm.codEstadoCivil);' onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codEstadoCivil", "cse.estado_civil", "cod_estado", "nom_estado", $codEstadoCivil,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtEstadoCivil);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="No caso de deficientes, selecione a deficiência">Tipo de deficiência</td>
        <td class="field" width="80%">
            <input type="text" size="6" maxlength="6" name="codTxtDeficiencia" value="<?=$codDeficiencia?>" onchange='preencheCampo(this, document.frm.codDeficiencia);' onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codDeficiencia", "cse.deficiencia", "cod_deficiencia", "nom_deficiencia", $codDeficiencia,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtDeficiencia);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Tempo de moradia no domicílio">Tempo de moradia</td>
        <td class="field" width="80%">
            <input type="text" name="tempoMoradia" value="<?=$tempoMoradia;?>" value="" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codUnidadeMedida" style="width: 200px;">
                <option value="xxx">Selecione</option>
<?php
    $dbCon = new dataBaseLegado;
    $dbCon->abreBD();
    $sSQL  = " SELECT ";
    $sSQL .= "     cod_unidade, ";
    $sSQL .= "     cod_grandeza, ";
    $sSQL .= "     nom_unidade ";
    $sSQL .= " FROM ";
    $sSQL .= "     administracao.unidade_medida ";
    $sSQL .= " WHERE ";
    $sSQL .= "     cod_grandeza = 1 ";
    $sSQL .= " ORDER BY ";
    $sSQL .= "     nom_unidade ";
    $dbCon->abreSelecao($sSQL);
    $dbCon->vaiPrimeiro();
    if (!$dbCon->eof()) {
        while ( !$dbCon->eof() ) {
            $codUnidade = $dbCon->pegaCampo("cod_unidade");
            $codGrandeza= $dbCon->pegaCampo("cod_grandeza");
            $nomUnidade = $dbCon->pegaCampo("nom_unidade");
            $selected = "";
            if ($codUnidadeMedida == $codUnidade."-".$codGrandeza) {
                $selected = " selected";
            }
            echo "                <option value=\"".$codUnidade."-".$codGrandeza."\"".$selected.">".$nomUnidade."</option>\n";
            $dbCon->vaiProximo();
        }
    }
    $dbCon->limpaSelecao();
    $dbCon->fechaBD();

?>
            </select>
        </td>
    </tr>
        <td class="field" colspan="2">
            <?php geraBotaoOk();?>
        </td>
    </tr>
</table>
</form>

<?php
    break;
    case 1:
?>
<input type="hidden" name="aba" value="documentacao">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Documentação do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número de identificação social">Identificação social</td>
        <td class="field" width="80%">
            <input type="text" name="numIdentSocial" value="<?=$numIdentSocial;?>" size="10" maxlength="10" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do cartão nacional de saúde">Cartão nacional de saúde</td>
        <td class="field" width="80%">
            <input type="text" name="numCartSaude" value="<?=$numCartSaude;?>" size="10" maxlength="10" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Certidão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Tipo de certidão">Tipo</td>
        <td class="field" width="80%">
            <input type="text" size="6" maxlength="6" name="codTxtCertidao" value="<?=$codCertidao?>" onchange='preencheCampo(this, document.frm.codCertidao);' onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codCertidao", "cse.tipo_certidao", "cod_certidao", "nom_certidao", $codCertidao,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtCertidao);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do termo de certidão">Termo</td>
        <td class="field" width="80%">
            <input type="text" name="numTermo" value="<?=$numTermo;?>" size="10" maxlength="10">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do livro de certidão">Livro</td>
        <td class="field" width="80%">
            <input type="text" name="numLivro" value="<?=$numLivro;?>" size="10" maxlength="10">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número da folha de certidão">Folha</td>
        <td class="field" width="80%">
            <input type="text" name="numFolha" value="<?=$numFolha;?>" size="10" maxlength="10">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data da emissão da certidão">Emissão</td>
        <td class="field" width="80%">
            <?php geraCampoData("dataEmissao", $dataEmissao, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de entrada no Brasil no caso de ">Entrada no Brasil</td>
        <td class="field" width="80%">
            <?php geraCampoData("dataEntradaBrasil", $dataEntradaBrasil, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado da certidão">UF</td>
        <td class="field" width="80%">
            <input type="text" name="txtUf" maxlength="6" size="6" value="<?=$uf;?>" onChange ="javascript: preencheCampo(this, document.frm.uf);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("uf", "sw_uf", "cod_uf", "nom_uf", $uf,"style='width: 200px;' onchange='preencheCampo(this, document.frm.txtUf);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Nome do cartório da certidão">Nome do cartório</td>
        <td class="field" width="80%">
            <input type="text" name="nomCartorio" size="80" maxlength="200" value="<?=$nomCartorio;?>">
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Documento de identidade
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Identidade do cidadão">RG</td>
        <td class="field" width="80%">
            <input type="text" name="rg" size="10" maxlength="10" value="<?=$rg;?>" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Complemento da identidade do cidadão">Complemento</td>
        <td class="field" width="80%">
            <input type="text" name="complementoRg" size="20" maxlength="20" value="<?=$complentoRg;?>" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Órgão emissor do RG">Órgão/emissor</td>
        <td class="field" width="80%">
            <input type="text" name="orgaoEmissor" size="20" maxlength="20" value="<?=$orgaoEmissor;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado do RG">UF</td>
        <td class="field" width="80%">
            <input type="text" name="txtUfRg" maxlength="6" size="6" value="<?=$ufRg;?>" onChange ="javascript: preencheCampo(this, document.frm.ufRg);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("ufRg", "sw_uf", "cod_uf", "nom_uf", $uf,"style='width: 200px;' onchange='preencheCampo(this, document.frm.txtUfRg);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de emissão do RG">Emissão</td>
        <td class="field" width="80%">
            <?php geraCampoData("dataEmissaoRg", $dataEmissaoRg, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            CTPS
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número da CTPS">Número</td>
        <td class="field" width="80%">
            <input type="text" name="numCtps" size="10" maxlength="10" value="<?=$numCtps;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Série da CTPS">Série</td>
        <td class="field" width="80%">
            <input type="text" name="serieCtps" size="10" maxlength="10" value="<?=$serieCtps;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Estado da CTPS">UF</td>
        <td class="field" width="80%">
            <input type="text" name="txtUfCtps" maxlength="6" size="6" value="<?=$ufCtps;?>" onChange ="javascript: preencheCampo(this, document.frm.ufCtps);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("ufCtps", "sw_uf", "cod_uf", "nom_uf", $uf,"style='width: 200px;' onchange='preencheCampo(this, document.frm.txtUfCtps);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data da emissão da CTPS">Emissão</td>
        <td class="field" width="80%">
            <?php geraCampoData("dataEmissaoCtps", $dataEmissaoCtps, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Título de eleitor
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Número do título de eleitor">Número</td>
        <td class="field" width="80%">
            <input type="text" name="numTitEleitor" size="10" maxlength="10" value="<?=$numTitEleitor;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Seção do título de eleitor">Seção</td>
        <td class="field" width="80%">
            <input type="text" name="secaoEleitor" size="10" maxlength="10" value="<?=$secaoEleitor;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Zona do título de eleitor">Zona</td>
        <td class="field" width="80%">
            <input type="text" name="zonaEleitor" size="10" maxlength="10" value="<?=$zonaEleitor;?>">
        </td>
    </tr>
    <tr>
        <td colspan="2" class="alt_dados">
            Outros
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CPF do cidadão">CPF</td>
        <td class="field" width="80%">
            <input type="text" maxlength="14" name="cpf" size="14" value="<?=$cpf;?>" onKeyUp="JavaScript: mascaraCPF( this, event );" onKeyPress="return(isValido(this, event, '0123456789'));" onBlur = "JavaScript: if ( !testaCPF( this ) ) {alertaAviso('@CPF inválido!('+this.value+')','form','erro','<?=$sessao->id;?>');} ">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="PIS/PASEP do cidadão">PIS/PASEP</td>
        <td class="field" width="80%">
            <input type="text" name="pis" size="10" maxlength="10" value="<?=$pis;?>">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CBO do cidadão">CBO-R</td>
        <td class="field" width="80%">
            <input type="text" name="cbor" size="10" maxlength="10" value="<?=$cbor;?>">
        </td>
    </tr>
</table>
</form>

<?php
    break;
    case 2:
?>
<input type="hidden" name="aba" value="escolaridade">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Escolaridade do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Instituição educacional do cidadão">Instituição educacional</td>
        <td class="field" width="80%">
            <input type="text" name="txtInstEducacional" maxlength="6" size="6" value="<?=$instEducacional;?>" onChange ="javascript: preencheCampo(this, document.frm.instEducacional);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("instEducacional", "cse.instituicao_educacional", "cod_instituicao", "nom_instituicao", $instEducacional,"style='width: 400px;' onchange='preencheCampo(this, document.frm.txtInstEducacional);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Grau de instrução do cidadão">Grau de instrução</td>
        <td class="field" width="80%">
            <input type="text" name="txtGrauInstrucao" maxlength="6" size="6" value="<?=$grauInstrucao;?>" onChange ="javascript: preencheCampo(this, document.frm.grauInstrucao);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("grauInstrucao", "cse.grau_escolar", "cod_grau", "nom_grau", $grauInstrucao,"style='width: 400px;' onchange='preencheCampo(this, document.frm.txtGrauInstrucao);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Série/frequência na instituição">Série/frequência</td>
        <td class="field" width="80%">
        <input type="text" name="serie" size="10" maxlength="10" value="<?=$serie;?>">&nbsp;/&nbsp;
        <input type="text" name="frequancia" size="10" maxlength="10" value="<?=$frequancia;?>">
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 3:
?>
<input type="hidden" name="aba" value="profissionais">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Informações profissionais do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Empresa em que trabalha">Empresa</td>
        <td class="field" width="80%">
            <input type="text" name="codTxtEmpresa" maxlength="6" size="6" value="<?=$codEmpresa;?>" onChange ="javascript: if ( preencheCampo(this, document.frm.codEmpresa) ) { buscaCNPJ(this); } else { buscaCNPJ(false); }" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codEmpresa", "cse.empresa", "cod_empresa", "nom_empresa", $codEmpresa,"style='width: 200px;' onchange='if ( preencheCampo(this, document.frm.codTxtEmpresa) ) { buscaCNPJ(this); }' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="CNPJ da empresa">CNPJ</td>
        <td class="field" width="80%">
            <input type="text" name="cnpj" maxlength="18" size="18" value="<?=$cnpj;?>" readonly onChange="JavaScript: buscaEmpresa();" onKeyUp="JavaScript: mascaraCNPJ( this, event );return autoTab(this, 18, event);" onKeyPress="return(isValido(this, event, '0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Profissão do cidadão">Profissão</td>
        <td class="field" width="80%">
            <input type="text" name="codTxtProfissao" maxlength="6" size="6" value="<?=$codProfissao;?>" onChange ="javascript: preencheCampo(this, document.frm.codProfissao);" onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codProfissao", "cse.profissao", "cod_profissao", "nom_profissao", $codProfissao,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtProfissao);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">Empregado atualmente</td>
        <td class="field" width="80%">
<?php
    if ( $empregado == "T" or empty($empregado) ) {
        $tChecked = " checked";
        $fChecked = "";
    } else {
        $tChecked = "";
        $fChecked = " checked";
    }

?>
            <input type="radio" name="empregado" value="t"<?=$tChecked;?>>Sim
            <input type="radio" name="empregado" value="f"<?=$fChecked;?>>Não
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Data de admissão na empresa">Admissão</td>
        <td class="field" width="80%">
            <?php geraCampoData("dataAdmissao", $dataAdmissao, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td class="label" width=????" title="Valor do salário">Salário</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrSalario", 10, 2, $vlrSalario );?>
        </td>
    </tr>
    <tr>
        <td class="label" width=????" title="Ocupação">Ocupação</td>
        <td class="field" width="80%">
            <input name="ocupacao" type="text" value="<?=$ocupacao;?>" size="40" maxlength="80">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor da aposentadoria">Aposentadoria</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrAposentadoria", 10, 2, $vlrAposentadoria );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor do seguro-desemprego">Seguro-desemprego</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrSegdesmprego", 10, 2, $vlrSegdesmprego );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor da pensão-alimentícia">Pensão-alimentícia</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrPensaoAlimenticia", 10, 2, $vlrPensaoAlimenticia );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Valor de outras rendas">Outras rendas</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrOutrasRendas", 10, 2, $vlrOutrasRendas );?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 4:
    if ($sessao->transf4['cidadao'][respDomicilio] == "t") {
?>
<input type="hidden" name="aba" value="despesas">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Valores de despesas mensais do cidadão
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com alugel">Aluguel</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrAluguel", 10, 2, $vlrAluguel );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com prestação da casa própia">Prestação habitacional</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespCasaPropria", 10, 2, $vlrDespCasaPropria );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com alimentação">Alimentação</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespAlimentacao", 10, 2, $vlrDespAlimentacao );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com água">Água</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespAgua", 10, 2, $vlrDespAgua );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com energia elétrica">Energia elétrica</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespEnergia", 10, 2, $vlrDespEnergia );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com transporte">Transporte</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespTransporte", 10, 2, $vlrDespTransporte );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com remédios">Remédios</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespRemedio", 10, 2, $vlrDespRemedio );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa com gás">Gás</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespGas", 10, 2, $vlrDespGas );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Despesa diversas">Despesas diversas</td>
        <td class="field" width="80%">
            <?php geraCampoMoeda("vlrDespDiversas", 10, 2, $vlrDespDiversas );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Quantidade de dependentes da renda">Dependentes da renda</td>
        <td class="field" width="80%">
            <?php geraCampoInteiro( "qtdDependentes", 3, 3, $qtdDependentes );?>
        </td>
    </tr>
</table>
</form>
<?php
    }
    break;
    case 5:
?>
<input type="hidden" name="aba" value="vinculo">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Informações de relação familiar
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Grau de parentesco em relação ao responsável">Grau de parentesco</td>
        <td class="field" width="80%">
          <input type="text" size="6" maxlength="6" name="codTxtGrauParentesco" value="<?=$codGrauParentesco?>" onchange='preencheCampo(this, document.frm.codGrauParentesco);' onKeyPress="return(isValido(this,event,'0123456789'));">
<?php
    $combo = montaComboGenerico("codGrauParentesco", "cse.grau_parentesco", "cod_grau", "nom_grau", $codGrauParentesco,"style='width: 200px;' onchange='preencheCampo(this, document.frm.codTxtGrauParentesco);' ","", true, false, false);
    echo $combo;
?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Responsável pela criança, na ausência do responsável">Responsável pela criança</td>
        <td class="field" width="80%">
            <input type="text" name="respCrianca" value="<?=$respCrianca;?>" size="80" maxlength="160">
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Mês de gestação, no caso de gravidez">Mês de gestação</td>
        <td class="field" width="80%">
            <?php geraCampoInteiro( "mesGestacao", 3, 3, $mesGestacao );?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">Amamentando</td>
        <td class="field" width="80%">
<?php
    if ( $amamentando == "true" or empty($amamentando) ) {
        $tChecked = " checked";
        $fChecked = "";
    } else {
        $tChecked = "";
        $fChecked = " checked";
    }

?>
            <input type="radio" name="amamentando" value="true"<?=$tChecked;?>>Sim
            <input type="radio" name="amamentando" value="false"<?=$fChecked;?>>Não
        </td>
    </tr>
    <tr>
        <td class="label" width="20%" title="Quantidade de filhos da gestante">Qtd filhos</td>
        <td class="field" width="80%">
            <?php geraCampoInteiro( "qtdFilhos", 3, 3, $qtdFilhos );?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 6:
?>
<input type="hidden" name="aba" value="censo">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Ano do censo
        </td>
    </tr>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    MAX(TO_NUMBER(exercicio,9999)) AS max_exercicio, ".         $sQuebra;
        $select .= "    MIN(TO_NUMBER(exercicio,9999)) AS min_exercicio ".         $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.questao_censo ".    $sQuebra;
        $select = str_replace("<br>", "", $select );
        $dbConfig->abreSelecao($select);
        $dbConfig->fechaBd();
        $anoMax = $dbConfig->pegaCampo("max_exercicio");
        $anoMin = $dbConfig->pegaCampo("min_exercicio");
?>
    <td class="label" width="20%" title="Ano do Censo">Ano do Censo</td>
        <td class="field" width="80%">
            <select name="anoCenso" style="width:200px" onChange="javascript: mudaAba(6)">
                <option value="xxx">Selecione o ano</option>
<?php
        while ($anoMax >= $anoMin) {
            if ($anoMax == $anoCenso) {
                $sSelected = " selected";
            } else {
                $sSelected = "";
            }
            echo "                <option value=\"".$anoMax."\"".$sSelected.">".$anoMax."</option>\n";
            $anoMax--;
        }
?>
            </select>
        </td>
</table>
<?php
    if ( isset($anoCenso) and $anoCenso != "" ) {
?>
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Questões de censo
        </td>
    </tr>
</table>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    cod_questao, ".         $sQuebra;
        $select .= "    nom_questao, ".         $sQuebra;
        $select .= "    valor_padrao, ".        $sQuebra;
        $select .= "    tipo ".                 $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.questao_censo ".    $sQuebra;
        $select .= " WHERE ".                   $sQuebra;
        $select .= "    exercicio = '".$anoCenso."' ".   $sQuebra;
        //echo $select."<br>";
        $select = str_replace("<br>", "", $select );
        if (!(isset($pagina))) {
            $sessao->transf['select'] = $select;
        }

        $paginacao = new paginacaoLegada;
        $paginacao->complemento ="&ctrl=".$ctrl."&anoCenso=".$anoCenso;
        $paginacao->pegaDados($select,"3");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder(" tipo desc, lower(cod_questao)","ASC");
        $sSQL = $paginacao->geraSQL();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->fechaBd();
        if (!$dbConfig->eof()) {
        echo "    <table width=\"100%\">\n";
        while (!$dbConfig->eof()) {
            switch ( $dbConfig->pegaCampo("tipo") ) {
                case "l":
                $questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]";
                if ($$questCenso) {
                    $valor = $$questCenso;
                }
?>
        <tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
                <select name="questaoCenso[<?=$anoCenso."_".$dbConfig->pegaCampo("cod_questao")?>]" style="width:300px">
<?php
        $options = explode("\n", $dbConfig->pegaCampo("valor_padrao") );
        foreach ($options as $option) {
            $option = trim($option);
            //if ( $questaoCenso[$dbConfig->pegaCampo("cod_questao")] ==  $option ) {
            if ($valor ==  $option) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            if ($option) {
                echo " <option value=\"".$option."\"".$selected.">".$option."</option>\n";
            }

        }
?>
                </select>
            </td>
        </tr>
<?php
                break;
                case "m":
//$questaoCenso[$dbConfig->pegaCampo("cod_questao")][$chkValue]
?>
        <tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
<?php
        $chkValues = explode("\n", $dbConfig->pegaCampo("valor_padrao") );
        foreach ($chkValues as $chkValue) {
            $chkValue = trim($chkValue);
            //if ($valor ==  $chkValue) {
            $questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."][".$chkValue."]";
            if ($$questCenso) {
                $checked = " checked";
            } else {
                $checked = "";
            }
            if ($chkValue) {
                echo "                    <input name=\"questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."][".$chkValue."]\" type=\"checkbox\" value=\"".$chkValue."\"".$checked.">".$chkValue."<br>\n";
            }
        }
?>
                <input type="hidden" name="questaoCenso[<?=$anoCenso."_".$dbConfig->pegaCampo("cod_questao");?>][]" value="">
            </td>
        </tr>
<?php
                break;
                case "n":
//$questaoCenso[$dbConfig->pegaCampo("cod_questao")]
$questCenso = "questaoCenso[".$dbConfig->pegaCampo("cod_questao")."]";
if ($$questCenso) {
    $valor = $$questCenso;
} else {
    $valor = $dbConfig->pegaCampo("valor_padrao");
}
?>
        </tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
                <?php geraCampoInteiro( "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]", 100, 10, $valor ); ?>
            </td>
        </tr>
<?php
                break;
                case "t":
?>
        </tr>
            <td class="label" width="20%">
                <?=$dbConfig->pegaCampo("nom_questao")?>
            </td>
            <td class="field" width="80%">
<?php
$questCenso = "questaoCenso[".$anoCenso."_".$dbConfig->pegaCampo("cod_questao")."]";
if ($$questCenso) {
    $valor = $$questCenso;
} else {
    $valor = $dbConfig->pegaCampo("valor_padrao");
}
?>
                <textarea cols="200" rows="5" name="questaoCenso[<?=$anoCenso."_".$dbConfig->pegaCampo("cod_questao");?>]"><?=$valor;?></textarea>
            </td>
        </tr>
<?php
                break;
            }
        $dbConfig->vaiProximo();
        }
    echo "    </table>\n";
    }
?>
<script type="text/javascript">
<!--
function paginacao(linkPagina)
{
    document.frm.pagina.value = linkPagina;
    document.frm.target = "telaPrincipal";
    document.frm.ctrl.value = "6";
    document.frm.submit();
}
//-->
</script>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
                <input type="hidden" name="pagina" value="">
<?php
$stPaginacao = strip_tags($paginacao->aux);
$arPaginacao = preg_split( "/\|/", $stPaginacao);
foreach ($arPaginacao as $novoLink) {
    if ( trim($novoLink) == "Anterior" ) {
        $sLinkPagina .= "<a href=\"javascript: paginacao(".(string) --$paginacao->pagina.");\">Anterior</a>\n";
    } elseif (trim($novoLink) == "Próxima" ) {
        $sLinkPagina .= " | <a href=\"javascript: paginacao(".(string) $inProximaPagina.");\">Próxima</a>\n";
    } elseif ( trim($novoLink) != "" ) {
        $novoLink = trim($novoLink) - 1 ;
        if ($pagina  == $novoLink) {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\" style=\"color: red\">".++$novoLink."</a>\n";
            $inProximaPagina = $novoLink;
        } else {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\">".++$novoLink."</a>\n";
        }
    }
}
echo $sLinkPagina;
?>
            </font>
        </td>
    </tr>
</table>
</form>
<?php
    }
    $dbConfig->limpaSelecao();
    break;
    case 7:
        if ( empty( $pagina ) ) {
            $pagina = 0;
        }
?>
<input type="hidden" name="aba" value="programas">
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Ano do programa
        </td>
    </tr>
<?php
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                  $sQuebra;
        $select .= "    MAX(TO_NUMBER(exercicio,9999)) AS max_exercicio, ".         $sQuebra;
        $select .= "    MIN(TO_NUMBER(exercicio,9999)) AS min_exercicio ".         $sQuebra;
        $select .= " FROM ".                    $sQuebra;
        $select .= "    cse.programa_social ".    $sQuebra;
        $select = str_replace("<br>", "", $select );
        $dbConfig->abreSelecao($select);
        $dbConfig->fechaBd();
        $anoMax = $dbConfig->pegaCampo("max_exercicio");
        $anoMin = $dbConfig->pegaCampo("min_exercicio");
?>
    <td class="label" width="20%" title="Ano do Programa">Ano do Programa</td>
        <td class="field" width="80%">
            <select name="anoPrograma" style="width:200px" onChange="javascript: mudaAba(7)">
                <option value="xxx">Selecione o ano</option>
<?php
        while ($anoMax >= $anoMin) {
            if ($anoMax == $anoPrograma) {
                $sSelected = " selected";
            } else {
                $sSelected = "";
            }
            echo "                <option value=\"".$anoMax."\"".$sSelected.">".$anoMax."</option>\n";
            $anoMax--;
        }
?>
            </select>
        </td>
</table>
<table width="100%">
    <tr>
        <td colspan="2" class="alt_dados">
            Participação em programas sociais
        </td>
    </tr>
<?php
    if ( isset($anoPrograma) and $anoPrograma != "xxx" ) {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sQuebra = "<br>";
        $select  = " SELECT ".                              $sQuebra;
        $select .= "    cod_programa, ".                    $sQuebra;
        $select .= "    exercicio, ".                       $sQuebra;
        $select .= "    nom_programa, ".                    $sQuebra;
        $select .= "    descricao ".                        $sQuebra;
        $select .= " FROM ".                                $sQuebra;
        $select .= "    cse.programa_social ".          $sQuebra;
        $select .= " WHERE ".                               $sQuebra;
        $select .= "    exercicio = '".$anoPrograma."' ".   $sQuebra;
        //echo $select."<br>";
        $select = str_replace("<br>", "", $select );
        if (!(isset($pagina))) {
            $sessao->transf['select'] = $select;
        }

        $paginacao = new paginacaoLegada;
        //$paginacao->complemento ="&ctrl=".$ctrl."&anoCenso=".$anoCenso;
        $paginacao->pegaDados($select,"3");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder(" exercicio, lower(cod_programa)","ASC");
        $sSQL = $paginacao->geraSQL();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->fechaBd();
        if (!$dbConfig->eof()) {
            while (!$dbConfig->eof()) {
?>
    <tr>
        <td class="alt_dados" colspan="2">
<?php
$participProgSociais = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_cod]";

if ($$participProgSociais) {
    $valor = $$participProgSociais;
} else {
    $valor = "";
}

if ($valor == "true") {
    $checked = " checked";
} else {
    $checked = "";
}
?>
            <input type="checkbox" name="ps[<?=$anoPrograma;?>_<?=$dbConfig->pegaCampo("cod_programa");?>_cod]" value="true"<?=$checked;?>>&nbsp;<?=$dbConfig->pegaCampo("nom_programa");?>
        </td>
    </tr>
    <tr>
        <td class="label" width="20%">
            Data de inclusão
        </td>
        <td class="field" width="80%">
<?php
$dataIncProg = "ps".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_di";
if ($$dataIncProg) {
    $valor = $$dataIncProg;
} else {
    $valor = "";
}

?>

            <?php geraCampoData("ps".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_di", $valor, false, "onKeyPress=\"return(isValido(this, event, '0123456789'));\" onKeyUp=\"mascaraData(this, event);\" onBlur=\"JavaScript: if ( !verificaData(this) ) {alertaAviso('@Data inválida!('+this.value+')','form','erro','$sessao->id');};\"" );?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Benefício
        </td>
        <td class="field">
<?php
$vlrBenef = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_vl]";
if ($$vlrBenef) {
    $valor = $$vlrBenef;
} else {
    $valor = "";
}
?>
            <?php geraCampoMoeda("ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_vl]", 10, 2, $valor );?>
        </td>
    </tr>
    <tr>
        <td class="label">
            Beneficiário prioritário
        </td>
        <td class="field">
<?php
$benPrior = "ps[".$anoPrograma."_".$dbConfig->pegaCampo("cod_programa")."_bp]";
if ($$benPrior) {
    $valor = $$benPrior;
} else {
    $valor = "";
}

if ($valor == "true") {
    $checked = " checked";
} else {
    $checked = "";
}
?>
            <input type="checkbox" name="ps[<?=$anoPrograma;?>_<?=$dbConfig->pegaCampo("cod_programa");?>_bp]" value="true"<?=$checked;?>>
        </td>
    </tr>
<?php
                $dbConfig->vaiProximo();
            }
        }
    }
?>
</table>
<script type="text/javascript">
<!--
function paginacao(linkPagina)
{
    document.frm.pagina.value = linkPagina;
    document.frm.target = "telaPrincipal";
    document.frm.ctrl.value = "7";
    document.frm.submit();
}
//-->
</script>
<table width="450" align="center">
    <tr>
        <td align="center">
            <font size=2>
                <input type="hidden" name="pagina" value="">
<?php
$stPaginacao = strip_tags($paginacao->aux);
$arPaginacao = preg_split( "/\|/", $stPaginacao);
foreach ($arPaginacao as $novoLink) {
    if ( trim($novoLink) == "Anterior" ) {
        $sLinkPagina .= "<a href=\"javascript: paginacao(".(string) --$paginacao->pagina.");\">Anterior</a>\n";
    } elseif (trim($novoLink) == "Próxima" ) {
        $sLinkPagina .= " | <a href=\"javascript: paginacao(".(string) $inProximaPagina.");\">Próxima</a>\n";
    } elseif ( trim($novoLink) != "" ) {
        $novoLink = trim($novoLink) - 1 ;
        if ($pagina  == $novoLink) {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\" style=\"color: red\">".++$novoLink."</a>\n";
            $inProximaPagina = $novoLink;
        } else {
            $sLinkPagina .=  " | <a href=\"javascript: paginacao(".(string) ($novoLink).");\">".++$novoLink."</a>\n";
        }
    }
}
echo $sLinkPagina;
?>
            </font>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 8:
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $select  = " SELECT ";
    $select .= "     cod_domicilio, ";
    $select .= "     logradouro, ";
    $select .= "     numero, ";
    $select .= "     complemento ";
    $select .= " FROM ";
    $select .= "     cse.domicilio ";
    $select .= " WHERE ";
    $select .= "     cod_domicilio = ".$_POST['codDomicilio']." ";
    $dbConfig->abreSelecao( $select );
    $dbConfig->fechaBd();
    $js  = "";
    if ( !$dbConfig->eof() ) {
        $codDomicilio   = $dbConfig->pegaCampo("cod_domicilio");
        $logradouro     = $dbConfig->pegaCampo("logradouro");
        $numero         = $dbConfig->pegaCampo("numero");
        $complemento    = $dbConfig->pegaCampo("complemento");
        $nomDomicilio = $logradouro;
        if ($numero) {
            $nomDomicilio .= " - ".$numero;
        }
        if ($complemento) {
            $nomDomicilio .= " - ".$complemento;
        }
        $js .= "f.nomDomicilio.value = '".$nomDomicilio."';\n";
    } else {
        $js .= "f.nomDomicilio.value = '';\n";
        $js .= "alertaAviso('Campo Logradouro inválido!(".$_POST['codDomicilio'].")','frm','erro','".$sessao->id."');\n";
    }
    sistemaLegado::executaFrameOculto($js);
    break;
    case 9:
        $cnpj = pegaDado("cnpj","cse.empresa"," WHERE cod_empresa = ".$codEmpresa );
        $js .= "f.cnpj.value = '".geraMascaraCNPJ( $cnpj )."';\n";
        sistemaLegado::executaFrameOculto($js);
    break;
    case 10:
        $incluir = new cse;
        $retorno = $incluir->incluiCidadao();
        if ($retorno) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, "Cidadão: ".$sessao->transf4[cidadao][codCidadao]);
            $audicao->insereAuditoria();
            //unset($sessao->transf4);

//			alertaAviso($PHP_SELF,$sessao->transf4[cidadao][nomCgm],"incluir","aviso");
            sistemaLegado::alertaAviso($PHP_SELF,$sessao->transf4[cidadao][nomCgm],"incluir","aviso","<?=$sessao->id?>");

        } else {
            sistemaLegado::exibeAviso($sessao->transf4[cidadao][nomCgm],"n_incluir","erro");
        }
    break;
    case 11:
        $sSQL  = " SELECT ";
        $sSQL .= "     numcgm, ";
        $sSQL .= "     nom_cgm ";
        $sSQL .= " FROM ";
        $sSQL .= "     sw_cgm ";
        $sSQL .= " WHERE ";
        $sSQL .= "     numcgm > 0 AND ";
        $sSQL .= "     numcgm = ".$_POST['numCgm']." ";
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBD();
        $dbConfig->abreSelecao($sSQL);
        $dbConfig->vaiPrimeiro();
        $dbConfig->fechaBd();
        $js  = "";
        if ( !$dbConfig->eof() ) {
            $numcgm   = $dbConfig->pegaCampo("numcgm");
            $nomcgm   = $dbConfig->pegaCampo("nom_cgm");
            $js .= "f.numCgm.value = '".$numcgm."';\n";
            $js .= "f.nomCgm.value = '".$nomcgm."';\n";
        } else {
            $js .= "f.nomCgm.value = '';\n";
            $js .= "alertaAviso('Campo CGM inválido!(".$_POST['numCgm'].")','frm','erro','".$sessao->id."');\n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    case 12:
        if ( strtoupper( $estado ) != 'XXX' ) {
            $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$estado." ORDER by nom_municipio";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboMunicipio = "";
            $iCont = 1;
            $js .= "f.txtMunicipio.value = '';\n";
            $js .= "limpaSelect(f.municipio,1); \n";
            while (!$dbEmp->eof()) {
                $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                $dbEmp->vaiProximo();
                $js .= "f.municipio.options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
            }
            if ($iCont > 1) {
                $js .= "f.txtMunicipio.disabled = false;\n";
                $js .= "f.municipio.disabled = false;\n";
                $js .= "f.txtMunicipio.focus ();\n";
            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js .= "limpaSelect(f.municipio,1); \n";
            $js .= "f.txtMunicipio.value = '';\n";
            $js .= "f.txtMunicipio.disabled = true;\n";
            $js .= "f.municipio.disabled = true;\n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
}
?>
