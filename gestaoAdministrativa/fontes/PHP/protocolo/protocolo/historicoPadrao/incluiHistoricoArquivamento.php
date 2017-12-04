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
* Arquivo de implementação de manutenção de histórico de arquivamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24719 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:53:04 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.92
*/

include '../../../framework/include/cabecalho.inc.php';
include CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include CAM_FRAMEWORK."legado/auditoriaLegada.class.php";
setAjuda('uc-01.06.92');

if (!(isset($_REQUEST["ctrl"]))) {
      $ctrl = 0;
} else {
      $ctrl = $_REQUEST["ctrl"];
}

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

        campo = trim( document.frm.nomHistorico.value );
            if (campo == "") {
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
</script>

<form name="frm" action="incluiHistoricoArquivamento.php?<?=Sessao::getId()?>&ctrl=1" method="POST" onSubmit='return  Valida();'>
<table width="100%">
<tr>
    <td class="alt_dados" colspan=2>Motivo do Arquivamento</td>
</tr>

<tr>
    <td class=label width=30% title="Descrição">*Descrição</td>
    <td class=field><input type="text" name="nomHistorico" size=60 maxlength=60></td>
</tr>

<td class=field colspan="2">
    <?php geraBotaoOk();?></td>

</table>
</form>
<?php
    break;

case 1:
    $ok = true;
    if (!comparaValor("nom_historico", $_REQUEST["nomHistorico"], "sw_historico_arquivamento","",1)) {
        alertaAviso($PHP_SELF,"O Motivo do Arquivamento ".$_REQUEST["nomHistorico"]." já existe!","unica","erro","'.Sessao::getId().'");
        $ok = false;
    }
    if ($ok) {
    $idCodHistorico = pegaID("cod_historico", "sw_historico_arquivamento");
    include '../configProtocolo.class.php';
    $configura = new configProtocolo;
    $configura->setaVariaveis($idCodHistorico, $_REQUEST["nomHistorico"]);
    if ($configura->incluiHistoricoArquivamento()) {
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $_REQUEST["nomHistorico"]);
            $audicao->insereAuditoria();
            echo '
            <script type="text/javascript">
                alertaAviso("'.$_REQUEST["nomHistorico"].'","incluir","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
        } else
            echo '
            <script type="text/javascript">
                alertaAviso("'.$_REQUEST["nomHistorico"].'","n_incluir","erro", "'.Sessao::getId().'");
                //mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
    }
break;
}
include '../../../framework/include/rodape.inc.php';
//include "../../includes/rodape.php";
?>
