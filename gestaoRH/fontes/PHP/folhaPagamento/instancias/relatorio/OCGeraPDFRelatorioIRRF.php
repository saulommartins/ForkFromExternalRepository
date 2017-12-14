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
* Página de relatório de Fornecedor
* Data de Criação   : 13/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @ignore

$Revision: 30840 $
$Name$
$Author: tiago $
$Date: 2007-07-24 11:38:32 -0300 (Ter, 24 Jul 2007) $

* Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaFormPDFRH.class.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"             );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaFormPDFRH();

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatório"            );
$obPDF->setTitulo            ( "Relatório de IRRF"    );
$obPDF->setSubTitulo         ( ""                     );
$obPDF->setUsuario           ( Sessao::getUsername()      );
$obPDF->setEnderecoPrefeitura( $arConfiguracao        );

$arRecordset = Sessao::read("relatorioIRRF");
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['boAgrupar'] != true) {

    //Linha de Cabeçalho
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arRecordset['linha1'] );
    $obPDF->addRecordSet($rsTemp);

    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCabecalho    ( "",     10, 10);
    $obPDF->addCabecalho    ( "",     20, 10);
    $obPDF->addCabecalho    ( "",     10, 10);
    $obPDF->addCabecalho    ( "",     30, 10);
    $obPDF->addCabecalho    ( "",     10, 10);
    $obPDF->addCabecalho    ( "",     20, 10);
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCampo        ( "campo1", 8,  '', '', 'TL'  );
    $obPDF->setAlinhamento  ( "L"           );
    $obPDF->addCampo        ( "campo2", 8,   '', '', 'T'  );
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCampo        ( "campo3", 8,   '', '', 'T'  );
    $obPDF->setAlinhamento  ( "L"           );
    $obPDF->addCampo        ( "campo4", 8,   '', '', 'T'  );
    $obPDF->setAlinhamento  ( "R"           );
    $obPDF->addCampo        ( "campo5", 8,   '', '', 'T'  );
    $obPDF->setAlinhamento  ( "L"           );
    $obPDF->addCampo        ( "campo6", 8,  '', '', 'TR'  );

    // corpo do relatorio
    //Cabeçalhos
    $rsTemp = new RecordSet;
    $arTemp = array();
    $arTemp[] = array("campo1"=>"Matrícula",
                      "campo2"=>"Servidor",
                      "campo3"=>"Lotação",
                      "campo4"=>"Base do IRRF",
                      "campo5"=>"Desconto do IRRF");
    $rsTemp->preenche($arTemp);

    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->addCabecalho    ( "",      9, 10);
    $obPDF->addCabecalho    ( "",     32, 10);
    $obPDF->addCabecalho    ( "",     32, 10);
    $obPDF->addCabecalho    ( "",     13, 10);
    $obPDF->addCabecalho    ( "",     14, 10);
    $obPDF->addCampo        ( "campo1",8 , '', '', 'BL', '205,206,205');
    $obPDF->addCampo        ( "campo2",8 , '', '', '', '205,206,205'  );
    $obPDF->addCampo        ( "campo3",8 , '', '', '', '205,206,205'  );
    $obPDF->setAlinhamento  ( "R" 									  );
    $obPDF->addCampo        ( "campo4",8  , '', '', '', '205,206,205' );
    $obPDF->setAlinhamento  ( "R" 									  );
    $obPDF->addCampo        ( "campo5",8 , '', '', 'BR', '205,206,205');

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arRecordset['corpo'] );
    $rsTemp->addFormatacao('campo4','NUMERIC_BR');
    $rsTemp->addFormatacao('campo5','NUMERIC_BR');
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("" , 9, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",32, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",32, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",13, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",14, 10);

    // Linhas
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo1"     , 8 ,  '' , '' ,'BL' );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo2"     , 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo3"     , 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ("campo4"     , 8 );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ("campo5"     , 8 , '', '', 'BR' );

    // Linha de totais
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arRecordset['totais'] );
    $rsTemp->addFormatacao('campo4','NUMERIC_BR');
    $rsTemp->addFormatacao('campo5','NUMERIC_BR');
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    //Cabeçalhos
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("" , 12, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",29, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",32, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",13, 10);
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ("",14, 10);
    // Linhas
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo1"     , 8 ,  '' , '' ,'BL' );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo2"     , 8 ,  '' , '' ,'B' );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ("campo3"     , 8 ,  '' , '' ,'B' );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ("campo4"     , 8 ,  '' , '' ,'B' );
    $obPDF->setAlinhamento ( "R" );
    $obPDF->addCampo       ("campo5"     , 8 ,  '' , '' ,'BR' );

} else {

    $inCont = 0;
    if ( is_array($arRecordset['agrupado']) ) {
        foreach ($arRecordset['agrupado'] AS $inIndex => $stValor) {

            if ($arFiltro['boQuebrarPagina'] != true) {

                $inQuantidadeCampos += count($stValor["corpo"]);

                if ($inCont == 0 || $inQuantidadeCampos >= 34) {

                    //Linha de Cabeçalho
                    $rsTemp = new RecordSet;
                    $rsTemp->preenche( $stValor['linha1'] );
                    $obPDF->addRecordSet($rsTemp);

                    $obPDF->setAlinhamento  ( "R"           );
                    $obPDF->addCabecalho    ( "",     10, 10);
                    $obPDF->addCabecalho    ( "",     20, 10);
                    $obPDF->addCabecalho    ( "",     10, 10);
                    $obPDF->addCabecalho    ( "",     30, 10);
                    $obPDF->addCabecalho    ( "",     10, 10);
                    $obPDF->addCabecalho    ( "",     20, 10);

                    $obPDF->setAlinhamento  ( "R"           );
                    $obPDF->addCampo        ( "campo1", 8,  '', '', 'TL'  );
                    $obPDF->setAlinhamento  ( "L"           );
                    $obPDF->addCampo        ( "campo2", 8,   '', '', 'T'  );
                    $obPDF->setAlinhamento  ( "R"           );
                    $obPDF->addCampo        ( "campo3", 8,   '', '', 'T'  );
                    $obPDF->setAlinhamento  ( "L"           );
                    $obPDF->addCampo        ( "campo4", 8,   '', '', 'T'  );
                    $obPDF->setAlinhamento  ( "R"           );
                    $obPDF->addCampo        ( "campo5", 8,   '', '', 'T'  );
                    $obPDF->setAlinhamento  ( "L"           );
                    $obPDF->addCampo        ( "campo6", 8,  '', '', 'TR'  );

                    // corpo do relatorio
                    //Cabeçalhos
                    $rsTemp = new RecordSet;
                    $arTemp = array();
                    $arTemp[] = array("campo1"=>"Matrícula",
                                      "campo2"=>"Servidor",
                                      "campo3"=>"Lotação",
                                      "campo4"=>"Base do IRRF",
                                      "campo5"=>"Desconto do IRRF");
                    $rsTemp->preenche($arTemp);

                    $obPDF->addRecordSet($rsTemp);
                    $obPDF->setQuebraPaginaLista( false );
                    $obPDF->addCabecalho    ( "",      9, 10);
                    $obPDF->addCabecalho    ( "",     32, 10);
                    $obPDF->addCabecalho    ( "",     32, 10);
                    $obPDF->addCabecalho    ( "",     13, 10);
                    $obPDF->addCabecalho    ( "",     14, 10);

                    $obPDF->addCampo        ( "campo1",8 , '', '', 'BL', '205,206,205');
                    $obPDF->addCampo        ( "campo2",8 , '', '', 'BR', '205,206,205'  );
                    $obPDF->addCampo        ( "campo3",8 , '', '', 'BR', '205,206,205'  );
                    $obPDF->setAlinhamento  ( "R" 									  );
                    $obPDF->addCampo        ( "campo4",8  , '', '', 'BR', '205,206,205' );
                    $obPDF->setAlinhamento  ( "R" 									  );
                    $obPDF->addCampo        ( "campo5",8 , '', '', 'BR', '205,206,205');

                }
                $inCont++;

                if ($inQuantidadeCampos >= 34) {
                    $inQuantidadeCampos = 0;
                    $inCont = 0;
                }

            } else {

                //Linha de Cabeçalho
                $rsTemp = new RecordSet;
                $rsTemp->preenche( $stValor['linha1'] );
                $obPDF->addRecordSet($rsTemp);

                $obPDF->setAlinhamento  ( "R"           );
                $obPDF->addCabecalho    ( "",     10, 10);
                $obPDF->addCabecalho    ( "",     20, 10);
                $obPDF->addCabecalho    ( "",     10, 10);
                $obPDF->addCabecalho    ( "",     30, 10);
                $obPDF->addCabecalho    ( "",     10, 10);
                $obPDF->addCabecalho    ( "",     20, 10);

                $obPDF->setAlinhamento  ( "R"           );
                $obPDF->addCampo        ( "campo1", 8,  '', '', 'TL'  );
                $obPDF->setAlinhamento  ( "L"           );
                $obPDF->addCampo        ( "campo2", 8,   '', '', 'T'  );
                $obPDF->setAlinhamento  ( "R"           );
                $obPDF->addCampo        ( "campo3", 8,   '', '', 'T'  );
                $obPDF->setAlinhamento  ( "L"           );
                $obPDF->addCampo        ( "campo4", 8,   '', '', 'T'  );
                $obPDF->setAlinhamento  ( "R"           );
                $obPDF->addCampo        ( "campo5", 8,   '', '', 'T'  );
                $obPDF->setAlinhamento  ( "L"           );
                $obPDF->addCampo        ( "campo6", 8,  '', '', 'TR'  );

                // corpo do relatorio
                //Cabeçalhos
                $rsTemp = new RecordSet;
                $arTemp = array();
                $arTemp[] = array("campo1"=>"Matrícula",
                                  "campo2"=>"Servidor",
                                  "campo3"=>"Lotação",
                                  "campo4"=>"Base do IRRF",
                                  "campo5"=>"Desconto do IRRF");
                $rsTemp->preenche($arTemp);

                $obPDF->addRecordSet($rsTemp);
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->addCabecalho    ( "",      9, 10);
                $obPDF->addCabecalho    ( "",     32, 10);
                $obPDF->addCabecalho    ( "",     32, 10);
                $obPDF->addCabecalho    ( "",     13, 10);
                $obPDF->addCabecalho    ( "",     14, 10);
                $obPDF->addCampo        ( "campo1",8 , '', '', 'BL', '205,206,205');
                $obPDF->addCampo        ( "campo2",8 , '', '', '', '205,206,205'  );
                $obPDF->addCampo        ( "campo3",8 , '', '', '', '205,206,205'  );
                $obPDF->setAlinhamento  ( "R" 									  );
                $obPDF->addCampo        ( "campo4",8  , '', '', '', '205,206,205' );
                $obPDF->setAlinhamento  ( "R" 									  );
                $obPDF->addCampo        ( "campo5",8 , '', '', 'BR', '205,206,205');

            }

            //apresenta filtro
            $rsTemp = new RecordSet;
            $rsTemp->preenche( $stValor['filtro'] );
            $obPDF->addRecordSet($rsTemp);
            $obPDF->setQuebraPaginaLista( false );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("" , 50, 10);
            $obPDF->addCabecalho   ("" , 50, 10);

            // Linhas
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("tipo_filtro"     , 8 ,  'B' , '' ,'BL' );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       (""     , 8 ,  'B' , '' ,'BR' 			);

            $rsTemp = new RecordSet;
            $rsTemp->preenche( $stValor['corpo'] );
            $rsTemp->addFormatacao('campo4','NUMERIC_BR');
            $rsTemp->addFormatacao('campo5','NUMERIC_BR');
            $obPDF->addRecordSet($rsTemp);
            $obPDF->setQuebraPaginaLista( false );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("" , 9, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",32, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",32, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",13, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",14, 10);

            // Linhas
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo1"     , 8 ,  '' , '' ,'BL' );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo2"     , 8 );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo3"     , 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ("campo4"     , 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ("campo5"     , 8 , '', '', 'BR' );

            // Linha de totais
            $rsTemp = new RecordSet;
            $rsTemp->preenche( $stValor['totais'] );
            $rsTemp->addFormatacao('campo4','NUMERIC_BR');
            $rsTemp->addFormatacao('campo5','NUMERIC_BR');
            $obPDF->addRecordSet($rsTemp);
            $obPDF->setQuebraPaginaLista( false );

            //Cabeçalhos
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("" , 12, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",29, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",32, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",13, 10);
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ("",14, 10);

            // Linhas
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo1"     , 8 ,  'B' , '' ,'BL' );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo2"     , 8 ,  'B' , '' ,'B'  );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ("campo3"     , 8 ,  'B' , '' ,'B'  );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ("campo4"     , 8 ,  'B' , '' ,'B'  );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ("campo5"     , 8 ,  'B' , '' ,'BR' );

        }
    }
}

$obPDF->show();

?>
