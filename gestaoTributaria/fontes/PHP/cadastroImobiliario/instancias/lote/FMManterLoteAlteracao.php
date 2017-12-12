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
    * Página de Formulário para a alteração de lote
    * Data de Criação   : 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLoteAlteracao.php 62460 2015-05-12 19:04:30Z jean $

    * Casos de uso: uc-05.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

$stLink = Sessao::read('stLink');

include_once 'OCManterLote.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterLote";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgListValidar = "LSValidarLote.php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//[funcionalidade] => 178 ->Lote Urbano  193 ->Lote Rural
if ($_REQUEST["funcionalidade"] == 178) {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $obRCIMLote = new RCIMLoteRural;
}

//MASCARA PROCESSO
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$obRCIMConfiguracao->consultarMascaraLote    ( $stMascaraLote     );

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
$obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );
$arPontoCardealSessao = Sessao::read('ponto_cardeal');

while ( !$rsListaPontosCardeais->eof() ) {
    $arPontoCardealSessao[$rsListaPontosCardeais->getCampo("cod_ponto")] = $rsListaPontosCardeais->getCampo("nom_ponto");
    $rsListaPontosCardeais->proximo();
}
Sessao::write('ponto_cardeal', $arPontoCardealSessao);
$rsListaPontosCardeais->setPrimeiroElemento();

if ($_REQUEST["inCodigoLote"]) {
    $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
    if ($_REQUEST["stOrigem"] == "validar") {
        $obRCIMLote->consultaLoteOriginal( $inCodigoLoteOriginal, $nuLotesValidacao );
        $stFiltro  = " WHERE COD_LOTE = ".$obRCIMLote->inCodigoLote;
        $obErro = $obRCIMLote->obTCIMParcelamentoSolo->recuperaTodos( $rsRecordSetParcelamento, $stFiltro, "", $boTransacao );
    }
}

$obRCIMLote->listarUnidadeMedida( $rsUnidadeMedida );
$obRCIMLote->setCodigoGrandeza( $_REQUEST["inCodigoGrandeza"] );
$obRCIMLote->setCodigoUnidade ( $_REQUEST["inCodigoUnidade"]  );

//FALTA DE RETORNO DA MIGRACAO
$obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
$obRCIMLote->listarLotes( $rsLoteConfrotacao );

$obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );

if ($_REQUEST["stOrigem"] == "validar") {
    $obErro = $obRCIMLote->consultarLoteValidacao();
} else {
    $obErro = $obRCIMLote->consultarLote();
}

$rsListaConfrontacao = new RecordSet;

//MONTA A LISTA DE CONFRONTACOES
$obErro = $obRCIMConfrontacao->listarConfrontacoesPorLote( $rsListaConfrontacao );

$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

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
                /*$obRCIMTrecho = new RCIMTrecho;
                $obRCIMTrecho->setCodigoLogradouro ( $rsListaConfrontacao->getCampo('cod_logradouro') );
                $obRCIMTrecho->setSequencia        ( $rsListaConfrontacao->getCampo('sequencia') );
                $obRCIMTrecho->setCodigoTrecho     ( $rsListaConfrontacao->getCampo('cod_trecho') );
                $obRCIMTrecho->consultarTrecho( $rsTrechoDois );*/

                $stTrecho = $rsTrecho->getCampo("tipo_nome");
                $stSequencia  = $rsTrecho->getCampo ("sequencia");
                $stDescricao = $rsListaConfrontacao->getCampo('cod_logradouro').".".$rsListaConfrontacao->getCampo ("sequencia")." - ".$stTrecho;
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
    Sessao::write('confrontacoes', $arConfrontacoesSessao);
    $rsListaConfrontacao = new RecordSet;
    $rsListaConfrontacao->preenche( Sessao::read('confrontacoes') );
    $rsListaConfrontacao->addFormatacao("stDescricao","N_TO_BR");
    $stJs = montaListaConfrontacao( $rsListaConfrontacao );
}

