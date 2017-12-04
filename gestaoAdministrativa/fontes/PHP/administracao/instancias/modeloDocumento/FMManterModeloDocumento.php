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
    * Página de Formulario para Modelo de Documentos
    * Data de Criação   : 20/02/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 7968 $
    $Name$
    $Autor: $
    $Date: 2006-03-28 17:58:07 -0300 (Ter, 28 Mar 2006) $

    * Casos de uso: uc-01.03.100
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoMenu.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterModeloDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

Sessao::write("arArquivos",array());
Sessao::write("arArquivosExcluidos",array());
Sessao::write("arArquivosIncluidos",array());
$inCount = 0;

$rsGestao = new RecordSet;
$rsModulo =  new RecordSet;
$rsFuncionalidade = new RecordSet;
$rsAcao =  new RecordSet;
$rsDocumento =  new RecordSet;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//if ($stAcao == 'incluir') {
    if ( Sessao::read('numCgm') == '0' ) {
        $obErro = $obRAdministracaoMenu->listarGestoesPorOrdem();
    } else {
        $obErro = $obRAdministracaoMenu->listarGestoes();
    }
    //Monta o recordset para o preenchimento do combo de gestões
    $rsGestao = new RecordSet;
    if ( !$obErro->ocorreu() ) {
        $arGestao = Array();
        while ( !$obRAdministracaoMenu->rsRAdministracaoGestao->eof() ) {
            $obRGestao = $obRAdministracaoMenu->rsRAdministracaoGestao->getObjeto();
            $arTmpGestao = array( 'cod_gestao' => $obRGestao->getCodigoGestao(),
                                  'nom_gestao' => $obRGestao->getNomeGestao() );
            $arGestao[] = $arTmpGestao;
            $obRAdministracaoMenu->rsRAdministracaoGestao->proximo();
        }
        $rsGestao->preenche( $arGestao );
    }
//}

// monta lista de arquivos

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setEncType                 ( "multipart/form-data" );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtGestao = new TextBox;
$obTxtGestao->setRotulo       ( "Gestão" );
$obTxtGestao->setName         ( "inCodGestaoTxt" );
$obTxtGestao->setValue        ( $inCodGestaoTxt );
$obTxtGestao->obEvento->setOnChange ( "buscaValor('montaModulos')");

$obCmbGestao = new Select;
$obCmbGestao->setRotulo        ( "Gestão" );
$obCmbGestao->setName          ( "inCodGestao" );
$obCmbGestao->setValue         ( $inCodGestao );
$obCmbGestao->setStyle         ( "width: 200px");
$obCmbGestao->setCampoID       ( "cod_gestao" );
$obCmbGestao->setCampoDesc     ( "nom_gestao" );
$obCmbGestao->addOption        ( "", "Selecione" );
$obCmbGestao->setNull          ( false );
$obCmbGestao->preencheCombo    ( $rsGestao );
$obCmbGestao->obEvento->setOnChange ( "buscaValor('montaModulos')");

// modulos
$obTxtModulo = new TextBox;
$obTxtModulo->setRotulo       ( "Módulo" );
$obTxtModulo->setName         ( "inCodModuloTxt" );
$obTxtModulo->setValue        ( $inCodModuloTxt );
$obTxtModulo->obEvento->SetOnChange("buscaValor('montaFuncionalidade');");

$obCmbModulo = new Select;
$obCmbModulo->setRotulo        ( "Módulo" );
$obCmbModulo->setName          ( "inCodModulo" );
$obCmbModulo->setValue         ( $inCodModulo );
$obCmbModulo->setStyle         ( "width: 200px");
$obCmbModulo->setCampoID       ( "cod_modulo" );
$obCmbModulo->setCampoDesc     ( "nom_modulo" );
$obCmbModulo->addOption        ( "", "Selecione" );
$obCmbModulo->setNull          ( false );
$obCmbModulo->preencheCombo    ( $rsModulo );
$obCmbModulo->obEvento->SetOnChange("buscaValor('montaFuncionalidade');");

// funcionalidades
$obTxtFuncionalidade = new TextBox;
$obTxtFuncionalidade->setRotulo       ( "Funcionalidade" );
$obTxtFuncionalidade->setName         ( "inCodFuncionalidadeTxt" );
$obTxtFuncionalidade->setValue        ( $inCodFuncionalidadeTxt );
$obTxtFuncionalidade->obEvento->SetOnChange("buscaValor('montaAcao');");

