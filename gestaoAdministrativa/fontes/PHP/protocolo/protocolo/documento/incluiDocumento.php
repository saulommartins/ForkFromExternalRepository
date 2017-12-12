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
* Arquivo de implementação de manutenção de documento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24721 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:58:43 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.06.96
*/

include '../../../framework/include/cabecalho.inc.php';
include (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"    );
include '../documentos.class.php';
include (CAM_FRAMEWORK."legado/auditoriaLegada.class.php");
setAjuda('uc-01.06.96');

if (!(isset($_REQUEST["nomDocumento"]))) {
?>

<script type="text/javascript">

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = trim( document.frm.nomDocumento.value );
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
<form name="frm" action="incluiDocumento.php?<?=Sessao::getId();?>" method="POST" onsubmit='return Valida();'>
    <table width="100%">
        <tr>
            <td class="alt_dados" colspan=2>Dados para documento</td>
        </tr>

        <tr>
            <td class="label" width="30%" title="Descrição do documento">*Descrição:</td>
            <td class="field">
                <input type="text" name="nomDocumento" size=60 maxlength=60>
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
} else {
    $nomDocumento = addSlashes($_REQUEST["nomDocumento"]);
    $nId = pegaID("cod_documento","sw_documento");
    $documento = new documentos;
    $documento->setaVariaveis($nId,$nomDocumento);
        //Verifica se já existe o registro a ser incluido
        if (!comparaValor("nom_documento", $nomDocumento, "sw_documento","",1)) {
            alertaAviso("incluiDocumento.php?'.Sessao::getId().'","O documento ".$nomDocumento." já existe!","unica","erro", ".Sessao::getId().");
        } else {
            if ($documento->insereDocumento()) {
                        $audicao = new auditoriaLegada;
                        $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $nomDocumento);
                        $audicao->insereAuditoria();
                        echo '<script type="text/javascript">
                        alertaAviso("'.$nomDocumento.'","incluir","aviso", "'.Sessao::getId().'");
                        window.location = "incluiDocumento.php?'.Sessao::getId().'";
                        </script>';
                        } else {
                        echo '<script type="text/javascript">
                        alertaAviso("'.$nomDocumento.'","n_incluir","erro", "'.Sessao::getId().'");
                        window.location = "incluiDocumento.php?'.Sessao::getId().'";
                        </script>';
                        }
            }
}
?>

<?php
include '../../../framework/include/rodape.inc.php';
?>
