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
    * Página de JavaScript
    * Data de Criação   : 23/01/2008


    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/
?>
<script type="text/javascript">

/**
* montaArrayOutrasRemuneracoes
*
* monta o array de opções do select multiplo de Outras Remunerações
* @param string tipo - recebe se o qual o select que partiu a chamada da função, se foi 
*                      do 'remuneracao' ou da 'gratificacao'
*
*/

function montaArrayOutrasRemuneracoes( tipo )
{
    // verifica se o tipo é remuneracao ou gratificacao para assim poder setar
    // as 3 variáveis que serão usadas. As 2 primeiras recebem as opcoes do select multiplo
    // que fez a chamada da função, e a outra variável recebe as opções do select de seleção 
    // inverso, ou seja da outro select multiplo.
    if (tipo=='remuneracao') {
        var arOpcoesDispBase = document.frm.arCodArqDisponiveisRemuneracaoBase.options;
        var arOpcoesSelBase = document.frm.arArquivosSelecionadosRemuneracaoBase.options;
        var arOpcoesSelInversa = document.frm.arArquivosSelecionadosGratificacaoFuncao.options;
        
    } else if (tipo=='gratificacao') {        
        var arOpcoesDispBase = document.frm.arCodArqDisponiveisGratificacaoFuncao.options;
        var arOpcoesSelBase = document.frm.arArquivosSelecionadosGratificacaoFuncao.options;
        var arOpcoesSelInversa = document.frm.arArquivosSelecionadosRemuneracaoBase.options;
    }
    
    var arDispOutrRem = document.frm.arCodArqDisponiveisOutrasRemuneracoes;    
    var arSelOutrRem = document.frm.arArquivosSelecionadosOutrasRemuneracoes;
    
    var arOpcoesDispOutrRem = arDispOutrRem.options;
    var arOpcoesSelOutrRem = arSelOutrRem.options;
    
    // limpa o select multiplo de disponivel de outras remunerações
    for (var chave in arOpcoesDispOutrRem) {
            arOpcoesDispOutrRem[chave] = null;
    }
    
    var stChaveOpcao;
    var inCont=0;
    // verifica se as opções disponiveis do select base tem algo, se tiver preenche
    // com o que sobrou, caso contrário o disponivel do outras remunerações fica vazio.
    if (arOpcoesDispBase.length > 0) {
        
        // faz o foreach para cada opção disponivel na base
        for (var chave in arOpcoesDispBase) {
            //se a chave for inteiro, entra
            if (!isNaN(chave)) {
                // se existe a opção da chave
                if (arOpcoesDispBase[chave]) {
                    // faz a verficação se o valor dessa chave no select multiplo base está no select multiplo de selecionados 
                    // da inversa, caso esteja não poderá ser escrito no select de outras remunerações
                    stChaveOpcao = verificaSeExiste( arOpcoesSelInversa, arOpcoesDispBase[chave].value );                    
                    // faz a verficação se o valor dessa chave no select multiplo base está no select multiplo de selecionados 
                    // da outras remunerações, caso esteja não poderá ser escrito no select de outras remunerações
                    stChaveOpcaoOutrRem = verificaSeExiste( arOpcoesSelOutrRem, arOpcoesDispBase[chave].value );
                    
                    // caso não tenha encontrado nada, escreve a opção que sobrou no select disponivel de outras rem.
                    if ( stChaveOpcao == '' && stChaveOpcaoOutrRem == '' ) {
                        arOpcoesDispOutrRem[inCont] = new Option( arOpcoesDispBase[chave].text, arOpcoesDispBase[chave].value, '' );
                        inCont++;
                    }
                    
                    // faz a verificação se existe algum dado no select de seleção da base, se ouver faz a verificação se existe
                    // o valor no select selecionado de outras remunerações, caso exista, deve apagar o valor.                    
                    if (arOpcoesSelBase[chave]) {
                        stChaveOpcao = verificaSeExiste( arOpcoesSelOutrRem, arOpcoesSelBase[chave].value );
                        if (stChaveOpcao != '') {                        
                            arOpcoesSelOutrRem[parseInt(stChaveOpcao)] = null;
                        }
                    }
                }
                
            } // end if isNaN
        } //end for
    } else {
        // limpa o select multiplo de selecionados de outras remunerações, pois não há nenhum item disponivel no select base
        for (var chave in arOpcoesSelOutrRem) {
                arOpcoesSelOutrRem[chave] = null;
        }
    }
    
}

function verificaSeExiste( arOpcoes, vlOpcao )
{
    for (var chave in arOpcoes) {
        if (!isNaN(chave)) {
            if (arOpcoes[chave]) {
                if (arOpcoes[chave].value == vlOpcao ) {
                    return chave;
                }
            }
        }
    }
    return '';
}


</script>
