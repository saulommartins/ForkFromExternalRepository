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
/*
    * Classe do componente de data com calendário
    * Data de Criação   : 03/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$campoNum            = $_REQUEST['campoNum'];
$campoNom            = $_REQUEST['campoNom'];
if (isset($_GET["dtData"]) and trim($_GET["dtData"]) != "") {
    $stFncJavaScript = "jQuery(window.opener.parent.frames['telaPrincipal'].document).find('#".$campoNum."').attr('value','".trim($_GET["dtData"])."');";
    echo "<script>".$stFncJavaScript."close();</script>";
}

include_once(CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriado.class.php");
$obTCalendarioFeriado = new TCalendarioFeriado();
$obTCalendarioFeriado->setDado("ano",Sessao::getExercicio());
$obTCalendarioFeriado->recuperaRelacionamento($rsFeriados);

$obCalendario = new Calendario();
$obCalendario->setComplementoLink("&campoNum=".$campoNum."&campoNom=".$campoNom);
$obCalendario->setRsFeriados($rsFeriados);
$obCalendario->montaCalendario(Sessao::getExercicio());

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addFormulario($obCalendario);
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
