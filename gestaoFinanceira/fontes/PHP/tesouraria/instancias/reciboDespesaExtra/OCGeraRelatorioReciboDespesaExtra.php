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
    * Página de geração do recordSet para o Relatório Metas de Execução da Despesa
    * Data de Criação   : 28/08/2006

    * @author Analista: Diego Vitoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: OCGeraRelatorioReciboDespesaExtra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.30
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoMunicipio.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';

$obRRelatorio   = new RRelatorio;
$obPDF          = new ListaFormPDF();
$obMunicipo     = new TMunicipio;
$obConfiguracao = new TAdministracaoConfiguracao;
$rsVazio        = new RecordSet();

$arDados = Sessao::read('filtroRelatorio');
$numeroRecibo = Sessao::read('numeroRecibo');

if($numeroRecibo == "")
    $numeroRecibo = $arDados['numeroRecibo'];

// Adicionar logo nos relatorios
if ( count( Sessao::read('inCodEntidade') ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( Sessao::read('inCodEntidade')  );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obPDF->setAcao( "Emitir Recibo" );
$obPDF->setSubTitulo( 'Recibo de Despesa Extra-Orçamentaria - Nro ' . $numeroRecibo . '/'. Sessao::getExercicio()  );

$obReciboExtra = new TTesourariaReciboExtra;
$obReciboExtra->setDado('cod_recibo_extra', $numeroRecibo);
$obReciboExtra->setDado('exercicio', Sessao::getExercicio()  );
$obReciboExtra->setDado('cod_entidade',$arDados['codEntidade']);
$obReciboExtra->setDado('tipo_recibo','D');
$obReciboExtra->recuperaPorChave($rsRetorno);

$stData = SistemaLegado::dataToBr( substr($rsRetorno->getCampo('timestamp'),0,10) );
$stDataIngles = substr($rsRetorno->getCampo('timestamp'),0,10);

$obPDF->setData                 ( $stData );
$obPDF->stHora                  = substr($rsRetorno->getCampo('timestamp'),11,8);

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->setAlturaCabecalho ( 1 );

$obConfiguracao->pegaConfiguracao ( $inUF ,           'cod_uf'        );
$obConfiguracao->pegaConfiguracao ( $inCodMunicipio , 'cod_municipio' );

//// pegando o municipio
$obMunicipo->setDado( 'cod_municipio', $inCodMunicipio );
$obMunicipo->setDado( 'cod_uf',        $inUF           );
$obMunicipo->consultar();
$stMunicipio = $obMunicipo->getDado ( 'nom_municipio' );

//// Entidade
$rsEntidade =new RecordSet;
$rsEntidade->preenche( $arDados['entidade'] );
$obPDF->addRecordSet( $rsEntidade );
$obPDF->addCabecalho( '', 100 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo( "entidade", 8,  'B', '', 'LTRB' );

//// Data Emissão e Valor
$rsDataValor = new RecordSet;
$rsDataValor->preenche( $arDados['dataValor']);
$obPDF->addRecordSet( $rsDataValor );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 100, 8,"B");
$obPDF->setAlinhamento ( 'L' );
$obPDF->addCampo( "valor", 8,  '', '', 'TLR' );

//// Credor
$rsCredor =new RecordSet;
$rsCredor->preenche( $arDados['credor'] );
$obPDF->addRecordSet( $rsCredor );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 100 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo( "credor", 8,  '', '', 'LR' );

//// Banco Recibo Credor
$rsContaBancaria = new RecordSet;
$rsContaBancaria->preenche( $arDados['conta_bancaria'] );
$obPDF->addRecordSet( $rsContaBancaria );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 100 );
$obPDF->addCampo( "conta_bancaria", 8,  '', '', 'LR' );

//// Set de ContaCaixa Banco e Despesa
$rsConta = new RecordSet;
$rsConta->preenche( $arDados['contas'] );
$obPDF->addRecordSet( $rsConta );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 100 );
$obPDF->addCampo( 'cod_estrutural' , 8,  '', '', 'LR' );

////  LInha vazia
$arVazio = array();
$arVazio[]['nome'] = '';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->addCampo    ("nome", 8, '', '', 'T' );

////  LInha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'LRT' );

$stBarCode = '';

//// apenas zeros
$stBarCode = str_pad ( '', 8, '0' ).'.';

//// Numero do recibo, com zeros a esquerda
$stBarCode .= str_pad ( $numeroRecibo, (6 -  strlen($numeroRecibo)), '0', STR_PAD_LEFT) .'.';

//// Exercicio
$stBarCode .= Sessao::getExercicio().'.' ;

//// Codigo da entidade + '0'
$stBarCode .= str_pad( $arDados['codEntidade'], (3 - strlen( $arDados['codEntidade'])), '0', STR_PAD_LEFT) . '0';

$arDados['historico'][]['historico'] = 'Codigo barras.: ' . $stBarCode;

//// Historico
$rsHistorico = new RecordSet;
$rsHistorico->preenche( $arDados['historico']);
$obPDF->addRecordSet( $rsHistorico );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 10);
$obPDF->addCabecalho( '', 90);
$obPDF->setAlinhamento ( 'L' );
$obPDF->addCampo( "titulo", 8,  '', '', 'L' );
$obPDF->addCampo( "historico", 8,  '', '', 'R' );

//// Recurso
$rsRecurso =new RecordSet;
$rsRecurso->preenche( $arDados['recurso'] );
$obPDF->addRecordSet( $rsRecurso );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 100 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo( "recurso", 8,  '', '', 'LR' );

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'LR' );

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'T' );

