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
    * Página de funções javascript para o cadastro de Borderô
    * Data de Criação   :29/01/2006


    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto
    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.20
*/

/*
$Log$
Revision 1.2  2006/07/05 20:39:28  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function preencheAgencia(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.stCtrl.value = 'preencheAgencia';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
    document.frm.stCtrl.value = stCtrl;
}

function preencheCamposCodigos(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.stCtrl.value = 'preencheCamposCodigos';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
    document.frm.stCtrl.value = stCtrl;
}

function validaForm(){
    
    var erro = false;

    var mensagem = "";

    if( trim(document.frm.inCodEntidade.value) == "" ){
        erro = true;
        mensagem += "@Campo Entidade inválido!()";
    }
    if( trim(document.frm.stExercicio.value) == "" ){
        erro = true;
        mensagem += "@Campo Exercício inválido!()";
    }
    if( trim(document.frm.inNumBanco.value) == "" ){
        erro = true;
        mensagem += "@Campo Banco inválido!()";
    }
    if( trim(document.frm.cmbBanco.value) == "" ){
        erro = true;
        mensagem += "@Campo Banco inválido!()";
    }
    if( trim(document.frm.inNumAgencia.value) == "" ){
        erro = true;
        mensagem += "@Campo Agência inválido!()";
    }
    if( trim(document.frm.cmbAgencia.value) == "" ){
        erro = true;
        mensagem += "@Campo Agência inválido!()";
    }
    if( trim(document.frm.stNumeroConta.value) == "" ){
        erro = true;
        mensagem += "@Campo Conta Corrente inválido!()";
    }
    if( erro ){ 
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }

 return !erro;
 }
 function inclui(){
     if( validaForm() ){
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluiTransferencia';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limpa();
     }
 }

function limpa(){
    document.frm.inValor.value = '';
    document.frm.inCodCredor.value = '';
    document.getElementById('stNomCredor').innerHTML = "";
    document.frm.inNumBancoCredor.value = '';
    document.frm.cmbBancoCredor.value = '';
    document.frm.inNumAgenciaCredor.value = '';
    document.frm.cmbAgenciaCredor.value = '';
    document.frm.stNumeroContaCredor.value = '';
    document.frm.inNrDocumento.value = '';
    document.frm.stObservacao.value = '';
}

function limpaForm() {
    document.frm.inCodEntidade.value   = "";
    document.getElementById('spnBoletim').innerHTML = "";
    document.frm.inNumBanco.value      = "";
    document.frm.cmbBanco.value        = "";
    document.frm.inNumAgencia.value    = "";
    document.frm.cmbAgencia.value      = "";
    document.frm.stNumeroConta.value   = "";
    limpa();

}
</script>
