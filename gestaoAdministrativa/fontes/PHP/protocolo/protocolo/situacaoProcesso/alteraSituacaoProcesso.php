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
    $altera = new situacaoProcesso; //chama a classe tipoProcesso
    $altera->setaVariaveis($nom);
    $altera->codigo = $codigo;
    if (!(isset($nom))) {
        $altera->mostraSituacaoProcesso($codigo);
?>
<script type="text/javascript">
    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;
        var f;

        f = document.frm;

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
            document.frm.submit();
        }
    }
</script>

<form action="alteraSituacaoProcesso.php?<?=Sessao::getId();?>" method="POST" name="frm">
<table width='70%'>
<tr><td class=alt_dados colspan=2>Altere a Situação de Processo</td></tr>
<tr><td class=label>Código:</td><td class=field><input type="text" name="cod" value="<?=$altera->codigo;?>" size="5" maxlength="4" readonly=""></td></tr>
<?php if ($altera->codigo == 1 || $altera->codigo == 2 || $altera->codigo == 3 || $altera->codigo == 4 || $altera->codigo == 5 || $altera->codigo == 9) {?>
    <tr><td class=label>*Nome:</td><td class=field><input type="text" name="nom" size="50" maxlength="60" value="<?=$altera->nome;?>" readonly=""></td></tr>
<?php } else { ?>
    <tr><td class=label>*Nome:</td><td class=field><input type="text" name="nom" size="50" maxlength="60" value="<?=$altera->nome;?>"></td></tr>
<?php } ?>

<tr><td class=field colspan=2><input type="button" onclick='Salvar();' name="salvar" value="OK" style="width: 60px">&nbsp;
<input type="reset" name="limpar" value="Limpar" style="width: 60px"></td></tr>
</table>
</form>
<?php
} else {
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("nom_situacao", $altera->nome, "sw_situacao_processo","And cod_situacao <> '".$cod."'",1)) {
        alertaAviso($PHP_SELF."?".Sessao::getId()."&codigo=$cod","A situação ".$altera->nome." já existe!","unica","erro", "'.Sessao::getId().'");
    } else {
        if ($altera->alteraSituacaoProcesso($cod)) {
            include '../../classes/auditoria.class.php';
            $audicao = new auditoria;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $altera->nome);
            $audicao->insereAuditoria();
            echo'
            <script type="text/javascript">
            alertaAviso("'.$altera->nome.'","alterar","aviso", "'.Sessao::getId().'");
            mudaTelaPrincipal("listaSituacaoProcesso.php?'.Sessao::getId().'");
            </script>';
        } else
            echo'
            <script type="text/javascript">
            alertaAviso("'.$altera->nome.'","n_alterar","erro", "'.Sessao::getId().'");
            mudaTelaPrincipal("listaSituacaoProcesso.php?'.Sessao::getId().'");
            </script>';
    }
}
    include '../../includes/rodape.php';
?>
