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
    * Página de Formulario de Inclusao/Alteracao programa

    * Data de Criação   : 19/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_PPA_NEGOCIO.'RPPAManterPrograma.class.php';
include_once CAM_GF_PPA_VISAO.'VPPAManterPrograma.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectTipoPrograma.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectOrgao.class.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_COMPONENTES.'IPopUpAcao.class.php';

$obNegocio = new RPPAManterPrograma();
$obVisao = new VPPAManterPrograma( $obNegocio );

$boProgramaOrcamento = false;
$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "incluir";
}

if ($stAcao == 'alterar' && $_REQUEST['inCodPPA'] == '') {
    $stAcao = 'incluir';
    $boProgramaOrcamento = true;
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterPrograma";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".php";

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

include_once $pgJs;

# Definição do form
$obForm = new Form;

$obForm->setAction($pgProc);
$obForm->settarget('oculto');

//Definição do Formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obHdnAnoInicioPPA = new Hidden;
$obHdnAnoInicioPPA->setName ('inAnoInicioPPA');
$obHdnAnoInicioPPA->setValue($_REQUEST['inAnoInicioPPA']);

$obHdnAnoFinalPPA = new Hidden;
$obHdnAnoFinalPPA->setName ('inAnoFinalPPA');
$obHdnAnoFinalPPA->setValue($_REQUEST['inAnoFinalPPA']);

$obHdnModificarNatureza = new Hidden;
$obHdnModificarNatureza->setName('boModificarNatureza');
$obHdnModificarNatureza->setId  ('boModificarNatureza');
$obHdnModificarNatureza->setValue('true');

$rsPrograma = new RecordSet;
if (isset($_REQUEST['inCodPPA'])) {
    $rsPrograma = $obVisao->buscaProgramaLista($_REQUEST);
} else {
    $rsPrograma->preenche(array());
}

switch ($stAcao) {
    case 'alterar':
        $obHdnCodPPA =  new Hidden;
        $obHdnCodPPA->setName ('inCodPPA');
        $obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

        $obHdnNumPrograma =  new Hidden;
        $obHdnNumPrograma->setName ('inNumPrograma');
        $obHdnNumPrograma->setValue($rsPrograma->getCampo('num_programa'));

        $obHdnCodProgramaSetorial =  new Hidden;
        $obHdnCodProgramaSetorial->setName ('inCodProgramaSetorial');
        $obHdnCodProgramaSetorial->setValue($_REQUEST['inCodSetorial']);

        $obHdnCodMacro =  new Hidden;
        $obHdnCodMacro->setName ('inCodMacroObjetivo');
        $obHdnCodMacro->setValue($_REQUEST['inCodMacro']);

        $obHdnCodPrograma =  new Hidden;
        $obHdnCodPrograma->setName   ('inCodPrograma');
        $obHdnCodPrograma->setValue  ($_REQUEST['inCodPrograma']);

        $obHdnDtInicio =  new Hidden;
        $obHdnDtInicio->setName   ('inDtInicio');
        $obHdnDtInicio->setValue  ($_REQUEST['inDtInicio']);

        $obHdnDtFinal =  new Hidden;
        $obHdnDtFinal->setName   ('inDtTermino');
        $obHdnDtFinal->setValue  ($_REQUEST['inDtTermino']);

        $obLabelPrograma = new label;
        $obLabelPrograma->setRotulo('Programa');
        $obLabelPrograma->setTitle ('Código do programa');
        $obLabelPrograma->setValue ( $_REQUEST['inNumPrograma']);

        $obLabelMacro = new label;
        $obLabelMacro->setRotulo('Macro Objetivo');
        $obLabelMacro->setTitle ('Macro Objetivo');
        $obLabelMacro->setValue ($_REQUEST['inCodMacro']." - ".$_REQUEST['stNomMacro']);

        $obLabelProgramaSetorial = new label;
        $obLabelProgramaSetorial->setRotulo('Programa Setorial');
        $obLabelProgramaSetorial->setTitle ('Programa Setorial');
        $obLabelProgramaSetorial->setValue ($_REQUEST['inCodSetorial']." - ".$_REQUEST['stNomSetorial']);

        $obHdnUnidade = new Hidden;
        $obHdnUnidade->setName  ('hdnCodUnidade');
        $obHdnUnidade->setValue ($rsPrograma->getCampo('num_unidade'));

        $obHdnOrgao = new Hidden;
        $obHdnOrgao->setName  ('hdnCodOrgao');
        $obHdnOrgao->setValue ($rsPrograma->getCampo('num_orgao'));

        $obHdnDataInicialTemporario = new Hidden;
        $obHdnDataInicialTemporario->setName ('hdnDataInicialTemporario');
        $obHdnDataInicialTemporario->setValue('');

        $obHdnDataFinalTemporario = new Hidden;
        $obHdnDataFinalTemporario->setName ('hdnDataFinalTemporario');
        $obHdnDataFinalTemporario->setValue('');

        $obHdnDataMinimaAcao = new Hidden;
        $obHdnDataMinimaAcao->setName ('hdnDataMinimaAcao');
        $obHdnDataMinimaAcao->setValue('');

        $obHdnDataMaximaAcao = new Hidden;
        $obHdnDataMaximaAcao->setName ('hdnDataMaximaAcao');
        $obHdnDataMaximaAcao->setValue('');

        include_once CAM_GF_PPA_MAPEAMENTO."TPPAAcao.class.php";

        # Recupera ppa.acao
        $obTPPAAcao = new TPPAAcao();
        $stFiltro  = '     programa.cod_programa = ' . $_REQUEST['inCodPrograma'];
        $stFiltro .= ' AND acao_dados.cod_tipo   = 1 ';
        $obTPPAAcao->recuperaDados($rsAcoes, $stFiltro, '', $boTransacao);

        if ($rsAcoes->getNumLinhas() > -1) {
            $stDtInicialAcao = '';
            $stDtFinalAcao = '';
            while (!$rsAcoes->EOF()) {
                if ($rsAcoes->getCampo('continuo') == 'f') {
                    $obHdnModificarNatureza->setValue('false');
                    $obHdnDataInicialTemporario->setValue($rsAcoes->getCampo('dt_inicial'));
                    $obHdnDataFinalTemporario->setValue($rsAcoes->getCampo('dt_final'));

                    if (SistemaLegado::comparaDatas($stDtInicialAcao, $rsAcoes->getCampo('dt_inicial_acao')) || $stDtInicialAcao == '') {
                        $stDtInicialAcao = $rsAcoes->getCampo('dt_inicial_acao');
                    }

                    if (SistemaLegado::comparaDatas($rsAcoes->getCampo('dt_final_acao'), $stDtFinalAcao) || $stDtFinalAcao == '') {
                        $stDtFinalAcao = $rsAcoes->getCampo('dt_final_acao');
                    }
                }
                $rsAcoes->proximo();
            }
            $obHdnDataMinimaAcao->setValue($stDtInicialAcao);
            $obHdnDataMaximaAcao->setValue($stDtFinalAcao);
        }
    break;
}

$obHdnProgramaOrcamento = new Hidden;
$obHdnProgramaOrcamento->setName('hdnProgramaOrcamento');
$obHdnProgramaOrcamento->setId('hdnProgramaOrcamento');
if ($boProgramaOrcamento == true) {
    $obHdnCodPrograma =  new Hidden;
    $obHdnCodPrograma->setName   ('inCodPrograma');
    $obHdnCodPrograma->setValue  ($_REQUEST['inCodPrograma']);

    $obLabelPrograma = new label;
    $obLabelPrograma->setRotulo('Programa');
    $obLabelPrograma->setTitle ('Código do programa');
    $obLabelPrograma->setValue ( $_REQUEST['inCodPrograma']);

    $obHdnProgramaOrcamento->setValue('true');
} else {
    $obHdnProgramaOrcamento->setValue('false');
}

//Recupera os ppas para o select
$obTPPA = new TPPA;
$obTPPA->recuperaTodos($rsPPA, ' ORDER BY ano_inicio');

# Define componente seletor de PPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("buscaMacroObjetivos();");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange ("buscaMacroObjetivos();");
$obITextBoxSelectPPA->obTextBox->setValue($_REQUEST['inCodPPA']);
$obITextBoxSelectPPA->obSelect->setValue ($_REQUEST['inCodPPA']);
$obITextBoxSelectPPA->setNull( false );
if ($rsPPA->getNumLinhas() == 1 && $_REQUEST['inCodPPA'] == '') {
    $obITextBoxSelectPPA->obTextBox->setValue($rsPPA->getCampo('cod_ppa'));
    $obITextBoxSelectPPA->obSelect->setValue ($rsPPA->getCampo('cod_ppa'));
}

//Instancia um textboxSelect para o macro objetivo
$obTextBoxSelectMacroObjetivo = new TextBoxSelect;
$obTextBoxSelectMacroObjetivo->setRotulo              ('Macro Objetivo');
$obTextBoxSelectMacroObjetivo->setTitle               ('Informe o Macro Objetivo.');
$obTextBoxSelectMacroObjetivo->setName                ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjetivo->obTextBox->setName     ('inCodMacroObjetivoTxt');
$obTextBoxSelectMacroObjetivo->obTextBox->setId       ('inCodMacroObjetivoTxt');
$obTextBoxSelectMacroObjetivo->obSelect->setName      ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjetivo->obSelect->setId        ('inCodMacroObjetivo');
$obTextBoxSelectMacroObjetivo->obSelect->addOption    ('','Selecione');
$obTextBoxSelectMacroObjetivo->obSelect->setDependente(true);
$obTextBoxSelectMacroObjetivo->setNull                (false);
$obTextBoxSelectMacroObjetivo->obTextBox->obEvento->setOnChange("buscaProgramasSetoriais();");
$obTextBoxSelectMacroObjetivo->obSelect->obEvento->setOnChange ("buscaProgramasSetoriais();");

//Instancia um textboxSelect para o programa setorial
$obTextBoxSelectProgramaSetorial = new TextBoxSelect;
$obTextBoxSelectProgramaSetorial->setRotulo              ('Programa Setorial');
$obTextBoxSelectProgramaSetorial->setTitle               ('Informe o Programa Setorial.');
$obTextBoxSelectProgramaSetorial->setName                ('inCodProgramaSetorial');
$obTextBoxSelectProgramaSetorial->obTextBox->setName     ('inCodProgramaSetorialTxt');
$obTextBoxSelectProgramaSetorial->obTextBox->setId       ('inCodProgramaSetorialTxt');
$obTextBoxSelectProgramaSetorial->obSelect->setName      ('inCodProgramaSetorial');
$obTextBoxSelectProgramaSetorial->obSelect->setId        ('inCodProgramaSetorial');
$obTextBoxSelectProgramaSetorial->obSelect->addOption    ('','Selecione');
$obTextBoxSelectProgramaSetorial->obSelect->setDependente(true);
$obTextBoxSelectProgramaSetorial->setNull                (false);

if ($stAcao == 'alterar') {
    $obITextBoxSelectPPA->setLabel(true);
}

//Informar código
$obTextBoxPrograma = new TextBox;
$obTextBoxPrograma->setRotulo    ('Programa');
$obTextBoxPrograma->setId        ('inCodPrograma');
$obTextBoxPrograma->setName      ('inCodPrograma');
$obTextBoxPrograma->setNull      (false);
$obTextBoxPrograma->setInteiro   (true);
$obTextBoxPrograma->setMaxLength (4);
$obTextBoxPrograma->setTitle     ('Escolha um codigo para programa');
$obTextBoxPrograma->setSize      (8);
$obTextBoxPrograma->obEvento->setOnBlur( "atualizaPrograma();preencheComZeros('9999', this, 'E');" );

$obITextBoxSelectTipoPrograma = new ITextBoxSelectTipoPrograma();
$obITextBoxSelectTipoPrograma->obTextBox->setValue($_REQUEST['inCodTipoPrograma']);
$obITextBoxSelectTipoPrograma->obSelect->setValue($_REQUEST['inCodTipoPrograma']);
$obITextBoxSelectTipoPrograma->setNull( false );

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
    include_once(TTPB."TTCEPBTipoObjetivoMilenio.class.php");

    $obTCEPBTipoObjetivoMilenio = new TTCEPBTipoObjetivoMilenio;
    $obTCEPBTipoObjetivoMilenio->recuperaTodos($rsTipoObjetivoMilenio);
    
    $obCmbObjMilenio= new Select;
    $obCmbObjMilenio->setRotulo   ("Tipo de Objetivo do Milênio");
    $obCmbObjMilenio->setName     ("inObjMilenio");
    $obCmbObjMilenio->setId       ( "inObjMilenio"    );
    $obCmbObjMilenio->setValue    ($rsPrograma->getCampo('cod_tipo_objetivo'));
    $obCmbObjMilenio->setStyle    ("width: 380px"     );
    $obCmbObjMilenio->setCampoID  ("cod_tipo_objetivo");
    $obCmbObjMilenio->setCampoDesc("[cod_tipo_objetivo] - [descricao]");
    $obCmbObjMilenio->addOption   ("", "Selecione");
    $obCmbObjMilenio->setNull     (false);
    $obCmbObjMilenio->preencheCombo($rsTipoObjetivoMilenio);
}
//Indentificação do programa
$obTextAreaIdPrograma = new TextArea;
$obTextAreaIdPrograma->setRotulo ('Identificação do Programa');
$obTextAreaIdPrograma->setTitle  ('Identificação do Programa');
$obTextAreaIdPrograma->setName   ('inIdPrograma');
$obTextAreaIdPrograma->setNull   (false);
$obTextAreaIdPrograma->setValue  ($rsPrograma->getCampo('identificacao'));
$obTextAreaIdPrograma->setMaxCaracteres (280);

