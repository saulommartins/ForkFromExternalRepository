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
    * Arquivo JS 
    * Data de Criação   : 06/12/2004


    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name:  $
    $Autor:$
    $Date: 2008-01-07 11:40:55 -0200 (Seg, 07 Jan 2008) $ 
    
    * Casos de uso: uc-02.03.04, uc-02.03.05
*/

/*
$Log: JSManterLiquidacao.js,v $
Revision 1.13  2007/04/09 13:39:38  cako
Bug #8864#

Revision 1.12  2007/03/14 20:51:44  cako
Bug #8117#

Revision 1.11  2007/03/13 15:09:04  cako
Bug #8117#

Revision 1.10  2007/02/26 23:26:42  cleisson
Bug #8117#

Revision 1.9  2007/02/05 18:44:54  rodrigo_sr
Bug #7858#

Revision 1.8  2007/01/03 12:32:31  bruce
Bug #7860#

Revision 1.7  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = 'telaListaItemPreEmpenho';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;

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

function validaDataLiquidacao() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtLiquidacao.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());
	exercicio = "<?=Sessao::getExercicio();?>";

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substring(0,2);
        MesEmpenho = stDataEmpenho.substring(3,5);
        AnoEmpenho = stDataEmpenho.substr(6);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        stDataLiquidacao = document.frm.stDtLiquidacao.value;
        DiaLiquidacao = stDataLiquidacao.substring(0,2);
        MesLiquidacao = stDataLiquidacao.substring(3,5);
        AnoLiquidacao = stDataLiquidacao.substr(6);

        var dataLiquidacao = AnoLiquidacao+""+MesLiquidacao+""+DiaLiquidacao;
        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = exercicio+"0101";

        if ( dataLiquidacao < dataPrimeiro) {
            erro = true;
            mensagem += "@Campo Data de Liquidação deve ser maior que '01/01/"+exercicio+"'!";
        }

        if ( dataLiquidacao > dataAtual ) {
            erro = true;
            mensagem += "@Campo Data de Liquidação maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        }

        if ( dataEmpenho > dataLiquidacao ) {
            erro = true;
            mensagem += "@Campo Data de Liquidação deve ser maior que data do empenho ("+DiaEmpenho+"/"+MesEmpenho+"/"+AnoEmpenho+")!";
        }
        
        var anoExercicio = "<?=Sessao::getExercicio();?>"+31+""+12;
        
        if ( dataLiquidacao > anoExercicio ) {
            erro = true;
            mensagem += "@Campo Data de Liquidação deve ser menor que '31/12/"+<?=Sessao::getExercicio();?>+"'";
        }
        
    }

    if(mensagem != ""){
        //document.frm.stDtLiquidacao.value= dia +"/"+ mes + "/" + ano;
        document.frm.stDtLiquidacao.value = stDataLiquidacao;
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        buscaDado('verificaDataLiquidacao');
    }
      else{
        buscaDado('verificaDataLiquidacao');
    }
}

function validaDataVencimento(){
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtLiquidacao.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substring(0,2);
        MesEmpenho = stDataEmpenho.substring(3,5);
        AnoEmpenho = stDataEmpenho.substr(6);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        stDataLiquidacao = document.frm.stDtLiquidacao.value;
        DiaLiquidacao = stDataLiquidacao.substring(0,2);
        MesLiquidacao = stDataLiquidacao.substring(3,5);
        AnoLiquidacao = stDataLiquidacao.substr(6);

        var dataLiquidacao = AnoLiquidacao+""+MesLiquidacao+""+DiaLiquidacao;
        
        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = exercicio+"0101";

        stDataValidadeFinal = document.frm.dtValidadeFinal.value;
        DiaValidade = stDataValidadeFinal.substring(0,2);
        MesValidade = stDataValidadeFinal.substring(3,5);
        AnoValidade = stDataValidadeFinal.substr(6);

        var dataValidade = AnoValidade+""+MesValidade+""+DiaValidade;

        if (document.frm.dtValidadeFinal.value != ''){
            if ( dataValidade < dataPrimeiro) {
                erro = true;
                mensagem += "@Campo Data de Vencimento deve ser maior que '01/01/"+ano+"'!";
            }
        
            if ( dataLiquidacao > dataValidade ) {
                erro = true;
                mensagem += "@Campo Data de Vencimento deve ser maior ou igual ao campo Data de Liquidação.";
            }
        }

        if(mensagem != ""){
            document.frm.dtValidadeFinal.value = stDataLiquidacao;
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
    }
}

function mudaMenu(titulo, func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho&cod_func_pass="+func+"&stTitulo="+titulo;
    parent.frames["telaMenu"].location.replace(sPag);
}

function contaCredDeb() {
	var cDeb = document.frm.inCodContaDebito.value.length;
	var cCred = document.frm.inCodContaCredito.value.length;
	var cHist = document.frm.inCodHistoricoPatrimon.value.length;
	mensagem = "";
	
	var ret = true;
	if ( (cCred == 0) && (cDeb > 0) ) {
		mensagem = "@Ao informar Conta Débito, a Conta Crédito também deve ser informada.";
		var campoFoco = document.frm.inCodContaCredito;
		ret = false;
	} else if ( (cCred > 0) && (cDeb == 0) ) {
		mensagem = "@Ao informar Conta Crédito, a Conta Débito também deve ser informada.";
		var campoFoco = document.frm.inCodContaDebito;		
		ret = false;
	}
	
	if (cCred > 0 || cDeb > 0) {
		if (cHist == 0) {
			mensagem += "@Ao informar contas, o Histórico Patrimonial é obrigatório.";
			var campoFoco = document.frm.inCodHistoricoPatrimon;
			ret = false;
		}
	}
	
	if (!ret) {
		alertaAviso(mensagem, "form", "erro","<?=Sessao::getId();?>", "../");
		try {
			campoFoco.focus();
		} catch(e) {}
	}
	return ret;
}

function confirmaLiquidacao () {
	var ret = true;
	var cDeb = document.frm.inCodContaDebito.value.length;
	var cCred = document.frm.inCodContaCredito.value.length;

	if (cDeb == 0 && cCred == 0) {
		ret = confirm("Liquidar Empenho sem realizar Incorporação Patrimonial?");
	}
	if (!ret) {
		document.frm.inCodContaDebito.focus();
	}
	return ret;
}

function verificaDebitosContribuinte() {
    var ret = true
    if (jq('#boExisteDebitoContribuinte').val()){
        ret = confirm("Existem débitos para este contribuinte. Deseja continuar?");        
    } else {
        ret = true;
    }
	return ret;
}

function Salvar(){
    BloqueiaFrames(true,false);
    if (jq('#boCriaContasDebCred').val()){
        if( contaCredDeb() && Valida()){ 
            if (verificaDebitosContribuinte()){
                if ( confirmaLiquidacao() ) {
                    document.frm.submit(); 
                }
            }
        }
    } else {
        if (verificaDebitosContribuinte()){
                document.frm.submit(); 
        }
    }
}

function validaDataEmissaoNF() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtEmissaoNF.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());
	exercicio = "<?=Sessao::getExercicio();?>";

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substring(0,2);
        MesEmpenho = stDataEmpenho.substring(3,5);
        AnoEmpenho = stDataEmpenho.substr(6);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        stDtEmissaoNF = document.frm.stDtEmissaoNF.value;
        DiaEmissao = stDtEmissaoNF.substring(0,2);
        MesEmissao = stDtEmissaoNF.substring(3,5);
        AnoEmissao = stDtEmissaoNF.substr(6);

        var dataEmissao = AnoEmissao+""+MesEmissao+""+DiaEmissao;
        
        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = exercicio+"0101";


        if ( dataEmissao < dataPrimeiro) {
            erro = true;
            mensagem += "@Campo Data de Emissão da NF deve ser maior que '01/01/"+exercicio+"'!";
        }

        if ( dataEmissao > dataAtual ) {
            erro = true;
            mensagem += "@Campo Data de Emissão da NF maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        }

        if ( dataEmpenho > dataEmissao ) {
            erro = true;
            mensagem += "@Campo Data de Emissão da NF deve ser maior que data do empenho ("+DiaEmpenho+"/"+MesEmpenho+"/"+AnoEmpenho+")!";
        }
        
        var anoExercicio = "<?=Sessao::getExercicio();?>"+31+""+12;
        
        if ( dataEmissao > anoExercicio ) {
            erro = true;
            mensagem += "@Campo Data de Emissão da NF deve ser menor que '31/12/"+<?=Sessao::getExercicio();?>+"'";
        }
        
    }

    if(mensagem != ""){
        //document.frm.stDtLiquidacao.value= dia +"/"+ mes + "/" + ano;
        alertaAvisoTelaPrincipal(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        document.frm.stDtEmissaoNF.value = '';
        document.frm.stDtEmissaoNF.focus();
        // buscaDado('verificaDataLiquidacao');
    }
    //   else{
    //     buscaDado('verificaDataLiquidacao');
    // }
}

</script>
