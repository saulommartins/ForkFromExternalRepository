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

    $Revision: 12234 $
    $Name$
    $Autor:$
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.06

*/

/*
$Log$
Revision 1.11  2006/07/06 14:00:25  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:09:52  diego


*/
?>

<script type="text/javascript">

function executaFuncaoAjax2( funcao, parametrosGET, sincrono ) {
    if( parametrosGET ) {
        stPag = '<?=$pgOcul2;?>?<?=Sessao::getId();?>'+parametrosGET;
    } else {
        stPag = '<?=$pgOcul2;?>?<?=Sessao::getId();?>';
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stPag, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stPag, funcao );
    }

}
function montaParametrosGET2( funcao, sincrono ) {
    var stLink = '';
    var f = document.frm;

    for( i=0 ; i<f.elements.length ; i++) {
        if ( f.elements[i].name) {
            stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
        }
    }

    executaFuncaoAjax2( funcao, stLink, sincrono );
}


this.addEventListener("load", function() { montaParametrosGET2('montaClassificacao') }, "undefined");


function goOculto(stControle)
{
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function goOcultoFiltro(stControle)
{
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = 'telaPrincipal';
}


function preencheProxCombo( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxCombo';
    var target = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = target;
}

function preencheCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var target = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = target;
}

</script>
