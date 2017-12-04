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
    * Página de javascrip da Elaboração de Estimativa da Receita
    * Data de Criação: 07/04/2009


    * @author Analista: Tonismar Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package      URBEM
    * @subpackage   PPA

    * $Id:$
*/
?>
<script type="text/javascript">

/**
 * Metodo que formata o valor para o padrao americano para poder realizar os calculos e transforma o valor em float
 *
 * @param  string stValor recebe o valor digitado no campo, porém ele está com o tipo sendo string
 * @return float          retorna o valor formatado
 */
function formataValor(stValor)
{
    return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
}

/**
 * Realiza a soma das posições do valor passadas pelo parametro
 *
 * @param  array arPositions array contendo as posições que serão somadas
 * @return float valor somado da coluna valor das posições da listagem passadas pelo parametro
 */
function getSomaPositions(arPositions)
{
    var inSoma = 0;
    arPositions.forEach(function(inPosition) {
        if ( jq("#flValorReceita_"+inPosition+"_A_"+inPosition).val() ) {
            inSoma += formataValor(jq("#flValorReceita_"+inPosition+"_A_"+inPosition).val());
        }
    });

    return inSoma;
}

function ajustesListagem() {

    jq("#tableReceita input").each(function(){        
        var arReceita = this.id.split("_");
        if (arReceita[2] != "S") {
            jq("#"+this.id).css('display', 'inline').css('text-align', 'right').val('0,00').blur(function() {
                if ((formataValor(this.value) >= 0 || arReceita[1] == 25) && (arReceita[0] == "flValorReceita")) {
                    if (formataValor(this.value) == 0 ) {
                       this.value = '0,00';
                    }
                    calculaSoma();
                } else if (formataValor(this.value) < 0 && arReceita[0] == "flValorReceita") {
                    this.value = '0,00';
                    calculaSoma();
                }
            });
        } else {            
            if (arReceita[0] == 'flValorReceita') {
                jq("#"+this.id+"_label").html("0,00").css('font-weight', 'bold');
            } else{
                jq("#"+this.id+"_label").html("&nbsp;");
            }
            
        }
    })
}

function calculaSoma()
{
    var inSoma = 0;
    var inSoma2 = 0;
    var inSoma3 = 0;
    var inSoma4 = 0;

    inSoma += getSomaPositions([9]);
    jq("#flValorReceita_8_S_8_label").html(retornaFormatoMonetario(inSoma, true));

    inSoma4 += getSomaPositions([5,6,7]);
    jq("#flValorReceita_4_S_4_label").html(retornaFormatoMonetario(inSoma4, true));

    inSoma += inSoma4;
    jq("#flValorReceita_3_S_3_label").html(retornaFormatoMonetario(inSoma, true));

    inSoma += getSomaPositions([10,11]);
    jq("#flValorReceita_2_S_2_label").html(retornaFormatoMonetario(inSoma, true));

    inSoma += getSomaPositions([12,13,14,15,16,17,18]);
    jq("#flValorReceita_1_S_1_label").html(retornaFormatoMonetario(inSoma, true));

    inSoma2 += getSomaPositions([20, 21, 22, 23, 24]);
    jq("#flValorReceita_19_S_19_label").html(retornaFormatoMonetario(inSoma2, true));

    inSoma3 += getSomaPositions([25]);
    if (inSoma3 > 0) {
        inSoma3 = inSoma3*-1;
    }
    jq("#flValorReceita_25_A_25").val(retornaFormatoMonetario(inSoma3, true));

    inSoma += inSoma2 + inSoma3;
    jq("#flValorReceita_26_S_26_label").html(retornaFormatoMonetario(inSoma, true));
}

/**
 * Realiza o bloqueio e desbloqueio dos campos de porcentagem na listagem
 *
 * @param  boolean boReadOnly recebe o valor do readOnly
 * @return void
 */
function bloqueiaPorcegemLista(boReadOnly, boZerarCampos)
{
    jq("#tableReceita input[class='porcentagem']").each(function(){
        jq("#"+this.id).attr('readOnly', boReadOnly);
        if (boZerarCampos) {
            jq("#"+this.id).val('0,00');
        }        
    });
}


function montaEventoInput()
{
    jq("input[id*='flPorcentagemAno']").blur(function() {        
        var arId = this.id.split('');
        var valor = retornaFormatoMonetario(formataValor(this.value));
        if (valor == '') {
            valor = '0,00';
        }
        jq("#tableReceita input[id*='flAno"+arId[16]+"']").each(function() {
            jq("#"+this.id).val(valor);
        });
    });

    return true;
}


</script>
                
