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
     * Classe de regra de negócio para trecho
     * Data de Criação: 25/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMTrecho.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.9  2007/07/09 21:35:24  cercato
alteracao para o cgm funcionar da mesma forma que no cadastro economico e utilizar as novas tabelas sw_cgm_logradouro e sw_cgm_logradouro_correspondencia.

Revision 1.8  2006/10/27 18:37:46  dibueno
*** empty log message ***

Revision 1.7  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrecho.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaTrecho.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMTrechoAtivo.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMFaceQuadra.class.php"       );

//INCLUD DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTrechoValor.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrechoAliquota.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTrechoValorM2.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConfrontacaoTrecho.class.php" );

class RCIMTrecho extends RCIMLogradouro
{
var $dtAliquotaVigencia;
var $inAliquotaCodNorma;
var $inAliquotaTerritorial;
var $inAliquotaPredial;
var $dtMDVigencia;
var $inMDCodNorma;
var $inMDTerritorial;
var $inMDPredial;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTrecho;
/**
    * @access Private
    * @var Integer
*/
var $inSequencia;
/**
    * @access Private
    * @var Float
*/
var $flExtensao;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stJustificativaReativar;
/**
    * @access Private
    * @var String
*/
var $stJustificativa;
/**
    * @access Private
    * @var String
*/
var $stNomeLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTCIMTrecho;
/**
    * @access Private
    * @var Object
*/
var $obTCIMBaixaTrecho;
/**
    * @access Private
    * @var Object
*/
var $obVCIMTrechoAtivo;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;

function setAliquotaVigencia($valor) { $this->dtAliquotaVigencia = $valor; }
function setAliquotaCodNorma($valor) { $this->inAliquotaCodNorma = $valor; }
function setAliquotaTerritorial($valor) { $this->inAliquotaTerritorial = $valor; }
function setAliquotaPredial($valor) { $this->inAliquotaPredial = $valor; }
function setMDVigencia($valor) { $this->dtMDVigencia = $valor; }
function setMDCodNorma($valor) { $this->inMDCodNorma = $valor; }
function setMDTerritorial($valor) { $this->inMDTerritorial = $valor; }
function setMDPredial($valor) { $this->inMDPredial = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoTrecho($valor) { $this->inCodigoTrecho  = $valor;  }
/**
    * @access Public
    * @param Integer $valo
*/
function setSequencia($valor) { $this->inSequencia     = $valor;  }
/**
    * @access Public
    * @param Float $valor
*/
function setExtensao($valor) { $this->flExtensao      = $valor;  }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa     = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor;  }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa = $valor;  }
/**
    * @access Public
    * @param String $valor
*/
function setNomeLogradouro($valor) { $this->stNomeLogradouro = $valor; }

function getCodigoTrecho() { return $this->inCodigoTrecho;   }
/**
    * @access Public
    * @return integer
*/
function getSequencia() { return $this->inSequencia;      }
/**
    * @access Public
    * @return Float
*/
function getExtensao() { return $this->flExtensao;       }
/**
    * @access Public
    * @return Date
*/
function getDataBaixa() { return $this->dtDataBaixa;      }
/**
    * @access Public
    * @return String
*/
function getJustificativa() { return $this->stJustificativa;  }

/**
    * @access Public
    * @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar;  }

/**
    * @access Public
    * @return String
*/
function getNomeLogradouro() { return $this->stNomeLogradouro; }

/**
     * MÃ©todo construtor
     * @access Private
*/
function RCIMTrecho()
{
    parent::RCIMLogradouro();
    $this->obTCIMBaixaTrecho   = new TCIMBaixaTrecho;
    $this->obTCIMTrecho        = new TCIMTrecho;
    $this->obVCIMTrechoAtivo   = new VCIMTrechoAtivo;
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoTrechoValor );
    $this->obRCadastroDinamico->setCodCadastro( 7 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
}

