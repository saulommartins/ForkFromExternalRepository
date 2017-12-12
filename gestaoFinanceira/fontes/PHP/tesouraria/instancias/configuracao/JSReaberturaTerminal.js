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
    * Data de Criação   : 27/10/2005


    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.04.06

*/

/*
$Log$
Revision 1.4  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
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
        if(document.frm.stReabrirTerminal.value=="I"){
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
        document.frm.stReabrirTerminal.value = "T";
        mostraSpan('T');
    }
}

</script>
                
