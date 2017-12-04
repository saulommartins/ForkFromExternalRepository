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
* Arquivo de instância para Domicilio
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 19067 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-03 09:33:57 -0200 (Qua, 03 Jan 2007) $

* Casos de uso: uc-01.07.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
  include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
  include_once '../cse.class.php';
  include_once (CAM_FW_LEGADO."configuracaoLegado.class.php");

    if (!(isset($ctrl)))
        $ctrl = 0;
    switch ($ctrl) {
        case 0:
        $codDomicilio = pegaID('cod_domicilio',"cse.domicilio");
        $codMunicipio = pegaConfiguracao("cod_municipio");
        $codUf = pegaConfiguracao("cod_uf");
        $configuracao = new configuracaoLegado;
        $configuracao->setaEstadoAtual($codUf);
        $configuracao->listaComboEstados();
?>
<script type="text/javascript">

      function atualizaMunicipio()
      {
        document.frm.target = "oculto";
        document.frm.ctrl.value = 2;
        document.frm.submit();
      }

      function zeraMunicipio()
      {
        limpaSelect(document.frm.municipio,1);
        document.frm.txtMunicipio.disabled = true;
        document.frm.municipio.disabled = true;
      }

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.cep.value;
        if (campo == "") {
            mensagem += "@Campo CEP inválido!()";
            erro = true;
        } else {
            campoLen = document.frm.cep.value.length;
            if (campoLen < 9) {
                mensagem += "@Campo CEP inválido!("+campo+")";
                erro = true;
            }
        }

        campo = document.frm.estado.value;
        if (campo == "xxx") {
            mensagem += "@Campo Estado inválido!()";
            erro = true;
         }

        campo = document.frm.municipio.value;
            if (campo == "xxx") {
            mensagem += "@Campo Múnicípio inválido!()";
            erro = true;
         }

        campo = document.frm.codTipoLogradouro.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo logradouro inválido!()";
            erro = true;
         }

         campo = document.frm.logradouro.value;
            if (campo == "") {
            mensagem += "@Campo Logradouro inválido!()";
            erro = true;
         }

         campo = document.frm.numero.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Número inválido!("+campo+")";
            erro = true;
         }

         campo = document.frm.codLocalidade.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo de localidade inválido!()";
            erro = true;
         }

         campo = document.frm.codTipoDomicilio.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo de domicílio inválido!()";
            erro = true;
         }

         campo = document.frm.codConstrucao.value;
            if (campo == "xxx") {
            mensagem += "@Campo Tipo de construção inválido!()";
            erro = true;
         }

         campo = document.frm.codSituacao.value;
            if (campo == "xxx") {
            mensagem += "@Campo Situação do domicílio inválido!()";
            erro = true;
         }

         campo = document.frm.qtdComodos.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Quantidade de cômodos inválido!("+campo+")";
            erro = true;
         }

         campo = document.frm.qtdResidentes.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Residentes inválido!("+campo+")";
            erro = true;
         }

         campo = document.frm.qtdGravidas.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Mulheres Grávidas inválido!("+campo+")";
            erro = true;
         }

         campo = document.frm.qtdMaesAmamentando.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Mães amamentando inválido!("+campo+")";
            erro = true;
         }

         campo = document.frm.qtdDeficientes.value;
            if (isNaN(campo)) {
            mensagem += "@Campo Deficientes inválido!("+campo+")";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=$sessao->id?>','');
                return !(erro);
      }

      function Salvar()
      {
         if (Valida()) {
            document.frm.target = "telaPrincipal";
            document.frm.ctrl.value = 1;
            document.frm.submit();
         }
      }
</script>

<form action="<?=$PHP_SELF?>?<?=$sessao->id?>" method="POST" name="frm" target="oculto">
<input type="hidden" name="codDomicilio" value="<?=$codDomicilio?>">
<input type="hidden" name="ctrl" value="">
<table width="100%">
    <tr>
        <td class="alt_dados" colspan="2">Dados do Domicílio</td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Localização</td>
    </tr>
    <tr>
        <td class="label" title="Cep do logradouro">*CEP</td>
        <td class="field">
            <input type="text" name="cep" value="<?=$cep;?>" size="9" maxlength="9" onKeyUp="mascaraCEP(this, event);return autoTab(this, 9, event); " onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" width="30%" title="Uf do logradouro">*Estado</td>
        <td class="field" width="70%">
            <input type="text" name="txtEstado" maxlength="4" size="4" value="" onChange ="javascript: if ( preencheCampo(this, document.frm.estado) ) { atualizaMunicipio() } else { zeraMunicipio(); };" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="estado" onChange="javascript: preencheCampo(this, document.frm.txtEstado);atualizaMunicipio()" style="width: 300px">
                    <option value="xxx">Selecione um estado</option>