$obCmbFuncionalidade = new Select;
$obCmbFuncionalidade->setRotulo        ( "Funcionalidade" );
$obCmbFuncionalidade->setName          ( "inCodFuncionalidade" );
$obCmbFuncionalidade->setValue         ( $inCodFuncionalidade  );
$obCmbFuncionalidade->setStyle         ( "width: 200px");
$obCmbFuncionalidade->addOption        ( "", "Selecione" );
$obCmbFuncionalidade->setCampoID       ( "cod_funcionalidade" );
$obCmbFuncionalidade->setCampoDesc     ( "nom_funcionalidade" );
$obCmbFuncionalidade->preencheCombo    ( $rsFuncionalidade );
$obCmbFuncionalidade->setNull          ( false );
$obCmbFuncionalidade->obEvento->SetOnChange("buscaValor('montaAcao');");

// ação
$obTxtAcao = new TextBox;
$obTxtAcao->setRotulo       ( "Ação" );
$obTxtAcao->setName         ( "inCodAcaoTxt" );
$obTxtAcao->setValue        ( $inCodAcaoTxt );
$obTxtAcao->obEvento->SetOnChange("buscaValor('montaDocumento');");

$obCmbAcao = new Select;
$obCmbAcao->setRotulo        ( "Ação" );
$obCmbAcao->setName          ( "inCodAcao" );
$obCmbAcao->setValue         ( $inCodAcao  );
$obCmbAcao->setStyle         ( "width: 200px");
$obCmbAcao->addOption        ( "", "Selecione" );
$obCmbAcao->setCampoID       ( "cod_acao" );
$obCmbAcao->setCampoDesc     ( "nom_acao" );
$obCmbAcao->preencheCombo    ( $rsAcao );
$obCmbAcao->setNull          ( false );
$obCmbAcao->obEvento->SetOnChange("buscaValor('montaDocumento');");

// ação
$obTxtDocumento = new TextBox;
$obTxtDocumento->setRotulo       ( "Documento" );
$obTxtDocumento->setName         ( "inCodDocumentoTxt" );
$obTxtDocumento->setId           ( "inCodDocumentoTxt" );
$obTxtDocumento->setValue        ( $inCodDocumentoTxt );
$obTxtDocumento->obEvento->SetOnChange("buscaValor('montaListaArquivos');");

$obCmbDocumento = new Select;
$obCmbDocumento->setRotulo        ( "Documento" );
$obCmbDocumento->setName          ( "inCodDocumento" );
$obCmbDocumento->setValue         ( $inCodDocumento  );
$obCmbDocumento->setStyle         ( "width: 200px");
$obCmbDocumento->addOption        ( "", "Selecione" );
$obCmbDocumento->setCampoID       ( "cod_documento" );
$obCmbDocumento->setCampoDesc     ( "nom_documento" );
$obCmbDocumento->preencheCombo    ( $rsDocumento );
$obCmbDocumento->setNull          ( false );
$obCmbDocumento->obEvento->SetOnChange(" buscaValor('montaListaArquivos');");

// arquivos
$obFbArquivo = new FileBox;
$obFbArquivo->setNull   ( true     );
$obFbArquivo->setRotulo ( "Arquivo" );
$obFbArquivo->setTitle  ( "Selecione o arquivo a ser inserido");
$obFbArquivo->setSize   ( 25 );
$obFbArquivo->setId     ( "aqArquivo" );
$obFbArquivo->setName   ( "aqArquivo" );

$obBtnOk = new Button;
$obBtnOk->setName ( "btnIncluirArquivo" );
$obBtnOk->setValue( "Adicionar" );
$obBtnOk->setTipo ( "button" );
$obBtnOk->obEvento->setOnClick ( "incluirArquivo();" );
/*
$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimparArquivo" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaArquivo();" );
*/

$obSpnLista = new Span;
$obSpnLista->setId("spnLista");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                ( "Dados para Documento" );
$obFormulario->addForm                  ( $obForm );
$obFormulario->addHidden                ( $obHdnAcao );
$obFormulario->addHidden                ( $obHdnCtrl );
$obFormulario->addComponenteComposto    ( $obTxtGestao, $obCmbGestao );
$obFormulario->addComponenteComposto    ( $obTxtModulo, $obCmbModulo );
$obFormulario->addComponenteComposto    ( $obTxtFuncionalidade, $obCmbFuncionalidade );
$obFormulario->addComponenteComposto    ( $obTxtAcao, $obCmbAcao );
$obFormulario->addComponenteComposto    ( $obTxtDocumento, $obCmbDocumento );
$obFormulario->addTitulo                ( "Arquivos"    );
$obFormulario->addComponente            ( $obFbArquivo  );
//$obFormulario->defineBarra              ( array( $obBtnOk ,$obBtnLimpar ) );
$obFormulario->addComponente            ( $obBtnOk      );
$obFormulario->addSpan                  ( $obSpnLista );
$obFormulario->OK                       ();
$obFormulario->show                     ();

?>
