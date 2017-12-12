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
<?
/**
    * Página Formulário - Parâmetros do Arquivo 
    * Data de Criação   : 30/08/2007


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/
?>

<script type="text/javascript">
    function limparResponsavel(){
        document.getElementById( 'stNomCGM' ).innerHTML = '&nbsp;';
        document.getElementById( 'inNumCGM' ).value = '';
        document.getElementById( 'stCargo' ).value = '';
        document.getElementById( 'stFuncao' ).value = '';
        document.getElementById( 'stDtInicio' ).value = '';
        document.getElementById( 'stDtFim' ).value = '';
        document.getElementById('btIncluir').value = 'Incluir';
        document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'incluiResponsavel\', \'inNumCGM,stNomCGM,stCargo,stFuncao,stDtInicio,stDtFim\')');
    }
    
    function excluirListaItens( id ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id,'excluirListaItens');
    }
    
    function montaAlteracaoLista( id ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id,'montaAlteracaoLista');
    }
    
    /*function alterarListaItens( id ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id,'alterarListaItens');
    }*/

</script>