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
    * Página de geração do recordSet para o Relatório Metas de Execução da Receita
    * Data de Criação   : 28/08/2006

    * @author Analista: Diego Vitoria
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor: $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.04.29
*/

/*
$Log$
Revision 1.10  2006/11/23 20:25:06  cako
Bug #7614#

Revision 1.9  2006/11/08 22:42:12  cleisson
Bug #7306#

Revision 1.8  2006/10/23 13:18:24  larocca
Bug #7252#

Revision 1.7  2006/10/23 12:44:33  larocca
Bug #7221#

Revision 1.6  2006/10/23 11:33:50  larocca
Bug #7216#

Revision 1.5  2006/10/19 12:04:41  bruce
*** empty log message ***

Revision 1.4  2006/10/04 15:14:45  bruce
colocada tag de log

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                );
include_once ( CAM_GA_ADM_MAPEAMENTO. 'TAdministracaoMunicipio.class.php'                       );
include_once ( CAM_GA_ADM_MAPEAMENTO. 'TAdministracaoConfiguracao.class.php'                    );
include_once( CAM_GF_TES_MAPEAMENTO. 'TTesourariaReciboExtra.class.php' );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDF();
$arDados      = Sessao::read('arDados');
$arFiltro     = Sessao::read('filtroRelatorio');

$obMunicipo     = new TMunicipio;
$obConfiguracao = new TAdministracaoConfiguracao;

$rsVazio = new RecordSet();

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setSubTitulo         ( "Recibo de Receita Extra-Orçamentária - Nro. ".$arDados['numeroRecibo'] ."/".$arDados['exercicio']);
$obPDF->setAcao              ( "Emitir Recibo" );

$obReciboExtra = new TTesourariaReciboExtra;
$obReciboExtra->setDado('cod_recibo_extra', $arDados['numeroRecibo'] );
$obReciboExtra->setDado('exercicio', $arDados['exercicio'] );
$obReciboExtra->setDado('cod_entidade',$arDados['codEntidade']);
$obReciboExtra->setDado('tipo_recibo','R');
$obReciboExtra->recuperaPorChave($rsRetorno);

$stData = SistemaLegado::dataToBr( substr($rsRetorno->getCampo('timestamp'),0,10) );
$stDataIngles = substr($rsRetorno->getCampo('timestamp'),0,10);

$obPDF->setData                 ( $stData );
$obPDF->stHora                  = substr($rsRetorno->getCampo('timestamp'),11,8);

//$obPDF->setSubTitulo         ( "Data do Boletim: ".sessao->filtro['stDtBoletim']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->setAlturaCabecalho ( 1 );

$obConfiguracao->setDado('exercicio', Sessao::getExercicio());
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

$stNomeCredor = $arDados['stNomeCredor'];

//// Set de ContaCaixa BAnco e Receita
$rsConta = new RecordSet;
$rsConta->preenche( $arDados['contas'] );
$obPDF->addRecordSet( $rsConta );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( '', 30 );
$obPDF->addCabecalho( '', 20 );
$obPDF->addCabecalho( '', 50 );

$obPDF->addCampo( 'cod_estrutural' , 8,  '', '', 'L' );
$obPDF->addCampo( 'cod_conta'      , 8,  '', '', ''  );
$obPDF->addCampo( 'nom_conta'      , 8,  '', '', 'R' );

////  Linha vazia
$arVazio = array();
$arVazio[]['nome'] = '';
$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho("", 100, 8, "", '' , '');
$obPDF->addCampo    ("nome", 8, '', '', 'LBTR' );

$stBarCode = '';

//// apenas zeros
$stBarCode = str_pad ( '', 8, '0' ).'.';

//// Numero do recibo, com zeros a esquerda
$stBarCode .= str_pad ( $arDados['numeroRecibo'], (6 -  strlen($arDados['numeroRecibo'])), '0', STR_PAD_LEFT) .'.';

//// Exercicio
$stBarCode .= $arDados['exercicio'].'.' ;

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
$obPDF->addCampo( "recurso", 8,  '', '', 'LRB' );

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

$stData = SistemaLegado::dataExtenso($stDataIngles);

/// dados para o recibo
$arVazio = array();

$arVazio[] = array( 'nome'=>'', 'titulo'=>'' );
$arVazio[] = array( 'nome'=>'', 'titulo'=>'' );
$arVazio[] = array( 'nome'=>'', 'titulo'=>'Recebi o valor acima informado.' );
$arVazio[] = array( 'nome'=>'_______________________________________________________', 'titulo'=>'' );

// Inicia a recuperação de assinaturas na Base
// Definição de Parâmetros

if ( $obReciboExtra->getDado('cod_recibo_extra') > 0 ) {

    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaReciboExtraAssinatura.class.php" );
    $obTTesReciboExtraAssinatura = new TTesourariaReciboExtraAssinatura;
    $obTTesReciboExtraAssinatura->setDado("cod_recibo_extra", $obReciboExtra->getDado('cod_recibo_extra') );
    $obTTesReciboExtraAssinatura->setDado("tipo_recibo", $obReciboExtra->getDado('tipo_recibo') );
    $obTTesReciboExtraAssinatura->setDado("exercicio", $obReciboExtra->getDado('exercicio') );
    $obTTesReciboExtraAssinatura->setDado("cod_entidade", $arFiltro['inCodEntidade'] );

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
    }

    // Atualizar a Sessão com as assinaturas recuperadas

    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->definePapeisDisponiveis('recibo_despesa_extra');
    // Método específico
    $obRAssinaturas->montaReciboReceitaExtra($arAssinaturaSelecionada, $arVazio);
}

$arVazio[] = array( 'nome'=>"$stMunicipio, $stData", 'titulo'=>'' );

$rsVazio = new RecordSet;
$rsVazio->preenche( $arVazio );
$rsVazio->setPrimeiroElemento();
$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 40 , 8, "", '' , '');
$obPDF->addCabecalho("", 60 , 8, "", '' , '');

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("titulo", 8, '', '', 'L' );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("nome",   8, '', '', 'R' );

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
$stBarCode .= str_pad ( $arDados['numeroRecibo'], (6 -  strlen($arDados['numeroRecibo'])), '0', STR_PAD_LEFT);

//// Exercicio
$stBarCode .= $arDados['exercicio'];

//// Codigo da entidade + '0'
$stBarCode .= str_pad( $arDados['codEntidade'], (3 - strlen( $arDados['codEntidade'])), '0', STR_PAD_LEFT) . '0';

$obPDF->setCodigoBarras( $stBarCode );
$obPDF->show();

?>
