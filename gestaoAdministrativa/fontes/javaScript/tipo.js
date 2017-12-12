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

* $Id: tipo.js 60017 2014-09-25 17:43:38Z franver $

Casos de uso: uc-01.01.00
*/

function inteiro(evento)
{
    var expRegular = new RegExp("[0-9]","g");
    var retorno = true;
    var teclaPressionada;
    var caracter;
    if (navigator.appName == "Netscape") {
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    
    caracter = String.fromCharCode(teclaPressionada);
    if (!validaTecla(evento.keyCode)) {
        if (!evento.ctrlKey && caracter.search(expRegular)
          || evento.ctrlKey && !(caracter=='c' || caracter=='x' || caracter=='v') && caracter.search(expRegular)) {
            retorno = false;
        }
    }

    return retorno;
}

function validaInteiroKeyUp( campo ) {
    RegExpNumero = new RegExp ("[^0-9]","g");
    campo.value = campo.value.replace( RegExpNumero, '' );
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UM TIPO INTERIO                   |
+------------------------------------------------------------------*/
function isInt( valor ){
    var expRegular = new RegExp("[^0-9]","g");
    var retorno = true;
    if( expRegular.test( valor ) ){
        retorno = false;
    }
    return retorno;
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UM TIPO FLOAT                     |
+------------------------------------------------------------------*/
function isFloat( valor ){
    var expRegular = new RegExp("[^0-9.,]","g");
    var retorno = true;
    if( expRegular.test( valor ) ){
        retorno = false;
    }
    return retorno;
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UMA DATA VALIDA                   |
+------------------------------------------------------------------*/
function isData( valor ){
    var retorno = true;
    function tipoData( data ){
        this.value = data;
    }
    if( valor.length > 0 ){
        campoData = new tipoData( valor );
        retorno = verificaData( campoData );
    }
    return retorno;
}

/*------------------------------------------------------------------+
|PERMITE A ENTRADA DE CARACTERS NUMÉRICOS E DA VIGULA               |
|<input type="text" onClick="javascript: return float( this );">    |
+------------------------------------------------------------------*/
function tfloat( campo, evento ){
    var expRegular = new RegExp("[0-9,\-]","g");
    var retorno = true;
    var teclaPressionada;
    var caracter;
    var strTemp;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    caracter = String.fromCharCode( teclaPressionada );
    if( !validaTecla( evento.keyCode ) ){
        if( caracter.search(expRegular) ){//NÃO PERMITE A ENTRADA DE CARACTERES DIFERENTES DE NUMEROS E VIRGULA
            retorno = false;
        }else{
            strTemp = campo.value + String.fromCharCode( teclaPressionada );
            if( strTemp.search(',') != strTemp.lastIndexOf(',') ){//VERIFICA SE JAH EXISTE VIRGULA NO CAMPO SE HOUVER NAO PERMITE A ENTRADA DE OUTRA
                retorno = false;
            }
        }
    }
    return retorno;
}
/*------------------------------------------------------------------+
|PERMITE A ENTRADA DE CARACTERS NUMÉRICOS E DA VIGULA               |
|<input type="text" onClick="javascript: return float( this );">    |
+------------------------------------------------------------------*/
function tfloatPonto( campo, evento ){
    var expRegular = new RegExp("[0-9.]","g");
    var retorno = true;
    var teclaPressionada;
    var caracter;
    var strTemp;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    caracter = String.fromCharCode( teclaPressionada );
    if( !validaTecla( evento.keyCode ) ){
        if( caracter.search(expRegular) ){//NÃO PERMITE A ENTRADA DE CARACTERES DIFERENTES DE NUMEROS VIRGULA
            retorno = false;
        }else{
            strTemp = campo.value + String.fromCharCode( teclaPressionada );
            if( campo.value.search( new RegExp('[.]','g') ) != -1 )
                retorno = false;
            if( caracter.search( new RegExp('[0-9]','g') ) != -1 )
                retorno = true;
        }
    }
    return retorno;
}

/*------------------------------------------------------------------+
|FORMATA UM NÚMERO PARA FLOAT, FORMATANDO EM MILHARES               |
|E CASAS DECIMAIS                                                   |
|<input type="text" onBlur="javascript: floatDecimal(this, 2, event );">   |
+------------------------------------------------------------------*/
function floatDecimal( campo, decimais, evento ){
    var camposFloat = "";
    var negativo = false;
   
    if ( campo.value.length == 0 ) {
        campo.value = "0";
    }

    if( campo.value.length >= 0 ){
        if(campo.value.search('-') == 0){
            negativo = true; 
            campo.value = campo.value.substring(1,campo.value.length);
        }else{
            negativo = false;
        }
        var virgPos = campo.value.search(',');
        if( virgPos >= 0 ){//VERIFICA SE EXISTE VRIGULA NO CAMPO
            var arcamposFloat = campo.value.split(',');//QUEBRA O CAMPO NA VIRGULA
            if( arcamposFloat[1].length < decimais ){
                while( arcamposFloat[1].length < decimais ){//PREENCHE COM ZEROS AS CASAS DECIMAIS QUE ESTIVEREM FALTANDO
                    arcamposFloat[1] += "0";
                }
            }else{
                 arcamposFloat[1] = arcamposFloat[1].substr( 0, decimais );
            }
            var milhar = inteiroParaMilhar( arcamposFloat[0] );
            if( milhar.length == 0 ){
                milhar = "0";
            }
            camposFloat = milhar+","+arcamposFloat[1];
        } else {
            var zeros = "";
            while( zeros.length < decimais ){
                zeros += "0";
            }
            camposFloat = inteiroParaMilhar( campo.value )+","+zeros;
        }
        if(negativo == true)
            campo.value = '-'+camposFloat;
        else
           campo.value = camposFloat;
    }
    return true;
}

function validaValorMaximoPermitido( campo, decimais ){
    if( campo.value.length > 0 ){
        var flValorMaximo = geraValorMaximoPermitido( campo, decimais );
        flValorMaximo = flValorMaximo.replace( ".", "" , "g");
        flValorMaximo = flValorMaximo.replace( ",", "." , "g");
        var flValorCampo = campo.value.replace( ".", "" , "g");
        flValorCampo = flValorCampo.replace( ",", "." , "g");
        if( parseFloat( flValorMaximo ) <  parseFloat(flValorCampo) ){
            return false;
        }else{
            return true;
        }
    }else{
        return true;
    }
}

function geraValorMaximoPermitido( campo, decimais ){
    var inTamanhoMaximo = campo.size - ( decimais + 1 );
    var inQtdPontos = inTamanhoMaximo % 3;
    inTamanhoMaximo = inTamanhoMaximo - inQtdPontos
    var inValorMaximo = "";
    for( var i = 0; i < inTamanhoMaximo; i++ ){
        inValorMaximo += "9";
    }
    var inValorMaximoDecimal = "";
    for( var i = 0; i < decimais ; i++ ){
        inValorMaximoDecimal += "9";
    }
    var flValorMaximo = inteiroParaMilhar(inValorMaximo)+","+inValorMaximoDecimal;
    return flValorMaximo;
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UMA DATA VALIDA                   |
|<input type="text" onBlur="javascript: return verificaData( this );">      |
+------------------------------------------------------------------*/
function verificaData(campoData)
{
    var boErro = false;    

    if( campoData.value.length ){
        if ( campoData.value.length < 6 ){
            boErro = false;
        }else{
            var exercicio = new Date().getFullYear();
            var tamanho = campoData.value.length;
            if (tamanho < 10) {
                if (tamanho == 9) {
                    if (trim(campoData.value).substr(6,3) == exercicio.toString().substr(0,3)) {
                        campoData.value = campoData.value.substr(0,6)+exercicio;
                    } else {
                        campoData.value = '';
                    }
                } else {
                    if (tamanho == 6) {
                        campoData.value = campoData.value+exercicio;
                    } else if ((tamanho == 7 && trim(campoData.value).substr(6,1) == '2') || (tamanho == 8 && trim(campoData.value).substr(6,2) == '20')) {
                        campoData.value = campoData.value.substr(0,6)+exercicio;
                    } else if (tamanho == 8) {
                        campoData.value  = campoData.value.substr(0,6) + '20' + campoData.value.substr(6,2);
                    } else {
                        campoData.value  = '';
                    }
                }
            }
            /*if ( campoData.value.length < 10 ) {
                if( campoData.value.substr(6,2) > 80 ) {
                    campoData.value  = campoData.value.substr(0,6) + '19' + campoData.value.substr(6,2);
                } else {
                    campoData.value  = campoData.value.substr(0,6) + '20' + campoData.value.substr(6,2);
                }  
            }*/            

            if(confereDataValida(campoData) == false) {
                return false;
            }

            if ( campoData.value.substr(0,2) < 1 || campoData.value.substr(0,2) > 31 ){
                boErro = true;
            }
            if ( campoData.value.substr(3,2) < 1 || campoData.value.substr(3,2) > 12 ){
                boErro = true;
            }
            if ( campoData.value.substr(3,2) == 4 || campoData.value.substr(3,2) == 6 || campoData.value.substr(3,2)==9 || campoData.value.substr(3,2) == 11 ){
                if ( campoData.value.substr(0,2) > 30 ){
                    boErro = true;
                }
            }
            if ( campoData.value.substr(3,2) == 2 ){
                var bissexto = Number(campoData.value.substr(6,4)) % 4;
                if ( bissexto != 0 && campoData.value.substr(0,2) > 28 ){
                    boErro = true;
                }
                if ( bissexto == 0 && campoData.value.substr(0,2) > 29 ){
                    boErro = true;
                }
            }
            if( boErro ){
                //campoData.focus();
                boErro = false;
            }else{
                boErro = true;
            }
        }
    }else{
        boErro = true;
    }
    return boErro;
}

/*Validar se data digitada é valida 
* passa a data para o formato yyyy-mm-dd para validar
* Alem de validar se a data tem os caracteres permitidos e se é valida
* entre 1000 e 2999!
*/
function confereDataValida(campoData)
{    
    arDataSeparada = campoData.value.split("/");
    dataValidaFormatada = arDataSeparada[2]+'-'+arDataSeparada[1]+'-'+arDataSeparada[0];
    
    // expressão regular para validar data onde serão testado parametros como ano bissexto e mes de fevereiro
    return isValidDate(dataValidaFormatada, 'YMD')
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UM CNPJ VALIDO                    |
|<input type="text" onBlur="javascript: return CNPJ( this );">      |
+------------------------------------------------------------------*/
function isCNPJ(valor) {
    valor = filtraAlfaNumerico( valor.value );
    var primeiro = valor.substr(1,1);
    var falso = true;
    var size =valor.length;
    var retorno = true;
    var proximo;

    if( size == 0 ){        
        retorno = false;
    }    
    else if( size != 14 ){
        retorno = false;
    }else{
        size--;
        for (i=2; i<size-1; ++i){
            proximo = ( valor.substr(i,1) );
            if ( primeiro!=proximo ) {
                falso = false;
            }
        }
        if (falso){
            retorno = true;
        }else{
            if(moduloCNPJ(valor.substring(0,valor.length - 2)) + "" + moduloCNPJ(valor.substring(0,valor.length - 1)) !=valor.substring(valor.length - 2,valor.length)) {
                retorno = false;
            }
        }
    }
    return retorno;
}

function moduloCNPJ(str) {
    var soma = 0;
    var ind = 2;
    var pos;
    var retorno;
    for( pos = str.length-1; pos>-1 ; pos = pos-1 ){
        soma = soma + ( parseInt(str.charAt(pos)) * ind );
        ind++;
        if(str.length>11) {
            if(ind>9) ind=2;
        }
    }
    resto = soma - (Math.floor(soma / 11) * 11);
    if(resto < 2) {
        retorno =  0;
    }else{
        retorno = 11 - resto;
    }
    return retorno;
}

/*------------------------------------------------------------------+
|VERIFICA SE O VALOR INFORMADO EH UM CNPJ VALIDO                    |
|<input type="text" onBlur="javascript: return CNPJ( this );">      |
+------------------------------------------------------------------*/

function isCPF(valor) {
    valor = filtraAlfaNumerico( valor.value );
    var primeiro = valor.substr(1,1);
    var falso = true;
    var size = valor.length;
    var retorno = true;
    if (size==0){
        retorno = true;
    }
    else if (size!=11){
        retorno = false;
    }else{
        size--;
        for (i=2; i<size-1; ++i){
            proximo = (valor.substr(i,1));
            if ( primeiro != proximo ){
                falso = false
            }
        }
        if (falso){
            retorno = false;
        }else{
            if(moduloCPF(valor.substring(0,valor.length - 2)) + "" + moduloCPF(valor.substring(0,valor.length - 1)) != valor.substring(valor.length - 2,valor.length)) {
                retorno = false;
            }
        }
    }
    return retorno;
}

function moduloCPF(str) {
    var soma = 0;
    var ind = 2;
    var resto;
    var retorno;
    for(pos = str.length-1; pos > -1; pos = pos-1) {
        soma = soma + (parseInt(str.charAt(pos)) * ind);
        ind++;
        if(str.length>11) {
            if(ind>9) ind=2;
        }
    }
    resto = soma - (Math.floor(soma / 11) * 11);
    if(resto < 2) {
        retorno = 0
    }else{
        retorno = 11 - resto
    }
    return retorno;
}

function isValidDate(dateStr, format) {
   if (format == null) { format = "YMD"; }
   format = format.toUpperCase();
   if (format.length != 3) { format = "YMD"; }
   if ( (format.indexOf("M") == -1) || (format.indexOf("D") == -1) ||  (format.indexOf("Y") == -1) ) { format = "MDY"; }
   if (format.substring(0, 1) == "Y") { // If the year is first
      var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
      var reg2 = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/
   } else if (format.substring(1, 2) == "Y") { // If the year is second
      var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
      var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/
   } else { // The year must be third
      var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
      var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
   }
   // If it doesn't conform to the right format (with either a 2 digit year or 4 digit year), fail
   if ( (reg1.test(dateStr) == false) && (reg2.test(dateStr) == false) ) { return false; }
   var parts = dateStr.split(RegExp.$1); // Split into 3 parts based on what the divider was
   // Check to see if the 3 parts end up making a valid date
   if (format.substring(0, 1) == "M") { var mm = parts[0]; } else       if (format.substring(1, 2) == "M") { var mm = parts[1]; } else { var mm = parts[2]; }
   if (format.substring(0, 1) == "D") { var dd = parts[0]; } else       if (format.substring(1, 2) == "D") { var dd = parts[1]; } else { var dd = parts[2]; }
   if (format.substring(0, 1) == "Y") { var yy = parts[0]; } else       if (format.substring(1, 2) == "Y") { var yy = parts[1]; } else { var yy = parts[2]; }
   if (parseFloat(yy) <= 50) { yy = (parseFloat(yy) + 2000).toString(); }
   if (parseFloat(yy) <= 99) { yy = (parseFloat(yy) + 1900).toString(); }
   var dt = new Date(parseFloat(yy), parseFloat(mm)-1, parseFloat(dd), 0, 0, 0, 0);
   if (parseFloat(dd) != dt.getDate()) { return false; }
   if (parseFloat(mm)-1 != dt.getMonth()) { return false; }
   return true;
}