// consulta se processo é de inclusao, se for processo deve ser um label
// TIMESTAMP DO PROCESSO
$rsProcessos = new Recordset;
//$obRCIMLote->listarProcessos($rsProcessos);
//$tmTimestampProcesso = $rsProcessos->getCampo("timestamp");
// TIMESTAMP DO LOTE
//$rsLoteProcesso = new Recordset;
//$tmTimestampLote = $obRCIMLote->getTimestampLote();
//echo "Timestamp do Lote: ".$tmTimestampLote." || Timestamp do Processo ". $tmTimestampProcesso;

//MONTA OS ATRIBUTOS
$arChaveAtributo = array( "cod_lote" => $_REQUEST["inCodigoLote"] );
$obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributosLote );

$obMontaAtributosLote = new MontaAtributos;
$obMontaAtributosLote->setTitulo     ( "Atributos"              );
$obMontaAtributosLote->setName       ( "AtributoLote_"    );
$obMontaAtributosLote->setLabel      ( true );
$obMontaAtributosLote->setRecordSet  ( $rsAtributosLote );

$boLabel = true;
if ($stAcao == "validar") {
    $boLabel = false;
}
//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnAcaoConfrontacao = new Hidden;
$obHdnAcaoConfrontacao->setName( "stAcaoConfrontacao" );
$obHdnAcaoConfrontacao->setValue( $stAcaoConfrontacao );

$obHdnIndiceConfrontacao = new Hidden;
$obHdnIndiceConfrontacao->setName( "inIndice" );
$obHdnIndiceConfrontacao->setValue( $inIndice );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $stTrecho );

$obHdnTimestampLote = new Hidden;
$obHdnTimestampLote->setName ( "hdnTimestampLote"   );
$obHdnTimestampLote->setValue( $tmTimestampLote     );

$obHdnTimestampParcelamento = new Hidden;
$obHdnTimestampParcelamento->setName ( "hdnTimestampParcelamento"   );
$obHdnTimestampParcelamento->setValue( $obRCIMLote->getDataParcelamento() );

$obHdMascaraLote = new Hidden;
$obHdMascaraLote->setName ( "hdnMascaraLote"   );
$obHdMascaraLote->setValue( $stMascaraLote     );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );
$obHdnCodigoUF->setValue ( $obRCIMLote->obRCIMBairro->getCodigoUF() );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );
$obHdnCodigoMunicipio->setValue( $obRCIMLote->obRCIMBairro->getCodigoMunicipio() );

$obHdnLotesValidacao = new Hidden;
$obHdnLotesValidacao->setName( "nuLotesValidacao" );
$obHdnLotesValidacao->setValue( $nuLotesValidacao );

//Alateracao
$obHdnCodigoLote = new Hidden;
$obHdnCodigoLote->setName  ( "inCodigoLote" );
$obHdnCodigoLote->setValue ( $_REQUEST["inCodigoLote"] );

$obHdnCodigoLoteOriginal = new Hidden;
$obHdnCodigoLoteOriginal->setName  ( "inCodigoLoteOriginal" );
$obHdnCodigoLoteOriginal->setValue ( $inCodigoLoteOriginal );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue( $_REQUEST["inCodigoLocalizacao"] );

//Flag de verificação da origem da alteração caso seja uma alteração a partir do desmembramento
//deve ser feito uma alteração na tabela de lote_parcelado
$obHdnOrigem = new Hidden;
$obHdnOrigem->setName  ( "stOrigem" );
$obHdnOrigem->setValue ( $_REQUEST["stOrigem"]  );

$obHdnCodParcelamento = new Hidden;
$obHdnCodParcelamento->setName  ( "inCodigoParcelamento" );
$obHdnCodParcelamento->setValue ( $_REQUEST["inCodigoParcelamento"] );

$obSpnEdificacoes = new Span;
$obSpnEdificacoes->setId( "lsListaEdificacoes" );

