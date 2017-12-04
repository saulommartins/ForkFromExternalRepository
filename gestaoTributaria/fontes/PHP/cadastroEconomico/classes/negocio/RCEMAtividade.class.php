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
    * Classe de regra de negócio para Atividade
    * Data de Criação: 19/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @package URBEM

    * @subpackage Regra

    * $Id: RCEMAtividade.class.php 66548 2016-09-21 13:05:07Z evandro $

    * Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.18  2007/04/26 14:53:36  cercato
Bug #9220#

Revision 1.17  2006/11/17 10:20:33  cercato
bug #7442#

Revision 1.16  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCnae.class.php"                     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                 );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMProfissao.class.php"                );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"                 );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"             );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php"             );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelAtividadeValor.class.php"   );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCadastroEconomico.class.php");
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAliquotaAtividade.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServicoAtividade.class.php"      );
/**
    * Classe de regra de negócio para Localizacao
    * Data de Criação: 17/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo
    * @package URBEM
    * @subpackage Regra
*/

class RCEMAtividade extends RCEMNivelAtividade
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoAtividade;
/**
    * @access Private
    * @var String
*/
var $stNomeAtividade;
/**
    * @access Private
    * @var Boolean
*/
var $boPrincipal;
/**
    * @access Private
    * @var Float
*/
var $flAliquota;
/**
    * @access Private
    * @var String
*/
var $stValor;//tabela NIVEL_ATIVIDADE
/**
    * @access Private
    * @var String
*/
var $stValorComposto;//valor de todos os niveis da atividade concateneados
/**
    * @access Private
    * @var String
*/
var $stValorReduzido;//valor de todos os niveis que possuem atividade
/**
    * @access Private
    * @var Object
*/
var $roUltimaProfissao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoElemento;
/**
    * @access Private
    * @var Object
*/
var $roUltimoCnae;
/**
    * @access Private
    * @var Boolean
*/
var $boUltimoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inOcorrenciaAtividade;
/**
     * @access Private
     * @var Array
 */
