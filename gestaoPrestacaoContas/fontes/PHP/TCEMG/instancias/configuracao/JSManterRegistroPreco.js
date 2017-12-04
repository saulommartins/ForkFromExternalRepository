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
    *                        
    **********************************************************************************                                                           
*/
</script>
<?php
/**
  * Página de JavaScript de Configuração de Registro de Preços
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: JSManterRegistroPreco.js 63765 2015-10-07 18:51:47Z michel $
  *
*/
?>
<script type="text/javascript">
function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function alterarItem() {
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.stCtrl.value = 'alterarListaItens';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.btnSalvar.value = 'Incluir Item';
    document.frm.stCtrl.value = 'incluirListaItens';
    jQuery('input[name=inCodItem]').removeAttr('readonly');
    jQuery('#imgBuscar').css('visibility', '');
}

function alterarOrgao() {
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.stCtrl.value = 'alterarListaOrgaos';
    document.frm.submit();
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.btnSalvarOrgao.value = 'Incluir Orgão';
    document.frm.stCtrl.value = 'incluirListaOrgaos';
}

/**
* Faz o chamada de alerta de Questão.
*/
function alertaQuestaoValor(pagina,tipo,sessao,valorEnt){
    var x = 350;
    var y = 200;
    if (typeof valorEnt == 'undefined') {
        valorEnt = "&chave=1&valor=teste";
    }
    var sessaoid = sessao.substr(15,8);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/alerta/alerta.php?'+sessaoid+'&tipo='+tipo+'&chamada=sn'+valorEnt+'&pagQuestao='+pagina;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+sessaoid+"','width=400px,height=230px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

function ValidaRegistroPreco()
{
    if( Valida() ){
        var erro = false;
        var mensagem = "";

        if (document.getElementById('boResponsavelOrgao').value=='false') {
            erro = true;
            mensagem += "@Preencha o campo CGM do Responsável da Lista de Orgãos!";
        }

        if( erro ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
        else{
            BloqueiaFrames(true,false);
            Salvar();
        }
    }
}

</script>