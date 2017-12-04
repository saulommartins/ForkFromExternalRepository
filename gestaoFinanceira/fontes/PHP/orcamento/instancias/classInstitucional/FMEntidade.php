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
    * Página de Formulario de Inclusao/Alteracao de Entidade
    * Data de Criação   : 04/11/2005

    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-07-25 10:37:43 -0300 (Qua, 25 Jul 2007) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.28  2007/07/25 13:37:43  hboaventura
Bug#9673#

Revision 1.27  2007/07/03 21:32:48  bruce
Bug #9552# , Bug #9534#

Revision 1.26  2007/05/21 19:04:15  melo
Bug #9229#

Revision 1.25  2007/05/21 18:51:29  luciano
#8856#

Revision 1.24  2006/11/17 17:56:22  cako
Bug #7441#

Revision 1.23  2006/07/14 17:18:42  leandro.zis
Bug #6179#

Revision 1.22  2006/07/05 20:42:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//if ($stAcao != 'alterar') {
    include_once( CAM_GF_INCLUDE."validaGF.inc.php");
//}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Entidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obREntidadeOrcamento  = new ROrcamentoEntidade;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;
$stOrdem               = " ORDER BY C.nom_cgm";

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//***************************************//
//Busca os dados para ALTERAÇÃO
//***************************************//
if ($stAcao == 'alterar') {
    $obREntidadeOrcamento->setCodigoEntidade( $_GET['inCodigoEntidade'] );
    $obREntidadeOrcamento->setExercicio     ( Sessao::getExercicio()        );
    $obREntidadeOrcamento->consultarNomes   ($rsEntidade, $obTransacao );
    $obREntidadeOrcamento->listarMembrosDisponiveis( $rsUsuariosDisponiveis, $stOrdem );
    $obREntidadeOrcamento->listarMembrosSelecionados( $rsUsuariosSelecionados, $stOrdem );

    $inCodigoEntidade           = $obREntidadeOrcamento->getCodigoEntidade();
    $stNomeEntidade             = $obREntidadeOrcamento->getNomeEntidade();
    $inNumCGM                   = $obREntidadeOrcamento->getNumCGM();
    $inCodigoResponsavel        = $obREntidadeOrcamento->getCodigoResponsavel();
    $stNomeResponsavel          = $obREntidadeOrcamento->getNomeResponsavel();
    $inCodigoResponsavelTecnico = $obREntidadeOrcamento->getCodigoResponsavelTecnico();
    $stNomeResponsavelTecnico   = $obREntidadeOrcamento->getNomeResponsavelTecnico();
    $inCodProfissao             = $obREntidadeOrcamento->getCodigoProfissao();
    $stArquivoLogotipo          = $obREntidadeOrcamento->getArquivoLogotipo();

    $obLblCodEntidade = new Label;
    $obLblCodEntidade->setRotulo ( "Código" );
    $obLblCodEntidade->setName   ( 'inCodigoEntidade' );
    $obLblCodEntidade->setValue  ( $inCodigoEntidade );

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setValue ( $inCodigoEntidade );
    $obHdnCodEntidade->setName  ( 'inCodigoEntidade' );

    if ($stNomeEntidade != "" AND $stNomeResponsavel != "" AND $stNomeResponsavelTecnico != "") {

        $obHdnNomeEntidade = new Hidden;
        $obHdnNomeEntidade->setValue ( $stNomeEntidade );
        $obHdnNomeEntidade->setName  ( 'stNomeEntidade' );

        $obHdnNomeResponsavel = new Hidden;
        $obHdnNomeResponsavel->setValue ( $stNomeResponsavel );
        $obHdnNomeResponsavel->setName  ( 'stNomeResponsavel' );

        $obHdnNomeRespTecnico = new Hidden;
        $obHdnNomeRespTecnico->setValue ( $stNomeResponsavelTecnico );
        $obHdnNomeRespTecnico->setName  ( 'stNomeResponsavelTecnico' );

        //$js = "buscaCGM('buscaNomes');";
        //SistemaLegado::executaFramePrincipal($js);
    }
} else {

    $rsLista = new RecordSet();
    $obREntidadeOrcamento->listarEntidades( $rsLista, " AND e.exercicio <= '".Sessao::getExercicio()."' ORDER BY C.nom_cgm ","" );
    $obLista = new Lista;

    while ( !$rsLista->eof() ) {
         $rsLista->setCampo( 'ativa', $rsLista->getCampo( 'exercicio_atual' )  == Sessao::getExercicio() ) ;
         $rsLista->proximo();
    }
    $rsLista->setPrimeiroElemento();

    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo("Marque as Entidades ATIVAS para este Exercício:");
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ativa");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome da Entidade");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Exercício");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obChkEntidade = new CheckBox;
    $obChkEntidade->setName ( "boEntidade|[exercicio]|[cod_entidade]|[numcgm]|[cod_responsavel]|[cod_resp_tecnico]|[cod_profissao]" );
    $obChkEntidade->setId   ( "boEntidade" );
    $obChkEntidade->setValue( "" );
    $obChkEntidade->setChecked( false );
    $obChkEntidade->setLabel( "" );
    $obChkEntidade->setTitle( "" );
    $obChkEntidade->obEvento->setOnClick("");

    $obLista->addDadoComponente( $obChkEntidade,false );
    $obLista->ultimoDado->setDesabilitaComponente( "ativa" );
    $obLista->ultimoDado->setCampo ( "ativa" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDadoComponente ();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_entidade" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "exercicio" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
}
if ( !$obREntidadeOrcamento->getCodigoEntidade() ) {
    $obREntidadeOrcamento->pegarProximoCodigo();
    $inCodigoEntidade = $obREntidadeOrcamento->getCodigoEntidade();
    $obREntidadeOrcamento->listarMembrosDisponiveis( $rsUsuariosDisponiveis, $stOrdem );
}

