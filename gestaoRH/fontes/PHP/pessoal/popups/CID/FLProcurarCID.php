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

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30865 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCID";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get("stAcao");

$stCampoNum = $request->get("campoNum");
$stCampoNom = $request->get("campoNom");
$inCodCID   = $request->get("inCodCID");

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

Sessao::remove("stLink");

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obCampoNum = new Hidden;
$obCampoNum->setName  ( "stCampoNum" );
$obCampoNum->setValue ( $stCampoNum );

$obCampoNom = new Hidden;
$obCampoNom->setName  ( "stCampoNom" );
$obCampoNom->setValue ( $stCampoNom );

$obCodCID = new Hidden;
$obCodCID->setName  ( "inCodCID" );
$obCodCID->setValue ( $inCodCID );

//Define objeto TEXTBOX para filtrar por SIGLA do CID
$obTxtFiltroSigla = new TextBox;
$obTxtFiltroSigla->setRotulo        ( "Sigla"                            );
$obTxtFiltroSigla->setTitle         ( "Informe a sigla para o CID."      );
$obTxtFiltroSigla->setName          ( "stFiltroSigla"                    );
$obTxtFiltroSigla->setId            ( "stFiltroSigla"                    );
$obTxtFiltroSigla->setSize          ( 5                            );
$obTxtFiltroSigla->setMaxLength     ( 5                            );
$obTxtFiltroSigla->setEspacosExtras ( false                        );

//Define objeto TEXTBOX para armazenar a DESCRICAO do CID
$obTxtFiltroDescricao = new TextBox;
$obTxtFiltroDescricao->setRotulo        ( "Descrição"                      );
$obTxtFiltroDescricao->setTitle         ( "Informe a descrição para o CID." );
$obTxtFiltroDescricao->setName          ( "stFiltroDescricao"                    );
$obTxtFiltroDescricao->setId            ( "stFiltroDescricao"                    );
$obTxtFiltroDescricao->setSize          ( 40                               );
$obTxtFiltroDescricao->setMaxLength     ( 80                               );
$obTxtFiltroDescricao->setEspacosExtras ( false                            );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addHidden        ( $obCampoNum                   );
$obFormulario->addHidden        ( $obCampoNom                   );
$obFormulario->addHidden        ( $obCodCID                     );
$obFormulario->addTitulo        ( "Dados para Filtro"           );
$obFormulario->addComponente    ( $obTxtFiltroSigla             );
$obFormulario->addComponente    ( $obTxtFiltroDescricao         );
$obFormulario->OK();

$obFormulario->setFormFocus($obTxtFiltroSigla->getId() );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
