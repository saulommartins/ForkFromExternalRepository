<?php
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
?>
<?php
/**
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html par
$nome = $_POST['nomCgm'];

?>
<script>
    function trimString(inputString,trimLeft,trimRight)
    {
        outputString  = '';
        espacosAntes  = 0;
        espacosDepois = 0;
        if (trimLeft) {
            for (i = 0 ; i < inputString.length ; i++) {
                if (inputString.charAt(i) == ' ') { espacosAntes++; } else {  break;  }
            }
         }
         if (trimRight) {
             for (i = inputString.length-1 ; i>0 ; i--) {
                 if (inputString.charAt(i) == ' ') { espacosDepois++; } else {  break;  }
             }
         }
         outputString =  inputString.substr(espacosAntes);
         outputString = outputString.substr(0,(outputString.length-espacosDepois));

         return outputString;
    }

    nome = trimString('<?=$nome?>',true,true);

    if (nome.substr(1,1)==" " || nome.substr(1,1)=="") {
        mensagem = 'Primeiro nome deve conter no mínimo duas letras!';
        parent.alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        parent.document.frm.nomCgm.focus();
    } else {
        palavras = nome.split(" ");
        erro = false;
        for (i = 0 ; i < palavras.length ; i++) {
            if ((palavras[i].charCodeAt(palavras[i].length-1)<97 || palavras[i].charCodeAt(palavras[i].length-1)>123) && palavras[i].charCodeAt(palavras[i].length-1)!=46) {
                erro = palavras[i];
                break;
            }
        }
        if (erro) {
            mensagem = 'Caracter inválido no fim do nome "'+erro+'"!';
            parent.alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            parent.document.frm.nomCgm.focus();
        } else {
            if (palavras[i-1].charCodeAt(palavras[i-1].length-1)<97 || palavras[i-1].charCodeAt(palavras[i-1].length-1)>123) {
                mensagem = 'Último nome não pode ser abreviado!';
                parent.alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                parent.document.frm.nomCgm.focus();
            }

        }
    }

</script>
