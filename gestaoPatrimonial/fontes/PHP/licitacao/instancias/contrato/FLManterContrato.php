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
    * Página de Formulário para cadastro de documentos exigidos
    * Data de Criação   : 06/10/2006

    * @author Leandro André Zis

    * @ignore

    * $Id: FLManterContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoLicitacao.class.php");
include_once(TCOM."TComprasObjeto.class.php");
include_once(TCOM."TComprasFornecedor.class.php");
include_once(TLIC."TLicitacaoDocumentosAtributos.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
include_once(CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php");
include_once(CAM_GP_LIC_COMPONENTES."ISelectDocumento.class.php");
include_once(CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php");
include_once( CAM_GP_LIC_COMPONENTES."ISelectModalidadeLicitacao.class.php" );
include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeGeral.class.php");
include_once(CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php");

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $request->get('stCtrl');

$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obISelectMultiploEntidadeGeral = new ISelectMultiploEntidadeGeral();
$obISelectMultiploEntidadeGeral->setNull( true );

$obIPopUpLicitacao = new Inteiro();
$obIPopUpLicitacao->setName ( 'inCodLicitacao'  );
$obIPopUpLicitacao->setId   ( 'inCodLicitacao'  );
$obIPopUpLicitacao->setTitle( 'Número da licitação.' );
$obIPopUpLicitacao->setRotulo( 'Licitação'      );

$obPeriodicidade = new Periodicidade;

$obIPopUpMapaCompras = new IPopUpMapaCompras($obForm);
$obIPopUpMapaCompras->setTipoBusca("manterContrato");

$obTxtNumeroContrato = new Inteiro;
$obTxtNumeroContrato->setRotulo('Número do Contrato');
$obTxtNumeroContrato->setName('inNumContrato');
$obTxtNumeroContrato->setId('inNumContrato');
$obTxtNumeroContrato->setTitle('Informe o número do contrato.');

$obComprasModalidade = new ISelectModalidadeLicitacao();

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->setAjuda         ("UC-03.05.22"          );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obISelectMultiploEntidadeGeral);
$obFormulario->addComponente    ( $obIPopUpLicitacao    );
$obComprasModalidade->geraFormulario($obFormulario);
$obFormulario->addComponente    ( $obPeriodicidade      );
$obFormulario->addComponente    ( $obIPopUpMapaCompras  );
$obFormulario->addComponente    ( $obTxtNumeroContrato  );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
