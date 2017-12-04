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
/**
    * Página JavaScript de Ordem de Compra
    * Data de Criação   : 06/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    $Id: JSManterOrdemCompra.js 65194 2016-05-02 13:27:34Z evandro $

*/
</script>
<script>
    function buscaDado( BuscaDado ){
        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = BuscaDado;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }

    function limparFiltro(){
        document.frm.reset();
        passaItem('document.frm.inCodEntidade','document.frm.inCodEntidadeDisponivel','tudo');
    }

    function incluirItem( idItem, linha_table_tree) {
        TableTreeLineControl( linha_table_tree , 'none', '', 'none');

        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = 'incluirItem';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&idItem='+idItem;
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }

    function limpaItem( idItem, boMarcaCentro, boLimparCentroCusto ) {
        if (boMarcaCentro == 't') {
            document.getElementById('inCodItem'+idItem).value = '';
            document.getElementById('stNomItem'+idItem).innerHTML = '&nbsp;';
        }
        if (boLimparCentroCusto == 't') {
            document.getElementById('inCodCentroCusto'+idItem).value = '';
            document.getElementById('stNomCentroCusto'+idItem).innerHTML = '&nbsp;';
        }
        document.getElementById('inMarca'+idItem).value = '';
        document.getElementById('stNomMarca'+idItem).innerHTML = '&nbsp;';

        var stTarget = document.frm.target;
        var stAction = document.frm.action;
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = 'excluirItem';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&idItem='+idItem;
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }
</script>