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
     * Classe de regra de negócio para Hierarquia de Serviços
     * Data de Criação: 19/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Tonismar Régis Bernardo

     * @package URBEM
     * @subpackage Regra

    * $Id: RCEMNivelServico.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.02.02
 */

/*
$Log$
Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
 include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelServico.class.php"     );
 include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelServicoValor.class.php");
 include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMVigenciaServico.class.php"  );

 class RCEMNivelServico
 {
 /**
     * @access Private
     * @var Integer
 */
 var $inCodigoNivel;
 /**
     * @access Private
     * @var Integer
 */
 var $inCodigoVigencia;
 /**
     * @access Private
     * @var String
 */
 var $stNomeNivel;
 /**
     * @access Private
     * @var String
 */
 var $stMascara;
 /**
     * @access Private
     * @var String
 */
 var $dtInicioVigencia;
 /**
     * @access Private
     * @var Object
 */
 var $obTCEMNivelServico;
 /**
     * @access Private
     * @var Object
 */
 var $obTCEMNivelServicoValor;
 /**
     * @access Private
     * @var Object
 */
 var $obTCEMVigenciaServico;
 /**
     * @access Private
     * @var Object
 */
 var $boTransacao;

 /**
     * @access Public
     * @param String $valor
 */
 function setCodigoNivel($valor) { $this->inCodigoNivel     = $valor;          }
 /**
     * @access Public
     * @param String $valor
 */
 function setCodigoVigencia($valor) { $this->inCodigoVigencia  = $valor;          }
 /**
     * @access Public
     * @param String $valor
 */
 function setNomeNivel($valor) { $this->stNomeNivel  = $valor;               }
 /**
     * @access Public
     * @param String $valor
 */
 function setMascara($valor) { $this->stMascara  = $valor;                 }
 /**
     * @access Public
     * @param String $valor
 */
 function setInicioVigencia($valor) { $this->dtInicioVigencia  = $valor;          }
 /**
     * @access Public
     * @param String $valor
 */
 function setTCEMNivelServico($valor) { $this->obTCEMNivelServico  = $valor;      }
 /**
     * @access Public
     * @param String $valor
 */
 function setTCEMNivelServicoValor($valor) { $this->obTCEMNivelServicoValor  = $valor; }
 /**
     * @access Public
     * @param String $valor
 */
 function setTCEMVigenciaServico($valor) { $this->obTCEMVigenciaServico  = $valor; }

 /**
     * @access Public
     * @return Integer
 */
 function getCodigoNivel() { return $this->inCodigoNivel;            }
 /**
     * @access Public
     * @return Integer
 */
 function getCodigoVigencia() { return $this->inCodigoVigencia;         }
 /**
     * @access Public
     * @return String
 */
 function getNomeNivel() { return $this->stNomeNivel;              }
 /**
     * @access Public
     * @return String
 */
 function getMascara() { return $this->stMascara;                }
 /**
     * @access Public
     * @return String
 */
 function getInicioVigencia() { return $this->dtInicioVigencia;         }
 /**
     * @access Public
     * @return Object
 */
 function getTCEMNivelServico() { return $this->obTCEMNivelServico;     }
 /**
     * @access Public
     * @return Object
 */
 function getTCEMNivelServicoValor() { return $this->obTCEMNivelServicoValor; }
 /**
     * @access Public
     * @return Object
 */
 function getTCEMVigenciaServico() { return $this->obTCEMVigenciaServico; }

 /**
      * Método construtor
      * @access Private
 */
 function RCEMNivelServico()
 {
     $this->obTCEMNivelServico         = new TCEMNivelServico;
     $this->obTCEMNivelServicoValor    = new TCEMNivelServicoValor;
     $this->obTCEMVigenciaServico      = new TCEMVigenciaServico;
     $this->obTransacao                = new Transacao;
 }

 /**
     * Inclui os dados setados na tabela de Nivel Serviço
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function incluirNivel($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $obErro = $this->validaNomeNivel( $boTransacao );
         if ( !$obErro->ocorreu() ) {
             $this->obTCEMNivelServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
             $obErro = $this->obTCEMNivelServico->proximoCod( $this->inCodigoNivel, $boTransacao );
             if ( !$obErro->ocorreu() ) {
                 $this->obTCEMNivelServico->setDado( "cod_nivel"   , $this->inCodigoNivel    );
                 $this->obTCEMNivelServico->setDado( "nom_nivel"   , $this->stNomeNivel      );
                 $this->obTCEMNivelServico->setDado( "mascara"     , $this->stMascara        );
                 $obErro = $this->obTCEMNivelServico->inclusao( $boTransacao );
             }
         }
      }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelServico );

     return $obErro;
 }

 /**
     * Altera os dados do Nivel Serviço selecionado no banco de dados
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function alterarNivel($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obTCEMNivelServico->setDado( "cod_nivel"   , $this->inCodigoNivel    );
         $this->obTCEMNivelServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
         $this->obTCEMNivelServico->setDado( "nom_nivel"   , $this->stNomeNivel      );
         $obErro = $this->validaNomeNivel( $boTransacao );
         if ( !$obErro->ocorreu() ) {
             $this->obTCEMNivelServico->setDado( "mascara", $this->stMascara        );
             $obErro = $this->obTCEMNivelServico->alteracao( $boTransacao );
         }
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelServico );

     return $obErro;
 }

 /**
     * Altera os dados da Vigência Serviço selecionado no banco de dados
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function alterarVigencia($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obTCEMVigenciaServico->setDado( "dt_inicio"   , $this->dtInicioVigencia );
         $this->obTCEMVigenciaServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
         $obErro = $this->obTCEMVigenciaServico->alteracao( $boTransacao );
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMVigenciaServico );

     return $obErro;
 }

 /**
     * Exclui o Nivel Serviço selecionado do banco de dados
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */

 function excluirNivel($boTransacao = "")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->recuperaProximoNivel( $rsProximoNivel );
        if ( $rsProximoNivel->getNumLinhas() > 0 ) {
            $obErro->setDescricao("Nível ".$this->getCodigoNivel()." não é o último da hierarquia!");
        } else {
            $this->obTCEMNivelServicoValor->setDado( "cod_nivel" , $this->inCodigoNivel );
            $this->obTCEMNivelServicoValor->setDado( "cod_vigencia" , $this->inCodigoVigencia );
            $obErro = $this->obTCEMNivelServicoValor->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMNivelServico->setDado( "cod_nivel"   , $this->inCodigoNivel    );
                $this->obTCEMNivelServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
                $obErro = $this->obTCEMNivelServico->exclusao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    $obErro->setDescricao("Nível ".$this->getCodigoNivel()." possui referências cadastradas no sistema!");
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelServico );

    return $obErro;
}

