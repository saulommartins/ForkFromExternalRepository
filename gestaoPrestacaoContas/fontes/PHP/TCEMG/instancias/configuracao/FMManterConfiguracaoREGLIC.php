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

    * Página de Formularioo para Configuracao Contas Bancarias TCEMG
    * Data de Criação   : 14/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: FMManterConfiguracaoREGLIC.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGContaBancaria.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoDecreto.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGREGLIC.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoREGLIC.class.php");
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoRegistroPreco.class.php");
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoREGLIC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ( $pgJS );

$obRegra = new RContabilidadeLancamentoValor;

//$obTCEMGContaBancaria = new TTCEMGContaBancaria;

$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio      ( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->consultar( $rs );

$stNomEntidade = $obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->getNomCGM();

$obTCEMGConfiguracaoREGLIC = new TTCEMGConfiguracaoREGLIC();
$obTCEMGConfiguracaoREGLIC->setDado    ('exercicio', Sessao::getExercicio() );
$obTCEMGConfiguracaoREGLIC->setDado('cod_entidade', $_POST[ 'inCodEntidade' ] );
$obTCEMGConfiguracaoREGLIC->recuperaTodos( $rsRecordSet, $boTransacao );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$obCmbRegArt47 = new Select();
$obCmbRegArt47->setRotulo  ( "O município implementou a regulamentação do art. 47 da LC 123/2006?" );
$obCmbRegArt47->setName      ( "inRegulamentoArt47" );
$obCmbRegArt47->addOption    ( "", "Selecione" );
$obCmbRegArt47->addOption    ( "1", "Sim" );
$obCmbRegArt47->addOption    ( "2", "Não" );
$obCmbRegArt47->setCampoId   ( "[regulamento_art_47]" );
$obCmbRegArt47->setValue ( $rsRecordSet->getCampo('reg_exclusiva'));
$obCmbRegArt47->setNull      ( false );

// Define Lista de Normas.
$obNorma = new TTCEMGREGLIC();
$obNorma->setDado    ('exercicio', Sessao::getExercicio() );
$obNorma->recuperaNorma( $rsNormas ) ;

$obNroNormaReg = new Select();
$obNroNormaReg->setRotulo  ( "Número da Norma que Regulamentou o Artigo 47" );
$obNroNormaReg->setTitle  ( "Número da Norma que Regulamentou o Artigo 47" );
$obNroNormaReg->setName      ( "inCodNorma" );
$obNroNormaReg->addOption ( "", "Selecione" );
$obNroNormaReg->setCampoId   ( "[cod_norma]" );
$obNroNormaReg->setValue ( $rsRecordSet->getCampo('cod_norma'));
$obNroNormaReg->setCampoDesc ( "[num_norma] - [nom_norma]" );
$obNroNormaReg->preencheCombo( $rsNormas );
$obNroNormaReg->setNull(false);

$obCmbRegExclusiva = new Select();
$obCmbRegExclusiva->setRotulo  ( "O município regulamentou procedimentos para a participação exclusiva de Microempresas e empresas de pequeno porte?" );
$obCmbRegExclusiva->setName      ( "inRegExclusiva" );
$obCmbRegExclusiva->addOption    ( "", "Selecione" );
$obCmbRegExclusiva->addOption    ( "1", "Sim" );
$obCmbRegExclusiva->addOption    ( "2", "Não" );
$obCmbRegExclusiva->setCampoId   ( "[reg_exclusiva]" );
$obCmbRegExclusiva->setValue ( $rsRecordSet->getCampo('reg_exclusiva'));
$obCmbRegExclusiva->setNull      ( false );

$obTxtArtRegExclusiva = new TextBox;
$obTxtArtRegExclusiva->setRotulo          ( "Artigo da regulamentação exclusiva (LC 23/2006)" );
$obTxtArtRegExclusiva->setTitle           ( "Artigo da regulamentação exclusiva (LC 23/2006)" );
$obTxtArtRegExclusiva->setName            ( "stArtigoRegExclusiva" );
$obTxtArtRegExclusiva->setValue           ($rsRecordSet->getCampo('artigo_reg_exclusiva'));
$obTxtArtRegExclusiva->setSize            ( 6 );
$obTxtArtRegExclusiva->setMaxLength       ( 6 );

$obValorLimiteRegExclusiva = new Moeda;
$obValorLimiteRegExclusiva->setValue   ( str_replace('.',',',$rsRecordSet->getCampo('valor_limite_reg_exclusiva') )      );
$obValorLimiteRegExclusiva->setNull    ( true         );
$obValorLimiteRegExclusiva->setId      ( "vlLimiteRegExclusiva" );
$obValorLimiteRegExclusiva->setSize    ( 20           );
$obValorLimiteRegExclusiva->setRotulo  ( "*Valor Limite da regulamentação exclusiva (LC 123/2006 (art. 48, I)"     );
$obValorLimiteRegExclusiva->setName    ( "vlLimiteRegExclusiva" );
$obValorLimiteRegExclusiva->setNegativo( "false"      );

$obCmbProcSubContratacao = new Select();
$obCmbProcSubContratacao->setRotulo( "O município estabeleceu procedimentos para a subcontratação de Microempresas e Empresas de Pequeno Porte? (LC 123/2006 (art. 48, II)" );
$obCmbProcSubContratacao->setName( "inProcSubContratacao" );
$obCmbProcSubContratacao->addOption( "", "Selecione" );
$obCmbProcSubContratacao->addOption( "1", "Sim" );
$obCmbProcSubContratacao->addOption ( "2", "Não" );
$obCmbProcSubContratacao->setCampoId( "[proc_sub_contratacao]" );
$obCmbProcSubContratacao->setValue( $rsRecordSet->getCampo('proc_sub_contratacao'));
$obCmbProcSubContratacao->setNull( false );

$obTxtArtProcSubContratacao = new TextBox;
$obTxtArtProcSubContratacao->setRotulo( "Artigo da regulamentação exclusiva (LC 23/2006)" );
$obTxtArtProcSubContratacao->setTitle( "Artigo da regulamentação exclusiva (LC 23/2006)" );
$obTxtArtProcSubContratacao->setName( "stArtigoProcSubContratacao" );
$obTxtArtProcSubContratacao->setValue( $rsRecordSet->getCampo('artigo_proc_sub_contratacao'));
$obTxtArtProcSubContratacao->setSize( 6 );
$obTxtArtProcSubContratacao->setMaxLength( 6 );

$obNumPercentualSubContratacao = new Porcentagem();
$obNumPercentualSubContratacao->setRotulo    ( "Percentual estabelecido para subcontratação (LC 123/2006 (art. 48, II)." );
$obNumPercentualSubContratacao->setName      ( "flPercentualSubContratacao" );
$obNumPercentualSubContratacao->setTitle     ( "Informe o percentual para Sub Contratação" );
$obNumPercentualSubContratacao->setValue(str_replace('.',',',$rsRecordSet->getCampo('percentual_sub_contratacao')));
$obNumPercentualSubContratacao->setSize      ( 5 );
$obNumPercentualSubContratacao->setMaxLength ( 5 );

$obCmbCriteriosEmpenhoPagamento = new Select();
$obCmbCriteriosEmpenhoPagamento->setRotulo ( "O município estabeleceu critérios para empenho e pagamento a Microempresas e Empresas de Pequeno Porte? (LC 123/2006 (art. 48, § 2º). " );
$obCmbCriteriosEmpenhoPagamento->setName ( "inCriteriosEmpenhoPagamento" );
$obCmbCriteriosEmpenhoPagamento->addOption ( "", "Selecione" );
$obCmbCriteriosEmpenhoPagamento->addOption ( "1", "Sim" );
$obCmbCriteriosEmpenhoPagamento->addOption ( "2", "Não" );
$obCmbCriteriosEmpenhoPagamento->setCampoId ( "[criterios_empenho_pagamento]" );
$obCmbCriteriosEmpenhoPagamento->setValue ( $rsRecordSet->getCampo('criterio_empenho_pagamento'));
$obCmbCriteriosEmpenhoPagamento->setNull ( false );

$obTxtArtEmpenhoPagamento = new TextBox;
$obTxtArtEmpenhoPagamento->setRotulo          ( "Artigo relativo aos critérios para empenho e pagamento (LC 123/2006 (art. 48, § 2º)" );
$obTxtArtEmpenhoPagamento->setTitle           ( "Artigo relativo aos critérios para empenho e pagamento (LC 123/2006 (art. 48, § 2º)" );
$obTxtArtEmpenhoPagamento->setName            ( "stArtigoEmpenhoPagamento" );
$obTxtArtEmpenhoPagamento->setValue           ( $rsRecordSet->getCampo('artigo_empenho_pagamento') );
$obTxtArtEmpenhoPagamento->setSize            ( 6 );
$obTxtArtEmpenhoPagamento->setMaxLength       ( 6 );

$obCmbEstabeleceuPercContratacao = new Select();
$obCmbEstabeleceuPercContratacao->setRotulo ( "O município estabeleceu critérios para empenho e pagamento a Microempresas e Empresas de Pequeno Porte? (LC 123/2006 (art. 48, § 2º). " );
$obCmbEstabeleceuPercContratacao->setName ( "inEstabeleceuPercContratacao" );
$obCmbEstabeleceuPercContratacao->addOption ( "", "Selecione" );
$obCmbEstabeleceuPercContratacao->addOption ( "1", "Sim" );
$obCmbEstabeleceuPercContratacao->addOption ( "2", "Não" );
$obCmbEstabeleceuPercContratacao->setCampoId ( "[estabeleceu_perc_contratacao]" );
$obCmbEstabeleceuPercContratacao->setValue ($rsRecordSet->getCampo('estabeleceu_perc_contratacao') );
$obCmbEstabeleceuPercContratacao->setNull ( false );

$obTxtArtPercentualContratacao = new TextBox;
$obTxtArtPercentualContratacao->setRotulo          ( "Artigo do percentual contratação (LC 23/2006 (art. 48, III)" );
$obTxtArtPercentualContratacao->setTitle           ( "Artigo do percentual contratação (LC 23/2006 (art. 48, III)" );
$obTxtArtPercentualContratacao->setName            ( "stArtigoPercContratacao" );
$obTxtArtPercentualContratacao->setValue           ( $rsRecordSet->getCampo('artigo_perc_contratacao') );
$obTxtArtPercentualContratacao->setSize            ( 6 );
$obTxtArtPercentualContratacao->setMaxLength       ( 6 );


$obNumPercentualContratacao = new Porcentagem();
$obNumPercentualContratacao->setRotulo    ( "Percentual estabelecido para contratação (LC 123/2006 (art. 48, III)" );
$obNumPercentualContratacao->setName      ( "flPercentualContratacao" );
$obNumPercentualContratacao->setTitle     ( "Informe o percentual para Contratação" );
$obNumPercentualContratacao->setSize      ( 5 );
$obNumPercentualContratacao->setValue(str_replace('.',',',$rsRecordSet->getCampo('percentual_contratacao')));
$obNumPercentualContratacao->setMaxLength ( 5 );

$obNorma = new TTCEMGTipoRegistroPreco();
$obNorma->setDado    ('exercicio', Sessao::getExercicio() );
$obNorma->recuperaTipoRegistroPreco( $rsNormasTipoRegistro ) ;

$obLista = new Lista;
$obLista->setMostraPaginacao( false );
$obLista->setTitulo( "Decreto" );

$obLista->setRecordSet($rsNormasTipoRegistro);
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número do Decreto" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Decreto" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data do Decreto " );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Publicação do Decreto" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Decreto" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_norma" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_norma" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_assinatura_formatado" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_publicacao" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obTipoDecreto= new TTCEMGTipoDecreto();
$obTipoDecreto->recuperaTodos($rsTipoDecreto);

//Select Tipo de Decreto
$obCmbTipoDecreto = new Select();
$obCmbTipoDecreto->addOption    ( "", "Selecione" );
$obCmbTipoDecreto->setName      ( "inCodTipoDecreto_" );
$obCmbTipoDecreto->setCampoId   ( "[cod_tipo_decreto]" );
$obCmbTipoDecreto->setCampoDesc ( "[descricao]" );
$obCmbTipoDecreto->setValue ( "cod_tipo_decreto" );
$obCmbTipoDecreto->preencheCombo( $rsTipoDecreto );
$obCmbTipoDecreto->setNull      ( false );

$obLista->addDadoComponente( $obCmbTipoDecreto );
$obLista->ultimoDado->setCampo( "cod_tipo_decreto" );
$obLista->commitDadoComponente();

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
$obHdnCodConta->setValue( "cod_conta"  );

$obLista->addDadoComponente( $obHdnCodConta );
$obLista->commitDadoComponente();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $_POST['inCodEntidade']  );

