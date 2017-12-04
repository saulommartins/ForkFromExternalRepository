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
    * Arquivo de implementação de manutenção de processo
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    Casos de uso: uc-01.06.98

    $Id: alteraProcesso.php 66029 2016-07-08 20:55:48Z carlos.silva $

    */

include '../../../framework/include/cabecalho.inc.php';

$pagina = $_REQUEST["pagina"];
if (!(isset($_REQUEST["ctrl"]))) {
    $ctrl = 0;
} else {
    $ctrl = $_REQUEST["ctrl"];
}

if (isset($pagina)) {
    Sessao::write('pagina',$pagina);
    $ctrl = 1;
}

switch ($ctrl) {
case 0:

if (isset($_REQUEST["acao"])) {
    Sessao::write('pagina',$pagina);

           $sql = "Select Distinct A.cod_processo, A.ano_exercicio, A.cod_andamento, C.nom_classificacao,
                S.nom_assunto, S.cod_classificacao, S.cod_assunto, A.timestamp, US.username
                From sw_andamento as A, sw_ultimo_andamento as U,
                sw_processo as P, sw_assunto as S, administracao.usuario as US, sw_classificacao as C
                Where A.cod_andamento = U.cod_andamento
                And A.cod_processo = U.cod_processo
                And A.ano_exercicio = U.ano_exercicio
                And A.cod_processo = P.cod_processo
                And A.ano_exercicio = P.ano_exercicio
                And P.cod_classificacao = S.cod_classificacao
                And C.cod_classificacao = S.cod_classificacao
                And P.cod_assunto = S.cod_assunto
                And A.cod_orgao = '".Sessao::read('codOrgao')."'
                And P.cod_situacao = '3'
                And US.numcgm = A.cod_usuario";

            Sessao::write('sSQLs',$sql);

    }

        include '../../classes/paginacao.class.php';
        $paginacao = new paginacao;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"12");
        $paginacao->pegaPagina(Sessao::read('pagina'));
        $paginacao->geraLinks();
        $paginacao->pegaOrder("cod_processo","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBase;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec .= "
                <table width='85%'>
                <tr>
                <td class='alt_dados'>Cód. Processo</td>
                <td class='alt_dados'>Classificação</td>
                <td class='alt_dados'>Assunto</td>
                <td class='alt_dados'>Data (Hora)</td>
                <td class='alt_dados' colspan=2>Usuário</td>
                </tr>
        ";
        while (!$dbEmp->eof()) {
                $codProcesso = $dbEmp->pegaCampo("cod_processo");
                $anoEx = $dbEmp->pegaCampo("ano_exercicio");
                $codAssunto = $dbEmp->pegaCampo("cod_assunto");
                $codClassif = $dbEmp->pegaCampo("cod_classificacao");
                $classificacao = $dbEmp->pegaCampo("nom_classificacao");
                $assunto = $dbEmp->pegaCampo("nom_assunto");
                $timestamp = $dbEmp->pegaCampo("timestamp");
                $usuario = $dbEmp->pegaCampo("username");

                $date = date($timestamp,"d");
                $time = date($timestamp,"hs");

                $dbEmp->vaiProximo();
                $exec .= "
                 <tr>
                <td class=show_dados>
                    ".$codProcesso."/".$anoEx."
                </td>
                <td class=show_dados>
                    ".$classificacao."
                </td>
                <td class=show_dados>
                    ".$assunto."
                </td>
                <td class=show_dados>
                ".$date." (".$time.")
                </td>
                <td class=show_dados>
                    ".$usuario."
                </td>
                <td class=show_dados>
                <a href='alteraProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&anoExercicio=".$anoEx."&ctrl=1'>
                <img src='../../images/btneditar.gif' alt='Consultar Processo'  border=0>
                </a>
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
$dbConfig = new database;

$dbConfig->abreBd();
$select =   "SELECT cl.nom_classificacao, ass.nom_assunto, p.anotacoes,p.observacoes,
                    p.cod_classificacao, p.cod_assunto
                    FROM sw_processo as p, sw_classificacao as cl, sw_assunto as ass
                    WHERE p.cod_classificacao = cl.cod_classificacao
                    AND p.cod_classificacao = ass.cod_classificacao
                    AND p.cod_assunto = ass.cod_assunto
                    AND p.cod_processo = ".$_REQUEST["codProcesso"]."
                    AND p.ano_exercicio = '".$_REQUEST["anoExercicio"]."'";
$dbConfig->abreSelecao($select);
$nomClassificaco = $dbConfig->pegaCampo("nom_classificacao");
$nomAssunto = $dbConfig->pegaCampo("nom_assunto");
$anotacoes = $dbConfig->pegaCampo("anotacoes");
$observacoes = $dbConfig->pegaCampo("observacoes");
$codClassificacao = $dbConfig->pegaCampo("cod_classificacao");
$codAssunto = $dbConfig->pegaCampo("cod_assunto");
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();

$dbConfig->abreBd();
$select =   "SELECT dp.cod_documento_processo, d.cod_documento
                    FROM sw_documento_processo as dp, sw_documento as d
                    WHERE dp.cod_documento = d.cod_documento
                    AND dp.cod_processo = ".$_REQUEST["codProcesso"]."
                    AND dp.ano_exercicio = '".$_REQUEST["anoExercicio"]."'";
$dbConfig->abreSelecao($select);
$i = 0;
while (!$dbConfig->eof()) {
        $cod = $i;
        $lista_domentos_entregues[$cod] = $dbConfig->pegaCampo("cod_documento");
$dbConfig->vaiProximo();
$i++;
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();

$dbConfig->abreBd();
$select =

"SELECT da.cod_documento, d.nom_documento
FROM sw_documento_assunto as da,  sw_documento as d
WHERE da.cod_documento = d.cod_documento
AND da.cod_classificacao = ".$codClassificacao."
AND da.cod_assunto = ".$codAssunto;
//echo $select;
$dbConfig->abreSelecao($select);
while (!$dbConfig->eof()) {
   $cod = $dbConfig->pegaCampo("cod_documento");
    $lista_domentos_processo[$cod] = $dbConfig->pegaCampo("nom_documento");
    $dbConfig->vaiProximo();
}
$dbConfig->limpaSelecao();
$dbConfig->fechaBd();
?>
<script type="text/javascript">
    function Salvar()
    {
          document.frm.submit();
    }
   function limpa()
   {
    document.frm.anotacoes.value = "";
    document.frm.observacoes.value = "";
    }
   </script>

<form name="frm" action="alteraProcesso.php?<?=Sessao::getId();?>&ctrl=2" method="POST">
<table width="80%">
  <tr>
    <td class="alt_dados" colspan="2">Alterar Processo</td>
  </tr>
  <tr>
<td class=label width="30%">Cód. Processo</td>
<td class=field><?=$codProcesso;?>/<?=$anoExercicio;?>
</td>
  </tr>
  <tr>
<td class=label>Classificação</td>
<td class=field><?=$nomClassificaco;?></td>
  </tr>
  <tr>
<td class=label>Assunto</td>
<td class=field><?=$nomAssunto;?></td>
  </tr>
<tr>
    <td class="alt_dados" colspan="2">Documentos</td>
  </tr>
<?php
if (is_array($lista_domentos_processo)) {

        while (list($key,$val) = each($lista_domentos_processo)) {
            $selected = "";
            if (is_array($lista_domentos_entregues)) {
                    if (in_Array($key,$lista_domentos_entregues)) {
                            $selected = "checked disabled=''";
                    }
             }
            echo "
            <tr>
            <td class=label><input type='checkbox' name='documento[]' value='".$key."'".$selected."></td>
            <td class=field>".$val."</td>
            </tr>
            ";
            }
}
?>

  <tr>
<td class=label valign="top">Anotações</td>
  <td class=field>
  <?=$anotacoes?>
  </td>
  </tr>
  <tr>
<td class=label valign="top">Observações</td>
  <td class=field>
  <?=$observacoes?>
  <input type="hidden" name="codProcesso" value="<?=$_REQUES["codProcesso"];?>">
  <input type="hidden" name="exercicio" value="<?=$_REQUEST["anoExercicio"];?>">
  </td>
  </tr>
    <tr>
        <td colspan='2' class='field'>

            <table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>
    <input type="button" name="ok" value="OK" style="width: 60px" onClick="Salvar();">
&nbsp;<input type="button" name="limpar" value="Limpar" style="width: 60px" onClick="limpa();">
    </td><td class="fieldright_noborder">
    <b>* Campos Obrigatórios</b>
    </td></tr></table>

        </td>
    </tr>
</table>
</form>
<?php
break;
case 2:
include '../../classes/processos.class.php';
$altDoc = new processos;
$erro = 0;
if (is_Array($documento)) {
        $is = 0;
        while (list($key,$val) = each($documento)) {
                  $codDocumentoProcesso = pegaID("cod_documento_processo", "sw_documento_processo");
                  if (!($altDoc->updateDocumento($codDocumentoProcesso,$val,$codProcesso,$exercicio,"",""))) {
                            $erro++;
                  } else {
                            $docs[$is] = $val;
                  }
        $is++;
        }
}

if (is_array($docs)) {
    while (list($key,$val) = each($docs)) {
        $nomDoc = pegaDado("nom_documento","sw_documento","WHERE cod_documento = ".$val."");
        $docObj .= $nomDoc." ";
   }
}

$obj = "Processo: ".$codProcesso."/".$exercicio." (".$docObj.")";
if ($erro == 0) {
            include '../../classes/auditoria.class.php';
            $audicao = new auditoria;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obj);
            $audicao->insereAuditoria();
            echo '
            <script type="text/javascript">
            alertaAviso("'.$obj.'","alterar","aviso","'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
} else {
            echo '
            <script type="text/javascript">
            alertaAviso("'.$obj.'","n_alterar","erro","'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
}
break;
}
    include '../../includes/rodape.php';
?>