//DADOS PARA ABA LOTE
if ( $_REQUEST["stOrigem"] == "validar" and $rsRecordSetParcelamento->getNumLinhas() < 1 ) {
    $obTxtNumeroLote = new TextBox;
    $obTxtNumeroLote->setName      ( "stNumeroLote"                 );
    $obTxtNumeroLote->setId        ( "stNumeroLote"                 );
    $obTxtNumeroLote->setRotulo    ( "Número do lote"               );
    $obTxtNumeroLote->setMaxLength ( strlen($stMascaraLote)         );
    $obTxtNumeroLote->setSize      ( 10                             );
    $obTxtNumeroLote->setNull      ( false                          );
    $obTxtNumeroLote->setValue     ( STR_PAD($obRCIMLote->getNumeroLote(),strlen($stMascaraLote),'0',STR_PAD_LEFT)   );
    $obTxtNumeroLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );
} else {
    $obHdnNumeroLote = new Hidden;
    $obHdnNumeroLote->setName  ( "stNumeroLote" );
    $obHdnNumeroLote->setValue ( $obRCIMLote->getNumeroLote() );

    $obTxtNumeroLote = new Label;
    $obTxtNumeroLote->setName               ( "stNumeroLote"    );
    $obTxtNumeroLote->setId                 ( "stNumeroLote"    );
    $obTxtNumeroLote->setRotulo             ( "Número do Lote "  );
    $obTxtNumeroLote->setValue              ( STR_PAD($obRCIMLote->getNumeroLote(),strlen($stMascaraLote),'0',STR_PAD_LEFT) );
}

$obLblNomeLocalizacao = new Label;
$obLblNomeLocalizacao->setName   ( "stNomeLocalizacao" );
$obLblNomeLocalizacao->setRotulo ( "Localização"       );
$stNomeLocalizacao  = $obRCIMLote->obRCIMLocalizacao->getValorComposto();
$stNomeLocalizacao .= " - ".$obRCIMLote->obRCIMLocalizacao->getNomeLocalizacao();
$obLblNomeLocalizacao->setValue  ( $stNomeLocalizacao );

$obTxtAreaLote = new Numerico;
$obTxtAreaLote->setName      ( "flAreaLote" );
$obTxtAreaLote->setId        ( "flAreaLote" );
$obTxtAreaLote->setRotulo    ( "Área"       );
$obTxtAreaLote->setMaxLength ( 18           );
$obTxtAreaLote->setSize      ( 18           );
$obTxtAreaLote->setFloat     ( true         );
$obTxtAreaLote->setTitle     ( "Área total do lote em metros quadrados ou ectares" );
$obTxtAreaLote->setNull      ( false        );
$obTxtAreaLote->setNegativo  ( false        );
$obTxtAreaLote->setNaoZero   ( true         );
$obTxtAreaLote->setMaxValue  ( 999999999999.99 );
$obTxtAreaLote->setValue     ( $obRCIMLote->getAreaLote() );

$obCmbUnidadeMedida = new Select;
$obCmbUnidadeMedida->setName      ( "stChaveUnidadeMedida"         );
$obCmbUnidadeMedida->setStyle     ( "width: 250px"                 );
$obCmbUnidadeMedida->setRotulo    ( "Área"                         );
$obCmbUnidadeMedida->setNull      ( false                          );
$obCmbUnidadeMedida->setCampoID   ( "[cod_unidade]-[cod_grandeza]" );
$obCmbUnidadeMedida->setCampoDesc ( "[nom_unidade] [simbolo]"      );
$obCmbUnidadeMedida->preencheCombo ( $rsUnidadeMedida );
$obCmbUnidadeMedida->setValue( $obRCIMLote->getCodigoUnidade()."-".$obRCIMLote->getCodigoGrandeza() );

