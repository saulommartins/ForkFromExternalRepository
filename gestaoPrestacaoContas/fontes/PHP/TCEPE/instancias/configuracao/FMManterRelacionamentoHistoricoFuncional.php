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
     * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: FMManterRelacionamentoHistoricoFuncional.php 60305 2014-10-13 13:19:28Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GA_NORMAS_COMPONENTES.'IBuscaInnerNorma.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoMovimentacao.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalAssentamentoAssentamento.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';

$stPrograma = "ManterRelacionamentoHistoricoFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include $pgJs;

$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName  ( "inCodEntidade" );
$obHdnEntidade->setValue ( $request->get('inCodEntidade') );

// Busca a entidade definida como prefeitura na configuração do orçamento
$stCampo   = "valor";
$stTabela  = "administracao.configuracao";
$stFiltro  = " WHERE exercicio = '".Sessao::getExercicio()."'";
$stFiltro .= "   AND parametro = 'cod_entidade_prefeitura' ";

$inCodEntidadePrefeitura = SistemaLegado::pegaDado($stCampo, $stTabela, $stFiltro);

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($request->get('inCodEntidade') == $inCodEntidadePrefeitura) {
    $stFiltro = " WHERE nspname = 'pessoal'";
    $stSchema = '';
} else {
    $stFiltro = " WHERE nspname = 'pessoal_".$request->get('inCodEntidade')."'";
    $stSchema = '_'.$request->get('inCodEntidade');
}

$obTEntidade = new TEntidade();
$obTEntidade->recuperaEsquemasCriados($rsEsquemas, $stFiltro);

// Verifica se existe o schema para a entidade selecionada
if ($rsEsquemas->getNumLinhas() < 1) {
    SistemaLegado::alertaAviso($pgFilt.'?stAcao='.$_REQUEST['stAcao'], 'Não existe entidade criada no RH para a entidade selecionada!' , '', 'aviso', Sessao::getId(), '../');
}

// Se foi selecionada a entidade definida como prefeitura, não vai "_" no schema
if ($request->get('inCodEntidade') == $inCodEntidadePrefeitura) {
    Sessao::setEntidade('');
} else {
    // Se não foi selecionada a entidade definida como prefeitura
    // ao executar as consultas, automaticamente é adicionado o "_" + cod_entidade selecionada
    Sessao::setEntidade($request->get('inCodEntidade'));
}

$obTTCEPETipoMovimentacao = new TTCEPETipoMovimentacao;
$obTTCEPETipoMovimentacao->recuperaTodos($rsTipoMovimentacao);

$obCmbTipoMovimentacao = new Select();
$obCmbTipoMovimentacao->setRotulo ("*Tipo de Movimentação");
$obCmbTipoMovimentacao->setTitle ("Selecione o tipo de movimentação");
$obCmbTipoMovimentacao->setName ('inCodMovimentacao');
$obCmbTipoMovimentacao->setId ('inCodMovimentacao');
$obCmbTipoMovimentacao->setCampoId ("cod_tipo_movimentacao");
$obCmbTipoMovimentacao->setCampoDesc ("descricao");
$obCmbTipoMovimentacao->addOption ("","Selecione");
$obCmbTipoMovimentacao->preencheCombo($rsTipoMovimentacao);

$obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento;
$obTPessoalAssentamentoAssentamento->setDado('exercicio', Sessao::getExercicio());
$obTPessoalAssentamentoAssentamento->setDado('cod_entidade', $request->get('inCodEntidade'));
$obTPessoalAssentamentoAssentamento->recuperaAssentamentosPE ($rsAssentamentos,""," ORDER BY assentamento_assentamento.descricao ");

$obCmbAssentamento = new Select();
$obCmbAssentamento->setName ("inCodAssentamento");
$obCmbAssentamento->setId ('inCodAssentamento');
$obCmbAssentamento->setRotulo ("*Assentamento");
$obCmbAssentamento->setTitle ("Selecione o assentamento");
$obCmbAssentamento->addOption ("", "Selecione");
$obCmbAssentamento->setCampoId ("[cod_assentamento]_[timestamp]");
$obCmbAssentamento->setCampoDesc ("descricao");
$obCmbAssentamento->preencheCombo($rsAssentamentos);

$obSpnAssentamento = new Span;
$obSpnAssentamento->setId ("spnAssentamento");

$obSpnLista = new Span;
$obSpnLista->setId ("spnLista");

$obBtOk = new Button();
$obBtOk->setValue('Incluir');
$obBtOk->setId('btIncluir');
$obBtOk->obEvento->setOnCLick("montaParametrosGET('incluiLista', 'inCodEntidade,inCodMovimentacao,inCodAssentamento');");

$obLimpar = new Limpar();

$obOk = new Ok();

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo('Configurações do Orçamento');
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addComponente($obCmbTipoMovimentacao);
$obFormulario->addComponente($obCmbAssentamento);
$obFormulario->defineBarra(array($obBtOk, $obLimpar));
$obFormulario->addSpan($obSpnLista);
$obFormulario->defineBarra(array($obOk));
$obFormulario->show();

$jsOnLoad = "montaParametrosGET('montaLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';