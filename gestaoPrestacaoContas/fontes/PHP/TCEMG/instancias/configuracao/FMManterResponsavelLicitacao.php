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
    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : 04/10/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Morais

    $Id: FMManterResponsavelLicitacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso :
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_CGM_COMPONENTES.'IPopUpCGMVinculado.class.php' );
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRespLic.class.php";

//Definições padrões do framework
$stPrograma = "ManterResponsavelLicitacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgCons     = "FM".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
//include_once ($pgJs);
$arLicitacao = Sessao::read("arLicitacao");

if(!empty($arLicitacao)){
    $_REQUEST = $arLicitacao;
}

$arCGMResponsaveis = Sessao::read("arCGMResponsaveis");

$obTTCEMGRespLic = new TTCEMGRespLic;
$arEntidade = explode('-',$_REQUEST['stEntidade']); 
$arModalidade = explode('-',$_REQUEST['stModalidade']); 

$obTTCEMGRespLic = new TTCEMGRespLic;
$obTTCEMGRespLic->setDado('exercicio', $_REQUEST['stExercicioLicitacao']);
$obTTCEMGRespLic->setDado('cod_entidade', $arEntidade[0]);
$obTTCEMGRespLic->setDado('cod_modalidade', $arModalidade[0]);
$obTTCEMGRespLic->setDado('cod_licitacao', $_REQUEST['inCodLicitacao']);
$obTTCEMGRespLic->recuperaPorChave($rsRecordSet);

$obForm = new Form;
$obForm->setAction ( $pgProc );

# Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                           );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

# Define o Label do Exercicio
$obLblExercicio = new Label;
$obLblExercicio->setRotulo('Exercício da Licitação');
$obLblExercicio->setValue($_REQUEST['stExercicioLicitacao']);

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName ( "stExercicioLicitacao" );
$obHdnExercicio->setValue( $_REQUEST['stExercicioLicitacao'] );

# Define o Label para modalidade
$obLblModalidade = new Label;
$obLblModalidade->setRotulo('Modalidade');
$obLblModalidade->setValue($_REQUEST['stModalidade']);

$obHdnModalidade = new Hidden;
$obHdnModalidade->setName ( "stModalidade" );
$obHdnModalidade->setValue( $_REQUEST['stModalidade'] );

# Define o Label da licitacao
$obLblLicitação = new Label;
$obLblLicitação->setRotulo('Código da Licitação');
$obLblLicitação->setValue($_REQUEST['inCodLicitacao']);

$obHdnLicitacao = new Hidden;
$obHdnLicitacao->setName ( "inCodLicitacao" );
$obHdnLicitacao->setValue( $_REQUEST['inCodLicitacao'] );

# Define o Label da Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue($_REQUEST['stEntidade']);

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "stEntidade" );
$obHdnCodEntidade->setValue( $_REQUEST['stEntidade']);

$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName ( "inCodModalidade" );
$obHdnCodModalidade->setValue( $_REQUEST['inCodModalidade']);

$obTTCEMGRespLic->recuperaPorChave($rsRecordSet);

