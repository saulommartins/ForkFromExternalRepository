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
//set_time_limit(120);
/**
    * Página de oculta de geração de relatório
    * Data de Criação   : 04/07/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.25
*/

/*
$Log$
Revision 1.9  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"       );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"       );

$obRRelatorio  = new RRelatorio;
$obPDF         = new ListaPDF("L");
$rsVazio       = new RecordSet;
$rsEntidades   = new RecordSet;
$inCount       = 0;

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$arFiltroNomRelatorio = Sessao::read('filtroNomRelatorio');
// Adicionar logo nos relatorios
if ( count( $arFiltroRelatorio['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltroRelatorio['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo               ( "Orcamento" );
$obPDF->setTitulo               ( "Relatório de Suplementações" );

if ($arFiltroRelatorio['stTipoRelatorio']) {
    switch ($arFiltroRelatorio['stTipoRelatorio']) {
        CASE 'entidade':    $tipoRelatorio = 'Entidade';
            break;
        CASE 'lei_decreto': $tipoRelatorio = 'Lei/Decreto';
            break;
        CASE 'data' :       $tipoRelatorio = 'Data';
            break;
        CASE 'dotacao' :    $tipoRelatorio = 'Dotação';
            break;
        CASE 'resumo' :     $tipoRelatorio = 'Resumo das Suplementações';
            break;
        CASE 'anuladas' :   $tipoRelatorio = 'Suplementações Anuladas';
            break;
    }
}
if ($arFiltroRelatorio['stDataInicial']) {
    $stPeriodo = $arFiltroRelatorio['stDataInicial']." até ".$arFiltroRelatorio['stDataFinal']." - ". $tipoRelatorio;
}
$obPDF->setSubTitulo            ( "Periodicidade: ".$stPeriodo );

$obPDF->setUsuario              ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura   ( $arConfiguracao );

$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio(Sessao::getExercicio());

foreach ($arFiltroRelatorio['inCodEntidade'] as $inCodEntidade) {
    include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
    $obTEntidade = new TOrcamentoEntidade;
    $obTEntidade->setDado('exercicio', Sessao::getExercicio());
    $obTEntidade->recuperaEntidades( $rsLista, " AND e.cod_entidade = ".$inCodEntidade );
    
    $arNomEntidade[] = $rsLista->arElementos[0]['nom_cgm'];
}

$obPDF->addFiltro( 'Entidades Relacionadas: '    , $arNomEntidade  );

if ($arFiltroRelatorio['stNomTipoNorma']) {
    $obPDF->addFiltro( 'Lei/Decreto : '    ,  $arFiltroRelatorio['stNomTipoNorma'] );
}
if ($arFiltroRelatorio['stNomDespesa']) {
    $obPDF->addFiltro( 'Dotação : '    ,  $arFiltroRelatorio['stNomDespesa'] );
}
if ($arFiltroRelatorio['inCodTipoSuplementacao']) {
   $obPDF->addFiltro('Tipo de Suplementação: ' , $arFiltroNomRelatorio['tipo'][$arFiltroRelatorio['inCodTipoSuplementacao']]);
}

if ($arFiltroRelatorio['stTipoRelatorio']) {
    $obPDF->addFiltro( 'Tipo de Relatório: '    ,  $tipoRelatorio );
}
if ($arFiltroRelatorio['stSituacao']) {
    $obPDF->addFiltro( 'Situação : '    ,  $arFiltroRelatorio['stSituacao'] );
}
if ($arFiltroRelatorio['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: '    ,  $arFiltroRelatorio['stDataInicial']." até ".$arFiltroRelatorio['stDataFinal'] );
}

//echo"lalala<pre>";
//print_r(//sessao->transf);
//echo"</pre>";
//exit;
$arRecordSet = Sessao::read('arRecordSet');
$arRecordSet1 = Sessao::read('arRecordSet1');
$arRecordSet2 = Sessao::read('arRecordSet2');
$arRecordSet3 = Sessao::read('arRecordSet3');

switch ($arFiltroRelatorio['stTipoRelatorio']) {
  CASE 'entidade':
    $inCount=0;

    if (is_array( $arRecordSet)) {
        foreach ($arRecordSet as $recordSet) {
            //Entidade Atual

            $obPDF->addRecordSet( $arRecordSet1[$inCount] );
            $obPDF->setQuebraPaginaLista( true );
            $obPDF->setAlturaCabecalho(5);
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCabecalho("", 15, 8);
            $obPDF->addCabecalho("", 25, 8);
            $obPDF->addCabecalho("", 30, 8);
            $obPDF->addCabecalho("", 15, 8);
            $obPDF->addCabecalho("", 15, 8);

            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna1", 8,"B" );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("coluna3", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna4", 8 );
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna5", 8 );

            $obPDF->addRecordSet( $recordSet );
            $obPDF->setQuebraPaginaLista( false);
            $obPDF->setAlturaCabecalho(5);
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCabecalho("Data"                 , 15, 8,"B");
            // $obPDF->addCabecalho("Lei/Decreto"           , 20, 8,"B");
            $obPDF->addCabecalho("Fundamentação"        , 23, 8,"B");
            $obPDF->addCabecalho(""                     , 2, 8);
            $obPDF->addCabecalho("Fonte"                , 8, 8, "B");
            $obPDF->addCabecalho("Tipo Suplementação"   , 27, 8,"B");
            $obPDF->addCabecalho("Valor"                , 15, 8,"B");
            $obPDF->addCabecalho("Situação"             , 15, 8,"B");

            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna1", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("", 8 );
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna3", 8 );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo("coluna4", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna5", 8 );
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna6", 8 );

            $inCount++;
        }
    }
  break;
  CASE 'lei_decreto':
  $inCount=0;
    if ( is_array( $arRecordSet ) ) {
        foreach ($arRecordSet as $recordSet) {
            $obPDF->addRecordSet( $recordSet );
            if ($inCount==0) {
                $obPDF->setQuebraPaginaLista( true );
            } else {
                $obPDF->setQuebraPaginaLista( false );
            }
            $obPDF->setAlturaCabecalho(5);
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCabecalho("" , 4, 8);
            $obPDF->addCabecalho("" , 11,8);
            $obPDF->addCabecalho("" , 9, 8);
            $obPDF->addCabecalho("" , 25, 8);
            $obPDF->addCabecalho("" , 12, 8);
            $obPDF->addCabecalho("" , 26, 8);
            $obPDF->addCabecalho("" , 6, 8);
            $obPDF->addCabecalho("" , 14, 8);

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo("coluna1", 8,"B" );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("coluna3", 8,"B" );
            $obPDF->addCampo("coluna4", 8 );
            $obPDF->addCampo("coluna5", 8,"B" );
            $obPDF->addCampo("coluna6", 8 );
            $obPDF->addCampo("coluna7", 8,"B" );
            $obPDF->addCampo("coluna8", 8 );

            // Dotações Reduzidas

           $obPDF->addRecordSet( $arRecordSet1[$inCount] );
           $obPDF->setQuebraPaginaLista( false );
           $obPDF->setAlturaCabecalho(5);
           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCabecalho(""                  , 12, 8);
           $obPDF->addCabecalho("Dotações Reduzidas", 30,8,"B");
           $obPDF->addCabecalho("Fonte"             , 13,8,"B");
           $obPDF->setAlinhamento ( "L" );
           $obPDF->addCabecalho("Descrição"         , 35, 8,"B");
           $obPDF->setAlinhamento ( "R" );
           $obPDF->addCabecalho("Valor"             , 10, 8,"B");

           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCampo("coluna1", 8 );
           $obPDF->addCampo("coluna2", 8 );
           $obPDF->addCampo("coluna3", 8 );
           $obPDF->setAlinhamento ( "L" );
           $obPDF->addCampo("coluna4", 8 );
           $obPDF->setAlinhamento ( "R" );
           $obPDF->addCampo("coluna5", 8 );

           //Dotações Suplementadas
           $obPDF->addRecordSet( $arRecordSet2[$inCount] );
           $obPDF->setQuebraPaginaLista( false);
           $obPDF->setAlturaCabecalho(5);
           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCabecalho(""                      , 12, 8);
           $obPDF->addCabecalho("Dotações Suplementadas", 30,8,"B");
           $obPDF->addCabecalho("Fonte"                 , 13,8,"B");
           $obPDF->setAlinhamento ( "L" );
           $obPDF->addCabecalho("Descrição"             , 35, 8,"B");
           $obPDF->setAlinhamento ( "R" );
           $obPDF->addCabecalho("Valor"                 , 10, 8,"B");

           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCampo("coluna1", 8 );
           $obPDF->addCampo("coluna2", 8 );
           $obPDF->addCampo("coluna3", 8 );
           $obPDF->setAlinhamento ( "L" );
           $obPDF->addCampo("coluna4", 8 );
           $obPDF->setAlinhamento ( "R" );
           $obPDF->addCampo("coluna5", 8 );

           //Total do Decreto
           $obPDF->addRecordSet( $arRecordSet3[$inCount] );
           $obPDF->setQuebraPaginaLista( false);
           $obPDF->setAlturaCabecalho(5);
           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCabecalho("", 20, 8);
           $obPDF->addCabecalho("", 54, 8);
           $obPDF->addCabecalho("", 20, 8);
           $obPDF->addCabecalho("", 6, 8);
           $obPDF->addCabecalho("", 6, 8);

           $obPDF->setAlinhamento ( "C" );
           $obPDF->addCampo("coluna1", 8 );
           $obPDF->addCampo("coluna2", 8 );
           $obPDF->setAlinhamento ( "L" );
           $obPDF->addCampo("coluna3", 8, "B" );
           $obPDF->setAlinhamento ( "R" );
           $obPDF->addCampo("coluna4", 8 );
           $obPDF->addCampo("coluna5", 8 );
           $inCount++;
        }
    }
  break;
  CASE 'data' :
        $obPDF->addRecordSet( $arRecordSet );

        $obPDF->setQuebraPaginaLista( true);
        $obPDF->setAlturaCabecalho(10);
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("Data"                 , 8 , 8,"B");
        // $obPDF->addCabecalho("Lei/Decreto"          , 15, 8,"B");
        $obPDF->addCabecalho("Fundamentação"        , 20, 8,"B");
        $obPDF->addCabecalho("Fonte"                , 8 , 8,"B");
        $obPDF->addCabecalho("Tipo Suplementação"   , 20, 8,"B");
        $obPDF->addCabecalho("Dotação"              , 30, 8,"B");
        $obPDF->addCabecalho("Valor"                , 12, 8,"B");

        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo("coluna1", 8 );
        $obPDF->addCampo("coluna2", 8 );
        $obPDF->addCampo("coluna3", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("coluna4", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna5", 8 );
        $obPDF->addCampo("coluna6", 8 );

        //adicionado cabeçalho para totais do relatório
        $obPDF->addRecordSet( $arRecordSet1 );
        $obPDF->setQuebraPaginaLista( false );
        $obPDF->setAlturaCabecalho(5);
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho(""                 , 20, 8,"B");
        $obPDF->addCabecalho(""                 , 13, 8,"B");
        $obPDF->addCabecalho(""                 , 10, 8,"B");
        $obPDF->addCabecalho(""                 , 23, 8,"B");
        $obPDF->addCabecalho(""                 , 12, 8,"B");
        $obPDF->addCabecalho(""                 , 20, 8,"B");

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("coluna1", 8 );
        $obPDF->addCampo("coluna2", 8 );
        $obPDF->addCampo("coluna3", 8 );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo("coluna4", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna5", 8 );
        $obPDF->addCampo("coluna6", 8 );

  break;
  CASE 'dotacao':
    $inCount=0;
    if ( is_array( $arRecordSet ) ) {
        foreach ($arRecordSet as $recordSet) {

            //Dotação Atual
            $obPDF->addRecordSet( $arRecordSet1[$inCount] );
            if ($inCount==0) {
                $obPDF->setQuebraPaginaLista( true );
            } else {
                $obPDF->setQuebraPaginaLista( false );
            }
            $obPDF->setAlturaCabecalho(10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho("", 8, 8);
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCabecalho("", 32,8);
            $obPDF->addCabecalho("", 45, 8);
            $obPDF->addCabecalho("", 15, 8);

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo("coluna1", 8,"B" );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("coluna3", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna4", 8 );

            $obPDF->addRecordSet( $recordSet );
            $obPDF->setQuebraPaginaLista( false);
            $obPDF->setAlturaCabecalho(10);
            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCabecalho("Data"                 , 10, 8,"B");
            // $obPDF->addCabecalho("Lei/Decreto"          , 15, 8,"B");
            $obPDF->addCabecalho("Fundamentação"        , 23, 8,"B");
            $obPDF->addCabecalho("Fonte"                , 7, 8,"B");
            $obPDF->addCabecalho("Tipo Suplementação"   , 30, 8,"B");
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("Valor Reduzido"       , 15, 8,"B");
            $obPDF->addCabecalho("Valor Suplementado"   , 15, 8,"B");

            $obPDF->setAlinhamento ( "C" );
            $obPDF->addCampo("coluna1", 8 );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("coluna3", 8 );
            $obPDF->addCampo("coluna4", 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna5", 8 );
            $obPDF->addCampo("coluna6", 8 );

            //Total da dotação atual
            $obPDF->addRecordSet( $arRecordSet2[$inCount] );
            $obPDF->setQuebraPaginaLista( false );
            $obPDF->setAlturaCabecalho(5);
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCabecalho("", 70, 8);
            $obPDF->addCabecalho("", 15, 8);
            $obPDF->addCabecalho("", 15, 8);

            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo("coluna1", 8,"B" );
            $obPDF->addCampo("coluna2", 8 );
            $obPDF->addCampo("coluna3", 8 );

            $inCount++;
        }
    }
  break;
  CASE 'resumo' :
        $obPDF->addRecordSet( $arRecordSet);

        $obPDF->setQuebraPaginaLista( true);
        $obPDF->setAlturaCabecalho(10);
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("Tipo de Suplementação", 70, 8,"B");
        $obPDF->addCabecalho("Valor"                , 15, 8,"B");

        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo("coluna1", 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna2", 8 );

        $obPDF->addRecordSet( $arRecordSet1);

        $obPDF->setQuebraPaginaLista( false);
        $obPDF->setAlturaCabecalho(-1);
        $obPDF->addCabecalho("", 75, 8);
        $obPDF->addCabecalho("", 10, 8);

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna1", 8,"B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna2", 8 );

        $obPDF->addRecordSet( $arRecordSet2);

        $obPDF->setQuebraPaginaLista( false);
        $obPDF->setAlturaCabecalho(5);
        $obPDF->setAlinhamento( "C" );
        $obPDF->addCabecalho("", 75, 8);
        $obPDF->addCabecalho("", 10, 8);

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna1", 8,"B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna2", 8 );

        $obPDF->addRecordSet( $arRecordSet3);

        $obPDF->setQuebraPaginaLista( false);
        $obPDF->setAlturaCabecalho(5);
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("", 75, 8);
        $obPDF->addCabecalho("", 10, 8);

        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna1", 8,"B" );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo("coluna2", 8 );

  break;
  CASE 'anuladas':
  $inCount=0;
    if (is_array( $arRecordSet) ) {
    foreach ($arRecordSet as $recordSet) {
        $obPDF->addRecordSet( $recordSet );
        $obPDF->setQuebraPaginaLista( true );
        $obPDF->setAlturaCabecalho(10);
        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCabecalho("Data Suplementação"   , 15, 8 ,"B");
        $obPDF->addCabecalho("Data Anulação"        , 10, 8 ,"B");
        // $obPDF->addCabecalho("Lei/Decreto"          , 10, 8 ,"B");
        $obPDF->addCabecalho("Fundamentação"        , 23, 8 ,"B");
        $obPDF->addCabecalho("Tipo Suplementação"   , 20, 8 ,"B");
        $obPDF->addCabecalho("Motivo"               , 33, 8 ,"B");

        $obPDF->setAlinhamento ( "C" );
        $obPDF->addCampo("coluna1", 8 );
        $obPDF->addCampo("coluna2", 8 );
        $obPDF->addCampo("coluna3", 8 );
        $obPDF->addCampo("coluna4", 8 );
        $obPDF->addCampo("coluna5", 8 );

       // Dotações Reduzidas

       $obPDF->addRecordSet( $arRecordSet1[$inCount] );
       $obPDF->setQuebraPaginaLista( false );
       $obPDF->setAlturaCabecalho(10);
       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCabecalho(""                  , 17, 8,"B");
       $obPDF->addCabecalho("Dotações Reduzidas", 25, 8,"B");
       $obPDF->addCabecalho("Fonte"             , 10, 8,"B");
       $obPDF->setAlinhamento ( "L" );
       $obPDF->addCabecalho("Descrição"         , 38, 8,"B");
       $obPDF->setAlinhamento ( "R" );
       $obPDF->addCabecalho("Valor"             , 10, 8,"B");

       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCampo("coluna1", 8 );
       $obPDF->addCampo("coluna2", 8 );
       $obPDF->addCampo("coluna3", 8 );
       $obPDF->setAlinhamento ( "L" );
       $obPDF->addCampo("coluna4", 8 );
       $obPDF->setAlinhamento ( "R" );
       $obPDF->addCampo("coluna5", 8 );

       //Dotações Suplementadas
       $obPDF->addRecordSet( $arRecordSet2[$inCount] );
       $obPDF->setQuebraPaginaLista( false);
       $obPDF->setAlturaCabecalho(10);
       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCabecalho(""                      , 17, 8, "B");
       $obPDF->addCabecalho("Dotações Suplementadas", 25, 8, "B");
       $obPDF->addCabecalho("Fonte"                 , 10, 8, "B");
       $obPDF->setAlinhamento ( "L" );
       $obPDF->addCabecalho("Descrição"             , 38, 8, "B");
       $obPDF->setAlinhamento ( "R" );
       $obPDF->addCabecalho("Valor"                 , 10, 8, "B");

       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCampo("coluna1", 8 );
       $obPDF->addCampo("coluna2", 8 );
       $obPDF->addCampo("coluna3", 8 );
       $obPDF->setAlinhamento ( "L" );
       $obPDF->addCampo("coluna4", 8 );
       $obPDF->setAlinhamento ( "R" );
       $obPDF->addCampo("coluna5", 8 );

       //Total do Decreto
       $obPDF->addRecordSet( $arRecordSet3[$inCount] );
       $obPDF->setQuebraPaginaLista( false);
       $obPDF->setAlturaCabecalho(10);
       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCabecalho("" , 20, 8);
       $obPDF->addCabecalho("", 25,8);
       $obPDF->addCabecalho("", 35, 8);
       $obPDF->addCabecalho("", 10, 8);
       $obPDF->addCabecalho("", 10, 8);

       $obPDF->setAlinhamento ( "C" );
       $obPDF->addCampo("coluna1", 8 );
       $obPDF->addCampo("coluna2", 8 );
       $obPDF->setAlinhamento ( "L" );
       $obPDF->addCampo("coluna3", 8 );
       $obPDF->setAlinhamento ( "R" );
       $obPDF->addCampo("coluna4", 8,"B" );
       $obPDF->addCampo("coluna5", 8 );

       $inCount++;
    }
  }
  break;

}

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
