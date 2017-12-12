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
    * Página de Frame Oculto de Definição de Desoneração
    * Data de Criação   : 30/05/2005


    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: JSManterDesoneracao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.3  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = "oculto"; 
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function Limpar(){
    buscaValor('limpaTudo');
}

function busca(tipoBusca){
    var stTMPTarget = document.frm.target;
    var stTMPAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTMPTarget;
    document.frm.action = stTMPAction;
}

function ExecutarDesoneracao(){
    if( Valida() ){
        document.frm.submit();
        BloqueiaFrames(true,false);      
    }
}

</script>

