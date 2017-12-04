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
<script>

function incluirPPA()
{
    if (validarPPA()) {
        document.frm.submit();
    }
}

function validarPPA()
{
    if ( trim( $('stAnoInicio').value ) == "" ) {
        alertaAviso('Campo Ano Inicial PPA inválido! ()', 'form', 'aviso', '<?= Sessao::getID() ?>');

        return false;
    }

    if ( trim( $('stAnoFinal').value ) == "" ) {
        alertaAviso('Campo Ano Final PPA inválido! ()', 'form', 'aviso', '<?= Sessao::getID() ?>');

        return false;
    }

    if ( trim( $('boArredondamento').value ) == "" ) {
        alertaAviso('Campo Arredondar Valores do Orçamento inválido! ()', 'form', 'aviso', '<?= Sessao::getID() ?>');

        return false;
    }

    if ($('inCodPrecisao')) {
        if ($('boArredondamento').value == '1' && $('inCodPrecisao').value == "") {
            alertaAviso('Campo Nivel de Arredondamento inválido! ()', 'form', 'aviso', '<?= Sessao::getID() ?>');

            return false;
        }
    }

    return true;
}

</script>
