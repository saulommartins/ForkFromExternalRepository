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
    * Página do Frame oculto para Popup de Responsavel
    * Data de Criação   : 01/03/2004


    * @author Diego Barbosa Victoria
    * @author Marcelo B. Paulino

    * @ignore

	* $Id: JSProcurarResponsavel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.5  2006/09/15 13:50:41  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function habilitaCampos( tipoHabilita ){
    var f = document.frm;

    //habilita campos para Pessoa Fisica
    if( tipoHabilita == 'F' ){
        f.stCPF.disabled = false;
        f.stCNPJ.value = '';
        f.stCNPJ.disabled = true;
        f.stNomeFantasia.value = '';
        f.stNomeFantasia.disabled = true;
    }
}

function filtrar(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;   
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
