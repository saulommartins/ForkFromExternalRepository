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

$Revision: 28826 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-27 16:33:30 -0300 (Qui, 27 Mar 2008) $

Casos de uso: uc-01.06.98
*/

include '../../../framework/include/cabecalho.inc.php';

if (!(isset($ctrl)))
$ctrl = 0;

if (isset($pagina)) {
$ctrl = 0;
}

switch ($ctrl) {
case 0:
$anoEx = pegaConfiguracao("ano_exercicio");
if (isset($acao)) {

           $sql = "Select Distinct A.cod_processo, A.ano_exercicio, A.cod_andamento, C.nom_classificacao,
                S.nom_assunto, S.cod_classificacao, S.cod_assunto, A.timestamp, US.username,  U.cod_andamento
                From sw_andamento as A, sw_ultimo_andamento as U,
                sw_processo as P, sw_assunto as S, administracao.usuario as US, sw_classificacao as C,
                sw_despacho as DP
                Where A.cod_andamento = U.cod_andamento
                And A.cod_processo = U.cod_processo
                And A.ano_exercicio = U.ano_exercicio
                And A.cod_processo = P.cod_processo
                And A.ano_exercicio = P.ano_exercicio
                And P.cod_classificacao = S.cod_classificacao
                And C.cod_classificacao = S.cod_classificacao
                And P.cod_assunto = S.cod_assunto
                And A.cod_orgao = '".Sessao::read('codOrgao')."'
                And A.cod_unidade = '".Sessao::read('codUnidade')."'
                And A.cod_departamento = '".Sessao::read('codDpto')."'
                And A.cod_setor = '".Sessao::read('codSetor')."'
                And P.cod_situacao = '3'
                And US.numcgm = A.cod_usuario
                AND A.cod_andamento = DP.cod_andamento
                AND A.cod_processo = DP.cod_processo
                And P.cod_usuario = ".Sessao::read('numCgm');

            Sessao::write('sSQLs',$sql);

    }

        include '../../classes/paginacao.class.php';
        $paginacao = new paginacao;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"12");
        $paginacao->pegaPagina($pagina);
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
                <td class='alt_dados' colspan=3>Usuário</td>
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
                $codAndamento= $dbEmp->pegaCampo("cod_andamento");

                $date = timestamptobr($timestamp);
                $time = timestamptobr($timestamp, "h");

                $chave = $anoEx."-".$codAndamento."-".$codProcesso."-".$assunto."-".$classificacao;
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
                 <a href='consultaProcesso.php?".Sessao::getId()."&codProcesso=".$codProcesso."&anoExercicio=".$anoEx."&controle=0&ctrl=2'>
                    <img src='../../images/procuracgm.gif' alt='Consultar Processo' width=20 height=20 border=0></a>
                </td>
                <td class='show_dados'>
                <input type='button' value='Alterar' onClick=\"document.location='alteraDespacho.php?".Sessao::getId()."&chave=".$chave."&ctrl=1'\">
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
$variaveis = explode("-",$chave);
            $anoE = $variaveis[0];
            $codAndamento = $variaveis[1];
            $codProcesso = $variaveis[2];
            $nomAssunto = $variaveis[3];
            $nomClassificacao = $variaveis[4];
            $codUsuario = Sessao::read('numCgm');

            $sSQL = "SELECT cod_classificacao, cod_assunto, anotacoes, observacoes FROM sw_processo WHERE ano_exercicio = '".$anoE."' AND cod_processo = ".$codProcesso;
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $anotacoes = trim($dbEmp->pegaCampo("anotacoes"));
            $codAssunto = trim($dbEmp->pegaCampo("cod_assunto"));
            $codClassificacao = trim($dbEmp->pegaCampo("cod_classificacao"));
            $observacoes = trim($dbEmp->pegaCampo("observacoes"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            $anotacoes = nl2br($anotacoes);
            $observacoes = nl2br($observacoes);
            Sessao::write('anotacoes',$anotacoes);
            Sessao::write('observacoes',$observacoes);
            //sessao->transf4["anotacoes"] = $anotacoes;
            //sessao->transf4["observacoes"] = $observacoes;

            $anotacoes_min = substr($anotacoes,0,100);
            $observacoes_min = substr($observacoes,0,100);

            $sSQL = "SELECT descricao FROM sw_despacho WHERE ano_exercicio = '".$anoE."' AND cod_andamento = ".$codAndamento." AND cod_processo = ".$codProcesso." AND cod_usuario = ".$codUsuario;
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $descricao = trim($dbEmp->pegaCampo("descricao"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

               $dbConfig = new database;
            $dbConfig->abreBd();
            $select =   "SELECT d.cod_documento
                                FROM sw_documento_processo as dp, sw_documento as d
                                WHERE dp.cod_documento = d.cod_documento
                                AND dp.cod_processo = ".$codProcesso."
                                AND dp.exercicio = '".$anoE."'";
//            echo $select;
            $dbConfig->abreSelecao($select);
            $i = 0;
            while (!$dbConfig->eof()) {
                    $cod = $i;
                    $lista_domentos_entregues[$cod] = $dbConfig->pegaCampo("cod_documento");
            $dbConfig->vaiProximo();
            $i++;
            }
            //$dbConfig->limpaSelecao();
            //$dbConfig->fechaBd();

            //$dbConfig->abreBd();
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

            //$dbEmp->limpaSelecao();
            //$dbEmp->fechaBD();

            ?>

<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.descricao.value.length;
            if (campo == 0) {
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

      function copiaDigital(cod, acao, codProcesso)
      {
                var x = 200;
                var y = 140;
                var sArq = '../../../framework/legado/imagens/copiaDigitalLegado.php?<?=Sessao::getId();?>&codDoc='+cod+'&acao='+acao+'&codProcesso='+codProcesso;
                var wVolta=false;
                tela = window.open(sArq,'tela','titlebar=no,hotkeys=no,width=450px,height=320px,resizable=1,scrollbars=1,left='+x+',top='+y);
            }
 </script>

<form name=frm action="alteraDespacho.php?<?=Sessao::getId()?>" method="POST">
  <table width=90%>

<tr>
<td class="alt_dados" colspan="3">Inclua os dados do despacho</td>
</tr>

<tr>
<td class=label>Cod. Processo</td>
<td class=field colspan="2"><input type="text" size=30 name="codProcesso" value="<?=$codProcesso;?>" readonly=""></td>
</tr>

<tr>
<td class=label>Classificação</td>
<td class=field colspan="2"><?=$nomClassificacao;?>
<input type="hidden" name="codAndamento" value="<?=$codAndamento;?>">
<input type="hidden" name="codUsuario" value="<?=$codUsuario;?>">
</td>
</tr>

<tr>
<td class=label>Assunto</td>
<td class=field colspan="2"><?=$nomAssunto;?>
</td>
</tr>

<tr>
<td class=label>Exercício</td>
<td class=field colspan="2"><?=$anoE;?><input type="hidden" name="anoE" value="<?=$anoE;?>"></td>
</tr>

<tr>
<td class=label>Anotações</td>
<td class=field colspan="2">&nbsp;
<?=$anotacoes_min;?>...  <br><a href="#" onclick="mostraDadosProcesso('anotacoes','<?=Sessao::getId();?>');">[mais]</a>
</td>
</tr>

<tr>
<td class=label>Observações</td>
<td class=field colspan="2">&nbsp;
<?=$observacoes_min;?>...  <br><a href="#" onclick="mostraDadosProcesso('observacoes','<?=Sessao::getId();?>');">[mais]</a>
</td>
</tr>

<tr>
    <td class="alt_dados" colspan="3">Documentos</td>
  </tr>
<?php
if (is_array($lista_domentos_processo)) {

        while (list($key,$val) = each($lista_domentos_processo)) {
            $selected = "";

            if (is_array($lista_domentos_entregues)) {
                //continue;
            } else {
                $lista_domentos_entregues = array();
            }
                    if (in_Array($key,$lista_domentos_entregues)) {
                        echo "
                            <tr>
                            <td class=label>Documento Já Entregue</td>
                            <input type='hidden' name='documento[]' value='".$key."'>
                            <td class=field>".$val."</td>
                            <td class=field width=5><input type='button' value='Cópia Digital' onClick='copiaDigital($key,Sessao::read('acao'),$codProcesso);'></td>
                            </tr>";
                    } else {
                        echo "
                            <tr>
                            <td class=label><input type='checkbox' name='documento[]' value='".$key."'></td>
                            <td class=field>".$val."</td>
                            <td class=field width=5><input type='button' value='Cópia Digital' 				onClick='copiaDigital($key,Sessao::read('acao'),$codProcesso);'></td>
                            </tr>";
                    }
            // }
        }
}
?>

<tr>
<td class=label>*Descrição</td>
<td class=field colspan="2">
<textarea name=descricao cols=50 rows=6><?=$descricao;?></textarea>
<input type="hidden" name="ctrl" value=2>
</td>
</tr>

<tr>
<td class=field colspan=3><input type="button" value="OK" style='width: 60px;' onClick="Salvar();">&nbsp;
<input type="reset" value="Limpar" style='width: 60px;'></td>
</tr>

</table>
</form>
<?php
break;
case 2:
include '../../classes/processos.class.php';

$chave = $anoE."-".$codAndamento."-".$codProcesso;

$obj = "Processo: ".$codProcesso."/".$anoE." (".$docObj.")";

$processo = new processos;
$processo->setaValorDespacho($codAndamento,$codProcesso,$anoE,$codUsuario,$descricao);
if ($processo->updateDespacho()) {
    //if ($processo->updateDocumento($documento,$codProcesso,$anoE)) {
                    include '../../classes/auditoria.class.php';
                    $audicao = new auditoria;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obj);
                    $audicao->insereAuditoria();
                    echo '<script type="text/javascript">
                    alertaAviso("'.$obj.'","alterar","aviso", "'.Sessao::getId().'");
                    window.location = "incluiDespacho.php?'.Sessao::getId().'&chave='.$chave.'&ctrl=3";
                    </script>';
    //}
} else {
                    echo '<script type="text/javascript">
                    alertaAviso("'.$obj.'","n_alterar","erro", "'.Sessao::getId().'");
                    window.location = "alteraDespacho.php?'.Sessao::getId().'";
                    </script>';
}

break;
}
include '../../includes/rodape.php';
