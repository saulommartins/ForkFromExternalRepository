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
* Classe de regra de negócio para Pessoal-CondicaoAssentamento
* Data de Criação: 04/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"                        );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                            );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCondicaoAssentamento.class.php"           );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCondicaoAssentamentoExcluido.class.php"   );

class RPessoalCondicaoAssentamento
{
    /**
        * @access Private
        * @var integer
    */
    public $inCodCondicao;
    /**
        * @access Private
        * @var String
    */
    public $stTimestamp;
    /**
        * @access Private
        * @var Object
    */
    public $obTransacao;
    /**
        * @access Private
        * @var Object
    */
    public $obTRPessoalCondicaoAssentamento;
    /**
        * @access Private
        * @var Object
    */
    public $obTPessoalCondicaoAssentamentoExcluido;
    /**
        * @access Private
        * @var Object
    */
    public $roUltimoRAssentamentoVinculado;
    /**
        * @access Private
        * @var array
    */
    public $arRPessoalAssentamentoVinculado;
    /**
        * @access Private
        * @var Object
    */
    public $roUltimoRAssentamentoVinculadoExcluido;
    /**
        * @access Private
        * @var array
    */
    public $arRPessoalAssentamentoVinculadoExcluido;

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodCondicao($valor) { $this->inCodCondicao                                = $valor  ;  }
    /**
        * @access Public
        * @param String $valor
    */
    public function setTimestamp($valor) { $this->stTimestamp                                  = $valor  ;  }
    /**
        * @access Public
        * @param Array $valor
    */
    public function setAssentamentoVinculado($valor) { $this->arRPessoalAssentamentoVinculado              = $valor  ;  }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTPessoalCondicaoAssentamento($valor) { $this->obTPessoalCondicaoAssentamento               = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTPessoalCondicaoAssentamentoExcluido($valor) { $this->obTPessoalCondicaoAssentamentoExcluido      = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setUltimoAssentamentoVinculado($valor) { $this->roUltimoRAssentamentoVinculado               = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setUltimoAssentamentoVinculadoExcluido($valor) { $this->roUltimoRAssentamentoVinculadoExcluido       = $valor  ; }
    /**
        * @access Public
        * @param Object $Valor
    */
    public function setTransacao($valor) { $this->obTransacao                                  = $valor  ; }

    /**
        * @access Public
        * @param Integer
    */
    public function getCodCondicao() { return $this->inCodCondicao;                            }
    /**
        * @access Public
        * @param String
    */
    public function getTimestamp() { return $this->stTimestamp;                              }
    /**
        * @access Public
        * @return Array
    */
    public function getAssentamentoVinculado() { return $this->arRAssentamentoVinculado;                 }
    /**
        * @access Public
        * @return Object
    */
    public function getTPessoalCondicaoAssentamento() { return $this->obTPessoalCondicaoAssentamento;           }
    /**
        * @access Public
        * @return Object
    */
    public function getTPessoalCondicaoAssentamentoExcluido() { return $this->obTPessoalCondicaoAssentamentoExcluido   ; }
    /**
        * @access Public
        * @return Object
    */
    public function getUltimoAssentamentoVinculado() { return $this->roUltimoRAssentamentoVinculado;           }
    /**
        * @access Public
        * @return Object
    */
    public function getUltimoAssentamentoVinculadoExcluido() { return $this->roUltimoRAssentamentoVinculadoExcluido;   }
    /**
        * Método construtor
        * @access Private
    */
    public function getTransacao() { return $this->obTransacao;                              }

    /**
        * Método construtor
        * @access Private
    */
    public function RPessoalCondicaoAssentamento()
    {
        $this->setTransacao                             ( new Transacao                             );
        $this->setTPessoalCondicaoAssentamento          ( new TPessoalCondicaoAssentamento          );
        $this->setTPessoalCondicaoAssentamentoExcluido  ( new TPessoalCondicaoAssentamentoExcluido  );
        $this->setAssentamentoVinculado                 = array();
        $this->setAssentamentoVinculadoExcluido         = array();
    }

    /**
    * Adiciona um array de referencia-objeto
    * @access Public
    */
    public function addAssentamentoVinculado()
    {
        $this->arRPessoalAssentamentoVinculado[]       =  new RPessoalAssentamentoVinculado(new RPessoalAssentamento(new RPessoalVantagem),new RPessoalAssentamento(new RPessoalVantagem),$this);
        $this->roUltimoRPessoalAssentamentoVinculado   = &$this->arRPessoalAssentamentoVinculado[ count($this->arRPessoalAssentamentoVinculado) - 1 ];
    }

    /**
    * Adiciona um array de referencia-objeto
    * @access Public
    */
    public function addAssentamentoVinculadoExcluido()
    {
        $this->arRPessoalAssentamentoVinculadoExcluido[]       =  new RPessoalAssentamentoVinculado(new RPessoalAssentamento(new RPessoalVantagem),new RPessoalAssentamento(new RPessoalVantagem),$this);
        $this->roUltimoRPessoalAssentamentoVinculadoExcluido   = &$this->arRPessoalAssentamentoVinculadoExcluido[ count($this->arRPessoalAssentamentoVinculadoExcluido) - 1 ];
    }

    /**
        * Inclui dados de condição de assentamento no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirCondicaoAssentamento($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $tmpComplementoChave =  $this->obTPessoalCondicaoAssentamento->getComplementoChave();
            $tmpComplementoCod   =  $this->obTPessoalCondicaoAssentamento->getCampoCod();
            $this->obTPessoalCondicaoAssentamento->setCampoCod('cod_condicao');
            $this->obTPessoalCondicaoAssentamento->proximoCod( $inCodCondicaoAssentamento , $boTransacao );
            $this->setCodCondicao( $inCodCondicaoAssentamento );
            $this->obTPessoalCondicaoAssentamento->setDado('cod_condicao',$this->getCodCondicao());
            $this->obTPessoalCondicaoAssentamento->setDado('cod_assentamento',$this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getCodAssentamento());
            $this->obTPessoalCondicaoAssentamento->setDado('timestamp_assentamento',$this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getTimestamp());
            $this->obTPessoalCondicaoAssentamento->recuperaNow3($stNow, $boTransacao);
            $this->setTimestamp($stNow);
            $this->obTPessoalCondicaoAssentamento->setComplementoChave($tmpComplementoCampo);
            $this->obTPessoalCondicaoAssentamento->setCampoCod($tmpComplementoCod);
            $obErro = $this->obTPessoalCondicaoAssentamento->inclusao($boTransacao);
            if ( !$obErro->ocorreu() ) {
                $boExcluir = false;
                foreach ($this->arRPessoalAssentamentoVinculado as $obRAssentamentoVinculado) {
                    if ( $obRAssentamentoVinculado->obRPessoalAssentamento1->getSigla() != 'filho_nulo' ) {
                        $obRAssentamentoVinculado->roRPessoalCondicaoAssentamento->setCodCondicao( $inCodCondicaoAssentamento );
                        $obRAssentamentoVinculado->roRPessoalCondicaoAssentamento->setTimestamp( $stNow );

                        $obErro = $obRAssentamentoVinculado->incluirAssentamentoVinculado($boTransacao);
                    } else {
                        $boExcluir = true;
                        break;
                    }
                }
                if ($boExcluir) {
                    $obErro = $this->incluirCondicaoAssentamentoExcluido($boTransacao);
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTRPessoalCondicaoAssentamento );

        return $obErro;
    }

    /**
        * Inclui dados de condição de assentamento no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarCondicaoAssentamento($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalCondicaoAssentamento->setDado('cod_condicao',          $this->getCodCondicao());
            $this->obTPessoalCondicaoAssentamento->setDado('cod_assentamento',      $this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getCodAssentamento());
            $this->obTPessoalCondicaoAssentamento->setDado('timestamp_assentamento',$this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getTimestamp());
            $this->obTPessoalCondicaoAssentamento->recuperaNow3($stNow, $boTransacao);
            $this->setTimestamp($stNow);
            $obErro = $this->obTPessoalCondicaoAssentamento->inclusao($boTransacao);

            if ( !$obErro->ocorreu() ) {
                $boExcluir = false;

                foreach ($this->arRPessoalAssentamentoVinculado as $obRAssentamentoVinculado) {
                    if ( $obRAssentamentoVinculado->obRPessoalAssentamento1->getSigla() != 'filho_nulo' ) {
                        $obRAssentamentoVinculado->roRPessoalCondicaoAssentamento->setCodCondicao( $this->getCodCondicao() );
                        $obRAssentamentoVinculado->roRPessoalCondicaoAssentamento->setTimestamp( $stNow );
                        $obErro = $obRAssentamentoVinculado->incluirAssentamentoVinculado($boTransacao);
                    } else {
                        $boExcluir = true;
                        break;
                    }
                }
                if ($boExcluir) {
                    $obErro = $this->incluirCondicaoAssentamentoExcluido($boTransacao);
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTRPessoalCondicaoAssentamento );

        return $obErro;
    }

    /**
        * Inclui dados de assentamento vinculado no banco de dados
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function incluirCondicaoAssentamentoExcluido($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalCondicaoAssentamentoExcluido->setDado("cod_condicao",                         $this->getCodCondicao()         );
            $this->obTPessoalCondicaoAssentamentoExcluido->setDado("timestamp",                            $this->getTimestamp()           );
            $this->obTPessoalCondicaoAssentamentoExcluido->setDado("cod_assentamento",                     $this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getCodAssentamento()            );
            $this->obTPessoalCondicaoAssentamentoExcluido->setDado("timestamp_assentamento",               $this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getTimestamp()                  );
            $obErro = $this->obTPessoalCondicaoAssentamentoExcluido->inclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamento );

        return $obErro;
    }

    /**
        * Executa um recuperaTodos na classe Persistente
        * @access Public
        * @param  Object $rsRecordSet Retorna o RecordSet preenchido
        * @param  String $stOrder Parâmetro de Ordenação
        * @param  Object $boTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function listarCondicaoAssentamento(&$rsRecordSet, $boTransacao = "")
    {
        if( $this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() )
            $stFiltro .= " AND paa.cod_classificacao = ".$this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento()." ";
        if( $this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getCodAssentamento() )
            $stFiltro .= " AND a.cod_assentamento = ".$this->roUltimoRPessoalAssentamentoVinculado->obRPessoalAssentamento1->getCodAssentamento()." ";
        $obErro = $this->obTPessoalCondicaoAssentamento->recuperaAssentamentos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

        return $obErro;
    }
}
