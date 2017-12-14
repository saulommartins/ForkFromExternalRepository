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
    * JavaScript
    * Data de Criação: 21/11/2005


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-09 18:01:18 -0300 (Qui, 09 Ago 2007) $

    * Casos de uso: uc-04.05.25
*/

/*
$Log$
Revision 1.6  2007/08/09 21:01:18  souzadl
Bug#7522#

Revision 1.5  2006/08/08 17:43:36  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = stAction;
}

function alteraDado(stControle, inId){
    stAction = document.frm.action;
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = stAction;
}

function disabledQuebraPagina(){
    obQuebrarPagina = document.frm.boQuebrarPaginaLotacao;
    if( obQuebrarPagina.disabled == true ){
        obQuebrarPagina.disabled = false;
    }else{
        obQuebrarPagina.disabled = true;
    }
}

function disabledQuebraPaginaLocal(){
    obQuebrarPagina = document.frm.boQuebrarPaginaLocal;
    if( obQuebrarPagina.disabled == true ){
        obQuebrarPagina.disabled = false;
    }else{
        obQuebrarPagina.disabled = true;
    }
}

</script>
