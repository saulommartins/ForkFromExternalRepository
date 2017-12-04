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
  * @author Desenvolvedor: Evandro Melos
  *
  $Id: OCManterTecnicoResponsavel.php 60584 2014-10-31 14:53:54Z michel $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEResponsavelTecnico.class.php';

$stPrograma = "ManterTecnicoResponsavel";
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
    $obTTCEPEResponsavelTecnico = new TTCEPEResponsavelTecnico();

    $arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');
    
    if (count($arTecnicoResponsavel) > 0) {

        $rsTecResponsavel = new RecordSet;
        $rsTecResponsavel->preenche ($arTecnicoResponsavel);
        $rsTecResponsavel->setPrimeiroElemento();

        $obTableTree = new Table;
        $obTableTree->setRecordset       ( $rsTecResponsavel );
        $obTableTree->setSummary         ( 'Lista de Responsável Técnico' );
        $obTableTree->setConditional     ( true );
        $obTableTree->Head->addCabecalho ( 'Entidade' , 20 );
        $obTableTree->Head->addCabecalho ( 'CGM' ,  4 );
        $obTableTree->Head->addCabecalho ( 'Responsável Técnico' , 20 );
        $obTableTree->Head->addCabecalho ( 'Tipo Responsável' , 10 );
        $obTableTree->Head->addCabecalho ( 'CRC' , 8 );
        $obTableTree->Body->addCampo     ( "[nom_entidade]" , 'E');
        $obTableTree->Body->addCampo     ( "[cgm_responsavel]" , 'C');
        $obTableTree->Body->addCampo     ( "[nom_cgm]" , 'E');
        $obTableTree->Body->addCampo     ( "[cod_tipo] - [descricao]" , 'C');
        $obTableTree->Body->addCampo     ( "[crc]" , 'C');

        $obTableTree->Body->addAcao      ( 'alterar', 'executaFuncaoAjax(\'%s\',\'&inId=%s\')',array('alterarResponsavelLista', 'id'));
        $obTableTree->Body->addAcao      ( 'excluir', 'executaFuncaoAjax(\'%s\',\'&inId=%s\')',array('excluirResponsavelLista', 'id'));
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
    $stJs .= "jQuery('#inId').val('');";
    $stJs .= "jQuery('#inCGM').val('');";
    $stJs .= "jQuery('#stCRC').val('');";
    $stJs .= "jQuery('#stDataInicial').val('".date("d/m/Y")."');";    
    $stJs .= "jQuery('#stDataFinal').val('');";
    $stJs .= "jQuery('#stNomCGM').html('&nbsp;');";
    $stJs .= "jQuery('select#inCodEntidade').selectOptions('');";
    $stJs .= "jQuery('select#inCodTipo').selectOptions('');";    
    $stJs .= "jQuery('#inCGM').focus();";

    return $stJs;
}

