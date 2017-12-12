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
    * Classe de Regra de Negócio Feriado
    * Data de Criação   : 09/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  * Casos de uso :uc-04.02.01
                  uc-04.02.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php"                              );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriado.class.php"                                 );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriadoVariavel.class.php"                         );

// include_once    ( "../../../includes/Constante.inc.php"                               );
// include_once    ( CAM_CONECTIVIDADE."Transacao.class.php"                             );
// include_once    ( CLA_RECORDSET );
// include_once    ( CLA_CONEXAO );

/**
    * Classe de Regra de Negócio Calendario
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins
*/
class RFeriado
{

  /**
    * @var Object
    * @access Private
  */
  public $obTFeriado;

  /**
    * @var Object
    * @access Private
  */
  public $obTransacao;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioFeriadoVariavel;

  /**
    * @var Integer
    * @access Private
  */
  public $inCodFeriado;

  /**
    * @var Date
    * @access Private
  */
  public $dtFeriado;

  /**
    * @var Date
    * @access Private
  */
  public $dtInicial;

  /**
    * @var Date
    * @access Private
  */
  public $dtFinal;

  /**
    * @var String
    * @access Private
  */
  public $stDescricao;

  /**
    * @var String
    * @access Private
  */
  public $stTipoFeriado;

  /**
    * @var String
    * @access Private
  */
  public $stTipo;

  /**
    * @var String
    * @access Private
  */
  public $stAbrangencia;

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTFeriado($valor)
  {
    $this->obTFeriado = $valor;
  }

  /**
     * @access Public
     * @param Object $valor
  */
  public function setTCalendarioFeriadoVariavel($valor)
  {
    $this->obTCalendarioFeriadoVariavel = $valor;
  }

  /**
    * @access Public
    * @param Integer $valor
  */
  public function setCodFeriado($valor)
  {
    $this->inCodFeriado = $valor;
  }

  /**
    * @access Public
    * @param Date $valor
  */
  public function setDtFeriado($valor)
  {
    $this->dtFeriado = $valor;
  }

  /**
    * @access Public
    * @param Date $valor
  */
  public function setDtInicial($valor)
  {
    $this->dtInicial = $valor;
  }

  /**
    * @access Public
    * @param Date $valor
  */
  public function setDtFinal($valor)
  {
    $this->dtFinal = $valor;
  }

  /**
    * @access Public
    * @param String $valor
  */
  public function setDescricao($valor)
  {
    $this->stDescricao = $valor;
  }

  /**
    * @access Public
    * @param String $valor
  */
  public function setTipoFeriado($valor)
  {
    $this->stTipoFeriado = $valor;
  }

