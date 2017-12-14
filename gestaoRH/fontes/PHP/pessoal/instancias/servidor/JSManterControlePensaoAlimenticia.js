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
* Javacript de Controle de Pensão Alimenticia
* Data de Criação: 03/04/2006


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @package URBEM
* @subpackage

* Casos de uso: uc-04.04.45
*/

/*
$Log$
Revision 1.16  2006/08/08 17:48:32  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">


function buscaValor(tipoBusca){

     var acao = document.frm.action;
     var target = document.frm.target;   

     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.target = 'oculto';
     document.frm.submit();

     document.frm.target = target;
     document.frm.action = acao;

}

function incluir(){
     var acao = document.frm.action;
     
     if ( Valida() ){
          document.frm.stCtrl.value = 'incluir';
          document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
          document.frm.target = 'oculto';
          document.frm.submit();
          document.frm.action = acao;
     }       
}

function filtrar(){

   document.frm.action = '<?=$pgForm;?>?<?=Sessao::getId();?>';
   document.frm.submit();

}



function validaDataLimite(){

 if ( document.frm.dtDataLimite.value.length != 0 ){
    if ( document.frm.dtDataInclusao.value.length != 0 ){

        var dtInclusao = document.frm.dtDataInclusao.value;
        var dtLimite   = document.frm.dtDataLimite.value;

        dtInclusao = dtInclusao.split("/");
        dtInclusao = dtInclusao[2] + dtInclusao[1] + dtInclusao[0];

        dtLimite = dtLimite.split("/");
        dtLimite = dtLimite[2] + dtLimite[1] + dtLimite [0];

        if (dtLimite  <= dtInclusao ){
            document.frm.dtDataLimite.value = "";
            mensagem = 'O valor da data de limite deve ser posterior a data de inclusao!';
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        }
    }
 }

}

function alteraDado(stControle, inId){
     var acao = document.frm.action;
     var target = document.frm.target;   

     document.frm.stCtrl.value = stControle;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
     document.frm.target = 'oculto';
     document.frm.submit();
     document.frm.target = target;
     document.frm.action = acao;
}

function validaPercentual(campo,descricao){
    var mensagem = "";
    var d = document.frm;
    var valor  = campo.value;

    valor = parseInt(valor,10);

    if( (valor > 100) || ( valor < 0) ) {
        mensagem += "@Campo " + descricao + " inválido!( " + valor + " )";
        campo.value = "";
        campo.focus();
    }

    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function preencherEvento(codigo,natureza){
     var acao = document.frm.action;
     var target = document.frm.target;

     document.frm.stCtrl.value = 'preencherEvento';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodTipo='+codigo+'&stNatureza='+natureza;
     document.frm.target = 'oculto';

     document.frm.submit();
     document.frm.target = target;
     document.frm.action = acao;
}

function ok(){


     var acao = document.frm.action;
     var target = document.frm.target;   

     document.frm.stCtrl.value = 'ok';
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
     document.frm.target = 'oculto';
     document.frm.submit();

     document.frm.target = target;
     document.frm.action = acao;

}
function abreAbaDependentes(cgm,  codContrato, codServidor ) {
     document.frm.action = 'FMManterServidor.php?<?=Sessao::getId();?>&stAcao=alterar&inAba=5&inCodContrato='+codContrato+'&inCodServidor='+codServidor+'&inNumCGM='+cgm+'&actVoltar=FMManterControlePensaoAlimenticia.php' ;
     document.frm.submit();
}




</script>
