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
    * Arquivo JavaScript
    * Data de Criação   : 22/08/2014
    

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: JSManterConciliacaoCC.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso:
*/

?>
<script type="text/javascript">

function buscaDado( BuscaDado )
{
    var stTarget = parent.frames['telaPrincipal'].document.frm.target;
    var stAction = parent.frames['telaPrincipal'].document.frm.action; 
    var stCtrl   = parent.frames['telaPrincipal'].document.frm.stCtrl.value; 
    parent.frames['telaPrincipal'].document.frm.target = 'oculto';
    parent.frames['telaPrincipal'].document.frm.stCtrl.value = BuscaDado;
    parent.frames['telaPrincipal'].document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    parent.frames['telaPrincipal'].document.frm.submit();
    parent.frames['telaPrincipal'].document.frm.stCtrl.value = stCtrl;
    parent.frames['telaPrincipal'].document.frm.action = stAction;
    parent.frames['telaPrincipal'].document.frm.target = stTarget;
}

function limparMovimentacao()
{
    stData = new Date();

    var dia;
    if(stData.getDate()<10)
        dia = '0'+stData.getDate().toString();
    else
        dia = stData.getDate();

    var mes = parseInt(stData.getMonth()) + 1;
    if(mes<10)
        mes = '0'+mes.toString();

    parent.frames['telaPrincipal'].document.frm.stDtMovimentacao.value = parent.frames['telaPrincipal'].document.frm.stDtExtrato.value;
    parent.frames['telaPrincipal'].document.frm.inCodConta.value = '';
    parent.frames['telaPrincipal'].document.frm.nuValor.value = '';
    parent.frames['telaPrincipal'].document.frm.stTipoMovimento[0].checked = true;
    parent.frames['telaPrincipal'].document.frm.stDescricao.value = '';
}

function selecionarTodos()
{
    if(document.frm.boTodos.checked==true){
        for (i=0;i<document.frm.elements.length;i++) {
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 11 ) == "boConciliar" ) {
                if(document.frm.elements[i].checked == false){
                    document.frm.elements[i].checked=1;
                    ajustaSaldo(document.frm.elements[i].name+'');
                }
            }
        }
    }else{
        for (i=0;i<document.frm.elements.length;i++)
            if(document.frm.elements[i].type == "checkbox" && document.frm.elements[i].name.substring( 0, 11 ) == "boConciliar")
                if(document.frm.elements[i].checked == true){
                    document.frm.elements[i].checked=0;
                    ajustaSaldo(document.frm.elements[i].name+'');
                }
    }
}
function selecionarTodosPendentes(obj)
{
    // varre todos os elementos do formulario
    for (i=0; i < document.frm.elements.length; i++) {
        
        // separa o nome dos elementos por '_'
        var elemento = document.frm.elements[i].name.split('_');
        // separa o nome do objeto que fez a chamada javascript da função
        // complemento[1] vai receber a qual divisao ele faz parte, na listagem de dados da tabletree da aba principal
        var complemento = obj.name.split('_');
        
        // verifica se o tipo do elemento é uma checkbox e se a primeira parte do nome do elemento é uma checkbox de pendencia
        // e de qual subdivisao ele faz parte (complemento[1])
        // com isso quando o usuario clicar em 'selecionar todos', ele vai preencher as checkbox somente da listagem que aparece na hora
        // de expandir a linha da tabletree
        if(document.frm.elements[i].type == "checkbox" && elemento[0] == "boPendencia"+complemento[1]) {
            
            // verifica se esta marcado a check 'selecionar todos' e se as outras checks da lista estão desmarcadas, para asism poder marcá-las
            // abaixo é feita a ferificacao ao contrario para poder desmarcar todos
            if(obj.checked == true && document.frm.elements[i].checked == false) {
                document.frm.elements[i].checked = 1;
                ajustaSaldo(document.frm.elements[i].name+'');
            } else if (obj.checked == false && document.frm.elements[i].checked == true) {
                document.frm.elements[i].checked = 0;
                ajustaSaldo(document.frm.elements[i].name+'');
            }
        }
    }
}

