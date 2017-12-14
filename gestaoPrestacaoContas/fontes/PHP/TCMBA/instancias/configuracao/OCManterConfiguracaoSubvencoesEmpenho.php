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

    * Pacote de configuração do TCMBA - Subvenções dos Empenhos
    * Data de Criação   : 25/08/2015

    * @author Analista: 
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id: $
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBASubvencaoEmpenho.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSubvencoesEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function limpaCampos ()
{
    $stJs  = " jq('#stDataInicial').val('');                \n";
    $stJs .= " jq('#stDataFinal').val('');                  \n";
    $stJs .= " jq('#inPrazoAplicacao').val('');             \n";
    $stJs .= " jq('#inPrazoComprovacao').val('');           \n";
    $stJs .= " jq('#inCodNormaReconhecida').val('');        \n";
    $stJs .= " jq('#stNomeNormaReconhecida').html('&nbsp'); \n";
    $stJs .= " jq('#inCodNormaConcedente').val('');         \n";
    $stJs .= " jq('#stNomeNormaConcedente').html('&nbsp');  \n";
    $stJs .= " jq('#inNumBanco').val('');                   \n";
    $stJs .= " jq('#stNomeBanco').val('');                  \n";
    $stJs .= " jq('#inNumAgencia').val('');                 \n";
    $stJs .= " jq('#stNomeAgencia').val('');                \n";
    $stJs .= " jq('#stContaCorrente').val('');              \n";

    return $stJs;
}

function bloqueiaCampos ()
{

    $stJs  = " jq('#stDataInicial').prop('disabled',true);             \n";
    $stJs .= " jq('#stDataFinal').prop('disabled',true);               \n";
    $stJs .= " jq('#inPrazoAplicacao').prop('disabled',true);          \n";
    $stJs .= " jq('#inPrazoComprovacao').prop('disabled',true);        \n";
    
    $stJs .= " jq('#inCodNormaReconhecida').prop('disabled',true);     \n";
    $stJs .= " jq('#stNomeNormaReconhecida').prop('disabled',true);    \n";
    $stJs .= " jq('#inCodNormaConcedente').prop('disabled',true);      \n";
    $stJs .= " jq('#stNomeNormaConcedente').prop('disabled',true);     \n";
    $stJs .= " jq('a[href*=\"Norma\"]').prop('hidden',true);           \n";
    $stJs .= " jq('#inNumBanco').prop('disabled',true);                \n";
    $stJs .= " jq('#stNomeBanco').prop('disabled',true);               \n";
    $stJs .= " jq('#inNumAgencia').prop('disabled',true);              \n";
    $stJs .= " jq('#stNomeAgencia').prop('disabled',true);             \n";
    $stJs .= " jq('#stContaCorrente').prop('disabled',true);           \n";

    return $stJs;
}

function liberaCampos ()
{
    $stJs  = " jq('#stDataInicial').removeProp('disabled');             \n";
    $stJs .= " jq('#stDataFinal').removeProp('disabled');               \n";
    $stJs .= " jq('#inPrazoAplicacao').removeProp('disabled');          \n";
    $stJs .= " jq('#inPrazoComprovacao').removeProp('disabled');        \n";
    $stJs .= " jq('#inCodNormaReconhecida').removeProp('disabled');     \n";
    $stJs .= " jq('#stNomeNormaReconhecida').removeProp('disabled');    \n";
    $stJs .= " jq('#inCodNormaConcedente').removeProp('disabled');      \n";
    $stJs .= " jq('#stNomeNormaConcedente').removeProp('disabled');     \n";
    $stJs .= " jq('a[href*=\"Norma\"]').removeProp('hidden');           \n";
    $stJs .= " jq('#inNumBanco').removeProp('disabled');                \n";
    $stJs .= " jq('#stNomeBanco').removeProp('disabled');               \n";
    $stJs .= " jq('#inNumAgencia').removeProp('disabled');              \n";
    $stJs .= " jq('#stNomeAgencia').removeProp('disabled');             \n";
    $stJs .= " jq('#stContaCorrente').removeProp('disabled');           \n";
    return $stJs;
}

