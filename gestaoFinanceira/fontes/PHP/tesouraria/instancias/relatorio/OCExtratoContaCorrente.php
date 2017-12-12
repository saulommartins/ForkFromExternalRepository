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

    * Página de Formulario de Seleção de Impressora para Relatorio  C/c
    * Data de Criação   : 21/07/2014
    *
    * @author Desenvolvedor: Carolina Schwaab Marçal
    *
    * @ignore
    *
    * $id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioExtratoContaCorrente.class.php"  );
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamento.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoBanco.class.php";
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
$obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );

switch ($stCtrl) {
    case "MontaAgencia":
            if ($_REQUEST["inNumBanco"] != '') {
                $stSelecionado = $_REQUEST['inNumAgencia'];
                $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
                $stJs .= "f.inNumAgencia.value=''; \n";
                $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";

                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
                $stJs .= "f.inCodBanco.value = '".$rsBanco->getCampo('cod_banco')."';\n";

                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);

                $inCount = 0;
                while (!$rsCombo->eof()) {
                    $inCount++;
                    $inId   = $rsCombo->getCampo("num_agencia");
                    $stDesc = $rsCombo->getCampo("nom_agencia");
                    if( $stSelecionado == $inId )
                        $stSelected = 'selected';
                    else
                        $stSelected = '';
                    $stJs .= "f.stNomeAgencia.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                    $rsCombo->proximo();
                }

                $stJs .= "limpaSelect(f.stContaCorrente, 1);";
            } else {
                $stJs .= "limpaSelect(f.stNomeAgencia,0); \n";
                $stJs .= "f.inNumAgencia.value=''; \n";
                $stJs .= "f.stNomeAgencia.options[0] = new Option('Selecione','', 'selected');\n";
                $stJs .= "limpaSelect(f.stContaCorrente,0);";
                $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
            }
            echo $stJs;
        break;
        case "MontaContaCorrente":
            if ($_REQUEST["inNumAgencia"] != '') {

                $obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
                $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST["inCodBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->setNumAgencia( $_REQUEST["inNumAgencia"] );
                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
                $stJs .= "f.inCodAgencia.value = '".$rsCombo->getCampo('cod_agencia')."';\n";

                $obRContabilidadePlanoBanco->consultar();

                $stCombo  = "stContaCorrente";
                $stSelecionado = $_REQUEST['stContaCorrente'];
                $stJs .= "limpaSelect(f.$stCombo,0); \n";
                $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

                include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                $obRMONContaCorrente = new RMONContaCorrente();
                $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );

                $rsCCorrente = new RecordSet();
                $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );

                $inCount - 0;
                while ( !$rsCCorrente->eof() ) {
                    $inCount++;
                    $inId = $rsCCorrente->getCampo("num_conta_corrente");
                    $stDesc = $rsCCorrente->getCampo("num_conta_corrente");
                    if ($stSelecionado == $inId) {
                        $stSelected = 'selected';
                    } else {
                        $stSelected = '';
                    }
                    $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                    $rsCCorrente->proximo();
                }

              
            } else {
                $stJs .= "limpaSelect(f.stContaCorrente,0);";
                $stJs .= "f.stContaCorrente.options[0] = new Option('Selecione','', 'selected');\n";
            }
            echo $stJs;
        break;

        case "limpaCombo2":
            $stJs .= "limpaSelect('f.stNomeAgencia',0);\n";
            SistemaLegado::executaFrameOculto($stJs);
        break;

        case "BuscaContaCorrente":

                if ($_REQUEST['inCodBanco']) {

                    
                    $obTContabilidadePlanoBanco = new TContabilidadePlanoBanco;
                    $obTContabilidadePlanoBanco->setDado( 'cod_banco'	 	, $_REQUEST['inCodBanco']);
                    $obTContabilidadePlanoBanco->setDado( 'cod_agencia'	 	, $_REQUEST['inCodAgencia']);
                    $obTContabilidadePlanoBanco->setDado( 'conta_corrente'	, $_REQUEST['stContaCorrente']);
                    $obTContabilidadePlanoBanco->setDado( 'exercicio'	 	, Sessao::getExercicio());

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.1' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.2%' or cod_estrutural like '1.1.1.1.3%' ) ";
                    }

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.2' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.2%' ) ";
                    }

                    if ( substr( $_REQUEST['stCodClass'],0,9 ) == '1.1.1.1.3' ) {
                        $stFiltro = " AND ( cod_estrutural like '1.1.1.1.1%' or cod_estrutural like '1.1.1.1.3%' ) ";
                    }

                    $obTContabilidadePlanoBanco->listarPorEstrutural( $rsPlanoBanco , $stFiltro );

                      $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );

                        include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                        $obRMONContaCorrente = new RMONContaCorrente();
                        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
                        $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
                        $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $_REQUEST['inCodAgencia'] );
                        $obRMONContaCorrente->setNumeroConta( $_REQUEST['stContaCorrente']);
                        $obRMONContaCorrente->consultarContaCorrente( $rsCCorrente, $obTransacao );

                        $stJs .= "f.inContaCorrente.value = ".$rsCCorrente->getCampo( 'cod_conta_corrente')."; \n";
                        $stJs .= "alertaAviso('','','','".Sessao::getId()."'); \n";
             
                }
                echo $stJs;
        break;
        
    default :
        $obRRelatorio                          = new RRelatorio;
        $obRTesourariaRelatorioExtratoContaCorrente = new RTesourariaRelatorioExtratoContaCorrente;

        $stEntidade = "";
        $arFiltro = Sessao::read('filtroRelatorio');

        foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $key => $valor) {
            $stEntidade.= $valor . ",";
        }

        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );

        $arCodPlano = array ();
        if ($arFiltro['inCodContaBancoInicial']) {
            $arCodPlano[] = $arFiltro['inCodContaBancoInicial'];
        }
        if ($arFiltro['inCodContaBancoFinal']) {
            $arCodPlano[] = $arFiltro['inCodContaBancoFinal'];
        }


        $obRTesourariaRelatorioExtratoContaCorrente->setCodPlano ( $arCodPlano );
        $obRTesourariaRelatorioExtratoContaCorrente->setExercicio($arFiltro['stExercicio']);
        $obRTesourariaRelatorioExtratoContaCorrente->setEntidade($stEntidade);
        $obRTesourariaRelatorioExtratoContaCorrente->setDataInicial($arFiltro['stDataInicial']);
        $obRTesourariaRelatorioExtratoContaCorrente->setDataFinal($arFiltro['stDataFinal']);
        $obRTesourariaRelatorioExtratoContaCorrente->setCodBanco($arFiltro['inCodBanco']);
        $obRTesourariaRelatorioExtratoContaCorrente->setCodAgencia($arFiltro['inCodAgencia']);
        $obRTesourariaRelatorioExtratoContaCorrente->setContaCorrente($arFiltro['stContaCorrente']);
        $obRTesourariaRelatorioExtratoContaCorrente->setCodRecurso($arFiltro['inCodRecurso']);
        $obRTesourariaRelatorioExtratoContaCorrente->boImprimeContasSemMov = ($arFiltro['stImprimirSemMovimentacao'] == 'sim');
        $obRTesourariaRelatorioExtratoContaCorrente->boDemonstrarCredor = ($arFiltro['stDemonstrarCredor'] == 'sim');
        $obRTesourariaRelatorioExtratoContaCorrente->geraRecordSet( $rsExtratoBancario );

        Sessao::write('arDados', $rsExtratoBancario);

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioExtratoContaCorrente.php" );

}




?>
