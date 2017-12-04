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
* Página de JavaScript da Transferencia
* Data de Criação: 05/01/2006


* @author Analista:
* @author Desenvolvedor: Leandro André Zis

* @ignore

* Casos de uso: uc-03.03.09
*/

/*

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

function DetalharItem(){
    montaParametrosGET('preencheSpanDadosItem');
}

function desabilitaIncluirLote(){
    var nmQtdLote = jq('#nmQtdLote').val();
    var stLote = jq('#stLote').val();

    if((parseFloat(nmQtdLote) == 0.0)||(nmQtdLote == 'NaN')||(nmQtdLote == '')||(stLote=='')){
       jq('#btnIncluirLote').attr( "disabled", "disabled" );
    } else {
       jq('#btnIncluirLote').attr( "disabled", "" );
    }
}

function LimparLote() {
    document.frm.stLote.value = '';
    document.getElementById('dtFabricacao').innerHTML = '&nbsp';
    document.getElementById('dtValidade').innerHTML = '&nbsp';
    document.getElementById('nmSaldoLote').innerHTML = '&nbsp';
    document.frm.nmQtdLote.value = '';
}

function AlterarLote(inId){
    executaFuncaoAjax('AlteraLote', '&inId='+inId+'&inIDPos='+document.frm.inIDPos.value);
}

function ExcluirLote(inId){
    executaFuncaoAjax('ExcluirLote', '&inId='+inId+'&inIDPos='+document.frm.inIDPos.value);
}


/*###########################getElementById('dtFabricacao').#########################################################################
  # Códigos para o Lightbox                                                                          #
  ####################################################################################################*/
var d = window.parent.frames["telaPrincipal"].document;

function criaLightbox(stCaminho,acao) {
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
	if(acao == 'saida') {
		objTituloLightbox.innerHTML = '<table width="100%" border=0><tr><td class="titulocabecalho" height="5" width="100%"><table cellspacing=0 cellpadding=0 class="titulocabecalho_gestao" width="100%"><tr><td width="80%">Gestão Patrimonial :: Almoxarifado :: Saída :: Saída por Transferência</td><td width="20%" class="versao"><a href="javascript:fechaLightbox(\''+stCaminho+'\');">fechar</a></tr></table></td></tr></table>';
	} else {
		objTituloLightbox.innerHTML = '<table width="100%" border=0><tr><td class="titulocabecalho" height="5" width="100%"><table cellspacing=0 cellpadding=0 class="titulocabecalho_gestao" width="100%"><tr><td width="80%">Gestão Patrimonial :: Almoxarifado :: Saída :: Entrada por Transferência</td><td width="20%" class="versao"><a href="javascript:fechaLightbox(\''+stCaminho+'\');">fechar</a></tr></table></td></tr></table>';
	}
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

  

</script>
