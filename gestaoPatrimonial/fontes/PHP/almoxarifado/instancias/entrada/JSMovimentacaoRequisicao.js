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
    * Página de JavaScript
    * Data de Criação   : 07/11/2005
    
    
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis
    
    * @ignore
    
    * Casos de uso: uc-03.03.02

    $Id: JSMovimentacaoRequisicao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
    
*/

?>
<script type="text/javascript">

function validaUsuarioSecundario (clickForm) {
    BloqueiaFrames(true,false);

    if (document.frm.stAcao.value == 'saida') {
    	window.open('../../popups/saida/FMValidaUsuario.php?clickForm='+clickForm,'','width=500px,height=400px,scrollbars=1');
    } else {
	    document.frm.Ok.onClick = clickForm;
	    document.frm.getElementById('Ok').click();
    }
}

function buscaDado(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function somaQuantidadeLotes(saldo) {
       var inQuantidade = 0;
       if(document.frm.nmQtdLoteLista.length) {
          for(i=0;i<document.frm.nmQtdLoteLista.length;i++) {
             inQuantidade += document.frm.nmQtdLoteLista[i];
          }
       }
       else {
          inQuantidade = document.frm.nmQtdLoteLista.value;
       }
       document.frm.nmQuantidade.value = inQuantidade;
}

function DetalharItem(){
    var stItem = '';
    var arItem = Array(); 
    if (document.frm.boDetalharItem.length) {
      for(var i=0; i<document.frm.boDetalharItem.length;i++) {
          if(document.frm.boDetalharItem[i].checked) {
             stItem = document.frm.boDetalharItem[i].value; 
             arItem = stItem.split('-');
          }
      }
    }
    else {
       if(document.frm.boDetalharItem.checked) {
           stItem = document.frm.boDetalharItem.value;
           arItem = stItem.split('-');
       } 
    }
    document.frm.stCtrl.value = 'preencheSpanDadosItem';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodItem='+arItem[0]+'&inCodMarca='+arItem[1]+'&inCodCentro=' + arItem[2];
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function AlterarLote(inId) {
   document.frm.stCtrl.value = 'preencheDadosLote';
   document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId'+inId;
   document.frm.submit();
   document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function ExcluirLote(inId) {
   document.frm.stCtrl.value = 'excluirLote';
   document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId='+inId;
   document.frm.submit();
   document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function AlterarItem() {
     document.frm.stCtrl.value = 'AlterarItem';
     document.frm.target = 'oculto';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.submit(); 
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function AlterarItemFrota() {
     document.frm.stCtrl.value = 'AlterarItemFrota';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function LimparItem() {
   document.getElementById('stComplemento').value = '';
   document.frm.Ok.disabled = false;
}


function ValidaLote(){
     var erro = false;
     var inTmp;
     var mensagem = "";
     stCampo = document.frm.stLote;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Lote inválido!().";
     }
     stCampo = document.frm.nmQtdLote;
     inTmp = parseInt( stCampo.value ); 
     if( inTmp <= 0 || isNaN(inTmp)) {
         erro = true;
         mensagem += "@Campo Quantidade inválido!().";
     }
     if( erro ){ 
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
     return !erro;
}

function LimparLote() {
   document.getElementById('stLote').value = '';
   document.getElementById('nmQtdLote').value = '';
   document.getElementById('dtValidade').innerHTML = '&nbsp;';
   document.getElementById('dtFabricacao').innerHTML = '&nbsp;';
   document.getElementById('nmSaldoLote').innerHTML = '&nbsp;';
}

function IncluirLote() {
    if (ValidaLote()) {
        stCampos  = '&dtValidade='+document.getElementById('dtValidade').innerHTML;
        stCampos += '&dtFabricacao='+document.getElementById('dtFabricacao').innerHTML;
        stCampos += '&nmSaldoLote='+document.getElementById('nmSaldoLote').innerHTML;
        document.frm.stCtrl.value = 'incluirLote';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+stCampos;
        document.frm.submit(); 
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }
}



function PreencheDadosLote(inId) {
   if(document.frm.stLote.value == '') {
      document.getElementById('dtFabricacao').innerHTML = '&nbsp';
      document.getElementById('dtValidade').innerHTML = '&nbsp';
      document.getElementById('nmSaldoLote').innerHTML = '&nbsp';
      document.getElementById('nmQtdLote').value = '';
   }
   else {
      document.frm.stCtrl.value = 'preencheDadosLote';
      document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIdLote'+inId;
      document.frm.submit(); 
      document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
   }
}

/*####################################################################################################
  # Códigos para o Lightbox                                                                          #
  ####################################################################################################*/
var d = window.parent.frames["telaPrincipal"].document;

function criaLightbox(stCaminho) {
    mostraFundo();
    var objBody = d.getElementsByTagName("body").item(0);
    
    windowWidth = window.parent.frames["telaPrincipal"].innerWidth;
    windowHeight = window.parent.frames["telaPrincipal"].innerHeight;

    var objLightbox = d.createElement("div");
    objLightbox.setAttribute('id','lightbox');
    objLightbox.style.position = 'absolute';
    objLightbox.style.top = '80';
    objLightbox.style.left = 80;
    objLightbox.style.zIndex = '101';
	  objLightbox.style.height = (windowHeight - 160) + 'px';
	  objLightbox.style.width = (windowWidth - 160) + 'px';
	  objLightbox.style.backgroundColor = '#E4EAE4';
    
    var objTituloLightbox = d.createElement("div");
    objTituloLightbox.setAttribute('id','titulo1');
    objTituloLightbox.style.height ='20px';
    objTituloLightbox.innerHTML = '<table width="100%" border=0><tr><td class="titulocabecalho" height="5" width="100%"><table cellspacing=0 cellpadding=0 class="titulocabecalho_gestao" width="100%"><tr><td width="80%">Gestão Patrimonial :: Almoxarifado :: Saída :: Saída por Requisição</td><td width="20%" class="versao"><a href="javascript:fechaLightbox(\''+stCaminho+'\');">fechar</a></tr></table></td></tr></table>';
    objLightbox.appendChild(objTituloLightbox);
    
    var objConteudoLightbox = d.createElement("div");
    objConteudoLightbox.setAttribute('id','conteudolightbox');
    objConteudoLightbox.style.height =  (windowHeight - 180) + 'px';
    objConteudoLightbox.style.overflowY = 'auto';
    objLightbox.appendChild(objConteudoLightbox);
    
    
    
    objBody.appendChild(objLightbox);
}
function fechaLightbox(stCaminho) {
    var objBody     = d.getElementsByTagName("body").item(0);
    var objLightbox = d.getElementById("lightbox");
    
    if( objLightbox ) {
        objBody.removeChild(objLightbox);
    }
    
    escondeFundo();
    
    window.parent.frames["telaPrincipal"].location.href = stCaminho;
}

function escondeFundo() {
    var objFunfo = d.getElementById('fundo');
    
    objFunfo.style.display = 'none';
}

function mostraFundo() {
    var objFunfo = d.getElementById('fundo');
    
    objFunfo.style.display = 'block';
}

function criaFundo() {

    var objBody = d.getElementsByTagName("body").item(0);

    var objFunfo = d.createElement("div");
    objFunfo.setAttribute('id','fundo');
    objFunfo.style.position = 'absolute';
	objFunfo.style.top = '0';
	objFunfo.style.left = '0';
	objFunfo.style.zIndex = '100';
	objFunfo.style.height = '100%';
	objFunfo.style.width = '100%';
	objFunfo.style.display = 'none';
	objFunfo.style.backgroundColor = '#333333';
	objFunfo.style.opacity = '0.40';
	
	objBody.appendChild(objFunfo);
}

// Adds event to window.onload without overwriting currently assigned onload functions.
// Function found at Simon Willison's weblog - http://simon.incutio.com/
function addLoadEvent(func)
{	
	var oldonload = window.onload;
	if (typeof window.onload != 'function'){
    	window.onload = func;
	} else {
		window.onload = function(){
		oldonload();
		func();
		}
	}

}

</script>
