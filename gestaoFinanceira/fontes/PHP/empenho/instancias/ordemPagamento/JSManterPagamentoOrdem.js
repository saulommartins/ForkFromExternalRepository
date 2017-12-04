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
    * Arquivo JavaScript - ManterPagamentoOrdem
    * Data de Criação   : 28/03/2005
    
    
    * @author Analista: Diego B. Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza
    
    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-06 08:36:39 -0200 (Ter, 06 Fev 2007) $
    
    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.5  2007/02/06 10:36:39  rodrigo_sr
Bug #7865#

Revision 1.4  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">
function recuperaItem(){
    document.frm.stCtrl.value = 'montaLista';
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function buscaFornecedor(){
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = 'buscaFornecedorDiverso';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function validaData( data ){
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = "validaData";
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?stDataValida='+data+'&<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function validaDataPagamento() {
    var erro       = false;
    var mensagem   = "";

    if(document.frm.stDtPagamento.value != ""){
        hoje = new Date();
        dia = parseInt(hoje.getDate());
        mes = parseInt(hoje.getMonth())+1;
        ano = parseInt(hoje.getFullYear());

        if(dia<10) dia = "0"+dia;
        if(mes<10) mes = "0"+mes;

        stDataOP = document.frm.stDataOrdem.value;
        DiaOP = stDataOP.substring(0,2);
        MesOP = stDataOP.substring(3,5);
        AnoOP = stDataOP.substr(6);

        var dataOP = AnoOP+""+MesOP+""+DiaOP;

        stDataPagamento = document.frm.stDtPagamento.value;
        DiaPagamento = stDataPagamento.substring(0,2);
        MesPagamento = stDataPagamento.substring(3,5);
        AnoPagamento = stDataPagamento.substr(6);

        var dataPagamento = AnoPagamento+""+MesPagamento+""+DiaPagamento;

        var dataAtual = ano+""+mes+""+dia;
        var dataPrimeiro = ano+"0101";

        if ( dataPagamento < dataPrimeiro) {
            erro = true;
            mensagem += "@Campo Data de Pagamento menor que '01/01/"+ano+"'!";
        }

        if ( dataPagamento > dataAtual ) {
            erro = true;
            mensagem += "@Campo Data de Pagamento maior que data atual ("+dia+"/"+mes+"/"+ano+")!";
        }

        if ( dataOP > dataPagamento ) {
            erro = true;
            mensagem += "@Campo Data de Pagamento deve ser maior que data da OP ("+DiaOP+"/"+MesOP+"/"+AnoOP+")!";
        }

        if(mensagem != ""){
            document.frm.stDtPagamento.value= '';
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }else{
            buscaDado ('verificaDataOP');
        }
    }
}


</script>
