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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 18/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoUnidadeGestora.class.php";
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAUnidadeGestora.class.php";
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

/*
require_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPAConfiguracao.class.php";
*/

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterUnidadeGestora";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Cria o campo text para a inclusão do dado da unidade gestora
$obTxtUnidadeGestora = new TextBox();
$obTxtUnidadeGestora->setName       ( 'inUnidadeGestora_[cod_entidade]' );
$obTxtUnidadeGestora->setId         ( 'inUnidadeGestora_[cod_entidade]' );
$obTxtUnidadeGestora->setValue      ( '[unidade_gestora]' );
$obTxtUnidadeGestora->setInteiro    ( true );
$obTxtUnidadeGestora->setMaxLength  ( 7 );
$obTxtUnidadeGestora->setSize       ( 7 );

// Cria a combo onde ficará os tipos de Unidades Gestoras
$obTTCMPATipoUnidadeGestora = new TTPATipoUnidadeGestora();
$obTTCMPATipoUnidadeGestora->recuperaTodos( $rsTTCMPATipoUnidadeGestora );

$obTipoUnidadeGestora = new  Select();
$obTipoUnidadeGestora->setName      ( 'codTipoUnidadeGestora_[cod_entidade]' );
$obTipoUnidadeGestora->setId        ( 'codTipoUnidadeGestora_[cod_entidade]' );
$obTipoUnidadeGestora->setValue     ( '[cod_tipo]' );
$obTipoUnidadeGestora->setCampoId   ( 'cod_tipo'  );
$obTipoUnidadeGestora->setCampoDesc ( 'descricao' );
$obTipoUnidadeGestora->addOption    ( '', 'Selecione' );
$obTipoUnidadeGestora->preencheCombo( $rsTTCMPATipoUnidadeGestora );

/*
// Cria a IpopUp de CGM onde receberá o numgcm responsável
$obResponsavelJuridico = new IPopUpCGMVinculado( $obForm );
$obResponsavelJuridico->setTabelaVinculo    ( 'sw_cgm_pessoa_juridica' );
$obResponsavelJuridico->setCampoVinculo     ( 'numcgm' );
$obResponsavelJuridico->setNomeVinculo      ( 'Responsavel' );
$obResponsavelJuridico->setName             ( 'stResponsavelJuridico_[num_orgao]');
$obResponsavelJuridico->setId               ( 'stResponsavelJuridico_[num_orgao]');
$obResponsavelJuridico->obCampoCod->setName ( 'inCodRespJuridico_[num_orgao]' );
$obResponsavelJuridico->obCampoCod->setId   ( 'inCodRespJuridico_[num_orgao]' );
$obResponsavelJuridico->obCampoCod->setValue( '[numcgm]' );
$obResponsavelJuridico->obCampoCod->setNull ( true );
$obResponsavelJuridico->setNull             ( false );
$obResponsavelJuridico->setMostrarDescricao ( false );
*/

// Faz as buscas das entidades para montar as listagens
$obTTPAUnidadeGestora = new TTPAUnidadeGestora();
$obTTPAUnidadeGestora->setDado( 'exercicio', Sessao::getExercicio() );
$obTTPAUnidadeGestora->recuperaListagemEntidades( $rsOrcamentoEntidade );

// Monta a table para a listagem de todos os campos */
$table = new Table   ();
$table->setRecordset  ( $rsOrcamentoEntidade );
$table->setSummary    ('Itens');
//$table->setConditional( true , "#ddd" );

$table->Head->addCabecalho( 'Órgão' , 80  );
$table->Head->addCabecalho( 'Unidade Gestora' , 5  );
$table->Head->addCabecalho( 'Tipo de Unidade Gestora' , 30 );
/*
$table->Head->addCabecalho( 'Responsável Jurídico' , 20  );
*/

$table->Body->addCampo      ( '[cod_entidade] - [descricao]' , 'E');
$table->Body->addComponente ( $obTxtUnidadeGestora  );
$table->Body->addComponente ( $obTipoUnidadeGestora );
/*
$table->Body->addComponente ($obResponsavelJuridico);
*/

$table->montaHTML();
$stLista = $table->getHTML();

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addSpan( $obSpnLista );
$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
