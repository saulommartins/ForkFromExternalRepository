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
    * Pagina de formulário para Cadastro de Comissão de licitação
    * Data de Criação   : 28/08/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: FMManterComissaoLicitacao.php 62654 2015-05-29 12:59:20Z evandro $

    * Casos de uso: uc-03.05.09
*/

include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php' );
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoTipoComissao.class.php'                              );
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoTipoMembro.class.php'                                );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormaDataTermino.class.php"                                 );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNormasNorma.class.php"                                      );
include_once ( CAM_GP_LIC_MAPEAMENTO. 'TLicitacaoNaturezaCargo.class.php'                             );

//Define o nome dos arquivos PHP
$stPrograma = "ManterComissaoLicitacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

include_once ($pgOcul);
include_once ($pgJS);

$stAcao            = $request->get('stAcao');
$stCtrl            = $request->get('stCtrl');
$inCodNorma        = $request->get('cod_norma');
$inCodComissao     = $request->get('cod_comissao');
$inCodTipoComissao = $request->get('cod_tipo_comissao');

if ($stAcao == 'alterar') {
    $obNormaDataTermino = new TNormaDataTermino;
    $obNormaDataTermino->setDado('cod_norma', $inCodNorma);
    $obNormaDataTermino->consultar();
    $dtTermino = $obNormaDataTermino->getDado('dt_termino');

    $obErro = new Erro;

    if ($dtTermino) {
        if (SistemaLegado::comparaDatas(date( 'd/m/Y' ), $dtTermino)) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", 'Esta comissão não pode ser alterada porque sua vigência já expirou.' ,"","erro", Sessao::getId(), "../");
        }
    }
}

Sessao::write('arMembros',array());
Sessao::write('inPosAlteracao', -1);
Sessao::write('arMembrosExcluidos', array());

if (( $stAcao == 'alterar' ) or ( $stAcao == 'consultar')) {
   $stFinalidade = $_REQUEST['cod_tipo_comissao'];
   $inCodNorma   = $_REQUEST['cod_norma'];
}

