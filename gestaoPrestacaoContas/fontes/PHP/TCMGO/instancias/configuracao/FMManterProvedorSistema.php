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
 * Formulário de Configuracao de Provedor do Sistema
 * Data de Criação: 27/01/2015

 * @author Desenvolvedor Evandro Melos

 * @package URBEM
 * @subpackage

 * @ignore

 * $Id: $
 
 */ 

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = 'ManterProvedorSistema';
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

//Recupera CGM já cadastrado
$inCGM = SistemaLegado::pegaConfiguracao("provedor_sistema", 42, Sessao::getExercicio(), $boTransacao );
if ($inCGM)
    $stNomCGM = SistemaLegado::pegaDado("nom_cgm", "sw_cgm"," where numcgm = ". $inCGM );

//Recupera exercício (sessão)
$obLblExercicio = new Label;
$obLblExercicio->setId    ( 'stExercicio'           );
$obLblExercicio->setName  ( 'stExercicio'           );
$obLblExercicio->setValue ( Sessao::getExercicio()  );
$obLblExercicio->setRotulo( 'Exercício'             );

//Componente para buscar CGM (pessoa física)
$obBscCGMSolicitante = new IPopUpCGM ($obForm);
$obBscCGMSolicitante->setTitle              ( 'Informe o CGM.'  );
$obBscCGMSolicitante->setRotulo             ('CGM'              );
$obBscCGMSolicitante->setTipo               ('juridica'         );
$obBscCGMSolicitante->setId                 ('stNomCGM'         );
$obBscCGMSolicitante->setNull               ( false             );
$obBscCGMSolicitante->setValue              ( $stNomCGM         );
$obBscCGMSolicitante->obCampoCod->setValue  ( $inCGM            );
$obBscCGMSolicitante->obCampoCod->setName   ( 'inCodCGM'        );
$obBscCGMSolicitante->obCampoCod->setSize   ( 10                );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm              );
$obFormulario->addHidden    ( $obHdnAcao           );
$obFormulario->addComponente( $obLblExercicio      );
$obFormulario->addComponente( $obBscCGMSolicitante );

$obFormulario->Cancelar ($pgForm.'?'.Sessao::getId().'&stAcao='.$stAcao);

$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
