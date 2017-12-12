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
    * Página de Formulário para a aglutinação de lotes
    * Data de Criação   : 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FMManterLoteAglutinar.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.16  2006/09/18 10:30:54  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"     );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP

include_once 'OCManterLote.php';

$stLink = Sessao::read('stLink');

$stPrograma = "ManterLote";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$pgList = "LSManterLote.php?".Sessao::getId().$stLink."&funcionalidade=".$_REQUEST["funcionalidade"];

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
//MASCARA INSCRICAO MOBILIARIA
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();
//MASCARA LOTE
$stMascaraLote      = $obRCIMConfiguracao->getMascaraLote();

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
$obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );

$arPontoCardealSessao = Sessao::read('ponto_cardeal');
while ( !$rsListaPontosCardeais->eof() ) {
    $arPontoCardealSessao[$rsListaPontosCardeais->getCampo("cod_ponto")] = $rsListaPontosCardeais->getCampo("nom_ponto");
    $rsListaPontosCardeais->proximo();
}
Sessao::write('ponto_cardeal', $arPontoCardealSessao);
$rsListaPontosCardeais->setPrimeiroElemento();

$obRCIMLote->setCodigoGrandeza( $_REQUEST["inCodigoGrandeza"] );
$obRCIMLote->setCodigoUnidade ( $_REQUEST["inCodigoUnidade"]  );
$obRCIMLote->listarUnidadeMedida( $rsUnidadeMedida );

$obRCIMLote->setCodigoLote($_REQUEST["inCodigoLote"]);

/*O resultado dessa consulta preenche uma combo que nao esta sendo utilizada*/

//$obRCIMLote->BuscarLotes( $rsLoteConfrotacao );
$rsLoteConfrotacao = new RecordSet;

$obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
$obRCIMLote->consultarLote();
//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoLote = array( "cod_lote" => $_REQUEST["inCodigoLote"] );
$obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
$obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

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
    /*            $obRCIMTrecho = new RCIMTrecho;
                $obRCIMTrecho->setCodigoLogradouro ( $rsListaConfrontacao->getCampo('cod_logradouro') );
                $obRCIMTrecho->setSequencia        ( $rsListaConfrontacao->getCampo('cod_trecho') );
                $obRCIMTrecho->consultarTrecho( $rsTrecho );*/

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
    Sessao::write('confrontacoes', $arConfrontacoesSessao);
    $rsListaConfrontacao = new RecordSet;
    $rsListaConfrontacao->preenche( Sessao::read('confrontacoes'));
    $rsListaConfrontacao->addFormatacao("stDescricao","N_TO_BR");
    $stJs =  montaListaConfrontacao( $rsListaConfrontacao );
    SistemaLegado::executaFramePrincipal($stJs);
}

//MONTA OS ATRIBUTOS
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnAcaoConfrontacao = new Hidden;
$obHdnAcaoConfrontacao->setName( "stAcaoConfrontacao" );
$obHdnAcaoConfrontacao->setValue( $stAcaoConfrontacao );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName  ( "funcionalidade"            );
$obHdnFuncionalidade->setValue ( $_REQUEST["funcionalidade"] );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName( "stTrecho" );
$obHdnTrecho->setValue( $stTrecho );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName ( "inCodigoUF" );
$obHdnCodigoUF->setValue ( $obRCIMLote->obRCIMBairro->getCodigoUF() );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName( "inCodigoMunicipio" );
$obHdnCodigoMunicipio->setValue( $obRCIMLote->obRCIMBairro->getCodigoMunicipio() );

//Alateracao
$obHdnCodigoLote = new Hidden;
$obHdnCodigoLote->setName  ( "inCodigoLote" );
$obHdnCodigoLote->setValue ( $_REQUEST["inCodigoLote"] );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName( "inCodigoLocalizacao" );
$obHdnCodigoLocalizacao->setValue( $_REQUEST["inCodigoLocalizacao"] );

$obHdnNumeroLote = new Hidden;
$obHdnNumeroLote->setName  ( "stNumeroLote" );
$obHdnNumeroLote->setValue ( $obRCIMLote->getNumeroLote() );

