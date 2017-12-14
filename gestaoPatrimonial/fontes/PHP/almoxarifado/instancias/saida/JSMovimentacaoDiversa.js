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
<script>
function validaUsuarioSecundario (clickForm) {
    BloqueiaFrames(true,false);

    if (document.frm.stAcao.value == 'saida') {
        window.open('../../popups/saida/FMValidaUsuario.php?clickForm='+clickForm,'','width=500px,height=400px,scrollbars=1');
    } else {
        document.frm.Ok.onClick = clickForm;
        document.frm.getElementById('Ok').click();
    }
}

function buscaValor(valor){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = valor;
    document.frm.target = 'oculto';
    document.frm.action = 'OCMovimentacaoDiversa.php?<?=Sessao::getId();?>';
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

    function Contador(textarea, limit){
    
        var text = textarea.value; 
        var textlength = text.length;
    
        if(textlength > limit){
            textarea.value = text.substr(0,limit);
            return false;
        } else {
            return true;
        }
    }
</script>
