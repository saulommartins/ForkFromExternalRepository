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
* Arquivo de instância para manutenção de assunto
* Data de Criação: 25/07/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 29194 $
$Name$
$Author: domluc $
$Date: 2008-04-15 10:03:13 -0300 (Ter, 15 Abr 2008) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TPROClassificacao.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumento.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAtributoProtocolo.class.php");
include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAcao.class.php");
include_once(CAM_GA_ADM_COMPONENTES.'IPopUpAcao.class.php');

$stPrograma = "ManterAssunto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('acaoSessao');

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'incluir';

$rsAssunto 			= new RecordSet();
$rsDocumentoAssunto = new RecordSet();
$rsAssuntoAtributo 	= new RecordSet();
$rsAtributoProtocolo= new RecordSet();
$arDocumentoAssunto = array();
$arAssuntoAtributo 	= array();

$obTPROClassificacao = new TPROClassificacao();

$obTPRODocumento = new TPRODocumento();
$obTPRODocumento->recuperaTodos($rsDocumento,'','nom_documento');

$obTPROAtributoProtocolo = new TPROAtributoProtocolo();
$obTPROAtributoProtocolo->recuperaTodos($rsAtributoProtocolo,'','nom_atributo');

//ALTERAÇÃO
if ($stAcao == "alterar") {
    include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssunto.class.php");
    include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumentoAssunto.class.php");
    include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAtributo.class.php");
    $inCodigoClassificacao = $_REQUEST['inCodigoClassificacao'];
    $inCodigoAssunto = $_REQUEST['inCodigoAssunto'];

    $stFiltro = ' WHERE cod_classificacao='.$inCodigoClassificacao;
    $obTPROClassificacao->recuperaTodos($rsClassificacao,$stFiltro,'nom_classificacao');

    $obTPROAssunto = new TPROAssunto();
    $obTPROAssunto->setDado('cod_classificacao', $inCodigoClassificacao);
    $obTPROAssunto->setDado('cod_assunto',		 $inCodigoAssunto);
    $obTPROAssunto->recuperaPorChave($rsAssunto);
    $stNomeAssunto = $rsAssunto->getCampo('nom_assunto');
    $stFiltro  = ' WHERE cod_classificacao ='.$inCodigoClassificacao.' AND ';
    $stFiltro .= 'cod_assunto ='.$inCodigoAssunto;

    $obTPRODocumentoAssunto = new TPRODocumentoAssunto();
    $obTPRODocumentoAssunto->recuperaTodos($rsDocumentoAssunto,$stFiltro);

    while (!$rsDocumentoAssunto->eof()) {
        $arDocumentoAssunto[$rsDocumentoAssunto->getCampo('cod_documento')] = true;
        $rsDocumentoAssunto->proximo();
    }
    $obTPROAssuntoAtributo = new TPROAssuntoAtributo();
    $obTPROAssuntoAtributo->recuperaTodos($rsAssuntoAtributo,$stFiltro);
    while (!$rsAssuntoAtributo->eof()) {
        $arAssuntoAtributo[$rsAssuntoAtributo->getCampo('cod_atributo')]=true;
        $rsAssuntoAtributo->proximo();
    }

    $rsAcao = new RecordSet();
    $obTPROAssuntoAcao = new TPROAssuntoAcao();
    $obTPROAssuntoAcao->obTPROAssunto = &$obTPROAssunto;
    $stFiltroAcao  = " AND assunto_acao.cod_classificacao = ".$inCodigoClassificacao;
    $stFiltroAcao .= " AND assunto_acao.cod_assunto = ".$inCodigoAssunto;
    $stOrdem .= " ORDER BY                          \n";
    $stOrdem .= " assunto_acao.cod_classificacao,   \n";
    $stOrdem .= " assunto_acao.cod_acao,            \n";
    $stOrdem .= " gestao.ordem,                     \n";
    $stOrdem .= " modulo.ordem,                     \n";
    $stOrdem .= " funcionalidade.ordem,             \n";
    $stOrdem .= " acao.ordem                        \n";
    $obTPROAssuntoAcao->recuperaRelacionamento($rsAcao,$stFiltroAcao,$stOrdem);

    Sessao::write('acaoSessao',$rsAcao->getElementos());

    $obLista = new Lista();
    $obLista->setMostraPaginacao(false);
    $obLista->setTitulo('Lista de Ações Selecionadas');
    $obLista->setRecordSet($rsAcao);
    $obLista->addCabecalho('', 5);
    $obLista->addCabecalho('Gestão', 15);
    $obLista->addCabecalho('Modulo', 15);
    $obLista->addCabecalho('Funcionalidade', 15);
    $obLista->addCabecalho('Ação', 15);
    $obLista->addCabecalho('', 5);

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_gestao');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_modulo');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('nom_funcionalidade');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento('ESQUERDA');
    $obLista->ultimoDado->setCampo('[cod_acao]-[nom_acao]');
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao('Excluir');
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirAcao()");
    $obLista->ultimaAcao->addCampo("1","cod_acao");
    $obLista->commitAcao();

  Sessao::write('documentos',$arDocumentoAssunto);
  Sessao::write('atributos',$arAssuntoAtributo);

} else {
    $obTPROClassificacao->recuperaTodos($rsClassificacao,'','nom_classificacao');
}

