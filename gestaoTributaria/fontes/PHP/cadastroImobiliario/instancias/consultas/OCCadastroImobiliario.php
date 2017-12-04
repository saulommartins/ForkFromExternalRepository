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
    * Frame Oculto para relatorio de Cadastro Imobiliario
    * Data de Criação: 25/04/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCCadastroImobiliario.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTransferencia.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNaturezaTransferencia.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMProprietario.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php" );

//Define o nome dos arquivos PHP
$stPrograma          = "CadastroImobiliario";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;

$arFiltro = sessao::read('filtroRelatorio') ;

$obRCIMTransferencia = new RCIMTransferencia;
$obRCIMTransferencia->setInscricaoMunicipal( $arFiltro['inCodInicioInscricao'] );
$obRCIMTransferencia->setEfetivacao        ( 'f' );
$obRCIMTransferencia->listarTransferencia  ( $rsListaTransferencia );
$obRCIMTransferencia->consultarTransferencia();

$obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;
$obRCIMNaturezaTransferencia->setCodigoNatureza( $obRCIMTransferencia->getCodigoNatureza() );
$obRCIMNaturezaTransferencia->consultarNaturezaTransferencia();

$arTransfSessao                     = array();
$inCount = 0;
while (!$rsListaTransferencia->eof()) {
    if ($rsListaTransferencia->getCampo('dt_efetivacao') != "") {
        $arTransfSessao[$inCount]["natureza"]      = $obRCIMNaturezaTransferencia->getDescricaoNatureza();
        $arTransfSessao[$inCount]["creci"]         = $rsListaTransferencia->getCampo("creci")." - ".$rsListaTransferencia->getCampo("nomcgm");
        $arTransfSessao[$inCount]["dt_efetivacao"] = $rsListaTransferencia->getCampo("dt_efetivacao");
        $arTransfSessao[$inCount]["processo"]      = $rsListaTransferencia->getCampo("cod_processo")."/".$rsListaTransferencia->getCampo("exercicio_proc");
        $arTransfSessao[$inCount]["observacao"]    = $rsListaTransferencia->getCampo("observacao");
        $inCount++;
    }
    $rsListaTransferencia->proximo();
}

$rsTransferencia = new RecordSet;
$rsTransferencia->preenche( $arTransfSessao );
$rsTransferencia->setPrimeiroElemento();

$arTransfSessao = $rsTransferencia;
Sessao::write('transferencia', $arTransfSessao);

$obRegra = new RCIMImovel( new RCIMLote );
$obRegra->setNumeroInscricao( $arFiltro['inCodInicioInscricao'] );

$obRCIMProprietario  = new RCIMProprietario ( $obRegra );
$obRCIMProprietario->listarProprietariosPorImovel( $rsProprietarios );

$obRCIMProprietario->setTimestamp( $rsProprietarios->getCampo("timestamp") );
$obRCIMProprietario->listarExProprietarios( $rsExProprietarios );
$arTMP = array();
$inX = 0;
$obRCGM = new RCGM;

while ( !$rsExProprietarios->Eof() ) {
    $obRCGM->setNumCGM( $rsExProprietarios->getCampo("numcgm") );
    $obRCGM->consultar( $rsCGM );
    $arTMP[$inX]["numcgm"] = $rsExProprietarios->getCampo("numcgm");
    $arTMP[$inX]["nomcgm"] = $obRCGM->getNomCGM();
    $arTMP[$inX]["quota"] = $rsExProprietarios->getCampo( "cota"   );
    $rsExProprietarios->proximo();
}

$rsExProprietarios->preenche( $arTMP );
$rsExProprietarios->setPrimeiroElemento();
Sessao::write('exproprietarios', $rsExProprietarios);
$obRegra->listarImoveisConsulta( $rsCadastroImobiliario );
$obRegra->consultarImovel( NULL, TRUE );

$arProprietariosSessao = array();
$arPromitentesSessao   = array();

