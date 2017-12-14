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
    * Pagina de javascript para relatorio de condominios
    * Data de Criação   : 13/02/2008


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: JSCondominios.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.27 
*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function submeteFiltro(){
    selecionaTodosSelect(document.frm.inCodAtributosSelecionados2); //funcao que seleciona todos no combo multiplo
    selecionaTodosSelect(document.frm.inCodAtributosSelecionados4);

    var atributos2 = document.getElementById('inCodAtributosSelecionados2');
    var atributos4 = document.getElementById('inCodAtributosSelecionados4');
    var size = atributos2.length + atributos4.length;

    if ( size > 4 ) {
        mensagem = "Selecionar até quatro atributos.";
        alertaAviso( mensagem, 'form', 'erro', '<?=Sessao::getId();?>', '../' );
    }else
    if( Valida() ){
        document.frm.submit();
    }

}

</script>
