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
* Manutneção de orgãos
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

if (!isset($controle)) {
    $controle = 0;
}

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";

if (!isset($pagina)) {
    $pagina = 0;
}

switch ($controle) {
case 0:
    $sSQLs = "
            SELECT
                cod_orgao, nom_orgao, ano_exercicio
            FROM
                administracao.orgao
            WHERE
                cod_orgao > 0";

    $paginacao = new paginacaoLegada;
    $paginacao->pegaDados($sSQLs,"10");
    $paginacao->pegaPagina($pagina);
    $paginacao->geraLinks();
    $paginacao->pegaOrder("lower(nom_orgao)","ASC");
    $sSQL = $paginacao->geraSQL();

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $exec = "";
    $exec .= "
        <table width='100%'>
        <tr>
            <td class='alt_dados' colspan='6'>Registros de Órgão</td>
        </tr>
        <tr>
            <td class='labelleft' width='5%'>&nbsp;</td>
            <td class='labelleft' width='10%'>Código</td>
            <td class='labelleft' width='84%'>Órgão</td>
            <td class='labelleft' width='1%'>&nbsp;</td>
        </tr>";

        $count = $paginacao->contador();
        while (!$dbEmp->eof()) {
                $codOrgaof  = trim($dbEmp->pegaCampo("cod_orgao"));
                $nomOrgaof  = trim($dbEmp->pegaCampo("nom_orgao"));
                $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
                $dbEmp->vaiProximo();
                $stCodOrgao = $codOrgaof."/".$anoEf;
                $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
                if ($arCodOrgao[0]) {
                    $stCodOrgao = $arCodOrgao[1];
                }
                $exec .= "
                 <tr>
                     <td class='labelcenter'>".$count++."</td>
                     <td class=show_dados>".$stCodOrgao."</td>
                     <td class=show_dados>".$nomOrgaof."</td>
                     <td class='botao'>
                     <a href='".$_SERVER['PHP_SELF']."?".Sessao::getId()."&codOrgao=".$codOrgaof."&controle=1&pagina=".$pagina."&anoE=".$anoEf."'>
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

//Início formulário
case 1:

    $codOrgao = $_REQUEST['codOrgao'];
    $anoE = $_REQUEST['anoE'];
    $pagina = $_REQUEST['pagina'];

    $sSQL = "SELECT * FROM administracao.orgao WHERE cod_orgao = ".$codOrgao." AND ano_exercicio =".$anoE;

    $dbEmp = new dataBaseLegado;
    $dbEmp->abreBD();
    $dbEmp->abreSelecao($sSQL);
    $dbEmp->vaiPrimeiro();
    $codg_orgao   = trim($dbEmp->pegaCampo("cod_orgao"));
    $nomg_orgao   = trim($dbEmp->pegaCampo("nom_orgao"));
    $ano_orgao    = trim($dbEmp->pegaCampo("ano_exercicio"));
    $responsavel  = trim($dbEmp->pegaCampo("usuario_responsavel"));
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();

    $select  = "SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$responsavel;
    $dbConfig = new dataBaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavelN = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();
    $stCodOrgao = $codg_orgao."/".$ano_orgao;
    $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
    if ($arCodOrgao[0]) {
        $stCodOrgao = $arCodOrgao[1];
    }

?>
<script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nomOrgao.value.length;
            if (campo == 0) {
            mensagem += "@Órgão";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
            return !(erro);
      }

      function Salvar()
      {
         var f = document.frm;
         if (Valida()) {
            document.frm.submit();
         } else {
            f.ok.disabled = false;
         }
      }

        function Cancela()
        {
           mudaTelaPrincipal('<?=$PHP_SELF;?>?<?=Sessao::getId();?>&controle=0&pagina=<?=$pagina;?>');
    }

        function AlteraNumCgm()
        {
            var stFrmAnt = document.frm.action;
            document.frm.action = document.frm.action + '&codOrgao=' + <?=$codOrgao?> + '&anoE=' + <?=$anoE?>;
            document.frm.controle.value = 3;
            document.frm.submit();
            document.frm.controle.value = 2;
            document.frm.action = stFrmAnt;
        }

   </script>

<form name="frm" action="alteraOrgao.php?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="pagina" value="<?=$pagina;?>">
<input type="hidden" name="codOrgao" value="<?=$codg_orgao;?>">
<input type="hidden" name="anoOrgao" value="<?=$ano_orgao;?>">
<input type="hidden" name="controle" value=2>
<table width=100%>
<tr><td class=alt_dados colspan=2>Dados para Órgão</td></tr>
<tr>
    <td class=label>Código</td>
    <td class=field>
        <?=$stCodOrgao;?>
    </td>
</tr>
<tr>
    <td class=label width='30%'>*Nome do Órgão</td>
    <td class=field>
        <input type="text" name="nomOrgao" size="60" maxlength="60" value="<?=$nomg_orgao;?>">
    </td>
</tr>
<tr>
    <td class=label title="Usuário responsável pela unidade">Responsável</td>
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
//Executa alteração
case 2:
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];
    $usuarioResponsavelN = $_REQUEST['usuarioResponsavelN'];
    $nomOrgao = $_REQUEST['nomOrgao'];
    $codOrgao = $_REQUEST['codOrgao'];
    $stCodOrgao = $_REQUEST['stCodOrgao'];
    $anoOrgao = $_REQUEST['anoOrgao'];
    $controle = $_REQUEST['controle'];
    $pagina = $_REQUEST['pagina'];

    $configuracao = new configuracaoLegado;
    $configuracao->setaValorOrgao($codOrgao,$nomOrgao,$anoOrgao,$usuarioResponsavel);
    $stCodOrgao = $codOrgao."/".$anoOrgao;
    $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
    if ($arCodOrgao[0]) {
        $stCodOrgao = $arCodOrgao[1];
    }
    $objeto = $stCodOrgao." - ".$nomOrgao;
    if (comparaValor("nom_orgao", $nomOrgao, "administracao.orgao", "and cod_orgao <> $codOrgao and ano_exercicio = $anoOrgao ",1)) {
        if ($configuracao->updateOrgao()) {
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
                $audicao->insereAuditoria();
                echo '<script type="text/javascript">
                    alertaAviso("'.$objeto.'","alterar","aviso","'.Sessao::getId().'");
                    mudaTelaPrincipal("alteraOrgao.php?'.Sessao::getId().'&pagina='.$pagina.'&controle=0");
                    </script>';
        } else {
                echo '<script type="text/javascript">
                    alertaAviso("'.$nomOrgao.'","n_alterar","erro","'.Sessao::getId().'");
                    </script>';
        }
    } else {
        echo '
            <script type="text/javascript">
            alertaAviso("O Órgão '.$nomOrgao.' já existe","unica","erro","'.Sessao::getId().'");
            </script>';
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

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\"\n";
    executaFrameOculto($js);
break;

}
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
