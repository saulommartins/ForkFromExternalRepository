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
include_once(TTPE."TTPEPlanoAnaliticaRelacionamento.class.php" );

$stPrograma = "ManterRecDespExtra";
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

$obTTPEPlanoAnaliticaRelacionamento = new TTPEPlanoAnaliticaRelacionamento();
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
$obTTPEPlanoAnaliticaRelacionamento->recuperaContaAnalitica( $rsContas, $stFiltro, $stOrder );

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsContas);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Reduzido',4 );
$obLista->addCabecalho('Estrutural', 10);
$obLista->addCabecalho('Descrição da Conta', 35);
$obLista->addCabecalho('Receita Extra', 13);
$obLista->addCabecalho('Despesa Extra', 13);
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

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_conta');
$obLista->commitDado();

$obCmbCodigoReceita = new Select();
$obCmbCodigoReceita->setName  ( 'inCodigoReceita_[cod_plano]' );
$obCmbCodigoReceita->setId    ( 'inCodigoReceita' );
$obCmbCodigoReceita->addOption( '','Selecione' );
$obCmbCodigoReceita->addOption('10000051', 'Serviços da Dívida a Pagar');
$obCmbCodigoReceita->addOption('10000052', 'Débitos de Tesouraria');
$obCmbCodigoReceita->addOption('10000053', 'Consignações – ISS');
$obCmbCodigoReceita->addOption('10000054', 'Consignações – IR');
$obCmbCodigoReceita->addOption('10000055', 'Consignações – Outras');
$obCmbCodigoReceita->addOption('10000056', 'Depósitos');
$obCmbCodigoReceita->addOption('10000057', 'Outras Operações');
$obCmbCodigoReceita->addOption('10000060', 'Restos a Pagar – Saúde');
$obCmbCodigoReceita->addOption('10000061', 'Restos a Pagar – Educação (FUNDEB)');
$obCmbCodigoReceita->addOption('10000062', 'Restos a Pagar – Educação (Demais)');
$obCmbCodigoReceita->addOption('10000063', 'Restos a Pagar – Câmara Municipal');
$obCmbCodigoReceita->addOption('10000064', 'Restos a Pagar – Demais');
$obCmbCodigoReceita->addOption('10000065', 'Consignações – INSS (Saúde)');
$obCmbCodigoReceita->addOption('10000066', 'Consignações – INSS (FUNDEB 60%)');
$obCmbCodigoReceita->addOption('10000067', 'Consignações – INSS (Educação – demais)');
$obCmbCodigoReceita->addOption('10000068', 'Consignações – INSS (Câmara)');
$obCmbCodigoReceita->addOption('10000069', 'Consignações – INSS (Demais)');
$obCmbCodigoReceita->addOption('10000070', 'Consignações Previdenciárias – Saúde');
$obCmbCodigoReceita->addOption('10000071', 'Consignações – Previdência Própria (FUNDEB 60%)');
$obCmbCodigoReceita->addOption('10000072', 'Consignações – Previdência Própria (Educação – demais)');
$obCmbCodigoReceita->addOption('10000073', 'Consignações – Previdência Própria (Câmara)');
$obCmbCodigoReceita->addOption('10000074', 'Consignações – Previdência Própria (Demais)');
$obCmbCodigoReceita->addOption('10000076', 'Outras transferências');
$obCmbCodigoReceita->addOption('10000077', 'Consignações Empréstimos');
$obCmbCodigoReceita->addOption('10000078', 'Consignações Pensões Alimentícias');
$obCmbCodigoReceita->addOption('10000079', 'Cauções');
$obCmbCodigoReceita->setStyle('width:200px;');
$obCmbCodigoReceita->setValue( 'cod_relacionamento_receita' );
    