switch ($stCtrl) {
    case "selecionaBanco":
        $stJs .= " jq('#inNumBanco').val('".$_REQUEST["stNomeBanco"]."'); \n";
        $stJs .= " jq('#inNumBanco').focus(); \n";
        $stJs .= " jq('#stNomeAgencia').focus(); \n";
    break;
    case "selecionaAgencia":
        $stJs .= " jq('#inNumAgencia').val('".$_REQUEST["stNomeAgencia"]."'); \n";
        $stJs .= " jq('#inNumAgencia').focus(); \n";
        $stJs .= " jq('#stContaCorrente').focus(); \n";
    break;
    
    case "montaAgencia":
        $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
        if ($_REQUEST["inNumBanco"] != '') {
            $stJs = " jq('#stNomeAgencia').empty().append(new Option('Selecione','')).val('2'); \n";
            $stJs .= " jq('#inNumAgencia').val(''); \n";

            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
            $stJs .= " jq('#inCodBanco').val('".$rsBanco->getCampo('cod_banco')."'); \n";

            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);

            if ($rsCombo->getNumLinhas() > 0) {
                foreach ($rsCombo->getElementos() as $value) {
                    $stJs .= " jq('#stNomeAgencia').append( new Option('".$value['nom_agencia']."','".$value['num_agencia']."') ); \n";
                }
                $stJs .= " if (jq('#hdnNumAgencia').val()!=''){jq('#stNomeAgencia').val(jq('#hdnNumAgencia').val()); jq('#inNumAgencia').val(jq('#hdnNumAgencia').val()); }\n";    
            }else{
                $stJs .= " jq('#stNomeAgencia').empty().append(new Option('Selecione','') ); \n";
                $stJs .= " jq('#inNumAgencia').val(''); \n";
                $stJs .= " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";
            }

        } else {
            $stJs .= " jq('#stNomeAgencia').empty().append(new Option('Selecione','') ); \n";
            $stJs .= " jq('#inNumAgencia').val(''); \n";
            $stJs .= " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";
        }
    break;
    case "montaContaCorrente":
        if ($_REQUEST["inNumAgencia"] != '') {
            $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
            $obRContabilidadePlanoBanco->setCodConta( $_REQUEST['inCodConta'] );
            $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST["inCodBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->setNumAgencia( $_REQUEST["inNumAgencia"] );
            $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsCombo , $stFiltro, $obTransacao);
            $stJs = " jq('#inCodAgencia').val('".$rsCombo->getCampo('cod_agencia')."'); \n";
            
            $obRContabilidadePlanoBanco->consultar();
            
            $stJs .= " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";
            
            include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
            $obRMONContaCorrente = new RMONContaCorrente();
            $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $_REQUEST['inCodBanco'] );
            $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );

            $rsCCorrente = new RecordSet();
            $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );
                     
            foreach ($rsCCorrente->getElementos() as $value) {
                $stJs .= "jq('#stContaCorrente').append( new Option('".$value['num_conta_corrente']."','".$value['cod_conta_corrente']."') ); \n";
            }    
            
            $stJs .= " if (jq('#hdnContaCorrente').val()!=''){ jq('#stContaCorrente').val(jq('#hdnContaCorrente').val());} \n";

        } else {
            $stJs = " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";
        }
    break;

    case 'carregaDadosBanco':
        //Dados do Banco
        $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
        $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );
        $arBancos = $rsBanco->getElementos();
        foreach ($arBancos as $arBanco) {
            if ($arBanco['cod_banco'] != 0) {
                $arNewBancos[] = $arBanco;
            }
        }
        $rsBanco->setElementos( $arNewBancos );
        $rsBanco->setNumLinhas( count( $arNewBancos ) );
        $stJs = "";
        foreach ($rsBanco->getElementos() as $value) {
            $stJs .= " jq('#stNomeBanco').append(new Option('".$value['nom_banco']."','".$value['num_banco']."')); \n";
        }
    break;

    case 'carregaDadosCgm':

        if( $_REQUEST['inCGMFornecedor'] != "" ){
            $obTTCMBASubvencaoEmpenho = new TTCMBASubvencaoEmpenho();
            $obTTCMBASubvencaoEmpenho->setDado('numcgm',$_REQUEST['inCGMFornecedor']);
            $obTTCMBASubvencaoEmpenho->recuperaSubvencaoEmpenho($rsSuvncaoEmpenho,"","",$boTransacao);
            //Verifica se o CGM ja possui configuracao
            if ( $rsSuvncaoEmpenho->getNumLinhas() > 0 ) {
                $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->setCodBanco( $rsSuvncaoEmpenho->getCampo('cod_banco') );
                $obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->listarBanco( $rsBanco );

                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsAgenciaBanco , $stFiltro, $obTransacao);
                $obRContabilidadePlanoBanco->obRMONAgencia->setCodAgencia( $rsSuvncaoEmpenho->getCampo('cod_agencia') );
                $obRContabilidadePlanoBanco->obRMONAgencia->listarAgencia( $rsAgenciaConfiguracao , $stFiltro, $obTransacao);

                $stJs = liberaCampos();
                $stJs .= " jq('#stDataInicial').val('".$rsSuvncaoEmpenho->getCampo('dt_inicio')."'); \n";
                $stJs .= " jq('#stDataFinal').val('".$rsSuvncaoEmpenho->getCampo('dt_termino')."'); \n";
                $stJs .= " jq('#inPrazoAplicacao').val('".$rsSuvncaoEmpenho->getCampo('prazo_aplicacao')."'); \n";
                $stJs .= " jq('#inPrazoComprovacao').val('".$rsSuvncaoEmpenho->getCampo('prazo_comprovacao')."'); \n";
                //Para preencher certo e carregar seta o foco no campo -> atribui valor -> muda o foco para que o evento seja disparado
                //Normas
                $stJs .= " jq('#inCodNormaReconhecida').focus(); \n";
                $stJs .= " jq('#inCodNormaReconhecida').val('".$rsSuvncaoEmpenho->getCampo('cod_norma_utilidade')."'); \n";
                $stJs .= " jq('#inCodNormaConcedente').focus(); \n";
                $stJs .= " jq('#inCodNormaConcedente').val('".$rsSuvncaoEmpenho->getCampo('cod_norma_valor')."'); \n";
                //Banco
                $stJs .= " jq('#inNumBanco').val('".$rsBanco->getCampo('num_banco')."'); \n";
                $stJs .= " jq('#stNomeBanco').val('".$rsBanco->getCampo('num_banco')."'); \n";
                $stJs .= " jq('#inCodBanco').val('".$rsBanco->getCampo('cod_banco')."'); \n";
                //Agencia
                if ($rsAgenciaBanco->getNumLinhas() > 0) {
                    foreach ($rsAgenciaBanco->getElementos() as $value) {
                        $stJs .= " jq('#stNomeAgencia').append( new Option('".$value['nom_agencia']."','".$value['num_agencia']."') ); \n";
                    }
                    $stJs .= " jq('#stNomeAgencia').val('".$rsAgenciaConfiguracao->getCampo('num_agencia')."'); \n";
                    $stJs .= " jq('#inNumAgencia').val('".$rsAgenciaConfiguracao->getCampo('num_agencia')."'); \n";
                    $stJs .= " jq('#inCodAgencia').val('".$rsSuvncaoEmpenho->getCampo('cod_agencia')."'); \n";
                }else{
                    $stJs .= " jq('#stNomeAgencia').empty().append(new Option('Selecione','') ); \n";
                    $stJs .= " jq('#inNumAgencia').val(''); \n";
                    $stJs .= " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";
                }
                //Conta Corrente
                include_once( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
                $obRMONContaCorrente = new RMONContaCorrente();
                $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $rsSuvncaoEmpenho->getCampo('cod_banco') );
                $obRMONContaCorrente->obRMONAgencia->setNumAgencia( $rsAgenciaConfiguracao->getCampo('num_agencia') );
                $obRMONContaCorrente->listarContaCorrente( $rsCCorrente, $obTransacao );
                if ($rsCCorrente->getNumLinhas() > 0) {
                    foreach ($rsCCorrente->getElementos() as $value) {
                        $stJs .= "jq('#stContaCorrente').append( new Option('".$value['num_conta_corrente']."','".$value['cod_conta_corrente']."') ); \n";
                    }        
                    $stJs .= " jq('#stContaCorrente').val('".$rsSuvncaoEmpenho->getCampo('cod_conta_corrente')."'); \n";
                    $stJs .= " jq('#stContaCorrente').focus(); \n";
                }else{
                    $stJs .= " jq('#stContaCorrente').empty().append(new Option('Selecione','') ); \n";   
                }
            }else{
                $stJs  = limpaCampos();
                $stJs .= liberaCampos();
            }
        //Caso venha vazio
        }else{
            $stJs  = limpaCampos();
            $stJs .= bloqueiaCampos();
        }
    break;
}

if ($stJs) {
   echo $stJs;
} 

?>