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
    * Página de funções javascript para o cadastro de trecho
    * Data de Criação   : 30/03/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore
    
    * $Id: JSProcurarTrecho.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.4  2006/09/15 15:04:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">
function Insere(codigo_sequencia,tipo_nome,sequencia){
    var sNum;
    var sNom;
    sNum = codigo_sequencia;
    sNom = tipo_nome+' ('+sequencia+')';
    window.opener.parent.frames['telaPrincipal'].document.frm.btnIncluirTrecho.disabled = false
    window.opener.parent.frames['telaPrincipal'].document.frm.inNumTrecho.value = sNum;
    window.opener.parent.frames['telaPrincipal'].document.frm.stTrecho.value = sNom;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('stNumTrecho').innerHTML = sNom;
    window.close();
}

function preencheMunicipio( stLimpar ){
    document.frm.stCtrl.value = 'preencheMunicipio';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stLimpar=' + stLimpar;
    document.frm.submit();
    document.frm.target = "";
    document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
}

function Limpar(){
    document.frm.reset();
    preencheMunicipio( 'limpar' );
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
