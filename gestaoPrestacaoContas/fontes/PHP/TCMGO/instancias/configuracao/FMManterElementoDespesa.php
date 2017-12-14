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
 * Página de Formulário para configuração
 * Data de Criação   : 09/10/2007

 * @author Henrique Boaventura

 * @ignore

 $Id: FMManterElementoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso : uc-06.04.00
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TTGO."TTGOElementoTribunal.class.php";

$stPrograma = "ManterElementoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

Sessao::write('link', '');
$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTElementoTribunal = new TTGOElementoTribunal();
$obTElementoTribunal->setDado( 'exercicio', Sessao::getExercicio() );
$obTElementoTribunal->recuperaElementoDespesa( $rsElemento );

Sessao::write('arElementos', $rsElemento->arElementos);

//Definição da lista
$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Elementos de Despesa');
$obLista->setRecordSet($rsElemento);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Código Estrutural',15);
$obLista->addCabecalho('Descrição',30);
$obLista->addCabecalho('Elemento de Despesa do TCM',30);

//Natureza
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_estrutural]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[descricao]');
$obLista->commitDado();

//Tipo do Bem
$obCmbElementoTCM = new Select;
$obCmbElementoTCM->setName  ( 'inElemento_[estrutural]' );
$obCmbElementoTCM->addOption( '', 'Selecione');

$obLista->addDadoComponente( $obCmbElementoTCM, false );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDadoComponente();

$obLista->montaHTML();

$obSpnElementos = new Span();
$obSpnElementos->setId( 'spnElementos' );
$obSpnElementos->setValue( $obLista->getHTML() );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();

$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addSpan   ( $obSpnElementos );

$obFormulario->OK();
$obFormulario->show();

$jsOnLoad = "montaParametrosGET('montaCombos');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
