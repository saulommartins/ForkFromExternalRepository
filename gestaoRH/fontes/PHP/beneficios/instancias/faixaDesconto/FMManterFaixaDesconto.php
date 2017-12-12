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
* Página de Formulário Beneficio Faixa Desconto
* Data de Criação   : 07/07/2005

* @author Analista: Vandré Ramos
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Caso de uso: uc-04.06.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_BEN_NEGOCIO."RBeneficioVigencia.class.php");
include_once( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php" );

$stPrograma = "ManterFaixaDesconto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$rsFaixas    = new RecordSet;
$obRegra     = new RBeneficioVigencia;
$obRBeneficioFornecedorValeTransporte = new RBeneficioFornecedorValeTransporte;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('Faixas', array());

if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";
    //$obRegra->obRFaixaDesconto->listarFaixaDesconto( $rsFaixas );

} elseif ($stAcao) {
    $obRegra->setCodVigencia( $_REQUEST['inCodVigencia'] );
    $obRegra->listarVigencia( $rsLista );

    $dtDataVigencia = $rsLista->getCampo('vigencia');
    $inCodTipoNorma = $rsLista->getCampo('cod_tipo_norma');
    $inCodNorma     = $rsLista->getCampo('cod_norma');
    $stTipo         = $rsLista->getCampo('tipo');

    $obRegra->addBeneficioFaixaDesconto();
    $obRegra->roUltimoFaixaDesconto->listarFaixaDesconto( $rsFaixas );
    $inCount = 0;
    $rsFaixas->addFormatacao("vl_inicial"      , "NUMERIC_BR");
    $rsFaixas->addFormatacao("vl_final"        , "NUMERIC_BR");
    $rsFaixas->addFormatacao("percentual_desconto", "NUMERIC_BR");

    $arFaixas = Sessao::read('Faixas');

    while ( !$rsFaixas->eof() ) {
        $arTMP['inId']              = $inCount++;
        $arTMP['inCodFaixas']       = $rsFaixas->getCampo("cod_faixa");
        $arTMP['flSalarioInicial']  = $rsFaixas->getCampo("vl_inicial");
        $arTMP['flSalarioFinal']    = $rsFaixas->getCampo("vl_final");
        $arTMP['flPercentualDesc']  = $rsFaixas->getCampo("percentual_desconto");

        $arFaixas[] = $arTMP;
        $rsFaixas->proximo();
    }

    Sessao::write('Faixas', $arFaixas);

    include_once($pgJs);
    sistemaLegado::executaFrameOculto("buscaValor('preencheInner');");
}

$obForm = new Form;
$obForm->setTarget( "oculto" );
$obForm->setAction( $pgProc );
if( $stAcao == 'consultar')
    $obForm->setAction( $pgList."?".Sessao::getId().$stLink );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodOrganograma = new Hidden;
$obHdnCodOrganograma->setName( "inCodPrevidencia" );
$obHdnCodOrganograma->setValue( $inCodPrevidencia );

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName( "hdninCodFaixaDesconto" );
$obHdnCodNorma->setValue( $hdninCodFaixaDesconto );

//Define objeto TEXTBOX para armazenar a DESCRICAO da previdencia
$obTxtVigencia = new Data;
$obTxtVigencia->setRotulo        ( "Vigência" );
$obTxtVigencia->setTitle         ( "Informe a Vigência" );
$obTxtVigencia->setName          ( "dtDataVigencia" );
$obTxtVigencia->setValue         ( $dtDataVigencia  );
$obTxtVigencia->setNull          ( false );
if ($stAcao == 'consultar') {
    $obTxtVigencia->setDisabled      ( true );
    $obTxtVigencia->setStyle         ( 'color:#333333' );
}

$stOrdem = " ORDER BY sw_cgm.nom_cgm";
$obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporte( $rsLista, $stOrdem );
// Define o tipo do beneficio
$obRdoTipoA = new Radio;
$obRdoTipoA->setName    ( "stTipo" );
$obRdoTipoA->setTitle   ( "Informe o tipo do benefício" );
$obRdoTipoA->setRotulo  ( "*Tipo" );
$obRdoTipoA->setLabel   ( "Vale-Transporte" );
$obRdoTipoA->setValue   ( "v" );
$obRdoTipoA->setChecked ( $stTipo == 'v' || !$stTipo );
if ($stAcao == 'consultar') {
    $obRdoTipoA->setDisabled      ( true );
    $obRdoTipoA->setStyle         ( 'color:#333333' );
}

$obRdoTipoV = new Radio;
$obRdoTipoV->setName    ( "stTipo" );
$obRdoTipoV->setRotulo  ( "*Tipo" );
$obRdoTipoV->setLabel   ( "Auxílio Refeição" );
$obRdoTipoV->setValue   ( "a");
$obRdoTipoV->setChecked ( $stTipo == 'a' );
if ($stAcao == 'consultar') {
    $obRdoTipoV->setStyle         ( 'color:#333333' );
}
    $obRdoTipoV->setDisabled      ( true );

