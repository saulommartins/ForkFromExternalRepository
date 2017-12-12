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
 * Pagina de Consulta de Lote
 * Data de Criação   : 10/06/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo Boezzio Paulino

 * @ignore

 * $Id: FMConsultaImovelLote.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.18
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgOculCons = "";

include_once $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$stFiltro = '';
$arTransf4 = Sessao::read('sessao_transf4');

if ($arTransf4) {
    $stFiltro = '';
    foreach ($arTransf4 as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

//Define o tipo de Lote ( Urbano / Rural )
if ($_REQUEST['stTipoLote'] == 'Urbano') {
    $obRCIMLote = new RCIMLoteUrbano;
} elseif ($_REQUEST['stTipoLote'] == 'Rural') {
    $obRCIMLote = new RCIMLoteRural;
}

//Busca dados da Configuracao
$obRCIMConfiguracao = new RCIMConfiguracao;
$obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMConfiguracao->consultarConfiguracao();
$obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );
$obRCIMConfiguracao->consultarMascaraLote    ( $stMascaraLote     );

if ($_REQUEST["inCodLote"] AND $_REQUEST["inCodLocalizacao"]) {
    $obRCIMLote->setCodigoLote( $_REQUEST["inCodLote"] );
    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $_REQUEST["inCodLocalizacao"] );
}
$obRCIMLote->consultarLote();

$rsListaProcesso = new Recordset;
$obRCIMLote->listarProcessos( $rsListaProcesso );
if ( $obRCIMLote->getTimestampLote() == $rsListaProcesso->getCampo( "timestamp" ) ) {
    $stProcesso = $rsListaProcesso->getCampo( "cod_processo_ano" );
}

$obRCIMConfrontacao = new RCIMConfrontacao( $obRCIMLote );

//MONTA A LISTA DE CONFRONTACOES
$obErro = $obRCIMConfrontacao->listarConfrontacoesPorLote( $rsListaConfrontacao );
$arConfrontacoesSessao = array();
Sessao::write('confrontacoes', $arConfrontacoesSessao);

