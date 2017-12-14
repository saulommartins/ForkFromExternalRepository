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
    * Include JavaScript para Baixa de débitos
    * Data de Criação   : 19/05/2005


    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @supackage Regras
    * @package Urbem

    * $Id: JSManterBaixaManual.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.13  2007/07/16 21:10:27  cercato
Bug #9668#

Revision 1.12  2007/02/07 17:42:29  rodrigo
#8318#

Revision 1.11  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function SalvarFormulario(){
    var mensagem = "";
    var erro;
    
    if( Valida() ){

        if( document.frm.stCreditosRef.value == 'da' ){
           if( document.frm.inNrParcelamento.value == '' ){
                erro = true;
                mensagem += "@Campo Cobrança/Ano inválido!()";
            }
        }

        if( document.frm.stCreditosRef.value == 'cgm' ){
            if( document.frm.inCodContribuinte.value == '' ){
                erro = true;
                mensagem += "@Campo Contribuinte inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }
        if( document.frm.stCreditosRef.value == 'ii' ){
            if( document.frm.inInscricaoImobiliaria.value == '' ){
                erro = true;
                mensagem += "@Campo Inscrição Imobiliária inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }
        if( document.frm.stCreditosRef.value == 'ie' ){
            if( document.frm.inInscricaoEconomica.value == '' ){
                erro = true;
                mensagem += "@Campo Inscrição Econômica inválido!()";
            }else
                if (document.frm.inExercicio.value == '' ){
                    erro = true;
                    mensagem += "@Campo Exercício inválido!()";
                }
        }

        if( erro ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        } else {
            document.frm.submit();
        }
    }
}
/*
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    //document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
*/
function buscaValor(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}
function carregaReferencia(stReferencia){
    stPag = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stReferencia='+stReferencia+'';
    ajax(stPag,'referencia','spnReferencia');
}

function preencheAgencia(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheAgencia';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function habilitaBanco(){
    var status;

    status = document.frm.inTodosBancos.checked;

    document.frm.inNumbanco.disabled = status;
    document.frm.cmbBanco.disabled = status;
    document.frm.inNumAgencia.disabled = status;
    document.frm.cmbAgencia.disabled = status;
}

function FechamentoBaixaManual() {
    var status = 1;
    if (!document.frm.inTodosBancos.checked) {
        if (!document.frm.inNumAgencia.value) {
            alertaAviso("@Campo Agência inválido!",'form','erro','<?=Sessao::getId();?>', '../');
            status = 0;
        }

        if (!document.frm.inNumbanco.value) {
            alertaAviso("@Campo Banco inválido!",'form','erro','<?=Sessao::getId();?>', '../');
            status = 0;
        }
    }

    if (status)
        document.frm.submit();
}

function Limpar(){
   limpaFormulario();
   document.frm.reset();
}

function CancelarBaixa() {
<?php
    $stLink = Sessao::read('stLink');
?>
    mudaTelaPrincipal("<?=$pgList.'?'.Sessao::getId().$stLink;?>");
}

function Cancelar(){
<?php
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgForm.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function visualizarRegistro(Numbanco, NumAgencia){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = "BancoAgenciaLista";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumbanco='+Numbanco+'&inNumAgencia='+NumAgencia;
    document.frm.submit();
    document.frm.target = 'telaPrincipal';
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function validar(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    if (tecla == 13) return true;  // Enter
    if (tecla == 45) return true;
    if (tecla == 10) return true;
    if (tecla == 0) return true;
    if (tecla == 1) return true;
    if (tecla == 2) return true;
    if (tecla == 3) return true;
    if (tecla == 4) return true;
    if (tecla == 5) return true;
    if (tecla == 6) return true;
    if (tecla == 7) return true;
    if (tecla == 9) return true;
    if (tecla == 8) return true;

    if (tecla >= 97 && tecla <= 122 ) return true;
    if (tecla >= 65 && tecla <= 90 ) return true;
    //if (tecla >= '0' && tecla <= '9' ) return true;


    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}


function consultarManual(){

   var itens = consultarManual.arguments;
   var x     = new Number(0);
   var link  = new String();
   var cmp   = new Array( 'incodLancamento','inNumeracao','inNumeracaoMigrada','stExercicio','inCodParcela','dtDataBase','stNomCgm','inNumCgm','inCodConvenio','nuValorParcela','inInscricao','dtVencimento','boValida','inNrParcela' );
  
   while(x<itens.length){
       link+= "&"+cmp[x]+"="+itens[x];
       x++;
   }
   location.href='FMManterBaixaManual.php?<?=Sessao::getId();?>'+link;
}

</script>

