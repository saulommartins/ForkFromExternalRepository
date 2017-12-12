<?php
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
?>
<?php
/**
    * Página do Frame oculto para Popup de Responsavel
    * Data de Criação   : 01/03/2004

    * @author Diego Barbosa Victoria
    * @author Marcelo B. Paulino

    * @ignore

    * $Id: OCProcurarResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.7  2006/09/15 13:50:41  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"   );

switch ($_GET['stCtrl']) {

    case 'buscaPopup':
         $obRegra = new TCGM;
        if ($_POST[$_GET['stNomCampoCod']] != "") {
            $stFiltro =  " AND CGM.numcgm = ".$_POST[$_GET['stNomCampoCod']];
            $obRegra->recuperaRelacionamentoSintetico( $rsResponsavelTecnico, $stFiltro );
            $stValorRetorno = $rsResponsavelTecnico->getCampo( "nom_cgm" );
        }
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stValorRetorno."')");
    break;

}

?>
