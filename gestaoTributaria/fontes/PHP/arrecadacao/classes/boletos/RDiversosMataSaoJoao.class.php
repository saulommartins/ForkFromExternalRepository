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
  * Regra Relatorio de Carnê para Mata de Sao Joao
  * Data de criação : 09/01/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @package URBEM

    * $Id: RDiversosMataSaoJoao.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.19  2007/04/11 20:43:05  fabio
correção da mensagem de parcelas não unicas - IPTU

Revision 1.18  2007/03/19 18:30:04  cercato
alterando frase do carne de iptu onde for unica para nao receber apos recebimento.

Revision 1.17  2007/03/05 18:59:58  cercato
retirando mensagem de nao receber apos vencimento.

Revision 1.16  2007/02/15 15:04:48  cercato
setando observacao nos carnes.

Revision 1.15  2007/01/17 18:39:52  cercato
correcoes no carne de iptu (capa)

Revision 1.14  2006/12/21 12:41:15  cercato
correcao para apresentar todos os dados na capa do iptu.

Revision 1.13  2006/12/18 18:26:51  cercato
correcao da imagem para carregar de forma dinamica.

Revision 1.12  2006/12/14 18:43:06  dibueno
Inclusao de Juros e Multa na Consolidação

Revision 1.11  2006/12/14 12:17:42  dibueno
Inclusao de Consolidação

Revision 1.10  2006/12/14 11:38:08  dibueno
Inclusao de Consolidação

Revision 1.9  2006/12/14 09:42:44  cercato
correcao para busca das datas para a capa do iptu.

Revision 1.8  2006/12/13 10:14:54  cercato
ajuste para sair em 7,5 cm e para o valor total sair no codigo de barras.

Revision 1.7  2006/12/12 15:18:05  cercato
correcao para mostrar juros e multa.

Revision 1.6  2006/12/08 17:52:01  cercato
correcao para nao aparecer "nao informado" no endereco do imovel.

Revision 1.5  2006/12/08 12:38:54  cercato
correcao para setar a posicao do proximo carne abaixo da zona de picote.

Revision 1.4  2006/12/06 11:05:55  cercato
correcao do codigo de barras

Revision 1.3  2006/11/29 16:32:58  cercato
bug #7691#

Revision 1.2  2006/11/21 11:29:06  cercato
correcao do titulo e imagem do carne. correcao da parte do carne que saia duplicada.

Revision 1.1  2006/11/06 14:53:02  domluc
...

Revision 1.12  2006/09/15 11:37:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

Revision 1.11  2006/09/15 10:26:13  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RProtocoloPetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarnePetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarneDiversosPetropolis.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCarneDadosCadastraisMataSaoJoao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class RDiversosMataSaoJoao
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
var $boConsolidacao;
var $stNumeracaoConsolidacao;
var $dtVencimentoConsolidacao;

/* setters */
function setHorizontal($valor) { $this->inHorizontal = $valor; }
function setVertical($valor) { $this->inVertical   = $valor; }
function setEmissao($valor) { $this->arEmissao    = $valor; }
function setBarra($valor) { $this->obBarra      = $valor; }
function setArBarra($valor) { $this->arBarra      = $valor; }
function setPulaPagina($valor) { $this->boPulaPagina = $valor; }
function setConsolidacao($valor) { $this->boConsolidacao = $valor;            }
function setVencimentoConsolidacao($valor) { $this->dtVencimentoConsolidacao = $valor;  }
function setNumeracaoConsolidacao($valor) { $this->stNumeracaoConsolidacao = $valor;   }

/* getters */
function getHorizontal() { return $this->inHorizontal;   }
function getVertical() { return $this->inVertical;     }
function getEmissao() { return $this->arEmissao;      }
function getBarra() { return $this->obBarra;        }
function getArBarra() { return $this->arBarra;        }
function getPulaPagina() { return $this->boPulaPagina;   }
function getConsolidacao() { return $this->boConsolidacao;   }
function getNumeracaoConsolidacao() { return $this->stNumeracaoConsolidacao; }
function getVencimentoConsolidacao() { return $this->dtVencimentoConsolidacao; }

