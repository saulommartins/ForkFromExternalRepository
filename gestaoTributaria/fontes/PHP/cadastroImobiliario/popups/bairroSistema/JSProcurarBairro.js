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
    * Página de funções javascript para o cadastro de bairro
    * Data de Criação   : 23/03/20054


    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    *@ignore

    * $Id: JSProcurarBairro.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.01.05
*/

/*
$Log$
Revision 1.5  2006/09/15 15:03:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
?>
<script type="text/javascript">

function fechar(){
    window.close ();
}

function preencheBairro( inCodigoUF, inCodigoMunicipio, inCodigoBairro, stNomeBairro ){
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stNomeBairro;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodigoUF.value = inCodigoUF;
    window.opener.parent.frames['telaPrincipal'].document.frm.inCodigoMunicipio.value = inCodigoMunicipio;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.value = inCodigoBairro;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.focus();
    window.close();
}

function filtrar(){
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>';
    document.frm.submit();
}
function incluir(){
    document.frm.action = '<?=$pgForm;?>?<?=Sessao::getId();?>';
    document.frm.submit();
}

function Cancelar () {
<?php
    $stLink = Sessao::read('stLink');
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}
</script>
