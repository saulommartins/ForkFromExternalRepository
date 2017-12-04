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
    * Data de Criação   : 22/01/2007

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/05/17 13:01:56  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTGO."TTGOBalancoPfraaaa.class.php" );

$stPrograma = "ManterPfraaaa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLabel = new Label();
$obLabel->setValue("Os arquivos serão agrupados pelo código informado para as entidades");

$obTTGOBalancoPfraaaa = new TTGOBalancoPfraaaa();
$obTTGOBalancoPfraaaa->setDado( 'exercicio', Sessao::getExercicio() );
$obTTGOBalancoPfraaaa->recuperaRelacionamento( $rsContas );

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsContas);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Conta', 75);
$obLista->addCabecalho('Tipo Lançamento', 15);
//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_conta] - [cod_estrutural] - [descricao]');
$obLista->commitDado();

$obCmbCodigo = new Select();
$obCmbCodigo->setName( 'inCodigo_[cod_conta]' );
$obCmbCodigo->setId( 'inCodigo' );
$obCmbCodigo->addOption( '','Selecione' );
$obCmbCodigo->addOption( '1', 'Restos a Pagar(Inclusive Obrigações Patrimoniais Empenhadas)');
$obCmbCodigo->addOption( '2', 'Serviços da Dívida a Pagar(Amortização e Juros da Dívida)');
$obCmbCodigo->setValue( 'tipo_lancamento' );

$obLista->addDadoComponente( $obCmbCodigo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "tipo_lancamento" );
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);

$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addTitulo            ( "Contas" );
$obFormulario->addSpan              ($obSpnCodigos);

$obFormulario->OK      ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