//MONTA LISTA DE PROPRIETÁRIOS
foreach ($obRegra->arRCIMProprietario as $obRCIMProprietario) {
    $arProprietariosSessao[] = array( "inNumCGM"  => $obRCIMProprietario->getNumeroCGM(),
                                      "stNomeCGM" => $obRCIMProprietario->obRCGM->getNomCGM(),
                                      "flQuota"   => number_format( $obRCIMProprietario->getCota(), 2, ",", "."),
                                      "ordem"     => $obRCIMProprietario->getOrdem()  );
}

$rsProprietarios = new RecordSet;
$rsProprietarios->preenche( $arProprietariosSessao );
Sessao::write('proprietarios', $rsProprietarios);

foreach ($obRegra->arRCIMProprietarioPromitente as $obRCIMProprietarioPromitente) {
    $arPromitentesSessao[] = array( "inNumCGM"  => $obRCIMProprietarioPromitente->getNumeroCGM(),
                                    "stNomeCGM" => $obRCIMProprietarioPromitente->obRCGM->getNomCGM(),
                                    "flQuota"   => number_format( $obRCIMProprietarioPromitente->getCota(),2,",", "." ),
                                    "ordem"     => $obRCIMProprietarioPromitente->getOrdem()  );
}

$rsProprietariosPromitente = new RecordSet;
$rsProprietariosPromitente->preenche( $arPromitentesSessao );
$rsProprietariosPromitente->setPrimeiroElemento();
Sessao::write('promitentes', $rsProprietariosPromitente);

$arTMP = $rsCadastroImobiliario->getElementos();
$arTMP[0]["matricula"]           = $obRegra->getMatriculaRegistroImoveis();
$arTMP[0]["dt_inscricao"]        = $obRegra->getDataInscricao();
$arTMP[0]["condominio"]          = $obRegra->obRCIMCondominio->getNomCondominio();
$arTMP[0]["area_imovel"]         = $obRegra->getAreaImovel();
$arTMP[0]["area_edificada"]      = $obRegra->getAreaEdificada();
$arTMP[0]["area_edificada_lote"] = $obRegra->roRCIMLote->getAreaEdificadaLote();
$arTMP[0]["creci"]               = $obRegra->obRCIMImobiliaria->getRegistroCreci();
$arTMP[0]["fracao_ideal"]        = $obRegra->getFracaoIdeal();
$rsCadastroImobiliario->preenche( $arTMP );
$rsCadastroImobiliario->setPrimeiroElemento();
Sessao::write('rsImoveis', $rsCadastroImobiliario);

$rsCondominio = new RecordSet;
if ( $rsCadastroImobiliario->getCampo("cod_condominio") ) {
    $obRCIMCondominio = new RCIMCondominio;
    $obRCIMCondominio->setCodigoCondominio ( $rsCadastroImobiliario->getCampo("cod_condominio") );
    $obRCIMCondominio->consultarCondominio ( $rsCondominio );
}

Sessao::write('rsCondominio', $rsCondominio);

$obRCIMEdificacao = new RCIMEdificacao;
if ( $rsCadastroImobiliario->getCampo("inscricao_municipal" ) && !$rsCadastroImobiliario->getCampo("cod_condominio") ) {
    $obRCIMEdificacao->obRCIMImovel->setNumeroInscricao( $rsCadastroImobiliario->getCampo("inscricao_municipal") );
    $obRCIMEdificacao->boListarBaixadas = true;
    $obRCIMEdificacao->listarEdificacoesConsulta( $rsListaEdificacoes );
    $arTMP = $rsListaEdificacoes->getElementos();
} else {
    $obRCIMEdificacao->obRCIMCondominio->setCodigoCondominio( $rsCadastroImobiliario->getCampo("cod_condominio") );
    $obRCIMEdificacao->setTipoVinculo( 'Condomínio' );
    $obRCIMEdificacao->listarEdificacoes( $rsListaEdificacoes );
    $obRCIMEdificacao->buscaAreaConstrucaoCondominio( $flAreaEdificacao );
    $arTMP = $rsListaEdificacoes->getElementos();
    $arTMP[0]["area"] = $flAreaEdificacao;
}

