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

    Caso de uso: uc-04.02.01

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php"                );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriado.class.php"                   );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriadoVariavel.class.php"           );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioFeriadoVariavel.class.php" );
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioFeriado.class.php"                             );

  /**
    * Classe de Regra de Negócio Feriado Variável
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins
  */
class RFeriadoVariavel extends RFeriado
{
  /**
    * @var Object
    * @access Private
  */
  public $obTFeriadoVariavel;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioFeriadoVariavel;

  /**
    * @var Object
    * @access Private
  */
  public $boVariavel;

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTFeriadoVariavel($valor)
  {
    $this->obTFeriadoVariavel       = $valor;
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
    * @param Object $valor
  */
  public function setBoVariavel($valor)
  {
    $this->boVariavel       = $valor;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTFeriadoVariavel()
  {
    return $this->obTFeriadoVariavel;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function getTCalendarioFeriadoVariavel()
  {
    return $this->obTCalendarioFeriadoVariavel;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function getBoVariavel()
  {
    return $this->boVariavel;
  }

  /**
    * Método Construtor
    * @access Private
  */
  public function RFeriadoVariavel()
  {
    parent::RFeriado();
    $this->setTFeriadoVariavel  ( new TCalendarioFeriadoVariavel );
    $this->setTCalendarioFeriadoVariavel  ( new TCalendarioCalendarioFeriadoVariavel );
  }

  /**
    * Salva Feriado no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function incluirFeriadoVariavel($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
      if ( !$obErro->ocorreu() ) {
        $this->obTFeriadoVariavel->setDado("cod_feriado"  , $this->getCodFeriado() );
        $obErro = $this->obTFeriadoVariavel->inclusao( $boTransacao );
      }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  /**
    * Alterar Feriado no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function alterarFeriadoVariavel($boTransacao = "")
  {
      $boFlagTransacao = false;
      $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
      if ( !$obErro->ocorreu() ) {
        $this->obTFeriadoVariavel->setDado("cod_feriado"  , $this->getCodFeriado() );
        $obErro = $this->obTFeriadoVariavel->alteracao( $boTransacao );
      }
      $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

    return $obErro;
  }

  /**
    * Exclui dados de Feriados Variaveis do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function excluirFeriadoVariavel($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {
        $this->obTCalendarioFeriadoVariavel->setDado("cod_feriado" , $this->getCodFeriado() );
        $obErro = $this->obTCalendarioFeriadoVariavel->exclusao( $boTransacao );

        if (!$obErro->ocorreu()) {
           $this->obTFeriadoVariavel->setDado("cod_feriado"  , $this->getCodFeriado() );
           $obErro = $this->obTFeriadoVariavel->exclusao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  public function listarFeriadoVariavel(&$rsLista, $stOrdem , $obTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
      $obErro = $this->obTFeriadoVariavel->recuperaRelacionamento ($rsLista, "", $stOrdem);
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

    return $obErro;
  }

}
