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
    * Formulário para vínculo Empenho-Convênio.
    * Data de Criação: 17/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.38

    $Id: FMManterVinculoEmpenhoConvenio.php 63268 2015-08-11 14:44:43Z jean $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_COMPONENTES."IPopUpEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$jsOnload = "executaFuncaoAjax('consultaEmpenhoConvenio');";

$arConvenio = array();
$arConvenio['num_convenio'] = $_REQUEST['numConvenio'];
$arConvenio['exercicio']    = $_REQUEST['exercicio'];
Sessao::write('convenio', $arConvenio);

$stLocation = $pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao;

//Consulta participantes
include_once( CAM_GP_LIC_MAPEAMENTO."TLicitacaoParticipanteConvenio.class.php" );
$obTLicitacaoParticipanteConvenio = new TLicitacaoParticipanteConvenio();
$obTLicitacaoParticipanteConvenio->setDado('num_convenio', $_REQUEST['numConvenio']);
$obTLicitacaoParticipanteConvenio->setDado('exercicio', $_REQUEST['exercicio']);
$stOrder   = " ORDER BY sw_cgm.nom_cgm ";
$obTLicitacaoParticipanteConvenio->recuperaParticipanteConvenio($rsParticipantes, "", $stOrder);

//Componentes
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnNumConvenio = new Hidden;
$obHdnNumConvenio->setName ( "numConvenio" );
$obHdnNumConvenio->setValue( $_REQUEST['numConvenio'] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST['exercicio'] );

$obLblConvenio = new Label;
$obLblConvenio->setRotulo  ( "Convênio" );
$obLblConvenio->setValue   (  $_REQUEST['numConvenio']."/".$_REQUEST['exercicio'] );

$obLblAssinatura = new Label;
$obLblAssinatura->setRotulo  ( "Assinatura" );
$obLblAssinatura->setValue   ( $_REQUEST['dt_assinatura'] );

$obLblVigencia = new Label;
$obLblVigencia->setRotulo  ( "Vigência" );
$obLblVigencia->setValue   ( $_REQUEST['dt_vigencia'] );

require_once( CAM_GF_EMP_NEGOCIO . 'REmpenhoEmpenho.class.php' );
$obEmpenho = new REmpenhoEmpenho();
$obEmpenho->buscaProximoCod();
$inTamanho = strlen( $obEmpenho->inCodEmpenho  );
$stMascara = str_pad( '' , $inTamanho , "9");
$stMascara .= "/9999";

$obLblEmpenho = new BuscaInner;
$obLblEmpenho->setRotulo               ( "Empenho"      );
$obLblEmpenho->setId                   ( "stEmpenho"    );
$obLblEmpenho->setValue                ( $stEmpenho     );
$obLblEmpenho->setMostrarDescricao     ( false          );
$obLblEmpenho->obCampoCod->setName     ( "numEmpenho"   );
$obLblEmpenho->obCampoCod->setValue    ( $numEmpenho    );
$obLblEmpenho->obCampoCod->setMascara  ( $stMascara     );
$obLblEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','&dtVigencia=".$_REQUEST['dt_vigencia']."','".Sessao::getId()."','800','450');");

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir" 	);
$obBtnIncluir->setName				( "btnIncluir"  );
$obBtnIncluir->setId				( "btnIncluir"  );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenho','inCodEntidade, inCodEmpenho, numConvenio, stExercicio', false);" );

$obBtnLimpar = new Button;
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar');" );

$obSpnListaEmpenhos = new Span;
$obSpnListaEmpenhos->setID ( "spnListaEmpenhos" );

/* Botoes */
$obBtnCancelar = new Button;
$obBtnCancelar->setName                    ( "btnClean"       				   );
$obBtnCancelar->setValue                   ( "Cancelar"         			   );
$obBtnCancelar->setTipo                    ( "button"         				   );
$obBtnCancelar->setDisabled                ( false            				   );
$obBtnCancelar->obEvento->setOnClick       ( "Cancelar('".$stLocation."');"	   );
$obBtnOK = new Ok;
$botoesForm = array ( $obBtnOK , $obBtnCancelar );

//Instancia o formulario
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obEmpenho = new IPopUpEmpenho($obForm);
$obEmpenho->setMostrarDescricao(false);
$obEmpenho->obCampoCod->obEvento->setOnChange($obEmpenho->obCampoCod->obEvento->getOnChange()." $('btnIncluir').disabled = false;");
$obEmpenho->obITextBoxSelectEntidadeUsuario->setCodEntidade(Sessao::getEntidade());
$obEmpenho->obITextBoxSelectEntidadeUsuario->obTextBox->setDisabled(false);
$obEmpenho->obITextBoxSelectEntidadeUsuario->obSelect->setDisabled(false);
$obEmpenho->obHdnCodEntidade->setValue(Sessao::getEntidade());

//Monta o formulario
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Vinculação de Empenhos a um Convênio" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnNumConvenio );
$obFormulario->addComponente( $obLblConvenio );
$obFormulario->addComponente( $obLblAssinatura );
$obFormulario->addComponente( $obLblVigencia );
$obFormulario->addTitulo( "Participantes do Convênio" );

// Faz o while para montar as labels dos participantes
while ( !$rsParticipantes->eof() ) {
    $obLblParticipantes = new Label;
    $obLblParticipantes->setValue  ( $rsParticipantes->getCampo('cgm_fornecedor').' - '.$rsParticipantes->getCampo('nom_cgm') );
    $obFormulario->addComponente( $obLblParticipantes );
    $rsParticipantes->proximo();
}

$obFormulario->addTitulo( "Empenho" );
$obEmpenho->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"","" );
$obFormulario->addSpan( $obSpnListaEmpenhos );
$obFormulario->defineBarra( $botoesForm );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
