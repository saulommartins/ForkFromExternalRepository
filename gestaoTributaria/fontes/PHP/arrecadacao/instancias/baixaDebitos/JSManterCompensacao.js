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
  * Página de JavaScript para Compensação  de Pagamentos
  * Data de criação : 08/06/2005


  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: JSManterCompensacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.03.10
*/

/*
$Log$
*/

?>

<script type="text/javascript">
    function Cancelar () {
        <?php
            $link = Sessao::read( "link" );
            $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."";
        ?>

        mudaTelaPrincipal("<?=$pgFilt.'?'.Sessao::getId().$stLink;?>");
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

    function validarLista(){
        var cont = 0;
        var selecionado = 0;
    
        while(cont < document.frm.elements.length){
            var namee = document.frm.elements[cont].name;
            if ( ( document.frm.elements[cont].type == 'checkbox' ) && ( namee.match('boParVenc') ) ) {
                if ( document.frm.elements[cont].checked ) {
                    selecionado = 1;
                    break;
                }
            }
    
            cont++;
        }
    
        if ( !selecionado ) {
            alertaAviso("Erro! Nenhum registro de parcela a vencer foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
        }else {
            if ( document.frm.boCompensar.value == 0 ) {
                alertaAviso("Erro! Nenhuma registro de parcela a compensar foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
            }else
            if( Valida() ){
                document.frm.stCtrl.value = "PRManterCompensacao.php";
                document.frm.action = '<?=$pgProc.'?'.Sessao::getId().$stLink;?>';
                document.frm.submit();
            }
        }
    }

</script>
