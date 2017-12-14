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
    * Data de Criação: 07/02/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-05-11 17:32:30 -0300 (Sex, 11 Mai 2007) $

    * Casos de uso: uc-04.05.41
*/

/**
$Log$
Revision 1.1  2007/05/11 20:32:25  andre
Bug #9211#



*/
?>

<script type="text/javascript">

function buscaValorFiltro(tipoBusca){
     target = document.frm.target ;
     action = document.frm.action;
     document.frm.stCtrl.value = tipoBusca;
     document.frm.target = 'oculto';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = action;
     document.frm.target = target;
}  
</script>
