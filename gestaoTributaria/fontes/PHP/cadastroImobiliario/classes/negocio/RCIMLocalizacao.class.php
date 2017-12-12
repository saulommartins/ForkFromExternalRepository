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
     * Classe de regra de negócio para localização
     * Data de Criação: 17/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMLocalizacao.class.php 64014 2015-11-18 17:13:21Z evandro $

     * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"               );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacaoNivel.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacao.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteLocalizacao.class.php"  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaLocalizacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMLocalizacaoAtiva.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacaoAliquota.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacaoValorM2.class.php" );

class RCIMLocalizacao extends RCIMNivel
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
var $inCodigoLocalizacao;
/**
    * @access Private
    * @var String
*/
var $stNomeLocalizacao;
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
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stValor;//tabela LOCALIZACAO_NIVEL
/**
    * @access Private
    * @var String
*/
var $stValorComposto;//valor de todos os niveis da localizacao concateneados
/**
    * @access Private
    * @var String
*/
var $stValorReduzido;//valor de todos os niveis que possuem localizacao
/**
    * @access Private
    * @var boolean
*/
var $boLocalizacaoAutomatica;
/**
    * @access Private
    * @var Object
*/

var $obTCIMLocalizacaoNivel;
/**
    * @access Private
    * @var Object
*/
var $obTCIMLocalizacao;
/**
    * @access Private
    * @var Object
*/
var $obVCIMLocalizacaoAtiva;
/**
    * @access Private
    * @var Object
*/
var $obTCIMBaixaLocalizacao;
/**
    * @access Private
    * @var Array
*/
var $arChaveLocalizacao;

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
function setCodigoLocalizacao($valor) { $this->inCodigoLocalizacao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeLocalizacao($valor) { $this->stNomeLocalizacao   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValor($valor) { $this->stValor             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido     = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setLocalizacaoAutomatica($valor) { $this->boLocalizacaoAutomatica = $valor; }
/**
    * @access Public
    * @return Integer
*/
function getCodigoLocalizacao() { return $this->inCodigoLocalizacao; }
/**
    * @access Public
    * @return String
*/
function getNomeLocalizacao() { return $this->stNomeLocalizacao;   }

/**
    * @access Public
    * @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
    * @access Public
    * @return String
*/
function getJustificativa() { return $this->stJustificativa;     }
/**
    * @access Public
    * @return String
*/
function getDataBaixa() { return $this->dtDataBaixa;        }
/**
    * @access Public
    * @return String
*/
function getValor() { return $this->stValor;            }
/**
    * @access Public
    * @return String
*/
function getValorComposto() { return $this->stValorComposto;    }
/**
    * @access Public
    * @return String
*/
function getValorReduzido() { return $this->stValorReduzido;    }
/**
    * @access Public
    * @return boolean
*/
function getLocalizacaoAutomatica() { return $this->boLocalizacaoAutomatica; }
/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    parent::RCIMNivel();
    $this->obTCIMLocalizacaoNivel = new TCIMLocalizacaoNivel;
    $this->obTCIMLocalizacao      = new TCIMLocalizacao;
    $this->obTCIMBaixaLocalizacao = new TCIMBaixaLocalizacao;
    $this->obVCIMLocalizacaoAtiva = new VCIMLocalizacaoAtiva;
    $this->arChaveLocalizacao    = array();
}

/**
    * Inclui os dados referentes a Localizacao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirLocalizacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        
        $obErro = $this->obTCIMLocalizacao->proximoCod( $this->inCodigoLocalizacao, $boTransacao );
        
        if ( !$obErro->ocorreu() ) {
            //MONTA O CODIGO COMPOSTO
            $obErro = $this->recuperaMascaraNiveis( $rsMascaraNivel, $boTransacao );
            $rsMascaraNivel->ordena("cod_nivel");
            $obErro = $this->consultarNivel( $boTransacao );            
            
            if ( empty($this->arChaveLocalizacao) ) {
                $stCodigoMascara  = $this->stValor.".";                
            }else{
                $stCodigoMascara  = $this->arChaveLocalizacao[ count($this->arChaveLocalizacao) - 1 ][3].".";
                $stCodigoMascara .= str_pad( $this->stValor, strlen($this->stMascara), "0", STR_PAD_LEFT );
            }

            $stMascaraComposta = '';
            $rsMascaraNivel->setPrimeiroElemento();
            $i = 1;
            $stCodigoComposto = '';
            while ( !$rsMascaraNivel->eof() ) {
                $stMascaraNivel = str_replace( "9", "0", $rsMascaraNivel->getCampo("mascara") );
                $stMascaraNivel = preg_replace("/[A-Za-z]/","0",$stMascaraNivel);
                $stCodigoComposto .= str_pad( $stMascaraNivel, strlen($stMascaraNivel), "0", STR_PAD_LEFT ).".";
                $rsMascaraNivel->proximo();
            }

            //Retira o '.' no final
            $stCodigoComposto = substr( $stCodigoComposto , 0, -1 );
            $stCodigoComposto = substr( $stCodigoComposto , strlen($stCodigoMascara) );
            $stCodigoComposto = $stCodigoMascara.$stCodigoComposto;

            //EXECUTA A INCLUSAO NA TABELA LOCALIZACAO
            $this->setValorComposto ( $stCodigoComposto );
            $obErro = $this->verificaNomeLocalizacao( $boTransacao );
            
            if ( !$obErro->ocorreu() )
                $obErro = $this->verificaCodigoComposto ( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $this->obTCIMLocalizacao->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                $this->obTCIMLocalizacao->setDado( "nom_localizacao", $this->stNomeLocalizacao   );
                $this->obTCIMLocalizacao->setDado( "codigo_composto", $stCodigoComposto          );
                $obErro = $this->obTCIMLocalizacao->inclusao( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    if ($this->dtAliquotaVigencia) {
                        $obTCIMLocalizacaoAliquota = new TCIMLocalizacaoAliquota;
                        $obTCIMLocalizacaoAliquota->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                        $obTCIMLocalizacaoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
                        $obTCIMLocalizacaoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
                        $obTCIMLocalizacaoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
                        $obTCIMLocalizacaoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
                        $obTCIMLocalizacaoAliquota->inclusao( $boTransacao );
                    }

                    if ($this->dtMDVigencia) {
                        $obTCIMLocalizacaoValorM2 = new TCIMLocalizacaoValorM2;
                        $obTCIMLocalizacaoValorM2->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                        $obTCIMLocalizacaoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
                        $obTCIMLocalizacaoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
                        $obTCIMLocalizacaoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
                        $obTCIMLocalizacaoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
                        $obTCIMLocalizacaoValorM2->inclusao( $boTransacao );
                    }

                    //LISTA OS NIVEIS EM RELAÇÃO A VIGÊNCIA SELECIONADA
                    $inCodigoNivelTmp = $this->inCodigoNivel;
                    $this->inCodigoNivel = "";
                    $obErro = $this->listarNiveis( $rsNiveis, $boTransacao );
                    $this->inCodigoNivel = $inCodigoNivelTmp;
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_vigencia",    $this->inCodigoVigencia    );
                        //EXECUTA A INCLUSAO DOS VALORES DAS LOCALIZACAOES NOS NIVEIS SUPERIORES AO CORRENTE
                        foreach ($this->arChaveLocalizacao as $arChaveLocalizacao) {
                            if ($arChaveLocalizacao[0] == $this->inCodigoNivel) //novo cc
                                break;

                            //[0] = cod_nivel | [1] = cod_localizacao | [2] = valor
                            $this->obTCIMLocalizacaoNivel->setDado( "cod_nivel"      , $arChaveLocalizacao[0] );
                            $this->obTCIMLocalizacaoNivel->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                            //Manda o valor sem mascara para o banco
                            $stValor = preg_replace( "/0/", "", trim( $arChaveLocalizacao[2] ) );
                            $this->obTCIMLocalizacaoNivel->setDado( "valor"          , $stValor );
                            $obErro = $this->obTCIMLocalizacaoNivel->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            if ( !$rsNiveis->eof() ) {
                                $rsNiveis->proximo();
                            }
                        }

                        //INCLUI O VALOR DA LOCALIZACAO NO NIVEL CORRENTE
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_nivel"      , $this->inCodigoNivel       );
                        $stValor = $this->stValor;
                        $this->obTCIMLocalizacaoNivel->setDado( "valor", $stValor );
                        $obErro = $this->obTCIMLocalizacaoNivel->inclusao( $boTransacao );
                        if ( !$rsNiveis->eof() ) {
                            $rsNiveis->proximo();
                        }

                        //INCLUI O VALOR DA LOCALIZACAO DOS NIVEIS SEGUINTES
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsNiveis->eof() ) {
                                $stValor = "0";
                                $this->obTCIMLocalizacaoNivel->setDado( "cod_nivel"      , $rsNiveis->getCampo("cod_nivel") );
                                $this->obTCIMLocalizacaoNivel->setDado( "valor", $stValor );
                                $obErro = $this->obTCIMLocalizacaoNivel->inclusao( $boTransacao );
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                if ( !$rsNiveis->eof() ) {
                                    $rsNiveis->proximo();
                                }
                            }
                        }
                        //CADASTRO DE ATRIBUTOS
                        if ( !$obErro->ocorreu() ) {
                            //O Restante dos valores vem setado da página de processamento
                            $arChavePersistenteValores = array( "cod_nivel" => $this->inCodigoNivel,
                                                                "cod_vigencia" => $this->inCodigoVigencia,
                                                                "cod_localizacao" => $this->inCodigoLocalizacao );
                            $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
                            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

    return $obErro;
}

/**
    * Altera os dados da Localizacao setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLocalizacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    //$obErro = $this->validaCodigoLocalizacao( $boTransacao );
    $obErro = $this->verificaNomeLocalizacao( $boTransacao, $this->inCodigoLocalizacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    if ($this->dtAliquotaVigencia) {
        $obTCIMLocalizacaoAliquota = new TCIMLocalizacaoAliquota;
        $obTCIMLocalizacaoAliquota->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
        $obTCIMLocalizacaoAliquota->setDado( "cod_norma", $this->inAliquotaCodNorma );
        $obTCIMLocalizacaoAliquota->setDado( "dt_vigencia", $this->dtAliquotaVigencia );
        $obTCIMLocalizacaoAliquota->setDado( "aliquota_territorial", $this->inAliquotaTerritorial );
        $obTCIMLocalizacaoAliquota->setDado( "aliquota_predial", $this->inAliquotaPredial );
        $obTCIMLocalizacaoAliquota->inclusao( $boTransacao );
    }

    if ($this->dtMDVigencia) {
        $obTCIMLocalizacaoValorM2 = new TCIMLocalizacaoValorM2;
        $obTCIMLocalizacaoValorM2->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
        $obTCIMLocalizacaoValorM2->setDado( "cod_norma", $this->inMDCodNorma );
        $obTCIMLocalizacaoValorM2->setDado( "dt_vigencia", $this->dtMDVigencia );
        $obTCIMLocalizacaoValorM2->setDado( "valor_m2_territorial", $this->inMDTerritorial );
        $obTCIMLocalizacaoValorM2->setDado( "valor_m2_predial", $this->inMDPredial );
        $obTCIMLocalizacaoValorM2->inclusao( $boTransacao );
    }

    $obErro = $this->consultarNivel( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    $obErro = $this->recuperaMascaraNiveis( $rsMascaraNivel, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    //montando codigo composto
    $arValRed = explode(".", $this->stValorReduzido);
    $rsMascaraNivel->ordena("cod_nivel");
    $rsMascaraNivel->setPrimeiroElemento();
    $stCodigoComposto = "";
    $inContador = 0;
    while ( !$rsMascaraNivel->eof() ) {
        $stMascaraNivel = str_replace( "9", "0", $rsMascaraNivel->getCampo("mascara") );
        $stMascaraNivel = preg_replace( "/[A-Za-z]/","0",$stMascaraNivel);

        if ($rsMascaraNivel->getCampo("cod_nivel") < $this->getCodigoNivel()) {
            $stCodigoComposto .= sprintf("%0".strlen($stMascaraNivel)."d", $arValRed[$inContador]).".";
        }else
            $stCodigoComposto .= $rsMascaraNivel->getCampo("cod_nivel") == $this->getCodigoNivel() ? sprintf( "%0".strlen($stMascaraNivel)."d", $this->stValor)."." : sprintf( "%0".strlen($stMascaraNivel)."d",$stMascaraNivel).".";

        $rsMascaraNivel->proximo();
        $inContador++;
    }

    $stCodigoComposto = substr( $stCodigoComposto, 0, strlen( $stMascara ) - 1);
    $stTempValorReduzido = $this->stValorReduzido;
    $this->stValorReduzido = "";
    $this->stValorComposto = $stCodigoComposto;

    $obErro = $this->listarNomLocalizacao( $rsListaLocais, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    $this->stValorReduzido = $stTempValorReduzido;
    $boSetarErro = false;
    while ( !$rsListaLocais->eof() ) {
        if ( $this->inCodigoLocalizacao != $rsListaLocais->getCampo("cod_localizacao") ) {
            //desconsiderando o local que esta sendo alterado agora
            $boSetarErro = true;
            break;
        }

        $rsListaLocais->proximo();
    }

    if ( $boSetarErro )
        $obErro->setDescricao("Localização '".$stCodigoComposto."' já cadastrada no sistema! ");

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    $this->obTCIMLocalizacaoNivel->setDado( "cod_vigencia"   , $this->inCodigoVigencia    );
    $this->obTCIMLocalizacaoNivel->setDado( "cod_nivel"      , $this->inCodigoNivel       );
    $this->obTCIMLocalizacaoNivel->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
    $this->obTCIMLocalizacaoNivel->setDado( "valor"          , $this->stValor             );
    $obErro = $this->obTCIMLocalizacaoNivel->alteracao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    $this->obTCIMLocalizacao->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
    $this->obTCIMLocalizacao->setDado( "nom_localizacao", $this->stNomeLocalizacao   );
    $this->obTCIMLocalizacao->setDado( "codigo_composto", $stCodigoComposto          );
    //$stCodigoComposto  = $this->arChaveLocalizacao[ count($this->arChaveLocalizacao) - 1 ][3].".";
    //$stCodigoComposto .= str_pad( $this->stValor, strlen($this->stMascara), "0", STR_PAD_LEFT );
    $obErro = $this->obTCIMLocalizacao->alteracao( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

        return $obErro;
    }

    //CADASTRO DE ATRIBUTOS
    //O Restante dos valores vem setado da página de processamento
    $arChavePersistenteValores = array( "cod_nivel" => $this->inCodigoNivel, "cod_vigencia" => $this->inCodigoVigencia, "cod_localizacao" => $this->inCodigoLocalizacao );
    $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
    $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );

    //UPDATE PARA ATUALIZAR OS CÓDIGO COMPOSTOS
    $this->obTCIMLocalizacao->setDado( "valor" , $this->getValorReduzido() );
    $obErro = $this->obTCIMLocalizacao->atualizaLocalizacao( $boTransacao );

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

    return $obErro;
}

/**
    * Exclui a Localizacao setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLocalizacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obTCIMLocalizacaoAliquota = new TCIMLocalizacaoAliquota;
        $obTCIMLocalizacaoAliquota->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
        $obTCIMLocalizacaoAliquota->exclusao( $boTransacao );

        $obTCIMLocalizacaoValorM2 = new TCIMLocalizacaoValorM2;
        $obTCIMLocalizacaoValorM2->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
        $obTCIMLocalizacaoValorM2->exclusao( $boTransacao );

        //CADASTRO DE ATRIBUTOS
        //O Restante dos valores vem setado da página de processamento
        $arChavePersistenteValores = array( "cod_nivel" => $this->inCodigoNivel,
                                            "cod_vigencia" => $this->inCodigoVigencia,
                                            "cod_localizacao" => $this->inCodigoLocalizacao );

        $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
        $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $inCodigoLocalizacaoTmp = $this->inCodigoLocalizacao;
            $inCodigoNivelTmp       = $this->inCodigoNivel;
    //        $this->inCodigoLocalizacao = "";
//            $this->inCodigoNivel       = "";
//deve verificar se a localizacao eh utilizada por algum lote!
            $obErro = $this->verificaFilhosLocalizacao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->inCodigoLocalizacao = $inCodigoLocalizacaoTmp;

            $this->setCodigoNivel("");
                $obErro = $this->listarNiveis( $rsNivel, $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    while ( !$rsNivel->eof() ) {
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_localizacao", $this->inCodigoLocalizacao      );
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_vigencia",    $this->inCodigoVigencia         );
                        $this->obTCIMLocalizacaoNivel->setDado( "cod_nivel",       $rsNivel->getCampo("cod_nivel") );
                        $obErro = $this->obTCIMLocalizacaoNivel->exclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                        $rsNivel->proximo();
                    }
                }
                $this->inCodigoNivel       = $inCodigoNivelTmp;
                //colocar aqui lista de validacao
                $obTCIMLoteLocalizacao = new TCIMLoteLocalizacao;
                $stFiltro = " WHERE cod_localizacao = ".$this->inCodigoLocalizacao;
                $obTCIMLoteLocalizacao->recuperaTodos( $rsListaLotes, $stFiltro, "", $boTransacao );
                if ( !$rsListaLotes->Eof() ) {
                    $obErro->setDescricao( "A localização é utilizada por um lote do sistema!" );
                }

                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMLocalizacao->setDado( "cod_localizacao", $this->inCodigoLocalizacao );
                    $obErro = $this->obTCIMLocalizacao->exclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

    return $obErro;
}

/**
    * Reativa o registro baixado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function reativarLocalizacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaLocalizacao->setDado ( "dt_termino", $dtdiaHOJE );
        $this->obTCIMBaixaLocalizacao->setDado ( "timestamp", $this->dtDataBaixa );
        $this->obTCIMBaixaLocalizacao->setDado ( "cod_localizacao", $this->inCodigoLocalizacao );
        $this->obTCIMBaixaLocalizacao->setDado ( "justificativa",  $this->stJustificativa );
        $this->obTCIMBaixaLocalizacao->setDado ( "justificativa_termino",  $this->stJustificativaReativar );

        $obErro = $this->obTCIMBaixaLocalizacao->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaLocalizacao );

    return $obErro;
}

/**
    * Insere o registro de baixa na tabela baixa_localizacao referente a Localizacao setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function baixarLocalizacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaFilhosLocalizacao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setCodigoVigencia ( $this->inCodigoVigencia );
            $inCodNivel = $this->inCodigoNivel;
            $this->inCodigoNivel = "";
            $obErro = $this->recuperaUltimoNivel ( $rsUltimoNivel, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$rsUltimoNivel->eof() ) {
                    if ( $inCodNivel == $rsUltimoNivel->getCampo("cod_nivel") ) {
                        //esta no ultimo nivel, verificar se tem lote
                        $obRCIMLote = new RCIMLote;
                        $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $this->inCodigoLocalizacao );
                        $obErro = $obRCIMLote->listarLotes( $rsLote, $boTransacao );
                        if ( !$obErro->ocorreu() )
                            if ( !$rsLote->eof() )
                                $obErro->setDescricao("Possui lote ativo neste nível!");
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $dtdiaHOJE = date ("d-m-Y");
                    $this->obTCIMBaixaLocalizacao->setDado( "dt_inicio"      , $dtdiaHOJE );
                    $this->obTCIMBaixaLocalizacao->setDado ( "cod_localizacao", $this->inCodigoLocalizacao );
                    $this->obTCIMBaixaLocalizacao->setDado ( "justificativa",   $this->stJustificativa     );

                    $obErro = $this->obTCIMBaixaLocalizacao->inclusao( $boTransacao );
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaLocalizacao );

    return $obErro;
}

function verificaBaixaLocalizacao(&$rsBaixaLocalizacao, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stValorComposto) {
        $stFiltro .= " AND l.codigo_composto like '".$this->stValorComposto."%' ";
    }

    $this->obTCIMLocalizacao->verificaBaixaLocalizacao( $rsBaixaLocalizacao, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Lista as Localizacoes segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLocalizacao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND";
    }
    if ($this->inCodigoLocalizacao) {
        $stFiltro .= " COD_LOCALIZACAO = ".$this->inCodigoLocalizacao." AND";
    }
    if ($this->stNomeLocalizacao) {
        $stFiltro .= " UPPER(NOM_LOCALIZACAO) LIKE UPPER('%".$this->stNomeLocalizacao."%') AND";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " UPPER(NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') AND";
    }

    if ($this->stValor < 0) {
        $stFiltro .= " valor != '".str_replace( "9", "0", -$this->stValor )."' AND";
    }

    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " valor_reduzido like '".$this->stValorReduzido."%' AND";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " valor_reduzido like '".$this->stValorReduzido."%' AND";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " valor_composto like '".$this->stValorComposto."%' AND";
    }
    if ($stFiltro) { $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, STRLEN( $stFiltro ) - 4 ); }
    $stOrdem = " ORDER BY valor_composto";
    
    $obErro = $this->obVCIMLocalizacaoAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    
    return $obErro;
}

function listarLocalizacaoPrimeiroNivel(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    $stOrdem = "";
    if ($this->inCodigoVigencia) {
        if ($this->inCodigoNivel) {
            $stFiltro = $this->inCodigoNivel.",".$this->inCodigoVigencia;
            //$stOrdem = " ORDER BY valor_composto";
            $obErro = $this->obTCIMLocalizacao->recuperaLocalizacaoPrimeiroNivel( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Localizacao Pai da Localizacao selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaPaiLocalizacao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stValorComposto) {
        $stFiltro .= " codigo_composto = '".$this->stValorComposto."' AND";
    }
    /*
    if ($this->stValorReduzido) {
        $stFiltro .= " valor_reduzido like '".substr($this->stValorReduzido,0,strlen($this->stValorReduzido))."%' AND";
    }
    */
    if ($stFiltro) { $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, STRLEN( $stFiltro ) - 4 ); }

    $stOrdem = " ORDER BY codigo_composto";
    $obErro = $this->obTCIMLocalizacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem,$boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Localizacao selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLocalizacao($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodigoLocalizacao) {
        $obErro = $this->listarLocalizacao( $rsLocalizacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stNomeLocalizacao = $rsLocalizacao->getCampo( "nom_localizacao" );
            $this->stNomeNivel       = $rsLocalizacao->getCampo( "nom_nivel" );
            $this->stMascara         = $rsLocalizacao->getCampo( "mascara" );
            $this->stValorComposto   = $rsLocalizacao->getCampo( "valor_composto" );
            $this->stValorReduzido   = $rsLocalizacao->getCampo( "valor_reduzido" );
            $arValor = explode( ".", $this->stValorReduzido );
            $this->stValor           = end( $arValor );
        }
    }

    return $obErro;
}

