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
<?
/**
* Arquivo de instância para popup de normas
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/

?>
<script type="text/javascript">

function habilitaLink( valor ){
    document.frm.stCtrl.value = valor;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function CancelarForm () {

<?php
     $stLink = "&pg=".Sessao::read('linkPopUp_pg')."&pos=".Sessao::read('linkPopUp_pos');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function formataValoresNorma(obj){
   var i;
   var string = '';
   var numZero = 0;
   var zeroDireita = 0;

   var valor = obj.value;
    for(j = 0; j<valor.length; j++) {
        if(valor[j] == '0') {
            numZero++;
        }
    }

    if(numZero < valor.length) {
        for(i = 0; i<valor.length; i++) {
            if( (valor[i] == '0') && ( zeroDireita == 0) ) {
                string += valor[i].ltrim();
            } else {
                zeroDireita = 1;
                string += valor[i];
            }
        }
    } else {
        string = valor;
    }
	obj.value = string;	
}

String.prototype.ltrim = function() {
	return this.replace('0',"");
}

</script>
