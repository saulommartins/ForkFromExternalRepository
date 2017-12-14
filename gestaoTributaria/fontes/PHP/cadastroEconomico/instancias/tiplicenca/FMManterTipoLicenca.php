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
   * Página de Formulario de Inclusao/Alteracao de Serviços

   * Data de Criação   : 15/04/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * $Id: FMManterTipoLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.11

*/

/*
$Log$
Revision 1.11  2007/05/14 20:34:47  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.10  2007/05/11 20:25:29  dibueno
Alterações para possibilitar a emissao do alvará

Revision 1.9  2006/10/11 10:15:13  dibueno
Inclusao do combo para busca de modelo de documento / alvará

Revision 1.8  2006/09/15 14:33:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtributoTipoLicencaDiversa.class.php"  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMTipoLicencaModeloDocumento.class.php");

include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterTipoLicenca";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//$stAcao = $request->get('stAcao');
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao )  ) {
    $stAcao = "incluir";
    }

//if ( empty( $stAcao ) ) {
//    $stAcao = "incluir";
//}
$obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
$obRCEMElemento           = new RCEMElemento( $this );

$rsAtributosDisponiveis   = new RecordSet;
$rsAtributosSelecionados  = new RecordSet;
$rsElementosSelecionados  = new RecordSet;
$rsElementosDisponiveis   = new RecordSet;

$obRCEMTipoLicencaDiversa->obRCadastroDinamico->obRModulo->setCodModulo(14);
$obRCEMTipoLicencaDiversa->obRCadastroDinamico->setCodCadastro( 4 );
$obRCEMTipoLicencaDiversa->obRCEMConfiguracao->consultarConfiguracao();

//$obHdnCodAcao = new Hidden;
//$obHdnCodAcao->setName ('stCodAcao');
//$obHdnCodAcao->setValue ('462');
//$obHdnCodAcao->setValue( $_REQUEST["stCodAcao"]);
//$_REQUEST["stCodAcao"] = '462';
//Sessao::write('acao', '462');

//$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
//$stAcao = $request->get('stAcao');
if ($stAcao == "incluir") {
    $obErro = $obRCEMTipoLicencaDiversa->obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosDisponiveis );
    $obRCEMElemento->listarElemento( $rsElementosDisponiveis  );
/*
    if ($stAcao == "alterar") {
        $obRCEMTipoLicencaDiversa->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis  );
    }
*/

} else {
    $obRCEMTipoLicencaDiversa->obRCadastroDinamico->setPersistenteAtributos( new TCEMAtributoTipoLicencaDiversa );
    $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_GET['inCodigoTipoLicencaDiversa'] );
    $obRCEMTipoLicencaDiversa->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo"=>$obRCEMTipoLicencaDiversa->getCodigoTipoLicencaDiversa() ) );
    $obRCEMTipoLicencaDiversa->obRCadastroDinamico->recuperaAtributosDisponiveis ( $rsAtributosDisponiveis  );
    $obRCEMTipoLicencaDiversa->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosSelecionados );

    $obRCEMTipoLicencaDiversa->consultar( $rsTipoLicencaDiversa );
    $stNomeTipoLicencaDiversa = $obRCEMTipoLicencaDiversa->getNomeTipoLicencaDiversa();
    $inTipoUtilizacao = $rsTipoLicencaDiversa->getCampo("cod_utilizacao");
    Sessao::write( 'inTipoUtilizacao', $inTipoUtilizacao );

    $obRCEMTipoLicencaDiversa->addTipoLicencaDiversaElemento();
    $obRCEMTipoLicencaDiversa->roUltimoElemento->referenciaTipoLicencaDiversa( $obRCEMTipoLicencaDiversa );

    $obRCEMTipoLicencaDiversa->roUltimoElemento->roRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_GET['inCodigoTipoLicencaDiversa'] );
    $obRCEMTipoLicencaDiversa->setTipoUtilizacao($inTipoUtilizacao);
    $obRCEMTipoLicencaDiversa->roUltimoElemento->listarElementoTipoLicencaDiversaSelecionados  ( $rsElementosSelecionados );
    $obRCEMTipoLicencaDiversa->roUltimoElemento->listarElementoTipoLicencaDiversaDisponiveis   ( $rsElementosDisponiveis  );

    $obTCEMTipoLicencaModeloDocumento = new TCEMTipoLicencaModeloDocumento;
    $obTCEMTipoLicencaModeloDocumento->setDado('cod_tipo', $_REQUEST["inCodigoTipoLicencaDiversa"]);
    $obTCEMTipoLicencaModeloDocumento->recuperaPorChave ( $rsTLMD );
    $inCodAlvara = $rsTLMD->getCampo("cod_documento");

}

$acaoReferente = sessao::read( "acao" );
$obITextBoxSelectDocumento = new ITextBoxSelectDocumento;
$obITextBoxSelectDocumento->setCodAcao( $acaoReferente );
$obITextBoxSelectDocumento->setCodModeloDocumento ( $inCodAlvara );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setRotulo ( "Modelo do Alvará" );
$obITextBoxSelectDocumento->obTextBoxSelectDocumento->setNULL   ( false );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( "");

$obHdnCodigoTipoLicencaDiversa =  new Hidden;
$obHdnCodigoTipoLicencaDiversa->setName   ( "inCodigoTipoLicencaDiversa" );
$obHdnCodigoTipoLicencaDiversa->setValue  ( $_REQUEST["inCodigoTipoLicencaDiversa"]  );