$obHdnCodGrupo= new Hidden;
$obHdnCodGrupo->setName ( "inCodGrupo" );
$obHdnCodGrupo->setValue( $_POST[ 'inCodGrupo' ]  );

//Define o objeto Label Entidade
$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo( "Entidade" );
$obLblCodEntidade->setValue( $_POST['inCodEntidade']." - $stNomEntidade" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda('UC-02.02.04');
$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnCodEntidade       );
$obFormulario->addHidden( $obHdnCodGrupo          );

$obFormulario->addTitulo( "Registros de saldos iniciais" );
$obFormulario->addComponente( $obLblCodEntidade);
$obFormulario->addComponente( $obCmbRegArt47);
$obFormulario->addComponente($obNroNormaReg);
$obFormulario->addComponente($obCmbRegExclusiva);
$obFormulario->addComponente($obTxtArtRegExclusiva);
$obFormulario->addComponente($obValorLimiteRegExclusiva);
$obFormulario->addComponente($obCmbProcSubContratacao);
$obFormulario->addComponente($obTxtArtProcSubContratacao);
$obFormulario->addComponente( $obNumPercentualSubContratacao);
$obFormulario->addComponente($obCmbCriteriosEmpenhoPagamento);
$obFormulario->addComponente($obTxtArtEmpenhoPagamento);
$obFormulario->addComponente($obCmbEstabeleceuPercContratacao);
$obFormulario->addComponente($obTxtArtPercentualContratacao);
$obFormulario->addComponente( $obNumPercentualContratacao );
$obFormulario->addLista($obLista);

$stLocation = $pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao ;
$obFormulario->Cancelar($stLocation);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
