<?
/**
  * Página de Cronograma de Execucao Mensal de Desembolso 
  * Data de Criação   : 29/02/2016

  * @author Analista      Ane Caroline
  * @author Desenvolvedor Lisiane Morais

  * @package URBEM
  * @subpackage

  * $Id:$
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/
?>

<script type="text/javascript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaSpan(){
    jQuery("#spnGruposDespesa").html("");
   
}

function validaCampos(){
  if ( (jQuery("#inCodEntidade").val() != "") && (jQuery("#inMes").val() != "") ) {
    return true;
  }else{
    return false;
  }
}


</script>