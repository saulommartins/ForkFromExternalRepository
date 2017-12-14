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
 * Página Javascript de Emissão de Auto de Infração
 * Data de Criacao: 27/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage JavaScript

 * Casos de uso:

 $Id: JSEmitirAutoInfracao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

 */
?>

<script type="text/javascript">

function preencheInfracao(codInfracao)
{
    document.frm.inCodInfracao.value = codInfracao;
    document.frm.inSelInfracao.value = codInfracao;
}

function incluirInfracao()
{
    select = document.frm.inSelInfracao;

    for (var i = 0; i < select.options.length; ++i) {
        if (select.options[i].selected) {
            document.frm.stHdnNomeInfracao.value = select.options[i].innerHTML;
            montaParametrosGET('incluirInfracao');
        }
    }
}

</script>
