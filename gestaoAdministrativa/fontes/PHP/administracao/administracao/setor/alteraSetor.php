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
* Manutneção de setores
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

$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (!(isset($controle)))
$controle = 0;

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
        $sSQLs = "SELECT s.cod_setor, s.nom_setor, d.cod_departamento, d.nom_departamento, d.cod_orgao,
                d.cod_unidade, u.nom_unidade,d.ano_exercicio, o.nom_orgao
                FROM administracao.setor as s, administracao.departamento as d, administracao.unidade as u, administracao.orgao as o
                WHERE s.cod_departamento = d.cod_departamento
                and s.cod_unidade = d.cod_unidade
                and s.cod_orgao = d.cod_orgao
                and s.ano_exercicio = d.ano_exercicio
                and s.ano_exercicio = u.ano_exercicio
                and s.ano_exercicio = o.ano_exercicio
                and d.cod_unidade = u.cod_unidade
                and d.cod_orgao = u.cod_orgao
                and u.cod_orgao = o.cod_orgao
                and s.cod_setor > 0";

        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados($sSQLs,"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao), lower(nom_unidade), lower(nom_departamento), lower(nom_setor)","ASC");
        $sSQL = $paginacao->geraSQL();

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $exec = "";
        $exec .= "<table width=100%>
               <tr>
                   <td class='alt_dados' colspan='8'>Registros de Setor</td>
               </tr>
               <tr>
                  <td class='labelleft' width='5%'>&nbsp;</td>
                  <td class='labelleft'>Código</td>
                  <td class='labelleft'>Órgão</td>
                  <td class='labelleft'>Unidade</td>
                  <td class='labelleft'>Departamento</td>
                  <td class='labelleft'>Setor</td>
                  <td class='labelleft''>&nbsp;</td>
               </tr>";
        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codSetorf  = trim($dbEmp->pegaCampo("cod_setor"));
                $nomSetorf  = trim($dbEmp->pegaCampo("nom_setor"));
                $codDepartamentof  = trim($dbEmp->pegaCampo("cod_departamento"));
                $nomDepartamentof  = trim($dbEmp->pegaCampo("nom_departamento"));
                $codUnidadef  = trim($dbEmp->pegaCampo("cod_unidade"));
                $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomUnidadef  = trim($dbEmp->pegaCampo("nom_unidade"));
                $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                $dbEmp->vaiProximo();
                $stCodSetor = $codOrgaof.".".$codUnidadef.".".$codDepartamentof.".".$codSetorf."/".$anoEf;
                $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
                if ($arCodSetor[0]) {
                    $stCodSetor = $arCodSetor[1];
                }
                $exec .= "
                <tr>
                   <td class='labelcenter'>".$count++."</td>
                   <td class=show_dados>".$stCodSetor."</td>
                   <td class=show_dados>".$nomOrgaof."</td>
                   <td class=show_dados>".$nomUnidadef."</td>
                   <td class=show_dados>".$nomDepartamentof."</td>
                   <td class=show_dados>".$nomSetorf."</td>
                   <td class=botao width=20>
                   <a href='".$PHP_SELF."?".Sessao::getId()."&codSetor=".$codSetorf."&codDepartamento=".$codDepartamentof."&codOrgao=".$codOrgaof."&codUnidade=".$codUnidadef."&anoE=".$anoEf."&controle=1&pagina=".$pagina."'>
                   <img src='".CAM_FW_IMAGENS."btneditar.gif' border=0></a></td></tr>\n";
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
        $codUnidade = $_REQUEST['codUnidade'];
        $codSetor = $_REQUEST['codSetor'];
        $codDepartamento = $_REQUEST['codDepartamento'];
        $codOrgao = $_REQUEST['codOrgao'];
        $anoE = $_REQUEST['anoE'];

        $sSQL = "SELECT usuario_responsavel, cod_setor, cod_departamento, cod_orgao, cod_unidade, nom_setor, ano_exercicio
                FROM administracao.setor WHERE cod_setor = ".$codSetor." AND cod_departamento= ".$codDepartamento." AND cod_orgao = ".$codOrgao."
                AND cod_unidade =".$codUnidade." AND ano_exercicio = '".$anoE."'";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codg_setor  = trim($dbEmp->pegaCampo("cod_setor"));
        $codg_departamento  = trim($dbEmp->pegaCampo("cod_departamento"));
        $codg_unidade  = trim($dbEmp->pegaCampo("cod_Unidade"));
        $codg_orgao  = trim($dbEmp->pegaCampo("cod_orgao"));
        $nomg_setor  = trim($dbEmp->pegaCampo("nom_setor"));
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

        $select = "SELECT nom_orgao FROM administracao.orgao  WHERE cod_orgao = ".$codg_orgao." AND ano_exercicio = '".$ano_setor."'";
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomOrgao = $dbConfig->pegaCampo("nom_orgao");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        $select = "SELECT nom_unidade FROM administracao.unidade WHERE
        cod_unidade = ".$codg_unidade."
        and ano_exercicio = '".$ano_setor."'
        and cod_orgao = ".$codg_orgao;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomUnidade = $dbConfig->pegaCampo("nom_unidade");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        $select = "SELECT nom_departamento FROM administracao.departamento WHERE
        cod_departamento= ".$codg_departamento."
        AND cod_orgao = ".$codg_orgao."
        AND cod_unidade =".$codg_unidade."
        AND ano_exercicio = '".$ano_setor."'";
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        $nomDepartamento = $dbConfig->pegaCampo("nom_departamento");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();

        $stCodSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor."/".$anoE;
        $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
        if ($arCodSetor[0]) {
            $stCodSetor = $arCodSetor[1];
        }

