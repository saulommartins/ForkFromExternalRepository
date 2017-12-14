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
    * Página de Formulario Relatório de Remissão

    * Data de Criação   : 06/10/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: $

    *Casos de uso: uc-05.04.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDividaIntervalo.class.php" );
include_once ( CAM_GA_NORMAS_CLASSES."componentes/IPopUpNorma.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = 'incluir';
}

//Define o nome dos arquivos PHP
$stPrograma    = "Remissao";
$pgForm        = "FM".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove( "arListaGrupoCredito" );

$obRadioTipoCredito = new Radio;
$obRadioTipoCredito->setName   ('boTipoLancamentoManual');
$obRadioTipoCredito->setTitle  ('Informe o filtro a ser utilizado.');
$obRadioTipoCredito->setRotulo ('Filtrar por ');
$obRadioTipoCredito->setValue  ('credito');
$obRadioTipoCredito->setLabel  ('Crédito');
$obRadioTipoCredito->setNull   (false);
$obRadioTipoCredito->setChecked(true);
$obRadioTipoCredito->obEvento->setOnClick("montaParametrosGET('montaCredito')");

$obRadioTipoGrupoCredito = new Radio;
$obRadioTipoGrupoCredito->setName ('boTipoLancamentoManual');
$obRadioTipoGrupoCredito->setValue('grupo_credito');
$obRadioTipoGrupoCredito->setLabel('Grupo de Crédito');
$obRadioTipoGrupoCredito->setNull (false);
$obRadioTipoGrupoCredito->obEvento->setOnClick("montaParametrosGET('montaGrupoCredito')");

//fundamentacao legal
$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setRotulo ( "Fundamentação Legal" );
$obIPopUpNorma->obInnerNorma->setTitle ( "Fundamentação legal que regulamenta a remisão." );
$obIPopUpNorma->obInnerNorma->setNull             (  true );

$obSpnCredito = new Span;
$obSpnCredito->setId('spnCredito');

$obSpnLista = new Span;
$obSpnLista->setID("spnLista");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
if (isset($_REQUEST['stCtrl'])) {
    $obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );
}

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpImovel = new IPopUpImovelIntervalo;
$obIPopUpImovel->setVerificaInscricao(false);
$obIPopUpDividaIntervalo = new IPopUpDividaIntervalo;

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
$obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
if (isset($inCodContribuinteInicio )) {
    $obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinteInicio  );
}
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
if (isset($inCodContribuinteFinal )) {
    $obBscContribuinte->obCampoCod2->setValue       ( $inCodContribuinteFinal  );
}
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNomeGrupo','','".Sessao::getId()."','800','450');" ));

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgForm );
$obForm->settarget ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.04.10" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Filtro" );

$obIPopUpDividaIntervalo->setVerifica( false );
$obIPopUpDividaIntervalo->obInnerDividaIntervalo->setNull( true );
$obIPopUpDividaIntervalo->geraFormulario ( $obFormulario );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obBscContribuinte );
$obIPopUpNorma->geraFormulario ( $obFormulario, true, true );
$obFormulario->addComponenteComposto($obRadioTipoCredito, $obRadioTipoGrupoCredito);
$obFormulario->addSpan($obSpnCredito);
$obFormulario->addSpan($obSpnLista);

$obFormulario->ok();
$obFormulario->show();

?>