/**
    * Inclui os dados setados na tabela de Trecho
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirTrecho($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaSequencia( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMTrecho->setDado( "cod_logradouro", $this->inCodigoLogradouro );
            $obErro = $this->obTCIMTrecho->proximoCod( $this->inCodigoTrecho, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMTrecho->setDado( "cod_trecho" , $this->inCodigoTrecho );
                $this->obTCIMTrecho->setDado( "sequencia"  , $this->inSequencia    );
                $this->obTCIMTrecho->setDado( "extensao"   , $this->flExtensao     );
                $obErro = $this->obTCIMTrecho->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($this->dtAliquotaVigencia) {
                        $obTCIMTrechoAliquota = new TCIMTrechoAliquota;
                        $obTCIMTrechoAliquota->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                        $obTCIMTrechoAliquota->setDado( "cod_trecho", $this->inCodigoTrecho );
                        $obTCIMTrechoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                        $obTCIMTrechoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                        $obTCIMTrechoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                        $obTCIMTrechoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                        $obTCIMTrechoAliquota->inclusao( $boTransacao );
                    }

                    if ($this->dtMDVigencia) {
                        $obTCIMTrechoValorM2 = new TCIMTrechoValorM2;
                        $obTCIMTrechoValorM2->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                        $obTCIMTrechoValorM2->setDado( "cod_trecho", $this->inCodigoTrecho );
                        $obTCIMTrechoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                        $obTCIMTrechoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                        $obTCIMTrechoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                        $obTCIMTrechoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                        $obTCIMTrechoValorM2->inclusao( $boTransacao );
                    }

                    //O Restante dos valores vem setado da pÃ¡gina de processamento
                    $arChaveAtributoTrecho =  array( "cod_trecho"      => $this->inCodigoTrecho,
                                                     "cod_logradouro" => $this->inCodigoLogradouro );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
                     $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                 }
             }
         }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTrecho );

     return $obErro;
 }

 /**
     * Altera os dados do Trecho setado
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function alterarTrecho($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obTCIMTrecho->setDado( "cod_logradouro" , $this->inCodigoLogradouro );
         $this->obTCIMTrecho->setDado( "cod_trecho"     , $this->inCodigoTrecho  );
         $this->obTCIMTrecho->setDado( "sequencia"      , $this->inSequencia     );
         $this->obTCIMTrecho->setDado( "extensao"       , $this->flExtensao      );
         $obErro = $this->obTCIMTrecho->alteracao( $boTransacao );
         if ( !$obErro->ocorreu() ) {
            if ($this->dtAliquotaVigencia) {
                $obTCIMTrechoAliquota = new TCIMTrechoAliquota;
                $obTCIMTrechoAliquota->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                $obTCIMTrechoAliquota->setDado( "cod_trecho", $this->inCodigoTrecho );
                $obTCIMTrechoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                $obTCIMTrechoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                $obTCIMTrechoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                $obTCIMTrechoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                $obTCIMTrechoAliquota->inclusao( $boTransacao );
            }

            if ($this->dtMDVigencia) {
                $obTCIMTrechoValorM2 = new TCIMTrechoValorM2;
                $obTCIMTrechoValorM2->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                $obTCIMTrechoValorM2->setDado( "cod_trecho", $this->inCodigoTrecho );
                $obTCIMTrechoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                $obTCIMTrechoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                $obTCIMTrechoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                $obTCIMTrechoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                $obTCIMTrechoValorM2->inclusao( $boTransacao );
            }

             //O Restante dos valores vem setado da pÃ¡gina de processamento
             $arChaveAtributoTrecho =  array( "cod_trecho"     => $this->inCodigoTrecho,
                                              "cod_logradouro" => $this->inCodigoLogradouro );
             $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
             $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );
         }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTrecho );

     return $obErro;
 }

    /**
        * Exclui o Trecho setado
        * @access Public
        * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
        * @return Object Objeto Erro
    */
    public function excluirTrecho($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->validaFaceQuadraTrecho( $rsFaceQuadraTrecho );
            if ( $rsFaceQuadraTrecho->getNumLinhas() <= 0 ) {
                //verificar se o trecho possui confrontacao!
                $obTCIMConfrontacaoTrecho = new TCIMConfrontacaoTrecho;
                $obTCIMConfrontacaoTrecho->recuperaListaConfrontacaoTrecho( $rsListaConfrontacao, $this->inCodigoTrecho, $this->inCodigoLogradouro, $boTransacao );
                if ( $rsListaConfrontacao->Eof() ) {
                    $obTCIMTrechoAliquota = new TCIMTrechoAliquota;
                    $obTCIMTrechoAliquota->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                    $obTCIMTrechoAliquota->setDado( "cod_trecho", $this->inCodigoTrecho );
                    $obTCIMTrechoAliquota->exclusao( $boTransacao );

                    $obTCIMTrechoValorM2 = new TCIMTrechoValorM2;
                    $obTCIMTrechoValorM2->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                    $obTCIMTrechoValorM2->setDado( "cod_trecho", $this->inCodigoTrecho );
                    $obTCIMTrechoValorM2->exclusao( $boTransacao );

                    //O Restante dos valores vem setado da pÃ¡gina de processamento
                    $arChaveAtributoTrecho =  array( "cod_trecho"     => $this->inCodigoTrecho,
                    "cod_logradouro" => $this->inCodigoLogradouro );
                    $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
                    $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCIMTrecho->setDado( "cod_logradouro" , $this->inCodigoLogradouro );
                        $this->obTCIMTrecho->setDado( "cod_trecho"     , $this->inCodigoTrecho  );
                        $obErro = $this->obTCIMTrecho->exclusao( $boTransacao );
                    }
                } else {
                    $obErro->setDescricao("Trecho referenciado em Confrontação de lote ".$rsListaConfrontacao->getCampo("nro_lote")."!");
                }
            } else {
                $obErro->setDescricao("Trecho referenciado na Face de quadra ".$rsFaceQuadraTrecho->getCampo("cod_face")."!");
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMTrecho );

        return $obErro;
    }

 /**
     * Baixa o Trecho setado
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function baixarTrecho($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaTrecho->setDado( "dt_inicio"      , $dtdiaHOJE );
        $this->obTCIMBaixaTrecho->setDado( "cod_logradouro" , $this->inCodigoLogradouro );
        $this->obTCIMBaixaTrecho->setDado( "cod_trecho"     , $this->inCodigoTrecho  );
        $this->obTCIMBaixaTrecho->setDado( "justificativa"  , $this->stJustificativa );
        $obErro = $this->obTCIMBaixaTrecho->inclusao( $boTransacao );
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaTrecho );

     return $obErro;
 }

 /**
     * Reativar o Trecho baixado
     * @access Public
     * @param  Object $obTransacao Parametro Transacao
     * @return Object Objeto Erro
 */
 function reativarTrecho($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaTrecho->setDado( "dt_termino"     , $dtdiaHOJE );
        $this->obTCIMBaixaTrecho->setDado( "cod_logradouro" , $this->inCodigoLogradouro );
        $this->obTCIMBaixaTrecho->setDado( "cod_trecho"     , $this->inCodigoTrecho  );
        $this->obTCIMBaixaTrecho->setDado( "timestamp"      , $this->dtDataBaixa );
        $this->obTCIMBaixaTrecho->setDado( "justificativa"  , $this->stJustificativa );
        $this->obTCIMBaixaTrecho->setDado( "justificativa_termino"  , $this->stJustificativaReativar );

        $obErro = $this->obTCIMBaixaTrecho->alteracao( $boTransacao );
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaTrecho );

     return $obErro;
 }

 /**
     * Altera os valores dos atributos do Trecho setado guardando o histÃ³rico
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function alterarCaracteristicas($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         //CADASTRO DE ATRIBUTOS
         if ( !$obErro->ocorreu() ) {
             //O Restante dos valores vem setado da pÃ¡gina de processamento
             $arChaveAtributoTrecho =  array( "cod_trecho"     => $this->inCodigoTrecho,
                                              "cod_logradouro" => $this->inCodigoLogradouro );
             $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoTrecho );
             $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
         }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

     return $obErro;
 }

 /**
     * Verifica se a sequÃªncia informada jÃ¡ havia sido informada para o logradouro setado
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function validaSequencia($boTransacao = "")
 {
     $stFiltro  = " WHERE cod_logradouro = ".$this->inCodigoLogradouro." AND ";
     $stFiltro .= " sequencia = ".$this->inSequencia;
     $obErro = $this->obTCIMTrecho->recuperaTodos( $rsSequencia, $stFiltro, "", $boTransacao );
     if ( !$obErro->ocorreu() ) {
         if ( !$rsSequencia->eof() ) {
             $obErro->setDescricao( "A seqüência ".$this->inSequencia." j&aacute; foi informada para este logradouro! " );
         }
     }

     return $obErro;
 }

 /**
     * Recupera a proxima sequÃªncia conforme o logradouro setado
     * @access Public
     * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function recuperaProximaSequencia(&$rsRecordSet, $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodigoLogradouro) {
         $stFiltro .= "     cod_logradouro = ".$this->inCodigoLogradouro." AND ";
     }
     if ($stFiltro) {
         $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
     }
     $obErro = $this->obTCIMTrecho->recuperaProximaSequencia( $rsRecordSet, $stFiltro, "", $boTransacao );

     return $obErro;
 }

 /**
     * Lista o relacionamento dos Logradouros com os trechos conforme o filtro setado
     * @access Public
     * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
function listarLogradourosTrecho(&$rsRecordSet, $boTransacao = "", $inCodPais = "")
{
    $stFiltro = "";
        $stFiltroLogradouro = "";
        if ( $this->getCEP() ) {
            $stFiltro = "  AND sw_cep_logradouro.cep = '".$this->getCEP()."' ";
        } else {
            if ($inCodPais) {
                $stFiltro .= "  AND sw_uf.cod_pais = ".$inCodPais." ";
            }
            if ($this->inCodigoUF) {
                    $stFiltro .= "  AND sw_uf.cod_uf = ".$this->inCodigoUF." ";
            }
            if ($this->inCodigoMunicipio) {
                    $stFiltro .= "  AND sw_municipio.cod_municipio = ".$this->inCodigoMunicipio." ";
            }
            if ( $this->getBairro() ) {
                $stFiltro .= "  AND sw_bairro.cod_bairro = ".$this->getBairro()." ";
            }
        }
        if ($this->inCodigoLogradouro) {
        $stFiltro .= " AND sw_logradouro.cod_logradouro = ".$this->inCodigoLogradouro." ";
    }
    if ($this->stNomeLogradouro) {
        $stFiltro .= " AND UPPER( sw_nome_logradouro.nom_logradouro ) ";
        $stFiltro .= "iLIKE UPPER( '%".$this->stNomeLogradouro."%' ) ";
    }

    $stOrder = " ORDER BY sw_nome_logradouro.nom_logradouro ";

    $obErro = $this->obTCIMTrecho->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

 /**
     * Lista os Trechos conforme o filtro setado
     * @access Public
     * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
function listarTrechos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLogradouro) {
        $stFiltro .= " AND sw_logradouro.cod_logradouro = ".$this->inCodigoLogradouro." ";
    }
    if ($this->stNomeLogradouro) {
        $stFiltro .= " AND  UPPER( sw_nome_logradouro.nom_logradouro ) ";
        $stFiltro .= "LIKE UPPER( '%".$this->stNomeLogradouro."%' ) ";
    }
    if ($this->inCodigoUF) {
        $stFiltro .= " AND sw_logradouro.cod_uf = ".$this->inCodigoUF." ";
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= " AND sw_logradouro.cod_municipio = ".$this->inCodigoMunicipio." ";
    }
        if ($this->inSequencia) {
        $stFiltro .= " AND vw_trecho_ativo.sequencia = ".$this->inSequencia;
    }

        $stFiltro .= " GROUP BY vw_trecho_ativo.cod_trecho
                                , vw_trecho_ativo.cod_logradouro
                                , sw_tipo_logradouro.nom_tipo
                                , sw_nome_logradouro.nom_logradouro
                                , sw_logradouro.cod_logradouro
                                , vw_trecho_ativo.sequencia
                                , vw_trecho_ativo.extensao";

    $stOrder = " ORDER BY sw_nome_logradouro.nom_logradouro, vw_trecho_ativo.cod_logradouro, vw_trecho_ativo.sequencia ";
    $obErro = $this->obVCIMTrechoAtivo->recuperaTrechos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados o Trecho selecionado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function consultarTrecho(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $stOrder = "";
    if ($this->inSequencia) {
        $stFiltro  .= " AND vw_trecho_ativo.sequencia = ".$this->inSequencia;
    }
    if ($this->inCodigoTrecho) {
        $stFiltro .= " AND vw_trecho_ativo.cod_trecho = ".$this->inCodigoTrecho;
    }
    $stFiltro .= " AND vw_trecho_ativo.cod_logradouro = ".$this->inCodigoLogradouro;
    $obErro = $this->obVCIMTrechoAtivo->recuperaTrechos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->flExtensao     = $rsRecordSet->getCampo( "extensao"  );
        $this->inSequencia    = $rsRecordSet->getCampo( "sequencia" );
        $this->inCodigoTrecho = $rsRecordSet->getCampo( "cod_trecho" );
        $obErro = parent::consultarLogradouro( $rsLogradouro );
        unset( $rsLogradouro );
    }

    return $obErro;
}

function verificaBaixaTrecho(&$rsBaixaImovel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inSequencia) {
        $stFiltro  .= " AND t.sequencia = ".$this->inSequencia;
    }

    if ($this->inCodigoTrecho) {
        $stFiltro .= " AND t.cod_trecho = ".$this->inCodigoTrecho;
    }

    if ($this->inCodigoLogradouro) {
        $stFiltro .= " AND t.cod_logradouro = ".$this->inCodigoLogradouro;
    }

    $this->obTCIMTrecho->recuperaTrechoBaixado( $rsBaixaImovel, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados o Trecho selecionado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function validaFaceQuadraTrecho(&$rsRecordSet, $boTransacao = "")
{
    $obRCIMFaceQuadra = new RCIMFaceQuadra;
    $obRCIMFaceQuadra->obRCIMTrecho->setCodigoLogradouro( $this->inCodigoLogradouro );
    $obRCIMFaceQuadra->obRCIMTrecho->setCodigoTrecho    ( $this->inCodigoTrecho     );
    $obRCIMFaceQuadra->listarFaceQuadraTrecho( $rsRecordSet );
}

}
?>
