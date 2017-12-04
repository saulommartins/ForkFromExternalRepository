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
    * Página de javascript
    * Data de Criação   : ???
    
    
    * @author Analista: ???
    * @author Desenvolvedor: ???
    
    * @ignore
    
    $Id: JSManterServidor.js 66017 2016-07-07 17:31:31Z michel $
    
    * Casos de uso: uc-04.04.07
*/

?>

<script type="text/javascript">
function incluirFoto(){
    document.frm.stCtrl.value = 'incluirFoto';
    document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function excluirFoto(){
    document.frm.stCtrl.value = 'excluirFoto';
    document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(tipoBusca,Aba){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    switch( Aba ){
        case 1:
            document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>';
            break
        case 2:
            document.frm.action = '<?=$pgOculDocumentacao;?>?<?=Sessao::getId();?>';
            break
        case 3:
            document.frm.action = '<?=$pgOculContrato;?>?<?=Sessao::getId();?>';
            break
        case 4:
            document.frm.action = '<?=$pgOculPrevidencia;?>?<?=Sessao::getId();?>';
            break
        case 5:
            document.frm.action = '<?=$pgOculDependentes;?>?<?=Sessao::getId();?>';
            break
        case 6:
            document.frm.action = '<?=$pgOculAtributos;?>?<?=Sessao::getId();?>';
            break
        default:
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
            break
    }
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget
}

function alterarDado( stAcao, Aba, inLinha ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    switch( Aba ){
        case 1:
            document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 2:
            document.frm.action = '<?=$pgOculDocumentacao;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 3:
            document.frm.action = '<?=$pgOculContrato;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 4:
            document.frm.action = '<?=$pgOculPrevidencia;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 5:
            document.frm.action = '<?=$pgOculDependentes;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 6:
            document.frm.action = '<?=$pgOculAtributos;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
    }
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    if( parametrosGET ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+parametrosGET;
    } else {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stPag, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stPag, funcao );
    }
}

</script>
