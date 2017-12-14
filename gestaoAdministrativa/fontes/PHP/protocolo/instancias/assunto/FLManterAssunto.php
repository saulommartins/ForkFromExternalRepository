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
* Arquivo de instância para manutenção de normas
* Data de Criação: 04/09/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 24983 $
$Name$
$Author: domluc $
$Date: 2007-08-21 16:50:15 -0300 (Ter, 21 Ago 2007) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_PROT_MAPEAMENTO."TPROClassificacao.class.php");
$stPrograma = "ManterAssunto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'excluir';

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('link');

$obTPROClassificacao = new TPROClassificacao;
$obTPROClassificacao->recuperaTodos($rsClassificacao,'','nom_classificacao');

$obForm = new Form();
$obForm->setAction($pgList);

$obHdnAcao = new hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obTxtClassificacao = new TextBox();
$obTxtClassificacao->setName		('inTxtCodigoClassificacao');
$obTxtClassificacao->setRotulo		('Classificação');
$obTxtClassificacao->setInteiro		(true);
$obTxtClassificacao->setMaxLength   (5);
$obTxtClassificacao->setSize		(5);
$obTxtClassificacao->setNull		(false);

$obCmbClassificacao = new Select();
$obCmbClassificacao->setName		('inCodigoClassificacao');
$obCmbClassificacao->setRotulo		('Classificação');
$obCmbClassificacao->setCampoId		('cod_classificacao');
$obCmbClassificacao->setCampoDesc	('nom_classificacao');
$obCmbClassificacao->addOption		('','Selecione');
$obCmbClassificacao->preencheCombo	($rsClassificacao);
$obCmbClassificacao->setStyle		('width:250px');
$obCmbClassificacao->setNull		(false);

$obFormulario = new Formulario;
$obFormulario->addForm	($obForm);
$obFormulario->addTitulo('Dados para o filtro:');
$obFormulario->setAjuda('uc-01.06.95');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addComponenteComposto($obTxtClassificacao,$obCmbClassificacao);
$obFormulario->Ok();
$obFormulario->show();
?>
