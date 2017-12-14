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
    * Arquivo JavaScript
    * Data de Criação   : 27/08/2014
    

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: JSManterRetirarVeiculo.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso:
*/

?>
<script type="text/javascript">

/*-------------------------------------------------------+
|MASCARA O CAMPO CONFORME A MÁSCARA INFORMADA            |
+--------------------------------------------------------+
Ex.: <input type="text" maxlength="90" size="90" name="campoMasc" onKeyUp = "JavaScript: mascaraDinamico('99/99', this, event);">
Copiado de mascara.js, para retirar a Anulação da mascara quando for digitado Backspace.
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
	//if (whichCode == 8) return true;  // Backspace
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

function mascaraHorasTrabalhadas(ent, event)
{
    var valor = ent.value.replace(":", "");
   
    var qtd = valor.length;
    var i=1;
    var valorSaida = '';
    var whichCode = (window.Event) ? event.which : event.keyCode;

    // Backspace ou Delete
    if (whichCode == 8 || whichCode == 46 ){
        valorSaida = '0' + valor;
    }
    // left, up, right, down
    else if( whichCode == 37 || whichCode == 38 || whichCode == 39 || whichCode == 40 ){
        ent.selectionStart = ent.value.length; ent.selectionEnd = ent.value.length;
        valorSaida = ent.value;
    }else{
        //Se Não é numero, não faz nada.
        if (isNaN(valor[5])) {
            valorSaida=valor;
        }else{
            if (valor[0]==0) {
                for(i=1;i<qtd;i++){
                    valorSaida = '' + valorSaida + valor[i];
                }
            }else{
                for(i=0;i<(qtd-1);i++){
                    valorSaida = '' + valorSaida + valor[i];
                }
            }
        }
    }
    ent.value=valorSaida;

    mascaraDinamico( '999:99', ent, event);
}


function verificaHorasTrabalhadas( hora) {
    mensagem=true;
    stHora = "";
    inHora = hora.value.replace(":", '');

    if ( inHora.length == 1 ){
        hora.value = '000:0'+ arHora ;
        mensagem = false;
    }
    else if ( inHora.length == 2 ) {
        stHora = '000:'+ inHora;
        if (inHora<60){
            hora.value = stHora;
            mensagem = false;
        }
    }
    else if (inHora.length ==  3){
        stHora = '00' + inHora[0] + ':' +inHora[1]+inHora[2];
        if (inHora[1]<6){
            hora.value = stHora;
            mensagem = false;
        }
    }
    else if ( inHora.length == 4 ) {
        stHora = '0' + inHora[0] + inHora[1] + ':' +inHora[2]+inHora[3];
        if (inHora[2]<6){
            hora.value = stHora;
            mensagem = false;
        }
    }
    else if ( inHora.length == 5 ) {
        stHora = inHora[0] + inHora[1] +inHora[2] + ':' +inHora[3]+inHora[4];
        if (inHora[3]<6){
            hora.value = stHora;
            mensagem = false;
        }
    }else{
        stHora  = '000:00';
        hora.value = stHora;
    }

    arHora = hora.value.split(":");
    
    if (arHora[1]) {
        stHoraTrabalhada = arHora[1] / 0.6;
    }else{
        stHoraTrabalhada = 00;
    }
    
    stHoraTrabalhada = arHora[0]+":"+Math.round(stHoraTrabalhada);

    document.getElementById('stHoraTrabalhada').value = stHoraTrabalhada;
    
    if( mensagem ) {
        hora.value='000:00';
        alertaAviso('@Campo Quantidade de Horas Trabalhadas inválido!('+stHora+')','form','erro','<?=Sessao::getId();?>');
    }
}


function validaCampos(){
    if (Valida()) {
        var Erro = false;
        if (document.getElementById('horaTrabalhada') ) {
            horaTrabalhada = document.getElementById('horaTrabalhada').value;
            horaTrabalhada = horaTrabalhada.replace(":", '');
            if (horaTrabalhada<1) {
                document.getElementById('horaTrabalhada').focus();
                alertaAviso('@Campo Quantidade de Horas Trabalhadas inválido!()','form','erro','<?=Sessao::getId();?>');
                Erro = true;
            }
        }
        if (!Erro) {
            BloqueiaFrames(true,false);
            Salvar();
        }
    }
}

</script>           
