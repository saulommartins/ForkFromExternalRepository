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
    * Arquivo JS - Manter Lançamento
    * Data de Criação   : 17/11/2004


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-19 15:02:57 -0300 (Qua, 19 Jul 2006) $
    
    * Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.5  2006/07/19 18:02:57  jose.eduardo
Bug #6302#

Revision 1.4  2006/07/17 19:59:09  jose.eduardo
Bug #6302#

Revision 1.3  2006/07/05 20:51:08  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function validaMes( campo , MesProcessamento ) {
    var Mes = campo.value.split('/');
    Mes[1] = parseInt( Mes[1],10 );
    
    if( Mes[1] != MesProcessamento )
        alertaAviso('@Valor inválido. (O Mês digitado não corresponde ao mês de Processamento)','form','erro','<?=Sessao::getId()?>');
}

function Limpar() {

    document.getElementById("inCodEntidade").value = "";
    document.getElementById("stNomEntidade").value = "";
    document.getElementById("inCodLote").value = "";
    document.getElementById("stNomLote").readOnly = false;
    document.getElementById("stDtLote").readOnly = false;
    document.getElementById("stNomLote").value = "";
    document.getElementById("stDtLote").value  = "";
    document.frm.inCodContaDebito.value = '';
    document.getElementById('stContaDebito').innerHTML = '&nbsp;';
    document.frm.inCodContaCredito.value = '';
    document.getElementById('stContaCredito').innerHTML = '&nbsp;';
    document.frm.inCodHistorico.value = '';
    document.getElementById('stNomHistorico').innerHTML = '&nbsp;';
    document.frm.stComplemento.value = '';
    document.frm.nuValor.value = '';
}

</script>
