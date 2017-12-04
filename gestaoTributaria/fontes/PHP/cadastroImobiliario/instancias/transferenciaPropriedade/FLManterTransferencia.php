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
    * Página de filtro para o cadastro de transferência de proipriedade
    * Data de Criação   : 03/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FLManterTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.10  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCIMConfiguracao = new RCIMConfiguracao;

$obRCIMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCIMConfiguracao->getMascaraIM();

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                               ( "stAcao"                                                     );
$obHdnAcao->setValue                              ( $_REQUEST['stAcao']                                          );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                               ( "stCtrl"                                                     );
$obHdnCtrl->setValue                              ( $_REQUEST['stCtrl']                                          );

$obHdnCampoNumDom = new Hidden;
$obHdnCampoNumDom->setName( "stNumeroDomicilio" );
$obHdnCampoNumDom->setID  ( "stNumeroDomicilio" );

$obBscInscricaoMunicipal = new BuscaInner;
$obBscInscricaoMunicipal->setNull                  ( true                                                        );
$obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"                                     );
$obBscInscricaoMunicipal->setTitle                 ( "Inscrição imobiliária que será transferida"                );
$obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"                                    );
$obBscInscricaoMunicipal->obCampoCod->setId        ( "inInscricaoImobiliaria"                                    );
$obBscInscricaoMunicipal->obCampoCod->setSize      ( strlen($stMascaraInscricao)                                 );
$obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)                                 );
$obBscInscricaoMunicipal->setFuncaoBusca("abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inInscricaoImobiliaria','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');");

/*
$obTxtInscricaoImobiliaria = new TextBox;
$obTxtInscricaoImobiliaria->setName               ( "inInscricaoImobiliaria"                                     );
$obTxtInscricaoImobiliaria->setSize               ( strlen( $stMascaraInscricao )                                );
$obTxtInscricaoImobiliaria->setMaxLength          ( strlen( $stMascaraInscricao )                                );
$obTxtInscricaoImobiliaria->setInteiro            ( true );
$obTxtInscricaoImobiliaria->setNull               ( true                                                         );
$obTxtInscricaoImobiliaria->setRotulo             ( "Inscrição Imobiliária"                                      );
$obTxtInscricaoImobiliaria->setTitle              ( "Inscrição imobiliária que será transferida"                 );
//$obTxtInscricaoImobiliaria->setMascara            ( $stMascaraInscricao );
$obTxtInscricaoImobiliaria->setPreencheComZeros   ( 'E' );
//$obTxtInscricaoImobiliaria->obEvento->setOnChange ( "buscaFiltro('buscaInscricao');"                             );
*/

$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo                              ( "Adquirente"                                                 );
$obBscCGM->setTitle                               ( "Informa um adquirente para o filtro"                        );
$obBscCGM->setNull                                ( true                                                         );
$obBscCGM->setId                                  ( "campoInner"                                                 );
$obBscCGM->obCampoCod->setName                    ( "inNumCGM"                                                   );
$obBscCGM->obCampoCod->setValue                   ( $inNumCGM                                                    );
$obBscCGM->obCampoCod->obEvento->setOnChange      ( "buscaFiltro('buscaCGMFiltro');"                                   );
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','geral','".Sessao::getId()."','800','550')" );

$obBtnOk = new Ok;

$obBtnLimparFiltro = new Button;
$obBtnLimparFiltro->setName                       ( "btnLimparFiltro"                                            );
$obBtnLimparFiltro->setValue                      ( "Limpar"                                                     );
$obBtnLimparFiltro->obEvento->setOnClick          ( "limparFiltro();"                                            );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                               );
$obFormulario->setAjuda ( "UC-05.01.17" );
$obFormulario->addTitulo     ( "Dados para filtro"                   );
$obFormulario->addHidden     ( $obHdnAcao                            );
$obFormulario->addHidden     ( $obHdnCtrl                            );
$obFormulario->addHidden     ( $obHdnCampoNumDom );
//$obFormulario->addComponente ( $obTxtInscricaoImobiliaria            );
$obFormulario->addComponente ( $obBscInscricaoMunicipal              );
$obFormulario->addComponente ( $obBscCGM                             );
$obFormulario->defineBarra   ( array( $obBtnOk, $obBtnLimparFiltro ) );
$obFormulario->setFormFocus  ( $obBscInscricaoMunicipal->obCampoCod->getId() );
$obFormulario->show();
?>
