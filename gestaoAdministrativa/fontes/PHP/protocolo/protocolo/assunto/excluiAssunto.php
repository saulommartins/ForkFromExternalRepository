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
* Arquivo de implementação de manutenção de assunto
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24857 $
$Name$
$Author: domluc $
$Date: 2007-08-16 12:33:55 -0300 (Qui, 16 Ago 2007) $

Casos de uso: uc-01.06.95
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include (CAM_FRAMEWORK."legado/paginacaoLegada.class.php");
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
include '../assunto.class.php';
setAjuda('uc-01.06.95');
    $exclui = new assunto;
    if (!(isset($ctrl)))
        $ctrl = 0;
    if (isset($codigo))
        $ctrl=1;

    switch ($ctrl) {
        case 0:
    $eAssunto = $exclui->listaAssunto(); //lista os Processos cadastrados
    $sSQL = "SELECT * FROM sw_classificacao ORDER BY nom_classificacao";
    $dbConfig = new databaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($sSQL);
    $dbConfig->fechaBd();

     $dbConfig2 = new databaseLegado;
     $paginacao = new paginacaoLegada;
     if ($codClassificacao and $codClassificacao != "xxx") {
        $select = "SELECT * FROM sw_assunto WHERE cod_classificacao = ".$codClassificacao." ";
        Sessao::write('filtro','lower(nom_assunto)');
        Sessao::write('sSQLs',$select);
        //sessao->transf2 ="lower(nom_assunto)";
        //sessao->transf = $select;
        $registros = 10;
        $paginacao->pegaDados(Sessao::read('sSQLs'),$registros);
        $paginacao->pegaPagina($pagina);
        $paginacao->complemento = "&codClassificacao=".$codClassificacao;
        $paginacao->geraLinks();
        $paginacao->pegaOrder(Sessao::read('filtro'),"ASC");
        $sSQL2 = $paginacao->geraSQL();
        $dbConfig2->abreBd();
        $dbConfig2->abreSelecao($sSQL2);
        $dbConfig2->fechaBd();
        Sessao::write('filtro','');
        Sessao::write('sSQLs','');
    }
?>
<form name=frm action=excluiAssunto.php?<?=Sessao::getId();?>&ctrl=0 method=post>
<table width=100%>
   <tr>
      <td colspan=2 class=alt_dados>Classificações de assunto</td>
   <tr>
      <td class=label width=30% title="Filtra os assuntos por classificação">Classificação</td>
      <td class=field>
        <input type="text" size="4" maxlength="8" name="codClassTxt" onChange="JavaScript: if (preencheCampo(document.frm.codClassTxt, document.frm.codClassificacao)) {document.frm.submit();}" value="<?=$codClassificacao;?>">
         <select name=codClassificacao onChange="JavaScript: if (preencheCampo(document.frm.codClassificacao, document.frm.codClassTxt)) {document.frm.submit();}">
            <option value='xxx'>Selecione</option>
<?php
   while (!$dbConfig->eof()) {
      $codClass = $dbConfig->pegaCampo('cod_classificacao');
      $nomClass = $dbConfig->pegaCampo('nom_classificacao');
      if ($codClass == $codClassificacao) {
         $selected = " selected";
      } else {
         $selected = "";
      }
?>
             <option value='<?=$codClass;?>'<?=$selected?>><?=$nomClass;?></option>
<?php
   $dbConfig->vaiProximo();
   }
?>
         </select>
      </td>
   </tr>
</table>
<table width=100%>
   <tr>
      <td colspan=4 class=alt_dados>Registros de assunto</td>
   </tr>
   <tr>
      <td class=labelleft width=5%>&nbsp;</td>
      <td class=labelleft width=12%>Código</td>
      <td class=labelleft width=80%>Descrição</td>
      <td class=label>&nbsp;</td>
   </tr>
<?php
   $cont = $paginacao->contador();
   while (!$dbConfig2->eof()) {
      $codAssunto = $dbConfig2->pegaCampo('cod_assunto');
      $nomAssunto = $dbConfig2->pegaCampo('nom_assunto');
      $key = $codAssunto.".".$codClassificacao.".".$pagina;
?>
   <tr>
      <td class=labelcenter><?=$cont++;?></td>
      <td class=show_dados><?=$codAssunto;?></td>
      <td class=show_dados><?=$nomAssunto;?></td>
<?php
    echo "<td class=botao ><a href='#' onClick=\"alertaQuestao('".CAM_PROTOCOLO."protocolo/assunto/excluiAssunto.php?".str_replace("&","*_*",Sessao::getId())."*_*codigo=".urlencode($key)."*_*stDescQuestao=".addslashes(urlencode($nomAssunto))."','sn_excluir','".Sessao::getId()."');\">
<img src='".CAM_FW_IMAGENS."btnexcluir.gif' border='0'></a></td>";
?>
   </tr>
<?php
   $dbConfig2->vaiProximo();
   }
?>
</table>
</form>
<?php
echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
$paginacao->mostraLinks();
echo "</font></tr></td></table>";
?>

<?php
    echo "</table>";
break;
case 1:
            $cod = explode(".", $codigo);
            $cod1 = $cod[0];
            $cod2 = $cod[1];
            $pagina = $cod[2];
           // $nom = pegaDado("nom_assunto","sw_assunto","where cod_assunto = $cod1 and cod_classificacao = $cod2");
            if ( $exclui->excluiAssunto($cod1, $cod2) ) { //exclui o Assunto
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $cod1);
                $audicao->insereAuditoria();
                echo '
                    <script type="text/javascript">
                    alertaAviso("Assunto '.urlencode($stDescQuestao).'","excluir","aviso", "'.Sessao::getId().'");
                    </script>';
                    echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL=excluiAssunto.php?'.Sessao::getId().'&codClassificacao='.$cod2.'&pagina='.$pagina.'">'; //da um refresh na página
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("Assunto '.$nom.'","n_excluir","erro", "'.Sessao::getId().'");
                    mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&codClassificacao='.$cod2.'&pagina='.$pagina.'");
                    </script>';
            }
    }
include '../../../framework/include/rodape.inc.php';
?>
