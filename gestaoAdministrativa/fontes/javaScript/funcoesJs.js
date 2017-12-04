/**
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
/**
* Funções
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 17164 $
$Name$
$Author: souzadl $
$Date: 2006-10-26 08:54:55 -0300 (Qui, 26 Out 2006) $

Casos de uso: uc-01.01.00
*/

function Hoje(){
   var a;
   var sHoje;
	a = new Date();
	lm_year=a.getYear();lm_year=((lm_year<1000)?((lm_year<70)?2000:1900):0)+lm_year;
	lm_month=a.getMonth()+1;lm_month=((lm_month<10)?'0':'')+lm_month;
	lm_day=a.getDate();lm_day=((lm_day<10)?'0':'')+lm_day;
   sHoje = lm_day+'-'+lm_month+'-'+lm_year;
   return sHoje;
}
function Agora(){
   var a;
   var sAgora;
	a = new Date();
	lm_hour=a.getHours();lm_hour=((lm_hour<10)?'0':'')+lm_hour;
	lm_minute=a.getMinutes();lm_minute=((lm_minute<10)?'0':'')+lm_minute;
	lm_second=a.getSeconds();lm_second=((lm_second<10)?'0':'')+lm_second;
   sAgora = lm_hour+':'+lm_minute+':'+lm_second;
   return sAgora;
}
function HojeAgora(){
   var sHoje = Hoje();
   var sHora = Agora();
   sAux  = "&sAgora=" + sHoje + "_" + sHora;
   return sAux;
}

function MostraCalendario(sForm,sCampo){
   var x = 400;
   var y = 200;
   var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/data0100Legado.php?sForm='+sForm+'&sCampo='+sCampo;
   var wVolta=false;
   wCal = window.open(sArq,'wCal','width=180px,height=150px,resizable=1,scrollbars=0,left='+x+',top='+y);
}


function MontaCSS(){
   var sLinha;
   var sNavegador = navigator.appName;
   if (sNavegador == "Microsoft Internet Explorer"){
      sLinha = "<link rel=STYLESHEET type=text/css href=../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/stylos_ie.css>\n";
   } else {
      sLinha = "<link rel=STYLESHEET type=text/css href=../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/CSS/stylos_ns.css>\n";
   }
   document.write(sLinha);
}
function MontaCSSMenu(){
   var sLinha;
   var sNavegador = navigator.appName;
   if (sNavegador == "Microsoft Internet Explorer"){
      sLinha = "<link rel=STYLESHEET type=text/css href=../../../stylos_ie_menu.css>";
   } else {
      sLinha = "<link rel=STYLESHEET type=text/css href=../../../framework/temas/padrao/CSS/stylos_ns_menu.css>";
   }
   document.write(sLinha);
}
function AbreTela(sTela,iTamH, iTamV){
   var sNow = HojeAgora();
   var sArq = sTela + sNow;
   var sVar = sTela.substr(0,8);
   eval(sVar+"= window.open (sArq, \""+sVar+"\", \"width="+iTamH+",height="+iTamV+",resizable=1,scrollbars=1\");");
}

function mnuEntra(obj){
   var sID = obj.id;
   eval("document.all."+ sID +".style.backgroundColor = '#ccccff'");
   eval("document.all."+ sID +".style.borderBottom = '1 solid #000080'");
   eval("document.all."+ sID +".style.borderRight = '1 solid #000080'");
   eval("document.all."+ sID +".style.borderLeft = '1 solid #ffffff'");
   eval("document.all."+ sID +".style.borderTop = '1 solid #ffffff'");
}
function mnuSai(obj){
   var sID = obj.id;
   eval("document.all."+ sID +".style.backgroundColor = '#526c9f'");
   eval("document.all."+ sID +".style.borderBottom = '1 solid #dcdcdc'");
   eval("document.all."+ sID +".style.borderRight = '1 solid #dcdcdc'");
   eval("document.all."+ sID +".style.borderLeft = '1 solid #dcdcdc'");
   eval("document.all."+ sID +".style.borderTop = '1 solid #dcdcdc'");
}
function mudaTitMenu(nomeID, classe){
   eval("document.links['"+ nomeID +"'].className = '"+ classe +"';");
}

function alerta(msg) {
   alert(msg);
}

function toFloat( strValor ) {
// Descrição: Garante retorno numérico para entradas de strings
// toFloat('-12,345') -> -12.345
// toFloat('') -> 0
// toFloat('12.3') -> 12.3
// toFloat('-12.3') -> -12.3
// toFloat() -> 0
// toFloat('12,3') -> 12.3
// toFloat('-12,3') -> -12.3
// toFloat('abc') -> 0
	if ( (strValor == null) || (strValor.length == 0) ) {
		return 0;
	}
	if (!isNaN(strValor)) {
		return parseFloat(strValor);
	}
	retorno = limpaParaMascara(strValor,'valores');
    procurado = /,/;
    retorno = retorno.replace(procurado, ".");
	if ( (retorno == "") || (isNaN(retorno)) ) {
		return 0;
	}
	return parseFloat(retorno);
}

function incluiZerosAEsquerda(str,tamanho,permiteZero) {
// Descrição: Retorna 'str' preenchido com zeros à esquerda até o 'tamanho' especificado
// incluiZerosAEsquerda("123",6) -> 000123
// incluiZerosAEsquerda(" 123",6) -> 00 123
// incluiZerosAEsquerda("abc",6) ->
// incluiZerosAEsquerda(" 1 3 ",6) -> 013
	retorno = str;
	if ( (str.length > 0) && (str.length < tamanho) && ( (toFloat(str) != 0) || permiteZero ) ) {
		for (i=(tamanho - str.length) ; i>0 ; i--) {
			retorno = '0' + retorno;
		}
		return retorno;
	}
	if (toFloat(str) == 0 && !permiteZero) {
		return "";
	} else {
		return str;
	}
}

