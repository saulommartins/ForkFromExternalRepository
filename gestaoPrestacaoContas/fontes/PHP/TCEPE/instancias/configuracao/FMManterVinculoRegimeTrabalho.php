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
 * Formulário de Vinculo de Tipo de Regime de Trabalho e Regime/Subdivisão
 * Data de Criação: 29/09/2014

 * @author Desenvolvedor Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @package URBEM
 * @subpackage

 * @ignore

 $Id: $
 
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_FW_COMPONENTES.'/Table/Table.class.php';

# Mapeamentos
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
require_once CAM_GRH_PES_MAPEAMENTO.'TPessoalVinculoRegimeSubdivisao.class.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoRegimeTrabalho.class.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoVinculo.class.php';

$stPrograma = 'ManterVinculoRegimeTrabalho';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

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

$inCodEntidade = $_REQUEST['inCodEntidade'];

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($inCodEntidade == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$_REQUEST['stAcao'], 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($inCodEntidade == $inCodEntidadePrefeitura) {
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

# Recupera Regime de Trabalho 
$obTTCEPETipoRegimeTrabalho = new TTCEPETipoRegimeTrabalho;
$obTTCEPETipoRegimeTrabalho->recuperaTodos($rsTipoRegimeTrabalho, '', ' ORDER BY cod_tipo_regime');

# Recupera Vinculo 
$obTTCEPETipoVinculo = new TTCEPETipoVinculo;
$obTTCEPETipoVinculo->recuperaTodos($rsTipoVinculo, '', ' ORDER BY cod_tipo_vinculo');

# Recupera de-para
$obTPessoalVinculoRegimeSubdivisao = new TPessoalVinculoRegimeSubdivisao;
$obTPessoalVinculoRegimeSubdivisao->recuperaVinculoRegimeSubdivisao($rsVinculoRegimeSubDivisao, '', ' ORDER BY sub_divisao.cod_regime, sub_divisao.cod_sub_divisao ');

# Select de Regime de Trabalho
$obCmbTipoCargo = new Select;
$obCmbTipoCargo->setId        ('cmbCargo_[cod_sub_divisao]');
$obCmbTipoCargo->setName      ('cmbCargo_[cod_sub_divisao]');
$obCmbTipoCargo->setCampoId   ('[cod_tipo_regime]');
$obCmbTipoCargo->setCampoDesc ('[descricao]');
$obCmbTipoCargo->addOption    ('','Selecione');
$obCmbTipoCargo->preencheCombo($rsTipoRegimeTrabalho);
$obCmbTipoCargo->setValue     ('[cod_tipo_regime]');
$obCmbTipoCargo->setStyle     ('width: 150px');

# Select de Vinculo
$obCmbTipoVinculo = new Select;
$obCmbTipoVinculo->setId        ('cmbVinculo_[cod_sub_divisao]');
$obCmbTipoVinculo->setName      ('cmbVinculo_[cod_sub_divisao]');
$obCmbTipoVinculo->setCampoId   ('[cod_tipo_vinculo]');
$obCmbTipoVinculo->setCampoDesc ('[descricao]');
$obCmbTipoVinculo->addOption    ('','Selecione');
$obCmbTipoVinculo->preencheCombo($rsTipoVinculo);
$obCmbTipoVinculo->setValue     ('[cod_tipo_vinculo]');
$obCmbTipoVinculo->setStyle     ('width: 400px');

# Monta tabela com os componentes
$obTable = new Table;
$obTable->setRecordset  ($rsVinculoRegimeSubDivisao);
$obTable->addLineNumber (true);

$obTable->Head->addCabecalho('Regime', 5);
$obTable->Head->addCabecalho('Descrição', 25);
$obTable->Head->addCabecalho('Regime de Trabalho', 10);
$obTable->Head->addCabecalho('Vínculo', 10);

$obTable->Body->addCampo('[descricao_regime]', 'C');
$obTable->Body->addCampo('[cod_sub_divisao] - [descricao]', 'E');
$obTable->Body->addCampo($obCmbTipoCargo, 'C');
$obTable->Body->addCampo($obCmbTipoVinculo, 'C');

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
