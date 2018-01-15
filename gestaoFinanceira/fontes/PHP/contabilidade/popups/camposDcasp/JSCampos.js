<script type="text/javascript">
  function insere(nomeTag, nomeCampo, tipoRegistro, codRegistro, seqArquivo) {
    if(eval(window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>') != null)) {
      window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = nomeCampo;
      var campoHidden = eval('window.opener.parent.frames["telaPrincipal"].document.frm.<?=$_REQUEST["campoNom"]?>');
      if(campoHidden != null) campoHidden.value = nomeCampo;
    }
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = nomeTag;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoTpReg']?>.value = tipoRegistro;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoCod']?>.value = codRegistro;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoSeq']?>.value = seqArquivo;

    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.close();
  }
</script>
