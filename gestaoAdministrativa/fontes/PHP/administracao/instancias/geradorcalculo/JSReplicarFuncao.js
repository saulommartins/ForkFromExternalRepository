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
    * Página de Javascript do Férias
    * Data de Criação: 22/09/2006

    
    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 16537 $
    $Name$
    $Author: cassiano $
    $Date: 2006-10-09 08:02:51 -0300 (Seg, 09 Out 2006) $

    * Casos de uso: uc-01.03.95
*/

/*
$Log$
Revision 1.2  2006/10/09 11:02:51  cassiano
Bug #7064#

Revision 1.1  2006/09/22 11:21:37  vandre
Construção.


*/
?>
<script type="text/javascript">
function abrePopUpFuncao(){
    var width  = 800;
    var height = 550;
    if(document.frm.inCodBiblioteca.value !="" && document.frm.inCodModulo.value != ""){
    	var sFiltros     = "&inCodBiblioteca="+document.frm.inCodBiblioteca.value+"&inCodModulo="+document.frm.inCodModulo.value+"&campoNum=inCodFuncao&campoNom=stFuncao&nomForm=frm";
    	var sSessao      = "<?=Sessao::getId()?>";
    	var sUrlFrames   = "<?=CAM_GA_ADM_POPUPS;?>funcao/FLBuscarFuncao.php?"+sSessao+sFiltros;
    	window.open( sUrlFrames, "popUpRegistrosEventosFerias", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    } else{
    	 Valida(); 
    }
    
}
function limpaCampoFuncao(){
    document.frm.inCodFuncao.value = "";
    document.getElementById('stFuncao').innerHTML ="&nbsp;";  
}

function buscaValor(tipoBusca){

     var acao = document.frm.action;
     var target = document.frm.target;

     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
     document.frm.target = 'oculto';
     document.frm.submit();

     document.frm.target = target;
     document.frm.action = acao;

}


</script>

<?php
$stValida = <<<HEREDOC
    var expReg = new RegExp(" ");
    if( expReg.test( document.frm.stFuncaoCriada.value ) ){
        erro = true;
        mensagem += "@Nome da Nova Função inválido!(Não pode haver espaços no nome)";
    }
    
HEREDOC;
?>