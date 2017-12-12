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
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include '../configProtocolo.class.php';
setAjuda('uc-01.06.94');

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (!isset($_REQUEST["pagina"])) {
    $pagina = 0;
} else {
    $pagina = $_REQUEST["pagina"];
}

if (isset($pagina)) {
    Sessao::write('pagina',$pagina);
}
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
switch ($ctrl) {
case 0:

if (isset($_REQUEST["acao"])) {
    $sSQLs = "SELECT cod_classificacao, nom_classificacao FROM sw_classificacao";
    Sessao::write('sSQLs',$sSQLs);
    }
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("nom_classificacao","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "
        <table width=100% id='processos'>
            <tr>
                <td class=alt_dados colspan=4>Registros de classificação</td>
            </tr>
            <tr>
                <td class=labelcenterCabecalho width=5%>&nbsp;</td>
                <td class=labelcenterCabecalho width=12%>Código</td>
                <td class=labelcenterCabecalho width=80%>Descrição</td>
                <td class=labelcenterCabecalho>&nbsp;</td>
            </tr>";
    $cont=1;
        while (!$dbEmp->eof()) {
                $codClassificacaof  = trim($dbEmp->pegaCampo("cod_classificacao"));
                $nomClassificacaof  = trim($dbEmp->pegaCampo("nom_classificacao"));
                $dbEmp->vaiProximo();
                $exec .= "
                <tr>
                  <td class=show_dados_center_bold>".$cont++."</td>
                  <td class=show_dados>".$codClassificacaof."</td>
                  <td class=show_dados>".$nomClassificacaof."</td>
                  <td class='botao'>
                    <a href='".$PHP_SELF."?".Sessao::getId()."&codClassificacao=".$codClassificacaof."&ctrl=1&pagina=".$pagina."'>
                       <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0>
                    </a>
                  </td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
?>
        <script>zebra('processos','zb');</script>
<?php
break;
case 1:
//****************************************************************************
$sSQL = "select cod_classificacao, nom_classificacao FROM sw_classificacao WHERE cod_classificacao = ".$_REQUEST["codClassificacao"];
$dbEmp = new dataBaseLegado;
$dbEmp->abreBD();
$dbEmp->abreSelecao($sSQL);
$dbEmp->vaiPrimeiro();
$codClassificacao  = trim($dbEmp->pegaCampo("cod_classificacao"));
$nomClassificacao  = trim($dbEmp->pegaCampo("nom_classificacao"));
$dbEmp->limpaSelecao();
$dbEmp->fechaBD();
//****************************************************************************
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
         var f = document.frm;
         f.ok.disabled = true;
         if (Valida()) {
            document.frm.submit();
         } else {
            f.ok.disabled = false;
         }
      }

      function Cancela()
      {
           pag = "<?=$PHP_SELF?>?<?=Sessao::getId()?>&pagina=<?=$pagina?>&ctrl=0";
           mudaTelaPrincipal(pag);
      }

   </script>

<form name="frm" action="alteraClassificacao.php?<?=Sessao::getId();?>" method="POST" onsubmit='return Valida();'>
<table width=100%>
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<tr>
<td colspan=2 class="alt_dados">Dados para classificação</td>
</tr>
<tr>
    <td class=label width=30%>Código</td>
    <td class=field><?=$codClassificacao;?></td>
</tr>
<tr>
<td class=label title="Descrição da classificação">*Descrição</td>
<td class=field><input type="text" name="nomClassificacao" size=60 maxlength='60' value="<?=$nomClassificacao;?>">
<input type="hidden" name="codClassificacao" value="<?=$codClassificacao;?>">
<input type="hidden" name="ctrl" value=2>
</td>
</tr>

<tr>
<td class=field colspan="2">
<?=geraBotaoAltera();?>
</td>
</tr>

</table>

</form>

<?php
break;
case 2:
    $ok = true;
    // Faz a verficação se já não existem um registro com esse nome
    if (!comparaValor("nom_classificacao", $_REQUEST["nomClassificacao"], "sw_classificacao","And cod_classificacao <> '".$_REQUEST["codClassificacao"]."'",1)) {
        alertaAviso($PHP_SELF.'?'.Sessao::getId(),"O nome de classificação ".$_REQUEST["nomClassificacao"]." já existe!","unica","erro", ".Sessao::getId().");
        $ok = false;
    }
    if ($ok) {
    $protocolo = new configProtocolo;
    $protocolo->setaVariaveisClassificacao($_REQUEST["codClassificacao"],$_REQUEST["nomClassificacao"]);
    if ($protocolo->alteraClassificacao()) {
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["nomClassificacao"]);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("'.$_REQUEST["nomClassificacao"].'","alterar","aviso", "'.Sessao::getId().'");
                    window.location = "alteraClassificacao.php?'.Sessao::getId().'&pagina='.$pagina.'";
                    </script>';
                    } else {
                    echo '<script type="text/javascript">
                    alertaAviso("'.$_REQUEST["nomClassificacao"].'","n_alterar","aviso", "'.Sessao::getId().'");
                    window.location = "alteraClassificacao.php?'.Sessao::getId().'&pagina='.$pagina.'";
                    </script>';
                    }

    }

break;
}
include '../../../framework/include/rodape.inc.php';
?>
