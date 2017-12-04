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
    * Javascript para cadastro de compensações de horas
    * Data de Criação   : 06/10/2008


    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza
    
    * @package URBEM
    * @subpackage 

    * @ignore 

    $Id:$
*/

?>
<script type="text/javascript">
function selecionarTodos(){
    if(jQuery('#boTodos').attr('checked')){
        jQuery(":checkbox").attr("checked", "checked");        
    }else{
        jQuery(":checkbox").attr("checked", "");        
    }
}

function processarPopUp(stTipoFiltro,inCodigo,stDescricao,dtFalta,dtCompensacao){
    var width  = 800;
    var height = 550;
    var sFiltros     = "&stTipoFiltro="+stTipoFiltro+"&inCodigo="+inCodigo+"&stDescricao="+stDescricao+"&dtFalta="+dtFalta+"&dtCompensacao="+dtCompensacao;
    var sSessao      = "<?=Sessao::getId()?>";
    var sUrlFrames   = "<?=CAM_GRH_PON_POPUPS;?>compensacoes/FMConsultarMatriculas.php?"+sSessao+sFiltros;
    window.open( sUrlFrames, "popUpConsultaMatriculas", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
}

</script>
