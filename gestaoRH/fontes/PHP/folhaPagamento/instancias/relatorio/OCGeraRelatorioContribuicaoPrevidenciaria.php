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
    * Oculto de Relatório de Contribuição Previdenciária
    * Data de Criação   : 02/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 24226 $
    $Name$
    $Author: melo $
    $Date: 2007-07-24 14:56:22 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.43
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
include_once( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                           );

$obRRelatorio           = new RRelatorio;
$obCGM                  = new RCGMPessoaFisica;
$obPDF                  = new ListaPDF('L');

$obRRelatorio->setExercicio  ( $sessao->exercicio );
$obRRelatorio->setCodigoEntidade( $sessao->getCodEntidade() );
$obRRelatorio->setExercicioEntidade( $sessao->exercicio );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Folha de Pagamento" );
$obPDF->setSubTitulo         ( $sessao->exercicio );
$obPDF->setUsuario           ( $sessao->username );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

if ($sessao->filtro['inCodConfiguracao'] == 1) {
    $obPDF->addFiltro( "Obs."    , "Os valores apresentados no relatório correspondem a valores acumulados das folhas de salário e folhas complementares." );
}
$rsRecordSet = (is_object($sessao->transf5)) ? $sessao->transf5 : new recordset ;
$rsVazio = new RecordSet;

$arRecordset = $rsRecordSet->getElementos();
$arRecordset = (is_array($arRecordset)) ? $arRecordset : array();

if ( count($arRecordset) ) {
    foreach ($arRecordset as $inIndex=>$arPagina) {
        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['linha1'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     10, 10);
        $obPDF->addCabecalho    ( "",     20, 10);
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo3", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo4", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo5", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo6", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo7", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo8", 8   );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['contratos'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "R"                       );
        $obPDF->addCabecalho    ( "Matrícula"                       ,    5, 8 );
        $obPDF->setAlinhamento  ( "L"                       );
        $obPDF->addCabecalho    ( "Servidor"                        ,   15, 8 );
        $obPDF->addCabecalho    ( "Lotação"                         ,   15, 8 );
        $obPDF->setAlinhamento  ( "C"                       );
        $obPDF->addCabecalho    ( "Categoria"                       ,   6, 8 );
        $obPDF->addCabecalho    ( "Classificação de Agentes Nocivos",   10, 8 );
        $obPDF->setAlinhamento  ( "R"                       );
        $obPDF->addCabecalho    ( "Base Previdência"                ,   7, 8 );
        $obPDF->addCabecalho    ( "Desconto da Previdência"         ,   7, 8 );
        $obPDF->addCabecalho    ( "Nro. Dependentes"                ,   7, 8 );
        $obPDF->addCabecalho    ( "Valor Salário Família"           ,   7, 8 );
        $obPDF->addCabecalho    ( "Valor Salário Maternidade"       ,   7, 8 );
        $obPDF->addCabecalho    ( "Valor Patronal"                  ,   7, 8 );
        $obPDF->addCabecalho    ( "Total Recolhido"                 ,   7, 8 );

        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "registro", 8   );
        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "nom_cgm", 8   );
        $obPDF->addCampo        ( "descricao_lotacao", 8   );
        $obPDF->setAlinhamento  ( "C"           );
        $obPDF->addCampo        ( "cod_categoria", 8   );
        $obPDF->addCampo        ( "cod_ocorrencia", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "valor_base", 8   );
        $obPDF->addCampo        ( "valor_desconto", 8   );
        $obPDF->addCampo        ( "num_dependentes", 8   );
        $obPDF->addCampo        ( "valor_familia", 8   );
        $obPDF->addCampo        ( "valor_maternidade",8   );
        $obPDF->addCampo        ( "valor_patronal",8   );
        $obPDF->addCampo        ( "valor_recolhido",8   );

        $rsTemp = new RecordSet;
        $rsTemp->preenche( $arPagina['totais'] );
        $obPDF->addRecordSet($rsTemp);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->setAlinhamento  ( "L"                 );
        $obPDF->addCabecalho    ( ""        ,   15, 8 );
        $obPDF->addCabecalho    ( ""        ,    2, 8 );
        $obPDF->setAlinhamento  ( "R"                 );
        $obPDF->addCabecalho    ( ""        ,   10, 8 );

        $obPDF->setAlinhamento  ( "L"           );
        $obPDF->addCampo        ( "campo1", 8   );
        $obPDF->addCampo        ( "campo2", 8   );
        $obPDF->setAlinhamento  ( "R"           );
        $obPDF->addCampo        ( "campo3", 8   );

    }
}

$obPDF->show();

?>
