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
Revision 1.1  2007/05/11 15:10:56  hboaventura
Arquivos para geração do TCEPB

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBPlanoAnaliticaRelacionamento.class.php" );

$stPrograma = "ManterRelacionamentoExtra";
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

//Lista de códigos cadastrados para cada entidade
// OR  pc.cod_estrutural LIKE '2.1.2%'

$obTTPBPlanoAnaliticaRelacionamento = new TTPBPlanoAnaliticaRelacionamento();
$stFiltro = "   AND  plano_conta.exercicio = '".Sessao::getExercicio()."'
                AND (plano_conta.cod_estrutural LIKE '1.1.2%' OR plano_conta.cod_estrutural LIKE '2.1%' OR plano_conta.cod_estrutural LIKE '1.1.3%')

                AND EXISTS (    SELECT  conta_debito.cod_plano
                                  FROM  contabilidade.conta_debito
                                 WHERE  plano_analitica.cod_plano = conta_debito.cod_plano
                                   AND  plano_analitica.exercicio = conta_debito.exercicio

                                 UNION

                                SELECT  conta_credito.cod_plano
                                  FROM  contabilidade.conta_credito
                                 WHERE  plano_analitica.cod_plano = conta_credito.cod_plano
                                   AND  plano_analitica.exercicio = conta_credito.exercicio

                                 LIMIT  1
                           ) 
            ";
            
$stOrder = "    ORDER BY    plano_conta.cod_estrutural   ";
$obTTPBPlanoAnaliticaRelacionamento->recuperaContaAnalitica( $rsContas, $stFiltro, $stOrder );

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsContas);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Reduzido',5 );
$obLista->addCabecalho('Estrutural', 25);
$obLista->addCabecalho('Descrição da Conta', 35);
$obLista->addCabecalho('Receita Extra', 15);
$obLista->addCabecalho('Despesa Extra', 15);
$obLista->addCabecalho('Tipo de Retenção', 15);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_plano');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obCmbCodigoReceita = new Select();
$obCmbCodigoReceita->setName  ( 'inCodigoReceita_[cod_plano]' );
$obCmbCodigoReceita->setId    ( 'inCodigoReceita' );
$obCmbCodigoReceita->addOption( '','Selecione' );

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_conta');
$obLista->commitDado();

if (Sessao::getExercicio() >= '2014') {
    
    $obCmbCodigoReceita->addOption( '10000014', 'Consignações');
    $obCmbCodigoReceita->addOption( '10000015', 'Débitos de Tesouraria');
    $obCmbCodigoReceita->addOption( '10000016', 'Depósitos');
    $obCmbCodigoReceita->addOption( '10000017', 'Outras Operações');
    
    $obCmbCodigoDespesa = new Select();
    $obCmbCodigoDespesa->setName   ( 'inCodigoDespesa_[cod_plano]' );
    $obCmbCodigoDespesa->setId     ( 'inCodigoDespesa' );
    $obCmbCodigoDespesa->addOption ( '','Selecione' );
    $obCmbCodigoDespesa->addOption ( '20000010', 'Restos a Pagar');
    $obCmbCodigoDespesa->addOption ( '20000011', 'Serviços da Divida');
    $obCmbCodigoDespesa->addOption ( '20000012', 'Débitos de Tesouraria');
    $obCmbCodigoDespesa->addOption ( '20000017', 'Consignações');
    $obCmbCodigoDespesa->addOption ( '20000018', 'Depósitos');
    $obCmbCodigoDespesa->addOption ( '20000019', 'Outras Operações');
    $obCmbCodigoDespesa->setValue  ( 'cod_relacionamento_despesa' );
    
}elseif(Sessao::getExercicio() < '2014'){
    
    $obCmbCodigoReceita->addOption( '10000010', 'Consignações - INSS');
    $obCmbCodigoReceita->addOption( '10000011', 'Consignações - Previdência Própria');
    $obCmbCodigoReceita->addOption( '10000012', 'Consignações - ISS');
    $obCmbCodigoReceita->addOption( '10000013', 'Consignações - IR');
    $obCmbCodigoReceita->addOption( '10000014', 'Consignações - Outras');
    $obCmbCodigoReceita->addOption( '10000015', 'Débitos de Tesouraria');
    $obCmbCodigoReceita->addOption( '10000016', 'Depósitos');
    $obCmbCodigoReceita->addOption( '10000017', 'Outras Operações');
    $obCmbCodigoReceita->addOption( '10000018', 'Consignações Previdenciárias - Fundef Magisterio');
    $obCmbCodigoReceita->addOption( '10000019', 'Consignações Previdenciárias - Fundef Outras Despesas');
    $obCmbCodigoReceita->addOption( '10000020', 'Consignações Previdenciárias - Saúde');
    
}elseif (Sessao::getExercicio() == '2009') {

    $obCmbCodigoReceita->addOption( '10000021', 'Consignações Previdenciárias - MDE');
    $obCmbCodigoReceita->addOption( '10000022', 'Consignações Pensões Alimentícias');
    $obCmbCodigoReceita->addOption( '10000023', 'Consignações Empréstimos');
    $obCmbCodigoReceita->addOption( '10000024', 'Consignações Plano de Saúde');
    $obCmbCodigoReceita->addOption( '10000025', 'Salário-Família');
    $obCmbCodigoReceita->addOption( '10000026', 'Salário-Maternidade');
    $obCmbCodigoReceita->addOption( '10000027', 'Cauções');
    $obCmbCodigoReceita->addOption( '10000028', 'Fianças');
    $obCmbCodigoReceita->addOption( '10000029', 'Estorno de Pagamento do exercício corrente');

}
$obCmbCodigoReceita->setValue( 'cod_relacionamento_receita' );

$obLista->addDadoComponente( $obCmbCodigoReceita , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obLista->addDadoComponente( $obCmbCodigoDespesa , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor_despesa" );
$obLista->commitDadoComponente();

$obCmbTipo = new Select();
$obCmbTipo->setName  ( 'inRetencao_[cod_plano]' );
$obCmbTipo->setId    ( 'inRetencao' );
$obCmbTipo->addOption( '','Selecione' );
$obCmbTipo->addOption( '1', 'ISS');
$obCmbTipo->addOption( '2', 'IRRF');
$obCmbTipo->addOption( '3', 'INSS');
$obCmbTipo->addOption( '4', 'Previdência Própria');
$obCmbTipo->addOption( '5', 'Outras Consignações');
$obCmbTipo->setValue( 'cod_tipo' );

$obLista->addDadoComponente( $obCmbTipo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor_retencao" );
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

$obFormulario->OK(true);
$obFormulario->show();

SistemaLegado::LiberaFrames(true, false);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