$obTxtNomeTipoLicencaDiversa = new TextBox ;
$obTxtNomeTipoLicencaDiversa->setRotulo    ( "Nome" );
$obTxtNomeTipoLicencaDiversa->setName      ( "stNomeTipoLicencaDiversa");
$obTxtNomeTipoLicencaDiversa->setValue     ( $stNomeTipoLicencaDiversa );
$obTxtNomeTipoLicencaDiversa->setTitle     ( "Nome da TipoLicencaDiversa" );
$obTxtNomeTipoLicencaDiversa->setSize      ( 80 );
$obTxtNomeTipoLicencaDiversa->setMaxLength ( 80 );
$obTxtNomeTipoLicencaDiversa->setNull      ( false );

$obRadioDiversas = new Radio;
$obRadioDiversas->setName     ("inTipoUtilizacao");
$obRadioDiversas->setId       ("inTipoUtilizacao");
$obRadioDiversas->setRotulo   ("Utilização");
$obRadioDiversas->setValue    ( 1 );
$obRadioDiversas->setLabel    ("Diversas");
$obRadioDiversas->setNull     ( false );
$obRadioDiversas->setChecked  ( true );

$obRadioSolo = new Radio;
$obRadioSolo->setName     ("inTipoUtilizacao");
$obRadioSolo->setId       ("inTipoUtilizacao");
$obRadioSolo->setRotulo   ("Utilização");
$obRadioSolo->setValue    ( 2 );
$obRadioSolo->setLabel    ("Uso e Ocupação do Solo");
$obRadioSolo->setNull     ( false );
$obRadioSolo->setChecked  ( false );

//Select para listar os atributos
$obCmbAtributos = new SelectMultiplo();
$obCmbAtributos->setName        ('inCodAtributos');
$obCmbAtributos->setRotulo      ( "Atributos" );
$obCmbAtributos->setNull        ( true );
$obCmbAtributos->setTitle       ( "Atributos" );

// lista de atributos disponiveis
$obCmbAtributos->SetNomeLista1  ('inCodAtributosDisponiveis');
$obCmbAtributos->setCampoId1    ('cod_atributo');
$obCmbAtributos->setCampoDesc1  ('nom_atributo');
$obCmbAtributos->SetRecord1     ( $rsAtributosDisponiveis );

// lista de atributos selecionados
$obCmbAtributos->SetNomeLista2  ('inCodAtributosSelecionados');
$obCmbAtributos->setCampoId2    ('cod_atributo');
$obCmbAtributos->setCampoDesc2  ('nom_atributo');
$obCmbAtributos->SetRecord2     ( $rsAtributosSelecionados );

//definicao dos combos de elementos
$obCmbElementos = new SelectMultiplo();
$obCmbElementos->setName        ( "inCodElementosSelecionados" );
$obCmbElementos->setRotulo      ( "Elementos" );
$obCmbElementos->setNull        ( true );
$obCmbElementos->setTitle       ( "Elementos que serão solicitados na inclusão da Atividade" );

// lista de elementos disponiveis
$obCmbElementos->SetNomeLista1  ( "inCodElementosDisponiveis" );
$obCmbElementos->setCampoId1    ( "cod_elemento" );
$obCmbElementos->setCampoDesc1  ( "nom_elemento" );
$obCmbElementos->SetRecord1     ( $rsElementosDisponiveis );

// lista de elementos selecionados
$obCmbElementos->SetNomeLista2  ( "inCodElementosSelecionados" );
$obCmbElementos->setCampoId2    ( "cod_elemento" );
$obCmbElementos->setCampoDesc2  ( "nom_elemento" );
$obCmbElementos->SetRecord2     ( $rsElementosSelecionados );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );
//$obForm->setTarget('telaPrincipal');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm );
$obFormulario->setAjuda         ( "UC-05.02.11");
$obFormulario->addTitulo        ( "Dados para Tipo de Licença" );

if ($stAcao == "alterar") {
$obFormulario->addHidden        ( $obHdnCodigoTipoLicencaDiversa);

    if ($inTipoUtilizacao == 1) {
        $obRadioDiversas->setChecked(true);
    } else {
        $obRadioSolo->setChecked(true);
    }
$obRadioDiversas->setDisabled   ( true  );
$obRadioSolo->setDisabled       ( true  );
}

$obSpanTeste = new Span;
$obSpanTeste->setId("SpnTeste");

$obFormulario->addHidden        ( $obHdnCtrl );
$obFormulario->addHidden        ( $obHdnAcao );
//$obFormulario->addHidden        ( $obHdnCodAcao );
$obFormulario->addComponente    ( $obTxtNomeTipoLicencaDiversa );
$obFormulario->addComponenteComposto ( $obRadioDiversas, $obRadioSolo );
$obFormulario->addComponente    ( $obCmbAtributos );
$obITextBoxSelectDocumento->geraFormulario ( $obFormulario );

$obFormulario->addTitulo        ( "Elementos para Base de Cálculo" );
$obFormulario->addComponente    ( $obCmbElementos );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {

    $obFormulario->Cancelar( $pgList."?PHPSESSID=".$PHPSESSID );
}
$obFormulario->addSpan($obSpanTeste);
$obFormulario->show();

//sistemaLegado::executaFrameOculto( $stJs );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
