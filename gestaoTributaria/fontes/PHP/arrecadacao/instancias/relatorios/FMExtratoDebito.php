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
    * Formulario de Consulta de Arrecadação
    * Data de Criação   : 13/07/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: FMExtratoDebito.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.8  2007/08/16 18:12:40  dibueno
Bug#9927#

Revision 1.7  2007/08/15 18:13:55  dibueno
Bug#9927#

Revision 1.6  2007/08/09 21:33:43  dibueno
Bug#9873#

Revision 1.5  2007/08/01 21:05:17  dibueno
Bug#9781#

Revision 1.4  2007/08/01 20:06:04  dibueno
Bug#9781#

Revision 1.3  2007/08/01 20:05:48  dibueno
Bug#9781#

Revision 1.2  2007/07/16 18:21:52  dibueno
Bug #9659#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                                                      );
include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php"                               );
include_once( CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRRelatorioExtratoDebitos.class.php"                        );

//Define o nome dos arquivos PHP
$stPrograma = "ExtratoDebito";
$pgFilt   = "FL".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgOcul   = "OCRelatorio".$stPrograma.".php";
$pgJs     = "JS".$stPrograma."s.js";

include_once($pgJs);

// passar do request pra variaveis
$inInscricao        = $request->get("inInscricao"       );
$inNumCgm           = $request->get("inNumCgm"          );
$stNomCgm           = $request->get("stNomCgm"          );
$stDados            = $request->get("stDados"           );

//* LISTAGEM DE PROPRIETARIOS
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"     );
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCGM              = new RCGM;
/* Listar Proprietarios */
/* Se estiver tudo certo, busca proprietarios do imovel */

// HIDDENS
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$stFiltroSQL = null;

if ( !$request->get("inCGM") && !$request->get("inInscricaoEconomica") && !$request->get("inCodImovel") ) {
    SistemaLegado::alertaAviso($pgFilt, "Campos 'Contribuinte', 'Inscrição Imobiliária' ou 'Inscrição Econômica' não foram preenchidos!", "n_emitir", "erro" );
    exit;
}

// COMPONENTES
$obLabelInscricao = new Label;
if ( $request->get('inCodImovel') ) {

    $stFiltroSQL .= "\n aic.inscricao_municipal = ".$request->get("inCodImovel")." AND\n";

    $stFiltroINNER = "
        INNER JOIN arrecadacao.imovel_calculo as aic
        ON aic.cod_calculo = ac.cod_calculo
    ";

    $obTCIMImovel = new TCIMImovel;

    $stFiltro = " AND I.inscricao_municipal = ".$_REQUEST["inCodImovel"];
    $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );

    $stTituloInscricao = "Inscrição Imobiliária";
    $stEnderecoImovel = $_REQUEST["inCodImovel"].' - '.$rsImoveis->getCampo("logradouro");
    if ( $rsImoveis->getCampo("numero") ) {
        $stEnderecoImovel .= ", ".$rsImoveis->getCampo("numero");
    }

    if ( $rsImoveis->getCampo("complemento") ) {
        $stEnderecoImovel .= " - ".$rsImoveis->getCampo("complemento");
    }

    $stInscricao = $stEnderecoImovel;

    //==================== PROPRIETARIOS
    $stProprietarios = '';
    $rsProprietarios = new RecordSet;
    $obTCIMProprietario = new TCIMProprietario;
    $arProprietarios = array();

    $stFiltro = " WHERE inscricao_municipal = ".$_REQUEST["inCodImovel"]." ";
    $stOrdem  = " ORDER BY inscricao_municipal, ordem ";
    $obTCIMProprietario->recuperaTodos( $rsProprietarios, $stFiltro, $stOrdem, $boTransacao );

    if ( $rsProprietarios->getNumLinhas() > 0 ) {
        $inCont = 0;
        while (!$rsProprietarios->eof()) {
            $inNumCgm   = $rsProprietarios->getCampo("numcgm"   );
            $obRCGM->setNumCGM  ($inNumCgm  );
            $obRCGM->consultar  ( $rsCGM    );
            $arProprietarios[$inCont][ 'inSeq'   ] = $inCont;
            $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm;
            $arProprietarios[$inCont][ 'nome'   ] = $obRCGM->getNomCGM();

            $stProprietarios .= $inNumCgm . ' - '. $obRCGM->getNomCGM(). '<br>';

            $rsProprietarios->proximo();
            $inCont++;
        }
    } else {
        $stProprietarios = $_REQUEST['stProprietarios'];
    }

    Sessao::write( 'TipoInscricao', "II" );
    Sessao::write( 'Proprietarios', $arProprietarios );

} elseif ($_REQUEST['inInscricaoEconomica']) {

    $stFiltroSQL .= " cec.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"]." AND\n";

    $stFiltroINNER = "
        INNER JOIN arrecadacao.cadastro_economico_calculo as cec
        ON cec.cod_calculo = ac.cod_calculo
    ";

    $obTCEMCadastroEconomico = new TCEMCadastroEconomico;

    $stFiltro = " AND CE.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"];
    $obTCEMCadastroEconomico->recuperaInscricao( $rsEmpresas, $stFiltro );

    $stTituloInscricao = "Inscrição Econômica";
    $stInscricao =    $_REQUEST['inInscricaoEconomica'].' - ';
    $stInscricao .=   str_replace ( "'", "", $rsEmpresas->getCampo('nom_cgm') );

    $obTCEMCadastroEconomico->recuperaInscricaoEndereco($rsEnderecoEmpresa,$_REQUEST["inInscricaoEconomica"]);

    Sessao::write( 'TipoInscricao', "IE" );
    Sessao::write( 'DadosComplementares', $rsEnderecoEmpresa->getCampo("endereco") );
} else {
    $stFiltroSQL .= " accgm.numcgm = ".$_REQUEST["inCGM"]." AND\n";
    $stTituloInscricao = "Contribuinte";
    $stInscricao = $_REQUEST['inCGM']." - ". $_REQUEST['stNomCGM'];
}

