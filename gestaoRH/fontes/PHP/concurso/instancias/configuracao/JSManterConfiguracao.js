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
* Página de Formulário Configuração de Concursos
* Data de Criação   : ???


* @author Analista: ???
* @author Desenvolvedor: ???

* @package URBEM
* @subpackage 

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.01
*/

/*
$Log$
Revision 1.3  2006/08/08 17:41:29  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function retiraCaracteres(obj){
    objValue    = obj.value;
    var count = 0;
    var countDec = 0;
    for (var i = 0; i < objValue.length; i++ ){
        letra = objValue.charAt(i);

      if(letra != "~" || letra != "´" || letra != "`" || letra != "¨" || letra != "^" || letra == "9" ){
        if( letra == "." || letra == "," || letra == "-" || letra == "9" ){
            if( letra == "." || letra == "," || letra == "-" ){
              count = count + 1;
            }
            if( letra == '9' && count == '1'){
                countDec++; 
            } 
            if( count > 1 ){
              obj.value = objValue.substring(0,i) + objValue.substring(i+1, objValue.length);
            }
            if( countDec > 2 ){
              obj.value = objValue.substring(0,i) + objValue.substring(i+1, objValue.length);
            }
        }else{
              obj.value = objValue.substring(0,i) + objValue.substring(i+1, objValue.length);
        
        }
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

function validaMascara(obj,pEvent){
    
        if ( navigator.appName == "Netscape" ){
            teclaPressionada = pEvent.which;
        } else {
            teclaPressionada = pEvent.charCode;
        }

        if(document.frm.stMascaraNota.value.length>0){
            if(teclaPressionada!= 190 &&
               teclaPressionada!= 188 &&
               teclaPressionada!= 108 &&
               teclaPressionada!= 109 &&
               teclaPressionada!= 57  &&
               teclaPressionada!= 46  &&
               teclaPressionada!= 8   &&
               teclaPressionada!= 105 &&
               teclaPressionada!= 110   
            ){
                return false;
            }else{
                return true;
            }
        }
        return true;
}

function Limpar(){
    document.frm.inTipoNorma.value     = '';
    document.frm.inCodTipoNorma.value  = '';
    document.frm.stMascaraNota.value   = '';

}

</script>
