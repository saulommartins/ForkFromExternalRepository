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
    * Data de Criação   : 05/10/2006


    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * Casos de uso: uc-03.05.15
    
    $Id: JSManterProcessoLicitatorio.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */ 
?>

<script type="text/javascript">

function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.stCtrl.value = variavel;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar(){
     executaFuncaoAjax('limpar');
}

function habilitaEquipeApoio(flag){
  
  if (flag){
    jQuery('#inCodComissaoApoio').attr('disabled', '');
  } else {
    jQuery('#inCodComissaoApoio').selectOptions('', true);
    jQuery('#inCodComissaoApoio').attr('disabled', 'disabled');
  }

}

function removeMembroAdicional(idMembro){
    ajaxJavaScript('<?=$pgOcul;?>?<?=Sessao::getId();?>&num_cgm='+idMembro+'','excluirMembroAdicional');
}

function recuperaRegimeExecucaoObra(inCodTipoObjeto, inCodRegime){
    ajaxJavaScript('<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodTipoObjeto='+inCodTipoObjeto+'&inCodRegime='+inCodRegime,'recuperaRegimeExecucaoObra');
}

</script>

