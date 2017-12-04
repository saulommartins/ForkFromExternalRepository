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
    * Arquivo JavaScript - Manter Ordem de Pagamento
    * Data de Criação   : 17/12/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2008-01-10 11:51:43 -0200 (Qui, 10 Jan 2008) $

    * Casos de uso: uc-02.03.05
                    uc-02.03.20
                    uc-02.03.28
*/

/*
$Log$
Revision 1.12  2007/04/30 19:20:46  cako
implementação uc-02.03.28

Revision 1.11  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.10  2006/07/07 18:23:20  cako
Bug #6431#

Revision 1.9  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function comparaDataLiquidacao( stUltima ){
        DiaUltima = stUltima.substring(0,2);
        MesUltima = stUltima.substring(3,5);
        AnoUltima = stUltima.substr(6);

        var dataUltima = AnoUltima+""+MesUltima+""+DiaUltima;

/*        stDataLiquidacao = document.frm.stDataLiquidacao.value;
        DiaLiquidacao = stDataLiquidacao.substring(0,2);
        MesLiquidacao = stDataLiquidacao.substring(3,5);
        AnoLiquidacao = stDataLiquidacao.substr(6);

        var dataLiquidacao = AnoLiquidacao+""+MesLiquidacao+""+DiaLiquidacao;

        if ( dataUltima > dataLiquidacao) {*/
            document.frm.stDataLiquidacao.value = stUltima;
//        }

        buscaDado('buscaDtOrdem');

}

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'telaListaNotaLiquidacao';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    //document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function buscaFornecedorDiverso(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'buscaFornecedorDiverso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    //document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}



