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
    Página de formulário de filtragem da Localização
    Data de criação : 27/03/2006

    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo D. Schreiner

    * @ignore

    * Casos de uso: uc-03.03.14

    $Id: FLManterLocalizacao.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoLocalizacao.class.php"                               );

$stPrograma = "ManterLocalizacao";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

include_once($pgJs);

$inCount = 0;

Sessao::write('link', '');

$obAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao();
$rsAlmoxarifado            = new Recordset;

$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCgm(Sessao::read('numCgm'));
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->listarPermissao($rsAlmoxarifado,"",true);
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->consultar();
$codAlmoxarifadoPadrao = $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo();

$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget("oculto");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obHdnVal = new Hidden;
$obHdnVal->setName("stVal");
$obHdnVal->setValue("");

$obHdnVal = new Hidden;
$obHdnVal->setName("stAcao");
$obHdnVal->setValue($stAcao);

$obHdnNomeUnidade = new Hidden;
$obHdnNomeUnidade->setName("HdnNomUnidade");
$obHdnNomeUnidade->setValue("");

$obCmbCodAlmoxarifado = new Select();
$obCmbCodAlmoxarifado->setRotulo            ("Almoxarifado"                          );
$obCmbCodAlmoxarifado->setTitle             ("Selecione o almoxarifado."             );
$obCmbCodAlmoxarifado->setName              ("inCodAlmoxarifado"                     );
$obCmbCodAlmoxarifado->setNull              (false                                   );
$obCmbCodAlmoxarifado->setCampoID           ("codigo"                                );
$obCmbCodAlmoxarifado->addOption            ("","Selecione"                          );
$obCmbCodAlmoxarifado->obEvento->setOnChange("goOcultoListagem('LMontaLocalizacao');");
$obCmbCodAlmoxarifado->setCampoDesc         ("[codigo] - [nom_a]"                    );
$obCmbCodAlmoxarifado->preencheCombo        ($rsAlmoxarifado                         );
$obCmbCodAlmoxarifado->setValue             ($codAlmoxarifadoPadrao                  );

Sessao::write('rsAlmoxarifado' , $rsAlmoxarifado);

if (!($codAlmoxarifadoPadrao == "")) {
 $js = "goOcultoListagem('LMontaLocalizacao');";
 SistemaLegado::executaFrameOculto($js);
}

$obSpnListaLocalizacao = new Span;
$obSpnListaLocalizacao->setId("spnListaLocalizacao");

$obFormulario = new Formulario;

$obFormulario->addTitulo    ("Dados da Localização" );
$obFormulario->addForm      ($obForm                );
$obFormulario->setAjuda     ("UC-03.03.14");
$obFormulario->addComponente($obCmbCodAlmoxarifado  );
$obFormulario->addSpan      ($obSpnListaLocalizacao );
$obFormulario->addHidden    ($obHdnCtrl             );
$obFormulario->addHidden    ($obHdnVal              );

$obFormulario->OK();
$obFormulario->show();

?>
