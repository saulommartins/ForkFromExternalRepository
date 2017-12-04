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
    * Página de Formulario de Inclusao/Alteracao de Lancamento Partida Dobrada
    * Data de Criação   : 19/10/2006


    * @author Analista      : Gelson Gonçalves 
    * @author Desenvolvedor : Rodrigo

    * @ignore
    
    * Casos de uso: uc-02.03.31
*/

/*
$Log$
Revision 1.6  2007/09/21 20:04:13  luciano
Ticket#9111#

Revision 1.5  2007/09/10 20:55:18  luciano
Bug#10011#

Revision 1.4  2007/08/31 15:43:36  luciano
Bug#10011#

Revision 1.3  2007/08/28 14:51:43  luciano
Bug#10010#

Revision 1.2  2007/08/27 15:06:40  luciano
Bug#10006#

Revision 1.1  2007/08/10 13:54:10  luciano
movido de lugar

Revision 1.20  2007/07/20 16:15:25  luciano
Bug#9677#

Revision 1.19  2007/07/18 18:56:05  luciano
Bug#9679#

Revision 1.18  2007/07/17 15:15:39  luciano
Bug#9674#

Revision 1.17  2007/07/17 13:54:53  luciano
Bug#9373#

Revision 1.16  2007/07/09 15:10:32  luciano
Bug#9373#

Revision 1.15  2007/06/29 15:26:35  luciano
Bug#9373#

Revision 1.14  2007/06/29 14:34:25  luciano
Bug#9365#

Revision 1.13  2007/06/29 14:19:59  luciano
Bug#9364#

Revision 1.12  2007/06/20 20:41:45  luciano
Bug#9111#

Revision 1.11  2007/06/20 15:29:16  luciano
Bug#9104#

Revision 1.10  2007/06/19 22:10:25  luciano
Bug#9104#

Revision 1.9  2007/04/27 21:23:35  luciano
Bug#9087#

Revision 1.8  2007/04/27 19:59:24  luciano
Bug#9087#

Revision 1.7  2007/04/26 18:51:15  luciano
Bug#9086#

Revision 1.6  2007/04/13 15:15:29  luciano
Bug#9083#

Revision 1.5  2007/04/12 15:43:28  luciano
Bug#9085#

Revision 1.4  2007/04/12 14:52:25  luciano
Bug#8898#

Revision 1.3  2007/04/03 16:12:48  luciano
Bug#8900#

Revision 1.2  2007/03/29 14:33:19  luciano
Bug#8899#

Revision 1.1  2006/11/01 11:59:00  rodrigo
Caso de Uso 02.03.31

*/

