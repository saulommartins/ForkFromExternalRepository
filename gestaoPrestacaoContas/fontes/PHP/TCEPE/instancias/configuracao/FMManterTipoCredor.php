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
 * Formulário de Relacao de Tipo de Credor
 * Data de Criação: 08/10/2014

 * @author Desenvolvedor Lisiane Morais

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
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoCredor.class.php';
require_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPECgmTipoCredor.class.php';

$stPrograma = 'ManterTipoCredor';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');
include_once $pgJs;
include_once ($pgOcul);

if (empty($stAcao)) {
    $stAcao = "manter";
}

# Recupera da base de dados os valores e grava na sessão.
$obTTCEPECGMTipoCredor = new TTCEPECGMTipoCredor; 
$obTTCEPECGMTipoCredor->recuperaTodos($rsCGMTipoCredor);

$inId = 0;

foreach ($rsCGMTipoCredor->getElementos() as $dados) {
    $arCGMTipoCredor[$inId]['inId']         = $inId;
    $arCGMTipoCredor[$inId]['exercicio']    = $dados['exercicio'];
    $arCGMTipoCredor[$inId]['cgm_credor']   = $dados['cgm_credor'];
    $arCGMTipoCredor[$inId]['nom_cgm']      = SistemaLegado::pegaDado('nom_cgm','sw_cgm','WHERE numcgm ='.$dados['cgm_credor']);
    $arCGMTipoCredor[$inId]['cod_tipo_credor'] = $dados['cod_tipo_credor'];
    $arCGMTipoCredor[$inId]['nom_tipo_credor'] = SistemaLegado::pegaDado('descricao','tcepe.tipo_credor','WHERE cod_tipo_credor ='.$dados['cod_tipo_credor']);
    $inId++;
}

Sessao::write('arCGMTipoCredor', $arCGMTipoCredor);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnNomCredor = new Hidden;
$obHdnNomCredor->setId('stNomTipoCredor');
$obHdnNomCredor->setName('stNomTipoCredor');

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setId('stNomCGMCredor');
$obHdnNomCGM->setName('stNomCGMCredor');

$obHdnInId = new Hidden;
$obHdnInId->setName("hdnInId");
$obHdnInId->setId  ("hdnInId");

# Recupera exercício (sessão)
$obLblExercicio = new Label;
$obLblExercicio->setId('stExercicio');
$obLblExercicio->setName('stExercicio');
$obLblExercicio->setValue(Sessao::getExercicio());
$obLblExercicio->setRotulo('Exercício');

# Componente para buscar CGM 
$obBscCGMCredor = new IPopUpCGM ($obForm);
$obBscCGMCredor->setRotulo('CGM');
$obBscCGMCredor->setId('stNomCGM');
$obBscCGMCredor->setNull( true  );
$obBscCGMCredor->setTitle( 'Informe o CGM.');
$obBscCGMCredor->setValue( $stNomCGM  );
$obBscCGMCredor->obCampoCod->setValue( $inCGM     );
$obBscCGMCredor->obCampoCod->setSize(10);
$obBscCGMCredor->obCampoCod->setName( 'inCodCGM' );

# Recupera Agente Político 
$obTTCEPETipoCredor = new TTCEPETipoCredor;
$obTTCEPETipoCredor->recuperaTodos($rsTipoCredor, '', ' ORDER BY cod_tipo_credor');

# Select do Tipo Credor
$obCmbTipoCredor = new Select;
$obCmbTipoCredor->setId        ('inCodTipoCredor');
$obCmbTipoCredor->setName      ('inCodTipoCredor');
$obCmbTipoCredor->setRotulo    ('Tipo de Credor');
$obCmbTipoCredor->setCampoId   ('[cod_tipo_credor]');
$obCmbTipoCredor->setCampoDesc ('[descricao]');
$obCmbTipoCredor->addOption    ('','Selecione');
$obCmbTipoCredor->setValue     ('[cod_tipo_credor]');
$obCmbTipoCredor->setStyle     ('width: 250px');
$obCmbTipoCredor->preencheCombo($rsTipoCredor);
$obCmbTipoCredor->obEvento->setOnBlur("jQuery('#stNomTipoCredor').val(jQuery('#inCodTipoCredor :selected').text());");

# Botão para Incluir 
$obBtnIncluir = new Button;
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->setId('btIncluirCGM');
$obBtnIncluir->obEvento->setOnClick("buscaValor('incluirLista');"     );

$obSpnLista = new Span();
$obSpnLista->setId('spnListaCredor');
//$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnNomCredor);
$obFormulario->addHidden($obHdnInId);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnNomCGM);
$obFormulario->addComponente($obLblExercicio);
$obFormulario->addComponente($obBscCGMCredor );
$obFormulario->addComponente($obCmbTipoCredor);
$obFormulario->addComponente($obBtnIncluir);
$obFormulario->addSpan($obSpnLista);

$obFormulario->Cancelar ($pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao, true);

$obFormulario->show();

processarForm(true,"Form",$stAcao);

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';