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
    * Página de 
    * Data de criação : 03/11/2005
  

    * @author Analista: 
    * @author Programador: Fernando Zank Correa Evangelista 

    Caso de uso: uc-03.01.21
    
    $Id: JSListaPatrimonial.js 59612 2014-09-02 12:00:51Z gelson $
    
    **/

?>
<script>

function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.stCtrl.value = variavel;
    document.frm.action = 'OCFichaPatrimonial.php?+<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function limpaAtributoDinamico()
{
	document.getElementById('spnListaAtributos').innerHTML = '';
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
                if( stCampo ){
                    if(stCampo.type == undefined ){
                        if( stCampo[0].type == 'radio' ){
                            for( j=0; j<stCampo.length; j++ ) {
                                 if( stCampo[j].checked == true ) {
                                     stLink += "&"+stCampo[j].name+"="+trim( stCampo[j].value );
                                 }
                             }
                        }
                    }else if (stCampo.type == 'select-multiple') {
                         for( j=0; j<stCampo.length; j++ ) {
                            stLink += "&"+stCampo.name+"="+trim( stCampo[j].value );
                         }
                    }else {
                         stLink += "&"+stCampo.name+"="+trim( stCampo.value );
                    }

                }
            }
        }
    } else {
	for( i=0 ; i<f.elements.length ; i++) {
            if( typeof(f.elements[i]) == 'object' ){               

                if( f.elements[i].type == 'radio' || f.elements[i].type == 'checkbox' ){
                    if( f.elements[i].checked == true ){
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                } else if (f.elements[i].type == 'select-multiple') {
                    for( j=0; j<f.elements[i].length; j++ ) {
                        stLink += "&"+f.elements[i].name+"="+trim( f.elements[i][j].value );
                    }
                }
                else{
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}


<?=  $jsOnload ? $jsOnload : $jsOnLoad;  ?>
</script>