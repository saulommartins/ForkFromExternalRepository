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
    if ((jQuery("#inCodEntidade").val() != "") && (jQuery("#inMes").val() != "")) {
      return true;
    } else {
      return false;
    }
  }
</script>
