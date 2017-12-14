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
    * Página de Filtro para Almoxarife
    * Data de Criação   : 12/11/2005

    * @author Leandro André Zis

    * @ignore

    * Casos de uso : uc-03.03.02

    $Id: FLManterAlmoxarife.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php" );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpAlmoxarife.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarife";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$rsAlmoxarifados = new RecordSet;
$obRegra = new RAlmoxarifadoAlmoxarife;
$obRegra->obAlmoxarifadoPadrao->listar($rsAlmoxarifados);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

#sessao->link= "";
Sessao::remove('link');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obForm = new Form;
$obForm->setAction                  ( $pgList );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbAlmoxarifados = new SelectMultiplo();
$obCmbAlmoxarifados->setName   ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setRotulo ( "Selecione o Almoxarifado" );
$obCmbAlmoxarifados->setTitle  ( "Selecione os almoxarifados que o almoxarife possui permissão." );
// lista de atributos disponiveis
$obCmbAlmoxarifados->SetNomeLista1 ('inCodAlmoxarifadoDisponivel');
$obCmbAlmoxarifados->setCampoId1   ( 'codigo' );
$obCmbAlmoxarifados->setCampoDesc1 ( '[codigo]-[nom_a]' );
$obCmbAlmoxarifados->SetRecord1    ( $rsAlmoxarifados );
$rsRecordset = new RecordSet;
// lista de atributos selecionados
$obCmbAlmoxarifados->SetNomeLista2 ('inCodAlmoxarifado');
$obCmbAlmoxarifados->setCampoId2   ('codigo');
$obCmbAlmoxarifados->setCampoDesc2 ('[codigo]-[nom_a]');
$obCmbAlmoxarifados->SetRecord2    ( $rsRecordset );

$obBscCGMAlmoxarife = new IPopUpAlmoxarife ($obForm);

$obFormulario = new Formulario;
$obFormulario->addTitulo                 ( "Dados do Almoxarife" );
$obFormulario->addForm                   ( $obForm               );
$obFormulario->setAjuda                  ("UC-03.03.02");
$obFormulario->addHidden                 ( $obHdnAcao            );
$obFormulario->addComponente             ( $obCmbAlmoxarifados   );
$obFormulario->addComponente             ( $obBscCGMAlmoxarife   );

$obFormulario->Ok();
$obFormulario->Show();

?>
