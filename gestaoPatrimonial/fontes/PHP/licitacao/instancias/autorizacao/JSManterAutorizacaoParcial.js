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
    * Página JavaScript de Licitações para Autorização de Empenho Parcial
    * Data de Criação   : 23/11/2015

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: JSManterAutorizacaoParcial.js 64393 2016-02-11 13:54:53Z arthur $
*/
?>

<script type="text/javascript">

function buscaValor(valor,parametros){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = valor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+parametros;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function selecionaDotacao( Objeto ){

    var f = document.frm;
    var arRetorno;
    
    for( i=0 ; i<f.elements.length ; i++) {			
        if( typeof(f.elements[i]) == 'object' ){
            var idE = new String(f.elements[i].id);
            if( f.elements[i].id != Objeto.id && idE.substring(0,10) == 'rd_dotacao'){
                f.elements[i].checked = false;
            }
        }
    }
    
    // Através do valor vindo da lista, é criado um array de parametros a ser passado por request
    arRetorno = Objeto.value.split(',');
    
    // Retira espaços da string, para quando cod_despesa vier vazio da consulta da lista
    arRetorno[2] = arRetorno[2].replace(/^\s+|\s+$/g,"");
    
    parametro = '&codItem='+arRetorno[0]+'&codCotacao='+arRetorno[1]+'&codDespesa='+arRetorno[2];
    
    executaFuncaoAjax( 'alterarItemDotacao', parametro, true );
}

</script>
