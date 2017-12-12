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
     * Classe de regra de negócio para unidade autônoma
     * Data de Criação: 16/11/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMUnidadeAutonoma.class.php 60973 2014-11-26 17:17:21Z carolina $

     * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.16  2006/09/18 09:12:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMUnidadeAutonoma.class.php"           );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaUnidadeAutonoma.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMUnidades.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaUnidadeAutonoma.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaConstrucao.class.php"            );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoProcesso.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"                   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeDependente.class.php"            );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                        );

class RCIMUnidadeAutonoma
{
/**
* @access Private
* @var Float
*/
var $flAreaUnidade;

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
var $stTipoUnidade;
/**
* @access Private
* @var Object
*/
var $obTCIMUnidadeAutonoma;
/**
* @access Private
* @var Object
*/
var $obTCIMBaixaUnidadeAutonoma;
/**
* @access Private
* @var Object
*/
var $obVCIMUnidades;
/**
* @access Private
* @var Object
*/
var $obTCIMAreaUnidadeAutonoma;
/**
* @access Private
* @var Object
*/
var $obTCIMAreaConstrucao;
/**
* @access Private
* @var Object
*/
var $obRCIMEdificacao;
/**
    * @access Private
    * @var Array
*/
var $arRCIMUnidadeDependente;
/**
    * @access Private
    * @var Object
*/
var $roUltimaUnidadeDependente;
/**
    * @access Private
    * @var Object
*/
var $obUltimaUnidadeDependente;
/**
    * @access Private
    * @var Object
*/
var $roRCIMImovel;
/**
    * @access Private
    * @var Int
*/
var $inUnidadeSelecionada;
/**
* @access Private
* @var Object
*/
var $obRProcesso;
/**
* @access Private
* @var Timestamp
*/
var $tmTimestampUnidadeAutonoma;
/**
* @access Private
* @var Integer
*/
var $inCodigoTipoNovo;

/**
* @access Private
* @var Timestamp
*/
var $tmTimestampBaixaUnidade;

/**
    * @access Private
    * @var Int
*/

//SETTERS
/**
* @access Public
* @param Timestamp $valor
*/
function setTimestampBaixaUnidade($valor) { $this->tmTimestampBaixaUnidade = $valor;      }

/**
* @access Public
* @param Float $valor
*/
function setAreaUnidade($valor) { $this->flAreaUnidade = $valor;        }

/**
* @access Public
* @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor;      }

/**
* @access Public
* @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa = $valor;      }
/**
* @access Public
* @param String $valor
*/
function setTipoUnidade($valor) { $this->stTipoUnidade = $valor;        }
/**
* @access Public
* @param Int $valor
*/
function setUnidadeSelecionada($valor) { $this->inUnidadeSelecionada = $valor; }
/**
* @access Public
* @param String $valor
*/
function setCodigoProcesso($valor) { $this->inCodigoProcesso = $valor; }
/**
* @access Public
* @param String $valor
*/
function setExercicioProcesso($valor) { $this->inExercicioProcesso = $valor; }
/**
* @access Public
* @param Timestamp $valor
*/
function setTimestampUnidadeAutonoma($valor) { $this->tmTimestampUnidadeAutonoma = $valor; }
/**
* @access Public
* @param Timestamp $valor
*/
function setCodigoTipoNovo($valor) { $this->inCodigoTipoNovo = $valor; }

//GETTERS
/**
* @access Public
* @return Timestamp
*/
function getTimestampBaixaUnidade() { return $this->tmTimestampBaixaUnidade;  }

/**
* @access Public
* @return Float
*/
function getAreaUnidade() { return $this->flAreaUnidade;        }
/**
* @access Public
* @return String
*/
function getJustificativa() { return $this->stJustificativa;      }

/**
* @access Public
* @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
* @access Public
* @return String
*/
function getTipoUnidade() { return $this->stTipoUnidade;        }
/**
* @access Public
* @return Int
*/
function getUnidadeSelecionada() { return $this->inUnidadeSelecionada; }
/**
* @access Public
* @return Int
*/
function getTimestampUnidadeAutonoma() { return $this->tmTimestampUnidadeAutonoma; }
/**
* @access Public
* @return Int
*/
function getCodigoTipoNovo() { return $this->inCodigoTipoNovo; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMUnidadeAutonoma(&$obRCIMImovel)
{
    $this->roRCIMImovel = &$obRCIMImovel;
    $this->obTCIMUnidadeAutonoma     = new TCIMUnidadeAutonoma;
    $this->obVCIMUnidades            = new VCIMUnidades;
    $this->obTCIMAreaUnidadeAutonoma = new TCIMAreaUnidadeAutonoma;
    $this->obTCIMAreaConstrucao      = new TCIMAreaConstrucao;
    $this->obTCIMBaixaUnidadeAutonoma= new TCIMBaixaUnidadeAutonoma;
    $this->obTCIMConstrucaoProcesso  = new TCIMConstrucaoProcesso;
    $this->roUltimaUnidadeDependente = new RCIMUnidadeDependente( $this );
    $this->obRCIMEdificacao          = new RCIMEdificacao;
    $this->obRProcesso               = new RProcesso;
    $this->obTransacao               = new Transacao;
    $this->arRCIMUnidadeDependente   = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Unidade Autonoma
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirUnidadeAutonoma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $flAreaUnidade = str_replace( ".", "", $this->getAreaUnidade() );
        $flAreaUnidade = str_replace( ",", ".", $flAreaUnidade );
        $flAreaConstruida = str_replace( ".", "", $this->obRCIMEdificacao->getAreaConstruida() );
        $flAreaConstruida = str_replace( ",", ".", $flAreaConstruida );
        if ( $this->obRCIMEdificacao->getCodigoConstrucao() == "" ) {
            $obErro = $this->obRCIMEdificacao->incluirEdificacao( $boTransacao );
        } else {
            $this->obRCIMEdificacao->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obRCIMEdificacao->obTCIMAreaConstrucao->setDado( "area_real"      , $this->obRCIMEdificacao->flAreaConstruida      );
            $obErro = $this->obRCIMEdificacao->obTCIMAreaConstrucao->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
           $this->obTCIMUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao() );
           $this->obTCIMUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
           $this->obTCIMUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
           $obErro = $this->obTCIMUnidadeAutonoma->inclusao( $boTransacao );
           if ( !$obErro->ocorreu() ) {
              $this->obTCIMAreaUnidadeAutonoma->setDado( "inscricao_municipal" ,  $this->roRCIMImovel->getNumeroInscricao() );
              $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
              $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
              $this->obTCIMAreaUnidadeAutonoma->setDado( "area"                , $this->flAreaUnidade                           );
              $obErro = $this->obTCIMAreaUnidadeAutonoma->inclusao( $boTransacao );
              if ( !$obErro->ocorreu() ) {
                 $obErro = $this->salvarUnidadesDependentes( $boTransacao );
              }
           }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Unidade Autonoma
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarUnidadeAutonoma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $flAreaUnidade = str_replace( ".", "", $this->getAreaUnidade() );
        $flAreaUnidade = str_replace( ",", ".", $flAreaUnidade );
        $flAreaConstruida = str_replace( ".", "", $this->obRCIMEdificacao->getAreaConstruida() );
        $flAreaConstruida = str_replace( ",", ".", $flAreaConstruida );

        $obErro = $this->obRCIMEdificacao->alterarEdificacao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTCIMUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao() );
            $this->obTCIMUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obTCIMUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
            $obErro = $this->obTCIMUnidadeAutonoma->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMAreaUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao()        );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao()   );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()         );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "timestamp"           , $this->getTimestampUnidadeAutonoma()             );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "area"                , $this->flAreaUnidade                             );
                $obErro = $this->obTCIMAreaUnidadeAutonoma->alteracao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->editarUnidadesDependentes( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Unidade Autonoma
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirUnidadeAutonoma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->removerUnidadesDependentes( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTCIMBaixaUnidadeAutonoma = new TCIMBaixaUnidadeAutonoma;
            $obTCIMBaixaUnidadeAutonoma->setDado( "inscricao_municipal", $this->roRCIMImovel->getNumeroInscricao() );
            $obTCIMBaixaUnidadeAutonoma->setDado( "cod_construcao", $this->obRCIMEdificacao->getCodigoConstrucao() );
            $obTCIMBaixaUnidadeAutonoma->setDado( "cod_tipo", $this->obRCIMEdificacao->getCodigoTipo() );
            $obTCIMBaixaUnidadeAutonoma->exclusao( $boTransacao );

            $this->obTCIMAreaUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao() );
            $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
            $this->obTCIMAreaUnidadeAutonoma->setDado( "area"                , $this->flAreaUnidade                           );
            $obErro = $this->obTCIMAreaUnidadeAutonoma->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {    
                $this->obRCIMUnidadeDependente = new RCIMUnidadeDependente( $this );      
                $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $this->roRCIMImovel->getNumeroInscricao() );
                $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $this->obRCIMEdificacao->getCodigoConstrucao() );
                $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $this->obRCIMEdificacao->getCodigoTipo() );
                $this->obRCIMUnidadeDependente->listarUnidadesDependentes( $rsUnidadesDependentes,$boTransacao );
                if (!$rsUnidadesDependentes->eof()) {
                    $obErro->setDescricao( "Unidade possui dependentes!" );            
                } else {
                    $this->obTCIMUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao() );
                    $this->obTCIMUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
                    $this->obTCIMUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
                    $obErro = $this->obTCIMUnidadeAutonoma->exclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obRCIMEdificacao->excluirEdificacao( $boTransacao );
                } 
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Baixa a Unidade Autonoma setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarUnidadeAutonoma($boTransacao = "")
{
    $this->obRCIMUnidadeDependente = new RCIMUnidadeDependente( $this );
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $this->roRCIMImovel->getNumeroInscricao() );
        $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $this->obRCIMEdificacao->getCodigoConstrucao() );
        $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $this->obRCIMEdificacao->getCodigoTipo() );
        $this->obRCIMUnidadeDependente->listarUnidadesDependentes( $rsUnidadesDependentes,$boTransacao );
        if (!$rsUnidadesDependentes->eof()) {
            $obErro->setDescricao( "Unidade possui dependentes!" );

            return $obErro;
        }

        while ( !$rsUnidadesDependentes->eof() ) {
            $this->obRCIMUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao( $rsUnidadesDependentes->getCampo("cod_construcao_dependente") );
            $this->obRCIMUnidadeDependente->obRCIMEdificacao->listarEdificacoes( $rsEdificacao,$boTransacao );
            if ( $rsEdificacao->getNumLinhas() > 0 ) {
                $this->obRCIMUnidadeDependente->obRCIMEdificacao->setCodigoConstrucao( $rsUnidadesDependentes->getCampo("cod_construcao_dependente") );
                $this->obRCIMUnidadeDependente->setTipoConstrucao("Edificacao");
            } else {
                $this->obRCIMUnidadeDependente->obRCIMConstrucaoOutros->setCodigoConstrucao( $rsUnidadesDependentes->getCampo("cod_construcao_dependente") );
                $this->obRCIMUnidadeDependente->setTipoConstrucao("ConstrucaoOutras");
            }
            $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $rsUnidadesDependentes->getCampo("inscricao_municipal") );
