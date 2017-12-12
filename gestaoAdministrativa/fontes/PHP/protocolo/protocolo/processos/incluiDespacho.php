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

    $Id: incluiDespacho.php 66029 2016-07-08 20:55:48Z carlos.silva $
*/

include '../../../framework/include/cabecalho.inc.php';

if (!(isset($ctrl))) {
    $ctrl = 0;

}

if (isset($pagina)) {
$ctrl = 0;
}

switch ($ctrl) {
case 0:
$anoEx = pegaConfiguracao("ano_exercicio");

if (isset($acao)) {

           $sql = " Select Distinct
                        A.cod_processo,
                        A.ano_exercicio,
                        A.cod_andamento,
                        C.nom_classificacao,
                        S.nom_assunto,
                        S.cod_classificacao,
                        S.cod_assunto,
                        A.timestamp,
                        US.username,
                        U.cod_andamento
                    From
                        sw_andamento           AS A,
                        sw_ultimo_andamento AS U,
                        sw_processo            AS P,
                        sw_assunto             AS S,
                        administracao.usuario             AS US,
                        sw_classificacao       AS C
                    Where
                        A.cod_andamento     = U.cod_andamento           AND
                        A.cod_processo      = U.cod_processo            AND
                        A.ano_exercicio     = U.ano_exercicio           AND
                        A.cod_processo      = P.cod_processo            AND
                        A.ano_exercicio     = P.ano_exercicio           AND
                        P.cod_classificacao = S.cod_classificacao       AND
                        C.cod_classificacao = S.cod_classificacao       AND
                        P.cod_assunto       = S.cod_assunto             AND
                        A.cod_orgao         = '".Sessao::read('codOrgao')."'   AND
                        -- A.cod_unidade       = '".Sessao::read('codUnidade')."' AND
                        -- A.cod_departamento  = '".Sessao::read('codDpto')."'    AND
                        -- A.cod_setor         = '".Sessao::read('codSetor')."'   AND
                        P.cod_situacao      = '3'                       AND
                        US.numcgm           = A.cod_usuario             AND
                        P.cod_processo || '.' || P.ano_exercicio not in
                        (
                            select cod_processo_filho || '.' || exercicio_filho
                            from sw_processo_apensado
                            where timestamp_desapensamento is null
                        )";

            Sessao::write('sSQLs',$sql);

    }

        include '../../classes/paginacao.class.php';
        $paginacao = new paginacao;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"15");
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

                $dbEmp->vaiProximo();

               $dbConfig = new database;
            $dbConfig->abreBd();
            $select =   "SELECT cod_processo
                                FROM sw_despacho
                                WHERE cod_andamento = ".$codAndamento."
                                AND cod_processo = ".$codProcesso;
            $dbConfig->abreSelecao($select);

            //if ($dbConfig->numeroDeLinhas == 0) {
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
                <input type='button' value='Despachar' onClick=\"document.location='incluiDespacho.php?".Sessao::getId()."&codProcesso=".$codProcesso."&anoExercicio=".$anoEx."&ctrl=1'\">
                </td>
                </tr>";
                }
        //}
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $exec;
        echo "<table id= 'paginacao' width=695 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";

