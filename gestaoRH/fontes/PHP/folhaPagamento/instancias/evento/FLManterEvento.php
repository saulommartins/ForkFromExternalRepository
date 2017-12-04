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
* Página de Filtro Evento
* Data de Criação   : 10/02/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @ignore

$Revision: 30727 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );

$stPrograma = "ManterEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJs);

Sessao::write("link","");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Carrega máscara do codigo do evento
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obTxtCodEvento = new TextBox;
$obTxtCodEvento->setRotulo           ( "Código"                          );
$obTxtCodEvento->setTitle            ( "Informe o código do evento"      );
$obTxtCodEvento->setName             ( "stCodigo"                        );
$obTxtCodEvento->setValue            ( ""                                );
$obTxtCodEvento->setSize             ( 10                                );
$obTxtCodEvento->setMaxLength        ( 5                                 );
$obTxtCodEvento->setMascara          ( $stMascaraEvento                  );
$obTxtCodEvento->setPreencheComZeros ( 'E'                               );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo           ( "Descrição"                       );
$obTxtDescricao->setTitle            ( "Informe a descrição do evento "  );
$obTxtDescricao->setName             ( "stDescricao"                     );
$obTxtDescricao->setValue            ( $stDescricao                      );
$obTxtDescricao->setSize             ( 50                                );
$obTxtDescricao->setMaxLength        ( 80                                );

$obForm = new Form;
$obForm->setAction                   ( $pgList                           );

$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm                           );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden             ( $obHdnAcao                        );
$obFormulario->addTitulo             ( "Dados para filtro"               );
$obFormulario->addComponente         ( $obTxtCodEvento                   );
$obFormulario->addComponente         ( $obTxtDescricao                   );
$obFormulario->OK                    ();
$obFormulario->show                  ();

?>
