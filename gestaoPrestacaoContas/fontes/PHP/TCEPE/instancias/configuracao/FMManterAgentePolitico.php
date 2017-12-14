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

/*
 * Formulário de Vinculo de Agente Político
 * Data de Criação: 01/10/2014

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
require_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEAgentePolitico.class.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPECGMAgentePolitico.class.php';

$stPrograma = 'ManterAgentePolitico';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

# Recupera da base de dados os valores e grava na sessão.
$obTTCEPECGMAgentePolitico = new TTCEPECGMAgentePolitico; 
$obTTCEPECGMAgentePolitico->recuperaVinculoAgentePolitico($rsVinculoAgentePolitico);

$inId = 0;

foreach ($rsVinculoAgentePolitico->getElementos() as $dados) {
    $arAgentePolitico[$inId]['id']           = $inId;
    $arAgentePolitico[$inId]['cod_entidade'] = $dados['cod_entidade'];
    $arAgentePolitico[$inId]['exercicio']    = Sessao::getExercicio();
    $arAgentePolitico[$inId]['num_cgm']      = $dados['numcgm'];
    $arAgentePolitico[$inId]['nom_cgm']      = $dados['nom_cgm'];
    $arAgentePolitico[$inId]['nom_entidade'] = $dados['nom_entidade'];
    $arAgentePolitico[$inId]['cod_agente_politico'] = $dados['cod_agente_politico'];
    $arAgentePolitico[$inId]['nom_agente_politico'] = $dados['nom_agente_politico'];
    $inId++;
}

Sessao::write('arAgentes', $arAgentePolitico);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnNomEntidade = new Hidden;
$obHdnNomEntidade->setId('stNomEntidade');
$obHdnNomEntidade->setName('stNomEntidade');

$obHdnNomAgentePolitico = new Hidden;
$obHdnNomAgentePolitico->setId('stNomAgentePolitico');
$obHdnNomAgentePolitico->setName('stNomAgentePolitico');

# Recupera exercício (sessão)
$obLblExercicio = new Label;
$obLblExercicio->setId('stExercicio');
$obLblExercicio->setName('stExercicio');
$obLblExercicio->setValue(Sessao::getExercicio());
$obLblExercicio->setRotulo('Exercício');

# Recupera Entidades
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio   (Sessao::getExercicio());
$obROrcamentoEntidade->listarEntidades($rsEntidades);

$obCmbEntidades = new Select;
$obCmbEntidades->setRotulo    ('Entidade');
$obCmbEntidades->setId        ('inCodEntidade');
$obCmbEntidades->setName      ('inCodEntidade');
$obCmbEntidades->setCampoId   ('cod_entidade');
$obCmbEntidades->setCampoDesc ('[nom_cgm]');
$obCmbEntidades->addOption    ('', 'Selecione');
$obCmbEntidades->preencheCombo($rsEntidades);
$obCmbEntidades->obEvento->setOnBlur("jQuery('#stNomEntidade').val(jQuery('#inCodEntidade :selected').text());");

# Componente para buscar CGM (pessoa física)
$obBscCGMSolicitante = new IPopUpCGM ($obForm);
$obBscCGMSolicitante->setRotulo('CGM');
$obBscCGMSolicitante->setTipo('fisica');
$obBscCGMSolicitante->setId('stNomCGM');
$obBscCGMSolicitante->setNull( true  );
$obBscCGMSolicitante->setTitle( 'Informe o CGM.');
$obBscCGMSolicitante->setValue( $stNomCGM  );
$obBscCGMSolicitante->obCampoCod->setValue( $inCGM     );
$obBscCGMSolicitante->obCampoCod->setSize(10);
$obBscCGMSolicitante->obCampoCod->setName( 'inCodCGM' );
$obBscCGMSolicitante->obImagem->setId('imgBuscaCGM');

# Recupera Agente Político 
$obTTCEPEAgentePolitico = new TTCEPEAgentePolitico;
$obTTCEPEAgentePolitico->recuperaTodos($rsAgentePolitico, '', ' ORDER BY cod_agente_politico');

# Select de Agente Político
$obCmbAgentePolitico = new Select;
$obCmbAgentePolitico->setId        ('inCodAgentePolitico');
$obCmbAgentePolitico->setName      ('inCodAgentePolitico');
$obCmbAgentePolitico->setRotulo    ('Agente Político');
$obCmbAgentePolitico->setCampoId   ('[cod_agente_politico]');
$obCmbAgentePolitico->setCampoDesc ('[descricao]');
$obCmbAgentePolitico->addOption    ('','Selecione');
$obCmbAgentePolitico->setValue     ('[cod_agente_politico]');
$obCmbAgentePolitico->setStyle     ('width: 250px');
$obCmbAgentePolitico->preencheCombo($rsAgentePolitico);
$obCmbAgentePolitico->obEvento->setOnBlur("jQuery('#stNomAgentePolitico').val(jQuery('#inCodAgentePolitico :selected').text());");

# Botão para Incluir / Limpar
$obBtnIncluir = new Button;
$obBtnIncluir->setId('btnIncluir');
$obBtnIncluir->setValue('Incluir CGM');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirLista');");

$obBtnLimpar = new Button;
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparLista');");

$obSpnLista = new Span;
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnNomEntidade);
$obFormulario->addHidden($obHdnNomAgentePolitico);
$obFormulario->addComponente($obLblExercicio);
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obBscCGMSolicitante );
$obFormulario->addComponente($obCmbAgentePolitico);
$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan($obSpnLista);

$obFormulario->Cancelar ($pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

$jsOnLoad = 'montaParametrosGET("montarLista");';

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';