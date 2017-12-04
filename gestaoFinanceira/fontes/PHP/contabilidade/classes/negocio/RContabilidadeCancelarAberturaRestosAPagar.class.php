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
    * Classe de Regra de Negócio Contabilidade Cancelar Abertura de Restos a Pagar
    * Data de Criação   : 20/01/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Regra

    * @ignore

    $Id: RContabilidadeCancelarAberturaRestosAPagar.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                             );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLote.class.php"                                );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php"                          );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeValorLancamento.class.php"                     );
include_once (CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaDebito.class.php"                          );
include_once (CAM_GF_CONT_MAPEAMENTO."TContabilidadeContaCredito.class.php"                         );

class RContabilidadeCancelarAberturaRestosAPagar
{
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeLote;
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeLancamento;
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeValorLancamento;
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeContaDebito;
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeContaCredito;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;

/**
    * @var String
    * @access Private
*/
var $stExercicio;

/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;

/**
    * @var Integer
    * @access Private
*/
var $inCodLote;

/**
    * @var String
    * @access Private
*/
var $stTipo;

/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeLote($valor) { $this->obTContabilidadeLote = $valor;           }
/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeLancamento($valor) { $this->obTContabilidadeLancamento = $valor;     }
/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeValorLancamento($valor) { $this->obTContabilidadeValorLancamento = $valor;}
/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeContaDebito($valor) { $this->obTContabilidadeContaDebito = $valor;    }
/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeContaCredito($valor) { $this->obTContabilidadeContaCredito = $valor;   }
/**
     * @access Public
     * @param Object $valor
*/

function setTransacao($valor) { $this->obTransacao = $valor;    }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor;   }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodLote($valor) { $this->inCodLote  = $valor;     }
/**
     * @access Public
     * @param String $valor
*/
function setTipo($valor) { $this->stTipo   = $valor;       }

/**
    * @access Public
    * @return Object
*/
function getTContabilidadeLote() { return $this->obTContabilidadeLote;             }
/**
    * @access Public
    * @return Object
*/
function getTContabilidadeLancamento() { return $this->obTContabilidadeLancamento;       }
/**
    * @access Public
    * @return Object
*/
function getTContabilidadeValorLancamento() { return $this->obTContabilidadeValorLancamento;  }
/**
    * @access Public
    * @return Object
*/
function getTContabilidadeContaDebito() { return $this->obTContabilidadeContaDebito;      }
/**
    * @access Public
    * @return Object
*/
function getTContabilidadeContaCredito() { return $this->obTContabilidadeContaCredito;     }

/**
     * @access Public
     * @return Integer $valor
*/
function getTransacao() { return $this->obTransacao;      }
/**
     * @access Public
     * @return String $valor
*/
function getExercicio() { return $this->stExercicio;      }
/**
     * @access Public
     * @return Integer $valor
*/
function getCodEntidade() { return $this->inCodEntidade;    }
/**
     * @access Public
     * @return Integer $valor
*/
function getCodLote() { return $this->inCodLote;        }
/**
     * @access Public
     * @return String $valor
*/
function getTipo() { return $this->stTipo;           }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeCancelarAberturaRestosAPagar()
{
    $this->obTContabilidadeContaCredito    = new TContabilidadeContaCredito;
    $this->obTContabilidadeContaDebito     = new TContabilidadeContaDebito;
    $this->obTContabilidadeValorLancamento = new TContabilidadeValorLancamento;
    $this->obTContabilidadeLancamento      = new TContabilidadeLancamento;
    $this->obTContabilidadeLote            = new TContabilidadeLote;
    $this->obTransacao                     = new Transacao;
}

/**
    * Exclui dados do Lancamento do Conta Credito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLancamentoContaCredito($boTransacao = "")
{
    $this->obTContabilidadeContaCredito->setDado( "exercicio",   $this->getExercicio()      );
    $this->obTContabilidadeContaCredito->setDado( "cod_entidade", $this->getCodEntidade()   );
    $this->obTContabilidadeContaCredito->setDado( "cod_lote", $this->getCodLote()           );
    $this->obTContabilidadeContaCredito->setDado( "tipo", $this->getTipo()                  );

    $obErro = $this->obTContabilidadeContaCredito->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Exclui dados do Lancamento do Conta Debito
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLancamentoContaDebito($boTransacao = "")
{
    $this->obTContabilidadeContaDebito->setDado( "exercicio", $this->getExercicio()         );
    $this->obTContabilidadeContaDebito->setDado( "cod_entidade", $this->getCodEntidade()    );
    $this->obTContabilidadeContaDebito->setDado( "cod_lote", $this->getCodLote()            );
    $this->obTContabilidadeContaDebito->setDado( "tipo", $this->getTipo()                   );

    $obErro = $this->obTContabilidadeContaDebito->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Exclui dados do Valor Lancamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirValorLancamento($boTransacao = "")
{
    $this->obTContabilidadeValorLancamento->setDado( "exercicio", $this->getExercicio()     );
    $this->obTContabilidadeValorLancamento->setDado( "cod_entidade", $this->getCodEntidade());
    $this->obTContabilidadeValorLancamento->setDado( "cod_lote", $this->getCodLote()        );
    $this->obTContabilidadeValorLancamento->setDado( "tipo", $this->getTipo()               );

    $obErro = $this->obTContabilidadeValorLancamento->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Exclui dados do Lancamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLancamento($boTransacao = "")
{
    $this->obTContabilidadeLancamento->setCampoCod('');
    $this->obTContabilidadeLancamento->setComplementoChave('cod_lote,tipo,exercicio,cod_entidade');

    $this->obTContabilidadeLancamento->setDado( "exercicio", $this->getExercicio()      );
    $this->obTContabilidadeLancamento->setDado( "cod_entidade", $this->getCodEntidade() );
    $this->obTContabilidadeLancamento->setDado( "cod_lote", $this->getCodLote()         );
    $this->obTContabilidadeLancamento->setDado( "tipo", $this->getTipo()                );

    $obErro = $this->obTContabilidadeLancamento->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Exclui dados do Lote
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLote($boTransacao = "")
{
    $this->obTContabilidadeLote->setComplementoChave( 'cod_lote,exercicio,tipo,cod_entidade' );

    $this->obTContabilidadeLote->setDado( "exercicio", $this->getExercicio()        );
    $this->obTContabilidadeLote->setDado( "cod_entidade", $this->getCodEntidade()   );
    $this->obTContabilidadeLote->setDado( "cod_lote", $this->getCodLote()           );
    $this->obTContabilidadeLote->setDado( "tipo", $this->getTipo()                  );
    $this->obTContabilidadeLote->setDado( "nom_lote", "Abertura do Exercicio Restos a Pagar" );

    $obErro = $this->obTContabilidadeLote->exclusao( $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLote(&$rsLote, $boTransacao = "")
{
    $this->obTContabilidadeLote->setCampoCod('nom_lote');

    $this->obTContabilidadeLote->setDado("exercicio", Sessao::getExercicio()                );
    $this->obTContabilidadeLote->setDado("nom_lote", "Abertura do Exercicio Restos a Pagar" );

    $obErro = $this->obTContabilidadeLote->recuperaPorChave( $rsLote, $boTransacao );

    return $obErro;
}

}
