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
    * Arquivo JavaScript - PAO   
    * Data de Criação   : 03/04/2006


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
    
    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-08-18 10:18:30 -0300 (Sex, 18 Ago 2006) $
    
    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.3  2006/08/18 13:16:19  jose.eduardo
Bug #6740#

Revision 1.2  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    stAction = document.frm.action;
    stTarget = document.frm.target;
    stCtrl   = document.frm.stCtrl.value;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limpar(){
    document.frm.inTipoPAO.value = '';
    document.getElementById('spnPAO').innerHTML = '';
    document.getElementById('spnListaPAO').innerHTML = '';
    document.frm.inTipoPAO.focus();
}

</script>
