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
* Manutneção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18683 $
$Name$
$Author: rodrigo_sr $
$Date: 2006-12-11 15:44:36 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraLocal = pegaConfiguracao("mascara_local");

$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (!(isset($controle))) {
    $controle = 0;
}

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
        $sSQLs = "SELECT l.cod_local, l.cod_setor, s.nom_setor, l.cod_departamento, l.nom_local,
                d.nom_departamento, l.cod_orgao,l.cod_unidade, u.nom_unidade, l.ano_exercicio, o.nom_orgao
                FROM administracao.local as l,administracao.setor as s, administracao.departamento as d, administracao.unidade as u, administracao.orgao as o
                WHERE l.cod_setor = s.cod_setor
                AND l.cod_departamento = s.cod_departamento
                AND l.cod_unidade = s.cod_unidade
                AND l.cod_orgao = s.cod_orgao
                AND l.ano_exercicio = d.ano_exercicio
                AND l.ano_exercicio = o.ano_exercicio
                AND l.ano_exercicio = u.ano_exercicio
                AND l.ano_exercicio = s.ano_exercicio
                AND s.cod_departamento = d.cod_departamento
                AND s.cod_unidade = d.cod_unidade
                AND s.cod_orgao = d.cod_orgao
                AND d.cod_unidade = u.cod_unidade
                AND d.cod_orgao = u.cod_orgao
                AND u.cod_orgao = o.cod_orgao
                AND l.cod_local > 0";

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento), lower(nom_setor), lower(nom_local)","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "
               <table width=100%>
               <tr>
                   <td class='alt_dados' colspan='9'>Registros de locais</td>
               </tr>
               <tr>
                <td class='labelleft' width='5%'>&nbsp;</td>
                <td class='labelleft'>Código</td>
                <td class='labelleft'>Órgão</td>
                <td class='labelleft'>Unidade</td>
                <td class='labelleft'>Departamento</td>
                <td class='labelleft'>Setor</td>
                <td class='labelleft'>Local</td>
                <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codSetorf  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetorf  = trim($dbEmp->pegaCampo("nom_setor"));
                $codLocalf  = trim($dbEmp->pegaCampo("cod_local"));
                $nomLocalf  = trim($dbEmp->pegaCampo("nom_local"));
                $codDepartamentof  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamentof  = trim($dbEmp->pegaCampo("nom_departamento"));
                $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
                $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                $dbEmp->vaiProximo();
                $stCodLocal = $codOrgaof.".".$codUnidadef.".".$codDepartamentof.".".$codSetorf.".".$codLocalf."/".$anoEf;
                $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
                if ($arCodLocal[0]) {
                    $stCodLocal = $arCodLocal[1];
                }
                $exec .= "
                <tr>
                      <td class='labelcenter'>".$count++."</td>
                      <td class=show_dados>".$stCodLocal."</td>
                      <td class=show_dados>".$nomOrgaof."</td>
                      <td class=show_dados>".$nomUnidadef."</td>
                      <td class=show_dados>".$nomDepartamentof."</td>
                      <td class=show_dados>".$nomSetorf."</td>
                      <td class=show_dados>".$nomLocalf."</td>
                      <td class='botao'>
                        <a
                        href='".$PHP_SELF."?".Sessao::getId()."&codLocal=".$codLocalf."&codSetor=".$codSetorf."&codDepartamento=".$codDepartamentof."&codOrgao=".$codOrgaof."&codUnidade=".$codUnidadef."&anoE=".$anoEf."&controle=1&pagina=".$pagina."'>
                        <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0>
                        </a>
                    </td>
                </tr>\n";
        }
        $exec .= "</table>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo "$exec";
        echo "<table width=450 align=center><tr><td align=center><font size=2>";
        $paginacao->mostraLinks();
        echo "</font></tr></td></table>";
break;

