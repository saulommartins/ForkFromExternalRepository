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
  * Página de JavaScript de Configuração de Receita/Despesa Extra por Fonte de Recurso
  * Data de Criação: 05/11/2015

  * @author Analista: Valtair Santos
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: JSManterReceitaDespesaExtraRecurso.js 63906 2015-11-05 12:31:01Z franver $
  * $Revision: 63906 $
  * $Author: franver $
  * $Date: 2015-11-05 10:31:01 -0200 (Thu, 05 Nov 2015) $
*/
?>
<script type="text/javascript">

jQuery( document ).ready(function() {
    if (jQuery('input[name="boIndicadorSaldoContasRecurso"]:checked').val() == 't') {
        montaParametrosGET('consultarConfiguracao');
        montaParametrosGET('montaContas');
    }
});

function ValidaContas() {
    var erro = false;
    var mensagem = "";
    if (document.frm.stCodEstrutural) {
        if ( trim(document.frm.stCodEstrutural.value) == "" ) {
            erro = true;
            mensagem += "@Campo Código Estrutural inválido!()";
        }
    }
    if (document.getElementById("stDescEstruturalConta")) {
        if ( trim( document.getElementById("stDescEstruturalConta").innerHTML ) == "&nbsp;" ) {
            erro = true;mensagem += "@Campo Descrição de Código Estrutural inválido!()";
        }
    }
    stCampo = document.frm.stCodEstrutural;
    if (stCampo) {
        if ( stCampo.value.length < 1 ) {
            erro = true;
            mensagem += "@Campoinválido!("+stCampo.value+")";
        }
    }

    if (erro) {
        alertaAviso(mensagem,"form","erro","<?=Sessao::getId()?>", "../");
    }
    return !erro;
}
 
</script>