//Justificativa do programa
$obTextAreaJustificativa = new TextArea;
$obTextAreaJustificativa->setRotulo ('Justificativa do Programa');
$obTextAreaJustificativa->setTitle  ('Justificativa do Programa');
$obTextAreaJustificativa->setName   ('stJustificativa');
$obTextAreaJustificativa->setNull   (false);
$obTextAreaJustificativa->setValue  ($rsPrograma->getCampo('justificativa'));
$obTextAreaJustificativa->setMaxCaracteres (480);

//Diagnostico do programa
$obTextAreaDiagnostico = new TextArea;
$obTextAreaDiagnostico->setRotulo ('Descrição do Problema');
$obTextAreaDiagnostico->setTitle  ('Descrição do Problema');
$obTextAreaDiagnostico->setName   ('inDigPrograma');
$obTextAreaDiagnostico->setNull   (false);
$obTextAreaDiagnostico->setValue  ($rsPrograma->getcampo('diagnostico'));
$obTextAreaDiagnostico->setMaxCaracteres (480);

//Objetivos do programa
$obTextAreaObjetivo = new TextArea;
$obTextAreaObjetivo->setRotulo ('Objetivos do Programa');
$obTextAreaObjetivo->setTitle  ('Objetivos do Programa');
$obTextAreaObjetivo->setName   ('inObjPrograma');
$obTextAreaObjetivo->setNull   (false);
$obTextAreaObjetivo->setValue  ($rsPrograma->getCampo('objetivo'));
$obTextAreaObjetivo->setMaxCaracteres (480);

