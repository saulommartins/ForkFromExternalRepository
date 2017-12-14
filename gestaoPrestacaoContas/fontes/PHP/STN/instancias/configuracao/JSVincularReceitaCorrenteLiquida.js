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
 * Formulario de Vinculo de Receita Corrente Liquida
 *
 * @category    Urbem
 * @package     STN
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$        
 */         
?>                                                                                                                     
<script type="text/javascript">
    function limpaFormularioAux(){
        jq('#flValor').val('0,00');
        jq('#nuValorReceitaTributaria').val('0,00');
        jq('#nuValorReceitaContribuicoes').val('0,00');
        jq('#nuValorReceitaPatrominial').val('0,00');
        jq('#nuValorReceitaAgropecuaria').val('0,00');
        jq('#nuValorReceitaIndustrial').val('0,00');
        jq('#nuValorReceitaServicos').val('0,00');
        jq('#nuValorTransferenciaCorrente').val('0,00');
        jq('#nuValorOutrasReceitas').val('0,00');
        jq('#nuValorContribPlanoSSS').val('0,00');
        jq('#nuValorCompensacaoFinanceira').val('0,00');
        jq('#nuValorDeducaoFundeb').val('0,00');
        jq('#stPeriodo').selectOptions('', true);
    }
    
    function LimparForm(){
        montaParametrosGET('limpaSessao');   
    }
</script>
