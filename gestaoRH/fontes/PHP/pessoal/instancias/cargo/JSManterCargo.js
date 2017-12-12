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
    * Página Oculta de Cargo
    * Data de Criação   : 07/12/2004


    * @author Gustavo Passos Tourinho
    * @author Vandre MIguel Ramos
    
    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Caso de uso: uc-04.04.06

*/

/*
$Log$
Revision 1.4  2006/08/08 17:46:27  vandre
Adicionada tag log.

*/

?>
<script type="text/javascript">

function focusIncluir(){
    document.frm.stDescricao.focus();
}

function focusFiltro(){
    document.frm.inCodCargo.focus();
}

function focusAlterar(){
    document.frm.stCBO.focus();
}

function buscaValor(tipoBusca){
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function Cancelar(){
<?php
$arLink = Sessao::read('link');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function incluirValor(tipoInclusao){
    var stCampo   = "";
    var stCampo1  = document.frm.stCBOEspecialidade.value;
    var CBO       = document.frm.stCBOEspecialidade.value;
    var CmbPadrao = document.frm.inCodPadraoEspecialidade.value;
    var TxtPadrao = document.frm.inCodPadraoTxtEspecialidade.value;
    var CmbNorma  = document.frm.inCodNormaEspecialidade.value;
    var TxtNorma  = document.frm.inCodNormaTxtEspecialidade.value;
    if (document.getElementById('stDescricaoEspecialidade') ) {
        var stCampo   = document.frm.stDescricaoEspecialidade.value;
    }
    else {
        var stCampo   = "ok";
    }

    if ( !stCampo || (trim (stCampo) == "" )) {
        alertaAviso('@Campo descrição da especialidade do cargo inválido.','form','erro','<?=Sessao::getId();?>');
    } else if( !stCampo1 || (trim (stCampo1) == "" )) {
        alertaAviso('@Campo CBO da especialidade do cargo inválido.','form','erro','<?=Sessao::getId();?>');
    } else if( parseInt(stCampo1) == 0 ) {
        alertaAviso('@Campo CBO da especialidade do cargo inválido ('+CBO+').','form','erro','<?=Sessao::getId();?>');
    } else if( !CmbPadrao || (trim (CmbPadrao) == "" ) ) {
        alertaAviso('@Campo Padrão da especialidade do cargo inválido ('+TxtPadrao+').','form','erro','<?=Sessao::getId();?>');
    } else if( !CmbNorma || (trim (CmbNorma) == "" ) ) {
        alertaAviso('@Campo Norma da especialidade do cargo inválido ('+TxtNorma+').','form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.stCtrl.value = tipoInclusao;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }
           
}
function excluirDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function ModificaDado1(stControle, inId){
var stCampo   = "";
var stCampo1 = document.frm.stCBOEspecialidade.value;
var CBO      = document.frm.stCBOEspecialidade.value;
if (document.getElementById('stDescricaoEspecialidade') ) {
    var stCampo   = document.frm.stDescricaoEspecialidade.value;
}
else {
    var stCampo   = "ok";
}

    if ( !stCampo || (trim (stCampo) == "" )) {
         alertaAviso('@Campo descrição da especialidade do cargo inválido.','form','erro','<?=Sessao::getId();?>');
    }else if( !stCampo1 || (trim (stCampo1) == "" )){
              alertaAviso('@Campo CBO da especialidade do cargo inválido.','form','erro','<?=Sessao::getId();?>');
          }
          else if( parseInt(stCampo1) == 0 ){
              alertaAviso('@Campo CBO da especialidade do cargo inválido ('+CBO+').','form','erro','<?=Sessao::getId();?>');
          }
           else{document.frm.stCtrl.value = stControle;
                document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
                document.frm.submit();
                document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
         
           }     
           
}    

function ModificaDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
           
}

function limparEspecialidade(stControle,inId){
if (document.frm.stDescricaoEspecialidade)
    document.frm.stDescricaoEspecialidade.value  = '';
    document.frm.stCBOEspecialidade.value = '';
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencherVagas(obj){
    if(obj.value <= 0){
        obj.value = 0;
    }
}

function confirmPopUp(stTitle,stText,stMethodSim,stMethodNao)
{
        stHTMLFrames = "<div id=\"containerPopUp\"></div>";
        stHTML = "    <div id=\"showPopUp\">";
        stHTML = stHTML + '        <h4>Confirmação</h4>';
        stHTML = stHTML + '        <p>'+stText+'</p>';
        stHTML = stHTML + '        <input type="button" value="Sim" id="btPopUpSim" name="btPopUpSim" onclick="javascript:removeConfirmPopUp();'+stMethodSim+';"; />';
        stHTML = stHTML + '        <input type="button" value="Não" id="btPopUpNao" name="btPopUpNao" onclick="removeConfirmPopUp();'+stMethodNao+'" />';
        stHTML = stHTML + '    </div>';

        var containerCSS = { 'width':'100%',  
                             'height': '1999px',
                             'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                             'position':'absolute',
                             'left':'0',
                             'top':'0' };
                              
        for(i=1;i<4;i++){
            jq('html',parent.frames[i].document).append(stHTMLFrames);
            jq('html',parent.frames[i].document).css({'overflow':'hidden'});
            jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
            jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
        }

        jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
        jq('#btPopUpSim').focus();

}

</script>
