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
    * Arquivo JavaScript utilizado na Consulta de Inclusão de Veiculos 
    * Data de Criação   : 11/10/2006


    * @author Analista      : Cleisson Barboza 
    * @author Desenvolvedor : Rodrigo 

    * @ignore
    
    * Casos de uso: uc-03.05.23
  
*/
/*
$Log$
Revision 1.2  2007/07/24 13:10:09  hboaventura
Bug#9730#

Revision 1.1  2006/10/16 08:56:53  rodrigo
Caso de Uso 03.05.23

Revision 1.1  2006/10/13 14:44:12  rodrigo
Caso de Uso 03.05.23

*/
?>
<script type="text/javascript">
   var data  = new Date();
   function consultaContrato(inIndice){
    var campo = new String();
    var cmp   = new Number(0);
    document.forms[0].stCtrl.value = "consultaContrato";
    while(cmp in document.forms[0].elements){
      campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
      cmp++;
    }
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value);
   }

   function excluirListaVeiculos(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirListaVeiculos');
   }

   function alterarListaVeiculos(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'alterarListaVeiculos');
   }

   function incluiVeiculos(prm){
       var erro     = new Boolean(false);
       var mensagem = new String();
       var campo    = new String();
       var cmp      = new Number(0);

       if(document.forms[0].stCtrl.value!="alteradoListaVeiculos"){
          document.forms[0].stCtrl.value = prm;
       }else{
          document.forms[0].stCtrl.value = "alteradoListaVeiculos";
       }

       campo  = document.frm.inVeiculo.value.length;
       vcampo = document.frm.inVeiculo.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo Veículo de publicação é obrigatório";
        erro = true;
       }

       campo  = document.frm.dtDataVigencia.value.length;
       vcampo = document.frm.dtDataVigencia.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo data de publicação é obrigatório";
        erro = true;
       }
       if(erro!=true){
         while(cmp in document.forms[0].elements){
           campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
           cmp++;
         }
         ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value);
         document.frm.action       = '<?=$pgProc;?>?<?=Sessao::getId();?>';
         document.frm.stCtrl.value                                       = "";
         document.frm.inVeiculo.value                                    = "";
         document.frm.dtDataVigencia.value                               = "<?=date('d/m/Y')?>";
         document.frm.stObservacao.value                                 = "";
         document.getElementById("stNomCgmVeiculoPublicadade").innerHTML = "&nbsp;";
         document.frm.inVeiculo.focus();
       }else{
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
       }
  }
  
  function limparVeiculo(){
  	$('inVeiculo').value = '';
  	$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';
  	$('stObservacao').value = '';
  }
</script>