function limpaZerosAEsquerda(inputString,tipo) {
// Descrição: Retira 'zeros' à esquerda do 'inputString' (usar tipo = 1 para permitir zero)
// limpaZerosAEsquerda("000123") -> 123
// limpaZerosAEsquerda(" 000123") -> 000123
// limpaZerosAEsquerda("000123",1) -> 123
// limpaZerosAEsquerda("000123",0) -> 123
// limpaZerosAEsquerda("-000123",1) -> 0123
// limpaZerosAEsquerda("-000123",0) -> -000123
// limpaZerosAEsquerda("000abc") -> abc
	outputString  = '';
	espacosAntes  = 0;
	if (tipo == 1) {
		re = /^0*$/;
		res = inputString.match(re);
//		if (inputString.substr(0,1) != "-" && inputString.substr(0,inputString.length) != "0") inic = 0;
		if (inputString.substr(0,1) != "-" && res == null) inic = 0;
		else  inic = 1;
	}
	else inic = 0;
	for(i = inic ; i < inputString.length ; i++){
		if(inputString.charAt(i) == '0'){ espacosAntes++; }
		else {	break;	}
	}
	outputString =  inputString.substr(espacosAntes);
	return outputString;
}

function trimString(inputString,trimLeft,trimRight){
// Descrição: Remove espaços em branco à direita e/ou à esquerda de 'inputString'
// trimString("  123  ",true,true) -> '123'
// trimString("  123  ",true,false) -> '123  '
// trimString("  123  ",false,true) -> '  123'
// trimString("  123  ",false,false) ->'  123  '
	outputString  = '';
	espacosAntes  = 0;
	espacosDepois = 0;
	if(trimLeft){
		for(i = 0 ; i < inputString.length ; i++){
			if(inputString.charAt(i) == ' '){ espacosAntes++; }
			else {	break;	}
		}
	}
	if(trimRight){
		for(i = inputString.length-1 ; i>0 ; i--){
			if(inputString.charAt(i) == ' '){ espacosDepois++; }
			else {	break;	}
		}
	}
	outputString =  inputString.substr(espacosAntes);
	outputString = outputString.substr(0,(outputString.length-espacosDepois));
	return outputString;
}

function formatoMonetario(oque,tipo){
// Descrição: Formata um campo de formulário como um valor monetário no evento onblur.
//            Usar tipo = true para permitir "0,00". Se não informado este parâmetro, não permite.
// onblur = "formatoMonetario(this,true)"
	if (oque.value == "-" || oque.value == "") {
		oque.value = ""
		return;
	}
    retorno = '';
    for (contador=0;contador < oque.value.length;contador++) {
    	if( (oque.value.charAt(contador) != ".")) {retorno += oque.value.charAt(contador);}
    }
    procurado = /,/;
    retorno = retorno.replace(procurado, ".");
    retorno = retornaFormatoMonetario(retorno-0,tipo);
    oque.value = retorno;
}

function formatoMonetarioSemCentavos(oque,tipo){
// Descrição: Formata um campo de formulário como um valor monetário sem os centavos no evento onblur.
//            Usar tipo = true para permitir "0". Se não informado este parâmetro, não permite.
// onblur = "formatoMonetarioSemCentavos(this,true)"
	if (oque.value == "-" || oque.value == "") {
		oque.value = ""
		return;
	}
    retorno = '';
    for (contador=0;contador < oque.value.length;contador++) {
    	if( (oque.value.charAt(contador) != ".")) {retorno += oque.value.charAt(contador);}
    }
    procurado = /,/;
    retorno = retorno.replace(procurado, ".");
    retorno = retornaFormatoMonetario(retorno-0,tipo);
	oque.value = retorno.substr(0,(retorno.length-3));
}

function retornaFormatoMonetario(valor,tipo) {
// Descrição: Retorna o parâmetro 'valor' formatado como um valor monetário.
//            Usar tipo = true para permitir "0,00". Se não informado este parâmetro, não permite.
// requerida pela função formatoMonetario
// retornaFormatoMonetario("12345") -> 12.345,00
// retornaFormatoMonetario("12.345") -> 12,35
// retornaFormatoMonetario("12,345") -> 12,35
// retornaFormatoMonetario("-12345") -> -12.345,00
// retornaFormatoMonetario("-12.345") -> -12,35
// retornaFormatoMonetario('-12,345') -> -12,35
// retornaFormatoMonetario("0",0) ->
// retornaFormatoMonetario("") ->
// retornaFormatoMonetario("0",1) -> 0,00
// retornaFormatoMonetario("0",true) -> 0,00
// retornaFormatoMonetario("0",false) ->
	valorNegativo = false;
	retorno = '';
	valor = toFloat(valor);
	if (valor < 0) {
		valorNegativo = true;
		valor = valor*(-1);
	}
    if(valor != 0 || (tipo == 1 && valor == 0) ) {
		retorno = parteInteira(Math.floor(valor) + '') + parteFracao(valor);
		if (valorNegativo) {
			retorno = '-'+retorno;
		}
	}
	return retorno;
}

function retornaFormatoMonetarioInteiro(valor) {
// Descrição: Retorna o parâmetro 'valor' formatado como um valor monetário inteiro.
// retornaFormatoMonetarioInteiro("12345") -> 12.345
// retornaFormatoMonetarioInteiro("12.345") -> 12
// retornaFormatoMonetarioInteiro("12,345") -> NaN
// retornaFormatoMonetarioInteiro("") ->
    if((valor-0) != 0) {
		return parteInteira(Math.floor(valor-0) + '');
	}
	else return '';
}

function parteInteira(valor) {
// Descrição: Requerida pela função formatoMonetario. Retorna a parte inteira formatada.
    if (valor.length <= 3)
        return (valor == '' ? '0' : valor);
    else {
        vezes = valor.length % 3;
        retorno = (vezes == 0 ? '' : (valor.substring(0,vezes)));
        for (i=0 ; i < Math.floor(valor.length/3) ; i++) {
            if ( (vezes ==0) && (i ==0) )
                retorno += valor.substring(vezes + 3 * i,vezes + 3 * i + 3);
            else
                retorno += '.' + valor.substring(vezes + 3 * i,vezes + 3 * i + 3);
        }
		retorno = retorno.replace(/-\./,"-");
        return (retorno);
    }
}

