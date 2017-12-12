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
 * Java Script da  Configuração Inicial LDO
 * Data de Criação: 02/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Heleno Menezes dos Santos <heleno.santos>
 * @author Desenvolvedor: Janilson Mendes P. da Silva <janilson.silva>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.11.03
 */
?>
<script type="text/javascript">

/**
 * Ao termino de carregar o DOM, e' executado esse codigo javascript
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @return void
 */
jq(document).ready(function () {
    // monta a listagem dos tipos de indicadores
    jq.post('OCManterConfiguracao.php', {'stCtrl':'montaListaTipoIndicadores'}, '', 'script');

    // adiciona um name para o span que monta ao adicionar um label, pois nao ha como fazer isso pelo framework. E se e inserido um id
    // ocorre que o span fica abaixo do input, quando deveria ficar ao lado, entao foi realizado isso para poder contornar esse problema
    jq("span[id='']").each(function () {
        this.name = 'spnSimbolo';
    });
});

/**
 * Metodo que limpa os campos da telaMetodo chamado quando clica-se no botao 'incluir' para inserir algum dado na listagem de tipo de indicadores
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @return void
 */
function incluirIndicador()
{
    // envia via post até o oculto os dados necessarios para poder incluir um indicador na listagem
    jq.post('OCManterConfiguracao.php'
            , {   'stCtrl':'incluirIndicador'
                , 'exercicio':jq('#inAnoLDO').val()
                , 'inCodTipoIndicador':jq('#inCodTipoIndicadorLinha').val()
                , 'inLinha':jq('#inLinha').val()
                , 'descricao':escape(jq('#inCodTipoIndicadorLinha :selected').text())
                , 'flIndice':formataValor(jq('#flIndice').val())
              }
            , ''
            , 'script');
}

/**
 * Metodo que limpa os campos da tela
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @return void
 */
function limparIndicador()
{
    jq('#inAnoLDO').val('');
    jq('#inCodTipoIndicador').val(0);
    jq('#inCodTipoIndicadorLinha').val(0);
    jq('#flIndice').val('');
}


/**
 * Metodo que formata o valor para o padrao americano para poder realizar os calculos e transforma o valor em float
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @param  string stValor recebe o valor digitado no campo, porém ele está com o tipo sendo string
 * @return float          retorna o valor formatado
 */
function formataValor(stValor)
{
    return parseFloat(stValor.replace(/\./g, '').replace(',', '.'));
}

</script>
