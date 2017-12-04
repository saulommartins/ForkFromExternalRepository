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
    * Página de Formulario de Inclusao/Alteracao de Bancos

    * Data de Criação   : 13/10/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva 
    * @ignore

    * $Id: JSManterAgencia.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.05.02

*/

/*
$Log$
Revision 1.4  2006/09/15 14:57:28  fabio
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

function montaAtributosAgencia(){
    document.frm.stCtrl.value = 'montaAtributosAgencia';
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function validar(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    if (tecla == 13) return true;  // Enter
    if (tecla == 45) return true;
    if (tecla == 10) return true;
    if (tecla == 0) return true;
    if (tecla == 1) return true;
    if (tecla == 2) return true;
    if (tecla == 3) return true;
    if (tecla == 4) return true;
    if (tecla == 5) return true;
    if (tecla == 6) return true;
    if (tecla == 7) return true;
    if (tecla == 9) return true;
    if (tecla == 8) return true;

    if (tecla >= 97 && tecla <= 122 ) return true;
    if (tecla >= 65 && tecla <= 90 ) return true;
    //if (tecla >= '0' && tecla <= '9' ) return true;


    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

</SCRIPT>
