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
* Página de Processamento da Geração de Assentamento
* Data de Criação: 09/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Id: PRManterGeracaoAssentamento.php 66364 2016-08-17 21:11:39Z michel $

* Casos de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoGeradoContratoServidor.class.php";

$arLink  = Sessao::read('link');
$stAcao  = $request->get('stAcao');
$stLink  = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

$stLink .= '&inCodLotacao='      .$request->get('inCodLotacao');
$stLink .= '&inCodAssentamento=' .$request->get('inCodAssentamento');
$stLink .= '&inContrato='        .$request->get('inContrato');
$stLink .= '&boCargoExercido='   .$request->get('boCargoExercido');
$stLink .= '&inCodCargo='        .$request->get('inCodCargo');
$stLink .= '&inCodEspecialidade='.$request->get('inCodEspecialidade');
$stLink .= '&boFuncaoExercida='  .$request->get('boFuncaoExercida');
$stLink .= '&stDataInicial='     .$request->get('stDataInicial');
$stLink .= '&stDataFinal='       .$request->get('stDataFinal');
$stLink .= '&stModoGeracao='     .$request->get('stModoGeracao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterGeracaoAssentamento";
$pgForm     = "FM".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgFilt     = "FL".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgProc     = "PR".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgOcul     = "OC".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgList     = "LS".$stPrograma.".php?stAcao=".$stAcao.$stLink;
$pgJS       = "JS".$stPrograma.".js";

$arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();

$obErro = new Erro();
$obTransacao = new Transacao;
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

$obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;
$obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();

$stDirTMP = CAM_GRH_PESSOAL."tmp/";
$stDirANEXO = CAM_GRH_PESSOAL."anexos/";

switch ($stAcao) {
    case "incluir":
        $arAssentamentos = Sessao::read('arAssentamentos');
        $arContratos = array();
        $stModoGeracao = $request->get('stModoGeracao');
        $stModoGeracao = (empty($stModoGeracao)) ? $request->get('hdnModoGeracao') : $stModoGeracao;;
        $arArquivosDigitais = ( is_array( Sessao::read('arArquivosDigitais') ) ) ? Sessao::read('arArquivosDigitais') : array();
        $arArquivosDigitaisIncluir = array();
        $arArquivosDigitaisExcluir = array();

        switch ($stModoGeracao) {
            case "contrato";
            case "cgm/contrato";
                foreach ($arAssentamentos as $arAssentamento) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setRegistro($arAssentamento['inRegistro']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->consultarContrato($boTransacao);
                    $arTemp['cod_contrato']             = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->getCodContrato();
                    $arTemp['cod_assentamento']         = $arAssentamento['inCodAssentamento'];
                    $arTemp['periodo_inicial']          = $arAssentamento['stDataInicial'];
                    $arTemp['periodo_final']            = $arAssentamento['stDataFinal'];
                    $arTemp['dt_inicial']               = $arAssentamento['dtInicial'];
                    $arTemp['dt_final']                 = $arAssentamento['dtFinal'];
                    $arTemp['observacao']               = $arAssentamento['stObservacao'];
                    $arTemp['arNormas']                 = $arAssentamento['arNormas'];
                    $arTemp['inCodClassificacao']       = $arAssentamento['inCodClassificacao'];
                    $arTemp['inCodTipoClassificacao']   = $arAssentamento['inCodTipoClassificacao'];
                    $arTemp['inCodNorma']               = $arAssentamento['inCodNorma'];
                    $arTemp['inCodTipoNorma']           = $arAssentamento['inCodTipoNorma'];
                    $arTemp['hdnDataAlteracaoFuncao']   = $arAssentamento['hdnDataAlteracaoFuncao'];
                    $arTemp['inCodProgressao']          = $arAssentamento['inCodProgressao'];
                    $arTemp['inCodRegime']              = $arAssentamento['inCodRegime'];
                    $arTemp['inCodSubDivisao']          = $arAssentamento['inCodSubDivisao'];
                    $arTemp['stSubDivisao']             = $arAssentamento['stSubDivisao'];
                    $arTemp['stCargo']                  = $arAssentamento['stCargo'];
                    $arTemp['inCodEspecialidadeCargo']  = $arAssentamento['inCodEspecialidadeCargo'];
                    $arTemp['stEspecialidadeCargo']     = $arAssentamento['stEspecialidadeCargo'];
                    $arTemp['inCodRegimeFuncao']        = $arAssentamento['inCodRegimeFuncao'];
                    $arTemp['stRegimeFuncao']           = $arAssentamento['stRegimeFuncao'];
                    $arTemp['inCodSubDivisaoFuncao']    = $arAssentamento['inCodSubDivisaoFuncao'];
                    $arTemp['stSubDivisaoFuncao']       = $arAssentamento['stSubDivisaoFuncao'];
                    $arTemp['inCodFuncao']              = $arAssentamento['inCodFuncao'];
                    $arTemp['stFuncao']                 = $arAssentamento['stFuncao'];
                    $arTemp['inCodEspecialidadeFuncao'] = $arAssentamento['inCodEspecialidadeFuncao'];
                    $arTemp['stEspecialidadeFuncao']    = $arAssentamento['stEspecialidadeFuncao'];
                    $arTemp['dtDataAlteracaoFuncao']    = $arAssentamento['dtDataAlteracaoFuncao'];
                    $arTemp['stHorasMensais']           = $arAssentamento['stHorasMensais'];
                    $arTemp['stHorasSemanais']          = $arAssentamento['stHorasSemanais'];
                    $arTemp['inCodPadrao']              = $arAssentamento['inCodPadrao'];
                    $arTemp['stPadrao']                 = $arAssentamento['stPadrao'];
                    $arTemp['inSalario']                = $arAssentamento['inSalario'];
                    $arTemp['dtVigenciaSalario']        = $arAssentamento['dtVigenciaSalario'];
                    if($stModoGeracao == 'contrato')
                        $arTemp['inChave']              = $arTemp['cod_contrato'];
                    else
                        $arTemp['inChave']              = $arAssentamento['inRegistro'];
                    $arTemp['inId']                     = $arAssentamento['inId'];

                    $arContratos[] = $arTemp;
                }
            break;
            case "cargo";
                $arContratosSearch = array();
                foreach ($arAssentamentos as $arAssentamento) {
                    $rsContratos = new RecordSet;
                    if ($arAssentamento['boCargoExercido']) {
                        if ($arAssentamento['inCodEspecialidade']) {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade($arAssentamento['inCodEspecialidade']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosCargoExercidoComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        } else {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo($arAssentamento['inCodCargo']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade("");
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosCargoExercidoComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        }
                    }
                    while ( !$rsContratos->eof() ) {
                        if ( array_search($rsContratos->getCampo('cod_contrato'),$arContratosSearch) === false ) {
                            $arTemp['cod_contrato']       = $rsContratos->getCampo('cod_contrato');
                            $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                            $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                            $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                            $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                            $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                            $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                            $arTemp['observacao']         = $arAssentamento['stObservacao'];
                            $arTemp['arNormas']           = $arAssentamento['arNormas'];
                            $arTemp['inChave']            = $arAssentamento['inCodCargo'];
                            $arTemp['inId']               = $arAssentamento['inId'];

                            $arFiltros = array();
                            $arFiltros['validacao']              = 'validar';
                            $arFiltros['inCodContrato']          = $arTemp['cod_contrato'];
                            $arFiltros['inCodAssentamento']      = $arTemp['cod_assentamento'];
                            $arFiltros['dtPeriodoInicial']       = $arTemp['periodo_inicial'];
                            $arFiltros['dtPeriodoFinal']         = $arTemp['periodo_final'];
                            $arFiltros['inCodTipoClassificacao'] = '';

                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->listarAssentamentoServidor($rsAssentamento,$arFiltros,'',$boTransacao);

                            if ( $rsAssentamento->getNumLinhas() < 1 )
                                $arContratos[] = $arTemp;
                        }
                        $arContratosSearch[] = $rsContratos->getCampo('cod_contrato');
                        $rsContratos->proximo();
                    }

                    if ($arAssentamento['boFuncaoExercida']) {
                        if ($arAssentamento['inCodEspecialidade']) {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade($arAssentamento['inCodEspecialidade']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosFuncaoExercidaComSubDivisaoAssentamento($rsContratos,$arAssentamento['inCodAssentamento'],$boTransacao);
                        } else {
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->addEspecialidade();
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->setCodCargo($arAssentamento['inCodCargo']);
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade("");
                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratosFuncaoExercidaComSubDivisaoAssentamento($rsContratos, $arAssentamento['inCodAssentamento'],$boTransacao);
                        }
                    }

                    while ( !$rsContratos->eof() ) {
                        if ( array_search($rsContratos->getCampo('cod_contrato'),$arContratosSearch) === false ) {
                            $arTemp['cod_contrato']       = $rsContratos->getCampo('cod_contrato');
                            $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                            $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                            $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                            $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                            $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                            $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                            $arTemp['observacao']         = $arAssentamento['stObservacao'];
                            $arTemp['arNormas']           = $arAssentamento['arNormas'];
                            $arTemp['inChave']            = $arAssentamento['inCodCargo'];
                            $arTemp['inId']               = $arAssentamento['inId'];

                            $arFiltros = array();
                            $arFiltros['validacao']              = 'validar';
                            $arFiltros['inCodContrato']          = $arTemp['cod_contrato'];
                            $arFiltros['inCodAssentamento']      = $arTemp['cod_assentamento'];
                            $arFiltros['dtPeriodoInicial']       = $arTemp['periodo_inicial'];
                            $arFiltros['dtPeriodoFinal']         = $arTemp['periodo_final'];
                            $arFiltros['inCodTipoClassificacao'] = '';

                            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->listarAssentamentoServidor($rsAssentamento,$arFiltros,'',$boTransacao);

                            if ( $rsAssentamento->getNumLinhas() < 1 )
                                $arContratos[] = $arTemp;
                        }
                        $arContratosSearch[] = $rsContratos->getCampo('cod_contrato');
                        $rsContratos->proximo();
                    }
                }
            break;
            case "lotacao";
                foreach ($arAssentamentos as $arAssentamento) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgaoEstruturado($arAssentamento['inCodLotacao']);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->listarOrgaoReduzido($rsOrgranogramaOrgao,'','',$boTransacao);
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgao($rsOrgranogramaOrgao->getCampo('cod_orgao'));
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->listarContratoServidorLotacaoComSubDivisaoAssentamento($rsContrato, $arAssentamento['inCodAssentamento'],$boTransacao);

                    while ( !$rsContrato->eof() ) {
                        $arTemp['cod_contrato']       = $rsContrato->getCampo('cod_contrato');
                        $arTemp['cod_assentamento']   = $arAssentamento['inCodAssentamento'];
                        $arTemp['inCodClassificacao'] = $arAssentamento['inCodClassificacao'];
                        $arTemp['periodo_inicial']    = $arAssentamento['stDataInicial'];
                        $arTemp['periodo_final']      = $arAssentamento['stDataFinal'];
                        $arTemp['dt_inicial']         = $arAssentamento['dtInicial'];
                        $arTemp['dt_final']           = $arAssentamento['dtFinal'];
                        $arTemp['observacao']         = $arAssentamento['stObservacao'];
                        $arTemp['arNormas']           = $arAssentamento['arNormas'];
                        $arTemp['inChave']            = $arAssentamento['inCodLotacao'];
                        $arTemp['inId']               = $arAssentamento['inId'];

                        $arFiltros = array();
                        $arFiltros['validacao']              = 'validar';
                        $arFiltros['inCodContrato']          = $arTemp['cod_contrato'];
                        $arFiltros['inCodAssentamento']      = $arTemp['cod_assentamento'];
                        $arFiltros['dtPeriodoInicial']       = $arTemp['periodo_inicial'];
                        $arFiltros['dtPeriodoFinal']         = $arTemp['periodo_final'];
                        $arFiltros['inCodTipoClassificacao'] = '';

                        $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->listarAssentamentoServidor($rsAssentamento,$arFiltros,'',$boTransacao);

                        if ( $rsAssentamento->getNumLinhas() < 1 )
                            $arContratos[] = $arTemp;

                        $rsContrato->proximo();
                    }
                    unset($rsContrato);
                }
            break;
        }

        include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();

        if ( !$obErro->ocorreu() ) {
            foreach ($arContratos as $keyArContrato => $arContrato) {
                $stFiltro = " WHERE cod_assentamento = ".$arContrato['cod_assentamento']." AND cod_classificacao = ".$arContrato["inCodClassificacao"];
                $inCodMotivo = SistemaLegado::pegaDado("cod_motivo","pessoal.assentamento_assentamento",$stFiltro,$boTransacao);

                //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
                if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){
                    $obRPessoalServidor->roUltimoContratoServidor->setCodContrato              ( $arContrato["cod_contrato"]           );
                    $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao          ( $arContrato["dtDataAlteracaoFuncao"]  );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["stCargo"]                );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $arContrato["inCodEspecialidadeCargo"]   );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $arContrato["inCodSubDivisao"] );
                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                    $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo,$boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        if ($rsCargo->getNumLinhas() < 1) {
                            sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                            exit;
                        }
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime( $arContrato["inCodRegimeFuncao"] );
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo  ( $arContrato["inCodFuncao"]       );

                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["inCodFuncao"]);
                        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
                        $obErro = $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao,$boTransacao);

                        if ( !$obErro->ocorreu() ) {
                            if ($rsFuncao->getNumLinhas() < 1) {
                                sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                                exit;
                            }

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $arContrato["stCargo"] );

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $arContrato["inCodEspecialidadeFuncao"]  );
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $arContrato["inCodSubDivisaoFuncao"]);

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime( $arContrato["inCodRegime"] );

                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $arContrato["inCodPadrao"]);
                            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();

                            $obRPessoalServidor->roUltimoContratoServidor->setSalario         ( $arContrato["inSalario"]         );
                            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal        ( $arContrato["stHorasMensais"]    );
                            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal       ( $arContrato["stHorasSemanais"]   );
                            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario ( $arContrato["dtVigenciaSalario"] );

                            $obErro = $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor($rsCargoServidor,$boTransacao);

                            if ( !$obErro->ocorreu() ) {

                                while ( !$rsCargoServidor->eof() ) {
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento             ( $rsCargoServidor->getCampo('cod_tipo_pagamento') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario                 ( $rsCargoServidor->getCampo('cod_tipo_salario') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao               ( $rsCargoServidor->getCampo('cod_tipo_admissao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $rsCargoServidor->getCampo('cod_vinculo') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria                     ( $rsCargoServidor->getCampo('cod_categoria') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade                      ( $rsCargoServidor->getCampo('cod_grade') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setNomeacao                                              ( $rsCargoServidor->getCampo('dt_nomeacao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setPosse                                                 ( $rsCargoServidor->getCampo('dt_posse') );
                                    $obRPessoalServidor->roUltimoContratoServidor->setAdmissao                                              ( $rsCargoServidor->getCampo('dt_admissao') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM            ( $rsCargoServidor->getCampo('numcgm_sindicato') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento           ( $rsCargoServidor->getCampo('cod_forma_pagamento') );
                                    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                    ( $rsCargoServidor->getCampo('cod_norma') );
    
                                    $rsCargoServidor->proximo();
                                }

                                $obErro = $obRPessoalServidor->roUltimoContratoServidor->alterarContrato($boTransacao);
                            }
                        }
                    }
                }

                $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;
                $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();

                if ( !$obErro->ocorreu() ) {
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato ( $arContrato['cod_contrato'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento ( $arContrato['cod_assentamento'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao                     ( $arContrato['observacao'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial                          ( $arContrato['periodo_inicial'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal                            ( $arContrato['periodo_final'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioInicial                 ( $arContrato['dt_inicial'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioFinal                   ( $arContrato['dt_final'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma                                ( $arContrato['arNormas'] );
                    $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodTipoClassificacao                    ( $arContrato['inCodTipoClassificacao'] );

                    $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma                                                    ( $arContrato['arNormas'] );
                    $obErro = $obRPessoalAssentametoGeradoContratoServidor->incluirAssentamentoGeradoContratoServidor($boTransacao);

                    //Arquivos Digitais
                    if ( !$obErro->ocorreu() ) {
                        $inCodAssentamentoGerado = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->getCodAssentamentoGerado();

                        foreach($arArquivosDigitais AS $chave => $arquivo){
                            if($arContrato['inId'] == $arquivo['inIdAssentamento']){
                                //Ajusta nome do arquivo de acordo com o contrato e assentamento gerado
                                $stArqDigitalAtualizado  = $arContrato['cod_contrato'];
                                $stArqDigitalAtualizado .= Sessao::getEntidade();
                                $stArqDigitalAtualizado .= '_'.$arContrato['inCodClassificacao'];
                                $stArqDigitalAtualizado .= '_'.$arContrato['cod_assentamento'];

                                $arDataInicial = explode('/', $arContrato['periodo_inicial']);
                                $stArqDigitalAtualizado .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];

                                if(!empty($arContrato['periodo_final'])){
                                    $arDataFinal = explode('/', $arContrato['periodo_final']);
                                    $stArqDigitalAtualizado .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                                }

                                $stArqDigitalAtualizado .= '_'.$arquivo['name'];

                                if($arquivo['arquivo_digital'] != $stArqDigitalAtualizado){
                                    //Armazena informações do arquivos antes de atualizar
                                    $stArqDigital = $arquivo['arquivo_digital'];
                                    $stArqTmp     = $arquivo['tmp_name'];
                                    $stArq        = $arquivo['stArquivo'];

                                    $stArqTmpAtualizado     = $stDirTMP.$stArqDigitalAtualizado;
                                    $stArqDigitalAtualizado = $inCodAssentamentoGerado.'_'.$stArqDigitalAtualizado;
                                    $stArqAtualizado        = $stDirANEXO.$stArqDigitalAtualizado;

                                    if($arquivo['boCopiado'] == 'FALSE' && $arquivo['boExcluido'] == 'FALSE'){
                                        if(!copy($stArqTmp,$stArqTmpAtualizado)){
                                            $obErro->setDescricao("Erro no upload do arquivo(".$arquivo['name'].")!");
                                            break;
                                        }
                                    }

                                    if ( !$obErro->ocorreu() ) {
                                        $arquivo['inChave']                 = $arContrato['cod_contrato'];
                                        $arquivo['stModoGeracao']           = 'contrato';
                                        $arquivo['inCodClassificacao']      = $arContrato['inCodClassificacao'];
                                        $arquivo['inCodAssentamento']       = $arContrato['cod_assentamento'];
                                        $arquivo['stDataInicial']           = $arContrato['periodo_inicial'];
                                        $arquivo['stDataFinal']             = $arContrato['periodo_final'];
                                        $arquivo['arquivo_digital']         = $stArqDigitalAtualizado;
                                        $arquivo['tmp_name']                = $stArqTmpAtualizado;
                                        $arquivo['stArquivo']               = $stArqAtualizado;
                                        $arquivo['inCodContrato']           = $arContrato['cod_contrato'];
                                        $arquivo['inCodAssentamentoGerado'] = $inCodAssentamentoGerado;
                                    }
                                }else{
                                    $arquivo['arquivo_digital']         = $inCodAssentamentoGerado.'_'.$arquivo['arquivo_digital'];
                                    $arquivo['stArquivo']               = $stDirANEXO.$arquivo['arquivo_digital'];
                                    $arquivo['inCodContrato']           = $arContrato['cod_contrato'];
                                    $arquivo['inCodAssentamentoGerado'] = $inCodAssentamentoGerado;
                                }

                                if($arquivo['boCopiado'] == 'FALSE' && $arquivo['boExcluido'] == 'FALSE'){
                                    //Salva Arquivo Digital
                                    $arArquivosDigitaisIncluir[] = $arquivo;
                                }elseif($arquivo['boCopiado'] == 'FALSE' && $arquivo['boExcluido'] == 'TRUE'){
                                    //Salva Arquivo Digital para Exclusão
                                    $arArquivosDigitaisExcluir[] = $arquivo;
                                }
                            }
                        }
                    }

                    unset($arContratos[$keyArContrato]);
                }

                if( $obErro->ocorreu() ) 
                    break;
            }

            if ( !$obErro->ocorreu() ) {
                $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;

                foreach($arArquivosDigitaisIncluir AS $chave => $arquivo){
                    $obRPessoalAssentametoGeradoContratoServidor->addArquivoDigital($arquivo);
                }

                foreach($arArquivosDigitaisExcluir AS $chave => $arquivo){
                    $obRPessoalAssentametoGeradoContratoServidor->addArquivoDigitalExcluir($arquivo);
                }

                $obErro = $obRPessoalAssentametoGeradoContratoServidor->executaArquivoDigital($boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $obRPessoalAssentametoGeradoContratoServidor->limparDiretorioPessoalTPM();
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalAssentametoGeradoContratoServidor->obTPessoalConselho );

        if ( !$obErro->ocorreu() ) {
            Sessao::write('arAssentamentos', array());
            sistemaLegado::alertaAviso($pgForm,"Gerar Assentamento","incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
        $obRPessoalServidor = new RPessoalServidor;
        $obRPessoalServidor->addContratoServidor();
        $arNormas = Sessao::read('arNormas');

        $stFiltro = " WHERE cod_assentamento = ".$request->get('inCodAssentamento')." AND cod_classificacao = ".$request->get('inCodClassificacao');
        $inCodMotivo = SistemaLegado::pegaDado("cod_motivo", "pessoal.assentamento_assentamento", $stFiltro, $boTransacao);
        $arArquivosDigitaisIncluir = array();
        $arArquivosDigitaisExcluir = array();

        //Verifica se o cod_motivo é '18 - Readaptação' ou '14 - Alteração de Cargo'
        if ( ($inCodMotivo == 18) || ($inCodMotivo == 14) ){
            $inRegistro = $request->get('inRegistro');
            $inCodContrato = SistemaLegado::pegaDado("cod_contrato","pessoal.contrato","WHERE registro = ".$inRegistro,$boTransacao);
            $obRPessoalServidor->roUltimoContratoServidor->setCodContrato     ( $inCodContrato );
            $obRPessoalServidor->roUltimoContratoServidor->setAlteracaoFuncao ( $request->get("dtDataAlteracaoFuncao") );

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($request->get('stCargo'));

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade( $request->get("inCodEspecialidadeCargo") );

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get("inCodSubDivisao") );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsCargo,$boTransacao);

            if ($rsCargo->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Cargo Inválido. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegimeFuncao->setCodRegime( $request->get("inCodRegimeFuncao") );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->setCodCargo( $request->get("inCodFuncao") );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo( $request->get('inCodFuncao') );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setBuscarCargosNormasVencidas(false);
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->listarCargosPorSubDivisaoServidor($rsFuncao,$boTransacao);

            if ($rsFuncao->getNumLinhas() < 1) {
                sistemaLegado::exibeAviso('Função Inválida. Norma não está mais em vigor.', 'n_alterar', 'erro');
                exit;
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->setCodCargo($request->get('stCargo'));

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addEspecialidade();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->addEspecialidadeSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoEspecialidade->setCodEspecialidade( $request->get("inCodEspecialidadeFuncao")  );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->addCargoSubDivisao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargoFuncao->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao( $request->get("inCodSubDivisaoFuncao") );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalRegime->setCodRegime( $request->get("inCodRegime") );

            if ($arNormas != "") {
                foreach ($arNormas as $arNorma) {
                    $arCodNorma[] = $arNorma['inCodNorma'];
                }
            }

            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->setCodPadrao( $request->get("inCodPadrao") );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->addNivelPadrao();
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCargo->obRFolhaPagamentoPadrao->roUltimoNivelPadrao->setCodNivelPadrao( $request->get("inCodProgressao") );
            $obRPessoalServidor->roUltimoContratoServidor->setSalario                                     ( $request->get("inSalario")         );
            $obRPessoalServidor->roUltimoContratoServidor->setHrMensal                                    ( $request->get("stHorasMensais")    );
            $obRPessoalServidor->roUltimoContratoServidor->setHrSemanal                                   ( $request->get("stHorasSemanais")   );
            $obRPessoalServidor->roUltimoContratoServidor->setInicioProgressao                            ( $request->get("dtDataProgressao")  );
            $obRPessoalServidor->roUltimoContratoServidor->setContaCorrenteSalario                        ( $request->get("inContaSalario")    );
            $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade            ( $request->get("inCodGradeHorario") );
            $obRPessoalServidor->roUltimoContratoServidor->setVigenciaSalario                             ( $request->get("dtVigenciaSalario") );

            $obRPessoalServidor->roUltimoContratoServidor->listarDadosAbaContratoServidor($rsCargoServidor,$boTransacao);

            while ( !$rsCargoServidor->eof() ) {
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoPagamento->setCodTipoPagamento ( $rsCargoServidor->getCampo('cod_tipo_pagamento') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoSalario->setCodTipoSalario ( $rsCargoServidor->getCampo('cod_tipo_salario') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalTipoAdmissao->setCodTipoAdmissao ( $rsCargoServidor->getCampo('cod_tipo_admissao') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalVinculoEmpregaticio->setCodVinculoEmpregaticio ( $rsCargoServidor->getCampo('cod_vinculo') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalCategoria->setCodCategoria ( $rsCargoServidor->getCampo('cod_categoria') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade ( $rsCargoServidor->getCampo('cod_grade') );
                $obRPessoalServidor->roUltimoContratoServidor->setNomeacao ( $rsCargoServidor->getCampo('dt_nomeacao') );
                $obRPessoalServidor->roUltimoContratoServidor->setPosse ( $rsCargoServidor->getCampo('dt_posse') );
                $obRPessoalServidor->roUltimoContratoServidor->setAdmissao ( $rsCargoServidor->getCampo('dt_admissao') );
                $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoSindicato->obRCGM->setNumCGM ( $rsCargoServidor->getCampo('numcgm_sindicato') );
                $obRPessoalServidor->roUltimoContratoServidor->obRNorma->setCodNorma( $rsCargoServidor->getCampo('cod_norma') );
                $obRPessoalServidor->roUltimoContratoServidor->obRPessoalFormaPagamento->setCodFormaPagamento( $rsCargoServidor->getCampo('cod_forma_pagamento') );
        
                $rsCargoServidor->proximo();
            }
        
            $obErro = $obRPessoalServidor->roUltimoContratoServidor->alterarContrato($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " WHERE registro = ".$request->get('inRegistro');
            $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
            $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodAssentamentoGerado( $request->get('inCodAssentamentoGerado') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento( $request->get('inCodAssentamento') );
            $stObservacao = $request->get('stObservacao');
            $stObservacao = stripslashes($stObservacao);
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoObservacao( $stObservacao );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoInicial( $request->get('stDataInicial') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoFinal( $request->get('stDataFinal') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato( $rsContrato->getCampo("cod_contrato") );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioInicial( $request->get('dtInicial') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setPeriodoLicPremioFinal( $request->get('dtFinal') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodNorma( $arCodNorma );

            $obErro = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->alterarGeracaoAssentamento($boTransacao);

            //Arquivos Digitais
            if ( !$obErro->ocorreu() ) {
                foreach($arArquivosDigitais AS $chave => $arquivo){
                    $boAtualizaArq = FALSE;

                    if( $arquivo['inCodClassificacao'] != $request->get('inCodClassificacao') )
                        $boAtualizaArq = TRUE;
                    if( $arquivo['inCodAssentamento'] != $request->get('inCodAssentamento') )
                        $boAtualizaArq = TRUE;
                    if( $arquivo['stDataInicial'] != $request->get('stDataInicial') )
                        $boAtualizaArq = TRUE;
                    if( $arquivo['stDataFinal'] != $request->get('stDataFinal') )
                        $boAtualizaArq = TRUE;

                    if($boAtualizaArq){
                        if($arquivo['boCopiado'] == 'FALSE' || $arquivo['boExcluido'] == 'FALSE'){
                            $arquivo['inCodClassificacao'] = $request->get('inCodClassificacao');
                            $arquivo['inCodAssentamento']  = $request->get('inCodAssentamento');
                            $arquivo['stDataInicial']      = $request->get('stDataInicial');
                            $arquivo['stDataFinal']        = $request->get('stDataFinal');

                            $stNameArq  = $request->get('inCodAssentamentoGerado');
                            $stNameArq .= '_'.$rsContrato->getCampo("cod_contrato");
                            $stNameArq .= Sessao::getEntidade();
                            $stNameArq .= '_'.$request->get('inCodClassificacao');
                            $stNameArq .= '_'.$request->get('inCodAssentamento');

                            $arDataInicial = explode('/', $request->get('stDataInicial'));
                            $stNameArq .= '_'.$arDataInicial[0].'_'.$arDataInicial[1].'_'.$arDataInicial[2];

                            if($request->get('stDataFinal', '') != ''){
                                $arDataFinal = explode('/', $request->get('stDataFinal'));
                                $stNameArq .= '_'.$arDataFinal[0].'_'.$arDataFinal[1].'_'.$arDataFinal[2];
                            }

                            $stNameArq .= '_'.$arquivo['name'];

                            $arquivo['arquivo_digital'] = $stNameArq;

                            if($arquivo['boCopiado'] == 'FALSE'){
                                rename($arquivo['tmp_name'], $stDirTMP.$stNameArq);
                                $arquivo['tmp_name'] = $stDirTMP.$stNameArq;
                            }else{
                                rename($arquivo['stArquivo'], $stDirANEXO.$stNameArq);
                                $arquivo['tmp_name'] = $stDirANEXO.$stNameArq;
                            }

                            $arquivo['stArquivo'] = $stDirANEXO.$stNameArq;
                        }
                    }else{
                        if($arquivo['boCopiado'] == 'FALSE'){
                            $arquivo['arquivo_digital'] = $request->get('inCodAssentamentoGerado').'_'.$arquivo['arquivo_digital'];
                            $arquivo['stArquivo']       = $stDirANEXO.$arquivo['arquivo_digital'];
                        }
                    }

                    $arquivo['inCodContrato']           = $rsContrato->getCampo("cod_contrato");
                    $arquivo['inCodAssentamentoGerado'] = $request->get('inCodAssentamentoGerado');

                    if($arquivo['boExcluido'] == 'FALSE'){
                        //Salva Arquivo Digital
                        $arArquivosDigitaisIncluir[] = $arquivo;
                    }elseif($arquivo['boCopiado'] == 'TRUE' && $arquivo['boExcluido'] == 'TRUE'){
                        //Salva Arquivo Digital para Exclusão
                        $arArquivosDigitaisExcluir[] = $arquivo;
                    }
                }
            }

            if ( !$obErro->ocorreu() ) {
                $obRPessoalAssentametoGeradoContratoServidor = new RPessoalAssentamentoGeradoContratoServidor;

                foreach($arArquivosDigitaisIncluir AS $chave => $arquivo){
                    $obRPessoalAssentametoGeradoContratoServidor->addArquivoDigital($arquivo);
                }

                foreach($arArquivosDigitaisExcluir AS $chave => $arquivo){
                    $obRPessoalAssentametoGeradoContratoServidor->addArquivoDigitalExcluir($arquivo);
                }

                $obErro = $obRPessoalAssentametoGeradoContratoServidor->executaArquivoDigital($boTransacao);

                if ( !$obErro->ocorreu() ) {
                    $obRPessoalAssentametoGeradoContratoServidor->limparDiretorioPessoalTPM();
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalAssentametoGeradoContratoServidor->obTPessoalConselho );

        if ( !$obErro->ocorreu() ) {
            Sessao::remove('asNormas');
            sistemaLegado::alertaAviso($pgList,"Gerar Assentamento","alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";

        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".$request->get('inRegistro');
        $obErro = $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro, "", $boTransacao);

        if ( !$obErro->ocorreu() ) {
            $obRPessoalAssentametoGeradoContratoServidor->addRPessoalGeracaoAssentamento();
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo("cod_contrato"));
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->obRPessoalAssentamento->setCodAssentamento( $request->get('inCodAssentamento') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setCodAssentamentoGerado( $request->get('inCodAssentamentoGerado') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setTimestamp( $request->get('stTimestamp') );
            $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->setDescricaoExclusao( $request->get('stMotivoExclusao') );
            $obErro = $obRPessoalAssentametoGeradoContratoServidor->roRPessoalGeracaoAssentamento->excluirGeracaoAssentamento($boTransacao);

            if ( !$obErro->ocorreu() ) {
                include_once CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoArquivoDigital.class.php";
                $obTPessoalAssentamentoArquivoDigital = new TPessoalAssentamentoArquivoDigital();
                $obTPessoalAssentamentoArquivoDigital->setDado('cod_assentamento_gerado' , $request->get('inCodAssentamentoGerado'));
                $obTPessoalAssentamentoArquivoDigital->setDado('cod_contrato'            , $rsContrato->getCampo("cod_contrato"));
                $obErro = $obTPessoalAssentamentoArquivoDigital->recuperaAssentamentoArquivoDigital($rsArquivoDigital, '', '', $boTransacao);

                if ( !$obErro->ocorreu() ) {
                    while ( !$rsArquivoDigital->eof() ) {
                        $arquivo['inCodAssentamentoGerado'] = $rsArquivoDigital->getCampo('cod_assentamento_gerado');
                        $arquivo['inCodContrato']           = $rsArquivoDigital->getCampo('cod_contrato');
                        $arquivo['name']                    = $rsArquivoDigital->getCampo('nome_arquivo');
                        $arquivo['arquivo_digital']         = $rsArquivoDigital->getCampo('arquivo_digital');
                        $arquivo['stArquivo']               = $stDirANEXO.$rsArquivoDigital->getCampo('arquivo_digital');

                        $obRPessoalAssentametoGeradoContratoServidor->addArquivoDigitalExcluir($arquivo);

                        $rsArquivoDigital->proximo();
                    }

                    $obErro = $obRPessoalAssentametoGeradoContratoServidor->executaArquivoDigital($boTransacao);

                    if ( !$obErro->ocorreu() )
                        $obRPessoalAssentametoGeradoContratoServidor->limparDiretorioPessoalTPM();
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRPessoalAssentametoGeradoContratoServidor->obTPessoalConselho );
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Gerar Assentamento","excluir","aviso", Sessao::getId(), "../");
        }else{
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}
?>
