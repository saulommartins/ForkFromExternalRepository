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
    * Página de Formulário do Acidos Cedidos
    * Data de Criação: 27/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 08:54:06 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                      );
include_once( CAM_GRH_PES_COMPONENTES."IBuscaInnerLocal.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdidoCedido";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

switch ($stAcao) {
    case "incluir":
        $jsOnload   = "montaParametrosGET('preencherSpanCedencia','stTipoCedencia');";
    break;
    case "alterar":
    case "consultar":
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
        include_once(CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedidoLocal.class.php");
        $obTPessoalAdidoCedido = new TPessoalAdidoCedido();
        if ($stAcao == "alterar") {
            $stFiltro = " AND contrato.cod_contrato = ".$_GET['inCodContrato'];
        } else {
            $stFiltro = " AND contrato.registro = ".$_POST['inContrato'];
        }
        $obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro);

        if ($rsAdidoCedido->getNumLinhas() != '-1') {
            $obTNorma = new TNorma();
            $stFiltro = " where cod_norma = ".$rsAdidoCedido->getCampo("cod_norma");
            $obTNorma->recuperaNormas($rsNorma,$stFiltro);

            $obTCGM = new TCGM;
            $stFiltro = " WHERE numcgm = ".$rsAdidoCedido->getCampo("cgm_cedente_cessionario");
            $obTCGM->recuperaTodos($rsCGM,$stFiltro);

            $obTPessoalAdidoCedidoLocal = new TPessoalAdidoCedidoLocal();
            $stFiltro  = " WHERE cod_contrato = ".$rsAdidoCedido->getCampo("cod_contrato");
            $stFiltro .= "   AND cod_norma = ".$rsAdidoCedido->getCampo("cod_norma");
            $stFiltro .= "   AND timestamp = '".$rsAdidoCedido->getCampo("timestamp")."'";
            $obTPessoalAdidoCedidoLocal->recuperaTodos($rsAdidoCedidoLocal,$stFiltro);

            $inCodTipoNormaTxt = $rsNorma->getCampo("cod_tipo_norma");
            $dtDataInicialAto  = $rsAdidoCedido->getCampo('data_inicial');
            $dtDataFinalAto    = $rsAdidoCedido->getCampo('data_final');
            $stTipoCedencia    = ( $rsAdidoCedido->getCampo("tipo_cedencia") == 'a' ) ? "Adido" : "Cedido";
            $stRotuloCGM       = ( $rsAdidoCedido->getCampo("tipo_cedencia") == 'a' ) ? "CGM Órgão/Entidade Cedente" : "CGM Órgão/Entidade Cessionário";
            $stValueCGM        = $rsAdidoCedido->getCampo("cgm_cedente_cessionario")."-".$rsCGM->getCampo("nom_cgm");
            $inCGMOrgao        = $rsAdidoCedido->getCampo("cgm_cedente_cessionario");
            $stIndicativoOnus  = ( $rsAdidoCedido->getCampo("indicativo_onus") == 'c' ) ? "Cedente" : "Cessionário";
            $inCodConvenioTxt  = $rsAdidoCedido->getCampo('num_convenio');
            if ($stAcao == "alterar") {
                $jsOnload   = "executaFuncaoAjax('preencherFormAlterar','&inCodLocal=".$rsAdidoCedidoLocal->getCampo("cod_local")."&inExercicio=".$rsNorma->getCampo("exercicio")."&inNumNormaTxt=".$rsNorma->getCampo('num_norma')."&inCodTipoNormaTxt=".$rsNorma->getCampo("cod_tipo_norma")."&inCodNorma=".$_GET['inCodNorma']."&inCodContrato=".$_GET['inCodContrato']."');";
            } else {
                $jsOnload   = "executaFuncaoAjax('preencherFormConsultar','&inCodLocal=".$rsAdidoCedidoLocal->getCampo("cod_local")."&inExercicio=".$rsNorma->getCampo("exercicio")."&inNumNormaTxt=".$rsNorma->getCampo('num_norma')."&inCodTipoNormaTxt=".$rsNorma->getCampo("cod_tipo_norma")."&inCodNorma=".$rsAdidoCedido->getCampo("cod_norma")."&inCodContrato=".$rsAdidoCedido->getCampo("cod_contrato")."');";
            }
        } else {
            sistemaLegado::alertaAviso($pgList."?inContrato=".$_POST['inContrato'],"Matrícula (".$_POST['inContrato'].") não encontrada em movimentação." ,"","aviso", Sessao::getId(), "../");
        }

        break;
}

$stLocation = $pgList.'?'.Sessao::getId()."&stAcao=".$stAcao."&stTipoFiltro=".$_REQUEST['stTipoFiltro'];

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName                             ( "stAcao"                 );
$obHdnAcao->setValue                            ( $stAcao                  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                 );
$obHdnCtrl->setValue                            ( $stCtrl                  );

$obHdnContrato = new Hidden;
$obHdnContrato->setName                         ( "inCodContrato"          );
$obHdnContrato->setValue                        ( $_GET['inCodContrato']   );

$obHdnRegistro = new Hidden;
$obHdnRegistro->setName                         ( "inRegistro"             );
$obHdnRegistro->setValue                        ( $_GET['inRegistro']      );

$obHdnCGMOrgaoEntidade = new Hidden;
$obHdnCGMOrgaoEntidade->setName                 ( "inCGMOrgaoEntidade"     );
$obHdnCGMOrgaoEntidade->setValue                ( $inCGMOrgao              );

//DEFINICAO DO FORM
$obForm = new Form;
if ($stAcao == "consultar") {
    $obForm->setAction                              ( $pgFilt                  );
    $obForm->setTarget                              ( "telaPrincipal"          );
} else {
    $obForm->setAction                              ( $pgProc                  );
    $obForm->setTarget                              ( "oculto"                 );
}

Sessao::write('obForm', $obForm);

$obIFiltroContrato = new IFiltroContrato(false,false);
$obIFiltroContrato->setTituloFormulario("");
$obIFiltroContrato->obIContratoDigitoVerificador->setNull(false);
$stOnChange = $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnChange();
$stOnBlur   = $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();
$obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange($stOnChange."montaParametrosGET('validaMatricula','inContrato');");
$obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur($stOnBlur."montaParametrosGET('validaMatricula','inContrato');");
if ($stAcao == "alterar" or $stAcao == "consultar") {
    $obIFiltroContrato->obIContratoDigitoVerificador->setPagFiltro(false);
    $obIFiltroContrato->obIContratoDigitoVerificador->setAutomatico(true);
}
if ($stAcao != 'consultar') {
    $obIFiltroContrato->obIContratoDigitoVerificador->setTipo('contrato_ativos');
}

$obRConfiguracaoPessoal = new RConfiguracaoPessoal();
include_once(CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php");
$obRTipoNorma = new RTipoNorma();
$obRTipoNorma->listarTodos( $rsTipoNorma     ) ;

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo              ( "Tipo de Norma"               );
$obTxtTipoNorma->setTitle               ( "Informe o tipo de norma para seleção da norma." );
$obTxtTipoNorma->setName                ( "inCodTipoNormaTxt"                             );
$obTxtTipoNorma->setValue               ( $inCodTipoNormaTxt                              );
$obTxtTipoNorma->setSize                ( 6                                               );
$obTxtTipoNorma->setMaxLength           ( 3                                               );
$obTxtTipoNorma->setInteiro             ( true                                            );
$obTxtTipoNorma->setNull                ( false                                           );
$obTxtTipoNorma->setSize                ( 10    );
$obTxtTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('preencherNorma','inCodTipoNormaTxt');"         );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo              ( "Tipo de Norma");
$obCmbTipoNorma->setName                ( "inCodTipoNorma"            );
$obCmbTipoNorma->setValue               ( $inCodTipoNormaTxt          );
$obCmbTipoNorma->setStyle               ( "width: 200px"              );
$obCmbTipoNorma->setCampoID             ( "cod_tipo_norma"            );
$obCmbTipoNorma->setCampoDesc           ( "nom_tipo_norma"            );
$obCmbTipoNorma->addOption              ( "", "Selecione"             );
$obCmbTipoNorma->setNull                ( false                       );
$obCmbTipoNorma->preencheCombo          ( $rsTipoNorma                );
$obCmbTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('preencherNorma','inCodTipoNormaTxt');"         );

$obLblTipoNorma = new Label();
$obLblTipoNorma->setRotulo("Tipo de Norma");
$obLblTipoNorma->setId("inCodTipoNorma");
$obLblTipoNorma->setValue($stTipoNorma);

$obLblNroNorma = new Label();
$obLblNroNorma->setRotulo("Nr. Norma");
$obLblNroNorma->setId("stNrNorma");
$obLblNroNorma->setValue($stNroNorma);

$obTxtCodNroNorma = new TextBox;
$obTxtCodNroNorma->setRotulo             ( "Nr. Norma"     );
$obTxtCodNroNorma->setTitle              ( "Informe o número do ato de cedência, cadastrado no Normas (Decreto, Lei, Portaria que autoriza)." );
$obTxtCodNroNorma->setName               ( "stNrNormaTxt" );
$obTxtCodNroNorma->setValue              ( $stNrNormaTxt  );
$obTxtCodNroNorma->setSize               ( 10    );
$obTxtCodNroNorma->setMaxLength          ( 20    );
$obTxtCodNroNorma->setNull               ( false );
$obTxtCodNroNorma->obEvento->setOnChange( "montaParametrosGET('preencherPublicacao','inCodTipoNormaTxt,stNrNormaTxt');" );

$obCmbCodNroNorma = new Select;
$obCmbCodNroNorma->setName                  ( "stNrNorma" );
$obCmbCodNroNorma->setValue                 ( $stNrNormaTxt  );
$obCmbCodNroNorma->setRotulo                ( "Nr. Norma"      );
$obCmbCodNroNorma->setTitle                 ( "Informe o número do ato de cedência, cadastrado no Normas (Decreto, Lei, Portaria que autoriza)." );
$obCmbCodNroNorma->setNull                  ( false );
$obCmbCodNroNorma->addOption                ( "", "Selecione" );
$obCmbCodNroNorma->setStyle                 ( "width: 250px"  );
$obCmbCodNroNorma->obEvento->setOnChange("montaParametrosGET('preencherPublicacao','inCodTipoNormaTxt,stNrNormaTxt');");

$obLblDataPublicacao = new Label();
$obLblDataPublicacao->setRotulo("Data da Publicação");
$obLblDataPublicacao->setValue($dtPublicacao);
$obLblDataPublicacao->setId("dtPublicacao");

$obTxtDataInicialAto = new Data;
$obTxtDataInicialAto->setName   ( "dtDataInicialAto" );
$obTxtDataInicialAto->setTitle  ("Informe a data inicial do ato de cedência.");
$obTxtDataInicialAto->setNull   ( false );
$obTxtDataInicialAto->setRotulo ( "Data Inicial do Ato" );
$obTxtDataInicialAto->setValue  ( $dtDataInicialAto  );
$obTxtDataInicialAto->obEvento->setOnChange ( "montaParametrosGET('comparaDatas','dtDataFinalAto,dtDataInicialAto');");

$obLblDataInicialAto = new Label();
$obLblDataInicialAto->setRotulo("Data Inicial do Ato");
$obLblDataInicialAto->setValue($dtDataInicialAto);

$obTxtDataFinalAto = new Data;
$obTxtDataFinalAto->setName   ( "dtDataFinalAto" );
$obTxtDataFinalAto->setTitle  ("Informe a data final do ato de cedência.");
$obTxtDataFinalAto->setRotulo ( "Data Final do Ato" );
$obTxtDataFinalAto->setValue  ( $dtDataFinalAto  );
$obTxtDataFinalAto->obEvento->setOnChange ( "montaParametrosGET('comparaDatas','dtDataFinalAto,dtDataInicialAto');");

$obLblDataFinalAto = new Label();
$obLblDataFinalAto->setRotulo("Data Final do Ato");
$obLblDataFinalAto->setValue($dtDataFinalAto);

$obRdoTipoAdido = new Radio;
$obRdoTipoAdido->setName("stTipoCedencia");
$obRdoTipoAdido->setRotulo("Tipo de Cedência");
$obRdoTipoAdido->setTitle("Selecione o tipo de cedência.");
$obRdoTipoAdido->setNull(false);
$obRdoTipoAdido->setValue("adido");
$obRdoTipoAdido->setChecked(true);
$obRdoTipoAdido->setLabel("Adido");
$obRdoTipoAdido->obEvento->setOnChange("montaParametrosGET('preencherSpanCedencia','stTipoCedencia');");

$obRdoTipoCedido = new Radio;
$obRdoTipoCedido->setName("stTipoCedencia");
$obRdoTipoCedido->setRotulo("Tipo de Cedência");
$obRdoTipoCedido->setTitle("Selecione o tipo de cedência.");
$obRdoTipoCedido->setNull(false);
$obRdoTipoCedido->setValue("cedido");
$obRdoTipoCedido->setLabel("Cedido");
$obRdoTipoCedido->obEvento->setOnChange("montaParametrosGET('preencherSpanCedencia','stTipoCedencia');");

$obLblTipoCedencia = new Label();
$obLblTipoCedencia->setRotulo("Tipo de Cedência");
$obLblTipoCedencia->setValue($stTipoCedencia);

$obHdnTipoCedencia =  new Hidden;
$obHdnTipoCedencia->setName("stTipoCedencia");
$obHdnTipoCedencia->setValue(strtolower($stTipoCedencia));

$obLblCgmOrgaoEntidade = new Label();
$obLblCgmOrgaoEntidade->setRotulo($stRotuloCGM);
$obLblCgmOrgaoEntidade->setValue($stValueCGM);

$obRdoIndicativoAdido = new Radio;
$obRdoIndicativoAdido->setName("stIndicativoOnus");
$obRdoIndicativoAdido->setRotulo("Indicativo de Ônus");
$obRdoIndicativoAdido->setTitle("Selecione o indicativo de ônus da cedência: Despesas por conta do cedente ou do cessionário.");
$obRdoIndicativoAdido->setNull(false);
$obRdoIndicativoAdido->setValue("cedente");
$obRdoIndicativoAdido->setChecked(true);
$obRdoIndicativoAdido->setLabel("Cedente");

$obRdoIndicativoCedido = new Radio;
$obRdoIndicativoCedido->setName("stIndicativoOnus");
$obRdoIndicativoCedido->setRotulo("Indicativo de Ônus");
$obRdoIndicativoCedido->setTitle("Selecione o indicativo de ônus da cedência: Despesas por conta do cedente ou do cessionário.");
$obRdoIndicativoCedido->setNull(false);
$obRdoIndicativoCedido->setValue("cessionario");
$obRdoIndicativoCedido->setLabel("Cessionário");

$obLblIndicativoOnus = new Label();
$obLblIndicativoOnus->setRotulo("Indicativo de Ônus");
$obLblIndicativoOnus->setValue($stIndicativoOnus);

$obHdnIndicativoOnus =  new Hidden;
$obHdnIndicativoOnus->setName("stIndicativoOnus");
$obHdnIndicativoOnus->setValue(strtolower($stIndicativoOnus));

$obSpnCedencia = new Span();
$obSpnCedencia->setId("spnCedencia");

$obTxtNroConvenio = new TextBox;
$obTxtNroConvenio->setRotulo             ( "Número do Convênio"     );
$obTxtNroConvenio->setTitle              ( "Informe o número do convênio." );
$obTxtNroConvenio->setName               ( "inCodConvenioTxt" );
$obTxtNroConvenio->setValue              ( $inCodConvenioTxt  );
$obTxtNroConvenio->setSize               ( 15    );
$obTxtNroConvenio->setMaxLength          ( 15    );
$obTxtNroConvenio->setNull               ( true );

$obLblNroConvenio = new Label();
$obLblNroConvenio->setRotulo("Número do Convênio");
$obLblNroConvenio->setValue($inCodConvenioTxt);

$obIBuscaInnerLocal = new IBuscaInnerLocal;

$obLblLocal = new Label();
$obLblLocal->setRotulo("Local");
$obLblLocal->setId("stLocal");
$obLblLocal->setValue($stLocal);

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter', '', true);");

$obBtnVoltar = new Voltar;

if ($stAcao == "incluir") {
    $obBtnLimpar = new Limpar();
    $obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparForm');");
    $arBotaoAcao = array( $obBtnOk, $obBtnLimpar );
} else if ($stAcao == "consultar") {
    $arBotaoAcao = array( $obBtnVoltar );
} else {
    $obBtnLimpar = new Ok();
    $obBtnLimpar->setValue("Limpar");
    $obBtnLimpar->obEvento->setOnClick("montaParametrosGET('limparFormAlterar');");
    $arBotaoAcao = array( $obBtnOk, $obBtnLimpar );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
switch ($stAcao) {
    case "incluir":
        $obFormulario->addTitulo                        ( "Dados de Movimentação"                                               );
        $obIFiltroContrato->geraFormulario              ( $obFormulario                                                         );
        $obFormulario->addComponenteComposto            ( $obTxtTipoNorma,$obCmbTipoNorma                                       );
        $obFormulario->addComponenteComposto            ( $obTxtCodNroNorma,$obCmbCodNroNorma                                   );
        $obFormulario->addComponente                    ( $obLblDataPublicacao                                                  );
        $obFormulario->addComponente                    ( $obTxtDataInicialAto                                                  );
        $obFormulario->addComponente                    ( $obTxtDataFinalAto                                                    );
        $obFormulario->agrupaComponentes                ( array($obRdoTipoAdido,$obRdoTipoCedido)                               );
        $obFormulario->addSpan                          ( $obSpnCedencia                                                        );
        $obFormulario->agrupaComponentes                ( array($obRdoIndicativoAdido,$obRdoIndicativoCedido)                   );
        $obFormulario->addComponente                    ( $obTxtNroConvenio                                                     );
        $obIBuscaInnerLocal->geraFormulario             ( $obFormulario                                                         );
        $obFormulario->defineBarra                      ( $arBotaoAcao                                                          );
    break;
    case "alterar":
        $obFormulario->addTitulo                        ( "Dados para Alteração de Movimentação"                                );
        $obIFiltroContrato->geraFormulario              ( $obFormulario                                                         );
        $obFormulario->addHidden($obHdnContrato);
        $obFormulario->addHidden($obHdnRegistro);
        $obFormulario->addHidden($obHdnCGMOrgaoEntidade);
        $obFormulario->addComponenteComposto            ( $obTxtTipoNorma,$obCmbTipoNorma                                       );
        $obFormulario->addComponenteComposto            ( $obTxtCodNroNorma,$obCmbCodNroNorma                                   );
        $obFormulario->addComponente                    ( $obLblDataPublicacao                                                  );
        $obFormulario->addComponente                    ( $obTxtDataInicialAto                                                  );
        $obFormulario->addComponente                    ( $obTxtDataFinalAto                                                    );
        $obFormulario->addComponente                    ( $obLblTipoCedencia                                                    );
        $obFormulario->addHidden                        ( $obHdnTipoCedencia                                                    );
        $obFormulario->addComponente                    ( $obLblCgmOrgaoEntidade                                                );
        $obFormulario->addComponente                    ( $obLblIndicativoOnus                                                  );
        $obFormulario->addHidden                        ( $obHdnIndicativoOnus                                                  );
        $obFormulario->addComponente                    ( $obTxtNroConvenio                                                     );
        $obIBuscaInnerLocal->geraFormulario             ( $obFormulario                                                         );
        $obFormulario->defineBarra                      ( $arBotaoAcao                                                          );
    break;
    case "consultar":
        $obFormulario->addTitulo                        ( "Dados para Alteração de Movimentação"                                );
        $obIFiltroContrato->geraFormulario              ( $obFormulario                                                         );
        $obFormulario->addComponente                    ( $obLblTipoNorma                                                       );
        $obFormulario->addComponente                    ( $obLblNroNorma                                                        );
        $obFormulario->addComponente                    ( $obLblDataPublicacao                                                  );
        $obFormulario->addComponente                    ( $obLblDataInicialAto                                                  );
        $obFormulario->addComponente                    ( $obLblDataFinalAto                                                    );
        $obFormulario->addComponente                    ( $obLblTipoCedencia                                                    );
        $obFormulario->addComponente                    ( $obLblCgmOrgaoEntidade                                                );
        $obFormulario->addComponente                    ( $obLblIndicativoOnus                                                  );
        $obFormulario->addComponente                    ( $obLblNroConvenio                                                     );
        $obFormulario->addComponente                    ( $obLblLocal                                                           );
        $obFormulario->defineBarra                      ( $arBotaoAcao                                                          );
    break;
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
