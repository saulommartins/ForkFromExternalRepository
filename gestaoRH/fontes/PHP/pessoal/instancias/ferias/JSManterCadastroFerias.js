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
    * Página de Javascript do Férias
    * Data de Criação: 07/06/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.04.22
*/

/*
$Log$
Revision 1.4  2006/08/08 17:46:49  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">
//Para chamar uma function de um oculto por ajax
function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    if( parametrosGET ) {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+parametrosGET;
    } else {
        stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stPag, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stPag, funcao );
        //ajax(stPag,funcao,'spnFiltro');
    }
}

//Para chamar uma function de um oculto por ajax
//passando todos os campos que possuam ID por GET
function montaParametrosGET( funcao, sincrono ) {
    var stLink = '';
    var f = document.frm;

    for( i=0 ; i<f.elements.length ; i++) {
        if ( f.elements[i].id ) {
            stLink += "&"+f.elements[i].id+"="+f.elements[i].value;
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}

function processarForm(stForm){
    if( stForm == 'Filtro' ){
        executaFuncaoAjax("processarFiltro");
    }
    if( stForm == 'Form' ){
        executaFuncaoAjax("processarForm","&inCodContrato=<?=$_REQUEST['inCodContrato']?>");
    }
    if( stForm == 'Consulta' ){
        executaFuncaoAjax("processarConsulta","&inCodContrato=<?=$_REQUEST['inCodContrato']?>&inCodFerias=<?=$_REQUEST['inCodFerias']?>");
    }
}

function abrePopUpRegistrosEventosFerias(){
    var width  = 800;
    var height = 550;
    if( document.frm.inContrato.value == "" ){
        inContrato = 0;
    }else{
        inContrato = document.frm.inContrato.value;
    }
    if( inContrato != 0 ){
        var sFiltros     = "&inContrato="+inContrato+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
        var sUrlConsulta = "FMConsultarRegistroEventoFerias.php?";
        var sSessao      = "<?=Sessao::getId()?>";
        var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>ferias/FRConsultarRegistroEventoFerias.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
        if( Valida() ){
            window.open( sUrlFrames, "popUpRegistrosEventosFerias", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
        }
    }
}

function abrePopUpLote(){
    abrePopUp('<?=CAM_GRH_PES_POPUPS;?>ferias/FMConsultaFiltroLote.php','frm','','','','<?=Sessao::getId()?>','800','550');
}

function abrePopUpLote2(){
    if(document.frm.inCodLote.value != ''){
        abrePopUpLote();
    }
}

function abrePopUpConsulta(inCodFerias,inCodContrato,inRegistro,inNumCGM,stNomCGM,inCodEstrutural,stDescLotacao,stDescFuncao,stDescRegime,inCodRegime,dtInicial,dtFinal,boFeriasCadastradas){
    stValores  = "stAcao=consultar";
    stValores += "&inCodFerias="+inCodFerias;
    stValores += "&inCodContrato="+inCodContrato;
    stValores += "&inRegistro="+inRegistro;
    stValores += "&inNumCGM="+inNumCGM;
    stValores += "&stNomCGM="+stNomCGM;
    stValores += "&inCodEstrutural="+inCodEstrutural;
    stValores += "&stDescLotacao="+stDescLotacao;
    stValores += "&stDescFuncao="+stDescFuncao;
    stValores += "&stDescRegime="+stDescRegime;
    stValores += "&inCodRegime="+inCodRegime;
    stValores += "&dtInicial="+dtInicial;
    stValores += "&dtFinal="+dtFinal;
    stValores += "&boFeriasCadastradas="+boFeriasCadastradas;
    abrePopUp('<?=CAM_GRH_PES_INSTANCIAS;?>ferias/FMManterCadastroFerias.php?'+stValores,'frm','','','','<?=Sessao::getId()?>','1000','750');
}

function selecionarTodos(){
    if(jQuery('#boTodos').attr('checked')){
        jQuery(":checkbox").attr("checked", "checked");
    }else{
        jQuery(":checkbox").attr("checked", "");
    }
}

function isValidDate(dateStr, format) {
   if (format == null) { format = "YMD"; }
   format = format.toUpperCase();
   if (format.length != 3) { format = "YMD"; }
   if ( (format.indexOf("M") == -1) || (format.indexOf("D") == -1) ||  (format.indexOf("Y") == -1) ) { format = "MDY"; }
   if (format.substring(0, 1) == "Y") { // If the year is first
      var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
      var reg2 = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
   } else if (format.substring(1, 2) == "Y") { // If the year is second
      var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
      var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/
   } else { // The year must be third
      var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
      var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
   }
   // If it doesn't conform to the right format (with either a 2 digit year or 4 digit year), fail
   if ( (reg1.test(dateStr) == false) && (reg2.test(dateStr) == false) ) { return false; }
   var parts = dateStr.split(RegExp.$1); // Split into 3 parts based on what the divider was
   // Check to see if the 3 parts end up making a valid date
   if (format.substring(0, 1) == "M") { var mm = parts[0]; } else       if (format.substring(1, 2) == "M") { var mm = parts[1]; } else { var mm = parts[2]; }
   if (format.substring(0, 1) == "D") { var dd = parts[0]; } else       if (format.substring(1, 2) == "D") { var dd = parts[1]; } else { var dd = parts[2]; }
   if (format.substring(0, 1) == "Y") { var yy = parts[0]; } else       if (format.substring(1, 2) == "Y") { var yy = parts[1]; } else { var yy = parts[2]; }
   if (parseFloat(yy) <= 50) { yy = (parseFloat(yy) + 2000).toString(); }
   if (parseFloat(yy) <= 99) { yy = (parseFloat(yy) + 1900).toString(); }
   var dt = new Date(parseFloat(yy), parseFloat(mm)-1, parseFloat(dd), 1, 0, 0, 0);
   if (parseFloat(dd) != dt.getDate()) { return false; }
   if (parseFloat(mm)-1 != dt.getMonth()) { return false; }
   return true;
}
</script>

