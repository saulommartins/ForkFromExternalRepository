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
 * Titulo do arquivo : Formulário de Suspensão de Edital
 * Data de Criação   : 05/12/2008

 * @author Analista      Gelson Wolowski Gonçalves
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_LIC_COMPONENTES."IPopUpNumeroEdital.class.php" );

//Definições padrões do framework
$stPrograma = "ManterSuspensaoEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

//Numero do edital
$obPopUpNumeroEdital = new IPopUpNumeroEdital( $obForm );
$obPopUpNumeroEdital->obCampoCod->setId("num_edital");
$obPopUpNumeroEdital->obCampoCod->setName("num_edital");
$obPopUpNumeroEdital->setNull(false);
if ($stAcao == 'incluir') {
    $obPopUpNumeroEdital->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&num_edital='+this.value,'exibeEdital');");
    $obPopUpNumeroEdital->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&num_edital='+this.value,'exibeEdital');");
} elseif ($stAcao == 'anular') {
    $obPopUpNumeroEdital->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&num_edital='+this.value,'exibeEditalSuspenso');");
    $obPopUpNumeroEdital->obCampoCod->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&num_edital='+this.value,'exibeEditalSuspenso');");
}

$obSpnNumLicitacao = new Span();
$obSpnNumLicitacao->setId('spnNumeroLicitacao');

$obHdnCodLicitacao = new Hidden;
$obHdnCodLicitacao->setName("cod_licitacao");
$obHdnCodLicitacao->setValue(" ");

$obHdnCgm = new Hidden;
$obHdnCgm->setName("numcgm");
$obHdnCgm->setValue(" ");

$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName("cod_modalidade");
$obHdnCodModalidade->setValue(" ");

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName("cod_entidade");
$obHdnCodEntidade->setValue(" ");

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName("exercicio");
$obHdnExercicio->setValue(" ");

$obFormulario = new Formulario;

$obFormulario->addForm      ($obForm            );

$obFormulario->addHidden    ($obHdnCtrl         );
$obFormulario->addHidden    ($obHdnAcao         );
$obFormulario->addHidden    ($obHdnCodLicitacao );
$obFormulario->addHidden    ($obHdnCgm          );
$obFormulario->addHidden    ($obHdnCodModalidade);
$obFormulario->addHidden    ($obHdnCodEntidade  );
$obFormulario->addHidden    ($obHdnExercicio    );

$obFormulario->addTitulo    ("Suspensão de Edital");
$obFormulario->addComponente($obPopUpNumeroEdital);
$obFormulario->addSpan      ($obSpnNumLicitacao  );

$obFormulario->Ok();
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