$obHdnDtInscricao = new Hidden;
$obHdnDtInscricao->setName  ( "dtDataInscricaoLote" );
$obHdnDtInscricao->setValue ( $_REQUEST['dtDataInscricaoLote'] );

$obHdnCodigoGrandeza= new Hidden;
$obHdnCodigoGrandeza->setName  ( "inCodigoGrandeza" );
$obHdnCodigoGrandeza->setValue ( $_REQUEST['inCodigoGrandeza'] );

$obHdnCodigoUnidade = new Hidden;
$obHdnCodigoUnidade->setName  ( "inCodigoUnidade" );
$obHdnCodigoUnidade->setValue ( $_REQUEST['inCodigoUnidade'] );

//DADOS PARA ABA LOTE
$obTxtNumeroLote = new Label;
$obTxtNumeroLote->setName               ( "stNumeroLote"    );
$obTxtNumeroLote->setRotulo             ( "Número do Lote "  );
$obTxtNumeroLote->setValue              ( STR_PAD($obRCIMLote->getNumeroLote(),4,'0',STR_PAD_LEFT) );

$obLblNomeLocalizacao = new Label;
$obLblNomeLocalizacao->setName   ( "stNomeLocalizacao" );
$obLblNomeLocalizacao->setRotulo ( "Localização"       );
$stNomeLocalizacao  = $obRCIMLote->obRCIMLocalizacao->getValorComposto();
$stNomeLocalizacao .= " - ".$obRCIMLote->obRCIMLocalizacao->getNomeLocalizacao();
$obLblNomeLocalizacao->setValue  ( $stNomeLocalizacao );

$obHdnAreaLote = new Hidden;
$obHdnAreaLote->setName   ( "flAreaLote" );
$obHdnAreaLote->setValue  ( $obRCIMLote->getAreaLote()."  ".$rsUnidadeMedida->getCampo("nom_unidade")." ".$rsUnidadeMedida->getCampo("simbolo") );

$obLblAreaLote = new Label;
$obLblAreaLote->setName   ( "flAreaLote" );
$obLblAreaLote->setValue  ( $obRCIMLote->getAreaLote()."  ".$rsUnidadeMedida->getCampo("nom_unidade")." ".$rsUnidadeMedida->getCampo("simbolo") );
$obLblAreaLote->setRotulo ( "Área" );
$obLblAreaLote->setId     ( "flAreaLote" );

$obTxtProfundidadeMedia = new Numerico;
$obTxtProfundidadeMedia->setName      ( "flProfundidadeMedia" );
$obTxtProfundidadeMedia->setId        ( "flProfundidadeMedia" );
$obTxtProfundidadeMedia->setRotulo    ( "Profundidade Média"  );
$obTxtProfundidadeMedia->setNull      ( false                 );
$obTxtProfundidadeMedia->setSize      ( 18                    );
$obTxtProfundidadeMedia->setMaxLength ( 18                    );
$obTxtProfundidadeMedia->setFloat     ( true                  );
$obTxtProfundidadeMedia->setNegativo  ( false                 );
$obTxtProfundidadeMedia->setNaoZero   ( true                  );
$obTxtProfundidadeMedia->setMaxValue  ( 999999999999.99 );
$obTxtProfundidadeMedia->setTitle     ( "Informe a profundidade média do lote (em metros)" );
$obTxtProfundidadeMedia->setValue     ( $flProfundidadeMedia   );

$obTxtDataAglutinacaoLote = new Data;
$obTxtDataAglutinacaoLote->setName   ( "dtDataAglutinacaoLote" );
$obTxtDataAglutinacaoLote->setRotulo ( "Data de Aglutinação"   );
$obTxtDataAglutinacaoLote->setNull   ( false                   );
$obTxtDataAglutinacaoLote->setValue  ( $dtDataAglutinacao      );

if ($_REQUEST[ "inNumProcesso" ]) {
    $inNumProcesso = $_REQUEST[ "inNumProcesso" ]."/".$_REQUEST[ "stExercicio" ];
}