var $arChaveAtividade;
/**
    * @access Private
    * @var Object
*/
var $roRCEMInscricaoAtividade;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoAtividade($valor) { $this->inCodigoAtividade = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeAtividade($valor) { $this->stNomeAtividade = $valor;   }
/**
    * @access Public
    * @param Boolean $valor
*/
function setPrincipal($valor) { $this->boPrincipal = $valor;       }
/**
    * @access Public
    * @param Float $valor
*/
function setAliquota($valor) { $this->flAliquota = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setValor($valor) { $this->stValor = $valor;           }
/**
    * @access Public
    * @param String $valor
*/
function setValorComposto($valor) { $this->stValorComposto = $valor;   }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzido($valor) { $this->stValorReduzido = $valor;   }
/**
    * @access Public
    * @param Array $valor
*/
function setServico($valor) { $this->arServico = $valor;         }
/**
    * @access Public
    * @param Boolean $valor
*/
function setUltimoNivel($valor) { $this->boUltimoNivel = $valor;     }
/**
    * @access Public
    * @param Boolean $valor
*/
function setOcorrenciaAtividade($valor) { $this->inOcorrenciaAtividade = $valor;     }

/**
    * @access Public
    * @return Integer
*/
function getCodigoAtividade() { return $this->inCodigoAtividade;  }
/**
    * @access Public
    * @return String
*/
function getNomeAtividade() { return $this->stNomeAtividade;    }
/**
    * @access Public
    * @return Boolean
*/
function getPrincipal() { return $this->boPrincipal;        }
/**
    * @access Public
    * @return Float
*/
function getAliquota() { return $this->flAliquota;         }
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
    * @return String
*/
function getServico() { return $this->arServico;          }
/**
    * @access Public
    * @return Boolean
*/
function getUltimoNivel() { return $this->boUltimoNivel;      }
/**
    * @access Public
    * @return Integer
*/
function getOcorrenciaAtividade() { return $this->inOcorrenciaAtividade;}

/**
     * Método construtor
     * @access Private
*/
function RCEMAtividade()
{
    parent::RCEMNivelAtividade();
    $this->obRCEMServico             = new RCEMServico;
    $this->obRCEMConfiguracao        = new RCEMConfiguracao;
    $this->roRCEMInscricaoAtividade  = &$RCEMInscricaoAtividade;
    //$this->obRCEMProfissao           = new RCEMProfissao( $this );
    //$this->obRCEMElemento            = new RCEMElemento( $this );
    //$this->obRCEMCnae                = new RCEMCnae( $this );
    $this->obTCEMAtividade           = new TCEMAtividade;
    $this->obTCEMNivelAtividadeValor = new TCEMNivelAtividadeValor;
    $this->obTCEMAliquotaAtividade   = new TCEMAliquotaAtividade;
    $this->obTCEMServicoAtividade    = new TCEMServicoAtividade;
    $this->obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico;
    $this->arChaveLocalizacao        = array();
    $this->arServico                 = array();
    $this->arRCEMProfissao           = array();
    $this->arRCEMElemento            = array();
    $this->arRCEMCnae                = array();
}

/**
    * Faz a referencia com um objeto de Inscricão Atividade
    * @access Public
    * @param Objet objeto de Inscri?ão Atividade
*/
function addInscricaoAtividade(&$RCEMInscricaoAtividade)
{
    $this->roRCEMInscricaoAtividade = &$RCEMInscricaoAtividade;
}

/**
    * Inclui os dados referentes a Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirAtividade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->validaCodigoAtividade( $boTransacao );
        if ( !$obErro->ocorreu() ) {

            $obErro = $this->obTCEMAtividade->proximoCod( $this->inCodigoAtividade, $boTransacao );

            if ( !$obErro->ocorreu() ) {
                //MONTA CODIGO ESTRUTURAL
                $obErro = $this->recuperaMascaraNiveis( $rsMascaraNivel, $boTransacao );
                $rsMascaraNivel->ordena('cod_nivel');
                $obErro = $this->consultarNivel( $boTransacao );

                $stCodigoMascara = $this->arChaveAtividade[ count($this->arChaveAtividade) - 1 ][3].".";
                $stCodigoMascara .= str_pad( $this->stValor, strlen($this->stMascara), "0", STR_PAD_LEFT );
                $stMascaraComposta = "";
                $rsMascaraNivel->setPrimeiroElemento();
                $i = 1;
                while ( !$rsMascaraNivel->eof() ) {
                    $stMascaraComposta .= $rsMascaraNivel->getCampo("mascara").".";
                    $stMascaraNivel = str_replace( "9", "0", $rsMascaraNivel->getCampo("mascara") );
                    $stMascaraNivel = preg_match("/[A-Za-z]/i","0",$stMascaraNivel);
                    $stCodigoComposto .= $rsMascaraNivel->getCampo("cod_nivel") == $this->getCodigoNivel() ? $stCodigoMascara."." : $stMascaraNivel.".";
                    $rsMascaraNivel->proximo();
                }
                $stMascaraComposta = substr( $stMascaraComposta, 0, strlen( $stMascara ) - 1 );
                $stCodigoComposto  = substr( $stCodigoComposto, 0, strlen( $stMascara ) - 1);

                $corteMascara     = strlen($stCodigoComposto) - strlen($stMascaraComposta);
                $stCodigoComposto = substr( $stCodigoComposto, $corteMascara );
                //EXECUTA A INCLUSAO NA TABELA ATIVIDADE
                $this->obTCEMAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                $this->obTCEMAtividade->setDado( "nom_atividade", $this->stNomeAtividade   );
                $this->obTCEMAtividade->setDado( "cod_vigencia" , $this->inCodigoVigencia  );
                $this->obTCEMAtividade->setDado( "cod_estrutural",$stCodigoComposto        );
                $this->obTCEMAtividade->setDado( "cod_nivel"    , $this->inCodigoNivel     );
                $obErro = $this->obTCEMAtividade->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    //LISTA OS NIVEIS EM RELAÇÃO A VIGÊNCIA SELECIONADA
                    $inCodigoNivelTmp = $this->inCodigoNivel;
                    $this->inCodigoNivel = "";

                    $obErro = $this->listarNiveis( $rsNiveis, $boTransacao );
                    $this->inCodigoNivel = $inCodigoNivelTmp;

                    if ( !$obErro->ocorreu() ) {
                        $this->obTCEMNivelAtividadeValor->setDado( "cod_vigencia",    $this->inCodigoVigencia    );
                        //EXECUTA A INCLUSAO DOS VALORES DAS ATIVIDADE NOS NIVEIS SUPERIORES AO CORRENTE
                        if ($this->arChaveAtividade) {
                        foreach ($this->arChaveAtividade as $arChaveAtividade) {
                            //[0] = cod_nivel | [1] = cod_atividade | [2] = valor
                            $this->obTCEMNivelAtividadeValor->setDado( "cod_nivel"      , $arChaveAtividade[0] );
                            $this->obTCEMNivelAtividadeValor->setDado( "cod_atividade", $this->inCodigoAtividade );
                            //MASCARA O VALOR CONFORME O MASCARA DO NIVEL
                            $stValor = $arChaveAtividade[2];
                            $this->obTCEMNivelAtividadeValor->setDado( "valor"          , $arChaveAtividade[2] );
                            $obErro = $this->obTCEMNivelAtividadeValor->inclusao( $boTransacao );

                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            if ( !$rsNiveis->eof() ) {
                                $rsNiveis->proximo();
                            }
                        }
                        }
                        //INCLUI O VALOR DA ATIVIDADE NO NIVEL CORRENTE
                        $this->obTCEMNivelAtividadeValor->setDado( "cod_atividade", $this->inCodigoAtividade );
                        $this->obTCEMNivelAtividadeValor->setDado( "cod_nivel"    , $this->inCodigoNivel     );
                        $stValor = $this->stValor;
                        $this->obTCEMNivelAtividadeValor->setDado( "valor", $stValor );
                        $obErro = $this->obTCEMNivelAtividadeValor->inclusao( $boTransacao );

                        if ( !$rsNiveis->eof() ) {
                            $rsNiveis->proximo();
                        }

                        //INCLUI O VALOR DA ATIVIDADE DOS NIVEIS SEGUINTES
                        if ( !$obErro->ocorreu() ) {
                            while ( !$rsNiveis->eof() ) {
                                $stValor = "0";
                                $this->obTCEMNivelAtividadeValor->setDado( "cod_nivel"      , $rsNiveis->getCampo("cod_nivel") );
                                $this->obTCEMNivelAtividadeValor->setDado( "valor", $stValor );
                                $obErro = $this->obTCEMNivelAtividadeValor->inclusao( $boTransacao );

                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                if ( !$rsNiveis->eof() ) {
                                    $rsNiveis->proximo();
                                }
                            }

                            if ($this->boUltimoNivel) {

                               //INCLUI NA TABELA DE ALIQUOTA_ATIVIDADE
                               if ( !$obErro->ocorreu() ) {
                                  $dtdiaHOJE = date ("d-m-Y");
                                  $this->obTCEMAliquotaAtividade->setDado( "dt_vigencia", $dtdiaHOJE);
                                  $this->obTCEMAliquotaAtividade->setDado( "cod_atividade", $this->getCodigoAtividade() );
                                  $this->obTCEMAliquotaAtividade->setDado( "valor"        , $this->flAliquota );

                                  if ($this->flAliquota) {
                                      $obErro = $this->obTCEMAliquotaAtividade->inclusao( $boTransacao );
                                  } else {
                                      $obErro = $this->obTCEMAliquotaAtividade->exclusao( $boTransacao );
                                  }

                                  //INCLUI NA TABELA DE ATIVIDADE_CNAE_FISCAL
                                  if ( !$obErro->ocorreu() ) {
                                      if ( is_object($this->roUltimoCnae) ) {
                                        if ( $this->roUltimoCnae->getCodigoCnae() ) {
                                            $obErro = $this->roUltimoCnae->incluirAtividadeCnae( $boFlagTransacao, $boTransacao );
                                        }
                                      }

                                      //INCLUI NA TABELA DE ATIVIDADE_PROFISSAO
                                      if ( !$obErro->ocorreu() ) {
                                          if ( count($this->roUltimaProfissao) ) {
                                              foreach ($this->arRCEMProfissao as $obRCEMProfissao) {
                                                  $obErro = $obRCEMProfissao->incluirAtividadeProfissao( $boFlagTransacao, $boTransacao );
                                                  if ( $obErro->ocorreu() ) {
                                                      break;
                                                  }
                                              }
                                          }
                                          //INCLUI NA TABELA DE ELEMENTO_ATIVIDADE
                                          if ( !$obErro->ocorreu() ) {
                                              if ( count($this->roUltimoElemento) ) {
                                                  foreach ($this->arRCEMElemento as $obRCEMElemento) {
                                                      $obErro = $obRCEMElemento->incluirAtividadeElemento( $boFlagTransacao, $boTransacao );

                                                      if ( $obErro->ocorreu() ) {
                                                          break;
                                                      }
                                                  }
                                              }
                                          }

                                          //INCLUI NA TABELA DE SERVICO_ATIVIDADE
                                          if ( !$obErro->ocorreu() ) {
                                              foreach ($this->arServico as $obRCEMServico) {
                                                  $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                                                  $this->obTCEMServicoAtividade->setDado( "cod_servico"  , $obRCEMServico->getCodigoServico() );
                                                  $obErro = $this->obTCEMServicoAtividade->inclusao( $boTransacao );
                                                  if ( $obErro->ocorreu() ) {
                                                      break;
                                                  }
                                              }

/*
                                              if ( !$obErro->ocorreu() ) {
                                                  $this->obTCEMAtividade->setDado( "valor" , $this->getValorComposto() );
                                                  $obErro = $this->obTCEMAtividade->atualizaAtividade( $boTransacao );
                                              }
*/
                                          }
                                      }
                                  }
                               }
                           }
                        }
                    }
               }
           }
       }
   }

   if ($boFlagTransacao==false) {
        $boFlagTransacao=true;
   }

   $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividade );

   return $obErro;
}