//Diretrizes do programa
$obTextAreaDiretriz = new TextArea;
$obTextAreaDiretriz->setRotulo ('Estratégia de Implementação');
$obTextAreaDiretriz->setTitle  ('Estratégia de Implementação');
$obTextAreaDiretriz->setName   ('inDirPrograma');
$obTextAreaDiretriz->setNull   (false);
$obTextAreaDiretriz->setValue  ($rsPrograma->getCampo('diretriz'));
$obTextAreaDiretriz->setMaxCaracteres (480);

//Informar código
$obTextAreaAlvo = new TextArea;
$obTextAreaAlvo->setRotulo    ('Público-Alvo');
$obTextAreaAlvo->setTitle     ('Público-Alvo');
$obTextAreaAlvo->setName      ('inPublicoAlvo');
$obTextAreaAlvo->setNull      (false);
$obTextAreaAlvo->setValue     ($rsPrograma->getCampo('publico_alvo'));
$obTextAreaAlvo->setMaxCaracteres (480);

$obRdContinuo = new Radio;
$obRdContinuo->setName              ('boNatureza');
$obRdContinuo->setRotulo            ('Natureza Temporal');
$obRdContinuo->setTitle             ('Informe tipo de Natureza');
$obRdContinuo->setValue             ('t');
$obRdContinuo->setLabel             ('Contínuo');
$obRdContinuo->setNull              (false);
$obRdContinuo->obEvento->setOnChange("montaParametrosGET('montaData');");

