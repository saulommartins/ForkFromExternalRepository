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
    * Página de Formulário Configuração das contas do relatório RGF 2
    * Data de Criação   : 28/05/2013

    * @author Analista: Valtair Lacerda
    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaDespesa.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

$stPrograma = "ConfigurarRGF1";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

// BUSCA DADO SALVO NA CONFIGURAÇÃO
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado("cod_modulo" , '36');
$obTAdministracaoConfiguracao->setDado("parametro"  , 'stn_rgf1_despesas_exercicios_anteriores');
$obTAdministracaoConfiguracao->recuperaPorChave($rsConfiguracao);

// LISTA ELEMENTOS DE DESPESA FILTRANDO POR EXERCICIO
$obTOrcamentoContaDespesa = new TOrcamentoContaDespesa;
$obTOrcamentoContaDespesa->recuperaTodos( $rsContaDespesa, " WHERE cod_estrutural LIKE '3.1.9.0.92%' and exercicio = '".Sessao::getExercicio()."' ");
   
//ELEMENTO DESPESA
$obCmbCodDespesa = new Select;
$obCmbCodDespesa->setRotulo     ( 'Elemento de Despesa' );
$obCmbCodDespesa->setName       ( 'inCodDespesa' );
$obCmbCodDespesa->setId         ( 'inCodDespesa' );
$obCmbCodDespesa->setValue      ( $rsConfiguracao->getCampo('valor') );
$obCmbCodDespesa->addOption     ( '', 'Selecione' );
$obCmbCodDespesa->setCampoId    ( 'cod_estrutural' );
$obCmbCodDespesa->setCampoDesc  ( '[cod_estrutural] - [descricao]' );
$obCmbCodDespesa->setStyle      ( 'width: 520' );
$obCmbCodDespesa->preencheCombo ( $rsContaDespesa );
$obCmbCodDespesa->setNull       ( true );

$obFormulario->addTitulo( 'Configurar Despesas de Exercícios Anteriores' );
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addComponente($obCmbCodDespesa);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
