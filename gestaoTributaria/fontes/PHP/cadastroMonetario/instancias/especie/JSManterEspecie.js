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
    * Página de Formulario de Inclusao/Alteracao de ESPECIES

    * Data de Criação   : 08/12/2005


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva 
    * @ignore

    * $Id: JSManterEspecie.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.05.09

*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function Cancelar(){
<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function preencheGenero(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheGenero';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</SCRIPT>
