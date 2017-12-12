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
    * Página de Formulário para configuração
    * Data de Criação   : 31/01/2012

    * @author Carlos Adriano

    * @ignore

    * $Id: OCManterConfiguracaoDividaConsolidada.php 45121 2011-01-27 19:52:49Z silvia $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioBeneficiario.class.php");
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioModalidadeConvenioMedico.class.php");
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioTipoConvenioMedico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterBeneficiario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl         = $_REQUEST['stCtrl'];
$arBeneficiario = Sessao::read('arBeneficiario');

switch ($stCtrl) {

    case 'buscaBeneficiario':
        buscaBeneficiario($_REQUEST['inContrato']);
    break;

    case 'incluiBeneficiario' :
        $arBeneficiario = Sessao::read('arBeneficiario');
        $stMensagem     = executaValidacao($_REQUEST);

        $arElementos = array();
        if ($stMensagem == "") {

            $arTmp['id']                = count($arBeneficiario);
            $arTmp['inContrato']        = SistemaLegado::pegaDado('cod_contrato', 'pessoal.contrato',  'WHERE registro ='.$_REQUEST['inContrato']);
            $arTmp['inCGMBeneficiario'] = $_REQUEST['inCGMBeneficiario'];
            $arTmp['stCGMBeneficiario'] = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='. $_REQUEST['inCGMBeneficiario']);
            $arTmp['inCGMFornecedor']   = $_REQUEST['inCGMFornecedor'];
            $arTmp['inModalidade']      = $_REQUEST['inModalidade'];
            $arTmp['inTipo']            = $_REQUEST['inTipo'];
            $arTmp['stTipo']            = SistemaLegado::pegaDado('descricao', 'beneficio.tipo_convenio_medico',  'WHERE cod_tipo_convenio ='.$_REQUEST['inTipo']);
            $arTmp['inCodUsuario']      = $_REQUEST['inCodUsuario'];
            $arTmp['inGrauParentesco']  = $_REQUEST['inGrauParentesco'];
            $arTmp['stGrauParentesco']  = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$_REQUEST['inGrauParentesco']);
            $arTmp['dtInicioBeneficio'] = $_REQUEST['dtInicioBeneficio'];
            $arTmp['dtFimBeneficio']    = $_REQUEST['dtFimBeneficio'];
            $arTmp['vlDesconto']        = $_REQUEST['vlDesconto'];

            $arBeneficiario[] = $arTmp;

            Sessao::write('arBeneficiario', $arBeneficiario);
            echo montaLista($arBeneficiario);

        } else {
           echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }

    break;

    case 'montaAlteracaoLista':
        $arBeneficiario = $arBeneficiario[$_REQUEST['id']];

        $stJs = "document.getElementById('hdnId').value = '".$_REQUEST['id']."';";
        $stJs.= "document.getElementById('inCGMBeneficiario').value = '".$arBeneficiario['inCGMBeneficiario']."';";
        $stJs.= "document.getElementById('stCGMBeneficiario').innerHTML = '".SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$arBeneficiario['inCGMBeneficiario'])."';";
        $stJs.= "document.getElementById('inCGMFornecedor').value = '".$arBeneficiario['inCGMFornecedor']."';";
        $stJs.= "document.getElementById('stCGMFornecedor').innerHTML = '".SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$arBeneficiario['inCGMFornecedor'])."';";
        $stJs.= "document.getElementById('inModalidade').value = '".$arBeneficiario['inModalidade']."';";
        $stJs.= "document.getElementById('inTipo').value = '".$arBeneficiario['inTipo']."';";
        $stJs.= "document.getElementById('inCodUsuario').value = '".$arBeneficiario['inCodUsuario']."';";
        $stJs.= "document.getElementById('inGrauParentesco').value = '".$arBeneficiario['inGrauParentesco']."';";
        $stJs.= "document.getElementById('dtInicioBeneficio').value = '".$arBeneficiario['dtInicioBeneficio']."';";
        $stJs.= "document.getElementById('dtFimBeneficio').value = '".$arBeneficiario['dtFimBeneficio']."';";
        $stJs.= "document.getElementById('vlDesconto').value = '".$arBeneficiario['vlDesconto']."';";

        $stJs.= "document.getElementById('btIncluir').value = 'Alterar';";
        $stJs.= "document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'alterarListaItens\', \'hdnId,inContrato,inCGMBeneficiario,inCGMFornecedor,inModalidade,inTipo,inCodUsuario,inGrauParentesco,dtInicioBeneficio,dtFimBeneficio,vlDesconto\' );');";
        echo $stJs;

    break;

    case 'alterarListaItens':
        $stMensagem = executaValidacao($_REQUEST);
        $inCount    = 0;

        if ($stMensagem == "") {
            foreach ($arBeneficiario as $key => $value) {
                if ($_REQUEST['hdnId'] == $value['id']) {

                    $arBeneficiario[$inCount]['id']                = $_REQUEST['hdnId'];
                    $arBeneficiario[$inCount]['inContrato']        = SistemaLegado::pegaDado('cod_contrato', 'pessoal.contrato', 'WHERE registro ='.$_REQUEST['inContrato']);
                    $arBeneficiario[$inCount]['inCGMBeneficiario'] = $_REQUEST['inCGMBeneficiario'];
                    $arBeneficiario[$inCount]['stCGMBeneficiario'] = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$_REQUEST['inCGMBeneficiario']);
                    $arBeneficiario[$inCount]['inCGMFornecedor']   = $_REQUEST['inCGMFornecedor'];
                    $arBeneficiario[$inCount]['stCGMFornecedor']   = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$_REQUEST['inCGMFornecedor']);
                    $arBeneficiario[$inCount]['inModalidade']      = $_REQUEST['inModalidade'];
                    $arBeneficiario[$inCount]['inTipo']            = $_REQUEST['inTipo'];
                    $arBeneficiario[$inCount]['stTipo']            = SistemaLegado::pegaDado('descricao', 'beneficio.tipo_convenio_medico',  'WHERE cod_tipo_convenio ='.$_REQUEST['inTipo']);
                    $arBeneficiario[$inCount]['inCodUsuario']      = $_REQUEST['inCodUsuario'];
                    $arBeneficiario[$inCount]['inGrauParentesco']  = $_REQUEST['inGrauParentesco'];
                    $arBeneficiario[$inCount]['stGrauParentesco']  = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$_REQUEST['inGrauParentesco']);
                    $arBeneficiario[$inCount]['dtInicioBeneficio'] = $_REQUEST['dtInicioBeneficio'];
                    $arBeneficiario[$inCount]['dtFimBeneficio']    = $_REQUEST['dtFimBeneficio'];
                    $arBeneficiario[$inCount]['vlDesconto']        = $_REQUEST['vlDesconto'];
                }

                $inCount++;
            }

            Sessao::write('arBeneficiario', $arBeneficiario);

            echo 'limparBeneficiario();';
            echo montaLista( $arBeneficiario );

            $stJs = "document.getElementById('btIncluir').value = 'Incluir';";
            $stJs.= "document.getElementById('btIncluir').setAttribute('onClick','montaParametrosGET( \'incluiBeneficiario\', \'inContrato,inCGMBeneficiario,inCGMFornecedor,inModalidade,inTipo,inCodUsuario,inGrauParentesco,dtInicioBeneficio,dtFimBeneficio,vlDesconto\' );');";
            echo $stJs;
        } else {
            echo "alertaAviso('".$stMensagem."!','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'excluirListaItens':
        $arTmp = $arTmpRemove = array();
        $arBeneficiarioRemovidos = Sessao::read('arBeneficiarioRemovidos');
        $inCount = 0;
        
        foreach ($arBeneficiario as $key => $value) {
            if ($value['id'] != $_REQUEST['id']) {

                $arTmp[$inCount]['id']                = $inCount;
                $arTmp[$inCount]['inContrato']        = $value['inContrato'];
                $arTmp[$inCount]['inCGMBeneficiario'] = $value['inCGMBeneficiario'];
                $arTmp[$inCount]['stCGMBeneficiario'] = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$value['inCGMBeneficiario']);
                $arTmp[$inCount]['inCGMFornecedor']   = $value['inCGMFornecedor'];
                $arTmp[$inCount]['stCGMFornecedor']   = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$value['inCGMFornecedor']);
                $arTmp[$inCount]['inModalidade']      = $value['inModalidade'];
                $arTmp[$inCount]['inTipo']            = $value['inTipo'];
                $arTmp[$inCount]['stTipo']            = SistemaLegado::pegaDado('descricao', 'beneficio.tipo_convenio_medico',  'WHERE cod_tipo_convenio ='.$value['inTipo']);
                $arTmp[$inCount]['inCodUsuario']      = $value['inCodUsuario'];
                $arTmp[$inCount]['inGrauParentesco']  = $value['inGrauParentesco'];
                $arTmp[$inCount]['stGrauParentesco']  = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$value['inGrauParentesco']);
                $arTmp[$inCount]['dtInicioBeneficio'] = $value['dtInicioBeneficio'];
                $arTmp[$inCount]['dtFimBeneficio']    = $value['dtFimBeneficio'];
                $arTmp[$inCount]['vlDesconto']        = $value['vlDesconto'];

                $inCount++;
            } else {
                $arTmpRemove['id']                = $inCount;
                $arTmpRemove['inContrato']        = $value['inContrato'];
                $arTmpRemove['inCGMBeneficiario'] = $value['inCGMBeneficiario'];
                $arTmpRemove['stCGMBeneficiario'] = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$value['inCGMBeneficiario']);
                $arTmpRemove['inCGMFornecedor']   = $value['inCGMFornecedor'];
                $arTmpRemove['stCGMFornecedor']   = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='.$value['inCGMFornecedor']);
                $arTmpRemove['inModalidade']      = $value['inModalidade'];
                $arTmpRemove['inTipo']            = $value['inTipo'];
                $arTmpRemove['stTipo']            = SistemaLegado::pegaDado('descricao', 'beneficio.tipo_convenio_medico',  'WHERE cod_tipo_convenio ='.$value['inTipo']);
                $arTmpRemove['inCodUsuario']      = $value['inCodUsuario'];
                $arTmpRemove['inGrauParentesco']  = $value['inGrauParentesco'];
                $arTmpRemove['stGrauParentesco']  = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$value['inGrauParentesco']);
                $arTmpRemove['dtInicioBeneficio'] = $value['dtInicioBeneficio'];
                $arTmpRemove['dtFimBeneficio']    = $value['dtFimBeneficio'];
                $arTmpRemove['vlDesconto']        = $value['vlDesconto'];
            }
        }
        $arBeneficiarioRemovidos[count($arBeneficiarioRemovidos)] = $arTmpRemove;

        Sessao::write('arBeneficiario', $arTmp);
        Sessao::write('arBeneficiarioRemovidos', $arBeneficiarioRemovidos);
        echo montaLista( $arTmp );
    break;

    case 'buscaBeneficiarios':
        $arTemp  = array();
        $inCount = 0;
        $rsBeneficiario = new RecordSet;

        $obTBeneficioBeneficiario = new TBeneficioBeneficiario();
        $inCodContrato = SistemaLegado::pegaDado('cod_contrato', 'pessoal.contrato', 'WHERE registro = '.$_REQUEST['inContrato']);
        $stFiltro = " WHERE cod_contrato =".$inCodContrato."\n";
        $stFiltro .= " AND timestamp = (SELECT MAX(timestamp) FROM beneficio.beneficiario AS BB WHERE BB.cod_contrato=beneficiario.cod_contrato AND BB.codigo_usuario=beneficiario.codigo_usuario ) \n";
        $stFiltro .= " AND timestamp_excluido is NULL \n";
        $obTBeneficioBeneficiario->recuperaTodos($rsBeneficiario, $stFiltro );

        foreach ($rsBeneficiario->arElementos as $arValue) {
                $arBeneficiario[$inCount]['id']                = $inCount;
                $arBeneficiario[$inCount]['inContrato']        = $arValue['cod_contrato'];
                $arBeneficiario[$inCount]['inCGMBeneficiario'] = $arValue['cgm_beneficiario'];
                $arBeneficiario[$inCount]['stCGMBeneficiario'] = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm',  'WHERE numcgm ='. $arValue['cgm_beneficiario']);
                $arBeneficiario[$inCount]['inCGMFornecedor']   = $arValue['cgm_fornecedor'];
                $arBeneficiario[$inCount]['inModalidade']      = $arValue['cod_modalidade'];
                $arBeneficiario[$inCount]['inTipo']            = $arValue['cod_tipo_convenio'];
                $arBeneficiario[$inCount]['stTipo']            = SistemaLegado::pegaDado('descricao', 'beneficio.tipo_convenio_medico',  'WHERE cod_tipo_convenio ='. $arValue['cod_tipo_convenio']);
                $arBeneficiario[$inCount]['inCodUsuario']      = $arValue['codigo_usuario'];
                $arBeneficiario[$inCount]['inGrauParentesco']  = $arValue['grau_parentesco'];
                $arBeneficiario[$inCount]['stGrauParentesco']  = SistemaLegado::pegaDado('nom_grau', 'cse.grau_parentesco',  'WHERE cod_grau ='.$arValue['grau_parentesco']);
                $arBeneficiario[$inCount]['dtInicioBeneficio'] = $arValue['dt_inicio'];
                $arBeneficiario[$inCount]['dtFimBeneficio']    = $arValue['dt_fim'];
                $arBeneficiario[$inCount]['vlDesconto']        = number_format($arValue['valor'], '2', ',', '');

            $inCount++;
        }

        Sessao::write('arBeneficiario', $arBeneficiario);
        echo '<script>'.montaLista( $arBeneficiario ).'</script>';
        
    break;
}

