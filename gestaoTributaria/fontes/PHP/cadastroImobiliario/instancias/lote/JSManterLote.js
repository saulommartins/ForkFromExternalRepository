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
    * Página de funções javascript para o cadastro de lote
    * Data de Criação   : 06/12/2004


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: JSManterLote.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.12  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function mudaMenu(func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_modulo_pass=12&cod_gestao_pass=5&stNomeGestao=Tributária&modulos=Cadastro Imobiliário&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);
}

function focusIncluir(){
    document.frm.inProcesso.focus();
}

function visualizarProcesso(processo, timestamp, cod_lote, ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'visualizarProcesso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&funcionalidade=<?=$_REQUEST["funcionalidade"]?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_lote='+cod_lote+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


//FUNCOES PARA PREENCHIMENTO DA LOCALIZACAO
function preencheProxCombo( inPosicao  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function preencheCombos(){
    //BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function limpaCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaDado(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaLogradouro(){
    document.frm.stCtrl.value = 'buscaLogradouro';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaBairro(){
    document.frm.stCtrl.value = 'buscaBairro';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaLogradouroFiltro(){
    document.frm.stCtrl.value = 'buscaLogradouroFiltro';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function montaConfrontacao( tipo ){
    document.frm.stCtrl.value = tipo;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscarTrecho(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'buscarTrecho';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

//FUNCAO GENERICA DE EXCLUSAO DE LINHAS DAS LISTAS
function excluirDado( stAcao, inLinha ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inLinha='+inLinha ;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

//FUCOES PARA A LISTA DE CONFRNTACOES
function validaConfrontacao(){
     var erro = false;
     var mensagem = "";

     if( !document.frm.stTipoConfrotacao[0].checked && !document.frm.stTipoConfrotacao[1].checked && !document.frm.stTipoConfrotacao[2].checked ){
         erro = true;
         mensagem += "@Campo Tipo inválido!()";
     }else{
         stCampo = document.frm.flExtensao;
         flCampo = stCampo.value.replace( "." , "", "g" );
         flCampo = flCampo.replace( ",", ".", 'g');
         flCampo = parseFloat( flCampo );
         if( trim( stCampo.value ) == "" || trim( stCampo.value) == '0,00'){
             erro = true;
             mensagem += "@Campo Extensão inválido!()";
         }else if( flCampo > 999999.99 ){
             erro = true;
             mensagem += "@Campo Extensão inválido, valor máximo 999.999,99!( "+stCampo.value+" )";
         }
         if( document.frm.stTipoConfrotacao[0].checked && (document.frm.inNumTrecho.value == "" || document.frm.stTrecho.value    == "") ){
             erro = true;
             mensagem += "@Campo Trecho inválido!()";
         }
         if( document.frm.stTipoConfrotacao[1].checked && document.frm.inCodigoLoteConfrontacao.value == "" ){
             erro = true;
             mensagem += "@Campo Lote inválido!()";
         }

           var expReg = new RegExp("\n","ig");
         if( document.frm.stTipoConfrotacao[2].checked && trim(stCampo) == "" ){
             erro = true;
             mensagem += "@Campo Descrição inválido!()";
         }else if( document.frm.stTipoConfrotacao[2].checked && document.frm.stDescricaoOutros.value.length > 500 ){
             erro = true;
             mensagem += "@Campo Descrição inválido, tamanho máximo 500 caracteres!( Total de caracteres "+document.frm.stDescricaoOutros.value.length+" )";
         }
     }
     if( erro ){
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
 return !erro;
}

function incluirConfrontacao(){

    var erro = false;
    var mensagem = "";

    if(document.frm.stTipoConfrotacao[2].checked){
        var expReg  = new RegExp("\n","ig"); 
        var stDescricaoOutros = document.frm.stDescricaoOutros.value.replace(expReg, ''); 
    }

    if( document.frm.stTipoConfrotacao[2].checked && trim(stDescricaoOutros) == ''){
        erro = true;
        mensagem += "@Campo Descrição inválido!()";
    }

    if( document.frm.stAcaoConfrontacao.value == 'alterar' ){           
        stCampo = document.frm.flExtensao;
        flCampo = stCampo.value.replace( "." , "", "g" );
        flCampo = flCampo.replace( ",", ".", 'g');
        flCampo = parseFloat( flCampo );

        if( trim( stCampo.value ) == "" || trim( stCampo.value) == '0,00'){
            erro = true;
            mensagem += "@Campo Extensão inválido!()";
        }else if( flCampo > 999999.99 ){
            erro = true;
            mensagem += "@Campo Extensão inválido, valor máximo 999.999,99!( "+stCampo.value+" )";
        }
        if( erro ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else {
            document.frm.stCtrl.value = 'alterarConfrontacaoLista';
            var stTraget = document.frm.target;
            document.frm.target = "oculto";
            var stAction = document.frm.action;
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
            document.frm.submit();
            document.frm.action = stAction;
            document.frm.target = stTraget;
        }
    } else {
        boValida = true;
        if( erro ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else {
            if(!document.frm.stTipoConfrotacao[2].checked){    
                boValida = validaConfrontacao();
            }
            if(boValida){
                document.frm.stCtrl.value = 'incluirConfrontacao';
                var stTraget = document.frm.target;
                document.frm.target = "oculto";
                var stAction = document.frm.action;
                document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
                document.frm.submit();
                document.frm.action = stAction;
                document.frm.target = stTraget;
            }
        }
    }
}

function limparConfrontacao(){
    if( document.frm.stAcaoConfrontacao.value == 'alterar' ){
        document.frm.stTipoConfrotacao[0].disabled = false;
        document.frm.stTipoConfrotacao[1].disabled = false;
        document.frm.stTipoConfrotacao[2].disabled = false;
    }
    document.getElementById("spnConfrontacao").innerHTML = "";
    document.frm.inCodigoPontoCardeal.options[0].selected = true;
    document.frm.stTipoConfrotacao[0].checked = false;
    document.frm.stTipoConfrotacao[1].checked = false;
    document.frm.stTipoConfrotacao[2].checked = false;
    document.frm.flExtensao.value = '';
}

function limparFiltro(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparFiltro';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparFormulario(){
    buscaValor('limparFormulario');
    limparConfrontacao();
}

function submeteFiltro(){
    stLocalizacao = document.frm.stChaveLocalizacao.value;
    stLote        = document.frm.stNumeroLote.value;
    if ( stLote == "" && stLocalizacao == "" ){
        mensagem = "Campos Número do Lote ou Localização não foram preenchidos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        Salvar();
    }
}

function atualizaComponente(){
    HabilitaLayer("");
}

function montaConfrontacaoAlterar(linha,tipo, extensao, testada, stChaveTrecho, stTrecho){
    document.frm.inIndice.value = linha;
    document.frm.flExtensao.value = extensao;
    
    document.getElementById("spnConfrontacao").innerHTML = "";
    if( tipo == 'Trecho' ){
        document.frm.stTipoConfrotacao[0].checked = true;
        tipo = 'trecho';
    }
    if( tipo == 'Lote' ){
        document.frm.stTipoConfrotacao[1].checked = true;
        tipo = 'lote';
    }
    if( tipo == 'Outros' ){
        document.frm.stTipoConfrotacao[2].checked = true;
        tipo = 'outros';
    }

    document.frm.stCtrl.value = tipo;

    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&testada='+testada+'&stChaveTrecho='+stChaveTrecho+'&stTrecho='+stTrecho+'&Acao=alterarConfrontacao';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
