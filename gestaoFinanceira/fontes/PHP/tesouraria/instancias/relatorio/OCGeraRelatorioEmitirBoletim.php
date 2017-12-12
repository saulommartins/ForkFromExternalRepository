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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 11/11/2004

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2008-01-14 13:08:51 -0200 (Seg, 14 Jan 2008) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.15  2007/03/14 20:58:38  luciano
#8656#

Revision 1.14  2007/01/12 11:53:54  luciano
Bug #7781#

Revision 1.13  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$rsVazio = new RecordSet();

$arFiltro = Sessao::read('filtroRelatorio');
$arRecordSet = Sessao::read('arDados');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) > 1 ) {
    $obPDF->setAcao            ( "Boletim de Tesouraria ( Consoliado )" );
} else {
    $obPDF->setAcao            ( "Boletim de Tesouraria nr. ".$arFiltro['inCodBoletim'] ."/".Sessao::getExercicio());
}

$obPDF->setSubTitulo         ( "Data do Boletim: ".$arFiltro['stDtBoletim']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                     );
    $obROrcamentoEntidade = new ROrcamentoEntidade();
    $obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

    while (!$rsEntidades->eof()) {
        if ($arFiltro['inCodEntidade'] == $rsEntidades->getCampo('cod_entidade')) {
            $arNomEntidade[] = $rsEntidades->getCampo('nom_cgm');
        }
        $rsEntidades->proximo();
    }

$obPDF->addFiltro( 'Entidade Relacionada'  		        , $arNomEntidade );
$obPDF->addFiltro( 'Tipo Emissão'                 , "Demostrativo Boletim" );
$obPDF->addFiltro( 'Nr. Terminal de Caixa'        , $arFiltro['inCodTerminal'] );
$obPDF->addFiltro( 'Data do Boletim'              , $arFiltro['stDtBoletim'] );

if ($arFiltro['inCodBoletim']) {
    $obPDF->addFiltro( 'Número Boletim'                 , $arFiltro['inCodBoletim'] );
}
if ($arFiltro['inNumCgm']) {
    $obPDF->addFiltro( 'Usuário'                        , $arFiltro['inNumCgm'] ." - ". $arFiltro['stNomCgm'] );
}

$arRecordSet[0]->addFormatacao( 'saldo_anterior', 'CONTABIL'        );
$arRecordSet[0]->addFormatacao( 'saldo_atual'   , 'CONTABIL'        );
$arRecordSet[0]->addFormatacao( 'vl_debito'     , 'NUMERIC_BR' );
$arRecordSet[0]->addFormatacao( 'vl_credito'    , 'NUMERIC_BR' );

// While para verificar se vem dados dos recordsets e formatar os dados
$i = 1;
while ($i <= 5) {
    if ($arRecordSet[$i] instanceof RecordSet) {
        $arRecordSet[$i]->addFormatacao( "valor", "NUMERIC_BR" );
    }
    $i++;
}

$obPDF->addRecordSet( $rsVazio );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Movimento de Caixa / Bancos", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[0] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta"          , 17 );
$obPDF->addCabecalho( "Reduz."         ,  6 );
$obPDF->addCabecalho( "Descrição"      , 29 );
$obPDF->addCabecalho( "Saldo Anterior" , 12 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "Débitos"        , 12 );
$obPDF->addCabecalho( "Créditos"       , 12 );
$obPDF->addCabecalho( "Saldo Atual"    , 12 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo( "cod_estrutural", 8 );
$obPDF->addCampo( "cod_plano"     , 8 );
$obPDF->addCampo( "nom_conta"     , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo( "saldo_anterior", 8 );
$obPDF->addCampo( "vl_debito"     , 8 );
$obPDF->addCampo( "vl_credito"    , 8 );
$obPDF->addCampo( "saldo_atual"   , 8 );

$inTamanhoLinha = 140;

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( str_repeat("_", $inTamanhoLinha), 100, 10 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Depósitos / Transferências", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[1] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta Débito"      , 45, 10 );
$obPDF->addCabecalho( "Conta Crédito"     , 45, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("conta_debito" , 8 );
$obPDF->addCampo("conta_credito", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"        , 8 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( str_repeat("_", $inTamanhoLinha), 100, 10 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Despesa Orçamentária Lançada", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[2] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta Débito"      , 45, 10 );
$obPDF->addCabecalho( "Conta Crédito"     , 45, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("conta_debito" , 8 );
$obPDF->addCampo("conta_credito", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"        , 8 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( str_repeat("_", $inTamanhoLinha), 100, 10 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Despesa Extra-Orçamentária Lançada", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[3] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta Débito"      , 45, 10 );
$obPDF->addCabecalho( "Conta Crédito"     , 45, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("conta_debito" , 8 );
$obPDF->addCampo("conta_credito", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"        , 8 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( str_repeat("_", $inTamanhoLinha), 100, 10 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Receita Orçamentária Lançada", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[4] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta Débito"      , 45, 10 );
$obPDF->addCabecalho( "Conta Crédito"     , 45, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("conta_debito" , 8 );
$obPDF->addCampo("conta_credito", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"        , 8 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( str_repeat("_", $inTamanhoLinha), 100, 10 );

$obPDF->addRecordSet( $rsVazio );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Receita Extra-Orçamentária Lançada", 100, 12 );
$obPDF->addCampo( "", 8 );

$obPDF->addRecordSet( $arRecordSet[5] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho( "Conta Débito"      , 45, 10 );
$obPDF->addCabecalho( "Conta Crédito"     , 45, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("conta_debito" , 8 );
$obPDF->addCampo("conta_credito", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"        , 8 );

$obPDF->addRecordSet( $arRecordSet[6] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->addCabecalho( "",   33, 10  );
$obPDF->addCabecalho( "",   33, 10  );
$obPDF->addCabecalho( "",   33, 10  );
$obPDF->setAlinhamento( "C"         );
$obPDF->addCampo("assinatura1", 10  );
$obPDF->addCampo("assinatura2", 10  );
$obPDF->addCampo("assinatura3", 10  );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
    $arAssinaturas['selecionadas'] = array();
    Sessao::write('assinaturas', $arAssinaturas);
}

$obPDF->show();

?>
