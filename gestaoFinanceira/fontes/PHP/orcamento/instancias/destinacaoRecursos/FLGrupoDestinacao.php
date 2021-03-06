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
    * Página de Formulario de Inclusao/Alteracao de Grupos de Destinação
    * Data de Criação   : 31/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: FLGrupoDestinacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "GrupoDestinacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

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

    $obTxtCod = new TextBox;
    $obTxtCod->setName     ( "inCodDestinacao"  );
    $obTxtCod->setRotulo   ( "Código do Grupo"           );
    $obTxtCod->setSize     ( 1 );
    $obTxtCod->setMaxLength( 1 );
    $obTxtCod->setInteiro  ( true               );
    $obTxtCod->setTitle    ( "Informe o Código do Grupo de Destinação de Recursos.");

    $obTxtDesc = new TextBox;
    $obTxtDesc->setName     ( "stDescricao"           );
    $obTxtDesc->setValue    ( $stDescricao            );
    $obTxtDesc->setRotulo   ( "Descrição"        );
    $obTxtDesc->setTitle    ( "Informe a descrição do Grupo de Descrição de Recursos.");
    $obTxtDesc->setSize     ( 80                 );
    $obTxtDesc->setMaxLength( 200                );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda( "UC-02.01.38"       );

$obFormulario->addHidden( $obHdnAcao              );

$obFormulario->addTitulo( "Dados para Filtro"     );
$obFormulario->addComponente( $obTxtCod    );
$obFormulario->addComponente( $obTxtDesc   );

$obFormulario->OK();

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$boDestinacao = $obRConfiguracaoOrcamento->getDestinacaoRecurso();

if ($boDestinacao == 'false') {
    SistemaLegado::exibeAviso("Ação não permitida. O sistema não está configurado para utilizar a Destinação de Recursos.","","erro");
    $obFormulario = new Formulario;
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
