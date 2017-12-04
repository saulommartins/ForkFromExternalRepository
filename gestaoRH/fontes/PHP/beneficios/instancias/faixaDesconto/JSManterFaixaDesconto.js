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
* Página de JavaScript
* Data de Criação   : ???


* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.06
*/

/*
$Log$
Revision 1.4  2006/08/08 17:30:44  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function excluiDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function IncluiFaixa(){
    var mensagem = validaPrevidencia();
    
    if ( mensagem == '' ){
        buscaValor('MontaFaixa');
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function validaPrevidencia(){
    var mensagem = '';
    var d = document.frm;
    var valorInicial = '';
    var valorFinal = '';
    var PercentualDesconto = ''; 
       
    valorInicial = document.frm.elements['flSalarioInicial'].value;
    re = "-";
    if(valorInicial.search(re) != '-1'){
        mensagem += "@Salário inicial negativo inválido ( )";
    };


    valorInicial = valorInicial.replace(',','');
    valorInicial = valorInicial.replace('.','');
    valorInicial = parseFloat(valorInicial);
    
    valorFinal = document.frm.elements['flSalarioFinal'].value;
    if(valorFinal.search(re) != '-1'){
        mensagem += "@Salário final negativo inválido ( )";
    };
    valorFinal = valorFinal.replace(',','');
    valorFinal = valorFinal.replace('.','');
    valorFinal = parseFloat(valorFinal);
    
    PercentualDesconto = document.frm.elements['flPercentualDesc'].value;
    if(PercentualDesconto.search(re) != '-1'){
        mensagem += "@Percentual de desconto negativo inválido ( )";
    };
    PercentualDesconto = PercentualDesconto.replace(',','');
    PercentualDesconto = PercentualDesconto.replace('.','');
    PercentualDesconto = parseFloat(PercentualDesconto);
    
    
       
   if ( valorInicial  == '0' || !valorInicial) {
        mensagem += "@Salário inicial inválido ( )";
    }

    if ( valorFinal  == '0' || !valorFinal ) {
        mensagem += "@Salário final inválido ( )";
    }
    if ( valorInicial >= valorFinal ) {
        mensagem += "@Valor inicial deve ser menor que final.";
    }
    
    if ( PercentualDesconto  == '0' || !PercentualDesconto ) {
        mensagem += "@Percentual de desconto inválido ( )";
    }
    return mensagem;
}

function validaDesconto( desconto, campo, descricao ){
    var mensagem = '';
    var d = document.frm;
    var valor  = campo.value;
    
    desconto = desconto.replace('.','');
    desconto = parseInt(desconto,10);

    if( desconto > 100){
        mensagem += "@Campo " + descricao + " inválido!( " + valor + " )";
        campo.value = "";
        campo.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}


function focusIncluir(){
    document.frm.stDescricao.focus();
}

function limpaPrevidencia(){
    document.frm.flSalarioInicial.value   = '';
    document.frm.flSalarioFinal.value  = '';
    document.frm.flPercentualDesc.value  = '';
}


</script>
