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
/** Página de JS da Emissao

    * Data de Criação   : 27/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato 
    * @ignore

	* $Id: JSManterEmissao.js 32939 2008-09-03 21:14:50Z domluc $

    * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.2  2007/04/23 18:31:09  dibueno
*** empty log message ***

Revision 1.1  2006/10/23 16:12:21  dibueno
*** empty log message ***

Revision 1.1  2006/10/03 09:57:51  cercato
*** empty log message ***

*/

?>

<script type="text/javascript">
function Cancelar () {
    <?php
        $link = Sessao::read( "link" );
        $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."";
    ?>

    mudaTelaPrincipal("<?=$pgFilt.'?'.Sessao::getId().$stLink;?>");
}

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;

    while(cont < document.frm.elements.length){
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
            document.frm.elements[cont].checked = campoT;
        }

        cont++;
    }
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

function buscaValor(tipoBusca, valor){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.HdnQual.value = valor;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
