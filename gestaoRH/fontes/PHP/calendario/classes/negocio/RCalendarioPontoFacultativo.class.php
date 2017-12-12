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
    * Classe de Regra de Negócio ponto facultativo
    * Data de Criação   : 28/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-20 12:34:13 -0300 (Qua, 20 Jun 2007) $

    * Casos de uso : uc-04.02.06

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php"                );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioPontoFacultativo.class.php"          );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioPontoFacultativo.class.php");
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioFeriado.class.php"                   );

  /**
    * Classe de Regra de Negócio Feriado Variável
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos
  */
class RCalendarioPontoFacultativo extends RFeriado
{
  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioPontoFacultativo;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioCalendarioPontoFacultativo;

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioPontoFacultativo($valor) {  $this->obTCalendarioPontoFacultativo   = $valor;  }

 /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioCalendarioPontoFacultativo($valor) {  $this->obTCalendarioCalendarioPontoFacultativo   = $valor;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioPontoFacultativo() {  return $this->obTCalendarioPontoFacultativo;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioCalendarioPontoFacultativo() {  return $this->obTCalendarioCalendarioPontoFacultativo;  }

  /**
    * Método Construtor
    * @access Private
  */
  public function RCalendarioPontoFacultativo()
  {
    parent::RFeriado();
    $this->setTCalendarioPontoFacultativo  ( new TCalendarioPontoFacultativo );
    $this->setTCalendarioCalendarioPontoFacultativo  ( new TCalendarioCalendarioPontoFacultativo );
  }

  /**
    * Inclui ponto facultativo no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function incluirPontoFacultativo($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->salvar();
        if ( !$obErro->ocorreu() ) {
           $this->obTCalendarioPontoFacultativo->setDado("cod_feriado"  , $this->getCodFeriado() );
           $obErro = $this->obTCalendarioPontoFacultativo->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  /**
    * Alterar Ponto Facultativo no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function alterarPontoFacultativo($boTransacao = "")
  {
      $boFlagTransacao = false;
      $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
      if (!$obErro->ocorreu()) {
           $obErro = $this->salvar($boTransacao);
           if ( !$obErro->ocorreu() ) {
              $this->obTCalendarioPontoFacultativo->setDado("cod_feriado"  , $this->getCodFeriado() );
              $obErro = $this->obTCalendarioPontoFacultativo->alteracao( $boTransacao );
            }
      }
      $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

      return $obErro;
  }

  /**
    * Exclui dados de Ponto Facultativo do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function excluirPontoFacultativo($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {
        $this->obTCalendarioCalendarioPontoFacultativo->setDado("cod_feriado" , $this->getCodFeriado() );
        $obErro = $this->obTCalendarioCalendarioPontoFacultativo->exclusao( $boTransacao );
        if (!$obErro->ocorreu()) {
           $this->obTCalendarioPontoFacultativo->setDado("cod_feriado"  , $this->getCodFeriado());
           $obErro = $this->obTCalendarioPontoFacultativo->exclusao( $boTransacao );
           if (!$obErro->ocorreu()) {
              $obErro = $this->excluir($boTransacao);
          }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  public function listarPontoFacultativo(&$rsLista, $stOrdem , $obTransacao = "")
  {
    if ($this->getDtFeriado()) {
       $stFiltro .=" and dt_feriado = to_date('".$this->getDtFeriado()."','dd/mm/yyyy')";
    }
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCalendarioPontoFacultativo->recuperaRelacionamento ($rsLista,$stFiltro,$stOrdem,$boTransacao);
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

    return $obErro;
  }

}
