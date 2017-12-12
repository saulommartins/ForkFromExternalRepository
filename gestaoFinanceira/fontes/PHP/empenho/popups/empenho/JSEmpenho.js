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
    * Arquivo JS
    * Data de Criação   : 30/03/2005


    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-01-30 15:13:54 -0200 (Qua, 30 Jan 2008) $

    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.4  2006/07/05 20:49:29  cleisson
Adicionada tag Log aos arquivos

*/
?>

<script type="text/javascript">

function insere(inCodEmpenho,stBeneficiario){
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = inCodEmpenho;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = stBeneficiario;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.close();
}
function insereEmpenhoExercicio  (inCodEmpenho,stBeneficiario,stExercicio){
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = inCodEmpenho;
    window.opener.parent.frames['telaPrincipal'].document.getElementById('<?=$_REQUEST["campoNom"]?>').innerHTML = stBeneficiario;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.value = stExercicio;
    window.opener.parent.frames['telaPrincipal'].document.<?=$_REQUEST['nomForm']?>.<?=$_REQUEST['campoNum']?>.focus();
    window.close();
}


</script>
