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
    * Página de formulario de Manter PPA
    * Data de Criação: 21/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * Casos de uso: uc-02.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_VISAO.'/VPPAManterPPA.class.php';
require_once CAM_GF_PPA_VISAO.'/VPPAHomologarPPA.class.php';
require_once CAM_GF_PPA_NEGOCIO.'/RPPAManterPPA.class.php';
require_once CAM_GF_PPA_NEGOCIO.'/RPPAHomologarPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterPPA';
$pgOcul     = 'OC'.$stPrograma.".php";
$pgProc     = 'PR'.$stPrograma.".php";
$pgFilt     = 'FL'.$stPrograma.".php";
$pgList     = 'LS'.$stPrograma.".php";
$pgJs       = 'JS'.$stPrograma.".php";

include_once $pgJs;

$stAcao = $request->get('stAcao');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setValue('verificaHomologacao');

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obTxtAnoInicio = new Inteiro();
$obTxtAnoInicio->setRotulo          ('Ano Inicial PPA');
$obTxtAnoInicio->setNull            (false);
$obTxtAnoInicio->setName            ('stAnoInicio');
$obTxtAnoInicio->setId              ('stAnoInicio');
$obTxtAnoInicio->setTitle           ('Informe o ano de início deste PPA.');
$obTxtAnoInicio->setMaxLength       (4);
$obTxtAnoInicio->setValue           ($stAnoInicio);
$obTxtAnoInicio->obEvento->setOnBlur("montaParametrosGET('atualizaPPA');");

$obLblAnoFinal = new Label;
$obLblAnoFinal->setRotulo('Ano Final PPA');
$obLblAnoFinal->setName  ('lbAnoFinal');
$obLblAnoFinal->setNull  (false);
$obLblAnoFinal->setId    ('lbAnoFinal');

$obTxtAnoFinal = new Hidden;
$obTxtAnoFinal->setName('stAnoFinal');
$obTxtAnoFinal->setId  ('stAnoFinal');

# Define checkbox de arredondamento.
$obRadArredondarSim = new Radio();
$obRadArredondarSim->setId                ('boArredondamento');
$obRadArredondarSim->setName              ('boArredondamento');
$obRadArredondarSim->setRotulo            ('Arredondar Valores do Orçamento');
$obRadArredondarSim->setLabel             ('Sim');
$obRadArredondarSim->setValue             (true);
$obRadArredondarSim->setNull              (false);
$obRadArredondarSim->obEvento->setOnChange("montaParametrosGET('montaSpanPrecisao')");

# Define checkbox de arredondamento.
$obRadArredondarNao = new Radio();
$obRadArredondarNao->setId                ('boArredondamento');
$obRadArredondarNao->setName              ('boArredondamento');
$obRadArredondarNao->setRotulo            ('Arredondar Valores do Orçamento');
$obRadArredondarNao->setLabel             ('Não');
$obRadArredondarNao->setValue             (false);
$obRadArredondarNao->setNull              (false);
$obRadArredondarNao->setChecked           (true);
$obRadArredondarNao->obEvento->setOnChange("montaParametrosGET('apagaSpanPrecisao')");

$arRadArredondar = array($obRadArredondarSim, $obRadArredondarNao);

# Define Span para campo precisão.
$obSpnPrecisao = new Span();
$obSpnPrecisao->setID('spnPrecisao');

# Define Span de importação de PPA anterior.
$obSpnImportarPPA = new Span();
$obSpnImportarPPA->setID('spnImportarPPA');

# Define checkbox de destinação de recursos.
$obChkDestRecursos = new CheckBox();
$obChkDestRecursos->setName('boDestRecursos');
$obChkDestRecursos->setLabel('Utilizar destinação de recursos');
$obChkDestRecursos->setChecked(false);

$obFormulario = new Formulario;
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addHidden        ($obHdnCtrl);
$obFormulario->addHidden        ($obTxtAnoFinal);
$obFormulario->addTitulo        ('Dados para Inclusão do PPA');
$obFormulario->addComponente    ($obTxtAnoInicio);
$obFormulario->addComponente    ($obLblAnoFinal);
$obFormulario->agrupaComponentes($arRadArredondar);
$obFormulario->addSpan          ($obSpnPrecisao);
$obFormulario->addSpan          ($obSpnImportarPPA);
$obFormulario->addComponente    ($obChkDestRecursos);

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick  ('incluirPPA()');

$obBtnLimpar = new Button;
$obBtnLimpar->setName             ('Limpar');
$obBtnLimpar->setValue            ('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpaFormulario()');

$obFormulario->defineBarra(array($obBtnOk, $obBtnLimpar));

$obFormulario->show();

$obController = new RPPAHomologarPPA;
$obVisao      = new VPPAHomologarPPA($obController);
$rsPPA        = $obVisao->pesquisaDadosPPA();

$obLista = new Lista;
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Listar PPA');

$obLista->setRecordSet($rsPPA);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(3);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Período');
$obLista->ultimoCabecalho->setWidth(30);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Destinação de Recursos');
$obLista->ultimoCabecalho->setWidth(20);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Arredondamento');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Nível');
$obLista->ultimoCabecalho->setWidth(30);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('periodo');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('destinacao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('precisao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('nivel');
$obLista->commitDado();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
