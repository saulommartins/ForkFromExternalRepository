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
* Data de Criação   : ???


* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.01
*/

/*
$Log$
Revision 1.5  2006/08/08 17:47:58  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

document.frm.stMascaraRegistro.focus();

function retiraCaracteresEspeciais(obj){
	objValue 	= obj.value;
 	for (var i = 0; i < objValue.length; i++ ){
		letra = objValue.charAt(i);
		if(letra == "~" || letra == "´" || letra == "`" || letra == "¨" || letra == "^"){
				obj.value = objValue.substring(0,i) + objValue.substring(i+1, objValue.length);
		}
	}
}

function validaDataBase(obj){
    if(obj.value<1 || obj.value>12){
		mensagem = 'O valor do campo Data-base deve estar entre 1 e 12';
		alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
		document.frm.inDataBase.value="";
	}
}

function validaMascaraRegistro(obj,pEvent){
	    if ( navigator.appName == "Netscape" ){
	        teclaPressionada = pEvent.which;
	    } else {
	        teclaPressionada = pEvent.charCode;
	    }

		if(document.frm.stMascaraRegistro.value.length>0){
			if(teclaPressionada!= 0 && teclaPressionada!= 8 && teclaPressionada!=45 && (teclaPressionada<47 || teclaPressionada>58)){
				return false;
			}
		}else{
			if(teclaPressionada!= 0 && teclaPressionada!= 8 && (teclaPressionada<48 || teclaPressionada>58)){
				return false;
			}
		}
}

function validaMascara(obj) {
    var stExpReg = "[0-9-/]";
    if ( !validaExpressaoInteira(obj, stExpReg ) ) {
        obj.value = "";
    }    
}

function limparForm(){
    f = document.frm;
    f.stMascaraRegistro.value = "";
    f.boGeracaoRegistro[1].checked = true;
    f.inCodTipoNormaTxt.value = "";
    f.inCodTipoNorma.options[0].selected = true;
    f.stMascaraCBO.value = "";
    f.inCodGrupoPeriodoTxt.value = "";
    f.inCodGrupoPeriodo.options[0].selected = true;
    f.stContagemInicial[0].checked = true;
}

</script>

