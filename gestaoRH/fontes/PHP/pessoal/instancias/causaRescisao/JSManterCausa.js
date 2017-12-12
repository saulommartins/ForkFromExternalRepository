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
    * Data de Criação   : 03/05/2005


    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-13 07:32:53 -0300 (Sex, 13 Out 2006) $

    * Casos de uso :uc-04.04.10
*/

/*
$Log$
Revision 1.5  2006/10/13 10:31:58  souzadl
recodificação

Revision 1.4  2006/08/08 17:46:32  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    if(tipoBusca == 'alterarCaso'){
       selecionaTodosSelect(document.frm.inCodRegimeSelecionados);
    }
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirCaso(){
    selecionaTodosSelect(document.frm.inCodRegimeSelecionados);
    var mensagem = validaCaso();      
    if( mensagem == '' ){
        buscaValor('incluirCaso');
        //montaParametrosGET('incluirCaso');
    }else{
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function validaPercentual(campo,descricao){
    var mensagem = "";
    var d = document.frm;
    var valor  = campo.value;

    valor = parseInt(valor,10);

    if( valor > 100){
        mensagem += "@Campo " + descricao + " inválido!( " + valor + " )";
        campo.value = "";
        campo.focus();
    }

    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}


function validaCaso(){
   var mensagem = '';
   if(document.frm.elements['stDescricaoCaso'].value == ''){
      mensagem += "@Descricao do caso inválido ( )";
   }
   if(document.frm.elements['inCodTxtPeriodo'].value == ''){
      mensagem += "@Código do período inválido ( )";
   }
   if(document.frm.elements['inCodRegimeSelecionados'].value == ''){
      mensagem += "@Regime/SubDivisão inválido ( )";
   }   
   return mensagem;
}

function alteraDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
} 


function focusFiltro(){
    document.frm.inCodCausa.focus();
}

function Limpar(){
   buscaValor('limpaSessao');
}


function limpaCaso(){
    buscaValor('atualizaRegime');
    document.frm.reset();
     
}

function Cancelar(){
<?php
$arLink = Sessao::read('link');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

</script>
