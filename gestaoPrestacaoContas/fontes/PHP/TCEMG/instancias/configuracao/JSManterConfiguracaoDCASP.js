<script type="text/javascript">
  function buscaValor(tipoBusca) {
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
  }

  function limpaSpan() {
    jQuery("#spnContas").html("");
    jQuery("#spnContas2").html("");
  }

  function validaCampos() {
    if (jQuery("#inCodCampo").val() != "" && jQuery("#inDescGrupo").val() != "") {
      return true;
    } else {
      return false;
    }
  }

  function limpaCampos() {
    jQuery("#stCampo").html("");
    jQuery("#inCodCampo").val("");
    jQuery("#inDescGrupo").val("");
  }

  function abrePopUpDcasp(arquivo,nomeform,camponum,camponom,campotpreg,campocod,seqarquivo,tipodebusca,sessao,width,height,namepopup){
      if (width == '') {
          width = 800;
      }
      if (height == '') {
          height = 550;
      }
      var x = 0;
      var y = 0;
      var sessaoid = sessao.substr(15,8);
      var sArq = ''+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&campoTpReg='+campotpreg+'&campoCod='+campocod+'&campoSeq='+seqarquivo+'&tipoBusca='+tipodebusca;
      var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +namepopup +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
      eval(sAux);
  }
</script>
