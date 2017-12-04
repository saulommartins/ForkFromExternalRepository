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
        formataTable();
        var contexto = jq('span table');
        for(x = 1; x <= 3; x++){
            calculaTotal(0,jq('span table#tableNormal'));
            calculaPorcentagem(0,jq('span table#tableNormal'));
            calculaTotal(1,jq('span table#tableRPPS'));
            calculaPorcentagem(1,jq('span table#tableRPPS'));
            console.log('%s',jq("input[id^='flValorAno" + x + "_3']").val());
        }
    });
    
    /**
     * funcao que aplica os estilos necessários a tabela
     **/
    function formataTable()
    {
        // define o contexto onde o jquery vai se limitar a buscar
        var context = jq('table tbody').next();
        
        // adiciona estilos as linhas da tabela
        jq('tr td:nth-child(2)',context).each(function(){
            jq(this).css('font-weight','bold');
        });
        jq('tr:nth-child(4)',context).each(function(){
            jq(this).css('font-weight','bold');
        });
        
        //flValorAno1_[cod_tipo]_[rpps]_[nivel]_[orcamento_1]
        jq("input[id^='flValorAno']",context).each(function(){
            arDado = (this.id).split('_');
            if(arDado[4] == 0 && arDado[3] == 1){
                jq(this).css('text-align','right');
                setLabel(this.id,true);
            }
        });
        
        LiberaFrames(true,true);
    }
    
    /**
     *  funcao para formatar uma string para float
     **/
    function formataValor(stValor)
    {
        return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
    }
    
    /**
     * soma valores para o total
     **/
    function calculaTotal(rpps,contexto)
    {
        for(ano = 3; ano >= 1; ano--){
            var flSoma = 0.00;
            jq("input[id^='flValorAno" + ano + "']",contexto).each(function(){
                arDado = (this.id).split('_');
                if(arDado[3] == 1){
                    if(this.value != ''){
                        flSoma += formataValor(this.value);
                    }
                }
            });
            if (flSoma == 0) {
               var stTotal = '0,00'; 
            } else {
                var stTotal = retornaFormatoMonetario(flSoma);
            }
            
            if(ano > 1){
                jq("input[id^='flValorAno" + (ano - 1) + "_1_" + rpps + "']:first",contexto).val(stTotal);
                jq("span[id^='flValorAno" + (ano - 1) + "_1_" + rpps + "']:first",contexto).html(stTotal);
            }
            
            jq('input#flValorAno' + ano + '_99_' + rpps + '_0_0_4',contexto).val(stTotal);
            jq('span#flValorAno' + ano + '_99_' + rpps + '_0_0_4_label',contexto).html(stTotal);
        } 
    }
    
    /**
     * calcula a porcentagem do valor em relação ao total
     **/
    function calculaPorcentagem(rpps,contexto)
    {
        for(ano = 3; ano >= 1; ano--){
            var flTotal = formataValor(jq('input#flValorAno' + ano + '_99_' + rpps + '_0_0_4',contexto).val());
            jq("input[id^='flValorAno" + ano + "']",contexto).each(function(){
                arDado = (this.id).split('_');
                flValor = formataValor(this.value);
                if (flValor != 0 && flTotal != 0) {
                    stPorc = retornaFormatoMonetario(Math.abs((flValor * 100) / flTotal));
                } else {
                    stPorc = '0.00';
                }
                jq('input#flPorcAno' + ano + '_' + arDado[1] + '_' + arDado[2] + '_' + arDado[3] + '_' + arDado[4] + '_' + arDado[5],contexto).val(stPorc);
                jq('span#flPorcAno' + ano + '_' + arDado[1] + '_' + arDado[2] + '_' + arDado[3] + '_' + arDado[4] + '_' + arDado[5] + '_label',contexto).html(stPorc);
            });  
        }
    }
    
    /**
     * funcao que calcula o total e calcula o percentual
     **/
    function calculaValor(id,table)
    {
        arDado = (id).split('_');
        if ((jq('input#' + id).val()).replace('-','') == '') {
            jq('input#' + id).val('0,00');
        }
        if (arDado[1] != '3') {
            jq('input#' + id).val((jq('input#' + id).val()).replace('-',''));
        }
        var contexto = jq('span table#' + table);
        calculaTotal(arDado[2],contexto);
        calculaPorcentagem(arDado[2],contexto);
    }
</script>
