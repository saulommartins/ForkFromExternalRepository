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
    * JavaScript
    * Data de Criação: 24/01/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-05-16 12:30:39 -0300 (Qua, 16 Mai 2007) $

    * Casos de uso: uc-04.05.10
*/

/*
$Log$
Revision 1.4  2007/05/16 15:29:03  souzadl
alterado

Revision 1.3  2006/10/04 16:47:52  vandre
Complementação.
Adicionada nova opção para consulta após cálculo.

Revision 1.2  2006/08/08 17:43:00  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValorFiltro(tipoBusca){
     target = document.frm.target ;
     action = document.frm.action;
     document.frm.stCtrl.value = tipoBusca;
     document.frm.target = 'oculto';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = action;
     document.frm.target = target;
}

function excluirDado( stAcao, inId ){
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId='+inId ;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function processarPopUp(inCodComplementar,inCodContrato,inRegistro,stNumCgm,stNomCgm){
	var width  = 800;
    var height = 550;
    var sFiltros     = "&inCodComplementar="+inCodComplementar+"&inCodContrato="+inCodContrato+"&inRegistro="+inRegistro+"&inCodConfiguracao=0&nom_cgm="+stNomCgm+"&numcgm="+stNumCgm;
    var sUrlConsulta = "FMConsultarFichaFinanceira.php?";
    var sSessao      = "<?=Sessao::getId()?>";
    var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FRConsultarFichaFinanceira.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
    if( Valida() ){
        window.open( sUrlFrames, "popUpConsultaFichaFinanceira", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    }	
}

</script>
