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

    $Revision: 56934 $
    $Name$
    $Author: gelson $
    $Date: 2014-01-08 17:46:44 -0200 (Wed, 08 Jan 2014) $

    * Casos de uso : uc-06.04.00
*/

/*
$Log$
Revision 1.3  2007/06/12 18:33:54  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/05/24 13:18:50  hboaventura
Arquivos para geração do TCMGO

Revision 1.1  2007/05/18 14:49:12  hboaventura
Arquivos para geração do TCMGO



*/
?>
<script type="text/javascript">
        
    function excluirListaItens( id,categoria,tipo_lancamento,sub_tipo_lancamento ){
        ajaxJavaScript('<?=$pgOcul."?".Sessao::getId()?>&id='+id+'&categoria='+categoria+'&tipo_lancamento='+tipo_lancamento+'&sub_tipo_lancamento='+sub_tipo_lancamento,'excluirListaItens');
    }
    
    function limpaCombos(){
        document.getElementById('inTipoLancamento').selectedIndex = 0;
        document.getElementById('spnSubTipo').innerHTML = '';
        document.getElementById('spnContas').innerHTML = '';
        document.getElementById('inCodConta').value = '';
        document.getElementById('stConta').innerHTML = '&nbsp';
    }

</script>