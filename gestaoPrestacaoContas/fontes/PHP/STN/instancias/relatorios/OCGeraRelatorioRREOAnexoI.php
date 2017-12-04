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

$Revision: 59612 $
$Name$
$Author: gelson $
$Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

* Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaFormPDFRH.class.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                                      );

    $obRRelatorio = new RRelatorio;
    $obPDF        = new ListaFormPDF();

    $arDados = //sessao->transf5;

    $obRRelatorio->setExercicio  ( Sessao::getExercicio() );
    $obRRelatorio->recuperaCabecalho( $arConfiguracao );

    $obPDF->setModulo            ( "Relatório"                                   );
    $obPDF->setTitulo            ( "Relatório Resumido de Execução Orçamentária" );
    $obPDF->setSubTitulo         ( ""                                            );
    $obPDF->setUsuario           ( Sessao::getUsername()                             );
    $obPDF->setEnderecoPrefeitura( $arConfiguracao                               );

    //LInha de Cabeçaçho do Relatório
    $arCabecalho = array();
    $arCabecalho[0]['campo1'] = '';
    $arCabecalho[1]['campo1'] = 'RELATÓRIO RESUMIDO DE EXECUÇÃO ORÇAMENTÁRIA';
    $arCabecalho[2]['campo1'] = 'BALANÇO ORÇAMENTÁRIO';
    $arCabecalho[4]['campo1'] = 'ORÇAMENTOS FISCAL E DE SEGURIDADE SOCIAL';
    $arCabecalho[5]['campo1'] = '';

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arCabecalho );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setAlinhamento  ( "C"        );
    $obPDF->addCabecalho    ( "", 100, 10);
    $obPDF->setAlinhamento  ( "C"        );
    $obPDF->addCampo        ( "campo1", 8,  '', '', ''  );

    // Cabeçalho da Seção receitas
    $arLinha1 = array();
    $arLinha1[0]['campo1'] = '';
    $arLinha1[0]['campo2'] = 'PREVISÃO';
    $arLinha1[0]['campo3'] = 'PREVISÃO';
    $arLinha1[0]['campo4'] = 'RECEITAS REALIZADAS';
    $arLinha1[0]['campo5'] = 'SALDO A';

    /// primeira linha do cabeçalho
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "C"       );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->addCabecalho    ( "", 10, 10);
    $obPDF->addCabecalho    ( "", 10, 10);
    $obPDF->addCabecalho    ( "", 40, 10);
    $obPDF->addCabecalho    ( "", 10, 10);

    $obPDF->setAlinhamento  ( "C"           );
    $obPDF->addCampo        ( "campo1", 8,  '', '', 'TR'   );
    $obPDF->addCampo        ( "campo2", 8,  '', '', 'LTR'  );
    $obPDF->addCampo        ( "campo3", 8,  '', '', 'LTR'  );
    $obPDF->addCampo        ( "campo4", 8,  '', '', 'LTRB' );
    $obPDF->addCampo        ( "campo5", 8,  '', '', 'LT'   );

    // segunda
    $arLinha1 = array();
    $arLinha1[0]['campo1'] = 'RECEITAS';
    $arLinha1[0]['campo2'] = 'INICIAL';
    $arLinha1[0]['campo3'] = 'ATUALIZADA';
    $arLinha1[0]['campo4'] = 'No Bimestre';
    $arLinha1[0]['campo5'] = '%';
    $arLinha1[0]['campo6'] = 'Até o Bimestre';
    $arLinha1[0]['campo7'] = '%';
    $arLinha1[0]['campo8'] = 'Realizar';

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "C"     );
    $obPDF->addCabecalho    ( "", 30 , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );
    $obPDF->addCabecalho    ( "", 15 , 10 );
    $obPDF->addCabecalho    ( "", 5  , 10 );
    $obPDF->addCabecalho    ( "", 15 , 10 );
    $obPDF->addCabecalho    ( "", 5  , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );

    $obPDF->addCampo        ( "campo1", 8,'', '', 'R'  );
    $obPDF->addCampo        ( "campo2", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo3", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo4", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo5", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo6", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo7", 8,'', '', 'LR' );
    $obPDF->addCampo        ( "campo8", 8,'', '', 'L'  );

    $arLinha1 = array();
    $arLinha1[0]['campo1'] = '';
    $arLinha1[0]['campo2'] = '';
    $arLinha1[0]['campo3'] = '(a)';
    $arLinha1[0]['campo4'] = '(b)';
    $arLinha1[0]['campo5'] = '(b/a)';
    $arLinha1[0]['campo6'] = '(c)';
    $arLinha1[0]['campo7'] = '(c/a)';
    $arLinha1[0]['campo8'] = '(a-c)';

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "C"     );
    $obPDF->addCabecalho    ( "", 30 , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );
    $obPDF->addCabecalho    ( "", 15 , 10 );
    $obPDF->addCabecalho    ( "", 5  , 10 );
    $obPDF->addCabecalho    ( "", 15 , 10 );
    $obPDF->addCabecalho    ( "", 5  , 10 );
    $obPDF->addCabecalho    ( "", 10 , 10 );

    $obPDF->addCampo        ( "campo1", 8,  '', '', 'RB' );
    $obPDF->addCampo        ( "campo2", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo3", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo4", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo5", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo6", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo7", 8,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo8", 8,  '', '', 'LB' );

    ////Dados da Seção Receitas
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['receita'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T'  );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T'  );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'R'  );
    $obPDF->setAlinhamento  ( "R"     );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'L'  );

    // totais da receita
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['totalReceita'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'B'  );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'B'  );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'B'  );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', ''  );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'L' );

    /// seção de CREDITO
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['creditos'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'TB' );

    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'R'  );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'L'  );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['totalCreditos'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'TB' );

    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'RB'  );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'LB'  );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['deficit'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'TB' );

    $obPDF->setAlinhamento  ( 'C' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->setAlinhamento  ( 'C' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'RB'  );
    $obPDF->setAlinhamento  ( 'C' );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RBL' );
    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RBL' );
    $obPDF->setAlinhamento  ( 'C' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'LB'  );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['total'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'TB' );

    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'RB'  );
    $obPDF->setAlinhamento  ( "R"     );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'LB'  );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['saldo'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCabecalho    ( "", 30, 10 ,  '', '', 'TB' );

    $obPDF->setAlinhamento  ( 'R' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 15, 10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 5,  10 ,  '', '', 'T' );
    $obPDF->addCabecalho    ( "", 10, 10 ,  '', '', 'T' );

    $obPDF->setAlinhamento  ( "L"     );
    $obPDF->addCampo        ( 'nom_conta'           , 6,  '', '', 'RB'  );
    $obPDF->setAlinhamento  ( "R"     );
    $obPDF->addCampo        ( 'previsao_inicial'    , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'previsao_atualizada' , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'no_bimestre'         , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_no_bimestre'       , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'ate_bimestre'        , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'p_ate_bimestre'      , 6,  '', '', 'RBL' );
    $obPDF->addCampo        ( 'a_realizar'          , 6,  '', '', 'LB'  );

    /// Seção de Despesas..................................................................................................

    // este recordset serve apenas para deixar um espaço entre duas partes deste relatório
    $arEspaco = array();
    $arEspaco[0]['campoo'] = '';
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arEspaco );
    $obPDF->addRecordSet($rsTemp );
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento  ( "L"       );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->addCampo        ( "campo", 6);

    // Linha 1 do cabeçalho
    $arLinha1 = array();
    $arLinha1[0]['campo1']  = '';
    $arLinha1[0]['campo2']  = 'DOTAÇÃO';
    $arLinha1[0]['campo3']  = 'CRÉDITOS';
    $arLinha1[0]['campo4']  = 'DOTAÇÃO';
    $arLinha1[0]['campo5']  = 'DESPESAS EMPENHADAS';
    $arLinha1[0]['campo6']  = 'DESPESAS LIQUIDADAS';
    $arLinha1[0]['campo7']  = 'SALDO A';
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );
    $obPDF->setAlinhamento  ( "L"       );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 16, 10);
    $obPDF->addCabecalho    ( '', 20, 10);
    $obPDF->addCabecalho    ( '', 10, 10);

    $obPDF->setAlinhamento  ( "C"           );
    $obPDF->addCampo        ( "campo1", 6,  '', '', 'TR'   );
    $obPDF->addCampo        ( "campo2", 6,  '', '', 'LTR'  );
    $obPDF->addCampo        ( "campo3", 6,  '', '', 'LTR'  );
    $obPDF->addCampo        ( "campo4", 6,  '', '', 'LTR'  );
    $obPDF->addCampo        ( "campo5", 6,  '', '', 'LTRB' );
    $obPDF->addCampo        ( "campo6", 6,  '', '', 'LTRB' );
    $obPDF->addCampo        ( "campo7", 6,  '', '', 'LT'  );

    // Linha 2 do cabeçalho
    $arLinha1 = array();
    $arLinha1[0]['campo1']  = 'DESPESAS';
    $arLinha1[0]['campo2']  = 'INICIAL';
    $arLinha1[0]['campo3']  = 'ADICIONAIS';
    $arLinha1[0]['campo4']  = 'ATUALIZADA';
    $arLinha1[0]['campo5']  = 'No Bimestre';
    $arLinha1[0]['campo6']  = 'Até o Bimestre';
    $arLinha1[0]['campo7']  = 'No Bimestre';
    $arLinha1[0]['campo8']  = 'Até o Bimestre';
    $arLinha1[0]['campo9']  = '%';
    $arLinha1[0]['campo10'] = 'LIQUIDAR';

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 4, 10 );
    $obPDF->addCabecalho    ( '', 10, 10);

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "campo1" , 6,  '', '', 'R' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCampo        ( "campo2" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo3" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo4" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo5" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo6" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo7" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo8" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo9" , 6,  '', '', 'LR' );
    $obPDF->addCampo        ( "campo10", 6,  '', '', 'L' );

    // linha 3 do cabeçalho
    $arLinha1 = array();
    $arLinha1[0]['campo1']  =  ''   ;
    $arLinha1[0]['campo2']  =  '(d)';
    $arLinha1[0]['campo3']  =  '(e)';
    $arLinha1[0]['campo4']  =  '(f)=(d+e)';
    $arLinha1[0]['campo5']  =  '(g)';
    $arLinha1[0]['campo6']  =  '(h)';
    $arLinha1[0]['campo7']  =  '(i)';
    $arLinha1[0]['campo8']  =  '(j)';
    $arLinha1[0]['campo9']  =  '(j/f)';
    $arLinha1[0]['campo10'] =  '(f-j)';

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arLinha1 );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10);
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 8, 10 );
    $obPDF->addCabecalho    ( '', 4, 10 );
    $obPDF->addCabecalho    ( '', 10, 10);

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "campo1" , 6,  '', '', 'RB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCampo        ( "campo2" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo3" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo4" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo5" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo6" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo7" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo8" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo9" , 6,  '', '', 'LRB' );
    $obPDF->addCampo        ( "campo10", 6,  '', '', 'LB' );

    /// dados das despesas
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['despesas'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'RB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LTB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 6,  '', '', '' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'L' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'L' );

    /// total das desepesas (VI)
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['totalDespesas'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'TRB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LTRB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 8,  '', '', 'B' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'LB' );

    // dados de amortização de divida / refinanciamento
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['amortizacao'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'TRB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LTRB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 6,  '', '', 'B' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'LB' );

    // SUBTOTAL COM REFINANCIAMENTO (VIII) = (VI) + (VII)
    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['totalAmortizacao'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'TRB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LTRB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 6,  '', '', 'B' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'LB' );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['superAvit'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'TRB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LTRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LTRB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 6,  '', '', 'B' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'LB' );

    $rsTemp = new RecordSet;
    $rsTemp->preenche( $arDados['totalRel'] );
    $obPDF->addRecordSet($rsTemp);
    $obPDF->setQuebraPaginaLista( false );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCabecalho    ( "", 30, 10, '', '', 'RB' );
    $obPDF->setAlinhamento  ( 'C'       );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 8, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 4, 10, '', '', 'LRB' );
    $obPDF->addCabecalho    ( '', 10, 10,'', '', 'LRB' );

    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo        ( "descricao" , 6,  '', '', 'B' );
    $obPDF->setAlinhamento  ( 'R'       );
    $obPDF->addCampo        ( 'dotacao_inicial'      , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'creditos_adicionais'  , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'dotacao_atualizada'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_empenhado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_bimestre', 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'vl_liquidado_total'   , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'percentual'           , 6,  '', '', 'LB' );
    $obPDF->addCampo        ( 'saldo_liquidar'       , 6,  '', '', 'LB' );

    //// FINAL Seção de despesas...........................................................................................
    $obPDF->setAlturaCabecalho( 0.3 );
    $obPDF->show();

?>
