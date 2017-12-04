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
    * Pagina de Formulario de Inclusao/Alteracao de ESPECIES

    * Data de Criacao   : 08/12/2005


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva 
    * @ignore

    * $Id: JSManterAcrescimo.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function Cancelar(){
<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function validaData1500( CampoData ) {
    dtDataCampo = CampoData.value;
    DiaData  = dtDataCampo.substring(0,2);
    MesData  = dtDataCampo.substring(3,5);
    AnoData  = dtDataCampo.substr(6);

    var dataValidar = 15000422;
    var dataCampoInvert = AnoData+MesData+DiaData;

    if( dataCampoInvert < dataValidar ){
        CampoData.value = "";
        erro = true;
        var mensagem = "";
        mensagem += "@Campo Data deve ser posterior a 21/04/1500!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function validaDataLimite( CampoData, DataLimite ) {
    dtDataCampo = CampoData.value;
    DiaData  = dtDataCampo.substring(0,2);
    MesData  = dtDataCampo.substring(3,5);
    AnoData  = dtDataCampo.substr(6);

    dtDataMinima = DataLimite.value;
    DiaDataL  = dtDataCampo.substring(0,2);
    MesDataL  = dtDataCampo.substring(3,5);
    AnoDataL  = dtDataCampo.substr(6);

    var dataCampoInvert = AnoData+MesData+DiaData;
    var dataLimite = AnoDataL+MesDataL+DiaDataL;

    if( dataCampoInvert < dataLimite ){
        CampoData.value = "";
        erro = true;
        var mensagem = "";
        mensagem += "@Campo Data deve ser posterior a ";
        mensagem += DiaDataL+"/"+MesDataL+"/"+AnoDataL;
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function alterarValor( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.dtVigenciaValor.value = inIndice1;
    document.frm.flValorAcrescimo.value = inIndice2;
    document.frm.stCtrl.value = 'alterarValor';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirValor( inIndice1, inIndice2 ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirValor';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice1='+inIndice1+'&inIndice2='+inIndice2;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function definirValor() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;

    if ( document.frm.dtVigenciaValor.value == "" ) {
        alertaAviso("@Campo 'Data de Vigência' inválido!",'form','erro','<?=Sessao::getId();?>', '../');
    }else
    if ( document.frm.flValorAcrescimo.value == "" ) {
        alertaAviso("@Campo 'Valor' inválido!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        document.frm.stCtrl.value = 'definirValor';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

</SCRIPT>
