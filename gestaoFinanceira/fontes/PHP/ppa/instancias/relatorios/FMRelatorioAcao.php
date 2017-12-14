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
    * Página de Formulario que filtra de Relatórios de Ações
    * Data de Criação: 19/11/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.09
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(CAM_GF_PPA_COMPONENTES  . "ITextBoxSelectOrgao.class.php");
require_once(CAM_GRH_PES_COMPONENTES . 'IFiltroContrato.class.php');
require_once(CAM_GF_PPA_COMPONENTES  . "ITextBoxSelectPPA.class.php");
require_once(CAM_GF_PPA_COMPONENTES  . "IPopUpRegiao.class.php");
require_once(CAM_GF_ORC_COMPONENTES  . "IPopUpRecurso.class.php");
require_once(CAM_GA_ADM_COMPONENTES  . "IMontaAssinaturas.class.php");
require_once(CAM_GF_PPA_COMPONENTES  . "MontaOrgaoUnidade.class.php");
require_once(CAM_GF_PPA_NEGOCIO      . "/RPPAHomologarPPA.class.php");
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

//Instanciando a Classe de Controle e de Visao de Homologar para Trazer o PPA vigente pelo Exercício
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

$rsRecordSet = $obVisao->pesquisaPPANorma($stFiltro);

$inCount = count($rsRecordSet->arElementos);
$inAnoExercicio = Sessao::getExercicio();

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

### Entidades Cadastradas no Sistema ###
$stEntidades = $obVisao->montarEntidades();

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioAcao";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJs = "JS".$stPrograma.".php";

include_once($pgJs);

$stAcao = $request->get('stAcao');

### Campos Hidden ###
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("encaminhaRelatorioAcao");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("encaminhaRelatorioAcao");

$obHdnPPAVigente = new Hidden;
$obHdnPPAVigente->setName("inCodPPAVigente");
$obHdnPPAVigente->setValue($inCodPPA);

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName("inCodEntidade");
$obHdnEntidade->setId("inCodEntidade");
$obHdnEntidade->setValue($stEntidades);

$obHdnAssinatura = new Hidden;
$obHdnAssinatura->setName("boAssinaturas");
$obHdnAssinatura->setId("boAssinaturas");
$obHdnAssinatura->setValue('n');

### Form ###
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

### ITextBoxSelectPPA ###
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);

### Informar Código Programa Inicial ###
$obTextBoxProgramaIni = new Inteiro;
$obTextBoxProgramaIni->setRotulo('Programas');
$obTextBoxProgramaIni->setName('inNumProgramaIni');
$obTextBoxProgramaIni->setNull(true);
$obTextBoxProgramaIni->setMaxLength(4);
$obTextBoxProgramaIni->setTitle('Escolha um codigo para programa');
$obTextBoxProgramaIni->setSize(8);

### Label Programa ###
$obLabelPrograma = new Label();
$obLabelPrograma->setValue('a');

### Informar Código Programa Final ###
$obTextBoxProgramaFim = new Inteiro;
$obTextBoxProgramaFim->setRotulo('Programas');
$obTextBoxProgramaFim->setName('inNumProgramaFim');
$obTextBoxProgramaFim->setNull(true);
$obTextBoxProgramaFim->setMaxLength(4);
$obTextBoxProgramaFim->setTitle('Escolha um codigo para programa');
$obTextBoxProgramaFim->setSize(8);

### Informar Código Ação Inicial ###
$obTextBoxAcaoIni = new Inteiro;
$obTextBoxAcaoIni->setRotulo('Ações');
$obTextBoxAcaoIni->setName('inCodAcaoIni');
$obTextBoxAcaoIni->setNull(true);
$obTextBoxAcaoIni->setMaxLength(4);
$obTextBoxAcaoIni->setSize(8);

### Label Acao ###
$obLabelAcao = new Label();
$obLabelAcao->setValue('a');

### Informar Código Ação Final ###
$obTextBoxAcaoFim = new Inteiro;
$obTextBoxAcaoFim->setRotulo('Programas');
$obTextBoxAcaoFim->setName('inCodAcaoFim');
$obTextBoxAcaoFim->setNull(true);
$obTextBoxAcaoFim->setMaxLength(4);
$obTextBoxAcaoFim->setSize(8);

### Unidade Orçamentária ###
$obIMontaUnidadeOrcamentaria = new MontaOrgaoUnidade();
$obIMontaUnidadeOrcamentaria->setRotulo('Unidade Responsável');
$obIMontaUnidadeOrcamentaria->setActionPosterior($pgProc);
$obIMontaUnidadeOrcamentaria->setNull(true);

### Servidor Resposável ###
$obCGMServidor = new IFiltroContrato();
$obCGMServidor->setInformacoesFuncao(false);
$obCGMServidor->obLblCGM->setRotulo('Servidor Responsável');
$obCGMServidor->setTituloFormulario('');

### Região de Abrangência ###
$obIPopUpRegiao = new IPopUpRegiao($obForm);
$obIPopUpRegiao->setRotulo('Região de Abrangência');
$obIPopUpRegiao->setNull(true);

### Recurso ###
$obIPopUpRecurso = new IPopUpRecurso();
$obIPopUpRecurso->setRotulo('Recurso');

### Ordenar ###
$obSelectOrdem = new Select();
$obSelectOrdem->setName('inOrdem');
$obSelectOrdem->setRotulo('Ordenar por');
$obSelectOrdem->setId('inOrdem');
$obSelectOrdem->addOption('1', 'Ação');
$obSelectOrdem->addOption('2', 'Função-Subfunção');
$obSelectOrdem->addOption('3', 'Região');
//$obSelectOrdem->addOption('4', 'Recurso');

### Instanciação do objeto Lista de Assinaturas ###
$obMontaAssinaturas = new IMontaAssinaturas();
$obMontaAssinaturas->obRadioAssinaturasSim->obEvento->setOnClick("validaSessao('s');");
$obMontaAssinaturas->obRadioAssinaturasNao->obEvento->setOnClick("validaSessao('n');");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnPPAVigente);
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addHidden($obHdnAssinatura);
$obFormulario->addTitulo('Filtro Ações');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->agrupaComponentes(array($obTextBoxProgramaIni, $obLabelPrograma, $obTextBoxProgramaFim));
$obFormulario->agrupaComponentes(array($obTextBoxAcaoIni, $obLabelAcao, $obTextBoxAcaoFim));
$obIMontaUnidadeOrcamentaria->geraFormulario($obFormulario);
$obCGMServidor->geraFormulario($obFormulario);
$obFormulario->addComponente($obIPopUpRegiao);
$obFormulario->addComponente($obIPopUpRecurso);
$obFormulario->addComponente($obSelectOrdem);
$obMontaAssinaturas->geraFormulario($obFormulario); // Injeção de código no formulário

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName('Limpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpaFormulario()');

$obFormulario->defineBarra(array($obBtnOk , $obBtnLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
