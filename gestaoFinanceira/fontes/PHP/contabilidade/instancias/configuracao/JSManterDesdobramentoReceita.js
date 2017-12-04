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
    * Arquivo JavaScript - Manter Desdobramento Receita
    * Data de Criação   : 09/02/2005


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira 

    * @ignore    
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-01-22 16:21:04 -0200 (Seg, 22 Jan 2007) $
    
    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.4  2007/01/22 18:20:03  cako
Bug #8154#

Revision 1.3  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    var tmpAction = document.frm.action;
    var tmpTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = tmpAction;
    document.frm.target = tmpTarget;
}

function Limpar(){
    document.frm.stCtrl.value = 'limparReceitaSecundaria';
    var tmpAction = document.frm.action;
    var tmpTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = tmpAction;
    document.frm.target = tmpTarget;
}

function validaReceitaSecundaria(){
    var erro = false;
    var mensagem = "";
    var stCampo = document.frm.inCodReceita;
    if( stCampo.value == "" ){
        erro = true;
        mensagem += "@Campo Receita Secundária inválido!()";
    }
    stCampo = document.frm.flPercentual;
    if( stCampo.value == "" ){
        erro = true;
        mensagem += "@Campo Percentual inválido!";
    }else{
        stCampo = stCampo.value.replace( '.','' );
        stCampo = stCampo.replace( ',','.' );
        stCampo = parseFloat( stCampo );
        if( stCampo > 100.00 ){
            erro = true;
            mensagem += "@Campo Percentual inválido!( Valor máximo permitido: 100 )";
        }else if( stCampo == 0 ){
            erro = true;
            mensagem += "@Campo Percentual inválido!( Valor deve maior que zero )";
        }
    }
    stCampo = document.frm.flPercentual;
    stCampo = stCampo.value.replace( '.','' );
    stCampo = stCampo.replace( ',','.' );
    stCampo = parseFloat( stCampo );
    if( document.frm.stPercentualAtualizado.value < stCampo ){
        erro = true;
            mensagem += "@Campo Percentual inválido!( A soma do percentual das receitas secundárias deve ser menor ou igual a 100 )";
    }
    if( erro ){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    }
    return !erro; 
}

function incluirReceitaSecundaria(){
    if( validaReceitaSecundaria() ){
        document.frm.stCtrl.value = "incluirReceitaSecundaria";
        var tmpAction = document.frm.action;
        var tmpTarget = document.frm.target;
        document.frm.action = '<?=$pgOcul."?".Sessao::getId();?>';
        document.frm.target = 'oculto';
        document.frm.submit();
        document.frm.action = tmpAction;
        document.frm.target = tmpTarget;
    }
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

function limparReceitaSecundaria(){
    document.frm.inCodReceita.value = "";
    document.getElementById("stNomReceita").innerHTML = "&nbsp;";
    document.frm.flPercentual.value = "";
    document.frm.stPercentualAtualizado.value = '100';
}
</script>
