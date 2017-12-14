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
    * Página de Formulario para Requisição
    * Data de criação : 26/01/2006


    * @author Analista: Diego Victoria
    * @author Programador: tonismar R. Bernardo

    * @ignore

    Caso de uso: uc-03.03.10
    
    $Id: JSManterRequisicao.js 59612 2014-09-02 12:00:51Z gelson $
    
    */
                   
?>

<script type="text/javascript">

function buscaValor(valor){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = valor;
    document.frm.target = 'oculto';
    document.frm.action = 'OCManterRequisicao.php?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirItem( inIndice){    
    executaFuncaoAjax('excluirItem','<?=Sessao::getId();?>&id='+inIndice);
}

function excluirItemAnulacao( inIndice){    
    executaFuncaoAjax('excluirItemAnulacao','<?=Sessao::getId();?>&id='+inIndice);
}

function limpaFormularioItemExtra(){
    executaFuncaoAjax('limpaItem');
}

function Limpar(){
    executaFuncaoAjax('limpaTotal');
}

// Função que valida se o almoxarifado está setado, caso sim, busca o ítem.
function validaAlmoxarifado()
{
    if (jQuery('#inCodAlmoxarifado').val() == ''){
        jQuery('#inCodItem').val('');
        jQuery('#stUnidadeMedida').val('&nbsp;');
        jQuery('#stNomItem').val('&nbsp;');
        alertaAviso('Selecione o Almoxarifado.','form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }else
        return true;
}

function validaItem()
{
    if (jQuery('#inCodItem').val() == ''){
        return false;
    } else {
        return true;
    }
}

</script>
