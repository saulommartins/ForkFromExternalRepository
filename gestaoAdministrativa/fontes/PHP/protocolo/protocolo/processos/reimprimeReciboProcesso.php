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
* Arquivo de implementação de manutenção de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.06.98
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_FW_LEGADO."paginacaoLegado.class.php");
include(CAM_FW_LEGADO."botoesPdfLegado.class.php");

$sAnoExercicio = pegaconfiguracao("ano_exercicio");
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.iCodProcesso.value.length;
            if (campo==0) {
                erro = true;
                mensagem += "@O campo código do processo é obrigatório!";
            }

        campo = document.frm.sAnoExercicio.value.length;
            if (campo==0) {
                erro = true;
                mensagem += "@O campo exercício é obrigatório!";
            }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);

    }// Fim da function Valida

    function Salvar()
    {
            document.frm.submit();
    }

    function Volta()
    {
        document.frm.action='consultaProcesso.php?<?=Sessao::getId()?>';
        document.frm.submit();
    }
</script>
<form name='frm' action="imprimeReciboProcesso.php?<?=Sessao::getId();?>" method="POST">
<table width='60%'>
<tr><td class=alt_dados colspan=2>Informe o Número de Processo</td></tr>
    <tr>
        <td class="label" width=30%>*Número Processo:</td>
        <td class="field">
            <input type="hidden" name="pagina" value="<?=$pagina?>">
            <input type="hidden" name="anoExercicio" value="<?=$anoExercicio?>">
            <input type="hidden" name="sAnoExercicio" value="<?=$anoExercicio?>">
            <input type="hidden" name="verificador" value="false">
            <input type="hidden" name="ctrl" value="2">
            <input type="hidden" name="controle" value="0">
            <input type="hidden" name="codProcesso" value="<?=$codProcesso?>">
            <input type="hidden" name="iCodProcesso" value="<?=$codProcesso?>">
            <?=$codProcesso?>
            <!--<input type="text" name="iCodProcesso" size=16 maxlength=16>
            &nbsp;<a href='javascript:procuraProcesso("frm","codProcesso","anoExercicio","<?=Sessao::getId()?>");'><img
            src="../../images/procuracgm.gif" alt="Procurar Processo" width=20 height=20 border=0></a>-->
        </td>
    </tr>
    <tr>
        <td colspan='2' class='field'>
            <input type="button" onClick='Salvar();' value="OK" style='width: 60px;'>
            <input type="reset" value="Limpar" style='width: 60px;'>
            <input type="button" onClick='Volta();' value="Voltar" style='width: 60px;'>
        </td>
    </tr>
</table>
</form>
<?php
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
