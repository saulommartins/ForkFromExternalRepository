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
    * Página de Formulario de relatório de Resumo de Despesa
    * Data de Criação   : 05/12/2005

    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31914 $
    $Name$
    $Autor: $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.04.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaRelatorioResumoDespesa.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoConfiguracao.class.php';
include_once CAM_GF_CONT_COMPONENTES.'IIntervaloPopUpContaBanco.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ResumoDespesa';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

$obRTesourariaRelatorioResumoDespesa = new RTesourariaRelatorioResumoDespesa;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$rsUsuariosDisponiveis = $rsUsuariosSelecionados = new recordSet;

if (empty($stAcao)) {
    $stAcao = 'incluir';
}

$obRTesourariaRelatorioResumoDespesa->obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade($rsUsuariosDisponiveis, ' ORDER BY cod_entidade');

$arFiltro = array();

while (!$rsUsuariosDisponiveis->eof()) {
    $arEntirdades[$rsUsuariosDisponiveis->getCampo('cod_entidade')] = $rsUsuariosDisponiveis->getCampo('nom_cgm');
    $rsUsuariosDisponiveis->proximo();
}
Sessao::write('arEntidades', $arEntirdades);

$rsUsuariosDisponiveis->setPrimeiroElemento();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction(CAM_FW_POPUPS.'relatorio/OCRelatorio.php');
$obForm->setTarget('oculto');

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName ('stCaminho');
$obHdnCaminho->setValue(CAM_GF_TES_INSTANCIAS.'relatorio/OCResumoDespesa.php');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue('');

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setRotulo('Entidade');
$obCmbEntidades->setTitle ('Entidade');
$obCmbEntidades->setNull  (false);

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsUsuariosDisponiveis->getNumLinhas() == 1) {
    $rsUsuariosSelecionados = $rsUsuariosDisponiveis;
    $rsUsuariosDisponiveis  = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1('inCodigoEntidadesDisponiveis');
$obCmbEntidades->setCampoId1  ('cod_entidade');
$obCmbEntidades->setCampoDesc1('nom_cgm');
$obCmbEntidades->SetRecord1   ($rsUsuariosDisponiveis);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2('inCodigoEntidadesSelecionadas');
$obCmbEntidades->setCampoId2  ('cod_entidade');
$obCmbEntidades->setCampoDesc2('nom_cgm');
$obCmbEntidades->SetRecord2   ($rsUsuariosSelecionados);

//Define Objeto Text para o Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ('stExercicio');
$obTxtExercicio->setValue    (Sessao::getExercicio());
$obTxtExercicio->setRotulo   ('Exercício');
$obTxtExercicio->setTitle    ('Informe o Exercício para o Resumo da Despesa');
$obTxtExercicio->setNull     (false);
$obTxtExercicio->setMaxLength(4);
$obTxtExercicio->setSize     (5);

$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio       (Sessao::getExercicio());
$obPeriodo->setValidaExercicio (true);
$obPeriodo->setNull            (false);
$obPeriodo->setValue           (4);

//Define Objeto Select para o Tipo de Transferencia
$obCmbTipoRelatorio = new Select;
$obCmbTipoRelatorio->setRotulo('Tipo de Relatório');
$obCmbTipoRelatorio->setName  ('stTipoRelatorio');
$obCmbTipoRelatorio->addOption('', 'Selecione');
$obCmbTipoRelatorio->addOption('B', 'por Banco');
$obCmbTipoRelatorio->addOption('R', 'por Recurso');
$obCmbTipoRelatorio->addOption('E', 'por Entidade');
$obCmbTipoRelatorio->setValue ('');
$obCmbTipoRelatorio->setStyle ('width: 120px');
$obCmbTipoRelatorio->setNull  (true);
$obCmbTipoRelatorio->setTitle ('Selecione o Tipo de Relatório');

//Define Objeto Text para a Despesa Inicial
$inDespesaInicial = '';
$obTxtDespesaInicial = new TextBox;
$obTxtDespesaInicial->setName     ('inDespesaInicial');
$obTxtDespesaInicial->setValue    ($inDespesaInicial);
$obTxtDespesaInicial->setRotulo   ('Despesa');
$obTxtDespesaInicial->setTitle    ('Informe a Despesa Inicial e Final');
$obTxtDespesaInicial->setNull     (true);
$obTxtDespesaInicial->setMaxLength(10);
$obTxtDespesaInicial->setSize     (11);
$obTxtDespesaInicial->setInteiro  (true);

//Define Objeto Label Até
$obTxtDespesaAte = new Label;
$obTxtDespesaAte->setName  ('stAte');
$obTxtDespesaAte->setValue ('até');
$obTxtDespesaAte->setRotulo('');

$inDespesaFinal = '';
//Define Objeto Text para a Despesa Final
$obTxtDespesaFinal = new TextBox;
$obTxtDespesaFinal->setName     ('inDespesaFinal');
$obTxtDespesaFinal->setValue    ($inDespesaFinal);
$obTxtDespesaFinal->setRotulo   ('');
$obTxtDespesaFinal->setTitle    ('Informe a Despesa Final');
$obTxtDespesaFinal->setNull     (true);
$obTxtDespesaFinal->setMaxLength(10);
$obTxtDespesaFinal->setSize     (11);
$obTxtDespesaFinal->setInteiro  (true);

//Define o componente IIntervaloPopUpContaConta
$obIIntervaloPopUpContaBanco = new IIntervaloPopUpContaBanco($obCmbEntidades);
$obIIntervaloPopUpContaBanco->obIPopUpContaBancoInicial->obCampoCod->setName('inContaBancoInicial');
$obIIntervaloPopUpContaBanco->obIPopUpContaBancoFinal->obCampoCod->setName  ('inContaBancoFinal');

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setOpcaoAssinaturas   (false);
$obMontaAssinaturas->setCampoEntidades     ('inCodigoEntidadesSelecionadas');
$obMontaAssinaturas->setFuncaoJS           ();
$obMontaAssinaturas->setEventosCmbEntidades($obCmbEntidades);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addHidden        ($obHdnCaminho);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addHidden        ($obHdnAcao);

$obFormulario->addTitulo        ('Dados para Filtro');
$obFormulario->addComponente    ($obCmbEntidades);
$obFormulario->addComponente    ($obTxtExercicio);
$obFormulario->addComponente    ($obPeriodo);
$obFormulario->addComponente    ($obCmbTipoRelatorio);
$obFormulario->agrupaComponentes(array($obTxtDespesaInicial, $obTxtDespesaAte, $obTxtDespesaFinal));
$obFormulario->addComponente    ($obIIntervaloPopUpContaBanco);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);

$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
