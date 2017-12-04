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
    * Página de Javascript
    * Data de Criação: 25/06/2008

    
    * @author Analista: Dagiane	Vieira	
    * @author Desenvolvedor: <Alex Cardoso>
    
    * @ignore
    
    $Id: JSExportarIpers.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
    
    * Casos de uso: uc-04.08.28     
*/

?>
<script type="text/javascript">

function download(){
    stAction = document.frm.action;
    document.frm.action = '<?=$pgDown?>';
    document.frm.submit();
    document.frm.action = stAction;
}

function juntarCalculo(valor){
    var boDisabled = false;
    if(valor=='sim'){
        boDisabled = true;
        document.frm.stConfiguracao.value='1';
        document.frm.inCodConfiguracao.value='1';
        ajaxJavaScriptSincrono('<?=CAM_GRH_PES_PROCESSAMENTO;?>OCIFiltroTipoFolha.php?<?=Sessao::getId()?>&inCodConfiguracao='+document.frm.inCodConfiguracao.value+'&inCodMes='+document.frm.inCodMes.value+'&inAno='+document.frm.inAno.value+'&boDesdobramento=false','gerarSpanTipoFolha' );
    }
    document.frm.stConfiguracao.disabled = boDisabled;
    document.frm.inCodConfiguracao.disabled = boDisabled;
}

</script>