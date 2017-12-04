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
    * Oculto para geração do recordset do relatório de Creditos por banco
    * Data de Criação: 14/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Revision: 24788 $
    $Name$
    $Author: tiago $
    $Date: 2007-08-15 10:42:50 -0300 (Qua, 15 Ago 2007) $

    * Casos de uso: uc-04.05.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaFormPDFRH.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/PDF/ListaPDFRH.class.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );

global $sessao;
$rsRecordset = $sessao->transf5;
$obRRelatorio = new RRelatorio    ;
$obPDF        = new ListaFormPDFRH();

$obRRelatorio->setExercicio      ( $sessao->exercicio );
$obRRelatorio->setCodigoEntidade( $sessao->getCodEntidade() );
$obRRelatorio->setExercicioEntidade( $sessao->exercicio );
$obRRelatorio->recuperaCabecalho ( $arConfiguracao    );

$obPDF->setModulo            ( "Relatório"                          );
$obPDF->setTitulo            ( "Relatório de Créditos por Banco"    );
$obPDF->setSubTitulo         ( ""                                   );
$obPDF->setUsuario           ( $sessao->username                    );
$obPDF->setEnderecoPrefeitura( $arConfiguracao                      );

while (!$rsRecordset->eof()) {
    $rsTemp = new Recordset;
    $rsTemp->preenche($rsRecordset->getCampo("arCabecalho"));

    $obPDF->addRecordSet($rsTemp);
    if ($rsRecordset->getCorrente() > 1) {
        $obPDF->setQuebraPaginaLista(false);
    }
    $obPDF->addCabecalho ( "", 10, 10);
    $obPDF->addCabecalho ( "", 20, 10);
    $obPDF->addCabecalho ( "", 10, 10);
    $obPDF->addCabecalho ( "", 30, 20);
    $obPDF->addCabecalho ( "", 10, 10);
    $obPDF->addCabecalho ( "", 20, 20);

    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo ( 'campo1' , 8 );
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo ( 'campo2' , 8 );

    $inCountTemp = (count($obPDF->arRecordSet )) - 1;

    for ($inTemp =0; $inTemp <= $inCountTemp ;$inTemp++) {
        $inTemp2 = count($obPDF->arRecordSet[$inTemp]->arElementos) - 1;
        for ($inTemp2=0; $inTemp2 <= $inCountTemp2; $inTemp2++) {
             $stCampo3 = trim($obPDF->arRecordSet[$inTemp]->arElementos[$inTemp2]["campo3"]);
             $stCampo4 = trim($obPDF->arRecordSet[$inTemp]->arElementos[$inTemp2]["campo4"]);
             $stCampo5 = trim($obPDF->arRecordSet[$inTemp]->arElementos[$inTemp2]["campo5"]);
             $stCampo6 = trim($obPDF->arRecordSet[$inTemp]->arElementos[$inTemp2]["campo6"]);
        }
    }
    if ( $sessao->filtro['boTotalLotacao'] == true and is_array($rsRecordset->getCampo("arTotalLotacao")) ) {
       $obPDF->setAlinhamento  ( "R" );
       $obPDF->addCampo ( 'campo3' , 8 );
       $obPDF->setAlinhamento  ( "L" );
       $obPDF->addCampo ( 'campo4' , 8 );
    }
    if ( $sessao->filtro['boTotalLocal'] == true and is_array($rsRecordset->getCampo("arTotalLocal")) ) {
          $obPDF->setAlinhamento  ( "R" );
          $obPDF->addCampo ( 'campo5' , 8 );
          $obPDF->setAlinhamento  ( "L" );
          $obPDF->addCampo ( 'campo6' , 8 );
    }

    $arCabecalho['campo1'] = "Matrícula";
    $arCabecalho['campo2'] = "CGM";
    $arCabecalho['campo3'] = "CPF";
    $arCabecalho['campo4'] = "Conta";
    $arCabecalho['campo5'] = "Valor";
    $arCabecalhos   = array();
    $arCabecalhos[] = $arCabecalho;
    $rsCabecalho = new Recordset;
    $rsCabecalho->preenche($arCabecalhos);
    $obPDF->addRecordSet($rsCabecalho);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->addCabecalho ( "", 10, 8);
    $obPDF->addCabecalho ( "", 50, 8);
    $obPDF->addCabecalho ( "", 15, 8);
    $obPDF->addCabecalho ( "", 15, 8);
    $obPDF->addCabecalho ( "", 10, 8);

    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo ( 'campo1' , 8 ,'','','','205,206,205');
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo ( 'campo2' , 8 ,'','','','205,206,205');
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo ( 'campo3' , 8 ,'','','','205,206,205');
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo ( 'campo4' , 8 ,'','','','205,206,205');
    $obPDF->addCampo ( 'campo5' , 8 ,'','','','205,206,205');

    $rsContratos = new Recordset;
    $rsContratos->preenche($rsRecordset->getCampo("arContratos"));

    $obPDF->addRecordSet($rsContratos);
    $obPDF->setQuebraPaginaLista(false);
    $obPDF->addCabecalho ( "", 10, 8);
    $obPDF->addCabecalho ( "", 50, 8);
    $obPDF->addCabecalho ( "", 15, 8);
    $obPDF->addCabecalho ( "", 15, 8);
    $obPDF->addCabecalho ( "", 10, 8);

    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo ( 'registro'           , 8 );
    $obPDF->setAlinhamento  ( "L" );
    $obPDF->addCampo ( 'cgm'                , 8 );
    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo ( 'cpf'                , 8 );
    $obPDF->setAlinhamento  ( "R" );
    $obPDF->addCampo ( 'nr_conta'           , 8 );
    $obPDF->addCampo ( 'valor'              , 8 );

    if ( $sessao->filtro['boTotalBanco'] == 'on' and is_array($rsRecordset->getCampo("arTotalBanco")) ) {
        $arCabecalho['campo1'] = "TOTAL DO BANCO";
        $arCabecalhos = array();
        $arCabecalhos[] = $arCabecalho;
        $rsCabecalho = new Recordset;
        $rsCabecalho->preenche($arCabecalhos);
        $obPDF->addRecordSet($rsCabecalho);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho ( "", 100, 8);
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo ( 'campo1' , 8 ,'','','','205,206,205');

        $rsTotalBanco = new Recordset;
        $rsTotalBanco->preenche($rsRecordset->getCampo("arTotalBanco"));
        $obPDF->addRecordSet($rsTotalBanco);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho ( "", 50, 8);
        $obPDF->addCabecalho ( "", 50, 8);

        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo ( 'campo1'           , 8 );
        $obPDF->addCampo ( 'campo2'           , 8 );

        if ( $sessao->filtro['boQuebraBanco'] == 'on' && ( $sessao->filtro['boQuebraLocal'] != 'on' && $sessao->filtro['boQuebraLotacao'] != 'on' ) ) {
            $rsRecordset->proximo();
            if ((!$rsRecordset->eof()) or ( $rsRecordset->eof() and $sessao->filtro['boTotalBanco']== 'on') ) {
                $rstemp = new Recordset;
                $obPDF->addRecordSet($rstemp);
                $obPDF->setQuebraPaginaLista(true);
            }
            $rsRecordset->anterior();
        }
    }

    if ( $sessao->filtro['boTotalLocal'] == 'on' and is_array($rsRecordset->getCampo("arTotalLocal")) ) {
        $arCabecalho['campo1'] = "TOTAL DO LOCAL";
        $arCabecalhos = array();
        $arCabecalhos[] = $arCabecalho;
        $rsCabecalho = new Recordset;
        $rsCabecalho->preenche($arCabecalhos);
        $obPDF->addRecordSet($rsCabecalho);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho ( "", 100, 8);
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo ( 'campo1' , 8 ,'','','','205,206,205');

        $rsTotalLocal = new Recordset;
        $rsTotalLocal->preenche($rsRecordset->getCampo("arTotalLocal"));

        $obPDF->addRecordSet($rsTotalLocal);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho ( "", 50, 8);
        $obPDF->addCabecalho ( "", 50, 8);

        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo ( 'campo1'           , 8 );
        $obPDF->addCampo ( 'campo2'                , 8 );

        if ( $sessao->filtro['boQuebraLocal'] == 'on' && ( $sessao->filtro['boQuebraLotacao'] != 'on' && $sessao->filtro['boQuebraBanco'] != 'on' ) ) {
            $rsRecordset->proximo();
            if ((!$rsRecordset->eof()) or ( $rsRecordset->eof() and $sessao->filtro['boTotalLotacao'] == 'on' ) ) {
                $rstemp = new Recordset;
                $obPDF->addRecordSet($rstemp);
                $obPDF->setQuebraPaginaLista(true);
            }
            $rsRecordset->anterior();
        }

    }

    if ( $sessao->filtro['boTotalLotacao'] == 'on' and is_array($rsRecordset->getCampo("arTotalLotacao")) ) {
        $arCabecalho['campo1'] = "TOTAL DA LOTAÇÃO";
        $arCabecalhos = array();
        $arCabecalhos[] = $arCabecalho;
        $rsCabecalho = new Recordset;
        $rsCabecalho->preenche($arCabecalhos);
        $obPDF->addRecordSet($rsCabecalho);
        $obPDF->setQuebraPaginaLista(false);
        $obPDF->addCabecalho ( "", 100, 8);
        $obPDF->setAlinhamento  ( "C" );
        $obPDF->addCampo ( 'campo1' , 8 ,'','','','205,206,205');

        $rsTotalLotacao = new Recordset;
        $rsTotalLotacao->preenche($rsRecordset->getCampo("arTotalLotacao"));

        $obPDF->addRecordSet($rsTotalLotacao);

        $obPDF->addCabecalho ( "", 50, 8);
        $obPDF->addCabecalho ( "", 50, 8);

        $obPDF->setAlinhamento  ( "R" );
        $obPDF->addCampo ( 'campo1'           , 8 );
        $obPDF->addCampo ( 'campo2'                , 8 );
        $obPDF->setQuebraPaginaLista(false);

        if ( $sessao->filtro['boQuebraLotacao'] == 'on' && ( $sessao->filtro['boQuebraLocal'] != 'on' && $sessao->filtro['boQuebraBanco'] != 'on' ) ) {
            $rsRecordset->proximo();
            if (!$rsRecordset->eof()) {
                $rstemp = new Recordset;
                $obPDF->addRecordSet($rstemp);
                $obPDF->setQuebraPaginaLista(true);
            }
            $rsRecordset->anterior();
        }
    }

    $arCabecalho['campo1'] = "";
    $arCabecalhos   = array();
    $arCabecalhos[] = $arCabecalho;
    $rsCabecalho = new Recordset;
    $rsCabecalho->preenche($arCabecalhos);
    $obPDF->addRecordSet($rsCabecalho);

    if ($sessao->filtro['boQuebraLocal'] == 'on' && $sessao->filtro['boQuebraBanco'] == 'on' || $sessao->filtro['boQuebraLocal'] == 'on' && $sessao->filtro['boQuebraLotacao'] == 'on' || $sessao->filtro['boQuebraLotacao'] == 'on' && $sessao->filtro['boQuebraLocal'] == 'on' || $sessao->filtro['boQuebraLotacao'] == 'on' && $sessao->filtro['boQuebraBanco'] && $sessao->filtro['boQuebraBanco'] == 'on' && $sessao->filtro['boQuebraLocal'] == 'on' || $sessao->filtro['boQuebraBanco'] == 'on' && $sessao->filtro['boQuebraLotacao'] == 'on') {
        $obPDF->setQuebraPaginaLista(true);
    } else {
        $obPDF->setQuebraPaginaLista(false);
    }

    $obPDF->addCabecalho ( "", 100, 8);

    $obPDF->setAlinhamento  ( "C" );
    $obPDF->addCampo ( 'campo1' , 8 ,'','','T','255,255,255');

    $rsRecordset->proximo();
    $inCount++;
}

$obPDF->show();

?>
