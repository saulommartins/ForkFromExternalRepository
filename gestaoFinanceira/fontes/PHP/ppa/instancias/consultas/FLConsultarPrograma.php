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
    * Página de Formulario de Consultar programa

    * Data de Criação   : 29/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php"         );
include_once ( CAM_GF_PPA_COMPONENTES."ITextBoxSelectOrgao.class.php"         );
include_once ( CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php" );
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

$obControllerHomologar = new RPPAHomologarPPA;
$obVisaoHomologar = new VPPAHomologarPPA($obControllerHomologar);

$stFiltro = ' ppn.cod_ppa IS NULL ';
$rsRecordSetHomologar = $obVisaoHomologar->pesquisaPPANorma($stFiltro);

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ConsultarPrograma";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );
$obForm->settarget ( "telaPrincipal" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$rsRSHomologar = '';

if ($stAcao =='excluir') {
    $rsRSHomologar = $rsRecordSetHomologar;
}
### ITextBoxSelectPPA ###
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull( false );

//Informar código
$obTextBoxPrograma = new TextBox;
$obTextBoxPrograma->setRotulo    ('Programa');
$obTextBoxPrograma->setName      ('inNumPrograma');
$obTextBoxPrograma->setNull      (true);
$obTextBoxPrograma->setInteiro   (true);
$obTextBoxPrograma->setMaxLength (4);
$obTextBoxPrograma->setTitle     ('Escolha um codigo para programa');
$obTextBoxPrograma->setSize      (8);

//Indentificação do programa
$obTextAreaIdPrograma = new TextArea;
$obTextAreaIdPrograma->setRotulo ('Indentificação do Programa');
$obTextAreaIdPrograma->setName   ('inIdPrograma');
$obTextAreaIdPrograma->setNull   (true);

$obRdContinuo = new Radio;
$obRdContinuo->setName   ('boNatureza');
$obRdContinuo->setRotulo ('Natureza Temporal');
$obRdContinuo->setTitle  ('Informe tipo de Natureza');
$obRdContinuo->setValue  ('t');
$obRdContinuo->setLabel  ('Contínuo');
$obRdContinuo->setNull   (true);
$obRdContinuo->setChecked(true);

$obRdTemporario = new Radio;
$obRdTemporario->setName   ('boNatureza');
$obRdTemporario->setValue  ('f');
$obRdTemporario->setLabel  ('Temporário');
$obRdTemporario->setNull   (true);

$obRadioNatureza = array($obRdContinuo,$obRdTemporario);

$obSelectOrgao = new ITextBoxSelectOrgao;

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obITextBoxSelectPPA);
$obFormulario->addComponente    ($obTextBoxPrograma);
$obFormulario->agrupaComponentes($obRadioNatureza);
$obFormulario->addComponente    ($obTextAreaIdPrograma);
$obFormulario->ok();
$obFormulario->show();
