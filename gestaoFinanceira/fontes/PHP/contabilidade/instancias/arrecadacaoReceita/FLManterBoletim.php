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
    * Página de Filtro Excluir Boletim
    * Data de Criação   : 12/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: FLManterBoletim.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceitaBoletim.class.php";

$stPrograma = "ManterBoletim";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once $pgJs;

Sessao::remove('link');
Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

$obRegra = new RContabilidadeLancamentoReceitaBoletim;
$obRegra->obROrcamentoReceita->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obROrcamentoReceita->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Incluir validação adicional no Salvar
$stEval = "
if (document.frm.inNumeroBoletim.value == '' && document.frm.stDtInicial.value == '' && document.frm.stDtFinal.value == '') {
    erro = true;
    mensagem += '@Você deve informar o número ou a data do Boletim que deseja excluir!';
}
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stEval );

$obTxtEntidade = new TextBox;
$obTxtEntidade->setRotulo              ( "Entidade"                   );
$obTxtEntidade->setTitle               ( "Selecione a Entidade"       );
$obTxtEntidade->setName                ( "inCodEntidadeTxt"           );

// Completa com o codigo da unica entidade, caso houver somente uma, o textbox.
// Se tiver mais de uma entidade, cai nas garras do AddComponenteComposto
if ($rsEntidades->getNumLinhas()==1) {
     $obTxtEntidade->setValue          ( $rsEntidades->getCampo('cod_entidade')  );
} else $obTxtEntidade->setValue          ( $inCodEntidadeTxt            );

$obTxtEntidade->setSize                ( 6                            );
$obTxtEntidade->setMaxLength           ( 3                            );
$obTxtEntidade->setInteiro             ( true                         );
$obTxtEntidade->setNull                ( false                        );

$obCmbEntidade = new Select;
$obCmbEntidade->setRotulo              ( "Entidade"                    );
$obCmbEntidade->setName                ( "inCodEntidade"               );
$obCmbEntidade->setValue               ( $inCodEntidade                );
$obCmbEntidade->setStyle               ( "width: 300px"                );
$obCmbEntidade->setCampoID             ( "cod_entidade"                );
$obCmbEntidade->setCampoDesc           ( "nom_cgm"                     );

// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidades->getNumLinhas()>1) {
    $obCmbEntidade->addOption              ( "", "Selecione"               );
}

$obCmbEntidade->preencheCombo          ( $rsEntidades                  );
$obCmbEntidade->setNull                ( false                         );

$obTxtNumeroBoletim = new TextBox;
$obTxtNumeroBoletim->setRotulo         ( "Número do Boletim"            );
$obTxtNumeroBoletim->setTitle          ( "Informe o número do boletim"  );
$obTxtNumeroBoletim->setName           ( "inNumeroBoletim"              );
$obTxtNumeroBoletim->setValue          ( $inNumeroBoletim               );
$obTxtNumeroBoletim->setSize           ( 10                             );
$obTxtNumeroBoletim->setMaxLength      ( 4                              );
$obTxtNumeroBoletim->setInteiro        ( true                           );

// Define Objeto Data
$obTxtDtInicial = new Data;
$obTxtDtInicial->setName        ( "stDtInicial"         );
$obTxtDtInicial->setRotulo      ( "Data do Boletim"     );
$obTxtDtInicial->setTitle       ( "Informe a data inicial e final do boletim"  );

// define Label
$obLblData = new Label;
$obLblData->setId( 'ate' );
$obLblData->setValue( ' até ' );

// Define Objeto Data
$obTxtDtFinal = new Data;
$obTxtDtFinal->setName          ( "stDtFinal"           );
$obTxtDtFinal->setRotulo        ( "Data do Boletim"     );
$obTxtDtFinal->setTitle         ( "Informe a data inicial e final do boletim"  );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnEval , true);

$obFormulario->addTitulo            ( "Dados para filtro" );
$obFormulario->addComponenteComposto( $obTxtEntidade, $obCmbEntidade  );
$obFormulario->addComponente        ( $obTxtNumeroBoletim );
$obFormulario->agrupaComponentes    ( array( $obTxtDtInicial, $obLblData, $obTxtDtFinal ) );

$obFormulario->OK                   ();
$obFormulario->show                 ();

/*$js .= "focusIncluir();";
SistemaLegado::SistemaLegado::executaFramePrincipal($js);*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
