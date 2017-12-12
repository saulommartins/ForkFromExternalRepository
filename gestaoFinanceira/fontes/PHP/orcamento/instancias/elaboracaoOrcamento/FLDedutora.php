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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 10/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    * Casos de uso: uc-02.01.06

    $Id: FLDedutora.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpEstruturalReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Dedutora";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obROrcamentoReceita = new ROrcamentoReceita;

//Recupera Mascara da Classificao de Receita

                     $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora( true );
$mascClassificacao = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );
$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Informe a descrição.' );

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
if($stAcao != 'incluir') $obIMontaRecursoDestinacao->setFiltro( true );

$obIPopUpEstruturalReceita = new IPopUpEstruturalReceita( $boDedutora = true);

$obIIntervaloPopUpReceita = new IIntervaloPopUpReceita('', $boDedutora = true);

//Instancia o objeto da classe que cria a seleçao de entidade
$obISelectEntidadeGeral = new ISelectMultiploEntidadeUsuario();
$obISelectEntidadeGeral->setNull ( true );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.06"           );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnMascClassificacao );

$obFormulario->addTitulo( "Dados para Filtro"        );
$obFormulario->addComponente( $obISelectEntidadeGeral );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obIIntervaloPopUpReceita  );
$obFormulario->addComponente( $obIPopUpEstruturalReceita );
$obFormulario->addComponente( $obTxtDesc             );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
