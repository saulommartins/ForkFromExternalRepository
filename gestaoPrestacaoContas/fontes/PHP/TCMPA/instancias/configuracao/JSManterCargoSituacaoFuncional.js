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

<script type="text/javascript">

function marcaArraySelecionados()
{
    var arraySelecionados = $("arCargosSelecionados");
    var indice =0;
    
    for(indice = 0; indice<arraySelecionados.options.length;indice++)
    {
        arraySelecionados.options[indice].selected = true;                
    }    
}

function excluirItemLista(inIndice)
{
    var url = '<?="OCManterCargoSituacaoFuncional.php?".Sessao::getId()?>&id='+inIndice;
    ajaxJavaScript(url,'excluirItemLista');       
}

//fazer a parte de resetar as combos
function resetElementos()
{
    $('idCodTipoCargo').options.selectedIndex = 0;
    $('inCodSituacao').options.selectedIndex = 0;
    var indice = 0;
    
    arrayDisponiveis = $('arCargosDisponiveis');
    arraySelecionados = $('arCargosSelecionados');
    
    for(indice = 0; indice<arraySelecionados.options.length;indice++)
    {
        arrayDisponiveis.options.add(arraySelecionados.options[indice]);
    }
}

function alterar(inIndice,subDivisao)
{
    var url = '<?="OCManterCargoSituacaoFuncional.php?".Sessao::getId()?>&id='+inIndice + '&inCodSubDivisao=' + subDivisao;
    ajaxJavaScript(url,'carregaFormularioAlteracao');       
}

function alterarListaIntens(inIndice)
{
    var cargos = '';
    cargos = cargos.toString();
    
    arraySelecionados = $('arCargosSelecionados');
    
    for(indice = 0; indice<arraySelecionados.options.length;indice++) {
        cargos += '&cargos['+indice+']='+arraySelecionados.options[indice].value;        
    }

    var url = '<?="OCManterCargoSituacaoFuncional.php?".Sessao::getId()?>&id='+inIndice
            + '&inCodSubDivisao=' + $('idCodSubDivisao').value + '&inCodTipoCargo=' + $('idCodTipoCargo').value
            + '&inCodRegime=' + $('idCodRegime').value + '&inCodSituacao=' + $('inCodSituacao').value
            +  cargos;
            
    ajaxJavaScript(url,'alterarListaItens');
}

</script>