function parteFracao(resto) {
// Descrição: Requerida pela função formatoMonetario. Retorna a parte fracionária.
// Autor: Eduardo Pinheiro
// Data: 04/01/2001
    resto = Math.round( ( (resto) - Math.floor(resto) ) *100);
    return (resto < 10 ? ',0' + resto : ',' + resto); }


function validaLengthData(oque,tipo,permiteZero){
// Descrição: Testa o tamanho de um campo de formulário, preenche-o com zeros e valida o conteúdo, no evento onblur.
// tipos: 'cc','cep','cpf','cgc'
	switch (tipo) {
	/********************************************************************
	Completa o campo com zeros do cartao
	********************************************************************/
		case 'visa':
		{
			if(oque.value == '')
				return true;
			var StringVisa = limpaParaMascara(oque.value,'numeros');
			if(StringVisa.length < 16)
			{
				oque.value = limpaParaMascara(oque.value,'numeros');
				oque.value = incluiZerosAEsquerda(oque.value,16);
				if(digitoVisa(oque) != 1)
				{
					mascara(oque,'cartao');
					alerta("Número do cartão inválido")
					oque.value = "";
					oque.focus();
					return false;
				}
				mascara(oque,'cartao',16);
			}
			return true;
			break;
		}
	/********************************************************************
	Completa o campo da Conta Corrente com zeros a direita e testa a CC
	********************************************************************/
		case 'cc': {
			if(oque.value == '' || oque.value.length >= 13){
				return true;
			}
			retorno = '';
			retorno = limpaParaMascara(oque.value,'numeros');
		    if (retorno.length < 11) {
				zeros = '00000000000';
				retorno = retorno + zeros.substr(0,(11-retorno.length));
				if (retorno.length >= 4) { retorno = retorno.substr(0,4) + "-" + retorno.substr(4); }
				if (retorno.length >= 10) { retorno = retorno.substr(0,10) + "-" + retorno.substr(10); }
				oque.value = retorno;
			}
			if (!isContaCorrente(limpaParaMascara(oque.value,'numeros'))) {
				alerta(oque.value+"\n"+"Conta Corrente inválida.");
				oque.value = "";
				oque.focus();
				return false;
			}
			return true;
			break;
		}
		case 'cep': {
			if(oque.value == ''){
				return true;
			}
			retorno = '';
			retorno = limpaParaMascara(oque.value,'numeros');
		  if (retorno.length < 8) {
				zeros = '00000000';
				retorno = retorno+zeros.substr(0,(8-retorno.length));
				if (retorno.length >= 5) { retorno = retorno.substr(0,5)+"-"+retorno.substr(5,7); }
				oque.value = retorno ;
			};
			if ( (limpaParaMascara(oque.value,'numeros') - 0) == 0) {
				alerta(oque.value+"\n"+"CEP inválido.");
				oque.value="";
				oque.focus();
				return false;
			}
			break;
		}
		case 'cpf': {
			if(oque.value == ''){
				return true;
			}
			retorno = '';
			retorno = limpaParaMascara(oque.value,'numeros');
		    if (retorno.length < 11) {
				cpf_zeros = '00000000000';
				retorno = cpf_zeros.substr(0,(11-retorno.length))+retorno;
				if (retorno.length >= 3) { retorno = retorno.substr(0,3)+"."+retorno.substr(3); }
				if (retorno.length >= 7) { retorno = retorno.substr(0,7)+"."+retorno.substr(7); }
				if (retorno.length >= 11) { retorno = retorno.substr(0,11)+"-"+retorno.substr(11); }
				oque.value = retorno ;
				if (retorno == '000.000.000-00' && permiteZero) return true;
				if (!validaCPF(retorno)) {
					alerta(oque.value+"\n"+"CPF inválido.");
					oque.value="";
					oque.focus();
					return false;
				}
			};
			break;
		}
		case 'cgc': {
			if(oque.value == ''){
				return true;
			}
			retorno = '';
			retorno = limpaParaMascara(oque.value,'numeros');
		    if (retorno.length < 14) {
				cgc_zeros = '00000000000000';
				retorno = cgc_zeros.substr(0,(14-retorno.length))+retorno;
				if (retorno.length >= 2)  { retorno = retorno.substr(0,2)+"."+retorno.substr(2); }
				if (retorno.length >= 6)  { retorno = retorno.substr(0,6)+"."+retorno.substr(6); }
				if (retorno.length >= 10) { retorno = retorno.substr(0,10)+"/"+retorno.substr(10); }
				if (retorno.length >= 15) { retorno = retorno.substr(0,15)+"-"+retorno.substr(15); }
				oque.value = retorno ;
	            if (!validaCGC(retorno)) {
					alerta(oque.value+"\n"+"CNPJ inválido.");
					oque.value="";
					oque.focus();
					return false;
				}
			};
			break;
		}
		case 'bdu': {
			if(oque.value == ''){
				return true;
			}
			retorno = '';
			retorno = limpaParaMascara(oque.value,'numeros');
			while ( retorno.length < 7 ) {
				retorno = '0'+retorno;
			}
			retorno = retorno.substr(0,5)+"-"+retorno.substr(5,2);
			oque.value = retorno;
			if (!ValidaBDU(retorno)) {
				alerta(oque.value+"\n"+"BDU inválido.");
				oque.value="";
				oque.focus();
				return false;
			}
			break;
		}
	}
	return true;
}


function formataAoSair(campo,separador,digitosFracao,prePos,simbolo) {
// Descrição: formata um campo de formulário ao sair do mesmo respeitando o formato especificado
// onblur="formataAoSair(this,',',4,1,'%')" resulta em 12.345,6789%
// parametros:
// 		campo			: this, para receber o objeto
// 		separador		: '.' ou ',' ou o separador da parte inteira/fracao
// 		digitosFracao	: quantos dígitos devem existir após o separador
// 		prePos			: 0 para colocar o simbolo antes do número ou 1 para depois do número
// 		simbolo			: o símbolo que deve ser colocado dentro do campo
	if ( campo.value.length == 0 ) { 
         retorno = '0' + separador  ;
         for( x=0;x<digitosFracao;x++){
              retorno = retorno + '0';   
         }         
	     if (prePos == 0) { retorno = simbolo + retorno ; }
         if (prePos == 1) { retorno = retorno + simbolo; }
         campo.value = retorno;   
        return; }
	posicaoSeparador = campo.value.indexOf(separador);
	if (posicaoSeparador == -1) { posicaoSeparador = campo.value.length; }
	retorno = separador+limpaParaMascara(campo.value.substring(posicaoSeparador),'numeros') // inicializa a parte fracionária do string de retorno
	while ( retorno.length < digitosFracao+separador.length ) { retorno = retorno + '0'; }
	retorno = campo.value.substr(0,posicaoSeparador) + retorno ; // completa a parte numérica do retorno
	if (prePos == 0) { retorno = simbolo + retorno ; }
	if (prePos == 1) { retorno = retorno + simbolo; }
	campo.value = retorno;
}

