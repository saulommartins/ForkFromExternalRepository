<script type="text/javascript">
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
</script>
<?php
/**
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4642 $
$Name$
$Author: cassiano $
$Date: 2006-01-04 10:33:50 -0200 (Qua, 04 Jan 2006) $

Casos de uso: uc-01.03.96
*/
?>

<script type="text/javascript">

function goOculto(stControle){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = stControle;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


function buscaCadastro(){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = "MontaCadastro";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


function AdicionaValores(stControle){
    var stMensagem = '';
    var stValor = document.frm.stValorPadrao.value;
    
    if (stValor.length == 0) {
        stMensagem += "@Campo valor inválido!( )";
    }
    if ( trim (stValor) == "" ) {
        stMensagem += "@Impossível inserir valores em branco!";
    }
    if (stMensagem == ''){
        document.frm.btnAlterar.disabled = true;
        goOculto(stControle);
    } else {
        alertaAviso(stMensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function modificaDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaValores(){
    document.frm.stValorPadrao.value  = '';
    document.frm.boAtivo[0].checked   = true;
    document.frm.boAtivo[1].checked   = false;
}
</script>