$obLblProcesso = new Label;
$obLblProcesso->setName  ( "inNumProcesso" );
$obLblProcesso->setValue ( $_REQUEST[ "inNumProcesso" ]."/".$_REQUEST[ "stExercicio" ]  );
$obLblProcesso->setRotulo( "Processo"      );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo do protocolo que formaliza" );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//BUSCA NOME DO BAIRRO
$obRCIMConfiguracao->listaDadosMunicipio( $arConfiguracao );
$inCodUF        = $arConfiguracao["cod_uf"];
$inCodMunicipio = $arConfiguracao["cod_municipio"];
$obRCIMLote->obRCIMBairro->setCodigoUF        ( $arConfiguracao["cod_uf"] );
$obRCIMLote->obRCIMBairro->setCodigoMunicipio ( $arConfiguracao["cod_municipio"] );
$obRCIMLote->obRCIMBairro->consultarBairro();

$obLblBairro = new Label;
$obLblBairro->setName  ( "inCodigoBairroLote" );
$obLblBairro->setValue ( $obRCIMLote->obRCIMBairro->getCodigoBairro()." - ".$obRCIMLote->obRCIMBairro->getNomeBairro() );
$obLblBairro->setId    ( "inCodigoBairroLote" );
$obLblBairro->setRotulo( "Bairro" );

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
$obBuscaTrecho->setId                             ( "inNumTrecho"     );
$obBuscaTrecho->setNull                           ( true              );
$obBuscaTrecho->setRotulo                         ( "*Trecho"         );
$obBuscaTrecho->obCampoCod->setName               ( "inNumTrecho"     );
$obBuscaTrecho->obCampoCod->setValue              ( $inNumTrecho      );
$obBuscaTrecho->obCampoCod->setInteiro            ( false             );
$obBuscaTrecho->obCampoCod->obEvento->setOnChange ( "buscarTrecho();" );
$obBuscaTrecho->setFuncaoBusca                    ("abrePopUp('".CAM_GT_CIM_POPUPS."trecho/FLProcurarTrecho.php','frm','inNumTrecho','inNumTrecho','','".Sessao::getId()."','800','550')");

$obBtnIncluirConfrontacao = new Button;
$obBtnIncluirConfrontacao->setName( "btnIncluirTrecho" );
$obBtnIncluirConfrontacao->setValue( "Incluir" );
$obBtnIncluirConfrontacao->obEvento->setOnClick( "incluirConfrontacao();" );

$obBtnLimparConfrontacao = new Limpar;
$obBtnLimparConfrontacao->obEvento->setOnClick( "limparConfrontacao();" );

$obSpnConfrontacao = new Span;
$obSpnConfrontacao->setId( "spnConfrontacao" );

$obSpnListaConfrontacao = new Span;
$obSpnListaConfrontacao->setId( "lsListaConfrontacoes" );

// ABA LOTES

$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

$obHdMascaraLote = new Hidden;
$obHdMascaraLote->setName ( "hdnMascaraLote"   );
$obHdMascaraLote->setValue( $stMascaraLote     );

$obSpnListaLotes = new Span;
$obSpnListaLotes->setId( "spanLotes" );

$obBtnIncluirLotes = new Button;
$obBtnIncluirLotes->setName( "btnIncluirLotes" );
$obBtnIncluirLotes->setValue( "Incluir" );
$obBtnIncluirLotes->obEvento->setOnClick( "buscaValor('incluiLote');" );

$obBtnLimparLotes= new Button;
$obBtnLimparLotes->setName ( "bntLimparLotes" );
$obBtnLimparLotes->setValue( "Limpar"         );
$obBtnLimparLotes->obEvento->setOnClick( "buscaValor('limparSpanLote');" );

