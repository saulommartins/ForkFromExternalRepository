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
    * Data de Criação   : 19/05/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id:$

    * Casos de uso:uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
require_once CAM_GRH_PES_MAPEAMENTO."TPessoalRegime.class.php";

include_once 'JSManterCargoSituacaoFuncional.js';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargoSituacaoFuncional";
$pgFilt     = "FL".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$pgForm     = "FM".$stPrograma.".php";

$stAcao = $_REQUEST["acao"];

//Monta os componentes
if ($_REQUEST['inCodEntidade'] == "") {

    // Busca pelas entidades
    $obTEntidade = new TEntidade();
    $stFiltro  = " AND exercicio = ".Sessao::getExercicio();

    $obTEntidade->recuperaEntidades($rsEntidades,$stFiltro);

    if ($rsEntidades->getNumLinhas() == 1) {
        $inCodEntidade = $rsEntidades->getCampo("cod_entidade");
    }

    $obCmbEntidade = new Select;
    $obCmbEntidade->setRotulo                        ( "Entidade");
    $obCmbEntidade->setTitle                         ( "Selecione a entidade");
    $obCmbEntidade->setName                          ( "inCodEntidade"                      );
    $obCmbEntidade->setValue                         ( $inCodEntidade                       );
    $obCmbEntidade->setStyle                         ( "width: 400px"                       );
    $obCmbEntidade->addOption                        ( "", "Selecione"                      );
    $obCmbEntidade->setNull(false);
    $obCmbEntidade->setCampoId("cod_entidade");
    $obCmbEntidade->setCampoDesc("nom_cgm");
    $obCmbEntidade->preencheCombo($rsEntidades);

} else {

    $inCodEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura",8,Sessao::getExercicio());

    if ($inCodEntidadePrefeitura == $_REQUEST['inCodEntidade']) {
        Sessao::setEntidade("");
    } else {
        Sessao::setEntidade($_REQUEST['inCodEntidade']);
    }

    $obTEntidade = new TEntidade();
    $stFiltro.= " AND exercicio = ".Sessao::getExercicio();
    $stFiltro.= " AND entidade.cod_entidade = ".$_REQUEST['inCodEntidade'];

    $obTEntidade->recuperaEntidades($rsEntidades,$stFiltro);

    $obLblEntidade = new Label;
    $obLblEntidade->setRotulo('Entidade');
    $obLblEntidade->setValue( $_REQUEST['inCodEntidade'].' - '.$rsEntidades->getCampo('nom_cgm'));

    $obTPessoalRegime = new TPessoalRegime();
    $obTPessoalRegime->recuperaTodos( $rsPessoalRegime );

    $obCmbPessoalRegime = new Select;
    $obCmbPessoalRegime->setRotulo                        ( "Regime");
    $obCmbPessoalRegime->setTitle                         ( "Selecione o Regime");
    $obCmbPessoalRegime->setName                          ( "inCodRegime");
    $obCmbPessoalRegime->setId                            ( "idCodRegime");
    $obCmbPessoalRegime->setValue                         ( $_REQUEST['inCodRegime']);
    $obCmbPessoalRegime->setStyle                         ( "width: 400px");
    $obCmbPessoalRegime->addOption                        ( "", "Selecione");
    $obCmbPessoalRegime->setNull(false);
    $obCmbPessoalRegime->setCampoId("cod_regime");
    $obCmbPessoalRegime->obEvento->setOnChange('montaParametrosGET( \'carregaComboSubdivisao\', \'inCodRegime\');');
    $obCmbPessoalRegime->setCampoDesc("descricao");
    $obCmbPessoalRegime->preencheCombo($rsPessoalRegime);

    $obSpanSubDivisao = new Span();
    $obSpanSubDivisao->setID( 'spnSubDivisao' );

    $obSpanFormularioCargo = new Span();
    $obSpanFormularioCargo->setID( 'spnFormularioCargo' );
}

//DEFINICAO DOS COMPONENTES HIDDEN
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao");
$obHdnAcao->setValue                            ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl");
$obHdnCtrl->setValue                            ( $stCtrl );

//DEFINICAO DO FORM
$obForm = new Form;
if ($_REQUEST['inCodEntidade'] == "") {
    $obForm->setAction                              ( $pgForm);
} else {
    $obForm->setAction                              ( $pgProc);
    $obForm->setTarget                              ( "oculto");
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addTitulo("Regime e Sub-Divisão");
$obFormulario->addForm                          ( $obForm );
$obFormulario->addHidden                        ( $obHdnAcao);
$obFormulario->addHidden                        ( $obHdnCtrl);

// Se houver entidade selecionada monta a combo de regime
if ($_REQUEST['inCodEntidade'] == "") {
    $obFormulario->addComponente($obCmbEntidade);
} else {
    $obFormulario->addComponente($obLblEntidade);
    $obFormulario->addComponente($obCmbPessoalRegime);
    $obFormulario->addSpan($obSpanSubDivisao);
    $obFormulario->addSpan($obSpanFormularioCargo);
}

$obFormulario->ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