function mascara(objEmFoco,tipo,tamanho1,tamanho2,sinal){
// Descrição: Máscaras para edição de campos de formulário.
// usar sinal = 1 para valores positivos/negativos. tamanho1 e tamanho2 são opcionais e determinam o tamanho máximo de um campo numérico e suas casas decimais.
// tipos: cep,cpf,cgc,cnpj10,ddd,ramal,fone,celular,DD/MM/AA,DD/MM/AAAA,MM/AAAA,IE,caracter,numero,valor,percentual,cartao,cc,poupanca,unidade, HH:MM
// Exemplo: onKeyUp="mascara(this,'cep');"
// Exemplo: onKeyUp="mascara(this,'valor',13,2);"
// Exemplo: onKeyUp="mascara(this,'valor',13,2,1);"
    if (
		(event.keyCode == 8) ||
		(event.keyCode == 13) ||
		(event.keyCode == 37) ||
		(event.keyCode == 39) ||
		(event.keyCode == 46) ||
		(event.keyCode == 16) ||
		(event.keyCode == 17)
		)
        return ;
	tamanho1 = toFloat(tamanho1);
	tamanho2 = toFloat(tamanho2);
	retorno = '';
	switch (tipo) {
		case 'cep': {  // 99999-999
			objEmFoco.maxLength=9;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			retorno = retorno.substr(0,9);
			if (retorno.length >= 8 && (retorno-0 == 0) )  {
				alerta(retorno.substr(0,5)+"-"+retorno.substr(5,7)+"\n"+"CEP inválido.");
				objEmFoco.value="";
				objEmFoco.focus();
				return;
			}
			if (retorno.length >= 5) { retorno = retorno.substr(0,5)+"-"+retorno.substr(5,7); }
			objEmFoco.value = retorno.substr(0,9);
		break;	}
		case 'cpf': {  // 999.999.999-99
			objEmFoco.maxLength=14;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 3) { retorno = retorno.substr(0,3)+"."+retorno.substr(3); }
			if (retorno.length >= 7) { retorno = retorno.substr(0,7)+"."+retorno.substr(7); }
			if (retorno.length >= 11) { retorno = retorno.substr(0,11)+"-"+retorno.substr(11); }
			retorno = retorno.substr(0,14);
			objEmFoco.value = retorno;
			if (retorno == '000.000.000-00' && tamanho1 == 1) return true;
			if (retorno.length >= 14) {
				if (!validaCPF(retorno) || retorno == '00000000000000') {
					alerta(objEmFoco.value+"\n"+"CPF inválido.");
					objEmFoco.value="";
					objEmFoco.focus();
					return;
				}
			}
		break;	}
		case 'cgc': {  // 99.999.999/9999-99
			objEmFoco.maxLength=18;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 2)  { retorno = retorno.substr(0,2)+"."+retorno.substr(2); }
			if (retorno.length >= 6)  { retorno = retorno.substr(0,6)+"."+retorno.substr(6); }
			if (retorno.length >= 10) { retorno = retorno.substr(0,10)+"/"+retorno.substr(10); }
			if (retorno.length >= 15) { retorno = retorno.substr(0,15)+"-"+retorno.substr(15); }
			objEmFoco.value = retorno.substr(0,18);
			if (retorno.length >= 18) {
                if (!validaCGC(retorno)) {
					alerta(objEmFoco.value+"\n"+"CNPJ inválido.");
					objEmFoco.value="";
					objEmFoco.focus();
					return;
				}
            }
		break;	}
		case 'ramal': {  // 9999
			objEmFoco.maxLength=4;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			objEmFoco.value = retorno.substr(0,4);
		break;	}
		case 'DD/MM/AA': {  // 99/99/99
			objEmFoco.maxLength=8;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 2) { retorno = retorno.substr(0,2)+"/"+retorno.substr(2); }
			if (retorno.length >= 5) { retorno = retorno.substr(0,5)+"/"+retorno.substr(5); }
			objEmFoco.value = retorno.substr(0,8);
			if (retorno.length >= 8) {
				dataEmTeste = retorno.substr(0,6)+'20'+retorno.substr(6,2) ;
                if (!retornaValidaData(dataEmTeste)) {
					objEmFoco.value="";
					objEmFoco.focus();
					return;
				}
            }
		break;	}
		case 'DD/MM/AAAA': {  // 99/99/9999
			objEmFoco.maxLength=10;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 2) { retorno = retorno.substr(0,2)+"/"+retorno.substr(2); }
			if (retorno.length >= 5) { retorno = retorno.substr(0,5)+"/"+retorno.substr(5); }
			objEmFoco.value = retorno.substr(0,10);
			if (retorno.length >= 10) {
				if (!retornaValidaData(objEmFoco.value,tamanho1)) {
					objEmFoco.value="";
					objEmFoco.focus();
					return;
				}
			}
		break;	}
		case 'MM/AAAA': {  // 99/9999
			objEmFoco.maxLength=7;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 2) { retorno = retorno.substr(0,2)+"/"+retorno.substr(2); }
			objEmFoco.value = retorno.substr(0,7);
			if (retorno.length >= 7) {
				dataEmTeste = '01/'+retorno
                if (!retornaValidaData(dataEmTeste)) {
					objEmFoco.value="";
					objEmFoco.focus();
					return;
				}
            }
		break;	}
		case 'numero': {
			if(tamanho1 != 0){
				objEmFoco.maxLength = tamanho1;
			}
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			objEmFoco.value = retorno.substr(0,objEmFoco.maxLength);
		break;	}
		case 'inteiro': {
			if(tamanho1 != 0){
				objEmFoco.maxLength = tamanho1;
			}
			retorno = limpaZerosAEsquerda(limpaParaMascara(objEmFoco.value,'numeros'));
			objEmFoco.value = retorno.substr(0,objEmFoco.maxLength);
		break;	}
		case 'valor': {
			retorno = objEmFoco.value;
			if (tamanho1+tamanho2 >0) {
				objEmFoco.maxLength = tamanho1 + 1 + tamanho2 + Math.floor(tamanho1/3);
			}
			var isNeg = false;
			if (retorno.charAt(0) == '-') {
				isNeg = true;
				retorno = retorno.substring(1);
				objEmFoco.maxLength++;
			}
			retorno = limpaParaMascara(retorno,'valores');
			var posPrimVirgula = retorno.indexOf(",");
			retorno = limpaParaMascara(retorno,'numeros');
			if (posPrimVirgula > 0) {
				valorInteiro = retorno.substr(0,posPrimVirgula);
				valorCentavo = retorno.substring(posPrimVirgula);
				if (retorno.charAt(0) == '0') {
					retorno = "0,"+valorCentavo.substr(0,tamanho2);
				} else {
					valorInteiro = retornaFormatoMonetarioInteiro(valorInteiro);
					valorCentavo = valorCentavo.substr(0,tamanho2);
					retorno = valorInteiro+","+valorCentavo;
				}
			} else { 
				retorno = retorno.substr(0,tamanho1);
				retorno = retornaFormatoMonetarioInteiro(retorno);
			}
			if (retorno == "" && (event.keyCode == 48 || event.keyCode == 96)) { retorno = '0'; }
			if (isNeg) { retorno = "-"+retorno; }
			objEmFoco.value = retorno;
		break;	}
		case 'percentual': {  // 999
			objEmFoco.maxLength=3;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			objEmFoco.value = retorno.substr(0,3);
/*			objEmFoco.maxLength=6;
			retorno = limpaParaMascara(objEmFoco.value,'valores');
			if (retorno.length >= 3)  {
				retorno = retorno.substr(0,3)+","+retorno.substr(3); }
			posicaoPrimeiraVirgula = retorno.indexOf(",");
			retorno = limpaParaMascara(retorno,'numeros');
			if (posicaoPrimeiraVirgula > -1) { 
				retorno = retorno.substr(0,posicaoPrimeiraVirgula)+","+retorno.substr(posicaoPrimeiraVirgula,2); 
			};
			objEmFoco.value = retorno ;
*/
		break;	}
		case 'cartao': {  // 9999 9999 9999 9999
			objEmFoco.maxLength=19;
			retorno = limpaParaMascara(objEmFoco.value,'numeros');
			if (retorno.length >= 4) { retorno = retorno.substr(0,4)+" "+retorno.substr(4); }
			if (retorno.length >= 9) { retorno = retorno.substr(0,9)+" "+retorno.substr(9); }
			if (retorno.length >= 14) { retorno = retorno.substr(0,14)+" "+retorno.substr(14); }
			objEmFoco.value = retorno.substr(0,19);
			if (objEmFoco.value.length == 19) {
				if (!validaCartao(objEmFoco.value)) {
					alerta(objEmFoco.value+"\nNúmero do Cartão inválido")
					objEmFoco.value = "";
					objEmFoco.focus();
					return;
				}
			}
		break;	}
		case 'cc': {  //  9999-99999-99
			objEmFoco.maxLength=13;
			retorno = limpaParaMascara(objEmFoco.value,"numeros");
			if (retorno.length >= 4) { retorno = retorno.substr(0,4) + "-" + retorno.substr(4); }
			if (retorno.length >= 10) { retorno = retorno.substr(0,10) + "-" + retorno.substr(10); }
			objEmFoco.value = retorno.substr(0,13);
			if (objEmFoco.value.length == 13) {
				if (!isContaCorrente(objEmFoco.value)) {
					alerta(objEmFoco.value+"\nConta corrente inválida")
					objEmFoco.value = "";
					objEmFoco.focus();
					return;
				}
			}
		break;	}		
		case 'HH:MM': { // 12:00
			objEmFoco.maxLength=5;
			retorno = limpaParaMascara(objEmFoco.value,"numeros");
			if ( retorno.substr(0,1) > 2 ) { retorno = ''; }
			if ( retorno.substr(0,2) > 23 ) { retorno = retorno.substr(0,1); }
			if ( retorno.substr(2,1) > 5 ) { retorno = retorno.substr(0,2); }
			if (retorno.length >= 2) { retorno = retorno.substr(0,2) + ":" + retorno.substr(2); }
			objEmFoco.value = retorno.substr(0,5);
		break; }
	}
}

