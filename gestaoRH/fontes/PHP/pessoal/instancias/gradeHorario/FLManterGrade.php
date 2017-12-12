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
* Definir grande de horário
* Data de Criação: 13/09/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30547 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterGrade";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setRotulo         ( "Código"                                      );
$obTxtCodigo->setTitle          ( "Informe o código da grade para filtro."       );
$obTxtCodigo->setName           ( "inCodGradeFiltro"                            );
$obTxtCodigo->setId             ( "inCodGradeFiltro"                            );
$obTxtCodigo->setValue          ( $inCodGradeFiltro                             );
$obTxtCodigo->setSize           ( 10                                            );
$obTxtCodigo->setMaxLength      ( 5                                             );
$obTxtCodigo->setInteiro        ( true                                          );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo      ( "Descrição"                                   );
$obTxtDescricao->setTitle       ( "Informe a descrição da grade para filtro."    );
$obTxtDescricao->setName        ( "stDescricaoFiltro"                           );
$obTxtDescricao->setValue       ( $stDescricaoFiltro                            );
$obTxtDescricao->setSize        ( 40                                            );
$obTxtDescricao->setMaxLength   ( 80                                            );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para Filtro"           );
$obFormulario->addComponente    ( $obTxtCodigo                  );
$obFormulario->addComponente    ( $obTxtDescricao               );
$obFormulario->OK();

$obFormulario->setFormFocus($obTxtCodigo->getId() );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
