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
* Arquivo de instância para popup de normas
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/

?>

<script type="text/javascript">

function insere( boExibeDataNorma, boExibeDataPublicacao, inCodNorma, stNomNorma, stNomTipoNorma, stNumNormaExercicio, dt_assinatura, dt_publicacao ){
    var stDescricao = stNomTipoNorma + ' ' + stNumNormaExercicio + ' - ' + stNomNorma;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = inCodNorma;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = stDescricao;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.Hdn<?=$_REQUEST['campoNum']?>.value = inCodNorma; 
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNom']?>.value = stDescricao; 
    if( boExibeDataNorma )
        window.opener.parent.frames['telaPrincipal'].document.getElementById('stDataNorma').innerHTML = dt_assinatura;
    if( boExibeDataPublicacao )
        window.opener.parent.frames['telaPrincipal'].document.getElementById('stDataPublicacao').innerHTML = dt_publicacao;

    window.close();
}

function insereNumExercicio(inCodNorma,stNomNorma,stNomTipoNorma,stNumNormaExercicio){
    var stDescricao = stNomTipoNorma + ' ' + stNumNormaExercicio + ' - ' + stNomNorma;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = trim(stNumNormaExercicio);
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = stDescricao;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.Hdn<?=$_REQUEST['campoNum']?>.value = trim(stNumNormaExercicio); 
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNom']?>.value = stDescricao;
    
    //verifica se existe o campo Hidden Codigo Tipo Norma. Para setar o valor após a consuta da norma.
    if(window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnCodNorma')){
        window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnCodNorma').value = inCodNorma ;
    }
    
    //verifica se existe o campo Hidden Codigo Tipo Norma. Para setar o valor após a consuta da norma.
    if(window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnCodTipoNorma')){
        window.opener.parent.frames['telaPrincipal'].document.getElementById('hdnCodTipoNorma').value = <?=$_REQUEST['inCodTipoNorma']?>;
    }
    window.close();
}


function incluir(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgForm;?>?stAcao=incluir&<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>
