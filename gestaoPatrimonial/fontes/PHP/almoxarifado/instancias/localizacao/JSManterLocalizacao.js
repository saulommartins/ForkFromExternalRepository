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
<?php
/**
    * Arquivo JavaScript
    * Data de Criação   : 30/01/2006


    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo D. Schreiner

    * @ignore

    * Casos de uso: uc-03.03.14

*/

/*
$Log$
Revision 1.8  2006/11/20 15:46:27  andre.almeida
Bug #7146#

Revision 1.7  2006/10/18 17:50:12  andre.almeida
Bug #6874#
Bug #6988#
Bug #7146#
Bug #7173#
Bug #7254#
Bug #6944#
Bug #6987#
Bug #6989#

Revision 1.6  2006/07/07 18:38:04  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:53  diego


*/
?>

<script type="text/javascript">

function VerificaLocalizacao(obj,vlr,prm){
    var tamanho = prm.length;
    var valor   = vlr.length;
    if(tamanho != valor){
        mensagem = "@O campo Localização não confere com a máscara.";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
    }
}

function VerificaItemMarca(prm){
    if(prm == 'Item'){
        buscaValorBscInner( '../../../../../../gestaoPatrimonial/fontes/PHP/almoxarifado/popups/catalogo/OCManterItem.php?<?=Sessao::getId();?>&nomCampoUnidade=stUnidadeMedida', 'frm', 'inCodItem','stNomItem', '' );
    }else if(prm == 'Marca'){
        buscaValorBscInner( '../../../../../../gestaoPatrimonial/fontes/PHP/almoxarifado/popups/marca/OCManterMarca.php?<?=Sessao::getId();?>', 'frm', 'inCodMarca','stNomMarca', '' );
    }
}

function goOcultoListagem(stControle,cmp){
    document.frm.action       = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target       = 'oculto';
    document.frm.stCtrl.value = stControle;
    document.frm.submit();

    setTimeout("VerificaItemMarca('"+cmp+"')",1600);

    document.frm.action       = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target       = 'telaPrincipal';
}

 function goOcultoProcessamento(stControle,cmp){

  document.frm.action       = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
  document.frm.target       = 'oculto';
  document.frm.stCtrl.value = stControle;
  document.frm.submit();

  document.frm.action       = '<?=$pgProc;?>?<?=Sessao::getId();?>';

 }

 function goOculto(stControle,verifica){

   document.frm.stCtrl.value        = stControle;
   document.frm.HdnNomItem.value    = document.getElementById("stNomItem").innerHTML;
   document.frm.HdnNomUnidade.value = document.getElementById("stUnidadeMedida").innerHTML;
   document.frm.HdnNomMarca.value   = document.getElementById("stNomMarca").innerHTML;

   var erro     = false;
   var mensagem = "";

   campo  = document.frm.inCodItem.value.length;
   vcampo = document.frm.inCodItem.value;

   if(campo == 0 | vcampo == '0'){
    mensagem += "@O campo Código do Item é obrigatório";
    erro = true;
   }

   campo  = document.frm.inCodMarca.value.length;
   vcampo = document.frm.inCodMarca.value;

   if(campo == 0 | vcampo == '0'){
    mensagem += "@O campo Código da Marca é obrigatório";
    erro = true;
   }

   if(erro & verifica){
    alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
   }else{
    document.frm.action    = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target    = 'oculto';
    document.frm.submit();
    document.frm.action    = '<?=$pgProc;?>?<?=Sessao::getId();?>';
   }
 }

 function excluirItemLocalidade(inIndice){
  document.frm.stCtrl.value = 'excluirItemLocalidade';
  document.frm.target       = "oculto";
  document.frm.action       = '<?=$pgOcul;?>?<?=Sessao::getId();?>&id='+inIndice;
  document.frm.submit();
  document.frm.action       = '<?=$pgProc;?>?<?=Sessao::getId();?>';
 }

function LimparDadosItem() {
    document.frm.inCodItem.value = "";
    document.getElementById('stNomItem').innerHTML = "&nbsp;"
    document.getElementById('stUnidadeMedida').innerHTML = "&nbsp;";
    document.frm.inCodMarca.value = "";
    document.getElementById('stNomMarca').innerHTML = "&nbsp;";
}

function LimpaTela() {
    LimparDadosItem();
    document.frm.inCodAlmoxarifado.value = "";
    document.getElementById('spnListaLocalizacao').innerHTML = "";
    document.getElementById('spnListaValores').innerHTML = "";
    document.frm.stCtrl.value = 'LimpaTela';
    document.frm.action    = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target    = 'oculto';
    document.frm.submit();
    document.frm.action    = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

</script>

