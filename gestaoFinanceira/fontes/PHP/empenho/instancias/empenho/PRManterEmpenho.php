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
    * Página de Processamento de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterEmpenho.php 66418 2016-08-25 21:02:27Z michel $

    * Casos de uso: uc-02.01.08
                    uc-02.03.03
                    uc-02.03.02
                    uc-02.03.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";
include CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipanteDocumentos.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEmpenho";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
include( $pgJS );

$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

$stAcao = $request->get('stAcao');
//Trecho de código do filtro
$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $request->get('stDtEmpenho'));
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, "", $boTransacao);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês do Empenho encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "incluir":

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        if (!$request->get('inModalidadeLicitacao') || $request->get('inModalidadeLicitacao') == '') {
            SistemaLegado::exibeAviso("Modalidade TCMGO não informada!","n_incluir","erro");
            SistemaLegado::LiberaFrames(true,False);
            break;
        }

        if ($request->get('inModalidadeLicitacao') == '10' || $request->get('inModalidadeLicitacao') == '11') {
        
            if (!$request->get('inFundamentacaoLegal') || $request->get('inFundamentacaoLegal') == '') {
                SistemaLegado::exibeAviso("Fundamentação legal não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        
            if (!$request->get('stJustificativa') || $request->get('stJustificativa') == '') {
                SistemaLegado::exibeAviso("Justificativa não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        
            if (!$request->get('stRazao') || $request->get('stRazao') == '') {
                SistemaLegado::exibeAviso("Razão da escolha não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                break;
            }
        }
    }

    if($request->get('inCodContrato') && $request->get('stExercicioContrato')){
        if (sistemaLegado::comparaDatas($request->get('dtContrato'), $request->get('stDtEmpenho'), true)) {
            SistemaLegado::exibeAviso("Data do empenho deve ser maior ou igual a data do Contrato!","n_incluir","erro");
            SistemaLegado::LiberaFrames(true,false);
            break;
        }
    }

    $obAtributos = new MontaAtributos;
    $obAtributos->setName      ( "Atributo_" );
    $obAtributos->recuperaVetor( $arChave    );

    //Atributos Dinâmicos
    foreach ($arChave as $key=>$value) {
        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
        $inCodAtributo = $arChaves[0];
        if ( is_array($value) ) {
            $value = implode(",",$value);
        }
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
    }

    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setCodReserva( $request->get('inCodReserva') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $request->get('inCodTipo') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $request->get('nuVlReserva') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $request->get('nuVlReserva') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $request->get('stCodClassificacao') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDescricao( $request->get('stNomEmpenho') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->setCodHistorico( $request->get('inCodHistorico') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->setNumCGM( $request->get('inCodFornecedor') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $request->get('inCodTipo') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodCategoria( $request->get('inCodCategoria') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenho( $request->get('stDtEmpenho') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtVencimento( $request->get('stDtVencimento') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodCategoria( $request->get('inCodCategoria'));
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setdataEmpenho($request->get('stDtEmpenho'));
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEntidade($request->get('inCodEntidade') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setTipoEmissao('R');
    
    $obErro = $obREmpenhoEmpenhoAutorizacao->autorizarEmpenho($boTransacao);

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao);
    if ( !$obErro->ocorreu() && $inCodUF == 9 && Sessao::getExercicio() >= 2012) {

        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOEmpenhoModalidade.class.php';
        $obTEmpenhoModalidade = new TTCMGOEmpenhoModalidade();

        if ($request->get('inModalidadeLicitacao') == '10' || $request->get('inModalidadeLicitacao') == '11') {

            $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $request->get('inCodEntidade'));
            $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho());
            $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
            $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $request->get('inModalidadeLicitacao'));
            $obTEmpenhoModalidade->setDado( 'cod_fundamentacao' , $request->get('inFundamentacaoLegal'));
            $obTEmpenhoModalidade->setDado( 'justificativa'     , $request->get('stJustificativa'));
            $obTEmpenhoModalidade->setDado( 'razao_escolha'     , $request->get('stRazao'));
            $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);

        } else {

            $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $request->get('inCodEntidade'));
            $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho());
            $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
            $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $request->get('inModalidadeLicitacao'));
            $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);
        }

        //Informações sobre a licitação
        if ($request->get('stProcessoLicitacao') || $request->get('stExercicioLicitacao') || $request->get('stProcessoAdministrativo')) {
            include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOProcessos.class.php';
            $obTTCMGOProcessos = new TTCMGOProcessos();
            $obTTCMGOProcessos->setDado( 'cod_empenho', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCMGOProcessos->setDado( 'cod_entidade', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTCMGOProcessos->setDado( 'exercicio', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getExercicio() );
            $obTTCMGOProcessos->setDado( 'numero_processo', $request->get('stProcessoLicitacao') );
            $obTTCMGOProcessos->setDado( 'exercicio_processo', $request->get('stExercicioLicitacao') );
            $obTTCMGOProcessos->setDado( 'processo_administrativo', $request->get('stProcessoAdministrativo') );
            $obErro = $obTTCMGOProcessos->inclusao($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        // Adiantamentos: Faz inclusao em empenho.contrapartida_empenho
        if ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3) {
            include_once( TEMP."TEmpenhoContrapartidaEmpenho.class.php" );
            $obTEmpenhoContrapartidaEmpenho = new TEmpenhoContrapartidaEmpenho();
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTEmpenhoContrapartidaEmpenho->setDado( 'conta_contrapartida' , $_POST['inCodContrapartida']        );
            $obErro = $obTEmpenhoContrapartidaEmpenho->inclusao($boTransacao);
        }
    }

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 20) {
        if ( !$obErro->ocorreu() ) {
            // Relaciona o empenho com o fundeb
            include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNFundebEmpenho.class.php" );
            $obTTCERNFundebEmpenho = new TTCERNFundebEmpenho();
            $obTTCERNFundebEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCERNFundebEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTTCERNFundebEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTTCERNFundebEmpenho->setDado( 'cod_fundeb'          , $request->get('inCodFundeb') );
            $obErro = $obTTCERNFundebEmpenho->inclusao($boTransacao);

            // Relaciona o empenho com o royalties
            include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNRoyaltiesEmpenho.class.php" );
            $obTTCERNRoyaltiesEmpenho = new TTCERNRoyaltiesEmpenho();
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_entidade'        , $_POST['inCodEntidade']             );
            $obTTCERNRoyaltiesEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()                  );
            $obTTCERNRoyaltiesEmpenho->setDado( 'cod_royalties'       , $request->get('inCodRoyalties') );
            $obErro = $obTTCERNRoyaltiesEmpenho->inclusao($boTransacao);
        }
    }

    if ( !$obErro->ocorreu() ) {
        // Relaciona o empenho a contrato
        if($request->get('inCodContrato') && $request->get('stExercicioContrato')){
            include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';
            $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato();
            $obTEmpenhoEmpenhoContrato->setDado( "exercicio"          , Sessao::getExercicio());
            $obTEmpenhoEmpenhoContrato->setDado( "cod_entidade"       , $request->get('inCodEntidade'));
            $obTEmpenhoEmpenhoContrato->setDado( "cod_empenho"        , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho());
            $obTEmpenhoEmpenhoContrato->setDado( "num_contrato"       , $request->get('inCodContrato'));
            $obTEmpenhoEmpenhoContrato->setDado( "exercicio_contrato" , $request->get('stExercicioContrato'));
            $obErro = $obTEmpenhoEmpenhoContrato->inclusao($boTransacao);

            if ( !$obErro->ocorreu() ) {
                list($inNumAditivo, $stExercicioAditivo) = explode('/', $request->get('inNumAditivo'));

                // Relaciona o empenho a aditivo de contrato
                if(!empty($inNumAditivo) && !empty($stExercicioAditivo)){
                    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContratoAditivo.class.php';
                    $obTEmpenhoEmpenhoContratoAditivo = new TEmpenhoEmpenhoContratoAditivo();
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "exercicio_empenho"  , Sessao::getExercicio()              );
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "cod_entidade"       , $request->get('inCodEntidade')      );
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "cod_empenho"        , $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "num_contrato"       , $request->get('inCodContrato')      );
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "exercicio_contrato" , $request->get('stExercicioContrato'));
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "num_aditivo"        , $inNumAditivo                       );
                    $obTEmpenhoEmpenhoContratoAditivo->setDado( "exercicio_aditivo"  , $stExercicioAditivo                 );

                    $obErro = $obTEmpenhoEmpenhoContratoAditivo->inclusao($boTransacao);
                }
            }
        }
    }

    //Atualizacao do cod_marca na tabela de empenho.item_pre_empenho
    if ( !$obErro->ocorreu() ) {
        $arItens = Sessao::read('arItens');
        if (!empty($arItens)) {
            include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoItemPreEmpenho.class.php';
            $obTEmpenhoItemPreEmpenho = new TEmpenhoItemPreEmpenho();
            $obTEmpenhoItemPreEmpenho->setDado('exercicio'      , Sessao::getExercicio() );
            $obTEmpenhoItemPreEmpenho->setDado('cod_pre_empenho', $request->get('inCodPreEmpenho') );
            foreach ($arItens as $key => $value) {
                if ( !$obErro->ocorreu() ) {
                    $obTEmpenhoItemPreEmpenho->setDado('num_item'  , $value['num_item']   );
                    $obTEmpenhoItemPreEmpenho->recuperaPorChave($rsItemPreEmpenho,$boTransacao);
                    if ( $rsItemPreEmpenho->getNumLinhas() > 0 ) {
                        $obTEmpenhoItemPreEmpenho->setDado('cod_grandeza'  , $rsItemPreEmpenho->getCampo('cod_grandeza')  );
                        $obTEmpenhoItemPreEmpenho->setDado('quantidade'    , $rsItemPreEmpenho->getCampo('quantidade')  );
                        $obTEmpenhoItemPreEmpenho->setDado('cod_unidade'   , $rsItemPreEmpenho->getCampo('cod_unidade')  );
                        $obTEmpenhoItemPreEmpenho->setDado('nom_unidade'   , $rsItemPreEmpenho->getCampo('nom_unidade')  );
                        $obTEmpenhoItemPreEmpenho->setDado('sigla_unidade' , $rsItemPreEmpenho->getCampo('sigla_unidade')  );
                        $obTEmpenhoItemPreEmpenho->setDado('vl_total'      , $rsItemPreEmpenho->getCampo('vl_total')  );
                        $obTEmpenhoItemPreEmpenho->setDado('nom_item'      , $rsItemPreEmpenho->getCampo('nom_item')  );
                        $obTEmpenhoItemPreEmpenho->setDado('complemento'   , $rsItemPreEmpenho->getCampo('complemento')  );
                        $obTEmpenhoItemPreEmpenho->setDado('cod_item'      , $rsItemPreEmpenho->getCampo('cod_item')  );
                        $obTEmpenhoItemPreEmpenho->setDado('cod_marca'     , $value['cod_marca']  );
                        $boErro = $obTEmpenhoItemPreEmpenho->alteracao( $boTransacao );
                    }
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        /* Salvar assinaturas configuráveis se houverem */
        $arAssinaturas = Sessao::read('assinaturas');
    if (array_key_exists('selecionadas', $arAssinaturas)) {
        $inCountArrayAssinaturas = count($arAssinaturas['selecionadas']);
    } else {
        $inCountArrayAssinaturas = 0;
    }

    if ( isset($arAssinaturas) && $inCountArrayAssinaturas > 0 ) {
        include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php" );
        $arAssinatura = $arAssinaturas['selecionadas'];
        $obTEmpenhoEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
        $obTEmpenhoEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
        $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_entidade', $_POST['inCodEntidade'] );
        $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_empenho', $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho() );
        $arPapel = $obTEmpenhoEmpenhoAssinatura->arrayPapel();

            $boInserir = 'true';
            $inCount = 0;
            $arAssinaInseridos = array();
            $arAssinaturaTemp = array_reverse($arAssinatura);
            foreach ($arAssinaturaTemp as $arAssina) {
                if ( !isset($arAssina['papel']) ) {
                    SistemaLegado::exibeAviso("Selecione um papel para cada nome selecionado!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,False);
                    exit;
                } else {
                    $stPapel = $arAssina['papel'];
                }

                if (array_key_exists($stPapel, $arPapel)) {
                    $inNumAssina = $arPapel[$stPapel];
                } elseif (array_search($stPapel, $arPapel)) {
                    $inNumAssina = $stPapel;
                }

                foreach ($arAssinaInseridos as $inCGMTemp => $stPapelTemp) {
                    if ($arAssina['inCGM'] != $inCGMTemp && $inNumAssina != $stPapelTemp) {
                        $boInserir = 'true';
                    } else {
                        $boInserir = 'false';
                        break;
                    }
                }
                if ($boInserir == 'true') {
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'num_assinatura', $inNumAssina );
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'numcgm',$arAssina['inCGM'] );
                    $obTEmpenhoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                    $obErro = $obTEmpenhoEmpenhoAssinatura->inclusao($boTransacao);
                    $arAssinaInseridos[$arAssina['inCGM']] = $inNumAssina;
                }
                $inCount++;
            }
            unset($obTEmpenhoEmpenhoAssinatura);
            // Limpa Sessao->assinaturas
            $arAssinaturas = array( 'disponiveis'=>array(), 'papeis'=>array(), 'selecionadas'=>array() );
            Sessao::write('assinaturas', $arAssinaturas);
    }
    }
    $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obREmpenhoEmpenhoAutorizacao->obTEmpenhoEmpenhoAutorizacao);
    if ( !$obErro->ocorreu() ) {
        if ($request->get('boEmitirLiquidacao') == "S") {
            $pgProx = CAM_GF_EMP_INSTANCIAS."liquidacao/FMManterLiquidacao.php";
            $stFiltroLiquidacao  = "&inCodEmpenho=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho();
            $stFiltroLiquidacao .= "&inCodPreEmpenho=".$_POST['inCodPreEmpenho'];
            $stFiltroLiquidacao .= "&inCodEntidade=".$_POST['inCodEntidade'];
            $stFiltroLiquidacao .= "&inCodReserva=".$_POST['inCodReserva'];
            $stFiltroLiquidacao .= "&inCodAutorizacao=".$_POST['inCodAutorizacao'];
            $stFiltroLiquidacao .= "&dtExercicioEmpenho=".Sessao::getExercicio();
            $stFiltroLiquidacao .= "&stEmitirEmpenho=S";
            $stFiltroLiquidacao .= "&stAcaoEmpenho=".$stAcao;
            $stFiltroLiquidacao .= "&pgProxEmpenho=".$pgFilt;
            $stFiltroLiquidacao .= "&acao=812&modulo=10&funcionalidade=202";
            $stFiltroLiquidacao .= "&acaoEmpenho=256&moduloEmpenho=10&funcionalidadeEmpenho=82";
            print '<script type="text/javascript">
                        mudaMenu         ( "Liquidação","202" );
                   </script>';
            SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId()."&stAcao=liquidar".$stFiltroLiquidacao, $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro, $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        }
        $stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEmpenho=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho(). "&inCodEntidade=" .$_POST['inCodEntidade']."&acao=" . Sessao::read('acao');
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    break;
}

?>