$obRdTemporario = new Radio;
$obRdTemporario->setName   ('boNatureza');
$obRdTemporario->setValue  ('f');
$obRdTemporario->setLabel  ('Temporário');
$obRdTemporario->setNull   (false);
$obRdTemporario->obEvento->setOnChange( "montaParametrosGET('montaData');");

if ($_REQUEST['inContinuo'] == 'Temporário') {
    $obRdTemporario->setChecked(true);
    sistemaLegado::executaFrameOculto("montaParametrosGET('montaData')");
} else {
    $obRdContinuo->setChecked (true);
}

$obSpanData = new span;
$obSpanData->setId('spnDtPrograma');

$obSelectOrgao = new ITextBoxSelectOrgao;
$obSelectOrgao->setRotulo('*Órgão Responsável');
$obSelectOrgao->obTextBox->obEvento->setOnChange("montaParametrosGET('montaUnidade');");
$obSelectOrgao->obSelect->obEvento->setOnChange ("montaParametrosGET('montaUnidade');");
$obSelectOrgao->obTextBox->setValue($rsPrograma->getCampo('num_orgao'));
$obSelectOrgao->obSelect->setValue ($rsPrograma->getCampo('num_orgao'));

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo   ("Unidade Responsável");
$obTxtUnidade->setTitle    ("Informe a unidade");
$obTxtUnidade->setName     ("inCodUnidadeTxt");
$obTxtUnidade->setValue    ($rsPrograma->getCampo('num_unidade'));
$obTxtUnidade->setSize     (6);
$obTxtUnidade->setMaxLength(3);
$obTxtUnidade->setInteiro  (true);
$obTxtUnidade->setNull     (false);

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo   ("Unidade Responsável");
$obCmbUnidade->setName     ("inCodUnidade");
$obCmbUnidade->setValue    ($rsPrograma->getCampo('num_unidade'));
$obCmbUnidade->setStyle    ("width: 200px");
$obCmbUnidade->setCampoID  ("cod_unidade");
$obCmbUnidade->setCampoDesc("descricao");
$obCmbUnidade->addOption   ("", "Selecione");
$obCmbUnidade->setNull     (false);

