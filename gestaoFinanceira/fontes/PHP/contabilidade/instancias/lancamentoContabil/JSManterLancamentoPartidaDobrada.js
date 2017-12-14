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
    
    * Casos de uso: uc-02.02.33
*/

/*
$Log$
Revision 1.2  2007/07/05 18:51:31  rodrigo_sr
Caso de Uso 02.02.33

Revision 1.1  2006/10/24 11:00:06  rodrigo
Caso de Uso 02.02.33

*/

?>
<script type="text/javascript">

   function incluirDebito(prm){
       var erro     = new Boolean(false);
       var mensagem = new String();
       var campo    = new String();
       var cmp      = new Number(0);
       document.forms[0].stCtrl.value = prm;

       campo  = document.frm.inCodContaDebito.value.length;
       vcampo = document.frm.inCodContaDebito.value;

       if(campo == 0){
        mensagem += "@O campo conta débito é obrigatório";
        erro = true;
       }

       campo  = document.frm.nuVlDebito.value.length;
       vcampo = document.frm.nuVlDebito.value;

       if(campo == 0 | vcampo == '0,00'){
        mensagem += "@O campo valor débito é obrigatório";
        erro = true;
       }

       campo  = document.frm.inCodHistoricoDebito.value.length;
       vcampo = document.frm.inCodHistoricoDebito.value;

       if(campo == 0){
        mensagem += "@O campo histórico débito é obrigatório";
        erro = true;
       }

       if(erro!=true){
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
         ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>'+campo,document.frm.stCtrl.value);
         document.frm.inCodContaDebito.focus();
         document.frm.action                                       = '<?=$pgProc;?>?<?=$sessao->id;?>';
         document.frm.stCtrl.value                                 = "";
         document.frm.inCodContaDebito.value                       = "";
         document.frm.nuVlDebito.value                             = "";
         document.frm.inCodHistoricoDebito.value                   = "";
         document.frm.stComplementoDebito.value                    = "";
         document.getElementById("innerContaDebito").innerHTML     = "&nbsp;";
         document.getElementById("stNomHistoricoDebito").innerHTML = "&nbsp;";
        }else{
         alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
       }
  }

  function excluirListaDebito(inIndice){
     ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>&id='+inIndice,'excluirListaDebito');
  }

   function incluirCredito(prm){
       var erro     = new Boolean(false);
       var mensagem = new String();
       var campo    = new String();
       var cmp      = new Number(0);
       document.forms[0].stCtrl.value = prm;

       campo  = document.frm.inCodContaCredito.value.length;
       vcampo = document.frm.inCodContaCredito.value;

       if(campo == 0){
        mensagem += "@O campo conta crédito é obrigatório";
        erro = true;
       }

       campo  = document.frm.nuVlCredito.value.length;
       vcampo = document.frm.nuVlCredito.value;

       if(campo == 0 | vcampo == '0,00'){
        mensagem += "@O campo valor crédito é obrigatório";
        erro = true;
       }

       campo  = document.frm.inCodHistoricoCredito.value.length;
       vcampo = document.frm.inCodHistoricoCredito.value;

       if(campo == 0){
        mensagem += "@O campo histórico crédito é obrigatório";
        erro = true;
       }

       if(erro!=true){
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
         ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>'+campo,document.frm.stCtrl.value);
         document.frm.inCodContaCredito.focus();
         document.frm.action                                        = '<?=$pgProc;?>?<?=$sessao->id;?>';
         document.frm.stCtrl.value                                  = "";
         document.frm.inCodContaCredito.value                       = "";
         document.frm.nuVlCredito.value                             = "";
         document.frm.inCodHistoricoCredito.value                   = "";
         document.frm.stComplementoCredito.value                    = "";
         document.getElementById("innerContaCredito").innerHTML     = "&nbsp;";
         document.getElementById("stNomHistoricoCredito").innerHTML = "&nbsp;";
        }else{
         alertaAviso(mensagem,'form','erro','<?=$sessao->id?>');
       }
  }

  function excluirListaCredito(inIndice){
     ajaxJavaScript('<?=$pgOcul."?".$sessao->id?>&id='+inIndice,'excluirListaCredito');
  }

  function buscaDado( BuscaDado ){
      document.frm.stCtrl.value = BuscaDado;
      document.frm.action = '<?=$pgOcul;?>?<?=$sessao->id;?>';
      document.frm.submit();
      document.frm.action = '<?=$pgProc;?>?<?=$sessao->id;?>';
  }
  
  function validaMes( campo , MesProcessamento ) {
    var Mes = campo.value.split('/');
    Mes[1] = parseInt( Mes[1],10 );
    
    if( Mes[1] != MesProcessamento )
        alertaAviso('@Valor inválido. (O Mês digitado não corresponde ao mês de Processamento)','form','erro','<?=$sessao->id?>');
}

function limparDebito() {

    document.frm.inCodContaDebito.value = '';
    document.getElementById('innerContaDebito').innerHTML = '&nbsp;';
    document.frm.inCodHistoricoDebito.value = '';
    document.getElementById('stNomHistoricoDebito').innerHTML = '&nbsp;';
    document.frm.stComplementoDebito.value = '';
    document.frm.nuVlDebito.value = '';
}


function limparCredito() {

    document.frm.inCodContaCredito.value = '';
    document.getElementById('innerContaCredito').innerHTML = '&nbsp;';
    document.frm.inCodHistoricoCredito.value = '';
    document.getElementById('stNomHistoricoCredito').innerHTML = '&nbsp;';
    document.frm.stComplementoCredito.value = '';
    document.frm.nuVlCredito.value = '';
}

function verificaDebitoCreditoInclusao() {

   var stDebitos  = document.frm.stDebitos.value;
   var stCreditos = document.frm.stCreditos.value;
   if( stDebitos == 'true' && stCreditos == 'false' ){
       confirmPopUp('Incluir conta débito','Atenção! Conta crédito não informada. Deseja continuar ?','document.frm.submit()');
   }else if( stDebitos == 'false' && stCreditos == 'true' ){
       confirmPopUp('Incluir conta crédito','Atenção! Conta débito não informada. Deseja continuar ?','document.frm.submit()'); 
   }else{
       document.frm.submit();
   } 
    
}

</script>
