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
    * JavaScript
    * Data de Criação   : 25/04/2006
    
    
    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Andre Almeida
    * @ignore
    
    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $
    
    * Casos de uso: uc-04.05.44
*/

/*
$Log$
Revision 1.2  2006/08/08 17:43:47  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function buscaValor( tipoBusca ) {
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}


function preencheEvento( obCampo, stIdCampo, stNatureza ) {
    if ( obCampo.value != "" ) {
        stAction = document.frm.action;
        document.frm.stCtrl.value = "preencheEvento";
        document.frm.action = "<?=$pgOcul;?>?<?=Sessao::getId();?>&stCampoCod="+obCampo.name+"&stCampoDesc="+stIdCampo+"&stNatureza="+stNatureza;
        document.frm.submit();
        document.frm.action = stAction;
    } else {
        obCampo.value = "";
        document.getElementById(stIdCampo).innerHTML = "&nbsp;";
    }
}

function incluirFaixasConcessoes() {
    var stValorInicial = document.frm.inSalarioInicial.value;
    var stValorFinal   = document.frm.inSalarioFinal.value;
    var stValorPagar   = document.frm.inValorPagar.value;
    
    //retira as virgulas e os pontos das strings
    stValorInicial = stValorInicial.replace( /[,.]/gi ,"");
    stValorFinal   = stValorFinal.replace( /[,.]/gi ,"");
    stValorPagar   = stValorPagar.replace( /[,.]/gi ,"");
    
    //Converte string pata inteiro
    inValorInicial = parseInt(stValorInicial);
    inValorFinal   = parseInt(stValorFinal);
    inValorPagar   = parseInt(stValorPagar);
    
    if ( inValorInicial && inValorInicial!="0" ) {
        if ( inValorFinal && inValorFinal!="0" ) {
            if ( inValorPagar && inValorPagar!="0" ) {
                if ( inValorInicial < inValorFinal ) {
                    buscaValor("incluirFaixaConcessao");
                } else {
                    alertaAviso( "O valor inicial do salário deve ser menor que o valor final.", "form", "erro", "<?=Sessao::getId();?>");
                }
            } else {
                if ( !inValorPagar ) {
                    inValorPagar = "";
                }
                alertaAviso( "Valor a Pagar inválido. ("+inValorPagar+")", "form", "erro", "<?=Sessao::getId();?>");
            }
        } else {
            alertaAviso( "Valor Final do Salário inválido. ("+inValorFinal+")", "form", "erro", "<?=Sessao::getId();?>");
        }
    } else {
        alertaAviso( "Valor Inicial do Salário inválido. ("+inValorInicial+")", "form", "erro", "<?=Sessao::getId();?>");
    }
}

function limparDadosFaixasConcessões() {
    document.frm.inSalarioInicial.value = "";
    document.frm.inSalarioFinal.value   = "";
    document.frm.inValorPagar.value     = "";

}

function limparCampos() {
    document.frm.reset();
    buscaValor("montaTela");
}

</script>