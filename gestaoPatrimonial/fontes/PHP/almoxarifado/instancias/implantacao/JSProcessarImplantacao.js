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

/*
    * Página Oculta de Processar Implantacao
    * Data de Criação   : 08/06/2006


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore
    
    $Id: JSProcessarImplantacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

// Casos de uso: uc-03.03.16
    

*/

?>

<script type="text/javascript">

function excluiItem(stControle, inId, sincrono ){
    param = '&inId='+inId;
    executaFuncaoAjax( stControle, param, sincrono );
}

function alteraLote(inIdLote){
    param = '&inIdLote='+inIdLote+'&inIdItem='+document.frm.inIdItem.value;
    executaFuncaoAjax( 'alteraLote', param );
}

function excluiLote(inIdLote){
    param = '&inIdLote='+inIdLote+'&inIdItem='+document.frm.inIdItem.value;
    executaFuncaoAjax( 'excluiLote', param );
}

function alteraItem(stControle, inId, sincrono ){
    param = '&inId='+inId;
    executaFuncaoAjax( stControle, param, sincrono );
}

function Limpar(){
    executaFuncaoAjax('limpaTodaTela');
}

function limpaFormulariomontaListaItensExtra(){
    executaFuncaoAjax('limpaItens');
}

</script>
