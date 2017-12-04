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

$Revision: 29046 $
$Name$
$Author: rodrigosoares $
$Date: 2008-04-08 08:54:22 -0300 (Ter, 08 Abr 2008) $

Casos de uso: uc-01.06.93
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/atributoLegado.class.php" );
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
setAjuda('uc-01.06.93');

if (!(isset($_REQUEST["ctrl"]))) {
   $ctrl = 0;
} else {
   $ctrl = $_REQUEST["ctrl"];
}

if (!(isset($_REQUEST["pagina"]))) {
    $pagina = 0;
} else {
   $pagina = $_REQUEST["pagina"];
}

if (isset($_REQUEST["codigo"])) {
   $ctrl=1;
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
            ((i%2) != 0) ? linhas[i].className = classe : void(0);
        }
    }
</script>

<?php

switch ($ctrl) {
case 0:
   if (isset($_REQUEST["acao"])) {
      Sessao::write('pagina','');
      $sql =  "select cod_atributo, nom_atributo from sw_atributo_protocolo";
      Sessao::write('sSQLs',$sql);
   }

   $paginacao = new paginacaoLegada;
   $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
   $paginacao->pegaPagina(Sessao::read('pagina'));
   $paginacao->geraLinks();
   $paginacao->pegaOrder("nom_atributo","ASC");
   $sSQL = $paginacao->geraSQL();
   //print $sSQL;
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
      <table width=100% id='processos'>
         <tr>
            <td colspan=4 class=alt_dados>Registros de atributo</td>
         </tr>";
   $cont = $paginacao->contador();
   $exec .="
         <tr>
            <td class=labelleftcabecalho width=5%>&nbsp;</td>
            <td class=labelleftcabecalho width=12%>Código</td>
            <td class=labelleftcabecalho width=80%>Descrição</td>
            <td class=labelleftcabecalho>&nbsp;</td>
         </tr>";
   while (!$dbEmp->eof()) {
      $codAtributo = $dbEmp->pegaCampo("cod_atributo");
      $nomAtributo = $dbEmp->pegaCampo("nom_atributo");
      $nomAtributo2 = addslashes($nomAtributo);
      $dbEmp->vaiProximo();
      $exec .= "
         <tr>
            <td class=show_dados_center_bold width=5%>".$cont++."</td>
            <td class=show_dados width=5%>".$codAtributo."</td>
            <td class=show_dados>".$nomAtributo."</td>
            <td class=botao width=5>
           <center>
            <a href='#' onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/atributo/excluiAtributo.php?".str_replace("&","*_*",Sessao::getId())."*_*codigo=".$codAtributo."*_*stDescQuestao=".addslashes(urlencode($nomAtributo2))."','sn_excluir','".Sessao::getId()."');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>
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
?>
        <script>zebra('processos','zb');</script>
<?php
break;
case 1:
   $obAtributo = new atributoLegado;
   $obAtributo->setaVariaveis("tabela","sw_atributo_protocolo");
   $obAtributo->setaVariaveis("codAtributo",$_REQUEST["codigo"]);
   $obAtributo->retornaAtributos($_REQUEST["codigo"]);
   if ( $obAtributo->excluirAtributo() ) {
      //Insere auditoria
      $audicao = new auditoriaLegada;
      $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obAtributo->codAtributo);
      $audicao->insereAuditoria();
      alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,addslashes(urlencode($obAtributo->nomAtributo)),"excluir","aviso");
   } else {
      alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,"O atributo ".addslashes(urlencode($obAtributo->nomAtributo))." não pode ser excluído, porque está relacionado a um ou mais processos!","n_excluir","erro");
   }
break;
}
include '../../../framework/include/rodape.inc.php';
?>
