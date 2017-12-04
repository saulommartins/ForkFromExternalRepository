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
    * Arquivo JavaScript de PopUp de Responsável Técnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

	* $Id: JSProcurarResponsavel.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.6  2006/09/15 13:50:37  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>
<script type="text/javascript">

function focusIncluir(){
    document.frm.inProcesso.focus();
}
    

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;   
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function montaAtributosUf(){
    document.frm.stCtrl.value = 'montaAtributosUf';
    var stTraget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function montaAtributosProfissao(){
    document.frm.stCtrl.value = 'montaAtributosProfissao';
    var stTraget        = document.frm.target;
    var stAction        = document.frm.action; 
    var cmbProfissao    = document.frm.cmbProfissao;
//    document.frm.inCodigoProfissao.value = cmbProfissao.options[cmbProfissao.selectedIndex].value;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;   
    document.frm.target = stTraget;
}

function Limpar(){
   limpaFormulario();
   buscaValor('LimparSessao');
   document.frm.reset();
}

function Insere(num,nom,NomProf,codProf,seq){
    var sNum;
    var sNom;
    var cProf;
    var sNProf;
    var sSeq;

    sSeq = seq;
    sNum = num;
    sNom = nom;
    cProf = codProf;
    sNProf = NomProf;

    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = sNom;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST["nomForm"];?>.<?=$_REQUEST["campoNum"];?>.value = sNum;

    <?
        if ( $_REQUEST["inCodProfissao"] != "" ) {
    ?>
            window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST["nomForm"];?>.<?=$_REQUEST["inCodProfissao"];?>.value = cProf;
    <?
        }
    ?>

    //window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST["nomForm"];?>.<?=$_REQUEST["stProfissao"];?>.value = sNProf;

    <?
    if( $_REQUEST['tipoBusca'] == 'Profissao' ){
        ?>
            window.opener.parent.frames['telaPrincipal'].document.getElementById("<?=$_REQUEST['stProfissao'];?>").innerHTML = sNProf;
        <?
    }
    ?>

    <?
        if ($_REQUEST["inSequencia"] != "") {
    ?>
        window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST["nomForm"];?>.<?=$_REQUEST["inSequencia"];?>.value = sSeq;
    <?
        }
    ?>

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