if ( $rsListaEdificacoes->getCampo('data_baixa') && ($rsListaEdificacoes->getCampo('data_termino') == "") ) {
    $arTMP[0]["situacao"] = "Baixado";
} else {
    $arTMP[0]["situacao"] = "Ativo";
}

$rsListaEdificacoes->preenche( $arTMP );
$rsListaEdificacoes->setPrimeiroElemento();
Sessao::write('rsEdificacoes', $rsListaEdificacoes);

$obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
if ( $rsCadastroImobiliario->getCampo("inscricao_municipal" ) && !$rsCadastroImobiliario->getCampo("cod_condominio") ) {
    $obRCIMConstrucaoOutros->obRCIMImovel->setNumeroInscricao( $rsCadastroImobiliario->getCampo("inscricao_municipal" ) );
    $obRCIMConstrucaoOutros->setTipoVinculo( "'Dependente'" );
} else {
    $obRCIMConstrucaoOutros->obRCIMCondominio->setCodigoCondominio( $rsCadastroImobiliario->getCampo("cod_condominio") );
    $obRCIMConstrucaoOutros->setTipoVinculo( "'Condomínio'" );
}

$obRCIMConstrucaoOutros->listarConstrucoes( $rsListaConstrucao );
Sessao::write('rsConstrucoes', $rsListaConstrucao);

if ( $rsCadastroImobiliario->getCampo("tipo_lote" ) == 'Urbano' ) {
    $obRCIMLote = new RCIMLoteUrbano;
} else {
    $obRCIMLote = new RCIMLoteRural;
}

$obRCIMLote->setCodigoLote( $rsCadastroImobiliario->getCampo("cod_lote" ) );
$obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $rsCadastroImobiliario->getCampo("cod_localizacao") );
$obRCIMLote->consultarLote();
$obRCIMLote->listarProcessos( $rsListaProcesso );
if ( $obRCIMLote->getTimestampLote() == $rsListaProcesso->getCampo( "timestamp" ) ) {
    $stProcesso = $rsListaProcesso->getCampo( "cod_processo_ano" );
}

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
$obErro = $obRCIMConfrontacao->listarConfrontacoesPorLote( $rsListaConfrontacao );
$arConfrontacoesSessao = array();
if ( !$obErro->ocorreu() ) {
    $inLinha = 0;
    while ( !$rsListaConfrontacao->eof() ) {
        $flExtensao = number_format( $rsListaConfrontacao->getCampo('valor'), 2, ",", "." );
        $boTestada = $rsListaConfrontacao->getCampo('principal') == "Sim" ? "S" : "N";
        $stTrecho = "";
        $stChaveTrecho = "";
        if ( $rsListaConfrontacao->getCampo('tipo') == "Trecho" ) {
            $obRCIMTrecho = new RCIMTrecho;
            $stChaveTrecho  = $rsListaConfrontacao->getCampo('cod_logradouro').".";
            $stChaveTrecho .= $rsListaConfrontacao->getCampo('sequencia');
            $obRCIMTrecho->setCodigoLogradouro ( $rsListaConfrontacao->getCampo('cod_logradouro') );
            $obRCIMTrecho->setCodigoTrecho     ( $rsListaConfrontacao->getCampo('cod_trecho')     );
            $obRCIMTrecho->setSequencia        ( $rsListaConfrontacao->getCampo('sequencia')      );
            $obRCIMTrecho->consultarTrecho( $rsTrecho );
            $stTrecho = $obRCIMTrecho->getNomeLogradouro()."(".$rsListaConfrontacao->getCampo('sequencia').")";
        }
        switch ( strtoupper(  $rsListaConfrontacao->getCampo('tipo') ) ) {
            case "TRECHO":
                $stTrecho = $rsTrecho->getCampo ("tipo_nome");
                $stSequencia  = $rsTrecho->getCampo ("sequencia");
                $stDescricao = $rsListaConfrontacao->getCampo('cod_logradouro').".".$rsTrecho->getCampo ("sequencia")." - ".$stTrecho;
            break;
            case "LOTE":
                $obCIMLoteConfrontacao = new RCIMLote;
                $obCIMLoteConfrontacao->setCodigoLote( $rsListaConfrontacao->getCampo('cod_lote_confrontacao') );
                $obCIMLoteConfrontacao->consultarLote();
                $stDescricao = $obCIMLoteConfrontacao->getNumeroLote();
            break;
            case "OUTROS":
                $stDescricao = $rsListaConfrontacao->getCampo('descricao');
            break;
        }

        $arConfrontacoes = array(
                     "inCodigoConfrontacao"           => $rsListaConfrontacao->getCampo('cod_confrontacao'),
                     "inCodigoPontoCardeal"           => $rsListaConfrontacao->getCampo('cod_ponto'),
                     "stNomePontoCardeal"             => $rsListaConfrontacao->getCampo('nom_ponto'),
                     "stTipoConfrotacao"              => strtolower( $rsListaConfrontacao->getCampo('tipo') ),
                     "stLsTipoConfrotacao"            => ucfirst( $rsListaConfrontacao->getCampo('tipo') ),
                     "flExtensao"                     => $flExtensao,
                     "boTestada"                      => $boTestada,
                     "stTestada"                      => $rsListaConfrontacao->getCampo('principal'),
                     "inCodigoLoteConfrontacao"       => $rsListaConfrontacao->getCampo('cod_lote_confrontacao'),
                     "inCodigoTrechoConfrnotacao"     => $rsListaConfrontacao->getCampo('cod_trecho'),
                     "inCodigoLogradouroConfrontacao" => $rsListaConfrontacao->getCampo('cod_logradouro'),
                     "stDescricaoOutros"              => $rsListaConfrontacao->getCampo('descricao'),
                     "stChaveTrecho"                  => $stChaveTrecho,
                     "stTrecho"                       => $stTrecho,
                     "stDescricao"                    => $stDescricao,
                     "inLinha"                        => $inLinha++ );

        $arConfrontacoesSessao[] = $arConfrontacoes;
        $rsListaConfrontacao->proximo();
    }
}

