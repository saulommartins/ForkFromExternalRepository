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
    * Pagina de formulário do Termo de Autuação de Edital
    * Data de Criação   : 13/01/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacao.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( TLIC."TLicitacaoEdital.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "TermoAutuacaoEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

// inicio da alteração devido a inclusão do filtro para acessar esta tela
$arEdital = explode('/',$_REQUEST['inNumEdital']);
$arEdital[1] = ($arEdital[1] == '') ? Sessao::getExercicio() : $arEdital[1];

include_once(TLIC."TLicitacaoEdital.class.php");
include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");

//O usuario digitou algo, agora busca no BD
$obj = new TLicitacaoEdital;
$obj->setDado("num_edital",$arEdital[0]);
$obj->setDado('exercicio',$arEdital[1]);
$obj->recuperaLicitacao($rs);

Sessao::write('rsProcesso', $rs);

$obHdnEdital = new Hidden();
$obHdnEdital->setName( 'numEdital' );
$obHdnEdital->setValue( $arEdital[0] );

$labelNumEdital = new Label();
$labelNumEdital->setId( 'inNumEdital' );
$labelNumEdital->setValue( $arEdital[0].'/'.$arEdital[1] );
$labelNumEdital->setRotulo( 'Número do Edital' );

$labelNumLicitacao = new Label();
$labelNumLicitacao->setId( 'numLicitacao' );
$labelNumLicitacao->setValue($rs->getCampo('cod_licitacao').'/'.$rs->getCampo('exercicio'));
$labelNumLicitacao->setRotulo( 'Número da Licitação' );

$labelModalidade = new Label();
$labelModalidade->setId( 'numModalidade' );
$labelModalidade->setValue($rs->getCampo('cod_modalidade').' - '.$rs->getCampo('modalidade_descricao'));
$labelModalidade->setRotulo( 'Modalidade' );

$labelEntidade = new Label();
$labelEntidade->setId('numEntidade');
$labelEntidade->setValue($rs->getCampo('cod_entidade').' - '.$rs->getCampo('nom_cgm'));
$labelEntidade->setRotulo( 'Entidade' );

//Objeto do processo licitatorio
$labelObjetoProcLic = new ILabelEditObjeto();
$labelObjetoProcLic->setCodObjeto($rs->getCampo('cod_objeto'));
$labelObjetoProcLic->setName( 'objeto' );
$labelObjetoProcLic->setRotulo( 'Objeto' );

//label para a data de aprovação do jurídico;
$labelDataAprovacao = new Label();
$labelDataAprovacao->setRotulo( 'Data de Aprovação do Jurídico' );
$labelDataAprovacao->setValue( $rs->getCampo('dt_aprovacao_juridico') );

//inclusão de assinatura
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

//Hidden para armazenar o exercicio do edital
$obExercicio = new Hidden;
$obExercicio->setName( "exercicioEdital" );
$obExercicio->setValue( $rs->getCampo('exercicio') );

//Hidden para armazenar a data de aprovação do edital
$obHdnDataEdital = new Hidden();
$obHdnDataEdital->setName( 'hdnDataEdital' );
$obHdnDataEdital->setValue( $rs->getCampo('dt_aprovacao_juridico') );

//Hidden para armazenar a entidade
$obHdnEntidade = new Hidden();
$obHdnEntidade->setName( 'inCodEntidade' );
$obHdnEntidade->setId  ( 'inCodEntidade' );

$obHdnEntidade->setValue( $_REQUEST['inCodEntidade'] );

$obHdnId = new Hidden();
$obHdnId->setId( 'hdnId' );
$obHdnId->setName( 'hdnId' );
$obHdnId->setValue( '' );

/*
 * Define o formulário
 */
$obFormulario = new Formulario;
//carrega a lista por padrao
$obFormulario->addForm          ( $obForm                        );
$obFormulario->addHidden        ( $obHdnCtrl                     );
$obFormulario->addHidden        ( $obHdnAcao                     );

$obFormulario->addTitulo( 'Termo de Autuação de Edital' );

// INCLUIDO DEVIDO A INCLUSÃO DO FILTRO ANTECEDENDO ESTA TELA
$obFormulario->addHidden($obHdnDataEdital);
$obFormulario->addHidden($obHdnId);
$obFormulario->addHidden($obHdnEdital);
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addComponente( $labelNumEdital );
$obFormulario->addComponente($labelEntidade);
$obFormulario->addComponente($labelNumLicitacao);
$obFormulario->addComponente($labelModalidade);
$obFormulario->addComponente($labelObjetoProcLic);
//$obFormulario->addComponente($labelDataAprovacao);
$obFormulario->addHidden($obExercicio);
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->Cancelar( $pgList."?".Sessao::getId()."&stAcao=".$stAcao.$stFiltro );

$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
