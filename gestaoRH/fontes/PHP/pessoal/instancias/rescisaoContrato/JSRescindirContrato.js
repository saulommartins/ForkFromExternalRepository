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
* Página de JavaScript
* Data de Criação   : 13/10/2005


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore 

$Id: JSRescindirContrato.js 65923 2016-06-30 13:18:20Z michel $

* Casos de uso: uc-04.04.44
*/

?>
<script type="text/javascript">

function buscaValorFiltro(tipoBusca){
    var stTraget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function confirmPopUp(stTitle,stText,stMethodSim,stMethodNao)
{
        stHTMLFrames = '<div id="containerPopUp"></div>';

        stHTML = '    <div id="showPopUp">';
        stHTML = stHTML + '        <h3>'+stTitle+'</h3>';
        stHTML = stHTML + '        <h4>Confirmação</h4>';
        stHTML = stHTML + '        <p>'+stText+'</p>';
        stHTML = stHTML + '        <input type="button" value="Sim" id="btPopUpSim" name="btPopUpSim" onclick="javascript:removeConfirmPopUp();'+stMethodSim+';"; />';
        stHTML = stHTML + '        <input type="button" value="Não" id="btPopUpNao" name="btPopUpNao" onclick="removeConfirmPopUp();'+stMethodNao+'" />';
        stHTML = stHTML + '    </div>';

        var containerCSS = { 'width':'100%',
                             'height': '100%',
                             'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                             'position':'absolute',
                             'left':'0',
                             'top':'0'
                           };

        for(i=1;i<4;i++){
            jq('html',parent.frames[i].document).append(stHTMLFrames);
            jq('html',parent.frames[i].document).css({'overflow':'hidden'});
            jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
        }

        jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
        jq('#btPopUpSim').focus();

}

function buscaTipoValor( tipoBusca, stId ){
    var stTraget = document.frm.target;
    var stAction= document.frm.action;

    var stChecked = jQuery('#'+stId).prop("checked");

    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stCtrl='+tipoBusca+'&boAtivarUsuario='+stChecked+'&stId='+stId;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