//Informar descrição indicador
$obTextBoxDescIndicador = new TextBox;
$obTextBoxDescIndicador->setRotulo          ('Descrição do Indicador');
$obTextBoxDescIndicador->setTitle           ('Descrição do Indicador');
$obTextBoxDescIndicador->setName            ('stDescIndicador');
$obTextBoxDescIndicador->setId              ('stDescIndicador');
$obTextBoxDescIndicador->setNull            (true);
$obTextBoxDescIndicador->setMaxLength       (100);
$obTextBoxDescIndicador->setSize            (105);
$obTextBoxDescIndicador->setObrigatorioBarra(true);

include_once CAM_GA_ADM_COMPONENTES."ISelectUnidadeMedida.class.php";
$obISelectUnidadeMedida = new ISelectUnidadeMedida;
$obISelectUnidadeMedida->setName('stUnidadeMedida');
$obISelectUnidadeMedida->setId  ('stUnidadeMedida');
$obISelectUnidadeMedida->setValue($_REQUEST['stUnidadeMedida']);
$obISelectUnidadeMedida->setObrigatorioBarra(true);

//Informar Indice Recente
$obTxtIndiceRecente	 = new Numerico;
$obTxtIndiceRecente->setRotulo            ('Índice Recente');
$obTxtIndiceRecente->setTitle             ('Índice Recente');
$obTxtIndiceRecente->setName              ('flIndiceRecente');
$obTxtIndiceRecente->setId                ('flIndiceRecente');
$obTxtIndiceRecente->setDecimais          (2);
$obTxtIndiceRecente->setMaxValue          (999999999999.99);
$obTxtIndiceRecente->setNull              (true);
$obTxtIndiceRecente->setNegativo          (false);
$obTxtIndiceRecente->setNaoZero           (false);
$obTxtIndiceRecente->setSize              (20);
$obTxtIndiceRecente->setMaxLength         (14);
$obTxtIndiceRecente->obEvento->setOnKeyUp ("mascaraDinamico( '999,99', this, event);");
$obTxtIndiceRecente->setObrigatorioBarra  (true);

