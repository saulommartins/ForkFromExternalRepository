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
    * Página de Filtro do relatório de Configuração de Lançamento de Receita
    * Data de Criação: 17/11/2011

    * @author Analista Tonismar Bernardo
    * @author Desenvolvedor Davi Aroldi

    * @ignore

    $Id: FLRelatorioConfiguracaoLancamentoReceita.php  $

    * Casos de uso: uc-02.03.18

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioConfiguracaoLancamentoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obROrcamentoReceita = new ROrcamentoReceita;
$stMascaraRubrica  = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obBscRubricaReceita = new BuscaInner;
$obBscRubricaReceita->setRotulo               ( "Elemento de Receita" );
$obBscRubricaReceita->setTitle                ( "Informe o elemento de receita para filtro." );
$obBscRubricaReceita->setId                   ( "stDescricaoReceita" );
$obBscRubricaReceita->obCampoCod->setName     ( "inCodReceita" );
$obBscRubricaReceita->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaReceita->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaReceita->obCampoCod->setValue    ( '' );
$obBscRubricaReceita->obCampoCod->setAlign    ("left");
$obBscRubricaReceita->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaReceita->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaReceita->obCampoCod->obEvento->setOnBlur ("buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscRubricaReceita->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaoreceita/FLClassificacaoReceita.php','frm','inCodReceita','stDescricaoReceita','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

$obForm = new Form;
$obForm->setAction( $pgGera );
$obForm->setTarget( "telaPrincipal" );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addHidden( $obHdnMascClassificacao );
$obFormulario->addComponente( $obBscRubricaReceita );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