Sessao::remove('arItens');
//sessao->transf3['arItens'] = array();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setEncType( "multipart/form-data" );

// Define Objeto Span as Entidades
$obSpanEntidades = new Span;
$obSpanEntidades->setId( "spnEntidades" );
$obSpanEntidades->setValue( $stHTML );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$stEval = "\n
    var i=0; \n
    var cont=0; \n
    for (i=0; i<document.forms['frm'].elements.length; i++) { \n
        if (document.forms['frm'].elements[i].type=='checkbox') {\n
            if (document.forms['frm'].elements[i].checked==true) {\n
                cont++;\n
            }\n
        }\n
    }\n
    if ((cont==0) && (document.getElementById('spnLista').innerHTML ==' ')) {\n
        erro = true;\n
        mensagem += '@Adicione uma Entidade e/ou Escolha uma Entidade Ativa!()';\n
    }\n
";

$obHdnEval = new HiddenEval;
$obHdnEval->setName  ( "stEval"            );
$obHdnEval->setValue ( $stEval             );

$obHdnSequencia = new HiddenEval;
$obHdnSequencia->setName  ( "inSequencia"       );
$obHdnSequencia->setValue ( ""                  );

//DEFINE O ARQUIVO DO BRASÃO NA ALTERAÇÃO
$obHdnArquivoLogotipo = new Hidden;
$obHdnArquivoLogotipo->setName  ( 'stHdnArquivoLogotipo' );
$obHdnArquivoLogotipo->setValue ( $stArquivoLogotipo  );

$obImgAquivoLogotipo = new Img;
$obImgAquivoLogotipo->setCaminho( CAM_GF_ORCAMENTO.'anexos/'.$stArquivoLogotipo );
$obImgAquivoLogotipo->setWidth  ( '60px' );
$obImgAquivoLogotipo->setHeight ( '55px' );
$obImgAquivoLogotipo->setNull   ( true   );

$obChkApagarLogotipo = new CheckBox;
$obChkApagarLogotipo->setName   ( 'boApargarLogotipo' );
$obChkApagarLogotipo->setLabel  ( 'Excluir Logotipo&nbsp;&nbsp;&nbsp;&nbsp;' );
$obChkApagarLogotipo->setRotulo ( 'Logotipo' );
$obChkApagarLogotipo->setValue  ( 's' );

