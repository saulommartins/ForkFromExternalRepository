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
    * Data de Criação   : 18/10/2005


    * @author Analista: Diego 
    * @author Desenvolvedor: Diego

    * @ignore

    $Revision: 23022 $
    $Name$
    $Autor:$
    $Date: 2007-06-01 11:31:13 -0300 (Sex, 01 Jun 2007) $

    * Casos de uso: uc-03.03.04

*/

/*
$Log$
Revision 1.14  2007/06/01 14:31:13  hboaventura
Bug #8668#

Revision 1.13  2007/05/23 14:52:53  tonismar
#8668

Revision 1.12  2007/03/15 21:02:19  tonismar
bug #8668

Revision 1.11  2007/02/07 15:39:13  hboaventura
Correção de bug

Revision 1.10  2007/02/06 13:03:55  hboaventura
Bug #8123#

Revision 1.9  2007/01/22 15:08:06  hboaventura
Bug #8123#, #8151#

Revision 1.8  2007/01/18 15:14:23  hboaventura
Bug #8123#

Revision 1.7  2006/07/06 14:00:25  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:09:52  diego


*/
?>

<script type="text/javascript">

function Limpar(){}

function goOculto(stControle)
{
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
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


function AdicionaValores(stControle)
{
/*var stDebug = '';

for(f = 0; f < document.frm.elements.length; f++)
{
    stDebug += document.frm.elements[f].name + ' = ' + document.frm.elements[f].value + '\n';
}


alert(stDebug);
*/

    var stMensagem = '';

    var stDescricaoCatalogo = document.frm.stDescricaoCatalogo.value;
    //var stMascara = document.frm.stMascara.value;
    var stDescricaoNivel = document.frm.stDescricaoNivel.value;
    
    if (stDescricaoCatalogo.length == 0) 
    {
        stMensagem += "@Campo descrição de catálogo inválido!( )";
    }

    /*if (stMascara.length == 0) 
    {
        stMensagem += "@Campo máscara inválido!( )";
    }*/

    if (stDescricaoNivel.length == 0) 
    {
        stMensagem += "@Campo descrição de nível inválido!( )";
    }

    if ( trim (stDescricaoCatalogo) == "" || trim(stDescricaoNivel) == "") 
    {
        stMensagem += "@Impossível inserir valores em branco!";
    }
    
    if (stMensagem == '')
    {
        //document.frm.btnAlterar.disabled = true;
        //document.frm.btnIncluir.disabled = false;
        goOculto(stControle);
    } 
    else 
    {
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

    if( document.getElementById('stMascara') ){
        document.getElementById('stMascara').value = '';
    }/*else{
        document.getElementById('stMascara').innerHTML = '';
    }*/
    document.frm.stDescricaoNivel.value  = '';
    //document.getElementById('spnFormulario').innerHTML = '';
}

function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    stPaginaProcessamento = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    
    if( parametrosGET ) {
        stLink = stPaginaProcessamento + parametrosGET;
    } else {
        stLink = stPaginaProcessamento;
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stLink, funcao, '<?=Sessao::getId();?>');
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
                    if(stCampo.type == 'checkbox' ){
                        if( stCampo.checked == true ){
                            stLink+= "&"+arCampos[i]+"="+trim( stCampo.value );
                        }
                    }else{
                        stLink+= "&"+arCampos[i]+"="+trim( stCampo.value );
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
                }else{
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}

function verificaTecla(tecla, obj)
{
    var valor = new String(obj.value);
    var tam = obj.value.length;
    var i = 0;
    var stringNova = new String();
    
    if (tecla.keyCode == 57 || tecla.keyCode == 105) {
        return true;
    } else {
        for(i =0;i<tam;i++) {
            if(valor.charAt(i) == 9) {
                stringNova += valor.charAt(i);
            }
        }
        obj.value = stringNova;
    }

}
</script>
