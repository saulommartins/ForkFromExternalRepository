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
    * Classe de Regra de Negócio Vale Transporte
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: leandro.zis $
      $Date: 2007-07-30 11:21:16 -0300 (Seg, 30 Jul 2007) $

      Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependente.class.php"                                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php"                         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependenteCid.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependenteExcluido.class.php"                         );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCarteiraVacinacao.class.php"                             );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalComprovanteMatricula.class.php"                          );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGrauParentesco.class.php"                                );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCID.class.php"                                           );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                      );
include_once ( CAM_GRH_PES_NEGOCIO.'RPessoalPensao.class.php'                                        );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"                                       );

/**
    * Classe de Regra de Negócio Pesssoal Servidor Dependentes
    * Data de Criação   : 20/12/2004

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalDependente
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodDependente;
/**
   * @access Private
   * @var Integer
*/
var $inNumCgm;
/**
   * @access Private
   * @var Integer
*/
var $inCodGrau;
/**
   * @access Private
   * @var Boolean
*/
var $boDependenteInvalido;
/**
   * @access Private
   * @var Boolean
*/
var $boCarteiraVacinacao;
/**
   * @access Private
   * @var Boolean
*/
var $boComprovanteMatricula;
/**
   * @access Private
   * @var Integer
*/
var $boDependentePrev;
/**
   * @access Private
   * @var Integer
*/
var $inCodVinculo;
/**
   * @access Private
   * @var Date
*/
var $dtDataInicioSalarioFamilia;
/**
   * @access Private
   * @var Boolean
*/
var $boDependenteSalarioFamilia;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalCarteiraVacinacao;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalCarteiraVacinacao;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalComprovanteMatricula;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalComprovanteMatricula;
/**
   * @access Private
   * @var Object
*/
var $obRCGMPessoalFisica;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalCID;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalPensao;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalGrauParentesco;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalServidor;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalDependente;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalServidorDependente;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalDependenteCID;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalDependenteExcluido;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodDependente($valor) { $this->inCodDependente                   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumCgm($valor) { $this->inNumCgm                        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGrau($valor) { $this->inCodGrau                       = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDependenteInvalido($valor) { $this->boDependenteInvalido            = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setCarteiraVacinacao($valor) { $this->boCarteiraVacinacao             = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setComprovanteMatricula($valor) { $this->boComprovanteMatricula          = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDependentePrev($valor) { $this->boDependentePrev          	   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodVinculo($valor) { $this->inCodVinculo                    = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInicioSalarioFamilia($valor) { $this->dtDataInicioSalarioFamilia      = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setDependenteSalarioFamilia($valor) { $this->boDependenteSalarioFamilia      = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalCarteiraVacinacao($valor) { $this->arRPessoalCarteiraVacinacao     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalCarteiraVacinacao(&$valor) { $this->roRPessoalCarteiraVacinacao    = &$valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRPessoalComprovanteMatricula($valor) { $this->arRPessoalComprovanteMatricula  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalComprovanteMatricula(&$valor) { $this->roRPessoalComprovanteMatricula = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCGMPessoalFisica($valor) { $this->obRCGMPessoalFisica             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalCID($valor) { $this->obRPessoalCID                   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalPensao($valor) { $this->obRPessoalPensao                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalGrauParentesco($valor) { $this->obRPessoalGrauParentesco        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCGMPessoaFisica($valor) { $this->obRCGMPessoaFisica              = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalServidor(&$valor) { $this->roRPessoalServidor             = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalDependente($valor) { $this->obTPessoalDependente            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalServidorDependente($valor) { $this->obTPessoalServidorDependente    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalDependenteCID($valor) { $this->obTPessoalDependenteCID         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalDependenteExcluido($valor) { $this->obTPessoalDependenteExcluido    = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                     }
/**
    * @access Public
    * @return Integer
*/
function getCodDependente() { return $this->inCodDependente;                   }
/**
    * @access Public
    * @return Integer
*/
function getNumCgm() { return $this->inNumCgm;                        }
/**
    * @access Public
    * @return Integer
*/
function getCodGrau() { return $this->inCodGrau;                       }
/**
    * @access Public
    * @return Boolean
*/
function getDependenteInvalido() { return $this->boDependenteInvalido;            }
/**
    * @access Public
    * @return Boolean
*/
function getCarteiraVacinacao() { return $this->boCarteiraVacinacao;             }
/**
    * @access Public
    * @return Boolean
*/
function getComprovanteMatricula() { return $this->boComprovanteMatricula;          }
/**
    * @access Public
    * @return Boolean
*/
function getDependentePrev() { return $this->boDependentePrev;          		}
/**
    * @access Public
    * @return Integer
*/
function getCodVinculo() { return $this->inCodVinculo;                    }
/**
    * @access Public
    * @return Date
*/
function getDataInicioSalarioFamilia() { return $this->dtDataInicioSalarioFamilia;      }
/**
    * @access Public
    * @return Boolean
*/
function getDependenteSalarioFamilia() { return $this->boDependenteSalarioFamilia;      }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalCarteiraVacinacao() { return $this->arRPessoalCarteiraVacinacao;     }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalCarteiraVacinacao() { return $this->roRPessoalCarteiraVacinacao;     }
/**
    * @access Public
    * @return Array
*/
function getARRPessoalComprovanteMatricula() { return $this->arRPessoalComprovanteMatricula;  }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalComprovanteMatricula() { return $this->roRPessoalComprovanteMatricula;  }
/**
    * @access Public
    * @return Object
*/
function getRCGMPessoalFisica() { return $this->obRCGMPessoalFisica;             }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCID() { return $this->obRPessoalCID;                   }
/**
    * @access Public
    * @return Object
*/
function getRPessoalPensao() { return $this->obRPessoalPensao;                }
/**
    * @access Public
    * @return Object
*/
function getRPessoalGrauParentesco() { return $this->obRPessoalGrauParentesco;        }
/**
    * @access Public
    * @return Object
*/
function getRCGNPessoaFisica() { return $this->obRCGMPessoaFisica;              }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalServidor() { return $this->roRPessoalServidor;              }
/**
    * @access Public
    * @return Object
*/
function getTPessoalDependente() { return $this->obTPessoalDependente;            }
/**
    * @access Public
    * @return Object
*/
function getTPessoalServidorDependente() { return $this->obTPessoalServidorDependente;    }
/**
    * @access Public
    * @return Object
*/
function getTPessoalDependenteCID() { return $this->obTPessoalDependenteCID;         }
/**
    * @access Public
    * @return Object
*/
function getTPessoalDependenteExcluido() { return $this->obTPessoalDependenteExcluido;    }

    /**
    * Método Construtor
    * @access Private
    */
    public function RPessoalDependente(&$roPessoalServidor)
    {
        $this->setTransacao                              ( new Transacao                               );
        $this->setTPessoalDependente                     ( new TPessoalDependente                      );
        $this->setTPessoalServidorDependente             ( new TPessoalServidorDependente              );
        //$this->setTPessoalDependenteCarteiraVacinacao    ( new TPessoalDependenteCarteiraVacinacao     );
        $this->setTPessoalDependenteCID                  ( new TPessoalDependenteCid                   );
        $this->setTPessoalDependenteExcluido             ( new TPessoalDependenteExcluido              );
        $this->setRPessoalCID                            ( new RPessoalCID                             );
        $this->setRPessoalGrauParentesco                 ( new RPessoalGrauParentesco                  );
        $this->setRCGMPessoaFisica                       ( new RCGMPessoaFisica                        );
        $this->arDatasCarteiraVacinacao                  = array();
        $this->arDatasComprovanteMatricula               = array();
        $this->arDependente                              = array();
        $this->roPessoalServidor                         = &$roPessoalServidor;
        $this->arPessoalCarteiraVacinacao                = array();
        $this->arPessoalComprovanteMatricula             = array();
        $this->bDependentePrev							 = false;
    }

    /**
        * Adiciona uma Carteira Vacinacao ao array de referencia-objeto
        * @access Public
    */
    public function addRPessoalCarteiraVacinacao()
    {
        $this->arRPessoalCarteiraVacinacao[] = new RPessoalCarteiraVacinacao ( $this );
        $this->roRPessoalCarteiraVacinacao   = &$this->arRPessoalCarteiraVacinacao[ count($this->arRPessoalCarteiraVacinacao) - 1 ];
    }

    /**
        * Adiciona um Comprovante Matricula ao array de referencia-objeto
        * @access Public
    */
    public function addRPessoalComprovanteMatricula()
    {
        $this->arRPessoalComprovanteMatricula[]  = new RPessoalComprovanteMatricula ( $this );
        $this->roRPessoalComprovanteMatricula    = &$this->arRPessoalComprovanteMatricula[ count($this->arRPessoalComprovanteMatricula) - 1 ];
    }

    /**
        * Salva dados de Vale-transporte no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirDependente($boTransacao = "")
    {
        $boFlagTransacao = false;
        $rsDependente = new RecordSet;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() and $this->obRCGMPessoaFisica->getDataNascimento() ) {
            include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
            $obTCGMPessoaFisica = new TCGMPessoaFisica;
            $stFiltro = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();

            $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem, $boTransacao );

            $obTCGMPessoaFisica->setDado("dt_nascimento",       $this->obRCGMPessoaFisica->getDataNascimento());
            $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
            $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
            $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));

            if ( $rsCGM->getCampo('cpf') ) {
                $obTCGMPessoaFisica->setDado("cpf",             $rsCGM->getCampo('cpf'));
            }

            $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
            $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));
            $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalDependente->proximoCod( $inCodDependente , $boTransacao );
            $this->setCodDependente( $inCodDependente );
            $this->obTPessoalDependente->setDado("cod_dependente"           , $this->getCodDependente()                 );
            $this->obTPessoalDependente->setDado("numcgm"                   , $this->obRCGMPessoaFisica->getNumCgm()    );
            $this->obTPessoalDependente->setDado("cod_grau"                 , $this->getCodGrau()                       );
            $this->obTPessoalDependente->setDado("dependente_invalido"      , $this->getDependenteInvalido()            );
            $this->obTPessoalDependente->setDado("carteira_vacinacao"       , $this->getCarteiraVacinacao()             );
            $this->obTPessoalDependente->setDado("comprovante_matricula"    , $this->getComprovanteMatricula()          );
            $this->obTPessoalDependente->setDado("cod_vinculo"              , $this->getCodVinculo()                    );
            $this->obTPessoalDependente->setDado("dt_inicio_sal_familia"    , $this->getDataInicioSalarioFamilia()      );
            $this->obTPessoalDependente->setDado("dependente_sal_familia"   , $this->getDependenteSalarioFamilia()      );
            $obErro = $this->obTPessoalDependente->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTPessoalServidorDependente->setDado("cod_servidor"  , $this->roPessoalServidor->getCodServidor() );
                $this->obTPessoalServidorDependente->setDado("cod_dependente", $this->getCodDependente()                  );
                $this->obTPessoalServidorDependente->setDado("dt_inicio", date('y-m-d'));
                $obErro = $this->obTPessoalServidorDependente->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() and $this->obRPessoalCID->getCodCid() != "" ) {
                $this->obTPessoalDependenteCID->setDado("cod_dependente", $this->getCodDependente()                     );
                $this->obTPessoalDependenteCID->setDado("cod_cid"       , $this->obRPessoalCID->getCodCid()             );
                $obErro = $this->obTPessoalDependenteCID->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                for ($inIndex=0;$inIndex<count($this->arRPessoalCarteiraVacinacao);$inIndex++) {
                    $obRPessoalCarteiraVacinacao = &$this->arRPessoalCarteiraVacinacao[$inIndex];
                    if ($inIndex == 0) {
                        $obErro = $obRPessoalCarteiraVacinacao->excluirCarteira( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                    $obErro = $obRPessoalCarteiraVacinacao->incluirCarteira( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
            if ( !$obErro->ocorreu()  ) {
                for ($inIndex=0;$inIndex<count($this->arRPessoalComprovanteMatricula);$inIndex++) {
                    $obRPessoalComprovanteMatricula = &$this->arRPessoalComprovanteMatricula[$inIndex];
                    if ($inIndex == 0) {
                        $obErro = $obRPessoalComprovanteMatricula->excluirComprovante($boTransacao);
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                    $obErro = $obRPessoalComprovanteMatricula->incluirComprovante( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalDependente );

        return $obErro;
    }

    /**
        * Salva dados de Vale-transporte no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirDependenteExcluido($boTransacao = "")
    {
        $boFlagTransacao = false;
        $rsDependente = new RecordSet;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalDependenteExcluido->setDado("cod_dependente"           , $this->getCodDependente()                     );
            $this->obTPessoalDependenteExcluido->setDado("cod_servidor"             , $this->roPessoalServidor->getCodServidor()   );
            $obErro = $this->obTPessoalDependenteExcluido->inclusao( $boTransacao );

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalDependente );

        return $obErro;
    }

     /**
        * Altera os dados da tabela Dependente
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarDependente($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() and $this->obRCGMPessoaFisica->getDataNascimento() ) {
            include_once ( CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
            $obTCGMPessoaFisica = new TCGMPessoaFisica;
            $stFiltro = " WHERE numcgm = ".$this->obRCGMPessoaFisica->getNumCgm();
            $obTCGMPessoaFisica->recuperaTodos($rsCGM,$stFiltro,$stOrdem,$boTransacao);

            $obTCGMPessoaFisica->setDado("dt_nascimento",       $this->obRCGMPessoaFisica->getDataNascimento());
            $obTCGMPessoaFisica->setDado("numcgm",              $this->obRCGMPessoaFisica->getNumCgm());
            $obTCGMPessoaFisica->setDado("cod_categoria_cnh",   $rsCGM->getCampo('cod_categoria_cnh'));
            $obTCGMPessoaFisica->setDado("orgao_emissor",       $rsCGM->getCampo('orgao_emissor'));
            $obTCGMPessoaFisica->setDado("cpf",                 $rsCGM->getCampo('cpf'));
            $obTCGMPessoaFisica->setDado("num_cnh",             $rsCGM->getCampo('num_cnh'));
            $obTCGMPessoaFisica->setDado("cod_nacionalidade",   $rsCGM->getCampo('cod_nacionalidade'));
            $obErro = $obTCGMPessoaFisica->alteracao($boTransacao);
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalDependente->setDado("cod_dependente"           , $this->getCodDependente()                 );
            $this->obTPessoalDependente->setDado("numcgm"                   , $this->obRCGMPessoaFisica->getNumCgm()    );
            $this->obTPessoalDependente->setDado("cod_grau"                 , $this->getCodGrau()                       );
            $this->obTPessoalDependente->setDado("dependente_invalido"      , $this->getDependenteInvalido()            );
            $this->obTPessoalDependente->setDado("carteira_vacinacao"       , $this->getCarteiraVacinacao()             );
            $this->obTPessoalDependente->setDado("comprovante_matricula"    , $this->getComprovanteMatricula()          );
            $this->obTPessoalDependente->setDado("dependente_prev"          , $this->getDependentePrev()                );
            $this->obTPessoalDependente->setDado("cod_vinculo"              , $this->getCodVinculo()                    );
            $this->obTPessoalDependente->setDado("dt_inicio_sal_familia"    , $this->getDataInicioSalarioFamilia()      );
            $this->obTPessoalDependente->setDado("dependente_sal_familia"   , $this->getDependenteSalarioFamilia()      );
            $obErro = $this->obTPessoalDependente->alteracao( $boTransacao );
            
            if ( !$obErro->ocorreu() and $this->obRPessoalCID->getCodCid() != null) {
                $this->obTPessoalDependenteCID->setDado("cod_dependente", $this->getCodDependente()                     );
                $this->obTPessoalDependenteCID->setDado("cod_cid"       , $this->obRPessoalCID->getCodCid()             );
                $this->obTPessoalDependenteCID->recuperaPorChave($rsCID);
                
                if( count($rsCID->arElementos) > 0 ){
                    $obErro = $this->obTPessoalDependenteCID->alteracao( $boTransacao );
                }else{
                    $obErro = $this->obTPessoalDependenteCID->inclusao( $boTransacao );
                }
            }
            if ( !$obErro->ocorreu() ) {
                $this->addRPessoalCarteiraVacinacao();
                $obErro = $this->roRPessoalCarteiraVacinacao->excluirCarteira($boTransacao);
                array_pop($this->arRPessoalCarteiraVacinacao);
            }
            if ( !$obErro->ocorreu() ) {
                for ($inIndex=0;$inIndex<count($this->arRPessoalCarteiraVacinacao);$inIndex++) {
                    $obRPessoalCarteiraVacinacao = &$this->arRPessoalCarteiraVacinacao[$inIndex];
                    $obErro = $obRPessoalCarteiraVacinacao->incluirCarteira( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
            if ( !$obErro->ocorreu()  ) {
                $this->addRPessoalComprovanteMatricula();
                $obErro = $this->roRPessoalComprovanteMatricula->excluirComprovante($boTransacao);
                array_pop($this->arRPessoalComprovanteMatricula);
            }
            if ( !$obErro->ocorreu()  ) {
                for ($inIndex=0;$inIndex<count($this->arRPessoalComprovanteMatricula);$inIndex++) {
                    $obRPessoalComprovanteMatricula = &$this->arRPessoalComprovanteMatricula[$inIndex];
                    $obErro = $obRPessoalComprovanteMatricula->incluirComprovante( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }

        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalDependente );

        return $obErro;
    }

    /**
    * Executa um recuperaTodos na classe Persistente Servidor consulta/alteração
    * @access Public
    * @param  Object $rsPessoalDependente Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function listarPessoalDependente(&$rsPessoalDependente, $stFiltro = "", $boTransacao = "")
    {
        $stFiltro = "";
        $stOrder = " ORDER BY nom_cgm";
        if ( $this->roPessoalServidor->getCodServidor() ) {
            $stFiltro .= " AND PS.cod_servidor = ".$this->roPessoalServidor->getCodServidor();
            $this->obTPessoalDependente->setDado('cod_servidor',$this->roPessoalServidor->getCodServidor());
        }
        $obErro = $this->obTPessoalDependente->recuperaRelacionamento( $rsPessoalDependente , $stFiltro, $stOrder ,$boTransacao );

        return $obErro;
    }

   /**
    * consulta um dependente
    * @access Public
    * @param  Object $rsPessoalDependente Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function consultarDependente(&$rsPessoalDependente, $stFiltro = "", $boTransacao = "")
    {
        $stFiltro = "";
        if ( $this->roPessoalServidor->getCodServidor() ) {
            $stFiltro .= " AND PS.cod_servidor = ".$this->roPessoalServidor->getCodServidor();
            $this->obTPessoalDependente->setDado('cod_servidor',$this->roPessoalServidor->getCodServidor());
        }

        if ( $this->getCodDependente() ) {
            $stFiltro .= ' AND PD.cod_dependente = ' . $this->getCodDependente();
            $this->obTPessoalDependente->setDado('cod_dependente',$this->getCodDependente());
        }
        $obErro = $this->obTPessoalDependente->recuperaRelacionamento( $rsPessoalDependente , $stFiltro, $stOrder ,$boTransacao );

        return $obErro;
    }

    /**
    * Executa a exclusao na classe Persistente
    * @access Public
    * @param  Object $rsPessoalDependente Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
    */
    public function excluirDependente($boTransacao = "")
    {
        $boFlagTransacao = false;
        $rsDependente = new RecordSet;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->listarPessoalDependente( $rsDependente,$stFiltro="", $boTransacao );
            if ( !$obErro->ocorreu() ) {
                while ( !$rsDependente->eof() ) {
                    $this->setCodDependente( $rsDependente->getCampo("cod_dependente") );
                    $this->obTPessoalServidorDependente->setDado( "cod_dependente", $rsDependente->getCampo("cod_dependente") );
                    $obErro = $this->obTPessoalServidorDependente->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTPessoalDependenteCID->setDado("cod_dependente", $rsDependente->getCampo("cod_dependente")                     );
                        $obErro = $this->obTPessoalDependenteCID->exclusao( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->addRPessoalCarteiraVacinacao();
                        $this->roRPessoalCarteiraVacinacao->setCarteira( $rsDependente->getCampo("VACINACAO") );
                        $obErro = $this->roRPessoalCarteiraVacinacao->excluirCarteira( $boTransacao );
                    }
                    if ( !$obErro->ocorreu()  ) {
                        $this->addRPessoalComprovanteMatricula();
                        $this->roRPessoalComprovanteMatricula->setComprovante( $rsDependente->getCampo("MATRICULA") );
                        $obErro = $this->roRPessoalComprovanteMatricula->excluirComprovante( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {

                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obTPessoalDependente->setDado("cod_dependente",$rsDependente->getCampo("cod_dependente"));
                        $obErro = $this->obTPessoalDependente->exclusao( $boTransacao );
                    }
                    $rsDependente->proximo();
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCTPS );

        return $obErro;
    }

}