$obTxtIndiceDesejado = new Numerico;
$obTxtIndiceDesejado->setRotulo            ('Índice Desejado no final do PPA');
$obTxtIndiceDesejado->setName              ('flIndiceDesejado');
$obTxtIndiceDesejado->setId                ('flIndiceDesejado');
$obTxtIndiceDesejado->setTitle             ('Índice Desejado no final do PPA');
$obTxtIndiceDesejado->setDecimais          (2);
$obTxtIndiceDesejado->setMaxValue          (999999999999.99);
$obTxtIndiceDesejado->setNull              (true);
$obTxtIndiceDesejado->setNegativo          (false);
$obTxtIndiceDesejado->setNaoZero           (false);
$obTxtIndiceDesejado->setSize              (20);
$obTxtIndiceDesejado->setMaxLength         (14);
$obTxtIndiceDesejado->obEvento->setOnKeyUp ("mascaraDinamico( '999,99', this, event);");
$obTxtIndiceDesejado->setObrigatorioBarra  (true);

# Informar a Fonte do Índice
$obTxtFonteIndice = new TextBox;
$obTxtFonteIndice->setRotulo          ('Fonte do Índice');
$obTxtFonteIndice->setTitle           ('Fonte do Índice');
$obTxtFonteIndice->setName            ('stFonteIndice');
$obTxtFonteIndice->setId              ('stFonteIndice');
$obTxtFonteIndice->setSize            (105);
$obTxtFonteIndice->setMaxLength       (100);
$obTxtFonteIndice->setObrigatorioBarra(true);

include_once CAM_GF_PPA_MAPEAMENTO."TPPAPeriodicidade.class.php";
$TPPAPeriodicidade = new TPPAPeriodicidade;
$TPPAPeriodicidade->recuperaPeriodicidade($rsPeriodicidade);

$obCmbPeriodicidade = new Select;
$obCmbPeriodicidade->setRotulo          ("Periodicidade");
$obCmbPeriodicidade->setName            ("stPeriodicidade");
$obCmbPeriodicidade->setId              ("stPeriodicidade");
$obCmbPeriodicidade->setValue           ($_REQUEST['stPeriodicidade']);
$obCmbPeriodicidade->setStyle           ("width: 100px");
$obCmbPeriodicidade->setCampoID         ("cod_periodicidade");
$obCmbPeriodicidade->setCampoDesc       ("nom_periodicidade");
$obCmbPeriodicidade->addOption          ("", "Selecione");
$obCmbPeriodicidade->preencheCombo      ($rsPeriodicidade);
$obCmbPeriodicidade->setObrigatorioBarra(true);

