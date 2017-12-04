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
 * Arquivo JS - Notas Explicativas
 *
 * Data de Criação: 23/06/2009
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id: $
*/
?>

<script type="text/javascript">

/**
 * Quando o formulário estiver pronto, ele adiciona ao click do botão incluir a chamada do metodo 'controleCadastro'
 */
jq(document).ready(function(){
    chamaOcultoPost('carregarListagem');
    jq('#incluir').bind('click', function(){(chamaOcultoPost('incluirListaCadastro'))});
})

/**
 * chamaOcultoPost
 *
 * realiza o controle das chamadas para o oculto, passando os valores por POST e passando a opção do controle do oculto como parametro
 * e os valores a serem mandados são pegos via JS diretamente pelos valores dos campos
 *
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @return void
 */
function chamaOcultoPost(controle)
{
    // Parametros necessários para se passar. Não tirar dessa identação, pois é necessário que estejam chaves e valores 'colados'
    var arOptions = {
        'stCtrl':controle
      , 'inCodAcao':jq('#inCodAcao').val()
      , 'stDtInicial':jq('#stDtInicial').val()
      , 'stDtFinal':jq('#stDtFinal').val()
      , 'stAnexo':jq('#stAnexo').val()
      , 'stNotaExplicativa':jq('#stNotaExplicativa').val()
      , 'id':jq('#stHdnId').val()
    };

    // Manda para o oculto o array de dados necessários e passa-se a forma que irá ser interpretado o retorno do oculto, no caso
    // como sendo um código javascript
    jq.post('OCManterNotasExplicativas.php', arOptions, '', 'script');
}

/**
 * limpaCadastro
 *
 * 'limpa' a tela de cadastro, fazendo o formulário ficar em seu estado inicial, sem nenhum valor e todos os campos habilitados
 *
 * @author Analista      : Tonismar Regis Bernardo <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor : Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 *
 * @return void
 */
function limparCadastro()
{
    jq('#inCodAcao').val('').attr('disabled', false);
    jq('#stNotaExplicativa').val('').attr('disabled', false);
    jq('#stDtInicial').val('').attr('disabled', false);
    jq('#stDtFinal').val('').attr('disabled', false);
    jq('#stHdnId').val('').attr('disabled', false);
    jq('#stAnexo').val('');
    jq('#incluir').val('Incluir').unbind('click').bind('click', function(){chamaOcultoPost("incluirListaCadastro")});
    jq('#limpar').attr('disabled', false);
}

</script>
