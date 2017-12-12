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
* Manutenção de departamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3242 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 15:59:40 -0200 (Qui, 01 Dez 2005) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[1]."/9999";
$stMascaraDpto = $arMascaraSetor[0].".".$arMascaraSetor[1].".".$arMascaraSetor[2]."/9999";

$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (!(isset($controle)))
    $controle = 0;

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
        $sSQLs = "SELECT d.cod_departamento, d.nom_departamento, d.ano_exercicio, u.cod_unidade,
                u.nom_unidade, u.cod_orgao, o.nom_orgao
                FROM administracao.departamento as d, administracao.unidade as u, administracao.orgao as o
                WHERE u.cod_orgao = d.cod_orgao
                AND o.cod_orgao = d.cod_orgao
                AND u.cod_unidade = d.cod_unidade
                and u.ano_exercicio = d.ano_exercicio
                and o.ano_exercicio = d.ano_exercicio
                and u.cod_orgao = d.cod_orgao
                and o.cod_orgao = d.cod_orgao
                and d.cod_departamento > 0";
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "
        <table width='100%'>
        <tr>
             <td class='alt_dados' colspan='7'>Registros de Departamento</td>
        </tr>
        <tr>
            <td class='labelleft' width='5%'>&nbsp;</td>
            <td class='labelleft' width='10%'>Código</td>
            <td class='labelleft' width='30%'>Órgão</td>
            <td class='labelleft' width='30%'>Unidade</td>
            <td class='labelleft' width='24%'>Departamento</td>
            <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
            $codDepartamentof  = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomDepartamentof  = trim($dbEmp->pegaCampo("nom_departamento"));
            $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
            $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
            $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
            $dbEmp->vaiProximo();
            $stCodDpto = $codOrgaof.".".$codUnidadef.".".$codDepartamentof."/".$anoEf;
            $arCodDpto = validaMascara($stMascaraDpto,$stCodDpto);
            if ($arCodDpto[0]) {
                $stCodDpto = $arCodDpto[1];
            }
            $exec .= "
            <tr>
                <td class='labelcenter'>".$count++."</td>
                <td class=show_dados>".$stCodDpto."</td>
                <td class=show_dados>".$nomOrgaof."</td>
                <td class=show_dados>".$nomUnidadef."</td>
                <td class=show_dados>".$nomDepartamentof."</td>
                <td class='botao'>
                    <a
                    href='".$PHP_SELF."?".Sessao::getId()."&codDepartamento=".$codDepartamentof."&codOrgao=".$codOrgaof."&codUnidade=".$codUnidadef."&anoE=".$anoEf."&controle=1&pagina=".$pagina."'>
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

        $codDepartamento = $_REQUEST['codDepartamento'];
        $codOrgao = $_REQUEST['codOrgao'];
        $codUnidade = $_REQUEST['codUnidade'];
        $anoE = $_REQUEST['anoE'];

        $sSQL = "SELECT usuario_responsavel, cod_departamento, cod_orgao, cod_unidade, nom_departamento, ano_exercicio
                FROM administracao.departamento
                WHERE cod_departamento= ".$codDepartamento."
                AND cod_orgao = ".$codOrgao."
                AND cod_unidade =".$codUnidade."
                AND ano_exercicio = '".$anoE."'";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codg_departamento  = trim($dbEmp->pegaCampo("cod_departamento"));
        $codg_unidade  = trim($dbEmp->pegaCampo("cod_Unidade"));
        $codg_orgao  = trim($dbEmp->pegaCampo("cod_orgao"));
        $nomg_departamento  = trim($dbEmp->pegaCampo("nom_departamento"));
        $ano_unidade  = trim($dbEmp->pegaCampo("ano_exercicio"));
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

        $select = "SELECT nom_orgao FROM administracao.orgao  WHERE cod_orgao = ".$codg_orgao." AND ano_exercicio = '".$ano_unidade."'";
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomOrgao = $dbConfig->pegaCampo("nom_orgao");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        $select = "SELECT nom_unidade FROM administracao.unidade WHERE
        cod_unidade = ".$codUnidade."
        and ano_exercicio = ".$ano_unidade."
        and cod_orgao = ".$codOrgao;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomUnidade = $dbConfig->pegaCampo("nom_unidade");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        $stCodDpto = $codOrgao.".".$codUnidade.".".$codDepartamento."/".$anoE;
        $arCodDpto = validaMascara($stMascaraDpto,$stCodDpto);
        if ($arCodDpto[0]) {
            $stCodDpto = $arCodDpto[1];
        }

