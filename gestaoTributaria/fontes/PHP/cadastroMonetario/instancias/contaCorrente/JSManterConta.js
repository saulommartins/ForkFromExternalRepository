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
    * Página de funções javascript para o cadastro de Conta Corrente
    * Data de Criação   :04/11/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @ignore

    * $Id: JSManterConta.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.05.03
*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function preencheAgencia(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheAgencia';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function Limpar(){
    document.frm.reset();
    preencheMunicipio( 'limpar' );
}

function validar(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    if (tecla == 88) return true;
    if (tecla == 120) return true;
    if (tecla == 13) return true;  // Enter
    if (tecla == 0) return true;
    if (tecla == 1) return true;
    if (tecla == 2) return true;
    if (tecla == 3) return true;
    if (tecla == 4) return true;
    if (tecla == 5) return true;
    if (tecla == 6) return true;
    if (tecla == 7) return true;
    if (tecla == 10) return true;
    if (tecla == 9) return true;
    if (tecla == 45) return true;
    if (tecla == 8) return true;
    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

function validarAgencia(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    if (tecla == 13) return true;  // Enter
    if (tecla == 0) return true;
    if (tecla == 1) return true;
    if (tecla == 2) return true;
    if (tecla == 3) return true;
    if (tecla == 4) return true;
    if (tecla == 5) return true;
    if (tecla == 6) return true;
    if (tecla == 7) return true;
    if (tecla == 10) return true;
    if (tecla == 9) return true;
    if (tecla == 45) return true;
    if (tecla == 8) return true;
    patron =/\d/;
    te = String.fromCharCode(tecla);
    return patron.test(te);
}

</script>
