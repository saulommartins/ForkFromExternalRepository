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
/*
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
$pgForm     = "FMImportar".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$arHeader   = Sessao::read('arHeader');
$arDetalhe  = Sessao::read('arDetalhe');
$arTrailler = Sessao::read('arTrailler');

/*
 * Definição dos componentes
 *
 */
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setValue('importar');

$obLblCompetencia = new Label;
$obLblCompetencia->setRotulo('Competência');
$obLblCompetencia->setValue($arHeader['stCompetencia']);

$obLblQuantidadeServidores = new Label;
$obLblQuantidadeServidores->setRotulo('Quantidade de Servidores');
$obLblQuantidadeServidores->setValue((int) $arTrailler['inQuantidadeRegistros'] - 2 );

$stValor = $arTrailler['inValorTotal'];
$stValor = substr($stValor,0,-2).'.'.substr($stValor,-2);
$stValor = number_format($stValor,2,',','.');

$obLblSomatorioValores = new Label;
$obLblSomatorioValores->setRotulo('Somatório de Valores à Consignar');
$obLblSomatorioValores->setValue('R$ '.$stValor);

$obForm = new  Form;
$obForm->setAction( $pgProc);
$obForm->setTarget("oculto");

$obBtnImportar = new Button;
$obBtnImportar->setValue('Importar Empréstimos');
$obBtnImportar->obEvento->setOnClick('BloqueiaFrames(true,false);jQuery(\'#frm\')[0].submit();');

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Resumo Arquivo Importação do Banco Banrisul');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addComponente($obLblCompetencia);
$obFormulario->addComponente($obLblQuantidadeServidores);
$obFormulario->addComponente($obLblSomatorioValores);
$obFormulario->defineBarra(array($obBtnImportar));
$obFormulario->show();
?>
