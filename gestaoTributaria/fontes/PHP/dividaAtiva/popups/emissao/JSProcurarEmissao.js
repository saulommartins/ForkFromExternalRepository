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
  * Página de Funções Javascript para popup de Emissao
  * Data de criação : 26/09/2006


    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Programador: Fernando Piccini Cercato

    * $Id: JSProcurarEmissao.js 66240 2016-07-28 18:10:17Z evandro $

    Caso de uso: uc-05.04.07
**/

/*
$Log$
Revision 1.1  2006/09/29 10:50:59  cercato
*** empty log message ***

*/

?>
<script type="text/javascript">

function Insere( stR1, stR2  ){
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stR2;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$request->get("campoNum");?>.value = stR1;

// var stNomCampoCombo = <?=$request->get('stNomCampoCombo') ? $request->get('stNomCampoCombo') : 'null' ;?>;
//     alert(stNomCampoCombo.length);
    window.close();
}

function InsereTipoDoc( stR1, stR2 , stR3 ){
    <?php
        $request = new Request($_REQUEST);
    ?>
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$request->get("campoNum");?>.value = stR1;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$request->get("campoNom");?>').innerHTML = stR2;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$request->get("campoNum");?>.disabled = false;    
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$request->get("stNomCampoCombo") ? $request->get("stNomCampoCombo") : $request->get("campoNom") ;?>.value = stR3;

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

jQuery(document).ready(function(){
    jQuery("#inCodTipoDocumento").val(jQuery('#inCodTipoDocumento', window.opener.parent.frames['telaPrincipal'].document).val());
});
</script>
