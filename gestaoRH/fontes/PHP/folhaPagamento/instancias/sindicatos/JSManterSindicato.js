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
* Página de javascript
* Data de Criação   : ???


* @author Analista: ???
* @author Desenvolvedor: ???

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.03
*/

/*
$Log$
Revision 1.6  2006/08/08 17:43:53  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function preencherEvento(codigo,natureza){

        if ( codigo.length == 0 ){
            document.getElementById('stEvento').innerHTML = '&nbsp;'; 
        }
        else {
            document.frm.stCtrl.value = 'preencherEvento';
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodTipo='+codigo+'&stNatureza='+natureza;
            document.frm.submit();
            document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        }
    
}



function buscaFuncao(){
    if ( document.frm.inCodFuncao.value.length != 0 ){
        document.frm.stCtrl.value = 'buscaFuncao';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.target = 'oculto';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }else {
        document.getElementById('stFuncao').innerHTML = '&nbsp';
    } 
}

function buscaCGM(BuscaValor,campo){
    if(campo.value == 0){
       alertaAviso('@Valor inválido.','form','erro','<?=Sessao::getId();?>');
       campo.value = '';
    }
    else{     
        document.frm.stCtrl.value = BuscaValor;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    };
}


function validaDesconto(desconto){

    var mensagem = '';
    var d = document.frm;

    desconto = desconto.replace('.','');
    desconto = desconto.replace(',','.');

    if( desconto > 100 ){
        desconto = desconto.replace('.','');
    }
    if( desconto > 100 || desconto <=0 ){
        mensagem += "@Campo Percentual de desconto inválido!( " + d.flDesconto.value + " )";
        d.flDesconto.value = "";
        document.frm.flDesconto.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function validaDataBase(obj){
    if(obj.value<1 || obj.value>12){
        mensagem = 'O valor do campo Data-base deve estar entre 1 e 12';
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        obj.value="";
    }
}


function Cancelar(){
    document.frm.target = 'telaPrincipal';
    window.location = '<?=$pgList;?>?<?=Sessao::getId();?>&stAcao=<?=$stAcao;?>&pg=<?=$_GET["pg"]?>&pos=<?=$_GET["pos"]?>';
}


function focusIncluir(){
    document.frm.inNumCGM.focus();
}
    

</script>