$obTxtProfundidadeMedia = new Numerico;
$obTxtProfundidadeMedia->setName      ( "flProfundidadeMedia" );
$obTxtProfundidadeMedia->setRotulo    ( "Profundidade Média"  );
$obTxtProfundidadeMedia->setNull      ( false                 );
$obTxtProfundidadeMedia->setSize      ( 18                    );
$obTxtProfundidadeMedia->setMaxLength ( 18                    );
$obTxtProfundidadeMedia->setFloat     ( true                  );
$obTxtProfundidadeMedia->setNegativo  ( false                 );
$obTxtProfundidadeMedia->setNaoZero   ( true                  );
$obTxtProfundidadeMedia->setMaxValue  ( 999999999999.99 );
$obTxtProfundidadeMedia->setTitle     ( "Informe a profundidade média do lote (em metros)" );
$obTxtProfundidadeMedia->setValue     ( $obRCIMLote->getProfundidadeMedia() );

$obTxtDataInscricaoLote = new Data;
$obTxtDataInscricaoLote->setName   ( "dtDataInscricaoLote" );
$obTxtDataInscricaoLote->setRotulo ( "Data de Inscrição"   );
$obTxtDataInscricaoLote->setNull   ( false                 );
$obTxtDataInscricaoLote->setValue  ( $obRCIMLote->getDataInscricao() );

if ($_REQUEST[ "inNumProcesso" ]) {
    $inNumProcesso = $_REQUEST[ "inNumProcesso" ]."/".$_REQUEST[ "stExercicio" ];
}

$obLblProcesso = new Label;
$obLblProcesso->setName  ( "inNumProcesso" );
//$obLblProcesso->setValue ( $_REQUEST[ "inNumProcesso" ]."/".$_REQUEST[ "stExercicio" ]  );
$obLblProcesso->setValue ($rsProcessos->getCampo("cod_processo")."/".$rsProcessos->getCampo("ano_exercicio"));
$obLblProcesso->setRotulo( "Processo"      );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo que formaliza" );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
if ($inNumProcesso) {
    $stValorProcesso = $inNumProcesso;
    if ($stExercicio) {
         $stValorProcesso .= '/'.$stExercicio;
    }
}else
    $stValorProcesso = "";

$obBscProcesso->obCampoCod->setValue ( $stValorProcesso ); //( $inNumProcesso .'/'. $stExercicio );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//CONFRONTAÇÕES
$obCmbPontoCardeal = new Select;
$obCmbPontoCardeal->setName       ( "inCodigoPontoCardeal" );
$obCmbPontoCardeal->setRotulo     ( "Ponto Cardeal"        );
$obCmbPontoCardeal->setStyle      ( "width: 150px"         );
$obCmbPontoCardeal->setCampoId    ( "cod_ponto"            );
$obCmbPontoCardeal->setCampoDesc  ( "nom_ponto"            );
$obCmbPontoCardeal->preencheCombo ( $rsListaPontosCardeais );

$obRdoTipoTrecho = new Radio;
$obRdoTipoTrecho->setName   ( "stTipoConfrotacao"    );
$obRdoTipoTrecho->setLabel  ( "Trecho"               );
$obRdoTipoTrecho->setValue  ( "trecho"               );
$obRdoTipoTrecho->setRotulo ( "*Tipo"                );
$obRdoTipoTrecho->setTitle  ( "Tipo de confrontação" );
$obRdoTipoTrecho->obEvento->setOnChange( "javascript: montaConfrontacao( 'trecho' );" );

$obRdoTipoLote = new Radio;
$obRdoTipoLote->setName  ( "stTipoConfrotacao" );
$obRdoTipoLote->setLabel ( "Lote"              );
$obRdoTipoLote->setValue ( "lote"              );
$obRdoTipoLote->obEvento->setOnChange( "javascript: montaConfrontacao( 'lote' );" );

$obRdoTipoOutros = new Radio;
$obRdoTipoOutros->setName  ( "stTipoConfrotacao" );
$obRdoTipoOutros->setLabel ( "Outros"            );
$obRdoTipoOutros->setValue ( "outros"            );
$obRdoTipoOutros->obEvento->setOnChange( "javascript: montaConfrontacao( 'outros' );" );

