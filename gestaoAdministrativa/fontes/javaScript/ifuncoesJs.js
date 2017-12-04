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

$Revision: 18993 $
$Name$
$Author: cako $
$Date: 2006-12-26 17:02:26 -0200 (Ter, 26 Dez 2006) $

Casos de uso: uc-01.01.00
*/

function mudaTelaPrincipal(sPag){
    parent.frames["telaPrincipal"].location.replace(sPag);
}

//carrega o frame telaMensagem com a página informada
function mudaTelaMensagem(sPag){
    parent.frames["telaMensagem"].location.replace(sPag);
}

//carrega o frame oculto com a página informada
function mudaFrameOculto(sPag){
    parent.frames["oculto"].location.replace(sPag);
}

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

/*********************************************************************************
 Função para alterar o valor de um campo para a data atual
 Utilizar preferencialmente com o atributo onDblClick que chama a função ao
 executar um clique duplo dentro do campo
*********************************************************************************
Autor: Ricardo Lopes de Alencar -- 22/07/2003
Exemplo: <input type="text"  onDblClick="retornaData(this);" name="dataExame" value="">
*/
function retornaData(campo){
    var data = Hoje();
    data = data.replace(/-/gi,"\/");
    campo.value = data;
}

function MostraCalendario(sForm,sCampo,sessao){
    var x = 400;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/data0100Legado.php?'+sessao+'&sForm='+sForm+'&sCampo='+sCampo;
    var wVolta=false;
    var sAux = "wCal"+ sessaoid +" = window.open(sArq,'wCal"+ sessaoid +"','width=180px,height=180px,resizable=0,scrollbars=0,left='+x+',top='+y);";
    eval(sAux);
}


function MontaCSS(caminho){
   var sLinha;
   var sNavegador = navigator.appName;
   if (sNavegador == "Microsoft Internet Explorer"){
      sLinha = "<link rel=STYLESHEET type=text/css href="+caminho+"includes/stylos_ie.css>";
   } else {
      sLinha = "<link rel=STYLESHEET type=text/css href="+caminho+"includes/stylos_ns.css>";
   }
   document.write(sLinha);
}

function MontaCSSInclude(caminho){
   var sLinha;
   var sNavegador = navigator.appName;
   if (sNavegador == "Microsoft Internet Explorer"){
      sLinha = "<link rel=STYLESHEET type=text/css href="+caminho+"includes/stylos_ie.css>";
   } else {
      sLinha = "<link rel=STYLESHEET type=text/css href="+caminho+"includes/stylos_ns.css>";
   }
   document.write(sLinha);
}


function MontaCSSMenu(){
   var sLinha;
   var sNavegador = navigator.appName;
   if (sNavegador == "Microsoft Internet Explorer"){
      sLinha = "<link rel=STYLESHEET type=text/css href=includes/stylos_ie_menu.css>";
   } else {
      sLinha = "<link rel=STYLESHEET type=text/css href=includes/stylos_ns_menu.css>";
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
    retorno = retorno.replace(/,/, ".");
    if ( (retorno == "") || (isNaN(retorno)) ) {
        return 0;
    }
    return parseFloat(retorno);
}

function numericToFloat( stValor ){
    stValor = stValor.replace( ".", "" );
    stValor = stValor.replace( ",", "." );
    return parseFloat(stValor);
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
//      if (inputString.substr(0,1) != "-" && inputString.substr(0,inputString.length) != "0") inic = 0;
        if (inputString.substr(0,1) != "-" && res == null) inic = 0;
        else  inic = 1;
    }
    else inic = 0;
    for(i = inic ; i < inputString.length ; i++){
        if(inputString.charAt(i) == '0'){ espacosAntes++; }
        else {  break;  }
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
            else {  break;  }
        }
    }
    if(trimRight){
        for(i = inputString.length-1 ; i>0 ; i--){
            if(inputString.charAt(i) == ' '){ espacosDepois++; }
            else {  break;  }
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
//      campo           : this, para receber o objeto
//      separador       : '.' ou ',' ou o separador da parte inteira/fracao
//      digitosFracao   : quantos dígitos devem existir após o separador
//      prePos          : 0 para colocar o simbolo antes do número ou 1 para depois do número
//      simbolo         : o símbolo que deve ser colocado dentro do campo
    if ( campo.value.length == 0 ) { return; }
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
        break;  }
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
        break;  }
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
        break;  }
        case 'ramal': {  // 9999
            objEmFoco.maxLength=4;
            retorno = limpaParaMascara(objEmFoco.value,'numeros');
            objEmFoco.value = retorno.substr(0,4);
        break;  }
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
        break;  }
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
        break;  }
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
        break;  }
        case 'numero': {
            if(tamanho1 != 0){
                objEmFoco.maxLength = tamanho1;
            }
            retorno = limpaParaMascara(objEmFoco.value,'numeros');
            objEmFoco.value = retorno.substr(0,objEmFoco.maxLength);
        break;  }
        case 'inteiro': {
            if(tamanho1 != 0){
                objEmFoco.maxLength = tamanho1;
            }
            retorno = limpaZerosAEsquerda(limpaParaMascara(objEmFoco.value,'numeros'));
            objEmFoco.value = retorno.substr(0,objEmFoco.maxLength);
        break;  }
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
        break;  }
        case 'percentual': {  // 999
            objEmFoco.maxLength=3;
            retorno = limpaParaMascara(objEmFoco.value,'numeros');
            objEmFoco.value = retorno.substr(0,3);
/*          objEmFoco.maxLength=6;
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
        break;  }
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
        break;  }
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
        break;  }
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
        break;  }
        case 'valores': {
            for ( i=ind; i < sujeira.length; i++ ) {
                if( valores.indexOf(sujeira.charAt(i))>-1 ) {
                    retorno2 += sujeira.charAt(i);
                }
            }
            if (sujeira.charAt(0)=='-') {
                retorno2 = "-"+retorno2;
            }
        break;  }
        case 'letras': {
            for ( i=0; i < sujeira.length; i++ ) {
                if( letras.indexOf(sujeira.charAt(i))>-1 ) {
                    retorno2 += sujeira.charAt(i);
                }
            }
        break;  }
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

function VerificaCPF(valor) {

    function moduloCPF(str) {
        soma=0;
        ind=2;
        for(pos=str.length-1;pos>-1;pos=pos-1) {
            soma = soma + (parseInt(str.charAt(pos)) * ind);
            ind++;
            if(str.length>11) {
                if(ind>9) ind=2;
            }
        }
        resto = soma - (Math.floor(soma / 11) * 11);
        if(resto < 2) {
            return 0
        }
        else {
            return 11 - resto
        }
    }

    primeiro=valor.substr(1,1);
    falso=true;
    size=valor.length;
    if (size!=11){
        return false;
    }
    size--;
    for (i=2; i<size-1; ++i){
        proximo=(valor.substr(i,1));
        if (primeiro!=proximo) {
            falso=false
        }
    }
    if (falso){
        return false;
    }
    if(moduloCPF(valor.substring(0,valor.length - 2)) + "" + moduloCPF(valor.substring(0,valor.length - 1)) != valor.substring(valor.length - 2,valor.length)) {
        return false;
    }
    return true
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

function VerificaCNPJ(valor) {

    function moduloCNPJ(str) {
        soma=0;
        ind=2;
        for(pos=str.length-1;pos>-1;pos=pos-1) {
            soma = soma + (parseInt(str.charAt(pos)) * ind);
            ind++;
            if(str.length>11) {
                if(ind>9) ind=2;
            }
        }
        resto = soma - (Math.floor(soma / 11) * 11);
        if(resto < 2) {
            return 0
        }
        else {
            return 11 - resto
        }
    }

    primeiro=valor.substr(1,1);
    falso=true;
    size=valor.length;
    if (size!=14){
        return false;
    }
    size--;
    for (i=2; i<size-1; ++i){
        proximo=(valor.substr(i,1));
        if (primeiro!=proximo) {
            falso=false
        }
    }

    if (falso){
        return;
    }

    if(moduloCNPJ(valor.substring(0,valor.length - 2)) + "" + moduloCNPJ(valor.substring(0,valor.length - 1)) !=valor.substring(valor.length - 2,valor.length)) {
        return false;
    }
    return true
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
//        obj = eval("document.all."+arrDesabilita[i])
        obj = document.getElementById(arrHabilita[i]);
        if (obj != null) {
        obj.disabled = true;
//              obj.style.backgroundColor = "#E0E0E0";
        }
    }
}

function habilitaCampo(str) {
// Descrição:
    arrHabilita = new Array();
    arrHabilita = str.split(",")
    for (i=0; i<arrHabilita.length; i++) {
//        obj = eval("document.all."+arrHabilita[i]);
        obj = document.getElementById(arrHabilita[i]);
        if (obj != null) {
            obj.disabled = false;
//              obj.style.backgroundColor = "#FFFFFF";
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
        return false;
    }
}

function MostraImageUpload(sessao){
    var x = 400;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/imagens/uploadImageLegado.php?'+sessao;
    var wVolta=false;
    var sAux = "up"+ sessaoid +" = window.open(sArq,'up"+ sessaoid +"','width=400px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

function validaDecimais(fieldName, fieldValue, decal) {
    if (fieldValue.indexOf(',') == -1) fieldValue += ",";
    dectext = fieldValue.substring(fieldValue.indexOf(',')+1, fieldValue.length);
    if (dectext.length != decal)
    {
    alert ("Por favor, use um número com " + decal + " casas decimais.");
    fieldName.focus();
    return false
      }
   }
//<input type=Submit name=ok value="Ok" onClick="javascript:return validaDecimais(this.form.numbox, this.form.numbox.value, 3);">

function validaNumeros(fieldName) {
    fieldValue = fieldName.value
    retorno = fieldValue.replace(",", ".");
    if (isNaN(retorno)) {
    alert("Você deve usar apenas números.");
    fieldName.focus();
    return false;
    }
}
//<input type="submit" onClick="return validaNumeros(this.form.faced);">



function abreAjuda(sessao){
    var x = 10;
    var y = 10;
    var sessaoid = sessao.substr(10,6);
    var sArq = 'ajuda/index.php?'+sessao;
    var wVolta=false;
    var sAux = "hlp"+ sessaoid +" = window.open(sArq,'hlp"+ sessaoid +"','width=500px,height=300px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}


function alertaMensagem(erro,tipo){
    var x = 350;
    var y = 200;
    var sArq = '../../includes/mensagem.php?mensagem='+erro+'&tipo='+tipo;
    //var wVolta=false;
    mensagem = window.open(sArq,'mensagem','width=300px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

function alertaConfirma(template,pagina,chave,valor,men){
    var x = 350;
    var y = 200;
    var sArq = '../../includes/mensagem.php?mensagem='+men+'&tipo='+template+'&chave='+chave+'&valor='+valor+'&pag='+pagina;
    var wVolta=false;
    mensagem = window.open(sArq,'mensagem','width=300px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

function alertaDecisao(pagina,men){
    var x = 350;
    var y = 200;
    var sArq = '../../includes/mensagem.php?mensagem='+men+'&tipo=decisao&pag='+pagina;
    var wVolta=false;
    mensagem = window.open(sArq,'mensagem','width=300px,height=200px,resizable=1,scrollbars=0,left='+x+',top='+y);
}

function MostraEstados(nomeform,nomeestado,codestado,nommunicipio,codmunicipio,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../includes/estados.php?'+sessao+'&nomForm='+nomeform+'&nomEstado='+nomeestado+'&codEstado='+codestado+'&nomMunicipio='+nommunicipio+'&codMunicipio='+codmunicipio;
    var wVolta=false;
    var sAux = "mest"+ sessaoid +" = window.open(sArq,'mest"+ sessaoid +"','width=300px,height=120px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/***************************************************************************
Esta função faz o cursor mudar de campo automaticamente ao atingir
um valor previamente estabelecido
Utilização:
    Insira o seguinte o comando dentro da tag <input>:
         onKeyUp="return autoTab(this, n, event);"
         onde n é o contador de caracteres
Exemplo:
    <input type="text" name="ddd" size=2 onKeyUp="return autoTab(this, 2, event);">
    Na tag acima o cursor passa para o próximo campo após a entrada de dois caracteres
/**************************************************************************/
function autoTab(input,len, e) {
    var isNN = (navigator.appName.indexOf("Netscape")!=-1);
    var keyCode = (isNN) ? e.which : e.keyCode;
    var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];
        if(input.value.length >= len && !containsElement(filter,keyCode)) {
            input.value = input.value.slice(0, len);
            input.form[(getIndex(input)+1) % input.form.length].focus();
        }
    function containsElement(arr, ele) {
        var found = false, index = 0;
            while(!found && index < arr.length)
                if(arr[index] == ele)
                    found = true;
                else
                    index++;
            return found;
    }
    function getIndex(input) {
        var index = -1, i = 0, found = false;
            while (i < input.form.length && index == -1)
                if (input.form[i] == input)index = i;
                else i++;
            return index;
    }
return true;
}

