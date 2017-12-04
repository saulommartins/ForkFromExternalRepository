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
    * Página de funções javascript oara o relatório de cadastro imobiliário
    * Data de Criação   : 13/04/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore
    
    * $Id: JSCadastroImobiliario.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.23
*/

/*
$Log$
Revision 1.3  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function disableAtributos(valor){
    if( valor == 'sintetico' ){
        document.frm.inCodAtributosDisponiveis.disabled = true;
        document.frm.inCodAtributosSelecionados.disabled = true;
        document.frm.inCodAtributosLote2Disponiveis.disabled = true;
        document.frm.inCodAtributosLote2Selecionados.disabled = true;
        document.frm.inCodAtributosLote3Disponiveis.disabled = true;
        document.frm.inCodAtributosLote3Selecionados.disabled = true;
    } else {
        document.frm.inCodAtributosDisponiveis.disabled = false;
        document.frm.inCodAtributosSelecionados.disabled = false;
        document.frm.inCodAtributosLote2Disponiveis.disabled = false;
        document.frm.inCodAtributosLote2Selecionados.disabled = false;
        document.frm.inCodAtributosLote3Disponiveis.disabled = false;
        document.frm.inCodAtributosLote3Selecionados.disabled = false;
    }
}

function Salvar( sessid ){
    var erro = false;
    var mensagem = "";
    var inLength = document.frm.inCodAtributosSelecionados.options.length;
    if( (inLength > 4)  && (document.frm.stTipoRelatorio.value == 'analitico') ){
        erro = true;
        mensagem = "@Não é possível selecionar mais de 4 atributos para exibição no relatório!";
    } else {
        mensagem = "@";
        erro = false;
    }
    if( erro == false ) {
        if( Valida() ){
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
            document.frm.submit();
        }
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
}
</script>
