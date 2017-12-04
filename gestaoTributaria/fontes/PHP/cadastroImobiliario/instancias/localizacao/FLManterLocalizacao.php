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
    * Página de iltro para o cadastro de localização
    * Data de Criação   : 10/10/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FLManterLocalizacao.php 63826 2015-10-21 16:39:23Z arthur $

    * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"       );
//include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgFormBaixa = "FM".$stPrograma."Baixa.php";
$pgFormiCaracteristica = "FM".$stPrograma."Caracteristica.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::remove('link');
Sessao::remove('stLink');

$obRCIMNivel = new RCIMNivel;
$obRCIMNivel->listarVigencias($rsTodasVigencias);
$rsTodasVigencias->ordena("dtinicio");

$obRCIMNivel->recuperaVigenciaAtual($rsVigencia);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obCmbVigencias = new Select;
$obCmbVigencias->setName        ( 'inCodigoVigencia');
$obCmbVigencias->setId          ( 'inCodigoVigencia');
$obCmbVigencias->setNull        ( false             );
$obCmbVigencias->setRotulo      ( 'Vigência'        );
$obCmbVigencias->setCampoId     ( 'cod_vigencia'    );
$obCmbVigencias->setCampoDesc   ( 'dtinicio'        );
$obCmbVigencias->preencheCombo  ( $rsTodasVigencias );
$obCmbVigencias->setValue       ( $rsVigencia->getCampo("cod_vigencia") );
$obCmbVigencias->obEvento->setOnChange("buscaDado('SetarMascaraLocalizacao');");

$obSpnLocalizacao = new Span;
$obSpnLocalizacao->setID("spnLocalizacao");

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick    ( "submeteFiltro();" );

$onBtnLimpar = new Limpar;
$onBtnLimpar->obEvento->setOnClick( "limparFiltro()" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda ( "UC-05.01.03" );
$obFormulario->addTitulo     ( "Dados para filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addComponente ( $obCmbVigencias );
$obFormulario->addSpan       ( $obSpnLocalizacao    );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

sistemaLegado::executaFrameOculto ( "buscaDado('SetarMascaraLocalizacao');" );

?>