?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomDepartamento.value.length;
            if (campo == 0) {
            mensagem += "@Nome";
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
          <?=$codUnidade?> + '&codDepartamento=' + <?=$codDepartamento?> + '&anoE=' + <?=$anoE?>;
          document.frm.controle.value = 3;
          document.frm.submit();
          document.frm.controle.value = 2;
          document.frm.action = stFrmAnt;
      }

      function Cancela()
      {
            mudaTelaPrincipal('<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=0&pagina=<?=$pagina;?>');
      }
   </script>

<form name="frm" action="alteraDepartamento.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="controle" value="2">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<input type="hidden" name="codDepartamento" value="<?=$codg_departamento;?>">
<input type="hidden" name="codUnidade" value="<?=$codg_unidade;?>">
<input type="hidden" name="codOrgao" value="<?=$codg_orgao;?>">
<input type="hidden" name="exercicio" value="<?=$anoE;?>">

<table width=100%>
<tr>
    <td class=alt_dados colspan=2>Dados para departamento</td>
</tr>
<tr>
    <td class="label" width="20%">Código</td>
    <td class=field><?=$stCodDpto;?></td>
</tr>
<tr>
    <td class=label>Órgão</td>
    <td class=field><?=$nomOrgao;?></td>
</tr>
<tr>
    <td class=label>Unidade</td>
    <td class=field><?=$nomUnidade;?></td>
</tr>
<tr>
    <td class=label>*Nome do Departamento</td>
    <td class=field>
        <input type="text" name="nomDepartamento" size="60" maxlength="60" value="<?=$nomg_departamento;?>">
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
    $exercicio = $_REQUEST['exercicio'];
    $stMascaraDpto = $_REQUEST['stMascaraDpto'];
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];
    $nomDepartamento = $_REQUEST['nomDepartamento'];

    $stCodDpto = $codOrgao.".".$codUnidade.".".$codDepartamento."/".$exercicio;
    $arCodDpto = validaMascara($stMascaraDpto,$stCodDpto);
    if ($arCodDpto[0]) {
        $stCodDpto = $arCodDpto[1];
    }

$configuracao = new configuracaoLegado;
$configuracao->setaValorDepartamento($codDepartamento,$codUnidade,$codOrgao,$nomDepartamento,$exercicio,$usuarioResponsavel);
$stWhere = " AND cod_orgao = $codOrgao
             AND cod_unidade = $codUnidade
             AND cod_departamento <> $codDepartamento
             AND ano_exercicio='".$exercicio."' ";

if (comparaValor("nom_departamento", $nomDepartamento, "administracao.departamento", $stWhere, 1)) {
    if ($configuracao->updateDepartamento()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomDepartamento);
            $audicao->insereAuditoria();
            echo '<script type="text/javascript">
                 alertaAviso("'.$stCodDpto.' - '.$nomDepartamento.'","alterar","aviso","'.Sessao::getId().'");
                 mudaTelaPrincipal("alteraDepartamento.php?'.Sessao::getId().'&pagina='.$pagina.'");
                 </script>';
    } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$stCodDpto.' - '.$nomDepartamento.'","n_alterar","erro","'.Sessao::getId().'");
                 </script>';
                 executaFrameOculto("f.ok.disabled = false;");
    }
} else {
    echo '
        <script type="text/javascript">
        alertaAviso("O Departamento '.$nomDepartamento.' já existe","unica","erro","'.Sessao::getId().'");
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
