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
    * Página de funções javascript para o cadastro de Borderô
    * Data de Criação   :06/01/2006


    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto
    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/
?>
<script type="text/javascript">

function mostraPreEmissao(){

    var erro = false;
    var mensagem = "";

    if(trim(document.getElementById('spnLista').innerHTML) == ''){
        erro = true;
        mensagem += '@Adicione uma Ordem de Pagamento!()';
    }
    if( erro ){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        var stTraget = document.frm.target;
        var stAction = document.frm.action;
        var inSimulacao = document.frm.inSimulacao;      
        if(document.frm.inCodEntidade.disabled){
            document.frm.inCodEntidade.disabled = false;
        }
        document.frm.inSimulacao.value = "1";
        document.frm.target = "oculto";
        document.frm.action = document.frm.stAction.value + '?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTraget;
        document.frm.action = stAction;
        document.frm.inSimulacao = inSimulacao;
        if(!document.frm.inCodEntidade.disabled){
            document.frm.inCodEntidade.disabled = true;
        }
    }
}

function mostraSpanBoletim(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'mostraSpanBoletim';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function buscaDado( BuscaDado ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function mostraSpanOrdem(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'mostraSpanOrdem';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheAgenciaCredor(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheAgenciaCredor';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheCamposCodigosCredor(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheCamposCodigosCredor';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function validaForm(){
    

    var erro = false;

    var mensagem = "";

    if(trim(document.getElementById('spnOrdem').innerHTML) == ''){
        erro = true;
        mensagem += '@Informe uma Ordem de Pagamento!()';
    }
    if( trim(document.frm.inCodEntidade.value) == "" ){
        erro = true;
        mensagem += "@Campo Entidade inválido!()";
    }
    if( trim(document.frm.stExercicio.value) == "" ){
        erro = true;
        mensagem += "@Campo Exercício inválido!()";
    }
    if( trim(document.frm.inCodConta.value) == "" ){
        erro = true;
        mensagem += "@Campo Conta inválido!()";
    }
    if( trim(document.frm.inNumOrdemPagamento.value) == "" ){
        erro = true;
        mensagem += "@Campo Nr. Ordem de Pagamento inválido!()";
    }
    if( trim(document.frm.inNumBancoCredor.value) == "" ){
        erro = true;
        mensagem += "@Campo Banco inválido!()";
    }
    if( trim(document.frm.inNumAgenciaCredor.value) == "" ){
        erro = true;
        mensagem += "@Campo Agência inválido!()";
    }
    if( trim(document.frm.stNumeroContaCredor.value) == "" ){
        erro = true;
        mensagem += "@Campo Conta Corrente inválido!()";
    }
    if( trim(document.frm.stObservacao.value) == "" ){
        erro = true;
        mensagem += "@Campo Observação inválido!()";
    }
    if( erro ){ 
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }

 return !erro;
 }
 function inclui(){
     if( validaForm() ){
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiOrdemPagamento';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limpa();
     }
 }

function limpa(){
    document.frm.stTipoTransacao.value = "2";
    document.frm.inNumOrdemPagamento.value = '';
    document.getElementById('stNumOrdemPagamento').innerHTML = '&nbsp;';
    document.getElementById('spnOrdem').innerHTML = "";
}

function excluirOrdemPagamento( inNumItem ){
    document.frm.stCtrl.value = 'excluirOrdemPagamento';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumItem=' + inNumItem;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaForm() {
    document.frm.inCodEntidade.value   = "";
    document.getElementById('spnBoletim').innerHTML = "";
    document.frm.inCodConta.value   = "";
    document.getElementById('stConta').innerHTML = "";
    limpa();

}
</script>
