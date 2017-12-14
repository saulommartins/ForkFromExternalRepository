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
 * @author Analista: Dagiane Vieira
 * @author Desenvolvedor: Michel Teixeira
 *
 * $Id: FMManterConfiguracaoProjecaoAtuarial.php 61820 2015-03-06 16:15:57Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoConfiguracao.class.php';
include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoEntidade.class.php';

$stPrograma = "ManterConfiguracaoProjecaoAtuarial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include ($pgJs);

$stAcao = $request->get('stAcao');

if (empty($stAcao))
    $stAcao = "incluir";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnExercicios = new Hidden;
$obHdnExercicios->setName( "stExercicios" );
$obHdnExercicios->setId( "stExercicios" );
$obHdnExercicios->setValue( "" );

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio( Sessao::getExercicio() );
$obRConfiguracaoOrcamento->consultarConfiguracao();

$rsEntidade = new RecordSet();

if($obRConfiguracaoOrcamento->getCodEntidadeRPPS()){
    $obREntidadeOrcamento     = new ROrcamentoEntidade;
    $obREntidadeOrcamento->setExercicio( Sessao::getExercicio() );
    $obREntidadeOrcamento->setCodigoEntidade( $obRConfiguracaoOrcamento->getCodEntidadeRPPS() );
    $obREntidadeOrcamento->listar( $rsEntidade );
}

$obTxtCodigoEntidadeRPPS = new TextBox;
$obTxtCodigoEntidadeRPPS->setName       ( "inCodEntidadeRPPS"           );
$obTxtCodigoEntidadeRPPS->setId         ( "inCodEntidadeRPPS"           );
$obTxtCodigoEntidadeRPPS->setValue      ( ''                            );
$obTxtCodigoEntidadeRPPS->setRotulo     ( "Entidade RPPS"               );
$obTxtCodigoEntidadeRPPS->setTitle      ( "Selecione a entidade."       );
$obTxtCodigoEntidadeRPPS->obEvento->setOnChange( "buscaLista(this);"    );
$obTxtCodigoEntidadeRPPS->setInteiro    ( true                          );
$obTxtCodigoEntidadeRPPS->setNull       ( false                         );

$obCmbNomeEntidadeRPPS = new Select;
$obCmbNomeEntidadeRPPS->setName         ( "stNomeEntidadeRPPS"      );
$obCmbNomeEntidadeRPPS->setId           ( "stNomeEntidadeRPPS"      );
$obCmbNomeEntidadeRPPS->setValue        ( ''                        );
$obCmbNomeEntidadeRPPS->addOption       ( "", "Selecione"           );
$obCmbNomeEntidadeRPPS->obEvento->setOnChange( "buscaLista(this);"  );
$obCmbNomeEntidadeRPPS->setCampoId      ( "cod_entidade"            );
$obCmbNomeEntidadeRPPS->setCampoDesc    ( "nom_cgm"                 );
$obCmbNomeEntidadeRPPS->setStyle        ( "width: 520"              );
$obCmbNomeEntidadeRPPS->preencheCombo   ( $rsEntidade               );
$obCmbNomeEntidadeRPPS->setNull         ( false                     );

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
$obFormulario->addHidden($obHdnExercicios);

$obFormulario->addComponenteComposto    ( $obTxtCodigoEntidadeRPPS, $obCmbNomeEntidadeRPPS );
$obFormulario->addSpan( $obSpanLista );

$obOk = new Ok(true);
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
