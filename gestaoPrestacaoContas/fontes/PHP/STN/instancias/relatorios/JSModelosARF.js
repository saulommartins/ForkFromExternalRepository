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
    * Arquivo JavaScript
    * Data de Criação: 12/06/2009



    * @author Analista      Tonismar Régis Bernardo     <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package      URBEM
    * @subpackage   STN

    $Id:$

*/
?>
<script type="text/javascript">

jq(document).ready(function(){
    montaExercicio();
});

function montaExercicio()
{
    if (jq('#inCodPPA').val() != '') {
        var stPPA = jq('#inCodPPA :selected').text();
        var arPPA = stPPA.split(' a ');
        jq('#stExercicio').addOption(arPPA[0], arPPA[0], false);
        jq('#stExercicio').addOption((Number(arPPA[0])+1), (Number(arPPA[0])+1), false);
        jq('#stExercicio').addOption((Number(arPPA[0])+2), (Number(arPPA[0])+2), false);
        jq('#stExercicio').addOption(arPPA[1], arPPA[1], false);
    } else {
        jq('#stExercicio').removeOption(/./);
    }
}

</script>
                