switch ($stCtrl) {
    case "incluirLista":
        
        $inCodEntidade      = $_REQUEST['inCodEntidade'];
        $stNomEntidade      = $_REQUEST['stNomEntidade'];
        $inNumCgm           = $_REQUEST['inCodCGM'];
        $stNomCgm           = $_REQUEST['stNomCGM'];
        $stCRC              = $_REQUEST['stCRC'];
        $stDescricaoTipo    = explode(' - ', $_REQUEST['inCodTipo']);
        $inCodTipo          = $stDescricaoTipo[0];
        $stDataInicial      = $_REQUEST['stDataInicial'];
        $stDataFinal        = $_REQUEST['stDataFinal'];
        
        if ( $inCodEntidade != '' && $inNumCgm != '' && $inCodTipo != '' && $stDataInicial != '' && $stDataFinal != '' ) {
            if( SistemaLegado::comparaDatas($_REQUEST['stDataInicial'], $_REQUEST['stDataFinal'], true )) {
                echo "alertaAviso('@A data final não pode ser MENOR que a data inicial.','form','erro','".Sessao::getId()."');";
                exit;
            }
            
            $obTTCEPEResponsavelTecnico = new TTCEPEResponsavelTecnico(); 
            $obTTCEPEResponsavelTecnico->recuperaResponsavelTecnico($rsResponsavelTecnico, "WHERE cgm_responsavel = ".$_REQUEST['inCodCGM']);
            
            $arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');

            if (is_array($arTecnicoResponsavel)) {
                foreach ($arTecnicoResponsavel as $arResponsavel) {
                    if ($arResponsavel['cgm_responsavel'] == $inNumCgm) {
                        echo "alertaAviso('@O CGM informado já está na lista de Responsáveis Técnicos.','form','erro','".Sessao::getId()."');";
                        exit;
                    }
                }
            }

            # Inicializa o ID com 0, ou então o total de registros + 1.
            $inId = (count($arTecnicoResponsavel) == 0) ? 0 : count($arTecnicoResponsavel);

            $arTecnicoResponsavel[$inId]['id']              = $inId;
            $arTecnicoResponsavel[$inId]['cod_entidade']    = $inCodEntidade;
            $arTecnicoResponsavel[$inId]['exercicio']       = Sessao::getExercicio();
            $arTecnicoResponsavel[$inId]['cgm_responsavel'] = $inNumCgm;
            $arTecnicoResponsavel[$inId]['nom_cgm']         = $stNomCgm;
            $arTecnicoResponsavel[$inId]['nom_entidade']    = $stNomEntidade;
            $arTecnicoResponsavel[$inId]['cod_tipo']        = $inCodTipo;
            $arTecnicoResponsavel[$inId]['crc']             = $stCRC;
            $arTecnicoResponsavel[$inId]['descricao']       = $stDescricaoTipo[1];
            $arTecnicoResponsavel[$inId]['dt_inicio']       = $stDataInicial;
            $arTecnicoResponsavel[$inId]['dt_fim']          = $stDataFinal;

            Sessao::write('arTecnicoResponsavel',$arTecnicoResponsavel);

            $stJs  =  montarLista();
            $stJs .=  limparLista();
            $stJs .= "alertaAviso('".$stDescricaoTipo[1]." inserido na lista.','','info','".Sessao::getId()."');";

            echo $stJs;

        } else {
           echo "alertaAviso('@Informe todos os campos para incluir um novo Responsável Técnico','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluirResponsavelLista":
        $inCount = 0;

        $arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');

        foreach ($arTecnicoResponsavel as $arResponsavel) {
            if ($arResponsavel["id"] != $_REQUEST["inId"]) {
                $arTmp[$inCount] = $arResponsavel;
                $inCount++;
            }
        }

        Sessao::write('arTecnicoResponsavel',$arTmp);

        $stJs .= "jQuery('#btnIncluir').val('Incluir');";
        $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'incluirLista\');');";

        $stJs .= "alertaAviso('Responsável Técnico excluido da lista.','','info','".Sessao::getId()."');";
        $stJs .= montarLista();
        $stJs .= limparLista();
        
        echo $stJs;
    break;

    case "alterarResponsavelLista":
        $inCount = 0;

        $arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');

        $stJs  = "";
        
        foreach ($arTecnicoResponsavel as $arResponsavel) {
            if ($arResponsavel["id"] == $_REQUEST["inId"]) {
                $stJs .= "jQuery('#inId').val('".$arResponsavel["id"]."');";
                $stJs .= "jQuery('#inCGM').val('".$arResponsavel["cgm_responsavel"]."');";
                $stJs .= "jQuery('#stNomCGM').html('".$arResponsavel["nom_cgm"]."');";
                $stJs .= "jQuery('select#inCodEntidade').val('".$arResponsavel["cod_entidade"]."');";
                $stJs .= "jQuery('#stNomEntidade').val('".$arResponsavel["nom_entidade"]."');";
                $stJs .= "jQuery('select#inCodTipo').val('".$arResponsavel["cod_tipo"]." - ".$arResponsavel["descricao"]."');";
                $stJs .= "jQuery('#stCRC').val('".$arResponsavel["crc"]."');";
                $stJs .= "jQuery('#stDataInicial').val('".$arResponsavel["dt_inicio"]."');";
                $stJs .= "jQuery('#stDataFinal').val('".$arResponsavel["dt_fim"]."');";
                $stJs .= "jQuery('#btnIncluir').val('Alterar');";
                $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'alterarLista\');');";

                break;
            }
        }

        $stJs .= montarLista();
        
        echo $stJs;
    break;

    case "alterarLista":

        $arTecnicoResponsavel = Sessao::read('arTecnicoResponsavel');
        $arTmp = array();

        $inCodEntidade      = $_REQUEST['inCodEntidade'];
        $stNomEntidade      = $_REQUEST['stNomEntidade'];
        $inNumCgm           = $_REQUEST['inCodCGM'];
        $stNomCgm           = $_REQUEST['stNomCGM'];
        $stCRC              = $_REQUEST['stCRC'];
        $stDescricaoTipo    = explode(' - ', $_REQUEST['inCodTipo']);
        $inCodTipo          = $stDescricaoTipo[0];
        $stDataInicial      = $_REQUEST['stDataInicial'];
        $stDataFinal        = $_REQUEST['stDataFinal'];
        
        $stDescricaoTipo = explode(' - ', $_REQUEST['inCodTipo']);
        
        if ( $inCodEntidade != '' && $inNumCgm != '' && $inCodTipo != '' && $stDataInicial != '' && $stDataFinal != '' ) {
            if( SistemaLegado::comparaDatas($_REQUEST['stDataInicial'], $_REQUEST['stDataFinal'], true )) {
                echo "alertaAviso('@A data final não pode ser MENOR que a data inicial.','form','erro','".Sessao::getId()."');";
                exit;
            }
            
            foreach ($arTecnicoResponsavel as $arResponsavelTmp) {
                if ($arResponsavelTmp['cgm_responsavel'] == $inNumCgm && $arResponsavelTmp["id"] != $_REQUEST["inId"]) {
                    echo "alertaAviso('@O CGM informado já está na lista de Responsáveis Técnicos.','form','erro','".Sessao::getId()."');";
                    exit;
                }

                if ($arResponsavelTmp["id"] == $_REQUEST["inId"]) {
                    
                    include_once(TCGM."TCGM.class.php");
                    $obTCGM = new TCGM();
                    $obTCGM->setDado('numcgm', $_REQUEST['inCodCGM']);
                    $obTCGM->recuperaPorChave($rsCGM);
                    
                    $arResponsavelTmp["cod_entidade"]    = $inCodEntidade;
                    $arResponsavelTmp["nom_entidade"]    = $stNomEntidade;
                    $arResponsavelTmp['cgm_responsavel'] = $inNumCgm;
                    $arResponsavelTmp['nom_cgm']         = $rsCGM->getCampo('nom_cgm');
                    $arResponsavelTmp['cod_tipo']        = $stDescricaoTipo[0];
                    $arResponsavelTmp['crc']             = $_REQUEST['stCRC'];
                    $arResponsavelTmp['descricao']       = $stDescricaoTipo[1];
                    $arResponsavelTmp['dt_inicio']       = $stDataInicial;
                    $arResponsavelTmp['dt_fim']          = $stDataFinal;
            
                }
    
                $arTmp[] = $arResponsavelTmp;
            }
        

            Sessao::write('arTecnicoResponsavel',$arTmp);
    
            $stJs .= "jQuery('#btnIncluir').val('Incluir CGM');";
            $stJs .= "jQuery('#btnIncluir').attr('onclick','montaParametrosGET(\'incluirLista\');');";
    
            $stJs .= "alertaAviso('Responsável Técnico alterado na lista.','','info','".Sessao::getId()."');";
    
            $stJs .= montarLista();
            $stJs .= limparLista();
            
            echo $stJs;
        } else {
           echo "alertaAviso('@Informe todos os campos para incluir um novo Responsável Técnico','form','erro','".Sessao::getId()."');";
        }
    break;

    case "limparLista":
        echo limparLista();
    break;

    case 'montarLista':
        echo montarLista();
    break;

}