/**
    * Excluir os dados referentes a Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAtividade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaFilhosAtividade( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->excluirNivelAtividade( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                $obErro = $this->obTCEMServicoAtividade->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCEMAliquotaAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                    $obErro = $this->obTCEMAliquotaAtividade->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->addAtividadeCnae();
                        $obErro = $this->roUltimoCnae->excluirAtividadeCnae( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->addAtividadeProfissao();
                            $obErro = $this->roUltimaProfissao->excluirAtividadeProfissao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->addAtividadeElemento();
                                $obErro = $this->roUltimoElemento->excluirElementoAtividade( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $this->obTCEMAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                                    $obErro = $this->obTCEMAtividade->exclusao( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividade );

    return $obErro;
}

/**
    * Excluir os dados referentes a Nível Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNivelAtividade($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodigoAtividadeTmp = $this->inCodigoAtividade;
        $inCodigoNivelTmp       = $this->inCodigoNivel;
        $this->inCodigoAtividade = "";
        $this->inCodigoNivel   = "";
        $obErro = $this->verificaFilhosAtividade( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->inCodigoAtividade = $inCodigoAtividadeTmp;
            $obErro = $this->listarNiveis( $rsNivel, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                while ( !$rsNivel->eof() ) {
                    $this->obTCEMNivelAtividadeValor->setDado( "cod_atividade",   $this->inCodigoAtividade        );
                    $this->obTCEMNivelAtividadeValor->setDado( "cod_vigencia",    $this->inCodigoVigencia         );
                    $this->obTCEMNivelAtividadeValor->setDado( "cod_nivel",       $rsNivel->getCampo("cod_nivel") );
                    $obErro = $this->obTCEMNivelAtividadeValor->exclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                    $rsNivel->proximo();
                }
            }
            $this->inCodigoNivel = $inCodigoNivelTmp;
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelAtividadeValor );

    return $obErro;
}

/**
    * Verifica se existem filhos da atividade setadas, se houver retorna o erro informando
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaFilhosAtividade($boTransacao = "")
{
    $inCodigoAtividadeTmp = $this->inCodigoAtividade;
    $inCodigoNivelTmp     = $this->inCodigoNivel;
    $stNomeAtividadeTmp   = $this->stNomeAtividade;
    $this->inCodigoAtividade = "";
    $this->inCodigoNivel     = "";
    $this->stNomeAtividade   = "";
    $obErro = $this->listarAtividade( $rsListaAtividade, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsListaAtividade->eof() ) {
        $obErro->setDescricao( "Existem atividades dependentes desta atividade!" );
    }
    $this->inCodigoAtividade = $inCodigoAtividadeTmp;
    $this->inCodigoNivel     = $inCodigoNivelTmp;
    $this->stNomeAtividade   = $stNomeAtividadeTmp;

    return $obErro;
}

/**
    * Lista as Atividades segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividade(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " AND ltrim ( LN.COD_ATIVIDADE::varchar, '0') = '". ltrim ($this->inCodigoAtividade , '0')."'";
        //$stFiltroLote .= " AND ltrim(LL.valor,\'0\') = \'".ltrim($this->roRCIMLote->getNumeroLote(),'0')."\' ";
    }
    if ($this->stNomeAtividade) {
        $stFiltro .= " AND UPPER(LO.NOM_ATIVIDADE) LIKE UPPER('%".$this->stNomeAtividade."%') ";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " AND UPPER(LN.NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') ";
    }
    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " AND ltrim (valor_reduzido, '0' ) like '". ltrim ( $this->stValorReduzido, '0' ) ."%' ";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " AND ltrim (valor_reduzido, '0' ) like '". ltrim ( $this->stValorReduzido, '0' ) .".%' ";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " AND ltrim (valor_composto, '0' ) like '". ltrim ( $this->stValorComposto, '0' ) ."%' ";
    }
    $stFiltro .= " AND valor_reduzido <> '' ";
    $stOrdem = " ORDER BY nom_atividade, valor_composto";
    $obErro = $this->obTCEMAtividade->recuperaAtividadeAtiva( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista as Atividades do ultimo nivel conforme o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadesUltimoNivel(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia."       \n";
        $stFiltro .= " AND LN.COD_NIVEL = (SELECT                              \n";
        $stFiltro .= "                         max(cod_nivel)                  \n";
        $stFiltro .= "                      FROM                               \n";
        $stFiltro .= "                         economico.nivel_atividade_valor \n";
        $stFiltro .= "                      WHERE                              \n";
        $stFiltro .= "                         (1=1)                           \n";
        $stFiltro .= "                         AND cod_vigencia= ".$this->inCodigoVigencia." \n";
        $stFiltro .= "                          )                              \n";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " AND ltrim (valor_composto, '0' ) like '". ltrim ( $this->stValorComposto, '0' ) ."%' ";
    }
    $stFiltro .= " AND valor_reduzido <> '' ";
    $stOrdem = " ORDER BY nom_atividade, valor_composto";
    $obErro = $this->obTCEMAtividade->recuperaAtividadeAtiva( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarAtividadeAtualeProxima(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA >= ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " AND ltrim ( LN.COD_ATIVIDADE::VARCHAR, '0') = '". ltrim ($this->inCodigoAtividade , '0')."' ";
        //$stFiltroLote .= " AND ltrim(LL.valor,\'0\') = \'".ltrim($this->roRCIMLote->getNumeroLote(),'0')."\' ";
    }
    if ($this->stNomeAtividade) {
        $stFiltro .= " AND UPPER(LO.NOM_ATIVIDADE) LIKE UPPER('%".$this->stNomeAtividade."%') ";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " AND UPPER(LN.NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') ";
    }
    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " AND ltrim (valor_reduzido, '0' ) like '". ltrim ( $this->stValorReduzido, '0' ) ."%' ";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " AND ltrim (valor_reduzido, '0' ) like '". ltrim ( $this->stValorReduzido, '0' ) .".%' ";
    }
    if ($this->stValorComposto) {
        $stFiltro .= " AND ltrim (valor_composto, '0' ) like '%". ltrim ( $this->stValorComposto, '0' ) ."%' ";
    }
    $stFiltro .= " AND valor_reduzido <> '' ";
    $stOrdem = " ORDER BY nom_atividade, valor_composto";
    $obErro = $this->obTCEMAtividade->recuperaAtividadeAtiva( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
       
    return $obErro;
}

/**
    * Lista os Servicos da Atividade o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadeServico(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoAtividade) {
        $stFiltro .= " AND A.COD_ATIVIDADE = ".$this->inCodigoAtividade." ";
    }

    if ($this->stNomeAtividade) {
        $stFiltro .= " AND UPPER(A.NOM_ATIVIDADE) LIKE UPPER( '".$this->stNomeAtividade."%') ";
    }

    if ( $this->obRCEMServico->getCodigoServico() ) {
        $stFiltro .= " AND SV.COD_SERVICO = ".$this->obRCEMServico->getCodigoServico();
    }
    if ( $this->obRCEMServico->isAtivo() ) {
        $stFiltro .= " AND SA.ATIVO = true ";
    }

    if ( $this->obRCEMServico->getNomeServico() ) {
        $stFiltro .= " AND UPPER(SV.NOM_SERVICO) LIKE UPPER('".$this->obRCEMServico->getNomeServico()."%') ";
    }

    $stOrdem = " ORDER BY SV.COD_ESTRUTURAL ";
    $obErro = $this->obTCEMServicoAtividade->recuperaServicoAtividade( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Valida o codigo da atividade
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function validaCodigoAtividade($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " AND LN.COD_ATIVIDADE <> ".$this->inCodigoAtividade." ";
    }
    if ($this->stValor) {
        $stFiltro .= " AND LPAD( LN.valor, length(NI.mascara),'0' ) =";
        $stFiltro .= " LPAD( '".$this->stValor."', length(NI.mascara), '0' ) ";
    }
    if ( count($this->arChaveAtividade) ) {
        $stValorReduzido = $this->arChaveAtividade[ count($this->arChaveAtividade) - 1 ][3];
        $stFiltro .= " AND LN.valor_reduzido like '".$stValorReduzido."%' ";
    }
    $obErro = $this->obTCEMAtividade->recuperaAtividadeAtiva( $rsRecordSet, $stFiltro, "" , $boTransacao );
    
    if ( !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe uma atividade cadastrada com o código ".$this->stValor );
    }

    return $obErro;
}

/**
    * Adiciona no array  arChaveAtividade os códigos das atividades ao de niveis superiores
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function addCodigoAtividade($arChaveAtividade)
{
    $this->arChaveAtividade[] = $arChaveAtividade;//[0] = cod_nivel | [1] = cod_atividade | [2] = valor
}

/**
    * Recupera do banco de dados os dados da Atividade selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarAtividade($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodigoVigencia and $this->inCodigoNivel and $this->inCodigoAtividade) {
        $obErro = $this->listarAtividade( $rsAtividade, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stNomeNivel       = $rsAtividade->getCampo( "nom_nivel" );
            $this->stNomeAtividade   = $rsAtividade->getCampo( "nom_atividade" );
            $this->stMascara         = $rsAtividade->getCampo( "mascara" );
            $this->stValorComposto   = $rsAtividade->getCampo( "valor_composto" );
            $this->stValorReduzido   = $rsAtividade->getCampo( "valor_reduzido" );
            $arValor = explode( ".", $this->stValorReduzido );
            $this->stValor           = end( $arValor );
        }
    }

    return $obErro;
}

/**
    * Altera os dados do Atividade setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAtividade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //EXECUTA A INCLUSAO NA TABELA SERVICO
        $this->obTCEMAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
        $this->obTCEMAtividade->setDado( "nom_atividade", $this->stNomeAtividade   );
        $this->obTCEMAtividade->setDado( "cod_vigencia" , $this->inCodigoVigencia  );
        $this->obTCEMAtividade->setDado( "cod_estrutural","" );
        $this->obTCEMAtividade->setDado( "cod_nivel"    , $this->inCodigoNivel     );
        $obErro = $this->obTCEMAtividade->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMNivelAtividadeValor->setDado( "cod_vigencia"   , $this->inCodigoVigencia    );
            $this->obTCEMNivelAtividadeValor->setDado( "cod_nivel"      , $this->inCodigoNivel       );
            $this->obTCEMNivelAtividadeValor->setDado( "cod_atividade"  , $this->inCodigoAtividade   );
            $stValor = $this->stValor;
            $this->obTCEMNivelAtividadeValor->setDado( "valor"          , $stValor                   );
            $obErro = $this->obTCEMNivelAtividadeValor->alteracao( $boTransacao );
            // DADOS DE ALIQUOTA
            if ( !$obErro->ocorreu() ) {
                if ($this->boUltimoNivel) {
                    $this->obTCEMAliquotaAtividade->setDado( "cod_atividade", $this->getCodigoAtividade() );
                    $this->obTCEMAliquotaAtividade->setDado( "valor"        , $this->getAliquota() );
                    $obErro = $this->obTCEMAliquotaAtividade->alteracao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        if ( is_object($this->roUltimoCnae) ) {
                            if ( $this->roUltimoCnae->getCodigoCnae() ) {
                                $inTmpCodigoCnae = $this->roUltimoCnae->getCodigoCnae();
                                $this->roUltimoCnae->setCodigoCnae("");
                                $obErro = $this->roUltimoCnae->listarCnaeAtividade( $rsAtividadeCnae,$boTransacao);
                                $this->roUltimoCnae->setCodigoCnae($inTmpCodigoCnae);
                                if ( $rsAtividadeCnae->getNumLinhas() > 0 ) {
                                    $obErro = $this->roUltimoCnae->alterarAtividadeCnae( $boFlagTransacao, $boTransacao );
                                } else {
                                    $obErro = $this->roUltimoCnae->incluirAtividadeCnae( $boFlagTransacao, $boTransacao );
                                }
                            } else {
                                $obErro = $this->roUltimoCnae->excluirAtividadeCnae( $boFlagTransacao, $boTransacao );
                            }
                        }
                        //ALTERA NA TABELA DE ATIVIDADE_PROFISSAO
                        if ( !$obErro->ocorreu() ) {
                            if ( count( $this->arRCEMProfissao ) ) {
                                $obErro = $this->alterarAtividadeProfissao( $boFlagTransacao, $boTransacao );
                            } else {
                                $this->addAtividadeProfissao();
                                $obErro = $this->roUltimaProfissao->excluirAtividadeProfissao( $boFlagTransacao, $boTransacao );
                            }
                            //ALTERA NA TABELA DE ATIVIDADE_ELEMENTO
                            if ( !$obErro->ocorreu() ) {
                                if ( count( $this->arRCEMElemento ) ) {
                                    $obErro = $this->alterarAtividadeElemento( $boFlagTransacao, $boTransacao );
                                } else {
                                    $this->addAtividadeElemento();
                                    $obErro = $this->roUltimoElemento->excluirElementoAtividade( $boFlagTransacao, $boTransacao );
                                }
                                //ALTERA NA TABELA DE SERVICO_ATIVIDADE
                                if ( !$obErro->ocorreu() ) {
                                    $arServico = array();
                                    $arServicoNovo = array();
                                    $stCondicao = ' where cod_atividade = '.$this->inCodigoAtividade;
                                    //RECUPERA TODOS OS SERVICOS DA ATIVIDADE E ATRIBUI FALSE PARA INATVAR O SERVICO
                                    $obErro = $this->obTCEMServicoAtividade->recuperaTodos( $rsRecordSet, $stCondicao, 'cod_servico', $boTransacao);
                                    if (!$obErro->ocorreu()) {
                                        while (!$rsRecordSet->eof()) {
                                            $arServico[$rsRecordSet->getCampo('cod_servico')] = 'f';
                                            $rsRecordSet->proximo();
                                        }                                 

                                        //CASO O SERVICO NÃO TENHA SIDO EXCLUIDO OU TENHA SIDO ATIVADO NOVAMENTE SETA O VALOR TRUE REATIVANDO/ATIVANDO
                                        //O SERVICO
                                        foreach ($this->arServico as $obRCEMServico) {
                                            if ( isset ($arServico[$obRCEMServico->getCodigoServico()])  ) {
                                                    $arServico[$obRCEMServico->getCodigoServico()]='t';
                                            } else {
                                                $arServicoNovo[$obRCEMServico->getCodigoServico()]='t';
                                            }
                                        }
                                        foreach ($arServico AS $inCodServico => $boAtivo) {
                                            $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                                            $this->obTCEMServicoAtividade->setDado( "cod_servico"  , $inCodServico );
                                            $this->obTCEMServicoAtividade->setDado( "ativo"  , $boAtivo );
                                            $obErro = $this->obTCEMServicoAtividade->alteracao( $boTransacao );
                                            if ( $obErro->ocorreu() ) {
                                                break;
                                            }
                                        }
                                        if (!$obErro->ocorreu()) {
                                            foreach ($arServicoNovo AS $inCodServico => $boAtivo) {
                                                $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                                                $this->obTCEMServicoAtividade->setDado( "cod_servico"  , $inCodServico );
                                                //$this->obTCEMServicoAtividade->setDado( "ativo"  , $boAtivo );
                                                $obErro = $this->obTCEMServicoAtividade->inclusao( $boTransacao );
                                                if ( $obErro->ocorreu() ) {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                if ( !$obErro->ocorreu() && $this->getValorComposto() ) {
                                    $this->obTCEMAtividade->setDado( "valor" , $this->getValorComposto() );
                                    $obErro = $this->obTCEMAtividade->atualizaAtividade( $boTransacao );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividade);

    return $obErro;
}

/**
    * Adiciona um objeto de profissão no de atividade
    * @access Public
*/
function addAtividadeProfissao()
{
    $this->arRCEMProfissao[] = new RCEMProfissao( $this );
    $this->roUltimaProfissao = &$this->arRCEMProfissao[ count($this->arRCEMProfissao) - 1 ];
}

