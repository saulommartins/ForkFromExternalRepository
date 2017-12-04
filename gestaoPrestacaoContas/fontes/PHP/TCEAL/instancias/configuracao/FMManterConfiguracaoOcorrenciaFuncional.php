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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 17/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalAssentamentoAssentamento.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOcorrenciaFuncional.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';

$stPrograma = "ManterConfiguracaoOcorrenciaFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);

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

$obTTCEALConfiguracaoOcorrenciaFuncional = new TTCEALConfiguracaoOcorrenciaFuncional();
$obTTCEALConfiguracaoOcorrenciaFuncional->recuperaTodos($rsOcorrenciaFuncional, " WHERE exercicio = '".Sessao::getExercicio()."'", " ORDER BY cod_ocorrencia");

$obCmbOcorrenciaFuncional = new Select();
$obCmbOcorrenciaFuncional->setName             ('cmbOcorrenciaFuncional');
$obCmbOcorrenciaFuncional->setId               ('cmbOcorrenciaFuncional');
$obCmbOcorrenciaFuncional->setValue            ('[cod_ocorrencia]');
$obCmbOcorrenciaFuncional->addOption           ('','Selecione');
$obCmbOcorrenciaFuncional->setRotulo           ('*Ocorrência Funcional');
$obCmbOcorrenciaFuncional->setCampoId          ('[cod_ocorrencia]');
$obCmbOcorrenciaFuncional->setCampodesc        ('[descricao]');
$obCmbOcorrenciaFuncional->setTitle            ('Informe a Ocorrência Funcional.');
$obCmbOcorrenciaFuncional->preencheCombo       ($rsOcorrenciaFuncional);

$stFiltro = " WHERE cod_motivo NOT IN (2,5,6,7,11,12,13)";
$obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
$obTPessoalAssentamentoAssentamento->recuperaTodos($rsAssentamentos, $stFiltro);

$obCmbAssentamentos = new SelectMultiplo();
$obCmbAssentamentos->setName  ( 'arAssentamentosSelecionadas' );
$obCmbAssentamentos->setRotulo( "Assentamentos" );
$obCmbAssentamentos->setNull  ( true );
$obCmbAssentamentos->setObrigatorioBarra (true);
$obCmbAssentamentos->setTitle ( 'Assentamentos Disponíveis' );

$obCmbAssentamentos->SetNomeLista1( 'arAssentamentosDisponiveis' );
$obCmbAssentamentos->setCampoId1  ( '[cod_assentamento]' );
$obCmbAssentamentos->setCampoDesc1( '[descricao]' );
$obCmbAssentamentos->SetRecord1   ( $rsAssentamentos  );

$obCmbAssentamentos->SetNomeLista2( 'arAssentamentosSelecionados' );
$obCmbAssentamentos->setCampoId2  ( '[cod_assentamento]' );
$obCmbAssentamentos->setCampoDesc2( '[descricao]' );
$obCmbAssentamentos->SetRecord2   ( new RecordSet );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue( 'Incluir' );
$obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirAssentamentoLista','cmbOcorrenciaFuncional,arAssentamentosSelecionados');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setValue( 'Limpar' );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparOcorrenciasLista');" );

$obSpnLista = new Span;
$obSpnLista->setId  ( 'spnLista' );

$obOk  = new Ok;
$obOk->setId ("btnOk");

$obLimpar = new Button;
$obLimpar->setValue ( "Limpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTudo');" );

$obFormulario = new Formulario();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addComponente    ($obCmbOcorrenciaFuncional);
$obFormulario->addComponente    ($obCmbAssentamentos);
$obFormulario->agrupaComponentes(array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan          ($obSpnLista);

$obFormulario->defineBarra( array( $obOk,$obLimpar ) );

$obFormulario->show();

$jsOnLoad = "executaFuncaoAjax('ocorrenciasExistentes');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
