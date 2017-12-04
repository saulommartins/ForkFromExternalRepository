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
    * Pagina de formulário Almoxarife
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.02

    $Id: FMManterAlmoxarife.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php"       );
include_once ( CAM_GA_ADM_COMPONENTES."IPopUpUsuario.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

$stNomCGMAlmoxarife = $_REQUEST['stNomCGMAlmoxarife'];
$inCGMAlmoxarife = $_REQUEST['inCGMAlmoxarife'];
$stDescQuestao = $_REQUEST['stDescQuestao'];

$rsAlmoxarifados = new RecordSet;
$rsAlmoxarifadosPermitidos = new RecordSet;
$obRegra = new RAlmoxarifadoAlmoxarife;
if ($stAcao == 'alterar') {
   $obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM($_GET['inCGMAlmoxarife']);
   $obRegra->consultar();

   $inCGM = $obRegra->obRCGMAlmoxarife->obRCGM->getNumCGM();
   $boAtivo = $obRegra->getAtivo();
   $obRegra->listarPermissao($rsAlmoxarifadosPermitidos);

   while ( !$rsAlmoxarifadosPermitidos->eof() ) {
      if ( $rsAlmoxarifadosPermitidos->getCampo( 'padrao' ) == 't' ) {
          $inCodPadrao = $rsAlmoxarifadosPermitidos->getCampo( 'codigo' );
      }
      $rsAlmoxarifadosPermitidos->proximo();
   }

   $boAtivo = $boAtivo == 't' ? true : false;
} else {
   $boAtivo = true;
}

$obRegra->listarDisponiveis($rsAlmoxarifados);

$obForm = new Form;
$obForm->setAction    ( $pgProc );
$obForm->setTarget    ( "oculto" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

if ($stAcao=="incluir") {
    $obBscCGMAlmoxarife = new IPopUpUsuario ($obForm);
    $obBscCGMAlmoxarife->setRotulo('Almoxarife');
    $obBscCGMAlmoxarife->setId    ('stNomCGMAlmoxarife');
    $obBscCGMAlmoxarife->setNull  ( false  );
    $obBscCGMAlmoxarife->setTitle ( 'Informe o CGM relacionado ao almoxarife');
    $obBscCGMAlmoxarife->setValue ( $stNomCGMAlmoxarife  );
    $obBscCGMAlmoxarife->obCampoCod->setValue ( $inCGM     );
    $obBscCGMAlmoxarife->obCampoCod->setSize  (10);
    $obBscCGMAlmoxarife->obCampoCod->setName  ( 'inCodCGMAlmoxarife' );

} else {
    $obHdnCGM = new Hidden;
    $obHdnCGM->setName("inCodCGMAlmoxarife");
    $obHdnCGM->setValue($inCGMAlmoxarife);

    $obLblCodigoCGM= new Label;
    $obLblCodigoCGM->setRotulo ( "Almoxarife"                              );
    $obLblCodigoCGM->setValue  ( $inCGMAlmoxarife." - ".$stNomCGMAlmoxarife);
}

$obRdbAtivo = new Radio;
$obRdbAtivo->setTitle  ( "Selecione o status do almoxarife." );
$obRdbAtivo->setName   ( "boAtivo" );
$obRdbAtivo->setId     ( "boAtivo" );
$obRdbAtivo->setChecked( $boAtivo  );
$obRdbAtivo->setValue  ( 'true' );
$obRdbAtivo->setRotulo ( "Status" );
$obRdbAtivo->setLabel  ( "Ativo" );
$obRdbAtivo->setNull   ( false );

$obRdbInativo = new Radio;
$obRdbInativo->setName   ( "boAtivo" );
$obRdbInativo->setId     ( "boAtivo" );
$obRdbInativo->setValue  ( 'false'    );
$obRdbInativo->setLabel  ( "Inativo"  );
$obRdbInativo->setChecked( !$boAtivo  );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbAlmoxarifados = new SelectMultiplo();
$obCmbAlmoxarifados->setName   ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setTitle  ( "Selecione os almoxarifados que o almoxarife possui permissão.");
$obCmbAlmoxarifados->setRotulo ( "Almoxarifados" );
$obCmbAlmoxarifados->setTitle  ( "" );

$obCmbAlmoxarifados->SetNomeLista1 ('inCodAlmoxarifadoDisponivel');
$obCmbAlmoxarifados->setCampoId1   ( '[codigo]-[nom_a]' );
$obCmbAlmoxarifados->setTitle  ( "Selecione os almoxarifados que o almoxarife possui permissão.");
$obCmbAlmoxarifados->setCampoDesc1 ( '[codigo]-[nom_a]' );
$obCmbAlmoxarifados->SetRecord1    ( $rsAlmoxarifados );

$rsAlmoxarifadosPermitidos->setPrimeiroElemento();

$obCmbAlmoxarifados->SetNomeLista2 ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setCampoId2   ('[codigo]-[nom_a]');
$obCmbAlmoxarifados->setCampoDesc2 ('[codigo]-[nom_a]');
$obCmbAlmoxarifados->SetRecord2    ( $rsAlmoxarifadosPermitidos );
$stOnClick = "selecionaAlmoxarifados(true);buscaValor('preencheComboPadrao');selecionaAlmoxarifados(false)";
$obCmbAlmoxarifados->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbAlmoxarifados->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
$obCmbAlmoxarifados->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
$obCmbAlmoxarifados->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
$obCmbAlmoxarifados->obSelect1->obEvento->setOnDblClick( $stOnClick );
$obCmbAlmoxarifados->obSelect2->obEvento->setOnDblClick( $stOnClick );

$obCmbPadrao = new Select;
$obCmbPadrao->setRotulo      ( "Almoxarifado Padrão"                       );
$obCmbPadrao->setTitle       ( "Selecione o almoxarifado padrão." );
$obCmbPadrao->setName        ( "inCodPadrao"           );
$obCmbPadrao->setValue       ( $inCodPadrao                   );
$obCmbPadrao->setStyle       ( "width: 200px"                );
$obCmbPadrao->setCampoID     ( "codigo"            );
$obCmbPadrao->setCampoDesc   ( "[codigo]-[nom_a]"                   );
$obCmbPadrao->addOption      ( "", "Selecione"               );
$obCmbPadrao->preencheCombo  ( $rsAlmoxarifadosPermitidos      );

$obFormulario = new Formulario;
$obFormulario->addForm            ( $obForm               );
$obFormulario->setAjuda           ("UC-03.03.02");
$obFormulario->addHidden          ( $obHdnAcao            );
$obFormulario->addTitulo          ( "Dados do Almoxarife" );
$obFormulario->addHidden          ( $obHdnCtrl            );

if ($stAcao=="incluir") {
    $obFormulario->addComponente  ( $obBscCGMAlmoxarife );
} else {
    $obFormulario->addHidden      ( $obHdnCGM           );
    $obFormulario->addComponente  ( $obLblCodigoCGM     );
}
$obFormulario->agrupaComponentes  ( array( $obRdbAtivo, $obRdbInativo ) );
$obFormulario->addTitulo          ( "Permissão nos Almoxarifados" );
$obFormulario->addComponente      ( $obCmbAlmoxarifados  );
$obFormulario->addComponente      ( $obCmbPadrao      );

if ($stAcao=="incluir") {
    $obFormulario->OK      ();
} else {
    $obFormulario->Cancelar( $stLocation );
}

    $obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
