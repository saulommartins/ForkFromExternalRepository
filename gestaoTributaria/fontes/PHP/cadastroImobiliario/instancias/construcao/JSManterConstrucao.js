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
    * Página de funções javascript para o cadastro de construção
    * Data de Criação   : 22/03/2005


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Bowzzio Paulino

    * @ignore

    * $Id: JSManterConstrucao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.12
*/

/*
$Log$
Revision 1.4  2006/09/18 10:30:16  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function focusIncluir(){
    document.frm.inProcesso.focus();
}

function visualizarProcesso(processo, timestamp, cod_construcao, ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'visualizarProcesso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_processo='+processo+'&timestamp='+timestamp+'&cod_construcao='+cod_construcao+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


function habilitaSpnImovelCond(){
    document.frm.stCtrl.value = 'habilitaSpnImovelCond';
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function Limpar(){
    document.frm.reset();
    document.getElementById('stNomeResponsavel').innerHTML = '&nbsp;';
    document.getElementById('campoInner').innerHTML = '&nbsp;';
}

function Cancelar () {
<?php
     $stLink = Sessao::read('stLink');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function validaDataConstrucao() {
    dtDataConstrucao = document.frm.stDtConstrucao.value;
    DiaData  = dtDataConstrucao.substring(0,2);
    MesData  = dtDataConstrucao.substring(3,5);
    AnoData  = dtDataConstrucao.substr(6);

    var dataValidar = 15000422;
    var dataConstrInvert = AnoData+MesData+DiaData;

    if( dataConstrInvert < dataValidar ){
        document.frm.stDtConstrucao.value = "";
        erro = true;
        mensagem = "@Campo Data de Construção deve ser posterior a 21/04/1500!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}
</script>
