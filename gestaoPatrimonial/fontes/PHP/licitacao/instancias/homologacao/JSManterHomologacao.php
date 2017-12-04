<?php
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
?>
<?php
/**
    * Regra para Arquivo Coletora
    *
    *
    * @date 11/08/2010
    * @author Analista: Gelson
    * @author Desenvol: Tonismar
    *
    * @ignore
**/
?>
<script type="text/javascript">
    
    function limpaCampos () {
        jQuery('#inCodModalidade').val('');
        jQuery('#inCodLicitacao').val('');
        jQuery('#stDtHomologacao').val('');
        jQuery('#stHoraHomologacao').val('');
        jQuery('#stProcesso').html('&nbsp;');
        jQuery('#inCodModalidade').attr('disabled',true);
        jQuery('#inCodLicitacao').find('option').remove().end().append('<option value=\'\'>Selecione</option>').val('');
    }
    
</script>
<?php

?>