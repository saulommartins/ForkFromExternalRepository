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
  * Arquivo de instância para manutenção de organograma
  * Data de Criação: 25/07/2005

  * @author Analista: Cassiano
  * @author Desenvolvedor: Cassiano

  Casos de uso: uc-01.05.01

  $Id: FMManterOrganograma.php 61288 2014-12-30 12:29:30Z evandro $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrganograma.class.php";
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";

$stPrograma = "ManterOrganograma";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

$rsTipoNorma = $rsNorma = new RecordSet;
$obRegra     = new ROrganogramaOrganograma;
$stLocation  = $pgList;

$stAcao                 = $request->get('stAcao');
$inCodOrganograma       = $request->get('inCodOrganograma');
$boPermissaoHierarquica = $request->get('boPermissaoHierarquica');

// Inicia a sessão de níveis.
Sessao::write('niveis', array());

if (empty($stAcao) || $stAcao == "incluir") {
    $stAcao = "incluir";
    $obRegra->obRNorma->obRTipoNorma->listar( $rsTipoNorma );

} elseif ($stAcao) {
    $obRNorma = new RNorma;
    $obRegra->obRNorma->obRTipoNorma->listar( $rsTipoNorma );
    $obRegra->setCodOrganograma( $_REQUEST['inCodOrganograma'] );
    $obRegra->consultar();
    $obRegra->listarNiveis( $rsNiveis );
    $obRegra->listarOrgaosRelacionados ($rsOrgaosRelacionados);
    if ( $rsOrgaosRelacionados->getNumLinhas () > 0 ) {
        sistemaLegado::exibeAviso("Níveis não exibidos por existirem órgãos cadastrados.", "", "aviso");
    }
    $stDataImplantacao      = $obRegra->getDtImplantacao();
    $inCodNorma             = $obRegra->obRNorma->getCodNorma();
    
    $obRNorma->setCodNorma ($inCodNorma);
    $obRNorma->consultar ( $rsNormaTMP );
    $inCodTipoNorma = $obRNorma->obRTipoNorma->getCodTipoNorma ();

    $obRegra2     = new ROrganogramaOrganograma;
    $obRegra2->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
    $obRegra2->obRNorma->listar( $rsNorma );

    $inCount = 0;

    while (!$rsNiveis->eof()) {
        $arTMP['inId']        = $inCount++;
        $arTMP['inCodNivel']  = $rsNiveis->getCampo("cod_nivel");
        $arTMP['stDescNivel'] = $rsNiveis->getCampo("descricao");
        $arTMP['stMascaraNivel'] = $rsNiveis->getCampo("mascaracodigo");

        $niveis[] = $arTMP;

        Sessao::write('niveis',$niveis);
        $rsNiveis->proximo();
    }
    SistemaLegado::executaFrameOculto("buscaValor('preencheInner');");
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName( "inCodOrganograma" );
$obHdnCodOrganograma->setValue( $inCodOrganograma );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName( "hdninCodNorma" );
$obHdnCodNorma->setValue(isset($hdninCodNorma) ? $hdninCodNorma : null );

if ($stAcao == "alterar" && is_numeric($inCodOrganograma)) {
    $obTOrganogramaOrganograma = new TOrganogramaOrganograma;
    $obTOrganogramaOrganograma->setDado('cod_organograma', $inCodOrganograma);
    $obTOrganogramaOrganograma->recuperaProcessamentoOrganograma($rsUtilizacaoOrganograma);

    $boOrganogramaProcessado = (bool) $rsUtilizacaoOrganograma->getCampo('organograma_processado');
}

# Na inclusão de organograma ou se o organograma não tiver sido utilizado,
# permite informar a data de implantação
$obTxtDataImplantacao = new Data;
$obTxtDataImplantacao->setRotulo ( "Data de Implantação" );
$obTxtDataImplantacao->setTitle  ( "Data de Implantação do Organograma" );
$obTxtDataImplantacao->setName   ( "stDataImplantacao" );
$obTxtDataImplantacao->setValue  ( isset($stDataImplantacao) ? $stDataImplantacao : null );
$obTxtDataImplantacao->setNull   ( false );

# Na alteração, valida para ver se o organograma já não foi utilizado, não
# permitindo alterar a data de implantação, pois já está implantado.
$obLblDtImplantacao = new Label;
$obLblDtImplantacao->setRotulo ( 'Data de Implantação');
$obLblDtImplantacao->setId     ( 'stLblDtImplantacao' );
$obLblDtImplantacao->setName   ( 'stLblDtImplantacao' );
$obLblDtImplantacao->setValue  ( isset($stDataImplantacao) ? $stDataImplantacao : null );

$obHdnDtImplantacao = new Hidden;
$obHdnDtImplantacao->setId    ( 'stDataImplantacao' );
$obHdnDtImplantacao->setName  ( 'stDataImplantacao' );
$obHdnDtImplantacao->setValue ( isset($stDataImplantacao) ? $stDataImplantacao : null );

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo    ( "Tipo Norma" );
$obTxtTipoNorma->setTitle     ( "Tipo de norma vinculada à criação do organograma" );
$obTxtTipoNorma->setName      ( "inCodTipoNormaTxt" );
$obTxtTipoNorma->setValue     ( isset($inCodTipoNorma) ? $inCodTipoNorma : null );
$obTxtTipoNorma->setSize      ( 5 );
$obTxtTipoNorma->setMaxLength ( 5 );
$obTxtTipoNorma->setInteiro   ( true  );
$obTxtTipoNorma->setNull      ( false );
$obTxtTipoNorma->obEvento->setOnChange("buscaValor('MontaNorma');");

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo     ( "Tipo Norma" );
$obCmbTipoNorma->setName       ( "inCodTipoNorma" );
$obCmbTipoNorma->setValue      ( isset($inCodTipoNorma) ? $inCodTipoNorma : null );
$obCmbTipoNorma->setStyle      ( "width: 200px");
$obCmbTipoNorma->setCampoID    ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc  ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption     ( "", "Selecione" );
$obCmbTipoNorma->setNull       ( false );
$obCmbTipoNorma->preencheCombo ( $rsTipoNorma );
$obCmbTipoNorma->obEvento->setOnChange("buscaValor('MontaNorma');");

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo    ( "Norma" );
$obTxtNorma->setTitle     ( "Norma vinculada à criação do organograma" );
$obTxtNorma->setName      ( "inCodNormaTxt" );
$obTxtNorma->setValue     ( isset($inCodNorma) ? $inCodNorma : null );
$obTxtNorma->setSize      ( 5 );
$obTxtNorma->setMaxLength ( 5 );
$obTxtNorma->setInteiro   ( true  );
$obTxtNorma->setNull      ( false );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo     ( "Norma" );
$obCmbNorma->setName       ( "inCodNorma" );
$obCmbNorma->setValue      ( isset($inCodNorma) ? $inCodNorma : null);
$obCmbNorma->setStyle      ( "width: 200px");
$obCmbNorma->setCampoID    ( "cod_norma" );
$obCmbNorma->setCampoDesc  ( "nom_norma" );
$obCmbNorma->addOption     ( "", "Selecione" );
$obCmbNorma->setNull       ( false );
$obCmbNorma->preencheCombo ( $rsNorma );

# Permissão Hierarquica
$obRdoPermissaoSim = new Radio;
$obRdoPermissaoSim->setName    ( 'boPermissaoHierarquica' );
$obRdoPermissaoSim->setId      ( 'boPermissaoHierarquica' );
$obRdoPermissaoSim->setTitle   ( 'Define se haverá permissão para visualizar registros na hierarquia do Organograma'  );
$obRdoPermissaoSim->setRotulo  ( 'Permissão Hierárquica'  );
$obRdoPermissaoSim->setLabel   ( 'Sim'  );
$obRdoPermissaoSim->setValue   ( 'true' );
$obRdoPermissaoSim->setNull    ( false  );

if ($boPermissaoHierarquica == 'Sim') {
    $obRdoPermissaoSim->setChecked ( true );
}

$obRdoPermissaoNao = new Radio;
$obRdoPermissaoNao->setName    ( 'boPermissaoHierarquica' );
$obRdoPermissaoNao->setId      ( 'boPermissaoHierarquica' );
$obRdoPermissaoNao->setRotulo  ( 'Permissão Hierárquica'  );
$obRdoPermissaoNao->setLabel   ( 'Não' );
$obRdoPermissaoNao->setValue   ( 'false' );
$obRdoPermissaoNao->setNull    ( false   );

if ($boPermissaoHierarquica == 'Não' || empty($boPermissaoHierarquica) ) {
    $obRdoPermissaoNao->setChecked ( true );
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo    ( "*Descrição" );
$obTxtDescricao->setTitle     ( "Descrição do nível do organograma" );
$obTxtDescricao->setName      ( "stDescNivel" );
$obTxtDescricao->setValue     ( isset($stDescNivel) ? $stDescNivel : null );
$obTxtDescricao->setSize      ( 40 );
$obTxtDescricao->setMaxLength ( 100 );

$obTxtMascNivel = new TextBox;
$obTxtMascNivel->setRotulo    ( "*Máscara para o Nível" );
$obTxtMascNivel->setTitle     ( "Máscara para o código do nível" );
$obTxtMascNivel->setName      ( "stMascaraNivel" );
$obTxtMascNivel->setValue     ( isset($stMascaraNivel) ? $stMascaraNivel : null );
$obTxtMascNivel->setSize      ( 5 );
$obTxtMascNivel->setMaxLength ( 5 );
$obTxtMascNivel->setInteiro   ( true  );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "return IncluiNivel();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName  ( "btnLimpar" );
$obBtnLimpar->setValue ( "Limpar" );
$obBtnLimpar->setTipo  ( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaNivel();" );

$obSpnNiveis = new Span;
$obSpnNiveis->setId ( "spnNiveis" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( 'UC-01.05.01' );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addTitulo     ( "Dados do organograma" );

# Validação para permitir ou não a alteração da data de implantação.
if ($stAcao == "incluir" || $boOrganogramaProcessado == false) {
    $obFormulario->addComponente ( $obTxtDataImplantacao );
} else {
    $obFormulario->addHidden     ( $obHdnDtImplantacao );
    $obFormulario->addComponente ( $obLblDtImplantacao );
}

$obFormulario->addComponenteComposto( $obTxtTipoNorma , $obCmbTipoNorma );
$obFormulario->addComponenteComposto( $obTxtNorma     , $obCmbNorma     );
$obFormulario->agrupaComponentes    ( array( $obRdoPermissaoSim , $obRdoPermissaoNao ) );

if ($stAcao != 'incluir') {
    $obFormulario->addHidden ( $obHdnCodOrganograma );
    $obFormulario->addHidden ( $obHdnCodNorma );
}

if ($stAcao != "alterar" || $rsOrgaosRelacionados->eof()) {
    $obFormulario->addTitulo     ( "Dados do nível"  );
    $obFormulario->addComponente ( $obTxtDescricao   );
    $obFormulario->addComponente ( $obTxtMascNivel   );
    $obFormulario->defineBarra   ( array( $obBtnIncluir , $obBtnLimpar ) ,'','');
    $obFormulario->addSpan       ( $obSpnNiveis );
}

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar($stLocation);
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
