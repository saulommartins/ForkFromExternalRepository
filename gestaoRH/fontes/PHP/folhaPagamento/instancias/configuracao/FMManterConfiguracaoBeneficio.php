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
    * Página de Form do Configuração do Cálculo de Benefícios
    * Data de Criação: 27/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php" );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
include_once ( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBeneficioEvento.class.php" );
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoBeneficio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::remove('arPlanos');

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obTFolhaPagamentoBeneficioEvento = new TFolhaPagamentoBeneficioEvento;
$obTFolhaPagamentoBeneficioEvento->recuperaRelacionamento($rsBeneficioEvento," AND beneficio_evento.cod_tipo = 1 ","",$boTransacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obIBscEvento = new IBscEvento("inCodigoEvento","stEvento");
$obIBscEvento->obBscInnerEvento->setRotulo( "Evento de Desconto de Vale-Transporte" );

$obIBscEvento->addNaturezasAceitas        ( "D"   );
$obIBscEvento->setNaturezaChecked         ( "D"   );
$obIBscEvento->setEventoSistema           ( true  );
$obIBscEvento->obBscInnerEvento->obCampoCod->setValue( $rsBeneficioEvento->getCampo("codigo") );
$obIBscEvento->obBscInnerEvento->setValue( trim($rsBeneficioEvento->getCampo("descricao")) );
$obIBscEvento->obBscInnerEvento->obCampoCodHidden->setValue( $rsBeneficioEvento->getCampo("cod_evento") );

$obIBscEventoPlanoSaude = new IBscEvento('inCodigoEventoSaude','stEventoSaude');
$obIBscEventoPlanoSaude->obBscInnerEvento->setRotulo ( "Evento de Desconto de Plano de Saúde" );
$obIBscEventoPlanoSaude->addNaturezasAceitas( "D" );
$obIBscEventoPlanoSaude->setNaturezaChecked( "D" );
$obIBscEventoPlanoSaude->setEventoSistema( true  );

// aqui é montado campo para se buscar o fornecedor do plano que será vinculado com o layout.
$obCGMFornecedor = new IPopUpCGMVinculado($obForm);
$obCGMFornecedor->setTabelaVinculo( 'compras.fornecedor' );
$obCGMFornecedor->setCampoVinculo( 'cgm_fornecedor' );
$obCGMFornecedor->setNomeVinculo( 'Fornecedor do plano' );
$obCGMFornecedor->setRotulo( 'Fornecedor do plano' );
$obCGMFornecedor->setName( 'stCGMFornecedor' );
$obCGMFornecedor->setId( 'stCGMFornecedor' );
$obCGMFornecedor->obCampoCod->setName( 'inCGMFornecedor' );
$obCGMFornecedor->obCampoCod->setId( 'inCGMFornecedor' );
$obCGMFornecedor->stTipo = "vinculadoPlanoSaude";
$obCGMFornecedor->setNull(true);

//Botão para Incluir / Limpar
$obBtnIncluir = new Button;
$obBtnIncluir->setId('btnIncluir');
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('incluirPlano', 'inCGMFornecedor,inCodigoEventoSaude');");

$obBtnLimpar = new Button;
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparPlano');");

$obSpnLista = new Span;
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm                                                          );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden( $obHdnAcao                                                       );
$obFormulario->addHidden( $obHdnCtrl                                                       );
$obFormulario->addTitulo( "Configuração do Cálculo de Benefícios"                          );
$obFormulario->addTitulo( "Vale-Transporte"                                                );
$obIBscEvento->geraFormulario( $obFormulario                                               );

$obFormulario->addTitulo( "Plano de Saúde" );
$obFormulario->addComponente( $obCGMFornecedor );
$obIBscEventoPlanoSaude->geraFormulario( $obFormulario );


$obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
$obFormulario->addSpan($obSpnLista);

$obFormulario->Ok();
$obFormulario->show();

$jsOnLoad = "executaFuncaoAjax('carregaPlanos');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
