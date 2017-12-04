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
    * Arquivo JavaScript
    * Data de Criação   : 06/09/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.04.02

*/

/*
$Log$
Revision 1.6  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function gerarCodigo(){
    document.frm.stCodVerificador.value = document.applets[0].getHashMacAddress();
}

function mudaStatusResponsavel( inIdUsuario, boNovoResponsavel ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'mudaStatusResponsavel';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&boNovoResponsavel='+boNovoResponsavel+'&inIdUsuario='+inIdUsuario;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function montaListaUsuario(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'montaListaUsuario';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function incluirUsuario() {
    var mensagem = "";
   
    if(!document.frm.inNumCgm.value)
        mensagem += '@Campo Usuário de Terminal de Caixa inválido!()';
    if(!document.frm.stNomCgm.value && document.frm.inNumCgm.value )
        mensagem += '@Campo Usuário de Terminal de Caixa inválido!()';
    
    if(!document.frm.boResponsavel.value)
        mensagem += '@Campo Responsável inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluirUsuario';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparUsuario();
    }
    
}

function excluirUsuario( inNumCgm ){
    document.frm.stCtrl.value = 'excluirUsuario';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumCgm=' + inNumCgm;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function limparUsuario() {
    document.frm.inNumCgm.value = '';
    document.frm.stNomCgm.value = '';
    document.getElementById( 'stNomCgm' ).innerHTML = "&nbsp;"
    document.frm.boResponsavel.value = '';
}

</script>
                
