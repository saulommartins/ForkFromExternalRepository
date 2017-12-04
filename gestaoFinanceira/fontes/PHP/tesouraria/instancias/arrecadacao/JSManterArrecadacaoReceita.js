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
    * Data de Criação   : 21/10/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-08-22 13:09:02 -0300 (Qua, 22 Ago 2007) $
    
    * Casos de uso: uc-02.04.04

*/

/*
$Log$
Revision 1.8  2007/08/22 16:09:02  cako
Bug#9971#

Revision 1.7  2007/07/13 20:13:44  cako
Bug#9569#

Revision 1.6  2007/02/23 16:45:49  luciano
#7856#

Revision 1.5  2007/01/15 16:57:25  luciano
Bug #7856#

Revision 1.4  2006/09/01 16:56:54  jose.eduardo
uc-02.04.04

Revision 1.3  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparItem() {
    parent.frames['telaPrincipal'].document.frm.inCodigoEntidade='';
    parent.frames['telaPrincipal'].document.getElementById('inCodPlano').value='';
    parent.frames['telaPrincipal'].document.frm.inCodReceita.value='';
    parent.frames['telaPrincipal'].document.frm.nuValor.value='';
    parent.frames['telaPrincipal'].document.frm.stObservacoes.value='';
    parent.frames['telaPrincipal'].document.getElementById('stNomConta').innerHTML='&nbsp;';
    parent.frames['telaPrincipal'].document.getElementById('stNomReceita').innerHTML='&nbsp;';
}

function incluirItem(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    var erro     = '';

    if( !document.frm.inCodEntidade.value )
        erro = erro + '@Campo Entidade inválido!()';
    if( !document.frm.inCodPlano.value )
        erro = erro + '@Campo Conta inválido!()';
    if( !document.frm.inCodReceita.value )
        erro = erro + '@Campo Cód. Receita inválido!()';
    if( !document.frm.nuValor.value )
        erro = erro + '@Campo Valor inválido!()';
        
    if( erro != '' ) {
        alertaAviso(erro,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = 'incluirItem';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.stCtrl.value = stCtrl;
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }
}

function excluirItem( stAcao, stExercicio, stCarne ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = stAcao;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stCarne='+stCarne+'&stExercicio='+stExercicio;
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function somaValorGeral( nuVlSoma ) {
    var nuVlGeral = parent.frames['telaPrincipal'].document.frm.nuVlTotalLista.value;
    if( nuVlGeral == '' ) nuVlGeral = new String( '0' );

    nuVlSoma  = nuVlSoma.replace( new  RegExp("[.]","g") ,'');
    nuVlSoma  = nuVlSoma.replace( ",", "." );

    nuVlGeral = new String( parseFloat(nuVlGeral) + parseFloat( nuVlSoma ) );

    arVlTotal = nuVlGeral.split( "." );
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
    while( arVlTotal[1].length < 2 ) {
        arVlTotal[1] = arVlTotal[1] + '0';
    }
    nuVlTotal = inValor + ',' + arVlTotal[1];
    parent.frames['telaPrincipal'].document.frm.nuVlTotalLista.value = nuVlGeral;
    calculaTroco();
    parent.frames['telaPrincipal'].document.getElementById('nuVlTotalLista').innerHTML = nuVlTotal;
}

function calculaTroco() {
    d = parent.frames['telaPrincipal'].document;
    f = d.frm;
    var nuVlRecebido   = new String( f.nuVlRecebido.value   );
    var nuVlTotal = new String( f.nuValor.value );
    if( nuVlTotal == "" ) nuVlTotal = new String( '0' );

    if( nuVlRecebido != "" ) {
        nuVlRecebido = nuVlRecebido.replace( new RegExp( "[.]","g"), '' );
        nuVlRecebido = nuVlRecebido.replace( ",", "." );

        nuVlTroco = new String( parseFloat( nuVlRecebido ) - parseFloat( nuVlTotal ) );
        arVlTroco = nuVlTroco.split( "." );
        if( !arVlTroco[1] )
            arVlTroco[1] = '00';
        var inCount = 0;
        var inValor = "";
        for( var i = (arVlTroco[0].length-1); i >= 0; i-- ) {
            if( inCount == 3 ) {
               inValor = '.' + inValor;
               inCount = 0;
            }
            inValor = arVlTroco[0].charAt(i) + inValor;
            var expNumeros = new RegExp( "[0-9]" );
            if( expNumeros.test( arVlTroco[0].charAt(i-1) ) )
                inCount++;
        }   
        if( arVlTroco[1].length > 2 ) {
            inTerceiroDigito = arVlTroco[1].charAt(2);
            arVlTroco[1] = arVlTroco[1].substr(0,2);
            if( inTerceiroDigito > 5 ) {
                arVlTroco[1] = new String( parseInt( arVlTroco[1] ) + 1 );
                if( arVlTroco[1].length < 2 ) arVlTroco[1] = '0'+arVlTroco[1];
            }
        } else {
            while( arVlTroco[1].length < 2 ) {
                arVlTroco[1] = arVlTroco[1] + '0';
            }
        }
        nuVlTroco = inValor + ',' + arVlTroco[1];
        
        d.getElementById('stVlTroco').innerHTML = nuVlTroco;
    }
}

function Limpar() {
    
    jq('#inCodigoEntidade').removeAttr('disabled');   
    var idx = jq('#inCodigoEntidade').val();
    jq("#inCodigoEntidade [value="+idx+"]").removeAttr("selected");
    jq('#stNomReceita').parent().parent().closest('tr').hide();
    jq('#stNomConta').parent().parent().closest('tr').hide();
    jq('stNomConta').html('&nbsp;');
    jq('#inCodPlano').val('1234');
    jq('#inCodPlano').removeAttr('value');
    jq('#nuValor_label').html('0,00');
    jq('nuValor').val('0,00');
    limpaFormulario();
    jq('#stCodBarraOtico').attr('disable',false);
    jq('#stCodBarraManual').attr('disable',false);
    $('imgReceita').style.display='inline';
}

function formataUS( valor ) {
   var retorno = valor;

   retorno = valor.replace( new RegExp( "[\.]", "gi" ), ""   );
   retorno = retorno.replace( new RegExp( ",","gi" )    , "."  );

   return retorno;
} 

function validaValorEstornado() {
    var erro = false;
    var mensagem = "";
    
    var flVlArrecadado  = parseFloat( formataUS( document.frm.nuHdnValorEstornar.value ) );
    var flVlEstornar	= parseFloat( formataUS( document.frm.nuValorEstornar.value ) );
    
    if ( flVlArrecadado < flVlEstornar ) {
        erro = true;
        mensagem = "@<b><i>Valor a Estornar</i></b> não deve ultrapassar o Valor Arrecadado.";
    }
  
    if ( flVlEstornar == 0.00 && erro == false ) {
        erro = true;
        mensagem = "@<b><i>Valor a Estornar</b></i> deve ser maior que 0,00.";
    }

    alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');

    return !erro;
}

function salvarArrecadacaoEstornada(){
    var mensagem   = "";
    if( Valida() ){
        if ( validaValorEstornado() ){
            BloqueiaFrames(true, false);
            document.frm.submit();
        }
    }
 }

 function abrePopUpContas(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height) {
    var stEntidades = '';

    if (document.frm.inCodEntidade.options.length < 1) {
        alertaAviso("Nenhum entidade selecionada!","n_incluir", "erro","<?=Sessao::getId()?>");
    } else {
        for ( i = 0; i < document.frm.inCodEntidade.options.length; i++) {
            stEntidades += ','+document.frm.inCodEntidade.options[i].value;
        }
        
        abrePopUp(arquivo,nomeform,camponum,camponom,tipodebusca+"&inCodEntidade="+stEntidades.substr(1),sessao,width,height)
    }
 }

</script>
                