if ( $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] ) {
    $arCodigoLocalizacao = explode( "-", $_REQUEST[ "inCodLocalizacao_".( $_REQUEST["inNumNiveis"] - 1 ) ] );
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $arCodigoLocalizacao[1] );
    $obRCIMLote->buscarLoteAglutinar( $rsLoteAglutinado );
} elseif ($_REQUEST["inCodigoLocalizacao"]) {
    $obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
    $obRCIMLote->buscarLoteAglutinar( $rsLoteAglutinado );
} else {
    $rsLoteAglutinado = new RecordSet;
}
$rsLoteAglutinado->addStrPad( "valor", strlen( $stMascaraLote ), "0" );

$obCmbLote = new Select;
$obCmbLote->setName       ( "inNumLote"              );
$obCmbLote->setTitle      ( "Lote"                   );
$obCmbLote->setRotulo     ( "*Lote"                  );
$obCmbLote->setStyle      ( "width: 150px"           );
$obCmbLote->addOption     ( "", "Selecione"          );
$obCmbLote->setCampoID    ( "[cod_lote]-[valor]"     );
$obCmbLote->setCampoDesc  ( "valor"                  );
$obCmbLote->preencheCombo ( $rsLoteAglutinado        );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm    );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnCtrl );
$obFormulario->addHidden          ( $obHdnFuncionalidade  );
$obFormulario->addHidden          ( $obHdnCodigoLote      );
$obFormulario->addHidden          ( $obHdMascaraLote      );
$obFormulario->addHidden          ( $obHdnAcao            );
$obFormulario->addHidden          ( $obHdnAcaoConfrontacao);
$obFormulario->addHidden          ( $obHdnTrecho          );
$obFormulario->addHidden          ( $obHdnCodigoUF        );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio );
$obFormulario->addHidden          ( $obHdnNumeroLote      );
$obFormulario->addHidden          ( $obHdnCodigoLocalizacao );
$obFormulario->addHidden          ( $obHdnAreaLote );
$obFormulario->addHidden          ( $obHdnDtInscricao );
$obFormulario->addHidden          ( $obHdnCodigoGrandeza );
$obFormulario->addHidden          ( $obHdnCodigoUnidade  );

$obFormulario->addAba             ( "Lote Original"       );
$obFormulario->addTitulo          ( "Dados para lote"     );
$obFormulario->addComponente      ( $obTxtNumeroLote      );
$obFormulario->addComponente      ( $obLblBairro          );
$obFormulario->addComponente      ( $obLblAreaLote          );
$obFormulario->addComponente      ( $obTxtProfundidadeMedia );
$obFormulario->addComponente      ( $obTxtDataAglutinacaoLote );
if ($_REQUEST[ "inNumProcesso" ]) {
    $obFormulario->addComponente  ( $obLblProcesso          );
} else {
    $obFormulario->addComponente  ( $obBscProcesso          );
}

$obFormulario->addAba             ( "Confrontações"         );
$obFormulario->addTitulo          ( "Confrontações"         );
$obFormulario->addComponente      ( $obCmbPontoCardeal      );
$obFormulario->agrupaComponentes  ( array( $obRdoTipoTrecho, $obRdoTipoLote, $obRdoTipoOutros) );
$obFormulario->addComponente      ( $obTxtExtensao          );
$obFormulario->addspan            ( $obSpnConfrontacao      );
$obFormulario->defineBarraAba     ( array( $obBtnIncluirConfrontacao, $obBtnLimparConfrontacao ),"","" );
$obFormulario->addspan            ( $obSpnListaConfrontacao );

$obFormulario->addAba             ( "Lotes"             );
$obFormulario->addTitulo          ( "Lotes"             );
$obFormulario->addComponente      ( $obCmbLote          );
$obFormulario->defineBarraAba     ( array( $obBtnIncluirLotes, $obBtnLimparLotes ), "", "" );
$obFormulario->addSpan            ( $obSpnListaLotes    );
//$obFormulario->addSpan            ( $obSpnListaAutonoma );

$obFormulario->addAba             ( "Características" );
$obMontaAtributos->geraFormulario ( $obFormulario     );

$obFormulario->Cancelar( $pgList );
$obFormulario->setFormFocus       ( $obTxtProfundidadeMedia->getId() );
$obFormulario->show();
?>