function limpaParaMascara(sujeira,filtro,tipo){
// Descrição: Recebe um string e retorna somente os caracteres que pertencem ao filtro. Usar tipo = 1 para valores positivo/negativo.
// limpaParaMascara('12.3ABC -def456','valores') -> 123456
// limpaParaMascara('12,3ABC -def456','valores') -> 12,3456
// limpaParaMascara('-12,3ABC -def456','valores') -> -12,3456
// limpaParaMascara('12,3ABC -def456','letras') -> 12,3ABC -def456
// limpaParaMascara('12,3ABC -def456','numeros') -> 123456
// limpaParaMascara('0','numeros') -> 0
//  ******
//  Filtros:
	numeros = "0123456789";
	valores = "0123456789,";
	letras  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyzÁÉÍÓÚÀÈÌÒÙÄËÏÖÜÂÊÎÔÛÃÕáéíóúàèìòùäëïöüâêîôûãõçÇ&ªº'\"\|@_<>!#$%&*()={[}]?:+-.,;/\\0123456789 ";
//  ******
	retorno2 = '';
	if (tipo == 1) {
		if (sujeira.substring(0,1) == "-") ind = 1;
		else ind = 0;
	}
	else ind = 0;
	switch (filtro){
		case 'numeros': {
			for ( i=ind; i < sujeira.length; i++ ) {
				if( numeros.indexOf(sujeira.charAt(i))>-1 ) { 
					retorno2 += sujeira.charAt(i);
				}
			}
		break;	}
		case 'valores': {
			for ( i=ind; i < sujeira.length; i++ ) {
				if( valores.indexOf(sujeira.charAt(i))>-1 ) { 
					retorno2 += sujeira.charAt(i);
				}
			}
			if (sujeira.charAt(0)=='-') {
				retorno2 = "-"+retorno2;
			}
		break;	}
		case 'letras': {
			for ( i=0; i < sujeira.length; i++ ) {
				if( letras.indexOf(sujeira.charAt(i))>-1 ) { 
					retorno2 += sujeira.charAt(i);
				}
			}
		break;	}
	}
	if (tipo == 1) {
		if (sujeira.substring(0,1) == "-") retorno2 = "-" + retorno2;
	}
	return retorno2;
}

