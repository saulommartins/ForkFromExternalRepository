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

$Revision: 15591 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 11:10:06 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.01.00
*/

function validaCaracterMascara( masc, caracter, posicao){
   var caracterMasc = masc.charAt(posicao);
   var expRegCharNumber = new RegExp("[a-zA-Z0-9]","ig");
   if( expRegCharNumber.test( caracterMasc ) ){
      if( isNaN(caracterMasc)){
        var expValReg = new RegExp("[a-"+caracterMasc.toLowerCase()+"A-"+caracterMasc.toUpperCase()+"]","ig");
      }else{
        var expValReg = new RegExp("[0-"+caracterMasc+"]","ig");
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
   var expReg = new RegExp("[^a-zA-Z0-9]","ig");
   var expRegTmp;
   var arExecExp = expReg.exec(mascara);
   var stCampoNovo = "";
    
   if( arExecExp ){
       var stMascaraTmp = mascara.substr(arExecExp.index + 1 );
   }
   var campoMasc = filtraAlfaNumerico( campo.value );
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
      if(validaCaracterMascara( mascara, caracter, inContIndex - 1)){
         stCampoNovo += caracter;
         expRegTmp = new RegExp("[^a-zA-Z0-9]","ig");
         if(expRegTmp.test(stMascaraTmp) || flagIndex){
            if( arExecExp &&  arExecExp.index == inContIndex){
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
|MASCARA O CAMPO CONFORME A MÁSCARA HORA                 |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraHora(this, event);">
*/
function mascaraHora( campo, evento ){
    return mascaraDinamico( '29:59', campo, evento);
}

/*-------------------------------------------------------+
|FUNÇÕES PARA MASCARAMENTO DE VALORES TIPO MOEDA         |
+-------------------------------------------------------*/
//EX.:
//<input type="text" size="15" maxlength="14" align="right" onkeypress="return float( this, event );" onkeyup="javascript: mascaraFloat(  this, 2, event );" onBlur=" javascript: moeda( this, 2, event );">
/*-------------------------------------------------------+
|FORMATA O CAMPO PARA MOEDA DURANTE A DIGITAÇÃO DA MESMA |
+-------------------------------------------------------*/
function mascaraFloat( campo, decimais, evento ){
    var virgPos = campo.value.search(',');
    //evento.keyCode == 188 é a virgula
    //VERIFICA SE O USUARIO TENTOU COLOCAR A VIRGULA NUMA POSICAO INVALIDA
    //SE ISSO OCORRER A VIRGULA SERA APAGADA
    if( evento.keyCode == 188 ){
        if( (campo.value.length - (decimais + 1)) > virgPos ){
            campo.value = campo.value.replace(',','');
        }else if( campo.value.length == 1 ){
            campo.value = "0,";//CASO TENHA SIDO DIGITADO SÁ A VIRGULA A FUNÇÃO COLOCA O ZERO
        }
    }else{
        var arMoeda = campo.value.split(',');//SEPARA O VALOR NA VIRGULA
        var inMoeda = inteiroParaMilhar( arMoeda[0] );
        if( typeof(arMoeda[1]) != 'undefined'){//VERIFICA SE EXISTEM CASAS DECIMAIS DIGITADAS
            if( inMoeda.length == 0 ){//
                inMoeda = "0";
            }
            campo.value = inMoeda+","+arMoeda[1].substr(0, decimais);
        }else{
            campo.value = inMoeda;
        }
    }

}
function preencheComZeros( mascara, campo, posicao ){
    var expReg = new RegExp("[a-zA-Z0-9]","ig");
    var stComplemento   = '';
    var inInicio  = 0;
    var inFim     = 0;

    if ( campo.value != "" ) {
        //posicao == 'E' || posicao == 'D'
        if(posicao == 'E'){
            inInicio  = 0;
            inFim     = mascara.length - campo.value.length;
        }else{
            inInicio  = campo.value.length;
            inFim     = mascara.length;
        }
        
        for(var inCount=inInicio; inCount<inFim; inCount++){
            if( mascara.charAt(inCount).search(expReg) == -1 ){
                stComplemento = stComplemento + mascara.charAt(inCount);
            }else{
                stComplemento = stComplemento + '0';
            }
        }
        if(posicao == 'E'){
            campo.value = stComplemento + campo.value;
        }else{
            campo.value = campo.value + stComplemento;
        }
    }
}

/*---------------------------------------------------------+
|Faz o preenchimento com zeros a esquerda no valor passado
|segundo a mascada informada.
|Ex.: mascara = 99999/999-99
|valor informado = 1/2-1 => resultado => 00001/002-01
|valor informado = 1     => resultado => 00001/000-00
|valor informado = 1/2   => resultado => 00001/002-00
+----------------------------------------------------------*/
function preencheComZerosPelaMascara( valor, mascara ){
    var expReg = new RegExp("[^a-zA-Z0-9]","ig");//EXPRESSÃO REGULAR PARA A MASCARA
    var expRegAN = new RegExp("[a-zA-Z0-9]","ig");//EXPRESSÃO REGULAR PARA OS SEPADORES
    mascara = mascara.replace( expRegAN, "0" );//TROCA OS VALORES DA MASCARA POR ZEROS
    var arMascara = mascara.split( expReg );//QUEBRA A MASCARA NOS SEPARADORES
    var arValor   = valor.split( expReg );//SEPARA O VALOR PASSADO NOS SEPARADORES
    //REMOVE OS CARACTERES ALFANUMERICOS E MONTA UM ARRAY COM OS SEPARADORES
    var arSeparador = mascara.replace( expRegAN, "" ).split("");
    var inCont = 0;
    var stValorPreenchido = "";
    var stTmp = "";
    do{
        if( arValor[inCont] ){//A CADA ITERAÇÃO VERIFICA SE EXISTE UM VALOR
            stTmp = arValor[inCont];
            //CASO O VALOR TENHA MAIS CARACTERES QUE A POSIÇÃO NA MASCARA
            //ESTE VALOR SERA EMPURRADO PARA A PRÓXIMA POSIÇÃO.
            //EX.:(MASC = 99/99, VALOR 111 => [0] = 11, [1] = 01 )
            if( stTmp.length > arMascara[inCont].length ){
                if( arValor[inCont + 1] == undefined ){
                    arValor[inCont + 1] = "";//SE O VALOR NÃO FOR DEFINIDO SETA UM AVALOR VAZIO
                }
                //CONTATENA O VALOR SEMPRE NA FRENTE DO PRÓXIMO VALOR
                arValor[inCont + 1] = stTmp.substr(arMascara[inCont].length ) + arValor[inCont + 1];
                stTmp =  stTmp.substr(0, arMascara[inCont].length );//REMOVE O EXESSO DA STRING
            }
        }else{//SE NÃO EXISTIR UM VALOR PARA A POSIÇÃO NA MASCARA É SETADO UM VALOR VAZIO
            stTmp = "";
        }
        //CONCATENA OS ZEROS COM O VALOR REFERENTE A POSIÇÃO NA MASCARA
        stValorPreenchido += arMascara[inCont].substr( 0,  arMascara[inCont].length - stTmp.length );
        stValorPreenchido += stTmp;
        if( arSeparador[inCont] ){//SE EXISTIR UM SEPARADOR É FEITO A CONCATENAÇÃO AQUI
            stValorPreenchido += arSeparador[inCont];
        }
        inCont++;
    }while( inCont < arMascara.length );
    return stValorPreenchido;
}

/*---------------------------------------------------------+
|preenche o processo com os zeros e caso não tenha sido
|o exercício, concatena este no final do valor
|valor informado = 1,"99999/9999","2006" => resultado => 00001/2006
|valor informado = "1/06,"99999/9999","" => resultado => 00001/0006
|valor informado = "1/2005","99999/9999","2006" => resultado => 00001/2005
+----------------------------------------------------------*/
function preencheProcessoComZeros( valor, mascara, exercicio ){
    //REMOVE OS CARACTERES ALFANUMERICOS E MONTA UM ARRAY COM OS SEPARADORES
    var arSeparador = mascara.replace( RegExp("[0-9a-zA-Z]", "ig"), "" ).split("");
    //PROCURA POR ALGUM CARACTER SEPARADOR SE NÃO ENCONTRAR PREENCHE CONCATENA O ANO DE EXERCÍCIO
    if( !RegExp("[^0-9a-zA-Z]").test( valor) ){
        valor += arSeparador[0]+exercicio;
    }else{
        //VERIFICA SE NÃO FOI INFORMADO O EXERCICIO, CASO NÃO ESTE É CONCATENADO AO VALOR
        if( parseInt(mascara.length) - parseInt(valor.length) == parseInt(exercicio.length) ){
            valor += exercicio;
        }
    }
    return preencheComZerosPelaMascara( valor, mascara );
}

function verificaHora( hora) {
    if ( hora.value.length == 1 ){
        hora.value = hora.value + '0:00';
    }else if ( hora.value.length == 3 ) {
              hora.value = hora.value + '00';
    }else if (hora.value.length ==  4){
              hora.value = hora.value + '0';
    }
}
/*-------------------------------------------------------+
|FIM DAS FUNÇÕES PARA MASCARAMENTO DE VALORES TIPO MOEDAS|
+-------------------------------------------------------*/