//DEFINICAO DOS COMPONENTES

//DEFINICAO DO FORM
$obForm = new Form();
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//ABA ASSUNTO
$obTxtCodigoClassificacao = new TextBox();
$obTxtCodigoClassificacao->setRotulo	("Classificação");
$obTxtCodigoClassificacao->setName		("inTxtCodigoClassificacao");
$obTxtCodigoClassificacao->setInteiro	( true );
$obTxtCodigoClassificacao->setNull		( false );
$obTxtCodigoClassificacao->setSize		(8);
$obTxtCodigoClassificacao->setMaxLength (8);
$obTxtCodigoClassificacao->setValue		($rsAssunto->getCampo('cod_classificacao'));

$obCmbClassificacao = new Select();
$obCmbClassificacao->setRotulo		("Classificação");
$obCmbClassificacao->setName		("inCmbCodigoClassificacao");
$obCmbClassificacao->setNull		( false );
$obCmbClassificacao->setCampoDesc	("nom_classificacao");
$obCmbClassificacao->setCampoId		("cod_classificacao");
$obCmbClassificacao->addOption		( "", "Selecione uma classificação");
$obCmbClassificacao->preencheCombo	($rsClassificacao);
$obCmbClassificacao->setStyle		('width:300px');
$obCmbClassificacao->setValue		($rsAssunto->getCampo('cod_classificacao'));

$obHdnCodigoAssunto = new hidden();
$obHdnCodigoAssunto->setName('inCodigoAssunto');
$obHdnCodigoAssunto->setValue($inCodigoAssunto);

$obHdnCodigoClassificacao = new hidden();
$obHdnCodigoClassificacao->setName('inCodigoClassificacao');
$obHdnCodigoClassificacao->setValue($inCodigoClassificacao);

$rsClassificacao->setPrimeiroElemento();

$obLblClassificacao = new Label();
$obLblClassificacao->setRotulo('Classificação');
$obLblClassificacao->setValue( $rsClassificacao->getCampo('cod_classificacao').' '.$rsClassificacao->getCampo('nom_classificacao') );

# Busca da configuração do Protocolo se deve gerar o código de classificação automático ou manual.
$boGeraCodigo = SistemaLegado::pegaConfiguracao("tipo_numeracao_classificacao_assunto", 5);

if (!empty($boGeraCodigo) && $boGeraCodigo == 'manual' && $stAcao == "incluir") {
    $obCodAssunto = new TextBox;
    $obCodAssunto->setRotulo    ( "Código do Assunto" );
    $obCodAssunto->setId        ( "inCodigoAssunto" );
    $obCodAssunto->setName      ( "inCodigoAssunto" );
    $obCodAssunto->setValue     ( $inCodigoAssunto );
    $obCodAssunto->setSize      ( 5 );
    $obCodAssunto->setMaxLength ( 3 );
    $obCodAssunto->setInteiro   ( true  );
    $obCodAssunto->setTitle     ( "Informe o código do assunto" );
    $obCodAssunto->setNull      ( false );
} else {
    $obHdnCodClassificacao = new Hidden;
    $obHdnCodClassificacao->setName( "inCodClassificacao" );
    $obHdnCodClassificacao->setValue( $inCodClassificacao );

    $obLabelAssunto = new Label;
    $obLabelAssunto->setRotulo('Código do Assunto');
    $obLabelAssunto->setValue($inCodigoAssunto);
}

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo		("Descrição");
$obTxtDescricao->setName		("stDescricao");
$obTxtDescricao->setSize		(60);
$obTxtDescricao->setMaxLength	(60);
$obTxtDescricao->setNull		(false);
$obTxtDescricao->setValue		($rsAssunto->getCampo('nom_assunto'));

$obRdConfidencialNao = new Radio();
$obRdConfidencialNao->setRotulo		("Confidencial");
$obRdConfidencialNao->setChecked	(true);
$obRdConfidencialNao->setName		("boConfidencial");
$obRdConfidencialNao->setValue		("f");
$obRdConfidencialNao->setLabel		("Não");
$obRdConfidencialNao->setNull		(false);

$obRdConfidencialSim = new Radio();
$obRdConfidencialSim->setRotulo		("Confidencial");
$obRdConfidencialSim->setChecked	(false);
$obRdConfidencialSim->setName		("boConfidencial");
$obRdConfidencialSim->setValue		("t");
$obRdConfidencialSim->setLabel		("Sim");
$obRdConfidencialSim->setNull		(false);

