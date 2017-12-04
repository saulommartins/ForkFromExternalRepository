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
    * JavaScript
    * Data de Criação: 07/02/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-13 13:46:10 -0200 (Qui, 13 Dez 2007) $

    * Casos de uso: uc-04.05.41
*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca){
     action = document.frm.action;
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = action;
}

function abrePopUpRegistrosEventos(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "LSConsultarRegistroEvento.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FRConsultarRegistroEvento.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventos", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os registros de eventos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');        
    }
}

function abrePopUpRegistrosEventosComplementar(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value+"&inCodComplementar="+document.frm.inCodComplementar.value;
        var sUrlConsulta = "LSConsultarRegistroEventoComplementar.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>folhaComplementar/FRConsultarRegistroEventoComplementar.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventosComplementar", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os registros de eventos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');        
    }
}

function abrePopUpRegistrosEventosFerias(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "FMConsultarRegistroEventoFerias.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>ferias/FRConsultarRegistroEventoFerias.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventosFerias", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os registros de eventos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');        
    }
}

function abrePopUpRegistrosEventosDecimo(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "FMConsultarRegistroEventoDecimo.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>decimo/FRConsultarRegistroEventoDecimo.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventosDecimo", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os registros de eventos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function abrePopUpRegistrosEventosRescisao(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "FMConsultarRegistroEventoRescisao.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>rescisao/FRConsultarRegistroEventoRescisao.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventosRescisao", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os registros de eventos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}


function abrePopUpAssentamentoGerado(){
    var width  = 800;
    var height = 550;
    if( document.getElementById("inContrato").value == "" ){
        inCodContrato = 0;
    }else{
        inCodContrato = document.getElementById("inContrato").value;
    }
    if( inCodContrato != 0 ){
        var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "FMConsultarAssentamentoGerado.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_PES_POPUPS;?>assentamento/FRConsultarAssentamentoGerado.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpAssentamentoGerado", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }else{
        mensagem = "Informe uma matrícula para consultar os assentamentos gerados!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

</script>
