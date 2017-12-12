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
     * Classe de regra de negócio para logradouro
     * Data de Criação: 09/08/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Gustavo Passos Tourinho
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMLogradouro.class.php 63920 2015-11-09 12:18:49Z evandro $

     * Casos de uso: uc-05.01.04
                     uc-04.04.07
*/

/*
$Log$
Revision 1.10  2007/07/09 21:35:02  cercato
alteracao para o cgm funcionar da mesma forma que no cadastro economico e utilizar as novas tabelas sw_cgm_logradouro e sw_cgm_logradouro_correspondencia.

Revision 1.9  2006/10/26 16:02:09  dibueno
Alterações para alteração/exclusão de logradouro

Revision 1.8  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"             );

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoLogradouro.class.php"       );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"               );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"        );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoBairroLogradouro.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCEPLogradouro.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoTipoLogradouro.class.php"   );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoNomeLogradouro.class.php"   );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCEP.class.php"              );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMLogradouro.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelCorrespondencia.class.php"      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMDomicilioInformado.class.php"      	);

class RCIMLogradouro
{
/**
    * @access Private
    * @var Object
*/
var $obTLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTBairroLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTCEPLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTTipoLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTNomeLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovelCorrespondencia;
/**
    * @access Private
    * @var Object
*/
var $obTCEMDomicilioInformado;
/**
    * @access Private
    * @var Object
*/
var $obTCEP;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTUF;
/**
    * @access Private
    * @var Object
*/
var $obTMunicipio;
/**
    * @access Private
    * @var Object
*/
var $obRCIMBairro;
/**
    * @access Private
    * @var String
*/
var $stNomeLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoMunicipio;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoUF;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoTipo;
/**
    * @access Private
    * @var Array
*/
var $arBairro;
/**
    * @access Private
    * @var Array
*/
var $arCEP;

var $arDadosHistorico;

