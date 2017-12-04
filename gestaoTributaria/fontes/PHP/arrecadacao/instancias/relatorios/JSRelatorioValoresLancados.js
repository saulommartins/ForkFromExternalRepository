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
  * Página de JavaScript para Telatório de Valores Lançados
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSRelatorioValoresLancados.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.13
*/
?>

<script type="text/javascript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

}

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;
    if (campoT == true){
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
    else{
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
}

function Cancelar () {
    document.frm.target = "";
    document.frm.action = "<?=$pgFilt.'?'.Sessao::getId();?>";
    document.frm.submit();
}

function submeteFiltro(){
    if( Valida() ){
        stTipoRelatorio = document.frm.stTipoRelatorio.value;
        if ( stTipoRelatorio == "analitico" ){

            inCodContribuinteInicial = document.frm.inCodContribuinteInicial.value;
            inCodContribuinteFinal = document.frm.inCodContribuinteFinal.value;
            inNumInscricaoImobiliariaInicial = document.frm.inNumInscricaoImobiliariaInicial.value;
            inNumInscricaoImobiliariaFinal = document.frm.inNumInscricaoImobiliariaFinal.value;
            inNumInscricaoEconomicaInicial = document.frm.inNumInscricaoEconomicaInicial.value;
            inNumInscricaoEconomicaFinal = document.frm.inNumInscricaoEconomicaFinal.value;
            inNumLogradouro                 = document.frm.inNumLogradouro.value;
            inCodCondominioInicial          = document.frm.inCodCondominioInicial.value;
            inCodCondominioFinal            = document.frm.inCodCondominioFinal.value;

            if ( !inCodContribuinteInicial && !inCodContribuinteFinal && !inNumInscricaoImobiliariaInicial && !inNumInscricaoImobiliariaFinal && !inNumInscricaoEconomicaInicial && !inNumInscricaoEconomicaFinal && !inNumLogradouro && !inCodCondominioInicial && !inCodCondominioFinal ) {
                mensagem = "Campos 'Contribuinte', 'Inscrição Imobiliária' ou 'Inscrição Econômica' não foram preenchidos!";
                alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
            }else {
                var stTarget = document.frm.target;
                var stAction = document.frm.action;

                loadingModal(true,false, 'Processando...' );
                document.frm.action = "OCGeraRelatorioValoresLancadosAnalitico.php";
                //document.frm.target = "telaPrincipal";
                document.frm.submit();
                document.frm.action = stAction;
                document.frm.target = stTarget;
            }
        } else {
            document.frm.submit();
        }
    }
}



function disableAtributos(valor){

    if( valor == 'sintetico' ){
        //document.frm.inCodContribuinte.disabled = true;
        document.frm.inCodContribuinteInicial.disabled = true;
        document.frm.inCodContribuinteFinal.disabled = true;
        document.frm.inNumInscricaoImobiliariaInicial.disabled = true;
        document.frm.inNumInscricaoImobiliariaFinal.disabled = true;
        document.frm.inNumInscricaoEconomicaInicial.disabled = true;
        document.frm.inNumInscricaoEconomicaFinal.disabled = true;
        document.frm.inCodGrupoInicio.disabled = true;
        document.frm.inCodGrupoTermino.disabled = true;
        document.frm.inCodCreditoInicio.disabled = false;
        document.frm.inCodCreditoTermino.disabled = false;        
        //document.frm.stContribuinte.disabled = true;
    } else {
        //document.frm.inCodContribuinte.disabled = false;
        document.frm.inCodContribuinteInicial.disabled = false;
        document.frm.inCodContribuinteFinal.disabled = false;
        document.frm.inNumInscricaoImobiliariaInicial.disabled = false;
        document.frm.inNumInscricaoImobiliariaFinal.disabled = false;
        document.frm.inNumInscricaoEconomicaInicial.disabled = false;
        document.frm.inNumInscricaoEconomicaFinal.disabled = false;
        document.frm.inCodGrupoInicio.disabled = false;
        document.frm.inCodGrupoTermino.disabled = false;
        document.frm.inCodCreditoInicio.disabled = true;
        document.frm.inCodCreditoTermino.disabled = true;
        // document.frm.inCodAtributosSelecionados.disabled = false;
    }
}
</script>
