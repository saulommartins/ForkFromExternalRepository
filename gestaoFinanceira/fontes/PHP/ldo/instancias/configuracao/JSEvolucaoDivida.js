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
 * Página com as funções js da evolução da dívida
 * Data de Criação   : 03/07/2009
 

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz
 
 * @package URBEM
 * @subpackage 
 
 * $Id:$
 */
?>
<script type="text/javascript">

    /**
     *  funcao para formatar a tabela, mudando os labels para text e adicionado estilos
     **/
    function formataTableDivida()
    {
        for (i=1;i<=6;i++) {
            jq("input[id^='flValor"+i+"']").each(function(){
                arDado = (this.id).split('_');
                if (arDado[2] == 0) {
                    jq(this).css('text-align','right');
                    setLabel(this.id,true);
                }
                if (i == 1 && arDado[1] == 6) {
                    defineNulo(i, arDado[1]);
                }
            });
        }
    }

    /**
     *  funcao para formatar uma string para float
     **/
    function formataValor(stValor)
    {
        return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
    }

    /**
     *  funcao para setar um campo da tabela sem valor
     **/
    function defineNulo(coluna, id)
    {
        jq("input[id^='flValor"+coluna+"_"+id+"_"+"']:first").val('');
        jq("span[id^='flValor"+coluna+"_"+id+"_"+"']:first").html('');
    }

    /**
     *  funcao que soma os valores de acordo com o que e passado
     **/
    function somaValor(arLinhas,coluna)
    {
        inSoma = 0.00;
        inCount = 1;
        arLinhas.forEach(function(inPos) {
            if (inCount == 1) {
                inSoma += formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val());
            } else {
                if ((inSoma >= 0 && formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val()) > 0)
                 || (inSoma < 0 && formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val()) < 0)) {
                    inSoma -= formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val());
                } else if ((inSoma >= 0 && formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val()) < 0)
                       || (inSoma < 0 && formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val()) > 0)) {
                    inSoma += formataValor(jq("input[id^='flValor"+coluna+"_"+inPos+"_"+"']").val());
                }
            }
            inCount++;
        });
        inSoma = retornaFormatoMonetario(inSoma);
        if (inSoma == '') {
            inSoma = '0,00';
        }

        return inSoma;
    }

    /**
     *  funcao que soma os valores de acordo com o que e passado
     **/
    function somaValorColunas(arLinhas,coluna)
    {
        inSoma = 0.00;
        inCount = 1;
        colunaTmp = coluna;
        arLinhas.forEach(function(inPos) {
            if (inCount == 1) {
                inSoma = formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val());
                colunaTmp = colunaTmp - 1;
            } else {
                if ((inSoma >= 0 && formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val()) > 0)) {
                    inSoma -= formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val());
                } else if ((inSoma >= 0 && formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val()) < 0)
                        || (inSoma  < 0 && formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val()) < 0)
                        || (inSoma  < 0 && formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val()) > 0)) {
                    inSoma += formataValor(jq("input[id^='flValor"+colunaTmp+"_"+inPos+"_"+"']").val());
                }
            }
            inCount++;
        });
        inSoma = retornaFormatoMonetario(inSoma);
        if (inSoma == ''){
            inSoma = '0,00';
        }

        return inSoma;
    }

    /**
     *  funcao para preencher os input/span com o valor
     **/
    function preencheValor(id, coluna, arLinhas)
    {
        if (id == 6) {
            for (contador=2;contador<=6;contador++) {
                coluna = contador;
                jq("input[id^='flValor"+coluna+"_"+id+"_"+"']:first").val(somaValorColunas(arLinhas,coluna));
                jq("span[id^='flValor"+coluna+"_"+id+"_"+"']:first").html(somaValorColunas(arLinhas,coluna));
            }
        } else {
            jq("input[id^='flValor"+coluna+"_"+id+"_"+"']:first").val(somaValor(arLinhas,coluna));
            jq("span[id^='flValor"+coluna+"_"+id+"_"+"']:first").html(somaValor(arLinhas,coluna));
        }
    }

    /**
     *  funcao que chama os calculos dos valores da tabela
     */
    function recalcularValores(coluna,id)
    {
        preencheValor(3, coluna, [1,2]);
        preencheValor(5, coluna, [3,4]);
        preencheValor(6, coluna, [5,5]);
    }    

</script>