<?php
    $sSQL = "SELECT * FROM sw_uf WHERE cod_uf > 0 ORDER by nom_uf";
    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $comboEstado = "";
    while (!$dbEmp->eof()) {
        $codg_uf  = trim($dbEmp->pegaCampo("cod_uf"));
        $nomg_uf  = trim($dbEmp->pegaCampo("nom_uf"));
        $dbEmp->vaiProximo();
        if ($dadosCgm['codUf'] == $codg_uf) {
            $comboEstado .= "                    <option value=".$codg_uf." selected>".$nomg_uf."</option>\n";
        } else {
            $comboEstado .= "                    <option value=".$codg_uf.">".$nomg_uf."</option>\n";
        }
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo "$comboEstado";
?>
            </select>
            <input type="hidden" name="cod_uf" value="<?=$codEstado;?>">
            <input type="hidden" name="cod_municipio" value="">
        </td>
    </tr>
    <tr>
        <td class="label" title="Município do logradouro">*Município</td>
        <td class="field">
            <?php
    $stDisabled = " disabled";
    if ( !empty($dadosCgm['codMunicipio'])) {
        $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$dadosCgm['codUf']." ORDER by nom_municipio";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboMunicipio = "";
        while (!$dbEmp->eof()) {
            $stDisabled = "";
            $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
            $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
            $dbEmp->vaiProximo();
            $comboMunicipio .= "                    ";
            $comboMunicipio .= "<option value='".$codg_municipio."'";
            if ($codg_municipio == $dadosCgm['codMunicipio']) {
                $comboMunicipio .= " SELECTED";
            }
            $comboMunicipio .= ">".$nomg_municipio."</option>\n";
        }
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }
?>
                <input type="text" name="txtMunicipio" maxlength="4" size="4" value="" <?=$stDisabled;?> onChange="JavaScript: preencheCampo(this, document.frm.municipio);" onKeyPress="return(isValido(this,event,'0123456789'));">
                <select name="municipio" style="width: 300px"<?=$stDisabled;?>  onChange="JavaScript: preencheCampo(this, document.frm.txtMunicipio);">
                    <option value="xxx">Selecione uma cidade</option>
<?=$comboMunicipio;?>
                </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo do logradouro">*Tipo Logradouro</td>
        <td class="field">
            <input type="text" name="codTxtTipoLogradouro" maxlength="4" size="4" value="<?=$codTipoLogradouro?>" onChange="javascript: preencheCampo(this, document.frm.codTipoLogradouro);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codTipoLogradouro" onChange="javascript: preencheCampo(this, document.frm.codTxtTipoLogradouro);">
                <option value="xxx">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                   .$stQuebra;
$select .= "     cod_tipo, "            .$stQuebra;
$select .= "     nom_tipo "             .$stQuebra;
$select .= " FROM "                     .$stQuebra;
$select .= "     sw_tipo_logradouro "  .$stQuebra;
$select .= " WHERE "                    .$stQuebra;
$select .= "     cod_tipo > 0 "         .$stQuebra;
$select .= " ORDER BY "                 .$stQuebra;
$select .= "     nom_tipo "             .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_tipo")."/".$dbConfig->pegaCampo("nom_tipo");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Nome aplicado ao logradouro">*Logradouro</td>
        <td class="field">
            <input type="text" name="logradouro" value="<?=$logradouro?>" size="30" maxlength="60">
        </td>
    </tr>
    <tr>
        <td class="label" title="Número do logradouro">Número</td>
        <td class="field">
            <input type="text" name="numero" value="<?=$numero;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="label" title="Complemento do endereço">Complemento</td>
        <td class="field">
            <input type="text" name="complemento" value="<?=$complemento;?>" size="30" maxlength="60">
        </td>
    </tr>
    <tr>
        <td class="label" title="Bairro do logradouro">Bairro</td>
        <td class="field">
            <input type="text" name="bairro" value="<?=$bairro;?>" size="30" maxlength="60">
        </td>
    </tr>
    <tr>
        <td class="label" title="Fone do logradouro">Fone</td>
        <td class="field">
            <input type="text" name="telefone" value="<?=$telefone;?>" size="10" maxlength="10" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Características</td>
    </tr>
    <tr>
        <td class="label" title="Tipo de localidade">*Tipo de Localidade</td>
        <td class="field">
            <input type="text" name="codTxtLocalidade" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codLocalidade);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codLocalidade" onChange="javascript: preencheCampo(this, document.frm.codTxtLocalidade);">
                <option value="xxx">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                       .$stQuebra;
