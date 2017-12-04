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
  * Regra TFF para Carne Mata de Sao Joao
  * Data de criação : 12/12/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM

    * $Id: RTFFMataSaoJoao.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.8  2007/03/15 15:08:25  cercato
alterando frase dos carnes de unica pra nao receber apos vencimento.

Revision 1.7  2007/03/05 18:59:58  cercato
retirando mensagem de nao receber apos vencimento.

Revision 1.6  2007/02/15 15:04:48  cercato
setando observacao nos carnes.

Revision 1.5  2007/01/26 15:31:27  cercato
correcao do endereco para funcionar com a PL nova de enderecos.

Revision 1.4  2007/01/23 15:35:48  cercato
deixando o exercicio dinamico e corrigindo preenximento da primeira parcela da capa.

Revision 1.3  2007/01/19 18:23:11  cercato
adicionando na capa TFF a inscricao imobiliaria.

Revision 1.2  2007/01/19 17:38:20  cercato
alteracao em funcao da pl de enderecos que mudou.

Revision 1.1  2006/12/21 17:04:01  cercato
*** empty log message ***

*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RProtocoloPetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarnePetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarneDiversosPetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarneDadosTFFMataSaoJoao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class RTFFMataSaoJoao
{
/*
    * @var Integer
    * @access Private
*/
var $inHorizontal;
/*
    * @var Integer
    * @access Private
*/
var $inVertical;
/*
    * @var Array
    * @access Private
*/
var $arEmissao;
/*
    * @var Object
    * @access Private
*/
var $obBarra;
/*
    * @var Array
    * @access Private
*/
var $arBarra;
/*
    * @var Boolean;
    * @access Private
*/
var $boPulaPagina;
/*
    * @var Object
    * @access Private
*/
var $obRARRCarne;
var $obRCarneDadosCadastrais;
var $stLocal;

/* setters */
function setHorizontal($valor) { $this->inHorizontal = $valor; }
function setVertical($valor) { $this->inVertical   = $valor; }
function setEmissao($valor) { $this->arEmissao    = $valor; }
function setBarra($valor) { $this->obBarra      = $valor; }
function setArBarra($valor) { $this->arBarra      = $valor; }
function setPulaPagina($valor) { $this->boPulaPagina = $valor; }

/* getters */
function getHorizontal() { return $this->inHorizontal;   }
function getVertical() { return $this->inVertical;     }
function getEmissao() { return $this->arEmissao;      }
function getBarra() { return $this->obBarra;        }
function getArBarra() { return $this->arBarra;        }
function getPulaPagina() { return $this->boPulaPagina;   }

/*
    * Metodo Construtor
    * @access Private
*/
function RTFFMataSaoJoao($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    //$obRProtocoloPetropolis = new RProtocoloPetropolis;
    //$obRCarnePetropolis     = new RCarnePetropolis;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    //---------------------
    $this->obRARRConfiguracao     = new RARRConfiguracao;
    $this->obRARRConfiguracao->setCodModulo ( 2 );
    $this->obRARRConfiguracao->consultar();
    $inCodFebraban = $this->obRARRConfiguracao->getCodFebraban();
    unset($this->obRARRConfiguracao);

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );
    $stNomeImagem = $rsListaImagens->getCampo("valor");

    $inSaltaPagina = "";
    $this->obRCarnePetropolis = new RCarneDadosTFFMataSaoJoao();
    $this->obRCarnePetropolis->stCamLogo = CAM_FW_TEMAS."imagens/".$stNomeImagem;
    $this->obRCarnePetropolis->lblTitulo1 = "MATA DE SÃO JOÃO - Sec. de Adm. e Fin.";

    foreach ($this->arEmissao as $valor => $chave) {
        /* imprimir duas folhas com dados cadastrais */
        /* buscar informações para dados cadastrais*/

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
        $stFiltro = " AND ece.inscricao_economica = ". $chave[0]['inscricao'] ." AND ac.exercicio = ".$chave[0]['exercicio']." \n";

$rsListaCarne = new RecordSet;

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosTFFMata( $rsListaCarne, $stFiltro, $chave[0]['cod_parcela'] );

        $rsListaCarne->addFormatacao ('valor','NUMERIC_BR');

        $arDadosParcelas = array();
        $inTotalParcelas = 0;
        if ( !$rsListaCarne->Eof() ) {
            $inCodCalculo = $rsListaCarne->getCampo("cod_calculo");
            while ( !$rsListaCarne->Eof() ) {
                $arDadosParcelas[$rsListaCarne->getCampo("nr_parcela")]["data"] = $rsListaCarne->getCampo("vencimento");
                $arDadosParcelas[$rsListaCarne->getCampo("nr_parcela")]["valor"] = $rsListaCarne->getCampo("valor");
                $inTotalParcelas++;
//                if ($inTotalParcelas > 4) {
  //                  break;
    //            }

                $rsListaCarne->proximo();
            }
        }

        $rsListaCarne->setPrimeiroElemento();

        $this->obRCarnePetropolis->stParcelaUnica = $arDadosParcelas[0]["data"].' R$ '.$arDadosParcelas[0]["valor"]; //'05/02/2007  R$ 140,87';
        $this->obRCarnePetropolis->stDescontoParcelaUnica = '5,00';
        $this->obRCarnePetropolis->stParcelaUm = $arDadosParcelas[1]["data"].' R$ '.$arDadosParcelas[1]["valor"]; //'05/02/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaDois = $arDadosParcelas[2]["data"].' R$ '.$arDadosParcelas[2]["valor"]; //'05/03/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaTres = $arDadosParcelas[3]["data"].'  R$ '.$arDadosParcelas[3]["valor"]; //'05/04/2007  R$ 140,87';

        $this->obRCarnePetropolis->stInscricaoMunicipal = $rsListaCarne->getCampo("inscricao_municipal");
        $this->obRCarnePetropolis->stInscricaoEconomica = $rsListaCarne->getCampo("inscricao_economica"); //'540857';
        $this->obRCarnePetropolis->stRazaoSocial = $rsListaCarne->getCampo("razao_social"); //'JOSE DIONIZIO DOS SANTOS';
        $this->obRCarnePetropolis->stNomeFantasia = $rsListaCarne->getCampo("nome_fantasia"); //'DIONIZIO MATERIAIS DE CONSTRUCAO';
        $this->obRCarnePetropolis->stCNPJ = $rsListaCarne->getCampo("cpf_cnpj"); //'09.018.561/0001-80';
        $this->obRCarnePetropolis->stAtividade = $rsListaCarne->getCampo("atividade"); //'Ajudante de pedreiro';
        $this->obRCarnePetropolis->stResponsavel = $rsListaCarne->getCampo("resposavel"); //'Capitao Jonas Vasconcelus';

        /* setar todos os dados necessarios */
        $this->obRCarnePetropolis->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->obRCarnePetropolis->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->obRCarnePetropolis->stExercicio  = Sessao::getExercicio();

        $arEndereco = explode ( "§", $rsListaCarne->getCampo("endereco") );

        $this->obRCarnePetropolis->stCodigoLogradouro  = '50.003' ;

        $this->obRCarnePetropolis->stNomeLogradouro  = $arEndereco[0].' '.$arEndereco[1].' '.$arEndereco[3]; //'AV DO FAROL 50.003' ;
        $this->obRCarnePetropolis->stComplemento  = $arEndereco[4]; //'CONDOMINIO SOLAR DOS ARCOS' ;
        $this->obRCarnePetropolis->stQuadra  = ''; //nao usar //'02' ;
        $this->obRCarnePetropolis->stLote  = $rsListaCarne->getCampo("nro_lote"); //'02' ;
        $this->obRCarnePetropolis->stDistrito  = $rsListaCarne->getCampo("distrito"); //'PRAIA DO FORTE' ;
        $this->obRCarnePetropolis->stRegiao  = $rsListaCarne->getCampo("regiao"); //'LITORAL' ;
        $this->obRCarnePetropolis->stCep  = $arEndereco[6]; //'48.820-000' ;
        $this->obRCarnePetropolis->stCidade  = 'MATA DE SÃO JOÃO' ;
        $this->obRCarnePetropolis->stEstado  = 'BAHIA' ;

        $this->obRCarnePetropolis->desenhaCarne(10,40);
        $this->obRCarnePetropolis->novaPagina();
        $inSaltaPagina++;

        $this->obRCarnePetropolis->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarnePetropolis->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {
            // montagem cabecalho (protocolo)
            $this->obRCarnePetropolis->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarnePetropolis->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarnePetropolis->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarnePetropolis->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarnePetropolis->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );

            $arEndereco = explode( '§', $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) );

            $this->obRCarnePetropolis->setRua               ( $arEndereco[0]." ".$arEndereco[2]  );

            $this->obRCarnePetropolis->setNumero            ( $arEndereco[3] );
            $this->obRCarnePetropolis->setComplemento       ( $arEndereco[4] );
            $this->obRCarnePetropolis->setCidade            ( $arEndereco[8] );
            $this->obRCarnePetropolis->setUf                ( $arEndereco[10] );
            $this->obRCarnePetropolis->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
            $this->obRCarnePetropolis->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarnePetropolis->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarnePetropolis->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarnePetropolis->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarnePetropolis->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarnePetropolis->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarnePetropolis->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            $this->obRCarnePetropolis->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' )              );
            $this->obRCarnePetropolis->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
            $this->obRCarnePetropolis->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
            $this->obRCarnePetropolis->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarnePetropolis->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            if ( preg_match( '/LIMPEZA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } else {
                $flImpostoAnualReal = $rsGeraCarneCabecalho->getCampo( 'valor' );
                $this->obRCarnePetropolis->setImpostoAnualReal  ( $flImpostoAnualReal                                     );
            }
            $this->obRCarnePetropolis->setReferencia        ( ""                                                          );
            $this->obRCarnePetropolis->setNumeroPlanta      ( ""                                                          );

            // capturar creditos
            $this->obRCarnePetropolis->setObservacaoL1 ( $this->obRCarnePetropolis->getObservacaoL1().$rsGeraCarneCabecalho->getCampo( 'descricao_credito').": ".$rsGeraCarneCabecalho->getCampo( 'valor' )."  ");

            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        $this->obRCarnePetropolis->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarnePetropolis->getTaxaLimpezaAnual() );
        // formatar
        $this->obRCarnePetropolis->setValorAnualReal    ( number_format($this->obRCarnePetropolis->getValorAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( number_format($this->obRCarnePetropolis->getTaxaLimpezaAnual(),2,',','.') );
        $this->obRCarnePetropolis->setImpostoAnualReal  ( number_format($this->obRCarnePetropolis->getImpostoAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setValorTributoReal  ( number_format($this->obRCarnePetropolis->getValorTributoReal(),2,',','.') );

        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 20;

        $this->obBarra = new RCodigoBarraFebraban;
        $this->arBarra = array();
        foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
            $inParcela++;

            $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" ); //imagem mudar
            $this->obRCarnePetropolis->setImagem("");
            $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
            $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

            // instanciar mapeamento da função de calculo de juro e multa
            require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
            require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
            // retorna parcela com juro e multa aplicados
            $obCalculaParcelas = new FARRCalculaParcelasReemissao;
            // retorna valores de juro e multa que foram aplicados
            $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;

            // data da reemissao
            $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
            $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

            // parametro padrao
            $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
            $stParametro .= ",".$parcela["cod_parcela"].",'";

            // monta paramentros com as datas
            $stParametro1 = $stParametro.$dtVencimento."'";

            // valor atualizado
            $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
            $nuValorNormal = $rsTmp->getCampo('valor');

            //$this->arBarra['valor_documento'] = $rsParcela->getCampo( 'valor' );
            $this->arBarra['valor_documento'] = $nuValorNormal;
            $this->arBarra['vencimento'] = (string) $rsParcela->getCampo( 'vencimento' );
            $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
            $this->obRCarnePetropolis->setNumeracao( (string) $rsParcela->getCampo( 'numeracao' ) );
            $this->arBarra['cod_febraban'] = $inCodFebraban;

            if ( $obErro->ocorreu() ) {
                break;
            }
            if ($diffBaixa) {
                    $this->arBarra['tipo_moeda'] = 6;
                    $this->obRCarnePetropolis->setParcelaUnica ( true );
                    $this->obRCarnePetropolis->lblTitulo2        = ' ';
                    $this->obRCarnePetropolis->lblValorCotaUnica = 'VALOR TOTAL';
                    $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
//                    $this->obRCarnePetropolis->setObservacaoL1 ( 'Não receber após o vencimento. ' );
//                    $this->obRCarnePetropolis->setObservacaoL2 ( ' ' );
//                    $this->obRCarnePetropolis->setObservacaoL3 ( ' ' );
                    $this->obRCarnePetropolis->setParcela ( $rsParcela->getCampo( 'info' ) );
                    //$this->obRCarnePetropolis->stObsVencimento = "Não receber após o vencimento.";
                    $this->obRCarnePetropolis->stObsVencimento = "";
            } else {
                if ( $rsParcela->getCampo( 'nr_parcela' ) == 0 ) {
                    $this->arBarra['tipo_moeda'] = 6;
                    $this->obRCarnePetropolis->setParcelaUnica ( true );
                    $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    //$this->obRCarnePetropolis->setValor        ( number_format($rsParcela->getCampo( 'valor' ),2,',','') );
                    $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
                    // Recuperar Desconto

                    include_once(CAM_GT_ARR_MAPEAMENTO."FARRParcentualDescontoParcela.class.php");
                    $obPercentual = new FARRParcentualDescontoParcela;
                    $obPercentual->executaFuncao($rsPercentual,"".$parcela["cod_parcela"].",'".$dtVencimento."'");
                    $this->obRCarnePetropolis->setObservacaoL1 ( 'Cota Única com '.$rsPercentual->getCampo('valor').'% de desconto.' );                      ;
                    $this->obRCarnePetropolis->setObservacaoL2 ( 'Desconto não incide sobre a Taxa de Coleta de Lixo' );
                    $this->obRCarnePetropolis->setObservacaoL3 ( 'Não receber após o vencimento.' );
                    $this->obRCarnePetropolis->setParcela ( 'ÚNICA' );
                    $this->obRCarnePetropolis->stObsVencimento = "Não receber após o vencimento.";
                } else {
                    $this->arBarra['tipo_moeda'] = 7;
                    //$arVencimentos = geraParcelas($rsParcela->getCampo( 'vencimento' ),count($chave));
                    $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                    //$this->obRCarnePetropolis->setParcela( $inParcela.'/'.count($chave) );
                    $this->obRCarnePetropolis->setParcela( $rsParcela->getCampo( 'info' ));
//                    $this->obRCarnePetropolis->setObservacaoL1( 'Após os vencimentos previstos nesta guia, retirar 2ª via na' );
//                    $this->obRCarnePetropolis->setObservacaoL2( 'Secretaria de Fazenda' );
//                    $this->obRCarnePetropolis->setObservacaoL3( ' ' );
                    $this->obRCarnePetropolis->setParcelaUnica( false );
                    $this->obRCarnePetropolis->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );

                    $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                    $boVenc1 = false;
                    $boVenc2 = false;
                    $boVenc3 = false;

                    if ($this->stLocal != "WEB") {
                        $stMes = $arTmp[1];
                        $arTmp = explode('/',$arVencimentos[0]);
                        if ($arTmp[1] >= $stMes) {
                            $stMes = $arTmp[1];
                            $boVenc1 = true;
                            $this->obRCarnePetropolis->setVencimento1 ( $arVencimentos[0] );
                            $arTmp = explode('/',$arVencimentos[1]);
                            if ($arTmp[1] >= $stMes) {
                                $stMes = $arTmp[1];
                                $boVenc2 = true;
                                $this->obRCarnePetropolis->setVencimento2 ( $arVencimentos[1] );
                                $arTmp = explode('/',$arVencimentos[2]);
                                if ($arTmp[1] >= $stMes) {
                                    $boVenc3 = true;
                                    $this->obRCarnePetropolis->setVencimento3 ( $arVencimentos[2] );
                                }
                            }
                        }
                        // converter vencimentos para formato americano

                        $arTmp = explode('/',$arVencimentos[0]);
                        $dtVencimento1 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                        $arTmp = explode('/',$arVencimentos[1]);
                        $dtVencimento2 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                        $arTmp = explode('/',$arVencimentos[2]);
                        $dtVencimento3 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                        $stParametro2 = $stParametro.$dtVencimento1."'";
                        $stParametro3 = $stParametro.$dtVencimento2."'";
                        $stParametro4 = $stParametro.$dtVencimento3."'";

                        // valor, % de juro, % de multa para valor normal do carne --------------
                        // valor
                        // % de juro
                        $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'j'");
                        $stJuroNormal = $rsTmp->getCampo('valor');
                        $this->obRCarnePetropolis->lblJuros1 = $stJuroNormal;
                        // % de multa
                        $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'m'");
                        $stMultaNormal = $rsTmp->getCampo('valor');
                        $this->obRCarnePetropolis->lblMulta1 = $stMultaNormal;
                        //-----------------------------------------------------------------------

                        // valor, % de juro, % de multa para valor vencimento 1 do carne --------------
                        // valor
                        if ($boVenc1 == true) {
                            $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro2);
                            $nuValor1 = $rsTmp->getCampo('valor');
                            // % de juro
                            $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'j'");
                            $stJuro1 = $rsTmp->getCampo('valor');
                            $this->obRCarnePetropolis->lblJuros2 = $stJuro1;
                            // % de multa
                            $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'m'");

                            $stMulta1 = $rsTmp->getCampo('valor');
                            $this->obRCarnePetropolis->lblMulta2 = $stMulta1;
                        } else {
                            $this->obRCarnePetropolis->lblJuros2 = "";
                            $this->obRCarnePetropolis->lblMulta2 = "";
                        }
                        //-----------------------------------------------------------------------

                        // valor, % de juro, % de multa para valor vencimento 2 do carne --------------
                        // valor
                        if ($boVenc2 == true) {
                            $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro3);
                            $nuValor2 = $rsTmp1->getCampo('valor');
                            // % de juro
                            $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro3.",'j'");

                            $stJuro2 = $rsTmp2->getCampo('valor');

                            $this->obRCarnePetropolis->lblJuros3 = $stJuro2;

                            // % de multa
                            $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro3.",'m'");
                            $stMulta2 = $rsTmp3->getCampo('valor');
                            $this->obRCarnePetropolis->lblMulta3 = $stMulta2;
                        } else {
                            $this->obRCarnePetropolis->lblJuros3 = "";
                            $this->obRCarnePetropolis->lblMulta3 = "";
                        }
                        //-----------------------------------------------------------------------

                        // valor, % de juro, % de multa para valor vencimento 3 do carne --------------
                        // valor
                        if ($boVenc3 == true) {
                            $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro4);
                            $nuValor3 = $rsTmp1->getCampo('valor');
                            // % de juro
                            $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro4.",'j'");

                            $stJuro3 = $rsTmp2->getCampo('valor');
                            $this->obRCarnePetropolis->lblJuros4 = $stJuro3;
                            // % de multa
                            $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro4.",'m'");
                            $stMulta3 = $rsTmp3->getCampo('valor');

                            $this->obRCarnePetropolis->lblMulta4 = $stMulta3;
                        } else {
                            $this->obRCarnePetropolis->lblJuros4 = "";
                            $this->obRCarnePetropolis->lblMulta4 = "";
                        }
                        //-----------------------------------------------------------------------


                        // repassa valores para pdf
                        $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                        if ($boVenc1 == true) {
                            $this->obRCarnePetropolis->setValor1      (number_format(round($nuValor1,2),2,',','.')) ;
                            if ($boVenc2 == true) {
                                $this->obRCarnePetropolis->setValor2      (number_format(round($nuValor2,2),2,',','.')) ;
                                if ($boVenc3 == true) {
                                    $this->obRCarnePetropolis->setValor3      (number_format(round($nuValor3,2),2,',','.')) ;
                                }
                            }
                        }
                    } else {
                        $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));

                    }

                }
            }
            $this->obRCarnePetropolis->setValorTotal   ( $this->obRCarnePetropolis->getValor());
            $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
            $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
            $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

            $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
            $this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
            $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
            $this->inVertical += 80;
            if ( ( $inParcela == 3 ) || ( $inCount == 3 ) ) {
                $this->obRCarnePetropolis->novaPagina();
                $inCount = 0;
                $this->inVertical = 7;
                $this->boPulaPagina = false;
            } else {
                $this->boPulaPagina = true;
            }
            $inCount++;
        }// fim foreach parcelas