case 1:

        $codOrgao = $_REQUEST['codOrgao'];
        $codUnidade = $_REQUEST['codUnidade'];
        $codDepartamento = $_REQUEST['codDepartamento'];
        $codSetor = $_REQUEST['codSetor'];
        $codLocal = $_REQUEST['codLocal'];
        $anoE = $_REQUEST['anoE'];

        $sSQL = "SELECT l.usuario_responsavel,
                        l.cod_local,
                        l.cod_setor,
                        s.nom_setor,
                        l.cod_departamento,
                        l.nom_local,
                        d.nom_departamento,
                 l.cod_orgao,
                 l.cod_unidade,
                 u.nom_unidade,
                 l.ano_exercicio,
                 o.nom_orgao

                 FROM administracao.local as l,
                                administracao.setor as s,
                                administracao.departamento as d,
                        administracao.unidade as u,
                        administracao.orgao as o

        WHERE o.cod_orgao = u.cod_orgao
                AND o.ano_exercicio = u.ano_exercicio
                AND u.cod_unidade = d.cod_unidade
                AND u.ano_exercicio = d.ano_exercicio
                AND u.cod_orgao = d.cod_orgao
                AND d.cod_orgao = s.cod_orgao
                AND d.ano_exercicio = s.ano_exercicio
                AND d.cod_unidade = s.cod_unidade
                AND d.cod_departamento = s.cod_departamento
                AND s.cod_orgao = l.cod_orgao
                AND s.ano_exercicio = l.ano_exercicio
                AND s.cod_unidade = l.cod_unidade
                AND s.cod_departamento = l.cod_departamento
                AND s.cod_setor = l.cod_setor
                AND l.cod_orgao = ".$codOrgao."
                AND l.cod_unidade = ".$codUnidade."
                AND l.cod_departamento = ".$codDepartamento."
                AND l.cod_setor = ".$codSetor."
                AND l.cod_local = ".$codLocal."
                AND l.ano_exercicio = '".$anoE."'";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codg_setor  = trim($dbEmp->pegaCampo("cod_setor"));
        $codg_local  = trim($dbEmp->pegaCampo("cod_local"));
        $codg_departamento  = trim($dbEmp->pegaCampo("cod_departamento"));
        $codg_unidade  = trim($dbEmp->pegaCampo("cod_Unidade"));
        $codg_orgao  = trim($dbEmp->pegaCampo("cod_orgao"));
        $nomg_local  = trim($dbEmp->pegaCampo("nom_local"));
        $nom_orgao  = trim($dbEmp->pegaCampo("nom_orgao"));
        $nom_unidade =trim($dbEmp->pegaCampo("nom_unidade"));
        $nom_departamento =trim($dbEmp->pegaCampo("nom_departamento"));
        $nom_setor =trim($dbEmp->pegaCampo("nom_setor"));
        $ano_setor  = trim($dbEmp->pegaCampo("ano_exercicio"));
        $responsavel  = trim($dbEmp->pegaCampo("usuario_responsavel"));
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        $select = 	"SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$responsavel;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $usuarioResponsavelN = $dbConfig->pegaCampo("nom_cgm");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

    $stCodLocal = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$anoE;
    $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
    if ($arCodLocal[0]) {
        $stCodLocal = $arCodLocal[1];
    }

?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomLocal.value.length;
            if (campo == 0) {
            mensagem += "@nome do local";
            erro = true;
         }

        if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
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

      function AlteraNumCgm()
      {
          var stFrmAnt = document.frm.action;
          document.frm.action = document.frm.action + '&codOrgao=' + <?=$codOrgao?> + '&codUnidade=' +
          <?=$codUnidade?> + '&codDepartamento=' + <?=$codDepartamento?> + '&codSetor=' + <?=$codSetor?> +
          '&codLocal=' + <?=$codLocal?> + '&anoE=' + <?=$anoE?>;
          document.frm.controle.value = 3;
          document.frm.submit();
          document.frm.controle.value = 2;
          document.frm.action = stFrmAnt;
      }

      function Cancela()
      {
            mudaTelaPrincipal('<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId();?>&controle=0&pagina=<?=$pagina;?>');
      }
   </script>