function setDadosHistorico($valor) { $this->arDadosHistorico = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeLogradouro($valor) { $this->stNomeLogradouro   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoLogradouro($valor) { $this->inCodigoLogradouro = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoMunicipio($valor) { $this->inCodigoMunicipio  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoUF($valor) { $this->inCodigoUF         = $valor; }
/**
    * @access Public
    * @param Integer $valor'
*/
function setCodigoTipo($valor) { $this->inCodigoTipo       = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setCEP($valor) { $this->arCEP              = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setBairro($valor) { $this->arBairro             = $valor; }

function getDadosHistorico() { return $this->arDadosHistorico; }
/**
    * @access Public
    * @return String
*/
function getNomeLogradouro() { return $this->stNomeLogradouro;   }
/**
    * @access Public
    * @return Integer
*/
function getCodigoLogradouro() { return $this->inCodigoLogradouro; }
/**
    * @access Public
    * @return Integer
*/
function getCodigoMunicipio() { return $this->inCodigoMunicipio;  }
/**
    * @access Public
    * @return Integer
*/
function getCodigoUF() { return $this->inCodigoUF;         }
/**
    * @access Public
    * @return Integer
*/
function getCodigoTipo() { return $this->inCodigoTipo;       }
/**
    * @access Public
    * @return Array
*/
function getCEP() { return $this->arCEP;              }
/**
    * @access Public
    * @return Array
*/
function getBairro() { return $this->arBairro;           }

/**
     * Método construtor
     * @access Private
*/
function RCIMLogradouro()
{
    $this->obTLogradouro        = new TLogradouro;
    $this->obTransacao          = new Transacao;
    $this->obTBairroLogradouro  = new TBairroLogradouro;
    $this->obTCEP               = new TCEP;
    $this->obTCEPLogradouro     = new TCEPLogradouro;
    $this->obTTipoLogradouro    = new TTipoLogradouro;
    $this->obTNomeLogradouro    = new TNomeLogradouro;
    $this->obTUF                = new TUF;
    $this->obTMunicipio         = new TMunicipio;
    $this->arBairro             = array();
    $this->arCEP                = array();
    $this->obRCIMBairro         = new RCIMBairro;
    $this->obTCGMLogradouro     = new TCGMLogradouro();

  $this->obTCIMImovelCorrespondencia 	= new TCIMImovelCorrespondencia;
  $this->obTCEMDomicilioInformado 	= new TCEMDomicilioInformado;
}

/**
    * Inclui os dados setados na tabela de Logradouro, Nome_Logradouro, Bairro e CEP
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirLogradouro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $arDadosHistorico = $this->getDadosHistorico();
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arBairro ) < 1 ) {
            $obErro->setDescricao( "Deve ser informado ao menos um bairro!" );
        } elseif ( count( $this->arCEP ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos um CEP!" );
        } else {
            $obErro = $this->validaNomeLogradouro( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                $this->obTLogradouro->setDado( "cod_uf"        , $this->inCodigoUF         );
                $this->obTLogradouro->setDado( "cod_municipio" , $this->inCodigoMunicipio  );
                $obErro = $this->obTLogradouro->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    foreach ($arDadosHistorico as $key => $value) {
                        $this->obTNomeLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                        $this->obTNomeLogradouro->setDado( "cod_tipo"      , $this->inCodigoTipo       );
                        $this->obTNomeLogradouro->setDado( "nom_logradouro", $value['nome_anterior']   );
                        $this->obTNomeLogradouro->setDado( "dt_inicio"     , $value['dt_inicio']   );
                        $this->obTNomeLogradouro->setDado( "dt_fim"        , $value['dt_fim']   );
                        $this->obTNomeLogradouro->setDado( "cod_norma"     , $value['cod_norma']   );
                        $obErro = $this->obTNomeLogradouro->inclusao ( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarBairroLogradouro( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->salvarCEPs( $boTransacao );
                        }
                    }    
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLogradouro );

    return $obErro;
}

/**
    * Altera os dados do Logradouro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLogradouro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $arDadosHistorico = $this->getDadosHistorico();
    $arDadosHistorico = end($arDadosHistorico);
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arBairro ) < 1 ) {
            $obErro->setDescricao( "Deve ser informado ao menos um bairro!" );
        } elseif ( count( $this->arCEP ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos um CEP!" );
        } else {
            $obErro = $this->validaNomeLogradouro( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arDados = $this->validaAlteracao($boTransacao);
                foreach ( $arDados as $key => $value) {
                    $this->obTNomeLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                    $this->obTNomeLogradouro->setDado( "cod_tipo"      , $this->inCodigoTipo       );
                    $this->obTNomeLogradouro->setDado( "nom_logradouro", $value['nome_anterior']   );
                    $this->obTNomeLogradouro->setDado( "dt_inicio"     , $value['dt_inicio']   );
                    $this->obTNomeLogradouro->setDado( "dt_fim"        , $value['dt_fim']   );
                    $this->obTNomeLogradouro->setDado( "cod_norma"     , $value['cod_norma']   );    

                    if ( $arDadosHistorico['sequencial'] == $value['sequencial'] ) {
                        $obErro = $this->obTNomeLogradouro->exclusao( $boTransacao );        
                        if ( !$obErro->ocorreu() ) {
                            $this->obTNomeLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                            $this->obTNomeLogradouro->setDado( "cod_tipo"      , $this->inCodigoTipo       );
                            $this->obTNomeLogradouro->setDado( "nom_logradouro", $_REQUEST['stNomeLogradouro'] );
                            $this->obTNomeLogradouro->setDado( "dt_inicio"     , $_REQUEST['stDataInicial'] );
                            $this->obTNomeLogradouro->setDado( "dt_fim"        , $_REQUEST['stDataFinal'] );
                            $this->obTNomeLogradouro->setDado( "cod_norma"     , $_REQUEST['inCodNorma'] );
                            $obErro = $this->obTNomeLogradouro->inclusao ( $boTransacao );    
                        }
                    }                    

                    if ( $value['confirma_alterar'] == 'true' && array_key_exists("sequencial", $value)) {
                        $obErro = $this->obTNomeLogradouro->alteracao( $boTransacao );    
                    }else{
                        $obErro = $this->obTNomeLogradouro->inclusao ( $boTransacao );
                    }
                
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarBairroLogradouro( $botransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->salvarCEPs( $botransacao );
                        }
                    }    
                }
                
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTNomeLogradouro );

    return $obErro;
}

/**
    * Exclui os dados do Logradouro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLogradouro($boTransacao = "")
{
    $boFlagTransacao = false;
    $boFlagExclusao = true;

    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTCEMDomicilioInformado->setDado ("cod_logradouro"  , $this->inCodigoLogradouro );
        $this->obTCEMDomicilioInformado->setDado ("cod_municipio"	, $this->inCodigoMunicipio	);
        $this->obTCEMDomicilioInformado->setDado ("cod_uf"		    , $this->inCodigoUF 		);
        $stFiltro = " WHERE cod_uf = ".$this->inCodigoUF;
        $stFiltro .=" AND cod_municipio = ". $this->inCodigoMunicipio;
        $stFiltro .=" AND cod_logradouro = ". $this->inCodigoLogradouro;
        $this->obTCEMDomicilioInformado->recuperaTodos ( $rsDomicilioI, $stFiltro, null, $boTransacao );
        if ( $rsDomicilioI->getNumLinhas() > 0 ) {
            $boFlagExclusao = false;
            $obErro->setDescricao('Logradouro utilizado como <b>Domicilio Informado</b> por alguma inscrição econômica.');
        } 
    }

    if (!$obErro->ocorreu()) {
        $this->obTCGMLogradouro->setDado( "cod_uf"          , $this->inCodigoUF        );
        $this->obTCGMLogradouro->setDado( "cod_municipio"   , $this->inCodigoMunicipio );
        $this->obTCGMLogradouro->setDado( "cod_logradouro"  , $this->inCodigoLogradouro );
        $this->obTCGMLogradouro->recuperaLogradouroCgm($rsLogradouro,"","",$boTransacao);
        if ( $rsLogradouro->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Logradouro está vinculado a um CGM.");
            $boFlagExclusao = false;
        }
    }

    if ($boFlagExclusao == true) {
        $this->obTCIMImovelCorrespondencia->setDado ("cod_logradouro", $this->inCodigoLogradouro );
        $this->obTCIMImovelCorrespondencia->setDado ("cod_municipio", 	$this->inCodigoMunicipio );
        $this->obTCIMImovelCorrespondencia->setDado ("cod_uf", $this->inCodigoUF );
        $obErro = $this->obTCIMImovelCorrespondencia->exclusao ( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTBairroLogradouro->setDado("cod_logradouro", $this->inCodigoLogradouro);
            $obErro = $this->obTBairroLogradouro->exclusao ( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $this->obTCEPLogradouro->setDado("cod_logradouro", $this->inCodigoLogradouro);
                $obErro = $this->obTCEPLogradouro->exclusao ( $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $this->obTNomeLogradouro->setDado("cod_logradouro", $this->inCodigoLogradouro  );
                    $obErro = $this->obTNomeLogradouro->exclusao ( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                        $this->obTLogradouro->setDado( "cod_uf",         $this->inCoidogoUF        );
                        $this->obTLogradouro->setDado( "cod_municipio",  $this->inCodigoMunicipio  );
                        $obErro = $this->obTLogradouro->exclusao( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTLogradouro );

    return $obErro;
}


/**
    * renomeia o Logradouro setado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function renomearLogradouro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTNomeLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
        $this->obTNomeLogradouro->setDado( "cod_tipo"      , $this->inCodigoTipo       );
        $this->obTNomeLogradouro->setDado( "nom_logradouro", $this->stNomeLogradouro   );
        $obErro = $this->validaNomeLogradouro( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTNomeLogradouro->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->salvarBairroLogradouro( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->salvarCEPs( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTNomeLogradouro );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Logradouro selecionado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLogradouro(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLogradouro) {
        $stFiltro .= " AND L.cod_logradouro = ".$this->inCodigoLogradouro;
    }
    $obErro = $this->obTLogradouro->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->stNomeLogradouro   = $rsRecordSet->getCampo( "nom_logradouro" );
        $this->inCodigoLogradouro = $rsRecordSet->getCampo( "cod_logradouro" );
        $this->inCodigoMunicipio  = $rsRecordSet->getCampo( "cod_municipio"  );
        $this->inCodigoUF         = $rsRecordSet->getCampo( "cod_uf"         );
        $this->inCodigoTipo       = $rsRecordSet->getCampo( "cod_tipo"       );
    }

    return $obErro;
}

/**
    * Lista os Logradouros conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLogradouros(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoLogradouro() ) {
        $stFiltro .= " AND L.cod_logradouro = ".$this->getCodigoLogradouro()." ";
    }
    if ( $this->getNomeLogradouro() ) {
        $stFiltro .= " AND UPPER( NL.nom_logradouro ) ";
        $stFiltro .= "LIKE UPPER( '".$this->getNomeLogradouro()."%' ) ";
    }
    if ( $this->getCodigoUF() ) {
        $stFiltro .= "  AND L.cod_uf = ".$this->getCodigoUF()." ";
    }
    if ( $this->getCodigoMunicipio() ) {
        $stFiltro .= "  AND L.cod_municipio = ".$this->getCodigoMunicipio()." ";
    }
    if ( $this->getCEP() ) {
        $stFiltro .= " AND imobiliario.fn_consulta_cep(L.cod_logradouro) = ".$this->getCEP()."::VARCHAR ";   
    }
    if ( $this->getBairro() ) {
        $stFiltro .= " AND B.cod_bairro = ".$this->getBairro();   
    }
    
    $stOrder = " ORDER BY NL.nom_logradouro ";
    $obErro = $this->obTLogradouro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista os Logradouros conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarHistoricoLogradouros(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " WHERE sw_logradouro.cod_logradouro = ".$this->inCodigoLogradouro." ";

    if ($this->stNomeLogradouro) {
        $stFiltro .= " AND UPPER( sw_nome_logradouro.nom_logradouro ) ";
        $stFiltro .= "LIKE UPPER( '".htmlentitles($this->stNomeLogradouro, ENT_QUOTES, 'UTF-8')."%' ) ";
    }
    if ($this->inCodigoUF) {
        $stFiltro .= "  AND sw_logradouro.cod_uf = ".$this->inCodigoUF." ";
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= "  AND sw_logradouro.cod_municipio = ".$this->inCodigoMunicipio." ";
    }
    $stOrder = " ORDER BY dt_inicio ASC, sw_nome_logradouro.nom_logradouro ";
    $obErro = $this->obTLogradouro->recuperaHistoricoLogradouro( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Valida se o nome do logradouro não existe na cidade infornmada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validaAlteracao($boTransacao = "")
{
    $stFiltro = " WHERE sw_logradouro.cod_logradouro = ".$this->inCodigoLogradouro." ";
    $stFiltro .= " AND sw_logradouro.cod_uf = ".$this->getCodigoUF()." ";
    $stFiltro .= " AND sw_logradouro.cod_municipio = ".$this->inCodigoMunicipio." ";
    $stFiltro .= " AND sw_tipo_logradouro.cod_tipo = ".$this->inCodigoTipo;
    $stOrder = "";
    $obErro = $this->obTLogradouro->recuperaHistoricoLogradouro( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    
    $arDadosHistorico = $this->getDadosHistorico();
    $arDadosConsulta = $rsRecordSet->getElementos();

    foreach ($arDadosHistorico as $chave => $historico) {
        foreach ($arDadosConsulta as $key => $value) {
            if ( $historico['sequencial'] == $value['sequencial'] ) {
                $arDadosHistorico[$chave]['confirma_alterar'] = "true";
            }   
        }
    }

    return $arDadosHistorico;
}   
    

/**
    * Valida se o nome do logradouro não existe na cidade infornmada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validaNomeLogradouro($boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoLogradouro) {
        //ENTRA NO FILTRO SOMENTE NO CASO DE ALTERACAO
        $stFiltro .= " AND L.cod_logradouro <> ".$this->inCodigoLogradouro." ";
    }
    $stFiltro .= " AND UPPER( NL.nom_logradouro ) ";
    $stFiltro .= "LIKE UPPER( '".htmlentities($this->stNomeLogradouro, ENT_QUOTES, 'UTF-8')."' )";
    $stFiltro .= " AND L.cod_uf = ".$this->getCodigoUF()." ";
    $stFiltro .= " AND L.cod_municipio = ".$this->inCodigoMunicipio." ";
    $stFiltro .= " AND TL.cod_tipo = ".$this->inCodigoTipo;
    $stOrder = "";
    $obErro = $this->obTLogradouro->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe um logradouro do tipo informado cadastrado com este nome neste muncípio!" );
    }

    return $obErro;
}

/**
    * Lista os Tipo de Logradouros conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarTipoLogradouro(&$rsRecordSet, $boTransacao = "")
{
   $stFiltro = "";
   if ($this->inCodigoTipo) {
       $stFiltro = " cod_tipo = ".$this->inCodigoTipo." AND ";
   }
   if ($stFiltro) {
       $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
   }
   $stOrdem  = " ORDER BY nom_tipo ";
   $obErro = $this->obTTipoLogradouro->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

//CEPS
/**
    * Lista todos os cep_logradouros
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCEP(&$rsRecordSet, $boTransacao = "")
{
    if ($this->inCodigoLogradouro) {
        $stFiltro  = " WHERE cod_logradouro = ".$this->inCodigoLogradouro." ";
    }
    $obErro = $this->obTCEPLogradouro->recuperaTodosNumeracao ( $rsRecordSet, $stFiltro,"", $boTransacao );

    return $obErro;
}

/**
    * Inclui o CEP setado na tabela de CEP
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCEP($inCEP, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEP->setDado("cep", $inCEP);
        $obErro = $this->obTCEP->inclusao ( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Inclui o CEP setado na tabela de CEP_Logradouro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirCEPLogradouro($arCEP, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTCEPLogradouro->setDado( "cep",            $arCEP["cep"]             );
        $this->obTCEPLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
        $this->obTCEPLogradouro->setDado( "num_inicial",    $arCEP["num_inicial"]     );
        $this->obTCEPLogradouro->setDado( "num_final",      $arCEP["num_final"]       );
        $this->obTCEPLogradouro->setDado( "par",            $arCEP["par"]             );
        $this->obTCEPLogradouro->setDado( "impar",          $arCEP["impar"]           );
        $obErro = $this->obTCEPLogradouro->inclusao ( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Exclui o CEP setado na tabela de CEP_Logradouro
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirCEPLogradouro($inCEP, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if(!$obErro->ocorreu() ){
        $stFiltro  = " WHERE cod_logradouro = ".$this->inCodigoLogradouro;
        $stFiltro .= " AND cep = '".$inCEP."'";
        
        $obErro=$this->obTCEPLogradouro->recuperaRelacionamentoCGMLogradouro ( $rsCGMLogradouro, $stFiltro, "", $boTransacao );
        if($rsCGMLogradouro->getNumLinhas()>0){
            $obErro->setDescricao("Não é possível excluir o cep ".$inCEP);
        }
    }

    if ( !$obErro->ocorreu() ) {
        $this->obTCEPLogradouro->setDado( "cep",            $inCEP             );
        $this->obTCEPLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
        $obErro = $this->obTCEPLogradouro->exclusao ( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Faz a verificação se o cep já esta cadastrado no sistema e inclui na tabela CEP_LOGRADOURO
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarCEPs($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE CL.cod_logradouro = ".$this->inCodigoLogradouro;
        $obErro = $this->obTCEPLogradouro->recuperaRelacionamento( $rsLogradouro, $stFiltro, "", $boTransacao );
        //$this->obTCEPLogradouro->debug();
        if ( !$obErro->ocorreu() ) {
            //MONTA ARRAY DE CEPS PARA VERIFICAR A EXCLUSAO
            $arCEPExclusao = array();
            while ( !$rsLogradouro->eof() ) {
                $arCEPExclusao[$rsLogradouro->getCampo("cep")] = true;
                $rsLogradouro->proximo();
            }
            //SALVA OS CEPs
            if ( !$obErro->ocorreu() ) {
                foreach ($this->arCEP as $inIndice => $arCEP) {
                    if ( !isset( $arCEPExclusao[$arCEP["cep"]] ) ) {
                        $this->obTCEP->setDado( "cep", $arCEP["cep"] );
                        $obErro = $this->obTCEP->recuperaPorChave( $rsValidaCEP, $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        } else {
                            if ( $rsValidaCEP->eof() ) {
                                $obErro = $this->obTCEP->inclusao( $boTransacao );
                            }
                            if ( !$obErro->ocorreu() ) {
                                $obErro = $this->incluirCEPLogradouro( $arCEP, $boTransacao );
                            } else {
                                break;
                            }
                        }
                    } else {
                        unset($arCEPExclusao[$arCEP["cep"]]);
                    }
                }
            }
            //EXCLUI OS CEPS DA TABELA DE RELACIONAMENTO
            foreach ($arCEPExclusao AS $inCEP => $boValue) {
                $obErro = $this->excluirCEPLogradouro( $inCEP, $boTransacao );           

                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}
//FIM CEPS

//BAIRROS
/**
    * Adiciona objetos de Bairro no objeto logradouro
    * @access Public
    * @param  Array  $arChaveBairro Array com a chave do bairro
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function addBairro($arChaveBairros, $boTransacao = "")
{
   $obRCIMBairro = new RCIMBairro;
   if (is_array($arChaveBairros)) {

        foreach ($arChaveBairros as $arChaveBairro) {
            $obRCIMBairro->setCodigoBairro    ( $arChaveBairro["cod_bairro"]    );
            $obRCIMBairro->setCodigoUF        ( $arChaveBairro["cod_uf"]        );
            $obRCIMBairro->setCodigoMunicipio ( $arChaveBairro["cod_municipio"] );
            $obErro = $obRCIMBairro->consultarBairro( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->arBairro[] = $arChaveBairro;
            }
        }
   } else {
        $arChaveBairro = $arChaveBairros;
        $obRCIMBairro->setCodigoBairro    ( $arChaveBairro["cod_bairro"]    );
        $obRCIMBairro->setCodigoUF        ( $arChaveBairro["cod_uf"]        );
        $obRCIMBairro->setCodigoMunicipio ( $arChaveBairro["cod_municipio"] );
        $obErro = $obRCIMBairro->consultarBairro( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->arBairro[] = $arChaveBairro;
        }
   }

   return $obErro;
}

/**
    * Lista os bairros segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarBairroLogradouro(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";//SE O COD_BAIRRO FOR INCLUIDO NESTE FILTRO O MÉTODO salvarBairrosLogradouro DEVE SER ALTERADO
    if ($this->inCodigoLogradouro) {
        $stFiltro .= " AND BL.cod_logradouro = ".$this->inCodigoLogradouro." ";
    }
    if ($this->inCodigoUF) {
        $stFiltro .= " AND BL.cod_uf = ".$this->inCodigoUF." ";
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= " AND BL.cod_municipio = ".$this->inCodigoMunicipio." ";
    }
    $obErro = $this->obTBairroLogradouro->recuperaRelacionamento ( $rsRecordSet, $stFiltro,"", $boTransacao );
    // $this->obTBairroLogradouro->debug();
    return $obErro;
}


/**
    * Faz a verificação se o bairro já esta relacionado ao logradouro e inclui ou exclui da tabela de relacionamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarBairroLogradouro($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarBairroLogradouro( $rsRecordSet , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            //MONTA UM ARRAY PARA SABER SE ALGUM BAIRRO DEVE SER EXCLUIDO
            $arBairroLogradouro = array();
            while ( !$rsRecordSet->eof() ) {
                $arBairroLogradouro[$rsRecordSet->getCampo("cod_bairro")] = true;
                $rsRecordSet->proximo();
            }
            //VERIFICA SE EXISTEM NOVOS BAIRROS PARA SEREM INCLUIDOS E SETA OS QUE NÃO DEVEM SER EXCLUIDOS
            $arDuplicados = array();
            foreach ($this->arBairro as $obRCIMBairro) {
                $inCodBairro = $obRCIMBairro["cod_bairro"];
                if ( $arBairroLogradouro[$inCodBairro] == "" && !in_array($inCodBairro, $arDuplicados) ) {
                    $arDuplicados[] = $inCodBairro;
                    $this->obTBairroLogradouro->setDado( "cod_uf",         $this->inCodigoUF         );
                    $this->obTBairroLogradouro->setDado( "cod_municipio",  $this->inCodigoMunicipio  );
                    $this->obTBairroLogradouro->setDado( "cod_bairro",     $inCodBairro );
                    $this->obTBairroLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                    $obErro = $this->obTBairroLogradouro->inclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                } else {
                    unset( $arBairroLogradouro[$inCodBairro] );
                }
            }
            //EXCLUI OS BAIRROS QUE NÃO FORAM SETADOS

            $inCount = 0;
            if ( !$obErro->ocorreu() && $_REQUEST['stAcao'] == 'alterar') {
                foreach ($arBairroLogradouro as $inCodigoBairro => $boValor) {
                    if ($boValor == true) {
                        $this->obTBairroLogradouro->setDado( "cod_uf",         $this->inCodigoUF         );
                        $this->obTBairroLogradouro->setDado( "cod_municipio",  $this->inCodigoMunicipio  );
                        $this->obTBairroLogradouro->setDado( "cod_bairro",     $inCodigoBairro           );
                        $this->obTBairroLogradouro->setDado( "cod_logradouro", $this->inCodigoLogradouro );
                        $obErro = $this->obTBairroLogradouro->exclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                        $inCount++;
                    }
                }
                if ($inCount == 0 && $inCodigoBairro != null) {
                    $obErro->setDescricao("Erro ao excluir Bairro! Bairro(".$inCodigoBairro.") possui Logradouros cadastrados");
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

//FIM BAIRROS

/**
    * Lista os Municipios conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMunicipios(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoUF) {
        $stFiltro .= " cod_uf = ".$this->inCodigoUF." AND ";
    }
    if ($this->inCodigoMunicipio) {
        $stFiltro .= " cod_municipio = ".$this->inCodigoMunicipio." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTMunicipio->recuperaTodos( $rsRecordSet, $stFiltro, "nom_municipio", $boTransacao );

    return $obErro;
}

/**
    * Lista as Unidades Federais conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUF(&$rsRecordSet, $boTransacao = "", $inCodPais = "")
{
    $stFiltro = "";
    if ($inCodPais) {
        $stFiltro = " cod_pais = ".$inCodPais." AND ";
    }

    if ($this->inCodigoUF) {
        $stFiltro .= " cod_uf = ".$this->inCodigoUF." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY nom_uf ";
    $obErro   = $this->obTUF->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