if ( !$obErro->ocorreu() ) {
    $inLinha = 0;
    $inCodFace = NULL;
    while ( !$rsListaConfrontacao->eof() ) {
        $flExtensao = number_format( $rsListaConfrontacao->getCampo('valor'), 2, ",", "." );
        $boTestada = $rsListaConfrontacao->getCampo('principal') == "Sim" ? "S" : "N";

        $cod_face = $rsListaConfrontacao->getCampo('cod_face');
        if ( $rsListaConfrontacao->getCampo('principal') == "Sim" && !empty($cod_face) ) {
            $inCodFace = $rsListaConfrontacao->getCampo('cod_face');
        }

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
    Sessao::write('confrontacoes', $arConfrontacoesSessao);
    SistemaLegado::executaFramePrincipal( "buscaValor('montaListaConfrontacoes');" );
}

//DEFINICAO DOS ATRIBUTOS DE LOTE
$arChaveAtributoLote = array( "cod_lote" => $_REQUEST["inCodLote"] );
$obRCIMLote->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
$obRCIMLote->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos do Lote" );
$obMontaAtributos->setName       ( "Atributo_"         );
$obMontaAtributos->setLabel      ( true                );
$obMontaAtributos->setRecordSet  ( $rsAtributos        );

/****************************************/

//Define COMPONENTES DO FORMULARIO
/****************************************/

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( ""  );

$obHdnTipoLote = new Hidden;
$obHdnTipoLote->setName ( "stTipoLote" );
$obHdnTipoLote->setValue( $_REQUEST['stTipoLote'] );

$stLoteTipo = STR_PAD($obRCIMLote->getNumeroLote(),strlen($stMascaraLote),'0',STR_PAD_LEFT)." - ".$_REQUEST["stTipoLote"];

$obLblLote = new Label;
$obLblLote->setRotulo( "Número do Lote" );
$obLblLote->setValue ( $stLoteTipo );

$stNomeLocalizacao  = $obRCIMLote->obRCIMLocalizacao->getValorComposto();
$stNomeLocalizacao .= " - ".$obRCIMLote->obRCIMLocalizacao->getNomeLocalizacao();
$obLblLocalizacao = new Label;
$obLblLocalizacao->setRotulo( "Localização"      );
$obLblLocalizacao->setValue ( $stNomeLocalizacao );

$obLblFaceQuadra = new Label;
$obLblFaceQuadra->setRotulo( "Código da Face da Quadra" );
$obLblFaceQuadra->setValue ( $inCodFace );

$obLblArea = new Label;
$obLblArea->setRotulo( "Área" );
$obLblArea->setValue ( $obRCIMLote->getAreaLote() );

$obLblProfundidade = new Label;
$obLblProfundidade->setRotulo( "Profundiade Média" );
$obLblProfundidade->setValue ( $obRCIMLote->getProfundidadeMedia() );

$obLblDtInscricao = new Label;
$obLblDtInscricao->setRotulo( "Data de Inscrição" );
$obLblDtInscricao->setValue ( $obRCIMLote->getDataInscricao() );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo( "Processo" );
$obLblProcesso->setValue ( isset($stProcesso) ? $stProcesso : "");

$stSituacao = "Ativo";
if ( $obRCIMLote->getDataBaixa() && ($obRCIMLote->getDataTermino() == "") ) {
    $stSituacao = "Baixado";
}
$obLblSituacao = new Label;
$obLblSituacao->setRotulo( "Situação" );
$obLblSituacao->setValue ( $stSituacao );

$obLblDtBaixa = new Label;
$obLblDtBaixa->setRotulo ( "Data de Baixa" );
$obLblDtBaixa->setValue  ( $obRCIMLote->getDataBaixa() );

$obLblMotivo = new Label;
$obLblMotivo->setRotulo  ( "Motivo" );
$obLblMotivo->setValue   ( $obRCIMLote->getJustificativa() );

$obSpnConfrontacoes = new Span;
$obSpnConfrontacoes->setId ( "spnListaConfrontacoes" );

$obFormulario = new FormularioAbas;
$obFormulario->AddAba (" Lotes" );
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.18" );
$obFormulario->addTitulo( "Dados do Lote" );
$obFormulario->addHidden( $obHdnCtrl      );
$obFormulario->addHidden( $obHdnAcao      );
$obFormulario->addHidden( $obHdnTipoLote  );

$obFormulario->addComponente( $obLblLote         );
$obFormulario->addComponente( $obLblLocalizacao  );
$obFormulario->addComponente( $obLblFaceQuadra   );
$obFormulario->addComponente( $obLblArea         );
$obFormulario->addComponente( $obLblProfundidade );
$obFormulario->addComponente( $obLblDtInscricao  );
$obFormulario->addComponente( $obLblProcesso     );
$obFormulario->addComponente( $obLblSituacao     );
if ($stSituacao == 'Baixado') {
    $obFormulario->addComponente( $obLblDtBaixa  );
    $obFormulario->addComponente( $obLblMotivo   );
}
$obFormulario->addSpan( $obSpnConfrontacoes );

$obMontaAtributos->geraFormulario ( $obFormulario );

include_once 'FMConsultaImovelLoteListaProcessos.php';
$obFormulario->addSpan( $obSpnProcesso          );
$obFormulario->addSpan( $obSpnAtributosProcesso );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

//ABA PARCELAMENTOS

    //traz informações do parcelamento e lotes
    include_once( CAM_GT_CIM_MAPEAMENTO."TCIMParcelamentoSolo.class.php" );
    $obTCIMParcelamentoSolo = new TCIMParcelamentoSolo;

    $stFiltro = " WHERE cod_lote = ".$_REQUEST['inCodLote'];
    $obTCIMParcelamentoSolo->recuperaLotesParcelados($rsLotesParcelados, $stFiltro);

if ($rsLotesParcelados) {
    $obFormulario->AddAba("Parcelamentos");

    $obSpnListaParcelamento = new Span;
    $obSpnListaParcelamento->setId ('spnListaParcelamento');

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLotesParcelados );
    $obLista->setTitulo( "Lista de Parcelamentos");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Parcelamento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Tipo");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lote de Origem");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data de Parcelamento");
    $obLista->ultimoCabecalho->setWidth(10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lotes Participantes");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo ( "cod_parcelamento");
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo ( "nom_tipo");
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo ( "lote_origem");
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo ( "dt_parcelamento");
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo ( "lotes");
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->montaHTML();
    $obSpnListaParcelamento->setValue($obLista->getHTML());
    $obFormulario->addSpan($obSpnListaParcelamento);
}

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->show();

?>