//*/
        //if ( ( $inSaltaPagina != count($arEmissao) ) && ( ( count($chave) != 2 ) && ( count($chave) != 3 ) ) ) {
        if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
            $this->obRCarnePetropolis->novaPagina();
        }
        $arGruposValidos = explode(',','101,102,10120, 10121, 10122, 10123, 10124, 10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299');
        if( in_array($this->obRCarnePetropolis->getCodDivida(),$arGruposValidos))
            $this->obRCarnePetropolis->drawComplemento($this->inHorizontal, $this->inVertical);

    } // fim foreach $arEmissao

    if ( Sessao::read( 'stNomPdf' ) )
        $stNome     = Sessao::read( 'stNomPdf' );
    else
        $stNome     = "Carne.pdf";

    if ( Sessao::read( 'stParamPdf' ) )
        $stParam    = Sessao::read( 'stParamPdf' );
    else
        $stParam    = "I";

    $this->obRCarnePetropolis->show($stNome,$stParam); // lanca o pdf
}

function geraParcelas($data, $iteracao)
{
    $arDataResult = array();

    for ($i=0;$i<$iteracao;$i++) {
        $arData = explode('/',$data);

        $mes = $arData[1];
        $dia = $arData[0];
        $ano = $arData[2];

        switch ( (int) $mes ) {
            case 2 :
                    if ($ano % 4 == 0) {
                        $dia = 29;
                    } else {
                        $dia = 28;
                    }
            break;
            case 1 :
            case 3 :
            case 5 :
            case 7 :
            case 8 :
            case 10: $dia = 31;
            break;

            case 4 :
            case 6 :
            case 9 :
            case 11: $dia = 30;
            break;
        }

        $data = str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$ano;
        array_push($arDataResult,$data);

        $mes++;
        if ($mes > 12) {
            $mes = 1;
            $ano++;
        }

        $data = $dia.'/'.$mes.'/'.$ano;
    }

    return $arDataResult;
}

}
