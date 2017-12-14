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
    * Pagina JS da Ata
    * Data de Criação: 23/01/2009
    * 
    *
    * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
    *
    * @ignore
    
    $Id:$
    
    */
?>
<script type="text/javascript">

function listaForm(inIdAta, inNumAta, stExercicioAta, inNumEdital, stExercicioEdital, stAcao){
    parent.frames['telaPrincipal'].location='FMManterAta.php?<?=Sessao::getId();?>&inIdAta='+inIdAta+'&inNumAta='+inNumAta+'&stExercicioAta='+stExercicioAta+'&inNumEdital='+inNumEdital+'&stExercicioEdital='+stExercicioEdital+'&stAcao='+stAcao;
}

function Limpar()
{
    jQuery('#stDescricaoAta').html(' ');
    jQuery('#stNumEdital').attr('Value',' ');
    jQuery('#stHoraAta').attr('Value',' ');
    jQuery('#dtDataAta').attr('Value',' ');
    jQuery('#inNumAta').attr('Value',' ');
}

function abrePopUpSugestao(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height){
    camponum+= '&dtDataAta='+document.frm.dtDataAta.value;
    camponum+= '&stHoraAta='+document.frm.stHoraAta.value;
    camponum+= '&stNumEdital='+document.frm.stNumEdital.value;
    if (width == '') {
        width = 800;
    }
    if (height == '') {
        height = 550;
    }
    var x = 0;
    var y = 0;
    var sessaoid = sessao.substr(15,6);
    var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca;
    var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

</script>