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
* Arquivo de implementação de manutenção de classificação
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24725 $
$Name$
$Author: domluc $
$Date: 2007-08-13 18:32:32 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.94
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include '../configProtocolo.class.php';
setAjuda('uc-01.06.94');

if (!(isset($_REQUEST["ctrl"]))) {
   $ctrl = 0;
} else {
   $ctrl = $_REQUEST["ctrl"];
}

switch ($ctrl) {
case 0:
?>
   <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = trim( document.frm.nomClassificacao.value );
            if (campo == "") {
            mensagem += "@O campo Descrição é obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.submit();
         }
      }
   </script>

<form name="frm" action="incluiClassificacao.php?<?=Sessao::getId();?>" method="POST" onsubmit='return Valida();'>

<table width=100%>

<tr>
    <td colspan=2 class="alt_dados">Dados para classificação</td>
</tr>

<tr>
    <td class=label width=30% title="Descrição da classificação">*Descrição</td>
    <td class=field><input type="text" name="nomClassificacao" size=60 maxlength='60'>
        <input type="hidden" name="ctrl" value=1>
    </td>
</tr>

<tr>
    <td class='field' colspan="2">
        <?=geraBotaoOk();?></td>
</tr>

</table>

</form>

<?php
    break;
case 1:
//*******************************************************************
    $ok = true;
    // Faz a verficação se já não existem um registro com esse nome
    if (!comparaValor("nom_classificacao", $_REQUEST["nomClassificacao"], "sw_classificacao","",1)) {
        alertaAviso($PHP_SELF.'?'.Sessao::getId(),"O nome de classificação ".$_REQUEST["nomClassificacao"]." já existe!","unica","erro", "'.Sessao::getId().'");
        $ok = false;
    }
//******************************************************************
if ($ok) { //Verifica a existência de registros iguais
//******************************************************************
//Se não existir nenhum igual
    $nId = pegaID("cod_classificacao","sw_classificacao");
    $protocolo = new configProtocolo;
    $protocolo->setaVariaveisClassificacao($nId,$_REQUEST["nomClassificacao"]);
    if ($protocolo->insereClassificacao()) {
        $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["nomClassificacao"]);
        $audicao->insereAuditoria();
        echo '<script type="text/javascript">
        alertaAviso("'.$_REQUEST["nomClassificacao"].'","incluir","aviso", "'.Sessao::getId().'");
        window.location = "incluiClassificacao.php?'.Sessao::getId().'";
        </script>';
    } else {
        echo '<script type="text/javascript">
        alertaAviso("'.$_REQUEST["nomClassificacao"].'","n_incluir","aviso", "'.Sessao::getId().'");
        window.location = "incluiClassificacao.php?'.Sessao::getId().'";
        </script>';
    }

}
//****************************************************************
break;
}
include '../../../framework/include/rodape.inc.php';
?>
