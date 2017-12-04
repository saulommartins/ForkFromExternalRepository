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
 * JavaScript da tela de Filtro do relatório Relação de Despesa Orçamentária
 * Data de Criação   : 02/04/2009
 
 
 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Pacuslki Schitz
     
 * @package URBEM
 * @subpackage 
 
 $Id:$
*/


?>
<script>

function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    jq('#inCodEntidade option').each(function(){this.selected = 'selected'});
    document.frm.action = 'OCRelacaoDespesaOrcamentaria.php?stCtrl='+variavel+'&<?=Sessao::getId();?>';
    document.frm.submit();
    jq('#inCodEntidade option').each(function(){this.selected = ''});
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

</script>
                                
