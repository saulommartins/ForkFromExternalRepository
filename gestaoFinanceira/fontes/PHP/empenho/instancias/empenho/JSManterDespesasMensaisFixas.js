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
<?
/**
    * Arquivo JavaScript
    * Data de Criação : 10/11/2006


    * @author Analista:
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-05-28 12:45:48 -0300 (Seg, 28 Mai 2007) $

    * Casos de uso: uc-02.03.30
*/

/**

$Log$
Revision 1.3  2007/05/28 15:45:48  cako
Bug #9177#

Revision 1.2  2006/11/16 20:55:39  gelson
Bug #7349#

Revision 1.1  2006/11/10 22:12:37  cleisson
Bug #7350#


**/

?>

<script type="text/javascript">

function Limpar(  ){
    executaFuncaoAjax('limpar');
/*
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'limpar';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
*/
}

function validaDataEmpenho(){
   var dtUltimoEmpenho   = new Number();
   var dtEmpenho         = new Number();
   var dtEmpenhoCorrente = new Number("<?=Sessao::getExercicio()?>1231");
   var erro              = new Boolean(false);
   var stUltimaData      = new String();

   var empenho = document.frm.stDtEmpenho.value.split("/");
   dtEmpenho   = empenho[2]+empenho[1]+empenho[0];

   ultimoEmpenho = document.frm.dtUltimaDataEmpenho.value.split("-");
   dtUltimaDataEmpenho = ultimoEmpenho[0]+ultimoEmpenho[1]+ultimoEmpenho[2];
   stUltimaData        = ultimoEmpenho[2]+"/"+ultimoEmpenho[1]+"/"+ultimoEmpenho[0];
   dtEmp = stUltimaData;

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
   }
}

</script>
