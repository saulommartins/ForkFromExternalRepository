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
    * Arquivo JS utilizado na Duplicação de Autorização
    * Data de Criação   : 09/05/2005


    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-02-23 13:15:05 -0200 (Sex, 23 Fev 2007) $
    
    * Casos de uso: uc-02.03.02
                    uc-02.01.08 
*/

/*
$Log$
Revision 1.4  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.3  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = BuscaDado;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function Voltar() {
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId();?>";
    document.frm.submit();

}

function Cancelar( stLocation ){
<?php
$stLink = "&pg=".Sessao::read('pg')."&pos=".Sessao::read('pos').$stFiltro;
//$stLink = "&pg=".sessao->transf4["pg"]."&pos=".sessao->transf4["pos"];
//$stLink = "&pg=".sessao->transf4["pg"]."&pos=".sessao->transf4["pos"]."&filtro".sessao->transf4["filtro"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    window.location = stLocation;
//    document.frm.submit();
}

</script>
