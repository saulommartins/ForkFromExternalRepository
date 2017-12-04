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
    * Página de Formulario de Vinculo entre a marca do URBEM e a do SIGA
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: FMManterMarca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO ."TTBAMarca.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterMarca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//SistemaLegado::executaFramePrincipal( "buscaDado('MontaListaUniOrcam');" );

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obPersistente = new TTBAMarca();
$obPersistente->obTFrotaMarca->recuperaTodos($rsRecordSetCombo,' ORDER BY nom_marca');

/**
 * recupera os tipos de veiculo do tcm, filtrando pelos que foram vinculados na acao
 * de manter vinculo veiculo
 */
$stFiltro = "
    WHERE EXISTS ( SELECT 1
                     FROM tcmba.tipo_veiculo_vinculo
                    WHERE tipo_veiculo_vinculo.cod_tipo_tcm = tipo_veiculo.cod_tipo_tcm
                 )
";
$obPersistente->obTTBATipoVeiculo->recuperaTodos($rsTipoVeiculo,$stFiltro);

while ( !$rsTipoVeiculo->eof() ) {
    unset($rsRecordSetLista);
    $obPersistente->setDado('cod_tipo_tcm',$rsTipoVeiculo->getCampo('cod_tipo_tcm'));
    $obPersistente->recuperaMarca($rsRecordSetLista);

    $obLista = new Lista;
    $obLista->setTitulo ( "Relacionamento com Marcas - ".$rsTipoVeiculo->getCampo('descricao'));
    $obLista->setRecordSet ($rsRecordSetLista );
    $obLista->setMostraPaginacao( false );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Marca - TCM" );
    $obLista->ultimoCabecalho->setWidth( 67 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Marca - Sistema" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[descricao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obCmbCombo = new Select();
    $obCmbCombo->setName          ("inMarca_[cod_tipo_tcm]_[cod_marca_tcm]_"   );
    $obCmbCombo->setTitle         ("Selecione"                  );
    $obCmbCombo->setRotulo        (""                           );
    $obCmbCombo->addOption        ("","Selecione"               );
    $obCmbCombo->setCampoId       ("cod_marca"                  );
    $obCmbCombo->setCampoDesc     ("[cod_marca] - [nom_marca]"  );
    $obCmbCombo->preencheCombo    ($rsRecordSetCombo            );
    $obCmbCombo->setNull          ( false                       );
    $obCmbCombo->setValue         ("cod_marca");

    $obLista->addDadoComponente( $obCmbCombo );
    $obLista->ultimoDado->setCampo( "cod_marca" );
    $obLista->commitDadoComponente();
    $obLista->montaInnerHTML();

    $stLista .= $obLista->getHTML();

    $rsTipoVeiculo->proximo();
}

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnLista );

$obFormulario->defineBarra( array( new Ok() ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