/*********************************************************************************
 Função para formatar o número no momento em que o usuário está digitando
 O número permanece sempre no formato xxxxx,xx
*********************************************************************************/
/*********************************************************************************
 Exemplo: onKeyPress="return(formataNumeroDecimais(this,'.',',',event))"
 retorna 1.057.689,04
*********************************************************************************/
function formataNumeroDecimais(fld, milSep, decSep, e) {
    var sep = 0;
    var key = '';
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '0123456789';
    var aux = aux2 = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
        if (whichCode == 13) return true;  // Enter
        if (whichCode == 0) return true;
        if (whichCode == 1) return true;
        if (whichCode == 2) return true;
        if (whichCode == 3) return true;
        if (whichCode == 4) return true;
        if (whichCode == 5) return true;
        if (whichCode == 6) return true;
        if (whichCode == 7) return true;
        if (whichCode == 8) return true;  // Backspace
        if (whichCode == 9) return true;
        if (whichCode == 10) return true;
        key = String.fromCharCode(whichCode);  // Get key value from key code
        if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
        len = fld.value.length;
        for(i = 0; i < len; i++)
            if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
        aux = '';
        for(; i < len; i++)
            if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
        aux += key;
        len = aux.length;
        if (len == 0) fld.value = '';
        if (len == 1) fld.value = '0'+ decSep + '0' + aux;
        if (len == 2) fld.value = '0'+ decSep + aux;
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += milSep;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
            fld.value = '';
            len2 = aux2.length;
            for (i = len2 - 1; i >= 0; i--)
                fld.value += aux2.charAt(i);
            fld.value += decSep + aux.substr(len - 2, len);
        }
    return false;
}

/*********************************************************************************
 Função para formatar o número no momento em que o usuário está digitando
 O número permanece sempre no formato xxxxx,xx
*********************************************************************************/
/*********************************************************************************
 Exemplo: onKeyPress="return(formataNumeroDecimais(this,'.',',',event))"
 retorna 1.057.689,04
*********************************************************************************/
function formataNumeroDecimaisNegativos(fld, milSep, decSep, e) {
    var sep = 0;
    var key = '';
    var i = j = 0;
    var len = len2 = 0;
    var strCheck = '-0123456789';
    var aux = aux2 = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
        if (whichCode == 13) return true;  // Enter
        if (whichCode == 0) return true;
        if (whichCode == 1) return true;
        if (whichCode == 2) return true;
        if (whichCode == 3) return true;
        if (whichCode == 4) return true;
        if (whichCode == 5) return true;
        if (whichCode == 6) return true;
        if (whichCode == 7) return true;
        if (whichCode == 8) return true;  // Backspace
        if (whichCode == 9) return true;
        if (whichCode == 10) return true;
        key = String.fromCharCode(whichCode);  // Get key value from key code
        if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
        len = fld.value.length;
        for(i = 0; i < len; i++)
            if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
        aux = '';
        for(; i < len; i++)
            if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
        aux += key;
        len = aux.length;
        if (len == 0) fld.value = '';
        if (len == 1) fld.value = '0'+ decSep + '0' + aux;
        if (len == 2) fld.value = '0'+ decSep + aux;
            if (len > 2) {
                aux2 = '';
                for (j = 0, i = len - 3; i >= 0; i--) {
                    if (j == 3) {
                        aux2 += milSep;
                        j = 0;
                    }
                    aux2 += aux.charAt(i);
                    j++;
                }
            fld.value = '';
            len2 = aux2.length;
            for (i = len2 - 1; i >= 0; i--)
                fld.value += aux2.charAt(i);
            fld.value += decSep + aux.substr(len - 2, len);
        }
    return false;
}

