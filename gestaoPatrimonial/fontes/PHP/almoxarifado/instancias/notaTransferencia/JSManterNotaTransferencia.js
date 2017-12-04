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
    * Página de Formulario para Requisição
    * Data de criação : 17/04/2006


    * @author Analista    : Diego Victoria
    * @author Programador : Rodrigo

    * @ignore

   Caso de uso: uc-03.03.08
*/

/*
$Log$
Revision 1.9  2007/08/06 19:02:21  leandro.zis
Corrigido nota de transferencia

Revision 1.8  2007/07/19 21:44:46  leandro.zis
Bug #9612#, Bug #9604#, Bug #9601#, Bug #9482#, Bug #9614#

Revision 1.7  2007/06/26 20:56:45  bruce
Bug#9482#

Revision 1.6  2006/12/08 18:57:30  leandro.zis
corrigindo

Revision 1.5  2006/07/06 14:02:54  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:53  diego


*/                          
?>

<script type="text/javascript">

function limparItem() {
   document.frm.inCodItem.value = '';
   document.getElementById('stNomItem').innerHTML = '&nbsp;';
   document.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';
   limpaSelect(document.frm.inCodMarca,1);
   limpaSelect(document.frm.inCodCentroCusto,1);
   document.getElementById('inSaldo').innerHTML = '&nbsp;';
   document.frm.nuQuantidade.value = '';
}

function buscaValor(valor){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = valor;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=$sessao->id;?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function carregaFormCentroCustoDestino()
{
   montaParametrosGET('montaCentroCustoDestino');
}

function montaAlmoxarifadoLabel(origem, destino){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'montaAlmoxarifadosLabel';
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=$sessao->id;?>&inCodAlmoxarifadoOrigem='+origem+'&inCodAlmoxarifadoDestino='+destino;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function alterarItem(inIndice) {
    document.frm.stCtrl.value = 'carregaItem';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=$sessao->id;?>&inId='+inIndice;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=$sessao->id;?>';
}

function excluirItem(inIndice){
    document.frm.stCtrl.value = 'excluirItem';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=$sessao->id;?>&id='+inIndice;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=$sessao->id;?>';
}

function buscaItemMarca(){
    parent.window.frames['telaPrincipal'].document.frm.stCtrl.value = 'carregaMarca';
    parent.window.frames['telaPrincipal'].document.frm.target       = 'oculto';
    parent.window.frames['telaPrincipal'].document.frm.action       = '<?=$pgOcul;?>?<?=$sessao->id;?>';
    parent.window.frames['telaPrincipal'].document.frm.submit();
    parent.window.frames['telaPrincipal'].document.frm.action       = '<?=$pgProc;?>?<?=$sessao->id;?>';
}

function limpaCamposItens() {
  document.frm.inCodItem.value = '';
  document.getElementById('stNomItem').innerHTML = '&nbsp;';
  document.getElementById('stUnidadeMedida').innerHTML = '&nbsp;';
  document.frm.inCodMarca.value = '';
  limpaSelect(document.frm.inCodMarca, 1);
  document.frm.inCodCentroCusto.value = '';
  limpaSelect(document.frm.inCodCentroCusto, 1);
  document.getElementById('inSaldo').innerHTML = '&nbsp;';
  document.frm.nuQuantidade.value = '';
  document.getElementById('spnAtributos').innerHTML = '';
}

</script>
