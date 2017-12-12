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
    * Página de Filtro Historico Padrão
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: FLHistoricoPadrao.php 60984 2014-11-27 12:35:45Z carlos.silva $

    * Casos de uso: uc-02.02.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php");

$stPrograma = "HistoricoPadrao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/

Sessao::remove('filtro');
Sessao::remove('link');

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCHistoricoPadrao.php" );

$obOk = new Ok;
$obLimpar = new Limpar;

$obTextBoxDescricao = new TextBox;
$obTextBoxDescricao->setName ("stDescricao");
$obTextBoxDescricao->setId ("stDescricao");
$obTextBoxDescricao->setTitle ("Informe a descrição do histórico");
$obTextBoxDescricao->setRotulo ("Descrição do Histórico");
$obTextBoxDescricao->setSize(80);
$obTextBoxDescricao->setMaxLength(80);

//CODIGO
//Define o objeto TEXT para Codigo do Historico Padrao Inicial
$obTxtCodHistoricoInicial = new TextBox;
$obTxtCodHistoricoInicial->setName("inCodHistoricoInicial");
$obTxtCodHistoricoInicial->setTitle("Informe o(s) Número(s) do(s) Histórico(s) que deseja pesquisar");
//$obTxtCodHistoricoInicial->setValue($inCodEmpenhoInicial);
$obTxtCodHistoricoInicial->setRotulo("Número do Histórico Padrão");
$obTxtCodHistoricoInicial->setInteiro(true);

//Define objeto Label
$obLblHistorico = new Label;
$obLblHistorico->setValue("a");

//Define o objeto TEXT para Codigo do Historico Padrao Final
$obTxtCodHistoricoFinal = new TextBox;
$obTxtCodHistoricoFinal->setName("inCodHistoricoFinal");
$obTxtCodHistoricoFinal->setTitle("Informe o(s) Número(s) do(s) Histórico(s) que deseja pesquisar");
//$obTxtCodHistoricoFinal->setValue($inCodEmpenhoFinal);
$obTxtCodHistoricoFinal->setRotulo("Número do Histórico Padrão");
$obTxtCodHistoricoFinal->setInteiro(true);

$obCmbComplemento = new Select;
$obCmbComplemento->setTitle ('Informe se deseja demonstrar com complemento.');
$obCmbComplemento->setRotulo ('Complemento');
$obCmbComplemento->setName ('stComplemento');
$obCmbComplemento->setId ('stComplemento');
$obCmbComplemento->addOption ("a", "Ambos");
$obCmbComplemento->addOption ("s", "Sim");
$obCmbComplemento->addOption ("n", "Não");

// Define ordenação
$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setTitle ('Informe a ordenação do relatório.');
$obCmbOrdenacao->setRotulo ('Ordenação');
$obCmbOrdenacao->setName ('stOrdenacao');
$obCmbOrdenacao->setId ('stOrdenacao');
$obCmbOrdenacao->addOption ("cod_historico", "Código Reduzido");
$obCmbOrdenacao->addOption ("nom_historico", "Descrição");

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda          ('UC-02.02.20');
$obFormulario->addForm           ( $obForm );
$obFormulario->addHidden         ( $obHdnAcao    );
$obFormulario->addHidden         ( $obHdnCtrl    );
$obFormulario->addHidden         ( $obHdnCaminho );

$obFormulario->addTitulo         ( "Dados para Filtro" );
$obFormulario->addComponente     ( $obTextBoxDescricao );
$obFormulario->agrupaComponentes (array($obTxtCodHistoricoInicial, $obLblHistorico, $obTxtCodHistoricoFinal));
$obFormulario->addComponente     ( $obCmbComplemento );
$obFormulario->addComponente     ( $obCmbOrdenacao );

$obFormulario->defineBarra       ( array( $obOk, $obLimpar ) );
$obFormulario->show              ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
