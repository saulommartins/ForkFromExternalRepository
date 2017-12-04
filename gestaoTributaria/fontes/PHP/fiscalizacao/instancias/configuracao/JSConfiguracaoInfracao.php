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
function LimparConfiguracaoInfracao()
{
    document.getElementById('stDocumentoNaoEntregue').innerHTML = '&nbsp;';
    document.getElementById('stDocumentoEntregueForaPrazo').innerHTML = '&nbsp;';
    document.getElementById('stDocumentoEntregueParcial').innerHTML = '&nbsp;';
    document.getElementById('stPagamentoMenos').innerHTML = '&nbsp;';
    document.getElementById('stDeclaracaoMenor').innerHTML = '&nbsp;';

    document.getElementById('inDocumentoNaoEntregue').value = '';
    document.getElementById('inDocumentoEntregueForaPrazo').value = '';
    document.getElementById('inDocumentoEntregueParcial').value = '';
    document.getElementById('inPagamentoMenos').value = '';
    document.getElementById('inDeclaracaoMenor').value = '';
}
</script>
