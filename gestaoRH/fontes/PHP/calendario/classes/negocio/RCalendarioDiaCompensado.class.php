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
    * Classe de Regra de Negócio dia compensado
    * Data de Criação   : 28/04/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso : uc-04.02.05

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php"                );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioDiaCompensado.class.php"             );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioDiaCompensado.class.php"   );
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioFeriado.class.php"                   );

  /**
    * Classe de Regra de Negócio Feriado Variável
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandré Miguel Ramos
  */
class RCalendarioDiaCompensado extends RFeriado
{
  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioDiaCompensado;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioCalendarioDiaCompensado;

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioDiaCompensado($valor) {  $this->obTCalendarioDiaCompensado   = $valor;  }

 /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioCalendarioDiaCompensado($valor) {  $this->obTCalendarioCalendarioDiaCompensado = $valor;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioDiaCompensado() {  return $this->obTCalendarioDiaCompensado;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioCalendarioDiaCompensado() {  return $this->obTCalendarioCalendarioDiaCompensado;  }

  /**
    * Método Construtor
    * @access Private
  */
  public function RCalendarioDiaCompensado()
  {
    parent::RFeriado();
    $this->setTCalendarioDiaCompensado               ( new TCalendarioDiaCompensado );
    $this->setTCalendarioCalendarioDiaCompensado     ( new TCalendarioCalendarioDiaCompensado );
  }

  /**
    * Inclui dia compensado no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function incluirDiaCompensado($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $obErro = $this->salvar();
        if ( !$obErro->ocorreu() ) {
           $this->obTCalendarioDiaCompensado->setDado("cod_feriado"  , $this->getCodFeriado() );
           $obErro = $this->obTCalendarioDiaCompensado->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  /**
    * Alterar Ponto Dia compensado no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function alterarDiaCompensado($boTransacao = "")
  {
      $boFlagTransacao = false;
      $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
      if (!$obErro->ocorreu()) {
           $obErro = $this->salvar($boTransacao);
           if ( !$obErro->ocorreu() ) {
              $this->obTCalendarioDiaCompensado->setDado("cod_feriado"  , $this->getCodFeriado() );
              $obErro = $this->obTCalendarioDiaCompensado->alteracao( $boTransacao );
            }
      }
      $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

      return $obErro;
  }

  /**
    * Exclui dados de dia compensado do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function excluirDiaCompensado($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if (!$obErro->ocorreu()) {
        $this->obTCalendarioCalendarioDiaCompensado->setDado("cod_feriado" , $this->getCodFeriado() );
        $obErro = $this->obTCalendarioCalendarioDiaCompensado->exclusao( $boTransacao );
        if (!$obErro->ocorreu()) {
           $this->obTCalendarioDiaCompensado->setDado("cod_feriado"  , $this->getCodFeriado());
           $obErro = $this->obTCalendarioDiaCompensado->exclusao( $boTransacao );
           if (!$obErro->ocorreu()) {
              $obErro = $this->excluir($boTransacao);
          }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,$this->obTFeriadoVariavel );

    return $obErro;
  }

  public function listarDiaCompensado(&$rsLista, $stOrdem , $obTransacao = "")
  {
    if ($this->getDtFeriado()) {
       $stFiltro .=" and dt_feriado = to_date('".$this->getDtFeriado()."','dd/mm/yyyy')";
    }
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
      if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCalendarioDiaCompensado->recuperaRelacionamento ($rsLista,$stFiltro,$stOrdem,$boTransacao);
      }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFeriadoVariavel );

    return $obErro;
  }

}