$select .= "     cod_localidade, "          .$stQuebra;
$select .= "     nom_localidade "           .$stQuebra;
$select .= " FROM "                         .$stQuebra;
$select .= "     cse.tipo_localidade "  .$stQuebra;
$select .= " WHERE "                        .$stQuebra;
$select .= "     cod_localidade > 0 "       .$stQuebra;
$select .= " ORDER BY "                     .$stQuebra;
$select .= "     nom_localidade "           .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_localidade")."/".$dbConfig->pegaCampo("nom_localidade");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de domicílio">*Tipo de Domicílio</td>
        <td class="field">
            <input type="text" name="codTxtTipoDomicilio" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codTipoDomicilio);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codTipoDomicilio" onChange="javascript: preencheCampo(this, document.frm.codTxtTipoDomicilio);">
                <option value=xxx>Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                       .$stQuebra;
$select .= "     cod_domicilio, "           .$stQuebra;
$select .= "     nom_domicilio "            .$stQuebra;
$select .= " FROM "                         .$stQuebra;
$select .= "     cse.tipo_domicilio "   .$stQuebra;
$select .= " WHERE "                        .$stQuebra;
$select .= "     cod_domicilio > 0 "        .$stQuebra;
$select .= " ORDER BY "                     .$stQuebra;
$select .= "     nom_domicilio "            .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_domicilio")."/".$dbConfig->pegaCampo("nom_domicilio");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de construção">*Tipo de Construção</td>
        <td class="field">
            <input type="text" name="codTxtConstrucao" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codConstrucao);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codConstrucao" onChange="javascript: preencheCampo(this, document.frm.codTxtConstrucao);">
                <option value="xxx">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                       .$stQuebra;
$select .= "     cod_construcao, "          .$stQuebra;
$select .= "     nom_construcao "           .$stQuebra;
$select .= " FROM "                         .$stQuebra;
$select .= "     cse.tipo_construcao "  .$stQuebra;
$select .= " WHERE "                        .$stQuebra;
$select .= "     cod_construcao > 0 "       .$stQuebra;
$select .= " ORDER BY "                     .$stQuebra;
$select .= "     nom_construcao "           .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_construcao")."/".$dbConfig->pegaCampo("nom_construcao");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Situação de uso do domicílio">*Situação do Domicílio</td>
        <td class="field">
            <input type="text" name="codTxtSituacao" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codSituacao);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codSituacao" onChange="javascript: preencheCampo(this, document.frm.codTxtSituacao);">
                <option value="xxx">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                           .$stQuebra;
$select .= "     cod_situacao, "                .$stQuebra;
$select .= "     nom_situacao "                 .$stQuebra;
$select .= " FROM "                             .$stQuebra;
$select .= "     cse.situacao_domicilio "   .$stQuebra;
$select .= " WHERE "                            .$stQuebra;
$select .= "     cod_situacao > 0 "             .$stQuebra;
$select .= " ORDER BY "                         .$stQuebra;
$select .= "     nom_situacao "                 .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_situacao")."/".$dbConfig->pegaCampo("nom_situacao");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
        <tr>
        <td class="label" title="Quantidade de cômodos do logradouro">Quantidade de Cômodos</td>
        <td class="field">
            <input type="text" name="qtdComodos" value="<?=$qtdComodos;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Quantidades</td>
    </tr>
    <tr>
        <td class ="" colspan="2" border="0">
            <table width="100%">
                <tr>
                    <td class="label" width="30%">Residentes</td>
                    <td class="field" width="20%">
                        <input type="text" name="qtdResidentes" value="<?=$qtdResidentes;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
                    </td>
                    <td class="label" width="30%">Mulheres Grávidas</td>
                    <td class="field" width="20%">
                        <input type="text" name="qtdGravidas" value="<?=$qtdGravidas;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
                    </td>
                </tr>
                <tr>
                    <td class="label">Mães Amamentando</td>
                    <td class="field">
                        <input type="text" name="qtdMaesAmamentando" value="<?=$qtdMaesAmamentando;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
                    </td>
                    <td class="label">Deficientes</td>
                    <td class="field">
                        <input type="text" name="qtdDeficientes" value="<?=$qtdDeficientes;?>" size="6" maxlength="6" onKeyPress="return(isValido(this,event,'0123456789'));">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="alt_dados" colspan="2">Serviços</td>
    </tr>
    <tr>
        <td class="label">*Energia Elétrica</td>
        <td class="field">
            <input type="radio" checked name="energia" value="t">Sim
            <input type="radio" name="energia" value="f">Não
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de cobertura do domicílio">Cobertura</td>
        <td class="field">
            <input type="text" name="codTxtCobertura" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codCobertura);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codCobertura" onChange="javascript: preencheCampo(this, document.frm.codTxtCobertura);">
                <option value="0">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                       .$stQuebra;
