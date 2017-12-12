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
    * Filtro de FolhaPagamentoIRRF
    * Data de Criação: 06/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterTabelaIRRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove("link");
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
$obFormulario->addTitulo                            ( "Dados para o Filtro"                             );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addComponente                        ( $obTxtDtVigencia                                  );
$obFormulario->OK();
$obFormulario->show();
?>
