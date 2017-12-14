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
    * Página de Filtro do Relatório Bancário de Pensão Judicial
    * Data de Criação : 21/03/2007 


    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30566 $
    $Name$
    $Autor: $
    $Date: 2007-08-09 17:17:24 -0300 (Qui, 09 Ago 2007) $

    * Casos de uso: uc-04.05.57 
*/
/*
$Log$
Revision 1.4  2007/08/09 20:16:15  andre
Construcao

Revision 1.2  2007/03/27 18:22:45  andre
Contrução


*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    stAction = document.frm.action; 
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = stAction;
}

</script>