$select .= "     cod_cobertura, "           .$stQuebra;
$select .= "     nom_cobertura "            .$stQuebra;
$select .= " FROM "                         .$stQuebra;
$select .= "     cse.tipo_cobertura "   .$stQuebra;
$select .= " WHERE "                        .$stQuebra;
$select .= "     cod_cobertura > 0 "        .$stQuebra;
$select .= " ORDER BY "                     .$stQuebra;
$select .= "     nom_cobertura "            .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_cobertura")."/".$dbConfig->pegaCampo("nom_cobertura");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo do abastecimento do domicílio">Abastecimento</td>
        <td class="field">
            <input type="text" name="codTxtAbastecimento" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codAbastecimento);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codAbastecimento" onChange="javascript: preencheCampo(this, document.frm.codTxtAbastecimento);">
                <option value="0">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                           .$stQuebra;
$select .= "     cod_abastecimento, "           .$stQuebra;
$select .= "     nom_abastecimento "            .$stQuebra;
$select .= " FROM "                             .$stQuebra;
$select .= "     cse.tipo_abastecimento "   .$stQuebra;
$select .= " WHERE "                            .$stQuebra;
$select .= "     cod_abastecimento > 0 "        .$stQuebra;
$select .= " ORDER BY "                         .$stQuebra;
$select .= "     nom_abastecimento "            .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_abastecimento")."/".$dbConfig->pegaCampo("nom_abastecimento");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de tratamento de água">Tratamento de Água</td>
        <td class="field">
            <input type="text" name="codTxtTratamentoAgua" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codTratamentoAgua);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codTratamentoAgua" onChange="javascript: preencheCampo(this, document.frm.codTxtTratamentoAgua);">
                <option value="0">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                           .$stQuebra;
$select .= "     cod_tratamento, "              .$stQuebra;
$select .= "     nom_tratamento "               .$stQuebra;
$select .= " FROM "                             .$stQuebra;
$select .= "     cse.tipo_tratamento_agua " .$stQuebra;
$select .= " WHERE "                            .$stQuebra;
$select .= "     cod_tratamento > 0 "           .$stQuebra;
$select .= " ORDER BY "                         .$stQuebra;
$select .= "     nom_tratamento "               .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_tratamento")."/".$dbConfig->pegaCampo("nom_tratamento");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Tipo de esgotamento">Esgotamento</td>
        <td class="field">
            <input type="text" name="codTxtEsgotamento" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codEsgotamento);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codEsgotamento" onChange="javascript: preencheCampo(this, document.frm.codTxtEsgotamento);">
                <option value=0>Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                           .$stQuebra;
$select .= "     cod_esgotamento, "             .$stQuebra;
$select .= "     nom_esgotamento "              .$stQuebra;
$select .= " FROM "                             .$stQuebra;
$select .= "     cse.tipo_esgotamento "     .$stQuebra;
$select .= " WHERE "                            .$stQuebra;
$select .= "     cod_esgotamento > 0 "          .$stQuebra;
$select .= " ORDER BY "                         .$stQuebra;
$select .= "     nom_esgotamento "              .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_esgotamento")."/".$dbConfig->pegaCampo("nom_esgotamento");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="label" title="Forma de tratamento de lixo">Tratamento de lixo</td>
        <td class="field">
            <input type="text" name="codTxtDestinoLixo" size="4" maxlength="4" onChange="javascript: preencheCampo(this, document.frm.codDestinoLixo);" onKeyPress="return(isValido(this,event,'0123456789'));">
            <select name="codDestinoLixo" onChange="javascript: preencheCampo(this, document.frm.codTxtDestinoLixo);">
                <option value="0">Selecione uma opção</option>
