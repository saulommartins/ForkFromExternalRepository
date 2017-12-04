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
/*
    * Página de Javascript para Vinculo de unidade orcamentaria da GF e organograma
    * Data de Criação: 20/07/2009


    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
    
    * @package URBEM
    * @subpackage 

    * @ignore 

    $Id: $ 
*/

?>

<script type='text/javascript'>
    // Varre as combos de órgão que tem algum valor setado, caso tenham, realizam a chamada para preencher elas
    jq(document).ready(function(){
        var id;
        jq('#tblOrgao select[name*="Orgao"]').each(function(){
            if (this.value != '') {
                jq.post(    'OCManterUnidadeOrcamentaria.php'
                        ,   {    'stCtrl': 'carregaUnidades'
                               , 'cbmId': this.id
                               , 'inNumOrgao':this.value
                               , 'boCarregaInicio':true
                            }
                        , ''
                        , 'script'
                )
            }
        });
        
    });
    
</script>

