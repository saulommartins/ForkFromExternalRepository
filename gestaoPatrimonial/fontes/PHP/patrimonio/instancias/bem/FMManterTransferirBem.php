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
    * Data de Criação: 28/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: FMManterTransferirBem.php 61462 2015-01-20 13:17:23Z diogo.zarpelon $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GP_PAT_COMPONENTES."IPopUpBem.class.php";

include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganograma.class.php";
include_once CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php";
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrganograma.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TLocal.class.php" ;
include_once CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php" ;
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php";

$stPrograma = "ManterTransferirBem";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $request->get("stAcao");

# Recupera o Organograma Ativo no sistema.
$obTOrganogramaOrganograma = new TOrganogramaOrganograma;
$obTOrganogramaOrganograma->setDado('ativo', true);
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo);

$inCodOrganogramaAtivo = $rsOrganogramaAtivo->getCampo('cod_organograma');

$obHdnOrganogramaAtivo = new Hidden;
$obHdnOrganogramaAtivo->setName ("inCodOrganogramaAtivo" );
$obHdnOrganogramaAtivo->setValue($inCodOrganogramaAtivo);

include_once( $pgJs );

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

//cria hidden local origem cfe filtro
$obHdnLocalOrigem = new Hidden;
$obHdnLocalOrigem->setId("inLocalOrigem");
$obHdnLocalOrigem->setName("inLocalOrigem");
$obHdnLocalOrigem->setValue($_REQUEST['inCodLocal']);

//recupera a localizacao
$obTOrganogramaLocal = new TOrganogramaLocal();
$obTOrganogramaLocal->setDado( 'cod_local', $_REQUEST['inCodLocal'] );
$stFiltro = " AND cod_local = ".$_REQUEST['inCodLocal'];
$obTOrganogramaLocal->recuperaLocalizacao( $rsLocalizacao,$stFiltro );

//label para o local
$obLblLocal = new Label();
$obLblLocal->setRotulo( 'Local de Origem' );
$obLblLocal->setValue( $rsLocalizacao->getCampo('cod_local').' - '.$rsLocalizacao->getCampo('nom_local') );

//cria um span para os bens
$obSpnBem = new Span();
$obSpnBem->setId( 'spnBem' );

//recupera bens para a localizacao
$obTPatrimonioBem = new TPatrimonioBem;

$stFiltro = " WHERE 1=1 ";

if (!empty($_REQUEST['hdnUltimoOrgaoSelecionado'])) {
    $stFiltro .= "
          AND historico_bem.cod_orgao = ".$_REQUEST['hdnUltimoOrgaoSelecionado']."
          AND historico_bem.cod_local = ".$_REQUEST['inCodLocal']." ";
    
} else {
    $stMensagem = "Selecione o Organograma";
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,$stMensagem,"erro","aviso", Sessao::getId(), "../");
}

if (!empty($_REQUEST['inNumResponsavelAtual'])){
    $stFiltro .= " AND bem_responsavel.numcgm = ".$_REQUEST['inNumResponsavelAtual'];
}

$obTPatrimonioBem->recuperaRelacionamentoTransferencia( $rsBens, $stFiltro,' ORDER BY cod_bem' );

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Lista de Bens" );

$obLista->setRecordSet( $rsBens );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Placa");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 54 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Baixado" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

//recupera as situacoes
$obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();
$obTPatrimonioSituacaoBem->recuperaTodos( $rsSituacaoBem );

//instancia um select para a situacao
$obSlSituacao = new Select();
$obSlSituacao->setName      ( 'slSituacao_[cod_bem]_' );
$obSlSituacao->setCampoID   ( 'cod_situacao' );
$obSlSituacao->setCampoDesc ( 'nom_situacao' );
$obSlSituacao->preencheCombo( $rsSituacaoBem );
$obSlSituacao->setValue     ( 'cod_situacao' );

//instancia um check para transferir o bem
$obChkTransferir = new CheckBox();
$obChkTransferir->setName      ( 'boTransferir_[cod_bem]_' ) ;
$obChkTransferir->setValue     ( 1 );

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_bem]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_placa]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[descricao]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addDadoComponente   ( $obSlSituacao );
$obLista->ultimoDado->setCampo( "slSituacao_[cod_bem']_"         );
$obLista->commitDadoComponente();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[baixado]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDadoComponente   ( $obChkTransferir );
$obLista->commitDadoComponente();

$obChkTodosN = new Checkbox;
$obChkTodosN->setName                        ( "boTodos" );
$obChkTodosN->setId                          ( "boTodos" );
$obChkTodosN->setRotulo                      ( "Selecionar Todas" );
$obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
$obChkTodosN->montaHTML();

$obTabelaCheckboxN = new Tabela;
$obTabelaCheckboxN->addLinha();
$obTabelaCheckboxN->ultimaLinha->addCelula();
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setColSpan ( 2 );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->setClass   ( $obLista->getClassPaginacao() );
$obTabelaCheckboxN->ultimaLinha->ultimaCelula->addConteudo( "<div align='right'>Selecionar Todos".$obChkTodosN->getHTML()."&nbsp;</div>");
$obTabelaCheckboxN->ultimaLinha->commitCelula();
$obTabelaCheckboxN->commitLinha();
$obTabelaCheckboxN->montaHTML();