<?php
$dbConfig = new dataBaseLegado;
$dbConfig->abreBd();
$stQuebra = "<br>";
$select  = "";
$select .= " SELECT "                           .$stQuebra;
$select .= "     cod_destino_lixo, "            .$stQuebra;
$select .= "     nom_destino_lixo "             .$stQuebra;
$select .= " FROM "                             .$stQuebra;
$select .= "     cse.tipo_destino_lixo "    .$stQuebra;
$select .= " WHERE "                            .$stQuebra;
$select .= "     cod_destino_lixo > 0 "         .$stQuebra;
$select .= " order by "                         .$stQuebra;
$select .= "     nom_destino_lixo "             .$stQuebra;
//echo $select;
$select = str_replace( "<br>", "", $select );
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
    $lista[] = $dbConfig->pegaCampo("cod_destino_lixo")."/".$dbConfig->pegaCampo("nom_destino_lixo");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
if ($lista != "") {
    while (list($key, $val) = each($lista)) {
        $combo = explode("/", $val);
        echo "                <option value=".$combo[0].">".$combo[1]."</option>\n";
    }
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="field" colspan="2">
            <?php geraBotaoOk();?>
        </td>
    </tr>
</table>
</form>
<?php
    break;
    case 1:
        $padrao = "Não Informado";
        if ($numero == "") {
            $numero = "''";
        }
        if ($qtdComodos == "") {
            $qtdComodos = 0;
        }

        if ($qtdResidentes == "") {
            $qtdResidentes = 0;
        }

        if ($qtdDeficientes == "") {
            $qtdDeficientes = 0;
        }

        if ($qtdMaesAmamentando == "") {
            $qtdMaesAmamentando = 0;
        }

        if ($qtdGravidas == "") {
            $qtdGravidas = 0;
        }
        $cep = str_replace("-","",$cep);
        $incluir = new cse;
        $var = array(
        codDomicilio        =>  $codDomicilio,
        codEstado           =>  $estado,
        codMunicipio        =>  $municipio,
        codLocalidade       =>  $codLocalidade,
        codTipoDomicilio    =>  $codTipoDomicilio,
        codConstrucao       =>  $codConstrucao,
        codSituacao         =>  $codSituacao,
        codTipoLogradouro   =>  $codTipoLogradouro,
        logradouro          =>  $logradouro,
        numero              =>  $numero,
        complemento         =>  $complemento,
        bairro              =>  $bairro,
        cep                 =>  $cep,
        telefone            =>  $telefone,
        qtdComodos          =>  $qtdComodos,
        energia             =>  $energia,
        qtdResidentes       =>  $qtdResidentes,
        qtdGravidas         =>  $qtdGravidas,
        qtdMaesAmamentando  =>  $qtdMaesAmamentando,
        qtdDeficientes      =>  $qtdDeficientes,
        codCobertura        =>  $codCobertura,
        codAbastecimento    =>  $codAbastecimento,
        codTratamentoAgua   =>  $codTratamentoAgua,
        codEsgotamento      =>  $codEsgotamento,
        codDestinoLixo      =>  $codDestinoLixo
        );
        //print_r($var);
        //exit();
        if ($incluir->incluiDomicilio($var)) {
            include CAM_FW_LEGADO."auditoriaLegada.class.php";
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria($sessao->numCgm, $sessao->acao, $var[logradouro]."-".$var[numero]."-".$var[complemento]);
            $audicao->insereAuditoria();
            echo '
            <script type="text/javascript">
            alertaAviso("'.$var[codDomicilio].' - '.$var[logradouro].'","incluir","aviso","'.$sessao->id.'","");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.$sessao->id.'");
            </script>';
        } else {
            echo '
            <script type="text/javascript">
            alertaAviso("'.$var[logradouro].'","n_incluir","erro","'.$sessao->id.'","");
            </script>';
        }
    break;
    case 2:
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
        executaFrameOculto($js);
    break;
}
    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