$rsListaConfrontacao = new RecordSet;
$rsListaConfrontacao->preenche( $arConfrontacoesSessao );
$arConfrontacoesSessao =  $rsListaConfrontacao;
Sessao::write('confrontacoes', $arConfrontacoesSessao);

$arChaveAtributoLote = array( "cod_lote" => $rsCadastroImobiliario->getCampo("cod_lote") );
$obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
$obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLote );

Sessao::write('atributos_lote', $rsAtributosLote);

$arLoteSessao = array();
$arLoteSessao[0]["numero_lote"]       = $obRCIMLote->getNumeroLote()." - ".$rsCadastroImobiliario->getCampo("tipo_lote" );
$arLoteSessao[0]["localizacao_lote"]  = $obRCIMLote->obRCIMLocalizacao->getValorComposto()." - ".$obRCIMLote->obRCIMLocalizacao->getNomeLocalizacao();
$arLoteSessao[0]["area_lote"]         = $obRCIMLote->getAreaLote();
$arLoteSessao[0]["profundidade_lote"] = $obRCIMLote->getProfundidadeMedia();
$arLoteSessao[0]["dt_inscricao_lote"] = $obRCIMLote->getDataInscricao();
$arLoteSessao[0]["processo_lote"]     = $stProcesso;
if ($obRCIMLote->getDataBaixa() && ($obRCIMLote->getDataTermino() == "")) {
    $arLoteSessao[0]["situacao_lote"]= "Baixado";
} else {
    $arLoteSessao[0]["situacao_lote"]= "Ativo";
}

$arLoteSessao[0]["dt_baixa_lote"] = $obRCIMLote->getDataBaixa();
$arLoteSessao[0]["justificativa_lote"] = $obRCIMLote->getJustificativa();

$rsListaLote = new RecordSet;
$rsListaLote->preenche( $arLoteSessao );
Sessao::write('lote', $rsListaLote);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCadastroImobiliario.php" );
?>
