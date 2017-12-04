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

$Revision: 19203 $
$Name$
$Author: rodrigo_sr $
$Date: 2007-01-10 09:45:55 -0200 (Qua, 10 Jan 2007) $

Casos de uso: uc-01.03.91
*/

    include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
    include(CAM_FW_LEGADO."funcoesLegado.lib.php");
    include(CAM_FW_LEGADO."sistema.class.php");

    setAjuda( "UC-01.03.91" );

    $msg = new sistema;

    $ctrl = $_REQUEST['ctrl'];

    if (!(isset($ctrl))) {
        $ctrl = 0;
    }

    switch ($ctrl) {
        case 0:
    $mensagem = pegaConfiguracao("mensagem", 2, Sessao::getExercicio() );
?>
<script type="text/javascript">
function Valida()
{
        var mensagem = "";
        var erro = false;
        var campo;

        campo = document.frm.mensagem.value;
        if (campo == "") {
            mensagem += "@Mensagem";
            erro = true;
        }
        if (erro) {
                alertaAviso(mensagem,'formulario','erro','<?=Sessao::getId();?>');
        } else {
                document.frm.action = "editaMensagem.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }

                return !(erro);
    }

    function Salvar()
    {
        if (Valida()) {
            document.frm.action = "editaMensagem.php?<?=Sessao::getId()?>&ctrl=1";
            document.frm.submit();
        }
    }

    function Limpar()
    {
        document.frm.mensagem.value = "";
    }

</script>
<form action="<?php echo $PHP_SELF; ?>?<?=Sessao::getId();?>" method="POST" name="frm" target='oculto'>
    <table width="100%">
        <tr>
            <td class=alt_dados colspan="2">
                Dados para mensagem
            </td>
        </tr>
        <tr>
            <td class=label>
                *Mensagem
            </td>
            <td class=field>
                <input type="text" name="mensagem" size="80" maxlength="75" value="<?=strip_tags($mensagem);?>" >
            </td>
        </tr>
        <tr>
            <td class=field colspan=2>

               <?=geraBotaoOk2();?>
            </td>
        </tr>
</form>
<?php
    break;
    case 1:

        $mensagem = $_REQUEST['mensagem'];

        $msg->mensagem = addslashes($mensagem);
        $inPosicaoQuebra = (integer) strpos($mensagem,"\n");
        $inPosicaoQuebra = $inPosicaoQuebra < 25 ? $inPosicaoQuebra : 25;
        $stMensagem = urlencode(stripslashes(trim(substr($mensagem,0, $inPosicaoQuebra))));
        if ($msg->registraMensagem()) { //Registra a nova mensagem no banco de dados
            include CAM_FW_LEGADO."auditoriaLegada.class.php";
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), "Mensagem"); //registra os passos no auditoria
            $audicao->insereAuditoria();

            echo '
            <script type="text/javascript">
                alertaAviso("'.$stMensagem.'...","alterar","aviso", "'.Sessao::getId().'");
                mudaTelaPrincipal("'.$_SERVER['PHP_SELF'].'?'.Sessao::getId().'&ctrl=0");
            </script>';
        } else {
            SistemaLegado::exibeAviso($stMensagem."...", "n_alterar");
        }
    break;
    }

  include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
