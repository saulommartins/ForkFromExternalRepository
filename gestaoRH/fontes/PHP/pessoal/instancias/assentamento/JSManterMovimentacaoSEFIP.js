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


/*****
* Javacript de Movimentação SEFIP
* Data de Criação: 07/02/2005


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.40
*/

/*
$Log$
Revision 1.12  2006/08/08 17:46:21  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">
function buscaValor(tipoBusca){
        
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';

    if ( tipoBusca == 'limpaForm' ) {
        limpar();
    }
     
   

}

function alteraDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function filtroSEFIP (acao){
     document.frm.stCtrl.value = acao;
     document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaSefipRetorno ( objeto ){

    texto = document.frm.stNumSefipRetorno.value;
    texto = trim(texto);
       
    if ( texto.length != 0 ){
        document.frm.stCtrl.value = 'buscaSefipRetorno';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&objeto=' + objeto;
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }else {
           document.getElementById('stSefipRetorno').innerHTML = '&nbsp';

           if ( texto.length != document.frm.stNumSefipRetorno.value.length ){
                mensagem = 'Valor inválido( ). ';
                alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');  
           }  
    }
}

function buscaCategoria( objeto ){

    if ( document.frm.inCodCategoria.value.length != 0){
       document.frm.stCtrl.value = 'buscaCategoria';
       document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&objeto=' + objeto;
       document.frm.submit();
       document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    }else {
        document.getElementById('stCategoria').innerHTML = '&nbsp';
    }    
  
}





function limpar (){
    
    document.frm.stCodigoSEFIP.value = '';
    document.frm.stDescricao.value   = '';


}

function limparEspecialidade(){

     document.frm.inCodCategoria.value = '';
     document.getElementById('stCategoria').innerHTML = '&nbsp';
     document.frm.stIndicativo.value  = " " ;
}




</script>
