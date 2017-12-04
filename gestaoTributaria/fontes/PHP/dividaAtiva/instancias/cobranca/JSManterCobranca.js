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
    * Arquivo com funcoes JavaScript para Configuração Divida Ativa
    * Data de Criação: 05/05/2006


    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: JSManterCobranca.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* Casos de uso: uc-05.04.04
*/

/*
$Log$
Revision 1.6  2007/08/28 20:11:17  cercato
botao para voltar tela.

Revision 1.5  2007/08/23 15:12:29  cercato
correcao na validacao do formulario da cobranca.

Revision 1.4  2007/03/30 19:42:33  cercato
Bug #8962#

Revision 1.3  2007/03/29 13:25:07  cercato
adicionando aviso de estorno de cobranca

Revision 1.2  2007/03/27 14:47:31  cercato
Bug #8891#

Revision 1.1  2007/02/09 18:32:04  cercato
*** empty log message ***

*/

?>

<script type="text/javascript">

function ListarCobranca() {
    if ( document.frm.inCGM.value == "" && document.frm.inCodInscricao.value == "" && document.frm.inCodImovelInicial.value == "" && document.frm.inNumInscricaoEconomicaInicial.value == "" ) {
        alertaAviso("Ao menos um filtro deve ser selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        document.frm.submit();
    }
}

function VoltarTela() {
<?php
    $stLink = Sessao::read('stLink');
?>
    mudaTelaPrincipal("<?=$pgList.'?'.Sessao::getId().$stLink;?>");
}

function Cancelar () {
<?php
    $stLink = Sessao::read('stLink');
?>
    mudaTelaPrincipal("<?=$pgFilt.'?'.Sessao::getId().$stLink;?>");
}

function selecionarTodos(){
    var cont = 0;

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boSelecionada')) ){
            document.frm.elements[cont].checked = true;
        }
        cont++;
    }
}

function validarListarEstorno(){
<?php
    $stLink = Sessao::read('stLink');
?>
    var cont = 0;
    var selecionado = 0;
    var tmp = '';
    var tmp_documento = '';
    var tmp_nrdocumento = '';
    var tmp_stMotivo = '';

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( document.frm.elements[cont].name == 'stMotivo'){
            tmp_stMotivo = document.frm.elements[cont].value;
        }
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boSelecionada')) ){
            if ( document.frm.elements[cont].checked ) {
                tmp = tmp + '*_*' + namee + '=' + document.frm.elements[cont].value;
                tmp_documento = document.frm.elements[cont].value;
                arTmp = tmp_documento.split('-');
                if ( selecionado )
                    tmp_nrdocumento = tmp_nrdocumento + ',';

                tmp_nrdocumento = tmp_nrdocumento + ' ' + arTmp[1];
                selecionado++;
            }
        }

        cont++;
    }

    if ( !selecionado ) {
        alertaAviso("Erro! Nenhum registro foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        tmp = tmp + '*_*stDescQuestao='+tmp_nrdocumento + '*_*stMotivo=' + tmp_stMotivo;
        alertaQuestao('../../../../../../gestaoTributaria/fontes/PHP/dividaAtiva/instancias/cobranca/<?=$pgProc.'?'.str_replace("&", "*_*", Sessao::getId().$stLink."&stAcao=Estornar") ?>'+tmp,'sn_excluir','<?=Sessao::getId()?>');

  //      document.frm.stCtrl.value = "PRManterCobranca.php";
    //    document.frm.action = '<?=$pgProc.'?'.Sessao::getId().$stLink;?>';
      //  document.frm.submit();
    }
}

function validarListar(){
    var cont = 0;
    var selecionado = 0;

    while(cont < document.frm.elements.length){
        var namee = document.frm.elements[cont].name;
        if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boSelecionada')) ){
            if ( document.frm.elements[cont].checked ) {
                selecionado = 1;
                break;
            }
        }

        cont++;
    }

    if ( !selecionado ) {
        alertaAviso("Erro! Nenhum registro de parcela foi selecionado!",'form','erro','<?=Sessao::getId();?>', '../');
    }else {
        if( Valida() ){
            document.frm.stCtrl.value = "LSManterCobrancaParcelas.php";
            document.frm.action = '<?=$pgListParc.'?'.Sessao::getId().$stLink;?>';
            document.frm.submit();
        }
    }
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

</script>
