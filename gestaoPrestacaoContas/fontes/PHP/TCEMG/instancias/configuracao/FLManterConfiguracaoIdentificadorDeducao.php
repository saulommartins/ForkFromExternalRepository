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
  * Página de Filtro de Configuração de Identificador de Dedução - TCE-MG
  * Data de Criação   : 17/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: FLManterConfiguracaoIdentificadorDeducao.php 59612 2014-09-02 12:00:51Z gelson $
  *
  * $Revision: 59612 $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpEstruturalReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IPopUpReceita.class.php" );
include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpReceita.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoIdentificadorDeducao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obROrcamentoReceita = new ROrcamentoReceita;

//Recupera Mascara da Classificao de Receita
                     $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setDedutora( true );
$mascClassificacao = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtroRelatorio');
Sessao::remove('link');

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $mascClassificacao );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtCodClassificacao = new TextBox;
$obTxtCodClassificacao->setName     ( "inCodClassificacao" );
$obTxtCodClassificacao->setValue    ( $inCodClassificacao );
$obTxtCodClassificacao->setRotulo   ( "Código" );
$obTxtCodClassificacao->setSize     ( strlen($mascClassificacao) );
$obTxtCodClassificacao->setMaxLength( strlen($mascClassificacao) );
$obTxtCodClassificacao->setNull     ( true );
$obTxtCodClassificacao->setTitle    ( 'Informe o código.' );
$obTxtCodClassificacao->obEvento->setOnKeyUp("mascaraDinamico('".$mascClassificacao."', this, event);");
$obTxtCodClassificacao->obEvento->setOnChange("buscaValor('mascaraClassificacaoFiltro','".$pgOcul."','".$pgList."','telaPrincipal','".Sessao::getId()."')");

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDesc = new TextBox;
$obTxtDesc->setName     ( "stDescricao" );
$obTxtDesc->setRotulo   ( "Descrição" );
$obTxtDesc->setSize     ( 80 );
$obTxtDesc->setMaxLength( 80 );
$obTxtDesc->setNull     ( true );
$obTxtDesc->setTitle    ( 'Informe a descrição.' );

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao();
if($stAcao != 'incluir') $obIMontaRecursoDestinacao->setFiltro( true );

$obIIntervaloPopUpReceita = new IIntervaloPopUpReceita('', $boDedutora = true);
$obIIntervaloPopUpReceita->obIPopUpReceitaInicial->setTipoBusca        ( 'receitaDedutoraExportacao' );
$obIIntervaloPopUpReceita->obIPopUpReceitaInicial->setId               ( "stDescricaoReceitaInicial" );
$obIIntervaloPopUpReceita->obIPopUpReceitaInicial->obCampoCod->setName ( "inCodReceitaInicial" );
$obIIntervaloPopUpReceita->obIPopUpReceitaFinal->setTipoBusca          ( 'receitaDedutoraExportacao' );
$obIIntervaloPopUpReceita->obIPopUpReceitaFinal->setId                 ( "stDescricaoReceitaFinal" );
$obIIntervaloPopUpReceita->obIPopUpReceitaFinal->obCampoCod->setName   ( "inCodReceitaFinal" );

$obIPopUpEstruturalReceita = new IPopUpEstruturalReceita( $boDedutora = true);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnMascClassificacao );

$obFormulario->addTitulo( "Dados para Filtro"        );
$obIMontaRecursoDestinacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obIIntervaloPopUpReceita  );
$obFormulario->addComponente( $obIPopUpEstruturalReceita );
$obFormulario->addComponente( $obTxtDesc             );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
