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
* Manutenção de banco
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
  include (CAM_FW_LEGADO."funcoesLegado.lib.php");
  include (CAM_FW_LEGADO."auditoriaLegada.class.php"); //Inclui classe para inserir auditoria
  include '../banco.class.php';

if (!isset($controle)) {
    $controle = 0;
    $codBanco = "";
    $nomBanco = "";
}

switch ($controle) {
case 0:
?>
<script type="text/javascript">
    function validaCodBanco(iCod)
    {
        //alert ("valor-"+document.frm.codAgencia.value);
        var cod = parseInt(iCod);
        var msg = "O Código do Banco "+iCod+" é Inválido";
        var f = document.frm;
        if (f.codBanco.value==0) {
            f.codBanco.value = "";
            f.codBanco.focus();
            alertaAviso(msg,'unica','erro','<?=Sessao::getId()?>','');
        }
    }

    function Valida()
    {
        var mensagem = "";
        var erro = false;
        var campo;
        var f;

        f = document.frm;

        campo = f.codBanco.value.length;
            if (campo==0) {
                mensagem += "@Código";
                erro = true;
            }

        campo = f.nomBanco.value.length;
            if (campo==0) {
                mensagem += "@Nome";
                erro = true;
            }

        if (erro) alertaAviso(mensagem,'formulario','','<?=Sessao::getId()?>','');
        return !(erro);
    }// Fim da function Valida

    //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
    function Salvar()
    {
        document.frm.ok.disabled = true;
        if (Valida()) {
            document.frm.submit();
        } else {
            document.frm.ok.disabled = false;
        }
    }
</script>

<form name='frm' method='post' target="oculto" action='<?=$PHP_SELF;?>?<?=Sessao::getId()?>'>
<input type='hidden' name='controle' value='1'>
<table width='100%'>
<tr><td class='alt_dados' colspan='2'>Dados para Banco</td></tr>
<tr>
    <td class='label' width='30%'>*Código</td>
    <td class='field' width='70%'>
        <input type='text' name='codBanco' value="<?=$codBanco;?>" size='5' maxlength='4' onBlur="validaCodBanco(this.value);this.value=incluiZerosAEsquerda(this.value,4,this,false);">
    </td>
</tr>
<tr>
    <td class='label' width='30%'>*Nome</td>
    <td class='field' width='70%'>
        <input type='text' name='nomBanco' value="<?=$nomBanco;?>" size='40' maxlength='80'>
    </td>
</tr>
<tr>
    <td colspan='2' class='field'>
        <?php geraBotaoOk(); ?>
    </td>
</tr>
</table>
</form>

<?php
    break;
case 1:
    $ok = true;
    $objeto = "Banco ".$codBanco." - ".$nomBanco;
    //$pag = $PHP_SELF."?".Sessao::getId()."&controle=0&codBanco=".$codBanco."&nomBanco=".$nomBanco;
/*** Primeiro valida os dados, depois faz a inclusão ***/
    //Verifica se já existe o registro a ser incluido
    if (!comparaValor("cod_banco", $codBanco, "administracao.banco")) {
        alertaAviso($pag,"O código ".$codBanco." já existe!","unica","erro","");
        $ok = false;
    }
    if (!comparaValor("nom_banco", $nomBanco, "administracao.banco","",1)) {
        alertaAviso($pag,"O nome ".$nomBanco." já existe!","unica","erro","");
        $ok = false;
    }
/*** Se não houver restrições faz a inclusão dos dados ***/
    if ($ok) {
        $banco = new banco($codBanco,$nomBanco);
        if ($banco->incluirBanco()) {
            //Insere auditoria
            $audicao = new auditoriaLegada;
            $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
            $audicao->insereAuditoria();
            //Exibe mensagem e retorna para a página padrão
            alertaAviso($PHP_SELF,$objeto,"incluir","aviso","");
        } else {
            exibeAviso($objeto,"n_incluir","erro");
            $js = "f.ok.disabled = false;";
            executaFrameOculto($js);
        }
    }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php'; //Insere o fim da página html
?>
