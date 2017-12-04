<?php
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
?>
<?php
/**
 * Javascript que valida inclusão e alteração de Ação
 * Data de Criação: 04/08/2007

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: JSManterAcao.php 64234 2015-12-21 17:24:45Z michel $

 * Casos de uso: uc-02.09.04
 */
?>

<script type="text/javascript">

jq(document).ready(function () {
    var verificador = '';
    jq('#inCodPPATxt').change(function () {
        if (verificador != '') {
            if (confirm('Se Você trocar o PPA agora irá apagar todas as informações da tela. Deseja prosseguir?')) {
                Limpar(false);
                jq.post('OCManterAcao.php', {'stCtrl':'verificaArrendondarValor', 'inCodPPA':this.value}, '', 'script');
                jq.post('OCManterAcao.php', {'stCtrl':'mostrarRecurso', 'inCodPPA':this.value}, '', 'script');
            } else {
                jq('#inCodPPATxt').val(verificador);
                jq('#inCodPPA').val(verificador);
            }

        } else {
            jq.post('OCManterAcao.php', {'stCtrl':'verificaArrendondarValor', 'inCodPPA':this.value}, '', 'script');
            jq.post('OCManterAcao.php', {'stCtrl':'mostrarRecurso', 'inCodPPA':this.value}, '', 'script');
        }
        verificador = jq('#inCodPPATxt').val();
    });
    jq('#inCodPPA').change(function () {jq('#inCodPPATxt').change()});

    if (jq('#inCodPPA').val() != '') {
        jq('#inCodPPA').trigger('change');
    }

    jq('#inCodRegiao').change(function () {
        jq.post( 'OCManterAcao.php'
              , {'stCtrl':'buscaDescricaoRegiao',
                 'inCodRegiao':jq('#inCodRegiao').val()
                }
              , ''
              , 'script');
    });
    if (jq('#inCodRegiao').val() != '') {
        jq('#inCodRegiao').trigger('change');
    }
});

var inTmpCodPPA = '';
var inTmpNumPrograma = '';

function existeReceita()
{
    if (document.frm.inSizeRecurso && document.frm.inSizeRecurso.value > 0) {
        return true;
    }

    return false;
}

function validarAcao()
{
    Salvar();
}

function atualizarAcao()
{
    montaParametrosGET('atualizarAcao', null, true);
}

/* Atualiza o PPA do formulário */
function confirmaAtualizaPPA(boSalvos)
{
    if (boSalvos) {
        // Restaura o valor novo de PPA.
        document.frm.inCodPPATxt.value = inTmpCodPPA;
    }

    // Atualiza valor guardado de PPA.
    $('inCodPPATmp').value = document.frm.inCodPPATxt.value;

    atualizarPPA();
}

function perguntaAtualizaPPA()
{
    // Captura novo valor de PPA.
    inTmpCodPPA = document.frm.inCodPPATxt.value;

    // Restaura valor anterior de PPA.
    document.frm.inCodPPATxt.value = $('inCodPPATmp').value;

    if (document.frm.inNumPrograma) {
        // Captura novo valor de Programa.
        inTmpNumPrograma = document.frm.inNumPrograma.value;

        // Restaura valor anterior de Programa.
        document.frm.inNumPrograma.value = $('inNumProgramaTmp').value;
    }

    // Pergunta se confirma a atualização.
    confirmPopUp('Alterando o PPA atual', 'Alterar o PPA agora pode mudar o tipo ' +
            ' de vinculação de recurso. Confirma apagar a lista de recursos?',
            'confirmaAtualizaPPA(true);');
}

function atualizaPPA()
{
    if (existeReceita()) {
        perguntaAtualizaPPA();
    } else {
        confirmaAtualizaPPA(false);
    }
}

function atualizarPrograma()
{
    montaParametrosGET('atualizarPrograma', null, true);
}

/* Atualiza o Programa do formulário */
function confirmaAtualizaPrograma(boSalvos)
{
    if (boSalvos) {
        // Restaura os valores novos.
        document.frm.inCodPPATxt.value = inTmpCodPPA;
        document.frm.inNumPrograma.value = inTmpNumPrograma;
    }

    // Atualiza valores guardados.
    $('inCodPPATmp').value = document.frm.inCodPPATxt.value;
    $('inNumProgramaTmp').value = document.frm.inNumPrograma.value;

    atualizarPrograma();
}

