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

    $Revision: 31956 $
    $Name$
    $Autor:$
    $Date: 2008-01-14 13:08:51 -0200 (Seg, 14 Jan 2008) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.12  2007/03/14 20:59:08  luciano
#8656#

Revision 1.11  2007/01/12 11:53:54  luciano
Bug #7781#

Revision 1.10  2006/11/14 12:22:24  cako
Bug #7232#

Revision 1.9  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$arFiltro = Sessao::read('filtroRelatorio');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setAcao( "Demonstrativo de Caixa");
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setSubTitulo         ( "Data do Boletim: ".$arFiltro['stDtBoletim'] );
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
$obPDF->addFiltro( 'Tipo Emissão'                       , "Demonstrativo Caixa" );
$obPDF->addFiltro( 'Nr. Terminal de Caixa'              , $arFiltro['inCodTerminal'] );
$obPDF->addFiltro( 'Data do Boletim'                    , $arFiltro['stDtBoletim'] );

if ($arFiltro['inCodBoletim']) {
    $obPDF->addFiltro( 'Número Boletim'                 , $arFiltro['inCodBoletim'] );
}
if ($arFiltro['inNumCgm']) {
    $obPDF->addFiltro( 'Usuário'                        , $arFiltro['inNumCgm'] ." - ". $arFiltro['stNomCgm'] );
}

$arDados = Sessao::read('arDados');

$obPDF->addRecordSet( $arDados[0] );
$obPDF->addCabecalho( "", 10 );
$obPDF->addCabecalho( "", 90 );

$obPDF->addCampo( "descricao", 12 );
$obPDF->addCampo( "valor"    , 12 );

$obPDF->addRecordSet( $arDados[1] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "Conta"             ,  6, 10 );
$obPDF->addCabecalho( "Descrição da Conta", 30, 10 );
$obPDF->addCabecalho( "Valor"             , 10, 10 );
$obPDF->addCabecalho( "Hora"              , 10, 10 );
$obPDF->addCabecalho( "Procedência"       , 24, 10 );
$obPDF->addCabecalho( "Usuário"           , 20, 10 );

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("cod_conta", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("nom_conta", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("hora", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 8 );
$obPDF->addCampo("nom_cgm", 8 );

$obPDF->addRecordSet( $arDados[2] );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "", 6, 10 );
$obPDF->addCabecalho( "",30, 10 );
$obPDF->addCabecalho( "",10, 10 );
$obPDF->addCabecalho( "",25, 10 );
$obPDF->addCabecalho( "",10, 10 );

$obPDF->addCampo(""         , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("valor"    , 8 );
$obPDF->addCampo("descricao_liquido", 8 );
$obPDF->addCampo("valor_liquido", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    //$obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();

?>