function incluirMovimentacao()
{
    var mensagem = "";

    if(parent.frames['telaPrincipal'].document.frm.nuSaldoExtrato.value != ''){

        if(!parent.frames['telaPrincipal'].document.frm.stDtMovimentacao.value)
            mensagem += '@Campo Data inválido!()';
        if(!parent.frames['telaPrincipal'].document.frm.inCodConta.value )
            mensagem += '@Campo Conta Contábil inválido!()';
        if(!parent.frames['telaPrincipal'].document.frm.nuValor.value )
            mensagem += '@Campo Valor inválido!()';
        if(!parent.frames['telaPrincipal'].document.frm.stDescricao.value)
            mensagem += '@Campo Descrição inválido!()';

        if( mensagem ) {
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        } else {
            var expRg = new RegExp( "\\.", "g" );
            valorAdicionado = parent.frames['telaPrincipal'].document.frm.nuValor.value.replace(expRg,'');
            valorAdicionado = valorAdicionado.replace(',','.');

            saldoTesouraria = parent.frames['telaPrincipal'].document.frm.nuSaldoTesouraria.value;

            valorContabilConciliado = parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value;
            valorContabilConciliado = valorContabilConciliado.replace(',','.');

            if(parent.frames['telaPrincipal'].document.frm.stTipoMovimento[0].checked == true){
                soma = parseFloat(valorContabilConciliado) - parseFloat(valorAdicionado);
                soma = Math.round(soma*100)/100;
                parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = soma;
            }else{
                soma = parseFloat(valorContabilConciliado) + parseFloat(valorAdicionado);
                soma = Math.round(soma*100)/100;
                parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = soma;
            }

            parent.frames['telaPrincipal'].document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
            parent.frames['telaPrincipal'].document.frm.stCtrl.value = 'incluirMovimentacao';
            parent.frames['telaPrincipal'].document.frm.submit();
            parent.frames['telaPrincipal'].document.frm.stCtrl.value = 'incluir';
            parent.frames['telaPrincipal'].document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        }
    }else{
        mensagem += '@Você deve informar o Saldo do Extrato!()';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
    limparMovimentacao();
    calculaSaldo();
}

function calculaSaldo()
{
    expRg = new RegExp( "\\.", "g" );
    
    saldoConciliado = (jq('#nuSaldoContabilConciliado').val());
    saldoExtrato    = (jq('#nuSaldoExtrato').val()).replace(expRg,'');
    saldoTesouraria = jq('#nuSaldoTesouraria').val();

    saldoConciliado = saldoConciliado.replace(',','.');
    saldoExtrato    = saldoExtrato.replace(',','.');

    var nuSaldoConciliado = parseFloat(saldoTesouraria) - parseFloat(saldoConciliado);
    nuSaldoConciliado = nuSaldoConciliado.toFixed( 2 );
    var arSaldoConciliado = (''+nuSaldoConciliado).split('.');

    arSaldoConciliado[0] = montaMilharMoeda(arSaldoConciliado[0]);
    var decimais;
    if( arSaldoConciliado[1] ) decimais = arSaldoConciliado[1];
    else decimais = '00';

    parent.frames['telaPrincipal'].document.getElementById('nuLblSaldoConciliado').innerHTML = arSaldoConciliado[0]+","+decimais;

}

function ajustaSaldo(valor)
{  
    var mensagem = "";

    var diferencaConciliar;
    var saldoConciliadoFinal;
    
    expRg = new RegExp( "\\.", "g" );
    saldoContabilConciliado = parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value;
    saldoContabilConciliado = saldoContabilConciliado.replace(',','.');

    if ($(valor).name.indexOf('boPendencia') > -1 ) {
        var valor3 = valor;
    } else {
        var valor2 = valor.split('_');
        var valor3 = 'H'+valor2[0]+'_'+valor2[1];
    }
    
    valorAdicionado = jq('#'+valor3).html().replace(expRg,'');
    valorAdicionado = valorAdicionado.replace(',','.');
    
    if( ( $(valor).name.indexOf('boManual') > -1 ) || ( $(valor).name.indexOf('boPendencia') > -1 )) valorAdicionado = valorAdicionado*(-1);
    
    if($(valor).checked == true){
       saldoConciliadoFinal = parseFloat(saldoContabilConciliado) - parseFloat(valorAdicionado);
    }else{
       saldoConciliadoFinal = parseFloat(saldoContabilConciliado) + parseFloat(valorAdicionado);
    }

    saldoConciliadoFinal = Math.round(saldoConciliadoFinal*100)/100;
    parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = saldoConciliadoFinal;

    if(parent.frames['telaPrincipal'].document.frm.nuSaldoExtrato.value != ''){
        calculaSaldo();
    }
    
}

function ajustaSaldoPendente(valor)
{  
    var mensagem = "";

    var diferencaConciliar;
    var saldoConciliadoFinal;
    
    expRg = new RegExp( "\\.", "g" );
    saldoContabilConciliado = parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value;
    saldoContabilConciliado = saldoContabilConciliado.replace(',','.');

    if ($(valor).name.indexOf('boPendencia') > -1 ) {
        var valor3 = valor;
    } else {
        var valor2 = valor.split('_');
        var valor3 = valor2[0]+'_'+valor2[1];
    }

    valorAdicionado = jq('#'+'H'+valor3).html().replace(expRg,'');
    valorAdicionado = valorAdicionado.replace(',','.');

    if( ( $(valor).name.indexOf('boManual') > -1 ) || ( $(valor).name.indexOf('boPendencia') > -1 )) valorAdicionado = valorAdicionado*(-1);
    
    if($(valor).checked == true){
       saldoConciliadoFinal = parseFloat(saldoContabilConciliado) - parseFloat(valorAdicionado);
    }else{
       saldoConciliadoFinal = parseFloat(saldoContabilConciliado) + parseFloat(valorAdicionado);
    }

    saldoConciliadoFinal = Math.round(saldoConciliadoFinal*100)/100;    
    parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = saldoConciliadoFinal;

    if(parent.frames['telaPrincipal'].document.frm.nuSaldoExtrato.value != ''){
        calculaSaldo();
    }
}

function ajustaSaldoTodos(conciliar)
{  
    var mensagem = "";

    var diferencaConciliar;
    var saldoConciliadoFinal;
    
    expRg = new RegExp( "\\.", "g" );
    saldoContabilConciliado = parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value;
    saldoContabilConciliado = saldoContabilConciliado.replace(',','.');
    saldoContabilConciliado = parseFloat(saldoContabilConciliado);


    valorAdicionado = parseFloat(0);
    jq('#spnMovimentacao input:checkbox:not(#boTodos)').each(function(){
                                                   var arId = (this.id).split('_');
                                                   valor = jq('#H' + arId[0] + '_' + arId[1]).html();
                                                   valor = valor.replace('.','');
                                                   valor = valor.replace(',','.');
                                                   valorAdicionado = valorAdicionado + parseFloat(valor);
                                               });

    saldoConciliadoFinal = parseFloat(0);
    if(valor){
        saldoConciliadoFinal = parseFloat(saldoContabilConciliado) - parseFloat(valorAdicionado);
    } else {
        saldoConciliadoFinal = parseFloat(saldoContabilConciliado) + parseFloat(valorAdicionado);
    }

    saldoConciliadoFinal = Math.round(saldoConciliadoFinal*100)/100;
     
    parent.frames['telaPrincipal'].document.frm.nuSaldoContabilConciliado.value = saldoConciliadoFinal;

    if(parent.frames['telaPrincipal'].document.frm.nuSaldoExtrato.value != ''){
        calculaSaldo();
    }

}

function Conciliar(ent)
{
    BloqueiaFrames(true,false);
    window.parent.frames["telaPrincipal"].document.location.href=ent;
}
</script>           
