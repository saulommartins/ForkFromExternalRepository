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
    * Página de filtro do CID
    * Data de Criação: 04/01/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-03.03.11

    $Id: FLMovimentacaoRequisicao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpCentroCusto.class.php");
include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php");
include_once( CAM_GP_ALM_COMPONENTES."ISelectMultiploAlmoxarifadoAlmoxarife.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoRequisicao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('link' , '');

$arAlmoxarifadoPadrao = array();
$rsRelacionados = new RecordSet;

$obRegra = new RAlmoxarifadoAlmoxarife();
$obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRegra->listarPermissao ( $rsDisponiveis, '', false);
$obRegra->consultar();

$inCodAlmoxarifadoPadrao = $obRegra->obAlmoxarifadoPadrao->getCodigo();
$stNomAlmoxarifadoPadrao = $obRegra->obAlmoxarifadoPadrao->obRCGMAlmoxarifado->getNomCGM();
if ($inCodAlmoxarifadoPadrao) {
   $arAlmoxarifadoPadrao[0]['codigo'] = $inCodAlmoxarifadoPadrao;
   $arAlmoxarifadoPadrao[0]['nom_a'] = $stNomAlmoxarifadoPadrao;
   $rsRelacionados->preenche($arAlmoxarifadoPadrao);

}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obSelectAlmoxarifado = new ISelectMultiploAlmoxarifadoAlmoxarife;
$obSelectAlmoxarifado->setNull(false);

$obTxtCodRequisicao = new TextBox;
$obTxtCodRequisicao->setRotulo        ( "Código da Requisição"       );
$obTxtCodRequisicao->setTitle         ( "Informe o código da requisição." );
$obTxtCodRequisicao->setName          ( "inCodRequisicao"            );
$obTxtCodRequisicao->setId            ( "inCodRequisicao"                );
$obTxtCodRequisicao->setValue         ( $inCodRequisicao             );
$obTxtCodRequisicao->setSize          ( 5                            );
$obTxtCodRequisicao->setMaxLength     ( 10                            );
$obTxtCodRequisicao->setInteiro       ( true                         );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setRotulo        ( "Observação"                      );
$obTxtObservacao->setTitle         ( "Informe a descrição"        );
$obTxtObservacao->setName          ( "stObservacao"                );
$obTxtObservacao->setId            ( "stObservacao"                );
$obTxtObservacao->setValue         ( $stObservacao);
$obTxtObservacao->setSize          ( 50                            );
$obTxtObservacao->setMaxLength     ( 160                            );

$obCmbTipoBusca = new TipoBusca ( $obTxtObservacao );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull(true);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setTitle("Informe a marca do item.");
$obBscMarca->setNull(true);

$obBscCentroCusto = new IPopUpCentroCusto($obForm);
$obBscCentroCusto->setNull(true);

$obPerDataRequisicao = new Periodicidade();
$obPerDataRequisicao->setRotulo ( "Data de Requisição" );
$obPerDataRequisicao->setTitle  ( "Informe a data de requisição." );
$obPerDataRequisicao->setName   ( "dtRequisicao"                 );
$obPerDataRequisicao->setExercicio( Sessao::getExercicio() );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda("UC-03.03.11");
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para filtro"           );
$obFormulario->addComponente ( $obSelectAlmoxarifado );
$obFormulario->addComponente    ( $obTxtCodRequisicao           );
$obFormulario->addComponente    ( $obCmbTipoBusca               );
$obFormulario->addComponente    ( $obBscItem                    );
$obFormulario->addComponente    ( $obBscMarca                   );
$obFormulario->addComponente    ( $obBscCentroCusto             );
$obFormulario->addComponente    ( $obPerDataRequisicao          );
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
