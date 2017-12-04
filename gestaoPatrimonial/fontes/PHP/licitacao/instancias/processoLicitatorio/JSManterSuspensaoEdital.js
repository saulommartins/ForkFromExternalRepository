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
    * Data de CriaÃ§Ã£o   : 20/11/2006 


    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 19168 $
    $Name$
    $Autor:$
    $Date: 2007-01-09 07:55:42 -0200 (Ter, 09 Jan 2007) $

    * Casos de uso: uc-03.05.18

*/

/*
$Log$
Revision 1.2  2007/01/09 09:55:28  rodrigo
*** empty log message ***

Revision 1.1  2006/11/20 11:49:49  fernando
Função para limpar o array de participantes


*/
?>
<script type="text/javascript">

function limpaFormularioParticipanteLicitacao(){
    if(document.getElementById('trCGMConsorcio').style.display==''){
       document.frm.cgmConsorcio.value="";
       document.getElementById('stNomConsorcio').innerHTML = "&nbsp;";
    }
    document.frm.cgmParticipante.value = "";
    document.frm.cgmRepLegal.value     = "";
    document.frm.dataInclusao.value    = "<?=date('d/m/Y');?>";

    document.getElementById('stNomParticipante').innerHTML = "&nbsp;";
    document.getElementById('stNomRepLegal').innerHTML     = "&nbsp;";

}

function ValidaParticipanteLicitacao(){
 var mensagem = new String();
 var erro     = new Boolean(false);

    if(document.getElementById('trCGMConsorcio').style.display==''){
       if(document.frm.cgmConsorcio.value==""){
           mensagem+="@O campo CGM do consórcio é obrigatório";
           erro     =true;
       }
    }

    if(document.frm.cgmParticipante.value==""){
        mensagem+="@O campo CGM do Participante é obrigatório";
        erro     =true;
    }
    if(document.frm.cgmRepLegal.value==""){
        mensagem+="@O campo CGM do Representante Legal é obrigatório";
        erro     =true;
    }
    if(document.frm.dataInclusao.value==""){
        mensagem+="@O campo Data de inclusão na Licitação é obrigatório";
        erro     =true;
    }
    if(erro==true){
      alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    }else{
      var campo = new String();
      var cmp   = new Number(0);
          while(cmp in document.forms[0].elements){
            campo+="&"+document.forms[0].elements[cmp].name+"="+document.forms[0].elements[cmp].value;
            cmp++;
          }
      ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>'+campo,'incluirParticipanteLicitacao');
      document.frm.action                         = '<?=$pgProc;?>?<?=Sessao::getId();?>';
      document.frm.stCtrl.value                   = "";
    }
}

function Limpar(){
     executaFuncaoAjax('limpar');
}
</script>
