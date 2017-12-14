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
    * Página de Formulário Almoxarifado
    * Data de Criação   : 22/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.07

    $Id: FMManterCentroCusto.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php");
include_once(CAM_GF_ORC_COMPONENTES. "IPopUpDotacaoFiltroClassificacao.class.php");
include_once(CAM_GF_ORC_COMPONENTES. "ITextBoxSelectEntidadeGeral.class.php");

$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once ( $pgJs );

$obRegra = new RAlmoxarifadoCentroDeCustos;
$obRegra->roUltimaEntidade->setExercicio ( Sessao::getExercicio() );

$stAcao = $request->get('stAcao');
$inCodigo = $_REQUEST['inCodigo'];
$inCGMResponsavel = $_REQUEST['inCGMResponsavel'];
$stDescricao = $_REQUEST['stDescricao'];

if (empty( $stAcao )) {
    $stAcao = "incluir";
}
$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodigo = new Hidden;
$obHdnCodigo->setName( "inCodigo" );
$obHdnCodigo->setValue( $inCodigo );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stAcao == "alterar") {
   $obLblCodigo = new Label;
   $obLblCodigo->setRotulo ( "Código"   );
   $obLblCodigo->setName   ( "inCodigo" );
   $obLblCodigo->setValue  ( $inCodigo  );
}

Sessao::write('arDotacoes', array());

$dtDataVigencia = $dtDataVigencia ? $dtDataVigencia : '31/12/'.Sessao::getExercicio();

$obRegra->obRCGMResponsavel->setNumCGM ( $inCGMResponsavel );
$rsRecordSet = new RecordSet();
$obRegra->obRCGMResponsavel->consultar ( $rsRecordSet );
$stNomCGMResponsavel = $obRegra->obRCGMResponsavel->getNomCGM();

if ($_REQUEST['inCodigo']) {
    $obRegra->setCodigo($inCodigo);
    $obRegra->consultar();

    $inCodigo = $obRegra->getCodigo();

    $inCount = 0;
    $arDotacoes = array();
    for ($inPos = 0; $inPos < count($obRegra->arDotacoes); $inPos++) {
        $arElementos[$inCount]['id']              = $inPos+1;
        $arElementos[$inCount]['cod_despesa']     = $obRegra->arDotacoes[$inPos]->getCodDespesa();
        $arElementos[$inCount]['descricao']       = $obRegra->arDotacoes[$inPos]->getDescricao();
        $obRegra->arDotacoes[$inCount]->setExercicio( Sessao::getExercicio() );
        $obRegra->arDotacoes[$inCount]->listarDespesa($rsDespesa);
        $arElementos[$inCount]['dotacao']         = $rsDespesa->getCampo('dotacao');
        $inCount++;
    }

    Sessao::write('arDotacoes', $arElementos);
}

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo ( "Descrição"  );
$obTxtDescricao->setName   ( "stDescricao" );
$obTxtDescricao->setSize ( 50 );
$obTxtDescricao->setMaxLength ( 160 );
$obTxtDescricao->setTitle ( "Informe a descrição do centro de custo." );
$obTxtDescricao->setValue  ( $stDescricao );
$obTxtDescricao->setNull ( false );

//Define o objeto SELECT para entidade

$inCodEntidade = (isset($_REQUEST["inCodEntidade"]) ? $_REQUEST["inCodEntidade"] : "");

$obCmbEntidade = new ITextBoxSelectEntidadeGeral;
$obCmbEntidade->setCodEntidade($inCodEntidade);
$obCmbEntidade->setNull ( false );

$obBscCGMResponsavel = new IPopUpCGM($obForm);
$obBscCGMResponsavel->setId                    ('stNomCGMResponsavel');
$obBscCGMResponsavel->setRotulo                ( 'Responsável'       );
$obBscCGMResponsavel->setTipo                  ('fisica'           );
$obBscCGMResponsavel->setTitle                ( 'Informe o CGM relacionado ao responsável.');
$obBscCGMResponsavel->setValue                 ( $stNomCGMResponsavel);
$obBscCGMResponsavel->obCampoCod->setName      ( 'inCGMResponsavel' );
$obBscCGMResponsavel->obCampoCod->setSize      (10);
$obBscCGMResponsavel->obCampoCod->setValue     ( $inCGMResponsavel   );

$obTxtVigencia = new Data;
$obTxtVigencia->setRotulo        ( "Data de Vigência" );
$obTxtVigencia->setTitle         ( "Informe a data de vigência." );
$obTxtVigencia->setName          ( "dtDataVigencia" );
$obTxtVigencia->setValue         ( $dtDataVigencia  );

$obIPopUpDotacao = new IPopUpDotacaoFiltroClassificacao($obCmbEntidade);

//Define o objeto BUTTON para incluir permissões
$obBtnIncluir= new Button;
$obBtnIncluir->setValue( "Incluir") ;
$obBtnIncluir->obEvento->setOnClick( 'incluirDotacao();');

//Define Span para DataGrid
$obSpnDotacoes = new Span;
$obSpnDotacoes->setId ( "spnListaDotacoes" );

SistemaLegado::executaFramePrincipal("buscaDado('montaListaDotacoes');");

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->setAjuda             ("UC-03.03.07");
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCodigo);
$obFormulario->addHidden            ($obHdnCtrl);

$obFormulario->addTitulo            ( "Dados do Centro de Custo" );

if ($stAcao == "alterar") {
  $obFormulario->addComponente      ($obLblCodigo  );
}

$obFormulario->addComponente        ($obTxtDescricao);

$obFormulario->addComponente        ($obCmbEntidade);

$obFormulario->addComponente        ($obBscCGMResponsavel);

$obFormulario->addComponente        ($obTxtVigencia);

$obFormulario->addTitulo            ( "Dotações" );

$obFormulario->addComponente        ($obIPopUpDotacao);

$obFormulario->defineBarra          (array($obBtnIncluir));

$obFormulario->addSpan              ($obSpnDotacoes);

if ($stAcao=="incluir") {
    $obFormulario->OK      ();
} else {
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