function validaCPF (CPF) {
// Descrição : Função de validação de CPF.
    CPF = limpaParaMascara(CPF,'numeros');    
    if (CPF.length != 11) { for(countZeros=0 ; countZeros < ((11-CPF.length)+2) ; countZeros++){ CPF = "0"+CPF; } };
	if(CPF == '00000000000'){ return false; }
    soma = 0;
    for(i=0 ; i<9 ; i++) {
        soma = soma + eval(CPF.charAt(i) * (10 - i));
    }
    Resto = 11 - ( soma - (parseInt(soma / 11) * 11) );
    if ( (Resto == 10) || (Resto == 11) ) { Resto = 0; }
    if ( Resto != eval( (CPF.charAt(9) ) ) ) { return false; }
	soma = 0;
    for (i = 0;i<10;i++) {
        soma = soma + eval(CPF.charAt(i) * (11 - i));
	} 
    Resto = 11 - ( soma - (parseInt(soma / 11) * 11) );
    if ( (Resto == 10) || (Resto == 11)) {
		Resto = 0;
	}
    if ( Resto != eval( (CPF.charAt(10)) )) {
		return false;
	}
	return true;
}

function validaCGC(field) {
// Descrição: Função de validação de CGC.
   field = limpaParaMascara(field,'numeros');    
   if ( (field == "") || (field == " ") || (field == '00000000000000')) return false;
   if (field.length != 14) {
        return false;
    }
	first_digit  = field.charAt(12);
	second_digit = field.charAt(13);
	field = field.substring(0,12);
	first_verified  = calcMod11(field,5,2);  // Através do modulo 11 descobre qual é o primeiro digito do final
	second_verified = calcMod11(field + first_verified,6,2);  // Através do modulo 11 descobre qual é o segundo digito do final
    /*
        Se os dois digitos gerados pelo modulo11 forem iguais aos dois últimos
        digitos digitados pelo usuário, validação de CGC OK.
    */	
	if( (first_verified == first_digit) && (second_verified==second_digit) ) { return true; } 
	else {
        return false;
	}
}


function limpaCampo(field) {
// Descrição: Função para extrair caracteres indesejados de um campo. ( . - / , )
// limpaCampo("1.23/45,abc") -> 12345abc
t_field='';
	for (i=0;i<field.length;i++) {
		if( (field.charAt(i) != ".") && (field.charAt(i) != "-") && (field.charAt(i) != "/") && (field.charAt(i) != ",")) {
			t_field = t_field + field.charAt(i);
		}
	}
return t_field;
}

function formataCPF(paramCpf) {
// Descrição: Função de máscara para CPF
// formataCPF("99999999999") -> 999.999.999-99
// formataCPF("abc99999999") -> abc99999999
// formataCPF("9999999999") -> 9999999999
// formataCPF("999999999990") -> 999999999990
	cpfSemMascara = limpaParaMascara(paramCpf,'numeros');
	if (cpfSemMascara.length == 11) {
		cpfRetorno = '';
		cpfRetorno += cpfSemMascara.substr(0,3);
		cpfRetorno += ".";
		cpfRetorno += cpfSemMascara.substr(3,3);
		cpfRetorno += ".";
		cpfRetorno += cpfSemMascara.substr(6,3);
		cpfRetorno += "-";
		cpfRetorno += cpfSemMascara.substr(9,2);
		return cpfRetorno;
	} else {
		return paramCpf;
	}
}

function formataCGC(paramCgc) {
// Descrição: Função de máscara para CGC.
// formataCGC("99999999999999") -> 99.999.999/9999-99
// formataCGC("abc99999999999") -> abc99999999999
// formataCGC("9999999999999") -> 9999999999999
// formataCGC("999999999999990") -> 999999999999990
    cgcSemMascara = limpaParaMascara(paramCgc,'numeros');
    if (cgcSemMascara.length == 14) {
		cgcRetorno = '';
    	cgcRetorno = cgcSemMascara.substr(0,2);
    	cgcRetorno += '.';
    	cgcRetorno += cgcSemMascara.substr(2,3);
    	cgcRetorno += '.';
    	cgcRetorno += cgcSemMascara.substr(5,3);
    	cgcRetorno += '/';
    	cgcRetorno += cgcSemMascara.substr(8,4);
    	cgcRetorno += '-';
    	cgcRetorno += cgcSemMascara.substr(12,2);
    	return cgcRetorno;
    }
    else {
        return paramCgc;
    }
}

function alertaDataInvalida(data,tipoTratamento) {
// !!! Veja a função dataValida() com vários tipos de teste
// Descrição : Verifica se a data informada é válida. Se não, emite alerta.
	falhou = false;
	t_data = data.value;
	t_data = limpaCampo(t_data);
	dia = t_data.substr(0,2);
	mes = t_data.substr(2,2) - 1;
	ano = t_data.substr(4,4);
	dataCorr = new Date();
	dataObj = new Date(ano,mes,dia);
	diaObj = dataObj.getDate();
	mesObj = dataObj.getMonth();
	anoObj = dataObj.getFullYear();
	if ( ( t_data.length < 8 ) || (dia != diaObj) || (mes != mesObj) || (ano != anoObj) )
		falhou = true;
    // Data não maior ou igual a data do dia
	if (tipoTratamento == 0 ) {
		if (dataObj >= dataCorr) {
			falhou = true;
		}
	}
	// Data não maior a data do dia
	if (tipoTratamento == 1) {
		if (dataObj > dataCorr) {
			falhou = true;
		}
	}
	if (tipoTratamento == 2 && data.value == '00/00/0000') falhou = false;
	if ( falhou ) {
		alerta("Data inválida");
		data.value = '';
		if (!data.disabled) {
			data.focus();
		}
	}
}
	
