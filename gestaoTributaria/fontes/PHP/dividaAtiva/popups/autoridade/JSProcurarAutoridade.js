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
  * Página de Funções Javascript para popup de AUTORIDADE
  * Data de criação : 26/09/2006


    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Diego Bueno Coelho

    * $Id: JSProcurarAutoridade.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    Caso de uso: uc-05.04.08
**/

/*
$Log$
Revision 1.1  2006/09/26 11:15:13  dibueno
*** empty log message ***

*/

?>
<script type="text/javascript">

function Insere( stR1, stR2 ){
//alert ( <?=$_REQUEST["campoNum"];?> );
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"];?>').innerHTML = stR2;
    window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNum"];?>.value = stR1;
	//window.opener.parent.frames['telaPrincipal'].document.frm.<?=$_REQUEST["campoNumCGM"];?>.value = stR3;

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
