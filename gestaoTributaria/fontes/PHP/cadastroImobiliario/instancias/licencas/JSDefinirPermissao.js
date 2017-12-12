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
/** Página de JavaScript Definir Permissao

    * Data de Criação   : 17/03/2008


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato 

    * $Id: JSDefinirPermissao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

}

function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    stPaginaProcessamento = '<?=$pgAjax ? $pgAjax : $pgOcul;?>?<?=Sessao::getId();?>';
    if( parametrosGET ) {
        stLink = stPaginaProcessamento + parametrosGET;
    } else {
        stLink = stPaginaProcessamento;
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stLink, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stLink, funcao );
    }
}

function montaParametrosGET( funcao, campos, sincrono  ) {
    var stLink = '';
    var f = document.frm;
    var d = document;

    if( campos ) {
        if ( campos.search(/,/) > 0 ) {
            arCampos = campos.split(",");
        } else {
            arCampos = new Array();
            arCampos[0] = campos;
        }
        for( i=0 ; i<arCampos.length ; i++ ) {
            stCampo = eval( 'document.frm.'+arCampos[i] );
            if( typeof(stCampo) == 'object' ){
                if ( stCampo[0] ){              
                     if ( stCampo[0].type == 'radio' ) {
                         for( j=0; j<stCampo.length; j++ ) {
                             if( stCampo[j].checked == true ) {
                                 stLink += "&"+arCampos[i]+"="+trim( stCampo[j].value );
                             }
                         }
                     } else {
                         stLink += "&"+arCampos[i]+"="+trim( stCampo.value );
                     }
                } else {
                    stLink+= "&"+arCampos[i]+"="+trim( stCampo.value );
                }
            }
        }
    } else {
        for( i=0 ; i<f.elements.length ; i++) {
            if( typeof(f.elements[i]) == 'object' ){               
                if( f.elements[i].type == 'radio' ){
                    if( f.elements[i].checked == true ){
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                }else{
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}

function excluirUsuario( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'ExcluirUsuario';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
   document.frm.cmbTipoLicenca.focus();
}
</script>