function montaLista($arBeneficiario)
{
    $rsBeneficiario = new RecordSet();
    $rsBeneficiario->preenche( $arBeneficiario  );

    $obTable = new Table();
    $obTable->setRecordSet( $rsBeneficiario );
    $obTable->setSummary('Lista de beneficiários');

    //$obTable->setConditional( true , "#efefef" );

    $obTable->Head->addCabecalho( 'CGM' , 18 );
    $obTable->Head->addCabecalho( 'Código de usuário' , 7 );
    $obTable->Head->addCabecalho( 'Grau de parentesco' , 7 );
    $obTable->Head->addCabecalho( 'Tipo de convênio' , 18 );
    $obTable->Head->addCabecalho( 'Início Benefício' , 5 );
    $obTable->Head->addCabecalho( 'Fim Benefício' , 5 );

    $obTable->Body->addCampo( '[inCGMBeneficiario] - [stCGMBeneficiario]', 'C' );
    $obTable->Body->addCampo( 'inCodUsuario', 'C' );
    $obTable->Body->addCampo( 'stGrauParentesco', 'C' );
    $obTable->Body->addCampo( 'stTipo', 'C' );
    $obTable->Body->addCampo( 'dtInicioBeneficio', 'C' );
    $obTable->Body->addCampo( 'dtFimBeneficio', 'C' );

    $obTable->Body->addAcao( 'excluir' ,  'excluirListaItens(%s)', array( 'id' ) );
    $obTable->Body->addAcao( 'alterar' ,  'montaAlteracaoLista(%s)' , array( 'id' ) );

    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "window.parent.frames['telaPrincipal'].document.getElementById('spnBeneficiario').innerHTML = '".$stHTML."';";
    $stJs.= "window.parent.frames['telaPrincipal'].limparBeneficiario();";

    return $stJs;
}

function executaValidacao($array)
{
    $stMensagem = "";
    
    if ($array['inContrato'] == '') {
        $stMensagem = 'Matrícula do CGM deve ser escolhida';
        
    } elseif ($array['inCGMBeneficiario'] == '') {
        $stMensagem = 'Beneficiário inválido';
        
    } elseif ($array['inCGMFornecedor'] == '') {
        $stMensagem = 'Fornecedor inválido';
        
    } elseif ($array['inModalidade'] == '') {
        $stMensagem = 'Modalidade de convênio inválida';
        
    } elseif ($array['inTipo'] == '') {
        $stMensagem = 'Tipo de convênio inválido';
        
    } elseif ($array['inCodUsuario'] == '') {
        $stMensagem = 'Código de usuário inválido';
        
    } elseif ($array['inGrauParentesco'] == '') {
        $stMensagem = 'Grau de parentesco inválido';
        
    } elseif ($array['dtInicioBeneficio'] == '') {
        $stMensagem = 'Data de início do benefício inválido';
        
    } elseif ($array['vlDesconto'] ==  '') {
        $stMensagem = 'Valor do desconto inválido';
    }
    
    return $stMensagem;
}