//            $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $this->obRCIMEdificacao->getCodigoTipo());
            $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $rsUnidadesDependentes->getCampo("cod_tipo"));
            $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $rsUnidadesDependentes->getCampo("cod_construcao") );
            $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setSistema( true );
            $this->obRCIMUnidadeDependente->obRCIMEdificacao->setSistema( true );
            $this->obRCIMUnidadeDependente->setJustificativa( $this->getJustificativa() );
            $obErro = $this->obRCIMUnidadeDependente->baixarUnidadeDependente( $boTransacao );
            if( $obErro->ocorreu() ) break;
            $rsUnidadesDependentes->proximo();
        }

        if ( !$obErro->ocorreu() ) {
            $this->obRCIMEdificacao->setJustificativa( $this->getJustificativa() );
            $obErro = $this->obRCIMEdificacao->baixarConstrucao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $dtdiaHOJE = date ("d-m-Y");
                $this->obTCIMBaixaUnidadeAutonoma->setDado( "dt_inicio", $dtdiaHOJE );
                $this->obTCIMBaixaUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao()      );
                $this->obTCIMBaixaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
                $this->obTCIMBaixaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
                $this->obTCIMBaixaUnidadeAutonoma->setDado( "justificativa"       , $this->getJustificativa()                      );
                $obErro = $this->obTCIMBaixaUnidadeAutonoma->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Baixa a Unidade Autonoma setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function reativarUnidadeAutonoma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRCIMEdificacao->setJustificativa( $this->getJustificativa() );
        $this->obRCIMEdificacao->setDataConstrucao( $this->tmTimestampBaixaUnidade );
        $obErro = $this->obRCIMEdificacao->reativarConstrucao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $dtdiaHOJE = date ("d-m-Y");
            $this->obTCIMBaixaUnidadeAutonoma->setDado( "dt_termino", $dtdiaHOJE );
            $this->obTCIMBaixaUnidadeAutonoma->setDado( "timestamp",  $this->tmTimestampBaixaUnidade);
            $this->obTCIMBaixaUnidadeAutonoma->setDado( "inscricao_municipal", $this->roRCIMImovel->getNumeroInscricao() );

            $this->obTCIMBaixaUnidadeAutonoma->setDado( "cod_construcao", $this->obRCIMEdificacao->getCodigoConstrucao() );

            $this->obTCIMBaixaUnidadeAutonoma->setDado( "cod_tipo", $this->obRCIMEdificacao->getCodigoTipo() );
            $this->obTCIMBaixaUnidadeAutonoma->setDado( "justificativa", $this->getJustificativa() );
            $this->obTCIMBaixaUnidadeAutonoma->setDado( "justificativa_termino", $this->getJustificativaReativar() );

            $obErro = $this->obTCIMBaixaUnidadeAutonoma->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**:
