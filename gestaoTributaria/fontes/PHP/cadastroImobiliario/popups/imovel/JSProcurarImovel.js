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
    * Página de funções javascript para o cadastro de face de imóvel
    * Data de Criação   : 11/01/2005


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore
    
    * $Id: JSProcurarImovel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.09
*/

/*
$Log$
Revision 1.6  2006/09/15 15:04:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function preencheProxCombo( inPosicao  ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function preencheCombos(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheCombos';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;

}

function Insere( inInscricaoMunicipal, stLogradouro, stNumero, stComplemento ){
    var stEndereco="";

    stEndereco = stLogradouro;
    if (stNumero)
        stEndereco += ", " + stNumero;

    if (stComplemento)
        stEndereco += " - " + stComplemento; 
    if( eval( window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>') != null )){
        window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stEndereco;
        var campoHidden = eval( 'window.opener.parent.frames["telaPrincipal"].document.frm.<?=$_REQUEST["campoNom"]?>' );
        if( campoHidden != null ) campoHidden.value=stEndereco;
    }

    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.value = inInscricaoMunicipal;
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
