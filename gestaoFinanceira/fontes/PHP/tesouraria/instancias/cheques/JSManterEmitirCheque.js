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
 * Formulario de Emissão de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$        
 */         
?>
<script>

    function limparCheque()
    {
        if(jq("#stTipoEmissaoCheque").val() != "transferencia"){
            jq("#inCodBancoTxt").val  ("");
            jq("#inCodBanco").val     ("");
            jq("#stNumAgenciaTxt").val("");
            jq("#stNumAgencia").val   ("");
            jq("#stContaCorrente").val("");
        }
        jq("#stNumCheque").val    ("");
        jq("#flValorCheque").val  ("");
        jq("#stDescricao").val    ("");
    }
    
    function limparCheques()
    {
        limparCheque();
        jq("#spnCheque").html ("");
    }
    
    function verificaDados()
    {
        stMensagem = '';
        if(jq('#stExercicio').val() == ''){
            stMensagem = 'Preencha o campo exercicio';  
        } else if (jq('#inCodEntidade').val() == ''){
            stMensagem = 'Preencha o campo entidade';   
        }
        if(stMensagem == ''){
            Salvar();   
        } else {
            alertaAviso('@'+stMensagem,'form','erro','".<?php echo Sessao::getId() ?>."');
        }
        
    }
    
    function selecionarTodos()
    {
        jq(':checkbox').each(function(){
                                if(jq('#boTodos').attr('checked') == true){
                                    this.checked = true;
                                } else {
                                    this.checked = false;
                                }
                             });
    }
    
</script>