?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomSetor.value.length;
            if (campo == 0) {
            mensagem += "@nome do setor";
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
          <?=$codUnidade?> + '&codDepartamento=' + <?=$codDepartamento?> + '&codSetor=' + <?=$codSetor?> + '&anoE=' + <?=$anoE?>;
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

<form name="frm" action="alteraSetor.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<input type="hidden" name="codSetor" value="<?=$codg_setor;?>">
<input type="hidden" name="codDepartamento" value="<?=$codg_departamento;?>">
<input type="hidden" name="codUnidade" value="<?=$codg_unidade;?>">
<input type="hidden" name="codOrgao" value="<?=$codg_orgao;?>">
<input type="hidden" name="controle" value=2>
<input type="hidden" name="anoSetor" value="<?=$ano_setor;?>">

<table width=100%>
<tr><td class=alt_dados colspan=2>Dados para setor</td></tr>
<tr>
    <td class=label>Código</td>
    <td class=field><?=$stCodSetor;?></td>
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
    <td class=label>Departamento</td>
    <td class=field><?=$nomDepartamento;?></td>
</tr>
<tr>
    <td class=label>*Nome do Setor</td>
    <td class=field><input type="text" name="nomSetor" size=60 maxlength=60 value="<?=$nomg_setor;?>">
</td>
</tr>
<tr>
    <td class="label" title="Usuário responsável pelo departamento">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="AlteraNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."/procuracgm.gif";?>" alt="" border=0></a>
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
$codSetor = $_REQUEST['codSetor'];
$codDepartamento = $_REQUEST['codDepartamento'];
$codOrgao = $_REQUEST['codOrgao'];
$anoSetor = $_REQUEST['anoSetor'];
$nomSetor = $_REQUEST['nomSetor'];
$usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

$configuracao = new configuracaoLegado;
$configuracao->setaValorSetor($codSetor,$codDepartamento,$codUnidade,$codOrgao,$nomSetor,$anoSetor,$usuarioResponsavel);
if (comparaValor("nom_Setor", $nomSetor, "administracao.setor", "and cod_orgao = $codOrgao and cod_unidade = $codUnidade and cod_departamento = $codDepartamento and cod_setor <> $codSetor",1)) {
    if ($configuracao->updateSetor()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomDepartamento);
            $audicao->insereAuditoria();
            $stCodSetor = $codOrgao.".".$codUnidade.".".$codDepartamento.".".$codSetor."/".$anoSetor;
            $arCodSetor = validaMascara($stMascaraSetor,$stCodSetor);
            if ($arCodSetor[0]) {
                $stCodSetor = $arCodSetor[1];
            }
            echo '<script type="text/javascript">
                 alertaAviso("'.$stCodSetor.' - '.$nomSetor.'","alterar","aviso","'.Sessao::getId().'");
                 mudaTelaPrincipal("alteraSetor.php?'.Sessao::getId().'&pagina='.$pagina.'");
                 </script>';
    } else {
            echo '<script type="text/javascript">
                 alertaAviso("'.$nomSetor.'","n_alterar","erro","'.Sessao::getId().'");
                 </script>';
                 executaFrameOculto("f.ok.disabled = false;");
    }
} else {
    echo '
        <script type="text/javascript">
        alertaAviso("O Setor '.$nomSetor.' já existe","unica","erro","'.Sessao::getId().'");
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