if ( $rsAssunto->getCampo('confidencial') == 't') {
    $obRdConfidencialNao->setChecked	(false);
    $obRdConfidencialSim->setChecked	(true);
}

//ABA DOCUMENTO
$obChkDocumento = new CheckBox();
$obChkDocumento->setRotulo("Documento");
$arDocumentos = array();

$rsDocumento->setPrimeiroElemento();

while ( !$rsDocumento->eof() ) {
    $obChkDocumento->setName("inDocumento[".$rsDocumento->getCorrente()."]");
    $obChkDocumento->setRotulo($rsDocumento->getCampo("nom_documento"));
    $obChkDocumento->setValue($rsDocumento->getCampo("cod_documento"));
    if ($arDocumentoAssunto[$rsDocumento->getCampo("cod_documento")]) {
        $obChkDocumento->setChecked(true);
    } else {
        $obChkDocumento->setChecked(false);
    }
    $arDocumentos[] = clone $obChkDocumento;
    $rsDocumento->proximo();
}

//ABA ATRIBUTO
$obChkAtributo = new CheckBox();
$obChkAtributo->setRotulo("Atributo");
$arAtributos = array();

$rsAtributoProtocolo->setPrimeiroElemento();

while ( !$rsAtributoProtocolo->eof() ) {
    $obChkAtributo->setName("inAtributo[".$rsAtributoProtocolo->getCorrente()."]");
    $obChkAtributo->setRotulo($rsAtributoProtocolo->getCampo("nom_atributo"));
    $obChkAtributo->setValue($rsAtributoProtocolo->getCampo("cod_atributo"));
    if ($arAssuntoAtributo[$rsAtributoProtocolo->getCampo("cod_atributo")]) {
        $obChkAtributo->setChecked(true);
    } else {
        $obChkAtributo->setChecked(false);
    }
    $arAtributos[] = clone $obChkAtributo;
    $rsAtributoProtocolo->proximo();
}

//ABA ACAO
$obPopUpAcao = new IPopUpAcao($obForm);

$obBntIncluir = new Button();
$obBntIncluir->setName('stBtnIncluirAcao');
$obBntIncluir->setValue('Incluir');
$obBntIncluir->obEvento->setOnClick('incluirAcao()');

$obBntLimparAcao = new Button();
$obBntLimparAcao->setName('stBtnLimparAcao');
$obBntLimparAcao->setValue('Limpar');
$obBntLimparAcao->obEvento->setOnClick('limparAcao()');

$obSpnListaAcao = new Span();
$obSpnListaAcao->setId('spnListaAcao');
if (is_object($obLista)) {
    $obLista->montaHTML();
    $obSpnListaAcao->setValue($obLista->getHTML());
}

//DEFINIÇÃO DO FORMULÁRIO
$obFormulario = new FormularioAbas();

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addForm($obForm);
$obFormulario->setAjuda('uc-01.06.95');

//ABA ASSUNTO
$obFormulario->addAba("Assunto");
$obFormulario->addTitulo("Dados para assunto");
if ($stAcao != 'incluir') {
    $obFormulario->addHidden		($obHdnCodigoClassificacao);
    $obFormulario->addHidden		($obHdnCodigoAssunto);
    $obFormulario->addComponente	($obLblClassificacao);
} else {
    $obFormulario->addComponenteComposto($obTxtCodigoClassificacao, $obCmbClassificacao);
}

if (!empty($boGeraCodigo) && $boGeraCodigo == 'manual' && $stAcao == "incluir")  {
    $obFormulario->addComponente($obCodAssunto);
}

if ($stAcao == 'alterar') {
    $obFormulario->addComponente($obLabelAssunto);
}

$obFormulario->addComponente($obTxtDescricao);
$obFormulario->agrupaComponentes(array($obRdConfidencialNao,$obRdConfidencialSim));

//ABA DOCUMENTOS
$obFormulario->addAba("Documentos",true);
$obFormulario->addTitulo("Documentos para assunto");

foreach ($arDocumentos as $obDocumento) {
    $obFormulario->addComponente($obDocumento);
}

//ABA ATRIBUTOS
$obFormulario->addAba("Atributos");
$obFormulario->addTitulo("Atributos para assunto");
foreach ($arAtributos as $obAtributo) {
    $obFormulario->addComponente($obAtributo);
}

$obFormulario->addAba("Ações",true);
$obFormulario->addTitulo("Ações para assunto");
$obFormulario->addComponente($obPopUpAcao);
$obFormulario->defineBarraAba(array($obBntIncluir, $obBntLimparAcao),'left','');
$obFormulario->addSpan( $obSpnListaAcao );
$obFormulario->montaHTML();
if ($stAcao == 'incluir') {
    $obFormulario->Ok();
} else {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId() );
}
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
