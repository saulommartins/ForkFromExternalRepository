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
    * Javascript para Licenca
    * Data de Criação   : 02/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

	* $Id: JSManterLicenca.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
    document.frm.action = stAction;


    
}

function buscaProcesso(){
    document.frm.stCtrl.value = 'buscaProcesso';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function preencheProxCombo( inPosicao  ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function preencheCombosAtividade(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheCombos';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;

}

function incluirHorario() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";
    stCampoInicio  = document.frm.hrHorarioInicio;
    stCampoTermino = document.frm.hrHorarioTermino;
    if ( stCampoInicio.value == "" ) {
        erro = true;
        mensagem += "@Campo Horário de Início inválido!";
        if ( stCampoTermino.value == "") {
            mensagem += "@Campo Horário de Término inválido!";
        }
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else if ( stCampoTermino.value == "") {
        erro = true;
        mensagem += "@Campo Horário de Término inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else if ( stCampoTermino.value <= stCampoInicio.value ) {
        erro = true;
        mensagem += "@Horário de Término deve ser após o Horário de Início!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirHorario';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function recuperaHorario( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'recuperaHorario';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirHorario( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirHorario';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirAtividade() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";
    stCampoInscEcon  = document.frm.inInscricaoEconomica;
//    stCampoAtiv      = document.frm.stChaveAtividade;
    stCampoAtiv      = document.frm.cmbAtividade;
    if ( stCampoInscEcon.value == "" ) {
        erro = true;
        mensagem += "@Campo Inscrição Econômica inválido!";
        if ( stCampoAtiv.value == "" ) {
            mensagem += "@Campo Atividade inválido!";
        }
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else if ( stCampoAtiv.value == "" ) {
        erro = true;
        mensagem += "@Campo Atividade inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirAtividade';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function recuperaAtividades(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'recuperaAtividades';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirAtividade( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function validaDataLicenca() {
    var erro       = false;
    var mensagem   = "";
    stDataInicio  = document.frm.dtDataInicio.value;
    DiaInicio     = stDataInicio.substring(0,2);
    MesInicio     = stDataInicio.substring(3,5);
    AnoInicio     = stDataInicio.substr(6);
    
    stDataTermino = document.frm.dtDataTermino.value;
    DiaTermino    = stDataTermino.substring(0,2);
    MesTermino    = stDataTermino.substring(3,5);
    AnoTermino    = stDataTermino.substr(6);

    var dataInicio  = "";
    var dataTermino = "";
    dataInicio  += AnoInicio+MesInicio+DiaInicio;
    dataTermino += AnoTermino+MesTermino+DiaTermino;
    if ( dataTermino < dataInicio ) {
        erro = true;
        mensagem += "@Campo Data de Término deve ser posterior ao campo Data de Início!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }                                                        
                                                                    
}

function validaDataBaixa() {
    var erro       = false;
    var mensagem   = "";
    stDataConcessao  = document.frm.hdnDtDataConcessao.value;
    DiaConcessao = stDataConcessao.substring(0,2);
    MesConcessao = stDataConcessao.substring(3,5);
    AnoConcessao = stDataConcessao.substr(6);
    
    stDataBaixa      = document.frm.dtDataBaixa.value;
    DiaBaixa = stDataBaixa.substring(0,2);
    MesBaixa = stDataBaixa.substring(3,5);
    AnoBaixa = stDataBaixa.substr(6);

    var dataConcessao = "";
    dataConcessao += AnoConcessao+MesConcessao+DiaConcessao;
    var dataBaixa = "";
    dataBaixa += AnoBaixa+MesBaixa+DiaBaixa;
    
    if ( dataBaixa < dataConcessao ) {
        erro = true;
        mensagem += "@Campo Data da Baixa deve ser posterior a "+stDataConcessao+"!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function validaDataSuspensao() {
    var erro       = false;
    var mensagem   = "";
    stDataConcessao  = document.frm.hdnDtDataConcessao.value;
    DiaConcessao     = stDataConcessao.substring(0,2);
    MesConcessao     = stDataConcessao.substring(3,5);
    AnoConcessao     = stDataConcessao.substr(6);
    
    stDataSuspensao  = document.frm.dtDataSuspensao.value;
    DiaSuspensao     = stDataSuspensao.substring(0,2);
    MesSuspensao     = stDataSuspensao.substring(3,5);
    AnoSuspensao     = stDataSuspensao.substr(6);

    var dataConcessao = "";
    dataConcessao += AnoConcessao+MesConcessao+DiaConcessao;
    var dataSuspensao = "";
    dataSuspensao += AnoSuspensao+MesSuspensao+DiaSuspensao;
    
    if ( dataSuspensao < dataConcessao ) {
        erro = true;
        mensagem += "@Campo Data da Suspensão deve ser posterior a "+stDataConcessao+"!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}

function validaDataTerminoSuspensao() {
    var erro       = false;
    var mensagem   = "";
    stDataSuspensao  = document.frm.hdnDtDataSuspensao.value;
    DiaSuspensao     = stDataSuspensao.substring(0,2);
    MesSuspensao     = stDataSuspensao.substring(3,5);
    AnoSuspensao     = stDataSuspensao.substr(6);
                
    stDataTermino    = document.frm.dtDataTermino.value;
    DiaTermino       = stDataTermino.substring(0,2);
    MesTermino       = stDataTermino.substring(3,5);
    AnoTermino       = stDataTermino.substr(6);

    var dataSuspensao = "";
    dataSuspensao += AnoSuspensao+MesSuspensao+DiaSuspensao;
    var dataTermino = "";
    dataTermino += AnoTermino+MesTermino+DiaTermino;

        if ( dataSuspensao > dataTermino ) {
           erro = true;
           mensagem += "@Campo Data de Término deve ser posterior a "+stDataSuspensao+"!";
           alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
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
                                                                

function limparHorario(){
    document.frm.dia1.checked = false;
    document.frm.dia2.checked = false;
    document.frm.dia3.checked = false;
    document.frm.dia4.checked = false;
    document.frm.dia5.checked = false;
    document.frm.dia6.checked = false;
    document.frm.dia7.checked = false;
    document.frm.hrHorarioInicio.value = '';
    document.frm.hrHorarioTermino.value = '';
}

function limparAtividade(){
    document.frm.stChaveAtividade.value = '';
    preencheCombosAtividade();
}

function limparFormularioAtividade(){
    document.frm.reset();
    document.getElementById("spnListaAtividade").innerHTML = " ";
    buscaValor('limparSessaoAtividade');
    document.frm.stChaveAtividade.value = '';
    preencheCombosAtividade();
}

function limparFormularioEspecial(){
    document.frm.reset();
    document.getElementById("spnListaHorario").innerHTML = " ";
    document.getElementById("spnListaAtividade").innerHTML = " ";
    buscaValor('limparSessaoEspecial');
    document.frm.stChaveAtividade.value = '';
    preencheCombosAtividade();
}

function Limpar(){
    document.frm.reset();
    document.getElementById("campoInner").innerHTML = " ";
    document.getElementById("spnListaHorario").innerHTML = " ";
    document.getElementById("spnListaAtividade").innerHTML = " ";
}

function Cancelar () {
<?php
    $link = Sessao::read( "link" );
     $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function filtrar(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}
</script>