function dataValida(dataValor,tipoTeste){
// Descrição: Retorna false caso o string 'dataValor' não passe no 'tipoTeste', ou true no caso de passar no teste.
// Tipos de teste: anterior,ult120anos,futura,futuraOUigual,anteriorOUigual,2mesesMMAAAA
// Exemplo: if(dataValida(document.form.txtData.value,'anterior')) {alerta("a data é anterior à atual e é válida")}
	dataValor = limpaCampo(dataValor);

	dia = dataValor.substr(0,2);
	mes = dataValor.substr(2,2) - 1;
	ano = dataValor.substr(4,4);

	dataObj = new Date(ano,mes,dia);
	diaObj = dataObj.getDate();
	mesObj = dataObj.getMonth();
	anoObj = dataObj.getFullYear();
	dataObj.setHours(0);
	dataObj.setMinutes(0);
	dataObj.setSeconds(0);
	dataObj.setMilliseconds(0);

	dataCorr = new Date();
	diaCorr = dataCorr.getDate();
	mesCorr = dataCorr.getMonth();
	anoCorr = dataCorr.getFullYear();
	dataCorr.setHours(0);
	dataCorr.setMinutes(0);
	dataCorr.setSeconds(0);
	dataCorr.setMilliseconds(0);

	data120 = new Date(anoCorr-120,mesCorr,diaCorr);
	data120.setHours(0);
	data120.setMinutes(0);
	data120.setSeconds(0);
	data120.setMilliseconds(0);

	if ( ( dataValor.length < 8 ) || (dia != diaObj) || (mes != mesObj) || (ano != anoObj) )
		return false;
/*
	if(tipoTeste != null && tipoTeste != 'anterior' && tipoTeste != 'ult120anos' && tipoTeste != 'futura' && tipoTeste != 'anteriorOUigual' && tipoTeste != '2mesesMMAAAA' && tipoTeste != 'futuraOUigual') {
		alerta("parâmetro de teste de data inválido");
		return false;
	}
*/
	switch (tipoTeste){
		case 'anterior':{
			if (dataObj >= dataCorr) {
				return false; }
		break; }
		case 'ult120anos':{
			if (dataObj < data120) {
				return false; }
			if (dataObj >= dataCorr) {
				return false; }
		break; }
		case 'futura':{
			if (dataObj <= dataCorr) {
				return false; }
		break; }
		case 'futuraOUigual':{
			if (dataObj < dataCorr) {
				return false; }
		break; }
		case 'anteriorOUigual':{
			if (dataObj > dataCorr) {
				return false; }
		break; }
		case '2mesesMMAAAA':{
			dia = '01';
			dataObj = new Date(ano,mes,dia);
			dataObj.setHours(0);
			dataObj.setMinutes(0);
			dataObj.setSeconds(0);
			dataObj.setMilliseconds(0);
			if( mesCorr >= 2) {mesCorr -= 2;} // mes a partir de março
			else {
				anoCorr -= 1;
				if(mesCorr == 0){mesCorr = 10};
				if(mesCorr == 1){mesCorr = 11};
			}
			data2meses = new Date(anoCorr,mesCorr,dia);
			data2meses.setHours(0);
			data2meses.setMinutes(0);
			data2meses.setSeconds(0);
			data2meses.setMilliseconds(0);
			if (dataObj < data2meses) {
				return false; }
		break; }
	}
	return true;
}

function retornaValidaData(t_data,tipoTratamento) {
// Descrição: Recebe o value da data e retorna true se é data válida ou false caso contrário.
	falhou = false;
    t_data = limpaCampo(t_data);
	dia = t_data.substr(0,2);
	mes = t_data.substr(2,2) - 1;
	ano = t_data.substr(4,4);
	dataCorr = new Date();
    dataObj = new Date(ano,mes,dia);
	diaObj = dataObj.getDate();
	mesObj = dataObj.getMonth();
	anoObj = dataObj.getFullYear();
	if ( ( t_data.length < 8 ) || (dia != diaObj) || (mes != mesObj) || (ano != anoObj) )
		falhou = true;
	if (tipoTratamento && tipoTratamento == 2 && t_data == '00000000') falhou = false;
	if ( falhou ) {
		return false;
  }
	else return true;
}

function imprimir(){  
// Descrição: Função de impressão
	var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
	document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
	window.onerror=printerrortrap;  // Configura o tratamento de erros na impressão do documento
	WebBrowser1.ExecWB(6, 2);
	window.onerror = null ;  //  Libera o tratamento de erros
	WebBrowser1.outerHTML = "";  
}

function printerrortrap(){
// Descrição: Função de tratamento de erro de impressão
	alerta("Impressão do documento não ocorreu.");
	window.onerror = null ;
	WebBrowser1.outerHTML="";
	return true;
}


function manipulaDatas(strData,nDias,futuroOuPassado) {
// Descrição: Retorna um string no formato de uma data nDias no futuro ou passado a partir de uma data em formato string informada
    dateArray = strData.split('/');
    sdate = new Date(dateArray[2],dateArray[1]-1,dateArray[0]);
	if(futuroOuPassado == '+'){ sdate.setDate(sdate.getDate() + nDias); }
	if(futuroOuPassado == '-'){ sdate.setDate(sdate.getDate() - nDias); }
	var dia = sdate.getDate();
	var mes = sdate.getMonth()+1
	if (dia < 10) { dia = '0'+dia; }
	if (mes < 10) { mes = '0'+mes; }
    return dia + '/' + mes + '/' + sdate.getFullYear();
}

