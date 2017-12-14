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
/*
   * Filtro para Vinculo de unidade orcamentaria da GF e organograma
   * Data de Criação: 22/10/2015

   * @author Analista Dagiane Vieira 
   * @author Desenvolvedor Lisiane da Rosa Morais  

   * @package URBEM
   * @subpackage

   * @ignore

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
require_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrgao.class.php';
require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalDeParaLotacaoOrgaoTCMBA.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
require_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoOrgao.class.php';

$stPrograma = 'RelacionarLotacoesOrgaos';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

//require $pgJs;

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

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

$obHdnSchema = new Hidden;
$obHdnSchema->setName ('stSchema');
$obHdnSchema->setValue($stSchema);

$obTOrganogramaOrgao = new TOrganogramaOrgao;
$obTOrganogramaOrgao->setDado('stEntidade' , Sessao::getEntidade());
$obTOrganogramaOrgao->recuperaLotacaoOrgao($rsOrgao);

$obTOrcamentoOrgao = new TOrcamentoOrgao;
$obTOrcamentoOrgao->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoOrgao->recuperaDadosExercicio($rsOrcamentoOrgao, '', ' ORDER BY orgao.num_orgao');

$obCmbOrgao = new Select;
$obCmbOrgao->setId        ('cmbOrgao_[cod_orgao]');
$obCmbOrgao->setName      ('cmbOrgao_[cod_orgao]');
$obCmbOrgao->setCampoId   ('[num_orgao]');
$obCmbOrgao->setCampoDesc ('[nom_orgao]');
$obCmbOrgao->addOption    ('','Selecione');
$obCmbOrgao->preencheCombo($rsOrcamentoOrgao);
$obCmbOrgao->setValue     ('[num_orgao]');

//cria uma table para demonstrar os valores para o vinculo
$obTable = new Table;
$obTable->setId('tblOrgao');
$obTable->setRecordset  ($rsOrgao);
$obTable->addLineNumber (true);

$obTable->Head->addCabecalho('Lotação do Servidor' , 50);
$obTable->Head->addCabecalho('Órgão Orçamentário'  , 15);

$obTable->Body->addCampo('[estrutural] - [descricao]', 'E');
$obTable->Body->addCampo($obCmbOrgao, 'E');

$obTable->montaHTML(true);
$stHTML = $obTable->getHtml();

$obSpnLista = new Span;
$obSpnLista->setId   ('spnLista');
$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addSpan  ($obSpnLista);
$obFormulario->Cancelar ($pgFilt.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
