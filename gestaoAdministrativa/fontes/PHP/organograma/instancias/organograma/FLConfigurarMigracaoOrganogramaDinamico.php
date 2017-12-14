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
    * Página de Formulário para Migrar Organograma
    * Data de criação : 14/04/2009

    * @author Analista: Gelson Wolowski   <gelson.goncalves@cnm.org.br>
    * @author Programador: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'TConfigurarMigracaoOrganogramaDinamico.class.php';

$stPrograma = "ConfigurarMigracaoOrganogramaDinamico";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include $pgJs;

$stAcao = $request->get('stAcao');

# Definição dos Componentes
$obForm = new Form;
$obForm->setAction ($pgForm);

$obTConfigurarMigracaoOrganogramaDinamico = new TConfigurarMigracaoOrganogramaDinamico;
$obTOrganogramaOrganograma                = new TOrganogramaOrganograma;

$stFiltro = " WHERE ativo = true";
$obTOrganogramaOrganograma->recuperaOrganogramasAtivo($rsOrganogramaAtivo, '', '', $stFiltro);
$stOrganogramaAtivo = $rsOrganogramaAtivo->getCampo('cod_organograma')." - ".$rsOrganogramaAtivo->getCampo('implantacao');

# Label com o organograma ativo (apenas para informação)
$obLabelOrganogramaAtivo = new Label;
$obLabelOrganogramaAtivo->setRotulo('Organograma Ativo');
$obLabelOrganogramaAtivo->setValue($stOrganogramaAtivo);

# Recupera todos os organogramas com data de implantação superior ao
# organograma ativo e que possuam ao menos um órgão vinculado.
$stFiltro  = " WHERE  implantacao > '".$rsOrganogramaAtivo->getCampo('dt_implantacao')."'    \n";
$stFiltro .= "   AND  cod_organograma <> ".$rsOrganogramaAtivo->getCampo('cod_organograma')."\n";
$stFiltro .= "   AND  EXISTS                                                                 \n";
$stFiltro .= "        (                                                                      \n";
$stFiltro .= "          SELECT  1                                                            \n";
$stFiltro .= "            FROM  organograma.orgao_nivel                                      \n";
$stFiltro .= "           WHERE  orgao_nivel.cod_organograma = organograma.cod_organograma    \n";
$stFiltro .= "        )                                                                      \n";

$obTOrganogramaOrganograma->recuperaOrganogramas($rsOrganogramaNovo, '', $stFiltro);

$obCmbOrganograma = new Select;
$obCmbOrganograma->setId         ('inCodOrganograma');
$obCmbOrganograma->setName       ('inCodOrganograma');
$obCmbOrganograma->setRotulo     ('Novo Organograma');
$obCmbOrganograma->setNull       (false);
$obCmbOrganograma->setTitle      ('Selecione o organograma que será vinculado ao sistema');
$obCmbOrganograma->addOption     ('', 'Selecione', '');
$obCmbOrganograma->setCampoId    ('[cod_organograma]');
$obCmbOrganograma->setCampoDesc  ('[implantacao]');
$obCmbOrganograma->preencheCombo ($rsOrganogramaNovo);

$obLabelOrganogramaInativo = new Label;
$obLabelOrganogramaInativo->setName("lblOrganogramaInativo");
$obLabelOrganogramaInativo->setRotulo("Aviso");
$obLabelOrganogramaInativo->setValue("Necessário configurar um novo Organograma com data superior a ".$rsOrganogramaAtivo->getCampo('implantacao').", vinculada com ao menos um orgão.");

# Caso o RecordSet retorne apenas um elemento, deixa selecionado no select.
if (count($rsOrganogramaNovo->arElementos) == 1)
    $obCmbOrganograma->setValue($rsOrganogramaNovo->getCampo('cod_organograma'));

# Caso já tenha uma migração parcial em andamento, sugere ao usuário o organograma já escolhido
$obTConfigurarMigracaoOrganogramaDinamico->recuperaVerificadorMigracaoParcial($rsMigracaoParcial);

$obHdnCodOrganograma = new Hidden;

if ($rsMigracaoParcial->getCampo('cod_organograma') != '') {
    $obCmbOrganograma->setValue($rsMigracaoParcial->getCampo('cod_organograma'));
    $obCmbOrganograma->setDisabled (true);
    $obCmbOrganograma->setLabel    (true);

    $obHdnCodOrganograma->setId   ('inCodOrganograma');
    $obHdnCodOrganograma->setName ('inCodOrganograma');
    $obHdnCodOrganograma->setValue($rsMigracaoParcial->getCampo('cod_organograma'));
}

# Definição do Formulário
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

# Span que irá guardar a tabela de Migração.
$obSpan = new Span;
$obSpan->setId('spnTable');

$obBtnOk = new Button;
$obBtnOk->setId    ('btnOk');
$obBtnOk->setName  ('btnOk');
$obBtnOk->setValue ('Nova Migração');
$obBtnOk->obEvento->setOnClick("validaFiltroNovaMigracao();");

$obBtnContinuar = new Button;
$obBtnContinuar->setId    ('btnContinuar');
$obBtnContinuar->setName  ('btnContinuar');
$obBtnContinuar->setValue ('Continuar Migração');
$obBtnContinuar->obEvento->setOnClick("jq('#inCodOrganograma').attr('disabled', ''); validaFiltroNovaMigracao();");

# Adiciona os componentes ao Formulário.
$obFormulario->addComponente($obLabelOrganogramaAtivo);

if (count($rsOrganogramaNovo->arElementos) == 0) {
    $obBtnOk->setDisabled (true);
    $obFormulario->addComponente( $obLabelOrganogramaInativo );
} else {
    $obFormulario->addComponente($obCmbOrganograma);
}
$obFormulario->addHidden($obHdnCodOrganograma);

if ($rsMigracaoParcial->getCampo('cod_organograma') != '') {
    $obFormulario->addHidden($obHdnCodOrganograma);
}

# Agrupa os novos botões.
if ($rsMigracaoParcial->getCampo('finalizado') == '' && $rsMigracaoParcial->getCampo('cod_organograma') == '')
    $obFormulario->defineBarra(array($obBtnOk));
else
    $obFormulario->defineBarra(array($obBtnOk, $obBtnContinuar));

# Monta a visualização do formulário.
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
