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
    * Página de Formulario de Manter Configuração e-Sfinge
    * Data de Criação: 27/04/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-02.09.01
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                      );
include_once( CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php");
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                 );
include_once( CAM_GF_PPA_MAPEAMENTO."TPPAConfiguracao.class.php"  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include($pgJs);

$sessao->transf['assinaturas'] = array();

$obTPPAConfiguracao = new TPPAConfiguracao();
$obTPPAConfiguracao->recuperaUltimaConfiguracao($rsConfiguracao);

$stTipoInclusao = '';
$inCodNorma     = '';
$stAnoInicial   = '';
$stAnoFinal     = '';

if ($rsConfiguracao->getNumLinhas()>0) {
   $stTipoInclusao = $rsConfiguracao->getCampo('pre_inclusao') == 't' ? 'P' : 'I';
   $inCodNorma     = $rsConfiguracao->getCampo('cod_norma');
   $stAnoInicial   = $rsConfiguracao->getCampo('ano_inicio');
   $stAnoFinal     = $rsConfiguracao->getCampo('ano_final');
   $inCGMVeiculo   = $rsConfiguracao->getCampo('cgm_veiculo_publicidade');
   $stNomVeiculo   = $rsConfiguracao->getCampo('nom_cgm');
   $stDtEncaminhamento = $rsConfiguracao->getCampo('dt_encaminhamento');
   $stDtDevolucao  = $rsConfiguracao->getCampo('dt_devolucao');
   $stNroProtocolo = $rsConfiguracao->getCampo('nro_protocolo');
   $stPeriodiciade = $rsConfiguracao->getCampo('periodicidade');
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnNormaAnterior = new Hidden;
$obHdnNormaAnterior->setId ("inCodNormaAnterior");
$obHdnNormaAnterior->setName("inCodNormaAnterior");
if($stTipoInclusao == 'I')
   $obHdnNormaAnterior->setValue( $inCodNorma );

$obCmbTipoInclusao = new Select();
$obCmbTipoInclusao->setRotulo ("Tipo de Inclusão");
$obCmbTipoInclusao->setName("stTipoInclusao");
$obCmbTipoInclusao->setId("stTipoInclusao");
$obCmbTipoInclusao->setTitle("Selecione o Tipo de Inclusão.");
$obCmbTipoInclusao->addOption("", "Selecione");
$obCmbTipoInclusao->addOption("P", "Pré-Inclusão");
$obCmbTipoInclusao->addOption("I", "Inclusão");
$obCmbTipoInclusao->setValue($stTipoInclusao);

$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setNull(true);
$obIPopUpNorma->setExibeDataNorma( true );
$obIPopUpNorma->setExibeDataPublicacao( true );
$obIPopUpNorma->setCodNorma($inCodNorma);

$obTxtAnoInicio = new TextBox();
$obTxtAnoInicio->setRotulo    ("Ano Inicial PPA");
$obTxtAnoInicio->setObrigatorio ( true );
$obTxtAnoInicio->setName      ("stAnoInicio");
$obTxtAnoInicio->setId        ("stAnoInicio");
$obTxtAnoInicio->setTitle     ("Informe o ano de início deste PPA.");
$obTxtAnoInicio->setMaxLength ( 4 );
$obTxtAnoInicio->setValue     ( $stAnoInicial );
$obTxtAnoInicio->obEvento->setOnBlur("if(this.value.length>3)  document.getElementById('stAnoFinal').innerHTML = parseInt(this.value)+4; else { document.getElementById('stAnoFinal').innerHTML = '&nbsp;'; document.frm.stAnoInicio.value = ''; } ");

$obLblAnoFinal = new Label;
$obLblAnoFinal->setRotulo("Ano Final PPA");
$obLblAnoFinal->setName  ("stAnoFinal");
$obLblAnoFinal->setId  ("stAnoFinal");
if ($stAnoFinal) {
   $obLblAnoFinal->setValue($stAnoFinal);
} else {
   $obLblAnoFinal->setValue("&nbsp;");
}

$obDtEncaminhamento = new Data();
$obDtEncaminhamento->setRotulo    ("Data Encaminhamento Legislativo");
$obDtEncaminhamento->setName      ("stDtEncaminhamento");
$obDtEncaminhamento->setTitle     ("Informe a Data de Encaminhamento do PPA ao Legislativo.");
$obDtEncaminhamento->setValue     ( $stDtEncaminhamento );

$obTxtNrProtocolo = new TextBox();
$obTxtNrProtocolo->setRotulo    ("Nr. Protocolo");
$obTxtNrProtocolo->setName      ("stNroProtocolo");
$obTxtNrProtocolo->setId        ("stNroProtocolo");
$obTxtNrProtocolo->setTitle     ("Informe o Nr. do Protocolo de Encaminhamento do PPA.");
$obTxtNrProtocolo->setMascara   ("999999/9999");
$obTxtNrProtocolo->obEvento->setOnBlur("if (!verificaProtocolo(this)) { this.value = '' }");
$obTxtNrProtocolo->setValue     ( $stNroProtocolo );

$obDtDevolucao = new Data();
$obDtDevolucao->setRotulo    ("Data de Devolução ao Executivo");
$obDtDevolucao->setName      ("stDtDevolucao");
$obDtDevolucao->setTitle     ("Informe a Data de Devolução do PPA ao Executivo.");
$obDtDevolucao->setValue     ( $stDtDevolucao );

$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( 'Veículo de Publicação'         );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->setNull                ( true                             );
$obVeiculoPublicidade->obCampoCod->setValue   ( $inCGMVeiculo                    );
$obVeiculoPublicidade->setValue               ( $stNomVeiculo                    );

$obCmbPeriodicidade = new Select();
$obCmbPeriodicidade->setRotulo ("Periodicidade Apuração de Metas");
$obCmbPeriodicidade->setName("stPeriodicidade");
$obCmbPeriodicidade->setId("stPeriodicidade");
$obCmbPeriodicidade->setTitle("Selecione a Periodicidade da Apuração de Metas.");
$obCmbPeriodicidade->addOption("", "Selecione");
$obCmbPeriodicidade->addOption("Mensal", "Mensal");
$obCmbPeriodicidade->addOption("Bimestral", "Bimestral");
$obCmbPeriodicidade->addOption("Trimestral", "Trimestral");
$obCmbPeriodicidade->addOption("Qadrimestral", "Quadrimestral");
$obCmbPeriodicidade->addOption("Semestral", "Semestral");
$obCmbPeriodicidade->setValue ( $stPeriodiciade );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnNormaAnterior );
$obFormulario->addTitulo( 'Dados para Configuração Inicial do PPA' );
$obFormulario->addComponente( $obCmbTipoInclusao );
$obIPopUpNorma->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obTxtAnoInicio );
$obFormulario->addComponente( $obLblAnoFinal );
$obFormulario->addTitulo( 'Outros Dados' );
$obFormulario->addComponente( $obDtEncaminhamento);
$obFormulario->addComponente( $obTxtNrProtocolo );
$obFormulario->addComponente( $obDtDevolucao);
$obFormulario->addComponente( $obVeiculoPublicidade );
$obFormulario->addComponente( $obCmbPeriodicidade );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limpaFormulario()" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
// include_once( $pgJs );
?>
