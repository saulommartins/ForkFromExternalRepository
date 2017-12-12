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
    * Data de Criação   : 07/10/2005


    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-23 18:09:30 -0300 (Qui, 23 Ago 2007) $
    
    * Casos de uso: uc-02.04.06,uc-02.04.25

*/

/*
$Log$
Revision 1.7  2007/08/23 21:09:30  cako
Bug#9856#

Revision 1.6  2007/04/13 20:51:02  vitor
9106

Revision 1.5  2006/10/23 18:34:58  domluc
Add Caso de Uso Boletim

Revision 1.4  2006/07/05 20:39:03  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.Ok.disabled = true;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function mostraSpan(stTipoFechamento){
    if(stTipoFechamento=="I"){
        document.getElementById('spnDataMovimentacao').innerHTML = '';
        document.frm.stCtrl.value = 'mostraSpan';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }else{
        document.getElementById('spnTerminais').innerHTML = '';
        document.frm.stCtrl.value = 'mostraData';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }
}
function mostraDataLabel(cgm){
    if(cgm>0){
        document.frm.stCtrl.value = 'mostraDataLabel';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }else{
        if(document.frm.stFecharTerminal.value=="I"){
            document.frm.stCtrl.value = 'mostraDataLabel';
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
            document.frm.submit();
            document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        }
    }
}

function limparTerminal(cgm) {
    if(cgm > 0){
        document.frm.inCodTerminal.value = '';
        document.getElementById( 'spnDataMovimentacao' ).innerHTML = "";
    }else{
        document.getElementById( 'spnTerminais' ).innerHTML = "&nbsp;"
        document.getElementById( 'spnDataMovimentacao' ).innerHTML = "&nbsp;";
        document.frm.stFecharTerminal.value = "T";
        mostraSpan('T');
    }
}

function selecionarTodos(){
    if(document.frm.boTodos.checked==true){
        for (i=0;i<document.frm.elements.length;i++) {
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 8 ) == "boFechar" ) {
                if(document.frm.elements[i].checked == false){
                    document.frm.elements[i].checked=1;
                }
            }
        }
    }else{
        for (i=0;i<document.frm.elements.length;i++)
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 8 ) == "boFechar")
                if(document.frm.elements[i].checked == true){
                    document.frm.elements[i].checked=0;
                }
    }
}

</script>
                
