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
    * Arquivo JS utilizado na anulação de liquidação
    * Data de Criação   : 06/12/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2008-01-07 11:40:55 -0200 (Seg, 07 Jan 2008) $ 
    
    * Casos de uso: uc-02.03.04
                    uc-02.03.18
*/

/*
$Log$
Revision 1.6  2006/07/25 14:39:24  cako
Bug #6606#

Revision 1.5  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirItem() {
    var mensagem;
    if(!document.frm.stNomItem.value)
        mensagem += '@Campo Descrição do Item inválido!()';
    if(!document.frm.stComplemento.value)
        mensagem += '@Campo Complemento inválido!()';
    if(!document.frm.nuQuantidade.value)
        mensagem += '@Campo Quantidade inválido!()';
    if(!document.frm.inCodUnidade.value)
        mensagem += '@Campo Unidade inválido!()';
    if(!document.frm.nuVlUnitario.value)
        mensagem += '@Campo Valor Unitário inválido!()';
    if(!document.frm.nuVlTotal.value)
        mensagem += '@Campo Valor Total inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiItemPreEmpenho';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparItem();
    }
    
}

function excluirItem(stControle, inNumItem ){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumItem=' + inNumItem;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function limparItem() {
    document.frm.stNomItem.value = '';
    document.frm.stComplemento.value = '';
    document.frm.nuQuantidade.value = '';
    document.frm.inCodUnidade.value = '';
    document.frm.nuVlUnitario.value = '';
    document.frm.nuVlTotal.value = '';
}

function gerarValorTotal() {
    var nuVlUnidade = document.frm.nuVlUnitario.value;
    var nuQuantidade = document.frm.nuQuantidade.value;
    var nuVlTotal;
    if( nuVlUnidade && nuQuantidade ) {
        nuVlUnidade = nuVlUnidade.replace( new  RegExp("[.]","g") ,'');
        nuVlUnidade = nuVlUnidade.replace( "," ,'.');
        nuVlTotal = nuVlUnidade * nuQuantidade;
        document.frm.nuVlTotal.value = nuVlTotal;
    }
}

function validaDataEstorno() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtEstorno.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEstorno = document.frm.stDtEstorno.value;
        DiaEstorno = stDataEstorno.substring(0,2);
        MesEstorno = stDataEstorno.substring(3,5);
        AnoEstorno = stDataEstorno.substr(6);

        var dataEstorno = AnoEstorno+""+MesEstorno+""+DiaEstorno;

        stDataLiquidacao = document.frm.stDtLiquidacao.value;
        DiaLiquidacao = stDataLiquidacao.substring(0,2);
        MesLiquidacao = stDataLiquidacao.substring(3,5);
        AnoLiquidacao = stDataLiquidacao.substr(6);

        var dataLiquidacao = AnoLiquidacao+""+MesLiquidacao+""+DiaLiquidacao;

        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = ano+"0101";

        var stExercicioLimite = jq('#stExercicioLimite').val();
        if (stExercicioLimite != ''){
            var dataExercicioLimite = stExercicioLimite+"1231";

            if (dataEstorno > dataExercicioLimite){    
                erro = true;
                mensagem += "@Campo Data de Anulação deve ser menor ou igual a '31/12/"+stExercicioLimite+"'!";
            }
        } else if ( dataEstorno < dataPrimeiro ) {
            erro = true;
            mensagem += "@Campo Data de Anulação de Liquidação menor que data '01/01/"+ano+"'!";
        }

        var stDataAnulacao = jq('#stDtAnulacaoOP').val();
        if (stDataAnulacao != ''){
            var stDataUltimaAnulacao = jq('#stDtUltimaAnulacao').val();
            DiaUltimaAnulacao = stDataUltimaAnulacao.substring(0,2);
            MesUltimaAnulacao = stDataUltimaAnulacao.substring(3,5);
            AnoUltimaAnulacao = stDataUltimaAnulacao.substr(6);
            var dataUltimaAnulacao = AnoUltimaAnulacao+""+MesUltimaAnulacao+""+DiaUltimaAnulacao;

            DiaAnulacao = stDataAnulacao.substring(0,2);
            MesAnulacao = stDataAnulacao.substring(3,5);
            AnoAnulacao = stDataAnulacao.substr(6);
            var dataAnulacao = AnoAnulacao+""+MesAnulacao+""+DiaAnulacao;

            if (dataAnulacao > dataUltimaAnulacao){    
                if (dataEstorno < dataAnulacao){
                    erro = true;
                    mensagem += "@Campo Data de Anulação menor que a data do Estorno de Pagamento! ("+stDataAnulacao+")";
                }
            }
        }


        // if ( dataEstorno > dataAtual ) {
        //     erro = true;
        //     mensagem += "@Campo Data de Anulação de Liquidação maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        // }

        if ( dataEstorno < dataLiquidacao ) {
            erro = true;
            mensagem += "@Campo Data de Anulação de Liquidação deve ser maior que Data de Liquidação ("+DiaLiquidacao+"/"+MesLiquidacao+"/"+AnoLiquidacao+")!";
        }

        if(mensagem != ""){
            if (stExercicioLimite) {
                document.frm.stDtEstorno.value= "31/12/"+stExercicioLimite;
            } else if (stDataAnulacao != ''){
                document.frm.stDtEstorno.value= stDataAnulacao;
                //document.frm.stDtEstorno.value= stDataEstorno;
            } else {
                document.frm.stDtEstorno.value= dia +"/"+ mes + "/" + ano;
            }
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else{
            buscaDado ('verificaDataLiquidacaoAnulada');
        }
    }
}

function Cancelar(){
<?php
Sessao::write('pg', '');
Sessao::write('pos', '');
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}


</script>
                
