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
<script type="text/javascript">

function buscaDado(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function IncluirItem() {
    stCampos  = '&stJustificativa=' + document.getElementById('stJustificativa').value;
    stCampos += '&nuQuantidade='    + document.getElementById('nuQuantidade').value;
    document.frm.stCtrl.value = 'incluirItem';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+stCampos;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
function AlterarItem(inIdItemEstorno) {
    stCampos += '&inIdItemEstorno=' + inIdItemEstorno;
    stCampos += '&stJustificativa=' + document.getElementById('stJustificativa').value;
    stCampos += '&nuQuantidade='    + document.getElementById('nuQuantidade').value;
    document.frm.stCtrl.value = 'alterarItem';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+stCampos;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
function montaAlterarItem ( inIdItem ) {
    var stParametros = "&inIdItem="+inIdItem;
    executaFuncaoAjax( 'montaAlterarItem', stParametros );
}

function excluirItem ( inIdItem ) {
    var stParametros = "&inIdItem="+inIdItem+"&stAcao="+document.frm.stAcao.value;
    executaFuncaoAjax( 'excluirItem', stParametros );
}


</script>
