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
 * Arquivo paga geração de relatorio de Cadastro de Imoveis
 * Data de Criação: 25/04/2007

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Fernando Piccini Cercato

 * @ignore

 * $Id: OCGeraRelatorioCadastroImobiliario.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_FW_PDF."ListaPDF.class.php";

set_time_limit(300000);

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Cadastro Imobiliário:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arTitulo[0] = array( "titulo" => "DADOS DO IMÓVEL" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32,  0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo"  , 12 );

$rsImoveis = Sessao::read('rsImoveis');
$obPDF->addRecordSet( $rsImoveis );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "INSCRIÇÃO"     ,10,  9 );
$obPDF->addCabecalho   ( "LOCALIZAÇÃO"   ,14, 9 );
$obPDF->addCabecalho   ( "LOTE"          ,10, 9 );
$obPDF->addCabecalho( "ENDEREÇO"    ,25, 9 );
$obPDF->addCabecalho( "CEP"        ,9, 9 );
$obPDF->addCabecalho( "PROPRIETÁRIOS" ,25, 9 );
$obPDF->addCabecalho( "SITUAÇÃO"      ,8,  9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo( "inscricao_municipal", 7 );
$obPDF->addCampo( "localizacao", 7 );
$obPDF->addCampo( "cod_lote", 7 );
$obPDF->addCampo( "[endereco] - [nom_bairro]", 7 );
$obPDF->addCampo( "cep", 7 );
$obPDF->addCampo( "proprietario_cota", 7 );
$obPDF->addCampo( "situacao", 7 );

$rsImoveis = Sessao::read('rsImoveis');

$obPDF->addRecordSet( $rsImoveis );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "FRAÇÃO IDEAL DO LOTE", 17, 9 );
$obPDF->addCabecalho   ( "ÁREA TOTAL EDIFICADA", 16, 9 );
$obPDF->addCabecalho   ( "ÁREA EDIFICADA DO LOTE", 18, 9 );
$obPDF->addCabecalho   ( "DATA DE INSCRIÇÃO", 15, 9 );
$obPDF->addCabecalho   ( "CONDOMÍNIO", 12, 9 );
$obPDF->addCabecalho   ( "CRECI", 8, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo( "fracao_ideal", 7 );
$obPDF->addCampo( "area_edificada", 7 );
$obPDF->addCampo( "area_edificada_lote", 7 );
$obPDF->addCampo( "dt_inscricao", 7 );
$obPDF->addCampo( "condominio", 7 );
$obPDF->addCampo( "creci", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE EDIFICAÇÕES" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsEdificacoes = Sessao::read('rsEdificacoes');
$obPDF->addRecordSet( $rsEdificacoes );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓDIGO", 8, 9 );
$obPDF->addCabecalho   ( "TIPO DE UNIDADE", 15, 9 );
$obPDF->addCabecalho   ( "TIPO DE EDIFICAÇÃO", 18, 9 );
$obPDF->addCabecalho   ( "DATA DE EDIFICAÇÃO", 18, 9 );
$obPDF->addCabecalho   ( "ÁREA DA EDIFICAÇÃO", 18, 9 );
$obPDF->addCabecalho   ( "PROCESSO", 8, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo( "cod_construcao", 7 );
$obPDF->addCampo( "tipo_vinculo", 7 );
$obPDF->addCampo( "nom_tipo", 7 );
$obPDF->addCampo( "data_construcao", 7 );
$obPDF->addCampo( "area", 7 );
$obPDF->addCampo( "cod_processo", 7 );

$rsEdificacoes = Sessao::read('rsEdificacoes');
$obPDF->addRecordSet( $rsEdificacoes );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento( "L" );
$obPDF->addCabecalho   ( "SITUAÇÃO", 8, 9       );
$obPDF->addCabecalho   ( "JUSTIFICATIVA", 12, 9 );
$obPDF->addCabecalho   ( "DATA DA BAIXA", 12, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo( "situacao", 7 );
$obPDF->addCampo( "justificativa", 7 );
$obPDF->addCampo( "data_baixa", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE CONSTRUÇÕES" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsConstrucoes = Sessao::read('rsConstrucoes');
$obPDF->addRecordSet( $rsConstrucoes );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓDIGO", 8, 9 );
$obPDF->addCabecalho   ( "DESCRIÇÃO", 10, 9 );
$obPDF->addCabecalho   ( "DATA DE CONSTRUÇÃO", 16, 9 );
$obPDF->addCabecalho   ( "ÁREA DA CONSTRUÇÃO", 16, 9 );
$obPDF->addCabecalho   ( "SITUAÇÃO", 8, 9 );
$obPDF->addCabecalho   ( "DATA DE BAIXA", 12, 9 );
$obPDF->addCabecalho   ( "JUSTIFICATIVA", 12, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo( "cod_construcao", 7 );
$obPDF->addCampo( "descricao", 7 );
$obPDF->addCampo( "data_construcao", 7 );
$obPDF->addCampo( "area_real", 7 );
$obPDF->addCampo( "situacao", 7 );
$obPDF->addCampo( "data_baixa", 7 );
$obPDF->addCampo( "justificativa", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE PROPRIETÁRIOS" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsProprietarios = Sessao::read('proprietarios');
$obPDF->addRecordSet( $rsProprietarios );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CGM", 25, 9 );
$obPDF->addCabecalho   ( "QUOTA", 8, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "[inNumCGM] - [stNomeCGM]", 7 );
$obPDF->addCampo      ( "flQuota", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE PROMITENTES" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsPromitentes = Sessao::read('promitentes');
$obPDF->addRecordSet( $rsPromitentes );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CGM", 25, 9 );
$obPDF->addCabecalho   ( "QUOTA", 8, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "[inNumCGM] - [stNomeCGM]", 7 );
$obPDF->addCampo      ( "flQuota", 7 );

$arTitulo[0] = array( "titulo" => "DADOS DO LOTE" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$arLotesSessao = Sessao::read('lote');
$obPDF->addRecordSet( $arLotesSessao );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "NÚMERO DO LOTE", 14, 9 );
$obPDF->addCabecalho   ( "LOCALIZAÇÃO", 10, 9 );
$obPDF->addCabecalho   ( "ÁREA", 6, 9 );
$obPDF->addCabecalho   ( "PROFUNDIDADE MÉDIA", 16, 9 );
$obPDF->addCabecalho   ( "DATA DE INSCRIÇÂO", 16, 9 );
$obPDF->addCabecalho   ( "PROCESSO", 10, 9 );
$obPDF->addCabecalho   ( "SITUAÇÃO", 10, 9 );
$obPDF->addCabecalho   ( "DATA DE BAIXA", 12, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "numero_lote", 7 );
$obPDF->addCampo      ( "localizacao_lote", 7 );
$obPDF->addCampo      ( "area_lote", 7 );
$obPDF->addCampo      ( "profundidade_lote", 7 );
$obPDF->addCampo      ( "dt_inscricao_lote", 7 );
$obPDF->addCampo      ( "processo_lote", 7 );
$obPDF->addCampo      ( "situacao_lote", 7 );
$obPDF->addCampo      ( "dt_baixa_lote;", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE CONFRONTAÇÕES" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsConfrontacoes = Sessao::read('confrontacoes');
$obPDF->addRecordSet( $rsConfrontacoes );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "PONTO CARDEAL", 14, 9 );
$obPDF->addCabecalho   ( "TIPO", 8, 9 );
$obPDF->addCabecalho   ( "DESCRIÇÃO", 18, 9 );
$obPDF->addCabecalho   ( "EXTENSÃO", 10, 9 );
$obPDF->addCabecalho   ( "TESTADA", 10, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "stNomePontoCardeal", 7 );
$obPDF->addCampo      ( "stTipoConfrotacao", 7 );
$obPDF->addCampo      ( "stDescricao", 7 );
$obPDF->addCampo      ( "flExtensao", 7 );
$obPDF->addCampo      ( "stTestada", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE TRANSFERÊNCIAS" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsTransferencia = Sessao::read('transferencia');
$obPDF->addRecordSet( $rsTransferencia );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "NATUREZA DE TRANSFERÊNCIA", 23, 9 );
$obPDF->addCabecalho   ( "PROCESSO", 8, 9 );
$obPDF->addCabecalho   ( "DATA DE EFETIVAÇÃO", 14, 9 );
$obPDF->addCabecalho   ( "CRECI", 14, 9 );
$obPDF->addCabecalho   ( "OBSERVAÇÃO", 14, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "natureza", 7 );
$obPDF->addCampo      ( "processo", 7 );
$obPDF->addCampo      ( "dt_efetivacao", 7 );
$obPDF->addCampo      ( "creci", 7 );
$obPDF->addCampo      ( "observacao", 7 );

$arTitulo[0] = array( "titulo" => "LISTA DE EX-PROPRIETÁRIOS" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsExproprietarios = Sessao::read('exproprietarios');
$obPDF->addRecordSet( $rsExproprietarios );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CGM", 25, 9 );
$obPDF->addCabecalho   ( "QUOTA", 8, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "[numcgm] - [nomcgm]", 7 );
$obPDF->addCampo      ( "quota", 7 );

$arTitulo[0] = array( "titulo" => "CONDOMÍNIO" );
$rsListaTitulo = new RecordSet;
$rsListaTitulo->preenche( $arTitulo );

$obPDF->addRecordSet( $rsListaTitulo );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "", 32, 0 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "titulo", 12 );

$rsCondominio = Sessao::read('rsCondominio');
$obPDF->addRecordSet( $rsCondominio );
$obPDF->setQuebraPaginaLista ( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓDIGO", 10, 9 );
$obPDF->addCabecalho   ( "NOME", 18, 9 );
$obPDF->addCabecalho   ( "TIPO", 8, 9 );
$obPDF->addCabecalho   ( "CGM", 18, 9 );
$obPDF->addCabecalho   ( "ÁREA TOTAL COMUM", 25, 9 );

$obPDF->setAlinhamento( "L" );
$obPDF->addCampo      ( "cod_condominio", 7 );
$obPDF->addCampo      ( "nom_condominio", 7 );
$obPDF->addCampo      ( "[cod_tipo]-[nom_tipo]", 7 );
$obPDF->addCampo      ( "[numcgm]-[nom_cgm]", 7 );
$obPDF->addCampo      ( "area_total_comum", 7 );

$arFiltro = Sessao::read('filtro');

$obPDF->addFiltro( 'Inscrição Imobiliária'  , $arFiltro['inInscricaoImobiliaria']                  );
$obPDF->addFiltro( 'Lote'                   , $arFiltro['inCodLote']                               );
$obPDF->addFiltro( 'Localização'            , $arFiltro['stChaveLocalizacao']                      );
$obPDF->addFiltro( 'Logradouro'             , $arFiltro['inNumLogradouro']                         );
if (trim($arFiltro['inNumero']) != "" or trim($arFiltro['stComplemento'])) {
    $obPDF->addFiltro( 'Número / Complemento'   , $arFiltro['inNumero'].' '.$arFiltro['stComplemento'] );
}
$obPDF->addFiltro( 'Condomínio'             , $arFiltro['inCodCondominio']                         );
$obPDF->addFiltro( 'Bairro'                 , $arFiltro['inCodBairro']                             );
$obPDF->addFiltro( 'Proprietário'           , $arFiltro['inNumCGM']                                );
$obPDF->addFiltro( 'CRECI'                  , $arFiltro['stCreci']                                 );

$obPDF->show();

Sessao::remove( 'rsImoveis'       );
Sessao::remove( 'rsEdificacoes'   );
Sessao::remove( 'rsConstrucoes'   );
Sessao::remove( 'proprietarios'   );
Sessao::remove( 'promitentes'     );
Sessao::remove( 'lote'            );
Sessao::remove( 'confrontacoes'   );
Sessao::remove( 'transferencia'   );
Sessao::remove( 'exproprietarios' );
Sessao::remove( 'rsCondominio'    );
Sessao::remove( 'atributos_lote'  );

?>