function perguntaAtualizaPrograma()
{
    // Captura os novos valores.
    inTmpCodPPA = document.frm.inCodPPATxt.value;
    inTmpNumPrograma = document.frm.inNumPrograma.value;

    // Restaura valores anteriores.
    document.frm.inCodPPATxt.value = $('inCodPPATmp').value;
    document.frm.inNumPrograma.value = $('inNumProgramaTmp').value;

    // Pergunta se confirma a atualização.
    confirmPopUp('Alterando o PPA atual', 'Alterar o PPA agora pode mudar o tipo ' +
            ' de vinculação de recurso. Confirma apagar a lista de recursos?',
            'confirmaAtualizaPrograma(true);');
}

function atualizaPrograma()
{
    if (existeReceita()) {
        perguntaAtualizaPrograma();
    } else {
        confirmaAtualizaPrograma(false);
    }
}

function limpaFormularioExtra()
{
    montaParametrosGET('limparFormulario');
}

function atualizaDadosRecursos()
{
    montaParametrosGET('atualizarDadosRecursos', null, true);
}

function cancelarAcao()
{
    <?php $stLink = $pgList.'?'.Sessao::getId(); ?>

    mudaTelaPrincipal('<?= $stLink ?>');
}

function formataRecurso()
{
    jq('#spnRecurso input[id^=\'flValorAno\']').each(function () {
        formataArrendondamentoValor(this);
    });
}

function incluirRecurso()
{
    montaParametrosGET('incluirRecurso', null, true);
}

function excluirRecurso(stCodAcao, stCodRecurso)
{
    jq.post('OCManterAcao.php', {'stCtrl':'excluirRecurso', 'cod_acao':stCodAcao, 'cod_recurso':stCodRecurso}, '', 'script');
}

function limparUnidade()
{
    jq("[name='stUnidadeOrcamentaria']").val('');
    jq("[name='inMontaCodOrgaoM']").selectOptions('', true);
    jq("[name='inMontaCodUnidadeM']").removeOption(/./);
    jq("[name='inMontaCodUnidadeM']").addOption('','Selecione');
}

function limparRecurso()
{
    montaParametrosGET('limparRecurso');
}

function atualizaDadosPrograma()
{
    montaParametrosGET('atualizarDadosPrograma', null, true);
}

function atualiza()
{
    montaParametrosGET('atualizar');
}