$obLista->montaHTML();

$obSpnBem->setValue( $obLista->getHtml().$obTabelaCheckboxN->getHTML() );

//instancia o componenete IMontaOrganograma
$obLblIMontaOrganograma = new IMontaOrganograma(true);
$obLblIMontaOrganograma->setCodOrgao($_REQUEST['hdnUltimoOrgaoSelecionado']);
$obLblIMontaOrganograma->setIdOrganograma('organogramaOrigem');
$obLblIMontaOrganograma->setRotuloComboOrganograma('Organograma Origem');
$obLblIMontaOrganograma->setCadastroOrganograma(true);
$obLblIMontaOrganograma->setNivelObrigatorio(0);
$obLblIMontaOrganograma->setComponenteSomenteLeitura(true);
$obLblIMontaOrganograma->setHiddenInformacoes(false);

//instancia o componenete IMontaOrganograma
$obIMontaOrganograma = new IMontaOrganograma(false);
$obIMontaOrganograma->setCodOrgao($codOrgao);
$obIMontaOrganograma->setRotuloComboOrganograma('Organograma Destino');
$obIMontaOrganograma->setCadastroOrganograma(true);
$obIMontaOrganograma->setNivelObrigatorio(1);

$obIMontaOrganogramaLocal = new IMontaOrganogramaLocal;
$obIMontaOrganogramaLocal->setId("inLocalDestino");
$obIMontaOrganogramaLocal->setName("inLocalDestino");
$obIMontaOrganogramaLocal->setRotulo("Local de Destino");
$obIMontaOrganogramaLocal->setTitle("Selecione o Local de Destino");
$obIMontaOrganogramaLocal->setStyle('width:350;');
$obIMontaOrganogramaLocal->setValue($_REQUEST['inCodLocal']);
$obIMontaOrganogramaLocal->setNull(false);

//instancia um textbox para a descricao
$obTxtDescricaoBem = new TextBox();
$obTxtDescricaoBem->setRotulo( 'Descrição da Situação' );
$obTxtDescricaoBem->setTitle( 'Informe a descrição da Situação do bem.' );
$obTxtDescricaoBem->setName( 'stNomBem' );
$obTxtDescricaoBem->setMaxLength( 60 );
$obTxtDescricaoBem->setSize( 60 );
$obTxtDescricaoBem->setNull( false );

//instancia o componente IPopUpCGM para o responsavel
$obIPopUpCGMResponsavel = new IPopUpCGM( $obForm );
$obIPopUpCGMResponsavel->setRotulo           ( 'Responsável'            );
$obIPopUpCGMResponsavel->setTitle            ( 'Informe o responsável.' );
$obIPopUpCGMResponsavel->setName             ( 'stNomResponsavel'       );
$obIPopUpCGMResponsavel->setId               ( 'stNomResponsavel'       );
$obIPopUpCGMResponsavel->obCampoCod->setName ( 'inCodResponsavel'       );
$obIPopUpCGMResponsavel->obCampoCod->setId   ( 'inCodResponsavel'       );
$obIPopUpCGMResponsavel->setNull             ( false                     );

//Gerar Termo
$obCheckBoxEmitirTermo = new CheckBox;
$obCheckBoxEmitirTermo->setName  ('boEmitirTermo');
$obCheckBoxEmitirTermo->setId    ('boEmitirTermo');
$obCheckBoxEmitirTermo->setRotulo('Emitir Termo de Responsabilidade');
$obCheckBoxEmitirTermo->setTitle ('Emitir Termo de Responsabilidade');
$obCheckBoxEmitirTermo->setChecked('true');
$obCheckBoxEmitirTermo->setValue ('true');

//Demonstrar valores no relatorio
$obChkValor = new Checkbox;
$obChkValor->setName    ( 'demo_valor'   );
$obChkValor->setId      ( 'demo_valor'   );
$obChkValor->setRotulo  ( 'Demonstrar Valor'       );
$obChkValor->setChecked ( true          );
$obChkValor->setValue   (1);

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-03.01.06');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnLocalOrigem );
$obFormulario->addHidden    ( $obHdnOrganogramaAtivo );

$obFormulario->addTitulo( 'Localização de Origem' );
$obLblIMontaOrganograma->geraFormulario( $obFormulario );
$obFormulario->addComponente($obLblLocal);

$obFormulario->addSpan  ( $obSpnBem );

$obFormulario->addTitulo( 'Localização de Destino' );

$obIMontaOrganograma->geraFormulario( $obFormulario );
$obIMontaOrganogramaLocal->geraFormulario( $obFormulario );

$obFormulario->addComponente( $obTxtDescricaoBem );
$obFormulario->addComponente( $obIPopUpCGMResponsavel );
$obFormulario->addComponente( $obCheckBoxEmitirTermo  );
$obFormulario->addComponente( $obChkValor             );
$obFormulario->Cancelar( $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
