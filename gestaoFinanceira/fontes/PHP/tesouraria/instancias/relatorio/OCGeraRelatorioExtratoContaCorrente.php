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

    * Página de Formulario de Seleção de Impressora para Relatorio C/c
    * Data de Criação   : 21/07/2014
    * 
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    *
    * @ignore
    *
    $id:$
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_GF_TES_MAPEAMENTO."FTesourariaExtratoContaCorrente.class.php");
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$arDados  = Sessao::read('arDados');
$arFiltro = Sessao::read('filtroRelatorio');
$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF(  );

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}
$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Dados para Extrato de Conta Corrente" );
$obPDF->setSubTitulo         ( $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

/* Monta as entidades selecionadas*/
$obROrcamentoEntidade = new ROrcamentoEntidade();
$obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades , ' ORDER BY cod_entidade');

while (!$rsEntidades->eof()) {
    foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $inCodEntidade) {
        if ($inCodEntidade == $rsEntidades->getCampo('cod_entidade')) {
            $arNomEntidade[] = $rsEntidades->getCampo('nom_cgm');
        }
    }
    $rsEntidades->proximo();
}

/* Monta os valores de de filtros */
$rsDadosBancarios = new RecordSet();
$obFTesourariaExtratoContaCorrente = new FTesourariaExtratoContaCorrente();

$obFTesourariaExtratoContaCorrente->setDado("inCodPlanoInicial"   ,$arFiltro["inCodContaBancoInicial"] );
$obFTesourariaExtratoContaCorrente->setDado("inCodPlanoFinal"     ,$arFiltro["inCodContaBancoFinal"] );

$obFTesourariaExtratoContaCorrente->setDado("stExercicio"          ,$arFiltro["stExercicio"]);
$obFTesourariaExtratoContaCorrente->setDado("stEntidade"           ,implode(",",$arFiltro['inCodigoEntidadesSelecionadas']));
$obFTesourariaExtratoContaCorrente->setDado("stDataInicial"        ,$arFiltro["stDataInicial"]);
$obFTesourariaExtratoContaCorrente->setDado("stDataFinal"          ,$arFiltro["stDataFinal"]);
$obFTesourariaExtratoContaCorrente->setDado("inCodBanco"           ,$arFiltro["inCodBanco"]);
$obFTesourariaExtratoContaCorrente->setDado("inCodAgencia"         ,$arFiltro["inCodAgencia"]);
$obFTesourariaExtratoContaCorrente->setDado("stContaCorrente"      ,$arFiltro["stContaCorrente"]);
$obFTesourariaExtratoContaCorrente->setDado("inCodRecurso"         ,$arFiltro["inCodRecurso"]);
$obErro = $obFTesourariaExtratoContaCorrente->recuperaDadosContaCorrente( $rsDadosBancarios, $stFiltroExtratoBancario, $stOrder );

$arContasPlanos = array();

if ( $rsDadosBancarios->getNumLinhas() > 0 ) {
    foreach( $rsDadosBancarios->getElementos() as $arDadosBancarios){
        $arContasPlanos[] = $arDadosBancarios["cod_plano"] ." - ".$arDadosBancarios["nom_conta"];
    }
}

if( $arFiltro["inCodContaBancoFinal"] != "" ) {
    $stContaBanco = " até ".$arFiltro["inCodContaBancoFinal"];
}
$obPDF->addFiltro( 'Entidades Relacionadas ' , $arNomEntidade );
$obPDF->addFiltro( 'Exercício '              , $arFiltro["stExercicio"] );
$obPDF->addFiltro( 'Periodicidade '          , $arFiltro["stDataInicial"] . " até " . $arFiltro["stDataFinal"] );
$obPDF->addFiltro( 'Banco '                  , $arFiltro["inNumBanco"] );
$obPDF->addFiltro( 'Angência '               , $arFiltro["inNumAgencia"] );
$obPDF->addFiltro( 'Conta Corrente '         , $arFiltro["stContaCorrente"] );
$obPDF->addFiltro( 'Recurso '                , $arFiltro["inCodRecurso"].$arFiltro["stDescricaoRecurso"] );

$obPDF->addFiltro( 'Conta Banco '            , $arContasPlanos );

$i = 0;

foreach ($arDados[0] as $arContas) {
    $arNomeConta = array();
    $arNomeConta[0]['dados_banco'] = $arContas['dados_banco'];
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arNomeConta );

    $obPDF->addRecordSet( $rsRecordSet );
    if($i > 0) $obPDF->setQuebraPaginaLista( $arFiltro['stQuebraPagPorConta'] == 'sim'  );
    $i++;
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho("", 100, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo("dados_banco", 8 );

    $rsRecordSetMov = new RecordSet;
    if ( !is_array( $arContas['movimentacao'] ) ) {
        $arContas['movimentacao'] = array();
    }
    $rsRecordSetMov->preenche( $arContas['movimentacao'] );

    $obPDF->addRecordSet($rsRecordSetMov);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Data Movim.', 15, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Histórico', 50, 10);
    $obPDF->setAlinhamento ('R');
    $obPDF->addCabecalho   ("Valor R\$",20, 10);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCabecalho   ('Saldo', 20, 10);
    $obPDF->addQuebraPagina('pagina', 1);

    $obPDF->setAlinhamento ('C');
    $obPDF->addCampo       ('data', 8);
    $obPDF->setAlinhamento ('L');
    $obPDF->addCampo       ('descricao', 8);
    $obPDF->setAlinhamento ('R');
    $obPDF->addCampo       ('valor', 8);
    $obPDF->setAlinhamento ('C');
    $obPDF->addCampo       ('saldo', 8);
}

$obPDF->addRecordSet( $arDados[1] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "", 6, 10 );
$obPDF->addCabecalho( "",35, 10 );
$obPDF->addCabecalho( "",10, 10 );
$obPDF->addCabecalho( "",30, 10 );
$obPDF->addCabecalho( "",10, 10 );

$obPDF->addCampo(""         , 7 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 7 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"    , 7 );
$obPDF->addCampo("descricao_liquido", 7 );
$obPDF->addCampo("valor_liquido", 7 );

$i = 0;

$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();

?>
