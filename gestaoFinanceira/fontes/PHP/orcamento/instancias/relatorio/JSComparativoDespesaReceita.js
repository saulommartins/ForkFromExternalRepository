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
    * Codigo javascript para validar os filtros da tela de Filtro do relatorio de Comparativo de Despesa com Receita
    * Data de Criação: 11/09/2009
    * @author Analista      Tonismar Régis Bernardo <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
    * @package URBEM
    * @subpackage ORCAMENTO
    * @ignore 
*/
?>
<script type="text/javascript">
    jq(document).ready( function(){
        jq('#Ok').removeAttr('onclick');
        jq('#Ok').click(function(){
            if (validarDestinacao()) {
                Salvar();
            }
        });
    });
    
function validarDestinacao()
{
    var stMensagem = '';
    if (jq('#inCodDetalhamento').val() != '' && jq('#inCodEspecificacao').val() == '') {
        stMensagem = 'Para selecionar o campo Detalhamento de Destinação é necessário selecionar o campo Especificação de Destinação';
    } else if (jq('#inCodEspecificacao').val() != '' && jq('#inCodDestinacao').val() == '') {
        stMensagem = 'Para selecionar o campo Especificação de Destinação é necessário selecionar o campo Grupo de Destinação';
    } else if (jq('#inCodDestinacao').val() != '' && jq('#inCodUso').val() == '') {
        stMensagem = 'Para selecionar o campo Grupo de Destinação é necessário selecionar o campo IDUSO';
    }
    
    if (stMensagem != '') {
        alertaAviso('@Campo Destinação de Recurso Inválido! ('+stMensagem+')', 'form', 'aviso', '<?php Sessao::getId()?>');
        return false;
    } else {
        return true;
    }
}
</script>
