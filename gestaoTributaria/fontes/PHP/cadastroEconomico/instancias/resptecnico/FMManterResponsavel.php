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
    * Classe de regra de negócio para Responsavel Tecnico
    * Data de Criação: 14/04/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: FMManterResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.10  2006/09/15 14:33:35  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"         );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"          );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"   );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"                );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterResponsavel";
$pgFormInc     = "FM".$stPrograma."Inclusao.php";
$pgFormAlt     = "FM".$stPrograma."Alteracao.php";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

if (!$_REQUEST["tipoBusca"]) {
    $tipoBusca = "profissional";
}

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

//COMPONENTES PARA INCLUSAO
$obRadioProfissional = new Radio;
$obRadioProfissional->setName      ( "boTipoResponsavel" );
$obRadioProfissional->setId        ( "boTipoResponsavel" );
$obRadioProfissional->setRotulo    ( "Tipo de Responsável" );
$obRadioProfissional->setValue     ( "profissional" );
$obRadioProfissional->setLabel     ( "Profissional" );
$obRadioProfissional->setNull      ( false );
if ($tipoBusca != 'profissional') {
    $obRadioProfissional->setChecked( false );
} else {
    $obRadioProfissional->setChecked( true );
}

$obRadioEmpresa = new Radio;
$obRadioEmpresa->setName         ( "boTipoResponsavel" );
$obRadioEmpresa->setId           ( "boTipoResponsavel" );
$obRadioEmpresa->setValue        ( "empresa" );
$obRadioEmpresa->setLabel        ( "Empresa" );
$obRadioEmpresa->setNull         ( false );
if ($tipoBusca != 'empresa') {
    $obRadioEmpresa->setChecked( false );
} else {
    $obRadioEmpresa->setChecked( true );
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgFormInc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda              ( "UC-05.02.04" );
$obFormulario->addForm               ( $obForm                               );
$obFormulario->addHidden             ( $obHdnCtrl                            );
$obFormulario->addHidden             ( $obHdnAcao                            );
$obFormulario->addTitulo             ( "Dados para Responsável Técnico"      );
$obFormulario->addComponenteComposto ( $obRadioProfissional, $obRadioEmpresa );
$obFormulario->Ok();
$obFormulario->setFormFocus( $obRadioProfissional->getId() );

$obFormulario->show ();

?>
