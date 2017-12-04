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
//Define o nome dos arquivos PHP
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/protocolo/classes/componentes/IPopUpProcesso.class.php';
include_once CAM_GT_CEM_COMPONENTES."IPopUpEmpresa.class.php";
include_once CAM_GT_CIM_COMPONENTES."IPopUpImovel.class.php";
include_once CAM_GT_FIS_COMPONENTES."ITextBoxSelectNaturezaFiscalizacao.class.php";
include_once CAM_GT_FIS_COMPONENTES."IPopUpFiscal.class.php";
include_once CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php";
include_once CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php";
include_once CAM_GT_FIS_NEGOCIO."RFISProcessoFiscal.class.php";
include_once CAM_GT_FIS_VISAO."VFISProcessoFiscal.class.php";

$obController = new RFISProcessoFiscal;
$obVisao = new VFISProcessoFiscal($obController);

$stPrograma = "ManterProcesso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJss      = "JS".$stPrograma.".php";

include_once( $pgJss );

Sessao::write("arCredito", null);

$stAcao = $_GET['stAcao'];
$tipoFiscalizacao = $_REQUEST["inTipoFiscalizacao"];

switch ($stAcao) {
    case "alterar":
        $codProcesso = $_REQUEST['inCodProcesso'];
        $alterar = true;
        $obDadosProcesso = $obVisao->BuscaDadosProcesso($codProcesso);
        $obDadosProcesso = $obDadosProcesso->arElementos[0];

        $iniciado = $obVisao->processoIniciado($codProcesso);

        if($iniciado)
            sistemaLegado::executaFrameOculto("desabilitaForm('frm')");

        $obHdnCodigoProcesso =  new Hidden;
        $obHdnCodigoProcesso->setName ("inCodProcesso");
        $obHdnCodigoProcesso->setValue($codProcesso);
        break;
    case "excluir":
        break;
    default:
        $stAcao = "incluir";
        break;
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $_GET['stCtrl']  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obTipoFisDados = $obVisao->getTipoFiscalizacao($tipoFiscalizacao);

$obTipoFiscalizacao = new Label;
$obTipoFiscalizacao->setRotulo( "Tipo de Fiscalização" );
$obTipoFiscalizacao->setName( "stTipoFiscalizacao" );
$obTipoFiscalizacao->setValue( $obTipoFisDados->arElementos[0]["cod_tipo"]." - ".$obTipoFisDados->arElementos[0]["descricao"]  );

$obInscricaoEconomica = new IPopUpEmpresa;
$obInscricaoEconomica->obInnerEmpresa->setNull(false);

$obInscricaoImobiliaria = new IPopUpImovel;
$obInscricaoImobiliaria->obInnerImovel->setNull(false);

$obFiscal = new IPopUpFiscal;
$obFiscal->setRotulo("*Fiscal");
$obFiscal->setTitle("Digite o Código ou busque um Fiscal.");

$obNaturezaFiscalizacao = new ITextBoxSelectNaturezaFiscalizacao;
$obNaturezaFiscalizacao->setNull(false);

if ($alterar) {
    $obNaturezaFiscalizacao->obTxtNaturezaFiscalizacao->setValue($obDadosProcesso['cod_natureza']);
    $obNaturezaFiscalizacao->obCmbNaturezaFiscalizacao->setValue($obDadosProcesso['cod_natureza']);
}

$obProcesso = new IPopUpProcesso($obForm);
$obProcesso->setValidar(true);
$obProcesso->setNull(true);

if ($alterar) {
    if ($obDadosProcesso['cod_processo_protocolo'] && $obDadosProcesso['ano_exercicio'])
        $obProcesso->obCampoCod->setValue($obDadosProcesso['cod_processo_protocolo']."/".$obDadosProcesso['ano_exercicio']);
}

$obDtPeriodo = new Periodo;
$obDtPeriodo->setNull(false);
$obDtPeriodo->setRotulo('Período');

if ($alterar) {
    $obDtPeriodo->getDataInicial()->stValue = $obDadosProcesso['periodo_inicio'];
    $obDtPeriodo->getDataFinal()->stValue = $obDadosProcesso['periodo_termino'];
}

$obDtPrevisaoInicio = new Data;
$obDtPrevisaoInicio->setNull(false);
$obDtPrevisaoInicio->setRotulo('Previsão de Início');
$obDtPrevisaoInicio->setName('bt_dtprevInicio');
$obDtPrevisaoInicio->setTitle( "Informe a Previsão de Início." );

$obDtPrevisaoEncerramento = new Data;
$obDtPrevisaoEncerramento->setNull(false);
$obDtPrevisaoEncerramento->setRotulo('Previsão de Encerramento');
$obDtPrevisaoEncerramento->setName('bt_dtprevEncerramento');
$obDtPrevisaoEncerramento->setTitle( "Informe a Previsão de Encerramento." );

if ($alterar) {
    $obDtPrevisaoInicio->setValue($obDadosProcesso['previsao_inicio']);
    $obDtPrevisaoEncerramento->setValue($obDadosProcesso['previsao_termino']);
}

$obLblVinculoCredito   = new Label;
$obLblVinculoCredito->setRotulo('Vínculo');
$obLblVinculoCredito->setValue('Grupo de Crédito');

$obObservacoes = new TextArea;
$obObservacoes->setRotulo("Observações");
$obObservacoes->setTitle( "Informe as Observações." );
$obObservacoes->setName("bt_obs");

if ($alterar)
    $obObservacoes->setValue($obDadosProcesso['observacao']);

$table = new Table();
$table->setRecordset( $obController->getDocumentos("and cod_tipo_fiscalizacao = ".$obTipoFisDados->arElementos[0]["cod_tipo"]));

$table->setSummary('Lista de Documentos de Uso Interno');
//$table->setConditional( true , "#ddd" );

$table->Head->addCabecalho( 'código'            , 5,'');
$table->Head->addCabecalho( 'Descrição',         70,'');

$table->Body->addCampo( 'cod_documento' , 'E','' );
$table->Body->addCampo( 'nom_documento', 'E','' );

$table->montaHTML();

$obListaDocumentos = $table->getHtml();

$obListaDocumentos = str_replace( "\n", "", $obListaDocumentos );
$obListaDocumentos = str_replace( "  ", "", $obListaDocumentos );
$obListaDocumentos = str_replace( "'", "\\'", $obListaDocumentos);

$obSpanListaDocumentos = new Span;
$obSpanListaDocumentos->setId('spnListaDocumentos');
$obSpanListaDocumentos->setValue($obListaDocumentos);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm      );

