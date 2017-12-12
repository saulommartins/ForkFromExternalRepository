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
    * Filtro de Manter Configuracao FGTS
    * Data de Criação: 10/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-04.05.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoFGTS";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

Sessao::write("link","");
#sessao->transf = "";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

$obTxtDtVigencia = new Data;
$obTxtDtVigencia->setName                           ( "dtVigencia"                                      );
$obTxtDtVigencia->setValue                          ( $dtVigencia                                       );
$obTxtDtVigencia->setRotulo                         ( "Vigência"                                        );
$obTxtDtVigencia->setTitle                          ( 'Informe a vigência para o filtro da busca.'      );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgList                                           );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                            ( "Dados para o Filtro"                             );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addComponente                        ( $obTxtDtVigencia                                  );
$obFormulario->OK();
$obFormulario->show();
?>
