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
 * JavaScript para o validação do Formulario de Inclusao/Alteracao de Receita
 *
 * Data de Criação   : 24/09/2008
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * $Id: $
 *
 * Casos de uso: uc-02.09.05
 */
?>
<script>

function SalvarReceita()
{
    if (ValidaForm()) {
        BloqueiaFrames(true,false);
        $('frm').submit();
    }
}

/**
* Valida todo o formulário Incluir Receita
*/
function ValidaForm()
{
    if ($('inCodPPATxt') != null) {
        var codPPA  = $('inCodPPATxt').value;
    } else {
        var codPPA  = $('inCodPPA').value;
    }
    var codEntidade     = $('inCodEntidade').value;
    var codContaReceita = $('inCodConta').value;
    // PPA
    if (codPPA == '') {
        alertaAviso('Campo PPA obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }
    // Receita
    if (codContaReceita == '') {
        alertaAviso('Campo Receita obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }
    // ENTIDADE
    if (codEntidade == '') {
        alertaAviso('Campo Entidade obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }
    // Norma
    if ($('inCodNorma') != null) {
        if ($('inCodNorma').value == '') {
            alertaAviso('Campo Norma obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

            return false;
        }
    }
    // Número de recursos na lista
    if ($('inSizeRecurso') == null) {
        alertaAviso('Insira pelo menos um Recurso!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }
    var inSizeRecurso = parseInt($('inSizeRecurso').value);
    if (inSizeRecurso < 1) {
        alertaAviso('Insira pelo menos um Recurso!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return false;
    }

    return true;
}

/**
* Recalcula o Total da Receita sempre que o valor de
* um campo da lista é alterado.
*/
function recalcularValorReceita(obValorNovo, event, stTipoCampo)
{
    // Buscar o valor original pelo elemento hidden correspondente
    var idCampoHidden = 'hdn' + ucfirst(obValorNovo.name);
    var obValorOriginal = $(idCampoHidden);
    var obValorTotalReceita = $('lblTotalReceita');
    var stValorOriginal = obValorOriginal.value;
    var stValorTotalReceita = obValorTotalReceita.innerHTML;
    var flValorDiferenca = 0; // Valor da diferença entre o valor novo e original
    var flValorNovoTotalReceita = 0;
    var stValorTotalReceitasPPA = $('lblTotalReceitasPPA').innerHTML;
    var flValorTotalReceitasPPA = toFloat(stValorTotalReceitasPPA);
    stValorTotalReceita = stValorTotalReceita.replace(" ","");
    stValorOriginal     = stValorOriginal.replace(" ","");
    // Converter para float o valor total da receita
    if (stValorTotalReceita != '') {
        var flValorTotalReceita = toFloat(stValorTotalReceita);
    } else {
        var flValorTotalReceita = 0;
    }
    // Converter para float o valor original
    if (stValorOriginal != '') {
        var flValorOriginal = toFloat(stValorOriginal);
    } else {
        var flValorOriginal = 0;
    }
    floatDecimal(obValorNovo, '2', event);

    var flNovoValor = toFloat(obValorNovo.value); // Armazena o valor digitado (novo valor)
    if (stTipoCampo == 'total' && flNovoValor == 0) {
        alertaAviso('O valor total não pode ser igual a zero!', 'form', 'erro', '<?= Sessao::getId() ?>');
        obValorNovo.value = retornaFormatoMonetario(flValorOriginal);

        return;
    } else if (stTipoCampo == 'ano') {
        // String esperada: arValorAno1[0], arValorAno2[0], etc
        var strAno = obValorNovo.name;
        // Indices esperados: 0 a 3
        var indiceLinha = parseInt(strAno.substring(12,13));
        var flValorAno = 0;
        var flTotalAnos = 0;
        var i = 1;
        for (i=1; i <= 4; i++) {
            var stIdCampo = 'arValorAno'+i+'_'+indiceLinha; // Ex. arValorAno1_0
            var stValorAno = $(stIdCampo).value;
            if (stValorAno != '' && stValorAno != '0,00') {
                flValorAno = toFloat(stValorAno);
                flTotalAnos += flValorAno;
            }
        }
        if (flTotalAnos < 1) {
            alertaAviso('Valor total dos 4 anos não pode ser igual a zero!', 'form', 'erro', '<?= Sessao::getId() ?>');
            obValorNovo.value = retornaFormatoMonetario(flValorOriginal);

            return;
        }
    }

    if (flNovoValor == flValorOriginal) {
        return;
    }
    var operacao = null; // soma ou subtração
    if (flNovoValor > flValorOriginal) {
        flValorDiferenca = flNovoValor - flValorOriginal;
        operacao = 'somar';
    } else {
        flValorDiferenca = flValorOriginal - flNovoValor;
    }

    if (operacao == 'somar') {
        flValorNovoTotalReceita = flValorTotalReceita + flValorDiferenca;
        flValorTotalReceitasPPA = flValorTotalReceitasPPA + flValorDiferenca;
    } else {
        // Subtrair
        flValorNovoTotalReceita = flValorTotalReceita - flValorDiferenca;
        flValorTotalReceitasPPA = flValorTotalReceitasPPA - flValorDiferenca;
    }

    if (flValorNovoTotalReceita == 0) {
        obValorNovo.value = retornaFormatoMonetario(flValorOriginal);
        alertaAviso('O valor total não pode ser igual a zero!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    if (!validarValorTotalReceita(flValorNovoTotalReceita)) {
        obValorNovo.value = retornaFormatoMonetario(flValorOriginal);
        alertaAviso('O valor total da Receita ultrapassou máximo permitido!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }

    // Gravar novos valores
    obValorOriginal.value          = retornaFormatoMonetario(flNovoValor);
    obValorTotalReceita.innerHTML  = retornaFormatoMonetario(flValorNovoTotalReceita);
    $('lblTotalReceitasPPA').innerHTML = retornaFormatoMonetario(flValorTotalReceitasPPA);
    if ($('lblTotalPrevisto') != null) {
        $('lblTotalPrevisto').innerHTML = retornaFormatoMonetario(flValorNovoTotalReceita);
    }
}

/**
 * Verifica se o valor dos vinculos de receita não ultrapassam o maximo
 * de 999.999.999.999,99, toda vez que for incluído ou alterado
 * na lista de Rcursos.
 */
function validarValorTotalReceita(valorTotalReceita)
{
    var flValorTotalReceita = toFloat(valorTotalReceita);
    if (flValorTotalReceita > 999999999999.99) {
        return false;
    } else {
        return true;
    }
}

/**
* Limpa o formulário Incluir Receita
*/
function limparFormIncluiReceita()
{
    $('inCodPPA').value                = '';
    $('inCodPPATxt').value             = '';
    $('inCodEntidade').value           = '';
    $('stNomEntidade').value           = '';
    $('inCodConta').value       = '';
    $('stDescricaoReceita').innerHTML   = '&nbsp;';
    $('spnNorma').innerHTML            = '&nbsp;';
    limparRecurso();
    $('spnFonteRecurso').innerHTML     = '';
    $('lblTotalReceita').innerHTML     = '0,00';
    $('lblTotalReceitasPPA').innerHTML = '0,00';
}

/**
* Limpa o formulário Alterar Receita
*/
function limparFormAlteraReceita()
{
    $('inCodRecurso').value           = '';
    $('stDescricaoRecurso').innerHTML = '&nbsp;';
    var arCodNorma = document.getElementsByName("inCodNorma");
    arCodNorma[0].value = '';
    $('stDataNorma').innerHTML = '&nbsp;';
    $('stNorma').innerHTML     = '&nbsp;';
    if ($('stDataPublicacao') != null) {
        $('stDataPublicacao').innerHTML = '&nbsp;';
    }
    limparRecurso();
    // Recarrega a lista de Recursos gravados no banco de dados
    setTimeout("montaParametrosGET('montaListaAlteraRecurso')", 500);
}

/**
* Verifica se já existe a Receita selecionada já está cadastrar para o PPA escolhido
*/
function verificarCadastroReceitaPPA(inCodPPA)
{
    var inCodPPA = $('inCodPPATxt').value;
    var inCodConta = $('inCodConta').value;
    if (inCodPPA != '' && inCodConta != '') {
        setTimeout("montaParametrosGET('verificarCadastroReceitaPPA')", 500);
    }
}

function recuperaValorTotalPPA()
{
    montaParametrosGET('recuperaValorTotalPPA', null, true);
}

function recuperaValorTotalReceita()
{
    montaParametrosGET('recuperaValorTotalReceita', null, true);
}

/**
 * Executado sempre que o PPA é selecionado/alterado para
 * verificar estado da destinacao_recurso do PPA.
 */
function montarComponenteRecurso()
{
    montaParametrosGET('montarComponenteRecurso', null, true);
}

function montaSpanNorma()
{
    montaParametrosGET('montaSpanNorma', null, true);
}

/**
 * Função para realizar a execução dos métodos relacionados ao
 * componente ITextBoxSelectPPA
 */
function confirmaExecutarEventosPPA(inCodPPA)
{
    var obCodPPAAnterior = $('inCodPPAAnterior');
    if (inCodPPA != '') {
        if (obCodPPAAnterior.value == '') {
            obCodPPAAnterior.value = inCodPPA;
        } else if ($('inSizeRecurso') == null) {
            obCodPPAAnterior.value = inCodPPA;
        }
        if (obCodPPAAnterior.value != '' && (obCodPPAAnterior.value != inCodPPA)) {
            if ($('inSizeRecurso') != null) {
                var inSizeRecurso = parseInt($('inSizeRecurso').value);
                if (inSizeRecurso > 0) {
                    $('inCodPPATxt').value = obCodPPAAnterior.value; // Text Box
                    $('inCodPPA').value    = obCodPPAAnterior.value; // Select Box
                    confirmPopUp('Alterando o PPA atual', 'Alterar o PPA agora pode mudar o tipo de vinculação de recurso. Confirma apagar a lista de recursos?', "executarEventosPPA('"+inCodPPA+"');");

                    return;
                }
            }
        }
        executarEventosPPA(inCodPPA);
    } else {
        obCodPPAAnterior.value = inCodPPA;
        // Receita
        $('inCodConta').value = '';
        $('stDescricaoReceita').innerHTML = '&nbsp;';
        // Entidade
        $('inCodEntidade').value = '';
        $('stNomEntidade').value = '';
        // Norma
        $('spnNorma').innerHTML = '';
        // Recurso
        $('spnRecurso').innerHTML = '';
        $('spnFonteRecurso').innerHTML = '';
        // Total Receita atual
        $('lblTotalReceita').innerHTML = '0,00';
        // Total Receitas PPA
        $('lblTotalReceitasPPA').innerHTML = '0,00';
        // Total de Despesas do PPA
        $('lblTotalDespesasPPA').innerHTML = '0,00';
    }
}

/**
 * Função disparada pela confirmaExecutarEventosPPA
 */
function executarEventosPPA(inCodPPA)
{
    $('inCodPPATxt').value = inCodPPA; // Text Box
    $('inCodPPA').value    = inCodPPA; // Select Box
    // Receita
    $('inCodConta').value = '';
    $('stDescricaoReceita').innerHTML = '&nbsp;';
    // Entidade
    $('inCodEntidade').value = '';
    $('stNomEntidade').value = '';
    verificarCadastroReceitaPPA();
    recuperaValorTotalPPA();
    recuperaValorTotalReceita();
    $('spnRecurso').innerHTML = '&nbsp;';
    $('spnFonteRecurso').innerHTML = '&nbsp;';
    montarComponenteRecurso();
    montaSpanNorma();
    // Zerar total Receita atual
    $('lblTotalReceita').innerHTML = '0,00';
}

/**
* Verifica se a Norma já está cadastrada para a Receita
*/
function verificarCadastroReceitaNorma()
{
    var obInCodNorma = $('inCodNorma');
    if (obInCodNorma.value != '') {
        setTimeout("montaParametrosGET('verificarCadastroReceitaNorma')", 1000);
    }
}

function limparRecurso()
{
    $('inCodRecurso').value = '';
    $('stDescricaoRecurso').innerHTML = '&nbsp;';
    $('spnValorReceita').innerHTML = '';
    $('btnTotal').checked = false;
    $('btnAno').checked = false;
}

/**
 * Inclui um novo Recurso na lista Fontes de Recurso
 */
function incluirRecurso()
{
    $('hdnDescPopUpRecurso').value = $('stDescricaoRecurso').innerHTML;
    var flValorTotalRecurso = 0; // Valor do recurso que está sendo inserido
    var flValorAtualReceita = 0;
    var stValorAtualReceita = $('lblTotalReceita').innerHTML;
    var obTotalReceitasPPA  = $('lblTotalReceitasPPA');
    if (stValorAtualReceita != '') {
        flValorAtualReceita = toFloat(stValorAtualReceita);
    }
    var stCodPPA          = $('inCodPPA').value;
    var stcodContaReceita = $('inCodConta').value;
    var stCodRecurso      = $('inCodRecurso').value;

    if (stCodPPA == '') {
        alertaAviso('Código PPA obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    if (stcodContaReceita == '') {
        alertaAviso('Código Receita obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    if (stCodRecurso == '') {
        alertaAviso('Código recurso obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    if (jQuery('#btnTotal').attr('checked') == false && jQuery('#btnAno').attr('checked') == false) {
        alertaAviso('Tipo de valor obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    // Valor por Total
   if ($('flValorTotal') != null) {
        flValorTotalRecurso = toFloat($('flValorTotal').value);
        var flValorTotalReceita = flValorAtualReceita + flValorTotalRecurso;
        if (flValorTotalRecurso == 0) {
            alertaAviso('Valor total obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

            return;
        }
    // Valor por Ano
    } else if ($('flValorAno1') != null) {
        // Valor Ano 1
        var stValorAno1 = $('flValorAno1').value;
        var flValorAno1 = 0;
        if (stValorAno1 != '') {
            flValorAno1 = toFloat(stValorAno1);
        }
        // Valor Ano 2
        var stValorAno2 = $('flValorAno2').value;
        var flValorAno2 = 0;
        if (stValorAno2 != '') {
            flValorAno2 = toFloat(stValorAno2);
        }
        // Valor Ano 3
        var stValorAno3 = $('flValorAno3').value;
        var flValorAno3 = 0;
        if (stValorAno3 != '') {
            flValorAno3 = toFloat(stValorAno3);
        }
        // Valor Ano 4
        var stValorAno4 = $('flValorAno4').value;
        var flValorAno4 = 0;
        if (stValorAno4 != '') {
            flValorAno4 = toFloat(stValorAno4);
        }
        // Valor Total
        flValorTotalRecurso = flValorAno1 + flValorAno2 + flValorAno3 + flValorAno4;
        var flValorTotalReceita = flValorAtualReceita + flValorTotalRecurso;
        if (flValorAno1 == 0 &&  flValorAno2 == 0 && flValorAno3 == 0 && flValorAno4 == 0) {
            alertaAviso('Valor para pelo menos um ano obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');

            return;
        }
     }
    // Fim validação
    var i = 0;
    var insere = true;
    var inCodRecurso = parseInt(stCodRecurso);
    // Varrer a lista e verificar se já existe um recurso com o mesmo código
    while ($('arCodRecurso['+i+']') != null) {
        var inCodRecursoLista = parseInt($('arCodRecurso['+i+']').value);
        if (inCodRecurso == inCodRecursoLista) {
            insere = false; // Recurso já existe na lista
        }
        i++;
    }
    if (insere == false) {
        alertaAviso('Recurso já cadastrado na lista!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    if (!validarValorTotalReceita(flValorTotalReceita)) {
        alertaAviso('O valor total da Receita ultrapassou máximo permitido!', 'form', 'erro', '<?= Sessao::getId() ?>');

        return;
    }
    montaParametrosGET('montaListaIncluiRecurso');
    limparRecurso();
    // Atualiza o valor da Label de Total de Receita com a soma dos totais dos recursos
    if ($('lblTotalPreviso') != null) { // Label existe somente na tela de alteração
        $('lblTotalPreviso').innerHTML = retornaFormatoMonetario(flValorTotalReceita);
    }
    $('lblTotalReceita').innerHTML = retornaFormatoMonetario(flValorTotalReceita);
    // Atualizar total de Receitas no PPA
    var flValorTotalReceitasPPA = toFloat(obTotalReceitasPPA.innerHTML);
    flValorTotalReceitasPPA = flValorTotalReceitasPPA + flValorTotalRecurso;
    obTotalReceitasPPA.innerHTML = retornaFormatoMonetario(flValorTotalReceitasPPA);
} // end incluirRecurso

function excluirReceita()
{

    if ($('inCodNorma') != null) {
        if ($('inCodNorma').value == '') {
            return alertaAviso('Campo Norma obrigatório!', 'form', 'erro', '<?= Sessao::getId() ?>');
        }
    }
    $('frm').submit();
}

/**
* Método associado ao botão de exclusão da lista numérica
* do objeto Utils
*/
function excluirRecurso(objeto)
{
    var tabela                  = objeto.parentNode.parentNode.parentNode;
    var linha                   = objeto.parentNode.parentNode;
    var flValorCelula           = 0;
    var stValorTotalReceita     = $('lblTotalReceita').innerHTML;
    stValorTotalReceita         = stValorTotalReceita.replace(" ","");
    var flValorTotalReceita     = toFloat(stValorTotalReceita);
    var obTotalReceitasPPA      = $('lblTotalReceitasPPA');
    var flValorTotalReceitasPPA = toFloat(obTotalReceitasPPA.innerHTML);
    // Subtrair uma linha do total de Recursos
    var obSizeRecurso = $('inSizeRecurso');
    obSizeRecurso.value = parseInt(obSizeRecurso.value) - 1;

    for (i = 0; i < linha.cells.length; i++) {
        if (linha.cells[i].firstChild.value != undefined) {
            var stValorCelula = linha.cells[i].firstChild.value;
            // dentro de loop não usar a função toFloat da framework (seu tipo de retorno pára a iteração)
            if (stValorCelula.search(",") > 0) {
                stValorCelula = stValorCelula.replace(".","");
                stValorCelula = stValorCelula.replace(".","");
                stValorCelula = stValorCelula.replace(",",".");
            }
            flValorCelula += parseFloat(stValorCelula);
       }

    } // end for

    flValorTotalReceita     = (flValorTotalReceita - flValorCelula);
    flValorTotalReceitasPPA = (flValorTotalReceitasPPA - flValorCelula);

    tabela.deleteRow(linha.rowIndex);

    if (tabela.rows.length == 2) {
        flValorTotalReceita = 0;
        tabela.parentNode.removeChild(tabela);
    } else {
        montaParametrosGET('atualizarListaFonteRecursos', null, true);
    }

    if ($('lblTotalPreviso') != null) {
        // Label existe somente na tela de alteração
        $('lblTotalPreviso').innerHTML = retornaFormatoMonetario(flValorTotalReceita);
    }
    // Atualizar o Total da Receita
    $('lblTotalReceita').innerHTML = retornaFormatoMonetario(flValorTotalReceita);
    // Atualizar o total de Receitas no PPA
    obTotalReceitasPPA.innerHTML = retornaFormatoMonetario(flValorTotalReceitasPPA);

} // end function excluirRecurso

/**
 *Arredonda o numero para a precisao informada
 */
function arredondarPrecisao(numero, inPrecisao)
{
    var novaPrecisao = Math.pow(10, inPrecisao);

    return( Math.round(numero * novaPrecisao) / novaPrecisao );
}

function confirmPopUp(stTitle,stText,stMethodSim)
{
    stHTMLFrames = '<div id="containerPopUp"></div>';
    stHTML = '    <div id="showPopUp">';
    stHTML = stHTML + '        <h3>'+stTitle+'</h3>';
    stHTML = stHTML + '        <h4>Confirmação</h4>';
    stHTML = stHTML + '        <p>'+stText+'</p>';
    stHTML = stHTML + '        <input type="button" value="Sim" id="btPopUpSim" name="btPopUpSim" onclick="javascript:removeConfirmPopUp();'+stMethodSim+';"; />';
    stHTML = stHTML + '        <input type="button" value="Não" id="btPopUpNao" name="btPopUpNao" onclick="removeConfirmPopUp();" />';
    stHTML = stHTML + '    </div>';

    var containerCSS = { 'width':'100%',
                         'height': '100%',
                         'background':'transparent url(../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/overlay.png) left',
                         'position':'absolute',
                         'left':'0',
                         'top':'0' };

    for (i=1;i<4;i++) {
        jq('html',parent.frames[i].document).append(stHTMLFrames);
        jq('html',parent.frames[i].document).css({'overflow':'hidden'});
        jq('div#containerPopUp', parent.frames[i].document).css(containerCSS);
    }

    jq('div#containerPopUp',parent.frames[2].document).html(stHTML);
    jq('#btPopUpSim').focus();
}

function CancelarCL()
{
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$_REQUEST["stAcao"]."&inCodPPA=".$_REQUEST['cod_ppa'];
?>
    mudaTelaPrincipal("<?=$pgList.'?'.Sessao::getId().$stLink;?>");
}

/**
* Passa o primeiro caracter da string para cx alta
*
* @param string str
* @return string
*/
function ucfirst(str)
{
    str += '';
    var f = str.charAt(0).toUpperCase();

    return f + str.substr(1, str.length-1);
}

</script>