$obFormulario->addHidden     ( $obHdnAcao   );
$obFormulario->addHidden     ( $obHdnCtrl   );

if($stAcao == 'alterar')
    $obFormulario->addHidden     ( $obHdnCodigoProcesso );

$obFormulario->addTitulo     ( "Dados para Processo Fiscal" );
$obFormulario->addComponente($obTipoFiscalizacao);

    switch ($_REQUEST["inTipoFiscalizacao"]) {
        case 1:
            $obInscricaoEconomica->geraFormulario($obFormulario);

            if ($alterar) {
                $obInscricao = $obVisao->BuscaInscricaoEconomicaProcesso($codProcesso);
                $obInscricaoEconomica->obInnerEmpresa->obCampoCod->setValue($obInscricao->arElementos[0]['inscricao_economica']);
                $obInscricaoEconomica->obInnerEmpresa->setValue($obInscricao->arElementos[0]['nom_cgm']);
            }
        break;
        case 2:
            if ($alterar) {
                $obInscricao = $obVisao->BuscaInscricaoImobiliariaProcesso($codProcesso);

                $stEnderecoImovel = $obInscricao->getCampo("logradouro");

                if ( $obInscricao->getCampo("numero") ) {
                    $stEnderecoImovel .= ", ".$obInscricao->getCampo("numero");
                }
                if ( $obInscricao->getCampo("complemento") ) {
                    $stEnderecoImovel .= " - ".$obInscricao->getCampo("complemento");
                }

                $obInscricaoImobiliaria->obInnerImovel->obCampoCod->setValue($obInscricao->arElementos[0]['inscricao_municipal']);
                $obInscricaoImobiliaria->obInnerImovel->setValue($stEnderecoImovel);
            }

            $obInscricaoImobiliaria->geraFormulario($obFormulario);
            $boImovel = true;

        break;
    }

$obFormulario->addComponente ( $obDtPeriodo );
$obFormulario->addComponente ( $obDtPrevisaoInicio );
$obFormulario->addComponente ( $obDtPrevisaoEncerramento );
$obNaturezaFiscalizacao->geraFormulario($obFormulario);
$obFormulario->addComponente ( $obProcesso );
$obFormulario->addComponente ( $obObservacoes );
$obFormulario->addSpan( $obSpanListaDocumentos );

