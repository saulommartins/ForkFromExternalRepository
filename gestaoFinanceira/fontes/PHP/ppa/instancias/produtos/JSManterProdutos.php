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
    * Arquivo JavaScript - PAO
    * Data de Criação   : 22/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * $Id $

    * Casos de uso: uc-02.09.11
*/

?>
<script type="text/javascript">

/**
 * Método para verificar se já existe um
 * Produto cadastrado com a mesma descricao.
 */
function checarCadastroProduto(stDescOriginal)
{
    if ($('stDescricao').value != '') {
        if (stDescOriginal == '') {
            // Inclusao
            montaParametrosGET('checarCadastroProduto');
        } else {
            // Alteracao
            if (stDescOriginal != $('stDescricao').value) {
                montaParametrosGET('checarCadastroProduto');
            }
        }
    }
}

function limpar()
{
    $('stDescricao').value = '';
}

function incluir()
{
    if (Valida()) {
        BloqueiaFrames(true,false);
        document.frm.submit();
    }
}

function alterar()
{
    if (Valida()) {
        BloqueiaFrames(true,false);
        document.frm.submit();
    }

}
</script>
