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
    * Página de Formulario que filtra de Relatórios de Regiões
    * Data de Criação: 13/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.07
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
require_once(CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once(CAM_GRH_PES_COMPONENTES . 'IFiltroContrato.class.php');
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

//Instanciando a Classe de Controle e de Visao de Homologar para Trazer o PPA vigente pelo Exercício
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

$rsRecordSet = $obVisao->pesquisaPPANorma($stFiltro);

$inCount = count($rsRecordSet->arElementos);
$inAnoExercicio = substr($_SESSION['exercicio'], 5,-2);

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioRegioes";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJs = "JS".$stPrograma.".php";

//include_once($pgJs);

$stAcao = $request->get('stAcao');

### Campos Hidden ###
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("encaminhaRelatorioRegioes");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("encaminhaRelatorioRegioes");

### Form ###
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

### ITextBoxSelectPPA ###
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);

### Listar Descrição completa das Regiões (sim) ###
$obRadioDescricaoRegiaoSim = new Radio();
$obRadioDescricaoRegiaoSim->setName("boDescricaoRegiao");
$obRadioDescricaoRegiaoSim->setRotulo("Listar Descrição completa das Regiões");
$obRadioDescricaoRegiaoSim->setLabel("Sim");
$obRadioDescricaoRegiaoSim->setValue("1");
$obRadioDescricaoRegiaoSim->setChecked(true);
$obRadioDescricaoRegiaoSim->setNull(false);

### Listar Descrição completa das Regiões (não) ###
$obRadioDescricaoRegiaoNao = new Radio();
$obRadioDescricaoRegiaoNao->setName("boDescricaoRegiao");
$obRadioDescricaoRegiaoNao->setLabel("Não");
$obRadioDescricaoRegiaoNao->setValue("0");
$obRadioDescricaoRegiaoNao->setNull(false);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo('Filtro Regiões');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->agrupaComponentes(array($obRadioDescricaoRegiaoSim, $obRadioDescricaoRegiaoNao));

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName('Limpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpaFormulario()');

$obFormulario->defineBarra(array($obBtnOk , $obBtnLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
