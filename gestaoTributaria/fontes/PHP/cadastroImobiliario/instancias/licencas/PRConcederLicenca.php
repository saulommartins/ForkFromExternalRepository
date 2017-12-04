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

  * Página de Processamento da Conceder Licenca
  * Data de criação : 27/03/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRConcederLicenca.php 59845 2014-09-15 19:32:00Z carolina $

  * Casos de uso: uc-05.01.28
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMEmissaoDocumento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovel.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelArea.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaLote.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaLoteLoteamento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaLoteParcelamentoSolo.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaProcesso.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaDocumento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaResponsavelTecnico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelNovaConstrucao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelNovaEdificacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelUnidadeAutonoma.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaImovelUnidadeDependente.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicencaImovelValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicencaLoteValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaLoteArea.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaBaixa.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaUnidadeDependente.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php" );
$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ConcederLicenca";
$pgFormL = "FL".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "cassar":
        $obTCIMLicencaBaixa = new TCIMLicencaBaixa;
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicencaBaixa );

            $obTCIMLicencaBaixa->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaBaixa->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaBaixa->setDado( "dt_inicio", $_REQUEST["dtBaixa"] );
            $obTCIMLicencaBaixa->setDado( "cod_tipo", 3 );
            $obTCIMLicencaBaixa->setDado( "motivo", $_REQUEST["stMotivo"] );
            $obTCIMLicencaBaixa->inclusao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=cassar", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." cassada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "cancelar":
        $obTCIMLicencaBaixa = new TCIMLicencaBaixa;
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicencaBaixa );

            $obTCIMLicencaBaixa->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaBaixa->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaBaixa->setDado( "dt_termino", $_REQUEST["dtTermino"] );
            $obTCIMLicencaBaixa->setDado( "cod_tipo", 2 );
            $obTCIMLicencaBaixa->setDado( "motivo", $_REQUEST["stMotivo"] );
            $obTCIMLicencaBaixa->alteracao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=cancelar", "Suspensão de licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." cancelada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "suspender":
        $obTCIMLicencaBaixa = new TCIMLicencaBaixa;
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicencaBaixa );

            $obTCIMLicencaBaixa->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaBaixa->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaBaixa->setDado( "dt_inicio", $_REQUEST["dtBaixa"] );
            $obTCIMLicencaBaixa->setDado( "dt_termino", $_REQUEST["dtTermino"] );
            $obTCIMLicencaBaixa->setDado( "cod_tipo", 2 );
            $obTCIMLicencaBaixa->setDado( "motivo", $_REQUEST["stMotivo"] );
            $obTCIMLicencaBaixa->inclusao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=suspender", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." suspensa com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "baixar":
        $obTCIMLicencaBaixa = new TCIMLicencaBaixa;
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicencaBaixa );

            $obTCIMLicencaBaixa->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaBaixa->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaBaixa->setDado( "dt_inicio", $_REQUEST["dtBaixa"] );
            $obTCIMLicencaBaixa->setDado( "cod_tipo", 1 );
            $obTCIMLicencaBaixa->setDado( "motivo", $_REQUEST["stMotivo"] );
            $obTCIMLicencaBaixa->inclusao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=baixar", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." baixada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "excluir":
        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaResponsavelTecnico->exclusao();

            $obTCIMLicencaBaixa = new TCIMLicencaBaixa;
            $obTCIMLicencaBaixa->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaBaixa->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaBaixa->exclusao();

            $obTCIMLicencaImovelArea = new TCIMLicencaImovelArea;
            $obTCIMLicencaImovelArea->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelArea->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelArea->exclusao();

            $obTCIMLicencaLoteArea = new TCIMLicencaLoteArea;
            $obTCIMLicencaLoteArea->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaLoteArea->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaLoteArea->exclusao();

            $obTCIMLicencaLoteLoteamento = new TCIMLicencaLoteLoteamento;
            $obTCIMLicencaLoteLoteamento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaLoteLoteamento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaLoteLoteamento->exclusao();

            $obTCIMLicencaLoteParcelamentoSolo = new TCIMLicencaLoteParcelamentoSolo;
            $obTCIMLicencaLoteParcelamentoSolo->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaLoteParcelamentoSolo->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaLoteParcelamentoSolo->exclusao();

            $obTCIMLicencaImovelUnidadeAutonoma = new TCIMLicencaImovelUnidadeAutonoma;
            $obTCIMLicencaImovelUnidadeAutonoma->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelUnidadeAutonoma->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelUnidadeAutonoma->exclusao();

            $obTCIMLicencaImovelUnidadeDependente = new TCIMLicencaImovelUnidadeDependente;
            $obTCIMLicencaImovelUnidadeDependente->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelUnidadeDependente->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelUnidadeDependente->exclusao();

            $obTCIMLicencaImovelNovaEdificacao = new TCIMLicencaImovelNovaEdificacao;
            $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelNovaEdificacao->exclusao();

            $obTCIMLicencaImovelNovaConstrucao = new TCIMLicencaImovelNovaConstrucao;
            $obTCIMLicencaImovelNovaConstrucao->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelNovaConstrucao->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelNovaConstrucao->exclusao();

            $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
            $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaProcesso->exclusao();

            $obTCIMEmissaoDocumento = new TCIMEmissaoDocumento;
            $obTCIMEmissaoDocumento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMEmissaoDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMEmissaoDocumento->exclusao();

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaDocumento->exclusao();

            $obTCIMAtributoTipoLicencaLoteValor = new TCIMAtributoTipoLicencaLoteValor;
            $obTCIMAtributoTipoLicencaLoteValor->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMAtributoTipoLicencaLoteValor->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMAtributoTipoLicencaLoteValor->exclusao();

            $obTCIMAtributoTipoLicencaImovelValor = new TCIMAtributoTipoLicencaImovelValor;
            $obTCIMAtributoTipoLicencaImovelValor->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMAtributoTipoLicencaImovelValor->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMAtributoTipoLicencaImovelValor->exclusao();

            $obTCIMLicencaLote = new TCIMLicencaLote;
            $obTCIMLicencaLote->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaLote->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaLote->exclusao();

            $obTCIMLicencaImovel = new TCIMLicencaImovel;
            $obTCIMLicencaImovel->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovel->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovel->exclusao();

            $obTCIMLicenca->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicenca->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicenca->exclusao();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=excluir", "Licença".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." excluída com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");

        break;

    case "alterarLote":
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }

        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMLicenca->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicenca->setDado( "exercicio", $_REQUEST["inExercicio"] );

            $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
            if ( $_REQUEST["dtValidadeFim"] )
                $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );

            $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );
            $obTCIMLicenca->alteracao();

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
                $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", $_REQUEST["inExercicio"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );

                $obTCIMLicencaResponsavelTecnico->inclusao();
            }

            $obTCIMLicencaLoteArea = new TCIMLicencaLoteArea;
            $obTCIMLicencaLoteArea->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaLoteArea->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaLoteArea->setDado( "cod_lote", $_REQUEST["inCodLote"] );
            $obTCIMLicencaLoteArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
            $obTCIMLicencaLoteArea->alteracao();

            $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
            $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaProcesso->exclusao();

            if ($_REQUEST["inProcesso"]) {
                $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
                $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
                $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );

                $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
                $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

                $obTCIMLicencaProcesso->inclusao();
            }

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );
            if ( $rsNumDoc->Eof() ) {
                $inNumDocumento = 1;
            } else {
                $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
            }

            $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );
            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
            $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
            $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
            $obTCIMLicencaDocumento->inclusao();

            if ($_REQUEST["boEmitir"]) { //devo ver como sera a emissao (se direto aqui) para continuar
                $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodLicenca"]." AND exercicio = ".$_REQUEST["inExercicio"]." AND cod_tipo_documento = ".$arTMPDadosDocumento[1]." AND cod_documento = ".$arTMPDadosDocumento[0];
                $obTCIMEmissaoDocumento = new TCIMEmissaoDocumento;
            }

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setCodCadastro( 10 );
            $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaLoteValor );
            $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $_REQUEST["inCodLicenca"], "exercicio" => $_REQUEST["inExercicio"], "cod_lote" => $_REQUEST["inCodLote"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

            $obAtributos = new MontaAtributos;
            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
            }

            $obRCadastroDinamico->salvarValores();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=alterar", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." alterada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");

        break;

    case "alterarEdificacao":
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }

        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMLicenca->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicenca->setDado( "exercicio", $_REQUEST["inExercicio"] );

            $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
            if ( $_REQUEST["dtValidadeFim"] )
                $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );

            $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );

            $obTCIMLicenca->alteracao();

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
                $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", $_REQUEST["inExercicio"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );

                $obTCIMLicencaResponsavelTecnico->inclusao();
            }

            $obTCIMLicencaImovelArea = new TCIMLicencaImovelArea;
            $obTCIMLicencaImovelArea->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelArea->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelArea->setDado( "inscricao_municipal", $_REQUEST["inCodInscriao"] );
            $obTCIMLicencaImovelArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
            $obTCIMLicencaImovelArea->alteracao();

            $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
            $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaProcesso->exclusao();

            if ($_REQUEST["inProcesso"]) {
                $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
                $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
                $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );

                $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
                $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

                $obTCIMLicencaProcesso->inclusao();
            }

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );
            if ( $rsNumDoc->Eof() ) {
                $inNumDocumento = 1;
            } else {
                $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
            }

            $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );
            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
            $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
            $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
            $obTCIMLicencaDocumento->inclusao();

            if ($_REQUEST["boEmitir"]) { //devo ver como sera a emissao (se direto aqui) para continuar
                $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodLicenca"]." AND exercicio = ".$_REQUEST["inExercicio"]." AND cod_tipo_documento = ".$arTMPDadosDocumento[1]." AND cod_documento = ".$arTMPDadosDocumento[0];
                $obTCIMEmissaoDocumento = new TCIMEmissaoDocumento;
            }

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setCodCadastro( 10 );
            $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaImovelValor );
            $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $_REQUEST["inCodLicenca"], "exercicio" => $_REQUEST["inExercicio"], "inscricao_municipal" => $_REQUEST["inCodInscriao"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

            $obAtributos = new MontaAtributos;
            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
            }

            $obRCadastroDinamico->salvarValores();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=alterar", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." alterada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "alterarImoveis":
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }
      
        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMLicenca->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicenca->setDado( "exercicio", $_REQUEST["inExercicio"] );

            $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
            if ( $_REQUEST["dtValidadeFim"] )
                $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );

            $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );
            $obTCIMLicenca->alteracao();

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
                $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", $_REQUEST["inExercicio"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );

                $obTCIMLicencaResponsavelTecnico->inclusao();
            }

            $obTCIMLicencaImovelArea = new TCIMLicencaImovelArea;
            $obTCIMLicencaImovelArea->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelArea->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelArea->setDado( "inscricao_municipal", $_REQUEST["inCodInscriao"] );
            $obTCIMLicencaImovelArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
            $obTCIMLicencaImovelArea->alteracao();

            $obTCIMLicencaImovelNovaEdificacao = new TCIMLicencaImovelNovaEdificacao;
            $obTCIMLicencaImovelNovaEdificacao->setDado( "inscricao_municipal", $_REQUEST["inCodInscriao"] );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaImovelNovaEdificacao->exclusao();

            if ($_REQUEST["stNovaUnidade"] == "edificacao") {
                $obTCIMLicencaImovelNovaEdificacao = new TCIMLicencaImovelNovaEdificacao;
                $obTCIMLicencaImovelNovaEdificacao->setDado( "inscricao_municipal", $_REQUEST["inCodInscriao"] );
                $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaImovelNovaEdificacao->setDado( "exercicio", $_REQUEST["inExercicio"] );
                $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_tipo", $_REQUEST["inTipoEdificacao"] );
                $obTCIMLicencaImovelNovaEdificacao->inclusao();
            } else {
                $inCodConstrucaoOutros= Sessao::read('inCodConstrucaoOutros');
                $obTCIMConstrucaoOutros = new TCIMConstrucaoOutros;
                $obTCIMConstrucaoOutros->setDado( "cod_construcao", $inCodConstrucaoOutros);
                $obTCIMConstrucaoOutros->setDado( "descricao", $_REQUEST["stDescricao"] );
                $obTCIMConstrucaoOutros->alteracao();
             }
            

            $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
            $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaProcesso->exclusao();

            if ($_REQUEST["inProcesso"]) {
                $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
                $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
                $obTCIMLicencaProcesso->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
                $obTCIMLicencaProcesso->setDado( "exercicio", $_REQUEST["inExercicio"] );

                $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
                $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

                $obTCIMLicencaProcesso->inclusao();
            }

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );
            if ( $rsNumDoc->Eof() ) {
                $inNumDocumento = 1;
            } else {
                $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
            }

            $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );
            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $_REQUEST["inCodLicenca"] );
            $obTCIMLicencaDocumento->setDado( "exercicio", $_REQUEST["inExercicio"] );
            $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
            $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
            $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
            $obTCIMLicencaDocumento->inclusao();

            if ($_REQUEST["boEmitir"]) { //devo ver como sera a emissao (se direto aqui) para continuar
                $stFiltro = " WHERE cod_licenca = ".$_REQUEST["inCodLicenca"]." AND exercicio = ".$_REQUEST["inExercicio"]." AND cod_tipo_documento = ".$arTMPDadosDocumento[1]." AND cod_documento = ".$arTMPDadosDocumento[0];
                $obTCIMEmissaoDocumento = new TCIMEmissaoDocumento;
            }

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setCodCadastro( 10 );
            $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaImovelValor );
            $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $_REQUEST["inCodLicenca"], "exercicio" => $_REQUEST["inExercicio"], "inscricao_municipal" => $_REQUEST["inCodInscriao"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

            $obAtributos = new MontaAtributos;
            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
            }

            $obRCadastroDinamico->salvarValores();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgFormL."&stAcao=alterar", "Licença ".$_REQUEST["inCodLicenca"]."/".$_REQUEST["inExercicio"]." alterada com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "incluirImoveis":
        $obAtributos = new MontaAtributos;
        $obAtributos->setName      ( "Atributo_" );
        $obAtributos->recuperaVetor( $arChave    );

        //Teste dos campos que não devem ser nulos (foi necessário deixar o setNull como true para não dar erro)
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }

        if ($_REQUEST['stNovaUnidade'] == 'construcao') {
            if ($_REQUEST['stDescricao'] == '') {
                SistemaLegado::exibeAviso( "Não foi inserido uma descrição para a construção.", "n_definir", "erro" );
                exit;
            }

            $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inCodImovel"] );
            $obErro = $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveis( $rsImovel );

            if ( sistemaLegado::comparaDatas($rsImovel->getCampo("dt_cadastro"),$_REQUEST['dtValidadeInicio']) ) {
                $obErro->setDescricao('Data de construção ( '.$_REQUEST['dtValidadeInicio']. ') inferior a data de cadastro do imóvel ('.$rsImovel->getCampo('dt_cadastro').')');
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsRecordSet );

                if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
                    $obErro = $obRCIMUnidadeAutonoma->consultarUnidadeAutonoma();
                    
                    if ( !$obErro->ocorreu() ) {                        
                        $obRCIMUnidadeAutonoma->addUnidadeDependente();

                        //ATRIBUTOS
                        foreach ($arChave as $key=>$value) {
                            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                            $inCodAtributo = $arChaves[0];

                            if ( is_array($value) ) {
                                $value = implode(",",$value);
                            }

                            $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                        }

                        if ($_REQUEST["inProcesso"]) {
                            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                            $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setCodigoProcesso ( $arProcesso[0] );
                            $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->obRProcesso->setExercicio      ( $arProcesso[1] );
                        }

                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDescricao( $_REQUEST["stDescricao"] );
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade( $_REQUEST["flAreaLicenca"] );
                        $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMConstrucaoOutros->setDataConstrucao( $_REQUEST["dtValidadeInicio"] );
                        $obErro = $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->incluirUnidadeDependente();
                    }
                } else {
                    $obErro->setDescricao( "Deve haver no mínimo uma edificação como unidade autônoma no imóvel informado!" );
                }

                if ( $obErro->ocorreu() ) {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                    die;
                } else {               
                    $obTCIMAreaUnidadeDependente = new TCIMAreaUnidadeDependente;
                    $obTCIMAreaUnidadeDependente->recuperaTodos ( $rsAreaUnidadeDependente, " WHERE inscricao_municipal = ".$_REQUEST['inCodImovel']."
                                                                                                AND timestamp::varchar ilike '".Sessao::getExercicio()."%'
                                                                                                AND area = ".str_replace(',', '.', $_REQUEST["flAreaLicenca"])."", " ORDER BY timestamp DESC LIMIT 1");
                    $inCodConstrucao = $rsAreaUnidadeDependente->getCampo('cod_construcao_dependente');                   
                }
            }

        } elseif ($_REQUEST['stNovaUnidade'] == 'edificacao') { 
            $obRCIMEdificacao        = new RCIMEdificacao;
            $obRCIMUnidadeAutonoma   = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote ) );
            $rsUnidadeAutonoma       = new RecordSet;
            $obRCIMUnidadeDependente = new RCIMUnidadeDependente( $obRCIMUnidadeAutonoma );

            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inCodImovel"] );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveisConsulta );

            if ( $rsImoveisConsulta->getNumLinhas() > 0 ) {
                $obRCIMUnidadeAutonoma->verificaUnidadeAutonoma( $rsUnidadeAutonoma );
                if ( $rsUnidadeAutonoma->getNumLinhas() > 0 ) {
                    $obRCIMUnidadeAutonoma->buscaAreaConstrucao( $flAreaTotal, $flAreaImovel );
                    $inCodigoConstrucaoAutonoma = $rsUnidadeAutonoma->getCampo( "cod_construcao" );
                    $inCodigoTipoAutonoma       = $rsUnidadeAutonoma->getCampo( "cod_tipo"       );
                    $stTipoUnidade              = "Dependente";
                } else {
                    $stTipoUnidade = "Autônoma";
                    $flAreaTotal   = 0.00;
                    $flAreaImovel  = 0.00;
                }
                //Calcula Área total edificada para a Inscrição Municipal selecionada
                $rsImoveisConsulta->addFormatacao( 'area_total_edificada', NUMERIC_BR );
                $flAreaTotal = $rsImoveisConsulta->getCampo('area_total_edificada');
            }
           
            $obErro = new Erro;
            $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao($_REQUEST['inCodImovel']);
            $obErro = $obRCIMEdificacao->obRCIMImovel->listarImoveisConsulta($rsImoveis);

            $arDataCadastro = explode( "-" , $rsImoveis->getCampo('dt_cadastro') );
            $dtCadastroBd = $arDataCadastro[2]."/".$arDataCadastro[1]."/".$arDataCadastro[0];

            if ( sistemaLegado::comparaDatas( $dtCadastroBd , $_REQUEST['dtValidadeInicio'] ) ) {
                $obErro->setDescricao('Data de construção ('.$_REQUEST['dtValidadeInicio'].') é menor que a data de cadastro do imóvel ('.$dtCadastroBd.')');
            }

            $obRCIMEdificacao->setDataConstrucao    ( $_REQUEST['dtValidadeFim']);
            $avisoInclusao = "Inscrição Imobiliária: ".$_REQUEST["inCodImovel"];

            if ($stTipoUnidade == "Autônoma") {
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];

                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }

                    $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }

                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao               ( $inCodigoConstrucaoAutonoma   );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                     ( $_REQUEST["inTipoEdificacao"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setAreaConstruida                 ( $flAreaTotal                  );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setDataConstrucao                 ( $_REQUEST["dtValidadeFim"]    );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setCodigoProcesso    ( $arProcesso[0]                );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->obRProcesso->setExercicio         ( $arProcesso[1]                );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                    ( $_REQUEST["inCodImovel"]      );
                $obRCIMUnidadeAutonoma->setAreaUnidade                                      ( $_REQUEST["flAreaLicenca"]    );
                $obRCIMUnidadeAutonoma->roRCIMImovel->recuperaDataLoteImovel();
                $dtLote         = explode( "/" , $obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                $arDtConstrucao = explode( "/" , $_REQUEST["dtValidadeFim"]                                           );
                $dtLote        = $dtLote[2].$dtLote[1].$dtLote[0];
                $dtConstrucao  = $arDtConstrucao[2].$arDtConstrucao[1].$arDtConstrucao[0];

                if ($dtConstrucao < $dtLote) {
                    $obErro->setDescricao("A Data de Construção deve ser superior a Data de Inscrição do Lote: ".$obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao());
                }

                if ( !$obErro->ocorreu() ) {
                    $obErro = $obRCIMUnidadeAutonoma->incluirUnidadeAutonoma();
                    $inCodConstrucao = $obRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao();
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
                
            } elseif ($stTipoUnidade == "Dependente") {
                $obRCIMUnidadeAutonoma->addUnidadeDependente();
                //ATRIBUTOS
                foreach ($arChave as $key=>$value) {
                    $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                    $inCodAtributo = $arChaves[0];

                    if ( is_array($value) ) {
                        $value = implode(",",$value);
                    }

                    $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
                }

                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo                  ( $_REQUEST["inTipoEdificacao"] );
                $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso ( $arProcesso[0]                );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio      ( $arProcesso[1]                );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setDataConstrucao              ( $_REQUEST["dtValidadeInicio"] );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->setAreaUnidade                                   ( $_REQUEST["flAreaLicenca"]    );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida              ( $flAreaTotal                  );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setTipoUnidade                 ( $stTipoUnidade                );
                $obRCIMUnidadeAutonoma->roUltimaUnidadeDependente->obRCIMEdificacao->setUnidadeAutonoma             ( $inCodigoConstrucaoAutonoma   );
                $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao                                            ( $_REQUEST["inCodImovel"]      );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao                                       ( $inCodigoConstrucaoAutonoma   );
                $obRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo                                             ( $inCodigoTipoAutonoma         );
                $obRCIMUnidadeAutonoma->roRCIMImovel->recuperaDataLoteImovel();
                $dtLote         = explode( "/" , $obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao() );
                $arDtConstrucao = explode( "/" , $_REQUEST["dtValidadeInicio"]                                          );
                $dtLote        = $dtLote[2].$dtLote[1].$dtLote[0];
                $dtConstrucao  = $arDtConstrucao[2].$arDtConstrucao[1].$arDtConstrucao[0];

                if ($dtConstrucao < $dtLote) {
                    $obErro->setDescricao("A Data de Construção deve ser superior a Data de Inscrição do Lote: ".$obRCIMUnidadeAutonoma->roRCIMImovel->roRCIMLote->getDataInscricao());
                }

                if ( !$obErro->ocorreu() ) {
                    $obErro = $obRCIMUnidadeAutonoma->salvarUnidadesDependentes();
                    $obTCIMAreaUnidadeDependente = new TCIMAreaUnidadeDependente;
                    $obTCIMAreaUnidadeDependente->recuperaTodos ( $rsAreaUnidadeDependente, " WHERE inscricao_municipal = ".$_REQUEST['inCodImovel']."
                                                                                                AND timestamp::varchar ilike '".Sessao::getExercicio()."%'
                                                                                                AND area = ".str_replace(',', '.', $_REQUEST["flAreaLicenca"])."", " ORDER BY timestamp DESC LIMIT 1");
                    $inCodConstrucao = $rsAreaUnidadeDependente->getCampo('cod_construcao_dependente');                                 
                }  
            }
        }

        $obTCIMLicenca = new TCIMLicenca;
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

        $obTCIMTipoLicenca = new TCIMTipoLicenca;
        $stFiltro = " AND permissao.cod_tipo = ".$_REQUEST["inTipoLicenca"];
        $obTCIMTipoLicenca->recuperaLicencaPorCGM( $rsTipoLicenca, $stFiltro );

        $obTCIMLicenca->recuperaProximaLicenca( $rsProximoCodigo );

        if ( $rsProximoCodigo->Eof() ) {
            $inNovoCodLicenca = 1;
        } else {
            $inNovoCodLicenca = $rsProximoCodigo->getCampo( "cod_licenca" ) + 1;
        }

        $obTCIMLicenca->setDado( "cod_licenca", $inNovoCodLicenca );
        $obTCIMLicenca->setDado( "exercicio", Sessao::getExercicio() );
        $obTCIMLicenca->setDado( "cod_tipo", $_REQUEST["inTipoLicenca"] );
        $obTCIMLicenca->setDado( "numcgm", Sessao::read('numCgm') );
        $obTCIMLicenca->setDado( "timestamp", $rsTipoLicenca->getCampo( "timestamp" ) );
        $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
        $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );
        if ($_REQUEST["dtValidadeFim"]) {
            $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );
        }
        $obTCIMLicenca->inclusao();

        $obTCIMLicencaImovel = new TCIMLicencaImovel;
        $obTCIMLicencaImovel->setDado( "cod_licenca", $inNovoCodLicenca );
        $obTCIMLicencaImovel->setDado( "exercicio", Sessao::getExercicio() );
        $obTCIMLicencaImovel->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
        $obTCIMLicencaImovel->inclusao();

        $obTCIMLicencaImovelArea = new TCIMLicencaImovelArea;
        $obTCIMLicencaImovelArea->setDado( "cod_licenca", $inNovoCodLicenca );
        $obTCIMLicencaImovelArea->setDado( "exercicio", Sessao::getExercicio() );
        $obTCIMLicencaImovelArea->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
        $obTCIMLicencaImovelArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
        $obTCIMLicencaImovelArea->inclusao();

        $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
        $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );

        if ( $rsNumDoc->Eof() ) {
            $inNumDocumento = 1;
        } else {
            $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
        }

        $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );

        $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
        $obTCIMLicencaDocumento->setDado( "cod_licenca", $inNovoCodLicenca );
        $obTCIMLicencaDocumento->setDado( "exercicio", Sessao::getExercicio() );
        $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
        $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
        $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
        $obTCIMLicencaDocumento->inclusao();
      
        if ($_REQUEST["stNovaUnidade"] == "construcao") {
            $obTCIMLicencaImovelNovaConstrucao = new TCIMLicencaImovelNovaConstrucao;
            $obTCIMLicencaImovelNovaConstrucao->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaImovelNovaConstrucao->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaImovelNovaConstrucao->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
            $obTCIMLicencaImovelNovaConstrucao->setDado( "cod_construcao", $inCodConstrucao );
            $obTCIMLicencaImovelNovaConstrucao->inclusao();
        } else {
            //falta colocar o tipo da edificacao aqui nesta tabela 27_03
            $obTCIMLicencaImovelNovaEdificacao = new TCIMLicencaImovelNovaEdificacao;
            $obTCIMLicencaImovelNovaEdificacao->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_tipo", $_REQUEST["inTipoEdificacao"] );
            $obTCIMLicencaImovelNovaEdificacao->setDado( "cod_construcao", $inCodConstrucao );
            $obTCIMLicencaImovelNovaEdificacao->inclusao();
        }
  
        $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
        for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
            
            $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
            $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );

            $obTCIMLicencaResponsavelTecnico->inclusao();
        }

        if ($_REQUEST["inProcesso"]) {
            $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
            $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
            $obTCIMLicencaProcesso->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaProcesso->setDado( "exercicio", Sessao::getExercicio() );

            $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
            $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

            $obTCIMLicencaProcesso->inclusao();
        }

        $obRCadastroDinamico = new RCadastroDinamico;
        $obRCadastroDinamico->setCodCadastro( 10 );
        $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaImovelValor );
        $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $inNovoCodLicenca, "exercicio" => Sessao::getExercicio(), "inscricao_municipal" => $_REQUEST["inCodImovel"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

        //Parte para preparar a emissão do alvará
        if ($_REQUEST['boEmitir'] == 'Impressão Local') {

            list($inCodDocAtual, $inCodTipoDocAtual) = explode('-', $_REQUEST['cmbModelo']);

            $stFiltro = " WHERE a.cod_acao = ". Sessao::read('acao');
            $stFiltro .= " AND b.cod_documento = ".$inCodDocAtual;

            $obTModeloDocumento = new TAdministracaoModeloDocumento;
            $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

            while ( !$rsDocumentos->Eof() ) {
                    $inCodTipoDocAtual 	= $rsDocumentos->getCampo( "cod_tipo_documento" );
                    $inCodDocAtual      = $rsDocumentos->getCampo( "cod_documento" );
                    $stNomeArquivo      = $rsDocumentos->getCampo( "nome_arquivo_agt" );
                    $stNomeDocumento    = $rsDocumentos->getCampo( 'nome_documento' );

                    $rsDocumentos->proximo();
            }

            $stCaminho = CAM_GT_CIM_INSTANCIAS."licencas/FMManterEmissaoImobiliaria.php";

            //ATRIBUTOS
            $obAtributos = new MontaAtributos;

            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
//          $obAtributos->setName      ( "AtributoEdificacao_" );
//          $obAtributos->recuperaVetor( $arChave );
            $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico;
            $obTAdministracaoAtributoDinamico->recuperaTodos ( $rsAtributoDinamico, " WHERE nom_atributo ilike 'CE -%'" );

            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );

                foreach ($rsAtributoDinamico->arElementos as $index => $valueAtributo) {

                    if ($arChaves[0] == $valueAtributo['cod_atributo']) {
                        $inCodAtributoEstrutura = $value;
                        $inCodAtributo = $arChaves[0];
                    }
                }
            }

            $stInscricoes = $stParametros = '';
                            $stParametros .= "&inNumeroLicenca=".$inNovoCodLicenca;
                            $stParametros .= "&inExercicio=".Sessao::getExercicio();
                            $stParametros .= "&stTipoModalidade=emissao";
                            $stParametros .= "&stCodAcao=".Sessao::read('acao');
                            $stParametros .= "&stOrigemFormulario=conceder_licenca";
                            $stParametros .= "&inCodigoTipoDocumento=".$inCodTipoDocAtual;
                            $stParametros .= "&inCodigoDocumento=". $inCodDocAtual;
                            $stParametros .= "&stNomeArquivo=".$stNomeArquivo;
                            $stParametros .= "&stNomeDocumento=".$stNomeDocumento;
                            $stParametros .= "&inInscricaoImobiliaria=".$_REQUEST["inCodImovel"];
                            $stParametros .= "&flAreaLicenca=".$_REQUEST["flAreaLicenca"];
                            $stParametros .= "&stProcesso=".$_REQUEST["inProcesso"];
                            $stParametros .= "&inCodAtributoEstrutura=".$inCodAtributoEstrutura;
                            $stParametros .= "&inCodAtributo=".$inCodAtributo;
                            $stParametros .= "&stTipoLicenca=".$_REQUEST['stNovaUnidade'];
                            $stParametros .= "&inCodConstrucao=".$inCodConstrucao;
                            $stParametros .= "&stCtrl=Download";

            Sessao::encerraExcecao();
            sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=incluir","Conceder de Licença ".$inNovoCodLicenca."/".Sessao::getExercicio()."", "incluir","aviso", Sessao::getId(), "../");

        } else {
            Sessao::encerraExcecao();
            SistemaLegado::alertaAviso( $pgForm."&stAcao=incluir", "Conceder de Licença ".$inNovoCodLicenca."/".Sessao::getExercicio()."", "incluir", "aviso", Sessao::getId(), "../");
        }
    break;

    case "incluirEdificacao":
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }
        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMTipoLicenca = new TCIMTipoLicenca;
            $stFiltro = " AND permissao.cod_tipo = ".$_REQUEST["inTipoLicenca"];
            $obTCIMTipoLicenca->recuperaLicencaPorCGM( $rsTipoLicenca, $stFiltro );

            $obTCIMLicenca->recuperaProximaLicenca( $rsProximoCodigo );
            if ( $rsProximoCodigo->Eof() ) {
                $inNovoCodLicenca = 1;
            } else {
                $inNovoCodLicenca = $rsProximoCodigo->getCampo( "cod_licenca" ) + 1;
            }

            $obTCIMLicenca->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicenca->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicenca->setDado( "cod_tipo", $_REQUEST["inTipoLicenca"] );
            $obTCIMLicenca->setDado( "numcgm", Sessao::read('numCgm') );
            $obTCIMLicenca->setDado( "timestamp", $rsTipoLicenca->getCampo( "timestamp" ) );
            $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
            if ( $_REQUEST["dtValidadeFim"] )
                $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );

            $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );
            $obTCIMLicenca->inclusao();

            $obTCIMLicencaImovel = new TCIMLicencaImovel;
            $obTCIMLicencaImovel->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaImovel->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaImovel->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
            $obTCIMLicencaImovel->inclusao();

            $obTCIMLicencaImovelArea = new TCIMLicencaImovelArea;
            $obTCIMLicencaImovelArea->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaImovelArea->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaImovelArea->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
            $obTCIMLicencaImovelArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
            $obTCIMLicencaImovelArea->inclusao();

            if ($_REQUEST["cmbEdifConst"]) {
                $arDadosEdificacaoConstrucao = explode( "-", $_REQUEST["cmbEdifConst"] );
                if ($arDadosEdificacaoConstrucao[2] == "autonoma") {
                    $obTCIMLicencaImovelUnidadeAutonoma = new TCIMLicencaImovelUnidadeAutonoma;
                    $obTCIMLicencaImovelUnidadeAutonoma->setDado( "cod_licenca", $inNovoCodLicenca );
                    $obTCIMLicencaImovelUnidadeAutonoma->setDado( "exercicio", Sessao::getExercicio() );
                    $obTCIMLicencaImovelUnidadeAutonoma->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
                    $obTCIMLicencaImovelUnidadeAutonoma->setDado( "cod_construcao", $arDadosEdificacaoConstrucao[0] );
                    $obTCIMLicencaImovelUnidadeAutonoma->setDado( "cod_tipo", $arDadosEdificacaoConstrucao[1] );
                    $obTCIMLicencaImovelUnidadeAutonoma->inclusao();
                } else {
                    $obTCIMLicencaImovelUnidadeDependente = new TCIMLicencaImovelUnidadeDependente;
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "cod_licenca", $inNovoCodLicenca );
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "exercicio", Sessao::getExercicio() );
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "cod_construcao", $arDadosEdificacaoConstrucao[0] );
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "cod_tipo", $arDadosEdificacaoConstrucao[1] );
                    $obTCIMLicencaImovelUnidadeDependente->setDado( "cod_construcao_dependente", $arDadosEdificacaoConstrucao[3] );
                    $obTCIMLicencaImovelUnidadeDependente->inclusao();
                }
            }

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
                $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", Sessao::getExercicio() );
                $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );
                $obTCIMLicencaResponsavelTecnico->inclusao();
            }

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );
            if ( $rsNumDoc->Eof() ) {
                $inNumDocumento = 1;
            } else {
                $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
            }

            $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaDocumento->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
            $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
            $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
            $obTCIMLicencaDocumento->inclusao();

            if ($_REQUEST["inProcesso"]) {
                $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
                $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
                $obTCIMLicencaProcesso->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaProcesso->setDado( "exercicio", Sessao::getExercicio() );

                $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
                $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

                $obTCIMLicencaProcesso->inclusao();
            }

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setCodCadastro( 10 );
            $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaImovelValor );
            $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $inNovoCodLicenca, "exercicio" => Sessao::getExercicio(), "inscricao_municipal" => $_REQUEST["inCodImovel"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

            $obAtributos = new MontaAtributos;
            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
            }

            $obRCadastroDinamico->salvarValores();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgForm."&stAcao=incluir", "Licença ".$inNovoCodLicenca."/".Sessao::getExercicio()." incluída com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;

    case "incluirLote":
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de responsáveis está vazia.", "n_definir", "erro" );
            exit;
        }

        $obTCIMLicenca = new TCIMLicenca;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMLicenca );

            $obTCIMTipoLicenca = new TCIMTipoLicenca;
            $stFiltro = " AND permissao.cod_tipo = ".$_REQUEST["inTipoLicenca"];
            $obTCIMTipoLicenca->recuperaLicencaPorCGM( $rsTipoLicenca, $stFiltro );

            $obTCIMLicenca->recuperaProximaLicenca( $rsProximoCodigo );
            if ( $rsProximoCodigo->Eof() ) {
                $inNovoCodLicenca = 1;
            } else {
                $inNovoCodLicenca = $rsProximoCodigo->getCampo( "cod_licenca" ) + 1;
            }

            $obTCIMLicenca->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicenca->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicenca->setDado( "cod_tipo", $_REQUEST["inTipoLicenca"] );
            $obTCIMLicenca->setDado( "numcgm", Sessao::read('numCgm') );
            $obTCIMLicenca->setDado( "timestamp", $rsTipoLicenca->getCampo( "timestamp" ) );
            $obTCIMLicenca->setDado( "dt_inicio", $_REQUEST["dtValidadeInicio"] );
            if ( $_REQUEST["dtValidadeFim"] )
                $obTCIMLicenca->setDado( "dt_termino", $_REQUEST["dtValidadeFim"] );

            $obTCIMLicenca->setDado( "observacao", $_REQUEST["stObservacao"] );
            $obTCIMLicenca->inclusao();

            $obTCIMLicencaLote = new TCIMLicencaLote;
            $obTCIMLicencaLote->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaLote->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaLote->setDado( "cod_lote", $_REQUEST["cmbLotes"] );
            $obTCIMLicencaLote->inclusao();

            $obTCIMLicencaLoteArea = new TCIMLicencaLoteArea;
            $obTCIMLicencaLoteArea->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaLoteArea->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaLoteArea->setDado( "cod_lote", $_REQUEST["cmbLotes"] );
            $obTCIMLicencaLoteArea->setDado( "area", $_REQUEST["flAreaLicenca"] );
            $obTCIMLicencaLoteArea->inclusao();

            if ($_REQUEST["inTipoLicenca"] == 7) { //loteamento
                $obTCIMLicencaLoteLoteamento = new TCIMLicencaLoteLoteamento;
                $obTCIMLicencaLoteLoteamento->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaLoteLoteamento->setDado( "exercicio", Sessao::getExercicio() );
                $obTCIMLicencaLoteLoteamento->setDado( "cod_lote", $_REQUEST["cmbLotes"] );
                $obTCIMLicencaLoteLoteamento->setDado( "cod_loteamento", $_REQUEST["cmbLoteamento"] );
                $obTCIMLicencaLoteLoteamento->inclusao();
            } else { //separar/aglutinar
                $obTCIMLicencaLoteParcelamentoSolo = new TCIMLicencaLoteParcelamentoSolo;
                $obTCIMLicencaLoteParcelamentoSolo->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaLoteParcelamentoSolo->setDado( "exercicio", Sessao::getExercicio() );
                $obTCIMLicencaLoteParcelamentoSolo->setDado( "cod_lote", $_REQUEST["cmbLotes"] );
                if ($_REQUEST["inTipoLicenca"] == 8) {
                    $obTCIMLicencaLoteParcelamentoSolo->setDado( "cod_parcelamento", $_REQUEST["cmbDesmembramento"] );
                } else {
                    $obTCIMLicencaLoteParcelamentoSolo->setDado( "cod_parcelamento", $_REQUEST["cmbAglutinação"] );
                }

                $obTCIMLicencaLoteParcelamentoSolo->inclusao();
            }

            $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
            for ( $inX=0; $inX<count( $arResponsaveisSessao ); $inX++ ) {
                $obTCIMLicencaResponsavelTecnico->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaResponsavelTecnico->setDado( "exercicio", Sessao::getExercicio() );
                $obTCIMLicencaResponsavelTecnico->setDado( "numcgm", $arResponsaveisSessao[$inX]["num_cgm"] );
                $obTCIMLicencaResponsavelTecnico->setDado( "sequencia", $arResponsaveisSessao[$inX]["sequencia"] );

                $obTCIMLicencaResponsavelTecnico->inclusao();
            }

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->recuperaNumeroDocumento( $rsNumDoc );
            if ( $rsNumDoc->Eof() ) {
                $inNumDocumento = 1;
            } else {
                $inNumDocumento = $rsNumDoc->getCampo("num_documento") + 1;
            }

            $arTMPDadosDocumento = explode( "-", $_REQUEST["cmbModelo"] );

            $obTCIMLicencaDocumento = new TCIMLicencaDocumento;
            $obTCIMLicencaDocumento->setDado( "cod_licenca", $inNovoCodLicenca );
            $obTCIMLicencaDocumento->setDado( "exercicio", Sessao::getExercicio() );
            $obTCIMLicencaDocumento->setDado( "cod_tipo_documento", $arTMPDadosDocumento[1] );
            $obTCIMLicencaDocumento->setDado( "cod_documento", $arTMPDadosDocumento[0] );
            $obTCIMLicencaDocumento->setDado( "num_documento", $inNumDocumento );
            $obTCIMLicencaDocumento->inclusao();

            if ($_REQUEST["inProcesso"]) {
                $arDadosProcesso = explode( "/", $_REQUEST["inProcesso"] );
                $obTCIMLicencaProcesso = new TCIMLicencaProcesso;
                $obTCIMLicencaProcesso->setDado( "cod_licenca", $inNovoCodLicenca );
                $obTCIMLicencaProcesso->setDado( "exercicio", Sessao::getExercicio() );

                $obTCIMLicencaProcesso->setDado( "cod_processo", $arDadosProcesso[0] );
                $obTCIMLicencaProcesso->setDado( "ano_exercicio", $arDadosProcesso[1] );

                $obTCIMLicencaProcesso->inclusao();
            }

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setCodCadastro( 10 );
            $obRCadastroDinamico->setPersistenteValores( new TCIMAtributoTipoLicencaLoteValor );
            $obRCadastroDinamico->setChavePersistenteValores( array( "cod_licenca" => $inNovoCodLicenca, "exercicio" => Sessao::getExercicio(), "cod_lote" => $_REQUEST["cmbLotes"], "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

            $obAtributos = new MontaAtributos;
            $obAtributos->setName      ( "Atributo_" );
            $obAtributos->recuperaVetor( $arChave );
            foreach ($arChave as $key=>$value) {
                $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
                $inCodAtributo = $arChaves[0];
                if ( is_array($value) ) {
                    $value = implode(",",$value);
                }

                $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo, $value );
            }

            $obRCadastroDinamico->salvarValores();

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgForm."&stAcao=incluir", "Licença ".$inNovoCodLicenca."/".Sessao::getExercicio()." incluída com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;
}