/*
    * Metodo Construtor
    * @access Private
*/
function RDiversosMataSaoJoao($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    $this->boConsolidacao = false;
    //$obRProtocoloPetropolis = new RProtocoloPetropolis;
    //$obRCarnePetropolis     = new RCarnePetropolis;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    ;
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
    $this->obRCarnePetropolis = new RCarneDadosCadastraisMataSaoJoao();
    $this->obRCarnePetropolis->stCamLogo = CAM_FW_TEMAS."imagens/".$stNomeImagem;
    $this->obRCarnePetropolis->lblTitulo1 = "MATA DE SÃO JOÃO - Sec. de Adm. e Fin.";

    //$this->obRCarnePetropolis->configuraCarne();
    $nuValorTotal = $nuValorNormal = $nuValorJuroNormal = $nuValorMultaNormal = 0.00;
    //$this->obRCarnePetropolis->configuraProtocolo();
    foreach ($this->arEmissao as $valor => $chave) {
        /* imprimir duas folhas com dados cadastrais */
        /* buscar informações para dados cadastrais*/

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
        $stFiltro = " WHERE aivv.inscricao_municipal = ". $chave[0]['inscricao'] ." AND aivv.exercicio = ".$chave[0]['exercicio']." \n";

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosIPTUMata( $rsListaCarne, $stFiltro, $chave[0]['cod_parcela'] );

        $rsListaCarne->addFormatacao ('area_lote','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupt','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupc','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_territorial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_territorial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_predial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_imovel','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_predial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('aliquota','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_total_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_imposto','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_total_tributos','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_luz','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_parcela','NUMERIC_BR');

        $arDadosParcelas = array();
        $inTotalParcelas = 0;
        if ( !$rsListaCarne->Eof() ) {
            $rsListaCarne->setPrimeiroElemento();
            //$inCodCalculo = $rsListaCarne->getCampo("cod_calculo");
            while ( !$rsListaCarne->Eof() ) {
                if ( $rsListaCarne->getCampo("nro_parcela") != "única" ) {
                    $arDadosParcelas[$rsListaCarne->getCampo("nro_parcela")]["data"] = $rsListaCarne->getCampo("vencimento_parcela");

                    $arDadosParcelas[$rsListaCarne->getCampo("nro_parcela")]["valor"] = $rsListaCarne->getCampo("valor_parcela");
                } else {
                    $arDadosParcelas[0]["data"] = $rsListaCarne->getCampo("vencimento_parcela");
                    $arDadosParcelas[0]["valor"] = $rsListaCarne->getCampo("valor_parcela");
                }

                $inTotalParcelas++;
                if ( $inTotalParcelas > 11 )
                    break;

                $rsListaCarne->proximo();
            }

        }

        $rsListaCarne->setPrimeiroElemento();

        /* setar todos os dados necessarios */
        $this->obRCarnePetropolis->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->obRCarnePetropolis->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->obRCarnePetropolis->stExercicio  = (string) (string) $rsListaCarne->getCampo("exercicio"); //'2006';
        $this->obRCarnePetropolis->stContribuinte  = (string) (string) $rsListaCarne->getCampo("nom_proprietario"); //'WELLIGNTON LAZARO BARRETO DE OLIVEIRA' ;
        $this->obRCarnePetropolis->stInscricaoCadastral  = (string) $rsListaCarne->getCampo("inscricao_municipal"); //'015041' ;
        $this->obRCarnePetropolis->stCategoriaUtilizacao  = (string) $rsListaCarne->getCampo("categoria_utilizacao_imovel"); //'RESIDENCIAL' ;
        $this->obRCarnePetropolis->stTipoTributo  = 'IPTU / TAXA DE LIMPEZA / CONTRB.  DE ILUM. PÚBLICA' ;
        $this->obRCarnePetropolis->stCodigoLogradouro  = (string) $rsListaCarne->getCampo("cod_logradouro"); //'50.003' ;

        $this->obRCarnePetropolis->stNomeLogradouro  = (string) str_replace ( "Não Informado ", "", $rsListaCarne->getCampo("endereco_logradouro") ); //'AV DO FAROL 50.003' ;
        $this->obRCarnePetropolis->stComplemento  = (string) $rsListaCarne->getCampo("endereco_complemento"); //'CONDOMINIO SOLAR DOS ARCOS' ;
        $this->obRCarnePetropolis->stQuadra  = (string) $rsListaCarne->getCampo("numero_quadra"); //'02' ;
        $this->obRCarnePetropolis->stLote  = (string) $rsListaCarne->getCampo("numero_lote"); //'02' ;
        $this->obRCarnePetropolis->stDistrito  = (string) $rsListaCarne->getCampo("distrito"); //'PRAIA DO FORTE' ;
        $this->obRCarnePetropolis->stRegiao  = (string) $rsListaCarne->getCampo("regiao"); //'LITORAL' ;
        $this->obRCarnePetropolis->stCep  = (string) $rsListaCarne->getCampo("cep"); //'48.820-000' ;
        $this->obRCarnePetropolis->stCidade  = 'MATA DE SÃO JOÃO' ;
        $this->obRCarnePetropolis->stEstado  = 'BAHIA' ;
        $this->obRCarnePetropolis->stAreaUsoPrivativoTerreno  = (string) $rsListaCarne->getCampo("area_lote"); //'114,52' ;
        $this->obRCarnePetropolis->stVupt = (string) $rsListaCarne->getCampo("vupt"); //'4,5' ;
        $this->obRCarnePetropolis->stVupc = (string) $rsListaCarne->getCampo("vupc"); //'180,00' ;
        $this->obRCarnePetropolis->stValorVenalTerreno = (string) $rsListaCarne->getCampo("venal_territorial_calculado"); //'526,50' ;
        $this->obRCarnePetropolis->stImpostoTerritorial = (string) $rsListaCarne->getCampo("imposto_territorial"); //'5,27' ;
        $this->obRCarnePetropolis->stAreaUsoPrivativoEdificacao = (string) $rsListaCarne->getCampo("area_imovel"); //'50,00' ;
        $this->obRCarnePetropolis->stValorVenalConstrucao = (string) $rsListaCarne->getCampo("venal_predial_calculado"); //'10.800,00' ;
        $this->obRCarnePetropolis->stImpostoPredial = (string) $rsListaCarne->getCampo("imposto_predial"); //'108,00' ;
        $this->obRCarnePetropolis->stAliquota = (string) $rsListaCarne->getCampo("aliquota"); //'1,00 %' ;
        $this->obRCarnePetropolis->stValorVenalImovel = (string) $rsListaCarne->getCampo("venal_total_calculado"); //'11.326,50' ;
        $this->obRCarnePetropolis->stValorImposto = (string) $rsListaCarne->getCampo("valor_imposto"); //'113,27' ;
        $this->obRCarnePetropolis->stTipoUnidade = (string) $rsListaCarne->getCampo("categoria_utilizacao_imovel"); //'RESIDENCIAL';
        $this->obRCarnePetropolis->stZona = (string) $rsListaCarne->getCampo("zona"); //'POPULAR' ;
        $this->obRCarnePetropolis->stAreaM2 = (string) $rsListaCarne->getCampo("area_m2_limpeza_publica"); //'11.656.220,00' ;
        $this->obRCarnePetropolis->stValorM2 = (string) $rsListaCarne->getCampo("valor_m2_limpeza_publica"); //'1,01' ;
        $this->obRCarnePetropolis->stValorTaxa = (string) $rsListaCarne->getCampo("taxa_limpeza_publica"); //'28,93' ;
        $this->obRCarnePetropolis->stValorTotalTributos = (string) $rsListaCarne->getCampo("valor_total_tributos"); //'162,20' ;
        $this->obRCarnePetropolis->stContribIlumPublica = (string) $rsListaCarne->getCampo("taxa_luz"); //'20,00';
        $this->obRCarnePetropolis->arDemonstrativoParcelas = array 	(
                                                         0 => $arDadosParcelas[0]["valor"], //'140,87',
                                                         1 => $arDadosParcelas[1]["valor"], //'16,22' ,
                                                         2 => $arDadosParcelas[2]["valor"], //'16,22' ,
                                                         3 => $arDadosParcelas[3]["valor"], //'16,22' ,
                                                         4 => $arDadosParcelas[4]["valor"], //'16,22' ,
                                                         5 => $arDadosParcelas[5]["valor"], //'16,22' ,
                                                         6 => $arDadosParcelas[6]["valor"], //'16,22' ,
                                                         7 => $arDadosParcelas[7]["valor"], //'16,22' ,
                                                         8 => $arDadosParcelas[8]["valor"], //'16,22' ,
                                                         9 => $arDadosParcelas[9]["valor"], //'16,22' ,
                                                         10 => $arDadosParcelas[10]["valor"] //'16,22'
                                                    ) ;
        $this->obRCarnePetropolis->arVencimentosDemonstrativos = array 	(
                                                         0 => $arDadosParcelas[0]["data"], //'05/02/2007',
                                                         1 => $arDadosParcelas[1]["data"], //'05/02/2007' ,
                                                         2 => $arDadosParcelas[2]["data"], //'05/03/2007' ,
                                                         3 => $arDadosParcelas[3]["data"], //'05/04/2007' ,
                                                         4 => $arDadosParcelas[4]["data"], //'05/05/2007' ,
                                                         5 => $arDadosParcelas[5]["data"], //'05/06/2007' ,
                                                         6 => $arDadosParcelas[6]["data"], //'05/07/2007' ,
                                                         7 => $arDadosParcelas[7]["data"], //'05/08/2007' ,
                                                         8 => $arDadosParcelas[8]["data"], //'05/09/2007' ,
                                                         9 => $arDadosParcelas[9]["data"], //'05/10/2007' ,
                                                         10 => $arDadosParcelas[10]["data"] //'05/11/2007'
                                                    ) ;

        $this->obRCarnePetropolis->desenhaCarne(10,40);
        $this->obRCarnePetropolis->novaPagina();

        $inSaltaPagina++;

        $this->obRCarnePetropolis->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarnePetropolis->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {
            /* montagem cabecalho (protocolo) */
            $this->obRCarnePetropolis->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarnePetropolis->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarnePetropolis->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarnePetropolis->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarnePetropolis->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );
            $this->obRCarnePetropolis->setRua               ( str_replace ( "Não Informado ", "",  $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) ) );
            $this->obRCarnePetropolis->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
            $this->obRCarnePetropolis->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
            $this->obRCarnePetropolis->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
            $this->obRCarnePetropolis->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );
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
/*        $this->obRCarnePetropolis->drawProtocolo();
        $this->obRCarnePetropolis->posicionaVariaveisProtocolo();
*/
        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 20;

        $this->obBarra = new RCodigoBarraFebraban;
        $this->arBarra = array();

        /*********************** CONSOLIDACAO */
        if ( $this->getConsolidacao() ) {

        #echo '<h2>CONSOLIDACAO </h2>'; exit;


            foreach ($chave as $parcela) {

                $inParcela++;

                $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
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





                $dtVencimento = $this->getVencimentoConsolidacao();
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );

                $nuValorTotal = $arValorNormal[0];
                $nuValorNormal = $arValorNormal[1];
                $nuValorJuroNormal = $arValorNormal[3];
                $nuValorMultaNormal = $arValorNormal[2];



                // data da reemissao
                #$arTmp = explode ( '/', $rsParcela->getCampo( 'vencimento' ) );
                #$dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                #$stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                #$stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                #$stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                #$obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
                #$nuValorNormal = $rsTmp->getCampo('valor');

                //$this->arBarra['valor_documento'] = $rsParcela->getCampo( 'valor' );
                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['vencimento'] = (string) $rsParcela->getCampo( 'vencimento' );
                //$this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                //$this->obRCarnePetropolis->setNumeracao( (string) $rsParcela->getCampo( 'numeracao' ) );
                $this->arBarra['nosso_numero'] = (string) $this->getNumeracaoConsolidacao();
                $this->obRCarnePetropolis->setNumeracao( (string) $this->getNumeracaoConsolidacao() );
                $this->arBarra['cod_febraban'] = $inCodFebraban;

                if ( !$obErro->ocorreu() ) {

                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_parcela"    , $rsParcela->getCampo('cod_parcela')   );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_lancamento" , $rsParcela->getCampo('cod_lancamento'));
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "nr_parcela"     , $rsParcela->getCampo('nr_parcela')    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "vencimento"     , $this->getVencimentoConsolidacao()    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "valor" , $nuValorTotal );
                    $obErro = $this->obRARRCarne->obRARRParcela->obTARRParcela->alteracao($boTransacao);

                    #$this->obRARRCarne->obRARRParcela->obTARRParcela->debug();
                    #exit;
                }

                $nuValorTotal += $nuValorTotal;
                $nuValorNormal += $nuValorNormal;
                $nuValorJuroNormal += $nuValorJuroNormal;
                $nuValorMultaNormal += $nuValorMultaNormal;

            }


                $this->obRCarnePetropolis->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                $this->obRCarnePetropolis->setParcela ( "1/1" );
                $this->obRCarnePetropolis->setVencimento  ( $this->getVencimentoConsolidacao() );
                $this->obRCarnePetropolis->flValorJuros = ( number_format(round($nuValorJuroNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorMulta = ( number_format(round($nuValorMultaNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValor       ( number_format(round($nuValorNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValorTotal(number_format(round($nuValorTotal,2),2,',',''));

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

                $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 95;


        } else {

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
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );
                $nuValorTotal = $arValorNormal[0];
                $nuValorNormal = $arValorNormal[1];
                $stJuroNormal = $arValorNormal[3];
                $stMultaNormal = $arValorNormal[2];

                //$this->arBarra['valor_documento'] = $rsParcela->getCampo( 'valor' );
                $this->arBarra['valor_documento'] = $nuValorTotal;
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
                        $this->obRCarnePetropolis->stObsVencimento = "Não receber após o vencimento.";
                        $this->obRCarnePetropolis->setParcela ( 'ÚNICA' );
                    } else {
                        $this->arBarra['tipo_moeda'] = 7;
                        //$arVencimentos = geraParcelas($rsParcela->getCampo( 'vencimento' ),count($chave));
                        $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                        //$this->obRCarnePetropolis->setParcela( $inParcela.'/'.count($chave) );
                        $this->obRCarnePetropolis->setParcela( $rsParcela->getCampo( 'info' ));
    //                    $this->obRCarnePetropolis->setObservacaoL1( 'Após os vencimentos previstos nesta guia, retirar 2ª via na' );
    //                    $this->obRCarnePetropolis->setObservacaoL2( 'Secretaria de Fazenda' );
    //                    $this->obRCarnePetropolis->setObservacaoL3( ' ' );
                        $this->obRCarnePetropolis->stObsVencimento = "Receber até 31/12/2007";
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
                            //$obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'j'");
                            //$stJuroNormal = $rsTmp->getCampo('valor');

                            $this->obRCarnePetropolis->flValorJuros = $stJuroNormal;

                            // % de multa
    //                        $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'m'");
    //                        $stMultaNormal = $rsTmp->getCampo('valor');
                            $this->obRCarnePetropolis->flValorMulta = $stMultaNormal;
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
                $this->obRCarnePetropolis->setValorTotal( $nuValorTotal );
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
        }

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