* Baixa a Unidade Autonoma pela classe edificação
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarUnidadeAutonomaEdificacao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //Tranforma a unidade dependente selecionada em autonoma
    $inCodigoConstrucaoBaixar   = $this->obRCIMEdificacao->getCodigoConstrucao();
    $inNumeroInscricaoBaixar    = $this->roRCIMImovel->getNumeroInscricao();
    $inCodigoTipoBaixar         = $this->obRCIMEdificacao->getCodigoTipo();

    $this->obRCIMUnidadeDependente = new RCIMUnidadeDependente( $this );
    $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $inCodigoConstrucaoBaixar );
    $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao( $inNumeroInscricaoBaixar );
    $this->obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo( $inCodigoTipoBaixar );
    $this->obRCIMUnidadeDependente->listarUnidadesDependentes( $rsUnidadesDependentes, $boTransacao );
    if (!$rsUnidadesDependentes->eof()) {
        $obErro->setDescricao( "Unidade possui dependentes!" );

        return $obErro;
    }

    $this->obRCIMEdificacao->setCodigoTipo($this->getCodigoTipoNovo());
    if ( $this->getUnidadeSelecionada() ) {
        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
        $this->obRCIMEdificacao->obRProcesso->setCodigoProcesso  ( $arProcesso[0]   );
        $this->obRCIMEdificacao->obRProcesso->setExercicio       ( $arProcesso[1]   );
        $obErro = $this->obRCIMEdificacao->incluirEdificacao     ( $boTransacao     );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao()        );
            $this->obTCIMUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao()   );
            $this->obTCIMUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()         );