  /**
    * @access Public
    * @param String $valor
  */
  public function setTipo($valor)
  {
    $this->stTipo = $valor;
  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setTransacao($valor)
  {
    $this->obTransacao = $valor;
  }

  /**
    * @access Public
    * @param String $valor
  */
  public function setAbrangencia($valor)
  {
    $this->stAbrangencia = $valor;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTFeriado()
  {
    return $this->obTFeriado;
  }

  /**
     * @access Public
     * @return Object
  */
  public function getTCalendarioFeriadoVariavel()
  {
    return $this->obTCalendarioFeriadoVariavel;
  }

  /**
    * @access Public
    * @return Integer
  */
  public function getCodFeriado()
  {
    return $this->inCodFeriado;
  }

  /**
    * @access Public
    * @return Date
  */
  public function getDtFeriado()
  {
    return $this->dtFeriado;
  }

  /**
    * @access Public
    * @return Date
  */
  public function getDtInicial()
  {
    return $this->dtInicial;
  }

  /**
    * @access Public
    * @return Date
  */
  public function getDtFinal()
  {
    return $this->dtFinal;
  }

  /**
    * @access Public
    * @return String
  */

  public function getDescricao()
  {
    return $this->stDescricao;
  }

  /**
    * @access Public
    * @return String
  */
  public function getTipoFeriado()
  {
    return $this->stTipoFeriado;
  }

  /**
    * @access Public
    * @return String
  */
  public function getTipo()
  {
    return $this->stTipo;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTransacao()
  {
    return $this->obTransacao;
  }

  /**
    * @access Public
    * @return String
  */
  public function getAbrangencia()
  {
    return $this->stAbrangencia;
  }

  /**
    * Método Construtor
    * @access Private
  */
  public function RFeriado()
  {
    $this->setTFeriado  ( new TCalendarioFeriado );
    $this->setTransacao ( new Transacao          );
    $this->setTCalendarioFeriadoVariavel ( new TCalendarioFeriadoVariavel );
  }

  /**
    * Salva Feriado no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function salvar($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
      $this->obTFeriado->setDado( "dt_feriado"   , $this->getDtFeriado() );
      $this->obTFeriado->setDado( "descricao"    , $this->getDescricao() );
      $this->obTFeriado->setDado( "tipoferiado"  , $this->getTipoFeriado() );
      $this->obTFeriado->setDado( "abrangencia"  , $this->getAbrangencia() );

      if ( $this->getCodFeriado()) {
        $this->obTFeriado->setDado("cod_feriado"    , $this->getCodFeriado() );
        $obErro = $this->obTFeriado->alteracao( $boTransacao );
      } else {
        $this->obTFeriado->proximoCod( $inCodFeriado , $boTransacao );
        $this->setCodFeriado( $inCodFeriado );
        $this->obTFeriado->setDado("cod_feriado", $this->getCodFeriado() );
        $obErro = $this->obTFeriado->inclusao( $boTransacao );
      }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriado );

    return $obErro;
  }

  /**
    * Exclui dados de Feriados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if (!$obErro->ocorreu()) {
            $this->obTFeriado->setDado("cod_feriado"  , $this->getCodFeriado() );
            $obErro = $this->obTFeriado->exclusao( $boTransacao );
        }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriado );

   return $obErro;
}

  /**
    * Lista todos os Feriados de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function listar(&$rsLista, $stFiltro = "", $stOrder, $boTransacao = "")
  {

    if ($this->getDtFeriado()) {
       $stAno = explode("/",$this->getDtFeriado());
       $this->obTFeriado->setDado("ano",$stAno[2]);
    }

    if ($this->inCodFeriado) {
      $stFiltro .= " AND f.cod_feriado = ". $this->inCodFeriado . " ";
    }

    if ($this->stDescricao) {
      $stFiltro .= " AND descricao LIKE '%".$this->stDescricao."%' ";
    }

    if ($this->stTipoFeriado) {
      $stFiltro .= " AND tipoferiado = '".$this->stTipoFeriado."' ";
    }

    if ($this->stAbrangencia) {
      $stFiltro .= " abrangencia = '".$this->stAbrangencia."'and ";
    }

    if ($this->dtFeriado) {
      $stFiltro .= "AND to_char(dt_feriado, 'dd/mm/yyyy') = '" . $this->dtFeriado ."'";
    }

    if (( $this->dtInicial ) && ( $this->dtFinal )) {
      $stFiltro .= " ( dt_feriado BETWEEN to_date('". $this->dtInicial ."','dd/mm,yyyy')
                                             AND to_date('". $this->dtFinal ."','dd/mm/yyyy') ) ";
    }

    $obErro = $this->obTFeriado->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder,$boTransacao );

    return $obErro;
}

  /**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function consultar(&$rsRecordSet, $boTransacao = "")
  {
    if ($this->getCodFeriado()) {
       $this->obTFeriado->setDado( "cod_feriado" , $this->getCodFeriado() );
    }
    $obErro = $this->obTFeriado->recuperaPorChave( $rsRecordSet, $boTransacao );

    return $obErro;
  }

  }
