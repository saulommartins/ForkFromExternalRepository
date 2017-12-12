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
    * Página de funções javascript para o cadastro de imóvel
    * Data de Criação   : 01/12/2004


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: JSManterImovel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function Salvar(){
    var mensagem = "";
    var erro;
    if( Valida() ){
        if( document.frm.boEnderecoEntrega.checked == true ){
            if( document.frm.inNumLogradouro.value == '' ){
                erro = true;
                mensagem += "@Campo Logradouro do Endereço de Entrega inválido!()";
            }
            if( document.frm.stNumero.value == '' ){
                erro = true;
                mensagem += "@Campo Número do Endereço de Entrega inválido!()";
            }
            if( erro ){
                alertaAviso(mensagem,'form','erro','PHPSESSID=0e686f5c4c1938bb154c8ce9fa366db5&iURLRandomica=20050714101751522', '../');
            } else {
                document.frm.submit();
            }
        } else {
            document.frm.submit();
        }
    }
}

function focusIncluir(){
    document.frm.inProcesso.focus();
}

function visualizarProcesso(processo, timestamp, inscricao_municipal,ano_exercicio){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'visualizarProcesso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&cod_processo='+processo+'&timestamp='+timestamp+'&inscricao_municipal='+inscricao_municipal+'&ano_exercicio='+ano_exercicio;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function mudaMenu(func){
    sPag = "<?=CAM_FW_INSTANCIAS;?>index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_gestao_pass=5&cod_modulo_pass=12&stNomeGestao=Tributária&modulos=Cadastro Imobiliário&cod_func_pass="+func;
    parent.parent.frames["telaMenu"].location.replace(sPag);
}

//FUNCOES PARA PREENCHIMENTO DA LOCALIZACAO
function preencheProxCombo( inPosicao  ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function preencheCombos(){
    BloqueiaFrames(true,false);
    document.frm.stCtrl.value = 'preencheCombos';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaDado(tipoBusca){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function buscaLogradouro(){
    document.frm.stCtrl.value = 'buscaLogradouro';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function buscaLote(){
    document.frm.stCtrl.value = 'buscaLote';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function buscaBairro(){
    document.frm.stCtrl.value = 'buscaBairro';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function buscaLogradouroFiltro(){
    document.frm.stCtrl.value = 'buscaLogradouroFiltro';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = stTraget;
}

function setaEndereco(){
    document.frm.inCodigoConfrontacao[1].selected = true;
    teste = document.frm.inCodigoConfrontacao.value;
    document.frm.stChaEndereco.value = teste;
}

//FUNCAO GENERICA DE EXCLUSAO DE LINHAS DAS LISTAS
function excluirDado( stAcao, inLinha ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inLinha='+inLinha ;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

//FUNCOES PARA A LISTA DE PROPRIETARIOS
function validaProprietario(){
     var erro = false;
     var mensagem = "";
     stCampo = document.frm.inNumCGM;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo CGM inválido!()";
     }
     stCampo = document.frm.flQuota;
     if( trim( stCampo.value ) == "" ){
         erro = true;
         mensagem += "@Campo Quota inválido!()";
     }
     if( numericToFloat( stCampo.value ) == 0 ){
         erro = true;
         mensagem += "@Campo Quota deve ter valor maior que zero!";
         stCampo.focus();
     }
     if( numericToFloat( stCampo.value ) > 100 ){
         erro = true;
         mensagem += "@Campo Quota deve ter valor menor ou igual a 100!";
         stCampo.focus();
     }
     if( erro ){
          alertaAviso(mensagem,'form','n_incluir','<?=Sessao::getId();?>', '../');
     }
 return !erro;
}

function incluirProprietario(){
    if( validaProprietario() ){
        document.frm.stCtrl.value = 'incluirProprietario';
        var stTraget = document.frm.target;
        document.frm.target = "oculto";
        var stAction = document.frm.action;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.action = stAction;
        document.frm.target = stTraget;
    }
}

function limparProprietario(){
   document.frm.inNumCGM.value = "";
   document.getElementById('inNomCGM').innerHTML = "&nbsp;";
   document.frm.flQuota.value = "";
   document.frm.boProprietario[0].checked = true;
}

function montaFormEnderecoEntrega(){
    document.frm.stCtrl.value = 'montaFormEnderecoEntrega';
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
   document.getElementById('inNomCGM').innerHTML             = "&nbsp;";
   document.getElementById('campoInner').innerHTML           = "&nbsp;";
   document.getElementById('innerBairroEntrega').innerHTML   = "&nbsp;";
   document.getElementById('lsListaProprietarios').innerHTML = "";
   document.getElementById('lsListaPromitentes').innerHTML   = "";
}

function limparFiltro(){
    document.frm.reset();
    preencheCombos();
    habilitaSpnImovelCond();
}

function submeteFiltro(){
    stLocalizacao = document.frm.stChaveLocalizacao.value;
    stInscricao   = document.frm.inNumeroInscricao.value;
    if ( stInscricao == "" && stLocalizacao == "" ){
        mensagem = "Campos Número da Inscrição ou Localização não foram preenchidos!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        Salvar();
    }
}

function validaCRECI( evento ){
    var teclaPressionada;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    if( teclaPressionada == 32 )
        return false;
    return true;
}

function atualizaComponente(){
    HabilitaLayer("");
}
</script>