if(!empty($arCGMResponsaveis)){
    $arResponsabilidades = array(
                             0 => array('responsabilidade'=>'Autorização para abertura do procedimento licitatório', 'cgm_responsavel'=>$arCGMResponsaveis[1]['inNumCGM_1'], 'nom_cgm'=>$arCGMResponsaveis[1]['stNomCGM_1']),
                             1 => array('responsabilidade'=>'Emissão do edital','cgm_responsavel'=>$arCGMResponsaveis[2]['inNumCGM_2'], 'nom_cgm'=>$arCGMResponsaveis[2]['stNomCGM_2']),
                             2 => array('responsabilidade'=>'Informação de existência de recursos orçamentários', 'cgm_responsavel'=>$arCGMResponsaveis[3]['inNumCGM_3'], 'nom_cgm'=>$arCGMResponsaveis[3]['stNomCGM_3']),
                             3 => array('responsabilidade'=>'Condução do procedimento licitatório', 'cgm_responsavel'=>$arCGMResponsaveis[4]['inNumCGM_4'], 'nom_cgm'=>$arCGMResponsaveis[4]['stNomCGM_4']),
                             4 => array('responsabilidade'=>'Homologação','cgm_responsavel'=>$arCGMResponsaveis[5]['inNumCGM_5'], 'nom_cgm'=>$arCGMResponsaveis[5]['stNomCGM_5']),
                             5 => array('responsabilidade'=>'Adjudicação', 'cgm_responsavel'=>$arCGMResponsaveis[6]['inNumCGM_6'], 'nom_cgm'=>$arCGMResponsaveis[6]['stNomCGM_6']),
                             6 => array('responsabilidade'=>'Publicação em órgão Oficial', 'cgm_responsavel'=>$arCGMResponsaveis[7]['inNumCGM_7'], 'nom_cgm'=>$arCGMResponsaveis[7]['stNomCGM_7']),
                             7 => array('responsabilidade'=>'Avaliação de Bens', 'cgm_responsavel'=>$arCGMResponsaveis[8]['inNumCGM_8'], 'nom_cgm'=>$arCGMResponsaveis[8]['stNomCGM_8']),
                             8 => array('responsabilidade'=>'Pesquisa de preços', 'cgm_responsavel'=>$arCGMResponsaveis[9]['inNumCGM_9'], 'nom_cgm'=>$arCGMResponsaveis[9]['stNomCGM_9']) 
                            );
}else{
    $arResponsabilidades = array(
                             0 => array('responsabilidade'=>'Autorização para abertura do procedimento licitatório', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_abertura_licitacao'), 'nom_cgm'=>""),
                             1 => array('responsabilidade'=>'Emissão do edital','cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_edital'), 'nom_cgm'=>""),
                             2 => array('responsabilidade'=>'Informação de existência de recursos orçamentários', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_recurso_orcamentario'), 'nom_cgm'=>""),
                             3 => array('responsabilidade'=>'Condução do procedimento licitatório', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_conducao_licitacao'), 'nom_cgm'=>""),
                             4 => array('responsabilidade'=>'Homologação','cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_homologacao'), 'nom_cgm'=>""),
                             5 => array('responsabilidade'=>'Adjudicação', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_adjudicacao'), 'nom_cgm'=>""),
                             6 => array('responsabilidade'=>'Publicação em órgão Oficial', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_publicacao'), 'nom_cgm'=>""),
                             7 => array('responsabilidade'=>'Avaliação de Bens', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_avaliacao_bens'), 'nom_cgm'=>""),
                             8 => array('responsabilidade'=>'Pesquisa de preços', 'cgm_responsavel'=>$rsRecordSet->getCampo('cgm_resp_pesquisa'), 'nom_cgm'=>"") 
                            );
}

foreach($arResponsabilidades as $inIndice => $valor){
    $stFiltro = $arResponsabilidades[$inIndice]['cgm_responsavel'];
    if(!empty($stFiltro)){
        $obTTCEMGRespLic->recuperaResponsavel($rsNomCGM, $stFiltro);
        $arResponsabilidades[$inIndice]['nom_cgm'] = $rsNomCGM->arElementos[0]['nom_cgm'];
    }
}

$rsLista = new RecordSet;
$rsLista->preenche($arResponsabilidades);
                             
$obLista = new Lista;
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Responsabilidades');
$obLista->setRecordSet($rsLista);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( " " );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->setWidth( 1);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Responsabilidade" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
       
$obLista->addDado();
$obLista->ultimoDado->setCampo( 'responsabilidade' );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

//Define objeto BuscaInner para cgm
$obBscCGM = new BuscaInner;
$obBscCGM->setRotulo              ( "CGM"              );
$obBscCGM->setTitle               ( "Selecione o CGM"  );
if(($_REQUEST['inCodModalidade'] != '9') && ($_REQUEST['inCodModalidade'] != '8')){
    $obBscCGM->setNull                ( false               );
}
$obBscCGM->setName                ( 'stNomCGM'         );
$obBscCGM->setValue               ( "nom_cgm"          );
$obBscCGM->obCampoCod->setId      ( "inNumCGM"         );
$obBscCGM->obCampoCod->setSize    (  8                 );
$obBscCGM->obCampoCod->setName    ( "inNumCGM"         );
$obBscCGM->obCampoCod->setValue   ( "cgm_responsavel"  );
if(($_REQUEST['inCodModalidade'] != '9') && ($_REQUEST['inCodModalidade'] != '8')){
    $obBscCGM->obCampoCod->setNull    ( true              );
}
$obBscCGM->setValoresBusca($pgOcul."?".Sessao::getId(),$obForm->getName(),'validaCGM');
$obBscCGM->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','stNomCGM','fisica','".Sessao::getId()."','800','550');" );
    
$obLista->addCabecalho('Responsável', 8);
$obLista->addDadoComponente( $obBscCGM );
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

# Define o caminho de ajuda do Caso de uso (padrão no Framework)
$obFormulario->addTitulo     ( "Dados da Licitação" );
$obFormulario->addComponente ( $obLblEntidade       );
$obFormulario->addComponente ( $obLblExercicio      );
$obFormulario->addComponente ( $obLblModalidade     );
$obFormulario->addComponente ( $obLblLicitação      );
$obFormulario->addSpan       ( $obSpnCodigos        );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnExercicio      );
$obFormulario->addHidden     ( $obHdnModalidade     );
$obFormulario->addHidden     ( $obHdnCodEntidade    );
$obFormulario->addHidden     ( $obHdnLicitacao      );
$obFormulario->addHidden     ( $obHdnCodModalidade  );

$obBtnOK = new Ok();
$obBtnOK->obEvento->setOnClick("Salvar();");
$obBtnOK->setId('Ok');

$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick( "Limpar();" );

$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;
$obCancelar  = new Cancelar;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

$obFormulario->defineBarra(array($obBtnOK,$obLimpar,$obCancelar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>