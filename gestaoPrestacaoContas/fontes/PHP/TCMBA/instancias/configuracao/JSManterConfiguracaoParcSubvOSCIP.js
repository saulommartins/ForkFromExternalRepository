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
    *                        
    **********************************************************************************                                                           
*/
</script>
<?php
/**
  * Página de JavaScript de Configuração de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: JSManterConfiguracaoParcSubvOSCIP.js 63979 2015-11-13 13:48:35Z evandro $
  * $Revision: 63979 $
  * $Author: evandro $
  * $Date: 2015-11-13 11:48:35 -0200 (Fri, 13 Nov 2015) $
*/
?>
<script type="text/javascript">
jQuery( document ).ready(function() {
    montaParametrosGET('consultaTermoParceria');
});

function LimparFormulario(){
    var stExercicioProcesso = jQuery("#stExercicioProcesso").val();
    jQuery("input[type='text']").each(function(){
        jQuery(this).val('');
    });
    jQuery("textarea").each(function(){
        jQuery(this).val('');
    });
    jQuery("td[class='fakefield']").each(function(){
        jQuery(this).html('&nbsp');
    });
    
    jQuery("#spnDotacoes").html('&nbsp');
    
    jQuery("#stExercicioProcesso").val(stExercicioProcesso);
    montaParametrosGET('consultaTermoParceria');
}

</script>