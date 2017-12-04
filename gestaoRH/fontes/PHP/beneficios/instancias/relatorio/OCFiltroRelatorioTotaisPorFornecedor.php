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
* Página de relatório de Totais por Fornecedor
* Data de Criação   : 22/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                       );
include_once( CAM_GRH_BEN_NEGOCIO."RRelatorioTotaisPorFornecedor.class.php"                           );
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php"                                );

function preencheItinerario()
{
    $stJs .= "limpaSelect(f.ItinerarioDisponiveis,0); \n";
    $stJs .= "limpaSelect(f.ItinerarioSelecionados,0); \n";

    $obRBeneficioValeTransporte = new RBeneficioValeTransporte;

    if ($_POST['FornecedorSelecionados']) {
        $obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->setNumCGM( implode(',', $_POST['FornecedorSelecionados']) );
        $obRBeneficioValeTransporte->obRBeneficioItinerario->listarItinerario($rsItinerario);

        while (!$rsItinerario->eof()) {
            $stOption  = $rsItinerario->getCampo('municipio_origem') ."/".$rsItinerario->getCampo('linha_origem')." - ";
            $stOption .= $rsItinerario->getCampo('municipio_destino')."/".$rsItinerario->getCampo('linha_destino');
            $stValue   = $rsItinerario->getCampo('vale_transporte_cod_vale_transporte');
            $stJs .= "f.ItinerarioDisponiveis[f.ItinerarioDisponiveis.length] = new Option('".$stOption."','".$stValue."'); \n";
            $rsItinerario->proximo();
        }
        unset($rsItinerario);
    }

    return $stJs;
}

//echo $_POST['stCtrl'];
switch ($_POST["stCtrl"]) {
    case "preencheItinerario":
        $stJs .= preencheItinerario();
    break;
}

if ($stJs) {
    SistemaLegado::ExecutaFrameOculto($stJs);
}

?>
