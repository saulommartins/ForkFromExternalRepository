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
    * Data de Criação   : 05/12/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Id: JSManterEmpenho.js 65311 2016-05-11 20:42:32Z michel $
    
    * Casos de uso: uc-02.03.03
                    uc-02.03.04
                    uc-02.01.08

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
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function validaVencimento() {
    var erro       = false;
    var mensagem   = "";
    stDataVencimento  = document.frm.stDtVencimento.value;
    DiaVencimento = stDataVencimento.substring(0,2);
    MesVencimento = stDataVencimento.substring(3,5);
    AnoVencimento = stDataVencimento.substr(6);

    VencMaximo = '<?=Sessao::getExercicio();?>'+'1231';
    
    var dataVencimento = "";
    dataVencimento += AnoVencimento+MesVencimento+DiaVencimento;

    if ( VencMaximo < dataVencimento ) {
        erro = true;
        mensagem += "@Campo Data de Vencimento excede 31/12/<?=Sessao::getExercicio();?>!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

  function alterarEmpenho(stControle,inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getExercicio()?>&num_item='+inIndice,stControle);
  }

function validaDataEmpenho(prm){
   var dtUltimoEmpenho   = new Number();
   var dtEmpenho         = new Number();
   var dtEmpenhoCorrente = new Number("<?=Sessao::getExercicio()?>1231");
   var erro              = new Boolean(false);
   var stUltimaData      = new String();
   var ultimoEmpenho     = new String();

   var empenho = document.frm.stDtEmpenho.value.split("/");
   dtEmpenho   = empenho[2]+empenho[1]+empenho[0];

   if(document.frm.dtUltimaDataEmpenho && document.frm.stDtAutorizacao){ // Empenho por autorização
      ultimoEmpenho = document.frm.dtUltimaDataEmpenho.value.split("/");
      dtUltimaDataEmpenho = ultimoEmpenho[2]+ultimoEmpenho[1]+ultimoEmpenho[0];

      dtAutorizacao = document.frm.stDtAutorizacao.value.split("/");
      dtEmissaoAutorizacao = dtAutorizacao[2]+dtAutorizacao[1]+dtAutorizacao[0];

        if(dtUltimaDataEmpenho > dtEmissaoAutorizacao ){
            stUltimaData        = ultimoEmpenho[0]+"/"+ultimoEmpenho[1]+"/"+ultimoEmpenho[2];
        } else {
            dtUltimaDataEmpenho = dtAutorizacao[2]+dtAutorizacao[1]+dtAutorizacao[0];        
            stUltimaData  = dtAutorizacao[0]+"/"+dtAutorizacao[1]+"/"+dtAutorizacao[2];    
        }

   } else { // Empenhos Diversos
      ultimoEmpenho = document.frm.dtUltimaDataEmpenho.value.split("/");
      dtUltimaDataEmpenho = ultimoEmpenho[2]+ultimoEmpenho[1]+ultimoEmpenho[0];
      stUltimaData        = ultimoEmpenho[0]+"/"+ultimoEmpenho[1]+"/"+ultimoEmpenho[2];
      dtEmp = stUltimaData;
   }
   if(dtEmpenho!=""){
      if(dtEmpenho < dtUltimaDataEmpenho){
          erro      = true;
          mensagem  = "@Campo Data de Empenho deve ser maior ou igual que "+stUltimaData +"!";
          
      }else if(dtEmpenho > dtEmpenhoCorrente){
          erro      = true;
          mensagem  = "@Campo Data de Empenho deve ser menor ou igual que 31/12/<?=Sessao::getExercicio()?>!";
      }
   } 
   if(erro==true){
      document.frm.stDtEmpenho.value = stUltimaData;
      alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
   }else{
      if(typeof(prm)=="undefined"){
         buscaDado('verificaDataEmpenho');
      }else{
          if(prm=="autorizacao"){
              buscaDado('verificaDataEmpenhoAutorizacao');
          }
      }
   }
}

function validaDataEmpenhoOld() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtEmpenho.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substring(0,2);
        MesEmpenho = stDataEmpenho.substring(3,5);
        AnoEmpenho = stDataEmpenho.substr(6);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = ano+"0101";

        if ( dataEmpenho > dataAtual ) {
            erro = true;
            mensagem += "@Campo Data de Empenho maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        }

        if(document.frm.inCodEntidade.value==""){
            erro = true;
            mensagem += "@Campo Entidade inválido!";
        }

        if ( dataEmpenho < dataPrimeiro ) {
            erro = true;
            mensagem += "@Campo Data de Empenho menor que '01/01/"+ano+"'!";
        }

        if(mensagem != ""){
            document.frm.stDtEmpenho.value= '';
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else{ 
            buscaDado ('verificaDataEmpenho');
        }
    }
}

function validaDataEmpenhoAutorizacao() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtEmpenho.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substring(0,2);
        MesEmpenho = stDataEmpenho.substring(3,5);
        AnoEmpenho = stDataEmpenho.substr(6);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        stDataAutorizacao = document.frm.stDtAutorizacao.value;
        DiaAutorizacao = stDataAutorizacao.substring(0,2);
        MesAutorizacao = stDataAutorizacao.substring(3,5);
        AnoAutorizacao = stDataAutorizacao.substr(6);

        var dataAutorizacao = AnoAutorizacao+""+MesAutorizacao+""+DiaAutorizacao;

        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = ano+"0101";

        if ( dataEmpenho > dataAtual ) {
            erro = true;
            mensagem += "@Campo Data de Empenho maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        }

        if ( dataEmpenho < dataAutorizacao ) {
            erro = true;
            mensagem += "@Campo Data de Empenho deve ser maior que data da autorizacao ("+DiaAutorizacao+"/"+MesAutorizacao+"/"+AnoAutorizacao+")!";
        }

        if ( dataEmpenho < dataPrimeiro ) {
            erro = true;
            mensagem += "@Campo Data de Empenho menor que '01/01/"+ano+"'!";
        }

        if(document.frm.inCodEntidade.value==""){
            erro = true;
            mensagem += "@Campo Entidade inválido!";
        }

        if(mensagem != ""){
            document.frm.stDtEmpenho.value= '';
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else{ 
            buscaDado ('verificaDataEmpenhoAutorizacao');
        }
    }
}

function incluirItem() {
    var mensagem = "";
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuUnitario   = document.frm.nuVlUnitario.value;

    if (document.frm.stTipoItem.value=='Catalogo') {
        if(!document.frm.inCodItem.value){
        mensagem += '@Campo Item inválido!()';
        }
    }
    
    if (document.frm.stTipoItem.value=='Descricao') {
        if(!document.frm.stNomItem.value){
        mensagem += '@Campo Descrição do Item inválido!()';
        }
    }  

    if(!document.frm.nuQuantidade.value)
        mensagem += '@Campo Quantidade inválido!()';
    
    nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
    nuQuantidade = nuQuantidade.replace( "," ,'.');
    if( nuQuantidade == 0 )
        mensagem += "@Campo Quantidade com valor inválido!( o valor deve ser maior que 0 (zero) )";
    

    if(!document.frm.inCodUnidade.value)
        mensagem += '@Campo Unidade inválido!()';
    if(!document.frm.nuVlUnitario.value)
        mensagem += '@Campo Valor Unitário inválido!()';

    nuUnitario = nuUnitario.replace( new  RegExp("[.]","g") ,'');
    nuUnitario = nuUnitario.replace( "," ,'.');
    if( nuUnitario == 0 )
        mensagem += "@Campo Valor Unitário com valor inválido!(o valor deve ser maior que 0 (zero) )";
    
    if(!document.frm.nuVlTotal.value)
        mensagem += '@Campo Valor Total inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.getElementById('stTipoItemRadio1').setAttribute('disabled',true);
        document.getElementById('stTipoItemRadio2').setAttribute('disabled',true);
        document.frm.Ok.disabled = true;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiItemPreEmpenhoDiverso';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparItem();
        document.frm.Ok.disabled = false;
    }
}

function alterarItem() {
    var mensagem = "";
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuUnitario   = document.frm.nuVlUnitario.value;
    
    if (document.frm.stTipoItem.value=='Catalogo') {
        if(!document.frm.inCodItem.value){
        mensagem += '@Campo Item inválido!()';
        }
    }
    
    if (document.frm.stTipoItem.value=='Descricao') {
        if(!document.frm.stNomItem.value){
        mensagem += '@Campo Descrição do Item inválido!()';
        }
    } 

    if(!document.frm.nuQuantidade.value)
        mensagem += '@Campo Quantidade inválido!()';
    
    nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
    nuQuantidade = nuQuantidade.replace( "," ,'.');
    if( nuQuantidade == 0 )
        mensagem += "@Campo Quantidade com valor inválido!( o valor deve ser maior que 0 (zero) )";
    

    if(!document.frm.inCodUnidade.value)
        mensagem += '@Campo Unidade inválido!()';
    if(!document.frm.nuVlUnitario.value)
        mensagem += '@Campo Valor Unitário inválido!()';

    nuUnitario = nuUnitario.replace( new  RegExp("[.]","g") ,'');
    nuUnitario = nuUnitario.replace( "," ,'.');
    if( nuUnitario == 0 )
        mensagem += "@Campo Valor Unitário com valor inválido!(o valor deve ser maior que 0 (zero) )";
    
    if(!document.frm.nuVlTotal.value)
        mensagem += '@Campo Valor Total inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.Ok.disabled = true;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'alteradoItemPreEmpenhoDiverso';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        document.frm.btnIncluir.value='Incluir';
        document.frm.stCtrl.value = 'incluiItemPreEmpenhoDiverso';
        limparItem();
        document.frm.Ok.disabled = false;
    }
    
}