// ASSINATURAS

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = '';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho("", 25, 8, "", '' , '');
$obPDF->addCabecalho("", 25, 8, "", '' , '');
$obPDF->addCabecalho("", 50, 8, "", '' , '');
$obPDF->addCampo    ("nome", 8, '', '', 'RLT' );
$obPDF->addCampo    ("nome", 8, '', '', 'RLT' );
$obPDF->addCampo    ("nome", 8, '', '', 'RLT' );

// Inicia a recuperação de assinaturas na Base
// Definição de Parâmetros

if ( $obReciboExtra->getDado('cod_recibo_extra') > 0 ) {

    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaReciboExtraAssinatura.class.php" );
    $obTTesReciboExtraAssinatura = new TTesourariaReciboExtraAssinatura;
    $obTTesReciboExtraAssinatura->setDado("cod_recibo_extra", $obReciboExtra->getDado('cod_recibo_extra') );
    $obTTesReciboExtraAssinatura->setDado("tipo_recibo", $obReciboExtra->getDado('tipo_recibo') );
    $obTTesReciboExtraAssinatura->setDado("exercicio", $obReciboExtra->getDado('exercicio') );
    $obTTesReciboExtraAssinatura->setDado("cod_entidade", $arDados['codEntidade'] );

    // Novo RecordSet com resultado da consulta
    $rsAssinatura = new RecordSet;
    $obTTesReciboExtraAssinatura->recuperaAssinaturasReciboExtra( $rsAssinatura, "", " ORDER BY num_assinatura ", "" );

    $arAssinaturaSelecionada = array();
    $arSelecionada = array();
    // Popular a sessão com assinaturas selecionadas

    while ($rsAssinatura->each()) {
        $arAssinatura = $rsAssinatura->getObjeto();
        $arAssinaturaSelecionada[] = array ('inId'=>'',
                                            'inCodEntidade'=>$arAssinatura['cod_entidade'],
                                            'inCGM'=>$arAssinatura['numcgm'],
                                            'stNomCGM'=>$arAssinatura['nom_cgm'],
                                            'stCargo'=>$arAssinatura['cargo'],
                                            'stCRC'=>'',
                                            'inPosAssinatura'=>$arAssinatura['num_assinatura']
                                            );
        //$arSelecionada[$arAssinatura['num_assinatura']] = array( 'stNomCGM'=>$arAssinatura['nom_cgm'], 'stCargo'=>$arAssinatura['cargo'] );
    }

    // Atualizar a Sessão com as assinaturas recuperadas

    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->definePapeisDisponiveis('recibo_despesa_extra');
    // Método específico
    $obRAssinaturas->montaReciboDespesaExtra($arAssinaturaSelecionada, $obPDF);

}

///  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 25, 8, "", '' , '');
$obPDF->addCabecalho("", 25, 8, "", '' , '');
$obPDF->addCabecalho("", 50, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'LR' );
$obPDF->addCampo       ("nome", 8, '', '', 'LR' );
$obPDF->addCampo       ("nome", 8, '', '', 'LR' );

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'T' );

// FIM ASSINATURAS

////  Titulo do Ultimo Quadro
$arVazio = array();
$arVazio[]['nome'] = 'RECIBO';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, 'B', '', 'TLR' );

/// dados para o recibo
$arVazio1 = array();
$arVazio1[0]['nome'] = '                                                          Recebi(emos) deste município a importância abaixo especificada, referente a:';

$arVazio = array();
$arVazio[0]['nome'] = '';
$arVazio[1]['nome']   = '     (  ) Parte do valor devido';
$arVazio[2]['nome'] = '';
$arVazio[3]['nome']   = 'R$ _________________________ em ____/____/____';
$arVazio[4]['nome'] = '';
$arVazio[5]['nome'] = trim($stNomeCredor);

$arVazio[0]['titulo'] = '';
$arVazio[1]['titulo'] = '            (  ) Saldo/Total do valor devido';
$arVazio[2]['titulo'] = '';
$arVazio[3]['titulo'] = 'R$ _________________________ em ____/____/____';
$arVazio[4]['titulo'] = '';
$arVazio[5]['titulo'] = trim($stNomeCredor);

$rsVazio1 = new RecordSet;
$rsVazio1->preenche( $arVazio1 );
$rsVazio1->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio1);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("", 100, 8, '', '', '');
$obPDF->addCampo       ('nome', 8, '', '', 'RL' );

$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 50 , 8, "", '' , '' );
$obPDF->addCabecalho("", 50 , 8, "",  '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'L' );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("titulo",   8, '', '', 'R' );

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = ' ';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome", 8, '', '', 'LRB' );

//// Geração do codigo de barras

$stBarCode = '';

//// apenas zeros
$stBarCode = str_pad ( '', 8, '0' );

//// Numero do recibo, com zeros a esquerda
$stBarCode .= str_pad ( $numeroRecibo, (6 -  strlen($numeroRecibo)), '0', STR_PAD_LEFT);

//// Exercicio
$stBarCode .= Sessao::getExercicio();

//// Codigo da entidade + '0'
$stBarCode .= str_pad( $arFiltroRelatorio['codEntidade'], (3 - strlen( $arFiltroRelatorio['codEntidade'])), '0', STR_PAD_LEFT) . '0';

$obPDF->setCodigoBarras( $stBarCode );

$obPDF->show();

?>
