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
/*
    * Arquivo com funcoes JavaScript para Relatorio Inscricao Divida Ativa
    * Data de Criação: 12/09/2014
    * @author Analista: Luciana Dellay
    * @author Desenvolvedor: Evandro Melos

    * $Id: JSRelatorioInscricaoDividaAtiva.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

?>

<script type="text/javascript">

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

function Cancelar () {
    document.frm.target = "";
    document.frm.action = "<?=$pgFilt.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function submeteFiltro(){
    if( Valida() ){

        inCodGrupo = document.frm.inCodGrupo.value;
        inCodCredito = document.frm.inCodCredito.value;

        inCGM = document.frm.inCGM.value;

        inNumInscInicial = document.frm.inNumInscricaoEconomicaInicial.value;
        inNumInscFinal = document.frm.inNumInscricaoEconomicaFinal.value;

        inNumInscMunicipalInicial = document.frm.inCodImovelInicial.value;
        inNumInscMunicipalFinal = document.frm.inCodImovelFinal.value;

        dtDataPreenchida = document.frm.dtInscricao.value;
        dtDataHoje = document.frm.dtHoje.value;

        if ( ( !inCodGrupo && !inCodCredito ) ) {
            mensagem = "Campos 'Grupo de Crédito' ou 'Crédito' não foram preenchidos!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else if ( !inCGM && !inNumInscInicial && !inNumInscMunicipalInicial ) {
            mensagem = "Campos 'CGM', 'Inscrição Municipal' ou 'Inscrição Econômica' não foram preenchidos!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else if( validaData( dtDataPreenchida, dtDataHoje ) ) {
            mensagem = "Campos 'Data de Inscrição' deve ser igual ou anterior à da ta de hoje!";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else {
            document.frm.submit();
        }
    }
}

function validaData( CampoData, DataHoje ) {

    dtDataCampo = CampoData;
    DiaData     = dtDataCampo.substring(0,2);
    MesData     = dtDataCampo.substring(3,5);
    AnoData     = dtDataCampo.substr(6);

    dtDataHoje = DataHoje;
    DiaHoje     = dtDataHoje.substring(0,2);
    MesHoje     = dtDataHoje.substring(3,5);
    AnoHoje     = dtDataHoje.substring(6);

    var dataCampoInvert = AnoData+MesData+DiaData;
    var dataHojeInvert = AnoHoje+MesHoje+DiaHoje;

    if( dataCampoInvert > dataHojeInvert ){
        CampoData.value = "";

        document.frm.dtInscricao.focus();

        erro = true;
        var mensagem = "";
        mensagem += "@Campo Data deve ser anterior ou igual à data de hoje!";

        setTimeout ( "document.getElementById('dtInscricao').focus();", 200 );
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
        
    }

}

function submeteInscricaoEmDivida () {

    inCodAutoridade = document.frm.inCodAutoridade.value;

    if ( inCodAutoridade ) {
        document.frm.stAcao.value = "incluir";
        document.frm.submit();
        document.frm.stAcao.value = "inscrever";
    } else {
        erro = true;
        var mensagem = "";
        mensagem += "@Campo Autoridade deve ser preenchido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }

}

function Limpar(){
    document.frm.reset();
    //BloqueiaFrames(true,false); 
    buscaValor('montaListaIncluir');
}

function validarListar(){
    var cont = 0;
    var selecionado = 0;

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( (document.frm.elements[cont].type == 'checkbox') && ( namee.match('boIncluir_')) ){
            if ( document.frm.elements[cont].checked ) {
                selecionado = 1;
                break;
            }
        }

        cont++;
    }

    if ( !selecionado && ( document.frm.inTotalRegistros.value <= 5000 ) ) {
        alertaAviso("Erro! Nenhum registro de parcela foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        if( Valida() ){
            document.frm.stCtrl.value = "PRManterInscricao.php";
            document.frm.action = '<?=$pgProc.'?'.Sessao::getId().$stLink;?>';
            document.frm.submit();
        }
    }
}

</script>
