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
    * Arquivo paga geração de relatorio BCI
    * Data de Criação: 22/08/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCGeraRelatorioCadastroImobiliarioBoletim.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.26
*/

/*
$Log$
Revision 1.3  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMRelatorioCadastroImobiliario.class.php" );
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

set_time_limit(300000);

function filtrar_dados(&$arDadosFiltrados, $inQtdDados, $arMaxCarAtr, $arDados, $arAtributosVariaveis="")
{
    $inPosicaoFinal = 0;
    $inPosicaoInicial = 0;
    $inQuantidadePaginas = 0;

    while ($inPosicaoFinal+1 < $inQtdDados) {
        $inMaxCaracters = 0;
        for ($inY=$inPosicaoInicial; $inY<$inQtdDados; $inY++) {
            if ($arAtributosVariaveis) {
                $inMaxCaracters += $arMaxCarAtr[ $arAtributosVariaveis[$inY]["cod_atributo"] ];
            }else
                $inMaxCaracters += $arMaxCarAtr[$inY];

            $inPosicaoFinal = $inY;
            if ($inMaxCaracters > 100) {
                break;
            }
        }

        $arTmpDados = array();
        for ($inY=$inPosicaoInicial; $inY<=$inPosicaoFinal; $inY++) {
            for ($inS=0; $inS<=$inQtdDados; $inS++) {
                for ($inZ=0; $inZ<count($arDados[$inS]); $inZ++) {
                    $stTmpNome2 = "val".$inY;
                    $stTmpNome = "att".$inY;

                    if ($arDados[$inS][$stTmpNome])
                        $arTmpDados[$inS][$stTmpNome] = $arDados[$inS][$stTmpNome];

                    if ($arDados[$inS][$stTmpNome2])
                        $arTmpDados[$inS][$stTmpNome2] = $arDados[$inS][$stTmpNome2];
                }
            }
        }

        $inPosicaoInicial = $inPosicaoFinal;
        $arDadosFiltrados[$inQuantidadePaginas] = $arTmpDados;
        $inQuantidadePaginas++;
    }

    return $inQuantidadePaginas;
}

$obRCIMRelatorioCadastroImobiliario = new RCIMRelatorioCadastroImobiliario;
$obRCadastroDinamico = new RCadastroDinamico;
$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("P");

//tratando dos atributos da construcao
$obRCadastroDinamico->setCodCadastro(5);
$obRCadastroDinamico->obRModulo->setCodModulo(12);
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosConstrucao );

$arAtributosConstrucaoTitulo = array(); //guarda titulo dos atribulos utilizados
$arAtributosConstrucaoValorPadrao = array(); //valores padrao com o espaco para marcar "[  ]"
$arAtributosConstrucaoCodigo = array(); //guarda o codigo dos atributos utilizados
$arAtributosConstrucaoValorModificado = array(); //valores padrao seguidos do selecionado "(*)"
$inQtdAtributosConstrucao = 0; //quantidade de atributos existentes
$arTamMaxAtributosConstrucao = array(); //guarda a quantidade maxima de caracteres dos atributos
$arValPadAtributoConstrucao = array(); //guarda a posicao onde esta o valor padrao do atributo

while ( !$rsAtributosConstrucao->Eof() ) {
    if ($rsAtributosConstrucao->getCampo("ativo") == 't') {
        $stTmp = $rsAtributosConstrucao->getCampo("valor_padrao_desc");
        $inCodAtributo = $rsAtributosConstrucao->getCampo("cod_atributo");
        $inMaxCaracters = strlen($rsAtributosConstrucao->getCampo("nom_atributo")); //inicializando contador de max

        if ( strlen($stTmp) ) {
            $arTmp = explode("[][][]", $stTmp);
            $arTmp2 = array();
            $inTot = count( $arTmp );
            for ($inX=0; $inX<$inTot; $inX++) {
                $arTmp[$inX] = "[   ] ".$arTmp[$inX];
                $arTmp2[$inX] = str_replace( "[   ] ", "[ * ] ", $arTmp[$inX] );
                if (strlen($arTmp2[$inX]) > $inMaxCaracters) {
                    $inMaxCaracters = strlen($arTmp2[$inX]);
                }
            }

            $arAtributosConstrucaoValorPadrao[$inCodAtributo] = $arTmp;
            $arAtributosConstrucaoValorModificado[$inCodAtributo] = $arTmp2;
        }

        $arTmp = explode(",", $rsAtributosConstrucao->getCampo("valor_padrao") );
        for ($inX=0; $inX<count($arTmp); $inX++) {
            if ($arTmp[$inX])
                $arValPadAtributoConstrucao[$inCodAtributo][$arTmp[$inX]] = $inX;
        }

        $arTamMaxAtributosConstrucao[$inCodAtributo] = (($inMaxCaracters+1) / 2) + 2;
        $arAtributosConstrucaoTitulo[$inCodAtributo] = $rsAtributosConstrucao->getCampo("nom_atributo");
//        $arAtributosConstrucaoCodigo[$inQtdAtributosConstrucao] = $inCodAtributo;
//        $inQtdAtributosConstrucao++;
    }

    $rsAtributosConstrucao->proximo();
}
//---------------------------------------------------------------------------------

//tratando dos atributos do imovel
$obRCadastroDinamico->setCodCadastro(4);
$obRCadastroDinamico->obRModulo->setCodModulo(12);
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosImovel );

$arAtributosImovelTitulo = array(); //guarda titulo dos atribulos utilizados
$arAtributosImovelValorPadrao = array(); //valores padrao com o espaco para marcar "[  ]"
$arAtributosImovelCodigo = array(); //guarda o codigo dos atributos utilizados
$arAtributosImovelValorModificado = array(); //valores padrao seguidos do selecionado "(*)"
$inQtdAtributosImovel = 0; //quantidade de atributos existentes
$arTamMaxAtributosImovel = array(); //guarda a quantidade maxima de caracteres dos atributos
$arValPadAtributoImovel = array(); //guarda a posicao onde esta o valor padrao do atributo

while ( !$rsAtributosImovel->Eof() ) {
    if ($rsAtributosImovel->getCampo("ativo") == 't') {
        $stTmp = $rsAtributosImovel->getCampo("valor_padrao_desc");
        $inCodAtributo = $rsAtributosImovel->getCampo("cod_atributo");
        $inMaxCaracters = strlen($rsAtributosImovel->getCampo("nom_atributo")); //inicializando contador de max

        if ( strlen($stTmp) ) {
            $arTmp = explode("[][][]", $stTmp);
            $arTmp2 = array();
            $inTot = count( $arTmp );
            for ($inX=0; $inX<$inTot; $inX++) {
                $arTmp[$inX] = "[   ] ".$arTmp[$inX];
                $arTmp2[$inX] = str_replace( "[   ] ", "[ * ] ", $arTmp[$inX] );
                if (strlen($arTmp2[$inX]) > $inMaxCaracters) {
                    $inMaxCaracters = strlen($arTmp2[$inX]);
                }
            }

            $arAtributosImovelValorPadrao[$inQtdAtributosImovel] = $arTmp;
            $arAtributosImovelValorModificado[$inQtdAtributosImovel] = $arTmp2;
        }

        $arTmp = explode(",", $rsAtributosImovel->getCampo("valor_padrao") );
        for ($inX=0; $inX<count($arTmp); $inX++) {
            if ($arTmp[$inX])
                $arValPadAtributoImovel[$inQtdAtributosImovel][$arTmp[$inX]] = $inX;
        }

        $stTmpNome = "att".$inQtdAtributosImovel;
        $arTamMaxAtributosImovel[$inQtdAtributosImovel] = (($inMaxCaracters+1) / 2) + 2;

        $arAtributosImovelTitulo[0][$stTmpNome] = $rsAtributosImovel->getCampo("nom_atributo");
        $arAtributosImovelCodigo[$inQtdAtributosImovel] = $inCodAtributo;
        $inQtdAtributosImovel++;
    }

    $rsAtributosImovel->proximo();
}
//---------------------------------------------------------------------------------

//tratando dos atributos do lote
$obRCadastroDinamico->setCodCadastro(2);
$obRCadastroDinamico->obRModulo->setCodModulo(12);
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosLote );

$arAtributosLoteTitulo = array(); //guarda titulo dos atribulos utilizados
$arAtributosLoteValorPadrao = array(); //valores padrao com o espaco para marcar "[  ]"
$arAtributosLoteCodigo = array(); //guarda o codigo dos atributos utilizados
$arAtributosLoteValorModificado = array(); //valores padrao seguidos do selecionado "(*)"
$inQtdAtributosLote = 0; //quantidade de atributos existentes
$arTamMaxAtributosLote = array(); //guarda a quantidade maxima de caracteres dos atributos
$arValPadAtributoLote = array(); //guarda a posicao onde esta o valor padrao do atributo

while ( !$rsAtributosLote->Eof() ) {
    if ($rsAtributosLote->getCampo("ativo") == 't') {
        $stTmp = $rsAtributosLote->getCampo("valor_padrao_desc");
        $inCodAtributo = $rsAtributosLote->getCampo("cod_atributo");
        $inMaxCaracters = strlen($rsAtributosLote->getCampo("nom_atributo")); //inicializando contador de max caracteres
        if ( strlen($stTmp) ) {
            $arTmp = explode("[][][]", $stTmp);
            $arTmp2 = array();
            $inTot = count( $arTmp );
            for ($inX=0; $inX<$inTot; $inX++) {
                $arTmp[$inX] = "[   ] ".$arTmp[$inX];
                $arTmp2[$inX] = str_replace( "[   ] ", "[ * ] ", $arTmp[$inX] );
                if (strlen($arTmp2[$inX]) > $inMaxCaracters) {
                    $inMaxCaracters = strlen($arTmp2[$inX]);
                }
            }

            $arAtributosLoteValorPadrao[$inQtdAtributosLote] = $arTmp;
            $arAtributosLoteValorModificado[$inQtdAtributosLote] = $arTmp2;
        }

        $arTmp = explode(",", $rsAtributosLote->getCampo("valor_padrao") );
        for ($inX=0; $inX<count($arTmp); $inX++) {
            if ($arTmp[$inX])
                $arValPadAtributoLote[$inQtdAtributosLote][$arTmp[$inX]] = $inX;
        }

        $stTmpNome = "att".$inQtdAtributosLote;
        $arTamMaxAtributosLote[$inQtdAtributosLote] = (($inMaxCaracters+1) / 2) + 2;
        $arAtributosLoteTitulo[0][$stTmpNome] = $rsAtributosLote->getCampo("nom_atributo");
        $arAtributosLoteCodigo[$inQtdAtributosLote] = $inCodAtributo;
        $inQtdAtributosLote++;
    }

    $rsAtributosLote->proximo();
}
//----------------------------------------------------------------

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Cadastro Imobiliário:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$arDadosConfrontacoes = Sessao::read('rsConfrontacoes');
$arDados = Sessao::read('rsImoveis');
$inTotalDados = count( $arDados );
$arTitulo[0] = array ( "proprietario" => "DADOS DO PROPRIETÁRIO",
                       "promitente" => "DADOS DO PROMITENTE",
                       "dadosimovel" => "DADOS SOBRE O IMÓVEL",
                       "localimovel" => "LOCALIZAÇÃO DO IMÓVEL",
                       "confrotac" => "CONFRONTAÇÕES",
                       "caracterreno" => "CARACTERÍSTICAS DO TERRENO",
                       "caracimovel" => "CARACTERÍSTICAS DO IMÓVEL",
                       "caracedificacao" => "CARACTERÍSTICAS DA EDIFICAÇÃO",
                       "dadosconstrucao" => "DADOS SOBRE A EDIFICAÇÃO / CONTRUÇÃO"
                     );
$rsTitulo = new RecordSet;
$rsTitulo->preenche( $arTitulo );
$rsTitulo->setPrimeiroElemento();

for ($inX=0; $inX<$inTotalDados; $inX++) {
    $obPDF->addRecordSet( $rsTitulo );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "proprietario", 9, B );

    //valor atributos lote
    $obRCIMRelatorioCadastroImobiliario->setCodInicioLote( $arDados[$inX]["cod_lote"] );
    $obRCIMRelatorioCadastroImobiliario->listarCaracteristicasTerreno( $rsAtributosLote );
    $arTmp = $rsAtributosLote->getElementos();
    $arValoresSelecionadosLote = array();
    for ($inY=0; $inY<count($arTmp); $inY++) {
        $arValoresSelecionadosLote[ $arTmp[$inY]["cod_atr_din_lote"] ] = $arTmp[$inY]["valor_atr_din_lote"];
    }

    //valor atributos imovel
    $obRCIMRelatorioCadastroImobiliario->setCodInicioInscricao($arDados[$inX]["inscricao_municipal"]);
    $obRCIMRelatorioCadastroImobiliario->listarCaracteristicasImovel( $rsAtributosImovel );
    $arTmp = $rsAtributosImovel->getElementos();
    $arValoresSelecionadosImovel = array();
    for ($inY=0; $inY<count($arTmp); $inY++) {
        $arValoresSelecionadosImovel[ $arTmp[$inY]["cod_atributo"] ] = $arTmp[$inY]["valor"];
    }

    //valor atributos construcao
    $obRCIMRelatorioCadastroImobiliario->listarCaracteristicasEdificacao( $rsAtributosConstrucao, $arDados[$inX]["cod_construcao"], $arDados[$inX]["cod_tipo"] );
    $arValoresSelecionadosConstrucao = $rsAtributosConstrucao->getElementos();

    //dados do proprietario
    $arTmpDado[0] = $arDados[$inX];
    $arTmpDado[0]["cgm"] = "CGM:";
    $arTmpDado[0]["cpf"] = "CPF/CNPJ:";
    $arTmpDado[0]["rg"] = "RG/Insc. Estadual:";
    $arTmpDado[0]["nome"] = "Nome/Razão Social:";
    $arTmpDado[0]["bairro"] = "Bairro:";
    $arTmpDado[0]["lograd"] = "Logradouro:";
    $arTmpDado[0]["cota"] = "Quota:";
    $arTmpDado[0]["numero"] = "Número:";

    $arTmpDado[0]["local"] = "Localização:";

    $arTmpDado[0]["inscricao"] = "Inscrição Imobiliária:";
    $arTmpDado[0]["processo"] = "Processo:";
    $arTmpDado[0]["arealote"] = "Área Lote:";
    $arTmpDado[0]["areatotal"] = "Área total do imóvel:";
    $arTmpDado[0]["cond"] = "Condomínio:";
    $arTmpDado[0]["datainscricao"] = "Data de Inscrição:";
    $arTmpDado[0]["matriculareg"] = "Matrícula Registro de Imóveis:";
    $arTmpDado[0]["profundidade"] = "Profundidade:";
    $arTmpDado[0]["loteamento"] = "Loteamento:";
    $arTmpDado[0]["corretagem"] = "Corretagem:";

    $arTmpDado[0]["tipo"] = "Tipo:";
    $arTmpDado[0]["codigo"] = "Código da Edificação:";
    $arTmpDado[0]["dataed"] = "Data da Edificação:";
    $arTmpDado[0]["areauni"] = "Área da unidade:";

    $rsDados = new RecordSet;
    $rsDados->preenche( $arTmpDado );
    $rsDados->setPrimeiroElemento();

    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    //primeira linha
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 4, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 13, 0 );
    $obPDF->addCabecalho   ( "", 30, 0 );

    $obPDF->addCabecalho   ( "", 5, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 8, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "cgm"    , 7, B );
    $obPDF->addCampo      ( "numcgm_proprietario"    , 7 );

    $obPDF->addCampo      ( "nome"    , 7, B );
    $obPDF->addCampo      ( "nom_cgm_proprietario"    , 7 );

    $obPDF->addCampo      ( "cota"    , 7, B );
    $obPDF->addCampo      ( "cota_proprietario"  , 7 );

    $obPDF->addCampo      ( "cpf"    , 7, B );
    $obPDF->addCampo      ( "cpf_cnpj_proprietario"  , 7 );

    //segunda linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 30, 0 );

    $obPDF->addCabecalho   ( "", 7, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 5, 0 );
    $obPDF->addCabecalho   ( "", 10, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "rg", 7, B );
    $obPDF->addCampo      ( "rg_insc_estad_proprietario", 7 );

    $obPDF->addCampo      ( "lograd"    , 7, B );
    $obPDF->addCampo      ( "logradouro_proprietario"    , 7 );

    $obPDF->addCampo      ( "numero"    , 7, B );
    $obPDF->addCampo      ( "numero_proprietario"    , 7 );

    $obPDF->addCampo      ( "bairro"    , 7, B );
    $obPDF->addCampo      ( "bairro_proprietario"    , 7 );

    //promitente
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "promitente", 9, B );

    //primeira linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 4, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 13, 0 );
    $obPDF->addCabecalho   ( "", 30, 0 );

    $obPDF->addCabecalho   ( "", 5, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 8, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "cgm"    , 7, B );
    $obPDF->addCampo      ( "promitente_cgm"    , 7 );

    $obPDF->addCampo      ( "nome"    , 7, B );
    $obPDF->addCampo      ( "promitente_nome"    , 7 );

    $obPDF->addCampo      ( "cota"    , 7, B );
    $obPDF->addCampo      ( "promitente_cota"  , 7 );

    $obPDF->addCampo      ( "cpf"    , 7, B );
    $obPDF->addCampo      ( "promitente_cpf_cnpj"  , 7 );

    //segunda linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 30, 0 );

    $obPDF->addCabecalho   ( "", 7, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 5, 0 );
    $obPDF->addCabecalho   ( "", 10, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "rg", 7, B );
    $obPDF->addCampo      ( "promitente_rg_insc_estad", 7 );

    $obPDF->addCampo      ( "lograd"    , 7, B );
    $obPDF->addCampo      ( "promitente_logradouro"    , 7 );

    $obPDF->addCampo      ( "numero"    , 7, B );
    $obPDF->addCampo      ( "promitente_numero"    , 7 );

    $obPDF->addCampo      ( "bairro"    , 7, B );
    $obPDF->addCampo      ( "promitente_bairro"    , 7 );

    //localizacao do imovel
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "localimovel", 9, B );

    //primeira linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 30, 0 );

    $obPDF->addCabecalho   ( "", 7, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 5, 0 );
    $obPDF->addCabecalho   ( "", 14, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 10, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "lograd"    , 7, B );
    $obPDF->addCampo      ( "imovel_logradouro"    , 7 );

    $obPDF->addCampo      ( "numero"    , 7, B );
    $obPDF->addCampo      ( "imovel_numero"    , 7 );

    $obPDF->addCampo      ( "bairro"    , 7, B );
    $obPDF->addCampo      ( "imovel_bairro"    , 7 );

    $obPDF->addCampo      ( "local"    , 7, B );
    $obPDF->addCampo      ( "imovel_localizacao"    , 7 );

    //dados sobre o imovel
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "dadosimovel", 9, B );

    //primeira linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 12, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->addCabecalho   ( "", 7, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->addCabecalho   ( "", 20, 0 );
    $obPDF->addCabecalho   ( "", 7, 0 );

    $obPDF->addCabecalho   ( "", 7, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "inscricao"    , 7, B );
    $obPDF->addCampo      ( "inscricao_municipal"    , 7 );

    $obPDF->addCampo      ( "datainscricao"    , 7, B );
    $obPDF->addCampo      ( "data_inscricao"    , 7 );

    $obPDF->addCampo      ( "processo"    , 7, B );
    $obPDF->addCampo      ( "imovel_processo"    , 7 );

    $obPDF->addCampo      ( "matriculareg"    , 7, B );
    $obPDF->addCampo      ( "matricula_imovel"    , 7 );

    $obPDF->addCampo      ( "arealote"    , 7, B );
    $obPDF->addCampo      ( "area_lote"    , 7 );

    //segunda linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 10, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 9, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "profundidade"    , 7, B );
    $obPDF->addCampo      ( "profundidade_imovel"    , 7 );

    $obPDF->addCampo      ( "areatotal"    , 7, B );
    $obPDF->addCampo      ( "area_total_imovel"    , 7 );

    $obPDF->addCampo      ( "loteamento"    , 7, B );
    $obPDF->addCampo      ( "loteamento_imovel"    , 7 );

    $obPDF->addCampo      ( "cond"    , 7, B );
    $obPDF->addCampo      ( "condominio"    , 7 );

    $obPDF->addCampo      ( "corretagem"    , 7, B );
    $obPDF->addCampo      ( "corretagem_imovel"    , 7 );

    //confrontacoes
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "confrotac", 9, B );

    $inPosicaoLivre = $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]]["total_conf"];
    for ($inZ=0; $inZ<3; $inZ++) {
        $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]][$inPosicaoLivre+$inZ]["conf_lot_ponto_cardeal"]= "[                      ]";
        $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]][$inPosicaoLivre+$inZ]["conf_lot_metragem"] = "[                    ]";
        $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]][$inPosicaoLivre+$inZ]["conf_lot_especificar"] = "[                                                           ]";
        $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]][$inPosicaoLivre+$inZ]["conf_principal"] = "[                    ]";

        $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]][$inPosicaoLivre+$inZ]["conf_ativa"] = "[  ]";
    }

    $arTmp = $arDadosConfrontacoes[$arDados[$inX]["inscricao_municipal"]];

    $rsDadosConf = new RecordSet;
    $rsDadosConf->preenche( $arTmp );
    $rsDadosConf->setPrimeiroElemento();

    //primeira linha
    $obPDF->addRecordSet( $rsDadosConf );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "Confrontação", 12, 7, B );
    $obPDF->addCabecalho   ( "Metragem", 10, 7, B );
    $obPDF->addCabecalho   ( "Ponto Cardeal", 14, 7, B );
    $obPDF->addCabecalho   ( "Especificar (numero lote, cod_trecho, outros)", 40, 7, B );
    $obPDF->addCabecalho   ( "Desativada", 12, 7, B );

    $obPDF->addCampo      ( "conf_principal", 7 );
    $obPDF->addCampo      ( "conf_lot_metragem", 7 );
    $obPDF->addCampo      ( "conf_lot_ponto_cardeal", 7 );
    $obPDF->addCampo      ( "conf_lot_especificar", 7 );
    $obPDF->addCampo      ( "conf_ativa", 7 );

    //caracteristicas do terreno
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "caracterreno", 9, B );

    $arAtributosTexto = array();
    for ($inZ=0; $inZ<$inQtdAtributosLote; $inZ++) {
        if ( !count($arAtributosLoteValorPadrao[$inZ]) ) {
            $arAtributosTexto[] = array (
                                        "valor" => $arValoresSelecionadosLote[ $arAtributosLoteCodigo[$inZ] ],
                                        "atributo" => $arAtributosLoteTitulo[0]["att".$inZ]
                                  );

            for ($inQ=$inZ+1; $inQ<$inQtdAtributosLote+1; $inQ++) {
                $stTmpNome = "att".($inQ-1);
                $stTmpNome2 = "att".$inQ;
                $arAtributosLoteTitulo[0][$stTmpNome] = $arAtributosLoteTitulo[0][$stTmpNome2];
                $arAtributosLoteValorPadrao[$inQ-1] = $arAtributosLoteValorPadrao[$inQ];
            }

            $inQtdAtributosLote--;
            $inZ--;
        }
    }

    for ($inZ=0; $inZ<$inQtdAtributosLote; $inZ++) {
        if ( count($arAtributosLoteValorPadrao[$inZ]) ) {
            for ($inY=0; $inY<count($arAtributosLoteValorPadrao[$inZ]); $inY++) {
                $stTmpNome = "val".$inZ;
                $arAtributosLoteTitulo[$inY][$stTmpNome] = $arAtributosLoteValorPadrao[$inZ][$inY]; //colocando junto do array de titulo os valores padrao dos atributos
            }

            if ( $arValoresSelecionadosLote[ $arAtributosLoteCodigo[$inZ] ] > 0 ) //substituindo valor padrao pelo valor selecionado
                $arAtributosLoteTitulo[ $arValPadAtributoLote[$inZ][ $arValoresSelecionadosLote[ $arAtributosLoteCodigo[$inZ] ] ] ][ $stTmpNome ] = $arAtributosLoteValorModificado[ $inZ ][ $arValPadAtributoLote[$inZ][ $arValoresSelecionadosLote[ $arAtributosLoteCodigo[ $inZ ] ] ] ];
        }
    }

    $inTotalPaginas = filtrar_dados( $arDadosAtributoLote, $inQtdAtributosLote, $arTamMaxAtributosLote, $arAtributosLoteTitulo );

    $rsDadosAtributoLote = new RecordSet;
    if ($arDadosAtributoLote[0]) {
        $rsDadosAtributoLote->preenche( $arDadosAtributoLote[0] );
        $rsDadosAtributoLote->setPrimeiroElemento();
    }
    //primeira linha
    $obPDF->addRecordSet( $rsDadosAtributoLote );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );

    $inZ = 0;
    $inMaxCaracters = 0;
    for ($inY=0; $inY<$inQtdAtributosLote; $inY++) {
        $stTmpNome = "att".$inY;
        $stTmpNome2 = "val".$inY;

        $inMaxCaracters += $arTamMaxAtributosLote[$inY];
        if ( ($inMaxCaracters > 100)  && ($inZ+1 < $inTotalPaginas) ) {
            //----------------
            $obPDF->addRecordSet( $rsTitulo );
            $obPDF->setQuebraPaginaLista ( false );
            $obPDF->setAlturaCabecalho(-4);

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "", 9, 0, B );

            $obPDF->setAlinhamento( "L" );
            $obPDF->addCampo      ( "espacamento", 7, B );
            //----------------
            $inZ++;

            $rsDadosAtributoLote = new RecordSet;
            $rsDadosAtributoLote->preenche( $arDadosAtributoLote[$inZ] );
            $rsDadosAtributoLote->setPrimeiroElemento();

            $obPDF->addRecordSet( $rsDadosAtributoLote );
            $obPDF->setQuebraPaginaLista ( false );

            $inMaxCaracters = $arTamMaxAtributosLote[$inY];
        }

        $obPDF->addCabecalho  ( $arAtributosLoteTitulo[0][$stTmpNome], $arTamMaxAtributosLote[$inY], 7, B );
        $obPDF->addCampo      ( $stTmpNome2, 7 );
    }

    unset ( $rsDadosTMP );
    $rsDadosTMP = new RecordSet;
    for ( $inQ=0; $inQ<count( $arAtributosTexto ); $inQ++ ) {
        $rsDadosTMP->preenche( array( $arAtributosTexto[$inQ] ) );

        $obPDF->addRecordSet( $rsDadosTMP );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento ( "L" );

        $obPDF->addCabecalho   ( $arAtributosTexto[$inQ]["atributo"], 50, 7, B );

        $obPDF->setAlinhamento( "L" );
        $obPDF->addCampo      ( "valor", 7 );
    }

    //caracteristicas do imovel
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "caracimovel", 9, B );

    $arAtributosTexto = array();
    for ($inZ=0; $inZ<$inQtdAtributosImovel; $inZ++) {
        if ( !count($arAtributosImovelValorPadrao[$inZ]) ) {
            $arAtributosTexto[] = array (
                                        "valor" => $arValoresSelecionadosImovel[ $arAtributosImovelCodigo[$inZ] ],
                                        "atributo" => $arAtributosImovelTitulo[0]["att".$inZ]
                                  );

            for ($inQ=$inZ+1; $inQ<$inQtdAtributosImovel+1; $inQ++) {
                $stTmpNome = "att".($inQ-1);
                $stTmpNome2 = "att".$inQ;
                $arAtributosImovelTitulo[0][$stTmpNome] = $arAtributosImovelTitulo[0][$stTmpNome2];
                $arAtributosImovelValorPadrao[$inQ-1] = $arAtributosImovelValorPadrao[$inQ];
            }

            $inQtdAtributosImovel--;
            $inZ--;
        }
    }

    for ($inZ=0; $inZ<=$inQtdAtributosImovel; $inZ++) {
        if ( count($arAtributosImovelValorPadrao[$inZ]) ) {
            for ($inY=0; $inY<count($arAtributosImovelValorPadrao[$inZ]); $inY++) {
                $stTmpNome = "val".$inZ;
                $arAtributosImovelTitulo[$inY][$stTmpNome] = $arAtributosImovelValorPadrao[$inZ][$inY]; //colocando junto do array de titulo os valores padrao dos atributos
            }

            if ( $arValoresSelecionadosImovel[ $arAtributosImovelCodigo[$inZ] ] > 0 ) //substituindo valor padrao pelo valor selecionado
                $arAtributosImovelTitulo[ $arValPadAtributoImovel[$inZ][ $arValoresSelecionadosImovel[ $arAtributosImovelCodigo[$inZ] ] ] ][ $stTmpNome ] = $arAtributosImovelValorModificado[ $inZ ][ $arValPadAtributoImovel[$inZ][ $arValoresSelecionadosImovel[ $arAtributosImovelCodigo[ $inZ ] ] ] ];
        }
    }

    $inTotalPaginas = filtrar_dados( $arDadosAtributoImovel, $inQtdAtributosImovel, $arTamMaxAtributosImovel, $arAtributosImovelTitulo );

    $rsDadosAtributoImovel = new RecordSet;
    $rsDadosAtributoImovel->preenche( $arDadosAtributoImovel[0] );
    $rsDadosAtributoImovel->setPrimeiroElemento();

    //primeira linha
    $obPDF->addRecordSet( $rsDadosAtributoImovel );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlinhamento ( "L" );

    $inMaxCaracters = 0;
    $inZ = 0;
    for ($inY=0; $inY<$inQtdAtributosImovel; $inY++) {
        $stTmpNome = "att".$inY;
        $stTmpNome2 = "val".$inY;

        $inMaxCaracters += $arTamMaxAtributosImovel[$inY];
        if ( ($inMaxCaracters > 100) && ($inZ+1 < $inTotalPaginas) ) {
            //----------------
            $obPDF->addRecordSet( $rsTitulo );
            $obPDF->setQuebraPaginaLista ( false );
            $obPDF->setAlturaCabecalho(-4);

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "", 9, 0, B );

            $obPDF->setAlinhamento( "L" );
            $obPDF->addCampo      ( "espacamento", 7, B );
            //----------------
            $inZ++;

            $rsDadosAtributoImovel = new RecordSet;
            $rsDadosAtributoImovel->preenche( $arDadosAtributoImovel[$inZ] );
            $rsDadosAtributoImovel->setPrimeiroElemento();

            $obPDF->addRecordSet( $rsDadosAtributoImovel );
            $obPDF->setQuebraPaginaLista ( false );

            $inMaxCaracters = $arTamMaxAtributosImovel[$inY];
        }

        $obPDF->addCabecalho  ( $arAtributosImovelTitulo[0][$stTmpNome], ($arTamMaxAtributosImovel[$inY]+4), 7, B );
        $obPDF->addCampo      ( $stTmpNome2, 7 );
    }

    unset ( $rsDadosTMP );
    $rsDadosTMP = new RecordSet;
    for ( $inQ=0; $inQ<count( $arAtributosTexto ); $inQ++ ) {
        $rsDadosTMP->preenche( array( $arAtributosTexto[$inQ] ) );

        $obPDF->addRecordSet( $rsDadosTMP );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento ( "L" );

        $obPDF->addCabecalho   ( $arAtributosTexto[$inQ]["atributo"], 50, 7, B );

        $obPDF->setAlinhamento( "L" );
        $obPDF->addCampo      ( "valor", 7 );
    }

    //dados sobre a edificacao / construcao
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "dadosconstrucao", 9, B );

    //primeira linha
    $obPDF->addRecordSet( $rsDados );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlturaCabecalho(-4);

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCabecalho   ( "", 4, 0 );
    $obPDF->addCabecalho   ( "", 9, 0 );

    $obPDF->addCabecalho   ( "", 14, 0 );
    $obPDF->addCabecalho   ( "", 8, 0 );

    $obPDF->addCabecalho   ( "", 12, 0 );
    $obPDF->addCabecalho   ( "", 6, 0 );

    $obPDF->addCampo      ( "codigo", 7, B );
    $obPDF->addCampo      ( "cod_construcao", 7 );

    $obPDF->addCampo      ( "tipo", 7, B );
    $obPDF->addCampo      ( "tipo_vinculo", 7 );

    $obPDF->addCampo      ( "dataed", 7, B );
    $obPDF->addCampo      ( "data_construcao", 7 );

    $obPDF->addCampo      ( "areauni", 7, B );
    $obPDF->addCampo      ( "area_da_unidade", 7 );

    //caracteristicas da edificacao
    $obPDF->addRecordSet( $rsTitulo );
    $obPDF->setQuebraPaginaLista ( false );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "", 40, 9, B );

    $obPDF->setAlinhamento( "L" );
    $obPDF->addCampo      ( "", 9, B );

    $inQtdAtributosConstrucao = count($arValoresSelecionadosConstrucao);
    $arAtributosConstrucaoTituloDados = array();

    $arAtributosTexto = array();
    for ($inZ=0; $inZ<$inQtdAtributosConstrucao; $inZ++) {
        if ( !count($arAtributosConstrucaoValorPadrao[$inZ]) ) {
            $arAtributosTexto[] = array (
                                        "valor" => $arValoresSelecionadosConstrucao[ $arValoresSelecionadosConstrucao[$inZ]["cod_atributo"] ],
                                        "atributo" => $arAtributosConstrucaoTitulo[0]["att".$inZ]
                                  );

            for ($inQ=$inZ+1; $inQ<$inQtdAtributosConstrucao+1; $inQ++) {
                $stTmpNome = "att".($inQ-1);
                $stTmpNome2 = "att".$inQ;
                $arAtributosConstrucaoTitulo[0][$stTmpNome] = $arAtributosConstrucaoTitulo[0][$stTmpNome2];
                $arAtributosConstrucaoValorPadrao[$inQ-1] = $arAtributosConstrucaoValorPadrao[$inQ];
            }

            $inQtdAtributosConstrucao--;
            $inZ--;
        }
    }

    for ($inZ=0; $inZ<=$inQtdAtributosConstrucao; $inZ++) {
        $stTmpNome2 = "att".$inZ;
        $inCodAtributo = $arValoresSelecionadosConstrucao[$inZ]["cod_atributo"];
        $arAtributosConstrucaoTituloDados[0][$stTmpNome2] = $arAtributosConstrucaoTitulo[ $inCodAtributo ];

        if ( count($arAtributosConstrucaoValorPadrao[ $inCodAtributo ] ) ) {
            for ($inY=0; $inY<count( $arAtributosConstrucaoValorPadrao[ $inCodAtributo ] ); $inY++) {
                $stTmpNome = "val".$inZ;
                $arAtributosConstrucaoTituloDados[$inY][$stTmpNome] = $arAtributosConstrucaoValorPadrao[$inCodAtributo][$inY]; //colocando junto do array de titulo os valores padrao dos atributos
            }

            if ( $arValoresSelecionadosConstrucao[$inZ]["valor"] > 0 ) //substituindo valor padrao pelo valor selecionado
                $arAtributosConstrucaoTituloDados[ $arValPadAtributoConstrucao[$inCodAtributo][ $arValoresSelecionadosConstrucao[$inZ]["valor"] ] ][ $stTmpNome ] = $arAtributosConstrucaoValorModificado[ $inCodAtributo ][ $arValPadAtributoConstrucao[$inCodAtributo][ $arValoresSelecionadosConstrucao[$inZ]["valor"] ] ];
        }
    }    

    $inTotalPaginas = filtrar_dados( $arDadosAtributoConstrucao, count($arValoresSelecionadosConstrucao), $arTamMaxAtributosConstrucao, $arAtributosConstrucaoTituloDados, $arValoresSelecionadosConstrucao );

    $rsDadosAtributoConstrucao = new RecordSet;
    $rsDadosAtributoConstrucao->preenche( $arDadosAtributoConstrucao[0] );
    $rsDadosAtributoConstrucao->setPrimeiroElemento();

    //primeira linha
    $obPDF->addRecordSet( $rsDadosAtributoConstrucao );
    $obPDF->setQuebraPaginaLista ( false );
    $obPDF->setAlinhamento ( "L" );

    $inMaxCaracters = 0;
    $inZ = 0;
    for ($inY=0; $inY<count($arValoresSelecionadosConstrucao); $inY++) {
        $stTmpNome = "att".$inY;
        $stTmpNome2 = "val".$inY;
        $inCodAtributo = $arValoresSelecionadosConstrucao[$inY]["cod_atributo"];

        $inMaxCaracters += $arTamMaxAtributosConstrucao[$inCodAtributo];
        /*
        if ( ($inMaxCaracters > 100) && ( $inZ+1 < $inTotalPaginas) ) {
            //----------------
            $obPDF->addRecordSet( $rsTitulo );
            $obPDF->setQuebraPaginaLista ( false );
            $obPDF->setAlturaCabecalho(-4);

            $obPDF->setAlinhamento ( "L" );
            $obPDF->addCabecalho   ( "", 9, 0, B );

            $obPDF->setAlinhamento( "L" );
            $obPDF->addCampo      ( "espacamento", 7, B );
            //----------------

            $inZ++;

            $rsDadosAtributoConstrucao = new RecordSet;
            $rsDadosAtributoConstrucao->preenche( $arDadosAtributoConstrucao[$inZ] );
            $rsDadosAtributoConstrucao->setPrimeiroElemento();

            $obPDF->addRecordSet( $rsDadosAtributoConstrucao );
            $obPDF->setQuebraPaginaLista ( false );

            $inMaxCaracters = $arTamMaxAtributosConstrucao[$inCodAtributo];
        }
        */
            $obPDF->addCabecalho  ( $arAtributosConstrucaoTituloDados[0][$stTmpNome], ($arTamMaxAtributosConstrucao[$inCodAtributo]+5), 7, B );
            $obPDF->addCampo      ( $stTmpNome2, 7 );
    }
