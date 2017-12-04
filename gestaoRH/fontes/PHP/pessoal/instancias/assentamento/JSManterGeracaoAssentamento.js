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
* Página de javascript do Gerar Assentamento
* Data de Criação   : 19/01/2006


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Id: JSManterGeracaoAssentamento.js 66253 2016-08-02 14:55:30Z michel $

* Casos de uso: uc-04.04.14
*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function validaObrigatorios(){
    var libera = true;

    if(jQuery('#inCodClassificacao').val()=='' || jQuery('#inCodAssentamento').val()==''){
        libera = false;
    }

    if(jQuery('#stDataInicial').val().length<10){
        jQuery('#stDataInicial').val('');
    }

    if(jQuery('#stDataFinal').val().length<10){
        jQuery('#stDataFinal').val('');
    }

    if((jQuery('#stModoGeracao').val()=='contrato'||jQuery('#stModoGeracao').val()=='cgm/contrato') && jQuery('#inContrato').val()==''){
        libera = false;
    }

    if(jQuery('#stModoGeracao').val()=='cargo' && jQuery('#inCodCargo').val()==''){
        libera = false;
    }

    if(jQuery('#stModoGeracao').val()=='lotacao' && jQuery('#inCodLotacao').val()==''){
        libera = false;
    }

    if(libera == true){
        jQuery('#btIncluir').removeAttr('disabled');
    } else {
        jQuery('#btIncluir').attr('disabled', 'disabled');
    }
}

function setBotaoIncluirNorma(){
    document.frm.btIncluirNorma.disabled = false;
}

function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

/**
* Valida o limite de caracteres em campos TextArea utilizando Quebra de linha.
*/

function validaMaxCaracterQuebraLinha(campo,limite,evento,blur){
    var numQuebras = 0;
    try {
        numQuebras = ((campo.value.match(/[^\n]*\n[^\n]*/gi).length));
    } catch(e) {
    }
    return validaMaxCaracter(campo,limite-numQuebras,evento,blur);
}

function preencheClassificacao(cod, combo_type) {
    //limpa o select Classificacao
    jQuery("#inCodClassificacao").find('option').remove().end().append("<option value=''>Selecione</option>");
    jQuery("#inCodClassificacaoTxt").val('');
    //limpa o select Assentamento
    jQuery("#inCodAssentamento").find('option').remove().end().append("<option value=''>Selecione</option>");
    jQuery("#inCodAssentamentoTxt").val('');

    if (cod != '') {
    //busca as informações para preenchimento
        jQuery.ajax({
                    url: "<?php echo $pgOcul; ?>?<?php echo Sessao::getId(); ?>",
                    type: "POST",
                    async: false,
                    datatype: "html",
                    data: { inCod : cod, stCtrl: "preencheClassificacao", combo_type: combo_type },
                            success: function(data) {
                                    data = JSON.parse(data);
                                    jQuery.each(data, function(index, value){
                                        jQuery("#inCodClassificacao").append("<option value='"+value.cod_classificacao+"'>"+value.descricao+"</option>")
                                    });
                    }
        });
    }

    buscaValor('limparArquivosAssentamentoAtual');
}

</script>
