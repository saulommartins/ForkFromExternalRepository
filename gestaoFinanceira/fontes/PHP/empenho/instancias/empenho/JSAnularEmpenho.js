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
    * Arquivo JavaScript
    * Data de Criação   : 06/12/2004

    
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2008-01-02 13:26:34 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.15
*/

/*
$Log$
Revision 1.6  2006/08/01 17:34:18  jose.eduardo
Bug #6706#

Revision 1.5  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    //document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
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

function limparFiltro(){
    document.frm.reset();
    passaItem('document.frm.inCodEntidade','document.frm.inCodEntidadeDisponivel','tudo');
}

function validaDataAnulacao() {
    var erro       = false;
    var mensagem   = "";
	var exercicioSessao = <?=Sessao::getExercicio()?>;
	var dataAtual = '<?=date("Ymd")?>';
	var dia = '<?=date("d")?>';
	var mes = '<?=date("m")?>';
	var ano = '<?=date("Y")?>';
	var dataMaxima = '<?=Sessao::getExercicio()?>' + '1231';

    if(document.frm.stDtAnulacao.value != "") {

		/*
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());
                                
        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;
		*/

        stDataEmpenho = document.frm.stDtEmpenho.value;
        DiaEmpenho = stDataEmpenho.substr(0,2);
        MesEmpenho = stDataEmpenho.substr(3,2);
        AnoEmpenho = stDataEmpenho.substr(6,4);

        var dataEmpenho = AnoEmpenho+""+MesEmpenho+""+DiaEmpenho;

        stDataAnulacao = document.frm.stDtAnulacao.value;
        DiaAnulacao = stDataAnulacao.substr(0,2);
        MesAnulacao = stDataAnulacao.substr(3,2);
        AnoAnulacao = stDataAnulacao.substr(6,4);

        var dataAnulacao = AnoAnulacao+""+MesAnulacao+""+DiaAnulacao;

        /*
		var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = ano+"0101";
		*/

		var dataPrimeiro = exercicioSessao + "0101";

        if ( dataAnulacao < dataPrimeiro ) {
            erro = true;
            // mensagem += "@Campo Data de Anulação deve ser maior que data '01/01/"+ano+"'!";
			mensagem += "@Campo Data de Anulação deve ser maior que data '01/01/" + exercicioSessao + "'!";
        }

		if (!erro && (dataAnulacao > dataMaxima)) {
            erro = true;
            mensagem += "@Campo Data de Anulação deve ser menor ou igual a data final do exercício (31/12/<?=Sessao::getExercicio()?>)!";
		}

   //      if (!erro && (dataAnulacao > dataAtual) ) {
   //          erro = true;
   //          // mensagem += "@Campo Data de Anulação deve ser menor ou igual que a data atual ("+dia+"/"+mes+"/"+ano+")!";
			// mensagem += "@Campo Data de Anulação deve ser menor ou igual que a data atual (" + dia + "/" + mes + "/" + ano + ")!";
   //      }

        if (!erro && (dataAnulacao < dataEmpenho) ) {
            erro = true;
            mensagem += "@Campo Data de Anulação deve ser maior ou igual que a data de empenho (" + DiaEmpenho + "/" + MesEmpenho + "/" + AnoEmpenho + ")!";
        }

        if(mensagem != ""){
			// document.frm.stDtEmpenho.value= dia +"/"+ mes + "/" + ano;
            document.frm.stDtAnulacao.value= '';
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else{
            buscaDado ('verificaDataAnulacaoEmpenho');
        }

    }
}


</script>
                
