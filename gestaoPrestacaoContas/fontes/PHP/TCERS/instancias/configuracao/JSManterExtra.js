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
    * Página Javascript - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 31/01/2005


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 12203 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 20:51:50 +0000 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.04
*/

/*
$Log$
Revision 1.5  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirdados( incluirdados ){
var stMensagem = '';
var stCodReduzido = document.frm.stCodReduzido.value;
var inClassificacao = document.frm.inClassificacao.value;

if (stCodReduzido.length == 0 || trim (stCodReduzido) == "") {
    stMensagem += "@Campo Código Estrutural da Conta Contábil inválido!( )";
    }
if (inClassificacao.length == 0 || trim (inClassificacao) == "") {
    stMensagem += "@Campo Classificação inválido!( )";
    }    
if (stMensagem == ''){
    document.frm.stCtrl.value = incluirdados;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>'; 
    document.frm.stCodReduzido.value = '';
    document.frm.inClassificacao.value = '';  
    document.getElementById('stNomContaContabil').innerHTML = '&nbsp;';     
    } 
else {
    alertaAviso(stMensagem,'form','erro','<?=Sessao::getId();?>');
    return false;
    }    
}

function excluirdados( excluirdados, indice ){
    document.frm.inApagar.value = indice;
    document.frm.stCtrl.value = excluirdados;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit(); 
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';        
}

</script>