function buscaValor(stTipoBusca)
{
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = stTipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();

    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function atualizaContrato()
{
    $('inContrato').value = document.frm.inRegistro.value;
    $('inContrato').blur();
}

// Componente Unidade Responsável chama esta função.
function retornaPagina() {}

function retornarConsulta()
{
<?php
    $link = Sessao::read( "link" );
    $stLink = 'stAcao=consultar&pg=' . $link['pg'] . '&pos=' . $link['pos'];
?>
    mudaTelaPrincipal('<?= 'LSManterAcao.php?' . Sessao::getId() . '&' . $stLink; ?>');
}

function atualizaServidor(valor)
{
    var stCaminho;
    stCaminho  = '<?php echo CAM_GRH_PES_PROCESSAMENTO; ?>OCFiltroCGM.php';
    stCaminho += '?<?php echo Sessao::getId(); ?>';
    stCaminho += '&inContrato=' + valor;
    stCaminho += '&stSituacao=ativos';

    ajaxJavaScript(stCaminho, 'preencheCGMContrato');
}

function Limpar(boNaoLimparPPA)
{
    if (boNaoLimparPPA) {
        jq('#inCodPPATxt').val('');
        jq('#inCodPPA').selectOptions('',true);
    }
    jq('#boArredondar').val('');

    jq('#inCodMacroObjetivoTxt').val('');
    jq('#inCodMacroObjetivo').selectOptions('', true);

    jq('#inCodProgramaSetorialTxt').val('');
    jq('#inCodProgramaSetorial').selectOptions('', true);

    jq('#inCodPrograma').val('');
    jq('#stNomPrograma').html('&nbsp;');

    jq('#inCodTipo').val('');
    jq('#spnPeriodo').html('');
    jq('#spnDescricaoRegiao').html('');

    jq('#slNaturezaDespesaCorrente').attr('checked', false);
    jq('#slNaturezaDespesaCapital').attr('checked', false);

    jq('select[name=\'inCodFuncao\']', jq('#spnOrcamentaria')).val('');
    jq('select[name=\'inCodSubFuncao\']', jq('#spnOrcamentaria')).val('');

    jq('#inCodAcao').val('');
    jq('#stTitulo').val('');
    jq('#stFinalidade').val('');
    jq('#stDescricao').val('');
    jq('#stDetalhamento').val('');

    jq('#slFormaImplementacao').selectOptions('',true);

    jq('#inCodRegiao').val('');
    jq('#stNomRegiao').html('&nbsp;');
    jq('#inCodProduto').val('');
    jq('#stNomProduto').html('&nbsp;');
    jq('#spnProduto').html('');

    jq('#inCodNorma').val('');
    jq('#stNorma').html('&nbsp;');

    jq('#slTipoOrcamento').selectOptions('', true);

    jq('#spnUnidade').html('');

    jq('#inCodFuncao').val('');
    jq('#stCodFuncao').html('&nbsp;');

    jq('#inCodSubFuncao').val('');
    jq('#stCodSubFuncao').html('&nbsp;');

    jq('#spnRecurso').html('');
    jq('#spnListaRecurso').html('');

    jq('#stUnidadeMedida').selectOptions('',true);

    jq("[name='stUnidadeOrcamentaria']").val('');
    jq("[name='inMontaCodOrgaoM']").selectOptions('', true);
    jq("[name='inMontaCodUnidadeM']").removeOption(/./);
    jq("[name='inMontaCodUnidadeM']").addOption('','Selecione');

    jq('#spnMetaFisica input').each(function () {
                                        jq(this).val('0,00');
                                        jq('#' + this.id + '_label').html('0,00');
                                    })
    jq.post('OCManterAcao.php', {'stCtrl':'limparSessaoParametrosMetas'});
}

/**
 * Método que retira os labels dos inputs de valores para que eles possam ser preenchidos
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function montaMetaFisica(inCodRecurso)
{
    // Remove a primeira coluna da tabela de metas fisicas, onde se encontra os números das linhas
    var stHtml = jq('#tblMetaFisica_'+inCodRecurso);
    jq('colgroup>col:first', stHtml).each(function () {
        jq(this).remove();
    });
    jq('tbody', stHtml).each(function () {
        jq('tr td[id$=\'cell_1\'], th[id$=\'header_0\']', this).each(function () {
            jq(this).remove();
        });
    });

    // Libera os inputs das quantidades
    var inLinha = jq('#tblMetaFisica_'+inCodRecurso).parent().parent().attr('id').replace('obTblRecursos_row_', '').replace('_sub', '');
    stHml = jq('#tblMetaFisica_'+inCodRecurso);
    jq('input[id*=\'flQuantidade\']', stHml).each( function () {
        var arId = this.id.split('_');
        if (arId[1] != 'total') {
            if (jq('#arValorAno'+arId[1]+'_'+inLinha).css('display') != 'none') {
                setLabel(this.id, true);
                jq(this).removeAttr('style').css('text-align', 'right');
            }
        }
    });

    // Atribui os valores dos totais dos anos de acordo com os totais informados na inclusao do recurso
    jq('span[id^=\'flValorTotal\']', stHtml).each( function () {
        // pega o numero da linha do objeto
        arObjId   = jq(this).attr('id').split('_');
        if (arObjId[1] == 'total') {
            arObjId[1] = 5;
        }
        // pega o nome dos inputs de acordo com o nome do label
        stInputId = jq(this).attr('id').replace('_label', '');

        // pega o id da coluna (na tabletree de recurso e nao da metafisica) para poder saber qual a linha que ele se encontra na tabletree para
        // conseguir montar o nome do input que tem o valor necessário para ser pego.
        arColunaId = jq(this).parent().parent().parent().parent().parent().parent().attr('id').split('_');
        flValorTotalLinha = jq('#arValorAno'+arObjId[1]+'_'+arColunaId[2]).val();

        jq(this).html(flValorTotalLinha);
        jq('#'+stInputId).val(flValorTotalLinha);
    });
}

/**
 * Realiza o processo de formatacao do campo para que ele possa arredondar corretamente o valor que recebera, retirando o que vem por default
 * do evento onBlur e adicionando o metodo de arredondar no change, isso se estiver configurado no ppa para arredondar. Alem disso é visto se o
 * segundo parametro é passado for 'listarecurso', deve ser feito a totalizacao do recurso
 *
 * @author  Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param   objeto  O campo que sera formatado
 * @param   string  parametro para verificar se deve ser calculado a totalizacao do recurso
 *
 * @returns void
 */
function formataArrendondamentoValor(obj, stParam)
{
    jq(obj).removeAttr('onblur');
    jq(obj).blur(function () {
        if (jq('#boArredondar').val() != '') {
            arredondaValor(this);
        } else {
            floatDecimal(this, '2', KeyboardEvent);
        }
        if (stParam == 'listarecurso') {
            totalizaRecurso();
        }
    });
}

/**
 * Monta uma string onde repete o numero do segundo parametro de vezes o que e passado no primeiro parametro
 *
 * @author  Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param   string   o que deseja que seja repetido
 * @param   integer  quantas vezes deseja que o primeiro parametro seja repetido
 *
 * @returns string
 */
function repeat(stValue, inNumber)
{
    var arValue = [];
    while (arValue.length < inNumber) {
        arValue.push(stValue);
    }

    return arValue.join('');
}

/**
 * Arredonda o valor do campo passado por referencia, isso se o PPA estiver configurado para isso.
 * É verificado se o valor deve ser arredondado para mais ou para menos, onde um valor que tenha ,55 arredonda para menos enquanto ,56 para mais
 *
 * @author  Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param   objeto  Passado o objeto do campo onde deve ser arredondado o valor
 *
 * @returns void
 */
function arredondaValor(obj)
{
    var flValue = formataValor(obj.value);
    var arValue = flValue.toString().split('.');

    var flReturn = arValue[0];

    if (arValue.length > 1) {
        var inCount = 2;
        var inLength = parseInt(arValue[1].length);

        if (inLength < inCount) {
            // Coloca zeros para preencher o valor se estiver incompleto. Ex: É digitado 5,6. Logo o 6 deve ficar 60
            arValue[1] = arValue[1].toString()+repeat('0', (inCount-inLength));
        } else {
            inCount = inLength;
        }
        // Cria o parametro de que deve ser maior que 55. Ex: 56, 566, 5666
        var inParametro = parseInt('5'+repeat('6', (inCount-1)));
        if (parseInt(arValue[1]) >= inParametro) {
            flReturn++;
        }
    }

    obj.value = flReturn;
    floatDecimal(obj, '2', null);
}

/**
 * Formata um valor BR em US para poder ser usado em calculos
 *
 * @author  Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param   string  Um valor que está formatado em BR é passado para o padrão americano para poder ser usado em calculos
 *
 * @returns float
 */
function formataValor(stValor)
{
    return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
}

/**
 * Quando mudar algum valor da quantidade, ele é reponsavel por alterar os valores dos labels da tabela de meta fisica, calculado o valor pelo
 * valor total, que ja está na listagem, o valor total da quantidade, o valor total dos valores e o valor total das metas fisicas
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function somaValorMetaFisica(obj)
{
    /*
     * posicao 0 = descricao
     * posicao 1 = linha
     * posicao 2 = codigo recurso
     */
    var arObjId = jq(obj).attr('id').split('_');

    // O valor total da linha (ou ano de referencia) será o valor adicionado no input referente ao ano da tabela de recurso
    var flValorTotalLinha = formataValor(jq('#flValorTotal_'+arObjId[1]+'_'+arObjId[2]).val());
    var flValorLinha = flValorTotalLinha / formataValor(jq(obj).val());

    var flValorTotal = 0;
    var flQuantidadeTotal = 0;
    var flTotalizador = 0;
    for (inCount = 1; inCount <= 4; ++inCount) {

        if (jq('#flQuantidade_' + inCount + '_' + arObjId[2]).val() == '') {
            jq('#flQuantidade_' + inCount + '_' + arObjId[2]).val('0,00');
        }

        var flValor = formataValor(jq('#flValorTotal_' + inCount + '_' + arObjId[2]).val());
        var flQuantidade = formataValor(jq('#flQuantidade_' + inCount + '_' + arObjId[2]).val());
        var flTotal =  flValor;

        flQuantidadeTotal = flQuantidadeTotal + flQuantidade;

        jq('#flQuantidade_total' + '_' + arObjId[2]).val(flQuantidadeTotal);
        jq('#flQuantidade_total_' + arObjId[2] + '_label').html(retornaFormatoMonetario(flQuantidadeTotal,true));
    }
}