<form name="frm" action="alteraLocal.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<input type="hidden" name="codLocal" value="<?=$codg_local;?>">
<input type="hidden" name="codSetor" value="<?=$codg_setor;?>">
<input type="hidden" name="codDepartamento" value="<?=$codg_departamento;?>">
<input type="hidden" name="codUnidade" value="<?=$codg_unidade;?>">
<input type="hidden" name="codOrgao" value="<?=$codg_orgao;?>">
<input type="hidden" name="controle" value=2>
<input type="hidden" name="anoSetor" value="<?=$ano_setor;?>">
<table width=100%>
<tr><td class=alt_dados colspan=2>Dados para local</td></tr>
<tr>
    <td class=label>Código</td>
    <td class=field><?=$stCodLocal;?></td>
</tr>
<tr>
    <td class=label>Órgão</td>
    <td class=field><?=$nom_orgao;?></td>
</tr>
<tr>
    <td class=label>Unidade</td>
    <td class=field><?=$nom_unidade;?></td>
</tr>
<tr>
    <td class=label>Departamento</td>
    <td class=field><?=$nom_departamento;?></td>
</tr>
<tr>
    <td class=label>Setor</td>
    <td class=field><?=$nom_setor;?></td>
</tr>
<tr>
    <td class=label>*Nome do Local</td>
    <td class=field>
        <input type="text" name="nomLocal" size=60 maxlength=60 value="<?=$nomg_local;?>">
    </td>
</tr>
<tr>
    <td class="label" title="Usuário responsável pelo departamento">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="AlteraNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="" border=0></a>
    </td>
</tr>
<tr>
    <td class=field colspan=2>
        <?php geraBotaoAltera(); ?>
    </td>
</tr>
</table>
</form>
<?php
break;

case 2:

$codOrgao = $_REQUEST['codOrgao'];
$codUnidade = $_REQUEST['codUnidade'];
$codDepartamento = $_REQUEST['codDepartamento'];
$codSetor = $_REQUEST['codSetor'];
$codLocal = $_REQUEST['codLocal'];
$nomLocal = $_REQUEST['nomLocal'];
$anoSetor = $_REQUEST['anoSetor'];

$configuracao = new configuracaoLegado;
$configuracao->setaValorLocal($codLocal,$codSetor,$codDepartamento,$codUnidade,$codOrgao,$nomLocal,$anoSetor,$usuarioResponsavel);

if (comparaValor("nom_local", $nomLocal, "administracao.local", "and cod_orgao = $codOrgao and cod_unidade = $codUnidade and cod_departamento = $codDepartamento and cod_setor = $codSetor and cod_local <> $codLocal and ano_exercicio = '".$anoSetor."'",1)) {
    if ($configuracao->updateLocal()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomDepartamento);
            $audicao->insereAuditoria();
            $stCodLocal = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor.".".$codLocal."/".$anoSetor;
            $arCodLocal = validaMascara($stMascaraLocal,$stCodLocal);
            if ($arCodLocal[0]) {
                $stCodLocal = $arCodLocal[1];
            }
            echo '<script type="text/javascript">
                 alertaAviso("'.$stCodLocal.' - '.$nomLocal.'","alterar","aviso","'.Sessao::getId().'");
                 mudaTelaPrincipal("alteraLocal.php?'.Sessao::getId().'&pagina='.$pagina.'");
                 </script>';
    } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomLocal.'","n_alterar","erro","'.Sessao::getId().'");
                 </script>';
                 executaFrameOculto("f.ok.disabled = false;");
    }
} else {
    echo '
        <script type="text/javascript">
        alertaAviso("O Local '.$nomLocal.' já existe","unica","erro","'.Sessao::getId().'");
        </script>';
        executaFrameOculto("f.ok.disabled = false;");
}
break;

case 3:

    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$usuarioResponsavel;
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\" \n";
    executaFrameOculto($js);
break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
