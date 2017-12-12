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
  * Página de Funções Javascript para popup de ACRESCIMO
  * Data de criação : 01/06/2005


    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: JSProcurarCredito.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Caso de uso: uc-05.05.10
**/

/*
$Log$
Revision 1.10  2007/08/13 18:54:32  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.9  2006/09/18 08:47:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function Insere( stR1,stR2,stR3,stR4,stR5,stR6 ){
    if( eval( window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>') ) != null )  {
        window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stR5;
        var campoHidden = eval( 'window.opener.parent.frames["telaPrincipal"].document.frm.<?=$_REQUEST["campoNom"]?>' );
        if( campoHidden != null ) campoHidden.value = stR5;
    }
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.value = stR1+"."+stR2+"."+stR3+"."+stR4;
    if (window.opener.parent.frames['telaPrincipal'].document.frm.inCodEntidade){
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodEntidade.value = stR6;
    }
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.focus();
    window.close();
} 

function Limpar(){
    document.frm.reset();
    preencheCombos();
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
