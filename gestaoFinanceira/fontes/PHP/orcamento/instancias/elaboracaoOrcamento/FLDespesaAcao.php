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
    * Novo filtro para inclusão de despesa, agora utilizando ação
    * Data de Criação   : 12/08/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE.'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_COMPONENTES.'IMontaRecursoDestinacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'DespesaAcao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stCtrl = $request->get('stCtrl');
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if (empty($stAcao)) {
    $stAcao = 'excluir';
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal'); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

# Define label de intervalo.
$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

# Define intervalo inicial da ação
$obTxtAcaoInicio = new TextBox();
$obTxtAcaoInicio->setName('inNumAcaoInicio');
$obTxtAcaoInicio->setRotulo('Código Ação');
$obTxtAcaoInicio->setTitle('Informe o intervalo de Códigos de Ação a consultar.');
$obTxtAcaoInicio->setInteiro(true);
$obTxtAcaoInicio->setMascara('9999');
$obTxtAcaoInicio->setPreencheComZeros('E');

# Define intervalo final da ação
$obTxtAcaoFim= new TextBox();
$obTxtAcaoFim->setName('inNumAcaoFim');
$obTxtAcaoFim->setRotulo('Código Ação');
$obTxtAcaoFim->setInteiro(true);
$obTxtAcaoFim->setMascara('9999');
$obTxtAcaoFim->setPreencheComZeros('E');

$obTxtNomAcao = new TextBox;
$obTxtNomAcao->setId       ('stNomAcao');
$obTxtNomAcao->setName     ('stNomAcao');
$obTxtNomAcao->setRotulo   ('Descrição da Ação');
$obTxtNomAcao->setSize     (90);
$obTxtNomAcao->setMaxLength(150);

$arTxtIntervaloAcao = array($obTxtAcaoInicio, $obLblIntervalo, $obTxtAcaoFim);

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->agrupaComponentes($arTxtIntervaloAcao);
$obFormulario->addComponente($obTxtNomAcao);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