function buscaLiquidacoes(){
    document.frm.stCtrl.value = 'buscaLiquidacoes';
    var stTarget = document.frm.target;
    /*document.frm.target = "telaListaNotaLiquidacao";*/
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function recuperaValorPagar() {
    var stCampoMixCombo = document.frm.cmbLiquidacao.value;
    var valores         = stCampoMixCombo.split("||");
    if(valores==""){
        valores[2]='0.00';
    }
    document.frm.flValorPagar.value = valores[2];
}

function incluirRetencao() {
    var stTarget      = document.frm.target;
    var stAction      = document.frm.action;
    var erro          = false;
    var mensagem      = "";

    jq('#Ok').attr('disabled', 'disabled');
    //document.frm.Ok.disabled = true; // desabilita botão Ok, até carregar a lista
    inCodPlanoRetencao = jq('#inCodPlanoRetencao');
    stNomContaRetencao = jq('#stNomContaRetencao');
    nuValorRetencao    = jq('#nuValorRetencao');
    inCodCredito       = jq('#inCodCredito');
    if ( inCodPlanoRetencao.val() == "" ) {
        erro = true;
        mensagem += "@Informe uma Conta de Retenção!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        jq('#Ok').attr('disabled', ''); // habilita novamente botão Ok, em caso de erro
    } else if ( inCodCredito.val() == "" ) {
        erro = true;
        mensagem += "@Informe um Crédito da Receita.";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        jq('#Ok').attr('disabled', ''); // habilita novamente botão Ok, em caso de erro
    } else if ( nuValorRetencao.val() == "0,00" || nuValorRetencao.val() == "" ) {
        erro = true;
        mensagem += "@Valor da Retenção não pode ser zero.";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        jq('#Ok').attr('disabled', ''); // habilita novamente botão Ok, em caso de erro
    } else {
        document.frm.stCtrl.value = 'incluirRetencao';
        document.frm.target = "telaListaNotaLiquidacao";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        // O botão ok será habilitado novamente no final da função no OC
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function avisoRetencao( mensagem ){
        alertaAvisoTelaPrincipal(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        window.parent.frames['telaPrincipal'].document.frm.Ok.disabled = false;

}


function incluirItem() {
    var stTarget      = document.frm.target;
    var stAction      = document.frm.action;
    var erro          = false;
    var mensagem      = "";

    document.frm.Ok.disabled = true; // desabilita botão Ok, até carregar a lista
    stCampoEmpenho    = document.frm.inCodigoEmpenho;
    stCampoLiquidacao = document.frm.cmbLiquidacao;
    stCampoValorPagar = document.frm.flValorPagar;
    if ( stCampoEmpenho.value == "" ) {
        erro = true;
        mensagem += "@Campo Empenho inválido!";
        if ( stCampoLiquidacao.value == "") {
            mensagem += "@Campo Liquidacao inválido!";
        }
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.frm.Ok.disabled = false; // habilita novamente botão Ok, em caso de erro
    } else if ( stCampoLiquidacao.value == "") {
        erro = true;
        mensagem += "@Campo Liquidacao inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.frm.Ok.disabled = false; // habilita novamente botão Ok, em caso de erro
    } else if ( stCampoValorPagar.value == "0,00") {
        erro = true;
        mensagem += "@Valor da O.P. não pode ser zero.";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.frm.Ok.disabled = false; // habilita novamente botão Ok, em caso de erro
    } else {
        document.frm.stCtrl.value = 'incluirItem';
        document.frm.target = "telaListaNotaLiquidacao";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        // O botão ok será habilitado novamente no final da função no OC
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;

}

function recuperaItem(){
    document.frm.stCtrl.value = 'recuperaItem';
    var stTarget = document.frm.target;
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function excluirItem( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirItem';
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirItemRetencao( inId ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirItemRetencao';
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId='+inId;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}


function limparItem( ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparItem';
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function limparOrdem(){
    document.frm.stCtrl.value = 'limparOrdem';
    var stTarget = document.frm.target;
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function limparRetencoes(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparRetencoes';
    document.frm.target = "telaListaNotaLiquidacao";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function Cancelar () {
<?php
     $stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function focusAnular(){
    recuperaItem();
    document.frm.stMotivoAnulacao.focus();
}

function mudaMenu(titulo, func, acao){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&acao="+acao+"&nivel=3&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho&cod_func_pass="+func+"&stTitulo="+titulo;
    parent.frames["telaMenu"].location.replace(sPag);
}


function formataBR( valor ) {
    var retorno = valor;

    retorno = valor.replace( new RegExp( ",","gi" ) , "."  );
    retorno = retorno.replace( new RegExp('[\.]', "gi") , ","  );

    return retorno;
}

function formataUS( valor ) {
    var retorno = valor;

    retorno = valor.replace( new RegExp( "[\.]", "gi" ), ""   );
    retorno = retorno.replace( new RegExp( ",","gi" )    , "."  );

    return retorno;
}


function totalizaValor() {
    var d = document;
    var f = document.frm;
    var CampoTotal = f.flValorAnular;
    var numLinhas  = d.getElementById('hdnNumLinhas').value;
    var nuTotal = 0;
    var numCampos = f.elements.length - 1;

    for ( i=0; i<= numCampos; i++ ){
        if ( f.elements[i].id == 'nuValor' ) {
            nuValor = f.elements[i].value;
            nuValor = formataUS( nuValor );

            nuTotal += parseFloat( nuValor );
        }
    }

    CampoTotal.value = formataBR( new String(nuTotal) );
    mascaraFloat( CampoTotal, 2, "" );
    floatDecimal( CampoTotal, 2, "" );
}


function buscaReceitas()
{
    limpaSelect(document.frm.inCodCredito, 1);
    var inCodPlanoRetencao = jq('#inCodPlanoRetencao').val();
    if (inCodPlanoRetencao != '') {
        var stLink = "<?=$pgOcul.'?'.Sessao::getId();?>&stCtrl=buscaReceitas&inCodPlanoRetencao="+inCodPlanoRetencao;
        jq.getJSON(stLink, function(json){
            jq.each(json, function(i, item) {
                jq('#inCodCredito').append(new Option(item.descricao, item.codigo));
            })
        });
    }
}

</script>