# Informar a Base Geográfica
$obTxtBaseGeografica = new TextBox;
$obTxtBaseGeografica->setRotulo          ('Base Geográfica');
$obTxtBaseGeografica->setTitle           ('Base Geográfica');
$obTxtBaseGeografica->setName            ('stBaseGeografica');
$obTxtBaseGeografica->setId              ('stBaseGeografica');
$obTxtBaseGeografica->setSize            (105);
$obTxtBaseGeografica->setMaxLength       (100);
$obTxtBaseGeografica->setObrigatorioBarra(true);

# Informar a Fonte de Cálculo
$obTxtFormaCalculo = new TextBox;
$obTxtFormaCalculo->setRotulo          ('Forma de Cálculo');
$obTxtFormaCalculo->setTitle           ('Forma de Cálculo');
$obTxtFormaCalculo->setName            ('stFormaCalculo');
$obTxtFormaCalculo->setId              ('stFormaCalculo');
$obTxtFormaCalculo->setSize            (105);
$obTxtFormaCalculo->setMaxLength       (100);
$obTxtFormaCalculo->setObrigatorioBarra(true);

// Define objeto Data para validade final
$obDtIndiceRecente = new Data;
$obDtIndiceRecente->setName              ("dtIndiceRecente");
$obDtIndiceRecente->setId                ("dtIndiceRecente");
$obDtIndiceRecente->setRotulo            ("Data do Índice Recente");
$obDtIndiceRecente->setTitle             ('Informe a data do índice recente.');
$obDtIndiceRecente->setObrigatorioBarra  (true);

$obSpnIndiceDesejado = new span;
$obSpnIndiceDesejado->setId('spnIndiceDesejado');

$obSpnIndice = new span;
$obSpnIndice->setId('spnListaIndice');

//botoes dos Indicadores
$obBtnIncluirIndice = new Button;
$obBtnIncluirIndice->setName              ('btnIncluirIndice');
$obBtnIncluirIndice->setValue             ('Incluir');
$obBtnIncluirIndice->setTipo              ('button');
$obBtnIncluirIndice->obEvento->setOnClick ('incluirIndiceLista();');
$obBtnIncluirIndice->setDisabled          (false);

$obBtnLimparIndice = new Button;
$obBtnLimparIndice->setName               ('btnLimparIndice');
$obBtnLimparIndice->setValue              ('Limpar');
$obBtnLimparIndice->setTipo               ('button');
$obBtnLimparIndice->obEvento->setOnClick  ('limparIndice();');
$obBtnLimparIndice->setDisabled           (false);

$arBotoesIndice = array($obBtnIncluirIndice, $obBtnLimparIndice);

//DEFINICAO DO FORMULARIO
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnCtrl);

$obFormulario->addHidden    ($obHdnAnoInicioPPA);
$obFormulario->addHidden    ($obHdnAnoFinalPPA);
$obFormulario->addHidden    ($obHdnProgramaOrcamento);
$obFormulario->addHidden    ($obHdnModificarNatureza);

