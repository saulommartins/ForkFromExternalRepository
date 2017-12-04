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
<?
/**
    * Arquivo com funcoes JavaScript para Relatorio de Divida
    * Data de Criação: 18/04/2007


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: JSRelatorioDivida.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.04.10
*/

/*
$Log$
Revision 1.1  2007/04/19 16:06:56  cercato
*** empty log message ***

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
}

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function Limpar(){
    buscaDado("limpaGeral");
    document.frm.reset();
}

function executaFuncaoAjax( funcao, parametrosGET, sincrono ) {
    stPaginaProcessamento = '<?=$pgAjax ? $pgAjax : $pgOcul;?>?<?=Sessao::getId();?>';
    if( parametrosGET ) {
        stLink = stPaginaProcessamento + parametrosGET;
    } else {
        stLink = stPaginaProcessamento;
    }
    if( sincrono ) {
        ajaxJavaScriptSincrono( stLink, funcao, '<?=Sessao::getId();?>' );
    } else {
        ajaxJavaScript( stLink, funcao );
    }
}

function montaParametrosGET( funcao, campos, sincrono  ) {
    var stLink = '';
    var f = document.frm;
    var d = document;

    if( campos ) {
        if ( campos.search(/,/) > 0 ) {
            arCampos = campos.split(",");
        } else {
            arCampos = new Array();
            arCampos[0] = campos;
        }
        for( i=0 ; i<arCampos.length ; i++ ) {
            stCampo = eval( 'document.frm.'+arCampos[i] );
            if( typeof(stCampo) == 'object' ){
                if ( stCampo[0] ){              
                     if ( stCampo[0].type == 'radio' ) {
                         for( j=0; j<stCampo.length; j++ ) {
                             if( stCampo[j].checked == true ) {
                                 stLink += "&"+arCampos[i]+"="+trim( stCampo[j].value );
                             }
                         }
                     } else {
                         stLink += "&"+arCampos[i]+"="+trim( stCampo.value );
                     }
                } else {
                    stLink+= "&"+arCampos[i]+"="+trim( stCampo.value );
                }
            }
        }
    } else {
        for( i=0 ; i<f.elements.length ; i++) {
            if( typeof(f.elements[i]) == 'object' ){               
                if( f.elements[i].type == 'radio' ){
                    if( f.elements[i].checked == true ){
                        stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                    }
                }else{
                    stLink += "&"+f.elements[i].name+"="+f.elements[i].value;
                }
            }
        }
    }
    executaFuncaoAjax( funcao, stLink, sincrono );
}

function excluiGrupoCredito(tipoBusca, inIndice1){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1=' + inIndice1;
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function limpa(controle,indice){
  
  var aux;
   if (controle == 'removeBloco'){
      if(document.frm.inBloco.value > 1) {
         aux = controle;
         document.frm.stRemove.value = indice;
         controle = 'mostraBloco';
    }
   }
    
    document.frm.stCtrl.value = controle;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';

    if(aux == 'removeBloco'){
       document.frm.stRemove.value ="" ;
       document.frm.inBloco.value = parseInt(document.frm.inBloco.value)-1;
     } 
}

function submeteFiltro(){
    var d = document.frm;
    var i;
    for (i=0;i<d.inCodSituacao.length;i++){
       if (d.inCodSituacao[i].checked)
         var codSituacao = d.inCodSituacao[i].value ;
    }
  
    if ( (d.inCGM.value || d.inCodImovelInicial.value || d.inCodImovelFinal.value || d.inNumInscricaoEconomicaInicial.value  || d.inNumInscricaoEconomicaFinal.value)  && codSituacao && document.getElementById("spnListaGrupos").innerHTML  ) {
        if ( d.inCodImovelInicial.value && d.inCodImovelFinal.value && d.inNumInscricaoEconomicaInicial.value && d.inNumInscricaoEconomicaFinal.value )
            alertaAviso("@Preencher apenas um dos dois filtros: Inscrição Imobiliária ou Inscrição Econômica.",'form','erro','<?=Sessao::getId();?>', '../');
        else
            Salvar();     
    } else {      
        alertaAviso("@Selecionar ao menos dois filtros. O filtro situação é de preencimento obrigatório, para esta consulta.",'form','erro','<?=Sessao::getId();?>', '../');
    }
}

</script>