function excluirItem(stControle, inNumItem ){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumItem=' + inNumItem;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    limparItem();
}


function limparItem() {
    document.frm.stNomItem.value = '';
    document.frm.stComplemento.value = '';
    document.frm.inCodUnidade.value = document.frm.inCodUnidadePadrao.value;
    document.frm.nuQuantidade.value = '';
    document.frm.nuVlUnitario.value = '';
    document.frm.nuVlTotal.value = '';
    document.frm.hdnNumItem.value = '';
    document.frm.inCodUnidadeMedida.value = '';
    document.frm.stNomUnidade.value = '';
    document.frm.inCodItem.value = '';
    document.getElementById('stNomItemCatalogo').innerHTML = '&nbsp;';
    document.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';
    jQuery('#inMarca').val('');
    jQuery('#stNomeMarca').html('&nbsp;');
    jQuery('input[name=stNomeMarca]').val('');

    document.frm.btnIncluir.value='Incluir';
    document.frm.btnIncluir.setAttribute('onclick','return incluirItem()');
}

function gerarValorTotal(objeto) {
    var nuVlUnidade = document.frm.nuVlUnitario.value;
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuVlTotal = "";
    var nuVlTotalTeste = "";
    if( nuVlUnidade && nuQuantidade ) {
        nuVlUnidade = nuVlUnidade.replace( new  RegExp("[.]","g") ,'');
        nuVlUnidade = nuVlUnidade.replace( "," ,'.');
        nuQuantidade = nuQuantidade.replace( new  RegExp("[.]","g") ,'');
        nuQuantidade = nuQuantidade.replace( "," ,'.');
        nuVlTotal = nuVlUnidade * nuQuantidade;
        nuVlTotal = Math.round(nuVlTotal*Math.pow(10,2))/Math.pow(10,2);
        nuVlTotalTeste = nuVlTotal;
        nuVlTotal = new String(nuVlTotal);
        arVlTotal = nuVlTotal.split(".") ;
        if( !arVlTotal[1] )
            arVlTotal[1] = '00';
        var inCount = 0;
        var inValor = "";
        for( var i = (arVlTotal[0].length-1); i >= 0; i-- ) {
            if( inCount == 3 ) {
               inValor = '.' + inValor;
               inCount = 0;
            }
            inValor = arVlTotal[0].charAt(i) + inValor;
            inCount++;
        }
        nuVlTotal = inValor + ',' + arVlTotal[1];

        var boTipo = /^[0-9.]*,[0-9]$/;
        if (boTipo.test(nuVlTotal)){
            nuVlTotal = nuVlTotal + String(0);
        }

        if (nuVlTotalTeste > 999999999999.99) {
            objeto.value = '';
            document.frm.nuVlTotal.value = '0,00';
            objeto.focus();
            alertaAviso('O resultado do campo Valor Total ('+nuVlTotal+') não pode ultrapassar o valor limite de 999.999.999.999,99','form','erro','<?=Sessao::getId();?>', '../');
        }
        else{
            document.frm.nuVlTotal.value = nuVlTotal;
        }
    }
}

