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
/** Página de JS da Emissao

    * Data de Criação   : 27/09/2006


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato 
    * @ignore

    * $Id: JSManterEmissao.js 63390 2015-08-24 19:17:05Z arthur $

    * Casos de uso: uc-05.04.03
*/
?>

<script type="text/javascript">

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;

    while(cont < document.frm.elements.length){
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
            document.frm.elements[cont].checked = campoT;
        }

        cont++;
    }
}

function buscaValor(tipoBusca, valor){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.HdnQual.value = valor;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar(){
    document.frm.reset();
    if ( document.frm.stTipoModalidade[0] )
        document.frm.stTipoModalidade[0].focus();
}

function verificaCamposFiltro(){
    var boValida = false;
    var mensagem ='';
    var arCampos = new Array('#inCodContribuinteInicial',
                            '#inCodContribuinteFinal',
                            '#inNumInscricaoEconomicaInicial',
                            '#inNumInscricaoEconomicaFinal',
                            '#inCodImovelInicial',
                            '#inCodImovelFinal',
                            '#inCodInscricaoInicial',
                            '#inCodInscricaoFinal',
                            '#stNumDocumento');
    for(var i=0; i< arCampos.length; i++ ){
        if( jQuery(arCampos[i]).val()){
            boValida = true;
            break;
        }
    }

    if( !boValida ){
        mensagem = 'Informe ao menos um contribuinte, uma inscrição ou o número do documento para o filtro!';
    }
    if(boValida){
        Salvar();
        BloqueiaFrames(true, false);
    }else{
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
    return false;
}

</script>
