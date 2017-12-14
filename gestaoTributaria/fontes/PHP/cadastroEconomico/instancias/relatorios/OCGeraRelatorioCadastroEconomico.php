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
    * Data de Criação   : 27/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCGeraRelatorioCadastroEconomico.php 65763 2016-06-16 17:31:43Z evandro $

    *Casos de uso: uc-05.02.17

*/

/*
$Log$
Revision 1.6  2007/01/30 11:36:51  dibueno
Bug #8042#

Revision 1.5  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Cadastro Econômico - Relatórios"   );
$obPDF->setTitulo            ( "Atividades" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arSessaoTransf7 = Sessao::read( "sessao_transf7" );
$rsResultadosEntidades = $arSessaoTransf7->arElementos[0]['dados'];

$arSessaoTransf6 = Sessao::read( "sessao_transf6" );
$rsResultados = new RecordSet;
$rsResultados = $arSessaoTransf6->arElementos;

$inContRg = $arSessaoTransf7->getNumLinhas();
$inCont = 0;

$arFiltroSessao = Sessao::read( "filtroRelatorio" );
$rsResultadosEntidades->setPrimeiroElemento();

if ( $rsResultadosEntidades->getNumLinhas() > 0 ) {

    while ( !$rsResultadosEntidades->eof() ) {

        //INSCRICAO
        unset ($rsInscricao );
        $rsInscricao = new RecordSet;

        $rsInscricao->preenche( $rsResultadosEntidades->getObjeto());
        $rsInscricao->setPrimeiroElemento();

        if ($arFiltroSessao['stSituacao'] == 'Todos' || $rsInscricao->arElementos['situacao_cadastro'] == $arFiltroSessao['stSituacao']) {

            $arInscricao = array();

            $arInscricao[] = array(
                        "labelA"=> "Entidade:",
                        "labelB"=> $rsInscricao->arElementos['inscricao_economica']. ' - ' . $rsInscricao->arElementos['nome'] ,
                        "labelC"=> "Categoria:",
                        "labelD"=> $rsInscricao->arElementos['nom_categoria']
            );
            $arInscricao[] = array(
                        "labelA"=> "Início:",
                        "labelB"=> $rsInscricao->arElementos['inicio_br'] ,
                        "labelC"=> "CPF / CNPJ:",
                        "labelD"=> $rsInscricao->arElementos['cpf']. '' . $rsInscricao->arElementos['cnpj']
            );
            $arInscricao[] = array(
                        "labelA"=> "Resp. Contábil:",
                        "labelB"=> $rsInscricao->arElementos['cgm_contador'].' '. $rsInscricao->arElementos['nom_contador'] ,
                        "labelC"=> "Tipo Inscrição:",
                        "labelD"=> $rsInscricao->arElementos['tipoempresa']
            );
            $arInscricao[] = array(
                        "labelA"=> "Domicílio Fiscal:",
                        "labelB"=> $rsInscricao->arElementos['endereco'] ,
                        "labelC"=> "Situação:",
                        "labelD"=> $rsInscricao->arElementos['situacao_cadastro']
            );

            $rsInscricaoDados = new RecordSet;
            $rsInscricaoDados->preenche ( $arInscricao );
            $rsInscricaoDados->setPrimeiroElemento() ;
            $obPDF->addRecordSet( $rsInscricaoDados );
            //$obPDF->setQuebraPaginaLista( false );
            if ( $inCont > 0) //$obPDF->setQuebraPaginaLista( true );
                $obPDF->setQuebraPaginaLista( false );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( ""  , 10, 10  );
            $obPDF->addCabecalho   ( ""  , 30, 10 );
            $obPDF->addCabecalho   ( ""  , 10, 10  );
            $obPDF->addCabecalho   ( ""  , 30, 10 );

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "labelA"  , 9, "B"  );
            $obPDF->addCampo       ( "labelB"  , 8 );
            $obPDF->setAlinhamento ( "R" );
            $obPDF->addCampo       ( "labelC"  , 9, "B"  );
            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCampo       ( "labelD"  , 8 );

            /** ATRIBUTOS DINAMICOS *///////////////---------------------------------------------------------------------
            if ($arFiltroSessao['stTipoRelatorio'] == 'analitico') {
                $rsAtributos_TMP = new RecordSet;
                //$rsAtributos_TMP = $sessao->transf5[3];
                $rsAtributos_TMP =  $rsResultados[$inCont]['atributos'];

                if ( $rsAtributos_TMP->getNumLinhas() > 0 ) {

                    $arAtributos = array();
                    while ( !$rsAtributos_TMP->eof() && $valorA = $rsAtributos_TMP->getCampo('valor') != '' ) {

                        $labelA = $rsAtributos_TMP->getCampo('nom_atributo');
                        $valorA = $rsAtributos_TMP->getCampo('valor');
                        $rsAtributos_TMP->proximo();

                        $arAtributos[ ] = array (
                            "labelA" => $labelA,
                            "valorA" => $valorA,
                            "labelB" => $rsAtributos_TMP->getCampo('nom_atributo'),
                            "valorB" => $rsAtributos_TMP->getCampo('valor')
                        );
                        $rsAtributos_TMP->proximo();

                    }

                    $arTitulo = array ();
                    $arTitulo[0]['label'] = "CARACTERÍSTICAS";
                    $rsTitulo = new RecordSet;
                    $rsTitulo->preenche ( $arTitulo );
                    $obPDF->addRecordSet( $rsTitulo );
                    $obPDF->setQuebraPaginaLista( false );
                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCabecalho   ( ""     ,30, 5  );
                    $obPDF->addCampo       ( "label"  ,11, "B");

                    $rsAtributos = new RecordSet;
                    $rsAtributos->preenche ( $arAtributos );
                    $obPDF->addRecordSet( $rsAtributos );
                    $obPDF->setQuebraPaginaLista( false );

                    $obPDF->setAlinhamento ( "C" );
                    $obPDF->addCabecalho   ( "  "     ,20, 5  );
                    $obPDF->addCabecalho   ( "  "     ,30, 5  );
                    $obPDF->addCabecalho   ( "  "     ,20, 5 );
                    $obPDF->addCabecalho   ( "  "     ,30, 5  );
                    $obPDF->setAlinhamento ( "R" );
                    $obPDF->addCampo       ( "[labelA]:"  , 9 ,"B" );
                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCampo       ( "[valorA]"  , 9 );
                    $obPDF->setAlinhamento ( "R" );
                    $obPDF->addCampo       ( "[labelB]:"  , 9 , "B" );
                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCampo       ( "[valorB]"  , 9 );
                }
            }
            /** ATRIBUTOS DINAMICOS *///////////////---------------------------------------------------------------------

                //SOCIOS
                if ($arFiltroSessao['stTipoInscricao'] == 'direito') {
                    if ( !$rsResultados[$inCont]['socios'] /*|| $rsSociedade->getNumLinhas() < 1*/ ) {
                        $arSocios = array ();
                        $arSocios[0]['socio'] = "nenhum registro encontrado";
                        $rsSociedade->preenche ( $arSocios );
                    } else {
                        $rsSociedade = $rsResultados[$inCont]['socios'];
                    }
                    $arSocios = array ();
                    $arSocios[0]['label'] = "LISTA DE SÓCIOS";
                    $rsSocios = new RecordSet;
                    $rsSocios->preenche ( $arSocios );
                    $obPDF->addRecordSet( $rsSocios );
                    $obPDF->setQuebraPaginaLista( false );
                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCabecalho   ( ""     ,30, 5  );
                    $obPDF->addCampo       ( "label"  ,11, "B");

                    $obPDF->addRecordSet ( $rsSociedade );
                    $obPDF->setQuebraPaginaLista( false );

                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCabecalho   ( "CONTRIBUINTE"     ,40, 10  );
                    $obPDF->addCabecalho   ( "QUOTA"  ,10, 10 );
                    $obPDF->setAlinhamento ( "L" );
                    $obPDF->addCampo       ( "[socio]"  , 9 );
                    $obPDF->setAlinhamento ( "C" );
                    $obPDF->addCampo       ( "[quota_socio]"  , 9 );
                }

            //ATIVIDADES
            if ($arFiltroSessao['stTipoRelatorio'] == 'analitico') {
                $arAtividades = array ();
                $arAtividades[0]['label'] = "LISTA DE ATIVIDADES";
                $rsAtividades = new RecordSet;
                $rsAtividades->preenche ( $arAtividades );
                $obPDF->addRecordSet( $rsAtividades );
                $obPDF->setQuebraPaginaLista( false );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCabecalho   ( ""     ,30, 5  );
                $obPDF->addCampo       ( "label"  ,11, "B" );

                $obPDF->addRecordSet( $rsResultados[$inCont]['atividades'] );
                $obPDF->setQuebraPaginaLista( false );

                $obPDF->setAlinhamento ( "C" );
                $obPDF->addCabecalho   ( "INÍCIO"         , 7 , 8 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCabecalho   ( "ATIVIDADE"      , 22, 8 );
                $obPDF->addCabecalho   ( "MODALIDADE"     , 10, 8 );
                $obPDF->addCabecalho   ( "ALIQUOTA"       , 7 , 8 );
                $obPDF->addCabecalho   ( "SERVIÇO"        , 26, 8 );
                $obPDF->addCabecalho   ( "ALIQUOTA"       , 8 , 8 );
                $obPDF->addCabecalho   ( "Nº LICENÇA"     , 10, 8 );
                $obPDF->addCabecalho   ( "SITUAÇÃO ALVARÁ", 13, 8 );

                $obPDF->setAlinhamento ( "C" );
                $obPDF->addCampo       ( "[dt_inicio]", 8 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo       ( "[cod_estrutural] - [nom_atividade]", 8 );
                $obPDF->addCampo       ( "[modalidade]", 8 );
                $obPDF->setAlinhamento ( "C" );
                $obPDF->addCampo       ( "[aliquota_atividade]", 8 );
                $obPDF->setAlinhamento ( "L" );
                $obPDF->addCampo       ( "[cod_estrutural_servico] - [nom_servico]", 8 );
                $obPDF->setAlinhamento ( "C" );
                $obPDF->addCampo       ( "[aliquota_servico]", 8 );
                $obPDF->addCampo       ( "[cod_licenca]/[exercicio]", 8 );
                $obPDF->addCampo       ( "[situacao]", 8 );
            }

            $inCont++;
        }
        $rsResultadosEntidades->proximo();

    }

} else {

        $arInscricao[] = array(
                    "labelA"=> "Nenhum registro encontrado!",
                    "labelB"=> "" ,
                    "labelC"=> "",
                    "labelD"=> ""
        );

        $rsInscricaoDados = new RecordSet;
        $rsInscricaoDados->preenche ( $arInscricao );
        $obPDF->addRecordSet( $rsInscricaoDados );
        $obPDF->setQuebraPaginaLista( true );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCabecalho   ( ""  , 50, 10, "B" );
        $obPDF->addCabecalho   ( ""  , 30, 10 );
        $obPDF->addCabecalho   ( ""  , 10, 10  );
        $obPDF->addCabecalho   ( ""  , 30, 10 );

        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "labelA"  , 10, "B"  );
        $obPDF->addCampo       ( "labelB"  , 8 );
        $obPDF->setAlinhamento ( "R" );
        $obPDF->addCampo       ( "labelC"  , 9, "B"  );
        $obPDF->setAlinhamento ( "L" );
        $obPDF->addCampo       ( "labelD"  , 8 );

}

    //$obPDF->addQuebraPagina( "pagina" , 1 );
    if ( $arFiltroSessao['inNumInscricaoEconomicaInicial'] && $arFiltroSessao['inNumInscricaoEconomicaFinal'] )
        $obPDF->addFiltro( 'Intervalo de Inscrição Econômica' , $arFiltroSessao['inNumInscricaoEconomicaInicial'].' até '.$arFiltroSessao['inNumInscricaoEconomicaFinal']);
    if ( $arFiltroSessao['inNumInscricaoEconomicaInicial'] && !$arFiltroSessao['inNumInscricaoEconomicaFinal'] )
        $obPDF->addFiltro( 'Inscrição Econômica'   , $arFiltroSessao['inNumInscricaoEconomicaInicial'] );
    if ( !$arFiltroSessao['inNumInscricaoEconomicaInicial'] && $arFiltroSessao['inNumInscricaoEconomicaFinal'])
        $obPDF->addFiltro( 'Inscrição Econômica'   , $arFiltroSessao['inNumInscricaoEconomicaFinal'] );

    if ( $arFiltroSessao['inCodInicioLogradouro'] && $arFiltroSessao['inCodTerminoLogradouro'] )
        $obPDF->addFiltro( 'Intervalo de Logradouro', $arFiltroSessao['inCodInicioLogradouro'].' até '.$arFiltroSessao['inCodTerminoLogradouro']);
    if ( $arFiltroSessao['inCodInicioLogradouro'] && !$arFiltroSessao['inCodTerminoLogradouro'] )
        $obPDF->addFiltro( 'Logradouro', $arFiltroSessao['inCodInicioLogradouro'] );
    if ( !$arFiltroSessao['inCodInicioLogradouro'] && $arFiltroSessao['inCodTerminoLogradouro'])
        $obPDF->addFiltro( 'Logradouro', $arFiltroSessao['inCodTerminoLogradouro'] );

    $obPDF->addFiltro( 'CGM Sócio'                  , $arFiltroSessao['inCodSocio']  );
    $obPDF->addFiltro( 'Atividade Inicial'            , $arFiltroSessao['inCodInicio']          );
    $obPDF->addFiltro( 'Atividade Final'             , $arFiltroSessao['inCodTermino']          );

    $obPDF->addFiltro( 'Licença Inicial'            , $arFiltroSessao['stLicencaInicio']          );
    $obPDF->addFiltro( 'Licença Final'             , $arFiltroSessao['stLicencaFim']          );

    $obPDF->addFiltro( 'Data Inicial'                   , $arFiltroSessao['dtInicio'] );
    $obPDF->addFiltro( 'Tipo da Inscrição'         , $arFiltroSessao['stTipoInscricao'] );
    $obPDF->addFiltro ('Tipo de Relatório'          ,  $arFiltroSessao['stTipoRelatorio'] );

$obPDF->show();
?>