$obTxtExtensao = new Numerico;
$obTxtExtensao->setName      ( "flExtensao" );
$obTxtExtensao->setRotulo    ( "*Extensão"  );
$obTxtExtensao->setSize      ( 10           );
$obTxtExtensao->setMaxlength ( 10           );
$obTxtExtensao->setTitle     ( "Extensão em metros da confrontação" );

$obCmbConfrontacaoLote = new Select;
$obCmbConfrontacaoLote->setName       ( "inCodigoLoteConfrontacao" );
$obCmbConfrontacaoLote->setRotulo     ( "Lote"                     );
$obCmbConfrontacaoLote->setStyle      ( "width: 150px"             );
$obCmbConfrontacaoLote->addOption     ( "", "Selecione"            );
$obCmbConfrontacaoLote->setCampoID    ( "cod_lote"                 );
$obCmbConfrontacaoLote->setCampoDesc  ( "cod_lote"                 );
$obCmbConfrontacaoLote->preencheCombo ( $rsLoteConfrotacao         );

$obTxtDescricaoOutros = new TextArea;
$obTxtDescricaoOutros->setName     ( "stDescricaoOutros" );
$obTxtDescricaoOutros->setRotulo   ( "Descrição"        );
$obTxtDescricaoOutros->setCols     ( 50                 );
$obTxtDescricaoOutros->setRows     ( 5                  );

$obBuscaTrecho = new BuscaInner;
$obBuscaTrecho->setId                             ( "stNumTrecho"     );
$obBuscaTrecho->setNull                           ( true              );
$obBuscaTrecho->setRotulo                         ( "*Trecho"         );
$obBuscaTrecho->obCampoCod->setName               ( "inNumTrecho"     );
$obBuscaTrecho->obCampoCod->setValue              ( $inNumTrecho      );
$obBuscaTrecho->obCampoCod->setInteiro            ( false             );
$obBuscaTrecho->obCampoCod->obEvento->setOnChange ( "buscarTrecho();" );
$obBuscaTrecho->setFuncaoBusca                    ("abrePopUp('".CAM_GT_CIM_POPUPS."trecho/FLProcurarTrecho.php','frm','inNumTrecho','stNumTrecho','','".Sessao::getId()."','800','550')");

$obBscBairro = new BuscaInner;
$obBscBairro->setRotulo ( "Bairro"                         );
$obBscBairro->setId     ( "innerBairroLote"                );
$obBscBairro->setNull   ( false                            );
$obBscBairro->setValue ( $obRCIMLote->obRCIMBairro->getNomeBairro() );
$obBscBairro->setTitle  ( "Bairro em que o lote está localizado" );
$obBscBairro->obCampoCod->setName  ( "inCodigoBairroLote"  );
$obBscBairro->obCampoCod->setValue ( $obRCIMLote->obRCIMBairro->getCodigoBairro()  );
$obBscBairro->obCampoCod->obEvento->setOnChange ( "BloqueiaFrames(true,false);buscaBairro();" );
$stBusca  = "abrePopUp('".CAM_GT_CIM_POPUPS."bairroSistema/FLProcurarBairro.php','frm','inCodigoBairroLote','innerBairroLote',''";
$stBusca .= " ,'".Sessao::getId()."','800','550')";
$obBscBairro->setFuncaoBusca ( $stBusca );

$obBtnIncluirConfrontacao = new Button;
$obBtnIncluirConfrontacao->setName( "btnIncluirTrecho" );
$obBtnIncluirConfrontacao->setValue( "Definir" );
$obBtnIncluirConfrontacao->obEvento->setOnClick( "incluirConfrontacao();" );

$obBtnLimparConfrontacao = new Button;
$obBtnLimparConfrontacao->setValue( "Limpar" );
$obBtnLimparConfrontacao->obEvento->setOnClick( "limparConfrontacao();" );

$obSpnConfrontacao = new Span;
$obSpnConfrontacao->setId( "spnConfrontacao" );

