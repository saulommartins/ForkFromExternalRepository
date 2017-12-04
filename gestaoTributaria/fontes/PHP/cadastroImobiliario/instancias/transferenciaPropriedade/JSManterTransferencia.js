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
<?
/**
    * Página de funções javascriptpara o cadastro de transferência de proipriedade 
    * Data de Criação   : 14/01/2005


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore
    
    * $Id: JSManterTransferencia.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.8  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function mudaMenu(func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_modulo_pass=12&cod_gestao=5&stNomeGestao=Tributária&modulos=Cadastro Imobiliário&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaFiltro(tipoBusca){
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = '';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function excluiDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function incluirAdquirentes(){
    var mensagem = validarDocumento();

    if( mensagem == '' ){
         buscaValor('MontaAdquirente');
    } else {
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
         return false;
    }
}

function validarDocumento(){
    var mensagem = '';
        
    if( document.frm.inNumCGM.value == 0 ) {
        mensagem += "@Campo CGM inválido!()";
    }
    if( document.frm.nuQuota.value == 0 ) {
        mensagem += "@Campo Quota não informado!";
    } else {
        if( numericToFloat(document.frm.nuQuota.value) == 0 ) {
            mensagem += "@Campo Quota deve ter valor maior que zero!";
            document.frm.nuQuota.focus();
        }
                        
        if( numericToFloat(document.frm.nuQuota.value) > 100 ) {
	        mensagem += "@Campo Quota deve ter valor menor ou igual a 100!";
            document.frm.nuQuota.focus();
	    }
    }
    return mensagem;
}

function limparAdquirentes() {
    document.frm.inNumCGM.value                     = '';
    document.getElementById('campoInner').innerHTML = '&nbsp;';
    document.frm.nuQuota.value                      = '';
}

function limparFormulario(){
    buscaValor('limparFormulario');
}

function limparFiltro() {
    document.frm.inNumCGM.value                     = '';
    document.getElementById('campoInner').innerHTML = '&nbsp;';
    document.frm.inInscricaoImobiliaria.value       = '';
}

function Cancelar(){
<?php
    $stLink = Sessao::read("stLink");
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

</script>
                                        
