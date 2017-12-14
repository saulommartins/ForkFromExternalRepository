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
    * Página de Frame Oculto para Consulta de Arrecadacao
    * Data de Criação   : 26/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCConsultarLote.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.2  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

switch ($_REQUEST["stCtrl"]) {

    case "buscaContribuinte":
        $stNull = "&nbsp;";
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inCodContribuinte' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );
            $obRCGM->consultar( $rsCGM );
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inCodContribuinte.value = "";';
                $stJs .= 'f.inCodContribuinte.focus();';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@CGM inválido. (".$_REQUEST['inCodContribuinte'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        } else {
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "preencheAgencia":
        $js .= "f.inNumAgencia.value=''; \n";
        $js .= "limpaSelect(f.cmbAgencia,1); \n";
        $js .= "f.cmbAgencia[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST['inNumBanco']) {
            $obRMONAgencia = new RMONAgencia;
            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
            $obRMONAgencia->listarAgencia( $rsAgencia );

            $inContador = 1;
            while ( !$rsAgencia->eof() ) {
                $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );
                $js .= "f.cmbAgencia.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                $inContador++;
                $rsAgencia->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inNumAgencia.value='".$_REQUEST["inNumAgencia"]."'; \n";
            $js .= "f.cmbAgencia.options[".$_REQUEST["inNumAgencia"]."].selected = true; \n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
}

?>
