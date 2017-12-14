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
    * Página Oculta de Processar Implantacao
    * Data de Criação   : 08/06/2006


    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

// Casos de uso: uc-03.03.17

*/

/*
$Log$
Revision 1.8  2007/07/19 12:32:54  hboaventura
Correção da Movimentação Diversa para preencher os parametros dinâmicos

Revision 1.7  2007/03/14 15:18:54  tonismar
bug #8698

Revision 1.6  2007/01/17 19:03:41  leandro.zis
Bug #7873#

Revision 1.5  2006/07/10 19:40:02  rodrigo
Adicionado nos componentes de itens,marca e centro de custa a função ajax para manipulação dos dados.

Revision 1.4  2006/07/06 14:00:36  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:53  diego


*/

?>

<script type="text/javascript">

function Limpar(){
	executaFuncaoAjax('limparSessao');
    executaFuncaoAjax('montaCampoAlmoxarifado');
}

function limpaFormulariomontaListaItensExtra(){
   document.getElementById("spnFormLotes").innerHTML     = "";
   document.frm.nuQuantidade.disabled = false;
   executaFuncaoAjax('limparLotesSessao');
}

function limpaFormulariomontaListaItens(){

}

function limparFormulario(){	
    $('spnAtributos').innerHTML = '';
    $('nuQuantidade').disabled = false; 
    $('spnDadosItem').innerHTML = '';
    $('inCodItem').value = '';
    $('stNomItem').innerHTML = '&nbsp;';
	$('inCodMarca').value='';
	$('stNomMarca').innerHTML = '&nbsp;';
	$('inCodCentroCusto').value='';
	$('stNomCentroCusto').innerHTML='&nbsp;';
	$('nuQuantidade').value='0,0000';
	$('nuVlTotal').value='0,00';
	$('spnFormLotes').innerHTML = '';
	$('inCodigoBarras').value='	';
	$('botaoIncluir').value = 'Incluir';
    $('botaoIncluir').setAttribute('onclick', 'montaParametrosGET("incluirmontaListaItens");');
    $('inCodItem').disabled  = false;
    $('stNomItem').disabled  = false;
	$('imgBuscar').style.display   = 'inline';
}

function excluiItem(stControle, inId, sincrono ){
    param = '&inId='+inId;
    executaFuncaoAjax( stControle, param, sincrono );
}

function alteraItem(stControle, inId,inCodItem, sincrono ){
    param = '&inId='+inId + '&inCodItem=' + inCodItem;
    executaFuncaoAjax( stControle, param, sincrono );
}

function excluiLote(inIdLote ){
    param = '&inIdLote='+inIdLote;
    executaFuncaoAjax( 'excluiLote', param );
}

</script>
