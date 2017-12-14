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
<script type='text/javascript'>

    function abreListagemBem(inIdInventario, stExercicio, inCodOrgao, inCodLocal){

        var link    = "<?=CAM_GP_PAT_INSTANCIAS.'inventario/LSConsultaInventarioHistoricoBem.php';?>";
        var sessao  = "<?=Sessao::getId();?>";
        //var params  = "&inIdInventario="+inIdInventario;
        var params  = "&stExercicio="+stExercicio;        
        params += "&inCodOrgao="+inCodOrgao;
        params += "&inCodLocal="+inCodLocal;

        // Abre a listagem que exibe os bens que estão do Inventário para o grupo e local selecionado.
        abrePopUp(link, '', '', '', sessao+params, '','');
    }

    function abreListagemBemInicial(inIdInventario, inCodOrgao, inCodLocal){

        var link    = "<?=CAM_GP_PAT_INSTANCIAS.'inventario/LSConsultaInventarioHistoricoBem.php';?>";
        var sessao  = "<?=Sessao::getId();?>";
        var params  = "&inCodOrgao="+inCodOrgao;        
        params += "&inCodLocal="+inCodLocal;

        // Abre a listagem que exibe os bens que estão do Inventário para o grupo e local selecionado.
        abrePopUp(link, '', '', '', sessao+params, '','');
    }


</script>