//Define o objeto TEXT para armazenar o CÓDIGO DA ENTIDADE
$obTxtCodEntidade = new Label;
$obTxtCodEntidade->setId       ( "CodigoEntidade" );
$obTxtCodEntidade->setName     ( "inCodigoEntidade"   );
$obTxtCodEntidade->setValue    ( $inCodigoEntidade    );
$obTxtCodEntidade->setRotulo   ( "Código"             );

$obHdnCodigoEntidade = new Hidden;
$obHdnCodigoEntidade->setValue ( $inCodigoEntidade );
$obHdnCodigoEntidade->setName  ( 'inCodigoEntidade' );

//Define o objeto INNER para armazenar a ENTIDADE
$obBscEntidade = new BuscaInner;
$obBscEntidade->setRotulo( "*Entidade" );
$obBscEntidade->setTitle( "Informe o código do CGM." );
$obBscEntidade->setId( "campoInner" );
$obBscEntidade->setValue( $stNomeEntidade);
$obBscEntidade->obCampoCod->setName("inNumCGM");
$obBscEntidade->obCampoCod->setValue( $inNumCGM );
$obBscEntidade->setFuncaoBusca ( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGM','campoInner','juridica','".Sessao::getId()."','800','550')" );
$obBscEntidade->setValoresBusca( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName(), 'juridica' );

include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
$obBscResponsavel = new IPopUpCGM( $obForm );
$obBscResponsavel->setNull     ( true               );
$obBscResponsavel->setRotulo   ( "*Responsável"          );
$obBscResponsavel->setId       ( "campoInner2" );
$obBscResponsavel->setTitle    ( "Informe o CGM do responsável" );
$obBscResponsavel->setValue( $stNomeResponsavel);
$obBscResponsavel->obCampoCod->setName("inCodigoResponsavel");
$obBscResponsavel->obCampoCod->setValue( $inCodigoResponsavel );
$obBscResponsavel->obCampoCod->setSize ( 10 );
$obBscResponsavel->setTipo ( 'fisica' );

/*
//Define o objeto INNER para armazenar o RESPONSAVEL
$obBscResponsavel = new BuscaInner;
$obBscResponsavel->setRotulo( "*Responsável" );
$obBscResponsavel->setTitle( "Informe o CGM do responsável." );
$obBscResponsavel->setId( "campoInner2" );
$obBscResponsavel->setValue( $stNomeResponsavel);
$obBscResponsavel->obCampoCod->setName("inCodigoResponsavel");
$obBscResponsavel->obCampoCod->setValue( $inCodigoResponsavel );
$obBscResponsavel->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."responsavel/FLProcurarResponsavel.php','frm','inCodigoResponsavel','campoInner2','fisica','".Sessao::getId()."','800','550')" );
$obBscResponsavel->setValoresBusca( CAM_GT_CEM_POPUPS.'responsavel/OCProcurarResponsavel.php?'.Sessao::getId(), $obForm->getName() );
*/

//Define o objeto INNER para armazenar o TECNICO
$obBscRespTec = new BuscaInner;
$obBscRespTec->setRotulo( "*Responsável Técnico" );
$obBscRespTec->setTitle( "Informe o código do responsável técnico." );
$obBscRespTec->setId( "campoInner3" );
$obBscRespTec->setValue( $stNomeResponsavelTecnico);
$obBscRespTec->obCampoCod->setName("inCodigoResponsavelTecnico");
$obBscRespTec->obCampoCod->setValue( $inCodigoResponsavelTecnico );
//$obBscRespTec->obCampoCod->obEvento->setOnBlur("buscaCGM('buscaRespTecnico');");
$obBscRespTec->setFuncaoBusca( "abrePopUp('".CAM_GT_CEM_POPUPS."RespTecnico/LSProcurarRespTecnico.php','frm','inCodigoResponsavelTecnico&inCodProfissao=inCodProfissao','campoInner3','geral','".Sessao::getId()."','800','550')" );
$obBscRespTec->setValoresBusca( CAM_GT_CEM_POPUPS.'RespTecnico/OCProcurarRespTecnico.php?'.Sessao::getId(), $obForm->getName() );

$obHdnProfissao = new hidden;
$obHdnProfissao->setValue ( $inCodProfissao );
$obHdnProfissao->setName  ( 'inCodProfissao' );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbUsuarios = new SelectMultiplo();
$obCmbUsuarios->setName   ('inCodigoUsuariosSelecionados');
$obCmbUsuarios->setRotulo ( "*Usuário da Entidade" );
$obCmbUsuarios->setTitle  ( "Selecione os usuários da entidade." );

// lista de atributos disponiveis
$obCmbUsuarios->SetNomeLista1 ('inCodigoUsuariosDisponiveis');
$obCmbUsuarios->setCampoId1   ( 'numcgm' );
$obCmbUsuarios->setCampoDesc1 ( 'nom_cgm' );
$obCmbUsuarios->SetRecord1    ( $rsUsuariosDisponiveis );

//brasão da entidade
$obFileLogotipo = new FileBox;
$obFileLogotipo->setName      ( 'stArquivoLogotipo' );
$obFileLogotipo->setRotulo    ( 'Logotipo' );
$obFileLogotipo->setTitle     ( 'Selecione o logotipo.' );
$obFileLogotipo->setSize      ( 50 );
$obFileLogotipo->setMaxLength ( 50 );

// lista de atributos selecionados
$obCmbUsuarios->SetNomeLista2 ('inCodigoUsuariosSelecionados');
$obCmbUsuarios->setCampoId2   ('numcgm');
$obCmbUsuarios->setCampoDesc2 ('nom_cgm');
$obCmbUsuarios->SetRecord2    ( $rsUsuariosSelecionados );

// Define Objeto Checkbox para desativar a Entidade
$obChkDesativarEntidade = new CheckBox;
$obChkDesativarEntidade->setRotulo( "Situação" );
$obChkDesativarEntidade->setName ( "boDesativarEntidade" );
$obChkDesativarEntidade->setId   ( "boDesativarEntidade" );
$obChkDesativarEntidade->setValue( "1" );
$obChkDesativarEntidade->setChecked( false );
$obChkDesativarEntidade->setLabel( "Desativar Entidade" );

$obSpan = new Span;
$obSpan->setId( "spnLista" );

// Define Objeto Button para Incluir
$obBtnIncluir = new Button;
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->obEvento->setOnClick( "inclui();" );

// Define Objeto Button para Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limpa();" );

// Define Objeto Button Ok
$obBtnOk = new Ok;

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda ( "UC-02.01.02"           );
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnProfissao           );
$obFormulario->addHidden        ( $obHdnSequencia           );