$obForm = new Form;
if ($stAcao == 'consultar') {
    $obForm->setAction( $pgList         );
    $obForm->setTarget( "telaPrincipal" );
} else {
    $obForm->setAction( $pgProc         );
    $obForm->setTarget( "oculto"        );
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName                     ( "stAcao"             );
$obHdnAcao->setValue                    ( $stAcao              );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName                     ( "stCtrl"             );
$obHdnCtrl->setValue                    ( $stCtrl              );

$obHdnDtDesignacao = new Hidden;
$obHdnDtDesignacao->setName             ( "obDtDesignacao"     );
$obHdnDtDesignacao->setValue            ( ""                   );

$obHdnInCodNorma = new Hidden;
$obHdnInCodNorma->setName               ( "inCodNorma"         );
$obHdnInCodNorma->setValue              ( $inCodNorma          );

if (($stAcao == 'alterar') or ($stAcao == 'consultar')) {
    $obLblCodigoComissao = new Label;
    $obLblCodigoComissao->setRotulo     ( "Código da Comissão" );
    $obLblCodigoComissao->setValue      ( $inCodComissao       );

    $obHdnCodigoComissao = new Hidden;
    $obHdnCodigoComissao->setName       ( 'stCodigoComissao'   );
    $obHdnCodigoComissao->setValue      ( $inCodComissao       );

    $obHdnCodTipoComissao = new Hidden;
    $obHdnCodTipoComissao->setName      ( 'inCodTipoComissao'  );
    $obHdnCodTipoComissao->setValue     ( $inCodTipoComissao   );
}

$obTTipoComissao = new TLicitacaoTipoComissao;
if ($stAcao == 'incluir') {
    $obTTipoComissao->recuperaTodos($rsTiposComissao);
    $obCmbFinalidade = new Select;
    $obCmbFinalidade->setRotulo             ( 'Finalidade da Comissão'                                                                 );
    $obCmbFinalidade->setTitle              ( 'Selecione a finalidade da comissão a ser cadastrada.'                                   );
    $obCmbFinalidade->setName               ( "stFinalidade"                                                                           );
    $obCmbFinalidade->setId                 ( "stFinalidade"                                                                           );
    $obCmbFinalidade->setValue              ( $stFinalidade                                                                            );
    $obCmbFinalidade->setStyle              ( "width: 200px"                                                                           );
    $obCmbFinalidade->addOption             ( "", "Selecione"                                                                          );

    while (!$rsTiposComissao->eof()) {
        $obCmbFinalidade->addOption         ( $rsTiposComissao->getCampo('cod_tipo_comissao'), $rsTiposComissao->getCampo('descricao') );
        $rsTiposComissao->proximo();
    }

    $obCmbFinalidade->setNull               ( false                                                                                    );
    $obCmbFinalidade->obEvento->setOnChange ( "montaParametrosGET('montaSpanTipoMembro', 'stFinalidade' );"                            );
    $obCmbFinalidade->setDisabled           ( $stAcao != 'incluir'                                                                     );
} else {
    $obTTipoComissao->setDado               ( 'cod_tipo_comissao', $inCodTipoComissao                                                  );
    $obTTipoComissao->consultar();

    $obCmbFinalidade = new Label;
    $obCmbFinalidade->setRotulo             ( "Finalidade da Comissão"                                                                 );
    $obCmbFinalidade->setId                 ( 'stFinalidade'                                                                           );
    $obCmbFinalidade->setValue              ( $obTTipoComissao->getDado( 'descricao')                                                  );

    $obNormaDataTermino = new TNormaDataTermino;
    $obNormaDataTermino->setDado            ('cod_norma',  $inCodNorma                                                                 );
    $obNormaDataTermino->consultar();

    $dtVigencia = $obNormaDataTermino->getDado ( 'dt_termino' );
}

$stDataDesignacaoComissao = '';

if ($stAcao == 'consultar' || $stAcao == 'alterar') {
    include_once( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php" );
    $obRNorma = new RNorma;
    $obRNorma->setCodNorma         ( $inCodNorma            );
    $obRNorma->setExercicio        ( Sessao::getExercicio() );
    $obErro = $obRNorma->consultar ( $rsRecordSet           );

    $stNorma  = $obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRNorma->getNumNorma();
    $stNorma .= '/'.$obRNorma->getExercicio().' - '.$obRNorma->getNomeNorma();

    $obBscAtoDesignacao = new Label;
    $obBscAtoDesignacao->setRotulo ( 'Número do Ato de Designação' );
    $obBscAtoDesignacao->setValue  ( $inCodNorma." - ".$stNorma    );

    $obTNormasNorma = new TNormasNorma;
    $obTNormasNorma->setDado         ('cod_norma',  $inCodNorma );
    $obTNormasNorma->recuperaPorChave($rsNorma);

    $stDataDesignacaoComissao = $rsNorma->getCampo('dt_assinatura');

} else {
    $obBscAtoDesignacao = new BuscaInner;
    $obBscAtoDesignacao->setMonitorarCampoCod(true);
    $obBscAtoDesignacao->setRotulo               ( "Número do Ato de Designação" );
    $obBscAtoDesignacao->setTitle                ( "Selecione, no Normas, o número do ato jurídico de designação da comissão ( Decreto, Portaria, etc." );
    $obBscAtoDesignacao->setNull                 ( false                         );
    $obBscAtoDesignacao->setId                   ( "stAtoDesignacao"             );
    $obBscAtoDesignacao->setValue                ( $stAtoDesignacao              );
    $obBscAtoDesignacao->obCampoCod->setName     ( "inCodNorma"                  );
    $obBscAtoDesignacao->obCampoCod->setId       ( "inCodNorma"                  );
    $obBscAtoDesignacao->obCampoCod->setSize     ( 10                            );
    $obBscAtoDesignacao->obCampoCod->setMaxLength( 7                             );
    $obBscAtoDesignacao->obCampoCod->setValue    ( $inCodNorma                   );
    $obBscAtoDesignacao->obCampoCod->setAlign    ( "left"                        );
    $obBscAtoDesignacao->obCampoCod->setReadOnly ( $stAcao != 'incluir'          );
    $obBscAtoDesignacao->obCampoCod->obEvento->setOnChange( "montaParametrosGET('buscaNorma','inCodNorma');" );
    $obBscAtoDesignacao->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stAtoDesignacao','','".Sessao::getId()."','800','550');");
}

$obTxtDataDesignacaoComissao = new Label;
$obTxtDataDesignacaoComissao->setRotulo( "Data de Designação da Comissão" );
$obTxtDataDesignacaoComissao->setId    ( "stDataDesignacaoComissao"       );
$obTxtDataDesignacaoComissao->setName  ( "stDataDesignacaoComissao"       );
$obTxtDataDesignacaoComissao->setValue ( $stDataDesignacaoComissao        );

$obTxtDataVigencia = new Label;
$obTxtDataVigencia->setName     ( "dtVigencia" );
$obTxtDataVigencia->setId       ( "dtVigencia" );
$obTxtDataVigencia->setValue    ( $dtVigencia  );
$obTxtDataVigencia->setRotulo   ( "Vigência"   );

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTipo             ( "fisica" );
$obIPopUpCGM->setTitle            ( "Informe o CGM relacionado ao Membro da Comissão." );
$obIPopUpCGM->setObrigatorioBarra ( true     );
$obIPopUpCGM->setNull             ( true     );

$obSpnTipoMembro = new Span;
$obSpnTipoMembro->setID  ( "spnTipoMembro" );

$obHdnNomCGM = new Hidden();
$obHdnNomCGM->setName('hdnNomCGM');
$obHdnNomCGM->setID  ('hdnNomCGM');

$obHdnId = new Hidden();
$obHdnId->setName('hdnId');
$obHdnId->setID  ('hdnId');

if ($stAcao == 'alterar') {
    switch ($stFinalidade) {
    case '1':
        $stFiltro = ' where cod_tipo_membro in ( 1, 2 ) ';
        break;
    case '2':
        $stFiltro = '';
        break;
    case '3':
        $stFiltro = ' where cod_tipo_membro in ( 1, 3 ) ';
        break;
    case '4':
        $stFiltro = ' where cod_tipo_membro = 1 ';
        break;
    }

    $obTTipoMembro = new TLicitacaoTipoMembro;
    $obTTipoMembro->recuperaTodos( $rsTiposMembro, $stFiltro );

    $obCmbTipoMembro = new Select;
    $obCmbTipoMembro->setRotulo  ( 'Tipo do Membro'                                        );
    $obCmbTipoMembro->setTitle   ( 'Selecione o tipo do membro que está sendo cadastrado.' );
    $obCmbTipoMembro->setName    ( "stTipoMembro"                                          );
    $obCmbTipoMembro->setId      ( "stTipoMembro"                                          );
    $obCmbTipoMembro->setValue   ( $stTipoMembro                                           );
    $obCmbTipoMembro->setStyle   ( "width: 200px"                                          );

    while (!$rsTiposMembro->eof()) {
        $obCmbTipoMembro->addOption ( $rsTiposMembro->getCampo( 'cod_tipo_membro' ) , $rsTiposMembro->getCampo ( 'descricao' ) );
        $rsTiposMembro->proximo();
    }
}

$obBscAtoDesignacaoMembro = new BuscaInner;
$obBscAtoDesignacaoMembro->setMonitorarCampoCod     ( true                            );
$obBscAtoDesignacaoMembro->setRotulo                ( "Número do Ato de Designação"   );
$obBscAtoDesignacaoMembro->setTitle                 ( "Selecione, no Normas, o número do ato jurídico de designação da comissão ( Decreto, Portaria, etc." );
$obBscAtoDesignacaoMembro->setNull                  ( false                           );
$obBscAtoDesignacaoMembro->setId                    ( "stAtoDesignacaoMembro"         );
$obBscAtoDesignacaoMembro->setName                  ( "stAtoDesignacaoMembro"         );
$obBscAtoDesignacaoMembro->setValue                 ( $stAtoDesignacaoMembro          );
$obBscAtoDesignacaoMembro->obCampoCod->setName      ( "inCodNormaMembro"              );
$obBscAtoDesignacaoMembro->obCampoCod->setId        ( "inCodNormaMembro"              );
$obBscAtoDesignacaoMembro->obCampoCod->setSize      ( 7                               );
$obBscAtoDesignacaoMembro->obCampoCod->setMaxLength ( 7                               );
$obBscAtoDesignacaoMembro->obCampoCod->setValue     ( $inCodNormaMembro               );
$obBscAtoDesignacaoMembro->obCampoCod->setAlign     ( "left"                          );
$obBscAtoDesignacaoMembro->obCampoCod->obEvento->setOnChange( "montaParametrosGET('buscaNormaMembro','inCodNormaMembro');");

$obBscAtoDesignacaoMembro->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNormaMembro','stAtoDesignacaoMembro','','".Sessao::getId()."','800','550');");
$obBscAtoDesignacaoMembro->setObrigatorioBarra ( true );
$obBscAtoDesignacaoMembro->setNull             ( true );

$obTxtDataDesignacaoMembro = new Label;
$obTxtDataDesignacaoMembro->setRotulo   ( "Data de Designação do Membro" );
$obTxtDataDesignacaoMembro->setId       ( "stDataDesignacaoMembro"       );
$obTxtDataDesignacaoMembro->setName     ( "stDataDesignacaoMembro"       );
$obTxtDataDesignacaoMembro->setValue    ( ""                             );

$obHdnDataDesignacaoMembro = new Hidden;
$obHdnDataDesignacaoMembro->setName     ( "hdDataDesignacaoMembro"       );
$obHdnDataDesignacaoMembro->setId       ( "hdDataDesignacaoMembro"       );

$obTxtDataVigenciaMembro = new Label;
$obTxtDataVigenciaMembro->setName       ( "dtVigenciaMembro"             );
$obTxtDataVigenciaMembro->setId         ( "dtVigenciaMembro"             );
$obTxtDataVigenciaMembro->setValue      ( ""                             );
$obTxtDataVigenciaMembro->setRotulo     ( "Vigência"                     );

$obTxtCargo = new TextBox;
$obTxtCargo->setRotulo     ( "*Cargo do membro"            );
$obTxtCargo->setTitle      ( "Informe o cargo do membro." );
$obTxtCargo->setName       ( "stCargoMembro"              );
$obTxtCargo->setId         ( "stCargoMembro"              );
$obTxtCargo->setValue      ( $stCargoMembro               );
$obTxtCargo->setMaxLength  ( 50                           );
$obTxtCargo->setSize       ( 50                           );

$obHdnCargo= new Hidden;
$obHdnCargo->setId    ( "hdnCargo"      );
$obHdnCargo->setName  ( "hdnCargo"      );

$obTNaturezaCargo = new TLicitacaoNaturezaCargo;
$obTNaturezaCargo->recuperaTodos( $rsNaturezaCargo );

$obCmbNaturezaCargo = new Select;
$obCmbNaturezaCargo->setRotulo    ( "*Natureza do Cargo"                 );
$obCmbNaturezaCargo->setTitle     ( 'Selecione a Natureza do Cargo.'    );
$obCmbNaturezaCargo->setName      ( "inNaturezaCargo"                   );
$obCmbNaturezaCargo->setId        ( "inNaturezaCargo"                   );
$obCmbNaturezaCargo->setStyle     ( "width: 200px"                      );
$obCmbNaturezaCargo->addOption    ( "","Selecione"                      );
$obCmbNaturezaCargo->setCampoId   ("codigo");
$obCmbNaturezaCargo->setCampoDesc ( "[codigo] - [descricao]");
$obCmbNaturezaCargo->preencheCombo( $rsNaturezaCargo                    );

$obHdnNaturezaCargo= new Hidden;
$obHdnNaturezaCargo->setId    ( "hdnNaturezaCargo"      );
$obHdnNaturezaCargo->setName  ( "hdnNaturezaCargo"      );

$obHdnhdStAtoDesignacaoMembro = new Hidden;
$obHdnhdStAtoDesignacaoMembro->setId    ( "hdStAtoDesignacaoMembro"      );
$obHdnhdStAtoDesignacaoMembro->setName  ( "hdStAtoDesignacaoMembro"      );

$obHdnDtVigenciaMembro = new Hidden;
$obHdnDtVigenciaMembro->setId           ( "hdDtVigenciaMembro"           );
$obHdnDtVigenciaMembro->setName         ( "hdDtVigenciaMembro"           );

$obHdnAtoDesc = new Hidden;
$obHdnAtoDesc->setId                    ( "hdAtoDesc"                    );
$obHdnAtoDesc->setName                  ( "hdAtoDesc"                    );

$obSpnListaMembros = new Span;
$obSpnListaMembros->setId               ( 'spnListaMembros'              );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm);
$obFormulario->addHidden( $obHdnAcao                    );
$obFormulario->addHidden( $obHdnCtrl                    );
$obFormulario->addHidden( $obHdnhdStAtoDesignacaoMembro );
$obFormulario->addHidden( $obHdnDataDesignacaoMembro    );
$obFormulario->addHidden( $obHdnAtoDesc                 );
$obFormulario->addHidden( $obHdnDtVigenciaMembro        );
$obFormulario->addHidden( $obHdnDtDesignacao            );
$obFormulario->addHidden( $obHdnNaturezaCargo           );
$obFormulario->addHidden( $obHdnCargo                   );

if (($stAcao == 'alterar') or ($stAcao == 'consultar')) {
    $obFormulario->addHidden( $obHdnInCodNorma );
}

$obFormulario->addTitulo( 'Dados da Comissão'           );

if ($inCodComissao) {
    $obFormulario->addHidden     ( $obHdnCodigoComissao );
}

if ($obLblCodigoComissao) {
    $obFormulario->addComponente ( $obLblCodigoComissao );
}

$obFormulario->addComponente ( $obCmbFinalidade                );
$obFormulario->addComponente ( $obBscAtoDesignacao             );
$obFormulario->addHidden     ( $obHdnNomCGM                    );
$obFormulario->addHidden     ( $obHdnId		               );
$obFormulario->addComponente ( $obTxtDataDesignacaoComissao    );
$obFormulario->addComponente ( $obTxtDataVigencia              );

if ($stAcao != 'consultar') {
    $obFormulario->addTitulo         ( 'Dados dos Membros da Comissão' );
    $obFormulario->addComponente     ( $obIPopUpCGM                    );
    if ($stAcao == 'alterar') {
        $obFormulario->addHidden     ( $obHdnCodTipoComissao           );
        $obFormulario->addComponente ( $obCmbTipoMembro                );
    }
    $obFormulario->addSpan           ( $obSpnTipoMembro                );
    $obFormulario->addComponente     ( $obBscAtoDesignacaoMembro       );
    $obFormulario->addComponente     ( $obTxtDataDesignacaoMembro      );
    $obFormulario->addComponente     ( $obTxtDataVigenciaMembro        );
    $obFormulario->addComponente     ( $obTxtCargo                     );
    $obFormulario->addComponente     ( $obCmbNaturezaCargo             );

    $obFormulario->Incluir('incluiMembro', array( $obIPopUpCGM,  $obTxtDataDesignacaoMembro, $obBscAtoDesignacaoMembro, $obTxtDataVigenciaMembro, $obTxtCargo,  $obCmbNaturezaCargo ) );
}

$obFormulario->addSpan ( $obSpnListaMembros );

$stFiltro = "";
$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

if ($stAcao == 'consultar') {
    $obFormulario->Voltar( $stLocation  );
} else {
    if ($stAcao == 'alterar') {
        $obFormulario->Cancelar( $stLocation );
    } else {
        $obFormulario->ok();
    }
}

SistemaLegado::executaFrameOculto( montaSpanTipoMembro( $stFinalidade) );

if (($stAcao == 'alterar') or ($stAcao == 'consultar')) {
        preencheListaMembros($_REQUEST['cod_comissao']) ;
        SistemaLegado::executaFrameOculto(buscaNorma($_REQUEST['cod_norma']));
}

SistemaLegado::executaFrameOculto( montaSpanMembros($stAcao) );

if ($_GET['stAcao'] == 'consultar') {
    include_once( $pgJS );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
