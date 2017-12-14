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
    * Página de funções javascript para Configuração de Recurso STN
    * Data de Criação   :16/08/2016


    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Michel Teixeira
    * @ignore

    $Id: JSManterRecurso.js 66353 2016-08-16 20:04:08Z michel $
*/

?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function excluirRecurso( inCodEntidade, inCodOrgao, inCodUnidade, inCodRecurso, inTipoRecurso, inCodAcao ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirRecurso';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inEntidade='+inCodEntidade+'&inOrgao='+inCodOrgao+'&inUnidade='+inCodUnidade+'&inRecurso='+inCodRecurso+'&inTipo='+inTipoRecurso+'&inAcao='+inCodAcao+'&stCtrl=excluirRecurso';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