/**
    * Adiciona um objeto de elemento no de atividade
    * @access Public
*/
function addAtividadeElemento()
{
    $this->arRCEMElemento[] = new RCEMElemento( $this );
    $this->roUltimoElemento = &$this->arRCEMElemento[ count($this->arRCEMElemento) - 1 ];
}

/**
    * Adiciona um objeto de cnae no de atividade
    * @access Public
*/
function addAtividadeCnae()
{
    $this->arRCEMCnae[] = new RCEMCnae( $this );
    $this->roUltimoCnae = &$this->arRCEMCnae[ count($this->arRCEMCnae) - 1 ];
}

/**
    * Adiciona um objeto Servico
    * @access Public
    * @param  Array $arChaveTrecho
    * @return Object Objeto Erro
*/
function addServico($arChaveServico)
{
    $this->obRCEMServico = new RCEMServico;
    $this->obRCEMServico->setCodigoServico( $arChaveServico['inCodigoServico'] );
    $this->obRCEMServico->setNomeServico  ( $arChaveServico['stNomeServico'] );

    $obErro = $this->obRCEMServico->consultarServico( $rsRecorSet );

    if ( !$obErro->ocorreu() ) {
        $this->arServico[] = $this->obRCEMServico;
    }

    return $obErro;
}

/**
    * Faz a verificação se o servico já esta relacionado a atividade e inclui ou exclui da tabela de relacionamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAtividadeServico($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $inCodigoServicoTmp = $this->obRCEMServico->getCodigoServico();
        $stNomeAtividadeTmp = $this->stNomeAtividade;
        $this->obRCEMServico->setCodigoServico( "" );
        $this->stNomeAtividade = '';
        $obErro = $this->listarAtividadeServico( $rsRecordSet );

        if ( !$obErro->ocorreu() ) {
            $arAtividadeServico = array();
            while ( !$rsRecordSet->eof() ) {
                $inCodigoServico   = $rsRecordSet->getCampo( "cod_servico" );
                $inCodigoAtividade = $rsRecordSet->getCampo( "cod_atividade" );
                $stChave = $inCodigoServico.".".$inCodigoAtividade;
                $arAtividadeServico[$stChave] = true;
                $rsRecordSet->proximo();
            }

            $this->obRCEMServico->setCodigoServico( $inCodigoServicoTmp );
            $this->stNomeAtividade = $stNomeAtividadeTmp;
            foreach ($this->arServico as $obRCEMServico) {
                $stChaveObjeto  = $obRCEMServico->getCodigoServico().".".$this->inCodigoAtividade;
                if ( !isset( $arAtividadeServico[$stChaveObjeto] ) ) {
                    $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                    $this->obTCEMServicoAtividade->setDado( "cod_servico"  , $obRCEMServico->getCodigoServico() );
                    $obErro = $this->obTCEMServicoAtividade->inclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                } else {
                    unset( $arAtividadeServico[$stChaveObjeto] );
                }

            }

            if ( !$obErro->ocorreu() ) {
                foreach ($arAtividadeServico as $stChave => $boValor) {
                    $arChave = explode(".",$stChave);
                    $this->obTCEMServicoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                    $this->obTCEMServicoAtividade->setDado( "cod_servico"  , $arChave[0] );
                    $obErro = $this->obTCEMServicoAtividade->exclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }

        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMServicoAtividade );

    return $obErro;
}

/**
    * Altera as profissões para a atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAtividadeProfissao($boFlagTransacao, $boTransacao = "")
{
    $obErro = $this->roUltimaProfissao->obTCEMAtividadeProfissao->recuperaTodos( $rsAtividadeProfissao,"","",$boTransacao );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsAtividadeProfissao->eof() ) {
            if ( $rsAtividadeProfissao->getCampo ('cod_atividade') == $this->getCodigoAtividade() ) {
            $inCodigoProfissao = $rsAtividadeProfissao->getCampo( "cod_profissao" );
            $arAtividadeProfissao[$inCodigoProfissao] = true;
            }
            $rsAtividadeProfissao->proximo();
        }
        foreach ($this->arRCEMProfissao as $obRCEMProfissao) {
            if ( !isset( $arAtividadeProfissao[$obRCEMProfissao->getCodigoProfissao()] ) ) {
                $this->roUltimaProfissao->obTCEMAtividadeProfissao->setDado( "cod_atividade", $this->inCodigoAtividade );
                $this->roUltimaProfissao->obTCEMAtividadeProfissao->setDado( "cod_profissao", $obRCEMProfissao->getCodigoProfissao());
                $obErro = $this->roUltimaProfissao->obTCEMAtividadeProfissao->inclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            } else {
                unset( $arAtividadeProfissao[$obRCEMProfissao->getCodigoProfissao()] );
            }
        }
        if ( !$obErro->ocorreu() ) {
            if(!empty($arAtividadeProfissao)){
                foreach ($arAtividadeProfissao as $inCodigoProfissao => $boValor) {
                    $this->roUltimaProfissao->obTCEMAtividadeProfissao->setDado( "cod_atividade", $this->inCodigoAtividade );
                    $this->roUltimaProfissao->obTCEMAtividadeProfissao->setDado( "cod_profissao", $inCodigoProfissao);
                    $obErro = $this->roUltimaProfissao->obTCEMAtividadeProfissao->exclusao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }

    return $obErro;
}

/**
    * Altera os elementos para a atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAtividadeElemento($boFlagTransacao, $boTransacao = "")
{
    $obErro = $this->roUltimoElemento->obTCEMElementoAtividade->recuperaElementoAtividade( $rsElementoAtividade,"","",$boTransacao );
    if ( !$obErro->ocorreu() ) {

        $arElementoAtividade = array();
        while ( !$rsElementoAtividade->eof() ) {

            if ( $rsElementoAtividade->getCampo ('cod_atividade') == $this->getCodigoAtividade() ) {

                $inCodigoElemento = $rsElementoAtividade->getCampo( "cod_elemento" );
                $arElementoAtividade[$inCodigoElemento] = true;

            }
            $rsElementoAtividade->proximo();
        }
        foreach ($this->arRCEMElemento as $obRCEMElemento) {

            if ( !isset( $arElementoAtividade[$obRCEMElemento->getCodigoElemento()] ) ) {
                $this->roUltimoElemento->obTCEMElementoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                $this->roUltimoElemento->obTCEMElementoAtividade->setDado( "cod_elemento",  $obRCEMElemento->getCodigoElemento());
                $obErro = $this->roUltimoElemento->obTCEMElementoAtividade->inclusao( $boTransacao );

                if ( $obErro->ocorreu() ) {
                    break;
                }
            } else {
                unset( $arElementoAtividade[$obRCEMElemento->getCodigoElemento()] );
            }
        }
        if ( !$obErro->ocorreu() ) {
            foreach ($arElementoAtividade as $inCodigoElemento => $boValor) {
                $this->roUltimoElemento->obTCEMElementoAtividade->setDado( "cod_atividade", $this->inCodigoAtividade );
                $this->roUltimoElemento->obTCEMElementoAtividade->setDado( "cod_elemento",  $inCodigoElemento);
                $obErro = $this->roUltimoElemento->obTCEMElementoAtividade->exclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }

    return $obErro;
}

/**
    * Lista as Atividades segundo o filtro setado especial para parte de COMBO
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadeCombo(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND \r\n";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND \r\n";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " COD_ATIVIDADE = ".$this->inCodigoAtividade." AND \r\n";
    }
    if ($this->stNomeAtividade) {
        $stFiltro .= " UPPER(NOM_ATIVIDADE) LIKE UPPER('%".$this->stNomeAtividade."%') AND\r\n";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " UPPER(NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') AND \r\n";
    }
    if ($this->stValorReduzido and  $this->stNomeNivel == 1) {
        $stFiltro .= " valor_reduzido like '".$this->stValorReduzido."%' AND \r\n";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " valor_reduzido like '".$this->stValorReduzido.".%' AND \r\n ";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ".substr($stFiltro,0,-8);
    }

    $stOrdem = " ORDER BY nom_atividade";
    $obErro = $this->obTCEMAtividade->recuperaAtividadeCombo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarAtividadeComboCNAE(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND \r\n ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND \r\n ";
    }
    if ($this->inCodigoAtividade) {
        $stFiltro .= " COD_ATIVIDADE = ".$this->inCodigoAtividade." AND \r\n ";
    }
    if ($this->stNomeAtividade) {
        $stFiltro .= " UPPER(NOM_ATIVIDADE) LIKE UPPER('%".$this->stNomeAtividade."%') AND \r\n ";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " UPPER(NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') AND \r\n ";
    }
    if ( $this->stValorReduzido && ($this->stNomeNivel == 1 || $this->inCodigoNivel >= 3 ) ) {
        $stFiltro .= " cod_estrutural like '".$this->stValorReduzido."%' AND \r\n ";
    } elseif ($this->stValorReduzido) {
        $stFiltro .= " cod_estrutural like '".$this->stValorReduzido.".%' AND \r\n ";
    }

    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ".substr($stFiltro,0,-8);
    }

    $stOrdem = " ORDER BY nom_atividade";
    $obErro = $this->obTCEMAtividade->recuperaAtividadeCombo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista as Atividades que estão definidas na inscrição
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAtividadeInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoAtividade) {
        $stFiltro .= " COD_ATIVIDADE = ".$this->inCodigoAtividade." AND \r\n";
    }
    if ($stFiltro) {
        $stFiltro = "\r\n\t WHERE ".substr($stFiltro,0,-6);
    }

    $stOrdem = " ORDER BY cod_atividade";
    $obErro = $this->obTCEMAtividadeCadastroEconomico->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}

?>
