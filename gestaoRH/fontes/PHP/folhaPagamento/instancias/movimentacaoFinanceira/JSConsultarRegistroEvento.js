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
* Página de javascript do Gerar Assentamento
* Data de Criação   : 19/01/2006


* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: rgarbin $
$Date: 2008-03-31 11:26:27 -0300 (Seg, 31 Mar 2008) $

* Casos de uso: uc-04.05.07
*/

/*
$Log$
Revision 1.4  2006/08/08 17:43:13  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function abrePopUpRegistrosEventos() {
    var width  = 800;
    var height = 550;
            
    var sFiltros     = "&inContrato="+document.getElementById("inContrato").value+"&inCodMes="+document.frm.inCodMes.value+"&inAno="+document.frm.inAno.value;
    var sUrlConsulta = "LSConsultarRegistroEvento.php?";
    var sSessao      = "<?=Sessao::getId()?>";
    var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FRConsultarRegistroEvento.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
    if (Valida()) {
        window.open( sUrlFrames, "popUpRegistrosEventos", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    }
}

function limpaForm() {
    document.frm.reset();
    buscaValor("montaSpanContrato");
}
</script>
