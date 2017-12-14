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
    * Página de Formulário para configuração
    * Data de Criação   : 16/04/2007


    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-06.04.00
*/

/*
$Log$
Revision 1.2  2007/06/12 18:33:54  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.1  2007/04/18 15:01:15  hboaventura
Arquivos para geração do TCMGO



*/
?>
<script type="text/javascript">
    function limparGestor(){
        document.getElementById( 'stNomCGMGestor' ).innerHTML = '&nbsp;';
        document.getElementById( 'inCGMGestor' ).value = '';
        document.getElementById( 'stCargoGestor' ).value = '';
        document.getElementById( 'dtInicio' ).value = '';
        document.getElementById( 'dtTermino' ).value = '';
        document.getElementById('btIncluir').value = 'Incluir';
        document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'incluiGestor\', \'inCGMGestor,stNomCGMGestor,stCargoGestor,dtInicio,dtTermino\')');
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