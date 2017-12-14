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
* Página de Formulario de Inclusao de Sequência de Cálculo
* Data de Criação: 05/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30711 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

//Define a função do arquivo, ex: incluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write("link","");

//Define o nome dos arquivos PHP
$stPrograma = "ManterSequenciaCalculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto TEXTBOX para o número da sequência para o filtro
$obTxtSequenciaFiltro = new TextBox;
$obTxtSequenciaFiltro->setRotulo            ( "Número"                                      );
$obTxtSequenciaFiltro->setTitle             ( "Informe o número da sequência para o filtro" );
$obTxtSequenciaFiltro->setName              ( "inSequenciaFiltro"                           );
$obTxtSequenciaFiltro->setId                ( "inSequenciaFiltro"                           );
$obTxtSequenciaFiltro->setValue             ( $inSequenciaFiltro                            );
$obTxtSequenciaFiltro->setSize              ( 10 );
$obTxtSequenciaFiltro->setMaxLength         ( 10 );
$obTxtSequenciaFiltro->setInteiro           ( true );

//Define objeto TEXTBOX para armazenar a DESCRICAO da sequência para o filtro
$obTxtDescricaoFiltro = new TextBox;
$obTxtDescricaoFiltro->setRotulo            ( "Descrição"                                      );
$obTxtDescricaoFiltro->setTitle             ( "Informe a descrição da sequência para o filtro" );
$obTxtDescricaoFiltro->setName              ( "stDescricaoFiltro"                              );
$obTxtDescricaoFiltro->setId                ( "stDescricaoFiltro"                              );
$obTxtDescricaoFiltro->setValue             ( $stDescricaoFiltro                               );
$obTxtDescricaoFiltro->setStyle             ( "width:300px"                                    );
$obTxtDescricaoFiltro->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]'      );
$obTxtDescricaoFiltro->setMaxLength         ( 80 );
$obTxtDescricaoFiltro->setEspacosExtras     ( false );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm               );
$obFormulario->addTitulo        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden        ( $obHdnAcao            );
$obFormulario->addHidden        ( $obHdnCtrl            );
$obFormulario->addTitulo        ( "Dados para o Filtro" );
$obFormulario->addComponente    ( $obTxtSequenciaFiltro );
$obFormulario->addComponente    ( $obTxtDescricaoFiltro );

$obFormulario->OK();

$obFormulario->setFormFocus($obTxtSequenciaFiltro->getId() );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
