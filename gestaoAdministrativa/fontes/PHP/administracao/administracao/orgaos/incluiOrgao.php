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

$Revision: 28401 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-06 11:31:53 -0300 (Qui, 06 Mar 2008) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_FW_LEGADO."mascarasLegado.lib.php");
include(CAM_FW_LEGADO."funcoesLegado.lib.php");
include(CAM_FW_LEGADO."auditoriaLegada.class.php");
include CAM_FW_LEGADO."configuracaoLegado.class.php";

$ctrl = $_REQUEST['ctrl'];

if (!isset($ctrl)) {
    $ctrl = 0;
}

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";

switch ($ctrl) {
case 0:
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
            mensagem += "@Nome";
            erro = true;
        }

        if (erro) alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
            return !(erro);
    }

    function IncluiNumCgm()
    {
        var stFrmAnt = document.frm.action;
        document.frm.ctrl.value = 2;
        document.frm.submit();
        document.frm.ctrl.value = 1;
        document.frm.action = stFrmAnt;
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
   </script>

<form name="frm" action="<?=$_SERVER['PHP_SELF'];?>?<?=Sessao::getId();?>" method="POST" target="oculto">
<input type="hidden" name="ctrl" value="1">
<table width="100%">
<tr><td class="alt_dados" colspan="2">Dados para Órgão</td></tr>
<tr>
    <td class=label width='30%' >*Nome do Órgão</td>
    <td class=field>
        <input type="text" name="nomOrgao" size="60" maxlength="60" value="">
    </td>
</tr>
<tr>
    <td class=label title="Usuário responsável pela unidade">Responsável</td>
    <td class=field>
        <?php geraCampoInteiro("usuarioResponsavel",10,10,$responsavel,false,'onChange="IncluiNumCgm();"'); ?>
        <input type="text" name="usuarioResponsavelN" size=40 maxlength=50 readonly="" value="<?=$usuarioResponsavelN?>">
        <a href="#" onClick="procurarCgm('frm','usuarioResponsavel','usuarioResponsavelN','usuario','<?=Sessao::getId();?>');">
        <img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>"  border=0 align="absmiddle" /></a>
    </td>
</tr>
<tr>
    <td class=field colspan=2>
    <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>
<?php
break;

case 1:

    $codOrgao = $_REQUEST['codOrgao'];
    $nomOrgao = $_REQUEST['nomOrgao'];
    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $anoExercicio = Sessao::getExercicio();
    $codOrgao = pegaID("cod_orgao", "administracao.orgao", "Where ano_exercicio = '".Sessao::getExercicio()."'");
    $configuracao = new configuracaoLegado;
    $configuracao->setaValorOrgao($codOrgao,$nomOrgao,$anoExercicio,$usuarioResponsavel);

    if (comparaValor("nom_orgao", $nomOrgao, "administracao.orgao","And ano_exercicio = '".Sessao::getExercicio()."' ",1)) {
        if ($configuracao->insertOrgao()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomOrgao);
            $audicao->insereAuditoria();
            $stCodOrgao = $codOrgao."/".$anoExercicio;
            $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
            if ($arCodOrgao[0]) {
                $stCodOrgao = $arCodOrgao[1];
            }
            echo '
                <script type="text/javascript">
                alertaAviso("'.$stCodOrgao." - ".$nomOrgao.'","incluir","aviso","'.Sessao::getId().'");
                mudaTelaPrincipal("'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'");
                </script>';
        } else {
            echo '
                <script type="text/javascript">
                alertaAviso("'.$nomOrgao.'","n_incluir","erro","'.Sessao::getId().'");
                </script>';
                executaFrameOculto("f.ok.disabled = false;");
        }
    } else {
        echo '
            <script type="text/javascript">
            alertaAviso("O Órgão '.$nomOrgao.' já existe","unica","erro","'.Sessao::getId().'");
            </script>';
            executaFrameOculto("f.ok.disabled = false;");
    }
break;

case 2:

    $usuarioResponsavel = $_REQUEST['usuarioResponsavel'];

    $select  = "SELECT nom_cgm FROM sw_cgm WHERE numcgm = ".$usuarioResponsavel;
    $dbConfig = new databaseLegado;
    $dbConfig->abreBd();
    $dbConfig->abreSelecao($select);
    $usuarioResponsavel = $dbConfig->pegaCampo("nom_cgm");
    $dbConfig->limpaSelecao();
    $dbConfig->fechaBd();

    $js .= "f.usuarioResponsavelN.value = \"".$usuarioResponsavel."\"\n";
    executaFrameOculto($js);
break;

}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';?>
