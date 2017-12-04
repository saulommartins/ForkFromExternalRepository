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
 * javascript para validacao de acoes
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */
?>
<script type="text/javascript">
    jq(document).ready(function(){
        formataTableReceita();
        calculaTotais('AL');
        calculaTotais('PF');
        calculaTotais('PJ');
        LiberaFrames(true,false);
    });

    /**
     *  funcao para formatar a tabela, mudando os labels para text e adicionado estilos
     **/
    function formataTableReceita()
    {
        jq("input[id^='flValorAno1']").each(function(){
            arDado = (this.name).split('_');
            if(arDado[4] == 0){
                jq(this).parent().parent().css('font-weight','bold');
            }
        });
        jq('table.tabela').each(function(){
            stTableId = this.id;
            if(stTableId.search('tableReserva') == -1){
                for(i = 1; i <= 4; i++){
                    if(!(stTableId.search('Projetado') != -1 && i >= 4)){
                        arInfo = (jq("table#"+stTableId+" input[id^='flValorAno"+i+"']:first").attr('name')).split('_');
                        if(arInfo[5] == 0){
                            jq("table#"+stTableId+" input[id^='flValorAno"+i+"']").each(function(){
                            arDado = (this.name).split('_');                        
                                if(arDado[4] == 1 && arDado[5] == 0){
                                    jq(this).css('text-align','right');
                                    setLabel(this.id,true);
                                }
                            });
                        }
                    }
                }
            }
        });
        jq('span#flValorAno4_RES1_C_0_AL_1_label').parent().parent().css('font-weight','bold');
        jq('span#flValorAno4_RES2_C_0_AL_2_label').parent().parent().css('font-weight','bold');
        
        jq("span[id^='flValorAno1_RES1_C_0_']").each(function(){
            jq(this).parent().parent().css('font-weight','bold');
        });
        jq("span[id^='flValorAno1_RES2_C_0_']").each(function(){
            jq(this).parent().parent().css('font-weight','bold');
        });
    }

    /**
     *  funcao para formatar uma string para float
     **/
    function formataValor(stValor)
    {
        if (stValor.blank()) stValor = "0,00";
        return ((parseFloat(stValor.replace(/\./g, '').replace(',', '.'))) * 100) / 100;
    }

    /**
     *  funcao que soma os valores de acordo com o que e passado
     **/
    function somaValor(arLinhas,coluna,tipo,aba)
    {
        inSoma = 0.00;
        
        arLinhas.forEach(function(inPos) {
            inSoma += formataValor(jq("input#flValorAno"+coluna+"_"+inPos+"_"+tipo+"_"+aba).val());
        });
        inSoma = retornaFormatoMonetario(inSoma);
        if(inSoma == ''){
            inSoma = '0,00';
        }

        return inSoma;
    }

    /**
     *  funcao para preencher os input/span com o valor
     **/
    function preencheValor(id,coluna,tipo,arLinhas,aba)
    {
        jq("input#flValorAno"+coluna+"_"+id+"_"+tipo+"_"+aba).val(somaValor(arLinhas,coluna,tipo,aba));
        jq("span#flValorAno"+coluna+"_"+id+"_"+tipo+"_"+aba+"_label").html(somaValor(arLinhas,coluna,tipo,aba));
    }

    /**
     *  funcao para calcular os totais
     **/
    function calculaTotais(aba)
    {
        inIni = (aba == 'AL') ? 4 : 1;
        inMax = (aba == 'PJ') ? 3 : 4;
        for(x=1;x<=inMax;x++){
            preencheValor('00',x,'R',[1,18,24,25],aba);
            preencheValor('99',x,'D',[26,36],aba);
            
            if (!(x<4 && aba == 'AL')) {
                //calcula o valor da reserva de contigencia para o RPPS
                flTotalReceitaRPPS = formataValor(somaValor([5,9,17,24],x,'R',aba));
                flTotalDespesaRPPS = formataValor(somaValor([29,32,35,39],x,'D',aba));
        
                flTotalRPPS = retornaFormatoMonetario(flTotalReceitaRPPS-flTotalDespesaRPPS);
                if(flTotalRPPS == ''){
                    flTotalRPPS = '0,00';
                }
        
                flSubTotalRPPS = flTotalReceitaRPPS-flTotalDespesaRPPS;
                if(flSubTotalRPPS == ''){
                    flSubTotalRPPS = 0.00;
                }
                //calcula o valor da reserva de contigencia
                flTotalReceita         = formataValor(somaValor(['00'],x,'R',aba));
                flTotalDespesaCorrente = formataValor(somaValor(['26'],x,'D',aba));
                flTotalDespesaCapital  = formataValor(somaValor(['36'],x,'D',aba));
        
                jq("input#flValorAno"+x+"_RES2_C_0_"+aba).val(flTotalRPPS);
                jq("span#flValorAno"+x+"_RES2_C_0_" + aba + "_label").html(flTotalRPPS);
        
                flTotalNaoRPPS = retornaFormatoMonetario(flTotalReceita-flTotalDespesaCorrente-flTotalDespesaCapital-flSubTotalRPPS);
                if(flTotalNaoRPPS == ''){
                    flTotalNaoRPPS = '0,00';
                }
        
                jq("input#flValorAno"+x+"_RES1_C_0_" + aba).val(flTotalNaoRPPS);
                jq("span#flValorAno"+x+"_RES1_C_0_" + aba + "_label").html(flTotalNaoRPPS);
            }
        }        
    }

    /**
     *  funcao que chama os calculos dos valores da tabela
     */
    function recalcularValores(coluna,id)
    {
        arInfo = id.split('_');
        inMax = (arInfo[3] == 'PJ') ? 3 : 4;
        
        if(arInfo[1] == '25'){
            for(y=1;y<=inMax;y++){
                flNegativo = Math.abs(formataValor(jq("input#flValorAno"+y+"_25_R_"+arInfo[3]).val()));
                if(flNegativo == 0){
                    flNegativo = '0,00';
                } else {
                    flNegativo = retornaFormatoMonetario(flNegativo * -1);
                }
                jq("input#flValorAno"+y+"_25_R_"+arInfo[3]).val(flNegativo);
                jq("span#flValorAno"+y+"_25_R_"+arInfo[3]).val(flNegativo);
            }
        }
        if(arInfo[2] == 'R'){
            preencheValor(1 ,coluna,arInfo[2],[2,4,5,8,9,10,11,12,13,14,16,17],arInfo[3]);
            preencheValor(3 ,coluna,arInfo[2],[4,5],arInfo[3]);
            preencheValor(6 ,coluna,arInfo[2],[8,9,10],arInfo[3]);
            preencheValor(7 ,coluna,arInfo[2],[8,9],arInfo[3]);
            preencheValor(15,coluna,arInfo[2],[16,17],arInfo[3]);
            preencheValor(18,coluna,arInfo[2],[19,20,21,22,23],arInfo[3]);
        } else {
            preencheValor(26,coluna,arInfo[2],[28,29,31,32,34,35],arInfo[3]);
            preencheValor(27,coluna,arInfo[2],[28,29],arInfo[3]);
            preencheValor(30,coluna,arInfo[2],[31,32],arInfo[3]);
            preencheValor(33,coluna,arInfo[2],[34,35],arInfo[3]);
            preencheValor(36,coluna,arInfo[2],[38,39,41,42,43],arInfo[3]);
            preencheValor(37,coluna,arInfo[2],[38,39],arInfo[3]);
            preencheValor(40,coluna,arInfo[2],[41,42],arInfo[3]);
        }
        calculaTotais(arInfo[3]);
    }    

</script>