if ($_REQUEST['stExercicio']) {
    $stFiltroSQL .= " ac.exercicio = '".$_REQUEST["stExercicio"]."' AND\n";
}

if ($stFiltroSQL) {
    $stFiltroSQL = substr ( $stFiltroSQL, 0, (strlen ($stFiltroSQL) - 4 ));
}

$stTipoInscricao = Sessao::read( 'TipoInscricao' );
if ($stTipoInscricao == "II") {
    $obLabelContribuinte = new Label;
    $obLabelContribuinte->setRotulo ( "Contribuinte"   );
    $obLabelContribuinte->setValue  ( $stProprietarios );
} elseif ($stTipoInscricao == "IE") {
    $obLabelContribuinte = new Label;
    $obLabelContribuinte->setRotulo ( "Dados Complementares"   );
    $arDadosComp = Sessao::read( 'DadosComplementares' );
    $obLabelContribuinte->setValue  ( $arDadosComp );
}

#$valorInscricao = $_REQUEST['inInscricaoEconomica'].$_REQUEST['inCodImovel'].$_REQUEST['inNumCgm'];
Sessao::write( 'vinculo', $stTituloInscricao );
Sessao::write( 'valor_vinculo', $stInscricao );

if ($_REQUEST['stExercicio']) {
    Sessao::write( 'exercicio', $_REQUEST['stExercicio'] );
}else{
    Sessao::write( 'exercicio', '' );
}

$obLabelInscricao   = new Label;
$obLabelInscricao->setRotulo    ( $stTituloInscricao );
$obLabelInscricao->setValue     ( $stInscricao );

$obForm = new Form;
$obForm->setName    ("frmX");
$obFormulario = new Formulario;
$obFormulario->addForm ($obForm);
$obFormulario->addComponente ( $obLabelInscricao );

$stTipoInscricao = Sessao::read( 'TipoInscricao' );

if ( ( $stTipoInscricao == "II" ) || ( $stTipoInscricao == "IE" ) ) {
    $obFormulario->addComponente ( $obLabelContribuinte );
}

$obFormulario->show();

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
#$obFormulario = new Formulario;
#$obFormulario->addForm      ( $obForm );

$obHdnSQL = new Hidden;
$obHdnSQL->setName ( "stSQL" );
$obHdnSQL->setValue( $stFiltroSQL );