/*********************************************************************************
Função para abrir janela de Procura BEM
*********************************************************************************
Exemplo:  procuraBem("frm","codbem")
*/
function procuraBem(nomeform,campobem,sessao){
    var x = 200;
    var y = 120;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/popups/bem/procuraBem.php?'+sessao+'&nomForm='+nomeform+'&campoBem='+campobem;
    var wVolta=false;
    var sAux = "prbem"+ sessaoid +" = window.open(sArq,'prbem"+ sessaoid +"','width=650px,height=500px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

//**********************************************************************************************
// Faz o chamada de alerta
// Opções:
// tipo: incluir, n_incluir, alterar, n_alterar, sn_excluir, excluir, n_excluir, form e unica
// chamada: erro, aviso, cc, sn
// Ex.: alertaAviso('Carro Sport','incluir','aviso');
// Ex.: alertaAviso('@Campo nome obrigatório@Campo idade Obrigatorio','form','erro');
// Ex.: alertaAviso('Inflatores corrigidos','unica','erro');
//**********************************************************************************************
function alertaAviso(objeto,tipo,chamada,sessao,caminho){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    if( caminho == undefined ){
        caminho = "";
    }
//    var sArq = caminho+'../../includes/mensagem.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/mensagem.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    //var wVolta=false;
    //var sAux = "msga"+ sessaoid +" = window.open(sArq,'msga"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    //eval(sAux);
    mudaTelaMensagem(sArq);
}
//**********************************************************************************************
// Faz o chamada de alerta para Nivel 2
// Opções:
// tipo: incluir, n_incluir, alterar, n_alterar, sn_excluir, excluir, n_excluir, form e unica
// chamada: erro, aviso, cc, sn
// Ex.: alertaAviso('Carro Sport','incluir','aviso');
// Ex.: alertaAviso('@Campo nome obrigatório@Campo idade Obrigatorio','form','erro');
// Ex.: alertaAviso('Inflatores corrigidos','unica','erro');
//**********************************************************************************************
function alertaAvisoNivel2(objeto,tipo,chamada,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = 'alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada='+chamada+'&obj='+objeto;
    //var wVolta=false;
    var sAux = "window.open(sArq,'','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}
//**********************************************************************************************
// Faz o chamada de alerta de Questão
// Ex.: alertaQuestao('../protocolo/andamentoPadrao/excluiPadrao.php','codAndamentoPadrao','21','Andamento no Gabinete do Diretor','sn_excluir');
//**********************************************************************************************
function alertaQuestao(pagina,chave,valor,objeto,tipo,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popups/alerta/alerta.php?'+sessao+'&tipo='+tipo+'&chamada=sn&chave='+chave+'&valor='+valor+'&pagQuestao='+pagina+'&obj='+objeto;
    var wVolta=false;
    var sAux = "window.open(sArq,'','width=350px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

function alertaQuestaoFabio(pagina,tipo,sessao){
    var x = 350;
    var y = 200;
    //alert(caminho);
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../includes/alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada=pp&chave='+chave+'&valor='+valor+'&pag='+pagina+'&obj='+objeto;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+ sessaoid +"','width=350px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

function alertaQuestaoPopUp(pagina,tipo,sessao){
    var x = 350;
    var y = 200;
    var valor = 'pp_excluir';
    var chave = 'cod';
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../popups/alertaGenerico.inc.php?'+sessao+'&tipo='+tipo+'&chamada=pp&chave='+chave+'&valor='+valor+'&pag='+pagina;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+ sessaoid +"','width=350px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

function alertaQuestao2(pagina,tipo,sessao){
    var x = 350;
    var y = 200;
    var valor = 'teste';
    var chave = 1;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../framework/popups/alerta/alerta.php?'+sessao+'&tipo='+tipo+'&chamada=sn&chave='+chave+'&valor='+valor+'&pagQuestao='+pagina;
    var wVolta=false;
    var sAux = "msgc"+ sessaoid +" = window.open(sArq,'msgc"+ sessaoid +"','width=350px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

//**********************************************************************************************
// Faz o chamada de alerta de Questão
// Ex.: alertaQuestao('../protocolo/andamentoPadrao/excluiPadrao.php','codAndamentoPadrao','21','Andamento no Gabinete do Diretor','sn_excluir');
//**********************************************************************************************
function alertaQuestaoOculto(pagina,chave,valor,objeto,tipo,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../includes/alerta.inc.php?'+sessao+'&tipo='+tipo+'&chamada=oculto&chave='+chave+'&valor='+valor+'&pag='+pagina+'&obj='+objeto;
    var wVolta=false;
    var sAux = "msgd"+ sessaoid +" = window.open(sArq,'msgd"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}
//**********************************************************************************************
// Faz o chamada de alerta de Questão e faz o submit
// Ex.: alertaSubmit("O mês da Data do Lote é diferente do mês de Processamento. Deseja continuar assim mesmo ?");
//**********************************************************************************************
function alertaSubmit(objeto,sessao){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../includes/alerta.inc.php?'+sessao+'&tipo=ccform&chamada=ccform&chave=&valor=&pag=&obj='+objeto;
    var wVolta=false;
    var sAux = "msge"+ sessaoid +" = window.open(sArq,'msge"+ sessaoid +"','width=300px,height=200px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura Local
*********************************************************************************
Exemplo:  procuraLocal('frm','codLocal','codExercicio')
*/
function procuraLocal(nomeform,campolocal,campoexercicio,sessao){
    var x = 350;
    var y = 200;
    var sArq = '../../includes/procuraLocal.php?'+sessao+'&nomForm='+nomeform+'&campoBem='+campolocal+'&campoexercicio='+campoexercicio;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prloc"+ sessaoid +" = window.open(sArq,'prloc"+ sessaoid +"','width=350px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura Setor
*********************************************************************************
Exemplo:  procuraSetor('frm','nomSetor','codSetor','codExercicio')
*/
function procuraSetor(nomeform,camponomesetor,camposetor,campoexercicio,sessao){
    var x = 350;
    var y = 200;
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popupsLegado/setor/procuraSetor.php?'+sessao+'&nomForm='+nomeform+'&campoNomeSetor='+camponomesetor+'&campoSetor='+camposetor+'&campoexercicio='+campoexercicio;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prset"+ sessaoid +" = window.open(sArq,'prset"+ sessaoid +"','width=550px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura Veículo
*********************************************************************************
Exemplo:  procuraVeiculo('frm','codVeiculo')
*/
function procuraVeiculo(nomeform,campoveiculo,sessao){
    var x = 350;
    var y = 200;
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/popupsLegado/veiculo/procuraVeiculo.php?'+sessao+'&nomForm='+nomeform+'&campoCodVeiculo='+campoveiculo;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prvei"+ sessaoid +" = window.open(sArq,'prvei"+ sessaoid +"','width=350px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura Motorista
*********************************************************************************
Exemplo:  procuraMotorista('frm','nomMotorista','codMotorista')
*/
function procuraMotorista(nomeform,camponommotorista,campocodmotorista,sessao){
    var x = 350;
    var y = 200;
    var sArq = '../../includes/procuraMotorista.php?'+sessao+'&nomForm='+nomeform+'&campoNomMotorista='+camponommotorista+'&campoCodMotorista='+campocodmotorista;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prmot"+ sessaoid +" = window.open(sArq,'prmot"+ sessaoid +"','width=350px,height=250px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura Processo
*********************************************************************************
Exemplo:  procuraProcesso('frm','codProcesso','anoExercicioProcesso')
*/
function procuraProcesso(nomeform,campocodprocesso,campoexercicio,sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../includes/procuraProcesso.php?'+sessao+'&nomForm='+nomeform+'&campoCodProcesso='+campocodprocesso+'&campoExercicio='+campoexercicio;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prpro"+ sessaoid +" = window.open(sArq,'prpro"+ sessaoid +"','width=400px,height=390px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para abrir janela de Procura Conta de Despesa
*********************************************************************************
Exemplo:  procuraContaDespesa('frm','nomConta','codConta');
          procuraContaDespesa('frm','','codConta');
*/
function procuraContaDespesa(nomeform,camponomeconta,campoconta,sessao){
    var x = 200;
    var y = 180;
    var sArq = '../../includes/procuraContaDespesa.php?'+sessao+'&nomForm='+nomeform+'&campoNomeConta='+camponomeconta+'&campoCodConta='+campoconta;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prcde"+ sessaoid +" = window.open(sArq,'prcde"+ sessaoid +"','width=450px,height=250px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para abrir janela de Procura Conta de Receita
*********************************************************************************
Exemplo:  procuraContaReceita('frm','nomConta','codConta','codReduzido','0');
          procuraContaReceita('frm','','codConta','','1');
 A variável reduzido indica se a busca deve procurar todas as classificações de receita ou somente
 as que possuem código reduzido (reduzido = 1).
*/
function procuraContaReceita(nomeform,camponomeconta,campoconta,camporeduzido,reduzido,sessao){
    var x = 200;
    var y = 180;
    var sArq = '../../includes/procuraContaReceita.php?'+sessao+'&nomForm='+nomeform+'&campoNomeConta='+camponomeconta+
               '&campoCodConta='+campoconta+'&campoCodReduz='+camporeduzido+'&reduzido='+reduzido;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prcre"+ sessaoid +" = window.open(sArq,'prcre"+ sessaoid +"','width=450px,height=250px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura CGM
*********************************************************************************
Exemplo:  procurarCgm("frm","numCgm","nomCgm","geral")
Exemplo:  procurarCgm("frm","numCgm","nomCgm","fisica")
Exemplo:  procurarCgm("frm","numCgm","nomCgm","juridica")
Exemplo:  procurarCgm("frm","numCgm","nomCgm","funcionario")  */
/*
ALTERAÇÃO:
* Foi acrescentado o parametro innerHtml que será usado quando o formulário possuir algum campo
para ser preenchido com a funcionalidade innerHtml.
* Ao parametro 'camponom' deve-se informar o 'id' do campo a ser preenchido com o nome via innerHtml
* O valor a ser passado para innerHtml é '1'.
Exemplo: procurarCgm("frm","numCgm","nomCgm","funcionario","1")
*/
function procurarCgm(nomeform,camponum,camponom,tipodebusca,sessao,innerHtml){
    var x = 350;
    var y = 200;
    var sessaoid = sessao.substr(10,6);
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/CGM/popups/cgm/FLProcurarCgm.php?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca+'&innerHtml='+innerHtml;
    var wVolta=false;
    var sAux = "window.open(sArq,'','width=800px,height=550px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para abrir janela de Procura Plano de Contas
*********************************************************************************
Exemplo:  procuraPlanoConta('frm','nomConta','codConta','codPlano,'0');
          procuraPlanoConta('frm','','codConta','','1');

 A variável reduzido indica se a busca deve procurar todas os planos de contas ou somente
 as que possuem código reduzido (reduzido = 1).

ALTERAÇÃO:
* Foi acrescentado o parametro innerHtml que será usado quando o formulário possuir algum campo
para ser preenchido com a funcionalidade innerHtml.
* Ao parametro 'camponomeconta' deve-se informar o 'id' do campo a ser preenchido com o nome da conta
via innerHtml
* O valor a ser passado para innerHtml é '1'.
Exemplo: procuraPlanoConta('frm','nomConta','codConta','codPlano,'0','1');
*/
function procuraPlanoConta(nomeform,camponomeconta,campoconta,campocodplano,reduzido,sessao,innerHtml){
    var x = 200;
    var y = 180;
    var sArq = '../../includes/procuraPlanoConta.php?'+sessao+'&nomForm='+nomeform+'&campoNomeConta='+camponomeconta+
               '&campoCodConta='+campoconta+'&campoCodPlano='+campocodplano+'&reduzido='+reduzido+'&innerHtml='+innerHtml;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prpco"+ sessaoid +" = window.open(sArq,'prpco"+ sessaoid +"','width=450px,height=250px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para abrir janela de Procura Programa de Trabalho
*********************************************************************************
Exemplo:  procuraPlanoConta('frm','nomConta','codConta','codDespesa');
          procuraPlanoConta('frm','','codConta','');
*/
function procuraProgramaTrabalho(nomeform,camponomeconta,campoconta,campocoddespesa,sessao){
    var x = 200;
    var y = 180;
    var sArq = '../../includes/procuraProgramaTrabalho.php?'+sessao+'&nomForm='+nomeform+'&campoNomeConta='+camponomeconta+
               '&campoCodConta='+campoconta+'&campoCodDespesa='+campocoddespesa;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prptb"+ sessaoid +" = window.open(sArq,'prptb"+ sessaoid +"','width=450px,height=250px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função que permite a digitação de apenas números e ponto
*********************************************************************************
Exemplo:  <input type='text' name='cod' onKeyPress="return(isNumber(this, event))" size='10' value=''>
*/
function isNumber(fld, e){
    var key = '';
    var strCheck = '0123456789.';
    var whichCode = (window.Event) ? e.which : e.keyCode;
        //Os códigos abaixo permitem a navegação através das setas, tecla home, end, delete...
        if (whichCode == 13) return true;  // Enter
        if (whichCode == 0) return true;
        if (whichCode == 1) return true;
        if (whichCode == 2) return true;
        if (whichCode == 3) return true;
        if (whichCode == 4) return true;
        if (whichCode == 5) return true;
        if (whichCode == 6) return true;
        if (whichCode == 7) return true;
        if (whichCode == 8) return true;  // Backspace
        if (whichCode == 9) return true;
        if (whichCode == 10) return true;
    key = String.fromCharCode(whichCode);  // Get key value from key code
    if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
}

/*********************************************************************************
Função que permite a digitação de apenas números
*********************************************************************************
Exemplo:  <input type='text' name='cod' onKeyPress="return(isNumeric(this, event))" size='10' value=''>
*/
function isNumeric(fld, e){
    var key = '';
    var strCheck = '0123456789';
    var whichCode = (window.Event) ? e.which : e.keyCode;
        //Os códigos abaixo permitem a navegação através das setas, tecla home, end, delete...
        if (whichCode == 13) return true;  // Enter
        if (whichCode == 0) return true;
        if (whichCode == 1) return true;
        if (whichCode == 2) return true;
        if (whichCode == 3) return true;
        if (whichCode == 4) return true;
        if (whichCode == 5) return true;
        if (whichCode == 6) return true;
        if (whichCode == 7) return true;
        if (whichCode == 8) return true;  // Backspace
        if (whichCode == 9) return true;
        if (whichCode == 10) return true;
    key = String.fromCharCode(whichCode);  // Get key value from key code
    if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
}

/*********************************************************************************
 Função que permite digitar apenas os caracteres que estiverem na
 variável dominio
*********************************************************************************
Exemplo:  <input type='text' name='cod' onKeyPress="return(isValido(this, event, '123-'))" size='10' value=''>
          Neste caso o usuário só poderia digitar "1", "2" , "3" ou "-"
*/
function isValido(fld, e, dominio){
    var key = '';
    var strCheck = dominio;
    var whichCode = (window.Event) ? e.which : e.keyCode;
        //Os códigos abaixo permitem a navegação através das setas, tecla home, end, delete...
        if (whichCode == 13) return true;  // Enter
        if (whichCode == 0) return true;
        if (whichCode == 1) return true;
        if (whichCode == 2) return true;
        if (whichCode == 3) return true;
        if (whichCode == 4) return true;
        if (whichCode == 5) return true;
        if (whichCode == 6) return true;
        if (whichCode == 7) return true;
        if (whichCode == 8) return true;  // Backspace
        if (whichCode == 9) return true;
        if (whichCode == 10) return true;
    key = String.fromCharCode(whichCode);  // Get key value from key code
    if (strCheck.indexOf(key) == -1) return false;  // Not a valid key
}

/*********************************************************************************
 Função que estabelece um limite de caracteres que podem ser inseridos dentro
 de uma textarea. Similar ao maxlength da tag input.
 Exemplo: <textarea name='desc' cols='40' rows='1'
            onKeyPress="return(maxTextArea(this.form.desc,10,event,false));"
            onBlur="return(maxTextArea(this.form.desc,10,event,true));"
            ></textarea>
*********************************************************************************/
function maxTextArea(campo,limite,e,blur){
    //var iLimite = limite - 1;
    var key = '';
    var strCheck = '';
    var whichCode = (window.Event) ? e.which : e.keyCode;
    if(blur){ // se estiver maior que o tamanho estabelecido, reduza-o
        campo.value = campo.value.substring(0, limite);
        return true;
    }
    //Se estiver maior que o tamanho estabelecido, não permitirá mais a entrada de caracteres
    if (campo.value.length > limite){
            campo.value = campo.value.substring(0, limite);
            return false;
    }
}

/*********************************************************************************
Função para abrir janela de Procura Arquivos de Documentos Digitais
*********************************************************************************
Exemplo:  copiaDigital()
*/
/*function copiaDigital(sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../includes/copiaDigital.php?'+sessao;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "cpdig"+ sessaoid +" = window.open(sArq,'cpdig"+ sessaoid +"','width=700px,height=390px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}*/

/*********************************************************************************
Função para abrir janela de Procura Domicilio
*********************************************************************************
Exemplo:  procuraDomicilio('frm','codDomicilio','logradouro')
*/
function procuraDomicilio(nomeform,campocoddomicilio,campologradouro,sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../../../../../gestaoAdministrativa/fontes/PHP/CSE/popups/domicilio/procuraDomicilio.php?'+sessao+'&nomForm='+nomeform+'&campoCodDomicilio='+campocoddomicilio+'&campoLogradouro='+campologradouro;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "window.open(sArq,'','width=500px,height=350px,resizable=1,scrollbars=0,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para apagar todas as opções de uma combo
 Informar o campo e a opção a partir da qual os itens serão apagados
*********************************************************************************
Autor: Ricardo Lopes de Alencar -- 18/07/2003
Exemplo:  limpaSelect(document.frm.comboOrgao,1); -- Apaga todas as opções menos a primeira opção: [0]
          limpaSelect(window.parent.frames["telaPrincipal"].document.frm.comboOrgao,0); -- Força apagar uma combo do frame principal
*/
function limpaSelect(sCampo,iOption){
    var campo = sCampo;
    var tam = campo.options.length;
        while (tam >= iOption) {
            campo.options[tam] = null;
            tam = tam - 1 ;
        }
    if(iOption > 0){
        campo.options[0].selected = true;
    }
}


/*********************************************************************************
Função para abrir janela de Procura de Autorização de empenho
*********************************************************************************
Exemplo:  procuraAutorizacaoEmpenho('frm','codAutorizacao')
*/
function procuraAutorizacaoEmpenho(nomeform,campoautorizacao,flag,sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../includes/procuraAutorizacaoEmpenho.php?'+sessao+'&nomForm='+nomeform+'&flag='+flag+'&campoAutorizacao='+campoautorizacao;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "window.open(sArq,'','width=450px,height=350px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura de Empenho
*********************************************************************************
Exemplo:  procuraEmpenho('frm','codEmpenho')
*/
function procuraEmpenho(nomeform,campoempenho,sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../includes/procuraEmpenho.php?'+sessao+'&nomForm='+nomeform+'&campoEmpenho='+campoempenho;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prem"+ sessaoid +" = window.open(sArq,'prem"+ sessaoid +"','width=450px,height=350px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
Função para abrir janela de Procura de Empenho
*********************************************************************************
Exemplo: mostraDadosProcesso("observacao",sessao)
*/
function mostraDadosProcesso(tipo,sessao){
    var x = 200;
    var y = 140;
    var sArq = '../../includes/mostraDadosProcesso.php?'+sessao+'&tipo='+tipo;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prem"+ sessaoid +" = window.open(sArq,'mdpr"+ sessaoid +"','width=450px,height=350px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*********************************************************************************
 Função para abrir janela de Procura Genérica
*********************************************************************************
Exemplo:  procuraGenerica('frm','nomConta','codConta');
*/
function procuraGenerica(nomeform,campocodigo,campodescricao,tabela,atributocod,atributonom,orderby,titulo,label,sessao){
    var x = 200;
    var y = 180;
    var sArq = '../../includes/procuraGenerica.php?'+sessao+'&nomForm='+nomeform+'&campoCodigo='+
                campocodigo+'&campoDescricao='+campodescricao+'&tabela='+tabela+'&atributoCod='+
                atributocod+'&atributoNom='+atributonom+'&orderBy='+orderby+'&titulo='+titulo+'&label='+label;
    var sessaoid = sessao.substr(10,6);
    var wVolta=false;
    var sAux = "prcde"+ sessaoid +" = window.open(sArq,'prcde"+ sessaoid +"','width=450px,height=250px,resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);
}

/*------------------------------------------------+
|VERIFICA SE A DATA INFORMADA EH VÁLIDA           |
|DEVER SE USADA NO MÉTODO ONBLUR                  |
+------------------------------------------------*/
function verificaData(campoData){
    if( campoData.value.length ){
        var stErro = false;
        if (campoData.value.substr(0,2)<1 || campoData.value.substr(0,2)>31){
            stErro = true;
        }
        if (campoData.value.substr(3,2)<1 || campoData.value.substr(3,2)>12){
            stErro = true;
        }
        if (campoData.value.substr(3,2)==4 || campoData.value.substr(3,2)==6 || campoData.value.substr(3,2)==9 || campoData.value.substr(3,2)==11){
            if (campoData.value.substr(0,2)>30){
                stErro = true;
            }
        }
        if (campoData.value.substr(3,2)==2){
            var bissexto=Number(campoData.value.substr(6,4)) % 4;
            if (bissexto!=0 && campoData.value.substr(0,2)>28){
                stErro = true;
            }
            if (bissexto==0 && campoData.value.substr(0,2)>29){
                stErro = true;
            }
        }
        if( stErro ){
            campoData.focus();
            return false;
        }else{
            return true;
        }
    }else{
        return true;
    }
}

/*------------------------------------------------+
|FILTRA A STRING INFORMADA, RETIRANDO TODOS       |
|CARACTERS NÃO ALFA NUMÉRICOS                     |
+------------------------------------------------*/
function filtraMascara( campo ){
  var expReg = new RegExp("[^a-zA-Z0-9]","g");
  var inCont = 0;
  var novoCampo = "";
  var tmpCampo;
  while(campo.length > inCont ){
     tmpCampo = campo.substr(inCont, 1);
     if( !expReg.test(tmpCampo) ){
        novoCampo += tmpCampo;
     }
     inCont++;
  }
  return novoCampo;
}

/*-------------------------------------------------------+
|VÁLIDA O CARACTER EM RELAÇÃO A SUA POSIÇÃO NA MÁSCARA   |
|VALIDANDO TIPO E VALOR                                  |
|EX.: "99:9X" O CARACTER X NÃO É VÁLIDO NA MASCARA 99:99 |
| "55:56" O CARACTER 6 NÃO É VÁLIDO NA MASCARA 55:55     |
| "AB=CD"O CARACATER D NÃO É VÁLIDO NA MASCARA CC=CC     |
+-------------------------------------------------------*/
function validaCaracter( masc, caracter, posicao){
   var caracterMasc = masc.charAt(posicao);
   var expRegCharNumber = new RegExp("[a-zA-Z0-9]","ig");
   if( expRegCharNumber.test( caracterMasc ) ){
      if( isNaN(caracterMasc)){
         var expValReg = new RegExp("[a-"+caracterMasc+"]","ig");
      }else{
      var expValReg = new RegExp("[0-"+caracterMasc+"]");
      }
      return expValReg.test(caracter);
   }else{
      return false;
   }
}

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA INFORMADA            |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraDinamico('99/99', this, event);">
*/
function mascaraDinamico( mascara, campo, evento){
   var expReg = new RegExp("[^a-zA-Z0-9]","g");
   var expRegTmp;
   var arExecExp = expReg.exec(mascara);
   var stCampoNovo = "";
   var stMascaraTmp = mascara.substr(arExecExp.index + 1 );
   var campoMasc = filtraMascara( campo.value );
   var inContIndex = 1;
   var inCont=0 ;
   var flagIndex = true;
   var whichCode = (window.Event) ? evento.which : evento.keyCode;

    if (whichCode == 13) return true;  // Enter
    if (whichCode == 0) return true;
    if (whichCode == 1) return true;
    if (whichCode == 2) return true;
    if (whichCode == 3) return true;
    if (whichCode == 4) return true;
    if (whichCode == 5) return true;
    if (whichCode == 6) return true;
    if (whichCode == 7) return true;
    if (whichCode == 8) return true;  // Backspace
    if (whichCode == 9) return true;
    if (whichCode == 10) return true;

   while( inCont < campoMasc.length && stCampoNovo.length < mascara.length ){
      caracter = campoMasc.substr(inCont, 1);
      if(validaCaracter( mascara, caracter, inContIndex - 1)){
         stCampoNovo += caracter;
         expRegTmp = new RegExp("[^a-zA-Z0-9]","g");
         if(expRegTmp.test(stMascaraTmp) || flagIndex){
            if( arExecExp.index == inContIndex){
               stCampoNovo += arExecExp[0];
               arExecExp = expReg.exec(mascara);
               inContIndex++;
               stMascaraTmp = mascara.substr(inContIndex );
               flagIndex = false;
            }
         }
      }
      inCont++;
      inContIndex++;
   }
   campo.value = stCampoNovo;
   return false;
}

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA DE CPF               |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraCPF( this, event);">
*/
function mascaraCPF( campo, evento ){
    return mascaraDinamico( '999.999.999-99', campo, evento);
}

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA DE CNPJ              |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraCNPJ(this, event);">
*/
function mascaraCNPJ( campo, evento ){
    return mascaraDinamico( '99.999.999/9999-99', campo, evento);
}

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA CEP                  |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraCEP(this, event);">
*/
function mascaraCEP( campo, evento ){
    return mascaraDinamico( '99999-999', campo, evento);
}

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA DATA                 |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraData(this, event);">
*/
function mascaraData( campo, evento ){
    return mascaraDinamico( '99/99/9999', campo, evento);
}


/*-------------------------------------------------------+
|FUNÇÕES PARA MASCARAMENTO DE VALORES TIPO MOEDA         |
+-------------------------------------------------------*/
//EX.:
//<input type="text" size="15" maxlength="14" align="right" onkeypress="return validaCharMoeda( this, event );" onkeyup="javascript: mascaraMoeda(  this, 2, event );" onBlur=" javascript: formataMoeda( this, 2, event );">
/*-------------------------------------------------------+
|RETIRA OS ZEROS A ESQUERDA DO VALOR DA MOEDA            |
+-------------------------------------------------------*/
function limpaZerosMoeda( valor ){
    while( valor.substr(0,1) == "0" ){
        valor = valor.substr(1, valor.length );
    }
    return valor;
}

/*-------------------------------------------------------+
|SEPARA UM INTEIRO NOS MILHARES                          |
+-------------------------------------------------------*/
function montaMilharMoeda( valor ){
    if( valor != "0" ){
        var expReg = new RegExp("[^0-9\-]","g");
        valor = valor.replace(expReg, '');
        valor = limpaZerosMoeda( valor );
        var tamanho = valor.length;
        var pos = tamanho - 3;
        var milhar = "";
        var cont = 0;
        while( pos > 0 && tamanho > 3 ){
            if(valor.substr(0, pos) != '-'){
                valor = valor.substr(0, pos)+"."+valor.substr(pos, tamanho);
            }
            tamanho = valor.length;
            pos = pos - 3;  
        }
    }
    return valor;
}

/*-------------------------------------------------------+
|FORMATA UM NÚMERO PARA MOEDA, FORMATANDO EM MILHARES    |
|E CASAS DECIMAIS                                        |
+-------------------------------------------------------*/
function formataMoeda( campo, decimais, evento , negativo){
    var moeda = "";
    if( campo.value.length > 0 ){
        var virgPos = campo.value.search(',');
        if( virgPos >= 0 ){//VERIFICA SE EXISTE VRIGULA NO CAMPO
            if(campo.value.search('-') == 0 && negativo)
                stMenos = campo.value.substring(1,campo.value.length);
            else
                stMenos = campo.value;

            var arMoeda = stMenos.split(',');//QUEBRA O CAMPO NA VIRGULA
            while( arMoeda[1].length < decimais ){//PREENCHE COM ZEROS AS CASAS DECIMAIS QUE ESTIVEREM FALTANDO
                arMoeda[1] += "0";
            }
            var milhar = montaMilharMoeda( arMoeda[0] );
            if( milhar.length == 0 ){
                milhar = "0";
            }
            moeda = milhar+","+arMoeda[1];
        } else {
            var zeros = "";
            while( zeros.length < decimais ){
                zeros += "0";
            }
            if(campo.value.search('-') == 0 && negativo)
                stMenos = campo.value.substring(1,campo.value.length);
            else
                stMenos = campo.value;

            moeda = montaMilharMoeda( stMenos )+","+zeros;
        }

        if(campo.value.search('-') == 0 && negativo)
            campo.value = '-'+ moeda;
        else
            campo.value = moeda;

    }
    return true;
}

/*-------------------------------------------------------+
|FORMATA O CAMPO PARA MOEDA DURANTE A DIGITAÇÃO DA MESMA |
+-------------------------------------------------------*/
function mascaraMoeda( campo, decimais, evento , negativo ){
    var virgPos = campo.value.search(',');
    var stMenos = '';
    
    if ( (evento.keyCode != 9)) {
        if ( (evento.keyCode != 16)) {
            //evento.keyCode == 188 é a virgula
            //VERIFICA SE O USUARIO TENTOU COLOCAR A VIRGULA NUMA POSICAO INVALIDA
            //SE ISSO OCORRER A VIRGULA SERA APAGADA
            if( evento.keyCode == 109){
                if(negativo){
                    if(campo.value.length != 1 )
                        campo.value = campo.value.substring(0,campo.value.length-1);
                }else
                        campo.value = campo.value.substring(0,campo.value.length-1);
            }else
            if( evento.keyCode == 188 ){    
                if( (campo.value.length - (decimais + 1)) > virgPos ){
                    campo.value = campo.value.replace(',','');
                }else if( campo.value.length == 1 ){
                    campo.value = "0,";//CASO TENHA SIDO DIGITADO SÁ A VIRGULA A FUNÇÃO COLOCA O ZERO
                }
            }else{
                if(campo.value.search('-') == 0 && negativo)
                    stMenos = campo.value.substring(1,campo.value.length);
                else
                    stMenos = campo.value;
            
                var arMoeda = stMenos.split(',');
                var inMoeda = montaMilharMoeda( arMoeda[0] );
                if( typeof(arMoeda[1]) != 'undefined'){//VERIFICA SE EXISTEM CASAS DECIMAIS DIGITADAS
                    if( inMoeda.length == 0 ){//
                        inMoeda = "0";
                    }
                    stMenos = inMoeda+","+arMoeda[1].substr(0, decimais);
                }else{
                    stMenos = inMoeda;
                }
            
                if(campo.value.search('-') == 0 && negativo)
                    campo.value = '-'+stMenos;
                else
                    campo.value = stMenos;
                
            }
        }
    }

}

/*-------------------------------------------------------+
|FORMATA O CAMPO PARA MOEDA DURANTE A DIGITAÇÃO DA MESMA |
+-------------------------------------------------------*/
function mascaraNumerico( campo, tamanho, decimais, evento, negativo ){
    RegExpMenos = new RegExp ("-","g");
    var boValorNegativo = false;

    if ( (evento.keyCode != 9)) {
        if ( (evento.keyCode != 16)) {
        
            if( (evento.keyCode != 0) ){
        
                if( (campo.value.length < (tamanho-(decimais+2))) || (campo.value.search(',') !='-1') || (evento.keyCode == 8) ){
                        
                    if( negativo ) {
                        //Verifica se o número é negativo
                        if( evento.keyCode == 109 ){
                            boValorNegativo = true;
                        } else {
                            if( (campo.value.charAt(0) == "-") && ( evento.keyCode != 107 && evento.keyCode != 61 ) ){
                                boValorNegativo = true;
                            }
                        }
                
                        //Calcula o tamanho máximo do campo
                        if( (campo.value.charAt(0) != "-") && (evento.keyCode == 109) ){
                            campo.maxLength = campo.maxLength + 1;
                        } else if( (campo.value.charAt(0) == "-") && ( evento.keyCode == 107 || evento.keyCode == 61 ) ) {
                            campo.maxLength = campo.maxLength - 1;
                        }
                    }
                    campo.value = campo.value.replace( RegExpMenos, '' );
                
                    var virgPos = campo.value.search(',');
                    //evento.keyCode == 188 é a virgula
                    //VERIFICA SE O USUARIO TENTOU COLOCAR A VIRGULA NUMA POSICAO INVALIDA
                    //SE ISSO OCORRER A VIRGULA SERA APAGADA
                    if( evento.keyCode == 188 || evento.keyCode == 108){
                        if( (campo.value.length - (decimais + 1)) > virgPos ){
                            campo.value = campo.value.replace(',','');
                        }else if( campo.value.length == 1 ){
                            campo.value = "0,";//CASO TENHA SIDO DIGITADO Só A VIRGULA A FUNÇÃO COLOCA O ZERO
                        }
                    }else {
                        var arMoeda = campo.value.split(',');//SEPARA O VALOR NA VIRGULA
                        var inMoeda = montaMilharMoeda( arMoeda[0] );
                        if( typeof(arMoeda[1]) != 'undefined'){//VERIFICA SE EXISTEM CASAS DECIMAIS DIGITADAS
                            if( inMoeda.length == 0 ){//
                                inMoeda = "0";
                            }
                            campo.value = inMoeda+","+arMoeda[1].substr(0, decimais);
                        }else{
                            campo.value = inMoeda;
                        }
                    }    
                
                    //Coloca o menos da frente se o valor for negativo
                    if( negativo ) {
                        if( boValorNegativo ) {
                            campo.value = "-" + campo.value;
                        }
                    }
                } else {        
                    campo.value = campo.value + ',';
                }
            } else {
                campo.value = campo.value.replace(/\^|~|´|`/g,'');
            }
        }
    }
}

/*-------------------------------------------------------+
|FORMATA O CAMPO PARA MOEDA DURANTE A DIGITAÇÃO DA MESMA |
+-------------------------------------------------------*/
function mascaraNumericoBR( campo, tamanho, decimais, evento, negativo ){
    RegExpMenos = new RegExp ("-","g");
    var boValorNegativo = false;

    if ( (evento.keyCode != 9)) {
        if ( (evento.keyCode != 16)) {

            if( (evento.keyCode != 0) ){
                campo.value = campo.value.replace(/[\.]/g, '');

                if( (campo.value.length < (tamanho-decimais)) || (campo.value.search(',') !='-1') || (evento.keyCode == 8) ){

                    if( negativo ) {
                        //Verifica se o número é negativo
                        if( evento.keyCode == 109 ){
                            boValorNegativo = true;
                        } else {
                            if( (campo.value.charAt(0) == "-") && ( evento.keyCode != 107 && evento.keyCode != 61 ) ){
                                boValorNegativo = true;
                            }
                        }

                        //Calcula o tamanho máximo do campo
                        if( (campo.value.charAt(0) != "-") && (evento.keyCode == 109) ){
                            campo.maxLength = campo.maxLength + 1;
                        } else if( (campo.value.charAt(0) == "-") && ( evento.keyCode == 107 || evento.keyCode == 61 ) ) {
                            campo.maxLength = campo.maxLength - 1;
                        }
                    }
                    campo.value = campo.value.replace( RegExpMenos, '' );

                    var virgPos = campo.value.search(',');
                    //evento.keyCode == 188 é a virgula
                    //VERIFICA SE O USUARIO TENTOU COLOCAR A VIRGULA NUMA POSICAO INVALIDA
                    //SE ISSO OCORRER A VIRGULA SERA APAGADA
                    if( evento.keyCode == 188 || evento.keyCode == 108){
                        if( (campo.value.length - (decimais + 1)) > virgPos ){
                            campo.value = campo.value.replace(',','');
                        }else if( campo.value == ',' ){
                            campo.value = "0,";//CASO TENHA SIDO DIGITADO Só A VIRGULA A FUNÇÃO COLOCA O ZERO
                        }
                    }else {
                        var arMoeda = campo.value.split(',');//SEPARA O VALOR NA VIRGULA
                        var inMoeda = montaMilharMoeda( arMoeda[0] );
                        if( typeof(arMoeda[1]) != 'undefined'){//VERIFICA SE EXISTEM CASAS DECIMAIS DIGITADAS
                            if( inMoeda.length == 0 ){//
                                inMoeda = "0";
                            }
                            campo.value = inMoeda+","+arMoeda[1].substr(0, decimais);
                        }else{
                            campo.value = inMoeda;
                        }
                    }

                    //Coloca o menos da frente se o valor for negativo
                    if( negativo ) {
                        if( boValorNegativo ) {
                            campo.value = "-" + campo.value;
                        }
                    }
                } else {
                    var inMoeda = montaMilharMoeda( campo.value );
                    campo.value = inMoeda + ',';
                }
            } else {
                campo.value = campo.value.replace(/\^|~|´|`/g,'');
            }
        }
    }
}

/*-------------------------------------------------------+
|SÓ PERMITE A ENTRADA DE CARACTERES VÁLIDOS PARA MOEDAS  |
+-------------------------------------------------------*/
function validaCharMoeda( campo, evento ){
    var arMoeda = campo.value.split(',');//SEPARA O VALOR NA VIRGULA

    if ( navigator.appName == "Netscape" ){
        var teclaPress = evento.which;
    } else {
        var teclaPress = evento.keyCode;
    }
    var retorno = true;
    var expReg = new RegExp("[0-9,\-]","g");
    var novo = String.fromCharCode( teclaPress );
    if( !validaTecla( evento.keyCode ) ){
        //NÃO PERMITE A ENTRADA DE CARACTERES DIFERENTES DE NUMEROS E VIRGULA
        if( novo.search(expReg) ){
            retorno = false;
        }else{
            //VERIFICA SE JAH EXISTE VIRGULA NO CAMPO
            //SE HOUVER NAO PERMITE A ENTRADA DE OUTRA
            var strTemp = campo.value + String.fromCharCode( teclaPress );
            if( strTemp.search(',') != strTemp.lastIndexOf(',') ){
                retorno = false;
            }
        }
    }
    return retorno;
}

function validaTecla( tecla ){
    var retorno = false;
    if ( navigator.appName == "Netscape" ){
        switch(tecla){
            //backspace
            case 8: retorno = true; break;
            //tab
            case 9: retorno = true; break;
            //enter
            case 13: retorno = true; break;
            //capslock
            case 20: retorno = true; break;
            //esc
            case 27: retorno = true; break;
            //pagup
            case 33: retorno = true; break;
            //pagdown
            case 34: retorno = true; break;
            //end
            case 35: retorno = true; break;
            //home
            case 36: retorno = true; break;
            //esquerda
            case 37: retorno = true; break;
            //cima
            case 38: retorno = true; break;
            //direita
            case 39: retorno = true; break;
            //baixo
            case 40: retorno = true; break;
            //insert
            case 45: retorno = true; break;
            //delete
            case 46: retorno = true; break;
        }
    } else {
        switch(tecla){
            //backspace
            case 8: retorno = true; break;
        }
    }
    return retorno;
}
/*-------------------------------------------------------+
|FIM DAS FUNÇÕES PARA MASCARAMENTO DE VALORES TIPO MOEDAS|
+--------------------------------------------------------+


/*--------------------------------------------------------+
|PREENCHE UM CAMPO CONFORME O VALOR SETADO EM OUTRO CAMPO |
|ex.:
|<input type=text name=codTxt onChange ="javascript: preencheCampo(this, document.frm.codCombo);">
|<select name=codCombo onChange = "javascript: preencheCampo(this, document.frm.codCombo);">
| <option>....</option>
|</select>
+--------------------------------------------------------*/
function preencheCampo( selecionado, preenchido ){
    var iIndice = 0;
    var formulario = selecionado.form.name;
    var d = eval("document."+formulario);
    var iIndex;

    if( selecionado.type == "select-one" && selecionado.value.toUpperCase() == "XXX" ){
        preenchido.value = "";
        return true;

    }else{
        preenchido.value = selecionado.value;

        if( preenchido.type == "select-one" &&  preenchido.value != selecionado.value ){
            alertaAviso("@Valor inválido. ("+selecionado.value+")",'form','erro','<?=$sessao->id?>');
            //selecionado.value = "";
            preenchido.selectedIndex = 0;
            return false;

        }else{
                for(var iCont = 1 ; iCont < d.elements.length ; iCont++){
                    if( d.elements[iCont].name == selecionado.name ){
                        break;
                    }
                }
                if( selecionado.type == "select-one" ){
                    iIndex = iCont+1;
                }else{
                    iIndex = iCont+2;
                }
                if( ( d.elements.length - iIndex ) > 0  ){
                    //d.elements[iIndex].disabled = false;
                    d.elements[iIndex].focus();
                }
                return true;
        }
    }
    return true;
}

/*--------------------------------------------------------+
|PREENCHE AS COMBOS DE CLASSIFICAÇÃO E ASSUNTO            |
|ex.:                                                     |
+--------------------------------------------------------*/
    function preencheCA(variavel, valor){
        var targetTmp = document.frm.target;
        document.frm.target = "oculto";
        var actionTmp = document.frm.action;
        //var actionTmp = "includes/filtrosProcesso.inc.php?<?=$sessao->id?>";
        document.frm.action += "&variavel="+variavel+"&valor="+escape(valor)+"&ctrl=100&controle=100";
        document.frm.submit();
        //document.frm.action = actionTmp;
        document.frm.target = targetTmp;
    }
    
	function preencheCA_ano(variavel, valor){
		ano = valor.substr((valor.length)-4,4);
		valor = valor.substr(0,(valor.length)-4);
		var targetTmp = document.frm.target;
        document.frm.target = "oculto";
        var actionTmp = document.frm.action;
        //var actionTmp = "includes/filtrosProcesso.inc.php?<?=$sessao->id?>";
        document.frm.action += "&anoOrgao="+ano+"&variavel="+variavel+"&valor="+escape(valor)+"&ctrl=100&controle=100";
        document.frm.submit();
        //document.frm.action = actionTmp;
        document.frm.target = targetTmp;
    }

/*--------------------------------------------------------+
|Verifica se existe um valor dentro de uma combo,
|selecionando-o se houver
|ex.:
|<input type=text name=codTxt onBlur="javascript: validaCombo(this.value, document.frm.codCombo);">
|<select name=codCombo
| <option>....</option>
|</select>
|Autor: Ricardo Lopes 27/01/2004
+--------------------------------------------------------*/
    function validaCombo(iCod,campo){
        var cod = iCod;
        var val;
        var erro = true;
        var f = document.frm;
        var tam = campo.options.length - 1;

        //Percorre todos os valores para encontrar qual item da combo tem o valor digitado
        while (tam >= 0) {
            val = campo.options[tam].value;
            if(cod==val){
                campo.options[tam].selected = true;
                erro = false;
            }
            tam = tam - 1 ;
        }
        //Se não encontrou o valor o código digitado é inválido
        if(erro){
            return false;
        }else{
            return true;
        }
    }

/*------------------------------------------------------------------+
|Abre popUp de pesquisa - por Marcelo B. Paulino (27/02/2004)       |
|                                                                   |
+------------------------------------------------------------------*/

/*
Definicao dos Paramentros:

-> arquivo:     nome do arquivo que sera aberto na janela
-> nomeform:    nome do formulario
-> camponum:    campo para onde sera enviado o codigo encontrado apos a pesquisa
-> camponom:    campo para onde sera enviado o nome encontrado apos a pesquisa
-> tipodebusca: geral ou usuario
-> sessao:      Session Id
-> width:       largura da janela
-> height:      altura da janela
*/

function abrePopUp(arquivo,nomeform,camponum,camponom,tipodebusca,sessao,width,height){

    // Definicao de Largura e Altura da Janela
    if (width == '') {
        width = 800;
    }

    if (height == '') {
        height = 550;
    }

    // Definicao da Localizacao da Janela
    var x = 0;
    var y = 0;

    var sessaoid = sessao.substr(10,6);

    // Definicao da URL completa do arquivo a ser aberto
    var sArq = '../../popups/popups/'+arquivo+'?'+sessao+'&nomForm='+nomeform+'&campoNum='+camponum+'&campoNom='+camponom+'&tipoBusca='+tipodebusca;

    //var wVolta=false;
    var sAux = "prcgm"+ sessaoid +" = window.open(sArq,'prcgm"+ sessaoid +"','width="+width+",height="+height+",resizable=1,scrollbars=1,left="+x+",top="+y+"');";
    eval(sAux);

}

function formataValor(campo)
{
    campo.value = filtraCampo(campo);
    vr = campo.value;
    tam = vr.length;

    if ( tam <= 2 ){ 
        campo.value = vr ; }
    if ( (tam > 2) && (tam <= 5) ){
        campo.value = vr.substr( 0, tam - 2 ) + ',' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 6) && (tam <= 8) ){
        campo.value = vr.substr( 0, tam - 5 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 9) && (tam <= 11) ){
        campo.value = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 12) && (tam <= 14) ){
        campo.value = vr.substr( 0, tam - 11 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
    if ( (tam >= 15) && (tam <= 18) ){
        campo.value = vr.substr( 0, tam - 14 ) + '.' + vr.substr( tam - 14, 3 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;}                 		
}   

function filtraCampo(campo)
{
    var s = '';
    var cp = '';
    var regra = new RegExp("[0-9]");
    vr = campo.value;
    tam = vr.length;
            
    for (i = 0; i < tam ; i++) {  
        var conferir = regra.exec(vr.substring(i,i + 1));   
        if (vr.substring(i,i + 1) != "/" && vr.substring(i,i + 1) != "-" && vr.substring(i,i + 1) != "."  && vr.substring(i,i + 1) != "," && conferir != null){
            s = s + vr.substring(i,i + 1);}
    }
    campo.value = s;
    return cp = campo.value
}

function atualizaFormataValor(campo)
{
    campo.value = filtraCampo(campo);
    vr = campo.value;
    tam = vr.length;

    if ( tam <= 2 ){ 
        campo.value = campo.value + ',' + '00'; 
    } else {
        formataValor(campo);
    }
}