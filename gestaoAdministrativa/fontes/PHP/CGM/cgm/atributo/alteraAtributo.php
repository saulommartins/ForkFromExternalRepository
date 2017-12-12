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
* Arquivo de manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24713 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:37:36 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.02.91
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php"); //Inclui classe para inserir auditoria
include (CAM_FRAMEWORK."legado/atributoLegado.class.php" );
setAjuda("uc-01.02.91");

if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (!(isset($_REQUEST["pagina"]))) {
    $pagina = 0;
}

if (isset($_REQUEST["pagina"])) {
    $inPagina = $pagina;
}

switch ($ctrl) {
case 0:
   if (isset($_REQUEST["acao"])) {
      $inPagina = "";
      $sql =  "select cod_atributo, nom_atributo from sw_atributo_cgm";
      Sessao::write('sSQLs', $sql);
   }
   Sessao::write('pagina', $inPagina);
   $paginacao = new paginacaoLegada;
   $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
   $paginacao->pegaPagina($inPagina);
   $paginacao->geraLinks();
   $paginacao->pegaOrder("lower(nom_atributo)","ASC");
   $sSQL = $paginacao->geraSQL();
   // print $sSQL;
   $dbEmp = new dataBaseLegado;
   $dbEmp->abreBD();
   $dbEmp->abreSelecao($sSQL);
   if ($dbEmp->numeroDeLinhas == 0 && $pagina != 0) {
        echo "<script type='text/javascript'>
                mudaTelaPrincipal('".$PHP_SELF."?".Sessao::getId()."');
              </script>";
    }
   $dbEmp->vaiPrimeiro();
   $exec .= "
      <table width=100%>
         <tr>
            <td colspan=4 class=alt_dados>Registros de atributo</td>
         </tr>";
   $cont = $paginacao->contador();
   $exec .="
         <tr>
            <td class=labelleft width=5%>&nbsp;</td>
            <td class=labelleft width=12%>Código</td>
            <td class=labelleft width=80%>Descrição</td>
            <td class=labelleft>&nbsp;</td>
         </tr>";
   while (!$dbEmp->eof()) {
      $codAtributo = $dbEmp->pegaCampo("cod_atributo");
      $nomAtributo = $dbEmp->pegaCampo("nom_atributo");
      $dbEmp->vaiProximo();
      $exec .= "
         <tr>
            <td class=labelcenter width=5%>".$cont++."</td>
            <td class=show_dados width=5%>".$codAtributo."</td>
            <td class=show_dados>".$nomAtributo."</td>
            <td class=botao width=5>
           <center>
              <a href='alteraAtributo.php?".Sessao::getId()."&codAtributo=".$codAtributo."&ctrl=1&pagina=".$pagina."'>
                 <img src='".CAM_FW_IMAGENS."btneditar.gif' border='0'>
              </a>
           </center>
        </td>
     </tr>";
   }
   $exec .= "</table>";
   $dbEmp->limpaSelecao();
   $dbEmp->fechaBD();
   echo $exec;
   echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
   $paginacao->mostraLinks();
   echo "</font></tr></td></table>";
break;
case 1:
$obAtributo = new atributoLegado;
$obAtributo->setaVariaveis("tabela","sw_atributo_cgm");
$vetAtributo = $obAtributo->retornaAtributos( $_REQUEST['codAtributo'] );

$codAtributo = $vetAtributo[0]['codAtributo'];
$nomAtributo = $vetAtributo[0]['nomAtributo'];
$tipo = $vetAtributo[0]['tipoValor'];
$valorPadrao = $vetAtributo[0]['valorPadrao'];

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

   campo = f.nomAtributo.value.length;
   if (campo == 0) {
      mensagem += "@descrição";
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

function Cancela()
{
   mudaTelaPrincipal("alteraAtributo.php?<?=Sessao::getId();?>&ctrl=0&pagina=<?=$pagina;?>");
}

//-->
</script>
<form name='frm' action="alteraAtributo.php?<?=Sessao::getId();?>&ctrl=2" method='post' target='oculto'>
<input type="hidden" name="codAtributo" value='<?=$codAtributo;?>'>
<input type="hidden" name="pagina" value="<?=$pagina?>">
<table width=100%>
   <tr>
      <td class=alt_dados colspan=2>Dados para atributo</td>
   </tr>
   <tr>
      <td class=label width=30%>Código</td>
      <td class=field><?=$codAtributo?></td>
   </tr>
   <tr>
      <td class=label width=30% title="Descrição do atributo.">*Descrição</td>
      <td class=field><input type=text name=nomAtributo size=60 maxlength=60 value="<?=$nomAtributo;?>"></td>
   </tr>
   <tr>
      <td class=label title="Tipo de registro.">Tipo</td>
      <td class=field>
    <?php
    switch ($tipo) {
    case 't':
        echo "Texto";
    break;
    case 'n':
        echo "Número";
    break;
    case 'l':
        echo "Lista";
    break;
    }
    ?>
      </td>
   </tr>
   <tr>
      <td class=label title="Valor pré-definido para o atributo.">Valor padrão</td>
      <td class=field><textarea cols=100 rows=5 name=valorPadrao><?=$valorPadrao?></textarea></td>
   </tr>
   <tr>
      <td colspan="2" class="field">
        <?php geraBotaoOk(1,0,1,1); ?>
      </td>
   </tr>
</table>
</form>
<script type="text/javascript">
<!--
document.frm.nomAtributo.focus();
//-->
</script>
<?php
break;
case 2:
   $obAtributo = new atributoLegado;
   $sWhere = " and cod_atributo != ".$_REQUEST["codAtributo"];
   //Verifica se já existe um nome cadastrado
   if (!comparaValor("nom_atributo", $_REQUEST["nomAtributo"], "sw_atributo_cgm",$sWhere,1) ) {
      exibeAviso("O atributo ".$_REQUEST["nomAtributo"]." já existe!","unica","erro", "'.Sessao::getId().'");
      $js .= "f.ok.disabled = false; \n";
   } else {
      $obAtributo->setaVariaveis("tabela","sw_atributo_cgm");
      $obAtributo->setaVariaveis($_REQUEST);
      if ( $obAtributo->alterarAtributo() ) {
         //Insere auditoria
         $audicao = new auditoriaLegada;
         $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obAtributo->codAtributo);
         $audicao->insereAuditoria();
         alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,$_REQUEST["nomAtributo"],"alterar","aviso","");
      } else {
         exibeAviso($REQUEST["nomAtributo"],"n_alterar","erro");
         $js .= "f.ok.disabled = false; \n";
      }
   }
break;
}

executaFrameOculto($js);

include '../../../framework/include/rodape.inc.php';
?>
