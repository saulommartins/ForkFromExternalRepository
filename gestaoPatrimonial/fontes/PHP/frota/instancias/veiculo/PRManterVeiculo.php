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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: PRManterVeiculo.php 64139 2015-12-08 15:47:35Z diogo.zarpelon $

    * Casos de uso: uc-03.02.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoPropriedade.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTerceiros.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoTerceirosResponsavel.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaProprio.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoCombustivel.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoDocumento.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculoDocumentoEmpenho.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTerceirosHistorico.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaAutorizacao.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMotoristaVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaControleInterno.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemResponsavel.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioVeiculoUniorcam.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNVeiculoCategoriaVinculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoLocacao.class.php' );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaVeiculoCessao.class.php' );


$stPrograma = "ManterVeiculo";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTFrotaVeiculo = new TFrotaVeiculo();
$obTFrotaTerceiros = new TFrotaTerceiros();
$obTFrotaVeiculoPropriedade = new TFrotaVeiculoPropriedade();
$obTFrotaVeiculoTerceirosResponsavel = new TFrotaVeiculoTerceirosResponsavel();
$obTFrotaProprio = new TFrotaProprio();
$obTFrotaVeiculoCombustivel = new TFrotaVeiculoCombustivel();
$obTFrotaVeiculoDocumento = new TFrotaVeiculoDocumento();
$obTFrotaVeiculoDocumentoEmpenho = new TFrotaVeiculoDocumentoEmpenho();
$obTFrotaTerceirosHistorico = new TFrotaTerceirosHistorico();
$obTFrotaAutorizacao = new TFrotaAutorizacao();
$obTFrotaMotoristaVeiculo = new TFrotaMotoristaVeiculo();
$obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
$obTPatrimonioVeiculoUniorcam = new TPatrimonioVeiculoUniorcam();
$obTFrotaControleInterno = new TFrotaControleInterno();
$obTFrotaVeiculoLocacao = new TFrotaVeiculoLocacao();
$obTFrotaVeiculoCessao = new TFrotaVeiculoCessao();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculo );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoPropriedade );
Sessao::getTransacao()->setMapeamento( $obTFrotaTerceiros );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoTerceirosResponsavel );
Sessao::getTransacao()->setMapeamento( $obTFrotaProprio );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoCombustivel );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoDocumento );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoDocumentoEmpenho );
Sessao::getTransacao()->setMapeamento( $obTFrotaTerceirosHistorico );
Sessao::getTransacao()->setMapeamento( $obTFrotaAutorizacao );
Sessao::getTransacao()->setMapeamento( $obTFrotaMotoristaVeiculo );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioBemResponsavel );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioVeiculoUniorcam );
Sessao::getTransacao()->setMapeamento( $obTFrotaControleInterno );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoLocacao );
Sessao::getTransacao()->setMapeamento( $obTFrotaVeiculoCessao );

if ($_REQUEST['inCategoriaVeiculo']) {
    $obTTCERNVeiculoCategoriaVinculo = new TTCERNVeiculoCategoriaVinculo();
    Sessao::getTransacao()->setMapeamento( $obTTCERNVeiculoCategoriaVinculo );
}