/*
    unset ( $rsDadosTMP );
    $rsDadosTMP = new RecordSet;
    for ( $inQ=0; $inQ<count( $arAtributosTexto ); $inQ++ ) {
        $rsDadosTMP->preenche( array( $arAtributosTexto[$inQ] ) );

        $obPDF->addRecordSet( $rsDadosTMP );
        $obPDF->setQuebraPaginaLista ( false );
        $obPDF->setAlinhamento ( "L" );

        $obPDF->addCabecalho   ( $arAtributosTexto[$inQ]["atributo"], 50, 7, B );

        $obPDF->setAlinhamento( "L" );
        $obPDF->addCampo      ( "valor", 7 );
    }
*/
}
$arFiltro = Sessao::read('filtroRelatorio');

$obPDF->addFiltro( 'Código Localização Inicial'    , $arFiltro['inCodInicioLocalizacao']   );
$obPDF->addFiltro( 'Código Localização Final'      , $arFiltro['inCodTerminoLocalizacao']  );
$obPDF->addFiltro( 'Inscrição Imobiliária Inicial' , $arFiltro['inCodInicioInscricao']     );
$obPDF->addFiltro( 'Inscrição Imobiliária Final'   , $arFiltro['inCodTerminoInscricao']    );
$obPDF->addFiltro( 'Código Logradouro Inicial'     , $arFiltro['inCodInicioLogradouro']    );
$obPDF->addFiltro( 'Código Logradouro Final'       , $arFiltro['inCodTerminoLogradouro']   );
$obPDF->addFiltro( 'Código Bairro Inicial'         , $arFiltro['inCodInicioBairro']        );
$obPDF->addFiltro( 'Código Bairro Final'           , $arFiltro['inCodTerminoBairro']       );

$obPDF->show();
?>
