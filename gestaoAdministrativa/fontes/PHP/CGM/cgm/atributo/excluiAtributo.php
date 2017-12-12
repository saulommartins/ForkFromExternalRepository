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
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"      );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php"); //Inclui classe para inserir auditoria
include (CAM_FRAMEWORK."legado/atributoLegado.class.php" );

setAjuda("uc-01.02.91");
$acao = $_REQUEST["acao"];
if (!(isset($ctrl))) {
   $ctrl = 0;
} else {
   $ctrl = $_REQUEST["ctrl"];
}

if (!(isset($pagina))) {
    $pagina = 0;
}

if (isset($_REQUEST["codigo"])) {
   $ctrl=1;
}
if (isset($pagina)) {
   //sessao->transf4 = $pagina;
   $inPagina = $pagina;
}
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {
case 0:
   if (isset($acao)) {
      $inPagina = "";
      $sql =  "select cod_atributo, nom_atributo from sw_atributo_cgm";
      //sessao->transf = $sql;
      Sessao::write('sSQLs', $sql);
   }

   $paginacao = new paginacaoLegada;
   $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
   $paginacao->pegaPagina($inPagina);
   $paginacao->geraLinks();
   $paginacao->pegaOrder("nom_atributo","ASC");
   $sSQL = $paginacao->geraSQL();
   //print $sSQL;
   $dbEmp = new dataBaseLegado;
   $dbEmp->abreBD();
   $dbEmp->abreSelecao($sSQL);
   $dbEmp->fechaBD();
   $dbEmp->vaiPrimeiro();
   if ( $pagina > 0 and $dbEmp->eof() ) {
       //sessao->transf4 = --$pagina;
       //$paginacao->pegaPagina(sessao->transf4);
       $inPagina = --$pagina;
       $paginacao->pegaPagina($inPagina);

       $paginacao->geraLinks();
       $paginacao->pegaOrder("nom_atributo","ASC");
       $sSQL = $paginacao->geraSQL();
       //Pega os dados encontrados em uma query
       $dbEmp->abreBD();
       $dbEmp->abreSelecao($sSQL);
       $dbEmp->fechaBD();
       $dbEmp->vaiPrimeiro();
   }

   Sessao::write('pagina', $inPagina);

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
      $nomAtributo2 = addslashes($nomAtributo);
      $dbEmp->vaiProximo();
      $exec .= "
         <tr>
            <td class=labelcenter width=5%>".$cont++."</td>
            <td class=show_dados width=5%>".$codAtributo."</td>
            <td class=show_dados>".$nomAtributo."</td>
            <td class=botao width=5>
           <center>
           <a href='#' onClick=\"alertaQuestao('".CAM_CGM."cgm/atributo/excluiAtributo.php?Sessao::getId()','codigo','$codAtributo%26pagina=$pagina','$nomAtributo2','sn_excluir', 'Sessao::getId()');\">
                    <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a>
           </center>
        </td>
     </tr>";
   }
   $exec .= "</table>";
   $dbEmp->limpaSelecao();
   echo $exec;
   echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
   $paginacao->mostraLinks();
   echo "</font></tr></td></table>";
break;
case 1:
   $obAtributo = new atributoLegado;
   $obAtributo->setaVariaveis("tabela","sw_atributo_cgm");
   $obAtributo->setaVariaveis("codAtributo",$_REQUEST["codigo"]);
   $obAtributo->retornaAtributos($_REQUEST["codigo"]);
   if ( $obAtributo->validaExcluirAtributo( $_REQUEST["codigo"] ) ) {
       $boErro = $obAtributo->excluirAtributo();
       if ($boErro) {
          //Insere auditoria
          $audicao = new auditoriaLegada;
          $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obAtributo->codAtributo);
          $audicao->insereAuditoria();
          alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,$obAtributo->nomAtributo,"excluir","aviso");
       } else {
          alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,$obAtributo->nomAtributo,"n_excluir","erro");
       }
   } else {
       sistemaLegado::alertaAviso($PHP_SELF.'?'.Sessao::getId()."&pagina=".$pagina,"O atributo ".$obAtributo->nomAtributo." não pode ser excluído porque está sendo utilizado!","n_excluir","erro", Sessao::getId(), "../");
   }
break;
}
include '../../../framework/include/rodape.inc.php';
?>