?>
<script type="text/javascript">

   function incluirNotaFiscal(prm){

       var erro     = new Boolean(false);
       var mensagem = new String();
       var campo    = new String();
       var cmp      = new Number(0);
       
       dataPagamentoEmpenho = document.frm.stDataPagamentoEmpenho.value;
       dataPrestacaoContas  = document.frm.stDtPrestacaoContas.value;
       dataAtual            = document.frm.stDataAtual.value;        
       
       
       if(document.frm.stCtrl.value!="alteradoListaPrestacaoContas"){
         document.frm.stCtrl.value = prm;
       }

       campo  = document.frm.stDtPrestacaoContas.value.length;
       vcampo = document.frm.stDtPrestacaoContas.value;
       
       if(campo == 0 && erro != true){
        mensagem += "@O campo data prestação de contas é obrigatório.";
        erro = true;
       } else {
           if ( parseInt( vcampo.split( "/" )[2].toString() + vcampo.split( "/" )[1].toString() + vcampo.split( "/" )[0].toString() ) < parseInt( dataPagamentoEmpenho.split( "/" )[2].toString() + dataPagamentoEmpenho.split( "/" )[1].toString() + dataPagamentoEmpenho.split( "/" )[0].toString() ) ) {
            mensagem += "@A data prestação de contas não pode ser anterior a data de pagamento do empenho ("+dataPagamentoEmpenho+").";
            erro = true;    
           }
           if ( parseInt( vcampo.split( "/" )[2].toString() + vcampo.split( "/" )[1].toString() + vcampo.split( "/" )[0].toString() ) > parseInt( dataAtual.split( "/" )[2].toString() + dataAtual.split( "/" )[1].toString() + dataAtual.split( "/" )[0].toString() ) ) {
            mensagem += "@A data prestação de contas não pode ser superior a data atual ("+dataAtual+").";
            erro = true;    
           }
       }

       campo  = document.frm.stDataDocumento.value.length;
       vcampo = document.frm.stDataDocumento.value;

       if(campo == 0 && erro != true){
        mensagem += "@O campo data do documento é obrigatório.";
        erro = true;
       } else {
           if ( parseInt( vcampo.split( "/" )[2].toString() + vcampo.split( "/" )[1].toString() + vcampo.split( "/" )[0].toString() ) < parseInt( dataPagamentoEmpenho.split( "/" )[2].toString() + dataPagamentoEmpenho.split( "/" )[1].toString() + dataPagamentoEmpenho.split( "/" )[0].toString() ) ) {
               mensagem += "@A data do documento não pode ser anterior a data de pagamento do empenho ("+dataPagamentoEmpenho+").";
               erro = true;    
           }
           if(dataPrestacaoContas) {
               if ( parseInt( vcampo.split( "/" )[2].toString() + vcampo.split( "/" )[1].toString() + vcampo.split( "/" )[0].toString() ) > parseInt( dataPrestacaoContas.split( "/" )[2].toString() + dataPrestacaoContas.split( "/" )[1].toString() + dataPrestacaoContas.split( "/" )[0].toString() ) ) {
                   mensagem += "@A data do documento não pode ser superior a data de prestação de contas.";
                   erro = true;
               }
           }
            
       }

       campo  = document.frm.inCodTipoDocumento.value.length;
       vcampo = document.frm.inCodTipoDocumento.value;

       if(campo == 0 && erro != true){
        mensagem += "@O campo tipo de documento é obrigatório.";
        erro = true;
       }
       
       campo  = document.frm.inNroDocumento.value.length;
       vcampo = document.frm.inNroDocumento.value;

       if(campo == 0 && erro != true ){
           tcampo = document.frm.inCodTipoDocumento.options[document.frm.inCodTipoDocumento.value].text;
           if(tcampo != 'Outros') {
                mensagem += "@O campo número do documento é obrigatório para o Tipo de Documento "+tcampo+".";
                erro = true;
           }
       }
       
       campo  = document.frm.inCodFornecedor.value.length;
       vcampo = document.frm.inCodFornecedor.value;

       if(campo == 0 && erro != true ){
        mensagem += "@O campo fornecedor é obrigatório.";
        erro = true;
       }

       campo  = document.frm.nuValor.value.length;
       vcampo = document.frm.nuValor.value;

       if(campo == 0 && erro != true ){
        mensagem += "@O campo valor é obrigatório.";
        erro = true;
       }else{
            var vlrPago      = document.frm.inVlPago.value;
            var vlrNota      = document.frm.nuValor.value.replace('.','').replace('.','').replace(',','.');

            if(vlrNota <= '0.00') {
                mensagem += "@Campo valor inválido (O valor deve ser maior que zero).";
                erro = true;                                                                            
            }
                       
            if((vlrPago - vlrNota) < 0){
                mensagem += "@O valor total da prestação de contas não pode ultrapassar o valor pago.";
                erro = true;                                                                            
            }
       }
       
       campo  = document.frm.stJustificativa.length;
       vcampo = document.frm.stJustificativa.value;
       
       if(campo != 0 && campo > 80 && erro != true ) {
            mensagem += "@O campo justificativa não pode ultrapassar 80 caracteres.";
            erro = true;                                                                             
       }
       
       if(erro!=true){
         var cmp   = new Number(0);
         var campo = new Object();
         while(cmp in document.forms[0].elements){
           if(document.forms[0].elements[cmp].type!="radio"){
                campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;      
           }else{
              if(document.forms[0].elements[cmp].checked==true){
                 campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
              }
           }
           cmp++;
          }
         ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value);
         document.getElementById("stNomCredor").innerHTML="&nbsp;";
       }else{
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
       }
   }  

   function limpaDado(){
     var cmp = new Number(0);
     while(cmp in document.forms[0].elements){
         if((document.forms[0].elements[cmp].type=="text" | document.forms[0].elements[cmp].type=="textarea") && (document.forms[0].elements[cmp].getAttribute('id') != 'stDtPrestacaoContas')){
            document.forms[0].elements[cmp].value="";
         }else if(document.forms[0].elements[cmp].type=="select-one"){
            document.forms[0].elements[cmp].options.selectedIndex = 0;
         }
      cmp++;
     }

     document.frm.stNomCredor.value = '';
     document.frm.HdnCodItem.value  = '';
     document.getElementById('stNomCredor').innerHTML= '&nbsp;';
     document.getElementById('incluirNota').value='Incluir'; 
     document.frm.stCtrl.value = 'incluir'                  ;
   }

   function buscaDado( BuscaDado ){
     document.frm.target       = 'oculto';
     document.frm.stCtrl.value = BuscaDado;
     document.frm.action       = '<?=$pgCred;?>?<?=Sessao::getId();?>';
     document.frm.submit();
     document.frm.action       = '<?=$pgProc;?>?<?=Sessao::getId();?>';
   }

</script>