$obHdnINNER = new Hidden;
$obHdnINNER->setName ( "stINNER" );
$obHdnINNER->setValue( $stFiltroINNER );

$obSpnDetalhes = new Span;
$obSpnDetalhes->setId('spnDetalhes');

$obBtnRelatorio = new Button;
$obBtnRelatorio->setName                    ( "btnRelatorio"                    );
$obBtnRelatorio->setValue                   ( "Gerar Relatório"                 );
$obBtnRelatorio->setTipo                    ( "button"                          );
$obBtnRelatorio->obEvento->setOnClick       ( "document.frm.submit()"  );
$obBtnRelatorio->setDisabled                ( false                             );

$boFARRRelatorioExtratoDebitos = new FARRRelatorioExtratoDebitos;
$boFARRRelatorioExtratoDebitos->recuperaRelatorioOrigem ( $rsListaOrigem, $stFiltroINNER, $stFiltroSQL );

$rsListaOrigem->addFormatacao('valor', "NUMERIC_BR");

$table = new Table();
$table->setRecordset( $rsListaOrigem );
$table->setSummary( 'Parcelas em Aberto' );

$table->Head->addCabecalho( 'Exercício' , 10  );
$table->Head->addCabecalho( 'Lançamento' , 15  );
$table->Head->addCabecalho( 'Origem' , 45  );
$table->Head->addCabecalho( 'Qtde de Parcelas' , 10  );
$table->Head->addCabecalho( 'Total' , 20  );

$table->Body->addCampo( 'exercicio', "C" );
$table->Body->addCampo( 'cod_lancamento', "C" );
$table->Body->addCampo( 'origem', "E");
$table->Body->addCampo( 'qtde', "C" );
$table->Body->addCampo( 'valor', "D" );

$table->Foot->addSoma ( 'valor', "D" );

$table->montaHTML();
$valor = $table->getHTML();

$valor = str_replace( "\n" ,"" ,$valor );
$valor = str_replace( "  " ,"" ,$valor );
$valor = str_replace( "'","\\'",$valor );

$obHdnTipo = new Hidden;
$obHdnTipo->setName("stTipo");
$obHdnTipo->setValue( "" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_ARR_INSTANCIAS."relatorios/OCRelatorioExtratoDebitos.php" );

$obButtonRelatorio = new Button;
$obButtonRelatorio->setName  ( "Relatorio" );
$obButtonRelatorio->setValue ( "Relatório" );
$obButtonRelatorio->obEvento->setOnClick( "document.frm2.submit();");

$obRadioTipoSimples = new Radio;
$obRadioTipoSimples->setName         ( "boTipoRelatorio"               );
$obRadioTipoSimples->setTitle        ( "Tipo de Relatório"             );
$obRadioTipoSimples->setRotulo       ( "Tipo de Relatório"             );
$obRadioTipoSimples->setValue        ( "Simples"                       );
$obRadioTipoSimples->setLabel        ( "Simples"                       );
$obRadioTipoSimples->setChecked      ( true                            );

$obRadioTipoDetalhado = new Radio;
$obRadioTipoDetalhado->setName     ( "boTipoRelatorio"      );
$obRadioTipoDetalhado->setValue    ( "Detalhado"            );
$obRadioTipoDetalhado->setLabel    ( "Detalhado"            );
$obRadioTipoDetalhado->setChecked  ( false                  );

    $obForm->setName    ("frm2");
    $obFormulario = new Formulario;
    $obFormulario->addForm ($obForm);

    $obFormulario->addHidden    ( $obHdnSQL                 );
    $obFormulario->addHidden    ( $obHdnINNER               );
    $obFormulario->addHidden    ( $obHdnAcao                );
    $obFormulario->addHidden    ( $obHdnCtrl                );
    $obFormulario->addHidden    ( $obHdnCaminho             );

    echo $valor;

    $obFormulario->addComponenteComposto ( $obRadioTipoSimples, $obRadioTipoDetalhado  );
    $obFormulario->defineBarra( array( $obButtonRelatorio ), "left", "" );
    $obFormulario->show();

$stJs .= "buscaValor('PreencheSpanOrigem'); ";
sistemaLegado::executaFrameOculto ( $stJs );

?>
