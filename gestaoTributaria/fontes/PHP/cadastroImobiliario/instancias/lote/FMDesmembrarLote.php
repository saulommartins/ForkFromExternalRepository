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
    * Página de Formulário para o desmembramento de lote
    * Data de Criação   : 01/04/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * $Id: FMDesmembrarLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.08
*/

/*
$Log$
Revision 1.12  2006/09/18 10:30:54  fabio
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
$stPrograma = "ManterLote";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );
include_once( $pgOcul );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "desmembrar";
}
$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

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

//MASCARA PROCESSO
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

$obRCIMLote->listarUnidadeMedida( $rsUnidadeMedida );

//FALTA DE RETORNO DA MIGRACAO
$obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodigoLocalizacao"] );
$obRCIMLote->listarLotes( $rsLoteConfrotacao );

$obRCIMLote->setCodigoLote( $_REQUEST["inCodigoLote"] );
$obRCIMLote->consultarLote();

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );
$obRCIMConfrontacao->listarPontosCardeais( $rsListaPontosCardeais );

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
                $obRCIMTrecho = new RCIMTrecho;
                $obRCIMTrecho->setCodigoLogradouro ( $rsListaConfrontacao->getCampo('cod_logradouro') );
                $obRCIMTrecho->setSequencia        ( $rsListaConfrontacao->getCampo('cod_trecho') );
                $obRCIMTrecho->consultarTrecho( $rsTrecho );
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
                     "inCodigoTrechoConfrontacao"     => $rsListaConfrontacao->getCampo('cod_trecho'),
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
    $stJs =  montaListaConfrontacao( $rsListaConfrontacao );
    SistemaLegado::executaFramePrincipal($stJs);
}

//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoLote =  array( "cod_lote"      => $_REQUEST["inCodigoLote"] );
$obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
$obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );

$obMontaAtributos2 = new MontaAtributos;
$obMontaAtributos2->setTitulo     ( "Atributos"  );
$obMontaAtributos2->setName       ( "Atributo_"  );
$obMontaAtributos2->setRecordSet  ( $rsAtributos );

$obRCIMBairro = new RCIMBairro;
$obRCIMBairro->setCodigoBairro      ( $_REQUEST["inCodigoBairro"]       );
$obRCIMBairro->setCodigoUF          ( $_REQUEST["inCodigoUF"]           );
$obRCIMBairro->setCodigoMunicipio   ( $_REQUEST["inCodigoMunicipio"]    );
$obRCIMBairro->consultarBairro();

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName               ( "stCtrl"                    );

$obHdnAcao = new Hidden;
$obHdnAcao->setName               ( "stAcao"                    );
$obHdnAcao->setValue              ( $stAcao                     );

$obHdnFuncionalidade = new Hidden;
$obHdnFuncionalidade->setName     ( "funcionalidade"            );
$obHdnFuncionalidade->setValue    ( $_REQUEST["funcionalidade"] );

$obHdnTrecho = new Hidden;
$obHdnTrecho->setName             ( "stTrecho"                  );
$obHdnTrecho->setValue            ( $stTrecho                   );

$obHdnCodigoLote = new Hidden;
$obHdnCodigoLote->setName         ( "inCodigoLote"              );
$obHdnCodigoLote->setValue        ( $_REQUEST["inCodigoLote"]   );

$obHdnDataInscricao = new Hidden;
$obHdnDataInscricao->setName      ( "dtDataInscricaoLote"    );
$obHdnDataInscricao->setValue     ( $_REQUEST["dtDataInscricaoLote"]     );

$obHdnCodigoBairro = new Hidden;
$obHdnCodigoBairro->setName       ( "inCodigoBairroLote"      );
$obHdnCodigoBairro->setValue      ( $_REQUEST["inCodigoBairro"]           );

$obHdnCodigoUF = new Hidden;
$obHdnCodigoUF->setName           ( "inCodigoUF"              );
$obHdnCodigoUF->setValue          ( $_REQUEST["inCodigoUF"]   );

$obHdnCodigoMunicipio = new Hidden;
$obHdnCodigoMunicipio->setName    ( "inCodigoMunicipio"       );
$obHdnCodigoMunicipio->setValue   ( $_REQUEST["inCodigoMunicipio"]        );

$obHdMascaraLote = new Hidden;
$obHdMascaraLote->setName         ( "hdnMascaraLote"          );
$obHdMascaraLote->setValue        ( $stMascaraLote            );

$obHdnCodigoLocalizacao = new Hidden;
$obHdnCodigoLocalizacao->setName  ( "inCodigoLocalizacao"     );
$obHdnCodigoLocalizacao->setValue ( $_REQUEST["inCodigoLocalizacao"]      );

$flAreaRealOrigem = number_format( $_REQUEST["flAreaRealOrigem"], 2, ',', '.' );
$obHdnAreaLote = new Hidden;
$obHdnAreaLote->setName           ( "flAreaRealOrigem"        );
$obHdnAreaLote->setValue          ( $flAreaRealOrigem         );

$obHdnAreaResultante = new Hidden;
$obHdnAreaResultante->setName     ( "flAreaLote"              );
$obHdnAreaResultante->setValue    ( $_REQUEST["flAreaLote"]   );

$obHdnNumeroLote = new Hidden;
$obHdnNumeroLote->setName         ( "stNumeroLote"            );
$obHdnNumeroLote->setValue        ( ltrim($_REQUEST["stDescQuestao"],"0") );

$obHdnCodigoUnidade = new Hidden;
$obHdnCodigoUnidade->setName      ( "inCodigoUnidadeOrigem"   );
$obHdnCodigoUnidade->setValue     ( $_REQUEST["inCodigoUnidadeOrigem"]    );

if ($_REQUEST["funcionalidade"] == 178) {
    $stChaveUnidadeMedida = "1-2";
    $stUnidadeMedida      = "m²";
} elseif ($_REQUEST["funcionalidade"] == 193) {
    $stChaveUnidadeMedida = "3-2";
    $stUnidadeMedida      = "ha";
}
$obHdnUnidadeMedida = new Hidden;
$obHdnUnidadeMedida->setName  ( "stChaveUnidadeMedida" );
$obHdnUnidadeMedida->setValue ( $stChaveUnidadeMedida  );

//DADOS PARA ABA PADRÃO DE LOTES
//DADOS PARA LOTE
$obLblNumeroLote = new Label;
$obLblNumeroLote->setRotulo     ( "Lote de Origem"                             );
$obLblNumeroLote->setValue      ( STR_PAD($_REQUEST["stValor"], strlen($stMascaraLote), '0', STR_PAD_LEFT) );

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName       ( "inCodigoTipo"                            );

$obLblTipoLote = new Label;
$obLblTipoLote->setRotulo       ( "Tipo de Lote"                            );
if ($_REQUEST["funcionalidade"] == 178) {
    $obLblTipoLote->setValue    ( "Urbano"                                  );
    $obHdnCodigoTipo->setValue  ( 1                                         );
} else {
    $obLblTipoLote->setValue    ( "Rural"                                   );
    $obHdnCodigoTipo->setValue  ( 2                                         );
}

$obLblBairroLote = new Label;
$obLblBairroLote->setRotulo     ( "Bairro do Lote de Origem"                            );
$obLblBairroLote->setValue      ( $_REQUEST["inCodigoBairro"] . "-" .$obRCIMBairro->getNomeBairro() );

$obTxtQuantLote = new TextBox;
$obTxtQuantLote->setName        ( "inQuantLote"                             );
$obTxtQuantLote->setId          ( "inQuantLote"                             );
$obTxtQuantLote->setMaxLength   ( 3                                         );
$obTxtQuantLote->setSize        ( 3                                         );
$obTxtQuantLote->setInteiro     ( true                                      );
$obTxtQuantLote->setNull        ( false                                     );
$obTxtQuantLote->setRotulo      ( "Quantidade de Lotes"                     );
$obTxtQuantLote->setvALUE       ( $_REQUEST["inQuantLote"]                  );
$obTxtQuantLote->obEvento->setOnChange( "buscaValor('calculaAreaTotal');"   );

$obLblAreaOriginal = new Label;
$obLblAreaOriginal->setRotulo     ( "Área Original"                         );
$obLblAreaOriginal->setValue      ( $flAreaRealOrigem." ".$stUnidadeMedida  );

$obLblAreaResultante = new Label;
$obLblAreaResultante->setRotulo   ( "Área Resultante"                       );
$obLblAreaResultante->setValue    ( $_REQUEST["flAreaResultante"]           );
$obLblAreaResultante->setId       ( "flAreaResultante"                      );
$obLblAreaResultante->setName     ( "flAreaResultante"                      );

$obTxtProfundidadeMedia = new Numerico;
$obTxtProfundidadeMedia->setName      ( "flProfundidadeMedia"               );
$obTxtProfundidadeMedia->setRotulo    ( "Profundidade Média"                );
$obTxtProfundidadeMedia->setNull      ( false                               );
$obTxtProfundidadeMedia->setNegativo  ( false                               );
$obTxtProfundidadeMedia->setNaoZero   ( true                                );
$obTxtProfundidadeMedia->setSize      ( 18                                  );
$obTxtProfundidadeMedia->setMaxLength ( 18                                  );
$obTxtProfundidadeMedia->setFloat     ( true                                );
$obTxtProfundidadeMedia->setTitle     ( "Informe a profundidade média do lote (em metros)" );
$obTxtProfundidadeMedia->setMaxValue  ( 999999999999.99                     );

$obTxtDataDesmembramento = new Data;
$obTxtDataDesmembramento->setName                ( "dtDataDesmembramento"         );
$obTxtDataDesmembramento->setId                  ( "dtDataDesmembramento"         );
$obTxtDataDesmembramento->setRotulo              ( "Data do Desmembramento"       );
$obTxtDataDesmembramento->setNull                ( false                          );
$obTxtDataDesmembramento->obEvento->setOnChange  ( "javascript: buscaValor( 'validaDataDesmembramento' );"  );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Número do processo no protocolo que gerou a aprovação do loteamento" );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setValue( $_REQUEST["inProcesso"] );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraProcesso."', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

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
$obTxtExtensao->setNegativo  ( false        );
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
$obTxtDescricaoOutros->setMaxCaracteres( 500            );

$obBtnIncluirConfrontacao = new Button;
$obBtnIncluirConfrontacao->setName( "btnIncluirTrecho" );
$obBtnIncluirConfrontacao->setValue( "Incluir" );
$obBtnIncluirConfrontacao->obEvento->setOnClick( "incluirConfrontacao();" );

$obBtnLimparConfrontacao = new Button;
$obBtnLimparConfrontacao->setName( "btnLimparConfrontacao" );
$obBtnLimparConfrontacao->setValue( "Limpar" );
$obBtnLimparConfrontacao->obEvento->setOnClick( "limparConfrontacao();" );

$obSpnConfrontacao = new Span;
$obSpnConfrontacao->setId( "spnConfrontacao" );

$obSpnListaConfrontacao = new Span;
$obSpnListaConfrontacao->setId( "lsListaConfrontacoes" );

$obBtnOK = new OK;

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFormulario()" );

//DADOS PARA ABA DEFINIR PADRÃO
//DADOS PARA LOTE

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
$obTxtExtensao->setNegativo  ( false        );
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
$obTxtDescricaoOutros->setMaxCaracteres( 500            );

$obBtnIncluirConfrontacao = new Button;
$obBtnIncluirConfrontacao->setName( "btnIncluirTrecho" );
$obBtnIncluirConfrontacao->setValue( "Incluir" );
$obBtnIncluirConfrontacao->obEvento->setOnClick( "incluirConfrontacao();" );

$obBtnLimparConfrontacao = new Button;
$obBtnLimparConfrontacao->setName( "btnLimparConfrontacao" );
$obBtnLimparConfrontacao->setValue( "Limpar" );
$obBtnLimparConfrontacao->obEvento->setOnClick( "limparConfrontacao();" );

$obSpnConfrontacao = new Span;
$obSpnConfrontacao->setId( "spnConfrontacao" );

$obSpnListaConfrontacao = new Span;
$obSpnListaConfrontacao->setId( "lsListaConfrontacoes" );

$obHdnAcaoConfrontacao = new Hidden;
$obHdnAcaoConfrontacao->setName( "stAcaoConfrontacao" );
$obHdnAcaoConfrontacao->setValue( $_REQUEST["stAcaoConfrontacao"] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obFormulario = new FormularioAbas;
$obFormulario->addForm            ( $obForm                                             );
$obFormulario->setAjuda ( "UC-05.01.08" );
$obFormulario->addHidden          ( $obHdnAcaoConfrontacao                              );
$obFormulario->addHidden          ( $obHdnCtrl                                          );
$obFormulario->addHidden          ( $obHdnFuncionalidade                                );
$obFormulario->addHidden          ( $obHdnAcao                                          );
$obFormulario->addHidden          ( $obHdnTrecho                                        );
$obFormulario->addHidden          ( $obHdMascaraLote                                    );
$obFormulario->addHidden          ( $obHdnCodigoLote                                    );
$obFormulario->addHidden          ( $obHdnCodigoBairro                                  );
$obFormulario->addHidden          ( $obHdnCodigoUF                                      );
$obFormulario->addHidden          ( $obHdnCodigoMunicipio                               );
$obFormulario->addHidden          ( $obHdnDataInscricao                                 );
$obFormulario->addHidden          ( $obHdnAreaLote                                      );
$obFormulario->addHidden          ( $obHdnAreaResultante                                );
$obFormulario->addHidden          ( $obHdnCodigoTipo                                    );
$obFormulario->addHidden          ( $obHdnCodigoLocalizacao                             );
$obFormulario->addHidden          ( $obHdnNumeroLote                                    );
$obFormulario->addHidden          ( $obHdnCodigoUnidade                                 );
$obFormulario->addHidden          ( $obHdnUnidadeMedida                                 );
$obFormulario->addAba             ( "Padrão de lotes"                                   );
$obFormulario->addTitulo          ( "Dados padrão para lotes"                           );
$obFormulario->addComponente      ( $obLblNumeroLote                                    );
$obFormulario->addComponente      ( $obLblTipoLote                                      );
$obFormulario->addComponente      ( $obLblBairroLote                                    );
$obFormulario->addComponente      ( $obTxtQuantLote                                     );
$obFormulario->addComponente      ( $obLblAreaOriginal                                  );
$obFormulario->addComponente      ( $obLblAreaResultante                                );
$obFormulario->addComponente      ( $obTxtProfundidadeMedia                             );
$obFormulario->addComponente      ( $obTxtDataDesmembramento                            );
$obFormulario->addComponente      ( $obBscProcesso                                      );

$obFormulario->addAba             ( "Confrontações"                                     );
$obFormulario->addTitulo          ( "Confrontações"                                     );
$obFormulario->addComponente      ( $obCmbPontoCardeal                                  );
$obFormulario->agrupaComponentes  ( array( $obRdoTipoTrecho, $obRdoTipoLote, $obRdoTipoOutros) );
$obFormulario->addComponente      ( $obTxtExtensao                                      );
$obFormulario->addspan            ( $obSpnConfrontacao                                  );
$obFormulario->defineBarraAba     ( array( $obBtnIncluirConfrontacao, $obBtnLimparConfrontacao ),"","" );
$obFormulario->addspan            ( $obSpnListaConfrontacao                             );

$obFormulario->addAba             ( "Características"                                   );
$obFormulario->addTitulo          ( "Características do lote"                           );
$obMontaAtributos->geraFormulario ( $obFormulario                                       );
$obFormulario->defineBarra        ( array( $obBtnOK , $onBtnLimpar )                    );
$obFormulario->setFormFocus       ( $obTxtQuantLote->getId()                            );
$obFormulario->show();

?>
