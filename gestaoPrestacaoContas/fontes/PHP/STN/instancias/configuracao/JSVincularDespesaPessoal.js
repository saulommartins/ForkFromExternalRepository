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
 * Formulario de Vinculo de Despesa Pessoal
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
        jq('#flValor').val('');
        jq('#stPeriodo').selectOptions('', true);
        jq('#nuValorPessoalAtivo').val('0,00');
        jq('#nuValorPessoalInativo').val('0,00');
        jq('#nuValorOutrasDespesas').val('0,00');
        jq('#nuValorIndenizacoes').val('0,00');
        jq('#nuValorDecisaoJudicial').val('0,00');
        jq('#nuValorExercicioAnterior').val('0,00');
        jq('#nuValorInativosPensionista').val('0,00');
    }
    
    function LimparForm(){
        montaParametrosGET('limpaSessao');   
    }
</script>
