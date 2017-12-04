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
    * Página de Formulario de Emissao de Documentos

    * Data de Criação   : 05/10/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMEmitirDocumento.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.03.11

*/

/*
$Log$
Revision 1.1  2007/10/09 18:48:59  cercato
 Ticket#9281#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "EmitirDocumento";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::write( 'link', "" );
$arDados = Sessao::read( 'dados' );

$inQtdDocs = count( $arDados );
foreach ($_REQUEST as $valor => $key) {
    if ( preg_match( "/boSelecionada_[0-9]/", $valor) ) {
        $arKey = explode( '§', $key );
        $arDados[$inQtdDocs] = $arKey[0]."§".$arKey[1]."§".$arKey[2]."§".$arKey[3];
        $inQtdDocs++;
    }
}

for ($inX=0; $inX<$inQtdDocs; $inX++) {
    //[num_documento]§[exercicio]§[cod_documento]§[cod_tipo_documento]
    $arKey = explode( '§', $arDados[$inX] );

    if ($arKey[2] == 1) {//certidao pos/neg
        $obTARRDocumento = new TARRDocumento;
        $stFiltro = " WHERE parcela_documento.num_documento = ".$arKey[0]." AND parcela_documento.exercicio = '".$arKey[1]."' AND documento.cod_documento = ".$arKey[2];
        $obTARRDocumento->recuperaListaCertidao( $rsDados, $stFiltro );

        if ( $rsDados->Eof() )
            $obTARRDocumento->recuperaTipoDocumento( $rsTipoDocumento, 7, $arKey[3] ); //nao existem parcelas, portanto eh negativo
        else
            $obTARRDocumento->recuperaTipoDocumento( $rsTipoDocumento, $rsDados->getCampo( "cod_doc" ), $arKey[3] );
    }

    // instantiate a TBS OOo class
    $OOParser = new clsTinyButStrongOOo;

    // setting the object
    $OOParser->SetZipBinary('zip');
    $OOParser->SetUnzipBinary('unzip');
    $OOParser->SetProcessDir('/tmp');
    $OOParser->SetDataCharset('UTF8');

    $stDocumento = '/tmp/';
    $OOParser->_process_path = $stDocumento;

    // create a new openoffice document from the template with an unique id
    if ($arKey[2] == 1) {//certidao pos/neg
        $OOParser->NewDocFromTpl( CAM_GT_ARR_MODELOS.$rsTipoDocumento->getCampo("arquivo_odt") ); //arquivo do openof
        $OOParser->LoadXmlFromDoc('content.xml');
        
        $prefeitura = Sessao::write('nom_prefeitura',trim(SistemaLegado::pegaConfiguracao('nom_prefeitura')));
        $prefeituraUp = mb_strtoupper($prefeitura, 'UTF-8');
        
        $cod_municipio = Sessao::write('cod_municipio',trim(SistemaLegado::pegaConfiguracao('cod_municipio')));
        $cod_uf = Sessao::write('cod_uf',trim(SistemaLegado::pegaConfiguracao('cod_uf')));
        
        $stFiltro = "WHERE cod_municipio=".$cod_municipio." AND cod_uf=".$cod_uf;
        $municipio = SistemaLegado::pegaDado('nom_municipio', 'sw_municipio', $stFiltro);
        
        $stFiltro = "WHERE cod_uf=".$cod_uf." AND cod_pais=1";
        $estado_sigla = SistemaLegado::pegaDado('sigla_uf', 'sw_uf', $stFiltro);
        
        $tipo_logradouro = Sessao::write('tipo_logradouro',trim(SistemaLegado::pegaConfiguracao('tipo_logradouro')));
        $logradouro = Sessao::write('logradouro',trim(SistemaLegado::pegaConfiguracao('logradouro')));
        $numero = Sessao::write('numero',trim(SistemaLegado::pegaConfiguracao('numero')));
        $endereco_prefeitura = $tipo_logradouro." ".$logradouro.", ".$numero;
        
        $cep = Sessao::write('cep',trim(SistemaLegado::pegaConfiguracao('cep')));
        for($i=0;$i<8;$i++){
            if($i!=5)
                $prefeitura_cep .= $cep[$i];
            else
                $prefeitura_cep .= "-".$cep[$i];
        }
        
        if ( ( $rsTipoDocumento->getCampo("cod_documento") == 6 ) || ( $rsTipoDocumento->getCampo("cod_documento") == 8) ) {
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDados3, Sessao::read('numCgm') );
            $arTMP3 = array();
            $arTMP3[0]["matricula"] = $rsDados3->getCampo("registro");
            $arTMP3[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');
            $arDataInscricao = explode( "/", $rsDados->getCampo("dt_emissao") );
            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTMP3[0]["dt_emissao"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];

            $OOParser->MergeBlock( 'Dat1', $rsDados->arElementos );
            $arTMP = $rsDados->arElementos;
            $arTMP2 = array();

            $stMenorVencimentoTMP = 99999999;
            $stDtMenorVencimento = "";
            for ( $inZ=0; $inZ<count( $arTMP ); $inZ++ ) {
                $arDataTMP = explode( "/", $arTMP[$inZ]["vencimento"] );
                if ($stMenorVencimentoTMP > $arDataTMP[2].$arDataTMP[1].$arDataTMP[0]) {
                    $stMenorVencimentoTMP = $arDataTMP[2].$arDataTMP[1].$arDataTMP[0];
                    $stDtMenorVencimento = $arTMP[$inZ]["vencimento"];
                }

                $arTMP2[$inZ] = $arTMP[$inZ];
                if ( $arTMP[$inZ]["cod_grupo"] )
                    $arTMP2[$inZ]["grucre"] = $arTMP[$inZ]["cod_grupo"]."/".$arTMP[$inZ]["exercicio_origem"];
                else
                    if ( $arTMP[$inZ]["cod_credito"] )
                        $arTMP2[$inZ]["grucre"] = $arTMP[$inZ]["cod_credito"];
                    else
                        $arTMP2[$inZ]["grucre"] = "DA";
            }

            $arTMP3[0]["mvencimento"] = $stDtMenorVencimento;
            $OOParser->MergeBlock( 'Dat2', $arTMP2 );
            $OOParser->MergeBlock( 'Dat3', $arTMP3 );
        } else {
            $obTDATDividaAtiva = new TDATDividaAtiva;
            $obTDATDividaAtiva->RecuperaMatriculaCGM( $rsDados3, Sessao::read('numCgm') );
            $arTMP3 = array();
            $arTMP3[0]["matricula"] = $rsDados3->getCampo("registro");
            $arTMP3[0]["cgm_testemunha"] = Sessao::read('numCgm')." - ".Sessao::read('nomCgm');
            if ($rsDados->getCampo("dt_emissao")<>'') {
                $arDataInscricao = explode( "/", $rsDados->getCampo("dt_emissao") );
            } else {
                $arDataInscricao = explode( "/", date('d/m/Y') );
            }

            $arMes = array( "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro" );
            $arTMP3[0]["dt_emissao"] = $arDataInscricao[0]." de ".$arMes[$arDataInscricao[1]]." de ".$arDataInscricao[2];

            $obTARRDocumento->recuperaCapaCertidaoNegativa( $rsDados2, $arKey[0], $arKey[1] );
            $OOParser->MergeBlock( 'Dat1', $rsDados2->arElementos );
            $OOParser->MergeBlock( 'Dat2', $arTMP3 );
        }
    }

    $OOParser->SaveXmlToDoc();

    $OOParser->LoadXmlFromDoc('styles.xml');
    $OOParser->SaveXmlToDoc();

    $arDadosArquivos[$inX]["nome_arquivo_tmp"] = $OOParser->GetPathnameDoc();
    $arDadosArquivos[$inX]["nome_arquivo"] = $rsTipoDocumento->getCampo("nome_documento");
}

Sessao::write( 'arquivos', $arDadosArquivos );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get("stCtrl")  );

$obHdnDownLoad = new Hidden;
$obHdnDownLoad->setName   ( "HdnQual" );

//DEFINICAO DO FORM
$obForm = new Form;

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.03.11" );
$obFormulario->addHidden     ( $obHdnDownLoad );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Documentos para Download" );

for ($inX=0; $inX<$inQtdDocs; $inX++) {
    $stDownLoadName = "stArq".$inX;
    $stLblDownLoadName = "stLBArq".$inX;
    $stBtnDownLoadName = "stBtnArq".$inX;

    $obLabelDownLoad = new Label;
    $obLabelDownLoad->setValue ( $arDadosArquivos[$inX]["nome_arquivo"] );
    $obLabelDownLoad->setName   ( $stLblDownLoadName );

    $obBtnDownLoad = new Button;
    $obBtnDownLoad->setName               ( $stBtnDownLoadName );
    $obBtnDownLoad->setValue              ( "Download" );
    $obBtnDownLoad->setTipo               ( "button" );
    $obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('Download','".$inX."')" );
    $obBtnDownLoad->setDisabled           ( false );

    $obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );
}

$obFormulario->show();