/**
 * Prepara os inputs da lista de recurso para que possam arredondar os valores, caso o ppa tenha isso configurado
 * e prepara que no evento change dos inputs ele pegue o novo valor e atribua os totais das linhas da tabela de metafisica de cada recurso
 * alem de recalcular os valores pelas quantidades
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function formataListaRecurso()
{
    var inCount = 1;
    jq('input[id*=\'arValorAno\']', jq('#obTblRecursos tbody>tr[name*=\'obTblRecursos_row_\']')).each(function () {

        // Formata o campo para que arredonde o valor quando o ppa selecionado estiver configurado para isso
        formataArrendondamentoValor(this, 'listarecurso');

        // Ao alterar o valor, deve alterar o valor do total na linha da metafisica
        jq(this).blur( function () {
            var stObjId = jq(this).attr('id').split('_');
            var inCampo = stObjId[0].replace('arValorAno', '');
            var stLinhaId = '#'+jq(this).parent().parent().attr('id')+'_sub';

            var inCodRecurso;
            jq('table', jq(stLinhaId)).each( function () {
                inCodRecurso = jq(this).attr('id').replace('tblMetaFisica_', '');
            });

            stObjIdMetaFisica = '#flValorTotal_'+inCampo+'_'+inCodRecurso;
            jq(stObjIdMetaFisica).val(jq(this).val());
            jq(stObjIdMetaFisica+'_label').html(jq(this).val());

            var objQuantidade = jq('#flQuantidade_'+inCampo+'_'+inCodRecurso);

            if (objQuantidade.val() != undefined && formataValor(objQuantidade.val()) > 0) {
                somaValorMetaFisica(objQuantidade);
            }
        });
    });
}

/**
 * Cada alteração de valor nos inputs da tabletree de recursos, ele recalcula os valores da linha e total da tabela
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function totalizaRecurso()
{
    var inCount = 1;
    var flTotal = 0;
    var flTotalizador = [];
    var flTotalizadorGeral = 0;
    var flValorCampo = 0;
    flTotalizador[1] = 0;
    flTotalizador[2] = 0;
    flTotalizador[3] = 0;
    flTotalizador[4] = 0;

    // Percorre as linhas principais da table tree, sendo as que possuem os inputs com valores
    jq('#obTblRecursos tbody>tr[name*=\'obTblRecursos_row_\']').each(function () {
        if (jq(this).attr('class') != '_sub') {
            for (inCampo = 1; inCampo <= 4; inCampo++) {
                flValorCampo = formataValor(jq('#arValorAno' + inCampo + '_' + inCount).val());
                flTotal = flTotal + flValorCampo;
                flTotalizador[inCampo] = flTotalizador[inCampo] + flValorCampo;
            }
            jq('#arValorAno5_label_' + inCount).html(retornaFormatoMonetario(flTotal,true));
            jq('#arValorAno5_' + inCount).val(retornaFormatoMonetario(flTotal,true));
            flTotalizadorGeral = flTotalizadorGeral + flTotal;
            flTotal = 0;
            inCount++;
        }
    });

    for (inCount = 1; inCount <=4; inCount++) {
        jq('#arValorAno' + inCount + '_totalizador_label').html(retornaFormatoMonetario(flTotalizador[inCount],true));
        jq('#arValorAno' + inCount + '_totalizador').val(flTotalizador[inCount]);
    }
    jq('#arValorAno5_totalizador_label').html(retornaFormatoMonetario(flTotalizadorGeral,true));
    jq('#arValorAno5_totalizador').val(flTotalizadorGeral);
}

/**
 * Monta a linha de totalizador na tabletree de recurso
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function montaTotalizadorRecurso()
{
    stHtml = jq('#obTblRecursos tbody:last');
    stHtml.append('<tr id=\'obTblRecursos_row_total\'></tr>');

    // Arruma os ids dos inputs da tabela, pois a tabletree não concatena o numero da linha nos ids dos componentes, o que acaba gerado
    // componentes com mesmo nome em linhas diferentes, alem de alinhar os numeros para a direita
    jq('tr td input', stHtml).each(function () {
        //pega o id da linha e explode para poder pegar o numero da linha
        arRowId = jq(this).parent().parent().attr('id').split('_');
        jq(this).attr('id', jq(this).attr('id')+'_'+arRowId[2]);
        jq(this).css('text-align', 'right');
    });

    // Faz exatamente a mesma coisa que acima, porém realiza isso nos totalizadores, que estão dentro de spans
    jq('tr td span[id*=\'label\']', stHtml).each(function () {
        arRowId = jq(this).parent().parent().attr('id').split('_');
        jq(this).attr('id', jq(this).attr('id')+'_'+arRowId[2]);
        jq(this).css('text-align', 'right');
    });

    var inCount = 1;
    var inCountInterno = 1;
    var stAppend;

    //Percorre as colunas da primeira linha para usar como base para montar a ultima linha de totalizadores
    jq('tr:first td', stHtml).each(function () {
        stAppend  = '<td id=\'obTblRecursos_row_total_cell_'+inCount+'\'>';
        if (inCount > 3 && inCount < 9) {
            stAppend += '<span class=\'totalizador\' id="arValorAno'+inCountInterno+'_totalizador_label">&nbsp;</span>';
            stAppend += '<input type="hidden" id="arValorAno'+inCountInterno+'_totalizador" name="arValorAno'+inCountInterno+'_totalizador" value="0" />';
            inCountInterno++;
        } else {
            stAppend += '&nbsp;';
        }
        stAppend += '</td>';
        jq('#obTblRecursos_row_total').append(stAppend);
        inCount++;
    })

    // Alinha os totalizadores a direita e os coloca em negrito
    jq('#obTblRecursos_row_total span[class=\'totalizador\']').css('text-align', 'right').css('font-weight', 'bold');
    totalizaRecurso();
}

/**
 * Método responsável por formatar o atributo 'name' dos inputs da listagem de recursos para se enquadrar a lógica usada
 * para salvar os dados. Era usado antes uma Lista e foi mudado para Table, o que mudou a forma de montagem do name
 * É usado um array com os valores de cada ano e a table não permite criar os names como array
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function formatTableInputName()
{
    var stName;
    var arName;
    // pega a parte da tabela onde se encontram os inputs
    stHtml = jq('#obTblRecursos tbody:last');

    // percorre todos inputs necessários na parte específica
    jq('input[name*=\'arValorAno\']', stHtml).each(function () {
        // pega-se o name atual e o formata como precisa
        // Como é: 'arValorAno1_1' e deve ficar 'arValorAno1[0]'
        stName = jq(this).attr('name');
        arName = stName.split('_');
        stName = arName[0]+'['+(arName[1]-1)+']';
        jq(this).attr('name', stName);
    });
}

/**
 * Método responsável por formatar o sinal '+' que fica na tabletree para que não seja necessário carregar os dados lá da visao cada vez que for
 * querer verificar os dados (caso ele já tenha aberto a aba alguma vez). Esse método serve para evitar que seja recarregado os inputs e se perca
 * os valores inseridos pelo usuário
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function formatTableTree()
{
    jq('a[id*=\'menos\']', jq('#obTblRecursos tbody:last tr')).each(function () {
        jq(this).click(function () {
            obMais  = jq('#'+this.id.replace('menos', 'mais'));
            jq(obMais).removeAttr('onclick').click(function () {
                obSub   = jq('#'+this.id.replace('mais', 'sub'));
                obMenos = jq('#'+this.id.replace('mais', 'menos'));
                jq(obSub)  .css('display', '');
                jq(obMenos).css('display', '');
                jq(this)   .css('display', 'none');
            });
        });
    });
}

/**
 * Método responsável por formatar os inputs da tabela de recursos para que eles não fiquem como label, a não ser que o ano referente ao label
 * não esteja vinculado a uma acao da ldo (ldo.acao_vinculada)
 * Esse metodo recebe como parametro um array json contendo o recurso como chave e quais anos que já foram validados
 * Então verifica-se quais anos não estão naquele array de anos vinculados e libera os inputs, deixando apenas em label os anos que foram vinculados
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param  json jsonAcaoValidada Contém os anos que devem ficar como label
 *
 * @returns void
 */