break;
case 1:
            $sSQL = "
                  SELECT
                            u.cod_processo,
                            u.cod_andamento,
                            u.ano_exercicio,
                            p.cod_assunto,
                            p.cod_classificacao,
                            p.cod_situacao,
                            a.cod_orgao,
                            -- a.cod_unidade,
                            -- a.cod_setor,
                            -- a.cod_departamento,
                            ass.nom_assunto,
                            cl.nom_classificacao,
                            p.anotacoes,
                            p.observacoes

                    FROM 	sw_ultimo_andamento as u,
                            sw_processo as p,
                            sw_andamento as a,
                            sw_assunto as ass,
                            sw_classificacao as cl
                   WHERE 	p.cod_processo = u.cod_processo
                     AND	p.ano_exercicio = u.ano_exercicio
                     AND	p.cod_processo = ".$codProcesso."
                     AND	a.cod_processo = u.cod_processo
                     AND	a.ano_exercicio = u.ano_exercicio
                     AND	a.cod_andamento = u.cod_andamento
                     AND	p.cod_processo = a.cod_processo
                     AND	p.ano_exercicio = a.ano_exercicio
                     AND	p.cod_assunto = ass.cod_assunto
                     AND	p.cod_classificacao = cl.cod_classificacao
                     AND	cl.cod_classificacao = ass.cod_classificacao
                     AND	a.cod_orgao = ".Sessao::read('codOrgao')."
                     -- AND a.cod_unidade = ".Sessao::read('codUnidade')."
                     -- AND a.cod_departamento = ".Sessao::read('codDpto')."
                     -- AND a.cod_setor = ".Sessao::read('codSetor')."
                     AND	p.cod_situacao = 3
                     AND	p.ano_exercicio = '".$anoExercicio."'";
            //echo $sSQL;
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $codProcesso	  = trim($dbEmp->pegaCampo("cod_processo"));
            $codAndamento	  = trim($dbEmp->pegaCampo("cod_andamento"));
            $anoExercicio	  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $codAssunto		  = trim($dbEmp->pegaCampo("cod_assunto"));
            $codClassificacao = trim($dbEmp->pegaCampo("cod_classificacao"));
            $codOrgao		  = trim($dbEmp->pegaCampo("cod_orgao"));
            $codUnidade		  = trim($dbEmp->pegaCampo("cod_unidade"));
            $codSetor		  = trim($dbEmp->pegaCampo("cod_setor"));
            $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomAssunto 	  = trim($dbEmp->pegaCampo("nom_assunto"));
            $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
            $anotacoes		  = trim($dbEmp->pegaCampo("anotacoes"));
            $observacoes	  = trim($dbEmp->pegaCampo("observacoes"));
            $anotacoes		  = nl2br($anotacoes);
            $observacoes	  = nl2br($observacoes);
            Sessao::write('anotacoes',$anotacoes);
            Sessao::write('observacoes',$observacoes);

            $anotacoes_min = substr($anotacoes,0,100);
            $observacoes_min = substr($observacoes,0,100);

            if ($dbEmp->numeroDeLinhas != 0) {
            //----------------------------------------------------------------
            $codUsuario = Sessao::read('numCgm');

            $sSQL = "SELECT count(cod_usuario) as total FROM sw_despacho WHERE ano_exercicio = '".$anoExercicio."' AND cod_andamento = ".$codAndamento." AND cod_processo = ".$codProcesso." AND cod_usuario = ".$codUsuario;
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $conta = trim($dbEmp->pegaCampo("total"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

            if ($conta == 0) {

            $dbConfig = new database;
            $dbConfig->abreBd();
            $select =   "SELECT d.cod_documento, cod_processo
                                FROM sw_documento_processo as dp, sw_documento as d
                                WHERE dp.cod_documento = d.cod_documento
                                AND dp.cod_processo = ".$codProcesso."
                                AND dp.exercicio = '".$anoExercicio."'";
            //echo $select;
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

<form name=frm action="incluiDespacho.php?<?=Sessao::getId();?>" method="POST">
  <table width=90%>

<tr>
<td class="alt_dados" colspan="3">Entre com os dados do despacho</td>
</tr>

<tr>
<td class=label>Cod. Processo</td>
<td class=field colspan="2"><input type="hidden" size=30 name="codProcesso" value="<?=$codProcesso;?>" readonly=""><?=$codProcesso;?></td>
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
<td class=label>Ano de Exercício</td>
<td class=field colspan="2"><?=$anoE;?><input type="hidden" name="anoE" value="<?=$anoExercicio;?>"><?=$anoExercicio;?></td>
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
<textarea name=descricao cols=70 rows=6></textarea>
<input type="hidden" name="ctrl" value=2>
</td>
</tr>

<tr>
    <td class=field colspan=3>
        <input type="button" value="OK" style='width: 60px;' onClick="Salvar();">&nbsp;
        <input type="reset" value="Limpar" style='width: 60px;'></td>
</tr>

</table>
</form>
<?php
} else {
echo '<script type="text/javascript">
                    alertaAviso("Você já despachou este processo. Se quiser alterá-lo, opte pela ação Alterar Despachos.","unica","aviso", "'.Sessao::getId().'");
                    window.location = "incluiDespacho.php?'.Sessao::getId().'";
                    </script>';
}

} else {

echo '<script type="text/javascript">
                    alertaAviso("Este processo não existe ou não está no seu Setor.","unica","aviso", "'.Sessao::getId().'");
                    window.location = "incluiDespacho.php?'.Sessao::getId().'";
                    </script>';
}
break;
case 2:
include '../../classes/processos.class.php';

$obj = "Despacho para Processo: ".$codProcesso."/".$anoE;

$processo = new processos;
$processo->setaValorDespacho($codAndamento,$codProcesso,$anoE,$codUsuario,$descricao);
if ($processo->insertDespacho()) {
    //if ($processo->updateDocumento($documento,$codProcesso,$exercicio)) {
                    include '../../classes/auditoria.class.php';
                    $audicao = new auditoria;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $obj);
                    $audicao->insereAuditoria();
                    $chave = $anoE."-".$codAndamento."-".$codProcesso;
                    echo '<script type="text/javascript">
                    alertaAviso("'.$obj.'","incluir","aviso", "'.Sessao::getId().'");
                    window.location = "incluiDespacho.php?'.Sessao::getId().'&ctrl=3&chave='.$chave.'";
                    </script>';
    //}
} else {
                    echo '<script type="text/javascript">
                    alertaAviso("'.$obj.'","n_incluir","erro", "'.Sessao::getId().'");
                    window.location = "incluiDespacho.php?'.Sessao::getId().'";
                    </script>';
}

break;
case 3:
include '../../classes/botoesPdf.class.php';
$variaveis = explode("-",$chave);
            $anoE = $variaveis[0];
            $codAndamento = $variaveis[1];
            $codProcesso = $variaveis[2];
            $codUsuario = Sessao::read('numCgm');

            $sSQL = "SELECT u.cod_processo, u.cod_andamento, u.ano_exercicio, u.cod_processo||'-'||u.ano_exercicio as chave, d.timestamp, d.ano_exercicio,
                        a.cod_orgao, a.cod_unidade, a.cod_setor, a.cod_departamento, ass.nom_assunto, cl.nom_classificacao, d.cod_usuario,
                        d.descricao, p.numcgm, c.nom_cgm, c2.nom_cgm as nom_usuario, tp.nom_situacao,
                        org.nom_orgao, uni.nom_unidade, dep.nom_departamento, set.nom_setor


                        FROM sw_ultimo_andamento as u, sw_processo as p, sw_andamento as a, sw_assunto as ass, sw_classificacao as cl,
                        sw_despacho as d, sw_cgm as c, sw_cgm as c2, sw_situacao_processo as tp,
                        administracao.orgao as org, administracao.unidade as uni, administracao.departamento as dep, administracao.setor as set


                        WHERE p.cod_processo = u.cod_processo
                        AND p.ano_exercicio = u.ano_exercicio

                        AND p.cod_situacao = tp.cod_situacao

                        AND d.cod_usuario = c.numcgm
                        AND p.numcgm = c2.numcgm

                        AND a.cod_processo = u.cod_processo
                        AND a.ano_exercicio = u.ano_exercicio
                        AND a.cod_andamento = u.cod_andamento

                        AND p.cod_processo = a.cod_processo
                        AND p.ano_exercicio = a.ano_exercicio

                        AND p.cod_assunto = ass.cod_assunto
                        AND p.cod_classificacao = cl.cod_classificacao
                        AND ass.cod_classificacao = cl.cod_classificacao

                        AND a.cod_orgao = ".Sessao::read('codOrgao')."
                        AND a.cod_unidade = ".Sessao::read('codUnidade')."
                        AND a.cod_departamento = ".Sessao::read('codDpto')."
                        AND a.cod_setor = ".Sessao::read('codSetor')."

                        AND p.cod_situacao = 3
                        AND p.ano_exercicio = '".$anoE."'

                        AND u.ano_exercicio = d.ano_exercicio
                        AND u.cod_andamento = d.cod_andamento
                        AND u.cod_processo = d.cod_processo

                        AND d.cod_andamento = ".$codAndamento."
                        AND d.cod_processo = ".$codProcesso."
                        AND d.ano_exercicio = '".$anoE."'

                        AND a.cod_orgao = org.cod_orgao
                        AND a.cod_unidade = uni.cod_unidade
                        AND a.cod_departamento = dep.cod_departamento
                        AND a.cod_setor = set.cod_setor

                        AND org.cod_orgao  = uni.cod_orgao
                        AND org.cod_orgao  = dep.cod_orgao
                        AND org.cod_orgao  = set.cod_orgao

                        AND uni.cod_unidade  = dep.cod_unidade
                        AND uni.cod_unidade  = set.cod_unidade

                        AND dep.cod_departamento  = set.cod_departamento

                        AND uni.cod_orgao  = set.cod_orgao
                        AND dep.cod_unidade = set.cod_unidade
                        AND dep.cod_orgao = uni.cod_orgao


                        AND d.cod_usuario = ".Sessao::read('numCgm');

            //echo $sSQL;
            $dbEmp = new dataBase;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $chave = trim($dbEmp->pegaCampo("chave"));
            $codProcesso = trim($dbEmp->pegaCampo("cod_processo"));
            $codAndamento = trim($dbEmp->pegaCampo("cod_andamento"));
            $anoE = trim($dbEmp->pegaCampo("ano_exercicio"));
            $nomAssunto = trim($dbEmp->pegaCampo("nom_assunto"));
            $nomClassificacao = trim($dbEmp->pegaCampo("nom_classificacao"));
            $codUsuario = trim($dbEmp->pegaCampo("cod_usuario"));
            $descricao = trim($dbEmp->pegaCampo("descricao"));
            $numcgm = trim($dbEmp->pegaCampo("numcgm"));
            $nomcgm = trim($dbEmp->pegaCampo("nom_cgm"));
            $nomUsuario = trim($dbEmp->pegaCampo("nom_usuario"));
            $nomSituacao = trim($dbEmp->pegaCampo("nom_situacao"));
            $nomOrgao = trim($dbEmp->pegaCampo("nom_orgao"));
            $nomUnidade = trim($dbEmp->pegaCampo("nom_unidade"));
            $nomDpto = trim($dbEmp->pegaCampo("nom_departamento"));
            $nomSetor = trim($dbEmp->pegaCampo("nom_setor"));
            $data =  trim($dbEmp->pegaCampo("timestamp"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            $datafim = substr($data, 0,10);

 $sSQL .= "; select c.valor||',' as nom_municipio,
                                     current_date as hoje
                             from administracao.configuracao c
                             where c.parametro = 'nom_municipio'";
 //print $sSQL;
 $botoesPDF = new botoesPdf;
 $botoesPDF->imprimeBotoes('../protocolo/processos/despacho.xml',$sSQL,'','');
  ?>

<table width=90%>

<tr>
<td class="alt_dados" colspan=2>Relatório de Despacho para Processo n. <?=$codProcesso;?></td>
</tr>

<tr>
<td class="field" colspan=2><b>Dados do Processo</b></td>
</tr>

<tr>
<td class=label width=30%>Número do Processo</td>
<td class=field><?=$chave;?></td>
</tr>

<tr>
<td class=label width=30%>Classificação</td>
<td class=field><?=$nomClassificacao;?></td>
</tr>

<tr>
<td class=label width=30%>Assunto</td>
<td class=field><?=$nomAssunto;?></td>
</tr>

<tr>
<td class=label width=30%>Interessado</td>
<td class=field><?=$nomUsuario;?></td>
</tr>

<tr>
<td class="field" colspan=2><b>Trâmite atual do Processo</b></td>
</tr>

<tr>
<td class=label width=30%>Situação atual</td>
<td class=field><?=$nomSituacao;?></td>
</tr>

<tr>
<td class=label width=30%>Órgão atual</td>
<td class=field><?=$nomOrgao;?></td>
</tr>

<tr>
<td class=label width=30%>Unidade atual</td>
<td class=field><?=$nomUnidade;?></td>
</tr>

<tr>
<td class=label width=30%>Departamento atual</td>
<td class=field><?=$nomDpto;?></td>
</tr>

<tr>
<td class=label width=30%>Setor atual</td>
<td class=field><?=$nomSetor;?></td>
</tr>

<tr>
<td class=label width=30%>Ano de Exercício</td>
<td class=field><?=$anoE;?></td>
</tr>

<tr>
<td class="field" colspan=2><b>Despacho</b></td>
</tr>

<tr>
<td class=label width=30%>Despachado por</td>
<td class=field><?=$nomcgm;?></td>
</tr>

<tr>
<td class=label width=30%>Data de Despacho</td>
<td class=field><?=$datafim;?></td>
</tr>

<tr>
<td class=label width=30%>Descrição</td>
<td class=field><?=$descricao;?></td>
</tr>

</table>

<?php
break;
}
?>

<?php
include '../../includes/rodape.php';
