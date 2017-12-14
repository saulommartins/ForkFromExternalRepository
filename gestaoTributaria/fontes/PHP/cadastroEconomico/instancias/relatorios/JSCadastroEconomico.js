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
    * Página de JavaScript para Consulta de Cadastro Economico
    * Data de Criação: 16/09/2005


    * @author Diego Bueno Coelho

    * @ignore

	* $Id: JSCadastroEconomico.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.17

*/

/*
$Log$
Revision 1.5  2007/02/27 11:48:54  rodrigo
Bug #8042#

Revision 1.4  2007/01/11 10:23:15  dibueno
Bug #8042#

Revision 1.3  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">
function inscricao(){
   if(document.forms[0]){
      document.forms[0].Ok.onclick=function(){ if(Valida()){        
                                                    var obj  = new Object(document.forms[0].inNumInscricaoEconomicaInicial);
                                                    var flag = new Boolean(true);
                                                    if(obj.value.substring(0)==0 & obj.value!=""){
                                                        obj.focus();
                                                        var mensagem="Inscrição Econômica não pode ser zero.";
                                                        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
                                                        flag=false;
                                                    }
                                                    if(flag){
                                                        document.forms[0].submit();
                                                    }
                                               }
                                             };
   }
}

function validaInscricao(){
    if(typeof(this.addEventListener)!="undefined"){
        this.addEventListener('load',inscricao(),'false');
    }
}

validaInscricao();

function buscaValor( tipoBusca ){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    var caminho = '<?=$pgRel;?>';
    document.frm.action = '<?=$pgRel;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
}

function Limpar(){
 /*   document.getElementById("stNomeNatureza").innerHTML = "&nbsp;";
    document.getElementById("stNomeSocio").innerHTML    = "&nbsp;";
    document.getElementById("stEndereco").innerHTML     = "&nbsp;";
*/
}


function AtualizaTipoRelatorio (  ){
        if ( document.frm.stTipoRelatorio.value ) {
            document.frm.stTipoRelatorioSubmit.value = document.frm.stTipoRelatorio.value;
            document.frm.submit();
            document.frm.stTipoRelatorioSubmit.value = '';
        } else {
            mensagem = "Selecione o tipo de relatório.";
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
}


function submeteFiltro(){
    //if( Valida() ){
        stTipoRelatorio = document.frm.stTipoRelatorio.value;
        
        if ( stTipoRelatorio == "analitico" ){
            inInscricaoEconomica = document.frm.inInscricaoEconomica.value;
            dtDataInicio = document.frm.dtInicio.value;
            stTipoEmpresa = document.frm.stTipoEmpresa.value;
            stSituacao = document.frm.stSituacao.value;
            //inCodSocio = document.frm.inCodigoSocio.value;
            stTipoRelatorioSubmit = document.frm.stTipoRelatorio.value;


            if ( !inInscricaoEconomica && !dtDataInicio && !stTipoEmpresa && !stSituacao && !inCodSocio) {
                mensagem = "Campos 'inscrição econômica', 'data de início', 'tipo de empresa' ou 'situação' não foram preenchidos!";
                alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
            }else
                document.frm.submit();
        } else {
            document.frm.submit();
        }
    //}
}

</script>
