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
    * Data de Criação   : 06/10/2008

    * @author Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id: $

    * Casos de uso :
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
include_once( CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php" );
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
$stCtrl = $_REQUEST['stCtrl'];

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

$obIPopUpCompraDireta = new Inteiro();
$obIPopUpCompraDireta->setName ( 'inCodCompraDireta'  );
$obIPopUpCompraDireta->setId   ( 'inCodCompraDireta'  );
$obIPopUpCompraDireta->setTitle( 'Código da Compra direta.' );
$obIPopUpCompraDireta->setRotulo( 'Compra Direta'      );

//periodos
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio( Sessao::read('exercicio'));

$obIPopUpMapaCompras = new IPopUpMapaCompras($obForm);
$obIPopUpMapaCompras->setTipoBusca("manterContratoCompraDireta");

$obTxtNumeroContrato = new Inteiro;
$obTxtNumeroContrato->setRotulo('Número do Contrato');
$obTxtNumeroContrato->setName('inNumContratoBusca');
$obTxtNumeroContrato->setId('inNumContrato');
$obTxtNumeroContrato->setTitle('Informe o número do contrato.');

$obComprasModalidade = new TComprasModalidade();
$rsRecordSet = new RecordSet;
$stFiltro = "   WHERE   cod_modalidade IN(8,9)  ";
$obComprasModalidade->recuperaTodos($rsRecordSet,$stFiltro);

$obISelectModalidade = new Select();
$obISelectModalidade->setRotulo            ("Modalidade"                            );
$obISelectModalidade->setTitle             ("Selecione a modalidade."               );
$obISelectModalidade->setName              ("inCodModalidade"                       );
$obISelectModalidade->setNull              (true                                    );
$obISelectModalidade->setCampoID           ("cod_modalidade"                        );
$obISelectModalidade->addOption            ("","Selecione"                          );
$obISelectModalidade->setCampoDesc         ("[cod_modalidade] - [descricao]"        );
$obISelectModalidade->preencheCombo        ($rsRecordSet                            );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->setAjuda         ("UC-03.05.22"          );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addTitulo        ( "Dados para Filtro"   );
$obFormulario->addComponente    ( $obISelectMultiploEntidadeGeral);
$obFormulario->addComponente    ( $obIPopUpCompraDireta    );
$obFormulario->addComponente    ( $obISelectModalidade  );
$obFormulario->addComponente    ( $obPeriodicidade      );
$obFormulario->addComponente    ( $obIPopUpMapaCompras  );
$obFormulario->addComponente    ( $obTxtNumeroContrato  );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