function limparCampos() {
    var f = document.frm;
    f.inCodDespesa.value = "";
    limpaSelect(f.stCodClassificacao,0);
    f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');
    document.getElementById("stNomDespesa").innerHTML = "&nbsp;";

    f.inCodEntidade.focus();
}

function limparTodos() {
    var d = document;
    limparCampos();
    d.getElementById( "stNomFornecedor" ).innerHTML = "&nbsp;";
    d.getElementById( "spnSaldoDotacao" ).innerHTML = "";
}
 
function mudaMenu(titulo, func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho&cod_func_pass="+func+"&stTitulo="+titulo;
    parent.frames["telaMenu"].location.replace(sPag);
}

function validaDesdobramento() {
    if (document.getElementById( 'stCodClassificacao' ).value != "") {
        document.frm.Ok.disabled = false;
    }
}

function proximoFoco(valor) {
    if ( valor.length == 0 ) {
        document.frm.Ok.focus();
    }
}
function buscaDtEmpenho( valor ) {
    if( valor != "") {
        limparCampos();
        BloqueiaFrames(true,false);
        buscaDado('buscaDtEmpenho');
    }
}
function Salvar(){
    if (jq('#boMsgValidadeFornecedor').val() == 'true'){
        if(confirm('Fornecedor possui documento fora da data de validade. Deseja continuar?')) {
            erro = false;
            jq('#frm').submit();
        }else{
            erro = true;
        }
    }else{
        jq('#frm').submit();
    }
}