//            $this->obTCIMUnidadeAutonoma->setDado( "cod_tipo"            , $this->getCodigoTipoNovo()                       );
            $obErro = $this->obTCIMUnidadeAutonoma->inclusao         ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $stAreaConstruida = str_replace(",",".",$this->obRCIMEdificacao->getAreaConstruida());
//                $stAreaConstruida = str_replace(",",".",$stAreaConstruida);
                $this->obTCIMAreaUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao()      );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
                $this->obTCIMAreaUnidadeAutonoma->setDado( "area"                , $stAreaConstruida                              );
                $obErro = $this->obTCIMAreaUnidadeAutonoma->inclusao( $boTransacao );
//                $this->obRCIMEdificacao->setCodigoTipo($inCodigoTipoBaixar);
                if ( !$obErro->ocorreu() ) {
                    //Muda relação das unidades dependentes
                    $obRCIMUnidadeDependente = new RCIMUnidadeDependente( new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) ) );
//                    $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo        ( $_REQUEST["hdnCodigoTipo"]        );
                    $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo        ( $this->obRCIMEdificacao->getCodigoTipo()   );
                    $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao  ( $_REQUEST["hdnCodigoConstrucao"]  );
                    $obRCIMUnidadeDependente->roRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao       ( $_REQUEST["stImovelCond"]         );
                    $obRCIMUnidadeDependente->listarUnidadesDependentes( $rsUnidadesDependentes,$boTransacao );

                    if (!$rsUnidadesDependentes->eof()) {
                        $obErro->setDescricao( "Unidade possui dependentes!" );

                        return $obErro;
                    }

                    //$this->obRCIMEdificacao->setCodigoTipo($inCodigoTipoBaixar);
                    while ( !$rsUnidadesDependentes->eof() ) {
                        if ( $rsUnidadesDependentes->getCampo("cod_construcao_dependente") != $this->getUnidadeSelecionada() ) {
                            $this->addUnidadeDependente(  );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->setCodigoTipo                   ( $rsUnidadesDependentes->getCampo("cod_tipo_dependente")   );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->setAreaConstruida               ( $_REQUEST["flAreaConstruida"]                             );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->setDataConstrucao               ( $rsUnidadesDependentes->getCampo("data_construcao")       );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->setSistema                      ( true                                                      );
                            $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inProcesso"] );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setCodigoProcesso  ( $arProcesso[0]                                            );
                            $this->roUltimaUnidadeDependente->obRCIMEdificacao->obRProcesso->setExercicio       ( $arProcesso[1]                                            );
                            $this->roUltimaUnidadeDependente->setAreaUnidade                                    ( str_replace('.',',',$rsUnidadesDependentes->getCampo("area")));
                            $this->roRCIMImovel->setNumeroInscricao                                             ( $rsUnidadesDependentes->getCampo("inscricao_municipal")   );
                            $this->obRCIMEdificacao->setCodigoConstrucao                                        ( $this->obRCIMEdificacao->getCodigoConstrucao()            );
                            $this->obRCIMEdificacao->setCodigoTipo                                              ( $this->obRCIMEdificacao->getCodigoTipo()                  );

                        }
                        $rsUnidadesDependentes->proximo();
                    }
                }
            }
        }
        $obErro = $this->salvarUnidadesDependentes( $boTransacao );
    }

    if ( !$obErro->ocorreu() ) {
        //Baixar unidade autonoma
        $this->obRCIMEdificacao->setCodigoConstrucao( $inCodigoConstrucaoBaixar );
        $this->roRCIMImovel->setNumeroInscricao     ( $inNumeroInscricaoBaixar  );
        $this->obRCIMEdificacao->setCodigoTipo      ( $inCodigoTipoBaixar       );
        $obErro = $this->baixarUnidadeAutonoma( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Lista as Unidades Autonomas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarUnidadesAutonomas(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $stFiltro .= " cod_construcao_dependente = 0 AND ";
    if ( $this->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " inscricao_municipal = ".$this->roRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_municipal ";
    $obErro = $this->obVCIMUnidades->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Unidade Autonoma selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarUnidadeAutonoma($boTransacao = "")
{
    $stFiltro = "";
    $stFiltro .= " cod_construcao_dependente = 0 AND ";
    if ( $this->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " inscricao_municipal = ". $this->roRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_municipal ";
    $obErro = $this->obVCIMUnidades->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->obRCIMEdificacao->setCodigoConstrucao ( $rsRecordSet->getCampo( "cod_construcao"      ) );
        $this->obRCIMEdificacao->setCodigoTipo       ( $rsRecordSet->getCampo( "cod_tipo"            ) );
        $this->flAreaUnidade                         = $rsRecordSet->getCampo( "area"                  );
        $this->stTipoUnidade                         = $rsRecordSet->getCampo( "tipo_unidade"          );
    }

    return $obErro;
}

// METODOS DE INTERFACE
/**
* Verifica  a existência de uma unidade autônoma para a inscrição municipal informada,
*           retornando o cod_construcao e cod_tipo da mesma se verdadeiro
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function verificaUnidadeAutonoma(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " AND ua.inscricao_municipal = ".$this->roRCIMImovel->getNumeroInscricao();
    }
    $stOrder = " ORDER BY cod_construcao ";
    $obErro = $this->obTCIMUnidadeAutonoma->recuperaVerificaUnidadeAutonoma( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Adiciona uma Unidade Dependente à Unidade Autônoma setada
* @access Public
*/
function addUnidadeDependente()
{
    $this->arRCIMUnidadeDependente[] = new RCIMUnidadeDependente( $this );
    $this->roUltimaUnidadeDependente = &$this->arRCIMUnidadeDependente[ count($this->arRCIMUnidadeDependente) - 1 ];
}

/**
* Salva as Unidades Dependentes adicionadas à Unidade Autônoma setada
* @access Public
*/
function salvarUnidadesDependentes($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arRCIMUnidadeDependente as $obRCIMUnidadeDependente) {
            if ( $obRCIMUnidadeDependente->obRCIMEdificacao->getCodigoTipo() ) {
                $obErro = $obRCIMUnidadeDependente->obRCIMEdificacao->incluirEdificacao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRCIMUnidadeDependente->incluirUnidadeDependente( $boTransacao );
            }
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
    }
    if ( is_object( $obRCIMUnidadeDependente ) ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRCIMUnidadeDependente->getTCIMUnidadeDependente() );
    } else {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}

/**
* Edita as Unidades Dependentes adicionadas à Unidade Autônoma setada
* @access Public
*/
function editarUnidadesDependentes($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->roUltimaUnidadeDependente->obRCIMEdificacao->getCodigoConstrucao() ) {
            $obErro = $this->roUltimaUnidadeDependente->obRCIMEdificacao->alterarEdificacao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                foreach ($this->arRCIMUnidadeDependente as $obRCIMUnidadeDependente) {
                    $obErro = $obRCIMUnidadeDependente->alterarUnidadeDependente( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Edita as Unidades Dependentes adicionadas à Unidade Autônoma setada
* @access Public
*/
function removerUnidadesDependentes($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arRCIMUnidadeDependente as $obRCIMUnidadeDependente) {
            $obErro = $obRCIMUnidadeDependente->excluirUnidadeDependente( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->roUltimaUnidadeDependente->obRCIMEdificacao->getCodigoConstrucao() ) {
                    $obErro = $this->roUltimaUnidadeDependente->obRCIMEdificacao->excluirEdificacao( $boTransacao );
                }
            } else {
                break;
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Inclui os dados setados na tabela de Unidade Autonoma
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirReforma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $flAreaUnidade = str_replace( ".", "", $this->getAreaUnidade() );
        $flAreaUnidade = str_replace( ",", ".", $flAreaUnidade );
        $flAreaConstruida = str_replace( ".", "", $this->obRCIMEdificacao->getAreaConstruida() );
        $flAreaConstruida = str_replace( ",", ".", $flAreaConstruida );
//        if ($flAreaUnidade <= $flAreaConstruida) {
        $this->obTCIMAreaUnidadeAutonoma->setDado( "inscricao_municipal" , $this->roRCIMImovel->getNumeroInscricao() );
        $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_construcao"      , $this->obRCIMEdificacao->getCodigoConstrucao() );
        $this->obTCIMAreaUnidadeAutonoma->setDado( "cod_tipo"            , $this->obRCIMEdificacao->getCodigoTipo()       );
        $this->obTCIMAreaUnidadeAutonoma->setDado( "area"                , $this->flAreaUnidade                           );
        $obErro = $this->obTCIMAreaUnidadeAutonoma->inclusao( $boTransacao );
        if ( ! $obErro->ocorreu() ) {
            $this->obTCIMAreaConstrucao->setDado ( "cod_construcao" , $this->obRCIMEdificacao->getCodigoConstrucao()    );
            $this->obTCIMAreaConstrucao->setDado ( "area_real"      , $this->obRCIMEdificacao->getAreaConstruida()      );
            $obErro = $this->obTCIMAreaConstrucao->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->salvarProcesso( $boTransacao );
            }
        }
//        }
//        else{
//            $obErro->setDescricao( "A área da unidade deve ser menor ou igual à área da edificação!" );
//        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeAutonoma );

    return $obErro;
}

/**
* Verifica de deve incluir, alterar ou excluir o processo informado
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarProcesso($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->obRProcesso->getCodigoProcesso() ) {
        $obErro = $this->obRProcesso->validarProcesso( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao"   , $this->obRCIMEdificacao->getCodigoConstrucao());
            $this->obTCIMConstrucaoProcesso->setDado( "cod_processo"     , $this->obRProcesso->getCodigoProcesso()       );
            $this->obTCIMConstrucaoProcesso->setDado( "exercicio"        , $this->obRProcesso->getExercicio()            );
            $obErro = $this->obTCIMConstrucaoProcesso->inclusao( $boTransacao );
        }
    }

    return $obErro;
}

/** Autor: Lucas Stephanou
* Recupera do banco de dados os dados O MAIOR TIMESTAMP da CONSTRUCAO
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarTimestampUnidadeAutonoma($boTransacao = "")
{
    $obErro = new Erro;
    // se a chave nao estiver setada
    if ( !$this->obRCIMEdificacao->getCodigoConstrucao() || !$this->roRCIMImovel->getNumeroInscricao() || !$this->obRCIMEdificacao->getCodigoTipo() ) {
        $obErro->setDescricao("Chave(inscricao_municipa, cod_tipo,cod_construcao) deve estar setada para esta operação!");
    } else {
        $stFiltro = "";
        $stFiltro .= " inscricao_municipal = ". $this->roRCIMImovel->getNumeroInscricao()." AND ";
        $stFiltro .= " cod_tipo = ". $this->obRCIMEdificacao->getCodigoTipo()." AND ";
        $stFiltro .= " cod_construcao = ". $this->obRCIMEdificacao->getCodigoConstrucao()." AND ";
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }
        $obErro = $this->obTCIMAreaUnidadeAutonoma->recuperaUltimoTimestamp( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $this->setTimestampUnidadeAutonoma( $rsRecordSet->getCampo( "max_timestamp"));
        }
    }

    return $obErro;
}

/**
* Recupera a área da construção (Autonoma ou Dependente)
* @access Public
* @param  Object $flAreaConstrucao
* @param  Object $flAreaImovel
* @return Object Objeto Erro
*/
function buscaAreaConstrucao(&$flAreaConstrucao, &$flAreaImovel)
{
    $obErro = new Erro;
    $stFiltro = "";
    if ( $this->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= "inscricao_municipal = ".$this->roRCIMImovel->getNumeroInscricao();
    }
    $obErro = $this->obTCIMAreaConstrucao->recuperaAreaConstrucao( $rsRecordSet, $stFiltro, $boTransacao );
    $rsRecordSet->addFormatacao( 'area_total'  , 'NUMERIC_BR' );
    $rsRecordSet->addFormatacao( 'area_imovel' , 'NUMERIC_BR' );
    $flAreaConstrucao = $rsRecordSet->getCampo('area_total');
    $flAreaImovel     = $rsRecordSet->getCampo('area_imovel');

    return $obErro;
}

}// FECHA CLASSE
