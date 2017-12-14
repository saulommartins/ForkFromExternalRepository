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
    * Arquivo com funcoes JavaScript para Consulta de Arrecadacao
    * Data de Criação: 09/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Id: $

    $Revision: 22671 $
    $Name$
    $Autor: $
    $Date: 2007-05-17 16:57:37 -0300 (Qui, 17 Mai 2007) $

    * Casos de uso: uc-01.00.00
*/
?>

<script type="text/javascript">

function executaFuncaoAjax(funcao, parametrosGET, sincrono)
{
    stPaginaProcessamento = '<?=isset($pgAjax) ? $pgAjax : $pgOcul;?>?<?=Sessao::getId();?>';

    if (parametrosGET) {
        stLink = stPaginaProcessamento + parametrosGET;
    } else {
        stLink = stPaginaProcessamento;
    }
    if (sincrono) {
        ajaxJavaScriptSincrono( stLink, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stLink, funcao );
    }
}

function montaParametrosFormDinamicoGET(funcao, campos, tipo, sincrono)
{
    var stLink = '';
    var f = document.frm;
    var d = document;
    var form = "parent.telaPrincipal.document.getElementsByName('frm')";
    var formSize = eval("parent.telaPrincipal.document.getElementsByName('frm').length");
    var formAux = "";
    var index = 1;

    if (campos) {
        if ( campos.search(/,/) > 0 ) {
            arCampos = campos.split(",");
        } else {
            arCampos = new Array();
            arCampos[0] = campos;
        }

        for (index; index < formSize; index++) {
            formAux = form+"["+index+"].elements";

            if (typeof (eval(""+formAux+".inCodBem"+tipo+".value;")) != 'undefined') {
                if (eval(""+formAux+".inCodBem"+tipo+".value;") != '') {
                    form = formAux;
                    break;
                }
            }
        }

        for (i=0 ; i<arCampos.length ; i++) {

            if (arCampos[i] == 'inIdInventario' || arCampos[i] == 'stExercicio') {
                stCampo = eval( "parent.telaPrincipal.document.getElementsByName('frm')[0].elements."+arCampos[i] );
            } else {
                stCampo = eval( ""+form+"."+arCampos[i] );
            }

            if ( typeof(stCampo) == 'object' ) {
                if (stCampo) {
                    if (stCampo.type == undefined) {
                        if (stCampo[0].type == 'radio') {
                            for (j=0; j<stCampo.length; j++) {
                                 if (stCampo[j].checked == true) {
                                     stLink += "&"+stCampo[j].name+"="+trim( stCampo[j].value );
                                 }
                             }
                        }
                    } else if (stCampo.type == 'select-multiple') {
                         for (j=0; j<stCampo.length; j++) {
                            stLink += "&"+stCampo.name+"="+trim( stCampo[j].value );
                         }
                    } else {
                         stLink += "&"+stCampo.name+"="+trim( stCampo.value );
                    }

                }
            }
        }
    } else {
        for (i=0 ; i<f.elements.length ; i++) {
            if ( typeof(f.elements[i]) == 'object' ) {

                if (f.elements[i].type == 'radio' || f.elements[i].type == 'checkbox') {
                    if (f.elements[i].checked == true) {
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                } else if (f.elements[i].type == 'select-multiple') {
                    for (j=0; j<f.elements[i].length; j++) {
                        stLink += "&"+f.elements[i].name+"="+trim( f.elements[i][j].value );
                    }
                } else {
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }

    executaFuncaoAjax( funcao, stLink, sincrono );
}

function executaFuncaoAjaxPOST(funcao, parametrosPOST, sincrono)
{
    stPaginaProcessamento = '<?=isset($pgAjax) ? $pgAjax : $pgOcul;?>?<?=Sessao::getId();?>';

    if (sincrono) {
        ajaxJavaScriptSincronoPOST( stPaginaProcessamento, parametrosPOST, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScriptPOST( stPaginaProcessamento, parametrosPOST, funcao );
    }
}

function montaParametrosGET(funcao, campos, sincrono)
{
    var stLink = '';
    var f = document.frm;
    var d = document;

    if (campos) {
        if ( campos.search(/,/) > 0 ) {
            arCampos = campos.split(",");
        } else {
            arCampos = new Array();
            arCampos[0] = campos;
        }
        for (i=0 ; i<arCampos.length ; i++) {
            stCampo = eval( 'document.frm.'+arCampos[i] );
            if ( typeof(stCampo) == 'object' ) {
                if (stCampo) {
                    if (stCampo.type == undefined) {
                        if (stCampo[0].type == 'radio') {
                            for (j=0; j<stCampo.length; j++) {
                                 if (stCampo[j].checked == true) {
                                     stLink += "&"+stCampo[j].name+"="+trim( stCampo[j].value );
                                 }
                             }
                        }
                    } else if (stCampo.type == 'select-multiple') {
                         for (j=0; j<stCampo.length; j++) {
                            stLink += "&"+stCampo.name+"="+trim( stCampo[j].value );
                         }
                    } else {
                         stLink += "&"+stCampo.name+"="+trim( stCampo.value );
                    }

                }
            }
        }
    } else {
        for (i=0 ; i<f.elements.length ; i++) {
            if ( typeof(f.elements[i]) == 'object' ) {

                if (f.elements[i].type == 'radio' || f.elements[i].type == 'checkbox') {
                    if (f.elements[i].checked == true) {
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                } else if (f.elements[i].type == 'select-multiple') {
                    for (j=0; j<f.elements[i].length; j++) {
                        stLink += "&"+f.elements[i].name+"="+trim( f.elements[i][j].value );
                    }
                } else {
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }

    executaFuncaoAjax( funcao, stLink, sincrono );
}

function executaFuncaoAjaxPOST(funcao, parametrosPOST, sincrono)
{
    stPaginaProcessamento = '<?=isset($pgAjax) ? $pgAjax : $pgOcul;?>?<?=Sessao::getId();?>';

    if (sincrono) {
        ajaxJavaScriptSincronoPOST( stPaginaProcessamento, parametrosPOST, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScriptPOST( stPaginaProcessamento, parametrosPOST, funcao );
    }
}

function montaParametrosPOST(funcao, campos, sincrono)
{
    var stLink = '';
    var f = document.frm;
    var d = document;

    if (campos) {
        if ( campos.search(/,/) > 0 ) {
            arCampos = campos.split(",");
        } else {
            arCampos = new Array();
            arCampos[0] = campos;
        }
        for (i=0 ; i<arCampos.length ; i++) {
            stCampo = eval( 'document.frm.'+arCampos[i] );
            if ( typeof(stCampo) == 'object' ) {
                if (stCampo) {
                    if (stCampo.type == undefined) {
                        if (stCampo[0].type == 'radio') {
                            for (j=0; j<stCampo.length; j++) {
                                 if (stCampo[j].checked == true) {
                                     stLink += "&"+stCampo[j].name+"="+trim( stCampo[j].value );
                                 }
                             }
                        }
                    } else if (stCampo.type == 'select-multiple') {
                         for (j=0; j<stCampo.length; j++) {
                            stLink += "&"+stCampo.name+"="+trim( stCampo[j].value );
                         }
                    } else {
                         stLink += "&"+stCampo.name+"="+trim( stCampo.value );
                    }

                }
            }
        }
    } else {
        for (i=0 ; i<f.elements.length ; i++) {
            if ( typeof(f.elements[i]) == 'object' ) {

                if (f.elements[i].type == 'radio' || f.elements[i].type == 'checkbox') {
                    if (f.elements[i].checked == true) {
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                } else if (f.elements[i].type == 'select-multiple') {
                    for (j=0; j<f.elements[i].length; j++) {
                        stLink += "&"+f.elements[i].name+"="+trim( f.elements[i][j].value );
                    }
                } else {
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }

    executaFuncaoAjaxPOST( funcao, stLink, sincrono );
}

<?php
    //Criado trecho de código para que seja verificado tanto a variável $jsOnload como a $jsOnLoad, respeitando o case sensitive do PHP
    if (isset($jsOnload)) {
        echo $jsOnload;
    } else {
        if (isset($jsOnLoad)) {
            echo $jsOnLoad;
        }
    }
?>
</script>