/**
     * Recupera a máscara dos níveis
     * @access Public
     * @param  Object $rsNivel     Objet RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao Parâmetro Transa?ão
     * @return Object Objeto $obErro
 */
function recuperaMascaraNiveis(&$rsNivel, $obTransacao)
{
    if ($this->inCodigoVigencia) {
        $stFiltro = " WHERE COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );
}

 /**
     * Exclui o Vigencia Servico selecionado do banco de dados
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function excluirVigencia($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obTCEMVigenciaServico->setDado( "cod_nivel"   , $this->inCodigoNivel    );
         $this->obTCEMVigenciaServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
         $obErro = $this->obTCEMVigenciaServico->exclusao( $boTransacao );
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMVigenciaServico );

     return $obErro;
 }

 /**
     * Recupera do banco de dados os dados do Nivel Servico selecionado
     * @access Public
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function consultarNivel($boTransacao = "")
 {
     $this->obTCEMNivelServico->setDado( "cod_nivel"   , $this->inCodigoNivel    );
     $this->obTCEMNivelServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
     $obErro = $this->obTCEMNivelServico->recuperaPorChave( $rsNivel, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->stNomeNivel = $rsNivel->getCampo( "nom_nivel" );
         $this->stMascara   = $rsNivel->getCampo( "mascara"   );
     }

     return $obErro;
 }

 /**
     * Lista os Niveis Servico segundo o filtro setado
     * @access Public
     * @param  Object $rsNivel     Objeto RecrdSet preenchido com os dados selecionados
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function listarNiveis(&$rsNivel, $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodigoNivel) {
         $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND";
     }
     if ($this->inCodigoVigencia) {
         $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
     }
     if ($stFiltro) {
         $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
     }
     $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
     $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

     return $obErro;
 }

 /**
     * Lista os Niveis Servico com Data de Vigência segundo o filtro setado
     * @access Public
     * @param  Object $rsNivel     Objeto RecrdSet preenchido com os dados selecionados
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function listarNiveisVigencia(&$rsNivel, $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodigoNivel) {
         $stFiltro .= " AND NA.COD_NIVEL = ".$this->inCodigoNivel;
     }
     if ($this->inCodigoVigencia) {
         $stFiltro .= " AND VA.COD_VIGENCIA = ". $this->inCodigoVigencia;
     }

     if ($this->stNomeNivel) {
         $stFiltro .= " AND UPPER( NA.NOM_NIVEL ) LIKE UPPER('".$this->stNomeNivel."%')";
     }

     $stOrdem = " ORDER BY DT_INICIO DESC ";
     $obErro = $this->obTCEMNivelServico->recuperaNiveisVigencia( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

     return $obErro;
 }

 /**
     * Lista as Vigências Servico segundo o filtro setado
     * @access Public
     * @param  Object $rsNivel     Objeto RecrdSet preenchido com os dados selecionados
     * @param  Object $obTransacao Parâmetro Transação
     * @return Object Objeto Erro
 */
 function listarVigencia(&$rsVigencia, $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodigoVigencia) {
         $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND";
     }
     if ($this->dtInicioVigencia) {
         $stFiltro .= " dt_inicio >= TO_DATE('".$this->dtInicioVigencia."','dd/mm/yyyy') AND";
     }
     if ($stFiltro) {
         $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
     }
     $stOrdem = " ORDER BY COD_VIGENCIA DESC ";
     $obErro = $this->obTCEMVigenciaServico->recuperaVigenciasValidas( $rsVigencia, $stFiltro, $stOrdem, $boTransacao );
     //$this->obTCEMVigenciaServico->debug();
     return $obErro;
 }

