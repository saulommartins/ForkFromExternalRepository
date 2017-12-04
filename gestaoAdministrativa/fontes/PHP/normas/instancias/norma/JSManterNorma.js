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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.04.02
*/
?>
<script type="text/javascript">

function Limpar(){
    document.frm.stCtrl.value = "limpaLink";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>';
}
function validaExercicio(campo){
if ( campo.value < 1900 ){
    alertaAviso('@Exercício deve ser maior que 1900.','form','erro','<?=Sessao::getId();?>');
    document.frm.elements['stExercicio'].value = '';
    }
}


function limpaCampos () {
    document.frm.inCodTipoNorma.value = "";
    document.frm.stNomeTipoNorma.value = "";
    document.frm.inNumNorma.value = "";
    document.frm.inCodTipoNorma.value = "";
    document.frm.stNomeNorma.value = "";
    document.frm.stDescricao.value = "";
    document.frm.stDataPublicacao.value = "";
}

function focusIncluir(){
    document.frm.stNomeTipoNorma.focus();
}

function focusAlterar(){
    document.frm.inNumNorma.focus();
}

function focusIncluirFL(){
    document.frm.stNomTipoNorma.focus();
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