if ($_REQUEST["inTipoFiscalizacao"] == 1) {
    $obSpanVinculo = new Span;
    $obSpanVinculo->setId('spnVinculo');

    $obSpanCreditoGrupo = new Span;
    $obSpanCreditoGrupo->setId('spnListaCreditoGrupo');

    if ($stAcao == 'alterar') {
        $inCodProcesso = $_REQUEST['inCodProcesso'];
        $obListaGrupoCredito = $obVisao->BuscaGrupoCreditoProcesso($inCodProcesso);
        $obSpanCreditoGrupo->setValue($obListaGrupoCredito);
    }

    $obFormulario->addTitulo( "Dados para Créditos" );
    $obFormulario->addComponente($obLblVinculoCredito);
    $obFormulario->addSpan($obSpanVinculo);
    $obFormulario->addSpan($obSpanCreditoGrupo);
}

$obSpnListaCredito = new Span;
$obSpnListaCredito->setID( "spnListaCredito" );
$obFormulario->addSpan($obSpnListaCredito);

$obBtnIncluirFiscal = new Button;
$obBtnIncluirFiscal->setName('btnIncluir');
$obBtnIncluirFiscal->setValue('Incluir');
$obBtnIncluirFiscal->setTipo('button');
$obBtnIncluirFiscal->obEvento->setOnClick('IncluirFiscal();');
$obBtnIncluirFiscal->setDisabled(false);

$obBtnLimparFiscal = new Button;
$obBtnLimparFiscal->setName('btnLimpar');
$obBtnLimparFiscal->setValue('Limpar');
$obBtnLimparFiscal->setTipo('button');
$obBtnLimparFiscal->obEvento->setOnClick('limparFiscal();');
$obBtnLimparFiscal->setDisabled(false);

$botoesSpanFiscal = array( $obBtnIncluirFiscal, $obBtnLimparFiscal );

$dados = $obVisao->VerificaFiscal($_SESSION["numCgm"]);

if ($dados) {
    $arFiscal[0]['codigo'] = $dados[0]['cod_fiscal'];
    $arFiscal[0]['nome'] = $dados[0]['nom_cgm'];

    $obHdn =  new Hidden;
    $obHdn->setName ( "fiscal[]");
    $obHdn->setValue( $arFiscal[0]['codigo'] );
    $obHdn->montaHtml();

    $arFiscal[0]['hidden'] = $obHdn->getHtml();

    Sessao::write("arFiscal", $arFiscal);

    if ($alterar) {
        $obListaFiscal = $obVisao->BuscaFiscalProcesso($codProcesso);
    } else {
        $obListaFiscal = $obVisao->InicializaFiscal($dados[0]["cod_fiscal"], $dados[0]["nom_cgm"], "fiscal");
    }

    if ($dados[0]["administrador"] == 't') {
        $obFormulario->addTitulo     ( "Dados para Fiscais" );
        $obFiscal->geraFormulario($obFormulario);
        $obFormulario->defineBarra( $botoesSpanFiscal,'left','' );
    }
}

$obHdnTipoFiscalizacao =  new Hidden;
$obHdnTipoFiscalizacao->setName ('inTipoFiscalizacao');
$obHdnTipoFiscalizacao->setValue($_REQUEST["inTipoFiscalizacao"]);

$obFormulario->addHidden( $obHdnTipoFiscalizacao );

$obSpnListaFiscal = new Span;
$obSpnListaFiscal->setID( "spnListaFiscal" );
$obSpnListaFiscal->setValue($obListaFiscal);
$obFormulario->addSpan($obSpnListaFiscal);

$obBtnOK = new Ok();

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTipo("button");
$obBtnLimpar->setStyle("width: 60px;");

if ($stAcao == "alterar") {
    $obBtnLimpar->obEvento->setOnClick("limparFormAlterar();");
}

$arBotoes = array($obBtnOK, $obBtnLimpar);
$obFormulario->defineBarra($arBotoes, 'left');
$obFormulario->show();

if ($_REQUEST['inTipoFiscalizacao'] == '1') {
    $jsOnLoad = "montaParametrosGET('MostraGrupoCredito');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
