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
    * Include JavaScript para Manter Cancelamento
    * Data de Criação   : 26/07/2007


    * @author Desenvolvedor: Fernando Piccini Cercato

    * @supackage Regras
    * @package Urbem

    * $Id: JSManterCancelamento.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.1  2007/07/27 13:16:25  cercato
Bug#9762#

*/

?>
<script type="text/javascript">

function Salvar(){
    var mensagem = "";
    var erro;
    if( Valida() ){

        if( document.frm.stCreditosRef.value == 'da' ){
            if( document.frm.inCodInscricao.value == '' ){
                erro = true;
                mensagem += "@Campo Incrição/Ano inválido!()";
            }
        }

        if( document.frm.stCreditosRef.value == 'cgm' ){
            if( document.frm.inCodContribuinte.value == '' ){
                erro = true;
                mensagem += "@Campo Contribuinte inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }
        if( document.frm.stCreditosRef.value == 'ii' ){
            if( document.frm.inInscricaoImobiliaria.value == '' ){
                erro = true;
                mensagem += "@Campo Inscrição Imobiliária inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }
        if( document.frm.stCreditosRef.value == 'ie' ){
            if( document.frm.inInscricaoEconomica.value == '' ){
                erro = true;
                mensagem += "@Campo Inscrição Econômica inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }

        if( erro ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else {
            document.frm.submit();
        }
    }
}

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}


function Limpar(){
   <? Sessao::write( 'listaCGM', array() ); ?> 
   limpaFormulario();
   document.frm.reset();
}

function Cancelar(){
<?php
    $link = Sessao::read( 'link' );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgForm.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function excluirCGM( inIndice1 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'ExcluirCGM';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

</script>

