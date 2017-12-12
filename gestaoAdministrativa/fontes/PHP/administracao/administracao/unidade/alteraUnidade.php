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
* Manutneção do unidade
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

$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (!(isset($controle))) {
    $controle = 0;
}

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";
$stMascaraUnidade = $arMascaraSetor[0].".".$arMascaraSetor[1]."/9999";

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
        $sSQLs = "SELECT u.cod_orgao, u.cod_unidade, u.nom_unidade, u.ano_exercicio, o.nom_orgao
                  FROM administracao.unidade as u,
                  administracao.orgao as o
                  WHERE u.cod_orgao = o.cod_orgao
                  AND u.ano_exercicio = o.ano_exercicio
                  AND u.cod_unidade > 0";
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade)","ASC");
        $sSQL = $paginacao->geraSQL();
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "
        <table width=100%>
        <tr>
             <td class='alt_dados' colspan='7'>Registros de Unidade</td>
        </tr>
        <tr>
            <td class='labelleft' width='5%'>&nbsp;</td>
            <td class='labelleft'>Código</td>
            <td class='labelleft'>Órgão</td>
            <td class='labelleft'>Unidade</td>
            <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
                $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                $dbEmp->vaiProximo();
                $stCodUnidade = $codOrgaof.".".$codUnidadef."/".$anoEf;
                $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
                if ($arCodUnidade[0]) {
                    $stCodUnidade = $arCodUnidade[1];
                }
                $exec .= "<tr>
                <td class='labelcenter'>".$count++."</td>
                <td class=show_dados>".$stCodUnidade."</td>
                <td class=show_dados>".$nomOrgaof."</td>
                <td class=show_dados>".$nomUnidadef."</td>
                <td class=botao>
                <a href='".$PHP_SELF."?".Sessao::getId()."&codOrgao=".$codOrgaof."&codUnidade=".$codUnidadef."&anoE=".$anoEf."&controle=1&pagina=".$pagina."'>
                <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0></a></td>
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
        $anoE = $_REQUEST['anoE'];

        $sSQL = "SELECT usuario_responsavel, cod_orgao, cod_unidade, nom_unidade, ano_exercicio
                FROM administracao.unidade
                WHERE cod_orgao = ".$codOrgao."
                AND cod_unidade=".$codUnidade."
                AND ano_exercicio = '".$anoE."'";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codg_unidade  = trim($dbEmp->pegaCampo("cod_Unidade"));
        $codg_orgao    = trim($dbEmp->pegaCampo("cod_orgao"));
        $nomg_unidade  = trim($dbEmp->pegaCampo("nom_unidade"));
        $ano_unidade   = trim($dbEmp->pegaCampo("ano_exercicio"));
        $responsavel   = trim($dbEmp->pegaCampo("usuario_responsavel"));
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        $stCodUnidade = $codg_orgao.".".$codg_unidade."/".$ano_unidade;
        $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
        if ($arCodUnidade[0]) {
            $stCodUnidade = $arCodUnidade[1];
        }

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

?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomUnidade.value.length;
            if (campo == 0) {
            mensagem += "@Unidade: é obrigatório";
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
            document.frm.submit();
         } else {
            f.ok.disabled = false;
         }
      }

      function AlteraNumCgm()
      {
          var stFrmAnt = document.frm.action;
          document.frm.action = document.frm.action + '&codOrgao='+<?=$codOrgao?> + '&codUnidade='+<?=$codUnidade?> + '&anoE=' + <?=$anoE?>;
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

<form name="frm" action="alteraUnidade.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="codUnidade" value="<?=$codg_unidade;?>">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<input type="hidden" name="codOrgao" value="<?=$codg_orgao;?>">
<input type="hidden" name="controle" value="2">
<input type="hidden" name="anoUnidade" value="<?=$ano_unidade;?>">

<table width=100%>
<tr>
    <td class=alt_dados colspan=2>Dados para unidade</td>
</tr>
<tr>
    <td class=label>Código </td>
    <td class=field><?=$stCodUnidade;?>
</tr>
<tr>
    <td class=label>Órgão</td>
    <td class=field><?=$nomOrgao;?></td>
</tr>

<tr>
    <td class=label>*Nome da Unidade </td>
    <td class=field>
        <input type="text" name="nomUnidade" size="60" maxlength="60" value="<?=$nomg_unidade;?>">
    </td>
</tr>
<tr>
    <td class="label" title="Usuário responsável pela unidade">Responsável</td>
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

    $codUnidade = $_REQUEST['codUnidade'];
    $codOrgao = $_REQUEST['codOrgao'];
    $nomUnidade = $_REQUEST['nomUnidade'];
    $anoUnidade = $_REQUEST['anoUnidade'];
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

$configuracao = new configuracaoLegado;
$configuracao->setaValorUnidade($codUnidade,$codOrgao,$nomUnidade,$anoUnidade,$usuarioResponsavel);
if (comparaValor("nom_unidade", $nomUnidade, "administracao.unidade", "and cod_orgao = $codOrgao and cod_unidade <> $codUnidade and ano_exercicio = '".$anoUnidade."'",1)) {
    if ($configuracao->updateUnidade()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomOrgao);
            $audicao->insereAuditoria();
            $stCodUnidade = $codOrgao.".".$codUnidade."/".$anoUnidade;
            $arCodUnidade = validaMascara($stMascaraUnidade,$stCodUnidade);
            if ($arCodUnidade[0]) {
                $stCodUnidade = $arCodUnidade[1];
            }
            echo '<script type="text/javascript">
                 alertaAviso("'.$stCodUnidade.' - '.$nomUnidade.'","alterar","aviso","'.Sessao::getId().'");
                 mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&pagina='.$pagina.'");
                 </script>';
    } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomUnidade.'","n_alterar","erro","'.Sessao::getId().'");
                 </script>';
                 executaFrameOculto("f.ok.disabled = false;");
    }
} else {
    echo'
        <script type="text/javascript">
        alertaAviso("A Unidade '.$nomUnidade.' já existe","unica","erro","'.Sessao::getId().'");
        </script>';
        executaFrameOculto("f.ok.disabled = false;");
}
break;

case 3:

    $usuarioNumCgm = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm, administracao.usuario
                 WHERE sw_cgm.numcgm = ".$usuarioNumCgm."
                   AND sw_cgm.numcgm = usuario.numcgm";
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    if ($usuarioResponsavel == "") {
        echo '<script type="text/javascript">
                    alertaAviso("Código de Usuário invalido ('.$usuarioNumCgm.')","unica","erro","'.Sessao::getId().'");
              </script>';
    }

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\" \n";
    executaFrameOculto($js);
break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
