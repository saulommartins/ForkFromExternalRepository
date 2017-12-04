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
/*
    * Arquivo com funcoes JavaScript para Consulta de Divida Ativa
    * Data de Criação: 23/02/2007


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: JSEmitirCarnes.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.04.04
*/

/*
$Log$
Revision 1.2  2007/07/02 20:44:52  cercato
adicionando validacao para exigir que pelo menos um filtro seja selecionado na emissao.

Revision 1.1  2007/04/16 18:11:29  cercato
adicionando funcoes para emitir carne pela divida.

*/

?>

<script type="text/javascript">

function submeteFiltro(){
    if ( Valida() ) {
        if ( document.frm.inNum.value || document.frm.inCGM.value ||document.frm.inCodImovel.value || document.frm.inInscricaoEconomica.value || document.frm.inNrParcelamento.value || document.frm.inCodInscricao.value ) {
            Salvar();
        }else {
            alertaAviso("@Selecionar ao menos um filtro para esta consulta.",'form','erro','<?=Sessao::getId();?>', '../');
        }
    }
}

function Cancelar () {
<?php
    $stLink = Sessao::read('stLink');
?>

    mudaTelaPrincipal("<?=$pgFilt.'?'.Sessao::getId().$stLink;?>");
}


function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
}

function excluiDado( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = inIndice1;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

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

function EnviaFormulario(){

	var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";
	var campoTipoEmissao;


    if ( Valida() ) {
	    if ( document.frm.emissao_carnes[0].checked ){
		    campoTipoEmissao = 'Local';
	    } else {
		    campoTipoEmissao = 'Grafica';

            var elemento = document.getElementById('inCodOrdemSelecionados');
            var size = elemento.length;
            var i = 0; var stValores="";
            while( i < size){ stValores = stValores+elemento.options[i].value+","; i++;  }
            document.frm.stOrdemEmissao.value = stValores;

            var elemento = document.getElementById('inCodOrdemLoteSelecionados');
            var size = elemento.length;
            var i = 0; var stValores="";
            while( i < size){ stValores = stValores+elemento.options[i].value+","; i++;  }
            document.frm.stOrdemLote.value = stValores;

            var elemento = document.getElementById('inCodOrdemImovelSelecionados');
            var size = elemento.length;
            var i = 0; var stValores="";
            while( i < size){ stValores = stValores+elemento.options[i].value+","; i++;  }
            document.frm.stOrdemImovel.value = stValores;

            var elemento = document.getElementById('inCodOrdemEdificacaoSelecionados');
            var size = elemento.length;
            var i = 0; var stValores="";
            while( i < size){ stValores = stValores+elemento.options[i].value+","; i++;  }
            document.frm.stOrdemEdificacao.value = stValores;
        }

        document.frm.submit();
    }
}

function buscaContribuinteIndividual(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'buscaContribuinteIndividual';
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

function atualizaEmissao(){
    var stEmissao = document.getElementById('emissao_geral').value; 
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&emissao_geral='+stEmissao;
    ajax(stPag,'montaFiltrosNovo','spnEmissaoCarne');
}

/* selecionar todos*/
function selecionarTodos(aba){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;
    if (campoT == true){
        while(cont < document.frm.elements.length){
            var namee = document.frm.elements[cont].name;
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match(aba+'boReemitir')) ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
    else{
        while(cont < document.frm.elements.length){
            var namee = document.frm.elements[cont].name;
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match(aba+'boReemitir')) ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
}

function ConsolidarDividas( boHabilita ){

    if ( boHabilita == false ){
        document.frm.dtNovoVencimentoN.disabled = true;
        document.frm.dtNovoVencimentoV.disabled = true;
        document.frm.boConsolidarN.checked = false;
        document.frm.boConsolidarV.checked = false;
    } else {
        document.frm.dtNovoVencimentoN.disabled = false;
        document.frm.dtNovoVencimentoV.disabled = false;
        //document.frm.boConsolidar.checked = false;
        document.frm.boConsolidarN.checked = true;
        document.frm.boConsolidarV.checked = true;
    }
    
}

function AtualizaDatas ( NovaData ){
        document.frm.dtNovoVencimentoN.value = NovaData;
        document.frm.dtNovoVencimentoV.value = NovaData;
}
</script>
