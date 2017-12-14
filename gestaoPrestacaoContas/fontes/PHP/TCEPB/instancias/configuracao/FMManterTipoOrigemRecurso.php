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

    * @author Diego Barbosa Victoria

    * @ignore

    * Casos de uso : uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/04/27 18:31:00  hboaventura
Arquivos para geração do TCEPB

Revision 1.2  2007/04/23 15:37:15  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/01/25 20:39:47  diego
Novos arquivos de exportação.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBTipoOrigemRecursos.class.php");
include_once(TTPB."TTPBRecurso.class.php");

$stPrograma = "ManterTipoOrigemRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

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

$obTMapeamento = new TTPBRecurso();
$obTMapeamento->recuperaRecurso( $rsRecursos );

$obTTipoOrigemRecurso = new TTPBTipoOrigemRecurso();
$obTTipoOrigemRecurso->recuperaTodos( $rsTipoOrigemRecurso, ' order by descricao ' );

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsRecursos);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Recurso', 75);
$obLista->addCabecalho('Código TCE', 15);
//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_recurso');
$obLista->commitDado();

$obCmbCodigoRecurso = new Select();
$obCmbCodigoRecurso->setName     ('inCodigo_[cod_recurso]');
$obCmbCodigoRecurso->setCampoId  ('cod_tipo');
$obCmbCodigoRecurso->setCampoDesc('descricao');
$obCmbCodigoRecurso->addOption   ('','Selecione');
$obCmbCodigoRecurso->setValue    ('cod_tipo');
$obCmbCodigoRecurso->preencheCombo( $rsTipoOrigemRecurso );

$obLista->addDadoComponente( $obCmbCodigoRecurso , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
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
$obFormulario->addTitulo            ( "Parâmetros por Entidade" );
$obFormulario->addSpan              ($obSpnCodigos);

$obFormulario->OK      ();
$obFormulario->show();

//SistemaLegado::executaFrameOculto( "buscaValor('recuperaFormularioAlteracao','$pgOcul','$pgProc','','Sessao::getId()');" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