/**
    * Gera a mascara segundo o filtro setado
    * @access Public
    * @param  Object $stMascara String com a mascara
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function geraMascara(&$stMascara , $boTransacao = "")
{
    $obErro = $this->listarNiveis( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stMascara = "";
        while ( !$rsNivel->eof() ) {
            $stMascara .= $rsNivel->getCampo( "mascara" ).".";
            $rsNivel->proximo();
        }
        if ($stMascara) {
            $stMascara = substr( $stMascara, 0, strlen( $stMascara ) - 1);
        }
    }

    return $obErro;
}

/**
    * Lista os Niveis Servico posteriores segundo o nivel setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveisPosteriores(&$rsNivel, $boTransacao = "")
{
   $stFiltro = "";
   if ($this->inCodigoNivel) {
       $stFiltro .= " COD_NIVEL > ".$this->inCodigoNivel." AND";
   }
   if ($this->inCodigoVigencia) {
       $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
   }
   if ($stFiltro) {
       $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
   }
   $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
   $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

/**
    * Lista os Niveis Servico anteriores segundo o nivel setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveisAnteriores(&$rsNivel, $boTransacao = "")
{
   $stFiltro = "";
   if ($this->inCodigoNivel) {
       $stFiltro .= " COD_NIVEL < ".$this->inCodigoNivel." AND";
   }
   if ($this->inCodigoVigencia) {
       $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
   }
   if ($stFiltro) {
       $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
   }
   $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
   $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

function validaNomeNivel($boTransacao = "")
{
    $stFiltro = " WHERE  NOM_NIVEL = '".$this->stNomeNivel."' ";
    $stFiltro .= " AND COD_VIGENCIA = ".$this->inCodigoVigencia;
    if ($this->inCodigoNivel AND $this->inCodigoVigencia) {
        $stFiltro .= " AND COD_NIVEL <> ".$this->inCodigoNivel;
    }
    $stOrdem = "";
    $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsNivel->eof() ) {
        $obErro->setDescricao( "Já existe outro nível de atividade cadastrado com o nome ".$this->stNomeNivel."!" );
    }

    return $obErro;
}

/**
    * Inclui os dados setados na tabela de vigencia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMVigenciaServico->proximoCod( $this->inCodigoVigencia, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMVigenciaServico->setDado( "cod_vigencia", $this->inCodigoVigencia );
            $this->obTCEMVigenciaServico->setDado( "dt_inicio", $this->dtInicioVigencia );
            $obErro = $this->obTCEMVigenciaServico->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}
/**
    * Recupera o a vigencia atual
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciaAtual(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTCEMVigenciaServico->recuperaVigenciaAtual( $rsRecordSet,  $boTransacao );
    $this->inCodigoVigencia = $rsRecordSet->getCampo( "cod_vigencia" );

    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia setada
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel < ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " DESC ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//    $this->obTCEMNivelServico->debug();
    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia corrente
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivelAtual(&$rsRecordSet , $boTransacao = "")
{
    $rsRecordSet = new RecordSet;
    $obErro = $this->recuperaVigenciaAtual( $rsVigencia, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsVigencia->eof() ) {
        $this->inCodigoVigencia = $rsVigencia->getCampo( "cod_vigencia" );
        $obErro = $this->recuperaUltimoNivel( $rsRecordSet, $boTransacao );
    }

    return $obErro;
}

/**
    * Recupera o proximo nivel da vigencia em relacao ao nivel
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaProximoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel > ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCEMNivelServico->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados ultima data cadastrada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDataUltimaVigencia(&$rsDataUltimaVigencia, $boTransacao = "")
{
    $obErro = $this->obTCEMVigenciaServico->recuperaDataUltimaVigencia( $rsDataUltimaVigencia, $boTransacao );
    if ( !$obErro->ocorreu() ) {
    }

    return $obErro;
}

}

?>