/**
    * Verifica se existem filhos da localizacao setadas, se houver retorna o erro informando
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaFilhosLocalizacao($boTransacao = "")
{
    $inCodigoLocalizacaoTmp = $this->inCodigoLocalizacao;
    $inCodigoNivelTmp       = $this->inCodigoNivel;
    $this->inCodigoLocalizacao = "";
    $this->inCodigoNivel       = "";
    $obErro = $this->listarLocalizacao( $rsListaLocalizacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsListaLocalizacao->eof() ) {
            $boTemFilhos = false;
            while ( !$rsListaLocalizacao->eof() ) {
                if ( $rsListaLocalizacao->getCampo("cod_nivel") > $inCodigoNivelTmp ) {
                    $boTemFilhos = true;
                    break;
                }

                $rsListaLocalizacao->proximo();
            }

            if ( $boTemFilhos == true)
                $obErro->setDescricao( "Existem localizações dependentes desta localização!" );
        }
    }

    $this->inCodigoLocalizacao = $inCodigoLocalizacaoTmp;
    $this->inCodigoNivel       = $inCodigoNivelTmp;

    return $obErro;
}

function validaCodigoLocalizacao($arValidaLocalizacao, $boTransacao = "")
{
    $stFiltro = "";

    if ($arValidaLocalizacao['cod_localizacao']) {
        $stFiltro .= " AND L.cod_localizacao <> ".$arValidaLocalizacao['cod_localizacao'];
    }
    if ($arValidaLocalizacao['cod_nivel']) {
        $stFiltro .= " AND LN.cod_nivel = ".$arValidaLocalizacao['cod_nivel'];
    }
    if ($arValidaLocalizacao['cod_vigencia']) {
        $stFiltro .= " AND LN.cod_vigencia = ".$arValidaLocalizacao['cod_vigencia'];
    }
    if ($arValidaLocalizacao['codigo_composto']) {
        $stFiltro .= " AND L.codigo_composto like '".$arValidaLocalizacao['codigo_composto']."%'";
    }
    if ($arValidaLocalizacao['valor']) {
        $stFiltro .= " AND LN.valor = ".$arValidaLocalizacao['valor'];
    }

    $obErro = $this->obTCIMLocalizacao->validaCodigoLocalizacao( $rsRecordSet, $stFiltro, "" , $boTransacao );

    if ( !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe uma localização cadastrada com o código ".$arValidaLocalizacao['valor']." para este nível.");
    }

    return $obErro;
}

/**
    * Adiciona no arrry  arChaveLocalizacao os códiogos das localizacao de niveis superiores
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function addCodigoLocalizacao($arChaveLocalizacao)
{
    $this->arChaveLocalizacao[] = $arChaveLocalizacao;//[0] = cod_nivel | [1] = cod_localizacao | [2] = valor
}

/**
    * Altera as caracteristicas da Localizacao setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCaracteristicas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //CADASTRO DE ATRIBUTOS
        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da página de processamento
            $arChavePersistenteValores = array( "cod_nivel" => $this->inCodigoNivel,
                                                "cod_vigencia" => $this->inCodigoVigencia,
                                                "cod_localizacao" => $this->inCodigoLocalizacao );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLocalizacao );

    return $obErro;
}

function listarNomLocalizacao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stValorReduzido) {
        $stFiltro .= " publico.fn_mascarareduzida(codigo_composto)='".$this->stValorReduzido."' AND";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " ln.cod_nivel = ".$this->inCodigoNivel." AND";
    }

    if ($this->inCodigoVigencia) {
        $stFiltro .= " ln.cod_vigencia = ".$this->inCodigoVigencia." AND";
    }

    if ($this->stValorComposto) {
        $stFiltro .= " codigo_composto = '".$this->stValorComposto."' AND";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, STRLEN( $stFiltro ) - 4 );
    }

    $obErro = $this->obTCIMLocalizacao->recuperaNomLocalizacao( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}
function consultaCodigoLocalizacao(&$inCodigoLocalizacao , $boTransacao = "")
{
    $obErro = new Erro;
    if ($this->stValorComposto || $this->stValorReduzido) {
        $obErro = $this->listarNomLocalizacao($rsLoc, $boTransacao);
        $inCodigoLocalizacao = $rsLoc->getCampo("cod_localizacao");
    } else {
        $obErro->setDescricao("Valor Composto da Localização deve ser setado!");
    }

    return $obErro;
}

//verifica se o nome já existe no nivel 1;
function verificaNomeLocalizacao($boTransacao = "", $inCodLocalizacao = "")
{
    $obErro = new Erro;
    //-------------------------
    $stValorRed = explode( ".", $this->stValorComposto );
    $stLoc = "";
    for ($inLoc = 0; $inLoc < $this->getCodigoNivel()-1; $inLoc++) {
        $stLoc .= $stValorRed[ $inLoc ].".";
    }

    if ( $this->getNomeLocalizacao() ) {
        $stFiltro = "";

        if ($this->inCodigoVigencia) {
            $stFiltro .= " ln.cod_vigencia = ".$this->inCodigoVigencia." AND";
        }

        if ($stLoc) {
            $stFiltro .= " codigo_composto like '".$stLoc."%' AND";
        } else {
            $this->setCodigoNivel("");

            $this->listarNiveis( $rsListaNivel, $boTransacao );
            $stLoc = "%";

            if ( !$rsListaNivel->eof() )
                $rsListaNivel->proximo();

            while ( !$rsListaNivel->eof() ) {
                $stLoc .= ".";
                for ($inLoc = 0; $inLoc < strlen($rsListaNivel->getCampo("mascara")); $inLoc++) {
                    $stLoc .= "0";
                }

                $rsListaNivel->proximo();
            }

            $this->setCodigoNivel(1);
            $stFiltro .= " codigo_composto like '".$stLoc."' AND";
        }

        if ($stFiltro) {
            $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, STRLEN( $stFiltro ) - 4 );
        }

        $obErro = $this->obTCIMLocalizacao->recuperaNomLocalizacao( $rsLoc, $stFiltro, "", $boTransacao );

        $stLoc1 = strtoupper( $this->getNomeLocalizacao() );
        $boSetarErro = false;
        if ( $rsLoc->getNumLinhas() > 0 ) {
            if ($inCodLocalizacao) {
                while ( !$rsLoc->eof() ) {
                    $stLoc2 = strtoupper( $rsLoc->getCampo("nom_localizacao") );
                    if ( $inCodLocalizacao != $rsLoc->getCampo("cod_localizacao") && $stLoc1 == $stLoc2 ) { //desconsiderando o local que esta sendo alterado agora
                        $boSetarErro = true;
                        break;
                    }

                    $rsLoc->proximo();
                }
            } else {
                while ( !$rsLoc->eof() ) {
                    $stLoc2 = strtoupper( $rsLoc->getCampo("nom_localizacao") );
                    if ($stLoc1 == $stLoc2) { //desconsiderando o local que esta sendo alterado agora
                        $boSetarErro = true;
                        break;
                    }

                    $rsLoc->proximo();
                }
            }

            if ($boSetarErro) {
                $stErro = "Localização já existe com o nome [ ".$this->getNomeLocalizacao()." ] para o nível (".$this->getCodigoNivel().").";
                if ( $this->getCodigoNivel() > 1 ) {
                    $arLoc = explode(".", $rsLoc->getCampo("codigo_composto") );
                    if ( $arLoc[ $this->getCodigoNivel()-1 ] == 0 ) {
                        $stErro = "Localização (".$rsLoc->getCampo("codigo_composto").") já existe com o nome [ ".$this->getNomeLocalizacao()." ].";
                    }
                }

                $obErro->setDescricao( $stErro );
            }
        }

    }

    return $obErro;
}

//verifica se o codigo composto já existe;
function verificaCodigoComposto($boTransacao = "")
{
    global $request;
    $obErro = new Erro;
    if ( $this->getValorComposto() ) {
        $obErro = $this->listarNomLocalizacao($rsLoc, $boTransacao);

        if ( $rsLoc->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Localização já existe com o código [ ".$request->get("inValorLocalizacao")." ] para este nível!");
        }

    }

    return $obErro;
}

/**
    * Retorna o valor do último codigo conforme filtros setados
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function ultimorValorComposto(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    $stOrdem = "";
    
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND";
    }
    
    if ($this->inCodigoNivel) {
        $this->obVCIMLocalizacaoAtiva->setDado("cod_nivel", $this->inCodigoNivel );
        $stFiltro .= " cod_nivel = ".$this->inCodigoNivel." AND";
    }
    
    if ($this->stValorReduzido && $this->inCodigoNivel != 1) {
        $stFiltro .= " valor_reduzido like '".$this->stValorReduzido."%' AND";
    }
    
    if ($stFiltro) { $stFiltro = " WHERE ".SUBSTR( $stFiltro, 0, STRLEN( $stFiltro ) - 4 ); }

    $obErro = $this->obVCIMLocalizacaoAtiva->recuperaUltimoValorComposto( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        
    return $obErro;
}

}

?>