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

    * Filtro de empréstimos Banrisul
    * Data de Criação   : 01/09/2009

    * @author Analista      Dagine Rodrigues Vieira
    * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "EmprestimoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcArq  = "PR".$stPrograma."Arquivo.php";
$pgProcRel  = "PR".$stPrograma."Relatorio.php";
$pgFormImp  = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$stCompetencia = str_pad($_REQUEST['inCodMes'], 2, '0',STR_PAD_LEFT).'/'.$_REQUEST['inAno'];

$obLblCompetencia = new Label;
$obLblCompetencia->setRotulo('Competência');
$obLblCompetencia->setValue($stCompetencia);

$obHdnCompetencia = new Hidden;
$obHdnCompetencia->setName('stCompetencia');
$obHdnCompetencia->setValue($stCompetencia);

switch ($_REQUEST['stFiltro']) {
    case 'A':
            $stCadastro = 'Ativos';
        break;
    case 'P':
            $stCadastro = 'Aposentados';
        break;
    case 'E':
            $stCadastro = 'Pensionistas';
        break;
    default:
            $stCadastro = 'Todos';
        break;
}

$obHdnMovimentacao = new Hidden;
$obHdnMovimentacao->setName('inCodPeriodoMovimentacao');
$obHdnMovimentacao->setValue($_REQUEST['inCodPeriodoMovimentacao']);

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName('stCadastro');
$obHdnCadastro->setValue($stCadastro);

$obLblCadastro = new Label;
$obLblCadastro->setRotulo('Cadastro');
$obLblCadastro->setValue($stCadastro);

$obLblQuantidade = new Label;
$obLblQuantidade->setRotulo('Quatidade Servidores');
$obLblQuantidade->setValue($_REQUEST['inQuantidade']);

$obBtnArquivo = new Button;
$obBtnArquivo->setValue('Download do Arquivo');
$obBtnArquivo->obEvento->setOnclick("javascript: EnviarArquivo();");
if ($_REQUEST['inQuantidade'] < 1) {
    $obBtnArquivo->setDisabled(true);
}

$obBtnRelatorio = new Button;
$obBtnRelatorio->setValue('Relatório de Conferência');
$obBtnRelatorio->obEvento->setOnclick("javascript: EnviarRelatorio();");
if ($_REQUEST['inQuantidade'] < 1) {
    $obBtnRelatorio->setDisabled(true);
}

$obForm = new Form;
$obForm->setTarget('oculto');

$obFormulario = new Formulario;
$obFormulario->addTitulo('Resumo Arquivo Retorno ao Banco Banrisul');
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnMovimentacao);
$obFormulario->addHidden($obHdnCompetencia);
$obFormulario->addHidden($obHdnCadastro);
$obFormulario->addComponente($obLblCompetencia);
$obFormulario->addComponente($obLblCadastro);
$obFormulario->addComponente($obLblQuantidade);
$obFormulario->defineBarra(array($obBtnArquivo,$obBtnRelatorio));
$obFormulario->show();
include $pgJS;
?>
