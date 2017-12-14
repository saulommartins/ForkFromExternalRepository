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
    * Data de Criação: 08/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Id: FMManterRescindirContrato.php 66447 2016-08-30 14:21:17Z michel $

    * Casos de uso : uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoRescisaoContrato.class.php";
include_once TLIC."TLicitacaoPublicacaoRescisaoContrato.class.php";
include_once TLIC."TLicitacaoRescisaoContratoResponsavelJuridico.class.php";
include_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";

// padrão do programa
$stPrograma = "ManterRescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";


$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::remove('arValores');

$inNumContrato = $request->get('inNumContrato');
$inCodEntidade = $request->get('inCodEntidade');
$stExercicio = $request->get('stExercicio');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( 'stAcao' );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( 'stCtrl' );
$obHdnCtrl->setValue( 'inclusao' );

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( 'HdnCodVeiculo' );
$obHdnCodVeiculo->setId ( 'HdnCodVeiculo' );
$obHdnCodVeiculo->setValue ( '' );
//fim do padrão

if ($inNumContrato) {
    //recupera os veiculos de publicacao, coloca na sessao e manda para o oculto
    $obTLicitacaoPublicacaoRescisaoContrato = new TLicitacaoPublicacaoRescisaoContrato();
    $obTLicitacaoPublicacaoRescisaoContrato->setDado('num_contrato', $inNumContrato);
    $obTLicitacaoPublicacaoRescisaoContrato->setDado('exercicio_contrato', $stExercicio);
    $obTLicitacaoPublicacaoRescisaoContrato->setDado('cod_entidade', $inCodEntidade);
    $obTLicitacaoPublicacaoRescisaoContrato->recuperaVeiculosPublicacao( $rsVeiculosPublicacao );
    $inCount = 0;
    $arValores = array();
    while ( !$rsVeiculosPublicacao->eof() ) {
        $arValores[$inCount]['id'            ] = $inCount + 1;
        $arValores[$inCount]['inVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'num_veiculo' );
        $arValores[$inCount]['stVeiculo'     ] = $rsVeiculosPublicacao->getCampo( 'nom_veiculo');
        $arValores[$inCount]['dtDataPublicacao'] = $rsVeiculosPublicacao->getCampo( 'dt_publicacao');
        $arValores[$inCount]['inNumPublicacao'] = $rsVeiculosPublicacao->getCampo( 'num_publicacao');
        $arValores[$inCount]['stObservacao'  ] = $rsVeiculosPublicacao->getCampo( 'observacao');
        $inCount++;
        $rsVeiculosPublicacao->proximo();
    }
    
    Sessao::write('arValores', $arValores);
}

$obTLicitacaoRescisaoContrato = new TLicitacaoRescisaoContrato;
$obTLicitacaoRescisaoContrato->setDado('num_contrato', $_REQUEST["inNumContrato"]);
$obTLicitacaoRescisaoContrato->setDado('licitacao', true);
$obTLicitacaoRescisaoContrato->setDado('exercicio', $stExercicio);
$obTLicitacaoRescisaoContrato->recuperaContratoRescisao($rsRescisaoContrato);

if($rsRescisaoContrato->getCampo('dt_rescisao') != '') {
    $jsOnLoad = "montaParametrosGET('listaVeiculos', '');";
    $obHdnCtrl->setValue( 'alteracao' );
}

$rsRescisaoContrato->addFormatacao('vlr_cancelamento', 'NUMERIC_BR');
$rsRescisaoContrato->addFormatacao('vlr_multa', 'NUMERIC_BR');
$rsRescisaoContrato->addFormatacao('vlr_indenizacao', 'NUMERIC_BR');

$obLblExercicioContrato = new Label;
$obLblExercicioContrato->setRotulo ( "Exercício do Contrato");
$obLblExercicioContrato->setValue ( $rsRescisaoContrato->getCampo('exercicio') );

$obLblExercicioLicitacao = new Label;
$obLblExercicioLicitacao->setRotulo ( "Exercício da Licitação");
$obLblExercicioLicitacao->setValue ( $rsRescisaoContrato->getCampo('exercicio_licitacao') );

$stNumeroContato = $rsRescisaoContrato->getCampo('num_contrato');
$stNumeroContato .= "/".$rsRescisaoContrato->getCampo('exercicio');

$obLblNumeroContrato = new Label;
$obLblNumeroContrato->setRotulo('Número Contrato');
$obLblNumeroContrato->setValue($stNumeroContato);

$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($rsRescisaoContrato->getCampo('cod_entidade')." - ".$rsRescisaoContrato->getCampo('entidade'));

$obLblContratado = new Label;
$obLblContratado->setRotulo('Contratado');
$obLblContratado->setValue($rsRescisaoContrato->getCampo('cgm_contratado')." - ".$rsRescisaoContrato->getCampo('contratado'));

// Consulta para buscar responsável jurídico
$obTLicitacaoRescisContrRespJuridico = new TLicitacaoRescisaoContratoResponsavelJuridico();
$obTLicitacaoRescisContrRespJuridico->setDado('exercicio_contrato', $request->get('stExercicio'));
$obTLicitacaoRescisContrRespJuridico->setDado('num_contrato', $request->get('inNumContrato'));
$obTLicitacaoRescisContrRespJuridico->setDado('cod_entidade', $request->get('inCodEntidade'));
$obTLicitacaoRescisContrRespJuridico->recuperaDadosCGM($rsResponsavelJuridico);

//monta o popUp de pessoa juridica
$obResponsavelJuridico = new IPopUpCGMVinculado( $obForm );
$obResponsavelJuridico->setTabelaVinculo       ( 'sw_cgm_pessoa_fisica' );
$obResponsavelJuridico->setCampoVinculo        ( 'numcgm' );
$obResponsavelJuridico->setNomeVinculo         ( 'Responsavel' );
$obResponsavelJuridico->setRotulo              ( 'Responsável Jurídico da Rescisão' );
$obResponsavelJuridico->setName                ( 'stResponsavelJuridico');
$obResponsavelJuridico->setId                  ( 'stResponsavelJuridico');
$obResponsavelJuridico->setValue               ( $rsResponsavelJuridico->getCampo('nom_cgm') );
$obResponsavelJuridico->obCampoCod->setName    ( "inCodResponsavelJuridico" );
$obResponsavelJuridico->obCampoCod->setId      ( "inCodResponsavelJuridico" );
$obResponsavelJuridico->obCampoCod->setNull    ( true );
$obResponsavelJuridico->obCampoCod->setValue   ( $rsResponsavelJuridico->getCampo('numcgm') );
$obResponsavelJuridico->setNull                ( true );

//monta o campo Data Data de Rescisão'
$obTxtDataRescisao = new Data;
$obTxtDataRescisao->setRotulo('Data da Rescisão');
$obTxtDataRescisao->setTitle('Informe a data da rescisão.');
$obTxtDataRescisao->setName('dtRescisao');
$obTxtDataRescisao->setValue($rsRescisaoContrato->getCampo('dt_rescisao'));
$obTxtDataRescisao->setNull(false);

//monta o campo Moeda Multa
$obVlCancelamento = new Moeda;
$obVlCancelamento->setRotulo('Valor do Cancelamento');
$obVlCancelamento->setTitle('Informe o valor do cancelamento.');
$obVlCancelamento->setName('vlCancelamento');
$obVlCancelamento->setValue($rsRescisaoContrato->getCampo('vlr_cancelamento'));
$obVlCancelamento->setNull(false);

//monta o campo Moeda Multa
$obVlMulta = new Moeda;
$obVlMulta->setRotulo('Valor da Multa');
$obVlMulta->setTitle('Informe o valor da multa.');
$obVlMulta->setName('vlMulta');
$obVlMulta->setValue($rsRescisaoContrato->getCampo('vlr_multa'));
$obVlMulta->setNull(false);

//monta o campo Moeda Indenizaçãos
$obVlIndenizacao = new Moeda;
$obVlIndenizacao->setRotulo('Valor da Indenização');
$obVlIndenizacao->setTitle('Informe o valor da indenização.');
$obVlIndenizacao->setName('vlIndenizacao');
$obVlIndenizacao->setValue($rsRescisaoContrato->getCampo('vlr_indenizacao'));
$obVlIndenizacao->setNull(false);

//monta a textArea Motivo
$obVlMotivo = new TextArea;
$obVlMotivo->setRotulo('Motivo');
$obVlMotivo->setTitle('Informe o motivo.');
$obVlMotivo->setName('stMotivo');
$obVlMotivo->setValue($rsRescisaoContrato->getCampo('motivo'));
$obVlMotivo->setNull(false);

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle               ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull( true );
$obVeiculoPublicidade->obCampoCod->setNull( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataPublicacao" );
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( date('d/m/Y') );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setObrigatorioBarra( true );
$obDataPublicacao->setTitle( "Informe a data de publicação." );

$obNumeroPublicacao = new Inteiro();
$obNumeroPublicacao->setId   ( "inNumPublicacao" );
$obNumeroPublicacao->setName ( "inNumPublicacao" );
$obNumeroPublicacao->setValue( "");
$obNumeroPublicacao->setRotulo( "Número Publicação" );
$obNumeroPublicacao->setObrigatorioBarra( false );
$obNumeroPublicacao->setTitle( "Informe o Número da Publicação." );

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId     ( "stObservacao"                               );
$obTxtObservacao->setName   ( "stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setObrigatorioBarra( false                               );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMaxCaracteres( 80 );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirListaVeiculos', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "montaParametrosGET('limparVeiculo', 'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao');" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

// objetos hidden das labels
$obHdnInNumContrato = new Hidden;
$obHdnInNumContrato->setName( "inNumContrato" );
$obHdnInNumContrato->setValue( $rsRescisaoContrato->getCampo('num_contrato') );

$obHdnStExercicioContrato = new Hidden;
$obHdnStExercicioContrato->setName( "stExercicioContrato" );
$obHdnStExercicioContrato->setValue( $rsRescisaoContrato->getCampo('exercicio') );

$obHdnInCodEntidade = new Hidden;
$obHdnInCodEntidade->setName( "inCodEntidade" );
$obHdnInCodEntidade->setValue( $rsRescisaoContrato->getCampo('cod_entidade') );

$obHdnInCgmContratado = new Hidden;
$obHdnInCgmContratado->setName( "inCgmContratado" );
$obHdnInCgmContratado->setValue( $rsRescisaoContrato->getCampo('cgm_contratado') );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnInCgmContratado );
$obFormulario->addHidden( $obHdnInNumContrato );
$obFormulario->addHidden( $obHdnStExercicioContrato );
$obFormulario->addHidden( $obHdnInCodEntidade );
$obFormulario->addHidden        ( $obHdnCodVeiculo );
$obFormulario->addTitulo        ( "Dados do Contrato"   );
$obFormulario->addComponente( $obLblExercicioContrato );
$obFormulario->addComponente( $obLblExercicioLicitacao );
$obFormulario->addComponente( $obLblNumeroContrato );
$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblContratado );
$obFormulario->addComponente( $obResponsavelJuridico );
$obFormulario->addComponente( $obTxtDataRescisao );
$obFormulario->addComponente( $obVlCancelamento );
$obFormulario->addComponente( $obVlMulta );
$obFormulario->addComponente( $obVlIndenizacao );
$obFormulario->addComponente( $obVlMotivo );
$obFormulario->addTitulo        ( 'Veículo de Publicação' );
$obFormulario->addComponente    ( $obVeiculoPublicidade );
$obFormulario->addComponente    ( $obDataPublicacao );
$obFormulario->addComponente    ( $obNumeroPublicacao );
$obFormulario->addComponente    ( $obTxtObservacao );
$obFormulario->defineBarra      ( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );
$obFormulario->addSpan          ( $obSpnListaVeiculo );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';