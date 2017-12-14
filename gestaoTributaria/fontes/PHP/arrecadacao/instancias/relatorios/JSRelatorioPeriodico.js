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
  * Página de JavaScript para Relatório Periódico de Arrecadação
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSRelatorioPeriodico.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.1  2007/05/23 19:34:52  dibueno
Bug #9279#

Revision 1.9  2007/05/09 19:56:47  cercato
Bug #9234#

Revision 1.8  2007/02/23 11:45:45  dibueno
Bug #8416#

Revision 1.7  2007/02/22 17:57:59  dibueno
Bug #8416#

Revision 1.6  2007/02/22 15:04:24  dibueno
Bug #8416#

Revision 1.5  2006/09/15 14:47:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;

}

function selecionarTodos(){
    var cont = 0;
    var campoT = document.frm.boTodos.checked;
    if (campoT == true){
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
    else{
        while(cont < document.frm.elements.length){
            if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') ){
                document.frm.elements[cont].checked = !document.frm.elements[cont].checked;
            }
            cont++;
        }
    }
}

function Cancelar () {
<?php
    //$link = Sessao::read( "link" );
 //   $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&boVinculoEdificacao=".$_REQUEST['boVinculoEdificacao']."";
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgFilt.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function submeteFiltro(){

    if( Valida() ){

        dtDataInicial   = document.frm.dtInicio.value;
        dtDataFinal     = document.frm.dtFinal.value;

        if ( !dtDataInicial && !dtDataFinal ) {
            mensagem = "Campos de intervalo de data não foram preenchidos!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else {
            document.frm.submit();
        }
    } else {
        //alert ('nao');
    }
}


</script>