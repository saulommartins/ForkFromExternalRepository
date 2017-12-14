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
    * Arquivo com funcoes JavaScript para Consulta de Arrecadacao
    * Data de Criação: 09/06/2005


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 30566 $
    $Name$
    $Autor: $
    $Date: 2007-11-22 15:31:15 -0200 (Qui, 22 Nov 2007) $

    * Casos de uso: uc-04.05.49
*/

/*
$Log$
Revision 1.6  2007/04/24 12:54:11  souzadl
alteração

Revision 1.5  2006/08/08 17:43:13  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function alterarEvento( inId ) {
    executaFuncaoAjax( 'montaAlterarEvento', '&inId='+inId );
}

function excluirEvento( inId ) {
    executaFuncaoAjax( 'excluirEvento', '&inId='+inId );
}

function limparEventoLista() {
    document.frm.inContrato.value = '';
    document.getElementById('inNomCGM').innerHTML = '&nbsp;';
    document.frm.inCodigoEvento.value = '';
    document.getElementById('stEvento').innerHTML = '&nbsp;';
    document.getElementById('spnDadosLoteEvento').innerHTML = '';
    document.frm.btAlterar.disabled = true;
    document.frm.btIncluir.disabled = false;
}


function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget
}

</script>