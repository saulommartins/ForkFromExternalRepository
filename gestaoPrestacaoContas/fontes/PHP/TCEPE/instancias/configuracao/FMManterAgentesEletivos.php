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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira
    *
    $Id: FMManterAgentesEletivos.php 60292 2014-10-10 18:24:22Z carlos.silva $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_COMPONENTES.'IBuscaInnerNorma.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalCargo.class.php';

$stPrograma = "ManterAgentesEletivos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::write('cod_entidade', $_REQUEST['inCodEntidade']);

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST["stAcao"] );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($_REQUEST['inCodEntidade'] == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
    $stSchema = '';
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$_REQUEST['inCodEntidade']."'";
    $stSchema = '_'.$_REQUEST['inCodEntidade'];
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$_REQUEST['stAcao'], 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($_REQUEST['inCodEntidade'] == $inCodEntidadePrefeitura) {
    Sessao::setEntidade('');
} else {
    // Se não foi selecionada a entidade definida como prefeitura
    // ao executar as consultas, automaticamente é adicionado o "_" + cod_entidade selecionada
    $arSchemasRH = array();
    $obTEntidade->recuperaSchemasRH($rsSchemasRH);
    while (!$rsSchemasRH->eof()) {
        $arSchemasRH[] = $rsSchemasRH->getCampo("schema_nome");
        $rsSchemasRH->proximo();
    }
    Sessao::write('arSchemasRH', $arSchemasRH, true);

    Sessao::setEntidade($_REQUEST['inCodEntidade']);
}

//Monta Tipo de Remuneração
$arTipoRemuneracao[0]['cod_tipo_remuneracao']   = 1;
$arTipoRemuneracao[0]['descricao']              = 'Subsídio';
$arTipoRemuneracao[1]['cod_tipo_remuneracao']   = 2;
$arTipoRemuneracao[1]['descricao']              = 'Representação';

$rsTipoRemuneracao = new RecordSet();
$rsTipoRemuneracao->preenche($arTipoRemuneracao);

$obCmbTipoRemuneracao = new Select();
$obCmbTipoRemuneracao->setName      ('cmbTipoRemuneracao');
$obCmbTipoRemuneracao->setId        ('cmbTipoRemuneracao');
$obCmbTipoRemuneracao->setValue     ('[cod_tipo_remuneracao]');
$obCmbTipoRemuneracao->addOption    ('','Selecione');
$obCmbTipoRemuneracao->setRotulo    ('*Tipo de Remuneração TCE');
$obCmbTipoRemuneracao->setCampoId   ('[cod_tipo_remuneracao]');
$obCmbTipoRemuneracao->setCampodesc ('[descricao]');
$obCmbTipoRemuneracao->setTitle     ('Selecione o Tipo de Remuneração TCE-PE.');
$obCmbTipoRemuneracao->preencheCombo($rsTipoRemuneracao);

//Monta Tipo de Norma
$arTipoNorma[0]['cod_tipo_norma']   = 1;
$arTipoNorma[0]['descricao']        = 'Lei';
$arTipoNorma[1]['cod_tipo_norma']   = 2;
$arTipoNorma[1]['descricao']        = 'Resolução';
$arTipoNorma[2]['cod_tipo_norma']   = 3;
$arTipoNorma[2]['descricao']        = 'Outra';

$rsTipoNorma = new RecordSet();
$rsTipoNorma->preenche($arTipoNorma);

$obCmbTipoNorma = new Select();
$obCmbTipoNorma->setName        ('cmbTipoNorma');
$obCmbTipoNorma->setId          ('cmbTipoNorma');
$obCmbTipoNorma->setValue       ('[cod_tipo_norma]');
$obCmbTipoNorma->addOption      ('','Selecione');
$obCmbTipoNorma->setRotulo      ('*Tipo de Norma TCE');
$obCmbTipoNorma->setCampoId     ('[cod_tipo_norma]');
$obCmbTipoNorma->setCampodesc   ('[descricao]');
$obCmbTipoNorma->setTitle       ('Selecione o Tipo de Norma TCE-PE.');
$obCmbTipoNorma->preencheCombo  ($rsTipoNorma);

$obTipoNormaNorma = new IBuscaInnerNorma( false, true );
$obTipoNormaNorma->obBscNorma->setRotulo( "*Norma" );
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obSelect->setDisabled( true );
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obTextBox->setReadOnly( true );
$obTipoNormaNorma->obBscNorma->setTitle("Informe a norma autorizativa.");
$obTipoNormaNorma->obBscNorma->obCampoCod->obEvento->setOnChange("executaFuncaoAjax('preencherDetalhesNorma', '&nuExercicioNorma='+this.value);");

$stFiltro = "";
$obTPessoalCargo = new TPessoalCargo();
$obTPessoalCargo->listarCargos($rsListaCargos, $stFiltro," ORDER BY descricao");

$obCmbCargos = new SelectMultiplo();
$obCmbCargos->setName  ( 'arCargosSelecionadas' );
$obCmbCargos->setRotulo( "Cargos" );
$obCmbCargos->setNull  ( true );
$obCmbCargos->setObrigatorioBarra (true);
$obCmbCargos->setTitle ( 'Cargos Disponíveis' );

$obCmbCargos->SetNomeLista1( 'arCargosDisponiveis' );
$obCmbCargos->setCampoId1  ( '[cod_cargo]' );
$obCmbCargos->setCampoDesc1( '[cod_cargo] - [descricao]' );
$obCmbCargos->SetRecord1   ( $rsListaCargos  );

$obCmbCargos->SetNomeLista2( 'arCargosSelecionados' );
$obCmbCargos->setCampoId2  ( '[cod_cargo]' );
$obCmbCargos->setCampoDesc2( '[cod_cargo] - [descricao]' );
$obCmbCargos->SetRecord2   ( new RecordSet );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue( 'Incluir' );
$obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirAgenteLista','cmbTipoRemuneracao,cmbTipoNorma,stCodNorma,stNorma,arCargosSelecionados');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setValue( 'Limpar' );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparAgentesLista');" );

$obSpnLista = new Span;
$obSpnLista->setId  ( 'spnLista' );

$obOk  = new Ok;
$obOk->setId ("btnOk");

$obLimpar = new Button;
$obLimpar->setValue ( "Limpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTudo');" );

$obFormulario = new Formulario();
$obFormulario->addForm          ( $obForm );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addComponente    ( $obCmbTipoRemuneracao );
$obFormulario->addComponente    ( $obCmbTipoNorma       );
$obTipoNormaNorma->geraFormulario( $obFormulario        );
$obFormulario->addComponente    ( $obCmbCargos          );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar )  );
$obFormulario->addSpan          ( $obSpnLista           );
$obFormulario->defineBarra      ( array( $obOk, $obLimpar )             );

$obFormulario->show();

$jsOnLoad = "executaFuncaoAjax('agentesExistentes');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
