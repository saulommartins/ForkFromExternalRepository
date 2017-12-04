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

/**
  * Pacote de configuração do TCEPE
  * Data de Criação: 01/10/2014
  * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
  *
  $Id: $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEAgentePolitico.class.php';

$stPrograma = "ManterAgentePolitico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

function montarLista()
{
    $obTTCEPEAgentePolitico = new TTCEPEAgentePolitico();

    $arAgentePolitico = Sessao::read('arAgentes');

    if (count($arAgentePolitico) > 0) {

        $rsAgentePolitico = new RecordSet;
        $rsAgentePolitico->preenche ($arAgentePolitico);
        $rsAgentePolitico->setPrimeiroElemento();

        $obTableTree = new Table;
        $obTableTree->setRecordset       ( $rsAgentePolitico );
        $obTableTree->setSummary         ( 'Lista de Agentes Políticos' );
        $obTableTree->setConditional     ( true );
        $obTableTree->Head->addCabecalho ( 'Entidade' , 20 );
        $obTableTree->Head->addCabecalho ( 'CGM' , 20 );
        $obTableTree->Head->addCabecalho ( 'Agente Político' , 8 );
        $obTableTree->Body->addCampo     ( "[nom_entidade]" , 'E');
        $obTableTree->Body->addCampo     ( "[num_cgm] - [nom_cgm]" , 'E');
        $obTableTree->Body->addCampo     ( "[nom_agente_politico]" , 'C');
        $obTableTree->Body->addAcao      ( 'alterar', 'executaFuncaoAjax(\'%s\',\'&inNumCGM=%s\')',array('alterarAgenteLista', 'num_cgm'));
        $obTableTree->Body->addAcao      ( 'excluir', 'executaFuncaoAjax(\'%s\',\'&inNumCGM=%s\')',array('excluirAgenteLista', 'num_cgm'));
        $obTableTree->montaHTML          ( true );

        $stHTML = $obTableTree->getHtml();

        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\\'","\\'",$stHTML );

        $stJs .= "jQuery('#spnLista').html('".$stHTML."'); \n";
    } else {
        $stJs .= "jQuery('#spnLista').html(''); \n";
    }
    

    return $stJs;
}

function limparLista() {
    $stJs  = "";
    $stJs .= "jQuery('#inCGM').val('');";
    $stJs .= "jQuery('#stNomCGM').html('&nbsp;');";
    $stJs .= "jQuery('select#inCodAgentePolitico').selectOptions('');";
    $stJs .= "jQuery('#inCGM').focus();";

    return $stJs;
}

switch ($stCtrl) {
    case "incluirLista":
        
        $inCodEntidade       = $_REQUEST['inCodEntidade'];
        $stNomEntidade       = $_REQUEST['stNomEntidade'];
        $inNumCgm            = $_REQUEST['inCodCGM'];
        $stNomCgm            = $_REQUEST['stNomCGM'];
        $inCodAgentePolitico = $_REQUEST['inCodAgentePolitico'];
        $stNomAgentePolitico = $_REQUEST['stNomAgentePolitico'];

        if ($inCodEntidade != '' && $inNumCgm != '' && $inCodAgentePolitico != '') {
            $arAgentePolitico = Sessao::read('arAgentes');

            if (is_array($arAgentePolitico)) {
                foreach ($arAgentePolitico as $arAgentesTmp) {
                    if ($arAgentesTmp['num_cgm'] == $inNumCgm) {
                        echo "alertaAviso('@O CGM informado já está na lista de Agentes Políticos.','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            # Inicializa o ID com 0, ou então o total de registros + 1.
            $inId = (count($arAgentePolitico) == 0) ? 0 : count($arAgentePolitico);

            $arAgentePolitico[$inId]['id']           = $inId;
            $arAgentePolitico[$inId]['cod_entidade'] = $inCodEntidade;
            $arAgentePolitico[$inId]['exercicio']    = Sessao::getExercicio();
            $arAgentePolitico[$inId]['num_cgm']      = $inNumCgm;
            $arAgentePolitico[$inId]['nom_cgm']      = $stNomCgm;
            $arAgentePolitico[$inId]['nom_entidade'] = $stNomEntidade;
            $arAgentePolitico[$inId]['cod_agente_politico'] = $inCodAgentePolitico;
            $arAgentePolitico[$inId]['nom_agente_politico'] = $stNomAgentePolitico;

            Sessao::write('arAgentes',$arAgentePolitico);

            $stJs  =  montarLista();
            $stJs .=  limparLista();
            $stJs .= "alertaAviso('Agente Político inserido na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Informe todos os campos para incluir um novo Agente Político.','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirAgenteLista":
        $inCount = 0;

        $arAgentePolitico = Sessao::read('arAgentes');

        foreach ($arAgentePolitico as $arAgentesTmp) {
            if ($arAgentesTmp["num_cgm"] != $_REQUEST["inNumCGM"]) {
                $arTmp[$inCount] = $arAgentesTmp;
                $inCount++;
            }
        }

        Sessao::write('arAgentes',$arTmp);

        $stJs .= "jQuery('#btnIncluir').val('Incluir CGM');";
        $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'incluirLista\');');";
        $stJs .= "jQuery('#inCGM').removeAttr('readonly');";
        $stJs .= "jQuery('#imgBuscaCGM').css('visibility', 'visible');";

        $stJs .= "alertaAviso('Agente Político excluido da lista.','','info','".Sessao::getId()."');";
        $stJs .= montarLista();
        $stJs .= limparLista();
        
        echo $stJs;
    break;

    case "alterarAgenteLista":
        $inCount = 0;

        $arAgentePolitico = Sessao::read('arAgentes');

        $stJs  = "";
        
        foreach ($arAgentePolitico as $arAgentesTmp) {
            if ($arAgentesTmp["num_cgm"] == $_REQUEST["inNumCGM"]) {
                $stJs .= "jQuery('#inCGM').val('".$arAgentesTmp["num_cgm"]."');";
                $stJs .= "jQuery('#stNomCGM').html('".$arAgentesTmp["nom_cgm"]."');";
                $stJs .= "jQuery('#inCGM').attr('readonly', 'readonly');";
                $stJs .= "jQuery('#imgBuscaCGM').css('visibility', 'hidden');";
                $stJs .= "jQuery('select#inCodEntidade').val('".$arAgentesTmp["cod_entidade"]."');";
                $stJs .= "jQuery('#stNomEntidade').val('".$arAgentesTmp["nom_entidade"]."');";
                $stJs .= "jQuery('select#inCodAgentePolitico').val('".$arAgentesTmp["cod_agente_politico"]."');";
                $stJs .= "jQuery('#stNomAgentePolitico').val('".$arAgentesTmp["nom_agente_politico"]."');";
                $stJs .= "jQuery('#btnIncluir').val('Alterar CGM');";
                $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'alterarLista\');');";

                break;
            }
        }

        $stJs .= montarLista();
        
        echo $stJs;
    break;

    case "alterarLista":

        $arAgentePolitico = Sessao::read('arAgentes');
        $arTmp = array();

        foreach ($arAgentePolitico as $arAgentesTmp) {
            if ($arAgentesTmp["num_cgm"] == $_REQUEST["inCodCGM"]) {
                $arAgentesTmp["cod_entidade"] = $_REQUEST['inCodEntidade'];
                $arAgentesTmp["nom_entidade"] = $_REQUEST['stNomEntidade'];
                $arAgentesTmp["cod_agente_politico"] = $_REQUEST['inCodAgentePolitico'];
                $arAgentesTmp["nom_agente_politico"] = $_REQUEST['stNomAgentePolitico'];
            }

            $arTmp[] = $arAgentesTmp;
        }

        Sessao::write('arAgentes',$arTmp);

        $stJs .= "jQuery('#btnIncluir').val('Incluir CGM');";
        $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'incluirLista\');');";
        $stJs .= "jQuery('#inCGM').removeAttr('readonly');";
        $stJs .= "jQuery('#imgBuscaCGM').css('visibility', 'visible');";

        $stJs .= "alertaAviso('Agente Político alterado na lista.','','info','".Sessao::getId()."');";

        $stJs .= montarLista();
        $stJs .= limparLista();
        
        echo $stJs;
    break;

    case "limparLista":
        echo limparLista();
    break;

    case 'montarLista':
        echo montarLista();
    break;

}