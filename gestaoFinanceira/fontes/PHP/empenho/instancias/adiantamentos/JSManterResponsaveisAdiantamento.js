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
    * Arquivo JavaScript utilizado na Consulta de Inclusão de Responsaveis por Adiantamento 
    * Data de Criação   : 16/10/2006


    * @author Analista      : Cleisson Barboza 
    * @author Desenvolvedor : Rodrigo 

    * @ignore
    
    * Casos de uso: uc-02.03.32
  
*/

/*
$Log$
Revision 1.2  2007/08/27 20:41:10  luciano
Bug#10007#

Revision 1.1  2007/08/10 13:55:27  luciano
movido de lugar

Revision 1.5  2007/07/09 20:59:03  luciano
Bug#9402#

Revision 1.4  2007/06/25 19:08:14  luciano
Bug#9402#,Bug#9359#,Bug#9094#

Revision 1.3  2007/06/06 19:16:37  luciano
Bug#9359#

Revision 1.2  2007/03/08 15:54:59  luciano
Bug#8609#

Revision 1.1  2006/10/18 18:57:28  rodrigo
Caso de Uso 02.03.32

*/
?>
<script>
   function Limpar() {

    document.frm.stCtrl.value                                  = "";   
    document.frm.inCGM.value                                   = "";
    document.frm.inCodContaLancamento.value                    = "";
    document.frm.innerContaLancamento.value                    = "";
    document.frm.stNomCGM.value                                = "";
    document.frm.HdninCGM.value                                = "";
    document.frm.HdnNomResponsavel.value                       = '';
    document.frm.HdninCodContaLancamento.value                 = "";
    document.frm.inCGM.disabled                                = false;    
    document.frm.inCodContaLancamento.disabled                 = false; 
    document.frm.inCodContraPartida.disabled                   = false;
    document.frm.inPrazo.disabled                              = false;
    document.getElementById('incluiResponsavel').value         = 'Incluir';
    document.getElementById("stNomCGM").innerHTML              = "&nbsp;";
    document.getElementById("innerContaLancamento").innerHTML  = "&nbsp;";
    document.getElementById("SituacaoS").checked               = true;
    document.getElementById('btCredor').style.display          = 'inline';  
    document.getElementById('btContaLancamento').style.display = 'inline'; 
    document.getElementById('btContraPartida').style.display   = 'inline';
    
    montaParametrosGET('limparFormulario');
    
   }
    
   function limparResponsaveis(){
     document.frm.stCtrl.value                                  = "";   
     document.frm.inCGM.value                                   = "";
     document.frm.inCodContaLancamento.value                    = "";
     document.frm.innerContaLancamento.value                    = "";
     document.frm.stNomCGM.value                                = "";
     document.frm.HdninCGM.value                                = "";
     document.frm.HdnNomResponsavel.value                       = '';
     document.frm.HdninCodContaLancamento.value                 = "";
     document.frm.inCGM.disabled                                = false;    
     document.frm.inCodContaLancamento.disabled                 = false; 
     document.getElementById('incluiResponsavel').value         = 'Incluir';
     document.getElementById("stNomCGM").innerHTML              = "&nbsp;";
     document.getElementById("innerContaLancamento").innerHTML  = "&nbsp;";
     document.getElementById("SituacaoS").checked               = true;
     document.getElementById('btCredor').style.display          = 'inline';  
     document.getElementById('btContaLancamento').style.display = 'inline'; 
   }
   
   function excluirListaResponsaveis(inIndice){
     ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'excluirListaResponsaveis');
   }

   function alterarListaResponsaveis(inIndice){
    ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+inIndice,'alterarListaResponsaveis');
   }

   function incluiResponsaveis(prm){
       var erro     = new Boolean(false);
       var mensagem = new String();
       var campo    = new String();
       var cmp      = new Number(0);

       if(document.forms[0].stCtrl.value!="alteradoListaResponsaveis"){
          document.forms[0].stCtrl.value = prm;
       }else{
          document.forms[0].stCtrl.value = "alteradoListaResponsaveis";
       }

       campo  = document.frm.inPrazo.value.length;
       vcampo = document.frm.inPrazo.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo Prazo máximo é obrigatório";
        erro = true;
       }

       campo  = document.frm.inCodContraPartida.value.length;
       vcampo = document.frm.inCodContraPartida.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo Contrapartida Contábil é obrigatório";
        erro = true;
       }

       campo  = document.frm.inCGM.value.length;
       vcampo = document.frm.inCGM.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo Credor é obrigatório";
        erro = true;
       }

       campo  = document.frm.inCodContaLancamento.value.length;
       vcampo = document.frm.inCodContaLancamento.value;

       if(campo == 0 | vcampo == '0'){
        mensagem += "@O campo Conta Contábil é obrigatório";
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
         ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,document.frm.stCtrl.value,false);
        }else{
         alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
       }
  }
</script>

