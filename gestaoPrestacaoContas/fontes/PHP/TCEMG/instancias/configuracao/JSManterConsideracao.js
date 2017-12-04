<?
/**
  * Página de Formulario de Configuração de Consideracoes dos Arquivos
  * Data de Criação: 25/02/2014

  * @author Analista:      Sergio Santos
  * @author Desenvolvedor: Evandro Melos
  *
  * @ignore
  * $Id: JSManterConsideracao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
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
    jQuery("#spnCodigos").html("");
    jQuery("#inMes").val("");
}

function validaCampos(){
  if ( (jQuery("#inCodEntidade").val() != "") && (jQuery("#inMes").val() != "") ) {
    return true;
  }else{
    return false;
  }
}


</script>