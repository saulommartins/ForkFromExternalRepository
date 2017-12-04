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
     * Classe de regra de Relatório de trecho
     * Data de Criação: 31/03/2005

     * @author Analista: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Marcelo B. Paulino

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMRelatorioTrechos.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrecho.class.php"         );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"            );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"     );

/**
    * Classe de Regra para Relatório de Trechos
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioTrechos
{
/**
    * @access Private
    * @var Integer
*/
var $inCodInicio;
/**
    * @access Private
    * @var Integer
*/
var $inCodInicioLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inCodTermino;
/**
    * @access Private
    * @var Integer
*/
var $inCodTerminoLogradouro;
/**
    * @access Private
    * @var String
*/
var $stOrder;
/**
    * @access Private
    * @var String
*/
var $stTipoRelatorio;
/**
    * @access Private
    * @var Array
*/
var $arAtributos;
/**
    * @var Object
    * @access Private
*/
var $obTTrecho;
/**
    * @var Object
    * @access Private
*/
var $obRTrecho;
/**
    * @var Object
    * @access Private
*/
var $obRCadastroDinamico;

var $boRSMD;
var $boAliquota;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicio($valor) { $this->inCodInicio             = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioLogradouro($valor) { $this->inCodInicioLogradouro   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTermino($valor) { $this->inCodTermino            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoLogradouro($valor) { $this->inCodTerminoLogradouro  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder                 = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio        = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setAtributos($valor) { $this->arAtributos[]          = $valor; }
function setboRSMD($valor) { $this->boRSMD     = $valor; }
function setboAliquota($valor) { $this->boAliquota = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodInicio() { return $this->inCodInicio;            }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioLogradouro() { return $this->inCodInicioLogradouro;  }
/**
    * @access Public
    * @return Integer
*/
function getCodTermino() { return $this->inCodTermino;           }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoLogradouro() { return $this->inCodTerminoLogradouro; }
/**
    * @access Public
    * @return String
*/
function getOrder() { return $this->stOrder;                }
/**
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;        }
/**
    * @access Public
    * @return Array
*/
function getAtributos() { return $this->arAtributos;            }

/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioTrechos()
{
    $this->obTCIMTrecho = new TCIMTrecho;
    $this->obRCIMTrecho = new RCIMTrecho;

    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoTrechoValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 7 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
}

/**
    * Método abstrato
    * @access Public
*/

function getRecordSetValor(&$rsRecordSet, &$arCabecalho, $stOrder = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecordSetValor( $this->stOrder );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );

    $arAtributos = $this->getAtributos();
    $arAtributos = $arAtributos[0];
    $arRecord          = array();
    $arCabecalho       = array();
    $inCount = 0;

    while ( !$rsRecordSet->eof() ) {
        $arRecord[$inCount]['pagina']     = 0;
        $arRecord[$inCount]['trecho']     = $rsRecordSet->getCampo('trecho'    );
        $arRecord[$inCount]['sequencia']  = $rsRecordSet->getCampo('sequencia' );
        $arRecord[$inCount]['extensao']   = $rsRecordSet->getCampo('extensao'  );
        $arRecord[$inCount]['logradouro'] = $rsRecordSet->getCampo('logradouro');
        $arRecord[$inCount]['valor_m2_territorial'] = $rsRecordSet->getCampo('valor_m2_territorial');
        $arRecord[$inCount]['valor_m2_predial']     = $rsRecordSet->getCampo('valor_m2_predial');
        $arRecord[$inCount]['aliquota_territorial'] = $rsRecordSet->getCampo('aliquota_territorial');
        $arRecord[$inCount]['aliquota_predial']     = $rsRecordSet->getCampo('aliquota_predial');
        $arRecord[$inCount]['vigencia']   = $rsRecordSet->getCampo('dt_vigencia');

        if ( $this->getTipoRelatorio() == 'analitico' ) {
            //monta array com os atributos que serao exibidos no relatorio
            $arChaveAtributoTrecho = array( "cod_trecho" => $rsRecordSet->getCampo('cod_trecho') , "cod_logradouro"  => $rsRecordSet->getCampo('cod_logradouro') );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
            $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

            while ( !$rsAtributos->eof() ) {
                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributos ) ) {
                    //monta array de cabecalho dos atributos
                    if ( count($arCabecalho) < count($arAtributos) ) {
                        $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');
                    }
                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {
                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":    $valor = $rsAtributos->getCampo('valor'); break;
                            case "Numerico": $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }
                    $arRecord[$inCount][$rsAtributos->getCampo('nom_atributo')] = $valor;
                }
                $rsAtributos->proximo();
            }
            $inCountCabecalho     = count( $arCabecalho );
            if ($inCountCabecalho <= 0)
                $inWidth = 0;
            else
                $inWidth = 55 / $inCountCabecalho;

            $arCabecalho['width'] = $inWidth;
        }
        $inCount++;
        $rsRecordSet->proximo();
    }

    //calcula a largura das colunas dos atributos de acordo com o numero de atributos selecionados
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;

}

function montaRecordSetValor($stOrder = "")
{
    $stSql = "";
    $stSql .= " SELECT                                                          \n";
    $stSql .= "          T.*,                                                       \n";
    $stSql .= "          T.cod_logradouro as trecho,                                \n";
    $stSql .= "          TL.nom_tipo||' '||NL.nom_logradouro as logradouro         \n";

    if ($this->boRSMD == true) {
        $stSql .= "     ,m2.valor_m2_territorial,    \n";
        $stSql .= "     m2.valor_m2_predial,    \n";
        $stSql .= " to_char(m2.dt_vigencia, 'dd/mm/YY') as dt_vigencia ";
    }

    if ($this->boAliquota == true) {
        $stSql .= "     ,aliquota.aliquota_territorial,  \n";
        $stSql .= "     aliquota.aliquota_predial      \n";
    }

    $stSql .= "      FROM                                                           \n";
    $stSql .= "         imobiliario.trecho      AS T    \n";

    if ($this->boRSMD == true) {
        $stSql .= "     LEFT JOIN (SELECT *     \n";
        $stSql .= "           FROM imobiliario.trecho_valor_m2      \n";
        $stSql .= "              INNER JOIN( SELECT max(timestamp) as timestamp         \n";
        $stSql .= "                     , cod_trecho        \n";
        $stSql .= "                     , cod_logradouro            \n";
        $stSql .= "                 FROM imobiliario.trecho_valor_m2        \n";
        $stSql .= "                GROUP BY cod_trecho, cod_logradouro) as valor_m2_predial     \n";
        $stSql .= "           USING(cod_trecho, cod_logradouro, timestamp) )AS m2       \n";
        $stSql .= "     USING( cod_trecho, cod_logradouro)          \n";
    }

    if ($this->boAliquota == true) {
        $stSql .= "     LEFT JOIN (SELECT *     \n";
        $stSql .= "           FROM imobiliario.trecho_aliquota  \n";
        $stSql .= "              INNER JOIN( SELECT max(timestamp) as timestamp         \n";
        $stSql .= "                     , cod_trecho        \n";
        $stSql .= "                     , cod_logradouro            \n";
        $stSql .= "                 FROM imobiliario.trecho_aliquota    \n";
        $stSql .= "                GROUP BY cod_trecho, cod_logradouro) as aliquota_temp    \n";
        $stSql .= "           USING(cod_trecho, cod_logradouro, timestamp) )AS aliquota     \n";
        $stSql .= "     USING( cod_trecho, cod_logradouro)          \n";
    }

    $stSql .= "               INNER JOIN ( SELECT tmp.*
                                             FROM sw_nome_logradouro AS tmp
                                       INNER JOIN ( SELECT max(timestamp) AS timestamp
                                                         , cod_logradouro
                                                      FROM sw_nome_logradouro
                                                  GROUP BY cod_logradouro
                                                  )AS tmp2
                                               ON tmp.cod_logradouro = tmp2.cod_logradouro
                                              AND tmp.timestamp = tmp2.timestamp
                                          )AS NL
                                       ON NL.cod_logradouro = T.cod_logradouro,       \n";
    $stSql .= "         sw_tipo_logradouro AS TL,                                   \n";
    $stSql .= "         sw_logradouro      AS L                                     \n";
    $stSql .= "      WHERE                                                          \n";
    $stSql .= "          T.cod_logradouro = L.cod_logradouro AND                    \n";
    $stSql .= "          L.cod_logradouro = NL.cod_logradouro AND                   \n";
    $stSql .= "          NL.cod_tipo      = TL.cod_tipo                             \n";

    $stFiltro = "";

    //monta filtro de acordo com os valores indicados na tela de filtro
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia > ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia < ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    if ( $this->getCodInicioLogradouro() AND !$this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro > ".$this->inCodInicioLogradouro;
    } elseif ( !$this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro < ".$this->inCodTerminoLogradouro;
    } elseif ( $this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro between ".$this->inCodInicioLogradouro." AND ".$this->inCodTerminoLogradouro ;
    }

    //monta ordem de acordo com os valores indicados na tela de filtro
    switch ($this->stOrder) {
        case 'trecho'        : $stOrder = "T.cod_trecho, T.cod_logradouro"; break;
        case 'codLogradouro' : $stOrder = "T.cod_logradouro, T.cod_trecho"; break;
        case 'nomLogradouro' : $stOrder = "NL.nom_logradouro, T.cod_trecho"; break;
        default: $stOrder = "T.cod_trecho, L.cod_logradouro";
    }

    $stSql = $stSql." ".$stFiltro." ORDER BY ".$stOrder;

    return $stSql;
}

function geraRecordSet(&$rsRecordSet, &$arCabecalho, $stOrder = "")
{
    $stFiltro = "";

    //monta filtro de acordo com os valores indicados na tela de filtro
    if ( $this->getCodInicio() AND !$this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia > ".$this->inCodInicio;
    } elseif ( !$this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia < ".$this->inCodTermino;
    } elseif ( $this->getCodInicio() AND $this->getCodTermino() ) {
        $stFiltro .= " AND T.sequencia between ".$this->inCodInicio." AND ".$this->inCodTermino ;
    }

    if ( $this->getCodInicioLogradouro() AND !$this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro > ".$this->inCodInicioLogradouro;
    } elseif ( !$this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro < ".$this->inCodTerminoLogradouro;
    } elseif ( $this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND T.cod_logradouro between ".$this->inCodInicioLogradouro." AND ".$this->inCodTerminoLogradouro ;
    }

    //monta ordem de acordo com os valores indicados na tela de filtro
    switch ($this->stOrder) {
        case 'trecho'        : $stOrder .= "T.cod_trecho, T.cod_logradouro"; break;
        case 'codLogradouro' : $stOrder .= "T.cod_logradouro, T.cod_trecho"; break;
        case 'nomLogradouro' : $stOrder .= "NL.nom_logradouro, T.cod_trecho"; break;
        default: $stOrder .= "T.cod_trecho, L.cod_logradouro";
    }

    $obErro = $this->obTCIMTrecho->recuperaRelacionamentoRelatorio( $rsRecordSet, $stFiltro, $stOrder );

    $arAtributos = $this->getAtributos();
    $arAtributos = $arAtributos[0];
    $arRecord          = array();
    $arCabecalho       = array();
    $inCount = 0;

    while ( !$rsRecordSet->eof() ) {
        $arRecord[$inCount]['pagina']     = 0;
        $arRecord[$inCount]['trecho']     = $rsRecordSet->getCampo('trecho'    );
        $arRecord[$inCount]['sequencia']  = $rsRecordSet->getCampo('sequencia' );
        $arRecord[$inCount]['extensao']   = $rsRecordSet->getCampo('extensao'  );
        $arRecord[$inCount]['logradouro'] = $rsRecordSet->getCampo('logradouro');
        $arRecord[$inCount]['territorial'] = $rsRecordSet->getCampo('valor_m2_territorial');
        $arRecord[$inCount]['predial']     = $rsRecordSet->getCampo('valor_m2_predial');
        $arRecord[$inCount]['vigencia']   = $rsRecordSet->getCampo('dt_vigencia');

        if ( $this->getTipoRelatorio() == 'analitico' ) {
            //monta array com os atributos que serao exibidos no relatorio
            $arChaveAtributoTrecho = array( "cod_trecho" => $rsRecordSet->getCampo('cod_trecho') , "cod_logradouro"  => $rsRecordSet->getCampo('cod_logradouro') );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
            $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

            while ( !$rsAtributos->eof() ) {
                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributos ) ) {
                    //monta array de cabecalho dos atributos
                    if ( count($arCabecalho) < count($arAtributos) ) {
                        $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');
                    }
                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {
                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":    $valor = $rsAtributos->getCampo('valor'); break;
                            case "Numerico": $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }
                    $arRecord[$inCount][$rsAtributos->getCampo('nom_atributo')] = $valor;
                }
                $rsAtributos->proximo();
            }
            $inCountCabecalho     = count( $arCabecalho );
            if ($inCountCabecalho <= 0)
                $inWidth = 0;
            else
                $inWidth = 55 / $inCountCabecalho;

            $arCabecalho['width'] = $inWidth;
        }
        $inCount++;
        $rsRecordSet->proximo();
    }

    //calcula a largura das colunas dos atributos de acordo com o numero de atributos selecionados
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

}
