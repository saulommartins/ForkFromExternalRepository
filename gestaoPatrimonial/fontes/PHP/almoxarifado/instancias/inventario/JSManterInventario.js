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
 * Página de Oculto de Manter Inventario
 * Data de Criação: 02/10/2007
 
 
 * @author Analista: Anelise Schwengber
 * @author Desenvolvedor: Andre Almeida
 
 * @ignore

 $Id:$

 * Casos de uso: uc-03.03.15
 
 */

?>

<script type="text/javascript">

    function alterarItem ( inIdItem, stCampo ) {
        var stParametros = "&inIdItem="+inIdItem;
        executaFuncaoAjax( 'montaAlterarItem', stParametros, true );
            
        if(stCampo != undefined){
            document.getElementsByName(stCampo)[0].focus();
        } else {
            document.getElementById('nuQuantidadeApurada').focus();
        }
    }

    function excluirItem ( inIdItem ) {
        var stParametros = "&inIdItem="+inIdItem+"&stAcao="+document.frm.stAcao.value;
        executaFuncaoAjax( 'excluirItem', stParametros );
    }

    function habilitaJustificativa( campo_quantidade_apurada ) {
        var arNomeCampo = campo_quantidade_apurada.id.split("_");
        var inIdCentro = arNomeCampo[1] - 1;
        var stParametros = "&inIdCentro="+inIdCentro+"&valor_apurado="+campo_quantidade_apurada.value;
        executaFuncaoAjax( 'habilitaJustificativa', stParametros );
    } 

</script>
