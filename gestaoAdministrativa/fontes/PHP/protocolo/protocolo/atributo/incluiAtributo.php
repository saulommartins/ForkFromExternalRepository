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
* Arquivo de implementação de manutenção de atributo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24720 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:55:40 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.93
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/atributoLegado.class.php");
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.93');

if (!(isset($_REQUEST["ctrl"]))) {
   $ctrl = 0;
} else {
   $ctrl = $_REQUEST["ctrl"];
}

switch ($ctrl) {
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
   var f;

   f = document.frm;

   campo = trim( f.nomAtributo.value );
   if (campo == "") {
      mensagem += "@Descrição";
      erro = true;
   }

   campo = f.tipo.value;
   if (campo == "xxx") {
      mensagem += "@Tipo";
      erro = true;
   }

   if (erro) {
      alertaAviso(mensagem,'formulario','','<?=Sessao::getId()?>');
   }

   return !(erro);
}

function Salvar()
{
    var f = document.frm;
    f.ok.disabled = true;
    if (Valida()) {
       f.submit();
    } else {
        f.ok.disabled = false;
    }
}

//-->
</script>
<form name="frm" action="incluiAtributo.php?<?=Sessao::getId();?>" method="POST" target="oculto" onSubmit='return Valida();'>
<input type="hidden" value="1" name="ctrl">
<table width=100%>
   <tr>
      <td class=alt_dados colspan=2>Dados para atributo</td>
   </tr>
   <tr>
      <td class=label width=30% title="Descrição do atributo">*Descrição</td>
      <td class=field><input type=text name=nomAtributo size=60 maxlength=60></td>
   </tr>
   <tr>
      <td class=label title="Tipo de registro">*Tipo</td>
      <td class=field>
         <select name="tipo" style="width: 200px;">
            <option value='xxx'>Selecione uma opção</option>
            <option value='t'>Texto</option>
            <option value='n'>Número</option>
            <option value='l'>Lista</option>
        </select>
      </td>
   </tr>
   <tr>
      <td class=label title="Valor pré-definido para o atributo">Valor padrão</td>
      <td class=field><textarea cols=100 rows=5 name=valorPadrao></textarea>
   </tr>
   <tr>
      <td colspan="2" class="field">
        <?php geraBotaoOk(); ?>
      </td>
   </tr>
</table>
</form>
<?php
   break;
case 1:
   $js = "";
   $obAtributo = new atributoLegado;
   //Verifica se já existe um nome cadastrado
   if (!comparaValor("nom_atributo", $_REQUEST["nomAtributo"], "sw_atributo_protocolo","",1) ) {
      exibeAviso("O atributo ".$_REQUEST["nomAtributo"]." já existe!","unica","erro", "'.Sessao::getId().'");
      $js .= "f.ok.disabled = false; \n";
   } else {
      $obAtributo->setaVariaveis("tabela","sw_atributo_protocolo");
      $obAtributo->setaVariaveis("nomAtributo",$_REQUEST["nomAtributo"]);
      $obAtributo->setaVariaveis("tipoValor",$_REQUEST["tipo"]);
      $obAtributo->setaVariaveis("valorPadrao",$_REQUEST["valorPadrao"]);
      if ( $obAtributo->incluirAtributo() ) {
         //Insere auditoria
         $audicao = new auditoriaLegada;
         $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obAtributo->codAtributo);
         $audicao->insereAuditoria();
         alertaAviso($PHP_SELF.'?'.Sessao::getId(),$_REQUEST["nomAtributo"],"incluir","aviso",'');
      } else {
         exibeAviso($_REQUEST["nomAtributo"],"n_incluir","erro");
         $js .= "f.ok.disabled = false; \n";
      }
   }
   break;
}

executaFrameOculto($js);
include '../../../framework/include/rodape.inc.php';

?>
