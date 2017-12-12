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
    * Arquivo JS utilizado na Reserva de Saldos
    * Data de Criação   : 04/05/2005


    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-08-08 10:20:29 -0300 (Ter, 08 Ago 2006) $
    
    * Casos de uso: uc-02.01.08  
*/

/*
$Log$
Revision 1.6  2006/08/08 13:20:29  jose.eduardo
Bug #6691#

Revision 1.5  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    //document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function validaValor() {    
    var erro       = false;
    var mensagem   = "";
    stValor  = document.frm.flValor.value;
    stValorDotacao  = document.frm.flVlSaldoDotacao.value;

    arValor = stValor.split(".");
    stValor = arValor.join("");
    stValor = stValor.replace(",",".");

    arValorDotacao = stValorDotacao.split(".");
    stValorDotacao = arValorDotacao.join("");
    stValorDotacao = stValorDotacao.replace(",",".");    

    if ( parseFloat(stValor) > parseFloat(stValorDotacao) ) {
        document.frm.flValor.focus();
        mensagem += "@Valor informado é maior que o saldo desta dotação!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        erro = true;
    }    
    if ( parseFloat(stValor) == 0.00 ){
        document.frm.flValor.focus();
        mensagem += "@Valor informado deve ser maior que 0,00!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        erro = true;
    }
    if ( trim( stValor ) == "" ){
        document.frm.flValor.focus();
        mensagem += "@Valor informado deve ser diferente de vazio!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');             
        erro = true;
    }
    return !erro;
}
function Salvar(){
    if( validaValor() ){
         document.frm.submit();
    }
}
function limparCampos() {
    var f = document.frm;
    f.inCodDespesa.value = "";
    limpaSelect(f.stCodClassificacao,0);
    f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');
    document.getElementById("stNomDespesa").innerHTML = "&nbsp;";
    limpaSelect(f.inCodOrgao,0);
    f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');
    limpaSelect(f.inCodUnidadeOrcamento,0);
    f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');
}

function limparTodos() {
    var d = document;
    limparCampos();
    d.getElementById( "stNomFornecedor" ).innerHTML = "&nbsp;";
    d.getElementById( "spnSaldoDotacao" ).innerHTML = "";
    d.getElementById( "stNomDespesa"    ).innerHTML = "";
}
</script>
                