function formatAnosAcaoValidada(jsonAcaoValidada)
{
    // pega todos os recursos para saber a linha correspondente deles (chave o array)
    var arCodRecurso = [];
    jq('#spnListaRecurso input[id^=\'arCodRecurso\']').each(function () {
        arCodRecurso[arCodRecurso.length] = lpad(this.value, 4, "0");
    });

    // pega e transforma a string json em um objeto
    var arDados = jsonAcaoValidada.evalJSON();

    // percorre o objeto
    for (chave in arCodRecurso) {
        if (!isNaN(parseInt(chave))) {
            // um array para representar os 4 anos
            var arAnos = [1,2,3,4];

            // Verifica se não dará nenhum erro, se der é pq não existe setado a chave passada para o array arDados, logo isso quer dizer que
            // deve tirar o label de todos os inputs
            try {
                // como dentro de cada posição existe m array, é percorrido ele e troca as posicoes que encontrar por zero, para que possa ser retirado
                // logo na linha de baixo, restando assim apenas os anos que não devem ficar como labels, pois eles não foram validados ainda na ldo
                arDados[arCodRecurso[chave]].each( function (valor) {
                    arAnos.splice((valor-1), 1, 0);
                });
                arAnos = arAnos.toString().replace(/0,/g, '').split(',');
            } catch (e) {
                console.log(e);
            }

            inLinha = parseInt(array_search(arCodRecurso[chave], arCodRecurso))+1;

            // percorre-se os anos que devem se tornar inputs
            var inCount = 0;
            for (var ano in arAnos) {
                if (!isNaN(parseInt(arAnos[ano]))) {
                    inCount++;
                    jq('#arValorAno'+arAnos[ano]+'_'+inLinha).css('display', 'block');
                    jq('#arValorAno'+arAnos[ano]+'_label_'+inLinha).css('display', 'none');
                }
            }

            // Regra atualizada no arquivo VPPAManterAcao.class.php
        }
    }
}

/**
 * Método responsável verificar se um determinado valor está no array
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param  string   valor a ser pesquisado no array
 * @param  array    o array onde deve ser pesquisado o valor
 *
 * @returns mixed (retorna a chave do array, mas caso nao encontre o valor, retorna false)
 */
function array_search(busca, oarray)
{
    for (var i in oarray) {
        if (oarray[i]==busca) {
            return i;
        }
    }

    return false;
}

/**
 * Método responsável por chamar na ordem os métodos para formatar corretamente a lista de recurso
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @returns void
 */
function formatListaRecurso()
{
    formataListaRecurso();
    formatTableInputName();
    montaTotalizadorRecurso();
    formatTableTree();
}

/**
 * Método responsável por adicionar valores a esquerda de uma string
 *
 * @author Franver Sarmento de Moraes <franver.moraes@cnm.org.br>
 * @param string  valor a ser modificado
 * @param inteiro tamanho de caracteres
 * @param string  valor que será adicionado à esquerda da string original
 *
 * @returns retorna a string original já modificada.
 */
function lpad(strOriginal, inLength, strToPad)
{
    while (strOriginal.length < inLength)
        strOriginal = strToPad + strOriginal;

    return strOriginal;
}

</script>
