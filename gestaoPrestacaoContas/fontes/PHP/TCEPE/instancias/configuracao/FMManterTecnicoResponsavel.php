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
 * Formulário de Configuracao de Tecnico Responsavel
 * Data de Criação: 16/10/2014

 * @author Desenvolvedor Evandro Melos

 * @package URBEM
 * @subpackage

 * @ignore

 * $Id: FMManterTecnicoResponsavel.php 60584 2014-10-31 14:53:54Z michel $
 
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_FW_COMPONENTES.'/Table/Table.class.php';

//Mapeamentos
require_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';
require_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEResponsavelTecnico.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoResponsavel.class.php';

$stPrograma = 'ManterTecnicoResponsavel';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

//Recupera da base de dados os valores e grava na sessão.
$obTTCEPEResponsavelTecnico = new TTCEPEResponsavelTecnico(); 
$obTTCEPEResponsavelTecnico->recuperaResponsavelTecnico($rsResponsavelTecnico, "" , "" , $boTransacao );

$inId = 0;

foreach ($rsResponsavelTecnico->getElementos() as $dados) {
    $arTecnicoResponsavel[$inId]['id']              = $inId;
    $arTecnicoResponsavel[$inId]['cod_entidade']    = $dados['cod_entidade'];
    $arTecnicoResponsavel[$inId]['exercicio']       = Sessao::getExercicio();
    $arTecnicoResponsavel[$inId]['cgm_responsavel'] = $dados['cgm_responsavel'];
    $arTecnicoResponsavel[$inId]['nom_cgm']         = $dados['nom_cgm'];
    $arTecnicoResponsavel[$inId]['nom_entidade']    = $dados['nom_entidade'];
    $arTecnicoResponsavel[$inId]['crc']             = $dados['crc'];
    $arTecnicoResponsavel[$inId]['cod_tipo']        = $dados['cod_tipo'];
    $arTecnicoResponsavel[$inId]['descricao']       = $dados['descricao'];
    $arTecnicoResponsavel[$inId]['dt_inicio']       = $dados['dt_inicio'];
    $arTecnicoResponsavel[$inId]['dt_fim']          = $dados['dt_fim'];

    $inId++;
}

Sessao::write('arTecnicoResponsavel', $arTecnicoResponsavel);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnNomEntidade = new Hidden;
$obHdnNomEntidade->setId('stNomEntidade');
$obHdnNomEntidade->setName('stNomEntidade');

$obHdnNomTecResponsavel = new Hidden;
$obHdnNomTecResponsavel->setId('stNomTecResponsavel');
$obHdnNomTecResponsavel->setName('stNomTecResponsavel');

$obHdnInId = new Hidden;
$obHdnInId->setId('inId');
$obHdnInId->setName('inId');

//Recupera exercício (sessão)
$obLblExercicio = new Label;
$obLblExercicio->setId('stExercicio');
$obLblExercicio->setName('stExercicio');
$obLblExercicio->setValue(Sessao::getExercicio());
$obLblExercicio->setRotulo('Exercício');

//Recupera Entidades
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio   (Sessao::getExercicio());
$obROrcamentoEntidade->listarEntidades($rsEntidades);

$obCmbEntidades = new Select;
$obCmbEntidades->setRotulo    ('*Entidade');
$obCmbEntidades->setId        ('inCodEntidade');
$obCmbEntidades->setName      ('inCodEntidade');
$obCmbEntidades->setCampoId   ('cod_entidade');
$obCmbEntidades->setCampoDesc ('[nom_cgm]');
$obCmbEntidades->addOption    ('', 'Selecione');
$obCmbEntidades->setNull      ( true );
$obCmbEntidades->preencheCombo($rsEntidades);
$obCmbEntidades->obEvento->setOnBlur("jQuery('#stNomEntidade').val(jQuery('#inCodEntidade :selected').text());");

//Componente para buscar CGM (pessoa física)
$obBscCGMSolicitante = new IPopUpCGM ($obForm);
$obBscCGMSolicitante->setRotulo('*CGM');
$obBscCGMSolicitante->setTipo('fisica');
$obBscCGMSolicitante->setId('stNomCGM');
$obBscCGMSolicitante->setNull( true  );
$obBscCGMSolicitante->setTitle( 'Informe o CGM.');
$obBscCGMSolicitante->setValue( $stNomCGM  );
$obBscCGMSolicitante->obCampoCod->setValue( $inCGM     );
$obBscCGMSolicitante->obCampoCod->setSize(10);
$obBscCGMSolicitante->obCampoCod->setName( 'inCodCGM' );
$obBscCGMSolicitante->obImagem->setId('imgBuscaCGM');

$bTTCEPETipoResponsavel = new TTCEPETipoResponsavel();
$bTTCEPETipoResponsavel->recuperaTodos($rsTipoResponsavel);
//Tipo de Responsável
$obCmbTipoResponsavel = new Select;
$obCmbTipoResponsavel->setRotulo    ( '*Tipo Responsável' );
$obCmbTipoResponsavel->setId        ( 'inCodTipo' );
$obCmbTipoResponsavel->setName      ( 'inCodTipo' );
$obCmbTipoResponsavel->setCampoId   ('[cod_tipo] - [descricao]');
$obCmbTipoResponsavel->setCampoDesc ('[cod_tipo] - [descricao]');
$obCmbTipoResponsavel->addOption    ( '', 'Selecione'    );
$obCmbTipoResponsavel->setNull      ( true );
$obCmbTipoResponsavel->preencheCombo ( $rsTipoResponsavel   ); 

//CRC
$obTxtCRC = new TextBox();
$obTxtCRC->setRotulo    ( 'CRC' );
$obTxtCRC->setName      ( "stCRC" );
$obTxtCRC->setId        ( "stCRC" );
$obTxtCRC->setMaxLength ( 10       );
$obTxtCRC->setSize      ( 10       );
$obTxtCRC->setDefinicao ( "text"   );

$obDtVigencia = new Periodo();
$obDtVigencia->setRotulo          ( '*Vigência'      );
$obDtVigencia->setExercicio       (  Sessao::getExercicio() );
$obDtVigencia->setNull            ( true            );
$obDtVigencia->setValue           ( 4               );
$obDtVigencia->obDataInicial->setValue( date("d/m/Y") );

//Botão para Incluir / Limpar
$obBtnIncluir = new Button;
$obBtnIncluir->setId('btnIncluir');
$obBtnIncluir->setValue('Incluir');
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
$obFormulario->addHidden($obHdnNomTecResponsavel);
$obFormulario->addHidden($obHdnInId);
$obFormulario->addComponente($obLblExercicio);
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obBscCGMSolicitante );
$obFormulario->addComponente($obCmbTipoResponsavel);
$obFormulario->addComponente($obTxtCRC);
$obFormulario->addComponente($obDtVigencia);

$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan($obSpnLista);

$obFormulario->Cancelar ($pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

$jsOnLoad = 'montaParametrosGET("montarLista");';

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
