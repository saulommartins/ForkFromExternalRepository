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
    * Página de Processamento de Empenho Diverso
    * Data de Criação   : 27/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Id: PRManterEmpenhoDiversos.php 66418 2016-08-25 21:02:27Z michel $

    * Casos de uso: uc-02.01.08
                    uc-02.03.03
                    uc-02.03.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipanteDocumentos.class.php";

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEmpenho";
if ($request->get('obHdnBoComplementar') == 1) {
    $pgFormDiverso = "FMManterEmpenhoComplementar.php";
} else {
    $pgFormDiverso = "FMManterEmpenhoDiversos.php";
}

$pgProcDiverso = "PRManterEmpenhoDiversos.php";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJS          = "JS".$stPrograma.".js";

$stCaminho = CAM_GF_EMP_INSTANCIAS."autorizacao/OCRelatorioAutorizacao.php";

include( $pgJS );

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obREmpenhoEmpenho = new REmpenhoEmpenho;

//Atributos Dinâmicos
//-------->
foreach ($arChave as $key=>$value) {
    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
    $inCodAtributo = $arChaves[0];
    if ( is_array($value) ) {
        $value = implode(",",$value);
    }
    $obREmpenhoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
}
//<--------
$stAcao = $request->get('stAcao');

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $request->get('stDtEmpenho'));
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9,"", $boTransacao);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ',$boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::exibeAviso(urlencode("Mês do Empenho encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "incluir":
        if (( $request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3) && (!$request->get('inCodContrapartida')) ) {
            SistemaLegado::exibeAviso("Campo Contrapartida inválido!","n_incluir","erro");
            SistemaLegado::LiberaFrames(true,false);
            exit();
        }
        if (!$request->get('inCodFornecedor')) {
            SistemaLegado::exibeAviso("Fornecedor não informado!","n_incluir","erro");
            SistemaLegado::LiberaFrames(true,false);
            exit();
        }

        $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
            if (!$request->get('inModalidadeLicitacao') || $request->get('inModalidadeLicitacao') == '') {
                SistemaLegado::exibeAviso("Modalidade TCMGO não informada!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,false);
                exit();
            }

            if ($request->get('inModalidadeLicitacao') == '10' || $request->get('inModalidadeLicitacao') == '11') {

                if (!$request->get('inFundamentacaoLegal') || $request->get('inFundamentacaoLegal') == '') {
                    SistemaLegado::exibeAviso("Fundamentação legal não informada!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,false);
                    exit();
                }

                if (!$request->get('stJustificativa') || $request->get('stJustificativa') == '') {
                    SistemaLegado::exibeAviso("Justificativa não informada!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,false);
                    exit();
                }

                if (!$request->get('stRazao') || $request->get('stRazao') == '') {
                    SistemaLegado::exibeAviso("Razão da escolha não informada!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,false);
                    exit();
                }
            }
        }

        $arAtributos = explode('#', $request->get('HdnAtributos'));

        foreach ($arAtributos as $arAtr) {
            if ($arAtr) {
               $arAtr = explode(',', $arAtr);
               if ($arAtr[1] == 'f') {
                   if (!$_REQUEST[$arAtr[0]]) {
                        SistemaLegado::exibeAviso("Campo ".$arAtr[2].' Não Informado!',"n_incluir","erro");
                        SistemaLegado::LiberaFrames(true,false);
                        $stErro = true;
                        break;
                   }
               }
            }
        }

        if ($stErro == true) {
            SistemaLegado::exibeAviso("Campo ".$arAtr[2].' Não Informado!',"n_incluir","erro");
            SistemaLegado::LiberaFrames(true,false);
            exit();
        }

        if($request->get('inCodContrato') && $request->get('stExercicioContrato')){
            if (sistemaLegado::comparaDatas($request->get('dtContrato'), $request->get('stDtEmpenho'), true)) {
                SistemaLegado::exibeAviso("Data do empenho deve ser maior ou igual a data do Contrato!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,false);
                exit();
            }
        }

        $obREmpenhoEmpenho->setExercicio( Sessao::getExercicio() );
        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
        $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( 0 );
        $obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $request->get('inCodDespesa') );
        $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $request->get('stCodClassificacao') );
        $obREmpenhoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREmpenhoEmpenho->obRCGM->setNumCGM($request->get('inCodFornecedor'));
        $obREmpenhoEmpenho->obREmpenhoHistorico->setCodHistorico( $request->get('inCodHistorico') );
        $obREmpenhoEmpenho->setDescricao( str_replace(chr(13),'',$request->get('stDescricao')) );
        $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->setCodTipo( $request->get('inCodTipo') );
        $obREmpenhoEmpenho->setDtEmpenho( $request->get('stDtEmpenho') );
        $obREmpenhoEmpenho->setDtVencimento( $request->get('stDtVencimento') );
        $obREmpenhoEmpenho->setCodCategoria( $request->get('inCodCategoria') );
        $obREmpenhoEmpenho->obROrcamentoReservaSaldos->setVlReserva( $request->get('nuVlReserva') );
        $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $request->get('hdnOrgaoOrcamento'));
        $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($request->get('hdnUnidadeOrcamento'));

        if ( Sessao::read('arItens') ) {
            $arItensSessao = array();
            $arItensSessao = Sessao::read('arItens');
            foreach ($arItensSessao as $arItemPreEmpenho) {
                $obREmpenhoEmpenho->addItemPreEmpenho();
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNumItem    ( $arItemPreEmpenho["num_item"    ] );
                
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setQuantidade ( $arItemPreEmpenho['quantidade'  ] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNomUnidade ( $arItemPreEmpenho["nom_unidade" ] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setValorTotal ( $arItemPreEmpenho["vl_total"    ] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNomItem    ( str_replace(chr(13),'',$arItemPreEmpenho["nom_item"    ]) );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setComplemento( $arItemPreEmpenho["complemento" ] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->setCodUnidade( $arItemPreEmpenho['cod_unidade'] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->obRGrandeza->setCodGrandeza( $arItemPreEmpenho["cod_grandeza"] );
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->obRUnidadeMedida->consultar($rsUnidade, $boTransacao);
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setSiglaUnidade( $rsUnidade->getCampo('simbolo') );                
                $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setCodigoMarca ( $arItemPreEmpenho['cod_marca'] );                                
                if($request->get('stTipoItem')=='Catalogo'){
                    $obREmpenhoEmpenho->roUltimoItemPreEmpenho->setCodItemPreEmp    ( $arItemPreEmpenho["cod_item"    ] );
                }
 
            }
        }
        $nuVlSaldo   = str_replace(".", "" , $request->get('flVlSaldo')     );
        $nuVlSaldo   = str_replace(",", ".", $nuVlSaldo                     );
        $nuVlReserva = str_replace(".", "" , $request->get('nuVlReserva')   );
        $nuVlReserva = str_replace(",", ".", $nuVlReserva                   );

        $obErro = $obREmpenhoEmpenho->emitirEmpenhoDiverso( $boTransacao );

        $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(),$boTransacao);
        if ( !$obErro->ocorreu() && $inCodUF == 9 && Sessao::getExercicio() >= 2012) {

            include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOEmpenhoModalidade.class.php';
            $obTEmpenhoModalidade = new TTCMGOEmpenhoModalidade();

            if ($request->get('inModalidadeLicitacao') == '10' || $request->get('inModalidadeLicitacao') == '11') {

                $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $request->get('inCodEntidade'));
                $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenho->getCodEmpenho());
                $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
                $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $request->get('inModalidadeLicitacao'));
                $obTEmpenhoModalidade->setDado( 'cod_fundamentacao' , $request->get('inFundamentacaoLegal'));
                $obTEmpenhoModalidade->setDado( 'justificativa'     , $request->get('stJustificativa'));
                $obTEmpenhoModalidade->setDado( 'razao_escolha'     , $request->get('stRazao'));
                $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);

            } else {

                $obTEmpenhoModalidade->setDado( 'cod_entidade'      , $request->get('inCodEntidade'));
                $obTEmpenhoModalidade->setDado( 'cod_empenho'       , $obREmpenhoEmpenho->getCodEmpenho());
                $obTEmpenhoModalidade->setDado( 'exercicio'         , Sessao::getExercicio());
                $obTEmpenhoModalidade->setDado( 'cod_modalidade'    , $request->get('inModalidadeLicitacao'));
                $obErro = $obTEmpenhoModalidade->inclusao($boTransacao);
            }

            //Informações sobre a licitação
            if ($request->get('stProcessoLicitacao') || $request->get('stExercicioLicitacao') || $request->get('stProcessoAdministrativo')) {
                include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOProcessos.class.php';
                $obTTCMGOProcessos = new TTCMGOProcessos();
                $obTTCMGOProcessos->setDado( 'cod_empenho', $obREmpenhoEmpenho->getCodEmpenho() );
                $obTTCMGOProcessos->setDado( 'cod_entidade', $obREmpenhoEmpenho->obROrcamentoEntidade->getCodigoEntidade() );
                $obTTCMGOProcessos->setDado( 'exercicio', $obREmpenhoEmpenho->getExercicio() );
                $obTTCMGOProcessos->setDado( 'numero_processo', $request->get('stProcessoLicitacao') );
                $obTTCMGOProcessos->setDado( 'exercicio_processo', $request->get('stExercicioLicitacao') );
                $obTTCMGOProcessos->setDado( 'processo_administrativo', $request->get('stProcessoAdministrativo') );
                $obErro = $obTTCMGOProcessos->inclusao($boTransacao);
            }
        }

        if ( !$obErro->ocorreu() ) {
            // Adiantamentos: Faz inclusao em empenho.contrapartida_empenho
            if ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3) {
                 include_once TEMP."TEmpenhoContrapartidaEmpenho.class.php";
                 $obTEmpenhoContrapartidaEmpenho = new TEmpenhoContrapartidaEmpenho();
                 $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenho->getCodEmpenho() );
                 $obTEmpenhoContrapartidaEmpenho->setDado( 'cod_entidade'        , $request->get('inCodEntidade')      );
                 $obTEmpenhoContrapartidaEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()              );
                 $obTEmpenhoContrapartidaEmpenho->setDado( 'conta_contrapartida' , $request->get('inCodContrapartida') );
                 $obErro = $obTEmpenhoContrapartidaEmpenho->inclusao($boTransacao);
            }
        }

        if ( !$obErro->ocorreu() ) {
            // Empenho Complemetar: Faz inclusao em empenho.empenho_complementar
           if ($request->get('obHdnBoComplementar') == 1) {
                include_once TEMP."TEmpenhoEmpenhoComplementar.class.php";
                $obTEmpenhoEmpenhoComplementar = new TEmpenhoEmpenhoComplementar();
                $obTEmpenhoEmpenhoComplementar->setDado( 'cod_empenho'         , $obREmpenhoEmpenho->getCodEmpenho() );
                $obTEmpenhoEmpenhoComplementar->setDado( 'cod_entidade'        , $request->get('inCodEntidade')      );
                $obTEmpenhoEmpenhoComplementar->setDado( 'exercicio'           , Sessao::getExercicio()              );
                $obTEmpenhoEmpenhoComplementar->setDado( 'cod_empenho_original' , $request->get('inCodigoEmpenho')   );
                $obErro = $obTEmpenhoEmpenhoComplementar->inclusao($boTransacao);
            }
        }

        if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao) == 20) {
            if ( !$obErro->ocorreu() && $request->get('obHdnBoComplementar') != 1 ) {
                // Relaciona o empenho com o fundeb
                include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNFundebEmpenho.class.php";
                $obTTCERNFundebEmpenho = new TTCERNFundebEmpenho();
                $obTTCERNFundebEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenho->getCodEmpenho() );
                $obTTCERNFundebEmpenho->setDado( 'cod_entidade'        , $request->get('inCodEntidade')      );
                $obTTCERNFundebEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()              );
                $obTTCERNFundebEmpenho->setDado( 'cod_fundeb'          , $request->get('inCodFundeb')        );
                $obErro = $obTTCERNFundebEmpenho->inclusao($boTransacao);

                // Relaciona o empenho com o royalties
                include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNRoyaltiesEmpenho.class.php";
                $obTTCERNRoyaltiesEmpenho = new TTCERNRoyaltiesEmpenho();
                $obTTCERNRoyaltiesEmpenho->setDado( 'cod_empenho'         , $obREmpenhoEmpenho->getCodEmpenho() );
                $obTTCERNRoyaltiesEmpenho->setDado( 'cod_entidade'        , $request->get('inCodEntidade')      );
                $obTTCERNRoyaltiesEmpenho->setDado( 'exercicio'           , Sessao::getExercicio()              );
                $obTTCERNRoyaltiesEmpenho->setDado( 'cod_royalties'       , $request->get('inCodRoyalties')     );
                $obErro = $obTTCERNRoyaltiesEmpenho->inclusao($boTransacao);
            }
        }

        if ( !$obErro->ocorreu() ) {
            // Relaciona o empenho a contrato
            if($request->get('inCodContrato') && $request->get('stExercicioContrato')){
                include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';
                $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato();
                $obTEmpenhoEmpenhoContrato->setDado( "exercicio"          , Sessao::getExercicio()              );
                $obTEmpenhoEmpenhoContrato->setDado( "cod_entidade"       , $request->get('inCodEntidade')      );
                $obTEmpenhoEmpenhoContrato->setDado( "cod_empenho"        , $obREmpenhoEmpenho->getCodEmpenho() );
                $obTEmpenhoEmpenhoContrato->setDado( "num_contrato"       , $request->get('inCodContrato')      );
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
                        $obTEmpenhoEmpenhoContratoAditivo->setDado( "cod_empenho"        , $obREmpenhoEmpenho->getCodEmpenho() );
                        $obTEmpenhoEmpenhoContratoAditivo->setDado( "num_contrato"       , $request->get('inCodContrato')      );
                        $obTEmpenhoEmpenhoContratoAditivo->setDado( "exercicio_contrato" , $request->get('stExercicioContrato'));
                        $obTEmpenhoEmpenhoContratoAditivo->setDado( "num_aditivo"        , $inNumAditivo                       );
                        $obTEmpenhoEmpenhoContratoAditivo->setDado( "exercicio_aditivo"  , $stExercicioAditivo                 );

                        $obErro = $obTEmpenhoEmpenhoContratoAditivo->inclusao($boTransacao);
                    }
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            /* Salvar assinaturas configuráveis se houverem */
            $arAssinaturas = Sessao::read('assinaturas');
            if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
                include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php";
                $arAssinatura = $arAssinaturas['selecionadas'];
                $obTEmpenhoEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
                $obTEmpenhoEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_entidade', $request->get('inCodEntidade') );
                $obTEmpenhoEmpenhoAssinatura->setDado( 'cod_empenho', $obREmpenhoEmpenho->getCodEmpenho() );
                $arPapel = $obTEmpenhoEmpenhoAssinatura->arrayPapel();

                $boInserir = 'true';
                $inCount = 0;
                $arAssinaInseridos = array();
                $arAssinaturaTemp = array_reverse($arAssinatura);
                foreach ($arAssinaturaTemp as $arAssina) {
                    if ( !isset($arAssina['papel']) ) {
                        SistemaLegado::exibeAviso("Selecione um papel para cada nome selecionado!","n_incluir","erro");
                        SistemaLegado::LiberaFrames(true,false);
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
                        $obTEmpenhoEmpenhoAssinatura->setDado( 'numcgm', $arAssina['inCGM'] );
                        $obTEmpenhoEmpenhoAssinatura->setDado( 'cargo', $arAssina['stCargo'] );
                        $obErro = $obTEmpenhoEmpenhoAssinatura->inclusao( $boTransacao );
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
        
    $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obREmpenhoEmpenho->obTEmpenhoEmpenho);
    
    if ( !$obErro->ocorreu() ) {
        SistemaLegado::LiberaFrames(true,false);
        if ($request->get('boEmitirLiquidacao') == "S") {
            $pgProx = CAM_GF_EMP_INSTANCIAS."liquidacao/FMManterLiquidacao.php";
            $stFiltroLiquidacao  = "&inCodEmpenho=".$obREmpenhoEmpenho->getCodEmpenho();
            $stFiltroLiquidacao .= "&inCodPreEmpenho=".$obREmpenhoEmpenho->getCodPreEmpenho();
            $stFiltroLiquidacao .= "&inCodEntidade=".$request->get('inCodEntidade');
            $stFiltroLiquidacao .= "&dtExercicioEmpenho=".Sessao::getExercicio();
            $stFiltroLiquidacao .= "&stEmitirEmpenho=S";
            $stFiltroLiquidacao .= "&stAcaoEmpenho=".$stAcao;
            $stFiltroLiquidacao .= "&pgProxEmpenho=".$pgFormDiverso;
            $stFiltroLiquidacao .= "&acao=812&modulo=10&funcionalidade=202&nivel=1&cod_gestao_pass=2&stNomeGestao=Financeira&modulos=Empenho";
            if ($request->get('obHdnBoComplementar') == 1) {
                $stFiltroLiquidacao .= "&acaoEmpenho=1856&moduloEmpenho=10&funcionalidadeEmpenho=82";
            } else {
                $stFiltroLiquidacao .= "&acaoEmpenho=822&moduloEmpenho=10&funcionalidadeEmpenho=82";
            }
            print '<script type="text/javascript">
                        mudaMenu         ( "Liquidação","202" );
                   </script>';

            SistemaLegado::alertaAviso($pgProx.'?'.Sessao::getId().'&stAcao=liquidar'.$stFiltroLiquidacao,"Emitir Empenho Diversos concluído com sucesso!(".$obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio().")", "aviso",Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgFormDiverso.'?'.Sessao::getId().$stFiltro, $obREmpenhoEmpenho->getCodEmpenho()."/".Sessao::getExercicio(), "incluir", "aviso", Sessao::getId(), "../");
        }

        if ($request->get('obHdnBoComplementar') == 1) {
            Sessao::write('acao', 1856);
        } else {
            Sessao::write('acao', 822);
        }
        $stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentario.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEmpenho=".$obREmpenhoEmpenho->getCodEmpenho(). "&inCodEntidade=".$request->get('inCodEntidade')."&acao=822";
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        $stMensagem = $obErro->getDescricao();
        if (substr($stMensagem,0,6)=='ERROR:') {
            $stMensagem = substr($stMensagem,8,strlen($stMensagem)-8);
        }

        SistemaLegado::exibeAviso(urlencode($stMensagem),"n_incluir","erro");
        SistemaLegado::LiberaFrames(true,false);
    }
    break;
}

?>