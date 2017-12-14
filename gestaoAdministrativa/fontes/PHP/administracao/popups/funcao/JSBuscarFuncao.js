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
* Arquivo de popup para manutenção de funções
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 21417 $
$Name$
$Author: cassiano $
$Date: 2007-03-28 16:43:59 -0300 (Qua, 28 Mar 2007) $

Casos de uso: uc-01.03.95
*/
?>
<script type="text/javascript">

function setaFuncao(cod_modulo, cod_biblioteca, cod_funcao, nom_funcao ){
    stChaveFuncao = cod_modulo + '.' + cod_biblioteca + '.' + cod_funcao;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = stChaveFuncao;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = nom_funcao;
    window.close();
}

function limpaCampoFuncao(){
    document.frm.stNomeFuncao.value ="";  
}

function limpar(){
    
    // Verifica se o objeto do FORM é um <select>, caso for e tenha valor, limpa.
    if (jQuery('select').attr('value') > 0)
        jQuery('select').val("");

    jQuery('input[type=text]').val("");
}

function filtrar(){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
//    alert( document.frm.name );
    if( document.frm.inCodBiblioteca.value == "" ){
        alertaAviso("Módulo e Biblioteca devem ser selecionados!",'frm','erro','<?=Sessao::getId();?>', '../');

    }else{
        document.frm.action = '<?=$pgList;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.target = stTraget;
        document.frm.action = stAction;
    }
}

function Voltar(){
    document.frm.target = 'frm';
    //window.location = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    //document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    //document.frm.submit();
    window.location = "<?=$pgFilt;?>?<?=Sessao::getId();?><?=$stLink;?>&nomForm=frm&campoNum=inCodFuncao&campoNom=stFuncao&tipoBusca=<?=$_REQUEST['tipoBusca']?>";
}
</script>