switch ($stAcao) {
    case 'incluir':
        if ($_REQUEST['stOrigemBem'] == 'proprio' AND $_REQUEST['inCodBem'] == '') {
            $stMensagem = 'Preencha o campo Código do Bem';
        }
        if ($_REQUEST['stOrigemBem'] == 'terceiro' AND $_REQUEST['inCodProprietario'] == '') {
            $stMensagem = 'Preencha o campo Proprietário';
        }
        if ( ( $_REQUEST['stAnoModelo'] < $_REQUEST['stAnoFabricacao'] ) OR ($_REQUEST['stAnoModelo'] > $_REQUEST['stAnoFabricacao']+1) ) {
            $stMensagem = 'O Ano do Modelo pode ser igual a '.$_REQUEST['stAnoFabricacao'].' ou '.($_REQUEST['stAnoFabricacao']+1);
        }
        if ( (int) substr($_REQUEST['dtAquisicao'],6) < (int) $_REQUEST['stAnoFabricacao'] ) {
            $stMensagem = 'A data de aquisição deve ser igual ou superior ao ano de fabricação';
        }
        if ($_REQUEST['stNumPlaca'] != '') {
            $obTFrotaVeiculo->recuperaTodos( $rsVeiculo, " WHERE placa = '".str_replace('-','',$_REQUEST['stNumPlaca'])."' " );
            if ( $rsVeiculo->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo com esta placa no sistema';
            }
        }
        if ($_REQUEST['stPrefixo'] != '') {
            $obTFrotaVeiculo->recuperaTodos( $rsVeiculo, " WHERE prefixo = '".str_replace('-','',$_REQUEST['stPrefixo'])."' " );
            if ( $rsVeiculo->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo com esta prefixo no sistema';
            }
        }
        if ($_REQUEST['stOrigemBem'] == 'proprio' AND $_REQUEST['HdninCodResponsavel'] != '') {
            $obTFrotaProprio->recuperaTodos( $rsBem, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' ' );
            if ( $rsBem->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo utilizando este bem';
            }
        }

        if ($_REQUEST['inCodResponsavel'] == '') {
            $stMensagem = 'Preencha o campo responsável';
        } elseif ($_REQUEST['dtInicio'] == '') {
            $stMensagem = 'Preencha o campo data de início';
        } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) > date('Ymd') ) {
            $stMensagem = 'A data de início do responsável deve ser menor ou igual ao dia de hoje';
        } elseif (  implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
            $stMensagem = 'A data de início do responsável deve ser maior ou igual a de aquisição do veículo';
        }

        $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['slTipoVeiculo'] );
        $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );

        //faz a verificacao do prefixo
        if ( $rsTipoVeiculo->getCampo('prefixo') == 't' AND $_REQUEST['stPrefixo'] == '' ) {
            $stMensagem = 'Preencha o campo Prefixo';
        }

        //faz a verificacao do placa
        if ( $rsTipoVeiculo->getCampo('placa') == 't' AND $_REQUEST['stNumPlaca'] == '' ) {
            $stMensagem = 'Preencha o campo Placa';
        }
        
        if(!isset($_REQUEST['boControleInterno'])){
            $stMensagem = 'Selecione Atestado de controle interno';
        }
        
        if(!empty($_REQUEST['inCodEntidade'])) {
            if(empty($_REQUEST['inCodOrgao'])) {
                $stMensagem = 'Ao selecionar uma Entidade você deve obrigatoriamente selecionar um Órgão.';
            }
        }
        
        if(!empty($_REQUEST['inCodOrgao'])) {
            if(empty($_REQUEST['inCodUnidade'])) {
                $stMensagem = 'Ao selecionar um Órgão você deve obrigatoriamente selecionar uma Unidade.';
            }
        }
        
        if (!$stMensagem) {
            
            //recupera o proximo cod_veiculo disponivel
            $obTFrotaVeiculo->proximoCod( $inCodVeiculo );
            
            //seta os dados da table frota.veiculo e inclui
            $obTFrotaVeiculo->setDado( 'cod_veiculo'       , $inCodVeiculo );
            $obTFrotaVeiculo->setDado( 'cod_marca'         , $_REQUEST['inCodMarca'] );
            $obTFrotaVeiculo->setDado( 'cod_modelo'        , $_REQUEST['inCodModelo'] );
            $obTFrotaVeiculo->setDado( 'cod_tipo_veiculo'  , $_REQUEST['slTipoVeiculo'] );
            $obTFrotaVeiculo->setDado( 'cod_categoria'     , $_REQUEST['slHabilitacao'] );
            $obTFrotaVeiculo->setDado( 'prefixo'           , $_REQUEST['stPrefixo'] );
            $obTFrotaVeiculo->setDado( 'chassi'            , $_REQUEST['stChassi'] );
            $obTFrotaVeiculo->setDado( 'dt_aquisicao'      , $_REQUEST['dtAquisicao'] );
            $obTFrotaVeiculo->setDado( 'km_inicial'        , str_replace(',','.',str_replace('.','',$_REQUEST['inKmInicial'])) );
            $obTFrotaVeiculo->setDado( 'num_certificado'   , $_REQUEST['inNumCertificado'] );
            $obTFrotaVeiculo->setDado( 'placa'             , str_replace('-','',$_REQUEST['stNumPlaca']) );
            $obTFrotaVeiculo->setDado( 'ano_fabricacao'    , $_REQUEST['stAnoFabricacao'] );
            $obTFrotaVeiculo->setDado( 'ano_modelo'        , $_REQUEST['stAnoModelo'] );
            $obTFrotaVeiculo->setDado( 'categoria'         , $_REQUEST['stCategoriaVeiculo'] );
            $obTFrotaVeiculo->setDado( 'cor'               , $_REQUEST['stCor'] );
            $obTFrotaVeiculo->setDado( 'capacidade'        , $_REQUEST['stCapacidade'] );
            $obTFrotaVeiculo->setDado( 'potencia'          , $_REQUEST['stPotencia'] );
            $obTFrotaVeiculo->setDado( 'cilindrada'        , $_REQUEST['stCilindrada'] );
            $obTFrotaVeiculo->setDado( 'num_passageiro'    , $_REQUEST['inNumPassageiro'] );
            $obTFrotaVeiculo->setDado( 'capacidade_tanque' , $_REQUEST['inCapacidadeTanque'] );

            $obTFrotaVeiculo->inclusao();

            if ($_REQUEST['stOrigemBem'] == 'proprio') {
                if ($_REQUEST['HdninCodResponsavel'] != '') {
                    $obTPatrimonioBemResponsavel->setDado('cod_bem', $_REQUEST['inCodBem']);
                    $obTPatrimonioBemResponsavel->setDado('numcgm', $_REQUEST['inCodResponsavel']);
                    $obTPatrimonioBemResponsavel->setDado('dt_inicio', $_REQUEST['dtInicio']);
                    $obTPatrimonioBemResponsavel->inclusao();
                }
            } else {
                //seta os dados da table frota.veiculo_terceiros_responsavel
                $obTFrotaVeiculoTerceirosResponsavel->setDado('cod_veiculo',$inCodVeiculo);
                $obTFrotaVeiculoTerceirosResponsavel->setDado('numcgm', $_REQUEST['inCodResponsavel'] );
                $obTFrotaVeiculoTerceirosResponsavel->setDado('dt_inicio', $_REQUEST['dtInicio'] );
                $obTFrotaVeiculoTerceirosResponsavel->inclusao();
                
                if(!empty($_REQUEST['inCodEntidade']) && !empty($_REQUEST['inCodOrgao']) && !empty($_REQUEST['inCodUnidade'])) {
                    $obTPatrimonioVeiculoUniorcam->setDado('cod_veiculo',$inCodVeiculo);
                    $obTPatrimonioVeiculoUniorcam->setDado('exercicio',$_REQUEST['stExercicio']);
                    $obTPatrimonioVeiculoUniorcam->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                    $obTPatrimonioVeiculoUniorcam->setDado('num_orgao',$_REQUEST['inCodOrgao']);
                    $obTPatrimonioVeiculoUniorcam->setDado('num_unidade',$_REQUEST['inCodUnidade']);
                    $obTPatrimonioVeiculoUniorcam->inclusao();
                }
            }

            //seta os dados da table frota.veiculo_combustivel
            if ( is_array($_REQUEST['inCodCombustivelSelecionados']) ) {
                $obTFrotaVeiculoCombustivel->setDado( 'cod_veiculo'   , $inCodVeiculo );
                foreach ($_REQUEST['inCodCombustivelSelecionados'] AS $stKey=>$stValue) {
                    $obTFrotaVeiculoCombustivel->setDado( 'cod_combustivel' , $stValue );
                    $obTFrotaVeiculoCombustivel->inclusao();
                }
            }

            //seta os dados na table frota.veiculo_propriedade
            $obTFrotaVeiculoPropriedade->setDado('cod_veiculo', $inCodVeiculo );

            if ($_REQUEST['stOrigemBem'] == 'proprio') {
                //seta true para veiculo_propriedade.proprio
                $obTFrotaVeiculoPropriedade->setDado('proprio',true);
                $obTFrotaVeiculoPropriedade->inclusao();

                //seta os dados da table frota.proprio e inclui
                $obTFrotaProprio->setDado( 'cod_veiculo', $inCodVeiculo );
                $obTFrotaProprio->setDado( 'cod_bem'    , $_REQUEST['inCodBem'] );
                $obTFrotaProprio->inclusao();

            } else {
                //seta false para veiculo_propriedade.proprio
                $obTFrotaVeiculoPropriedade->setDado('proprio',false);
                $obTFrotaVeiculoPropriedade->inclusao();

                //seta os dados da table frota.terceiros
                $obTFrotaTerceiros->setDado( 'cod_veiculo', $inCodVeiculo );
                $obTFrotaTerceiros->setDado( 'cod_proprietario', $_REQUEST['inCodProprietario'] );
                $obTFrotaTerceiros->inclusao();

                //seta os dados da table frota.terceiros_historico
                $arOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];

                $obTFrotaTerceirosHistorico->setDado( 'cod_veiculo'     , $inCodVeiculo );
                $obTFrotaTerceirosHistorico->setDado( 'cod_orgao'       , $arOrgao );
                $obTFrotaTerceirosHistorico->setDado( 'cod_local'       , (int) $_REQUEST['inCodLocal'] );
                $obTFrotaTerceirosHistorico->setDado( 'ano_exercicio'    , Sessao::getExercicio() );
                $obTFrotaTerceirosHistorico->inclusao();
            }
            
            $obTFrotaControleInterno->setDado( 'cod_veiculo', $inCodVeiculo );
            $obTFrotaControleInterno->setDado( 'exercicio', Sessao::getExercicio() );
            $obTFrotaControleInterno->setDado( 'verificado', $_REQUEST['boControleInterno'] );
            $obTFrotaControleInterno->inclusao();

            //seta os dados da table frota.veiculo_documento e inclui
            if ( is_array( Sessao::read('arDocumentos') ) ) {
                foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                    $obTFrotaVeiculoDocumento->setDado( 'cod_veiculo'   , $inCodVeiculo );
                    $obTFrotaVeiculoDocumento->setDado( 'cod_documento' , $arTemp['cod_documento'] );
                    $obTFrotaVeiculoDocumento->setDado( 'mes'           , $arTemp['mes'] );
                    $obTFrotaVeiculoDocumento->setDado( 'exercicio'     , $arTemp['ano_documento'] );
                    $obTFrotaVeiculoDocumento->inclusao();
                    //se estiver pago, inclui também na table frota.veiculo_documento_empenho e inclui
                    if ($arTemp['situacao']) {
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_veiculo'       , $inCodVeiculo );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_documento'     , $arTemp['cod_documento'] );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'exercicio'         , $arTemp['ano_documento'] );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_empenho'       , $arTemp['cod_empenho']   );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_entidade'      , $arTemp['cod_entidade']  );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'exercicio_empenho' , $arTemp['exercicio_empenho'] );
                        $obTFrotaVeiculoDocumentoEmpenho->inclusao();
                    }
                }
            }

            //seta os dados da table frota.veiculo_locacao e inclui
            if ( is_array( Sessao::read('arLocacoes') ) ) {
                foreach ( Sessao::read('arLocacoes') AS $arTemp ) {
                    $obTFrotaVeiculoLocacao->proximoCod($inCodLocacao);
                    $obTFrotaVeiculoLocacao->setDado( 'id'           , $inCodLocacao );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_veiculo'  , $inCodVeiculo );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_processo' , substr($arTemp['stProcessoLocacao'],0,5) );
                    $obTFrotaVeiculoLocacao->setDado( 'ano_exercicio', substr($arTemp['stProcessoLocacao'],6,4) );
                    $obTFrotaVeiculoLocacao->setDado( 'cgm_locatario', $arTemp['inCodLocatario'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_contrato'  , $arTemp['dtContrato'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_inicio'    , $arTemp['dtIniLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_termino'   , $arTemp['dtFimLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'exercicio'    , $arTemp['stExercicioLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_entidade' , $arTemp['inCodEntidadeLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_empenho'  , $arTemp['inNumEmpenhoLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'vl_locacao'   , $arTemp['inValorLocacao'] );
                    $obTFrotaVeiculoLocacao->inclusao();
                }
            }

            //seta os dados da table frota.veiculo_cessao e inclui
            if ( is_array( Sessao::read('arListaCessao') ) ) {
                foreach ( Sessao::read('arListaCessao') AS $arTemp ) {
                    $obTFrotaVeiculoCessao->proximoCod($inCodCessao);
                    $obTFrotaVeiculoCessao->setDado( 'id'            , $inCodCessao );
                    $obTFrotaVeiculoCessao->setDado( 'cod_veiculo'   , $inCodVeiculo );
                    $obTFrotaVeiculoCessao->setDado( 'cod_processo'  , $arTemp['cod_processo'] );
                    $obTFrotaVeiculoCessao->setDado( 'exercicio'     , $arTemp['exercicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'cgm_cedente'   , $arTemp['cgm_cedente'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_inicio'     , $arTemp['dt_inicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_termino'    , $arTemp['dt_termino'] );
                    $obTFrotaVeiculoCessao->inclusao();
                }
            }

            if ($_REQUEST['inCategoriaVeiculo']) {
                $obTTCERNVeiculoCategoriaVinculo = new TTCERNVeiculoCategoriaVinculo();
                $obTTCERNVeiculoCategoriaVinculo->setDado('cod_veiculo', $inCodVeiculo);
                $obTTCERNVeiculoCategoriaVinculo->setDado('cod_categoria', $_REQUEST['inCategoriaVeiculo']);
                $obErro = $obTTCERNVeiculoCategoriaVinculo->inclusao();
            }

            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,'Veículo - '.$inCodVeiculo,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem).'!',"n_incluir","erro");
            echo "<script>LiberaFrames(true,true);</script>";
        }

        break;

    case 'alterar' :

        if ($_REQUEST['stOrigemBem'] == 'proprio' AND $_REQUEST['inCodBem'] == '') {
            $stMensagem = 'Preencha o campo Código do Bem';
        }
        if ($_REQUEST['stOrigemBem'] == 'terceiro' AND $_REQUEST['inCodProprietario'] == '') {
            $stMensagem = 'Preencha o campo Proprietário';
        }
        if ($_REQUEST['stOrigemBem'] == '') {
            $stMensagem = 'Selecione a Origem do Bem';
        }
        if ( ( $_REQUEST['stAnoModelo'] < $_REQUEST['stAnoFabricacao'] ) OR ($_REQUEST['stAnoModelo'] > $_REQUEST['stAnoFabricacao']+1) ) {
            $stMensagem = 'O Ano do Modelo pode ser igual a '.$_REQUEST['stAnoFabricacao'].' ou '.($_REQUEST['stAnoFabricacao']+1);
        }
        if ( substr($_REQUEST['dtAquisicao'],6) < $_REQUEST['stAnoFabricacao'] ) {
            $stMensagem = 'A data de aquisição deve ser superior ou igual ao ano de fabricação';
        }
        if ($_REQUEST['stNumPlaca'] != '') {
            $obTFrotaVeiculo->recuperaTodos( $rsVeiculo, " WHERE placa = '".str_replace('-','',$_REQUEST['stNumPlaca'])."' AND cod_veiculo <> ".$_REQUEST['inCodVeiculo']." " );
            if ( $rsVeiculo->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo com esta placa no sistema';
            }
        }
        if ($_REQUEST['stPrefixo'] != '') {
            $obTFrotaVeiculo->recuperaTodos( $rsVeiculo, " WHERE prefixo = '".str_replace('-','',$_REQUEST['stPrefixo'])."' AND cod_veiculo <> ".$_REQUEST['inCodVeiculo']." " );
            if ( $rsVeiculo->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo com esta prefixo no sistema';
            }
        }
        if ($_REQUEST['stOrigemBem'] == 'proprio' AND $_REQUEST['HdninCodResponsavel'] != '') {
            $obTFrotaProprio->recuperaTodos( $rsBem, ' WHERE cod_bem = '.$_REQUEST['inCodBem'].' AND cod_veiculo <> '.$_REQUEST['inCodVeiculo'].' ' );
            if ( $rsBem->getNumLinhas() > 0 ) {
                $stMensagem = 'Já existe um veículo utilizando este bem';
            }
        }

        if ($_REQUEST['inCodResponsavel'] == '') {
            $stMensagem = 'Preencha o campo responsável';
        } elseif ($_REQUEST['dtInicio'] == '') {
            $stMensagem = 'Preencha o campo data de início';
        } elseif ( implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) > date('Ymd') ) {
            $stMensagem = 'A data de início do responsável deve ser menor ou igual ao dia de hoje';
        } elseif (  implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) < implode('',array_reverse(explode('/',$_REQUEST['dtAquisicao']))) ) {
            $stMensagem = 'A data de início do responsável deve ser maior ou igual a de aquisição do veículo';
        }

        //verifican se a data do responsavel e maior que a do ultimo cadastrado
        $obTFrotaVeiculoTerceirosResponsavel->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
        $obTFrotaVeiculoTerceirosResponsavel->recuperaUltimoResponsavel( $rsResponsavel );
        if ( $rsResponsavel->getNumLinhas() > 0 ) {
            if ( (implode('',array_reverse(explode('/',$rsResponsavel->getCampo('dt_inicio')))) >= implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) AND $rsResponsavel->getCampo('numcgm') <> $_REQUEST['inCodResponsavel']) OR ( implode('',array_reverse(explode('/',$rsResponsavel->getCampo('dt_inicio')))) > implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) AND $rsResponsavel->getCampo('numcgm') == $_REQUEST['inCodResponsavel'] )) {
                $stMensagem = 'A data de início do responsável deve ser posterior a do atual responsável('.$rsResponsavel->getCampo('dt_inicio').')';
            }
        }

        $obTFrotaTipoVeiculo->setDado( 'cod_tipo', $_REQUEST['slTipoVeiculo'] );
        $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );

        //faz a verificacao do prefixo
        if ( $rsTipoVeiculo->getCampo('prefixo') == 't' AND $_REQUEST['stPrefixo'] == '' ) {
            $stMensagem = 'Preencha o campo Prefixo';
        }

        //faz a verificacao do placa
        if ( $rsTipoVeiculo->getCampo('placa') == 't' AND $_REQUEST['stNumPlaca'] == '' ) {
            $stMensagem = 'Preencha o campo Placa';
        }
        
        if(!isset($_REQUEST['boControleInterno'])){
            $stMensagem = 'Selecione Atestado de controle interno';
        }

        if (!$stMensagem) {

            //seta os dados da table frota.veiculo e altera
            $obTFrotaVeiculo->setDado( 'cod_veiculo'       , $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculo->setDado( 'cod_marca'         , $_REQUEST['inCodMarca'] );
            $obTFrotaVeiculo->setDado( 'cod_modelo'        , $_REQUEST['inCodModelo'] );
            $obTFrotaVeiculo->setDado( 'cod_tipo_veiculo'  , $_REQUEST['slTipoVeiculo'] );
            $obTFrotaVeiculo->setDado( 'cod_responsavel'   , $_REQUEST['inCodResponsavel'] );
            $obTFrotaVeiculo->setDado( 'cod_categoria'     , $_REQUEST['slHabilitacao'] );
            $obTFrotaVeiculo->setDado( 'prefixo'           , $_REQUEST['stPrefixo'] );
            $obTFrotaVeiculo->setDado( 'chassi'            , $_REQUEST['stChassi'] );
            $obTFrotaVeiculo->setDado( 'dt_aquisicao'      , $_REQUEST['dtAquisicao'] );
            $obTFrotaVeiculo->setDado( 'km_inicial'        , str_replace(',','.',str_replace('.','',$_REQUEST['inKmInicial'])) );
            $obTFrotaVeiculo->setDado( 'num_certificado'   , $_REQUEST['inNumCertificado'] );
            $obTFrotaVeiculo->setDado( 'placa'             , str_replace('-','',$_REQUEST['stNumPlaca']) );
            $obTFrotaVeiculo->setDado( 'ano_fabricacao'    , $_REQUEST['stAnoFabricacao'] );
            $obTFrotaVeiculo->setDado( 'ano_modelo'        , $_REQUEST['stAnoModelo'] );
            $obTFrotaVeiculo->setDado( 'categoria'         , $_REQUEST['stCategoriaVeiculo'] );
            $obTFrotaVeiculo->setDado( 'cor'               , $_REQUEST['stCor'] );
            $obTFrotaVeiculo->setDado( 'capacidade'        , $_REQUEST['stCapacidade'] );
            $obTFrotaVeiculo->setDado( 'potencia'          , $_REQUEST['stPotencia'] );
            $obTFrotaVeiculo->setDado( 'cilindrada'        , $_REQUEST['stCilindrada'] );
            $obTFrotaVeiculo->setDado( 'num_passageiro'    , $_REQUEST['inNumPassageiro'] );
            $obTFrotaVeiculo->setDado( 'capacidade_tanque' , $_REQUEST['inCapacidadeTanque'] );
            $obTFrotaVeiculo->alteracao();

            //deleta os registros da table frota.veiculo_combustivel
            $obTFrotaVeiculoCombustivel->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaVeiculoCombustivel->exclusao();

            //seta os dados da table frota.veiculo_combustivel
            if ( is_array($_REQUEST['inCodCombustivelSelecionados']) ) {
                $obTFrotaVeiculoCombustivel->setDado( 'cod_veiculo'   , $_REQUEST['inCodVeiculo'] );
                foreach ($_REQUEST['inCodCombustivelSelecionados'] AS $stKey=>$stValue) {
                    $obTFrotaVeiculoCombustivel->setDado( 'cod_combustivel' , $stValue );
                    $obTFrotaVeiculoCombustivel->inclusao();
                }
            }

            if ($_REQUEST['stOrigemBem'] == 'proprio') {
                if ($_REQUEST['inCodResponsavel'] != '') {
                    $obTPatrimonioBemResponsavel->setDado('cod_bem', $_REQUEST['inCodBem']);
                    $obTPatrimonioBemResponsavel->setDado('numcgm', $_REQUEST['inCodResponsavel']);
                    $obTPatrimonioBemResponsavel->setDado('dt_inicio', $_REQUEST['dtInicio']);
                    $obTPatrimonioBemResponsavel->inclusao();
                }
            } else {
                //verifica se o ultimo responsavel e diferente do formulario
                if ( $rsResponsavel->getNumLinhas() <= 0 OR $rsResponsavel->getCampo('numcgm') <> $_REQUEST['inCodResponsavel'] OR  implode('',array_reverse(explode('/',$rsResponsavel->getCampo('dt_inicio')))) <> implode('',array_reverse(explode('/',$_REQUEST['dtInicio']))) ) {
                    //seta os dados da table frota.veiculo_terceiros_responsavel
                    $inCodVeiculo = $_REQUEST['inCodVeiculo'];
                    $obTFrotaVeiculoTerceirosResponsavel->setDado('cod_veiculo',$inCodVeiculo);
                    $obTFrotaVeiculoTerceirosResponsavel->setDado('numcgm', $_REQUEST['inCodResponsavel'] );
                    $obTFrotaVeiculoTerceirosResponsavel->setDado('dt_inicio', $_REQUEST['dtInicio'] );
                    $obTFrotaVeiculoTerceirosResponsavel->inclusao();

                    //se tem um responsavel ja cadastrado, coloca a data de termino
                    if ( $rsResponsavel->getNumLinhas() > 0 ) {
                        $obTFrotaVeiculoTerceirosResponsavel->setDado('numcgm',$rsResponsavel->getCampo('numcgm'));
                        $obTFrotaVeiculoTerceirosResponsavel->setDado('dt_inicio',$rsResponsavel->getCampo('dt_inicio'));
                        $obTFrotaVeiculoTerceirosResponsavel->setDado('dt_fim',$_REQUEST['dtInicio']);
                        $obTFrotaVeiculoTerceirosResponsavel->setDado('timestamp', $rsResponsavel->getCampo('timestamp'));
                        $obTFrotaVeiculoTerceirosResponsavel->alteracao();
                    }                                      
                }
                $obTPatrimonioVeiculoUniorcam->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
                $obTPatrimonioVeiculoUniorcam->recuperaPorChave( $rsVeiculoUniorcam );

                if(!empty($_REQUEST['inCodEntidade'])) {
                    if(empty($_REQUEST['inCodOrgao'])) {
                        SistemaLegado::exibeAviso(urlencode('Ao selecionar uma Entidade você deve obrigatoriamente selecionar um Órgão.'),"n_incluir","erro");
                        Sessao::encerraExcecao();
                        die;
                    }
                }
                
                if(!empty($_REQUEST['inCodOrgao'])) {
                    if(empty($_REQUEST['inCodUnidade'])) {
                        SistemaLegado::exibeAviso(urlencode('Ao selecionar um Órgão você deve obrigatoriamente selecionar uma Unidade.'),"n_incluir","erro");
                        Sessao::encerraExcecao();
                        die;
                    }
                }
                
                if(!empty($_REQUEST['inCodEntidade']) && !empty($_REQUEST['inCodOrgao']) && !empty($_REQUEST['inCodUnidade'])) {
                    if($rsVeiculoUniorcam->getNumLinhas() > 0){
                        $obTPatrimonioVeiculoUniorcam->setDado('exercicio',$_REQUEST['stExercicio']);
                        $obTPatrimonioVeiculoUniorcam->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                        $obTPatrimonioVeiculoUniorcam->setDado('num_orgao',$_REQUEST['inCodOrgao']);
                        $obTPatrimonioVeiculoUniorcam->setDado('num_unidade',$_REQUEST['inCodUnidade']);
                        $obTPatrimonioVeiculoUniorcam->alteracao();
                    } else {
                        $obTPatrimonioVeiculoUniorcam->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
                        $obTPatrimonioVeiculoUniorcam->setDado('exercicio',$_REQUEST['stExercicio']);
                        $obTPatrimonioVeiculoUniorcam->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
                        $obTPatrimonioVeiculoUniorcam->setDado('num_orgao',$_REQUEST['inCodOrgao']);
                        $obTPatrimonioVeiculoUniorcam->setDado('num_unidade',$_REQUEST['inCodUnidade']);
                        $obTPatrimonioVeiculoUniorcam->inclusao();
                    }
                } else {
                    $obTPatrimonioVeiculoUniorcam->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
                    $obTPatrimonioVeiculoUniorcam->exclusao();
                }
            }

            //seta os dados na table frota.veiculo_propriedade
            $obTFrotaVeiculoPropriedade->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );

            if ($_REQUEST['stOrigemBem'] == 'proprio') {

                //seta true para veiculo_propriedade.proprio
                $obTFrotaVeiculoPropriedade->setDado('proprio',true);
                $obTFrotaVeiculoPropriedade->inclusao();

                //seta os dados da table frota.proprio e inclui
                $obTFrotaProprio->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
                $obTFrotaProprio->setDado( 'cod_bem'    , $_REQUEST['inCodBem'] );

                $obTFrotaProprio->inclusao();
            } else {
                //seta false para veiculo_propriedade.proprio
                $obTFrotaVeiculoPropriedade->setDado('proprio',false);
                $obTFrotaVeiculoPropriedade->inclusao();

                //seta os dados da table frota.terceiros
                $obTFrotaTerceiros->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
                $obTFrotaTerceiros->setDado( 'cod_proprietario', $_REQUEST['inCodProprietario'] );
                $obTFrotaTerceiros->inclusao();

                $arOrgao = $_REQUEST['hdnUltimoOrgaoSelecionado'];
                //seta os dados da table frota.terceiros_historico
                $obTFrotaTerceirosHistorico->setDado( 'cod_veiculo'     , $_REQUEST['inCodVeiculo'] );
                $obTFrotaTerceirosHistorico->setDado( 'cod_orgao'       , $arOrgao );
                $obTFrotaTerceirosHistorico->setDado( 'cod_unidade'     , $_REQUEST['inCodUnidade'] );
                $obTFrotaTerceirosHistorico->setDado( 'cod_departamento', $_REQUEST['inCodDepartamento'] );
                $obTFrotaTerceirosHistorico->setDado( 'cod_setor'       , $_REQUEST['inCodSetor'] );
                $obTFrotaTerceirosHistorico->setDado( 'cod_local'       , $_REQUEST['inCodLocal'] );
                //recupera o ano_exercicio da localizacao
                $arLocalizacao = explode('.',$_REQUEST['stCodLocalizacao']);
                $arLocalizacao[4] = explode('/',$arLocalizacao[4]);
                $obTFrotaTerceirosHistorico->setDado( 'ano_exercicio'    , Sessao::getExercicio() );
                $obTFrotaTerceirosHistorico->inclusao();
            }
            
            $obTFrotaControleInterno->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
            $obTFrotaControleInterno->setDado( 'exercicio', Sessao::getExercicio() );
            $obTFrotaControleInterno->setDado( 'verificado', $_REQUEST['boControleInterno'] );
            
            $obTFrotaControleInterno->recuperaTodos($rsRecordSet, " WHERE cod_veiculo = '".$_REQUEST['inCodVeiculo']."' AND exercicio = '".Sessao::getExercicio()."'");
            
            if ($rsRecordSet->getNumLinhas() > 0) {
                $obTFrotaControleInterno->alteracao();
            } else {
                $obTFrotaControleInterno->inclusao();
            }
            
            //deleta os documentos selecionados
            if ( is_array( Sessao::read('arDocumentosExcluidos') ) ) {
                foreach ( Sessao::read('arDocumentosExcluidos') AS $arTemp ) {
                    //deleta da table frota.veiculo_documento_empenho
                    $obTFrotaVeiculoDocumentoEmpenho->setDado('cod_veiculo'  , $_REQUEST['inCodVeiculo']);
                    $obTFrotaVeiculoDocumentoEmpenho->setDado('cod_documento', $arTemp['cod_documento'] );
                    $obTFrotaVeiculoDocumentoEmpenho->setDado('exercicio'    , $arTemp['ano_exercicio'] );
                    $obTFrotaVeiculoDocumentoEmpenho->exclusao();

                    //deleta da table frota.veiculo_documento
                    $obTFrotaVeiculoDocumento->setDado('cod_veiculo'  , $_REQUEST['inCodVeiculo']);
                    $obTFrotaVeiculoDocumento->setDado('cod_documento', $arTemp['cod_documento'] );
                    $obTFrotaVeiculoDocumento->setDado('exercicio'    , $arTemp['ano_exercicio'] );
                    $obTFrotaVeiculoDocumento->exclusao();
               }
            }

            //seta os dados da table frota.veiculo_documento e inclui
            if ( is_array( Sessao::read('arDocumentos') ) ) {
                foreach ( Sessao::read('arDocumentos') AS $arTemp ) {
                    $obTFrotaVeiculoDocumento->setDado( 'cod_veiculo'   , $_REQUEST['inCodVeiculo'] );
                    $obTFrotaVeiculoDocumento->setDado( 'cod_documento' , $arTemp['cod_documento'] );
                    $obTFrotaVeiculoDocumento->setDado( 'mes'           , $arTemp['mes'] );
                    $obTFrotaVeiculoDocumento->setDado( 'exercicio'     , $arTemp['ano_documento'] );
                    $obTFrotaVeiculoDocumento->recuperaPorChave( $rsDocumentos );
                    if ( $rsDocumentos->getNumLinhas() > 0) {
                        $obTFrotaVeiculoDocumento->alteracao();
                    } else {
                        $obTFrotaVeiculoDocumento->inclusao();
                    }
                    //se estiver pago, inclui também na table frota.veiculo_documento_empenho e inclui
                    if ($arTemp['situacao'] == 1) {
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_veiculo'       , $_REQUEST['inCodVeiculo'] );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_documento'     , $arTemp['cod_documento'] );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'exercicio'         , $arTemp['ano_documento'] );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_empenho'       , $arTemp['cod_empenho']   );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'cod_entidade'      , $arTemp['cod_entidade']  );
                        $obTFrotaVeiculoDocumentoEmpenho->setDado( 'exercicio_empenho' , $arTemp['exercicio_empenho'] );
                        $obTFrotaVeiculoDocumentoEmpenho->recuperaPorChave( $rsDocumentos );
                        if ( $rsDocumentos->getNumLinhas() > 0 ) {
                            $obTFrotaVeiculoDocumentoEmpenho->alteracao();
                        } else {
                            $obTFrotaVeiculoDocumentoEmpenho->inclusao();
                        }
                    }
                }  
            }

            if ( is_array( Sessao::read('arLocacoesExcluidas') ) ) {
                foreach ( Sessao::read('arLocacoesExcluidas') AS $arTemp ) {
                    $obTFrotaVeiculoLocacao->setDado( 'id'           , $arTemp['id_locacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_processo' , substr($arTemp['stProcessoLocacao'],0,5) );
                    $obTFrotaVeiculoLocacao->setDado( 'ano_exercicio', substr($arTemp['stProcessoLocacao'],6,4) );
                    $obTFrotaVeiculoLocacao->setDado( 'cgm_locatario', $arTemp['inCodLocatario'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_contrato'  , $arTemp['dtContrato'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_inicio'    , $arTemp['dtIniLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_termino'   , $arTemp['dtFimLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'exercicio'    , $arTemp['stExercicioLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_entidade' , $arTemp['inCodEntidadeLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_empenho'  , $arTemp['inNumEmpenhoLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'vl_locacao'   , $arTemp['inValorLocacao'] );

                    $obTFrotaVeiculoLocacao->recuperaPorChave($rsVeiculoLocacaoExclusao);

                    if ($rsVeiculoLocacaoExclusao->getNumLinhas() > 0) {
                        $obTFrotaVeiculoLocacao->exclusao();
                    }
                }
            }

            //seta os dados da table frota.veiculo_locacao e inclui
            if ( is_array( Sessao::read('arLocacoes') ) ) {
                foreach ( Sessao::read('arLocacoes') AS $arTemp ) {
                    $obTFrotaVeiculoLocacao->setDado( 'cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_processo' , substr($arTemp['stProcessoLocacao'],0,5) );
                    $obTFrotaVeiculoLocacao->setDado( 'ano_exercicio', substr($arTemp['stProcessoLocacao'],6,4) );
                    $obTFrotaVeiculoLocacao->setDado( 'cgm_locatario', $arTemp['inCodLocatario'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_contrato'  , $arTemp['dtContrato'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_inicio'    , $arTemp['dtIniLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'dt_termino'   , $arTemp['dtFimLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'exercicio'    , $arTemp['stExercicioLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_entidade' , $arTemp['inCodEntidadeLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'cod_empenho'  , $arTemp['inNumEmpenhoLocacao'] );
                    $obTFrotaVeiculoLocacao->setDado( 'vl_locacao'   , $arTemp['inValorLocacao'] );

                    if ($arTemp['id_locacao'] != '') {
                        $obTFrotaVeiculoLocacao->setDado( 'id'       , $arTemp['id_locacao'] );
                    } else {
                        $obTFrotaVeiculoLocacao->proximoCod($inCodNovoLocacao);
                        $obTFrotaVeiculoLocacao->setDado( 'id'       , $inCodNovoLocacao );
                    }

                    $obTFrotaVeiculoLocacao->recuperaPorChave($rsVeiculoLocacao);

                    if ($rsVeiculoLocacao->getNumLinhas() > 0) {
                        $obTFrotaVeiculoLocacao->alteracao();
                    } else {
                        $obTFrotaVeiculoLocacao->inclusao();
                    }
                }
            } else {
                $obTFrotaVeiculoLocacao->recuperaTodos($rsVeiculoLocacao, " WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo'] );
                if ($rsVeiculoLocacao->getNumLinhas() > 0) {
                    foreach ($rsVeiculoLocacao->getElementos() as $arTemp => $value) {
                        $obTFrotaVeiculoLocacao->setDado( 'id' , $value['id'] );
                        $obTFrotaVeiculoLocacao->exclusao();
                    }
                }
            }

            if ( is_array( Sessao::read('arCessoesExcluidas') ) ) {
                foreach ( Sessao::read('arCessoesExcluidas') AS $arTemp ) {
                    $obTFrotaVeiculoCessao->setDado( 'id'           , $arTemp['id_cessao'] );
                    $obTFrotaVeiculoCessao->setDado( 'cod_veiculo'  , $_REQUEST['inCodVeiculo'] );
                    $obTFrotaVeiculoCessao->setDado( 'cod_processo' , $arTemp['cod_processo'] );
                    $obTFrotaVeiculoCessao->setDado( 'exercicio'    , $arTemp['exercicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'cgm_cedente'  , $arTemp['cgm_cedente'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_inicio'    , $arTemp['dt_inicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_termino'   , $arTemp['dt_termino'] );

                    $obTFrotaVeiculoCessao->recuperaPorChave($rsVeiculoCessaoExclusao);

                    if ($rsVeiculoCessaoExclusao->getNumLinhas() > 0) {
                        $obTFrotaVeiculoCessao->exclusao();
                    }
                }
            }

            //seta os dados da table frota.veiculo_cessao e inclui
            if ( is_array( Sessao::read('arListaCessao') ) ) {
                foreach ( Sessao::read('arListaCessao') AS $arTemp ) {
                    $obTFrotaVeiculoCessao->setDado( 'cod_veiculo'   , $_REQUEST['inCodVeiculo'] );
                    $obTFrotaVeiculoCessao->setDado( 'cod_processo'  , $arTemp['cod_processo'] );
                    $obTFrotaVeiculoCessao->setDado( 'exercicio'     , $arTemp['exercicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'cgm_cedente'   , $arTemp['cgm_cedente'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_inicio'     , $arTemp['dt_inicio'] );
                    $obTFrotaVeiculoCessao->setDado( 'dt_termino'    , $arTemp['dt_termino'] );

                    if ($arTemp['id_cessao'] != '') {
                        $obTFrotaVeiculoCessao->setDado( 'id' , $arTemp['id_cessao'] );
                        $obTFrotaVeiculoCessao->recuperaPorChave($rsVeiculoCessao);
                        if ($rsVeiculoCessao->getNumLinhas() > 0) {
                            $obTFrotaVeiculoCessao->alteracao();
                        }
                    } else {
                        $obTFrotaVeiculoCessao->proximoCod($inCodNovoCessao);
                        $obTFrotaVeiculoCessao->setDado( 'id' , $inCodNovoCessao );
                        $obTFrotaVeiculoCessao->inclusao();
                    }
                }
            }

            if ($_REQUEST['inCategoriaVeiculo']) {
                $obTTCERNVeiculoCategoriaVinculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
                $obTTCERNVeiculoCategoriaVinculo->setDado('cod_categoria', $_REQUEST['inCategoriaVeiculo']);
                $obTTCERNVeiculoCategoriaVinculo->recuperaPorChave($rsVinculo);

                if ($rsVinculo->getNumLinhas() > 0) {
                    $obErro = $obTTCERNVeiculoCategoriaVinculo->alteracao();
                } else {
                    $obErro = $obTTCERNVeiculoCategoriaVinculo->inclusao();
                }
            }

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&inCodVeiculo=".Sessao::read('codVeiculoFiltro'), 'Veículo - '.$_REQUEST['inCodVeiculo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
            echo "<script>LiberaFrames(true,true);</script>";
        }
        break;

    case 'excluir' :

        //deleta da table frota.terceiros_historico
        $obTFrotaTerceirosHistorico->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaTerceirosHistorico->exclusao();

        //deleta da table frota.terceiros
        $obTFrotaTerceiros->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaTerceiros->exclusao();

        //deleta da table frota.proprio
        $obTFrotaProprio->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaProprio->exclusao();

        //deleta da table frota.veiculo_propriedade
        $obTFrotaVeiculoPropriedade->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaVeiculoPropriedade->exclusao();

        //deleta da table frota.veiculo_terceiros_responsavel
        $obTFrotaVeiculoTerceirosResponsavel->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaVeiculoTerceirosResponsavel->exclusao();
        
        //deleta da table patrimonio.veiculo_uniorcam
        $obTPatrimonioVeiculoUniorcam->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTPatrimonioVeiculoUniorcam->exclusao();
        
        //deleta da table frota.veiculo_documento
        $obTFrotaVeiculoDocumento->setDado('cod_veiculo',$_REQUEST['inCodVeiculo']);
        $obTFrotaVeiculoDocumento->exclusao();

        //recupera as autorizacoes para o veiculo
        $obTFrotaAutorizacao->recuperaTodos( $rsAutorizacao, ' WHERE cod_veiculo = '.$_REQUEST['inCodVeiculo'].' ' );

        //deleta da table frota.autorizacao
        while ( !$rsAutorizacao->eof() ) {
            $obTFrotaAutorizacao->setDado('cod_autorizacao',$rsAutorizacao->getCampo('cod_autorizacao'));
            $obTFrotaAutorizacao->setDado('exercicio', $rsAutorizacao->getCampo('exercicio') );
            $obTFrotaAutorizacao->exclusao();
            $rsAutorizacao->proximo();
        }

        //deleta da table frota.veiculo_combustivel
        $obTFrotaVeiculoCombustivel->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculoCombustivel->exclusao();

        //deleta da table frota.motorista_veiculo
        $obTFrotaMotoristaVeiculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaMotoristaVeiculo->exclusao();

        //deleta da table frota.controle_interno
        $obTFrotaControleInterno->setDado( 'cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaControleInterno->setDado( 'exercicio', Sessao::getExercicio() );
        $obTFrotaControleInterno->exclusao();
        
        //deleta da table frota.veiculo
        $obTFrotaVeiculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo'] );
        $obTFrotaVeiculo->exclusao();

        # Caso o município faça parte do estado do RN.
        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == '20') {
            $obTTCERNVeiculoCategoriaVinculo->setDado('cod_veiculo', $_REQUEST['inCodVeiculo']);
            $obTTCERNVeiculoCategoriaVinculo->exclusao();
        }

        $obTFrotaVeiculoLocacao->recuperaTodos($rsVeiculoLocacao, " WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo'] );
        if ($rsVeiculoLocacao->getNumLinhas() > 0) {
            foreach ($rsVeiculoLocacao->getElementos() as $arTemp => $value) {
                $obTFrotaVeiculoLocacao->setDado( 'id' , $value['id'] );
                $obTFrotaVeiculoLocacao->exclusao();
            }
        }

        $obTFrotaVeiculoCessao->recuperaTodos($rsVeiculoCessao, " WHERE cod_veiculo = ".$_REQUEST['inCodVeiculo'] );
        if ($rsVeiculoCessao->getNumLinhas() > 0) {
            foreach ($rsVeiculoCessao->getElementos() as $arTemp => $value) {
                $obTFrotaVeiculoCessao->setDado( 'id' , $value['id'] );
                $obTFrotaVeiculoCessao->exclusao();
            }
        }
                
        sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,'Veículo - '.$_REQUEST['inCodVeiculo'],"excluir","excluir", Sessao::getId(), "../");

        break;
}

Sessao::encerraExcecao();
