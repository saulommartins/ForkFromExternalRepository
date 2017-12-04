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
    * Classe de regra de negócio para Hierarquia de Atividades
    * Data de Criação: 17/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMNivelAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06
*/

/*
$Log$
Revision 1.7  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelAtividade.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelAtividadeValor.class.php");
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMVigenciaAtividade.class.php"  );

class RCEMNivelAtividade
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
var $obTCEMNivelAtividade;
/**
    * @access Private
    * @var Object
*/
var $obTCEMNivelAtividadeValor;
/**
    * @access Private
    * @var Object
*/
var $obTCEMVigenciaAtividade;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

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
function setTCEMNivelAtividade($valor) { $this->obTCEMNivelAtividade  = $valor;      }
/**
    * @access Public
    * @param String $valor
*/
function setTCEMNivelAtividadeValor($valor) { $this->obTCEMNivelAtividadeValor  = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTCEMVigenciaAtividade($valor) { $this->obTCEMVigenciaAtividade  = $valor; }

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
function getTCEMNivelAtividade() { return $this->obTCEMNivelAtividade;     }
/**
    * @access Public
    * @return Object
*/
function getTCEMNivelAtividadeValor() { return $this->obTCEMNivelAtividadeValor; }
/**
    * @access Public
    * @return Object
*/
function getTCEMVigenciaAtividade() { return $this->obTCEMVigenciaAtividade; }
/**
     * Método construtor
     * @access Private
*/
function RCEMNivelAtividade()
{
    $this->obTCEMNivelAtividade         = new TCEMNivelAtividade;
    $this->obTCEMNivelAtividadeValor    = new TCEMNivelAtividadeValor;
    $this->obTCEMVigenciaAtividade      = new TCEMVigenciaAtividade;
    $this->obTransacao                  = new Transacao;
}

/**
    * Inclui os dados setados na tabela de Nivel Atividade
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
            $this->obTCEMNivelAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
            $obErro = $this->obTCEMNivelAtividade->proximoCod( $this->inCodigoNivel, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCEMNivelAtividade->setDado( "cod_nivel"   , $this->inCodigoNivel    );
                $this->obTCEMNivelAtividade->setDado( "nom_nivel"   , $this->stNomeNivel      );
                $this->obTCEMNivelAtividade->setDado( "mascara"     , $this->stMascara        );
                $obErro = $this->obTCEMNivelAtividade->inclusao( $boTransacao );
            }
        }
     }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelAtividade );

    return $obErro;
}

/**
    * Altera os dados do Nivel Atividade selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarNivel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMNivelAtividade->setDado( "cod_nivel"   , $this->inCodigoNivel    );
        $this->obTCEMNivelAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $this->obTCEMNivelAtividade->setDado( "nom_nivel"   , $this->stNomeNivel      );
        $obErro = $this->validaNomeNivel( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMNivelAtividade->setDado( "mascara", $this->stMascara        );
            $obErro = $this->obTCEMNivelAtividade->alteracao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelAtividade );

    return $obErro;
}

/**
    * Altera os dados da Vigência Atividade selecionado no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMVigenciaAtividade->setDado( "dt_inicio"   , $this->dtInicioVigencia );
        $this->obTCEMVigenciaAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $obErro = $this->obTCEMVigenciaAtividade->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMVigenciaAtividade );

    return $obErro;
}

/**
    * Exclui o Nivel Atividade selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirNivel($boTransacao = "")
{
    include_once( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

    $obRCEMAtividade = new RCEMAtividade;
    $obRCEMAtividade->setCodigoNivel( $this->inCodigoNivel );
    $obRCEMAtividade->setCodigoVigencia( $this->inCodigoVigencia );
    $obRCEMAtividade->listarAtividade( $rsListaAtividade );
    if ( !$rsListaAtividade->Eof() ) {
        $obErro = new Erro;
        //-----------------
        $obErro->setDescricao("Existem atividades relacionadas ao nível selecionado! (".$this->getCodigoNivel().")");

        return $obErro;
    }

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMNivelAtividade->setDado( "cod_nivel"   , $this->inCodigoNivel    );
        $this->obTCEMNivelAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $obErro = $this->listarNiveisPosteriores( $rsRecordSet, $boTransacao );

        if ( $rsRecordSet->eof() ) {
            $obErro = $this->obTCEMNivelAtividade->exclusao( $boTransacao );
        } else {
            $obErro->setDescricao( "Existem níveis dependentes deste nível." );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMNivelAtividade );

    return $obErro;
}

/**
    * Exclui o Vigencia Atividade selecionado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirVigencia($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMVigenciaAtividade->setDado( "cod_nivel"   , $this->inCodigoNivel    );
        $this->obTCEMVigenciaAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
        $obErro = $this->obTCEMVigenciaAtividade->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMVigenciaAtividade );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Nivel Atividade selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarNivel($boTransacao = "")
{
    $this->obTCEMNivelAtividade->setDado( "cod_nivel"   , $this->inCodigoNivel    );
    $this->obTCEMNivelAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
    $obErro = $this->obTCEMNivelAtividade->recuperaPorChave( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomeNivel = $rsNivel->getCampo( "nom_nivel" );
        $this->stMascara   = $rsNivel->getCampo( "mascara"   );
    }

    return $obErro;
}

/**
    * Lista os Niveis Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $obTransacao );
}
/**
    * Lista os Niveis Atividade com Data de Vigência segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
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

    $stOrdem = " ORDER BY VA.COD_VIGENCIA ";
    $obErro = $this->obTCEMNivelAtividade->recuperaNiveisVigencia( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista as Vigências Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVigencia(&$rsVigencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    
    $stOrdem = " ORDER BY DT_INICIO DESC";
    $obErro = $this->obTCEMVigenciaAtividade->recuperaTodos( $rsVigencia, $stFiltro, $stOrdem, $boTransacao );
    
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
    * Lista os Niveis Atividade posteriores segundo o nivel setado
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
   $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

/**
    * Lista os Niveis Atividade anteriores segundo o nivel setado
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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );
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
        $obErro = $this->obTCEMVigenciaAtividade->proximoCod( $this->inCodigoVigencia, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMVigenciaAtividade->setDado( "cod_vigencia", $this->inCodigoVigencia );
            $this->obTCEMVigenciaAtividade->setDado( "dt_inicio", $this->dtInicioVigencia );
            $obErro = $this->obTCEMVigenciaAtividade->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

    return $obErro;
}

/**
    * Recupera a vigencia do periodo atual
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciaAtual(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTCEMVigenciaAtividade->recuperaVigenciaAtual( $rsRecordSet,  $boTransacao );
    
    $this->setCodigoVigencia( $rsRecordSet->getCampo( "cod_vigencia" ) );

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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

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
    $obErro = $this->obTCEMNivelAtividade->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

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
    $obErro = $this->obTCEMVigenciaAtividade->recuperaDataUltimaVigencia( $rsDataUltimaVigencia, $boTransacao );
    if ( !$obErro->ocorreu() ) {
    }

    return $obErro;
}

}
?>