$obFormulario->addTitulo        ( "Dados para Entidade"     );
if ($stAcao == 'incluir') {
    $obFormulario->addHidden( $obHdnCodigoEntidade );
    $obFormulario->addHidden( $obHdnEval, true );
    $obFormulario->addSpan( $obSpanEntidades );
    $obFormulario->addComponente( $obTxtCodEntidade );
}

if ($stAcao == 'alterar') {
    $obFormulario->addHidden        ( $obHdnNomeEntidade    );
    $obFormulario->addHidden        ( $obHdnCodEntidade     );
    $obFormulario->addHidden        ( $obHdnNomeResponsavel );
    $obFormulario->addHidden        ( $obHdnNomeRespTecnico );
    $obFormulario->addHidden        ( $obHdnArquivoLogotipo );
    $obFormulario->addComponente    ( $obLblCodEntidade     );
}
$obFormulario->addComponente    ( $obBscEntidade            );
$obFormulario->addComponente    ( $obBscResponsavel         );
$obFormulario->addComponente    ( $obBscRespTec             );
$obFormulario->addComponente    ( $obCmbUsuarios            );
$obFormulario->addComponente    ( $obFileLogotipo             );

if ($stAcao == "incluir") {
    $obFormulario->agrupaComponentes( array ($obBtnIncluir, $obBtnLimpar));
    $obFormulario->addSpan( $obSpan );
    $obFormulario->defineBarra( array($obBtnOk) );
} else {
    $rsLista = new Recordset();
    $obREntidadeOrcamento->verificaSituacaoEntidade($rsLista);
    if ($rsLista->getNumLinhas()==-1) {
        $obFormulario->addComponente($obChkDesativarEntidade);
    }
    if ($stArquivoLogotipo) {
        $obFormulario->agrupaComponentes( array( $obChkApagarLogotipo, $obImgAquivoLogotipo ) );
    }
    $obFormulario->cancelar();
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
