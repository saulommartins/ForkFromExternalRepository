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
    * Página de JS - Manter Plano Conta
    * Data de Criação   : 04/11/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-07-12 17:47:15 -0300 (Qui, 12 Jul 2007) $
    
    * Casos de uso: uc-02.02.02
*/

/*
$Log$
Revision 1.5  2007/07/12 20:43:26  luciano
Bug#9577#

Revision 1.4  2006/07/05 20:51:13  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function bloqueiaAbas(){
    window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = "javascript:buscaDado('exibeAviso')";
}


function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = 'OCManterPlanoConta.php?stCtrl='+variavel+'&<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function exibeAvisoAbaBloqueada() {
    alertaAviso('Somente a conta Analítica pode ter conta de banco','aviso','aviso','<?=Sessao::getId();?>');
}

</script>
                