$obCmbCodigoDespesa = new Select();
$obCmbCodigoDespesa->setName   ( 'inCodigoDespesa_[cod_plano]' );
$obCmbCodigoDespesa->setId     ( 'inCodigoDespesa' );
$obCmbCodigoDespesa->addOption ( '','Selecione' );
$obCmbCodigoDespesa->addOption('20000051', 'Serviços da Dívida a Pagar');
$obCmbCodigoDespesa->addOption('20000052', 'Débitos de Tesouraria');
$obCmbCodigoDespesa->addOption('20000053', 'Consignações – ISS');
$obCmbCodigoDespesa->addOption('20000054', 'Consignações – IR');
$obCmbCodigoDespesa->addOption('20000055', 'Consignações – Outras');
$obCmbCodigoDespesa->addOption('20000056', 'Depósitos');
$obCmbCodigoDespesa->addOption('20000057', 'Outras Operações');
$obCmbCodigoDespesa->addOption('20000060', 'Restos a Pagar - Saúde');
$obCmbCodigoDespesa->addOption('20000061', 'Restos a Pagar - Educação (FUNDEB)');
$obCmbCodigoDespesa->addOption('20000062', 'Restos a Pagar - Educação (Demais)');
$obCmbCodigoDespesa->addOption('20000063', 'Restos a Pagar - Câmara Municipal');
$obCmbCodigoDespesa->addOption('20000064', 'Restos a Pagar - Demais');
$obCmbCodigoDespesa->addOption('20000065', 'Consignações - INSS (Saúde)');
$obCmbCodigoDespesa->addOption('20000066', 'Consignações - INSS (FUNDEB 60%)');
$obCmbCodigoDespesa->addOption('20000067', 'Consignações - INSS (Educação - demais)');
$obCmbCodigoDespesa->addOption('20000068', 'Consignações - INSS (Câmara)');
$obCmbCodigoDespesa->addOption('20000069', 'Consignações - INSS (Demais)');
$obCmbCodigoDespesa->addOption('20000070', 'Consignações - Previdência Própria (Saúde)');
$obCmbCodigoDespesa->addOption('20000071', 'Consignações - Previdência Própria (FUNDEB 60%)');
$obCmbCodigoDespesa->addOption('20000072', 'Consignações - Previdência Própria (Educação - demais)');
$obCmbCodigoDespesa->addOption('20000073', 'Consignações - Previdência Própria (Câmara)');
$obCmbCodigoDespesa->addOption('20000074', 'Consignações - Previdência Própria (Demais)');
$obCmbCodigoDespesa->addOption('20000076', 'Outras transferências');
$obCmbCodigoDespesa->addOption('20000077', 'Consignações Empréstimos');
$obCmbCodigoDespesa->addOption('20000078', 'Consignações Pensões Alimentícias');
$obCmbCodigoDespesa->addOption('20000079', 'Cauções');
$obCmbCodigoDespesa->setStyle('width:200px;');
$obCmbCodigoDespesa->setValue  ( 'cod_relacionamento_despesa' );
    

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
$obCmbTipo->addOption ( '','Selecione' );
$obCmbTipo->addOption('1',  'ISS');
$obCmbTipo->addOption('2',  'IRRF');
$obCmbTipo->addOption('5',  'Outras Consignações');
$obCmbTipo->addOption('6',  'INSS (Saúde)');
$obCmbTipo->addOption('7',  'INSS (FUNDEB 60%)');
$obCmbTipo->addOption('8',  'INSS (Educação - demais)');
$obCmbTipo->addOption('9',  'INSS (Câmara)');
$obCmbTipo->addOption('10', 'INSS (Demais)');
$obCmbTipo->addOption('11', 'Previdência Própria (Saúde)');
$obCmbTipo->addOption('12', 'Previdência Própria (FUNDEB 60%)');
$obCmbTipo->addOption('13', 'Previdência Própria (Educação - demais)');
$obCmbTipo->addOption('14', 'Previdência Própria (Câmara)');
$obCmbTipo->addOption('15', 'Previdência Própria (Demais)');
$obCmbTipo->setStyle('width:200px;');
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