if ($stAcao == 'alterar') {
    $obFormulario->addTitulo    ('Dados para Alteração de Programas do PPA');
    $obFormulario->addHidden    ($obHdnCodPPA);
    $obFormulario->addHidden    ($obHdnNumPrograma);
    $obFormulario->addHidden    ($obHdnCodProgramaSetorial);
    $obFormulario->addHidden    ($obHdnCodMacro);
    $obFormulario->addHidden    ($obHdnCodPrograma);
    $obFormulario->addHidden    ($obHdnDtInicio);
    $obFormulario->addHidden    ($obHdnDtFinal);
    $obFormulario->addHidden    ($obHdnOrgao);
    $obFormulario->addHidden    ($obHdnUnidade);
    $obFormulario->addHidden    ($obHdnDataInicialTemporario);
    $obFormulario->addHidden    ($obHdnDataFinalTemporario);
    $obFormulario->addHidden    ($obHdnDataMinimaAcao);
    $obFormulario->addHidden    ($obHdnDataMaximaAcao);
    $obFormulario->addComponente($obITextBoxSelectPPA);
    $obFormulario->addComponente($obLabelMacro);
    $obFormulario->addComponente($obLabelProgramaSetorial);
    $obFormulario->addComponente($obLabelPrograma);
} elseif ($boProgramaOrcamento == true) {
    $obFormulario->addHidden    ($obHdnCodPrograma);
    $obFormulario->addComponente($obITextBoxSelectPPA);
    $obFormulario->addComponente($obTextBoxSelectMacroObjetivo);
    $obFormulario->addComponente($obTextBoxSelectProgramaSetorial);
    $obFormulario->addComponente($obLabelPrograma);
} else {
    $obFormulario->addTitulo    ('Dados para Inclusão de Programas do PPA');
    $obFormulario->addComponente($obITextBoxSelectPPA);
    $obFormulario->addComponente($obTextBoxSelectMacroObjetivo);
    $obFormulario->addComponente($obTextBoxSelectProgramaSetorial);
    $obFormulario->addComponente($obTextBoxPrograma);
}

$obFormulario->addComponente($obITextBoxSelectTipoPrograma);
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 15){
    $obFormulario->addComponente($obCmbObjMilenio);
}
$obFormulario->addComponente($obTextAreaIdPrograma);
$obFormulario->addComponente($obTextAreaJustificativa);
$obFormulario->addComponente($obTextAreaDiagnostico);
$obFormulario->addComponente($obTextAreaObjetivo);
$obFormulario->addComponente($obTextAreaDiretriz);
$obFormulario->addComponente($obTextAreaAlvo);
$obFormulario->agrupaComponentes    (array($obRdContinuo,$obRdTemporario) );
$obFormulario->addSpan              ($obSpanData);
$obFormulario->addTitulo            ('Órgão Responsável');
$obFormulario->addComponente        ($obSelectOrgao);
$obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
$obFormulario->addTitulo            ('Indicadores');
$obFormulario->addComponente        ($obTextBoxDescIndicador);
$obFormulario->addComponente        ($obISelectUnidadeMedida);
$obFormulario->addComponente        ($obTxtIndiceRecente);
$obFormulario->addComponente        ($obDtIndiceRecente);
$obFormulario->addComponente        ($obTxtIndiceDesejado);
$obFormulario->addComponente        ($obTxtFonteIndice);
$obFormulario->addComponente        ($obCmbPeriodicidade);
$obFormulario->addComponente        ($obTxtBaseGeografica);
$obFormulario->addComponente        ($obTxtFormaCalculo);
$obFormulario->agrupaComponentes    ($arBotoesIndice);
$obFormulario->addSpan              ($obSpnIndice);

# Define botões de ação.
$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick('SalvarPrograma();');

if ($stAcao == 'alterar' || $boProgramaOrcamento == 'true') {
    $obBtnCancelar = new Cancelar();
    $obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
} else {
    $obBtnCancelar = new Limpar();
    $obBtnCancelar->setValue('Limpar');
    $obBtnCancelar->obEvento->setOnClick('frm.reset();');
}

$arButoes = array($obBtnOK, $obBtnCancelar);
$obFormulario->defineBarra($arButoes);

$obFormulario->show();

$jsOnLoad = "buscaMacroObjetivos();";
$jsOnLoad .= "montaParametrosGET('montaData');";

if ($stAcao == 'alterar') {
    $obSpnIndice->setValue($obVisao->buscaIndicadores($_REQUEST));
    $jsOnLoad .= "montaParametrosGET('recuperaPPA');";
    $jsOnLoad .= "montaParametrosGET('montaUnidade');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
