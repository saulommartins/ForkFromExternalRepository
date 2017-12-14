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
 * @author Analista: Carlos Adriano
 * @author Desenvolvedor: Carlos Adriano
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";
include_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoOrgao.class.php";

$stPrograma = "ManterConfiguracaoProjecaoAtuarial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include ($pgJs);

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "incluir";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if (isset($inCodigo)) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTOrcamentoOrgao = new TOrcamentoOrgao();
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoOrgao->recuperaDadosExercicio( $rsOrgao );

$obCmbOrgao = new Select();
$obCmbOrgao->setRotulo( 'Orgão' );
$obCmbOrgao->setTitle( 'Selecione o orgão' );
$obCmbOrgao->setName( 'inOrgao' );
$obCmbOrgao->setId( 'inOrgao' );
$obCmbOrgao->addOption( '', 'Selecione' );
$obCmbOrgao->setCampoId( 'num_orgao' );
$obCmbOrgao->setCampoDesc( 'nom_orgao' );
$obCmbOrgao->setStyle('width: 520');
$obCmbOrgao->obEvento->setOnChange('buscaLista(this);');
$obCmbOrgao->setNull(false);
$obCmbOrgao->preencheCombo( $rsOrgao );

$obSpanLista = new Span;
$obSpanLista->setId('spnLista');

//****************************************//
// Monta formulário
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addTitulo('Configuração da projeção atuarial');

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

$obFormulario->addComponente($obCmbOrgao);
$obFormulario->addSpan( $obSpanLista );

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
