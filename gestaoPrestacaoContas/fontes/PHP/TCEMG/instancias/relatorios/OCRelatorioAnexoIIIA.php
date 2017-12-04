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
    * Oculto de Relatório de Demonstrativo da Aplicação nas Ações e Serviços Púb. de Saúde
    * Data de Criação   : 25/07/2014
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    * @ignore
    *   
    * $Id:
*/

include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'     );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php'  );

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

function MontaUnidade($inCodOrgao)
{        
    include_once    ( CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php' );
    $obROrcamentoDespesa = new ROrcamentoDespesa;
    if( $inCodOrgao ){
        $stJs .= "jq('#inCodUnidade').empty().append('<option value=\"\">Selecione</option>');";

        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inCodOrgao);
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
        $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->listar( $rsCombo, " ORDER BY num_unidade");
        $inCount = 0;

        while(!$rsCombo->eof()){
            $inCount++;
            $inId   = $rsCombo->getCampo("num_unidade");
            $stDesc = $rsCombo->getCampo("nom_unidade");

            $stJs .= "jq('#inCodUnidade').append(jq('<option>', {value: ".$inId.", html: '".$stDesc."'})); \n";
            $rsCombo->proximo();
        }
    }
    $stJs .= $js;

    return $stJs;
}

switch( $stCtrl ){

    case "MontaUnidade":
        $stJs = "";
        if( $_REQUEST["inCodOrgao"] ){
            $stJs .= MontaUnidade($_REQUEST["inCodOrgao"]);
        }

        echo $stJs;
    break;
}
?>