function geraValor(objeto)
{
    if (parseInt(objeto.value) < 0) {
        objeto.value = '';
        alertaAviso('O valor unitário deve ser um valor positivo.','form','erro','<?=Sessao::getId();?>', '../');
    } else {
        gerarValorTotal(objeto);
    }
}

function verificaModalidade(objeto){
    if((objeto.value == '10')||(objeto.value == '11')){
        buscaDado ('buscaFundamentacaoLegal');
    } else {
        document.getElementById('spnFundamentacaoLegal').innerHTML = "";
    }
}

function unidadeItem( Ent ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = "unidadeItem";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&codItem='+Ent;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function habilitaCampos(ent) {
    var f = document.frm;
    
    //habilita campos para Catalogo
    if( ent == 'Catalogo' ){
        f.stTipoItem.value = ent;
        f.stNomItem.disabled = true;
        f.stNomItem.value = '';
        f.inCodItem.disabled = false;
        f.inCodItem.value = '';
        document.getElementById('stNomItemCatalogo').disabled = false;
        document.getElementById('stNomItemCatalogo').innerHTML = "&nbsp;";
        document.getElementById('imgBuscar').hidden= false;
        document.getElementById('stUnidadeMedida').innerHTML = '';
        document.getElementById('inCodUnidade').disabled = true;
    }

    //habilita campos para Descricao
    if( ent == 'Descricao' ){
        f.stTipoItem.value = ent;
        f.stNomItem.disabled = false;
        f.stNomItem.value = '';
        f.inCodItem.disabled = true;
        f.inCodItem.value = '';
        document.getElementById('stNomItemCatalogo').setAttribute('disabled',true);
        document.getElementById('stNomItemCatalogo').innerHTML = "&nbsp;";
        document.getElementById('imgBuscar').setAttribute('hidden',true);
        document.getElementById('stUnidadeMedida').innerHTML = '';
        document.getElementById('inCodUnidade').disabled = false;
    }
}
function limparOrdem(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = "limparOrdem";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
    
    document.getElementById('stTipoItemRadio1').disabled = false;
    document.getElementById('stTipoItemRadio2').disabled = false;
}
</script>
