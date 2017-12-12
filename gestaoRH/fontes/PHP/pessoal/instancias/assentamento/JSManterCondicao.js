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
* JavaScript de condição do assentamento
* Data de Criação: 05/08/2005


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage 

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.13
*/

/*
$Log$
Revision 1.4  2006/08/08 17:46:21  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">
function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaFuncao ( objeto ){
     document.frm.stCtrl.value = 'buscaFuncao';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&objeto=' + objeto;
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function alteraDado(stControle, inId, inCodClassificacaoVinculacao){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId +'&inCodClassificacaoVinculacao=' + inCodClassificacaoVinculacao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirAssentamentoVinculado(){
    var mensagem = validaAssentamentoVinculado();
    
    if ( mensagem == '' ){
        buscaValor('incluirAssentamentoVinculado');
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function validaAssentamentoVinculado(){
    var mensagem = '';
    
    if ( document.frm.elements['inCodClassificacaoVinculacaoTxt'].value  == '') {
        mensagem += "@É necessário selecionar a classificação do assentamento para vinculação()!";
    }
    if ( document.frm.elements['stSiglaVinculado'].value  == '') {
        mensagem += "@É necessário selecionar o assentamento do assentamento para vinculação()!";
    }    
    return mensagem;
}

function limparAssentamentoVinculado(){
    document.frm.inCodClassificacaoVinculacaoTxt.value  = '';
    document.frm.inCodClassificacaoVinculacao.value     = '';
    document.frm.stSiglaVinculado.value                 = '';
    document.frm.inCodAssentamentoVinculacao.value      = '';
    document.frm.stSiglaVinculado.value                 = '';
    document.frm.inDiasVinculado.value                  = '';
    document.frm.inCodFuncaoTxt.value                   = '';
    document.frm.inCodFuncao.value                      = '';
    document.frm.inDiasIncidencia.value                 = '';
    document.frm.inDiasIncidencia.disabled              = false;
    document.frm.inAssentamentoVinculado.value          = '';
    document.frm.boCondicao[0].checked                  = true;
    limpaSelect(document.frm.inCodAssentamentoVinculacao,0);
    document.frm.inCodAssentamentoVinculacao.options[0] = new Option('Selecione','', 'selected');
    document.frm.inCodClassificacaoVinculacaoTxt.readOnly = false; 
    document.frm.inCodClassificacaoVinculacaoTxt.style.color = '#000000';
    document.frm.inCodClassificacaoVinculacao.disabled = false;      
    document.frm.stSiglaVinculado.readOnly              = false;                
    document.frm.stSiglaVinculado.style.color           = '#000000';          
    document.frm.inCodAssentamentoVinculacao.disabled   = false;          
}

function limparAssentamento(){
    document.frm.inCodClassificacaoVinculacaoTxt.value  = '';
    document.frm.inCodClassificacaoVinculacao.value     = '';
    document.frm.stSiglaVinculado.value     = '';    
    limpaSelect(document.frm.inCodAssentamentoVinculacao,0);
    document.frm.inCodAssentamentoVinculacao.options[0] = new Option('Selecione','', 'selected');

}

</script>