function periodoDatas(dataFimPeriodo,dataInicioPeriodo) {
// Descrição: Subtrai datas
	dateFim = new Date(dataFimPeriodo.substring(6,10),dataFimPeriodo.substring(3,5)-1,dataFimPeriodo.substring(0,2));
	dateInicio = new Date(dataInicioPeriodo.substring(6,10),dataInicioPeriodo.substring(3,5)-1,dataInicioPeriodo.substring(0,2));
	return ((dateFim - dateInicio)/86400000);
}

function dateToddmmaaaa(objDate) {
// Descrição: recebe um objeto Date e retorna ele formatado como DD/MM/AAAA
	var dia = objDate.getDate();
	var mes = objDate.getMonth()+1;
	if (dia < 10) { dia = '0'+dia; }
	if (mes < 10) { mes = '0'+mes; }
	return dia+"/"+mes+"/"+objDate.getFullYear();
}

function toData(stringData){
// Descrição: Gera um objeto data a partir de um string no formato dd/mm/aaaa
	if (stringData.substr(0,1) == "0") dia = stringData.substr(1,1);
	else dia = stringData.substr(0,2);
	if (stringData.substr(3,1) == "0") mes = (stringData.substr(4,1)-1);
	else  mes = (stringData.substr(3,2)-1);
	ano = stringData.substr(6,4);
	tmp_Data = new Date(ano,mes,dia,0,0,0,0);
	// ATENCAO: PROBLEMA DA PENDENCIA 3820 E 6411
	// Foi constatado em algumas máquinas fora do padrão do HSBC que ao criar um objeto Date(), este era criado no Timezone GMT 0200, ao invés do
	// GMT 0300, por enquanto, inexplicavelmente, então o teste abaixo corrige este problema. Isto pode ocorrer em outros lugares da Aplicação.
	corrigeBugTimeZoneGMT3(tmp_Data)
	// fim do PROBLEMA DA PENDENCIA 3820 E 6411
	return (tmp_Data);
}

function limitaTexto(objText,limiteChars){
// Descrição: quando associada ao evento onkeyup, limita o tamanho do objText ao tamanho limiteChars. ( trunca )
	if(objText.value.length > limiteChars){
		objText.value = objText.value.substr(0,limiteChars);
		return(false);
	}
	return(true);
}

function getRandom() {
// Descrição:
   return Math.random()
}

function desabilitaCampo(str) {
// Descrição:
// Autor: fernando
// Data:
	arrDesabilita = new Array();
	arrDesabilita = str.split(",")
	for (i=0; i<arrDesabilita.length; i++) {
		//obj = eval("document.all."+arrDesabilita[i])
        obj = document.getElementById(arrDesabilita[i]);
		if (obj != null) {
		obj.disabled = true;
//				obj.style.backgroundColor = "#E0E0E0";
		}
	}
}

function habilitaCampo(str) {
// Descrição:
	arrHabilita = new Array();
	arrHabilita = str.split(",")
	for (i=0; i<arrHabilita.length; i++) {
		//obj = eval("document.all."+arrHabilita[i])
        obj = document.getElementById(arrHabilita[i]);
		if (obj != null) {
			obj.disabled = false;
//				obj.style.backgroundColor = "#FFFFFF";
		}
	}
}

function placeFocus() {
if (document.forms.length > 0) {
var field = document.forms[0];
for (i = 0; i < field.length; i++) {
if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea") || (field.elements[i].type.toString().charAt(0) == "s")) {
document.forms[0].elements[i].focus();
break;
         }
      }
   }
}

function obrigatorio(campo){
    if (campo.value == "") {
        alert("Este campo é um campo Obrigatório\nPor favor, complete seu preenchimento.");
        return;
    }
}

function MostraImageUpload(sessao){
   var x = 400;
   var y = 200;
   var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/imagens/uploadImageLegado.php?'+sessao;
   var wVolta=false;
   Up = window.open(sArq,'Up','width=400px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

function fechaLogout(sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = 'logoutfecha.php?'+sessao;
    var wVolta=false;
    var sAux = "window.open(sArq,'','width=50px,height=50px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
    }
function abreAjuda(){
    var x = 10;
    var y = 10;
    var sArq = 'ajuda/index.php';
    var wVolta=false;
    help = window.open(sArq,'help','width=500px,height=300px,resizable=1,scrollbars=0,left='+x+',top='+y);
        }

//Função utilizada para abrir popup de consulta das informações do servidor
//Gestão Recursos Humanos                                                     
//Função criada em: 24/10/2006                                                
//Programador: Diego Lemos de Souza                                           
function abrePopUpConsultaServidor(sessao){
    var width  = 800;
    var height = 550;
    var inCodContrato = 0;
    var inContrato = 0;
    var contratoPensionista = null;
    if( document.getElementById("inCodContrato") ){
        if( document.getElementById("inCodContrato").value != "" ){
            inCodContrato = document.getElementById("inCodContrato").value;
        }
    }
    if( document.getElementById("inContrato") ){
        if( document.getElementById("inContrato").value != "" ){
            inContrato = document.getElementById("inContrato").value;
        }    
    }
    if( document.getElementById("inContratoPensionista") ){
        if( document.getElementById("inContratoPensionista").value != "" ){
            contratoPensionista = document.getElementById("inContratoPensionista").value;
        }    
    }

    if( inCodContrato != 0 || inContrato != 0 ){
        var sFiltros     = "&inCodContrato="+inCodContrato+"&inRegistro="+inContrato;
        //if (contratoPensionista == 'Pensionista Encerrado'){
        if( document.getElementById("inContratoPensionista").value != "" ){
            var sUrlFrames   = "../../../../../../gestaoRH/fontes/PHP/pessoal/popups/servidor/FMConsultarPensionista.php?"+sessao+sFiltros;
        }else{
            var sUrlFrames   = "../../../../../../gestaoRH/fontes/PHP/pessoal/popups/servidor/FMConsultarServidor.php?"+sessao+sFiltros;
        }
        if( Valida() ){
            janela = window.open( sUrlFrames, "popUpConsultarServidor", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
            janela.document.body.scrollTop=0
            janela.document.getElementById('fundo_carregando').style.visibility='visible';
        }
    }        
}
//Função utilizada para abrir popup de consulta das informações do servidor        
