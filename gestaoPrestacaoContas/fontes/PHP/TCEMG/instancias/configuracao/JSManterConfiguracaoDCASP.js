<script type="text/javascript">
  function buscaValor(tipoBusca) {
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
  }

  function limpaSpan() {
    jQuery("#spnContas").html("");
  }

  function validaCampos() {
    if (jQuery("#inCodCampo").val() != "" && jQuery("#inDescGrupo").val() != "") {
      return true;
    } else {
      return false;
    }
  }

  function limpaCampos() {
    if (jQuery("#stNomeArquivo").val() == 'BO' || jQuery("#stNomeArquivo").val() == 'BF') {
      jQuery("#stTipoConta").val("OrcamentariaDespesa");
    } else {
      jQuery("#stTipoConta").val("Contabil");
    }

    jQuery("#stCampo").html("");
    jQuery("#inCodCampo").val("");
    jQuery("#inDescGrupo").val("");
  }

  function validaTipoArquivo(idSessao) {
    if (jQuery("#stTipoConta").val() == 'Contabil' && (jQuery("#stNomeArquivo").val() == 'BO' || jQuery("#stNomeArquivo").val() == 'BF')) {
      alertaAviso('Esse tipo de arquivo aceita apenas contas orçamentárias!', 'aviso', 'aviso', idSessao, '../');
      jQuery("#stTipoConta").val('OrcamentariaDespesa');
    } else if ((jQuery("#stTipoConta").val() == 'OrcamentariaDespesa' || jQuery("#stTipoConta").val() == 'OrcamentariaReceita') && (jQuery("#stNomeArquivo").val() != 'BO' && jQuery("#stNomeArquivo").val() != 'BF')) {
      alertaAviso('Esse tipo de arquivo aceita apenas contas contábeis!', 'aviso', 'aviso', idSessao, '../');
      jQuery("#stTipoConta").val('Contabil');
    }
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