$obSpnListaConfrontacao = new Span;
$obSpnListaConfrontacao->setId( "lsListaConfrontacoes" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm    );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnCtrl               );
$obFormulario->addHidden          ( $obHdnAcao               );
$obFormulario->addHidden          ( $obHdnCodParcelamento    );
$obFormulario->addHidden          ( $obHdnFuncionalidade     );
$obFormulario->addHidden          ( $obHdMascaraLote         );
$obFormulario->addHidden          ( $obHdnCodigoLote         );
$obFormulario->addHidden          ( $obHdnCodigoLoteOriginal );
$obFormulario->addHidden          ( $obHdnIndiceConfrontacao );
$obFormulario->addHidden          ( $obHdnAcaoConfrontacao   );
$obFormulario->addHidden          ( $obHdnTrecho             );
$obFormulario->addHidden          ( $obHdnTimestampLote      );
$obFormulario->addHidden          ( $obHdnTimestampParcelamento );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio    );
$obFormulario->addHidden          ( $obHdnCodigoUF   );
$obFormulario->addHidden          ( $obHdnLotesValidacao     );
if ( $_REQUEST["stOrigem"] != "validar"  or $rsRecordSetParcelamento->getNumLinhas() >= 1 ) {
    $obFormulario->addHidden      ( $obHdnNumeroLote         );
}
$obFormulario->addHidden          ( $obHdnCodigoLocalizacao  );
$obFormulario->addHidden          ( $obHdnOrigem             );

$obFormulario->addAba             ( "Lote"                );
$obFormulario->addTitulo          ( "Dados para lote"     );
$obFormulario->addComponente      ( $obTxtNumeroLote      );
$obFormulario->addComponente      ( $obLblNomeLocalizacao );
$obFormulario->agrupaComponentes  ( array( $obTxtAreaLote , $obCmbUnidadeMedida ) );
$obFormulario->addComponente      ( $obTxtProfundidadeMedia );
$obFormulario->addComponente      ( $obTxtDataInscricaoLote );
$obFormulario->addComponente      ( $obBscProcesso          );
//$obFormulario->addComponente      ( $obBscBairro            );
//$obFormulario->addComponente      ( $obLblBairro            );
$obFormulario->addComponente      ( $obBscBairro );
$obFormulario->addSpan            ( $obSpnEdificacoes       );
$obFormulario->addAba             ( "Confrontações"         );
$obFormulario->addTitulo          ( "Confrontações"         );
$obFormulario->addComponente      ( $obCmbPontoCardeal      );
$obFormulario->agrupaComponentes  ( array( $obRdoTipoTrecho, $obRdoTipoLote, $obRdoTipoOutros) );
$obFormulario->addComponente      ( $obTxtExtensao          );
$obFormulario->addspan            ( $obSpnConfrontacao      );
$obFormulario->defineBarraAba     ( array( $obBtnIncluirConfrontacao, $obBtnLimparConfrontacao ),"","" );
$obFormulario->addspan            ( $obSpnListaConfrontacao );

$obFormulario->addAba             ( "Características" );
$obMontaAtributosLote->geraFormulario ( $obFormulario );

if ($stAcao == "validar") {
    $obFormulario->Cancelar( $pgListValidar);
} else {
    //$obFormulario->Cancelar( $pgList);
    $obFormulario->Cancelar( $pgFilt);
}

if ($stAcao == "alterar") {
    $obFormulario->setFormFocus( $obTxtAreaLote->getId() );
} elseif ($stAcao == "validar") {
    if ( $rsRecordSetParcelamento->getNumLinhas() < 1 ) {
        $obFormulario->setFormFocus( $obTxtNumeroLote->getId() );
    } else {
        $obFormulario->setFormFocus( $obTxtAreaLote->getId() );
    }
}
$obFormulario->show();
if ($_REQUEST["stOrigem"] == "validar") {
    $stJs .= montaListaEdificacoes();
}

SistemaLegado::executaFrameOculto($stJs);
?>