/************************************/
include_once(CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php" );
include_once(CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );

$obTTipoNorma = new TTipoNorma();
$obTTipoNorma->recuperaTodos( $rsTipoNorma );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo              ( "Tipo de Norma"             );
$obCmbTipoNorma->setName                ( "inCodTipoNorma"            );
if ($stAcao == 'consultar') {
    $obCmbTipoNorma->setValue           ( $inCodTipoNorma             );
    $obCmbTipoNorma->setDisabled        ( true                        );
} else {
    $obCmbTipoNorma->addOption          ( "", "Selecione"             );
}
$obCmbTipoNorma->setCampoID             ( "cod_tipo_norma"            );
$obCmbTipoNorma->setCampoDesc           ( "nom_tipo_norma"            );
$obCmbTipoNorma->setNull                ( false                       );
$obCmbTipoNorma->preencheCombo          ( $rsTipoNorma                );
$obCmbTipoNorma->obEvento->setOnChange  ( "buscaValor('montaNorma');" );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo        ( "Norma"         );
$obCmbNorma->setName          ( "inCodNorma"    );
if ($stAcao == 'consultar') {
    $obTNorma = new TNorma();
    $obTNorma->recuperaNormas  ( $rsNorma, " INNER JOIN normas.tipo_norma ON tipo_norma.cod_tipo_norma = N.cod_tipo_norma WHERE N.cod_tipo_norma = ".$inCodTipoNorma  );
    $obCmbNorma->setCampoID    ( "cod_norma"  );
    $obCmbNorma->setCampoDesc  ( "nom_norma"  );
    $obCmbNorma->preencheCombo ( $rsNorma     );
    $obCmbNorma->setValue      ( $inCodNorma  );
    $obCmbNorma->setDisabled   ( true         );
}
$obCmbNorma->setCampoID       ( "cod_norma"     );
$obCmbNorma->setCampoDesc     ( "nom_norma"     );
$obCmbNorma->addOption        ( "", "Selecione" );
$obCmbNorma->setNull          ( false           );
/************************************/

//Faixas de Desconto para beneficios

//Define objeto TEXTBOX para armazenar o VALOR  para SALARIO INICIAL
$obTxtSalarioInicial = new Moeda;
$obTxtSalarioInicial->setRotulo     ( "*Salário Inicial" );
$obTxtSalarioInicial->setName       ( "flSalarioInicial" );
$obTxtSalarioInicial->setValue      ( $flSalarioInicial  );
$obTxtSalarioInicial->setTitle      ( "Faixa de Salário Inicial" );
$obTxtSalarioInicial->setNull       ( true );
$obTxtSalarioInicial->setMaxLength  ( 10 );
$obTxtSalarioInicial->setSize       ( 10 );

//Define objeto TEXTBOX para armazenar o VALOR  para SALARIO FINAL
$obTxtSalarioFinal = new Moeda;
$obTxtSalarioFinal->setRotulo     ( "*Salário Final" );
$obTxtSalarioFinal->setName       ( "flSalarioFinal" );
$obTxtSalarioFinal->setValue      ( $flSalarioFinal  );
$obTxtSalarioFinal->setTitle      ( "Faixa de Salário Final" );
$obTxtSalarioFinal->setNull       ( true );
$obTxtSalarioFinal->setMaxLength  ( 10   );
$obTxtSalarioFinal->setSize       ( 10   );

//Define objeto TEXTBOX para armazenar o Percentual para DESCONTO
$obTxtDesconto = new Moeda;
$obTxtDesconto->setRotulo     ( "*Percentual" );
$obTxtDesconto->setName       ( "flPercentualDesc" );
$obTxtDesconto->setValue      ( $flPercentualDesc  );
$obTxtDesconto->setTitle      ( "Percentual de Desconto" );
$obTxtDesconto->setNull       ( true );
$obTxtDesconto->setMaxLength  ( 6     );
$obTxtDesconto->obEvento->setOnChange ( "validaDesconto(document.frm.flPercentualDesc.value, document.frm.flPercentualDesc, 'Percentual de Desconto');" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "return IncluiFaixa();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaPrevidencia();" );

$obSpnFaixas = new Span;
$obSpnFaixas->setId ( "spnFaixas" );

//DEFINICAO DOS COMPONENTES
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );

$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addTitulo            ( "Dados de Faixa"    );
$obFormulario->addComponente        ( $obCmbTipoNorma     );
$obFormulario->addComponente        ( $obCmbNorma         );
$obFormulario->addComponente        ( $obTxtVigencia      );
$obFormulario->addComponenteComposto( $obRdoTipoA, $obRdoTipoV  );

if ($stAcao == "incluir") {
    $obFormulario->addTitulo            ( "Faixas de Desconto"  );
    $obFormulario->addComponente        ( $obTxtSalarioInicial  );
    $obFormulario->addComponente        ( $obTxtSalarioFinal    );
    $obFormulario->addComponente        ( $obTxtDesconto        );
    $obFormulario->defineBarra          ( array( $obBtnIncluir , $obBtnLimpar ) ,'','');
}

$obFormulario->addSpan              ( $obSpnFaixas );

if ($stAcao == "incluir")
    $obFormulario->OK();
if ($stAcao == "consultar") {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;
    $obButtonVoltar = new Button;
    $obButtonVoltar->setName  ( "Voltar" );
    $obButtonVoltar->setValue ( "Voltar" );
    $obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
    $obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
}

$obFormulario->show();

include_once($pgJs);
if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    sistemaLegado::executaFrameOculto($js);
}

?>
