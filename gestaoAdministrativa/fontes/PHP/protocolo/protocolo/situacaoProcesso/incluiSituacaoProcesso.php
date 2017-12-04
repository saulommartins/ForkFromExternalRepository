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
* Arquivo de implementação de situação de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3190 $
$Name$
$Author: lizandro $
$Date: 2005-11-30 17:57:09 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.98
*/
?>
<?php
    include '../../../framework/include/cabecalho.inc.php';
    include '../situacaoProcesso.class.php';
    $inclui = new situacaoProcesso;
    $inclui->setaVariaveis($nom);
    if (!(isset($nom))) {
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f;

        f = document.frmSituacao;

        campo = f.nom.value.length;
            if (campo==0) {
                mensagem += "@O campo nome é obrigatório";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        if (Valida()) {
            document.frmSituacao.submit();
        }
    }
</script>
<form action="incluiSituacaoProcesso.php?<?=Sessao::getId();?>" method="POST" name="frmSituacao">
    <table width = 70%>
    <tr><td class=alt_dados colspan=2>Insira a Situação de Processo</td></tr>
<!--
    <tr>
        <td class=label>*Código:</td>
        <td class=field><input type="text" name="cod" value="<?=$inclui->codigo;?>" size="5" maxlength="4" readonly=""></td>
    </tr>
-->
    <tr>
        <td class=label>*Nome:</td>
        <td class=field><input type="text" name="nom" size="30" maxlength="60"></td>
    </tr>
    <tr>
        <td class=field colspan=2><input type="button" onClick="Salvar();" name="salvar" value="OK" style="width: 60px">&nbsp;
        <input type="reset" name="limpar" value="Limpar" style="width: 60px"></td>
    </tr>
    </table>
</form>
<?php
} else {
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_situacao", $inclui->nome, "sw_situacao_processo","",1)) {
        alertaAviso($PHP_SELF.'?'.Sessao::getId(),"A situação ".$inclui->nome." já existe!","unica","erro", "'.Sessao::getId().'");
    } else {
        if ($inclui->incluiSituacaoProcesso()) {
            include '../../classes/auditoria.class.php';
            $audicao = new auditoria;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $inclui->nome);
            $audicao->insereAuditoria();
            echo'
            <script type="text/javascript">
            alertaAviso("'.$inclui->nome.'","incluir","aviso", "'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';        } else
            echo'
            <script type="text/javascript">
            alertaAviso("'.$inclui->nome.'","n_incluir","erro", "'.Sessao::getId().'");
            mudaTelaPrincipal("'.$PHP_SELF.'?'.Sessao::getId().'");
            </script>';
    }
}
    include '../../includes/rodape.php';
?>
