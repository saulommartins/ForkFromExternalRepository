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
    * Página de Formulario de Ajustes Gerais Exportacao - MANAD
    * Data de Criação   : 16/11/2012
    *
    *
    * @author Analista: Gelson Gonçalves
    * @author Desenvolvedor: Matheus Figueredo
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_MANAD_MAPEAMENTO."TExportacaoMANADConfiguracao.class.php");
include_once(CAM_GPC_MANAD_NEGOCIO."RExportacaoMANAD.class.php");
include_once(CAM_GA_CGM_NEGOCIO."RResponsavelTecnico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ConfiguracaoMANAD";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

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

$obTExportacaoConfiguracao = new TExportacaoMANADConfiguracao;
$obRExportacaoMANAD = new RExportacaoMANAD;

$obTExportacaoConfiguracao->setDado("parametro",  "manad_cod_mun");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setName("inCodMun");
$obTxtCodMunicipio->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtCodMunicipio->setRotulo("Código do Munícípio");
$obTxtCodMunicipio->setTitle("Informe o código do Município conforme tabela do IBGE");
$obTxtCodMunicipio->setInteiro(true);
$obTxtCodMunicipio->setSize(6);
$obTxtCodMunicipio->setMaxLength("6");
$obTxtCodMunicipio->setNull(false);

//Gera a Lista de Finalidades
$obRExportacaoMANAD->geraListaFinalidades($rsListaFinalidades);

$obTExportacaoConfiguracao->setDado("parametro",  "manad_cod_fin");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obCmbCodFin = new Select;
$obCmbCodFin->setName("inCodFin");
$obCmbCodFin->setRotulo("* Finalidade do MANAD");
$obCmbCodFin->addOption("", "Selecione");
$obCmbCodFin->setCampoId("cod_fin");
$obCmbCodFin->setCampoDesc("[nom_fin]");
$obCmbCodFin->preencheCombo($rsListaFinalidades);
$obCmbCodFin->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obCmbCodFin->setTitle('Selecione uma Finalidade');

$obTExportacaoConfiguracao->setDado("parametro",  "manad_numcgm_contador_responsavel");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obRResponsavelTecnico = new RResponsavelTecnico;
$obRResponsavelTecnico->listarResponsavelContabil($rsResponsavelTecnico);

$obCmbResponsavelContabilidade = new Select;
$obCmbResponsavelContabilidade->setName("inCodResponsavelContabilidade");
$obCmbResponsavelContabilidade->setRotulo("Contador responsável");
$obCmbResponsavelContabilidade->addOption("", "Selecione");
$obCmbResponsavelContabilidade->setCampoId("[numcgm]");
$obCmbResponsavelContabilidade->setCampoDesc("[nom_profissao] - [nom_cgm]");
$obCmbResponsavelContabilidade->preencheCombo($rsResponsavelTecnico);
$obCmbResponsavelContabilidade->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obCmbResponsavelContabilidade->setTitle('Selecione o Contador Responsável');
$obCmbResponsavelContabilidade->setNull(false);

include_once( TLIC."TLicitacaoDocumento.class.php");
$obTLicitacaoDocumento = new TLICitacaoDocumento;
$obTLicitacaoDocumento->recuperaTodos( $rsDocINSS );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_documento_inss_fornecedor");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtDocINSS = new TextBox;
$obTxtDocINSS->setName  ( "inDocINSS" );
$obTxtDocINSS->setRotulo( "Documento do INSS" );
$obTxtDocINSS->setTitle ( "Selecione o documento." );
$obTxtDocINSS->setValue ( $obTExportacaoConfiguracao->getDado("valor") );

$obCmbDocINSS = new Select;
$obCmbDocINSS->setName      ( "stDocINSS" );
$obCmbDocINSS->setValue     ( $obTExportacaoConfiguracao->getDado("valor") );
$obCmbDocINSS->setRotulo    ( "Documento do INSS" );
$obCmbDocINSS->setTitle     ( "Selecione o documento." );
$obCmbDocINSS->setId        ( "stDocINSS" );
$obCmbDocINSS->setCampoID   ( 'cod_documento' );
$obCmbDocINSS->setCampoDesc ( 'nom_documento' );
$obCmbDocINSS->addOption    ( "", "Selecione" );
$obCmbDocINSS->preencheCombo( $rsDocINSS );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_orgao_prefeitura");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtOrgaoExecutivo = new TextBox;
$obTxtOrgaoExecutivo->setName        ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setValue    ($obTExportacaoConfiguracao->getDado("valor") );
$obTxtOrgaoExecutivo->setId          ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setRotulo      ( "Órgão Poder Executivo" );
$obTxtOrgaoExecutivo->setTitle       ( "Informe o código do orgão relativo ao poder executivo");
$obTxtOrgaoExecutivo->setInteiro     ( true );
$obTxtOrgaoExecutivo->setSize        ( 3 );
$obTxtOrgaoExecutivo->setMaxLength   ( "5" );
$obTxtOrgaoExecutivo->setNull        ( false );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_unidade_prefeitura");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtUnidadeExecutivo = new TextBox;
$obTxtUnidadeExecutivo->setName        ( "inCodUnidadeExecutivo" );
$obTxtUnidadeExecutivo->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtUnidadeExecutivo->setId          ( "inCodUnidadeExecutivo" );
$obTxtUnidadeExecutivo->setRotulo      ( "Unidade Poder Executivo" );
$obTxtUnidadeExecutivo->setTitle       ( "Informe o código do unidade relativo ao poder executivo");
$obTxtUnidadeExecutivo->setInteiro     ( true );
$obTxtUnidadeExecutivo->setSize        ( 3 );
$obTxtUnidadeExecutivo->setMaxLength   ( "5" );
$obTxtUnidadeExecutivo->setNull        ( false );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_orgao_camara");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtOrgaoLegislativo = new Textbox;
$obTxtOrgaoLegislativo->setName        ( "inCodOrgaoLegislativo" );
$obTxtOrgaoLegislativo->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtOrgaoLegislativo->setId          ( "inCodOrgaoLegislativo" );
$obTxtOrgaoLegislativo->setRotulo      ( "ÓrgãoPoder Legislativo" );
$obTxtOrgaoLegislativo->setTitle       ( "Informe o código do orgão relativo ao poder legislativo");
$obTxtOrgaoLegislativo->setInteiro     ( true );
$obTxtOrgaoLegislativo->setSize        ( 3 );
$obTxtOrgaoLegislativo->setMaxLength   ( "5" );
$obTxtOrgaoLegislativo->setNull        ( false );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_unidade_camara");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtUnidadeLegislativo = new Textbox;
$obTxtUnidadeLegislativo->setName        ( "inCodUnidadeLegislativo" );
$obTxtUnidadeLegislativo->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtUnidadeLegislativo->setId          ( "inCodUnidadeLegislativo" );
$obTxtUnidadeLegislativo->setRotulo      ( "Unidade Poder Legislativo" );
$obTxtUnidadeLegislativo->setTitle       ( "Informe o código do unidade relativo ao poder legislativo");
$obTxtUnidadeLegislativo->setInteiro     ( true );
$obTxtUnidadeLegislativo->setSize        ( 3 );
$obTxtUnidadeLegislativo->setMaxLength   ( "5" );
$obTxtUnidadeLegislativo->setNull        ( false );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_orgao_rpps");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtOrgaoRPPS = new Textbox;
$obTxtOrgaoRPPS->setName        ( "inCodOrgaoRPPS" );
$obTxtOrgaoRPPS->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtOrgaoRPPS->setId          ( "inCodOrgaoRPPS" );
$obTxtOrgaoRPPS->setRotulo      ( "Órgão RPPS" );
$obTxtOrgaoRPPS->setTitle       ( "Informe o código do orgão relativo ao RPPS");
$obTxtOrgaoRPPS->setInteiro     ( true );
$obTxtOrgaoRPPS->setSize        ( 3 );
$obTxtOrgaoRPPS->setMaxLength   ( "5" );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_unidade_rpps");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtUnidadeRPPS = new Textbox;
$obTxtUnidadeRPPS->setName        ( "inCodUnidadeRPPS" );
$obTxtUnidadeRPPS->setValue ($obTExportacaoConfiguracao->getDado("valor"));
$obTxtUnidadeRPPS->setId          ( "inCodUnidadeRPPS" );
$obTxtUnidadeRPPS->setRotulo      ( "Unidade RPPS" );
$obTxtUnidadeRPPS->setTitle       ( "Informe o código do unidade relativo ao RPPS");
$obTxtUnidadeRPPS->setInteiro     ( true );
$obTxtUnidadeRPPS->setSize        ( 3 );
$obTxtUnidadeRPPS->setMaxLength   ( "5" );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_orgao_outros");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtOrgaoOutros = new Textbox;
$obTxtOrgaoOutros->setName        ( "inCodOrgaoOutros" );
$obTxtOrgaoOutros->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtOrgaoOutros->setId          ( "inCodOrgaoOutros" );
$obTxtOrgaoOutros->setRotulo      ( "Órgão Outros" );
$obTxtOrgaoOutros->setTitle       ( "Informe o código do orgão para outros poderes");
$obTxtOrgaoOutros->setInteiro     ( true );
$obTxtOrgaoOutros->setSize        ( 3 );
$obTxtOrgaoOutros->setMaxLength   ( "5" );

$obTExportacaoConfiguracao->setDado("parametro",  "manad_unidade_outros");
$obTExportacaoConfiguracao->setDado("cod_modulo", 59);
$obTExportacaoConfiguracao->consultar();

$obTxtUnidadeOutros = new Textbox;
$obTxtUnidadeOutros->setName        ( "inCodUnidadeOutros" );
$obTxtUnidadeOutros->setValue($obTExportacaoConfiguracao->getDado("valor"));
$obTxtUnidadeOutros->setId          ( "inCodUnidadeOutros" );
$obTxtUnidadeOutros->setRotulo      ( "Unidade Outros" );
$obTxtUnidadeOutros->setTitle       ( "Informe o código do unidade para outros poderes");
$obTxtUnidadeOutros->setInteiro     ( true );
$obTxtUnidadeOutros->setSize        ( 3 );
$obTxtUnidadeOutros->setMaxLength   ( "5" );
//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addTitulo("Configuração do Bloco 0");
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addComponente($obTxtCodMunicipio);
$obFormulario->addComponente($obCmbCodFin);
$obFormulario->addComponente($obCmbResponsavelContabilidade);
$obFormulario->addComponenteComposto( $obTxtDocINSS, $obCmbDocINSS );
$obFormulario->addTitulo ( "Configuração de Órgão/Unidade"     );
$obFormulario->addComponente( $obTxtOrgaoExecutivo );
$obFormulario->addComponente( $obTxtUnidadeExecutivo );
$obFormulario->addComponente( $obTxtOrgaoLegislativo );
$obFormulario->addComponente( $obTxtUnidadeLegislativo );
$obFormulario->addComponente( $obTxtOrgaoRPPS );
$obFormulario->addComponente( $obTxtUnidadeRPPS );
$obFormulario->addComponente( $obTxtOrgaoOutros );
$obFormulario->addComponente( $obTxtUnidadeOutros );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
