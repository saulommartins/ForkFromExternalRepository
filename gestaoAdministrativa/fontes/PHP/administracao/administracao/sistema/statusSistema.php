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
* Manutneção do sistema
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27765 $
$Name$
$Author: luiz $
$Date: 2008-01-28 07:40:28 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.03.91
*/

      include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
      include(CAM_FW_LEGADO."funcoesLegado.lib.php");
      include(CAM_FW_LEGADO."sistema.class.php");

      setAjuda( "UC-01.03.91" );

      $ctrl = $_REQUEST['ctrl'];

      if (!(isset($ctrl))) {
        $ctrl = 0;
      }
      switch ($ctrl) {
        case 0:
      $stat = new sistema;
      $atual = $stat->consultaStatus(); //Variável recebe status atual do sistema, 'A' Ativo ou 'I' Inativo
      if ($atual == "A") {
        $atual = "Ativo";
      }
      if ($atual == "I") {
        $atual = "Inativo";
      }
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;

        campo = document.frm.status.checked;
        if (campo == false) {
            mensagem += "@Status";
            erro = true;
        }
        if (erro) {alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');}

        return !(erro);
    }

    function Salvar()
    {
        if (Valida) {
            document.frm.action = "statusSistema.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }
    }
</script>
<form name="frm" action="statusSistema.php?<?=Sessao::getId()?>" method="post">
    <table width="100%">
        <tr>
            <td class="alt_dados" colspan="2">
                Dados para Status
            </td>
        </tr>
<tr>
            <td class="label" width="20%">
                Banco de Dados
            </td>
            <td class="field" width="80%">
                <?=BD_NAME;?>
            </td>
        </tr>
<tr>
            <td class="label" width="20%">
                Porta
            </td>
            <td class="field" width="80%">
                <?=BD_PORT;?>
            </td>
        </tr>
<tr>
            <td class="label" width="20%">
                Host
            </td>
            <td class="field" width="80%">
                <?=BD_HOST;?>
            </td>
        </tr>

        <tr>
            <td class="label" width="20%">
                Status atual
            </td>
            <td class="field" width="80%">
                <?=$atual?>
            </td>
        </tr>
        <tr>
            <td class="label">
                *Status
            </td>
            <td class="field">
                <input type="radio" name="status" value="A" <?php if( $atual == 'Ativo' ) echo "checked";?>>Ativo<br>
                <input type="radio" name="status" value="I" <?php if( $atual == 'Inativo' ) echo "checked";?>>Inativo
            </td>
        </tr>
        <tr>
            <td class="field" colspan="2">
                <?=geraBotaoOk();?>
            </td>
        </tr>
    </table>
</form>

<?php
    break;
    case 1:

        $status = $_REQUEST['status'];

        $sMensagem = "";
        if ($status == "A") {
            $sMensagem = "Ativo";
        } else {
            $sMensagem = "Inativo";
        }
        $stat = new sistema;
        if ($stat->alteraStatus($status)) { //Altera o status do sistema
            include CAM_FW_LEGADO."auditoriaLegada.class.php";
            $audicao = new auditoriaLegada;
        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $atual);
            $audicao->insereAuditoria();

            echo '
            <script type="text/javascript">
                alertaAviso("'.$sMensagem.'","alterar","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&ctrl=0");
            </script>';
        } else {
            echo '
            <script type="text/javascript">
                alertaAviso("'.$sMensagem.'","n_alterar","erro", "'.Sessao::getId().'");
                mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'&ctrl=0");
            </script>';
        }
    }
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
