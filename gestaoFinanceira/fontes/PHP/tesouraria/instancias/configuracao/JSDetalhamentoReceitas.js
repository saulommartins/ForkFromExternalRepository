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
    * Arquivo JavaScript
    * Data de CriaÃ§Ã£o   : 08/09/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 32867 $
    $Name$
    $Autor:$
    $Date: 2007-07-18 17:20:15 -0300 (Qua, 18 Jul 2007) $
    
    * Casos de uso: uc-02.04.03

*/

/*
$Log$
Revision 1.8  2007/07/18 20:20:15  vitor
Bug#8920#

Revision 1.7  2007/07/03 19:17:28  vitor
Bug#8920#

Revision 1.6  2007/06/14 18:40:21  domluc
Correção de Bug 

Revision 1.5  2007/05/29 14:11:35  domluc
Mudanças na forma de classificação de receitas.

Revision 1.4  2007/03/09 15:41:51  domluc
uc-02.04.33

Revision 1.3  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limpaCredito() {
    parent.frames['telaPrincipal'].document.frm.inCodCredito.value='';
    parent.frames['telaPrincipal'].document.getElementById('stCredito').innerHTML='&nbsp;';
    parent.frames['telaPrincipal'].document.getElementById('spnAcrescimo').innerHTML='';
}

function detalhaConta( stExercicio, inCodPlano ) {
    parent.frames['telaPrincipal'].location='FMDetalhamentoReceitas.php?<?=Sessao::getId();?>&stExercicio='+stExercicio+'&inCodConta='+inCodPlano;
}

function excluirCredito( stCodCredito, exCodAcrescimo, exCodTipo ) {
    document.frm.stCtrl.value = 'excluiCredito';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stCodCredito=' + stCodCredito + '&exCodAcrescimo='+exCodAcrescimo+'&exCodTipo='+exCodTipo+'';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';

}
</script>
