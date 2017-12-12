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
    * Pagina de funcoes Javacript para Responsavel Tecnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * 
    * @ignore

	* $Id: JSProcurarInscricaoEconomica.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.6  2006/09/15 13:48:10  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function Insere( inInscricaoEconomica , stNomCgm, inNumCgm){
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.value = inInscricaoEconomica;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stNomCgm;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.focus();

	var nomcgm = window.opener.parent.frames['telaPrincipal'].document.getElementById('nomCGM');
	var numcgm = window.opener.parent.frames['telaPrincipal'].document.frm.numCgm;

	if(nomcgm) {
		nomcgm.innerHTML = stNomCgm;
	}

	if(numcgm) {
		numcgm.value = inNumCgm;
	}

    window.close();
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
