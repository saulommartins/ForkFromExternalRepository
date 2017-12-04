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
    * Arquivo com funcoes JavaScript para Emissão de DOcumentos
    * Data de Criação: 09/06/2005


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: JSEmitirDocumento.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.1  2007/10/09 18:48:59  cercato
 Ticket#9281#

*/

?>

<script type="text/javascript">
function Cancelar () {
<?php
    $link = Sessao::read("link");

    $stLink = "";

    if (!is_null($link) ) {
        if (isset($link["pg"]) && isset($link["pos"])) {
            $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."";
        }
    }

?>
    mudaTelaPrincipal("<?=$pgFilt.'?'.Sessao::getId().$stLink;?>");
}

function submeteFiltro() {
    if (Valida()){
        
        if (jQuery('#emissao_carnes:checked').val() == 'emissao') {
            document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
            document.frm.target = "oculto";
        } else {
            document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
            document.frm.target = "telaPrincipal";
        }

        document.frm.submit();
    }
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
}

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

function buscaContribuinteIndividual(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'buscaContribuinteIndividual';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function selecionarTodos(){
    var cont = 0;

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boSelecionada')) ){
            document.frm.elements[cont].checked = true;
        }
        cont++;
    }
}

function validarListar(){
    var cont = 0;
    var selecionado = 0;

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boSelecionada')) ){
            if ( document.frm.elements[cont].checked ) {
                selecionado = 1;
                break;
            }
        }

        cont++;
    }

    if ( !selecionado ) {
        alertaAviso("Erro! Nenhum registro foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        document.frm.stCtrl.value = "FMEmitirDocumento.php";
        
        <?php
            if (!isset($pgForm)) {
                $pgForm = "";
            }
        ?>
        
        document.frm.action = '<?=$pgForm.'?'.Sessao::getId().$stLink;?>';
        document.frm.submit();
    }
}

function buscaValor(tipoBusca, valor){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;

    if ( document.frm.HdnQual )
        document.frm.HdnQual.value = valor;

    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
